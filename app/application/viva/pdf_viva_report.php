<?php
include("../../../lib/common.php");
checkLogin();

$user_id=$_SESSION['user_id'];
$thesisId = $_GET['tid']; 
//@$referenceNo=$_GET['rid'];
$matrixNo=$_GET['mn'];
$rolestatus=$_GET['role'];
$submit=$_GET['submit'];
$pd=$_GET['pd'];
$roleType=$_GET['type'];
$save=$_GET['save'];


		if (substr($user_id,0,2) != '07') { 
			$dbConn=$dbc; 
			} 
		else { 
			$dbConn=$dbc1; 
			}
		
		$sql_rate = "SELECT description 
			FROM ref_report_rating 
			WHERE id = 'RPR0002'";
			
		//	$dbConn->query($sql_rate);
			$dbg->query($sql_rate);
			$dbg->next_record();
			$not_appropriate= $dbg->f('description');

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
		
		$sql_supervisor = " SELECT b.id AS evaVivaDetailId, b.major_revision, b.other_comment, b.recommendation_id, c.description AS recoDesc,
							b.comment_sec_e AS commentSecE, b.comment_sec_a as commentSecA, a.id AS evaVivaId
							FROM pg_evaluation_viva a
							LEFT JOIN pg_evaluation_viva_detail b ON (b.pg_eva_viva_id = a.id)
							LEFT JOIN ref_recommendation c ON (c.id = b.recommendation_id)
							WHERE b.id = '$pd'
							AND a.pg_thesis_id = '$thesisId'
							AND a.student_matrix_no = '$matrixNo'
							AND a.status = 'A'
							AND b.status = 'A'";
							
							$result_sql_supervisor = $db_klas2->query($sql_supervisor); //echo $sql;
							$db_klas2->next_record();
							$row_cnt_supervisor = mysql_num_rows($result_sql_supervisor);
							
							$evaVivaDetailId = $db_klas2->f('evaVivaDetailId');
							$major_revision = $db_klas2->f('major_revision');
							$other_comment = $db_klas2->f('other_comment');
							$recommendation_id = $db_klas2->f('recommendation_id');
							$recoDesc = $db_klas2->f('recoDesc');
							$commentSecE = $db_klas2->f('commentSecE');
							$commentSecA = $db_klas2->f('commentSecA');
							$evaVivaId = $db_klas2->f('evaVivaId');

		
		
		////////////////////Section A//////////////////////////
		$sqlA = "SELECT a.id as vivaStyleId, a.ref_overall_style_id, a.ref_overall_rating_id, a.comments, 
		b.description AS questionA, c.description AS answerA, c.rate
		FROM pg_evaluation_viva_style a
		LEFT JOIN ref_overall_style_viva b ON (b.id=a.ref_overall_style_id)
		LEFT JOIN ref_overall_viva_rating c ON (c.id=a.ref_overall_rating_id)
		WHERE a.pg_eva_viva_detail_id = '$evaVivaDetailId'
		AND a.status = 'A'
		ORDER BY b.seq ASC, c.seq";
		$dbt = $dbu;
		$result_sqlA = $dbt->query($sqlA); 
		$dbt->next_record();
		$row_cnt5 = mysql_num_rows($result_sqlA);
		$noA= 0;
		$incA= 0;
		
		$vivaStyleId = array();
		$ref_overall_style_id = array();
		$ref_overall_rating_idA = array();
		$comments = array();
		$answerA = array();
		$questionA = array();
		$rateA = array();
		
		do{
			$vivaStyleId[$noA] = $dbt->f('vivaStyleId');
			$ref_overall_style_id[$noA] = $dbt->f('ref_overall_style_id');
			$ref_overall_rating_idA[$noA] = $dbt->f('ref_overall_rating_id');
			$comments[$noA] = $dbt->f('comments');
			$questionA[$noA] = $dbt->f('questionA');
			$answerA[$noA] = $dbt->f('answerA');
			$rateA[$noA] = $dbt->f('rate');
			
			$noA++;
			$incA++;
		
		}while($dbt->next_record());


		////////Select reference of question and answer//////
		$sqlques = "SELECT * FROM ref_overall_style_viva ORDER BY seq ASC";
		$dbbq = $dbu;
		$result_sqlques = $dbbq->query($sqlques); 
		$dbbq->next_record();
		$row_cnt5 = mysql_num_rows($result_sqlques);
		$iq= 0;
		$inq= 0;
		
		$amendmentArray = array();
		$feedbackByExaminerArray = array();
		$idArray = array();
		$descArray = array();
		$seqArray = array();
		
		do{
			$idArray[$iq] = $dbbq->f('id');
			$descArray[$iq] = $dbbq->f('description');
			$seqArray[$iq] = $dbbq->f('seq');
			$iq++;
			$inq++;
	
		}while($dbbq->next_record());
		
