<? session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title></title>
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
$proposalId=$_GET['pid'];
$matrixNo=$_GET['mn'];
$evaluationId=$_GET['ed'];

$save=$_GET['save'];
$pd=$_GET['pd'];
$msg = array();



if(isset($save) || $save == '1')
{
	$msg[] = "<div class=\"success\"><span>Academic Committee decision successfully submit.</span></div>";
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

$sqleva = " SELECT b.id AS evaVivaDetailId, b.major_revision, b.other_comment, b.recommendation_id, c.description AS recoDesc, a.school_remark,
b.comment_sec_e AS commentSecE, b.comment_sec_a as commentSecA, a.id AS evaVivaId, a.id as evaVivaId, b.report_status
FROM pg_evaluation_viva a
LEFT JOIN pg_evaluation_viva_detail b ON (b.pg_eva_viva_id = a.id)
LEFT JOIN ref_recommendation c ON (c.id = b.recommendation_id)
WHERE b.id = '$pd'
AND a.pg_thesis_id = '$thesisId'
AND a.student_matrix_no = '$matrixNo'
AND a.status = 'A'
AND b.status = 'A'";

$result_sqleva = $db_klas2->query($sqleva); //echo $sql;
$db_klas2->next_record();
$row_sqleva = mysql_num_rows($result_sqleva);

$evaVivaDetailId = $db_klas2->f('evaVivaDetailId');
$evaVivaId= $db_klas2->f('evaVivaId');
$major_revision = $db_klas2->f('major_revision');
$other_comment = $db_klas2->f('other_comment');
$recommendation_id = $db_klas2->f('recommendation_id');
$recoDesc = $db_klas2->f('recoDesc');
$commentSecE = $db_klas2->f('commentSecE');
$commentSecA = $db_klas2->f('commentSecA');
$evaVivaId = $db_klas2->f('evaVivaId');
$reportStatusSingle = $db_klas2->f('report_status');
$school_remark= $db_klas2->f('school_remark');



///////////////////////list of evaluation panel for viva///////////////////////
$sql_supervisor = " SELECT a.pg_empid_viva, b.role_status, b.ref_supervisor_type_id, e.description as recDesc,
c.description AS roleDesc, a.report_status, DATE_FORMAT(a.submit_date,'%d-%b-%Y %h:%i:%s %p') as submit_date, a.id as otherDetailId
FROM pg_evaluation_viva_detail a
LEFT JOIN pg_supervisor b ON (b.pg_employee_empid = a.pg_empid_viva)
LEFT JOIN ref_supervisor_type c ON (c.id = b.ref_supervisor_type_id)
LEFT JOIN pg_evaluation_viva d ON (d.id = a.pg_eva_viva_id)
LEFT JOIN ref_recommendation e ON (e.id = a.recommendation_id)
WHERE b.pg_thesis_id = '$thesisId'
AND a.pg_eva_viva_id = '$evaluationId'
AND d.id = '$evaluationId'
AND b.status = 'A'
AND a.pg_empid_viva <> '$user_id'
ORDER by b.ref_supervisor_type_id ASC";

$db_klas3 = $db_klas2;
$result_sql_supervisor = $db_klas3->query($sql_supervisor); //echo $sql;
$db_klas3->next_record();
$row_cnt_supervisor = mysql_num_rows($result_sql_supervisor);

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

$sql4 = "SELECT a.id, c.final_result
FROM pg_calendar a
LEFT JOIN pg_viva b ON (b.pg_calendar_id = a.id)
LEFT JOIN pg_evaluation_viva c ON (c.pg_viva_id = b.id) 
WHERE a.student_matrix_no = '$matrixNo'
AND a.thesis_id = '$thesisId'
AND a.ref_session_type_id = 'VIV'
AND a.recomm_status = 'REC'
AND a.status = 'A'
AND b.status IN ('A', 'ARC', 'ARC1')
AND c.id = '$evaluationId'
ORDER BY defense_date ASC";

$result_sql4 = $dbg->query($sql4); 
$dbg->next_record();
$row_cnt4 = mysql_num_rows($result_sql4);
$calendarIdViva = $dbg->f('id');
$final_result = $dbg->f('final_result');


$thesisId = $_GET['tid']; 
$proposalId=$_GET['pid'];
$matrixNo=$_GET['mn'];
$evaluationId=$_GET['ed'];
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
		var ask = window.confirm("Are you sure to submit School Board Result? \nClick OK to proceed or CANCEL to stay on the same page.");
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
				<td><h3><strong>VIVA School Board Committee</strong><h3></td>
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
					<td><?=$vivaDate?>, <?=$vivaSTime?> to <?=$vivaETime?>, <?=$venue?>
					<input type="hidden" name= "scheId" id= "scheId" value="<?=$calendarIdViva?>" /></td>				
				</tr>   			
			</table>
			<br />
			<legend><strong>List of Evaluation Panel on VIVA </strong></legend>
			<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="90%" class="thetable">
              <tr>
                <th width="5%">No</th>
                <th width="15%">Role / Acceptance Date</th>
                <th width="15%" align="left">Staff ID</th>
                <th width="25%" align="left">Name</th>
                <th width="5%" align="left">Faculty</th>
                <th width="15%" align="left">Status</th>
                <th width="15%">Last Update</th>
				<th>Recommendation Result</th>
				<th width="15%">Feedback</th>
              </tr>
              <input type="hidden" name="rowCntSupervisor" id="rowCntSupervisor" value="<?=$row_cnt_supervisor;?>" />
			  <?

					$varRecCount=0;	
					if ($row_cnt_supervisor>0) {
						do {
							$employeeId = $db_klas3->f('pg_empid_viva');
							$role_status = $db_klas3->f('role_status');
							$ref_supervisor_type_id = $db_klas3->f('ref_supervisor_type_id');
							$roleDesc = $db_klas3->f('roleDesc');
							$submit_date = $db_klas3->f('submit_date');
							$report_status = $db_klas3->f('report_status');
							$otherDetailId = $db_klas3->f('otherDetailId');
							$recDesc= $db_klas3->f('recDesc');

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
              <input type="hidden" name="supervisorIdArray[]" id="supervisorIdArray" value="<?=$employeeId; ?>" />
              <tr>
                <td align="center"><?=$varRecCount;?>
                  .</td>
                <td align="left"><?=$roleDesc?>
                    <? if($ref_supervisor_type_id == 'SV'){
										if($role_status == 'PRI')
										{
											echo "- Main";
										}
									} 
								?>
                </td>
                <input type="hidden" name="confirmAcceptanceDate" id="confirmAcceptanceDate" value="<?=$confirmAcceptanceDate; ?>" />
                <td align="left"><?=$employeeId;?></td>
                <td align="left"><?=$employeeName;?></td>
                <td align="left"><a href="javascript:void(0);" onmouseover="toolTip('<?=$departmentName;?>', 300)" onmouseout="toolTip()">
                  <?=$departmentId;?>
                </a></td>
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
							
								if ($report_status == '') {
								?>
                <td align="left"><label>Not Submitted Yet</label></td>
                <? }
								else if ($report_status == 'SUB') {
								?>
                <td align="left"><label><span style="color:#FF0000">Submitted</span></label></td>
                <? }?>
                <td align="left"><label>
                  <?=$submit_date;?>
                </label></td>
				<td><?=$recDesc?></td>
                <td align="left">
				<? if($report_status == 'SUB') {?>
                    <a href="view_school_eva_other.php?tid=<?=$thesisId?>&type=<?=$ref_supervisor_type_id?>&mn=<?=$matrixNo?>&role=<?=$role_status?>&pd=<?=$otherDetailId?>&empid=<?=$employeeId?>&other=<?=$evaluationId?>&othertype=<?=$roleType?>" name="thesisId" value="<?=$thesisId?>" title="View feedback"><img src="../images/view.jpg" width="45" height="30" style="border:0px;" title="View feedback" /></a>
                    <? } ?>
				</td>
              </tr>
              <? 	} while($db_klas2->next_record());
						
					}
					else {
						?>
              <table>
                <tr>
                  <td>Notes: <br/>
                    No Supervisor/Co-Supervisor in the list.
                    Possible Reasons:-<br/>
                    1. Supervisor/Co-Supervisor is yet to be assigned<br/>
                    2. Pending approval by the Senate.<br/>
                    3. If already assigned, it could be the Supervisor/Co-Supervisor pending to accept<br/>
                    <br/>
                    <!--<span style="color:#FF0000"> Make sure your Supervisor has been assigned first before submit the Monthly Defense report</span>--></td>
                </tr>
              </table>
			  <?
					}?>
            </table>
			<br/>
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
	
	<fieldset><legend><strong>Result by School Board Comittee</strong></legend>
	<table class="thetable" border="1">
		<tr>
			<th>No</td>
			<th align="left">Components</td>
			<th>Tick</td>
		</tr>
		<? for ($no=0; $no<$noU; $no++){ ?>
		<tr>
			
			<td align= "center"><?=$no+1?></td>
			<td><?=$descRecArray[$no]?></td>
			<td align="center">
			<? if($final_result == $idRecArray[$no]) {?>
			<input type="radio" name="recCheck" id="recCheck" checked="checked" disabled="disabled" value="<?=$idRecArray[$no]?>" />
			<? } else { ?>
			<input type="radio" name="recCheck" id="recCheck" disabled="disabled" value="<?=$idRecArray[$no]?>" />
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

$sqleva = " SELECT b.school_remark 
FROM pg_viva a
LEFT JOIN pg_evaluation_viva b ON (b.pg_viva_id = a.id)
WHERE b.id = '$evaluationId'";

$result_sqleva = $db_klas2->query($sqleva); //echo $sql;
$db_klas2->next_record();
$row_sqleva = mysql_num_rows($result_sqleva);

$school_remark = $db_klas2->f('school_remark');
?>

	<fieldset><legend><strong>Remarks</strong></legend>
	<table>
		<tr>
			<td><?=$school_remark?></td>
		</tr>
	</table>
	</fieldset>
	<table>
		<tr>
		  <td colspan="2">
		  <input type="button" name="btnBack" id="btnBack" value="Back" onClick="javascript:document.location.href='viva_school.php';"/>
		  <!--<input type="submit" name="btnSave" id="btnSave" value="Save as Draft" onclick = ""/>-->
		  </td>
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




