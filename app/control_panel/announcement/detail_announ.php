<?php
    include("../../../lib/common.php");
    checkLogin();
    
	$id = $_GET['id'];

	
	$sqlannounce = "SELECT a.id AS trackId, a.action_by,  DATE_FORMAT(a.action_date,'%d-%b-%Y %l:%i %p') as action_date, a.query_track, b.title, b.id AS announcementId, 
	c.description, b.announcement
	FROM pg_announcement_tracking a
	LEFT JOIN pg_announcement b ON (b.id=a.announcement_id)
	LEFT JOIN ref_announcement c ON (c.id=a.action)
	WHERE a.id = '$id'
	ORDER BY b.id DESC , a.action_date";
	$resultA = $dbf->query($sqlannounce);
	$dbf->next_record();
	
	$trackId =$dbf->f('trackId');
	$actionBy =$dbf->f('action_by');
	$queryTrack =$dbf->f('query_track');
	$title = $dbf->f('title');
	$announcementId = $dbf->f('announcementId');
	$description = $dbf->f('description');
	$action_date = $dbf->f('action_date');
	$announcement = $dbf->f('announcement');
	
function runnum2($column_name, $tblname) 
{ 
    global $db_klas2;
    
    $run_start = "001";
    
    $sql_slct_max = "SELECT MAX(SUBSTR($column_name,2,11)) AS run_id FROM $tblname";
    $sql_slct = $db_klas2;
    $sql_slct->query($sql_slct_max);
    $sql_slct->next_record();

    if($sql_slct->num_rows($sql_slct_max)== 0 || $sql_slct->f("run_id")==NULL) 
	{
        $run_id = date("Ymd").$run_start;
    } 
	else 
	{
        $todate = date("Ymd");
        
        if($todate > substr($sql_slct->f("run_id"),0,8)) 
		{
            $run_id = $todate.$run_start;
        } 
		else 
		{
            $run_id = $sql_slct->f("run_id") + 1; 
        }
    }
    return $run_id;
}

	
    //$level = 0; /* set the menu level = 0 */

?>
<style>
h3 {
font-family:Verdana;
font-size:14px;
background: rgba(105, 162, 255, 0.7);
width: 100%;
}
<!--
.style2 {color: #000000}
-->


.close-btn { 
    border: 2px solid #c2c2c2;
    position: relative;
    padding: 1px 5px;
    bottom: 5px;
    background-color: #605F61;
    left: 910px; 
    border-radius: 20px;
}

.close-btn a {
    font-size: 15px;
    font-weight: bold;
    color: white;
    text-decoration: none;
}
.idd {
font-family:Verdana;
font-size:12px;
}

</style>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang; ?>" lang="<?php echo $lang; ?>">

<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
    <link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
    <link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
	<script src="../../../lib/js/jquery.min2.js"></script>
	<script src="../../../lib/js/jquery.colorbox.js"></script>
	<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
	
	<link id="bs-css" href="../../../theme/css/button.css" rel="stylesheet" />
	<link id="bs-css" href="../administrative/css/button.css" rel="stylesheet" />

	<link href='../../../theme/UI/css/jquery.iphone.toggle.css' rel='stylesheet'>
	<script src="../../../theme/UI/bower_components/jquery/jquery.min.js"></script>

	<!--<script src="../../../lib/js/jquery.min2.js"></script>
	<script src="../../../lib/js/jquery.colorbox.js"></script>-->

	<!-- external javascript -->
	
	<script src="../../theme/UI/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
	
	<!-- library for cookie management -->
	<script src="../../theme/UI/js/jquery.cookie.js"></script>
	<!-- calender plugin -->
	<script src='../../theme/UI/bower_components/moment/min/moment.min.js'></script>
	<script src='administrative/UI/bower_components/fullcalendar/dist/fullcalendar.min.js'></script>
	<!-- data table plugin -->
	<script src='../../theme/UI/js/jquery.dataTables.min.js'></script>
	
	<!-- select or dropdown enhancer -->
	<script src="../../theme/UI/bower_components/chosen/chosen.jquery.min.js"></script>
	<!-- plugin for gallery image view -->
	<script src="../../theme/UI/bower_components/colorbox/jquery.colorbox-min.js"></script>
	<!-- notification plugin -->
	<script src="../../theme/UI/js/jquery.noty.js"></script>
	<!-- library for making tables responsive -->
	<script src="../../theme/UI/bower_components/responsive-tables/responsive-tables.js"></script>
	<!-- tour plugin -->
	<script src="../../theme/UI/bower_components/bootstrap-tour/build/js/bootstrap-tour.min.js"></script>
	<!-- star rating plugin -->
	<script src="../../theme/UI/js/jquery.raty.min.js"></script>
	<!-- for iOS style toggle switch -->
	<script src="../../theme/UI/js/jquery.iphone.toggle.js"></script>
	<!-- autogrowing textarea plugin -->
	<script src="../../theme/UI/js/jquery.autogrow-textarea.js"></script>
	<!-- multiple file upload plugin -->
	<script src="../../theme/UI/js/jquery.uploadify-3.1.min.js"></script>
	<!-- history.js for cross-browser state change on ajax -->
	<script src="../../theme/UI/js/jquery.history.js"></script>
	<!-- application script for Charisma demo -->
	<script src="../../theme/UI/js/charisma.js"></script>


	<meta name="author" content="NR" />

	<title>Edit Menu</title>
    <link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
</head>

<body>
	<!--<span class="close-btn"><a href="#" onClick="javascript: parent.$.fn.colorbox.close();">X</a></span>-->

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
<h3>Detail Announcement </h3>
<?php
if(!empty($msg)) {
    foreach($msg as $err) {
        echo $err;
    }
}
?>
<form method="post" id="form-set">
<table class = "idd">
	<tr>
		<td style="background-color: rgba(105, 162, 255, 0.7);">Track Id</td>
		<td style="background-color:#DBD9D9;"><?=$trackId;?></td> 
	</tr>
	<tr>
		<td style="background-color: rgba(105, 162, 255, 0.7);">Action By</td> 
		<td style="background-color:#DBD9D9;"><?=$actionBy?></td>	
	</tr>
	<tr>
		<td style="background-color: rgba(105, 162, 255, 0.7);">Action Taken</td> <td style="background-color:#DBD9D9;"><?=$description?></td>
	</tr>	
	<tr>
		<td style="background-color: rgba(105, 162, 255, 0.7);">Action Date</td> <td style="background-color:#DBD9D9;"><?=$action_date?></td>
	</tr>

	<!--$announcement-->

		<tr>
    		<td style="background-color: rgba(105, 162, 255, 0.7);">Title</td>
		<td style="background-color:#DBD9D9;" align="left"><input type="text" size="50" name="title" id="title" value="<?=$title?>" readonly="readonly"/></td>		</tr>
		<tr>
			<td style="background-color: rgba(105, 162, 255, 0.7);">Query Tracking </td>
    		<td style="background-color:#DBD9D9;"><textarea style="width: 600px; height: 150px;" name="announ" cols="30" rows="3" id="announ" readonly="readonly"><?=$queryTrack?></textarea></td>
		</tr>
		<tr>
			<td colspan="2" align="left"><input type="submit" id="close" name="close" onclick="javascript: parent.$.fn.colorbox.close();" value = "Close" /></td>
		</tr>
	</table>
</form>
</div>
</body>
</html>