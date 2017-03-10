<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: assign_examiner_view.php
//
// Created by: Zuraimi
// Created Date: 11-Jun-2015
// Modified by: Zuraimi
// Modified Date: 11-Jun-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

session_start();
$userid=$_SESSION['user_id'];
$matrixNo=$_REQUEST['mn'];
$sname=$_REQUEST['sname'];
$thesisId=$_REQUEST['tid'];

//used for pagination-------------------
	$page = ($_GET['page'] == 0 ? 1 : $_GET['page']);
	$perpage = 2;
	$startpoint = ($page * $perpage) - $perpage;

$varParamSend="";
$curdatetime = date("Y-m-d H:i:s");

foreach($_REQUEST as $key => $value)
{
	if($key!="page")
		$varParamSend.="&$key=$value";
}
//---------------------------------------

$sql6 = "SELECT a.id, a.pg_employee_empid, 
a.ref_supervisor_type_id, e.description AS stype,  a.acceptance_status, f.description as  acceptance_desc,
DATE_FORMAT(a.acceptance_date,'%d-%b-%Y') AS acceptance_date, DATE_FORMAT(a.assigned_date,'%d-%b-%Y') AS assigned_date,
DATE_FORMAT(a.respondedby_date,'%d-%b-%Y') AS respondedby_date
FROM pg_supervisor a
LEFT JOIN ref_supervisor_type e ON (e.id = a.ref_supervisor_type_id)
LEFT JOIN ref_acceptance_status f ON (f.id = a.acceptance_status)
WHERE a.pg_student_matrix_no = '$matrixNo' 
AND a.ref_supervisor_type_id in ('EE','EI', 'EC', 'XE')
AND a.pg_thesis_id = '$thesisId'
AND a.status = 'A' 
ORDER BY e.seq, a.pg_employee_empid";

