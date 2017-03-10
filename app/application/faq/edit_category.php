<?php

include("../../../lib/common.php");
checkLogin();
session_start();

$user_id=$_SESSION['user_id'];
$faq_id=$_REQUEST['id'];
   
$sql_login = "SELECT * from ref_faq_category where id='$id'";
$db->query($sql_login);
$db->next_record();

if(isset($_POST['btnUpdate']) && ($_POST['btnUpdate'] <> ""))
{
	$description = $_POST['description'];
	$sequence = $_POST['sequence'];
	$msg = array();
	
	if(empty($_POST['description'])) $msg[] = "<div class=\"error\"><span>Please provide the description of the category. The original description is restored.</span></div>";
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
		$sequence = $_REQUEST['sequence'];
		echo $sql_update = "UPDATE ref_faq_category SET id='".$id."', description='".$description."',
		modify_by='".$user_id."', modify_date='".$curdatetime."', sequence='".$sequence."'
		WHERE id='".$id."'";
		
		$process = $db->query($sql_update);

		$msg[] = "<div class=\"success\"><span>The FAQ category has been updated successfully.</span></div>";
	}
}
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
.style1 {
	color: #FFFFFF;
	font-weight: bold;
}
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
  
<?

	
if (strcmp($verified_status,'INP')!=0)
{
?>
<br/>			
<fieldset>
<legend><strong>Update FAQ  Category </strong></legend>
	<table width="30%">
	<?	
		do{
			$id=$db->f("id");
			$description=$db->f("description");
			$sequence=$db->f("sequence");			
			?>	
			<tr>
				<td bgcolor="#5B74A8" width="15%"><span class="style1">Category</span></td>
				<td width="15%"><input type="text" name="description" id="description" value="<?=$description;?>" /></td>
			</tr>
			<tr>
				<td bgcolor="#5B74A8" width="15%"><span class="style1">Display Order</span></td>
				<td width="15%"><input type="text" name="sequence" id="sequence" value="<?=$sequence;?>" /></td>
			</tr>
			<?
		}while($db->next_record());	
	?>
	</table>

	  
	   <table>
			<tr>
				
				<td><input type="submit" name="btnUpdate" id="btnUpdate" align="center"  value="Update" onClick="" /></td>
				<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../faq/list_edit_category.php';" /></td>		
			</tr>
  </table>
	<table>
		
	</table>		
  </fieldset>
  
  <? } 
  	else 
	{
	 }?>
  </form>
</body>
</html>
