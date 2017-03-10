<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: work_completion_history.php
//
// Created by: Zuraimi
// Created Date: 09-October-2015
// Modified by: Zuraimi
// Modified Date: 09-October-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

$user_id=$_SESSION['user_id'];

if(isset($_POST['btnSearch']) && ($_POST['btnSearch'] <> "")) {
	
	$searchReferenceNo = $_POST['searchReferenceNo'];
	$searchStudentName = $_POST['searchStudentName'];
	$searchMatrixNo = $_POST['searchMatrixNo'];
	
	if ($searchReferenceNo=="") {
		$tmpSearchReferenceNo = "";
	}
	else {
		$tmpSearchReferenceNo = "AND a.reference_no = '$searchReferenceNo'";
	}
	if ($searchMatrixNo=="") {
		$tmpSearchMatrixNo = "";
	}
	else {
		$tmpSearchMatrixNo = "AND a.student_matrix_no = '$searchMatrixNo'";
	}
	if ($searchStudentName!="") 
	{
		$tmpSearchStudentName = " AND name LIKE '%$searchStudentName%'";
	}
	else 
	{
		$tmpSearchStudentName="";
	}
	$sql1 = "SELECT a.pg_thesis_id, a.pg_proposal_id, a.pg_calendar_id, a.status as work_status, a.reference_no, 
	a.student_matrix_no,
	b.id as work_evaluation_id, b.ref_work_marks_id, b.proposed_marks_id, 
	b.status as work_evaluation_status, d.description as work_evaluation_desc,
	b.respond_status, b.confirmed_status,
	c1.description as ref_work_marks_desc, c2.description as ref_proposed_marks_desc,
	DATE_FORMAT(e.defense_date,'%d-%b-%Y') as defense_date, 
	DATE_FORMAT(e.defense_stime,'%h:%i%p') as defense_stime,
	DATE_FORMAT(e.defense_etime,'%h:%i%p') as defense_etime, e.venue,
	f.id as work_amendment_id, f.status as work_amendment_status, g.description as work_amendment_desc,
	DATE_FORMAT(f.amendment_date,'%d-%b-%Y %h:%i%p') as amendment_date,
	f.feedback_status, h.description as feedback_status_desc, g.remarks,
	DATE_FORMAT(f.feedback_date,'%d-%b-%Y %h:%i%p') as feedback_date
	FROM pg_work a
	LEFT JOIN pg_work_evaluation b ON (b.pg_work_id = a.id)
	LEFT JOIN ref_work_marks c1 ON (c1.id = b.ref_work_marks_id)
	LEFT JOIN ref_work_marks c2 ON (c2.id = b.proposed_marks_id)
	LEFT JOIN ref_proposal_status d ON (d.id = b.status)
	LEFT JOIN pg_calendar e ON (e.id = a.pg_calendar_id)
	LEFT JOIN pg_work_amendment f ON (f.pg_work_evaluation_id = b.id)
	LEFT JOIN ref_amendment_status g ON (g.id = f.status)
	LEFT JOIN ref_amendment_status h ON (h.id = f.feedback_status)
	WHERE a.status = 'REC'"
	.$tmpSearchReferenceNo." "
	.$tmpSearchMatrixNo." "."	
	AND f.reviewer_id = '$user_id'
	AND a.submit_status = 'INP'
	AND a.respond_status = 'Y'
	AND ((b.status = 'APP' AND b.ref_work_marks_id = 'NSA')
	OR (b.status = 'DIS' AND b.ref_work_marks_id = 'SUB' AND b.proposed_marks_id = 'NSA')
	OR (b.status = 'DIS' AND b.ref_work_marks_id = 'SAT' AND b.proposed_marks_id = 'NSA'))
	AND a.archived_status IS NULL
	AND b.archived_status IS NULL
	AND e.status = 'A'
	/*AND f.archived_status IS NULL*/
	ORDER BY f.feedback_date, f.amendment_date";

	$result_sql1 = $dbg->query($sql1); 
	$dbg->next_record();
	//$row_cnt1 = mysql_num_rows($result_sql1);

	$workEvaluationIdArray = Array();
	$thesisIdArray = Array();
	$proposalIdArray = Array();
	$calendarIdArray = Array();
	$workStatusArray = Array();
	$referenceNoArray = Array();
	$workMarksIdArray = Array();
	$workMarksDescArray = Array();
	$proposedMarksIdArray = Array(); 
	$proposedMarksDescArray = Array();
	$workEvaluationStatusArray = Array();
	$workEvaluationDescArray = Array();
	$respondStatusArray = Array();
	$confirmedStatusArray = Array();
	$defenseDateArray = Array(); 
	$defenseSTimeArray = Array();
	$defenseETimeArray = Array();
	$venueArray = Array();
	$workAmendmentIdArray = Array();
	$workAmendmentStatusArray = Array();
	$workAmendmentDateArray = Array();
	$workAmendmentDescArray = Array();
	$feedbackStatusArray = Array();
	$feedbackStatusDescArray = Array();
	$feedbackDateArray = Array(); 
	$studentMatrixNoArray = Array();
	$studentNameArray = Array();
	$remarksArray = Array();
							

	$i=0;
	if (mysql_num_rows($result_sql1)>0){
		do {
			$workEvaluationIdArray[$i] = $dbg->f('work_evaluation_id');
			$thesisIdArray[$i] = $dbg->f('pg_thesis_id');
			$proposalIdArray[$i] = $dbg->f('pg_proposal_id');
			$calendarIdArray[$i] = $dbg->f('pg_calendar_id');
			$workStatusArray[$i] = $dbg->f('work_status');
			$referenceNoArray[$i] = $dbg->f('reference_no');
			$workMarksIdArray[$i] = $dbg->f('ref_work_marks_id');
			$workMarksDescArray[$i] = $dbg->f('ref_work_marks_desc');
			$proposedMarksIdArray[$i] = $dbg->f('proposed_marks_id');
			$proposedMarksDescArray[$i] = $dbg->f('ref_proposed_marks_desc');
			$workEvaluationStatusArray[$i] = $dbg->f('work_evaluation_status');
			$workEvaluationDescArray[$i] = $dbg->f('work_evaluation_desc');
			$respondStatusArray[$i] = $dbg->f('respond_status');
			$confirmedStatusArray[$i] = $dbg->f('confirmed_status');
			$defenseDateArray[$i] = $dbg->f('defense_date');
			$defenseSTimeArray[$i] = $dbg->f('defense_stime');
			$defenseETimeArray[$i] = $dbg->f('defense_etime');
			$venueArray[$i] = $dbg->f('venue');	
			$workAmendmentIdArray[$i] = $dbg->f('work_amendment_id');	
			$workAmendmentStatusArray[$i] = $dbg->f('work_amendment_status');
			$workAmendmentDateArray[$i] = $dbg->f('amendment_date');
			$workAmendmentDescArray[$i] = $dbg->f('work_amendment_desc');
			$feedbackStatusArray[$i] = $dbg->f('feedback_status');
			$feedbackStatusDescArray[$i] = $dbg->f('feedback_status_desc');
			$feedbackDateArray[$i] = $dbg->f('feedback_date');
			$studentMatrixNoArray[$i] = $dbg->f('student_matrix_no');
			$remarksArray[$i] = $dbg->f('remarks');
			$i++;
		} while ($dbg->next_record());
	}
	$k = 0;
	for ($j=0; $j<$i; $j++){
		if (substr($studentMatrixNoArray[$j],0,2) != '07') { 
			$dbConnStudent= $dbc; 
		} 
		else { 
			$dbConnStudent=$dbc1; 
		}
		$sql9 = "SELECT name
		FROM student
		WHERE matrix_no = '$studentMatrixNoArray[$j]'"
		.$tmpSearchStudentName." ";
		$result9 = $dbConnStudent->query($sql9); 			
		$dbConnStudent->next_record();
		
		
		if (mysql_num_rows($result9)>0) {
			$studentNameArray[$k] = $dbConnStudent->f('name');
			$workEvaluationIdArray[$k] = $workEvaluationIdArray[$j];
			$thesisIdArray[$k] = $thesisIdArray[$j];
			$proposalIdArray[$k] = $proposalIdArray[$j];
			$calendarIdArray[$k] = $calendarIdArray[$j];
			$workStatusArray[$k] = $workStatusArray[$j];
			$referenceNoArray[$k] = $referenceNoArray[$j];
			$workMarksIdArray[$k] = $workMarksIdArray[$j];
			$workMarksDescArray[$k] = $workMarksDescArray[$j];
			$proposedMarksIdArray[$k] = $proposedMarksIdArray[$j];
			$proposedMarksDescArray[$k] = $proposedMarksDescArray[$j];
			$workEvaluationStatusArray[$k] = $workEvaluationStatusArray[$j];
			$workEvaluationDescArray[$k] = $workEvaluationDescArray[$j];
			$respondStatusArray[$k] = $respondStatusArray[$j];
			$confirmedStatusArray[$k] = $confirmedStatusArray[$j];
			$defenseDateArray[$k] = $defenseDateArray[$j];
			$defenseSTimeArray[$k] = $defenseSTimeArray[$j];
			$defenseETimeArray[$k] = $defenseETimeArray[$j];
			$venueArray[$k] = $venueArray[$j];
			$workAmendmentIdArray[$k] = $workAmendmentIdArray[$j];
			$workAmendmentStatusArray[$k] = $workAmendmentStatusArray[$j];
			$workAmendmentDateArray[$k] = $workAmendmentDateArray[$j];
			$workAmendmentDescArray[$k] = $workAmendmentDescArray[$j];
			$feedbackStatusArray[$k] = $feedbackStatusArray[$j];
			$feedbackStatusDescArray[$k] = $feedbackStatusDescArray[$j];
			$feedbackDateArray[$k] = $feedbackDateArray[$j];
			$studentMatrixNoArray[$k] = $studentMatrixNoArray[$j];
			$remarksArray[$k] = $remarksArray[$j];
			$k++;
		}
	}	
	$row_cnt1 = $k;
}
else {
	$sql1 = "SELECT a.pg_thesis_id, a.pg_proposal_id, a.pg_calendar_id, a.status as work_status, a.reference_no, 
	a.student_matrix_no,
	b.id as work_evaluation_id, b.ref_work_marks_id, b.proposed_marks_id, 
	b.status as work_evaluation_status, d.description as work_evaluation_desc,
	b.respond_status, b.confirmed_status,
	c1.description as ref_work_marks_desc, c2.description as ref_proposed_marks_desc,
	DATE_FORMAT(e.defense_date,'%d-%b-%Y') as defense_date, 
	DATE_FORMAT(e.defense_stime,'%h:%i%p') as defense_stime,
	DATE_FORMAT(e.defense_etime,'%h:%i%p') as defense_etime, e.venue,
	f.id as work_amendment_id, f.status as work_amendment_status, g.description as work_amendment_desc,
	DATE_FORMAT(f.amendment_date,'%d-%b-%Y %h:%i%p') as amendment_date,
	f.feedback_status, h.description as feedback_status_desc, g.remarks as remarks,
	DATE_FORMAT(f.feedback_date,'%d-%b-%Y %h:%i%p') as feedback_date, 
	f.archived_status, DATE_FORMAT(f.archived_date,'%d-%b-%Y %h:%i%p') as archived_date 
	FROM pg_work a
	LEFT JOIN pg_work_evaluation b ON (b.pg_work_id = a.id)
	LEFT JOIN ref_work_marks c1 ON (c1.id = b.ref_work_marks_id)
	LEFT JOIN ref_work_marks c2 ON (c2.id = b.proposed_marks_id)
	LEFT JOIN ref_proposal_status d ON (d.id = b.status)
	LEFT JOIN pg_calendar e ON (e.id = a.pg_calendar_id)
	LEFT JOIN pg_work_amendment f ON (f.pg_work_evaluation_id = b.id)
	LEFT JOIN ref_amendment_status g ON (g.id = f.status)
	LEFT JOIN ref_amendment_status h ON (h.id = f.feedback_status)
	WHERE a.status = 'REC'
	AND f.reviewer_id = '$user_id'
	/*AND a.submit_status = 'INP'
	AND a.respond_status = 'Y'*/
	AND ((b.status = 'APP' AND b.ref_work_marks_id = 'NSA')
	OR (b.status = 'DIS' AND b.ref_work_marks_id = 'SUB' AND b.proposed_marks_id = 'NSA')
	OR (b.status = 'DIS' AND b.ref_work_marks_id = 'SAT' AND b.proposed_marks_id = 'NSA'))
	AND a.archived_status IS NULL
	AND b.archived_status IS NULL
	AND e.status = 'A'
	/*AND f.archived_status IS NULL*/
	ORDER BY f.feedback_date, f.amendment_date";

	$result_sql1 = $dbg->query($sql1); 
	$dbg->next_record();
	$row_cnt1 = mysql_num_rows($result_sql1);

	$workEvaluationIdArray = Array();
	$thesisIdArray = Array();
	$proposalIdArray = Array();
	$calendarIdArray = Array();
	$workStatusArray = Array();
	$referenceNoArray = Array();
	$workMarksIdArray = Array();
	$workMarksDescArray = Array();
	$proposedMarksIdArray = Array(); 
	$proposedMarksDescArray = Array();
	$workEvaluationStatusArray = Array();
	$workEvaluationDescArray = Array();
	$respondStatusArray = Array();
	$confirmedStatusArray = Array();
	$defenseDateArray = Array(); 
	$defenseSTimeArray = Array();
	$defenseETimeArray = Array();
	$venueArray = Array();
	$workAmendmentIdArray = Array();
	$workAmendmentStatusArray = Array();
	$workAmendmentDescArray = Array();
	$feedbackStatusArray = Array();
	$workAmendmentDateArray = Array();
	$feedbackStatusDescArray = Array();
	$feedbackDateArray = Array(); 
	$studentMatrixNoArray = Array();
	$remarksArray = Array();
	$archivedStatusArray = Array();
	$archivedDateArray = Array();						

	$i=0;
	do {
		$workEvaluationIdArray[$i] = $dbg->f('work_evaluation_id');
		$thesisIdArray[$i] = $dbg->f('pg_thesis_id');
		$proposalIdArray[$i] = $dbg->f('pg_proposal_id');
		$calendarIdArray[$i] = $dbg->f('pg_calendar_id');
		$workStatusArray[$i] = $dbg->f('work_status');
		$referenceNoArray[$i] = $dbg->f('reference_no');
		$workMarksIdArray[$i] = $dbg->f('ref_work_marks_id');
		$workMarksDescArray[$i] = $dbg->f('ref_work_marks_desc');
		$proposedMarksIdArray[$i] = $dbg->f('proposed_marks_id');
		$proposedMarksDescArray[$i] = $dbg->f('ref_proposed_marks_desc');
		$workEvaluationStatusArray[$i] = $dbg->f('work_evaluation_status');
		$workEvaluationDescArray[$i] = $dbg->f('work_evaluation_desc');
		$respondStatusArray[$i] = $dbg->f('respond_status');
		$confirmedStatusArray[$i] = $dbg->f('confirmed_status');
		$defenseDateArray[$i] = $dbg->f('defense_date');
		$defenseSTimeArray[$i] = $dbg->f('defense_stime');
		$defenseETimeArray[$i] = $dbg->f('defense_etime');
		$venueArray[$i] = $dbg->f('venue');	
		$workAmendmentIdArray[$i] = $dbg->f('work_amendment_id');	
		$workAmendmentStatusArray[$i] = $dbg->f('work_amendment_status');
		$workAmendmentDateArray[$i] = $dbg->f('amendment_date');
		$workAmendmentDescArray[$i] = $dbg->f('work_amendment_desc');
		$feedbackStatusArray[$i] = $dbg->f('feedback_status');
		$feedbackStatusDescArray[$i] = $dbg->f('feedback_status_desc');
		$feedbackDateArray[$i] = $dbg->f('feedback_date');
		$studentMatrixNoArray[$i] = $dbg->f('student_matrix_no');
		$remarksArray[$i] = $dbg->f('remarks');
		$archivedStatusArray[$i] = $dbg->f('archived_status');
		$archivedDateArray[$i] = $dbg->f('archived_date');
		$i++;
	} while ($dbg->next_record());
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang; ?>" lang="<?php echo $lang; ?>">

<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
	<meta name="author" content="MJMZ" />

	<title>Manage Work Amendment</title>
    <link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
    <link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
   	<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
	<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
	<link id="bs-css" href="../../../theme/css/button.css" rel="stylesheet" />
	<script src="../../../lib/js/jquery.min2.js"></script>
	<script src="../../../lib/js/jquery.colorbox.js"></script>
	<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
	<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>
</head>

<body>

<?php
if(!empty($msg)) 
{
    foreach($msg as $key) {
       echo $key;
    }
}
?>
    <form method="post" id="form-set">
	<table>
		<tr>							
			<td>Please enter searching criteria below:-</td>
		</tr>
	</table>
	<table>
		<tr>
			<td>WC Reference No</td>
			<td>:</td>
			<td><input type="text" id="searchReferenceNo" name="searchReferenceNo" value="<?=$_POST['searchReferenceNo']?>"/>
			</td>
		</tr>
		<tr>	
			<td>Student Name </td>	
			<td>:</td>			
			<td><input type="text" id="searchStudentName" name="searchStudentName" value="<?=$_POST['searchStudentName']?>"/>
			</td>
		</tr>
		<tr>	
			<td>Student Matrix No</td>	
			<td>:</td>			
			<td><input type="text" id="searchMatrixNo" name="searchMatrixNo" value="<?=$_POST['searchMatrixNo']?>"/>
			<input type="submit" name="btnSearch" value="Search" /><span style="color:#FF0000;">Note:</span>If no parameters are provided, it will search all.</td>
		</tr>
		
	</table>
	<br>
	<table>
		<tr>
			<td><label><strong>List of Attended Work Completion</strong></label></td>
		</tr>
		<tr>
			<td>Searching Results: <?=$row_cnt1?> record(s) found</td>
		</tr>
	  </table>
	  
		<? if($row_cnt1 > 0) {?>
		<div class = "viewA" style="overflow:auto;width: 980px; height: 200px;">
		<? } else { ?>
		<div class = "viewA" style="overflow:auto;width: 980px; height: 100px;">
		<? } ?>
      <table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="95%" class="thetable">
        <tr>
		  <th width="5%">No</th>
		  <th width="20%" align="left">Student</th>
		  <th width="20%" align="left">Evaluation Schedule</th>
		  <th width="20%" align="left">Evaluation Panel Status<br>Schoolboard Status</th>
		  <th width="15%" align="left">Feedback Status</th>
		  <th width="15%" align="left">Amendment Status</th>
		</tr>  
		<?
		if ($row_cnt1 > 0) {		
			for ($j=0; $j<$row_cnt1; $j++){	
				if($j % 2) $color ="first-row"; else $color = "second-row";
				?>			
				<tr class="<?=$color?>">
					<td align="center"><?=$j+1?>.</td>
					<td><?=$studentNameArray[$j]?><br><?=$studentMatrixNoArray[$j]?></td>
					<td><label>Thesis ID: <?=$thesisIdArray[$j]?><br>Ref: <?=$referenceNoArray[$j]?><br><br><?=$defenseDateArray[$j]?>,<br> <?=$defenseSTimeArray[$j]?> to <?=$defenseETimeArray[$j]?>,<br> <?=$venueArray[$j]?></label></td>
					<td><?=$workMarksDescArray[$j]?><br><?=$workEvaluationDescArray[$j]?>-[<?=$proposedMarksDescArray[$j]?>]</td>
					<td><?=$feedbackStatusDescArray[$j]?><br><?=$feedbackDateArray[$j]?></td>
					<td><?=$workAmendmentDescArray[$j]?><br><?=$workAmendmentDateArray[$j]?></td>
				</tr>		
			<? }			
		}		
		else {
			?>
			<table>
				<tr>
					<td>No record(s) found.</td>
				</tr>
			</table>
			<?
		}?>
	  </table>
    </div>
    </form>
</div>
</body>
</html>