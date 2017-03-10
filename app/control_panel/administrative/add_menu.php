<?php
    include("../../../lib/common.php");
    checkLogin();
    
    if($_POST['submit'] <> "") {
        $msg = array();
        if(empty($_POST['title_en'])) $msg[] = "<div class=\"error\"><span>Please insert menu title in english translation</span></div>"; else $title_en = $_POST['title_en'];
        if(empty($_POST['title_bm'])) $msg[] = "<div class=\"error\"><span>Please insert menu title in bahasa translation</span></div>"; else $title_bm = $_POST['title_bm'];
        if(empty($_POST['term'])) $msg[] = "<div class=\"error\"><span>Please enter the menu alias</span></div>"; else $term = $_POST['term'];
        if($_POST['parent']=="") $msg[] = "<div class=\"error\"><span>Please select parent from the list</span></div>"; else $parent = $_POST['parent'];
        if(empty($_POST['link'])) $link = "#"; else $link = $_POST['link'];
        
        if(empty($msg)) {
                        
            if($parent=="0") $level = 1; else $level = getValue_local('menu_level','base_user_sys_menu','menu_id',$parent) + 1;
            
            $sql = "INSERT INTO base_user_sys_menu (parent_id, menu_module, menu_link, menu_level)
                    VALUES('$parent','$title_en','$link','$level')";
            $process = $db->query($sql);
            if($process) {
                $menuID = mysql_insert_id();
                $title_menu = array($title_en=>$_POST['en_lang_cd'] , $title_bm=>$_POST['bm_lang_cd']);
                            
                foreach($title_menu as $key=>$a) {
                    $sql2 = "INSERT INTO base_language_text (language_code, variable, term, text, revised_date, context) VALUES ('$a','$menuID','$term','$key', now(), 'BASE_MENU')";
                    $db->query($sql2);
                }
                
                tracking($_SESSION['user_id'], load_constant('INSERT'), 'ADD NEW MENU');
                
                $msg[] = "<div class=\"success\"><span>Menu successfully added!</span></div>";
                //header("refresh:2; url=menu_manager.php");
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

	<title>Add Menu</title>
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
        <h3>Add Menu</h3>
        <div class="info"><span><?php echo load_lang('compulsary'); ?></span></div><br />
        <form method="post" id="form-set">
            <label class="labeling">* <?php echo load_lang('menu_title'); ?></label><input type="text" name="title_en" size="40" value="<?php echo isset($title_en) ? $title_en : ""  ?>" /> <i><?php echo load_lang('menu_translation_en'); ?></i><input type="hidden" value="en" name="en_lang_cd" /><br />
            <label class="labeling">&nbsp;</label><input type="text" name="title_bm" size="40" value="<?php echo isset($title_bm) ? $title_bm : ""  ?>" /> <i><?php echo load_lang('menu_translation_bm'); ?></i><input type="hidden" value="bm" name="bm_lang_cd" /><br />
            <label class="labeling">* Alias</label><input type="text" name="term" size="30" value="<?php echo isset($term) ? $term : ""  ?>" /><i>e.g: control_panel, main_page, etc.</i><br />
            <label class="labeling">&nbsp;<?php echo load_lang('hyperlink'); ?></label><input type="text" name="link" size="70" value="<?php echo isset($link) ? $link : ""  ?>" /><br />
            <label class="labeling">* <?php echo load_lang('parent_menu'); ?></label>
                <select name="parent">
                    <option value="">-- Please select --</option>
                    <option value="0">Top [ parent ]</option>
                    <?php
                        $sqlSlct = "SELECT * FROM base_user_sys_menu ORDER BY parent_id";
                        $db->query($sqlSlct);
                        if($db->next_record()) 
                        {
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
            <label>&nbsp;</label><input type="submit" name="submit" value="<?php echo load_lang('save'); ?>" class="fancy-button-green" />
            <input type="reset" name="reset" value="<?php echo load_lang('reset'); ?>" class="fancy-button-grey" />
        </form>
    </div>
</body>
</html>