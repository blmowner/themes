<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: edit_supervisor_senate.php
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
function runnum($column_name, $tblname) 
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

if(isset($_POST['btnAssign']) && !empty($_POST['btnAssign']) )
{
	$msg = array();
	$supervisorBox=$_POST['supervisorBox'];
	$myDropDownSType=$_POST['myDropDownSType'];
	$myDropDownSRole=$_POST['myDropDownSRole'];
	$myRespondedByDate=$_SESSION['myRespondedByDate'];
	$empName = $_POST['empName'];
	$empEmail = $_POST['empEmail'];
	$empId = $_POST['empId'];

	$primaryCount = 0;
	if (sizeof($_POST['supervisorBox'])>0) {
		$tmpSupervisorBox = $supervisorBox;
		//validate primary supervisor status for new staff
		while (list ($key,$val) = @each ($tmpSupervisorBox))  {
			if ($myDropDownSType[$val] == 'CS' && $myDropDownSRole[$val]=='PRI') {
				$msg[] = "<div class=\"error\"><span>Please check role for this staff $myEmployeeId[$val]. Only role Supervisor can be assigned as Primary. </span></div>";
			}
			if ($myDropDownSType[$val] == 'SV' && $myDropDownSRole[$val]=='PRI') {
				//validate primary supervisor status for existing staff
				$sql_1 = "SELECT pg_employee_empid
				FROM pg_supervisor
				WHERE ref_supervisor_type_id in ('SV','XS')
				AND pg_student_matrix_no = '$matrixNo'
				AND pg_thesis_id = '$thesisId'
				AND role_status = 'PRI'
				AND STATUS = 'A'";
					
				$result_sql_1 = $dbg->query($sql_1); 
				$dbg->next_record();
				$employeeId = $dbg->f('pg_employee_empid');
				$row_cnt_1 = mysql_num_rows($result_sql_1);
				
				if ($row_cnt_1>0) {
					$msg[] = "<div class=\"error\"><span>The staff $employeeId in the list below has been assigned as Primary Supervisor already. Please check the role for new selected staff $myEmployeeId[$val]. </span></div>";
				}
				else {
					$primaryCount++;
				}
			}	
			if ($primaryCount > 1) {
				$msg[] = "<div class=\"error\"><span>The Primary Supervisor should not be more than 1 staff. Please check.</span></div>";
			}
		}
			
		if(empty($msg)) 
		{
			while (list ($key,$val) = @each ($supervisorBox)) 
			{			
				$run_pg_supervisor_id = run_num('id','pg_supervisor'); // generate id (running number)

				if ($myDropDownSType[$val]=='') $role='XS';
				else $role=$myDropDownSType[$val];
				
				$sql4="INSERT INTO pg_supervisor
				(id, pg_employee_empid, role_status, pg_student_matrix_no, pg_thesis_id, assigned_by, assigned_date, 
				respondedby_date, ref_supervisor_type_id, status, insert_by, insert_date, modify_by, modify_date)
				VALUES ('$run_pg_supervisor_id', '$myEmployeeId[$val]', '$myDropDownSRole[$val]', '$matrixNo', '$thesisId', '$userid', '$curdatetime', 
				STR_TO_DATE('".$_POST['myRespondedByDate'][$key]."','%d-%b-%Y'),'$role', 'A', '$userid', '$curdatetime', '$userid', 
				'$curdatetime') ";
					
				$dbb->query($sql4);
				
				$sqlapprove = "SELECT DATE_FORMAT(a.report_date,'%d-%b-%Y') AS report_date_email, a.thesis_title, c.description 
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
				$row_cnt2 = mysql_num_rows($result_sqlapprove);
				if($row_cnt2 == 1)
				{
					/****************start email**************/
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
							if (substr($matrixNo,0,2) != '07') { 
								$dbConnStudent= $dbk; 
							} 
							else { 
								$dbConnStudent=$dbc1; 
							}

							$resultstudname = $dbConnStudent->query($studname);
							$dbConnStudent->next_record();
							$studidname =$dbConnStudent->f('name');
							$studemail =$dbConnStudent->f('email');
							
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
							include("../../../app/application/email/email_assign_supervisor_1.php");
								
						}
					/****************end email**************/
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
							if (substr($matrixNo,0,2) != '07') { 
								$dbConnStudent= $dbk; 
							} 
							else { 
								$dbConnStudent=$dbc1; 
							}
							$resultstudname = $dbConnStudent->query($studname);
							$dbConnStudent->next_record();
							$studidname =$dbConnStudent->f('name');
							$studemail =$dbConnStudent->f('email');
							
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
							include("../../../app/application/inbox/submission/senate_to_supervisor_inbox1.php");

					}
					/****************end message**************/	
				}
 
				
			}
			$sql5="UPDATE pg_thesis
				SET supervisor_status = 'A'
				WHERE id = '$thesisId'
				AND student_matrix_no = '$matrixNo'";
			
				$dbe->query($sql5); 

			
			$msg[] = "<div class=\"success\"><span>The selected Supervisor has been added to the list successfully.</span></div>";
			
			/*while (list ($key,$val) = @each ($supervisorBox)) 
			{	//echo "lol";			
				
			}*/


		}
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please select the Supervisor first before click ASSIGN button.</span></div>";
	}

}

