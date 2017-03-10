<?php

include("../../../lib/common.php");
checkLogin();

	?>

<?
$thesis_id=$_SESSION["thesis_id"];

/*function runnum($column_name, $tblname) 
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

//echo $run_start;

if(isset($_POST['btnSave']) && ($_POST['btnSave'] <> ""))
	{	
		//$sqlselect="SELECT semester_id FROM student_program where matrix_no='$user_id'";
		$sqlselect="SELECT semester_id FROM student_program where matrix_no='$user_id'";
		$dbsel=$db_klas2;
		$dbsel->query($sqlselect); //echo $sql;
		$dbsel->next_record();
		$semester_id=$dbsel->f("semester_id");
		
		//echo $thesis_id=$dbsel->f("semester_id")+$run_start;
		$curdatetime = date("Y-m-d H:i:s");
		$thesis_id = runnum('id','pg_thesis');
		$sqlsubmit="INSERT INTO pg_proposal(id,matrix_no,thesis_title,thesis_type,introduction,objective,description,insert_by,insert_date,modify_by,modify_date,discussion_status)
		 			VALUES('$thesis_id','$user_id','$thesis_title','$thesis_type','$introduction','$objective','$description','$user_id','$curdatetime','$user_id','$curdatetime','$discussion_status') ";
		 $db_klas2->query($sqlsubmit); 
		 
		$sqlsubmit2="INSERT INTO pg_thesis(id,student_matrix_no,proposal_date,modify_by,modify_date,ref_thesis_status_id_proposal)
		 			VALUES('$thesis_id','$user_id','$curdatetime','$user_id','$curdatetime','INP') ";
		 $db_klas2->query($sqlsubmit2); 
		 
		$sqlsubmit3="INSERT INTO pg_meeting_detail(id,lecturer_name,meeting_sdate,meeting_edate,remark,insert_by,insert_date,modify_by,modify_date)
		 			VALUES('$thesis_id','$lecturer_name','$meeting_sdate','','$remark','$user_id','$curdatetime','$user_id','$curdatetime') ";
		 $db_klas3->query($sqlsubmit3); 
		
				
	
	}

$varBtnNm="Submit";*/
?>	


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
    <link rel="stylesheet" type="text/css" href="http://his.msu.edu.my/theme/css/colorbox.css" media="screen" />
   	<link rel="stylesheet" href="http://his.msu.edu.my/lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
	<link rel="stylesheet" href="../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
	<script type="text/javascript" src="http://his.msu.edu.my/lib/js/ckeditor/ckeditor.js"></script>
	<script src="http://his.msu.edu.my/lib/js/jquery.min2.js"></script>
    <script type="text/javascript" src="http://his.msu.edu.my/lib/js/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="http://his.msu.edu.my/lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
</head>
<body>

<?
$sql_thesis="SELECT pt.id as thesis_id, pt.student_matrix_no,pp.thesis_title,pp.thesis_type,pp.introduction, pp.objective, pp.description,pp.discussion_status,pmd.id,pmd.lecturer_name, 
				pmd.meeting_sdate,pmd.meeting_time,pmd.meeting_edate,pmd.remark,pmd.insert_by,pmd.insert_date,pp.status as proposal_status,pt.status,
				pp.feedback_date,pp.feedback_remarks
				FROM pg_thesis pt
				LEFT JOIN pg_proposal pp ON (pp.id=pt.id)
				LEFT JOIN pg_meeting_detail pmd ON (pmd.id=pp.id)
				WHERE pt.student_matrix_no = '$user_id'
				ORDER BY pt.id DESC LIMIT 1";
$db_klas2->query($sql_thesis);
$row_personal=$db_klas2->fetchArray();

