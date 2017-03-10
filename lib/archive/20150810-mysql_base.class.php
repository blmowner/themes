<?php

include("conf.php");
class mysql_base {
//	display:block;
//	padding-left:20px; 
	
  /* public: connection parameters */
  public $Host     = "";
  public $Database = "";
  public $User     = "";
  public $Password = "";

  /* public: configuration parameters */
  public $Auto_Free     = 0;     ## Set to 1 for automatic mysql_free_result()
  public $Debug         = 0;     ## Set to 1 for debugging messages.
  public $Halt_On_Error = "yes"; ## "yes" (halt with message), "no" (ignore errors quietly), "report" (ignore errror, but spit a warning)
  public $Seq_Table     = "db_sequence";

  /* public: result array and current row number */
  public $Record   = array();
  public $Row;

  /* public: current error number and error text */
  public $Errno    = 0;
  public $Error    = "";

  /* public: this is an api revision, not a CVS revision. */
  public $type     = "mysql";
  public $revision = "1.2";

  /* private: link and query handles */
  private $Link_ID  = 0;
  private $Query_ID = 0;
  


  /* public: constructor */
  public function mysql_base($query = "") {
      $this->query($query);
  }

  /* public: some trivial reporting */
  public function link_id() {
    return $this->Link_ID;
  }

  public function query_id() {
    return $this->Query_ID;
  }

  /* public: connection management */
  public function connect($Database = "", $Host = "", $User = "", $Password = "") {
    /* Handle defaults */
    if ("" == $Database)
      $Database = $this->Database;
    if ("" == $Host)
      $Host     = $this->Host;
    if ("" == $User)
      $User     = $this->User;
    if ("" == $Password)
      $Password = $this->Password;
      
    /* establish connection, select database */
    if ( 0 == $this->Link_ID ) {
	//printf("Host: %s<br>\n",$Host);
     $this->Link_ID=mysql_pconnect($Host, $User, $Password);
        

      if (!$this->Link_ID) {
	 //$connect = "connect($Host, $User, \$Password) failed.";
        //$this->halt("connect($Host, $User, \$Password) failed.");
	 //printf("Host: %s<br>\n",$connect);
        $this->halt("connect($Host, $User, \$Password) failed.");
        return 0;
      }

      if (!@mysql_select_db($Database,$this->Link_ID)) {
        $this->halt("cannot use database ".$this->Database);
        return 0;
      }
    }
    
    return $this->Link_ID;
  }

  /* public: discard the query result */
  public function free() {
      @mysql_free_result($this->Query_ID);
      $this->Query_ID = 0;
  }

  /* public: perform a query */
  public function query($Query_String) {
    /* No empty queries, please, since PHP4 chokes on them. */
    if ($Query_String == "")
      /* The empty query string is passed on from the constructor,
       * when calling the class without a query, e.g. in situations
       * like these: '$db = new DB_Sql_Subclass;'
       */
      return 0;

    if (!$this->connect()) {
      return 0; /* we already complained in connect() about that. */
    };

    # New query, discard previous result.
    if ($this->Query_ID) {
      $this->free();
    }

    if ($this->Debug)
      printf("Debug: query = %s<br>\n", $Query_String);

    $this->Query_ID = @mysql_query($Query_String,$this->Link_ID);
    $this->Row   = 0;
    //$this->Errno = mysql_errno();
    //$this->Error = mysql_error();
    if (!$this->Query_ID) {
      $this->halt("Invalid SQL: ".$Query_String);
    }

    # Will return nada if it fails. That's fine.
    return $this->Query_ID;
  }

  /* public: walk result set */
  public function next_record() {
    if (!$this->Query_ID) {
      $this->halt("next_record called with no query pending.");
      return 0;
    }

    $this->Record = @mysql_fetch_array($this->Query_ID);    
    $this->Row   += 1;
    $this->Errno  = mysql_errno();
    $this->Error  = mysql_error();

    $stat = is_array($this->Record);
    if (!$stat && $this->Auto_Free) {
      $this->free();
    }
    return $stat;
  }
/* public: walk result set */
  public function row_record() {
    if (!$this->Query_ID) {
      $this->halt("next_record called with no query pending.");
      return 0;
    }

    $this->Record = @mysql_fetch_row($this->Query_ID);
    $this->Row   += 1;
    $this->Errno  = mysql_errno();
    $this->Error  = mysql_error();

    $stat = is_array($this->Record);
    if (!$stat && $this->Auto_Free) {
      $this->free();
    }
    return $stat;
  }


  /* public: table locking */
  public function lock($table, $mode="write") {
    $this->connect();
    
    $query="lock tables ";
    if (is_array($table)) {
      while (list($key,$value)=each($table)) {
        if ($key=="read" && $key!=0) {
          $query.="$value read, ";
        } else {
          $query.="$value $mode, ";
        }
      }
      $query=substr($query,0,-2);
    } else {
      $query.="$table $mode";
    }
    $res = @mysql_query($query, $this->Link_ID);
    if (!$res) {
      $this->halt("lock($table, $mode) failed.");
      return 0;
    }
    return $res;
  }
  
  public function unlock() {
    $this->connect();

    $res = @mysql_query("unlock tables");
    if (!$res) {
      $this->halt("unlock() failed.");
      return 0;
    }
    return $res;
  }

