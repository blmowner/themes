<? session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Submit Defense Proposal</title>
		<link rel="stylesheet" type="text/css" href="../../theme/css/<?php echo $css; ?>" />
		<link rel="stylesheet" type="text/css" href="../../theme/css/colorbox.css" media="screen" />
		<link rel="stylesheet" href="../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />    
		<script src="../../lib/js/jquery.min2.js"></script>
		<script src="../../lib/js/jquery.colorbox.js"></script>
		<script src="../../lib/js/jquery.mask_input-1.3.js"></script>
		<script type="text/javascript" src="../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
		<script src="../../lib/js/datePicker/jquery.ui.core.js"></script>
		<script src="../../lib/js/datePicker/jquery.ui.widget.js"></script>
		<script src="../../lib/js/datePicker/jquery.ui.datepicker.js"></script>
		<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
		<script language="JavaScript" src="../../../lib/js/windowopen.js"></script></head> 
	<body> 
	
<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: new_defense.php
//
// Created by: Zuraimi
// Created Date: 24-Jun-2015
// Modified by: Zuraimi
// Modified Date: 24-Jun-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

$user_id=$_SESSION['user_id'];
$thesisId = $_GET['tid']; 
$proposalId = $_GET['pid'];
$roleType=$_GET['type'];
$matrixNo=$_GET['mn'];
$rolestatus=$_GET['role'];

$sqlamend1 = "SELECT id FROM pg_amendment
WHERE pg_thesis_id = '$thesisId'
AND pg_proposal_id = '$proposalId'
AND reference_no = '$referenceNo'
AND confirm_status IS NULL
AND STATUS = 'A'
AND confirm_by IS NULL
AND student_matrix_no = '$matrixNo'
AND amendment_status = 'SUB'";
		
$result_sqlamend1 = $dbf->query($sqlamend1); 
$dbf->next_record();
$amendmentId = $dbf->f('id');

if (!class_exists('DateTime')) {
	class DateTime {
		public $date;
	   
		public function __construct($date) {
			$this->date = strtotime($date);
		}
	   
		public function setTimeZone($timezone) {
			return;
		}
	   
		private function __getDate() {
			return date(DATE_ATOM, $this->date);   
		}
	   
		public function modify($multiplier) {
			$this->date = strtotime($this->__getDate() . ' ' . $multiplier);
		}
	   
		public function format($format) {
			return date($format, $this->date);
		}
	}
}

/**
 *
 * @create a roman numeral from a number
 *
 * @param int $num
 *
 * @return string
 *
 */
function romanNumerals($num) 
{
    $n = intval($num);
    $res = '';
 
    /*** roman_numerals array  ***/
    $roman_numerals = array(
                'M'  => 1000,
                'CM' => 900,
                'D'  => 500,
                'CD' => 400,
                'C'  => 100,
                'XC' => 90,
                'L'  => 50,
                'XL' => 40,
                'X'  => 10,
                'IX' => 9,
                'V'  => 5,
                'IV' => 4,
                'I'  => 1);
 
    foreach ($roman_numerals as $roman => $number) 
    {
        /*** divide to get  matches ***/
        $matches = intval($n / $number);
 
        /*** assign the roman char * $matches ***/
        $res .= str_repeat($roman, $matches);
 
        /*** substract from the number ***/
        $n = $n % $number;
    }
 
    /*** return the res ***/
    return $res;
}
	
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


