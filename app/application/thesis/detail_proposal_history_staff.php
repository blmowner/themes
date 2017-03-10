<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: detail_proposal_history_staff.php
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
$sql1 = "SELECT a.thesis_title, b.description as thesis_type_desc, a.introduction, a.objective, a.description, a.endorsed_by, 
		DATE_FORMAT(a.endorsed_date,'%d-%b-%Y') AS theEndorsedDate, d.endorsed_remarks, d.endorsed_status, a.verified_by, 
		DATE_FORMAT(a.verified_date,'%d-%b-%Y') AS theVerifiedDate, a.verified_remarks, a.verified_status, a.discussion_status, 
		a.thesis_type, b.description AS theThesisTypeDescription, c1.description AS verified_desc, 
		c2.description AS status_desc 
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
				$endorsedRemarks=$db->f('endorsed_remarks');
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
				<fieldset>
					<legend><strong>Verification by Faculty</strong></legend>					
					<table>
						<tr>
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
							<td width="126">Verified By</td>	
							<td width="10">:</td>
							<td width="396"><label><?=$verifiedName?></label></td>
						</tr>
						<tr>
							<td>Verification Status</td>	
							<td>:</td>
							<td><label><?=$verifiedDesc?></label></td>
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
					<table width="558">				
						<tr>
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
						<td width="127">Endorsed By</td>	
							<td width="10">:</td>
						  <td width="405"><label><?=$endorsedName?></label></td>
						</tr>
						<tr>
							<td>Endorsement Status</td>	
							<td>:</td>
							<td><label><?=$statusDesc?></label></td>
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
				<br/>
				<fieldset>
					<legend><strong>Outline of Proposed Research/Case Study</strong></legend>							
					<table width="98%">
						<tr>
							<td>Thesis Title</td>
							<td>:</td>
							<td><label><?=$thesisTitle?></label></td>		
						</tr>
						<tr>
							<td>Thesis Type</td>
							<td>:</td>
							<td><label><?=$thesisTypeDesc?></label></td>		
						</tr>
						<tr>
							<td width="141">Introduction</td>
							<td width="10">:</td>
						  <td width="1103"><label class="ckeditor" /><?=$introduction?></label></td>		
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
					</table>
				</fieldset>
				<br/>
				<?
				$sql2 = "SELECT pmd.id,pmd.lecturer_id, pmd.meeting_mode, rm.id, rm.description, DATE_FORMAT(pmd.meeting_sdate,'%d-%b-%Y') as theMeetingDate, 
				DATE_FORMAT(pmd.meeting_sdate,'%h:%i:%s %p') as theMeetingSTime, DATE_FORMAT(pmd.meeting_edate,'%h:%i:%s %p') as theMeetingETime, 
				pmd.remark, pmd.venue, pmd.pg_proposal_id
				FROM pg_meeting_detail pmd
				LEFT JOIN ref_meeting_mode rm ON (rm.id=pmd.meeting_mode) 
				WHERE pg_proposal_id = '$id'"; 
					
				$result2 = $dba->query($sql2); 
				$dba->next_record();
				$row_cnt = mysql_num_rows($result2);	
				?>

					<fieldset>
					<legend><strong>Discussion Details</strong></legend>
						<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="100%" class="thetable">
							<tr>						
								<th width="64">No</th>	
								<th width="290">Lecturer Name</th>	
								<th width="294">Meeting Date</th>
								<th width="175"><strong>Meeting Mode</strong></th>
								<th width="399">Meeting Time</th>					
								<th width="211">Notes</th>							
							</tr>
							<?
							if ($row_cnt >0) {
								$no=1;
								do
								{
									$lecturerId=$dba->f('lecturer_id');
									
									if (substr($lecturerId,0,3) != 'S07') {
										$dbConn = $dbc;
									}
									else {
										$dbConn = $dbc1;
									}
						
									$sql3 = "SELECT name
									FROM new_employee
									WHERE empid = '$lecturerId'"; 
						
									$result3 = $dbConn->query($sql3); 
									$dbConn->next_record();
									
									$lecturerName = $dbConn->f('name');
									$externalLecturer = $dba->f('external_lecturer');
									$theMeetingDate=$dba->f('theMeetingDate');
									$theMeetingSTime=$dba->f('theMeetingSTime');
									$remark=$dba->f('remark');
									$meeting_mode=$dba->f('description');
								?>
								<tr>		
									<td><label name="no" size="3" id="no" ></label><?=$no?></td>
									<td><label name="lecturerName" size="15" id="lecturerName" ></label><?=$lecturerName?></td>
									<td><label name="theMeetingDate" size="25" id="theMeetingDate" ></label><?=$theMeetingDate?></td>
									<td><label name="meetingmode" size="25" id="meetingmode" >
                                      <?=$meeting_mode?>
                                    </label></td>
									<td><label name="theMeetingSTime" size="25" id="theMeetingSTime" ></label><?=$theMeetingSTime?></td>
									<td><label name="remark" class="ckeditor" id="remark"></label><?=$remark?></td>		
								</tr>	
								<?
								$no=$no+1;
								}while($dba->next_record());
							}
							else {
								?>
								<table>
								<tr>
									<td><label>No record(s) found.</label></td>
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
					<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='proposal_history_staff.php';" /></td>		
				</tr>
			</table>
		</form>
	</body>
</html>




