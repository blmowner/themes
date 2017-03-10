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
	if (sizeof($_POST['myCheckbox'])>0) {
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
	else {
		
		$msg[] = "<div class=\"error\"><span>Please tick the checkbox before submit.</div>";
		
	}
}

if(isset($_POST['btnSubmit']) && ($_POST['btnSubmit'] <> ""))
{	
	$myCheckbox=$_POST['myCheckbox'];
	$myReviewerId=$_POST['myReviewerId'];
	$myStudentMatrixNo=$_SESSION['myStudentMatrixNo'];
	$studentname = $_REQUEST['studentname'];
	$curdatetime = date("Y-m-d H:i:s");
	$matrixno = $_REQUEST['matricno'];
	$thesisid = $_REQUEST['thesisid'];
	$thesistitle = $_REQUEST['thesistitle'];
	$msg = array();
	
	if (sizeof($_POST['myCheckbox'])>0) {
		
		while (list ($key,$val) = @each ($myCheckbox)) 
		{								
			$sql1 = "SELECT id
			FROM pg_supervisor
			WHERE id = '$myReviewerId[$val]'
			AND pg_employee_empid = '$userid'  
			AND pg_student_matrix_no = '$myStudentMatrixNo[$val]' 
			AND recipient_remarks IS NOT NULL
			AND recipient_date IS NOT NULL
			AND status = 'A'";
			
			$result1 = $dba->query($sql1); 	
			$dba->next_record();
			
			$row_cnt = mysql_num_rows($result1);	
			
			if ($row_cnt > 0) {
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
				
				$emailfaculty = explode(" , ", $facultyemail);
				$emailfaculty[0]; /// email = test
				$emailfaculty[1]; /// email = GSM
				$emailfaculty[2]; /// email = SGS

				
				$sqlfacid = "SELECT const_value
				FROM base_constant WHERE const_term = 'FACULTY_STAFF_ID'";
				$resultfacid = $dba->query($sqlfacid);
				$dba->next_record();
				$facultyid =$dba->f('const_value');
				
				$sqlfacname = "SELECT a.name,b.title 
				FROM new_employee a
				LEFT JOIN lookup_gelaran b ON(b.id = a.title)
				WHERE a.empid = '$facultyid'";
				$resultfacname = $dbc->query($sqlfacname);
				$dbc->next_record();
				$facultyname =$dbc->f('name');
				$title =$dbc->f('title');
				
				$sqlDept = "SELECT a.program_code, b.programid,b.dept_unit
				FROM student_program a 
				LEFT JOIN program b ON (a.program_code =b.programid)
				WHERE matrix_no = '$myStudentMatrixNo[$val]'";
				$result_sqlDep = $dbn->query($sqlDept);
				$dbn->next_record();
				$studDept =$dbn->f('dept_unit'); /////////////////////////>>>>>>>>>>>>>>>>>>>>>>student faculty
				
				//email cc, name
				$sqlreviewer = "SELECT a.name,a.email,b.title 
				FROM new_employee a
				LEFT JOIN lookup_gelaran b ON(b.id = a.title)
				WHERE a.empid = '$user_id'";

				$resultsqlreviewer = $dbk->query($sqlreviewer);
				$dbk->next_record();
				$revieweremail =$dbk->f('email');		
				$reviewername =$dbk->f('name');
				$reviewertitle=$dbk->f('title');

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
				$msg[] = "<div class=\"success\"><span>Your feedback Thesis ID $thesisid[$val] has been submitted to the Faculty successfully.</span></div>";
			}
			else {
				$msg[] = "<div class=\"error\"><span>Your feedback for Thesis ID $thesisid[$val] cannot be submitted due to no remarks has been provided. Please complete it first.</span></div>";
			}
			
		}
	}
	else {
		
		$msg[] = "<div class=\"error\"><span>Please tick the checkbox before submit.</div>";
		
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
		AND e.verified_status IN ('INP') 
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
		<link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
		<link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
		<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
		<link rel="stylesheet" href="../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
		<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
		<script src="../../../lib/js/jquery.min2.js"></script>
		<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>
		<script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
</head>
	<body>
	<?php
		if(!empty($msg)) 
		{
			foreach($msg as $err) 
			{
				echo $err;
			}
		}
	?>	
		<form id="form2" name="form2" method="post" enctype="multipart/form-data">
			
			<?
				$row_cnt = mysql_num_rows($result1);
					if ($row_cnt>0) {?>
					<fieldset>
					<legend><strong>List of Thesis Proposal for Reviewer Feedback</strong></legend>
					<table width="100%" border="1" cellpadding="3" cellspacing="1" style="border-collapse:collapse;" class="thetable">
					<tr>
						<th width="30" align="center"><strong>Tick</strong></th>
						<th width="24"><strong>No.</strong></div></th>
						<th width="24"><strong>Feedback Status</strong></th>
						<th width="24"><strong>Feedback Date</strong></th>
						<th width="137"><strong>Student Name</strong></th>
						<th width="104"><strong>Thesis ID / Project ID</strong></th>						
						<th width="152"><strong>Thesis / Project Title</strong></th>						
						<th width="115"><strong>Due Date (to reply)</strong></th>
						<th width="115"><strong>Reviewer Feedback</strong></th>
						<th width="152"><strong>Attachment by Student</strong></th>
						<th width="152"><strong>Request Extension (if needed)</strong></th>							
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
							if (substr($studentMatrixNo,0,2) != '07') { 
								$dbConnStudent= $dbc; 
							} 
							else { 
								$dbConnStudent=$dbc1; 
							}		
							$result1 = $dbConnStudent->query($sql1);
							$dbConnStudent->next_record();
							$studentName = $dbConnStudent->f('student_name');
							?>

							
							<td><label name="studentName[]" size="30" id="studentName" ></label><?=$studentName?><br/>(<?=$studentMatrixNo?>)
							<input type="hidden" name="studentname[]" size="30" id="studentname" value="<?=$studentName?>" />
							<input type="hidden" name="matricno[]" size="30" id="matricno" value="<?=$studentMatrixNo?>"/></td>
							<?$myStudentName[$no]=$studentName;?>
							
							<td><a href="../reviewer/reviewer_feedback_outline.php?thesisId=<? echo $thesisId;?>&proposalId=<? echo $proposalId;?>" name="thesisId[]" title="Outline of Proposed Case Study by the Student - Read more..."><?=$thesisId;?></a>
						    <input type="hidden" name="thesisid[]" size="30" id="thesisid[]" value="<?=$thesisId;?>" /></td>	
							
							<td><label name="thesisTitle[]" cols="30" rows="3" id="thesisTitle" ></label><?=$thesisTitle?>
						    <input type="hidden" name="thesistitle[]" size="30" id="thesistitle[]" value="<?=$thesisTitle;?>" /></td>														
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
							//echo $tmpRespondedByDate->format( 'd-m-Y' );
							$currentDate = date('d-M-Y');	
							$myCurrentDate1 = new DateTime($currentDate);
							//echo $myCurrentDate1->format( 'd-m-Y' );
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
							<td><span style="color:#FF0000">Notes:</span></td>
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
					<legend><strong>Notification Message</strong></legend>
						<table>
							<tr>
								<td>
									<p>No thesis proposal is available to be reviewed and provide feedback. Please check again later.</p>
								</td>
							</tr>
						</table>
					</fieldset>
					<?
				}				
				?>			
					
		</form>
	</body>
</html>