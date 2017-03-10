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
		AND a.pg_thesis_id = '$thesisId'
		AND a.confirm_status = 'INP'";
			
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
		<table>
			<tr>
				<td><strong>  Outline of Proposed <?echo $thesisTypeDescription;?> by the Student</strong></td>
			</tr>
		<br/>		
		<table>
		<tr>
			<td>Thesis ID: </td>
		    <td><input type="text" name="pgThesisId" size="30" id="pgThesisId" value="<?=$pgThesisId;?>" disabled="disabled"></td>
			<td>Matrix No: </td>
			<td><input type="text" name="studentMatrixNo" size="12" id="studentMatrixNo" value="<?=$studentMatrixNo;?>" disabled="disabled"></td>
			<td>Student Name: </td>
			<td><input type="text" name="name" size="50" id="name" value="<?=$name;?>" disabled="disabled"></td>
		</tr>  	
		</table>
		<br/>
		<table>
		<tr>
			<td>Thesis / Project Title</td>
			<td><textarea name="thesisTitle" size="30" rows="3" disabled="disabled" id="thesisTitle" /><?=$thesisTitle;?></textarea></td>
		</tr>  	
		<tr>
			<td>Thesis Type</td>
			<td><input type="text" name="thesisTypeDescription" size="30" id="thesisTypeDescription" value="<?=$thesisTypeDescription;?>" disabled="disabled"></td>			
		</tr>
		<tr>
			<td>Introduction</td>
			<td><textarea name="introduction" disabled="disabled" id="introduction" class="ckeditor" /><?=$introduction;?></textarea></td>		
		</tr>
		<tr>
			<td>Objective</td>			
			<td><textarea name="objective" disabled="disabled" id="objective" class="ckeditor" /><?=$objective?></textarea></td>
		</tr>
		<tr>
			<td>Brief Description of <?=$thesisTypeDescription;?></td>
			<td><textarea name="description" disabled="disabled" id="description" class="ckeditor" /><?=$description?></textarea></td>		
		</tr>
		<tr>
			<td></td>
			<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='approve_proposal.php';" /></td>			
		</tr>	
	</table>
	<br/>
	  </form>
	</body>
</html>




