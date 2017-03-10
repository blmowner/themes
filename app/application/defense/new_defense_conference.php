<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: new_defense_conference.php
//
// Created by: Zuraimi
// Created Date: 24-Jun-2015
// Modified by: Zuraimi
// Modified Date: 24-Jun-2015
//
//**************************************************************************************
/*ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);*/
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
@$defense_id=$_GET['pgid']; // not did
$referenceNo=$_GET['ref'];

if (!class_exists('DateTime')) {
	class DateTime {
		public $date;
	   
		public function __construct($date) {
			$this->date = strtotime($date);
		}
	   
		public function setTimeZone($timezone) {
			return;
		}
	   
		private function __getDate() {
			return date(DATE_ATOM, $this->date);   
		}
	   
		public function modify($multiplier) {
			$this->date = strtotime($this->__getDate() . ' ' . $multiplier);
		}
	   
		public function format($format) {
			return date($format, $this->date);
		}
	}
}

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
	if (empty($_POST['add_conference'])) $msg[] = "<div class=\"error\"><span>Please provide Conference information.</span></div>";
	if (empty($_POST['add_conference_loc'])) $msg[] = "<div class=\"error\"><span>Please provide Conference Location.</span></div>";
	
	
	if (empty($_POST['add_conference_sdate']) || empty($_POST['add_conference_edate'])) {
		if (empty($_POST['add_conference_sdate'])) $msg[] = "<div class=\"error\"><span>Please provide Conference Start Date.</span></div>";
		if (empty($_POST['add_conference_edate'])) $msg[] = "<div class=\"error\"><span>Please provide Conference End Date.</span></div>";
	}
	else {
		$conferenceSDate1 = date('d-M-Y', strtotime($_POST['add_conference_sdate']));
		$conferenceSDate2 = new DateTime($conferenceSDate1);
		$conferenceSDate3 = $conferenceSDate2->format('d-M-Y');
		$conferenceSDate4 = new DateTime($conferenceSDate3);
		
		$conferenceEDate1 = date('d-M-Y', strtotime($_POST['add_conference_edate']));
		$conferenceEDate2 = new DateTime($conferenceEDate1);
		$conferenceEDate3 = $conferenceEDate2->format('d-M-Y');
		$conferenceEDate4 = new DateTime($conferenceEDate3);
		
		if ($conferenceEDate4 < $conferenceSDate4) $msg[] = "<div class=\"error\"><span>Conference Start Date cannot be later than Conference End Date.</span></div>";
	
	}
	if (empty($_POST['add_presentation_title'])) $msg[] = "<div class=\"error\"><span>Please provide Title of Presentation.</span></div>";
	
	if(empty($msg)) 
	{
		$myAddConference = $_POST['add_conference'];
		$myAddConferenceLoc = $_POST['add_conference_loc'];
		$myAddConferenceSDate = $_POST['add_conference_sdate'];
		$myAddConferenceEDate = $_POST['add_conference_edate'];
		$myAddPresentationStatus = $_POST['add_presentation_status'];
		$myAddPresentationTitle = $_POST['add_presentation_title'];
		
		$curdatetime = date("Y-m-d H:i:s");
		$conference_id = runnum2('id','pg_defense_conference');	

		if ($defense_id!="") {
			$sqlMeeting = "INSERT INTO pg_defense_conference(
			id, pg_defense_id, reference_no, pg_thesis_id, pg_proposal_id, student_matrix_no, ref_session_type_id,
			conference, location, conference_sdate, conference_edate, presentation_status, presentation_title,
			add_status, insert_by, insert_date, modify_by, modify_date)
			VALUES ('$conference_id', '$defense_id', '$referenceNo', '$thesis_id', '$proposal_id', '$user_id', 'DEF',
			'$myAddConference', '$myAddConferenceLoc', STR_TO_DATE('$myAddConferenceSDate','%d-%M-%Y'), 
			STR_TO_DATE('$myAddConferenceEDate','%d-%M-%Y'), '$myAddPresentationStatus', '$myAddPresentationTitle',
			'TMP', '$user_id', '$curdatetime', '$user_id', '$curdatetime')";

			$db_klas2->query($sqlMeeting); 
		}
		else {
			$sqlMeeting = "INSERT INTO pg_defense_conference(
			id, pg_defense_id, reference_no, pg_thesis_id, pg_proposal_id, student_matrix_no, ref_session_type_id,
			conference, location, conference_sdate, conference_edate, presentation_status, presentation_title,
			add_status, insert_by, insert_date, modify_by, modify_date)
			VALUES ('$conference_id', null, null, '$thesis_id', '$proposal_id', '$user_id', 'DEF',
			'$myAddConference', '$myAddConferenceLoc', STR_TO_DATE('$myAddConferenceSDate','%d-%M-%Y'), 
			STR_TO_DATE('$myAddConferenceEDate','%d-%M-%Y'), '$myAddPresentationStatus', '$myAddPresentationTitle',
			'TMP', '$user_id', '$curdatetime', '$user_id', '$curdatetime')";

			$db_klas2->query($sqlMeeting); 
		}
		
		$msg[] = "<div class=\"success\"><span>Conference information has been added successfully.</span></div>";
	}
	unset($_POST);
}

