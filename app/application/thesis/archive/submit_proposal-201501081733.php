<?php

include("../../../lib/common.php");
checkLogin();

session_start();
$thesis_id=$_SESSION["thesis_id"];
$proposal_id=$_SESSION["proposal_id"];
$userid=$_SESSION['user_id'];

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

//echo $run_start;

/*if(isset($_POST['btnSave']) && ($_POST['btnSave'] <> ""))
	{	
		//$sqlselect="SELECT semester_id FROM student_program where matrix_no='$user_id'";
		$sqlselect="SELECT semester_id FROM student_program where matrix_no='$user_id'";
		$dbsel=$db_klas2;
		$dbsel->query($sqlselect); //echo $sql;
		$dbsel->next_record();
		$semester_id=$dbsel->f("semester_id");
		
		//echo $thesis_id=$dbsel->f("semester_id")+$run_start;
		$curdatetime = date("Y-m-d H:i:s");
		$thesis_id = "T".runnum('id','pg_thesis');
		$proposal_id = "P".runnum('id','pg_proposal');
		$meeting_detail_id = runnum('id','pg_meeting_detail');
		
		$sqlsubmit2="INSERT INTO pg_thesis(id,status, student_matrix_no,insert_by,insert_date, modify_by,modify_date,ref_thesis_status_id_proposal)
		 			VALUES('$thesis_id','INP','$user_id','$user_id','$curdatetime','$user_id','$curdatetime','INP') ";
		 $db_klas2->query($sqlsubmit2); echo $sqlsubmit2;
		 
		 $sqlsubmit="INSERT INTO pg_proposal(id,thesis_title,thesis_type,introduction,objective,description,insert_by,insert_date,modify_by,modify_date,discussion_status,
		 pg_thesis_id, confirm_status, report_date)
		 			VALUES('$proposal_id','$thesis_title','$thesis_type','$introduction','$objective','$description','$user_id','$curdatetime','$user_id','$curdatetime','$discussion_status','$thesis_id','INP', '$curdatetime') ";
					$db_klas2->query($sqlsubmit); 
		 
		
		 
		$sqlsubmit3="INSERT INTO pg_meeting_detail(id,lecturer_name,meeting_sdate,remark,insert_by,insert_date,modify_by,modify_date)
		 			VALUES('$meeting_detail_id','$lecturer_name','$meeting_sdate','$remark','$user_id','$curdatetime','$user_id','$curdatetime') ";
		 $db_klas3->query($sqlsubmit3); 
		
				
	
	}*/
	
	
	if(isset($_POST['btnEdit']) && ($_POST['btnEdit'] <> ""))
	{			

		$curdatetime = date("Y-m-d H:i:s");
		/*$meeting_detail_id = runnum('id','pg_meeting_detail');
		
		$sqlUpdate1 = "UPDATE pg_thesis
					SET proposal_date = '$curdatetime', modify_by = '$user_id', modify_date = '$curdatetime', ref_thesis_status_id_proposal = 'INP'
					WHERE id = '$thesis_id'";
		$db_klas2->query($sqlUpdate1);*/
		
		$sqlUpdate2 = "UPDATE pg_proposal
					SET thesis_title = '$thesis_title', thesis_type = '$thesis_type', introduction = '$introduction', objective = '$objective', 
					description = '$description', modify_by = '$user_id', modify_date = '$curdatetime', discussion_status = '$discussion_status',
					report_date = '$curdatetime'
					WHERE id = '$proposal_id'
					AND confirm_status='INP'";
		$db_klas2->query($sqlUpdate2);
		
		$sqlDel1= "DELETE FROM pg_meeting_detail WHERE pg_proposal_id = '$proposal_id' ";
		$db_klas2->query($sqlDel1);
	
		//to add record meeting into db
		for ($i=0; $i<sizeof($_POST['date']); $i++) {
		$meeting_detail_id = runnum('id','pg_meeting_detail');	
		$sqlUpdate3 = "INSERT INTO pg_meeting_detail (id,lecturer_name,meeting_sdate,remark,pg_proposal_id,insert_by,insert_date,modify_by,modify_date)
				VALUES ('$meeting_detail_id','" . $_POST['lecturer_name'][$i] . "','" . $_POST['date'][$i] . " ".$_POST['meeting_time'][$i]."','" .  $_POST['remarks'][$i] . "',
				'".addslashes($_REQUEST["proposal_id"])."','$user_id','$curdatetime','$user_id','$curdatetime')";
		//echo "sqlUpdate3 ".$sqlUpdate3;
		$db_klas2->query($sqlUpdate3); 			 
		}

		$sqlDel2 = "DELETE FROM file_upload_proposal WHERE pg_proposal_id = '$proposal_id'";
		//echo "sqlDel2 ".$sqlDel2;
		$db_klas2->query($sqlDel2);	
		
		//to upload doc into db
		for ($i=0; $i<sizeof($_FILES['fileData']['name']); $i++) {
				
			// running no doc attachment
			$upload_id = runnum('fu_cd','file_upload_proposal');			
			$file_name = $_FILES['fileData']['name'][$i];
			$fileType = $_FILES['fileData']['type'][$i];
			$fileSize = intval($_FILES['fileData']['size'][$i]);
			$fileData = file_get_contents($_FILES['fileData']['tmp_name'][$i]);
			
					
			echo $sqlUpload = "INSERT INTO file_upload_proposal (
								fu_cd, 
								fu_document_filename, 
								fu_document_filetype, 
								fu_document_filedata,
								fu_document_thumbnail,
								insert_by,
								insert_date,
								modify_by,
								modify_date,
								pg_proposal_id)
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
								'$proposal_id')";
					$db_klas2->query($sqlUpload);
					//echo $sqlUpload;		
		}
	
	}
