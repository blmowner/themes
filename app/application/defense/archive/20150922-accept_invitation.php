<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: accept_invitation.php
//
// Created by: Zuraimi
// Created Date: 21-July-2015
// Modified by: Zuraimi
// Modified Date: 21-July-2015
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
    global $db;
    
    $run_start = "0001";
    
    $sql_slct_max = "SELECT MAX(SUBSTR($column_name,2,12)) AS run_id FROM $tblname";
    $sql_slct = $db;
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
	$supervisorTypeId=$_POST['supervisorTypeId'];
	$supervisorId=$_POST['supervisorId'];
	$studentMatrixNo=$_POST['studentMatrixNo'];
	$theThesisId=$_POST['theThesisId'];
	$theProposalId=$_POST['theProposalId'];
	$invitationDetailId=$_POST['invitationDetailId'];
	$theCalendarId=$_POST['theCalendarId'];
	$refSessionTypeId=$_POST['refSessionTypeId'];
	
	
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
			
			
			$sql1="UPDATE pg_invitation_detail
			SET	acceptance_date = '$curdatetime' , 
			acceptance_status = 'ACC' , 
			modify_by = '$user_id' , 
			modify_date = '$curdatetime' 	
			WHERE id = '$invitationDetailId[$val]'
			AND pg_invitation_id = '$invitationId[$val]'  
			AND pg_supervisor_id = '$supervisorId[$val]'  
			AND pg_employee_empid = '$user_id'  
			AND status = 'A'";

			$result1 = $dba->query($sql1); 	
			$dba->next_record();
			
			if ($refSessionTypeId[$val] == "DEF") {
				$sql9 = "SELECT a.id as the_evaluation_id
				FROM pg_evaluation a
				LEFT JOIN pg_defense b ON (b.id = a.pg_defense_id)
				WHERE a.pg_thesis_id = '$theThesisId[$val]'
				AND  a.pg_proposal_id = '$theProposalId[$val]'
				AND a.student_matrix_no = '$studentMatrixNo[$val]'
				AND a.respond_status = 'N'
				AND a.status = 'IN1'
				AND a.archived_status IS NULL
				AND b.status = 'REC'
				AND b.respond_status = 'Y'
				AND b.archived_status IS NULL";
				
				$result9 = $dbg->query($sql9);
				$dbg->next_record();
				
				$theDefenseId = $dbg->f('the_defense_id');
				$theEvaluationId = $dbg->f('the_evaluation_id');
				$row_cnt9 = mysql_num_rows($result9);

				if ($row_cnt9 > 0) {				
					$curdatetime = date("Y-m-d H:i:s");	
					$newEvaluationDetailId = runnum('id','pg_evaluation_detail');
					$sql13 = "INSERT INTO pg_evaluation_detail
					(id, pg_eval_id, pg_employee_empid, major_revision, other_comment, responded_date, ref_defense_marks_id, status, 
					insert_by, insert_date, modify_by, modify_date)
					VALUES ('$newEvaluationDetailId', '$theEvaluationId','$user_id', null, null, null, null,'IN1',
					'$user_id', '$curdatetime', '$user_id', '$curdatetime')";
					
					$dbg->query($sql13);
				}
			}
			else if ($refSessionTypeId[$val] == "WCO") {
				$sql9 = "SELECT a.id as the_evaluation_id
				FROM pg_work_evaluation a
				LEFT JOIN pg_work b ON (b.id = a.pg_work_id)
				WHERE a.pg_thesis_id = '$theThesisId[$val]'
				AND  a.pg_proposal_id = '$theProposalId[$val]'
				AND a.student_matrix_no = '$studentMatrixNo[$val]'
				AND a.respond_status = 'N'
				AND a.status = 'IN1'
				AND a.archived_status IS NULL
				AND b.status = 'REC'
				AND b.respond_status = 'Y'
				AND b.archived_status IS NULL";
				
				$result9 = $dbg->query($sql9);
				$dbg->next_record();			
				$theWorkId = $dbg->f('the_work_id');
				$theEvaluationId = $dbg->f('the_evaluation_id');
				$row_cnt9 = mysql_num_rows($result9);

				if ($row_cnt9 > 0) {				
					$curdatetime = date("Y-m-d H:i:s");	
					$newEvaluationDetailId = runnum('id','pg_work_evaluation_detail');
					$sql13 = "INSERT INTO pg_work_evaluation_detail
					(id, pg_eval_id, pg_employee_empid, major_revision, other_comment, responded_date, ref_work_marks_id, status, 
					insert_by, insert_date, modify_by, modify_date)
					VALUES ('$newEvaluationDetailId', '$theEvaluationId','$user_id', null, null, null, null,'IN1',
					'$user_id', '$curdatetime', '$user_id', '$curdatetime')";
					
					$dbg->query($sql13);
				}
			}
			
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
				include("../../../app/application/inbox/submission/supervisor_to_faculty_inbox_2.php"); //>>>>>>>>>>>>>>>>>>>>>>message notification
			
			}
			//>>>>>>>>>>>>>>>>>>>>>>message notification end <<<<<<<<<<<<<<<<<<<<<<<<<<<<<
		}
		
		
		$msg[] = "<div class=\"success\"><span>You have been assigned as Examiner to the selected student successfully.</span></div>";		
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please select the invitation first before click ACCEPT INVITATION button.</span></div>";
	}
}

