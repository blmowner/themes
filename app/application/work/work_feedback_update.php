<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: work_feedback_update.php
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
$workFeedbackDetailId = $_GET['wfdid'];
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
	$workFeedbackDetailId = $_POST['workFeedbackDetailId'];
	$pageAffected = $_POST['pageAffected'];
	$panelFeedback = $_POST['panelFeedback'];
	$curdatetime = date("Y-m-d H:i:s");	
	
	if ($_POST['pageAffected'] == "") $msg[] = "<div class=\"error\"><span>Please enter affected page number as required.</span></div>";
	if ($_POST['panelFeedback'] == "") $msg[] = "<div class=\"error\"><span>Please enter the feedback as required.</span></div>";	
	
	if(empty($msg))  
	{
		$sql1 = "SELECT id, work_feedback_id, panel_feedback, page_affected, comment_date, comment, 
		submit_date, status, insert_by, insert_date, modify_by, modify_date
		FROM pg_work_feedback_detail
		WHERE id = '$workFeedbackDetailId'
		AND archived_status IS NULL";
		
		$db->query($sql1);
		$db->next_record();
		
		$workFeedbackId = $db->f('work_feedback_id');  
		$commentDate = $db->f('comment_date'); 
		$comment = $db->f('comment');
		$submitDate = $db->f('submit_date');  
		$status = $db->f('status');
		$insertBy = $db->f('insert_by');  
		$insertDate = $db->f('insert_date');  		
		
		if ($status == 'N') {
			$sql2 = "UPDATE pg_work_feedback_detail
			SET panel_feedback = '$panelFeedback', page_affected = '$pageAffected', 
			modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE id = '$workFeedbackDetailId'
			AND status = 'N'
			AND archived_status IS NULL";			
			$dba->query($sql2);	
		}
		else if ($status == 'A') {
			$newWorkFeedbackDetailId = runnum('id','pg_work_feedback_detail');
			$sql4 = "INSERT INTO pg_work_feedback_detail
			(id, work_feedback_id, panel_feedback, page_affected, comment_date, comment, 
			submit_date, status, insert_by, insert_date, modify_by, modify_date)
			VALUE ('$newWorkFeedbackDetailId', '$workFeedbackId', '$panelFeedback', '$pageAffected', '$commentDate', '$comment',
			'$submitDate', 'N', '$insertBy', '$insertDate', '$user_id', '$curdatetime')";
			$dba->query($sql4);
			
			$sql5 = "UPDATE pg_work_feedback_detail
			SET archived_status = 'ARC', archived_date = '$curdatetime'
			WHERE id = '$workFeedbackDetailId'
			AND status = 'A'
			AND archived_status IS NULL";
			$dba->query($sql5);
			
			//$_POST['newWorkFeedbackDetailId'] = $newWorkFeedbackDetailId;
		}
		else if ($status == 'T') {
			$sql2 = "UPDATE pg_work_feedback_detail
			SET panel_feedback = '$panelFeedback', page_affected = '$pageAffected', 
			modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE id = '$workFeedbackDetailId'
			AND status = 'T'
			AND archived_status IS NULL";			
			$dba->query($sql2);			
		}
		$msg[] = "<div class=\"success\"><span>The comment has been updated successfully.</span></div>";
	}
}

if ($newWorkFeedbackDetailId!="") $workFeedbackDetailId = $newWorkFeedbackDetailId;

$sql3 = "SELECT page_affected, panel_feedback, comment
FROM pg_work_feedback_detail 
WHERE id = '$workFeedbackDetailId'
AND status IN ('A', 'N')
AND archived_status IS NULL";

$result_sql3 = $dba->query($sql3);
$dba->next_record();
$row_cnt3 = mysql_num_rows($result_sql3);

$pageAffected = $dba->f('page_affected');
$panelFeedback = $dba->f('panel_feedback');
$comment = $dba->f('comment');




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
	<input type="hidden" name="workFeedbackDetailId" id="workFeedbackDetailId" value="<?=$workFeedbackDetailId?>">
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
			<td><strong>Comment by Supervisor</td>
		</tr>
	</table>
	<table>
		<tr>
			<td><label><?=$comment?></label></td>
			</td>
		</tr>	
	</table>
	<table>
		<tr>
			<td><strong>Please update your feedback here</td>
		</tr>
	</table>
	<table>
		<tr>
			<td>Affected Page No. <span style="color:#FF0000">*</span></td>
			<td>:</td>
			<td><input type="text" id="pageAffected" name="pageAffected" value="<?=$pageAffected?>"/></td>
		</tr>		
	</table>
	<table>
		<tr>
			<td>Feedback of Panel Member <span style="color:#FF0000">*</span></td>
		</tr>
		<tr>
			<td><textarea name="panelFeedback" id="panelFeedback" class="ckeditor" ><?=$panelFeedback?></textarea></td>
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
			<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../work/work_feedback_change.php?waid=<?=$workAmendmentId?>&wfid=<?=$workFeedbackId?>&eid=<?=$panelEmployeeId?>&tid=<?=$thesisId?>&ref=<?=$referenceNo?>&pid=<?=$proposalId?>&cid=<?=$calendarId?>&wmid=<?=$workMarksId?>&wes=<?=$workEvaluationStatus?>&pmid=<?=$proposedMarksId?>'" /></td>
		</tr>		
	</table>
    </form>
</div>
</body>
</html>