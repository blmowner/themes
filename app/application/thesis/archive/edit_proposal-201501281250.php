<?php

include("../../../lib/common.php");
checkLogin();

session_start();
$thesis_id=$_SESSION["thesis_id"];
$proposal_id=$_SESSION["proposal_id"];


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
	$curdatetime = date("Y-m-d H:i:s");
	if (strcmp($_POST[verified_status],'REQ')==0) {
		$new_proposal_id = "P".runnum('id','pg_proposal');
		
		 $sql1="INSERT INTO pg_proposal
				(id,thesis_title,thesis_type,introduction,objective,description,discussion_status,proposal_remarks,
				pg_thesis_id, verified_status, status, report_date, insert_by,insert_date,modify_by,modify_date)
				VALUES('$new_proposal_id','$thesis_title','$thesis_type','$introduction','$objective','$description','$discussion_status',
				'$proposalRemarks','$thesis_id','SAV', 'OPN', '$curdatetime','$user_id','$curdatetime','$user_id','$curdatetime') ";
				
		$db_klas2->query($sql1); 
		
		 $sql2 = "UPDATE pg_thesis
				SET modify_by = '$user_id', modify_date = '$curdatetime', ref_thesis_status_id_proposal = 'INP'
				WHERE id = '$thesis_id'";
				
		$db_klas2->query($sql2);

		$sql3 = "UPDATE pg_proposal 		
				SET archived_status = 'ARC', archived_date = '$curdatetime' 
				WHERE id = '$proposal_id'
				AND verified_status = 'REQ'";

		$db_klas2->query($sql3);
		
		 $sqlMeeting2 = "UPDATE pg_meeting_detail
					SET pg_proposal_id =  '$new_proposal_id'
					WHERE pg_proposal_id = '$proposal_id'";

		$result_sqlMeeting2 = $dbg->query($sqlMeeting2); 	
		
		 $sqlUpload2 = "select fu_cd
					FROM file_upload_proposal
					WHERE pg_proposal_id = '$proposal_id'
					ORDER BY fu_cd";
		
		$result_sqlUpload2 = $dbg->query($sqlUpload2); 			

		$row_cnt = mysql_num_rows($result_sqlUpload2);
		if ($row_cnt>0) {							
			while ($dbg->next_record()) {			
				$fuCdTmp=$dbg->f('fu_cd');
				
				$sqlUpload3 = "UPDATE file_upload_proposal 
							set pg_proposal_id = '$new_proposal_id'
						WHERE fu_cd = '$fuCdTmp'";
				
				$result_sqlUpload3 = $dba->query($sqlUpload3);
				$dba->next_record();			
			};		
		} 
		
	}
	else {//SAV
		$sql4 = "UPDATE pg_proposal
				SET thesis_title = '$thesis_title', thesis_type = '$thesis_type', introduction = '$introduction', 
				objective = '$objective', description = '$description', modify_by = '$user_id', modify_date = '$curdatetime',
				discussion_status = '$discussion_status', verified_status = 'SAV', report_date = '$curdatetime', 
				proposal_remarks = '$proposalRemarks'
				WHERE id = '$proposal_id'
				AND verified_status = 'SAV'";

		$db_klas2->query($sql4);
		
		$sql5 = "UPDATE pg_thesis
				SET modify_by = '$user_id', modify_date = '$curdatetime', ref_thesis_status_id_proposal = 'INP'
				WHERE id = '$thesis_id'";
				
		$db_klas2->query($sql5);
		
	}
	
	for ($i=0; $i<sizeof($_POST['date']); $i++) {
		
		$meeting_detail_id = runnum2('id','pg_meeting_detail');	
		
		$tmpDate = date_create($_POST['date'][$i]." ".$_POST['meeting_time'][$i]);
		$newDate = date_format($tmpDate, 'Y-m-d H:i:s');
		$sqlMeeting1 = "INSERT INTO pg_meeting_detail(
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
						'" . $newDate ."', 
						'" . $_POST['remarks'][$i] . "',
						'$proposal_id',
						null,
						'$user_id',
						'$curdatetime',
						'$user_id',
						'$curdatetime')";
		$db_klas2->query($sqlMeeting1); 
	}

	for ($i=0; $i<sizeof($_FILES['fileData']['name']); $i++) {
			
		$upload_id = runnum2('fu_cd','file_upload_proposal');
		
		$file_name = $_FILES['fileData']['name'][$i];
		$fileType = $_FILES['fileData']['type'][$i];
		$fileSize = intval($_FILES['fileData']['size'][$i]);
		$fileData = file_get_contents($_FILES['fileData']['tmp_name'][$i]);
		
				
		$sqlUpload1 = "INSERT INTO file_upload_proposal (
							fu_cd, 
							fu_document_filename, 
							fu_document_filetype, 
							fu_document_filedata,
							fu_document_thumbnail,
							insert_by,
							insert_date,
							modify_by,
							modify_date,
							pg_proposal_id,
							attachment_level)
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
							'$proposal_id','S')";
				$db_klas2->query($sqlUpload1);
	}
	
	
}

