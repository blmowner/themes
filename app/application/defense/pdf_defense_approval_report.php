<?php
include("../../../lib/common.php");
checkLogin();

session_start();
$user_id=$_SESSION['user_id'];
$evaluationId=$_GET['id'];
$calendarId=$_GET['cid'];
$studentMatrixNo=$_GET['mn'];
$thesisId=$_GET['tid'];
$proposalId=$_GET['pid'];
$referenceNo=$_GET['ref'];
$referenceNoDefence=$_GET['refd'];
$supervisorTypeId=$_GET['rol'];
$defenseId=$_GET['did'];
$marksGivenAlert = false;
$overallRatingAlert = false;
//echo $user_id;
//echo $evaluationId;
//echo $_GET['id'];
//echo $studentMatrixNo;
//echo $calendarId. "----";

/*ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);*/

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

$sql2 = "SELECT b.id as evaluation_detail_id, 
DATE_FORMAT(b.responded_date, '%d-%b-%Y %h:%i %p') as responded_date,  
a.student_matrix_no, a.pg_thesis_id, a.pg_proposal_id, 
b.status as evaluation_detail_status, 
a.status as evaluation_status, c2.description as evaluation_desc,
a.insert_by, a.insert_date, a.modify_by, a.modify_date,	 
c.description as evaluation_detail_desc,
b.major_revision, b.other_comment, b.ref_defense_marks_id
FROM pg_evaluation a
LEFT JOIN pg_evaluation_detail b ON (b.pg_eval_id = a.id)
LEFT JOIN ref_proposal_status c ON (c.id = b.status)
LEFT JOIN ref_proposal_status c2 ON (c2.id = a.status)
WHERE a.id = '$evaluationId'
AND a.student_matrix_no = '$studentMatrixNo'
AND a.pg_thesis_id = '$thesisId'
AND a.pg_proposal_id = '$proposalId'
AND a.reference_no = '$referenceNo'
AND b.pg_employee_empid = '$user_id'
AND a.archived_status is null
AND b.archived_status is null";

$result2 = $dbg->query($sql2); 
$dbg->next_record();

$evaluationStatus=$dbg->f('evaluation_status');
$evaluationDesc=$dbg->f('evaluation_desc');
$evaluationDetailStatus1=$dbg->f('evaluation_detail_status');
$evaluationDetailDesc1=$dbg->f('evaluation_detail_desc');
$respondedDate=$dbg->f('responded_date');
$evaluationDetailId=$dbg->f('evaluation_detail_id');
$majorRevision1=$dbg->f('major_revision');
$otherComment1=$dbg->f('other_comment');
$refDefenseMarksId1=$dbg->f('ref_defense_marks_id');
$row_cnt2 = mysql_num_rows($result2);

// For description 
$sql2A = "SELECT a.id, a.pg_evaldetail_id, a.ref_overall_style_id, a.ref_report_rating_id, a.comments
FROM pg_evaluation_style a
LEFT JOIN ref_overall_style d ON (d.id = a.ref_overall_style_id)
LEFT JOIN pg_evaluation_detail c ON (c.id = a.pg_evaldetail_id)
LEFT JOIN pg_evaluation b ON (b.id = c.pg_eval_id)
WHERE b.id = '$evaluationId'
AND b.pg_thesis_id = '$thesisId'
AND b.pg_proposal_id = '$proposalId'
AND b.reference_no = '$referenceNo'
AND c.pg_employee_empid = '$user_id'
AND a.status = 'A'
ORDER BY d.seq";

$result2A = $dbg->query($sql2A); 
$dbg->next_record();
$row_cnt2A = mysql_num_rows($result2A);
$i=0;
$esIdArray = Array();
$esEvalDetailIdArray = Array();
$esRefOverallStyleIdArray = Array();
$esRefReportRatingIdArray = Array();
$esCommentsArray = Array();

do {
	$esIdArray[$i] = $dbg->f('id');
	$esEvalDetailIdArray[$i] = $dbg->f('pg_evaldetail_id');
	$esRefOverallStyleIdArray[$i] = $dbg->f('ref_overall_style_id');
	$esRefReportRatingIdArray[$i] = $dbg->f('ref_report_rating_id');
	$esCommentsArray[$i] = $dbg->f('comments');
	$i++;
} while ($dbg->next_record());

