<?php

/*error_reporting(E_ALL);
ini_set('display_errors', 1);*/
include("../../../lib/common.php");
checkLogin();

//require("../../lib/fpdf/fpdf.php");
require("../../../lib/fpdf/senate_endorse_table.php");


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

$varHeader=array("No","Student Name", "Matric No","Thesis Title/Thesis ID","List of Supervisor","Result","Senate Remark");
$varColWidth=array(7,45,25,50,45,25,48);
	  				
	  				

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

			$j++;
			$k++;
			
		}
		$TT = "Id: ". $thesisId	. "<br/>" . "Title: " . $thesisTitle;
						
		$main_data = array_push($setOfIngres, array($no.".",$studentName,$studentMatrixNo,$TT,$g,$vivaStatDesc,''));
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
	/*function Header()
	{		
				
	}*/

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
	//$pdf->Ln(3); // >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
	
	// --- TABLE HEADER (START) ---
	$pdf->SetFont('Arial','',7);
	$varLabelLength=18;
	$varValueLength=60;
	$varSpaceLength=25; //ngam2 size
	//$this->AddPage();
	
	
	$pdf->SetLineWidth(.1);
	$pdf->SetFont('Arial','B',7);
	$pdf->Ln();	//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
	$tw=0;
	//adjust the table to the left (align)		
	$pdf->Cell(8,0,'',0,0,'L');
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
		//$pdf->Cell(5,4,'',0,0,'L');
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
	
	$sqlProposal = "SELECT a.pg_thesis_id, DATE_FORMAT(a.report_date,'%d-%b-%Y') AS report_date, a.thesis_title, 
	a.thesis_type, d.description AS thesis_desc, a.introduction, a.objective, a.description,  
	b.student_matrix_no
	FROM pg_proposal a
	LEFT JOIN pg_thesis b ON (b.id = a.pg_thesis_id)
	LEFT JOIN ref_thesis_type d ON (d.id = a.thesis_type)
	WHERE a.verified_status in ('APP','AWC')
	AND a.archived_status IS NULL
	AND b.status = 'INP'";
	
	$result_sqlProposal = $db->query($sqlProposal);
	$num_rows = mysql_num_rows($result_sqlProposal);
	
	if ($num_rows > 7)
	{
		$pdf->SetAutoPageBreak(true,50);
		$pdf->Cell(15,4,'',0,0,'L');
		$pdf->Ln();
		$pdf->SetFont('','B');
		$pdf->Cell(15,4,'',0,0,'L');/////////
		$pdf->Cell(50,4,'Prepared By: ',0,0,'L');
		$pdf->Cell(50,4,'Verified By: ',0,0,'L');	
		$pdf->Cell(50,4,'Endorsed By: ',0,0,'L');	
		$pdf->SetFont('','');
		$pdf->Ln(10);
		$pdf->Cell(15,4,'',0,0,'L');///////////
		$pdf->Cell(50,4,'.......................................................',0,0,'L');
		$pdf->Cell(50,4,'.......................................................',0,0,'L');
		$pdf->Cell(50,4,'.......................................................',0,0,'L');
		$pdf->Ln();
		$pdf->Cell(15,4,'',0,0,'L');/////////////////
		$pdf->Cell(50,4,'Name: '.$staffName,0,0,'L');
		$pdf->Cell(51,4,'Name:',0,0,'L');
		$pdf->MultiCell(50,4,'Name: Professor Tan Sri Dato Wira',0,1,'L');
		$pdf->Cell(15,4,'',0,0,'L');////////////////////
		$pdf->Cell(50,4,'Staff ID: '.$staffId,0,0,'L');
		$pdf->Cell(50,4,'Staff ID: ',0,0,'L');
		$pdf->Cell(50,4,' Dr. Mohd Shukri Ab Yajid, President of MSU',0,1,'L');
		$pdf->Cell(15,4,'',0,0,'L');/////////////////////////////////
		$pdf->Cell(50,4,'Date:',0,0,'L');
		$pdf->Cell(50,4,'Date:',0,0,'L');
		$pdf->Cell(50,4,' Date:',0,0,'L');
		$pdf->SetDisplayMode(50);
	}
	else
	{
		
		$pdf->Cell(15,4,'',0,0,'L');
		$pdf->Ln();
		$pdf->SetFont('','B');
		$pdf->Cell(15,4,'',0,0,'L');/////////
		$pdf->Cell(50,4,'Prepared By: ',0,0,'L');
		$pdf->Cell(50,4,'Verified By: ',0,0,'L');	
		$pdf->Cell(50,4,'Endorsed By: ',0,0,'L');	
		$pdf->SetFont('','');
		$pdf->Ln(10);
		$pdf->Cell(15,4,'',0,0,'L');///////////
		$pdf->Cell(50,4,'.......................................................',0,0,'L');
		$pdf->Cell(50,4,'.......................................................',0,0,'L');
		$pdf->Cell(50,4,'.......................................................',0,0,'L');
		$pdf->Ln();
		$pdf->Cell(15,4,'',0,0,'L');/////////////////
		$pdf->Cell(50,4,'Name: '.$staffName,0,0,'L');
		$pdf->Cell(51,4,'Name:',0,0,'L');
		$pdf->MultiCell(50,4,'Name: Professor Tan Sri Dato Wira',0,1,'L');
		$pdf->Cell(15,4,'',0,0,'L');////////////////////
		$pdf->Cell(50,4,'Staff ID: '.$staffId,0,0,'L');
		$pdf->Cell(50,4,'Staff ID: ',0,0,'L');
		$pdf->Cell(50,4,' Dr. Mohd Shukri Ab Yajid, President of MSU',0,1,'L');
		$pdf->Cell(15,4,'',0,0,'L');/////////////////////////////////
		$pdf->Cell(50,4,'Date:',0,0,'L');
		$pdf->Cell(50,4,'Date:',0,0,'L');
		$pdf->Cell(50,4,' Date:',0,0,'L');
		$pdf->SetDisplayMode(50);
	
	}
