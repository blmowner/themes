<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: assign_supervisor_ouline.php
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

///////////////////////////////////////////////////////////////
		
		$sql = "select a.pg_thesis_id, a.id, DATE_FORMAT(a.report_date,'%d-%b-%Y') as report_date, a.thesis_title, a.thesis_type, 
		b.description as thesisTypeDescription, a.introduction, a.objective, a.description, d.student_matrix_no, e.name,
		d1.name AS verified_name,d2.name AS endorsed_name, a.endorsed_by, 
		DATE_FORMAT(a.endorsed_date,'%d-%b-%Y') AS endorsed_date, a.endorsed_remarks, a.status, a.verified_by, 
		DATE_FORMAT(a.verified_date,'%d-%b-%Y') AS verified_date, a.verified_remarks, a.verified_status, a.discussion_status,
		c1.description AS verified_desc, c2.description AS status_desc
		FROM pg_proposal a
		LEFT JOIN ref_thesis_type b ON (b.id = a.thesis_type)
		LEFT JOIN ref_proposal_status c1 ON (c1.id=a.verified_status) 
		LEFT JOIN ref_proposal_status c2 ON (c2.id=a.status) 
		LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id )
		LEFT JOIN new_employee d1 ON (d1.empid = a.verified_by) 
		LEFT JOIN new_employee d2 ON (d2.empid = a.endorsed_by) 
		LEFT JOIN student e ON (e.matrix_no = d.student_matrix_no)
		WHERE a.id = '$proposalId'
		AND a.pg_thesis_id = '$thesisId'";
			
		$result = $db->query($sql); 
		//echo $sql;
		//var_dump($db);
		$db->next_record();

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
		$pgThesisId=$db->f('pg_thesis_id');
		$id=$db->f('id');
		$studentMatrixNo=$db->f('student_matrix_no');
		$name=$db->f('name');
		$thesisTitle=$db->f('thesis_title');
		$thesisType=$db->f('thesis_type');
		$introduction=$db->f('introduction');
		$objective=$db->f('objective');
		$description=$db->f('description');
		$thesisTypeDescription=$db->f('thesisTypeDescription');
		$verifiedName=$db->f('verified_name');
		$verifiedStatus=$db->f('verified_status');
		$verifiedDate=$db->f('verified_date');
		$verifiedRemarks=$db->f('verified_remarks');
		$endorsedName=$db->f('endorsed_name');
		$endorsedStatus=$db->f('status');
		$endorsedDate=$db->f('endorsed_date');
		$endorsedRemarks=$db->f('endorsed_remarks');
		$reportDate=$db->f('report_date');
		$verifiedDesc=$db->f('verified_desc');
		$statusDesc=$db->f('status_desc');
		$discussionStatus=$db->f('discussion_status');
		
		?>
		<fieldset>
					<legend><strong>Verification by Faculty</strong></legend>					
					<table>
						<tr>
							<td>Verification Status</td>	
							<td>:</td>
							<td><label><?=$verifiedDesc?></label></td>
						</tr>
						<tr>
							<td width="146">Verified By</td>	
							<td width="10">:</td>
							<td width="396"><label><?=$verifiedName?></label></td>
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
							<td>Endorsement Status</td>	
							<td>:</td>
							<td><label><?=$statusDesc?></label></td>
						</tr>
						<tr>
							<td width="143">Endorsed By</td>	
							<td width="10">:</td>
						  <td width="396"><label><?=$endorsedName?></label></td>
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
		<legend><strong>Outline of Proposed <?echo $thesisTypeDescription;?> by the Student</strong></legend>
		<br/>		
		<table width="428">
		<tr>
			<td width="132">Thesis Date</td>
			<td width="10">:</td>
	      <td width="231"><label name="reportDate" size="30" id="reportDate" ></label><?=$reportDate;?></td>
		</tr>
		<tr>
			<td>Thesis ID</td>
			<td>:</td>
		    <td><label name="pgThesisId" size="30" id="pgThesisId" ></label><?=$pgThesisId;?></td>
		</tr>
		<tr>
			<td>Matrix No</td>
			<td>:</td>
			<td><label name="studentMatrixNo" size="12" id="studentMatrixNo" ><?=$studentMatrixNo;?></label></td>
		</tr>
		<tr>
			<td>Student Name</td>
			<td>:</td>
			<td><label name="name" size="50" id="name" ><?=$name;?></label></td>
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
		<tr>
						<td>Discussion status with Lecturer</td>	
						<td>:</td>
						<?if ($discussionStatus=="Y") {
							?><td><label>Yes</label></td>
						<?}
						else {
							?><td width="35"><label>No</label></td>
						<?
						}?>
		  </tr>
		</table>
		</fieldset>
		
		
		<? 			
		if ($discussionStatus=="Y") 
		{?>
		<br/>
		<fieldset>
		<legend><strong>Discussion Details</strong></legend>
			<table width="100%" border="1" cellpadding="2" cellspacing="1" style="border-collapse:collapse;">
				<tr>						
					<td width="25"><strong>No</strong></td>	
					<td width="131"><strong>Meeting Date</strong></td>
					<td width="131"><strong>Meeting Time</strong></td>					
					<td width="165"><strong>Lecturer Name</strong></td>	
					<td width="89"><strong>Remark</strong></td>							
				</tr>
				<?
				$sql2 = "SELECT id,lecturer_name, DATE_FORMAT(meeting_sdate,'%d-%b-%Y') as theMeetingDate, 
				DATE_FORMAT(meeting_sdate,'%h:%i:%s %p') as theMeetingSTime, DATE_FORMAT(meeting_edate,'%h:%i:%s %p') as theMeetingETime, 
				remark, venue, pg_proposal_id
				FROM pg_meeting_detail
				WHERE pg_proposal_id = '$id'"; 
				
				$result2 = $dba->query($sql2); 

				$no=1;
				while($dba->next_record())
				{
					$lecturerName=$dba->f('lecturer_name');
					$theMeetingDate=$dba->f('theMeetingDate');
					$theMeetingSTime=$dba->f('theMeetingSTime');
					$remark=$dba->f('remark');
				?>
				<tr>		
					<td><label name="no" size="3" id="no" ></label><?=$no?></td>					
					<td><label name="theMeetingDate" size="25" id="theMeetingDate" ></label><?=$theMeetingDate?></td>
					<td><label name="theMeetingSTime" size="25" id="theMeetingSTime" ></label><?=$theMeetingSTime?></td>
					<td><label name="lecturerName" size="15" id="lecturerName" ></label><?=$lecturerName?></td>
					<td><label name="remark" class="ckeditor" id="remark"></label><?=$remark?></td>		
				</tr>	
				<?
				$no=$no+1;
				};
				?>
			</table>
		</fieldset>
		<?}?>
		<br/>
		<fieldset>
		<legend><strong>Remark by Faculty to this proposal</strong></legend>		
			<table>
				<tr>
					<td>Remark By</td>
					<td>:</td>
					<td><label name="remarksBy" size="50" id="remarksBy" ></label><?=$employeeName;?></td>					
				</tr>
				<tr>
					<td>Remark Date</td>
					<td>:</td>
					<td><label name="remarksDate" size="15" id="remarksDate" ></label><?=$remarksDate;?></td>					
				</tr>
				<tr>
					<td>Remarks</td>
					<td></td>
					<td><label name="verifiedRemarks" id="verifiedRemarks" class="ckeditor" cols="50" rows="3"><?=$verifiedRemarks;?></label></td>					
				</tr>
			</table>
		</fieldset>
		
	<table>
		<tr>
			<td></td>
			<td></td>
			<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='assign_supervisor.php';" /></td>			
		</tr>	
	</table>
	<br/>
	  </form>
	</body>
</html>




