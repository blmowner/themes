<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: work_review_amendment_change.php
//
// Created by: Zuraimi
// Created Date: 28-September-2015
// Modified by: Zuraimi
// Modified Date: 28-September-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

$user_id=$_SESSION['user_id'];

$workFeedbackId = $_GET['wfid'];
$panelEmployeeId = $_GET['eid'];
$employeeId = $_GET['seid'];
$workAmendmentId = $_GET['waid'];
$thesisId = $_GET['tid'];
$referenceNo = $_GET['ref'];
$proposalId = $_GET['pid'];
$calendarId = $_GET['cid'];
$workMarksId = $_GET['wmid'];
$workEvaluationStatus = $_GET['wes'];
$proposedMarksId = $_GET['pmid'];
$studentMatrixNo = $_GET['mn'];

$sql1 = "SELECT a.id, a.work_feedback_detail_id, b.page_affected, b.panel_feedback, a.page_after_chg, 
a.amendment_before_chg, a.amendment_after_chg, DATE_FORMAT(a.amendment_date,'%d-%b-%Y %h:%i%p') as amendment_date,
DATE_FORMAT(a.comment_date,'%d-%b-%Y %h:%i%p') as comment_date, a.comment,
a.confirmed_date, a.verified_by, a.verified_status, a.status, e.description as status_desc
FROM pg_work_amendment_detail a
LEFT JOIN pg_work_feedback_detail b ON (b.id = a.work_feedback_detail_id)
LEFT JOIN pg_work_feedback c ON (c.id = b.work_feedback_id)
LEFT JOIN pg_work_amendment d ON (d.id = c.work_amendment_id)
LEFT JOIN ref_amendment_status e ON (e.id = a.status)
WHERE d.pg_thesis_id = '$thesisId'
AND d.pg_proposal_id = '$proposalId'
AND d.student_matrix_no = '$studentMatrixNo'
AND d.id = '$workAmendmentId'
AND c.id = '$workFeedbackId'
AND c.panel_employee_id = '$panelEmployeeId'
AND d.feedback_status = 'FVE'
AND a.archived_status IS NULL
AND b.archived_status IS NULL
AND c.archived_status IS NULL
AND d.archived_status IS NULL
ORDER BY b.page_affected";

$result_sql1 = $dba->query($sql1);
$dba->next_record();
$row_cnt1 = mysql_num_rows($result_sql1);

$workAmendmentDetailIdArray = Array(); 
$workFeedbackDetailIdArray = Array();  
$pageAffectedArray = Array();
$pageAfterChgArray = Array();  
$panelFeedbackArray = Array();
$amendmentDateArray = Array();  
$amendmentBeforeChgArray = Array();  
$amendmentAfterChgArray = Array(); 
$commentArray = Array(); 
$commentDateArray = Array();
$confirmedDateArray = Array();  
$confirmedStatusArray = Array();  
$verifiedByArray = Array(); 
$verifiedDateArray = Array();  
$verifiedStatusArray = Array();  
$verifiedRemarkArray = Array(); 
$statusArray = Array();  
$statusDescArray = Array();

$i=0;

