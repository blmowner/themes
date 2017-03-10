<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: accept_invitation.php
//
// Created by: Zuraimi
// Created Date: 24-Dec-2014
// Modified by: Zuraimi
// Modified Date: 05-Jan-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

if (!class_exists('DateTime')) {
	class DateTime {
		public $date;
	   
		public function __construct($date) {
			$this->date = strtotime($date);
		}
	   
		public function setTimeZone($timezone) {
			return;
		}
	   
		private function __getDate() {
			return date(DATE_ATOM, $this->date);   
		}
	   
		public function modify($multiplier) {
			$this->date = strtotime($this->__getDate() . ' ' . $multiplier);
		}
	   
		public function format($format) {
			return date($format, $this->date);
		}
	}
}

session_start();
$userid=$_SESSION['user_id'];


if(isset($_POST['btnRequestExtend']) && ($_POST['btnRequestExtend'] <> ""))
{	
	$myCheckbox=$_POST['myCheckbox'];
	$myReviewerId=$_POST['myReviewerId'];
	$myStudentMatrixNo=$_SESSION['myStudentMatrixNo'];
	
	$curdatetime = date("Y-m-d H:i:s");
	while (list ($key,$val) = @each ($myCheckbox)) 
	{
		/*"myReviewerId [".$val."] ".$myReviewerId[$val]."<br/>";
		echo "myStudentMatrixNo [".$val."] ".$myStudentMatrixNo[$val]."<br/>";
		echo "myStudentName [".$val."] ".$myStudentName[$val]."<br/>";
		echo "mySupervisorType [".$val."] ".$mySupervisorType[$val]."<br/>";
		echo "myThesisId [".$val."] ".$myThesisId[$val]."<br/>";
		echo "myThesisTitle [".$val."] ".$myThesisTitle[$val]."<br/>";
		echo "myRespondedbyDate [".$val."] ".$myRespondedbyDate[$val]."<br/>";*/
							
		
		$sql2=	"UPDATE pg_supervisor 
				SET	extension_status = 'REQ', extension_date = '$curdatetime', 
				modify_by = '$userid' , modify_date = '$curdatetime' 	
				WHERE id = '$myReviewerId[$val]'
				AND pg_employee_empid = '$userid'  
				AND pg_student_matrix_no = '$myStudentMatrixNo[$val]'  
				AND status = 'A'";

		$result2 = $dba->query($sql2); 	
		//var_dump($db);
		$dba->next_record();
	}
	
}

