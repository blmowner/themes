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
        if(preg_match('/[^-_@. 0-9A-Za-z]/', $username)||preg_match('/[^-_@. 0-9A-Za-z]/', $password)) 
		{
			$validpwd = TRUE;
			$msg = "Invalid username or password!";
		}
		else
		{
			if($username!="" && $password!="") {
				$sql1 = "SELECT staff_id
				FROM user_acc 
				WHERE staff_id='$username'
				AND user_status='ACTIVE'";
				$db->query($sql1);
				$db->next_record();
				$sql_no_of_row = $db->num_rows($sql1);
				
				if ($sql_no_of_row > 0) {
					
					$sql_login = "SELECT *
					FROM user_acc 
					WHERE user_pass='$encryptpass' 
					AND user_status='ACTIVE'
					AND staff_id='$username'";
					$db->query($sql_login);
					$db->next_record();
					$attempts = $db->f('attempts');
					$lockStatus = $db->f('lock_status');
					$no_of_row = $db->num_rows($sql_login);
					
					if($no_of_row > 0) {
						if ($lockStatus == 'U' ) {
							$validpwd = FALSE;
							$row_rec = $db->rowdata();
							
							/* set all session for this particular person */
							$_SESSION['user_id'] = $username;
							$_SESSION['user_role'] = $row_rec['role_id'];
							$_SESSION['user_log'] = load_constant("LOGIN"); /* indicator to detect whether the user login or not */ 
							
							$sql_update0 = "SELECT user_curr_login
							FROM user_acc
							WHERE staff_id='$username'";
							$dba->query($sql_update0);
							$dba->next_record();
							$lastLogin = $dba->f('user_curr_login');
							
							$sql_update = "UPDATE user_acc SET attempts = 0, user_online_stat = 1, 
							user_ip='".$_SERVER['REMOTE_ADDR']."', user_curr_login=now(), user_last_login = '$lastLogin'
							WHERE staff_id='$username'";
							$process = $db->query($sql_update);
							if($process) {
								tracking($row_rec['staff_id'], load_constant('LOGIN'), "LOGIN"); // track user activity
							}
							
							header("location:app/index.php");
						}
						else {//lockStatus = L
							$validpwd = TRUE;
							$sql = "SELECT attempts
							FROM user_acc
							WHERE staff_id = '$username'";
							$result_sql = $db->query($sql);
							$db->next_record();
							$attempts = $db->f('attempts');
							
							$attempts++;
							$sql_update = "UPDATE user_acc SET attempts = '$attempts'
										   WHERE staff_id='".$row_rec['staff_id']."'";
							$process = $db->query($sql_update);
							//$msg="Invalid username or password!. You have reached maximum tries. Please retry after 5 minutes.";
							$msg="Invalid username or password!";
						}
					}
					else {
						$validpwd = TRUE;
						$sql = "SELECT attempts
						FROM user_acc
						WHERE staff_id = '$username'";
						$result_sql = $db->query($sql);
						$db->next_record();
						$attempts = $db->f('attempts');
						$attempts++;
						$sql_update1 = "UPDATE user_acc SET attempts = '$attempts'
									   WHERE staff_id='$username'";
						$process_sql_update1 = $db->query($sql_update1);
						//$msg="Invalid username or password! You have tried $attempts time(s).";
						$msg="Invalid username or password!";
					}
				}
				else {
					$validpwd = TRUE;
					$msg="Invalid username or password!";
				}
			} 
			else {
				$validpwd = TRUE;
				$msg="Invalid username or password!";
			}
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang; ?>" lang="<?php echo $lang; ?>">
<head>
<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1"/>
<meta name="author" content="" />
<link rel="stylesheet" type="text/css" href="theme/css/colorbox.css" media="screen" />
<script type="text/javascript" src="lib/js/ckeditor/ckeditor.js"></script>
<script src="lib/js/jquery.min2.js"></script>

<title><?php echo load_lang('frame_name'); ?></title>
<script language="JavaScript" type="text/javascript">
    // throw away from current frame!
    if (top.location != self.location) {
        top.location = self.location.href;
    }
</script>
<link rel="stylesheet" href="theme/css/login.css">
<script src="lib/js/jquery.colorbox.js"></script>
</head>

<body>
<script>
function iframecode(url, w, h) {
				//alert("111");
				window.parent.$.colorbox({href:url, iframe: true, scrolling: false, width: w, height: h});
			}
//$(".forgot").colorbox({ bottom: "65px", width:"70%", height:"75%", iframe:true, onClosed:function(){} }); 
</script>
    <div class="logo"><img src="theme/images/logo.png" /><h1>Management and Science University (MSU)</h1></div>
    <div class="white_stripe"></div>
    <div class="banner_left">
        <div class="banner_right">
            <div class="banner_rightContent">
                <div class="title"><img src="theme/images/title.png"><span class="title_support"><?php echo load_lang('frame_name'); ?></span></div>
                <div id="login">
                    <?php
                        if ($validpwd) 
                        {
                            session_unset();
                            session_destroy();
                            //echo load_lang("invalid_login");
                            ?>
                            <span class="message"><?=$msg?></span>
                            <?;
                        }
                    ?>
                    <form method="post">
                        <input type="text" name="username" placeholder="User ID"/>
                        <input type="password" name="password" placeholder="Password"/>
                        <input type="submit" name="submit" value="<?php echo load_lang('login'); ?>"/><br />
                        <a class="forgot" onclick="iframecode('forgot_pwd.php','67.2%','42.2%')" href="#"  id = "forgot1" name = "forgot1">Forgot Your Password?</a>
                    </form>
                 </div>
            </div>
            <div id="word-login"><?php echo load_lang('disclaimer'); ?></div>
        </div>
    </div>
</body>
</html>