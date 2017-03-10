<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: defense_calendar.php
//
// Created by: Zuraimi
// Created Date: 13-July-2015
// Modified by: Zuraimi
// Modified Date: 13-July-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

session_start();
$user_id=$_SESSION['user_id'];
$thesis_id=$_GET['tid'];
$proposal_id=$_GET['pid'];
$defense_id=$_GET['did'];
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
	if (empty($_POST['add_defense_date'])) $msg[] = "<div class=\"error\"><span>Please provide <strong>Defence Proposal Start Date</strong>.</span></div>";
	if (empty($_POST['add_defense_stime'])) $msg[] = "<div class=\"error\"><span>Please provide <strong>Defence Proposal Start Time</strong>.</span></div>";
	if (empty($_POST['add_defense_etime'])) $msg[] = "<div class=\"error\"><span>Please provide <strong>Defence Proposal End Time</strong>.</span></div>";
	if (empty($_POST['add_venue'])) $msg[] = "<div class=\"error\"><span>Please provide the <strong>Venue</strong> where the defense proposal will be conducted.</span></div>";
	if (empty($_POST['add_matrix_no'])) $msg[] = "<div class=\"error\"><span>Please select the <strong>Student</strong>.</span></div>";
	
	
	if(empty($msg)) 
	{
		$myAddDefenseDate = $_POST['add_defense_date'];
		$myAddDefenseSTime = $_POST['add_defense_stime'];
		$myAddDefenseETime = $_POST['add_defense_etime'];
		$myAddVenue = $_POST['add_venue'];
		$myAddMatrixNo = $_POST['add_matrix_no'];
		$myAddThesisId = $_POST['add_thesis_id'];
		$myAddRemarks = $_POST['add_remarks'];
		if ($myAddRemarks="") {
			$myAddRemarks = 'null';
		}
		
		$curdatetime = date("Y-m-d H:i:s");
		$calendarId = runnum2('id','pg_defense_calendar');	

		$sql = "INSERT INTO pg_defense_calendar(
		id, student_matrix_no, thesis_id, defense_date, defense_stime, defense_etime, venue, remarks, status, insert_by, insert_date, modify_by, modify_date)
		VALUES ('$calendarId', '$myAddMatrixNo', '$myAddThesisId', STR_TO_DATE('$myAddDefenseDate','%d-%M-%Y'), 
		STR_TO_DATE('$myAddDefenseSTime','%H:%i'), STR_TO_DATE('$myAddDefenseETime','%H:%i'),
		'$myAddVenue', '$myAddRemarks', 'A', '$user_id', '$curdatetime', '$user_id', '$curdatetime')";

		$db_klas2->query($sql); 

		$msg[] = "<div class=\"success\"><span>The entry for the Defense Proposal calendar has been added successfully.</span></div>";
	}
}

if(isset($_POST['btnUpdate']) && ($_POST['btnUpdate'] <> ""))
{					
	$tmpDefenseCheckBox = $_POST['defense_checkbox'];
	$no=1;
	while (list ($key,$val) = @each ($tmpDefenseCheckBox)) 
	{
		$no=$no+$val;
		if (empty($_POST['venue'])) $msg[] = "<div class=\"error\"><span>Please provide the <strong>Venue</strong> for record no $no where the defense proposal will be conducted.</span></div>";
	}
	
	if (sizeof($_POST['defense_checkbox'])>0) {
		if(empty($msg)) 
		{
			$curdatetime = date("Y-m-d H:i:s");
			while (list ($key,$val) = @each ($_POST['defense_checkbox'])) 
			{
				$defense_calendar_id = $_POST['defense_calendar_id'][$val];
				$defense_date = $_POST['defense_date'][$val];
				$defense_stime = $_POST['defense_stime'][$val];
				$defense_etime = $_POST['defense_etime'][$val];
				$venue = $_POST['venue'][$val];
				$remarks = $_POST['remarks'][$val];
				
				$sql1 = "UPDATE pg_defense_calendar
				SET defense_date = STR_TO_DATE('$defense_date','%d-%b-%Y'), 
				defense_stime = STR_TO_DATE('$defense_stime','%H:%i'),
				defense_etime = STR_TO_DATE('$defense_etime','%H:%i'),
				venue = '$venue',
				remarks = '$remarks',
				modify_by = '$user_id', modify_date = '$curdatetime'
				WHERE id = '$defense_calendar_id'";
				
				$dba->query($sql1); 				
			}
			$msg[] = "<div class=\"success\"><span>The selected Defense Proposal detail has been updated successfully.</span></div>";
		}
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please tick which Defence Proposal record to be updated!</span></div>";
	}
}