if(isset($_POST['btnSubmit']) && ($_POST['btnSubmit'] <> ""))
{	
	$curdatetime = date("Y-m-d H:i:s");
	if (strcmp($_POST[verified_status],'REQ')==0) 
	{		
		$new_proposal_id = "P".runnum('id','pg_proposal');
		
		 $sql1="INSERT INTO pg_proposal
				(id,thesis_title,thesis_type,introduction,objective,description,discussion_status,proposal_remarks,
				pg_thesis_id, verified_status, status, report_date, insert_by,insert_date,modify_by,modify_date)
				VALUES('$new_proposal_id','$thesis_title','$thesis_type','$introduction','$objective','$description','$discussion_status',
				'$proposalRemarks','$thesis_id','INP', 'OPN', '$curdatetime','$user_id','$curdatetime','$user_id','$curdatetime') ";
				
		$db_klas2->query($sql1); 
		
		 $sql2 = "UPDATE pg_thesis
				SET modify_by = '$user_id', modify_date = '$curdatetime', ref_thesis_status_id_proposal = 'INP'
				WHERE id = '$thesis_id'";
				
		$db_klas2->query($sql2);

		$sql3 = "UPDATE pg_proposal 		
				SET archived_status = 'ARC', archived_date = '$curdatetime' 
				WHERE id = '$proposal_id'
				AND verified_status = 'REQ'";

		$db_klas2->query($sql3);
		
		 $sqlMeeting2 = "UPDATE pg_meeting_detail
					SET pg_proposal_id =  '$new_proposal_id'
					WHERE pg_proposal_id = '$proposal_id'";

		$result_sqlMeeting2 = $dbg->query($sqlMeeting2); 	
		
		 $sqlUpload2 = "select fu_cd
					FROM file_upload_proposal
					WHERE pg_proposal_id = '$proposal_id'
					ORDER BY fu_cd";
		
		$result_sqlUpload2 = $dbg->query($sqlUpload2); 			

		$row_cnt = mysql_num_rows($result_sqlUpload2);
		if ($row_cnt>0) {							
			while ($dbg->next_record()) {			
				$fuCdTmp=$dbg->f('fu_cd');
				
				$sqlUpload3 = "UPDATE file_upload_proposal 
							set pg_proposal_id = '$new_proposal_id'
						WHERE fu_cd = '$fuCdTmp'";
				
				$result_sqlUpload3 = $dba->query($sqlUpload3);
				$dba->next_record();			
			};		
		} 
	}
	else //SAV
	{
		$sql6 = "UPDATE pg_proposal
				SET thesis_title = '$thesis_title', thesis_type = '$thesis_type', introduction = '$introduction', 
				objective = '$objective', description = '$description', modify_by = '$user_id', modify_date = '$curdatetime',
				discussion_status = '$discussion_status', verified_status = 'INP', report_date = '$curdatetime', 
				proposal_remarks = '$proposalRemarks'
				WHERE id = '$proposal_id'
				AND verified_status = 'SAV'";

		$db_klas2->query($sql6);
		
		$sql7 = "UPDATE pg_thesis
				SET modify_by = '$user_id', modify_date = '$curdatetime', ref_thesis_status_id_proposal = 'INP'
				WHERE id = '$thesis_id'";
				
		$db_klas2->query($sql7);
	}
	
	for ($i=0; $i<sizeof($_POST['date']); $i++) {
		
		$meeting_detail_id = runnum2('id','pg_meeting_detail');	
		
		$tmpDate = date_create($_POST['date'][$i]." ".$_POST['meeting_time'][$i]);
		$newDate = date_format($tmpDate, 'Y-m-d H:i:s');
		$sqlMeeting2 = "INSERT INTO pg_meeting_detail(
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
						'" . $newDate ."', 
						'" . $_POST['remarks'][$i] . "',
						'$proposal_id',
						null,
						'$user_id',
						'$curdatetime',
						'$user_id',
						'$curdatetime')";
		$db_klas2->query($sqlMeeting2); 
	}

	for ($i=0; $i<sizeof($_FILES['fileData']['name']); $i++) {
			
		$upload_id = runnum2('fu_cd','file_upload_proposal');
		
		$file_name = $_FILES['fileData']['name'][$i];
		$fileType = $_FILES['fileData']['type'][$i];
		$fileSize = intval($_FILES['fileData']['size'][$i]);
		$fileData = file_get_contents($_FILES['fileData']['tmp_name'][$i]);
		
				
		$sqlUpload2 = "INSERT INTO file_upload_proposal (
							fu_cd, 
							fu_document_filename, 
							fu_document_filetype, 
							fu_document_filedata,
							fu_document_thumbnail,
							insert_by,
							insert_date,
							modify_by,
							modify_date,
							pg_proposal_id,
							attachment_level)
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
							'$proposal_id','S')";
				$db_klas2->query($sqlUpload2);
	}
}

