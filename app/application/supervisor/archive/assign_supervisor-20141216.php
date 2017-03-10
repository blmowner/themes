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
  <p><strong>Search Student </strong></p>
  <table border="0">
    <tr>
      <td >By Student Name </td>
      <td ><input type="text" name="studentName" size="50" id="studentName" /></td>
    </tr>
    <tr>
      <td>By Matrix Number </td>
      <td><input type="text" name="matrixNumber" size="50" id="matrixNumber" /></td>
    </tr>
    <tr>
      <td>By Programme</td>
      <td><input type="text" name="programme" size="50" id="programme" /></td>
    </tr>
    <tr>
      <td>By Intake</td>
      <td><input type="text" name="intake" size="50" id="intake" /></td>
    </tr>
    <tr>
      <td>By Thesis ID </td>
      <td><input type="text" name="thesisID" size="50" id="thesisID" /></td>
    </tr>
    <tr>
      <td>By Thesis Title </td>
      <td><input type="text" name="thesisTitle" size="50" id="thesisTitle" /></td>
    </tr>
</table>
<p></label><input type="submit" name="submit" value="Search" /></p>
</form>
<br />
<form id="form2" name="form2" method="post" enctype="multipart/form-data">
<p><strong>List of Student </strong></p>
  <table border="0">
    <tr>
      <td>Please Tick</td>
	  <td>No.</td>
	  <td>Student Name</td>
	  <td>Matrix Number</td>
	  <td>Programme</td>
	  <td>Intake</td>
	  <td>Thesis ID </td>
	  <td>Thesis Title </td>      
    </tr>
    <tr>
	  <td><input name="checkbox" type="checkbox" value="" /></td>
	  <td>1.</td>
      <td ><input type="text" name="studentName" size="30" id="studentName" value="Bui Ngoc Dung" /></td>
	  <td><input type="text" name="matrixNumber" size="30" id="matrixNumber" value="1001"/></td>
	  <td><textarea name="programme" cols="30" id="programme">Doctor of Philosophy (Information and Communication Technology)</textarea></td>
	  <td><input type="text" name="intake" size="30" id="intake" value="201405"/></td>
	  <td><input type="text" name="thesisID" size="30" id="thesisID" value="201405001" /></td>
	  <td><textarea name="thesisTitle" cols="30" id="thesisTitle">Model-Free Analysis of Single and Multiple fMRI Time Series Based on Spatial Synchronization</textarea></td>
    </tr>   
	<tr>
	  <td><input name="checkbox" type="checkbox" value="" /></td>
	  <td>2.</td>
	  <td ><input type="text" name="studentName" size="30" id="studentName" /></td>
	  <td><input type="text" name="matrixNumber" size="30" id="matrixNumber" /></td>
	  <td><textarea name="programme" cols="30" id="programme"></textarea></td>
	  <td><input type="text" name="intake" size="30" id="intake" /></td>
	  <td><input type="text" name="thesisID" size="30" id="thesisID" /></td>
	  <td><textarea name="thesisTitle" cols="30" id="thesisTitle"></textarea></td>
    </tr>   
	<tr>
	  <td><input name="checkbox" type="checkbox" value="" /></td>
	  <td>3.</td>
      <td ><input type="text" name="studentName" size="30" id="studentName" /></td>
	  <td><input type="text" name="matrixNumber" size="30" id="matrixNumber" /></td>
	  <td><textarea name="programme" cols="30" id="programme"></textarea></td>
	  <td><input type="text" name="intake" size="30" id="intake" /></td>
	  <td><input type="text" name="thesisID" size="30" id="thesisID" /></td>
	  <td><textarea name="thesisTitle" cols="30" id="thesisTitle"></textarea></td>
    </tr>   
</table>
<br />
<p><strong>Supervisor/Co-Supervisor Invitation</strong></p>
<form id="form1" name="form1" method="post" enctype="multipart/form-data">
  <p><strong>Search Supervisor </strong></p>
  <table border="0">
    <tr>
      <td >By Supervisor Name </td>
      <td ><input type="text" name="studentName" size="50" id="studentName" /></td>
    </tr>
    <tr>
      <td>By Staff ID </td>
      <td><input type="text" name="matrixNumber" size="50" id="matrixNumber" /></td>
    </tr>
    <tr>
      <td>By Faculty</td>
      <td><input type="text" name="programme" size="50" id="programme" /></td>
    </tr>
</table>
<p></label><input type="submit" name="submit" value="Search" /></p>
</form>
<table border="0">
    <tr>
      <td>Please Tick</td>
      <td>No</td>	  
      <td>Supervisor Name </td>
      <td>Staff ID </td>
	  <td>Faculty </td>
	  <td>Contact No. </td>	  
      <td>Email ID</td>
    </tr>
	
    <tr>
      <td><input name="" type="checkbox" value="" /></td>  
	  <td>1</td>
      <td><input type="text" name="supervisorName1" size="30" id="supervisorName" /></td>
	  <td><input type="text" name="staffID" size="50" id="staffID" /></td>
	  <td><input type="text" name="faculty" size="50" id="faculty" /></td>
	  <td><input type="text" name="contactNumber" size="15" id="contactNumber" /></td>
      <td><input type="text" name="emailID" size="20" id="emailID" /></td>
    </tr>
    <tr>
       <td><input name="" type="checkbox" value="" /></td>
	   <td>2</td>
      <td><input type="text" name="supervisorName1" size="30" id="supervisorName" /></td>
	  <td><input type="text" name="staffID" size="50" id="staffID" /></td>
	  <td><input type="text" name="faculty" size="50" id="faculty" /></td>
	  <td><input type="text" name="contactNumber" size="15" id="contactNumber" /></td>
      <td><input type="text" name="emailID" size="20" id="emailID" /></td>
    </tr>
</table>
<br />
<table>
<tr>
      <td>Invitation acceptance date by</td>
	  <td><input name="" type="text" /></td>
	   <td><input type="submit" name="submit" value="Date" /></td>
	</tr>
<tr>
<td></label><input type="submit" name="submit" value="Assign Supervisor to Student" /></td>
<td><input type="submit" name="submit" value="Print Invitation Letter" /></td>
</table>
  </form>
</body>
</html>