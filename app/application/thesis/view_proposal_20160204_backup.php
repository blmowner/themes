<?php

include("../../../lib/common.php");
checkLogin();

session_start();
$userid=$_REQUEST['uid'];
$studentMatrixNo=$_REQUEST['uid'];
$pgThesisId=$_REQUEST['tid'];
$pgProposalId=$_REQUEST['pid'];


?>	


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
	<link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
    <link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
   	<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
	<link rel="stylesheet" href="../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
	<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
	<script src="../../../lib/js/jquery.min2.js"></script>
    <script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
	
</head>
<body>



  <form id="form1" name="form1" method="post" enctype="multipart/form-data" onsubmit="return saveRec();">

<?
 $sql_thesis="SELECT pt.id AS thesis_id, pt.student_matrix_no,pt.status as thesis_status,
				pp.id AS proposal_id, pp.thesis_title,pp.thesis_type, pp.objective, pp.introduction,pp.description,pp.discussion_status, 
				DATE_FORMAT(pp.verified_date,'%d-%b-%Y') AS verified_date, pp.verified_remarks, pp.verified_by,
				pp.verified_status,pp.endorsed_by, DATE_FORMAT(pp.endorsed_date,'%d-%m-%y') as endorsed_date, 
				pp.endorsed_remarks, pp.status as endorsement_status,
				rps.description AS proposal_description
				FROM pg_thesis pt 
				LEFT JOIN pg_proposal pp ON (pp.pg_thesis_id=pt.id) 
				LEFT JOIN ref_proposal_status rps ON (rps.id = pp.verified_status)  
				WHERE pt.student_matrix_no = '$user_id'
				AND pp.verified_status IN ('APP','AWC') 
				AND pp.status IN ('APP','APC') 
				AND pt.ref_thesis_status_id_proposal IN ('APP','APC') 
				AND pp.archived_status IS NULL";
				
	$result_sql_thesis = $db->query($sql_thesis);
	$row_area = $db->fetchArray();
	
	$thesis_id=$row_area['thesis_id'];
	$proposal_id=$row_area['proposal_id'];
	$thesis_title=$row_area['thesis_title'];
	$thesis_type=$row_area['thesis_type'];
	$introduction=$row_area['introduction'];
	$objective=$row_area['objective'];
	$description=$row_area['description'];
	$discussion_status=$row_area['discussion_status'];
	$verified_date=$row_area['verified_date'];
	$verified_remarks=$row_area['verified_remarks'];
	$verified_status=$row_area['verified_status'];
	$proposal_description=$row_area['proposal_description'];
	
