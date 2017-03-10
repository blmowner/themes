<?php
include("../../../lib/common.php");
checkLogin();
$user_id=$_SESSION['user_id'];
$thesisId = $_GET['tid']; 
$proposalId = $_GET['pid'];
$referenceNo=$_GET['rid'];
$matrixNo=$_GET['mn'];
$rolestatus=$_GET['role'];
$amendmentId=$_GET['ad'];
$mid=$_GET['mid'];
$save=$_GET['save'];

//echo $referenceNo;


if($save == '1')
{
	$msg[] = "<div class=\"success\"><span>Request Changes successfully submitted.</span></div>";
}
else if($save == '3')
{
	$msg[] = "<div class=\"success\"><span>Amendment successfully submitted.</span></div>";
}

$sqlamend1 = "SELECT id, submit_date AS submitDate FROM pg_amendment
WHERE pg_thesis_id = '$thesisId'
AND pg_proposal_id = '$proposalId'
AND id = '$amendmentId'
AND confirm_status = 'CON2'
AND STATUS = 'A'
AND confirm_by IS NOT NULL
AND student_matrix_no = '$matrixNo'";
		
$result_sqlamend1 = $dbf->query($sqlamend1); 
$dbf->next_record();
$submitDate = $dbf->f('submitDate');


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
function romanNumerals($num) 
{
    $n = intval($num);
    $res = '';
 
    /*** roman_numerals array  ***/
    $roman_numerals = array(
                'M'  => 1000,
                'CM' => 900,
                'D'  => 500,
                'CD' => 400,
                'C'  => 100,
                'XC' => 90,
                'L'  => 50,
                'XL' => 40,
                'X'  => 10,
                'IX' => 9,
                'V'  => 5,
                'IV' => 4,
                'I'  => 1);
 
    foreach ($roman_numerals as $roman => $number) 
    {
        /*** divide to get  matches ***/
        $matches = intval($n / $number);
 
        /*** assign the roman char * $matches ***/
        $res .= str_repeat($roman, $matches);
 
        /*** substract from the number ***/
        $n = $n % $number;
    }
 
    /*** return the res ***/
    return $res;
}
	
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
function runnum3($column_name, $tblname) 
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

	if (substr($user_id,0,2) != '07') { 

		$dbConn=$dbc; 
	} 
	else { 
		$dbConn=$dbc1; 
	}

//Student Personal Data SQL
$sql_personal="SELECT s.student_status,s.matrix_no,s.name,s.ic_passport,s.address_aa,s.address_ab,s.city_a,s.state_a,
		s.postcode_a,s.country_a,s.handphone,s.house,s.office,s.email,s.address_bb,s.address_ba,s.city_b,s.state_b,s.postcode_b,
		s.country_b,s.xgender,sp.intake_no,sp.program_code,po.code,po.program_e,s.skype_id, sp.manage_by_whom
		FROM student s
		LEFT JOIN student_program sp ON (sp.matrix_no=s.matrix_no) 
		LEFT JOIN pro_off po ON (po.code=sp.program_code) 
		WHERE (sp.program_code LIKE 'M%' OR sp.program_code LIKE 'P%' AND sp.program_code NOT LIKE 'MBBS%')
		AND s.matrix_no = '$matrixNo'";

		$dbConn->query($sql_personal);
		$row_personal = $dbConn->fetchArray(); //echo $sql_personal;
		
		$matrix_no=$row_personal['matrix_no'];
		$skype_id=$row_personal['skype_id'];
		$studentName=$row_personal['name'];
		$program_code=$row_personal['program_code'];
		$program_e=$row_personal['program_e'];
		$ic_passport=$row_personal['ic_passport'];
		$address_aa=$row_personal['address_aa'];
		$address_ab=$row_personal['address_ab'];
		$city_a=$row_personal['city_a'];
		$state_a=$row_personal['state_a'];
		$postcode_a=$row_personal['postcode_a'];
		$country_a=$row_personal['country_a'];
		$address_bb=$row_personal['address_bb'];
		$address_ba=$row_personal['address_ba'];
		$city_b=$row_personal['city_b'];
		$state_b=$row_personal['state_b'];
		$postcode_b=$row_personal['postcode_b'];
		$country_b=$row_personal['country_b'];
		$citizenship=$row_personal['citizenship'];
		$gender=$row_personal['xgender'];
		$intake_no=$row_personal['intake_no'];
		$mobile=$row_personal['handphone'];
		$house=$row_personal['house'];
		$office=$row_personal['office'];
		$email=$row_personal['email'];
		$student_status=$row_personal["student_status"];
		$manageby=$row_personal["manage_by_whom"];
			
			//Thesis Information SQL
	
