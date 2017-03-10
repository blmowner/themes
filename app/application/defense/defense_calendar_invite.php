<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: defense_calendar_invite.php
//
// Created by: Zuraimi
// Created Date: 13-July-2015
// Modified by: Zuraimi
// Modified Date: 13-July-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

session_start();
$user_id=$_SESSION['user_id'];
$calendarId = $_GET['cid'];
$thesisId=$_GET['tid'];
$studentMatrixNo=$_GET['mn'];

if(isset($_POST['btnCancel']) && ($_POST['btnCancel'] <> ""))
{					
	$invitationDetailId = $_POST['invitationDetailId'];
	$invitedStatusId = $_POST['invitedStatusId'];

	if (sizeof($_POST['invite_checkbox'])>0) {
		$curdatetime = date("Y-m-d H:i:s");
		while (list ($key,$val) = @each ($_POST['invite_checkbox'])) 
		{						
			$sql6 = "UPDATE pg_invitation_detail
			SET ref_invite_status_id = 'CAN', cancelled_date = '$curdatetime'
			WHERE id = '$invitationDetailId[$val]'
			AND ref_invite_status_id = '$invitedStatusId[$val]'
			AND status = 'A'";
			
			$db->query($sql6);
			
			$no = $val + 1;
			$msg[] = "<div class=\"success\"><span>The invitation cancellation for the selected Evaluation Panel (record no. $no) has been done successfully.</span></div>";						
		}				
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please tick which Evaluation Panel need to cancel the invitation!</span></div>";
		$tickAlert = true;
	}
}

$employeeIdArray = Array();
$employeeNameArray = Array();
$acceptanceStatusArray = Array();
$acceptanceDateArray = Array();
$acceptanceStatusDescArray = Array();
$examinerRoleArray = Array();

$sql = "SELECT b.id as invitation_detail_id, b.pg_employee_empid, c.ref_supervisor_type_id, d.description AS examiner_role, 
DATE_FORMAT(b.cancelled_date,'%d-%b-%Y %h:%i %p') AS cancelled_date, 
DATE_FORMAT(b.acceptance_date,'%d-%b-%Y %h:%i %p') AS acceptance_date, b.acceptance_status,
DATE_FORMAT(b.assigned_date,'%d-%b-%Y %h:%i %p') AS assigned_date, b.assigned_by,
e.description as acceptance_status_desc, b.ref_invite_status_id, f.description as invited_status_desc
FROM pg_invitation a
LEFT JOIN pg_invitation_detail b ON (b.pg_invitation_id = a.id)
LEFT JOIN pg_supervisor c ON (c.id = b.pg_supervisor_id)
LEFT JOIN ref_supervisor_type d ON (d.id = c.ref_supervisor_type_id) 
LEFT JOIN ref_acceptance_status e ON (e.id = b.acceptance_status)
LEFT JOIN ref_invite_status f ON (f.id = b.ref_invite_status_id)
WHERE c.pg_student_matrix_no = '$studentMatrixNo'
AND c.pg_thesis_id = '$thesisId'
AND c.ref_supervisor_type_id in ('EI','EE','EC','XE','SV','CS', 'XS')
AND a.pg_calendar_id = '$calendarId'
AND c.status = 'A'
AND d.status = 'A'
ORDER BY f.seq, d.seq";
				
$result_sql = $dbe->query($sql); 
$dbe->next_record();
$row_cnt = mysql_num_rows($result_sql);