$name=$row_personal['name'];
$program_code=$row_personal['program_code'];
$program_e=$row_personal['program_e'];
$ic_passport=$row_personal['ic_passport'];
$address_aa=$row_personal['address_aa'];
$address_ab=$row_personal['address_ab'];
$city_a=$row_personal['city_a'];
$state_a=$row_personal['state_a'];
$postcode_a=$row_personal['postcode_a'];
$country_a=$row_personal['country_a'];
$address_bb=$row_personal['address_bb'];
$address_ba=$row_personal['address_ba'];
$city_b=$row_personal['city_b'];
$state_b=$row_personal['state_b'];
$postcode_b=$row_personal['postcode_b'];
$country_b=$row_personal['country_b'];
$citizenship=$row_personal['citizenship'];
$gender=$row_personal['xgender'];
$intake_no=$row_personal['intake_no'];
$mobile=$row_personal['handphone'];
$house=$row_personal['house'];
$office=$row_personal['office'];
$email=$row_personal['email'];
$thesis_title=$row_personal["thesis_title"];
$cases=$row_personal["thesis_type"];
$introduction=$row_personal["introduction"];
$objective=$row_personal["objective"];
$description=$row_personal["description"];
$proposal_status=$row_personal["proposal_status"];
$status=$row_personal["status"];
$student_status=$row_personal["student_status"];
$status=$row_personal['status'];
$thesis_id=$row_personal['thesis_id'];
$thesis_type=$row_personal['thesis_type'];
$supervisor_id=$row_personal['supervisor_id'];
$supervisor_name=$row_personal['name'];
$email=$row_personal['email'];
$hp_no=$row_personal['hp_no'];
$skype_id=$row_personal['skype_id'];
$feedback_date=$row_personal['feedback_date'];
$feedback_remarks=$row_personal['feedback_remarks'];
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

	$('<tr><td align="center"><input type="checkbox" name="cbDelFile2" class="case_certificate" /></td><td><input type="text" name="date[]" size="15" readonly id="datepicker'+i+'" value="<?php echo $date; ?>"/></td><td width="66" height="1" class="tbmain"><select name="meeting_time[]" size="1" value="<?=$meeting_time?>"><option value=\"\"></option><option value="07:00">07:00 AM</option><option value="07:30">07:30 AM</option><option value="08:00">08:00 AM</option><option value="08:30">08:30 AM</option><option value="09:00">09:00 AM</option><option value="09:30">09:30 AM</option><option value="10:00">10:00 AM</option><option value="10:30">10:30 AM</option><option value="11:00">11:00 AM</option><option value="11:30">11:30 AM</option><option value="12:00">12:00 PM</option><option value="12:30">12:30 PM</option><option value="01:00">01:00 PM</option><option value="01:30">01:30 PM</option><option value="02:00">02:00 PM</option><option value="02:30">02:30 PM</option><option value="03:00">03:00 PM</option><option value="03:30">03:30 PM</option><option value="04:00">04:00 PM</option><option value="04:30">04:30 PM</option><option value="05:00">05:00 PM</option><option value="05:30">05:30 PM</option><option value="06:00">06:00 PM</option><option value="06:30">06:30 PM</option><option value="07:00">07:00 PM</option><option value="07:30">07:30 PM</option><option value="08:00">08:00 PM</option><option value="08:30">08:30 PM</option><option value="09:00">09:00 PM</option><option value="09:30">09:30 PM</option><option value="10:00">10:00 PM</option><option value="10:30">10:30 PM</option><option value="11:00">11:00 PM</option><option value="11:30">11:30 PM</option><option value="12:00">12:00 PM</option></select></td><td><input type="text" name="lecturer_name[]" size="40" value="<?php echo $lecturer_name; ?>"/></td><td><textarea name="remarks[]" cols="100" id="remarks"><?php echo $remark; ?></textarea></td></tr>').animate({ opacity: "show" }, "slow").appendTo('#inputs10');

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

