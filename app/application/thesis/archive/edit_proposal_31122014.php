<?php

include("../../../lib/common.php");
checkLogin();

	?>
<?
if(isset($_POST['btnSave']) && ($_POST['btnSave'] <> ""))
{	

	//echo $thesis_id=$dbsel->f("semester_id")+$run_start;
	$curdatetime = date("Y-m-d H:i:s");
	$meeting_sdate = date("Y-m-d H:i:s");
	$sqlupdate="UPDATE pg_thesis SET
				proposal_date='$curdatetime',
				modify_by='$user_id',
				modify_date='$curdatetime',
				ref_thesis_status_id_proposal='INP'
				WHERE id='$thesis_id'";
	$db_klas2->query($sqlupdate); 
	
	$sqlupdate1="UPDATE pg_proposal SET
				thesis_title='$thesis_title',
				thesis_type='$thesis_type',
				introduction='$introduction',
				objective='$objective',
				description='$description' 
				WHERE id='$thesis_id' ";
	$db_klas2->query($sqlupdate1); 
	
	$sqlupdate2="UPDATE pg_meeting_detail SET
				lecturer_name='$lecturer_name',
				meeting_sdate='$meeting_sdate',
				meeting_edate='',
				remark='$remark',
				insert_by='$user_id',
				insert_date='$curdatetime',
				modify_by='$user_id',
				modify_date='$curdatetime'
				WHERE id='$thesis_id'";
	$db_klas2->query($sqlupdate2); 
	
	
}

$varBtnNm="Update";
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
$sql_area = "SELECT * FROM pg_proposal WHERE matrix_no = '$user_id' ORDER BY id DESC LIMIT 1 ";
$db_klas2->query($sql_area);
$row_area = $db_klas2->fetchArray();

$thesis_id=$row_area['id'];
$thesis_title=$row_area['thesis_title'];
$thesis_type=$row_area['thesis_type'];
$remarks=$row_area['remarks'];
$introduction=$row_area['introduction'];
$objective=$row_area['objective'];
$description=$row_area['description'];
$feedback_date=$row_area['feedback_date'];
$feedback_remarks=$row_area['feedback_remarks'];
$status=$row_area['status'];
?>

<script>
$(function() {
	$( "#datepickerFirst" ).datepicker({
		changeMonth: true,
		changeYear: true,
		yearRange: '-100:+0'
		});
});
</script>

<script type="text/javascript">
$(function() {

	var i = $('input').size() + 1;
	
	//###################################### funtion add more document files ######################################//
	$('a.add-file').click(function() {

		$('<tr><td align="center"><input type="checkbox" class="case_file" /></td><td align=\"center\"><input type=\"text\" name=\"file_name[]\" size="40" /></td><td align="center"><input name="fileData[]" type="file" size="40"/></td><td ></td></tr>').animate({ opacity: "show" }, "slow").appendTo('#inputs9');
		i++;
	});
	
	$('a.remove-file').click(function() {
        $(".case_file:checked").each(function() {
            $(this).parent().parent().remove()
        });
    });
    
    // add multiple select / deselect functionality
    $("#selectall_file").click(function () {
          $('.case_file').attr('checked', this.checked);
    });
 
    // if all checkbox are selected, check the selectall checkbox and viceversa
    $(".case_file").click(function(){
 
        if($(".case_file").length == $(".case_file:checked").length) {
            $("#selectall_file").attr("checked", "checked");
        } else {
            $("#selectall_file").removeAttr("checked");
        }
 
    });
	
	//###################################### end of funtion add more document files ######################################//
	
});

</script>	

