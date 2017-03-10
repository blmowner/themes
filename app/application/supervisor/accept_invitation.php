<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: accept_invitation.php
//
// Created by: Zuraimi
// Created Date: 24-Dec-2014
// Modified by: Zuraimi
// Modified Date: 05-Jan-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

session_start();
$userid=$_SESSION['user_id'];

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

if(isset($_POST['acceptBtn']) && ($_POST['acceptBtn'] <> ""))
{	
	$myCheckbox=$_POST['myCheckbox'];
	$mySupervisorTypeId=$_POST['mySupervisorTypeId'];
	$mySupervisorId=$_POST['supervisorId'];
	$myStudentMatrixNo=$_SESSION['myStudentMatrixNo'];
	$myThesisId=$_SESSION['myThesisId'];
	
	$curdatetime = date("Y-m-d H:i:s");
	$msg=array();
	if (sizeof($_POST['myCheckbox'])>0) {
		while (list ($key,$val) = @each ($myCheckbox)) 
		{	
			$studname = $_REQUEST['hidname'];
			$studid = $_REQUEST['hidstudno'];
			$staffrole = $_REQUEST['hidtype'];
			$thesisid = $_REQUEST['hidthesisid'];
			$thesistitle = $_REQUEST['hidtitle'];
			$senatedate = $_REQUEST['hidsenatedate'];
			$duedate = $_REQUEST['hidduedate'];
			
			$sql1=	"UPDATE pg_supervisor 
					SET	
					acceptance_date = '$curdatetime' , 
					acceptance_status = 'ACC' , 
					modify_by = '$user_id' , 
					modify_date = '$curdatetime' 	
					WHERE id = '$mySupervisorId[$val]'
					AND pg_employee_empid = '$user_id'  
					AND pg_student_matrix_no = '$myStudentMatrixNo[$val]'  
					AND pg_thesis_id = '$myThesisId[$val]'
					AND status = 'A'";//acceptance_remarks = '$acceptanceRemark', 


			$result1 = $dba->query($sql1); 	
			$dba->next_record();
						
			$selectfrom = "SELECT const_value
			FROM base_constant WHERE const_term = 'EMAIL_ADMIN'";
			
			$resultfrom = $db->query($selectfrom);
			$db->next_record();
			$fromadmin =$db->f('const_value');
			
			$sqlfaculty = "SELECT const_value
			FROM base_constant WHERE const_term = 'EMAIL_FACULTY'";
			$resultfaculty = $dbe->query($sqlfaculty);
			$dbe->next_record();
			$facultyemail =$dbe->f('const_value');
			
			$emailfaculty = explode(" , ", $facultyemail);
			$emailfaculty[0]; /// email = test
			$emailfaculty[1]; /// email = GSM
			$emailfaculty[2]; /// email = SGS
			
			$studdept = "SELECT a.program_code, b.programid,b.dept_unit
						FROM student_program a 
						LEFT JOIN program b ON (a.program_code =b.programid)
						WHERE matrix_no = '$myStudentMatrixNo[$val]'";		
			$resultstuddept = $dbn->query($studdept); //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>Student dept unit
			$dbn->next_record();
			$studDept = $dbn->f('dept_unit');
		
			$sqlfacid = "SELECT const_value FROM base_constant WHERE const_term = 'FACULTY_STAFF_ID'";
			$resultsqlfalid = $dbd->query($sqlfacid);
			$dbd->next_record();
			$facid = $dbd->f('const_value');

			$sqlname = "SELECT a.name,b.title 
			FROM new_employee a
			LEFT JOIN lookup_gelaran b ON(b.id = a.title)
			WHERE a.empid = '$facid'";
			$resultsqlname = $dbl->query($sqlname);
			$dbl->next_record();
			$faculty =$dbl->f('name');
			$title =$dbl->f('title');
			
			$sql1 = "SELECT acceptance_remarks
			FROM pg_supervisor 
			WHERE pg_employee_empid = '$user_id' 
			AND pg_thesis_id = '$myThesisId[$val]'";

			$result1 = $db->query($sql1);
			$db->next_record();
			$remarks = $db->f('acceptance_remarks');					


			$sql2 = "SELECT acceptance_remarks FROM pg_supervisor
			WHERE pg_employee_empid = '$user_id' 
			AND pg_thesis_id = '$thesisid'";

			$result2 = $dba->query($sql2);
			$dba->next_record();
			$superremarks =$dba->f('acceptance_remarks');
			
			$sql3 = "SELECT a.name,a.email,b.title 
			FROM new_employee a
			LEFT JOIN lookup_gelaran b ON(b.id = a.title)
			WHERE a.empid = '$user_id'";

			$result3 = $dbc->query($sql3);
			$dbc->next_record();
			$namesuper =$dbc->f('name');
			$superemail =$dbc->f('email');
			$supertitle =$dbc->f('title');
			
			$sql4 = "SELECT description FROM ref_supervisor_type
			WHERE id = '$mySupervisorTypeId[$val]'";

			$result4 = $db->query($sql4);
			$db->next_record();
			$superrole[] =$db->f('description');

			$sqldate = "SELECT DATE_FORMAT(report_date,'%d-%b-%Y') as report_date FROM pg_proposal 
			WHERE pg_thesis_id = '$thesisid[$val]'
			AND verified_status = 'INP'
			AND STATUS = 'OPN'
			AND archived_status = 'ARC'";

			$resultdate = $dbj->query($sqldate);
			$dbj->next_record();
			$date =$dbj->f('report_date');
			
			$sqlverified = "SELECT const_value FROM base_constant WHERE const_term = 'EMAIL_SUP_TO_FAC'";
			$resultverified = $dbu->query($sqlverified);
			$dbu->next_record();
			$constvalue =$dbu->f('const_value');
			if ($constvalue == 'Y')
			{
				//$curdatetime1 = date("d-m-Y");
				include("../../../app/application/email/email_supervisor_accept.php");
			}
			else
			{
			}
			//>>>>>>>>>>>>>>>>>>>>>>message notification<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
			$sqlval = "SELECT const_value
			FROM base_constant WHERE const_term = 'MESSAGE_SUP_TO_FAC'";
			$resultvalidate = $dbf->query($sqlval);
			$dbf->next_record();
			$valid =$dbf->f('const_value');
			if($valid == 'Y')
			{
				include("../../../app/application/inbox/submission/supervisor_to_faculty_inbox.php"); //>>>>>>>>>>>>>>>>>>>>>>message notification
			
			}
			//>>>>>>>>>>>>>>>>>>>>>>message notification end <<<<<<<<<<<<<<<<<<<<<<<<<<<<<

		
		}
		$msg[] = "<div class=\"success\"><span>You have been assigned as Supervisor/Co-Supervisor to the selected student successfully.</span></div>";		
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please select student first before click ACCEPT INVITATION button.</span></div>";
	}
}