if(isset($_POST['btnSubmit']) && ($_POST['btnSubmit'] <> ""))
{	
	$myCheckbox=$_POST['myCheckbox'];
	$myReviewerId=$_POST['myReviewerId'];
	$myStudentMatrixNo=$_SESSION['myStudentMatrixNo'];
	$studentname = $_REQUEST['studentname'];
	$curdatetime = date("d-m-Y H:i:s");
	while (list ($key,$val) = @each ($myCheckbox)) 
	{								
		$sql3=	"UPDATE pg_supervisor 
				SET	acceptance_status = 'DNE', acceptance_date = '$curdatetime', 
				modify_by = '$userid' , modify_date = '$curdatetime' 	
				WHERE id = '$myReviewerId[$val]'
				AND pg_employee_empid = '$userid'  
				AND pg_student_matrix_no = '$myStudentMatrixNo[$val]'  
				AND status = 'A'";
		$result3 = $dba->query($sql3); 	
		$dba->next_record();
		
		//sender email
		$selectfrom = "SELECT const_value
		FROM base_constant WHERE const_term = 'EMAIL_ADMIN'";
		$resultfrom = $db->query($selectfrom);
		$db->next_record();
		$fromadmin =$db->f('const_value');
		//email receiver, id, name
		$sqlfaculty = "SELECT const_value
		FROM base_constant WHERE const_term = 'EMAIL_FACULTY'";
		$resultfaculty = $dbe->query($sqlfaculty);
		$dbe->next_record();
		$facultyemail =$dbe->f('const_value');
		
		$sqlfacid = "SELECT const_value
		FROM base_constant WHERE const_term = 'FACULTY_STAFF_ID'";
		$resultfacid = $dba->query($sqlfacid);
		$dba->next_record();
		$facultyid =$dba->f('const_value');
		
		$sqlfacname = "SELECT name
		FROM new_employee WHERE empid = '$facultyid'";
		$resultfacname = $dbc->query($sqlfacname);
		$dbc->next_record();
		$facultyname =$dbc->f('name');
		
		//email cc, name
		$sqlreviewer = "SELECT name,email
		FROM new_employee WHERE empid = '$user_id'";
		$resultsqlreviewer = $dbk->query($sqlreviewer);
		$dbk->next_record();
		$revieweremail =$dbk->f('email');		
		$reviewername =$dbk->f('name');
		
		//remark that been given
		$sqlremark = "SELECT recipient_remarks
		FROM pg_supervisor WHERE id = '$myReviewerId[$val]'";
		$resultsqlremark = $dbb->query($sqlremark);
		$dbb->next_record();
		$remark =$dbb->f('recipient_remarks');		
		
		//position
		$sqlposition = "SELECT description
		FROM ref_supervisor_type WHERE id = 'RV'";
		$resultsqlposition = $dbd->query($sqlposition);
		$dbd->next_record();
		$position =$dbd->f('description');		
		$curdatetime1 = date("d-m-Y");

		include("../../../app/application/email/email_reviewer_feedback.php");
		
		/*echo ("<br> To: ".$facultyemail."<br> Receiver Name: ".$facultyname.
		"<br> Receiver ID: ".$facultyid."<br> Sender Email: ".$fromadmin.
		"<br> Cc: ".$revieweremail."<br>Cc Name: ".$reviewername."<br>Reviewer: ".$remark."<br>student name: ".$studentname[$val]."<br>studid: ".$myStudentMatrixNo[$val].
		"<br>Position: ".$position."");*/	
		
		
		
	}
	
}

$sql1 = "SELECT a.id, a.assigned_by, DATE_FORMAT(a.assigned_date,'%d-%b-%Y') AS assigned_date, 
		DATE_FORMAT(a.acceptance_date,'%d-%b-%Y') AS acceptance_date, a.acceptance_status, a.acceptance_remarks,
		DATE_FORMAT(a.respondedby_date,'%d-%b-%Y') AS respondedby_date, 
		DATE_FORMAT(a.assigned_date,'%d-%b-%Y') AS assigned_date, 
		DATE_FORMAT(f.endorsed_date,'%d-%b-%Y') AS endorsed_date, a.pg_student_matrix_no, 
		a.ref_supervisor_type_id, b.description AS supervisor_type, 
		e.pg_thesis_id, e.thesis_title, e.id AS proposal_id, a.recipient_remarks, a.recipient_date, a.extension_reasons,
		a.extension_status, a.acceptance_status
		FROM pg_supervisor a 
		LEFT JOIN ref_supervisor_type b ON (b.id = a.ref_supervisor_type_id) 
		LEFT JOIN pg_thesis d ON (d.student_matrix_no = a.pg_student_matrix_no) 
		LEFT JOIN pg_proposal e ON (e.pg_thesis_id = d.id) 
		LEFT JOIN pg_proposal_approval f ON (f.id = e.pg_proposal_approval_id) 
		WHERE a.status = 'A' 
		AND a.pg_employee_empid = '$user_id' 
		AND a.acceptance_status IS NULL 
		AND (a.extension_status IS NULL || a.extension_status = 'APP')
		AND d.status = 'INP' 
		AND e.verified_status IN ('APP','AWC') 
		AND e.status = 'OPN'		
		AND e.archived_status IS NULL
		AND a.ref_supervisor_type_id = 'RV'
		ORDER BY a.acceptance_date";

