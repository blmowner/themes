<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: list_progress_report_faculty.php
//
// Created by: Zuraimi
// Created Date: 23-Jun-2015
// Modified by: Zuraimi
// Modified Date: 23-Jun-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

session_start();
$user_id=$_SESSION['user_id'];

if(isset($_POST['btnSearch']) && ($_POST['btnSearch'] <> "")) {
	$searchThesisId = $_POST['searchThesisId'];
	$searchStudent = $_POST['searchStudent'];
	$searchStudentName = $_POST['searchStudentName'];
	$msg = Array();
	
	if ($searchThesisId!="") 
	{
		$tmpSearchThesisId = " AND a.pg_thesis_id = '$searchThesisId'";
	}
	else 
	{
		$tmpSearchThesisId="";
	}
	
	if ($searchStudent!="") 
	{
		$tmpSearchStudent = " AND a.pg_student_matrix_no = '$searchStudent'";
	}
	else 
	{
		$tmpSearchStudent="";
	}

	if ($searchStudentName!="") 
	{
		$tmpSearchStudentName = " AND name LIKE '%$searchStudentName%'";
	}
	else 
	{
		$tmpSearchStudentName="";
	}
	if ($searchEmployeeName!="") 
	{
		$tmpSearchEmployeeName = " AND name LIKE '%$searchEmployeeName%'";
	}
	else 
	{
		$tmpSearchEmployeeName="";
	}
	
	
	$sql = " SELECT a.pg_employee_empid, a.pg_thesis_id, g.id as proposal_id, a.pg_student_matrix_no, a.ref_supervisor_type_id, 
	d.description as supervisor_type_desc, DATE_FORMAT(a.acceptance_date,'%d-%b-%Y %h:%i %p') as acceptance_date,
	h.description as role_status_desc
	FROM pg_supervisor a 
	LEFT JOIN ref_supervisor_type d ON (d.id = a.ref_supervisor_type_id) 
	LEFT JOIN pg_thesis f ON (f.student_matrix_no = a.pg_student_matrix_no)
	LEFT JOIN pg_proposal g ON (g.pg_thesis_id = f.id)
	LEFT JOIN ref_role_status h ON (h.id = a.role_status)
	WHERE a.acceptance_status ='ACC'"
	.$tmpSearchThesisId." "
	.$tmpSearchStudent." 
	AND a.ref_supervisor_type_id in ('SV','CS','XS')
	AND g.verified_status in ('APP','AWC')
	AND g.status in ('APP','APC')
	AND g.archived_status IS NULL
	AND a.status = 'A'
	ORDER BY d.seq, a.ref_supervisor_type_id";

	$result_sql = $dbg->query($sql); 
	$dbg->next_record();
	$row_cnt = mysql_num_rows($result_sql);

	$employeeIdArray = Array();
	$employeeNameArray = Array();
	$thesisIdArray = Array();
	$proposalIdArray = Array();
	$studentMatrixNoArray = Array();
	$studentNameArray = Array();
	$supervisorTypeArray = Array();
	$supervisorTypeArray = Array();
	$acceptanceDateArray = Array();
	$roleStatusDescArray = Array();
	
	$no=0;
	$no1=0;
	$no2=0;

	if ($row_cnt > 0) {
		do {
			$employeeIdArray[$no]=$dbg->f('pg_employee_empid');
			$studentMatrixNoArray[$no]=$dbg->f('pg_student_matrix_no');
			$thesisIdArray[$no]=$dbg->f('pg_thesis_id');
			$proposalIdArray[$no]=$dbg->f('proposal_id');
			$supervisorTypeIdArray[$no]=$dbg->f('ref_supervisor_type_id');
			$supervisorTypeDescArray[$no]=$dbg->f('supervisor_type_desc');
			$acceptanceDateArray[$no]=$dbg->f('acceptance_date');
			$roleStatusDescArray[$no]=$dbg->f('role_status_desc');
			$no++;
		}while ($dbg->next_record());
		
		for ($i=0; $i<$no; $i++){
			if (substr($studentMatrixNoArray[$i],0,2) != '07') { 
				$dbConn=$dbc; 
			} 
			else { 
				$dbConn=$dbc1; 
			}

			$sql1 = "SELECT name
			FROM student
			WHERE matrix_no = '$studentMatrixNoArray[$i]'"
			.$tmpSearchStudentName." ";			
			
			$result_sql1 = $dbConn->query($sql1); 
			$dbConn->next_record();
			$row_cnt1 = mysql_num_rows($result_sql1);
			
			if ($row_cnt1 > 0) {
				$studentNameArray[$no1]=$dbConn->f('name');
				$employeeIdArray[$no1]=$employeeIdArray[$i];
				$thesisIdArray[$no1]=$thesisIdArray[$i];
				$proposalIdArray[$no1]=$proposalIdArray[$i];
				$studentMatrixNoArray[$no1]=$studentMatrixNoArray[$i];
				$supervisorTypeIdArray[$no1]=$supervisorTypeIdArray[$i];
				$supervisorTypeDescArray[$no1]=$supervisorTypeDescArray[$i];
				$acceptanceDateArray[$no1]=$acceptanceDateArray[$i];
				$roleStatusDescArray[$no1]=$roleStatusDescArray[$i];
				$no1++;
			}
		}
		
		for ($i=0; $i<$no1; $i++){
			if (substr($employeeIdArray[$i],0,3) != 'S07') { 
				$dbConn=$dbc; 
			} 
			else { 
				$dbConn=$dbc1; 
			}

			$sql2 = "SELECT name
			FROM new_employee
			WHERE empid = '$employeeIdArray[$i]'"
			.$tmpSearchEmployeeName." ";			
			
			$result_sql2 = $dbConn->query($sql2); 
			$dbConn->next_record();
			$row_cnt2 = mysql_num_rows($result_sql2);
			
			if ($row_cnt2 > 0) {
				$employeeNameArray[$no2]=$dbConn->f('name');
				$studentNameArray[$no2]=$studentNameArray[$i];
				$employeeIdArray[$no2]=$employeeIdArray[$i];
				$thesisIdArray[$no2]=$thesisIdArray[$i];
				$proposalIdArray[$no2]=$proposalIdArray[$i];
				$studentMatrixNoArray[$no2]=$studentMatrixNoArray[$i];
				$supervisorTypeIdArray[$no2]=$supervisorTypeIdArray[$i];
				$supervisorTypeDescArray[$no2]=$supervisorTypeDescArray[$i];
				$acceptanceDateArray[$no2]=$acceptanceDateArray[$i];
				$roleStatusDescArray[$no2]=$roleStatusDescArray[$i];
				$no2++;
			}
		}
		
		if ($no2 == 0) {			
			$msg[] = "<div class=\"error\"><span>No record(s) found.</span></div>";			
		}
		$row_cnt = $no2;
	}
	else {
		$msg[] = "<div class=\"error\"><span>No record(s) found.</span></div>";
	}
}
else {

	$sql = " SELECT a.pg_employee_empid, a.pg_thesis_id, g.id as proposal_id, a.pg_student_matrix_no, a.ref_supervisor_type_id, 
	d.description as supervisor_type_desc, DATE_FORMAT(a.acceptance_date,'%d-%b-%Y  %h:%i %p') as acceptance_date, 
	h.description as role_status_desc
	FROM pg_supervisor a 
	LEFT JOIN ref_supervisor_type d ON (d.id = a.ref_supervisor_type_id) 
	LEFT JOIN pg_thesis f ON (f.student_matrix_no = a.pg_student_matrix_no)
	LEFT JOIN pg_proposal g ON (g.pg_thesis_id = f.id)
	LEFT JOIN ref_role_status h ON (h.id = a.role_status)
	WHERE a.acceptance_status = 'ACC'
	AND a.ref_supervisor_type_id in ('SV','CS','XS')
	AND g.verified_status in ('APP','AWC')
	AND g.status in ('APP','APC')
	AND g.archived_status IS NULL
	AND a.status = 'A'
	ORDER BY d.seq, a.ref_supervisor_type_id";

	$result_sql = $dbg->query($sql); 
	$dbg->next_record();
	$row_cnt = mysql_num_rows($result_sql);

	$employeeIdArray = Array();
	$employeeNameArray = Array();
	$thesisIdArray = Array();
	$proposalIdArray = Array();
	$studentMatrixNoArray = Array();
	$studentNameArray = Array();
	$supervisorTypeArray = Array();
	$supervisorTypeArray = Array();
	$acceptanceDateArray = Array();
	$roleStatusDescArray = Array();
	
	$no=0;
	$no1=0;
	$no2=0;
	
	if ($row_cnt > 0) {
		do {
			$employeeIdArray[$no]=$dbg->f('pg_employee_empid');
			$studentMatrixNoArray[$no]=$dbg->f('pg_student_matrix_no');
			$thesisIdArray[$no]=$dbg->f('pg_thesis_id');
			$proposalIdArray[$no]=$dbg->f('proposal_id');
			$supervisorTypeIdArray[$no]=$dbg->f('ref_supervisor_type_id');
			$supervisorTypeDescArray[$no]=$dbg->f('supervisor_type_desc');
			$acceptanceDateArray[$no]=$dbg->f('acceptance_date');
			$roleStatusDescArray[$no]=$dbg->f('role_status_desc');
			$no++;
		}while ($dbg->next_record());
		
		for ($i=0; $i<$row_cnt; $i++){
			if (substr($studentMatrixNoArray[$i],0,2) != '07') { 
				$dbConn=$dbc; 
			} 
			else { 
				$dbConn=$dbc1; 
			}

			$sql1 = "SELECT name
			FROM student
			WHERE matrix_no = '$studentMatrixNoArray[$i]'";
			
			$result_sql1 = $dbConn->query($sql1); 
			$dbConn->next_record();
			
			$studentNameArray[$no1]=$dbConn->f('name');
			$employeeIdArray[$no1]=$employeeIdArray[$i];
			$thesisIdArray[$no1]=$thesisIdArray[$i];
			$proposalIdArray[$no1]=$proposalIdArray[$i];
			$studentMatrixNoArray[$no1]=$studentMatrixNoArray[$i];
			$supervisorTypeIdArray[$no1]=$supervisorTypeIdArray[$i];
			$supervisorTypeDescArray[$no1]=$supervisorTypeDescArray[$i];
			$acceptanceDateArray[$no1]=$acceptanceDateArray[$i];
			$roleStatusDescArray[$no1]=$roleStatusDescArray[$i];
			$no1++;
		}
		
		for ($i=0; $i<$no1; $i++){
			if (substr($employeeIdArray[$i],0,3) != 'S07') { 
				$dbConn=$dbc; 
			} 
			else { 
				$dbConn=$dbc1; 
			}

			$sql2 = "SELECT name
			FROM new_employee
			WHERE empid = '$employeeIdArray[$i]'"
			.$tmpSearchEmployeeName." ";			
			
			$result_sql2 = $dbConn->query($sql2); 
			$dbConn->next_record();
			$row_cnt2 = mysql_num_rows($result_sql2);
			
			if ($row_cnt2 > 0) {
				$employeeNameArray[$no2]=$dbConn->f('name');
				$studentNameArray[$no2]=$studentNameArray[$i];
				$employeeIdArray[$no2]=$employeeIdArray[$i];
				$thesisIdArray[$no2]=$thesisIdArray[$i];
				$proposalIdArray[$no2]=$proposalIdArray[$i];
				$studentMatrixNoArray[$no2]=$studentMatrixNoArray[$i];
				$supervisorTypeIdArray[$no2]=$supervisorTypeIdArray[$i];
				$supervisorTypeDescArray[$no2]=$supervisorTypeDescArray[$i];
				$acceptanceDateArray[$no2]=$acceptanceDateArray[$i];
				$roleStatusDescArray[$no2]=$roleStatusDescArray[$i];
				$no2++;
			}
		}
	}
}


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
		<script language="JavaScript" src="../../../lib/js/windowopen.js"></script>	
		<script language="JavaScript" type="text/javascript" src="../../../lib/js/tooltip.js"></script>
		<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>		
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
	<form id="form1" name="form1" method="post" enctype="multipart/form-data">	

	<fieldset>
	<legend><strong>List of Student</strong></legend>
		<table>
			<tr>							
				<td>Please enter searching criteria below:-</td>
			</tr>
		</table>
		<table>
			<tr>
				<td>Thesis / Project ID</td>
				<td>:</td>
				<td><input type="text" name="searchThesisId" size="15" id="searchThesisId" value="<?=$searchThesisId;?>"/></td>
			</tr>
			<tr>
				<td>Matrix No</td>
				<td>:</td>
				<td><input type="text" name="searchStudent" size="15" id="searchStudent" value="<?=$searchStudent;?>"/></td>

			</tr>
			<tr>
				<td>Student Name</td>
				<td>:</td>
				<td><input type="text" name="searchStudentName" size="30" id="searchStudentName" value="<?=$searchStudentName;?>"/></td>				
			</tr>
			<tr>
				<td>Supervisor / Co-Supervisor Name</td>
				<td>:</td>
				<td><input type="text" name="searchEmployeeName" size="30" id="searchEmployeeName" value="<?=$searchEmployeeName;?>"/></td>
				<td><input type="submit" name="btnSearch" value="Search" /><span style="color:#FF0000"> Note:</span> If no entry is provided, it will search all.</td>
			</tr>
		</table>
		</br>
		
		<table>
			<tr>							
				<td>Searching Results:- <?=$row_cnt ?> record(s) found.</td>
			</tr>
		</table>
		<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="90%" class="thetable">			
			<tr>
				<th width="5%">No</th>					
				<th width="10%">Thesis / Project ID</th>
				<th width="10%">Student Matrix No.</th>
				<th width="20%" align="left">Student Name</th>
				<th width="20%" align="left">Supervisor / Co-Supervisor</th>
				<th width="10%" align="left">Staff Role</th>
				<th width="10%">Acceptance Date</th>
				<th width="5%">Action</th>

			</tr>
			<?if ($row_cnt > 0 ) {?>	
				<?
				$no=0;
				for ($i=0; $i<$row_cnt; $i++){?>
					<tr>
						<td align="center" width="4%"><?=$no+1;?>.</td>
						<td width="10%" align="center"><label><?=$thesisIdArray[$i]?></label></td>
						<td width="10%" align="center"><label><?=$studentMatrixNoArray[$i]?></label></td>
						<td width="20%"><label><?=$studentNameArray[$i]?></label></td>
						<td width="20%"><label><?=$employeeNameArray[$i]?></label></td>
						<td width="15%"><?
						if ($supervisorTypeIdArray[$i] == 'XS') {
						?>
							<label><span style="color:#FF0000"><?=$supervisorTypeDescArray[$i]?></span></label>
						<?}
						else {
							?>
							<label><?=$supervisorTypeDescArray[$i]?></label>
							<?
						}?>
						<br/><?=$roleStatusDescArray[$i]?></td>
						<td width="15%" align="center"><label><?=$acceptanceDateArray[$i]?></label></td>						
						<td width="6%" align="center"><input type="button" name="btnReview" value="View" onClick="javascript:document.location.href='list_progress_report_facview.php?tid=<?=$thesisIdArray[$i]?>&pid=<?=$proposalIdArray[$i]?>&mn=<?=$studentMatrixNoArray[$i];?>&eid=<?=$employeeIdArray[$i]?>';" /></td>
					</tr>
					<?
				$no++;	
				}
			}
			else {
			?>
				<table>
					<tr>
						<td>No record found!</td>
					</tr>
				</table>	
				<?
			}?>	
		</table>
		</fieldset>			
</body>
</html>