<script>
function getUpdSP(id,frm)
{
	// START EDIT BY AD 2014-08-25
	var winMe = window.open("edit_proposal.php?thesis_id="+id+"&form="+frm,"aform","dependent=no,width=900,height=900,resizable=yes,scrollbars=yes");
	winMe.focus();
}
</script>
  <form id="form1" name="form1" method="post" enctype="multipart/form-data">
  <input type="hidden" name="thesis_id" id="thesis_id" value="<?php echo $thesis_id; ?>">
  
	<fieldset>
	<table>
	<tr>
	 <? if ($proposal_status=="Disapprove" || $proposal_status=="") {?>
		<td valign="middle" align="right">
		<input type="button" name="btnNew" id="btnNew" value="New Thesis Proposal" onClick="javascript:window.open('tp_form.php','aform','dependent=no,width=950,height=500,resizable=yes,scrollbars=yes');">
	 <? } elseif ($proposal_status=="Request with Changes" || $proposal_status=="INP"){?>
		<?php /*?><input type="button" name="btnEdit" id="btnEdit" value="Edit Thesis Proposal" onClick="javascript:window.open('edit_proposal.php?thesis_id='+document.form1.thesis_id.value,'aform','dependent=no,width=950,height=500,resizable=yes,scrollbars=yes');"><?php */?>
		<input type="button" name="btnEdit" id="btnEdit" value="Edit Thesis Proposal" onClick="javascript:getUpdSP('<?=$thesis_id;?>','form1');">
	 <? } else {}?>
		</td>
	</tr>
	</table>
 	<br \>
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
		
		<?php	}	elseif($status=='Disapproved') { ?> 
			<input type=radio name=status value=Approved disabled="disabled"> Approved
			<input type=radio name=status value=Request with Changes disabled="disabled"> Request with Changes
			<input type=radio name=status value=Disapproved checked disabled="disabled"> Disapproved
		
		<?php	}  else { ?> 
			<input type=radio name=status value=Approved disabled="disabled"> Approved
			<input type=radio name=status value=Request with Changes disabled="disabled"> Request with Changes
			<input type=radio name=status value=Disapproved disabled="disabled"> Disapproved
		<?php	}  ?> 
	
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
   
  <p><strong>Notes:</strong><br />
  (1) This form should be submitted to MSU Graduate School of Management (GSM) upon completing of the Research Methodology and before student starts the project.<br />
  (2) Students are advised to seek the lecturer's advice before proceeding with the proposal.<br />
  (3) Student should plan on 6-month's time from the Official Approval Date to complete the Final Project.<br />
  (4) As refer to MBA rules, No candidate with CGPA below 3.0 shall be eligible to register for the Final Project of the degree unless recommended by the Board of Examiners.<br />
  (5) Appointment of supervisor is subject to the recommendation from the Director of MSU Graduate School of Management (GSM).</p>
   
