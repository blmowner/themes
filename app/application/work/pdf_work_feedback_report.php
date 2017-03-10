<?
include("../../../lib/common.php");
checkLogin();

$user_id=$_SESSION['user_id'];

$workFeedbackId = $_GET['wfid'];
$panelEmployeeId = $_GET['eid'];
$employeeId = $_GET['seid'];
$workAmendmentId = $_GET['waid'];
$thesisId = $_GET['tid'];
$referenceNo = $_GET['ref'];
$proposalId = $_GET['pid'];
$calendarId = $_GET['cid'];
$workMarksId = $_GET['wmid'];
$workEvaluationStatus = $_GET['wes'];
$proposedMarksId = $_GET['pmid'];

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



////Student Personal Data SQL
$sql_personal="SELECT s.student_status,s.matrix_no,s.name,s.ic_passport,s.address_aa,s.address_ab,s.city_a,s.state_a,
			s.postcode_a,s.country_a,s.handphone,s.house,s.office,s.email,s.address_bb,s.address_ba,s.city_b,s.state_b,s.postcode_b,
			s.country_b,s.xgender,sp.intake_no,sp.program_code,po.code,po.program_e,s.skype_id, sp.manage_by_whom
			FROM student s
			LEFT JOIN student_program sp ON (sp.matrix_no=s.matrix_no) 
			LEFT JOIN pro_off po ON (po.code=sp.program_code) 
			WHERE (sp.program_code LIKE 'M%' OR sp.program_code LIKE 'P%' AND sp.program_code NOT LIKE 'MBBS%')
			AND s.matrix_no = '$user_id'";
	
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
			
			/////////////////////Cohord and Intake No. SQL//////////////////			
			$sqlCohord = "SELECT semester_id, intake_no
			FROM student_program 
			WHERE matrix_no ='$user_id'";
			$resultCohord = $dbc->query($sqlCohord); 
			$dbc->next_record();
				
			$session=$dbc->f('semester_id');
			$cohord=$dbc->f('intake_no');
			
			/////////////////////Thesis Information SQL//////////////////
	
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
		WHERE pt.student_matrix_no = '$user_id'
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
			
			
			// Staff SQL
				$sql31 = "SELECT NAME as staffname 
				FROM new_employee 
				WHERE empid ='$user_id'";
			
			if (substr($user_id,0,2) != '07') { 
				$dbConnStaff= $dbc; 
			} 
			else { 
				$dbConnStaff=$dbc1; 
			}
			$result1 = $dbConnStaff->query($sql31); 
			$dbConnStaff->next_record();
			$staffname=$dbConnStaff->f('staffname');
			
				//////// supervisor SQL///////
				
$sql_main = " SELECT a.pg_employee_empid, a.ref_supervisor_type_id, d.description as supervisor_type, 
	DATE_FORMAT(a.acceptance_date,'%d-%b-%Y') as acceptance_date, h.description as role_status_desc
	FROM pg_supervisor a 
	LEFT JOIN ref_supervisor_type d ON (d.id = a.ref_supervisor_type_id) 
	LEFT JOIN pg_thesis f ON (f.student_matrix_no = a.pg_student_matrix_no)
	LEFT JOIN pg_proposal g ON (g.pg_thesis_id = f.id)
	LEFT JOIN ref_role_status h ON (h.id = a.role_status)
	WHERE a.pg_student_matrix_no='$user_id'
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

$sql_supervisor = " SELECT a.pg_employee_empid, a.ref_supervisor_type_id, d.description as supervisor_type, 
		DATE_FORMAT(a.acceptance_date,'%d-%b-%Y') as acceptance_date, h.description as role_status_desc
		FROM pg_supervisor a 
		LEFT JOIN ref_supervisor_type d ON (d.id = a.ref_supervisor_type_id) 
		LEFT JOIN pg_thesis f ON (f.student_matrix_no = a.pg_student_matrix_no)
		LEFT JOIN pg_proposal g ON (g.pg_thesis_id = f.id)
		LEFT JOIN ref_role_status h ON (h.id = a.role_status)
		WHERE a.pg_student_matrix_no='$user_id'
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
		
		///////////////////Amendments SQL//////////////