$sql2B = "SELECT a.id, a.pg_evaldetail_id, a.ref_overall_comments_id, a.ref_overall_rating_id
FROM pg_evaluation_overall a
LEFT JOIN pg_evaluation_detail c ON (c.id = a.pg_evaldetail_id)
LEFT JOIN pg_evaluation b ON (b.id = c.pg_eval_id)
LEFT JOIN ref_overall_comments d ON (d.id = a.ref_overall_comments_id)
WHERE b.id = '$evaluationId'
AND b.pg_thesis_id = '$thesisId'
AND b.pg_proposal_id = '$proposalId'
AND b.reference_no = '$referenceNo'
AND c.pg_employee_empid = '$user_id'
AND a.status = 'A'
ORDER BY d.seq";

$result2B = $dbg->query($sql2B); 
$dbg->next_record();
$row_cnt2B = mysql_num_rows($result2B);
$i=0;
$eoIdArray = Array();
$eoEvalDetailIdArray = Array();
$eoRefOverallCommentsIdArray = Array();
$eoRefOverallRatingIdArray = Array();

do {
	$eoIdArray[$i] = $dbg->f('id');
	$eoEvalDetailIdArray[$i] = $dbg->f('pg_evaldetail_id');
	$eoRefOverallCommentsIdArray[$i] = $dbg->f('ref_overall_comments_id');
	$eoRefOverallRatingIdArray[$i] = $dbg->f('ref_overall_rating_id');
	$i++;
} while ($dbg->next_record());
			
	

$sql_rate = "SELECT description 
	FROM ref_report_rating 
	WHERE id = 'RPR0002'";
	
	//$dbConn->query($sql_rate);
	$dbg->query($sql_rate);
	$dbg->next_record();
	$not_appropriate= $dbg->f('description');


	$sql_personal="SELECT s.student_status,s.matrix_no,s.name,s.ic_passport,s.address_aa,s.address_ab,s.city_a,s.state_a,
			s.postcode_a,s.country_a,s.handphone,s.house,s.office,s.email,s.address_bb,s.address_ba,s.city_b,s.state_b,s.postcode_b,
			s.country_b,s.xgender,sp.intake_no,sp.program_code,po.code,po.program_e,s.skype_id, sp.manage_by_whom
			FROM student s
			LEFT JOIN student_program sp ON (sp.matrix_no=s.matrix_no) 
			LEFT JOIN pro_off po ON (po.code=sp.program_code) 
			WHERE (sp.program_code LIKE 'M%' OR sp.program_code LIKE 'P%' AND sp.program_code NOT LIKE 'MBBS%')
			AND s.matrix_no = '$studentMatrixNo'";
	
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
			WHERE pt.student_matrix_no = '$studentMatrixNo'
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
		
		$sql111="SELECT registered_date as period_month FROM student 
			WHERE matrix_no ='$studentMatrixNo'";
		if (substr($studentMatrixNo,0,2) != '07') { 
			$dbConnStudent= $dbc; 
		} 
		else { 
			$dbConnStudent=$dbc1; 
		}
		$result1 = $dbConnStudent->query($sql111); 
		$dbConnStudent->next_record();
		$period_month=$dbConnStudent->f('period_month');
		