$sql_thesis="SELECT pa.verified_by,
		pa.verified_date, pa.verified_status, pa.status,pa.thesis_title,pa.id,pt.id AS thesis_id, 
		pt.ref_thesis_status_id_proposal, pt.ref_thesis_status_id_defense, pt.ref_thesis_status_id_work, pt.ref_thesis_status_id_evaluation, 
		pt.ref_thesis_status_id_final, pt.ref_thesis_status_id_senate, rps1.description AS proposal_desc, rps2.description AS defense_desc, 
		rps3.description AS work_desc, rps4.description AS evaluation_desc, rps5.description AS final_desc, 
		rps6.description AS senate_desc, DATE_FORMAT(ppa.endorsed_date,'%d-%b-%Y') AS endorsed_date
		FROM pg_thesis pt
		LEFT JOIN pg_proposal pa ON (pa.pg_thesis_id = pt.id)
		LEFT JOIN pg_proposal_approval ppa ON (ppa.id = pa.pg_proposal_approval_id)
		LEFT JOIN ref_thesis_status rps1 ON (rps1.id = pt.ref_thesis_status_id_proposal) 
		LEFT JOIN ref_thesis_status rps2 ON (rps2.id = pt.ref_thesis_status_id_defense) 
		LEFT JOIN ref_thesis_status rps3 ON (rps3.id = pt.ref_thesis_status_id_work) 
		LEFT JOIN ref_thesis_status rps4 ON (rps4.id = pt.ref_thesis_status_id_evaluation) 
		LEFT JOIN ref_thesis_status rps5 ON (rps5.id = pt.ref_thesis_status_id_final) 
		LEFT JOIN ref_thesis_status rps6 ON (rps6.id = pt.ref_thesis_status_id_senate) 
		WHERE pt.student_matrix_no = '$matrixNo'
		AND pt.archived_status is null
		ORDER BY pa.id DESC"; 
		
		$db->query($sql_thesis);
		$row_thesis=$db->fetchArray(); //echo $sql_thesis;
		
		$cases=$row_thesis["thesis_type"];
		$introduction=$row_thesis["introduction"];
		$objective=$row_thesis["objective"];
		$description=$row_thesis["description"];
		$proposal_status=$row_thesis["status"];
		$thesis_title=$row_thesis["thesis_title"];
		$verifiedStatus=$row_thesis['verified_status'];
		$status=$row_thesis['status'];
		$thesis_id=$row_thesis['thesis_id'];
		$supervisor_id=$row_thesis['supervisor_id'];
		$supervisor_name=$row_thesis['name'];
		$hp_no=$row_thesis['hp_no'];
		$ref_thesis_status_id_proposal=$row_thesis['ref_thesis_status_id_proposal'];
		$endorsed_date=$row_thesis['endorsed_date'];
		$ref_thesis_status_id_defense=$row_thesis['ref_thesis_status_id_defense'];
		$ref_thesis_status_id_work=$row_thesis['ref_thesis_status_id_work'];
		$ref_thesis_status_id_evaluation=$row_thesis['ref_thesis_status_id_evaluation'];
		$ref_thesis_status_id_final=$row_thesis['ref_thesis_status_id_final'];
		$ref_thesis_status_id_senate=$row_thesis['ref_thesis_status_id_senate'];
		$proposal_desc=$row_thesis['proposal_desc'];
		$defense_desc=$row_thesis['defense_desc'];
		$work_desc=$row_thesis['work_desc'];
		$evaluation_desc=$row_thesis['evaluation_desc'];
		$final_desc=$row_thesis['final_desc'];
		$senate_desc=$row_thesis['senate_desc'];
		
