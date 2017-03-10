<?php
    include("../../../lib/common.php");
    checkLogin();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo lang; ?>" lang="<?php echo $lang; ?>">

<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
	<meta name="author" content="MJMZ" />

	<title>Permission Manager</title>
    <link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
</head>

<body>
    <div class="padding-5 margin-5 outer">
        <h3><?php echo load_lang('permission_manager'); ?></h3>
        <a href="add_permission.php" class="float-right"><?php echo load_lang('add_permission'); ?></a><br /><br />
        <table cellpadding="3" cellspacing="3" width="100%" class="thetable">
            <tr>
                <th width="10%">#</th>
                <th width="20%"><?php echo load_lang('module_name'); ?></th>
                <th>Permission</th>
            </tr>
            <?php
                $sqlshow = "SELECT module_id, module_name from base_module";
                $db->query($sqlshow);
                $result = $db->next_record();
                if($result){
                    do {
            ?>
            <tr>
                <td align="center"><?php echo $db->f("module_id"); ?></td>
                <td align="center"><?php echo $db->f("module_name"); ?></td>
                <td><?php echo show_permission_value($db->f("module_id")); ?></td>
            </tr>
            <?php
                $result = $db->next_record();    
                    } while($result);
                }
            ?>
        </table>
    </div>
</body>
</html>