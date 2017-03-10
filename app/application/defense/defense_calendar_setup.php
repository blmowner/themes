<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: defense_calendar_setup.php
//
// Created by: Zuraimi
// Created Date: 13-July-2015
// Modified by: Zuraimi
// Modified Date: 13-July-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

/*ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);*/

session_start();
$user_id=$_SESSION['user_id'];
$tickAlert = false;

if (!class_exists('DateTime')) {
	class DateTime {
		public $date;
	   
		public function __construct($date) {
			$this->date = strtotime($date);
		}
	   
		public function setTimeZone($timezone) {
			return;
		}
	   
		private function __getDate() {
			return date(DATE_ATOM, $this->date);   
		}
	   
		public function modify($multiplier) {
			$this->date = strtotime($this->__getDate() . ' ' . $multiplier);
		}
	   
		public function format($format) {
			return date($format, $this->date);
		}
	}
}

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

				
						
						
if(isset($_POST['btnAdd']) && ($_POST['btnAdd'] <> ""))
{
	$msg = array();
	if (empty($_POST['add_defense_date'])) $msg[] = "<div class=\"error\"><span>Please provide <strong>Defence Proposal Start Date</strong>.</span></div>";
	if (empty($_POST['add_defense_stime'])) $msg[] = "<div class=\"error\"><span>Please provide <strong>Defence Proposal Start Time</strong>.</span></div>";
	if (empty($_POST['add_defense_etime'])) $msg[] = "<div class=\"error\"><span>Please provide <strong>Defence Proposal End Time</strong>.</span></div>";
	if (empty($_POST['add_venue'])) $msg[] = "<div class=\"error\"><span>Please provide the <strong>Venue</strong> where the defense proposal will be conducted.</span></div>";
	if (empty($_POST['add_matrix_no'])) $msg[] = "<div class=\"error\"><span>Please select the <strong>Student</strong>.</span></div>";
	
	$addDefenseDateFirst1 = date('d-M-Y H:i', strtotime($_POST['add_defense_date'.$val]." ".$_POST['add_defense_stime']));
	$addDefenseDateFirst2 = new DateTime($addDefenseDateFirst1);
	$addDefenseDateFirst3 = $addDefenseDateFirst2->format('d-M-Y H:i');
	$addDefenseDateFirst4 = new DateTime($addDefenseDateFirst3);
	
	$addDefenseDateSecnd1 = date('d-M-Y H:i', strtotime($_POST['add_defense_date'.$val]." ".$_POST['add_defense_etime']));
	$addDefenseDateSecnd2 = new DateTime($addDefenseDateSecnd1);
	$addDefenseDateSecnd3 = $addDefenseDateSecnd2->format('d-M-Y H:i');
	$addDefenseDateSecnd4 = new DateTime($addDefenseDateSecnd3);
	
	if ($addDefenseDateFirst4 > $addDefenseDateSecnd4) {
		$msg[] = "<div class=\"error\"><span>The Session Start Time cannot be later than Session End Time. Please reselect.</div>";
	}

	if(empty($msg)) 
	{
		$myAddDefenseDate = $_POST['add_defense_date'];
		$myAddDefenseSTime = $_POST['add_defense_stime'];
		$myAddDefenseETime = $_POST['add_defense_etime'];
		$myAddVenue = $_POST['add_venue'];
		$myAddMatrixNo = $_POST['add_matrix_no'];
		$myAddThesisId = $_POST['add_thesis_id'];
		$myAddRemarks = $_POST['add_remarks'];
		$myAddEvaluationSession = $_POST['add_evaluation_session'];

		$curdatetime = date("Y-m-d H:i:s");
		$calendarId = runnum2('id','pg_calendar');	

		$sql = "INSERT INTO pg_calendar(
		id, student_matrix_no, ref_session_type_id, thesis_id, defense_date, defense_stime, defense_etime, venue, remarks, status, insert_by, insert_date, modify_by, modify_date)
		VALUES ('$calendarId', '$myAddMatrixNo', '$myAddEvaluationSession', '$myAddThesisId', STR_TO_DATE('$myAddDefenseDate','%d-%M-%Y'), 
		STR_TO_DATE('$myAddDefenseSTime','%H:%i'), STR_TO_DATE('$myAddDefenseETime','%H:%i'),
		'$myAddVenue', '$myAddRemarks', 'A', '$user_id', '$curdatetime', '$user_id', '$curdatetime')";

		$dbd->query($sql); 
		
		$msg[] = "<div class=\"success\"><span>The entry for the Evaluation Schedule has been added successfully.</span></div>";
	}
}

if(isset($_POST['btnUpdate']) && ($_POST['btnUpdate'] <> ""))
{					
	$defenseCheckBox1 = $_POST['defense_checkbox'];
	
	$no=1;
	
	if (sizeof($_POST['defense_checkbox'])>0) {
		while (list ($key,$val) = @each ($defenseCheckBox1)) 
		{
			$no=$no+$val;
			if (empty($_POST['venue'])) $msg[] = "<div class=\"error\"><span>Please provide the <strong>Venue</strong> for record no $no where the Evaluation Session will be conducted.</span></div>";
		}
		
		if(empty($msg)) 
		{
			$curdatetime = date("Y-m-d H:i:s");
			$listInvitationId = $_POST['list_invitation_id'];
			while (list ($key,$val) = @each ($_POST['defense_checkbox'])) 
			{
				if ($listInvitationId[$val]=="") {
					$row_cnt6=0;
				}
				else {
					$sql6 = "SELECT id
					FROM pg_invitation_detail
					WHERE pg_invitation_id = '$listInvitationId[$val]'
					AND acceptance_status = 'ACC'
					AND ref_invite_status_id = 'INV'
					AND status = 'A'";
					
					$result_sql6 = $dbb->query($sql6);
					$dbb->next_record();
					
					$row_cnt6 = mysql_num_rows($result_sql6);
				}
				
				if ($row_cnt6 == 0) {
					$calendar_id = $_POST['calendar_id'][$val];
					$defense_date = $_POST['defense_date'][$val];
					$defense_stime = $_POST['defense_stime'][$val];
					$defense_etime = $_POST['defense_etime'][$val];
					$venue = $_POST['venue'][$val];
					$remarks = $_POST['remarks'][$val];
					$session_type_id = $_POST['session_type_id'][$val];
					

					
					$sql1 = "UPDATE pg_calendar
					SET ref_session_type_id = '$session_type_id',
					defense_date = STR_TO_DATE('$defense_date','%d-%b-%Y'), 
					defense_stime = STR_TO_DATE('$defense_stime','%H:%i'),
					defense_etime = STR_TO_DATE('$defense_etime','%H:%i'),
					venue = '$venue',
					remarks = '$remarks',
					modify_by = '$user_id', modify_date = '$curdatetime'
					WHERE id = '$calendar_id'";
					
					$dba->query($sql1);
					$no = $val + 1;
					$msg[] = "<div class=\"success\"><span>The selected Evaluation Schedule (record no $no) has been updated successfully.</span></div>";
				}
				else {
					$no = $val + 1;
					$msg[] = "<div class=\"error\"><span>The selected Evaluation Schedule (record no $no) cannot be updated due to the Evaluation Panel has accepted the invitation.</span></div>";
				}
			}
		}
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please tick which Evaluation Schedule to be updated!</span></div>";
	}
}

