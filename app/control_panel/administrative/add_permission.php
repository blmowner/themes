<?php
    include("../../../lib/common.php");
    checkLogin();
    
    if($_POST['submit'] <> "") {
        $msg = array();
        if(empty($_POST['module'])) $msg[] = "<div class=\"error\"><span>Please select module</span></div>";  
        if(empty($_POST['term'])) $msg[] = "<div class=\"error\"><span>Please insert permission term</span></div>";
        
        if(empty($msg)) {
            $_POST['term'] = trim(strtoupper($_POST['term']));
            $permission_id = run_num('permission_id','base_user_permission');
            
           $sql = "INSERT INTO base_user_permission (permission_id, permission_term, module_id) 
                    VALUES ('$permission_id','".$_POST['term']."','".$_POST['module']."')";
           $save = $db->query($sql);
           if($save) {
                tracking($_SESSION['user_id'],load_constant('INSERT'),"ADD PERMISSION");
                header("refresh:2; URL=permission_manager.php");
                $msg[] = "<div class=\"success\"><span>New permission successfully added!</span></div>";
           }
           
        }
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo lang; ?>" lang="<?php echo $lang; ?>">

<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
	<meta name="author" content="MJMZ" />

	<title>Permission Manager</title>
    <link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
    <link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
    <script src="../../../lib/js/jquery.min2.js"></script>
	<script src="../../../lib/js/jquery.colorbox.js"></script>
    <script>
  
		$(document).ready(function(){
		       $(".add_module").colorbox({width:"70%", height:"50%", iframe:true,          
               onClosed:function(){ 
                window.location.reload(true); //uncomment this line if you want to refresh the page when child close
                                
                } }); 
          });
	</script>
</head>

<body>
    <div class="padding-5 margin-5 outer">
    <?php
        if(!empty($msg)) {
            foreach($msg as $err){
                echo $err;
            }
        }
    ?>
        <h3><?php echo load_lang('add_permission'); ?></h3>
        <form id="form-set" method="post">
        <div style="overflow: auto; height: 150px;">
        * <?php echo load_lang('select_module'); ?> <a href="add_module.php" class="add_module">[ <?php echo load_lang('add_module'); ?> ]</a>
        <table cellpadding="3" cellspacing="3" width="100%" class="thetable">
            <tr>
                <th width="5%">&nbsp;</th>
                <th width="5%">#</th>
                <th><?php echo load_lang('module_name'); ?></th>
            </tr>
            <?php
                $sqlshow = "SELECT module_id, module_name from base_module";
                $db->query($sqlshow);
                $result = $db->next_record();
                if($result){
                    do {
            ?>
            <tr>
                <td align="center"><input type="radio" name="module" id="module" value="<?php echo $db->f("module_id"); ?>" /></td>
                <td align="center"><?php echo $db->f("module_id"); ?></td>
                <td><?php echo $db->f("module_name"); ?></td>
            </tr>
            <?php
                $result = $db->next_record();    
                    } while($result);
                }
            ?>
        </table>
        </div><br /><br />
        <label class="labeling">* <?php echo load_lang('permission_term'); ?></label><input type="text" name="term" size="50" />e.g: VIEW_PERMISSION, ADD_ROLE<br /><br />
        <input type="submit" name="submit" value="<?php echo load_lang('save'); ?>" class="fancy-button-green" /><a href="permission_manager.php">[ <?php echo load_lang('cancel'); ?> ]</a>
        </form>
    </div>
</body>
</html>