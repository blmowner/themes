<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: assign_examiner_change.php
//
// Created by: Zuraimi
// Created Date: 27-Dec-2014
// Modified by: Zuraimi
// Modified Date: 27-Dec-2014
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

session_start();
$userid=$_SESSION['user_id'];
$proposalId=$_REQUEST['pid'];
$thesisId=$_REQUEST['tid'];
$matrixNo=$_REQUEST['mn'];
$sname=$_REQUEST['sname'];

$curdatetime = date("Y-m-d H:i:s");

foreach($_REQUEST as $key => $value)
{
	if($key!="page")
		$varParamSend.="&$key=$value";
}

if(isset($_POST['btnEdit']) && ($_POST['btnEdit'] <> ""))
{
	$examinerBox1=$_POST['examinerBox1'];
	$myDropDownSType1=$_POST['myDropDownSType1'];
	$respondedByDate=$_POST['respondedByDate'];
	
	$msg=array();
	if (sizeof($_POST['examinerBox1'])>0) {
		$tmpExaminerBox1 = $examinerBox1;
		//validate chairman status for new examiner
		while (list ($key,$val) = @each ($tmpExaminerBox1))  {
			if ($myDropDownSType1[$val] == 'EC') {
				$sql_1 = "SELECT pg_employee_empid
				FROM pg_supervisor
				WHERE ref_supervisor_type_id in ('EC')
				AND pg_student_matrix_no = '$matrixNo'
				AND pg_thesis_id = '$thesisId'
				AND STATUS = 'A'";
					
				$result_sql_1 = $dbg->query($sql_1); 
				$dbg->next_record();
				$employeeId = $dbg->f('pg_employee_empid');
				$row_cnt_1 = mysql_num_rows($result_sql_1);
				
				if ($row_cnt_1>0) {
					$msg[] = "<div class=\"error\"><span>The staff $employeeId in the list below has been assigned as Chairman already. Please check the role for new selected staff $myEmployeeId[$val]. </span></div>";
				}
			}	
		}
		if(empty($msg)) 
		{
			while (list ($key,$val) = @each ($examinerBox1)) 
			{
				if ($myDropDownSType1[$val]=='') $role='XE';
				else $role=$myDropDownSType1[$val];
				
				$sql7 = "UPDATE pg_supervisor
				SET ref_supervisor_type_id='$role', 
				modify_by='$user_id', modify_date='$curdatetime'
				WHERE id='$examinerId1[$val]'
				AND ref_supervisor_type_id in ('EI','EE','EC','XE')
				AND pg_student_matrix_no = '$matrixNo'
				AND pg_thesis_id = '$thesisId'
				AND STATUS = 'A'";
				
				$dbg->query($sql7); 
				$dbg->next_record();
				
				$sql9 = "UPDATE pg_thesis
				SET examiner_status = 'A'
				WHERE id ='$thesisId'
				AND student_matrix_no = '$matrixNo'";
							
				$dbg->query($sql9); 
				
			}
			$msg[] = "<div class=\"success\"><span>The selected Examiner has been updated successfully.</span></div>";
		}
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please select the Examiner first before click UPDATE button.</span></div>";
	}
}