if(isset($_POST['btnDelete']) && ($_POST['btnDelete'] <> ""))
{					
	$listInvitationId = $_POST['list_invitation_id'];
	if (sizeof($_POST['defense_checkbox'])>0) {
		$curdatetime = date("Y-m-d H:i:s");
		while (list ($key,$val) = @each ($_POST['defense_checkbox'])) 
		{
			$sql6 = "SELECT id
			FROM pg_invitation_detail
			WHERE pg_invitation_id = '$listInvitationId[$val]'
			AND acceptance_status = 'ACC'
			AND ref_invite_status_id = 'INV'
			AND status = 'A'";
			
			$result_sql6 = $dbb->query($sql6);
			$dbb->next_record();
			
			$row_cnt6 = mysql_num_rows($result_sql6);
			
			if ($row_cnt6 == 0) {
				$calendarId = $_POST['calendar_id'][$val];
				
				$sql1 = "SELECT id
				FROM pg_calendar
				WHERE id = '$calendarId'
				AND recomm_status = 'REC'";
				
				$result_sql1 = $dba->query($sql1); 
				$row_cnt1 = mysql_num_rows($result_sql1);
				if ($row_cnt1 > 0) {
					$sql1 = "UPDATE pg_calendar
					SET status = 'I', modify_by = '$user_id', modify_date = '$curdatetime'
					WHERE id = '$calendarId'
					AND recomm_status = 'REC'
					AND status = 'A'";
				
				$result_sql1 = $dba->query($sql1); 
				}
				else {
					$sql3 = "DELETE FROM pg_calendar
					WHERE id = '$calendarId'";
					
					$dba->query($sql3); 
				}
				$no = $val + 1;
				$msg[] = "<div class=\"success\"><span>The selected Evaluation Schedule (record no. $no) has been deleted successfully.</span></div>";
			}
			else {
				$no = $val + 1;
				$msg[] = "<div class=\"error\"><span>The selected Evaluation Schedule (record no. $no) cannot be deleted due to the Evaluation Panel has accepted the invitation.</span></div>";
			}
		}
		
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please tick which Evaluation Schedule to be deleted!</span></div>";
	}
}

if(isset($_POST['btnInvite']) && ($_POST['btnInvite'] <> ""))
{					
	$studentMatrixNo = $_POST['studentMatrixNo'];
	$calendarId = $_POST['calendar_id'];
	$submitStatus = $_POST['submitStatus'];
	$listThesisId = $_POST['list_thesis_id'];
	$listInvitationId = $_POST['list_invitation_id'];
	$listSessionTypeId = $_POST['list_session_type_id'];
	$calendarId = $_POST['calendar_id'];
	
	if (sizeof($_POST['defense_checkbox'])>0) {
		
		$curdatetime = date("Y-m-d H:i:s");
		
		while (list ($key,$val) = @each ($_POST['defense_checkbox'])) 
		{

			$supervisorIdArray = Array();
			$thesisIdArray = Array();
			$employeeIdArray = Array();
			$acceptanceStatusArray = Array();

			if ($listSessionTypeId[$val] != "VIV") {
				$conditionSupervisorType = "AND a.ref_supervisor_type_id in ('EE','EI', 'EC', 'XE')";
			}
			else {
				$conditionSupervisorType = "AND a.ref_supervisor_type_id in ('EE','EI', 'EC', 'XE', 'SV','CS', 'XS')";
			}
			
			$sql = "SELECT a.id, a.pg_thesis_id, a.pg_employee_empid
			FROM pg_supervisor a
			LEFT JOIN ref_supervisor_type e ON (e.id = a.ref_supervisor_type_id)
			LEFT JOIN ref_acceptance_status f ON (f.id = a.acceptance_status)
			WHERE a.pg_student_matrix_no = '$studentMatrixNo[$val]'
			AND a.pg_thesis_id = '$listThesisId[$val]' "
			.$conditionSupervisorType." "."
			AND a.pg_employee_empid NOT IN 
			(SELECT b.pg_employee_empid
			FROM pg_invitation a
			LEFT JOIN pg_invitation_detail b ON (b.pg_invitation_id = a.id)
			LEFT JOIN pg_calendar c ON (c.id = a.pg_calendar_id)
			WHERE a.pg_student_matrix_no = '$studentMatrixNo[$val]' 
			AND a.pg_thesis_id = '$listThesisId[$val]'
			AND a.pg_calendar_id = '$calendarId[$val]'
			AND b.ref_invite_status_id = 'INV'
			AND c.ref_session_type_id = '$listSessionTypeId[$val]'
			AND a.status = 'A'
			AND b.status = 'A')
			AND a.status = 'A' 
			ORDER BY e.seq, a.pg_employee_empid";
			
			$result_sql = $dbe->query($sql); 
			$dbe->next_record();
			$row_cnt = mysql_num_rows($result_sql);

			$no=0;
			if ($row_cnt > 0) {
				do {
					$supervisorIdArray[$no] = $dbe->f('id');
					$employeeIdArray[$no] = $dbe->f('pg_employee_empid');
					$acceptanceStatusArray[$no] = $dbe->f('acceptance_status');
					$no++;		
				} while ($dbe->next_record());
			
				if ($listInvitationId[$val]=="") {
					
					$lock_tables="LOCK TABLES pg_invitation WRITE, pg_invitation_detail WRITE"; //lock the table
					$db->query($lock_tables);
				
					$newInvitationId = runnum2('id','pg_invitation');
					$sql3 = "INSERT INTO pg_invitation
					(id, pg_calendar_id, pg_student_matrix_no, pg_thesis_id, submit_status, status,
					insert_by, insert_date, modify_by, modify_date)
					VALUES ('$newInvitationId','$calendarId[$val]','$studentMatrixNo[$val]','$listThesisId[$val]','IN2', 'A',			
					'$user_id','$curdatetime','$user_id','$curdatetime')";
					
					$dba->query($sql3); 
					
					for ($i=0;$i<$no;$i++) {
						$newInvitationDetailId = runnum2('id','pg_invitation_detail');
						$sql4 = "INSERT INTO pg_invitation_detail
						(id, pg_supervisor_id, pg_invitation_id, pg_employee_empid, ref_invite_status_id, assigned_by, assigned_date, status,
						insert_by, insert_date, modify_by, modify_date)
						VALUES ('$newInvitationDetailId','$supervisorIdArray[$i]','$newInvitationId','$employeeIdArray[$i]', 'INV',
						'$user_id','$curdatetime', 'A','$user_id','$curdatetime','$user_id','$curdatetime')";
						
						$dba->query($sql4); 
					}
					$unlock_tables="UNLOCK TABLES"; //unlock the table;
					$db->query($unlock_tables);	
				}
				else {
					$sql3 = "UPDATE pg_invitation
					SET modify_by = '$user_id', modify_date = '$curdatetime'
					WHERE id = '$listInvitationId[$val]'";
					
					$dba->query($sql3);
					
					for ($i=0;$i<$no;$i++) {
						$lock_tables="LOCK TABLES pg_invitation_detail WRITE"; //lock the table
						$db->query($lock_tables);
						
						$newInvitationDetailId = runnum2('id','pg_invitation_detail');
						$sql4 = "INSERT INTO pg_invitation_detail
						(id, pg_supervisor_id, pg_invitation_id, pg_employee_empid, ref_invite_status_id, assigned_by, assigned_date, status,
						insert_by, insert_date, modify_by, modify_date)
						VALUES ('$newInvitationDetailId','$supervisorIdArray[$i]','$listInvitationId[$val]','$employeeIdArray[$i]', 'INV',
						'$user_id','$curdatetime', 'A','$user_id','$curdatetime','$user_id','$curdatetime')";
						
						$dba->query($sql4); 
					}
					$unlock_tables="UNLOCK TABLES"; //unlock the table;
					$db->query($unlock_tables);	
				}									
				$no = $val + 1;
				$msg[] = "<div class=\"success\"><span>The invitation for the selected Evaluation Schedule (record no. $no) has been submitted to the Evaluation Panel successfully.</span></div>";
			}
			else {
				$no = $val + 1;
				$msg[] = "<div class=\"error\"><span>The invitation for the selected Evaluation Schedule (record no. $no) cannot be sent due to no new Evaluation Panel has been assigned.</span></div>";
			}					
		}	
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please tick which Evaluation Schedule is required to send the invitation to Evaluation Panel!</span></div>";
		$tickAlert = true;
	}
}