?>	


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
    <link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
   	<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />

	<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
	<script src="../../../lib/js/jquery.min2.js"></script>
    <script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
	<script language="JavaScript" src="../../../lib/js/windowopen.js"></script>
</head>
<body>

<?
$sql_thesis="SELECT pt.id as thesis_id, pp.id as proposal_id, pt.student_matrix_no,pp.thesis_title,pp.thesis_type,pp.introduction, pp.objective, pp.description,pp.discussion_status,pmd.id,pmd.lecturer_name, 
				DATE_FORMAT(pmd.meeting_sdate,'%d-%m-%y') as date,DATE_FORMAT(pmd.meeting_sdate,'%h:%i:%s') as time,pmd.remark,pmd.insert_by,pmd.insert_date,pp.confirm_status as proposal_status,pt.status,
				DATE_FORMAT(pp.confirm_date,'%d-%b-%Y') as confirm_date, pp.confirm_remarks, rps.description as proposal_description, pp.confirm_by, ne.name as confirm_name
				FROM pg_thesis pt
				LEFT JOIN pg_proposal pp ON (pp.pg_thesis_id=pt.id)
				LEFT JOIN pg_meeting_detail pmd ON (pmd.id=pp.id)
				LEFT JOIN ref_proposal_status rps ON (rps.id = pp.confirm_status)
				LEFT JOIN new_employee ne ON (ne.empid = pp.confirm_by)
				WHERE pt.student_matrix_no = '$user_id'
				AND pp.confirm_status in ('INP','APP','REQ','DIS')				
				AND pt.ref_thesis_status_id_proposal = 'INP'
				ORDER BY pt.id";

//echo "sql_thesis ".$sql_thesis;
$db_klas2->query($sql_thesis);
$row_personal=$db_klas2->fetchArray();

