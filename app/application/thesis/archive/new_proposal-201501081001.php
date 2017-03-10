<?php

include("../../../lib/common.php");
checkLogin();

session_start();
$userid=$_REQUEST['$userid'];
$studentMatrixNo=$_REQUEST['$pid'];


function runnum($column_name, $tblname) 
{ 
    global $db_klas2;
    
    $run_start = "001";
    
    $sql_slct_max = "SELECT MAX(SUBSTR($column_name,2,11)) AS run_id FROM $tblname";
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

function runnum2($column_name, $tblname) 
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

if(isset($_POST['btnSave']) && ($_POST['btnSave'] <> ""))
{	
	//echo $thesis_id=$dbsel->f("semester_id")+$run_start;
	$curdatetime = date("Y-m-d H:i:s");
	$thesis_id = "T".runnum('id','pg_thesis');
	$proposal_id = "P".runnum('id','pg_proposal');
	
	$sqlsubmit2="INSERT INTO pg_thesis(id,status, student_matrix_no,proposal_date,insert_by,insert_date, modify_by,modify_date,ref_thesis_status_id_proposal)
				VALUES('$thesis_id','INP','$user_id','$curdatetime','$user_id','$curdatetime','$user_id','$curdatetime','INP') ";
	 $db_klas2->query($sqlsubmit2); 
	 //echo "sqlsubmit2 ".$sqlsubmit2;
	 
	 $sqlsubmit="INSERT INTO pg_proposal(id,thesis_title,thesis_type,introduction,objective,description,insert_by,insert_date,modify_by,modify_date,discussion_status,
	 pg_thesis_id, confirm_status, report_date)
				VALUES('$proposal_id','$thesis_title','$thesis_type','$introduction','$objective','$description','$user_id','$curdatetime','$user_id','$curdatetime','$discussion_status','$thesis_id','INP', '$curdatetime') ";
	$db_klas2->query($sqlsubmit); 
	//echo "sqlsubmit ".$sqlsubmit;
	 	
	//to add record meeting into db
	for ($i=0; $i<sizeof($_POST['date']); $i++) {
		
		$meeting_detail_id = runnum2('id','pg_meeting_detail');	
		
		echo $sqlMeeting = "INSERT INTO pg_meeting_detail(
						id, 
						lecturer_name,
						meeting_sdate,
						remark,
						pg_proposal_id,
						pg_thesis_progress_id,
						insert_by,
						insert_date,
						modify_by,
						modify_date)
						VALUES (
						'$meeting_detail_id',
						'" . $_POST['lecturer_name'][$i] . "',
						'" . $_POST['date'][$i] . " ".$_POST['meeting_time'][$i]."', 
						'" . $_POST['remarks'][$i] . "',
						'$proposal_id',
						'$thesis_id',
						'$user_id',
						'$curdatetime',
						'$user_id',
						'$curdatetime')";
		$db_klas2->query($sqlMeeting); 
		//echo "sqlMeeting ".$sqlMeeting;
	}

	/*echo "<pre>";
	print_r($_FILES);
	echo "</pre>";*/
	
	//to upload doc into db
	for ($i=0; $i<sizeof($_FILES['fileData']['name']); $i++) {
			
		// running no doc attachment
		$upload_id = runnum2('fu_cd','file_upload_proposal');
		
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
							'$thesis_id')";
				//$db_klas2->query($sqlUpload);
				//echo $sqlUpload;	
	}
}
?>	


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
    <link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
   	<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
	<link rel="stylesheet" href="../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
	<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
	<script src="../../../lib/js/jquery.min2.js"></script>
    <!--<script type="text/javascript" src="http://his.msu.edu.my/lib/js/ckeditor/ckeditor.js"></script>-->
    <script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
	<script type="text/javascript" src="//cdn.ckeditor.com/4.4.6/standard/ckeditor.js"></script>
</head>
<body>

