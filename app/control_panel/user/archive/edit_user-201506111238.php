<?php 
    include("../../../lib/common.php"); 
    checkLogin();
    
    $userId = $_GET['uid'];
    
    if($_POST['submit'] <> "") {
        $msg = array();
        
        if($_POST['role'] == "") $msg[] = "<div class=\"error\"><span>Please select role type!</span></div>"; else $role = $_POST['role'];
        $status = $_POST['status'];
        if(empty($msg)) {
            
            $sqlUpd = "UPDATE user_acc SET role_id='$role', user_status='$status' WHERE staff_id='$userId'";
            $process = $db->query($sqlUpd);
            
            if($process){
                //delete all the permission first before insert new permission
                $deleteAllPermission = "DELETE FROM base_permission_link WHERE user_id='$userId'";
               $db->query($deleteAllPermission);
                
                if(!empty($_POST['select'])) {
                    //insert new permission value based on the tick checkbox
                    foreach($_POST['select'] as $permission) {
                        $sqlPermission = "INSERT INTO base_permission_link (link_id, permission_id, user_id) 
                                               VALUES ('".run_num('link_id','base_permission_link')."','".$permission."','".$userId."')";
                       $db->query($sqlPermission);
                    }
                }
            
            
            tracking($_SESSION['user_id'], load_constant('UPDATE'), "EDIT USER"); 
            header("refresh:2; url=manage_user.php");
            $msg[] = "<div class=\"success\"><span>User has been successfully updated!</span></div>";
            
            }
        }
    }

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang; ?>" lang="<?php echo $lang; ?>">

<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
	<meta name="author" content="MFZ" />

	<title>Edit User</title>
    <link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
</head>

<body>
<div class="margin-5">
<?php
    if(!empty($msg)) {
        foreach($msg as $err) {
            echo $err;
        }
    }
?>
    <h3>Edit User</h3>
    <form method="post" id="form-set">
        <label class="labeling"><?php echo load_lang('name'); ?></label><?php echo getValue('name','new_employee','empid',$userId); ?><br /><br />
        <label class="labeling"><?php echo load_lang('role_type'); ?></label>
            <select name="role">
            <?php
                $roleType = getValue('role_id','user_acc','staff_id',$userId);
                dd_menu(array('role_id', 'role_type'), 'base_user_role', $roleType ,'role_id'); 
            ?>
            </select><br />
           
        <label class="labeling"><?php echo load_lang('status'); ?></label>
            <select name="status">
            <?php
                $stat = getValue('user_status','user_acc','staff_id', $userId);
                if($stat == "ACTIVE") {
                    echo "<option value=\"ACTIVE\" selected>Active</option>";
                    echo "<option value=\"INACTIVE\">Inactive</option>";
                } else {
                    echo "<option value=\"ACTIVE\">Active</option>";
                    echo "<option value=\"INACTIVE\" selected>Inactive</option>";
                }
            ?> 
            </select>
        <br /><br />
        <h3>User Permission</h3>
        <?php
            
            $sqlshow = "SELECT module_id, module_name from base_module";
            $db->query($sqlshow);
            $result = $db->next_record();
            if($result){
                do {
                    echo "<div style=\"float:left; padding-right:15px;\"><b>".$db->f("module_name")."</b><br />";
                    echo show_tick_permission($db->f("module_id"),$userId);
                    echo "</div>";
                $result = $db->next_record();    
                } while($result);
            }
            echo "<br clear=\"all\" />";
            
        ?>
        
        <label class="labeling">&nbsp;</label><input type="submit" name="submit" value="<?php echo load_lang('save'); ?>" class="fancy-button-green" />
        <a href="manage_user.php">[ <?php echo load_lang('cancel'); ?> ]</a>
    </form>
</div>
</body>
</html>