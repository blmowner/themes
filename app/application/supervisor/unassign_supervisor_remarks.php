<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: unassign_supervisor_biodata.php
//
// Created by: Zuraimi
// Created Date: 16-Jun-2015
// Modified by: Zuraimi
// Modified Date: 16-Jun-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

session_start();
$userid=$_SESSION['user_id'];
$thesisId=$_REQUEST['tid'];
$proposalId=$_REQUEST['pid'];
$staffId=$_REQUEST['sid'];
$matrixNo=$_REQUEST['mn'];
$supervisorId=$_REQUEST['id'];




if(isset($_POST['btnUpdate']) && ($_POST['btnUpdate'] <> ""))
{	
	$unassignedRemarksStaff = $_POST['unassignedRemarksStaff'];
	$curdatetime = date("Y-m-d H:i:s");
	$msg = array();
	
	if ($unassignedRemarksStaff !='') {
		$sql2 = "UPDATE pg_supervisor
		SET unassigned_remarks = '$unassignedRemarksStaff', modify_by = '$user_id', modify_date = '$curdatetime'
		WHERE id = '$supervisorId'";
	}
	else {
		$sql2 = "UPDATE pg_supervisor
		SET unassigned_remarks = null, modify_by = '$user_id', modify_date = '$curdatetime'
		WHERE id = '$supervisorId'";		
	}

	$result2 = $dba->query($sql2);
	$dba->next_record();
	
	
	$msg[] = "<div class=\"success\"><span>Remarks for Supervisor $staffId has been updated successfully.</span></div>";
}


?>

	

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Untitled Document</title>
		<link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
		<link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
		<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />		
		<script src="../../../lib/js/jquery.min2.js"></script>
		<script src="../../../lib/js/jquery.colorbox.js"></script>
		<script src="../../../lib/js/jquery.mask_input-1.3.js"></script>
		<script src="../../../lib/js/jquery.min2.js"></script>
   		<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
    	<script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
		<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>	
		<script language="JavaScript" src="../../../lib/js/windowopen.js"></script>	
		<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>
	</head>
	<body>
	<?php
    if(!empty($msg)) 
	{
        foreach($msg as $err) 
		{
            echo $err;
        }
    }
?>
		<form id="form2" name="form2" method="post" enctype="multipart/form-data">
			<?			
			$sql1 = "SELECT unassigned_remarks
			FROM pg_supervisor
			WHERE id = '$supervisorId'";

			$result1 = $db->query($sql1);
			$db->next_record();

			$unassignedRemarks=$db->f('unassigned_remarks');		
				
			?>
			<fieldset>
			<legend><strong>Brief Biodata</strong></legend>
			<table>
				<tr>
					<td><label>Staff ID</label></td>
					<td>:</td>
					<td><label type="text" name="supervisorStaffId" ><?=$staffId?></label></td>
				</tr>
				<?
				if (substr($supervisorId,0,3) != 'S07') {
					$dbConn = $dbc;
				}
				else {
					$dbConn = $dbc1;
				}
				$sql = "SELECT name
				FROM new_employee
				WHERE empid = '$staffId'";
				
				$dbConn->query($sql);
				$dbConn->next_record();
				$staffName = $dbConn->f('name');
				?>
				<tr>
					<td><label>Supervisor Name</label></td>
					<td>:</td>
					<td><label type="text" name="supervisorName" ><?=$staffName?></label></td>
				</tr>
				<tr>
				</tr>
			</table>
			<table>	
				<tr>
					<td><br/><label>Enter your remarks here:-</label></td>					
				</tr>
				<tr>
					<td><textarea name="unassignedRemarksStaff" cols="30" rows="3" id="unassignedRemarksStaff" class="ckeditor"><?=$unassignedRemarks?></textarea></td>														
				</tr>	
			</table>
			<br/>
			<table>
				<tr>
					<td><input type="submit" name="btnUpdate" value="Update" /></td>
					<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../supervisor/unassign_supervisor_change.php?tid=<?=$thesisId;?>&pid=<?=$proposalId;?>&mn=<?=$matrixNo;?>';" /></td>
				</tr>
			</table>
			</fieldset>
		</form>
	</body>
</html>