<?php
    include("../../../lib/common.php");
    checkLogin();
    
    if($_POST['submit'] <> ""){
        
        /* trim all values before save */
        $_POST['module_name'] = trim($_POST['module_name']);
        $_POST['desc'] = trim($_POST['desc']);
        
        $msg = array();
        if(empty($_POST['module_name'])) $msg[] = "<div class=\"error\"><span>Please enter the module name</span></div>";
        if($_POST['parent']=="") $msg[] = "<div class=\"error\"><span>Please select parent module</span></div>";
        
        if(empty($msg)){
            
            if($_POST['parent']==0) $level = 0; else $level = getValue('module_level','base_module','module_id',$_POST['parent']) + 1;
            $sqlSave = "INSERT INTO base_module (parent_id, module_name, module_level, module_description) 
                        VALUES ('".$_POST['parent']."','".$_POST['module_name']."','$level','".$_POST['desc']."')";
            $db->query($sqlSave);
            
            
            tracking($_SESSION['user_id'], load_constant('INSERT'), 'ADD NEW MODULE');
            //$msg[] = "<div class=\"success\"><span>Module successfully added!</span></div>"; if you didn't use the colorbox, uncomment this line
            
            echo "<script>parent.$.fn.colorbox.close();</script>";
        }
    }
    
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang; ?>" lang="<?php echo $lang; ?>">

<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
	<meta name="author" content="MJMZ" />

	<title>Add Module</title>
    <link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
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
        <h3><?php echo load_lang('add_module'); ?></h3>
        <div class="info"><span><?php echo load_lang('compulsary'); ?></span></div><br />
        <form method="post" id="form-set">
            <label class="labeling">* <?php echo load_lang('module_name'); ?></label><input type="text" size="50" name="module_name" value="<?php echo isset($_POST['module_name']) ? $_POST['module_name'] : "" ?>" /><br />
            <label class="labeling">* <?php echo load_lang('parent_menu'); ?></label>
            <select name="parent">
                    <option value="">-- Please select --</option>
                    <option value="0">Top [ parent ]</option>
                    <?php
                        $sqlSlct = "SELECT * FROM base_module ORDER BY parent_id";
                        $db->query($sqlSlct);
                        if($db->next_record()) 
                        {
                            do {
                                
                                switch ($db->f("module_level"))
                                {
                                case 0:
                                  $style = "color:#191919";
                                  break;
                                case 2:
                                  $style = "color:#203360";
                                  $indent = "&nbsp;- ";
                                  break;
                                case 3:
                                  $style = "color:#FF0000";
                                  $indent = "&nbsp;&nbsp;&nbsp;- ";
                                  break;
                                default:
                                  $style = "color:#191919";
                                }
                                    echo "<option style=\"$style\" value=".$db->f("module_id").">".$indent.$db->f("module_name")."</option>";
                                
                            } while($db->next_record());
                        }
                    ?>
                </select><br />
            <label class="labeling"><?php echo load_lang('description'); ?></label>
            <textarea name="desc" cols="40" rows="5"><?php echo isset($_POST['desc']) ? $_POST['desc'] : "" ?></textarea><br />
            <label class="labeling">&nbsp;</label><input type="submit" name="submit" value="<?php echo load_lang('save'); ?>" class="fancy-button-green" />
            <input type="reset" name="reset" value="<?php echo load_lang('reset'); ?>" class="fancy-button-grey" />
        </form>
    </div>
</body>
</html>