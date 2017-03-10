<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: view_supervisor_senate.php
//
// Created by: Zuraimi
// Created Date: 29-Dec-2014
// Modified by: Zuraimi
// Modified Date: 29-Dec-2014
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


$sql6 = "SELECT a.id, a.pg_employee_empid, d.id AS dept_id, d.description AS department, b.name, 
		a.ref_supervisor_type_id, e.description AS stype, b.skype_id,f.description as  acceptance_desc,
		DATE_FORMAT(a.acceptance_date,'%d-%b-%Y') AS acceptance_date, DATE_FORMAT(a.assigned_date,'%d-%b-%Y') AS assigned_date,
		DATE_FORMAT(a.respondedby_date,'%d-%b-%Y') AS respondedby_date		
		FROM pg_supervisor a
		LEFT JOIN ref_supervisor_type e ON (e.id = a.ref_supervisor_type_id)
		LEFT JOIN new_employee b ON (b.empid = a.pg_employee_empid)
		LEFT JOIN student c ON (c.matrix_no = a.pg_student_matrix_no)
		LEFT JOIN dept_unit d ON (d.id = b.unit_id)
		LEFT JOIN ref_acceptance_status f ON (f.id = a.acceptance_status)
		WHERE a.pg_student_matrix_no = '$matrixNo' 
		AND a.ref_supervisor_type_id in ('SV','CS','EI','EE','XS')
		AND a.pg_thesis_id = '$thesisId'
		AND a.status = 'A' 
		ORDER BY e.seq, d.id, a.pg_employee_empid, b.name";

		$dbe->query($sql6); 
		//$dbe->next_record();
		//echo $sql6;
		
$sql3 = "SELECT id, description
		FROM ref_supervisor_type
		WHERE status='A'";
 
$dbf->query($sql3); 
$dbf->next_record();
//echo $sql3;	
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
	</head>
	
	<body>
	<form id="form1" name="form1" method="post" enctype="multipart/form-data">
		<fieldset>
			<legend><strong>List of Assigned Supervisor</strong></legend>
		<br/>
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
		<table border="1" style="border-collapse:collapse;" cellpadding="3" cellspacing="1" width="100%">
			<tr>				
				<td align="center"><strong>No.</strong></td>
				<td><strong>Dept</strong></td>
				<td><strong>Name</strong></td> 
				<td><strong>Qualification</strong></td>
				<td><strong>Field of Expertise</strong></td>								
				<td><strong>Role</strong></td>
				<td><strong>Skype ID</strong></td>
				<td><strong>Assigned Date</strong></td>
				<td><strong>Expected Reply Date</strong></td>	
				<td><strong>Acceptance Status</strong></td>				
			</tr>    
			<?
			$dbe->query($sql6); 
			$dbe->next_record();
			//echo $sql6;
			$no1=0;
			$myArrayNo=0;
			do {						
				$supervisorId=$dbe->f('id');	
				$employeeId=$dbe->f('pg_employee_empid');	
				$departmentId=$dbe->f('dept_id');
				$department=$dbe->f('department');
				$employeeName=$dbe->f('name');				
				$refSupervisorType=$dbe->f('ref_supervisor_type_id');
				$stDescription=$dbe->f('stype');
				$skypeId=$dbe->f('skype_id');
				$acceptanceDesc=$dbe->f('acceptance_desc');
				$acceptanceDate=$dbe->f('acceptance_date');
				$assignedDate=$dbe->f('assigned_date');
				$respondedByDate=$dbe->f('respondedby_date');
			?>
			<tr>
				<input type="hidden" name="supervisorId" value="<?=$supervisorId;?>"/>								
				<td align="center"><?=$no1+1;?>.</td>				
				<td><a width="20" height="19" style="border:0px;" title="<?=$department;?>"><?=$departmentId;?></a></td>				
				
				<td><label name="myEmployeeName" size="50" id="employeeName"></label><?=$employeeName;?><br/>(<?=$employeeId;?>)</td>
									
				<td>
					<?
					$sql_expertise1 = "SELECT descrip
					FROM education
					WHERE empid='$employeeId'
					AND LEVEL IN ('4','5')
					ORDER BY LEVEL";
					
					$sql_expertise1;
					$result_sql_expertise1 = $db->query($sql_expertise1); 
					$db->next_record();
					
					do {
						$educationDesc1=$db->f('descrip');	
					?>
					
					<label name="qualification" size="50" id="qualification" >- <?=$educationDesc1;?></label><br/>
					<?
					} while ($db->next_record())
					
					
					?>					
					</td>
							
				<td><label name="expertise" type="text" id="expertise" size="50" ></label><?=$expertise;?></td>				
				<td><label name="stDescription" type="text" id="stDescription" size="50" ></label><?=$stDescription;?></td>							
				<td><label  name="mySkypeId" id="skypeId" size="20" ></label><?=$skypeId?></td>
				<td><label name="myAssignedDate[]" size="15" id="assignedDate" ><?=$assignedDate;?></label></td>
				
				<td><label name="myRespondedByDate[]" size="15" id="respondedByDate" ><?=$respondedByDate;?></label></td>

					<?if ($acceptanceDesc==null || $acceptanceDesc=="")
					{?>
						<td></td>
					<?
					}
					else 
					{
						?><td><label  name="myAcceptance" id="myAcceptance" size="20" ><?=$acceptanceDesc?> on <?=$acceptanceDate?></label></td>
						<?
					}?>
					
			</tr>  
			<?
			$no1=$no1+1;
			}while($dbe->next_record());	
			?>					
		</table>
		</fieldset>
		<br />
		<table>
			<tr>
				<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../thesis/senate_approval.php';" /></td>				
			</tr>
		</table>
		<br/>
		
	</form>
	</body>
</html>
