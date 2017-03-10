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

if(isset($_POST['btnUpdate']) && ($_POST['btnUpdate'] <> "")) {
	
	$cancelApprovedRemarks=$_POST['cancelApprovedRemarks'];
	$currentDate = date('Y-m-d H:i:s');
	
	$sql1 = "UPDATE pg_proposal SET
				cancel_approved_remarks='$cancelApprovedRemarks', cancel_approved_date = '$currentDate', 
				cancel_approved_by = '$userid',	modify_date = '$currentDate', modify_by = '$userid'		
				WHERE id='$proposalId'";
	//echo $sql1;exit();
	$dbg->query($sql1); 
}

///////////////////////////////////////////////////////////////
$sql = "select a.pg_thesis_id, a.id, DATE_FORMAT(a.report_date,'%d-%b-%Y') as report_date, a.thesis_title, a.thesis_type, 
		b.description as thesisTypeDescription, a.introduction, a.objective, a.description, d.student_matrix_no, e.name,
		a.verified_remarks, a.faculty_remarks_by, f.name as employee_name, DATE_FORMAT(a.faculty_remarks_date,'%d-%b-%Y') as remarks_date, a.cancel_requested_remarks, DATE_FORMAT(a.cancel_requested_date,'%d-%b-%Y') as cancel_requested_date,
		a.cancel_approved_remarks, DATE_FORMAT(a.cancel_approved_date,'%d-%b-%Y') as cancel_approved_date, a.verified_status
		FROM pg_proposal a
		LEFT JOIN ref_thesis_type b ON (b.id = a.thesis_type)
		LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id )
		LEFT JOIN student e ON (e.matrix_no = d.student_matrix_no)
		LEFT JOIN new_employee f ON (f.empid = a.faculty_remarks_by)
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
		$reportDate=$db->f('report_date');
		$verifiedRemarks=$db->f('verified_remarks');
		$remarksBy=$db->f('remarks_by');
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
			<td>Thesis ID</td>
			<td>:</td>
		    <td><label name="pgThesisId" size="30" id="pgThesisId" ></label><?=$pgThesisId;?></td>
		</tr>
		<tr>
			<td>Matrix No</td>
			<td>:</td>
			<td><label name="studentMatrixNo" size="12" id="studentMatrixNo" ></label><?=$studentMatrixNo;?></td>
		</tr>
		<tr>
			<td>Student Name</td>
			<td>:</td>
			<td><label name="name" size="50" id="name" ></label><?=$name;?></td>
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
			<td>Remarks</td>			
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
	<br/>
	  </form>
	</body>
</html>




