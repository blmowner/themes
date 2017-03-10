<?
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

include("../../../lib/common.php");
checkLogin(); 

function clean($input, $maxlength)
{
	$input = substr($input, 0, $maxlength);
	$input = EscapeShellCmd($input);
	return ($input);
}

$pid = "0";
if (isset($_GET['pid'])) 
{
$pid = (get_magic_quotes_gpc()) ? $_GET['pid'] : addslashes($_GET['pid']);
}	


$xpic_desc = clean($xpic_desc, 50);

// Was a file uploaded?
if (is_uploaded_file($xpic_file))
{
	switch ($xpic_file_type)
	{
	  	case "image/gif";       
			$mimeName = "GIF Image";
			break;
	  	case "image/jpg";          
			$mimeName = "JPEG Image";
			break;
		case "image/jpeg";          
			$mimeName = "JPEG Image";
			break;
	  	case "image/pjpeg";          
			$mimeName = "JPEG Image";
			break; 
	  	case "image/png";       
			$mimeName = "PNG Image";
			break;
		case "image/tiff";       
			$mimeName = "TIFF Image";
			break;
	  	case "image/x-MS-bmp";       
			$mimeName = "Windows Bitmap";
			break;
	  	default: 
			$mimeName = "Unknown image type";
	}
	
	$fileContents = addSlashes(fread(fopen($xpic_file, "r"), filesize($xpic_file)));
	//$fileContents=$_POST["xpic_file"];
	$sql="	UPDATE fu_upload_student SET ".
				//"pic_name = '$xpic_desc', ".
				"fu_document_filetype = '$xpic_file_type', ".
				"fu_document_filename = '$mimeName', ".
				"fu_document_filedata = '$fileContents', ".
				"modify_date = now(), ".
				"modify_by = '$pid' ".	
			"WHERE student_matrix_no='$pid' ";
//echo $sql;
	if (($db->query($sql)) && @ mysql_affected_rows() == 1) 
	{
		header("Location: image_receipt.php?data=$fileContent&status=T&userId=$pid");
	}
	else 
	{
		header("Location: image_receipt.php?status=F&userId=$pid");
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Picture Upload</title>
<link rel="stylesheet" type="text/css" href="../../theme/css/<?php echo $css; ?>" />
<link rel="stylesheet" type="text/css" href="../../theme/css/colorbox.css" media="screen" />
<link rel="stylesheet" href="../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />

<script src="../../../lib/js/jquery.min2.js"></script>
<script src="../../../lib/js/jquery.colorbox.js"></script>
<script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
<script language="JavaScript" type="text/javascript" src="../../../lib/js/tooltip.js"></script>
<script src="../../../lib/js/datePicker/jquery.ui.core.js"></script>
<script src="../../../lib/js/datePicker/jquery.ui.widget.js"></script>
<script src="../../../lib/js/datePicker/jquery.ui.datepicker.js"></script>
<script language="JavaScript" src="../../../lib/js/windowopen.js"></script>

<script language="JavaScript">
function validate() 
{
	var pass=true;
	var docz=document.frmEmpUpdPic.elements;
	for (i=0;i<docz.length;i++) 
	{
		var tempobj=docz[i];
		if (tempobj.name.substring(0,1)=="x") 
		{
			if (((tempobj.type=="text"||tempobj.type=="textarea"||tempobj.type=="password")&&
			tempobj.value=='')||(tempobj.type.toString().charAt(0)=="s"&&
			tempobj.selectedIndex==0)) 
			{
				pass=false;
				break;
			}
		}
	}
	if (!pass) 
	{
		shortFieldName=tempobj.name.substring(1,30).toUpperCase();
		tempobj.focus();
		alert("Please make sure the "+shortFieldName+" field was properly completed.");
		return false;
	}
	document.frmEmpUpdPic.submit();
}
</script>
</head>
		
<body>
<form name="frmEmpUpdPic" method="post" action="image_insert.php?pid=<?=$pid?>" enctype="multipart/form-data">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
	<td>
		<table border="1" cellpadding="0" cellspacing="0" bordercolorlight="#cccccc" bordercolordark="#eeeeee" width="100%">
			<tr>
				<td width="100%" colspan="8" valign="top" height="17">&nbsp;Upload Picture</td>
			</tr>
			<!--tr>
				<td width="25%" ><p align="left"><font size="1">&nbsp;Short Description:</font></td>
				<td width="75%" ><p align="left">&nbsp;<input type="text" name="xpic_desc" size="40"></td>
			</tr-->
			<tr>
				<td width="25%" ><p align="left"><font size="1">&nbsp;Picture File:</font></td>
				<td width="75%" ><p align="left">&nbsp;<input type="file" name="xpic_file" size="25"></td>
			</tr>
			<tr>
				<td width="100%" colspan="8" align="middle">
				<input type="button" value="Insert Picture" onClick="validate();" name="B2">
				<input type="reset" value="Reset" name="btnReset">  
				<input type="button" value="Exit" name="Cancel" onClick="window.close()"></td>
			</tr>
		</table>
	</td>
	</tr>
</table>
</form>
</body>
</html>