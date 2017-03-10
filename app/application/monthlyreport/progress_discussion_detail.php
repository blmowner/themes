<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: progress_discussion_detail.php
//
// Created by: Zuraimi
// Created Date: 07-May-2015
// Modified by: Zuraimi
// Modified Date: 07-May-2015
//
//**************************************************************************************

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Untitled Document</title>
		<link rel="stylesheet" type="text/css" href="../../theme/css/<?php echo $css; ?>" />
		<link rel="stylesheet" type="text/css" href="../../theme/css/colorbox.css" media="screen" />
		<link rel="stylesheet" href="../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />    
		<script src="../../lib/js/jquery.min2.js"></script>
		<script src="../../lib/js/jquery.colorbox.js"></script>
		<script src="../../lib/js/jquery.mask_input-1.3.js"></script>
		<script type="text/javascript" src="../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
		<script src="../../lib/js/datePicker/jquery.ui.core.js"></script>
		<script src="../../lib/js/datePicker/jquery.ui.widget.js"></script>
		<script src="../../lib/js/datePicker/jquery.ui.datepicker.js"></script>
	</head>
	
	<body>

<?php

include("../../../lib/common.php");
checkLogin();

session_start();
$user_id=$_SESSION['user_id'];
$thesis_id=$_GET['tid'];
$proposal_id=$_GET['pid'];
$progress_id=$_GET['pgid'];
$referenceNo=$_GET['ref'];

function runnum($column_name, $tblname) 
{ 
    global $db_klas2;
    
    $run_start = "001";
    
    $sql_slct_max = "SELECT MAX(SUBSTR($column_name,2,11)) AS run_id FROM $tblname";
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

function runnum2($column_name, $tblname) 
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
	$msg = array();
	if (empty($_POST['add_meeting_date'])) $msg[] = "<div class=\"error\"><span>Please provide Meeting Date.</span></div>";
	if (empty($_POST['add_meeting_stime'])) $msg[] = "<div class=\"error\"><span>Please select Meeting Start Time from the list given.</span></div>";
	if (empty($_POST['add_meeting_etime'])) $msg[] = "<div class=\"error\"><span>Please select Meeting End Time from the list given.</span></div>";
	if (empty($_POST['add_meeting_mode'])) $msg[] = "<div class=\"error\"><span>Please select Meeting Mode from the list given.</span></div>";
	
	if(empty($msg)) 
	{
		/*$add_meeting_date = $_POST['add_meeting_date'];
		$add_meeting_stime = $_POST['add_meeting_stime'];
		$add_meeting_etime = $_POST['add_meeting_etime'];
		$add_meeting_mode = $_POST['add_meeting_mode'];*/
		
		
		$curdatetime = date("Y-m-d H:i:s");

		$monthly_meeting_id = runnum2('id','pg_progress_meeting');	

		$myMeetingDate = $_POST['add_meeting_date'];
		$myMeetingSTime = $_POST['add_meeting_stime'];
		$myMeetingETime = $_POST['add_meeting_etime'];
		$myMeetingMode = $_POST['add_meeting_mode'];

		if ($progress_id="") {
			$sqlMeeting = "INSERT INTO pg_progress_meeting(
			id, pg_progress_id, reference_no, pg_thesis_id, pg_proposal_id, student_matrix_no, meeting_date,
			meeting_stime, meeting_etime, meeting_mode, add_status, insert_by, insert_date, modify_by, modify_date)
			VALUES ('$monthly_meeting_id', '$progress_id', '$referenceNo', '$thesis_id', '$proposal_id', '$user_id',
			STR_TO_DATE('$myMeetingDate','%d-%M-%Y'), STR_TO_DATE('$myMeetingSTime','%H:%i'), STR_TO_DATE('$myMeetingETime','%H:%i'), 
			'$myMeetingMode', 'TMP', '$user_id', '$curdatetime', '$user_id', '$curdatetime')";

			$db_klas2->query($sqlMeeting); 
		}
		else {
			$sqlMeeting = "INSERT INTO pg_progress_meeting(
			id, pg_progress_id, reference_no, pg_thesis_id, pg_proposal_id, student_matrix_no, meeting_date,
			meeting_stime, meeting_etime, meeting_mode, add_status, insert_by, insert_date, modify_by, modify_date)
			VALUES ('$monthly_meeting_id', null, null, '$thesis_id', '$proposal_id', '$user_id',
			STR_TO_DATE('$myMeetingDate','%d-%M-%Y'), STR_TO_DATE('$myMeetingSTime','%H:%i'), STR_TO_DATE('$myMeetingETime','%H:%i'), 
			'$myMeetingMode', 'TMP', '$user_id', '$curdatetime', '$user_id', '$curdatetime')";

			$db_klas2->query($sqlMeeting); 
		}
		
		$msg[] = "<div class=\"success\"><span>Discussion detail has been added successfully.</span></div>";
	}
}

