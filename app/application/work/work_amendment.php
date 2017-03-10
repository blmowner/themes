<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: work_amendment.php
//
// Created by: Zuraimi
// Created Date: 11-September-2015
// Modified by: Zuraimi
// Modified Date: 11-September-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

$user_id=$_SESSION['user_id'];

function runnum($column_name, $tblname) 
{ 
	global $db_klas2;
	
	$run_start = "0001";
	
	$sql_slct_max = "SELECT MAX($column_name) AS run_id FROM $tblname";
	$sql_slct = $db_klas2;
	$sql_slct->query($sql_slct_max);
	$sql_slct->next_record();

	if($sql_slct->num_rows($sql_slct_max)== 0 || $sql_slct->f("run_id")==NULL) 
	{
		$run_id = date("Ymd").$run_start;
	} 
	else 
	{
		$todate = date("Ymd");
		
		if($todate > substr($sql_slct->f("run_id"),0,8)) 
		{
			$run_id = $todate.$run_start;
		} 
		else 
		{
			$run_id = $sql_slct->f("run_id") + 1; 
		}
	}
	return $run_id;
}

if(isset($_POST['btnSearch']) && ($_POST['btnSearch'] <> "")) {
	
	$searchReferenceNo = $_POST['searchReferenceNo'];
	$searchFeedbackStatus = $_POST['searchFeedbackStatus'];
	$searchAmendmentStatus = $_POST['searchAmendmentStatus'];
	
	if ($searchReferenceNo=="") {
		$tmpSearchReferenceNo = "";
	}
	else {
		$tmpSearchReferenceNo = "AND a.reference_no = '$searchReferenceNo'";
	}
	if ($searchFeedbackStatus=="") {
		$tmpSearchFeedbackStatus = "";
	}
	else {
		$tmpSearchFeedbackStatus = "AND f.feedback_status = '$searchFeedbackStatus'";
	}
	if ($searchAmendmentStatus=="") {
		$tmpSearchAmendmentStatus = "";
	}
	else {
		$tmpSearchAmendmentStatus = "AND f.status = '$searchAmendmentStatus'";
	}
	$sql1 = "SELECT a.pg_thesis_id, a.pg_proposal_id, a.pg_calendar_id, a.status as work_status, a.reference_no, 
	b.id as work_evaluation_id, b.ref_work_marks_id, b.proposed_marks_id, 
	b.status as work_evaluation_status, d.description as work_evaluation_desc,
	b.respond_status, b.confirmed_status,
	c1.description as ref_work_marks_desc, c2.description as ref_proposed_marks_desc,
	DATE_FORMAT(e.defense_date,'%d-%b-%Y') as defense_date, 
	DATE_FORMAT(e.defense_stime,'%h:%i%p') as defense_stime,
	DATE_FORMAT(e.defense_etime,'%h:%i%p') as defense_etime, e.venue,
	f.id as work_amendment_id, f.status as work_amendment_status, g.description as work_amendment_desc,
	DATE_FORMAT(f.amendment_date,'%d-%b-%Y %h:%i%p') as amendment_date,
	f.feedback_status, h.description as feedback_status_desc,
	DATE_FORMAT(f.feedback_date,'%d-%b-%Y') as feedback_date
	FROM pg_work a
	LEFT JOIN pg_work_evaluation b ON (b.pg_work_id = a.id)
	LEFT JOIN ref_work_marks c1 ON (c1.id = b.ref_work_marks_id)
	LEFT JOIN ref_work_marks c2 ON (c2.id = b.proposed_marks_id)
	LEFT JOIN ref_proposal_status d ON (d.id = b.status)
	LEFT JOIN pg_calendar e ON (e.id = a.pg_calendar_id)
	LEFT JOIN pg_work_amendment f ON (f.pg_work_evaluation_id = b.id)
	LEFT JOIN ref_amendment_status g ON (g.id = f.status)
	LEFT JOIN ref_amendment_status h ON (h.id = f.feedback_status)
	WHERE a.student_matrix_no = '$user_id'"
	.$tmpSearchReferenceNo." "
	.$tmpSearchFeedbackStatus." "
	.$tmpSearchAmendmentStatus." "."	
	AND a.status = 'REC'
	AND a.submit_status = 'INP'
	AND a.respond_status = 'Y'
	AND ((b.status = 'APP' AND b.ref_work_marks_id = 'NSA')
	OR (b.status = 'DIS' AND b.ref_work_marks_id = 'SUB' AND b.proposed_marks_id = 'NSA')
	OR (b.status = 'DIS' AND b.ref_work_marks_id = 'SAT' AND b.proposed_marks_id = 'NSA'))
	AND e.status = 'A'
	AND a.archived_status IS NULL
	AND b.archived_status IS NULL
	AND f.archived_status IS NULL";

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
	$workAmendmentDateArray = Array();
	$workAmendmentDescArray = Array();
	$feedbackStatusDescArray = Array();
	$feedbackDateArray = Array();
							

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
		$feedbackStatusDescArray[$i] = $dbg->f('feedback_status_desc');
		$feedbackDateArray[$i] = $dbg->f('feedback_date');
		$i++;
	} while ($dbg->next_record());
}
else {
	$sql1 = "SELECT a.pg_thesis_id, a.pg_proposal_id, a.pg_calendar_id, a.status as work_status, a.reference_no, 
	b.id as work_evaluation_id, b.ref_work_marks_id, b.proposed_marks_id, 
	b.status as work_evaluation_status, d.description as work_evaluation_desc,
	b.respond_status, b.confirmed_status,
	c1.description as ref_work_marks_desc, c2.description as ref_proposed_marks_desc,
	DATE_FORMAT(e.defense_date,'%d-%b-%Y') as defense_date, 
	DATE_FORMAT(e.defense_stime,'%h:%i%p') as defense_stime,
	DATE_FORMAT(e.defense_etime,'%h:%i%p') as defense_etime, e.venue,
	f.id as work_amendment_id, f.status as work_amendment_status, g.description as work_amendment_desc,
	DATE_FORMAT(f.amendment_date,'%d-%b-%Y %h:%i%p') as amendment_date,
	f.feedback_status, h.description as feedback_status_desc,
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
	WHERE a.student_matrix_no = '$user_id'
	AND a.status = 'REC'
	AND a.submit_status = 'INP'
	AND a.respond_status = 'Y'
	AND ((b.status = 'APP' AND b.ref_work_marks_id = 'NSA')
	OR (b.status = 'DIS' AND b.ref_work_marks_id = 'SUB' AND b.proposed_marks_id = 'NSA')
	OR (b.status = 'DIS' AND b.ref_work_marks_id = 'SAT' AND b.proposed_marks_id = 'NSA'))
	AND e.status = 'A'
	AND a.archived_status IS NULL
	AND b.archived_status IS NULL
	AND f.archived_status IS NULL";

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
	$workAmendmentDateArray = Array();
	$workAmendmentDescArray = Array();
	$feedbackStatusArray = Array();
	$feedbackStatusDescArray = Array();
	$feedbackDateArray = Array(); 
							

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
		<tr>
			<td><strong>Note:</strong> <em>by default it will display all the the Work Completion which require amendment before resubmission.</em></td>
	</table>
	<br>
	<table>
		<tr>
			<td>WC Reference No</td>
			<td>:</td>
			<td><input type="text" id="searchReferenceNo" name="searchReferenceNo" value="<?=$_POST['searchReferenceNo']?>"/>
			</td>
		</tr>
		<?$sql2 = "SELECT id, description 
		FROM ref_amendment_status
		WHERE type = 'FEE'
		AND status = 'A'
		ORDER BY type";
		
		$result_sql2 = $dba->query($sql2);
		$dba->next_record();
		$row_cnt2 = mysql_num_rows($result_sql2);
		
		$theFeedbackStatusIdArray = Array();
		$theFeedbackStatusDescArray = Array();
		$k=0;
		do {
			$theFeedbackStatusIdArray[$k] = $dba->f('id');
			$theFeedbackStatusDescArray[$k] = $dba->f('description');
			$k++;
		} while ($dba->next_record());
		?>
		<tr>
			<td>Feedback Status</td>
			<td>:</td>
			<td>
				<select name="searchFeedbackStatus" id="searchFeedbackStatus">
					<option value=""></option>
					<?for ($l=0;$l<$row_cnt2;$l++) {
						if ($theFeedbackStatusIdArray[$l]==$_POST['searchFeedbackStatus']) {
						?>
							<option value="<?=$theFeedbackStatusIdArray[$l]?>" selected="true"><?=$theFeedbackStatusDescArray[$l]?></option>
						<?
						}
						else {
							?>
							<option value="<?=$theFeedbackStatusIdArray[$l]?>"><?=$theFeedbackStatusDescArray[$l]?></option>
							<?
						}
					}?>
				</select></td>
		</tr>
		<?$sql2 = "SELECT id, description 
		FROM ref_amendment_status
		WHERE type = 'AME'
		AND status = 'A'
		ORDER BY type";
		
		$result_sql2 = $dba->query($sql2);
		$dba->next_record();
		$row_cnt2 = mysql_num_rows($result_sql2);
		
		$amendmentStatusIdArray = Array();
		$amendmentStatusDescArray = Array();
		$k=0;
		do {
			$amendmentStatusIdArray[$k] = $dba->f('id');
			$amendmentStatusDescArray[$k] = $dba->f('description');
			$k++;
		} while ($dba->next_record());
		?>
		<tr>
			<td>Amendment Status</td>
			<td>:</td>
			<td>
				<select name="searchAmendmentStatus" id="searchAmendmentStatus">
					<option value=""></option>
					<?for ($l=0;$l<$row_cnt2;$l++) {
						if ($amendmentStatusIdArray[$l]==$_POST['searchAmendmentStatus']) {
						?>
							<option value="<?=$amendmentStatusIdArray[$l]?>" selected="true"><?=$amendmentStatusDescArray[$l]?></option>
						<?
						}
						else {
							?>
							<option value="<?=$amendmentStatusIdArray[$l]?>"><?=$amendmentStatusDescArray[$l]?></option>
							<?
						}
					}?>
				</select><input type="submit" name="btnSearch" value="Search" /><span style="color:#FF0000;">Note:</span>If no parameters are provided, it will search all.</td>
		</tr>
		
	</table>
	<br>
		<? if($row_cnt1 > 3) {?>
		<div class = "viewA" style="overflow:auto;width: 980px; height: 350px;">
		<? } else { ?>
		<div class = "viewA" style="overflow:auto;width: 980px; height: 250px;">
		<? } ?>
      <table>
		<tr>
			<td><label><strong>List of Attended Work Completion</strong></label></td>
		</tr>
		<tr>
			<td>Searching Results: <?=$row_cnt1?> record(s) found</td>
		</tr>
	  </table>
	  <table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="100%" class="thetable">
        <tr>
		  <th width="5%">No</th>
		  <th width="10%" align="left">Thesis ID</th>
		  <th width="10%" align="left">Reference No</th>
		  <th width="20%" align="left">Evaluation Schedule</th>
		  <th width="20%" align="left">Evaluation Panel Status</th>
		  <th width="15%" align="left">Schoolboard Status</th>
		  <th width="10%" align="left">Feedback Status</th>
		  <th width="10%" align="left">Amendment Status</th>
		</tr>  
		<?
		if ($row_cnt1 > 0) {		
			for ($j=0; $j<$row_cnt1; $j++){	?>
				<?
				if($j % 2) $color ="first-row"; else $color = "second-row";
				?>			
				<tr class="<?=$color?>">
					<td align="center"><?=$j+1?>.</td>
					<td><?=$thesisIdArray[$j]?></td>
					<td><?=$referenceNoArray[$j]?></td>
					<td><label><?=$defenseDateArray[$j]?>,<br> <?=$defenseSTimeArray[$j]?> to <?=$defenseETimeArray[$j]?>,<br> <?=$venueArray[$j]?></label></td>
					<td><?=$workMarksDescArray[$j]?></td>
					<td><?=$workEvaluationDescArray[$j]?><br>[<?=$proposedMarksDescArray[$j]?>]</td>
					<?if ($feedbackStatusArray[$j]=="PND") {?>
						<td><?=$feedbackStatusDescArray[$j]?><br><?=$feedbackDateArray[$j]?><a href="../work/work_feedback_detail.php?waid=<?=$workAmendmentIdArray[$j]?>&tid=<?=$thesisIdArray[$j]?>&pid=<?=$proposalIdArray[$j]?>&ref=<?=$referenceNoArray[$j]?>&cid=<?=$calendarIdArray[$j]?>&wmid=<?=$workMarksIdArray[$j]?>&wes=<?=$workEvaluationStatusArray[$j]?>&pmid=<?=$proposedMarksIdArray[$j]?>"><br>Update</a></td>
					<?}
					else {?>
						<td><?=$feedbackStatusDescArray[$j]?><br><?=$feedbackDateArray[$j]?><a href="../work/work_feedback_detail.php?waid=<?=$workAmendmentIdArray[$j]?>&tid=<?=$thesisIdArray[$j]?>&pid=<?=$proposalIdArray[$j]?>&ref=<?=$referenceNoArray[$j]?>&cid=<?=$calendarIdArray[$j]?>&wmid=<?=$workMarksIdArray[$j]?>&wes=<?=$workEvaluationStatusArray[$j]?>&pmid=<?=$proposedMarksIdArray[$j]?>"><br>View</a></td>
					<?}?>
					<? if ($workAmendmentStatusArray[$j]=="") {
						?><td><?=$workAmendmentDescArray[$j]?></td><?
					}
					else if ($workAmendmentStatusArray[$j]=="PND"){
						?>
						<td><?=$workAmendmentDescArray[$j]?><br><?=$workAmendmentDateArray[$j]?><a href="../work/work_amendment_detail.php?waid=<?=$workAmendmentIdArray[$j]?>&tid=<?=$thesisIdArray[$j]?>&pid=<?=$proposalIdArray[$j]?>&ref=<?=$referenceNoArray[$j]?>&cid=<?=$calendarIdArray[$j]?>&wmid=<?=$workMarksIdArray[$j]?>&wes=<?=$workEvaluationStatusArray[$j]?>&pmid=<?=$proposedMarksIdArray[$j]?>"><br>Update</a></td>
						<?
					}
					else if (($workAmendmentStatusArray[$j]=="ASU") || ($workAmendmentStatusArray[$j]=="ARQ") || ($workAmendmentStatusArray[$j]=="ACO") || ($workAmendmentStatusArray[$j]=="AVE")){
						?>
						<td><?=$workAmendmentDescArray[$j]?><br><?=$workAmendmentDateArray[$j]?><a href="../work/work_amendment_detail.php?waid=<?=$workAmendmentIdArray[$j]?>&tid=<?=$thesisIdArray[$j]?>&pid=<?=$proposalIdArray[$j]?>&ref=<?=$referenceNoArray[$j]?>&cid=<?=$calendarIdArray[$j]?>&wmid=<?=$workMarksIdArray[$j]?>&wes=<?=$workEvaluationStatusArray[$j]?>&pmid=<?=$proposedMarksIdArray[$j]?>"><br>View</a></td>
						<?
					}?>
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