<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: assign_reviewer_ouline.php
//
// Created by: Zuraimi
// Created Date: 27-Dec-2014
// Modified by: Zuraimi
// Modified Date: 27-Dec-2014
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

session_start();
$userid=$_SESSION['user_id'];
$thesisId=$_REQUEST['thesisId'];
$proposalId=$_REQUEST['proposalId'];

///////////////////////////////////////////////////////////////
// used for pagination
	$page = ($_GET['page'] == 0 ? 1 : $_GET['page']);
	$perpage = 2;
	$startpoint = ($page * $perpage) - $perpage;

$varParamSend="";

foreach($_REQUEST as $key => $value)
{
	if($key!="page")
		$varParamSend.="&$key=$value";
}


function retrieveJobAreaId($jobAreaId)
{
	global $dbc;
	$sql = "SELECT area from job_list_category WHERE JobArea = '$jobAreaId'";
	$dbc->query($sql);
	$dbc->next_record();
	$jobAreaDesc = $dbc->f('area');
	return $jobAreaDesc;

}

$sql = "select a.pg_thesis_id, a.id, DATE_FORMAT(a.report_date,'%d-%b-%Y') as report_date, a.thesis_title, a.thesis_type, 
b.description as thesisTypeDescription, a.introduction, a.objective, a.description, d.student_matrix_no, 
g.endorsed_by, DATE_FORMAT(a.endorsed_date,'%d-%b-%Y') AS endorsed_date, a.endorsed_remarks, a.status, a.verified_by,  
DATE_FORMAT(a.verified_date,'%d-%b-%Y') AS verified_date, a.verified_remarks, a.verified_status, a.discussion_status,
c1.description AS verified_desc, c2.description AS status_desc,
a.faculty_remarks_by, DATE_FORMAT(a.faculty_remarks_date,'%d-%b-%Y') AS faculty_remarks_date,
DATE_FORMAT(f.assigned_date,'%d-%b-%Y') as assigned_date, f.assigned_remarks, f.assigned_by,
DATE_FORMAT(f.recipient_date,'%d-%b-%Y') AS recipient_date, f.recipient_remarks,
f.pg_employee_empid
FROM pg_proposal a 
LEFT JOIN ref_thesis_type b ON (b.id = a.thesis_type)
LEFT JOIN ref_proposal_status c1 ON (c1.id=a.verified_status) 
LEFT JOIN ref_proposal_status c2 ON (c2.id=a.status) 
LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id )
LEFT JOIN pg_supervisor f ON (f.pg_student_matrix_no = d.student_matrix_no)
LEFT JOIN pg_proposal_approval g ON (g.id = a.pg_proposal_approval_id)
WHERE a.id = '$proposalId'
AND a.pg_thesis_id = '$thesisId'";
	
