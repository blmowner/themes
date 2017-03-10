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



if(isset($_POST['acceptBtn']) && ($_POST['acceptBtn'] <> ""))
{	
	$myCheckbox=$_POST['myCheckbox'];
	$mySupervisorId=$_SESSION['mySupervisorId'];
	$myStudentMatrixNo=$_SESSION['myStudentMatrixNo'];
	$myThesisId=$_SESSION['myThesisId'];
	
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
				AND pg_thesis_id = '$myThesisId[$val]'
				AND status = 'A'";

		$result1 = $dba->query($sql1); 	
		$dba->next_record();
	}
	
}

if(isset($_POST['rejectBtn']) && ($_POST['rejectBtn'] <> ""))
{	
	$myCheckbox=$_POST['myCheckbox'];
	$mySupervisorId=$_SESSION['mySupervisorId'];
	$myStudentMatrixNo=$_SESSION['myStudentMatrixNo'];
	$myThesisId=$_SESSION['myThesisId'];
	
	$curdatetime = date("Y-m-d H:i:s");
	while (list ($key,$val) = @each ($myCheckbox)) 
	{
		/*echo "mySupervisorId [".$val."] ".$mySupervisorId[$val]."<br/>";
		echo "myStudentMatrix [".$val."] ".$myStudentMatrixNo[$val]."<br/>";
		echo "myStudentName [".$val."] ".$myStudentName[$val]."<br/>";
		echo "mySupervisorType [".$val."] ".$mySupervisorType[$val]."<br/>";
		echo "myThesisId [".$val."] ".$myThesisId[$val]."<br/>";
		echo "myThesisTitle [".$val."] ".$myThesisTitle[$val]."<br/>";
		echo "myRespondedbyDate [".$val."] ".$myRespondedbyDate[$val]."<br/>";*/
							
		
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
				AND pg_thesis_id = '$myThesisId[$val]'
				AND status = 'A'";

		$result2 = $dba->query($sql2); 	
		$dba->next_record();
	}
	
}

