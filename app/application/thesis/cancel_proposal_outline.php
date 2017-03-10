<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: proposal_ouline.php
//
// Created by: Zuraimi
// Created Date: 27-Dec-2014
// Modified by: Zuraimi
// Modified Date: 27-Dec-2014, 26-Dec-2105 (changes after review session 23/01/15)
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

if(isset($_POST['btnUpdate']) && ($_POST['btnUpdate'] <> "")) {
	
	$msg = array();
	if (empty($_POST['cancelApprovedRemarks'])) $msg[] = "<div class=\"error\"><span>Please enter the remark as required below.</span></div>";
	
	if(empty($msg)) 
	{
		$cancelApprovedRemarks=$_POST['cancelApprovedRemarks'];
		$currentDate = date('Y-m-d H:i:s');
		
		$sql1 = "UPDATE pg_proposal SET
					cancel_approved_remarks='$cancelApprovedRemarks', cancel_approved_date = '$currentDate', 
					cancel_approved_by = '$userid',	modify_date = '$currentDate', modify_by = '$userid'		
					WHERE id='$proposalId'";
		$dbg->query($sql1); 
		
		$msg[] = "<div class=\"success\"><span>The remark has been updated successfully.</span></div>";
	}
}


$sql = "select a.pg_thesis_id, a.id, DATE_FORMAT(a.report_date,'%d-%b-%Y') as report_date, a.thesis_title, a.thesis_type, 
		b.description as thesisTypeDescription, a.introduction, a.objective, a.description, d.student_matrix_no, 
		a.verified_remarks, a.faculty_remarks_by, DATE_FORMAT(a.faculty_remarks_date,'%d-%b-%Y') as remarks_date, a.cancel_requested_remarks, DATE_FORMAT(a.cancel_requested_date,'%d-%b-%Y') as cancel_requested_date,
		a.cancel_approved_remarks, DATE_FORMAT(a.cancel_approved_date,'%d-%b-%Y') as cancel_approved_date, a.verified_status
		FROM pg_proposal a
		LEFT JOIN ref_thesis_type b ON (b.id = a.thesis_type)
		LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id )
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
	<link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
    <link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
   	<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
	<link rel="stylesheet" href="../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
	<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
	<script src="../../../lib/js/jquery.min2.js"></script>
    <script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
	<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>
	</head>
	<body>  
	<?php
    if(!empty($msg)) 
	{
        foreach($msg as $err) 
		{
            echo $err;
        }
    }
	?>

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
		$reportDate=$db->f('report_date');
		$verifiedRemarks=$db->f('verified_remarks');
		$employeeName=$db->f('employee_name');
		$remarksDate=$db->f('remarks_date');
		$cancelRequestedDate=$db->f('cancel_requested_date');
		$cancelRequestedRemarks=$db->f('cancel_requested_remarks');
		$cancelApprovedDate=$db->f('cancel_approved_date');
		$cancelApprovedRemarks=$db->f('cancel_approved_remarks');
		$verifiedStatus=$db->f('verified_status');
		
		
		?>
		<fieldset>
		<legend><strong>Outline of Proposed <?echo $thesisTypeDescription;?> by the Student</strong></legend>
		<br/>		
		<table>
		<tr>
			<td>Thesis Date</td>
			<td>:</td>
		    <td><label name="reportDate" size="15" id="reportDate" ></label><?=$reportDate;?></td>
		</tr>
		<tr>
			<td>Thesis / Project ID</td>
			<td>:</td>
		    <td><label name="pgThesisId" size="30" id="pgThesisId" ></label><?=$pgThesisId;?></td>
		</tr>
		<tr>
			<td>Matrix No</td>
			<td>:</td>
			<td><label name="studentMatrixNo" size="12" id="studentMatrixNo" ></label><?=$studentMatrixNo;?></td>
		</tr>
		<?
		$sql3 = "SELECT name
				FROM student
				WHERE matrix_no = '$studentMatrixNo'";		
		if (substr($studentMatrixNo,0,2) != '07') { 
			$dbConnStudent= $dbc; 
		} 
		else { 
			$dbConnStudent=$dbc1; 
		}
		$result3 = $dbConnStudent->query($sql3); 
		$dbConnStudent->next_record();
		$studentName=$dbConnStudent->f('name');						
					
		?>
		<tr>
			<td>Student Name</td>
			<td>:</td>
			<td><label name="name" size="50" id="name" ></label><?=$studentName;?></td>
		</tr>  			
		<tr>
			<td>Thesis / Project Title</td>
			<td>:</td>
			<td><label name="thesisTitle" size="30" rows="3" disabled="disabled" id="thesisTitle"></label><?=$thesisTitle;?></td>
		</tr>  	
		<tr>
			<td>Thesis Type</td>
			<td>:</td>
			<td><label name="thesisTypeDescription" size="30" id="thesisTypeDescription" ></label><?=$thesisTypeDescription;?></td>			
		</tr>
		<tr>
			<td>Introduction</td>
			<td>:</td>
			<td><label name="introduction" disabled="disabled" id="introduction" class="ckeditor" /></label><?=$introduction;?></td>		
		</tr>
		<tr>
			<td>Objective</td>	
			<td>:</td>				
			<td><label name="objective" disabled="disabled" id="objective" class="ckeditor"></label><?=$objective?></td>
		</tr>
		<tr>
			<td>Brief Description</td>
			<td>:</td>
			<td><label name="description" disabled="disabled" id="description" class="ckeditor"></label><?=$description?></td>		
		</tr>
		</table>
		
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
				<th width="240"><strong>Notes</strong></th>
			  </tr>
			  <?
			  
				$sql2 = "SELECT id,lecturer_id, external_lecturer, DATE_FORMAT(meeting_sdate,'%d-%b-%Y') as theMeetingDate, 
				DATE_FORMAT(meeting_sdate,'%h:%i:%s %p') as theMeetingSTime, DATE_FORMAT(meeting_edate,'%h:%i:%s %p') as theMeetingETime, 
				remark, venue, pg_proposal_id
				FROM pg_meeting_detail
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
	  

					?>
					<tr>
						<td align="center"><label name="no" size="3" id="no" ><?=$no?>.</label></td>
						<td><label name="lecturerName" size="15" id="lecturerName" ><?=$lecturerName?></label></td>
						<td><label name="externalLecturer" size="15" id="externalLecturer"><?=$externalLecturer?></label></td>
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

		<br/>
		</fieldset>
		<br/>	
		<fieldset>
		<legend><strong>Cancellation Remarks by Student</strong></legend>		
		<table>
		<tr>
			<td>Requested Date</td>
			<td>:</td>
			<td><label name="cancelRequestedDate" size="50" id="cancelRequestedDate" class="ckeditor" cols="50" rows="3" ><?=$cancelRequestedDate;?></label></td>					
		</tr>
		<tr>
			<td>Remarks</td>
			<td>:</td>
			<td><label name="cancelRequestedRemarks" id="cancelRequestedRemarks" class="ckeditor" cols="50" rows="3"><?=$cancelRequestedRemarks;?></label></td>					
		</tr>
		</table>
		</fieldset>
		<br/>	
		<fieldset>
		<legend><strong>Approval Remarks by Faculty</strong></legend>		
		<table>
		<tr>
			<td>Approval Date</td>
			<td>:</td>
			<td><label name="cancelApprovedDate" size="15" id="cancelApprovedDate" ></label><?=$cancelApprovedDate;?></td>					
		</tr>
		<tr>
			<td>Remarks <span style="color:#FF0000">*</span></label></td>			
			<?if ($verifiedStatus=='CAN') 
			{?>
				<td>:</td>
				<td><label name="cancelApprovedRemarks" id="cancelApprovedRemarks" class="ckeditor" cols="50" rows="3"><?=$cancelApprovedRemarks;?></label></td>					
			<?}
			else 
			{
				?>
				<td></td>
				<td><textarea name="cancelApprovedRemarks" id="cancelApprovedRemarks" class="ckeditor" cols="50" rows="3"><?=$cancelApprovedRemarks;?></textarea></td>					
				<?
			}?>
		</tr>
		</table>
		</fieldset>
		<table>
		<tr>
			<td></td>
			<td></td>
			<?if ($verifiedStatus=='CAN') 
			{?>
				<td></td>
			<?}
			else 
			{
				?>
				<td><input type="submit" name="btnUpdate" id="btnUpdate" value="Update" /></td>	
				<?
			}?>
			<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='approve_request_proposal.php';" /></td>			
		</tr>	
	</table>
	<table>
			<tr>
				<td><span style="color:#FF0000">Note:</span></td>
			</tr>
			<tr>
				<td>1. Field marks with (<span style="color:#FF0000">*</span>) is compulsory.</td>
			</tr>
		</table>
	<br/>
	  </form>
	</body>
</html>




