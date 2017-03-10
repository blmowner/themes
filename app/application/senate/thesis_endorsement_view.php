<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: thesis_endorsement_detail.php
//
// Created by: Zuraimi
// Created Date: 16-October-2015
// Modified by: Zuraimi
// Modified Date: 16-October-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();
/*ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
session_start();*/
$user_id = $_SESSION['user_id'];
$senateId = $_GET['id'];
$studentMatrixNo = $_GET['mn'];
$thesisId = $_GET['tid'];
$proposalId = $_GET['pid'];
$referenceNo = $_GET['ref'];
$calendarId = $_GET['cid'];

$sql1 = "SELECT DATE_FORMAT(submit_date,'%d-%b-%Y %h:%i%p') AS submit_date,
remarks as senate_remarks
FROM pg_senate 
WHERE id = '$senateId'";

$dba->query($sql1); 
$dba->next_record();
$submitDate = $dba->f('submit_date');
$senateRemarks = $dba->f('senate_remarks');
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
		<script src="../../lib/js/jquery.mask_input-1.3.js"></script>
		<script src="../../../lib/js/jquery.min2.js"></script>
   		<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
    	<script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
		<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>	
		<script language="JavaScript" src="../../../lib/js/windowopen.js"></script>	
		<script language="JavaScript" type="text/javascript" src="../../../lib/js/tooltip.js"></script>
		
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
	<form id="form1" name="form1" method="post" enctype="multipart/form-data">
	<input type="hidden" name = "supervisorEmpId" id = "supervisorEmpId" value="<?=$supervisorEmpId?>" />

		<table border="0">
			<tr>
				<td><h2><strong>Report Details</strong><h2></td>
			</tr>
		</table>
		<table>
			<tr>
				<td>Endorsement Date</td>
				<td>:</td>
				<td><?=$submitDate?></td>
			</tr>
			<tr>
				<td>Reference No.</td>
				<td>:</td>
				<td><?=$referenceNo?></td>
			</tr>
			<tr>
				<td>Student Matrix No</td>
				<td>:</td>
				<td><?=$studentMatrixNo?></td>
			</tr>
				<?
				$sql1 = "SELECT name AS student_name
				FROM student
				WHERE matrix_no = '$studentMatrixNo'";
				if (substr($studentMatrixNo,0,2) != '07') { 
					$dbConnStudent= $dbc; 
				} 
				else { 
					$dbConnStudent=$dbc1; 
				}
				$result1 = $dbConnStudent->query($sql1); 
				$dbConnStudent->next_record();
				$sname=$dbConnStudent->f('student_name');
				?>
			<tr>
				<td>Student Name</td>
				<td>:</td>
				<td><?=$sname?></td>
			</tr>    
			<input type="hidden" name="sname" id="sname" value="<?=$sname; ?>">
			<tr>
				<td>Thesis / Project ID</td>
				<td>:</td>
				<td><label><?=$thesisId;?></label></td>
			</tr>
			<tr>
			<td>Evaluation Schedule</td>
			<td>:</td>	
			<?				
				$sql7 = "SELECT const_value
				FROM base_constant
				WHERE const_category = 'DEFENSE_PROPOSAL'
				AND const_term = 'DEFENSE_DURATION'";
				
				$result_sql7 = $db->query($sql7); 
				$db->next_record();
				$defenseDurationParam = $db->f('const_value');

				$sql3 = "SELECT a.id, a.defense_date, DATE_FORMAT(a.defense_date,'%d-%b-%Y') as defense_date1, 
				DATE_FORMAT(a.defense_stime,'%h:%i%p') as defense_stime,
				DATE_FORMAT(a.defense_etime,'%h:%i%p') as defense_etime, a.venue, a.recomm_status, a.status,
				c.description as session_type_desc
				FROM pg_calendar a
				LEFT JOIN pg_defense b On (b.pg_calendar_id = a.id)
				LEFT JOIN ref_session_type c ON (c.id = a.ref_session_type_id)
				WHERE a.id = '$calendarId'
				AND a.student_matrix_no = '$studentMatrixNo'
				AND a.thesis_id = '$thesisId'
				AND a.status = 'A'
				ORDER BY a.defense_date ASC";
				
				$result_sql3 = $dba->query($sql3); 
				$dba->next_record();
				
				$recommendedId = $dba->f('id');
				$defenseDate = $dba->f('defense_date1');
				$defenseSTime = $dba->f('defense_stime');
				$defenseETime = $dba->f('defense_etime');	
				$venue = $dba->f('venue');		
				$calendarStatus = $dba->f('status');
				$recommStatus = $dba->f('recomm_status');
				$sessionTypeDesc = $dba->f('session_type_desc');
				?>				
			<td><label><?=$sessionTypeDesc?> - <?=$defenseDate?>, <?=$defenseSTime?> to <?=$defenseETime?>, <?=$venue?></label></td>				
		</tr>   				
		</table>
		<h3><legend><strong>Supervisor / Co-Supervisor</strong></h3>
		<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="55%" class="thetable">			
			<tr>
				<th width="5%">No</th>					
				<th width="15%" align="left">Role</th>
				<th width="10%" align="left">Staff ID</th>
				<th width="20%" align="left">Name</th>
				<th width="5%" align="left">Faculty</th>
			</tr>
			 <?php	
			$sql_sv="SELECT  a.pg_employee_empid, d.description as supervisor_desc,
			DATE_FORMAT(a.acceptance_date,'%d-%b-%Y %h:%i %p') AS acceptance_date,
			DATE_FORMAT(a.assigned_date,'%d-%b-%Y %h:%i %p') AS assigned_date,
			e.description as acceptance_status_desc
			FROM pg_supervisor a 
			LEFT JOIN ref_supervisor_type d ON (d.id = a.ref_supervisor_type_id) 
			LEFT JOIN pg_thesis f ON (f.student_matrix_no = a.pg_student_matrix_no)
			LEFT JOIN pg_proposal g ON (g.pg_thesis_id = f.id)
			LEFT JOIN ref_acceptance_status e ON (e.id = a.acceptance_status)
			WHERE a.pg_student_matrix_no='$studentMatrixNo' 
			AND a.pg_thesis_id = '$thesisId'
			AND a.ref_supervisor_type_id in ('SV','CS')
			AND a.acceptance_status is not null
			AND g.verified_status in ('APP','AWC')
			AND g.status in ('APP','APC')
			AND g.archived_status IS NULL
			AND a.status='A'
			ORDER BY d.seq, a.ref_supervisor_type_id";

			$result_sv = $db_klas2->query($sql_sv); 
			
			$row_cnt_sv = mysql_num_rows($result_sv);
			$db_klas2->next_record();
			$varRecCount=0;	
			if ($row_cnt_sv>0) {

				do {
					$employeeId = $db_klas2->f('pg_employee_empid');
					$partnerSupervisorTypeId = $db_klas2->f('ref_supervisor_type_id');
					$supervisorDesc = $db_klas2->f('supervisor_desc');
					$acceptanceDate = $db_klas2->f('acceptance_date');
					$assignedDate = $db_klas2->f('assigned_date');
					$acceptanceStatusDesc = $db_klas2->f('acceptance_status_desc');
					
					$sql_employee="SELECT  b.name, c.id, c.description
						FROM new_employee b 
						LEFT JOIN dept_unit c ON (c.id = b.unit_id) 
						WHERE b.empid= '$employeeId'";
						
					$result_sql_employee = $dbc->query($sql_employee);
					$dbc->next_record();
					
					$employeeName = $dbc->f('name');
					$departmentId = $dbc->f('id');
					$departmentName = $dbc->f('description');
					$varRecCount++;

					?>
					<input type="hidden" name="evaluationPanelArray[]" id="evaluationPanelArray" value="<?=$employeeId; ?>">
					<tr>
						<td align="center"><?=$varRecCount;?>.</td>					
						<td>
						<?
						if ($partnerSupervisorTypeId == 'XE') {
						?>
							<label><span style="color:#FF0000"><?=$supervisorDesc?></span></label>
						<?}
						else {
							?>
							<label><?=$supervisorDesc?></label>
							<?
						}?></td>
						<td align="left"><?=$employeeId;?></td>
						<td align="left"><?=$employeeName;?></td>
						<td align="left"><a href="javascript:void(0);" onMouseOver="toolTip('<?=$departmentName;?>', 300)" onMouseOut="toolTip()"><?=$departmentId;?></a></td>
						
					<?
					} while($db_klas2->next_record());
				}
				else {
					?>
					<table>				
						<tr><td>No record found!</tr>
					</table>
					<br/>
					<table>				
						<tr><td><br/><span style="color:#FF0000">Note:</span><br/>
									Possible Reasons:-<br/>
									1. Evaluation Committee is yet to be assigned.<br/>
									2. If already assigned, it could be the Committee Member is pending to accept.</td>
						</tr>
					</table>
					<?
				}?>
				
		</table>
		<?
		//Declarations
		$trackingItemArray = Array();
		$trackingStatusArray = Array();
		$trackingStatusDescArray = Array();
		$trackingDateArray = Array();
	
		//Tracking Status for Thesis Proposal
		$sql_thesis_proposal="SELECT pa.thesis_title, pa.status, 
		ppa.endorsed_status as endorsed_status, DATE_FORMAT(ppa.endorsed_date,'%d-%b-%Y %h:%i%p') AS endorsed_date,
		rps1.description
		FROM pg_thesis pt
		LEFT JOIN pg_proposal pa ON (pa.pg_thesis_id = pt.id)
		LEFT JOIN pg_proposal_approval ppa ON (ppa.id = pa.pg_proposal_approval_id)
		LEFT JOIN ref_thesis_status rps1 ON (rps1.id = ppa.endorsed_status) 
		WHERE pt.student_matrix_no = '$studentMatrixNo'
		AND pt.status = 'INP'
		AND pt.archived_status is null
		AND pa.archived_status is null";
		$db->query($sql_thesis_proposal);
		$db->next_record();
		
		$trackingItemIdArray[] = "TP";
		$trackingItemArray[] = "Thesis Proposal";
		$trackingStatusArray[] = $db->f('endorsed_status');
		$trackingStatusDesc1Array[] = $db->f('description');
		$trackingStatusDesc2Array[] = "";
		$trackingStatusDesc3Array[] = "";
		$trackingDateArray[] = $db->f('endorsed_date');
		$trackingCalendarIdArray[] = "";
		
		//Tracking Status for Defence Proposal
		$sql_defence_proposal = "SELECT a.id as defense_id, b.id as evaluation_id, a.status as defense_status, 
		b.status as evaluation_status, h.description as evaluation_status_desc,
		b.respond_status, b.confirmed_status, a.pg_calendar_id as def_calendar_id,
		b.ref_defense_marks_id, g.description as ref_defense_marks_desc,
		b.proposed_marks_id, i.description as proposed_marks_desc,
		DATE_FORMAT(b.confirmed_date,'%d-%b-%Y %h:%i%p') AS confirmed_date
		FROM pg_defense a
		LEFT JOIN pg_evaluation b ON (b.pg_defense_id = a.id)
		LEFT JOIN ref_proposal_status h ON (h.id = b.status)
		LEFT JOIN ref_defense_marks g ON (g.id = b.ref_defense_marks_id)
		LEFT JOIN ref_defense_marks i ON (i.id = b.proposed_marks_id)
		WHERE a.student_matrix_no = '$studentMatrixNo'
		AND a.pg_thesis_id = '$thesisId'
		AND a.pg_proposal_id = '$proposalId'
		AND a.status = 'REC'
		AND a.submit_status = 'INP'
		AND a.respond_status = 'Y'
		AND b.respond_status = 'Y'
		AND b.confirmed_status = 'Y'
		AND a.archived_status IS NULL
		AND b.archived_status IS NULL
		ORDER BY a.submit_date DESC";

		$dbg->query($sql_defence_proposal); 
		$dbg->next_record();
		
		$trackingItemIdArray[] = "PD";
		$trackingItemArray[] = "Proposal Defence";
		$trackingStatusArray[] = $dbg->f('ref_defense_marks_id');
		$trackingStatusDesc1Array[] = $dbg->f('ref_defense_marks_desc');
		$trackingStatusDesc2Array[] = $dbg->f('proposed_marks_id');
		$trackingStatusDesc3Array[] = $dbg->f('evaluation_status_desc');
		$trackingStatusArray[] = $dbg->f('evaluation_status');
		$trackingDateArray[] = $dbg->f('confirmed_date');
		$trackingCalendarIdArray[] = $dbg->f('def_calendar_id');
		
		//Tracking Status for Work Completion
		$sql_work_completion = "SELECT a.id as defense_id, b.id as evaluation_id, a.status as work_status, 
		b.status as evaluation_status, h.description as evaluation_status_desc, a.pg_calendar_id as wc_calendar_id,
		b.respond_status, b.confirmed_status,
		b.ref_work_marks_id, g.description as ref_work_marks_desc,
		b.proposed_marks_id, i.description as proposed_marks_desc,
		DATE_FORMAT(b.confirmed_date,'%d-%b-%Y %h:%i%p') AS confirmed_date
		FROM pg_work a
		LEFT JOIN pg_work_evaluation b ON (b.pg_work_id = a.id)
		LEFT JOIN ref_proposal_status h ON (h.id = b.status)
		LEFT JOIN ref_work_marks g ON (g.id = b.ref_work_marks_id)
		LEFT JOIN ref_work_marks i ON (i.id = b.proposed_marks_id)
		WHERE a.student_matrix_no = '$studentMatrixNo'
		AND a.pg_thesis_id = '$thesisId'
		AND a.pg_proposal_id = '$proposalId'
		AND a.status = 'REC'
		AND a.submit_status = 'INP'
		AND a.respond_status = 'Y'
		AND b.respond_status = 'Y'
		AND b.confirmed_status = 'Y'
		AND a.archived_status IS NULL
		AND b.archived_status IS NULL
		ORDER BY a.submit_date DESC";

		$dbg->query($sql_work_completion); 
		$dbg->next_record();
		
		$trackingItemIdArray[] = "WC";
		$trackingItemArray[] = "Work Completion";
		$trackingStatusArray[] = $dbg->f('ref_work_marks_id');
		$trackingStatusDesc1Array[] = $dbg->f('ref_work_marks_desc');
		$trackingStatusDesc2Array[] = $dbg->f('proposed_marks_id');
		$trackingStatusDesc3Array[] = $dbg->f('evaluation_status_desc');
		$trackingDateArray[] = $dbg->f('confirmed_date');
		$trackingCalendarIdArray[] = $dbg->f('wc_calendar_id');
		
		//Tracking Status for VIVA
		$sql_viva = "SELECT a.id, a.student_matrix_no, a.pg_thesis_id, a.pg_proposal_id, a.reference_no, 
		a.submit_status, d.description as submit_status_desc,
		a.pg_work_id, a.pg_calendar_id as vv_calendar_id,
		c.final_result, e.description AS final_result_desc,
		DATE_FORMAT(c.final_result_date,'%d-%b-%Y %h:%i%p') AS final_result_date
		FROM pg_senate a
		LEFT JOIN pg_viva b ON (b.id = a.pg_viva_id)
		LEFT JOIN pg_evaluation_viva c ON (c.pg_viva_id = b.id)
		LEFT JOIN ref_proposal_status d ON (d.id = a.submit_status)
		LEFT JOIN ref_recommendation e ON (e.id = c.final_result)
		WHERE a.status = 'A'
		AND a.student_matrix_no = '$studentMatrixNo'
		AND a.pg_thesis_id = '$thesisId'
		AND a.pg_proposal_id = '$proposalId'
		/*AND a.respond_status = 'N'
		AND c.respond_status = 'SUB'*/
		AND c.status = 'A'
		AND a.archived_status IS NULL";

		$dbg->query($sql_viva); 
		$dbg->next_record();
		
		$trackingItemIdArray[] = "VV";
		$trackingItemArray[] = "VIVA /  Thesis Evaluation";
		$trackingStatusArray[] = $dbg->f('final_result');
		$trackingStatusDesc1Array[] = $dbg->f('final_result_desc');
		$trackingStatusDesc2Array[] = "";
		$trackingStatusDesc3Array[] = "";
		$trackingDateArray[] = $dbg->f('final_result_date');
		$trackingCalendarIdArray[] = $dbg->f('vv_calendar_id');
		?>		
		<h3><strong>Thesis Progress Flow</strong></h3>
		<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="90%" class="thetable">			
			<tr>
				<th width="5%">No</th>					
				<th width="20%" align="left">Item</th>
				<th width="20%" align="left">Status</th>
				<th width="15%" align="left">Date</th>
				<th width="30%" align="left"></th>
			</tr>			
			<?
			if (count($trackingItemArray) > 0) {
				for ($i=0;$i<count($trackingItemArray);$i++) {?>
				<tr>
					<td align="center"><label><?=$i+1?>.</label></td>
					<td><label><?=$trackingItemArray[$i]?></label></td>
					<td><label><?=$trackingStatusDesc1Array[$i]?><br>
					<?=$trackingStatusDesc2Array[$i]?><br><?=$trackingStatusDesc3Array[$i]?></label></td>
					<td><label><?=$trackingDateArray[$i]?></label></td>
					<td><label>
					<?
					if ($trackingItemIdArray[$i] == "TP") {
						$sql_thesis_proposal="SELECT pa.endorsed_by
						FROM pg_thesis pt
						LEFT JOIN pg_proposal pa ON (pa.pg_thesis_id = pt.id)
						LEFT JOIN pg_proposal_approval ppa ON (ppa.id = pa.pg_proposal_approval_id)
						LEFT JOIN ref_thesis_status rps1 ON (rps1.id = ppa.endorsed_status) 
						WHERE pt.student_matrix_no = '$studentMatrixNo'
						AND pt.status = 'INP'
						AND pt.archived_status is null
						AND pa.archived_status is null";
						$db->query($sql_thesis_proposal);
						$db->next_record();
						
						$endorsedBy = $db->f('endorsed_by');
						
						$sql_employee="SELECT  b.name, c.id, c.description
						FROM new_employee b 
						LEFT JOIN dept_unit c ON (c.id = b.unit_id) 
						WHERE b.empid= '$endorsedBy'";
							
						$result_sql_employee = $dbc->query($sql_employee);
						$dbc->next_record();
						
						$employeeName = $dbc->f('name');
						$departmentId = $dbc->f('id');
						$departmentName = $dbc->f('description');
						?>
							<label><strong>Approved By</strong></label><br>
							<?=$employeeName;?> (<?=$employeeId;?>)<br><a href="javascript:void(0);" onMouseOver="toolTip('<?=$departmentName;?>', 300)" onMouseOut="toolTip()"><?=$departmentId;?></a><br>
						<?
						
					}
					else if ($trackingItemIdArray[$i] == "PD") {						
						$sql_examiner = "SELECT b.pg_employee_empid, c.ref_supervisor_type_id, d.description AS supervisor_desc, 
						DATE_FORMAT(b.acceptance_date,'%d-%b-%Y %h:%i %p') AS acceptance_date, b.acceptance_status,
						DATE_FORMAT(b.assigned_date,'%d-%b-%Y %h:%i %p') AS assigned_date,
						e.description as acceptance_status_desc
						FROM pg_invitation a
						LEFT JOIN pg_invitation_detail b ON (b.pg_invitation_id = a.id)
						LEFT JOIN pg_supervisor c ON (c.id = b.pg_supervisor_id)
						LEFT JOIN ref_supervisor_type d ON (d.id = c.ref_supervisor_type_id) 
						LEFT JOIN ref_acceptance_status e ON (e.id = b.acceptance_status)
						WHERE c.pg_student_matrix_no = '$studentMatrixNo'
						AND c.pg_thesis_id = '$thesisId'
						AND c.ref_supervisor_type_id in ('EI','EE','EC','XE')
						AND a.pg_calendar_id = '$trackingCalendarIdArray[$i]'
						AND c.status = 'A'
						AND d.status = 'A'
						ORDER BY d.seq";

						$result_sql_examiner = $db_klas2->query($sql_examiner); 
						$row_cnt_examiner = mysql_num_rows($result_sql_examiner);
						$db_klas2->next_record();
						$varRecCount=0;	
						?><label><strong>Evaluation Committee Member</strong></label><br><?
						if ($row_cnt_examiner > 0) {
							
							do {
								$employeeId = $db_klas2->f('pg_employee_empid');
								$supervisorTypeId = $db_klas2->f('ref_supervisor_type_id');
								$supervisorDesc = $db_klas2->f('supervisor_desc');
								$acceptanceDate = $db_klas2->f('acceptance_date');
								$assignedDate = $db_klas2->f('assigned_date');
								$acceptanceStatusDesc = $db_klas2->f('acceptance_status_desc');
								
								$sql_employee="SELECT  b.name, c.id, c.description
								FROM new_employee b 
								LEFT JOIN dept_unit c ON (c.id = b.unit_id) 
								WHERE b.empid= '$employeeId'";
									
								$result_sql_employee = $dbc->query($sql_employee);
								$dbc->next_record();
								
								$employeeName = $dbc->f('name');
								$departmentId = $dbc->f('id');
								$departmentName = $dbc->f('description');
								$varRecCount++;
								?>
									<?=$varRecCount?>. <?=$employeeName;?> (<?=$employeeId;?>)<br><?=$supervisorDesc?>, <a href="javascript:void(0);" onMouseOver="toolTip('<?=$departmentName;?>', 300)" onMouseOut="toolTip()"><?=$departmentId;?></a><br>
								<?
								} while($db_klas2->next_record());

							}
							else {
								?>
								<label>No record found!</label>
								<?
							}?>
						<?
					}
					else if ($trackingItemIdArray[$i] == "WC") {
						$sql_examiner = "SELECT b.pg_employee_empid, c.ref_supervisor_type_id, d.description AS supervisor_desc, 
						DATE_FORMAT(b.acceptance_date,'%d-%b-%Y %h:%i %p') AS acceptance_date, b.acceptance_status,
						DATE_FORMAT(b.assigned_date,'%d-%b-%Y %h:%i %p') AS assigned_date,
						e.description as acceptance_status_desc
						FROM pg_invitation a
						LEFT JOIN pg_invitation_detail b ON (b.pg_invitation_id = a.id)
						LEFT JOIN pg_supervisor c ON (c.id = b.pg_supervisor_id)
						LEFT JOIN ref_supervisor_type d ON (d.id = c.ref_supervisor_type_id) 
						LEFT JOIN ref_acceptance_status e ON (e.id = b.acceptance_status)
						WHERE c.pg_student_matrix_no = '$studentMatrixNo'
						AND c.pg_thesis_id = '$thesisId'
						AND c.ref_supervisor_type_id in ('EI','EE','EC','XE')
						AND a.pg_calendar_id = '$trackingCalendarIdArray[$i]'
						AND c.status = 'A'
						AND d.status = 'A'
						ORDER BY d.seq";

						$result_sql_examiner = $db_klas2->query($sql_examiner); 
						
						$row_cnt_examiner = mysql_num_rows($result_sql_examiner);
						$db_klas2->next_record();
						$varRecCount=0;	
						?><label><strong>Evaluation Committee Member</strong></label><br><?
						if ($row_cnt_examiner > 0) {
							
							do {
								$employeeId = $db_klas2->f('pg_employee_empid');
								$supervisorTypeId = $db_klas2->f('ref_supervisor_type_id');
								$supervisorDesc = $db_klas2->f('supervisor_desc');
								$acceptanceDate = $db_klas2->f('acceptance_date');
								$assignedDate = $db_klas2->f('assigned_date');
								$acceptanceStatusDesc = $db_klas2->f('acceptance_status_desc');
								
								$sql_employee="SELECT  b.name, c.id, c.description
								FROM new_employee b 
								LEFT JOIN dept_unit c ON (c.id = b.unit_id) 
								WHERE b.empid= '$employeeId'";
									
								$result_sql_employee = $dbc->query($sql_employee);
								$dbc->next_record();
								
								$employeeName = $dbc->f('name');
								$departmentId = $dbc->f('id');
								$departmentName = $dbc->f('description');
								$varRecCount++;
								?>
									<?=$varRecCount?>. <?=$employeeName;?> (<?=$employeeId;?>)<br><?=$supervisorDesc?>, <a href="javascript:void(0);" onMouseOver="toolTip('<?=$departmentName;?>', 300)" onMouseOut="toolTip()"><?=$departmentId;?></a><br>
								<?
								} while($db_klas2->next_record());

							}
							else {
								?>
								<label>No record found!</label>
								<?
							}?>
						<?
					}
					else if ($trackingItemIdArray[$i] == "VV") {
						$sql_examiner = "SELECT b.pg_employee_empid, c.ref_supervisor_type_id, d.description AS supervisor_desc, 
						DATE_FORMAT(b.acceptance_date,'%d-%b-%Y %h:%i %p') AS acceptance_date, b.acceptance_status,
						DATE_FORMAT(b.assigned_date,'%d-%b-%Y %h:%i %p') AS assigned_date,
						e.description as acceptance_status_desc
						FROM pg_invitation a
						LEFT JOIN pg_invitation_detail b ON (b.pg_invitation_id = a.id)
						LEFT JOIN pg_supervisor c ON (c.id = b.pg_supervisor_id)
						LEFT JOIN ref_supervisor_type d ON (d.id = c.ref_supervisor_type_id) 
						LEFT JOIN ref_acceptance_status e ON (e.id = b.acceptance_status)
						WHERE c.pg_student_matrix_no = '$studentMatrixNo'
						AND c.pg_thesis_id = '$thesisId'
						AND c.ref_supervisor_type_id in ('EI','EE','EC','XE')
						AND a.pg_calendar_id = '$trackingCalendarIdArray[$i]'
						AND c.status = 'A'
						AND d.status = 'A'
						ORDER BY d.seq";

						$result_sql_examiner = $db_klas2->query($sql_examiner); 
						
						$row_cnt_examiner = mysql_num_rows($result_sql_examiner);
						$db_klas2->next_record();
						$varRecCount=0;	
						?><label><strong>Evaluation Committee Member</strong></label><br><?
						if ($row_cnt_examiner > 0) {
							
							do {
								$employeeId = $db_klas2->f('pg_employee_empid');
								$supervisorTypeId = $db_klas2->f('ref_supervisor_type_id');
								$supervisorDesc = $db_klas2->f('supervisor_desc');
								$acceptanceDate = $db_klas2->f('acceptance_date');
								$assignedDate = $db_klas2->f('assigned_date');
								$acceptanceStatusDesc = $db_klas2->f('acceptance_status_desc');
								
								$sql_employee="SELECT  b.name, c.id, c.description
								FROM new_employee b 
								LEFT JOIN dept_unit c ON (c.id = b.unit_id) 
								WHERE b.empid= '$employeeId'";
									
								$result_sql_employee = $dbc->query($sql_employee);
								$dbc->next_record();
								
								$employeeName = $dbc->f('name');
								$departmentId = $dbc->f('id');
								$departmentName = $dbc->f('description');
								$varRecCount++;
								?>
									<?=$varRecCount?>. <?=$employeeName;?> (<?=$employeeId;?>)<br><?=$supervisorDesc?>, <a href="javascript:void(0);" onMouseOver="toolTip('<?=$departmentName;?>', 300)" onMouseOut="toolTip()"><?=$departmentId;?></a><br>
								<?
								} while($db_klas2->next_record());

							}
							else {
								?>
								<label>No record found!</label>
								<?
							}?>
						<?
					}
					?>
					</label></td>
				</tr>
				<?}
			}?>
		</table>	
	<table>
		<tr>
			<td>
				<h3><label><strong>Senate Remark</strong></label></h3>
			</td>
		</tr>
		<tr>
			<td><label><?=$senateRemarks?></label></td>
		</tr>
	</table>
	<table>
		<tr>
			<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../senate/thesis_endorsement.php';" /></td>
		</tr>
	</table>		
	<br>
	</form>
</body>
</html>




