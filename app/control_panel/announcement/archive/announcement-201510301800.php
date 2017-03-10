<?php

    include("../../../lib/common.php");
    checkLogin();
	
	$sqlannounce = "SELECT id, title, announcement, insert_by, DATE_FORMAT(insert_date,'%d-%b-%Y, %l:%i %p') AS insert_date, 
	DATE_FORMAT(modify_date,'%d-%b-%Y, %l:%i %p') AS date FROM pg_announcement
	WHERE status = 'A' 
	ORDER BY insert_date DESC";
	$resultA = $dba->query($sqlannounce);
	while($dbf->next_record())
	{
		$idA =$dba>f('id');
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang; ?>" lang="<?php echo $lang; ?>">

<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
	<meta name="author" content="MJMZ" />

	<title>Change Password</title>
    <link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
    <link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
   	<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
	<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
	<link id="bs-css" href="../../../theme/css/button.css" rel="stylesheet" />
	<script src="../../../lib/js/jquery.min2.js"></script>
	<script src="../../../lib/js/jquery.colorbox.js"></script>
	<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
	<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>
	
<script>
$(document).ready(function(){
	$(".title").colorbox({width:"83%", height:"90%", iframe:true,          
	onClosed:function(){ 
	//window.location.reload(true); //uncomment this line if you want to refresh the page when child close
	window.location = window.location;   //reload the page                 
	} }); 
});
</script>
	
<script>
function iframecode(url, w, h) {
	//alert("111");
	window.parent.$.colorbox({href:url, iframe: true, scrolling: true, width: w, height: h});
}
//$(".forgot").colorbox({ bottom: "65px", width:"70%", height:"75%", iframe:true, onClosed:function(){} }); 
</script>

<script>
window.onload = firstLoad;
	function firstLoad() 
	{
		$(".announce_table").load("announcement_detail.php");
	}
	
    $(document).ready(function(){
        setInterval(function() {
            $(".announce_table").load("announcement_detail.php");
        }, 30000);
    });

</script>

</head>

<body>
<?php
if(!empty($msg)) 
{
    foreach($msg as $key) {
       echo $key;
    }
}
?>
    <form method="post" id="form-set">
	<!--<div class="wrapper2" id="try" name="try">
		<!--<div class = "banner_center">-->
	<div><font class="headline1">Announcement</font></div>
		<div class="announce_table" id= "latestData" name= "latestData"></div>

	<br />
	<!--<div><font class="headline1">Notification</font></div>
		<div class="notification_table">
			<div class="one"><font>12 new messages</font>
				<p style="border-bottom: dashed #818489; margin-bottom: 1px; margin-top:2px;"></p>
			</div>
			<div class="two"><font>You can submit Work Completion</font>
				<p style="border-bottom: dashed #818489; margin-bottom: 1px; margin-top:2px;"></p>
			</div>
			<div class="one"><font>Your Defense Proposal result has been decided</font>
				<p style="border-bottom: dashed #818489; margin-bottom: 1px; margin-top:2px;"></p>
			</div>
			<div class="two"><font>Please submit monthly progress report for this month</font>
				<p style="border-bottom: dashed #818489; margin-bottom: 1px; margin-top:2px;"></p>
			</div>
		</div>-->
    </form>

</body>
</html>