////////////////////Overall sql From View Viva Report//////////////////////////

$sql_supervisor = " SELECT b.id AS evaVivaDetailId, b.major_revision, b.other_comment, b.recommendation_id, c.description AS recoDesc,
b.comment_sec_e AS commentSecE, b.comment_sec_a as commentSecA, a.id AS evaVivaId
FROM pg_evaluation_viva a
LEFT JOIN pg_evaluation_viva_detail b ON (b.pg_eva_viva_id = a.id)
LEFT JOIN ref_recommendation c ON (c.id = b.recommendation_id)
WHERE b.id = '$pd'
AND a.pg_thesis_id = '$thesisId'
AND a.student_matrix_no = '$matrixNo'
AND a.status = 'A'
AND b.status = 'A'";

$result_sql_supervisor = $db_klas2->query($sql_supervisor); //echo $sql;
$db_klas2->next_record();
$row_cnt_supervisor = mysql_num_rows($result_sql_supervisor);

$evaVivaDetailId = $db_klas2->f('evaVivaDetailId');
$major_revision = $db_klas2->f('major_revision');
$other_comment = $db_klas2->f('other_comment');
$recommendation_id = $db_klas2->f('recommendation_id');
$recoDesc = $db_klas2->f('recoDesc');
$commentSecE = $db_klas2->f('commentSecE');
$commentSecA = $db_klas2->f('commentSecA');
$evaVivaId = $db_klas2->f('evaVivaId');

///////////////////////list of evaluation panel for viva///////////////////////
$sql_supervisor = " SELECT a.pg_empid_viva, b.role_status, b.ref_supervisor_type_id, 
c.description AS roleDesc, a.report_status, DATE_FORMAT(a.submit_date,'%d-%b-%Y %h:%i:%s %p') as submit_date, a.id as otherDetailId
FROM pg_evaluation_viva_detail a
LEFT JOIN pg_supervisor b ON (b.pg_employee_empid = a.pg_empid_viva)
LEFT JOIN ref_supervisor_type c ON (c.id = b.ref_supervisor_type_id)
LEFT JOIN pg_evaluation_viva d ON (d.id = a.pg_eva_viva_id)
WHERE b.pg_thesis_id = '$thesisId'
AND a.pg_eva_viva_id = '$evaVivaId'
AND d.id = '$evaVivaId'
AND b.status = 'A'
AND a.pg_empid_viva <> '$user_id'";

$db_klas3 = $db_klas2;
$result_sql_supervisor = $db_klas3->query($sql_supervisor); //echo $sql;
$db_klas3->next_record();
$row_cnt_supervisor = mysql_num_rows($result_sql_supervisor);


////////////////////Section A//////////////////////////
$sqlA = "SELECT a.id as vivaStyleId, a.ref_overall_style_id, a.ref_overall_rating_id, a.comments, 
b.description AS questionA, c.description AS answerA, c.rate
FROM pg_evaluation_viva_style a
LEFT JOIN ref_overall_style_viva b ON (b.id=a.ref_overall_style_id)
LEFT JOIN ref_overall_viva_rating c ON (c.id=a.ref_overall_rating_id)
WHERE a.pg_eva_viva_detail_id = '$evaVivaDetailId'
AND a.status = 'A'
ORDER BY b.seq ASC, c.seq";
$dbt = $dbu;
$result_sqlA = $dbt->query($sqlA); 
$dbt->next_record();
$row_cnt5 = mysql_num_rows($result_sqlA);
$noA= 0;
$incA= 0;

$vivaStyleId = array();
$ref_overall_style_id = array();
$ref_overall_rating_idA = array();
$comments = array();
$answerA = array();
$questionA = array();
$rateA = array();

