<?php
include("../../../lib/common.php");
checkLogin();

$user_id=$_SESSION['user_id'];
$thesisId = $_GET['tid'];
$proposalId = $_GET['pid'];
$matrixNo = $_GET['mn'];
$employeeId = $_GET['eid'];

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

/*ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);*/

$sql_supervisor1 = " SELECT DATE_FORMAT(a.acceptance_date,'%d-%b-%Y') as acceptance_date
FROM pg_supervisor a 
LEFT JOIN ref_supervisor_type d ON (d.id = a.ref_supervisor_type_id) 
LEFT JOIN pg_thesis f ON (f.student_matrix_no = a.pg_student_matrix_no)
LEFT JOIN pg_proposal g ON (g.pg_thesis_id = f.id)
WHERE a.pg_student_matrix_no='$matrixNo'
AND g.pg_thesis_id = '$thesisId'
AND g.id = '$proposalId'
AND a.acceptance_status = 'ACC'
AND a.ref_supervisor_type_id in ('SV','CS','XS')
AND g.verified_status in ('APP','AWC')
AND g.status in ('APP','APC')
AND g.archived_status IS NULL
ORDER BY a.acceptance_date
LIMIT 1";

$result_sql_supervisor1 = $db->query($sql_supervisor1); //echo $sql;
$db->next_record();
$confirmAcceptanceDate = $db->f('acceptance_date');

$sql1 = "SELECT const_value
FROM base_constant
WHERE const_term = 'FIRST_MONTHLY_REPORT'";
$db->query($sql1);
$db->next_record();
$firstMonthlyReportParam = $db->f('const_value');	

$firstMonthlyReportParam = $firstMonthlyReportParam + 1;

if ($firstMonthlyReportParam == 1) {
	$expectedReport = $firstMonthlyReportParam.' month';
}
else {
	$expectedReport = $firstMonthlyReportParam.' months';
}

$firstMonthlyReport = date('M-Y', strtotime($confirmAcceptanceDate. ' '.($firstMonthlyReportParam).' month'));

if (substr($employeeIdArray[$i],0,3) != 'S07') { 
	$dbConn=$dbc; 
} 
else { 
	$dbConn=$dbc1; 
}

$sql2 = "SELECT name
FROM new_employee
WHERE empid = '$employeeId'";			

$result_sql2 = $dbConn->query($sql2); 
$dbConn->next_record();
$employeeName=$dbConn->f('name');

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


$sql_supervisor2 = "SELECT DATE_FORMAT(a.acceptance_date,'%d-%b-%Y') as acceptance_date
FROM pg_supervisor a 
LEFT JOIN ref_supervisor_type d ON (d.id = a.ref_supervisor_type_id) 
LEFT JOIN pg_thesis f ON (f.student_matrix_no = a.pg_student_matrix_no)
LEFT JOIN pg_proposal g ON (g.pg_thesis_id = f.id)
LEFT JOIN ref_role_status h ON (h.id = a.role_status) 
WHERE a.pg_student_matrix_no = '$matrixNo'
AND a.pg_thesis_id = '$thesisId'
AND a.acceptance_status = 'ACC'
AND a.ref_supervisor_type_id in ('SV','CS','XS')
AND g.verified_status in ('APP','AWC')
AND g.status in ('APP','APC')
AND g.archived_status IS NULL
AND a.status = 'A'
ORDER BY a.acceptance_date
LIMIT 1";

$result_sql_supervisor = $dba->query($sql_supervisor2);
$dba->next_record();
$myAcceptanceDate = $dba->f('acceptance_date');

	
$currMonth = date("F");// current date
$currMonth1 = date("F Y");// current date

$newReportDate = date('d-M-Y', strtotime($myAcceptanceDate. ' '.($firstMonthlyReportParam).' month'));		
$tmpNewReportDate = new DateTime($newReportDate);
$startMonth = $tmpNewReportDate->format('F');

