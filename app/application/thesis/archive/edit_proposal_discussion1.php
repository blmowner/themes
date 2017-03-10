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
$studentMatrixNo=$_REQUEST['uid'];
$pgThesisId=$_REQUEST['tid'];
$pgProposalId=$_GET['pid'];

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
	if (empty($_POST['add_meeting_date'])) $msg[] = "<div class=\"error\"><span>Please select  Meeting Date</span></div>";
	if (empty($_POST['add_meeting_time'])) $msg[] = "<div class=\"error\"><span>Please select Meeting Time</span></div>";
	if (empty($_POST['add_lecturer_name'])) $msg[] = "<div class=\"error\"><span>Please enter Lecturer Name</span></div>";
	if(empty($msg)) 
	{
		$add_meeting_date = $_POST['add_meeting_date'];
		$add_meeting_time = $_POST['add_meeting_time'];
		$add_lecturer_name = $_POST['add_lecturer_name'];
		$add_remark = $_POST['add_remark'];
		$curdatetime = date("Y-m-d H:i:s");
		
		$meeting_detail_id = runnum2('id','pg_meeting_detail');	

		$myMeetingDate = $_POST['add_meeting_date']." ".$_POST['add_meeting_time'];
		$myLecturer = mysql_real_escape_string($_POST['add_lecturer_name']);
		$myRemarks = mysql_real_escape_string($_POST['add_remark']);
		$sqlMeeting = "INSERT INTO pg_meeting_detail(
						id, student_matrix_no,
						lecturer_name,
						meeting_sdate,
						remark,
						pg_proposal_id,
						pg_thesis_progress_id,
						insert_by,
						insert_date,
						modify_by,
						modify_date)
						VALUES (
						'$meeting_detail_id', '$user_id',
						'$myLecturer',
						STR_TO_DATE('$myMeetingDate','%d-%M-%Y %H:%i'), 
						'$myRemarks',
						'$pgProposalId',
						null,
						'$user_id',
						'$curdatetime',
						'$user_id',
						'$curdatetime')";
		
		$db_klas2->query($sqlMeeting); 
		
		$msg[] = "<div class=\"success\"><span>Discussion detail has been added successfully.</span></div>";
	}
}

if(isset($_POST['btnUpdate']) && ($_POST['btnUpdate'] <> ""))
{					
	if (sizeof($_POST['meeting_detail_checkbox'])>0) {
		$curdatetime = date("Y-m-d H:i:s");
		while (list ($key,$val) = @each ($_POST['meeting_detail_checkbox'])) 
		{
			$meeting_detail_id = $_POST['meeting_detail_id'][$val];
			$meeting_date = $_POST['meeting_date'][$val];
			$meeting_time = $_POST['meeting_time'][$val];
			$lecturer_name = $_POST['lecturer_name'][$val];
			$remark = $_POST['remark'][$val];
			$myMeetingDate = $_POST['meeting_date'][$key]." ".$_POST['meeting_time'][$key];
			
			$sql1 = "UPDATE pg_meeting_detail
			SET meeting_sdate = STR_TO_DATE('$myMeetingDate','%d-%b-%Y %H:%i'), lecturer_name = '$lecturer_name', remark = '$remark', 
			modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE id = '$meeting_detail_id'
			AND student_matrix_no = '$user_id'";
			
			$dba->query($sql1); 
			
			
		}
		$msg[] = "<div class=\"success\"><span>Discussion detail has been updated successfully.</span></div>";
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please tick the discussion record for update!</span></div>";
	}
}

if(isset($_POST['btnDelete']) && ($_POST['btnDelete'] <> ""))
{					
	if (sizeof($_POST['meeting_detail_checkbox'])>0) {
		$curdatetime = date("Y-m-d H:i:s");
		while (list ($key,$val) = @each ($_POST['meeting_detail_checkbox'])) 
		{
			$meeting_detail_id = $_POST['meeting_detail_id'][$val];
			$meeting_date = $_POST['meeting_date'][$val];
			$meeting_time = $_POST['meeting_time'][$val];
			$lecturer_name = $_POST['lecturer_name'][$val];
			$remark = $_POST['remark'][$val];
			$myMeetingDate = $_POST['meeting_date'][$key]." ".$_POST['meeting_time'][$key];
			
			$sql1 = "DELETE FROM pg_meeting_detail
			WHERE id = '$meeting_detail_id'
			AND student_matrix_no = '$user_id'";
			
			$dba->query($sql1); 
			
			
		}
		$msg[] = "<div class=\"success\"><span>Discussion detail has been deleted successfully.</span></div>";
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please tick the discussion record for deletion!</span></div>";
	}
}

		
		
