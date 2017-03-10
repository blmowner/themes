<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: detail_proposal_history.php
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

$id=$_GET['pid'];
$matrix_no=$_GET['mn'];

function retrieveJobAreaId($jobAreaId)
{
	global $dbc;
	$sql = "SELECT area from job_list_category WHERE JobArea = '$jobAreaId'";
	$dbc->query($sql);
	$dbc->next_record();
	$jobAreaDesc = $dbc->f('area');
	return $jobAreaDesc;

}

$sql1 = "SELECT a.thesis_title, b.description as thesis_type_desc, a.introduction, a.objective, a.description, a.endorsed_by, 
		DATE_FORMAT(a.endorsed_date,'%d-%b-%Y') AS theEndorsedDate, a.endorsed_remarks, a.status, a.verified_by, 
		DATE_FORMAT(a.verified_date,'%d-%b-%Y') AS theVerifiedDate, a.verified_remarks, a.verified_status, a.discussion_status, 
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
    <script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
	</head>
	<body>
		<form id="form1" name="form1" method="post" enctype="multipart/form-data">
				
			
			<?
			//do{
				$thesisTitle=$db->f('thesis_title');
				$thesisTypeDesc=$db->f('thesis_type_desc');
				$endorsedBy=$db->f('endorsed_by');
				$name=$db->f('name');
				$endorsedDate=$db->f('theEndorsedDate');
				$approvalRemark=$db->f('approval_remark');
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
									
			?>
			<fieldset>
				<legend><strong>Verification by Faculty</strong></legend>
				<table width="410">
					<tr>
						<td>Verification Status</td>	
						<td>:</td>
						<td><label><?=$verifiedDesc?></label></td>
					</tr>
					<?
					$sql4 = "SELECT name AS verified_name
							FROM new_employee 
							WHERE empid = '$verifiedBy'";
								
							$result4 = $dbc->query($sql4); 
							$dbc->next_record();
							$verifiedName=$dbc->f('verified_name');
				
					?>
					<tr>
						<td width="139">Verified By</td>	
						<td width="10">:</td>
					<td width="252"><label><?=$verifiedName?></label></td>
				  
					<tr>
						<td>Verified Date</td>	
						<td>:</td>
						<td><label><?=$verifiedDate?></label></td>
					</tr>
					<tr>	
						<td>Remarks </td>	
						<td>:</td>
						<td><label class="ckeditor"/><?=$verifiedRemarks?></label></td>	
					</tr>
				</table>
			</fieldset>
			</br>
			<fieldset>
				<legend><strong>Endorsement by Senate</strong></legend>
				<table width="410">
					<tr>
						<td>Endorsement Status</td>	
						<td>:</td>
						<td><label><?=$statusDesc?></label></td>
					</tr>
					<?
					$sql5 = "SELECT name AS endorsed_name
							FROM new_employee 
							WHERE empid = '$endorsedBy'";
								
							$result5 = $dbc->query($sql5); 
							$dbc->next_record();
							$endorsedName=$dbc->f('endorsed_name');
				
					?>
					<tr>
						<td width="135">Endorsed By</td>	
						<td width="10">:</td>
					  <td width="251"><label><?=$endorsedName?></label></td>
					</tr>
					<tr>
						<td>Endorsed Date</td>	
						<td>:</td>
						<td><label><?=$endorsedDate?></label></td>
					</tr>
					<tr>	
						<td>Remarks </td>	
						<td>:</td>
						<td><label class="ckeditor"/><?=$approvalRemark?></label></td>	
					</tr>
			  </table>
			</fieldset>
			<br/>			
			<fieldset>
				<legend><strong>Outline of Proposed Research/Case Study</strong></legend>
				</table>
				<table width="98%">
					<tr>
						<td width="128">Thesis Title</td>
						<td width="10">:</td>
					  <td><label><?=$thesisTitle?></label></td>		
					</tr>
					<tr>
						<td>Thesis Type</td>
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
					
				<?
				//}while($db->next_record());
				//?>
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
			WHERE pg_proposal_id = '$id'"; 

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
                <th align="center" width="29"><strong>No</strong></th>
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
				WHERE pg_proposal_id = '$id'
				ORDER BY meeting_sdate DESC"; 
					
				$result2 = $dbb->query($sql2); 
				$dbb->next_record();
				$row_cnt2 = mysql_num_rows($result2);

				if ($row_cnt2 > 0) {
					$no=1;
					do
					{
						$lecturerId=$dbb->f('lecturer_id');
						
						$sql3 = "SELECT name
						FROM new_employee
						WHERE empid = '$lecturerId'"; 
			
						$result3 = $dbc->query($sql3); 
						$dbc->next_record();
						
						$lecturerName = $dbc->f('name');
						$externalLecturer = $dbb->f('external_lecturer');
						$theMeetingDate=$dbb->f('theMeetingDate');
						$theMeetingSTime=$dbb->f('theMeetingSTime');
						$remark=$dbb->f('remark'); 
					?>
				  <tr>
					<td align="center"><label name="no" size="3" id="no" ><?=$no?>.</label></td>
					<td><label name="lecturerName" size="15" id="lecturerName"><?=$lecturerName?></label></td>
					<td><label name="externalLecturer" size="15" id="externalLecturer"><?=$externalLecturer?></label></td>
					<td><label name="theMeetingDate" size="25" id="theMeetingDate" ><?=$theMeetingDate?></label></td>
					<td><label name="theMeetingSTime" size="25" id="theMeetingSTime" ><?=$theMeetingSTime?></label></td>
					<td><label name="remark" cols="20" disabled="disabled" id="remark"><?=$remark?></label></td>
				  </tr>
				  <?
					$no=$no+1;
					}while($dbb->next_record());
				}
			else {
				?>
				<table>
					<tr>		
						<td><label>No record found!</label></td>		
					</tr>
				</table>
				<?
			}
			?>  
			</table>
			</fieldset>
		<br/>
		<table>
			<tr>		
				<td><input type="button" name="btnBack" value="Back to History List" onclick="javascript:document.location.href='../thesis/proposal_history_staff_detail.php?pid=<?=$id?>&matrix_no=<?=$matrix_no?>';" /></td>		
			</tr>
		</table>
		</form>
	</body>
</html>




