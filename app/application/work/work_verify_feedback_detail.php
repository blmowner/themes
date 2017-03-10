<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: work_verify_feedback_detail.php
//
// Created by: Zuraimi
// Created Date: 12-September-2015
// Modified by: Zuraimi
// Modified Date: 12-September-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

$user_id=$_SESSION['user_id'];
$workAmendmentId = $_GET['waid'];
$thesisId = $_GET['tid'];
$referenceNo = $_GET['ref'];
$proposalId = $_GET['pid'];
$calendarId = $_GET['cid'];
$workMarksId = $_GET['wmid'];
$workEvaluationStatus = $_GET['wes'];
$proposedMarksId = $_GET['pmid'];
$studentMatrixNo = $_GET['mn'];



$sql1 = "SELECT a.id as work_feedback_id, a.panel_employee_id, a.feedback_status, b.description as feedback_status_desc, 
DATE_FORMAT(a.verified_date,'%d-%b-%Y %h:%i%p') as verified_date, b.remarks
FROM pg_work_feedback a
LEFT JOIN ref_amendment_status b ON (b.id = a.feedback_status)
WHERE a.work_amendment_id = '$workAmendmentId'
AND a.archived_status IS NULL
AND b.status = 'A'
ORDER BY a.id";

$result_sql1 = $dbg->query($sql1); 
$dbg->next_record();
$row_cnt1 = mysql_num_rows($result_sql1);

$workFeedbackIdArray = Array();
$panelEmployeeIdArray = Array();
$panelEmployeeNameArray = Array();
$feedbackStatusArray = Array();
$feedbackStatusDescArray = Array();
$verifiedDateArray = Array();
$remarksArray = Array();

$i=0;
do {
	$panelEmployeeIdArray[$i] = $dbg->f('panel_employee_id');
	
	$sql5 = "SELECT name
	FROM new_employee
	WHERE empid = '$panelEmployeeIdArray[$i]'";
	
	$dbc->query($sql5); 
	$dbc->next_record();
	
	$panelEmployeeNameArray[$i] = $dbc->f('name');
	$workFeedbackIdArray[$i] = $dbg->f('work_feedback_id');
	$reviewedStatusArray[$i] = $dbg->f('reviewed_status');
	$feedbackStatusArray[$i] = $dbg->f('feedback_status');
	$feedbackStatusDescArray[$i] = $dbg->f('feedback_status_desc');
	$reviewedStatusDescArray[$i] = $dbg->f('reviewed_status_desc');
	$verifiedDateArray[$i] = $dbg->f('verified_date');
	$remarksArray[$i] = $dbg->f('remarks');
	$i++;
} while ($dbg->next_record());

$sql3 = "SELECT DATE_FORMAT(defense_date,'%d-%b-%Y') as defense_date, 
DATE_FORMAT(defense_stime,'%h:%i%p') as defense_stime,
DATE_FORMAT(defense_etime,'%h:%i%p') as defense_etime, venue
FROM pg_calendar
WHERE id = '$calendarId'
AND status = 'A'
AND archived_status IS NULL";
$dba->query($sql3);
$dba->next_record();
$defenseDate = $dba->f('defense_date');
$defenseSTime = $dba->f('defense_stime');
$defenseETime = $dba->f('defense_etime');
$defenseVenue = $dba->f('venue');

$sql4 = "SELECT description
FROM ref_work_marks
WHERE id = '$workMarksId'
AND status = 'A'";
$dba->query($sql4);
$dba->next_record();
$workMarksDesc = $dba->f('description');

$sql5 = "SELECT description
FROM ref_work_marks
WHERE id = '$proposedMarksId'
AND status = 'A'";
$dba->query($sql5);
$dba->next_record();
$proposedMarksDesc = $dba->f('description');

$sql6 = "SELECT description
FROM ref_proposal_status
WHERE id = '$workEvaluationStatus'
AND status = 'A'";
$dba->query($sql6);
$dba->next_record();
$workEvaluationDesc = $dba->f('description');

$sql_sv = "SELECT a.pg_employee_empid, a.ref_supervisor_type_id, d.description as supervisor_type,
DATE_FORMAT(a.acceptance_date,'%d-%b-%Y %h:%i%p') as acceptance_date, h.description as role_status_desc
FROM pg_supervisor a 
LEFT JOIN ref_supervisor_type d ON (d.id = a.ref_supervisor_type_id) 
LEFT JOIN pg_thesis f ON (f.student_matrix_no = a.pg_student_matrix_no)
LEFT JOIN pg_proposal g ON (g.pg_thesis_id = f.id)
LEFT JOIN ref_role_status h ON (h.id = a.role_status)
WHERE a.pg_student_matrix_no='$user_id'
AND g.pg_thesis_id = '$thesisId'
AND g.id = '$proposalId'
AND a.acceptance_status = 'ACC'
AND a.ref_supervisor_type_id = 'SV'
AND a.role_status = 'PRI'
AND g.verified_status in ('APP','AWC')
AND g.status in ('APP','APC')
AND f.archived_status IS NULL 
AND g.archived_status IS NULL
AND a.status = 'A'";

