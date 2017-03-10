<?php
    include("../../../lib/common.php");
    checkLogin();
    
	$id = $_GET['id'];

	
	$sql = "SELECT * FROM base_constant
	WHERE const_id = '$id'";
    $dbf->query($sql);
	$dbf->next_record();
	$value1 = $dbf->f('const_value');
	$term2 = $dbf->f('const_term');

	
    //$level = 0; /* set the menu level = 0 */


    if($_POST['update'] <> ""){
        
        /*$msg = array();
        if(empty($_POST['value'])) $msg[] = "<div class=\"error\">Text field couldn't be empty!</div>"; else $value = $_POST['value'];*/
		$onoff = $_POST['onoff'];
		$email = $_POST['email'];
		$term = $_POST['term'];
		$onhid = $_POST['onoffhidden'];
		$lang;

		if($onoff <> ""){
		
			$sqlUpd = "UPDATE base_constant SET const_value='Y' 
			WHERE const_id='$onoff'";
			$process = $db->query($sqlUpd);
			//echo "Value = $onoff <br> Update value = Y";
			
		}
		else if($onoff == ""){
			
			$sqlUpd = "UPDATE base_constant SET const_value='N' 
			WHERE const_id='$onhid'";
			$process = $db->query($sqlUpd);
			
			//echo "Hidden value = $onhid <br> Update value = N";
		}
		if($email <> "")
		{
			$sqlUpd = "UPDATE base_constant SET const_value='$email' 
			WHERE const_id='$onhid'";
			$process = $db->query($sqlUpd);

		}       
		echo "<script>parent.$.fn.colorbox.close();</script>";    
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang; ?>" lang="<?php echo $lang; ?>">

<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
    <link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
    <link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
	<script src="../../../lib/js/jquery.min2.js"></script>
	<script src="../../../lib/js/jquery.colorbox.js"></script>
	
	<link id="bs-css" href="../../../theme/css/button.css" rel="stylesheet" />
	<link id="bs-css" href="css/button.css" rel="stylesheet" />

	<link href='../../../theme/UI/css/jquery.iphone.toggle.css' rel='stylesheet'>
	<script src="../../../theme/UI/bower_components/jquery/jquery.min.js"></script>

	<!--<script src="../../../lib/js/jquery.min2.js"></script>
	<script src="../../../lib/js/jquery.colorbox.js"></script>-->

	<!-- external javascript -->
	
	<script src="../../../theme/UI/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
	
	<!-- library for cookie management -->
	<script src="../../../theme/UI/js/jquery.cookie.js"></script>
	<!-- calender plugin -->
	<script src='../../../theme/UI/bower_components/moment/min/moment.min.js'></script>
	<script src='UI/bower_components/fullcalendar/dist/fullcalendar.min.js'></script>
	<!-- data table plugin -->
	<script src='../../../theme/UI/js/jquery.dataTables.min.js'></script>
	
	<!-- select or dropdown enhancer -->
	<script src="../../../theme/UI/bower_components/chosen/chosen.jquery.min.js"></script>
	<!-- plugin for gallery image view -->
	<script src="../../../theme/UI/bower_components/colorbox/jquery.colorbox-min.js"></script>
	<!-- notification plugin -->
	<script src="../../../theme/UI/js/jquery.noty.js"></script>
	<!-- library for making tables responsive -->
	<script src="../../../theme/UI/bower_components/responsive-tables/responsive-tables.js"></script>
	<!-- tour plugin -->
	<script src="../../../theme/UI/bower_components/bootstrap-tour/build/js/bootstrap-tour.min.js"></script>
	<!-- star rating plugin -->
	<script src="../../../theme/UI/js/jquery.raty.min.js"></script>
	<!-- for iOS style toggle switch -->
	<script src="../../../theme/UI/js/jquery.iphone.toggle.js"></script>
	<!-- autogrowing textarea plugin -->
	<script src="../../../theme/UI/js/jquery.autogrow-textarea.js"></script>
	<!-- multiple file upload plugin -->
	<script src="../../../theme/UI/js/jquery.uploadify-3.1.min.js"></script>
	<!-- history.js for cross-browser state change on ajax -->
	<script src="../../../theme/UI/js/jquery.history.js"></script>
	<!-- application script for Charisma demo -->
	<script src="../../../theme/UI/js/charisma.js"></script>


	<meta name="author" content="NR" />

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
<h3>Edit Email Notification </h3>
<?php
if(!empty($msg)) {
    foreach($msg as $err) {
        echo $err;
    }
}
?>
<form method="post" id="form-set">
	<table class="thetable">
		<tr>
    		<th><label class="labeling"><?=$term2?></label></td>
			<th><input type="hidden" size="50" name="term" value="<?=$term2?>" /></td>
		</tr>
		<tr>
			<td><label class="labeling">Constant Value</label></td>
    		<td>
	<?
	if($value1 == "Y") {
					
		echo "<input name = \"onoff\" id=\"onoff\" data-no-uniform=\"true\" value = ".$id." checked=\"checked\" type=\"checkbox\" class=\"iphone-toggle\">";
		echo "<input name = \"onoffhidden\" id=\"onoffhidden\" value = ".$id." type=\"hidden\">";	

	}
	else if ($value1 == "N"){
		echo "<input name = \"onoffhidden\" id=\"onoffhidden\" value = ".$id." type=\"hidden\">";	
		echo "<input name = \"onoff\" id=\"onoff\" data-no-uniform=\"true\" value = ".$id." type=\"checkbox\" class=\"iphone-toggle\">";										
	}
	else {
		echo "<textarea style=\"width: 200px;\" name = \"email\" id=\"onoffhidden\" type=\"text\">".$value1."</textarea>";
		echo "<input name = \"onoffhidden\" id=\"onoffhidden\" value = ".$id." type=\"hidden\">";
	}
		//echo "<input type=\"text\" size=\"50\" name=\"value\" value=".$value1." />";
	?>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="right"><input class="btn btn-primary btn-xs"  type="submit" value="Update" name="update" /></td>
		</tr>
	</table>
</form>
</div>
</body>
</html>