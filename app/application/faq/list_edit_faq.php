<?php

include("../../../lib/common.php");
checkLogin();

session_start();
$userid=$_REQUEST['uid'];


if(isset($_POST['btnSearch']) && ($_POST['btnSearch'] <> ""))
{
	$catid = $_POST['categoryid'];
	$row_cnt_sql_faq = 0;
	
	if($catid == '')
	{
		$sql_faq_category = "SELECT id, description
		FROM ref_faq_category
		WHERE status = 'A'
		ORDER BY sequence, description";
		
		$result_sql_faq_category = $db->query($sql_faq_category);
		$db->next_record();
		$row_cnt_sql_faq_category = mysql_num_rows($result_sql_faq_category);
		
		if ($row_cnt_sql_faq_category == 0) {
			$msg[] = "<div class=\"error\"><span>There is no <strong>FAQ Category</strong> has been created as such no record will be shown. Please create it first.</span></div>";
		}
		else {
			$categoryIdArray = Array();
			$categoryDescArray = Array();
			$categoryCountArray = Array();
			$no2 = 0;
			
			do {
				$categoryIdArray[$no2] = $db->f('id');
				$categoryDescArray[$no2] = $db->f('description');
				$no2++;
			} while ($db->next_record());
			
			for ($i=0;$i<count($categoryIdArray);$i++) {
				$sql_faq = "SELECT a.id, a.question, a.answer, a.sequence
				FROM pg_faq a
				LEFT JOIN ref_faq_category b ON (b.id = a.category_id)
				WHERE a.category_id = '$categoryIdArray[$i]' 
				AND a.status = 'A'
				AND b.status = 'A'
				ORDER BY b.sequence, b.description, a.sequence";
				
				$result_sql_faq = $db->query($sql_faq);
				$db->next_record();
				$row_cnt_sql_faq = mysql_num_rows($result_sql_faq);
				
				if ($row_cnt_sql_faq == 0) {
					$categoryCountArray[$i] = 0;
				}
				else {
					$categoryCountArray[$i] = $row_cnt_sql_faq;
					$faqIdArray[][] = Array();
					$faqSequenceArray[][] = Array();
					$faqQuestionArray[][] = Array();
					$faqAnswerArray[][] = Array();
					$no3 = 0;
					do {
						$faqIdArray[$i][$no3] = $db->f('id');
						$faqSequenceArray[$i][$no3] = $db->f('sequence');
						$faqQuestionArray[$i][$no3] = $db->f('question');
						$faqAnswerArray[$i][$no3] = $db->f('answer');
						$no3++;
					} while ($db->next_record());
				}
			}
		}
	}
	else
	{
		$sql_faq_category = "SELECT id, description
		FROM ref_faq_category
		WHERE id = '$catid'
		AND status = 'A'
		ORDER BY sequence, description";
		
		$result_sql_faq_category = $db->query($sql_faq_category);
		$db->next_record();
		
		$categoryIdArray = Array();
		$categoryDescArray = Array();
		$categoryCountArray = Array();
		
		$categoryIdArray[] = $db->f('id');
		$categoryDescArray[] = $db->f('description');
		
		$sql_faq = "SELECT a.id, a.question, a.answer, a.sequence
		FROM pg_faq a
		LEFT JOIN ref_faq_category b ON (b.id = a.category_id)
		WHERE a.category_id = '$catid' 
		AND a.status = 'A'
		AND b.status = 'A'
		ORDER BY b.sequence, b.description, a.sequence";
		
		$result_sql_faq = $db->query($sql_faq);
		$db->next_record();
		$row_cnt_sql_faq = mysql_num_rows($result_sql_faq);
		
		
		if ($row_cnt_sql_faq > 0) {
			$categoryCountArray[] = $row_cnt_sql_faq;
			$faqIdArray[][] = Array();
			$faqSequenceArray[][] = Array();
			$faqQuestionArray[][] = Array();
			$faqAnswerArray[][] = Array();
			$no1 = 0;
			do{
				$faqIdArray[0][$no1] = $db->f("id");
				$faqSequenceArray[0][$no1] = $db->f("sequence");
				$faqQuestionArray[0][$no1] = $db->f("question");
				$faqAnswerArray[0][$no1] = $db->f("answer");
				$no1++;
			}while($db->next_record());
		}
		else {
			$categoryCountArray[] = $row_cnt_sql_faq;
		}
	}
}
else {
	$categoryCountArray[] = 0;
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
		.style3 {color: #FFFFFF; font-weight: bold; }
		.style4 {color: #FF0000}
		.style8 {color: #5B74A8; font-weight: bold; font-size: 14px;}
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
	<table width="40%">
		<tr>
			<td width="10%" bgcolor="#5B74A8"><label><span class="style3">FAQ Category</span></label></td>
			<?$sql_category= "SELECT id,description FROM ref_faq_category ORDER by sequence ASC";?>
			<td width="30%"><select name="categoryid" id="categoryid"><option value="">--Please Select--</option>
			<?			
				$result_sql_category= $db->query($sql_category);		
				while ($db->next_record()) 
				{
					$catid=$db->f('id');
					$description=$db->f('description');
											
					if ($catid==$_POST['id']) 
					{
						?><option value="<?=$catid?>" selected="selected"><?=$description?></option><?
					}
					else 
					{
						?><option value="<?=$catid?>"><?=$description?></option><?
					}
				};
			?></select></span></td>
		</tr>
	</table>
	<table>
		<tr>
		<td><input type="submit" name="btnSearch" value="Search" />
		<input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../faq/faq.php';" /> Note: If no entry is provided, it will search all.</td>
		</tr>
	</table>
	<br>
	<? if(count($categoryCountArray) > 0) {?>
		<div class = "viewA" style="overflow:auto;width: 980px; height: 250px;">
		<? } else { ?>
		<div class = "viewA" style="overflow:auto;width: 980px; height: 150px;">
		<? } ?>
	<?if (count($categoryCountArray) > 0) {
		for ($i=0;$i<count($categoryCountArray);$i++) {
			?>
			<table>
				<tr>
					<td><span class="style8"><label><?=$categoryDescArray[$i]?></label></span></td>
				</tr>
			</table>
			<table>
				<tr>
					<td><label>Searching Results:- <?=$categoryCountArray[$i]?> record(s) found.</label></td>
				</tr>
			</table>

			<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="100%" class="thetable">
				<tr>
					<th width="5%"><label>No</label></th>
					<th width="5%"><label>Display Order</label></th>
					<th width="40%"><label>Question</label></th>
					<th width="45%"><label>Answer</label></th>
					<th width="5%"><label>Action</label></th>
				</tr>
			
			<?if ($categoryCountArray[$i] > 0) {			
				?>
				<?for ($j=0;$j<$categoryCountArray[$i];$j++) {
					if($j % 2) $color ="first-row"; else $color = "second-row";
					?>
					<tr class="<?=$color?>">
						<td align="center"><label><?=$j+1?>.</label></td>
						<td align="center"><label><?=$faqSequenceArray[$i][$j]?></label></td>
						<td><label><?=$faqQuestionArray[$i][$j]?></label></td>
						<td><label><?=$faqAnswerArray[$i][$j]?></label></td>
						<td><label><a href="edit_faq.php?fid=<?=$faqIdArray[$i][$j]?>">Update</a></label></td>
					</tr>
				<?}?>
				</table><br>
			<?}
			else {
				?>
				<table>
					<tr>
						<td><label>No record(s) found.</label></td>
					</tr>
				</table><br>
				<?
			}
		}
	}
	else {
		?>
		<table>
			<tr>
				<td><label>Searching Results:- No record(s) found.</label></td>
			</tr>
		</table>
	<?}?>
	</div>	
	<br>
	<table>
		<tr>
			<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../faq/faq.php';" /></td>
		</tr>
	</table>	
</fieldset>
</form>
</body>
</html>
