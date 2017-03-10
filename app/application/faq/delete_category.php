<?php

include("../../../lib/common.php");
checkLogin();

session_start();
$user_id = $_SESSION['user_id'];


if(isset($_REQUEST['btnDelete']) && ($_REQUEST['btnDelete'] <> ""))
{
	$deleteBox = $_POST['deleteBox'];
	$msg = array();
	
	if (sizeof($_POST['deleteBox'])== 0) {
		$msg[] = "<div class=\"error\"><span>Please select the required FAQ Category before proceed with the deletion.</span></div>";	
	}
	
	if(empty($msg))  
	{
		while (list ($key,$val) = @each ($deleteBox)) {
			$sql = "SELECT a.id, a.description
			FROM ref_faq_category a
			LEFT JOIN pg_faq b ON (b.category_id = a.id)
			WHERE a.id = '$val'
			AND a.status = 'A'
			AND b.status = 'Active'";
			
			$result_sql = $dba->query($sql);
			$dba->next_record();
			
			$description = $dba->f('description');
			$row_cnt_sql = mysql_num_rows($result_sql);
			
			if ($row_cnt_sql == 0 ) {
				$sql_delete = "DELETE 
				FROM ref_faq_category 
				WHERE id= '$val'";
				$dba->query($sql_delete);
				$msg[] = "<div class=\"success\"><span>The selected category has been deleted successfully.</span></div>";
			}
			else {
				$msg[] = "<div class=\"error\"><span>The selected category <strong>$description</strong> cannot be deleted. Please delete the FAQ content first!</span></div>";
			}
		}
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
	
$sql_category = "SELECT id, description, sequence
FROM ref_faq_category
WHERE status = 'A'
ORDER BY sequence, description";

$result_sql_faq = $db->query($sql_category);
$db->next_record();
	
?>		
<fieldset>
<legend><strong>Delete FAQ Category</strong></legend>	
	<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="35%" class="thetable">	
		<tr>
			<th width="5%"><label>Tick</label></th>
			<th width="5%"><label>No</label></th>
			<th width="10%" align="left"><label>Category</label></th>
			<th width="5%"><label>Display Order</label></th>
		</tr>
		<?
	 		$no=0;
			do{
				if($no % 2) $color ="first-row"; else $color = "second-row";
				$id=$db->f("id");
				$category=$db->f("description");
				$sequence=$db->f("sequence");
				
				?>
				<tr class="<?=$color?>">
					<td align="center"><input type="checkbox" name="deleteBox[]" value="<?=$id?>"/></td>
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
			<td><input type="submit" name="btnDelete" value="Delete" onclick = "return confirm('Do you wish to proceed?')" class = "" /></td>
			<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../faq/faq.php';" /></td>
		</tr>
	</table>
	   
	
</fieldset>

  </form>
</body>
</html>