$sql1 = "SELECT a.id, a.assigned_by, DATE_FORMAT(a.assigned_date,'%d-%b-%Y') AS assigned_date, 
		DATE_FORMAT(a.acceptance_date,'%d-%b-%Y') AS acceptance_date, a.acceptance_status, a.acceptance_remarks,
		DATE_FORMAT(a.respondedby_date,'%d-%b-%Y') AS respondedby_date, 
		DATE_FORMAT(f.endorsed_date,'%d-%b-%Y') AS senate_mtg_date, a.pg_student_matrix_no, 
		a.ref_supervisor_type_id, b.description AS supervisor_type, 
		e.pg_thesis_id, e.thesis_title, e.id as proposal_id
		FROM pg_supervisor a 
		LEFT JOIN ref_supervisor_type b ON (b.id = a.ref_supervisor_type_id) 
		LEFT JOIN pg_thesis d ON (d.student_matrix_no = a.pg_student_matrix_no) 
		LEFT JOIN pg_proposal e ON (e.pg_thesis_id = d.id) 
		LEFT JOIN pg_proposal_approval f ON (f.id = e.pg_proposal_approval_id)
		WHERE a.ref_supervisor_type_id in ('SV','CS')
		AND a.status = 'A' 
		AND a.acceptance_status IS NULL
		AND a.pg_employee_empid = '$userid' 
		AND a.acceptance_status IS NULL 
		AND d.status = 'INP' 
		AND e.verified_status in ('APP','AWC')
		AND e.status in ('APP','APC')";

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
					<legend><strong>List of Thesis</strong></legend>
					<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1">
					<tr>
						<td width="30"><strong>Tick</strong></td>
						<td width="24"><strong>No.</strong></td>
						<td width="137"><strong>Student Name</strong></td>
						<td width="78"><strong>Staff Role</strong></td>
						<td width="104"><strong>Thesis ID</strong></td>						
						<td width="152"><strong>Thesis / Project Title</strong></td>
						<td width="115"><strong>Senate Approval Date</strong></td> 						
						<td width="115"><strong>Reply Date (Due Date)</strong></td>
						<td width="115"><strong>Acceptance Remarks</strong></td>
						<td width="152"><strong>Attachment by Student</strong></td>
						 
					</tr>
					<?
					
					$no=0;
					do {
						$supervisorId=$db->f('id');	
						$studentMatrixNo=$db->f('pg_student_matrix_no');
						$supervisorTypeId=$db->f('ref_supervisor_type_id');
						$supervisorType=$db->f('supervisor_type');
						$thesisId=$db->f('pg_thesis_id');
						$thesisTitle=$db->f('thesis_title');						
						$respondedByDate=$db->f('respondedby_date');
						$senateMtgDate=$db->f('senate_mtg_date');
						$proposalId=$db->f('proposal_id');
						$acceptanceRemarks=$db->f('acceptance_remarks');

					?>
						<tr>
							<? if ($acceptanceStatus=='ACC'){
									?><td align="center"><input name="myCheckbox[]" type="checkbox" value="<?=$no;?>" disabled="disabled"/></td>
							<?}
								else {
									?><td align="center"><input name="myCheckbox[]" type="checkbox" value="<?=$no;?>"/></td><?
								}
								?>
							<td align="center"><?=$no+1?>.</td>
							<input type="hidden" name="supervisorId[]" size="30" id="supervisorId" value="<?=$supervisorId?>" disabled="disabled"/>							
							<?$mySupervisorId[$no]=$supervisorId;?>
							
							<?$myStudentMatrixNo[$no]=$studentMatrixNo;?>
							
							<?
							$sql1 = "SELECT name as student_name
									FROM student
									WHERE matrix_no = '$studentMatrixNo'";
							
							$result1 = $dbc->query($sql1);
							$dbc->next_record();
							$studentName=$dbc->f('student_name');
						
							?>

							
							<td><label name="studentName[]" size="30" id="studentName" ></label><?=$studentName?><br/>(<?=$studentMatrixNo?>)</td>
							<?$myStudentName[$no]=$studentName;?>
							
							<td><label name="supervisorType[]" size="15" id="supervisorType" ></label><?=$supervisorType?>
							<br/><a href="../supervisor/accept_invitation_partner.php?tid=<?=$thesisId;?>&sname=<?=$studentName?>&mn=<?=$studentMatrixNo?>"><br/><img src="../images/person_assigned.jpg" width="20" height="19" style="border:0px;" title="Supervisor has been assigned" />Partners </a>
							</td>
							<?$mySupervisorType[$no]=$supervisorType;?>
							
							<td><a href="accept_invitation_outline.php?thesisId=<? echo $thesisId;?>&proposalId=<? echo $proposalId;?>" name="thesisId[]" title="Outline of Proposed Case Study by the Student - Read more..."><?=$thesisId;?></a></td>	
							<?$myThesisId[$no]=$thesisId;?>
							
							<td><label name="thesisTitle[]" cols="30" rows="3" id="thesisTitle" ></label><?=$thesisTitle?></td>														
							<?$myThesisTitle[$no]=$thesisTitle;?>
							
							<td><label name="senateMtgDate[]" size="15" id="senateMtgDate" ></label><?=$senateMtgDate?></td>
							<?$mySenateMtgDate[$no]=$senateMtgDate;?>

							<td><label name="respondedByDate[]" size="15" id="respondedByDate" ></label><?=$respondedByDate?></td>
							<?$myRespondedByDate[$no]=$respondedByDate;?>	


							
							
							<td><a href="accept_invitation_detail.php?sid=<?=$supervisorId;?>" name="acceptanceRemarks[]" value="<?=$acceptanceRemarks?>" title=""><br/>
						
						
						<?
						if (strlen($acceptanceRemarks) == 0) {?>
						
							<img src="../images/red_edit.jpg" width="20" height="19" style="border:0px;" title="Enter remarks here" ></a>Enter Remarks</td>	
						<?}
						else {
						?>
							<img src="../images/edit.jpg" width="20" height="19" style="border:0px;" title="Read remarks detail" ></a>Read Remarks</td>	
						<?
						}?>
						
												
						<? $myAceptanceRemarks[$no]=$acceptanceRemarks;?>
							
							
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
													<a href="download.php?fc=<?=$row["fu_cd"];?>&al=S"><?=$row["fu_document_filename"];?><img src="../images/download.png" width="20" height="19" style="border:0px;" title="Download"></a><br/>
													
															
												
										<?}
										?></td><?
									}
									else {
										?><td>No attachment</td><?
									}
								?>	
					<?
					$no=$no+1;
					}while($db->next_record());	
					?>	

				</table>
				<br/>
				<table>
					<tr>
						<td><span class="style1">Note:</span> Please provide <strong>Acceptance Remarks</strong> above if you are not willing to accept the appointment.</td>
					</tr>
				</table>
				<br/>
				<table>
					
					<?$_SESSION['myCheckbox'] = $myCheckbox;?>
					<?$_SESSION['mySupervisorId'] = $mySupervisorId;?>
					<?$_SESSION['myStudentMatrixNo'] = $myStudentMatrixNo;?>	
					<?$_SESSION['myThesisId'] = $myThesisId;?>					
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
					
					<fieldset>
					<legend><span style="color:#FF0000"> Notification Message:</span></legend>
						<table>
							<tr>
								<td>
									<p>There is no invitation from Faculty for you to accept as Supervisor/Co-Supervisor for any Postgrad student.</p>
								</td>
							</tr>
						</table>
						</fieldset>
						<table>
						<tr>
							<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../supervisor/accept_invitation.php';" /></td>
						</tr>
						</table>
					<?
				}				
				?>			
					
		</form>
	</body>
</html>