<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: reviewer_feedback_outline.php
//
// Created by: Fizmie
// Created Date: 24 Dec 2014
// Modified by: Zuraimi
// Modified Date: 24 Dec 2014
//
//**************************************************************************************


//Read common library for page execution i.e. database connection. login validation
include("../../../lib/common.php");
checkLogin();

$id=$_REQUEST['proposalId'];
$sql1 = "SELECT a.thesis_title, b.description as thesis_type_desc, a.introduction, a.objective, a.description, a.endorsed_by, DATE_FORMAT(a.endorsed_date,'%d-%b-%Y') AS theEndorsedDate, a.endorsed_remarks, a.status,
		a.verified_by, DATE_FORMAT(a.verified_date,'%d-%b-%Y') AS theVerifiedDate, a.verified_remarks, a.verified_status, a.discussion_status,DATE_FORMAT(a.report_date,'%d-%b-%Y') AS report_date, a.pg_thesis_id,
		a.thesis_type, b.description AS theThesisTypeDescription, c1.description AS verified_desc, c2.description AS status_desc 
		FROM pg_proposal a 
		LEFT JOIN ref_thesis_type b ON (b.id=a.thesis_type) 
		LEFT JOIN ref_proposal_status c1 ON (c1.id=a.verified_status) 
		LEFT JOIN ref_proposal_status c2 ON (c2.id=a.status) 
		WHERE a.id ='$id'";

		$result1 = $db->query($sql1); 
		$db->next_record();


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
		<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>
		<script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
	
	</head>
	<body>
		<form id="form1" name="form1" method="post" enctype="multipart/form-data">
				
			
			<?
			//do{
				$thesisId=$db->f('pg_thesis_id');
				$thesisTitle=$db->f('thesis_title');
				$thesisTypeDesc=$db->f('thesis_type_desc');
				$name=$db->f('name');
				$endorsedBy=$db->f('endorsed_by');				
				$endorsedDate=$db->f('theEndorsedDate');
				$endorsedRemarks=$db->f('endorsed_remark');
				$statusDesc=$db->f('status_desc');
				$verifiedBy=$db->f('verified_by');
				$verifiedDate=$db->f('theVerifiedDate');
				$verifiedRemarks=$db->f('verified_remarks');
				$verifiedDesc=$db->f('verified_desc');				
				$introduction=$db->f('introduction');
				$objective=$db->f('objective');
				$description=$db->f('description');
				$theThesisTypeDescription=$db->f('theThesisTypeDescription');
				$discussionStatus=$db->f('discussion_status');		
				$reportDate=$db->f('report_date');	
				
			?>
			<fieldset>
				<legend><strong>Outline of Proposed Research/Case Study</strong></legend>	
				<table width="410">
					<tr>
						<td width="128">Thesis / Project ID</td>
						<td width="10">:</td>
					  <td width="232"><label><?=$thesisId?></label></td>		
					</tr>
					<tr>
						<td width="128">Proposal Date</td>
						<td width="10">:</td>
					  <td width="232"><label><?=$reportDate?></label></td>		
					</tr>
					<tr>
						<td width="128">Proposal Title</td>
						<td width="10">:</td>
					  <td width="232"><label><?=$thesisTitle?></label></td>		
					</tr>
					<tr>
						<td>Proposal Type</td>
						<td>:</td>
						<td><label><?=$thesisTypeDesc?></label></td>		
					</tr>
					<tr>
						<td>Introduction</td>
						<td>:</td>
					  <td><label class="ckeditor"/><?=$introduction?></label></td>		
					</tr>
					<tr>
						<td>Objective</td>			
						<td>:</td>
						<td><label class="ckeditor"/><?=$objective?></label></td>
					</tr>
					<tr>
						<td>Brief Description</td>
						<td>:</td>
						<td><label class="ckeditor"/><?=$description?></label></td>		
					</tr>	
			</table>	
				<br/>
				<fieldset>
					<legend><strong>Discussion Details</strong></legend>
					
				<table width="100%" border="1" cellpadding="3" cellspacing="1" style="border-collapse:collapse;" class="thetable">
				  <tr>
					<th width="29" align="center"><strong>No</strong></th>
					<th width="155"><strong>Lecturer Name</strong></th>
					<th width="155"><strong>External Lecturer</strong></th>
					<th width="117"><strong>Meeting Mode</strong></th>
					<th width="140"><strong>Meeting Date</strong></th>
					<th width="117"><strong>Meeting Time</strong></th>
					<th width="240"><strong>Notes</strong></th>
				  </tr>
				  <?
				  
					$sql2 = "SELECT pmd.id,pmd.lecturer_id, pmd.external_lecturer,rm.id, rm.description, pmd.meeting_mode, 
					DATE_FORMAT(pmd.meeting_sdate,'%d-%b-%Y') as theMeetingDate,
					DATE_FORMAT(pmd.meeting_sdate,'%h:%i:%s %p') as theMeetingSTime,DATE_FORMAT(pmd.meeting_edate,'%h:%i:%s %p') as theMeetingETime, 
					pmd.remark, pmd.venue, pmd.pg_proposal_id
					FROM pg_meeting_detail pmd 
					LEFT JOIN ref_meeting_mode rm ON (rm.id=pmd.meeting_mode)
					WHERE pg_proposal_id = '$id'"; 

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
							$meeting_mode=$dba->f('description');		  

						?>
						<tr>
							<td align="center"><label name="no" size="3" id="no" ><?=$no?>.</label></td>
							<td><label name="lecturerName" size="15" id="lecturerName" ><?=$lecturerName?></label></td>
							<td><label name="externalLecturer" size="15" id="externalLecturer"><?=$externalLecturer?></label></td>
							<td><label name="meetingmode" size="25" id="meetingmode" >
                              <?=$meeting_mode?>
                            </label></td>
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
			</fieldset>
			
		<table>
			<tr>		
				<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../reviewer/reviewer_feedback.php';" /></td>		
			</tr>
		</table>
		</form>
	</body>
</html>




