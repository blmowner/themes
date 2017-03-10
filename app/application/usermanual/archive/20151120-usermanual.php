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
	</head>
	<body>		
	<form id="form1" name="form1" method="post" enctype="multipart/form-data">	
	<fieldset>
		<legend><strong>List of User Manual for Themes</strong></legend>	
		<p>Please click the link below to retrieve and download the user manual.</p>
		<div>    
			<ul>          		
          		<? if($role_type == 'Student' || $role_type == 'Super Admin') {?>
					<li><a href="THEMES_UM001_STU_v1.0.pdf" title="User Manual For Student (PDF)">User Manual For Student (PDF)  <img src="../images/download.png" width="20" height="19" style="border:0px;" title="User Manual For Student (PDF)"></a></li>
				<? } if ($role_type == 'Director/Faculty' || $role_type == 'Admin' || $role_type == 'Super Admin') { ?>
					<li><a href="THEMES_UM002_FAC_v1.0.pdf" title="User Manual For Faculty (PDF)">User Manual For Faculty (PDF)  <img src="../images/download.png" width="20" height="19" style="border:0px;" title="User Manual For Faculty (PDF)"></a></li>
				<? } if ($role_type == 'Senate' || $role_type == 'Super Admin') {?>
		        	<li><a href="THEMES_UM004_SEN_v1.0.pdf" title="User Manual For Senate (PDF)">User Manual For Senate (PDF) <img src="../images/download.png" width="20" height="19" style="border:0px;" title="User Manual For Student (PDF)" /></a></li>
		    	<? } if ($role_type == 'Supervisor' || $role_type == 'Super Admin' || $role_type == 'Admin') { ?>
		        	<li><a href="THEMES_UM003_SVR_v1.0.pdf" title="User Manual For Reviewer/Supervisor (PDF)">User Manual For Reviewer/Supervisor (PDF) <img src="../images/download.png" width="20" height="19" style="border:0px;" title="User Manual For Reviewer/Supervisor (PDF)" /></a></li>
		    	<? } if ($role_type == 'Evaluation Committee' || $role_type == 'Super Admin' || $role_type == 'Admin') { ?>
		        	<li><a href="THEMES_UM005_EXA_v1.0.pdf" title="User Manual For Evaluation Committee (PDF)">User Manual For Evaluation Committee (PDF)  <img src="../images/download.png" width="20" height="19" style="border:0px;" title="User Manual For Evaluation Committee (PDF)"></a></li>
		    	<? } if ($role_type == 'School Board Committ' || $role_type == 'Super Admin') { ?>
		        	<li><a href="THEMES_UM006_SCH_v1.0.pdf" title="User Manual For School Board Committ (PDF)">User Manual For School Board Committ (PDF)  <img src="../images/download.png" width="20" height="19" style="border:0px;" title="User Manual For School Board Committ (PDF)"></a></li>
		    	<? } ?>
          </ul>
	</div>
	</fieldset>
	</form>
	</body>
</html>