<?php
require('fpdf.php');

class PDF_MC_Table_asset extends FPDF
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
		$this->Cell(260,3,"MSU/PURCH/ARP/01 - 03",0,0,'R'); // Title
		$this->Ln(); 
		$this->Ln();
		$this->SetFont('Arial','B',12); // Arial bold 15
			$this->Cell(0,6,"MANAGEMENT & SCIENCE UNIVERSITY",0,1,"C"); // Title
		$this->Cell(0,6,"ASSET REGISTRATION DATABASE FOR CAPITAL ASSET & INVENTORY ".date("Y"),0,0,"C"); // Title
		$this->Ln(10); 
		//$this->Cell(4,6,''.$varColList("varColList"),0,0,"L"); // Title
		
		
	$varColList=array("No"=>array("width"=>6,"fieldNm"=>""),
					  "Item"=>array("width"=>50,"fieldNm"=>"category_desc"),
					    "Model"=>array("width"=>20,"fieldNm"=>"model"),
					  "Qty"=>array("width"=>8,"fieldNm"=>"quantity"),
					  "UOM"=>array("width"=>10,"fieldNm"=>"product_unit_desc"),
					  "Vendor 1"=>array("width"=>35,"fieldNm"=>"vendor_name"),
					  "Vendor 2"=>array("width"=>35,"fieldNm"=>"total"),
					  "Vendor 3"=>array("width"=>35,"fieldNm"=>"total"));
					
			//	$varColWidth=array(6,15,24,22,22,28,15,15,22,22,13,15,20);	
	}
	
	
	
	
	
	
	
	
	

	// Page footer
	function Footer()
	{
		$this->SetY(-15);// Position at 1.5 cm from bottom
		$this->SetFont('Arial','I',8);// Arial italic 8
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
