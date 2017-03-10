<?php
include("../../../lib/common.php");
checkLogin();


require("../../../lib/fpdf/senate_endorse_table2.php");

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
//////////////////////////SECTION A///////////////////////////////	
	//$pdf->SetXY(20,38);
	//$pdf->SetFont("Arial","B","9");
	//$pdf->Cell(70,5,"List of Thesis for Senate Endorsement",0,0);
	//$pdf->Ln();
	$pdf->SetX (20);
	$pdf->SetFont("Arial","I","9");
	$pdf->Cell(0,5,"",0,0);
	$pdf->Ln();
	$pdf->SetFont('Arial','B',9);
	//Table with 14 rows and 5 columns

	$spW = 45; /////// $spW is variable for width for supervisor multicell size and send parameter in row2 at line 190. it must same as row below
	$pdf->SetWidths(array(7,45,25,50,45,25,48));
	$pdf->Row2(array("No","Student Name", "Matric No","Thesis Title/Thesis ID","List of Supervisor","Result","Senate Remark"), '', '');
	
	$pdf->SetFont('Arial','',9);

$sqlProposal = "SELECT a.id, a.viva_status, d.description AS vivaStatDesc, a.pg_calendar_id, a.student_matrix_no, 
a.pg_thesis_id, defense_date, DATE_FORMAT(f.defense_date,'%d-%b-%Y') AS viva_date, g.id AS pg_proposal_id,
DATE_FORMAT(f.defense_stime,'%h:%i%p') AS viva_stime, DATE_FORMAT(f.defense_etime,'%h:%i%p') AS viva_etime, 
f.venue, g.thesis_title
FROM pg_viva a
LEFT JOIN pg_evaluation_viva b ON (b.pg_viva_id = a.id)
LEFT JOIN pg_amendment c ON (c.pg_viva_id = a.id)
LEFT JOIN ref_recommendation d ON (d.id = a.viva_status)
LEFT JOIN pg_calendar f ON (f.id = a.pg_calendar_id)
LEFT JOIN pg_proposal g ON (g.pg_thesis_id = a.pg_thesis_id)
WHERE a.status = 'ARC'
AND a.submit_status = 'CON'
AND c.status <> 'ARC'
AND ((a.viva_status = 'PMI' AND c.confirm_status = 'CON2') 
OR (a.viva_status = 'PMA' AND c.confirm_status = 'CON2') 
OR (a.viva_status = 'PAS' AND c.status = 'ARC1'))
-- OR (a.viva_status = 'FAI' AND a.appeal_result = 'DIS')
-- OR (a.viva_status = 'PMR' AND c.confirm_status = 'CON2')
-- OR (a.viva_status = 'FAI' AND a.appeal_result IS NULL AND CURDATE() > b.end_appeal_date ))
AND b.final_result IN ('PAS', 'PMI', 'PMA')
AND g.archived_status IS NULL
-- AND a.id NOT IN (SELECT pg_viva_id FROM pg_senate)";

$result_sqlProposal = $db->query($sqlProposal);

