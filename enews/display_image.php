<?php 

include("include/cfg.php");

ob_clean();

$userid=$_SESSION['user'];

$sqlX = "INSERT INTO ejournal.logs(VisIP, VisRef, VisUrl, VisDate, VisAgent, VisLogin) VALUES(\"".$HTTP_SERVER_VARS['REMOTE_ADDR']."\", \"".$HTTP_SERVER_VARS['HTTP_REFERER']."\", \"".$HTTP_SERVER_VARS['REQUEST_URI']."\", NOW(), \"".$HTTP_SERVER_VARS['HTTP_USER_AGENT']."\", \"".$_SESSION['user']."\")";

$sql = "SELECT imgtype,imgdata FROM eadvertorial_upload WHERE fid=". $_GET["fid"]; 

$select = mysql_query($sql,$CONN) or die (mysql_error().$sql);

$contenttype = @mysql_result($select,0,"imgtype"); 
$image = @mysql_result($select,0,"imgdata"); 

header("Content-type: $contenttype"); 
echo $image; 

mysql_close($CONN);

?>