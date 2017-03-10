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
$studentName=$_REQUEST['sname'];
$matrixNo=$_REQUEST['mn'];



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
				AND pg_thesis_id = '$thesisId'
				AND status = 'A'";

		//echo $sql2;
		//exit();
		$result2 = $dba->query($sql2); 	
		$dba->next_record();
	}
	
}

$sql1 = "SELECT a.id, a.pg_employee_empid, g.name as staff_name, g.skype_id,
			DATE_FORMAT(a.acceptance_date,'%d-%b-%Y') AS acceptance_date, a.acceptance_status, h.description as acceptance_desc,
			a.ref_supervisor_type_id, b.description AS supervisor_type, a.brief_biodata 
			FROM pg_supervisor a 
			LEFT JOIN ref_supervisor_type b ON (b.id = a.ref_supervisor_type_id) 
			LEFT JOIN student c ON (c.matrix_no = a.pg_student_matrix_no) 
			LEFT JOIN pg_thesis d ON (d.student_matrix_no = c.matrix_no) 
			LEFT JOIN pg_proposal e ON (e.pg_thesis_id = d.id) 
			LEFT JOIN pg_proposal_approval f ON (f.id = e.pg_proposal_approval_id) 
			LEFT JOIN new_employee g ON (g.empid = a.pg_employee_empid)
			LEFT JOIN ref_acceptance_status h ON (h.id = a.acceptance_status)
			WHERE a.ref_supervisor_type_id IN ('SV','CS')
			AND a.status = 'A' 
			AND (a.acceptance_status IS NULL OR a.acceptance_status = 'ACC')
			AND a.pg_employee_empid NOT IN ('$userid')
			AND d.status = 'INP' 
			AND e.verified_status = 'APP' 
			AND e.status = 'APP'";

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
			
			
					<fieldset>
					<legend><strong>List of Partner</strong></legend>
					<table>
						<tr>
							<td>Student Matrix No</td>
							<td>:</td>
							<td><?=$matrixNo?></td>
						</tr>
						<tr>
							<td>Student Name</td>
							<td>:</td>
							<td><?=$sname?></td>
						</tr>
					</table>
					<br/>
					<?
					$row_cnt = mysql_num_rows($result1);
					if ($row_cnt>0) {?>
					<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1">
					<tr>
						<td width="30"><div align="center"><strong>No.</strong></div></td>
						<td width="200"><strong>Partner Name</strong></td>
						<td width="100"><strong>Brief Biodata</strong></td>
						<td width="200"><strong>Qualification</strong></td>
						<td width="200"><strong>Area of Expertise</strong></td>
						<td width="150"><strong>Skype ID</strong></td>						
						<td width="100"><strong>Acceptance Date</strong></td>
						<td width="100"><strong>Acceptance Status</strong></td>
					</tr>
					<?
					$no=0;
					do {
						$supervisorId=$db->f('id');	
						$employeeId=$db->f('pg_employee_empid');
						$staffName=$db->f('staff_name');
						$supervisorTypeId=$db->f('ref_supervisor_type_id');
						$supervisorType=$db->f('supervisor_type');
						$skypeId=$db->f('skype_id');
						$acceptanceDate=$db->f('acceptance_date');
						$acceptanceStatus=$db->f('acceptance_status');	
						$acceptanceDesc=$db->f('acceptance_desc');
						$briefBiodata=$db->f('brief_biodata');
						
					?>
						<tr>
							<td><div align="center"><?=$no+1?>.</div></td>
							<input type="hidden" name="supervisorId[]" size="30" id="supervisorId" value="<?=$supervisorId?>" disabled="disabled"/>	<td><label name="staffName[]" size="30" id="staffName" ></label><?=$staffName?><br/>(<?=$employeeId?>)<br/>(<?=$supervisorType?>)</td>
							<td><label name="briefBiodata[]" size="50" id="briefBiodata" ><?=$briefBiodata;?></label></td>
							<td>
								<?
								$sql_expertise1 = "SELECT descrip
								FROM education
								WHERE empid='$employeeId'
								AND LEVEL IN ('4','5')
								ORDER BY LEVEL";
								
								$sql_expertise1;
								$result_sql_expertise1 = $dba->query($sql_expertise1); 
								$dba->next_record();
								
								do {
									$educationDesc1=$dba->f('descrip');	
								?>
								
								<label name="educationDesc1[]" size="50" id="educationDesc1" >- <?=$educationDesc1;?></label><br/>
								<?
								} while ($dba->next_record())
								
								
								?>							</td>
							<td>
								<?$sqlAreaExpertise = "SELECT a.expertise, b.area
									FROM employee_expertise a
									LEFT JOIN job_list_category b ON (b.jobarea = a.expertise)
									WHERE empid = '$employeeId'";
									
									$resultSqlAreaExpertise = $dbb->query($sqlAreaExpertise); 
									$dbb->next_record();
									
									do {
										$area=$dbb->f('area');
										if ($area!="") {							
											?><label>- <?=$area;?></label><br/><?
										}
									} while ($dbb->next_record());
									?>							</td>					
							<td><label name="skypeId[]" size="15" id="skypeId" ></label><?=$skypeId?></td>	
							<td><label name="acceptanceDate[]" size="15" id="acceptanceDate" ></label><?=$acceptanceDate?></td>
							<td><label name="acceptanceStatus[]" size="15" id="acceptanceStatus" ></label><?=$acceptanceDesc?></td>
					<?
					$no=$no+1;
					}while($db->next_record());	
					?>	
				</table>
				<br/>				
				<table>
					
					<?$_SESSION['myCheckbox'] = $myCheckbox;?>
					<?$_SESSION['mySupervisorId'] = $mySupervisorId;?>
					<?$_SESSION['myStudentMatrixNo'] = $myStudentMatrixNo;?>					
					<tr>
						<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../supervisor/accept_invitation.php';" /></td>
					</tr>
				</table>
				
				<br/>
				
				</fieldset>				
				<?
				}
				else {
					?>
					
					<fieldset>
					<legend><strong><span class="style1">Notification Message</span></strong></legend>
						<table>
							<tr>
								<td>
									<p>There is no partner assigned with you to supervise this student at this moment.</p>
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