$result = $db->query($sql); 
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
		$pgThesisId=$db->f('pg_thesis_id');
		$id=$db->f('id');
		$studentMatrixNo=$db->f('student_matrix_no');
		$thesisTitle=$db->f('thesis_title');
		$thesisType=$db->f('thesis_type');
		$introduction=$db->f('introduction');
		$objective=$db->f('objective');
		$description=$db->f('description');
		$thesisTypeDescription=$db->f('thesisTypeDescription');
		$verifiedStatus=$db->f('verified_status');
		$verifiedDate=$db->f('verified_date');
		$verifiedRemarks=$db->f('verified_remarks');
		$endorsedBy=$db->f('endorsed_by');
		$verifiedBy=$db->f('verified_by');
		$endorsedStatus=$db->f('status');
		$endorsedDate=$db->f('endorsed_date');
		$endorsedRemarks=$db->f('endorsed_remarks');
		$reportDate=$db->f('report_date');
		$verifiedDesc=$db->f('verified_desc');
		$statusDesc=$db->f('status_desc');
		$discussionStatus=$db->f('discussion_status');
		$facultyRemarksDate=$db->f('faculty_remarks_date');
		
		$assignedBy=$db->f('assigned_by');
		$assignedDate=$db->f('assigned_date');
		$assignedRemarks=$db->f('assigned_remarks');
		
		$employeeId=$db->f('pg_employee_empid');
		$recipientDate=$db->f('recipient_date');
		$recipientRemarks=$db->f('recipient_remarks');
		
		?>
		<fieldset>
					<legend><strong>Verification by Faculty</strong></legend>					
					<table>
					<?
						if (substr($verifiedBy,0,3) != 'S07') {
							$dbConn1 = $dbc;
						}
						else {
							$dbConn1 = $dbc1;
						}
						
						$sql3 = "SELECT name AS verified_name
						FROM new_employee 
						WHERE empid = '$verifiedBy'"; 
						
						$result3 = $dbConn1->query($sql3);
						$dbConn1->next_record();
						$verifiedName=$dbConn1->f('verified_name');
						?>
						<tr>
							<td>Verification Status</td>	
							<td>:</td>
							<td><label><?=$verifiedDesc?></label></td>
						</tr>
						<tr>
							<td >Verified By</td>	
							<td >:</td>
							<td ><label><?=$verifiedName?></label></td>
						</tr>
						<tr>
							<td>Verified Date</td>	
							<td>:</td>
							<td><label><?=$verifiedDate?></label></td>
						</tr>
						<tr>	
							<td>Remarks </td>	
							<td>:</td>
							<td><label class="ckeditor" /><?=$verifiedRemarks?></label></td>	
						</tr>
					</table>
				</fieldset>
				<br/>
				<fieldset>
					<legend><strong>Endorsement by Senate</strong></legend>							
					<table>	
						<?
							if (substr($endorsedBy,0,3) != 'S07') {
								$dbConn2 = $dbc;
							}
							else {
								$dbConn2 = $dbc1;
							}

							$sql4 = "SELECT name AS endorsed_name
							FROM new_employee 
							WHERE empid = '$endorsedBy'"; 
							$result4 = $dbConn2->query($sql4);
							$dbConn2->next_record();
							$endorsedName=$dbConn2->f('endorsed_name');
						?>					
						<tr>
							<td>Endorsement Status</td>	
							<td>:</td>
							<td><label><?=$statusDesc?></label></td>
						</tr>
						<tr>
							<td>Endorsed By</td>	
							<td>:</td>
							<td><label><?=$endorsedName?></label></td>
						</tr>
						<tr>
							<td>Endorsed Date</td>	
							<td>:</td>
							<td><label><?=$endorsedDate?></label></td>
						</tr>
						<tr>	
							<td>Remarks </td>	
							<td>:</td>
							<td><label class="ckeditor" /><?=$endorsedRemarks?></label></td>	
						</tr>
					</table>
				</fieldset>
			<table>
			<tr>
				<td></td>
				<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../examiner/assign_examiner.php';" /></td>			
			</tr>	
		</table>
		<fieldset>
		<legend><strong>Outline of Proposed <?echo $thesisTypeDescription;?> by the Student</strong></legend>
		<br/>		
		<table>
		<tr>
			<td>Thesis Date</td>
			<td>:</td>
			<td><label name="reportDate" size="30" id="reportDate" ></label><?=$reportDate;?></td>
		</tr>
		<tr>
			<td>Thesis / Project ID</td>
			<td>:</td>
		    <td><label name="pgThesisId" size="30" id="pgThesisId" ></label><?=$pgThesisId;?></td>
		</tr>
		<?
			$sql1="SELECT name as student_name
					FROM student
					WHERE matrix_no = '$studentMatrixNo'";
			if (substr($studentMatrixNo,0,2) != '07') { 
				$dbConnStudent= $dbc; 
			} 
			else { 
				$dbConnStudent=$dbc1; 
			}		
			$result1 = $dbConnStudent->query($sql1);
			$dbConnStudent->next_record();
			$studentName = $dbConnStudent->f('student_name');
		?>
		<tr>
			<td>Matrix No</td>
			<td>:</td>
			<td><label name="studentMatrixNo" size="12" id="studentMatrixNo" ><?=$studentMatrixNo;?></label></td>
		</tr>
		<tr>
			<td>Student Name</td>
			<td>:</td>
			<td><label name="name" size="50" id="name" ><?=$studentName;?></label></td>
		</tr>  			
		<tr>
			<td>Thesis / Project Title</td>
			<td>:</td>
			<td><label name="thesisTitle" size="30" rows="3" id="thesisTitle"></label><?=$thesisTitle;?></td>
		</tr>  	
		<tr>
			<td>Thesis Type</td>
			<td>:</td>
			<td><label name="thesisTypeDescription" size="30" id="thesisTypeDescription" ></label><?=$thesisTypeDescription;?></td>			
		</tr>
		<tr>
			<td>Introduction</td>
			<td>:</td>
			<td><label name="introduction" id="introduction" class="ckeditor" /></label><?=$introduction;?></td>		
		</tr>
		<tr>
			<td>Objective</td>	
			<td>:</td>				
			<td><label name="objective" id="objective" class="ckeditor"></label><?=$objective?></td>
		</tr>
		<tr>
			<td>Brief Description</td>
			<td>:</td>
			<td><label name="description" id="description" class="ckeditor"></label><?=$description?></td>		
		</tr>
		</table>
		</fieldset>
		
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
			WHERE pg_proposal_id = '$proposalId'"; 

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
				<th width="117"><strong>Meeting Mode</strong></th>
				<th width="240"><strong>Notes</strong></th>
			  </tr>
			  <?
			  
				$sql2 = "SELECT pmd.id,pmd.lecturer_id, pmd.external_lecturer, rm.id, rm.description, pmd.meeting_mode, DATE_FORMAT(pmd.meeting_sdate,'%d-%b-%Y') as theMeetingDate,
				DATE_FORMAT(pmd.meeting_sdate,'%h:%i:%s %p') as theMeetingSTime, DATE_FORMAT(pmd.meeting_edate,'%h:%i:%s %p') as theMeetingETime, 
				pmd.remark, pmd.venue, pmd.pg_proposal_id
				FROM pg_meeting_detail pmd 
				LEFT JOIN ref_meeting_mode rm ON (rm.id=pmd.meeting_mode)
				WHERE pg_proposal_id = '$proposalId'"; 

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
						<td><label name="theMeetingDate" size="25" id="theMeetingDate" ><?=$theMeetingDate?></label></td>
						<td><label name="theMeetingSTime" size="25" id="theMeetingSTime" ><?=$theMeetingSTime?></label></td>
						<td><label name="meetingmode" size="25" id="meetingmode" >
						  <?=$meeting_mode?>
						  </label></td>
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
	<table>
		<tr>
			<td></td>
			<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../examiner/assign_examiner.php';" /></td>			
		</tr>	
	</table>
	  </form>
	</body>
</html>




