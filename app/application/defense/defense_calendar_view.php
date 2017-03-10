<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: defense_calendar_view.php
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
$thesisId=$_GET['tid'];
$studentMatrixNo=$_GET['mn'];
$defenseCalendarId = $_GET['cid'];
$sessionType = $_GET['st'];

$employeeIdArray = Array();
$employeeNameArray = Array();
$examinerRoleArray = Array();
$assignedDateArray = Array();

$sql = "SELECT a.id, a.pg_employee_empid, 
a.ref_supervisor_type_id, e.description AS examiner_role, 
DATE_FORMAT(a.assigned_date,'%d-%b-%Y %h:%i %p') AS assigned_date
FROM pg_supervisor a
LEFT JOIN ref_supervisor_type e ON (e.id = a.ref_supervisor_type_id)
WHERE a.pg_student_matrix_no = '$studentMatrixNo'
AND a.ref_supervisor_type_id in ('EE','EI', 'EC', 'XE')
AND a.pg_thesis_id = '$thesisId'
AND a.status = 'A' 
ORDER BY e.seq, a.pg_employee_empid";
				
$result_sql = $dbe->query($sql); 
$dbe->next_record();
$row_cnt = mysql_num_rows($result_sql);

$no=0;
if ($row_cnt > 0) {
	do {
		if (substr($dbe->f('pg_employee_empid'),0,3) != 'S07') {
			$dbConn = $dbc;
		}
		else {
			$dbConn = $dbc1;
		}
		$sql4 = "SELECT name
		FROM new_employee
		where empid = '".$dbe->f('pg_employee_empid')."'";

		$result_sql4 = $dbConn->query($sql4);
		$dbConn->next_record();
		
		$employeeNameArray[$no] = $dbConn->f('name');
		$employeeIdArray[$no] = $dbe->f('pg_employee_empid');
		$examinerRoleArray[$no] = $dbe->f('examiner_role');
		$assignedDateArray[$no] = $dbe->f('assigned_date');
		$no++;		
	} while ($dbe->next_record());
	$row_cnt = $no;
}

$supervisorIdArray = Array();
$supervisorNameArray = Array();
$supervisorRoleArray = Array();
$supervisorAssignedDateArray = Array();

$sql1 = "SELECT a.id, a.pg_employee_empid, 
a.ref_supervisor_type_id, e.description AS supervisor_role, 
DATE_FORMAT(a.assigned_date,'%d-%b-%Y %h:%i %p') AS supervisor_assigned_date
FROM pg_supervisor a
LEFT JOIN ref_supervisor_type e ON (e.id = a.ref_supervisor_type_id)
WHERE a.pg_student_matrix_no = '$studentMatrixNo'
AND a.ref_supervisor_type_id in ('SV','CS', 'XS')
AND a.pg_thesis_id = '$thesisId'
AND a.status = 'A' 
ORDER BY e.seq, a.pg_employee_empid";
				
$result_sql1 = $db->query($sql1); 
$db->next_record();
$row_cnt1 = mysql_num_rows($result_sql1);

$no1=0;
if ($row_cnt1 > 0) {
	do {
		if (substr($db->f('pg_employee_empid'),0,3) != 'S07') {
			$dbConn = $dbc;
		}
		else {
			$dbConn = $dbc1;
		}
		$sql4 = "SELECT name
		FROM new_employee
		where empid = '".$db->f('pg_employee_empid')."'";

		$result_sql4 = $dbConn->query($sql4);
		$dbConn->next_record();
		
		$supervisorNameArray[$no1] = $dbConn->f('name');
		$supervisorIdArray[$no1] = $db->f('pg_employee_empid');
		$supervisorRoleArray[$no1] = $db->f('supervisor_role');
		$supervisorAssignedDateArray[$no1] = $db->f('supervisor_assigned_date');
		$no1++;		
	} while ($db->next_record());
	$row_cnt1 = $no1;
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

</head>
<body>

  <form id="form1" name="form1" method="post" enctype="multipart/form-data" onsubmit="return saveRec();">

	 <fieldset>
		<legend><strong>List of Evaluation Committee</strong></legend>
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
				<td><strong>List of Evaluation Panel - </strong>Searching Results:- <?=$row_cnt?> record(s) found.</td></td>
			</tr>
		</table>
		<?if ($row_cnt <= 3) {
			?>
			<div id = "tabledisplay" style="overflow:auto; height:100px;">
			<?
		}
		else {
			?>
			<div id = "tabledisplay" style="overflow:auto; height:150px;">
			<?
		}?>		
		<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="70%" class="thetable">
			  <tr>
				<th width="5%"><label>No</label></td>
				<th align="left" width="25%"><label>Evaluation Committee</label></th>
				<th align="left" width="10%"><label>Staff ID</label></th>
				<th align="left" width="15%"><label>Role </label></th>
				<th align="left" width="15%"><label>Assigned Date </label></th>
			  </tr>

			<?php
			if ($row_cnt > 0) {
				$no1=1;
				for ($i=0;$i<$row_cnt;$i++) {				
					?><tr>
						<td align="center"><label><?=$no1;?>.</label></td>
						<td align="left"><label><?=$employeeNameArray[$i]?></label></td>	
						<td align="left"><label><?=$employeeIdArray[$i]?></label></td>	
						<td align="left"><label><?=$examinerRoleArray[$i]?></label></td>
						<td align="left"><label><?=$assignedDateArray[$i]?></label></td>					
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
		<br>
		<?if ($sessionType == "VIV") {?>
			<table>
				<tr>							
					<td><strong>List of Supervisor/Co-Supervisor - </strong>Searching Results:- <?=$row_cnt?> record(s) found.</td></td>
				</tr>
			</table>
			<?if ($row_cnt1 <= 3) {
				?>
				<div id = "tabledisplay" style="overflow:auto; height:100px;">
				<?
			}
			else {
				?>
				<div id = "tabledisplay" style="overflow:auto; height:150px;">
				<?
			}?>		
			<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="70%" class="thetable">
				  <tr>
					<th width="5%"><label>No</label></td>
					<th align="left" width="25%"><label>Supervisor/Co-Supervisor Name</label></th>
					<th align="left" width="10%"><label>Staff ID</label></th>
					<th align="left" width="15%"><label>Role </label></th>
					<th align="left" width="15%"><label>Assigned Date </label></th>
				  </tr>

				<?php
				if ($row_cnt1 > 0) {
					$no1=1;
					for ($i=0;$i<$row_cnt1;$i++) {				
						?><tr>
							<td align="center"><label><?=$no1?>.</label></td>
							<td align="left"><label><?=$supervisorNameArray[$i]?></label></td>	
							<td align="left"><label><?=$supervisorIdArray[$i]?></label></td>	
							<td align="left"><label><?=$supervisorRoleArray[$i]?></label></td>
							<td align="left"><label><?=$supervisorAssignedDateArray[$i]?></label></td>					
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
		<?}?>
	</fieldset>
	<table>
		<tr>
			<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../defense/defense_calendar_setup.php';" /></input></td>
		</tr>
	</table>

  </form>
  	<script>
		<?=$jscript;?>
	</script>
</body>
</html>