if(isset($_POST['rejectBtn']) && ($_POST['rejectBtn'] <> ""))
{	
	$myCheckbox=$_POST['myCheckbox'];
	$mySupervisorId=$_SESSION['mySupervisorId'];
	$mySupervisorId=$_POST['supervisorId'];
	$myStudentMatrixNo=$_SESSION['myStudentMatrixNo'];
	$myThesisId=$_SESSION['myThesisId'];
	$mySupervisorTypeId=$_POST['mySupervisorTypeId'];
	$remark = $_REQUEST['acceptanceRemarks'];
	
	$curdatetime = date("Y-m-d H:i:s");

	$msg=array();
	if (sizeof($_POST['myCheckbox'])>0) {
		while (list ($key,$val) = @each ($myCheckbox)) 
		{
			$studname = $_REQUEST['hidname'];
			$studid = $_REQUEST['hidstudno'];
			$staffrole = $_REQUEST['hidtype'];
			$thesisid = $_REQUEST['hidthesisid'];
			$thesistitle = $_REQUEST['hidtitle'];
			$senatedate = $_REQUEST['hidsenatedate'];
			$duedate = $_REQUEST['hidduedate'];
			

			$sql2=	"UPDATE pg_supervisor 
			SET	
			acceptance_date = '$curdatetime' , 
			acceptance_status = 'REJ' , 
			modify_by = '$user_id' , 
			modify_date = '$curdatetime' 	
			WHERE id = '$mySupervisorId[$val]'
			AND pg_employee_empid = '$user_id'  
			AND pg_student_matrix_no = '$myStudentMatrixNo[$val]'  
			AND pg_thesis_id = '$myThesisId[$val]'
			AND status = 'A'";//acceptance_remarks = '$acceptanceRemark', 

			
			$result2 = $dba->query($sql2); 	
			$dba->next_record();

			/*echo "mySupervisorId [".$val."] ".$mySupervisorId[$val]."<br/>";
			echo "myStudentMatrix [".$val."] ".$myStudentMatrixNo[$val]."<br/>";
			echo "myStudentName [".$val."] ".$myStudentName[$val]."<br/>";
			echo "mySupervisorType [".$val."] ".$mySupervisorType[$val]."<br/>";
			echo "myThesisId [".$val."] ".$myThesisId[$val]."<br/>";
			echo "myThesisTitle [".$val."] ".$myThesisTitle[$val]."<br/>";
			echo "myRespondedbyDate [".$val."] ".$myRespondedbyDate[$val]."<br/>";*/
			
			
			$selectfrom = "SELECT const_value
			FROM base_constant WHERE const_term = 'EMAIL_ADMIN'";
			
			$resultfrom = $db->query($selectfrom);
			$db->next_record();
			$fromadmin =$db->f('const_value');
			
			$sqlfaculty = "SELECT const_value
			FROM base_constant WHERE const_term = 'EMAIL_FACULTY'";
			$resultfaculty = $dbe->query($sqlfaculty);
			$dbe->next_record();
			$facultyemail =$dbe->f('const_value');
						
			$emailfaculty = explode(" , ", $facultyemail);
			$emailfaculty[0]; /// email = test
			$emailfaculty[1]; /// email = GSM
			$emailfaculty[2]; /// email = SGS
		
			$sqlfacid = "SELECT const_value FROM base_constant WHERE const_term = 'FACULTY_STAFF_ID'";
			$resultsqlfalid = $dbd->query($sqlfacid);
			$dbd->next_record();
			$facid = $dbd->f('const_value');

			$sqlname = "SELECT a.name,b.title 
			FROM new_employee a
			LEFT JOIN lookup_gelaran b ON(b.id = a.title)
			WHERE a.empid = '$facid'";
			$resultsqlname = $dbl->query($sqlname);
			$dbl->next_record();
			$faculty =$dbl->f('name');
			$title =$dbl->f('title');
			
			$studdept = "SELECT a.program_code, b.programid,b.dept_unit
						FROM student_program a 
						LEFT JOIN program b ON (a.program_code =b.programid)
						WHERE matrix_no = '$myStudentMatrixNo[$val]'";		
			$resultstuddept = $dbn->query($studdept); //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>Student dept unit
			$dbn->next_record();
			$studDept = $dbn->f('dept_unit');

			
			$sql1 = "SELECT acceptance_remarks
			FROM pg_supervisor 
			WHERE pg_employee_empid = '$user_id' 
			AND pg_thesis_id = '$myThesisId[$val]'
			AND pg_student_matrix_no = '$myStudentMatrixNo[$val]'
			AND id = '$mySupervisorId[$val]'";

			$result1 = $dba->query($sql1);
			$dba->next_record();
			$remarks = $dba->f('acceptance_remarks');					
			
			$sql3 = "SELECT a.name,a.email,b.title 
			FROM new_employee a
			LEFT JOIN lookup_gelaran b ON(b.id = a.title)
			WHERE a.empid = '$user_id'";

			$result3 = $dbc->query($sql3);
			$dbc->next_record();
			$namesuper =$dbc->f('name');
			$superemail =$dbc->f('email');
			$supertitle =$dbc->f('title');
			
			$sql4 = "SELECT description FROM ref_supervisor_type
			WHERE id = '$mySupervisorTypeId[$val]'";

			$result4 = $db->query($sql4);
			$db->next_record();
			$superrole[] =$db->f('description');

			$sqldate = "SELECT DATE_FORMAT(report_date,'%d-%b-%Y') as report_date FROM pg_proposal 
			WHERE pg_thesis_id = '$thesisid[$val]'
			AND verified_status = 'INP'
			AND STATUS = 'OPN'
			AND archived_status = 'ARC'";

			$resultdate = $dbj->query($sqldate);
			$dbj->next_record();
			$date =$dbj->f('report_date');
			
			$sqlverified = "SELECT const_value FROM base_constant WHERE const_term = 'EMAIL_SUP_TO_FAC'";
			$resultverified = $dbu->query($sqlverified);
			$dbu->next_record();
			$constvalue =$dbu->f('const_value');
			if ($constvalue == 'Y')
			{
				include("../../../app/application/email/email_supervisor_reject.php");
			}
			else
			{
			
			}
			
			//>>>>>>>>>>>>>>>>>>>>>>message notification<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
			$sqlval = "SELECT const_value
			FROM base_constant WHERE const_term = 'MESSAGE_SUP_TO_FAC'";
			$resultvalidate = $dbf->query($sqlval);
			$dbf->next_record();
			$valid =$dbf->f('const_value');
			if($valid == 'Y')
			{
				include("../../../app/application/inbox/submission/supervisor_to_faculty_inbox_rej.php"); //>>>>>>>>>>>>>>>>>>>>>>message notification
			
			}
			//>>>>>>>>>>>>>>>>>>>>>>message notification end <<<<<<<<<<<<<<<<<<<<<<<<<<<<<
					
			

		}
		$msg[] = "<div class=\"success\"><span>You have been unassigned as Supervisor/Co-Supervisor from the selected student accordingly.</span></div>";		
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please select student first before click NOT ACCEPT INVITATION button.</span></div>";
	}
	
}

