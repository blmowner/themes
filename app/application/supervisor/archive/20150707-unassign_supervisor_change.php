<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: unassign_supervisor_change.php
//
// Created by: Zuraimi
// Created Date: 16-Jun-2015
// Modified by: Zuraimi
// Modified Date: 16-Jun-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

session_start();
$userid=$_SESSION['user_id'];
$proposalId=$_REQUEST['pid'];
$thesisId=$_REQUEST['tid'];
$matrixNo=$_REQUEST['mn'];

function runnum($column_name, $tblname) 
{ 
    global $db_klas2;
    
    $run_start = "001";
    
    $sql_slct_max = "SELECT MAX($column_name) AS run_id FROM $tblname";
    $sql_slct = $db_klas2;
    $sql_slct->query($sql_slct_max);
    $sql_slct->next_record();

    if($sql_slct->num_rows($sql_slct_max)== 0 || $sql_slct->f("run_id")==NULL) 
	{
        $run_id = date("Ymd").$run_start;
    } 
	else 
	{
        $todate = date("Ymd");
        
        if($todate > substr($sql_slct->f("run_id"),0,8)) 
		{
            $run_id = $todate.$run_start;
        } 
		else 
		{
            $run_id = $sql_slct->f("run_id") + 1; 
        }
    }
    return $run_id;
}
function runnum2($column_name, $tblname) 
{ 
    global $db_klas2;
    
    $run_start = "001";
    
    $sql_slct_max = "SELECT MAX($column_name) AS run_id FROM $tblname";
    $sql_slct = $db_klas2;
    $sql_slct->query($sql_slct_max);
    $sql_slct->next_record();

    if($sql_slct->num_rows($sql_slct_max)== 0 || $sql_slct->f("run_id")==NULL) 
	{
        $run_id = date("Ymd").$run_start;
    } 
	else 
	{
        $todate = date("Ymd");
        
        if($todate > substr($sql_slct->f("run_id"),0,8)) 
		{
            $run_id = $todate.$run_start;
        } 
		else 
		{
            $run_id = $sql_slct->f("run_id") + 1; 
        }
    }
    return $run_id;
}
function runnum3($column_name, $tblname) 
{ 
    global $db_klas2;
    
    $run_start = "001";
    
    $sql_slct_max = "SELECT MAX(SUBSTR($column_name,2,11)) AS run_id FROM $tblname";
    $sql_slct = $db_klas2;
    $sql_slct->query($sql_slct_max);
    $sql_slct->next_record();

    if($sql_slct->num_rows($sql_slct_max)== 0 || $sql_slct->f("run_id")==NULL) 
	{
        $run_id = date("Ymd").$run_start;
    } 
	else 
	{
        $todate = date("Ymd");
        
        if($todate > substr($sql_slct->f("run_id"),0,8)) 
		{
            $run_id = $todate.$run_start;
        } 
		else 
		{
            $run_id = $sql_slct->f("run_id") + 1; 
        }
    }
    return $run_id;
}