<script type="text/javascript">
$(function() 
{
	var i = $('input').size() + 1;
	
	//###################################### funtion add more @ certification ######################################//
	$('a.add-certification').click(function() {

	$('<tr><td align="center"><input type="checkbox" name="cbDelFile2" class="case_certificate" /></td><td><input type="text" name="date" size="15" readonly id="datepicker'+i+'" value="<?php echo $date; ?>"/></td><td width="66" height="1" class="tbmain"><select name="meeting_sdate[]" size="1" value="<?=$meeting_sdate?>"><option value=\"\"></option><option value="07:00">07:00 AM</option><option value="07:30">07:30 AM</option><option value="08:00">08:00 AM</option><option value="08:30">08:30 AM</option><option value="09:00">09:00 AM</option><option value="09:30">09:30 AM</option><option value="10:00">10:00 AM</option><option value="10:30">10:30 AM</option><option value="11:00">11:00 AM</option><option value="11:30">11:30 AM</option><option value="12:00">12:00 PM</option><option value="12:30">12:30 PM</option><option value="01:00">01:00 PM</option><option value="01:30">01:30 PM</option><option value="02:00">02:00 PM</option><option value="02:30">02:30 PM</option><option value="03:00">03:00 PM</option><option value="03:30">03:30 PM</option><option value="04:00">04:00 PM</option><option value="04:30">04:30 PM</option><option value="05:00">05:00 PM</option><option value="05:30">05:30 PM</option><option value="06:00">06:00 PM</option><option value="06:30">06:30 PM</option><option value="07:00">07:00 PM</option><option value="07:30">07:30 PM</option><option value="08:00">08:00 PM</option><option value="08:30">08:30 PM</option><option value="09:00">09:00 PM</option><option value="09:30">09:30 PM</option><option value="10:00">10:00 PM</option><option value="10:30">10:30 PM</option><option value="11:00">11:00 PM</option><option value="11:30">11:30 PM</option><option value="12:00">12:00 PM</option></select></td><td><input type="text" name="lecturer_name[]" size="40" value="<?php echo $lecturer_name; ?>"/></td><td><textarea name="remarks" cols="100" id="remarks"><?php echo $remark; ?></textarea></td></tr>').animate({ opacity: "show" }, "slow").appendTo('#inputs10');

	$("#datepicker"+i).datepicker({
		changeMonth: true,
		changeYear: true,
		yearRange: '-100:+0'
		});

		i++;		
	});
    
	
	$('a.remove-certification').click(function() {
        $(".case_certificate:checked").each(function() {
            $(this).parent().parent().remove()
        });	
    });
    
    // add multiple select / deselect functionality
    $("#selectall_certificate").click(function () 
	{
          $('.case_certificate').attr('checked', this.checked);
    });
 
    // if all checkbox are selected, check the selectall checkbox and viceversa
    $(".case_certificate").click(function()
	{
		if($(".case_certificate").length == $(".case_certificate:checked").length) 
		{
            $("#selectall_certificate").attr("checked", "checked");
        } else {
            $("#selectall_certificate").removeAttr("checked");
        }
    });
	//###################################### end of funtion add more @ certification ######################################//
});
</script>

  <form id="form1" name="form1" method="post" enctype="multipart/form-data">
  <input type="hidden" name="thesis_id" id="thesis_id" value="<?php echo $thesis_id; ?>">
  <fieldset>
  <table border="0">
  <tr>
    <td><strong>Feedback Information by  Dean Faculty / Director </strong>	</td>
  </tr>
	</table>
	<table>
    <tr>
      <td>Feedback Date</td>
      <td><input name="feedback_date" type="text" id="feedback_date" value="<?php echo $feedback_date; ?>"  size="15" disabled="disabled"></td>
    </tr>

     <tr>
      <td><p>Approval Status</p>       </td>
      
	  <td><p>
	  		<?php if($status=='Approved')	{	?>
			<input type=radio name=status value=Approved checked disabled="disabled"> Approved
			<input type=radio name=status value=Request with Changes disabled="disabled"> Request with Changes
			<input type=radio name=status value=Disapproved disabled="disabled"> Disapproved
					
		<?php	}	elseif($status=='Request with Changes')	{	?>
			<input type=radio name=status value=Approved disabled="disabled"> Approved
			<input type=radio name=status value=Request with Changes checked disabled="disabled"> Request with Changes
			<input type=radio name=status value=Disapproved disabled="disabled"> Disapproved
		
		<?php	}	else { ?> 
			<input type=radio name=status value=Approved disabled="disabled"> Approved
			<input type=radio name=status value=Request with Changes disabled="disabled"> Request with Changes
			<input type=radio name=status value=Disapproved checked disabled="disabled"> Disapproved
		
		<?php	} ?> 
		
	  <?php /*?>  <label><input type="radio" name="approvalStatus" value="Recommended for Approval" disabled="disabled"/>Approved</label>
	    <label><input name="approvalStatus" type="radio" value="request" checked="checked" disabled="disabled" />
	    Request with Changes</label>
	    <label><input type="radio" name="approvalStatus" value="request" disabled="disabled"/>Disapproved</label>	<?php */?>	
	</p></td>
    </tr>
	<tr>
	<td> Remarks </td>
		<td><textarea name="feedback_remarks" rows="2" cols="50" id="feedback_remarks" disabled="disabled"><?php echo $feedback_remarks; ?></textarea></td>
	</tr>
   <tr>
   <td></td>
   <td><input type="submit" name="attachment" value="View Attachment"/>   </td>
   </tr>

   </table>
	
  <p><strong>Notes:</strong></p>
  (1) This form should be submitted to MSU Graduate School of Management (GSM) upon completing of the Research Methodology and before student starts the project.<br/>
  (2) Students are advised to seek the lecturer's advice before proceeding with the proposal.<br/>
  (3) Student should plan on 6-month's time from the Official Approval Date to complete the Final Project.<br/>
  (4) As refer to MBA rules, No candidate with CGPA below 3.0 shall be eligible to register for the Final Project of the degree unless recommended by the Board of Examiners.<br/>
  (5) Appointment of supervisor is subject to the recommendation from the Director of MSU Graduate School of Management (GSM).<br/>
   
  