$result_sql6 = $dbe->query($sql6); 
$dbe->next_record();
$row_cnt6 = mysql_num_rows($result_sql6);
	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Untitled Document</title>
		<link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
		<link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
		<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
		
		<script src="../../../lib/js/jquery.min2.js"></script>
		<script src="../../../lib/js/jquery.colorbox.js"></script>
		<script src="../../lib/js/jquery.mask_input-1.3.js"></script>
		<script src="../../../lib/js/jquery.min2.js"></script>
   		<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
    	<script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
		<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>	
		<script language="JavaScript" src="../js/windowopen.js"></script>
		<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>
	</head>
	
	<body>
	<form id="form1" name="form1" method="post" enctype="multipart/form-data">
	<fieldset>
		<legend><strong>List of Assigned Examiner</strong></legend>
		<table>
			<tr>
				<td>Student Matrix No</td>
				<td>:</td>
				<td><?=$matrixNo?></td>
			</tr>
			<?
			$sql5 = "SELECT name as student_name
				FROM student 
				WHERE matrix_no = '$matrixNo'";
				if (substr($matrixNo,0,2) != '07') { 
					$dbConnStudent= $dbc; 
				} 
				else { 
					$dbConnStudent=$dbc1; 
				}	
				$result5 = $dbConnStudent->query($sql5); 
				$dbConnStudent->next_record();
				$sname=$dbConnStudent->f('student_name');
			?>
			<tr>
				<td>Student Name</td>
				<td>:</td>
				<td><?=$sname?></td>
			</tr>
		</table>
		<br/>
		<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="100%" class="thetable">
			<tr>				
				<th width="5%"><strong>No.</strong></th>
				<th width="5%"><strong>Dept</strong></th>
				<th width="15%" align="left"><strong>Name</strong></th> 
				<th width="10%" align="left"><strong>Role</strong></th>
				<th width="15%" align="left"><strong>Qualification</strong></th>
				<th width="10%" align="left"><strong>Field of Expertise</strong></th>
				<th width="15%" align="left"><strong>Email ID</strong></th>
				<th width="10%"><strong>Assigned Date</strong></th>
				<th width="10%"><strong>Expected Reply Date</strong></th>					
				<th width="5%" align="left"><strong>Acceptance Status</strong></th>				
			</tr>    
			<?

			$no1=0;
			$myArrayNo=0;
			if ($row_cnt6 > 0) {
				do {						
					$supervisorId=$dbe->f('id');	
					$employeeId=$dbe->f('pg_employee_empid');	
					$departmentId=$dbe->f('dept_id');
					$department=$dbe->f('department');
					$refSupervisorType=$dbe->f('ref_supervisor_type_id');
					$acceptanceDesc=$dbe->f('acceptance_desc');
					$acceptanceDate=$dbe->f('acceptance_date');
					$assignedDate=$dbe->f('assigned_date');
					$respondedByDate=$dbe->f('respondedby_date');
					$examinerRole=$dbe->f('stype');
				?>
				<tr>
					<input type="hidden" name="supervisorId" value="<?=$supervisorId;?>"/>								
					<td align="center"><?=$no1+1;?>.</td>
					<?
					$sql4="SELECT a.id as dept_id, a.description as department
					FROM dept_unit a
					LEFT JOIN new_employee b ON (b.unit_id = a.id)
					WHERE empid = '$employeeId'";
					
					$dbc->query($sql4);
					$row_personal=$dbc->fetchArray();
					$departmentId=$row_personal['dept_id'];
					$department=$row_personal['department'];
					?>	
					
					<td align="center"><a><width="20" height="19" style="border:0px;" title="<?=$department;?>"><?=$departmentId;?></a></td>
					<?
						$sql4 = "SELECT name AS employee_name
								FROM new_employee 
								WHERE empid = '$employeeId'";
									
								$result4 = $dbc->query($sql4); 
								$dbc->next_record();
								$employeeName=$dbc->f('employee_name');
					
					?>
					<td align="left"><label name="employeeName" size="50" id="employeeName" ></label><?=$employeeName;?><br/>(<?=$employeeId;?>)</td>
					<td><label><?=$examinerRole;?></label></td>
					<td>
						<?
						$sql_expertise = "SELECT descrip
						FROM education
						WHERE empid='$employeeId'
						AND LEVEL IN ('4','5')
						ORDER BY LEVEL";
						
						$sql_expertise;
						$result_sql_expertise = $dbc->query($sql_expertise); 
						$dbc->next_record();
						
						do {
							$educationDesc=$dbc->f('descrip');	
						?>
						
						<label name="myExpertise" size="50" id="expertise" >- <?=$educationDesc;?></label><br/>
						<?
						} while ($dbc->next_record())
						
				
						?></td>					
					<td align="left">
						<?$sqlAreaExpertise = "SELECT a.expertise, b.area
							FROM employee_expertise a
							LEFT JOIN job_list_category b ON (b.jobarea = a.expertise)
							WHERE a.empid = '$employeeId'";
							
							$resultSqlAreaExpertise = $dbc->query($sqlAreaExpertise); 
							$dbc->next_record();
							
							do {
								$area=$dbc->f('area');
								if ($area!="") {							
									?><label>- <?=$area;?></label><br/><?
								}
							} while ($dbc->next_record());
							?>					
					</td>
					
					<?
						$sql4 = "SELECT email
								FROM new_employee 
								WHERE empid = '$employeeId'";
									
								$result4 = $dbc->query($sql4); 
								$dbc->next_record();
								$emailId=$dbc->f('email');
					
					?>
					<td align="left"><label  name="emailId[]" id="emailId" size="20" ></label><?=$emailId?></td>
					<?$emailId[$no1]=$emailId;?>
					
					<td align="center"><label name="myAssignedDate[]" size="15" id="assignedDate" ><?=$assignedDate;?></label></td>
				
					<td align="center"><label name="myRespondedByDate[]" size="15" id="respondedByDate" ><?=$respondedByDate;?></label></td>

					<?if ($acceptanceDesc==null || $acceptanceDesc=="")
					{?>
						<td></td>
					<?
					}
					else 
					{
						?><td align="left"><label  name="myAcceptance" id="myAcceptance" size="20" ><?=$acceptanceDesc?> on <?=$acceptanceDate?></label></td>
						<?
					}?>
						
				</tr>  
				<?
				$no1=$no1+1;
				$myArrayNo1=$myArrayNo1+1;
				} while($dbe->next_record());	
			}
			else {
				?>
				<table>
					<tr>
						<td><label>No record found!</label></td>
					</tr>
				</table>
				<?
			}
			?>					
		</table>
		</fieldset>
		<table>
			<tr>
				<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../examiner/assign_examiner.php';" /></td>				
			</tr>
		</table>
	</form>
	</body>
</html>