if(isset($_POST['btnSave']) && ($_POST['btnSave'] <> ""))
{
	$pgVivaIdNew = "V".runnum2('id','pg_evaluation_viva');//main hold thesis id and student matrix no;
	$refVivaNew = "R".runnum2('reference_no','pg_evaluation_viva');//reference for main
	$pgVivaDetailNew = "D".runnum2('id','pg_evaluation_viva_detail');//viva detail hold for every supervisor that submit report regarding thesis on main
																	//section dbce comment
	//////Section A////////
	$overallStyle=$_REQUEST['overallStyle'];	
	$addRating = $_REQUEST['addRating'];
	$comment = $_REQUEST['comment'];
	/////end A////////////
	
	/// Single Comment : Save in pg_evaluation_detail////
	$commentB = $_REQUEST['commentB'];
	$commentC = $_REQUEST['commentC'];
	$commentE = $_REQUEST['commentE'];
	$commentA = $_REQUEST['commentA'];
	//////end single comment///////
	
	//////Section D: recommendation: save in pg_evaluation_detail//
	$recCheck = $_REQUEST['recCheck'];
	/////end D////////////
	
	////section E//
	$sectionEid = $_REQUEST['sectionEid'];
	$addoverall = $_REQUEST['addoverall'];
	////end///////
	
	$curdatetime = date("Y-m-d H:i:s");
	$curdatetime1 = date("Y-m-d H:i:s");

	$sqlviva = "SELECT id FROM pg_evaluation_viva 
	WHERE pg_thesis_id = '$thesisId'
	AND student_matrix_no = '$matrixNo'";
	
	$result_sqlviva = $dbf->query($sqlviva); 
	$dbf->next_record();
	
	$vivaIdExist = $dbf->f('id');
	if(empty($vivaIdExist))
	{
		$sqlviva = "INSERT INTO pg_evaluation_viva
		(id, pg_thesis_id, student_matrix_no, reference_no, status, insert_by, insert_date)
		VALUES
		('$pgVivaIdNew', '$thesisId' , '$matrixNo' , '$refVivaNew' , 'A', '$user_id', '$curdatetime')";
		
		$dba->query($sqlviva);
		
		$sqlviva = "INSERT INTO pg_evaluation_viva_detail 
		(id, pg_eva_viva_id, major_revision, other_comment, status, insert_by, insert_date, pg_empid_viva, recommendation_id, comment_sec_e, report_status, comment_sec_a)
		VALUES
		('$pgVivaDetailNew', '$pgVivaIdNew', '$commentB' , '$commentC' , 'A', '$user_id', '$curdatetime', '$user_id', '$recCheck', '$commentE', 'SAV', '$commentA')";
		
		$dba->query($sqlviva);
			
		$k = 1;
		while (list ($key,$val) = @each ($overallStyle)) 
		{
			$pgVivaStyleNew = "S".runnum2('id','pg_evaluation_viva_style');// content of section A
			
			$sqlviva = "INSERT INTO pg_evaluation_viva_style 
			(id, pg_eva_viva_detail_id, ref_overall_style_id, ref_overall_rating_id, insert_by, insert_date, status,seq, comments)
			VALUES
			('$pgVivaStyleNew', '$pgVivaDetailNew', '$overallStyle[$key]' , '$addRating[$key]' , '$user_id', '$curdatetime', 'A', '$k', '$comment[$key]')";
			
			$dba->query($sqlviva);
			$k++;
		}
		while (list ($key,$val) = @each ($sectionEid)) 
		{
			/*$pgVivaStyleNew = "S".runnum2('id','pg_evaluation_viva_style');// content of section A
			echo $sectionEid[$key].": ".$addoverall[$key]." Comment:".$comment[$key];
			echo "<br>";*/
			$pgVivaOverallNew = "O".runnum2('id','pg_evaluation_viva_overall'); // content of section E
			
			$sqloverall = "INSERT INTO pg_evaluation_viva_overall 
			(id, pg_eva_viva_detail_id, status, insert_by, insert_date, ref_overall_comment_id, ref_overall_rating_id)
			VALUES
			('$pgVivaOverallNew', '$pgVivaDetailNew', 'A', '$user_id', '$curdatetime', '$sectionEid[$key]', '$addoverall[$key]')";
			
			$dba->query($sqloverall);
			$k++;
		}
	}
	else{

		$sqlviva = "INSERT INTO pg_evaluation_viva_detail 
		(id, pg_eva_viva_id, major_revision, other_comment, status, insert_by, insert_date, pg_empid_viva, recommendation_id, comment_sec_e, report_status, comment_sec_a)
		VALUES
		('$pgVivaDetailNew', '$vivaIdExist', '$commentB' , '$commentC' , 'A', '$user_id', '$curdatetime', '$user_id', '$recCheck', '$commentE', 'SAV', '$$commentA')";
		
		$dba->query($sqlviva);
			
		$k = 1;
		while (list ($key,$val) = @each ($overallStyle)) 
		{
			$pgVivaStyleNew = "S".runnum2('id','pg_evaluation_viva_style');// content of section A
			
			$sqlviva = "INSERT INTO pg_evaluation_viva_style 
			(id, pg_eva_viva_detail_id, ref_overall_style_id, ref_overall_rating_id, insert_by, insert_date, status,seq, comments)
			VALUES
			('$pgVivaStyleNew', '$pgVivaDetailNew', '$overallStyle[$key]' , '$addRating[$key]' , '$user_id', '$curdatetime', 'A', '$k', '$comment[$key]')";
			
			$dba->query($sqlviva);
			$k++;
		}
		while (list ($key,$val) = @each ($sectionEid)) 
		{

			$pgVivaOverallNew = "O".runnum2('id','pg_evaluation_viva_overall'); // content of section E
			
			$sqloverall = "INSERT INTO pg_evaluation_viva_overall 
			(id, pg_eva_viva_detail_id, status, insert_by, insert_date, ref_overall_commment_id, ref_overall_rating_id)
			VALUES
			('$pgVivaOverallNew', '$pgVivaDetailNew', 'A', '$user_id', '$curdatetime', '$sectionEid[$key]', '$addoverall[$key]')";
			
			$dba->query($sqloverall);
			$k++;
		}	
	
	}
	$sqlviva = "SELECT b.id AS pg_eva_viva_detail_id, a.id AS pg_eva_viva_id
	FROM pg_evaluation_viva a
	LEFT JOIN pg_evaluation_viva_detail b ON (b.pg_eva_viva_id=a.id)
	WHERE a.student_matrix_no = '$matrixNo'
	AND a.status = 'A'
	AND b.pg_empid_viva = '$user_id'
	AND a.pg_thesis_id = '$thesisId'";
	
	$result_sqlviva = $dbf->query($sqlviva); 
	$dbf->next_record();
	
	$pg_eva_viva_detail_id = $dbf->f('pg_eva_viva_detail_id');
	$pg_eva_viva_id = $dbf->f('pg_eva_viva_id');
	$save = "1";
	echo "<script>window.location = 'edit_viva_report.php?tid=".$thesisId."&rid".$referenceNo."&mn=".$matrixNo."&role=".$rolestatus."&save=".$save."&pd=".$pg_eva_viva_detail_id."';</script>";
	
}

