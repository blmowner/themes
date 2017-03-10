<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: work_completion_evaluate.php
//
// Created by: Zuraimi
// Created Date: 23-August-2015
// Modified by: Zuraimi
// Modified Date: 23-August-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

session_start();
$user_id=$_SESSION['user_id'];

if(isset($_POST['btnSearch']) && ($_POST['btnSearch'] <> "")) {
	
	$searchThesisId = $_POST['searchThesisId'];
	$searchStudent = $_POST['searchStudent'];
	$searchStudentName = $_POST['searchStudentName'];
	$msg = Array();
	if ($searchThesisId!="") 
	{
		$tmpSearchThesisId = " AND a.pg_thesis_id = '$searchThesisId'";
	}
	else 
	{
		$tmpSearchThesisId="";
	}
	if ($searchStudent!="") 
	{
		$tmpSearchStudent = " AND a.student_matrix_no = '$searchStudent'";
	}
	else 
	{
		$tmpSearchStudent="";
	}
	if ($searchStudentName!="") 
	{
		$tmpSearchStudentName = " AND name LIKE '%$searchStudentName%'";
	}
	else 
	{
		$tmpSearchStudentName="";
	}
	
	$sql1 = "SELECT a.pg_work_id, a.student_matrix_no, a.pg_thesis_id, a.pg_proposal_id, a.reference_no,
	c.pg_calendar_id
	FROM pg_work_evaluation a
	LEFT JOIN pg_work_evaluation_detail b ON (b.pg_eval_id = a.id)
	LEFT JOIN pg_work c ON (c.id = a.pg_work_id)
	WHERE b.pg_employee_empid = '$user_id'"
	.$tmpSearchThesisId." "
	.$tmpSearchStudent." "."
	AND a.status IN ('IN1','APP', 'DIS')
	AND a.archived_status IS NULL
	AND b.archived_status IS NULL
	ORDER BY b.insert_date DESC";

	$result1 = $dba->query($sql1); 
	$dba->next_record();
	
	$studentMatrixNoArray = Array();
	$thesisIdArray = Array();
	$proposalIdArray = Array();
	$referenceNoArray = Array();
	$calendarIdArray = Array();
	$defenseIdArray = Array();
	
	$no1=0;
	$no2=0;
	
	if (mysql_num_rows($result1) > 0){
		do {
			$studentMatrixNoArray[$no1] = $dba->f('student_matrix_no');
			$thesisIdArray[$no1] = $dba->f('pg_thesis_id');
			$proposalIdArray[$no1] = $dba->f('pg_proposal_id');
			$referenceNoArray[$no1] = $dba->f('reference_no');
			$calendarIdArray[$no1] = $dba->f('pg_calendar_id');
			$defenseIdArray[$no1] = $dba->f('pg_work_id');
			$no1++;
		} while ($dba->next_record());
		
		$studentNameArray = Array();
		for ($i=0; $i<$no1; $i++){
			if (substr($studentMatrixNoArray[$i],0,2) != '07') { 
				$dbConnStudent= $dbc; 
			} 
			else { 
				$dbConnStudent=$dbc1; 
			}
			$sql9 = "SELECT name
			FROM student
			WHERE matrix_no = '$studentMatrixNoArray[$i]'"
			.$tmpSearchStudentName." ";
			$result9 = $dbConnStudent->query($sql9); 
			
			$dbConnStudent->next_record();
			if (mysql_num_rows($result9)>0) {
				$studentNameArray[$no2] = $dbConnStudent->f('name');
				$studentMatrixNoArray[$no2] = $studentMatrixNoArray[$i];
				$thesisIdArray[$no2] = $thesisIdArray[$i];
				$proposalIdArray[$no2] = $proposalIdArray[$i];
				$referenceNoArray[$no2] = $referenceNoArray[$i];
				$calendarIdArray[$no2] = $calendarIdArray[$i];
				$defenseIdArray[$no2] = $defenseIdArray[$i];
				$no2++;
			}
		}
		if ($no2 == 0) {			
			$msg[] = "<div class=\"error\"><span>No record(s) found.</span></div>";			
		}		
	}
	else {
		$msg[] = "<div class=\"error\"><span>No record(s) found.</span></div>";
	}	
	$row_cnt = $no2;			
}
else 
{
	$sql1 = "SELECT a.pg_work_id, a.student_matrix_no, a.pg_thesis_id, a.pg_proposal_id, a.reference_no,
	c.pg_calendar_id
	FROM pg_work_evaluation a
	LEFT JOIN pg_work_evaluation_detail b ON (b.pg_eval_id = a.id)
	LEFT JOIN pg_work c ON (c.id = a.pg_work_id)
	WHERE b.pg_employee_empid = '$user_id'
	AND a.status IN ('IN1', 'APP', 'DIS')
	AND a.archived_status IS NULL
	AND b.archived_status IS NULL
	ORDER BY b.insert_date DESC";

	$result1 = $dba->query($sql1); 
	$dba->next_record();
	
	$studentMatrixNoArray = Array();
	$thesisIdArray = Array();
	$proposalIdArray = Array();
	$referenceNoArray = Array();
	$calendarIdArray = Array();
	$defenseIdArray = Array();
	
	$no1=0;
	$no2=0;
	if (mysql_num_rows($result1) > 0){
		do {
			$studentMatrixNoArray[$no1] = $dba->f('student_matrix_no');
			$thesisIdArray[$no1] = $dba->f('pg_thesis_id');
			$proposalIdArray[$no1] = $dba->f('pg_proposal_id');
			$referenceNoArray[$no1] = $dba->f('reference_no');
			$calendarIdArray[$no1] = $dba->f('pg_calendar_id');
			$defenseIdArray[$no1] = $dba->f('pg_work_id');
			$no1++;
		} while ($dba->next_record());
		$studentNameArray = Array();
		for ($i=0; $i<$no1; $i++){
			$sql9 = "SELECT name
					FROM student
					WHERE matrix_no = '$studentMatrixNoArray[$i]'";
			if (substr($studentMatrixNoArray[$i],0,2) != '07') { 
				$dbConnStudent= $dbc; 
			} 
			else { 
				$dbConnStudent=$dbc1; 
			}
			$result9 = $dbConnStudent->query($sql9); 
			$dbConnStudent->next_record();
			if (mysql_num_rows($result9)>0) {
				$studentNameArray[$no2] = $dbConnStudent->f('name');
				$studentMatrixNoArray[$no2] = $studentMatrixNoArray[$i];
				$thesisIdArray[$no2] = $thesisIdArray[$i];
				$proposalIdArray[$no2] = $proposalIdArray[$i];
				$referenceNoArray[$no2] = $referenceNoArray[$i];
				$calendarIdArray[$no2] = $calendarIdArray[$i];
				$defenseIdArray[$no2] = $defenseIdArray[$i];
				$no2++;
			}
		}		
	}
	$row_cnt = $no2;
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
	
	<SCRIPT LANGUAGE="JavaScript">

	function respConfirm () {
		var confirmSubmit = confirm("Click OK if confirm to submit or CANCEL to return back.");
		if (confirmSubmit==true)
		{
			return saveStatus;
		}
		if (confirmSubmit==false)
		{
			return false;
		}
	}

	</SCRIPT>
		<fieldset>
		<legend><strong>List of Student</strong></legend>
			<table>
				<tr>							
					<td>Please enter searching criteria below:-</td>
				</tr>
			</table>
			<table>
				<tr>
					<td>Thesis / Project ID</td>
					<td>:</td>
					<td><input type="text" name="searchThesisId" size="15" id="searchThesisId" value="<?=$searchThesisId;?>"/></td>
				</tr>
				<tr>
					<td>Matrix No</td>
					<td>:</td>
					<td><input type="text" name="searchStudent" size="15" id="searchStudent" value="<?=$searchStudent;?>"/></td>					
				</tr>
				<tr>
					<td>Student Name</td>
					<td>:</td>
					<td><input type="text" name="searchStudentName" size="30" id="searchStudentName" value="<?=$searchStudentName;?>"/></td>
					<td><input type="submit" name="btnSearch" value="Search" /><span style="color:#FF0000"> Note:</span> If no entry is provided, it will search all.</td>
				</tr>
			</table>
			</br>
			
			<table>
				<tr>							
					<td>Searching Results:- <?=$row_cnt ?> record(s) found.</td>
				</tr>
			</table>
			<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="100%" class="thetable">			
				<tr>
					<th width="5%">No</th>					
					<th width="10%" align="left">Staff Role</th>
					<th width="20%"  align="left">Student Name</th>
					<th width="10%">Thesis / Project ID</th>
					<th width="20%" align="left">Evaluation Report</th>						
					<th width="15%"  align="left">Evaluation Status</th>					
					<th width="15%"  align="left">Confirmation by School Board</th>
					<th width="5%">Action</th>
				</tr>
				<?if ($row_cnt > 0 ) {?>	
				<?
				$no=0;
				for ($i=0; $i<$no2; $i++){?>
					<tr>
						<td align="center"><?=$no+1;?>.</td>
						<td>
						<?
						$sql_supervisor = "SELECT b.pg_employee_empid, c.ref_supervisor_type_id, d.description AS supervisor_desc, 
						DATE_FORMAT(b.acceptance_date,'%d-%b-%Y %h:%i %p') AS acceptance_date, b.acceptance_status,
						e.description as acceptance_status_desc
						FROM pg_invitation a
						LEFT JOIN pg_invitation_detail b ON (b.pg_invitation_id = a.id)
						LEFT JOIN pg_supervisor c ON (c.id = b.pg_supervisor_id)
						LEFT JOIN ref_supervisor_type d ON (d.id = c.ref_supervisor_type_id) 
						LEFT JOIN ref_acceptance_status e ON (e.id = b.acceptance_status)
						WHERE c.pg_student_matrix_no = '$studentMatrixNoArray[$i]'
						AND c.pg_thesis_id = '$thesisIdArray[$i]'
						AND c.ref_supervisor_type_id in ('EI','EE','EC','XE')
						AND b.pg_employee_empid = '$user_id'
						AND c.status = 'A'
						AND d.status = 'A'
						ORDER BY d.seq";
				
						$result_sql_supervisor = $dbg->query($sql_supervisor); //echo $sql;
						$dbg->next_record();
						$supervisorTypeId=$dbg->f('ref_supervisor_type_id');
						$supervisorDesc=$dbg->f('supervisor_desc');						
						
						if ($supervisorTypeId == 'XE') {
						?>
							<label><span style="color:#FF0000"><?=$supervisorDesc?></span></label>
						<?}
						else {
							?>
							<label><?=$supervisorDesc?></label>
							<?
						}?>
						</td>
						<td><label><?=$studentNameArray[$i]?><br><?=$studentMatrixNoArray[$i]?></label></td>
						<td align="center"><label><?=$thesisIdArray[$i]?></label></td>
						
						<?
						$sql2 = "SELECT DISTINCT e.id,  
						DATE_FORMAT(f.responded_date, '%d-%b-%Y %h:%i%p') AS responded_date, 
						DATE_FORMAT(e.confirmed_date, '%d-%b-%Y %h:%i%p') AS confirmed_date,
						DATE_FORMAT(d.defense_date, '%d-%b-%Y') AS defense_date,
						DATE_FORMAT(d.defense_stime,'%h:%i%p') as defense_stime,
						DATE_FORMAT(d.defense_etime,'%h:%i%p') as defense_etime, 
						h.description as session_type_desc,
						e.reference_no, e.status AS evaluation_status, f.status AS evaluation_detail_status, 
						c1.description AS evaluation_desc,
						c2.description AS evaluation_detail_desc, a.reference_no as reference_no_defence,
						g.description as defense_marks_desc
						FROM pg_work a
						LEFT JOIN pg_work_detail b ON (b.pg_work_id = a.id)
						LEFT JOIN pg_calendar d ON (d.id = a.pg_calendar_id)
						LEFT JOIN pg_work_evaluation e ON (e.pg_work_id = a.id)
						LEFT JOIN pg_work_evaluation_detail f ON (f.pg_eval_id = e.id)
						LEFT JOIN ref_proposal_status c1 ON (c1.id = e.status)
						LEFT JOIN ref_proposal_status c2 ON (c2.id = f.status)
						LEFT JOIN ref_work_marks g ON (g.id = f.ref_work_marks_id)
						LEFT JOIN ref_session_type h ON (h.id = d.ref_session_type_id)
						WHERE a.student_matrix_no = '$studentMatrixNoArray[$i]'
						AND a.pg_thesis_id = '$thesisIdArray[$i]'
						AND a.pg_proposal_id = '$proposalIdArray[$i]'
						AND e.reference_no = '$referenceNoArray[$i]'
						AND f.pg_employee_empid = '$user_id'
						AND b.status = 'REC'
						/*AND a.archived_status is null*/
						AND b.archived_status is null
						AND e.archived_status is null
						AND f.archived_status is null";
						
						$result2 = $dbg->query($sql2); 
						$dbg->next_record();
						
						$id=$dbg->f('id');
						$evaluationDesc=$dbg->f('evaluation_desc');
						$evaluationDetailDesc=$dbg->f('evaluation_detail_desc');
						$respondedDate=$dbg->f('responded_date');
						$defenseDate=$dbg->f('defense_date');
						$defenseSTime=$dbg->f('defense_stime');
						$defenseETime=$dbg->f('defense_etime');
						$sessionTypeDesc=$dbg->f('session_type_desc');
						$referenceNoDefence=$dbg->f('reference_no_defence');
						$evaluationStatus=$dbg->f('evaluation_status');
						$evaluationDetailStatus=$dbg->f('evaluation_detail_status');
						$defenseMarksDesc=$dbg->f('defense_marks_desc');
						$confirmedDate=$dbg->f('confirmed_date');
						$row_cnt2 = mysql_num_rows($result2);						
						?>						
						<td><label><?=$sessionTypeDesc?> - <?=$defenseDate?>, <?=$defenseSTime?> - <?=$defenseETime?></strong><br/></br>Ref. No: <?=$referenceNoArray[$i]?></label></td>
						<td><label><?=$defenseMarksDesc?><br><?=$evaluationDetailDesc?><br><?=$respondedDate?></label></td>
						<td><label><?=$evaluationDesc?><br><?=$confirmedDate?></label></td>
						<td align="center">			
						<? if ($evaluationStatus == 'IN1') {
							if ($evaluationDetailStatus =='IN1' || $evaluationDetailStatus =='SV1') {
								if ($supervisorTypeId != 'XE') {?>										
									<a href="../work/review_work_evaluation_detail.php?id=<?=$id?>&mn=<?=$studentMatrixNoArray[$i]?>&tid=<?=$thesisIdArray[$i]?>&pid=<?=$proposalIdArray[$i]?>&ref=<?=$referenceNoArray[$i]?>&refd=<?=$referenceNoDefence?>&rol=<?=$supervisorTypeId?>&cid=<?=$calendarIdArray[$i]?>&did=<?=$defenseIdArray[$i]?>">Update</a></td>
								<? }
								else {?>
									<label>Note: Please check Staff Role status.</label><br/><br/>
									<a href="../work/review_work_evaluation_detail.php?id=<?=$id?>&mn=<?=$studentMatrixNoArray[$i]?>&tid=<?=$thesisIdArray[$i]?>&pid=<?=$proposalIdArray[$i]?>&ref=<?=$referenceNoArray[$i]?>&refd=<?=$referenceNoDefence?>&rol=<?=$supervisorTypeId?>&cid=<?=$calendarIdArray[$i]?>&did=<?=$defenseIdArray[$i]?>">Update</a></td>
									<?
								}
							}
							else {?>
								<a href="../work/review_work_evaluation_detail_view.php?id=<?=$id?>&mn=<?=$studentMatrixNoArray[$i]?>&tid=<?=$thesisIdArray[$i]?>&pid=<?=$proposalIdArray[$i]?>&ref=<?=$referenceNoArray[$i]?>&refd=<?=$referenceNoDefence?>&rol=<?=$supervisorTypeId?>&cid=<?=$calendarIdArray[$i]?>&did=<?=$defenseIdArray[$i]?>">View</a></td>
							<?}
						}
						else {
							?>
								<a href="../work/review_work_evaluation_detail_view.php?id=<?=$id?>&mn=<?=$studentMatrixNoArray[$i]?>&tid=<?=$thesisIdArray[$i]?>&pid=<?=$proposalIdArray[$i]?>&ref=<?=$referenceNoArray[$i]?>&refd=<?=$referenceNoDefence?>&rol=<?=$supervisorTypeId?>&cid=<?=$calendarIdArray[$i]?>&did=<?=$defenseIdArray[$i]?>">View</a></td>
							<?
						}?>
						</td>
					</tr>
					<?
				$no++;	
				}
				?>
		</table>
		</fieldset>
		<table>				
			<tr><td><br/><span style="color:#FF0000">Notes:</span><br/>
				1. If you don't find the student and the session, you have yet to be assigned as Evaluation Committee. If already assigned, please <a href="../administration/tab_accept_invitation.php?>#tabs-2">accept the invitation.</a></td>
			</tr>
		</table>
	<?}
	else {
	?>
		<table>
			<tr>
				<td>No record found!</td>
			</tr>
		</table>	
		<br/>
		<table>				
			<tr><td><br/><span style="color:#FF0000">Notes:</span><br/>
				Possible Reasons:-<br/>
				1. You have yet to be assigned as Evaluation Committee. If already assigned, please <a href="../administration/tab_accept_invitation.php?>#tabs-2">accept the invitation.</a><br/>
				2. If you have accepted the invitation, the Work Completion is pending submission by the Student.<br/>
				3. If you have rejected the invitation, the Work Completion would not be displayed in here.</td>
			</tr>
		</table>
		<?
	}?>	
		
</body>
</html>




