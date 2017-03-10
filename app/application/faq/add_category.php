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
	$description = $_POST['description'];
	$sequence = $_POST['sequence'];
	$msg = array();
	
	if(empty($_POST['description'])) $msg[] = "<div class=\"error\"><span>Please provide the description of the category.</span></div>";
	if(empty($_POST['sequence'])) {
		$msg[] = "<div class=\"error\"><span>Please provide the order of the category for display.</span></div>";
	}
	else if(!is_numeric($sequence)) {
		$msg[] = "<div class=\"error\"><span>Please provide numeric value for the <strong>Display Order</strong>.</span></div>";
	}

	if(empty($msg)) 
	{	
		$curdatetime = date("Y-m-d H:i:s");	
	
		$id = "C".runnum('id','ref_faq_category');
		
		$sqlsubmit1="INSERT INTO ref_faq_category 
		(id, description, status, insert_by, insert_date, sequence)
		VALUES('$id','$description','A','$user_id','$curdatetime', '$sequence') ";

		$db_klas2->query($sqlsubmit1);

		$msg[] = "<div class=\"success\"><span>The new FAQ category has been added successfully.</span></div>";		
	}
}
?>	


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Add Category</title>
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
        foreach($msg as $err) {
            echo $err;
        }
    }
?>

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
<legend><strong>Add FAQ Category </strong></legend>	
	<table>
		<tr>
			<td bgcolor="#5B74A8"><span class="style3">Category Description</span></td>
			<td><input type="text" name="description" id="description" value="<?=$description;?>" /></td>
		</tr>
		<tr>
			<td bgcolor="#5B74A8"><span class="style3">Display Order</span></td>
			<td><input type="text" name="sequence" id="description2" value="<?=$sequence;?>" /> Note: <em>Accept Numeric value only.</em></td>
		</tr>
	</table>
	<table>
		<tr>
			<td>Note: The <strong>Display Order</strong> will be used to sort the display of the list of category by ascending order.</td>
		</tr>
	</table>
	   <table>
			<tr>
				
				<td><input type="submit" name="btnSubmit" id="btnSubmit" align="center"  value="Add" onClick="return respConfirm()" /></td>
				<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../faq/faq.php';" /></td>		
			</tr>
  </table>
	<table>
		
	</table>		
  </fieldset>
  
  <? } 
  	else 
	{
	?>
	
	
		
	<? }?>
  </form>
</body>
</html>