if(isset($_POST['btnSearch']) && ($_POST['btnSearch'] <> "")) {	
	$searchEvaluationSession = $_POST['search_evaluation_session'];
	$searchStudentName = $_POST['search_student_name'];
	$searchMatrixNo = $_POST['search_matrix_no'];
	
	if ($searchEvaluationSession!="") {
		$searchEvaluationSessionTmp = " AND a.ref_session_type_id = '$searchEvaluationSession' ";
	}
	else {
		$searchEvaluationSessionTmp = "";
	}
	if ($searchMatrixNo!="") {
		$searchMatrixNoTmp = " AND a.student_matrix_no = '$searchMatrixNo' ";
	}
	else {
		$searchMatrixNoTmp = "";
	}
	
	$sql7="(SELECT a.id, b.id AS invitation_id, a.student_matrix_no, a.thesis_id, DATE_FORMAT(a.defense_date,'%d-%b-%Y') AS defense_date, 
	DATE_FORMAT(a.defense_stime,'%H:%i') AS defense_stime, DATE_FORMAT(a.defense_etime,'%H:%i') AS defense_etime, a.venue, a.remarks,
	b.submit_status, c.description AS submit_status_desc, a.ref_session_type_id, f.status AS evaluation_status
	FROM  pg_calendar a
	LEFT JOIN pg_invitation b ON (b.pg_calendar_id = a.id)
	LEFT JOIN ref_proposal_status c ON (c.id = b.submit_status)
	LEFT JOIN ref_session_type d ON (d.id = a.ref_session_type_id)
	LEFT JOIN pg_defense e ON (e.pg_calendar_id = a.id)
	LEFT JOIN pg_evaluation f ON (f.pg_defense_id = e.id)
	WHERE a.status = 'A'"
	.$searchEvaluationSessionTmp." "
	.$searchMatrixNoTmp."
	AND a.archived_status IS NULL
	AND a.ref_session_type_id = 'DEF'
	ORDER BY a.student_matrix_no, d.seq, a.defense_date, a.defense_stime)
	UNION

	(SELECT a.id, b.id AS invitation_id, a.student_matrix_no, a.thesis_id, DATE_FORMAT(a.defense_date,'%d-%b-%Y') AS defense_date, 
	DATE_FORMAT(a.defense_stime,'%H:%i') AS defense_stime, DATE_FORMAT(a.defense_etime,'%H:%i') AS defense_etime, a.venue, a.remarks,
	b.submit_status, c.description AS submit_status_desc, a.ref_session_type_id, f.status AS evaluation_status
	FROM  pg_calendar a
	LEFT JOIN pg_invitation b ON (b.pg_calendar_id = a.id)
	LEFT JOIN ref_proposal_status c ON (c.id = b.submit_status)
	LEFT JOIN ref_session_type d ON (d.id = a.ref_session_type_id)
	LEFT JOIN pg_work e ON (e.pg_calendar_id = a.id)
	LEFT JOIN pg_work_evaluation f ON (f.pg_work_id = e.id)
	WHERE a.status = 'A'"
	.$searchEvaluationSessionTmp." "
	.$searchMatrixNoTmp."
	AND a.archived_status IS NULL
	AND a.ref_session_type_id = 'WCO'
	ORDER BY a.student_matrix_no, d.seq, a.defense_date, a.defense_stime)
	UNION
	(SELECT a.id, b.id AS invitation_id, a.student_matrix_no, a.thesis_id, DATE_FORMAT(a.defense_date,'%d-%b-%Y') AS defense_date, 
	DATE_FORMAT(a.defense_stime,'%H:%i') AS defense_stime, DATE_FORMAT(a.defense_etime,'%H:%i') AS defense_etime, a.venue, a.remarks,
	b.submit_status, c.description AS submit_status_desc, a.ref_session_type_id, f.status AS evaluation_status
	FROM  pg_calendar a
	LEFT JOIN pg_invitation b ON (b.pg_calendar_id = a.id)
	LEFT JOIN ref_proposal_status c ON (c.id = b.submit_status)
	LEFT JOIN ref_session_type d ON (d.id = a.ref_session_type_id)
	LEFT JOIN pg_viva e ON (e.pg_calendar_id = a.id)
	LEFT JOIN pg_evaluation_viva f ON (f.pg_viva_id = e.id)
	WHERE a.status = 'A'"
	.$searchEvaluationSessionTmp." "
	.$searchMatrixNoTmp."
	AND a.archived_status IS NULL
	AND a.ref_session_type_id = 'VIV'
	ORDER BY a.student_matrix_no, d.seq, a.defense_date, a.defense_stime)";
	
	$result_sql7 = $db->query($sql7);
	$db->next_record();	
	
	$calendarIdArray = Array();
	$invitationIdArray = Array();
	$studentMatrixNoArray = Array();
	$thesisIdArray = Array();
	$defenseDateArray = Array();
	$defenseSTimeArray = Array();
	$defenseETimeArray = Array();
	$venueArray = Array();
	$remarksArray = Array();
	$submitStatusArray = Array();
	$submitStatusDescArray = Array();
	$sessionTypeIdArray = Array();
	$evaluationStatusArray = Array();
	$studentNameArray = Array();
	
	$i=0;
	do {
		$calendarIdArray[$i] = $db->f('id');
		$invitationIdArray[$i] = $db->f('invitation_id');
		$studentMatrixNoArray[$i] = $db->f('student_matrix_no');
		$thesisIdArray[$i] = $db->f('thesis_id');
		$defenseDateArray[$i] = $db->f('defense_date');
		$defenseSTimeArray[$i] = $db->f('defense_stime');
		$defenseETimeArray[$i] = $db->f('defense_etime');
		$venueArray[$i] = $db->f('venue');
		$remarksArray[$i] = $db->f('remarks');
		$submitStatusArray[$i] = $db->f('submit_status');
		$submitStatusDescArray[$i] = $db->f('submit_status_desc');
		$sessionTypeIdArray[$i] = $db->f('ref_session_type_id');
		$evaluationStatusArray[$i] = $db->f('evaluation_status');
		$i++;
	} while ($db->next_record());
	
	$n=0;
	for ($m=0;$m<count($calendarIdArray);$m++) {
		if (substr($studentMatrixNoArray[$m],0,2) != '07') {
			$dbConn = $dbc;
		}
		else {
			$dbConn = $dbc1;
		}
		$sql2 = "SELECT name
		FROM student
		WHERE matrix_no = '$studentMatrixNoArray[$m]'
		AND name like '%$searchStudentName%'";
		
		$result_sql2 = $dbConn->query($sql2);
		$dbConn->next_record();
		$row_cnt2 = mysql_num_rows($result_sql2);
		if ($row_cnt2 > 0) {
			$studentNameArray[$n] = $dbConn->f('name');
			$calendarIdArray[$n] = $calendarIdArray[$m];
			$invitationIdArray[$n] = $invitationIdArray[$m];
			$studentMatrixNoArray[$n] = $studentMatrixNoArray[$m];
			$thesisIdArray[$n] = $thesisIdArray[$m];
			$defenseDateArray[$n] = $defenseDateArray[$m];
			$defenseSTimeArray[$n] = $defenseSTimeArray[$m];
			$defenseETimeArray[$n] = $defenseETimeArray[$m];
			$venueArray[$n] = $venueArray[$m];
			$remarksArray[$n] = $remarksArray[$m];
			$submitStatusArray[$n] = $submitStatusArray[$m];
			$submitStatusDescArray[$n] = $submitStatusDescArray[$m];
			$sessionTypeIdArray[$n] = $sessionTypeIdArray[$m];
			$evaluationStatusArray[$n] = $evaluationStatusArray[$m];
			$n++;
		}
		$row_cnt7 = $n;
	}
	
}
else {
	$sql1="(SELECT a.id, b.id AS invitation_id, a.student_matrix_no, a.thesis_id, DATE_FORMAT(a.defense_date,'%d-%b-%Y') AS defense_date, 
	DATE_FORMAT(a.defense_stime,'%H:%i') AS defense_stime, DATE_FORMAT(a.defense_etime,'%H:%i') AS defense_etime, a.venue, a.remarks,
	b.submit_status, c.description AS submit_status_desc, a.ref_session_type_id, f.status AS evaluation_status
	FROM  pg_calendar a
	LEFT JOIN pg_invitation b ON (b.pg_calendar_id = a.id)
	LEFT JOIN ref_proposal_status c ON (c.id = b.submit_status)
	LEFT JOIN ref_session_type d ON (d.id = a.ref_session_type_id)
	LEFT JOIN pg_defense e ON (e.pg_calendar_id = a.id)
	LEFT JOIN pg_evaluation f ON (f.pg_defense_id = e.id)
	WHERE a.status = 'A'
	AND a.archived_status IS NULL
	AND a.ref_session_type_id = 'DEF'
	ORDER BY a.student_matrix_no, d.seq, a.defense_date, a.defense_stime)
	UNION
	(SELECT a.id, b.id AS invitation_id, a.student_matrix_no, a.thesis_id, DATE_FORMAT(a.defense_date,'%d-%b-%Y') AS defense_date, 
	DATE_FORMAT(a.defense_stime,'%H:%i') AS defense_stime, DATE_FORMAT(a.defense_etime,'%H:%i') AS defense_etime, a.venue, a.remarks,
	b.submit_status, c.description AS submit_status_desc, a.ref_session_type_id, f.status AS evaluation_status
	FROM  pg_calendar a
	LEFT JOIN pg_invitation b ON (b.pg_calendar_id = a.id)
	LEFT JOIN ref_proposal_status c ON (c.id = b.submit_status)
	LEFT JOIN ref_session_type d ON (d.id = a.ref_session_type_id)
	LEFT JOIN pg_work e ON (e.pg_calendar_id = a.id)
	LEFT JOIN pg_work_evaluation f ON (f.pg_work_id = e.id)
	WHERE a.status = 'A'
	AND a.archived_status IS NULL
	AND a.ref_session_type_id = 'WCO'
	ORDER BY a.student_matrix_no, d.seq, a.defense_date, a.defense_stime)
	UNION
	(SELECT a.id, b.id AS invitation_id, a.student_matrix_no, a.thesis_id, DATE_FORMAT(a.defense_date,'%d-%b-%Y') AS defense_date, 
	DATE_FORMAT(a.defense_stime,'%H:%i') AS defense_stime, DATE_FORMAT(a.defense_etime,'%H:%i') AS defense_etime, a.venue, a.remarks,
	b.submit_status, c.description AS submit_status_desc, a.ref_session_type_id, f.result_status AS evaluation_status
	FROM  pg_calendar a
	LEFT JOIN pg_invitation b ON (b.pg_calendar_id = a.id)
	LEFT JOIN ref_proposal_status c ON (c.id = b.submit_status)
	LEFT JOIN ref_session_type d ON (d.id = a.ref_session_type_id)
	LEFT JOIN pg_viva e ON (e.pg_calendar_id = a.id)
	LEFT JOIN pg_evaluation_viva f ON (f.pg_viva_id = e.id)
	WHERE a.status = 'A'
	AND a.archived_status IS NULL
	AND a.ref_session_type_id = 'VIV'
	ORDER BY a.student_matrix_no, d.seq, a.defense_date, a.defense_stime)";

	$result_sql1 = $db->query($sql1); 
	$db->next_record();
	$row_cnt7 = mysql_num_rows($result_sql1);
	
	$calendarIdArray = Array();
	$invitationIdArray = Array();
	$studentMatrixNoArray = Array();
	$thesisIdArray = Array();
	$defenseDateArray = Array();
	$defenseSTimeArray = Array();
	$defenseETimeArray = Array();
	$venueArray = Array();
	$remarksArray = Array();
	$submitStatusArray = Array();
	$submitStatusDescArray = Array();
	$sessionTypeIdArray = Array();
	$evaluationStatusArray = Array();
	
	$i=0;
	do {
		$calendarIdArray[$i] = $db->f('id');
		$invitationIdArray[$i] = $db->f('invitation_id');
		$studentMatrixNoArray[$i] = $db->f('student_matrix_no');
		$thesisIdArray[$i] = $db->f('thesis_id');
		$defenseDateArray[$i] = $db->f('defense_date');
		$defenseSTimeArray[$i] = $db->f('defense_stime');
		$defenseETimeArray[$i] = $db->f('defense_etime');
		$venueArray[$i] = $db->f('venue');
		$remarksArray[$i] = $db->f('remarks');
		$submitStatusArray[$i] = $db->f('submit_status');
		$submitStatusDescArray[$i] = $db->f('submit_status_desc');
		$sessionTypeIdArray[$i] = $db->f('ref_session_type_id');
		$evaluationStatusArray[$i] = $db->f('evaluation_status');
		$i++;
	} while ($db->next_record());

}

