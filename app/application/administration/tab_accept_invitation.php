<?php

include("../../../lib/common.php");
checkLogin();
session_start();
$user_id=$_SESSION['user_id'];
?>

	
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
	<link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
	<link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
   	<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />

	<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
	<script src="../../../lib/js/jquery.min2.js"></script>
    <script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
	<script language="JavaScript" src="../../../lib/js/windowopen.js"></script>
	<!--<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>-->
	<script language="JavaScript" type="text/javascript" src="../../../lib/js/tooltip.js"></script>
	<script type="text/javascript">
		$(function() {
			$("#tab1").tabs();
		});
	</script>
</head>
<body>

  <form id="form1" name="form1" method="post" enctype="multipart/form-data">
 	<div id="tab1" style="width:95%">
		
		<ul>
            <li><a href="#tabs-1">Supervisor/Co-Supervisor</a></li>
			<li><a href="#tabs-2">Evaluation Panel</a></li>			
        </ul>
				
		<div id="tabs-1">	
			<iframe id="iframesv" src="../supervisor/accept_invitation.php" frameborder="0" style="height:350px;width:100%"></iframe>		
		</div>
		
		<div id="tabs-2">
			<iframe id="iframeev" src="../defense/accept_invitation.php" frameborder="0" style="height:350px;width:100%"></iframe>
		</div>
		
		
	</div>
 </form>
</body>
</html>