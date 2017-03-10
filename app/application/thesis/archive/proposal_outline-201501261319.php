<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: proposal_ouline.php
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
$sql = "select a.pg_thesis_id, a.id, DATE_FORMAT(a.report_date,'%d-%b-%Y'), a.thesis_title, a.thesis_type, 
		b.description as thesisTypeDescription, a.introduction, a.objective, a.description, d.student_matrix_no, e.name 
		FROM pg_proposal a
		LEFT JOIN ref_thesis_type b ON (b.id = a.thesis_type)
		LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id )
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
		
		?>
		<fieldset>
		<legend><strong>Outline of Proposed <?echo $thesisTypeDescription;?> by the Student</strong></legend>
		<br/>		
		<table>
		<tr>
			<td>Thesis ID</td>
			<td>:</td>
		    <td><label name="pgThesisId" size="30" id="pgThesisId" </label><?=$pgThesisId;?></td>
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
		<table>
		<tr>
			<td></td>
			<td></td>
			<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='confirm_proposal.php';" /></td>			
		</tr>	
	</table>
	<br/>
	  </form>
	</body>
</html>




