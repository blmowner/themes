<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: reviewer_feedback_faculty.php
//
// Created by: Zuraimi
// Created Date: 24-Dec-2014
// Modified by: Zuraimi
// Modified Date: 05-Jan-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

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

session_start();
$userid=$_SESSION['user_id'];

////////////////

$sql1 = "SELECT a.id as reviewer_id, a.assigned_by, DATE_FORMAT(a.assigned_date,'%d-%b-%Y') AS assigned_date, 
DATE_FORMAT(a.acceptance_date,'%d-%b-%Y') AS acceptance_date, a.acceptance_status, g.description as acceptance_desc, 
a.acceptance_remarks,
DATE_FORMAT(a.respondedby_date,'%d-%b-%Y') AS respondedby_date, 
DATE_FORMAT(a.assigned_date,'%d-%b-%Y') AS assigned_date, 
DATE_FORMAT(f.endorsed_date,'%d-%b-%Y') AS endorsed_date, a.pg_student_matrix_no, 
a.ref_supervisor_type_id, b.description AS supervisor_type, 
e.pg_thesis_id, e.thesis_title, e.id as proposal_id, a.recipient_remarks, a.recipient_date, a.extension_reasons,
a.extension_status, a.acceptance_status, a.pg_employee_empid
FROM pg_supervisor a 
LEFT JOIN ref_supervisor_type b ON (b.id = a.ref_supervisor_type_id) 
LEFT JOIN pg_thesis d ON (d.student_matrix_no = a.pg_student_matrix_no) 
LEFT JOIN pg_proposal e ON (e.pg_thesis_id = d.id) 
LEFT JOIN pg_proposal_approval f ON (f.id = e.pg_proposal_approval_id) 
LEFT JOIN ref_acceptance_status g ON (g.id = a.acceptance_status) 
WHERE a.status = 'A' 
AND a.ref_supervisor_type_id IN ('RV')
/* AND a.acceptance_status IS NULL */
AND d.status = 'INP' 
AND e.verified_status IN ('INP','REQ') 
AND (a.extension_status IS NULL OR a.extension_status IN ('REQ','APP'))
AND e.status = 'OPN'		
AND e.archived_status is null
ORDER BY a.acceptance_date";

$result1 = $db->query($sql1); 
$db->next_record();

$reviewerIdArray = Array(); 	
$studentMatrixNoArray = Array(); 
$supervisorTypeIdArray = Array(); 
$supervisorTypeArray = Array(); 
$thesisIdArray = Array(); 
$thesisTitleArray = Array(); 						
$respondedByDateArray = Array(); 
$endorsedDateArray = Array(); 
$proposalIdArray = Array(); 
$recipientRemarksArray = Array(); 
$recipientDateArray = Array(); 
$assignedDateArray = Array(); 
$extensionReasonsArray = Array(); 
$extensionStatusArray = Array(); 
$acceptanceStatusArray = Array(); 
$acceptanceDescArray = Array(); 
$acceptanceDateArray = Array(); 
$staffIdArray = Array(); 

$no1=0;
$no2=0;
$no3=0;
do {
	$reviewerIdArray[$no1] = $db->f('reviewer_id');	
	$studentMatrixNoArray[$no1] = $db->f('pg_student_matrix_no');
	$supervisorTypeIdArray[$no1] = $db->f('ref_supervisor_type_id');
	$supervisorTypeArray[$no1] = $db->f('supervisor_type');
	$thesisIdArray[$no1] = $db->f('pg_thesis_id');
	$thesisTitleArray[$no1] = $db->f('thesis_title');						
	$respondedByDateArray[$no1] = $db->f('respondedby_date');
	$endorsedDateArray[$no1] = $db->f('endorsed_date');
	$proposalIdArray[$no1] = $db->f('proposal_id');
	$recipientRemarksArray[$no1] = trim(strip_tags($db->f('recipient_remarks')));
	$recipientDateArray[$no1] = $db->f('recipient_date');
	$assignedDateArray[$no1] = $db->f('assigned_date');
	$extensionReasonsArray[$no1] = $db->f('extension_reasons');
	$extensionStatusArray[$no1] = $db->f('extension_status');
	$acceptanceStatusArray[$no1] = $db->f('acceptance_status');
	$acceptanceDescArray[$no1] = $db->f('acceptance_desc');
	$acceptanceDateArray[$no1] = $db->f('acceptance_date');
	$staffIdArray[$no1] = $db->f('pg_employee_empid');
	$no1++;
	
} while ($db->next_record());