$sqlMeeting="SELECT pmd.id,pmd.lecturer_name, DATE_FORMAT(pmd.meeting_sdate,'%d-%b-%Y') as date,
DATE_FORMAT(pmd.meeting_sdate,'%H:%i') as time, pmd.remark, pmd.insert_by, pmd.insert_date 
FROM  pg_meeting_detail pmd  
WHERE pmd.pg_proposal_id='$pgProposalId'
AND student_matrix_no = '$user_id' 
ORDER BY pmd.meeting_sdate DESC ";			

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
				<td><input name="add_meeting_date" type="text" id="add_meeting_date" maxlength="50" value="<?=$_POST['add_meeting_date']?>"></td>
				<?	$jscript .= "\n" . '$( "#add_meeting_date' . $no . '" ).datepicker({
												changeMonth: true,
												changeYear: true,
												yearRange: \'-100:+0\',
												dateFormat: \'dd-M-yy\'
											});';
					 
				?>
			</tr>
			<tr>
				<td><label>Meeting Time <span style="color:#FF0000">*</span></label></td>
				<td><select name="add_meeting_time" id="add_meeting_time">
				<option value="" selected = "selected">Select Time</option>				
				<?if ($_POST['add_meeting_time'] == "07:00") {?><option value="07:00" selected = "selected">07:00 AM</option><?} else {?><option value="07:00">07:00 AM</option><?}?>									
				<?if ($_POST['add_meeting_time'] == "07:30") {?><option value="07:30" selected = "selected">07:30 AM</option><?} else {?><option value="07:30">07:30 AM</option><?}?>									
				<?if ($_POST['add_meeting_time'] == "08:00") {?><option value="08:00" selected = "selected">08:00 AM</option><?} else {?><option value="08:00">08:00 AM</option><?}?>									
				<?if ($_POST['add_meeting_time'] == "08:30") {?><option value="08:30" selected = "selected">08:30 AM</option><?} else {?><option value="08:30">08:30 AM</option><?}?>									
				<?if ($_POST['add_meeting_time'] == "09:00") {?><option value="09:00" selected = "selected">09:00 AM</option><?} else {?><option value="09:00">09:00 AM</option><?}?>									
				<?if ($_POST['add_meeting_time'] == "09:30") {?><option value="09:30" selected = "selected">09:30 AM</option><?} else {?><option value="09:30">09:30 AM</option><?}?>									
				<?if ($_POST['add_meeting_time'] == "10:00") {?><option value="10:00" selected = "selected">10:00 AM</option><?} else {?><option value="10:00">10:00 AM</option><?}?>									
				<?if ($_POST['add_meeting_time'] == "10:30") {?><option value="10:30" selected = "selected">10:30 AM</option><?} else {?><option value="10:30">10:30 AM</option><?}?>									
				<?if ($_POST['add_meeting_time'] == "11:00") {?><option value="11:00" selected = "selected">11:00 AM</option><?} else {?><option value="11:00">11:00 AM</option><?}?>									
				<?if ($_POST['add_meeting_time'] == "11:30") {?><option value="11:30" selected = "selected">11:30 AM</option><?} else {?><option value="11:30">11:30 AM</option><?}?>									
				<?if ($_POST['add_meeting_time'] == "12:00") {?><option value="12:00" selected = "selected">12:00 PM</option><?} else {?><option value="12:00">12:00 PM</option><?}?>									
				<?if ($_POST['add_meeting_time'] == "12:30") {?><option value="12:30" selected = "selected">12:30 PM</option><?} else {?><option value="12:30">12:30 PM</option><?}?>									
				<?if ($_POST['add_meeting_time'] == "13:00") {?><option value="13:00" selected = "selected">01:00 PM</option><?} else {?><option value="13:00">01:00 PM</option><?}?>									
				<?if ($_POST['add_meeting_time'] == "13:30") {?><option value="13:30" selected = "selected">01:30 PM</option><?} else {?><option value="13:30">01:30 PM</option><?}?>									
				<?if ($_POST['add_meeting_time'] == "14:00") {?><option value="14:00" selected = "selected">02:00 PM</option><?} else {?><option value="14:00">02:00 PM</option><?}?>									
				<?if ($_POST['add_meeting_time'] == "14:30") {?><option value="14:30" selected = "selected">02:30 PM</option><?} else {?><option value="14:30">02:30 PM</option><?}?>									
				<?if ($_POST['add_meeting_time'] == "15:00") {?><option value="15:00" selected = "selected">03:00 PM</option><?} else {?><option value="15:00">03:00 PM</option><?}?>									
				<?if ($_POST['add_meeting_time'] == "15:30") {?><option value="15:30" selected = "selected">03:30 PM</option><?} else {?><option value="15:30">03:30 PM</option><?}?>									
				<?if ($_POST['add_meeting_time'] == "16:00") {?><option value="16:00" selected = "selected">04:00 PM</option><?} else {?><option value="16:00">04:00 PM</option><?}?>									
				<?if ($_POST['add_meeting_time'] == "16:30") {?><option value="16:30" selected = "selected">04:30 PM</option><?} else {?><option value="16:30">04:30 PM</option><?}?>									
				<?if ($_POST['add_meeting_time'] == "17:00") {?><option value="17:00" selected = "selected">05:00 PM</option><?} else {?><option value="17:00">05:00 PM</option><?}?>									
				<?if ($_POST['add_meeting_time'] == "17:30") {?><option value="17:30" selected = "selected">05:30 PM</option><?} else {?><option value="17:30">05:30 PM</option><?}?>									
				<?if ($_POST['add_meeting_time'] == "18:00") {?><option value="18:00" selected = "selected">06:00 PM</option><?} else {?><option value="18:00">06:00 PM</option><?}?>									
				<?if ($_POST['add_meeting_time'] == "18:30") {?><option value="18:30" selected = "selected">06:30 PM</option><?} else {?><option value="18:30">06:30 PM</option><?}?>									
				<?if ($_POST['add_meeting_time'] == "19:00") {?><option value="19:00" selected = "selected">07:00 PM</option><?} else {?><option value="19:00">07:00 PM</option><?}?>									
				<?if ($_POST['add_meeting_time'] == "19:30") {?><option value="19:30" selected = "selected">07:30 PM</option><?} else {?><option value="19:30">07:30 PM</option><?}?>									
				<?if ($_POST['add_meeting_time'] == "20:00") {?><option value="20:00" selected = "selected">08:00 PM</option><?} else {?><option value="20:00">08:00 PM</option><?}?>									
				<?if ($_POST['add_meeting_time'] == "20:30") {?><option value="20:30" selected = "selected">08:30 PM</option><?} else {?><option value="20:30">08:30 PM</option><?}?>									
				<?if ($_POST['add_meeting_time'] == "21:00") {?><option value="21:00" selected = "selected">09:00 PM</option><?} else {?><option value="21:00">09:00 PM</option><?}?>									
				<?if ($_POST['add_meeting_time'] == "21:30") {?><option value="21:30" selected = "selected">09:30 PM</option><?} else {?><option value="21:30">09:30 PM</option><?}?>									
				<?if ($_POST['add_meeting_time'] == "22:00") {?><option value="22:00" selected = "selected">10:00 PM</option><?} else {?><option value="22:00">10:00 PM</option><?}?>									
				<?if ($_POST['add_meeting_time'] == "22:30") {?><option value="22:30" selected = "selected">10:30 PM</option><?} else {?><option value="22:30">10:30 PM</option><?}?>									
				<?if ($_POST['add_meeting_time'] == "23:00") {?><option value="23:00" selected = "selected">11:00 PM</option><?} else {?><option value="23:00">11:00 PM</option><?}?>									
				<?if ($_POST['add_meeting_time'] == "23:30") {?><option value="23:30" selected = "selected">11:30 PM</option><?} else {?><option value="23:30">11:30 PM</option><?}?>									
				<?if ($_POST['add_meeting_time'] == "00:00") {?><option value="00:00" selected = "selected">12:00 AM</option><?} else {?><option value="00:00">12:00 AM</option><?}?>																											
				</select></td>
			</tr>
			<tr>
				<td><label>Lecturer Name <span style="color:#FF0000">*</span></label></td>
				<td><input name="add_lecturer_name" type="text" id="add_lecturer_name" size="50" value="<?=$_POST['add_lecturer_name']?>"></td>
			</tr>
			<tr>
				<td><label>Notes</label></td>
				<td><textarea name="add_remark" cols="50" id="add_remark"><?=$_POST['add_remark']?></textarea></td>
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
			<td><strong>Searching Results:-</strong> <?=$row_cnt?> record(s) found.</td>
		</tr>
		</table>
		<table border="1" cellpadding="3" cellspacing="3" width="100%" id="inputs10" class="thetable">
			  <tr align="left">
				<th align="center"><label>Tick</label></td>
				<th align="center"><label>No</label></td>
				<th align="center"><label>Date <span style="color:#FF0000">*</span></label></th>
				<th align="center"><label>Time <span style="color:#FF0000">*</span></label></th>
				<th >Lecturer Name <span style="color:#FF0000">*</span></th>
				<th >Notes</th>
			  </tr>

			<?php
			if ($row_cnt > 0) {
				
				$tmp_no = 0;
				while($row = mysql_fetch_array($result)) 					
				{ 
					?><tr>
							<td align="center" width="30"><input type="checkbox" name="meeting_detail_checkbox[]" id="meeting_detail_checkbox" value="<?=$tmp_no;?>" /></td>
							<td align="center"><label><?=$tmp_no+1;?>.</label></td>
							<td align="center" width="116"><input type="text" name="meeting_date[]" id="meeting_date<?=$tmp_no;?>" value="<?=$row["date"];?>"></input></td>
							<?	$jscript .= "\n" . '$( "#meeting_date' . $tmp_no . '" ).datepicker({
												changeMonth: true,
												changeYear: true,
												yearRange: \'-100:+0\',
												dateFormat: \'dd-M-yy\'
											});';
					 
				?>
							<input type="hidden" name="meeting_detail_id[]" id="meeting_detail_id" value="<?=$row['id'];?>" />
							<td align="center"">
							<select name="meeting_time[]" id="meeting_time" size="1" >
							<?if ($row['time'] == "07:00") {?><option value="07:00" selected = "selected">07:00 AM</option><?} else {?><option value="07:00">07:00 AM</option><?}?>									
							<?if ($row['time'] == "07:30") {?><option value="07:30" selected = "selected">07:30 AM</option><?} else {?><option value="07:30">07:30 AM</option><?}?>									
							<?if ($row['time'] == "08:00") {?><option value="08:00" selected = "selected">08:00 AM</option><?} else {?><option value="08:00">08:00 AM</option><?}?>									
							<?if ($row['time'] == "08:30") {?><option value="08:30" selected = "selected">08:30 AM</option><?} else {?><option value="08:30">08:30 AM</option><?}?>									
							<?if ($row['time'] == "09:00") {?><option value="09:00" selected = "selected">09:00 AM</option><?} else {?><option value="09:00">09:00 AM</option><?}?>									
							<?if ($row['time'] == "09:30") {?><option value="09:30" selected = "selected">09:30 AM</option><?} else {?><option value="09:30">09:30 AM</option><?}?>									
							<?if ($row['time'] == "10:00") {?><option value="10:00" selected = "selected">10:00 AM</option><?} else {?><option value="10:00">10:00 AM</option><?}?>									
							<?if ($row['time'] == "10:30") {?><option value="10:30" selected = "selected">10:30 AM</option><?} else {?><option value="10:30">10:30 AM</option><?}?>									
							<?if ($row['time'] == "11:00") {?><option value="11:00" selected = "selected">11:00 AM</option><?} else {?><option value="11:00">11:00 AM</option><?}?>									
							<?if ($row['time'] == "11:30") {?><option value="11:30" selected = "selected">11:30 AM</option><?} else {?><option value="11:30">11:30 AM</option><?}?>									
							<?if ($row['time'] == "12:00") {?><option value="12:00" selected = "selected">12:00 PM</option><?} else {?><option value="12:00">12:00 PM</option><?}?>									
							<?if ($row['time'] == "12:30") {?><option value="12:30" selected = "selected">12:30 PM</option><?} else {?><option value="12:30">12:30 PM</option><?}?>									
							<?if ($row['time'] == "13:00") {?><option value="13:00" selected = "selected">01:00 PM</option><?} else {?><option value="13:00">01:00 PM</option><?}?>									
							<?if ($row['time'] == "13:30") {?><option value="13:30" selected = "selected">01:30 PM</option><?} else {?><option value="13:30">01:30 PM</option><?}?>									
							<?if ($row['time'] == "14:00") {?><option value="14:00" selected = "selected">02:00 PM</option><?} else {?><option value="14:00">02:00 PM</option><?}?>									
							<?if ($row['time'] == "14:30") {?><option value="14:30" selected = "selected">02:30 PM</option><?} else {?><option value="14:30">02:30 PM</option><?}?>									
							<?if ($row['time'] == "15:00") {?><option value="15:00" selected = "selected">03:00 PM</option><?} else {?><option value="15:00">03:00 PM</option><?}?>									
							<?if ($row['time'] == "15:30") {?><option value="15:30" selected = "selected">03:30 PM</option><?} else {?><option value="15:30">03:30 PM</option><?}?>									
							<?if ($row['time'] == "16:00") {?><option value="16:00" selected = "selected">04:00 PM</option><?} else {?><option value="16:00">04:00 PM</option><?}?>									
							<?if ($row['time'] == "16:30") {?><option value="16:30" selected = "selected">04:30 PM</option><?} else {?><option value="16:30">04:30 PM</option><?}?>									
							<?if ($row['time'] == "17:00") {?><option value="17:00" selected = "selected">05:00 PM</option><?} else {?><option value="17:00">05:00 PM</option><?}?>									
							<?if ($row['time'] == "17:30") {?><option value="17:30" selected = "selected">05:30 PM</option><?} else {?><option value="17:30">05:30 PM</option><?}?>									
							<?if ($row['time'] == "18:00") {?><option value="18:00" selected = "selected">06:00 PM</option><?} else {?><option value="18:00">06:00 PM</option><?}?>									
							<?if ($row['time'] == "18:30") {?><option value="18:30" selected = "selected">06:30 PM</option><?} else {?><option value="18:30">06:30 PM</option><?}?>									
							<?if ($row['time'] == "19:00") {?><option value="19:00" selected = "selected">07:00 PM</option><?} else {?><option value="19:00">07:00 PM</option><?}?>									
							<?if ($row['time'] == "19:30") {?><option value="19:30" selected = "selected">07:30 PM</option><?} else {?><option value="19:30">07:30 PM</option><?}?>									
							<?if ($row['time'] == "20:00") {?><option value="20:00" selected = "selected">08:00 PM</option><?} else {?><option value="20:00">08:00 PM</option><?}?>									
							<?if ($row['time'] == "20:30") {?><option value="20:30" selected = "selected">08:30 PM</option><?} else {?><option value="20:30">08:30 PM</option><?}?>									
							<?if ($row['time'] == "21:00") {?><option value="21:00" selected = "selected">09:00 PM</option><?} else {?><option value="21:00">09:00 PM</option><?}?>									
							<?if ($row['time'] == "21:30") {?><option value="21:30" selected = "selected">09:30 PM</option><?} else {?><option value="21:30">09:30 PM</option><?}?>									
							<?if ($row['time'] == "22:00") {?><option value="22:00" selected = "selected">10:00 PM</option><?} else {?><option value="22:00">10:00 PM</option><?}?>									
							<?if ($row['time'] == "22:30") {?><option value="22:30" selected = "selected">10:30 PM</option><?} else {?><option value="22:30">10:30 PM</option><?}?>									
							<?if ($row['time'] == "23:00") {?><option value="23:00" selected = "selected">11:00 PM</option><?} else {?><option value="23:00">11:00 PM</option><?}?>									
							<?if ($row['time'] == "23:30") {?><option value="23:30" selected = "selected">11:30 PM</option><?} else {?><option value="23:30">11:30 PM</option><?}?>									
							<?if ($row['time'] == "00:00") {?><option value="00:00" selected = "selected">12:00 AM</option><?} else {?><option value="00:00">12:00 AM</option><?}?>																											
							</select></td>
							<td align="left"><input type="text" name="lecturer_name[]" id="lecturer_name" size="50" value="<?=$row["lecturer_name"];?>"></input></td>
							<td align="left"><textarea name="remark[]" id="remark" cols="50" value="<?=$row["remark"];?>"><?=$row["remark"];?></textarea></td>
							
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
				<td>Notes:</td>
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
			<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../thesis/edit_proposal.php?tid=<?=pgThesisId?>&pid=<?=$pgProposalId?>';" /></td>		
			<td><input type="submit" name="btnUpdate" value="Update" /></td>
			<td><input type="submit" name="btnDelete" onClick="return respConfirm()" value="Delete" /></td>
		</tr>
	</table>

  </form>
  	<script>
		<?=$jscript;?>
	</script>
</body>
</html>