if(isset($_POST['btnEdit']) && ($_POST['btnEdit'] <> ""))
{
	$msg = array();
	$loop=$_POST['supervisorBox1'];
	$supervisorBox1=$_POST['supervisorBox1'];
	$empId = $_POST['empId1'];

	//$myDropDownSType1=$_POST['myDropDownSType1'];
	$myDropDownSRole1=$_POST['myDropDownSRole1'];
	$respondedByDateEdit=$_POST['respondedByDateEdit'];
	$myEmployeeId1=$_POST['myEmployeeId1'];
	$mySupervisorId1=$_POST['supervisorId1'];
	$myDropDownSType1=$_POST['role'];
	$empName = $_POST['empName1'];
	$empEmail = $_POST['empEmail1'];
	$unassignedRemarksOverall=$_POST['unassignedRemarksOverall'];
	$curdatetime = date("Y-m-d H:i:s");
	
	if (sizeof($_POST['supervisorBox1'])>0) {
		if(empty($msg)) 
		{
			while (list ($key,$val) = @each ($supervisorBox1)) 
			{
				$sql6 = "SELECT acceptance_status
				FROM pg_supervisor
				WHERE id = '$mySupervisorId1[$val]'";
				
				$result_sql6 = $dbg->query($sql6); 
				$dbg->next_record();
				$theAcceptanceStatus = $dbg->f('acceptance_status');
				 
				if ($theAcceptanceStatus == 'ACC') {
										
					$supervisorUnassignId = runnum('id','pg_supervisor_unassign');
					
					$sql7_2 = "INSERT INTO pg_supervisor_unassign
					(id, unassigned_by, unassigned_date, unassigned_remarks, 
					insert_by, insert_date,	modify_by, modify_date)
					VALUES ('$supervisorUnassignId', '$userid','$curdatetime','$unassignedRemarksOverall',
					'$userid','$curdatetime','$userid','$curdatetime')";
					
					$dbg->query($sql7_2);
					$dbg->next_record();
					
					$sql7_1 = "UPDATE pg_supervisor
					SET status = 'I', supervisor_unassign_id = '$supervisorUnassignId',
					modify_by='$user_id', modify_date='$curdatetime'
					WHERE id='$mySupervisorId1[$val]'
					AND pg_student_matrix_no = '$matrixNo'
					AND acceptance_status = 'ACC'
					AND pg_thesis_id = '$thesisId'
					AND STATUS = 'A'";
					
					$dbg->query($sql7_1);
					$dbg->next_record();
					
					
					
				}
				else { //null
					$sql7 = "DELETE FROM pg_supervisor
					WHERE id='$mySupervisorId1[$val]'
					AND acceptance_status IS NULL
					AND pg_student_matrix_no = '$matrixNo'
					AND pg_thesis_id = '$thesisId'
					AND STATUS = 'A'";
					
					$dbg->query($sql7); 
					$dbg->next_record();
				}
			}
			$sql8 = "SELECT id
			FROM pg_supervisor
			WHERE pg_student_matrix_no = '$matrixNo'
			AND (acceptance_status = 'ACC' OR acceptance_status IS NULL)
			AND pg_thesis_id = '$thesisId'
			AND STATUS = 'A'";
			
			$result_sql8 = $dbg->query($sql8); 
			$dbg->next_record();
			
			$row_cnt8 = mysql_num_rows($result_sql8);
			
			if ($row_cnt8 == 0) {
				$sql9 = "UPDATE pg_thesis
				SET supervisor_status = 'U', modify_by='$user_id', modify_date='$curdatetime'
				WHERE id ='$thesisId'
				AND student_matrix_no = '$matrixNo'";
							
				$dbg->query($sql9); 
			}
			while (list ($key,$val) = @each ($loop)) 
			{
				$sqlapprove = "SELECT COUNT(*) as approve_status, DATE_FORMAT(a.report_date,'%d-%b-%Y') AS report_date_email, a.thesis_title, c.description 
				FROM pg_proposal a
				LEFT JOIN pg_proposal_approval b ON (b.id = a.pg_proposal_approval_id)
				LEFT JOIN ref_thesis_type c ON (c.id = a.thesis_type)
				WHERE a.pg_thesis_id = '$thesisId'
				AND a.pg_proposal_approval_id IS NOT NULL
				AND b.id IS NOT NULL
				AND a.status IN ('APP', 'AWC')";
					
				$result_sqlapprove = $dbb->query($sqlapprove); 
				$dbb->next_record();
				$approve_status = $dbb->f('approve_status');
				$submitDate = $dbb->f('report_date_email');
				$thesisTitle = $dbb->f('thesis_title');
				$thesisType = $dbb->f('description');
				if($approve_status == '1')
				{
					$sqlval = "SELECT const_value FROM base_constant
					WHERE const_term = 'EMAIL_SEN_TO_SUP'";
						
					$result_sqlval = $dbe->query($sqlval); 
					$dbe->next_record();
					$valid = $dbe->f('const_value');
					if($valid == 'Y')
					{
						$sqlstatus = "SELECT email_status FROM pg_employee
						WHERE staff_id = '$myEmployeeId[$val]'";
							
						$result_sqlstatus = $dbg->query($sqlstatus); 
						$dbg->next_record();
						$emailStatus = $dbg->f('email_status');
						if($emailStatus == 'Y')
						{
							$studname = "SELECT name,email FROM student WHERE matrix_no='$matrixNo'";
							$resultstudname = $dbk->query($studname);
							$dbk->next_record();
							$studidname =$dbk->f('name');
							$studemail =$dbk->f('email');
							
							$sql3_3 = "SELECT id, description
							FROM ref_supervisor_type
							WHERE status='A'
							AND id = '$myDropDownSType1[$val]'";
							
							$dbf->query($sql3_3); 
							$dbf->next_record();
							$position =$dbf->f('description');
							
							/*echo "<br>Data for unassign";
							echo "<br>Student Name: $studidname ($matrixNo)";
							echo "<br>Student Email: $studemail";
							echo "<br>Position: $position";
							echo "<br>Supervisor Name: $empName[$val]";
							echo"<br>Submit Date: $submitDate";
							echo "<br>Thesis Id: $thesisId";
							echo "<br>Thesis Title: $thesisTitle";
							echo "<br>Thesis Type: $thesisType";
							echo "<br>Supervisor Email: $empEmail[$val]";*/
							include("../../../app/application/email/email_unassign_supervisor.php");
								
						}
					
					}
				/****************start message**************/	
				$sqlmsj = "SELECT const_value FROM base_constant
				WHERE const_term = 'MESSAGE_SEN_TO_SUP'";
					
				$result_sqlmsj = $dbe->query($sqlmsj); 
				$dbe->next_record();
				$valid = $dbe->f('const_value');
				if($valid == 'Y')
				{

					$studname = "SELECT name,email FROM student WHERE matrix_no='$matrixNo'";
					$resultstudname = $dbk->query($studname);
					$dbk->next_record();
					$studidname =$dbk->f('name');
					$studemail =$dbk->f('email');
					
					$sql3_3 = "SELECT id, description
					FROM ref_supervisor_type
					WHERE status='A'
					AND id = '$myDropDownSType[$val]'";
					
					$dbf->query($sql3_3); 
					$dbf->next_record();
					$position =$dbf->f('description');
					
					/*echo "<br>Data";
					echo "<br>Student Name: $studidname ($matrixNo)";
					echo "<br>Student Email: $studemail";
					echo "<br>Position: $position";
					echo "<br>Supervisor Name: $empName[$val]";
					echo"<br>Submit Date: $submitDate";
					echo "<br>Thesis Id: $thesisId";
					echo "<br>Thesis Title: $thesisTitle";
					echo "<br>Thesis Type: $thesisType";
					echo "<br>Supervisor Email: $empEmail[$val]";*/
					include("../../../app/application/inbox/submission/unassign_supervisor_inbox1.php");

				}
				/****************end message**************/		
					
				}
			
			}
			$msg[] = "<div class=\"success\"><span>The selected Staff has been unassigned successfully for being Supervisor to student $matrixNo.</span></div>";
		}
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please select the Supervisor from the list before click UNASSIGN button.</span></div>";
	}
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

$sql6 = "SELECT a.id, a.pg_employee_empid, a.role_status, f.description as srole, g.description as  acceptance_desc,
a.ref_supervisor_type_id, e.description AS stype, DATE_FORMAT(a.acceptance_date,'%d-%b-%Y') AS acceptance_date,
DATE_FORMAT(a.respondedby_date,'%d-%b-%Y') as respondedby_date,
DATE_FORMAT(a.assigned_date,'%d-%b-%Y') AS assigned_date, a.assigned_by, a.unassigned_remarks
FROM pg_supervisor a
LEFT JOIN ref_supervisor_type e ON (e.id = a.ref_supervisor_type_id)
LEFT JOIN ref_role_status f ON (f.id = a.role_status)
LEFT JOIN ref_acceptance_status g ON (g.id = a.acceptance_status)
LEFT JOIN pg_supervisor_unassign h ON (h.id = a.supervisor_unassign_id)
WHERE a.pg_student_matrix_no = '$matrixNo' 
AND a.ref_supervisor_type_id in ('SV','CS','XS')
AND (a.acceptance_status = 'ACC' OR a.acceptance_status IS NULL)
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

$sql3_2 = "SELECT id, description
		FROM ref_supervisor_type		
		WHERE status='A'
		AND type in ('SV')
		ORDER BY seq";
 
$dbf->query($sql3_2); 
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

	</head>
	
	<body>
	<SCRIPT LANGUAGE="JavaScript">
	function respConfirm () {
		var confirmSubmit = confirm("Click OK if confirm to unassign the staff as being Supervisor to this student \nor click CANCEL to stay on the same page.");
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
		<table>
			<?
			if (substr($matrixNo,0,2) !='07') {
				$dbConn = $dbc;
			}
			else {
				$dbConn = $dbc1;
			}
			$sql = "SELECT name
			FROM student
			WHERE matrix_no = '$matrixNo'";
			
			$dbConn->query($sql);
			$dbConn->next_record();
			$sname = $dbConn->f('name');
			?>		
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
		<fieldset>
		<?
		$result6 = $dbe->query($sql6); 
		$dbe->next_record();	
		$row_cnt = mysql_num_rows($result6);		
		?>
		<legend><strong>List of Assigned Supervisor/Co-Supervisor</strong> - <?=$row_cnt?> record(s) found</legend>		
		<? 
		if ($row_cnt >1)
		{?>
			<div id = "tabledisplay" style="overflow:auto; height:220px;">
		<? }
		else 
		{ ?>
			<div id = "tabledisplay">
		<? } ?>		
		<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="100%" class="thetable">
				<tr>
					<th><strong>Tick</strong></th>
					<th align="center"><strong>No.</strong></th>
					<th><strong>Dept</strong></th>
					<th><strong>Staff Name / Staff ID / Skype ID</strong></th>  
					<th><strong>Qualification</strong></th>
					<th><strong>Field of Expertise</strong></th>
					<th><strong>Role / Role Status</strong></th>
					<th><strong>Assigned By / Date</strong></th>
					<th><strong>Acceptance Date</strong></th>					
				</tr>    
				<?
				
				if ($row_cnt>0) {
				$no1=0;
				$myArrayNo=0;
				$jscript1="";
				do {						
					
					$supervisorId1=$dbe->f('id');	
					$employeeId1=$dbe->f('pg_employee_empid');	
					$expertise1=$dbe->f('expertise');
					$refSupervisorType1=$dbe->f('ref_supervisor_type_id');
					$stDescription1=$dbe->f('stype');
					$refRoleStatusId1=$dbe->f('role_status');
					$roleStatusDescription1=$dbe->f('srole');
					$respondedByDate=$dbe->f('respondedby_date');
					$assignedDate=$dbe->f('assigned_date');
					$assignedBy=$dbe->f('assigned_by');
					$unassignedRemarks=$dbe->f('unassigned_remarks');
					$acceptanceDesc=$dbe->f('acceptance_desc');
					$acceptanceDate=$dbe->f('acceptance_date');
				?>
				<tr>
					<input type="hidden" name="supervisorId1[]" value="<?=$supervisorId1;?>"/>				
					<td><input name="supervisorBox1[]" type="checkbox" value="<?=$no1;?>"/></td>				
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
					
					<input type="hidden" name="myEmployeeId1[]" size="15" id="employeeId1" value="<?=$employeeId1?>" >
					<?$myEmployeeId1[$no1]=$employeeId1;?>
					
					<?
					$sql4="SELECT name AS employee_name
					FROM new_employee
					WHERE empid = '$employeeId1'";
					
					$dbc->query($sql4);
					$row_personal=$dbc->fetchArray();
					$employeeName1=$row_personal['employee_name'];

					$sql4_2="SELECT skype_id,email
					FROM new_employee
					WHERE empid = '$employeeId1'";
					
					$dbc->query($sql4_2);
					$row_personal=$dbc->fetchArray();
					$skypeId1=$row_personal['skype_id'];
					$superEmail1=$row_personal['email'];
					?>
					<td><label name="myEmployeeName1[]" size="50" id="employeeName1"></label><?=$employeeName1;?>
					<input type="hidden" name="empName1[]" id = "empName1" value="<?=$employeeName1?>" />
					<input type = "hidden" name="empEmail1[]" id="empEmail1" value="<?=$superEmail1?>"  />
					<input type = "hidden" name="empId1[]" id="empId1" value="<?=$employeeId1?>"  />
					<br/>(<?=$employeeId1;?>)<br/>
						<em><?=$skypeId1?></em>
						<?$myEmployeeName1[$no1]=$employeeName1;?>
						<?$mySkypeId1[$no1]=$skypeId1;?><br/>
						<a href="../supervisor/unassign_supervisor_biodata.php?tid=<?=$thesisId;?>&sid=<?=$employeeId1;?>&pid=<?=$proposalId;?>
						&ename=<?=$employeeName1;?>&mn=<?=$matrixNo;?>&id=<?=$supervisorId1;?>"><br/>View Brief Biodata </a>


					<?if ($unassignedRemarks != null || $unassignedRemarks != '') {?>
						<a href="../supervisor/unassign_supervisor_remarks.php?tid=<?=$thesisId;?>&sid=<?=$employeeId1;?>&pid=<?=$proposalId;?>&mn=<?=$matrixNo;?>&id=<?=$supervisorId1;?>"><br/><img src="../images/edit.jpg" width="20" height="19" style="border:0px;" title="Remark has been entered" />Update Remarks</a><br/>
					<?}
					else {?>
						<a href="../supervisor/unassign_supervisor_remarks.php?tid=<?=$thesisId;?>&sid=<?=$employeeId1;?>&pid=<?=$proposalId;?>&mn=<?=$matrixNo;?>&id=<?=$supervisorId1;?>"><br/><img src="../images/red_edit.jpg" width="20" height="19" style="border:0px;" title="Remark is yet to be entered" />Enter Remarks </a>
					<?}?>
					</td>
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


					<?$myExpertise1[$no1]=$expertise1;?>

					
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
					
					<td><label><?=$stDescription1?></label><input type="hidden" name = "role[]" id="role" value="<?=$refSupervisorType1?>" /><br/>
					<label><?=$roleStatusDescription1?></label></td>
					<?
					if (substr($employeeId1,0,3)!='S07') {
						$dbConn = $dbc;
					}
					else {
						$dbConn = $dbc1;
					}
					$sql1="SELECT name
					FROM new_employee
					WHERE empid = '$assignedBy'";
					
					$dbConn->query($sql1);
					$dbConn->next_record();
					$assignedName = $dbc->f('name');
					
					?>
					<td><label><?=$assignedName?><br/>(<?=$assignedBy?>)<br/><?=$assignedDate?></label></td>
					<td><label><?=$acceptanceDate?></label></td>					
				</tr>  
				<?
				$no1=$no1+1;
				$myArrayNo1=$myArrayNo1+1;
				}while($dbe->next_record());	
				?>			
				<?$_SESSION['myEmployeeName1'] = $myEmployeeName1;?>
				<?$_SESSION['myEmployeeId1'] = $myEmployeeId1;?>
				<?$_SESSION['myDropDownSType1'] = $myDropDownSType1;?>
				<?$_POST['respondedByDateEdit'] = $respondedByDateEdit;?>
				
			</table>
			</div>		
			<br/>
			<table>
				<tr>
					<td><strong>Overall Remarks</strong></td>
				</tr>	
				<tr>
					<td><textarea name="unassignedRemarksOverall" cols="30" rows="3" id="unassignedRemarksOverall" class="ckeditor"></textarea></td>
				</tr>	
				<tr>
					<td><input type="submit" name="btnEdit" value="Unassign" onclick="return respConfirm()"/></td>
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
		<table>
			<tr>
				<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../supervisor/unassign_supervisor.php';" /></td>
			</tr>
		</table>		
	</form>
	<script>
		<?=$jscript;?>
		<?=$jscript1;?>
	</script>
	</body>
</html>