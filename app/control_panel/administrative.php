<?php
    include("../../lib/common.php");
    checkLogin();
	
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang; ?>" lang="<?php echo $lang; ?>">

<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
	<meta name="author" content="MJMZ" />

	<title>Administrative Control</title>
    <link rel="stylesheet" type="text/css" href="../../theme/css/<?php echo $css; ?>" />
</head>

<body>

<div class="padding-5 margin-5 outer">
    
    <ul class="downline-ul">
        <!--<li><?php echo load_hyperlink('2','VIEW_MANAGE_USER', load_lang('manage_user'),'user/manage_user.php',''); ?></li>
        <li><?php echo load_hyperlink('2','VIEW_MANAGE_ROLE', load_lang('manage_role'),'role/manage_role.php',''); ?></li>
        <li><?php echo load_hyperlink('2','VIEW_MENU_MANAGER', load_lang('menu_manager'),'administrative/menu_manager.php',''); ?></li>
        <li><?php echo load_hyperlink('2','VIEW_SYS_CONFIG',load_lang('sys_config'),'administrative/system_config.php',''); ?></li>
        <li><?php echo load_hyperlink('2','VIEW_MODULE_MANAGER', load_lang('module_manager'),'administrative/module_manager.php',''); ?></li>
        <li><?php echo load_hyperlink('2','VIEW_PERMISSION_MANAGER', load_lang('permission_manager'),'administrative/permission_manager.php',''); ?></li>
	 	<li><?php echo load_hyperlink('2','VIEW_BASE_MANAGER', load_lang('base_manager'),'administrative/base_manager.php',''); ?></li>
	 	<li><?php echo load_hyperlink('2','EMAIL_NOTIFICATION_MANAGER', load_lang('email_notification_manager'),'administrative/email_notification_manager.php',''); ?></li>-->
		<li><a href="user/manage_user.php">Manage User </a></li>
		<li><a href="role/manage_role.php">Manage Role </a></li>
		<li><a href="administrative/menu_manager.php">Menu Manager </a></li>
		<li><a href="administrative/system_config.php">System Configuration </a></li>
		<li><a href="administrative/module_manager.php">Module Manager </a></li>
		<li><a href="administrative/permission_manager.php">Permission Manager </a></li>
		<li><a href="administrative/base_manager.php">Base Language  Manager</a></li>
		<li><a href="administrative/email_notification_manager.php">Email Notification  Manager</a></li>
		<li><a href="administrative/publication_manager.php">Publication Manager</a></li>
		<li><a href="administrative/base_constant.php">Base_Constant Manager</a></li>


    </ul>
</div>

</body>
</html>