$sql1 = "SELECT a.id, a.work_feedback_detail_id, b.page_affected, b.panel_feedback, a.page_after_chg, 
		a.amendment_before_chg, a.amendment_after_chg, DATE_FORMAT(a.amendment_date,'%d-%b-%Y %h:%i%p') as amendment_date,
		DATE_FORMAT(a.comment_date,'%d-%b-%Y %h:%i%p') as comment_date, a.comment,
		DATE_FORMAT(a.verified_date,'%d-%b-%Y %h:%i%p') as verified_date, a.verified_remark,
		a.confirmed_date, a.verified_by, a.verified_status, a.status, e.description as status_desc
		FROM pg_work_amendment_detail a
		LEFT JOIN pg_work_feedback_detail b ON (b.id = a.work_feedback_detail_id)
		LEFT JOIN pg_work_feedback c ON (c.id = b.work_feedback_id)
		LEFT JOIN pg_work_amendment d ON (d.id = c.work_amendment_id)
		LEFT JOIN ref_amendment_status e ON (e.id = a.status)
		WHERE d.pg_thesis_id = '$thesisId'
		AND d.pg_proposal_id = '$proposalId'
		AND d.student_matrix_no = '$user_id'
		AND d.id = '$workAmendmentId'
		AND c.id = '$workFeedbackId'
		AND a.archived_status IS NULL
		AND b.archived_status IS NULL
		AND c.archived_status IS NULL
		AND d.archived_status IS NULL
		ORDER BY b.page_affected";
		
		$result_sql1 = $dba->query($sql1);
		$dba->next_record();
		$row_cnt1 = mysql_num_rows($result_sql1);
		
		$workAmendmentDetailIdArray = Array(); 
		$workFeedbackDetailIdArray = Array();  
		$pageAffectedArray = Array();
		$pageAfterChgArray = Array();  
		$panelFeedbackArray = Array();
		$amendmentDateArray = Array();  
		$amendmentBeforeChgArray = Array();  
		$amendmentAfterChgArray = Array(); 
		$commentArray = Array(); 
		$commentDateArray = Array();
		$confirmedDateArray = Array();  
		$confirmedStatusArray = Array();  
		$verifiedByArray = Array(); 
		$verifiedDateArray = Array();  
		$verifiedStatusArray = Array();  
		$verifiedRemarkArray = Array(); 
		$statusArray = Array();  
		$statusDescArray = Array();
		
		$i=0;
		
		do {
			$workAmendmentDetailIdArray[$i] = $dba->f('id');
			$workFeedbackDetailIdArray[$i] = $dba->f('work_feedback_detail_id');
			$pageAffectedArray[$i] = $dba->f('page_affected'); 
			$pageAfterChgArray[$i] = $dba->f('page_after_chg'); 
			$panelFeedbackArray[$i] = $dba->f('panel_feedback'); 
			$amendmentDateArray[$i] = $dba->f('amendment_date');
			$amendmentBeforeChgArray[$i] = $dba->f('amendment_before_chg'); 
			$amendmentAfterChgArray[$i] = $dba->f('amendment_after_chg'); 
			$commentArray[$i] = $dba->f('comment');
			$commentDateArray[$i] = $dba->f('comment_date');
			$confirmedDateArray[$i] = $dba->f('confirmed_date'); 
			$confirmedStatusArray[$i] = $dba->f('confirmed_status'); 
			$verifiedByArray[$i] = $dba->f('verified_by'); 
			$verifiedDateArray[$i] = $dba->f('verified_date'); 
			$verifiedStatusArray[$i] = $dba->f('verified_status'); 
			$verifiedRemarkArray[$i] = $dba->f('verified_remark'); 
			$statusArray[$i] = $dba->f('status'); 
			$statusDescArray[$i] = $dba->f('status_desc'); 
			$i++;
			
		} while ($dba->next_record());
		
		$sql7 = "SELECT name
		FROM new_employee
		WHERE empid = '$panelEmployeeId'";
		
		$dbc->query($sql7); 
		$dbc->next_record();
		
		$panelEmployeeName=$dbc->f('name');
		
		$sql5 = "SELECT name
		FROM new_employee
		WHERE empid = '$employeeId'";
		
		$dbc->query($sql5); 
		$dbc->next_record();
		
		$supervisorName=$dbc->f('name');
		
$sql3 = "SELECT id, work_feedback_id, panel_feedback, page_affected,  
DATE_FORMAT(comment_date,'%d-%b-%Y %h:%i%p') as comment_date, comment,
DATE_FORMAT(submit_date,'%d-%b-%Y %h:%i%p') as submit_date,
status as feedback_status
FROM pg_work_feedback_detail
WHERE work_feedback_id = '$workFeedbackId'
AND status = 'A'
AND archived_status IS NULL
ORDER BY page_affected";