$no=0;
$no1=0;

$reportMonth = Array(
"January" => "1",
"February" => "2",
"March" => "3",
"April" => "4",
"May" => "5",
"June" => "6",
"July" => "7",
"August" => "8",
"September" => "9",
"October" => "10",
"November" => "11",
"December" => "12"
);				

for ($i=$reportMonth[$currMonth]-$reportMonth[$startMonth];$i>=0;$i--) {
					
	$expectedMonth1 = date("F Y",strtotime("-$i months"));
	$expectedMonth = date("F",strtotime("-$i months"));
	$expectedYear = date("Y",strtotime("-$i months"));

	$sql2 = "SELECT a.id as progress_id, b.id as progress_detail_id, a.report_month, a.report_year, 
	DATE_FORMAT(b.responded_date, '%d-%b-%Y %h:%i %p') as responded_date, 
	DATE_FORMAT(a.submit_date, '%d-%b-%Y %h:%i %p') as submit_date,
	a.reference_no, b.status as progress_detail_status, c.description as progress_detail_desc
	FROM pg_progress a
	LEFT JOIN pg_progress_detail b ON (b.pg_progress_id = a.id)
	LEFT JOIN ref_proposal_status c ON (c.id = b.status)
	WHERE a.student_matrix_no = '$matrixNo'
	AND a.pg_thesis_id = '$thesisId'
	AND a.pg_proposal_id = '$proposalId'
	AND b.pg_employee_empid = '$employeeId'
	AND a.report_month = '$expectedMonth'
	AND a.report_year = '$expectedYear'
	AND a.archived_status IS NULL
	AND b.archived_status IS NULL";
	
	$result_sql2 = $dbg->query($sql2); 
	$dbg->next_record();
	
	$row_cnt2 = mysql_num_rows($result_sql2);
	if ($row_cnt2 > 0) {
		$theReportMonth = $dbg->f('report_month');
		$theReportMonth1 = date("M",strtotime("$theReportMonth"));
		$reportYear = $dbg->f('report_year');
		$respondedDate = $dbg->f('responded_date');
		$submitDate = $dbg->f('submit_date');
		$referenceNo = $dbg->f('reference_no');
		$progressId =  $dbg->f('progress_id');
		$progressDetailId =  $dbg->f('progress_detail_id');
		$progressDetailStatus = $dbg->f('progress_detail_status');
		$progressDetailDesc = $dbg->f('progress_detail_desc');						
	}
	else {
		$theReportMonth1 = "";
		$reportYear = "";
		$respondedDate = "";
		$submitDate = "";
		$referenceNo = "";
		$progressId =  "";
		$progressDetailId =  "";
		$progressDetailStatus = "";
		$progressDetailDesc = "";
	}

	$expectedMonth2 = date("M Y",strtotime("$expectedMonth1"));
	if ($expectedMonth1 == $currMonth1) {						
		
	}
	else {						
		
		
	}

}	

				
$sql_supervisor = "SELECT a.pg_employee_empid, a.ref_supervisor_type_id, d.description as supervisor_desc, 
DATE_FORMAT(a.acceptance_date,'%d-%b-%Y') as acceptance_date, h.description as role_status_desc
FROM pg_supervisor a 
LEFT JOIN ref_supervisor_type d ON (d.id = a.ref_supervisor_type_id) 
LEFT JOIN pg_thesis f ON (f.student_matrix_no = a.pg_student_matrix_no)
LEFT JOIN pg_proposal g ON (g.pg_thesis_id = f.id)
LEFT JOIN ref_role_status h ON (h.id = a.role_status) 
WHERE a.pg_employee_empid <> '$employeeId'
AND a.pg_student_matrix_no = '$matrixNo'
AND a.pg_thesis_id = '$thesisId'
AND a.acceptance_status = 'ACC'
AND a.ref_supervisor_type_id in ('SV','CS','XS')
AND g.verified_status in ('APP','AWC')
AND g.status in ('APP','APC')
AND g.archived_status IS NULL
AND a.status = 'A'
ORDER BY d.seq, a.ref_supervisor_type_id";

