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

session_start();
$userid=$_SESSION['user_id'];



$sql1 = "SELECT	a.id, a.assigned_by, DATE_FORMAT(a.assigned_date,'%d-%b-%Y') AS assigned_date, a.assigned_remarks, 
		DATE_FORMAT(a.acceptance_date,'%d-%b-%Y') AS acceptance_date, a.acceptance_status, a.acceptance_remarks,
		DATE_FORMAT(a.respondedby_date,'%d-%b-%Y') AS respondedby_date, a.endorsed_by, a.endorsed_remarks, a.endorsed_date, 
		DATE_FORMAT(a.senate_date,'%d-%b-%Y') AS senate_date, a.pg_student_matrix_no, 
		a.ref_supervisor_type_id, b.description AS supervisor_type, c.name AS student_name, 
		e.pg_thesis_id, e.thesis_title, e.id as proposal_id
		FROM pg_supervisor a
		LEFT JOIN ref_supervisor_type b ON (b.id = a.ref_supervisor_type_id) 
		LEFT JOIN student c ON (c.matrix_no = a.pg_student_matrix_no) 
		LEFT JOIN pg_thesis d ON (student_matrix_no = c.matrix_no)
		LEFT JOIN pg_proposal e ON (e.pg_thesis_id = d.id)
		WHERE a.status = 'A'
		-- AND a.pg_employee_empid = '$user_id'
		AND a.acceptance_status IS NULL 
		AND a.pg_proposal_id = '$proposalId'
		AND d.status = 'CMP' 
		AND e.verified_status = 'APP' 
		AND e.status = 'APP'";

//echo "sql1 ".$sql1;
$result1 = $db->query($sql1);
$db->next_record();


if(isset($_POST['acceptBtn']) && ($_POST['acceptBtn'] <> ""))
{	
	$myCheckbox=$_POST['myCheckbox'];
	$mySupervisorId=$_SESSION['mySupervisorId'];
	$myStudentMatrixNo=$_SESSION['myStudentMatrixNo'];
	
	$curdatetime = date("Y-m-d H:i:s");
	while (list ($key,$val) = @each ($myCheckbox)) 
	{
		/*echo "mySupervisorId [".$val."] ".$mySupervisorId[$val]."<br/>";
		echo "myStudentMatrixNo [".$val."] ".$myStudentMatrixNo[$val]."<br/>";
		echo "myStudentName [".$val."] ".$myStudentName[$val]."<br/>";
		echo "mySupervisorType [".$val."] ".$mySupervisorType[$val]."<br/>";
		echo "myThesisId [".$val."] ".$myThesisId[$val]."<br/>";
		echo "myThesisTitle [".$val."] ".$myThesisTitle[$val]."<br/>";
		echo "myRespondedbyDate [".$val."] ".$myRespondedbyDate[$val]."<br/>";*/
							
		
		$sql1=	"UPDATE pg_supervisor 
				SET	
				acceptance_date = '$curdatetime' , 
				acceptance_status = 'ACC' , 
				acceptance_remarks = '$acceptanceRemark', 
				modify_by = '$user_id' , 
				modify_date = '$curdatetime' 	
				WHERE id = '$mySupervisorId[$val]'
				AND pg_employee_empid = '$user_id'  
				AND pg_student_matrix_no = '$myStudentMatrixNo[$val]'
				AND pg_thesis_id = '$thesisId'
				AND status = 'A'";

		//echo $sql1;exit();
		$result1 = $dba->query($sql1); 	
		//var_dump($db);
		$dba->next_record();
	}
	
}

