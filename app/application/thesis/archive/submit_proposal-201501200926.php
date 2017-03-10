<?php

//include("../../../lib/common.php");
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

if(isset($_POST['btnEdit']) && ($_POST['btnEdit'] <> ""))
	{			

		$curdatetime = date("Y-m-d H:i:s");
		$meeting_detail_id = runnum2('id','pg_meeting_detail');
		
		$sqlUpdate1 = "UPDATE pg_thesis
					SET modify_by = '$user_id', modify_date = '$curdatetime', ref_thesis_status_id_proposal = 'INP'
					WHERE id = '$thesis_id'";
					
		//echo "sqlUpdate1 ".$sqlUpdate1;
		$db_klas2->query($sqlUpdate1);
		
		$sqlUpdate2 = "UPDATE pg_proposal
					SET thesis_title = '$thesis_title', thesis_type = '$thesis_type', introduction = '$introduction', objective = '$objective', 
					description = '$description', modify_by = '$user_id', modify_date = '$curdatetime', discussion_status = '$discussion_status',
					report_date = '$curdatetime', proposal_remarks = '$proposalRemarks'
					WHERE id = '$proposal_id'
					AND verified_status='INP'";
					
		//echo "sqlUpdate2 ".$sqlUpdate2;
		$db_klas2->query($sqlUpdate2);
		
		for ($i=0; $i<sizeof($_POST['date']); $i++) {
		
		$myMeetingDate = $_POST['date'][$i]." ".$_POST['meeting_time'][$i];
		$myLecturer = $_POST['lecturer_name'][$i];
		$myRemarks = $_POST['remarks'][$i] ?><br/><?
		
		$meeting_detail_id = runnum2('id','pg_meeting_detail');	
		
		/*$sqlUpdate3 = "INSERT INTO pg_meeting_detail (id,lecturer_name,meeting_sdate,remark,pg_proposal_id,
						insert_by,insert_date,modify_by,modify_date)
						VALUES ('$meeting_detail_id','" . $_GET['lecturer_name'][$i] . "',
						STR_TO_DATE('" . $_GET['date'][$i] . " ".$_GET['meeting_time'][$i]."','%m/%d/%Y %h:%i'),'" .  $_GET['remarks'][$i] . "',
				'$proposal_id','$user_id','$curdatetime','$user_id','$curdatetime')";*/
		
		$sqlUpdate3 = "INSERT INTO pg_meeting_detail (id,lecturer_name,meeting_sdate,remark,pg_proposal_id,
						insert_by,insert_date,modify_by,modify_date)
						VALUES ('$meeting_detail_id','$myLecturer',
						STR_TO_DATE('$myMeetingDate','%m/%d/%Y %h:%i'),'$myRemarks',
				'$proposal_id','$user_id','$curdatetime','$user_id','$curdatetime')";
		
		//echo "sqlUpdate3 ".$sqlUpdate3;
		$db_klas2->query($sqlUpdate3); 			 
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

					$db_klas2->query($sqlUpload);
				}
							
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
		alert("Please insert title.");
		return false;
	}
	
	if(introduction.length==0)
	{
		alert("Please insert introduction.");
		return false;
	}
	
	if(objective.length==0)
	{
		alert("Please insert objective.");
		return false;
	}
	
	if(description.length==0)
	{
		alert("Please insert description");
		return false;
	}

}					
</script>

<?
$sql_thesis="SELECT pt.id AS thesis_id, pt.student_matrix_no,pt.status as thesis_status,
				pp.id AS proposal_id, pp.thesis_title,pp.thesis_type, pp.objective, pp.introduction,pp.description,pp.discussion_status, 
				DATE_FORMAT(pp.verified_date,'%d-%b-%Y') AS verified_date, pp.verified_remarks, pp.verified_by,
				pp.verified_status AS proposal_status,pp.endorsed_by, DATE_FORMAT(pp.endorsed_date,'%d-%m-%y') as endorsed_date, 
				pp.endorsed_remarks, pp.status as endorsement_status, pp.proposal_remarks,
				rps.description AS proposal_description, ne.name AS verified_name
				FROM pg_thesis pt 
				LEFT JOIN pg_proposal pp ON (pp.pg_thesis_id=pt.id) 
				LEFT JOIN ref_proposal_status rps ON (rps.id = pp.verified_status)  
				LEFT JOIN new_employee ne ON (ne.empid = pp.verified_by) 
				WHERE pt.student_matrix_no = '$user_id'
				AND pp.verified_status in ('INP','APP','REQ','DIS','REV')				
				AND pp.archived_status is null
				AND pt.ref_thesis_status_id_proposal = 'INP'
				ORDER BY pt.id";