if(isset($_POST['btnSearch']) && ($_POST['btnSearch'] <> "")) {
	
	$searchThesisId = $_POST['searchThesisId'];
	$searchStudent = $_POST['searchStudent'];
	
	if ($searchThesisId!="") 
	{
		$tmpSearchThesisId = " AND e.pg_thesis_id = '$searchThesisId'";
	}
	else 
	{
		$tmpSearchThesisId="";
	}
	if ($searchStudent!="") 
	{
		$tmpSearchStudent = " AND d.student_matrix_no = '$searchStudent'";
	}
	else 
	{
		$tmpSearchStudent="";
	}
	
	$sql1 = "SELECT a.id, a.assigned_by, DATE_FORMAT(a.assigned_date,'%d-%b-%Y') AS assigned_date, 
		DATE_FORMAT(a.acceptance_date,'%d-%b-%Y') AS acceptance_date, a.acceptance_status, a.acceptance_remarks,
		DATE_FORMAT(a.respondedby_date,'%d-%b-%Y') AS respondedby_date, 
		DATE_FORMAT(f.endorsed_date,'%d-%b-%Y') AS senate_mtg_date, a.pg_student_matrix_no, 
		a.ref_supervisor_type_id, b.description AS supervisor_type, 
		e.pg_thesis_id, e.thesis_title, e.id as proposal_id
		FROM pg_supervisor a 
		LEFT JOIN ref_supervisor_type b ON (b.id = a.ref_supervisor_type_id) 
		LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id) 
		LEFT JOIN pg_proposal e ON (e.pg_thesis_id = d.id) 
		LEFT JOIN pg_proposal_approval f ON (f.id = e.pg_proposal_approval_id)
		WHERE a.ref_supervisor_type_id in ('SV','CS','XS')
		AND a.status = 'A'"
		.$tmpSearchThesisId." "
		.$tmpSearchStudent." "." 		
		AND a.pg_employee_empid = '$userid' 
		AND d.status = 'INP' 
		AND e.verified_status in ('APP','AWC')
		AND e.status in ('APP','APC')";

		//echo "sql1 ".$sql1;
		$result1 = $db->query($sql1);
		$db->next_record();
		
		$supervisorIdArray = Array();	
		$studentMatrixNoArray = Array();	
		$supervisorTypeIdArray = Array();	
		$supervisorType = Array();	
		$thesisIdArray = Array();	
		$thesisTitleArray = Array();					
		$respondedByDateArray = Array();	
		$senateMtgDateArray = Array();	
		$proposalIdArray = Array();	
		$acceptanceRemarksArray = Array();	
		$acceptanceStatusArray = Array();	
		$acceptanceDateArray = Array();	
		
		$no1=0;
		$no2=0;
		do {
			$supervisorIdArray[$no1]=$db->f('id');	
			$studentMatrixNoArray[$no1]=$db->f('pg_student_matrix_no');
			$supervisorTypeIdArray[$no1]=$db->f('ref_supervisor_type_id');
			$supervisorTypeArray[$no1]=$db->f('supervisor_type');
			$thesisIdArray[$no1]=$db->f('pg_thesis_id');
			$thesisTitleArray[$no1]=$db->f('thesis_title');						
			$respondedByDateArray[$no1]=$db->f('respondedby_date');
			$senateMtgDateArray[$no1]=$db->f('senate_mtg_date');
			$proposalIdArray[$no1]=$db->f('proposal_id');
			$acceptanceRemarksArray[$no1]=$db->f('acceptance_remarks');
			$acceptanceStatusArray[$no1]=$db->f('acceptance_status');
			$acceptanceDateArray[$no1]=$db->f('acceptance_date');						
			$no1++;
			
		} while ($db->next_record());
		
		$studentNameArray = Array();
		for ($i=0; $i<$no1; $i++){
			$sql9 = "SELECT name
				FROM student
				WHERE matrix_no = '$studentMatrixNoArray[$i]'
				AND name like '%$searchStudentName%'";
			if (substr($studentMatrixNoArray[$i],0,2) != '07') { 
				$dbConnStudent= $dbc; 
			} 
			else { 
				$dbConnStudent=$dbc1; 
			}
			$result9 = $dbConnStudent->query($sql9); 
			$dbConnStudent->next_record();
			if (mysql_num_rows($result9)>0) {
				$studentNameArray[$no2] = $dbConnStudent->f('name');
				$supervisorIdArray[$no2] = $supervisorIdArray[$i];	
				$studentMatrixNoArray[$no2] = $studentMatrixNoArray[$i];
				$supervisorTypeIdArray[$no2] = $supervisorTypeIdArray[$i];
				$supervisorTypeArray[$no2] = $supervisorTypeArray[$i];
				$thesisIdArray[$no2] = $thesisIdArray[$i];
				$thesisTitleArray[$no2] = $thesisTitleArray[$i];					
				$respondedByDateArray[$no2] = $respondedByDateArray[$i];
				$senateMtgDateArray[$no2] = $senateMtgDateArray[$i];
				$proposalIdArray[$no2] = $proposalIdArray[$i];
				$acceptanceRemarksArray[$no2] = $acceptanceRemarksArray[$i];
				$acceptanceStatusArray[$no2] = $acceptanceStatusArray[$i];
				$acceptanceDateArray[$no2] = $acceptanceDateArray[$i];
				$no2++;
			}
		}
		$row_cnt = $no2;

}
else 
{
	$sql1 = "SELECT a.id, a.assigned_by, DATE_FORMAT(a.assigned_date,'%d-%b-%Y') AS assigned_date, 
		DATE_FORMAT(a.acceptance_date,'%d-%b-%Y') AS acceptance_date, a.acceptance_status, a.acceptance_remarks,
		DATE_FORMAT(a.respondedby_date,'%d-%b-%Y') AS respondedby_date, 
		DATE_FORMAT(f.endorsed_date,'%d-%b-%Y') AS senate_mtg_date, a.pg_student_matrix_no, 
		a.ref_supervisor_type_id, b.description AS supervisor_type, 
		e.pg_thesis_id, e.thesis_title, e.id as proposal_id
		FROM pg_supervisor a 
		LEFT JOIN ref_supervisor_type b ON (b.id = a.ref_supervisor_type_id) 
		LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id) 
		LEFT JOIN pg_proposal e ON (e.pg_thesis_id = d.id) 
		LEFT JOIN pg_proposal_approval f ON (f.id = e.pg_proposal_approval_id)
		WHERE a.ref_supervisor_type_id in ('SV','CS','XS')
		AND a.status = 'A'
		AND a.pg_employee_empid = '$userid' 
		AND d.status = 'INP' 
		AND e.verified_status in ('APP','AWC')
		AND e.status in ('APP','APC')";
			
		$result2 = $db->query($sql1); 
		$db->next_record();
		
		$supervisorIdArray = Array();	
		$studentMatrixNoArray = Array();	
		$supervisorTypeIdArray = Array();	
		$supervisorType = Array();	
		$thesisIdArray = Array();	
		$thesisTitleArray = Array();					
		$respondedByDateArray = Array();	
		$senateMtgDateArray = Array();	
		$proposalIdArray = Array();	
		$acceptanceRemarksArray = Array();	
		$acceptanceStatusArray = Array();	
		$acceptanceDateArray = Array();	
		
		$no1=0;
		$no2=0;
		do {
			$supervisorIdArray[$no1] = $db->f('id');	
			$studentMatrixNoArray[$no1] = $db->f('pg_student_matrix_no');
			$supervisorTypeIdArray[$no1] = $db->f('ref_supervisor_type_id');
			$supervisorTypeArray[$no1] = $db->f('supervisor_type');
			$thesisIdArray[$no1] = $db->f('pg_thesis_id');
			$thesisTitleArray[$no1] = $db->f('thesis_title');						
			$respondedByDateArray[$no1] = $db->f('respondedby_date');
			$senateMtgDateArray[$no1] = $db->f('senate_mtg_date');
			$proposalIdArray[$no1] = $db->f('proposal_id');
			$acceptanceRemarksArray[$no1] = $db->f('acceptance_remarks');
			$acceptanceStatusArray[$no1] = $db->f('acceptance_status');
			$acceptanceDateArray[$no1] = $db->f('acceptance_date');
			$no1++;
			
		} while ($db->next_record());
		
		$studentNameArray = Array();
		for ($i=0; $i<$no1; $i++){
			$sql9 = "SELECT name
				FROM student
				WHERE matrix_no = '$studentMatrixNoArray[$i]'
				AND name like '%$searchStudentNameArray[$i]%'";
			if (substr($studentMatrixNoArray[$i],0,2) != '07') { 
				$dbConnStudent= $dbc; 
			} 
			else { 
				$dbConnStudent=$dbc1; 
			}
			$result9 = $dbConnStudent->query($sql9); 
			$dbConnStudent->next_record();
			if (mysql_num_rows($result9)>0) {
				$studentNameArray[$no2] = $dbConnStudent->f('name');
				$supervisorIdArray[$no2] = $supervisorIdArray[$i];
				$studentMatrixNoArray[$no2] = $studentMatrixNoArray[$i];
				$supervisorTypeIdArray[$no2] = $supervisorTypeIdArray[$i];
				$supervisorTypeArray[$no2] = $supervisorTypeArray[$i];
				$thesisIdArray[$no2] = $thesisIdArray[$i];
				$thesisTitleArray[$no2] = $thesisTitleArray[$i];			
				$respondedByDateArray[$no2] = $respondedByDateArray[$i];
				$senateMtgDateArray[$no2] = $senateMtgDateArray[$i];
				$proposalIdArray[$no2] = $proposalIdArray[$i];
				$acceptanceRemarksArray[$no2] = $acceptanceRemarksArray[$i];
				$acceptanceStatusArray[$no2] = $acceptanceStatusArray[$i];
				$acceptanceDateArray[$no2] = $acceptanceDateArray[$i];
				$no2++;
			}
		}
		$row_cnt = $no2;
}

