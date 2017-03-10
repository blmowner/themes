<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: work_verify_feedback_view.php
//
// Created by: Zuraimi
// Created Date: 30-September-2015
// Modified by: Zuraimi
// Modified Date: 30-September-2015
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

if(isset($_POST['btnAdd']) && ($_POST['btnAdd'] <> ""))
{
	$pageAffected = $_POST['pageAffected'];
	$feedback = $_POST['feedback'];
	$curdatetime = date("Y-m-d H:i:s");	
	
	if ($_POST['pageAffected'] == "") $msg[] = "<div class=\"error\"><span>Please enter affected page number as required.</span></div>";
	if ($_POST['feedback'] == "") $msg[] = "<div class=\"error\"><span>Please enter the feedback as required.</span></div>";	
	
	if(empty($msg))  
	{
		$workFeedbackDetailId = runnum('id','pg_work_feedback_detail');
		$sql2 = "INSERT INTO pg_work_feedback_detail
		(id, work_feedback_id, panel_feedback, page_affected, reviewed_date, reviewed_status, reviewed_comment,
		submit_date, status, insert_by, insert_date, modify_by, modify_date, archived_status, archived_date)
		VALUES ('$workFeedbackDetailId', '$workFeedbackId', '$feedback', '$pageAffected', null, null, null, 
		'$curdatetime', 'A', '$user_id', '$curdatetime', '$user_id', '$curdatetime', null, null)";
		
		$dba->query($sql2);
		
		$msg[] = "<div class=\"success\"><span>The feedback has been added successfully.</span></div>";
	}
}


if(isset($_POST['btnDelete']) && ($_POST['btnDelete'] <> ""))
{
	$amendmentCheckbox = $_POST['amendment_checkbox'];
	$curdatetime = date("Y-m-d H:i:s");	
	if (count($amendmentCheckbox) == 0) {
		$msg[] = "<div class=\"error\"><span>Please tick which amendment to be submitted.</span></div>";
	}
	
	if(empty($msg))  
	{
		while (list ($key,$val) = @each ($amendment_checkbox)) {
			$sql4 = "DELETE FROM pg_work_feedback_detail
			WHERE id = '$val'";
			
			$db->query($sql4);
		
		}
		$msg[] = "<div class=\"success\"><span>The selected amendment record has been deleted successfully.</span></div>";
	}
	
}

$sql3 = "SELECT id, work_feedback_id, panel_feedback, page_affected,  
DATE_FORMAT(comment_date,'%d-%b-%Y %h:%i%p') as comment_date, comment,
DATE_FORMAT(submit_date,'%d-%b-%Y %h:%i%p') as submit_date,
status as feedback_status
FROM pg_work_feedback_detail 
WHERE work_feedback_id = '$workFeedbackId'
AND status IN ('A','N')
AND archived_status IS NULL
ORDER BY page_affected";

$result_sql3 = $dba->query($sql3);
$dba->next_record();
$row_cnt3 = mysql_num_rows($result_sql3);

$workFeedbackDetailIdArray = Array();
$workFeedbackIdArray = Array();
$panelFeedbackArray = Array();
$pageAffectedArray = Array();
$commentDateArray = Array();
$reviewedStatusDescArray = Array();
$feedbackStatusArray = Array();
$feedbackStatusDescArray = Array();
$commentArray = Array();
$submitDateArray = Array();
$i=0;

do {
	$workFeedbackDetailIdArray[$i] = $dba->f('id');
	$workFeedbackIdArray[$i] = $dba->f('work_feedback_id');
	$panelFeedbackArray[$i] = $dba->f('panel_feedback');
	$pageAffectedArray[$i] = $dba->f('page_affected');
	$commentDateArray[$i] = $dba->f('comment_date');
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
</head>

<body>
<SCRIPT LANGUAGE="JavaScript">
function respConfirm () {
    var confirmSubmit = confirm("Click OK if you confirm to delete else click Cancel to stay on the same page.");
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
	  	<? if($row_cnt3 > 0) {?>
		<div class = "viewA" style="overflow:auto;width: 980px; height: 200px;">
		<? } else { ?>
		<div class = "viewA" style="overflow:auto;width: 980px; height: 100px;">
		<? } ?>
      <table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="75%" class="thetable">
        <tr>
		  <th width="5%">No</th>
		  <th width="10%" align="left">Page No</th>
		  <th width="30%" align="left">Feedback of Panel Member</th>
		  <th width="30%" align="left">Comment by Supervisor</th>
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
					<td><?=$submitDateArray[$j]?><br><?=$panelFeedbackArray[$j]?></td>
					<td><?=$commentDateArray[$j]?><br><?=$commentArray[$j]?></td>									
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
			<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../work/work_verify_feedback_detail.php?waid=<?=$workAmendmentId?>&tid=<?=$thesisId?>&ref=<?=$referenceNo?>&pid=<?=$proposalId?>&cid=<?=$calendarId?>&wmid=<?=$workMarksId?>&wes=<?=$workEvaluationStatus?>&pmid=<?=$proposedMarksId?>&mn=<?=$studentMatrixNo?>'" /></td>			
		</tr>
	</table>
    </form>
</div>
</body>
</html>