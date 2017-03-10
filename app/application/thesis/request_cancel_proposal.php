<?php

include("../../../lib/common.php");
checkLogin();

session_start();
$userid=$_SESSION['user_id'];

function retrieveJobAreaId($jobAreaId)
{
	global $dbc;
	$sql = "SELECT area from job_list_category WHERE JobArea = '$jobAreaId'";
	$dbc->query($sql);
	$dbc->next_record();
	$jobAreaDesc = $dbc->f('area');
	return $jobAreaDesc;

}

if(isset($_POST['btnSubmit']) && ($_POST['btnSubmit'] <> "")) {

	$msg = array();
	if(empty($_POST['cancelRemarks'])) $msg[] = "<div class=\"error\"><span>Please enter your cancellation reason.</span></div>";
	
	if(empty($msg)) 
	{
		$cancelRemarks=$_POST['cancelRemarks'];

		$thesis_id=$_POST['thesis_id'];
		$proposal_id=$_POST['proposal_id'];
		$currentDate = date('Y-m-d H:i:s');

		
		$sql1 = "UPDATE pg_proposal SET
					cancel_requested_remarks='$cancelRemarks', cancel_requested_date = '$currentDate', verified_status = 'WIT',	
					modify_date = '$currentDate', modify_by = '$userid'		
					WHERE id='$proposal_id'";

		$dbg->query($sql1); 
		$msg[] = "<div class=\"success\"><span>Your request to cancel the submitted thesis proposal has been sent successfully to the Faculty .</span></div>";
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
	<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>
</head>
<body>
<SCRIPT LANGUAGE="JavaScript">

function respConfirm () {
    var confirmSubmit = confirm("Click OK if confirm to submit or CANCEL to proceed with the changes.");
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
	foreach($msg as $err) 
	{
		echo $err;
	}
}
?>
		
<form id="form1" name="form1" method="post" enctype="multipart/form-data" onsubmit="return saveRec();">

<?
$sql_thesis="SELECT pt.id AS thesis_id, pt.student_matrix_no,pt.status as thesis_status,
				pp.id AS proposal_id, pp.thesis_title,pp.thesis_type, pp.objective, pp.introduction,pp.description,pp.discussion_status, 
				DATE_FORMAT(pp.verified_date,'%d-%b-%Y') AS verified_date, pp.verified_remarks, pp.verified_by,
				pp.verified_status,pp.endorsed_by, DATE_FORMAT(pp.endorsed_date,'%d-%m-%y') as endorsed_date, 
				pp.endorsed_remarks, pp.status as endorsement_status, 
				rps.description AS proposal_description, rtt.description as thesis_type_desc,
				DATE_FORMAT(pp.report_date,'%d-%b-%Y') AS report_date, pp.cancel_requested_remarks, 
				DATE_FORMAT(pp.cancel_requested_date,'%d-%b-%Y %h:%i:%s %p') AS cancel_requested_date
				FROM pg_thesis pt 
				LEFT JOIN pg_proposal pp ON (pp.pg_thesis_id=pt.id) 
				LEFT JOIN ref_proposal_status rps ON (rps.id = pp.verified_status)  
				LEFT JOIN ref_thesis_type rtt ON (rtt.id = pp.thesis_type)
				WHERE pt.student_matrix_no = '$user_id'
				AND pp.verified_status in ('SAV','INP','WIT')				
				AND pp.archived_status is null
				AND pt.ref_thesis_status_id_proposal = 'INP'
				ORDER BY pt.id";
				
	$result_sql_thesis = $db->query($sql_thesis);
	$row_area = $db->fetchArray();
	
	$thesis_id=$row_area['thesis_id'];
	$proposal_id=$row_area['proposal_id'];
	$thesis_title=$row_area['thesis_title'];
	$thesis_type=$row_area['thesis_type'];
	$introduction=$row_area['introduction'];
	$objective=$row_area['objective'];
	$description=$row_area['description'];
	$discussion_status=$row_area['discussion_status'];
	$verified_date=$row_area['verified_date'];
	$verified_remarks=$row_area['verified_remarks'];
	$verified_status=$row_area['verified_status'];
	$proposal_description=$row_area['proposal_description'];
	$thesis_type_desc=$row_area['thesis_type_desc'];
	$report_date=$row_area['report_date'];
	$cancelRequestedDate=$row_area['cancel_requested_date'];
	$cancelRequestedRemarks=$row_area['cancel_requested_remarks'];
	

?>
<fieldset>
<legend><strong>Cancel Application - Outline of Proposed Research/Case Study</strong></legend>	
	<input type="hidden" name="proposal_id" id="proposal_id" value="<?=$proposal_id;?>">
	<table>
		<tr>
			<td>Submitted on</td>
			<td>:</td>
			<td><label name="report_date" id="report_date" ></label><?=$report_date;?></td>
		</tr>
		<tr>
			<td>Thesis / Project ID</td>
			<td>:</td>
			<td><label name="thesis_id" id="thesis_id" ></label><?=$thesis_id;?></td>
		</tr>
		<tr>
			<td>Thesis / Project Title</td>
			<td>:</td>
			<td><label name="thesis_title" size="100" maxlength="100"  id="thesis_title" ></label><?=$thesis_title;?></td>
		</tr>
		<tr>
			<td>  Proposal Type</td>
			<td>:</td>
			<td><label name="thesis_type" id="thesis_type" ></label><?=$thesis_type_desc;?></td>
		</tr>
		<tr>
			<td>Introduction</td>
			<td>:</td>
			<td><label name="introduction" cols="30" class="ckeditor" rows="3" ><?=$introduction;?></label></td>
		</tr>
		<tr>
			<td>Objective</td>
			<td>:</td>
			<td><label name="objective" cols="30" class="ckeditor" rows="3" ><?=$objective;?></label></td>
		</tr>
		<tr>
			<td>Brief Description</td>
			<td>:</td>
			<td><label name="description" cols="30" class="ckeditor" rows="3" ><?=$description;?></label></td>
		</tr>
		 
		</table>
		
		<br/>
		<fieldset>
			<legend><strong>Proposal Area</strong></legend>
			
		<table width="100%" border="1" cellpadding="3" cellspacing="1" style="border-collapse:collapse;" class="thetable">
		  <tr>
			<th width="7%" align="center"><strong>Area No</strong></th>
			<th width="43%"><strong>Proposal Area</strong></th>
			<th width="7%" align="center"><strong>Area No</strong></th>
			<th width="43%"><strong>Proposal Area</strong></th>
		  </tr>
		  <?
		  
			$sql2 = "SELECT id,job_id1_area,job_id2_area,job_id3_area,job_id4_area,job_id5_area,job_id6_area
			FROM pg_proposal_area
			WHERE pg_proposal_id = '$proposal_id'"; 

			$result2 = $dba->query($sql2); 

			$dba->next_record();
			$no=1;
			$row_cnt = mysql_num_rows($result2);
			if ($row_cnt>0) {
				$jobIdArea1=$dba->f('job_id1_area');
				$jobIdArea2=$dba->f('job_id2_area');
				$jobIdArea3=$dba->f('job_id3_area');
				$jobIdArea4=$dba->f('job_id4_area');
				$jobIdArea5=$dba->f('job_id5_area');
				$jobIdArea6=$dba->f('job_id6_area');
				?>
				<tr>
					<td align="center"><label>1.</label></td>
					<td><label><?=retrieveJobAreaId($jobIdArea1)?></label></td>
					<td align="center"><label>4.</label></td>
					<td><label><?=retrieveJobAreaId($jobIdArea4)?></label></td>
				</tr>
				<tr>
					<td align="center"><label>2.</label></td>
					<td><label><?=retrieveJobAreaId($jobIdArea2)?></label></td>
					<td align="center"><label>5.</label></td>
					<td><label><?=retrieveJobAreaId($jobIdArea5)?></label></td>
				</tr>
				<tr>
					<td align="center"><label>3.</label></td>
					<td><label><?=retrieveJobAreaId($jobIdArea3)?></label></td>
					<td align="center"><label>6.</label></td>
					<td><label><?=retrieveJobAreaId($jobIdArea6)?></label></td>
				</tr>
				<?
			}
			else {
				?>
				<table>
					<tr>
						<td>No record found!</td>
					</tr>
				</table>
				<?
			}
				?>
		</table>
		</fieldset>

			<br/>
			<fieldset>
				<legend><strong>Discussion Details</strong></legend>
				
			<table width="100%" border="1" cellpadding="3" cellspacing="1" style="border-collapse:collapse;" class="thetable">
			  <tr>
				<th width="29" align="center"><strong>No</strong></th>
				<th width="155"><strong>Lecturer Name</strong></th>
				<th width="155"><strong>External Lecturer</strong></th>
                <th width="140"><strong>Meeting Date</strong></th>
				<th width="117"><strong>Meeting Time</strong></th>
				<th width="240"><strong>Notes</strong></th>
			  </tr>
			  <?
			  
				$sql2 = "SELECT id,lecturer_id, external_lecturer, DATE_FORMAT(meeting_sdate,'%d-%b-%Y') as theMeetingDate, 
				DATE_FORMAT(meeting_sdate,'%h:%i:%s %p') as theMeetingSTime, DATE_FORMAT(meeting_edate,'%h:%i:%s %p') as theMeetingETime, 
				remark, venue, pg_proposal_id
				FROM pg_meeting_detail
				WHERE pg_proposal_id = '$proposal_id'"; 

				$result2 = $dba->query($sql2); 

				$dba->next_record();
				$no=1;
				$row_cnt = mysql_num_rows($result2);
				if ($row_cnt>0) {
					do
					{
						$lecturerId=$dba->f('lecturer_id');
						
						$sql3 = "SELECT name
						FROM new_employee
						WHERE empid = '$lecturerId'"; 
			
						$result3 = $dbc->query($sql3); 
						$dbc->next_record();
						
						$lecturerName = $dbc->f('name');
						$externalLecturer = $dba->f('external_lecturer');
						$theMeetingDate=$dba->f('theMeetingDate');
						$theMeetingSTime=$dba->f('theMeetingSTime');
						$remark=$dba->f('remark');
	  

					?>
					<tr>
						<td align="center"><label name="no" size="3" id="no" ><?=$no?>.</label></td>
						<td><label name="lecturerName" size="15" id="lecturerName" ><?=$lecturerName?></label></td>
						<td><label name="externalLecturer" size="15" id="externalLecturer"><?=$externalLecturer?></label></td>
						<td><label name="theMeetingDate" size="25" id="theMeetingDate" ><?=$theMeetingDate?></label></td>
						<td><label name="theMeetingSTime" size="25" id="theMeetingSTime" ><?=$theMeetingSTime?></label></td>
						<td><label name="remark" cols="20" disabled="disabled" id="remark"><?=$remark?></label></td>
					</tr>
					<?
					$no=$no+1;
					}while($dba->next_record());
				}
				else {
					?>
					<table>
						<tr>
							<td>No record found!</td>
						</tr>
					</table>
					<?
				}
					?>
			</table>
			</fieldset>

			<br/>
	  
			<div>
			<fieldset>
			<legend><strong>Proposal Attachment</strong></legend>	
		   <?php
				$sqlUpload="SELECT * FROM file_upload_proposal 
				WHERE pg_proposal_id='$proposal_id' 
				AND attachment_level='S' ";			

				$result = $db_klas2->query($sqlUpload); //echo $sql;
				$row_cnt = mysql_num_rows($result);
				$attachmentNo1=1;
				if ($row_cnt>0)
				{
					?><td align="left"><?
					while($row = mysql_fetch_array($result)) 					
					{ 
						?>
								<a href="download.php?fc=<?=$row["fu_cd"];?>&al=S" title="File Description: <?=$row["fu_document_filedesc"];?>">Attachment <?=$attachmentNo1++;?>: <img src="../images/download.png" width="20" height="19" style="border:0px;" title="Download <?=$row["fu_document_filename"];?>"></a><br/>
								
										
							
					<?}
					?></td><?
				}
				else {
					?><td>No attachment</td><?
				}
			?>
			</fieldset>
	   </div>
	   <br/>
	   <fieldset>
		<legend><strong>Cancellation Request</strong></legend>	
			<table>
			   <tr>
					<td><strong><span style="color:#FF0000">Note: </span></strong></td>
					<td>:</td>
					<td>You can request to withdraw or cancel the submitted proposal before the Faculty review it and provide the feedback</td>
				</tr>
				<tr>
					<td>Submitted on</td>
					<td>:</td>
					<td><label name="cancelDate" id="cancelDate" ></label><?=$cancelRequestedDate;?></td>
				</tr>
			</table>
			<br/>
		   <table>
				<tr>
					<td><span style="color:#FF0000">*</span> Please provide your reason of cancellation:-</td>
				</tr>
				<tr>
					<td><textarea name="cancelRemarks" id="cancelRemarks" class="ckeditor" cols="50" rows="3"> <?=$cancelRemarks; ?></textarea></td>
				</tr>
			</table>
		</fieldset>
	   <br/>
		 <table>
			<tr>
				<?if ($verified_status!='WIT') {?>
				<td><input type="submit" name="btnSubmit" id="btnSubmit" align="center"  value="Request Cancellation" onClick="return respConfirm()" /></td>
				<?}?>
				<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../student/student_programme.php';" /></td>
				<td>Note: Field marks with (<span style="color:#FF0000">*</span>) is compulsory.</td>		
			</tr>
		</table>	
  </fieldset>


  </form>
</body>
</html>




