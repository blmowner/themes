<?php

include("../../../lib/common.php");
checkLogin();

session_start();
$userid=$_REQUEST['uid'];
//$studentMatrixNo=$_REQUEST['uid'];
//$pgThesisId=$_REQUEST['tid'];
//$pgProposalId=$_REQUEST['pid'];
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
.style5 {color: #FFFFFF; font-weight: bold; }
.style6 {color: #FFFFFF}
.style8 {color: #FFFFFF; font-weight: bold; font-size: 14px; }
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
  

<br/>			
<fieldset>
<legend><strong>View Frequently Asked Question</strong></legend>

<?
		/*$sql_role = "SELECT *
		FROM ref_faq_category";
		
		$result_role = $dbf->query($sql_role);
		$dbf->next_record();
		$catId=$dbf->f("id");
		$desc = $dbf->f("description");*/
		
		$sql_faq = "SELECT id, description
					FROM ref_faq_category
					WHERE status = 'A'
					ORDER BY sequence, description";
		
		$result_faq = $dba->query($sql_faq);
		$dba->next_record();

		do {
			
				$faq_id=$dba->f("id");
				$category=$dba->f("description");
				
			?>
		
			<table bordercolor="#3B5998">
				<tr>
				  <td width="618" bgcolor="#3B5998"><span class="style8">
				    <label>
				    <?=$category?>
				    </label>
				  </span></td>					
				</tr>
  </table>
			
			<table >
				<?		
					$sql_faq2 = "SELECT question, answer 
					FROM pg_faq 
					WHERE category_id = '$faq_id'";
								

					$result_sql_faq2 = $db->query($sql_faq2);
					
					$db->next_record();
					do{
						
						$question=$db->f("question");
						$answer=$db->f("answer");

						?>
						<tr bgcolor="#CCCCCC">
							<td width="54" bgcolor="#5B74A8"><span class="style5">Question</span></td>
							<td width="558" bgcolor="#DBDBDB"><?=$question?></td>
			  </tr>
						<tr bgcolor="#B7B7FF">
							<td bgcolor="#5B74A8" ><span class="style5">Answer</span></td>
							<td bgcolor="#DBDBDB"><?=$answer?></td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td height="21"><span class="style6"></span></td>
							<td><span class="style6"></span></td>
						</tr>	
				
  <? } while($db->next_record());
			?></table>
			
			
			
			<? 
		} while($dba->next_record());?>

            <input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../faq/faq.php';" />
</fieldset>	
</form>

</body>
</html>
