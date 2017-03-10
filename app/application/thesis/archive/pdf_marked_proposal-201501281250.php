<?php

include("../../../lib/common.php");
checkLogin();

//require("../../lib/fpdf/fpdf.php");
require("../../../lib/fpdf/marked_proposal_table.php");


/*function getTextBetweenTags($tag, $html, $strict=0)
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
}*/


if(isset($_GET['pid']) && $_GET['pid']=='')
{
	$pid = $_GET['pid'];
}

$strPrint=(strlen($strPrint)>0?"($strPrint)":$strPrint);

$varHeader=array("No","Student Information","Thesis Date","Thesis/Project ID","Thesis Type","Thesis/Project Title","Introduction","Objective", "Description", "Remarks by Student");
$varColWidth=array(7,25,16,22,17,35,35,35,35,35);	  				

$varDetail=array();

## SET OF VARIABLES(FOR INGREDIENTS)...
$setOfIngres = array();
$Ingres = array();

$sqlIng = "SELECT a.pg_thesis_id, DATE_FORMAT(a.report_date,'%d-%b-%Y') AS report_date, a.thesis_title, 
			a.thesis_type, d.description AS thesis_desc, a.introduction, a.objective, a.description, a.proposal_remarks, 
			b.student_matrix_no, c.name
			FROM pg_proposal a
			LEFT JOIN pg_thesis b ON (b.id = a.pg_thesis_id)
			LEFT JOIN student c ON (c.matrix_no = b.student_matrix_no)
			LEFT JOIN ref_thesis_type d ON (d.id = a.thesis_type)
			WHERE a.marked_status = 'MAR'
			AND a.archived_status IS NULL
			AND b.status = 'INP'";
$dbIng = $db;
$dbIng->query($sqlIng);

$no = 1;

while($dbIng->next_record()) 
{
	$studentName = $dbIng->f("name");
	$studentMatrixNo = $dbIng->f("student_matrix_no");
	$reportDate = $dbIng->f("report_date");
	$thesisDesc = $dbIng->f("thesis_desc");
	$thesisId = $dbIng->f("pg_thesis_id");
	$thesisTitle = $dbIng->f("thesis_title");
	$introduction = trim(strip_tags($dbIng->f("introduction")));
	$objective = trim(strip_tags($dbIng->f("objective")));
	$description = trim(strip_tags($dbIng->f("description")));
	$proposalRemarks = $dbIng->f("proposal_remarks");
	
	/*if (strpos($main_data[2],'<p>')==true && strpos($main_data[2],'</p>')==true) 
	{
		$tagIs = 'p'; 
	}	*/
	
	$prep_thesis_title = preg_replace("/&nbsp;/", "", $thesisTitle);
	//$prep_introduction = preg_replace("/&nbsp;/", "", $introduction);
	//$prepArr = getTextBetweenTags($tagIs, $prep_introduction);

	array_push($setOfIngres, array($no,$studentName."-".$studentMatrixNo,$reportDate,$thesisId,$thesisDesc,$prep_thesis_title,$introduction,$objective,$description,$proposalRemarks));
	$no++;
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
		$this->SetY(-15);// Position at 1.5 cm from bottom
		$this->SetFont('Arial','I',8);// Arial italic 8
		//$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');// Page number
		//$this->Cell(0,10,'MSU',0,0,'C');
		$this->Ln();
		$this->Cell(0,10,'Page '.$this->PageNo().'',0,0,'L');// Page number
	}

}



$pdf=new PDF_MC_Table_sr1("L","mm","letter");
//$pdf=new PDF();

//foreach($varDetail3Tab as $key3 => $value3)
//{
	$pdf->AddPage();
	$pdf->SetFont('Arial','B',5); // Arial bold 15
	/*$pdf->Text(170,10,"Date Printed: ".date("F j, Y, h:i A"));
	$pdf->Text(170,12,"Printed By: ".$_SESSION['user_id']);
	$pdf->Text(170,14,"MSU/NT/012014/V1 - 01"); // Title*/
	$pdf->SetFont('Arial','B',10); // Arial bold 15
	$pdf->Ln(3);
	
	// --- TABLE HEADER (START) ---
	$pdf->SetFont('Arial','',7);
	$varLabelLength=18;
	$varValueLength=60;
	$varSpaceLength=25; //ngam2 size
	//$this->AddPage();
	
	
	$pdf->SetLineWidth(.1);
	$pdf->SetFont('Arial','B',7);
	$pdf->Ln();	
	$tw=0;
	foreach($varHeader as $key=>$value)
	{
		$pdf->SetFillColor(240,240,240);
		$pdf->Cell($varColWidth[$key],4,$value,1,0,'L',1);
		
	}
	$pdf->SetFont('','');
	$pdf->Ln();
	
	// --- TABLE HEADER (FINISH) ---
	
	$pdf->SetWidths($varColWidth);

	srand(microtime()*1000000);

/*	$sscounter = 1;
	foreach($product_name as $index => $value)
	{
			
			$pdf->Cell($varColWidth[0],4,$sscount,1,0,'L',0);
			$pdf->Cell($varColWidth[1],4,$value,1,0,'L',0);
			$pdf->Cell($varColWidth[2],4,$quantity[$index],1,0,'L',0);
			$pdf->Cell($varColWidth[3],4,$specification[$index],1,0,'L',0);
			$pdf->Cell($varColWidth[4],4,$item_category[$index],1,0,'L',0);
			$pdf->Cell($varColWidth[5],4,$order_category[$index],1,0,'L',0);
			$pdf->Ln();
			
			$sscounter++;
	}*/
	//$value3 = array(array('qw','wen fwef ewf wefk fwef wekf wef wekf wekf wkef fwefw fwe fwe fwe fw f wef wef wef wef wef we','haha','test','test','test'));
	
	foreach($setOfIngres as $key => $value)
	{
		$pdf->Row($value);
	}
	

	// --- GENERATE SIGNATURE (START) ---
	$staffId = $_SESSION['user_id'];
	$sqlStaff = "SELECT name
				FROM new_employee
				WHERE empid = '$staffId'";

	$db->query($sqlStaff);
	$db->next_record();
	$staffName = $db->f("name");

	
	$pdf->Ln();
	$pdf->SetFont('','B');
	$pdf->Cell(50,4,'Prepared By: ',0,0,'L');	
	//$pdf->Cell(51,4,'Verified By: ',0,0,'L');	
	$pdf->SetFont('','');
	$pdf->Ln(10);
	$pdf->Cell(50,4,'............................................',0,0,'L');
	//$pdf->Cell(51,4,'............................................',0,0,'L');
	$pdf->Ln();
	$pdf->Cell(50,4,'Name: '.$staffName,0,1,'L');
	//$pdf->Cell(51,4,'Name: '.$_SESSION['user_id'],0,1,'L');
	$pdf->Cell(50,4,'Staff ID: '.$staffId,0,0,'L');
	$pdf->SetDisplayMode(50);
//}
$pdf->Output();
?>