if(isset($_POST['btnEdit']) && ($_POST['btnEdit'] <> ""))
{
	$msg = array();
	$supervisorBox1=$_POST['supervisorBox1'];
	$myDropDownSType1=$_POST['myDropDownSType1'];
	$myDropDownSRole1=$_POST['myDropDownSRole1'];
	$respondedByDateEdit=$_POST['respondedByDateEdit'];
	$myEmployeeId1=$_POST['myEmployeeId1'];
	$empName = $_POST['empName1'];
	$empEmail = $_POST['empEmail1'];
	$empId = $_POST['empId1'];

	$primaryCount = 0;
	
	if (sizeof($_POST['supervisorBox1'])>0) {
		$tmpSupervisorBox1 = $supervisorBox1;
		//validate primary supervisor status for new staff
		while (list ($key,$val) = @each ($tmpSupervisorBox1))  {
			if ($myDropDownSType1[$val] == 'CS' && $myDropDownSRole1[$val]=='PRI') {
				$msg[] = "<div class=\"error\"><span>Please check new role for this staff $myEmployeeId1[$val]. Only role Supervisor can be assigned as Primary. </span></div>";
			}
			if ($myDropDownSType1[$val] == 'SV' && $myDropDownSRole1[$val]=='PRI') {
				//validate primary supervisor status for existing staff
				$sql_1 = "SELECT pg_employee_empid
				FROM pg_supervisor
				WHERE ref_supervisor_type_id in ('SV','XS')
				AND pg_student_matrix_no = '$matrixNo'
				AND pg_thesis_id = '$thesisId'
				AND role_status = 'PRI'
				AND STATUS = 'A'";
					
				$result_sql_1 = $dbg->query($sql_1); 
				$dbg->next_record();
				$employeeId = $dbg->f('pg_employee_empid');
				$row_cnt_1 = mysql_num_rows($result_sql_1);
				
				if ($row_cnt_1>0) {
					if ($employeeId != $myEmployeeId1[$val])
					$msg[] = "<div class=\"error\"><span>The staff $employeeId in the list below has been assigned as Primary Supervisor already. Please check the role for new selected staff $myEmployeeId1[$val]. </span></div>";
				}
				else {
					$primaryCount++;
				}
			}	
			if ($primaryCount > 1) {
				$msg[] = "<div class=\"error\"><span>The Primary Supervisor should not be more than 1 staff. Please check.</span></div>";
			}
		}
			
		if(empty($msg)) 
		{
			while (list ($key,$val) = @each ($supervisorBox1)) 
			{
				if ($myDropDownSType1[$val]=='') $role='XS';
				else $role=$myDropDownSType1[$val];
				
				$sql7 = "UPDATE pg_supervisor
				SET ref_supervisor_type_id='$role', role_status = '$myDropDownSRole1[$val]',
				respondedby_date = STR_TO_DATE('$respondedByDateEdit[$val]','%d-%b-%Y'),
				modify_by='$user_id', modify_date='$curdatetime'
				WHERE id='$supervisorId1[$val]'
				AND pg_student_matrix_no = '$matrixNo'
				AND pg_thesis_id = '$thesisId'
				AND STATUS = 'A'";
				
				$dbg->query($sql7); 
				$dbg->next_record();
				
				$sql9 = "UPDATE pg_thesis
				SET supervisor_status = 'A'
				WHERE id ='$thesisId'
				AND student_matrix_no = '$matrixNo'";
							
				$dbg->query($sql9); 
				
				$sqlapprove = "SELECT DATE_FORMAT(a.report_date,'%d-%b-%Y') AS report_date_email, a.thesis_title, c.description 
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
				$row_cnt3 = mysql_num_rows($result_sqlapprove);
				if($row_cnt3 == 1)
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
							if (substr($matrixNo,0,2) != '07') { 
								$dbConnStudent= $dbk; 
							} 
							else { 
								$dbConnStudent=$dbc1; 
							}
							$resultstudname = $dbConnStudent->query($studname);
							$dbConnStudent->next_record();
							$studidname =$dbConnStudent->f('name');
							$studemail =$dbConnStudent->f('email');
							
							$sql3_3 = "SELECT id, description
							FROM ref_supervisor_type
							WHERE status='A'
							AND id = '$myDropDownSType1[$val]'";
							
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
							include("../../../app/application/email/email_assign_supervisor_1.php");
								
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
							if (substr($matrixNo,0,2) != '07') { 
								$dbConnStudent= $dbk; 
							} 
							else { 
								$dbConnStudent=$dbc1; 
							}
							$resultstudname = $dbConnStudent->query($studname);
							$dbConnStudent->next_record();
							$studidname =$dbConnStudent->f('name');
							$studemail =$dbConnStudent->f('email');
							
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
							include("../../../app/application/inbox/submission/senate_to_supervisor_inbox1.php");

					}
					/****************end message**************/						
				}
				
				
			}
			$msg[] = "<div class=\"success\"><span>The selected Supervisor has been updated successfully.</span></div>";
		}
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please select the Supervisor from the list before click UPDATE button.</span></div>";
	}
}