  /* return number of rows result (counting) */
  public function num_rows() {
    return @mysql_num_rows($this->Query_ID);
  }

  public function num_fields() {
    return @mysql_num_fields($this->Query_ID);
  }

  /* public: shorthand notation */
  public function nf() {
    return $this->num_rows();
  }

  public function np() {
    print $this->num_rows();
  }

  public function f($Name) {
    if(isset($this->Record[$Name]))
      return stripslashes($this->Record[$Name]);
    else 
      return "";
  }

  //mysql_fetch_row($result)
  public function rowdata() {	
    return $this->Record;
  }
  
  //mysql_fetch_array()
  public function fetchArray() {
    return @mysql_fetch_array($this->Query_ID);
  }
  
  /* private: error handling */
  public function halt($msg) {
    $this->Error = @mysql_error($this->Link_ID);
    $this->Errno = @mysql_errno($this->Link_ID);
    if ($this->Halt_On_Error == "no")
      return;

    $this->haltmsg($msg);

    if ($this->Halt_On_Error != "report")
      die("Session halted.");
  }

  public function haltmsg($msg) 
  {
    $error = $this->Error;
	$errorno = $this->Errno;
	$curdatetime = "date";
  	//$curdatetime = date("d-M-Y h:i:s A");	
  	$location  = $_SERVER['PHP_SELF'];

	//$curdatetime1 = date("Ymd");
	$curdatetime1 = "date";
	//$file = "C:\AppServ\www\demo-postgrad.msu.edu.my\app\application\log\postgrad".$curdatetime1.".txt";
	$file = "/home/httpd/demo-postgrad.msu.edu.my/app/application/log/postgrad".$curdatetime1.".txt";
	
	if (file_exists($file)) 
	{
		//////////////////////////APPEND LOG///////////////////////////
		$fp = fopen($file, 'a');
		$stringData = "Date Time: ".$curdatetime."\r\n";
		fwrite($fp, $stringData);
		$stringData = "Error no : ".$errorno.": ".$error."\r\n";
		fwrite($fp, $stringData);
		$stringData = $msg."\r\n";
		fwrite($fp, $stringData);
		$stringData = "Source: ".$location."\r\n";
		fwrite($fp, $stringData);
		$stringData = "Triggered By: ".$_SESSION['user_id']."\r\n";
		fwrite($fp, $stringData);
		$stringData = "\r\n";
		fwrite($fp, $stringData);
		fclose($fp);
		//////////////////////////END APPEND LOG///////////////////////////
				
	} 
	else 
	{
		//////////////////////////WRITE LOG///////////////////////////
		$fp = fopen($file, 'w');
		$stringData = "Date Time: ".$curdatetime."\r\n";
		fwrite($fp, $stringData);
		$stringData = "Error no : ".$errorno.": ".$error."\r\n";
		fwrite($fp, $stringData);
		$stringData = $msg."\r\n";
		fwrite($fp, $stringData);
		$stringData = "Source: ".$location."\r\n";
		fwrite($fp, $stringData);
		$stringData = "Triggered By: ".$_SESSION['user_id']."\r\n";
		fwrite($fp, $stringData);
		$stringData = "\r\n";
		fwrite($fp, $stringData);
		fclose($fp);
		
		//////////////////////////END WRITE LOG///////////////////////////

		
	}
?>
<style>
.error {
	border:1px solid #FF0000;
	background: #F8D1D2; 
	padding:2px 5px 5px 10px;
	color:#FF0000;
    margin-bottom: 2px;
	width: 98%;
	}
.error span {
	background:url(../../images/error.png) no-repeat left;

	}
</style>
<?
		
	$selectfrom = "SELECT const_value
	FROM base_constant WHERE const_term = 'EMAIL_ADMIN'";
	$resultfrom = $this->query($selectfrom);
	$this->next_record();
	$adminEmail =$this->f('const_value');
	$error = $this->Error;
	$errorno = $this->Errno;
	$adminEmail = "";
  	$contact = "Opps! The function is not working as desired. The following details has been captured.";
	$contact1 = "Please email it to administrator ".$adminEmail." for rectification.<br>";
	$detail = "1) <b>Error detail</b> - ".$error."</pre><br>
				2) <b>Date & time of error</b> - ".$curdatetime."<br>
				3) <b>Source of error</b> - ".$location."<br>
				4) ".$msg;

	//<font color = \"#FF0000\" size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">
	//<div id = \"notes\" class = \"notes\"  style=\"width: 96%; background: #FFB9B9; padding: 3px; border: 2px solid #FF0000 ; color:Black; border-radius: 10px;\">			
	$table = "<div class = \"error\">
				<span><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">Application Error</font></span>
				</div>
				<table style =\"table-layout: auto;\">
						<tr> 
						  <td ><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">".$contact."</font></td>
						</tr>
						<tr>   
						  <td><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">".$detail."</font></td>
						</tr>
						<tr> 
						  <td ><br><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">".$contact1."</font></td>
						</tr>
				</table>";
													
    printf("%s<br>\n",$table);
		
	//include("/home/httpd/demo-postgrad.msu.edu.my/app/application/email/email_error.php");
  }

  
}
?>