?>	
	

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>PGTMCS - Edit Proposal</title>
    <link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
	<link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
   	<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
	<script src="../../../lib/js/jquery.min2.js"></script>
    <script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>	
</head>
<body>
  
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

		$('<tr><td width="40"><input type="checkbox" class="case_file" /></td><td width="30"><input type=\"text\" name=\"file_name[]\" size="40" /></td><td width="30"><input name="fileData[]" type="file" size="40"/></td><td width="30"><label size="30"></label></td></tr>').animate({ opacity: "show" }, "slow").appendTo('#inputs9');
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

	$('<tr><td width="26" align="center"><input type="checkbox" name="cbDelFile2" class="case_certificate" /></td><td align="center" width="77" ><input type="text" name="date[]" readonly id="datepicker'+i+'" /></td><td width="88" height="1" class="tbmain"><select name="meeting_time[]" size="1" ><option value="" selected></option><option value="07:00">07:00 AM</option><option value="07:30">07:30 AM</option><option value="08:00">08:00 AM</option><option value="08:30">08:30 AM</option><option value="09:00">09:00 AM</option><option value="09:30">09:30 AM</option><option value="10:00">10:00 AM</option><option value="10:30">10:30 AM</option><option value="11:00">11:00 AM</option><option value="11:30">11:30 AM</option><option value="12:00">12:00 PM</option><option value="12:30">12:30 PM</option><option value="01:00">01:00 PM</option><option value="01:30">01:30 PM</option><option value="02:00">02:00 PM</option><option value="02:30">02:30 PM</option><option value="03:00">03:00 PM</option><option value="03:30">03:30 PM</option><option value="04:00">04:00 PM</option><option value="04:30">04:30 PM</option><option value="05:00">05:00 PM</option><option value="05:30">05:30 PM</option><option value="06:00">06:00 PM</option><option value="06:30">06:30 PM</option><option value="07:00">07:00 PM</option><option value="07:30">07:30 PM</option><option value="08:00">08:00 PM</option><option value="08:30">08:30 PM</option><option value="09:00">09:00 PM</option><option value="09:30">09:30 PM</option><option value="10:00">10:00 PM</option><option value="10:30">10:30 PM</option><option value="11:00">11:00 PM</option><option value="11:30">11:30 PM</option><option value="12:00">12:00 PM</option></select></td><td width="170"><input type="text" name="lecturer_name[]"/></td><td width="257"><textarea name="remarks[]" cols="50" id="remarks"></textarea></td><td width="29"><label></label></td></tr>').animate({ opacity: "show" }, "slow").appendTo('#inputs10');

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
  <?

	$sql_thesis="SELECT pt.id as thesis_id, pp.id as proposal_id, 
				pt.student_matrix_no,pp.thesis_title,pp.thesis_type,pp.introduction, pp.objective, 
				pp.description,pp.discussion_status,pmd.id,pmd.lecturer_name, 
				pmd.meeting_sdate,pmd.meeting_edate,pmd.remark,pmd.insert_by,pmd.insert_date,
				pp.verified_status as verified_status,pt.status,
				DATE_FORMAT(pp.verified_date,'%d-%b-%Y') as verified_date, pp.verified_remarks, 
				rps1.description as proposal_description, pp.verified_by, ne.name as verified_name, pp.proposal_remarks 
				FROM pg_thesis pt
				LEFT JOIN pg_proposal pp ON (pp.pg_thesis_id=pt.id)
				LEFT JOIN pg_meeting_detail pmd ON (pmd.id=pp.id)
				LEFT JOIN ref_proposal_status rps ON (rps.id = pp.status)
				LEFT JOIN ref_proposal_status rps1 ON (rps1.id = pp.verified_status)
				LEFT JOIN new_employee ne ON (ne.empid = pp.verified_by)
				WHERE pt.student_matrix_no = '$user_id'
				AND pp.verified_status in ('SAV','INP','APP','REQ','DIS')
				AND pp.archived_status IS NULL
				AND pt.ref_thesis_status_id_proposal = 'INP'
				ORDER BY pt.id";

	$db_klas2->query($sql_thesis);
	$row_area = $db_klas2->fetchArray();
	
	$thesis_id=$row_area['thesis_id'];
	$proposal_id=$row_area['proposal_id'];
	$thesis_title=$row_area['thesis_title'];
	$thesis_type=$row_area['thesis_type'];
	$proposal_remarks=$row_area['proposal_remarks'];
	$introduction=$row_area['introduction'];
	$objective=$row_area['objective'];
	$description=$row_area['description'];
	$verified_date=$row_area['verified_date'];
	$verified_remarks=$row_area['verified_remarks'];
	$verified_status=$row_area['verified_status'];
	$meeting_sdate=$row_area['meeting_sdate'];
	$meeting_time=$row_area['meeting_time'];
	$lecturer_name=$row_area['lecturer_name'];
	$proposal_description=$row_area['proposal_description'];
	$discussion_status=$row_area['discussion_status'];	

