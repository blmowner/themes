<?php

include("../../../lib/common.php");
checkLogin();

session_start();
$user_id = $_SESSION['user_id'];

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

$sql_category = "SELECT * FROM ref_faq_category ORDER BY sequence, description";
		
$result_sql_faq = $db->query($sql_category);
$db->next_record();

	

?>

<br/>			
<fieldset>
<legend><strong>List of FAQ Category </strong></legend>	
	<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="30%" class="thetable">
		<tr>
			<th width="5%"><label>No</label></th>
			<th width="20%" align="left"><label>Category</label></th>
		    <th width="5%" align="center"><label>Display Order</label></th>
		</tr>
		<?
			$no=0;
	 		do {
				if($no % 2) $color ="first-row"; else $color = "second-row";
				$id=$db->f("id");
				$category=$db->f("description");
				$sequence=$db->f("sequence");
					
			?>
			<tr class="<?=$color?>">
				<td align="center"><label><?=$no+1?>.</label></td>
				<td align="left"><label><?=$category?></label></td>
				<td align="center"><label><?=$sequence?></label></td>
			</tr>
			<?
			$no++;	
	 		}while($db->next_record());
	
		?>
	</table>
	<table>
		<tr>
			<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../faq/faq.php';" /></td>
		</tr>
	</table>	
  </fieldset>
  
	

		
</form>
</body>
</html>