if(isset($_POST['btnDelete']) && ($_POST['btnDelete'] <> ""))
{
	$supervisorBox1=$_POST['supervisorBox1'];
	$myDropDownSType1=$_POST['myDropDownSType1'];
	$thesisId=$_REQUEST['tid'];
	$empName = $_POST['empName1'];
	$empEmail = $_POST['empEmail1'];
	$empId = $_POST['empId1'];

	$msg=array();
	if (sizeof($_POST['supervisorBox1'])>0) {
		while (list ($key,$val) = @each ($supervisorBox1)) 
		{
			$sql8 = "DELETE FROM pg_supervisor
			WHERE id='$supervisorId1[$val]'
			AND ref_supervisor_type_id in ('SV','CS','XS')
			AND pg_student_matrix_no = '$matrixNo'
			AND pg_thesis_id = '$thesisId'
			AND STATUS = 'A'";
			
			$dbg->query($sql8); 

			$sqlapprove = "SELECT DATE_FORMAT(a.report_date,'%d-%b-%Y') AS report_date_email, a.thesis_title, c.description 
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
			$row_cnt4 = mysql_num_rows($result_sqlapprove);
			if($row_cnt4 == 1)
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
						if (substr($matrixNo,0,2) != '07') { 
							$dbConnStudent= $dbk; 
						} 
						else { 
							$dbConnStudent=$dbc1; 
						}
						$resultstudname = $dbConnStudent->query($studname);
						$dbConnStudent->next_record();
						$studidname =$dbConnStudent->f('name');
						$studemail =$dbConnStudent->f('email');
						
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
					if (substr($matrixNo,0,2) != '07') { 
						$dbConnStudent= $dbk; 
					} 
					else { 
						$dbConnStudent=$dbc1; 
					}
					$resultstudname = $dbConnStudent->query($studname);
					$dbConnStudent->next_record();
					$studidname =$dbConnStudent->f('name');
					$studemail =$dbConnStudent->f('email');
					
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
					include("../../../app/application/inbox/submission/unassign_supervisor_inbox.php");

				}
				/****************end message**************/					
			}
		}
		
		$sql9 = "SELECT id
		FROM pg_supervisor
		WHERE pg_student_matrix_no = '$matrixNo'
		AND ref_supervisor_type_id in ('SV','CS','XS')
		AND pg_thesis_id = '$thesisId'
		AND STATUS = 'A'";

		$result_sql9 = $dbg->query($sql9); 
		
		$row_cnt = mysql_num_rows($result_sql9);
		if ($row_cnt==0) {
			$sql10="UPDATE pg_thesis
			SET supervisor_status = 'U'
			WHERE id = '$thesisId'
			AND student_matrix_no = '$matrixNo'";

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
		WHERE a.teachingcat IN ('1','2','3') 
		/*AND a.dept_id='ACAD'*/ "		
		.$tmpSearchDeptId." "
		.$tmpSearchSupervisorName." "
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
		$emailStatusArray = Array();
		
		$theEmployeeIdArray = Array();
		$theEmployeeNameArray = Array();
		$theExpertiseArray = Array();
		$theEkypeIdArray = Array();
		$theDepartmentArray = Array();
		$theUnitIdArray = Array();
		$theEmailStatusArray = Array();
		
		$no1=0;	

		if ($row_cnt_sv>0) {
			do {
				$employeeId=$dbc->f('empid');	
				
				$sql10 = "SELECT a.pg_employee_empid, b.email_status 
				FROM pg_supervisor a
				LEFT JOIN pg_employee b ON (b.staff_id = a.pg_employee_empid) 
				WHERE a.ref_supervisor_type_id in ('SV','CS','XS')
				AND a.pg_student_matrix_no = '$matrixNo'
				AND a.pg_thesis_id = '$thesisId'
				AND a.pg_employee_empid = '$employeeId'
				AND a.status = 'A'";
				
				$result_sql10 = $dbg->query($sql10); 
				$dbg->next_record();
				$emailStatus1=$dbg->f('email_status');
			
				$row_cnt10 = mysql_num_rows($result_sql10);
				if ($row_cnt10 == 0) {
				
					$sql10 = "SELECT email_status
					FROM pg_employee
					WHERE staff_id = '$employeeId'
					AND status = 'A'";
					
					$result_sql10 = $dbg->query($sql10); 
					$dbg->next_record();
					$emailStatus1=$dbg->f('email_status');
				
					$employeeId=$dbc->f('empid');
					$employeeName = $dbc->f('name');
					$expertise = $dbc->f('description');
					$skypeId = $dbc->f('skype_id');
					$department = $dbc->f('department');
					$unitId = $dbc->f('unit_id');
					
					
					$theEmployeeIdArray[$no1] = $employeeId;
					$theEmployeeNameArray[$no1] = $employeeName;
					$theExpertiseArray[$no1] = $expertise;
					$theSkypeIdArray[$no1] = $skypeId;
					$theDepartmentArray[$no1] = $department;
					$theUnitIdArray[$no1] = $unitId;
					$theEmailStatusArray[$no1] = $emailStatus1;
					
					$no1++;
				}
			}while($dbc->next_record());
			$employeeIdArray = $theEmployeeIdArray;
			$employeeNameArray = $theEmployeeNameArray;
			$expertiseArray = $theExpertiseArray;
			$skypeIdArray = $theEkypeIdArray;
			$departmentArray = $theDepartmentArray;
			$unitIdArray = $theUnitIdArray;
			$emailStatusArray = $theEmailStatusArray;
			$row_cnt_sv = $no1;
		}
		else {
			$row_cnt_sv = 0;	
		}

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

$sql6 = "SELECT a.id, a.pg_employee_empid, a.role_status, f.description as srole,
		a.ref_supervisor_type_id, e.description AS stype, DATE_FORMAT(a.respondedby_date,'%d-%b-%Y') as respondedby_date, b.email_status 
		FROM pg_supervisor a
		LEFT JOIN ref_supervisor_type e ON (e.id = a.ref_supervisor_type_id)
		LEFT JOIN ref_role_status f ON (f.id = a.role_status)
		LEFT JOIN pg_employee b ON (b.staff_id = a.pg_employee_empid) 
		WHERE a.pg_student_matrix_no = '$matrixNo' 
		AND a.ref_supervisor_type_id in ('SV','CS','XS')
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
		<?
		$result6 = $dbe->query($sql6); 
		$dbe->next_record();	
		$row_cnt = mysql_num_rows($result6);		
		?>
		<br/>
		<fieldset>		
		<legend><strong>List of Assigned Supervisor/Co-Supervisor</strong> - <?=$row_cnt?> record(s) found</legend>	
		<? 
		if ($row_cnt >1)
		{?>
			<div id = "tabledisplay" style="overflow:auto; height:170px;">
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
					<th><strong>Role</strong></th>
					<th><strong>Role Status</strong></th>
					<th><strong>Reply Date</strong></th>					
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
					$briefBiodata=$dbe->f('brief_biodata');
					$emailStatus=$dbe->f('email_status');
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
					
					<input type="hidden" name="myEmployeeId1[]" size="15" id="employeeId1" value="<?=$employeeId1?>" >
					<?$myEmployeeId1[$no1]=$employeeId1;?>

					<?
					$sql4="SELECT name AS employee_name
					FROM new_employee
					WHERE empid = '$employeeId1'";
					
					$dbc->query($sql4);
					$row_personal=$dbc->fetchArray();
					$employeeName1=$row_personal['employee_name'];

					$sql4_2="SELECT skype_id, email
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
					
				<br/>(<?=$employeeId1;?>)
					  
					
					<br />
					<?
					if($emailStatus == 'Y')
					{		
						echo "Email Status: <span class=\"label-success label label-default\">Yes</span>";
					}
					else
					{
						echo "Email Status: <span class=\"label-default label label-danger\">No</span>";
					}
					?>
					<?$myEmployeeName1[$no1]=$employeeName1;?>
					<?$mySkypeId1[$no1]=$skypeId1;?>
					
											
					<?if ($briefBiodata != null || $briefBiodata != '') {?>
                            <a href="../supervisor/edit_supervisor_biodata.php?tid=<?=$thesisId;?>&sid=<?=$employeeId1;?>&pid=<?=$proposalId;?>&ename=<?=$employeeName1;?>&mn=<?=$matrixNo;?>&sname=<?=$sname;?>&id=<?=$supervisorId1;?>"><br/>
                      <img src="../images/edit.jpg" width="20" height="19" style="border:0px;" title="Supervisor has been assigned" />Read Brief Biodata </a><br/>
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
						?>					</td>
					
					<td><select name = "myDropDownSType1[]">						
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
					<?}?>					</td>
					
					<td><select name = "myDropDownSRole1[]">						
					<?
					$sql3_3 = "SELECT id, description
					FROM ref_role_status
					WHERE status='A'
					ORDER BY seq";

					$dbf->query($sql3_3); 
					$dbf->next_record();

					do {
						$roleStatus1=$dbf->f('id');	
						$roleStatusDesc1=$dbf->f('description');	
						if (strcmp($roleStatus1,$refRoleStatusId1)!=true) {
							?>
								<option value="<?=$roleStatus1?>" selected="selected"><?=$roleStatusDesc1?></option>
								<?$myDropDownSRole1[$no1]=$roleStatus1;?>							
								<?echo "myDropDownSRole1".$myDropDownSRole1[$no1];?>
							<?
						}
						else {?>
								<option value="<?=$roleStatus1?>"><?=$roleStatusDesc1?></option>
								<?$myDropDownSRole1[$no1]=$roleStatus1;?>
								<?echo "myDropDownSRole1".$myDropDownSRole1[$no1];?>
							<?}
					}while ($dbf->next_record());
					?>
					</select>					</td>
					
					<td><input type="text" name="respondedByDateEdit[]" size="15" id="respondedByDateEdit<?=$no1;?>" value="<?=$respondedByDate;?>" readonly=""/></td>
					<?	$jscript1 .= "\n" . '$( "#respondedByDateEdit' . $no1 . '" ).datepicker({
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
				
				$sqlr1 = "SELECT * FROM base_constant 
				WHERE const_category = 'EMAIL'
				AND const_term = 'EMAIL_SEN_TO_SUP'
				AND const_value = 'N'
				ORDER BY const_term DESC";
				$result_sqlr1 = $dbu->query($sqlr1);
				$trigger1 = mysql_num_rows($result_sqlr1);

	
				?>			
				<?$_SESSION['myEmployeeName1'] = $myEmployeeName1;?>
				<?$_SESSION['myEmployeeId1'] = $myEmployeeId1;?>
				<?$_POST['respondedByDateEdit'] = $respondedByDateEdit;?>
				<?$_SESSION['myDropDownSType1'] = $myDropDownSType1;?>
			</table>
			
			</div>
			<table>
				<tr>
					<td><span style="color:#FF0000">Note:</span></td>
				</tr>
				<tr>
					<td><?if ($row_cnt_sv_chk==0) {							
							?><label>1. Please assign Supervisor/Co-Supervisor to supervise this student.</label><br/><?
					}?>
				<?
					if($trigger1 >0)
					{
						if ($row_cnt_sv_chk==0){
						echo "2. <label style=\"color:#010000\">Auto email notification to Supervisor trigger by faculty's action is currently disabled by Admin.</label>";
						}
						else {
						echo "1. <label style=\"color:#010000\">Auto email notification to Supervisor trigger by faculty's action is currently disabled by Admin.</label>";
						}
					}
				?>
					
					</td>
				</tr>
			</table>
			<table>
				<tr>
					<td><input type="submit" name="btnEdit" value="Update" /></td>
					<td><input type="submit" name="btnDelete" value="Delete" onClick="return respConfirm()"  /></td>
					<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../thesis/senate_approval.php';"  /></td>					
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
		<legend><strong>Assign New Supervisor/Co-Supervisor</strong></legend>
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
				<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../thesis/senate_approval.php';" /></td>
				<td><span style="color:#FF0000"> Note:</span>If no entry is provided, it will search all.</td>
			</tr>
		</table>
		<br />

		<table>
				<tr>							
					<td>Searching Results:- <?=$row_cnt_sv?> record(s) found.</td>
				</tr>
		</table>
		<? 
		if ($row_cnt_sv > 10)
		{?>
			<div id = "tabledisplay" style="overflow:auto; height:250px;">
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
				<th><strong>Role</strong></th>
				<th><strong>Role Status</strong></th>		  
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
			if ($row_cnt_sv>0) {
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
				}?>				</td>
				<td><a width="20" height="19" style="border:0px;" title="<?=$departmentArray[$no];?>"><?=$unitIdArray[$no];?></a></td>
				
				<?
				$sql4_1="SELECT name AS employee_name
				FROM new_employee
				WHERE empid = '$employeeIdArray[$no]'";
				
				$dbc->query($sql4_1);
				$row_personal=$dbc->fetchArray();
				$employeeName=$row_personal['employee_name'];
				
				$sql4_2="SELECT skype_id, email
				FROM new_employee
				WHERE empid = '$employeeIdArray[$no]'";
				
				$dbc->query($sql4_2);
				$row_personal=$dbc->fetchArray();
				$skypeId=$row_personal['skype_id'];
				$empEmail=$row_personal['email'];
				
				?>
			  <td><label name="myEmployeeName[]" size="50" id="employeeName" ></label><?=$employeeName;?>
			    <input type="hidden" name="empName[]" id = "empName" value="<?=$employeeName?>" />
			    <input type = "hidden" name="empEmail[]" id="empEmail" value="<?=$empEmail?>"  />
				<input type = "hidden" name="empId[]" id="empId" value="<?=$employeeIdArray[$no]?>"  />
			    <br/>
			  
				(<label name="myEmployeeId[]" size="15" id="employeeId" ></label><?=$employeeIdArray[$no];?>)<br/>
				<?
					if($emailStatusArray[$no] == 'Y')
					{		
						echo "Email Status: <span class=\"label-success label label-default\">Yes</span>";
					}
					else
					{
						echo "Email Status: <span class=\"label-default label label-danger\">No</span>";
					}
					?>
				<label name="mySkypeId[]" id="skypeId" size="20"></label>
				<em></em></td>
				<?$myEmployeeName[$no]=$employeeName;?>
				<?$myEmployeeId[$no]=$employeeIdArray[$no];?>
				<?$mySkypeId[$no1]=$skypeId;?>
										
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
				$sql3_2 = "SELECT id, description
				FROM ref_supervisor_type
				WHERE status='A'
				AND type in ('SV')
				ORDER BY seq";

				$dbf->query($sql3_2); 
				$dbf->next_record();
				do {
					$stId=$dbf->f('id');	
					$stDescription=$dbf->f('description');	
					?><option value="<?=$stId?>"><?=$stDescription?></option><?$myDropDownSType[$no]=$stId;?><?echo "myDropDownSType".$myDropDownSType[$no];?>
					<?
				}while ($dbf->next_record());
				?>
				</select></td>
				<td><select name = "myDropDownSRole[]">
				<?
				$sql3_3 = "SELECT id, description
				FROM ref_role_status
				WHERE status='A'
				ORDER BY seq";

				$dbf->query($sql3_3); 
				$dbf->next_record();
				do {
					$roleStatus=$dbf->f('id');	
					$roleStatusDesc=$dbf->f('description');	
					if ($roleStatus=='SEC') {
						?>
							<option value="<?=$roleStatus?>" selected="selected"><?=$roleStatusDesc?></option>
							<?$myDropDownSRole[$no]=$roleStatus;?>							
							<?echo "myDropDownSRole".$myDropDownSRole[$no];?>
						<?
					}
					else {?>
							<option value="<?=$roleStatus?>"><?=$roleStatusDesc?></option>
							<?$myDropDownSRole[$no]=$roleStatus;?>
							<?echo "myDropDownSRole".$myDropDownSRole[$no];?>
						<?}
				}while ($dbf->next_record());
				?>
				</select></td>
				<?$respondedByDate = date('d-M-Y', strtotime($currentDate. ' + '.$parameterValue.' days'));
					?>	
					
					<td><input type="text" name="myRespondedByDate[]" size="15" id="respondedByDate<?=$no;?>" value="<?php echo $respondedByDate;?>"readonly=""/></td>
					<?	$jscript .= "\n" . '$( "#respondedByDate' . $no . '" ).datepicker({
													changeMonth: true,
													changeYear: true,
													yearRange: \'-100:+0\',
													dateFormat: \'dd-M-yy\'
												});';
					?>
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
		</div>
		<?
			$sqlr = "SELECT * FROM base_constant 
			WHERE const_category = 'EMAIL'
			AND const_term = 'EMAIL_SEN_TO_SUP'
			AND const_value = 'N'
			ORDER BY const_term DESC";
			$result_sqlr = $dbu->query($sqlr);
			$trigger = mysql_num_rows($result_sqlr);

		?>
		<table>
			<tr>
				<td><span style="color:#FF0000">Notes: </span><label><br/> 1. Please select the Supervisor from the list before click ASSIGN button.<br/>2. <span style="color:#FF0000">Role Status</span> - If more than 1 Supervisor has been assigned, ONLY one can be a primary supervisor.</label>
				<?
					if($trigger >0)
					{
						echo "<br>3. <label style=\"color:#010000\">Auto email notification to Supervisor trigger by faculty's action is currently disabled by Admin.</label>";
					}
				?>
				</td>
			</tr>
			<tr>
				<td><input type="submit" name="btnAssign" value="Assign" /></td>
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
				<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../thesis/senate_approval.php';" /></td>
			</tr>
		</table>
	</form>
	<script>
		<?=$jscript;?>
		<?=$jscript1;?>
	</script>
	</body>
</html>