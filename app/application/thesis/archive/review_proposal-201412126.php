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
	<table>
    <tr>
      <td>Last Feedback Date by Lecturer</td>
      <td><input type="text" name="reportDate" size="15" id="reportDate" disabled="false" /></td>
    </tr>

   </table>
<br />
<table>
	<tr><td><strong>Student Information</strong>
	</td></tr>
	<tr>
	  <td>Report Date </td>
	  <td><input type="text" name="reportDate" size="15" id="reportDate" /></td>
    </tr>
	<tr>
      <td width="153">Student Name </td>
      <td width="300"><input type="text" name="studentName" size="50" id="studentName" /></td>
    </tr>
    <tr>
      <td>Telephone </td>
      <td><input type="text" name="telephone" size="50" id="telephone" /></td>
    </tr>
    <tr>
      <td>Email</td>
      <td><input type="text" name="email" size="50" id="email" /></td>
    </tr>
    <tr>
      <td>Matrix Number</td>
      <td><input type="text" name="matrixNumber" size="50" id="matrixNumber" /></td>
    </tr>
    <tr>
      <td>Programme</td>
      <td><input type="text" name="programme" size="50" id="programme" /></td>
    </tr>
	<tr>
      <td>Intake</td>
      <td><input type="text" name="intake" size="50" id="intake" /></td>
    </tr>
	<tr>
      <td>Specialisation <em>(for MBA only)</em> </td>
      <td><input type="text" name="specialisation" size="50" id="specialisation" /></td>
    </tr>
</table>

  <p><strong>Notes:</strong></p>
  (1) This form should be submitted to MSU Graduate School of Management (GSM) upon completing of the Research Methodology and before student starts the project.<brp>
  (2) Students are advised to seek the lecturer's advice before proceeding with the proposal.<br/>
  (3) Student should plan on 6-month's time from the Official Approval Date to complete the Final Project.<br/>
  (4) As refer to MBA rules, No candidate with CGPA below 3.0 shall be eligible to register for the Final Project of the degree unless recommended by the Board of Examiners.<br/>
  (5) Appointment of supervisor is subject to the recommendation from the Director of MSU Graduate School of Management (GSM).<br/>
  <p>&nbsp;   </p>
  <p><strong>The module(s) which has been completed by the Student:</strong>  </p>
  <table border="0">
    <tr>
      <td>Code</td>
      <td>Core Module(s) </td>
      <td>Results</td>
    </tr>
    <tr>
      <td><input type="text" name="programme" size="15" id="programme" /></td>
      <td><input type="text" name="programme" size="50" id="programme" /></td>
      <td><input type="text" name="programme" size="15" id="programme" /></td>
    </tr>
    <tr>
      <td><input type="text" name="programme" size="15" id="programme" /></td>
      <td><input type="text" name="programme" size="50" id="programme" /></td>
      <td><input type="text" name="programme" size="15" id="programme" /></td>
    </tr>
  </table>
  <table border="0">
    <tr>
      <td>Code</td>
      <td>Specialisation Module(s) </td>
      <td>Results</td>
    </tr>
    <tr>
      <td><input type="text" name="programme" size="15" id="programme" /></td>
      <td><input type="text" name="programme" size="50" id="programme" /></td>
      <td><input type="text" name="programme" size="15" id="programme" /></td>
    </tr>
    <tr>
      <td><input type="text" name="programme" size="15" id="programme" /></td>
      <td><input type="text" name="programme" size="50" id="programme" /></td>
      <td><input type="text" name="programme" size="15" id="programme" /></td>
    </tr>
  </table>
  <br />
<strong>  Outline of Proposed Research/Case Study by the Student</strong>
<table>
  
  <tr>
    <td>Topic of Final Project</td>
    <td><textarea name="programme2" cols="50" id="programme2"></textarea></td>
   
  </tr>
  <tr>
    <td>Propose</td>
    <td><p>
      <label>
        <input type="radio" name="proposalType" value="research" />
        Research</label>
      <label>
        <input type="radio" name="proposalType" value="caseStudy" />
        Case Study</label>
    </p></td>
	
  </tr>
  <tr>
    <td>Introduction</td>
    <td><textarea rows="2" columns="30" name="introduction"></textarea></td>
    
  </tr>
  <tr>
    <td>Objective</td>
    <td><textarea rows="2" columns="30" name="textarea"></textarea></td>
    
  </tr>
  <tr>
    <td>Brief Description of Research/Case Study </td>
    <td><textarea rows="2" columns="30"  name="description"></textarea></td>
    
  </tr>
</table>
<strong>Discussion Details </strong><br />
<table>
     <tr>
      <td><p>Have you discussed about your research/case study to any Lecturer of MSU? </p></td>      
	  <td>
	  <p>
	    <label><input type="radio" name="discussionConfirmation" value="yes" />Yes</label>
	    <label><input type="radio" name="discussionConfirmation" value="no" />No</label>
	    </p>	   </td>
    </tr>
    </table>
	 <table>
     <tr>
       <td>If yes, please give his/her name </td>
       <td><input type="text" name="lecturerName" size="50" id="lecturerName" /></td>
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
	 </tr>
	 <tr>
	 <td><input type="text" name="meetingDate" value="" size="15" /></td>
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
	 <td><textarea name="remarks" cols="50" id="remarks"></textarea></td>
	 </tr>
	 <tr>
	 <td><input type="text" name="meetingDate" value="" size="15" /></td>
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
	 <td><textarea name="remarks" cols="50" id="remarks"></textarea></td>
	 </tr>
  </table>
   <table>
     <tr>
       <td>Thesis proposal as attached by the student </td>
       <td>
       <input type="submit" name="Submit" value="View Attachment" /></td>
     </tr>
    </table>
	 <br />
	 <strong>Recomendation Status by Lecturer </strong>
	 </table>
	 <table>
     <tr>
      <td><p>Recomendation Status</p>       </td>
      
	  <td><p>
	    <label>
	      <input type="radio" name="recomendedStatus" value="recommended" />
	      Reccomended for Approval</label>
		    <label>
	      <input type="radio" name="recomendedStatus" value="request" />
	      Request with Changes</label>
	    <label></p>
		</td>
    </tr>
	<tr>
	<td> Remarks  </td>
		<td><textarea name="objective" cols="50" id="objective"></textarea></td>
	</tr>
	</table>
	<p>
     <label>
     <input type="submit" name="submit" value="Print" />
     </label>
   <input type="submit" name="submit" value="Submit" />
   </p>
  </form>
</body>
</html>