if(isset($_POST['btnSubmit']) && ($_POST['btnSubmit'] <> ""))
{
	$pgVivaIdNew = "V".runnum2('id','pg_evaluation_viva');//main hold thesis id and student matrix no;
	$refVivaNew = "R".runnum2('reference_no','pg_evaluation_viva');//reference for main
	$pgVivaDetailNew = "D".runnum2('id','pg_evaluation_viva_detail');//viva detail hold for every supervisor that submit report regarding thesis on main
																	//section dbce comment
	//////Section A////////
	$overallStyle=$_REQUEST['overallStyle'];	
	$addRating = $_REQUEST['addRating'];
	$comment = $_REQUEST['comment'];
	/////end A////////////
	
	/// Single Comment : Save in pg_evaluation_detail////
	$commentB = $_REQUEST['commentB'];
	$commentC = $_REQUEST['commentC'];
	$commentE = $_REQUEST['commentE'];
	$commentA = $_REQUEST['commentA'];
	//////end single comment///////
	
	//////Section D: recommendation: save in pg_evaluation_detail//
	$recCheck = $_REQUEST['recCheck'];
	/////end D////////////
	
	////section E//
	$sectionEid = $_REQUEST['sectionEid'];
	$addoverall = $_REQUEST['addoverall'];
	////end///////
	
	$curdatetime = date("Y-m-d H:i:s");
	$curdatetime1 = date("Y-m-d H:i:s");

	$sqlviva = "SELECT id FROM pg_evaluation_viva 
	WHERE pg_thesis_id = '$thesisId'
	AND student_matrix_no = '$matrixNo'";
	
	$result_sqlviva = $dbf->query($sqlviva); 
	$dbf->next_record();
	
	$vivaIdExist = $dbf->f('id');
	if(empty($vivaIdExist))
	{
		$sqlviva = "INSERT INTO pg_evaluation_viva
		(id, pg_thesis_id, student_matrix_no, reference_no, status, insert_by, insert_date)
		VALUES
		('$pgVivaIdNew', '$thesisId' , '$matrixNo' , '$refVivaNew' , 'A', '$user_id', '$curdatetime')";
		
		$dba->query($sqlviva);
		
		$sqlviva = "INSERT INTO pg_evaluation_viva_detail 
		(id, pg_eva_viva_id, major_revision, other_comment, status, insert_by, insert_date, pg_empid_viva, recommendation_id, comment_sec_e, report_status, comment_sec_a, submit_date)
		VALUES
		('$pgVivaDetailNew', '$pgVivaIdNew', '$commentB' , '$commentC' , 'A', '$user_id', '$curdatetime', '$user_id', '$recCheck', '$commentE', 'SUB', '$commentA', '$curdatetime')";
		
		$dba->query($sqlviva);
			
		$k = 1;
		while (list ($key,$val) = @each ($overallStyle)) 
		{
			$pgVivaStyleNew = "S".runnum2('id','pg_evaluation_viva_style');// content of section A
			
			$sqlviva = "INSERT INTO pg_evaluation_viva_style 
			(id, pg_eva_viva_detail_id, ref_overall_style_id, ref_overall_rating_id, insert_by, insert_date, status,seq, comments)
			VALUES
			('$pgVivaStyleNew', '$pgVivaDetailNew', '$overallStyle[$key]' , '$addRating[$key]' , '$user_id', '$curdatetime', 'A', '$k', '$comment[$key]')";
			
			$dba->query($sqlviva);
			$k++;
		}
		while (list ($key,$val) = @each ($sectionEid)) 
		{
			/*$pgVivaStyleNew = "S".runnum2('id','pg_evaluation_viva_style');// content of section A
			echo $sectionEid[$key].": ".$addoverall[$key]." Comment:".$comment[$key];
			echo "<br>";*/
			$pgVivaOverallNew = "O".runnum2('id','pg_evaluation_viva_overall'); // content of section E
			
			$sqloverall = "INSERT INTO pg_evaluation_viva_overall 
			(id, pg_eva_viva_detail_id, status, insert_by, insert_date, ref_overall_comment_id, ref_overall_rating_id)
			VALUES
			('$pgVivaOverallNew', '$pgVivaDetailNew', 'A', '$user_id', '$curdatetime', '$sectionEid[$key]', '$addoverall[$key]')";
			
			$dba->query($sqloverall);
			$k++;
		}
	}
	else{

		$sqlviva = "INSERT INTO pg_evaluation_viva_detail 
		(id, pg_eva_viva_id, major_revision, other_comment, status, insert_by, insert_date, pg_empid_viva, recommendation_id, comment_sec_e, report_status, comment_sec_a, submit_date)
		VALUES
		('$pgVivaDetailNew', '$vivaIdExist', '$commentB' , '$commentC' , 'A', '$user_id', '$curdatetime', '$user_id', '$recCheck', '$commentE', 'SUB', '$$commentA', '$curdatetime')";
		
		$dba->query($sqlviva);
			
		$k = 1;
		while (list ($key,$val) = @each ($overallStyle)) 
		{
			$pgVivaStyleNew = "S".runnum2('id','pg_evaluation_viva_style');// content of section A
			
			$sqlviva = "INSERT INTO pg_evaluation_viva_style 
			(id, pg_eva_viva_detail_id, ref_overall_style_id, ref_overall_rating_id, insert_by, insert_date, status,seq, comments)
			VALUES
			('$pgVivaStyleNew', '$pgVivaDetailNew', '$overallStyle[$key]' , '$addRating[$key]' , '$user_id', '$curdatetime', 'A', '$k', '$comment[$key]')";
			
			$dba->query($sqlviva);
			$k++;
		}
		while (list ($key,$val) = @each ($sectionEid)) 
		{

			$pgVivaOverallNew = "O".runnum2('id','pg_evaluation_viva_overall'); // content of section E
			
			$sqloverall = "INSERT INTO pg_evaluation_viva_overall 
			(id, pg_eva_viva_detail_id, status, insert_by, insert_date, ref_overall_commment_id, ref_overall_rating_id)
			VALUES
			('$pgVivaOverallNew', '$pgVivaDetailNew', 'A', '$user_id', '$curdatetime', '$sectionEid[$key]', '$addoverall[$key]')";
			
			$dba->query($sqloverall);
			$k++;
		}	
	
	}
	$sqlviva = "SELECT b.id AS pg_eva_viva_detail_id, a.id AS pg_eva_viva_id
	FROM pg_evaluation_viva a
	LEFT JOIN pg_evaluation_viva_detail b ON (b.pg_eva_viva_id=a.id)
	WHERE a.student_matrix_no = '$matrixNo'
	AND a.status = 'A'
	AND b.pg_empid_viva = '$user_id'
	AND a.pg_thesis_id = '$thesisId'";
	
	$result_sqlviva = $dbf->query($sqlviva); 
	$dbf->next_record();
	
	$pg_eva_viva_detail_id = $dbf->f('pg_eva_viva_detail_id');
	$pg_eva_viva_id = $dbf->f('pg_eva_viva_id');
	$submit = "1";
	echo "<script>window.location = 'view_viva_report.php?tid=".$thesisId."&rid".$referenceNo."&mn=".$matrixNo."&role=".$rolestatus."&submit=".$submit."&pd=".$pg_eva_viva_detail_id."';</script>";

}

