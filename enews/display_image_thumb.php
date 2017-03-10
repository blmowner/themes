<?php 

include("include/cfg.php");

ob_clean();


$userid=$_SESSION['user'];

$sql = "SELECT imgtype,imgdataT FROM eadvertorial_upload WHERE fid='". mysql_real_escape_string($_GET["fid"])."'"; 

$select = mysql_query($sql,$CONN) or die (mysql_error().$sql);

$contenttype = @mysql_result($select,0,"imgtype"); 
$imageT = @mysql_result($select,0,"imgdataT"); 

header("Content-type: $contenttype"); 
echo $imageT; 

mysql_close($CONN);
?>