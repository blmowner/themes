<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: progress_detail_history.php
//
// Created by: Zuraimi
// Created Date: 06 Apr 2015
// Modified by: Zuraimi
// Modified Date: 06 Apr 2015
//
//**************************************************************************************


//Read common library for page execution i.e. database connection. login validation
include("../../../lib/common.php");
checkLogin();

$id=$_REQUEST['pid'];
$sql1 = "SELECT a.thesis_title, b.description as thesis_type_desc, a.introduction, a.objective, a.description, a.endorsed_by, 
		DATE_FORMAT(d.endorsed_date,'%d-%b-%Y') AS theEndorsedDate, d.endorsed_remarks, a.status, a.verified_by, 
		DATE_FORMAT(a.verified_date,'%d-%b-%Y') AS theVerifiedDate, a.verified_remarks, a.verified_status, a.discussion_status, 
		a.thesis_type, b.description AS theThesisTypeDescription, c1.description AS verified_desc, c2.description AS status_desc 
		FROM pg_proposal a 
		LEFT JOIN ref_thesis_type b ON (b.id=a.thesis_type) 
		LEFT JOIN ref_proposal_status c1 ON (c1.id=a.verified_status) 
		LEFT JOIN ref_proposal_status c2 ON (c2.id=a.status) 
		LEFT JOIN pg_proposal_approval d ON (d.id = a.pg_proposal_approval_id)
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
				$thesisTitle=$db->f('thesis_title');
				$thesisTypeDesc=$db->f('thesis_type_desc');
				$endorsedBy=$db->f('endorsed_by');
				$name=$db->f('name');
				$endorsedDate=$db->f('theEndorsedDate');
				$approvalRemark=$db->f('endorsed_remarks');
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
				<table width="98%">
					<tr>
						<td width="15%">Verification Status</td>	
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
						<td>Verified By</td>	
						<td>:</td>
					<td><label><?=$verifiedName?></label></td>
				  
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
				<table width="98%">
					<tr>
						<td width="15%">Endorsement Status</td>	
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
						<td><label class="ckeditor"/><?=$approvalRemark?></label></td>	
					</tr>
			  </table>
			
			</fieldset>
			<fieldset>
				<legend><strong>Outline of Proposed Research/Case Study</strong></legend>
				<div id = "tabledisplay" style="overflow:auto; height:300px;">
				<table width="98%">
					<tr>
						<td><label><strong>Thesis Type</strong>: <?=$thesisTypeDesc?></label></td>
					</tr>

					<tr>
						<td><label><br/><strong>Thesis / Project Title</strong></label></td>
					</tr>
					<tr>
					  <td width="232"><label><?=$thesisTitle?></label></td>		
					</tr>
					<tr>
						<td><br/><label><strong>Introduction</strong></label></td>
					</tr>
					<tr>
					  <td><label class="ckeditor"/><?=$introduction?></label></td>		
					</tr>
					<tr>
						<td><br/><label><strong>Objective</strong></label></td>	
					</tr>
					<tr>
						<td><label class="ckeditor"/><?=$objective?></label></td>
					</tr>
					<tr>
						<td><br/><label><strong>Brief Description</strong></label></td>
					</tr>
					<tr>
						<td><label class="ckeditor"/><?=$description?></label></td>		
					</tr>					
				<?
				//}while($db->next_record());
				//?>
				</table>
				</div>
			</fieldset>
              <?
			  $sql2 = "SELECT id,lecturer_id, external_lecturer, DATE_FORMAT(meeting_sdate,'%d-%b-%Y') as theMeetingDate, 
						DATE_FORMAT(meeting_sdate,'%h:%i:%s %p') as theMeetingSTime, DATE_FORMAT(meeting_edate,'%h:%i:%s %p') as theMeetingETime, 
						remark, venue, pg_proposal_id
						FROM pg_meeting_detail
						WHERE pg_proposal_id = '$id'"; 
					
				$result2 = $dba->query($sql2); 
				$dba->next_record();
				$row_cnt2 = mysql_num_rows($result2);
				?>
			<br/>
			<fieldset>
				<legend><strong>Discussion Details</strong></legend>
				
			<table border="1" cellpadding="2" cellspacing="1" width="100%" id="inputs10" class="thetable">
				<tr>
					<th width="29"><strong>No</strong></th>
					<th width="155"><strong>Lecturer Name</strong></th>
					<th width="155"><strong>External Lecturer</strong></th>
					<th width="140"><strong>Meeting Date</strong></th>
					<th width="117"><strong>Meeting Time</strong></th>
					<th width="240"><strong>Notes</strong></th>
				</tr>
				<?
				if ($row_cnt2 > 0) {
					$no=1;
					do
					{
						$lecturerId=$dba->f('lecturer_id');
						
						$sql3 = "SELECT name
						FROM new_employee
						WHERE empid = '$lecturerId'"; 
			
						$result3 = $dbc->query($sql3); 
						$dbc->next_record();
						
						$lecturerName = $dbc->f('name');
						$externalLecturer = $dbc->f('external_lecturer');
						$theMeetingDate=$dba->f('theMeetingDate');
						$theMeetingSTime=$dba->f('theMeetingSTime');
						$remark=$dba->f('remark');  
						?>
						<tr>
							<td><label name="no" size="3" id="no" ><?=$no?></label></td>
							<td><label name="lecturerName" size="15" id="lecturerName" ><?=$lecturerName?></label></td>
							<td><label name="externalLecturer" size="15" id="externalLecturer" ><?=$externalLecturer?></label></td>
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
		<table>
			<tr>		
				<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../monthlyreport/progress_history.php';" /></td><!--window.history.back()-->		
			</tr>
		</table>
		</form>
	</body>
</html>