$sql_supervisor = " SELECT a.pg_employee_empid, a.ref_supervisor_type_id, d.description as supervisor_type, 
DATE_FORMAT(a.acceptance_date,'%d-%b-%Y') as acceptance_date, h.description as role_status_desc
FROM pg_supervisor a 
LEFT JOIN ref_supervisor_type d ON (d.id = a.ref_supervisor_type_id) 
LEFT JOIN pg_thesis f ON (f.student_matrix_no = a.pg_student_matrix_no)
LEFT JOIN pg_proposal g ON (g.pg_thesis_id = f.id)
LEFT JOIN ref_role_status h ON (h.id = a.role_status)
WHERE a.pg_student_matrix_no='$matrixNo'
AND g.pg_thesis_id = '$thesisId'
AND a.acceptance_status = 'ACC'
AND a.ref_supervisor_type_id in ('SV','CS','XS')
AND g.verified_status in ('APP','AWC')
AND g.status in ('APP','APC')
AND g.archived_status IS NULL
AND a.status = 'A'
ORDER BY d.seq, a.ref_supervisor_type_id";

$result_sql_supervisor = $db_klas2->query($sql_supervisor); //echo $sql;
$db_klas2->next_record();
$row_cnt_supervisor = mysql_num_rows($result_sql_supervisor);


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