$result_sql_sv = $db->query($sql_sv); //echo $sql;
$db->next_record();
$row_cnt_supervisor = mysql_num_rows($result_sql_sv);

$employeeId = $db->f('pg_employee_empid');

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
	<!--<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>-->
</head>

<body>
<SCRIPT LANGUAGE="JavaScript">
function verifyConfirm () {
    var confirmSubmit = confirm("Click OK if you confirm to submit it else click Cancel to stay on the same page.");
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
			<td><strong>Work Completion - Feedback</strong></td>
		</tr>
	</table>	  
	<table>
	<?if (substr($studentMatrixNo,0,2) != '07') { 
		$dbConnStudent= $dbc; 
	} 
	else { 
		$dbConnStudent=$dbc1; 
	}
	$sql9 = "SELECT name
	FROM student
	WHERE matrix_no = '$studentMatrixNo'";
	$result9 = $dbConnStudent->query($sql9); 
	
	$dbConnStudent->next_record();
	$studentName = $dbConnStudent->f('name');
	?>
		<tr>
			<td>Student</td>
			<td>:</td>
			<td><label><?=$studentName?> (<?=$studentMatrixNo?>)</label></td>
		</tr>
		<tr>
			<td>Thesis ID</td>
			<td>:</td>
			<td><label><?=$thesisId?></label></td>
		</tr>
		<tr>
			<td>Reference No</td>
			<td>:</td>
			<td><label><?=$referenceNo?></label></td>
			</td>
		</tr>
		<tr>
			<td>Evaluation Schedule</td>
			<td>:</td>
			<td><label><?=$defenseDate?>, <?=$defenseSTime?> to <?=$defenseETime?>, <?=$defenseVenue?></label></td>
		</tr>
		<tr>
			<td>Evaluation Panel Status</td>
			<td>:</td>
			<td><label><?=$workMarksDesc?></label></td>
		</tr>
		<tr>
			<td>Schoolboard Status</td>
			<td>:</td>
			<td><label><?=$workEvaluationDesc?> [<?=$proposedMarksDesc?>]</label></td>
		</tr>		
	</table>
	<br>
	<table>
		<tr>
			<td><label><strong>List of Evaluation Panel</strong></label></td>
		</tr>
		<tr>
			<td>Searching Results: <?=$row_cnt1?> record(s) found</td>
		</tr>
	  </table>
	 	<? if($row_cnt1 > 0) {?>
		<div class = "viewA" style="overflow:auto;width: 980px; height: 150px;">
		<? } else { ?>
		<div class = "viewA" style="overflow:auto;width: 980px; height: 100px;">
		<? } ?>
      <table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="55%" class="thetable">
        <tr>
		  <th width="5%">No</th>
		  <th width="10%" align="left">Staff ID</th>
		  <th width="30%" align="left">Panel Evaluation Name</th>
		  <th width="10%" align="left">Action</th>
		</tr>  
		<?
		if ($row_cnt1 > 0) {		
			for ($j=0; $j<$row_cnt1; $j++){	?>
				<?
				if($j % 2) $color ="first-row"; else $color = "second-row";
				?>			
				<tr class="<?=$color?>">
					<td align="center"><?=$j+1?>.</td>
					<td><?=$panelEmployeeIdArray[$j]?></td>
					<td><?=$panelEmployeeNameArray[$j]?></td>
					<?if ($feedbackStatusArray[$j]=="FVE"){
						?>
						<td align="left"><a href="../work/work_verify_feedback_view.php?wfid=<?=$workFeedbackIdArray[$j]?>&eid=<?=$panelEmployeeIdArray[$j]?>&waid=<?=$workAmendmentId?>&tid=<?=$thesisId?>&ref=<?=$referenceNo?>&pid=<?=$proposalId?>&cid=<?=$calendarId?>&wmid=<?=$workMarksId?>&wes=<?=$workEvaluationStatus?>&pmid=<?=$proposedMarksId?>&mn=<?=$studentMatrixNo?>" title="<?=$remarksArray[$j]?>">View</a></td>
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
	<table>
		<tr>		
			<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../work/work_verify_amendment.php';" /></td>			
		</tr>
	</table>
    </form>
</div>
</body>
</html>