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
  <table border="0">
    <tr><td><strong>Student Information</strong>
	</td></tr>
	<tr>
      <td width="153">Report Date</td>
      <td width="300"><input type="text" name="reportDate" size="15" id="reportDate" value="15/12/2014" disabled="disabled"/></td>
    </tr>
	<tr>
      <td width="153">Student Name </td>
      <td width="300"><input type="text" name="studentName" size="50" id="studentName" value="Bui Ngoc Dung" disabled="disabled"/></td>
    </tr>
    <tr>
      <td>Telephone </td>
      <td><input type="text" name="telephone" size="50" id="telephone" value="013-3921122" disabled="disabled"/></td>
    </tr>
    <tr>
      <td>Email</td>
      <td><input type="text" name="email" size="50" id="email" value="bui@hotmail.com" disabled="disabled"/></td>
    </tr>
    <tr>
      <td>Matric Number</td>
      <td><input type="text" name="matricNumber" size="50" id="matricNumber" value="102014-03-0001" disabled="disabled"/></td>
    </tr>
    <tr>
      <td>Programme</td>
      <td><textarea name="programme" cols="50" disabled="disabled" id="programme">Doctor of Philosophy (Information and Communication Technology)</textarea></td>
    </tr>
	<tr>
      <td>Intake</td>
      <td><input type="text" name="intake" size="50" id="intake" value="May 2014" disabled="disabled"/></td>
    </tr>
	<tr>
      <td>Specialisation <em>(for MBA only)</em> </td>
      <td><input type="text" name="specialisation" size="50" id="specialisation" disabled="disabled"/></td>
    </tr>
</table>

  <p><strong>Notes:</strong><br />
  (1) This form should be submitted to MSU Graduate School of Management (GSM) upon completing of the Research Methodology and before student starts the project.<br />
  (2) Students are advised to seek the lecturer's advice before proceeding with the proposal.<br />
  (3) Student should plan on 6-month's time from the Official Approval Date to complete the Final Project.<br />
  (4) As refer to MBA rules, No candidate with CGPA below 3.0 shall be eligible to register for the Final Project of the degree unless recommended by the Board of Examiners.<br />
  (5) Appointment of supervisor is subject to the recommendation from the Director of MSU Graduate School of Management (GSM).</p>
  <br />
  <p><strong>Please indicate the module(s) that you have completed:</strong>  </p>
  <table width="522" border="0">
    <tr>
      <td width="142">Code</td>
      <td width="240">Core Module(s) </td>
      <td width="118">Results</td>
    </tr>
    <tr>
      <td><input type="text" name="code" size="15" id="code" /></td>
      <td><input type="text" name="core" size="50" id="core" /></td>
      <td><input type="text" name="result" size="15" id="result" /></td>
    </tr>
    <tr>
      <td><input type="text" name="code" size="15" id="code" /></td>
      <td><input type="text" name="core" size="50" id="core" /></td>
      <td><input type="text" name="result" size="15" id="result" /></td>
    </tr>
  </table>
  <table width="522" border="0">
    <tr>
      <td width="142">Code</td>
      <td width="240">Specialisation Module(s) </td>
      <td width="118">Results</td>
    </tr>
    <tr>
      <td><input type="text" name="code" size="15" id="code" /></td>
      <td><input type="text" name="specialisation" size="50" id="specialisation" /></td>
      <td><input type="text" name="result" size="15" id="result" /></td>
    </tr>
    <tr>
      <td><input type="text" name="code" size="15" id="code" /></td>
      <td><input type="text" name="specialisation" size="50" id="specialisation" /></td>
      <td><input type="text" name="result" size="15" id="result" /></td>
    </tr>
  </table>