$studentNameArray = Array();
for ($i=0; $i<$no1; $i++){
	$sql11 = "SELECT name
		FROM student
		WHERE matrix_no = '$studentMatrixNoArray[$i]'";
	if (substr($studentMatrixNoArray[$i],0,2) != '07') { 
		$dbConnStudent= $dbc; 
	} 
	else { 
		$dbConnStudent=$dbc1; 
	}
	$result11 = $dbConnStudent->query($sql11); 
	$dbConnStudent->next_record();
	if (mysql_num_rows($result11)>0) {
		$studentNameArray[$no2] = $dbConnStudent->f('name');
		$reviewerIdArray[$no2] = $reviewerIdArray[$i];	
		$studentMatrixNoArray[$no2] = $studentMatrixNoArray[$i];
		$supervisorTypeIdArray[$no2] = $supervisorTypeIdArray[$i];
		$supervisorTypeArray[$no2] = $supervisorTypeArray[$i];
		$thesisIdArray[$no2] = $thesisIdArray[$i];
		$thesisTitleArray[$no2] = $thesisTitleArray[$i];						
		$respondedByDateArray[$no2] = $respondedByDateArray[$i];
		$endorsedDateArray[$no2] = $endorsedDateArray[$i];
		$proposalIdArray[$no2] = $proposalIdArray[$i];
		$recipientRemarksArray[$no2] = $recipientRemarksArray[$i];
		$recipientDateArray[$no2] = $recipientDateArray[$i];
		$assignedDateArray[$no2] = $assignedDateArray[$i];
		$extensionReasonsArray[$no2] = $extensionReasonsArray[$i];
		$extensionStatusArray[$no2] = $extensionStatusArray[$i];
		$acceptanceStatusArray[$no2] = $acceptanceStatusArray[$i];
		$acceptanceDescArray[$no2] = $acceptanceDescArray[$i];
		$acceptanceDateArray[$no2] = $acceptanceDateArray[$i];
		$staffIdArray[$no2] = $staffIdArray[$i];
		
		$no2++;
	}
}
$staffNameArray = Array();
for ($j=0; $j<$no2; $j++){
	$sql12 = "SELECT name as staff_name
		FROM new_employee
		WHERE empid = '$staffIdArray[$j]'";

	$result12 = $dbc->query($sql12); 
	$dbc->next_record();
	if (mysql_num_rows($result12)>0) {
		$staffNameArray[$no3] = $dbc->f('staff_name');
		$reviewerIdArray[$no3] = $reviewerIdArray[$j];	
		$studentMatrixNoArray[$no3] = $studentMatrixNoArray[$j];
		$supervisorTypeIdArray[$no3] = $supervisorTypeIdArray[$j];
		$supervisorTypeArray[$no3] = $supervisorTypeArray[$j];
		$thesisIdArray[$no3] = $thesisIdArray[$j];
		$thesisTitleArray[$no3] = $thesisTitleArray[$j];						
		$respondedByDateArray[$no3] = $respondedByDateArray[$j];
		$endorsedDateArray[$no3] = $endorsedDateArray[$j];
		$proposalIdArray[$no3] = $proposalIdArray[$j];
		$recipientRemarksArray[$no3] = $recipientRemarksArray[$j];
		$recipientDateArray[$no3] = $recipientDateArray[$j];
		$assignedDateArray[$no3] = $assignedDateArray[$j];
		$extensionReasonsArray[$no3] = $extensionReasonsArray[$j];
		$extensionStatusArray[$no3] = $extensionStatusArray[$j];
		$acceptanceStatusArray[$no3] = $acceptanceStatusArray[$j];
		$acceptanceDescArray[$no3] = $acceptanceDescArray[$j];
		$acceptanceDateArray[$no3] = $acceptanceDateArray[$j];
		$staffIdArray[$no3] = $staffIdArray[$j];
		
		$no3++;
	}
}
$row_cnt = $no3;

///////////////

if(isset($_POST['btnSubmit']) && ($_POST['btnSubmit'] <> ""))
{	
	//echo var_dump($_POST);
	$myCheckbox = $_POST['myCheckbox'];
	$myReviewerId = $_POST['myReviewerId'];
	$myStudentMatrixNo = $_SESSION['myStudentMatrixNo'];
	$myRespondedByDate = $_POST['myRespondedByDate'];

	$curdatetime = date("Y-m-d H:i:s");
	if (sizeof($_POST['myCheckbox'])> 0) {
		while (list ($key,$val) = @each ($myCheckbox)) 
		{
			/*echo "myReviewerId [".$val."] ".$myReviewerId[$val]."<br/>";
			echo "myStudentMatrixNo [".$val."] ".$myStudentMatrixNo[$val]."<br/>";
			echo "myStudentName [".$val."] ".$myStudentName[$val]."<br/>";
			echo "mySupervisorType [".$val."] ".$mySupervisorType[$val]."<br/>";
			echo "myThesisId [".$val."] ".$myThesisId[$val]."<br/>";
			echo "myThesisTitle [".$val."] ".$myThesisTitle[$val]."<br/>";
			echo "myRespondedByDate [".$val."] ".$myRespondedByDate[$val]."<br/>";*/
								
			
			$sql3 =	"UPDATE pg_supervisor 
			SET	extension_status = 'APP', respondedby_date = STR_TO_DATE('$myRespondedByDate[$val]','%d-%b-%Y'), 
			modify_by = '$userid' , modify_date = '$curdatetime' 	
			WHERE id = '$myReviewerId[$val]'
			AND pg_student_matrix_no = '$myStudentMatrixNo[$val]'  
			AND status = 'A'";

			$result3 = $dba->query($sql3); 	
			//var_dump($db);
			$dba->next_record();
		}
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please select the Reviewer first before click APPROVE EXTENSION button.</span></div>";
	}
}

