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


/*ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);*/

$user_id=$_SESSION['user_id'];

$thesisId = $_GET['tid']; 
$matrixNo=$_GET['mn'];
$rolestatus=$_GET['vid'];
$respond_by = $_GET['rb'];


$msg = array();

if(isset($submit) || $submit == '1')
{
	$msg[] = "<div class=\"success\"><span>VIVA report successfully submitted.</span></div>";
}


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

///////////////////////list of evaluation panel for viva///////////////////////
$sql_supervisor = " SELECT a.pg_employee_empid, a.ref_supervisor_type_id, d.description as supervisor_type,
DATE_FORMAT(a.acceptance_date,'%d-%b-%Y %h:%i%p') as acceptance_date, h.description as role_status_desc, a.role_status
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

$db_klas3 = $db_klas2;
$result_sql_supervisor = $db_klas3->query($sql_supervisor); //echo $sql;
$db_klas3->next_record();
$row_cnt_supervisor = mysql_num_rows($result_sql_supervisor);




/*$sql_supervisor = "SELECT a.pg_empid_viva, b.role_status, b.ref_supervisor_type_id, 
c.description AS roleDesc, a.report_status, DATE_FORMAT(a.submit_date,'%d-%b-%Y %h:%i:%s %p') as submit_date, a.id as otherDetailId
FROM pg_evaluation_viva_detail a
LEFT JOIN pg_supervisor b ON (b.pg_employee_empid = a.pg_empid_viva)
LEFT JOIN ref_supervisor_type c ON (c.id = b.ref_supervisor_type_id)
LEFT JOIN pg_evaluation_viva d ON (d.id = a.pg_eva_viva_id)
WHERE b.pg_thesis_id = '$thesisId'
AND a.pg_eva_viva_id = '$evaVivaId'
AND d.id = '$evaVivaId'
AND b.status = 'A'
AND b.ref_supervisor_type_id = 'SV' ";*/



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

$sql8 = "SELECT thesis_title
FROM pg_proposal
WHERE pg_thesis_id = '$thesisId'";
$dbg4 = $dbg;
$result_sql8 = $dbg4->query($sql8); 
$dbg4->next_record();
$row_cnt4 = mysql_num_rows($result_sql8);
$thesis_title = $dbg4->f('thesis_title');
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
			<table border="0" align="center"> 
				<tr>
				<td><h3><strong>PhD VIVA VOCE EVALUATION FORM</strong><h3></td>
				</tr>
			</table>
			<table class="thetable" width="80%" border="1" align="center">
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
				<tr>
					<td>Student Matrix No</td>
					<td>:</td>
					<td><?=$matrixNo?><input type="hidden" name="matrixNo" id="matrixNo" value="<?=$matrixNo?>" /></td>
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
					<td>Thesis Title</td>
					<td>:</td>
					<td><?=$thesis_title?></td>
				</tr>
				
					
				
				<?$jscript3 = "";?>
				<? do {
							$employeeId = $db_klas3->f('pg_employee_empid');
							$role_status = $db_klas3->f('role_status');
							$ref_supervisor_type_id = $db_klas3->f('ref_supervisor_type_id');
							$roleDesc = $db_klas3->f('supervisor_type');
							$submit_date = $db_klas3->f('submit_date');
							$report_status = $db_klas3->f('report_status');
							$otherDetailId = $db_klas3->f('otherDetailId');

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
				<tr>				
					<td><?=$roleDesc?>
					<?
						if($role_status == 'PRI')
						{
							echo "- Main";
						}
					
					?></td>
					<td>:</td>
					<td><?=$employeeName?>(<?=$employeeId?>)</td>
				</tr> 
				<? 	} while($db_klas3->next_record()); ?>	
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
			<br/>
<? // Call list of amendment
	
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
	<table width="80%" align="center">
		<tr>
			<td align="left">Recommendation by Board of Examiner:</td>
		</tr>
	</table>
	<table align="center">
		<tr>
			<!--<th>No</td>
			<th align="left">Components</td>
			<th>Recommendation</td>-->
		</tr>
		<? for ($no=0; $no<$noU; $no++){ ?>
		<tr>
			
			<td><? //$no+1?></td>
			
			<td align="center">
			<? if($recommendation_id == $idRecArray[$no]) {?>
			<!--<input disabled="disabled" type="radio" name="recCheck" id="recCheck" checked="checked" value="<?=$idRecArray[$no]?>" />
			<input type="checkbox" readonly="readonly" checked="checked" disabled="disabled"/>-->
			<img src="../images/checked.png" width="18" height="18" style="border:0px;" title="View feedback">
			<? } else { ?>
			<!--<input disabled="disabled" type="radio" name="recCheck" id="recCheck" value="<?=$idRecArray[$no]?>" />
			<input type="checkbox" readonly="readonly" disabled="disabled"/>-->
			<img src="../images/unchecked.png" width="18" height="18" style="border:0px;" title="View feedback">
			<? } ?>
			</td>
			<td><?=$descRecArray[$no]?></td>
			
		</tr>
		<? }?>
	</table>
	<table width="80%" align="center">
		<tr>
			<td align="left">Note: For any other additional comments, please enclose as attachments.</td>
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




