<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Untitled Document</title>
		<link rel="stylesheet" type="text/css" href="../../theme/css/<?php echo $css; ?>" />
		<link rel="stylesheet" href="../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />    
		<script src="../../lib/js/jquery.min2.js"></script>
		<script src="../../lib/js/jquery.colorbox.js"></script>
		<script src="../../lib/js/jquery.mask_input-1.3.js"></script>
		<script type="text/javascript" src="../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
		<script src="../../lib/js/datePicker/jquery.ui.core.js"></script>
		<script src="../../lib/js/datePicker/jquery.ui.widget.js"></script>
		<script src="../../lib/js/datePicker/jquery.ui.datepicker.js"></script>
		<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>
		<script>
		window.onload = firstLoad;
		function firstLoad()
		{
			//document.getElementById("Focus").focus();
			//window.scrollTo(0, 0);
			document.location.href = '#top'; 
		}
		</script>

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
	if (empty($_POST['add_lecturer_name']) && empty($_POST['add_external_lecturer'])) $msg[] = "<div class=\"error\"><span>Please enter Lecturer Name or External Lecturer</span></div>";
	if(empty($msg)) 
	{
		$add_meeting_date = $_POST['add_meeting_date'];
		$add_meeting_time = $_POST['add_meeting_time'];
		$add_lecturer_name = $_POST['add_lecturer_name'];
		$add_external_lecturer = $_POST['add_external_lecturer'];
		$meeting_mode = $_POST['meeting_mode'];

		$add_remark = $_POST['add_remark'];
		$curdatetime = date("Y-m-d H:i:s");
		
		$meeting_detail_id = runnum2('id','pg_meeting_detail');	

		$myMeetingDate = $_POST['add_meeting_date']." ".$_POST['add_meeting_time'];
		$myLecturer = mysql_real_escape_string($_POST['add_staff_id']);
		$myExternalLecturer = mysql_real_escape_string($_POST['add_external_lecturer']);
		$myRemarks = mysql_real_escape_string($_POST['add_remark']);
		$sqlMeeting = "INSERT INTO pg_meeting_detail(
						id, student_matrix_no,
						lecturer_id,
						external_lecturer,
						meeting_sdate,meeting_mode,
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
						'$myExternalLecturer',
						STR_TO_DATE('$myMeetingDate','%d-%M-%Y %H:%i'), '$meeting_mode', 
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
			$lecturer_id = $_POST['staffIdArray'.$val];
			$external_lecturer = $_POST['external_lecturer'][$val];
			$remark = $_POST['remark'][$val];
			$myMeetingDate = $_POST['meeting_date'][$val]." ".$_POST['meeting_time'][$val];
			$meetID = $_POST['meeting_mode1'][$val];
			
			$sql1 = "UPDATE pg_meeting_detail
			SET meeting_sdate = STR_TO_DATE('$myMeetingDate','%d-%b-%Y %H:%i'), lecturer_id = '$lecturer_id', 
			external_lecturer = '$external_lecturer', remark = '$remark', meeting_mode = '$meetID',
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
			$lecturer_id = $_POST['lecturer_id'][$val];
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

		
		
$sqlMeeting="SELECT pmd.id,pmd.lecturer_id, pmd.external_lecturer, DATE_FORMAT(pmd.meeting_sdate,'%d-%b-%Y') as date,
DATE_FORMAT(pmd.meeting_sdate,'%H:%i') as time, pmd.remark,pmd.meeting_mode 
FROM  pg_meeting_detail pmd  
WHERE pmd.pg_proposal_id='$pgProposalId'
AND student_matrix_no = '$user_id' 
ORDER BY pmd.meeting_sdate DESC ";			

$result = $db->query($sqlMeeting); 
$db->next_record();
$row_cnt = mysql_num_rows($result);

$idArray = Array();
$staffIdArray = Array();
$staffNameArray = Array();
$externalLecturerArray = Array();
$meetingSDateArray = Array();
$meetingSTimeArray = Array();
$remarkArray = Array();
$meetingidArray = Array();

$i=0;
$j=0;
if ($row_cnt > 0) {
	do {
		$idArray[$i] = $db->f('id');
		$staffIdArray[$i] = $db->f('lecturer_id');
		$externalLecturerArray[$i] = $db->f('external_lecturer');
		$meetingSDateArray[$i] = $db->f('date');
		$meetingSTimeArray[$i] = $db->f('time');
		$remarkArray[$i] = $db->f('remark');
		$meetingidArray[$i] = $db->f('meeting_mode');

		$i++;
	} while ($db->next_record());

	for ($no = 0; $no < $row_cnt; $no++) {
		$sql1 = "SELECT name  
		FROM new_employee
		WHERE empid = '$staffIdArray[$no]'";
		
		$result_sql1 = $dbc->query($sql1);
		$dbc->next_record();
		
		$idArray[$j] = $idArray[$no];
		$staffIdArray[$j] = $staffIdArray[$no];
		$staffNameArray[$j] = $dbc->f('name');
		$externalLecturerArray[$j] = $externalLecturerArray[$no];
		$meetingSDateArray[$j] = $meetingSDateArray[$no];
		$meetingSTimeArray[$j] = $meetingSTimeArray[$no];
		$remarkArray[$j] = $remarkArray[$no];
		$meetingidArray[$j] = $meetingidArray[$no];

		$j++;
	}
}

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
	<script language="JavaScript" src="../../../lib/js/windowopen.js"></script>
	<script src="../../../lib/js/jquery.colorbox.js"></script>
	<script>
  
		$(document).ready(function(){
		      //$(".select_user").colorbox({width:"60%", height:"40%", iframe:true});
              
              $.fn.getParameterValue = function(data,data2) {
                  //alert(data + ' - ' + data2);
                  document.form1.add_staff_id.value = data;
				  document.form1.add_lecturer_name.value = data2;
                };
              
               $(".select_user").colorbox({width:"80%", height:"90%", iframe:true,          
               onClosed:function(){ 
                //location.reload(true); //uncomment this line if you want to refresh the page when child close
                                
                } }); 
				

				$.fn.getParameterValue3 = function(data,data2,data3,data4) {
                  //alert(data2 + data + data4 + data3);
                  //document.form1.JobArea.value = data; $("[name='field07']").prop("disabled", false);
				  //var field1 = $("#"+data2).val(data);
				  $("#"+data4).val(data3);
				  $("#"+data2).val(data);
				};
              
               $(".select_user").colorbox({width:"80%", height:"90%", iframe:true,          
               onClosed:function(){ 
                //location.reload(true); //uncomment this line if you want to refresh the page when child close
                                
                } }); 
          });
	</script>
	
</head>
<body>

<SCRIPT LANGUAGE="JavaScript">

function respConfirm () {
    var confirmSubmit = confirm("Click OK if you confirm to delete else click CANCEL to proceed with the changes.");
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
		<legend><label id = "Focus"><strong>Discussion Details</strong></label></legend>
	<a name="top"></a>
		<?php ?> 
		<table>
			<tr>
				<td><label>Meeting Date <span style="color:#FF0000">*</span></label></td>
				<td><input type="text" name="add_meeting_date" id="add_meeting_date" maxlength="50" readonly=""></input></td>
				<?	$jscript .= "\n" . '$( "#add_meeting_date" ).datepicker({
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
				<option value="07:00">07:00 AM</option>									
				<option value="07:30">07:30 AM</option>									
				<option value="08:00">08:00 AM</option>									
				<option value="08:30">08:30 AM</option>									
				<option value="09:00">09:00 AM</option>									
				<option value="09:30">09:30 AM</option>									
				<option value="10:00">10:00 AM</option>									
				<option value="10:30">10:30 AM</option>									
				<option value="11:00">11:00 AM</option>									
				<option value="11:30">11:30 AM</option>									
				<option value="12:00">12:00 PM</option>									
				<option value="12:30">12:30 PM</option>									
				<option value="13:00">01:00 PM</option>									
				<option value="13:30">01:30 PM</option>									
				<option value="14:00">02:00 PM</option>									
				<option value="14:30">02:30 PM</option>									
				<option value="15:00">03:00 PM</option>									
				<option value="15:30">03:30 PM</option>									
				<option value="16:00">04:00 PM</option>									
				<option value="16:30">04:30 PM</option>									
				<option value="17:00">05:00 PM</option>									
				<option value="17:30">05:30 PM</option>									
				<option value="18:00">06:00 PM</option>									
				<option value="18:30">06:30 PM</option>									
				<option value="19:00">07:00 PM</option>									
				<option value="19:30">07:30 PM</option>									
				<option value="20:00">08:00 PM</option>									
				<option value="20:30">08:30 PM</option>									
				<option value="21:00">09:00 PM</option>									
				<option value="21:30">09:30 PM</option>									
				<option value="22:00">10:00 PM</option>									
				<option value="22:30">10:30 PM</option>									
				<option value="23:00">11:00 PM</option>									
				<option value="23:30">11:30 PM</option>									
				<option value="00:00">12:00 AM</option>																											
				</select></td>
			</tr>
			<tr>
				<td><label>Lecturer <span style="color:#FF0000">*</span></label></td>
				<td><input name="add_lecturer_name" type="text" id="add_lecturer_name" size="50" readonly=""/>
				<a class='select_user' href="../../application/thesis/select_staff.php">[Select]</a>
				<input id ="add_staff_id" type="hidden" size="100" name="add_staff_id" /></td>
			</tr>
			<tr>
				<td><label>External Lecturer <span style="color:#FF0000">*</span></label></td>
			  <td><input type="text "name="add_external_lecturer" cols="50" id="add_external_lecturer"> <em><span style="color:#FF0000">Note:</span> If non-MSU staff.</em></input></td>
			</tr>
			<tr>
				<td><label>Meeting Mode</label></td>
				<td><select name="meeting_mode">
				<option value = "">--Please Select--</option>
				<?
					$sql = "SELECT * FROM ref_meeting_mode";
					$var = $dba;
					$var->query($sql);
					$var->next_record();
					
					do{
						$meetingID = $var->f('id');
						$meetingDesc = $var->f('description');
				?>
						<option value="<?=$meetingID; ?>" /><?=$meetingDesc; ?></option>
							
				<? 
					}while($var->next_record()); 
				?>
                </select>
				</td>
			</tr>
			<tr>
				<td><label>Notes</label></td>
				<td><textarea name="add_remark" cols="50" id="add_remark"></textarea></td>
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
			<td>Searching Results:- <?=$row_cnt?> record(s) found.</td>
		</tr>
		</table>
		<? if($row_cnt > 5)
		{ ?>
			<div id = "tabledisplay" style="overflow:auto; height:350px;">
		<? }
		else
		{?>
			<div id = "tabledisplay" style="overflow:auto;">		
		<? }
		?>


		<table border="1" cellpadding="3" cellspacing="3" width="100%" id="inputs10" class="thetable">
			  <tr align="left">
				<th width="2%" align="center"><label>Tick</label></td>
				<th width="3%" align="center"><label>No</label></td>
				<th width="10%" align="center"><label>Date <span style="color:#FF0000">*</span></label></th>
				<th width="10%" align="center"><label>Time <span style="color:#FF0000">*</span></label></th>
				<th width="26%" >Lecturer Name <span style="color:#FF0000">*</span></th>
				<th width="17%" >External Lecturer <span style="color:#FF0000">*</span></th>
				<th width="11%" align="center"><label>Meeting Mode <span style="color:#FF0000"></span></label></th>
				<th width="22%" >Notes</th>
			  </tr>

			<?php		
			if ($j > 0) {				
				$tmp_no = 0;
				for ($k = 0; $k < $j; $k++) 					
				{ 
					?><tr>
							<td align="center"><input type="checkbox" name="meeting_detail_checkbox[]" id="meeting_detail_checkbox" value="<?=$tmp_no;?>" /></td>
							<td align="center"><label><?=$tmp_no+1;?>.</label></td>
							<td align="center"><input name="meeting_date[]" type="text" id="meeting_date<?=$tmp_no;?>" value="<?=$meetingSDateArray[$k];?>" size="10" readonly="">
							</input></td>
							<?	$jscript .= "\n" . '$( "#meeting_date' . $tmp_no . '" ).datepicker({
												changeMonth: true,
												changeYear: true,
												yearRange: \'-100:+0\',
												dateFormat: \'dd-M-yy\'
											});';
					 
				?>
							<input type="hidden" name="meeting_detail_id[]" id="meeting_detail_id" value="<?=$idArray[$k];?>" />
							<td align="center"">
							<select name="meeting_time[]" id="meeting_time" size="1" >
							<?if ($meetingSTimeArray[$k] == "07:00") {?><option value="07:00" selected = "selected">07:00 AM</option><?} else {?><option value="07:00">07:00 AM</option><?}?>									
							<?if ($meetingSTimeArray[$k] == "07:30") {?><option value="07:30" selected = "selected">07:30 AM</option><?} else {?><option value="07:30">07:30 AM</option><?}?>									
							<?if ($meetingSTimeArray[$k] == "08:00") {?><option value="08:00" selected = "selected">08:00 AM</option><?} else {?><option value="08:00">08:00 AM</option><?}?>									
							<?if ($meetingSTimeArray[$k] == "08:30") {?><option value="08:30" selected = "selected">08:30 AM</option><?} else {?><option value="08:30">08:30 AM</option><?}?>									
							<?if ($meetingSTimeArray[$k] == "09:00") {?><option value="09:00" selected = "selected">09:00 AM</option><?} else {?><option value="09:00">09:00 AM</option><?}?>									
							<?if ($meetingSTimeArray[$k] == "09:30") {?><option value="09:30" selected = "selected">09:30 AM</option><?} else {?><option value="09:30">09:30 AM</option><?}?>									
							<?if ($meetingSTimeArray[$k] == "10:00") {?><option value="10:00" selected = "selected">10:00 AM</option><?} else {?><option value="10:00">10:00 AM</option><?}?>									
							<?if ($meetingSTimeArray[$k] == "10:30") {?><option value="10:30" selected = "selected">10:30 AM</option><?} else {?><option value="10:30">10:30 AM</option><?}?>									
							<?if ($meetingSTimeArray[$k] == "11:00") {?><option value="11:00" selected = "selected">11:00 AM</option><?} else {?><option value="11:00">11:00 AM</option><?}?>									
							<?if ($meetingSTimeArray[$k] == "11:30") {?><option value="11:30" selected = "selected">11:30 AM</option><?} else {?><option value="11:30">11:30 AM</option><?}?>									
							<?if ($meetingSTimeArray[$k] == "12:00") {?><option value="12:00" selected = "selected">12:00 PM</option><?} else {?><option value="12:00">12:00 PM</option><?}?>									
							<?if ($meetingSTimeArray[$k] == "12:30") {?><option value="12:30" selected = "selected">12:30 PM</option><?} else {?><option value="12:30">12:30 PM</option><?}?>									
							<?if ($meetingSTimeArray[$k] == "13:00") {?><option value="13:00" selected = "selected">01:00 PM</option><?} else {?><option value="13:00">01:00 PM</option><?}?>									
							<?if ($meetingSTimeArray[$k] == "13:30") {?><option value="13:30" selected = "selected">01:30 PM</option><?} else {?><option value="13:30">01:30 PM</option><?}?>									
							<?if ($meetingSTimeArray[$k] == "14:00") {?><option value="14:00" selected = "selected">02:00 PM</option><?} else {?><option value="14:00">02:00 PM</option><?}?>									
							<?if ($meetingSTimeArray[$k] == "14:30") {?><option value="14:30" selected = "selected">02:30 PM</option><?} else {?><option value="14:30">02:30 PM</option><?}?>									
							<?if ($meetingSTimeArray[$k] == "15:00") {?><option value="15:00" selected = "selected">03:00 PM</option><?} else {?><option value="15:00">03:00 PM</option><?}?>									
							<?if ($meetingSTimeArray[$k] == "15:30") {?><option value="15:30" selected = "selected">03:30 PM</option><?} else {?><option value="15:30">03:30 PM</option><?}?>									
							<?if ($meetingSTimeArray[$k] == "16:00") {?><option value="16:00" selected = "selected">04:00 PM</option><?} else {?><option value="16:00">04:00 PM</option><?}?>									
							<?if ($meetingSTimeArray[$k] == "16:30") {?><option value="16:30" selected = "selected">04:30 PM</option><?} else {?><option value="16:30">04:30 PM</option><?}?>									
							<?if ($meetingSTimeArray[$k] == "17:00") {?><option value="17:00" selected = "selected">05:00 PM</option><?} else {?><option value="17:00">05:00 PM</option><?}?>									
							<?if ($meetingSTimeArray[$k] == "17:30") {?><option value="17:30" selected = "selected">05:30 PM</option><?} else {?><option value="17:30">05:30 PM</option><?}?>									
							<?if ($meetingSTimeArray[$k] == "18:00") {?><option value="18:00" selected = "selected">06:00 PM</option><?} else {?><option value="18:00">06:00 PM</option><?}?>									
							<?if ($meetingSTimeArray[$k] == "18:30") {?><option value="18:30" selected = "selected">06:30 PM</option><?} else {?><option value="18:30">06:30 PM</option><?}?>									
							<?if ($meetingSTimeArray[$k] == "19:00") {?><option value="19:00" selected = "selected">07:00 PM</option><?} else {?><option value="19:00">07:00 PM</option><?}?>									
							<?if ($meetingSTimeArray[$k] == "19:30") {?><option value="19:30" selected = "selected">07:30 PM</option><?} else {?><option value="19:30">07:30 PM</option><?}?>									
							<?if ($meetingSTimeArray[$k] == "20:00") {?><option value="20:00" selected = "selected">08:00 PM</option><?} else {?><option value="20:00">08:00 PM</option><?}?>									
							<?if ($meetingSTimeArray[$k] == "20:30") {?><option value="20:30" selected = "selected">08:30 PM</option><?} else {?><option value="20:30">08:30 PM</option><?}?>									
							<?if ($meetingSTimeArray[$k] == "21:00") {?><option value="21:00" selected = "selected">09:00 PM</option><?} else {?><option value="21:00">09:00 PM</option><?}?>									
							<?if ($meetingSTimeArray[$k] == "21:30") {?><option value="21:30" selected = "selected">09:30 PM</option><?} else {?><option value="21:30">09:30 PM</option><?}?>									
							<?if ($meetingSTimeArray[$k] == "22:00") {?><option value="22:00" selected = "selected">10:00 PM</option><?} else {?><option value="22:00">10:00 PM</option><?}?>									
							<?if ($meetingSTimeArray[$k] == "22:30") {?><option value="22:30" selected = "selected">10:30 PM</option><?} else {?><option value="22:30">10:30 PM</option><?}?>									
							<?if ($meetingSTimeArray[$k] == "23:00") {?><option value="23:00" selected = "selected">11:00 PM</option><?} else {?><option value="23:00">11:00 PM</option><?}?>									
							<?if ($meetingSTimeArray[$k] == "23:30") {?><option value="23:30" selected = "selected">11:30 PM</option><?} else {?><option value="23:30">11:30 PM</option><?}?>									
							<?if ($meetingSTimeArray[$k] == "00:00") {?><option value="00:00" selected = "selected">12:00 AM</option><?} else {?><option value="00:00">12:00 AM</option><?}?>																											
							</select></td>
							
							<td align="left">
							<input type="hidden" name="staffIdArray<?=$k;?>" id="staffIdArray<?=$k;?>" value="<?=$staffIdArray[$k];?>" />
							<input type="text" name="staffNameArray[]" id="staffNameArray<?=$k;?>" size="31" value="<?=$staffNameArray[$k];?>" readonly=""/>							
							<a class='select_user' href="../../application/thesis/select_staff.php?field=staffNameArray<?=$k;?>&field2=staffIdArray<?=$k;?>">[Select]</a>							
							<input id ="staff_id<?=$k;?>" type="hidden" size="100" name="staff_id[<?=$k;?>]" /></td>
							
							<td align="left"><input type="text" name="external_lecturer[]" id="external_lecturer" size="25" value="<?=$externalLecturerArray[$k];?>"></input></td>
							<td align="center""><select name="meeting_mode1[]">
                                <option value = "">--Please Select--</option>
                                <?
									$sql = "SELECT * FROM ref_meeting_mode";
									$var = $dbf;
									$var->query($sql);
									$var->next_record();
									
									do{
										$meetingID = $var->f('id');
										$meetingDesc = $var->f('description');
								?>
                                <option value="<?=$meetingID; ?>" <?php if ($meetingID == $meetingidArray[$k] ) echo 'selected'; ?>/><?=$meetingDesc; ?></option>
							  
                                
                                
                                <? 
									}while($var->next_record()); 
								?>
                              </select>
                            </td>
							<td align="left"><textarea name="remark[]" id="remark" cols="25" value="<?=$remarkArray[$k];?>"><?=$remarkArray[$k];?></textarea></td>
							
						</tr>
					<?
					$tmp_no++;
				}
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
	</div>
	
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
			<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../student/student_programme.php?tid=<?=$pgThesisId?>&pid=<?=$pgProposalId?>#tabs-2';" /></td>		
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