$result_sql3 = $dba->query($sql3);
$dba->next_record();
$row_cnt3 = mysql_num_rows($result_sql3);

$feedbackIdArray = Array();
$feedbackAmendDetailIdArray = Array();
$panelFeedbackArray = Array();
$pageAffectedArray = Array();
$reviewerIdArray = Array();
$commentDateArray = Array();
$reviewedStatusArray = Array();
$reviewedStatusDescArray = Array();
$feedbackStatusArray = Array();
$feedbackStatusDescArray = Array();
$commentArray = Array();
$submitDateArray = Array();
$i=0;

do {
	$feedbackIdArray[$i] = $dba->f('id');
	$feedbackAmendDetailIdArray[$i] = $dba->f('work_feedback_id');
	$panelFeedbackArray[$i] = $dba->f('panel_feedback');
	$pageAffectedArray[$i] = $dba->f('page_affected');
	$commentDateArray[$i] = $dba->f('comment_date');
	$reviewedStatusArray[$i] = $dba->f('reviewed_status');
	$reviewedStatusDescArray[$i] = $dba->f('reviewed_status_desc');
	$feedbackStatusArray[$i] = $dba->f('feedback_status');
	$feedbackStatusDescArray[$i] = $dba->f('feedback_status_desc');
	$commentArray[$i] = $dba->f('comment');
	$submitDateArray[$i] = $dba->f('submit_date');
	$i++;
}while ($dba->next_record());
		
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
$pdf->Cell(0,9,'POSTGRADUATE WORK COMPLETION REPORT[FOR WORK COMPLETION SEMINAR]',0,'','C');
$pdf->Ln();
$pdf->SetFont('Arial','BI',10);
$pdf->Cell(0,5,'[FOR WORK COMPLETION SEMINAR]',0,'','C');
$pdf->Ln(10);
	
$pdf->SetFont('Arial','B',9);
	$pdf->SetWidths(array(35,225));
	$pdf->Row1(array("Full Name:",$studentName));

	$pdf->SetFont('Arial','B',9);
	$pdf->SetWidths(array(35,225));
	$pdf->Row1(array("Matrix Number:",$user_id));
	
	$pdf->SetFont('Arial','B',9);
	$pdf->SetWidths(array(35,225));
	$pdf->Row1(array("Cohort/ Intake:",$cohord));
	
	$pdf->SetFont('Arial','B',9);
	$pdf->SetWidths(array(35,225));
	$pdf->Row1(array("Email:",$email));
	
	$pdf->SetFont('Arial','B',9);
	$pdf->SetWidths(array(35,225));
	$pdf->Row1(array("Telephone:",$mobile));
	
	
	$pdf->SetFont('Arial','B',9);
	$pdf->SetWidths(array(35,225));
	$pdf->Row1(array("Programme",$program_code." - ".$program_e));
	
	$pdf->SetFont('Arial','B',9);
	$pdf->SetWidths(array(35,225));
	$pdf->Row1(array("Thesis's Title",$thesis_title));
	
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
	//Table with 14 rows and 5 columns

	$pdf->SetWidths(array(10,55,80,50,55));
	$pdf->Row2(array("No.","Feedback of Panel Members","Amendments based on the comment from Panel Members (Please specify the page number)", "Confirmation from Supervisor on Amendments of Thesis","Faculty Verify by and Remark"));
	$pdf->SetFont('Arial','',9);

if ($row_cnt1 > 0) {
	for ($j=0; $j<$row_cnt1; $j++)
		{ 
			$html=$panelFeedbackArray[$j];
			$tmpHTML = strip_tags($html);
			$htmlB=$commentArray[$j];
			$tmpHTMLB = strip_tags($htmlB);
			$htmlA=$amendmentAfterChgArray[$j];
			$tmpHTMLA = strip_tags($htmlA);
			$htmlV=$verifiedRemarkArray[$j];
			$tmpHTMLV = strip_tags($htmlV);
			$pdf->Row2(array(($j+1).".",$tmpHTML,"","Name: ".$supervisorName."\n(".$employeeId.")\nComments: ".$tmpHTMLB,""));	
		}
		}
		else
		{
		}
$pdf->ln();
$pdf->Output('WC_Amendment_'.$studentMatrixNo.'.pdf','I');
close();


?>