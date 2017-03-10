<?php 
include("../../../lib/common.php");
checkLogin();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang; ?>" lang="<?php echo $lang; ?>">

<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
	<meta name="author" content="MJMZ" />

	<title>Manage Role</title>
    <link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
</head>

<body>
<div class="padding-5 margin-5 outer">
    <span class="float-right"><?php echo load_hyperlink('4','ADD_ROLE', load_lang('add_new_role'),'add_role.php',''); ?></span><br /><br />
    <table cellpadding="3" cellspacing="0" width="100%" class="thetable">
        <tr>
            <th width="20%"><?php echo load_lang('role_id'); ?></th>
            <th width="20%"><?php echo load_lang('role_type'); ?></th>
            <th width="10%"><?php echo load_lang('service'); ?></th>
            <th width="45%"><?php echo load_lang('description'); ?></th>
            <th width="5%"><?php echo load_lang('action'); ?></th>
        </tr>
        <?php
            $sql_role = "SELECT * FROM base_user_role";
            $db->query($sql_role);
            $result = $db->next_record();
            if($result) {
                $inc = 1;
                do {
                    if($inc % 2) $color = "first-row"; else $color = "second-row";
        ?>
        <tr class="<?php echo $color; ?>">
            <td align="center"><?php echo $db->f('role_id'); ?></td>
            <td align="center"><?php echo $db->f('role_type'); ?></td>
            <td align="center"><?php echo $db->f('service_id'); ?></td>
            <td><?php echo $db->f('description'); ?></td>
            <td align="center"><?php echo load_hyperlink('4','EDIT_ROLE',load_lang('edit'),'edit_role.php?rlid='.$db->f('role_id'),''); ?></td>
        </tr>
        <?php            
                $inc++;    
                $result = $db->next_record();
                } while($result);
            }
            
        ?>
    </table>
</div>


</body>
</html>