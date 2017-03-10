<?php

include("../../../lib/common.php");
checkLogin();

session_start();
$userid=$_REQUEST['uid'];

$pgProposalId=$_REQUEST['pid'];

if(isset($_REQUEST['btnDelete']) && ($_REQUEST['btnDelete'] <> ""))
{
	$msg = array();
	 
	if (isset($_REQUEST['id'])) /* checks weather $_GET['empids'] is set */
	{
		$checkbox = $_REQUEST['id'];
		
		if (is_array($checkbox)) /* value is stored in $checkbox variable */
		{
			foreach ($checkbox as $key => $checkbox) /* for each loop is used to get id and that id is used to delete the record below */
  			{
				//echo $check = $_REQUEST['id'];
				
				$q="DELETE FROM pg_faq WHERE id = '$checkbox' ";/* Sql query to delete the records whose id is equal to $your_slected_id */
				$db->query($q); /* runs the query */
				
				
			}
		
			
		$msg[] = "<div class=\"success\"><span>Delete FAQ Success!</span></div>";
		}
	}
	else 
	{	
		//echo" you have not selected reords  .. to delete";
		$msg[] = "<div class=\"error\"><span>There is no selected faq to delete!</span></div>";
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
.style3 {color: #FFFFFF; font-weight: bold; }
.style4 {color: #FF0000}
-->
    </style>
</head>
<body>
 <?php
    if(!empty($msg)) {
        foreach($msg as $err) 
		{
            echo $err;
        }
    }
?>
<script>

	$(document).ready(function() {         
		$('.error').hide();
		var showError = <?php echo $msg; ?>;         
		if (showError) {             
			$('.error').fadeIn(500).delay(5000).fadeOut(500);
			$msg.focus();       
		} else {
			
		}
	
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
  
<?


$sql_faq = "SELECT pg_faq.question, pg_faq.answer, ref_faq_category.description, pg_faq.id FROM pg_faq
LEFT JOIN ref_faq_category ON pg_faq.category_id=ref_faq_category.id ORDER BY pg_faq.category_id";
				
	$result_sql_faq = $db->query($sql_faq);
	$db->next_record();

?>
<br/>			
<fieldset>
<legend><strong>Delete Frequently Asked Question</strong></legend>
<? $sql_category= "SELECT id,description FROM ref_faq_category ORDER by sequence ASC";?>
<select name="categoryid" id="categoryid"><option value="">--Please Select--</option>
			<?php
					
					$result_sql_category= $dba->query($sql_category);
					
					while ($dba->next_record()) 
					{
						$catid=$dba->f('id');
						$description=$dba->	f('description');
												
						if ($catid==$_POST['id']) 
						{
							?><option value="<?=$catid?>" selected="selected"><?=$description?></option><?
						}
						else 
						{
							?><option value="<?=$catid?>"><?=$description?></option><?
						}
					};
			?></select></span>
            <input type="submit" name="btnSearch" value="Search" />
	
	        <span class="style4"><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../faq/faq.php';" />Note</span>: If no entry is provided, it will search all.
<table width="915">
    <tr bgcolor="#5B74A8">
      <td width="26">&nbsp;</td>
		  <td width="152"><span class="style3">ID</span></td>
			<td width="220" bgcolor="#5B74A8"><span class="style3">Question</span></td>
	        <td width="403"><span class="style3">Answer</span></td>
          <td width="90">
		  <p class="style3">Action</p>		  </td>		
		</tr>
		<?
			if(isset($_POST['btnSearch']) && ($_POST['btnSearch'] <> ""))
			{
				$catid = $_POST['categoryid'];
				if($catid != '')
				{
					$sql_faq = "SELECT * FROM pg_faq WHERE category_id = '$catid' 
					ORDER BY id";
					
					$result_sql_faq = $dbb->query($sql_faq);
					$dbb->next_record();
					
				}
				else
				{
					$sql_faq = "SELECT * FROM pg_faq ORDER BY id";
					
					$result_sql_faq = $dbb->query($sql_faq);
					$dbb->next_record();
				}
					
					do{
						$faq_id=$dbb->f("id");
						$question=$dbb->f("question");
						$answer=$dbb->f("answer");

   		?>
			
   		
		<tr bgcolor="#DBDBDB">
		  <td><input type="checkbox" name="id[]" value= "<?=$faq_id;?>"/></td>
		  <td><?=$faq_id?></td>
			<td><?=$question?></td>
			<td><?=$answer?></td>
		  <td></a><a href="deletefaq.php?id=<?=$faq_id?>" onclick = "return confirm('Do you wish to proceed?:')">Delete</a></a></td>
		</tr>
		  
		<?
	 		}while($dbb->next_record());
		}
		?>
		
		
	</table>
	 <legend>
	   <input type="submit" name="btnDelete" value="Delete" onclick = "return confirm('Do you wish to proceed?:')" class = "" />
	   <input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../faq/faq.php';" />
  </legend>
</fieldset>
</form>
</body>
</html>
