<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: accept_invitation_partner.php
//
// Created by: Zuraimi
// Created Date: 21-Jul-2015
// Modified by: Zuraimi
// Modified Date: 21-Jul-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

session_start();
$user_id=$_SESSION['user_id'];
$studentMatrixNo=$_REQUEST['mn'];
$thesisId=$_REQUEST['tid'];
$invitationId=$_REQUEST['vid'];

$sql1 = "SELECT a.id as invitation_id, b.id as invitation_detail_id, b.pg_supervisor_id, a.pg_student_matrix_no, 
a.pg_thesis_id, e.id AS proposal_id, e.thesis_title, b.acceptance_remarks,
c.ref_supervisor_type_id, g.description AS supervisor_type, 
b.acceptance_status, j.description as acceptance_status_desc,
DATE_FORMAT(b.acceptance_date,'%d-%b-%Y %h:%i%p') AS acceptance_date, 
b.pg_employee_empid, k.biodata
FROM pg_invitation a 
LEFT JOIN pg_invitation_detail b ON (b.pg_invitation_id = a.id) 
LEFT JOIN pg_supervisor c ON (c.id = b.pg_supervisor_id) 
LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id)
LEFT JOIN pg_proposal e ON (e.pg_thesis_id = d.id)
LEFT JOIN pg_calendar f ON (f.id = a.pg_calendar_id)
LEFT JOIN ref_supervisor_type g ON (g.id = c.ref_supervisor_type_id) 
LEFT JOIN ref_acceptance_status j ON (j.id = b.acceptance_status)
LEFT JOIN pg_employee k ON (k.staff_id = b.pg_employee_empid)
WHERE b.pg_employee_empid <> '$user_id' 
AND a.pg_student_matrix_no = '$studentMatrixNo'
AND a.id = '$invitationId'
AND a.status = 'A' 
AND b.status = 'A' 
AND d.status = 'INP' 
AND e.verified_status IN ('APP','AWC') 
AND e.status IN ('APP','APC')
AND e.archived_status IS NULL 
AND c.ref_supervisor_type_id IN ('EE','EI','EC')
ORDER BY g.seq";
	
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
	<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
	<script src="../../../lib/js/jquery.min2.js"></script>
    <script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
	<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>
	</head>
	<body>
		<form id="form2" name="form2" method="post" enctype="multipart/form-data">
			
			
					<fieldset>
					<legend><strong>List of Partner</strong></legend>
					<table>
						<tr>
							<td>Student Matrix No</td>
							<td>:</td>
							<td><?=$studentMatrixNo?></td>
						</tr>
						<?
						$sql9 = "SELECT name
						FROM student
						WHERE matrix_no = '$studentMatrixNo'";
						if (substr($studentMatrixNo,0,2) != '07') { 
							$dbConnStudent= $dbc; 
						} 
						else { 
							$dbConnStudent=$dbc1; 
						}
						$result9 = $dbConnStudent->query($sql9); 
						$dbConnStudent->next_record();
						$studentName = $dbConnStudent->f('name');
						?>
						<tr>
							<td>Student Name</td>
							<td>:</td>
							<td><?=$studentName?></td>
						</tr>
					</table>
					<br/>
					<?
					$row_cnt = mysql_num_rows($result1);
					if ($row_cnt>0) {?>
					<table width="100%" border="1" cellpadding="3" cellspacing="1" style="border-collapse:collapse;" class="thetable">
					<tr>
						<th width="5%" align="left"><div align="center"><strong>No.</strong></div></th>
						<th width="20%" align="left"><strong>Partner Name</strong></th>
						<th width="20%" align="left"><strong>Brief Biodata</strong></th>
						<th width="10%" align="left"><strong>Role</strong></th>						
						<th width="20%" align="left"><strong>Qualification</strong></th>
						<th width="15%" align="left"><strong>Area of Expertise</strong></th>
						<th width="10%" align="left"><strong>Invitation Status</strong></th>
					</tr>
					<?
					$no=0;
					do {
						$employeeId=$db->f('pg_employee_empid');
						$supervisorTypeId=$db->f('ref_supervisor_type_id');
						$supervisorType=$db->f('supervisor_type');
						$acceptanceDate=$db->f('acceptance_date');
						$acceptanceStatus=$db->f('acceptance_status');	
						$acceptanceDesc=$db->f('acceptance_status_desc');
						$briefBiodata=$db->f('biodata');
						
					?>
						<tr>
							<td><div align="center"><?=$no+1?>.</div></td>
							<?
							$sql4="SELECT name AS staff_name
							FROM new_employee
							WHERE empid = '$employeeId'";
							
							$dbc->query($sql4);
							$row_personal=$dbc->fetchArray();
							$staffName=$row_personal['staff_name'];
							?>
							<td><label name="staffName[]" size="30" id="staffName" ></label><?=$staffName?><br/>(<?=$employeeId?>)</td>
							<td><label name="briefBiodata[]" size="50" id="briefBiodata" ><?=$briefBiodata;?></label></td>
							<td><label><?=$supervisorType?></label></td>	
							
							<td>
								<?
								$sql_expertise1 = "SELECT descrip
								FROM education
								WHERE empid='$employeeId'
								AND LEVEL IN ('4','5')
								ORDER BY LEVEL";
								
								$sql_expertise1;
								$result_sql_expertise1 = $dbc->query($sql_expertise1); 
								$dbc->next_record();
								
								do {
									$educationDesc1=$dbc->f('descrip');	
								?>
								
								<label>- <?=$educationDesc1;?></label><br/>
								<?
								} while ($dbc->next_record())
								
								
								?>	</td>
							<td>
								<?$sqlAreaExpertise = "SELECT a.expertise, b.area
									FROM employee_expertise a
									LEFT JOIN job_list_category b ON (b.jobarea = a.expertise)
									WHERE empid = '$employeeId'";
									
									$resultSqlAreaExpertise = $dbc->query($sqlAreaExpertise); 
									$dbc->next_record();
									
									do {
										$area=$dbc->f('area');
										if ($area!="") {							
											?><label>- <?=$area;?></label><br/><?
										}
									} while ($dbc->next_record());
									?></td>					
							<?
							$sql4="SELECT email
							FROM new_employee
							WHERE empid = '$employeeId'";
							
							$dbc->query($sql4);
							$row_personal=$dbc->fetchArray();
							$email=$row_personal['email'];
							?>
							<td><label><?=$acceptanceDesc?><br/><?=$acceptanceDate?></label></td>
					<?
					$no=$no+1;
					}while($db->next_record());	
					?>	
				</table>
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
									<p>There is no partner assigned with you to evaluate this student at this moment.</p>
								</td>
							</tr>
						</table>
					</fieldset>						
					<?
				}				
				?>			
				<table>
					<tr>
						<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../defense/accept_invitation.php';" /></td>
					</tr>
				</table>	
		</form>
	</body>
</html>