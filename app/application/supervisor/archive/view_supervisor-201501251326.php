<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: view_supervisor.php
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


$sql6 = "SELECT a.id, a.pg_employee_empid, d.id AS dept_id, d.description AS department, b.name, a.expertise, 
		a.ref_supervisor_type_id, e.description AS stype, a.skype_id 
		FROM pg_supervisor a
		LEFT JOIN ref_supervisor_type e ON (e.id = a.ref_supervisor_type_id)
		LEFT JOIN new_employee b ON (b.empid = a.pg_employee_empid)
		LEFT JOIN student c ON (c.matrix_no = a.pg_student_matrix_no)
		LEFT JOIN dept_unit d ON (d.id = b.unit_id)
		WHERE a.pg_student_matrix_no = '$matrixNo' 
		AND a.status = 'A' 
		ORDER BY d.id, a.pg_employee_empid, b.name";

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
		<table>
			<tr>
				<td><strong>List of Assigned Supervisor</strong></td>
			</tr>
		</table>
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
		<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1">
			<tr>				
				<td><strong>No.</strong></td>
				<td><strong>Department</strong></td>
				<td><strong>Staff ID</strong></td>
				<td><strong>Supervisor Name</strong></td>  
				<td><strong>Field of Expertise</strong></td>
				<td><strong>Supervisor Type</strong></td>
				<td><strong>Skype ID</strong></td>		  
			</tr>    
			<?
			$dbe->query($sql6); 
			$dbe->next_record();
			//echo $sql6;
			$no1=0;
			$myArrayNo=0;
			do {						
				//a.id, a.pg_employee_empid, d.description, b.name, a.expertise, a.ref_supervisor_type_id, e.description, a.skype_id
				
				$supervisorId=$dbe->f('id');	
				$employeeId=$dbe->f('pg_employee_empid');	
				$departmentId=$dbe->f('dept_id');
				$department=$dbe->f('department');
				$employeeName=$dbe->f('name');
				$expertise=$dbe->f('expertise');
				$refSupervisorType=$dbe->f('ref_supervisor_type_id');
				$stDescription=$dbe->f('stype');
				$skypeId=$dbe->f('skype_id');
			?>
			<tr>
				<input type="hidden" name="supervisorId" value="<?=$supervisorId;?>"/>								
				<td><?=$no1+1;?>.</td>
				
				<td><label name="departmentId" id="departmentId"></label><?=$departmentId;?></td>
				
				<td><label name="employeeId" size="10" id="employeeId" ></label><?=$employeeId;?></td>
				
				<td><label name="employeeName" size="50" id="employeeName" ></label><?=$employeeName;?></td>
										
				<td><label name="expertise" type="text" id="expertise" size="50" ></label><?=$expertise;?></td>
				
				<td><label name="stDescription" type="text" id="stDescription" size="50" ></label><?=$stDescription;?></td>
				
			
				<td>
			    <label  name="mySkypeId1[]" id="skypeId1" size="20" ></label><?=$skypeId1?></td>
				<?$mySkypeId1[$no1]=$skypeId1;?>
			</tr>  
			<?
			$no1=$no1+1;
			$myArrayNo1=$myArrayNo1+1;
			}while($dbe->next_record());	
			?>					
		</table>
		<br />
		<table>
			<tr>
				<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../thesis/confirm_proposal.php';" /></td>				
			</tr>
		</table>
		<br/>
		
	</form>
	</body>
</html>