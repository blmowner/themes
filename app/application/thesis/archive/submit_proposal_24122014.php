<?php

include("../../../lib/common.php");
checkLogin();

	?>

	

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
    <link rel="stylesheet" type="text/css" href="http://his.msu.edu.my/theme/css/colorbox.css" media="screen" />
   	<link rel="stylesheet" href="http://his.msu.edu.my/lib/js/datePicker/jquery-ui-1.8.11.custom.css" />

	<script type="text/javascript" src="http://his.msu.edu.my/lib/js/ckeditor/ckeditor.js"></script>
	<script src="http://his.msu.edu.my/lib/js/jquery.min2.js"></script>
    <script type="text/javascript" src="http://his.msu.edu.my/lib/js/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="http://his.msu.edu.my/lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
</head>
<body>

<?
//$app_id=$_SESSION["user_id"];

function runnum($column_name, $tblname) 
{ 
    global $db_klas2;
    
    $run_start = "001";
    
    $sql_slct_max = "SELECT MAX($column_name) AS run_id FROM $tblname";
    $sql_slct = $db_klas2;
    $sql_slct->query($sql_slct_max);
    $sql_slct->next_record();

    if($sql_slct->num_rows($sql_slct_max)== 0 || $sql_slct->f("run_id")==NULL) 
	{
        $run_id = date("Ymd").$run_start;
    } 
	else 
	{
        $todate = date("Ymd");
        
        if($todate > substr($sql_slct->f("run_id"),0,8)) 
		{
            $run_id = $todate.$run_start;
        } 
		else 
		{
            $run_id = $sql_slct->f("run_id") + 1; 
        }
    }
    return $run_id;
}

//echo $_POST['thesis_title'];

if($_REQUEST["btnSave"]<> '0')
	{	
		/*
		----hide for temp coz it will auto update if not click button submit
		$sqlselect="SELECT semester_id FROM student_program where matrix_no='$user_id'";
		$dbsel=$db;
		$dbsel->query($sqlselect); //echo $sql;
		$dbsel->next_record();
		$semester_id=$dbsel->f("semester_id");
		
		$curdatetime = date("Y-m-d H:i:s");
			$sqlsubmit="INSERT INTO pg_assessment(id,matrix_no,thesis_title,thesis_type,introduction,objective,description,modify_dt)
		 				VALUES('$user_id','$user_id','".$_POST['thesis_title']."','".$_POST['thesis_type']."','".$_POST['introduction']."','".$_POST['objective']."',
							'".$_POST['description']."','$curdatetime') ";
		 $db_klas2->query($sqlsubmit); */ 
		 //echo $sqlsubmit;
				
				
	
			
	
	}
?>

	
  <form id="form1" name="form1" method="post" enctype="multipart/form-data">
<fieldset>

  <p><strong>Notes:</strong><br />
  (1) This form should be submitted to MSU Graduate School of Management (GSM) upon completing of the Research Methodology and before student starts the project.<br />
  (2) Students are advised to seek the lecturer's advice before proceeding with the proposal.<br />
  (3) Student should plan on 6-month's time from the Official Approval Date to complete the Final Project.<br />
  (4) As refer to MBA rules, No candidate with CGPA below 3.0 shall be eligible to register for the Final Project of the degree unless recommended by the Board of Examiners.<br />
  (5) Appointment of supervisor is subject to the recommendation from the Director of MSU Graduate School of Management (GSM).</p>
   
<strong>  Outline of Proposed Research/Case Study</strong>
 <table>
    <tr>
      <td>Topic of Final Project</td>
      <td><input type="text" name="thesis_title" cols="100" rows="3" id="thesis_title" value="<?=$thesis_title?>"></td>
    </tr>
    <tr>
      <td>Propose</td>
      <td><p>
	  	<?php if($cases=='Research')	{	?>
			<input type=radio name=cases value=Research checked> Research
			<input type=radio name=cases value=Case Study> Case Study
					
		<?php	}	else	{	?>
			<input type=radio name=cases value=Research> Research
			<input type=radio name=cases value=Case Study checked> Case Study
		<?php	}	?> 

       <?php /*?> <label>
          <input name="proposalType" type="radio" value="research" checked="checked" />
          Research</label>
        <label>
          <input type="radio" name="proposalType" value="caseStudy"  />
          Case Study</label><?php */?>
      </p></td>
    </tr>
    <tr>
      <td>Introduction</td>
      <td><textarea name="introduction" cols="30" class="ckeditor" rows="3" value="<?=$introduction?>"></textarea></td>
    </tr>
    <tr>
      <td>Objective</td>
      <td><textarea name="objective" cols="30" class="ckeditor" rows="3" value="<?=$objective?>"></textarea></td>
    </tr>
    <tr>
      <td>Brief Description of Research/Case Study </td>
      <td><textarea name="description" cols="30" class="ckeditor" rows="3" value="<?=$description?>"></textarea></td>
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
	<tr>
	<table>
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
     <label></label>
     <input type="submit" name="submit2" align="centre" value="Print" />
     <input type="submit" name="btnSave" id="btnSubmit" align="centre"  value="Submit" />
   </p>
  </fieldset>
  </form>
</body>
</html>




