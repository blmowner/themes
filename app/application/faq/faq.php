<?php

include("../../../lib/common.php");
checkLogin();

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
	<script language="JavaScript" type="text/javascript" src="../../../lib/js/tooltip.js"></script>
<style type="text/css">
<!--
.style9 {font-size: 14px; color: #000000; font-weight: bold; }
-->
</style>
<script type="text/javascript">
		$(function() {
			$("#tab1").tabs();
		});
	</script>
</head>

<body>
<form id="form1" name="form1" method="post" action="">
  <ul class="downline-ul">
  	<div align="left"><h3>Category Setup</h3></div>
    <ul>
      <li><a href="add_category.php">Add Category</a></li>
      <li><a href="list_edit_category.php">Update Category</a></li>
      <li><a href="delete_category.php">Delete Category</a></li>
      <li><a href="view_category.php">View Category </a></li>
    </ul>
	<br>
	<div align="left"><h3>FAQ Setup</h3></div>
  	<ul>
		<li><a href="add_faq.php">Add FAQ</a>
		<li><a href="list_edit_faq.php">Update FAQ</a></li>
		<li><a href="delete_faq.php">Delete FAQ</a></li>
		<li><a href="view_faq.php">View FAQ</a></li>
  	</ul>
	
  </ul>
</form>
</body>
</html>
