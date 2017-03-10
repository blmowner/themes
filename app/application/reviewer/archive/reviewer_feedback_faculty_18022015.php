<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: reviewer_feedback_faculty.php
//
// Created by: Zuraimi
// Created Date: 24-Dec-2014
// Modified by: Zuraimi
// Modified Date: 05-Jan-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

session_start();
$userid=$_SESSION['user_id'];

if(isset($_POST['btnSearch']) && ($_POST['btnSearch'] <> "")) {
	
	$searchThesisId = $_POST['searchThesisId'];
	$searchStudent = $_POST['searchStudent'];
	$searchStaff = $_POST['searchStaff'];
	
	if ($searchThesisId!="") 
	{
		$tmpSearchThesisId = " AND e.pg_thesis_id = '$searchThesisId'";
	}
	else 
	{
		$tmpSearchThesisId="";
	}
	if ($searchStudent!="") 
	{
		$tmpSearchStudent = " AND (d.student_matrix_no = '$searchStudent' OR c.name like '%$searchStudent%')";
	}
	else 
	{
		$tmpSearchStudent="";
	}
	if ($searchStaff!="") 
	{
		$tmpSearchStaff = " AND (a.pg_employee_empid = '$searchStaff' OR g.name like '%$searchStaff%')";
	}
	else 
	{
		$tmpSearchStaff="";
	}
	
	
	$sql1 = "SELECT a.id as reviewer_id, a.assigned_by, DATE_FORMAT(a.assigned_date,'%d-%b-%Y') AS assigned_date, 
		DATE_FORMAT(a.acceptance_date,'%d-%b-%Y') AS acceptance_date, a.acceptance_status, a.acceptance_remarks,
		DATE_FORMAT(a.respondedby_date,'%d-%b-%Y') AS respondedby_date, 
		DATE_FORMAT(a.assigned_date,'%d-%b-%Y') AS assigned_date, 
		DATE_FORMAT(f.endorsed_date,'%d-%b-%Y') AS endorsed_date, a.pg_student_matrix_no, 
		a.ref_supervisor_type_id, b.description AS supervisor_type, c.name AS student_name, 
		e.pg_thesis_id, e.thesis_title, e.id as proposal_id, a.recipient_remarks, a.recipient_date, a.extension_reasons,
		a.extension_status, a.acceptance_status, a.pg_employee_empid, g.name as staff_name
		FROM pg_supervisor a 
		LEFT JOIN ref_supervisor_type b ON (b.id = a.ref_supervisor_type_id) 
		LEFT JOIN student c ON (c.matrix_no = a.pg_student_matrix_no) 
		LEFT JOIN pg_thesis d ON (d.student_matrix_no = c.matrix_no) 
		LEFT JOIN pg_proposal e ON (e.pg_thesis_id = d.id) 
		LEFT JOIN pg_proposal_approval f ON (f.id = e.pg_proposal_approval_id) 
		LEFT JOIN new_employee g ON (g.empid = a.pg_employee_empid)
		WHERE a.status = 'A' "
		.$tmpSearchThesisId." "
		.$tmpSearchStudent." "
		.$tmpSearchStaff." "."
		AND a.acceptance_status IS NULL 
		AND d.status = 'INP' 
		AND e.verified_status IN ('INP','REQ') 
		AND (a.extension_status IS NULL OR a.extension_status IN ('REQ','APP'))
		AND e.status = 'OPN'		
		AND e.archived_status is null
		ORDER BY a.acceptance_date";

		//echo "sql1 ".$sql1;
		$result1 = $db->query($sql1);
		$db->next_record();
		$row_cnt = mysql_num_rows($result1);
}
else {
	/*$sql1 = "SELECT a.id as reviewer_id, a.assigned_by, DATE_FORMAT(a.assigned_date,'%d-%b-%Y') AS assigned_date, 
		DATE_FORMAT(a.acceptance_date,'%d-%b-%Y') AS acceptance_date, a.acceptance_status, a.acceptance_remarks,
		DATE_FORMAT(a.respondedby_date,'%d-%b-%Y') AS respondedby_date, 
		DATE_FORMAT(a.assigned_date,'%d-%b-%Y') AS assigned_date, 
		DATE_FORMAT(f.endorsed_date,'%d-%b-%Y') AS endorsed_date, a.pg_student_matrix_no, 
		a.ref_supervisor_type_id, b.description AS supervisor_type, c.name AS student_name, 
		e.pg_thesis_id, e.thesis_title, e.id as proposal_id, a.recipient_remarks, a.recipient_date, a.extension_reasons,
		a.extension_status, a.acceptance_status, a.pg_employee_empid, g.name as staff_name
		FROM pg_supervisor a 
		LEFT JOIN ref_supervisor_type b ON (b.id = a.ref_supervisor_type_id) 
		LEFT JOIN student c ON (c.matrix_no = a.pg_student_matrix_no) 
		LEFT JOIN pg_thesis d ON (d.student_matrix_no = c.matrix_no) 
		LEFT JOIN pg_proposal e ON (e.pg_thesis_id = d.id) 
		LEFT JOIN pg_proposal_approval f ON (f.id = e.pg_proposal_approval_id) 
		LEFT JOIN new_employee g ON (g.empid = a.pg_employee_empid)
		WHERE a.status = 'A' 
		AND a.acceptance_status IS NULL 
		AND d.status = 'INP' 
		AND e.verified_status IN ('INP','REQ') 
		AND (a.extension_status IS NULL OR a.extension_status IN ('REQ','APP'))
		AND e.status = 'OPN'		
		AND e.archived_status is null
		ORDER BY a.acceptance_date";

		//echo "sql1 ".$sql1;
		$result1 = $db->query($sql1);
		$db->next_record();*/
		$row_cnt = 0;

}

