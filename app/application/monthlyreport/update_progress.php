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
  
<p><strong>Important Notes:</strong><br />
(1) Student must bring this form to each meeting with the supervisor /Co-supervisor. <br /> 
(2) An original copy of the completed form must be returned to the MSU Colombo in end of every month.<br />
(3) Students are not allowed to hand over the final completed Thesis if he /she does not submit the completed and signed form every month. <br />
(4) Students should meet supervisor/co-supervisor through the face-to-face, Skype or email at least once a month.<br /></p>
  <form id="form1" name="form1" method="post" enctype="multipart/form-data">
  <table border="0">
  <tr>
    <td><strong>Report Details </strong><em>(filled in by the Student)</em> </td>
  </tr>
	</table>	
	<table>
    <tr>
      <td>Report Date</td>
      <td><input name="reportDate" type="text" value="16/12/2014" size="15" disabled="disabled"/></td>
    </tr>
    <tr>
      <td>Our Reference Number <em></em></td>
      <td><input name="referenceNumber" type="text" value="201405-001" size="50" disabled="disabled"/></td>
    </tr>
     <tr>
      <td>Student Name</td>
      <td><input name="studentName" type="text" value="Bui Ngoc Dung" size="50" disabled="disabled"/></td>
    </tr>
	<tr>
      <td>Programme</td>      
	  <td><textarea name="programme" cols="50" disabled="disabled">Doctor of Philosophy (Information and Communication Technology) </textarea></td>
    </tr>
	<tr>
	<td>Matrix Number </td>
		<td><input type="text" name="matrixNumber" value="102014-03-0001" size="50" disabled="disabled"/></td>
	</tr>
	<tr>
	  <td>Thesis / Project ID </td>
	  <td><input type="text" name="thesisID" value="201405001" size="50" disabled="disabled"/></td>
	  </tr>
	<tr>
	<td>Thesis Topic</td>
	<td><textarea name="thesisTopic" cols="50" disabled="disabled">Model-Free Analysis of Single and Multiple fMRI Time Series Based on Spatial Synchronization</textarea></td>
	</tr>
	<tr>
	  <td>Supervisor</td>
	  <td><input type="text" name="supervisor" value="Mohd Nizam Bin Abdul Ghani" size="50" disabled="disabled"/></td>
	  </tr>
	<tr>
	  <td>Co-Supervisor</td>
	  <td><input type="text" name="cosupervisor" value="Anand Shaker Ivvala" size="50" disabled="disabled"/></td>
	  </tr>
	<tr>
	  <td>Meeting Date</td>
	  <td><input type="text" name="meetingDate" value="14/12/2015" size="15" disabled="disabled"/></td>
	  </tr>
    </table>
	  <table>
	<tr>
	  <td>Meeting Start Time </td>
	  <td><select name="selectStartHour" disabled="disabled">
        <option value="00">00</option>
        <option value="01">01</option>
        <option value="02">02</option>
        <option value="03">03</option>
        <option value="04">04</option>
        <option value="05">05</option>
        <option value="06">06</option>
        <option value="07">07</option>
        <option value="08">08</option>
        <option value="09" selected="selected">09</option>
        <option value="10">10</option>
        <option value="11">11</option>
		<option value="12">12</option>
        <option value="13">13</option>
        <option value="14">14</option>
        <option value="15">15</option>
        <option value="16">16</option>
        <option value="17">17</option>
        <option value="18">18</option>
        <option value="19">19</option>
        <option value="20">20</option>
        <option value="21">21</option>
		<option value="22">22</option>
        <option value="23">23</option>
      </select></td>
	  <td><select name="selectStartMinute" disabled="disabled">
        <option value="00" selected="selected">00</option>
        <option value="01">15</option>
        <option value="02">30</option>
        <option value="03">35</option>
        <option value="04">40</option>
        <option value="05">45</option>
        <option value="06">50</option>
        <option value="07">55</option>        
      </select></td>
	  <td><select name="selectStartPM" disabled="disabled">
        <option value="AM" selected="selected">AM</option>
        <option value="PM">PM</option>
      </select></td>
	  </tr>
	<tr>
	  <td>Meeting End Time </td>
	  <td><select name="selectStartHour" disabled="disabled">
        <option value="00">00</option>
        <option value="01">01</option>
        <option value="02">02</option>
        <option value="03">03</option>
        <option value="04">04</option>
        <option value="05">05</option>
        <option value="06">06</option>
        <option value="07">07</option>
        <option value="08">08</option>
        <option value="09">09</option>
        <option value="10">10</option>
        <option value="11" selected="selected">11</option>
		<option value="12">12</option>
        <option value="13">13</option>
        <option value="14">14</option>
        <option value="15">15</option>
        <option value="16">16</option>
        <option value="17">17</option>
        <option value="18">18</option>
        <option value="19">19</option>
        <option value="20">20</option>
        <option value="21">21</option>
		<option value="22">22</option>
        <option value="23">23</option>
      </select></td>
	  <td><select name="selectStartMinute" disabled="disabled">
        <option value="00" selected="selected">00</option>
        <option value="01">15</option>
        <option value="02">30</option>
        <option value="03">35</option>
        <option value="04">40</option>
        <option value="05">45</option>
        <option value="06">50</option>
        <option value="07">55</option>        
      </select></td>
	  <td><select name="selectStartPM" disabled="disabled">
        <option value="AM" selected="selected">AM</option>
        <option value="PM">PM</option>
      </select></td>
	  </tr>
	</table>
	<table>
	<tr>
	<td>Duration of meeting</td>
	<td><input name="meetingDurationHour" type="text" value="2" size="10" disabled="disabled"/> 
	hour</td>
	<td><input name="meetingDurationMinute" type="text" value="00" size="10" disabled="disabled"/> 
	minute</td>
	</tr>
	</table>
	
