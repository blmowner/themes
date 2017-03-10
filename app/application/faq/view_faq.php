<?php

include("../../../lib/common.php");
checkLogin();

session_start();
$userid=$_REQUEST['uid'];

function romanNumerals($num) 
{
    $n = intval($num);
    $res = '';
 
    /*** roman_numerals array  ***/
    $roman_numerals = array(
                'M'  => 1000,
                'CM' => 900,
                'D'  => 500,
                'CD' => 400,
                'C'  => 100,
                'XC' => 90,
                'L'  => 50,
                'XL' => 40,
                'X'  => 10,
                'IX' => 9,
                'V'  => 5,
                'IV' => 4,
                'I'  => 1);
 
    foreach ($roman_numerals as $roman => $number) 
    {
        /*** divide to get  matches ***/
        $matches = intval($n / $number);
 
        /*** assign the roman char * $matches ***/
        $res .= str_repeat($roman, $matches);
 
        /*** substract from the number ***/
        $n = $n % $number;
    }
 
    /*** return the res ***/
    return $res;
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
.style5 {color: #FFFFFF; font-weight: bold; }
.style6 {color: #FFFFFF}
.style8 {color: #5B74A8; font-weight: bold; font-size: 14px; }
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
<legend><strong>View Frequently Asked Question</strong></legend>

<?
	$sql_faq = "SELECT id, description
	FROM ref_faq_category
	WHERE status = 'A'
	ORDER BY sequence, description";
	
	$result_faq = $dba->query($sql_faq);
	$dba->next_record();
	$no1=0;
	do {			
		$faq_id=$dba->f("id");
		$category=$dba->f("description");				
		?>
	
		<table>
			<tr>
			  <td><span class="style8"><label><?=$category?></label></span></td>					
			</tr>
		</table>
		
		<table width="90%">
			<?		
				$sql_faq2 = "SELECT question, answer 
				FROM pg_faq 
				WHERE category_id = '$faq_id'
				AND status = 'A'";
							
				$result_sql_faq2 = $db->query($sql_faq2);					
				$db->next_record();
				$row_cnt_sql_faq2 = mysql_num_rows($result_sql_faq2);
				$no=0;
				if ($row_cnt_sql_faq2 > 0) {
					do {
						$question=$db->f("question");
						$answer=$db->f("answer");
						?>
						<tr>
							<td><strong><?=$no+1?>. <?=$question?></strong></td>
						</tr>
						<tr>
							<td><?=$answer?></td>
						</tr>					
					<? 
					$no++;
					} while($db->next_record());
				}
				else {
					?>
					<table>
						<tr>
							<td><label>No record found.</label></td>
						</tr>
					</table>
				<?}
		?></table>
		<br>
		<? 
	$no1++;
	} while($dba->next_record());?>
</fieldset>	
</form>

</body>
</html>