if(isset($_POST['btnUpdate']) && ($_POST['btnUpdate'] <> ""))
{	
	$msg = array();
	$tmpConferenceCheckBox = $_POST['conference_checkbox'];
	$no=1;
	while (list ($key,$val) = @each ($tmpConferenceCheckBox)) 
	{
		$no=$no+$val;
		if (empty($_POST['conference'][$val])) $msg[] = "<div class=\"error\"><span>Please provide Conference information for record no $no.</span></div>";
		if (empty($_POST['location'][$val])) $msg[] = "<div class=\"error\"><span>Please provide Conference Location for record no $no.</span></div>";
		if (empty($_POST['conference_sdate'][$val])) $msg[] = "<div class=\"error\"><span>Please provide Conference Start Date for record no $no.</span></div>";
		if (empty($_POST['conference_edate'][$val])) $msg[] = "<div class=\"error\"><span>Please provide Conference End Date for record no $no.</span></div>";
		if (empty($_POST['presentation_title'][$val])) $msg[] = "<div class=\"error\"><span>Please provide Title of Presentation for record no $no.</span></div>";		
	}
		
	if(empty($msg)) 
	{
		if (sizeof($_POST['conference_checkbox'])>0) {
			$curdatetime = date("Y-m-d H:i:s");
			while (list ($key,$val) = @each ($_POST['conference_checkbox'])) 
			{
				$conference_id = $_POST['conference_id'][$val];
				$conference = $_POST['conference'][$val];
				$location = $_POST['location'][$val];
				$conferenceSDate = $_POST['conference_sdate'][$val];
				$conferenceEDate = $_POST['conference_edate'][$val];
				$presentationStatus = $_POST['presentation_status'.$val];
				$presentationTitle = $_POST['presentation_title'][$val];
			
				$sql1 = "UPDATE pg_defense_conference
				SET conference = '$conference',
				location = '$location',
				conference_sdate = STR_TO_DATE('$conferenceSDate','%d-%b-%Y'), 
				conference_edate = STR_TO_DATE('$conferenceEDate','%d-%b-%Y'), 
				presentation_status = '$presentationStatus',
				presentation_title = '$presentationTitle',
				modify_by = '$user_id', modify_date = '$curdatetime'
				WHERE id = '$conference_id'
				AND student_matrix_no = '$user_id'";
				
				$dba->query($sql1); 
				
				
			}
			$msg[] = "<div class=\"success\"><span>The selected Conference detail has been updated successfully.</span></div>";
		}
		else {
			$msg[] = "<div class=\"error\"><span>Please tick which Conference record to update!</span></div>";
		}
	}
	unset($_POST);
}