if(isset($_POST['rejectBtn']) && ($_POST['rejectBtn'] <> ""))
{	
	$myCheckbox=$_POST['myCheckbox'];
	$supervisorTypeId=$_POST['supervisorTypeId'];
	$supervisorId=$_POST['supervisorId'];
	$defenseId=$_POST['defenseId'];
	$studentMatrixNo=$_POST['studentMatrixNo'];
	$thesisId=$_POST['thesisId'];
	$invitationDetailId=$_POST['invitationDetailId'];
	
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
			
			$sql2="UPDATE pg_invitation_detail
			SET	acceptance_date = '$curdatetime' , 
			acceptance_status = 'REJ' , 
			modify_by = '$user_id' , 
			modify_date = '$curdatetime' 	
			WHERE id = '$invitationDetailId[$val]'
			AND pg_invitation_id = '$invitationId[$val]'  
			AND pg_supervisor_id = '$supervisorId[$val]'  
			AND pg_employee_empid = '$user_id'  
			AND status = 'A'"; 
			
			$result2 = $dba->query($sql2); 	
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
		$msg[] = "<div class=\"success\"><span>You have been unassigned as Examiner from the selected student accordingly.</span></div>";		
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please select the invitation first before click NOT ACCEPT INVITATION button.</span></div>";
	}
	
}

