<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: assign_supervisor.php
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
//$deptId=$_SESSION['dropDownDept'];

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

if(isset($_POST['btnAssign']) && !empty($_POST['btnAssign']) )
{
	//echo "btnAssign set ++++++++++++++++++++"."<br/>";
	$msg = array();
	$supervisorBox=$_POST['supervisorBox'];
	$myDropDownSType=$_POST['myDropDownSType'];
	$myRespondedByDate=$_SESSION['myRespondedByDate'];
	//echo var_dump($_POST);

	if (sizeof($_POST['supervisorBox'])>0) {

		while (list ($key,$val) = @each ($supervisorBox)) 
		{			
			$run_pg_supervisor_id = run_num('id','pg_supervisor'); // generate id (running number)
			
			/*echo "supervisorBox key".$key." ";
			echo "val".$val."<br/>";
			echo "myEmployeeId ".$val." ".$myEmployeeId[$val]."<br/>";	
			echo "myEmployeeName ".$val." ".$myEmployeeName[$val]."<br/><br/>>";
			echo "myExpertise ".$val." ".$myExpertise[$val]."<br/><br/>";
			echo "myRespondedByDate ".$val." ".$myRespondedByDate[$val]."<br/><br/>";
			/*echo "myDropDownSType ".$val." ".$myDropDownSType[$val]."<br/><br/>";*/
			

			if ($myDropDownSType[$val]=='') $role='XS';
			else $role=$myDropDownSType[$val];
			
			$sql4="INSERT INTO pg_supervisor
				(id, pg_employee_empid, pg_student_matrix_no, pg_thesis_id, assigned_by, assigned_date, 
				respondedby_date, ref_supervisor_type_id, status, insert_by, insert_date, modify_by, modify_date)
				VALUES ('$run_pg_supervisor_id', '$myEmployeeId[$val]', '$matrixNo', '$thesisId', '$userid', '$curdatetime', 
				STR_TO_DATE('".$_POST['myRespondedByDate'][$val]."','%d-%b-%Y'),'$role', 'A', '$userid', '$curdatetime', '$userid', 
				'$curdatetime') ";
				
				$dbb->query($sql4); 
			
		}
		$sql5="UPDATE pg_thesis
			SET supervisor_status = 'A'
			WHERE id = '$thesisId'
			AND student_matrix_no = '$matrixNo'";
		
			//echo $sql5;
		
			$dbe->query($sql5); 
		//$dbe->next_record();	
		
		$msg[] = "<div class=\"success\"><span>The selected Supervisor has been added to the list successfully.</span></div>";
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please select the Supervisor first before click ASSIGN button.</span></div>";
	}

}

if(isset($_POST['btnEdit']) && ($_POST['btnEdit'] <> ""))
{
	//echo "btnEdit set ++++++++++++++++++++"."<br/>";
	$supervisorBox1=$_POST['supervisorBox1'];
	$myDropDownSType1=$_POST['myDropDownSType1'];
	$respondedByDate=$_POST['respondedByDate'];
	
	if (sizeof($_POST['supervisorBox1'])>0) {
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
		$msg[] = "<div class=\"success\"><span>The selected Supervisor has been updated successfully.</span></div>";
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please select the Supervisor from the list before click UPDATE button.</span></div>";
	}
}

