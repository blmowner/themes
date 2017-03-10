<?php
    include("../../../lib/common.php");
    checkLogin();
    
    $menuID = $_GET['mid'];
	$lang = $_GET['lang'];
	$term = $_GET['term'];

	
	$sql = "SELECT * FROM base_language_text
	WHERE language_code = '$lang'
	AND term = '$term'";
    $dbf->query($sql);
	$dbf->next_record();
	$variable1 = $dbf->f('variable');
	$text = $dbf->f('text');

	
    //$level = 0; /* set the menu level = 0 */


    if($_POST['update'] <> ""){
        
        $msg = array();
        if(empty($_POST['title'])) $msg[] = "<div class=\"error\">Text field couldn't be empty!</div>"; else $title = $_POST['title'];
        if($_POST['dropvariable'] == '') $msg[] = "<div class=\"error\">Variable field couldn't be empty!</div>"; else $link = $_POST['link'];
        $title = $_POST['title'];
		$variable2 = $_POST['dropvariable'];
		$lang;

        if(empty($msg)) 
		{
            $sqlUpd = "UPDATE base_language_text SET text='$title', variable = '$variable2' 
			WHERE term='$term' 
			AND language_code='$lang'";
            $process = $db->query($sqlUpd);
			if($process)
			{
			    $sqlUpd = "UPDATE base_language_text SET variable = '$variable2' 
				WHERE term='$term'";
				$process = $db->query($sqlUpd);

			}
            echo "<script>parent.$.fn.colorbox.close();</script>";
			$msg[] = "<div class=\"success\">Success</div>";
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
   /*$sql = "SELECT b.text, a.menu_level, a.menu_link FROM base_user_sys_menu a
            LEFT JOIN base_language_text b ON (a.menu_id = b.variable)
            WHERE a.menu_id=$menuID AND b.language_code='".$_GET['langcd']."'";
   $result = $db->query($sql);
   $rows = $db->fetchArray($result);
   $menu_title = $rows['text'];
   $menu_link = $rows['menu_link'];
   $menu_level = $rows['menu_level'];
   
   $db->free();*/
        
?>
<div class="padding-5 margin-5 outer">
<h3>Edit Base Language </h3>
<?php
if(!empty($msg)) {
    foreach($msg as $err) {
        echo $err;
    }
}
?>
<form method="post" id="form-set">
    <label class="labeling"><?php echo load_lang('base_title'); ?></label>
	<input type="text" size="50" name="title" value="<?php echo $text; ?>" /><br />
    <label class="labeling"><?php echo load_lang('variable'); ?></label>
	
	<select name = "dropvariable">
	<option value = "">--Please Select--</option>
	<?
		$sql = "SELECT * FROM base_variable";
		$var = $db;
		$var->query($sql);
		$var->next_record();
		
		do{
			$variable = $var->f('variable');
			if($variable == $variable1)
			{ ?>
				<option value="<?=$variable?>" selected="selected" /><?=$variable ?></option>
				
				
			<? }
			else
			{?>
				<option value="<?=$variable; ?>" /><?=$variable ?></option>
				
			<? }
		}while($db->next_record()); 
	?>
	</select>
    <br /><br />
    <label class="labeling">&nbsp;</label><input type="submit" value="<?php echo load_lang('save'); ?>" name="update" class="fancy-button-green" />
</form>
</div>
</body>
</html>