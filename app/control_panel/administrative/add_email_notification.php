<?php
    include("../../../lib/common.php");
    checkLogin();
    
    if($_POST['submit'] <> "") {
        $msg = array();
        if(empty($_POST['const_term'])) $msg[] = "<div class=\"error\"><span>Please insert constant term</span></div>";
        if(empty($_POST['const_value'])) $msg[] = "<div class=\"error\"><span>Please insert constant value</span></div>";
        
        if(empty($msg)) 
		{
				$sql = "SELECT const_id FROM base_constant ORDER BY const_id DESC";
				$var = $dbf;
				$count_total_result=$var->query($sql);
				$var->next_record();
				$ori=$var->f('const_id');
				$id = $ori+1;
												
				$sql2 = "INSERT INTO base_constant (`const_id`,`const_category`,`const_term`,`const_value`) 
				VALUES ('$id','EMAIL','".$_POST['const_term']."','".$_POST['const_value']."')";
				$db->query($sql2);

                                
                $msg[] = "<div class=\"success\"><span>Email Notification successfully added!</span></div>";
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
        <h3>Add Email Notification </h3>
        <div class="info"><span><?php echo load_lang('compulsary'); ?></span></div><br />
        <form method="post" id="form-set">
		<table>
			<tr>
			  <td width="223">* Email Notification/Constant Term</td>
			  <td width="493"><input type="text" name="const_term" size="40" value="" />
		      eg: EMAIL_FAC_TO_STU</td>
			</tr>
			<tr>
				<td>* Status/Constant Value</td>
			  <td><input type="text" name="const_value" size="40" value="" />
			  eg: Y, N,admin@msu.edu.my</td>
			</tr>
		</table>
				<label>&nbsp;</label><input type="submit" name="submit" value="<?php echo load_lang('save'); ?>" class="fancy-button-green" />
				<input type="reset" name="reset" value="<?php echo load_lang('reset'); ?>" class="fancy-button-grey" />
        </form>
    </div>
</body>
</html>