if(isset($_POST['btnSearch']) && ($_POST['btnSearch'] <> "")) {
	
	$searchThesisId = $_POST['searchThesisId'];
	$searchStudent = $_POST['searchStudent'];
	$searchSession = $_POST['searchSession'];
	
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
	if ($searchSession!="") 
	{
		$tmpSearchSession = " AND f.ref_session_type_id = '$searchSession'";
	}
	else 
	{
		$tmpSearchSession="";
	}
	
	$sql1 = "SELECT a.id as invitation_id, b.id as invitation_detail_id, b.pg_supervisor_id, a.pg_student_matrix_no, 
	a.pg_thesis_id, e.id AS proposal_id, e.thesis_title, b.acceptance_remarks,
	DATE_FORMAT(b.assigned_date,'%d-%b-%Y') AS assigned_date,
	DATE_FORMAT(f.defense_date,'%d-%b-%Y') AS defense_date,
	DATE_FORMAT(f.defense_stime,'%h:%i%p') AS defense_stime,
	DATE_FORMAT(f.defense_etime,'%h:%i%p') AS defense_etime, f.venue,
	c.ref_supervisor_type_id, g.description AS supervisor_type, h.id as defense_id, 
	b.acceptance_status, j.description as acceptance_status_desc,
	DATE_FORMAT(b.acceptance_date,'%d-%b-%Y %h:%i%p') AS acceptance_date, k.description as ref_session_type_desc
	FROM pg_invitation a 
	LEFT JOIN pg_invitation_detail b ON (b.pg_invitation_id = a.id) 
	LEFT JOIN pg_supervisor c ON (c.id = b.pg_supervisor_id) 
	LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id)
	LEFT JOIN pg_proposal e ON (e.pg_thesis_id = d.id)
	LEFT JOIN pg_calendar f ON (f.id = a.pg_calendar_id)
	LEFT JOIN ref_supervisor_type g ON (g.id = c.ref_supervisor_type_id) 
	LEFT JOIN pg_defense h ON (h.pg_thesis_id = d.id)
	LEFT JOIN ref_acceptance_status j ON (j.id = b.acceptance_status)
	LEFT JOIN ref_session_type k ON (k.id = f.ref_session_type_id)
	WHERE b.pg_employee_empid = '$user_id'"
	.$tmpSearchThesisId." "
	.$tmpSearchStudent." "
	.$tmpSearchSession." "."
	AND a.status = 'A' 
	AND b.status = 'A' 
	AND d.status = 'INP' 
	AND e.verified_status IN ('APP','AWC') 
	AND e.status IN ('APP','APC')
	AND e.archived_status IS NULL 
	AND c.ref_supervisor_type_id IN ('EE','EI','EC','SV','CS','XS')";

	//echo "sql1 ".$sql1;
	$result1 = $db->query($sql1);
	$db->next_record();
	
	$invitationIdArray = Array();	
	$invitationDetailIdArray = Array();	
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
	$acceptanceStatusDescArray = Array();	
	$acceptanceDateArray = Array();	
	$assignedDateArray = Array();
	$assignedDateArray = Array();	
	$defenseDateArray = Array();	
	$defenseSTimeArray = Array();	
	$defenseETimeArray = Array();	
	$defenseVenueArray = Array();
	$defenseIdArray = Array();
	$sessionTypeDescArray = Array();
	
	$no1=0;
	$no2=0;
	do {
		$invitationIdArray[$no1] = $db->f('invitation_id');	
		$invitationDetailIdArray[$no1] = $db->f('invitation_detail_id');	
		$supervisorIdArray[$no1] = $db->f('pg_supervisor_id');	
		$studentMatrixNoArray[$no1] = $db->f('pg_student_matrix_no');
		$supervisorTypeIdArray[$no1] = $db->f('ref_supervisor_type_id');
		$supervisorTypeArray[$no1] = $db->f('supervisor_type');
		$thesisIdArray[$no1] = $db->f('pg_thesis_id');
		$thesisTitleArray[$no1] = $db->f('thesis_title');						
		$respondedByDateArray[$no1] = $db->f('respondedby_date');
		$proposalIdArray[$no1] = $db->f('proposal_id');
		$acceptanceRemarksArray[$no1] = $db->f('acceptance_remarks');
		$acceptanceStatusArray[$no1] = $db->f('acceptance_status');
		$acceptanceStatusDescArray[$no1] = $db->f('acceptance_status_desc');
		$assignedDateArray[$no1] = $db->f('assigned_date');
		$assignedDateArray[$no1] = $db->f('assigned_date');
		$acceptanceDateArray[$no1] = $db->f('acceptance_date');
		$defenseDateArray[$no1]=$db->f('defense_date');		
		$defenseSTimeArray[$no1]=$db->f('defense_stime');
		$defenseETimeArray[$no1]=$db->f('defense_etime');	
		$defenseVenueArray[$no1]=$db->f('venue');	
		$defenseIdArray[$no1]=$db->f('defense_id');	
		$calendarIdArray[$no1]=$db->f('pg_calendar_id');	
		$sessionTypeDescArray[$no1]=$db->f('ref_session_type_desc');
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
			$invitationIdArray[$no2] = $invitationIdArray[$i];
			$invitationDetailIdArray[$no2] = $invitationDetailIdArray[$i];	
			$supervisorIdArray[$no2] = $supervisorIdArray[$i];	
			$supervisorIdArray[$no2] = $supervisorIdArray[$i];
			$studentMatrixNoArray[$no2] = $studentMatrixNoArray[$i];
			$supervisorTypeIdArray[$no2] = $supervisorTypeIdArray[$i];
			$supervisorTypeArray[$no2] = $supervisorTypeArray[$i];
			$thesisIdArray[$no2] = $thesisIdArray[$i];
			$thesisTitleArray[$no2] = $thesisTitleArray[$i];			
			$respondedByDateArray[$no2] = $respondedByDateArray[$i];
			$proposalIdArray[$no2] = $proposalIdArray[$i];
			$acceptanceRemarksArray[$no2] = $acceptanceRemarksArray[$i];
			$acceptanceStatusArray[$no2] = $acceptanceStatusArray[$i];
			$acceptanceStatusDescArray[$no2] = $acceptanceStatusDescArray[$i];
			$assignedDateArray[$no2] = $assignedDateArray[$i];
			$acceptanceDateArray[$no2] = $acceptanceDateArray[$i];
			$defenseDateArray[$no2] = $defenseDateArray[$i];
			$defenseSTimeArray[$no2] = $defenseSTimeArray[$i];
			$defenseETimeArray[$no2] = $defenseETimeArray[$i];
			$defenseVenueArray[$no2] = $defenseVenueArray[$i];
			$defenseIdArray[$no2] = $defenseIdArray[$i];
			$sessionTypeDescArray[$no2] = $sessionTypeDescArray[$i];
			$no2++;
		}
	}
	$row_cnt = $no2;

}
else 
{
	$sql1 = "SELECT DISTINCT a.id as invitation_id, b.id as invitation_detail_id, b.pg_supervisor_id, a.pg_student_matrix_no, 
	a.pg_thesis_id, e.id AS proposal_id, e.thesis_title, b.acceptance_remarks,
	a.pg_calendar_id,
	DATE_FORMAT(b.assigned_date,'%d-%b-%Y') AS assigned_date,
	DATE_FORMAT(f.defense_date,'%d-%b-%Y') AS defense_date,
	DATE_FORMAT(f.defense_stime,'%h:%i%p') AS defense_stime,
	DATE_FORMAT(f.defense_etime,'%h:%i%p') AS defense_etime, f.venue,
	c.ref_supervisor_type_id, g.description AS supervisor_type, /*h.id as defense_id, */
	b.acceptance_status, j.description as acceptance_status_desc,
	DATE_FORMAT(b.acceptance_date,'%d-%b-%Y %h:%i%p') AS acceptance_date, 
	f.ref_session_type_id as session_type_id, k.description as ref_session_type_desc
	FROM pg_invitation a 
	LEFT JOIN pg_invitation_detail b ON (b.pg_invitation_id = a.id) 
	LEFT JOIN pg_supervisor c ON (c.id = b.pg_supervisor_id) 
	LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id)
	LEFT JOIN pg_proposal e ON (e.pg_thesis_id = d.id)
	LEFT JOIN pg_calendar f ON (f.id = a.pg_calendar_id)
	LEFT JOIN ref_supervisor_type g ON (g.id = c.ref_supervisor_type_id) 
	/*LEFT JOIN pg_defense h ON (h.pg_thesis_id = d.id)*/
	LEFT JOIN ref_acceptance_status j ON (j.id = b.acceptance_status)
	LEFT JOIN ref_session_type k ON (k.id = f.ref_session_type_id)
	WHERE b.pg_employee_empid = '$user_id' 
	AND b.ref_invite_status_id = 'INV'
	AND a.status = 'A' 
	AND b.status = 'A' 
	AND d.status = 'INP' 
	AND e.verified_status IN ('APP','AWC') 
	AND e.status IN ('APP','APC')
	AND e.archived_status IS NULL 
	AND c.ref_supervisor_type_id IN ('EE','EI','EC','SV','CS', 'XS')
	ORDER BY b.assigned_date DESC";
		
	$result2 = $db->query($sql1); 
	$db->next_record();
	
	$invitationIdArray = Array();	
	$invitationDetailIdArray = Array();	
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
	$acceptanceStatusDescArray = Array();	
	$acceptanceDateArray = Array();	
	$assignedDateArray = Array();	
	$defenseDateArray = Array();	
	$defenseSTimeArray = Array();	
	$defenseETimeArray = Array();	
	$defenseVenueArray = Array();
	//$defenseIdArray = Array();	
	$calendarIdArray = Array();
	$sessionTypeDescArray = Array();
	$refSessionTypeIdArray = Array();
	
	$no1=0;
	$no2=0;
	do {
		$invitationIdArray[$no1] = $db->f('invitation_id');	
		$invitationDetailIdArray[$no1] = $db->f('invitation_detail_id');	
		$supervisorIdArray[$no1] = $db->f('pg_supervisor_id');	
		$studentMatrixNoArray[$no1] = $db->f('pg_student_matrix_no');
		$supervisorTypeIdArray[$no1] = $db->f('ref_supervisor_type_id');
		$supervisorTypeArray[$no1] = $db->f('supervisor_type');
		$thesisIdArray[$no1] = $db->f('pg_thesis_id');
		$thesisTitleArray[$no1] = $db->f('thesis_title');						
		$respondedByDateArray[$no1] = $db->f('respondedby_date');
		$proposalIdArray[$no1] = $db->f('proposal_id');
		$acceptanceRemarksArray[$no1] = $db->f('acceptance_remarks');
		$acceptanceStatusArray[$no1] = $db->f('acceptance_status');
		$acceptanceStatusDescArray[$no1] = $db->f('acceptance_status_desc');
		$assignedDateArray[$no1] = $db->f('assigned_date');
		$acceptanceDateArray[$no1] = $db->f('acceptance_date');
		$defenseDateArray[$no1]=$db->f('defense_date');		
		$defenseSTimeArray[$no1]=$db->f('defense_stime');
		$defenseETimeArray[$no1]=$db->f('defense_etime');	
		$defenseVenueArray[$no1]=$db->f('venue');	
		//$defenseIdArray[$no1]=$db->f('defense_id');	
		$calendarIdArray[$no1]=$db->f('pg_calendar_id');	
		$sessionTypeDescArray[$no1]=$db->f('ref_session_type_desc');
		$refSessionTypeIdArray[$no1]=$db->f('session_type_id');
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
			$invitationIdArray[$no2] = $invitationIdArray[$i];
			$invitationDetailIdArray[$no2] = $invitationDetailIdArray[$i];	
			$supervisorIdArray[$no2] = $supervisorIdArray[$i];	
			$supervisorIdArray[$no2] = $supervisorIdArray[$i];
			$studentMatrixNoArray[$no2] = $studentMatrixNoArray[$i];
			$supervisorTypeIdArray[$no2] = $supervisorTypeIdArray[$i];
			$supervisorTypeArray[$no2] = $supervisorTypeArray[$i];
			$thesisIdArray[$no2] = $thesisIdArray[$i];
			$thesisTitleArray[$no2] = $thesisTitleArray[$i];			
			$respondedByDateArray[$no2] = $respondedByDateArray[$i];
			$proposalIdArray[$no2] = $proposalIdArray[$i];
			$acceptanceRemarksArray[$no2] = $acceptanceRemarksArray[$i];
			$acceptanceStatusArray[$no2] = $acceptanceStatusArray[$i];
			$acceptanceStatusDescArray[$no2] = $acceptanceStatusDescArray[$i];
			$acceptanceDateArray[$no2] = $acceptanceDateArray[$i];
			$assignedDateArray[$no2] = $assignedDateArray[$i];
			$defenseDateArray[$no2] = $defenseDateArray[$i];
			$defenseSTimeArray[$no2] = $defenseSTimeArray[$i];
			$defenseETimeArray[$no2] = $defenseETimeArray[$i];
			$defenseVenueArray[$no2] = $defenseVenueArray[$i];
			//$defenseIdArray[$no2] = $defenseIdArray[$i];
			$calendarIdArray[$no2] = $calendarIdArray[$i];
			$sessionTypeDescArray[$no2] = $sessionTypeDescArray[$i];
			$refSessionTypeIdArray[$no2] = $refSessionTypeIdArray[$i];
			$no2++;
		}
	}
	$row_cnt = $no2;
}

