<?
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

include("../../../lib/common.php");
checkLogin();
/*if (file_exists('../../lib/common.php')) 
{
	require_once('../../lib/common.php');
}

$usrid=$_SESSION['valid_ses_usr'];

$usrid = (! empty($_SESSION['valid_ses_usr'])) ? $_SESSION['valid_ses_usr'] : NULL;

if (!$usrid)
{
	echo 'Sorry incorrect parameter. Redirecting, standby...<meta http-equiv="refresh" content="3;URL=http://klas2.msu.edu.my">'; exit;
}*/
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta content="ITIC" NAME="author"> 
<meta content="2003-08-05T17:22:24" NAME="date">
<meta http-equiv="Cache-control" content="no-cache">
<meta http-equiv="Pragma" content="no-cache">
<link type="text/css" rel="stylesheet" href="../../themes-css/default.css">
<title>:: Image Insert Receipt ::</title>
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
	 
	$sql="select fu_document_filename from file_upload_student where student_matrix_no = $pid";
	$dbemppico=$db;
	$dbemppico->query($sql);
	$nxemppico=$dbemppico->next_record();  
	
	//sekiranya ada gambar. display gambar
	if (!empty($nxemppico))		 
 	{
?>
<h3>Image Insert Receipt</h3> 
<h4>The following file was successfully uploaded:</h4>

<table>
<col span="1" align="right">
	<!--<tr>
		<td><font color="red">Short description:</font></td>
		<td><?php //echo $dbemppico->f("pic_name");?></td>
	</tr>-->
	
	<tr>
		<td><font color="red">File type:</font></td>
		<td><?php echo $dbemppico->f("fu_document_filename");?></td>
	</tr>
	
	<tr>
		<td><font color="red">File:</font></td>
		<td><?php echo "<img src=\"image.php?pid=$pid\" height=170 width=126>";?></td>
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