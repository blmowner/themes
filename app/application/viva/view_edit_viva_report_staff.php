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
$submit=$_GET['submit'];

$thesisId = $_GET['tid']; 
$matrixNo=$_GET['mn'];
$rolestatus=$_GET['role'];
$pd=$_GET['pd'];
$roleType=$_GET['type'];
$empid=$_GET['empid'];
$other=$_GET['other'];
$othertype=$_GET['othertype'];

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
WHERE b.id = '$pd'
AND a.pg_thesis_id = '$thesisId'
AND a.student_matrix_no = '$matrixNo'
AND a.status = 'A'
AND b.status = 'A'";

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

////////////////////Section A//////////////////////////
$sqlA = "SELECT a.id as vivaStyleId, a.ref_overall_style_id, a.ref_overall_rating_id, a.comments, 
b.description AS questionA, c.description AS answerA, c.rate
FROM pg_evaluation_viva_style a
LEFT JOIN ref_overall_style_viva b ON (b.id=a.ref_overall_style_id)
LEFT JOIN ref_overall_viva_rating c ON (c.id=a.ref_overall_rating_id)
WHERE a.pg_eva_viva_detail_id = '$evaVivaDetailId'
AND a.status = 'A'
ORDER BY b.seq ASC, c.seq";
$dbt = $dbu;
$result_sqlA = $dbt->query($sqlA); 
$dbt->next_record();
$row_cnt5 = mysql_num_rows($result_sqlA);
$noA= 0;
$incA= 0;

$vivaStyleId = array();
$ref_overall_style_id = array();
$ref_overall_rating_idA = array();
$comments = array();
$answerA = array();
$questionA = array();
$rateA = array();

do{
	$vivaStyleId[$noA] = $dbt->f('vivaStyleId');
	$ref_overall_style_id[$noA] = $dbt->f('ref_overall_style_id');
	$ref_overall_rating_idA[$noA] = $dbt->f('ref_overall_rating_id');
	$comments[$noA] = $dbt->f('comments');
	$questionA[$noA] = $dbt->f('questionA');
	$answerA[$noA] = $dbt->f('answerA');
	$rateA[$noA] = $dbt->f('rate');
	
	$noA++;
	$incA++;

}while($dbt->next_record());


////////////////////Section E//////////////////////////
$sqlA = "SELECT a.id AS vivaOverallId, a.ref_overall_comment_id, a.ref_overall_rating_id, 
b.description AS commentDesc, c.description AS ratingDesc, c.rate as rateE
FROM pg_evaluation_viva_overall a
LEFT JOIN ref_overall_comments b ON (b.id=a.ref_overall_comment_id)
LEFT JOIN ref_overall_rating c ON (c.id=a.ref_overall_rating_id)
WHERE a.pg_eva_viva_detail_id = '$evaVivaDetailId'
AND a.status = 'A'
ORDER BY a.id ASC";
$dbh = $dbu;
$result_sqlA = $dbh->query($sqlA); 
$dbh->next_record();
$row_cnt5 = mysql_num_rows($result_sqlA);
$noE= 0;
$incE= 0;

$vivaOverallId = array();
$ref_overall_comment_id = array();
$ref_overall_rating_id = array();
$commentDesc = array();
$ratingDesc = array();
$rateE = array();

do{
	$vivaOverallId[$noE] = $dbh->f('vivaOverallId');
	$ref_overall_comment_id[$noE] = $dbh->f('ref_overall_comment_id');
	$ref_overall_rating_id[$noE] = $dbh->f('ref_overall_rating_id');
	$commentDesc[$noE] = $dbh->f('commentDesc');
	$ratingDesc[$noE] = $dbh->f('ratingDesc');
	$rateE[$noE] = $dbh->f('rateE');
	
	$noE++;
	$incE++;

}while($dbh->next_record());

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


$sql5 = "SELECT description from ref_supervisor_type where id = '$roleType'";
$dbg5 = $dbg;
$result_sql5 = $dbg5->query($sql5); 
$dbg5->next_record();
$row_cnt4 = mysql_num_rows($result_sql5);
$header11 = $dbg5->f('description');