<br />  
<strong>  Outline of Proposed Research/Case Study</strong>
 <table>
    <tr>
      <td>Topic of Final Project</td>
      <td><textarea name="programme" cols="50" id="programme" >Model-Free Analysis of Single and Multiple fMRI Time Series Based on Spatial Synchronization</textarea></td>
    </tr>
    <tr>
      <td>Propose</td>
      <td><p>
        <label>
          <input name="proposalType" type="radio" value="research" checked="checked" />
          Research</label>
        <label>
          <input type="radio" name="proposalType" value="caseStudy"  />
          Case Study</label>
      </p></td>
    </tr>
    <tr>
      <td>Introduction</td>
      <td><textarea rows="2" columns="80"  name="introduction" ></textarea></td>
    </tr>
    <tr>
      <td>Objective</td>
      <td><textarea rows="2" columns="80"  name="objective" ></textarea></td>
    </tr>
    <tr>
      <td>Brief Description of Research/Case Study </td>
      <td><textarea rows="2" columns="80" name="description" ></textarea></td>
    </tr>
  </table>
  <strong>Discussion Details </strong><br />
   <table>
     <tr>
      <td><p>Have you discussed about your research/case study to any Lecturer of MSU? </p>       </td>
      <td><p>
        <label>
          <input name="discussionConfirmation" type="radio" value="yes" checked="checked" />
          Yes</label>
        <label>
          <input type="radio" name="discussionConfirmation" value="no" />
          No</label>
      </p></td>
    </tr>
	</table>
	<table>
     <tr>
       <td>
         <label>
         <select name="searchBy">
           <option value="By Staff Name">By Staff Name</option>
           <option value="By Staff ID">By Staff ID</option>
         </select>
       </label></td>
       <td><input name="searchLecturer" type="text" id="searchLecturer" value="Enter search text here.." size="50" /></td>	   
	   <td><input type="submit" name="submit" value="Search"/></td>
     </tr>
    </table>
	<table>
	<tr>
	<td>If yes, please give his/her name </td>
	<td><input type="text" name="lecturerName" size="30" id="lecturerName" disabled="disabled"/></td>
	</tr>
	</table>
	 <br />
	 <strong>Discussion Date</strong>
	 <table>
	 <tr>
	 <td>Date</td>
	 <td>Time</td>
	 <td></td>
	 <td></td>	 	 
	 <td>Remarks</td>
	 <td></td>	 	 
	 </tr>
	 <tr>
	 <td><input type="text" name="meetingDate" value="3/12/2014" size="15" /></td>
	 <td><select name="selectStartHour">
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
	  <td><select name="selectStartMinute">
        <option value="00" selected="selected">00</option>
        <option value="01">15</option>
        <option value="02">30</option>
        <option value="03">35</option>
        <option value="04">40</option>
        <option value="05">45</option>
        <option value="06">50</option>
        <option value="07">55</option>        
      </select></td>
	  <td><select name="selectStartPM">
        <option value="AM" selected="selected">AM</option>
        <option value="PM">PM</option>
      </select></td>
	 <td><textarea name="remarks" cols="50" id="remarks">Discussed and agreed on the thesis topic</textarea></td>
	 <td>Delete</td>
	 </tr>
	 <tr>
	 <td><input type="text" name="meetingDate" value="13/12/2014" size="15" /></td>
	 <td><select name="selectStartHour">
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
	  <td><select name="selectStartMinute">
        <option value="00" selected="selected">00</option>
        <option value="01">15</option>
        <option value="02">30</option>
        <option value="03">35</option>
        <option value="04">40</option>
        <option value="05">45</option>
        <option value="06">50</option>
        <option value="07">55</option>        
      </select></td>
	  <td><select name="selectStartPM">
        <option value="AM" selected="selected">AM</option>
        <option value="PM">PM</option>
      </select></td>
	 <td><textarea name="remarks" cols="50" id="remarks">Finalised thesis topic and did amendment to proposal</textarea></td>
	 <td>Delete</td>
	 </tr>
  </table>
   <div>
   <table>
   <tr>
   <td>Attach the updated thesis proposal here
   </td>
   <td><input type="submit" name="attachment" value="Attachment"/></td>
   </tr>
    <tr>
   <td>Remarks
   </td>
   <td><textarea name="remarks" cols="50" id="remarks"></textarea></td>
   </tr>
   </table>
   </div>
   <p>
     <label>
     <input type="submit" name="submit" value="Print" />
     </label>
   <input type="submit" name="submit" value="Submit" />
   </p>
  </form>
</body>
</html>




