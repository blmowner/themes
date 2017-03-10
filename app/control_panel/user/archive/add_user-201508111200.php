<?php 
    include("../../../lib/common.php"); 
    checkLogin();
    
    
    if($_POST['submit'] <> "") {
        $msg = array();
        
        if(empty($_POST['staff_id'])) $msg[] = "<div class=\"error\"><span>Please select staff before submit!</span></div>"; else $staff_id = $_POST['staff_id'];
        if($_POST['staff_role']=="") $msg[] = "<div class=\"error\"><span>Please select role!</span></div>"; else $staff_role = $_POST['staff_role'];
        if($_POST['staff_status']=="") $msg[] = "<div class=\"error\"><span>Please select status!</span></div>"; else $staff_status = $_POST['staff_status'];
        
        if(empty($msg)) {
            
            //check if user already exist
            echo $checkUserExist = getValue_local('staff_id','user_acc','staff_id',$staff_id);
            if($staff_id == $checkUserExist) {
                $msg[] = "<div class=\"error\"><span>User already exist in the system!</span></div>";
            } else {
            //if user not in a system, then set them in!
			$sqlIns1 = "INSERT INTO user_acc (user_id, role_id, staff_id, user_pass, user_online_stat, user_status, user_type, created_by, created_date) 
                       VALUES('".run_num('user_id','user_acc')."','$staff_role', '$staff_id', '".md5($staff_id)."','0','$staff_status','".$_POST['user_type']."',
					   '".$_SESSION['user_id']."', now())";
            $result_sqlIns1 = $db->query($sqlIns1);		
            
                if($result_sqlIns1) {
                    if(!empty($_POST['select'])){
                        foreach($_POST['select'] as $permisssion) {
                            $sqlPermission = "INSERT INTO base_permission_link (link_id, permission_id, user_id) 
                                              VALUES ('".run_num('link_id','base_permission_link')."','".$permisssion."','".$staff_id."')";
                            $db->query($sqlPermission);
                            } 
                    }
                    
                    //$staff_name = getValueSiso('name','siso_employee','empid',$staff_id);
                    //$staff_dept = getValueSiso('dept_id','siso_employee','empid',$staff_id);
                    
                    //$sqlInsertEmployee = "INSERT INTO employee (empid, name, dept_id) VALUES ('$staff_id','$staff_name','$staff_dept')";
                    //$db->query($sqlInsertEmployee);

					if ($_POST['user_type']=='E') {
						//set them in pg_employee if they are 'E'mployee or staff
						$sqlIns2 = "INSERT INTO pg_employee (staff_id, insert_by, insert_date, status) 
						VALUES('$staff_id', '".$_SESSION['user_id']."', now(), 'A')";
						$result_sqlIns2 = $db->query($sqlIns2);
					}
			
                    tracking($_SESSION['user_id'], load_constant('INSERT'), 'ADD_USER');
                    header("refresh:2; URL=manage_user.php");
                    $msg[] = "<div class=\"success\"><span>New user has been added!</span></div>";
                          
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

	<title>Add new user</title>
    <link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
    <link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
    
    <script src="../../../lib/js/jquery.min2.js"></script>
	<script src="../../../lib/js/jquery.colorbox.js"></script>
    <script>
  
		$(document).ready(function(){
		      //$(".select_user").colorbox({width:"60%", height:"40%", iframe:true});
              
              $.fn.getParameterValue = function(data1, data2) {
                  //alert(data);
                  document.addStaff.staff_id.value = data1;
				  document.addStaff.user_type.value = data2;
                };
              
               $(".select_user").colorbox({width:"80%", height:"90%", iframe:true,          
               onClosed:function(){ 
                //location.reload(true); //uncomment this line if you want to refresh the page when child close
                                
                } }); 
          });
	</script>
</head>

<body>
<div class="padding-5 margin-5 outer">
<?php
if(!empty($msg)) {
    foreach($msg as $key) {
        echo $key;
    }
}

?>
<h3>Add new user</h3>
<form method="post" id="form-set" name="addStaff">
<label class="labeling"><?php echo load_lang('staff_id'); ?></label><input type="text" size="20" name="staff_id" readonly="" value="<?php echo isset($staff_id) ? $staff_id : "" ?>" /><a class='select_user' href="select_user.php">[ Select User ]</a><br />
<!--label><?php //echo load_lang('name'); ?></label><input type="text" name="staff_name" size="50" readonly="" /><br /><br /-->
<label class="labeling">Role</label><select name="staff_role"><?php dd_menu(array('role_id', 'role_type'), 'base_user_role', '' ,'role_id'); ?></select>&nbsp;&nbsp;
<label class="labeling">Status</label>
<select name="staff_status">
    <option value="">-- Please select --</option>
    <option value="ACTIVE">Active</option>
    <option value="INACTIVE">Inactive</option>
</select>
<input type="hidden" name="user_type" id="user_type"></input>
<h3>User Permission</h3>
    <?php
        
        $sqlshow = "SELECT module_id, module_name from base_module";
        $db->query($sqlshow);
        $result = $db->next_record();
        if($result){
            do {
                echo "<div style=\"float:left; padding-right:15px;\"><b>".$db->f("module_name")."</b><br />";
                echo show_permission($db->f("module_id"));
                echo "</div>";
            $result = $db->next_record();    
            } while($result);
        }
        echo "<br clear=\"all\" />";
        
    ?>
<center>
<input type="submit" name="submit" value="<?php echo load_lang('save'); ?>" class="fancy-button-green" />
<input type="reset" name="reset" value="<?php echo load_lang('reset'); ?>" class="fancy-button-grey" />
<a href="manage_user.php">[ <?php echo load_lang('cancel'); ?> ]</a>
</center>
</form>
</div>
</body>
</html>