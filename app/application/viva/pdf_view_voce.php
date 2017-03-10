<?php
include("../../../lib/common.php");
checkLogin();

$user_id=$_SESSION['user_id'];
$thesisId = $_GET['tid']; 
$matrixNo=$_GET['mn'];
$rolestatus=$_GET['vid'];
$respond_by = $_GET['rb'];

//Student name SQL
$sql1 = "SELECT name AS student_name
		FROM student
		WHERE matrix_no = '$matrixNo'";
		if (substr($user_id,0,2) != '07') { 
			$dbConnStudent= $dbc; 
		} 
		else { 
			$dbConnStudent=$dbc1; 
		}
		$result1 = $dbConnStudent->query($sql1); 
		$dbConnStudent->next_record();
		$sname=$dbConnStudent->f('student_name');
		
//Thesis title SQl
$sql8 = "SELECT thesis_title
		FROM pg_proposal
		WHERE pg_thesis_id = '$thesisId'";
		$dbg4 = $dbg;
		$result_sql8 = $dbg4->query($sql8); 
		$dbg4->next_record();
		$row_cnt4 = mysql_num_rows($result_sql8);
		$thesis_title = $dbg4->f('thesis_title');
//Schedule date for viva SQL

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
//component
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
	
	// result for recomendation
	$sql_supervisor = " SELECT b.id AS evaVivaDetailId, b.major_revision, b.other_comment, b.recommendation_id, c.description AS recoDesc,
						b.comment_sec_e AS commentSecE, b.comment_sec_a as commentSecA, a.id AS evaVivaId
						FROM pg_evaluation_viva a
						LEFT JOIN pg_evaluation_viva_detail b ON (b.pg_eva_viva_id = a.id)
						LEFT JOIN ref_recommendation c ON (c.id = b.recommendation_id)
						WHERE a.pg_thesis_id = '$thesisId'
						AND a.student_matrix_no = '$matrixNo'
						AND a.status = 'A'
						AND b.status = 'A'
						AND b.pg_empid_viva = '$user_id'";
						
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
					

//require("../../lib/fpdf/fpdf.php");
require("../../../lib/fpdf/viva_voce_evaluation_table.php");

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
$pdf->Cell(0,0,'PhD VIVA VOCE EVALUATION FORM',0,0,'C');
$pdf->Ln(10);

	
	$pdf->SetFont('Arial','B',9);
	$pdf->SetWidths(array(35,145));
	$pdf->Row1(array("Student Name",$sname));
	
	$pdf->SetFont('Arial','B',9);
	$pdf->SetWidths(array(35,145));
	$pdf->Row1(array("Matrix Number",$matrixNo));
	
	$pdf->SetFont('Arial','B',9);
	$pdf->SetWidths(array(35,145));
	$pdf->Row1(array("Thesis / Project ID",$thesisId));
	
	$pdf->SetFont('Arial','B',9);
	$pdf->SetWidths(array(35,145));
	$pdf->Row1(array("Thesis Title",$thesis_title));
	
	
	$pdf->SetFont('Arial','B',9);
	$pdf->SetWidths(array(35,145));
	$pdf->Row1(array("Evaluation Schedule",$vivaDate." , ".$vivaSTime." to ".$vivaETime." , ".$venue));
	$pdf->ln();
	$pdf->SetFont('Arial','B',10);
	$pdf->SetX (20);
	$pdf->Cell(0,9,'Recommendation by Board of Examiner:',0);
	$pdf->SetX (20);
	$pdf->ln();
	$pdf->SetFont('Arial','B',9);
	$pdf->SetWidths(array(55,60));
	$pdf->Row(array("Components","Recommendation"));
	$pdf->SetFont('Arial','',9);
	for ($no=0; $no<$noU; $no++){
		if($recommendation_id == $idRecArray[$no]) {
			$pdf->Row(array($descRecArray[$no],"Recommended"));
		}
		else {
		$pdf->Row(array($descRecArray[$no],""));
		}
	
	
	}
	$pdf->SetX (20);
	$pdf->Cell(0,9,'Note: For any other additional comments, please enclose as attachments.',0);
	
$pdf->Output('Work_Completion_report('.$studentMatrixNo.').pdf','I');
close();
?>

