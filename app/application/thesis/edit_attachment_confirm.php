<?php

/** * @author MJMZ
 * @copyright 2011
 */
	include("../../../lib/common.php");
	checkLogin();
	
	
if(isset($_POST['btnUpdate']) && ($_POST['btnUpdate'] <> "")) 
{    
	$fileDesc=$_POST['fileDesc'];
	$sql2 = "UPDATE file_upload_proposal
	SET fu_document_filedesc = '$fileDesc'
	WHERE fu_cd = '".$_GET['fc']."'
	AND attachment_level = '".$_GET['al']."'";
	
	$db->query($sql2);
    $db->next_record();
    $row_download = $db->rowdata();		
} 
?>
		
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Edit Attachment</title>
		<link rel="stylesheet" type="text/css" href="../../theme/css/<?php echo $css; ?>" />
		<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>
	</head>
	<body>
		<form id="form1" name="form1" method="post" enctype="multipart/form-data">	
		
		
		

			<?
			$sql1 = "SELECT fu_document_filename, fu_document_filedesc
					FROM file_upload_proposal
					WHERE fu_cd = '".$_GET['fc']."'
					AND attachment_level = '".$_GET['al']."'";
					
			$db->query($sql1);
			$db->next_record();
			$filename = $db->f('fu_document_filename');
			$filedesc = $db->f('fu_document_filedesc');
			?>
			<fieldset>
			<legend><strong>Attachment Details</strong></legend>
			<table>
				<tr>
					<td>File Name</td>
					<td>:</td>
					<td><label name="filename" type="text" id="filename"><?=$filename;?></label/></td>
				</tr>
				<tr>
					<td>File Description</td>
					<td>:</td>
					<td><input name="fileDesc" type="text" size="60" maxlength="50" value="<?=$filedesc;?>"/></td>
				</tr>
			</table>
			</fieldset>		
			<br/>
			<table>
				<tr>		
					<td><input type="submit" name="btnUpdate" value="Update" /></td>				
					<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='confirm_proposal.php';" /></td>		
				</tr>
			</table>
		</form>
	</body>
</html>