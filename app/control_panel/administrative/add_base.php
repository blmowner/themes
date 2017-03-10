<?php
    include("../../../lib/common.php");
    checkLogin();
    
    if($_POST['submit'] <> "") {
        $msg = array();
        if(empty($_POST['title_en'])) $msg[] = "<div class=\"error\"><span>Please insert base title in english translation</span></div>"; else $title_en = $_POST['title_en'];
        if(empty($_POST['title_bm'])) $msg[] = "<div class=\"error\"><span>Please insert base title in bahasa translation</span></div>"; else $title_bm = $_POST['title_bm'];
        if(empty($_POST['term'])) $msg[] = "<div class=\"error\"><span>Please enter the term</span></div>"; else $term = $_POST['term'];
        if($_POST['variable']=="") $msg[] = "<div class=\"error\"><span>Please select variable from the list</span></div>"; else $variable = $_POST['variable'];
        
        if(empty($msg)) 
		{
                                    
                $title_menu = array($title_en=>$_POST['en_lang_cd'] , $title_bm=>$_POST['bm_lang_cd']);
                            
                foreach($title_menu as $key=>$a) 
				{
                    $sql2 = "INSERT INTO base_language_text (language_code, variable, term, text, revised_date, context) 
					VALUES ('$a','$variable','$term','$key', now(), 'BASE_MENU')";
                    $db->query($sql2);
                }
                
                tracking($_SESSION['user_id'], load_constant('INSERT'), 'ADD NEW MENU');
                
                $msg[] = "<div class=\"success\"><span>Base successfully added!</span></div>";
                //header("refresh:2; url=menu_manager.php");
                echo "<script>parent.$.fn.colorbox.close();</script>";
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
        <h3>Add Base Language </h3>
        <div class="info"><span><?php echo load_lang('compulsary'); ?></span></div><br />
        <form method="post" id="form-set">
            <label class="labeling">* <?php echo load_lang('base_title'); ?></label><input type="text" name="title_en" size="40" value="<?php echo isset($title_en) ? $title_en : ""  ?>" /> <i><?php echo load_lang('menu_translation_en'); ?></i><input type="hidden" value="en" name="en_lang_cd" /><br />
            <label class="labeling">&nbsp;</label><input type="text" name="title_bm" size="40" value="<?php echo isset($title_bm) ? $title_bm : ""  ?>" /> <i><?php echo load_lang('menu_translation_bm'); ?></i><input type="hidden" value="bm" name="bm_lang_cd" /><br />
            <label class="labeling">* Alias</label>
            <input type="text" name="term" size="30" value="<?php echo isset($term) ? $term : ""  ?>" />
            <i>e.g: control_panel, main_page, etc.</i><br />
           
            <label class="labeling">* <?php echo load_lang('variable'); ?></label>
                <select name="variable">
				<option value = "">--Please Select--</option>
				<?
					$sql = "SELECT * FROM base_variable";
					$var = $dbf;
					$var->query($sql);
					$var->next_record();
					
					do{
						$variable = $var->f('variable');
				?>
						<option value="<?=$variable; ?>" /><?=$variable ?></option>
							
				<? 
					}while($var->next_record()); 
				?>
                </select><br /><br />
            <label>&nbsp;</label><input type="submit" name="submit" value="<?php echo load_lang('save'); ?>" class="fancy-button-green" />
            <input type="reset" name="reset" value="<?php echo load_lang('reset'); ?>" class="fancy-button-grey" />
        </form>
    </div>
</body>
</html>