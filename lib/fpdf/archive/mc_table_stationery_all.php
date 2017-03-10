<?php
require('fpdf.php');

class PDF_MC_Table_stationery_all extends FPDF
{
var $widths;
var $aligns;

function SetWidths($w)
{
	//Set the array of column widths
	$this->widths=$w;
}

function SetAligns($a)
{
	//Set the array of column alignments
	$this->aligns=$a;
}

///////////////////////////////////////////////////////////////////////////////////////////////////
function Header()
	{
		$this->Image("../../theme/images/msuLogo.gif",10,9,30); // Logo
		$this->SetFont('Arial','B',5); // Arial bold 15
		$this->Cell(260,3,"Date Printed: ".date("F j, Y, h:i A"),0,0,"R"); // Move to the right
		$this->Ln(); 
		//$this->Cell(190,3,"Ownership Code: ",0,0,"R"); // Move to the right
		//$this->Ln(); 
		$this->Cell(260,3,"Printed By: ".$_SESSION['user_id'],0,0,"R"); // Move to the right
		$this->Ln(); 
		$this->Cell(260,3,"MSU/PURCH/ADP/08 - 02",0,0,'R'); // Title
		$this->Ln(); 
		$this->Ln();
		$this->SetFont('Arial','B',12); // Arial bold 15
		$this->Cell(0,6,"MANAGEMENT & SCIENCE UNIVERSITY",0,1,"C"); // Title
		$this->Cell(0,6,"STATIONERY REQUISTION RECORD ".date("Y"),0,1,"C"); // Title
		$this->Ln(6); 
		
	$varColList=array("No"=>array("width"=>6,"fieldNm"=>""),
					"Stationery ID "=>array("width"=>22,"fieldNm"=>"stationery_cd"),
					"Department "=>array("width"=>40,"fieldNm"=>"descriptions"),
					"Unit Department "=>array("width"=>40,"fieldNm"=>"description"),
					"Date Purchase "=>array("width"=>28,"fieldNm"=>"dateStationery"),
					"Product Description"=>array("width"=>33,"fieldNm"=>"category_desc"),
					"Quantity"=>array("width"=>12,"fieldNm"=>"quantity"),
					"Status"=>array("width"=>23,"fieldNm"=>"status_approve")

					);
			//	$varColWidth=array(6,15,24,22,22,28,15,15,22,22,13,15,20);	
	}
	
	
	
	
	
	
	
	
	

	// Page footer
	function Footer()
	{
		$this->SetY(-15);// Position at 1.5 cm from bottom
		$this->SetFont('Arial','I',5);// Arial italic 8
		//$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');// Page number
		//$this->Cell(0,10,'MSU',0,0,'C');
		$this->Ln();
		$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');// Page number
	}

///////////////////////////////////////////////////////////////////////////////////////////////////

function Row($data)
{
	//Calculate the height of the row
	$nb=0;
	for($i=0;$i<count($data);$i++)
		$nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
	$h=5*$nb;
	//Issue a page break first if needed
	$this->CheckPageBreak($h);
	//Draw the cells of the row
	for($i=0;$i<count($data);$i++)
	{
		$w=$this->widths[$i];
		$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
		//Save the current position
		$x=$this->GetX();
		$y=$this->GetY();
		//Draw the border
		$this->Rect($x,$y,$w,$h);
		//Print the text
		$this->MultiCell($w,5,$data[$i],0,$a);
		//Put the position to the right of the cell
		$this->SetXY($x+$w,$y);
	}
	//Go to the next line
	$this->Ln($h);
}

function CheckPageBreak($h)
{
	//If the height h would cause an overflow, add a new page immediately
	if($this->GetY()+$h>$this->PageBreakTrigger)
		$this->AddPage($this->CurOrientation);
}

function NbLines($w,$txt)
{
	//Computes the number of lines a MultiCell of width w will take
	$cw=&$this->CurrentFont['cw'];
	if($w==0)
		$w=$this->w-$this->rMargin-$this->x;
	$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
	$s=str_replace("\r",'',$txt);
	$nb=strlen($s);
	if($nb>0 and $s[$nb-1]=="\n")
		$nb--;
	$sep=-1;
	$i=0;
	$j=0;
	$l=0;
	$nl=1;
	while($i<$nb)
	{
		$c=$s[$i];
		if($c=="\n")
		{
			$i++;
			$sep=-1;
			$j=$i;
			$l=0;
			$nl++;
			continue;
		}
		if($c==' ')
			$sep=$i;
		$l+=$cw[$c];
		if($l>$wmax)
		{
			if($sep==-1)
			{
				if($i==$j)
					$i++;
			}
			else
				$i=$sep+1;
			$sep=-1;
			$j=$i;
			$l=0;
			$nl++;
		}
		else
			$i++;
	}
	return $nl;
}
}
?>
