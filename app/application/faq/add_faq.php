<?php

include("../../../lib/common.php");
checkLogin();

session_start();
$userid=$_REQUEST['uid'];
//$studentMatrixNo=$_REQUEST['uid'];
//$pgThesisId=$_REQUEST['tid'];
$pgProposalId=$_REQUEST['pid'];


function runnum($column_name, $tblname) 
{ 
    global $db_klas2;
    
    $run_start = "001";
    
    $sql_slct_max = "SELECT MAX(SUBSTR($column_name,2,11)) AS run_id FROM $tblname";
    $sql_slct = $db_klas2;
    $sql_slct->query($sql_slct_max);
    $sql_slct->next_record();

    if($sql_slct->num_rows($sql_slct_max)== 0 || $sql_slct->f("run_id")==NULL) 
	{
        $run_id = date("Ymd").$run_start;
    } 
	else 
	{
        $todate = date("Ymd");
        
        if($todate > substr($sql_slct->f("run_id"),0,8)) 
		{
            $run_id = $todate.$run_start;
        } 
		else 
		{
            $run_id = $sql_slct->f("run_id") + 1; 
        }
    }
    return $run_id;
}

if(isset($_POST['btnSubmit']) && ($_POST['btnSubmit'] <> ""))
{

	$msg = array();
	$catid = $_POST['categoryid'];
	if($_POST['categoryid'] == "") $msg[] = "<div class=\"error\"><span>Please select the required Category from the given list below.</span></div>";
	if(empty($_POST['question'])) {
		$msg[] = "<div class=\"error\"><span>Please enter the Question for FAQ.</span></div>";
	}
	else if(empty($_POST['answer'])) { 
		$msg[] = "<div class=\"error\"><span>Please enter the Answer for the entered Question.</span></div>";
	}
	if(empty($_POST['sequence'])) {
		$msg[] = "<div class=\"error\"><span>Please provide the order of the category for display.</span></div>";
	}
	else if(!is_numeric($sequence)) {
		$msg[] = "<div class=\"error\"><span>Please provide numeric value for the <strong>Display Order</strong>.</span></div>";
	}
	if(empty($msg)) 
	{	
			 
		$curdatetime = date("Y-m-d H:i:s");	
		if ($row_cnt==0) //no record
		{		
			$faqid = "F".runnum('id','pg_faq');
			$desc = $_POST['description'];
		
			$sqlsubmit1="INSERT INTO pg_faq(id,category_id, question, answer, sequence, 
			status, insert_by, insert_date, modify_by, modify_date)
			VALUES('$faqid','$catid','$question', '$answer', 'A', '$user_id', '$sequence',
			'$curdatetime', '$user_id', '$curdatetime') ";
				
			$db_klas2->query($sqlsubmit1);
		}
		$msg[] = "<div class=\"success\"><span>The new FAQ content has been added successfully.</span></div>";
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
    if(!empty($msg)) 
	{
        foreach($msg as $err) 
		{
            echo $err;
        }
    }
?>
<script>
$(document).ready(function() 
{
	$('#add-job').validate({
		ignore: [],         
		rules: {
					introduction: 
					{
						required: function() 
						{
							CKEDITOR.instances.question.updateElement();
							CKEDITOR.instances.answer.updateElement();
							$('.error').fadeIn(500).delay(5000).fadeOut(500);
							$msg.focus();       
						}
					 }
				},
				messages: 
				{
				},
					/* use below section if required to place the error*/
				errorPlacement: function(error, element) 
			   {
					if (element.attr("name") == "introduction") 
					{
						error.insertBefore("textarea#introduction");
					} else 
					{
						error.insertBefore(element);
					}
				}
	});
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
<legend><strong>New Frequently Asked Question</strong></legend>
<table width="761">
	<tr> <? $sql_category= "SELECT id,description FROM ref_faq_category ORDER by sequence ASC";?>
	
		<td width="83" bgcolor="#5B74A8"><span class="style3">Category</span></td>
		<td width="666" bgcolor="#DBDBDB">
		<select name="categoryid" id="categoryid"><option value="">--Please Select--</option>
	<?php
					$result_sql_category= $db->query($sql_category);
					
					while ($db->next_record()) 
					{
						$catid=$db->f('id');
						$description=$db->	f('description');
												
						if ($catid==$_POST['id']) 
						{
							?><option value="<?=$catid?>" selected="selected"><?=$description?></option><?
						}
						else 
						{
							?><option value="<?=$catid?>"><?=$description?></option><?
						}
					};
			?></select></span>	</tr>
	<tr>
		<td bgcolor="#5B74A8"><span class="style3">Question </span></td>
		<td bgcolor="#DBDBDB"><textarea name="question" cols="80" rows="3"><?=$question;?></textarea></td>
	</tr>
	<tr>
		<td bgcolor="#5B74A8"><span class="style3">Answer</span></td>
		<td bgcolor="#DBDBDB"><textarea name="answer" cols="50" class = "ckeditor" rows="8"><?=$answer;?></textarea></td>
	</tr>
	<tr>
		<td bgcolor="#5B74A8"><span class="style3">Display Order</span></td>
		<td bgcolor="#DBDBDB"><input name="sequence" id="sequence" value="<?=$sequence;?>"/>Note: The <strong>Display Order</strong> will be used to sort the display of the list of FAQ by ascending order.</td>
	</tr>
</table>

	<tr>
		<td><input type="submit" name="btnSubmit" id="btnSubmit" align="center"  value="Add" onClick="return respConfirm()" /></td>
		<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../faq/faq.php';" /></td>		
	</tr>
</table>
  </fieldset>
  </form>
</body>
</html>