$sql5 = "SELECT id, description 
FROM ref_session_type
WHERE status = 'A'
ORDER BY seq";
$dba->query($sql5);
$dba->next_record();

$refSessionTypeIdArray = Array();
$refSessionDescArray =Array();
$no = 0;
do {
	$refSessionTypeIdArray[$no] = $dba->f('id');
	$refSessionDescArray[$no] = $dba->f('description');
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
	<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
	<script src="../../../lib/js/jquery.min2.js"></script>
    <script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
	<script language="JavaScript" src="../../../lib/js/windowopen.js"></script>
	<script src="../../../lib/js/jquery.colorbox.js"></script>
	<script language="JavaScript" type="text/javascript" src="../../../lib/js/tooltip.js"></script>	
</head>
<body>
<script>
	$(document).ready(function(){
		  
		  $.fn.getParameterValue = function(matrixNo, studentName, thesisId) {
			  //alert(matrixNo + ' - ' + studentName);
			  document.form1.add_student_name.value = studentName;
			  document.form1.add_matrix_no.value = matrixNo;
			  document.form1.add_thesis_id.value = thesisId;
			};
		  
		   $(".select_student").colorbox({width:"90%", height:"100%", iframe:true,          
		   onClosed:function(){ 
			//location.reload(true); //uncomment this line if you want to refresh the page when child close
							
			} }); 

	  });
</script>

</head>
<body>

<SCRIPT LANGUAGE="JavaScript">

function respConfirm () {
    var confirmSubmit = confirm("Click OK if you confirm to delete else click Cancel to stay on the same page.");
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
<script>
$(function() {
	$( "#datepickerFirst" ).datepicker({
		changeMonth: true,
		changeYear: true,
		yearRange: '-100:+0',
		dateFormat: 'dd-mm-yy'
		});
});
</script>

 <?php
    if(!empty($msg)) 
	{
        foreach($msg as $err) 
		{
            echo $err;
        }
    }
?>

  <form id="form1" name="form1" method="post" enctype="multipart/form-data" onsubmit="return saveRec();">

	 <fieldset>
		<legend><strong>Evaluation Schedule Setup</strong></legend>
		<table>
			<tr>							
				<td>Please provide the requested information below to add the record:-</td>
			</tr>
		</table>
		<table width="100%">
			<tr>
				<td width="15%"><label>Session Type <span style="color:#FF0000">*</span></label></td>
				<td width="1%">:</td>
				<td width="84%">
					<select name="add_evaluation_session" id="add_evaluation_session">
					<?if ($_POST['add_evaluation_session']=="") {?>
						<option value="" selected="selected"></option>
					<? }
					else {?>
						<option value=""></option>
					<?}
					for ($i=0;$i<count($refSessionTypeIdArray);$i++) {
						if ($_POST['add_evaluation_session'] == $refSessionTypeIdArray[$i]) {
							?>
							<option value="<?=$refSessionTypeIdArray[$i]?>" selected="selected"><?=$refSessionDescArray[$i]?></option>
							<?
						}
						else {
							?>
							<option value="<?=$refSessionTypeIdArray[$i]?>"><?=$refSessionDescArray[$i]?></option>
							<?
						}
					}?>
						
					</select>
				</td>
			</tr>
			<tr>
				<td><label>Session Date <span style="color:#FF0000">*</span></label></td>
				<td>:</td>
				<td><input name="add_defense_date" type="text" id="add_defense_date" size="10" value="<?=$_POST['add_defense_date']?>"readonly=""></td>
				<?	$jscript .= "\n" . '$( "#add_defense_date" ).datepicker({
												changeMonth: true,
												changeYear: true,
												yearRange: \'-100:+0\',
												dateFormat: \'dd-M-yy\'
											});';
					 
				?>
			</tr>
			<tr>
				<td><label>Session Start Time <span style="color:#FF0000">*</span></label></td>
				<td>:</td>
				<td><select name="add_defense_stime" id="add_defense_stime">
				<option value="" selected = "selected">Select Time</option>				
				<?if ($_POST['add_defense_stime'] == "07:00") {?><option value="07:00" selected = "selected">07:00 AM</option><?} else {?><option value="07:00">07:00 AM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "07:30") {?><option value="07:30" selected = "selected">07:30 AM</option><?} else {?><option value="07:30">07:30 AM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "08:00") {?><option value="08:00" selected = "selected">08:00 AM</option><?} else {?><option value="08:00">08:00 AM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "08:30") {?><option value="08:30" selected = "selected">08:30 AM</option><?} else {?><option value="08:30">08:30 AM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "09:00") {?><option value="09:00" selected = "selected">09:00 AM</option><?} else {?><option value="09:00">09:00 AM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "09:30") {?><option value="09:30" selected = "selected">09:30 AM</option><?} else {?><option value="09:30">09:30 AM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "10:00") {?><option value="10:00" selected = "selected">10:00 AM</option><?} else {?><option value="10:00">10:00 AM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "10:30") {?><option value="10:30" selected = "selected">10:30 AM</option><?} else {?><option value="10:30">10:30 AM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "11:00") {?><option value="11:00" selected = "selected">11:00 AM</option><?} else {?><option value="11:00">11:00 AM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "11:30") {?><option value="11:30" selected = "selected">11:30 AM</option><?} else {?><option value="11:30">11:30 AM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "12:00") {?><option value="12:00" selected = "selected">12:00 PM</option><?} else {?><option value="12:00">12:00 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "12:30") {?><option value="12:30" selected = "selected">12:30 PM</option><?} else {?><option value="12:30">12:30 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "13:00") {?><option value="13:00" selected = "selected">01:00 PM</option><?} else {?><option value="13:00">01:00 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "13:30") {?><option value="13:30" selected = "selected">01:30 PM</option><?} else {?><option value="13:30">01:30 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "14:00") {?><option value="14:00" selected = "selected">02:00 PM</option><?} else {?><option value="14:00">02:00 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "14:30") {?><option value="14:30" selected = "selected">02:30 PM</option><?} else {?><option value="14:30">02:30 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "15:00") {?><option value="15:00" selected = "selected">03:00 PM</option><?} else {?><option value="15:00">03:00 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "15:30") {?><option value="15:30" selected = "selected">03:30 PM</option><?} else {?><option value="15:30">03:30 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "16:00") {?><option value="16:00" selected = "selected">04:00 PM</option><?} else {?><option value="16:00">04:00 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "16:30") {?><option value="16:30" selected = "selected">04:30 PM</option><?} else {?><option value="16:30">04:30 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "17:00") {?><option value="17:00" selected = "selected">05:00 PM</option><?} else {?><option value="17:00">05:00 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "17:30") {?><option value="17:30" selected = "selected">05:30 PM</option><?} else {?><option value="17:30">05:30 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "18:00") {?><option value="18:00" selected = "selected">06:00 PM</option><?} else {?><option value="18:00">06:00 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "18:30") {?><option value="18:30" selected = "selected">06:30 PM</option><?} else {?><option value="18:30">06:30 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "19:00") {?><option value="19:00" selected = "selected">07:00 PM</option><?} else {?><option value="19:00">07:00 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "19:30") {?><option value="19:30" selected = "selected">07:30 PM</option><?} else {?><option value="19:30">07:30 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "20:00") {?><option value="20:00" selected = "selected">08:00 PM</option><?} else {?><option value="20:00">08:00 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "20:30") {?><option value="20:30" selected = "selected">08:30 PM</option><?} else {?><option value="20:30">08:30 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "21:00") {?><option value="21:00" selected = "selected">09:00 PM</option><?} else {?><option value="21:00">09:00 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "21:30") {?><option value="21:30" selected = "selected">09:30 PM</option><?} else {?><option value="21:30">09:30 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "22:00") {?><option value="22:00" selected = "selected">10:00 PM</option><?} else {?><option value="22:00">10:00 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "22:30") {?><option value="22:30" selected = "selected">10:30 PM</option><?} else {?><option value="22:30">10:30 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "23:00") {?><option value="23:00" selected = "selected">11:00 PM</option><?} else {?><option value="23:00">11:00 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "23:30") {?><option value="23:30" selected = "selected">11:30 PM</option><?} else {?><option value="23:30">11:30 PM</option><?}?>									
				<?if ($_POST['add_defense_stime'] == "00:00") {?><option value="00:00" selected = "selected">12:00 AM</option><?} else {?><option value="00:00">12:00 AM</option><?}?>																											
				</select></td>
			</tr>
			<tr>
				<td><label>Session End Time <span style="color:#FF0000">*</span></label></td>		
				<td>:</td>				
				<td><select name="add_defense_etime" id="add_defense_etime">
				<option value="" selected = "selected">Select Time</option>				
				<?if ($_POST['add_defense_etime'] == "07:00") {?><option value="07:00" selected = "selected">07:00 AM</option><?} else {?><option value="07:00">07:00 AM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "07:30") {?><option value="07:30" selected = "selected">07:30 AM</option><?} else {?><option value="07:30">07:30 AM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "08:00") {?><option value="08:00" selected = "selected">08:00 AM</option><?} else {?><option value="08:00">08:00 AM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "08:30") {?><option value="08:30" selected = "selected">08:30 AM</option><?} else {?><option value="08:30">08:30 AM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "09:00") {?><option value="09:00" selected = "selected">09:00 AM</option><?} else {?><option value="09:00">09:00 AM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "09:30") {?><option value="09:30" selected = "selected">09:30 AM</option><?} else {?><option value="09:30">09:30 AM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "10:00") {?><option value="10:00" selected = "selected">10:00 AM</option><?} else {?><option value="10:00">10:00 AM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "10:30") {?><option value="10:30" selected = "selected">10:30 AM</option><?} else {?><option value="10:30">10:30 AM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "11:00") {?><option value="11:00" selected = "selected">11:00 AM</option><?} else {?><option value="11:00">11:00 AM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "11:30") {?><option value="11:30" selected = "selected">11:30 AM</option><?} else {?><option value="11:30">11:30 AM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "12:00") {?><option value="12:00" selected = "selected">12:00 PM</option><?} else {?><option value="12:00">12:00 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "12:30") {?><option value="12:30" selected = "selected">12:30 PM</option><?} else {?><option value="12:30">12:30 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "13:00") {?><option value="13:00" selected = "selected">01:00 PM</option><?} else {?><option value="13:00">01:00 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "13:30") {?><option value="13:30" selected = "selected">01:30 PM</option><?} else {?><option value="13:30">01:30 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "14:00") {?><option value="14:00" selected = "selected">02:00 PM</option><?} else {?><option value="14:00">02:00 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "14:30") {?><option value="14:30" selected = "selected">02:30 PM</option><?} else {?><option value="14:30">02:30 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "15:00") {?><option value="15:00" selected = "selected">03:00 PM</option><?} else {?><option value="15:00">03:00 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "15:30") {?><option value="15:30" selected = "selected">03:30 PM</option><?} else {?><option value="15:30">03:30 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "16:00") {?><option value="16:00" selected = "selected">04:00 PM</option><?} else {?><option value="16:00">04:00 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "16:30") {?><option value="16:30" selected = "selected">04:30 PM</option><?} else {?><option value="16:30">04:30 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "17:00") {?><option value="17:00" selected = "selected">05:00 PM</option><?} else {?><option value="17:00">05:00 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "17:30") {?><option value="17:30" selected = "selected">05:30 PM</option><?} else {?><option value="17:30">05:30 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "18:00") {?><option value="18:00" selected = "selected">06:00 PM</option><?} else {?><option value="18:00">06:00 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "18:30") {?><option value="18:30" selected = "selected">06:30 PM</option><?} else {?><option value="18:30">06:30 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "19:00") {?><option value="19:00" selected = "selected">07:00 PM</option><?} else {?><option value="19:00">07:00 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "19:30") {?><option value="19:30" selected = "selected">07:30 PM</option><?} else {?><option value="19:30">07:30 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "20:00") {?><option value="20:00" selected = "selected">08:00 PM</option><?} else {?><option value="20:00">08:00 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "20:30") {?><option value="20:30" selected = "selected">08:30 PM</option><?} else {?><option value="20:30">08:30 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "21:00") {?><option value="21:00" selected = "selected">09:00 PM</option><?} else {?><option value="21:00">09:00 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "21:30") {?><option value="21:30" selected = "selected">09:30 PM</option><?} else {?><option value="21:30">09:30 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "22:00") {?><option value="22:00" selected = "selected">10:00 PM</option><?} else {?><option value="22:00">10:00 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "22:30") {?><option value="22:30" selected = "selected">10:30 PM</option><?} else {?><option value="22:30">10:30 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "23:00") {?><option value="23:00" selected = "selected">11:00 PM</option><?} else {?><option value="23:00">11:00 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "23:30") {?><option value="23:30" selected = "selected">11:30 PM</option><?} else {?><option value="23:30">11:30 PM</option><?}?>									
				<?if ($_POST['add_defense_etime'] == "00:00") {?><option value="00:00" selected = "selected">12:00 AM</option><?} else {?><option value="00:00">12:00 AM</option><?}?>																											
				</select></td>
			</tr>	
			<tr>
				<td><label>Venue <span style="color:#FF0000">*</span></label></td>
				<td>:</td>
				<td><input name="add_venue" cols="40" rows="2" id="add_venue" type="text" size="50" value="<?=$_POST['add_venue']?>"></td>
			</tr>
			<tr>
				<td><label>Remarks</label></td>
				<td>:</td>
				<td><input name="add_remarks" type="text" id="add_remarks" size="50" value="<?=$_POST['add_remarks']?>"></td>
			</tr>	
			<tr>
				<td><label>Student Name<span style="color:#FF0000">*</span></label></td>
				<td>:</td>
				<td><input name="add_student_name" type="text" id="add_student_name" size="50" readonly="" value="<?=$_POST['add_student_name']?>"/>
				<a class='select_student' href="../../application/defense/select_student.php">[Select]</a></td>
			</tr>
			<tr>
				<td><label>Matrix No</label></td>
				<td>:</td>
				<td><input name="add_matrix_no" type="text" id="add_matrix_no" size="50" readonly="" value="<?=$_POST['add_matrix_no']?>"/></td>
			</tr>			
			<input name="add_thesis_id" type="hidden" id="add_thesis_id" readonly="" value="<?=$_POST['add_thesis_id']?>"/>
		</table>
		<table>
			<tr>
				<td><input type="submit" name="btnAdd" value="Add" /></td>
			</tr>
		</table>
		<table width="100%">
			<tr>
				<td width="15%"><label>Session Type</label></td>
				<td width="1%">:</td>
				<td width="84%">
					<select name="search_evaluation_session" id="search_evaluation_session">
					<?if ($_POST['search_evaluation_session']=="") {?>
						<option value="" selected="selected"></option>
					<? }
					else {?>
						<option value=""></option>
					<?}
					for ($i=0;$i<count($refSessionTypeIdArray);$i++) {
						if ($_POST['search_evaluation_session'] == $refSessionTypeIdArray[$i]) {
							?>
							<option value="<?=$refSessionTypeIdArray[$i]?>" selected="selected"><?=$refSessionDescArray[$i]?></option>
							<?
						}
						else {
							?>
							<option value="<?=$refSessionTypeIdArray[$i]?>"><?=$refSessionDescArray[$i]?></option>
							<?
						}
					}?>						
					</select>
				</td>
			</tr>
			<tr>
				<td><label>Student Name</label></td>
				<td>:</td>
				<td><input name="search_student_name" type="text" id="search_student_name" value="<?=$_POST['search_student_name']?>"></td>
			</tr>
			<tr>
				<td><label>Student Matrix No</label></td>
				<td>:</td>
				<td><input name="search_matrix_no" type="text" id="search_matrix_no" value="<?=$_POST['search_matrix_no']?>"></td>
			</tr>
		</table>
		<table>
			<tr>
				<td><input type="submit" name="btnSearch" value="Search"/> Note: If no entry is provided, it will search all.</td>
			</tr>
		</table>
		<br>
		<table>
		<tr>							
			<td>Searching Results:- <?=$row_cnt7?> record(s) found.</td></td>
		</tr>
		</table>
		<?if ($row_cnt7 <= 0) {?>
			<div id = "tabledisplay" style="overflow:auto; height:80px;">
		<?}
		else if ($row_cnt7 <= 1) {?>
			<div id = "tabledisplay" style="overflow:auto; height:150px;">
		<?}
		else if ($row_cnt7 <= 2) {?>
			<div id = "tabledisplay" style="overflow:auto; height:200px;">
		<?}
		else if ($row_cnt7 <= 3) {
			?>
			<div id = "tabledisplay" style="overflow:auto; height:250px;">
			<?
		}
		else {
			?>
			<div id = "tabledisplay" style="overflow:auto; height:300px;">
			<?
		}?>		
		<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="100%" class="thetable">
			  <tr>
				<?if ($tickAlert == true) {?>
					<th align="center" width="5%"><label><span style="color:#FF0000">Tick</span></label></td>
				<?}
				else {
					?>
					<th align="center" width="5%"><label>Tick</label></td>
					<?
				}?>
				<th width="5%"><label>No</label></td>
				<th align="left" width="15%"><label>Student</label></th>
				<th align="left" width="15%"><label>Session Type <span style="color:#FF0000">*</span></label></label></td>
				<th align="left" width="25%"><label>Session Date & Time <span style="color:#FF0000">*</span></label></th>
				<th align="left" width="15%"><label>Venue <span style="color:#FF0000">*</span></label></th>
				<th align="left" width="15%"><label>Remarks</label></th>
				<th align="left" width="5%"><label>Evaluation Committee</label></th>
				<th align="left" width="5%"><label>Invitation Status</label></th>
			  </tr>

			<?php
			
			
			if ($row_cnt7 > 0) {

				$tmp_no = 0;
				for ($k=0;$k<$row_cnt7;$k++)					
				{ 
					?>
					<input type="hidden" name="calendar_id[]" id="calendar_id" value="<?=$calendarIdArray[$k]?>"/>
					<input type="hidden" name="list_thesis_id[]" id="list_thesis_id" value="<?=$thesisIdArray[$k]?>"/>
					<input type="hidden" name="list_invitation_id[]" id="list_invitation_id" value="<?=$invitationIdArray[$k]?>"/>
					<input type="hidden" name="list_session_type_id[]" id="list_session_type_id" value="<?=$sessionTypeIdArray[$k]?>"/>
					<tr>
						<?
						if (($evaluationStatusArray[$k]=="") || ($evaluationStatusArray[$k]=="IN1")){?>
							<td align="center"><input type="checkbox" name="defense_checkbox[]" id="defense_checkbox" value="<?=$tmp_no;?>" /></td>
						<?}
						else {
							?>
							<td align="center"><input type="checkbox" name="defense_checkbox[]" id="defense_checkbox" value="<?=$tmp_no;?>"  disabled="disabled"/></td>
						<?}?>
						<td align="center"><label><a href="javascript:void(0);" onMouseOver="toolTip('<?=$calendarIdArray[$k]?>', 100)" onMouseOut="toolTip()"><?=$tmp_no+1;?>.</a></label></td>
						<?
						if (substr($student_matrix_no,0,2) != '07') {
							$dbConn = $dbc;
						}
						else {
							$dbConn = $dbc1;
						}
						$sql2 = "SELECT name
						FROM student
						where matrix_no = '".$studentMatrixNoArray[$k]."'";
						
						$result_sql2 = $dbConn->query($sql2);
						$dbConn->next_record();
						$studentName = $dbConn->f('name');
						?>
						<td><label><?=$studentName?><br/><?=$studentMatrixNoArray[$k]?></label></td>
						<input type="hidden" name="studentMatrixNo[]" id="studentMatrixNo" value="<?=$studentMatrixNoArray[$k]?>" />
						
						<td><select name="session_type_id[]" id="session_type_id">
						<?
						for ($j=0;$j<count($refSessionTypeIdArray);$j++) {
							if ($refSessionTypeIdArray[$j] == $sessionTypeIdArray[$k]) {
								?><option value="<?=$refSessionTypeIdArray[$j]?>" selected="selected"><?=$refSessionDescArray[$j];?></option><?
							}
							else {
								?><option value="<?=$refSessionTypeIdArray[$j]?>"><?=$refSessionDescArray[$j];?></option><?
							}
						}?>
						</select>
						
						</td>
						<td align="left"><input type="text" name="defense_date[]" id="defense_date<?=$tmp_no;?>" size="10" value="<?=$defenseDateArray[$k]?>" readonly=""></input><br/><br/>
						<?	$jscript .= "\n" . '$( "#defense_date' . $tmp_no . '" ).datepicker({
											changeMonth: true,
											changeYear: true,
											yearRange: \'-100:+0\',
											dateFormat: \'dd-M-yy\'
										});';
				 
						?>
							<select name="defense_stime[]" id="defense_stime">
							<?if ($defenseSTimeArray[$k] == "07:00") {?><option value="07:00" selected = "selected">07:00 AM</option><?} else {?><option value="07:00">07:00 AM</option><?}?>									
							<?if ($defenseSTimeArray[$k] == "07:30") {?><option value="07:30" selected = "selected">07:30 AM</option><?} else {?><option value="07:30">07:30 AM</option><?}?>									
							<?if ($defenseSTimeArray[$k] == "08:00") {?><option value="08:00" selected = "selected">08:00 AM</option><?} else {?><option value="08:00">08:00 AM</option><?}?>									
							<?if ($defenseSTimeArray[$k] == "08:30") {?><option value="08:30" selected = "selected">08:30 AM</option><?} else {?><option value="08:30">08:30 AM</option><?}?>									
							<?if ($defenseSTimeArray[$k] == "09:00") {?><option value="09:00" selected = "selected">09:00 AM</option><?} else {?><option value="09:00">09:00 AM</option><?}?>									
							<?if ($defenseSTimeArray[$k] == "09:30") {?><option value="09:30" selected = "selected">09:30 AM</option><?} else {?><option value="09:30">09:30 AM</option><?}?>									
							<?if ($defenseSTimeArray[$k] == "10:00") {?><option value="10:00" selected = "selected">10:00 AM</option><?} else {?><option value="10:00">10:00 AM</option><?}?>									
							<?if ($defenseSTimeArray[$k] == "10:30") {?><option value="10:30" selected = "selected">10:30 AM</option><?} else {?><option value="10:30">10:30 AM</option><?}?>									
							<?if ($defenseSTimeArray[$k] == "11:00") {?><option value="11:00" selected = "selected">11:00 AM</option><?} else {?><option value="11:00">11:00 AM</option><?}?>									
							<?if ($defenseSTimeArray[$k] == "11:30") {?><option value="11:30" selected = "selected">11:30 AM</option><?} else {?><option value="11:30">11:30 AM</option><?}?>									
							<?if ($defenseSTimeArray[$k] == "12:00") {?><option value="12:00" selected = "selected">12:00 PM</option><?} else {?><option value="12:00">12:00 PM</option><?}?>									
							<?if ($defenseSTimeArray[$k] == "12:30") {?><option value="12:30" selected = "selected">12:30 PM</option><?} else {?><option value="12:30">12:30 PM</option><?}?>									
							<?if ($defenseSTimeArray[$k] == "13:00") {?><option value="13:00" selected = "selected">01:00 PM</option><?} else {?><option value="13:00">01:00 PM</option><?}?>									
							<?if ($defenseSTimeArray[$k] == "13:30") {?><option value="13:30" selected = "selected">01:30 PM</option><?} else {?><option value="13:30">01:30 PM</option><?}?>									
							<?if ($defenseSTimeArray[$k] == "14:00") {?><option value="14:00" selected = "selected">02:00 PM</option><?} else {?><option value="14:00">02:00 PM</option><?}?>									
							<?if ($defenseSTimeArray[$k] == "14:30") {?><option value="14:30" selected = "selected">02:30 PM</option><?} else {?><option value="14:30">02:30 PM</option><?}?>									
							<?if ($defenseSTimeArray[$k] == "15:00") {?><option value="15:00" selected = "selected">03:00 PM</option><?} else {?><option value="15:00">03:00 PM</option><?}?>									
							<?if ($defenseSTimeArray[$k] == "15:30") {?><option value="15:30" selected = "selected">03:30 PM</option><?} else {?><option value="15:30">03:30 PM</option><?}?>									
							<?if ($defenseSTimeArray[$k] == "16:00") {?><option value="16:00" selected = "selected">04:00 PM</option><?} else {?><option value="16:00">04:00 PM</option><?}?>									
							<?if ($defenseSTimeArray[$k] == "16:30") {?><option value="16:30" selected = "selected">04:30 PM</option><?} else {?><option value="16:30">04:30 PM</option><?}?>									
							<?if ($defenseSTimeArray[$k] == "17:00") {?><option value="17:00" selected = "selected">05:00 PM</option><?} else {?><option value="17:00">05:00 PM</option><?}?>									
							<?if ($defenseSTimeArray[$k] == "17:30") {?><option value="17:30" selected = "selected">05:30 PM</option><?} else {?><option value="17:30">05:30 PM</option><?}?>									
							<?if ($defenseSTimeArray[$k] == "18:00") {?><option value="18:00" selected = "selected">06:00 PM</option><?} else {?><option value="18:00">06:00 PM</option><?}?>									
							<?if ($defenseSTimeArray[$k] == "18:30") {?><option value="18:30" selected = "selected">06:30 PM</option><?} else {?><option value="18:30">06:30 PM</option><?}?>									
							<?if ($defenseSTimeArray[$k] == "19:00") {?><option value="19:00" selected = "selected">07:00 PM</option><?} else {?><option value="19:00">07:00 PM</option><?}?>									
							<?if ($defenseSTimeArray[$k] == "19:30") {?><option value="19:30" selected = "selected">07:30 PM</option><?} else {?><option value="19:30">07:30 PM</option><?}?>									
							<?if ($defenseSTimeArray[$k] == "20:00") {?><option value="20:00" selected = "selected">08:00 PM</option><?} else {?><option value="20:00">08:00 PM</option><?}?>									
							<?if ($defenseSTimeArray[$k] == "20:30") {?><option value="20:30" selected = "selected">08:30 PM</option><?} else {?><option value="20:30">08:30 PM</option><?}?>									
							<?if ($defenseSTimeArray[$k] == "21:00") {?><option value="21:00" selected = "selected">09:00 PM</option><?} else {?><option value="21:00">09:00 PM</option><?}?>									
							<?if ($defenseSTimeArray[$k] == "21:30") {?><option value="21:30" selected = "selected">09:30 PM</option><?} else {?><option value="21:30">09:30 PM</option><?}?>									
							<?if ($defenseSTimeArray[$k] == "22:00") {?><option value="22:00" selected = "selected">10:00 PM</option><?} else {?><option value="22:00">10:00 PM</option><?}?>									
							<?if ($defenseSTimeArray[$k] == "22:30") {?><option value="22:30" selected = "selected">10:30 PM</option><?} else {?><option value="22:30">10:30 PM</option><?}?>									
							<?if ($defenseSTimeArray[$k] == "23:00") {?><option value="23:00" selected = "selected">11:00 PM</option><?} else {?><option value="23:00">11:00 PM</option><?}?>									
							<?if ($defenseSTimeArray[$k] == "23:30") {?><option value="23:30" selected = "selected">11:30 PM</option><?} else {?><option value="23:30">11:30 PM</option><?}?>									
							<?if ($defenseSTimeArray[$k] == "00:00") {?><option value="00:00" selected = "selected">12:00 AM</option><?} else {?><option value="00:00">12:00 AM</option><?}?>																											
							</select> to
							<select name="defense_etime[]" id="defense_etime" size="1" >
							<?if ($defenseETimeArray[$k] == "07:00") {?><option value="07:00" selected = "selected">07:00 AM</option><?} else {?><option value="07:00">07:00 AM</option><?}?>									
							<?if ($defenseETimeArray[$k] == "07:30") {?><option value="07:30" selected = "selected">07:30 AM</option><?} else {?><option value="07:30">07:30 AM</option><?}?>									
							<?if ($defenseETimeArray[$k] == "08:00") {?><option value="08:00" selected = "selected">08:00 AM</option><?} else {?><option value="08:00">08:00 AM</option><?}?>									
							<?if ($defenseETimeArray[$k] == "08:30") {?><option value="08:30" selected = "selected">08:30 AM</option><?} else {?><option value="08:30">08:30 AM</option><?}?>									
							<?if ($defenseETimeArray[$k] == "09:00") {?><option value="09:00" selected = "selected">09:00 AM</option><?} else {?><option value="09:00">09:00 AM</option><?}?>									
							<?if ($defenseETimeArray[$k] == "09:30") {?><option value="09:30" selected = "selected">09:30 AM</option><?} else {?><option value="09:30">09:30 AM</option><?}?>									
							<?if ($defenseETimeArray[$k] == "10:00") {?><option value="10:00" selected = "selected">10:00 AM</option><?} else {?><option value="10:00">10:00 AM</option><?}?>									
							<?if ($defenseETimeArray[$k] == "10:30") {?><option value="10:30" selected = "selected">10:30 AM</option><?} else {?><option value="10:30">10:30 AM</option><?}?>									
							<?if ($defenseETimeArray[$k] == "11:00") {?><option value="11:00" selected = "selected">11:00 AM</option><?} else {?><option value="11:00">11:00 AM</option><?}?>									
							<?if ($defenseETimeArray[$k] == "11:30") {?><option value="11:30" selected = "selected">11:30 AM</option><?} else {?><option value="11:30">11:30 AM</option><?}?>									
							<?if ($defenseETimeArray[$k] == "12:00") {?><option value="12:00" selected = "selected">12:00 PM</option><?} else {?><option value="12:00">12:00 PM</option><?}?>									
							<?if ($defenseETimeArray[$k] == "12:30") {?><option value="12:30" selected = "selected">12:30 PM</option><?} else {?><option value="12:30">12:30 PM</option><?}?>									
							<?if ($defenseETimeArray[$k] == "13:00") {?><option value="13:00" selected = "selected">01:00 PM</option><?} else {?><option value="13:00">01:00 PM</option><?}?>									
							<?if ($defenseETimeArray[$k] == "13:30") {?><option value="13:30" selected = "selected">01:30 PM</option><?} else {?><option value="13:30">01:30 PM</option><?}?>									
							<?if ($defenseETimeArray[$k] == "14:00") {?><option value="14:00" selected = "selected">02:00 PM</option><?} else {?><option value="14:00">02:00 PM</option><?}?>									
							<?if ($defenseETimeArray[$k] == "14:30") {?><option value="14:30" selected = "selected">02:30 PM</option><?} else {?><option value="14:30">02:30 PM</option><?}?>									
							<?if ($defenseETimeArray[$k] == "15:00") {?><option value="15:00" selected = "selected">03:00 PM</option><?} else {?><option value="15:00">03:00 PM</option><?}?>									
							<?if ($defenseETimeArray[$k] == "15:30") {?><option value="15:30" selected = "selected">03:30 PM</option><?} else {?><option value="15:30">03:30 PM</option><?}?>									
							<?if ($defenseETimeArray[$k] == "16:00") {?><option value="16:00" selected = "selected">04:00 PM</option><?} else {?><option value="16:00">04:00 PM</option><?}?>									
							<?if ($defenseETimeArray[$k] == "16:30") {?><option value="16:30" selected = "selected">04:30 PM</option><?} else {?><option value="16:30">04:30 PM</option><?}?>									
							<?if ($defenseETimeArray[$k] == "17:00") {?><option value="17:00" selected = "selected">05:00 PM</option><?} else {?><option value="17:00">05:00 PM</option><?}?>									
							<?if ($defenseETimeArray[$k] == "17:30") {?><option value="17:30" selected = "selected">05:30 PM</option><?} else {?><option value="17:30">05:30 PM</option><?}?>									
							<?if ($defenseETimeArray[$k] == "18:00") {?><option value="18:00" selected = "selected">06:00 PM</option><?} else {?><option value="18:00">06:00 PM</option><?}?>									
							<?if ($defenseETimeArray[$k] == "18:30") {?><option value="18:30" selected = "selected">06:30 PM</option><?} else {?><option value="18:30">06:30 PM</option><?}?>									
							<?if ($defenseETimeArray[$k] == "19:00") {?><option value="19:00" selected = "selected">07:00 PM</option><?} else {?><option value="19:00">07:00 PM</option><?}?>									
							<?if ($defenseETimeArray[$k] == "19:30") {?><option value="19:30" selected = "selected">07:30 PM</option><?} else {?><option value="19:30">07:30 PM</option><?}?>									
							<?if ($defenseETimeArray[$k] == "20:00") {?><option value="20:00" selected = "selected">08:00 PM</option><?} else {?><option value="20:00">08:00 PM</option><?}?>									
							<?if ($defenseETimeArray[$k] == "20:30") {?><option value="20:30" selected = "selected">08:30 PM</option><?} else {?><option value="20:30">08:30 PM</option><?}?>									
							<?if ($defenseETimeArray[$k] == "21:00") {?><option value="21:00" selected = "selected">09:00 PM</option><?} else {?><option value="21:00">09:00 PM</option><?}?>									
							<?if ($defenseETimeArray[$k] == "21:30") {?><option value="21:30" selected = "selected">09:30 PM</option><?} else {?><option value="21:30">09:30 PM</option><?}?>									
							<?if ($defenseETimeArray[$k] == "22:00") {?><option value="22:00" selected = "selected">10:00 PM</option><?} else {?><option value="22:00">10:00 PM</option><?}?>									
							<?if ($defenseETimeArray[$k] == "22:30") {?><option value="22:30" selected = "selected">10:30 PM</option><?} else {?><option value="22:30">10:30 PM</option><?}?>									
							<?if ($defenseETimeArray[$k] == "23:00") {?><option value="23:00" selected = "selected">11:00 PM</option><?} else {?><option value="23:00">11:00 PM</option><?}?>									
							<?if ($defenseETimeArray[$k] == "23:30") {?><option value="23:30" selected = "selected">11:30 PM</option><?} else {?><option value="23:30">11:30 PM</option><?}?>									
							<?if ($defenseETimeArray[$k] == "00:00") {?><option value="00:00" selected = "selected">12:00 AM</option><?} else {?><option value="00:00">12:00 AM</option><?}?>																											
							</select></td>
						<td><input name="venue[]" type="text" id="venue" value="<?=$venueArray[$k]?>" ></input></td>
						<td><input type="text" name="remarks[]" id="remarks" value="<?=$remarksArray[$k]?>"></input></td>
						<td align="center"><a href="../defense/defense_calendar_view.php?cid=<?=$calendarIdArray[$k]?>&mn=<?=$studentMatrixNoArray[$k]?>&tid=<?=$thesisIdArray[$k]?>&st=<?=$sessionTypeIdArray[$k]?>">View</a></td>	
						<input type="hidden" name="submitStatus[]" id="submitStatus" value="<?=$submitStatusArray[$k]?>" />
						<td>
						<?if ($submitStatusArray[$k]=="") {
							?>
							<label>Pending</label>
							<?
						}
						else {
							?>
							<a href="../defense/defense_calendar_invite.php?cid=<?=$calendarIdArray[$k]?>&mn=<?=$studentMatrixNoArray[$k]?>&tid=<?=$thesisIdArray[$k]?>"><?=$submitStatusDescArray[$k]?></a>
							<?
						}?></td>						
					</tr>
					<?
					$tmp_no++;
				}
			}
			else {
				?>
				<table>
					<tr>
						<td><label>No record found!</label></td>
					</tr>
				</table>
				<?
			}?> 			
		</table>
		</div>
		<br/>
		<table>
			<tr>
				<td>Notes:</td>
			</tr>
			<tr>
				<td>1. Field marks with (<span style="color:#FF0000">*</span>) is compulsory.<br/>
				2. The disabled Checkbox indicates the evaluation session has been completed.<br/>
				3. Please tick the checkbox before click Update or Delete button.<br/>
				4. Send Invitation button will be working for those who has yet to receive the invitation.
				</td>
			</tr>
		</table>
	</fieldset>
	<table>
		<tr>
			<td><input type="submit" name="btnUpdate" value="Update" /></td>
			<td><input type="submit" name="btnDelete" onClick="return respConfirm()" value="Delete" /></td>
			<td><input type="submit" name="btnInvite" value="Send Invitation" /></td>
		</tr>
	</table>

  </form>
  	<script>
		<?=$jscript;?>
	</script>
</body>
</html>





