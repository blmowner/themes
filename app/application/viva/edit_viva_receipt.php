<?
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

include("../../../lib/common.php");
checkLogin();
ini_set('session.cache_limiter','public');
session_cache_limiter(true);
$fucd = $_GET['fucd'];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
	<link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
    <link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
   	<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
	<link rel="stylesheet" href="../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
	<script src="../../../lib/js/jquery.min2.js"></script>
	<script src="../../../lib/js/jquery.colorbox.js"></script>
	<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
	<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>
</head>
<body>

<?php

$pid = "0";
if (isset($_GET['userId'])) 
{
	$pid = (get_magic_quotes_gpc()) ? $_GET['userId'] : addslashes($_GET['userId']);
}	  

function clean($input, $maxlength)
{
	$input = substr($input, 0, $maxlength);
	$input = EscapeShellCmd($input);
	return ($input);
}
	
//include 'db.inc';
  
$status = clean($status, 1);
$file = clean($file, 50);

// did the insert operation succeed?
switch ($status)
{
	case "T":
	 
	$sql="SELECT fu_document_filename 
			FROM file_upload_viva
			WHERE fu_cd = '$fucd'";
	$dbemppico=$db;
	$dbemppico->query($sql);
	$nxemppico=$dbemppico->next_record();  
	
	//sekiranya ada gambar. display gambar
	if (!empty($nxemppico))		 
 	{
?>
<table>
	<tr>
		<td><label><strong>Attachment Notification</strong> 		
	</tr>
	<tr>		
		<td><label>The following file has been uploaded successfully.</label></td>
	</tr>
</table>

<table>
<col span="1" align="right">
	<tr>
		<td><font color="red">Filename:</font></td>
		<td><?php echo $dbemppico->f("fu_document_filename");?></td>
	</tr>
</table>
<?php
     } // if mysql_fetch_array()

		break;
		
		case "F":
		// No, insert operation failed
		// Show an error message
		echo "The file insert operation failed.";
		echo "<br>Contact the system administrator.";
		
		break;
		
		default:
		// User did not provide a status parameter
		echo "You arrived unexpectedly at this page.";          
	} // end of switch
?>
<br>

<center><!--input type="button" value="Done" name="Done" onClick="opener.parent.contents.location.reload();self.close();"-->
<input type="button" value="Done" name="Done" onClick="window.opener.location.reload();self.close();">
</center>
<!-- <h5>Click <a href="image_insert.php">here</a> to reupload the image (Prevous uploaded image will be overwrite).</h5> -->
</body>
</html>