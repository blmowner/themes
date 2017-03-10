<?php

include("../../../lib/common.php");
checkLogin();

session_start();
$thesis_id=$_SESSION["thesis_id"];

function runnum($column_name, $tblname) 
{ 
    global $db_klas2;
    
    $run_start = "001";
    
    $sql_slct_max = "SELECT MAX(SUBSTR($column_name,2,11)) AS run_id FROM $tblname";
	//echo "sql_slct_max ".$sql_slct_max;
	
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

if(isset($_POST['btnSave']) && ($_POST['btnSave'] <> ""))
{	

	$thesis_id=$_SESSION["thesis_id"];
	$proposal_id=$_SESSION["proposal_id"];
	//echo $thesis_id=$dbsel->f("semester_id")+$run_start;
	$curdatetime = date("Y-m-d H:i:s");
	//$meeting_sdate = date("Y-m-d H:i:s");
	
	$sqlUpdate = "UPDATE pg_thesis
					SET modify_by = '$user_id', modify_date = '$curdatetime', ref_thesis_status_id_proposal='INP', status = 'INP'
					WHERE id = '$thesis_id'";
	echo "sqlUpdate ".$sqlUpdate;
	$db_klas2->query($sqlupdate); 
	
	$sqlupdate1="UPDATE pg_proposal SET
				status='ARC', confirm_status = 'ARC', modify_by = '$user_id', modify_date =  '$curdatetime'
				WHERE id='$proposal_id' ";
	echo "sqlupdate1 ".$sqlupdate1;
	$db_klas2->query($sqlupdate1); 
	
	$new_proposal_id = "P".runnum('id','pg_proposal');
	//echo "new_proposal_id ".$new_proposal_id;
	
	$sqlsubmit="INSERT INTO pg_proposal(id,thesis_title,thesis_type,introduction,objective,description,insert_by,insert_date,modify_by,modify_date,
				discussion_status, pg_thesis_id, confirm_status, report_date)
				VALUES('$new_proposal_id','$thesis_title','$thesis_type','$introduction','$objective','$description','$user_id','$curdatetime','$user_id','$curdatetime',
				'$discussion_status','$thesis_id','INP', '$curdatetime') ";
	echo "sqlsubmit ".$sqlsubmit;
	$db_klas2->query($sqlsubmit); 
	
	
	$sqlDel = "DELETE FROM pg_meeting_detail WHERE pg_proposal_id = '$proposal_id'";
	//echo "sqlDel ".$sqlDel;
	$db_klas2->query($sqlDel);
		
	for ($i=0; $i<sizeof($_POST['date']); $i++) {
		// delete dulu pg_meeting_detail where pg_proposal_id = 'id_die';
		/*$sqlDel = "DELETE FROM pg_meeting_detail WHERE pg_proposal_id = '".addslashes($_REQUEST["thesis_id"])."' ";
		$db_klas2->query($sqlDel);	*/	
		$pg_meeting_detail_id = "P".runnum('id','pg_meeting_detail');
		$sql_meeting_detail = "INSERT INTO pg_meeting_detail(id, lecturer_name,meeting_sdate,remark,pg_proposal_id,insert_by,insert_date,modify_by,modify_date)
				VALUES ('$pg_meeting_detail_id','" . $_POST['lecturer_name'][$i] . "','" . $_POST['date'][$i] . "', '" .  $_POST['remarks'][$i] . "',
				'$new_proposal_id','$user_id','$curdatetime','$user_id','$curdatetime')";
		//echo "sql_meeting_detail ".$sql_meeting_detail;
		$db_klas2->query($sql_meeting_detail); 				 
	}
		
	$sqlDelUpl = "DELETE FROM file_upload_proposal WHERE pg_proposal_id = '$proposal_id' ";
	//echo "sqlDelUpl ".$sqlDelUpl;
	$db_klas2->query($sqlDelUpl);
	
	//to upload doc into db
	for ($i=0; $i<sizeof($_FILES['fileData']['name']); $i++) {
			
		// running no doc attachment
		$upload_id = runnum('fu_cd','file_upload_proposal');
		
		//$tmpFileName = $_FILES['fileData']['tmp_name'][$i];
		$file_name = $_FILES['fileData']['name'][$i];
		$fileType = $_FILES['fileData']['type'][$i];
		//$fileSize = filesize($tmpFileName);
		$fileSize = intval($_FILES['fileData']['size'][$i]);
		//$fileData = addslashes(file_get_contents($tmpFileName));
		$fileData = file_get_contents($_FILES['fileData']['tmp_name'][$i]);
		
				
		$sqlUpload = "INSERT INTO file_upload_proposal (
							fu_cd, 
							fu_document_filename, 
							fu_document_filetype, 
							fu_document_filedata,
							fu_document_thumbnail,
							insert_by,
							insert_date,
							modify_by,
							modify_date,
							pg_proposal_id)
							VALUES (
							'$upload_id',
							'".$file_name."', 
							'".$fileType."',
							'".mysql_escape_string($fileData)."',
							'',
							'$user_id',
							'$curdatetime',
							'$user_id',
							'$curdatetime',
							'$new_proposal_id)";
				//echo $sqlUpload;
				$db_klas2->query($sqlUpload);
				
	
	}
}

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

	$sql_thesis="SELECT pt.id as thesis_id, pp.id as proposal_id, pt.student_matrix_no,pp.thesis_title,pp.thesis_type,pp.introduction, pp.objective, pp.description,pp.discussion_status,pmd.id,pmd.lecturer_name, 
				pmd.meeting_sdate,pmd.meeting_edate,pmd.remark,pmd.insert_by,pmd.insert_date,pp.status as proposal_status,pt.status,
				DATE_FORMAT(pp.feedback_date,'%d-%b-%Y') as feedback_date, pp.feedback_remarks, rps.description as proposal_description, pp.feedback_by, ne.name as feedback_name
				FROM pg_thesis pt
				LEFT JOIN pg_proposal pp ON (pp.pg_thesis_id=pt.id)
				LEFT JOIN pg_meeting_detail pmd ON (pmd.id=pp.id)
				LEFT JOIN ref_proposal_status rps ON (rps.id = pp.status)
				LEFT JOIN new_employee ne ON (ne.empid = pp.feedback_by)
				WHERE pt.student_matrix_no = '$user_id'
				AND pp.confirm_status in ('INP','APP','REQ','DIS')
				AND pt.ref_thesis_status_id_proposal = 'INP'
				ORDER BY pt.id";

	echo "sql_thesis ".$sql_thesis;
	$db_klas2->query($sql_thesis);
	$row_area = $db_klas2->fetchArray();
	
	$thesis_id=$row_area['thesis_id'];
	$proposal_id=$row_area['proposal_id'];
	$thesis_title=$row_area['thesis_title'];
	$thesis_type=$row_area['thesis_type'];
	$remarks=$row_area['remarks'];
	$introduction=$row_area['introduction'];
	$objective=$row_area['objective'];
	$description=$row_area['description'];
	$feedback_date=$row_area['feedback_date'];
	$feedback_remarks=$row_area['feedback_remarks'];
	$status=$row_area['status'];
	$meeting_sdate=$row_area['meeting_sdate'];
	$meeting_time=$row_area['meeting_time'];
	$lecturer_name=$row_area['lecturer_name'];
	$remarks=$row_area['remarks'];

