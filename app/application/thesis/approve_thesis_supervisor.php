<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: approve_thesis_supervisor.php
//
// Created by: Zuraimi
// Created Date: 26-Dec-2014
// Modified by: Zuraimi
// Modified Date: 26-Dec-2014
//
//**************************************************************************************


//Read common library for page execution i.e. database connection. login validation
include("../../../lib/common.php");
checkLogin();

$id=$_REQUEST['pid'];
$sql1 = "SELECT a.introduction, a.objective, a.description, a.feedback_by, 
		DATE_FORMAT(a.feedback_date,'%d-%b-%Y %h:%i:%s %p') as theFeedbackDate, a.feedback_remarks, a.status, 
		a.discussion_status,a.thesis_type, b.description as theThesisTypeDescription, 
		c.description as theProposalStatusDescription 
		FROM pg_proposal a, ref_thesis_type b, ref_proposal_status c
		WHERE a.id ='$id'
		AND a.thesis_type=b.id
		AND a.status = c.id";
		
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
		<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>
	</head>
	<body>
		<form id="form1" name="form1" method="post" enctype="multipart/form-data">
			<table>
				<tr>
					<td><br/><p><strong>Recommended Supervisor for the Student</strong></p></td>
				</tr>
			</table>
			
			<table>
			<?
			//do{
				$feedbackBy=$db->f('feedback_by');
				$feedbackDate=$db->f('theFeedbackDate');
				$feedbackRemarks=$db->f('feedback_remarks');
				$theProposalStatusDescription=$db->f('theProposalStatusDescription');		
				$introduction=$db->f('introduction');
				$objective=$db->f('objective');
				$description=$db->f('description');
				$theThesisTypeDescription=$db->f('theThesisTypeDescription');
				$discussionStatus=$db->f('discussion_status');		
			?>
				<tr>
					<td>Feedback By</td>	
					<td><input type="text" name="feedbackBy" cols="45" id="feedbackBy" value="<?=$feedbackBy?>" disabled="disabled"></td>
				</tr>
				<tr>
					<td>Feedback Date</td>	
					<td><input type="text" name="feedbackDate" size="25" id="feedbackDate" value="<?=$feedbackDate?>" disabled="disabled"/></td>
				</tr>
				<tr>	
					<td>Feedback Remarks</td>	
					<td><textarea name="feedbackRemarks" cols="30" rows="3" disabled="disabled" id="feedbackRemarks" class="ckeditor" /><?=$feedbackRemarks?></textarea></td>	
				</tr>
				<tr>
					<td>Introduction</td>
					<td><textarea name="introduction" cols="30" rows="3" disabled="disabled" id="introduction" class="ckeditor" /><?=$introduction?></textarea></td>		
				</tr>
				<tr>
					<td>Objective</td>			
					<td><textarea name="objective" cols="30" rows="3" disabled="disabled" id="objective" class="ckeditor" /><?=$objective?></textarea></td>
				</tr>
				<tr>
					<td>Brief Description of <?=$theThesisTypeDescription?></td>
					<td><textarea name="theThesisTypeDescription" cols="30" rows="3" disabled="disabled" id="theThesisTypeDescription" class="ckeditor" /><?=$theThesisTypeDescription?></textarea></td>		
				</tr>
				<tr>
				  <td>Proposal Remarks</td>
					<td><textarea name="proposalRemarks" cols="30" rows="3" disabled="disabled" id="proposalRemarks" class="ckeditor" /><?=$proposalRemarks?></textarea></td>		
				  </tr>
				<tr>
					<td>Discussion Status with Lecturer</td>	
					<td><input type="text" name="discussionStatus" size="5" id="discussionStatus" value="<?=$discussionStatus?>" disabled="disabled"/></td>
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
					<td>Notes</td>							
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
				<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='approve_proposal.php';" /></td>		
			</tr>
		</table>
		</form>
	</body>
</html>




