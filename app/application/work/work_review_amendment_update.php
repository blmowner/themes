<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: work_review_amendment_update.php
//
// Created by: Zuraimi
// Created Date: 29-September-2015
// Modified by: Zuraimi
// Modified Date: 29-September-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

$user_id=$_SESSION['user_id'];
$workAmendmentDetailId = $_GET['wadid'];
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
$studentMatrixNo = $_GET['mn'];

function runnum($column_name, $tblname) 
{ 
	global $db_klas2;
	
	$run_start = "001";
	
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


if(isset($_POST['btnUpdate']) && ($_POST['btnUpdate'] <> ""))
{
	$workAmendmentDetailId = $_POST['workAmendmentDetailId'];
	$comment = $_POST['comment'];
	$curdatetime = date("Y-m-d H:i:s");	
	
	if ($_POST['comment'] == "") $msg[] = "<div class=\"error\"><span>Please enter the comment. It cannot be empty.</span></div>";
	
	if(empty($msg))  
	{
		$sql1 = "SELECT id, work_amendment_id, work_feedback_detail_id, page_after_chg, amendment_before_chg, 
		amendment_after_chg, amendment_date, comment, confirmed_date, confirmed_status, verified_by, verified_date, 
		verified_status, verified_remark, status, insert_by, insert_date, modify_by, modify_date
		FROM pg_work_amendment_detail
		WHERE id = '$workAmendmentDetailId'
		AND archived_status IS NULL";
		
		$db->query($sql1);
		$db->next_record();
		
		$workFeedbackDetailId = $db->f('work_feedback_detail_id'); 
		$pageAfterChg = $db->f('page_after_chg'); 
		$amendmentBeforeChg = $db->f('amendment_before_chg'); 
		$amendmentAfterChg = $db->f('amendment_after_chg'); 
		$amendmentDate = $db->f('amendment_date'); 
		$confirmedDate = $db->f('confirmed_date'); 
		$confirmedStatus = $db->f('confirmed_status'); 
		$verifiedBy = $db->f('verified_by'); 
		$verifiedDate = $db->f('verified_date'); 
		$verifiedStatus = $db->f('verified_status'); 
		$verifiedRemark = $db->f('verified_remark'); 
		$status = $db->f('status'); 
		$insertBy = $db->f('insert_by'); 
		$insertDate = $db->f('insert_date'); 
		$modifyBy = $db->f('modify_by'); 
		$modifyDate = $db->f('modify_date'); 		
		
		if ($status == 'ASU') {
			$newWorkAmendmentDetailId = runnum('id','pg_work_amendment_detail');
			$sql4 = "INSERT INTO pg_work_amendment_detail
			(id, work_amendment_id, work_feedback_detail_id, page_after_chg, amendment_before_chg, amendment_after_chg, amendment_date, 
			comment_date, comment, confirmed_date,confirmed_status, verified_by, verified_date, verified_status, verified_remark, 
			status, insert_by, insert_date, modify_by, modify_date)
			VALUE ('$newWorkAmendmentDetailId', '$workAmendmentId', '$workFeedbackDetailId', '$pageAfterChg', '$amendmentBeforeChg', '$amendmentAfterChg', 
			'$amendmentDate', '$curdatetime', '$comment', '$confirmedDate', '$confirmedStatus', '$verifiedBy', '$verifiedDate', '$verifiedStatus',
			'$verifiedRemark', 'NEW', '$insertBy', '$insertDate', '$modifyBy', '$modifyDate')";
			$dba->query($sql4);
			
			$sql5 = "UPDATE pg_work_amendment_detail
			SET archived_status = 'ARC', archived_date = '$curdatetime'
			WHERE id = '$workAmendmentDetailId'
			AND status = 'ASU'
			AND archived_status IS NULL";
			$dba->query($sql5);
						
		}
		else if ($status == 'NEW') {
			$sql2 = "UPDATE pg_work_amendment_detail
			SET comment = '$comment', comment_date = '$curdatetime', modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE id = '$workAmendmentDetailId'
			AND status = 'NEW'
			AND archived_status IS NULL";			
			$dba->query($sql2);			
		}
		$msg[] = "<div class=\"success\"><span>The comment has been updated successfully.</span></div>";
	}
}

if ($newWorkAmendmentDetailId!="") $workAmendmentDetailId = $newWorkAmendmentDetailId;
	
$sql3 = "SELECT b.page_affected, b.panel_feedback, 
DATE_FORMAT(a.comment_date,'%d-%b-%Y %h:%i%p') as comment_date, a.comment,
a.page_after_chg, a.amendment_before_chg, a.amendment_after_chg
FROM pg_work_amendment_detail a
LEFT JOIN pg_work_feedback_detail b ON (b.id = a.work_feedback_detail_id)
WHERE a.id = '$workAmendmentDetailId'
AND a.status IN ('ASU', 'NEW')
AND a.archived_status IS NULL
AND b.archived_status IS NULL";

$result_sql3 = $dba->query($sql3);
$dba->next_record();
$row_cnt3 = mysql_num_rows($result_sql3);

$pageAffected = $dba->f('page_affected');
$panelFeedback = $dba->f('panel_feedback');
$commentDate = $dba->f('comment_date');
$comment = $dba->f('comment');
$pageAfterChg = $dba->f('page_after_chg');
$amendmentBeforeChg = $dba->f('amendment_before_chg');
$amendmentAfterChg = $dba->f('amendment_after_chg');




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

<?php
if(!empty($msg)) 
{
    foreach($msg as $key) {
       echo $key;
    }
}
?>
    <form method="post" id="form-set">
	<input type="hidden" name="workAmendmentDetailId" id="workAmendmentDetailId" value="<?=$workAmendmentDetailId?>">
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
			<td><strong>Feedback by the Evaluation Panel</strong></td>
		</tr>
	</table>
	<table>
		<tr>
			<td>Feedback of Panel Member</td>
		</tr>
		<tr>
			<td><label><?=$panelFeedback?></label></td>
		</tr>
	</table>
	<br>
	<table>
		<tr>
			<td>Page No. Before Change</td>
			<td>:</td>
			<td><label><?=$pageAffected?></label></td>
		</tr>		
		<tr>
			<td>Page No. After Change</td>
			<td>:</td>
			<td><label><?=$pageAfterChg?></label></td>
		</tr>		
	</table>
	<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="100%" class="thetable">
		<tr>
			<th width="50%" align="left">Amendment based on the Comment - Before Change</th>
			<th width="50%" align="left">Amendment based on the Comment - After Change</th>
		</tr>
		<tr>
			<td><label><?=$amendmentBeforeChg?></label></td>
			<td><label><?=$amendmentAfterChg?></label></td>
		</tr>
	</table>
	<br>
	<table>
		<tr>
			<td><strong>Comment by Supervisor</strong></td>
		</tr>
	</table>
	<table>
		<tr>
			<td>Last Update:</td>
			<td><label><?=$commentDate?></label></td>
		</tr>
	</table>
	<table>
		<tr>
			<td><textarea name="comment" id="comment" class="ckeditor" ><?=$comment?></textarea></td>
		</tr>
	</table>
	<table>
		<tr>
			<td><input type="submit" name="btnUpdate" id="btnUpdate" value="Update"></td>
			<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../work/work_review_amendment_change.php?wadid=<?=$workAmendmentDetailIdArray?>&wfid=<?=$workFeedbackId?>&eid=<?=$panelEmployeeId?>&waid=<?=$workAmendmentId?>&tid=<?=$thesisId?>&ref=<?=$referenceNo?>&pid=<?=$proposalId?>&cid=<?=$calendarId?>&wmid=<?=$workMarksId?>&wes=<?=$workEvaluationStatus?>&pmid=<?=$proposedMarksId?>&mn=<?=$studentMatrixNo?>'" /></td>
		</tr>		
	</table>
    </form>
</div>
</body>
</html>