<p><strong>  Outline of Proposed Research/Case Study</strong></p>
 <table >
    <tr>
	  <td>Thesis ID </td>
	  <td><input type="text" name="thesis_id" cols="100" rows="3" id="thesis_id" readonly value="<?php echo $thesis_id; ?>"> </td>
	  </tr>
    <tr>
    <tr>
      <td >Topic of Final Project</td>
      <td ><input type="text" name="thesis_title" cols="100" rows="3" id="thesis_title" value="<?php echo $thesis_title;?>"></td>
      
    </tr>
	
      <td>Propose</td>
      <td><p>
	  	<?php if($thesis_type=='Research')	{	?>
			<input type=radio name=thesis_type value=Research checked> Research
			<input type=radio name=thesis_type value=Case Study> Case Study
					
		<?php	}	else	{	?>
			<input type=radio name=thesis_type value=Research> Research
			<input type=radio name=thesis_type value=Case Study checked> Case Study
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
	  <td><textarea name="introduction" cols="30" rows="3" class="ckeditor" > <?php echo $introduction; ?></textarea></td>
	</tr>
    <tr>
      <td>Objective</td>
      <td><textarea name="objective" cols="30" rows="3" class="ckeditor" > <?php echo $objective; ?></textarea></td>
	  
    </tr>
    <tr>
      <td>Brief Description of Research/Case Study </td>
      <td><textarea name="description" cols="30" rows="3" class="ckeditor"> <?php echo $description; ?></textarea></td>
	  
    </tr>
  </table>
  <strong>Discussion Details </strong><br />
   <table>
     <tr>
      <td><p>Have you discussed about your research/case to any Lecturer of MSU?  </p>       </td>
	   <td><p>
	  	<?php if($discussion_status=='Yes')	{	?>
			<input type=radio name=discussion_status value=Yes checked> Yes
			<input type=radio name=discussion_status value=No Study> No
					
		<?php	}	else	{	?>
			<input type=radio name=discussion_status value=Yes> Yes
			<input type=radio name=discussion_status value=No checked> No
		<?php	}	?>  
	     <?php /*?> <label><input type="radio" name="discussionConfirmation" value="yes" />Yes</label>
	    <label><input type="radio" name="discussionConfirmation" value="no" />No</label><?php */?>
	   </p></td>
    </tr>
	</table>

	<?php /*?><table>
	<tr>
	<td>If yes, please give his/her name </td>
	<td><input type="text" name="lecturer_name" size="30" id="lecturer_name" value="<?=$lecturer_name?>" ></td>
	</tr>
	</table><?php */?>
<br />

	<fieldset>
	 	<legend><strong>Discussion Date</strong></legend>
		 <p>
	 	<a href="javascript:void(0)" class="add-certification add_btn" ;>ADD MORE DATE</a>&nbsp; 
           <a href="javascript:void(0)" class="remove-certification del_btn" ;>DELETE DATE</a>
	 </p>
	 <table border="1" cellpadding="3" cellspacing="3" width="100%" id="inputs10" class="thetable">
		<tr>
			 <th width="11%"><input type="checkbox" id="selectall_certificate" /></th>
			 <th width="14%">Date</th>
			 <th width="15%">Time</th>
			 <th width="22%">Lecturer Name</th>
			 <th width="38%">Remarks</th>
			 
		 </tr>
<?php /*?>	 <tr>
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
	 </tr><?php */?>
  </table>
   </fieldset>
   
   <div>
 <?php /*?>  <table>
   <tr>
   <td>Attach the updated thesis proposal here   </td>
   <td><input type="submit" name="attachment" value="Attachment"/>   </td>
   </tr>
   <tr>
   <td>Remarks</td>
   <td><textarea name="remarks" cols="50" id="remarks"></textarea></td>
   </tr>
   </table><?php */?>
     <fieldset>
		   <legend><strong>DOCUMENT UPLOAD</strong></legend>
					<a href="javascript:void(0)" class="add-file add_btn">Add File</a> 
					<a href="javascript:void(0)" class="remove-file del_btn">Delete File</a>
							
					<table border="1" cellpadding="3" cellspacing="3" width="100%" id="inputs9" class="thetable">
						<tr>
							<th><input type="checkbox" id="selectall_file" /></th>
							<th><span class="labeling">Document Name</span></th>
							<th><span class="labeling">Upload File</span></th>
							<th><span class="labeling">Download</span></th>
						</tr>
							
					<?php
							$sql="SELECT SELECT * FROM pg_thesis pt 
									LEFT JOIN pg_proposal pp ON (pp.id=pt.id)
									WHERE pt.student_matrix_no='$user_id' ";			
							$result = $db_klas2->query($sql); //echo $sql;
							$varRecCount=0;					
							while($row = mysql_fetch_array($result)) 					
							{ 
								$varRecCount++;
								echo "<tr>
										<td align=\"center\"><input type=\"checkbox\" class=\"case_file\" name=\"cbDelFile[]\" /><input type=\"hidden\" name=\"delFile[]\" value=\"".$row["document_id"]."\" /></td>
										<td align=\"center\"><input type=\"text\" name=\"file_name[]\" size=\"40\"value=\"".$row["file_name"]."\" /></td>
										<td align=\"center\"><input type=\"file\" name=\"fileData[]\" size=\"40\" value=\"\" /></td>
										<td align=\"left\"><a href=\"download.php?id=".$row["document_id"]."\" target=\"_blank\" onmouseover=\"toolTip('".$row["document_fileName"]."', 300)\" onmouseout=\"toolTip()\" align=\"center\">".$row["document_fileName"]."
										<img src=\"images/file.gif\" width=\"20\" height=\"19\" style=\"border:0px;\" title=\"Download\"></a></td>			
									</tr>";
							}
		
							if($varRecCount==0)
							{
								echo "<tr>
										<td align=\"center\"><input type=\"checkbox\" class=\"case_file\" name=\"cbDelFile[]\" /></td>
										<td align=\"center\"><input type=\"text\" name=\"file_name[]\" size=\"40\" /></td>
										<td align=\"left\"><input type=\"file\" name=\"fileData[]\" size=\"40\" /></td>
									</tr>";
							}
						
						?>
					</table>
		</fieldset>
   </div>
	<p>
     <label>
     <input type="submit" name="submit" value="Print" />
     </label>
   <input type="submit" name="btnSave" id="btnSave" align="center"  value="<?php echo $varBtnNm; ?>" />
   <td valign="middle" class="tbmenu"><input type="button" name="btnClose" id="btnClose" value="Close" onClick="javascript:window.close();"></td>
   </p>
   </fieldset>
  </form>
</body>
</html>