$name=$row_personal['name'];
$program_code=$row_personal['program_code'];
$program_e=$row_personal['program_e'];
$ic_passport=$row_personal['ic_passport'];
$address_aa=$row_personal['address_aa'];
$address_ab=$row_personal['address_ab'];
$city_a=$row_personal['city_a'];
$state_a=$row_personal['state_a'];
$postcode_a=$row_personal['postcode_a'];
$country_a=$row_personal['country_a'];
$address_bb=$row_personal['address_bb'];
$address_ba=$row_personal['address_ba'];
$city_b=$row_personal['city_b'];
$state_b=$row_personal['state_b'];
$postcode_b=$row_personal['postcode_b'];
$country_b=$row_personal['country_b'];
$citizenship=$row_personal['citizenship'];
$gender=$row_personal['xgender'];
$intake_no=$row_personal['intake_no'];
$mobile=$row_personal['handphone'];
$house=$row_personal['house'];
$office=$row_personal['office'];
$email=$row_personal['email'];
$thesis_title=$row_personal["thesis_title"];
$cases=$row_personal["thesis_type"];
$introduction=$row_personal["introduction"];
$objective=$row_personal["objective"];
$description=$row_personal["description"];
$proposal_status=$row_personal["proposal_status"];
$status=$row_personal["status"];
$student_status=$row_personal["student_status"];
$status=$row_personal['status'];
$thesis_id=$row_personal['thesis_id'];
$thesis_type=$row_personal['thesis_type'];
$supervisor_id=$row_personal['supervisor_id'];
$supervisor_name=$row_personal['name'];
$email=$row_personal['email'];
$hp_no=$row_personal['hp_no'];
$skype_id=$row_personal['skype_id'];
$confirm_date=$row_personal['confirm_date'];
$confirm_remarks=$row_personal['confirm_remarks'];
$proposal_description=$row_personal['proposal_description'];
$proposal_id=$row_personal['proposal_id'];
$confirm_name=$row_personal['confirm_name'];
$confirm_by=$row_personal['confirm_by'];
?>

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

