<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: assign_reviewer_change.php
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

if(isset($_POST['btnEdit']) && ($_POST['btnEdit'] <> ""))
{
	//echo "btnEdit set ++++++++++++++++++++"."<br/>";
	$reviewerBox1=$_POST['reviewerBox1'];
	$myDropDownSType1='RV';
	$respondedByDate=$_POST['respondedByDate'];
	$msg = array();
	$empName = $_POST['empName1'];
	$empEmail = $_POST['empEmail1'];
	$empId = $_POST['empId1'];

	if (sizeof($_POST['reviewerBox1'])>0) {
		while (list ($key,$val) = @each ($reviewerBox1)) 
		{
			/*echo "reviewerBox1 key ".$key." "."val ".$val."<br/>";
			echo "reviewerId1 ".$val." ".$reviewerId1[$val]."<br/>";	
			echo "myEmployeeId1 ".$val." ".$myEmployeeId1[$val]."<br/>";	
			echo "myEmployeeName1 ".$val." ".$myEmployeeName1[$val]."<br/><br/>>";
			echo "myExpertise1 ".$val." ".$myExpertise1[$val]."<br/><br/>";
			echo "mySkypeId1 ".$val." ".$mySkypeId1[$val]."<br/><br/>";
			echo "myDropDownSType1 ".$val." ".$myDropDownSType1[$val]."<br/><br/>";*/
			
			$sql7 = "UPDATE pg_supervisor
			SET ref_supervisor_type_id='RV', extension_status = 'APP',
			respondedby_date = STR_TO_DATE('$respondedByDate[$val]','%d-%b-%Y'),
			modify_by='$user_id', modify_date='$curdatetime'
			WHERE id='$reviewerId1[$val]'
			AND pg_student_matrix_no = '$matrixNo'
			AND pg_thesis_id = '$thesisId'
			AND STATUS = 'A'";
			
			$dbg->query($sql7); 
			$dbg->next_record();
			//echo $sql7;
			
			$sql9 = "UPDATE pg_thesis
			SET reviewer_status = 'A'
			WHERE id ='$thesisId'
			AND student_matrix_no = '$matrixNo'";
						
			$dbg->query($sql9); 
			//$dbg->next_record();
			//echo $sql9;

			/************start Email***************/
			$sqlapprove = "SELECT DATE_FORMAT(a.report_date,'%d-%b-%Y') AS report_date_email, a.thesis_title, c.description 
			FROM pg_proposal a
			LEFT JOIN pg_proposal_approval b ON (b.id = a.pg_proposal_approval_id)
			LEFT JOIN ref_thesis_type c ON (c.id = a.thesis_type)
			WHERE a.pg_thesis_id = '$thesisId'
			AND a.pg_proposal_approval_id IS NULL";
				
			$result_sqlapprove = $dbb->query($sqlapprove); 
			$dbb->next_record();
			$approve_status = $dbb->f('approve_status');
			$submitDate = $dbb->f('report_date_email');
			$thesisTitle = $dbb->f('thesis_title');
			$thesisType = $dbb->f('description');
			$row_cnt3 = mysql_num_rows($result_sqlapprove);
			if($row_cnt3 == '1')
			{
				/****************start email**************/
				$sqlval = "SELECT const_value FROM base_constant
				WHERE const_term = 'EMAIL_FAC_TO_REV'";
					
				$result_sqlval = $dbe->query($sqlval); 
				$dbe->next_record();
				$valid = $dbe->f('const_value');
				if($valid == 'Y')
				{
					$sqlstatus = "SELECT email_status FROM pg_employee
					WHERE staff_id = '$empId[$val]'";
						
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
						
						$sqluser = "SELECT name,email FROM new_employee WHERE empid='$user_id'";
						$resultsqluser = $dbu->query($sqluser);
						$dbu->next_record();
						$username =$dbu->f('name');
						$useremail =$dbu->f('email');

						
						$sql3_3 = "SELECT id, description
						FROM ref_supervisor_type
						WHERE status='A'
						AND id = '$myDropDownSType1'";
						
						$dbf->query($sql3_3); 
						$dbf->next_record();
						$position =$dbf->f('description');
						
						$studdept = "SELECT a.program_code, b.programid,a.manage_by_whom
						FROM student_program a 
						LEFT JOIN program b ON (a.program_code =b.programid)
						WHERE matrix_no = '$matrixNo'";		

						$dbc->query($studdept); 
						$dbc->next_record();
						$manage =$dbc->f('manage_by_whom');

						$select = "SELECT const_value from base_constant where const_term = 'EMAIL_FACULTY'";
						$resultselect = $dbj->query($select);
						$dbj->next_record();
						$email =$dbj->f('const_value');
						
						$email2 = explode(" , ", $email);
						$email2[0]; /// email = test
						$email2[1]; /// email = GSM
						$email2[2]; /// email = SGS
						
						/*echo "<br>Data";
						echo "<br>Student Name: $studidname ($matrixNo)";
						echo "<br>Student Email: $studemail";
						echo "<br>Position: $position";
						echo "<br>Reviewer Name: $empName[$val] ($myEmployeeId[$val])";
						echo"<br>Submit Date: $submitDate";
						echo "<br>Thesis Id: $thesisId";
						echo "<br>Thesis Title: $thesisTitle";
						echo "<br>Thesis Type: $thesisType";
						echo "<br>Reviewer Email: $empEmail[$val]";
						echo "<br>Manage by Whom: $manage";
						echo "<br> username : $username <br> email: $useremail";*/
						include("../../../app/application/email/email_assign_reviewer.php");
							
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
						$resultstudname = $dbk->query($studname);
						$dbk->next_record();
						$studidname =$dbk->f('name');
						$studemail =$dbk->f('email');
						
						$sql3_3 = "SELECT id, description
						FROM ref_supervisor_type
						WHERE status='A'
						AND id = '$myDropDownSType1'";
						
						$dbf->query($sql3_3); 
						$dbf->next_record();
						$position =$dbf->f('description');
						
						/*echo "<br>Data";
						echo "<br>Student Name: $studidname ($matrixNo)";
						echo "<br>Student Email: $studemail";
						echo "<br>Position: $position";
						echo "<br>Reviewer Name: $empName[$val] ($myEmployeeId[$val])";
						echo"<br>Submit Date: $submitDate";
						echo "<br>Thesis Id: $thesisId";
						echo "<br>Thesis Title: $thesisTitle";
						echo "<br>Thesis Type: $thesisType";
						echo "<br>Reviewer Email: $empEmail[$val]";
						echo "<br>Manage by Whom: $manage";
						echo "<br> username : $username <br> email: $useremail";*/
						include("../../../app/application/inbox/submission/faculty_to_reviewer_inbox.php");

				}
				/****************end message**************/	
			}
		
		}
		$msg[] = "<div class=\"success\"><span>The selected Reviewer has been updated successfully.</span></div>";
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please select the Reviewer from the list before click UPDATE button.</span></div>";
	}
}