if (strcmp($verified_status,'INP')!=0)
{
?>
<fieldset>
<legend><strong>View Approved Thesis- Outline of Research/Case Study</strong></legend>	
	<table>
		<tr>
			<td>Thesis / Project ID</td>
			<td>:</td>
			<td><label type="text" name="thesis_id" size="15" id="thesis_id" ><?=$thesis_id;?></label></td>
		</tr>
		<tr>
			<td>Thesis / Project Title</td>
			<td>:</td>
			<td><label type="text" name="thesis_title"  id="thesis_title" ><?=$thesis_title;?></label></td>
		</tr>
		<tr>
			<td>Proposal Type</td>
			<td>:</td>
			<td>
				<?php if($thesis_type=='R')	{	?>
				<input type="radio" name="thesis_type" value="R" checked disabled="disabled">Research
				<input type="radio" name="thesis_type" value="C" disabled="disabled">Case Study						
				<input type="radio" name="thesis_type" value="P" disabled="disabled">Project	
				<?php	}	else if ($thesis_type=='C')	{	?>
					<input type="radio" name="thesis_type" value="R" disabled="disabled">Research
					<input type="radio" name="thesis_type" value="C" checked  disabled="disabled">Case Study
					<input type="radio" name="thesis_type" value="P" disabled="disabled">Project
				<?php	}	else if ($thesis_type=='P')	{	?>
					<input type="radio" name="thesis_type" value="R" disabled="disabled">Research
					<input type="radio" name="thesis_type" value="C" disabled="disabled">Case Study
					<input type="radio" name="thesis_type" value="P" checked disabled="disabled">Project
				<?php	}	else {	?>
					<input type="radio" name="thesis_type" value="R" checked disabled="disabled">Research
					<input type="radio" name="thesis_type" value="C" disabled="disabled">Case Study
					<input type="radio" name="thesis_type" value="P" disabled="disabled">Project
				<?php	}
				?> 
			</td>		
		</tr>
		<tr>
			<td>Introduction</td>
			<td>:</td>
			<td><br/><label name="introduction" cols="30" class="ckeditor" rows="3" id="introduction" ><?=$introduction;?></label></td>
		</tr>
		<tr>
			<td>Objective</td>
			<td>:</td>
			<td><br/><label name="objective" cols="30" class="ckeditor" rows="3" ><?=$objective;?></label></td>
		</tr>
		<tr>
			<td>Brief Description</td>
			<td>:</td>
			<td><br/><label name="description" cols="30" class="ckeditor" rows="3" ><?=$description;?></label></td>
		</tr>
	</table>
	<table>
	<tr>
		<td><strong>Discussion Details</strong></td>	
	</tr>
	</table>
	<table border="1" cellpadding="2" cellspacing="1" width="99%" id="inputs10" class="thetable">
	 <tr>
		 <th width="98">Date</th>
		 <th width="97">Time</th>
		 <th width="276">Lecturer Name</th>
		 <th width="276">External Lecturer Name</th>
		 <th width="175"><strong>Meeting Mode</strong></th>
		 <th width="318">Notes</th>				 
	 </tr>
	 <?php
		$sqlMeeting="SELECT pmd.id,pmd.lecturer_id, pmd.external_lecturer,pmd.meeting_mode,rm.id,rm.description, DATE_FORMAT(pmd.meeting_sdate,'%d/%m/%Y') AS DATE, 
		DATE_FORMAT(pmd.meeting_sdate,'%h:%i %p') AS TIME, 
		pmd.remark 
		FROM pg_meeting_detail pmd 
		LEFT JOIN ref_meeting_mode rm ON (rm.id=pmd.meeting_mode)
		WHERE pmd.pg_proposal_id='$proposal_id' 
		ORDER BY pmd.meeting_sdate DESC ";			
		
		$result = $db_klas2->query($sqlMeeting); //echo $sql;
		$lecturerId = $db_klas2->f('lecturer_id');
		
				
		$row_cnt = mysql_num_rows($result);
		if ($row_cnt > 0 ) {
			do {			
				$sql3 = "SELECT name
				FROM new_employee
				WHERE empid = '$lecturerId'";
				
				$dbc->query($sql3);
				$dbc->next_record();
				
				$lecturerName=$dbc->f('name');
				$meeting_mode = $db_klas2->f('description');
					
				?><tr>
						<td align="center" width="98"><label name="date[]" ></label><?=$db_klas2->f('date');?></td>
						<td align="center" width="97"><label name="meeting_time[]"></label><?=$db_klas2->f('time');?></td>
						<td align="left" width="276"><label name="lecturer_name[]" ></label><?=$lecturerName;?></td>
						<td align="left" width="276"><label name="lecturer_name[]" ></label><?=$db_klas2->f('external_lecturer');?></td>
						<td><label name="meetingmode" size="25" id="meetingmode" >
                          <?=$meeting_mode?>
                        </label></td>
						<td align="left" width="318"><label name="remarks[]" size="50" ></label><?=$db_klas2->f('remark');?></td>		
				</tr>
			<?} while ($db_klas2->next_record());
		
	   }
	   else {
		   ?>
		   <table>
			<tr>
				<td>No record found!</td>
			</tr>
		</table>
	   <?}?> 	
	</table> 

	<br />
	   <? $sqlPgProposalArea = "SELECT a.pg_proposal_id,
								a.job_id1_area, 
								a.job_id2_area, 
								a.job_id3_area, 
								a.job_id4_area, 
								a.job_id5_area, 
								a.job_id6_area
								FROM pg_proposal_area a
								WHERE a.pg_proposal_id = '$proposal_id'"; 
								
			$rsPgProposalArea = $dbf->query($sqlPgProposalArea);						
			$dbf->next_record();
			$job_id1_area=$dbf->f('job_id1_area');
			$job_id2_area=$dbf->f('job_id2_area');
			$job_id3_area=$dbf->f('job_id3_area');
			$job_id4_area=$dbf->f('job_id4_area');
			$job_id5_area=$dbf->f('job_id5_area');
			$job_id6_area=$dbf->f('job_id6_area');
			
		
			$sql_job_id1_desc = "SELECT area as job_id1_desc
								FROM job_list_category  
								WHERE jobarea = '$job_id1_area'";
			$rs_sql_job_id1_desc = $dbc->query($sql_job_id1_desc);						
			$dbc->next_record();
			$jobAreaDesc1=$dbc->f('job_id1_desc');
			
			$sql_job_id2_desc = "SELECT area as job_id2_desc
								FROM job_list_category  
								WHERE jobarea = '$job_id2_area'";
			$rs_sql_job_id2_desc = $dbc->query($sql_job_id2_desc);						
			$dbc->next_record();
			$jobAreaDesc2=$dbc->f('job_id2_desc');

			$sql_job_id3_desc = "SELECT area as job_id3_desc
								FROM job_list_category  
								WHERE jobarea = '$job_id3_area'";
			$rs_sql_job_id3_desc = $dbc->query($sql_job_id3_desc);						
			$dbc->next_record();
			$jobAreaDesc3=$dbc->f('job_id3_desc');					
			
			
			$sql_job_id4_desc = "SELECT area as job_id4_desc
								FROM job_list_category  
								WHERE jobarea = '$job_id4_area'";
			$rs_sql_job_id4_desc = $dbc->query($sql_job_id4_desc);						
			$dbc->next_record();
			$jobAreaDesc4=$dbc->f('job_id4_desc');	
			
			$sql_job_id5_desc = "SELECT area as job_id5_desc
								FROM job_list_category  
								WHERE jobarea = '$job_id5_area'";
			$rs_sql_job_id5_desc = $dbc->query($sql_job_id5_desc);						
			$dbc->next_record();
			$jobAreaDesc5=$dbc->f('job_id5_desc');	
			
			$sql_job_id6_desc = "SELECT area as job_id6_desc
								FROM job_list_category  
								WHERE jobarea = '$job_id6_area'";
			$rs_sql_job_id6_desc = $dbc->query($sql_job_id6_desc);						
			$dbc->next_record();
			$jobAreaDesc6=$dbc->f('job_id6_desc');	
			?>
			
   <table>
   <tr>
		<td><strong>Thesis Area</strong></td>
	</tr>
  </table>
	<table border="1" cellpadding="2" cellspacing="1" width="99%" id="inputs10" class="thetable">		
		<tr>
			<th width="69" nowrap><b>Area No.</b></th>
			
			<th width="312"><label>Area Description</label></th>
			<th width="45" nowrap><b>Area No.</b></th>
			
		  <th width="334"><label>Area Description</label></th>
		</tr>
		<tr>
			<td width="69" nowrap><b>Area 1</b></td>
			
			<td width="312"><label><?=$jobAreaDesc1?></label></td>
			<td width="45" nowrap><b>Area 4</b></td>
			
		  <td width="334"><label><?=$jobAreaDesc4?></label></td>
		</tr>
		<tr>
			<td width="69" nowrap><b>Area 2</b></td>
			
			<td width="312"><label><?=$jobAreaDesc2?></label></td>
			<td width="45" nowrap><b>Area 5</b></td>
			
		  <td width="334"><label><?=$jobAreaDesc5?></label></td>
		</tr>
		<tr>
			<td width="69" nowrap><b>Area 3</b></td>
			
			<td width="312"><label><?=$jobAreaDesc3?></label></td>
			<td width="45" nowrap><b>Area 6</b></td>
			
		  <td width="334"><label><?=$jobAreaDesc6?></label></td>
		</tr>
	</table>

	<br/>   
	<table>
	<tr>
		<td><strong>Attachment by Student:</strong></td>
	</tr>
	<?
		$sqlUpload="SELECT * FROM file_upload_proposal 
		WHERE pg_proposal_id='$proposal_id' 
		AND attachment_level='S' ";			
		
		$result_sqlUpload = $db_klas2->query($sqlUpload); //echo $sql;
		$row_cnt = mysql_num_rows($result_sqlUpload);
		if ($row_cnt>0)		
		{
			$no=1;
			?><tr><?
			while($row = mysql_fetch_array($result_sqlUpload)) 					
			{ 
				?>
					<td><a href="download.php?fc=<?=$row["fu_cd"];?>&al=S" title="File Description: <?=$row["fu_document_filedesc"];?>">Attachment <?=$no++;?>: <img src="../images/download.png" width="20" height="19" style="border:0px;" title="Download <?=$row["fu_document_filename"];?>"></a></td>
			<?}
			?></tr><?
		}
		else 
		{?>		

			<tr>
				<td>No attachment.</td>
			</tr>

		<?}?>							
		
		
	</table>
	<br/>

	

  <?}
else {
	?>
	<fieldset>
		<legend><strong><span style="color:#FF0000">Notification Message</span></strong></legend>	
		<table>
			<tr>
				<td>Your thesis proposal has been submitted to the Faculty for verification.</td>
			</tr>
		</table>
	</fieldset>	
	<?	
}?>
</fieldset>
  <table>
		<tr>
		 	<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../student/student_programme.php?>#tabs-2';" /></td>		
		</tr>
	</table>
</form>
</body>
</html>