$result_sql_supervisor = $db_klas2->query($sql_supervisor); 

$row_cnt_supervisor = mysql_num_rows($result_sql_supervisor);
$db_klas2->next_record();
$varRecCount=0;	

$employeeIdArray = Array();
$supervisorTypeIdArray = Array();
$supervisorDescArray = Array();
$acceptanceDateArray = Array();
$roleStatusDescArray = Array();
$employeeNameArray = Array();
$departmentIdArray = Array();
$departmentNameArray = Array();
$j=0;

if ($row_cnt_supervisor>0) {

	do {
		$employeeIdArray[$j] = $db_klas2->f('pg_employee_empid');
		$supervisorTypeIdArray[$j] = $db_klas2->f('ref_supervisor_type_id');
		$supervisorDescArray[$j] = $db_klas2->f('supervisor_desc');
		$acceptanceDateArray[$j] = $db_klas2->f('acceptance_date');
		$roleStatusDescArray[$j] = $db_klas2->f('role_status_desc');
	
		$sql_employee="SELECT  b.name, c.id, c.description
			FROM new_employee b 
			LEFT JOIN dept_unit c ON (c.id = b.unit_id) 
			WHERE b.empid= '$employeeIdArray[$j]'";
			
		$result_sql_employee = $dbc->query($sql_employee);
		$dbc->next_record();
		
		$employeeNameArray[$j] = $dbc->f('name');
		$departmentIdArray[$j] = $dbc->f('id');
		$departmentNameArray[$j] = $dbc->f('description');
		$varRecCount++;

		if ($supervisorTypeIdArray[$j] == 'XS') {
		
		}
		else {
			
		}
		$j++;
		} while($db_klas2->next_record());
	}
	else {
		
	}


//require("../../lib/fpdf/fpdf.php");
require("../../../lib/fpdf/progress_report_table.php");

	function getTextBetweenTags($tag, $html, $strict=0)
	{
		$dom = new domDocument;
	
		if($strict==1)
		{
			$dom->loadXML($html);
		}
		else
		{
			$dom->loadHTML($html);
		}
	
		$dom->preserveWhiteSpace = false;
	
		$content = $dom->getElementsByTagname($tag);
	
		$out = array();
		foreach ($content as $item)
		{
			$out[] = $item->nodeValue;
		}
	
		return $out;
	}
	
	
	
	$varDetail2=array();
	$varRecCount=0;
	$varRecPgCount=0;
	$varTempsouvenirCd="";
	
	if (substr($user_id,0,2) != '07') { 
		$dbConn=$dbc; 
	} 
	else { 
		$dbConn=$dbc1; 
	}

class PDF extends FPDF
{
	function Header()
	{		
				
	}

	// Page footer
	function Footer()
	{

	}

}

//create a FPDF object
$pdf=new PDF_MC_Table_sr2("P","mm","A4");
$pdf->AliasNbPages(); 
//set font for the entire document
$pdf->SetFont('Helvetica','B',15);

