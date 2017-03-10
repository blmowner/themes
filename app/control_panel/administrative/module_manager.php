<?php
    include("../../../lib/common.php");
    checkLogin();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang; ?>" lang="<?php echo $lang; ?>">

<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
	<meta name="author" content="MJMZ" />

	<title>Module Manager</title>
    <link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
    <link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
    <script src="../../../lib/js/jquery.min2.js"></script>
	<script src="../../../lib/js/jquery.colorbox.js"></script>
    <script>
  
		$(document).ready(function(){
		       $(".add_module").colorbox({width:"70%", height:"50%", iframe:true,          
               onClosed:function(){ 
                //window.location.reload(true); //uncomment this line if you want to refresh the page when child close
                window.location = window.location;   //reload the parent page  
                                
                } }); 
                
                $(".edit_module").colorbox({width:"70%", height:"50%", iframe:true,          
               onClosed:function(){ 
                //window.location.reload(true); //uncomment this line if you want to refresh the page when child close
                window.location = window.location;   //reload the parent page  
                                
                } }); 
          });
	</script>
</head>

<body>
    <div class="padding-5 margin-5 outer">
        <h3>Module Manager</h3>
        <a href="add_module" class="add_module float-right"><?php echo load_lang('add_module'); ?></a><br /><br />
        <table width="100%" cellpadding="3" cellspacing="3" class="thetable">
            <tr>
                <th width="5%">#</th>
                <th width="45%"><?php echo load_lang('module_name'); ?></th>
                <th width="45%"><?php echo load_lang('description'); ?></th>
                <th width="5%"><?php echo load_lang('action'); ?></th>
            </tr>
            <?php
                $sql = "SELECT * FROM base_module ORDER BY parent_id, module_level";
                $db->query($sql);
                $nx = $db->next_record();
                if($nx){
                    $inc = 1;
                    do{
                        if($inc % 2) $color = "first-row"; else $color = "second-row";
            ?>
            <tr class="<?php echo $color; ?>">
                <td align="center"><?php echo $inc; ?></td>
                <td><?php echo $db->f('module_name'); ?></td>
                <td><?php echo $db->f('module_description'); ?></td>
                <td align="center"><a href="edit_module.php?mdid=<?php echo $db->f('module_id'); ?>" class="edit_module"><?php echo load_lang('edit'); ?></a></td>
            </tr>
            <?php        
                    $inc++;
                    $nx = $db->next_record();
                    } while($nx);
                }
            
            ?>
        </table>
    </div>
</body>
</html>