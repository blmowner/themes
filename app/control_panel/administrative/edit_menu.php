<?php
    include("../../../lib/common.php");
    checkLogin();
    
    $menuID = $_GET['mid'];
    //$level = 0; /* set the menu level = 0 */
    

    if($_POST['update'] <> ""){
        
        $msg = array();
        if(empty($_POST['title'])) $msg[] = "<div class=\"error\">Title field couldn't be empty!</div>"; else $title = $_POST['title'];
        if(empty($_POST['link'])) $msg[] = "<div class=\"error\">Hyperlink field couldn't be empty!</div>"; else $link = $_POST['link'];
        
        if(empty($msg)) 
		{
           $sqlUpd = "UPDATE base_language_text SET text='$title' WHERE variable='$menuID' AND language_code='".$_GET['langcd']."'";
            $process = $db->query($sqlUpd);
			
            if($process) 
			{       
                if($_POST['menu'] == 0) 
				{
                    $parentID = "0";
                    $level = "1";
                } 
                
                if($_POST['menu'] == $menuID ) {
                    $parentID = getValue_local('parent_id','base_user_sys_menu', 'menu_id',$_POST['menu']);
                    $level = getValue_local('menu_level','base_user_sys_menu', 'menu_id',$_POST['menu']);
                } 
                
                if($_POST['menu'] != $menuID ) {
                   $parentID = $_POST['menu'];
                    $level = getValue_local('menu_level','base_user_sys_menu', 'menu_id',$parentID) + 1;
                }
                
                
              $sqlUpd = "UPDATE base_user_sys_menu SET parent_id='$parentID', 
                          menu_link='$link', menu_level='$level' WHERE menu_id='$menuID'";
                $db->query($sqlUpd);
                tracking($_SESSION['user_id'], load_constant('UPDATE'), "EDIT MENU");
                                
               //header("location:menu_manager.php");
                echo "<script>parent.$.fn.colorbox.close();</script>";
            }
        }
        
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang; ?>" lang="<?php echo $lang; ?>">

<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
	<meta name="author" content="MJMZ" />

	<title>Edit Menu</title>
    <link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
</head>

<body>
<?php
   $sql = "SELECT b.text, a.menu_level, a.menu_link FROM base_user_sys_menu a
            LEFT JOIN base_language_text b ON (a.menu_id = b.variable)
            WHERE a.menu_id=$menuID AND b.language_code='".$_GET['langcd']."'";
   $result = $db->query($sql);
   $rows = $db->fetchArray($result);
   $menu_title = $rows['text'];
   $menu_link = $rows['menu_link'];
   $menu_level = $rows['menu_level'];
   
   $db->free();
        
?>
<div class="padding-5 margin-5 outer">
<h3>Edit Menu</h3>
<?php
if(!empty($msg)) {
    foreach($msg as $err) {
        echo $err;
    }
}
?>
<form method="post" id="form-set">
    <label class="labeling"><?php echo load_lang('menu_title'); ?></label><input type="text" size="50" name="title" value="<?php echo $menu_title; ?>" /><br />
    <label class="labeling"><?php echo load_lang('hyperlink'); ?></label><input type="text" size="50" name="link" value="<?php echo $menu_link; ?>" /><br />
    <label class="labeling"><?php echo load_lang('parent_menu'); ?></label>
    <select name="menu">
        <?php
            echo "<option value=\"0\">Top</option>";
            $sql_menu = "SELECT * FROM base_user_sys_menu ORDER BY parent_id";
            $db->query($sql_menu);
                if($db->next_record()) {
                    do {
                        
                        switch ($db->f("menu_level"))
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
                        if($db->f("menu_id")==$_GET['mid']) {
                        echo "<option selected style=\"$style\" value=".$db->f("menu_id").">".$indent.$db->f("menu_module")."</option>";
                        } else {
                            echo "<option style=\"$style\" value=".$db->f("menu_id").">".$indent.$db->f("menu_module")."</option>";
                        }
                        
                    } while($db->next_record());
                }
        ?>
    </select><br /><br />
    <label class="labeling">&nbsp;</label><input type="submit" value="<?php echo load_lang('save'); ?>" name="update" class="fancy-button-green" />
</form>
</div>
</body>
</html>