<?php
	ini_set('display_errors', 'On');
	ini_set('html_errors', 0);
    include("../../lib/common.php");
    checkLogin();
    
    if($_POST['change_pwd'] <> "") {
        
        $msg = array();
        if(empty($_POST['current_password'])) { $msg[] = "<div class=\"error\"><span>".load_lang('insert_current_pass')."</span></div>"; } else { $current_password = md5($_POST['current_password']); }
        if(empty($_POST['new_password'])) { $msg[] = "<div class=\"error\"><span>".load_lang('insert_new_pass')."</span></div>"; } else { $new_password = $_POST['new_password']; }
        if(empty($_POST['verify_password'])) { $msg[] = "<div class=\"error\"><span>".load_lang('insert_verify_pass')."</span></div>"; } else { $verify_password = $_POST['verify_password']; }
        
        if(empty($msg)) 
		{
            if($current_password != getValue('user_pass','user_acc','staff_id',$_SESSION['user_id'])) 
			{
				echo "if";
                $msg[] = "<div class=\"error\"><span>".load_lang('invalid_current_pass')."</span></div>";
            } 
			else if($new_password != $verify_password) 
			{
				echo "elseif";
                $msg[] = "<div class=\"error\"><span>".load_lang('pass_not_match')."</span></div>";
            } 
			else 
			{
				echo "else";
                $sql_update = "UPDATE user_acc SET user_pass='".md5($verify_password)."' WHERE staff_id='".$_SESSION['user_id']."'";
                $action = $db->query($sql_update);
                if($action) 
				{
					echo " if dalam else";
                    tracking($_SESSION['user_id'],load_constant('CHANGE_PASSWORD'), "CHANGE PASSWORD");
                    header("refresh:2; url=change_pwd.php");
                    $msg[] = "<div class=\"success\"><span>".load_lang('success_pass')."</span></div>";
                }
            }
        }
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang; ?>" lang="<?php echo $lang; ?>">

<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
	<meta name="author" content="MJMZ" />

	<title>Change Passowrd</title>
    <link rel="stylesheet" type="text/css" href="../../theme/css/<?php echo $css; ?>" />
</head>

<body>
<div class="margin-5 padding-5 outer">
<?php
if(!empty($msg)) 
{
    foreach($msg as $key) {
       echo $key;
    }
}
?>
    <form method="post" id="form-set">
    <div class="info"><span><?php echo load_lang('secure_current_password'); ?></span><label class="labeling"><? echo load_lang('current_password'); ?></label>
    <input type="password" size="30" name="current_password" /></div>
        <br class="clear" />
        <label class="labeling"><? echo load_lang('new_password'); ?></label>&nbsp;<input type="password" size="30" name="new_password" /><br />
        <label class="labeling"><? echo load_lang('verify_password'); ?></label>&nbsp;<input type="password" size="30" name="verify_password" /><br />
        <label class="labeling">&nbsp;</label><input type="submit" name="change_pwd" value="<?php echo load_lang('change_pwd_btn'); ?>" class="fancy-button-blue" />
        <input type="reset" name="reset" value="<?php echo load_lang('reset_btn'); ?>" class="fancy-button-grey" />
    </form>
</div>
</body>
</html>