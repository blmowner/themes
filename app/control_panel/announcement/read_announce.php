<?php
/**
 * @author MJMZ
 * This file will handle all the password recovery matters. User need to enter their username andkey-in a valid captcha image.
 * Then, the system automatically send an email address contained random password generated by the system itself.
 */ 
include("../../../lib/common.php");
	$id = $_GET['id'];

/*$_user_location	= 'public';
define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');*/


	$sqlannounce = "SELECT id, title, announcement, insert_by, DATE_FORMAT(insert_date,'%d-%b-%Y, %l:%i %p') AS insert_date, 
	DATE_FORMAT(modify_date,'%d-%b-%Y, %l:%i %p') AS date FROM pg_announcement WHERE id = '$id'";
	$resultA = $dbf->query($sqlannounce);
	$dbf->next_record();
	$idA =$dbf->f('id');
	$titleA =$dbf->f('title');
	$announcement =$dbf->f('announcement');
	$insertBy = $dbf->f('insert_by');
	$insertDate = $dbf->f('insert_date');
	$modifyDate = $dbf->f('date');
	
	$sqlname = "SELECT name FROM new_employee WHERE empid = '$insertBy'";
	if (substr($insertDateArray[$i],0,2) != '07') { 
		$dbConnStudent= $dbc; 
	} 
	else { 
		$dbConnStudent=$dbc1; 
	}
	$resultName = $dbConnStudent->query($sqlname);
	$dbConnStudent->next_record();
	
	$empName =$dbConnStudent->f('name');
	
	$sqledit = "SELECT COUNT(*) as number FROM pg_announcement WHERE id = '$id' AND insert_by = '$user_id'";
	$resultA = $db->query($sqledit);
	$db->next_record();
	$number =$db->f('number');
	
	$sqladmin = "SELECT COUNT(*) as admin
	FROM user_acc WHERE role_id = '20150410001' 
	AND staff_id = '$user_id'";
	$resultAdmin = $dba->query($sqladmin);
	$dba->next_record();
	$admin =$dba->f('admin');
	
	

?>
<style>
h3 {
font-family:Verdana, Arial, Helvetica, sans-serif;
font-size:16px;
background: rgba(105, 162, 255, 0.7);
width: 926px;
}
<!--
.style2 {color: #000000}
-->


.close-btn { 
    border: 2px solid #c2c2c2;
    position: relative;
    padding: 1px 5px;
    bottom: -5px;
    background-color: #605F61;
    left: 880px; 
    border-radius: 20px;
}

.close-btn a {
    font-size: 15px;
    font-weight: bold;
    color: white;
    text-decoration: none;
}

</style>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang; ?>" lang="<?php echo $lang; ?>">

<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
	<meta name="author" content="MJMZ" />

	<title><?php echo load_lang('frame_name'); ?></title>
    <!--<link rel="stylesheet" type="text/css" href="../../theme/css/style.css" media="screen" />-->
	<link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
	<link id="bs-css" href="../../../theme/css/button.css" rel="stylesheet" />
    <!--[if IE 6]><link rel="stylesheet" href="css/ie6.css" type="text/css" media="all" /><![endif]-->
    <script src="js/jquery-1.3.2.min.js" type="text/javascript"></script>
    <script src="js/jquery-fns.js" type="text/javascript"></script>
    <script type="text/javascript" src="lib/js/datePicker/jquery-1.5.1.js"></script>
    <script type="text/javascript" src="lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
    

	<!-- START : put this code below the counting bar -->
	<script src="../lib/js/idle/jquery.min.js" type="text/javascript"></script>
	<script src="../lib/js/idle/jquery.idletimer.js" type="text/javascript"></script>
	<script src="../lib/js/idle/jquery.idletimeout.js" type="text/javascript"></script>
	
	<!--END : put this code below the counting bar -->

    


</head>
<body>

 <?php
 if(!empty($errormsg)) 
 {
	 foreach($errormsg as $v) 
		echo "  $v\n";
 } 
 ?>
 <script type="text/javascript">
function gohtml(pid) 
{
		//document.location.href = "../thesis/new_proposal_discussion.php?pid=" + pid;
		document.location.href = "edit_announ.php?id=" + pid;
		return true;
}

</script>

<form method="post" class="formStyle">
	<!--<span class="close-btn"><a href="#" onClick="javascript: parent.$.fn.colorbox.close();">X</a></span>-->

	<? if(!empty($modifyDate)) { ?>
	<h4><?=$titleA?></h4><span class="h5"><?=$empName?> (<?=$insertBy?>)</span> <span class="datespan"><?=$modifyDate?></span>
	<? } else { ?> <h4><?=$titleA?></h4><span class="h5"><?=$empName?> (<?=$insertBy?>)</span> <span class="datespan"><?=$insertDate?></span> <? } ?>
	<?
	if ($number == 1)
	{
		//echo "<a class=\"edit btn btn-primary btn-xs\" id=\"edit\" name=\"edit\" style = \" float: right; width:80px; margin-right: 25px \" href=\"edit_announ.php?id=".$idA."\">Edit</a>";
		?><!--<a class="edit" id="edit" name="edit" style = " float: right; width:80px; margin-right: 25px " href="edit_announ.php?id=<?=$idA?>">Edit</a>-->
		<input class="edit" id="edit" name="edit" style = " float: right; width:80px; margin-right: 25px" onClick="gohtml(<?=$idA?>)" type="button" value="Edit"><?
		//btn btn-primary btn-xs
	}
	else if ($admin == 1)
	{
		//echo "<button class=\"edit\" id=\"edit\" name=\"edit\" style = \"float: right; width:80px; margin-right: 25px \" href=\"edit_announ.php?id=".$idA."\">Edit</button>";
		?><input class="edit" id="edit" name="edit" style = " float: right; width:80px; margin-right: 25px" onClick="gohtml(<?=$idA?>)" type="button" value="Edit"><? //btn btn-primary btn-xs
	}
	
	?>	
		<div class="container2">
		<?=$announcement?>
		</div>
		<input type="submit" style = " float: left; width:80px; margin-left: 25px" id="btnsave" name="btnsave" onClick="javascript: parent.$.fn.colorbox.close();" value = "Close" />
		
		
	
</form>
</body>

</html>