if(isset($_POST['btnDelete']) && ($_POST['btnDelete'] <> ""))
{
	$examinerBox1=$_POST['examinerBox1'];
	$employeeId1=$_POST['employeeId1'];
	$myDropDownSType1=$_POST['myDropDownSType1'];
	$msg=array();

	if (sizeof($_POST['examinerBox1'])>0) {
		while (list ($key,$val) = @each ($examinerBox1)) 
		{
			$sql12="SELECT id
			FROM pg_invitation_detail
			WHERE pg_supervisor_id = '$examinerId1[$val]'
			AND pg_employee_empid = '$employeeId1[$val]'";
			
			$result12 = $dba->query($sql12);
			$dba->next_record();
			
			$row_cnt12 = mysql_num_rows($result12);
			
			if ($row_cnt12>0) {
				$sql13="UPDATE pg_supervisor
				SET status = 'I'
				WHERE id = '$examinerId1[$val]'
				AND ref_supervisor_type_id in ('EI','EE', 'EC', 'XE')
				AND pg_student_matrix_no = '$matrixNo'
				AND pg_thesis_id = '$thesisId'
				AND STATUS = 'A'";
				$dba->query($sql13);
			}
			else {
				$sql8 = "DELETE FROM pg_supervisor
				WHERE id='$examinerId1[$val]'
				AND ref_supervisor_type_id in ('EI','EE', 'EC', 'XE')
				AND pg_student_matrix_no = '$matrixNo'
				AND pg_thesis_id = '$thesisId'
				AND STATUS = 'A'";
				
				$dbg->query($sql8); 
			}
		}
		
		$sql9 = "SELECT id
		FROM pg_supervisor
		WHERE pg_student_matrix_no = '$matrixNo'
		AND ref_supervisor_type_id in ('EI','EE', 'EC', 'XE')
		AND pg_thesis_id = '$thesisId'
		AND STATUS = 'A'";

		$result_sql9 = $dbg->query($sql9); 
		
		$row_cnt = mysql_num_rows($result_sql9);
		if ($row_cnt==0) {
			$sql10="UPDATE pg_thesis
			SET examiner_status = 'U'
			WHERE id = '$thesisId'
			AND student_matrix_no = '$matrixNo'";
			
			$dbe->query($sql10); 
		}
		$msg[] = "<div class=\"success\"><span>The selected Examiner has been deleted from the list successfully.</span></div>";
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please select the Examiner from the list before click DELETE button.</span></div>";
	}
}

if(isset($_POST['btnSearch']) && ($_POST['btnSearch'] <> ""))
{
	$searchDeptId = $_POST['searchDeptId'];	
	$searchExaminerName = $_POST['searchExaminerName'];
	$searchExpertise = $_POST['searchExpertise'];

	if ($searchDeptId!="") 
	{
		$tmpSearchDeptId = " AND a.unit_id = '$searchDeptId'";
	}
	else 
	{
		$tmpSearchDeptId="";
	}
	if ($searchExaminerName!="") 
	{
		$tmpSearchExaminerName = " AND (a.name like '%$searchExaminerName%' OR a.empid like '%$searchExaminerName%')";
	}
	else 
	{
		$tmpSearchExaminerName="";
	}
	if ($searchExpertise!="") 
	{
		$tmpSearchExpertise = " AND (f.expertise like '%$searchExpertise%' OR g.area like '%$searchExpertise%')";
	}
	else 
	{
		$tmpSearchExpertise="";
	}
	
	
	$sql2 = "SELECT DISTINCT a.empid, a.name, b.description, a.skype_id, a.unit_id, b.description AS department  
	FROM new_employee a 
	LEFT JOIN dept_unit b ON (b.id = a.unit_id) 
	LEFT JOIN education c ON (c.empid = a.empid)
	LEFT JOIN lookup_level_qualification d ON (d.id = c.level)
	LEFT JOIN lookup_teaching e ON (e.id = a.teachingcat)
	LEFT JOIN employee_expertise f ON (f.empid = a.empid)
	LEFT JOIN job_list_category g ON (g.jobarea = f.expertise)
	WHERE a.teachingcat IN ('1','2','3') 
	/*AND a.dept_id='ACAD'*/ "		
	.$tmpSearchDeptId." "
	.$tmpSearchExaminerName." "
	.$tmpSearchExpertise." "."
	AND c.level IN ('4','5')
	ORDER BY a.unit_id, a.name, a.empid";		
	
	$result_sql2 = $dbc->query($sql2); 
	$dbc->next_record();
	$row_cnt_sv = mysql_num_rows($result_sql2);

	$employeeIdArray = Array();
	$employeeNameArray = Array();
	$expertiseArray = Array();
	$skypeIdArray = Array();
	$departmentArray = Array();
	$unitIdArray = Array();
	
	$tmpEmployeeIdArray = Array();
	$tmpEmployeeNameArray = Array();
	$tmpExpertiseArray = Array();
	$tmpSkypeIdArray = Array();
	$tmpDepartmentArray = Array();
	$tmpUnitIdArray = Array();
	
	$no1=0;		
	$no2=0;
	if ($row_cnt_sv > 0){
		do {						
			$employeeIdArray[$no1]=$dbc->f('empid');	
			$employeeNameArray[$no1]=$dbc->f('name');
			$expertiseArray[$no1]=$dbc->f('description');
			$skypeIdArray[$no1]=$dbc->f('skype_id');
			$departmentArray[$no1]=$dbc->f('department');
			$unitIdArray[$no1]=$dbc->f('unit_id');
			$no1++;
			
		}while($dbc->next_record());
		
		for ($i=0; $i<$no1; $i++){
			$sql9 = "SELECT id
			FROM pg_supervisor
			WHERE pg_employee_empid = '$employeeIdArray[$i]'
			AND pg_student_matrix_no = '$matrixNo'
			AND pg_thesis_id = '$thesisId'
			AND ref_supervisor_type_id IN ('EE','EI','EC','XE')
			AND status = 'A'";
			
			$result9 = $db->query($sql9);
			$db->next_record();			
			
			$row_cnt_sql9 = mysql_num_rows($result9);//if the examiner is not yet assigned to this student 
			if ($row_cnt_sql9 == 0) {
				$tmpEmployeeIdArray[$no2] = $employeeIdArray[$i];
				$tmpEmployeeNameArray[$no2] = $employeeNameArray[$i];
				$tmpEexpertiseArray[$no2] = $expertiseArray[$i];
				$tmpSkypeIdArray[$no2] = $skypeIdArray[$i];
				$tmpDepartmentArray[$no2] = $departmentArray[$i];
				$tmpUnitIdArray[$no2] = $unitIdArray[$i];
				$no2++;
			}
		}
		$employeeIdArray = $tmpEmployeeIdArray;
		$employeeNameArray = $tmpEmployeeNameArray;
		$expertiseArray = $tmpEexpertiseArray;
		$skypeIdArray = $tmpSkypeIdArray;
		$departmentArray = $tmpDepartmentArray;
		$unitIdArray = $tmpUnitIdArray;
		$no1 = $no2;
		$row_cnt_sv = $no2;		
	}

}
else {
	$row_cnt_sv = 0;	
}

