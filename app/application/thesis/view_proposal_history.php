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
	
	
	<?php
$varSearchCat["01"]["desc"]="By Month/Year";
$varSearchCat["01"]["db"]="";
$varSearchCat["02"]["desc"]="Thesis ID";
$varSearchCat["02"]["db"]="";
$varSearchCat["03"]["desc"]="Thesis Title";
$varSearchCat["04"]["db"]="";


	?>
	
</head>
<body>
<form id="form-set" name="form1" method="post" enctype="multipart/form-data" action="<?php echo $_SERVER["../asset07112012/PHP_SELF"];?>">

	<table>
     <tr>
       <label class="labeling">Search by</label>
	   <td><strong>Search Thesis Proposal</strong></td>       
     </tr>
    </table>

	<table>
     <tr>
       <td>
         <label>
         <select name="searchBy">
           <option value="By Month/Year">By Month/Year</option>
           <option value="By Thesis ID">By Thesis / Project ID</option>
           <option value="By Thesis Title">By Thesis Title</option>
         </select>
       </label></td>
       <td><input name="searchLecturer" type="text" id="searchLecturer" value="Enter search text here.." size="50" /></td>	   
	   <td><input type="submit" name="submit" value="Search"/></td>
     </tr>
    </table>
	<br />
	<p><strong>Result List</strong></p>
	
	<table border="0">
  <tr>
    <td>Thesis Submission Date</td>
    <td>Thesis Feedback Date by Faculty</td>
    <td>Topic of Final Project</td>
    <td>Proposal Type</td>
    <td>Introduction</td>
    <td>Objective</td>
    <td>Brief Description of Research/Case Study </td>
    <td>Lecturer Name </td>
    <td>Discussion Date</td>
    <td>Recommendation Status</td>
	<td>Approval Status</td>	
	<td>Proposal Attachment</td>
  </tr>
  <tr>
    <td><input type="text" name="discussionDate" size="15" id="discussionDate" disabled="disabled"/></td>
    <td><input type="text" name="discussionDate" size="15" id="discussionDate" disabled="disabled"/></td>
    <td><textarea name="discussionDate" cols="15" id="discussionDate" disabled="disabled"></textarea></td>
    <td><input type="text" name="discussionDate" size="15" id="discussionDate" disabled="disabled"/></td>
    <td><textarea name="discussionDate" cols="15" id="discussionDate" disabled="disabled"></textarea></td>
    <td><textarea name="discussionDate" cols="15" id="discussionDate" disabled="disabled"></textarea></td>
    <td><textarea name="discussionDate" cols="15" id="discussionDate" disabled="disabled"></textarea></td>
    <td><input type="text" name="discussionDate" size="15" id="discussionDate" disabled="disabled"/></td>
    <td><input type="text" name="discussionDate" size="15" id="discussionDate" disabled="disabled"/></td>
    <td><input type="text" name="discussionDate" size="15" id="discussionDate" disabled="disabled"/></td>
    <td><input type="text" name="discussionDate" size="15" id="discussionDate" disabled="disabled"/></td>
	<td><input type="submit" name="Submit" value="View Attachment" /></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
	
</form>
</body>
</html>