do{
	$vivaStyleId[$noA] = $dbt->f('vivaStyleId');
	$ref_overall_style_id[$noA] = $dbt->f('ref_overall_style_id');
	$ref_overall_rating_idA[$noA] = $dbt->f('ref_overall_rating_id');
	$comments[$noA] = $dbt->f('comments');
	$questionA[$noA] = $dbt->f('questionA');
	$answerA[$noA] = $dbt->f('answerA');
	$rateA[$noA] = $dbt->f('rate');
	
	$noA++;
	$incA++;

}while($dbt->next_record());

/////////////////////section D/////////////////////////
$sqlrec = "SELECT * FROM ref_recommendation ORDER BY seq ASC ";
$db3 = $db;
$result_sqlrec = $db3->query($sqlrec); 
$db3->next_record();
$row_cnt7 = mysql_num_rows($result_sqlrec);
$no= 0;
$noU = 0;

$feedbackByExaminerArray = array();
$idRecArray = array();
$descRecArray = array();

do{
	$idRecArray[$no] = $db3->f('id');
	$descRecArray[$no] = $db3->f('description');
	$no++;
	$noU++;

}while($db3->next_record());

////////////////////Section E//////////////////////////
$sqlA = "SELECT a.id AS vivaOverallId, a.ref_overall_comment_id, a.ref_overall_rating_id, 
b.description AS commentDesc, c.description AS ratingDesc, c.rate as rateE
FROM pg_evaluation_viva_overall a
LEFT JOIN ref_overall_comments b ON (b.id=a.ref_overall_comment_id)
LEFT JOIN ref_overall_rating c ON (c.id=a.ref_overall_rating_id)
WHERE a.pg_eva_viva_detail_id = '$evaVivaDetailId'
AND a.status = 'A'
ORDER BY a.id ASC";
$dbh = $dbu;
$result_sqlA = $dbh->query($sqlA); 
$dbh->next_record();
$row_cnt5 = mysql_num_rows($result_sqlA);
$noE= 0;
$incE= 0;

$vivaOverallId = array();
$ref_overall_comment_id = array();
$ref_overall_rating_id = array();
$commentDesc = array();
$ratingDesc = array();
$rateE = array();

do{
	$vivaOverallId[$noE] = $dbh->f('vivaOverallId');
	$ref_overall_comment_id[$noE] = $dbh->f('ref_overall_comment_id');
	$ref_overall_rating_id[$noE] = $dbh->f('ref_overall_rating_id');
	$commentDesc[$noE] = $dbh->f('commentDesc');
	$ratingDesc[$noE] = $dbh->f('ratingDesc');
	$rateE[$noE] = $dbh->f('rateE');
	
	$noE++;
	$incE++;

}while($dbh->next_record());

$sql4 = "SELECT id
FROM pg_calendar
WHERE student_matrix_no = '$matrixNo'
AND thesis_id = '$thesisId'
AND ref_session_type_id = 'VIV'
AND recomm_status = 'REC'
AND status = 'A'
ORDER BY defense_date ASC";

$result_sql4 = $dbg->query($sql4); 
$dbg->next_record();
$row_cnt4 = mysql_num_rows($result_sql4);
$calendarIdViva = $dbg->f('id');


$sql5 = "SELECT description from ref_supervisor_type where id = '$roleType'";
$dbg5 = $dbg;
$result_sql5 = $dbg5->query($sql5); 
$dbg5->next_record();
$row_cnt4 = mysql_num_rows($result_sql5);
$header11 = $dbg5->f('description');

$sqlcomment = "SELECT * FROM ref_overall_comments ORDER BY seq ASC ";
$db4 = $db;
$result_sqlcomment = $db4->query($sqlcomment); 
$db4->next_record();
$row_cnt7 = mysql_num_rows($result_sqlcomment);
$no1= 0;
$noC = 0;

$idComArray = array();
$descComArray = array();

do{
	$idComArray[$no1] = $db4->f('id');
	$descComArray[$no1] = $db4->f('description');
	$no1++;
	$noC++;

}while($db4->next_record());

$sqloverrating = "SELECT id, rate, description FROM ref_overall_rating WHERE STATUS = 'A' ORDER BY seq ASC";
$db5 = $db;
$result_sqloverrating = $db5->query($sqloverrating); 
$db5->next_record();
$row_cnt8 = mysql_num_rows($result_sqloverrating);
$no2= 0;
$noOR = 0;

$idOrArray = array();
$descOrArray = array();
$rateOrArray = array();

