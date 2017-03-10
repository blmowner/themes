<?php

include("../../../lib/common.php");
checkLogin();

	?>

	

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
    <link rel="stylesheet" type="text/css" href="../../theme/css/<?php echo $css; ?>" />
	<link rel="stylesheet" type="text/css" href="../../theme/css/colorbox.css" media="screen" />
   	<link rel="stylesheet" href="../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />    
    <script src="../../lib/js/jquery.min2.js"></script>
	<script src="../../lib/js/jquery.colorbox.js"></script>
	<script src="../../lib/js/jquery.mask_input-1.3.js"></script>
    <script type="text/javascript" src="../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
    <script src="../../lib/js/datePicker/jquery.ui.core.js"></script>
    <script src="../../lib/js/datePicker/jquery.ui.widget.js"></script>
    <script src="../../lib/js/datePicker/jquery.ui.datepicker.js"></script>
</head>
<body>
  

  <form id="form1" name="form1" method="post" enctype="multipart/form-data">
  <table width="501" border="0">
    <tr>
      <td width="153">Student Name </td>
      <td width="300"><input type="text" name="studentName" size="50" id="studentName" /></td>
    </tr>
    <tr>
      <td>Identity Card/Passport No.</td>
      <td><input type="text" name="studentName" size="50" id="studentName" /></td>
    </tr>
    <tr>
      <td>Gender</td>
      <td><input type="text" name="programme" size="50" id="programme" /></td>
    </tr>
    <tr>
      <td>Place of Birth</td>
      <td><input type="text" name="intake" size="50" id="intake" /></td>
    </tr>
    <tr>
      <td>Nationality</td>
      <td><input type="text" name="thesisTitle" size="50" id="thesisTitle" /></td>
    </tr>
    <tr>
      <td>Race</td>
      <td><input type="text" name="thesisTitle2" size="50" id="thesisTitle2" /></td>
    </tr>
    <tr>
      <td>Marital Status</td>
      <td><input type="text" name="thesisTitle3" size="50" id="thesisTitle3" /></td>
    </tr>
    <tr>
      <td>Name of Spouse</td>
      <td><input type="text" name="thesisTitle4" size="50" id="thesisTitle4" /></td>
    </tr>
    <tr>
      <td>Number of Children</td>
      <td><input type="text" name="thesisTitle5" size="50" id="thesisTitle5" /></td>
    </tr>
    <tr>
      <td>Permanent Address in home country</td>
      <td><input type="text" name="thesisTitle6" size="50" id="thesisTitle6" /></td>
    </tr>
    <tr>
      <td>Corresspondence Address</td>
      <td><input type="text" name="thesisTitle7" size="50" id="thesisTitle7" /></td>
    </tr>
    <tr>
      <td>Telephone</td>
      <td><input type="text" name="thesisTitle8" size="50" id="thesisTitle8" /></td>
    </tr>
    <tr>
      <td>Email</td>
      <td><input type="text" name="thesisTitle9" size="50" id="thesisTitle9" /></td>
    </tr>
</table>

  </form>
</body>
</html>