if(isset($_POST['btnDelete']) && ($_POST['btnDelete'] <> ""))
{
	//echo "btnDelete set ++++++++++++++++++++"."<br/>";
	$supervisorBox1=$_POST['supervisorBox1'];
	$myDropDownSType1=$_POST['myDropDownSType1'];
	$thesisId=$_REQUEST['tid'];
	$msg=array();
	if (sizeof($_POST['supervisorBox1'])>0) {
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
		$msg[] = "<div class=\"success\"><span>The selected Supervisor has been deleted from the list successfully.</span></div>";
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please select the Supervisor from the list before click DELETE button.</span></div>";
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
		$tmpSearchReviewerName="";
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
		/*AND a.dept_id='ACAD'*/ "		
		.$tmpSearchDeptId." "
		.$tmpSearchSupervisorName." "
		.$tmpSearchExpertise." "."
		AND c.level IN ('4','5')
		ORDER BY a.unit_id, a.name, a.empid";		
		
		$result_sql2 = $dbc->query($sql2); 
		$dbc->next_record();
		$row_cnt_sv = mysql_num_rows($result_sql2);
		$no1=0;
		$employeeIdArray = Array();
		$expertiseArray = Array();
		$departmentArray = Array();
		$unitIdArray = Array();
				
		do {						
			$employeeId=$dbc->f('empid');	
			$expertise=$dbc->f('description');
			$department=$dbc->f('department');
			$unitId=$dbc->f('unit_id');
			
			$employeeIdArray[$no1]=$employeeId;
			$expertiseArray[$no1]=$expertise;
			$departmentArray[$no1]=$department;
			$unitIdArray[$no1]=$unitId;
			$no1++;
			
		}while($dbc->next_record());

}
else {
		$row_cnt_sv = 0;
}
$sql11 = "SELECT a.ref_supervisor_type_id
			FROM pg_supervisor a
			LEFT JOIN ref_supervisor_type e ON (e.id = a.ref_supervisor_type_id)
			WHERE a.pg_student_matrix_no = '$matrixNo' 
			AND a.ref_supervisor_type_id in ('SV')
			AND a.acceptance_status IS NULL
			AND a.pg_thesis_id = '$thesisId'
			AND a.status = 'A' 
			ORDER BY a.pg_employee_empid, e.id";
			
			$result_sql11 = $db->query($sql11); 
			$db->next_record();

$row_cnt_sv_chk = mysql_num_rows($result_sql11);

$sql6 = "SELECT a.id, a.pg_employee_empid, 
		a.ref_supervisor_type_id, e.description AS stype, DATE_FORMAT(a.respondedby_date,'%d-%b-%Y') as respondedby_date,
		a.brief_biodata
		FROM pg_supervisor a
		LEFT JOIN ref_supervisor_type e ON (e.id = a.ref_supervisor_type_id)
		WHERE a.pg_student_matrix_no = '$matrixNo' 
		AND a.ref_supervisor_type_id in ('SV','CS','EI','EE','XS')
		AND a.acceptance_status IS NULL
		AND a.pg_thesis_id = '$thesisId'
		AND a.status = 'A' 
		ORDER BY e.seq, a.pg_employee_empid";
	

$sql3 = "SELECT id, description
		FROM ref_supervisor_type
		WHERE status='A'
		AND type in ('SV','XS')
		ORDER BY seq";
 
$dbf->query($sql3); 
$dbf->next_record();
//echo $sql3;	

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
		<legend><strong>List of Assigned Supervisor</strong></legend>
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
								
							$result5 = $dbc->query($sql5); 
							$dbc->next_record();
							$sname=$dbc->f('student_name');
				
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
					<th><strong>Tick</strong></th>
					<th align="center"><strong>No.</strong></th>
					<th><strong>Dept</strong></th>
					<th><strong>Name</strong></th>  
					<th><strong>Qualification</strong></th>
					<th><strong>Field of Expertise</strong></th>
					<th><strong>Role</strong></th>
					<th><strong>Skype ID</strong></th>
					<th><strong>Reply Date</strong></th>					
				</tr>    
				<?
				$result6 = $dbe->query($sql6); 
				$dbe->next_record();
				//echo $sql6;
				$row_cnt = mysql_num_rows($result6);
				if ($row_cnt>0) {
				$no1=0;
				$myArrayNo=0;
				$jscript1="";
				do {						
					
					$supervisorId1=$dbe->f('id');	
					$employeeId1=$dbe->f('pg_employee_empid');	
					//$departmentId1=$dbe->f('dept_id');
					//$department1=$dbe->f('department');
					$expertise1=$dbe->f('expertise');
					$refSupervisorType1=$dbe->f('ref_supervisor_type_id');
					$stDescription1=$dbe->f('stype');
					$respondedByDate=$dbe->f('respondedby_date');
					$briefBiodata=$dbe->f('brief_biodata');
				?>
				<tr>
					<input type="hidden" name="supervisorId1[]" value="<?=$supervisorId1;?>"/>				
					<td><input name="supervisorBox1[]" type="checkbox" value="<?=$no1;?>"/></td>				
					<td align="center"><?=$no1+1;?>.</td>
					<?
					$sql4="SELECT a.id as dept_id, a.description as department
					FROM dept_unit a
					LEFT JOIN new_employee b ON (b.unit_id = a.id)
					WHERE empid = '$employeeId1'";
					
					$dbc->query($sql4);
					$row_personal=$dbc->fetchArray();
					$departmentId1=$row_personal['dept_id'];
					$department1=$row_personal['department'];
					?>
					<td><a width="20" height="19" style="border:0px;" title="<?=$department1;?>"><?=$departmentId1;?></a></td>
					
					<input type="hidden" name="myEmployeeId1[]" size="15" id="employeeId1" >
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
                            <a href="../supervisor/edit_supervisor_biodata.php?tid=<?=$thesisId;?>&sid=<?=$employeeId1;?>&pid=<?=$proposalId;?>&ename=<?=$employeeName1;?>&mn=<?=$matrixNo;?>&sname=<?=$sname;?>&id=<?=$supervisorId1;?>"><br/><img src="../images/edit.jpg" width="20" height="19" style="border:0px;" title="Supervisor has been assigned" />Read Brief Biodata </a><br/>
                            <?}
								else {?>
                            <a href="../supervisor/edit_supervisor_biodata.php?tid=<?=$thesisId;?>&sid=<?=$employeeId1;?>&pid=<?=$proposalId;?>&ename=<?=$employeeName1;?>&mn=<?=$matrixNo;?>&sname=<?=$sname;?>&id=<?=$supervisorId1;?>"><br/><img src="../images/red_edit.jpg" width="20" height="19" style="border:0px;" title="Supervisor is not assigned yet" />View Brief Biodata </a>
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
					<?$myExpertise1[$no1]=$expertise1;?>
					<?//echo "$no1"."-"."myExpertise1"."-".$myExpertise1[$no1];?>
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
					<?
					$sql4="SELECT skype_id
					FROM new_employee
					WHERE empid = '$employeeId1'";
					
					$dbc->query($sql4);
					$row_personal=$dbc->fetchArray();
					$skypeId1=$row_personal['skype_id'];
					?>
					<td><label name="mySkypeId1[]" id="skypeId1" size="20" ></label><?=$skypeId1?></td>
					<?$mySkypeId1[$no1]=$skypeId1;?>
					
					<td><input type="text" name="respondedByDate[]" size="15" id="respondedByDate<?=$no1;?>" value="<?=$respondedByDate;?>"/><?php /*?><input type="text" name="respondedByDate[]" size="15" id="respondedByDate" value="<?=$respondedByDate;?>"/><?php */?></td>
					<?	$jscript1 .= "\n" . '$( "#respondedByDate' . $no1 . '" ).datepicker({
													changeMonth: true,
													changeYear: true,
													yearRange: \'-100:+0\',
													dateFormat: \'dd-M-yy\'
												});';
						 
						?>
					
					
				</tr>  
				<?
				$no1=$no1+1;
				$myArrayNo1=$myArrayNo1+1;
				}while($dbe->next_record());	
				?>			
				<?$_SESSION['myEmployeeName1'] = $myEmployeeName1;?>
				<?$_SESSION['myEmployeeId1'] = $myEmployeeId1;?>
				<?$_POST['respondedByDate'] = $respondedByDate;?>
				<?$_SESSION['myDropDownSType1'] = $myDropDownSType1;?>
				
			</table>
			<table>
				<tr>
					<td><?if ($row_cnt_sv_chk==0) {							
							?><label><strong><span style="color:#FF0000">Note:</span></strong> Please assign Supervisor to supervise this student.</label><br/><?
					}?></td>
				</tr>
			</table>
			<br />
			<table>
				<tr>
					<td><input type="submit" name="btnEdit" value="Update" /></td>
					<td><input type="submit" name="btnDelete" value="Delete" onClick="return respConfirm()"  /></td>
					<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../thesis/confirm_proposal.php';" /></td>					
				</tr>
			</table>
			
			<?
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
		<legend><strong>Assign New Supervisor</strong></legend>
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
				<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../thesis/confirm_proposal.php';" /></td>
				<td><span style="color:#FF0000"> Note:</span>If no entry is provided, it will search all.</td>
			</tr>
		</table>
		<br />

		<table>
				<tr>							
					<td>Searching Results:-</td>
				</tr>
		</table>
		<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="100%" class="thetable">
			<tr>
				<th><strong>Tick</strong></th>
				<th align="center"><strong>No.</strong></th>
				<th><strong>Dept</strong></th>
				<th><strong>Staff ID</strong></th>
				<th><strong>Name</strong></th>  
				<th><strong>Qualification</strong></th>
				<th><strong>Field of Expertise</strong></th>
				<th><strong>Role</strong></th>
				<th><strong>Skype ID</strong></th>		  
				<?
				$sql3_1 = "SELECT const_value
				FROM base_constant
				WHERE const_term = 'SV_RESPOND_DURATION'";

				$result3_1 = $dbb->query($sql3_1);
				$dbb->next_record();
				$parameterValue=$dbb->f('const_value');
				$currentDate = date('d-M-Y');					
				?>				
				<th><strong>Reply Date</strong><br/> within <?=$parameterValue?> day(s)</th>	
			</tr>    
			<?
			$supervisorArray = Array();
				$sql10 = "SELECT pg_employee_empid FROM pg_supervisor
						WHERE ref_supervisor_type_id in ('SV','CS','EI','EE','XS')
						AND pg_student_matrix_no = '$matrixNo'
						AND pg_thesis_id = '$thesisId'
						AND status = 'A'";
				
				$result_sql10 = $dbg->query($sql10); 
				$dbg->next_record();
			
			if (($row_cnt_sv>0) && ($row_cnt_sv!=mysql_num_rows($result_sql10))) {
			$no=0;
			$myArrayNo=0;
			$jscript = "";
			
			
			$no2=0;
			?>
			
			<?
				
				$no4=0;
				do {
					$supervisorArray[$no4]= $dbg->f('pg_employee_empid');					
					$no4++;
				} while ($dbg->next_record());
				$display = "DISPLAY";
				for ($no = 0; $no < count($employeeIdArray); $no++) 
				{

					for ($no5 = 0; $no5 < count($supervisorArray); $no5++) 
					{				
						if ($supervisorArray[$no5]==$employeeIdArray[$no])
						{							
							$display="NO_DISPLAY";
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
								
				<td align="center"><?=++$no2;?>.
				<? if ($recommended==1)
				{
					?><img src="../images/recommended.jpg" width="20" height="19" style="border:0px;" title="Recommended" /></a><?
				}?>
				</td>
				<td><a><width="20" height="19" style="border:0px;" title="<?=$departmentArray[$no];?>"><?=$unitIdArray[$no];?></a></td>
				
				<td><label name="myEmployeeId[]" size="15" id="employeeId" ></label><?=$employeeIdArray[$no];?></td>
				<?$myEmployeeId[$no]=$employeeIdArray[$no];?>
				<?
				$sql4="SELECT name AS employee_name
				FROM new_employee
				WHERE empid = '$employeeIdArray[$no]'";
				
				$dbc->query($sql4);
				$row_personal=$dbc->fetchArray();
				$employeeName=$row_personal['employee_name'];
				?>
				<td><label name="myEmployeeName[]" size="50" id="employeeName" ></label><?=$employeeName;?></td>
				<?$myEmployeeName[$no]=$employeeName;?>
										
				<td>
					<?
					$sql_expertise = "SELECT descrip
					FROM education
					WHERE empid='$employeeIdArray[$no]'
					AND LEVEL IN ('4','5')
					ORDER BY LEVEL";
					
					$sql_expertise;
					$result_sql_expertise = $dbc->query($sql_expertise); 
					$dbc->next_record();
					
					do {
						$educationDesc=$dbc->f('descrip');	
					?>
					
					<label name="myExpertise[]" size="50" id="expertise" >- <?=$educationDesc;?></label><br/>
					<?
					} while ($dbc->next_record())
					
			
					?></td>
					<?$myExpertise1[$no1]=$expertise1;?>
				
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
				<?
				$sql4="SELECT skype_id
				FROM new_employee
				WHERE empid = '$$employeeIdArray[$no]'";
				
				$dbc->query($sql4);
				$row_personal=$dbc->fetchArray();
				$skypeId=$row_personal['skype_id'];
				?>
				<td><label name="mySkypeId[]" id="skypeId" size="20"></label><?=$skypeId;?></td>
				
				<?$respondedByDate = date('d-M-Y', strtotime($currentDate. ' + '.$parameterValue.' days'));
					?>	
					
					<td><input type="text" name="myRespondedByDate[]" size="15" id="respondedByDate<?=$no;?>" value="<?php echo $respondedByDate;?>"/><?php /*?><label name="myRespondedByDate[]" size="15" id="respondedByDate" ><?=$respondedbyDate;?></label><?php */?></td>
					<?	$jscript .= "\n" . '$( "#respondedByDate' . $no . '" ).datepicker({
													changeMonth: true,
													changeYear: true,
													yearRange: \'-100:+0\',
													dateFormat: \'dd-M-yy\'
												});';
						 
						?>
					<? //$myRespondedByDate[$no]=$respondedByDate;	?>
					
					
			</tr>  
			<?
			//$no=$no+1;
			$myArrayNo=$myArrayNo+1;
			}while($dbc->next_record());
			}			
			?>			
			<?$_SESSION['myEmployeeName'] = $myEmployeeName;?>
			<?$_SESSION['myEmployeeId'] = $myEmployeeId;?>
			<?$_SESSION['myRespondedByDate'] = $myRespondedByDate;?>
			<?$_SESSION['myDropDownSType'] = $myDropDownSType;?>
			
		</table>
		<br />
		<table>
			<tr>
				<td><input type="submit" name="btnAssign" value="Assign" /><span style="color:#FF0000"> Note:</span> Please select the Supervisor from the list before click ASSIGN button.</td>
			</tr>
		</table>
		
		
		<?
		}
		else
		{
		?>
		<table>
			<tr>
				<td>No record found!</td>
			</tr>
		</table>	
		<?
		}
		?>
		</fieldset>
		<table>
			<tr>
				<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../thesis/confirm_proposal.php';" /></td>
			</tr>
		</table>
	</form>
	<script>
		<?=$jscript;?>
	</script>
	<script>
		<?=$jscript1;?>
	</script>
	</body>
</html>