if(isset($_POST['btnSearchByName']) && ($_POST['btnSearchByName'] <> "")) {
	
	$searchStudentName = $_POST['searchStudentName'];
	
	$sql8 = "SELECT a.id, a.assigned_by, DATE_FORMAT(a.assigned_date,'%d-%b-%Y') AS assigned_date, 
		DATE_FORMAT(a.acceptance_date,'%d-%b-%Y') AS acceptance_date, a.acceptance_status, a.acceptance_remarks,
		DATE_FORMAT(a.respondedby_date,'%d-%b-%Y') AS respondedby_date, 
		DATE_FORMAT(f.endorsed_date,'%d-%b-%Y') AS senate_mtg_date, a.pg_student_matrix_no, 
		a.ref_supervisor_type_id, b.description AS supervisor_type, 
		e.pg_thesis_id, e.thesis_title, e.id as proposal_id
		FROM pg_supervisor a 
		LEFT JOIN ref_supervisor_type b ON (b.id = a.ref_supervisor_type_id) 
		LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id) 
		LEFT JOIN pg_proposal e ON (e.pg_thesis_id = d.id) 
		LEFT JOIN pg_proposal_approval f ON (f.id = e.pg_proposal_approval_id)
		WHERE a.ref_supervisor_type_id in ('SV','CS','XS')
		AND a.status = 'A'
		AND a.pg_employee_empid = '$userid' 
		AND d.status = 'INP' 
		AND e.verified_status in ('APP','AWC')
		AND e.status in ('APP','APC')";		
		
		$result8 = $db->query($sql8); 
		$db->next_record();
		
		$supervisorIdArray = Array();	
		$studentMatrixNoArray = Array();	
		$supervisorTypeIdArray = Array();	
		$supervisorType = Array();	
		$thesisIdArray = Array();	
		$thesisTitleArray = Array();					
		$respondedByDateArray = Array();	
		$senateMtgDateArray = Array();	
		$proposalIdArray = Array();	
		$acceptanceRemarksArray = Array();	
		$acceptanceStatusArray = Array();	
		$acceptanceDateArray = Array();
		
		$no1=0;
		$no2=0;
		do {
			$supervisorIdArray[$no1] = $db->f('id');	
			$studentMatrixNoArray[$no1] = $db->f('pg_student_matrix_no');
			$supervisorTypeIdArray[$no1] = $db->f('ref_supervisor_type_id');
			$supervisorTypeArray[$no1] = $db->f('supervisor_type');
			$thesisIdArray[$no1] = $db->f('pg_thesis_id');
			$thesisTitleArray[$no1] = $db->f('thesis_title');						
			$respondedByDateArray[$no1] = $db->f('respondedby_date');
			$senateMtgDateArray[$no1] = $db->f('senate_mtg_date');
			$proposalIdArray[$no1] = $db->f('proposal_id');
			$acceptanceRemarksArray[$no1] = $db->f('acceptance_remarks');
			$acceptanceStatusArray[$no1] = $db->f('acceptance_status');
			$acceptanceDateArray[$no1] = $db->f('acceptance_date');
			$no1++;
			
		} while ($db->next_record());
		
		$studentNameArray = Array();
		for ($i=0; $i<$no1; $i++){
			$sql9 = "SELECT name
				FROM student
				WHERE matrix_no = '$studentMatrixNoArray[$i]'
				AND name like '%$searchStudentName%'";
			if (substr($studentMatrixNoArray[$i],0,2) != '07') { 
				$dbConnStudent= $dbc; 
			} 
			else { 
				$dbConnStudent=$dbc1; 
			}
			$result9 = $dbConnStudent->query($sql9); 
			$dbConnStudent->next_record();
			if (mysql_num_rows($result9)>0) {
				$studentNameArray[$no2] = $dbConnStudent->f('name');
				$supervisorIdArray[$no2] = $supervisorIdArray[$i];
				$studentMatrixNoArray[$no2] = $studentMatrixNoArray[$i];
				$supervisorTypeIdArray[$no2] = $supervisorTypeIdArray[$i];
				$supervisorTypeArray[$no2] = $supervisorTypeArray[$i];
				$thesisIdArray[$no2] = $thesisIdArray[$i];
				$thesisTitleArray[$no2] = $thesisTitleArray[$i];			
				$respondedByDateArray[$no2] = $respondedByDateArray[$i];
				$senateMtgDateArray[$no2] = $senateMtgDateArray[$i];
				$proposalIdArray[$no2] = $proposalIdArray[$i];
				$acceptanceRemarksArray[$no2] = $acceptanceRemarksArray[$i];
				$acceptanceStatusArray[$no2] = $acceptanceStatusArray[$i];
				$acceptanceDateArray[$no2] = $acceptanceDateArray[$i];
				$no2++;
			}
		}
		$row_cnt = $no2;
		
		
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
	    <style type="text/css">