<script type="text/javascript">
$(function() {

	var i = $('input').size() + 1;
	
	//###################################### funtion add more document files ######################################//
	$('a.add-file').click(function() {

		$('<tr><td align="center"><input type="checkbox" class="case_file" /></td><td align=\"center\"><input type=\"text\" name=\"file_name[]\" size="40" /></td><td align="center"><input name="fileData[]" type="file" size="40"/></td></tr>').animate({ opacity: "show" }, "slow").appendTo('#inputs9');
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

	$('<tr><td align="center"><input type="checkbox" name="cbDelFile2" class="case_certificate" /></td><td><input type="text" name="date[]" size="15" readonly id="datepicker'+i+'" /></td><td width="66" height="1" class="tbmain"><select name="meeting_time[]" size="1" ><option value=\"\"></option><option value="07:00">07:00 AM</option><option value="07:30">07:30 AM</option><option value="08:00">08:00 AM</option><option value="08:30">08:30 AM</option><option value="09:00">09:00 AM</option><option value="09:30">09:30 AM</option><option value="10:00">10:00 AM</option><option value="10:30">10:30 AM</option><option value="11:00">11:00 AM</option><option value="11:30">11:30 AM</option><option value="12:00">12:00 PM</option><option value="12:30">12:30 PM</option><option value="13:00">01:00 PM</option><option value="13:30">01:30 PM</option><option value="14:00">02:00 PM</option><option value="14:30">02:30 PM</option><option value="15:00">03:00 PM</option><option value="15:30">03:30 PM</option><option value="16:00">04:00 PM</option><option value="16:30">04:30 PM</option><option value="17:00">05:00 PM</option><option value="17:30">05:30 PM</option><option value="18:00">06:00 PM</option><option value="18:30">06:30 PM</option><option value="19:00">07:00 PM</option><option value="19:30">07:30 PM</option><option value="20:00">08:00 PM</option><option value="20:30">08:30 PM</option><option value="21:00">09:00 PM</option><option value="21:30">09:30 PM</option><option value="22:00">10:00 PM</option><option value="22:30">10:30 PM</option><option value="23:00">11:00 PM</option><option value="23:30">11:30 PM</option><option value="24:00">12:00 PM</option></select></td><td><input type="text" name="lecturer_name[]" size="40" /></td><td><textarea name="remarks[]" cols="100" id="remarks"></textarea></td></tr>').animate({ opacity: "show" }, "slow").appendTo('#inputs10');

	$("#datepicker"+i).datepicker({
		changeMonth: true,
		changeYear: true,
		yearRange: '-100:+0',
		dateFormat: 'dd-mm-yy'
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
	<input type="hidden" name="thesis_id" id="thesis_id" value="<?php echo $thesis_id; ?>">
  
	<fieldset>
	<?
	$result_sql_thesis=$db_klas2->query($sql_thesis);
	$row_cnt = mysql_num_rows($result_sql_thesis);
	//echo "sql_thesis ".$sql_thesis;
	//echo "proposal_status ".$proposal_status;
	if ($row_cnt>0) {//echo "has record";
		if ($proposal_status=='APP') {?>		
			<table>
				<tr>		
					<table border="0">
						<tr>
							<td><strong>Confirmation by Faculty</strong>	</td>
						</tr>
					</table>
					<table>
						<tr>
							<td>Confirmation Date</td>
							<td><input name="confirm_date" type="text" id="confirm_date" value="<?php echo $confirm_date; ?>"  size="15" disabled="disabled"></td>
						</tr>
						<tr>
							<td>Confirmed By</td>
							<td><input name="confirm_date" type="text" id="confirmBy" value="<?php echo $confirm_name; ?>"  title="Staff ID: <?echo $confirm_by;?>" size="40" disabled="disabled"></td>
						</tr>
						<tr>
							<td><p>Approval Status</p></td>
							<td><p>								
									<input type="radio" name="proposal_status" value="APP" checked disabled="disabled"> Approved
									<input type="radio" name="proposal_status" value="REQ" disabled="disabled"> Request with Changes
									<input type="radio" name="proposal_status" value="DIS" disabled="disabled"> Disapproved								
							</p></td>
						</tr>
						<tr>
							<td> Remarks </td>
							<td><textarea name="confirm_remarks" rows="2" cols="50" id="confirm_remarks" disabled="disabled"><?php echo $confirm_remarks; ?></textarea></td>
						</tr>
						<tr>
							<td></td>
							<td><input type="submit" name="attachment" value="View Attachment"/>   </td>
						</tr>
					</table>
				</tr>
			</table>
			<?
		}
		else if ($proposal_status=='REQ'){
			
			?>
			<table border="0">
				<tr>
					<td><strong>Confirmation by Faculty</strong>	</td>
				</tr>
			</table>
			<table>
				<tr>
					<td>Confirmation Date</td>
					<td><input name="confirm_date" type="text" id="confirm_date" value="<?php echo $confirm_date; ?>"  size="15" disabled="disabled"></td>
				</tr>
				<tr>
					<td>Confirmed By</td>
					<td><input name="confirm_date" type="text" id="confirmBy" value="<?php echo $confirm_name; ?>"  title="Staff ID: <?echo $confirm_by;?>" size="40" disabled="disabled"></td>
				</tr>
				<tr>
					<td><p>Confirmation Status</p></td>
					<td><p>								
							<input type="radio" name="proposal_status" value="APP" disabled="disabled"> Approved
							<input type="radio" name="proposal_status" value="REQ" checked disabled="disabled"> Request with Changes
							<input type="radio" name="proposal_status" value="DIS" disabled="disabled"> Disapproved								
					</p></td>
				</tr>
				<tr>
					<td> Remarks </td>
					<td><textarea name="confirm_remarks" rows="2" cols="50" id="confirm_remarks" disabled="disabled"><?php echo $confirm_remarks; ?></textarea></td>
				</tr>
				<tr>
					<td></td>
					<td><input type="submit" name="attachment" value="View Attachment"/>   </td>
				</tr>
			</table>
			<table>
				<tr>
					<td>Note: Your thesis proposal has been reviewed and require changes. Please <a href="edit_proposal.php?id=<?php echo $thesis_id;?>">click here </a> to update it and re-submit. </td>
				</tr>
			</table>
			<?
				
		}		
		else if ($proposal_status=='INP'){?>
			
			<table>
				<tr>
					<p><strong>Notes:</strong><br />
					(1) This form should be submitted to MSU Graduate School of Management (GSM) upon completing of the Research Methodology and before student starts the project.<br />
					(2) Students are advised to seek the lecturer's advice before proceeding with the proposal.<br />
					(3) Student should plan on 6-month's time from the Official Approval Date to complete the Final Project.<br />
					(4) As refer to MBA rules, No candidate with CGPA below 3.0 shall be eligible to register for the Final Project of the degree unless recommended by the Board of Examiners.<br />
					(5) Appointment of supervisor is subject to the recommendation from the Director of MSU Graduate School of Management (GSM).</p>
					   
					<strong>  Outline of Proposed Research/Case Study</strong>
					<table>
						<tr>
							<td>Thesis Proposal Status by Faculty</td>
							<td><input type="text" name="proposal_status" size="25" id="proposal_status" readonly value="<?php echo $proposal_description; ?>"></td>
						</tr>
						<tr>
							<td>Thesis ID</td>
							<td><input type="text" name="thesis_id" size="15" id="thesis_id" disabled value="<?php echo $thesis_id; ?>"></td>
						</tr>
						<tr>
							<td>Thesis / Project Title</td>
							<td><input type="text" name="thesis_title" size="100" rows="3" id="thesis_title" value="<?=$thesis_title?>" ></td>
						</tr>
						<tr>
							<td>Proposal Type</td>
							<td><p>
							<?php if($thesis_type=='R')	{	?>
								<input type="radio" name="thesis_type" value="R" checked > By Research
								<input type="radio" name="thesis_type" value="C" > Case Study

							<?php	}	else {	?>
								<input type="radio" name="thesis_type" value="R" > By Research
								<input type="radio" name="thesis_type" value="C" checked disabled="disabled"> Case Study
							<?php	}	?> 			
							</p></td>
						</tr>
						<tr>
							<td>Introduction</td>
							<td><textarea name="introduction" cols="30" class="ckeditor" rows="3" ><?php echo $introduction; ?></textarea></td>
						</tr>
						<tr>
							<td>Objective</td>
							<td><textarea name="objective" cols="30" class="ckeditor" rows="3" ><?php echo $objective; ?></textarea></td>
						</tr>
						<tr>
							<td>Brief Description of Research/Case Study </td>
							<td><textarea name="description" cols="30" class="ckeditor" rows="3" > <?php echo $description; ?></textarea></td>
						</tr>
					</table>
					
					<strong>Discussion Details </strong><br />
					<table>
						<tr>
							<td><p>Have you discussed about your research/case study to any Lecturer of MSU? </p>       </td>
							<td><p>
							<?echo "discussion_status ".$discussion_status?>
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
								<tr>
									<th width="11%"><input type="checkbox" name="cbDelFile2" id="selectall_certificate" /></th>
									<th width="20%">Date</th>
									<th width="15%">Time</th>
									<th width="22%">Lecturer Name</th>
									<th width="38%">Remarks</th>
								</tr>

								<?php
									 $sqlMeeting="SELECT pmd.id,pmd.lecturer_name, DATE_FORMAT(pmd.meeting_sdate,'%d-%b-%Y') as date,
									 DATE_FORMAT(pmd.meeting_sdate,'%h:%i:%s %p') as time,pmd.remark,pmd.insert_by,pmd.insert_date FROM  pg_meeting_detail pmd  
										WHERE pmd.pg_proposal_id='$proposal_id' ORDER BY pmd.pg_proposal_id DESC ";			
										$result = $db_klas2->query($sqlMeeting); //echo $sqlMeeting;
										$varRecCount=0;					
										while($row = mysql_fetch_array($result)) 					
										{ 
											$varRecCount++;
											echo "<tr>
													<td align=\"center\"><input type=\"checkbox\" class=\"case_certificate\" name=\"cbDelFile[]\" /><input type=\"hidden\" name=\"delFile[]\" value=\"".$row["document_id"]."\" /></td>
													<td align=\"center\"><input type=\"text\" name=\"date[]\" size=\"15\" value=\"".$row["date"]."\" disabled=\"disabled\" /></td>
													<td align=\"center\"><input type=\"text\" name=\"meeting_time[]\" size=\"20\" value=\"".$row["time"]."\" disabled=\"disabled\" /></td>
													<td align=\"center\"><input type=\"text\" name=\"lecturer_name[]\" size=\"22\"value=\"".$row["lecturer_name"]."\" /></td>
													<td align=\"center\"><input type=\"text\" name=\"remarks[]\" size=\"40\"value=\"".$row["remark"]."\" /></td>		
												</tr>";
										}  	?>
							</table>
						</fieldset>
					<?//}?>
					
					<div>
		   
				   <fieldset>
						<legend><strong>Attachment</strong></legend>
						<?php ?>  <p>
							<a href="javascript:void(0)" class="add-file add_btn">Add File</a> 
							<a href="javascript:void(0)" class="remove-file del_btn">Delete File</a> </p><?php ?>
									
							<table border="1" cellpadding="3" cellspacing="3" width="100%" id="inputs9" class="thetable">
								<tr>
									<th><input type="checkbox" name="cbDelFile" id="selectall_file" /></th>
									<th><span class="labeling">File Name</span></th>
									<th><span class="labeling">Upload File</span></th>							
								</tr>
									<?php
									$sqlUpload="SELECT * FROM file_upload_proposal WHERE pg_proposal_id='$proposal_id' ";			
									$result = $db_klas2->query($sqlUpload); //echo $sql;
									$varRecCount=0;					
									while($row = mysql_fetch_array($result)) 					
									{ 
										$varRecCount++;
										echo "<tr>
												<td align=\"center\"><input type=\"checkbox\" class=\"case_file\" name=\"cbDelFile[]\" /><input type=\"hidden\" name=\"delFile[]\" value=\"".$row["fu_cd"]."\" /></td>
												<td align=\"center\"><input type=\"text\" name=\"file_name[]\" size=\"40\"value=\"".$row["fu_document_filename"]."\" /></td>
												<td align=\"left\"><a href=\"download.php?id=".$row["fu_cd"]."\" target=\"_blank\" onmouseover=\"toolTip('".$row["fu_document_filename"]."', 300)\" onmouseout=\"toolTip()\" align=\"center\">".$row["fu_document_filename"]."
												</a></td>		
											</tr>";
									}
								?>
							</table>
							
						</fieldset>
				   </div>
				</tr>
			</table>
			<? $_SESSION['thesis_id']=$thesis_id;?>
			<? $_SESSION['proposal_id']=$proposal_id;?>
			<table>
				<tr>
					<td><input type="submit" name="btnEdit" value="Update"/>   </td>
				</tr>
			</table>
			<?
	

			
		}
		else {//$proposal_status=='DIS'
			?>
			<table border="0">
				<tr>
					<td><strong>Confirmation by  Faculty</strong>	</td>
				</tr>
			</table>
			<table>
				<tr>
					<td>Confirmation Date</td>
					<td><input name="confirm_date" type="text" id="confirm_date" value="<?php echo $confirm_date; ?>"  size="15" disabled="disabled"></td>
				</tr>
				<tr>
					<td>Confirmed By</td>
					<td><input name="confirm_date" type="text" id="confirmBy" value="<?php echo $confirm_name; ?>"  title="Staff ID: <?echo $confirm_by;?>" size="40" disabled="disabled"></td>
				</tr>
				<tr>
					<td><p>Confirmation Status</p></td>
					<td><p>								
							<input type="radio" name="proposal_status" value="APP" disabled="disabled"> Approved
							<input type="radio" name="proposal_status" value="REQ" disabled="disabled"> Request with Changes
							<input type="radio" name="proposal_status" value="DIS" checked disabled="disabled"> Disapproved								
					</p></td>
				</tr>
				<tr>
					<td> Remarks </td>
					<td><textarea name="confirm_remarks" rows="2" cols="50" id="confirm_remarks" disabled="disabled"><?php echo $confirm_remarks; ?></textarea></td>
				</tr>
				<tr>
					<td></td>
					<td><input type="submit" name="attachment" value="View Attachment"/>   </td>
				</tr>
			</table>
			<table>
				<tr>
					<td>Note: Your thesis proposal has been disapproved. Please <a href="new_proposal.php?pid=<?php echo $userid;?>">click here </a> to create another one and re-submit. </td>
				</tr>
			</table>
			<?
		}
		
	}
	else {//echo "has no record";?>
		<td>Note: You don't have a thesis proposal to view. Please <a href="new_proposal.php?pid=<?php echo $userid;?>">click here </a> to create a new one and submit. </td>		
	<?}?>
  </fieldset>
  </form>
</body>
</html>




