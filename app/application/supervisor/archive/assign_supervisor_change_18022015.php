<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: assign_supervisor_change.php
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

$needSupervisor='Y';

//used for pagination-------------------
	$page = ($_GET['page'] == 0 ? 1 : $_GET['page']);
	$perpage = 5;
	$startpoint = ($page * $perpage) - $perpage;

$varParamSend="";
$curdatetime = date("Y-m-d H:i:s");

foreach($_REQUEST as $key => $value)
{
	if($key!="page")
		$varParamSend.="&$key=$value";
}
//---------------------------------------

//ZZZZZZZZZZZZZZZ

if(isset($_POST['btnEdit']) && ($_POST['btnEdit'] <> ""))
{
	//echo "btnEdit set ++++++++++++++++++++"."<br/>";
	$supervisorBox1=$_POST['supervisorBox1'];
	$myDropDownSType1=$_POST['myDropDownSType1'];
	$respondedByDate=$_POST['respondedByDate'];
	
	while (list ($key,$val) = @each ($supervisorBox1)) 
		{
			/*echo "supervisorBox1 key ".$key." "."val ".$val."<br/>";
			echo "supervisorId1 ".$val." ".$supervisorId1[$val]."<br/>";	
			echo "myEmployeeId1 ".$val." ".$myEmployeeId1[$val]."<br/>";	
			echo "myEmployeeName1 ".$val." ".$myEmployeeName1[$val]."<br/><br/>>";
			echo "myExpertise1 ".$val." ".$myExpertise1[$val]."<br/><br/>";
			echo "mySkypeId1 ".$val." ".$mySkypeId1[$val]."<br/><br/>";
			echo "myDropDownSType1 ".$val." ".$myDropDownSType1[$val]."<br/><br/>";*/
			
			if ($myDropDownSType1[$val]=='') $role='XS';
			else $role=$myDropDownSType1[$val];
			
			$sql7 = "UPDATE pg_supervisor
			SET ref_supervisor_type_id='$role', 
			respondedby_date = STR_TO_DATE('$respondedByDate[$val]','%d-%b-%Y'),
			modify_by='$user_id', modify_date='$curdatetime'
			WHERE id='$supervisorId1[$val]'
			AND ref_supervisor_type_id in ('SV','CS','EI','EE','XS')
			AND pg_student_matrix_no = '$matrixNo'
			AND pg_thesis_id = '$thesisId'
			AND STATUS = 'A'";
			
			$dbg->query($sql7); 
			$dbg->next_record();
			//echo $sql7;
			
			$sql9 = "UPDATE pg_thesis
			SET supervisor_status = 'A'
			WHERE id ='$thesisId'
			AND student_matrix_no = '$matrixNo'";
						
			$dbg->query($sql9); 
			//$dbg->next_record();
			//echo $sql9;
		
		}

}

if(isset($_POST['btnDelete']) && ($_POST['btnDelete'] <> ""))
{
	//echo "btnDelete set ++++++++++++++++++++"."<br/>";
	$supervisorBox1=$_POST['supervisorBox1'];
	$myDropDownSType1=$_POST['myDropDownSType1'];
	
	while (list ($key,$val) = @each ($supervisorBox1)) 
		{
			/*echo "supervisorBox1 key ".$key." "."val ".$val."<br/>";
			echo "supervisorId1 ".$val." ".$supervisorId1[$val]."<br/>";	
			echo "myEmployeeId1 ".$val." ".$myEmployeeId1[$val]."<br/>";	
			echo "myEmployeeName1 ".$val." ".$myEmployeeName1[$val]."<br/><br/>>";
			echo "myExpertise1 ".$val." ".$myExpertise1[$val]."<br/><br/>";
			echo "mySkypeId1 ".$val." ".$mySkypeId1[$val]."<br/><br/>";
			echo "myDropDownSType1 ".$val." ".$myDropDownSType1[$val]."<br/><br/>";*/
			
			$sql8 = "DELETE FROM pg_supervisor
			WHERE id='$supervisorId1[$val]'
			AND ref_supervisor_type_id in ('SV','CS','EI','EE','XS')
			AND pg_student_matrix_no = '$matrixNo'
			AND pg_thesis_id = '$thesisId'
			AND STATUS = 'A'";
			
			$dbg->query($sql8); 
			//$dbg->next_record();
			//echo $sql8;		
		}
		
		$sql9 = "SELECT id
		FROM pg_supervisor
		WHERE pg_student_matrix_no = '$matrixNo'
		AND ref_supervisor_type_id in ('SV','CS','EI','EE','XS')
		AND pg_thesis_id = '$thesisId'
		AND STATUS = 'A'";

		$result_sql9 = $dbg->query($sql9); 
		//$dbg->next_record();
		//echo $sql8;
		
		$row_cnt = mysql_num_rows($result_sql9);
		if ($row_cnt==0) {
			$sql10="UPDATE pg_thesis
			SET supervisor_status = 'U'
			WHERE id = '$thesisId'
			AND student_matrix_no = '$matrixNo'";
			
			//echo $sql5; exit();
			$dbe->query($sql10); 
		}

}