<!--
.style1 {
	color: #FF0000;
	font-weight: bold;
}
-->
        </style>
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
		<form id="form2" name="form2" method="post" enctype="multipart/form-data">

			
			
			<fieldset>
			<legend><strong>List of Thesis</strong></legend>
				<table>
					<tr>							
						<td><strong>Please enter searching criteria below:-</strong></td>
					</tr>
				</table>
				<br/>							
				<table>
					<tr>						
						<td>Thesis/Project ID</td>
						<td>:</td>
						<td><input type="text" name="searchThesisId" size="15" id="searchThesisId" value="<?=$searchThesisId;?>"/></td>
					</tr>
					<tr>
						<td>Matrix No</td>
						<td>:</td>
						<td><input type="text" name="searchStudent" size="15" id="searchStudent" value="<?=$searchStudent;?>"/></td>
						<td><input type="submit" name="btnSearch" value="Search" /></td>
					</tr>
					<tr>
						<td>Student Name</td>
						<td>:</td>
						<td><input type="text" name="searchStudentName" size="30" id="searchStudentName" value="<?=$searchStudentName;?>"/></td>
						<td><input type="submit" name="btnSearchByName" value="Search by Name Only" /><span style="color:#FF0000"> Note:</span> If no entry is provided, it will search all.</td>
					</tr>
				</table>
				<br/>
					<table>
						<tr>							
							<td>Searching Results:-</td>
						</tr>
					</table>
		<? if ($row_cnt >5)
		{?>
			<div id = "tabledisplay" style="overflow:auto; height:400px;">
		<? }
		else 
		{ ?>
			<div id = "tabledisplay">
		<? } ?>

					<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="100%" class="thetable">
					<tr>
						<th width="30"><strong>Tick</strong></th>
						<th width="24"><strong>No.</strong></th>
						<th width="137"><strong>Student Name</strong></th>
						<th width="78"><strong>Staff Role</strong></th>
						<th width="104"><strong>Thesis ID</strong></th>						
						<th width="152"><strong>Thesis / Project Title</strong></th>
						<th width="115"><strong>Senate Approval Date</strong></th> 						
						<th width="115"><strong>Reply Date (Due Date)</strong></th>
						<th width="115"><strong>Acceptance Remarks</strong></th>
						<th width="152"><strong>Attachment by Student</strong></th>
						 
					</tr>
					<?
					//$row_cnt = mysql_num_rows($result1);
					if ($row_cnt>0) {?>
					<?
					
						$no=0;
						for ($i=0; $i<$no2; $i++){	
							

						?>
							<tr>
								<? if ($acceptanceStatusArray[$i]!=""){
										?><td align="center"><input name="myCheckbox[]" type="checkbox" value="<?=$no;?>" disabled="disabled"/></td>
								<?}
									else {
										?><td align="center"><input name="myCheckbox[]" type="checkbox" value="<?=$no;?>"/></td><?
									}
									?>
								<td align="center"><?=$no+1?>.</td>
								<input type="hidden" name="supervisorId[]" size="30" id="supervisorId" value="<?=$supervisorIdArray[$i]?>"/>							
								<?$mySupervisorId[$no]=$supervisorId;?>
								
								<?$myStudentMatrixNo[$no]=$studentMatrixNoArray[$i];?>
								
								<?
								$sql1 = "SELECT name as student_name
										FROM student
										WHERE matrix_no = '$studentMatrixNoArray[$i]'";
								if (substr($studentMatrixNoArray[$i],0,2) != '07') { 
									$dbConnStudent= $dbc; 
								} 
								else { 
									$dbConnStudent=$dbc1; 
								}
								
								$result1 = $dbConnStudent->query($sql1);
								$dbConnStudent->next_record();
								$studentName=$dbConnStudent->f('student_name');
							
								?>

								
								<td><label name="studentName[]" size="30" id="studentName" ></label><?=$studentNameArray[$i]?>
								  <input type="hidden" name="hidname[]" value = "<?=$studentName?>" />
								  <input type="hidden" name="hidstudno[]" value = "<?=$studentMatrixNoArray[$i]?>" />
								  <br/>(<?=$studentMatrixNoArray[$i]?>)</td>
								<?$myStudentName[$no]=$studentName;?>
								
								<td>
								<?if ($supervisorTypeIdArray[$i]!='XS') {?>
									<label name="supervisorType[]" size="15" id="supervisorType" ></label><?=$supervisorTypeArray[$i]?>
								<?} else {
									?>
									<label name="supervisorType[]" size="15" id="supervisorType" ></label><span style="color:#FF0000"><?=$supervisorTypeArray[$i]?></span>
									<?
								}?>
								<input type="hidden" name="mySupervisorTypeId[]" id="mySupervisorTypeId" value="<?=$supervisorTypeIdArray[$i]?>"/>
								
								<input type="hidden" name="hidtype[]" value = "<?=$supervisorTypeArray[$i]?>" />
								<br/><a href="../supervisor/accept_invitation_partner.php?tid=<?=$thesisIdArray[$i];?>&mn=<?=$studentMatrixNoArray[$i]?>"><br/><img src="../images/person_assigned.jpg" width="20" height="19" style="border:0px;" title="Supervisor has been assigned"/> Partners </a>
								</td>
								<?$mySupervisorType[$no]=$supervisorTypeArray[$i];?>
								
								<td><a href="accept_invitation_outline.php?thesisId=<? echo $thesisIdArray[$i];?>&proposalId=<? echo $proposalIdArray[$i];?>" name="thesisId[]" title="Outline of Proposed Case Study by the Student - Read more..."><?=$thesisIdArray[$i];?></a>
							    <input type="hidden" name="hidthesisid[]" value = "<?=$thesisIdArray[$i];?>" /></td>	
								<?$myThesisId[$no]=$thesisIdArray[$i];?>
								<?
									// strip tags to avoid breaking any html
									$thesisTitleString[$i] = strip_tags($thesisTitleArray[$i]);
									
									if (strlen($thesisTitleString[$i]) > 100) 
									{
										// make sure it ends in a word
										$more[$i] = "<a href=\"#\" value=\".$thesisIdArray[$i].\" title=\"".preg_replace('/"/',"'",$thesisTitleArray[$i])."\"> . . read more</a>";
									}
									// truncate string
									$thesisTitleCut[$i] = substr($thesisTitleString[$i], 0, 100);
								?>
							  <td><label name="thesisTitle[]" cols="30" rows="3" id="thesisTitle" ><?=$thesisTitleCut[$i]?></label><?=$more[$i]?>
							    <input type="hidden" name="hidtitle[]" value = "<?=$thesisTitleArray[$i]?>" /></td>														
								<?$myThesisTitle[$no]=$thesisTitleArray[$i];?>
								
							  <td><label name="senateMtgDate[]" size="15" id="senateMtgDate" ></label><?=$senateMtgDateArray[$i]?>
							    <input type="hidden" name="hidsenatedate[]" value = "<?=$senateMtgDateArray[$i]?>" /></td>
								<?$mySenateMtgDate[$no]=$senateMtgDateArray[$i];?>

							  <td><label name="respondedByDate[]" size="15" id="respondedByDate" ></label><?=$respondedByDateArray[$i]?>
							    <input type="hidden" name="hidduedate[]" value = "<?=$respondedByDateArray[$i]?>" /></td>
								<?$myRespondedByDate[$no]=$respondedByDateArray[$i];?>	


								
								
								<td>
								<?if ($acceptanceStatusArray[$i]=='ACC') {
									?>Acceptance Date: <?=$acceptanceDateArray[$i];?></br><?
								}
								else if ($acceptanceStatusArray[$i]=='REJ') {
									?>Rejected Date: <?=$acceptanceDateArray[$i];?></br><?
								}?>
								
								<a href="../supervisor/accept_invitation_detail.php?sid=<?=$supervisorIdArray[$i];?>" name="acceptanceRemarks[]" value="<?=$acceptanceRemarksArray[$i]?>" title="">
							
							
								<?
								if (strlen($acceptanceRemarksArray[$i]) == 0) {?>
								
									<br/><img src="../images/red_edit.jpg" width="20" height="19" style="border:0px;" title="Enter remarks here" >Enter Remarks</a>	
								<?}
								else {
								?>
									<br/><img src="../images/edit.jpg" width="20" height="19" style="border:0px;" title="Read remarks detail" >Read Remarks</a>
								<?
								}?></td>	
								
														
								<? $myAceptanceRemarks[$no]=$acceptanceRemarks;?>
									
									
								<?php
											$sqlUpload="SELECT * FROM file_upload_proposal 
											WHERE pg_proposal_id='$proposalIdArray[$i]' 
											AND attachment_level='S' ";			

											$result = $db_klas2->query($sqlUpload); //echo $sql;
											$row_cnt = mysql_num_rows($result);
											if ($row_cnt>0)
											{
												?><td align="left"><?
												while($row = mysql_fetch_array($result)) 					
												{ 
													?>
															<a href="../thesis/download.php?fc=<?=$row["fu_cd"];?>&al=S"><?=$row["fu_document_filename"];?><img src="../images/download.png" width="20" height="19" style="border:0px;" title="Download"></a><br/>
															
																	
														
												<?}
												?></td><?
											}
											else {
												?><td>No attachment</td><?
											}
										?>	
								<?
						$no=$no+1;
						}
				?>	

				</table>
				</div>
				<br/>
				<table>
					<tr>
						<td><span class="style1">Note:</span> Please provide <strong>Acceptance Remarks</strong> above if you are not willing to accept the appointment.</td>
					</tr>
				</table>
				<br/>
				<table>
					
					<?$_SESSION['myCheckbox'] = $myCheckbox;?>
					<?$_SESSION['mySupervisorId'] = $mySupervisorId;?>
					<?$_SESSION['myStudentMatrixNo'] = $myStudentMatrixNo;?>	
					<?$_SESSION['myThesisId'] = $myThesisId;?>					
					<tr>
						<td></td>
						<td></label><input type="submit" name="acceptBtn" value="Accept Invitation" />
						</label><input type="submit" name="rejectBtn" value="Not Accept Invitation" /></td>
					</tr>
				</table>
				
				<br/>
				<table>					
					<tr>
						<td><strong>DUTIES OF SUPERVISOR / CO-SUPERVISOR</strong></td>
					</tr>
					<tr>
						<td>A Supervisor shall be responsible to:-</td>
					</tr>
				</table>
				<table>
					<tr>
						<td>(a)</td>
						<td>assist in the supervision of appointed postgraduate candidate/s in MSU and in meeting the high standards prescribed for such programmes.</td>
					</tr>
					<tr>
						<td>(b)</td>
						<td>liase with the Coordinator of PhD programme at Graduate School of Management, Faculty of Business Management & Professional Studies of MSU.</td>
					</tr>
					<tr>
						<td>(c)</td>
						<td>maintain regular contact with the postgraduate candidate/s.</td>
					</tr>
					<tr>
						<td>(d)</td>
						<td>may supervise one or more candidates concurrently during the period of appointment.</td>
					</tr>
				</table>			
				<?
				}
				else {
					?>

						<table>
							<tr>
								<td>
									<p>No record found!</p>
								</td>
							</tr>
					<?
				}				
				?>			
					
		</form>
	</body>
</html>