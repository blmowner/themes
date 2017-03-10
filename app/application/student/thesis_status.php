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
	<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>
</head>
<body>
  

  <form id="form1" name="form1" method="post" enctype="multipart/form-data">
  <table width="418" border="0">
    <tr>
      <td><strong>Thesis Workflow</strong></td>
      <td><strong>Status</strong></td>
    </tr>
    <tr>
      <td >Thesis Proposal </td>
      <td ><input type="text" name="thesisProposal" size="10" id="thesisProposal" /></td
    ></tr>
    <tr>
      <td>Proposal Defense </td>
      <td><input type="text" name="proposalDefense" size="10" id="proposalDefense" /></td>

    </tr>
    <tr>
      <td>Work Completion </td>
      <td><input type="text" name="programme" size="10" id="programme" /></td>

    </tr>
    <tr>
      <td>Thesis Evaluation/VIVA </td>
      <td><input type="text" name="intake" size="10" id="intake" /></td>

    </tr>
    <tr>
      <td>Final Submission </td>
      <td><input type="text" name="thesisTitle" size="10" id="thesisTitle" /></td>

    </tr>
    <tr>
      <td>Senate Endorsement </td>
      <td><input type="text" name="thesisTitle2" size="10" id="thesisTitle2" /></td>

    </tr>
</table>

  </form>
</body>
</html>