//////// supervisor SQL///////

	$sql_supervisor = " SELECT a.pg_employee_empid, a.ref_supervisor_type_id, d.description as supervisor_type, 
		DATE_FORMAT(a.acceptance_date,'%d-%b-%Y') as acceptance_date, h.description as role_status_desc
		FROM pg_supervisor a 
		LEFT JOIN ref_supervisor_type d ON (d.id = a.ref_supervisor_type_id) 
		LEFT JOIN pg_thesis f ON (f.student_matrix_no = a.pg_student_matrix_no)
		LEFT JOIN pg_proposal g ON (g.pg_thesis_id = f.id)
		LEFT JOIN ref_role_status h ON (h.id = a.role_status)
		WHERE a.pg_student_matrix_no='$matrixNo'
		AND g.pg_thesis_id = '$thesisId'
		AND g.id = '$proposalId'
		AND a.acceptance_status = 'ACC'
		AND a.ref_supervisor_type_id in ('SV','CS','XS')
		AND g.verified_status in ('APP','AWC')
		AND g.status in ('APP','APC')
		AND g.archived_status IS NULL
		AND a.status = 'A'
		ORDER BY d.seq, a.ref_supervisor_type_id";
		
		$result_sql_supervisor = $db_klas2->query($sql_supervisor); //echo $sql;
		$db_klas2->next_record();
		$row_cnt_supervisor = mysql_num_rows($result_sql_supervisor);
		
		// Evaluation Schedule
	$sql4 = "SELECT a.id
		FROM pg_calendar a
		LEFT JOIN pg_viva b ON (b.pg_calendar_id = a.id)
		WHERE a.student_matrix_no = '$matrixNo'
		AND a.thesis_id = '$thesisId'
		AND a.ref_session_type_id = 'VIV'
		AND a.recomm_status = 'REC'
		AND a.status = 'A'
		ORDER BY a.defense_date ASC";
		
		$result_sql4 = $dbg->query($sql4); 
		$dbg->next_record();
		$row_cnt4 = mysql_num_rows($result_sql4);
		$calendarIdViva = $dbg->f('id');
		
$sql3 = "SELECT id, defense_date, DATE_FORMAT(defense_date,'%d-%b-%Y') as viva_date, 
		DATE_FORMAT(defense_stime,'%h:%i%p') as viva_stime,
		DATE_FORMAT(defense_etime,'%h:%i%p') as viva_etime, venue, recomm_status, status
		FROM pg_calendar
		WHERE student_matrix_no = '$matrixNo'
		AND thesis_id = '$thesisId'
		/*AND recomm_status = 'REC'*/
		/*AND defense_date >= DATE_ADD(DATE_FORMAT(NOW(),'%Y-%m-%d'),INTERVAL ".$defenseDurationParam." DAY)*/
		AND status = 'A'
		AND id = '$calendarIdViva'
		ORDER BY defense_date ASC";
		
		$result_sql3 = $dba->query($sql3); 
		$dba->next_record();
		
		$recommendedId = $dba->f('id');
		$vivaDate = $dba->f('viva_date');
		$vivaSTime = $dba->f('viva_stime');
		$vivaETime = $dba->f('viva_etime');	
		$venue = $dba->f('venue');		
		$calendarStatus = $dba->f('status');
		$recommStatus = $dba->f('recomm_status');

		
		 // Call list of amendment
	 