do{
	$idOrArray[$no2] = $db5->f('id');
	$descOrArray[$no2] = $db5->f('description');
	$rateOrArray[$no2] = $db5->f('rate');
	$no2++;
	$noOR++;

}while($db5->next_record());


$vivaOverallId[$noE] = $dbh->f('vivaOverallId');
$ref_overall_comment_id[$noE] = $dbh->f('ref_overall_comment_id');
$ref_overall_rating_id[$noE] = $dbh->f('ref_overall_rating_id');
$commentDesc[$noE] = $dbh->f('commentDesc');
$ratingDesc[$noE] = $dbh->f('ratingDesc');
$rateE[$noE] = $dbh->f('rateE');

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

//require("../../lib/fpdf/fpdf.php");
require("../../../lib/fpdf/viva_report_table.php");

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
$pdf->SetXY(20,38);
$pdf->SetDrawColor(50,60,10);
$pdf->Cell(0,9,'PhD VIVA EXAMINATION Report',1);
$pdf->Ln(10);

	$pdf->SetFont('Arial','B',9);
	$pdf->SetWidths(array(35,145));
	$pdf->Row1(array("Student's Name",$studentName));
	
	$pdf->SetFont('Arial','B',9);
	$pdf->SetWidths(array(35,145));
	$pdf->Row1(array("Matrix Number",$matrixNo));
	
	$pdf->SetFont('Arial','B',9);
	$pdf->SetWidths(array(35,145));
	$pdf->Row1(array("Programme",$program_code." - ".$program_e));
	
	$pdf->SetFont('Arial','B',9);
	$pdf->SetWidths(array(35,145));
	$pdf->Row1(array("Thesis Topic",$thesisId." - ".$thesis_title));
	
	$pdf->Ln();
	$pdf->SetX (20);
	$pdf->SetFont("Arial","B","9");
	$pdf->Cell(70,5,"Intruction for Completing the Thesis Examiner's Report",0,0);
	$pdf->Ln();
	$pdf->SetX (20);
	$pdf->SetFont("Arial","","9");
	$pdf->Cell(70,5,"This report consist of five (5) sections:",0,0);
	$pdf->Ln();
	$pdf->SetX (20);
	$pdf->SetFont("Arial","","9");
	$pdf->Cell(70,5,"Section A : Report",0,0);
	$pdf->Ln();
	$pdf->SetX (20);
	$pdf->SetFont("Arial","","9");
	$pdf->Cell(70,5,"Section B : Unguided Report",0,0);
	$pdf->Ln();
	$pdf->SetX (20);
	$pdf->SetFont("Arial","","9");
	$pdf->Cell(70,5,"Section C : Other Comment",0,0);
	$pdf->Ln();
	$pdf->SetX (20);
	$pdf->SetFont("Arial","","9");
	$pdf->Cell(70,5,"Section D : mark Given",0,0);
	$pdf->Ln();
	$pdf->SetX (20);
	$pdf->SetFont("Arial","","9");
	$pdf->Cell(70,5,"Section E : Overall Comment on Student",0,0);
	$pdf->Ln();
	$pdf->Ln();
	$pdf->SetX (20);
	$pdf->SetFont("Arial","","9");
	$pdf->Cell(70,5,"(1) All Examiner are required to submit their report using thesis Examiner's Report Form.",0,0);
	$pdf->Ln();
	$pdf->SetX (20);
	$pdf->SetFont("Arial","","9");
	$pdf->Cell(70,5,"(2) Examiner have the choice of submitting, either:",0,0);
	$pdf->Ln();
	$pdf->SetX (30);
	$pdf->SetFont("Arial","","9");
	$pdf->MultiCell(0,5,"(a) Guided Format: By filling the section B, C, D and E. Section B must contain only a brief written report higlight the main strenght and weakness of the Thesis; ",0);
	$pdf->SetX (30);
	$pdf->SetFont("Arial","","9");
	$pdf->Cell(70,5,"OR",0,0);
	$pdf->Ln();
	$pdf->SetX (30);
	$pdf->SetFont("Arial","","9");
	$pdf->MultiCell(0,5,"(b) Unguided Format: By filling the section B, C, D and E only. Section B must contain a full report detailing the strengths and weaknesses of the Thesis according to each section and sub-section in the Thesis. Examiners are required to draw the Thesis Exammination Panel's attention to specific points that may require elaboration and clarification during the viva. ",0);
	//$pdf->Cell(70,5,"(b) Unguided Format: By filling the section B, C, D and E only. Section B must contain a full report detailing the strengths and weaknesses of the Thesis according to each section and sub-section in the Thesis. Examiners are required to draw the Thesis Exammination Panel's attention to specific points that may require elaboration and clarification during the viva. ",0,0);
	