do {
	$workAmendmentDetailIdArray[$i] = $dba->f('id');
	$workFeedbackDetailIdArray[$i] = $dba->f('work_feedback_detail_id');
	$pageAffectedArray[$i] = $dba->f('page_affected'); 
	$pageAfterChgArray[$i] = $dba->f('page_after_chg'); 
	$panelFeedbackArray[$i] = $dba->f('panel_feedback'); 
	$amendmentDateArray[$i] = $dba->f('amendment_date');
	$amendmentBeforeChgArray[$i] = $dba->f('amendment_before_chg'); 
	$amendmentAfterChgArray[$i] = $dba->f('amendment_after_chg'); 
	$commentArray[$i] = $dba->f('comment');
	$commentDateArray[$i] = $dba->f('comment_date');
	$confirmedDateArray[$i] = $dba->f('confirmed_date'); 
	$confirmedStatusArray[$i] = $dba->f('confirmed_status'); 
	$verifiedByArray[$i] = $dba->f('verified_by'); 
	$verifiedDateArray[$i] = $dba->f('verified_date'); 
	$verifiedStatusArray[$i] = $dba->f('verified_status'); 
	$verifiedRemarkArray[$i] = $dba->f('verified_remark'); 
	$statusArray[$i] = $dba->f('status'); 
	$statusDescArray[$i] = $dba->f('status_desc'); 
	$i++;
	
} while ($dba->next_record());

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
			<td><strong>Panel Evaluation Detail</strong></td>
		</tr>
	</table>
	<table>
		<?
		$sql7 = "SELECT name
		FROM new_employee
		WHERE empid = '$panelEmployeeId'";
		
		$dbc->query($sql7); 
		$dbc->next_record();
		
		$panelEmployeeName = $dbc->f('name');
		?>
		<tr>
			<td>Name</td>
			<td>:</td>
			<td><label><?=$panelEmployeeName?> (<?=$panelEmployeeId?>)</label></td>
			</td>
		</tr>
	</table>
	<br>
	<table>
		<tr>
			<td><strong>List of Amendment for Work Completion</strong></td>
		</tr>
	  </table>
	  <table>
		<tr>
			<td>Searching Results: <?=$row_cnt1?> record(s) found</td>
		</tr>
	  </table>
	 	<? if($row_cnt1 > 3) {?>
		<div class = "viewA" style="overflow:auto;width: 980px; height: 350px;">
		<? } else { ?>
		<div class = "viewA" style="overflow:auto;width: 980px; height: 250px;">
		<? } ?>
       <table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="100%" class="thetable">
        <tr>
		  <th width="5%">No</th>
		  <th width="5%" align="center">Page No (Before)</th>
		  <th width="20%" align="left">Feedback of Panel Member</th>
		  <th width="5%" align="center">Page No (After)</th>
		  <th width="20%" align="left">Amendment based on the Comment</th>
		  <th width="20%" align="left">Comment by Supervisor</th>
		  <th width="5%" align="left">Action</th>
		</tr>  
		<?
		if ($row_cnt1 > 0) {		
			for ($j=0; $j<$row_cnt1; $j++){	?>
				<?
				if($j % 2) $color ="first-row"; else $color = "second-row";
				?>			
				<tr class="<?=$color?>">					
					<td align="center"><?=$j+1?>.</td>
					<td align="center"><?=$pageAffectedArray[$j]?></td>
					<td><?=$panelFeedbackArray[$j]?></td>
					<td align="center"><?=$pageAfterChgArray[$j]?></td>
					<td>Last Update: <?=$amendmentDateArray[$j]?><br>Amendment (Before Change):<br><em><?=$amendmentBeforeChgArray[$j]?></em><br><br>Amendment (After Change):<br><em><?=$amendmentAfterChgArray[$j]?></em></td>
					<td><em><?=$commentDateArray[$j]?></em><br><?=$commentArray[$j]?></td>
					<td><a href="../work/work_review_amendment_update.php?wadid=<?=$workAmendmentDetailIdArray[$j]?>&wfid=<?=$workFeedbackId?>&eid=<?=$panelEmployeeId?>&waid=<?=$workAmendmentId?>&tid=<?=$thesisId?>&ref=<?=$referenceNo?>&pid=<?=$proposalId?>&cid=<?=$calendarId?>&wmid=<?=$workMarksId?>&wes=<?=$workEvaluationStatus?>&pmid=<?=$proposedMarksId?>&mn=<?=$studentMatrixNo?>">Update</a></td>
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
	<br>
	<table>
		<tr>		
			<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../work/work_review_amendment_detail.php?waid=<?=$workAmendmentId?>&tid=<?=$thesisId?>&ref=<?=$referenceNo?>&pid=<?=$proposalId?>&cid=<?=$calendarId?>&wmid=<?=$workMarksId?>&wes=<?=$workEvaluationStatus?>&pmid=<?=$proposedMarksId?>&mn=<?=$studentMatrixNo?>'" /></td>			
		</tr>
	</table>
    </form>
</div>
</body>
</html>