if(isset($_POST['btnAssign']) && !empty($_POST['btnAssign']) )
{
	$examinerBox=$_POST['examinerBox'];
	$myDropDownSType=$_POST['myDropDownSType'];
	$myRespondedByDate=$_POST['myRespondedByDate'];
	$employeeId=$_POST['employeeId'];
	
	$msg=array();
	if (sizeof($_POST['examinerBox'])>0) {
		$tmpExaminerBox = $examinerBox;
		//validate chairman status for new examiner
		while (list ($key,$val) = @each ($tmpExaminerBox))  {
			if ($myDropDownSType[$val] == 'EC') {
				$sql_1 = "SELECT pg_employee_empid
				FROM pg_supervisor
				WHERE ref_supervisor_type_id in ('EC')
				AND pg_student_matrix_no = '$matrixNo'
				AND pg_thesis_id = '$thesisId'
				AND STATUS = 'A'";
					
				$result_sql_1 = $dbg->query($sql_1); 
				$dbg->next_record();
				$row_cnt_1 = mysql_num_rows($result_sql_1);
				$employeeIdChairman = $dbg->f('pg_employee_empid');
				
				if ($row_cnt_1>0) {
					$msg[] = "<div class=\"error\"><span>The staff $employeeIdChairman in the list below has been assigned as Chairman already. Please check the role for new selected staff $employeeId[$val]. </span></div>";
				}
				
				$sql_2 = "SELECT id
				FROM pg_supervisor
				WHERE ref_supervisor_type_id in ('SV','CS','XS')
				AND pg_student_matrix_no = '$matrixNo'
				AND pg_thesis_id = '$thesisId'
				AND pg_employee_empid = '$employeeId[$val]'
				AND STATUS = 'A'";
					
				$result_sql_2 = $dbg->query($sql_2); 
				$dbg->next_record();
				$row_cnt_2 = mysql_num_rows($result_sql_2);
				
				if ($row_cnt_2>0) {
					$msg[] = "<div class=\"error\"><span>The selected staff $employeeId[$val] has been assigned as Supervisor/Co-Supervisor already. Please check the role for this staff. </span></div>";
				}
			}	
		}
		if(empty($msg)) 
		{
			while (list ($key,$val) = @each ($examinerBox)) 
			{			
				$run_pg_examiner_id = run_num('id','pg_supervisor'); // generate id (running number)
				
				if ($myDropDownSType[$val]=='') $role='XE';
				else $role=$myDropDownSType[$val];
				
					$lock_tables="LOCK TABLES pg_supervisor WRITE"; //lock the table
					$db->query($lock_tables);
					$sql4="INSERT INTO pg_supervisor
					(id, pg_employee_empid, pg_student_matrix_no, pg_thesis_id, assigned_by, assigned_date,
					respondedby_date, ref_supervisor_type_id, status, insert_by, insert_date, modify_by, modify_date)
					VALUES ('$run_pg_examiner_id', '$employeeId[$val]', '$matrixNo', '$thesisId', '$userid', '$curdatetime',  
					null,'$role', 'A', '$userid', '$curdatetime', '$userid', '$curdatetime') ";
					
					$dbb->query($sql4); 
					$unlock_tables="UNLOCK TABLES"; //unlock the table;
					$db->query($unlock_tables);

			}
			$sql5="UPDATE pg_thesis
				SET examiner_status = 'A'
				WHERE id = '$thesisId'
				AND student_matrix_no = '$matrixNo'";

				$dbe->query($sql5); 
				
			$msg[] = "<div class=\"success\"><span>The selected Examiner has been added to the list successfully.</span></div>";
		}
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please select the Examiner first before click ASSIGN button.</span></div>";
	}

}