if(isset($_POST['rejectBtn']) && ($_POST['rejectBtn'] <> ""))
{	
	$myCheckbox=$_POST['myCheckbox'];
	$mySupervisorId=$_SESSION['mySupervisorId'];
	$myStudentMatrixNo=$_SESSION['myStudentMatrixNo'];
	$curdatetime = date("Y-m-d H:i:s");
	while (list ($key,$val) = @each ($myCheckbox)) 
	{
		echo "mySupervisorId [".$val."] ".$mySupervisorId[$val]."<br/>";
		echo "myStudentMatrix [".$val."] ".$myStudentMatrixNo[$val]."<br/>";
		echo "myStudentName [".$val."] ".$myStudentName[$val]."<br/>";
		echo "mySupervisorType [".$val."] ".$mySupervisorType[$val]."<br/>";
		echo "myThesisId [".$val."] ".$myThesisId[$val]."<br/>";
		echo "myThesisTitle [".$val."] ".$myThesisTitle[$val]."<br/>";
		echo "myRespondedbyDate [".$val."] ".$myRespondedbyDate[$val]."<br/>";
							
		
		$sql2=	"UPDATE pg_supervisor 
				SET	
				acceptance_date = '$curdatetime' , 
				acceptance_status = 'REJ' , 
				acceptance_remarks = '$acceptanceRemark', 
				modify_by = '$user_id' , 
				modify_date = '$curdatetime' 	
				WHERE id = '$mySupervisorId[$val]'
				AND pg_employee_empid = '$user_id'  
				AND pg_student_matrix_no = '$myStudentMatrixNo[$val]'  
				AND status = 'A'";

		//echo $sql2;
		exit();
		$result2 = $dba->query($sql2); 	
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
		<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>
	</head>
	<body>
		<form id="form2" name="form2" method="post" enctype="multipart/form-data">
			<fieldset>
			<legend><strong>List of Thesis</strong></legend>
			<?
				$row_cnt = mysql_num_rows($result1);
					if ($row_cnt>0) {?>
					<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1">
					<tr>
						<td width="30"><strong>Tick</strong></td>
						<td width="24"><strong>No.</strong></td>
						<td width="89"><strong>Matrix No.</strong></td>
						<td width="137"><strong>Student Name</strong></td>
						<td width="78"><strong>Staff Role</strong></td>
						<td width="104"><strong>Thesis/Project ID</strong></td>						
						<td width="152"><strong>Thesis / Project Title</strong></td>
						<td width="115"><strong>Senate Approval Date</strong></td> 						
						<td width="115"><strong>Please Reply By</strong></td> 
					</tr>
					<?
					$no=0;
					do {
						$supervisorId=$db->f('id');	
						$studentMatrixNo=$db->f('pg_student_matrix_no');
						$studentName=$db->f('student_name');
						$supervisorTypeId=$db->f('ref_supervisor_type_id');
						$supervisorType=$db->f('supervisor_type');
						$thesisId=$db->f('pg_thesis_id');
						$thesisTitle=$db->f('thesis_title');						
						$respondedbyDate=$db->f('respondedby_date');
						$senateDate=$db->f('senate_date');
						$proposalId=$db->f('proposal_id');

					?>
						<tr>
							<td><input name="myCheckbox[]" type="checkbox" value="<?=$no;?>"/></td>
							<td><?=$no+1?></td>
							<input type="hidden" name="supervisorId[]" size="30" id="supervisorId" value="<?=$supervisorId?>" disabled="disabled"/>							
							<?$mySupervisorId[$no]=$supervisorId;?>
							
							<td><label name="studentMatrixNo[]" size="15" id="studentMatrixNo" ></label><?=$studentMatrixNo?></td>
							<?$myStudentMatrixNo[$no]=$studentMatrixNo;?>

							
							<td><label name="studentName[]" size="30" id="studentName" ></label><?=$studentName?></td>
							<?$myStudentName[$no]=$studentName;?>
							
							<td><label name="supervisorType[]" size="15" id="supervisorType" ></label><?=$supervisorType?></td>
							<?$mySupervisorType[$no]=$supervisorType;?>
							
							<td><a href="accept_invitation_outline_admin.php?thesisId=<? echo $thesisId;?>&proposalId=<? echo $proposalId;?>" name="thesisId[]" title="Outline of Proposed Case Study by the Student - Read more..."><?=$thesisId;?></a></td>	
							
							<td><label name="thesisTitle[]" cols="30" rows="3" id="thesisTitle" ></label><?=$thesisTitle?></td>														
							<?$myThesisTitle[$no]=$thesisTitle;?>
							
							<td><label name="senateDate[]" size="15" id="senateDate" ></label><?=$senateDate?></td>
							<?$mySenateDate[$no]=$senateDate;?>

							<td><label name="respondedbyDate[]" size="15" id="respondedbyDate" ></label><?=$respondedbyDate?></td>
							<?$myRespondedbyDate[$no]=$respondedbyDate;?>							
					<?
					$no=$no+1;
					}while($db->next_record());	
					?>	

				</table>
				<br/>
				<table>
					<tr>
						<td><strong>Note:</strong> Please enter your reason here if to reject the appointment as Supervisor/Co-Supervisor.</td>
					</tr>
				</table>
				<table>
					<tr>
						<td>Remark</td>
						<td><textarea name="acceptanceRemark" cols="30" rows="3" class="ckeditor" value="<?=$acceptanceRemark;?>"> </textarea></td>
						<td></td>
					</tr>
					<?$_SESSION['myCheckbox'] = $myCheckbox;?>
					<?$_SESSION['mySupervisorId'] = $mySupervisorId;?>
					<?$_SESSION['myStudentMatrixNo'] = $myStudentMatrixNo;?>					
					<tr>
						<td></td>
						<td></label><input type="submit" name="acceptBtn" value="Accept Invitation" />
						</label><input type="submit" name="rejectBtn" value="Not Accept Invitation" /></td>
					</tr>
				</table>
				
				<br/>
				<table>					
					<tr>
						<td><strong>DUTIES OF SUPERVISOR</strong></td>
					</tr>
					<tr>
						<td>A Supervisor shall be responsible to:-</td>
					</tr>
				</table>
				<table>
					<tr>
						<td>(a)</td>
						<td>assist in the supervision of appointed postgraduate candidate/s in MSU and in meeting the high standards prescribed for such programmes.</td>
					</tr>
					<tr>
						<td>(b)</td>
						<td>liase with the Coordinator of PhD programme at Graduate School of Management, Faculty of Business Management & Professional Studies of MSU.</td>
					</tr>
					<tr>
						<td>(c)</td>
						<td>maintain regular contact with the postgraduate candidate/s.</td>
					</tr>
					<tr>
						<td>(d)</td>
						<td>may supervise one or more candidates concurrently during the period of appointment.</td>
					</tr>
				</table>
				</fieldset>				
				<?
				}
				else {
					?>
					
					<table>
						<tr>
							<td>
								<p>Currently you don't have an invitation by the Senate as Supervisor/Co-Supervisor for post graduate student.</p>
							</td>
						</tr>
					</table>
					<?
				}				
				?>			
					
		</form>
	</body>
</html>