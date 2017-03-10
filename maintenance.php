<?php 

$maintenance_page_name = "maintenance";
include("lib/common.php"); 


if($_POST['retry'] <> "") {
    $sql_check = "SELECT config_status FROM base_config";
    $db->query($sql_check);
    $db->next_record();
    if($db->f("config_status")=="1") {
        session_unset();
        session_destroy();
        $URL = load_constant('REDIRECT_FORCE');
        header("location:$URL");
    }
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang; ?>" lang="<?php echo $lang; ?>">

<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
	<meta name="author" content="MJMZ" />

    <title><?php echo load_lang('frame_name'); ?></title>
    <link rel="stylesheet" type="text/css" href="theme/css/<?php echo $css; ?>" />
</head>

<body>

<div id="login">
    <div class="maintenance"><span><?php echo load_lang('maintenance_msg'); ?></span></div><br />
    
    <form method="post">
        <input type="submit" name="retry" class="fancy-button-blue" value="<?php echo load_lang('retry_btn'); ?>" />
    </form>
</div>
</body>
</html>