if(isset($_POST['btnSearch']) && ($_POST['btnSearch'] <> "")) {
	
	$searchThesisId = $_POST['searchThesisId'];
	$searchStudent = $_POST['searchStudent'];
	$searchStaff = $_POST['searchStaff'];
	
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
	if ($searchStaff!="") 
	{
		$tmpSearchStaff = " AND a.pg_employee_empid = '$searchStaff'";
	}
	else 
	{
		$tmpSearchStaff="";
	}
	
	
	$sql1 = "SELECT a.id as reviewer_id, a.assigned_by, DATE_FORMAT(a.assigned_date,'%d-%b-%Y') AS assigned_date, 
		DATE_FORMAT(a.acceptance_date,'%d-%b-%Y') AS acceptance_date, a.acceptance_status, g.description as acceptance_desc, 
		a.acceptance_remarks,
		DATE_FORMAT(a.respondedby_date,'%d-%b-%Y') AS respondedby_date, 
		DATE_FORMAT(a.assigned_date,'%d-%b-%Y') AS assigned_date, 
		DATE_FORMAT(f.endorsed_date,'%d-%b-%Y') AS endorsed_date, a.pg_student_matrix_no, 
		a.ref_supervisor_type_id, b.description AS supervisor_type, 
		e.pg_thesis_id, e.thesis_title, e.id as proposal_id, a.recipient_remarks, a.recipient_date, a.extension_reasons,
		a.extension_status, a.acceptance_status, a.pg_employee_empid
		FROM pg_supervisor a 
		LEFT JOIN ref_supervisor_type b ON (b.id = a.ref_supervisor_type_id) 
		LEFT JOIN pg_thesis d ON (d.student_matrix_no = a.pg_student_matrix_no) 
		LEFT JOIN pg_proposal e ON (e.pg_thesis_id = d.id) 
		LEFT JOIN pg_proposal_approval f ON (f.id = e.pg_proposal_approval_id) 
		LEFT JOIN ref_acceptance_status g ON (g.id = a.acceptance_status) 
		WHERE a.status = 'A' 
		AND a.ref_supervisor_type_id IN ('RV')"
		.$tmpSearchThesisId." "
		.$tmpSearchStudent." "
		.$tmpSearchStaff." "."
		/* AND a.acceptance_status IS NULL */
		AND d.status = 'INP' 
		AND e.verified_status IN ('INP','REQ') 
		AND (a.extension_status IS NULL OR a.extension_status IN ('REQ','APP'))
		AND e.status = 'OPN'		
		AND e.archived_status is null
		ORDER BY a.acceptance_date";

		$result1 = $db->query($sql1); 
		$db->next_record();
		
		$reviewerIdArray = Array(); 	
		$studentMatrixNoArray = Array(); 
		$supervisorTypeIdArray = Array(); 
		$supervisorTypeArray = Array(); 
		$thesisIdArray = Array(); 
		$thesisTitleArray = Array(); 						
		$respondedByDateArray = Array(); 
		$endorsedDateArray = Array(); 
		$proposalIdArray = Array(); 
		$recipientRemarksArray = Array(); 
		$recipientDateArray = Array(); 
		$assignedDateArray = Array(); 
		$extensionReasonsArray = Array(); 
		$extensionStatusArray = Array(); 
		$acceptanceStatusArray = Array(); 
		$acceptanceDescArray = Array(); 
		$acceptanceDateArray = Array(); 
		$staffIdArray = Array(); 
		
		$no1=0;
		$no2=0;
		$no3=0;
		do {
			$reviewerIdArray[$no1] = $db->f('reviewer_id');	
			$studentMatrixNoArray[$no1] = $db->f('pg_student_matrix_no');
			$supervisorTypeIdArray[$no1] = $db->f('ref_supervisor_type_id');
			$supervisorTypeArray[$no1] = $db->f('supervisor_type');
			$thesisIdArray[$no1] = $db->f('pg_thesis_id');
			$thesisTitleArray[$no1] = $db->f('thesis_title');						
			$respondedByDateArray[$no1] = $db->f('respondedby_date');
			$endorsedDateArray[$no1] = $db->f('endorsed_date');
			$proposalIdArray[$no1] = $db->f('proposal_id');
			$recipientRemarksArray[$no1] = trim(strip_tags($db->f('recipient_remarks')));
			$recipientDateArray[$no1] = $db->f('recipient_date');
			$assignedDateArray[$no1] = $db->f('assigned_date');
			$extensionReasonsArray[$no1] = $db->f('extension_reasons');
			$extensionStatusArray[$no1] = $db->f('extension_status');
			$acceptanceStatusArray[$no1] = $db->f('acceptance_status');
			$acceptanceDescArray[$no1] = $db->f('acceptance_desc');
			$acceptanceDateArray[$no1] = $db->f('acceptance_date');
			$staffIdArray[$no1] = $db->f('pg_employee_empid');
			$no1++;
			
		} while ($db->next_record());
		
		$studentNameArray = Array();
		for ($i=0; $i<$no1; $i++){
			$sql11 = "SELECT name as student_name
				FROM student
				WHERE matrix_no = '$studentMatrixNoArray[$i]'";
			if (substr($studentMatrixNoArray[$i],0,2) != '07') { 
				$dbConnStudent= $dbc; 
			} 
			else { 
				$dbConnStudent=$dbc1; 
			}
			$result11 = $dbConnStudent->query($sql11); 
			$dbConnStudent->next_record();
			if (mysql_num_rows($result11)>0) {
				$studentNameArray[$no2] = $dbConnStudent->f('student_name');
				$reviewerIdArray[$no2] = $reviewerIdArray[$i];	
				$studentMatrixNoArray[$no2] = $studentMatrixNoArray[$i];
				$supervisorTypeIdArray[$no2] = $supervisorTypeIdArray[$i];
				$supervisorTypeArray[$no2] = $supervisorTypeArray[$i];
				$thesisIdArray[$no2] = $thesisIdArray[$i];
				$thesisTitleArray[$no2] = $thesisTitleArray[$i];						
				$respondedByDateArray[$no2] = $respondedByDateArray[$i];
				$endorsedDateArray[$no2] = $endorsedDateArray[$i];
				$proposalIdArray[$no2] = $proposalIdArray[$i];
				$recipientRemarksArray[$no2] = $recipientRemarksArray[$i];
				$recipientDateArray[$no2] = $recipientDateArray[$i];
				$assignedDateArray[$no2] = $assignedDateArray[$i];
				$extensionReasonsArray[$no2] = $extensionReasonsArray[$i];
				$extensionStatusArray[$no2] = $extensionStatusArray[$i];
				$acceptanceStatusArray[$no2] = $acceptanceStatusArray[$i];
				$acceptanceDescArray[$no2] = $acceptanceDescArray[$i];
				$acceptanceDateArray[$no2] = $acceptanceDateArray[$i];
				$staffIdArray[$no2] = $staffIdArray[$i];
				$no2++;
			}
		}
		$staffNameArray = Array();
		for ($j=0; $j<$no2; $j++){
			$sql12 = "SELECT name as staff_name
				FROM new_employee
				WHERE empid = '$staffIdArray[$j]'";

			$result12 = $dbc->query($sql12); 
			$dbc->next_record();
			if (mysql_num_rows($result12)>0) {
				$staffNameArray[$no3] = $dbc->f('staff_name');
				$studentNameArray[$no3] = $studentNameArray[$j];
				$reviewerIdArray[$no3] = $reviewerIdArray[$j];	
				$studentMatrixNoArray[$no3] = $studentMatrixNoArray[$j];
				$supervisorTypeIdArray[$no3] = $supervisorTypeIdArray[$j];
				$supervisorTypeArray[$no3] = $supervisorTypeArray[$j];
				$thesisIdArray[$no3] = $thesisIdArray[$j];
				$thesisTitleArray[$no3] = $thesisTitleArray[$j];						
				$respondedByDateArray[$no3] = $respondedByDateArray[$j];
				$endorsedDateArray[$no3] = $endorsedDateArray[$j];
				$proposalIdArray[$no3] = $proposalIdArray[$j];
				$recipientRemarksArray[$no3] = $recipientRemarksArray[$j];
				$recipientDateArray[$no3] = $recipientDateArray[$j];
				$assignedDateArray[$no3] = $assignedDateArray[$j];
				$extensionReasonsArray[$no3] = $extensionReasonsArray[$j];
				$extensionStatusArray[$no3] = $extensionStatusArray[$j];
				$acceptanceStatusArray[$no3] = $acceptanceStatusArray[$j];
				$acceptanceDescArray[$no3] = $acceptanceDescArray[$j];
				$acceptanceDateArray[$no3] = $acceptanceDateArray[$j];
				$staffIdArray[$no3] = $staffIdArray[$j];
				$no3++;
			}
		}
		$row_cnt = $no3;
}


