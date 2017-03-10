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
		<script language="JavaScript" src="../../../lib/js/windowopen.js"></script>

	</head> 
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
$matrixNo=$_GET['mn'];
$amendmentId=$_GET['ad'];
$save=$_GET['save'];
if($save == '1')
{
	$msg[] = "<div class=\"success\"><span>Amendment successfully submitted.</span></div>";
}


$sqlamend1 = "SELECT id, submit_date AS submitDate, reference_no as referenceNo FROM pg_amendment
WHERE pg_thesis_id = '$thesisId'
AND pg_proposal_id = '$proposalId'
AND id = '$amendmentId'
AND confirm_status = 'CON2'
AND STATUS = 'A'
AND confirm_by IS NOT NULL
AND student_matrix_no = '$matrixNo'";
		
$result_sqlamend1 = $dbf->query($sqlamend1); 
$dbf->next_record();
$submitDate = $dbf->f('submitDate');
$referenceNo = $dbf->f('referenceNo');

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


$sql_supervisor = " SELECT a.pg_employee_empid, a.ref_supervisor_type_id, d.description as supervisor_type, 
DATE_FORMAT(a.acceptance_date,'%d-%b-%Y') as acceptance_date, h.description as role_status_desc
FROM pg_supervisor a 
LEFT JOIN ref_supervisor_type d ON (d.id = a.ref_supervisor_type_id) 
LEFT JOIN pg_thesis f ON (f.student_matrix_no = a.pg_student_matrix_no)
LEFT JOIN pg_proposal g ON (g.pg_thesis_id = f.id)
LEFT JOIN ref_role_status h ON (h.id = a.role_status)
WHERE a.pg_student_matrix_no='$matrixNo'
AND g.pg_thesis_id = '$thesisId'
AND g.id = '$proposalId'
AND a.acceptance_status = 'ACC'
AND a.ref_supervisor_type_id in ('SV','CS','XS')
AND g.verified_status in ('APP','AWC')
AND g.status in ('APP','APC')
AND g.archived_status IS NULL
AND a.status = 'A'
AND a.role_status = 'PRI'
ORDER BY d.seq, a.ref_supervisor_type_id";

$result_sql_supervisor = $db_klas2->query($sql_supervisor); //echo $sql;
$db_klas2->next_record();
$row_cnt_supervisor = mysql_num_rows($result_sql_supervisor);
$empid = $db_klas2->f('pg_employee_empid');
$employeeId = $db_klas2->f('pg_employee_empid');
$supervisorType = $db_klas2->f('supervisor_type');
$supervisorTypeId = $db_klas2->f('ref_supervisor_type_id');
$acceptanceDate = $db_klas2->f('acceptance_date');
$roleStatusDesc = $db_klas2->f('role_status_desc');
$confirmAcceptanceDate = $db_klas2->f('acceptance_date');

