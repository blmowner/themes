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
  <p><strong>Student Information</strong></p>
  <table border="0">
    <tr>
	<td><img src="photo/student1.jpg" alt="" name="photo" width="90" height="91" /></td>
	</tr>
  </table>
  <table border="0">
  	<tr>
      <td >Student Name </td>
      <td><input type="text" name="studentName" size="50" id="studentName" value="Bui Ngoc Dung" disabled="disabled"/></td>
    </tr>
    <tr>
      <td>Matric Number </td>
      <td><input type="text" name="matricNumber" size="50" id="matricNumber" value="102014-03-0001" disabled="disabled"/></td>
    </tr>
    <tr>
      <td>Programme</td>
      <td><textarea name="programme" cols="50" id="programme" disabled="disabled">Doctor of Philosophy (Information and Communication Technology) </textarea></td>
    </tr>
    <tr>
      <td>Intake</td>
      <td><input type="text" name="intake" size="50" id="intake" value="May 2014" disabled="disabled"/></td>
    </tr>
    <tr>
      <td>Thesis ID </td>
      <td><input type="text" name="thesisID" size="50" id="thesisID" value="201405001" disabled="disabled"/></td>
    </tr>
    <tr>
      <td>Thesis Title </td>
      <td><textarea name="thesisTitle" cols="50" id="thesisTitle" disabled="disabled">Model-Free Analysis of Single and Multiple fMRI Time Series Based on Spatial Synchronization</textarea></td>
    </tr>
	<tr>
      <td>Student Status </td>
      <td><input type="text" name="studentStatus" size="50" id="studentStatus" value="Active" disabled="disabled"/></td>
    </tr>
</table>
<p><strong>List of Supervisor/Co-Supervisor</strong></p>
<table border="0">
    <tr>
      <td>No</td>
	  <td>Photo</td>  	  
      <td>Supervisor Name </td>
	  <td>Staff ID</td>
      <td>Faculty </td>
	  <td>Contact No. </td>	  
      <td>Email ID</td>
      <td>Skype ID </td>
    </tr>
    <tr>
      <td>1</td>
	  <td><img src="photo/staff1.jpg" alt="" name="photo" width="90" height="91" /></td>
      <td><input type="text" name="supervisorName1" size="30" id="supervisorName" value="MOHAMAD NIZAM BIN ABDUL GHANI" disabled="disabled"/></td>
	  <td><input type="text" name="staffID" size="15" id="staffID" value="A3321" disabled="disabled"/></td>
	  <td><input type="text" name="faculty" size="50" id="faculty" value="FISE" disabled="disabled"/></td>	  
	  <td><input type="text" name="contactNumber" size="15" id="contactNumber" value="019-3381030"  disabled="disabled"/></td>
      <td><input type="text" name="emailID" size="30" id="emailID" value="m_nizam@msu.edu.my" disabled="disabled"/></td>
      <td><input type="text" name="skypeID" size="30" id="skypeID" value="m_nizam@msu.edu.my" disabled="disabled"/></td>
    </tr>
    <tr>
      <td>2</td>
      <td><img src="photo/staff2.jpg" alt="" name="photo" width="90" height="91" /></td>
	  <td><input type="text" name="supervisorName2" size="30" id="supervisorName2" value=" ANAND SHAKER IVVALA" disabled="disabled"/></td>
      <td><input type="text" name="staffID" size="15" id="staffID" value="A5534" disabled="disabled"/></td>
	  <td><input type="text" name="faculty2" size="50" id="faculty2" value="FISE" disabled="disabled"/></td>
	  <td><input type="text" name="contactNumber2" size="15" id="contactNumber2" value="019-3381030" disabled="disabled"/></td>
	  <td><input type="text" name="emailID2" size="30" id="emailID2" value="m_nizam@msu.edu.my" disabled="disabled"/></td>
      <td><input type="text" name="skypeID2" size="30" id="skypeID2" value="m_nizam@msu.edu.my" disabled="disabled"/></td>
    </tr>
</table>
<br />
<table border="0">
    <tr>
      <td>1. </td>
	  <td><strong>Thesis Process Flow </strong></td>
      <td><strong>Status</strong></td>
    </tr>
    <tr>
      <td>2. </td>
	  <td >Thesis Proposal </td>
      <td ><input type="text" name="thesisProposal" size="10" id="thesisProposal" value="In Progress" disabled="disabled"/></td
    ></tr>
    <tr>
      <td>3. </td>
	  <td>Proposal Defense </td>
      <td><input type="text" name="proposalDefense" size="10" id="proposalDefense" value="Pending" disabled="disabled"/></td>

    </tr>
    <tr>
      <td>4. </td>
	  <td>Work Completion </td>
      <td><input type="text" name="programme" size="10" id="programme" value="Pending" disabled="disabled"/></td>

    </tr>
    <tr>
      <td>5. </td>
	  <td>Thesis Evaluation/VIVA </td>
      <td><input type="text" name="intake" size="10" id="intake" value="Pending" disabled="disabled"/></td>

    </tr>
    <tr>
      <td>6. </td>
	  <td>Final Submission </td>
      <td><input type="text" name="thesisTitle" size="10" id="thesisTitle" value="Pending" disabled="disabled"/></td>

    </tr>
    <tr>
      <td>7. </td>
	  <td>Senate Endorsement </td>
      <td><input type="text" name="thesisTitle2" size="10" id="thesisTitle2" value="Pending" disabled="disabled"/></td>

    </tr>
</table>
  </form>
</body>
</html>