<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: senate_approval_outline.php
//
// Created by: Zuraimi
// Created Date: 14-Jan-2015
// Modified by: Zuraimi
// Modified Date: 14-Jan-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

session_start();
$userid=$_SESSION['user_id'];
$thesisId=$_REQUEST['thesisId'];
$proposalId=$_REQUEST['proposalId'];
$searchRequestDate=$_GET['endorsedDate'];
$listOfThesisStatus=$_GET['lot'];

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
$sql = "select a.pg_thesis_id, a.id, DATE_FORMAT(a.report_date,'%d-%b-%Y') AS theReportDate, a.thesis_title, a.thesis_type, 
		b.description as thesisTypeDescription, a.introduction, a.objective, a.description, d.student_matrix_no, 
		f.endorsed_remarks
		FROM pg_proposal a
		LEFT JOIN ref_thesis_type b ON (b.id = a.thesis_type)
		LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id )
		LEFT JOIN pg_proposal_approval f ON (f.id = a.pg_proposal_approval_id)
		WHERE a.id = '$proposalId'
		AND a.pg_thesis_id = '$thesisId'";
			
		$result = $db->query($sql); 
		//echo $sql;
		//var_dump($db);
		$db->next_record();

if(isset($_POST['btnSave']) && ($_POST['btnSave'] <> "")) {
	
	$endorsedRemarks=$_POST['endorsedRemarks'];
	$currentDate = date('Y-m-d H:i:s');
	
	$sql1 = "UPDATE pg_proposal SET
				endorsed_by = '$userid', endorsed_date = '$currentDate', endorsed_remarks='
				$endorsedRemarks',	modify_date = '$currentDate', modify_by = '$userid'		
				WHERE id='$proposalId'";
	//echo $sql1;exit();
	$dbg->query($sql1); 
	
}
			
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
		<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
		<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>
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
		$reportDate=$db->f('theReportDate');
		$endorsedRemarks=$db->f('endorsed_remarks');
		
		?>
		<fieldset>
		<legend><strong>Outline of Proposed <?echo $thesisTypeDescription;?> by the Student</strong></legend>		
		<table>
		<td><input type="hidden" name="proposalId" size="15" id="proposalId" value="<?=$id?>"/></td>
		<tr>
			<td width="20%">Thesis / Project ID</td>
			<td>:</td>
		    <td><label name="pgThesisId" size="30" id="pgThesisId" </label><?=$pgThesisId;?></td>
		</tr>
		<tr>
			<td>Matrix No</td>
			<td>:</td>
			<td><label name="studentMatrixNo" size="12" id="studentMatrixNo" ></label><?=$studentMatrixNo;?></td>
		</tr>
		<?
		$sql2 = "select name
		FROM student
		WHERE matrix_no = '$studentMatrixNo'";
		if (substr($studentMatrixNo,0,2) != '07') { 
			$dbConnStudent= $dbc; 
		} 
		else { 
			$dbConnStudent=$dbc1; 
		}	
		$result2 = $dbConnStudent->query($sql2); 
		$dbConnStudent->next_record();
		$studentName=$dbConnStudent->f('name');
		?>
		<tr>
			<td>Student Name</td>
			<td>:</td>
			<td><label name="name" size="50" id="name" ></label><?=$studentName;?></td>
		</tr>  			
		<tr>
			<td>Thesis / Proposal Date</td>
			<td>:</td>
			<td><label name="reportDate" size="30" rows="3" disabled="disabled" id="reportDate"></label><?=$reportDate;?></td>		
		</tr>
		<tr>
			<td>Proposal Type</td>
			<td>:</td>
			<td><label name="thesisTypeDescription" size="30" id="thesisTypeDescription" ></label><?=$thesisTypeDescription;?></td>			
		</tr>
		</table>
		<div id = "tabledisplay" style="overflow:auto; height:550px;">
		
		<table  width="98%">
		<tr>
			<td><br/><label><strong>Thesis / Project Title</strong></label></td>
		</tr>
		<tr>
			<td><label name="thesisTitle" size="30" rows="3" disabled="disabled" id="thesisTitle"></label><?=$thesisTitle;?></td>
		</tr>  	
		
		<tr>
			<td><br/><label><strong>Introduction</strong></label></td>
		</tr>
		<tr>			
			<td><label name="introduction" disabled="disabled" id="introduction" class="ckeditor" /></label><?=$introduction;?></td>		
		</tr>
		<tr>
			<td><br/><label><strong>Objective</strong></label></td>	
		</tr>
		<tr>		
			<td><label name="objective" disabled="disabled" id="objective" class="ckeditor"></label><?=$objective?></td>
		</tr>
		<tr>
			<td><br/><label><strong>Brief Description</strong></label></td>
		</tr>
		<tr>			
			<td><label name="description" disabled="disabled" id="description" class="ckeditor"></label><?=$description?></td>		
		</tr>
		<tr>
			<td><br/><label><strong>Remark by Senate</strong></label></td>
		</tr>
		<tr>			
			<td><label name="endorsedRemarks" id="endorsedRemarks" class="ckeditor"><?=$endorsedRemarks; ?></label></td>
		</tr>
		</table>
		</div>
		</fieldset>
		<table>
		<tr>
			<td></td>
			<td></td>
			<?if ($listOfThesisStatus=='A'){?>
				<td><input type="button" name="btnBack" value="Back" onclick="document.location.href='../thesis/listing_thesis_status_app.php?endorsedDate=<?=$searchRequestDate?>&thesisId=<?= $pgThesisId?>'" /></td>
			<?}
			else {?>
				<td><input type="button" name="btnBack" value="Back" onclick="document.location.href='../thesis/listing_thesis_status.php?endorsedDate=<?=$searchRequestDate?>&thesisId=<?= $pgThesisId?>'" /></td>
			<?}?>
			
		</tr>	
	</table>
	  </form>
	</body>
</html>




