<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: accept_invitation_detail.php
//
// Created by: Zuraimi
// Created Date: 21-Jul-2015
// Modified by: Zuraimi
// Modified Date: 21-Jul-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

session_start();
$userid=$_SESSION['user_id'];
$invitationDetailId=$_REQUEST['vdid'];

if(isset($_POST['btnUpdate']) && ($_POST['btnUpdate'] <> ""))
{	
	$sql2 = "UPDATE pg_invitation_detail
	SET acceptance_remarks = '$acceptanceRemarks'
	WHERE id = '$invitationDetailId'";

	$result2 = $dba->query($sql2);
	$dba->next_record();
}

$sql1="SELECT acceptance_remarks
FROM pg_invitation_detail
WHERE id = '$invitationDetailId'";

$result1 = $dba->query($sql1);
$dba->next_record();
$acceptanceRemarks = $dba->f('acceptance_remarks');
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Untitled Document</title>
		<link rel="stylesheet" type="text/css" href="../../theme/css/<?php echo $css; ?>" />
		<link rel="stylesheet" type="text/css" href="../../theme/css/colorbox.css" media="screen" />
		<link rel="stylesheet" href="../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />    
		<script src="../../lib/js/jquery.min2.js"></script>
		<script src="../../lib/js/jquery.colorbox.js"></script>
		<script src="../../lib/js/jquery.mask_input-1.3.js"></script>
		<script type="text/javascript" src="../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
		<script src="../../lib/js/datePicker/jquery.ui.core.js"></script>
		<script src="../../lib/js/datePicker/jquery.ui.widget.js"></script>
		<script src="../../lib/js/datePicker/jquery.ui.datepicker.js"></script>
		<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>
		<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>	
	</head>
	<body>
		<form id="form2" name="form2" method="post" enctype="multipart/form-data">
			<fieldset>
			<legend><strong>Remarks</strong></legend>
				<table>	
					<tr>
						<td><textarea name="acceptanceRemarks" cols="30" rows="3" id="acceptanceRemarks" class="ckeditor"><?=$acceptanceRemarks?></textarea></td>														
					</tr>	
				</table>
				<br/>
				<table>
					<tr>
						<td><input type="submit" name="btnUpdate" value="Update" /></td>
						<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='accept_invitation.php';" /></td>
					</tr>
				</table>
			</fieldset>
		</form>
	</body>
</html>