<?
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(-1);


include("../../../lib/common.php");
checkLogin(); 
$userid=$_SESSION['user_id'];
$referenceNo = $_GET['ref'];

//echo $studentMatrixNo = $_REQUEST['mn'];
//$thesisId = $_GET['tid'];
/*$proposalId = $_GET['pid'];
$progressId = $_GET['prgid'];
$attachmentLevel = $_GET['al'];
$attachmentType = $_GET['at'];
*/


function runnum($column_name, $tblname) 
{ 
    global $db_klas2;
    
    $run_start = "001";
    
    $sql_slct_max = "SELECT MAX($column_name) AS run_id FROM $tblname";
    $sql_slct = $db_klas2;
    $sql_slct->query($sql_slct_max);
    $sql_slct->next_record();

    if($sql_slct->num_rows($sql_slct_max)== 0 || $sql_slct->f("run_id")==NULL) 
	{
        $run_id = date("Ymd").$run_start;
    } 
	else 
	{
        $todate = date("Ymd");
        
        if($todate > substr($sql_slct->f("run_id"),0,8)) 
		{
            $run_id = $todate.$run_start;
        } 
		else 
		{
            $run_id = $sql_slct->f("run_id") + 1; 
        }
    }
    return $run_id;
}

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
			case "image/x-MS-bmp";       
				$mimeName = "Windows Bitmap";
				break;
			case "image/doc";       
				$mimeName = "Ms Word";
				break;
			case "image/docx";       
				$mimeName = "Ms Word";
				break;
			case "image/xls";       
				$mimeName = "Ms Excel";
				break;
			case "image/pdf";       
				$mimeName = "PDF File";
				break;
			default: 
				$mimeName = "Unknown image type";
		}
	
		$fu_cd = runnum('fu_cd','file_upload_progress');
		$fileContents = addSlashes(fread(fopen($xpic_file, "r"), filesize($xpic_file)));

        $FileName=$_FILES['xpic_file']['name'];
		
		if ($_GET['al'] == 'S') { //S - Student
			if ($referenceNo!="") {
				$sql2="INSERT INTO file_upload_progress 
				(fu_cd, fu_document_filename,fu_document_filedesc,fu_document_filetype,  fu_document_filedata, 
				student_matrix_no, progress_id, reference_no,
				insert_date, insert_by, modify_date, modify_by,pg_proposal_id, attachment_level, attachment_type, status, upload_status)	
				VALUES ('$fu_cd','$FileName','$fileDesc','$xpic_file_type','$fileContents','$userid',".$_GET['prgid'].",'$referenceNo',
				now(),'$userid',now(),'$userid','".$_GET['pid']."', '".$_GET['al']."', '".$_GET['at']."','A', 'TMP' )";
				
			}
			else {
				$sql2="INSERT INTO file_upload_progress 
				(fu_cd, fu_document_filename,fu_document_filedesc,fu_document_filetype,  fu_document_filedata, 
				student_matrix_no, progress_id, reference_no,
				insert_date, insert_by, modify_date, modify_by,pg_proposal_id, attachment_level, attachment_type, status, upload_status)	
				VALUES ('$fu_cd','$FileName','$fileDesc','$xpic_file_type','$fileContents','$userid',null,null,
				now(),'$userid',now(),'$userid','".$_GET['pid']."', '".$_GET['al']."', '".$_GET['at']."','A', 'TMP' )";
				
			}
		}
		else {//F - Faculty
			if ($referenceNo!="") {
				$sql2="INSERT INTO file_upload_progress 
				(fu_cd, fu_document_filename,fu_document_filedesc,fu_document_filetype,  fu_document_filedata, pg_employee_empid, progress_id, 
				insert_date, insert_by, modify_date,
				modify_by,pg_proposal_id, attachment_level, attachment_type, status, upload_status)	
				VALUES ('$fu_cd','$FileName','$fileDesc','$xpic_file_type','$fileContents','$userid',".$_GET['prgid'].",'$referenceNo',
				now(),'$userid',now(),'$userid','".$_GET['pid']."',	'".$_GET['al']."', '".$_GET['at']."','A', 'TMP' )";
			}
			else {
				$sql2="INSERT INTO file_upload_progress 
				(fu_cd, fu_document_filename,fu_document_filedesc,fu_document_filetype,  fu_document_filedata, pg_employee_empid, progress_id, 
				insert_date, insert_by, modify_date,
				modify_by,pg_proposal_id, attachment_level, attachment_type, status, upload_status)	
				VALUES ('$fu_cd','$FileName','$fileDesc','$xpic_file_type','$fileContents','$userid',null,null,
				now(),'$userid',now(),'$userid','".$_GET['pid']."',	'".$_GET['al']."', '".$_GET['at']."','A', 'TMP' )";
			}
		}
		
			
		if (($db->query($sql2)) && @ mysql_affected_rows() == 1) 
		{
			echo "<script language=\"javascript\">";
			echo "opener.location.href=\"submit_progress_attachment.php?tid=".$_GET['tid']."&pid=".$_GET['pid']."&mn=".$_GET['mn']."&prgid=".$_GET['prgid']."&al=".$_GET['al']."&at=".$_GET['at']."&ref=".$_GET['ref']."\";";
			echo "document.location.href=\"submit_progress_receipt.php?data=$fileContent&status=T&userId=$userid&ppid=$pid&fucd=$fu_cd\";";
			echo "</script>";			
		}
		else 
		{
			header("Location: submit_progress_receipt.php?status=F&userId=$userid&ppid=$pid&fucd=$fu_cd");
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
<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>

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
<form name="frmEmpUpdPic" method="post"  enctype="multipart/form-data">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
	<td>
		<table border="0" cellpadding="0" cellspacing="0" bordercolorlight="#cccccc" bordercolordark="#eeeeee" width="100%">
			<tr>
				<td valign="top"><strong>Upload Attachment</strong></td>
			</tr>
			<tr>
			  <td>Attachment File</td>
			  <td>:</td>
			  <td width="67%" ><p align="left"><input type="file" name="xpic_file" size="25"></td>				
			</tr>
			<tr>
				<td>File Description</td>
				<td>:</td>
				<td align="left"><div align="left">
				  <input name="fileDesc" type="text" size="50" maxlength="50" />
				</div></td>
			</tr>
		</table>
		<br/>
		<table>
			<tr>			
				<td align="middle">
				    <td><input type="button" value="Insert Attachment" onClick="validate();" name="B2"></td>
				    <td><input type="reset" value="Reset" name="btnReset">  </td>
			        <td><input type="button" value="Exit" name="Cancel" onClick="window.close()"></td>		      
			</tr>
		</table>
	</td>
	</tr>
</table>
</form>
</body>
</html>