if (strcmp($verified_status,'INP')!=0) 
{
?>
  <fieldset>
  <legend><strong>Edit Thesis Proposal - Outline of Proposed Research/Case Study</strong></legend>
 <table >
    <tr>
		<td>Verification Status by Faculty</td>
		<td>:</td>
		<td><label><?=$proposal_description; ?></label></td>
	</tr>
	<input type="hidden" name="verified_status" value="<?=$verified_status;?>" 
	<tr>
		<td>Thesis ID </td>
		<td>:</td>
		<td><label name="thesis_id" cols="100" rows="3" id="thesis_id" title = "<?=$proposal_id;?>"></label> <?php echo $thesis_id; ?></td>
	</tr>
    <tr>
		<td><span style="color:#FF0000">*</span> Thesis / Project Title</td>
		<td>:</td>
		<td><input name="thesis_title" type="text" id="thesis_title" value="<?php echo $thesis_title;?>" size="100"></td>      
    </tr>
		<td>Proposal Type</td>
		<td>:</td>
		<td>
		<?php if($thesis_type=='R')	{	?>
			<input type="radio" name="thesis_type" value="R" checked> Research
			<input type="radio" name="thesis_type" value="C"> Case Study
					
		<?php	}	else	{	?>
			<input type="radio" name="thesis_type" value="R"> Research
			<input type="radio" name="thesis_type" value="C" checked > Case Study
		<?php	}	?> 
		</td>	  
    </tr>
    <tr>
		<td><span style="color:#FF0000">*</span> Introduction</td>
		<td></td>
		<td><textarea name="introduction" cols="30" rows="3" class="ckeditor" > <?php echo $introduction; ?></textarea></td>
	</tr>
    <tr>
		<td><span style="color:#FF0000">*</span> Objective</td>
		<td></td>
		<td><textarea name="objective" cols="30" rows="3" class="ckeditor" > <?php echo $objective; ?></textarea></td>	  
    </tr>
    <tr>
		<td><span style="color:#FF0000">*</span> Brief Description</td>
		<td></td>
		<td><textarea name="description" cols="30" rows="3" class="ckeditor"> <?php echo $description; ?></textarea></td>	  
    </tr>
  </table>
   <table>
     <tr>
      <td><p>Have you discussed about your research/case to any lecturer of MSU?  </p>       </td>
	   <td><p>
	  	<? if($discussion_status=='Y')	{	?>
			<input type="radio" name="discussion_status" value="Y" checked> Yes
			<input type="radio" name="discussion_status" value="N"> No
					
		<?	}	else	{	?>
			<input type="radio" name="discussion_status" value="Y"> Yes
			<input type="radio" name="discussion_status" value="N" checked> No
		<?	}	?>  
	    
	   </p></td>
    </tr>
	</table>

	
<br />

						<fieldset>
							<legend><strong>Discussion Details</strong></legend>

							<?php ?> <p>
							<a href="javascript:void(0)" class="add-certification add_btn" ;>Add</a>&nbsp; 
							<a href="javascript:void(0)" class="remove-certification del_btn" ;>Delete</a>
							</p><?php ?>
							<table border="1" cellpadding="3" cellspacing="3" width="100%" id="inputs10" class="thetable">
								  <tr align="left">
									<th width="26" align="center"><input type="checkbox" name="cbDelFile22" id="selectall_certificate" /></td>
									<th width="77" align="center">Date</th>
									<th width="88" align="center">Time</th>
									<th width="170">Lecturer Name</th>
									<th width="257">Remarks</th>
									<th width="29">Action</th>
								  </tr>

								<?php
									$sqlMeeting="SELECT pmd.id,pmd.lecturer_name, DATE_FORMAT(pmd.meeting_sdate,'%d/%m/%Y') as date,
									DATE_FORMAT(pmd.meeting_sdate,'%h:%i %p') as time, pmd.remark, pmd.insert_by, pmd.insert_date 
									FROM  pg_meeting_detail pmd  
									WHERE pmd.pg_proposal_id='$proposal_id' 
									ORDER BY pmd.meeting_sdate DESC ";			
									
									$result = $db_klas2->query($sqlMeeting); //echo $sql;
				
										while($row = mysql_fetch_array($result)) 					
										{ 
											?><tr>
													<td align="center" width="30"><input type="checkbox" class="case_certificate" name="cbDelFile[]" /><input type="hidden" name="delFile[]" value="<?=$row['document_id'];?>" /></td>
													<td align="center" width="116"><label name="date[]" ></label><?=$row["date"];?></td>
													<td align="center" width="124"><label name="meeting_time[]"></label><?=$row["time"];?></td>
													<td align="left" width="185"><label name="lecturer_name[]" ></label><?=$row["lecturer_name"];?></td>
													<td align="left" width="340"><label name="remarks[]" size="50" ></label><?=$row["remark"];?></td>		
													<td width="34" align="center"></a>
												<a href="delete_meeting_detail.php?id=<?=$row["id"];?>">
												<img src="../images/delete_calendar.jpg" width="20" height="19" style="border:0px;" title="Delete Meeting Information" ></a></td>		
												</tr>
										<?} ?> 	
							</table>
						</fieldset>
					
					<div>
		   
				   <fieldset>
						<legend><strong>Attachment</strong></legend>
						<?php ?>  <p>
							<a href="javascript:void(0)" class="add-file add_btn">Add </a>&nbsp;  
							<a href="javascript:void(0)" class="remove-file del_btn">Delete </a> </p><?php ?>
									
							<table border="1" cellpadding="3" cellspacing="3" width="60%" id="inputs9" class="thetable">
								<tr>
									<th width="30" align="center"><input type="checkbox" name="cbDelFile" id="selectall_file"/></th>
									<th width="103">File Name</th>
									<th width="171">Upload File</th>							
									<th width="40">Action</th>
								 </tr>
									<?php
									$sqlUpload="SELECT * FROM file_upload_proposal 
									WHERE pg_proposal_id='$proposal_id' 
									AND attachment_level='S' ";			
									
									$result = $db_klas2->query($sqlUpload); //echo $sql;
									$varRecCount=0;					
									while($row = mysql_fetch_array($result)) 					
									{ 
										$varRecCount++;
										?><tr>
												<td align="center"><input type="checkbox" class="case_file" name="cbDelFile[]" /><input type="hidden" name="delFile[]" value="<?=$row["fu_cd"];?>" /></td>
												<td><label name="file_name[]" size="40" ></label><?=$row["fu_document_filename"];?></td>
												<td align="left"></td>
												<td><a href="download.php?fc=<?=$row["fu_cd"];?>&al=S" target="_blank" onMouseOver="toolTip('<?=$row["fu_document_filename"];?>', 300)" onMouseOut="toolTip()" align="center">
												<img src="../images/view_doc.jpg" width="20" height="19" style="border:0px;" title="View document"></a>
												<a href="delete_attachment_proposal.php?fc=<?=$row["fu_cd"];?>&al=S" >
												<img src="../images/delete_doc.jpg" width="20" height="19" style="border:0px;" title="Delete document"></a></td>
											</tr>										
									<?}?>								
							</table>
							<br/>
							<table>
								<tr>
									<td>Remarks</td>
								</tr>
								<tr>
									<td><textarea name="proposalRemarks" id="proposalRemarks" class="ckeditor" cols="50" rows="3"> <?=$proposal_remarks; ?></textarea></td>
								</tr>
							</table>
							
						</fieldset>
				</div>
	<table>
		<tr>
			<?$_SESSION["thesis_id"]=$thesis_id;?>
			<?$_SESSION["proposal_id"]=$proposal_id;?>
			<?$_SESSION["verified_status"]=$verified_status;?>
		   <td><input type="submit" name="btnSave" id="btnSave" align="center"  value="Save as Draft" /></td>
		   <td><input type="submit" name="btnSubmit" id="btnSubmit" align="center"  value="Submit" /></td>
		   <td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='submit_proposal.php';" /></td>
		</tr>
	</table>
   </fieldset>
   
   <?}
else {
	?>
	<fieldset>
		<legend><strong>Notification Message</strong></legend>	
		<table>
			<tr>
				<td>Your thesis proposal has been submitted to the Faculty for verification.</td>
			</tr>
		</table>
	</fieldset>	
	<?
	
}?>
   
  </form>
</body>
</html>