//		DATE_FORMAT(f.responded_date, '%d-%b-%Y %h:%i%p') AS responded_date,
		
		$sql22 = "SELECT DATE_FORMAT(registered_date,'%d-%b-%Y') AS start_date
		FROM student
		WHERE matrix_no = '$studentMatrixNo'";
		if (substr($studentMatrixNo,0,2) != '07') { 
			$dbConnStudent= $dbc; 
		} 
		else { 
			$dbConnStudent=$dbc1; 
		}
		$result1 = $dbConnStudent->query($sql22); 
		$dbConnStudent->next_record();
		$ssdate=$dbConnStudent->f('start_date');
		
		//for Section Overall style and Organization SQL
		
		
	$sql18 = "SELECT id, rate, description
	FROM ref_overall_rating
	WHERE status = 'A'								
	ORDER BY seq";
	
	$result18 = $db->query($sql18);
	$db->next_record();
	
	$overallRatingIdArray = Array();
	$overallRatingRateArray = Array();
	$overallRatingDescArray = Array();
	$i=0;
	do {
		$overallRatingIdArray[$i] = $db->f('id');
		$overallRatingRateArray[$i] = $db->f('rate');
		$overallRatingDescArray[$i] = $db->f('description');
		$i++;
	} while ($db->next_record());
	$row_cnt18 = mysql_num_rows($result18);
	
	$sql14 = "SELECT id, description, rating_status
	FROM ref_overall_style
	WHERE status = 'A'
	ORDER by seq";
	
	$result14 = $db->query($sql14);
	$db->next_record();
	
	$overallStyleIdArray = Array();
	$overallStyleDescArray = Array();
	$overallStyleRatingStatusArray = Array();
	
	$i=0;
	$inc = 0;
	do {
		$overallStyleIdArray[$i] = $db->f('id');
		$overallStyleDescArray[$i] = $db->f('description');
		
		$overallStyleRatingStatusArray[$i] = $db->f('rating_status');
		$i++;
		$inc++;
	} while ($db->next_record());
	
	$row_cnt14 = mysql_num_rows($result14);
	
	//Mark Given sql
	$sql16 = "SELECT id, description, remarks
			FROM ref_defense_marks
			WHERE status = 'A'								
			ORDER BY seq";
		
			$result16 = $db->query($sql16);
			$db->next_record();
			
			$defenseMarksIdArray = Array();
			$defenseMarksDescArray = Array();
			$defenseMarksRemarksArray = Array();
			$i=0;
			do {
				$defenseMarksIdArray[$i] = $db->f('id');
				$defenseMarksDescArray[$i] = $db->f('description');
				$defenseMarksRemarksArray[$i] = $db->f('remarks');
				$i++;
				}
			while ($db->next_record());
			$row_cnt16 = mysql_num_rows($result16);
			
			//OVERALL COMMENT ON STUDENT Section SQL
	$sql17 = "SELECT id, description
			FROM ref_overall_comments
			WHERE status = 'A'								
			ORDER BY seq";
	
			$result17 = $db->query($sql17);
			$db->next_record();
			
			$overallCommentsIdArray = Array();
			$overallCommentsArray = Array();
			$i=0;
			do {
				$overallCommentsIdArray[$i] = $db->f('id');
				$overallCommentsArray[$i] = $db->f('description');
				$i++;
			} while ($db->next_record());
			$row_cnt17 = mysql_num_rows($result17);
	
	$sql18 = "SELECT id, rate, description
			FROM ref_overall_rating
			WHERE status = 'A'								
			ORDER BY seq";
			
			$result18 = $db->query($sql18);
			$db->next_record();
			
			$overallRatingIdArray = Array();
			$overallRatingRateArray = Array();
			$overallRatingDescArray = Array();
			$i=0;
			do {
				$overallRatingIdArray[$i] = $db->f('id');
				$overallRatingRateArray[$i] = $db->f('rate');
				$overallRatingDescArray[$i] = $db->f('description');
				$i++;
			} while ($db->next_record());
			$row_cnt18 = mysql_num_rows($result18);
			
			
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

	$sql_supervisor = " SELECT a.pg_employee_empid, a.ref_supervisor_type_id, d.description as supervisor_type, 
		DATE_FORMAT(a.acceptance_date,'%d-%b-%Y') as acceptance_date, h.description as role_status_desc
		FROM pg_supervisor a 
		LEFT JOIN ref_supervisor_type d ON (d.id = a.ref_supervisor_type_id) 
		LEFT JOIN pg_thesis f ON (f.student_matrix_no = a.pg_student_matrix_no)
		LEFT JOIN pg_proposal g ON (g.pg_thesis_id = f.id)
		LEFT JOIN ref_role_status h ON (h.id = a.role_status)
		WHERE a.pg_student_matrix_no='$studentMatrixNo'
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
///////Session Cohord and Session Start	
//	$sqlCohord = "SELECT  session_start
//			FROM student_program 
//			WHERE matrix_no ='$studentMatrixNo'";
//			$resultCohord = $dbc->query($sqlCohord); 
//			$dbc->next_record();
				
		
			//$session_s=$dbc->f('session_start');
	
//require("../../lib/fpdf/fpdf.php");
require("../../../lib/fpdf/defense_proposal_report_table.php");

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

//insert an image and make it a link



