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

/*ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);*/

include("../../../lib/common.php");
checkLogin();

$user_id=$_SESSION['user_id'];

$thesisId = $_GET['tid']; 
$proposalId = $_GET['pid'];
$matrixNo=$_GET['mn'];
$amendmentId=$_GET['ad'];


$sqlamend1 = "SELECT id, submit_date AS submitDate, reference_no as referenceNo FROM pg_amendment
WHERE pg_thesis_id = '$thesisId'
AND pg_proposal_id = '$proposalId'
AND id = '$amendmentId'
AND confirm_status = 'CON1'
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
function runnum4($column_name, $tblname) 
{ 
    global $db_klas2;
    
    $run_start = "001";
    
    $sql_slct_max = "SELECT MAX(SUBSTR($column_name,1,11)) AS run_id FROM $tblname";
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


if(isset($_POST['btnsubmit']) && ($_POST['btnsubmit'] <> ""))
{
	unset($redirect);

	$amendmendIdDetail = $_REQUEST['amendmendIdDetail'];
	$commentId = $_REQUEST['commentId'];
	@$statusA = $_REQUEST['statusA'];
	@$comment = $_REQUEST['comment'];
	$pg_viva_id = $_REQUEST['pg_viva_id'];
	
	$countDetail = $_REQUEST['countDetail'];
	$thesisId = $_REQUEST['thesisId'];
	$proposalId = $_REQUEST['proposalId'];
	$amendmentId = $_REQUEST['amendmentId'];
	$curdatetime = date("Y-m-d H:i:s");
	$curdatetime1 = date("Y-m-d H:i:s");
	$inc = $_REQUEST['totaldetail'];
	$scheId= $_REQUEST['scheId'];
	
	$reviewId = "R".runnum2('id','pg_amendment_review');
	$conId = "C".runnum2('id','pg_amendment_confirmation');
	$msg = array();
	
	$lock_tables="LOCK TABLES pg_amendment WRITE"; //lock the table
	$db->query($lock_tables);
	
	$sql1 = "UPDATE pg_amendment SET
	confirm_status = 'CON2', confirm_date = '$curdatetime', faculty_remark_date = '$curdatetime', remark_by = '$user_id',
	modify_by = '$user_id', modify_date = '$curdatetime1'
	WHERE id = '$amendmentId'
	AND pg_thesis_id = '$thesisId'
	AND pg_proposal_id = '$proposalId'
	AND student_matrix_no = '$matrixNo'
	AND status = 'A'
	AND amendment_status = 'SUB1'";
	
	$dba->query($sql1); 
	
	$lock_tables="UNLOCK TABLES"; //lock the table
	$db->query($lock_tables);
					
	$save = "1";
			
	//////query for insert into pg_senate/ for senate endorsement///////
	$sql_senate = "SELECT a.id, a.viva_status, d.description AS vivaStatDesc, a.pg_calendar_id, a.student_matrix_no, 
	a.pg_thesis_id, defense_date, DATE_FORMAT(f.defense_date,'%d-%b-%Y') AS viva_date, g.id as pg_proposal_id,
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
	AND ((a.viva_status = 'PMI' AND c.confirm_status = 'CON2') 
	OR (a.viva_status = 'PMA' AND c.confirm_status = 'CON2') 
	OR (a.viva_status = 'PAS' AND c.status = 'ARC1')
	OR (a.viva_status = 'FAI' AND a.appeal_result = 'DIS')
	OR (a.viva_status = 'PMR' AND c.confirm_status = 'CON2'))
	AND g.archived_status IS NULL
	AND a.id = '$pg_viva_id'
	AND a.student_matrix_no = '$matrixNo'
	AND a.pg_thesis_id = '$thesisId'
	AND a.id NOT IN (SELECT pg_viva_id FROM pg_senate)";
	
	$dbSenate = $db;					
	$result_sqlforsenate = $dbSenate->query($sql_senate); 
	$dbSenate->next_record();
	$row_cnt_result_sqlforsenate = mysql_num_rows($result_sqlforsenate);
	
	if($row_cnt_result_sqlforsenate > 0) {

		
		$pgVivaId = $dbSenate->f('id');
		$pg_thesis_id = $dbSenate->f('pg_thesis_id');
		$student_matrix_no = $dbSenate->f('student_matrix_no');
		$pg_proposal_id = $dbSenate->f('pg_proposal_id');
		$pg_calendar_id = $dbSenate->f('pg_calendar_id');
		$vivaStatDesc = $dbSenate->f('vivaStatDesc');
		
		////////////////work completion////////
		$sqlwork = "SELECT pw.id as pgWorkid, pwe.id as pgEvaWorkId
		FROM pg_thesis pt 
		LEFT JOIN pg_proposal pp ON (pp.pg_thesis_id=pt.id) 
		LEFT JOIN ref_proposal_status rps ON (rps.id = pp.verified_status) 
		LEFT JOIN ref_proposal_status rps2 ON (rps2.id = pp.status) 
		LEFT JOIN pg_work_evaluation pwe ON (pwe.pg_thesis_id = pt.id)
		LEFT JOIN pg_work pw ON (pw.id = pwe.pg_work_id)
		WHERE pt.student_matrix_no = '$student_matrix_no'
		AND pp.verified_status in ('APP','AWC')				
		AND pp.archived_status is null
		AND pt.ref_thesis_status_id_proposal in ('APP','AWC','APC')
		AND pwe.ref_work_marks_id IS NOT NULL 
		AND ((pwe.status = 'APP' AND pwe.ref_work_marks_id IN ('SAT','SUB')) 
		OR (pwe.status = 'DIS' AND pwe.ref_work_marks_id = 'SUB' AND pwe.proposed_marks_id = 'SAT') 
		OR (pwe.status = 'DIS' AND pwe.ref_work_marks_id = 'SAT' AND pwe.proposed_marks_id = 'SUB')
		OR (pwe.status = 'DIS' AND pwe.ref_work_marks_id = 'NSA' AND pwe.proposed_marks_id IN ('SAT','SUB')))
		AND pw.archived_status IS NULL 
		AND pwe.student_matrix_no = '$student_matrix_no'
		AND pt.id = '$pg_thesis_id'
		AND pp.id = '$pg_proposal_id'
		ORDER BY pt.id";
		
		$dbWork = $dbg;
		$resultwork = $dbWork->query($sqlwork);
		$resultsqlwork = $dbWork->next_record(); 
		$pgWorkid = $dbWork->f('pgWorkid');
		$pgEvaWorkId = $dbWork->f('pgEvaWorkId');
		
		
		$senateNewId = runnum4('id','pg_senate');
		$refNewId = "S".runnum2('reference_no','pg_senate');
		$curdatetime = date("Y-m-d H:i:s");
		
		$lock_tables="LOCK TABLES pg_senate WRITE"; //lock the table
		$db->query($lock_tables);
		
		$sqlinsertsenate = "INSERT INTO pg_senate
		(id, pg_viva_id, reference_no, student_matrix_no, pg_thesis_id, pg_proposal_id, pg_calendar_id, status, 
		insert_by, insert_date, modify_by, modify_date, pg_work_id, pg_work_evaluation_id, submit_status, respond_status)
		VALUES
		('$senateNewId','$pgVivaId','$refNewId', '$student_matrix_no', '$pg_thesis_id', '$pg_proposal_id', '$pg_calendar_id', 'A',
		'$user_id', '$curdatetime', '$user_id', '$curdatetime', '$pgWorkid', '$pgEvaWorkId', 'IN1', 'N')";
		
		$result_sqlinsertsenate = $dbg->query($sqlinsertsenate); 
		
		$lock_tables="UNLOCK TABLES"; //lock the table
		$db->query($lock_tables);
		
		//////email /////////////////////////////
		$sql3 = "SELECT id, defense_date, DATE_FORMAT(defense_date,'%d-%b-%Y') as viva_date, 
		DATE_FORMAT(defense_stime,'%h:%i%p') as viva_stime,
		DATE_FORMAT(defense_etime,'%h:%i%p') as viva_etime, venue, recomm_status, status
		FROM pg_calendar
		WHERE student_matrix_no = '$student_matrix_no'
		AND thesis_id = '$pg_thesis_id'
		AND id = '$scheId'
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
				
		$sqlemail = "SELECT email, name FROM new_employee
		WHERE `empid` = '$user_id'";
		$resultreceive = $dbk->query($sqlemail);
		$resultsqlreceive = $dbk->next_record(); 
		$staffEmail = $dbk->f('email');
		$staffName = $dbk->f('name');
		
		$sqlstudent = "SELECT email, name FROM student
		WHERE `matrix_no` = '$student_matrix_no'";
		
		
		if (substr($student_matrix_no,0,2) != '07') { 
			$dbStudent= $dbc; 
		} 
		else { 
			$dbStudent=$dbc1; 
		}
		
		$resultsqlstudent = $dbStudent->query($sqlstudent);
		$student = $dbStudent->next_record(); 
		$studentEmail = $dbStudent->f('email');
		$studentName = $dbStudent->f('name');
		
				
		$sqlSenate = "SELECT const_value FROM base_constant
		WHERE const_term = 'EMAIL_SENATE'";
		$dbSenateEmail = $db;
		$result_sqlSenate = $dbSenateEmail->query($sqlSenate);
		$kSenate = $dbSenateEmail->next_record(); 
		$senateEmail = $dbSenateEmail->f('const_value');
		
		$sqlmsgSenate = "SELECT const_value FROM base_constant
		WHERE const_term = 'MESSAGE_SCH_TO_SEN'";
		
		$dbMsgSenate = $dbg;
		
		$result_sqlmsgSenate = $dbMsgSenate->query($sqlmsgSenate); 
		$dbMsgSenate->next_record();
		$row_cnt_msg = mysql_num_rows($result_sqlmsgSenate);
		$msgConstValue = $dbMsgSenate->f('const_value');
		if($msgConstValue == 'Y')
		{
			include("../../../app/application/inbox/viva/school_senate_1.php");
		}
	
		$sqlemailsenate = "SELECT const_value FROM base_constant
		WHERE const_term = 'EMAIL_SCH_TO_SEN'";
		
		$dbSenate = $dbg;
		
		$result_sqlemailsenate = $dbSenate->query($sqlemailsenate); 
		$dbSenate->next_record();
		$row_cnt_emailSenate = mysql_num_rows($result_sqlemailsenate);
		$emailConstValue1 = $dbSenate->f('const_value');
		
		if ($emailConstValue1 == 'Y')
		{
			//echo "sadsa";
			include("../../../app/application/email/viva/email_school_senate_1.php");
		}
			
	}
		
			
			echo "<script>window.location = 'view_amendment_review_faculty.php?tid=".$thesisId."&pid=".$proposalId."&save=".$save."&ad=".$amendmentId."&mn=".$matrixNo."';</script>";	
}
if(isset($_POST['btnUpdate']) && ($_POST['btnUpdate'] <> ""))
{
	$thesisId = $_REQUEST['thesisId'];
	$proposalId = $_REQUEST['proposalId'];
	$amendmentId = $_REQUEST['amendmentId'];
	
	$curdatetime = date("Y-m-d H:i:s");
	
	$check = $_REQUEST['amendCheck'];
	
	while (list ($key,$val) = @each ($check)) 
	{
		$no=1+$val;
		if (empty($_POST['remark'][$val])) $msg[] = "<div class=\"error\"><span>Please provide comment for amendment no $no.</span></div>";
	}
	if(empty($msg))
	{
		if (sizeof($_POST['amendCheck'])>0) {
			
			
			while (list ($key,$val) = @each ($_POST['amendCheck'])) 
			{
				$reviewId = "R".runnum2('id','pg_amendment_review');
				$remark = $_POST['remark'][$val];
				$amendmendIdDetail = $_POST['amendmendIdDetail'][$val];
				$commentId = $_POST['commentId'][$val];
				
				$lock_tables="LOCK TABLES pg_amendment_detail WRITE"; //lock the table
				$db->query($lock_tables);
				
				$sql1 = "UPDATE pg_amendment_detail
				SET faculty_remark = '$remark', modify_by = '$user_id', modify_date = '$curdatetime'
				WHERE id = '$amendmendIdDetail'
				AND amendment_detail_status = 'SUB1'
				AND amendment_confirm_status = 'CON1'
				AND STATUS = 'A'";
				
				$dba->query($sql1);
				
				$lock_tables="UNLOCK TABLES"; //lock the table
				$db->query($lock_tables);
					
				//echo "$amendmentSave<br>"."$feedbackAdd<br>"."$amendmendIdDetail<br>"."$proposalId<br>"."$thesisId<br>";
			}
			
			
			
			$msg[] = "<div class=\"success\"><span>Remarks successfully added.</span></div>";
		}
		else {
			$msg[] = "<div class=\"error\"><span>Please tick checkbox provided.</span></div>";
		}
	}

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
<? 
	$sqlamend1 = "SELECT id, confirm_status, confirm_date, pg_viva_id FROM pg_amendment
	WHERE pg_thesis_id = '$thesisId'
	AND pg_proposal_id = '$proposalId'
	AND reference_no = '$referenceNo'
	AND STATUS = 'A'
	AND confirm_status = 'CON1'
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
	<table width="95%" class="thetable" border="1">
		<tr>
			<th align="center" width="3%">Tick
				<input type="hidden" name="amendmentId" id="amendmentId" value="<?=$amendmentId?>" />
				<input type="hidden" name="pg_viva_id" id="pg_viva_id" value="<?=$pg_viva_id?>" />
				<input type="hidden" name="confirm_date" id="confirm_date" value="<?=$confirm_date?>" />
		  </th>
			<th align="center" width="3%">No</th>
			<th align="left" width="31%">Feedback of External Examiner</th>
			<th align="left"width="35%">Amendmenst Based on the comment<br />
		  from External Examiner<br />(Please specify the page number)</th>
			<th width="28%" align="left">Remarks</th>
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
				<td align="center">
				<input type="hidden" name="revConfirm" id="revConfirm" value="<?=$revConfirm?>" />
				<input type="checkbox" name="amendCheck[]" id="amendCheck" value="<?=$i?>"/>
				<input type="hidden" name="amendmendIdDetail[]" id="amendmendIdDetail" value="<?=$idArray[$i]?>" />
				<input type="hidden" name="commentId[]" id="commentId" value="<?=$commentId?>" /></td>
				<td align="center"><?=$i+1?></td>
				<td align="left"><p style="width:98%;"><?=$feedbackByExaminerArray[$i]?></p></td><!---->
				<td align="left"><p style="width:98%;"><?=$amendmentArray[$i]?></p>
				<input type="hidden" name="countDetail" id="countDetail" value="<?=$row_cnt5?>" /></td>
				<td><textarea cols="32" rows="4" name="remark[]" id="remark"><?=$facultyRemark[$i]?></textarea></td>
			</tr>
			<? } ?>
		<? } else { ?>
			<tr><td colspan="4">No record found.<input type="hidden" name="countDetail" id="countDetail" value="<?=$row_cnt5?>" /></td>
			</tr>
		<? } ?>
	</table>
	<table>
		<tr>
			<td><input type="submit" name="btnUpdate" id="btnUpdate" value="Update" /></td>
		</tr>
	</table>
