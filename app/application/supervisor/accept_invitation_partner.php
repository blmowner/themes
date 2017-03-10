<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: accept_invitation_partner.php
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



$sql1 = "SELECT a.id, a.pg_employee_empid, 
DATE_FORMAT(a.acceptance_date,'%d-%b-%Y') AS acceptance_date, a.acceptance_status, h.description as acceptance_desc,
a.ref_supervisor_type_id, b.description AS supervisor_type, i.biodata as brief_biodata
FROM pg_supervisor a 
LEFT JOIN ref_supervisor_type b ON (b.id = a.ref_supervisor_type_id) 
LEFT JOIN pg_thesis d ON (d.student_matrix_no = a.pg_student_matrix_no) 
LEFT JOIN pg_proposal e ON (e.pg_thesis_id = d.id) 
LEFT JOIN pg_proposal_approval f ON (f.id = e.pg_proposal_approval_id) 
LEFT JOIN ref_acceptance_status h ON (h.id = a.acceptance_status)
LEFT JOIN pg_employee i ON (i.staff_id = a.pg_employee_empid)
WHERE a.ref_supervisor_type_id IN ('SV','CS','XS')
AND a.status = 'A' 
AND (a.acceptance_status IS NULL OR a.acceptance_status = 'ACC')
AND a.pg_employee_empid NOT IN ('$userid')
AND d.status = 'INP' 
AND e.verified_status = 'APP' 
AND e.status = 'APP'";

$result1 = $db->query($sql1);
$db->next_record();
$row_cnt = mysql_num_rows($result1);


?>

	

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Untitled Document</title>
		<link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
		<link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
		<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
		<script src="../../lib/js/jquery.min2.js"></script>
		<script src="../../lib/js/jquery.colorbox.js"></script>
		<script src="../../lib/js/jquery.mask_input-1.3.js"></script>
		<script type="text/javascript" src="../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
		<script src="../../lib/js/datePicker/jquery.ui.core.js"></script>
		<script src="../../lib/js/datePicker/jquery.ui.widget.js"></script>
		<script src="../../lib/js/datePicker/jquery.ui.datepicker.js"></script>
		<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>
		<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
	    <style type="text/css"></style>
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
					<?
					$sql9 = "SELECT name
						FROM student
						WHERE matrix_no = '$matrixNo'";
					if (substr($matrixNo,0,2) != '07') { 
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
				<table>
					<tr>							
						<td>Searching Results:-</td>
					</tr>
				</table>					
				<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="100%" class="thetable">
					<tr>
						<th width="5%" align="center"><strong>No.</strong></th>
						<th width="20%" align="left"><strong>Partner Name</strong></th>
						<th width="15%" align="left"><strong>Brief Biodata</strong></th>
						<th width="15%" align="left"><strong>Qualification</strong></th>
						<th width="15%" align="left"><strong>Area of Expertise</strong></th>
						<th width="10%" align="left"><strong>Skype ID</strong></th>						
						<th width="10%"><strong>Acceptance Date</strong></th>
						<th width="10%" align="left"><strong>Acceptance Status</strong></th>
					</tr>
					<?
					if ($row_cnt>0) {
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
								<?
								$sql4="SELECT name AS staff_name
								FROM new_employee
								WHERE empid = '$employeeId'";
								
								$dbc->query($sql4);
								$row_personal=$dbc->fetchArray();
								$staffName=$row_personal['staff_name'];
								?>
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
									$result_sql_expertise1 = $dbc->query($sql_expertise1); 
									$dbc->next_record();
									
									do {
										$educationDesc1=$dbc->f('descrip');	
										?>
										<label name="educationDesc1[]" size="50" id="educationDesc1" >- <?=$educationDesc1;?></label><br/>
										<?
									} while ($dbc->next_record())
									?>							
								</td>
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
								$sql4="SELECT skype_id
								FROM new_employee
								WHERE empid = '$employeeId'";
								
								$dbc->query($sql4);
								$row_personal=$dbc->fetchArray();
								$skypeId=$row_personal['skype_id'];
								?>
								<td><label name="skypeId[]" size="15" id="skypeId" ></label><?=$skypeId?></td>	
								<td><label name="acceptanceDate[]" size="15" id="acceptanceDate" ></label><?=$acceptanceDate?></td>
								<td><label name="acceptanceStatus[]" size="15" id="acceptanceStatus" ></label><?=$acceptanceDesc?></td>
						<?
						$no=$no+1;
						}while($db->next_record());	
					}
					else {
						?>
						<table>
							<tr>
								<td>
									<p>There is no partner assigned with you to supervise this student at this moment.</p>
								</td>
							</tr>
						</table>
						<?
					}				
					?>	
				</table>
			</fieldset>		
			<table>							
				<tr>
					<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../supervisor/accept_invitation.php';" /></td>
				</tr>
			</table>	
		</form>
	</body>
</html>