<?php

/*
 * Copyright 2004-2014, AfterLogic Corp.
 * Licensed under AGPLv3 license or AfterLogic license
 * if commercial version of the product was purchased.
 * See the LICENSE file for a full license statement.
 */

CApi::Inc('common.db.sql');

/**
 * @package Api
 * @subpackage Db
 */
class CDbPdoMySql extends CDbSql
{
	/**
	 * @var bool
	 */
	protected $bUseExplain;

	/**
	 * @var bool
	 */
	protected $bUseExplainExtended;

	/**
	 * @var PDO database object
	 */
	protected $oPDO;

	/**
	 * @var PDO resource
	 */
	protected $rResultId;

	/**
	 * @param string $sHost
	 * @param string $sUser
	 * @param string $sPassword
	 * @param string $sDbName
	 * @param string $sDbTablePrefix = ''
	 */
	public function __construct($sHost, $sUser, $sPassword, $sDbName, $sDbTablePrefix = '')
	{
		$this->sHost = trim($sHost);
		$this->sUser = trim($sUser);
		$this->sPassword = trim($sPassword);
		$this->sDbName = trim($sDbName);
		$this->sDbTablePrefix = trim($sDbTablePrefix);

		$this->oPDO = null;
		$this->rResultId = null;

		$this->iExecuteCount = 0;
		$this->bUseExplain = CApi::GetConf('labs.db.use-explain', false);
		$this->bUseExplainExtended = CApi::GetConf('labs.db.use-explain-extended', false);
	}

	/**
	 * @param string $sHost
	 * @param string $sUser
	 * @param string $sPassword
	 * @param string $sDbName
	 */
	public function ReInitIfNotConnected($sHost, $sUser, $sPassword, $sDbName)
	{
		if (!$this->IsConnected())
		{
			$this->sHost = trim($sHost);
			$this->sUser = trim($sUser);
			$this->sPassword = trim($sPassword);
			$this->sDbName = trim($sDbName);
		}
	}