if(isset($_POST['btnSearchByStudentName']) && ($_POST['btnSearchByStudentName'] <> "")) {
	
	$searchStudentName = $_POST['searchStudentName'];
	
	$sql10 = "SELECT a.id as reviewer_id, a.assigned_by, DATE_FORMAT(a.assigned_date,'%d-%b-%Y') AS assigned_date, 
		DATE_FORMAT(a.acceptance_date,'%d-%b-%Y') AS acceptance_date, a.acceptance_status, g.description as acceptance_desc, a.acceptance_remarks,
		DATE_FORMAT(a.respondedby_date,'%d-%b-%Y') AS respondedby_date, 
		DATE_FORMAT(a.assigned_date,'%d-%b-%Y') AS assigned_date, 
		DATE_FORMAT(f.endorsed_date,'%d-%b-%Y') AS endorsed_date, a.pg_student_matrix_no, 
		a.ref_supervisor_type_id, b.description AS supervisor_type,  
		e.pg_thesis_id, e.thesis_title, e.id as proposal_id, a.recipient_remarks, a.recipient_date, a.extension_reasons,
		a.extension_status, a.acceptance_status, a.pg_employee_empid
		FROM pg_supervisor a 
		LEFT JOIN ref_supervisor_type b ON (b.id = a.ref_supervisor_type_id) 
		LEFT JOIN pg_thesis d ON (d.student_matrix_no = a.pg_student_matrix_no) 
		LEFT JOIN pg_proposal e ON (e.pg_thesis_id = d.id) 
		LEFT JOIN pg_proposal_approval f ON (f.id = e.pg_proposal_approval_id) 
		LEFT JOIN ref_acceptance_status g ON (g.id = a.acceptance_status) 
		WHERE a.status = 'A' 
		AND a.ref_supervisor_type_id IN ('RV')
		/* AND a.acceptance_status IS NULL */
		AND d.status = 'INP' 
		AND e.verified_status IN ('INP','REQ') 
		AND (a.extension_status IS NULL OR a.extension_status IN ('REQ','APP'))
		AND e.status = 'OPN'		
		AND e.archived_status is null
		ORDER BY a.acceptance_date";

		$result10 = $db->query($sql10); 
		$db->next_record();
		
		$reviewerIdArray = Array(); 	
		$studentMatrixNoArray = Array(); 
		$supervisorTypeIdArray = Array(); 
		$supervisorTypeArray = Array(); 
		$thesisIdArray = Array(); 
		$thesisTitleArray = Array(); 						
		$respondedByDateArray = Array(); 
		$endorsedDateArray = Array(); 
		$proposalIdArray = Array(); 
		$recipientRemarksArray = Array(); 
		$recipientDateArray = Array(); 
		$assignedDateArray = Array(); 
		$extensionReasonsArray = Array(); 
		$extensionStatusArray = Array(); 
		$acceptanceStatusArray = Array(); 
		$acceptanceDescArray = Array(); 
		$acceptanceDateArray = Array(); 
		$staffIdArray = Array(); 
		
		$no1=0;
		$no2=0;
		$no3=0;
		do {
			$reviewerIdArray[$no1] = $db->f('reviewer_id');	
			$studentMatrixNoArray[$no1] = $db->f('pg_student_matrix_no');
			$supervisorTypeIdArray[$no1] = $db->f('ref_supervisor_type_id');
			$supervisorTypeArray[$no1] = $db->f('supervisor_type');
			$thesisIdArray[$no1] = $db->f('pg_thesis_id');
			$thesisTitleArray[$no1] = $db->f('thesis_title');						
			$respondedByDateArray[$no1] = $db->f('respondedby_date');
			$endorsedDateArray[$no1] = $db->f('endorsed_date');
			$proposalIdArray[$no1] = $db->f('proposal_id');
			$recipientRemarksArray[$no1] = $db->f('recipient_remarks');
			$recipientDateArray[$no1] = $db->f('recipient_date');
			$assignedDateArray[$no1] = $db->f('assigned_date');
			$extensionReasonsArray[$no1] = $db->f('extension_reasons');
			$extensionStatusArray[$no1] = $db->f('extension_status');
			$acceptanceStatusArray[$no1] = $db->f('acceptance_status');
			$acceptanceDescArray[$no1] = $db->f('acceptance_desc');
			$acceptanceDateArray[$no1] = $db->f('acceptance_date');
			$staffIdArray[$no1] = $db->f('pg_employee_empid');
			$no1++;
			
		} while ($db->next_record());
		
		$studentNameArray = Array();
		for ($i=0; $i<$no1; $i++){
			$sql11 = "SELECT name
				FROM student
				WHERE matrix_no = '$studentMatrixNoArray[$i]'
				AND name like '%$searchStudentName%'";
			if (substr($studentMatrixNoArray[$i],0,2) != '07') { 
				$dbConnStudent= $dbc; 
			} 
			else { 
				$dbConnStudent=$dbc1; 
			}
			$result11 = $dbConnStudent->query($sql11); 
			$dbConnStudent->next_record();
			if (mysql_num_rows($result11)>0) {
				$studentNameArray[$no2] = $dbConnStudent->f('name');
				$reviewerIdArray[$no2] = $reviewerIdArray[$i];	
				$studentMatrixNoArray[$no2] = $studentMatrixNoArray[$i];
				$supervisorTypeIdArray[$no2] = $supervisorTypeIdArray[$i];
				$supervisorTypeArray[$no2] = $supervisorTypeArray[$i];
				$thesisIdArray[$no2] = $thesisIdArray[$i];
				$thesisTitleArray[$no2] = $thesisTitleArray[$i];						
				$respondedByDateArray[$no2] = $respondedByDateArray[$i];
				$endorsedDateArray[$no2] = $endorsedDateArray[$i];
				$proposalIdArray[$no2] = $proposalIdArray[$i];
				$recipientRemarksArray[$no2] = $recipientRemarksArray[$i];
				$recipientDateArray[$no2] = $recipientDateArray[$i];
				$assignedDateArray[$no2] = $assignedDateArray[$i];
				$extensionReasonsArray[$no2] = $extensionReasonsArray[$i];
				$extensionStatusArray[$no2] = $extensionStatusArray[$i];
				$acceptanceStatusArray[$no2] = $acceptanceStatusArray[$i];
				$acceptanceDescArray[$no2] = $acceptanceDescArray[$i];
				$acceptanceDateArray[$no2] = $acceptanceDateArray[$i];
				$staffIdArray[$no2] = $staffIdArray[$i];
				$no2++;
			}
		}
		
		$staffNameArray = Array();
		for ($j=0; $j<$no2; $j++){
			$sql12 = "SELECT name as staff_name
				FROM new_employee
				WHERE empid = '$staffIdArray[$j]'";

			$result12 = $dbc->query($sql12); 
			$dbc->next_record();
			if (mysql_num_rows($result12)>0) {
				$staffNameArray[$no3] = $dbc->f('staff_name');
				$reviewerIdArray[$no3] = $reviewerIdArray[$j];	
				$studentMatrixNoArray[$no3] = $studentMatrixNoArray[$j];
				$supervisorTypeIdArray[$no3] = $supervisorTypeIdArray[$j];
				$supervisorTypeArray[$no3] = $supervisorTypeArray[$j];
				$thesisIdArray[$no3] = $thesisIdArray[$j];
				$thesisTitleArray[$no3] = $thesisTitleArray[$j];						
				$respondedByDateArray[$no3] = $respondedByDateArray[$j];
				$endorsedDateArray[$no3] = $endorsedDateArray[$j];
				$proposalIdArray[$no3] = $proposalIdArray[$j];
				$recipientRemarksArray[$no3] = $recipientRemarksArray[$j];
				$recipientDateArray[$no3] = $recipientDateArray[$j];
				$assignedDateArray[$no3] = $assignedDateArray[$j];
				$extensionReasonsArray[$no3] = $extensionReasonsArray[$j];
				$extensionStatusArray[$no3] = $extensionStatusArray[$j];
				$acceptanceStatusArray[$no3] = $acceptanceStatusArray[$j];
				$acceptanceDescArray[$no3] = $acceptanceDescArray[$j];
				$acceptanceDateArray[$no3] = $acceptanceDateArray[$j];
				$staffIdArray[$no3] = $staffIdArray[$j];
				$no3++;
			}
		}
		$row_cnt = $no3;
		
		
}