?>

	

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?
if(isset($redirect)) {
?>
		<META http-equiv="refresh" content="4;URL=review_amendment.php"> 
<? } ?>

		<title>Untitled Document</title>
		<link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
		<link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
		<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
		
		<script src="../../../lib/js/jquery.min2.js"></script>
		<script src="../../../lib/js/jquery.colorbox.js"></script>
		<script src="../../lib/js/jquery.mask_input-1.3.js"></script>
		<script src="../../../lib/js/jquery.min2.js"></script>
   		<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
    	<script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
		<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>	
		<script language="JavaScript" src="../../../lib/js/windowopen.js"></script>	
		<script language="JavaScript" type="text/javascript" src="../../../lib/js/tooltip.js"></script>	
	</head>
	<body>
	<script type="text/javascript">
	function deleteReport() 
	{
		var ask = window.confirm("Are you sure to delete this amendment? \nClick OK to proceed or CANCEL to stay on the same page.");
		if (ask) 
		{
			return true;
		}
		return false;
	}
	</script>

	<script type="text/javascript">
	function submitReport() 
	{
		var ask = window.confirm("Are you sure to submit Amendment On Thesis? \nClick OK to proceed or CANCEL to stay on the same page.");
		if (ask) 
		{
			//document.location.href = "../monthlyreport/new_defense.php?pid=" + pid + "&tid=" + tid + "&id=" + id;
			return true;
		}
		return false;
	}

	</script>
	<SCRIPT LANGUAGE="JavaScript">
	function respConfirm () {
		var confirmSubmit = confirm("Make sure any changes done is saved first. \nClick OK if confirm to submit or CANCEL to stay on the same page.");
		if (confirmSubmit==true)
		{
			return saveStatus;
		}
		if (confirmSubmit==false)
		{
			return false;
		}
	}
	</SCRIPT>	
	<?php
    if(!empty($msg)) 
	{
        foreach($msg as $err) 
		{
            echo $err;
        }
    }
	?>
	
	<form id="form1" name="form1" method="post" enctype="multipart/form-data">	
			<table border="0"> 
				<tr>
				<td><h3><strong>Supervisor Report </strong><h3></td>
				</tr>
			</table>
			<table width="52%">
				<!--<tr>
					<td width="20%">Report Status</td>
					<td width="2%">:</td>
					<?if ($defenseDesc=="") $defenseDesc='New';?>
					<td width="30%"><strong><?=$defenseDesc?></strong></td>
				</tr>-->
				<tr>
					<td>Reference No</td>
					<td>:</td>
					<td><?=$referenceNo?></td>
				</tr>
				<tr>
					<td>Thesis / Project ID</td>
					<td>:</td>
					<td><label><?=$thesisId;?></label></td>
					<input type="hidden" name="id" id="id" value="<?=$id; ?>">
					<input type="hidden" name="thesisId" id="thesisId" value="<?=$thesisId; ?>">
					<input type="hidden" name="proposalId" id="proposalId" value="<?=$proposalId; ?>">
				</tr>
				<tr>
					<td>Student Matrix No</td>
					<td>:</td>
					<td><?=$matrixNo?><input type="hidden" name="matrixNo" id="matrixNo" value="<?=$matrixNo?>" /></td>
				</tr>
					<?
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

					?>
				<tr>
					<td>Student Name</td>
					<td>:</td>
					<td><?=$sname?></td>
				</tr> 
				<?$jscript3 = "";?>
				<tr>
					<td>Evaluation Schedule</td>
					<td>:</td>				
					<?
		
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

					?>
					<td><?=$vivaDate?>, <?=$vivaSTime?> to <?=$vivaETime?>, <?=$venue?></td>				
				</tr>   			
			</table>
			<br />
			<legend><strong>List of Supervisor/Co-Supervisor</strong></legend>
			<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="80%" class="thetable">			
					<tr>
						<th width="5%">No</th>					
						<th width="15%">Role / Acceptance Date</th>
						<th width="15%" align="left">Staff ID</th>
						<th width="25%" align="left">Name</th>
						<th width="5%" align="left">Faculty</th>
						<th width="5%">View Feedback</th>
						<th width="15%" align="left">Status</th>
						<th width="15%">Last Update</th>
					</tr>
					<input type="hidden" name="rowCntSupervisor" id="rowCntSupervisor" value="<?=$row_cnt_supervisor;?>"><?

					$varRecCount=0;	
					if ($row_cnt_supervisor>0) {
						do {
							$employeeId = $db_klas2->f('pg_employee_empid');
							$supervisorType = $db_klas2->f('supervisor_type');
							$supervisorTypeId = $db_klas2->f('ref_supervisor_type_id');
							$acceptanceDate = $db_klas2->f('acceptance_date');
							$roleStatusDesc = $db_klas2->f('role_status_desc');
							$confirmAcceptanceDate = $db_klas2->f('acceptance_date');

							$sql_employee="SELECT  b.name, c.id, c.description
							FROM new_employee b 
							LEFT JOIN dept_unit c ON (c.id = b.unit_id) 
							WHERE b.empid= '$employeeId'";
								
							$result_sql_employee = $dbc->query($sql_employee);
							$dbc->next_record();
							
							$employeeName = $dbc->f('name');
							$departmentId = $dbc->f('id');
							$departmentName = $dbc->f('description');

							$varRecCount++;

							?>
							<input type="hidden" name="supervisorIdArray[]" id="supervisorIdArray" value="<?=$employeeId; ?>">
							<tr>
								<td align="center"><?=$varRecCount;?>.</td>	
								<td align="left">
								<?if ($supervisorTypeId != 'XS') {?>
									<?=$supervisorType;?><br/><?=$roleStatusDesc?>
								<?}
								else {
									?>
									<span style="color:#FF0000"><?=$supervisorType;?></span><br/><?=$roleStatusDesc?>
									<?
								}?>
								<br/><?=$acceptanceDate?></td>
								<input type="hidden" name="confirmAcceptanceDate" id="confirmAcceptanceDate" value="<?=$confirmAcceptanceDate; ?>">
								<td align="left"><?=$employeeId;?></td>
								<td align="left"><?=$employeeName;?></td>
								<td align="left"><a href="javascript:void(0);" onMouseOver="toolTip('<?=$departmentName;?>', 300)" onMouseOut="toolTip()"><?=$departmentId;?></a></td>
								
								<?
								/*$sql12 = "SELECT b.status as defense_detail_status, c.description as defense_detail_desc,
								DATE_FORMAT(b.responded_date,'%d-%b-%Y %h:%i %p') AS responded_date
								FROM pg_defense a
								LEFT JOIN pg_defense_detail b ON (b.pg_defense_id = a.id)
								LEFT JOIN ref_proposal_status c ON (c.id = b.status)
								WHERE a.student_matrix_no = '$user_id'
								AND a.reference_no = '$referenceNo'
								AND b.pg_employee_empid = '$employeeId'
								AND a.pg_thesis_id = '$thesisId'
								AND a.pg_proposal_id = '$proposalId'
								/*AND a.status NOT IN ('SAV')
								AND a.archived_status is null
								AND b.archived_status is NULL";
								
								$result12 = $dbg->query($sql12); 
								$dbg->next_record();
								$row_cnt12 = mysql_num_rows($result12);
								$defenseDetailStatus=$dbg->f('defense_detail_status');
								$defenseDetailDesc=$dbg->f('defense_detail_desc');
								$respondedDate=$dbg->f('responded_date');
								*/
								if ($row_cnt12>0) {
								?>
								
								<td><a href="../defense/defense_view_feedback.php?tid=<? echo $thesisId;?>&amp;pid=<? echo $proposalId;?>&amp;eid=<? echo $employeeId;?>&amp;id=<? echo $id;?>&amp;mn=<?=$user_id?>" name="thesisId" value="<?=$thesisId?>" title="View feedback"><img src="../images/view.jpg" width="45" height="30" style="border:0px;" title="View feedback"></a></td>	
								<?}
								else {
									?>
									<td></td>
									<?
								}
								if ($defenseDetailStatus == '') {
								?>
									<td align="left"><label>Expecting Defense Proposal</label></td>
								<?}
								else if ($defenseDetailStatus == 'SV1') {
								?>
									<td align="left"><label><span style="color:#FF0000"><?=$defenseDetailDesc;?></span></label></td>
								<?}
								else {
									?>								
									<td align="left"><label><?=$defenseDetailDesc;?></label></td>
									<?
								}?>
								<td align="left"><label><?=$respondedDate;?></label></td>
							</tr>
									
					<? 	} while($db_klas2->next_record());
						
					}
					else {
						?>
						<table>				
							<tr><td>Notes: <br/>No Supervisor/Co-Supervisor in the list.
										Possible Reasons:-<br/>
										1. Supervisor/Co-Supervisor is yet to be assigned<br/>
										2. Pending approval by the Senate.<br/>
										3. If already assigned, it could be the Supervisor/Co-Supervisor pending to accept<br/><br/>
										<!--<span style="color:#FF0000"> Make sure your Supervisor has been assigned first before submit the Monthly Defense report</span>--></td>
							</tr>
						</table>
						<?
					}?>	
	  </table>
			<br/>
