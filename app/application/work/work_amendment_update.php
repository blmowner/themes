<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: work_amendment_update.php
//
// Created by: Zuraimi
// Created Date: 15-September-2015
// Modified by: Zuraimi
// Modified Date: 15-September-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

$user_id=$_SESSION['user_id'];
$workFeedbackId = $_GET['wfid'];
$workAmendmentDetailId = $_GET['wadid'];
$panelEmployeeId = $_GET['eid'];
$svEmployeeId = $_GET['seid'];
$workAmendmentId = $_GET['waid'];
$thesisId = $_GET['tid'];
$referenceNo = $_GET['ref'];
$proposalId = $_GET['pid'];
$calendarId = $_GET['cid'];
$workMarksId = $_GET['wmid'];
$workEvaluationStatus = $_GET['wes'];
$proposedMarksId = $_GET['pmid'];

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
	$pageAfterChg = $_POST['pageAfterChg'];
	$amendmentBeforeChg = $_POST['amendmentBeforeChg'];
	$amendmentAfterChg = $_POST['amendmentAfterChg'];
	$curdatetime = date("Y-m-d H:i:s");	
	
	if ($_POST['pageAfterChg'] == "") $msg[] = "<div class=\"error\"><span>Please enter <strong>Page No. After Change</strong> as required.</span></div>";
	if ($_POST['amendmentBeforeChg'] == "") $msg[] = "<div class=\"error\"><span>Please enter the <strong>Amendment Before Changes</strong> as required.</span></div>";	
	if ($_POST['amendmentAfterChg'] == "") $msg[] = "<div class=\"error\"><span>Please enter the <strong>Amendment After Changes</strong> as required.</span></div>";	
	
	if(empty($msg))  
	{
		$sql1 = "SELECT id, work_amendment_id, work_feedback_detail_id, page_after_chg, amendment_before_chg, amendment_after_chg, 
		confirmed_date, confirmed_status, comment, comment_date, verified_by, verified_date, verified_status, verified_remark, 
		status, insert_by, insert_date, modify_by, modify_date
		FROM pg_work_amendment_detail
		WHERE id = '$workAmendmentDetailId'
		AND archived_status IS NULL";
		
		$dba->query($sql1);
		$dba->next_record();
		
		$workAmendmentId = $dba->f('work_amendment_id');
		$workFeedbackDetailId = $dba->f('work_feedback_detail_id');
		$confirmedStatus = $dba->f('confirmed_status');
		$verifiedStatus = $dba->f('verified_status');
		$comment = $dba->f('comment');
		$commentDate = $dba->f('comment_date');
		$status = $dba->f('status');
		$insertBy = $dba->f('insert_by');
		$insertDate = $dba->f('insert_date');
		$modifyBy = $dba->f('modify_by');
		$modifyDate = $dba->f('modify_date');
	
		
		if (($status == "PND") || ($status == "ARQ")) {
			$newWorkAmendmentDetailId = runnum('id','pg_work_amendment_detail');
		
			$sql4 = "INSERT INTO pg_work_amendment_detail
			(id, work_amendment_id, work_feedback_detail_id, page_after_chg, amendment_before_chg, amendment_after_chg, 
			confirmed_status, comment, comment_date, verified_status, amendment_date, status, insert_by, insert_date, modify_by, modify_date)
			VALUES ('$newWorkAmendmentDetailId', '$workAmendmentId', '$workFeedbackDetailId', '$pageAfterChg', '$amendmentBeforeChg',
			'$amendmentAfterChg', '$confirmedStatus', '$comment', '$commentDate', '$verifiedStatus', '$curdatetime',
			'AIP', '$insertBy', '$insertDate', '$user_id', '$curdatetime')";
			
			$dba->query($sql4);
			
			$sql5 = "UPDATE pg_work_amendment_detail
			SET archived_status = 'ARC', archived_date = '$curdatetime'
			WHERE id = '$workAmendmentDetailId'
			AND archived_status IS NULL";
			
			$dba->query($sql5);
		}
		else if ($status == "AIP") {
			$sql2 = "UPDATE pg_work_amendment_detail
			SET page_after_chg = '$pageAfterChg', amendment_before_chg = '$amendmentBeforeChg', amendment_after_chg = '$amendmentAfterChg',
			amendment_date = '$curdatetime', modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE id = '$workAmendmentDetailId'";
			
			$dba->query($sql2);
		}
		
		$msg[] = "<div class=\"success\"><span>The amendment has been updated successfully.</span></div>";
	}
}

if ($newWorkAmendmentDetailId!="") $workAmendmentDetailId = $newWorkAmendmentDetailId;
	