//echo "sql1 ".$sql1;
$result1 = $db->query($sql1);
$db->next_record();

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
			
			<?
				$row_cnt = mysql_num_rows($result1);
					if ($row_cnt>0) {?>
					<fieldset>
					<legend><strong>List of Thesis Proposal for Reviewer Feedback</strong></legend><table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1">
					<tr>
						<td width="30" align="center"><strong>Tick</strong></td>
						<td width="24"><strong>No.</strong></div></td>
						<td width="24"><strong>Feedback Status</strong></td>
						<td width="24"><strong>Feedback Date</strong></td>
						<td width="137"><strong>Student Name</strong></td>
						<td width="104"><strong>Thesis ID</strong></td>						
						<td width="152"><strong>Thesis / Project Title</strong></td>						
						<td width="115"><strong>Due Date (to reply)</strong></td>
						<td width="115"><strong>Reviewer Feedback</strong></td>
						<td width="152"><strong>Attachment by Student</strong></td>
						<td width="152"><strong>Request Extension (if needed)</strong></td>							
					</tr>
					<?
					$no=0;
					do {
						$reviewerId=$db->f('id');	
						$studentMatrixNo=$db->f('pg_student_matrix_no');
						$supervisorTypeId=$db->f('ref_supervisor_type_id');
						$supervisorType=$db->f('supervisor_type');
						$thesisId=$db->f('pg_thesis_id');
						$thesisTitle=$db->f('thesis_title');						
						$endorsedDate=$db->f('endorsed_date');
						$proposalId=$db->f('proposal_id');
						$recipientRemarks=trim(strip_tags($db->f('recipient_remarks')));
						$recipientDate=$db->f('recipient_date');
						$assignedDate=$db->f('assigned_date');
						$respondedByDate=$db->f('respondedby_date');
						$extensionReasons=$db->f('extension_reasons');
						$extensionStatus=$db->f('extension_status');
						$acceptanceStatus=$db->f('acceptance_status');
						$acceptanceDate=$db->f('acceptance_date');
					?>
						<tr>
							<?
							$tmpAssignedDate = new DateTime($assignedDate);
							$tmpRespondedByDate = new DateTime($respondedByDate);
							
							if (($extensionStatus == null || $extensionStatus == 'APP') && ($acceptanceStatus == null)) {?>
								<td align="center"><input name="myCheckbox[]" type="checkbox" value="<?=$no;?>"/></td>
							<?}
							else {//$extensionStatus == 'REQ' || $acceptanceStatus == 'DNE'
								?>
								<td><div align="center">
								  <input name="myCheckbox[]" type="checkbox" value="<?=$no;?>" disabled="disabled"/>
								  </div></td>
								<?
								
							}?>
							
							<td align="center"><?=$no+1?>.</td>
							
							<td><label name="acceptanceStatus[]" value="<?=$acceptanceStatus;?>" disabled="disabled" ><?=$acceptanceStatus?></label></td>
							
							<td><label name="acceptanceDate[]" value="<?=$acceptanceDate;?>" disabled="disabled" ><?=$acceptanceDate?></label></td>
							
							<input type="hidden" name="myReviewerId[]" size="30" id="reviewerId" value="<?=$reviewerId?>"/>							
							<?$myreviewerId[$no]=$reviewerId;?>
							
							<?$myStudentMatrixNo[$no]=$studentMatrixNo;?>
							<?
							$sql1="SELECT name as student_name
									FROM student
									WHERE matrix_no = '$studentMatrixNo'";
									
							$result1 = $dbc->query($sql1);
							$dbc->next_record();
							$studentName = $dbc->f('student_name');
							?>

							
							<td><label name="studentName[]" size="30" id="studentName" ></label><?=$studentName?><br/>(<?=$studentMatrixNo?>)
							<input type="hidden" name="studentname[]" size="30" id="studentname" value="<?=$studentName?>" />
							<input type="hidden" name="matricno[]" size="30" id="matricno" value="<?=$studentMatrixNo?>"/></td>
							<?$myStudentName[$no]=$studentName;?>
							
							<td><a href="../reviewer/reviewer_feedback_outline.php?thesisId=<? echo $thesisId;?>&proposalId=<? echo $proposalId;?>" name="thesisId[]" title="Outline of Proposed Case Study by the Student - Read more..."><?=$thesisId;?></a></td>	
							
							<td><label name="thesisTitle[]" cols="30" rows="3" id="thesisTitle" ></label><?=$thesisTitle?></td>														
							<?$myThesisTitle[$no]=$thesisTitle;?>


							<td><label name="respondedByDate[]" size="15" id="respondedByDate" ></label><?=$respondedByDate?></td>
							<?$myRespondedByDate[$no]=$respondedByDate;?>	


							
							
							<td><a href="../reviewer/reviewer_feedback_remarks.php?rid=<?=$reviewerId;?>" name="acceptanceRemarks[]" value="<?=$acceptanceRemarks?>" title=""><br/>
						
						
							<?
							if (strlen($recipientRemarks) == 0) {?>
							
								<img src="../images/red_edit.jpg" width="20" height="19" style="border:0px;" title="Enter remarks here" >Enter feedback here</a></td>	
							<?}
							else {
							?>
								<img src="../images/edit.jpg" width="20" height="19" style="border:0px;" title="Read remarks detail" >Edit Feedback</a></td>	
							<?
							}?>
							
													
							<? $myRecipientRemarks[$no]=$recipientRemarks;?>
								
								
							<?php
										$sqlUpload="SELECT * FROM file_upload_proposal 
										WHERE pg_proposal_id='$proposalId' 
										AND attachment_level='S' ";			

										$result = $db_klas2->query($sqlUpload); //echo $sql;
										$row_cnt = mysql_num_rows($result);
										if ($row_cnt>0)
										{
											?><td align="left"><?
											while($row = mysql_fetch_array($result)) 					
											{ 
												?>
														<a href="../thesis/download.php?fc=<?=$row["fu_cd"];?>&al=S"><?=$row["fu_document_filename"];?><img src="../images/download.png" width="20" height="19" style="border:0px;" title="Download"></a><br/>
														
																
													
											<?}
											?></td><?
										}
										else {
											?><td>No attachment</td><?
										}
									?>	
							<?												
							$no=$no+1;
							}while($db->next_record());	?>	
							
							<td>
							
							<?$tmpRespondedByDate = new DateTime($respondedByDate);				
							$currentDate = date('d-M-Y');	
							$myCurrentDate1 = new DateTime($currentDate);

							if ($tmpRespondedByDate >= $myCurrentDate1) {?>
							
								<label >You still have time to provide feedback.</label>
									
							<?} else {
								?><a href="../reviewer/reviewer_feedback_remarks.php?rid=<?=$reviewerId;?>&ext=Y" title="">
								<?
									if (strlen($extensionReasons) == 0) {?>
									
										<img src="../images/red_edit.jpg" width="20" height="19" style="border:0px;" title="Enter reason here" >Enter reason here</a>
									<?}
									else {
									?>
										<img src="../images/edit.jpg" width="20" height="19" style="border:0px;" title="Reason provided" >Edit Reason</a>	
									<?}
									?>
									<?if ($extensionStatus=='REQ'){
										?>
										<br/><br/><label>Request has been submitted</label>
										<?
									} else {
										?>
										</a><br/><br/><input type="submit" name="btnRequestExtend" value="Request for Extension" />
										<?
									}?>
							<?}?>
								
							</td>
						
							
						</tr>
					</table>
					<br/>
					<table>
						<tr>
							<td><span class="style1">Notes:-</span></td>
						</tr>
						<tr>
							<td>1. Please enter your <strong>Reviewer Feedback</strong> above and submit it before the due date.</td>
						</tr>
						<tr>
							<td>2. Or otherwise you may need to request for date extension from the Faculty.</td>
						</tr>
					</table>
					<br/>
					<table>
						
						<?$_SESSION['myCheckbox'] = $myCheckbox;?>
						<?$_POST['myReviewerId'] = $myReviewerId;?>
						<?$_SESSION['myStudentMatrixNo'] = $myStudentMatrixNo;?>					
						<tr>
							<td></td>
							<td><input type="submit" name="btnSubmit" value="Submit Feedback" /></td>
						</tr>
					</table>							
				</fieldset>				
				<?
				}
				else {
					?>
					<fieldset>
					<legend><span style="color:#FF0000">Notification Message</span></legend>
						<table>
							<tr>
								<td>
									<p>There is no thesis proposal available for you to review and provide feedback.</p>
								</td>
							</tr>
						</table>
					</fieldset>
					<table>
					<tr>
						<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../reviewer/reviewer_feedback.php';" /></td>
					</tr>
					</table>
					<?
				}				
				?>			
					
		</form>
	</body>
</html>