<? 

	
	$sqlamend1 = "SELECT id FROM pg_amendment
	WHERE pg_thesis_id = '$thesisId'
	AND pg_proposal_id = '$proposalId'
	AND reference_no = '$referenceNo'
	AND confirm_status IS NULL
	AND STATUS = 'A'
	AND confirm_by IS NULL
	AND student_matrix_no = '$matrixNo'
	AND amendment_status = 'SUB'
	AND ref_req_no IS NULL";
		
	$result_sqlamend1 = $dbf->query($sqlamend1); 
	$dbf->next_record();
	$amendmentId = $dbf->f('id');


?>
<? // Call list of amendment
	
	$sqlamend = "SELECT a.id,a.amendment_by_examiner AS amendment, a.feedback_by_examiner, a.amendment_detail_status
	FROM pg_amendment_detail a
	WHERE a.pg_amendment_id = '$amendmentId'
	AND a.pg_thesis_id = '$thesisId'
	AND a.student_matrix_no = '$matrixNo'
	AND a.amendment_detail_status = 'SUB' 
	AND a.confirm_by IS NULL
	AND a.confirm_date IS NULL
	AND a.status = 'A'";
	
	$result_sqlamend = $dbb->query($sqlamend); 
	$dbb->next_record();
	$row_cnt5 = mysql_num_rows($result_sqlamend);
	$i= 0;
	$inc= 0;
	$amendmentArray = array();
	$feedbackByExaminerArray = array();
	$idArray = array();
	$amendment_detail_statusArray = array();
	$commentArray = array();
	$commentIdArray =array();
	$confirmStatusArray =array();
	
	do{
		$id = $dbb->f('id');
		$amendment = $dbb->f('amendment');
		$feedbackByExaminer = $dbb->f('feedback_by_examiner');
		$amendment_detail_status = $dbb->f('amendment_detail_status');
		$commentArray[$i]=$dbb->f('comment');
		$commentIdArray[$i]=$dbb->f('commentId');
		$confirmStatusArray[$i]=$dbb->f('confirm_status');
		
		$idArray[$i] = $id;
		$amendmentArray[$i] =$amendment;
		$feedbackByExaminerArray[$i] =$feedbackByExaminer;
		$amendment_detail_statusArray[$i] =$amendment_detail_status;
		$inc++;
		$i++;

	}while($dbb->next_record());
	
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



	
	/////////end reference/////////////////