if(isset($_POST['btnDelete']) && ($_POST['btnDelete'] <> ""))
{
	//echo "btnDelete set ++++++++++++++++++++"."<br/>";
	$reviewerBox1=$_POST['reviewerBox1'];
	$loopnotify=$_POST['reviewerBox1'];
	$myDropDownSType1='RV';
	$acceptanceStatus=$_POST['acceptanceStatus'];
	$msg = array();
	$empName = $_POST['empName1'];
	$empEmail = $_POST['empEmail1'];
	$empId = $_POST['empId1'];

	if (sizeof($_POST['reviewerBox1'])>0) {
		while (list ($key,$val) = @each ($reviewerBox1)) 
		{
			/*echo "reviewerBox1 key ".$key." "."val ".$val."<br/>";
			echo "reviewerId1 ".$val." ".$reviewerId1[$val]."<br/>";	
			echo "myEmployeeId1 ".$val." ".$myEmployeeId1[$val]."<br/>";	
			echo "myEmployeeName1 ".$val." ".$myEmployeeName1[$val]."<br/><br/>>";
			echo "myExpertise1 ".$val." ".$myExpertise1[$val]."<br/><br/>";
			echo "mySkypeId1 ".$val." ".$mySkypeId1[$val]."<br/><br/>";
			echo "myDropDownSType1 ".$val." ".$myDropDownSType1[$val]."<br/><br/>";
			echo "acceptanceStatus ".$val." ".$acceptanceStatus[$val]."<br/><br/>";*/
			
			if ($acceptanceStatus == 'DNE') {
				$sql8 = "UPDATE pg_supervisor
				SET STATUS = 'D'
				WHERE id='$reviewerId1[$val]'
				AND ref_supervisor_type_id='RV'
				AND pg_student_matrix_no = '$matrixNo'
				AND pg_thesis_id = '$thesisId'
				AND STATUS = 'A'";
				
				$dbg->query($sql8); 
				//$dbg->next_record();
				//echo $sql8;
			} else {				
				$sql8 = "DELETE FROM pg_supervisor
				WHERE id='$reviewerId1[$val]'
				AND ref_supervisor_type_id='RV'
				AND pg_student_matrix_no = '$matrixNo'
				AND pg_thesis_id = '$thesisId'
				AND STATUS = 'A'";
				
				$dbg->query($sql8); 
				//$dbg->next_record();
				//echo $sql8;		
			}
		}
		
		$sql9 = "SELECT id
		FROM pg_supervisor
		WHERE pg_student_matrix_no = '$matrixNo'
		AND ref_supervisor_type_id='RV' 
		AND pg_thesis_id = '$thesisId'
		AND STATUS = 'A'";

		$result_sql9 = $dbg->query($sql9); 
		//$dbg->next_record();
		//echo $sql8;
		
		$row_cnt = mysql_num_rows($result_sql9);
		if ($row_cnt==0) {
			$sql10="UPDATE pg_thesis
			SET reviewer_status = 'U'
			WHERE id = '$thesisId'
			AND student_matrix_no = '$matrixNo'";
			
			//echo $sql5; exit();
			$dbe->query($sql10); 
		}
		$msg[] = "<div class=\"success\"><span>The selected Reviewer has been deleted from the list successfully.</span></div>";

		while (list ($key,$val) = @each ($loopnotify)) 
		{
			/************start Email***************/
			$sqlapprove = "SELECT DATE_FORMAT(a.report_date,'%d-%b-%Y') AS report_date_email, a.thesis_title, c.description 
			FROM pg_proposal a
			LEFT JOIN pg_proposal_approval b ON (b.id = a.pg_proposal_approval_id)
			LEFT JOIN ref_thesis_type c ON (c.id = a.thesis_type)
			WHERE a.pg_thesis_id = '$thesisId'
			AND a.pg_proposal_approval_id IS NULL";
				
			$result_sqlapprove = $dbb->query($sqlapprove); 
			$dbb->next_record();
			$approve_status = $dbb->f('approve_status');
			$submitDate = $dbb->f('report_date_email');
			$thesisTitle = $dbb->f('thesis_title');
			$thesisType = $dbb->f('description');
			$row_cnt3 = mysql_num_rows($result_sqlapprove);
			if($row_cnt3 == 1)
			{
				/****************start email**************/
				$sqlval = "SELECT const_value FROM base_constant
				WHERE const_term = 'EMAIL_FAC_TO_REV'";
					
				$result_sqlval = $dbe->query($sqlval); 
				$dbe->next_record();
				$valid = $dbe->f('const_value');
				if($valid == 'Y')
				{
					$sqlstatus = "SELECT email_status FROM pg_employee
					WHERE staff_id = '$empId[$val]'";
						
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
						
						$sqluser = "SELECT name,email FROM new_employee WHERE empid='$user_id'";
						$resultsqluser = $dbc->query($sqluser);
						$dbc->next_record();
						$username =$dbc->f('name');
						$useremail =$dbc->f('email');

						
						$sql3_3 = "SELECT id, description
						FROM ref_supervisor_type
						WHERE status='A'
						AND id = '$myDropDownSType1'";
						
						$dbf->query($sql3_3); 
						$dbf->next_record();
						$position =$dbf->f('description');
						
						$studdept = "SELECT a.program_code, b.programid,a.manage_by_whom
						FROM student_program a 
						LEFT JOIN program b ON (a.program_code =b.programid)
						WHERE matrix_no = '$matrixNo'";		

						$dbc->query($studdept); 
						$dbc->next_record();
						$manage =$dbc->f('manage_by_whom');

						$select = "SELECT const_value from base_constant where const_term = 'EMAIL_FACULTY'";
						$resultselect = $dbj->query($select);
						$dbj->next_record();
						$email =$dbj->f('const_value');
						
						$email2 = explode(" , ", $email);
						$email2[0]; /// email = test
						$email2[1]; /// email = GSM
						$email2[2]; /// email = SGS
						
						/*echo "<br>Data";
						echo "<br>Student Name: $studidname ($matrixNo)";
						echo "<br>Student Email: $studemail";
						echo "<br>Position: $position";
						echo "<br>Reviewer Name: $empName[$val] ($myEmployeeId[$val])";
						echo"<br>Submit Date: $submitDate";
						echo "<br>Thesis Id: $thesisId";
						echo "<br>Thesis Title: $thesisTitle";
						echo "<br>Thesis Type: $thesisType";
						echo "<br>Reviewer Email: $empEmail[$val]";
						echo "<br>Manage by Whom: $manage";
						echo "<br> username : $username <br> email: $useremail";*/
						include("../../../app/application/email/email_unassign_reviewer.php");
							
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
						$resultstudname = $dbk->query($studname);
						$dbk->next_record();
						$studidname =$dbk->f('name');
						$studemail =$dbk->f('email');
						
						$sql3_3 = "SELECT id, description
						FROM ref_supervisor_type
						WHERE status='A'
						AND id = '$myDropDownSType1'";
						
						$dbf->query($sql3_3); 
						$dbf->next_record();
						$position =$dbf->f('description');
						
						/*echo "<br>Data";
						echo "<br>Student Name: $studidname ($matrixNo)";
						echo "<br>Student Email: $studemail";
						echo "<br>Position: $position";
						echo "<br>Reviewer Name: $empName[$val] ($myEmployeeId[$val])";
						echo"<br>Submit Date: $submitDate";
						echo "<br>Thesis Id: $thesisId";
						echo "<br>Thesis Title: $thesisTitle";
						echo "<br>Thesis Type: $thesisType";
						echo "<br>Reviewer Email: $empEmail[$val]";
						echo "<br>Manage by Whom: $manage";
						echo "<br> username : $username <br> email: $useremail";*/
						include("../../../app/application/inbox/submission/unassign_reviewer_inbox.php");

				}
				/****************end message**************/	
			}		
		}
	
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please select the Reviewer from the list before click DELETE button.</span></div>";
	}
}