$sql5 = "SELECT id, description 
FROM ref_session_type
WHERE status = 'A'
ORDER BY seq";
$dba->query($sql5);
$dba->next_record();

$sessionTypeIdArray = Array();
$sessionDescArray =Array();
$no = 0;
do {
	$sessionTypeIdArray[$no] = $dba->f('id');
	$sessionDescArray[$no] = $dba->f('description');
	$no++;
} while ($dba->next_record());

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
		<script language="JavaScript" src="../js/windowopen.js"></script>
		<!--<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>-->
		<script language="JavaScript" type="text/javascript" src="../../../lib/js/tooltip.js"></script>
	    <style type="text/css"></style>
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
						<td><label>Session Type</label></td>
						<td>:</td>
						<td>
							<select name="searchSession" id="searchSession">
							<?if ($_POST['searchSession']=="") {?>
								<option value="" selected="selected"></option>
							<? }
							else {?>
								<option value=""></option>
							<?}
							for ($i=0;$i<count($sessionTypeIdArray);$i++) {
								if ($_POST['searchSession'] == $sessionTypeIdArray[$i]) {
									?>
									<option value="<?=$sessionTypeIdArray[$i]?>" selected="selected"><?=$sessionDescArray[$i]?></option>
									<?
								}
								else {
									?>
									<option value="<?=$sessionTypeIdArray[$i]?>"><?=$sessionDescArray[$i]?></option>
									<?
								}
							}?>
								
							</select>
						</td>
						<td><input type="submit" name="btnSearch" value="Search" /><span style="color:#FF0000"> Note:</span> If no entry is provided, it will search all.</td>
					</tr>
					
				</table>
				<br/>
					<table>
						<tr>							
							<td>Searching Results:- <?=$row_cnt?> record(s) found.</td>
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
					<th width="5%"><strong>Tick</strong></th>
					<th width="5%"><strong>No.</strong></th>
					<th width="15%" align="left"><strong>Student Name</strong></th>
					<th width="25%" align="left"><strong>Thesis / Project Title</strong></th>
					<th width="10%" align="left"><strong>Staff Role</strong></th>
					<th width="15%" align="left"><strong>Evaluation Schedule</strong></th>
					<th width="10%" align="left"><strong>Invite Date</strong></th>
					<th width="15%" align="left"><strong>Invitation Status</strong></th>					
				</tr>
				<?
				if ($row_cnt>0) {?>
				<?				
					$no=0;
					for ($i=0; $i<$no2; $i++){	
					?>
						<input type="hidden" name="invitationId[]" size="30" id="invitationId" value="<?=$invitationIdArray[$i]?>"/>
						<input type="hidden" name="invitationDetailId[]" size="30" id="invitationDetailId" value="<?=$invitationDetailIdArray[$i]?>"/>
						<input type="hidden" name="supervisorId[]" size="30" id="supervisorId" value="<?=$supervisorIdArray[$i]?>"/>
						<input type="hidden" name="defenseId[]" size="30" id="defenseId" value="<?=$defenseIdArray[$i]?>"/>
						<input type="hidden" name="supervisorTypeId[]" id="supervisorTypeId" value="<?=$supervisorTypeIdArray[$i]?>"/>	
						<input type="hidden" name="studentMatrixNo[]" id="studentMatrixNo" value ="<?=$studentMatrixNoArray[$i]?>"/>
						<input type="hidden" name="theThesisId[]" id="theThesisId" value ="<?=$thesisIdArray[$i]?>"/>
						<input type="hidden" name="theProposalId[]" id="theProposalId" value ="<?=$proposalIdArray[$i]?>"/>
						<input type="hidden" name="theCalendarId[]" id="theCalendarId" value ="<?=$calendarIdArray[$i]?>"/>
						<input type="hidden" name="refSessionTypeId[]" id="refSessionTypeId" value ="<?=$refSessionTypeIdArray[$i]?>"/>
						<tr>
							<? if ($acceptanceStatusArray[$i]!=""){
									?><td align="center"><input name="myCheckbox[]" type="checkbox" value="<?=$no;?>" disabled="disabled"/></td>
							<?}
								else {
									?><td align="center"><input name="myCheckbox[]" type="checkbox" value="<?=$no;?>"/></td><?
								}
								?>
							<td align="center"><?=$no+1?>.</td>
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
							  <br/>(<?=$studentMatrixNoArray[$i]?>)</td>
							  <input type="hidden" name="hidname[]" value = "<?=$studentName?>" />
							  <input type="hidden" name="hidstudno[]" value = "<?=$studentMatrixNoArray[$i]?>" />
							<?
								// strip tags to avoid breaking any html
								$thesisTitleString[$i] = strip_tags($thesisTitleArray[$i]);
								
								if (strlen($thesisTitleString[$i]) > 100) 
								{
									// make sure it ends in a word
									$more[$i] = "<a href=\"#\" value=\".$thesisIdArray[$i].\" title=\"".preg_replace('/"/',"'",$thesisTitleArray[$i])."\">... Read more</a>";
								}
								// truncate string
								$thesisTitleCut[$i] = substr($thesisTitleString[$i], 0, 100);
							?>
							  <td>Thesis / Project ID: <a href="accept_invitation_outline.php?thesisId=<? echo $thesisIdArray[$i];?>&proposalId=<? echo $proposalIdArray[$i];?>" name="thesisId[]" title="Outline of Proposed Case Study by the Student - Read more..."><?=$thesisIdArray[$i];?></a>
								<input type="hidden" name="hidthesisid[]" value = "<?=$thesisIdArray[$i];?>" /><br/><br/>
								<label name="thesisTitle[]" cols="30" rows="3" id="thesisTitle" ><?=$thesisTitleCut[$i]?></label><?=$more[$i]?>
								<input type="hidden" name="hidtitle[]" value = "<?=$thesisTitleArray[$i]?>" /></td>
							<td>
							<?if ($supervisorTypeIdArray[$i]!='XE') {?>
								<label name="supervisorType[]" size="15" id="supervisorType" ></label><?=$supervisorTypeArray[$i]?>
							<?} else {
								?>
								<label name="supervisorType[]" size="15" id="supervisorType" ></label><span style="color:#FF0000"><?=$supervisorTypeArray[$i]?></span>
								<?
							}?>
							<input type="hidden" name="hidtype[]" value = "<?=$supervisorTypeArray[$i]?>" />
							<br/><a href="../defense/accept_invitation_partner.php?vid=<?=$invitationIdArray[$i]?>&tid=<?=$thesisIdArray[$i];?>&mn=<?=$studentMatrixNoArray[$i]?>"><br/><img src="../images/person_assigned.jpg" width="20" height="19" style="border:0px;" title="Supervisor has been assigned" /> Partners </a>
							</td>
							<td><?=$sessionTypeDescArray[$i]?><br/><a href="javascript:void(0);" onMouseOver="toolTip('ID:<?=$calendarIdArray[$i];?>', 20)" onMouseOut="toolTip()"><?=$defenseDateArray[$i]?></a><br/><?=$defenseSTimeArray[$i]?> to <?=$defenseETimeArray[$i]?><br/>
							<?=$defenseVenueArray[$i]?></td>
						<td><label><?=$assignedDateArray[$i]?><br/></label></td>							
						  <td><label>
							<?if ($acceptanceStatusArray[$i]=="ACC") {
								?>
								<?=$acceptanceStatusDescArray[$i]?>
								<?
							}
							else {
								?>
								<span style="color:#FF0000"><?=$acceptanceStatusDescArray[$i]?></span>
								<?
							}
							?>
							<br/><?=$acceptanceDateArray[$i];?></br></label>
							<?							
							if ($acceptanceStatusArray[$i]=='') {
								?>
								<a href="accept_invitation_detail.php?vdid=<?=$invitationDetailIdArray[$i];?>" name="acceptanceRemarks[]" value="<?=$acceptanceRemarksArray[$i]?>" title=""><?
								if (strlen($acceptanceRemarksArray[$i]) == 0) {?>
								
									<br/><img src="../images/red_edit.jpg" width="20" height="19" style="border:0px;" title="Enter remarks here" >Enter Remarks</a>	
								<?}
								else {
								?>
									<br/><img src="../images/edit.jpg" width="20" height="19" style="border:0px;" title="Read remarks detail" >Read Remarks</a>
								<?
								}
							}
							else {

								// strip tags to avoid breaking any html
								$acceptanceRemarksString[$i] = strip_tags($acceptanceRemarksArray[$i]);
								
								if (strlen($acceptanceRemarksString[$i]) > 20) 
								{
									// make sure it ends in a word
									$moreRemarks[$i] = "<a href=\"#\" value=\".$acceptanceRemarksCut[$i].\" title=\"".preg_replace('/"/',"'",$acceptanceRemarksArray[$i])."\">... Read more</a>";
								}
								// truncate string
								$acceptanceRemarksCut[$i] = substr($acceptanceRemarksString[$i], 0, 20);
								?><br/>Remarks: <label><?=$acceptanceRemarksCut[$i]?></label><?=$moreRemarks[$i]?>

								
							<?}?>
							</td>	
							<?
					$no=$no+1;
					}
				?>	
				</table>
				</div>
				<br/>
				<table>
					<tr>
						<td><span class="style1">Note:</span> Please provide <strong>Acceptance Remarks</strong> above if you are not able to accept the invitation.</td>
					</tr>
				</table>
				<br/>
				<table>					
					<tr>
						<td></td>
						<td></label><input type="submit" name="acceptBtn" value="Accept Invitation" />
						</label><input type="submit" name="rejectBtn" value="Not Accept Invitation" /></td>
					</tr>
				</table>
				
				<br/>
						
				<?
				}
				else {
					?>
					<table>
						<tr>
							<td><label>No record found!</label>	</td>
						</tr>
					</table>
					<br/>
					<table>				
						<tr><td><br/><span style="color:#FF0000">Note:</span><br/>
							Possible Reason:-<br/>
							1. The invitation as Evaluation Committee Panel is still pending.</td>
						</tr>
					</table>
					<?
				}				
				?>			
					
		</form>
	</body>
</html>