//set up a page
$pdf->AddPage();
$pdf->SetDisplayMode(real,'default');
$pdf->SetXY(20,38);
$pdf->SetDrawColor(50,60,10);
$pdf->Cell(0,9,"List of Student's Monthly Progress Report",1);
$pdf->Ln(10);

	$pdf->SetFont('Arial','B',9);
	$pdf->SetWidths(array(35,145));
	$pdf->Row1(array("Staff ID",$employeeId));
	
	$pdf->SetFont('Arial','B',9);
	$pdf->SetWidths(array(35,145));
	$pdf->Row1(array("Supervisor / Co-Supervisor Name",$employeeName));
	
	$pdf->SetFont('Arial','B',9);
	$pdf->SetWidths(array(35,145));
	$pdf->Row1(array("Matrix No",$matrixNo));
	
	$pdf->SetFont('Arial','B',9);
	$pdf->SetWidths(array(35,145));
	$pdf->Row1(array("Student Name",$studentName));
	
	//////////////////////////List of Accepted Monthly Progress Report///////////////////////////////	
	$pdf->SetXY(20,78);
	$pdf->SetFont("Arial","B","11");
	$pdf->Cell(70,7,"List of Accepted Monthly Progress Report",0,0);
	$pdf->Ln();
	$pdf->SetFont('Arial','B',9);
	//Table with 14 rows and 5 columns
	$pdf->SetWidths(array(10,30,30,36,27,30,17));
	$d = array("No.","Expected Monthly Progress Report","Accepted Monthly Progress Report", "Submitted Date by Student", "Reference No","Last Update by Supervisor / Co-Supervisor", "Status");
	$pdf->Row2(array("No.","Expected Monthly Progress Report","Accepted Monthly Progress Report", "Submitted Date by Student", "Reference No","Last Update by Supervisor / Co-Supervisor", "Status"), $d);
	
	$pdf->SetFont('Arial','',9);

	$sql_supervisor2 = "SELECT DATE_FORMAT(a.acceptance_date,'%d-%b-%Y') as acceptance_date
	FROM pg_supervisor a 
	LEFT JOIN ref_supervisor_type d ON (d.id = a.ref_supervisor_type_id) 
	LEFT JOIN pg_thesis f ON (f.student_matrix_no = a.pg_student_matrix_no)
	LEFT JOIN pg_proposal g ON (g.pg_thesis_id = f.id)
	LEFT JOIN ref_role_status h ON (h.id = a.role_status) 
	WHERE a.pg_student_matrix_no = '$matrixNo'
	AND a.pg_thesis_id = '$thesisId'
	AND a.acceptance_status = 'ACC'
	AND a.ref_supervisor_type_id in ('SV','CS','XS')
	AND g.verified_status in ('APP','AWC')
	AND g.status in ('APP','APC')
	AND g.archived_status IS NULL
	AND a.status = 'A'
	ORDER BY a.acceptance_date
	LIMIT 1";

	$result_sql_supervisor = $dba->query($sql_supervisor2);
	$dba->next_record();
	$myAcceptanceDate = $dba->f('acceptance_date');
	
			
	$currMonth = date("F");// current date
	$currMonth1 = date("F Y");// current date

	$newReportDate = date('d-M-Y', strtotime($myAcceptanceDate. ' '.($firstMonthlyReportParam).' month'));		
	$tmpNewReportDate = new DateTime($newReportDate);
	$startMonth = $tmpNewReportDate->format('F');
	
	$no=0;
	$no1=0;
	
	$reportMonth = Array(
		"January" => "1",
		"February" => "2",
		"March" => "3",
		"April" => "4",
		"May" => "5",
		"June" => "6",
		"July" => "7",
		"August" => "8",
		"September" => "9",
		"October" => "10",
		"November" => "11",
		"December" => "12"
	);
	
	$reportMonthReverse = Array(
		"1" => "January",
		"2"=> "February",
		"3"=> "March",
		"4"=> "April",
		"5" => "May",
		"6" => "June",
		"7" => "July",
		"8" => "August",
		"9" => "September",
		"10" => "October",
		"11" => "November",
		"12" => "December"
	);

$prevYear = date('Y', strtotime($confirmAcceptanceDate));
$currYear = date('Y');