<strong>  Outline of Proposed Research/Case Study</strong>
 <table>
 	<tr>
      <td>Thesis Status</td>
      <td><input type="text" name="proposal_status" cols="100" rows="3" id="proposal_status" readonly value="<?php echo $proposal_status; ?>"></td>
    </tr>
 	<tr>
      <td>Thesis ID</td>
      <td><input type="text" name="thesis_id" cols="100" rows="3" id="thesis_id" readonly value="<?php echo $thesis_id; ?>"></td>
    </tr>
    <tr>
      <td>Topic of Final Project</td>
      <td><input type="text" name="thesis_title" size="100" rows="3" id="thesis_title" value="<?=$thesis_title?>" disabled="disabled"></td>
    </tr>
    <tr>
      <td>Propose</td>
      <td><p>
	  	<?php if($thesis_type=='Research')	{	?>
			<input type=radio name=thesis_type value=Research checked disabled="disabled"> Research
			<input type=radio name=thesis_type value=Case Study disabled="disabled"> Case Study
					
		<?php	}	else	{	?>
			<input type=radio name=thesis_type value=Research disabled="disabled"> Research
			<input type=radio name=thesis_type value=Case Study checked disabled="disabled"> Case Study
		<?php	}	?> 

     
      </p></td>
    </tr>
    <tr>
      <td>Introduction</td>
      <td><textarea name="introduction" cols="30" class="ckeditor" rows="3" disabled="disabled"><?php echo $introduction; ?></textarea></td>
    </tr>
    <tr>
      <td>Objective</td>
      <td><textarea name="objective" cols="30" class="ckeditor" rows="3" disabled="disabled"><?php echo $objective; ?></textarea></td>
    </tr>
    <tr>
      <td>Brief Description of Research/Case Study </td>
      <td><textarea name="description" cols="30" class="ckeditor" rows="3" disabled="disabled"> <?php echo $description; ?></textarea></td>
    </tr>
  </table>
  <strong>Discussion Details </strong><br />
   <table>
     <tr>
      <td><p>Have you discussed about your research/case study to any Lecturer of MSU? </p>       </td>
      <td><p>
	  	<?php if($discussion_status=='Yes')	{	?>
			<input type=radio name=discussion_status value=Yes checked disabled="disabled"> Yes
			<input type=radio name=discussion_status value=No Study disabled="disabled"> No
					
		<?php	}	elseif($discussion_status=='No')	{	?>
			<input type=radio name=discussion_status value=Yes disabled="disabled"> Yes
			<input type=radio name=discussion_status value=No checked disabled="disabled"> No
		<?php	} else { ?> 
			<input type=radio name=discussion_status value=Yes disabled="disabled"> Yes
			<input type=radio name=discussion_status value=No disabled="disabled"> No
		<?php	}  ?> 
   
      </p></td>
    </tr>
  </table>
	<?php /*?><tr>
	<table>
	<td>If yes, please give his/her name </td>
	<td><input type="text" name="lecturer_name" size="30" id="lecturer_name" value="<?=$lecturer_name?>" ></td>
	</tr>
	</table><?php */?>
	 <br />
	 <fieldset>
	 	<legend><strong>Discussion Date</strong></legend>
	 
	<?php /*?> <p>
	 	<a href="javascript:void(0)" class="add-certification add_btn" ;>ADD MORE DATE</a>&nbsp; 
           <a href="javascript:void(0)" class="remove-certification del_btn" ;>DELETE DATE</a>
	 </p><?php */?>
	 <table border="1" cellpadding="3" cellspacing="3" width="100%" id="inputs10" class="thetable">
	 <tr>
	 <th width="11%"><input type="checkbox" name="cbDelFile2" id="selectall_certificate" /></th>
	 <th width="14%">Date</th>
	 <th width="15%">Time</th>
	 <th width="22%">Lecturer Name</th>
	 <th width="38%">Remarks</th>
	 
	 </tr>
	
	 		<?php
						$sqlMeeting="SELECT * FROM  pg_meeting_detail pmd  
							WHERE pmd.pg_proposal_id='$thesis_id' ORDER BY pmd.pg_proposal_id DESC ";			
							$result = $db_klas2->query($sqlMeeting); //echo $sql;
							$varRecCount=0;					
							while($row = mysql_fetch_array($result)) 					
							{ 
								$varRecCount++;
								echo "<tr>
										<td align=\"center\"><input type=\"checkbox\" class=\"case_certificate\" name=\"cbDelFile[]\" /><input type=\"hidden\" name=\"delFile[]\" value=\"".$row["document_id"]."\" /></td>
										<td align=\"center\"><input type=\"text\" name=\"date[]\" size=\"15\"value=\"".$row["meeting_sdate"]."\" /></td>
										<td align=\"center\"><input type=\"text\" name=\"meeting_time[]\" size=\"15\"value=\"".$row["meeting_time"]."\" /></td>
										<td align=\"center\"><input type=\"text\" name=\"lecturer_name[]\" size=\"22\"value=\"".$row["lecturer_name"]."\" /></td>
										<td align=\"center\"><input type=\"text\" name=\"remarks[]\" size=\"40\"value=\"".$row["remark"]."\" /></td>		
									</tr>";
							}  	?>
	 </table>
  </fieldset>
  
   <div>
   
   <fieldset>
		   <legend><strong>DOCUMENT UPLOAD</strong></legend>
		 <?php /*?>  <p>
					<a href="javascript:void(0)" class="add-file add_btn">Add File</a> 
					<a href="javascript:void(0)" class="remove-file del_btn">Delete File</a> </p><?php */?>
							
					<table border="1" cellpadding="3" cellspacing="3" width="100%" id="inputs9" class="thetable">
						<tr>
							<th><input type="checkbox" name="cbDelFile" id="selectall_file" /></th>
							<th><span class="labeling">Document Name</span></th>
							<th><span class="labeling">Upload File</span></th>
							<?php /*?><th><span class="labeling">Download</span></th><?php */?>
						</tr>
							
					<?php
							$sqlUpload="SELECT * FROM file_upload_proposal WHERE pg_proposal_id='$thesis_id' ";			
							$result = $db_klas2->query($sqlUpload); //echo $sql;
							$varRecCount=0;					
							while($row = mysql_fetch_array($result)) 					
							{ 
								$varRecCount++;
								echo "<tr>
										<td align=\"center\"><input type=\"checkbox\" class=\"case_file\" name=\"cbDelFile[]\" /><input type=\"hidden\" name=\"delFile[]\" value=\"".$row["fu_cd"]."\" /></td>
										<td align=\"center\"><input type=\"text\" name=\"file_name[]\" size=\"40\"value=\"".$row["fu_document_filename"]."\" /></td>
										<td align=\"left\"><a href=\"download.php?id=".$row["fu_cd"]."\" target=\"_blank\" onmouseover=\"toolTip('".$row["fu_document_filename"]."', 300)\" onmouseout=\"toolTip()\" align=\"center\">".$row["fu_document_filename"]."
										<img src=\"images/file.gif\" width=\"20\" height=\"19\" style=\"border:0px;\" title=\"Download\"></a></td>		
									</tr>";
							}
		
							/*if($varRecCount==0)
							{
								echo "<tr>
										<td align=\"center\"><input type=\"checkbox\" class=\"case_file\" name=\"cbDelFile[]\" /></td>
										<td align=\"center\"><input type=\"text\" name=\"file_name[]\" size=\"40\" /></td>
										<td align=\"left\"><input type=\"file\" name=\"fileData[]\" size=\"40\" /></td>
											
									</tr>";
							}*/
						
						?>
					</table>
		</fieldset>
			
