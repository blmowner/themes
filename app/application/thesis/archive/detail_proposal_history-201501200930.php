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

$id=$_REQUEST['pid'];
$sql1 = "SELECT a.thesis_title, b.description as thesis_type_desc, a.introduction, a.objective, a.description, d1.name AS verified_name,d2.name AS endorsed_name, a.endorsed_by, 
		DATE_FORMAT(a.endorsed_date,'%d-%b-%Y') AS theEndorsedDate, a.endorsed_remarks, a.status, a.verified_by, 
		DATE_FORMAT(a.verified_date,'%d-%b-%Y') AS theVerifiedDate, a.verified_remarks, a.verified_status, a.discussion_status, 
		a.proposal_remarks, a.thesis_type, b.description AS theThesisTypeDescription, c1.description AS verified_desc, c2.description AS status_desc 
		FROM pg_proposal a 
		LEFT JOIN ref_thesis_type b ON (b.id=a.thesis_type) 
		LEFT JOIN ref_proposal_status c1 ON (c1.id=a.verified_status) 
		LEFT JOIN ref_proposal_status c2 ON (c2.id=a.status) 
		LEFT JOIN new_employee d1 ON (d1.empid = a.verified_by) 
		LEFT JOIN new_employee d2 ON (d2.empid = a.endorsed_by) 
		WHERE a.id ='$id'";
		
$sql2 = "SELECT id,lecturer_name, DATE_FORMAT(meeting_sdate,'%d-%b-%Y') as theMeetingDate, 
		DATE_FORMAT(meeting_sdate,'%h:%i:%s %p') as theMeetingSTime, DATE_FORMAT(meeting_edate,'%h:%i:%s %p') as theMeetingETime, 
		remark, venue, pg_proposal_id
		FROM pg_meeting_detail
		WHERE pg_proposal_id = '$id'"; 
	
/*LIMIT $startpoint, $perpage*/

$result1 = $db->query($sql1); 
$result2 = $dba->query($sql2); 

//echo $sql1;
//echo $sql2;
//var_dump($db);
$db->next_record();
$dba->next_record();
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
		<script type="text/javascript" src="//cdn.ckeditor.com/4.4.6/standard/ckeditor.js"></script>
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
				$proposalRemarks=$db->f('proposal_remarks');
				$discussionStatus=$db->f('discussion_status');		
				$verifiedName=$db->f('verified_name');
				$endorsedName=$db->f('endorsed_name');					
			?>
			<table>
				<tr>
					<td><strong>Verification by Faculty </strong>
				</tr>
			</table>
			<table width="410">
				<tr>
					<td>Verification Status</td>	
					<td>:</td>
					<td><label><?=$verifiedDesc?></label></td>
				</tr>
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
			</br>
			<table>
				<tr>
					<td><strong>Endorsement by Senate </strong></td>
				</tr>
			</table>
			<table width="410">
				<tr>
					<td>Endorsement Status</td>	
					<td>:</td>
					<td><label><?=$statusDesc?></label></td>
				</tr>
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
			<br/>			
			<table>
				<tr>
					<td><strong>Outline of Proposed Research/Case Study </strong></td>	
				</tr>
			</table>
			<table width="410">
				<tr>
					<td width="128">Thesis Title</td>
					<td width="10">:</td>
				  <td width="232"><label><?=$thesisTitle?></label></td>		
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
				<tr>
				  <td>Remarks by Student</td>
					<td>:</td>
					<td><label class="ckeditor"/><?=$proposalRemarks?></label></td>		
			  </tr>
				<tr>
					<td>Discussion status with Lecturer</td>	
					<td>:</td>
					<?if (strcmp($discussionStatus,"Yes")==0) {
						?><td><label>Yes</label></td>
					<?}
					else {
						?><td width="20"><label>No</label></td>
					<?
					}?>
				</tr>	
			<?
			//}while($db->next_record());
			//?>
			</table>
			<? 
			if (strcmp($discussionStatus,"Yes")==0) {?>
			<table>
			<tr>		
				<td>Discussion Details</td>		
			</tr>
			</table>
			<table>
				<tr>						
					<td>No</td>	
					<td>Lecturer Name</td>	
					<td>Meeting Date</td>
					<td>Start Time</td>
					<td>End Time</td>
					<td>Venue</td>
					<td>Remark</td>							
				</tr>
				<?
				$no=1;
				do
				{
					$lecturerName=$dba->f('lecturer_name');
					$theMeetingDate=$dba->f('theMeetingDate');
					$theMeetingSTime=$dba->f('theMeetingSTime');
					$theMeetingETime=$dba->f('theMeetingETime');
					$remark=$dba->f('remark');
					$venue=$dba->f('venue');	

				?>
				<tr>		
					<td><input type="text" name="no" size="3" id="no" value="<?=$no?>" disabled="disabled"/></td>
					<td><input type="text" name="lecturerName" size="15" id="lecturerName" value="<?=$lecturerName?>" disabled="disabled"/></td>
					<td><input type="text" name="theMeetingDate" size="25" id="theMeetingDate" value="<?=$theMeetingDate?>" disabled="disabled"/></td>
					<td><input type="text" name="theMeetingSTime" size="25" id="theMeetingSTime" value="<?=$theMeetingSTime?>" disabled="disabled"/></td>
					<td><input type="text" name="theMeetingETime" size="25" id="theMeetingETime" value="<?=$theMeetingETime?>" disabled="disabled"/></td>
					<td><input type="text" name="venue" size="20" id="venue" value="<?=$venue?>" disabled="disabled"/></td>
					<td><textarea name="remark" cols="20" disabled="disabled" id="remark"><?=$remark?>
					</textarea></td>		
				</tr>	
				<?
				$no=$no+1;
				}while($dba->next_record());
				?>
			</table>
			<?}?>
		<table>
			<tr>		
				<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../student/student_programme.php';" /></td>		
			</tr>
		</table>
		</form>
	</body>
</html>