//display the title with a border around it
$pdf->SetXY(20,38);
$pdf->SetDrawColor(50,60,10);
$pdf->Cell(0,9,'DEFENSE PROPOSAL REPORT',1);
$pdf->Ln(10);
//Set x and y position for the main text, reduce font size and write content
	// minor changing
	$pdf->SetFont('Arial','B',9);
	$pdf->SetWidths(array(35,145));
	$pdf->Row1(array("Period/Month",/*$session_s*/""));
	
	$pdf->SetFont('Arial','B',9);	
	$pdf->SetWidths(array(35,145));
	$pdf->Row1(array("Student's Name",$studentName));
	
	$pdf->SetFont('Arial','B',9);
	$pdf->SetWidths(array(35,145));
	$pdf->Row1(array("Email",$email));
	
	$pdf->SetFont('Arial','B',9);
	$pdf->SetWidths(array(35,145));
	$pdf->Row1(array("Matrix Number",$studentMatrixNo));
	
	$pdf->SetFont('Arial','B',9);
	$pdf->SetWidths(array(35,145));
	$pdf->Row1(array("Programme",$program_code." - ".$program_e));
	
	$pdf->SetFont('Arial','B',9);
	$pdf->SetWidths(array(35,145));
	$pdf->Row1(array("Thesis Title",$thesisId." - ".$thesis_title));
	 //minor changging future
	$pdf->SetFont('Arial','B',9);
	$pdf->SetWidths(array(35,145));
	$pdf->Row1(array("Date of Program Started",$ssdate));

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
				
	$pdf->Ln();
	$pdf->SetX (20);
	$pdf->SetFont("Arial","B","9");
	$pdf->Cell(70,5,"OVERALL STYLE AND ORGANIZATION",0,0);
	$pdf->Ln();
	$pdf->SetX (20);
	$pdf->SetFont("Arial","BI","9");
	$pdf->Cell(0,5,"Please tick at appropriate number. Please state (not applicable N/A) where appropriate.",0,0);
	$pdf->Ln();
		
	$pdf->SetFont('Arial','B',9);
	//Table with 10 rows and 5 columns

	$pdf->SetWidths(array(7,55,63,40,15));
	$pdf->Row2(array("No","Item Description","Comments", "Rating Description","Rating"));
	
	$pdf->SetFont('Arial','',9);
	$j=0;
do {
	$sql15 = "SELECT id, rate, description
					FROM ref_report_rating
					WHERE rating_status = '$overallStyleRatingStatusArray[$j]'
					AND status = 'A'								
					ORDER BY seq";
					
					$result15 = $db->query($sql15);
					$db->next_record();
					
					$reportRatingIdArray = Array();
					$reportRatingRateArray = Array();
					$reportRatingDescArray = Array();
					$i=0;
					do {
						$reportRatingIdArray[$i] = $db->f('id');
						$reportRatingRateArray[$i] = $db->f('rate');
						$reportRatingDescArray[$i] = $db->f('description');
						$i++;
					} while ($db->next_record());
					$row_cnt15 = mysql_num_rows($result15);	
		
					$sqlVar1 = "SELECT description 
					FROM ref_report_rating
					WHERE rating_status = '$overallStyleRatingStatusArray[$j]'
					AND status = 'A'
					AND rate = '1'								
					ORDER BY seq";
					
					$resultVar1 = $db->query($sqlVar1);
					$db->next_record();	
					$var1=$db->f('description');	
					
			$sqlVar2 = "SELECT description 
					FROM ref_report_rating
					WHERE rating_status = '$overallStyleRatingStatusArray[$j]'
					AND status = 'A'
					AND rate = '5'								
					ORDER BY seq";
					
					$resultVar2 = $db->query($sqlVar2);
					$db->next_record();	
					$var2=$db->f('description');
						
				
			
if ($j !=9){	
		for ($i=0;$i<$row_cnt2A;$i++) {
			if ($overallStyleIdArray[$j] == $esRefOverallStyleIdArray[$i]) {
				for ($k=0;$k<$row_cnt15;$k++) {
					if ($reportRatingIdArray[$k] == $esRefReportRatingIdArray[$i]) {	
						if($k!=0){
							if ($row_cnt15 > 0) {
							if($esCommentsArray[$j]!= NULL ) {
								$pdf->Row2(array(($j+1).".",$overallStyleDescArray[$j],$esCommentsArray[$j],"1 = ".$var1."\n"."5 = ".$var2,$k));
								}
								else {
								$pdf->Row2(array(($j+1).".",$overallStyleDescArray[$j],$esCommentsArray[$j]."","1 = ".$var1."\n"."5 = ".$var2,$k));
								}
							}
							else
							{
								if($esCommentsArray[$j]!= NULL ) {
								$pdf->Row2(array(($j+1).".",$overallStyleDescArray[$j],$esCommentsArray[$j],"" ,$k));
								}
								else {
								$pdf->Row2(array(($j+1).".",$overallStyleDescArray[$j],$esCommentsArray[$j].""," ",$k));
								}
							}
		}
		else{
						if ($row_cnt15 > 0) {
							if($esCommentsArray[$j]!= NULL ) {
								$pdf->Row2(array(($j+1).".",$overallStyleDescArray[$j],$esCommentsArray[$j],"1 = ".$var1."\n"."5 = ".$var2,"N/A"));
								}
								else {
								$pdf->Row2(array(($j+1).".",$overallStyleDescArray[$j],$esCommentsArray[$j]."","1 = ".$var1."\n"."5 = ".$var2,"N/A"));
								}
							}
							else
							{
								if($esCommentsArray[$j]!= NULL ) {
								$pdf->Row2(array(($j+1).".",$overallStyleDescArray[$j],$esCommentsArray[$j],"" ,"N/A"));
								}
								else {
								$pdf->Row2(array(($j+1).".",$overallStyleDescArray[$j],$esCommentsArray[$j].""," ","N/A"));
								}
							}
		}

		}
		else {
		
	
		
		}
		}
		}
		}
	}
	else
	{
								if($esCommentsArray[$j]!= NULL ) {
								$pdf->Row2(array(($j+1).".",$overallStyleDescArray[$j],$esCommentsArray[$j],"" ,""));
								}
								else {
								$pdf->Row2(array(($j+1).".",$overallStyleDescArray[$j],$esCommentsArray[$j].""," ",""));
								}
	}
		




		$j++;
	} while ($j<$row_cnt14);
	