if(isset($_POST['btnSearchByStaffName']) && ($_POST['btnSearchByStaffName'] <> "")) {
	
	$searchStaffName = $_POST['searchStaffName'];

	$sql10 = "SELECT a.id as reviewer_id, a.assigned_by, DATE_FORMAT(a.assigned_date,'%d-%b-%Y') AS assigned_date, 
		DATE_FORMAT(a.acceptance_date,'%d-%b-%Y') AS acceptance_date, a.acceptance_status, g.description as acceptance_desc, a.acceptance_remarks,
		DATE_FORMAT(a.respondedby_date,'%d-%b-%Y') AS respondedby_date, 
		DATE_FORMAT(a.assigned_date,'%d-%b-%Y') AS assigned_date, 
		DATE_FORMAT(f.endorsed_date,'%d-%b-%Y') AS endorsed_date, a.pg_student_matrix_no, 
		a.ref_supervisor_type_id, b.description AS supervisor_type, 
		e.pg_thesis_id, e.thesis_title, e.id as proposal_id, a.recipient_remarks, a.recipient_date, a.extension_reasons,
		a.extension_status, a.acceptance_status, a.pg_employee_empid
		FROM pg_supervisor a 
		LEFT JOIN ref_supervisor_type b ON (b.id = a.ref_supervisor_type_id) 
		LEFT JOIN pg_thesis d ON (d.student_matrix_no = a.pg_student_matrix_no) 
		LEFT JOIN pg_proposal e ON (e.pg_thesis_id = d.id) 
		LEFT JOIN pg_proposal_approval f ON (f.id = e.pg_proposal_approval_id) 
		LEFT JOIN ref_acceptance_status g ON (g.id = a.acceptance_status) 
		WHERE a.status = 'A' 
		AND a.ref_supervisor_type_id IN ('RV')
		/* AND a.acceptance_status IS NULL */
		AND d.status = 'INP' 
		AND e.verified_status IN ('INP','REQ') 
		AND (a.extension_status IS NULL OR a.extension_status IN ('REQ','APP'))
		AND e.status = 'OPN'		
		AND e.archived_status is null
		ORDER BY a.acceptance_date";

		$result10 = $db->query($sql10); 
		$db->next_record();
		
		$reviewerIdArray = Array(); 	
		$studentMatrixNoArray = Array(); 
		$supervisorTypeIdArray = Array(); 
		$supervisorTypeArray = Array(); 
		$thesisIdArray = Array(); 
		$thesisTitleArray = Array(); 						
		$respondedByDateArray = Array(); 
		$endorsedDateArray = Array(); 
		$proposalIdArray = Array(); 
		$recipientRemarksArray = Array(); 
		$recipientDateArray = Array(); 
		$assignedDateArray = Array(); 
		$extensionReasonsArray = Array(); 
		$extensionStatusArray = Array(); 
		$acceptanceStatusArray = Array(); 
		$acceptanceDescArray = Array(); 
		$acceptanceDateArray = Array(); 
		$staffIdArray = Array(); 
		
		$no1=0;
		$no2=0;
		$no3=0;
		do {
			$reviewerIdArray[$no1] = $db->f('reviewer_id');	
			$studentMatrixNoArray[$no1] = $db->f('pg_student_matrix_no');
			$supervisorTypeIdArray[$no1] = $db->f('ref_supervisor_type_id');
			$supervisorTypeArray[$no1] = $db->f('supervisor_type');
			$thesisIdArray[$no1] = $db->f('pg_thesis_id');
			$thesisTitleArray[$no1] = $db->f('thesis_title');						
			$respondedByDateArray[$no1] = $db->f('respondedby_date');
			$endorsedDateArray[$no1] = $db->f('endorsed_date');
			$proposalIdArray[$no1] = $db->f('proposal_id');
			$recipientRemarksArray[$no1] = $db->f('recipient_remarks');
			$recipientDateArray[$no1] = $db->f('recipient_date');
			$assignedDateArray[$no1] = $db->f('assigned_date');
			$extensionReasonsArray[$no1] = $db->f('extension_reasons');
			$extensionStatusArray[$no1] = $db->f('extension_status');
			$acceptanceStatusArray[$no1] = $db->f('acceptance_status');
			$acceptanceDescArray = $db->f('acceptance_desc');
			$acceptanceDateArray[$no1] = $db->f('acceptance_date');
			$staffIdArray[$no1] = $db->f('pg_employee_empid');
			$no1++;
			
		} while ($db->next_record());
		
		$staffNameArray = Array();
		for ($i=0; $i<$no1; $i++){
			$sql11 = "SELECT name as staff_name
				FROM new_employee
				WHERE empid = '$staffIdArray[$i]'
				AND name like '%$searchStaffName%'";

			$result11 = $dbc->query($sql11); 
			$dbc->next_record();
			if (mysql_num_rows($result11)>0) {
				$staffNameArray[$no2] = $dbc->f('staff_name');
				$reviewerIdArray[$no2] = $reviewerIdArray[$i];	
				$studentMatrixNoArray[$no2] = $studentMatrixNoArray[$i];
				$supervisorTypeIdArray[$no2] = $supervisorTypeIdArray[$i];
				$supervisorTypeArray[$no2] = $supervisorTypeArray[$i];
				$thesisIdArray[$no2] = $thesisIdArray[$i];
				$thesisTitleArray[$no2] = $thesisTitleArray[$i];						
				$respondedByDateArray[$no2] = $respondedByDateArray[$i];
				$endorsedDateArray[$no2] = $endorsedDateArray[$i];
				$proposalIdArray[$no2] = $proposalIdArray[$i];
				$recipientRemarksArray[$no2] = $recipientRemarksArray[$i];
				$recipientDateArray[$no2] = $recipientDateArray[$i];
				$assignedDateArray[$no2] = $assignedDateArray[$i];
				$extensionReasonsArray[$no2] = $extensionReasonsArray[$i];
				$extensionStatusArray[$no2] = $extensionStatusArray[$i];
				$acceptanceStatusArray[$no2] = $acceptanceStatusArray[$i];
				$acceptanceDescArray[$no2] = $acceptanceDescArray[$i];
				$acceptanceDateArray[$no2] = $acceptanceDateArray[$i];
				$staffIdArray[$no2] = $staffIdArray[$i];
				$no2++;
			}
		}
		
		$studentNameArray = Array();
		for ($j=0; $j<$no2; $j++){
			$sql12 = "SELECT name
				FROM student
				WHERE matrix_no = '$studentMatrixNoArray[$j]'";
			if (substr($studentMatrixNoArray[$i],0,2) != '07') { 
				$dbConnStudent= $dbc; 
			} 
			else { 
				$dbConnStudent=$dbc1; 
			}
			$result12 = $dbConnStudent->query($sql12); 
			$dbConnStudent->next_record();
			if (mysql_num_rows($result12)>0) {
				$studentNameArray[$no3] = $dbConnStudent->f('name');
				$reviewerIdArray[$no3] = $reviewerIdArray[$j];	
				$studentMatrixNoArray[$no3] = $studentMatrixNoArray[$j];
				$supervisorTypeIdArray[$no3] = $supervisorTypeIdArray[$j];
				$supervisorTypeArray[$no3] = $supervisorTypeArray[$j];
				$thesisIdArray[$no3] = $thesisIdArray[$j];
				$thesisTitleArray[$no3] = $thesisTitleArray[$j];						
				$respondedByDateArray[$no3] = $respondedByDateArray[$j];
				$endorsedDateArray[$no3] = $endorsedDateArray[$j];
				$proposalIdArray[$no3] = $proposalIdArray[$j];
				$recipientRemarksArray[$no3] = $recipientRemarksArray[$j];
				$recipientDateArray[$no3] = $recipientDateArray[$j];
				$assignedDateArray[$no3] = $assignedDateArray[$j];
				$extensionReasonsArray[$no3] = $extensionReasonsArray[$j];
				$extensionStatusArray[$no3] = $extensionStatusArray[$j];
				$acceptanceStatusArray[$no3] = $acceptanceStatusArray[$j];
				$acceptanceDescArray[$no3] = $acceptanceDescArray[$j];
				$acceptanceDateArray[$no3] = $acceptanceDateArray[$j];
				$staffIdArray[$no3] = $staffIdArray[$j];
				$no3++;
			}
		}
		
		$row_cnt = $no3;
		
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
		<link rel="stylesheet" href="../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
		<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
		<script src="../../../lib/js/jquery.min2.js"></script>
		<script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
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
					<legend><strong>List of Thesis Proposal for Reviewer Feedback</strong></legend>
					<table>
						<tr>							
							<td><strong>Please enter searching criteria below</strong></td>
						</tr>
						<tr>
							<td><strong>Notes: </strong>(by default it will display,<br/> 
							1. All Reviewers which has been requested to provide their feedback on student's proposal. </td>
						</tr>
					</table>
					<br/>
					<table>
						<tr>
							<?$searchRequestDate = date("d-M-Y");?>
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
							<td>Staff ID</td>
							<td>:</td>
							<td><input type="text" name="searchStaff" size="15" id="searchStaff" value="<?=$searchStaff;?>"/></td>
							<td><input type="submit" name="btnSearch" value="Search" /></td>
						</tr>
						<tr>
							<td>Student Name</td>
							<td>:</td>
							<td><input type="text" name="searchStudentName" size="30" id="searchStudentName" value="<?=$searchStudentName;?>"/></td>
							<td><input type="submit" name="btnSearchByStudentName" value="Search by Student Name Only" /></td>
						</tr>
						<tr>
							<td>Reviewer Name</td>
							<td>:</td>
							<td><input type="text" name="searchStaffName" size="30" id="searchStaffName" value="<?=$searchStaffName;?>"/></td>
							<td><input type="submit" name="btnSearchByStaffName" value="Search by Reviewer Name Only" /><span style="color:#FF0000"> Note:</span> If no entry is provided, it will search all.</td>
						</tr>
						
					</table>
					<br/>
					<table>
						<tr>							
							<td>Searching Results:- <?=$row_cnt?> record(s) found.</td>
						</tr>
					</table>
				<? if($row_cnt > 6)
				{ ?>
					<div id = "tabledisplay" style="overflow:auto; height:500px;">
				<? }
				else
				{?>
					<div id = "tabledisplay" style="overflow:auto;">		
				<? }
				?>
					<table width="100%" border="1" cellpadding="3" cellspacing="1" style="border-collapse:collapse;" class="thetable">
					<tr>
						<th width="30"><strong>Tick</strong></th>
						<th width="24"><strong>No.</strong></th>
						<th width="24"><strong>Review Status</strong></th>
						<th width="24"><strong>Replied Date</strong></th>
						<th width="137"><strong>Reviewer Name</strong></th>
						<th width="137"><strong>Student Name</strong></th>
						<th width="104"><strong>Thesis / Project ID</strong></th>						
						<th width="152"><strong>Thesis / Project Title</strong></th>						
						<th width="115"><strong>Remarks</strong></th>
						<th width="152"><strong>Due Date (to reply)</strong></th>							
					</tr>
					<?
					//$row_cnt = mysql_num_rows($result1);
					if ($row_cnt>0) {?>
					<?
					$no=0;
					$jscript1="";
					for ($i=0; $i<$row_cnt; $i++){
					?>
						<tr>
							<?
							//$tmpAssignedDate = new DateTime($assignedDate);
							$tmpRespondedByDate = new DateTime($respondedByDate);
							
							$currentDate = new DateTime();	
							if ($extensionStatusArray[$i] =='REQ') {?>
								<td><input name="myCheckbox[]" type="checkbox" value="<?=$no;?>"/></td>
							<?}
							else {//$extensionStatus == 'REQ' || $acceptanceStatus == 'DNE'
								?>
								<td align="center"><input name="myCheckbox[]" type="checkbox" value="<?=$no;?>" disabled="disabled"/></td>
								<?
								
							}?>
							
							<td align="center"><?=$no+1?>.</td>
							
							<td><label name="acceptanceDesc[]" value="<?=$acceptanceDescArray[$i];?>" disabled="disabled" ><?=$acceptanceDescArray[$i]?></label></td>
							
							<td><label name="acceptanceDate[]" value="<?=$acceptanceDateArray[$i];?>" disabled="disabled" ><?=$acceptanceDateArray[$i]?></label></td>
							
							<input type="hidden" name="myReviewerId[]" size="30" id="reviewerId" value="<?=$reviewerIdArray[$i]?>">
							<?$myReviewerId[$no]=$reviewerIdArray[$i];?>						
							
							
							<?$myStudentMatrixNo[$no]=$studentMatrixNoArray[$i];?>

							<?
							/*$sql4 = "SELECT name AS staff_name
									FROM new_employee 
									WHERE empid = '$staffId'";
										
									$result4 = $dbc->query($sql4); 
									$dbc->next_record();
									$staffName=$dbc->f('staff_name');*/
						
							?>
							<td><label name="staffName[]" size="30" id="staffName" ></label><?=$staffNameArray[$i]?><br/>(<?=$staffIdArray[$i]?>)</td>
							<?$myStaffName[$no]=$staffNameArray[$i];?>
							
							<td><label name="studentName[]" size="30" id="studentName" ></label><?=$studentNameArray[$i]?><br/>(<?=$studentMatrixNoArray[$i]?>)</td>
							<?$myStudentName[$no]=$studentNameArray[$i];?>
							
							<td><a href="../reviewer/reviewer_feedback_faculty_outline.php?thesisId=<? echo $thesisIdArray[$i];?>&proposalId=<? echo $proposalIdArray[$i];?>" name="thesisId[]" title="Outline of Proposed Case Study by the Student - Read more..."><?=$thesisIdArray[$i];?></a></td>	
							
							<td><label name="thesisTitle[]" cols="30" rows="3" id="thesisTitle" ></label><?=$thesisTitleArray[$i]?></td>														
							<?$myThesisTitle[$no]=$thesisTitleArray[$i];?>
								
							<td><a href="../reviewer/reviewer_feedback_faculty_remarks.php?rid=<?=$reviewerIdArray[$i];?>&ext=N" name="acceptanceRemarks[]" value="<?=$acceptanceRemarksArray[$i]?>" title="">View Remarks</a></td>
													
							<? $myRecipientRemarks[$no]=$recipientRemarksArray[$i];?>
								
							<?
							if ($tmpRespondedByDate <= $currentDate)  //reviewer is blocked to provide feedback
							{
								if  ($extensionStatusArray[$i]=='' || $extensionStatusArray[$i]==null)
								{?>							
									<td><label name="myRespondedByDate[]" size="15" id="respondedByDate" ><?=$respondedByDate?></label></td>
								<?}
								else if ($extensionStatusArray[$i]=='REQ')
								{?>							
									<td><input type="text" name="myRespondedByDate[]" size="15" id="respondedByDate<?=$i;?>" value="<?=$respondedByDateArray[$i]?>" readonly="true"/><?	$jscript1 .= "\n" . '$( "#respondedByDate' . $i . '" ).datepicker({
													changeMonth: true,
													changeYear: true,
													yearRange: \'-100:+0\',
													dateFormat: \'dd-M-yy\'
												});';
						 
									?>
									<br/><br/><span style="color:#FF0000">Note:</span> This Reviewer has requested for additional time to provide feedback.<br/>You may change its <strong>Due Date</strong> here.
									</td>
								<?}
								else //APP
								{?>							
									<td><label name="myRespondedByDate[]" size="15" id="respondedByDate" ><?=$respondedByDateArray[$i]?></label>									
									<br/><br/><label><span style="color:#FF0000">Note:</span> Extension request has been approved with a new date</label></td>
								<?}
							}
							else 
							{
								?>
								<td><label name="myRespondedByDate[]" size="15" id="respondedByDate" ><?=$respondedByDateArray[$i]?> - Due</label></td>							
							<?}?>
							<?$myRespondedByDate[$no]=$respondedByDateArray[$i];?>	
							</tr>
							<?												
							$no=$no+1;
							}?>
						
					</table>
					</div>
					<br/>
					<table>
						<tr>
							<td><span class="style1">Note:-</span></td>
						</tr>
						<tr>
							<td>1. Please select the above proposal before proceed with the approval.</td>
						</tr>
					</table>
					<br/>
					<table>
						
						<?$_SESSION['myCheckbox'] = $myCheckbox;?>
						<?$_POST['myReviewerId'] = $myReviewerId;?>
						<?$_SESSION['myStudentMatrixNo'] = $myStudentMatrixNo;?>
						<?$_POST['myRespondedByDate'] = $myRespondedByDate;?>						
						<tr>
							<td></td>
							<td><input type="submit" name="btnSubmit" value="Approve Extension" /></td>
						</tr>
					</table>							
								
				<?
				}
				else {
					?>
					
						<table>
							<tr>
								<td>No record found.</td>
							</tr>
						</table>

					
					<?
				}				
				?>			
				</fieldset>				
		</form>
		<script>
		<?=$jscript1;?>
	</script>
	</body>
</html>