if(isset($_POST['btnDelete']) && ($_POST['btnDelete'] <> ""))
{					
	if (sizeof($_POST['defense_checkbox'])>0) {
		$curdatetime = date("Y-m-d H:i:s");
		while (list ($key,$val) = @each ($_POST['defense_checkbox'])) 
		{
			$defenseCalendarId = $_POST['defense_calendar_id'][$val];
			
			$sql1 = "SELECT id
			FROM pg_defense_calendar
			WHERE id = '$defenseCalendarId'
			AND recomm_status = 'REC'";
			
			$result_sql1 = $dba->query($sql1); 
			$row_cnt1 = mysql_num_rows($result_sql1);
			if ($row_cnt1 > 0) {
				
				$sql1 = "UPDATE pg_defense_calendar
				SET status = 'I', modify_by = '$user_id', modify_date = '$curdatetime'
				WHERE id = '$defenseCalendarId'
				AND recomm_status = 'REC'
				AND status = 'A'";
			
				$result_sql1 = $dba->query($sql1); 
			}
			else {
				$sql3 = "DELETE FROM pg_defense_calendar
				WHERE id = '$defenseCalendarId'";
				
				$dba->query($sql3); 
			}
		}
		$msg[] = "<div class=\"success\"><span>The selected Defence Scheduled has been deleted successfully.</span></div>";
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please tick which Defence Proposal schedule to be deleted!</span></div>";
	}
}

		
		
$sql1="SELECT id, student_matrix_no, thesis_id, DATE_FORMAT(defense_date,'%d-%b-%Y') as defense_date, 
DATE_FORMAT(defense_stime,'%H:%i') as defense_stime, DATE_FORMAT(defense_etime,'%H:%i') as defense_etime, venue, remarks
FROM  pg_defense_calendar
WHERE status = 'A'
AND archived_status IS NULL";