for ($j=$prevYear;$j<=$currYear;$j++){
	if ($j==$prevYear){
		$theYear = $j;
		$theStartMonth = $reportMonth[date('F', strtotime($confirmAcceptanceDate. ' '.($firstMonthlyReportParam).' month'))];
		$theEndMonth = 12;
	}
	else if (($j>$prevYear) && ($j<$currYear)){
		$theYear = $j;
		$theStartMonth = 1;
		$theEndMonth = 12;
	}
	else if ($j==$currYear){
		$theYear = $j;
		$theStartMonth = 1;
		$theEndMonth = $reportMonth[date('F')];
	}

	for ($i=$theEndMonth-$theStartMonth;$i>=0;$i--) {
	
	$expectedMonth1 = $theEndMonth-$i;//theStartMonth
	$expectedMonth = $theEndMonth;//theEndMonth
	$expectedYear = $j;

	$sql2 = "SELECT a.id as progress_id, b.id as progress_detail_id, a.report_month, a.report_year, 
	DATE_FORMAT(b.responded_date, '%d-%b-%Y %h:%i %p') as responded_date, 
	DATE_FORMAT(a.submit_date, '%d-%b-%Y %h:%i %p') as submit_date,
	a.reference_no, b.status as progress_detail_status, c.description as progress_detail_desc
	FROM pg_progress a
	LEFT JOIN pg_progress_detail b ON (b.pg_progress_id = a.id)
	LEFT JOIN ref_proposal_status c ON (c.id = b.status)
	WHERE a.student_matrix_no = '$matrixNo'
	AND a.pg_thesis_id = '$thesisId'
	AND a.pg_proposal_id = '$proposalId'
	AND b.pg_employee_empid = '$employeeId'
	AND a.report_month = '$reportMonthReverse[$expectedMonth1]'
	AND a.report_year = '$expectedYear'
	AND a.archived_status IS NULL
	AND b.archived_status IS NULL";
	
	$result_sql2 = $dbg->query($sql2); 
	$dbg->next_record();
	
	$row_cnt2 = mysql_num_rows($result_sql2);
	if ($row_cnt2 > 0) {
		$theReportMonth = $dbg->f('report_month');
		$theReportMonth1 = date("M",strtotime("$theReportMonth"));
		$reportYear = $dbg->f('report_year');
		$respondedDate = $dbg->f('responded_date');
		$submitDate = $dbg->f('submit_date');
		$referenceNo = $dbg->f('reference_no');
		$progressId =  $dbg->f('progress_id');
		$progressDetailId =  $dbg->f('progress_detail_id');
		$progressDetailStatus = $dbg->f('progress_detail_status');
		$progressDetailDesc = $dbg->f('progress_detail_desc');						
	}
	else {
		$theReportMonth1 = "";
		$reportYear = "";
		$respondedDate = "";
		$submitDate = "";
		$referenceNo = "";
		$progressId =  "";
		$progressDetailId =  "";
		$progressDetailStatus = "";
		$progressDetailDesc = "";
	}
	$my_date = date('M Y', strtotime($reportMonthReverse[$expectedMonth1]." ".$theYear));
	$expectedMonth2 = $expectedMonth1;
	////////////// Insert data for List of Accepted Monthly Progress Report
	$pdf->Row2(array((++$no1).".",$my_date,$theReportMonth1." ".$reportYear, $submitDate,$referenceNo, $respondedDate, $progressDetailDesc),$d);	
	//}
	}
	
}

$pdf->ln();
//////////////////////////Partners///////////////////////////////	
	$pdf->SetX(20);
	$pdf->SetFont("Arial","B","11");
	$pdf->Cell(70,7,"Partners",0,0);
	$pdf->Ln();
	$pdf->SetFont('Arial','B',9);
	//Table with 14 rows and 5 columns
	$pdf->SetWidths(array(10,30,28,67,15,30));
	$d = array("No.","Role","Staff ID", "Name","Faculty", "Acceptance Date");
	$pdf->Row2(array("No.","Role","Staff ID", "Name","Faculty", "Acceptance Date"), $d);
	
	$pdf->SetFont('Arial','',9);

