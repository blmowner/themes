<?php
    include("../../lib/common.php");
    checkLogin();
    
	function runnum($column_name, $tblname) 
	{ 
		global $db_klas2;
		
		$run_start = "001";
		
		$sql_slct_max = "SELECT MAX($column_name) AS run_id FROM $tblname";
		$sql_slct = $db_klas2;
		$sql_slct->query($sql_slct_max);
		$sql_slct->next_record();

		if($sql_slct->num_rows($sql_slct_max)== 0 || $sql_slct->f("run_id")==NULL) 
		{
			$run_id = date("Ymd").$run_start;
		} 
		else 
		{
			$todate = date("Ymd");
			
			if($todate > substr($sql_slct->f("run_id"),0,8)) 
			{
				$run_id = $todate.$run_start;
			} 
			else 
			{
				$run_id = $sql_slct->f("run_id") + 1; 
			}
		}
		return $run_id;
	}

    if($_POST['change_pwd'] <> "") {
        
        
		$msg = array();
        if(empty($_POST['current_password'])) 
		{ 
			$msg[] = "<div class=\"error\"><span>".load_lang('insert_current_pass')."</span></div>"; 
		} 
		else 
		{ 
			$current_password = md5($_POST['current_password']); 
		}
        if(empty($_POST['new_password'])) 
		{ 
			$msg[] = "<div class=\"error\"><span>".load_lang('insert_new_pass')."</span></div>"; 
		} else 
		{ 
			$new_password = $_POST['new_password']; 
		}
        if(empty($_POST['verify_password'])) 
		{ 
			$msg[] = "<div class=\"error\"><span>".load_lang('insert_verify_pass')."</span></div>"; 
		} else 
		{ 
			$verify_password = $_POST['verify_password'];
			$verifyPassword = md5($verify_password);
		}
		
        if(empty($msg)) {
            if($current_password != getValue_local('user_pass','user_acc','staff_id',$_SESSION['user_id'])) 
			{
                $msg[] = "<div class=\"error\"><span>".load_lang('invalid_current_pass')."</span></div>";
            } else if($new_password != $verify_password) 
			{
                $msg[] = "<div class=\"error\"><span>".load_lang('pass_not_match')."</span></div>";
            } else if($current_password == $verifyPassword) 
			{
                $msg[] = "<div class=\"error\"><span>Current Password and New Password cannot be the same.</span></div>";
			} else {				
				$sql = "SELECT const_value
				FROM base_constant
				WHERE const_term = 'NO_OF_PASSWORDS'";
				
				$db->query($sql);
				$db->next_record();
				$noOfPasswords = $db->f('const_value');
				
				$sql1 = "SELECT password
				FROM pg_credential
				WHERE user_id = '".$_SESSION['user_id']."'
				ORDER BY id DESC
				LIMIT $noOfPasswords";
				
				$result_sq1 = $dba->query($sql1);
				$dba->next_record();
				$no_of_row = $dba->num_rows($sql1);
				

				$isMatched = 'N';
				do {
					$storedPassword = $dba->f('password');
					if ($verifyPassword != $storedPassword) {
						$isMatched = 'N';
					}
					else {
						$isMatched = 'Y'; break;
					}						
				} while ($dba->next_record());

				if ($isMatched == 'N') {
					$credentialId = runnum('id','pg_credential');
					$sql2 = "INSERT INTO pg_credential
					(id, user_id, password, passwordx, change_date)
					VALUES ('$credentialId', '".$_SESSION['user_id']."', '".md5($verify_password)."', '$verify_password', now())";
					
					$result_sq2 = $db->query($sql2);
					$db->next_record();
				
					$sql_update = "UPDATE user_acc SET user_pass='".md5($verify_password)."' WHERE staff_id='".$_SESSION['user_id']."'";
					$action = $db->query($sql_update);
					if($action) {
						tracking($_SESSION['user_id'],load_constant('CHANGE_PASSWORD'), "CHANGE PASSWORD");
						header("refresh:2; url=change_pwd.php");
						$msg[] = "<div class=\"success\"><span>".load_lang('success_pass')."</span></div>";
					}
				}
				else {
					$msg[] = "<div class=\"error\"><span>The new password has been used within the last provided $noOfPasswords
					passwords previously. Please enter a different one.</span></div>";
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

	<title>Change Password</title>
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