if(isset($_POST['btnUpdate']) && ($_POST['btnUpdate'] <> ""))
{					
	if (sizeof($_POST['progress_meeting_checkbox'])>0) {
		$curdatetime = date("Y-m-d H:i:s");
		while (list ($key,$val) = @each ($_POST['progress_meeting_checkbox'])) 
		{
			$progress_meeting_id = $_POST['progress_meeting_id'][$val];
			$meeting_date = $_POST['meeting_date'][$val];
			$meeting_stime = $_POST['meeting_stime'][$val];
			$meeting_etime = $_POST['meeting_etime'][$val];
			$meeting_mode = $_POST['meeting_mode'][$val];
			
			$sql1 = "UPDATE pg_progress_meeting
			SET meeting_date = STR_TO_DATE('$meeting_date','%d-%b-%Y'), 
			meeting_stime = STR_TO_DATE('$meeting_stime','%H:%i'),
			meeting_etime = STR_TO_DATE('$meeting_etime','%H:%i'),
			meeting_mode = '$meeting_mode',
			modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE id = '$progress_meeting_id'
			AND student_matrix_no = '$user_id'";
			
			$dba->query($sql1); 
			
			
		}
		$msg[] = "<div class=\"success\"><span>The selected discussion detail has been updated successfully.</span></div>";
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please tick which discussion record to update!</span></div>";
	}
}

if(isset($_POST['btnDelete']) && ($_POST['btnDelete'] <> ""))
{					
	if (sizeof($_POST['progress_meeting_checkbox'])>0) {
		$curdatetime = date("Y-m-d H:i:s");
		while (list ($key,$val) = @each ($_POST['progress_meeting_checkbox'])) 
		{
			$progress_meeting_id = $_POST['progress_meeting_id'][$val];
			
			$sql1 = "DELETE FROM pg_progress_meeting
			WHERE id = '$progress_meeting_id'
			AND student_matrix_no = '$user_id'";
			
			$dba->query($sql1); 
			
			
		}
		$msg[] = "<div class=\"success\"><span>The selected discussion detail has been deleted successfully.</span></div>";
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please tick which discussion record to delete!</span></div>";
	}
}

		
		
$sqlMeeting="SELECT id, DATE_FORMAT(meeting_date,'%d-%b-%Y') as meeting_date, DATE_FORMAT(meeting_stime,'%H:%i') as meeting_stime, 
DATE_FORMAT(meeting_etime,'%H:%i') as meeting_etime, meeting_mode as meeting_mode
FROM  pg_progress_meeting  
WHERE (pg_progress_id IS NULL OR pg_progress_id = '$progress_id')
AND pg_proposal_id='$proposal_id'
AND pg_thesis_id = '$thesis_id'
AND (reference_no = '' OR reference_no IS NULL)
AND student_matrix_no = '$user_id' 
ORDER BY meeting_date DESC ";			

$result = $db->query($sqlMeeting); 
$row_cnt = mysql_num_rows($result);
?>	


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
	<link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
    <link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
   	<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
	<link rel="stylesheet" href="../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
	<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
	<script src="../../../lib/js/jquery.min2.js"></script>
    <script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
	
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
<script>
$(function() {
	$( "#datepickerFirst" ).datepicker({
		changeMonth: true,
		changeYear: true,
		yearRange: '-100:+0',
		dateFormat: 'dd-mm-yy'
		});
});
</script>

 <?php
    if(!empty($msg)) 
	{
        foreach($msg as $err) 
		{
            echo $err;
        }
    }
