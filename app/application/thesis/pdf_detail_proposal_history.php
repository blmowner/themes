<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: detail_proposal_history_student.php
//
// Created by: SyEdZ
// Created Date: 10 Jun 2015
// Modified by: 
// Modified Date: 
//
//**************************************************************************************


//Read common library for page execution i.e. database connection. login validation

include("../../../lib/common.php");
checkLogin();

function retrieveJobAreaId($jobAreaId)
{
	global $dbc;
	$sql = "SELECT area from job_list_category WHERE JobArea = '$jobAreaId'";
	$dbc->query($sql);
	$dbc->next_record();
	$jobAreaDesc = $dbc->f('area');
	return $jobAreaDesc;

}
session_start();

//$id1=$_GET['pid'];

$sql1 = "SELECT a.thesis_title, b.description as thesis_type_desc, a.introduction, a.objective, a.description, a.endorsed_by, 
		DATE_FORMAT(a.endorsed_date,'%d-%b-%Y') AS theEndorsedDate, a.endorsed_remarks, a.status, a.verified_by, 
		DATE_FORMAT(a.verified_date,'%d-%b-%Y') AS theVerifiedDate, a.verified_remarks, a.verified_status, a.discussion_status, 
		a.thesis_type, b.description AS theThesisTypeDescription, c1.description AS verified_desc, c2.description AS status_desc 
		FROM pg_proposal a 
		LEFT JOIN ref_thesis_type b ON (b.id=a.thesis_type) 
		LEFT JOIN ref_proposal_status c1 ON (c1.id=a.verified_status) 
		LEFT JOIN ref_proposal_status c2 ON (c2.id=a.status) 
		WHERE a.id ='$id'";
				
		$result1 = $db->query($sql1); 
		$db->next_record();

		$thesisTitle=$db->f('thesis_title');
		$thesisTypeDesc=$db->f('thesis_type_desc');
		$endorsedBy=$db->f('endorsed_by');
		$name=$db->f('name');
		$endorsedDate=$db->f('theEndorsedDate');
		$approvalRemark=$db->f('approval_remark');
		$statusDesc=$db->f('status_desc');
		$verifiedBy=$db->f('verified_by');
		$verifiedDate=$db->f('theVerifiedDate');
		$verifiedRemarks=$db->f('verified_remarks');
		$verifiedDesc=$db->f('verified_desc');				
		$introduction=$db->f('introduction');
		$objective=$db->f('objective');
		$description=$db->f('description');
		$theThesisTypeDescription=$db->f('theThesisTypeDescription');
		$discussionStatus=$db->f('discussion_status');
		
		$row_cnt1 = mysql_num_rows($result1);		


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
			
///////////// SQL OTHER ///////////////////////////////

	$sql4 = "SELECT name AS verified_name
			FROM new_employee 
			WHERE empid = '$verifiedBy'";
				
			$result4 = $dbc->query($sql4); 
			$dbc->next_record();
			$verifiedName=$dbc->f('verified_name');
			
	$sql5 = "SELECT name AS endorsed_name
			FROM new_employee 
			WHERE empid = '$endorsedBy'";
				
			$result5 = $dbc->query($sql5); 
			$dbc->next_record();
			$endorsedName=$dbc->f('endorsed_name');
			