$sqlamend1 = "SELECT confirm_status, confirm_date, pg_viva_id, confirm_by FROM pg_amendment
	WHERE pg_thesis_id = '$thesisId'
	AND pg_proposal_id = '$proposalId'
	AND reference_no = '$referenceNo'
	AND STATUS = 'A'
	AND confirm_status = 'CON2'
	AND student_matrix_no = '$matrixNo'
	AND amendment_status = 'SUB1'
	AND ref_req_no IS NULL";
		
	$result_sqlamend1 = $dbf->query($sqlamend1); 
	$dbf->next_record();

	$mainStatus= $dbf->f('confirm_status');
	$pg_viva_id= $dbf->f('pg_viva_id');
	$confirm_date= $dbf->f('confirm_date');
	$confirm_by= $dbf->f('confirm_by');
		 
// Call list of amendment
	
$sqlamend = "SELECT a.id,a.amendment_by_examiner AS amendment, a.feedback_by_examiner, a.amendment_detail_status, 
	a.amendment_confirm_status As amendmentConfirmStatus, faculty_remark as  facultyRemark
	FROM pg_amendment_detail a
	WHERE a.pg_amendment_id = '$amendmentId'
	AND a.pg_thesis_id = '$thesisId'
	AND a.student_matrix_no = '$matrixNo'
	AND a.amendment_detail_status = 'SUB1' 
	AND a.confirm_by IS NOT NULL
	AND a.confirm_date IS NOT NULL
	AND a.amendment_confirm_status IN ('CON', 'CON1')
	AND a.status = 'A'";
	
	$result_sqlamend = $dbb->query($sqlamend); 
	$dbb->next_record();
	$row_cnt5 = mysql_num_rows($result_sqlamend);
	$i= 0;
	$inc= 0;
	$amendmentArray = array();
	$feedbackByExaminerArray = array();
	$idArray = array();
	$amendment_detail_statusArray = array();
	$commentArray = array();
	$commentIdArray =array();
	$confirmStatusArray =array();
	$amendmentConfirmStatusArray =array();
	$facultyRemark =array();
	
	do{
		$id = $dbb->f('id');
		$amendment = $dbb->f('amendment');
		$feedbackByExaminer = $dbb->f('feedback_by_examiner');
		$amendment_detail_status = $dbb->f('amendment_detail_status');
		$commentArray[$i]=$dbb->f('comment');
		$commentIdArray[$i]=$dbb->f('commentId');
		$confirmStatusArray[$i]=$dbb->f('confirm_status');
		$amendmentConfirmStatusArray[$i]=$dbb->f('amendmentConfirmStatus');
		$facultyRemark[$i]=$dbb->f('facultyRemark');
		
		$idArray[$i] = $id;
		$amendmentArray[$i] =$amendment;
		$feedbackByExaminerArray[$i] =$feedbackByExaminer;
		$amendment_detail_statusArray[$i] =$amendment_detail_status;
		
		$inc++;
		$i++;

	}while($dbb->next_record());
	$sql_falcuty = "SELECT remark_by
		FROM pg_amendment
		WHERE pg_thesis_id = '$thesisId'
		AND pg_proposal_id = '$proposalId'
		AND id = '$amendmentId'
		AND confirm_status = 'CON2'
		AND STATUS = 'A'
		AND confirm_by IS NOT NULL
		AND student_matrix_no = '$matrixNo'";
		
	$result_sql_falcuty = $db->query($sql_falcuty); 
		$db->next_record();
		$fal_id = $db->f('remark_by');
		
		$sql_employee="SELECT  b.name, c.id, c.description
		FROM new_employee b 
		LEFT JOIN dept_unit c ON (c.id = b.unit_id) 
		WHERE b.empid= '$fal_id'";
					
	$result_sql_employee = $dbc->query($sql_employee);
	$dbc->next_record();
		
	$fal_name = $dbc->f('name');
		