$no=0;
if ($row_cnt > 0) {
	do {
		if (substr($dbe->f('pg_employee_empid'),0,3) != 'S07') {
			$dbConn1 = $dbc;
		}
		else {
			$dbConn1 = $dbc1;
		}
		$sql4 = "SELECT name as employee_name
		FROM new_employee
		where empid = '".$dbe->f('pg_employee_empid')."'";

		$result_sql4 = $dbConn1->query($sql4);
		$dbConn1->next_record();
		$employeeNameArray[$no] = $dbConn1->f('employee_name');
		
		if (substr($dbe->f('assigned_by'),0,3) != 'S07') {
			$dbConn2 = $dbc;
		}
		else {
			$dbConn2 = $dbc1;
		}
		$sql5 = "SELECT name as assigned_name
		FROM new_employee
		where empid = '".$dbe->f('assigned_by')."'";

		$result_sql5 = $dbConn2->query($sql5);
		$dbConn2->next_record();
		
		$assignedNameArray[$no] = $dbConn2->f('assigned_name');
		$invitationDetailIdArray[$no] = $dbe->f('invitation_detail_id');
		$employeeIdArray[$no] = $dbe->f('pg_employee_empid');
		$acceptanceStatusArray[$no] = $dbe->f('acceptance_status');
		$acceptanceDateArray[$no] = $dbe->f('acceptance_date');
		$acceptanceStatusDescArray[$no] = $dbe->f('acceptance_status_desc');
		$examinerRoleArray[$no] = $dbe->f('examiner_role');
		$invitedStatusIdArray[$no] = $dbe->f('ref_invite_status_id');
		$invitedStatusDescArray[$no] = $dbe->f('invited_status_desc');
		$cancelledDateArray[$no] = $dbe->f('cancelled_date');
		$assignedDateArray[$no] = $dbe->f('assigned_date');
		$assignedByArray[$no] = $dbe->f('assigned_by');
		$no++;		
	} while ($dbe->next_record());
	$row_cnt = $no;
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
	<link rel="stylesheet" href="../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
	<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
	<script src="../../../lib/js/jquery.min2.js"></script>
    <script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
	<script language="JavaScript" src="../../../lib/js/windowopen.js"></script>
	<script src="../../../lib/js/jquery.colorbox.js"></script>
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

  <form id="form1" name="form1" method="post" enctype="multipart/form-data" onsubmit="return saveRec();">

	 <fieldset>
		<legend><strong>Evaluation Committee - Defence Proposal</strong></legend>
		<?
		if (substr($studentMatrixNo,0,2) != '07') {
			$dbConn = $dbc;
		}
		else {
			$dbConn = $dbc1;
		}
		$sql2 = "SELECT name
		FROM student
		where matrix_no = '$studentMatrixNo'";
		
		$result_sql2 = $dbConn->query($sql2);
		$dbConn->next_record();
		$studentName = $dbConn->f('name');
		?>
		<table>
			<tr>
				<td><label>Student Name</label></td>
				<td>:</td>
				<td><label><?=$studentName?></label></td>
			</tr>
			<tr>
				<td><label>Matrix No </label></td>
				<td>:</td>
				<td><label><?=$studentMatrixNo?></label></td>
			</tr>			
		</table>
		<br/>
		<table>
		<tr>							
			<td>Searching Results:- <?=$row_cnt?> record(s) found.</td></td>
		</tr>
		</table>
		<?if ($row_cnt <= 3) {
			?>
			<div id = "tabledisplay" style="overflow:auto; height:200px;">
			<?
		}
		else {
			?>
			<div id = "tabledisplay" style="overflow:auto; height:250px;">
			<?
		}?>		
		<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="90%" class="thetable">
			  <tr>
				<th width="5%"><label>Tick</label></td>
				<th width="5%"><label>No</label></td>
				<th align="left" width="15%"><label>Evaluation Panel</label></th>
				<th align="left" width="15%"><label>Role </label></th>
				<th align="left" width="10%"><label>Invited Status</label></th>
				<th align="left" width="15%"><label>Invited By</label></th>
				<th align="left" width="10%"><label>Invited Date</label></th>
				<th align="left" width="15%"><label>Acceptance Status</label></th>
			  </tr>

			<?php
			if ($row_cnt > 0) {
				$no1=1;
				for ($i=0;$i<$row_cnt;$i++) {				
					?><tr>
						<input type="hidden" name="invitationDetailId[]" id="invitationDetailId" value="<?=$invitationDetailIdArray[$i];?>" />
						<input type="hidden" name="invitedStatusId[]" id="invitedStatusId" value="<?=$invitedStatusIdArray[$i];?>" />
						<?if (($invitedStatusIdArray[$i] == "INV") && (($acceptanceStatusArray[$i] == "") || ($acceptanceStatusArray[$i]=="ACC"))) {?>
							<td align="center"><input type="checkbox" name="invite_checkbox[]" id="invite_checkbox" value="<?=$i;?>"/></td>
						<?}
						else {?>
							<td align="center"><input type="checkbox" name="invite_checkbox[]" id="invite_checkbox" value="<?=$i;?>" disabled="disabled"/></td>
						<?}?>
						<td align="center"><label><?=$no1;?>.</label></td>
						<td align="left"><label><?=$employeeNameArray[$i];?><br><?=$employeeIdArray[$i];?></label></td>	
						<td align="left"><label><?=$examinerRoleArray[$i];?></label></td>
						<td align="left"><label><?=$invitedStatusDescArray[$i];?></label></td>
						<td align="left"><label><?=$assignedNameArray[$i];?><br/><?=$assignedByArray[$i]?></label></td>
						<?if ($invitedStatusIdArray[$i] == "INV") {?>
							<td align="left"><label><?=$assignedDateArray[$i];?></label></td>
						<?}
						else {
							?>
							<td align="left"><label><?=$cancelledDateArray[$i];?></label></td>
							<?
						} ?>
						<?if ($acceptanceStatusArray[$i]=="") {
							?>
							<td align="left"><label>Pending</label></td>	
							<?
						}
						else {
							?>
							<td align="left"><label><?=$acceptanceStatusDescArray[$i];?> on <?=$acceptanceDateArray[$i];?></label></td>	
							<?
						}?>						
												
					</tr>
					<?
					$no1++;
				}
			}
			else {
				?>
				<table>
					<tr>
						<td><label>No record found!</label></td>
					</tr>
				</table>
				<?
			}?> 			
		</table>
		</div>
	</fieldset>
	<table>
		<tr>
			<td><input type="submit" name="btnCancel" value="Cancel Invite" /></input></td>
			<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../defense/defense_calendar_setup.php';" /></input></td>
		</tr>
	</table>

  </form>
  	<script>
		<?=$jscript;?>
	</script>
</body>
</html>