$sql11 = "SELECT a.ref_supervisor_type_id
FROM pg_supervisor a
LEFT JOIN ref_supervisor_type e ON (e.id = a.ref_supervisor_type_id)
WHERE a.pg_student_matrix_no = '$matrixNo' 
AND a.ref_supervisor_type_id in ('EE','EI','EC','XE')
AND a.acceptance_status IS NULL
AND a.pg_thesis_id = '$thesisId'
AND a.status = 'A' 
ORDER BY a.pg_employee_empid, e.id";

$result_sql11 = $db->query($sql11); 
$db->next_record();

$row_cnt_sv_chk = mysql_num_rows($result_sql11);

$sql6 = "SELECT a.id, a.pg_employee_empid, 
a.ref_supervisor_type_id, e.description AS stype, DATE_FORMAT(a.respondedby_date,'%d-%b-%Y') as respondedby_date
FROM pg_supervisor a
LEFT JOIN ref_supervisor_type e ON (e.id = a.ref_supervisor_type_id)
WHERE a.pg_student_matrix_no = '$matrixNo' 
AND a.ref_supervisor_type_id in ('EI','EE', 'EC', 'XE')
/* AND a.acceptance_status IS NULL*/
AND pg_thesis_id = '$thesisId'
AND a.status = 'A' 
ORDER BY e.seq,  a.pg_employee_empid, e.id";

$sql3_3 = "SELECT id, description
FROM ref_supervisor_type		
WHERE status='A'
AND type in ('EX','XE')
ORDER BY seq";

$dbf->query($sql3); 
$dbf->next_record();

$sql3_2 = "SELECT id, description
FROM ref_supervisor_type		
WHERE status='A'
AND type in ('EX')
ORDER BY seq";

$dbf->query($sql3); 
$dbf->next_record();