if(isset($_POST['btnSubmit']) && ($_POST['btnSubmit'] <> ""))
{	
	/*echo var_dump($_POST);
	$myCheckbox = $_POST['myCheckbox'];
	$myReviewerId = $_POST['myReviewerId'];
	$myStudentMatrixNo = $_SESSION['myStudentMatrixNo'];
	$myRespondedByDate = $_POST['myRespondedByDate'];*/
	
	$curdatetime = date("Y-m-d H:i:s");
	while (list ($key,$val) = @each ($myCheckbox)) 
	{
		/*echo "myReviewerId [".$val."] ".$myReviewerId[$val]."<br/>";
		echo "myStudentMatrixNo [".$val."] ".$myStudentMatrixNo[$val]."<br/>";
		echo "myStudentName [".$val."] ".$myStudentName[$val]."<br/>";
		echo "mySupervisorType [".$val."] ".$mySupervisorType[$val]."<br/>";
		echo "myThesisId [".$val."] ".$myThesisId[$val]."<br/>";
		echo "myThesisTitle [".$val."] ".$myThesisTitle[$val]."<br/>";
		echo "myRespondedByDate [".$val."] ".$myRespondedByDate[$val]."<br/>";*/
							
		
		$sql3 =	"UPDATE pg_supervisor 
				SET	extension_status = 'APP', extension_date = '$curdatetime', 
				modify_by = '$userid' , modify_date = '$curdatetime' 	
				WHERE id = '$myReviewerId[$val]'
				/* AND pg_employee_empid = '$userid' */
				AND pg_student_matrix_no = '$myStudentMatrixNo[$val]'  
				AND status = 'A'";

		$result3 = $dba->query($sql3); 	
		//var_dump($db);
		$dba->next_record();
	}
	
}


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
		<script type="text/javascript" src="//cdn.ckeditor.com/4.4.6/standard/ckeditor.js"></script>
	    <style type="text/css">
<!--
.style1 {
	color: #FF0000;
	font-weight: bold;
}
-->
        </style>