?>

  <form id="form1" name="form1" method="post" enctype="multipart/form-data" onsubmit="return saveRec();">

	 <fieldset>
		<legend><strong>Discussion Details</strong></legend>

		<?php ?> <p>
		<table>
			<tr>
				<td><label>Meeting Date <span style="color:#FF0000">*</span></label></td>
				<td><input name="add_meeting_date" type="text" id="add_meeting_date" maxlength="50" value="<?=$_POST['add_meeting_date']?>" readonly=""></td>
				<?	$jscript .= "\n" . '$( "#add_meeting_date' . $no . '" ).datepicker({
												changeMonth: true,
												changeYear: true,
												yearRange: \'-100:+0\',
												dateFormat: \'dd-M-yy\'
											});';
					 
				?>
			</tr>
			<tr>
				<td><label>Meeting Start Time <span style="color:#FF0000">*</span></label></td>
				<td><select name="add_meeting_stime" id="add_meeting_stime">
				<option value="" selected = "selected">Select Time</option>				
				<?if ($_POST['add_meeting_stime'] == "07:00") {?><option value="07:00" selected = "selected">07:00 AM</option><?} else {?><option value="07:00">07:00 AM</option><?}?>									
				<?if ($_POST['add_meeting_stime'] == "07:30") {?><option value="07:30" selected = "selected">07:30 AM</option><?} else {?><option value="07:30">07:30 AM</option><?}?>									
				<?if ($_POST['add_meeting_stime'] == "08:00") {?><option value="08:00" selected = "selected">08:00 AM</option><?} else {?><option value="08:00">08:00 AM</option><?}?>									
				<?if ($_POST['add_meeting_stime'] == "08:30") {?><option value="08:30" selected = "selected">08:30 AM</option><?} else {?><option value="08:30">08:30 AM</option><?}?>									
				<?if ($_POST['add_meeting_stime'] == "09:00") {?><option value="09:00" selected = "selected">09:00 AM</option><?} else {?><option value="09:00">09:00 AM</option><?}?>									
				<?if ($_POST['add_meeting_stime'] == "09:30") {?><option value="09:30" selected = "selected">09:30 AM</option><?} else {?><option value="09:30">09:30 AM</option><?}?>									
				<?if ($_POST['add_meeting_stime'] == "10:00") {?><option value="10:00" selected = "selected">10:00 AM</option><?} else {?><option value="10:00">10:00 AM</option><?}?>									
				<?if ($_POST['add_meeting_stime'] == "10:30") {?><option value="10:30" selected = "selected">10:30 AM</option><?} else {?><option value="10:30">10:30 AM</option><?}?>									
				<?if ($_POST['add_meeting_stime'] == "11:00") {?><option value="11:00" selected = "selected">11:00 AM</option><?} else {?><option value="11:00">11:00 AM</option><?}?>									
				<?if ($_POST['add_meeting_stime'] == "11:30") {?><option value="11:30" selected = "selected">11:30 AM</option><?} else {?><option value="11:30">11:30 AM</option><?}?>									
				<?if ($_POST['add_meeting_stime'] == "12:00") {?><option value="12:00" selected = "selected">12:00 PM</option><?} else {?><option value="12:00">12:00 PM</option><?}?>									
				<?if ($_POST['add_meeting_stime'] == "12:30") {?><option value="12:30" selected = "selected">12:30 PM</option><?} else {?><option value="12:30">12:30 PM</option><?}?>									
				<?if ($_POST['add_meeting_stime'] == "13:00") {?><option value="13:00" selected = "selected">01:00 PM</option><?} else {?><option value="13:00">01:00 PM</option><?}?>									
				<?if ($_POST['add_meeting_stime'] == "13:30") {?><option value="13:30" selected = "selected">01:30 PM</option><?} else {?><option value="13:30">01:30 PM</option><?}?>									
				<?if ($_POST['add_meeting_stime'] == "14:00") {?><option value="14:00" selected = "selected">02:00 PM</option><?} else {?><option value="14:00">02:00 PM</option><?}?>									
				<?if ($_POST['add_meeting_stime'] == "14:30") {?><option value="14:30" selected = "selected">02:30 PM</option><?} else {?><option value="14:30">02:30 PM</option><?}?>									
				<?if ($_POST['add_meeting_stime'] == "15:00") {?><option value="15:00" selected = "selected">03:00 PM</option><?} else {?><option value="15:00">03:00 PM</option><?}?>									
				<?if ($_POST['add_meeting_stime'] == "15:30") {?><option value="15:30" selected = "selected">03:30 PM</option><?} else {?><option value="15:30">03:30 PM</option><?}?>									
				<?if ($_POST['add_meeting_stime'] == "16:00") {?><option value="16:00" selected = "selected">04:00 PM</option><?} else {?><option value="16:00">04:00 PM</option><?}?>									
				<?if ($_POST['add_meeting_stime'] == "16:30") {?><option value="16:30" selected = "selected">04:30 PM</option><?} else {?><option value="16:30">04:30 PM</option><?}?>									
				<?if ($_POST['add_meeting_stime'] == "17:00") {?><option value="17:00" selected = "selected">05:00 PM</option><?} else {?><option value="17:00">05:00 PM</option><?}?>									
				<?if ($_POST['add_meeting_stime'] == "17:30") {?><option value="17:30" selected = "selected">05:30 PM</option><?} else {?><option value="17:30">05:30 PM</option><?}?>									
				<?if ($_POST['add_meeting_stime'] == "18:00") {?><option value="18:00" selected = "selected">06:00 PM</option><?} else {?><option value="18:00">06:00 PM</option><?}?>									
				<?if ($_POST['add_meeting_stime'] == "18:30") {?><option value="18:30" selected = "selected">06:30 PM</option><?} else {?><option value="18:30">06:30 PM</option><?}?>									
				<?if ($_POST['add_meeting_stime'] == "19:00") {?><option value="19:00" selected = "selected">07:00 PM</option><?} else {?><option value="19:00">07:00 PM</option><?}?>									
				<?if ($_POST['add_meeting_stime'] == "19:30") {?><option value="19:30" selected = "selected">07:30 PM</option><?} else {?><option value="19:30">07:30 PM</option><?}?>									
				<?if ($_POST['add_meeting_stime'] == "20:00") {?><option value="20:00" selected = "selected">08:00 PM</option><?} else {?><option value="20:00">08:00 PM</option><?}?>									
				<?if ($_POST['add_meeting_stime'] == "20:30") {?><option value="20:30" selected = "selected">08:30 PM</option><?} else {?><option value="20:30">08:30 PM</option><?}?>									
				<?if ($_POST['add_meeting_stime'] == "21:00") {?><option value="21:00" selected = "selected">09:00 PM</option><?} else {?><option value="21:00">09:00 PM</option><?}?>									
				<?if ($_POST['add_meeting_stime'] == "21:30") {?><option value="21:30" selected = "selected">09:30 PM</option><?} else {?><option value="21:30">09:30 PM</option><?}?>									
				<?if ($_POST['add_meeting_stime'] == "22:00") {?><option value="22:00" selected = "selected">10:00 PM</option><?} else {?><option value="22:00">10:00 PM</option><?}?>									
				<?if ($_POST['add_meeting_stime'] == "22:30") {?><option value="22:30" selected = "selected">10:30 PM</option><?} else {?><option value="22:30">10:30 PM</option><?}?>									
				<?if ($_POST['add_meeting_stime'] == "23:00") {?><option value="23:00" selected = "selected">11:00 PM</option><?} else {?><option value="23:00">11:00 PM</option><?}?>									
				<?if ($_POST['add_meeting_stime'] == "23:30") {?><option value="23:30" selected = "selected">11:30 PM</option><?} else {?><option value="23:30">11:30 PM</option><?}?>									
				<?if ($_POST['add_meeting_stime'] == "00:00") {?><option value="00:00" selected = "selected">12:00 AM</option><?} else {?><option value="00:00">12:00 AM</option><?}?>																											
				</select></td>
			</tr>
			<tr>
				<td><label>Meeting End Time <span style="color:#FF0000">*</span></label></td>				
				<td><select name="add_meeting_etime" id="add_meeting_etime">
				<option value="" selected = "selected">Select Time</option>				
				<?if ($_POST['add_meeting_etime'] == "07:00") {?><option value="07:00" selected = "selected">07:00 AM</option><?} else {?><option value="07:00">07:00 AM</option><?}?>									
				<?if ($_POST['add_meeting_etime'] == "07:30") {?><option value="07:30" selected = "selected">07:30 AM</option><?} else {?><option value="07:30">07:30 AM</option><?}?>									
				<?if ($_POST['add_meeting_etime'] == "08:00") {?><option value="08:00" selected = "selected">08:00 AM</option><?} else {?><option value="08:00">08:00 AM</option><?}?>									
				<?if ($_POST['add_meeting_etime'] == "08:30") {?><option value="08:30" selected = "selected">08:30 AM</option><?} else {?><option value="08:30">08:30 AM</option><?}?>									
				<?if ($_POST['add_meeting_etime'] == "09:00") {?><option value="09:00" selected = "selected">09:00 AM</option><?} else {?><option value="09:00">09:00 AM</option><?}?>									
				<?if ($_POST['add_meeting_etime'] == "09:30") {?><option value="09:30" selected = "selected">09:30 AM</option><?} else {?><option value="09:30">09:30 AM</option><?}?>									
				<?if ($_POST['add_meeting_etime'] == "10:00") {?><option value="10:00" selected = "selected">10:00 AM</option><?} else {?><option value="10:00">10:00 AM</option><?}?>									
				<?if ($_POST['add_meeting_etime'] == "10:30") {?><option value="10:30" selected = "selected">10:30 AM</option><?} else {?><option value="10:30">10:30 AM</option><?}?>									
				<?if ($_POST['add_meeting_etime'] == "11:00") {?><option value="11:00" selected = "selected">11:00 AM</option><?} else {?><option value="11:00">11:00 AM</option><?}?>									
				<?if ($_POST['add_meeting_etime'] == "11:30") {?><option value="11:30" selected = "selected">11:30 AM</option><?} else {?><option value="11:30">11:30 AM</option><?}?>									
				<?if ($_POST['add_meeting_etime'] == "12:00") {?><option value="12:00" selected = "selected">12:00 PM</option><?} else {?><option value="12:00">12:00 PM</option><?}?>									
				<?if ($_POST['add_meeting_etime'] == "12:30") {?><option value="12:30" selected = "selected">12:30 PM</option><?} else {?><option value="12:30">12:30 PM</option><?}?>									
				<?if ($_POST['add_meeting_etime'] == "13:00") {?><option value="13:00" selected = "selected">01:00 PM</option><?} else {?><option value="13:00">01:00 PM</option><?}?>									
				<?if ($_POST['add_meeting_etime'] == "13:30") {?><option value="13:30" selected = "selected">01:30 PM</option><?} else {?><option value="13:30">01:30 PM</option><?}?>									
				<?if ($_POST['add_meeting_etime'] == "14:00") {?><option value="14:00" selected = "selected">02:00 PM</option><?} else {?><option value="14:00">02:00 PM</option><?}?>									
				<?if ($_POST['add_meeting_etime'] == "14:30") {?><option value="14:30" selected = "selected">02:30 PM</option><?} else {?><option value="14:30">02:30 PM</option><?}?>									
				<?if ($_POST['add_meeting_etime'] == "15:00") {?><option value="15:00" selected = "selected">03:00 PM</option><?} else {?><option value="15:00">03:00 PM</option><?}?>									
				<?if ($_POST['add_meeting_etime'] == "15:30") {?><option value="15:30" selected = "selected">03:30 PM</option><?} else {?><option value="15:30">03:30 PM</option><?}?>									
				<?if ($_POST['add_meeting_etime'] == "16:00") {?><option value="16:00" selected = "selected">04:00 PM</option><?} else {?><option value="16:00">04:00 PM</option><?}?>									
				<?if ($_POST['add_meeting_etime'] == "16:30") {?><option value="16:30" selected = "selected">04:30 PM</option><?} else {?><option value="16:30">04:30 PM</option><?}?>									
				<?if ($_POST['add_meeting_etime'] == "17:00") {?><option value="17:00" selected = "selected">05:00 PM</option><?} else {?><option value="17:00">05:00 PM</option><?}?>									
				<?if ($_POST['add_meeting_etime'] == "17:30") {?><option value="17:30" selected = "selected">05:30 PM</option><?} else {?><option value="17:30">05:30 PM</option><?}?>									
				<?if ($_POST['add_meeting_etime'] == "18:00") {?><option value="18:00" selected = "selected">06:00 PM</option><?} else {?><option value="18:00">06:00 PM</option><?}?>									
				<?if ($_POST['add_meeting_etime'] == "18:30") {?><option value="18:30" selected = "selected">06:30 PM</option><?} else {?><option value="18:30">06:30 PM</option><?}?>									
				<?if ($_POST['add_meeting_etime'] == "19:00") {?><option value="19:00" selected = "selected">07:00 PM</option><?} else {?><option value="19:00">07:00 PM</option><?}?>									
				<?if ($_POST['add_meeting_etime'] == "19:30") {?><option value="19:30" selected = "selected">07:30 PM</option><?} else {?><option value="19:30">07:30 PM</option><?}?>									
				<?if ($_POST['add_meeting_etime'] == "20:00") {?><option value="20:00" selected = "selected">08:00 PM</option><?} else {?><option value="20:00">08:00 PM</option><?}?>									
				<?if ($_POST['add_meeting_etime'] == "20:30") {?><option value="20:30" selected = "selected">08:30 PM</option><?} else {?><option value="20:30">08:30 PM</option><?}?>									
				<?if ($_POST['add_meeting_etime'] == "21:00") {?><option value="21:00" selected = "selected">09:00 PM</option><?} else {?><option value="21:00">09:00 PM</option><?}?>									
				<?if ($_POST['add_meeting_etime'] == "21:30") {?><option value="21:30" selected = "selected">09:30 PM</option><?} else {?><option value="21:30">09:30 PM</option><?}?>									
				<?if ($_POST['add_meeting_etime'] == "22:00") {?><option value="22:00" selected = "selected">10:00 PM</option><?} else {?><option value="22:00">10:00 PM</option><?}?>									
				<?if ($_POST['add_meeting_etime'] == "22:30") {?><option value="22:30" selected = "selected">10:30 PM</option><?} else {?><option value="22:30">10:30 PM</option><?}?>									
				<?if ($_POST['add_meeting_etime'] == "23:00") {?><option value="23:00" selected = "selected">11:00 PM</option><?} else {?><option value="23:00">11:00 PM</option><?}?>									
				<?if ($_POST['add_meeting_etime'] == "23:30") {?><option value="23:30" selected = "selected">11:30 PM</option><?} else {?><option value="23:30">11:30 PM</option><?}?>									
				<?if ($_POST['add_meeting_etime'] == "00:00") {?><option value="00:00" selected = "selected">12:00 AM</option><?} else {?><option value="00:00">12:00 AM</option><?}?>																											
				</select></td>
			</tr>	
			<?
			$sql = "SELECT id, description
			FROM ref_meeting_mode
			WHERE status = 'A'
			ORDER BY seq";

			$dba->query($sql);
			$dba->next_record();	
			?>
			<tr>
				<td><label>Meeting Mode <span style="color:#FF0000">*</span></label></td>				
				<td><select name="add_meeting_mode" id="add_meeting_mode">
				<?
				if ($add_meeting_mode == "") $add_meeting_mode = $_POST['add_meeting_mode'];
				
				if ($add_meeting_mode=="") {?><option value="" selected="selected"></option><?} else {?><option value=""></option><?}				
				do {
					$meetingModeId = $dba->f('id');
					$meetingModeDesc = $dba->f('description');
					if ($add_meeting_mode==$meetingModeId) {?><option value="<?=$meetingModeId?>" selected="selected"><?=$meetingModeDesc?></option><?} else {?><option value="<?=$meetingModeId?>"><?=$meetingModeDesc?></option><?}
				} while ($dba->next_record());?>				
																															
				</select></td>
			</tr>			
		</table>
		<table>
			<tr>
				<td><input type="submit" name="btnAdd" value="Add" /></td>
			</tr>
		</table>

		</p><?php ?>
		<table>
		<tr>							
			<td>Searching Results:- <?=$row_cnt?> record(s) found.</td></td>
		</tr>
		</table>
		<table border="1" cellpadding="3" cellspacing="3" width="75%" id="inputs10" class="thetable">
			  <tr align="left">
				<th align="center" width="5%"><label>Tick</label></td>
				<th align="center" width="5%"><label>No</label></td>
				<th align="center" width="20%"><label>Date <span style="color:#FF0000">*</span></label></th>
				<th align="center" width="15%"><label>Start Time <span style="color:#FF0000">*</span></label></th>
				<th align="center" width="15%"><label>End Time <span style="color:#FF0000">*</span></label></th>
				<th align="center" width="15%"><label>Meeting Mode<span style="color:#FF0000">*</span></label></th>
			  </tr>

			<?php
			if ($row_cnt > 0) {
				
				$tmp_no = 0;
				while($row = mysql_fetch_array($result)) 					
				{ 
					?><tr>
							<td align="center"><input type="checkbox" name="progress_meeting_checkbox[]" id="progress_meeting_checkbox" value="<?=$tmp_no;?>" /></td>
							<td align="center"><label><?=$tmp_no+1;?>.</label></td>
							<td align="center"><input type="text" name="meeting_date[]" id="meeting_date<?=$tmp_no;?>" value="<?=$row["meeting_date"];?>" readonly=""></input></td>
							<?	$jscript .= "\n" . '$( "#meeting_date' . $tmp_no . '" ).datepicker({
												changeMonth: true,
												changeYear: true,
												yearRange: \'-100:+0\',
												dateFormat: \'dd-M-yy\'
											});';
					 
				?>
							<input type="hidden" name="progress_meeting_id[]" id="progress_meeting_id" value="<?=$row['id'];?>" />
							<td align="center"">
							<select name="meeting_stime[]" id="meeting_stime" size="1" >
							<?if ($row['meeting_stime'] == "07:00") {?><option value="07:00" selected = "selected">07:00 AM</option><?} else {?><option value="07:00">07:00 AM</option><?}?>									
							<?if ($row['meeting_stime'] == "07:30") {?><option value="07:30" selected = "selected">07:30 AM</option><?} else {?><option value="07:30">07:30 AM</option><?}?>									
							<?if ($row['meeting_stime'] == "08:00") {?><option value="08:00" selected = "selected">08:00 AM</option><?} else {?><option value="08:00">08:00 AM</option><?}?>									
							<?if ($row['meeting_stime'] == "08:30") {?><option value="08:30" selected = "selected">08:30 AM</option><?} else {?><option value="08:30">08:30 AM</option><?}?>									
							<?if ($row['meeting_stime'] == "09:00") {?><option value="09:00" selected = "selected">09:00 AM</option><?} else {?><option value="09:00">09:00 AM</option><?}?>									
							<?if ($row['meeting_stime'] == "09:30") {?><option value="09:30" selected = "selected">09:30 AM</option><?} else {?><option value="09:30">09:30 AM</option><?}?>									
							<?if ($row['meeting_stime'] == "10:00") {?><option value="10:00" selected = "selected">10:00 AM</option><?} else {?><option value="10:00">10:00 AM</option><?}?>									
							<?if ($row['meeting_stime'] == "10:30") {?><option value="10:30" selected = "selected">10:30 AM</option><?} else {?><option value="10:30">10:30 AM</option><?}?>									
							<?if ($row['meeting_stime'] == "11:00") {?><option value="11:00" selected = "selected">11:00 AM</option><?} else {?><option value="11:00">11:00 AM</option><?}?>									
							<?if ($row['meeting_stime'] == "11:30") {?><option value="11:30" selected = "selected">11:30 AM</option><?} else {?><option value="11:30">11:30 AM</option><?}?>									
							<?if ($row['meeting_stime'] == "12:00") {?><option value="12:00" selected = "selected">12:00 PM</option><?} else {?><option value="12:00">12:00 PM</option><?}?>									
							<?if ($row['meeting_stime'] == "12:30") {?><option value="12:30" selected = "selected">12:30 PM</option><?} else {?><option value="12:30">12:30 PM</option><?}?>									
							<?if ($row['meeting_stime'] == "13:00") {?><option value="13:00" selected = "selected">01:00 PM</option><?} else {?><option value="13:00">01:00 PM</option><?}?>									
							<?if ($row['meeting_stime'] == "13:30") {?><option value="13:30" selected = "selected">01:30 PM</option><?} else {?><option value="13:30">01:30 PM</option><?}?>									
							<?if ($row['meeting_stime'] == "14:00") {?><option value="14:00" selected = "selected">02:00 PM</option><?} else {?><option value="14:00">02:00 PM</option><?}?>									
							<?if ($row['meeting_stime'] == "14:30") {?><option value="14:30" selected = "selected">02:30 PM</option><?} else {?><option value="14:30">02:30 PM</option><?}?>									
							<?if ($row['meeting_stime'] == "15:00") {?><option value="15:00" selected = "selected">03:00 PM</option><?} else {?><option value="15:00">03:00 PM</option><?}?>									
							<?if ($row['meeting_stime'] == "15:30") {?><option value="15:30" selected = "selected">03:30 PM</option><?} else {?><option value="15:30">03:30 PM</option><?}?>									
							<?if ($row['meeting_stime'] == "16:00") {?><option value="16:00" selected = "selected">04:00 PM</option><?} else {?><option value="16:00">04:00 PM</option><?}?>									
							<?if ($row['meeting_stime'] == "16:30") {?><option value="16:30" selected = "selected">04:30 PM</option><?} else {?><option value="16:30">04:30 PM</option><?}?>									
							<?if ($row['meeting_stime'] == "17:00") {?><option value="17:00" selected = "selected">05:00 PM</option><?} else {?><option value="17:00">05:00 PM</option><?}?>									
							<?if ($row['meeting_stime'] == "17:30") {?><option value="17:30" selected = "selected">05:30 PM</option><?} else {?><option value="17:30">05:30 PM</option><?}?>									
							<?if ($row['meeting_stime'] == "18:00") {?><option value="18:00" selected = "selected">06:00 PM</option><?} else {?><option value="18:00">06:00 PM</option><?}?>									
							<?if ($row['meeting_stime'] == "18:30") {?><option value="18:30" selected = "selected">06:30 PM</option><?} else {?><option value="18:30">06:30 PM</option><?}?>									
							<?if ($row['meeting_stime'] == "19:00") {?><option value="19:00" selected = "selected">07:00 PM</option><?} else {?><option value="19:00">07:00 PM</option><?}?>									
							<?if ($row['meeting_stime'] == "19:30") {?><option value="19:30" selected = "selected">07:30 PM</option><?} else {?><option value="19:30">07:30 PM</option><?}?>									
							<?if ($row['meeting_stime'] == "20:00") {?><option value="20:00" selected = "selected">08:00 PM</option><?} else {?><option value="20:00">08:00 PM</option><?}?>									
							<?if ($row['meeting_stime'] == "20:30") {?><option value="20:30" selected = "selected">08:30 PM</option><?} else {?><option value="20:30">08:30 PM</option><?}?>									
							<?if ($row['meeting_stime'] == "21:00") {?><option value="21:00" selected = "selected">09:00 PM</option><?} else {?><option value="21:00">09:00 PM</option><?}?>									
							<?if ($row['meeting_stime'] == "21:30") {?><option value="21:30" selected = "selected">09:30 PM</option><?} else {?><option value="21:30">09:30 PM</option><?}?>									
							<?if ($row['meeting_stime'] == "22:00") {?><option value="22:00" selected = "selected">10:00 PM</option><?} else {?><option value="22:00">10:00 PM</option><?}?>									
							<?if ($row['meeting_stime'] == "22:30") {?><option value="22:30" selected = "selected">10:30 PM</option><?} else {?><option value="22:30">10:30 PM</option><?}?>									
							<?if ($row['meeting_stime'] == "23:00") {?><option value="23:00" selected = "selected">11:00 PM</option><?} else {?><option value="23:00">11:00 PM</option><?}?>									
							<?if ($row['meeting_stime'] == "23:30") {?><option value="23:30" selected = "selected">11:30 PM</option><?} else {?><option value="23:30">11:30 PM</option><?}?>									
							<?if ($row['meeting_stime'] == "00:00") {?><option value="00:00" selected = "selected">12:00 AM</option><?} else {?><option value="00:00">12:00 AM</option><?}?>																											
							</select></td>
							<td align="center"">
							<select name="meeting_etime[]" id="meeting_etime" size="1" >
							<?if ($row['meeting_etime'] == "07:00") {?><option value="07:00" selected = "selected">07:00 AM</option><?} else {?><option value="07:00">07:00 AM</option><?}?>									
							<?if ($row['meeting_etime'] == "07:30") {?><option value="07:30" selected = "selected">07:30 AM</option><?} else {?><option value="07:30">07:30 AM</option><?}?>									
							<?if ($row['meeting_etime'] == "08:00") {?><option value="08:00" selected = "selected">08:00 AM</option><?} else {?><option value="08:00">08:00 AM</option><?}?>									
							<?if ($row['meeting_etime'] == "08:30") {?><option value="08:30" selected = "selected">08:30 AM</option><?} else {?><option value="08:30">08:30 AM</option><?}?>									
							<?if ($row['meeting_etime'] == "09:00") {?><option value="09:00" selected = "selected">09:00 AM</option><?} else {?><option value="09:00">09:00 AM</option><?}?>									
							<?if ($row['meeting_etime'] == "09:30") {?><option value="09:30" selected = "selected">09:30 AM</option><?} else {?><option value="09:30">09:30 AM</option><?}?>									
							<?if ($row['meeting_etime'] == "10:00") {?><option value="10:00" selected = "selected">10:00 AM</option><?} else {?><option value="10:00">10:00 AM</option><?}?>									
							<?if ($row['meeting_etime'] == "10:30") {?><option value="10:30" selected = "selected">10:30 AM</option><?} else {?><option value="10:30">10:30 AM</option><?}?>									
							<?if ($row['meeting_etime'] == "11:00") {?><option value="11:00" selected = "selected">11:00 AM</option><?} else {?><option value="11:00">11:00 AM</option><?}?>									
							<?if ($row['meeting_etime'] == "11:30") {?><option value="11:30" selected = "selected">11:30 AM</option><?} else {?><option value="11:30">11:30 AM</option><?}?>									
							<?if ($row['meeting_etime'] == "12:00") {?><option value="12:00" selected = "selected">12:00 PM</option><?} else {?><option value="12:00">12:00 PM</option><?}?>									
							<?if ($row['meeting_etime'] == "12:30") {?><option value="12:30" selected = "selected">12:30 PM</option><?} else {?><option value="12:30">12:30 PM</option><?}?>									
							<?if ($row['meeting_etime'] == "13:00") {?><option value="13:00" selected = "selected">01:00 PM</option><?} else {?><option value="13:00">01:00 PM</option><?}?>									
							<?if ($row['meeting_etime'] == "13:30") {?><option value="13:30" selected = "selected">01:30 PM</option><?} else {?><option value="13:30">01:30 PM</option><?}?>									
							<?if ($row['meeting_etime'] == "14:00") {?><option value="14:00" selected = "selected">02:00 PM</option><?} else {?><option value="14:00">02:00 PM</option><?}?>									
							<?if ($row['meeting_etime'] == "14:30") {?><option value="14:30" selected = "selected">02:30 PM</option><?} else {?><option value="14:30">02:30 PM</option><?}?>									
							<?if ($row['meeting_etime'] == "15:00") {?><option value="15:00" selected = "selected">03:00 PM</option><?} else {?><option value="15:00">03:00 PM</option><?}?>									
							<?if ($row['meeting_etime'] == "15:30") {?><option value="15:30" selected = "selected">03:30 PM</option><?} else {?><option value="15:30">03:30 PM</option><?}?>									
							<?if ($row['meeting_etime'] == "16:00") {?><option value="16:00" selected = "selected">04:00 PM</option><?} else {?><option value="16:00">04:00 PM</option><?}?>									
							<?if ($row['meeting_etime'] == "16:30") {?><option value="16:30" selected = "selected">04:30 PM</option><?} else {?><option value="16:30">04:30 PM</option><?}?>									
							<?if ($row['meeting_etime'] == "17:00") {?><option value="17:00" selected = "selected">05:00 PM</option><?} else {?><option value="17:00">05:00 PM</option><?}?>									
							<?if ($row['meeting_etime'] == "17:30") {?><option value="17:30" selected = "selected">05:30 PM</option><?} else {?><option value="17:30">05:30 PM</option><?}?>									
							<?if ($row['meeting_etime'] == "18:00") {?><option value="18:00" selected = "selected">06:00 PM</option><?} else {?><option value="18:00">06:00 PM</option><?}?>									
							<?if ($row['meeting_etime'] == "18:30") {?><option value="18:30" selected = "selected">06:30 PM</option><?} else {?><option value="18:30">06:30 PM</option><?}?>									
							<?if ($row['meeting_etime'] == "19:00") {?><option value="19:00" selected = "selected">07:00 PM</option><?} else {?><option value="19:00">07:00 PM</option><?}?>									
							<?if ($row['meeting_etime'] == "19:30") {?><option value="19:30" selected = "selected">07:30 PM</option><?} else {?><option value="19:30">07:30 PM</option><?}?>									
							<?if ($row['meeting_etime'] == "20:00") {?><option value="20:00" selected = "selected">08:00 PM</option><?} else {?><option value="20:00">08:00 PM</option><?}?>									
							<?if ($row['meeting_etime'] == "20:30") {?><option value="20:30" selected = "selected">08:30 PM</option><?} else {?><option value="20:30">08:30 PM</option><?}?>									
							<?if ($row['meeting_etime'] == "21:00") {?><option value="21:00" selected = "selected">09:00 PM</option><?} else {?><option value="21:00">09:00 PM</option><?}?>									
							<?if ($row['meeting_etime'] == "21:30") {?><option value="21:30" selected = "selected">09:30 PM</option><?} else {?><option value="21:30">09:30 PM</option><?}?>									
							<?if ($row['meeting_etime'] == "22:00") {?><option value="22:00" selected = "selected">10:00 PM</option><?} else {?><option value="22:00">10:00 PM</option><?}?>									
							<?if ($row['meeting_etime'] == "22:30") {?><option value="22:30" selected = "selected">10:30 PM</option><?} else {?><option value="22:30">10:30 PM</option><?}?>									
							<?if ($row['meeting_etime'] == "23:00") {?><option value="23:00" selected = "selected">11:00 PM</option><?} else {?><option value="23:00">11:00 PM</option><?}?>									
							<?if ($row['meeting_etime'] == "23:30") {?><option value="23:30" selected = "selected">11:30 PM</option><?} else {?><option value="23:30">11:30 PM</option><?}?>									
							<?if ($row['meeting_etime'] == "00:00") {?><option value="00:00" selected = "selected">12:00 AM</option><?} else {?><option value="00:00">12:00 AM</option><?}?>																											
							</select></td>
							<?
							
							$sql = "SELECT id, description
							FROM ref_meeting_mode
							WHERE status = 'A'
							ORDER BY seq";

							$result_sql = $dbb->query($sql);
							$dbb->next_record();
							$row_cnt = mysql_num_rows($result_sql);?>
							
							<td align="center">
							<select name="meeting_mode[]" id="meeting_mode">
							<?do {
								$myMeetingModeId = $dbb->f('id');
								$myMeetingModeDesc = $dbb->f('description');
								if ($row['meeting_mode']==$myMeetingModeId) {?><option value="<?=$myMeetingModeId?>" selected="selected"><?=$myMeetingModeDesc?></option><?} else {?><option value="<?=$myMeetingModeId?>"><?=$myMeetingModeDesc?></option><?}
							} while ($dbb->next_record());?>								
							</select></td>
						</tr>
					<?
					$tmp_no++;}
			}
			else {
				?>
				<table>
					<tr>
						<td><label>No record found!</label></td>
					</tr>
				</table>
				<?
			}?> 			
		</table>
		<br/>
		<table>
			<tr>
				<td><span style="color:#FF0000">Notes:</span></td>
			</tr>
			<tr>
				<td>1. Field marks with (<span style="color:#FF0000">*</span>) is compulsory.</td>
			</tr>
			<tr>
				<td>2. Please tick the checkbox before click Update or Delete button.</td>
			</tr>
		</table>
	</fieldset>
	<table>
		<tr>
			<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../monthlyreport/edit_progress.php?tid=<?=$thesis_id?>&pid=<?=$proposal_id?>&ref=<?=$referenceNo?>';" /></input></td>		
			<td><input type="submit" name="btnUpdate" value="Update" /></input></td>
			<td><input type="submit" name="btnDelete" onClick="return respConfirm()" value="Delete" /></input></td>
		</tr>
	</table>

  </form>
  	<script>
		<?=$jscript;?>
	</script>
</body>
</html>