$sql3 = "SELECT b.page_affected, b.panel_feedback, a.page_after_chg, a.amendment_before_chg, a.amendment_after_chg, 
a.confirmed_date, a.confirmed_status, a.verified_by, a.verified_date, a.verified_status, a.verified_remark, a.status, 
e.description as status_desc
FROM pg_work_amendment_detail a
LEFT JOIN pg_work_feedback_detail b ON (b.id = a.work_feedback_detail_id)
LEFT JOIN ref_amendment_status e ON (e.id = a.status)
WHERE a.id = '$workAmendmentDetailId'
AND a.archived_status IS NULL
AND b.archived_status IS NULL";

$result_sql3 = $dba->query($sql3);
$dba->next_record();
$row_cnt3 = mysql_num_rows($result_sql3);

$pageAffected = $dba->f('page_affected'); 
$pageAfterChg = $dba->f('page_after_chg'); 
$panelFeedback = $dba->f('panel_feedback'); 
$amendmentBeforeChg = $dba->f('amendment_before_chg'); 
$amendmentAfterChg = $dba->f('amendment_after_chg'); 
$confirmedDate = $dba->f('confirmed_date'); 
$confirmedStatus = $dba->f('confirmed_status'); 
$verifiedBy = $dba->f('verified_by'); 
$verifiedDate = $dba->f('verified_date'); 
$verifiedStatus = $dba->f('verified_status'); 
$verifiedRemark = $dba->f('verified_remark'); 
$status = $dba->f('status'); 
$statusDesc = $dba->f('status_desc'); 
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
	<input type="hidden" id="workAmendmentDetailId" name="workAmendmentDetailId" value="<?=$workAmendmentDetailId?>"/>
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
			<td><strong>Work Completion Amendment Report</strong> - <em>Please update your amendment here</em></td>
		</tr>
	</table>
	<?
	if ($_POST['pageAfterChg']!="") $pageAfterChg = $_POST['pageAfterChg'];
	if ($_POST['amendmentBeforeChg']!="") $amendmentBeforeChg = $_POST['amendmentBeforeChg'];
	if ($_POST['amendmentAfterChg']!="") $amendmentAfterChg = $_POST['amendmentAfterChg'];	
	?>
	<table width="100%">
		<tr>
			<td style="background-color: rgba(105, 162, 255, 0.7);" width="7%">Page No. Before Change</th>
			<td width="1%">:</td>
			<td width="34%"><label><?=$pageAffected?></label></td>
		</tr>
		<tr>
			<td style="background-color: rgba(105, 162, 255, 0.7);" width="7%">Page No. After Change <span style="color:#FF0000">*</span></td>
			<td width="1%">:</td>
			<td width="34%"><input type="text" id="pageAfterChg" name="pageAfterChg" value="<?=$pageAfterChg?>"/></td>
		</tr>
		<tr>
			<td style="background-color: rgba(105, 162, 255, 0.7);" width="7%">Feedback by Panel Member</td>
			<td width="1%">:</td>
			<td colspan="84%"><label><?=$panelFeedback?></label></td>
		</tr>		
	</table>
	<br>
	<table width="100%">
		<tr>
			<td>Amendment Before Change</strong> <span style="color:#FF0000">*</span></td>
			<td>Amendment After Change</strong> <span style="color:#FF0000">*</span></td>
		</tr>
		<tr>
			<td><textarea name="amendmentBeforeChg" id="amendmentBeforeChg" class="ckeditor" ><?=$amendmentBeforeChg?></textarea></td>
			<td><textarea name="amendmentAfterChg" id="amendmentAfterChg" class="ckeditor" ><?=$amendmentAfterChg?></textarea></td>
		</tr>		
	</table>
	<table>
		<tr>
			<td><label>Notes:<br/>
			1. Field marks with (<span style="color:#FF0000">*</span>) is compulsory.
			</td>
		</tr>
	</table>
	<table>
		<tr>
			<td><input type="submit" name="btnUpdate" id="btnUpdate" value="Update"></td>
			<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../work/work_amendment_change.php?wadid=<?=$workAmendmentDetailId?>&wfid=<?=$workFeedbackId?>&eid=<?=$panelEmployeeId?>&seid=<?=$employeeId?>&waid=<?=$workAmendmentId?>&tid=<?=$thesisId?>&ref=<?=$referenceNo?>&pid=<?=$proposalId?>&cid=<?=$calendarId?>&wmid=<?=$workMarksId?>&wes=<?=$workEvaluationStatus?>&pmid=<?=$proposedMarksId?>'" /></td>
		</tr>		
	</table>
    </form>
</div>
</body>
</html>