if(isset($_POST['btnDelete']) && ($_POST['btnDelete'] <> ""))
{					
	if (sizeof($_POST['conference_checkbox'])>0) {
		$curdatetime = date("Y-m-d H:i:s");
		while (list ($key,$val) = @each ($_POST['conference_checkbox'])) 
		{
			$conference_id = $_POST['conference_id'][$val];
			
			$sql1 = "DELETE FROM pg_defense_conference
			WHERE id = '$conference_id'
			AND student_matrix_no = '$user_id'";
			
			$dba->query($sql1); 
			
			
		}
		$msg[] = "<div class=\"success\"><span>The selected Conference detail has been deleted successfully.</span></div>";
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please tick which Conference record to delete!</span></div>";
	}
	unset($_POST);
}

		
		
$sqlConference="SELECT id, conference, location, DATE_FORMAT(conference_sdate,'%d-%b-%Y') as conference_sdate, 
DATE_FORMAT(conference_edate,'%d-%b-%Y') as conference_edate, presentation_status, 
presentation_title
FROM  pg_defense_conference 
WHERE (pg_defense_id IS NULL OR pg_defense_id = '$defense_id')
AND pg_proposal_id='$proposal_id'
AND pg_thesis_id = '$thesis_id'
AND (reference_no = '$referenceNo' OR reference_no IS NULL)
AND student_matrix_no = '$user_id' 
AND ref_session_type_id = 'DEF'
ORDER BY conference_sdate DESC ";

