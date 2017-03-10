<?php

/** * @author MJMZ
 * @copyright 2011
 */
	include("../../../lib/common.php");
	checkLogin();
	
	
    
    $sql_delete_attachment = "DELETE FROM file_upload_progress
	WHERE fu_cd = '".$_GET['fc']."'
	AND attachment_level = '".$_GET['al']."'
	AND attachment_type = '".$_GET['at']."'
	AND status = 'A'";
	
	$db_klas2->query($sql_delete_attachment);
    $db_klas2->next_record();
    $row_download = $db_klas2->rowdata();				
    
    ?>
		
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Picture Upload</title>
<link rel="stylesheet" type="text/css" href="../../theme/css/<?php echo $css; ?>" />
<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>
</head>

<body>

		<fieldset>
		<legend><span style="color:#FF0000"> Notification Message</span></legend>
		<table>
			<tr>
				<td>The attachment has been deleted successfully.</td>
			</tr>
		</table>
		<br/>
		<table>
			<tr>		
				<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='submit_progress.php';" /></td>		
			</tr>
		</table>
	</fieldset>
</body>