//}

	////for every listed thesis
	/*foreach($setOfIngres as $key => $value)
	{*/
		//Retrieve Data from database
		
		//$row_cnt = mysql_num_rows($result_sqlProposal);
		
		
		
		$no = 1;
		while($db->next_record())
		{
			$pdf->AddPage();
			
			$studentMatrixNo = $db->f("student_matrix_no");
			$reportDate = $db->f("report_date");
			$thesisDesc = $db->f("thesis_desc");
			$thesisId = $db->f("pg_thesis_id");
			$thesisTitle = $db->f("thesis_title");
			$introduction = trim(strip_tags($db->f("introduction")));
			$objective = trim(strip_tags($db->f("objective")));
			$description = trim(strip_tags($db->f("description")));
			
			
			$sql3 = "SELECT name
			FROM student
			WHERE matrix_no = '$studentMatrixNo'";		
			
			$result3 = $dbk->query($sql3); 
			$dbk->next_record();
			$studentName=$dbk->f('name');
			//Rect($x, $y, $w, $h, $style='') function
			////////////////////////////////////START DISPLAY PDF////////////////////////////////////////					
			//$this->Cell(0,10,"",0,0,"C"); // Title
			//Student Name
			//$pdf->SetAligns(5);
			$pdf->SetFont('Arial','B',9);
			//$pdf->Ln(10);
			//$pdf->Ln();
			$pdf->Ln();
			$pdf->Cell(5,4,'',0,0,'L');
			$pdf->Cell(32,4,'Student Name',0,0,'L');
			$pdf->SetFont('Arial','',9);
			$pdf->Cell(50,4,": ".$studentName,0,0,'L');
		
			//Matric No
			$pdf->SetFont('Arial','B',9);
			$pdf->Ln();
			$pdf->Cell(5,4,'',0,0,'L');		
			$pdf->Cell(32,4,'Matric No',0,0,'L');
			$pdf->SetFont('Arial','',9);
			$pdf->Cell(50,4,": ".$studentMatrixNo,0,0,'L');
			
			//Thesis ID
			$pdf->SetFont('Arial','B',9);
			$pdf->Ln();
			$pdf->Cell(5,4,'',0,0,'L');
			$pdf->Cell(32,4,'Thesis/Project ID',0,0,'L');
			$pdf->SetFont('Arial','',9);
			$pdf->Cell(50,4,": ".$thesisId,0,0,'L');
			
			//Thesis ID
			$pdf->SetFont('Arial','B',9);
			$pdf->Ln();
			$pdf->Cell(5,4,'',0,0,'L');
			$pdf->Cell(32,4,'Thesis/Project Title',0,0,'L');
			$pdf->SetFont('Arial','',9);
			$pdf->Cell(50,4,": ".$thesisTitle,0,0,'L');

			//Thesis ID
			$pdf->SetFont('Arial','B',9);
			$pdf->Ln();
			$pdf->Cell(5,4,'',0,0,'L');
			$pdf->Cell(32,4,'Thesis Date',0,0,'L');
			$pdf->SetFont('Arial','',9);
			$pdf->Cell(50,4,": ".$reportDate,0,0,'L');
			$pdf->Ln();

			//Introduction
			$pdf->SetFont('Arial','B',9);
			$pdf->Ln();
			$pdf->Cell(5,4,'',0,0,'L');
			$pdf->Cell(32,4,'Introduction',0,0,'L');
			$pdf->SetFont('Arial','',9);
			$pdf->MultiCell(210,4,": ".$introduction,0,'L');
			
			//Objective
			$pdf->SetFont('Arial','B',9);
			$pdf->Ln();
			$pdf->Ln();
			$pdf->Ln();
			$pdf->Cell(5,4,'',0,0,'L');
			$pdf->Cell(32,4,'Objective',0,0,'L');
			$pdf->SetFont('Arial','',9);
			$pdf->MultiCell(210,4,": ".$objective,0,'L');
			$pdf->SetAutoPageBreak(true,45);
			//Description
			$pdf->SetFont('Arial','B',9);
			$pdf->Ln();
			$pdf->Ln();
			$pdf->Ln();
			$pdf->Cell(5,4,'',0,0,'L');
			$pdf->Cell(32,4,'Description',0,0,'L');
			$pdf->SetFont('Arial','',9);
			//$pdf->Rect(50, 99, 50, 4, ''); ///////////////////////table
			$pdf->MultiCell(210,4,": ".$description,0,'L');
			
			////////////////////////////////////END DISPLAY PDF////////////////////////////////////////	
			//$pdf->Cell($varColWidth[$key],4,$value,1,0,'L',1);
				
		
		$no++;
		}



	


$pdf->Output();
?>