<?php /*?>   <table>
   <tr>
   <td>Attach the updated thesis proposal here
   </td>
   	<?php
			
					$sql="SELECT * FROM thesis_doc WHERE matrix_no='$user_id' ";			
					$result = $db_klas2->query($sql); //echo $sql;
					$varRecCount=0;					
					while($row = mysql_fetch_array($result)) 					
					{ 
						$varRecCount++;
						echo "<tr>
								<td align=\"center\"><input type=\"checkbox\" class=\"case_file\" name=\"cbDelFile[]\" /><input type=\"hidden\" name=\"delFile[]\" value=\"".$row["document_id"]."\" /></td>
								<td align=\"center\"><input type=\"text\" name=\"description[]\" size=\"40\"value=\"".$row["description"]."\" /></td>
								<td align=\"center\"><input type=\"file\" name=\"fileData[]\" size=\"40\" value=\"\" /></td>
								<td align=\"left\"><a href=\"download.php?id=".$row["document_id"]."\" target=\"_blank\" onmouseover=\"toolTip('".$row["document_fileName"]."', 300)\" onmouseout=\"toolTip()\" align=\"center\">".$row["document_fileName"]."
								<img src=\"images/file.gif\" width=\"20\" height=\"19\" style=\"border:0px;\" title=\"Download\"></a></td>			
							</tr>";
					}

					if($varRecCount==0)
					{
						echo "<tr>
								<td align=\"center\"><input type=\"checkbox\" class=\"case_file\" name=\"cbDelFile[]\" /></td>
								<td align=\"center\"><input type=\"text\" name=\"description[]\" size=\"40\" /></td>
								<td align=\"left\"><input type=\"file\" name=\"fileData[]\" size=\"40\" /></td>
							</tr>";
					}
				
				?>
   </tr>
    
   </table><?php */?>
   </div>
   <p>
     <label></label>
     <input type="submit" name="submit2" align="center" value="Print" />
    <?php /*?> <input type="submit" name="btnSave" id="btnSubmit" align="center"  value="<?php echo $varBtnNm; ?>" /><?php */?>
   </p>
  </fieldset>
  </form>
</body>
</html>




