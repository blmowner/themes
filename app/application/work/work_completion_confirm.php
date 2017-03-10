<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: work_completion_confirm.php
//
// Created by: Zuraimi
// Created Date: 24-August-2015
// Modified by: Zuraimi
// Modified Date: 24-August-2015
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
	
	$sql1 = "SELECT a.id, a.student_matrix_no, a.pg_thesis_id, a.pg_proposal_id, a.reference_no, a.status,
	c.description as status_desc, a.pg_work_id, a.ref_work_marks_id, b.description, a.respond_by
	FROM pg_work_evaluation a
	LEFT JOIN ref_work_marks b ON (b.id = a.ref_work_marks_id)
	LEFT JOIN ref_proposal_status c ON (c.id = a.status)
	WHERE a.status IN ('IN1','APP','DIS')"
	.$tmpSearchThesisId." "
	.$tmpSearchStudent." "."	
	AND a.respond_status = 'Y'
	AND a.archived_status IS NULL
	ORDER BY a.student_matrix_no, a.reference_no DESC";

	$result1 = $dba->query($sql1); 
	$dba->next_record();
	
	$workEvaluationIdArray = Array();
	$studentMatrixNoArray = Array();
	$thesisIdArray = Array();
	$proposalIdArray = Array();
	$referenceNoArray = Array();
	$workIdArray = Array();
	$workMarksIdArray = Array();
	$workMarksDescArray = Array();
	$evaluationStatusArray = Array();
	$statusDescArray = Array();
	$respondByArray = Array();
	
	$no1=0;
	$no2=0;
	
	if (mysql_num_rows($result1) > 0){
		do {
			$workEvaluationIdArray[$no1] = $dba->f('id');
			$studentMatrixNoArray[$no1] = $dba->f('student_matrix_no');
			$thesisIdArray[$no1] = $dba->f('pg_thesis_id');
			$proposalIdArray[$no1] = $dba->f('pg_proposal_id');
			$referenceNoArray[$no1] = $dba->f('reference_no');
			$workIdArray[$no1] = $dba->f('pg_work_id');
			$workMarksIdArray[$no1] = $dba->f('ref_work_marks_id');
			$workMarksDescArray[$no1] = $dba->f('description');
			$evaluationStatusArray[$no1] = $dba->f('status');
			$statusDescArray[$no1] = $dba->f('status_desc');
			$respondByArray[$no1] = $dba->f('respond_by');
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
				$workEvaluationIdArray[$no2] = $workEvaluationIdArray[$i];
				$studentMatrixNoArray[$no2] = $studentMatrixNoArray[$i];
				$thesisIdArray[$no2] = $thesisIdArray[$i];
				$proposalIdArray[$no2] = $proposalIdArray[$i];
				$referenceNoArray[$no2] = $referenceNoArray[$i];
				$workIdArray[$no2] = $workIdArray[$i];
				$workMarksIdArray[$no2] = $workMarksIdArray[$i];
				$workMarksDescArray[$no2] = $workMarksDescArray[$i];
				$evaluationStatusArray[$no2] = $evaluationStatusArray[$i];
				$statusDescArray[$no2] = $statusDescArray[$i];
				$respondByArray[$no2] = $respondByArray[$i];
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
	$sql1 = "SELECT a.id, a.student_matrix_no, a.pg_thesis_id, a.pg_proposal_id, a.reference_no, a.status,
	c.description as status_desc, a.pg_work_id, a.ref_work_marks_id, b.description, a.respond_by, d.pg_calendar_id,
	DATE_FORMAT(a.confirmed_date,'%d-%b-%Y %h:%i%p') AS confirmed_date
	FROM pg_work_evaluation a
	LEFT JOIN ref_work_marks b ON (b.id = a.ref_work_marks_id)
	LEFT JOIN ref_proposal_status c ON (c.id = a.status)
	LEFT JOIN pg_work d ON (d.id = a.pg_work_id)
	WHERE a.status IN ('IN1','APP','DIS')
	AND a.respond_status = 'Y'
	AND a.archived_status IS NULL
	AND b.status = 'A'
	ORDER BY a.student_matrix_no, a.reference_no DESC";

	$result1 = $dba->query($sql1); 
	$dba->next_record();
	
	$workEvaluationIdArray = Array();
	$studentMatrixNoArray = Array();
	$thesisIdArray = Array();
	$proposalIdArray = Array();
	$referenceNoArray = Array();
	$workIdArray = Array();
	$workMarksIdArray = Array();
	$workMarksDescArray = Array();
	$evaluationStatusArray = Array();
	$statusDescArray = Array();
	$respondByArray = Array();
	$calendarIdArray = Array();
	$confirmedDateArray = Array();
	
	$no1=0;
	$no2=0;
	if (mysql_num_rows($result1) > 0){
		do {
			$workEvaluationIdArray[$no1] = $dba->f('id');
			$studentMatrixNoArray[$no1] = $dba->f('student_matrix_no');
			$thesisIdArray[$no1] = $dba->f('pg_thesis_id');
			$proposalIdArray[$no1] = $dba->f('pg_proposal_id');
			$referenceNoArray[$no1] = $dba->f('reference_no');
			$workIdArray[$no1] = $dba->f('pg_work_id');
			$workMarksIdArray[$no1] = $dba->f('ref_work_marks_id');
			$workMarksDescArray[$no1] = $dba->f('description');
			$evaluationStatusArray[$no1] = $dba->f('status');
			$statusDescArray[$no1] = $dba->f('status_desc');
			$respondByArray[$no1] = $dba->f('respond_by');
			$calendarIdArray[$no1] = $dba->f('pg_calendar_id');
			$confirmedDateArray[$no1] = $dba->f('confirmed_date');
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
				$workEvaluationIdArray[$no2] = $workEvaluationIdArray[$i];
				$studentMatrixNoArray[$no2] = $studentMatrixNoArray[$i];
				$thesisIdArray[$no2] = $thesisIdArray[$i];
				$proposalIdArray[$no2] = $proposalIdArray[$i];
				$referenceNoArray[$no2] = $referenceNoArray[$i];
				$workIdArray[$no2] = $workIdArray[$i];
				$workMarksIdArray[$no2] = $workMarksIdArray[$i];
				$workMarksDescArray[$no2] = $workMarksDescArray[$i];
				$evaluationStatusArray[$no2] = $evaluationStatusArray[$i];
				$statusDescArray[$no2] = $statusDescArray[$i];
				$respondByArray[$no2] = $respondByArray[$i];
				$calendarIdArray[$no2] = $calendarIdArray[$i];
				$confirmedDateArray[$no2] = $confirmedDateArray[$i];
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
			<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="85%" class="thetable">			
				<tr>
					<th width="5%">No</th>					
					<th width="20%"  align="left">Student Name</th>
					<th width="10%">Thesis / Project ID</th>
					<th width="20%" align="left">Evaluation Report</th>						
					<th width="10%"  align="left">Status by Evaluation Committee</th>
					<th width="15%"  align="left">Status by School Board</th>
					<th width="5%">Action</th>
				</tr>
				<?if ($row_cnt > 0 ) {?>	
				<?
				$no=0;
				for ($i=0; $i<$no2; $i++){?>
					<tr>
						<td align="center"><?=$no+1;?>.</td>
						<td><label><?=$studentNameArray[$i]?><br><?=$studentMatrixNoArray[$i]?></label></td>
						<td align="center"><label><?=$thesisIdArray[$i]?></label></td>
						<?
						$sql3 = "SELECT DATE_FORMAT(d.responded_date,'%d-%b-%Y %h:%i%p') as responded_date, 
						DATE_FORMAT(a.defense_date,'%d-%b-%Y') as defense_date,
						DATE_FORMAT(a.defense_stime,'%h:%i%p') as defense_stime,
						DATE_FORMAT(a.defense_etime,'%h:%i%p') as defense_etime, a.venue, b.reference_no as reference_no_defence,
						e.description as session_type_desc
						FROM pg_calendar a
						LEFT JOIN pg_work b ON (b.pg_calendar_id = a.id)
						LEFT JOIN pg_work_evaluation c ON (c.pg_work_id = b.id) 
						LEFT JOIN pg_work_evaluation_detail d ON (d.pg_eval_id = c.id)
						LEFT JOIN ref_session_type e ON (e.id = a.ref_session_type_id)
						WHERE a.id = '$calendarIdArray[$i]'
						AND a.student_matrix_no = '$studentMatrixNoArray[$i]'
						AND c.pg_work_id = '$workIdArray[$i]'
						AND d.pg_employee_empid = '$respondByArray[$i]'
						AND a.thesis_id = '$thesisIdArray[$i]'
						AND a.status = 'A'
						AND d.status = 'IN2'
						/*AND b.archived_status IS NULL*/
						AND c.archived_status IS NULL
						AND d.archived_status IS NULL";
						
						$result_sql3 = $dba->query($sql3); 
						$dba->next_record();
						
						$recommendedId = $dba->f('id');
						$defenseDate = $dba->f('defense_date');
						$defenseSTime = $dba->f('defense_stime');
						$defenseETime = $dba->f('defense_etime');
						$respondedDate = $dba->f('responded_date');	
						$referenceNoDefence = $dba->f('reference_no_defence');	
						$sessionTypeDesc = $dba->f('session_type_desc');	
						$venue = $dba->f('venue');		
						?>
						<td><label><?=$sessionTypeDesc?> - <?=$defenseDate?>, <?=$defenseSTime?>-<?=$defenseETime?><br/></br>Ref. No: <strong><?=$referenceNoArray[$i]?></strong>
						<br/>Last Update:<?=$respondedDate?></br> </label></td>
						<td><label><?=$workMarksDescArray[$i]?></label></td>
						<td><label><?=$statusDescArray[$i]?><br><?=$confirmedDateArray[$i]?></label></td>
						<td align="center">			
						<?if ($evaluationStatusArray[$i] == 'IN1') {?>										
							<a href="../work/confirm_work_evaluation_detail.php?id=<?=$workEvaluationIdArray[$i]?>&mn=<?=$studentMatrixNoArray[$i]?>&tid=<?=$thesisIdArray[$i]?>&pid=<?=$proposalIdArray[$i]?>&ref=<?=$referenceNoArray[$i]?>&refd=<?=$referenceNoDefence?>&eid=<?=$respondByArray[$i]?>&cid=<?=$calendarIdArray[$i]?>&did=<?=$workIdArray[$i]?>">Update</a></td>
							<?
						}
						else {
							?>
								<a href="../work/confirm_work_evaluation_detail_view.php?id=<?=$workEvaluationIdArray[$i]?>&mn=<?=$studentMatrixNoArray[$i]?>&tid=<?=$thesisIdArray[$i]?>&pid=<?=$proposalIdArray[$i]?>&ref=<?=$referenceNoArray[$i]?>&refd=<?=$referenceNoDefence?>&eid=<?=$respondByArray[$i]?>&cid=<?=$calendarIdArray[$i]?>&did=<?=$workIdArray[$i]?>">View</a></td>
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
				1. You have yet to be assigned as Evaluation Committee.<br/>
				2. If already assigned, please <a href="../administration/tab_accept_invitation.php?>#tabs-2">accept the invitation.</a></td>
			</tr>
		</table>
		<?
	}?>	
		
</body>
</html>