/////////////////////////////////////////////////////////			
			
	
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
$pdf->Cell(0,9,'Archive Report',1);
$pdf->Ln(10);
//Set x and y position for the main text, reduce font size and write content
	// minor changing
	
	$pdf->SetFont('Arial','B',9);	
	$pdf->SetWidths(array(180));
	$pdf->Row1(array("Verification by Faculty",));
	
	
	$pdf->SetFont('Arial','B',9);	
	$pdf->SetWidths(array(35,145));
	$pdf->Row1(array("Verification Status",$verifiedDesc));
	
	$pdf->SetFont('Arial','B',9);	
	$pdf->SetWidths(array(35,145));
	$pdf->Row1(array("Verified By",$verifiedName));
	
	$pdf->SetFont('Arial','B',9);	
	$pdf->SetWidths(array(35,145));
	$pdf->Row1(array("Verified Date",$verifiedDate));
	
	$pdf->SetFont('Arial','B',9);	
	$pdf->SetWidths(array(35,145));
	$pdf->Row1(array("Remarks",$verifiedRemarks)); 
	
	$pdf->ln();
	
	$pdf->SetFont('Arial','B',9);	
	$pdf->SetWidths(array(180));
	$pdf->Row1(array("Endorsement by Senate",));
	
	$pdf->SetFont('Arial','B',9);	
	$pdf->SetWidths(array(35,145));
	$pdf->Row1(array("Endorsement Status",$statusDesc)); 
	
	$pdf->SetFont('Arial','B',9);	
	$pdf->SetWidths(array(35,145));
	$pdf->Row1(array("Endorsed By",$endorsedName)); 
	
	$pdf->SetFont('Arial','B',9);	
	$pdf->SetWidths(array(35,145));
	$pdf->Row1(array("Endorsed Date",$endorsedDate)); 
	
	$pdf->SetFont('Arial','B',9);	
	$pdf->SetWidths(array(35,145));
	$pdf->Row1(array("Remarks",$approvalRemark)); 
	
	$pdf->ln();
	
	
	$pdf->SetFont('Arial','B',9);	
	$pdf->SetWidths(array(180));
	$pdf->Row1(array("Outline of Proposed Research/Case Study",));
	
	$pdf->SetFont('Arial','B',9);	
	$pdf->SetWidths(array(35,145));
	$pdf->Row1(array("Thesis Title",$thesisTitle)); 
	
	$pdf->SetFont('Arial','B',9);	
	$pdf->SetWidths(array(35,145));
	$pdf->Row1(array("Thesis Type",$thesisTypeDesc)); 
	
	$html=$introduction;
	$tmpHTML = strip_tags($html);
	
	$pdf->SetFont('Arial','B',9);	
	$pdf->SetWidths(array(35,145));
	$pdf->Row1(array("Introduction",$tmpHTML)); 
	
	
	$html=$objective;
	$tmpHTML = strip_tags($html);
	
	$pdf->SetFont('Arial','B',9);	
	$pdf->SetWidths(array(35,145));
	$pdf->Row1(array("Objective",$tmpHTML)); 
	
	
	$html=$description;
	$tmpHTML = strip_tags($html);
	
	$pdf->SetFont('Arial','B',9);	
	$pdf->SetWidths(array(35,145));
	$pdf->Row1(array("Brief Description",$description)); 
	
	$pdf->ln();
	
	////////////////// Proposal Area.
	
	$pdf->SetFont('Arial','B',9);	
			$pdf->SetWidths(array(180));
			$pdf->Row1(array("Proposal Area",));
	
	$sql2 = "SELECT id,job_id1_area,job_id2_area,job_id3_area,job_id4_area,job_id5_area,job_id6_area
			FROM pg_proposal_area
			WHERE pg_proposal_id = '$id'"; 

			$result2 = $dba->query($sql2); 

			$dba->next_record();
			$no=1;
			$row_cnt = mysql_num_rows($result2);
			if ($row_cnt>0) {
				$jobIdArea1=$dba->f('job_id1_area');
				$jobIdArea2=$dba->f('job_id2_area');
				$jobIdArea3=$dba->f('job_id3_area');
				$jobIdArea4=$dba->f('job_id4_area');
				$jobIdArea5=$dba->f('job_id5_area');
				$jobIdArea6=$dba->f('job_id6_area');
			
			$pdf->SetWidths(array(10,80,10,80));
			$pdf->Row2(array("No","Proposal Area"," No", "Proposal Area"));
			
			$pdf->SetFont('Arial','B',9);	
			$pdf->SetWidths(array(10,80,10,80));
			$pdf->Row1(array("1.",retrieveJobAreaId($jobIdArea1)," 4.", retrieveJobAreaId($jobIdArea4)));
			$pdf->SetFont('Arial','B',9);	
			$pdf->SetWidths(array(10,80,10,80));
			$pdf->Row1(array("2.",retrieveJobAreaId($jobIdArea2)," 5.", retrieveJobAreaId($jobIdArea5)));
			$pdf->SetFont('Arial','B',9);	
			$pdf->SetWidths(array(10,80,10,80));
			$pdf->Row1(array("3.",retrieveJobAreaId($jobIdArea3)," 6.", retrieveJobAreaId($jobIdArea6)));				
				
			}
		
			else {
			$pdf->SetFont('Arial','B',9);	
			$pdf->SetWidths(array(180));
			$pdf->Row1(array("Proposal Area",));}
$pdf->Output('DP_'.$referenceNo."_".$studentMatrixNo.'.pdf','I');
close();

?>