<br />
	<table>
	<tr>
	  <td><strong>Content of Discussion</strong> <em>(filled in by the Supervisor)</em></td>
	</tr>
	<tr>
		<td><em>Please tick.</em></td>
	</tr>
	</table>
	<table>
	<tr>
      <td><label><input type="checkbox" name="checkbox" value="checkbox" />Chapter 1: Introduction</label>
	  </td>
	  </tr>
    </table>
	  <table>
	  <tr>
	  <td>Remarks </td>
      <td><textarea name="chapter1Remarks" rows="3" cols="50" ></textarea></td>
	  <td><input type="submit" name="attachment" value="Attachment"/></td>
    </tr>
	</table>
	<br />
	<table>
	<tr>
      <td><label><input type="checkbox" name="checkbox" value="checkbox" />Chapter 2: Literature Review</label>
	  </td>
	  </tr>
    </table>
	  <table>
	  <tr>
	  <td>Remarks </td>
      <td><textarea name="chapter2Remarks" rows="3" cols="50" ></textarea></td>
	  	  <td><input type="submit" name="attachment" value="Attachment"/></td>
    </tr>
	</table>    
		<br />
	<table>
	<tr>
      <td><label><input type="checkbox" name="checkbox" value="checkbox" />Chapter 3: Methodology</label>
	  </td>
	  </tr>
    </table>
	  <table>
	  <tr>
	  <td>Remarks </td>
      <td><textarea name="chapter3Remarks" rows="3" cols="50" ></textarea></td>
	  	  <td><input type="submit" name="attachment" value="Attachment"/></td>
    </tr>
	</table> 
		<br />
	<table>
	<tr>
      <td><label><input type="checkbox" name="checkbox" value="checkbox" />Chapter 4: Data Analysis</label></td>
	  </tr>
    </table>
	  <table>
	  <tr>
	  <td>Remarks </td>
      <td><textarea name="chapter4Remarks" rows="3" cols="50" ></textarea></td>
	  	  <td><input type="submit" name="attachment" value="Attachment"/></td>
    </tr>
	</table> 
		<br />      
  	<table>
	<tr>
      <td><label><input type="checkbox" name="checkbox" value="checkbox" />Chapter 5: Discussion and Conclusion</label></td>
	  </tr>
    </table>
	  <table>
	  <tr>
	  <td>Remarks </td>
      <td><textarea name="chapter5Remarks" rows="3" cols="50" ></textarea></td>
	  	  <td><input type="submit" name="attachment" value="Attachment"/></td>
    </tr>
	</table>   
		<br />
	<table>
	<tr>
      <td><label><input type="checkbox" name="checkbox" value="checkbox" />Others<em> (Please Specify)</em></label></td>
	  </tr>
    </table>
	  <table>
	  <tr>
	  <td>Remarks</td>
      <td><textarea name="chapter6Remarks" rows="3" cols="50" ></textarea></td>
	  	  <td><input type="submit" name="attachment" value="Attachment"/></td>
    </tr>
	</table>  
	<br />
	<table>
	<tr>
      <td>Description of topic or Issues facing by student</td>
	  </tr>
    </table>
	  <table>
	  <tr>
	  <td>Remarks </td>
      <td><textarea name="chapter5Remarks" rows="3" cols="50" ></textarea></td>
	  	  <td><input type="submit" name="attachment" value="Attachment"/></td>
    </tr>
	</table>		  
    <br />
	<table>
	<tr>
      <td>Advice from Supervisor &amp; list of Action to be taken by th student. </td>
	  </tr>
    </table>
	  <table>
	  <tr>
	  <td>Remarks </td>
      <td><textarea name="chapter5Remarks" rows="3" cols="50" ></textarea></td>
	  	  <td><input type="submit" name="attachment" value="Attachment"/></td>
    </tr>
	</table>    
<br />
	<p>
     <label>
     <input type="submit" name="submit" value="Print" />
     </label>
   <input type="submit" name="submit" value="Submit" />
   </p>
  </form>
</body>
</html>




