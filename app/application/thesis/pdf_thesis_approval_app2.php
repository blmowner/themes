<?php

include("../../../lib/common.php");
checkLogin();
$senateMtgDate=$_GET['smd'];

//require("../../lib/fpdf/fpdf.php");
require("../../../lib/fpdf/senate_approval_table_app2.php");


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

$varHeader=array("No","Thesis/Project ID","Thesis/Project Title","Student Name", "Matric No","Supervisor List","Decision by Senate");
$varColWidth=array(7,22,55,45,20,35,30);	  				
/*$varHeader=array("No","Student Name", "Matric No","Thesis Date","Thesis/Project ID","Thesis Type","Thesis/Project Title", "Senate Decision (Please Tick)");
$varColWidth=array(7,45,20,18,22,18,55,45);*/

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
if ($senateMtgDate=="") {
	$senateMtgDate = "";
}
else {
	$senateMtgDate = " AND (DATE_FORMAT(f.endorsed_date,'%Y-%m-%d') = CURDATE() OR DATE_FORMAT(f.endorsed_date,'%d-%b-%Y') = '$senateMtgDate') ";
}

$sqlProposal = "SELECT a.pg_thesis_id , a.id, DATE_FORMAT(a.report_date,'%d-%b-%Y') AS theReportDate, a.thesis_title, a.thesis_type, 
		b.description AS theThesisTypeDescription, a.introduction, a.objective, a.description, a.verified_by, 
		a.verified_date, a.verified_remarks, a.verified_status,a.status as endorsedStatus, a.discussion_status, 
		c1.description AS verifiedDesc, c2.description AS endorsedDesc, d.student_matrix_no, c1.description AS facultyStatus,
		a.endorsed_remarks
		FROM pg_proposal a
		LEFT JOIN ref_thesis_type b ON (b.id = a.thesis_type)
		LEFT JOIN ref_proposal_status c1 ON (c1.id = a.verified_status) 
		LEFT JOIN ref_proposal_status c2 ON (c2.id = a.status)
		LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id) 
		LEFT JOIN pg_proposal_approval f ON (f.id = a.pg_proposal_approval_id)
		WHERE a.status in ('APP','APC','DIS') "
		.$senateMtgDate." "."
		AND a.archived_status IS NULL 
		AND d.status = 'INP'
		ORDER BY a.pg_thesis_id, a.id, a.endorsed_date DESC";
$result_sqlProposal = $db->query($sqlProposal);

//$row_cnt = mysql_num_rows($result_sqlProposal);

	$no = 1;
	while($db->next_record())
	{
		$pgThesisId=$db->f('pg_thesis_id');	
		$studentMatrixNo=$db->f('student_matrix_no');
		$proposalId=$db->f('id');
		$reportDate=$db->f('theReportDate');
		$thesisTitle=$db->f('thesis_title');
		$description=$db->f('description');
		$verified_status=$db->f('verified_status');
		$facultyStatus=$db->f('facultyStatus');
		$endorsedRemarks=$db->f('endorsed_remarks');
		$endorsedStatus=$db->f('endorsedStatus');
		$endorsedDesc=$db->f('endorsedDesc');

// untuk supervisor
//------------

 $sqlSupervisor="SELECT ps.id, ps.ref_supervisor_type_id,
									ps.pg_employee_empid, rst.description  
									FROM  pg_supervisor ps 
									LEFT JOIN ref_supervisor_type rst ON (rst.id = ps.ref_supervisor_type_id) 
									WHERE ps.pg_student_matrix_no='$studentMatrixNo' 
									AND ps.ref_supervisor_type_id IN ('SV','CS','XS') 
									AND ps.pg_thesis_id = '$pgThesisId' 
									AND ps.status = 'A' 
									ORDER BY rst.seq";	
							
$result_sqlSupervisor = mysql_query($sqlSupervisor);	
$row_cnt = mysql_num_rows($result_sqlSupervisor);							
$no1=1;
$super = "";
while($row = mysql_fetch_array($result_sqlSupervisor)) {
	
	$sql1 = "SELECT name
			FROM new_employee
			WHERE empid =  '".$row['pg_employee_empid']."' ";		

	$result1 = $dbc->query($sql1); 
	$dbc->next_record();
	$staffName=$dbc->f('name');	
		
	$super .= $no1 . ". " . $staffName . " - ".$row["description"]." (" . $row['pg_employee_empid'] . ")\n";
	$no1++;
}

//------------
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

		array_push($setOfIngres, array($no,$pgThesisId,$thesisTitle,$studentName,$studentMatrixNo,$super,$endorsedDesc));	
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
	$pdf->SetFont('','B',7);
	//$pdf->Ln();	
	$tw=0;
	$pdf->Cell(20,0,'',0,0,'L');///>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
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

	$dbc->query($sqlStaff);
	$dbc->next_record();
	$staffName = $dbc->f("name");
	$pdf->CheckPageBreak(30);

	if ($pdf->bMargin = 50)
	{
		//$pdf->PageBreakTrigger;
		//$pdf->SetAutoPageBreak(true,50);
		$pdf->Ln();
		$pdf->SetFont('','B');
		$pdf->Cell(20,0,'',0,0,'L');//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
		$pdf->Cell(50,4,'Prepared By: ',0,0,'L');
		$pdf->Cell(50,4,'Verified By: ',0,0,'L');	
		$pdf->Cell(50,4,'Endorsed By: ',0,0,'L');	
		$pdf->SetFont('','');
		$pdf->Ln(10);
		$pdf->Cell(20,0,'',0,0,'L');//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
		$pdf->Cell(50,4,'.......................................................',0,0,'L');
		$pdf->Cell(50,4,'.......................................................',0,0,'L');
		$pdf->Cell(50,4,'.......................................................',0,0,'L');
		$pdf->Ln();
		$pdf->Cell(20,0,'',0,0,'L');//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
		$pdf->Cell(50,4,'Name: '.$staffName,0,0,'L');
		$pdf->Cell(51,4,'Name:',0,0,'L');
		$pdf->Cell(50,4,'Name: Professor Tan Sri Dato Wira',0,1,'L');
		$pdf->Cell(20,0,'',0,0,'L');//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
		$pdf->Cell(50,4,'Staff ID: '.$staffId,0,0,'L');
		$pdf->Cell(50,4,'Staff ID: ',0,0,'L');
		$pdf->Cell(50,4,' Dr. Mohd Shukri Ab Yajid, President of MSU',0,1,'L');
		$pdf->Cell(20,0,'',0,0,'L');//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
		$pdf->Cell(50,4,'Date:',0,0,'L');
		$pdf->Cell(50,4,'Date:',0,0,'L');
		$pdf->Cell(50,4,' Date:',0,0,'L');
		$pdf->SetDisplayMode(50);
	}
	else
	{
	
	}
//}
$pdf->Output();
?>