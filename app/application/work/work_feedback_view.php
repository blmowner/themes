<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: work_feedback_view.php
//
// Created by: Zuraimi
// Created Date: 14-September-2015
// Modified by: Zuraimi
// Modified Date: 14-September-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

$user_id=$_SESSION['user_id'];
$workFeedbackId = $_GET['wfid'];
$panelEmployeeId = $_GET['eid'];

$workAmendmentId = $_GET['waid'];
$thesisId = $_GET['tid'];
$referenceNo = $_GET['ref'];
$proposalId = $_GET['pid'];
$calendarId = $_GET['cid'];
$workMarksId = $_GET['wmid'];
$workEvaluationStatus = $_GET['wes'];
$proposedMarksId = $_GET['pmid'];


$sql3 = "SELECT id, work_feedback_id, panel_feedback, page_affected,  
DATE_FORMAT(comment_date,'%d-%b-%Y %h:%i%p') as comment_date, comment,
DATE_FORMAT(submit_date,'%d-%b-%Y %h:%i%p') as submit_date,
status as feedback_status
FROM pg_work_feedback_detail
WHERE work_feedback_id = '$workFeedbackId'
AND status = 'A'
AND archived_status IS NULL
ORDER BY page_affected";

$result_sql3 = $dba->query($sql3);
$dba->next_record();
$row_cnt3 = mysql_num_rows($result_sql3);

$feedbackIdArray = Array();
$feedbackAmendDetailIdArray = Array();
$panelFeedbackArray = Array();
$pageAffectedArray = Array();
$reviewerIdArray = Array();
$commentDateArray = Array();
$reviewedStatusArray = Array();
$reviewedStatusDescArray = Array();
$feedbackStatusArray = Array();
$feedbackStatusDescArray = Array();
$commentArray = Array();
$submitDateArray = Array();
$i=0;

do {
	$feedbackIdArray[$i] = $dba->f('id');
	$feedbackAmendDetailIdArray[$i] = $dba->f('work_feedback_id');
	$panelFeedbackArray[$i] = $dba->f('panel_feedback');
	$pageAffectedArray[$i] = $dba->f('page_affected');
	$commentDateArray[$i] = $dba->f('comment_date');
	$reviewedStatusArray[$i] = $dba->f('reviewed_status');
	$reviewedStatusDescArray[$i] = $dba->f('reviewed_status_desc');
	$feedbackStatusArray[$i] = $dba->f('feedback_status');
	$feedbackStatusDescArray[$i] = $dba->f('feedback_status_desc');
	$commentArray[$i] = $dba->f('comment');
	$submitDateArray[$i] = $dba->f('submit_date');
	$i++;
}while ($dba->next_record());


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
	<script language="JavaScript" src="../../../lib/js/windowopen.js"></script>
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
			<td><strong>List of Entered Feedback</strong></td>
		</tr>
	  </table>
	  <table>
		<tr>
			<td>Searching Results: <?=$row_cnt3?> record(s) found</td>
		</tr>
	  </table>
	 	<? if($row_cnt3 > 3) {?>
		<div class = "viewA" style="overflow:auto;width: 980px; height: 350px;">
		<? } else { ?>
		<div class = "viewA" style="overflow:auto;width: 980px; height: 250px;">
		<? } ?>
       <table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="65%" class="thetable">
        <tr>
		  <th width="5%">No</th>
		  <th width="10%" align="left">Page No</th>
		  <th width="30%" align="left">Feedback of Panel Member</th>
		  <th width="20%" align="left">Comment by Supervisor</th>
		</tr>  
		<?
		if ($row_cnt3 > 0) {		
			for ($j=0; $j<$row_cnt3; $j++){	?>
				<?
				if($j % 2) $color ="first-row"; else $color = "second-row";
				?>			
				<tr class="<?=$color?>">					
					<td align="center"><?=$j+1?>.</td>
					<td><?=$pageAffectedArray[$j]?></td>
					<td><em><?=$submitDateArray[$j]?></em><?=$panelFeedbackArray[$j]?></td>
					<td><em><?=$commentDateArray[$j]?></em><br><?=$commentArray[$j]?></td>
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
		<tr>	<?  ?>	
			<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../work/work_feedback_detail.php?waid=<?=$workAmendmentId?>&tid=<?=$thesisId?>&ref=<?=$referenceNo?>&pid=<?=$proposalId?>&cid=<?=$calendarId?>&wmid=<?=$workMarksId?>&wes=<?=$workEvaluationStatus?>&pmid=<?=$proposedMarksId?>'" /></td>		
	<!--		<td> <input type="submit" name="submit" value="Print PDF" onclick="javascript:open_win('pdf_work_feedback_report.php?wfid=<?=$workFeedbackId?>&eid=<?=$panelEmployeeId?>&waid=<?=$workAmendmentId?>&tid=<?=$thesisId?>&ref=<?=$referenceNo?>&pid=<?=$proposalId?>&cid=<?=$calendarId?>&wmid=<?=$workMarksId?>&wes=<?=$workEvaluationStatus?>&pmid=<?=$proposedMarksId?>',1000,760,0,0,0,1,0,1,1,0,5,'winupload'); "/></td>-->
			
<!--			<td><input type="button" name="btnBack" value="Print PDF" onclick="javascript:document.location.href='../work/pdf_work_amendment_report.php?wfid=<?=$workFeedbackId?>&eid=<?=$panelEmployeeId?>&waid=<?=$workAmendmentId?>&tid=<?=$thesisId?>&ref=<?=$referenceNo?>&pid=<?=$proposalId?>&cid=<?=$calendarId?>&wmid=<?=$workMarksId?>&wes=<?=$workEvaluationStatus?>&pmid=<?=$proposedMarksId?>'" /></td>	--> 
		</tr>
	</table>
    </form>
</div>
</body>
</html>