?>


	<fieldset><legend><strong>SECTION A: OVERALL STYLE AND ORGANIZATION</strong></legend>
		<table class="thetable" width="90%" border="1">
			<tr>
				<th width="3%">No</td>
				<th width="47%" align="left">Description</td>
				<th width="10%">Rate</td>
			  <th width="40%" align="left">Comments</td>		  
			</tr>
		<? for ($iq=0; $iq<$inq; $iq++){ ?>
		<tr>
		
			<td width="3%"><?=$iq+1?><input type="hidden" name="overallStyle[]" id="overallStyle" value="<?=$idArray[$iq]?>" /></td>
			<td width="47%"><?=$descArray[$iq]?></td>
			<? 
			$sqlrate = "SELECT * FROM ref_overall_viva_rating where ques_seq = '$seqArray[$iq]' ORDER BY seq ASC ";
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
				$idRateArray[$ir] = $db1->f('id');
				$descRateArray[$ir] = $db1->f('description');
				$rateArray[$ir] = $db1->f('rate');
				$ir++;
				$inr++;
		
			}while($db1->next_record());
		
		?>
			<td><select name="addRating[]" id="addRating[]">
			<? for ($ir=0; $ir<$inr; $ir++){ ?>
				<? if(!empty($descRateArray[$ir])) { ?>
				<option value="<?=$idRateArray[$ir]?>"><?=$rateArray[$ir]?>-<?=$descRateArray[$ir]?></option>
				<? } else { ?>
				<option value="<?=$idRateArray[$ir]?>"><?=$rateArray[$ir]?></option>
				<? } ?>
			<? }?>	
				</select></td>
			<td><textarea cols="40" name="comment[]" id="comment[]"></textarea></td>	
		</tr>

		<? }?>
		<tr>
			<td colspan="4"><span style="font-style:italic;"><strong>Other Comment</strong></span></td>
		</tr>
		<tr>
			<td colspan="4"><textarea cols="40" name="commentA" id="commentA"></textarea></td>
		</tr>
	</table>

	</fieldset>
	<fieldset><legend><strong>SECTION B: MAJOR REVISIONS REQUIRED <span style="font-style:italic;">(if any)</span></strong></legend>
	<table>
		<tr>
			<td><span style="font-style:italic;">Please use additional sheet if required</span></td>
		</tr>
		<tr>
			<td><textarea cols="70" rows="4" name="commentB" id="commentB"></textarea></td>
		</tr>
	</table>
	
	
	</fieldset>
	<fieldset><legend><strong>SECTION C: OTHER COMMENTS</strong></legend>
	<table>
		<tr>
			<td><span style="font-style:italic;">For example: Suitability for publication and award, if any. Please use additional sheet if required</span></td>
		</tr>
		<tr>
			<td><textarea cols="70" rows="4" name="commentC" id="commentC"></textarea></td>
		</tr>
	</table>
	
	
	</fieldset>