$sql4 = "SELECT a.id
FROM pg_calendar a
LEFT JOIN pg_amendment b ON (b.pg_calendar_id = a.id)
WHERE a.student_matrix_no = '$matrixNo'
AND a.thesis_id = '$thesisId'
AND a.ref_session_type_id = 'VIV'
AND a.recomm_status = 'REC'
AND a.status = 'A'
AND b.pg_thesis_id = '$thesisId'
AND b.student_matrix_no = '$matrixNo'
AND b.submit_status IN ('SUB', 'SUB1')
AND b.status = 'A'
AND b.id = '$amendmentId'
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
	<script>	
	function newAttachment(pid, tid, ppid, rid, mn, role, mid) {
	
			document.location.href = "../viva/review_amendment_attachment.php?pid=" + pid +"&tid="+tid+"&ppid="+ppid+"&ref="+rid+"&mn="+mn+"&role="+role+"&mid="+mid;
	
	}

	</script>
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
				<td><h3><strong>Amendment Details </strong><h3></td>
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
					AND status IN ('A', 'ARC')
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
					<input type="hidden" name="scheId" id= "scheId" value="<?=$calendarIdViva?>" /></td>				
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
                <th width="15%" align="left">Status</th>
				<!--<th width="15%" align="left">Feedback</th>-->
              </tr>
              <input type="hidden" name="rowCntSupervisor" id="rowCntSupervisor" value="<?=$row_cnt_supervisor;?>" />
			  <?

					$varRecCount=0;	
					if ($row_cnt_supervisor>0) {

							

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
                <td align="left"><?if ($supervisorTypeId != 'XS') {?>
                    <?=$supervisorType;?>
                  <br/>
                  <?=$roleStatusDesc?>
                    <?}
								else {
									?>
                    <span style="color:#FF0000">
                      <?=$supervisorType;?>
                  </span><br/>
                  <?=$roleStatusDesc?>
                    <?
								}?>
                    <br/>
                  <?=$acceptanceDate?></td>
                <input type="hidden" name="confirmAcceptanceDate" id="confirmAcceptanceDate" value="<?=$confirmAcceptanceDate; ?>" />
                <td align="left"><?=$employeeId;?></td>
                <td align="left"><?=$employeeName;?></td>
                <td align="left"><a href="javascript:void(0);" onmouseover="toolTip('<?=$departmentName;?>', 300)" onmouseout="toolTip()">
                  <?=$departmentId;?>
                </a></td>
                <?
								$sql12 = "SELECT amendment_status, confirm_date
								FROM pg_amendment
								WHERE id = '$amendmentId'";
								
								$dbg2 = $dbg;
								$result12 = $dbg2->query($sql12); 
								$dbg2->next_record();
								$row_cnt12 = mysql_num_rows($result12);
								$confirm_date = $dbg2->f('confirm_date');
								$amendment_status = $dbg2->f('amendment_status');
				?>
                <td align="left">Confirmed</td>
				<!--<td align="left">
				<a href="view_supervisor_feedback.php?tid=<?=$thesisId?>&pid=<?=$proposalId?>&mn=<?=$matrixNo?>&ad=<?=$amendmentId?>" name="thesisId" value="<?=$thesisId?>" title="View feedback"><img src="../images/view.jpg" width="45" height="30" style="border:0px;" title="View feedback" /></a>
				</td>-->
              </tr>
              <? } else {?>
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
			<br />
			
			
<? 
	$sqlamend1 = "SELECT id, confirm_status, confirm_date, pg_viva_id FROM pg_amendment
	WHERE pg_thesis_id = '$thesisId'
	AND pg_proposal_id = '$proposalId'
	AND reference_no = '$referenceNo'
	AND STATUS = 'A'
	AND confirm_status = 'CON2'
	AND student_matrix_no = '$matrixNo'
	AND amendment_status = 'SUB1'
	AND ref_req_no IS NULL";
		
	$result_sqlamend1 = $dbf->query($sqlamend1); 
	$dbf->next_record();
	$amendmentId = $dbf->f('id');
	$mainStatus= $dbf->f('confirm_status');
	$pg_viva_id= $dbf->f('pg_viva_id');
	$confirm_date= $dbf->f('confirm_date');
	


?>
<? // Call list of amendment
	
	$sqlamend = "SELECT a.id,a.amendment_by_examiner AS amendment, a.feedback_by_examiner, a.amendment_detail_status, 
	a.amendment_confirm_status As amendmentConfirmStatus, faculty_remark as  facultyRemark
	FROM pg_amendment_detail a
	WHERE a.pg_amendment_id = '$amendmentId'
	AND a.pg_thesis_id = '$thesisId'
	AND a.student_matrix_no = '$matrixNo'
	AND a.amendment_detail_status = 'SUB1' 
	AND a.confirm_by IS NOT NULL
	AND a.confirm_date IS NOT NULL
	AND a.amendment_confirm_status IN ('CON', 'CON1')
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
	$amendmentConfirmStatusArray =array();
	$facultyRemark =array();
	
	do{
		$id = $dbb->f('id');
		$amendment = $dbb->f('amendment');
		$feedbackByExaminer = $dbb->f('feedback_by_examiner');
		$amendment_detail_status = $dbb->f('amendment_detail_status');
		$commentArray[$i]=$dbb->f('comment');
		$commentIdArray[$i]=$dbb->f('commentId');
		$confirmStatusArray[$i]=$dbb->f('confirm_status');
		$amendmentConfirmStatusArray[$i]=$dbb->f('amendmentConfirmStatus');
		$facultyRemark[$i]=$dbb->f('facultyRemark');
		
		$idArray[$i] = $id;
		$amendmentArray[$i] =$amendment;
		$feedbackByExaminerArray[$i] =$feedbackByExaminer;
		$amendment_detail_statusArray[$i] =$amendment_detail_status;
		
		$inc++;
		$i++;

	}while($dbb->next_record());
	
?>
<style>
.thetable {
	 table-layout: fixed;
}
.thetable td {
	word-wrap: break-word;         /* All browsers since IE 5.5+ */
    overflow-wrap: break-word;     /* Renamed property in CSS3 draft spec */
}
</style>


	<fieldset><legend><strong>List of Amendment</strong></legend>
	<table width="91%" class="thetable" border="1">
		<tr>
			<input type="hidden" name="amendmentId" id="amendmentId" value="<?=$amendmentId?>" />
			<input type="hidden" name="pg_viva_id" id="pg_viva_id" value="<?=$pg_viva_id?>" />
			<input type="hidden" name="confirm_date" id="confirm_date" value="<?=$confirm_date?>" />
			<th align="center" width="8%">No</th>
			<th align="left" width="27%">Feedback of External Examiner</th>
			<th align="left"width="37%">Amendmenst Based on the comment<br />from External Examiner<br />(Please specify the page number)</th>
			<th width="20%" align="left">Remarks</th>
			<input type="hidden" name="totaldetail" id="totaldetail" value="<?=$inc?>" />
			<input type="hidden" name="amendStat" id="amendStat" value="<?=$amendStat?>" />
		</tr>
		<? if($row_cnt5>0) {?>
			<? for ($i=0; $i<$inc; $i++){ 
			
			$sql13 = "SELECT a.confirm_status, a.comment, a.id AS commentId, b.id AS confirmID, a.confirm_status AS revConfirm, b.confirm_status AS amendStat
			FROM pg_amendment_review a
			LEFT JOIN pg_amendment_confirmation b ON (b.pg_supervisor_empid = a.empid)
			WHERE pg_amend_detail_id = '$idArray[$i]'
			AND a.empid = '$user_id'
			AND a.comment_status IN ('SAV', 'SUB')
			AND a.status = 'A' OR a.status IS NULL";
			$db2 = $db;
			$result_sql13 = $db2->query($sql13); 
			$db2->next_record();
			$comment=$db2->f('comment');
			$commentId=$db2->f('commentId');
			$confirmStatus=$db2->f('confirm_status');
			$confirmID=$db2->f('confirmID');
			$amendStat = $db2->f('amendStat');
			$revConfirm = $db2->f('revConfirm');
				
			?>
			
			<tr>	
				<input type="hidden" name="revConfirm" id="revConfirm" value="<?=$revConfirm?>" />
				<input type="hidden" name="amendmendIdDetail[]" id="amendmendIdDetail" value="<?=$idArray[$i]?>" />
				<input type="hidden" name="commentId[]" id="commentId" value="<?=$commentId?>" />
				<td align="center"><?=$i+1?></td>
				<td align="left"><p style="width:98%;"><?=$feedbackByExaminerArray[$i]?></p></td><!---->
				<td align="left"><p style="width:98%;"><?=$amendmentArray[$i]?>
				<input type="hidden" name="countDetail" id="countDetail" value="<?=$row_cnt5?>" /></p></td>
				<td><p style="width:98%;"><?=$facultyRemark[$i]?></p></td>
			</tr>
			<? } ?>
		<? } else { ?>
			<tr><td colspan="4">No record found.<input type="hidden" name="countDetail" id="countDetail" value="<?=$row_cnt5?>" /></td>
			</tr>
		<? } ?>
	</table>
<?
		$sqlUpload="SELECT * FROM file_upload_amendment
		WHERE amendment_id = '$amendmentId' 
		AND student_matrix_no = '$matrixNo'";			
		$dbh = $dbf;
		$result = $dbh->query($sqlUpload); 
		$dbh->next_record();
		$row_cntupload = mysql_num_rows($result);
		$i = 0;
		$inc = 0;
		do{
			
			$namefile = $dbh->f('fu_document_filename');
			$fu_cd = $dbh->f('fu_cd');
			$namefileArray[$i] = $namefile;
			$fu_cdArray[$i] = $fu_cd;
			$i++;
			$inc++;
		}while($dbh->next_record());

?>
	
	<table>
		<tr>
			<td><strong>Attachment by Student</strong></td>
		</tr>
		<tr>
			<td>
			<? for ($i=0; $i<$inc; $i++)
			{ 
				if($row_cntupload>0) {
			?>
			
				<a href="downloadamend.php?id=<?=$fu_cdArray[$i];?>" target="_blank"><?=$namefileArray[$i] ?>
				<img src="../images/download.png" width="20" height="19" style="border:0px;" title="Download <?=$namefileArray[$i]?>"></a>
				
			<? 
					if($i%4 == 0 && $i !=0)
					{
					   echo "<br>";
					}
				} else {
					echo "<span style = \"margin-left: 10px\">No attachment uploaded</span";
				} 
			} ?>
			</td>
		</tr>
	</table>
	</fieldset>
<?
		$sqlUpload1="SELECT * FROM file_upload_amendment
		WHERE amendment_id = '$amendmentId' 
		AND employee_id = '$empid'";			
		$dbh1 = $dbf;
		$result1 = $dbh1->query($sqlUpload1); 
		$dbh1->next_record();
		$row_cntupload1 = mysql_num_rows($result1);
		$i1 = 0;
		$inc1 = 0;
		do{
			
			$namefile1 = $dbh1->f('fu_document_filename');
			$fu_cd1 = $dbh1->f('fu_cd');
			$namefileArray1[$i1] = $namefile1;
			$fu_cdArray1[$i1] = $fu_cd1;
			$i1++;
			$inc1++;
		}while($dbh1->next_record());

?>
	
	<table>
		<tr>
			<td><strong>Attachment by Supervisor</strong></td>
		</tr>
		<tr>
			<td>
			<? for ($i1=0; $i1<$inc1; $i1++)
			{ 
				if($row_cntupload1>0) {
			?>
			
			<a href="downloadamend.php?id=<?=$fu_cdArray1[$i1];?>" target="_blank"><?=$namefileArray1[$i1] ?>
		 	<img src="../images/download.png" width="20" height="19" style="border:0px;" title="Download <?=$namefileArray1[$i1]?>"></a>
				
			<? 
					if($i%4 == 0 && $i !=0)
					{
					   echo "<br>";
					}
				} else {
					echo "<span style = \"margin-left: 10px\">No attachment uploaded</span";
				}    
			} ?>
			</td>
		</tr>
	</table>
	<table>
		<tr>
		  <td>
		  <input type="button" name="btnBack" id="btnBack" value="Back" onClick="javascript:document.location.href='amendment_review.php';"/>
		  </td>
		  <td><!--<input type="submit" name="submit" value="Print" onclick="javascript:open_win('print_amendment_faculty.php?tid=<?=$thesisId?>&pid=<?=$proposalId?>&mn=<?=$matrixNo?>&ad=<?=$amendmentId?>',1000,760,0,0,0,1,0,1,1,0,5,'winupload'); "/>--></td>	  
		<td> <input type="submit" name="submit" value="Print PDF" onclick="javascript:open_win('pdf_viva_amendment_faculty.php?tid=<?=$thesisId?>&pid=<?=$proposalId?>&rid=<?=$referenceNo?>&mn=<?=$matrixNo?>&ad=<?=$amendmentId?>',1000,760,0,0,0,1,0,1,1,0,5,'winupload'); "/></td>  
		</tr>
		</tr>
	</table>
	<table width="485">
			<tr>							
				<td width="477"><span style="color:#FF0000"> Notes:</span><br/>
			  1. To print amendment, click print button on the interface and then click (Ctrl + P) on keyboard.</td>
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