//MAJOR REVISIONS section
	$pdf->Ln();
	$pdf->SetX (20);
	$pdf->SetFont("Arial","B","9");
	$pdf->Cell(70,5,"MAJOR REVISIONS REQUIRED (If any)",0,0);
	$pdf->ln();
	$pdf->SetX (20);
	$pdf->SetFont("Arial","I","9");
	$pdf->Cell(0,5,"Please use additional sheet if required.",0,0);
	$pdf->ln();
	//$pdf->SetX (20);
	$pdf->SetFont("Arial","","9");
	$html=$majorRevision1;
		 if (empty($majorRevision1)) { 
		 
		 		$pdf->SetWidths(array(180));
				$pdf->Row(array("None Comments"));
				//$pdf->Cell(0,5,"None Comments",1,1);
		}
		else {
			
				$tmpHTML = strip_tags($html);
				$pdf->SetWidths(array(180));
				$pdf->Row(array($tmpHTML));
				//$pdf->Cell(0,5,$tmpHTML,1,1);
				//$pdf->Cell(0,5,$majorRevision1."----",1,1);
			}
	
	$pdf->Ln();
	
	//OTHER COMMENTS Section
	$pdf->SetX (20);
	$pdf->SetFont("Arial","B","9");
	$pdf->Cell(70,5,"OTHER COMMENTS",0,0);
	$pdf->ln();
	$pdf->ln();
	//$pdf->SetX (20);
	$pdf->SetFont("Arial","","9");
	
	$html=$otherComment1;

	 if (empty($otherComment1)) { 
				$pdf->SetWidths(array(180));
				$pdf->Row(array("None Comments"));
				//$pdf->Cell(0,5,"None Comments",1,1);
		}
		else {
			$tmpHTML = strip_tags($html);
			$pdf->SetWidths(array(180));
			$pdf->Row(array($tmpHTML));
			//$pdf->Cell(0,5,$tmpHTML,1,1);
			//$pdf->Cell(0,5,$pdf->WriteHTML($html),1,1);
		}
	$pdf->ln();
	//Mark Given Section
	$pdf->SetX (20);
	$pdf->SetFont("Arial","B","9");
	$pdf->Cell(70,5,"MARK GIVEN",0,0);
	$pdf->ln();
		
	$pdf->SetFont('Arial','B',9);
	//Table with 3 rows and 3 columns
 //$image1=


	$pdf->SetWidths(array(7,125,48));
	
	$pdf->Row2(array("No","Components","Recommendations"));
	
	$pdf->SetFont('Arial','',9);
	//$pdf->Image('../../../theme/images/success.jpg','','',4,4);
 	for ($i=0;$i<$row_cnt16;$i++) {
			if ($refDefenseMarksId1!= null) 
		{	
			if ($defenseMarksIdArray[$i] == $refDefenseMarksId1)
				{ 
					if($defenseMarksIdArray[$i] == 'SAT')
						{
							$pdf->Row2(array(($i+1).".",$defenseMarksDescArray[$i]."".$defenseMarksRemarksArray[$i],"Recommended"));	
						}
					else 
						{
							$pdf->Row2(array(($i+1).".",$defenseMarksDescArray[$i]."/".$defenseMarksRemarksArray[$i],"Recommended"));
						}
				}
			else{
					if($defenseMarksIdArray[$i] == 'SAT')
						{
							$pdf->Row2(array(($i+1).".",$defenseMarksDescArray[$i]."".$defenseMarksRemarksArray[$i],""));	
						}
					else 
						{
							$pdf->Row2(array(($i+1).".",$defenseMarksDescArray[$i]."/".$defenseMarksRemarksArray[$i],""));
						}
				}	
				
		} 
	else
		{ 
		}	
}
	//Overal Comment On Student
	$pdf->Ln();
	$pdf->SetX (20);
	$pdf->SetFont("Arial","B","9");
	$pdf->Cell(70,5,"OVERALL COMMENT ON STUDENT",0,0);
	$pdf->ln();
	$pdf->SetX (20);
	$pdf->SetFont("Arial","BI","9");
	$pdf->MultiCell(0,5,"As a supervisor, how do you assess your student on the following grounds? Please rate how strongly you agree or disagree with the statement about the candidate on the following ground.",0,"L");
	$pdf->SetX (20);
	$pdf->SetFont("Arial","BI","8");
	$pdf->MultiCell(0,5,"(1= strongly disagree, 2 = disagree, 3 = slightly disagree, 4 = neither agree nor disagree, 5 = slightly agree, 6 = agree 7 = strongly agree)",0,"L");
	$pdf->SetFont('Arial','B',9);
	$pdf->SetWidths(array(10,120,50));
	$pdf->Row2(array("No.","Components","Recommendations"));
	$pdf->SetFont('Arial','',9);
	for ($i=0;$i<$row_cnt17;$i++){
	if ($row_cnt2B>0) {
			for ($l=0;$l<$row_cnt2B;$l++) {//ref_overall_rating
				if ($overallCommentsIdArray[$i] == $eoRefOverallCommentsIdArray[$l]) {
					for ($k=0;$k<$row_cnt18;$k++) {//ref_overall_rating
						if ($overallRatingIdArray[$k] == $eoRefOverallRatingIdArray[$l]) {
						//$pdf->MultiCell(0,5,$overallRatingRateArray[$k]. " - ".$overallRatingDescArray[$k],1,"L");
						$pdf->Row2(array(($i+1).".",$overallCommentsArray[$i],$overallRatingRateArray[$k]. " - ".$overallRatingDescArray[$k]));	
							}
						else{
							}
					}
				}	
			else {	
				}
	}
	}
	else{}
	}
	//Evalution Committee Section
	
	$pdf->ln();
	$pdf->SetX (20);
	$pdf->SetFont("Arial","B","9");
	$pdf->Cell(45,5,"Name Evalution Commitee",1,0);
	$pdf->SetFont("Arial","","9");
	$pdf->Cell(0,5,$staffname,1,1);
	$pdf->SetX (20);
	$pdf->SetFont("Arial","B","9");
	$pdf->Cell(45,20,"Signature",1,0);
	$pdf->SetFont("Arial","","9");
	$pdf->Cell(0,20,"",1,1);
	$pdf->SetX (20);
	$pdf->SetFont("Arial","B","9");
	$pdf->Cell(45,5,"Date",1,0);
	$pdf->SetFont("Arial","","9");
	$pdf->Cell(0,5,"",1,1);

	$pdf->ln();

$pdf->Output('DP_'.$referenceNo."_".$studentMatrixNo.'.pdf','I');
close();

?>