<? 
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




?>
	
	<fieldset><legend><strong>SECTION D: Recommendation</strong></legend>
	<table class="thetable" border="1">
		<tr>
			<th>No</td>
			<th align="left">Components</td>
			<th>Recommendation</td>
		</tr>
		<? for ($no=0; $no<$noU; $no++){ ?>
		<tr>
			
			<td><?=$no+1?></td>
			<td><?=$descRecArray[$no]?></td>
			<td align="center"><input type="radio" name="recCheck" id="recCheck" value="<?=$idRecArray[$no]?>" /></td>
			
		</tr>
		<? }?>
	</table>
	
	
	</fieldset>


<? 
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


?>

	<fieldset><legend><strong>SECTION E: OVERALL COMMENT ON STUDENT</strong></legend>
	<table>
		<tr>
			<td colspan="3"><strong>As a Examiner, how do you asses your student on the following grounds. Please rate how strongly you agree or disagree with the statement about the candidate on the following ground</strong></td>
		</tr>
	</table>
	<table class="thetable" border="1" width="79%">
		
		<tr>
			<th width="2%" align="center">No</td>
			<th width="40%" align="left">Components</td>
			<th width="30%" align="left">Ratings</td>		
		</tr>
		<? for ($no1=0; $no1<$noC; $no1++){ ?>
		<tr>
			<td><?=$no1+1?><input type="hidden" name="sectionEid[]" id="sectionEid" value="<?=$idOrArray[$no1]?>" /></td>
			<td><?=$descComArray[$no1]?></td>
			
			<td><select name="addoverall[]" id="addoverall">
			<? for ($no2=0; $no2<$noOR; $no2++){ ?>
				<option value="<?=$idOrArray[$no2]?>"><?=$rateOrArray[$no2]?>-<?=$descOrArray[$no2]?></option>
			<? } ?>
				</select>
			</td>
			
		</tr>
		<? } ?>
		
	</table>
	<table>
		<tr>
			<td colspan="3"><strong>Other Comments</strong></td>
		</tr>
		<tr>
			<td colspan="3"><textarea cols="70" rows="5" name="commentE" id="commentE"></textarea></td>
		</tr>
	</table>
	</fieldset>
	<table>
		<tr>
			<td colspan="2"><label><span style="color:#FF0000">Notes:</span><br/>
					1. Submit button is for submit amendments on thesis to supervisor for review.</label></td>
		</tr>
		<tr>
		  <td colspan="2">
		  <input type="button" name="btnBack" id="btnBack" value="Back" onClick="javascript:document.location.href='viva_evaluation.php';"/>
		  <input type="submit" name="btnSave" id="btnSave" value="Save as Draft" onclick = ""/>
		  <input type="submit" name="btnSubmit" id="btnSubmit" value="Submit" onclick = "return submitReport()"/></td>
		</tr>
	</table>	
	</form>
	<script>
		<?=$jscript1;?>
		<?=$jscript2;?>
		<?=$jscript3;?>
	</script>
</body>
</html>




