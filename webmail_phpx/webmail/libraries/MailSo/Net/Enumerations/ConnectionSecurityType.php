<?php

/*
 * Copyright 2004-2014, AfterLogic Corp.
 * Licensed under AGPLv3 license or AfterLogic license
 * if commercial version of the product was purchased.
 * See the LICENSE file for a full license statement.
 */

namespace MailSo\Net\Enumerations;

/**
 * @category MailSo
 * @package Net
 * @subpackage Enumerations
 */
class ConnectionSecurityType
{
	const NONE = 0;
	const SSL = 1;
	const STARTTLS = 2;
	const AUTO_DETECT = 9;

	/**
	 * @param int $iPort
	 * @param int $iSecurityType
	 *
	 * @return bool
	 */
	public static function UseSSL($iPort, $iSecurityType)
	{
		$iPort = (int) $iPort;
		$iResult = (int) $iSecurityType;
		if (self::AUTO_DETECT === $iSecurityType)
		{
			switch (true)
			{
				case 993 === $iPort:
				case 995 === $iPort:
				case 465 === $iPort:
					$iResult = self::SSL;
					break;
			}
		}

		if (self::SSL === $iResult && !\in_array('ssl', \stream_get_transports()))
		{
			$iResult = self::NONE;
		}

		return self::SSL === $iResult;
	}

	/**
	 * @param bool $bSupported
	 * @param int $iSecurityType
	 * @param bool $bHasSupportedAuth = true
	 *
	 * @return bool
	 */
	public static function UseStartTLS($bSupported, $iSecurityType, $bHasSupportedAuth = true)
	{
		return ($bSupported &&
			(self::STARTTLS === $iSecurityType || 
				(self::AUTO_DETECT === $iSecurityType && (!$bHasSupportedAuth || \MailSo\Config::$PreferStartTlsIfAutoDetect))) &&
			\defined('STREAM_CRYPTO_METHOD_TLS_CLIENT') && \MailSo\Base\Utils::FunctionExistsAndEnabled('stream_socket_enable_crypto'));
	}
}
