<?php
    include("../../../lib/common.php");
    checkLogin();
    
    if($_POST['submit'] <> "") {
        $msg = array();
        if(empty($_POST['css'])) $msg[] = "<div class=\"error\"><span>The CSS field couldn't be blank!</span></div>"; else $theCss = $_POST['css'];
        
        if(empty($msg)) {
            $sql_ins = "UPDATE base_config SET config_theme='".$theCss."', config_status='".$_POST['status']."', config_language='".$_POST['lang_option']."', config_charset='".$_POST['charset']."', config_lang_select='".$_POST['lang_select']."'";
            $process = $db->query($sql_ins);
            if($process) {
                tracking($_SESSION['user_id'], load_constant('UPDATE'), 'UPDATE SYS CONFIG');
                $msg[] = "<div class=\"success\"><span>Configuration successfully updated!</span></div>";
                header("refresh:2; url=system_config.php");
            }
        }
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang; ?>" lang="<?php echo $lang; ?>">

<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
	<meta name="author" content="MJMZ" />

	<title>System Configuration</title>
    <link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css ?>" />
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
        <h3>System Configuration</h3>
        <?php
            $sql = "SELECT * FROM base_config";
            $db->query($sql);
            $db->next_record();
        ?>
        <form method="post">
            <table width="100%" cellpadding="3" cellspacing="3" class="thetable">
                <tr>
                    <th>Theme CSS</th>
                    <th>Status (UP/DOWN)</th>
                    <th>Language</th>
                    <th>Charset</th>
                    <th>Language Selection</th>
                </tr>
                <tr align="center">
                    <td><input type="text" name="css" size="20" value="<?php echo $db->f('config_theme') ?>" /></td>
                    <td>
                        <select name="status">
                        <?php
                            if($db->f('config_status') == 1) {
                                echo "<option value=\"1\" selected>Up</option>";
                                echo "<option value=\"0\">Down</option>";
                            } else {
                                echo "<option value=\"0\" selected>Down</option>"; 
                                echo "<option value=\"1\">Up</option>"; 
                            }
                        ?>
                        </select>
                    </td>
                    <td>
                        Primary language use?
                        <select name="lang_option">
                        <?php
                            if($db->f('config_language') == 'en') {
                                echo "<option value=\"en\" selected>English</option>";
                                echo "<option value=\"bm\">Bahasa</option>";
                            } else {
                                echo "<option value=\"bm\" selected>Bahasa</option>"; 
                                echo "<option value=\"en\">English</option>"; 
                            }
                        ?>
                        </select>
                    </td>
                    <td>
                        <select name="charset">
                        <?php
                            if($db->f('config_charset') == 'utf-8') {
                                echo "<option value=\"utf-8\" selected>utf-8</option>";
                                echo "<option value=\"iso-8859-1\">iso-8859-1</option>";
                            } else {
                                echo "<option value=\"iso-8859-1\" selected>iso-8859-1</option>"; 
                                echo "<option value=\"utf-8\">utf-8</option>"; 
                            }
                        ?>
                        </select>
                    </td>
                    <td>
                        Allow user to choose language?
                        <select name="lang_select">
                        <?php
                            if($db->f('config_lang_select') == 1) {
                                echo "<option value=\"1\" selected>Yes</option>";
                                echo "<option value=\"0\">No</option>";
                            } else {
                                echo "<option value=\"0\" selected>No</option>"; 
                                echo "<option value=\"1\">Yes</option>"; 
                            }
                        ?>
                        </select>
                    </td>
                </tr>
            </table><br /><br />
            <center><input type="submit" name="submit" value="<?php echo load_lang('save'); ?>" class="fancy-button-green" /></center>
        </form>
    </div>
</body>
</html>