?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Untitled Document</title>
		<link rel="stylesheet" type="text/css" href="../../../theme/css/<?=$css;?>" />
		<link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
		<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
		
		<script src="../../../lib/js/jquery.min2.js"></script>
		<script src="../../../lib/js/jquery.colorbox.js"></script>
		<script src="../../../lib/js/jquery.mask_input-1.3.js"></script>
		<script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
		<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>	
		<script language="JavaScript" src="../js/windowopen.js"></script>	
		<!--<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>-->
	</head>
	
	<body>
	<SCRIPT LANGUAGE="JavaScript">
	function respConfirm () {
		var confirmSubmit = confirm("Ensure you have selected the record to delete. \nClick OK if confirm to proceed or CANCEL to stay on the same page.");
		if (confirmSubmit==true)
		{
			return saveStatus;
		}
		if (confirmSubmit==false)
		{
			return false;
		}
	}
	</SCRIPT>
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
		<legend><strong>List of Assigned Examiner</strong></legend>
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
		<??>		
		<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="90%" class="thetable">
				<tr>
					<th width="5%"><strong>Tick</strong></th>
					<th width="5%"><strong>No.</strong></th>
					<th width="5%" align="left"><strong>Dept</strong></th>
					<th width="20%" align="left"><strong>Name</strong></th>  
					<th width="20%" align="left"><strong>Qualification</strong></th>
					<th width="20%" align="left"><strong>Field of Expertise</strong></th>
					<th width="15%" align="left"><strong>Role</strong></th>				
				</tr>    
				<?
				$result6 = $dbe->query($sql6); 
				$dbe->next_record();	
				$row_cnt = mysql_num_rows($result6);
				if ($row_cnt>0) {
				$no1=0;
				$myArrayNo=0;
				$jscript1="";
				do {						
					
					$examinerId1=$dbe->f('id');	
					$employeeId1=$dbe->f('pg_employee_empid');	
					$expertise1=$dbe->f('expertise');
					$refExaminerType1=$dbe->f('ref_supervisor_type_id');
					$stDescription1=$dbe->f('stype');
					$respondedByDate=$dbe->f('respondedby_date');
				?>
				<tr>
					<input type="hidden" name="examinerId1[]" value="<?=$examinerId1;?>"/>
					<input type="hidden" name="employeeId1[]" value="<?=$employeeId1;?>"/>					
					<td align="center"><input name="examinerBox1[]" type="checkbox" value="<?=$no1;?>"/></td>				
					<td align="center"><?=$no1+1;?>.</td>
					<?
					$sql4="SELECT a.id as dept_id, a.description as department
					FROM dept_unit a
					LEFT JOIN new_employee b ON (b.unit_id = a.id)
					WHERE b.empid = '$employeeId1'";
					
					$dbc->query($sql4);
					$row_personal=$dbc->fetchArray();
					$departmentId1=$row_personal['dept_id'];
					$department1=$row_personal['department'];
					?>
					
					<td><a width="20" height="19" style="border:0px;" title="<?=$department1;?>"><?=$departmentId1;?></a></td>
					
					
					<?$myEmployeeId1[$no1]=$employeeId1;?>
					
					<?
					$sql4="SELECT name AS employee_name
					FROM new_employee
					WHERE empid = '$employeeId1'";
					
					$dbc->query($sql4);
					$row_personal=$dbc->fetchArray();
					$employeeName1=$row_personal['employee_name'];
					?>
					<td><label name="myEmployeeName1[]" size="50" id="employeeName1"></label><?=$employeeName1;?><br/>(<?=$employeeId1;?>)
					<?$myEmployeeName1[$no1]=$employeeName1;?>

											
					<?if ($briefBiodata != null || $briefBiodata != '') {?>
                            <a href="../examiner/assign_examiner_biodata.php?tid=<?=$thesisId;?>&sid=<?=$employeeId1;?>&pid=<?=$proposalId;?>&ename=<?=$employeeName1;?>&mn=<?=$matrixNo;?>&sname=<?=$sname;?>&id=<?=$examinerId1;?>"><br/><img src="../images/edit.jpg" width="20" height="19" style="border:0px;" title="Examiner has been assigned" />Read Brief Biodata </a><br/>
                            <?}
								else {?>
                            <a href="../examiner/assign_examiner_biodata.php?tid=<?=$thesisId;?>&sid=<?=$employeeId1;?>&pid=<?=$proposalId;?>&ename=<?=$employeeName1;?>&mn=<?=$matrixNo;?>&sname=<?=$sname;?>&id=<?=$examinerId1;?>"><br/><img src="../images/red_edit.jpg" width="20" height="19" style="border:0px;" title="Examiner is not assigned yet" />View Brief Biodata </a>
                            <?}?>
							
					<td>
					<?
					$sql_expertise1 = "SELECT descrip
					FROM education
					WHERE empid='$employeeId1'
					AND LEVEL IN ('4','5')
					ORDER BY LEVEL";
					
					$sql_expertise1;
					$result_sql_expertise1 = $dbc->query($sql_expertise1); 
					$dbc->next_record();
					
					do {
						$educationDesc1=$dbc->f('descrip');
						if ($educationDesc1!="") {							
								?><label name="myExpertise1[]" size="50" id="expertise1" >- <?=$educationDesc1;?></label><br/><?
						}	
					} while ($dbc->next_record())							
					?></td>
					
					<td>
					<?$sqlAreaExpertise = "SELECT a.expertise, b.area
						FROM employee_expertise a
						LEFT JOIN job_list_category b ON (b.jobarea = a.expertise)
						WHERE empid = '$employeeId1'";
						
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
					
					<td><select name = "myDropDownSType1[]">
						<option value="" selected="selected"></option>						
					
					<?
					$dbf->query($sql3_3); 
					$dbf->next_record();
					do {
						$stId1=$dbf->f('id');	
						$stDescription1=$dbf->f('description');	
						if (strcmp($stId1,$refExaminerType1)!=true) {
							?>
								<option value="<?=$stId1?>" selected="selected"><?=$stDescription1?></option>
							<?
						}
						else {?>
								<option value="<?=$stId1?>"><?=$stDescription1?></option>
							<?}
					}while ($dbf->next_record());
					?>
					</select>
					<? if ($refExaminerType1=='XE') {?>
						<br/><label><span style="color:#FF0000">Please assign role to this staff<span></label>
					<?}?>
					</td>
					

				</tr>  
				<?
				$no1=$no1+1;
				$myArrayNo1=$myArrayNo1+1;
				}while($dbe->next_record());	
				?>			
			
			</table>			
			<table>
				<tr>
					<td><?if ($row_cnt_sv_chk==0) {							
							?><label><strong><span style="color:#FF0000">Note:</span></strong> Please assign Examiner to evaluate this student.</label><br/><?
					}?></td>
				</tr>
			</table>
			<br />
			<table>
				<tr>
					<td><input type="submit" name="btnEdit" value="Update" /></td>
					<td><input type="submit" name="btnDelete" value="Delete" onClick="return respConfirm()" /></td>
					<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../examiner/assign_examiner.php';" /></td>					
				</tr>
			</table>
			
			<?
		}
		else {
			?>
			<table>
				<tr>
					<td>No record(s) found.</td>
				</tr>
			</table>
			<?
		}?>
		</fieldset>
		<br/>
		<?
		
		$sql1 = "SELECT du.id, du.description
		FROM new_employee e 
		LEFT JOIN dept_unit du ON (du.id=e.unit_id)
		WHERE e.dept_id='ACAD' 
		AND du.id IS NOT NULL
		GROUP BY du.id
		ORDER BY du.description";
		
		$result = $dbc->query($sql1); 
		//echo $sql1;
		//var_dump($db);
		$dbc->next_record();

		?>
		<fieldset>
		<legend><strong>Assign New Examiner</strong></legend>
		<table>
			<tr>							
				<td>Please enter searching criteria below:-</td>
			</tr>
		</table>
		<br/>
		<table>
			<tr>
				<td>Department</td>			
				<td>
					<select name = "searchDeptId">
						<option value="" selected="selected"></option>
						
					<?					
						do {						
							$id=$dbc->f('id');							
							$description=$dbc->f('description');
							if ($id==$searchDeptId) {
							?>
								<option value="<?=$id?>" selected="selected"><?=$description;?></option>
							<?
							}
							else {?>
								<option value="<?=$id?>"><?=$description;?></option>
							<?}
						}while($dbc->next_record());	?>
					</select>
				</td>							
			</tr>
			<tr>
			<tr>
				<td>Staff Name / ID</td>			
				<td><input name="searchExaminerName" type="text" id="searchExaminerName" size="40" value="<?=$searchExaminerName;?>" /></td>				
			</tr>
			<tr>
				<td>Field of Expertise / ID</td>			
				<td><input name="searchExpertise" type="text" id="searchExpertise" size="40" value="<?=$searchExpertise;?>" /></td>		
				<td><input type="submit" name="btnSearch" id="btnSearch" align="center"  value="Search" /></td>
				<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../examiner/assign_examiner.php';" /></td>
				<td><span style="color:#FF0000">Note:</span> If no entry is provided, it will search all.</td>
			</tr>
		</table>
		<br />
		
		<table>
			<tr>							
				<td>Searching Results:- <?=$row_cnt_sv ?> record(s) found.</td>
			</tr>
		</table>
		<? if ($row_cnt_sv >5)
		{?>
			<div id = "tabledisplay" style="overflow:auto; height:400px;">
		<? }
		else 
		{ ?>
			<div id = "tabledisplay">
		<? } ?>

		<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="100%" class="thetable">
			<tr>
				<th width="5%"><strong>Tick</strong></th>
				<th width="5%"><strong>No.</strong></th>
				<th width="5%"><strong>Dept</strong></th>
				<th width="10%"><strong>Staff ID</strong></th>
				<th width="20%"><strong>Name</strong></th>  
				<th width="20%"><strong>Qualification</strong></th>
				<th width="20%"><strong>Field of Expertise</strong></th>
				<th width="15%"><strong>Role</strong></th>				
			</tr>    
			<?
			$sql10 = "SELECT pg_employee_empid 
			FROM pg_supervisor
			WHERE ref_supervisor_type_id in ('EI','EE', 'EC', 'XE')
			AND pg_student_matrix_no = '$matrixNo'
			AND pg_thesis_id = '$thesisId'
			AND status = 'A'";
			
			$result_sql10 = $dbg->query($sql10); 
			$dbg->next_record();
			
			//if (($row_cnt_sv>0) && ($row_cnt_sv!=mysql_num_rows($result_sql10))) {
			if ($row_cnt_sv>0) {
			$no=0;
			$tmp_no=1;
			$myArrayNo=0;
			$jscript = "";
			$no3=0;
			
			$no2=0;
			$examinerArray = Array();
			
			$no4=0;
			do {
				$examinerArray[$no4]= $dbg->f('pg_employee_empid');					
				$no4++;
			} while ($dbg->next_record());
			$display = "DISPLAY";
			for ($no = 0; $no < count($employeeIdArray); $no++) 
			{

				for ($no5 = 0; $no5 < count($examinerArray); $no5++) 
				{				
					if ($examinerArray[$no5]==$employeeIdArray[$no])
					{							
						$display="NO_DISPLAY";
						$tmp_no--;
						break;
					}
					else 
					{
						$display = "DISPLAY";
					}
				}
				if (strcmp($display,"DISPLAY")==0) 
				{
				

			?>
			<tr>
				<input type="hidden" name="employeeId[]" id="employeeId" value="<?=$employeeIdArray[$no]?>"/>
				<td align="center"><input name="examinerBox[]" type="checkbox" value="<?=$no;?>" /></td>
				
				<?
				
				$sqlProposalArea = "SELECT job_id1_area, job_id2_area, job_id3_area, job_id4_area, job_id5_area, job_id6_area
				FROM pg_proposal_area
				WHERE pg_proposal_id = '$proposalId'";
				
				$resultSqlProposalArea = $dbg->query($sqlProposalArea); 
				$dbg->next_record();
				
				$jobIdArea1=$dbg->f('job_id1_area');
				$jobIdArea2=$dbg->f('job_id2_area');
				$jobIdArea3=$dbg->f('job_id3_area');
				$jobIdArea4=$dbg->f('job_id4_area');
				$jobIdArea5=$dbg->f('job_id5_area');
				$jobIdArea6=$dbg->f('job_id6_area');
					
				$sqlAreaExpertise = "SELECT a.expertise, b.area
				FROM employee_expertise a
				LEFT JOIN job_list_category b ON (b.jobarea = a.expertise)
				WHERE empid = '$employeeIdArray[$no]'";
				
				$resultSqlAreaExpertise = $dbc->query($sqlAreaExpertise); 
				$dbc->next_record();
				$recommended=0;
				$tmpAreaExpertise="";
				do {
					$expertise=$dbc->f('expertise');
					$area=$dbc->f('area');
					
					if (($expertise==$jobIdArea1 || $expertise==$jobIdArea2 || $expertise==$jobIdArea3 || $expertise==$jobIdArea4 || $expertise==$jobIdArea5 || $expertise==$jobIdArea6))				
					{
						if($tmpAreaExpertise!="" || $expertise!=""){
							$tmpAreaExpertise = $area." ".$tmpAreaExpertise;
							$recommended=1;
						}else{
							$tmpAreaExpertise = $area." ".$tmpAreaExpertise;
							$recommended=0;
						}
					}else
					{
						if($tmpAreaExpertise!="" || $expertise!=""){
							$tmpAreaExpertise = $tmpAreaExpertise." ".$area;
							$recommended=1;
						}else{
							$tmpAreaExpertise = $tmpAreaExpertise." ".$area;
							$recommended=0;
						}
					}
					
				} while ($dbc->next_record());?>
								
				<td align="center"><?=$tmp_no;?>.
				<? if ($recommended==1)
				{
					?><img src="../images/recommended.jpg" width="20" height="19" style="border:0px;" title="Recommended" /></a><?
				}?>
				</td>
				
				<?
					$sql4="SELECT a.id as dept_id, a.description as department
					FROM dept_unit a
					LEFT JOIN new_employee b ON (b.unit_id = a.id)
					WHERE b.empid = '$employeeIdArray[$no]'";
					
					$dbc->query($sql4);
					$row_personal=$dbc->fetchArray();
					$departmentId=$row_personal['dept_id'];
					$department=$row_personal['department'];
				?>
				<td><a width="20" height="19" style="border:0px;" title="<?=$department;?>"><?=$departmentId;?></a></td>				
				<td><label name="employeeId[]" size="15" id="employeeId" ></label><?=$employeeIdArray[$no];?></td>				
				
				<?
				$sql4="SELECT name AS employee_name
				FROM new_employee
				WHERE empid = '$employeeIdArray[$no]'";
				
				$dbc->query($sql4);
				$row_personal=$dbc->fetchArray();
				$employeeName=$row_personal['employee_name'];
				?>
				<td><label name="myEmployeeName[]" size="50" id="employeeName" ></label><?=$employeeName;?></td>
				<td>
					<?
					$sql_expertise = "SELECT descrip
					FROM education
					WHERE empid='$employeeIdArray[$no]'
					AND LEVEL IN ('4','5')
					ORDER BY LEVEL";

					$result_sql_expertise = $dbc->query($sql_expertise); 
					$dbc->next_record();
					
					do {
						$educationDesc=$dbc->f('descrip');	
					?>
					
					<label name="myExpertise[]" size="50" id="expertise" >- <?=$educationDesc;?></label><br/>
					<?
					} while ($dbc->next_record())
					
			
					?></td>
				<td><label name="$areaExpertise"><?=$tmpAreaExpertise;?></label></td>				
				
				<td><select name = "myDropDownSType[]">
					<option value="" selected="selected"></option>
				<?
				$dbf->query($sql3_2); 
				$dbf->next_record();
				do {
					$stId=$dbf->f('id');	
					$stDescription=$dbf->f('description');	
					if (strcmp($stId,$stId2)!=true) {
						?>
							<option value="<?=$stId?>" selected="selected"><?=$stDescription?></option>
						<?
					}
					else {?>
							<option value="<?=$stId?>"><?=$stDescription?></option>
						<?}
				}while ($dbf->next_record());
				?>
				</select></td>
				
					
					
			</tr>  
			<?
			//$no=$no+1;
			
			$myArrayNo=$myArrayNo+1;
			}
			$tmp_no++;
			}			
			?>					
		</table>
		</div>
		<br />
		<br />
		<table>
			<tr>
				<td><input type="submit" name="btnAssign" value="Assign" /><span style="color:#FF0000"> Note:</span> Please select the Examiner from the list before click ASSIGN button.</td>
			</tr>
		</table>
		
		<?
		}
		else
		{
			?>
			<table>
				<tr>
					<td>No record(s) found.</td>
				</tr>
			</table>	
			<?
		}
		?>
		</fieldset>
		<table>
			<tr>
				<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../examiner/assign_examiner.php';" /></td>
			</tr>
		</table>		
	</form>
	<script>
		<?=$jscript;?>
		<?=$jscript1;?>
	</script>
	</body>
</html>