?>

<script>
$(function() {
	$( "#datepickerFirst" ).datepicker({
		changeMonth: true,
		changeYear: true,
		yearRange: '-100:+0',
		dateFormat: 'yy-mm-dd'
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

	$('<tr><td align="center"><input type="checkbox" name="cbDelFile2" class="case_certificate" /></td><td><input type="text" name="date[]" size="15" readonly id="datepicker'+i+'" /></td><td width="66" height="1" class="tbmain"><select name="meeting_time[]" size="1" ><option value=\"\"></option><option value="07:00">07:00 AM</option><option value="07:30">07:30 AM</option><option value="08:00">08:00 AM</option><option value="08:30">08:30 AM</option><option value="09:00">09:00 AM</option><option value="09:30">09:30 AM</option><option value="10:00">10:00 AM</option><option value="10:30">10:30 AM</option><option value="11:00">11:00 AM</option><option value="11:30">11:30 AM</option><option value="12:00">12:00 PM</option><option value="12:30">12:30 PM</option><option value="01:00">01:00 PM</option><option value="01:30">01:30 PM</option><option value="02:00">02:00 PM</option><option value="02:30">02:30 PM</option><option value="03:00">03:00 PM</option><option value="03:30">03:30 PM</option><option value="04:00">04:00 PM</option><option value="04:30">04:30 PM</option><option value="05:00">05:00 PM</option><option value="05:30">05:30 PM</option><option value="06:00">06:00 PM</option><option value="06:30">06:30 PM</option><option value="07:00">07:00 PM</option><option value="07:30">07:30 PM</option><option value="08:00">08:00 PM</option><option value="08:30">08:30 PM</option><option value="09:00">09:00 PM</option><option value="09:30">09:30 PM</option><option value="10:00">10:00 PM</option><option value="10:30">10:30 PM</option><option value="11:00">11:00 PM</option><option value="11:30">11:30 PM</option><option value="12:00">12:00 PM</option></select></td><td><input type="text" name="lecturer_name[]" size="40" /></td><td><textarea name="remarks[]" cols="100" id="remarks"></textarea></td></tr>').animate({ opacity: "show" }, "slow").appendTo('#inputs10');

	$("#datepicker"+i).datepicker({
		changeMonth: true,
		changeYear: true,
		yearRange: '-100:+0',
		dateFormat: 'yy-mm-dd'
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
  
  <fieldset>
  <p><strong>  Outline of Proposed Research/Case Study</strong></p>
 <table >
    <tr>
	  <td>Thesis ID </td>
	  <td><input type="text" name="thesis_id" cols="100" rows="3" id="thesis_id" readonly value="<?php echo $thesis_id; ?>"> </td>
	  </tr>
    <tr>
    <tr>
      <td >Thesis / Project Title</td>
      <td ><input type="text" name="thesis_title" cols="100" rows="3" id="thesis_title" value="<?php echo $thesis_title;?>"></td>
      
    </tr>
	
      <td>Proposal Type</td>
      <td><p>
	  	<?php if($thesis_type=='R')	{	?>
			<input type="radio" name="thesis_type" value="R" checked> Research
			<input type="radio" name="thesis_type" value="C"> Case Study
					
		<?php	}	else	{	?>
			<input type="radio" name="thesis_type" value="R"> Research
			<input type="radio" name="thesis_type" value="C" checked > Case Study
		<?php	}	?> 

     
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
      <td><p>Have you discussed about your research/case to any lecturer of MSU?  </p>       </td>
	   <td><p>
	  	<?php if($discussion_status=='Yes')	{	?>
			<input type="radio" name="discussion_status" value="Y" checked> Yes
			<input type="radio" name="discussion_status" value="N"> No
					
		<?php	}	else	{	?>
			<input type="radio" name="discussion_status" value="Y"> Yes
			<input type="radio" name="discussion_status" value="N" checked> No
		<?php	}	?>  
	    
	   </p></td>
    </tr>
	</table>

	
<br />

	<fieldset>
	 	<legend><strong>Discussion Details</strong></legend>
		 <p>
	 	<a href="javascript:void(0)" class="add-certification add_btn" ;>Add</a>&nbsp; 
           <a href="javascript:void(0)" class="remove-certification del_btn" ;>Delete</a>
	 </p>
	 <table border="1" cellpadding="3" cellspacing="3" width="100%" id="inputs10" class="thetable">
		<tr>
			 <th width="11%"><input type="checkbox" id="selectall_certificate" /></th>
			 <th width="14%">Date</th>
			 <th width="15%">Time</th>
			 <th width="22%">Lecturer Name</th>
			 <th width="38%">Remarks</th>
		</tr>
		
		<?php
							$sqlMeeting="SELECT * FROM pg_meeting_detail pmd
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
							}
		
													
						?>

  </table>
   </fieldset>
   
   <div>

     <fieldset>
		   <legend><strong>Attachment</strong></legend>
					<a href="javascript:void(0)" class="add-file add_btn">Add File</a> 
					<a href="javascript:void(0)" class="remove-file del_btn">Delete File</a>
							
					<table border="1" cellpadding="3" cellspacing="3" width="100%" id="inputs9" class="thetable">
						<tr>
							<th><input type="checkbox" id="selectall_file" /></th>
							<th><span class="labeling">File Name</span></th>
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
   </div>
	<table>
		<tr>
			<?$_SESSION["thesis_id"]=$thesis_id;?>
			<?$_SESSION["proposal_id"]=$proposal_id;?>
		   <td><input type="submit" name="btnSave" id="btnSave" align="center"  value="Submit" /></td>
		   <td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='submit_proposal.php';" /></td>
		</tr>
	</table>
   </fieldset>
  </form>
</body>
</html>