<?
		$sqlUpload="SELECT * FROM file_upload_amendment
		WHERE amendment_id = '$amendmentId' 
		AND student_matrix_no = '$matrixNo'";			
		$dbh = $dbf;
		$result = $dbh->query($sqlUpload); 
		$dbh->next_record();
		$row_cntam = mysql_num_rows($result);
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
				if ($row_cntam>0) {
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
	
<?
		$sqlUpload1="SELECT * FROM file_upload_amendment
		WHERE amendment_id = '$amendmentId' 
		AND employee_id = '$empid'";			
		$dbh1 = $dbf;
		$result1 = $dbh1->query($sqlUpload1); 
		$dbh1->next_record();
		$row_cnt1 = mysql_num_rows($result1);
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
			{ 	if($row_cnt1>0) {?>
			
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
	</fieldset>
	<table>
		<tr>
			<td colspan="2"><label><span style="color:#FF0000">Notes:</span><br/>
					1. Submit button is for submit amendments on thesis to faculty for remarks.</label></td>
		</tr>
		<tr>
		  <td colspan="2">
		  <input type="button" name="btnBack" id="btnBack" value="Back" onClick="javascript:document.location.href='amendment_review.php';"/>	  
		  <!--<input type="submit" name="btnReq" id="btnReq" value="Request Changes" onclick = ""/>
		  <input type="submit" name="btnSubmit" id="btnSubmit" value="Submit" onclick = "return submitReport()"/>-->
		  <input type="submit" name="btnsubmit" id="btnsubmit" value="Submit" /></td>
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