$sql6 = "SELECT name from new_employee where empid = '$empid'";
$dbc5 = $dbc;
$result_sql6 = $dbc5->query($sql6); 
$dbc5->next_record();
$row_cnt6 = mysql_num_rows($result_sql6);
$empName = $dbc5->f('name');
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
				<td><h3><strong><?=$header11?> Report </strong><h3></td>
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
					<td>Name/Staff ID </td>
					<td>:</td>
					<td><?=$empName?>(<?=$empid?>)</td>
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
	<input type="hidden" id="evaVivaId" name="evaVivaId" value="<?=$evaVivaId?>" />
		<table class="thetable" width="95%" border="1">
			<tr>
				<th width="3%">No</td>
				<th width="45%" align="left">Description</td>
				<th width="15%">Rate</td>
			  <th width="37%" align="left">Comments</td>			
		  </tr>
		<? for ($iq=0; $iq<$inq; $iq++){ ?>
		<tr>
			<td width="3%" height="40" align="center"><?=$iq+1?><input type="hidden" name="overallStyle[]" id="overallStyle" value="<?=$idArray[$iq]?>" />
			<input type="hidden" name="vivaStyleId[]" id="vivaStyleId" value="<?=$vivaStyleId[$iq]?>" /></td>
			<td width="45%"><?=$descArray[$iq]?></td>
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

			<td>
			<? 
			if(!empty($answerA[$iq])) 
			{
				echo "$rateA[$iq]-$answerA[$iq]";
			}
			else {
				echo "$rateA[$iq]";
			}
			?>
			</td>
			
			<td><?=$comments[$iq]?></td>	
			
		</tr>

		<? }?>
		<tr>
			<td colspan="4"><span style="font-style:italic;"><strong>Other Comment</strong></span></td>
		</tr>
		<tr>
			<td colspan="4" height="80"><?=$commentSecA?></td>
		</tr>
	</table>

	</fieldset>
	<fieldset><legend><strong>SECTION B: MAJOR REVISIONS REQUIRED <span style="font-style:italic;">(if any)</span></strong></legend>
	<table>
		<tr>
			<td height="80" width="618"><?=$major_revision?></td>
		</tr>
	</table>
	
	
	</fieldset>
	<fieldset><legend><strong>SECTION C: OTHER COMMENTS</strong></legend>
	<table>
		<tr>
			<td height="80" width="618"><?=$other_comment?></td>
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
			
			<td align="center"><?=$no+1?></td>
			<td><?=$descRecArray[$no]?></td>
			<td align="center">
			<? if($recommendation_id == $idRecArray[$no]) {?>
			<input disabled="disabled" type="radio" name="recCheck" id="recCheck" checked="checked" value="<?=$idRecArray[$no]?>" />
			<? } else { ?>
			<input disabled="disabled" type="radio" name="recCheck" id="recCheck" value="<?=$idRecArray[$no]?>" />
			<? } ?>
			</td>
			
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


$vivaOverallId[$noE] = $dbh->f('vivaOverallId');
$ref_overall_comment_id[$noE] = $dbh->f('ref_overall_comment_id');
$ref_overall_rating_id[$noE] = $dbh->f('ref_overall_rating_id');
$commentDesc[$noE] = $dbh->f('commentDesc');
$ratingDesc[$noE] = $dbh->f('ratingDesc');
$rateE[$noE] = $dbh->f('rateE');

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
			<td align="center"><?=$no1+1?><input type="hidden" name="sectionEid[]" id="sectionEid" value="<?=$idOrArray[$no1]?>" />
			<input type="hidden" name="vivaOverallId[]" id="vivaOverallId" value="<?=$vivaOverallId[$no1]?>" /></td>
			<td><?=$descComArray[$no1]?></td>
			
			<td><select name="addoverall[]" id="addoverall" disabled="disabled">
			<? for ($no2=0; $no2<$noOR; $no2++){ ?>
				<? if($ref_overall_rating_id[$no1] == $idOrArray[$no2]) { ?>
				<option selected="selected" value="<?=$idOrArray[$no2]?>"><?=$rateOrArray[$no2]?>-<?=$descOrArray[$no2]?></option>
				<? } else { ?>
				<option value="<?=$idOrArray[$no2]?>"><?=$rateOrArray[$no2]?>-<?=$descOrArray[$no2]?></option>
				<? } ?>
			<? } ?>
				</select></td>
			
		</tr>
		<? } ?>
		
	</table>
	<table>
		<tr>
			<td width="455" colspan="3"><strong>Other Comments</strong></td>
		</tr>
		<tr>
			<td colspan="3" height="80"><?=$commentSecE?></td>
		</tr>
	</table>
	</fieldset>
	<table>

		<tr>
		  <td colspan="2">	  
		  
		  <input type="button" name="btnBack" id="btnBack" value="Back" onClick="javascript:document.location.href='edit_viva_report.php?tid=<?=$thesisId?>&mn=<?=$matrixNo?>&role=<?=$rolestatus?>&pd=<?=$other?>&type=<?=$othertype?>';"/>
		  
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




