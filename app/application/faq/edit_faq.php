<?php

include("../../../lib/common.php");
checkLogin();
session_start();
$userid=$_REQUEST['uid'];
$faq_id=$_REQUEST["fid"];

if(isset($_POST['btnUpdate']) && ($_POST['btnUpdate'] <> ""))
{

	$msg = array();
	$cat_id = $_REQUEST['categoryid'];
	if(empty($_POST['question'])) $msg[] = "<div class=\"error\"><span>Please provide the Question. It cannot be empty. The previous question is restored.</span></div>";
	if(empty($_POST['answer'])) $msg[] = "<div class=\"error\"><span>Please provide the Answer. It cannot be empty. The previous answer is restored.</span></div>";
	if($_POST['categoryid'] == "") $msg[] = "<div class=\"error\"><span>Please select the FAQ Category from the list below.</span></div>";
	if(empty($_POST['sequence'])) {
		$msg[] = "<div class=\"error\"><span>Please provide the order of the category for display.</span></div>";
	}
	else if(!is_numeric($sequence)) {
		$msg[] = "<div class=\"error\"><span>Please provide numeric value for the <strong>Display Order</strong>.</span></div>";
	}
	if(empty($msg)) 
	{	
		$curdatetime = date("Y-m-d H:i:s");	
		$category_id = $_POST['category'];
		$categoryid = $_POST['categoryid'];
		$sql_update = "UPDATE pg_faq 
		SET category_id='".$categoryid."', question='".$question."', answer='".$answer."', sequence = '".$sequence."',
		modify_by='".$user_id."', modify_date='".$curdatetime."'
		WHERE id='".$faq_id."'";

		$process = $db->query($sql_update);	
		$msg[] = "<div class=\"success\"><span>The FAQ has been updated successfully.</span></div>";
				
	}
}

$sql_login = "SELECT id, question, answer, category_id, sequence
FROM pg_faq 
WHERE id='$faq_id'
AND status = 'A'";
$db->query($sql_login);
$db->next_record();

$faqId = $db->f("id");
$categoryId = $db->f("category_id");
$question = $db->f("question");
$answer = $db->f("answer");
$sequence = $db->f("sequence");

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
	
    <style type="text/css">
<!--
.style4 {color: #FFFFFF; font-weight: bold; }
-->
    </style>
</head>
<body>
 <?php
    if(!empty($msg)) {
        foreach($msg as $err) {
            echo $err;
        }
    }
?>
<script>
$(document).ready(function() {

              
        CKEDITOR.instances.textarea.updateElement();
					//CKEDITOR.instances.answer.updateElement();

            
});				
</script>

<SCRIPT LANGUAGE="JavaScript">

</SCRIPT>
<script>
$(function() {
	$( "#datepickerFirst" ).datepicker({
		changeMonth: true,
		changeYear: true,
		yearRange: '-100:+0',
		dateFormat: 'dd-mm-yy'
		});
});
</script>

<form id="form1" name="form1" method="post" enctype="multipart/form-data"> 			
<fieldset>
	<legend><strong>Update Frequently Asked Question</strong></legend>
	<? 
	$sql_category= "SELECT id,description 
	FROM ref_faq_category 
	WHERE status = 'A'
	ORDER by sequence, description";
	$result_sql_category= $db->query($sql_category);?>
	<table>
		<tr>
			<td bgcolor="#5B74A8"><span class="style4">Category</span></td>
			<td bgcolor="#DBDBDB">
				<select name="categoryid" id="categoryid">
				<option value="">--Please Select--</option>
					<?
					while ($db->next_record()) {
						$catid=$db->f('id');
						$description=$db->	f('description');						
						if ($catid == $categoryId) {
							?>
							<option value="<?=$catid?>" selected="selected"><?=$description?></option><?
						}
						else {
							?>
							<option value="<?=$catid?>"><?=$description?></option><?
						}
					};
					?>
			 </select></td>		
		</tr>
		<tr>
			<td bgcolor="#5B74A8"><span class="style4">Question </span></td>
			<td bgcolor="#DBDBDB"><textarea name="question" cols="80" rows="3"><?=$question;?></textarea></td>
		</tr>
		<tr>
		<tr>
			<td bgcolor="#5B74A8"><span class="style4">Answer</span></td>
			<td bgcolor="#DBDBDB"><textarea name="answer" cols="50" class = "ckeditor" rows="8"><?=$answer;?></textarea></td>
		</tr>
		<tr>
			<td bgcolor="#5B74A8"><span class="style3">Display Order</span></td>
			<td bgcolor="#DBDBDB"><input name="sequence" id="sequence" value="<?=$sequence;?>"/>Note: The <strong>Display Order</strong> will be used to sort the display of the list of FAQ by ascending order.</td>
		</tr>
		</table>  
	   <table>
			<tr>
				
				<td><input type="submit" name="btnUpdate" id="btnUpdate" align="center"  value="Update" onClick="return respConfirm()" /></td>
				<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../faq/list_edit_faq.php';" /></td>		
			</tr>
	</table>		
	</fieldset>  
</form>
</body>
</html>