$result_sql1 = $db->query($sql1); 
$row_cnt = mysql_num_rows($result_sql1);
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
</head>
<body>
<script>
  
		$(document).ready(function(){
              
              $.fn.getParameterValue = function(matrixNo, studentName, thesisId) {
                  //alert(matrixNo + ' - ' + studentName);
                  document.form1.add_student_name.value = studentName;
				  document.form1.add_matrix_no.value = matrixNo;
				  document.form1.add_thesis_id.value = thesisId;
                };
              
               $(".select_student").colorbox({width:"90%", height:"100%", iframe:true,          
               onClosed:function(){ 
                //location.reload(true); //uncomment this line if you want to refresh the page when child close
                                
                } }); 

          });
	</script>

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
		<legend><strong>Defence Calendar Setup</strong></legend>
		<table>
			<tr>							
				<td>Please provide the requested information below to add the record:-</td>
			</tr>
		</table>
		<table>
			<tr>
				<td><label>Defence Proposal Date <span style="color:#FF0000">*</span></label></td>
				<td>:</td>
				<td><input name="add_defense_date" type="text" id="add_defense_date" size="10" value="<?=$_POST['add_defense_date']?>"readonly=""></td>
				<?	$jscript .= "\n" . '$( "#add_defense_date" ).datepicker({
												changeMonth: true,
												changeYear: true,
												yearRange: \'-100:+0\',
												dateFormat: \'dd-M-yy\'
											});';
					 
				?>
			</tr>
			<tr>
				<td><label>Defence Proposal Start Time <span style="color:#FF0000">*</span></label></td>
				<td>:</td>
				<td><select name="add_defense_stime" id="add_defense_stime">
				<option value="" selected = "selected">Select Time</option>				
				<?if ($_POST['add_defense_stime'] == "07:00") {?><option value="07:00" selected = "selected">07:00 AM</option><?} else {?><option value="07:00">07:00 AM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "07:30") {?><option value="07:30" selected = "selected">07:30 AM</option><?} else {?><option value="07:30">07:30 AM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "08:00") {?><option value="08:00" selected = "selected">08:00 AM</option><?} else {?><option value="08:00">08:00 AM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "08:30") {?><option value="08:30" selected = "selected">08:30 AM</option><?} else {?><option value="08:30">08:30 AM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "09:00") {?><option value="09:00" selected = "selected">09:00 AM</option><?} else {?><option value="09:00">09:00 AM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "09:30") {?><option value="09:30" selected = "selected">09:30 AM</option><?} else {?><option value="09:30">09:30 AM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "10:00") {?><option value="10:00" selected = "selected">10:00 AM</option><?} else {?><option value="10:00">10:00 AM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "10:30") {?><option value="10:30" selected = "selected">10:30 AM</option><?} else {?><option value="10:30">10:30 AM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "11:00") {?><option value="11:00" selected = "selected">11:00 AM</option><?} else {?><option value="11:00">11:00 AM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "11:30") {?><option value="11:30" selected = "selected">11:30 AM</option><?} else {?><option value="11:30">11:30 AM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "12:00") {?><option value="12:00" selected = "selected">12:00 PM</option><?} else {?><option value="12:00">12:00 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "12:30") {?><option value="12:30" selected = "selected">12:30 PM</option><?} else {?><option value="12:30">12:30 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "13:00") {?><option value="13:00" selected = "selected">01:00 PM</option><?} else {?><option value="13:00">01:00 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "13:30") {?><option value="13:30" selected = "selected">01:30 PM</option><?} else {?><option value="13:30">01:30 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "14:00") {?><option value="14:00" selected = "selected">02:00 PM</option><?} else {?><option value="14:00">02:00 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "14:30") {?><option value="14:30" selected = "selected">02:30 PM</option><?} else {?><option value="14:30">02:30 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "15:00") {?><option value="15:00" selected = "selected">03:00 PM</option><?} else {?><option value="15:00">03:00 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "15:30") {?><option value="15:30" selected = "selected">03:30 PM</option><?} else {?><option value="15:30">03:30 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "16:00") {?><option value="16:00" selected = "selected">04:00 PM</option><?} else {?><option value="16:00">04:00 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "16:30") {?><option value="16:30" selected = "selected">04:30 PM</option><?} else {?><option value="16:30">04:30 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "17:00") {?><option value="17:00" selected = "selected">05:00 PM</option><?} else {?><option value="17:00">05:00 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "17:30") {?><option value="17:30" selected = "selected">05:30 PM</option><?} else {?><option value="17:30">05:30 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "18:00") {?><option value="18:00" selected = "selected">06:00 PM</option><?} else {?><option value="18:00">06:00 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "18:30") {?><option value="18:30" selected = "selected">06:30 PM</option><?} else {?><option value="18:30">06:30 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "19:00") {?><option value="19:00" selected = "selected">07:00 PM</option><?} else {?><option value="19:00">07:00 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "19:30") {?><option value="19:30" selected = "selected">07:30 PM</option><?} else {?><option value="19:30">07:30 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "20:00") {?><option value="20:00" selected = "selected">08:00 PM</option><?} else {?><option value="20:00">08:00 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "20:30") {?><option value="20:30" selected = "selected">08:30 PM</option><?} else {?><option value="20:30">08:30 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "21:00") {?><option value="21:00" selected = "selected">09:00 PM</option><?} else {?><option value="21:00">09:00 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "21:30") {?><option value="21:30" selected = "selected">09:30 PM</option><?} else {?><option value="21:30">09:30 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "22:00") {?><option value="22:00" selected = "selected">10:00 PM</option><?} else {?><option value="22:00">10:00 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "22:30") {?><option value="22:30" selected = "selected">10:30 PM</option><?} else {?><option value="22:30">10:30 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "23:00") {?><option value="23:00" selected = "selected">11:00 PM</option><?} else {?><option value="23:00">11:00 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "23:30") {?><option value="23:30" selected = "selected">11:30 PM</option><?} else {?><option value="23:30">11:30 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "00:00") {?><option value="00:00" selected = "selected">12:00 AM</option><?} else {?><option value="00:00">12:00 AM</option><?}?>																											
				</select></td>
			</tr>
			<tr>
				<td><label>Defence Proposal  End Time <span style="color:#FF0000">*</span></label></td>		
				<td>:</td>				
				<td><select name="add_defense_etime" id="add_defense_etime">
				<option value="" selected = "selected">Select Time</option>				
				<?if ($_POST['add_defense_etime'] == "07:00") {?><option value="07:00" selected = "selected">07:00 AM</option><?} else {?><option value="07:00">07:00 AM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "07:30") {?><option value="07:30" selected = "selected">07:30 AM</option><?} else {?><option value="07:30">07:30 AM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "08:00") {?><option value="08:00" selected = "selected">08:00 AM</option><?} else {?><option value="08:00">08:00 AM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "08:30") {?><option value="08:30" selected = "selected">08:30 AM</option><?} else {?><option value="08:30">08:30 AM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "09:00") {?><option value="09:00" selected = "selected">09:00 AM</option><?} else {?><option value="09:00">09:00 AM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "09:30") {?><option value="09:30" selected = "selected">09:30 AM</option><?} else {?><option value="09:30">09:30 AM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "10:00") {?><option value="10:00" selected = "selected">10:00 AM</option><?} else {?><option value="10:00">10:00 AM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "10:30") {?><option value="10:30" selected = "selected">10:30 AM</option><?} else {?><option value="10:30">10:30 AM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "11:00") {?><option value="11:00" selected = "selected">11:00 AM</option><?} else {?><option value="11:00">11:00 AM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "11:30") {?><option value="11:30" selected = "selected">11:30 AM</option><?} else {?><option value="11:30">11:30 AM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "12:00") {?><option value="12:00" selected = "selected">12:00 PM</option><?} else {?><option value="12:00">12:00 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "12:30") {?><option value="12:30" selected = "selected">12:30 PM</option><?} else {?><option value="12:30">12:30 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "13:00") {?><option value="13:00" selected = "selected">01:00 PM</option><?} else {?><option value="13:00">01:00 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "13:30") {?><option value="13:30" selected = "selected">01:30 PM</option><?} else {?><option value="13:30">01:30 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "14:00") {?><option value="14:00" selected = "selected">02:00 PM</option><?} else {?><option value="14:00">02:00 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "14:30") {?><option value="14:30" selected = "selected">02:30 PM</option><?} else {?><option value="14:30">02:30 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "15:00") {?><option value="15:00" selected = "selected">03:00 PM</option><?} else {?><option value="15:00">03:00 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "15:30") {?><option value="15:30" selected = "selected">03:30 PM</option><?} else {?><option value="15:30">03:30 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "16:00") {?><option value="16:00" selected = "selected">04:00 PM</option><?} else {?><option value="16:00">04:00 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "16:30") {?><option value="16:30" selected = "selected">04:30 PM</option><?} else {?><option value="16:30">04:30 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "17:00") {?><option value="17:00" selected = "selected">05:00 PM</option><?} else {?><option value="17:00">05:00 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "17:30") {?><option value="17:30" selected = "selected">05:30 PM</option><?} else {?><option value="17:30">05:30 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "18:00") {?><option value="18:00" selected = "selected">06:00 PM</option><?} else {?><option value="18:00">06:00 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "18:30") {?><option value="18:30" selected = "selected">06:30 PM</option><?} else {?><option value="18:30">06:30 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "19:00") {?><option value="19:00" selected = "selected">07:00 PM</option><?} else {?><option value="19:00">07:00 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "19:30") {?><option value="19:30" selected = "selected">07:30 PM</option><?} else {?><option value="19:30">07:30 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "20:00") {?><option value="20:00" selected = "selected">08:00 PM</option><?} else {?><option value="20:00">08:00 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "20:30") {?><option value="20:30" selected = "selected">08:30 PM</option><?} else {?><option value="20:30">08:30 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "21:00") {?><option value="21:00" selected = "selected">09:00 PM</option><?} else {?><option value="21:00">09:00 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "21:30") {?><option value="21:30" selected = "selected">09:30 PM</option><?} else {?><option value="21:30">09:30 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "22:00") {?><option value="22:00" selected = "selected">10:00 PM</option><?} else {?><option value="22:00">10:00 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "22:30") {?><option value="22:30" selected = "selected">10:30 PM</option><?} else {?><option value="22:30">10:30 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "23:00") {?><option value="23:00" selected = "selected">11:00 PM</option><?} else {?><option value="23:00">11:00 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "23:30") {?><option value="23:30" selected = "selected">11:30 PM</option><?} else {?><option value="23:30">11:30 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "00:00") {?><option value="00:00" selected = "selected">12:00 AM</option><?} else {?><option value="00:00">12:00 AM</option><?}?>																											
				</select></td>
			</tr>	
			<tr>
				<td><label>Venue <span style="color:#FF0000">*</span></label></td>
				<td>:</td>
				<td><input name="add_venue" cols="40" rows="2" id="add_venue" type="text" size="50" value="<?=$_POST['add_venue']?>"></td>
			</tr>
			<tr>
				<td><label>Remarks</label></td>
				<td>:</td>
				<td><input name="add_remarks" type="text" id="add_remarks" size="50" value="<?=$_POST['add_remarks']?>"></td>
			</tr>	
			<tr>
				<td><label>Student Name<span style="color:#FF0000">*</span></label></td>
				<td>:</td>
				<td><input name="add_student_name" type="text" id="add_student_name" size="50" readonly=""/>
				<a class='select_student' href="../../application/defense/select_student.php">[Select]</a>
				<input id ="add_staff_id" type="hidden" size="100" name="add_staff_id" /></td>
			</tr>
			<tr>
				<td><label>Matrix No</label></td>
				<td>:</td>
				<td><input name="add_matrix_no" type="text" id="add_matrix_no" size="50" readonly=""/></td>
			</tr>			
			<input name="add_thesis_id" type="hidden" id="add_thesis_id" readonly=""/>
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
		<?if ($row_cnt <= 0) {?>
			<div id = "tabledisplay" style="overflow:auto; height:80px;">
		<?}
		else if ($row_cnt <= 1) {?>
			<div id = "tabledisplay" style="overflow:auto; height:150px;">
		<?}
		else if ($row_cnt <= 2) {?>
			<div id = "tabledisplay" style="overflow:auto; height:200px;">
		<?}
		else if ($row_cnt <= 3) {
			?>
			<div id = "tabledisplay" style="overflow:auto; height:250px;">
			<?
		}
		else {
			?>
			<div id = "tabledisplay" style="overflow:auto; height:300px;">
			<?
		}?>		
		<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="90%" class="thetable">
			  <tr>
				<th align="center" width="5%"><label>Tick</label></td>
				<th align="center" width="5%"><label>No</label></td>
				<th align="center" width="25%"><label>Defence Proposal Date & Time <span style="color:#FF0000">*</span></label></th>
				<th align="left" width="15%"><label>Venue <span style="color:#FF0000">*</span></label></th>
				<th align="left" width="15%"><label>Remarks</label></th>
				<th align="left" width="20%"><label>Student</label></th>
				<th align="left" width="5%"><label>Evaluation Committee/ Examiner</label></th>
			  </tr>

			<?php
			if ($row_cnt > 0) {
				
				$tmp_no = 0;
				while($row = mysql_fetch_array($result_sql1)) 					
				{ 
					?><tr>
						<td align="center"><input type="checkbox" name="defense_checkbox[]" id="defense_checkbox" value="<?=$tmp_no;?>" /></td>
						<td align="center"><label><?=$tmp_no+1;?>.</label></td>
						<td align="left"><input type="text" name="defense_date[]" id="defense_date<?=$tmp_no;?>" size="10" value="<?=$row["defense_date"];?>" readonly=""></input><br/><br/>
						<?	$jscript .= "\n" . '$( "#defense_date' . $tmp_no . '" ).datepicker({
											changeMonth: true,
											changeYear: true,
											yearRange: \'-100:+0\',
											dateFormat: \'dd-M-yy\'
										});';
				 
						?>
							<select name="defense_stime[]" id="defense_stime">
							<?if ($row['defense_stime'] == "07:00") {?><option value="07:00" selected = "selected">07:00 AM</option><?} else {?><option value="07:00">07:00 AM</option><?}?>									
							<?if ($row['defense_stime'] == "07:30") {?><option value="07:30" selected = "selected">07:30 AM</option><?} else {?><option value="07:30">07:30 AM</option><?}?>									
							<?if ($row['defense_stime'] == "08:00") {?><option value="08:00" selected = "selected">08:00 AM</option><?} else {?><option value="08:00">08:00 AM</option><?}?>									
							<?if ($row['defense_stime'] == "08:30") {?><option value="08:30" selected = "selected">08:30 AM</option><?} else {?><option value="08:30">08:30 AM</option><?}?>									
							<?if ($row['defense_stime'] == "09:00") {?><option value="09:00" selected = "selected">09:00 AM</option><?} else {?><option value="09:00">09:00 AM</option><?}?>									
							<?if ($row['defense_stime'] == "09:30") {?><option value="09:30" selected = "selected">09:30 AM</option><?} else {?><option value="09:30">09:30 AM</option><?}?>									
							<?if ($row['defense_stime'] == "10:00") {?><option value="10:00" selected = "selected">10:00 AM</option><?} else {?><option value="10:00">10:00 AM</option><?}?>									
							<?if ($row['defense_stime'] == "10:30") {?><option value="10:30" selected = "selected">10:30 AM</option><?} else {?><option value="10:30">10:30 AM</option><?}?>									
							<?if ($row['defense_stime'] == "11:00") {?><option value="11:00" selected = "selected">11:00 AM</option><?} else {?><option value="11:00">11:00 AM</option><?}?>									
							<?if ($row['defense_stime'] == "11:30") {?><option value="11:30" selected = "selected">11:30 AM</option><?} else {?><option value="11:30">11:30 AM</option><?}?>									
							<?if ($row['defense_stime'] == "12:00") {?><option value="12:00" selected = "selected">12:00 PM</option><?} else {?><option value="12:00">12:00 PM</option><?}?>									
							<?if ($row['defense_stime'] == "12:30") {?><option value="12:30" selected = "selected">12:30 PM</option><?} else {?><option value="12:30">12:30 PM</option><?}?>									
							<?if ($row['defense_stime'] == "13:00") {?><option value="13:00" selected = "selected">01:00 PM</option><?} else {?><option value="13:00">01:00 PM</option><?}?>									
							<?if ($row['defense_stime'] == "13:30") {?><option value="13:30" selected = "selected">01:30 PM</option><?} else {?><option value="13:30">01:30 PM</option><?}?>									
							<?if ($row['defense_stime'] == "14:00") {?><option value="14:00" selected = "selected">02:00 PM</option><?} else {?><option value="14:00">02:00 PM</option><?}?>									
							<?if ($row['defense_stime'] == "14:30") {?><option value="14:30" selected = "selected">02:30 PM</option><?} else {?><option value="14:30">02:30 PM</option><?}?>									
							<?if ($row['defense_stime'] == "15:00") {?><option value="15:00" selected = "selected">03:00 PM</option><?} else {?><option value="15:00">03:00 PM</option><?}?>									
							<?if ($row['defense_stime'] == "15:30") {?><option value="15:30" selected = "selected">03:30 PM</option><?} else {?><option value="15:30">03:30 PM</option><?}?>									
							<?if ($row['defense_stime'] == "16:00") {?><option value="16:00" selected = "selected">04:00 PM</option><?} else {?><option value="16:00">04:00 PM</option><?}?>									
							<?if ($row['defense_stime'] == "16:30") {?><option value="16:30" selected = "selected">04:30 PM</option><?} else {?><option value="16:30">04:30 PM</option><?}?>									
							<?if ($row['defense_stime'] == "17:00") {?><option value="17:00" selected = "selected">05:00 PM</option><?} else {?><option value="17:00">05:00 PM</option><?}?>									
							<?if ($row['defense_stime'] == "17:30") {?><option value="17:30" selected = "selected">05:30 PM</option><?} else {?><option value="17:30">05:30 PM</option><?}?>									
							<?if ($row['defense_stime'] == "18:00") {?><option value="18:00" selected = "selected">06:00 PM</option><?} else {?><option value="18:00">06:00 PM</option><?}?>									
							<?if ($row['defense_stime'] == "18:30") {?><option value="18:30" selected = "selected">06:30 PM</option><?} else {?><option value="18:30">06:30 PM</option><?}?>									
							<?if ($row['defense_stime'] == "19:00") {?><option value="19:00" selected = "selected">07:00 PM</option><?} else {?><option value="19:00">07:00 PM</option><?}?>									
							<?if ($row['defense_stime'] == "19:30") {?><option value="19:30" selected = "selected">07:30 PM</option><?} else {?><option value="19:30">07:30 PM</option><?}?>									
							<?if ($row['defense_stime'] == "20:00") {?><option value="20:00" selected = "selected">08:00 PM</option><?} else {?><option value="20:00">08:00 PM</option><?}?>									
							<?if ($row['defense_stime'] == "20:30") {?><option value="20:30" selected = "selected">08:30 PM</option><?} else {?><option value="20:30">08:30 PM</option><?}?>									
							<?if ($row['defense_stime'] == "21:00") {?><option value="21:00" selected = "selected">09:00 PM</option><?} else {?><option value="21:00">09:00 PM</option><?}?>									
							<?if ($row['defense_stime'] == "21:30") {?><option value="21:30" selected = "selected">09:30 PM</option><?} else {?><option value="21:30">09:30 PM</option><?}?>									
							<?if ($row['defense_stime'] == "22:00") {?><option value="22:00" selected = "selected">10:00 PM</option><?} else {?><option value="22:00">10:00 PM</option><?}?>									
							<?if ($row['defense_stime'] == "22:30") {?><option value="22:30" selected = "selected">10:30 PM</option><?} else {?><option value="22:30">10:30 PM</option><?}?>									
							<?if ($row['defense_stime'] == "23:00") {?><option value="23:00" selected = "selected">11:00 PM</option><?} else {?><option value="23:00">11:00 PM</option><?}?>									
							<?if ($row['defense_stime'] == "23:30") {?><option value="23:30" selected = "selected">11:30 PM</option><?} else {?><option value="23:30">11:30 PM</option><?}?>									
							<?if ($row['defense_stime'] == "00:00") {?><option value="00:00" selected = "selected">12:00 AM</option><?} else {?><option value="00:00">12:00 AM</option><?}?>																											
							</select> to
							<select name="defense_etime[]" id="defense_etime" size="1" >
							<?if ($row['defense_etime'] == "07:00") {?><option value="07:00" selected = "selected">07:00 AM</option><?} else {?><option value="07:00">07:00 AM</option><?}?>									
							<?if ($row['defense_etime'] == "07:30") {?><option value="07:30" selected = "selected">07:30 AM</option><?} else {?><option value="07:30">07:30 AM</option><?}?>									
							<?if ($row['defense_etime'] == "08:00") {?><option value="08:00" selected = "selected">08:00 AM</option><?} else {?><option value="08:00">08:00 AM</option><?}?>									
							<?if ($row['defense_etime'] == "08:30") {?><option value="08:30" selected = "selected">08:30 AM</option><?} else {?><option value="08:30">08:30 AM</option><?}?>									
							<?if ($row['defense_etime'] == "09:00") {?><option value="09:00" selected = "selected">09:00 AM</option><?} else {?><option value="09:00">09:00 AM</option><?}?>									
							<?if ($row['defense_etime'] == "09:30") {?><option value="09:30" selected = "selected">09:30 AM</option><?} else {?><option value="09:30">09:30 AM</option><?}?>									
							<?if ($row['defense_etime'] == "10:00") {?><option value="10:00" selected = "selected">10:00 AM</option><?} else {?><option value="10:00">10:00 AM</option><?}?>									
							<?if ($row['defense_etime'] == "10:30") {?><option value="10:30" selected = "selected">10:30 AM</option><?} else {?><option value="10:30">10:30 AM</option><?}?>									
							<?if ($row['defense_etime'] == "11:00") {?><option value="11:00" selected = "selected">11:00 AM</option><?} else {?><option value="11:00">11:00 AM</option><?}?>									
							<?if ($row['defense_etime'] == "11:30") {?><option value="11:30" selected = "selected">11:30 AM</option><?} else {?><option value="11:30">11:30 AM</option><?}?>									
							<?if ($row['defense_etime'] == "12:00") {?><option value="12:00" selected = "selected">12:00 PM</option><?} else {?><option value="12:00">12:00 PM</option><?}?>									
							<?if ($row['defense_etime'] == "12:30") {?><option value="12:30" selected = "selected">12:30 PM</option><?} else {?><option value="12:30">12:30 PM</option><?}?>									
							<?if ($row['defense_etime'] == "13:00") {?><option value="13:00" selected = "selected">01:00 PM</option><?} else {?><option value="13:00">01:00 PM</option><?}?>									
							<?if ($row['defense_etime'] == "13:30") {?><option value="13:30" selected = "selected">01:30 PM</option><?} else {?><option value="13:30">01:30 PM</option><?}?>									
							<?if ($row['defense_etime'] == "14:00") {?><option value="14:00" selected = "selected">02:00 PM</option><?} else {?><option value="14:00">02:00 PM</option><?}?>									
							<?if ($row['defense_etime'] == "14:30") {?><option value="14:30" selected = "selected">02:30 PM</option><?} else {?><option value="14:30">02:30 PM</option><?}?>									
							<?if ($row['defense_etime'] == "15:00") {?><option value="15:00" selected = "selected">03:00 PM</option><?} else {?><option value="15:00">03:00 PM</option><?}?>									
							<?if ($row['defense_etime'] == "15:30") {?><option value="15:30" selected = "selected">03:30 PM</option><?} else {?><option value="15:30">03:30 PM</option><?}?>									
							<?if ($row['defense_etime'] == "16:00") {?><option value="16:00" selected = "selected">04:00 PM</option><?} else {?><option value="16:00">04:00 PM</option><?}?>									
							<?if ($row['defense_etime'] == "16:30") {?><option value="16:30" selected = "selected">04:30 PM</option><?} else {?><option value="16:30">04:30 PM</option><?}?>									
							<?if ($row['defense_etime'] == "17:00") {?><option value="17:00" selected = "selected">05:00 PM</option><?} else {?><option value="17:00">05:00 PM</option><?}?>									
							<?if ($row['defense_etime'] == "17:30") {?><option value="17:30" selected = "selected">05:30 PM</option><?} else {?><option value="17:30">05:30 PM</option><?}?>									
							<?if ($row['defense_etime'] == "18:00") {?><option value="18:00" selected = "selected">06:00 PM</option><?} else {?><option value="18:00">06:00 PM</option><?}?>									
							<?if ($row['defense_etime'] == "18:30") {?><option value="18:30" selected = "selected">06:30 PM</option><?} else {?><option value="18:30">06:30 PM</option><?}?>									
							<?if ($row['defense_etime'] == "19:00") {?><option value="19:00" selected = "selected">07:00 PM</option><?} else {?><option value="19:00">07:00 PM</option><?}?>									
							<?if ($row['defense_etime'] == "19:30") {?><option value="19:30" selected = "selected">07:30 PM</option><?} else {?><option value="19:30">07:30 PM</option><?}?>									
							<?if ($row['defense_etime'] == "20:00") {?><option value="20:00" selected = "selected">08:00 PM</option><?} else {?><option value="20:00">08:00 PM</option><?}?>									
							<?if ($row['defense_etime'] == "20:30") {?><option value="20:30" selected = "selected">08:30 PM</option><?} else {?><option value="20:30">08:30 PM</option><?}?>									
							<?if ($row['defense_etime'] == "21:00") {?><option value="21:00" selected = "selected">09:00 PM</option><?} else {?><option value="21:00">09:00 PM</option><?}?>									
							<?if ($row['defense_etime'] == "21:30") {?><option value="21:30" selected = "selected">09:30 PM</option><?} else {?><option value="21:30">09:30 PM</option><?}?>									
							<?if ($row['defense_etime'] == "22:00") {?><option value="22:00" selected = "selected">10:00 PM</option><?} else {?><option value="22:00">10:00 PM</option><?}?>									
							<?if ($row['defense_etime'] == "22:30") {?><option value="22:30" selected = "selected">10:30 PM</option><?} else {?><option value="22:30">10:30 PM</option><?}?>									
							<?if ($row['defense_etime'] == "23:00") {?><option value="23:00" selected = "selected">11:00 PM</option><?} else {?><option value="23:00">11:00 PM</option><?}?>									
							<?if ($row['defense_etime'] == "23:30") {?><option value="23:30" selected = "selected">11:30 PM</option><?} else {?><option value="23:30">11:30 PM</option><?}?>									
							<?if ($row['defense_etime'] == "00:00") {?><option value="00:00" selected = "selected">12:00 AM</option><?} else {?><option value="00:00">12:00 AM</option><?}?>																											
							</select></td>
						<input type="hidden" name="defense_calendar_id[]" id="defense_calendar_id" value="<?=$row['id'];?>" />
						<td><input name="venue[]" type="text" id="venue" value="<?=$row["venue"];?>" ></input></td>
						<td><input type="text" name="remarks[]" id="remarks" value="<?=$row["remarks"];?>"></input></td>
						<?
						if (substr($student_matrix_no,0,2) != '07') {
							$dbConn = $dbc;
						}
						else {
							$dbConn = $dbc1;
						}
						$sql2 = "SELECT name
						FROM student
						where matrix_no = '".$row["student_matrix_no"]."'";
						
						$result_sql2 = $dbConn->query($sql2);
						$dbConn->next_record();
						$studentName = $dbConn->f('name');
						?>
						<td><label><?=$studentName?><br/><?=$row["student_matrix_no"]?></label></td>
						<td align="center"><a href="../defense/defense_calendar_view.php?mn=<?=$row["student_matrix_no"];?>&tid=<?=$row["thesis_id"];?>">View</a></td>					
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
		</div>
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