$pdf->AddPage();
//////////////////////////SECTION A///////////////////////////////	
	$pdf->SetXY(20,38);
	$pdf->SetFont("Arial","B","9");
	$pdf->Cell(70,5,"SECTION A: OVERALL STYLE AND ORGANIZATION",0,0);
	$pdf->Ln();
	$pdf->SetX (20);
	$pdf->SetFont("Arial","I","9");
	$pdf->Cell(0,5,"Please tick at appropriate number. Please state (not applicable N/A) where appropriate.",0,0);
	$pdf->Ln();
	$pdf->SetFont('Arial','B',9);
	//Table with 14 rows and 5 columns

	$pdf->SetWidths(array(10,55,60,40,15));
	$pdf->Row2(array("No.","Item Description","Comments", "Rating Description","Rating"));
	
	$pdf->SetFont('Arial','',9);

$sql14 = "SELECT id, description, ques_seq
	FROM ref_overall_viva_rating
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
		
		$overallStyleRatingStatusArray[$i] = $db->f('ques_seq');
		$i++;
		$inc++;
	} while ($db->next_record());
	
	$row_cnt14 = mysql_num_rows($result14);



	
$j=0;
do {
	$sql15 = "SELECT id, rate, description
					FROM ref_overall_viva_rating
					WHERE ques_seq = '$overallStyleRatingStatusArray[$j]'
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
					FROM ref_overall_viva_rating
					WHERE ques_seq = '$overallStyleRatingStatusArray[$j]'
					AND status = 'A'
					AND rate = '1'								
					ORDER BY seq";
					
					$resultVar1 = $db->query($sqlVar1);
					$db->next_record();	
					$var1=$db->f('description');	
					
		$sqlVar2 = "SELECT description 
					FROM ref_overall_viva_rating
					WHERE ques_seq = '$overallStyleRatingStatusArray[$j]'
					AND status = 'A'
					AND rate = '5'								
					ORDER BY seq";
					
					$resultVar2 = $db->query($sqlVar2);
					$db->next_record();	
					$var2=$db->f('description');	
					
						//echo $var1;	
$sqlrate = "SELECT * FROM ref_overall_viva_rating where ques_seq = '$seqArray[$i]' ORDER BY seq ASC ";
			$db1 = $db;
			$result_sqlrate = $db1->query($sqlrate); 
			$db1->next_record();
			$row_cnt5 = mysql_num_rows($result_sqlrate);
			$ir= 0;
			$inr = 0;
			
			$idRateArray = array();
			$descRateArray = array();
			$rateArray = array();
			
			do{
				$idRateArray = $db1->f('id');
				$descRateArray = $db1->f('description');
				$rateArray = $db1->f('rate');
				
						
			}while($db1->next_record());
	
	//echo $row_cnt5;	
			
	//for ($i=0;$i<17;$i++){
	$pdf->Row2(array(($j+1).".",$descArray[$j],$comments[$j],"1 = ".$var1."\n"."5 = ".$var2,$rateA[$j]));	
	//}
	
	
				$j++;
} while ($j<17);
	
	
	
	//$esCommentsArray[$i]."","1 = ".$var1."\n"."5 = ".$var2,$k

	$pdf->SetX (20);
	$pdf->SetFont("Arial","B","9");
	$pdf->Cell(70,5,"Other Comment",0,0);
	$pdf->ln();
	$pdf->SetFont("Arial","I","9");
	$pdf->SetWidths(array(180));
	$pdf->Row(array($commentSecA));
	
	$pdf->Ln();