//require("../../lib/fpdf/fpdf.php");
require("../../../lib/fpdf/work_amendment_report_table.php");

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
	
	if(isset($_GET['pid']) && $_GET['pid']=='')
	{
		$pid = $_GET['pid'];
	}
	
	
	$varDetail2=array();
	$varRecCount=0;
	$varRecPgCount=0;
	$varTempsouvenirCd="";
	

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
$pdf=new PDF_MC_Table_sr2("L","mm","A4");
$pdf->AliasNbPages(); 
//set font for the entire document
$pdf->SetFont('Helvetica','B',15);

//set up a page
$pdf->AddPage();
$pdf->SetDisplayMode(real,'default');

//display the title with a border around it
$pdf->SetXY(20,45);
$pdf->SetDrawColor(50,60,10);
$pdf->Cell(260,9,'AMENDMENTS ON THESIS (BASED ON VIVA REPORTS)',1);

	$pdf->Ln(15);
	$pdf->SetFont('Arial','B',9);
	$pdf->SetWidths(array(35,225));
	$pdf->Row1(array("Name of Candidate:",$studentName));

	$pdf->SetFont('Arial','B',9);
	$pdf->SetWidths(array(35,225));
	$pdf->Row1(array("Matrix Number:",$matrixNo));
	
	$pdf->SetFont('Arial','B',9);
	$pdf->SetWidths(array(35,225));
	$pdf->Row1(array("Programme:",$program_code." - ".$program_e));
	
	$pdf->SetFont('Arial','B',9);
	$pdf->SetWidths(array(35,225));
	$pdf->Row1(array("Thesis Title:",$thesis_title));

$q=0;
do{
	$pg_employee_empid = $db_klas2->f('pg_employee_empid');
	$supervisor_type = $db_klas2->f('supervisor_type');
	$role_status_desc = $db_klas2->f('role_status_desc');

	$sql_name_sp = "SELECT name from new_employee where empid = '$pg_employee_empid'";
	$dbc = $dbc;
	$result_sql_name_sp = $dbc->query($sql_name_sp); 
	$dbc->next_record();
	$spName1 = $dbc->f('name');
		if ($role_status_desc!= ""){
				$pdf->Row1(array("Name of ".$role_status_desc." ".$supervisor_type,($q+1).". ".$spName1."\n(".$pg_employee_empid.")"));
			}
		else{
				$pdf->Row1(array("Name of ".$role_status_desc."".$supervisor_type,($q+1).". ".$spName1."\n(".$pg_employee_empid.")"));
			}
$q++;	
}while($db_klas2->next_record());


//----------------------------SECTION LIST OF AMENDMENTS ------------------//
	$pdf->AddPage();
	$pdf->SetX (20);
	$pdf->SetFont("Arial","B","12");
	$pdf->Cell(70,5,"List of Amendments",0,0);
	$pdf->Ln();
	$pdf->Ln();
	$pdf->SetFont('Arial','B',9);
	
//////// main Supervisor////////
$sql_main = " SELECT a.pg_employee_empid, a.ref_supervisor_type_id, d.description as supervisor_type, 
	DATE_FORMAT(a.acceptance_date,'%d-%b-%Y') as acceptance_date, h.description as role_status_desc
	FROM pg_supervisor a 
	LEFT JOIN ref_supervisor_type d ON (d.id = a.ref_supervisor_type_id) 
	LEFT JOIN pg_thesis f ON (f.student_matrix_no = a.pg_student_matrix_no)
	LEFT JOIN pg_proposal g ON (g.pg_thesis_id = f.id)
	LEFT JOIN ref_role_status h ON (h.id = a.role_status)
	WHERE a.pg_student_matrix_no='$matrixNo'
	AND g.pg_thesis_id = '$thesisId'
	AND g.id = '$proposalId'
	AND a.acceptance_status = 'ACC'
	AND a.ref_supervisor_type_id in ('SV','CS','XS')
	AND g.verified_status in ('APP','AWC')
	AND g.status in ('APP','APC')
	AND g.archived_status IS NULL
	AND a.status = 'A'
	AND a.role_status = 'PRI'
	ORDER BY d.seq, a.ref_supervisor_type_id";
	
	$result_sql_main = $db_klas2->query($sql_main); //echo $sql;
	$db_klas2->next_record();
	$row_cnt_main = mysql_num_rows($result_sql_main);
	$empid = $db_klas2->f('pg_employee_empid');
	$employeeId = $db_klas2->f('pg_employee_empid');
	$supervisorType = $db_klas2->f('supervisor_type');
	$supervisorTypeId = $db_klas2->f('ref_supervisor_type_id');
	$acceptanceDate = $db_klas2->f('acceptance_date');
	$roleStatusDesc = $db_klas2->f('role_status_desc');
	$confirmAcceptanceDate= $db_klas2->f('acceptance_date');
	