<script>
$(function() {
	$( "#datepickerFirst" ).datepicker({
		changeMonth: true,
		changeYear: true,
		yearRange: '-100:+0',
		dateFormat: 'dd-mm-yy'
		});
});
</script>

<script type="text/javascript">
$(function() {

	var i = $('input').size() + 1;
	
	//###################################### funtion add more document files ######################################//
	$('a.add-file').click(function() {

		$('<tr><td align="center"><input type="checkbox" class="case_file" /></td><td align=\"center\"><input type=\"text\" name=\"file_name[]\" size="40" /></td><td align="center"><input name="fileData[]" type="file" size="40"/></td></tr>').animate({ opacity: "show" }, "slow").appendTo('#inputs9');
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

	$('<tr><td align="center"><input type="checkbox" name="box[]" class="case_certificate" /></td><td><input type="text" name="date[]" size="15" readonly id="datepicker'+i+'" /></td><td width="66" height="1" class="tbmain"><select name="meeting_time[]" size="1" ><option value=\"\"></option><option value="07:00">07:00 AM</option><option value="07:30">07:30 AM</option><option value="08:00">08:00 AM</option><option value="08:30">08:30 AM</option><option value="09:00">09:00 AM</option><option value="09:30">09:30 AM</option><option value="10:00">10:00 AM</option><option value="10:30">10:30 AM</option><option value="11:00">11:00 AM</option><option value="11:30">11:30 AM</option><option value="12:00">12:00 PM</option><option value="12:30">12:30 PM</option><option value="13:00">01:00 PM</option><option value="13:30">01:30 PM</option><option value="14:00">02:00 PM</option><option value="14:30">02:30 PM</option><option value="15:00">03:00 PM</option><option value="15:30">03:30 PM</option><option value="16:00">04:00 PM</option><option value="16:30">04:30 PM</option><option value="17:00">05:00 PM</option><option value="17:30">05:30 PM</option><option value="18:00">06:00 PM</option><option value="18:30">06:30 PM</option><option value="19:00">07:00 PM</option><option value="19:30">07:30 PM</option><option value="20:00">08:00 PM</option><option value="20:30">08:30 PM</option><option value="21:00">09:00 PM</option><option value="21:30">09:30 PM</option><option value="22:00">10:00 PM</option><option value="22:30">10:30 PM</option><option value="23:00">11:00 PM</option><option value="23:30">11:30 PM</option><option value="24:00">12:00 PM</option></select></td><td><input type="text" name="lecturer_name[]" size="40" /></td><td><textarea name="remarks[]" cols="100" id="remarks"></textarea></td></tr>').animate({ opacity: "show" }, "slow").appendTo('#inputs10');

	$("#datepicker"+i).datepicker({
		changeMonth: true,
		changeYear: true,
		yearRange: '-100:+0',
		dateFormat: 'dd-mm-yy'
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
	
<table>
	<tr>
		<td><strong>New Thesis Proposal</strong></td>
	</tr>
		<td><strong>Outline of Proposed Research/Case Study</strong></td>
</table>
<table>
	<tr>
		<td>Thesis / Project Title</td>
		<td><input type="text" name="thesis_title" size="100" rows="3"  id="thesis_title" value="<?=$thesis_title?>"></td>
	</tr>
	<tr>
		<td>Proposal Type</td>		
		<td><input name="thesis_type" type=radio value="R" checked="checked"> 
		Research
		<input type=radio name="thesis_type" value="C"> Case Study
		</td>
	</tr>
	<tr>
		<td>Introduction</td>
		<td><textarea name="introduction" cols="30" class="ckeditor" rows="3" id="introduction" value="<?=$introduction?>"></textarea></td>
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
<table>
	<tr>
		<td><p>Have you discussed about your research/case study to any lecturer of MSU? </p></td>
		<td><p>
		<input type=radio name="discussion_status" value="Y"> Yes
		<input name="discussion_status" type=radio value="N" checked="checked"> 
		No
		</p></td>
	</tr>
</table>
	 <fieldset>
		<legend><strong>Discussion Details</strong></legend>	 
	 <p>
	 	<a href="javascript:void(0)" class="add-certification add_btn" ;>Add</a>&nbsp; 
        <a href="javascript:void(0)" class="remove-certification delete" ;>Delete</a>
	 </p>
	 <table border="1" cellpadding="3" cellspacing="3" width="100%" id="inputs10" class="thetable">
	 <tr>
		 <th width="11%"><input type="checkbox" name="box[]" id="selectall_file" /></th>
		 <th width="14%">Date</th>
		 <th width="15%">Time</th>
		 <th width="22%">Lecturer Name</th>
		 <th width="38%">Remarks</th>
	 </tr>
	
	 <?php
							$sqlMeeting="SELECT SELECT pmd.id,pmd.lecturer_name, DATE_FORMAT(pmd.meeting_sdate,'%d-%m-%Y') as date,
									 DATE_FORMAT(pmd.meeting_sdate,'%h:%i:%s %p') as time,pmd.remark,pmd.insert_by,pmd.insert_date FROM  pg_meeting_detail pmd
							WHERE pmd.pg_proposal_id='$proposal_id' ORDER BY pmd.pg_proposal_id DESC ";			
							$result = $db_klas2->query($sqlMeeting); //echo $sql;
							$varRecCount=0;					
							while($row = mysql_fetch_array($result)) 					
							{ 
								$varRecCount++;
								echo "<tr>
										<td align=\"center\"><input type=\"checkbox\" class=\"case_certificate\" name=\"cbDelFile[]\" /><input type=\"hidden\" name=\"delFile[]\" value=\"".$row["document_id"]."\" /></td>
										<td align=\"center\"><input type=\"text\" name=\"date[]\" size=\"15\"value=\"".$row["date"]."\" /></td>
										<td align=\"center\"><input type=\"text\" name=\"meeting_time[]\" size=\"15\"value=\"".$row["time"]."\" /></td>
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
		   <p>
					<a href="javascript:void(0)" class="add-file add_btn">Add </a> &nbsp; 
					<a href="javascript:void(0)" class="remove-file del_btn">Delete </a> </p>
							
					<table border="1" cellpadding="3" cellspacing="3" width="100%" id="inputs9" class="thetable">
						<tr>
							<th><input type="checkbox" id="selectall_certificate" /></th>
							<th><span class="labeling">File Name</span></th>
							<th><span class="labeling">Upload File</span></th>
							<?php /*?><th><span class="labeling">Download</span></th><?php */?>
						</tr>
							
					<?php
						$sql="SELECT * FROM file_upload_proposal WHERE pg_proposal_id='$thesis_id' ";			
							$result = $db_klas2->query($sql); //echo $sql;
							$varRecCount=0;					
							while($row = mysql_fetch_array($result)) 					
							{ 
								$varRecCount++;
								echo "<tr>
										<td align=\"center\"><input type=\"checkbox\" class=\"case_file\" name=\"cbDelFile[]\" /><input type=\"hidden\" name=\"delFile[]\" value=\"".$row["document_id"]."\" /></td>
										<td align=\"center\"><input type=\"text\" name=\"file_name[]\" size=\"40\"value=\"".$row["file_name"]."\" /></td>
										<td align=\"center\"><input type=\"file\" name=\"fileData[]\" size=\"40\" value=\"".$row["fu_document_filename"]."\" /></td>			
									</tr>";
							}							
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
   <td><input type="file" name="attachment" value="Attachment"/></td>
   </tr>
    <tr>
   <td>Remarks
   </td>
   <td><textarea name="remarks" cols="50" id="remarks"></textarea></td>
   </tr>
   </table><?php */?>
   </div>
     <table>
		<tr>
			<td><input type="submit" name="btnSave" id="btnSave" align="center"  value="Submit" /></td>
			<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='submit_proposal.php';" /></td>		
		</tr>
	</table>
  </fieldset>
  </form>
</body>
</html>




