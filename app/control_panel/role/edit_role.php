<?php
    include("../../../lib/common.php");
    checkLogin();
    
    $rlid = $_GET['rlid'];
    
    if($_POST['submit'] <> "") {
        $msg = array();
        if(empty($_POST['role_name'])) $msg[] = "<div class=\"error\"><span>Please insert role type!</span></div>"; else $role_name = $_POST['role_name'];
        if(empty($_POST['select'])) $msg[] = "<div class=\"error\"><span>Please tick at least 1 menu!</span></div>";
        if(empty($_POST['role_code'])) $msg[] = "<div class=\"error\"><span>Please insert role code!</span></div>"; else $role_code = $_POST['role_code'];
		
		$role_code = $_POST['role_code'];
		$rolehidden = $_POST['rolehidden'];
		
		$sql6_1 = "SELECT COUNT(*) as total
		FROM base_user_role
		WHERE role_code = '$role_code'";
		
		$result6_1 = $db->query($sql6_1);		
		$db->next_record();
		
		$total = $db->f('total'); 
		if($total>0 && ($rolehidden != $role_code)) {
			$msg[] = "<div class=\"error\"><span>Role Code Already Exist, Please change role_code</span></div>";
			//$role_code = $_POST['role_code'];
		}
		
		
        if(empty($msg)) {
            
            $roleID = run_num('role_id','base_user_role'); // generate id for the role
            
            
            $sqlIns = "UPDATE base_user_role SET role_type='$role_name', description='".$_POST['desc']."' WHERE role_id='$rlid'";
            $insResult = $db->query($sqlIns);
            if($insResult) {
                
                $deleteAllRole = "DELETE FROM base_menu_link WHERE role_id='$rlid'";
                $db->query($deleteAllRole);
                            
                foreach($_POST['select'] as $key) {

                    $linkID = run_num('link_id','base_menu_link')." ";//generate link_id-(auto increment value)             
                    $sqlInsRole = "INSERT INTO base_menu_link (link_id, role_id, menu_id) VALUES ('$linkID','$rlid','".$key."')";
                    $db->query($sqlInsRole);
                   
                }
                
                header("refresh:2; URL=manage_role.php");
                $msg[] = "<div class=\"success\"><span>Role successfully updated!</span></div>";
                
            }
        }
        
    }
    
    
    $sqlShow = "SELECT * FROM base_user_role WHERE role_id='$rlid'";
    $res = $db->query($sqlShow);
    $rows = mysql_fetch_array($res);
    
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang; ?>" lang="<?php echo $lang; ?>">

<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
	<meta name="author" content="MJMZ" />

	<title>Edit Role</title>
    <link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
</head>

<body>
<div class="padding-5 margin-5 outer">
<?php
    if(!empty($msg)) {
        foreach($msg as $err) {
            echo $err;
        }
    }
?>
    <h3>Edit Role</h3>
    <?php echo load_lang('compulsary'); ?>
    <form method="post" id="form-set">
        <label class="labeling">* <?php echo load_lang('role_type'); ?></label><input type="text" name="role_name" size="40" value="<?php echo $rows['role_type']; ?>" />
		<br />
		<label class="labeling">* Role Code</label><input type="text" name="role_code" size="40" value="<?=$rows['role_code']?>" />
		<input type="hidden" name="rolehidden" size="40" value="<?=$rows['role_code']?>" />
		<br />
        <!-- will be open for the future use label></label><input type="text" name="service_id" size="20" value="" /-->
        <label class="labeling"><?php echo load_lang('description'); ?></label><textarea name="desc" cols="50" rows="5"><?php echo $rows['description']; ?></textarea><br /><br />
        
        <table width="100%" cellpadding="3" cellspacing="3" class="thetable">
            <tr>
                <th>* Menu</th>
            </tr>
            <tr>
                <td>
                    <?php 
                       include("../../../lib/menu_role_class.php"); 
                        $rolemenu = new MenuRoles();
                        echo $rolemenu->get_role();
                        
                    ?>
                </td>
            </tr>
        </table><br /><br />
        <input type="submit" name="submit" value="<?php echo load_lang('edit'); ?>" class="fancy-button-green" />
        <input type="reset" name="reset" value="<?php echo load_lang('reset'); ?>" class="fancy-button-grey" />
        <a href="manage_role.php">[ <?php echo load_lang('cancel'); ?> ]</a>
        </form>
</div>


</body>
</html>