if(isset($_POST['btnSearch']) && ($_POST['btnSearch'] <> ""))
{
	$searchDeptId = $_POST['searchDeptId'];	
	$searchSupervisorName = $_POST['searchSupervisorName'];
	$searchExpertise = $_POST['searchExpertise'];
	
	if ($searchDeptId!="") 
	{
		$tmpSearchDeptId = " AND a.unit_id = '$searchDeptId'";
	}
	else 
	{
		$tmpSearchDeptId="";
	}
	if ($searchSupervisorName!="") 
	{
		$tmpSearchSupervisorName = " AND (a.name like '%$searchSupervisorName%' OR a.empid like '%$searchSupervisorName%')";
	}
	else 
	{
		$tmpSearchSupervisorName="";
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
		WHERE a.teachingcat IN ('1','3') 
		AND a.dept_id='ACAD'
		AND  a.empid NOT IN (SELECT pg_employee_empid FROM pg_supervisor
		WHERE ref_supervisor_type_id in ('SV','CS','EI','EE','XS')
		AND pg_student_matrix_no = '$matrixNo'
		AND pg_thesis_id = '$thesisId'
		AND status = 'A')"		
		.$tmpSearchDeptId." "
		.$tmpSearchSupervisorName." "
		.$tmpSearchExpertise." "."
		AND c.level IN ('4','5')
		ORDER BY a.unit_id, a.name, a.empid";		
		
		$result2 = $dba->query($sql2); 
		$row_cnt_sv = mysql_num_rows($result2);
		$dba->next_record();

}
else {
	/*$sql2 = "SELECT DISTINCT a.empid, a.name, b.description, a.skype_id, a.unit_id, b.description AS department  
		FROM new_employee a 
		LEFT JOIN dept_unit b ON (b.id = a.unit_id) 
		LEFT JOIN education c ON (c.empid = a.empid)
		LEFT JOIN lookup_level_qualification d ON (d.id = c.level)
		LEFT JOIN lookup_teaching e ON (e.id = a.teachingcat)
		LEFT JOIN employee_expertise f ON (f.empid = a.empid)
		LEFT JOIN job_list_category g ON (g.jobarea = f.expertise)
		WHERE a.teachingcat IN ('1','3')
		AND a.dept_id='ACAD'
		AND  a.empid NOT IN (SELECT pg_employee_empid FROM pg_supervisor
		WHERE ref_supervisor_type_id in ('SV','CS','EI','EE','XS')
		AND pg_student_matrix_no = '$matrixNo'
		AND pg_thesis_id = '$thesisId'
		AND status = 'A')
		AND c.level NOT IN ('4','5') 
		ORDER BY a.unit_id, a.name, a.empid";		
		
		$result2 = $dba->query($sql2); 
		$dba->next_record();*/
		
		/*$sql11 = "SELECT a.ref_supervisor_type_id
			FROM pg_supervisor a
			LEFT JOIN ref_supervisor_type e ON (e.id = a.ref_supervisor_type_id)
			LEFT JOIN student c ON (c.matrix_no = a.pg_student_matrix_no)
			WHERE a.pg_student_matrix_no = '$matrixNo' 
			AND a.ref_supervisor_type_id in ('SV')
			AND a.acceptance_status IS NULL
			AND a.pg_thesis_id = '$thesisId'
			AND a.status = 'A' 
			ORDER BY a.pg_employee_empid, e.id";
			
			$result_sql11 = $db->query($sql11); 
			$db->next_record();

			$row_cnt_sv = mysql_num_rows($result_sql11);*/
			$row_cnt_sv = 0;
}

 $sql6 = "SELECT a.id, a.pg_employee_empid, d.id AS dept_id, d.description AS department, b.name, 
		a.ref_supervisor_type_id, e.description AS stype, b.skype_id, DATE_FORMAT(a.respondedby_date,'%d-%b-%Y') as respondedby_date,
		a.brief_biodata
		FROM pg_supervisor a
		LEFT JOIN ref_supervisor_type e ON (e.id = a.ref_supervisor_type_id)
		LEFT JOIN new_employee b ON (b.empid = a.pg_employee_empid)
		LEFT JOIN student c ON (c.matrix_no = a.pg_student_matrix_no)
		LEFT JOIN dept_unit d ON (d.id = b.unit_id)
		WHERE a.pg_student_matrix_no = '$matrixNo' 
		AND a.ref_supervisor_type_id in ('SV','CS','EI','EE','XS')
		AND a.acceptance_status IS NULL
		AND pg_thesis_id = '$thesisId'
		AND a.status = 'A' 
		ORDER BY e.seq, d.id, a.pg_employee_empid, b.name, e.id";
	

$sql3 = "SELECT id, description
		FROM ref_supervisor_type
		WHERE status='A'
		ORDER BY seq";
 
$dbf->query($sql3); 
$dbf->next_record();
//echo $sql3;	



if(isset($_POST['btnAssign']) && !empty($_POST['btnAssign']) )
{
	//echo "btnAssign set ++++++++++++++++++++"."<br/>";
	$supervisorBox=$_POST['supervisorBox'];
	$myDropDownSType=$_POST['myDropDownSType'];
	$myRespondedByDate=$_SESSION['myRespondedByDate'];
	
	/*echo "<pre>";
	print_r($_POST);
	echo "</pre>";*/
	
	//$arrLength = count($supervisorBox);
	//echo "arrLength ".$arrLength." ";
		
	//if ($arrLenghth>0){
	
		while (list ($key,$val) = @each ($supervisorBox)) 
		{			
			$run_pg_supervisor_id = run_num('id','pg_supervisor'); // generate id (running number)
			
			/*echo "supervisorBox key".$key." ";
			echo "val".$val."<br/>";
			echo "myEmployeeId ".$val." ".$myEmployeeId[$val]."<br/>";	
			echo "myEmployeeName ".$val." ".$myEmployeeName[$val]."<br/><br/>>";
			echo "myExpertise ".$val." ".$myExpertise[$val]."<br/><br/>";
			echo "mySkypeId ".$val." ".$mySkypeId[$val]."<br/><br/>";
			echo "myDropDownSType ".$val." ".$myDropDownSType[$val]."<br/><br/>";*/
			
			if ($myDropDownSType[$val]=='') $role='XS';
			else $role=$myDropDownSType[$val];
			
			$sql4="INSERT INTO pg_supervisor
				(id, pg_employee_empid, pg_student_matrix_no, pg_thesis_id, assigned_by, assigned_date,
				respondedby_date, ref_supervisor_type_id, status, insert_by, insert_date, modify_by, modify_date)
				VALUES ('$run_pg_supervisor_id', '$myEmployeeId[$val]', '$matrixNo', '$thesisId', '$userid', '$curdatetime',  
				STR_TO_DATE('".$_POST['myRespondedByDate'][$val]."','%d-%b-%Y'),'$role', 'A', '$userid', '$curdatetime', '$userid', '$curdatetime') ";
				
				$dbb->query($sql4); 
		}
		$sql5="UPDATE pg_thesis
			SET supervisor_status = 'A'
			WHERE id = '$thesisId'
			AND student_matrix_no = '$matrixNo'";

			$dbe->query($sql5); 

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
		<script language="JavaScript" src="../js/windowopen.js"></script>	
	</head>
	
	<body>
	<form id="form1" name="form1" method="post" enctype="multipart/form-data">		
		<fieldset>
		<legend><strong>List of Assigned Supervisor</strong></legend>
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
		<?$result6 = $dbe->query($sql6); 
		$dbe->next_record();
		//echo $sql6;
		$row_cnt = mysql_num_rows($result6);
		if ($row_cnt>0) {?>		
		<table border="1" style="border-collapse:collapse;" cellpadding="4" cellspacing="3">
				<tr>
					<td><strong>Tick</strong></td>
					<td><strong>No.</strong></td>
					<td><strong>Dept</strong></td>
					<td><strong>Name</strong></td>  
					<td><strong>Qualification</strong></td>
					<td><strong>Field of Expertise</strong></td>
					<td><strong>Role</strong></td>
					<td><strong>Skype ID</strong></td>
					<td><strong>Reply Date</strong></td>					
				</tr>    
				<?
				//$dbe->query($sql6); 
				//$dbe->next_record();
				//echo $sql6;
				$no1=0;
				$myArrayNo=0;
				$jscript1="";
				do {						
					
					$supervisorId1=$dbe->f('id');	
					$employeeId1=$dbe->f('pg_employee_empid');	
					$departmentId1=$dbe->f('dept_id');
					$department1=$dbe->f('department');
					$employeeName1=$dbe->f('name');
					$expertise1=$dbe->f('expertise');
					$refSupervisorType1=$dbe->f('ref_supervisor_type_id');
					$stDescription1=$dbe->f('stype');
					$skypeId1=$dbe->f('skype_id');
					$respondedByDate=$dbe->f('respondedby_date');
					$briefBiodata=$dbe->f('brief_biodata');
				?>
				<tr>
					<input type="hidden" name="supervisorId1[]" value="<?=$supervisorId1;?>"/>				
					<td><input name="supervisorBox1[]" type="checkbox" value="<?=$no1;?>"/></td>				
					<td><?=$no1+1;?>.</td>
					
					<td><a width="20" height="19" style="border:0px;" title="<?=$department1;?>"><?=$departmentId1;?></a></td>
					
					
					<?$myEmployeeId1[$no1]=$employeeId1;?>
					<?//echo "$no1"."-"."myEmployeeId1"."-".$myEmployeeId1[$no1];?>
					
					<td><label name="myEmployeeName1[]" size="50" id="employeeName1"></label><?=$employeeName1;?><br/>(<?=$employeeId1;?>)
					<?$myEmployeeName1[$no1]=$employeeName1;?>
					<?//echo "$no1"."-"."myEmployeeName1"."-".$myEmployeeName1[$no1];?>
											
					<?if ($briefBiodata != null || $briefBiodata != '') {?>
                            <a href="../supervisor/assign_supervisor_biodata.php?tid=<?=$thesisId;?>&sid=<?=$employeeId1;?>&pid=<?=$proposalId;?>&ename=<?=$employeeName1;?>&mn=<?=$matrixNo;?>&sname=<?=$sname;?>&id=<?=$supervisorId1;?>"><br/><img src="../images/edit.jpg" width="20" height="19" style="border:0px;" title="Supervisor has been assigned" />Read Brief Biodata </a><br/>
                            <?}
								else {?>
                            <a href="../supervisor/assign_supervisor_biodata.php?tid=<?=$thesisId;?>&sid=<?=$employeeId1;?>&pid=<?=$proposalId;?>&ename=<?=$employeeName1;?>&mn=<?=$matrixNo;?>&sname=<?=$sname;?>&id=<?=$supervisorId1;?>"><br/><img src="../images/red_edit.jpg" width="20" height="19" style="border:0px;" title="Supervisor is not assigned yet" />Add Brief Biodata </a>
                            <?}?>
							
					<td>
					<?
					$sql_expertise1 = "SELECT descrip
					FROM education
					WHERE empid='$employeeId1'
					AND LEVEL IN ('4','5')
					ORDER BY LEVEL";
					
					$sql_expertise1;
					$result_sql_expertise1 = $db->query($sql_expertise1); 
					$db->next_record();
					
					do {
						$educationDesc1=$db->f('descrip');
						if ($educationDesc1!="") {							
								?><label name="myExpertise1[]" size="50" id="expertise1" >- <?=$educationDesc1;?></label><br/><?
						}	
					} while ($db->next_record())							
					?></td>
					<?$myExpertise1[$no1]=$expertise1;?>
					<?//echo "$no1"."-"."myExpertise1"."-".$myExpertise1[$no1];?>

					<?$myExpertise1[$no1]=$expertise1;?>
					<?//echo "$no1"."-"."myExpertise1"."-".$myExpertise1[$no1];?>
					
					<td>
					<?$sqlAreaExpertise = "SELECT a.expertise, b.area
						FROM employee_expertise a
						LEFT JOIN job_list_category b ON (b.jobarea = a.expertise)
						WHERE empid = '$employeeId1'";
						
						$resultSqlAreaExpertise = $db->query($sqlAreaExpertise); 
						$db->next_record();
						
						do {
							$area=$db->f('area');
							if ($area!="") {							
								?><label>- <?=$area;?></label><br/><?
							}
						} while ($db->next_record());
						?>					
					</td>
					
					<td><select name = "myDropDownSType1[]">
						<option value="" selected="selected"></option>						
					
					<?
					$dbf->query($sql3); 
					$dbf->next_record();
					do {
						$stId1=$dbf->f('id');	
						$stDescription1=$dbf->f('description');	
						if (strcmp($stId1,$refSupervisorType1)!=true) {
							?>
								<option value="<?=$stId1?>" selected="selected"><?=$stDescription1?></option>
								<?$myDropDownSType1[$no1]=$stId;?>							
								<?echo "myDropDownSType1".$myDropDownSType1[$no1];?>
							<?
						}
						else {?>
								<option value="<?=$stId1?>"><?=$stDescription1?></option>
								<?$myDropDownSType1[$no1]=$stId1;?>
								<?echo "myDropDownSType1".$myDropDownSType1[$no1];?>
							<?}
					}while ($dbf->next_record());
					?>
					</select>
					<? if ($refSupervisorType1=='XS') {?>
						<br/><label><span style="color:#FF0000">Please assign role to this staff<span></label>
					<?}?>
					</td>
					<td>
					<label name="mySkypeId1[]" id="skypeId1" size="20" ></label><?=$skypeId1?></td>
					<?$mySkypeId1[$no1]=$skypeId1;?>
					
					<td><input type="text" name="respondedByDate[]" size="15" id="respondedByDate<?=$no1;?>" value="<?php echo $respondedByDate;?>"/></td>
					<?	$jscript1 .= "\n" . '$( "#respondedByDate' . $no1 . '" ).datepicker({
													changeMonth: true,
													changeYear: true,
													yearRange: \'-100:+0\',
													dateFormat: \'dd-M-yy\'
												});';
						 
					?>
					<? $respondedByDate[$no1]=$respondedByDate;	?>
				</tr>  
				<?
				$no1=$no1+1;
				$myArrayNo1=$myArrayNo1+1;
				}while($dbe->next_record());	
				?>			
				<?$_SESSION['myEmployeeName1'] = $myEmployeeName1;?>
				<?$_SESSION['myEmployeeId1'] = $myEmployeeId1;?>
				<?$_SESSION['myDropDownSType1'] = $myDropDownSType1;?>
				<?$_POST['respondedByDate'] = $respondedByDate;?>
				
			</table>			
			<table>
				<tr>
					<td><?if ($row_cnt_sv==0) {							
							?><label><strong><span style="color:#FF0000">Note:</span></strong> Please assign Supervisor to supervise this student.</label><br/><?
					}?></td>
				</tr>
			</table>
			<br />
			<table>
				<tr>
					<td><input type="submit" name="btnEdit" value="Update" /></td>
					<td><input type="submit" name="btnDelete" value="Delete" /></td>
					<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../supervisor/assign_supervisor.php';" /></td>					
				</tr>
			</table>
			</fieldset>
			<?
		}
		else {
			?>
			<fieldset>
			<legend><strong><span style="color:#FF0000">Notification Message</span></strong></legend>
				<table>
					<tr>
						<td>There is no Supervisor/Co-Supervisor has been assigned to supervise this student.</td>
					</tr>
				</table>
			</fieldset>
			<?
		}?>
		<br/>
		<?
		
		$sql1 = "SELECT du.id, du.description
		FROM new_employee e 
		LEFT JOIN dept_unit du ON (du.id=e.unit_id)
		WHERE e.dept_id='ACAD' 
		AND du.id IS NOT NULL
		GROUP BY du.id
		ORDER BY du.description";
		
		$result = $db->query($sql1); 
		//echo $sql1;
		//var_dump($db);
		$db->next_record();

		?>
		<fieldset>
		<legend><strong>Assign New Supervisor</strong></legend>
		<table>
			<tr>							
				<td><strong>Please enter searching criteria below</strong></td>
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
							$id=$db->f('id');							
							$description=$db->f('description');
							if ($id==$searchDeptId) {
							?>
								<option value="<?=$id?>" selected="selected"><?=$description;?></option>
							<?
							}
							else {?>
								<option value="<?=$id?>"><?=$description;?></option>
							<?}
						}while($db->next_record());	?>
					</select>
				</td>							
			</tr>
			<tr>
			<tr>
				<td>Staff Name / Staff ID</td>			
				<td><input name="searchSupervisorName" type="text" id="searchSupervisorName" size="40" value="<?=$searchSupervisorName;?>" /></td>				
			</tr>
			<tr>
				<td>Field of Expertise / ID</td>			
				<td><input name="searchExpertise" type="text" id="searchExpertise" size="40" value="<?=$searchExpertise;?>" /></td>
				<?$_POST['searchDeptId'] = $searchDeptId;?>
				<?$_POST['searchSupervisorName'] = $searchSupervisorName;?>
				<?$_POST['searchExpertise'] = $searchExpertise;?>
				
				<td><input type="submit" name="btnSearch" id="btnSearch" align="center"  value="Search" /></td>
				<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../supervisor/assign_supervisor.php';" /></td>
				<td>   Note: When clicked, if no parameters are provided, it will search all.</td>
			</tr>
		</table>
		<br />
		
		<?

		
		//$row_cnt = mysql_num_rows($result2);
		if ($row_cnt_sv>0) {?>	
		<table>
			<tr>							
				<td><strong>Searching Results:-</strong></td>
			</tr>
		</table>
		<table border="1" style="border-collapse:collapse;" cellpadding="4" cellspacing="3">
			<tr>
				<td><strong>Tick</strong></td>
				<td><strong>No.</strong></td>
				<td><strong>Dept</strong></td>
				<td><strong>Staff ID</strong></td>
				<td><strong>Name</strong></td>  
				<td><strong>Qualification</strong></td>
				<td><strong>Field of Expertise</strong></td>
				<td><strong>Role</strong></td>
				<td><strong>Skype ID</strong></td>
				<?
				$sql3_1 = "SELECT const_value
				FROM base_constant
				WHERE const_term = 'SV_RESPOND_DURATION'";

				$result3_1 = $dbb->query($sql3_1);
				$dbb->next_record();
				$parameterValue=$dbb->f('const_value');
				$currentDate = date('d-M-Y');					
				?>				
				<td><strong>Reply Date</strong><br/> within <?=$parameterValue?> day(s)</td>	
			</tr>    
			<?

			$no=0;
			$myArrayNo=0;
			$jscript = "";
			do {						
				$employeeId=$dba->f('empid');	
				$employeeName=$dba->f('name');
				$expertise=$dba->f('description');
				$skypeId=$dba->f('skype_id');
				$department=$dba->f('department');
				$unitId=$dba->f('unit_id')
			?>
			<tr>
				<td><input name="supervisorBox[]" type="checkbox" value="<?=$no;?>" /></td>
				
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
				WHERE empid = '$employeeId'";
				
				$resultSqlAreaExpertise = $db->query($sqlAreaExpertise); 
				$db->next_record();
				$recommended=0;
				$tmpAreaExpertise="";
				do {
					$expertise=$db->f('expertise');
					$area=$db->f('area');
					
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
					
				} while ($db->next_record());?>
								
				<td><?=$no+1;?>.
				<? if ($recommended==1)
				{
					?><img src="../images/recommended.jpg" width="20" height="19" style="border:0px;" title="Recommended" /></a><?
				}?>
				</td>
				
				
				<td><a width="20" height="19" style="border:0px;" title="<?=$department;?>"><?=$unitId;?></a></td>
				
				<td><label name="myEmployeeId[]" size="15" id="employeeId" ></label><?=$employeeId;?></td>
				<?$myEmployeeId[$no]=$employeeId;?>
				<?//echo "$no"."-"."myEmployeeId"."-".$myEmployeeId[$no];?>
				
				<td><label name="myEmployeeName[]" size="50" id="employeeName" ></label><?=$employeeName;?></td>
				<?$myEmployeeName[$no]=$employeeName;?>
				<?//echo "$no"."-"."myEmployeeName"."-".$myEmployeeName[$no];?>
										
				<td>
					<?
					$sql_expertise = "SELECT descrip
					FROM education
					WHERE empid='$employeeId'
					AND LEVEL IN ('4','5')
					ORDER BY LEVEL";

					$result_sql_expertise = $db->query($sql_expertise); 
					$db->next_record();
					
					do {
						$educationDesc=$db->f('descrip');	
					?>
					
					<label name="myExpertise[]" size="50" id="expertise" >- <?=$educationDesc;?></label><br/>
					<?
					} while ($db->next_record())
					
			
					?></td>
					<?$myExpertise1[$no1]=$expertise1;?>
					<?//echo "$no1"."-"."myExpertise1"."-".$myExpertise1[$no1];?>
				
				<td><label name="$areaExpertise"><?=$tmpAreaExpertise;?></label></td>				
				
				<td><select name = "myDropDownSType[]">
					<option value="" selected="selected"></option>
				<?
				$dbf->query($sql3); 
				$dbf->next_record();
				do {
					$stId=$dbf->f('id');	
					$stDescription=$dbf->f('description');	
					if (strcmp($stId,$stId2)!=true) {
						?>
							<option value="<?=$stId?>" selected="selected"><?=$stDescription?></option>
							<?$myDropDownSType[$no]=$stId;?>							
							<?echo "myDropDownSType".$myDropDownSType[$no];?>
						<?
					}
					else {?>
							<option value="<?=$stId?>"><?=$stDescription?></option>
							<?$myDropDownSType[$no]=$stId;?>
							<?echo "myDropDownSType".$myDropDownSType[$no];?>
						<?}
				}while ($dbf->next_record());
				?>
				</select></td>
				<td>
			    <label name="mySkypeId[]" id="skypeId" size="20"></label><?=$skypeId;?></td>
				
				<?
					
					$sql3_1 = "SELECT const_value
					FROM base_constant
					WHERE const_term = 'SV_RESPOND_DURATION'";

					$result3_1 = $dbb->query($sql3_1);
					$dbb->next_record();
					$parameterValue=$dbb->f('const_value');
					$currentDate = date('d-M-Y');
					$respondedByDate = date('d-M-Y', strtotime($currentDate. ' + '.$parameterValue.' days'));
					?>	
					
					<td><input type="text" name="myRespondedByDate[]" size="15" id="respondedByDate<?=$no;?>" value="<?php echo $respondedByDate;?>"/><?php /*?><label name="myRespondedByDate[]" size="15" id="respondedByDate" ><?=$respondedbyDate;?></label><?php */?></td>
					<?	$jscript .= "\n" . '$( "#respondedByDate' . $no . '" ).datepicker({
													changeMonth: true,
													changeYear: true,
													yearRange: \'-100:+0\',
													dateFormat: \'dd-M-yy\'
												});';
						 
					//$myRespondedByDate[$no]=$respondedByDate;
?>				
			</tr>  
			<?
			$no=$no+1;
			$myArrayNo=$myArrayNo+1;
			}while($dba->next_record());	
			?>			
			<?$_SESSION['myEmployeeName'] = $myEmployeeName;?>
			<?$_SESSION['myEmployeeId'] = $myEmployeeId;?>
			<?$_SESSION['myRespondedByDate'] = $myRespondedByDate;?>
			<?$_SESSION['myDropDownSType'] = $myDropDownSType;?>
			
		</table>
		<br />
		<br />
		<table>
			<tr>
				<td><input type="submit" name="btnAssign" value="Assign" /></td>
			</tr>
		</table>
		</fieldset>
		<table>
			<tr>
				<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../supervisor/assign_supervisor.php';" /></td>
			</tr>
		</table>
		<?
		}
		else
		{
			?>
			</fieldset>
			<table>
				<tr>
					<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../supervisor/assign_supervisor.php';" /></td>
				</tr>
			</table>
			<?
		}
		?>
	</form>
	<script>
		<?=$jscript1;?>
	</script>
	<script>
		<?=$jscript;?>
	</script>
	</body>
</html>