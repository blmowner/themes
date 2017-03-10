<?php

//include("../../../lib/common.php");
//checkLogin();

session_start();
$thesis_id=$_SESSION["thesis_id"];
$proposal_id=$_SESSION["proposal_id"];
$userid=$_SESSION['user_id'];

function runnum($column_name, $tblname) 
{ 
    global $db;
    
    $run_start = "001";
    
    $sql_slct_max = "SELECT MAX(SUBSTR($column_name,2,11)) AS run_id FROM $tblname";
    $sql_slct = $db;
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
    global $db;
    
    $run_start = "001";
    
    $sql_slct_max = "SELECT MAX($column_name) AS run_id FROM $tblname";
    $sql_slct = $db;
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

if(isset($_POST['btnSave']) && ($_POST['btnSave'] <> ""))
{		
	$thesis_title = $_POST['thesis_title'];
	$thesis_type = $_POST['thesis_type'];
	$introduction = $_POST['introduction'];
	$objective = $_POST['objective'];
	$description = $_POST['description'];
	$jobs_area1 = $_POST['jobs_area1'];
	$jobs_area2 = $_POST['jobs_area2'];
	$jobs_area3 = $_POST['jobs_area3'];
	$jobs_area4 = $_POST['jobs_area4'];
	$jobs_area5 = $_POST['jobs_area5'];
	$jobs_area6 = $_POST['jobs_area6'];
	
	$curdatetime = date("Y-m-d H:i:s");
	$meeting_detail_id = runnum2('id','pg_meeting_detail');
	
	$sqlUpdate1 = "UPDATE pg_thesis
				SET modify_by = '$user_id', modify_date = '$curdatetime', ref_thesis_status_id_proposal = 'INP'
				WHERE id = '$thesis_id'";

	$db->query($sqlUpdate1);
	
	$sqlUpdate2 = "UPDATE pg_proposal
				SET thesis_title = '$thesis_title', thesis_type = '$thesis_type', introduction = '$introduction', objective = '$objective', 
				description = '$description', modify_by = '$user_id', modify_date = '$curdatetime', discussion_status = '$discussion_status',
				report_date = '$curdatetime'
				WHERE id = '$proposal_id'
				AND verified_status in ('SAV')";
				
	$db->query($sqlUpdate2);
	
	// --- Job Area Category (START) ---
		$updateArea = "UPDATE pg_proposal_area
		SET pg_proposal_id = '".$proposal_id."',job_id1_area = '".$_POST['jobs_area1']."', job_id2_area = '".$_POST['jobs_area2']."', job_id3_area = '".$_POST['jobs_area3']."',
		job_id4_area = '".$_POST['jobs_area4']."',job_id5_area = '".$_POST['jobs_area5']."',job_id6_area = '".$_POST['jobs_area6']."', insert_date = now(), 
		insert_by = '".$_SESSION['user_id']."',modified_date = now(),modified_by = '".$_SESSION['user_id']."'
		WHERE id = '".$proposal_id."' ";
		$db_klas2->query($updateArea); //echo $updateArea;
		// --- Job Area Category (FINISH) ---
	
		
	for ($i=0; $i<sizeof($_POST['date']); $i++) {
	
	$myMeetingDate = $_POST['date'][$i]." ".$_POST['meeting_time'][$i];
	$myLecturer = $_POST['lecturer_name'][$i];
	$myRemarks = $_POST['remarks'][$i]; 
	
	$meeting_detail_id = runnum2('id','pg_meeting_detail');	

	
	$sqlUpdate3 = "INSERT INTO pg_meeting_detail (id,lecturer_name,meeting_sdate,remark,pg_proposal_id,
					insert_by,insert_date,modify_by,modify_date)
					VALUES ('$meeting_detail_id','$myLecturer',
					STR_TO_DATE('$myMeetingDate','%m/%d/%Y %h:%i'),'$myRemarks',
			'$proposal_id','$user_id','$curdatetime','$user_id','$curdatetime')";
	

	$db->query($sqlUpdate3); 			 
	}
	for ($i=0; $i<sizeof($_FILES['fileData']['name']); $i++) 
	{
		$upload_id = runnum2('fu_cd','file_upload_proposal');
		
		$file_name = $_FILES['fileData']['name'][$i];
		$fileType = $_FILES['fileData']['type'][$i];
		$fileSize = intval($_FILES['fileData']['size'][$i]);
		$fileData = file_get_contents($_FILES['fileData']['tmp_name'][$i]);
		if ($fileSize>0)
		{
			$sqlUpload = "INSERT INTO file_upload_proposal (
							fu_cd, 
							fu_document_filename, 
							fu_document_filetype, 
							fu_document_filedata,
							fu_document_thumbnail,
							insert_by,
							insert_date,
							modify_by,
							modify_date,
							pg_proposal_id,
							attachment_level)
							VALUES (
							'$upload_id',
							'".$file_name."', 
							'".$fileType."',
							'".mysql_escape_string($fileData)."',
							'',
							'$user_id',
							'$curdatetime',
							'$user_id',
							'$curdatetime',
							'$proposal_id',
							'S')";

			$db->query($sqlUpload);
		}
					
	}			

}
	