$sql_employee="SELECT  b.name, c.id, c.description
	FROM new_employee b 
	LEFT JOIN dept_unit c ON (c.id = b.unit_id) 
	WHERE b.empid= '$employeeIdM'";
					
	$result_sql_employee = $dbc->query($sql_employee);
	$dbc->next_record();
		
	$employeeName = $dbc->f('name');
	$departmentId = $dbc->f('id');
	$departmentName = $dbc->f('description');
				
				
	$pdf->SetWidths(array(10,55,80,50,55));
	$pdf->Row2(array("No.","Feedback of Panel Members","Amendments based on the comment from Panel Members (Please specify the page number)", "Confirmation from Supervisor on Amendments of Thesis","Faculty Verify by and Remark"));
	$pdf->SetFont('Arial','',9);

if($row_cnt5>0) {
	for ($i=0; $i<$inc; $i++){ 
			
$sql13 = "SELECT a.confirm_status, a.comment, a.id AS commentId
	FROM pg_amendment_review a
	WHERE pg_amend_detail_id = '$idArray[$i]'
	AND a.empid = '$employeeId'
	AND a.comment_status IN ('SUB', 'SAV')
	AND a.status = 'A' OR a.status IS NULL";
	$db2 = $db;
	$result_sql13 = $db2->query($sql13); 
	$db2->next_record();
	$comment=$db2->f('comment');
	$commentId=$db2->f('commentId');
	$confirmStatus=$db2->f('confirm_status');
	$confirmID=$db2->f('confirmID');
	$amendStat = $dbb->f('amendStat');
	
	$sql_name_sp = "SELECT name from new_employee where empid = '$employeeId'";
			$dbc1 = $dbc;
			$result_sql_name_sp = $dbc1->query($sql_name_sp); 
			$dbc1->next_record();
			$employeeName= $dbc1->f('name');
			//$pdf->Row(array($i+1,$feedbackByExaminerArray[$i],$amendmentArray[$i],$confirmDesc,$confirmDes,""));
		
			if($amendmentConfirmStatusArray[$i] == "CON1") 
				{ 
					if ($fal_id == Null){
						$pdf->Row2(array(($i+1).".",$feedbackByExaminerArray[$i],$amendmentArray[$i],$employeeName."\n(".$employeeId.")\nRemark: ".$comment,"Remark: Not Verify"));
						}
						else
						{
						$pdf->Row2(array(($i+1).".",$feedbackByExaminerArray[$i],$amendmentArray[$i],$employeeName."\n(".$employeeId.")\nRemark: ".$comment,$fal_name."\n".$fal_id."\nRemark: ".$facultyRemark[$i]."\nStatus: Verified"));
						}
						}
			else
			{
				$pdf->Row2(array(($i+1).".",$feedbackByExaminerArray[$i],$amendmentArray[$i],"Remark: Not Confirmed", "Remark: Not Verify"));
			}
				
				
			 
			
	} 
} 
else{
	}
$pdf->ln();
$pdf->Output('List_Amendment_'.$studentMatrixNo.'.pdf','I');
close();


?>