</head>
	<body>
		<form id="form2" name="form2" method="post" enctype="multipart/form-data">
			
			
					<fieldset>
					<legend><strong>List of Thesis Proposal for Reviewer Feedback</strong></legend>
					<table>
						<tr>							
							<td><strong>Please enter searching criteria below</strong></td>
						</tr>
						<tr>
							<td><strong>Notes: </strong>(by default it will display,<br/> 
							1. All Reviewers which has been requested to provide their feedback on student's proposal. </td>
						</tr>
					</table>
					<br/>
					<table>
						<tr>
							<?$searchRequestDate = date("d-M-Y");?>
							<td>Thesis ID</td>
							<td>:</td>
							<td><input type="text" name="searchThesisId" size="15" id="searchThesisId" value="<?=$searchThesisId;?>"/></td>
						</tr>
						<tr>
							<td>Student Name / Matrix No</td>
							<td>:</td>
							<td><input type="text" name="searchStudent" size="15" id="searchStudent" value="<?=$searchStudent;?>"/></td>							
						</tr>
						<tr>
							<td>Reviewer Name / Staff ID</td>
							<td>:</td>
							<td><input type="text" name="searchStaff" size="15" id="searchStaff" value="<?=$searchStaff;?>"/></td>
							<td><input type="submit" name="btnSearch" value="Search" />  Note: When clicked, if no parameters are provided, it will search all.</td>
						</tr>
					</table>
					<br/>
					<table>
						<tr>							
							<td><strong>Searching Results:-</strong></td>
						</tr>
					</table>
					<?
					//$row_cnt = mysql_num_rows($result1);
					if ($row_cnt>0) {?>
					<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1">
					<tr>
						<td width="30"><strong>Tick</strong></td>
						<td width="24"><strong>No.</strong></td>
						<td width="24"><strong>Review Status</strong></td>
						<td width="24"><strong>Replied Date</strong></td>
						<td width="137"><strong>Reviewer Name</strong></td>
						<td width="137"><strong>Student Name</strong></td>
						<td width="104"><strong>Thesis ID</strong></td>						
						<td width="152"><strong>Thesis / Project Title</strong></td>						
						<td width="115"><strong>Remarks</strong></td>
						<td width="152"><strong>Due Date (to reply)</strong></td>							
					</tr>
					<?
					$no=0;
					do {
						$reviewerId=$db->f('reviewer_id');	
						$studentMatrixNo=$db->f('pg_student_matrix_no');
						$studentName=$db->f('student_name');
						$staffName=$db->f('staff_name');
						$supervisorTypeId=$db->f('ref_supervisor_type_id');
						$supervisorType=$db->f('supervisor_type');
						$thesisId=$db->f('pg_thesis_id');
						$thesisTitle=$db->f('thesis_title');						
						$respondedByDate=$db->f('respondedby_date');
						$endorsedDate=$db->f('endorsed_date');
						$proposalId=$db->f('proposal_id');
						$recipientRemarks=trim(strip_tags($db->f('recipient_remarks')));
						$recipientDate=$db->f('recipient_date');
						$assignedDate=$db->f('assigned_date');
						//$respondedByDate=$db->f('respondedby_date');
						$extensionReasons=$db->f('extension_reasons');
						$extensionStatus=$db->f('extension_status');
						$acceptanceStatus=$db->f('acceptance_status');
						$acceptanceDate=$db->f('acceptance_date');
						$staffId=$db->f('pg_employee_empid');
					?>
						<tr>
							<?
							//$tmpAssignedDate = new DateTime($assignedDate);
							$tmpRespondedByDate = new DateTime($respondedByDate);
							
							$currentDate = new DateTime();	
							
							if ($extensionStatus =='REQ') {?>
								<td><input name="myCheckbox[]" type="checkbox" value="<?=$no;?>"/></td>
							<?}
							else {//$extensionStatus == 'REQ' || $acceptanceStatus == 'DNE'
								?>
								<td><input name="myCheckbox[]" type="checkbox" value="<?=$no;?>" disabled="disabled"/></td>
								<?
								
							}?>
							
							<td><?=$no+1?></td>
							
							<td><label name="acceptanceStatus[]" value="<?=$acceptanceStatus;?>" disabled="disabled" ><?=$acceptanceStatus?></label></td>
							
							<td><label name="acceptanceDate[]" value="<?=$acceptanceDate;?>" disabled="disabled" ><?=$acceptanceDate?></label></td>
							
							<input type="hidden" name="myReviewerId[]" size="30" id="reviewerId" value="<?=$reviewerId?>">
							<?$myReviewerId[$no]=$reviewerId;?>						
							
							
							<?$myStudentMatrixNo[$no]=$studentMatrixNo;?>

							
							<td><label name="staffName[]" size="30" id="staffName" ></label><?=$staffName?><br/>(<?=$staffId?>)</td>
							<?$myStaffName[$no]=$staffName;?>
							
							<td><label name="studentName[]" size="30" id="studentName" ></label><?=$studentName?><br/>(<?=$studentMatrixNo?>)</td>
							<?$myStudentName[$no]=$studentName;?>
							
							<td><a href="../reviewer/reviewer_feedback_faculty_outline.php?thesisId=<? echo $thesisId;?>&proposalId=<? echo $proposalId;?>" name="thesisId[]" title="Outline of Proposed Case Study by the Student - Read more..."><?=$thesisId;?></a></td>	
							
							<td><label name="thesisTitle[]" cols="30" rows="3" id="thesisTitle" ></label><?=$thesisTitle?></td>														
							<?$myThesisTitle[$no]=$thesisTitle;?>
								
							<td><a href="../reviewer/reviewer_feedback_faculty_remarks.php?rid=<?=$reviewerId;?>&ext=N" name="acceptanceRemarks[]" value="<?=$acceptanceRemarks?>" title=""><br/>
						
						
							<?
							if (strlen($recipientRemarks) == 0) {?>
							
								<img src="../images/red_edit.jpg" width="20" height="19" style="border:0px;" title="Enter remarks here" >Enter feedback here</a>	
							<?}
							else {
							?>
								<img src="../images/edit.jpg" width="20" height="19" style="border:0px;" title="Read remarks detail" >Edit Feedback</a>	
							<?
							}?>
							</td>
													
							<? $myRecipientRemarks[$no]=$recipientRemarks;?>
								
							<?
							
							if ($tmpRespondedByDate->format('d-M-Y') < $currentDate->format('d-M-Y'))  //reviewer is blocked to provide feedback
							{
								if  ($extensionStatus=='' || $extensionStatus==null)
								{?>							
									<td><label name="myRespondedByDate[]" size="15" id="respondedByDate" ><?=$respondedByDate?></label></td>
								<?}
								else if ($extensionStatus=='REQ')
								{?>							
									<td><input type="text" name="myRespondedByDate[]" size="15" id="respondedByDate" value="<?=$respondedByDate?>"> 									
									<br/><br/><span style="color:#FF0000">Note:</span> This Reviewer has requested for additional time to provide feedback.<br/>You may change its <strong>Due Date</strong> here.
									</td>
								<?}
								else //APP
								{?>							
									<td><label name="myRespondedByDate[]" size="15" id="respondedByDate" ><?=$respondedByDate?></label>									
									<br/><br/><label><span style="color:#FF0000">Note:</span> Extension request has been approved with a new date</label></td>
								<?}
							}
							else 
							{
								?>
								<td><label name="myRespondedByDate[]" size="15" id="respondedByDate" ><?=$respondedByDate?> - Due</label></td>							
							<?}?>
							<?$myRespondedByDate[$no]=$respondedByDate;?>	
							</tr>
							<?												
							$no=$no+1;
							}while($db->next_record());	?>														
						
					</table>
					<br/>
					<table>
						<tr>
							<td><span class="style1">Note:-</span></td>
						</tr>
						<tr>
							<td>1. Please select the above proposal above before proceed with the extension approval.</td>
						</tr>
					</table>
					<br/>
					<table>
						
						<?$_SESSION['myCheckbox'] = $myCheckbox;?>
						<?$_POST['myReviewerId'] = $myReviewerId;?>
						<?$_SESSION['myStudentMatrixNo'] = $myStudentMatrixNo;?>
						<?$_POST['myRespondedByDate'] = $myRespondedByDate;?>						
						<tr>
							<td></td>
							<td><input type="submit" name="btnSubmit" value="Approve Extension" /></td>
						</tr>
					</table>							
				</fieldset>				
				<?
				}
				else {
					?>
					<fieldset>
					<legend><strong><span style="color:#FF0000">Notification Message</span></strong></legend>
						<table>
							<tr>
								<td>
									<p>There is no reviewer available to be displayed.</p>
								</td>
							</tr>
						</table>
					</fieldset>
					<table>
					<tr>
						<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../reviewer/reviewer_feedback_faculty.php';" /></td>
					</tr>
					</table>
					<?
				}				
				?>			
					
		</form>
	</body>
</html>