if(isset($_POST['btnSubmit']) && ($_POST['btnSubmit'] <> ""))
{			
		
	$thesis_title = $_POST['thesis_title'];
	$thesis_type = $_POST['thesis_type'];
	$introduction = $_POST['introduction'];
	$objective = $_POST['objective'];
	$description = $_POST['description'];
	$jobs_area1 = $_POST['jobs_area1'];
	$jobs_area2 = $_POST['jobs_area2'];
	$jobs_area3 = $_POST['jobs_area3'];
	$jobs_area4 = $_POST['jobs_area4'];
	$jobs_area5 = $_POST['jobs_area5'];
	$jobs_area6 = $_POST['jobs_area6'];
	
	$curdatetime = date("Y-m-d H:i:s");
	$meeting_detail_id = runnum2('id','pg_meeting_detail');
	
	$sqlUpdate1 = "UPDATE pg_thesis
				SET modify_by = '$user_id', modify_date = '$curdatetime', ref_thesis_status_id_proposal = 'INP'
				WHERE id = '$thesis_id'";

	$db->query($sqlUpdate1);
	
	$sqlUpdate2 = "UPDATE pg_proposal
				SET thesis_title = '$thesis_title', thesis_type = '$thesis_type', introduction = '$introduction', objective = '$objective', 
				description = '$description', modify_by = '$user_id', modify_date = '$curdatetime', discussion_status = '$discussion_status',
				report_date = '$curdatetime', verified_status = 'INP'
				WHERE id = '$proposal_id'
				AND verified_status in ('SAV')";

	$db->query($sqlUpdate2);
	
	
	// --- Job Area Category (START) ---
		/*$area = "INSERT INTO pg_proposal_area
		(id,pg_proposal_id,job_id1_area,job_id2_area,job_id3_area,job_id4_area,job_id5_area,job_id6_area,insert_date,insert_by,modified_date,modified_by)
		VALUES('".$proposal_id."','".$proposal_id."','".$_POST['jobs_area1']."','".$_POST['jobs_area2']."','".$_POST['jobs_area3']."','".$_POST['jobs_area4']."',
			'".$_POST['jobs_area5']."','".$_POST['jobs_area6']."',now(),'".$_SESSION['user_id']."',now(),'".$_SESSION['user_id']."')";
		$db_klas2->query($area); //echo $area;*/
		// --- Job Area Category (FINISH) ---
	
	// --- Job Area Category (START) ---
		$updateArea = "UPDATE pg_proposal_area
		SET pg_proposal_id = '".$proposal_id."',job_id1_area = '".$_POST['jobs_area1']."', job_id2_area = '".$_POST['jobs_area2']."', job_id3_area = '".$_POST['jobs_area3']."',
		job_id4_area = '".$_POST['jobs_area4']."',job_id5_area = '".$_POST['jobs_area5']."',job_id6_area = '".$_POST['jobs_area6']."', insert_date = now(), 
		insert_by = '".$_SESSION['user_id']."',modified_date = now(),modified_by = '".$_SESSION['user_id']."'
		WHERE id = '".$proposal_id."' ";
		$db_klas2->query($updateArea); //echo $updateArea;
		// --- Job Area Category (FINISH) ---
	
	for ($i=0; $i<sizeof($_POST['date']); $i++) {
	
	$myMeetingDate = $_POST['date'][$i]." ".$_POST['meeting_time'][$i];
	$myLecturer = $_POST['lecturer_name'][$i];
	$myRemarks = $_POST['remarks'][$i]; 
	
	$meeting_detail_id = runnum2('id','pg_meeting_detail');	
	
	$sqlUpdate3 = "INSERT INTO pg_meeting_detail (id,lecturer_name,meeting_sdate,remark,pg_proposal_id,
					insert_by,insert_date,modify_by,modify_date)
					VALUES ('$meeting_detail_id','$myLecturer',
					STR_TO_DATE('$myMeetingDate','%m/%d/%Y %h:%i'),'$myRemarks',
			'$proposal_id','$user_id','$curdatetime','$user_id','$curdatetime')";

	$db->query($sqlUpdate3); 			 
	}
	for ($i=0; $i<sizeof($_FILES['fileData']['name']); $i++) 
	{
		$upload_id = runnum2('fu_cd','file_upload_proposal');
		
		$file_name = $_FILES['fileData']['name'][$i];
		$fileType = $_FILES['fileData']['type'][$i];
		$fileSize = intval($_FILES['fileData']['size'][$i]);
		$fileData = file_get_contents($_FILES['fileData']['tmp_name'][$i]);
		if ($fileSize>0)
		{
			$sqlUpload = "INSERT INTO file_upload_proposal (
							fu_cd, 
							fu_document_filename, 
							fu_document_filetype, 
							fu_document_filedata,
							fu_document_thumbnail,
							insert_by,
							insert_date,
							modify_by,
							modify_date,
							pg_proposal_id,
							attachment_level)
							VALUES (
							'$upload_id',
							'".$file_name."', 
							'".$fileType."',
							'".mysql_escape_string($fileData)."',
							'',
							'$user_id',
							'$curdatetime',
							'$user_id',
							'$curdatetime',
							'$proposal_id',
							'S')";

			$db->query($sqlUpload);
		}
					
	}			
	//include("../email/email_submit_proposal.php");
	
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
	<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
	<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
	<script src="../../../lib/js/jquery.min2.js"></script>
    <script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>

</head>
<body>

<script>
function saveRec()
{
	saveStatus=true;
	var introduction = CKEDITOR.instances['introduction'].getData();
	var objective = CKEDITOR.instances['objective'].getData();
	var description = CKEDITOR.instances['description'].getData();
	
	if(document.form1.thesis_title.value=="")
	{
		alert("Please enter your Thesis / Project title.");
		return false;
	}
	
	if(document.form1.thesis_type.value=="")
	{
		alert("Please enter your Thesis / Project proposal type.");
		return false;
	}
	
	if(introduction.length==0)
	{
		alert("Please enter your thesis / project introduction.");
		return false;
	}
	
	if(objective.length==0)
	{
		alert("Please enter your thesis / project objective.");
		return false;
	}
	
	if(description.length==0)
	{
		alert("Please enter your thesis / project description");
		return false;
	}

	return saveStatus;
}					
</script>

<SCRIPT LANGUAGE="JavaScript">