//////////////////////////SECTION B///////////////////////////////
	$pdf->SetX (20);
	$pdf->SetFont("Arial","B","9");
	$pdf->Cell(70,5,"SECTION B: MAJOR REVISIONS REQUIRED (If any)",0,0);
	$pdf->ln();
	$pdf->SetX (20);
	$pdf->SetFont("Arial","I","9");
	$pdf->Cell(0,5,"Please use additional sheet if required.",0,0);
	$pdf->ln();
	$pdf->SetFont("Arial","","9");
	$html=$major_revision;
		 if (empty($major_revision)) { 
		 
		 		$pdf->SetWidths(array(180));
				$pdf->Row(array("None Comments"));
		}
		else {
			
				$tmpHTML = strip_tags($html);
				$pdf->SetWidths(array(180));
				$pdf->Row(array($tmpHTML));
			}
	//////////////////////////////SECTION C//////////////////////////////
	$pdf->Ln();
	$pdf->SetX (20);
	$pdf->SetFont("Arial","B","9");
	$pdf->Cell(70,5,"SECTION C: OTHER COMMENTS",0,0);
	$pdf->ln();
	$pdf->SetX (20);
	$pdf->SetFont("Arial","I","9");
	$pdf->Cell(0,5,"For example: Suitability for publication and award, if any. Please use additional sheet if required.",0,0);
	$pdf->ln();
	$pdf->SetFont("Arial","","9");
	$html=$other_comment;
		 if (empty($other_comment)) { 
		 
		 		$pdf->SetWidths(array(180));
				$pdf->Row(array("None Comments"));
		}
		else {
			
				$tmpHTML = strip_tags($html);
				$pdf->SetWidths(array(180));
				$pdf->Row(array($tmpHTML));
			}
	//////////////////////SECTION D///////////////////////////////
	$pdf->Ln();
	$pdf->SetX (20);
	$pdf->SetFont("Arial","B","9");
	$pdf->Cell(70,5,"SECTION D: RECOMMENDATION",0,0);
	$pdf->ln();

	$pdf->SetFont('Arial','B',9);
	//Table with 3 rows and 3 columns
	$pdf->SetWidths(array(10,120,50));
	$pdf->Row2(array("No.","Components","Recomendation"));
	$pdf->SetFont('Arial','',9);		
	
	for ($no=0; $no<$noU; $no++){
		if($recommendation_id == $idRecArray[$no])
			{
			$pdf->Row2(array(($no+1).".",$descRecArray[$no],"Recommended"));
			}
			else
			{
			$pdf->Row2(array(($no+1).".",$descRecArray[$no],""));
			}
				
		}
			//////////////////////SECTION E///////////////////////////////
	$pdf->ln();
	$pdf->SetX (20);
	$pdf->SetFont("Arial","B","9");
	$pdf->Cell(70,5,"SECTION E: OVERALL COMMENT ON STUDENT",0,0);
	$pdf->ln();
	$pdf->SetX (20);
	$pdf->SetFont("Arial","BI","9");
	$pdf->MultiCell(0,5,"As a Examiner, how do you asses your student on the following grounds. Please rate how strongly you agree or disagree with the statement about the candidate on the following ground.",0,"L");

	$pdf->SetX (20);
	$pdf->SetFont("Arial","BI","8");
	$pdf->MultiCell(0,5,"(1= strongly disagree, 2 = disagree, 3 = slightly disagree, 4 = neither agree nor disagree, 5 = slightly agree, 6 = agree 7 = strongly agree)",0,"L");

	$pdf->SetFont('Arial','B',9);
	$pdf->SetWidths(array(10,120,50));
	$pdf->Row2(array("No.","Components","Recommendations"));
	$pdf->SetFont('Arial','',9);
	for ($no1=0; $no1<$noC; $no1++){
		for ($no2=0; $no2<$noOR; $no2++){//ref_overall_rating
				if($ref_overall_rating_id[$no1] == $idOrArray[$no2]) {
						$pdf->Row2(array(($no1+1).".",$descComArray[$no1],$rateOrArray[$no2]. " - ".$descOrArray[$no2]));	
							}
				else {
					}
		}
	}
	////////////Other Comment///////////
	$pdf->ln();
	$pdf->SetX (20);
	$pdf->SetFont("Arial","B","9");
	$pdf->Cell(45,5,"OTHER COMMENT",0,0);
	$pdf->SetFont("Arial","","9");
	$html=$commentSecE;
	$pdf->ln();
	 if (empty($commentSecE)) { 
				$pdf->SetWidths(array(180));
				$pdf->Row(array("None Comments"));
				//$pdf->Cell(0,5,"None Comments",1,1);
		}
		else {
			$tmpHTML = strip_tags($html);
			$pdf->SetWidths(array(180));
			$pdf->Row(array($tmpHTML));
			}
	/////////////////////SIGNATURE///////////////////
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

$pdf->Output('VIVA('.$matrixNo.').pdf','I');
close();
?>
