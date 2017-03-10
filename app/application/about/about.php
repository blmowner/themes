<?php
include("../../../lib/common.php");
checkLogin();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>About</title>
		<link rel="stylesheet" type="text/css" href="../../theme/css/<?php echo $css; ?>" />
		<link rel="stylesheet" type="text/css" href="../../theme/css/colorbox.css" media="screen" />
		<link rel="stylesheet" href="../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />  
		<script src="../../lib/js/jquery.min2.js"></script>
		<script src="../../lib/js/jquery.colorbox.js"></script>
		<script src="../../lib/js/jquery.mask_input-1.3.js"></script>
	</head>
	<body>		
	<form id="form1" name="form1" method="post" enctype="multipart/form-data">	
	<fieldset>
		<?$currentYear = date('Y');?>
		<h3><strong>About Thesis Management and Evaluation System (THEMES)</strong></h3>
		<?$sql = "SELECT const_value as version
		FROM base_constant
		WHERE const_category = 'VERSION'
		AND const_term = 'VERSION'";
		
		$dba->query($sql);
		$dba->next_record();		
		$version = $dba->f('version');
		
		$sql = "SELECT const_value as release_date
		FROM base_constant
		WHERE const_category = 'VERSION'
		AND const_term = 'RELEASED_DATE'";
		
		$dba->query($sql);
		$dba->next_record();		
		$releaseDate = $dba->f('release_date');
		
		$sql = "SELECT const_value as admin_email
		FROM base_constant
		WHERE const_category = 'EMAIL'
		AND const_term = 'EMAIL_ADMIN'";
		
		$dba->query($sql);
		$dba->next_record();		
		$adminEmail = $dba->f('admin_email');?>
		<table>
			<tr >
				<td><label><img src="../../../theme/images/title.png" width="200" height="100" style="border:0px;" title="Delete <?=$row["fu_document_filename"];?>"></label><td>
			</tr>
		</table>
		<table  height="150">
			<tr>
				<td><label>Version <strong><?=$version?></strong> Released on <?=$releaseDate?></label></td>
			</tr>
			<tr>
				<td><label>Developed by ITIC Department.</label></td>
			</tr>
			<tr>
				<td><label>Copyright &copy; 2015 - <?=$currentYear?> MSU - Management &amp; Science University. All rights reserved.</label></td>
			</tr>
				<td><label>To send comments and report bugs use this support email: <strong><?=$adminEmail?></strong></label></td>
			</tr>
		</table>
	</fieldset>
	</form>
	</body>
</html>