$sql_supervisor = "SELECT a.pg_employee_empid, a.ref_supervisor_type_id, d.description as supervisor_desc, 
DATE_FORMAT(a.acceptance_date,'%d-%b-%Y') as acceptance_date, h.description as role_status_desc
FROM pg_supervisor a 
LEFT JOIN ref_supervisor_type d ON (d.id = a.ref_supervisor_type_id) 
LEFT JOIN pg_thesis f ON (f.student_matrix_no = a.pg_student_matrix_no)
LEFT JOIN pg_proposal g ON (g.pg_thesis_id = f.id)
LEFT JOIN ref_role_status h ON (h.id = a.role_status) 
WHERE a.pg_employee_empid <> '$employeeId'
AND a.pg_student_matrix_no = '$matrixNo'
AND a.pg_thesis_id = '$thesisId'
AND a.acceptance_status = 'ACC'
AND a.ref_supervisor_type_id in ('SV','CS','XS')
AND g.verified_status in ('APP','AWC')
AND g.status in ('APP','APC')
AND g.archived_status IS NULL
AND a.status = 'A'
ORDER BY d.seq, a.ref_supervisor_type_id";

$result_sql_supervisor = $db_klas2->query($sql_supervisor); 

$row_cnt_supervisor = mysql_num_rows($result_sql_supervisor);
$db_klas2->next_record();
$varRecCount=0;	

$employeeIdArray = Array();
$supervisorTypeIdArray = Array();
$supervisorDescArray = Array();
$acceptanceDateArray = Array();
$roleStatusDescArray = Array();
$employeeNameArray = Array();
$departmentIdArray = Array();
$departmentNameArray = Array();
$j=0;

if ($row_cnt_supervisor>0) {

	do {
		$employeeIdArray[$j] = $db_klas2->f('pg_employee_empid');
		$supervisorTypeIdArray[$j] = $db_klas2->f('ref_supervisor_type_id');
		$supervisorDescArray[$j] = $db_klas2->f('supervisor_desc');
		$acceptanceDateArray[$j] = $db_klas2->f('acceptance_date');
		$roleStatusDescArray[$j] = $db_klas2->f('role_status_desc');
	
		$sql_employee="SELECT  b.name, c.id, c.description
			FROM new_employee b 
			LEFT JOIN dept_unit c ON (c.id = b.unit_id) 
			WHERE b.empid= '$employeeIdArray[$j]'";
			
		$result_sql_employee = $dbc->query($sql_employee);
		$dbc->next_record();
		
		$employeeNameArray[$j] = $dbc->f('name');
		$departmentIdArray[$j] = $dbc->f('id');
		$departmentNameArray[$j] = $dbc->f('description');
		$varRecCount++;
	////////////// Insert data for List of Accepted Monthly Progress Report
	$pdf->Row2(array($varRecCount.".",$supervisorDescArray[$j]."\n ".$roleStatusDescArray[$j],$employeeIdArray[$j], $employeeNameArray[$j],$departmentIdArray[$j], $acceptanceDateArray[$j]), $d);
		$j++;
		} while($db_klas2->next_record());
}
	

$pdf->ln();

