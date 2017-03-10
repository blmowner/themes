<?php
    $page_name = "login";
    include("lib/common.php"); 
        
    if($_POST['submit'] <> "") {
        $validpwd = FALSE;
        $username = $_POST['username'];
        $username = (get_magic_quotes_gpc()) ? mysql_real_escape_string($username) : $username;
        $password = $_POST['password'];
        $password = (get_magic_quotes_gpc()) ? stripcslashes($password) : $password;
        
        $encryptpass = md5($password);
        
        
        if(!$validpwd) {
            $sql_login = "SELECT * FROM user_acc WHERE (user_pass='$encryptpass' AND user_status='ACTIVE') AND staff_id='$username'";
            $db->query($sql_login);
            $db->next_record();
            $no_of_row = $db->num_rows($sql_login);
            if($no_of_row > 0) {
                $validpwd = TRUE;
                $row_rec = $db->rowdata();
                
                /* set all session for this paticular person */
                $_SESSION['user_id'] = $row_rec['staff_id'];
                $_SESSION['user_role'] = $row_rec['role_id'];
                $_SESSION['user_log'] = load_constant("LOGIN"); /* indicator to detect whether the user login or not */ 
                
                $sql_update = "UPDATE user_acc SET user_online_stat = 1, user_ip='".$_SERVER['REMOTE_ADDR']."', user_last_login=now() 
                               WHERE staff_id='".$row_rec['staff_id']."'";
                $process = $db->query($sql_update);
                if($process) {
                    tracking($row_rec['staff_id'], load_constant('LOGIN'), "LOGIN"); // track user activity
                }
                
                header("location:app/index.php");
            }
        } 
    } else {
        $validpwd = TRUE;
        //print "<meta http-equiv='refresh' content='0; url=login.php' />";
    }
    
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang; ?>" lang="<?php echo $lang; ?>">

<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
	<meta name="author" content="mjmz" />

	<title><?php echo load_lang('frame_name'); ?></title>
    	<script language="JavaScript" type="text/javascript">
    		// throw away from current frame!
        	if (top.location != self.location) {
          		top.location = self.location.href;
        	}
    	</script>
    <link rel="stylesheet" type="text/css" href="theme/css/<?php echo $css; ?>" />
</head>

<body>

    <div id="login">
        <h2><?php echo load_lang('frame_name'); ?></h2>
        <?php
            if (!$validpwd) 
    		{
    			session_unset();
    			session_destroy();
                echo load_lang("invalid_login");
    			
    		}
		?>
        <form method="post">
            <label class="float-left"><?php echo load_lang('username'); ?></label><input type="text" class="text-input float-left" name="username" maxlength="16" />
            <label class="float-left"><?php echo load_lang('password'); ?></label><input type="password" class="text-input float-left" name="password" /><br />
            <div class="button-placement">
                <input type="submit" name="submit" value="<?php echo load_lang('login'); ?>" class="fancy-button-grey float-right" />
            </div>
        </form>
    <br class="clear" />
    </div>
    <div id="word-login">
    <?php echo load_lang('disclaimer'); ?>
</div>

</body>
</html>