//echo "sql_thesis ".$sql_thesis;
$db_klas2->query($sql_thesis);
$row_personal=$db_klas2->fetchArray();

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
$endorsement_status=$row_personal['endorsement_status'];
$proposal_description=$row_personal["proposal_description"];
$verified_name=$row_personal['verified_name'];
$proposal_remarks=$row_personal['proposal_remarks'];

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

		$('<tr><td align="center"><input type="checkbox" class="case_file" /></td><td align=\"center\"><input type=\"text\" name=\"file_name[]\" size="50" /></td><td align="center"><input name="fileData[]" type="file" size="40"/></td></tr>').animate({ opacity: "show" }, "slow").appendTo('#inputs9');
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

	$('<tr><td align="center"><input type="checkbox" name="box[]" class="case_certificate" /></td><td><input type="text" name="date[]" size="15" readonly id="datepicker'+i+'" /></td><td width="70" height="1" class="tbmain"><select name="meeting_time[]" size="1" ><option value=\"\"></option><option value="07:00">07:00 AM</option><option value="07:30">07:30 AM</option><option value="08:00">08:00 AM</option><option value="08:30">08:30 AM</option><option value="09:00">09:00 AM</option><option value="09:30">09:30 AM</option><option value="10:00">10:00 AM</option><option value="10:30">10:30 AM</option><option value="11:00">11:00 AM</option><option value="11:30">11:30 AM</option><option value="12:00">12:00 PM</option><option value="12:30">12:30 PM</option><option value="13:00">01:00 PM</option><option value="13:30">01:30 PM</option><option value="14:00">02:00 PM</option><option value="14:30">02:30 PM</option><option value="15:00">03:00 PM</option><option value="15:30">03:30 PM</option><option value="16:00">04:00 PM</option><option value="16:30">04:30 PM</option><option value="17:00">05:00 PM</option><option value="17:30">05:30 PM</option><option value="18:00">06:00 PM</option><option value="18:30">06:30 PM</option><option value="19:00">07:00 PM</option><option value="19:30">07:30 PM</option><option value="20:00">08:00 PM</option><option value="20:30">08:30 PM</option><option value="21:00">09:00 PM</option><option value="21:30">09:30 PM</option><option value="22:00">10:00 PM</option><option value="22:30">10:30 PM</option><option value="23:00">11:00 PM</option><option value="23:30">11:30 PM</option><option value="24:00">12:00 PM</option></select></td><td><input type="text" name="lecturer_name[]" size="40" /></td><td><input type="text" name="remarks[]" size="60" id="remarks" /></td></tr>').animate({ opacity: "show" }, "slow").appendTo('#inputs10');

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
	$result_sql_thesis=$db_klas2->query($sql_thesis);
	$row_cnt = mysql_num_rows($result_sql_thesis);
	//echo "sql_thesis ".$sql_thesis;
	//echo "proposal_status ".$proposal_status;
	if ($row_cnt>0) 
	{//echo "has record";
		if ($proposal_status=='APP') 
		{
			if ($endorsement_status=='APP') 	?>
					<table>
						<tr>
							<td><strong>Verification by Faculty</strong></td>
						</tr>
					</table>
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
							<td><label><?=$verified_by;?> <?=$verified_name; ?></label></td>
						</tr>
						<tr>
							<td> Remarks </td>
							<td>:</td>
							<td><label class="ckeditor"><?=$verified_remarks;?></label></td>							
						</tr>
						<tr>
							<td></td>
							<td></td>
							<td><input type="submit" name="attachment" value="View Attachment"/>   </td>
						</tr>
					</table>
					<br/>
					<table>
						<tr>
							<td><strong>Endorsement by Senate</strong>	</td>
						</tr>
					</table>
					<table>
						<tr>
							<td>Endorsement Status</td>							
							<td>:</td>
							<td>								
								<?if ($endorsement_status=="APP")
								{?>
									<input type="radio" name="endorsement_status" value="APP"  disabled="disabled" checked> Approved
									<input type="radio" name="endorsement_status" value="DIS" disabled="disabled"> Disapproved
									<?
								}
								elseif ($endorsement_status=="DIS")
								{?>
									<input type="radio" name="endorsement_status" value="APP"  disabled="disabled"> Approved
									<input type="radio" name="endorsement_status" value="DIS"  disabled="disabled" checked> Disapproved	
								<?}
								else 
								{?>
									<input type="radio" name="endorsement_status" value="APP"  disabled="disabled"> Approved
									<input type="radio" name="endorsement_status" value="DIS"  disabled="disabled"> Disapproved	
								<?}?>
							</td>
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
							<td><label><?=$endorsed_by;?> <?=$endorsed_name; ?></label></td>
						</tr>
						<tr>
							<td> Remarks </td>
							<td>:</td>
							<td><label class="ckeditor"><?=$endorsed_remarks;?></label></td>							
						</tr>
						<tr>
							<td></td>
							<td></td>
							<td><input type="submit" name="attachment" value="View Attachment"/>   </td>
						</tr>
					</table>

					<br/>
					<table>
						<tr>
							<?if ($proposal_status=='APP' && $endorsement_status=='APP')
							{?>
								<td><strong>Note:</strong> Your thesis proposal has been approved by the Senate. You may start to <a href="">prepare and submit </a>your monthly progress report.</td>
							<?}
							elseif ($proposal_status=='APP' && $endorsement_status=='DIS'){
							?>
								<td><strong>Note:</strong> Your thesis proposal has been disapproved by the Senate. You may need to  <a href="new_proposal.php?pid=<?php echo $userid;?>">create another one </a> and re-submit.</td>
							<?}
							else{
							?>
								<td><strong>Note:</strong> Your thesis proposal is pending for endorsement by the Senate. Please check it again later. </td>
							<?}?>
							
						</tr>
					</table>

		<?
		}
		else if ($proposal_status=='REQ')
		{ 
			
			?>
			<table border="0">
				<tr>
					<td><strong>Verification by Faculty</strong>	</td>
				</tr>
			</table>
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
					<td><label><?=$verified_by;?> <?=$verified_name; ?></label></td>
				</tr>
				<tr>
					<td> Remarks </td>
					<td>:</td>
					<td><label class="ckeditor"><?=$verified_remarks; ?></label></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td><input type="submit" name="attachment" value="View Attachment"/>   </td>
				</tr>
			</table>
			<br/>
			<table>
				<tr>
					<td><strong>Note:</strong> Your thesis proposal has been reviewed and require changes. Please <a href="../thesis/edit_proposal.php?id=<?php echo $thesis_id;?>">click here </a> to update it and re-submit. </td>				
				</tr>
			</table>					
			<?
				
		}		
		else if ($proposal_status=='REV')
		{ 
			
			?>
			<table border="0">
				<tr>
					<td><strong>Verification by Faculty</strong>	</td>
				</tr>
			</table>
			<table>
				<tr>
					<td>Proposal Status</td>
					<td>:</td>
					<td><?=$proposal_description;?></td>
				</tr>
				<tr>
					<td>Reviewed Date</td>
					<td>:</td>
					<td><label><?=$verified_date; ?></label></td>
				</tr>
				<tr>
					<td>Reviewed By</td>
					<td>:</td>
					<td><label><?=$verified_by;?> <?=$verified_name; ?></label></td>
				</tr>
				<tr>
					<td> Remarks </td>
					<td>:</td>
					<td><label class="ckeditor"><?=$verified_remarks; ?></label></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td><input type="submit" name="attachment" value="View Attachment"/>   </td>
				</tr>
			</table>
			<br/>
			<table>
				<tr>
					<td><strong>Note:</strong> Your thesis proposal is under review by the Faculty. Changes can only be done after they have provided their feedback. Please come back later.</td>				
				</tr>
			</table>					
			<?
				
		}	
		else if ($proposal_status=='INP'){?>
		
			
			<table>
				<tr>
					<strong>Notes:</strong><br />
					(1) This form should be submitted to MSU Graduate School of Management (GSM) upon completing of the Research Methodology and before student starts the project.<br />
					(2) Students are advised to seek the lecturer's advice before proceeding with the proposal.<br />
					(3) Student should plan on 6-month's time from the Official Approval Date to complete the Final Project.<br />
					(4) As refer to MBA rules, No candidate with CGPA below 3.0 shall be eligible to register for the Final Project of the degree unless recommended by the Board of Examiners.<br />
					(5) Appointment of supervisor is subject to the recommendation from the Director of MSU Graduate School of Management (GSM).<br/><br/>
					   
					<strong>  Outline of Proposed Research/Case Study</strong>
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
							<td><textarea name="thesis_title" rows="2" cols="75" id="thesis_title" ><?=$thesis_title;?></textarea></td>							
						</tr>
						<tr>
							<td>Proposal Type</td>
							<td>:</td>
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
							<td><span style="color:#FF0000">*</span> Introduction</td>
							<td></td>
							<td><textarea name="introduction" id="introduction" class="ckeditor"><?=$introduction; ?></textarea></td>
						</tr>
						<tr>
							<td><span style="color:#FF0000">*</span> Objective</td>
							<td></td>
							<td><textarea name="objective" id="objective" class="ckeditor" ><?=$objective; ?></textarea></td>
						</tr>
						<tr>
							<td><span style="color:#FF0000">*</span> Brief Description</td>
							<td></td>
							<td><textarea name="description" id="description" class="ckeditor" > <?=$description; ?></textarea></td>
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

					<fieldset style="width:800px">
						<legend><strong>Discussion Details</strong></legend>

						<p>
						<a href="javascript:void(0)" class="add-certification add_btn" ;>Add</a>&nbsp; 
						<a href="javascript:void(0)" class="remove-certification del_btn" ;>Delete</a>
						</p><?php ?>
						<table border="1" cellpadding="2" cellspacing="1" width="99%" id="inputs10" class="thetable">
							<tr>
								<th width="6%"><input type="checkbox" name="box[]" id="selectall_certificate" /></th>
								<th width="12%">Date</th>
								<th width="12%">Time</th>
								<th width="29%">Lecturer Name</th>
								<th width="41%">Remarks</th>
								<th width="29"></th>
							</tr>

						<?php
							$sqlMeeting="SELECT pmd.id,pmd.lecturer_name, DATE_FORMAT(pmd.meeting_sdate,'%d-%m-%Y') as date,
									DATE_FORMAT(pmd.meeting_sdate,'%h:%i:%s %p') as time,pmd.remark,pmd.insert_by,pmd.insert_date 
									FROM  pg_meeting_detail pmd
									WHERE pmd.pg_proposal_id='$proposal_id' ORDER BY pmd.pg_proposal_id DESC ";			
							$result = $db_klas2->query($sqlMeeting); //echo $sql;
							$varRecCount=0;					
							while($row = mysql_fetch_array($result)) 					
							{ 
								$varRecCount++;
								?><tr>
										<td align="center"><input type="checkbox" class="case_certificate" name="cbDelFile2[]" /><input type="hidden" name="delFile[]" value="<?=$row['document_id'];?>" /></td>
													<td align="center"><input type="text" name="date[]" size="15" value="<?=$row["date"];?>" disabled="disabled" /></td>
													<td align="center"><input type="text" name="meeting_time[]" size="20" value="<?=$row["time"];?>" disabled="disabled" /></td>
													<td align="center"><input type="text" name="lecturer_name[]" size="40" value="<?=$row["lecturer_name"];?>" /></td>
													<td align="center"><input type="text" name="remarks[]" size="60" value="<?=$row["remark"];?>" /></td>
													<td width="34" align="center"></a>
												<a href="delete_meeting_detail.php?id=<?=$row["id"];?>">
												<img src="../images/delete_calendar.jpg" width="20" height="19" style="border:0px;" title="Delete Meeting Information" ></a></td>
									</tr>
							<?}?>
							</table>
						</fieldset>
					
					
					<div>
		   
				   <fieldset style="width:800px">
						<legend><strong>Attachment</strong></legend>
						<?php ?>  <p>
							<a href="javascript:void(0)" class="add-file add_btn">Add </a>&nbsp;  
							<a href="javascript:void(0)" class="remove-file del_btn">Delete </a> </p><?php ?>
									
							<table border="1" cellpadding="3" cellspacing="3" width="60%" id="inputs9" class="thetable">
								<tr>
									<th width="12%"><input type="checkbox" id="selectall_file" /></th>
									<th width="103">File Name</th>
									<th width="171">Upload File</th>
									<th width="40"></th>
								</tr>
									<?
						$sql="SELECT * FROM file_upload_proposal WHERE pg_proposal_id='$proposal_id' ";			
							$result = $db_klas2->query($sql); //echo $sql;
							$varRecCount=0;					
							while($row = mysql_fetch_array($result)) 					
							{ 
								$varRecCount++;
								?><tr>
									<td align="center"><input type="checkbox" class="case_file" name="cbDelFile[]" /><input type="hidden" name="delFile[]" value="<?=$row["fu_cd"];?>" /></td>
												<td align="left"><input type="text" name="file_name[]" size="40" value="<?=$row["fu_document_filename"];?>" /></td>
												<td><a href="download.php?fc=<?=$row["fu_cd"];?>&al=S" target="_blank" onMouseOver="toolTip('<?=$row["fu_document_filename"];?>', 300)" onMouseOut="toolTip()" align="center">
												<img src="../images/view_doc.jpg" width="20" height="19" style="border:0px;" title="View document"></a>
												<a href="delete_attachment_proposal.php?fc=<?=$row["fu_cd"];?>&al=S" >
												<img src="../images/delete_doc.jpg" width="20" height="19" style="border:0px;" title="Delete document"></a></td>
									</tr>
						<?	}							
						?>	
							</table>
							<br/>
							<table>
								<tr>
									<td>Remarks</td>
								</tr>
								<tr>
									<td><textarea name="proposalRemarks" id="proposalRemarks" class="ckeditor" cols="50" rows="3"> <?=$proposal_remarks; ?></textarea></td>
								</tr>
							</table>
							
						</fieldset>
				   </div>
				</tr>
			</table>
			<?$_SESSION['thesis_id']=$thesis_id;?>
			<?$_SESSION['proposal_id']=$proposal_id;?>
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
					<td><strong>Verification by  Faculty</strong>	</td>
				</tr>
			</table>
			<table>
				<tr>
					<td>Proposal Status</td>
					<td>:</td>
					<td>							
							<input type="radio" name="proposal_status" value="APP" disabled="disabled"> Approved
							<input type="radio" name="proposal_status" value="REQ" disabled="disabled"> Request with Changes
							<input type="radio" name="proposal_status" value="DIS" checked disabled="disabled"> Disapproved								
					</td>
				</tr>
				<tr>
					<td>Verified Date</td>
					<td>:</td>
					<td><label><?=$Verified_date;?></label></td>
				</tr>
				<tr>
					<td>Verified By</td>
					<td>:</td>
					<td><label><?=$verified_by;?> <?=$verified_name;?></label></td>
				</tr>
				<tr>
					<td> Remarks </td>
					<td>:</td>
					<td><label class="ckeditor"><?=$verified_remarks; ?></label></td>
				</tr>			
				<tr>
					<td></td>
					<td></td>
					<td><input type="submit" name="attachment" value="View Attachment"/>   </td>
				</tr>
			</table>
			<br/>
			<table>
				<tr>
					<td><strong>Note:</strong> Your thesis proposal has been disapproved by the Senate. You may need to  <a href="../thesis/new_proposal.php?pid=<?php echo $userid;?>">create another one </a> and re-submit.</td>
				</tr>
			</table>
			<?
		}
		
	}
	else {//echo "has no record";?>
		
		<fieldset>
			<legend><strong>Notification Message</strong></legend>
			<table>
				<tr>			
					<td>Note: You don't have a thesis proposal to view. Please <a href="../thesis/new_proposal.php?pid=<?=$userid;?>">click here </a> to create a new one and submit. </td>							
				</tr>
			</table>
		</fieldset>
	<?}?>

  </form>
</body>
</html>




