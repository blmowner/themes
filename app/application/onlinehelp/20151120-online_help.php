<?php
include("../../../lib/common.php");
checkLogin();

$sql6_1 = "SELECT b.role_type 
FROM user_acc a
LEFT JOIN base_user_role b ON (b.role_id = a.role_id)
WHERE a.staff_id = '$user_id'";

$result6_1 = $db->query($sql6_1);		
$db->next_record();

$role_type = $db->f('role_type'); 

/*Admin
Staff
Student
Supervisor
Senate
Director/Faculty
Reviewer
Super Admin
Admin
Evaluation Committee
School Board Committ*/

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Untitled Document</title>
		<link rel="stylesheet" type="text/css" href="../../theme/css/<?php echo $css; ?>" />
		<link rel="stylesheet" type="text/css" href="../../theme/css/colorbox.css" media="screen" />
		<link rel="stylesheet" href="../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />  
		<script src="../../lib/js/jquery.min2.js"></script>
		<script src="../../lib/js/jquery.colorbox.js"></script>
		<script src="../../lib/js/jquery.mask_input-1.3.js"></script>
		<script language="JavaScript" src="../../../lib/js/windowopen.js"></script>	

	</head>
	<body>		
	<form id="form1" name="form1" method="post" enctype="multipart/form-data">	
	<fieldset>
		<legend><strong>List of Online Help for Themes</strong></legend>	
		<p>Please click the link below to open online help.</p>
		<div>    
			<ul>
				<? if($role_type == 'Student' || $role_type == 'Super Admin') {?>
					<li><a href="#" onclick="javascript:open_win('student/OnlineHelp-Student.html',1000,760,0,0,0,1,0,1,1,0,5,'winupload'); " title="Online Help">Online Help - Student  </a></li>
				<? } if ($role_type == 'Director/Faculty' || $role_type == 'Admin' || $role_type == 'Super Admin') { ?>
					<li><a href="#" title="Online Help">Online Help - Director/Faculty  </a></li>
				<? } if ($role_type == 'Senate' || $role_type == 'Super Admin') {?>
		        	<li><a href="#" title="Online Help">Online Help - Senate </a></li>
		    	<? } if ($role_type == 'Supervisor' || $role_type == 'Super Admin' || $role_type == 'Admin') { ?>
		        	<li><a href="#" title="Online Help">Online Help - Reviewer/Supervisor </a></li>
		    	<? } if ($role_type == 'Evaluation Committee' || $role_type == 'Super Admin' || $role_type == 'Admin') { ?>
		        	<li><a href="#" title="Online Help">Online Help - Evaluation Committee </a></li>
		    	<? } if ($role_type == 'School Board Committ' || $role_type == 'Super Admin') { ?>
		        	<li><a href="#" title="Online Help">Online Help - School Board Committ </a></li>
		    	<? } ?>
          </ul>
	</div>
	</fieldset>
	</form>
	</body>
</html>