//////////////////////////List of Accepted Monthly Progress Report - Partner(s)///////////////////////////////	
if ($row_cnt_supervisor > 0) {	
	for ($k=0;$k<$row_cnt_supervisor;$k++) {
		$no2=0;
		$pdf->SetX(20);
		$pdf->SetFont("Arial","B","11");
		$pdf->Cell(70,5,"List of Accepted Monthly Progress Report - Partner(s)",0,0);
		$pdf->Ln();
		$pdf->SetX (20);
		$pdf->SetFont("Arial","B","9");
		$pdf->Cell(0,6,"Partner Name:",0,0);
		$pdf->SetX(42);
		$pdf->SetFont("Arial", "","9");
		$pdf->Cell(0,6,"$employeeNameArray[$k]",0,0);
		$pdf->Ln();
		$pdf->SetFont('Arial','B',9);
		$pdf->SetWidths(array(10,30,30,36,27,30,17));
		$headRow = (array("No.","Expected Monthly Progress Report","Accepted Monthly Progress Report", "Submitted Date by Student", "Reference No",
		"Last Update by Supervisor / Co-Supervisor", "Status"));
		$pdf->Row2(array("No.","Expected Monthly Progress Report","Accepted Monthly Progress Report", "Submitted Date by Student", "Reference No",
		"Last Update by Supervisor / Co-Supervisor", "Status"), $headRow);
		
		$prevYear = date('Y', strtotime($confirmAcceptanceDate));
		$currYear = date('Y');

		for ($j=$prevYear;$j<=$currYear;$j++){
			if ($j==$prevYear){
				$theYear = $j;
				$theStartMonth = $reportMonth[date('F', strtotime($confirmAcceptanceDate. ' '.($firstMonthlyReportParam).' month'))];
				$theEndMonth = 12;
			}
			else if (($j>$prevYear) && ($j<$currYear)){
				$theYear = $j;
				$theStartMonth = 1;
				$theEndMonth = 12;
			}
			else if ($j==$currYear){
				$theYear = $j;
				$theStartMonth = 1;
				$theEndMonth = $reportMonth[date('F')];
			}
			for ($i=$theEndMonth-$theStartMonth;$i>=0;$i--) {
				$expectedMonth1 = $theEndMonth-$i;//theStartMonth
				$expectedMonth = $theEndMonth;//theEndMonth
				$expectedYear = $j;
				
				$sql2 = "SELECT a.id, a.report_month, a.report_year, DATE_FORMAT(b.responded_date, '%d-%b-%Y %h:%i %p') as responded_date, 
				DATE_FORMAT(a.submit_date, '%d-%b-%Y %h:%i %p') as submit_date,
				a.reference_no, b.status as progress_detail_status, c.description as progress_detail_desc
				FROM pg_progress a
				LEFT JOIN pg_progress_detail b ON (b.pg_progress_id = a.id)
				LEFT JOIN ref_proposal_status c ON (c.id = b.status)
				WHERE a.student_matrix_no = '$matrixNo'
				AND a.pg_thesis_id = '$thesisId'
				AND a.pg_proposal_id = '$proposalId'
				AND b.pg_employee_empid = '$employeeIdArray[$k]'
				AND a.report_month = '$reportMonthReverse[$expectedMonth1]'
				AND a.report_year = '$expectedYear'
				AND a.archived_status IS NULL
				AND b.archived_status IS NULL";
				
				$result_sql2 = $dbg->query($sql2); 
				$dbg->next_record();
				
				$row_cnt2 = mysql_num_rows($result_sql2);
				if ($row_cnt2 > 0) {
					$theReportMonth = $dbg->f('report_month');
					$theReportMonth1 = date("M",strtotime("$theReportMonth"));
					$reportYear = $dbg->f('report_year');
					$respondedDate = $dbg->f('responded_date');
					$submitDate = $dbg->f('submit_date');
					$referenceNo = $dbg->f('reference_no');
					$progressDetailStatus = $dbg->f('progress_detail_status');
					$progressDetailDesc = $dbg->f('progress_detail_desc');
				}
				else {
					$theReportMonth1 = "";
					$reportYear = "";
					$respondedDate = "";
					$submitDate = "";
					$referenceNo = "";
					$progressDetailDesc = "";
				}
				
				$my_date = date('M Y', strtotime($reportMonthReverse[$expectedMonth1]." ".$theYear));
				$expectedMonth2 = $expectedMonth1;
		
				////////////// Insert data for List of Accepted Monthly Progress Report
				$pdf->Row2(array((++$no2).".",$my_date,$theReportMonth1." ".$reportYear, $submitDate,$referenceNo, $respondedDate, $progressDetailDesc), $headRow);
			}
		}
	}
}
	


$pdf->Output('VIVA('.$matrixNo.').pdf','I');
close();
?>
