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
$sql1 = "SELECT a.introduction, a.objective, a.description, d1.name as confirm_name,d2.name as approved_name,
		a.approved_by, DATE_FORMAT(a.approved_date,'%d-%b-%Y') as theApprovedDate, a.approval_remark, a.status, 
		a.confirm_by, DATE_FORMAT(a.confirm_date,'%d-%b-%Y') as theConfirmDate, a.confirm_remarks, a.confirm_status,
		a.discussion_status, a.proposal_remarks, a.thesis_type, b.description as theThesisTypeDescription, 
		c1.description as confirm_desc, c2.description as status_desc
		FROM pg_proposal a
		LEFT JOIN ref_thesis_type b on (b.id=a.thesis_type)
		LEFT JOIN ref_proposal_status c1 on (c1.id=a.confirm_status)
		LEFT JOIN ref_proposal_status c2 on (c2.id=a.status)
		LEFT JOIN new_employee d1 on (d1.empid = a.confirm_by)
		LEFT JOIN new_employee d2 on (d2.empid = a.approved_by)
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
				$approvedBy=$db->f('approved_by');
				$name=$db->f('name');
				$approvedDate=$db->f('theApprovedDate');
				$approvalRemark=$db->f('approval_remark');
				$statusDesc=$db->f('status_desc');
				$confirmBy=$db->f('confirm_by');
				$confirmDate=$db->f('theConfirmDate');
				$confirmRemarks=$db->f('confirm_remarks');
				$confirmDesc=$db->f('confirm_desc');				
				$introduction=$db->f('introduction');
				$objective=$db->f('objective');
				$description=$db->f('description');
				$theThesisTypeDescription=$db->f('theThesisTypeDescription');
				$proposalRemarks=$db->f('proposal_remarks');
				$discussionStatus=$db->f('discussion_status');		
				$confirmName=$db->f('confirm_name');
				$approvedName=$db->f('approved_name');					
			?>
			<table>
				<tr>
					<td><strong>Approval Information by Senate </strong></td>
				</tr>
			</table>
			<table width="558">				
				<tr>
					<td width="127">Approved By</td>	
				    <td width="10">:</td>
			      <td width="405"><label><?=$approvedName?></label></td>
				</tr>
				<tr>
					<td>Approval Status</td>	
					<td>:</td>
					<td><label><?=$statusDesc?></label></td>
				</tr>
				<tr>
					<td>Approved Date</td>	
					<td>:</td>
					<td><label><?=$approvedDate?></label></td>
				</tr>
				<tr>	
					<td>Remarks </td>	
					<td>:</td>
					<td><label class="ckeditor" /><?=$approvalRemark?></label></td>	
				</tr>
			</table>
			<br/>
			<table>
				<tr>
					<td><strong>Confirmation Information by Faculty</strong></td>
				</tr>
			</table>
			<table>
				<tr>
					<td width="126">Confirmed By</td>	
					<td width="10">:</td>
					<td width="396"><label><?=$confirmName?></label></td>
				</tr>
				<tr>
					<td>Confirmation Status</td>	
					<td>:</td>
					<td><label><?=$confirmDesc?></label></td>
				</tr>
				<tr>
					<td>Confirmation Date</td>	
					<td>:</td>
					<td><label><?=$confirmDate?></label></td>
				</tr>
				<tr>	
					<td>Remarks </td>	
					<td>:</td>
					<td><label class="ckeditor" /><?=$confirmRemarks?></label></td>	
				</tr>
			</table>
			<br/>
			<table>
				<tr>
					<td><strong>Outline of Proposed Research/Case Study</strong></td>
				</tr>
			</table>
			<table>
				<tr>
					<td width="128">Introduction</td>
					<td width="10">:</td>
					<td width="147"><label class="ckeditor" /><?=$introduction?></label></td>		
				</tr>
				<tr>
					<td>Objective</td>			
					<td>:</td>
					<td><label class="ckeditor" /><?=$objective?></label></td>
				</tr>
				<tr>
					<td>Brief Description</td>
					<td>:</td>
					<td><label class="ckeditor" /><?=$theThesisTypeDescription?></label></td>		
				</tr>
				<tr>
				  <td> Remarks by Student</td>
					<td>:</td>
					<td><label class="ckeditor" /><?=$proposalRemarks?></label></td>		
				</tr>
				<tr>
					<td>Discussion status with Lecturer</td>	
					<td>:</td>
					<?if (strcmp($discussionStatus,"Yes")==0) {
						?><td><label>: Yes</label></td>
					<?}
					else {
						?><td width="27"><label>No</label></td>
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
				<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='proposal_history_staff.php';" /></td>		
			</tr>
		</table>
		</form>
	</body>
</html>