if(isset($_POST['btnSearch']) && ($_POST['btnSearch'] <> ""))
{
	$searchDeptId = $_POST['searchDeptId'];	
	$searchReviewerName = $_POST['searchReviewerName'];
	$searchExpertise = $_POST['searchExpertise'];
	
	if ($searchDeptId!="") 
	{
		$tmpSearchDeptId = " AND a.unit_id = '$searchDeptId'";
	}
	else 
	{
		$tmpSearchDeptId="";
	}
	if ($searchReviewerName!="") 
	{
		$tmpSearchReviewerName = " AND (a.name like '%$searchReviewerName%' OR a.empid like '%$searchReviewerName%')";
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
	
	
	$sql2 = "SELECT DISTINCT a.empid, a.name, b.description, a.email, a.unit_id, b.description AS department  
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
		.$tmpSearchReviewerName." "
		.$tmpSearchExpertise." "."
		AND c.level IN ('4','5')
		ORDER BY a.unit_id, a.name, a.empid";		
		
		$result_sql2 = $dbc->query($sql2); 
		$dbc->next_record();
		$row_cnt_rv = mysql_num_rows($result_sql2);
		
		$no1=0;
		$employeeIdArray = Array();
		$employeeNameArray = Array();
		$expertiseArray = Array();
		$emailIdArray = Array();
		$departmentArray = Array();
		$unitIdArray = Array();
		$emailStatusArray = Array();
		$theEmailStatusArray = Array();
		
		do {						
			$employeeId=$dbc->f('empid');	
			$employeeName=$dbc->f('name');
			$expertise=$dbc->f('description');
			$emailId=$dbc->f('email');
			$department=$dbc->f('department');
			$unitId=$dbc->f('unit_id');

			$sql10 = "SELECT email_status 
			FROM pg_employee 
			WHERE staff_id = '$employeeId'
			AND status = 'A'";
			
			$result_sql10 = $dbg->query($sql10); 
			$dbg->next_record();
			$emailStatus1=$dbg->f('email_status');
			
			$employeeIdArray[$no1]=$employeeId;
			$employeeNameArray[$no1]=$employeeName;
			$expertiseArray[$no1]=$expertise;
			$emailIdArray[$no1]=$emailId;
			$departmentArray[$no1]=$department;
			$unitIdArray[$no1]=$unitId;
			$theEmailStatusArray[$no1] = $emailStatus1;

			$no1++;
			
		}while($dbc->next_record());
		$emailStatusArray = $theEmailStatusArray;

}
else {
		$row_cnt_rv = 0;
}


$sql6 = "SELECT a.id, a.pg_employee_empid, 
		a.ref_supervisor_type_id, e.description AS stype, DATE_FORMAT(a.respondedby_date,'%d-%b-%Y') as respondedby_date,
		a.assigned_remarks, a.recipient_remarks, a.extension_status, a.acceptance_status, b.email_status
		FROM pg_supervisor a
		LEFT JOIN ref_supervisor_type e ON (e.id = a.ref_supervisor_type_id)
		LEFT JOIN pg_employee b ON (b.staff_id = a.pg_employee_empid) 
		WHERE a.pg_student_matrix_no = '$matrixNo' 
		AND a.ref_supervisor_type_id in ('RV')
		AND a.acceptance_status IS NULL
		AND a.pg_thesis_id = '$thesisId'
		AND a.status = 'A' 
		ORDER BY a.pg_employee_empid";

if(isset($_POST['btnAssign']) && !empty($_POST['btnAssign']) )
{
	//echo "btnAssign set ++++++++++++++++++++"."<br/>";
	$reviewerBox=$_POST['reviewerBox'];
	$myDropDownSType='RV';
	$myRespondedByDate=$_SESSION['myRespondedByDate'];
	$empName = $_POST['empName'];
	$empEmail = $_POST['empEmail'];
	$empId = $_POST['empId'];
	
	//$arrLength = count($reviewerBox);
	//echo "arrLength ".$arrLength." ";
		
	//if ($arrLenghth>0){
	
		while (list ($key,$val) = @each ($reviewerBox)) 
		{			
			$run_pg_supervisor_id = run_num('id','pg_supervisor'); // generate id (running number)
			
			/*echo "reviewerBox key".$key." ";
			echo "val".$val."<br/>";
			echo "myEmployeeId ".$val." ".$myEmployeeId[$val]."<br/>";	
			echo "myEmployeeName ".$val." ".$myEmployeeName[$val]."<br/><br/>>";
			echo "myExpertise ".$val." ".$myExpertise[$val]."<br/><br/>";
			echo "mySkypeId ".$val." ".$mySkypeId[$val]."<br/><br/>";
			echo "myDropDownSType ".$val." ".$myDropDownSType[$val]."<br/><br/>";*/
			
			$lock_tables="LOCK TABLES pg_supervisor WRITE"; //lock the table
			$db->query($lock_tables);	
			$sql4=" INSERT INTO pg_supervisor
			(id, pg_employee_empid, pg_student_matrix_no, pg_thesis_id, assigned_by, assigned_date,
			respondedby_date, ref_supervisor_type_id, status, insert_by, insert_date, modify_by, modify_date)
			VALUES ('$run_pg_supervisor_id', '$myEmployeeId[$val]', '$matrixNo', '$thesisId', '$userid', '$curdatetime',  
			STR_TO_DATE('".$_POST['myRespondedByDate'][$val]."','%d-%b-%Y'),'RV', 'A', '$userid', '$curdatetime', '$userid', '$curdatetime') ";
			
			$dbb->query($sql4); 
			$unlock_tables="UNLOCK TABLES"; //unlock the table;
			$db->query($unlock_tables);
			$sqlapprove = "SELECT DATE_FORMAT(a.report_date,'%d-%b-%Y') AS report_date_email, a.thesis_title, c.description 
			FROM pg_proposal a
			LEFT JOIN pg_proposal_approval b ON (b.id = a.pg_proposal_approval_id)
			LEFT JOIN ref_thesis_type c ON (c.id = a.thesis_type)
			WHERE a.pg_thesis_id = '$thesisId'
			AND a.pg_proposal_approval_id IS NULL";
				
			$result_sqlapprove = $dbb->query($sqlapprove); 
			$dbb->next_record();
			$approve_status = $dbb->f('approve_status');
			$submitDate = $dbb->f('report_date_email');
			$thesisTitle = $dbb->f('thesis_title');
			$thesisType = $dbb->f('description');
			$row_cnt3 = mysql_num_rows($result_sqlapprove);
			if($row_cnt3 == 1)
			{
				/****************start email**************/
				$sqlval = "SELECT const_value FROM base_constant
				WHERE const_term = 'EMAIL_FAC_TO_REV'";
					
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
						AND id = '$myDropDownSType'";
						
						$dbf->query($sql3_3); 
						$dbf->next_record();
						$position =$dbf->f('description');
						
						$studdept = "SELECT a.program_code, b.programid,a.manage_by_whom
						FROM student_program a 
						LEFT JOIN program b ON (a.program_code =b.programid)
						WHERE matrix_no = '$matrixNo'";		

						$dbc->query($studdept); 
						$dbc->next_record();
						$manage =$dbc->f('manage_by_whom');

						$select = "SELECT const_value from base_constant where const_term = 'EMAIL_FACULTY'";
						$resultselect = $dbj->query($select);
						$dbj->next_record();
						$email =$dbj->f('const_value');
						
						$email2 = explode(" , ", $email);
						$email2[0]; /// email = test
						$email2[1]; /// email = GSM
						$email2[2]; /// email = SGS
						
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
						include("../../../app/application/email/email_assign_reviewer.php");
							
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
						$resultstudname = $dbk->query($studname);
						$dbk->next_record();
						$studidname =$dbk->f('name');
						$studemail =$dbk->f('email');
						
						$sql3_3 = "SELECT id, description
						FROM ref_supervisor_type
						WHERE status='A'
						AND id = '$myDropDownSType'";
						
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
						include("../../../app/application/inbox/submission/faculty_to_reviewer_inbox.php");

				}
				/****************end message**************/	
			}
		
		
		}
		$sql5="UPDATE pg_thesis
			SET reviewer_status = 'A'
			WHERE id = '$thesisId'
			AND student_matrix_no = '$matrixNo'";
		
			$dbe->query($sql5); 

}
else {
	//echo "btnAssign not set ++++++++++++++++++++"."<br/>";
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
		<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>
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
		<legend><strong>List of Assigned Reviewer</strong></legend>
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
		<?$result6 = $dbe->query($sql6); 
		$dbe->next_record();		
		$row_cnt = mysql_num_rows($result6);
		?>
		<br/>
			<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="100%" class="thetable">
				<tr>
					<th><strong>Tick</strong></th>
					<th><strong>No.</strong></th>
					<th><strong>Dept</strong></th>
					<th><strong>Name</strong></th>  
					<th><strong>Qualification</strong></th>
					<th><strong>Field of Expertise</strong></th>
					<th><strong>Email ID</strong></th>
					<th><strong>Reply Date</strong></th>					
				</tr>    
				<?
				if ($row_cnt>0) {
				$no1=0;
				$myArrayNo=0;
				$jscript1 = "";
				do {						
					
					$reviewerId1=$dbe->f('id');	
					$employeeId1=$dbe->f('pg_employee_empid');	
					//$departmentId1=$dbe->f('dept_id');
					//$department1=$dbe->f('department');
					$expertise1=$dbe->f('expertise');
					$refSupervisorType1=$dbe->f('ref_supervisor_type_id');
					$respondedByDate=$dbe->f('respondedby_date');
					$assignedRemarks=$dbe->f('assigned_remarks');
					$recipientRemarks=$dbe->f('recipient_remarks');
					$extensionStatus=$dbe->f('extension_status');
					$acceptanceStatus=$dbe->f('acceptance_status');
					$emailStatus=$dbe->f('email_status');
					?>
					<tr>
						<input type="hidden" name="reviewerId1[]" value="<?=$reviewerId1;?>"/>	
						<input type="hidden" name="acceptanceStatus[]" value="<?=$acceptanceStatus;?>"/>					
						<?$acceptanceStatus[$no1]=$acceptanceStatus;?>
						<td align="center"><input name="reviewerBox1[]" type="checkbox" value="<?=$no1;?>"/></td>				
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
						<td><a><width="20" height="19" style="border:0px;" title="<?=$department1;?>"><?=$departmentId1;?></a></td>
						
						<?$myEmployeeId1[$no1]=$employeeId1;?>						
						<?
						$sql4 = "SELECT name AS employee_name, email
								FROM new_employee 
								WHERE empid = '$employeeId1'";
									
								$result4 = $dbc->query($sql4); 
								$dbc->next_record();
								$employeeName1=$dbc->f('employee_name');
								$superEmail1=$dbc->f('email');
					
						?>
						<td><label name="myEmployeeName1[]" size="50" id="employeeName1"></label><?=$employeeName1;?>
						<input type="hidden" name="empName1[]" id = "empName1" value="<?=$employeeName1?>" />
					<input type = "hidden" name="empEmail1[]" id="empEmail1" value="<?=$superEmail1?>"  />
					<input type = "hidden" name="empId1[]" id="empId1" value="<?=$employeeId1?>"  /><br/>(<?=$employeeId1;?>)
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

						<br/>
							<a href="../reviewer/assign_reviewer_remarks.php?rid=<?=$reviewerId1;?>&mn=<?=$matrixNo?>&tid=<?=$thesisId?>&pid=<?=$proposalId?>" title="Remarks">
							  <?=$pgThesisId;?>
							  <?if ($assignedRemarks == null || $assignedRemarks ==""){?>
										<img src="../images/red_edit.jpg" width="20" height="19" style="border:0px;" title="Faculty Remark is not yet provided" >Enter Faculty remarks	
									<?}
									else {
									?>
										<img src="../images/edit.jpg" width="20" height="19" style="border:0px;" title="Faculty Remark is provided" >Read remarks by Faculty	
									<?
									}?>
							<br/>
							<?if ($recipientRemarks == null || $recipientRemarks ==""){?>
										<img src="../images/red_edit.jpg" width="20" height="19" style="border:0px;" title="Faculty Remark is not yet provided" >Pending feedback by Reviewer
									<?}
									else {
									?>
										<img src="../images/edit.jpg" width="20" height="19" style="border:0px;" title="Faculty Remark is provided" >Read feedback provided by Reviewer
									<?
									}?></a></td>
						<?$myEmployeeName1[$no1]=$employeeName1;?>						
												
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
						
						<?
						$sql4 = "SELECT email
								FROM new_employee 
								WHERE empid = '$employeeId1'";
									
								$result4 = $dbc->query($sql4); 
								$dbc->next_record();
								$emailId1=$dbc->f('email');
					
						?>
						<td><label name="myEmail1[]" id="email1" size="20" ></label><?=$emailId1?></td>
						
						<td><input type="text" name="respondedByDate[]" size="15" id="respondedByDate<?=$no1;?>" value="<?=$respondedByDate;?>" readonly="true"/>
					<?	$jscript1 .= "\n" . '$( "#respondedByDate' . $no1 . '" ).datepicker({
													changeMonth: true,
													changeYear: true,
													yearRange: \'-100:+0\',
													dateFormat: \'dd-M-yy\'
												});';
						 
					?>
						<? if ($extensionStatus == 'REQ') {?>
								<br/><span style="color:#FF0000">Note:</span> This Reviewer has requested for additional time to provide feedback.<br/>You may change its <strong>Reply Date</strong> here.
						<?}?>
						</td>				
						
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
				<?$_POST['reviewerBox1'] = $reviewerBox1;?>
				<?$_POST['acceptanceStatus'] = $acceptanceStatus;?>
				
			</table>
			<br />
			<table>
				<tr>
					<td><input type="submit" name="btnEdit" value="Update" /></td>
					<td><input type="submit" name="btnDelete" value="Delete" onClick="return respConfirm()"/></td>
					<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../reviewer/assign_reviewer.php';" /></td>
				</tr>
			</table>
			
			<?
		}
		else {
			?>
			<table>
				<tr>
					<td>No record found!.</td>
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
		<legend><strong>Assign New Reviewer</strong></legend>
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
				<td>Reviewer Name / Staff ID</td>			
				<td><input name="searchReviewerName" type="text" id="searchReviewerName" size="40" value="<?=$searchReviewerName;?>" /></td>				
			</tr>
			<tr>
				<td>Field of Expertise / ID</td>			
				<td><input name="searchExpertise" type="text" id="searchExpertise" size="40" value="<?=$searchExpertise;?>" /></td>
				<?$_POST['searchDeptId'] = $searchDeptId;?>
				<?$_POST['searchReviewerName'] = $searchReviewerName;?>
				<?$_POST['searchExpertise'] = $searchExpertise;?>
				<td><input type="submit" name="btnSearch" id="btnSearch" align="center"  value="Search" /></td>
				<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../reviewer/assign_reviewer.php';" /></td>
				<td><span style="color:#FF0000"> Note:</span>  If no entry is provided, it will search all.</td>
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
				<th><strong>No.</strong></th>
				<th><strong>Dept</strong></th>
				<th><strong>Staff ID</strong></th>
				<th><strong>Name</strong></th>  
				<th><strong>Qualification</strong></th>
				<th><strong>Field of Expertise</strong></th>
				<th><strong>Email ID</strong></th>	
				<?
					$sql3_1 = "SELECT const_value
					FROM base_constant
					WHERE const_term = 'RV_RESPOND_DURATION'";

					$result3_1 = $dbb->query($sql3_1);
					$dbb->next_record();
					$parameterValue=$dbb->f('const_value');
					$currentDate = date('d-M-Y');					
					?>	
					
				<th><strong>Reply Date<br/></strong> within <?=$parameterValue?> day(s)</th>	
			</tr>    
			<?
			$supervisorArray = Array();
			$sql10 = "SELECT pg_employee_empid FROM pg_supervisor
					WHERE ref_supervisor_type_id in ('RV')
					AND pg_student_matrix_no = '$matrixNo'
					AND pg_thesis_id = '$thesisId'
					AND status = 'A'";
			
			$result_sql10 = $dbg->query($sql10); 
			$dbg->next_record();
			if (($row_cnt_rv>0) && ($row_cnt_rv!=mysql_num_rows($result_sql10))) {
				$no=0;
				$myArrayNo=0;
				$jscript = "";
				
				$no2=0;
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
					<td><input name="reviewerBox[]" type="checkbox" value="<?=$no;?>" /></td>
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
									
					<td><?=++$no2;?>.
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
					<td><a><width="20" height="19" style="border:0px;" title="<?=$department;?>"><?=$departmentId;?></a></td>
					
					<td><label name="myEmployeeId[]" size="15" id="employeeId" ></label><?=$employeeIdArray[$no];?>
					<input type="hidden" name="empName[]" id = "empName" value="<?=$employeeNameArray[$no]?>" />
					<input type = "hidden" name="empEmail[]" id="empEmail" value="<?=$emailIdArray[$no]?>"  />
					<input type = "hidden" name="empId[]" id="empId" value="<?=$employeeIdArray[$no]?>"  /></td>
					<?$myEmployeeId[$no]=$employeeIdArray[$no];?>
					
					<td><label name="myEmployeeName[]" size="50" id="employeeName" ></label><?=$employeeNameArray[$no];?>
					<br /><?
					if($emailStatusArray[$no] == 'Y')
					{		
						echo "Email Status: <span class=\"label-success label label-default\">Yes</span>";
					}
					else
					{
						echo "Email Status: <span class=\"label-default label label-danger\">No</span>";
					}
					?></td>
					<?$myEmployeeName[$no]=$employeeNameArray[$no];?>
											
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
						} while ($dbc->next_record());
						
				
						?>
						</td>
						<?$myExpertise1[$no1]=$expertise1;?>
						
					
					<td><label name="$areaExpertise"><?=$tmpAreaExpertise;?></label></td>
										
					
					<td><label name="emailId[]" id="emailId" size="20"></label><?=$emailIdArray[$no];?></td>
					
					<?$respondedByDate1 = date('d-M-Y', strtotime($currentDate. ' + '.$parameterValue.' days'));?>
					
					<td><input type="text" name="myRespondedByDate[]" size="15" id="respondedByDate1<?=$no;?>" value="<?php echo $respondedByDate1;?>" readonly="true"/></td>
					<?	$jscript .= "\n" . '$( "#respondedByDate1' . $no . '" ).datepicker({
													changeMonth: true,
													changeYear: true,
													yearRange: \'-100:+0\',
													dateFormat: \'dd-M-yy\'
												});';
						 
					?>							
				</tr>  
				<?
				
				$myArrayNo=$myArrayNo+1;
				}
			}

			
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
				<td><input type="submit" name="btnAssign" value="Assign" /><span style="color:#FF0000"> Note:</span> Please select the Reviewer from the list before click ASSIGN button.</td>
			</tr>
		</table>
		</fieldset>
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
				<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../reviewer/assign_reviewer.php';" /></td>
			</tr>
		</table>
	</form>
	<script>
		<?=$jscript;?>
		<?=$jscript1;?>
	</script>
	</body>
</html>