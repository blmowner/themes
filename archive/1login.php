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
						
						echo $sql_update = "UPDATE user_acc SET attempts = 0, user_online_stat = 1, 
						user_ip='".$_SERVER['REMOTE_ADDR']."', user_curr_login=now(), user_last_login='$lastLogin'
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
            if ($validpwd) 
    		{
    			session_unset();
    			session_destroy();
                //echo load_lang("invalid_login");
				?>
				<table>
					<tr>
						<td align="center"><span style="color:#FF0000"><?=$msg?></span></td>
					</tr>
				</table>
				<?;
    			
    		}
		?>
        <form method="post">
		<table>
			<tr>
				<td><label class="float-left"><?php echo load_lang('username'); ?></label></td>
				<td></label><input type="text" name="username" maxlength="16" size="30"/></td>
			</tr>
			<tr>
				<td><label class="float-left"><?php echo load_lang('password'); ?></label></td>
				<td></label><input type="password" name="password" maxlength="16" size="30"/>
				</td>
            </tr>
			<tr>
				<td></td>
				<td>
				<input type="submit" name="submit" value="<?php echo load_lang('login'); ?>" class="fancy-button-grey float-right" /></td>
			</tr>
		</table>
        </form>
    <br class="clear" />
    </div>
    <div id="word-login">
    <?php echo load_lang('disclaimer'); ?>
</div>

</body>
</html>