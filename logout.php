<?php

/**
 * @author MJMZ
 * @copyright 2011
 */
 
include("lib/common.php");
session_start();


    
$sql_update = "UPDATE user_acc SET user_online_stat=0, user_last_session=now() WHERE staff_id ='".$_SESSION['user_id']."'";
$process = $db->query($sql_update);

if($process) {
    if(!empty($_SESSION['user_id'])) {
        tracking($_SESSION['user_id'], load_constant('LOGOUT'), "LOGOUT");
    }
}


session_unset();
session_destroy();
$URL = load_constant('REDIRECT_FORCE');
header("refresh:2; url=$URL");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang; ?>" lang="<?php echo $lang; ?>">

<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
	<meta name="author" content="mjmz" />

	<title><?php echo load_lang('frame_name'); ?></title>
    <link rel="stylesheet" type="text/css" href="theme/css/<?php echo $css; ?>" />
</head>

<body>
    <div id="login"><div class="success"><span><?php echo load_lang('logout_msg'); ?></span></div></div>
    
</body>
</html>