$result = $db->query($sqlConference); 
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

  <form id="form1" name="form1" method="post" enctype="multipart/form-data">

	 <fieldset>
		<legend><strong>Participation In Conference</strong></legend>
		<table>
			<tr>
				<td><label>Conference <span style="color:#FF0000">*</span></label></td>
				<td><textarea name="add_conference" cols="40" rows="2" id="add_conference" type="text"></textarea></td>
			</tr>
			<tr>
				<td><label>Location <span style="color:#FF0000">*</span></label></td>
				<td><input name="add_conference_loc" type="text" id="add_conference_loc" size="50"></td>
			</tr>
			<tr>
				<td><label>Start Date <span style="color:#FF0000">*</span></label></td>
				<td><input name="add_conference_sdate" type="text" id="add_conference_sdate" size="10" readonly=""></td>
				<?	$jscript1 .= "\n" . '$( "#add_conference_sdate" ).datepicker({
												changeMonth: true,
												changeYear: true,
												yearRange: \'-100:+0\',
												dateFormat: \'dd-M-yy\'
											});';
					 
				?>
			</tr>
			<tr>
				<td><label>End Date <span style="color:#FF0000">*</span></label></td>
				<td><input name="add_conference_edate" type="text" id="add_conference_edate" size="10" readonly=""></td>
				<?	$jscript2 .= "\n" . '$( "#add_conference_edate" ).datepicker({
												changeMonth: true,
												changeYear: true,
												yearRange: \'-100:+0\',
												dateFormat: \'dd-M-yy\'
											});';
					 
				?>
			</tr>
			<tr>
				<td><label>Presentation (Y/N) <span style="color:#FF0000">*</span></label></td>
				<td><input type="radio" name="add_presentation_status" value="Y"> Yes
				<input type="radio" name="add_presentation_status" value="N" checked> No</td>
			</tr>
			<tr>
				<td><label>Title of Presentation <span style="color:#FF0000">*</span></label></td>
				<td><input name="add_presentation_title" type="text" id="add_presentation_title" size="50"></td>
			</tr>			
		</table>
		<table>
			<tr>
				<td><input type="submit" name="btnAdd" value="Add" /></td>
			</tr>
		</table>
		<table>
		<tr>							
			<td>Searching Results:- <?=$row_cnt?> record(s) found.</td></td>
		</tr>
		</table>
		<?if ($row_cnt <= 0) {?>
			<div id = "tabledisplay" style="overflow:auto; height:60px;">
		<?}
		else if ($row_cnt <= 1) {?>
			<div id = "tabledisplay" style="overflow:auto; height:150px;">
		<?}
		else if ($row_cnt <= 2) {?>
			<div id = "tabledisplay" style="overflow:auto; height:220px;">
		<?}
		else if ($row_cnt <= 3) {
			?>
			<div id = "tabledisplay" style="overflow:auto; height:300px;">
			<?
		}
		else {
			?>
			<div id = "tabledisplay" style="overflow:auto; height:340px;">
			<?
		}?>		
		<table border="1" cellpadding="3" cellspacing="3" width="100%" id="inputs10" class="thetable">
			  <tr align="left">
				<th align="center" width="5%"><label>Tick</label></td>
				<th align="center" width="5%"><label>No</label></td>
				<th align="center" width="10%"><label>Conference / Location <span style="color:#FF0000">*</span></label></th>
				<th align="center" width="10%"><label>Start Date / <br/>End Date <span style="color:#FF0000">*</span></label></th>
				<th align="center" width="15%"><label>Presentation <br/>(Y/N) <span style="color:#FF0000">*</span></label></th>
				<th align="center" width="55%"><label>Title of Presentation <span style="color:#FF0000">*</span></label></th>
			  </tr>

			<?php
			if ($row_cnt > 0) {
				
				$tmp_no = 0;
				while($row = mysql_fetch_array($result)) 					
				{ 
					?><tr>
							<input type="hidden" name="conference_id[]" id="conference_id" value="<?=$row['id'];?>" />
							<td align="center"><input type="checkbox" name="conference_checkbox[]" id="conference_checkbox" value="<?=$tmp_no;?>" /></td>
							<td align="center"><label><?=$tmp_no+1;?>.</label></td>
							<td><textarea name="conference[]" type="text" id="conference" cols="40" rows="2"><?=$row["conference"]?></textarea><br/><input name="location[]" type="text" id="location" value="<?=$row["location"];?>" size="50"></input></td>
							<td align="center"><input type="text" name="conference_sdate[]" id="conference_sdate<?=$tmp_no;?>" size="10" value="<?=$row["conference_sdate"];?>" readonly=""></input><br/>
							<?	$jscript1 .= "\n" . '$( "#conference_sdate' . $tmp_no . '" ).datepicker({
												changeMonth: true,
												changeYear: true,
												yearRange: \'-100:+0\',
												dateFormat: \'dd-M-yy\'
											});';
					 
							?>
							<input type="text" name="conference_edate[]" id="conference_edate<?=$tmp_no;?>" size="10" value="<?=$row["conference_edate"];?>" readonly=""></input></td>
							<?	$jscript2 .= "\n" . '$( "#conference_edate' . $tmp_no . '" ).datepicker({
												changeMonth: true,
												changeYear: true,
												yearRange: \'-100:+0\',
												dateFormat: \'dd-M-yy\'
											});';
					 
							?>
							<?if ($row["presentation_status"]=="Y")  {
								?>
								<td><input type="radio" name="presentation_status<?=$tmp_no;?>" value="Y" checked> Yes
								<input type="radio" name="presentation_status<?=$tmp_no;?>" value="N" > No</td>								
								<?
							}
							else {
								?>
								<td><input type="radio" name="presentation_status<?=$tmp_no;?>" value="Y"> Yes
								<input type="radio" name="presentation_status<?=$tmp_no;?>" value="N" checked> No</td>								
								<?
								
							}?>
							<td><input name="presentation_title[]" type="text" id="presentation_title" value="<?=$row["presentation_title"];?>" size="65"></input></td>
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
				<td><label>Notes:</label></td>
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
			<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../defense/new_defense.php?tid=<?=$thesis_id?>&pid=<?=$proposal_id?>&ref=<?=$referenceNo?>';" /></input></td>		
		</tr>
	</table>

  </form>
  	<script>
		<?=$jscript1;?>
		<?=$jscript2;?>
	</script>
</body>
</html>