	/**
	 * @param bool $bWithSelect = true
	 * @return bool
	 */
	public function Connect($bWithSelect = true, $bNewLink = false)
	{
		if (!class_exists('PDO'))
		{
			throw new CApiDbException('Can\'t load PDO extension.', 0);
		}

		$mPdoDrivers = PDO::getAvailableDrivers();
		if (!is_array($mPdoDrivers) || !in_array('mysql', $mPdoDrivers))
		{
			throw new CApiDbException('Can\'t load PDO mysql driver.', 0);
		}

		if (strlen($this->sHost) == 0 || strlen($this->sUser) == 0 || strlen($this->sDbName) == 0)
		{
			throw new CApiDbException('Not enough details required to establish connection.', 0);
		}

		if (CApi::$bUseDbLog)
		{
			CApi::Log('DB(PDO/mysql) : start connect to '.$this->sUser.'@'.$this->sHost);
		}

		$aPDOAttr = array(PDO::ATTR_TIMEOUT => 5, PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
		if (defined('PDO::MYSQL_ATTR_MAX_BUFFER_SIZE'))
		{
			$aPDOAttr[PDO::MYSQL_ATTR_MAX_BUFFER_SIZE] = 1024*1024*50;
		}
		
		if (defined('PDO::MYSQL_ATTR_USE_BUFFERED_QUERY'))
		{
			$aPDOAttr[PDO::MYSQL_ATTR_USE_BUFFERED_QUERY] = true;
		}

		$sDbPort = '';
		$sUnixSocket = '';

		$sDbHost = $this->sHost;
		$sDbName = $this->sDbName;
		$sDbLogin = $this->sUser;
		$sDbPassword = $this->sPassword;

		$iPos = strpos($sDbHost, ':');
		if (false !== $iPos && 0 < $iPos)
		{
			$sAfter = substr($sDbHost, $iPos + 1);
			$sDbHost = substr($sDbHost, 0, $iPos);

			if (is_numeric($sAfter))
			{
				$sDbPort = $sAfter;
			}
			else
			{
				$sUnixSocket = $sAfter;
			}
		}

		$this->oPDO = false;
		if (class_exists('PDO'))
		{
			try
			{
				$aParts = array();
				if ($bWithSelect && 0 < strlen($sDbName))
				{
					$aParts[] = 'dbname='.$sDbName;
				}
				if (0 < strlen($sDbHost))
				{
					$aParts[] = 'host='.$sDbHost;
				}
				if (0 < strlen($sDbPort))
				{
					$aParts[] = 'port='.$sDbPort;
				}
				if (0 < strlen($sUnixSocket))
				{
					$aParts[] = 'unix_socket='.$sUnixSocket;
				}
				$aParts[] = 'charset=utf8';

				$sPdoString = 'mysql:'.implode(';', $aParts);
				if (CApi::$bUseDbLog)
				{
					CApi::Log('DB : PDO('.$sPdoString.')');
				}

				$this->oPDO = @new PDO($sPdoString, $sDbLogin, $sDbPassword, $aPDOAttr);
				if (CApi::$bUseDbLog)
				{
					CApi::Log('DB : connected to '.$this->sUser.'@'.$this->sHost);
				}

				if ($this->oPDO)
				{
					@register_shutdown_function(array(&$this, 'Disconnect'));
				}
			}
			catch (Exception $oException)
			{
				CApi::Log($oException->getMessage(), ELogLevel::Error);
				CApi::Log($oException->getTraceAsString(), ELogLevel::Error);
				$this->oPDO = false;

				throw new CApiDbException($oException->getMessage(), $oException->getCode(), $oException);
			}
		}
		else
		{
			CApi::Log('Class PDO dosn\'t exist', ELogLevel::Error);
		}

		return !!$this->oPDO;
	}

	/**
	 * @return bool
	 */
	public function ConnectNoSelect()
	{
		return $this->Connect(false);
	}

	/**
	 * @return bool
	 */
	public function Select()
	{
		return $this->oPDO != null;
	}

	/**
	 * @return bool
	 */
	public function Disconnect()
	{
		$result = false;
		if ($this->oPDO)
		{
			if (is_resource($this->rResultId))
			{
				$this->rResultId->closeCursor();
			}

			$this->rResultId = null;

			if (CApi::$bUseDbLog)
			{
				CApi::Log('DB : disconnect from '.$this->sUser.'@'.$this->sHost);
			}

			unset($this->oPDO);
			$this->oPDO = null;
			$result = true;
		}

		return $result;
	}

	/**
	 * @return bool
	 */
	function IsConnected()
	{
		return !!$this->oPDO;
	}

	/** @param $sQuery
	    @return false or PDOStatement
	 */
	private function SilentQuery($sQuery)
	{
		if (!$this->oPDO)
		{
			return false;
		}
		
		try
		{
			$res = $this->oPDO->query($sQuery);
			return $res;
		}
		catch (Exception $e)
		{
		}

		return false;
	}

	/**
	 * @param string $sQuery
	 * @param string $bIsSlaveExecute = false
	 * @return bool
	 */
	public function Execute($sQuery, $bIsSlaveExecute = false)
	{
		$sExplainLog = '';
		$sQuery = trim($sQuery);
		
		if (($this->bUseExplain || $this->bUseExplainExtended) && 0 === strpos($sQuery, 'SELECT'))
		{
			$sExplainQuery = 'EXPLAIN ';
			$sExplainQuery .= ($this->bUseExplainExtended) ? 'extended '.$sQuery : $sQuery;

			$rExplainResult = $this->SilentQuery($sExplainQuery);
			if ($rExplainResult != false)
			{
				while (false != ($mResult = $rExplainResult->fetch(PDO::FETCH_ASSOC)))
				{
					$sExplainLog .= API_CRLF.print_r($mResult, true);
				}
				
				$rExplainResult->closeCursor();
			}

			if ($this->bUseExplainExtended)
			{
				$rExplainResult = $this->SilentQuery('SHOW warnings');
				if ($rExplainResult != false)
				{
					while (false != ($mResult = $rExplainResult->fetch(PDO::FETCH_ASSOC)))
					{
						$sExplainLog .= API_CRLF.print_r($mResult, true);
					}
					
					$rExplainResult->closeCursor();
				}
			}
		}

		$this->iExecuteCount++;
		$this->log($sQuery, $bIsSlaveExecute);
		if (!empty($sExplainLog))
		{
			$this->log('EXPLAIN:'.API_CRLF.trim($sExplainLog), $bIsSlaveExecute);
		}

		$this->rResultId = $this->SilentQuery($sQuery);
		if ($this->rResultId === false)
		{
			$this->_setSqlError();
		}

		return $this->rResultId !== false;
	}

	/**
	 * @param bool $bAutoFree = true
	 * @return &object
	 */
	public function &GetNextRecord($bAutoFree = true)
	{
		if ($this->rResultId)
		{
			$mResult = $this->rResultId->fetch(PDO::FETCH_OBJ);
			if (!$mResult && $bAutoFree)
			{
				$this->FreeResult();
			}
			
			return $mResult;
		}
		else
		{
			$nNull = false;
			$this->_setSqlError();
			return $nNull;
		}
	}

	/**
	 * @param bool $bAutoFree = true
	 * @return &array
	 */
	public function &GetNextArrayRecord($bAutoFree = true)
	{
		if ($this->rResultId)
		{
			$mResult = $this->rResultId->fetch(PDO::FETCH_ASSOC);
			if (!$mResult && $bAutoFree)
			{
				$this->FreeResult();
			}
			return $mResult;
		}
		else
		{
			$nNull = false;
			$this->_setSqlError();
			return $nNull;
		}
	}

	/**
	 * @param string $sTableName = null
	 * @param string $sFieldName = null
	 * @return int
	 */
	public function GetLastInsertId($sTableName = null, $sFieldName = null)
	{
		try
		{
			return (int) $this->oPDO->lastInsertId();
		}
		catch( Exception $e)
		{
			CApi::LogException($e);
		}
		
		return 0;
	}

	/**
	 * @return array
	 */
	public function GetTableNames()
	{
		if (!$this->Execute('SHOW TABLES'))
		{
			return false;
		}

		$aResult = array();
		while (false !== ($aValue = $this->GetNextArrayRecord()))
		{
			foreach ($aValue as $sValue)
			{
				$aResult[] = $sValue;
				break;
			}
		}

		return $aResult;
	}

	/**
	 * @param string $sTableName
	 * @return array
	 */
	public function GetTableFields($sTableName)
	{
		if (!$this->Execute('SHOW COLUMNS FROM `'.$sTableName.'`'))
		{
			return false;
		}

		$aResult = array();
		while (false !== ($oValue = $this->GetNextRecord()))
		{
			if ($oValue && isset($oValue->Field) && 0 < strlen($oValue->Field))
			{
				$aResult[] = $oValue->Field;
			}
		}

		return $aResult;
	}

	/**
	 * @param string $sTableName
	 * @return array
	 */
	public function GetTableIndexes($sTableName)
	{
		if (!$this->Execute('SHOW INDEX FROM `'.$sTableName.'`'))
		{
			return false;
		}

		$aResult = array();
		while (false !== ($oValue = $this->GetNextRecord()))
		{
			if ($oValue && isset($oValue->Key_name, $oValue->Column_name))
			{
				if (!isset($aResult[$oValue->Key_name]))
				{
					$aResult[$oValue->Key_name] = array();
				}
				$aResult[$oValue->Key_name][] = $oValue->Column_name;
			}
		}

		return $aResult;
	}

	/**
	 * @return bool
	 */
	public function FreeResult()
	{
		if ($this->rResultId)
		{
			if (!$this->rResultId->closeCursor())
			{
				$this->_setSqlError();
				return false;
			}
			else
			{
				$this->rResultId = null;
			}
		}
		return true;
	}

	/**
	 * @return int
	 */
	public function ResultCount()
	{
		if ($this->rResultId)
		{
			return $this->rResultId->rowCount(); // Only works for MySQL
//			return $this->SilentQuery("SELECT FOUND_ROWS()")->fetchColumn();
		}
		
		return 0;
	}

	/**
	 * @return void
	 */
	private function _setSqlError()
	{
		if ($this->IsConnected())
		{
			$aEr = $this->oPDO->errorInfo();
			$this->ErrorDesc = (string) implode("\r\n", is_array($aEr) ? $aEr : array()).' ['.$this->oPDO->errorCode().']';
			$this->ErrorCode = 0;
		}
		else
		{
			$this->ErrorDesc = 'No connection';
			$this->ErrorCode = -23456789;
		}

		if (0 < strlen($this->ErrorDesc))
		{
			$this->errorLog($this->ErrorDesc);
			throw new CApiDbException($this->ErrorDesc, $this->ErrorCode);
		}
	}
}
