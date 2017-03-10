<?php

include("../../../lib/common.php");
checkLogin();

session_start();
$userid=$_SESSION['user_id'];

if(isset($_POST['btnSubmit']) && ($_POST['btnSubmit'] <> "")) {

	$cancelRemarks=$_POST['cancelRemarks'];

	$thesis_id=$_POST['thesis_id'];
	$proposal_id=$_POST['proposal_id'];
	$currentDate = date('Y-m-d H:i:s');

	
	$sql1 = "UPDATE pg_proposal SET
				cancel_requested_remarks='$cancelRemarks', cancel_requested_date = '$currentDate', verified_status = 'WIT',	
				modify_date = '$currentDate', modify_by = '$userid'		
				WHERE id='$proposal_id'";

	$dbg->query($sql1); 
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

<form id="form1" name="form1" method="post" enctype="multipart/form-data" onsubmit="return saveRec();">

<?
$sql_thesis="SELECT pt.id AS thesis_id, pt.student_matrix_no,pt.status as thesis_status,
				pp.id AS proposal_id, pp.thesis_title,pp.thesis_type, pp.objective, pp.introduction,pp.description,pp.discussion_status, 
				DATE_FORMAT(pp.verified_date,'%d-%b-%Y') AS verified_date, pp.verified_remarks, pp.verified_by,
				pp.verified_status,pp.endorsed_by, DATE_FORMAT(pp.endorsed_date,'%d-%m-%y') as endorsed_date, 
				pp.endorsed_remarks, pp.status as endorsement_status, pp.proposal_remarks,
				rps.description AS proposal_description, ne.name AS verified_name, rtt.description as thesis_type_desc,
				DATE_FORMAT(pp.report_date,'%d-%b-%Y') AS report_date, pp.cancel_requested_remarks, 
				DATE_FORMAT(pp.cancel_requested_date,'%d-%b-%Y %h:%i:%s') AS cancel_Requested_date
				FROM pg_thesis pt 
				LEFT JOIN pg_proposal pp ON (pp.pg_thesis_id=pt.id) 
				LEFT JOIN ref_proposal_status rps ON (rps.id = pp.verified_status)  
				LEFT JOIN new_employee ne ON (ne.empid = pp.verified_by) 
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
	$proposal_remarks=$row_area['proposal_remarks'];
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
	
if (strcmp($verified_status,'WIT')!=0) 
{
?>
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
<legend><strong>Cancel Application - Outline of Proposed Research/Case Study</strong></legend>	
	<input type="hidden" name="proposal_id" id="proposal_id" value="<?=$proposal_id;?>">
	<table>
		<tr>
			<td>Submitted on</td>
			<td>:</td>
			<td><label name="report_date" id="report_date" ></label><?=$report_date;?></td>
		</tr>
		<tr>
			<td>Thesis ID</td>
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
		 <tr>
			<td>Discussion status with Lecturer</td>	
			<td>:</td>
			<?if (strcmp($discussionStatus,"Y")==true) {
				?><td><label>Yes</label></td>
			<?}
			else {
				?><td width="20"><label>No</label></td>
			<?
			}?>
		</tr>	
		</table>
		 <? 
			if (strcmp($discussionStatus,"Y")==true) 
			{?>
				<br/>
				<fieldset>
					<legend><strong>Discussion Details</strong></legend>
					
				<table width="100%" border="1" cellpadding="3" cellspacing="1" style="border-collapse:collapse;">
				  <tr>
					<td width="29"><strong>No</strong></td>
					<td width="155"><strong>Lecturer Name</strong></td>
					<td width="140"><strong>Meeting Date</strong></td>
					<td width="117"><strong>Meeting Time</strong></td>
					<td width="240"><strong>Remark</strong></td>
				  </tr>
				  <?
				  
					$sql2 = "SELECT id,lecturer_name, DATE_FORMAT(meeting_sdate,'%d-%b-%Y') as theMeetingDate, 
					DATE_FORMAT(meeting_sdate,'%h:%i:%s %p') as theMeetingSTime, DATE_FORMAT(meeting_edate,'%h:%i:%s %p') as theMeetingETime, 
					remark, venue, pg_proposal_id
					FROM pg_meeting_detail
					WHERE pg_proposal_id = '$proposal_id'"; 

					$result2 = $dba->query($sql2); 

					$dba->next_record();
					$no=1;
					do
					{
						$lecturerName=$dba->f('lecturer_name');
						$theMeetingDate=$dba->f('theMeetingDate');
						$theMeetingSTime=$dba->f('theMeetingSTime');
						$remark=$dba->f('remark');
	  

					?>
					<tr>
						<td><label name="no" size="3" id="no" ><?=$no?></label></td>
						<td><label name="lecturerName" size="15" id="lecturerName" ><?=$lecturerName?></label></td>
						<td><label name="theMeetingDate" size="25" id="theMeetingDate" ><?=$theMeetingDate?></label></td>
						<td><label name="theMeetingSTime" size="25" id="theMeetingSTime" ><?=$theMeetingSTime?></label></td>
						<td><label name="remark" cols="20" disabled="disabled" id="remark"><?=$remark?></label></td>
					</tr>
					<?
					$no=$no+1;
					}while($dba->next_record());
					?>
				</table>
				</fieldset>
			<?}?>
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
					<td>You can request for withdraw or cancel the submitted proposal before the Faculty review it and provide the feedback</td>
				</tr>
				<tr>
					<td>Submitted on</td>
					<td>:</td>
					<td><label name="cancelDate" id="cancelDate" ></label><?=$cancelDate;?></td>
				</tr>
			</table>
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
				<td><input type="submit" name="btnSubmit" id="btnSubmit" align="center"  value="Request for Cancellation" /></td>
				<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../student/student_programme.php';" /></td>		
			</tr>
		</table>
	<table>
		<tr>
			<td><span style="color:#FF0000">*</span> - is a required field. </td>
		</tr>
	</table>		
  </fieldset>
  <?}
else {
	?>
	<fieldset>
		<legend><strong><span style="color:#FF0000">Notification Message</span></strong></legend>	
		<table>
			<tr>
				<td>Your cancellation request has been submitted to the Faculty successfully. Please <strong><a href="../student/student_programme.php?tid=<?=$thesis_id;?>&pid=<?=$proposal_id;?>"><img src="../images/click_here.jpg" width="70" height="30" style="border:0px;" title="Please click here"></a></strong>to return back to student profile.</td>
			</tr>
		</table>
	</fieldset>	
	<?
	
}?>
  </form>
</body>
</html>