function respConfirm () {
    var confirmSubmit = confirm("Are you confirm to submit?");
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


<?
$sql_thesis="SELECT pt.id AS thesis_id, pt.student_matrix_no,pt.status AS thesis_status,
				pp.id AS proposal_id, pp.thesis_title,pp.thesis_type, pp.objective, pp.introduction,pp.description,pp.discussion_status, 
				DATE_FORMAT(pp.verified_date,'%d-%b-%Y') AS verified_date, pp.verified_remarks, pp.verified_by,
				pp.verified_status AS proposal_status,pp.endorsed_by, DATE_FORMAT(pp.endorsed_date,'%d-%b-%Y') AS endorsed_date, 
				pp.endorsed_remarks, pp.status AS endorsed_status, 
				rps.description AS proposal_description, rps2.description AS endorsed_desc, ne1.name AS verified_name,
				ne2.name AS endorsed_name,
				DATE_FORMAT(pp.cancel_requested_date,'%d-%b-%Y') AS cancel_requested_date,
				DATE_FORMAT(pp.cancel_approved_date,'%d-%b-%Y') AS cancel_approved_date, pp.cancel_approved_by, 
				ne3.name AS cancel_approved_name, pp.cancel_approved_remarks 
				FROM pg_thesis pt 
				LEFT JOIN pg_proposal pp ON (pp.pg_thesis_id=pt.id) 
				LEFT JOIN ref_proposal_status rps ON (rps.id = pp.verified_status) 
				LEFT JOIN ref_proposal_status rps2 ON (rps2.id = pp.status) 
				LEFT JOIN new_employee ne1 ON (ne1.empid = pp.verified_by) 
				LEFT JOIN new_employee ne2 ON (ne2.empid = pp.endorsed_by) 
				LEFT JOIN new_employee ne3 ON (ne3.empid = pp.cancel_approved_by)
				WHERE pt.student_matrix_no = '$user_id'
				AND pp.verified_status in ('SAV','INP','APP','AWC','REQ','DIS','REV','WIT','CAN')				
				AND pp.archived_status is null
				AND pt.ref_thesis_status_id_proposal in ('INP','APP','AWC','APC','DIS')
				ORDER BY pt.id";

//echo "sql_thesis ".$sql_thesis;
$db->query($sql_thesis);
$row_personal=$db->fetchArray();

$thesis_id=$row_personal['thesis_id'];
$student_matrix_no=$row_personal['student_matrix_no'];
$thesis_status=$row_personal["thesis_status"];
$proposal_id=$row_personal['proposal_id'];
$thesis_title=$row_personal["thesis_title"];
$thesis_type=$row_personal["thesis_type"];
$objective=$row_personal["objective"];
$introduction=$row_personal["introduction"];
$description=$row_personal["description"];
$discussion_status=$row_personal["discussion_status"];
$verified_by=$row_personal['verified_by'];
$verified_date=$row_personal['verified_date'];
$verified_remarks=$row_personal['verified_remarks'];
$proposal_status=$row_personal["proposal_status"];
$endorsed_by=$row_personal['endorsed_by'];
$endorsed_date=$row_personal['endorsed_date'];
$endorsed_remarks=$row_personal['endorsed_remarks'];
$endorsed_status=$row_personal['endorsed_status'];
$endorsed_desc=$row_personal['endorsed_desc'];
$proposal_description=$row_personal["proposal_description"];
$verified_name=$row_personal['verified_name'];
$endorsed_name=$row_personal['endorsed_name'];
$cancel_requested_date=$row_personal['cancel_requested_date'];
$cancel_approved_date=$row_personal['cancel_approved_date'];
$cancel_approved_by=$row_personal['cancel_approved_by'];
$cancel_approved_name=$row_personal['cancel_approved_name'];
$cancel_approved_remarks=$row_personal['cancel_approved_remarks'];


?>

<script>
$(function() {
	$( "#datepickerFirst" ).datepicker({
		changeMonth: true,
		changeYear: true,
		yearRange: '-100:+0'
		});
});
</script>

<script type="text/javascript">
$(function() {

	var i = $('input').size() + 1;
	
	//###################################### funtion add more document files ######################################//
	$('a.add-file').click(function() {

		$('<tr><td width="40"><input type="checkbox" class="case_file" /></td><td width="30"><input type=\"text\" name=\"file_name[]\" size="40" /></td><td width="30"><input name="fileData[]" type="file" size="40"/></td><td width="30"><label size="30"></label></td></tr>').animate({ opacity: "show" }, "slow").appendTo('#inputs9');
		i++;
	});
	
	$('a.remove-file').click(function() {
        $(".case_file:checked").each(function() {
            $(this).parent().parent().remove()
        });
    });
    
    // add multiple select / deselect functionality
    $("#selectall_file").click(function () {
          $('.case_file').attr('checked', this.checked);
    });
 
    // if all checkbox are selected, check the selectall checkbox and viceversa
    $(".case_file").click(function(){
 
        if($(".case_file").length == $(".case_file:checked").length) {
            $("#selectall_file").attr("checked", "checked");
        } else {
            $("#selectall_file").removeAttr("checked");
        }
 
    });
	
	//###################################### end of funtion add more document files ######################################//
	
});

</script>	
					
<script type="text/javascript">
$(function() 
{
	var i = $('input').size() + 1;
	
	//###################################### funtion add more @ certification ######################################//
	$('a.add-certification').click(function() {

	$('<tr><td width="26" align="center"><input type="checkbox" name="cbDelFile2" class="case_certificate" /></td><td align="center" width="77" ><input type="text" name="date[]" readonly id="datepicker'+i+'" /></td><td width="88" height="1" class="tbmain"><select name="meeting_time[]" size="1" ><option value="" selected></option><option value="07:00">07:00 AM</option><option value="07:30">07:30 AM</option><option value="08:00">08:00 AM</option><option value="08:30">08:30 AM</option><option value="09:00">09:00 AM</option><option value="09:30">09:30 AM</option><option value="10:00">10:00 AM</option><option value="10:30">10:30 AM</option><option value="11:00">11:00 AM</option><option value="11:30">11:30 AM</option><option value="12:00">12:00 PM</option><option value="12:30">12:30 PM</option><option value="01:00">01:00 PM</option><option value="01:30">01:30 PM</option><option value="02:00">02:00 PM</option><option value="02:30">02:30 PM</option><option value="03:00">03:00 PM</option><option value="03:30">03:30 PM</option><option value="04:00">04:00 PM</option><option value="04:30">04:30 PM</option><option value="05:00">05:00 PM</option><option value="05:30">05:30 PM</option><option value="06:00">06:00 PM</option><option value="06:30">06:30 PM</option><option value="07:00">07:00 PM</option><option value="07:30">07:30 PM</option><option value="08:00">08:00 PM</option><option value="08:30">08:30 PM</option><option value="09:00">09:00 PM</option><option value="09:30">09:30 PM</option><option value="10:00">10:00 PM</option><option value="10:30">10:30 PM</option><option value="11:00">11:00 PM</option><option value="11:30">11:30 PM</option><option value="12:00">12:00 PM</option></select></td><td width="170"><input type="text" name="lecturer_name[]"/></td><td width="257"><textarea name="remarks[]" cols="50" id="remarks"></textarea></td><td width="29"><label></label></td></tr>').animate({ opacity: "show" }, "slow").appendTo('#inputs10');

	$("#datepicker"+i).datepicker({
		changeMonth: true,
		changeYear: true,
		yearRange: '-100:+0'
		});

		i++;		
	});
    
	
	$('a.remove-certification').click(function() {
        $(".case_certificate:checked").each(function() {
            $(this).parent().parent().remove()
        });	
    });
    
    // add multiple select / deselect functionality
    $("#selectall_certificate").click(function () 
	{
          $('.case_certificate').attr('checked', this.checked);
    });
 
    // if all checkbox are selected, check the selectall checkbox and viceversa
    $(".case_certificate").click(function()
	{
		if($(".case_certificate").length == $(".case_certificate:checked").length) 
		{
            $("#selectall_certificate").attr("checked", "checked");
        } else {
            $("#selectall_certificate").removeAttr("checked");
        }
    });
	//###################################### end of funtion add more @ certification ######################################//
});
</script>

<script>
function getUpdSP(id,frm)
{
	// START EDIT BY AD 2014-08-25
	var winMe = window.open("edit_proposal.php?thesis_id="+id+"&form="+frm,"aform","dependent=no,width=900,height=900,resizable=yes,scrollbars=yes");
	winMe.focus();
}
</script>
	<form id="form1" name="form1" method="post" enctype="multipart/form-data">
	<input type="hidden" name="thesis_id" id="thesis_id" value="<?=$thesis_id; ?>">
  
	
<?
	$result_sql_thesis=$db->query($sql_thesis);
	$row_cnt = mysql_num_rows($result_sql_thesis);
	//echo "sql_thesis ".$sql_thesis;
	//echo "proposal_status ".$proposal_status; 
	if ($row_cnt>0) 
	{//echo "has record";
		if ($proposal_status=='APP' || $proposal_status=='AWC') 
		{
	
			?>
				<fieldset>
				<legend><strong>Verification by Faculty</strong></legend>
					<table>
						<tr>
							<td>Proposal Status</td>
							<td>:</td>
							<td><?=$proposal_description;?></td>
						</tr>
						<tr>
							<td>Verification Date</td>
							<td>:</td>
							<td><label><?=$verified_date; ?></label></td>
						</tr>
						<tr>
							<td>Verified By</td>
							<td>:</td>
							<td><label><?=$verified_name; ?></label></td>
						</tr>
						<tr>
							<td> Remarks </td>
							<td>:</td>
							<td><label class="ckeditor"><?=$verified_remarks;?></label></td>							
						</tr>
						<tr>
							<td> Attachment by Faculty </td>
							<td>:</td>							
							<?php
									$sqlUpload="SELECT * FROM file_upload_proposal 
									WHERE pg_proposal_id='$proposal_id' 
									AND attachment_level='F' ";			

									$result = $db->query($sqlUpload); //echo $sql;
									$row_cnt = mysql_num_rows($result);
									if ($row_cnt>0)
									{
										?><td align="left"><?
										while($row = mysql_fetch_array($result)) 					
										{ 
											?>
												<a href="../thesis/download.php?fc=<?=$row["fu_cd"];?>&al=F"><?=$row["fu_document_filename"];?><img src="../images/download.png" width="20" height="19" style="border:0px;" title="Download"></a><br/>	
										<?}										
									}
									else {
										?><td>No attachment<br/></td><?
									}
								?>
						</tr>
					</table>
				</fieldset>
					<br/>
				<fieldset>
				<legend><strong>Endorsement by Senate</strong></legend>
					<table>
						<tr>
							<td>Endorsement Status</td>							
							<td>:</td>
							<td><label><?=$endorsed_desc; ?></label></td>
						</tr>
						<tr>
							<td>Endorsement Date</td>
							<td>:</td>
							<?if ($endorsed_date=='00-00-00'){
							?>
								<td><label></label></td>
							<?	
							}
							else 
							{
							?>
								<td><label><?=$endorsed_date; ?></label></td>
							<?
							}?>
							
						</tr>
						<tr>
							<td>Endorsed By</td>
							<td>:</td>
							<td><label><?=$endorsed_name; ?></label></td>
						</tr>
						<tr>
							<td> Remarks </td>
							<td>:</td>
							<td><label class="ckeditor"><?=$endorsed_remarks;?></label></td>							
						</tr>						
					</table>
					</fieldset>
					<br/>
					<table>
						<tr>
							<?if (($proposal_status=='APP' || $proposal_status=='AWC') && ($endorsed_status=='APP' || $endorsed_status=='APC'))
							{?>
								<td><strong>Note:</strong> Your thesis proposal has been approved by the Senate. You may start to <a href="">prepare and submit </a>your monthly progress report.</td>
							<?}
							elseif (($proposal_status=='APP' || $proposal_status=='AWC') && $endorsed_status=='DIS') {
							?>
								<td><strong>Note:</strong> Your thesis proposal has been disapproved by the Senate. You may need to  <a href="../thesis/new_proposal.php?uid=<?php echo $userid;?>&pid=<?php echo $proposal_id;?>&tid=<?php echo $thesis_id;?>"><img src="../images/click_here.jpg" width="60" height="30" style="border:0px;" title="Plase click here"></a> to create another one and re-submit.</td>
							<?}
							else{
							?>
								<td><strong>Note:</strong> Your thesis proposal is pending for endorsement by the Senate. Please check it again later. </td>
							<?}?>
							
						</tr>
					</table>
			<?
		
		}	
		else if ($proposal_status=='WIT'){?>
				<fieldset>
				<legend><strong><span style="color:#FF0000">Notification Message</span></strong></legend>	
				<table>
						<tr>
							<td>Your thesis proposal <strong>(Ref. No.: <?=$thesis_id?>)</strong> is pending with Faculty for cancellation approval.</td>
						</tr>
					</table>
				</fieldset>
		<?	}
		else if ($proposal_status=='INP'){?>
				<fieldset>
				<legend><strong><span style="color:#FF0000">Notification Message</span></strong></legend>	
				<table>
						<tr>
							<td>Your thesis proposal <strong>(Ref. No.: <?=$thesis_id?>)</strong> is pending with Faculty for verification.<br/><br/>
							You can request for withdraw or cancel it before the Faculty start to review and provide the feedback.<br/>
							Please click here <a href="../thesis/request_cancel_proposal.php?tid=<?=$thesis_id;?>&pid=<?=$proposal_id;?>" >
													<img src="../images/cancel.jpg" width="40" height="40" style="border:0px;" title="Request cancellation for thesis proposal submission"></a>if you want to proceed.</td>
						</tr>
					</table>
				</fieldset>
		<?	}
		else if ($proposal_status=='REQ')
		{ 
			
			?>
		 <fieldset>
		 <legend><strong>Verification by Faculty</strong></legend>
			<table>
				<tr>
					<td>Proposal Status</td>
					<td>:</td>
					<td><?=$proposal_description;?></td>
				</tr>
				<tr>
					<td>Verified Date</td>
					<td>:</td>
					<td><label><?=$verified_date; ?></label></td>
				</tr>
				<tr>
					<td>Verified By</td>
					<td>:</td>
					<td><label><?=$verified_name; ?></label></td>
				</tr>
				<tr>
					<td> Remarks </td>
					<td>:</td>
					<td><label class="ckeditor"><?=$verified_remarks; ?></label></td>
				</tr>
				<tr>
					<tr>
							<td> Attachment by Faculty </td>
							<td>:</td>							
							<?php
									$sqlUpload="SELECT * FROM file_upload_proposal 
									WHERE pg_proposal_id='$proposal_id' 
									AND attachment_level='F' ";			

									$result = $db->query($sqlUpload); //echo $sql;
									$row_cnt = mysql_num_rows($result);
									if ($row_cnt>0)
									{
										?><td align="left"><?
										while($row = mysql_fetch_array($result)) 					
										{ 
											?>
												<a href="../thesis/download.php?fc=<?=$row["fu_cd"];?>&al=F"><?=$row["fu_document_filename"];?><img src="../images/download.png" width="20" height="19" style="border:0px;" title="Download"></a><br/>	
										<?}										
									}
									else {
										?><td>No attachment<br/></td><?
									}
								?>
				</tr>
			</table>
			<br/>
			</fieldset>
			<table>
				<tr>
					<td><strong>Note: </strong>Your thesis proposal has been reviewed by the Faculty and require changes. Please <a href="../thesis/edit_proposal.php?id=<?=$thesis_id;?>&pid=<?=$proposal_id;?>"><img src="../images/click_here.jpg" width="60" height="30" style="border:0px;" title="Please click here"></a> to update it and re-submit.</td>
				</tr>
			</table>					
		
			<?
				
		}
		else if ($proposal_status=='SAV'){?>
		
			
			<table>
				<tr>
					<td><strong>Notes:</strong><td>
				</tr>
				<tr>
					<td>(1) This form should be submitted to MSU Graduate School of Management (GSM) upon completing of the Research Methodology and before student starts the project.</td>
				</tr>
				<tr>
					<td>(2) Students are advised to seek the lecturer's advice before proceeding with the proposal.</td>
				</tr>
				<tr>
					<td>(3) Student should plan on 6-month's time from the Official Approval Date to complete the Final Project.</td>
				</tr>
				<tr>
					<td>(4) As refer to MBA rules, No candidate with CGPA below 3.0 shall be eligible to register for the Final Project of the degree unless recommended by the Board of Examiners.</td>
				</tr>
				<tr>
					<td>(5) Appointment of supervisor is subject to the recommendation from the Director of MSU Graduate School of Management (GSM).</td>
				</tr>				
			</table>
			<br/>		   
			<fieldset>
				<legend><strong>Outline of Proposed Research/Case Study</strong></legend>	
			<table>
					<table>
						<tr>
							<td>Verification Status by Faculty</td>
							<td>:</td>
							<td><label><?=$proposal_description; ?></label></td>
						</tr>
						<tr>
							<td>Thesis ID</td>
							<td>:</td>
							<td><label><?=$thesis_id; ?></label></td>
						</tr>
						<tr>
							<td><span style="color:#FF0000">*</span> Thesis / Project Title</td>
							<td>:</td>
							<td><input type="text" name="thesis_title" size="100" maxlength="100"  id="thesis_title" value="<?=$thesis_title;?>"></td>
						</tr><tr>
							<td>Proposal Type</td>
							<td>:</td>
							<td>
								<?php if($thesis_type=='R')	{	?>
								<input type="radio" name="thesis_type" value="R" checked>Research
								<input type="radio" name="thesis_type" value="C">Case Study						
								<input type="radio" name="thesis_type" value="P">Project	
								<?php	}	else if ($thesis_type=='C')	{	?>
									<input type="radio" name="thesis_type" value="R">Research
									<input type="radio" name="thesis_type" value="C" checked >Case Study
									<input type="radio" name="thesis_type" value="P">Project
								<?php	}	else if ($thesis_type=='P')	{	?>
									<input type="radio" name="thesis_type" value="R">Research
									<input type="radio" name="thesis_type" value="C">Case Study
									<input type="radio" name="thesis_type" value="P" checked>Project
								<?php	}	else {	?>
									<input type="radio" name="thesis_type" value="R" checked>Research
									<input type="radio" name="thesis_type" value="C">Case Study
									<input type="radio" name="thesis_type" value="P">Project
								<?php	}
								?> 
							</td>	
						</tr>
						<tr>
							<td><span style="color:#FF0000">*</span> Introduction</td>
							<td></td>
							<td><textarea name="introduction" class="ckeditor"><?=$introduction; ?></textarea></td>
						</tr>
						<tr>
							<td><span style="color:#FF0000">*</span> Objective</td>
							<td></td>
							<td><textarea name="objective" class="ckeditor" ><?=$objective; ?></textarea></td>
						</tr>
						<tr>
							<td><span style="color:#FF0000">*</span> Brief Description</td>
							<td></td>
							<td><textarea name="description" class="ckeditor" > <?=$description; ?></textarea></td>
						</tr>
					</table>
					<table>
						<tr>
							<td><p>Have you discussed about your research/case study to any Lecturer of MSU? </p>       </td>
							<td><p>
							<?php if($discussion_status=='Y')	{	?>
								<input type=radio name=discussion_status value="Y" checked > Yes
								<input type=radio name=discussion_status value="N" > No
							<?php	}	else {	?>
								<input type=radio name=discussion_status value="Y" > Yes
								<input type=radio name=discussion_status value="N" checked > No
							<?php	} ?> 
							</p></td>
						</tr>
					</table>
					<br />

					<?//php if($discussion_status=='Y')	{	
						?>
						<fieldset>
							<legend><strong>Discussion Details</strong></legend>

							<?php ?> <p>
							<a href="javascript:void(0)" class="add-certification add_btn" ;>Add</a>&nbsp; 
							<a href="javascript:void(0)" class="remove-certification del_btn" ;>Delete</a>
							</p><?php ?>
							<table border="1" cellpadding="3" cellspacing="3" width="100%" id="inputs10" class="thetable">
								  <tr align="left">
									<th width="26" align="center"><input type="checkbox" name="cbDelFile22" id="selectall_certificate" /></td>
									<th width="77" align="center">Date</th>
									<th width="88" align="center">Time</th>
									<th width="170">Lecturer Name</th>
									<th width="257">Remarks</th>
									<th width="29">Action</th>
								  </tr>

								<?php
									$sqlMeeting="SELECT pmd.id,pmd.lecturer_name, DATE_FORMAT(pmd.meeting_sdate,'%d/%m/%Y') as date,
									DATE_FORMAT(pmd.meeting_sdate,'%h:%i %p') as time, pmd.remark, pmd.insert_by, pmd.insert_date 
									FROM  pg_meeting_detail pmd  
									WHERE pmd.pg_proposal_id='$proposal_id' 
									ORDER BY pmd.meeting_sdate DESC ";			
									
									$result = $db->query($sqlMeeting); //echo $sql;
				
										while($row = mysql_fetch_array($result)) 					
										{ 
											?><tr>
													<td align="center" width="30"><input type="checkbox" class="case_certificate" name="cbDelFile[]" /><input type="hidden" name="delFile[]" value="<?=$row['document_id'];?>" /></td>
													<td align="center" width="116"><label name="date[]" ></label><?=$row["date"];?></td>
													<td align="center" width="124"><label name="meeting_time[]"></label><?=$row["time"];?></td>
													<td align="left" width="185"><label name="lecturer_name[]" ></label><?=$row["lecturer_name"];?></td>
													<td align="left" width="340"><label name="remarks[]" size="50" ></label><?=$row["remark"];?></td>		
													<td width="34" align="center"></a>
												<a href="delete_meeting_detail.php?id=<?=$row["id"];?>">
												<img src="../images/delete_calendar.jpg" width="20" height="19" style="border:0px;" title="Delete Meeting Information" ></a></td>		
												</tr>
										<?} ?> 	
							</table>
						</fieldset>

					<?//}?>
					<br/>
					<? $sqlLookupJobArea = "SELECT jobarea, area
											FROM job_list_category
											ORDER By jobarea";?>
											
					<? $sqlPgProposalArea = "SELECT pg_proposal_id,job_id1_area,job_id2_area,job_id3_area,job_id4_area,job_id5_area,job_id6_area
											FROM pg_proposal_area
											WHERE pg_proposal_id = '$proposal_id'"; ?>
					
					<br />
					<fieldset style="width:800px">
					<legend><strong>Proposal Areas</strong></legend>	
						<table  align="center">
						<tr>
						<td width="70" nowrap><font color="#FF0000">*</font><b>Area 1</b></td>
						<td width="134">
						  <select name="jobs_area1" id="jobs_area1"><option value="" selected="selected">--Please Select--</option>
					<?php

											
						$rsLookupJobArea = $db->query($sqlLookupJobArea);
						
						$rsPgProposalArea = $dbf->query($sqlPgProposalArea);						
						$dbf->next_record();
						$jobArea1=$dbf->f('job_id1_area');						
						
						while ($db->next_record()) {
							$jobarea=$db->f('jobarea');
							$area=$db->f('area');
							if ($jobArea1==$jobarea) {
								?><option value="<?=$jobarea?>" selected="selected"><?=$area?></option><?
							}
							else {
								?><option value="<?=$jobarea?>"><?=$area?></option><?
							}
						};?>
					</select></td>
					
						<td width="70" nowrap><b>Area 4</b></td>
						<td width="134">
							<select name="jobs_area4" id="jobs_area4"><option value="" selected="selected">--Please Select--</option>
								<?php

											
						$rsLookupJobArea = $db->query($sqlLookupJobArea);
						
						$rsPgProposalArea = $dbf->query($sqlPgProposalArea);						
						$dbf->next_record();
						$jobArea4=$dbf->f('job_id4_area');						
						
						while ($db->next_record()) {
							$jobarea=$db->f('jobarea');
							$area=$db->f('area');
							if ($jobArea4==$jobarea) {
								?><option value="<?=$jobarea?>" selected="selected"><?=$area?></option><?
							}
							else {
								?><option value="<?=$jobarea?>"><?=$area?></option><?
							}
						};?>
						</select></td>
					</tr>
					
					<tr>
						<td width="70" nowrap><b>Area 2</b></td>
						<td width="134">
						  <select name="jobs_area2" id="jobs_area2"><option value="" selected="selected">--Please Select--</option>
					<?php

											
						$rsLookupJobArea = $db->query($sqlLookupJobArea);
						
						$rsPgProposalArea = $dbf->query($sqlPgProposalArea);						
						$dbf->next_record();
						$jobArea2=$dbf->f('job_id2_area');						
						
						while ($db->next_record()) {
							$jobarea=$db->f('jobarea');
							$area=$db->f('area');
							if ($jobArea2==$jobarea) {
								?><option value="<?=$jobarea?>" selected="selected"><?=$area?></option><?
							}
							else {
								?><option value="<?=$jobarea?>"><?=$area?></option><?
							}
						};?>
					</select></td>
					
						<td width="70" nowrap><b>Area 5</b></td>
						<td width="134">
							<select name="jobs_area5" id="jobs_area5"><option value="" selected="selected">--Please Select--</option>
								<?php

											
						$rsLookupJobArea = $db->query($sqlLookupJobArea);
						
						$rsPgProposalArea = $dbf->query($sqlPgProposalArea);						
						$dbf->next_record();
						$jobArea5=$dbf->f('job_id5_area');						
						
						while ($db->next_record()) {
							$jobarea=$db->f('jobarea');
							$area=$db->f('area');
							if ($jobArea5==$jobarea) {
								?><option value="<?=$jobarea?>" selected="selected"><?=$area?></option><?
							}
							else {
								?><option value="<?=$jobarea?>"><?=$area?></option><?
							}
						};?>
						</select></td>
					</tr>
					
					<tr>
						<td width="70" nowrap><b>Area 3</b></td>
						<td width="134">
						  <select name="jobs_area3" id="jobs_area3"><option value="" selected="selected">--Please Select--</option>
					<?php

											
						$rsLookupJobArea = $db->query($sqlLookupJobArea);
						
						$rsPgProposalArea = $dbf->query($sqlPgProposalArea);						
						$dbf->next_record();
						$jobArea3=$dbf->f('job_id3_area');						
						
						while ($db->next_record()) {
							$jobarea=$db->f('jobarea');
							$area=$db->f('area');
							if ($jobArea3==$jobarea) {
								?><option value="<?=$jobarea?>" selected="selected"><?=$area?></option><?
							}
							else {
								?><option value="<?=$jobarea?>"><?=$area?></option><?
							}
						};?>
					</select></td>
					
						<td width="70" nowrap><b>Area 6</b></td>
						<td width="134">
							<select name="jobs_area6" id="jobs_area6"><option value="" selected="selected">--Please Select--</option>
								<?php

											
						$rsLookupJobArea = $db->query($sqlLookupJobArea);
						
						$rsPgProposalArea = $dbf->query($sqlPgProposalArea);						
						$dbf->next_record();
						$jobArea6=$dbf->f('job_id6_area');						
						
						while ($db->next_record()) {
							$jobarea=$db->f('jobarea');
							$area=$db->f('area');
							if ($jobArea6==$jobarea) {
								?><option value="<?=$jobarea?>" selected="selected"><?=$area?></option><?
							}
							else {
								?><option value="<?=$jobarea?>"><?=$area?></option><?
							}
						};?>
						</select></td>
					</tr>
						</table>
						
					</fieldset>
					
					
					
					<div>
		   
				   <fieldset style="width:800px">
				   <legend><strong>Attachment</strong></legend>
				   <p>
							<a href="javascript:void(0)" class="add-file add_btn">Add </a> &nbsp; 
							<a href="javascript:void(0)" class="remove-file del_btn">Delete </a> </p>
									
							<table border="1" cellpadding="3" cellspacing="3" width="100%" id="inputs9" class="thetable">
								<tr>
									<th><input type="checkbox" id="selectall_certificate" /></th>
									<th><span class="labeling">Document Description</span></th>
									<th><span class="labeling">Upload File</span></th>
									<th><span class="labeling">Action</span></th>
								</tr>
									
							<?
											$sqlUpload="SELECT * FROM file_upload_proposal 
											WHERE pg_proposal_id='$proposal_id' 
											AND attachment_level='S' ";			
											
											$result = $db_klas2->query($sqlUpload); //echo $sql;
											$varRecCount=0;					
											while($row = mysql_fetch_array($result)) 					
											{ 
												$varRecCount++;
												?><tr>
														<td align="center"><input type="checkbox" class="case_file" name="cbDelFile[]" /><input type="hidden" name="delFile[]" value="<?=$row["fu_cd"];?>" /></td>
														<td><label name="file_name[]" size="40" ></label><?=$row["fu_document_filename"];?></td>
														<td align="left"></td>
														<td><a href="download.php?fc=<?=$row["fu_cd"];?>&al=S" target="_blank" onMouseOver="toolTip('<?=$row["fu_document_filename"];?>', 300)" onMouseOut="toolTip()" align="center">
														<img src="../images/view_doc.jpg" width="20" height="19" style="border:0px;" title="View document"></a>
														<a href="delete_attachment_proposal.php?fc=<?=$row["fu_cd"];?>&al=S" >
														<img src="../images/delete_doc.jpg" width="20" height="19" style="border:0px;" title="Delete document"></a></td>
													</tr>										
											<?}?>							
								
								
							</table>
							<br/>
							
				</fieldset>
				   </div>
				</tr>
			</table>
			<?$_SESSION['thesis_id']=$thesis_id;?>
			<?$_SESSION['proposal_id']=$proposal_id;?>
		
			<table>
				<tr>
					<td><input type="submit" name="btnSave" value="Save as Draft"/></td>
					<td><input type="submit" name="btnSubmit" value="Submit" onClick="return respConfirm()"/>   </td>
				</tr>
			</table>
			<table>
				<tr>
					<td><span style="color:#FF0000">*</span> - is a required field. </td>
				</tr>
			</table>
			</fieldset>
			<?
	

			
		}
		else if ($proposal_status=='DIS'){?>

		<fieldset>
		 <legend><strong>Verification by Faculty</strong></legend>
			<table>
				<tr>
					<td>Proposal Status</td>
					<td>:</td>
					<td><label><?=$proposal_description; ?></label></td>
				</tr>
				<tr>
					<td>Verified Date</td>
					<td>:</td>
					<td><label><?=$verified_date;?></label></td>
				</tr>
				<tr>
					<td>Verified By</td>
					<td>:</td>
					<td><label><?=$verified_name;?></label></td>
				</tr>
				<tr>
					<td> Remarks </td>
					<td>:</td>
					<td><label class="ckeditor"><?=$verified_remarks; ?></label></td>
				</tr>			
				<tr>
					<td> Attachment by Faculty </td>
					<td>:</td>							
					<?php
							$sqlUpload="SELECT * FROM file_upload_proposal 
							WHERE pg_proposal_id='$proposal_id' 
							AND attachment_level='F' ";			

							$result = $db->query($sqlUpload); //echo $sql;
							$row_cnt = mysql_num_rows($result);
							if ($row_cnt>0)
							{
								?><td align="left"><?
								while($row = mysql_fetch_array($result)) 					
								{ 
									?>
										<a href="download.php?fc=<?=$row["fu_cd"];?>&al=F"><?=$row["fu_document_filename"];?><img src="../images/download.png" width="20" height="19" style="border:0px;" title="Download"></a><br/>	
								<?}										
							}
							else {
								?><td>No attachment<br/></td><?
							}
						?>
				</tr>
			</table>
			</fieldset>
			<br/>
			<table>
				<tr>
					<td><strong>Note: </strong>Your thesis proposal has been disapproved by the Faculty. You may need to <a href="../thesis/new_proposal.php?uid=<?php echo $userid;?>&pid=<?php echo $proposal_id;?>&tid=<?php echo $thesis_id;?>"><img src="../images/click_here.jpg" width="60" height="30" style="border:0px;" title="Please click here"></a> to create another one and re-submit.</td>
				</tr>
			</table>
		
			<?
		}
		
		else {//$proposal_status=='CAN'
			?>
		<fieldset>
		 <legend><strong>Verification by Faculty</strong></legend>
			<table>
				<tr>
					<td>Proposal Status</td>
					<td>:</td>
					<td><label><?=$proposal_description; ?></label></td>
				</tr>
				<tr>
					<td>Requested Date</td>
					<td>:</td>
					<td><label><?=$cancel_requested_date;?></label></td>
				</tr>
				<tr>
					<td>Approved By</td>
					<td>:</td>
					<td><label><?=$cancel_approved_name;?></label></td>
				</tr>
				<tr>
					<td>Approved Date</td>
					<td>:</td>
					<td><label><?=$cancel_approved_date;?></label></td>
				</tr>
				<tr>
					<td> Remarks by Faculty</td>
					<td>:</td>
					<td><label class="ckeditor"><?=$cancel_approved_remarks; ?></label></td>
				</tr>							
			</table>
			</fieldset>
			<br/>
			<table>
				<tr>
					<td><strong>Note: </strong>Your request to cancel the proposal has been approved by the Faculty. You may need to <a href="../thesis/edit_proposal.php?id=<?=$thesis_id;?>&pid=<?=$proposal_id;?>"><img src="../images/click_here.jpg" width="60" height="30" style="border:0px;" title="Please click here"></a> to edit it again and re-submit.</td>
				</tr>
			</table>
		
			<?
		}
		
	}
	else {//echo "has no record";?>
		
		<fieldset>
			<legend><strong><span style="color:#FF0000">Notification Message</span></strong></legend>
			<table>
				<tr>			
					<td>There is no thesis proposal available to view. You may need <a href="../thesis/new_proposal.php?uid=<?=$userid;?>&pid=<?php echo $proposal_id;?>&tid=<?php echo $thesis_id;?>"><img src="../images/click_here.jpg" width="60" height="30" style="border:0px;" title="Plase click here"></a> to create a new one and submit. </td>							
					
				</tr>
			</table>
		</fieldset>
	<?}?>

  </form>
</body>
</html>




