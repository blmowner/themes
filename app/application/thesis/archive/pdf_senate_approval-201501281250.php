<?php

include("../../../lib/common.php");
checkLogin();

//require("../../lib/fpdf/fpdf.php");
require("../../../lib/fpdf/senate_approval_table.php");


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
/*$varPrint=isset($_REQUEST["print"])?$_REQUEST["print"]:"";
$varPrintList=isset($_REQUEST["printlist"])?$_REQUEST["printlist"]:"";
$varid=isset($_REQUEST["request_by"])?$_GET["pid"]:"";



$varPrintSplit=array();
//$varColWidth=array();
$strPrint="";
$query_str="";


if(strlen($varPrintList)>0)
{
	$varPrintSplit=explode(",",$varPrintList);
	
	foreach($varPrintSplit as $key => $value)
	{
		$strPrint.=(strlen($strPrint)>0?",":"")."'$value'";
	}
}*/
$strPrint=(strlen($strPrint)>0?"($strPrint)":$strPrint);

$varHeader=array("No","Student Information","Thesis Date","Thesis/Project ID","Thesis Type","Thesis/Project Title","Introduction","Objective", "Description", "Remarks by Student");
$varColWidth=array(7,25,16,22,17,35,35,35,35,35);	  				

$varDetail=array();



## SET OF VARIABLES(FOR INGREDIENTS)...
$setOfIngres = array();
$Ingres = array();

$sqlmain = " SELECT introduction, objective, description,pg_thesis_id
			 FROM pg_proposal
			 /*SELECT a.sr_name, a.sr_code, a.preparation, a.empid , b.name
			FROM sr1_main a 
			LEFT JOIN siso_employee b on(a.empid  = b.empid)
			WHERE a.sr1_cd = '$pid'*/";
$dbMain = $db;
$dbMain->query($sqlmain);
$dbMain->next_record();
$main_data = array($dbMain->f("introduction"),$dbMain->f("objective"),$dbMain->f("description"),$dbMain->f("pg_thesis_id"));

/*if(strpos($main_data[2],'<li>')==true && strpos($main_data[2],'</li>')==true) 
{
	$prep = preg_replace("/<ol[\s]?>/", "", $main_data[2]);
	$prep = preg_replace("/<\\/ol>/", "", $prep);
	$prep = preg_replace("/<li[\s]?>/", "<p>", $prep);
	$prep = preg_replace("/<\\/li>/", "</p>", $prep);
	
}*/

$sqlProposal = "SELECT a.pg_thesis_id, DATE_FORMAT(a.report_date,'%d-%b-%Y') AS report_date, a.thesis_title, 
			a.thesis_type, d.description AS thesis_desc, a.introduction, a.objective, a.description, a.proposal_remarks, 
			b.student_matrix_no, c.name
			FROM pg_proposal a
			LEFT JOIN pg_thesis b ON (b.id = a.pg_thesis_id)
			LEFT JOIN student c ON (c.matrix_no = b.student_matrix_no)
			LEFT JOIN ref_thesis_type d ON (d.id = a.thesis_type)
			WHERE a.verified_status = 'APP'			
			AND a.archived_status IS NULL
			AND b.status = 'INP'";

$result_sqlProposal = $db->query($sqlProposal);

//$row_cnt = mysql_num_rows($result_sqlProposal);



	$no = 1;
	while($db->next_record())
	{
		$studentName = $db->f("name");
		$studentMatrixNo = $db->f("student_matrix_no");
		$reportDate = $db->f("report_date");
		$thesisDesc = $db->f("thesis_desc");
		$thesisId = $db->f("pg_thesis_id");
		$thesisTitle = $db->f("thesis_title");
		$introduction = trim(strip_tags($db->f("introduction")));
		$objective = trim(strip_tags($db->f("objective")));
		$description = trim(strip_tags($db->f("description")));
		$proposalRemarks = $db->f("proposal_remarks");

		array_push($setOfIngres, array($no,$studentName."-".$studentMatrixNo,$reportDate,$thesisId,$thesisDesc,$thesisTitle,$introduction,$objective,$description,$proposalRemarks));
		$no++;
	}

if (strpos($main_data[2],'<li>')==true && strpos($main_data[2],'</li>')==true) 
{
    $tagIs = 'li'; 
}

if (strpos($main_data[2],'<p>')==true && strpos($main_data[2],'</p>')==true) 
{
    $tagIs = 'p'; 
}
$prep = preg_replace("/&nbsp;/", "", $main_data[2]);
$prepArr = getTextBetweenTags($tagIs, $prep);

			 
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
	$pdf->Cell(50,4,'Verified By: ',0,0,'L');	
	$pdf->Cell(50,4,'Endorsed By: ',0,0,'L');	
	$pdf->SetFont('','');
	$pdf->Ln(10);
	$pdf->Cell(50,4,'.......................................................',0,0,'L');
	$pdf->Cell(50,4,'.......................................................',0,0,'L');
	$pdf->Cell(50,4,'.......................................................',0,0,'L');
	$pdf->Ln();
	$pdf->Cell(50,4,'Name: '.$staffName,0,0,'L');
	$pdf->Cell(51,4,'Name:',0,0,'L');
	$pdf->MultiCell(50,4,'Name: Professor Tan Sri Dato Wira',0,1,'L');
	$pdf->Cell(50,4,'Staff ID: '.$staffId,0,0,'L');
	$pdf->Cell(50,4,'Staff ID: '.$staffId,0,0,'L');
	$pdf->Cell(50,4,' Dr. Mohd Shukri Ab Yajid, President of MSU',0,1,'L');
	$pdf->Cell(50,4,'Date:',0,0,'L');
	$pdf->Cell(50,4,'Date:',0,0,'L');
	$pdf->Cell(50,4,' Date:',0,0,'L');
	$pdf->SetDisplayMode(50);
//}
$pdf->Output();
?>