//$row_cnt = mysql_num_rows($result_sqlProposal);



	$no = 1;
	while($db->next_record())
	{

		$studentMatrixNo = $db->f("student_matrix_no");
		$vivaStatDesc = $db->f("vivaStatDesc");
		$thesisDesc = $db->f("thesis_desc");
		$thesisId = $db->f("pg_thesis_id");
		$thesisTitle = $db->f("thesis_title");
		$proposalId = $db->f("pg_proposal_id");

		
		
		$sql3 = "SELECT name
				FROM student
				WHERE matrix_no = '$studentMatrixNo'";		
		if (substr($studentMatrixNo,0,2) != '07') { 
			$dbConnStudent= $dbc; 
		} 
		else { 
			$dbConnStudent=$dbc1; 
		}
		$result3 = $dbConnStudent->query($sql3); 
		$dbConnStudent->next_record();
		$studentName=$dbConnStudent->f('name');
		//$senateRevision = '<input type="checkbox" name="checkbox" value="checkbox" checked="checked" />Approved';
		$senate = array();
		$sp = array();
		$j = 1;
		$k = 0;

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
		
		$result_sql_supervisor = $db_klas2->query($sql_supervisor); 
		$row_cnt_supervisor = mysql_num_rows($result_sql_supervisor);	
		while($db_klas2->next_record())
		{					
			$empid = $db_klas2->f("pg_employee_empid");
			$spType = $db_klas2->f("supervisor_type");

			$sqlname = "Select name from new_employee where empid = '$empid'";
			$dbc2 = $dbc;		
			$result_sqlname = $dbc2->query($sqlname); 
			$dbc2->next_record();
			$spName = $dbc2->f("name");

			$spv = $j . ". " . $spName . " - " .$spType;

			//$spArray = array($spv);
			//$sparray2 = array_push($spArray);
			//$g = array_push($spv);
			//$main_data = array_push($setOfIngres, array('','','','',$spv,'',''));
			//$main_data = array_push('', array('','','','',$spv,'',''));
			$sp[] = $spv;
			//$contentSp = array_push($sp);
			$j++;
			$k++;
			
		}
		$TT = "Id: ". $thesisId	. "<br/>" . "Title: " . $thesisTitle;
		//$main_data = array_push($setOfIngres, array($no.".",$studentName,$studentMatrixNo,$TT,$g,$vivaStatDesc,''));
		$testTitle = 
		"SdadasdasdaadsaSdadasdasdaadsadSdadasdasdaadsadSdadasdasdaadsadSdadasdasdaadsadSdadasdasdaadsad
		SdadasdasdaadsaSdadasdasdaadsadSdadasdasdaadsadSdadasdasdaadsadSdadasdasdaadsadSdadasdasdaadsad
		SdadasdasdaadsaSdadasdasdaadsadSdadasdasdaadsadSdadasdasdaadsadSdadasdasdaadsadSdadasdasdaadsad
		SdadasdasdaadsaSdadasdasdaadsadSdadasdasdaadsadSdadasdasdaadsadSdadasdasdaadsadSdadasdasdaadsad
		SdadasdasdaadsaSdadasdasdaadsadSdadasdasdaadsadSdadasdasdaadsadSdadasdasdaadsadSdadasdasdaadsad";
		
		$pdf->Row2(array($no.".",$studentName,$studentMatrixNo,$thesisTitle,'',$vivaStatDesc, ''), $row_cnt_supervisor, $sp, $spW);
		$no++;	

	}
	
	
	$staffId = $_SESSION['user_id'];
	$sqlStaff = "SELECT name
				FROM new_employee
				WHERE empid = '$staffId'";

	$dbc->query($sqlStaff);
	$dbc->next_record();
	$staffName = $dbc->f("name");

	$pdf->SetAutoPageBreak(true,50);
	$pdf->Cell(0,4,'',0,0,'L');
	$pdf->Ln();
	$pdf->SetFont('','B');
	$pdf->Cell(20,4,'',0,0,'L');/////////
	$pdf->Cell(80,4,'Prepared By: ',0,0,'L');
	$pdf->Cell(80,4,'Verified By: ',0,0,'L');	
	$pdf->Cell(200,4,'Endorsed By: ',0,0,'L');	
	$pdf->SetFont('','');
	$pdf->Ln(10);
	$pdf->Cell(20,4,'',0,0,'L');///////////
	$pdf->Cell(80,4,'.......................................................',0,0,'L');
	$pdf->Cell(80,4,'.......................................................',0,0,'L');
	$pdf->Cell(200,4,'.......................................................',0,0,'L');
	$pdf->Ln();
	$pdf->Cell(20,4,'',0,0,'L');/////////////////
	$pdf->Cell(80,4,'Name: '.$staffName,0,0,'L');
	$pdf->Cell(80,4,'Name:',0,0,'L');
	$pdf->Cell(200,4,'Name: Professor Tan Sri Dato Wira',0,1,'L');
	$pdf->Cell(20,4,'',0,0,'L');////////////////////
	$pdf->Cell(80,4,'Staff ID: '.$staffId,0,0,'L');
	$pdf->Cell(80,4,'Staff ID: ',0,0,'L');
	$pdf->Cell(200,4,'Dr. Mohd Shukri Ab Yajid, President of MSU',0,1,'L');
	$pdf->Cell(20,4,'',0,0,'L');/////////////////////////////////
	$pdf->Cell(80,4,'Date:',0,0,'L');
	$pdf->Cell(80,4,'Date:',0,0,'L');
	$pdf->Cell(200,4,'Date:',0,0,'L');
	$pdf->SetDisplayMode(50);

//$pdf->Output('VIVA('.$matrixNo.').pdf','I');
$pdf->Output();

?>
