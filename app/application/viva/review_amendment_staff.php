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
$referenceNo=$_GET['rid'];
$matrixNo=$_GET['mn'];
$rolestatus=$_GET['role'];
$mid=$_GET['mid'];

$sqlamend1 = "SELECT id, submit_date AS submitDate FROM pg_amendment
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
$submitDate = $dbf->f('submitDate');

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


if(isset($_POST['btnConfirmAll']) && ($_POST['btnConfirmAll'] <> ""))
{
	unset($redirect);

	$amendmendIdDetail = $_REQUEST['amendmendIdDetail'];
	$commentId = $_REQUEST['commentId'];
	$statusA = $_REQUEST['statusA'];
	$comment = $_REQUEST['comment'];
	
	$countDetail = $_REQUEST['countDetail'];
	$thesisId = $_REQUEST['thesisId'];
	$proposalId = $_REQUEST['proposalId'];
	$amendmentId = $_REQUEST['amendmentId'];
	$curdatetime = date("Y-m-d H:i:s");
	$curdatetime1 = date("Y-m-d H:i:s");
	$inc = $_REQUEST['totaldetail'];
	$reviewId = "R".runnum2('id','pg_amendment_review');
	$conId = "C".runnum2('id','pg_amendment_confirmation');
	$scheId = $_REQUEST['scheId'];
	$thesisTitle = $_REQUEST['thesisTitle'];
	$studentName = $_REQUEST['studentName'];
	
	$msg = array();
	
	if($rolestatus == 'PRI')
	{
		$lock_tables="LOCK TABLES pg_amendment WRITE, pg_amendment_detail WRITE, pg_amendment_review WRITE, pg_amendment_confirmation WRITE"; //lock the table
		$db->query($lock_tables);
					
		$sql1 = "UPDATE pg_amendment SET
		confirm_status = 'CON', confirm_by = '$user_id', confirm_date = '$curdatetime', 
		modify_by = '$user_id', modify_date = '$curdatetime1'
		WHERE id = '$amendmentId'
		AND pg_thesis_id = '$thesisId'
		AND pg_proposal_id = '$proposalId'
		AND student_matrix_no = '$matrixNo'
		AND STATUS = 'A'";
		
		$dba->query($sql1); 
		
		$sql1 = "UPDATE pg_amendment_detail SET
		amendment_confirm_status = 'CON', confirm_by = '$user_id', confirm_date = '$curdatetime',
		modify_by = '$user_id', modify_date = '$curdatetime1'
		WHERE pg_amendment_id = '$amendmentId'
		AND pg_thesis_id = '$thesisId'
		AND student_matrix_no = '$matrixNo'
		AND STATUS = 'A'";
		
		$dba->query($sql1); 
		
		$sql1 = "UPDATE pg_amendment_review
				SET comment = '', modify_by = '$user_id', modify_date = '$curdatetime'
				WHERE pg_amendment_id = '$amendmentId'
				AND empid = '$user_id'
				AND comment_status = 'SUB'";
				
				$dba->query($sql1);
		
		$sql1 = "INSERT INTO pg_amendment_confirmation
				(id, pg_amendment_id, confirm_status, pg_supervisor_empid, confirm_by, confirm_date, status, insert_by, insert_date)
				VALUES
				('$conId', '$amendmentId', 'CON',  '$user_id', '$user_id', '$curdatetime', 'A', '$user_id', '$curdatetime')";
				
		$dba->query($sql1); 
		
		$lock_tables="UNLOCK TABLES"; //lock the table
		$db->query($lock_tables);
		
		///////select date schedule//////////////////////////
		$sql3 = "SELECT id, defense_date, DATE_FORMAT(defense_date,'%d-%b-%Y') as viva_date, 
		DATE_FORMAT(defense_stime,'%h:%i%p') as viva_stime,
		DATE_FORMAT(defense_etime,'%h:%i%p') as viva_etime, venue, recomm_status, status
		FROM pg_calendar
		WHERE student_matrix_no = '$matrixNo'
		AND thesis_id = '$thesisId'
		/*AND recomm_status = 'REC'*/
		/*AND defense_date >= DATE_ADD(DATE_FORMAT(NOW(),'%Y-%m-%d'),INTERVAL ".$defenseDurationParam." DAY)*/
		AND status = 'A'
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
		
		$sqlemail = "SELECT name,email FROM new_employee
		WHERE `empid` = '$user_id'";
		$resultreceive = $dbk->query($sqlemail);
		$resultsqlreceive = $dbk->next_record(); 
		$superEmail = $dbk->f('email');
		$staffName = $dbk->f('name');
		
		$sqlemail = "SELECT email FROM student
		WHERE `matrix_no` = '$matrixNo'";
		$dbk1 = $dbk;
		$resultreceive = $dbk1->query($sqlemail);
		$resultsqlreceive = $dbk1->next_record(); 
		$studemail = $dbk1->f('email');
			
		///// message notification////
		$sqlmsg = "SELECT const_value FROM base_constant
		WHERE const_term = 'MESSAGE_SUP_TO_STU'";
		
		$result_sqlmsg = $dbg->query($sqlmsg); 
		$dbg->next_record();
		$row_cnt_msg = mysql_num_rows($result_sqlmsg);
		$msgConstValue = $dbg->f('const_value');
		
		if ($msgConstValue == 'Y')
		{
			include("../../../app/application/inbox/viva/review_amendment_confirm.php");
		}
		
		////email notification/////
		$sqlemail = "SELECT const_value FROM base_constant
		WHERE const_term = 'EMAIL_SUP_TO_STU'";
		$dbg1 = $dbg;
		$result_sqlemail = $dbg1->query($sqlemail); 
		$dbg1->next_record();
		$row_cnt_email = mysql_num_rows($result_sqlemail);
		$emailConstValue = $dbg1->f('const_value');
		
		if ($emailConstValue == 'Y')
		{
			include("../../../app/application/email/viva/email_review_amendment_confirm.php");
		}
		
		$save = "3";
		echo "<script>window.location = 'view_amendment_staff_after.php?tid=".$thesisId."&pid=".$proposalId."&save=".$save."&rid=".$referenceNo."&mn=".$matrixNo."&role=".$rolestatus."&mid=".$amendmentId."';</script>";	
		$msg[] = "<div class=\"success\"><span>Amendment successfully submitted.</span></div>";
		/*$msg[] = "<div class=\"info\"><span>The system will redirect to the previous page in 4 seconds.</span></div>";
		$redirect = "1";*/
	}
}
if(isset($_POST['btnSubmit']) && ($_POST['btnSubmit'] <> ""))
{
	unset($redirect);

	$amendmendIdDetail = $_REQUEST['amendmendIdDetail'];
	$commentId = $_REQUEST['commentId'];
	$statusA = $_REQUEST['statusA'];
	$comment = $_REQUEST['comment'];
	
	$countDetail = $_REQUEST['countDetail'];
	$thesisId = $_REQUEST['thesisId'];
	$proposalId = $_REQUEST['proposalId'];
	$amendmentId = $_REQUEST['amendmentId'];
	$curdatetime = date("Y-m-d H:i:s");
	$curdatetime1 = date("Y-m-d H:i:s");
	$inc = $_REQUEST['totaldetail'];
	$reviewId = "R".runnum2('id','pg_amendment_review');
	$conId = "C".runnum2('id','pg_amendment_confirmation');
	$msg = array();
	
	if ($countDetail == 0) $msg[] = "<div class=\"error\"><span>Please insert amendment detail.</span></div>";
	
	if(empty($msg))
	{
		if($rolestatus == 'PRI')
		{
			$lock_tables="LOCK TABLES pg_amendment WRITE, pg_amendment_detail WRITE, pg_amendment_confirmation WRITE"; //lock the table
			$db->query($lock_tables);
			
			$sql1 = "UPDATE pg_amendment SET
			confirm_status = 'CON', confirm_by = '$user_id', confirm_date = '$curdatetime', 
			modify_by = '$user_id', modify_date = '$curdatetime1'
			WHERE id = '$amendmentId'
			AND pg_thesis_id = '$thesisId'
			AND pg_proposal_id = '$proposalId'
			AND student_matrix_no = '$matrixNo'
			AND STATUS = 'A'";
			
			$dba->query($sql1); 
			
			$sql1 = "UPDATE pg_amendment_detail SET
			amendment_confirm_status = 'CON', confirm_by = '$user_id', confirm_date = '$curdatetime',
			modify_by = '$user_id', modify_date = '$curdatetime1'
			WHERE pg_amendment_id = '$amendmentId'
			AND pg_thesis_id = '$thesisId'
			AND student_matrix_no = '$matrixNo'
			AND STATUS = 'A'";
			
			$dba->query($sql1); 
			
			$sql1 = "INSERT INTO pg_amendment_confirmation
					(id, pg_amendment_id, confirm_status, pg_supervisor_empid, confirm_by, confirm_date, status, insert_by, insert_date)
					VALUES
					('$conId', '$amendmentId', 'CON',  '$user_id', '$user_id', '$curdatetime', 'A', '$user_id', '$curdatetime')";
					
			$dba->query($sql1); 
			
			$lock_tables="UNLOCK TABLES"; //lock the table
			$db->query($lock_tables);
			
			$msg[] = "<div class=\"success\"><span>Amendment successfully submitted.</span></div>";
			$msg[] = "<div class=\"info\"><span>The system will redirect to the previous page in 4 seconds.</span></div>";
			$redirect = "1";
			
		}
		else {
			
			$commentId = $_POST['commentId'];
			for ($i=0; $i<$inc; $i++){
				
				$lock_tables="LOCK TABLES pg_amendment_review WRITE"; //lock the table
				$db->query($lock_tables);
				
				if(empty($commentId))
				{		
					$sql1 = "INSERT INTO pg_amendment_review 
					(id, pg_amendment_id, empid, pg_amend_detail_id, comment, comment_date, status, comment_status, insert_by, insert_date, confirm_status)
					VALUES
					('$reviewId', '$amendmentId', '$user_id',  '$amendmendIdDetail[$i]', '$comment[$i]', '$curdatetime', 'A', 'SAV', '$user_id', '$curdatetime', 'CON')";
					
					$dba->query($sql1); 
					
				}
				else{
					$sql1 = "UPDATE pg_amendment_review
					SET comment = '$comment[$i]', confirm_status = 'CON', modify_by = '$user_id', modify_date = '$curdatetime'
					WHERE pg_amend_detail_id = '$amendmendIdDetail'
					AND empid = '$user_id'
					AND comment_status = 'SAV'
					AND id = '$commentId'";
					
					$dba->query($sql1);
									
				}
				
				$lock_tables="UNLOCK TABLES"; //lock the table
				$db->query($lock_tables);
			}
			
			$lock_tables="LOCK TABLES pg_amendment_confirmation WRITE"; //lock the table
			$db->query($lock_tables);
				
			$sql1 = "INSERT INTO pg_amendment_confirmation
				(id, pg_amendment_id, confirm_status, pg_supervisor_empid, confirm_by, confirm_date, status, insert_by, insert_date)
				VALUES
				('$conId', '$amendmentId', 'CON',  '$user_id', '$user_id', '$curdatetime', 'A', '$user_id', '$curdatetime')";
					
				$dba->query($sql1); 
				
			$lock_tables="UNLOCK TABLES"; //lock the table
			$db->query($lock_tables);
		}
	}

}
if(isset($_POST['btnReq']) && ($_POST['btnReq'] <> ""))
{
	////////detail old not appear !
	unset($redirect);

	$thesisId = $_REQUEST['thesisId'];
	$proposalId = $_REQUEST['proposalId'];
	$amendmentId = $_REQUEST['amendmentId'];
	$curdatetime = date("Y-m-d H:i:s");
	$amendStat = $_REQUEST['amendStat'];
	$confirmID = $_REQUEST['confirmID'];
	$pg_viva_id = $_REQUEST['pg_viva_id'];
	$scheId = $_REQUEST['scheId'];
	$thesisTitle = $_REQUEST['thesisTitle'];
	$studentName = $_REQUEST['studentName'];
	
	$conId = "C".runnum2('id','pg_amendment_confirmation');
	
	$reqChangesId = "C".runnum2('ref_req_no','pg_amendment');
	
	$amendmentIdNew = runnum('id','pg_amendment');		

	
	$sqlamend1 = "SELECT id ,pg_amendment_id FROM pg_amendment_confirmation
	WHERE pg_amendment_id = '$amendmentId'
	AND confirm_status = 'REQ'
	AND STATUS = 'A'";
	
			
	$result_sqlamend1 = $dbd->query($sqlamend1); 
	$dbd->next_record();
	$confirmIdFromOthers = $dbd->f('id');
	$pg_amendment_idOld = $dbd->f('pg_amendment_id');	
	
	if(empty($confirmIdFromOthers))
	{
		$lock_tables="LOCK TABLES pg_amendment WRITE"; //lock the table
		$db->query($lock_tables);
		
		///////save old amendment for tracking & create new amendment but same reference no////////////////////////
		$sqlamend = "UPDATE pg_amendment
					SET modify_by = '$user_id', modify_date = '$curdatetime', ref_req_no = '$reqChangesId', status = 'ARC', amendment_status = 'REQ'
					WHERE id = '$amendmentId'";
		$dba->query($sqlamend);
	
		$sqlamend = "INSERT INTO pg_amendment
		(id, pg_thesis_id, reference_no, pg_proposal_id, status, amendment_status, student_matrix_no, insert_by, insert_date, submit_date, submit_status, pg_viva_id, pg_calendar_id)
		VALUES
		('$amendmentIdNew', '$thesisId', '$referenceNo', '$proposalId', 'A', 'REQ', '$matrixNo', '$user_id', '$curdatetime', '$submitDate', 'SUB', '$pg_viva_id', '$scheId')";
		$dba->query($sqlamend);
		//////////////////////////////////////END////////////////////////////////////////////////////////////////////////
		
		$lock_tables="UNLOCK TABLES"; //lock the table
		$db->query($lock_tables);
		
		//////////////update amendmentid file/attachment//////////////
		$sqlUpload="SELECT * FROM file_upload_amendment
		WHERE amendment_id = '$amendmentId' 
		AND student_matrix_no = '$matrixNo'";			
		$dbh = $dbf;
		$result = $dbh->query($sqlUpload); 
		$dbh->next_record();
		$i = 0;
		$inc = 0;
		do{
			
			$namefile = $dbh->f('fu_document_filename');
			$fu_cd = $dbh->f('fu_cd');
			
			$lock_tables="LOCK TABLES file_upload_amendment WRITE"; //lock the table
			$db->query($lock_tables);
			
			$sql1 = "UPDATE file_upload_amendment SET
			amendment_id = '$amendmentIdNew'
			WHERE fu_cd = '$fu_cd'
			AND student_matrix_no = '$matrixNo'";
			
			$dba->query($sql1); 
			
			$lock_tables="UNLOCK TABLES"; //lock the table
			$db->query($lock_tables);

			$i++;
			$inc++;
		}while($dbh->next_record());
		////////////////////////END////////////////////////
		
		///////////Save old amendment detail & create new//////////////////////////////////
		$sqlamend = "SELECT a.id,a.amendment_by_examiner AS amendment, a.feedback_by_examiner, 
		a.amendment_detail_status, a.amendment_confirm_status, a.confirm_by, a.confirm_date
		FROM pg_amendment_detail a
		WHERE a.pg_amendment_id = '$amendmentId'
		AND a.pg_thesis_id = '$thesisId'
		AND a.student_matrix_no = '$matrixNo'
		AND a.amendment_detail_status IN ('SUB','CON') 
		/*AND a.confirm_by IS NULL
		AND a.confirm_date IS NULL*/
		AND a.status = 'A'";
		
		$result_sqlamend = $dbb->query($sqlamend); 
		$dbb->next_record();
		$row_cnt5 = mysql_num_rows($result_sqlamend);
		$i= 0;
		$inc= 0;
		
		do{
			$amendmentDetailId = "A".runnum2('id','pg_amendment_detail');
			$reviewId = "R".runnum2('id','pg_amendment_review');
			
			$id = $dbb->f('id');
			$amendment = $dbb->f('amendment');
			$feedbackByExaminer = $dbb->f('feedback_by_examiner');
			$amendment_detail_status = $dbb->f('amendment_detail_status');
			$comment=$dbb->f('comment');
			$commentIdArray=$dbb->f('commentId');
			$confirmStatusArray=$dbb->f('confirm_status');
			$amendment_confirm_status=$dbb->f('amendment_confirm_status');
			$confirm_by=$dbb->f('confirm_by');
			$confirm_date=$dbb->f('confirm_date');
			
			if ($confirm_date == '' || empty($confirm_date))
			{
				$confirm_date = 'NULL';
			}
			else {
				$confirm_date = "'$confirm_date'";
			}
			
			$lock_tables="LOCK TABLES pg_amendment_detail WRITE"; //lock the table
			$db->query($lock_tables);
			
			//////copy amendment detail
			$sqlamendetail = "INSERT INTO pg_amendment_detail 
			(id, pg_amendment_id, feedback_by_examiner, status, insert_by, insert_date, pg_thesis_id, student_matrix_no, amendment_detail_status, amendment_confirm_status, confirm_by, confirm_date)
			VALUES
			('$amendmentDetailId', '$amendmentIdNew', '$feedbackByExaminer', 'A', '$user_id', '$curdatetime', '$thesisId', '$matrixNo', 'SUB', '$amendment_confirm_status', '$confirm_by', $confirm_date)";
				
			$dba->query($sqlamendetail);
			
			$lock_tables="UNLOCK TABLES"; //lock the table
			$db->query($lock_tables);
			
			
			
			$sql13 = "SELECT a.confirm_status, a.comment, a.id AS commentId, b.id AS confirmID, b.confirm_status AS amendStat
			FROM pg_amendment_review a
			LEFT JOIN pg_amendment_confirmation b ON (b.pg_supervisor_empid = a.empid)
			WHERE pg_amend_detail_id = '$id'
			AND a.empid = '$user_id'
			/*AND a.comment_status = 'SAV' OR a.comment_status IS NULL*/
			AND a.status = 'A' OR a.status IS NULL";
			$db2 = $db;
			$result_sql13 = $db2->query($sql13); 
			$db2->next_record();
			$comment=$db2->f('comment');
			$commentId=$db2->f('commentId');
			$confirmStatus=$db2->f('confirm_status');
			$confirmID=$db2->f('confirmID');
			$amendStat = $dbb->f('amendStat');
	
	
			$lock_tables="LOCK TABLES pg_amendment_review WRITE"; //lock the table
			$db->query($lock_tables);
			
			//////copy comment/ review from supervisor/////
			$sql1 = "INSERT INTO pg_amendment_review 
			(id, pg_amendment_id, empid, pg_amend_detail_id, comment, comment_date, status, comment_status, insert_by, insert_date, confirm_status)
			VALUES
			('$reviewId', '$amendmentIdNew', '$user_id',  '$amendmentDetailId', '$comment', '$curdatetime', 'A', 'SUB', '$user_id', '$curdatetime', '$amendStat')";
			
			$dba->query($sql1); 
			
			$lock_tables="UNLOCK TABLES"; //lock the table
			$db->query($lock_tables);
			
		}while($dbb->next_record());
		/////////////////////////////////END//////////////////////////////////////////
		
		$save = "1";
		echo "<script>window.location = 'view_amendment_staff.php?tid=".$thesisId."&pid=".$proposalId."&save=".$save."&rid=".$referenceNo."&mn=".$matrixNo."&role=".$rolestatus."&mid=".$mid."';</script>";	
	}
	else {

		$sqlamend1 = "SELECT id, pg_amendment_id FROM pg_amendment_confirmation
		WHERE pg_amendment_id <> '$amendmentId'
		AND confirm_status = 'REQ'
		AND STATUS = 'A'
		ORDER BY pg_amendment_id DESC";

		$dbd3 = $dbd;
		$result_sqlamend1 = $dbd3->query($sqlamend1); 
		$dbd3->next_record();
		$confirmIdOld = $dbd3->f('id');	
		$pg_amendment_idOld = $dbd3->f('pg_amendment_id');	

		///////////Save old amendment detail & create new//////////////////////////////////
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
		
		do{
			$reviewId = "R".runnum2('id','pg_amendment_review');
			
			$id = $dbb->f('id');
			$amendment = $dbb->f('amendment');
			$feedbackByExaminer = $dbb->f('feedback_by_examiner');
			$amendment_detail_status = $dbb->f('amendment_detail_status');
			$comment=$dbb->f('comment');
			$commentIdArray=$dbb->f('commentId');
			$confirmStatusArray=$dbb->f('confirm_status');
			
			$sql13 = "SELECT a.confirm_status, a.comment, a.id AS commentId, b.id AS confirmID, b.confirm_status AS amendStat
			FROM pg_amendment_review a
			LEFT JOIN pg_amendment_confirmation b ON (b.pg_supervisor_empid = a.empid)
			WHERE pg_amend_detail_id = '$id'
			AND a.empid = '$user_id'
			AND a.comment_status = 'SAV' OR a.comment_status IS NULL
			AND a.status = 'A' OR a.status IS NULL";
			$db2 = $db;
			$result_sql13 = $db2->query($sql13); 
			$db2->next_record();
			$comment=$db2->f('comment');
			$commentId=$db2->f('commentId');
			$confirmStatus=$db2->f('confirm_status');
			$confirmID=$db2->f('confirmID');
			$amendStat = $dbb->f('amendStat');
			
			$lock_tables="LOCK TABLES pg_amendment_review WRITE"; //lock the table
			$db->query($lock_tables);
			
			//////copy comment/ review from supervisor/////
			$sql1 = "INSERT INTO pg_amendment_review 
			(id, pg_amendment_id, empid, pg_amend_detail_id, comment, comment_date, status, comment_status, insert_by, insert_date, confirm_status)
			VALUES
			('$reviewId', '$pg_amendment_idOld', '$user_id',  '$id', '$comment', '$curdatetime', 'A', 'SUB', '$user_id', '$curdatetime', '$amendStat')";
			
			$dba->query($sql1); 
			
			$lock_tables="UNLOCK TABLES"; //lock the table
			$db->query($lock_tables);
			
		}while($dbb->next_record());	
	
	}
		///////select date schedule//////////////////////////
		$sql3 = "SELECT id, defense_date, DATE_FORMAT(defense_date,'%d-%b-%Y') as viva_date, 
		DATE_FORMAT(defense_stime,'%h:%i%p') as viva_stime,
		DATE_FORMAT(defense_etime,'%h:%i%p') as viva_etime, venue, recomm_status, status
		FROM pg_calendar
		WHERE student_matrix_no = '$matrixNo'
		AND thesis_id = '$thesisId'
		/*AND recomm_status = 'REC'*/
		/*AND defense_date >= DATE_ADD(DATE_FORMAT(NOW(),'%Y-%m-%d'),INTERVAL ".$defenseDurationParam." DAY)*/
		AND status = 'A'
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
		
		$sqlemail = "SELECT name,email FROM new_employee
		WHERE `empid` = '$user_id'";
		$resultreceive = $dbk->query($sqlemail);
		$resultsqlreceive = $dbk->next_record(); 
		$superEmail = $dbk->f('email');
		$staffName = $dbk->f('name');
		
		$sqlemail = "SELECT email FROM student
		WHERE `matrix_no` = '$matrixNo'";
		$dbk1 = $dbk;
		$resultreceive = $dbk1->query($sqlemail);
		$resultsqlreceive = $dbk1->next_record(); 
		$studemail = $dbk1->f('email');
			
		///// message notification////
		$sqlmsg = "SELECT const_value FROM base_constant
		WHERE const_term = 'MESSAGE_SUP_TO_STU'";
		
		$result_sqlmsg = $dbg->query($sqlmsg); 
		$dbg->next_record();
		$row_cnt_msg = mysql_num_rows($result_sqlmsg);
		$msgConstValue = $dbg->f('const_value');
		
		if ($msgConstValue == 'Y')
		{
			include("../../../app/application/inbox/viva/review_amendment.php");
		}
		
		////email notification/////
		$sqlemail = "SELECT const_value FROM base_constant
		WHERE const_term = 'EMAIL_SUP_TO_STU'";
		$dbg1 = $dbg;
		$result_sqlemail = $dbg1->query($sqlemail); 
		$dbg1->next_record();
		$row_cnt_email = mysql_num_rows($result_sqlemail);
		$emailConstValue = $dbg1->f('const_value');
		
		if ($emailConstValue == 'Y')
		{
			include("../../../app/application/email/viva/email_review_amendment.php");
		}
		/*if(empty($confirmIdOld))
		{

						
			$sql1 = "INSERT INTO pg_amendment_confirmation
					(id, pg_amendment_id, confirm_status, pg_supervisor_empid, confirm_by, confirm_date, status, insert_by, insert_date, ref_req_no)
					VALUES
					('$conId', '$amendmentId', 'REQ',  '$user_id', '$user_id', '$curdatetime', 'A', '$user_id', '$curdatetime', '$reqChangesId')";

			$dba->query($sql1);
			
			$conId2 = "C".runnum2('id','pg_amendment_confirmation');
			
			$sql1 = "INSERT INTO pg_amendment_confirmation
					(id, pg_amendment_id, confirm_status, pg_supervisor_empid, confirm_by, confirm_date, status, insert_by, insert_date, ref_req_no)
					VALUES
					('$conId2', '$amendmentIdNew', 'REQ', '$user_id', '$user_id', '$curdatetime', 'A', '$user_id', '$curdatetime', '$reqChangesId')";
			$dba->query($sql1);
		
		}
		else {
			$conId2 = "C".runnum2('id','pg_amendment_confirmation');
			$sql1 = "INSERT INTO pg_amendment_confirmation
					(id, pg_amendment_id, confirm_status, pg_supervisor_empid, confirm_by, confirm_date, status, insert_by, insert_date, ref_req_no)
					VALUES
					('$conId2', '$pg_amendment_idOld', 'REQ',  '$user_id', '$user_id', '$curdatetime', 'A', '$user_id', '$curdatetime', '$reqChangesId')";
		
			$dba->query($sql1);
			
			$conId2 = "C".runnum2('id','pg_amendment_confirmation');

			$sql1 = "INSERT INTO pg_amendment_confirmation
					(id, pg_amendment_id, confirm_status, pg_supervisor_empid, confirm_by, confirm_date, status, insert_by, insert_date, ref_req_no)
					VALUES
					('$conId2', '$amendmentId', 'REQ', '$user_id', '$user_id', '$curdatetime', 'A', '$user_id', '$curdatetime', '$reqChangesId')";
			$dba->query($sql1);
		}*/

			
	
		
	//$msg[] = "<div class=\"info\"><span>The system will redirect to the previous page in 4 seconds.</span></div>";
	//$redirect = "1";


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
		if (empty($_POST['comment'][$val])) $msg[] = "<div class=\"error\"><span>Please provide comment for amendment no $no.</span></div>";
	}
	if(empty($msg))
	{
		if (sizeof($_POST['amendCheck'])>0) {
			while (list ($key,$val) = @each ($_POST['amendCheck'])) 
			{
				$reviewId = "R".runnum2('id','pg_amendment_review');
				$comment = $_POST['comment'][$val];
				$amendmendIdDetail = $_POST['amendmendIdDetail'][$val];
				$commentId = $_POST['commentId'][$val];
				
				if(empty($commentId))
				{		
					$sql1 = "INSERT INTO pg_amendment_review 
					(id, pg_amendment_id, empid, pg_amend_detail_id, comment, comment_date, status, comment_status, insert_by, insert_date)
					VALUES
					('$reviewId', '$amendmentId', '$user_id',  '$amendmendIdDetail', '$comment', '$curdatetime', 'A', 'SAV', '$user_id', '$curdatetime')";
					
					$dba->query($sql1); 
					
					
				}
				else{
				
					$sql1 = "UPDATE pg_amendment_review
					SET comment = '$comment', modify_by = '$user_id', modify_date = '$curdatetime'
					WHERE pg_amend_detail_id = '$amendmendIdDetail'
					AND empid = '$user_id'
					AND id = '$commentId'";
					
					$dba->query($sql1);
					
					//$msg[] = "<div class=\"success\"><span>Comment successfully updated.</span></div>";
				
				}
				
				//echo "$amendmentSave<br>"."$feedbackAdd<br>"."$amendmendIdDetail<br>"."$proposalId<br>"."$thesisId<br>";
			}
			$msg[] = "<div class=\"success\"><span>Comment successfully added.</span></div>";
		}
		else {
			$msg[] = "<div class=\"error\"><span>Please tick checkbox provided.</span></div>";
		}
	}

}

if(isset($_POST['btnConfirm']) && ($_POST['btnConfirm'] <> ""))
{
	$thesisId = $_REQUEST['thesisId'];
	$proposalId = $_REQUEST['proposalId'];
	$amendmentId = $_REQUEST['amendmentId'];
	
	$curdatetime = date("Y-m-d H:i:s");
	$pg_viva_id = $_REQUEST['pg_viva_id'];
	$check = $_REQUEST['amendCheck'];
	
	if(empty($msg))
	{
		if (sizeof($_POST['amendCheck'])>0) {
			while (list ($key,$val) = @each ($_POST['amendCheck'])) 
			{
				$comment = $_POST['comment'][$val];
				$amendmendIdDetail = $_POST['amendmendIdDetail'][$val];
				$commentId = $_POST['commentId'][$val];
				
				if($rolestatus == 'PRI')
				{
					$sql1 = "UPDATE pg_amendment_detail
					SET amendment_confirm_status = 'CON', modify_by = '$user_id', modify_date = '$curdatetime', confirm_by = '$user_id', confirm_date = '$curdatetime'
					WHERE id = '$amendmendIdDetail'
					AND pg_thesis_id = '$thesisId'";
					
					$dba->query($sql1);
				}
				else {
				
					$sql1 = "UPDATE pg_amendment_detail
					SET modify_by = '$user_id', modify_date = '$curdatetime'
					WHERE id = '$amendmendIdDetail'
					AND pg_thesis_id = '$thesisId'";
					
					$dba->query($sql1);
				
				}
			
				$sql4 = "SELECT * FROM pg_amendment_review
				WHERE id = '$commentId'
				AND pg_amend_detail_id = '$amendmendIdDetail'";
				
				$result_sql4 = $dbg->query($sql4); 
				$dbg->next_record();
				$row_cnt4 = mysql_num_rows($result_sql4);
				
				if ($row_cnt4 >0)
				{
					$sql1 = "UPDATE pg_amendment_review
					SET confirm_status = 'CON', modify_by = '$user_id', modify_date = '$curdatetime'
					WHERE pg_amend_detail_id = '$amendmendIdDetail'
					AND empid = '$user_id'
					AND comment_status = 'SAV'
					AND id = '$commentId'";
					
					$dba->query($sql1);
				}
				else {
					$reviewId = "R".runnum2('id','pg_amendment_review');
				
					$sql1 = "INSERT INTO pg_amendment_review 
					(id, pg_amendment_id, empid, pg_amend_detail_id, comment, comment_date, status, comment_status, insert_by, insert_date, confirm_status)
					VALUES
					('$reviewId', '$amendmentId', '$user_id',  '$amendmendIdDetail', '$comment', '$curdatetime', 'A', 'SAV', '$user_id', '$curdatetime', 'CON')";
					
					$dba->query($sql1); 				
				}
				
				$msg[] = "<div class=\"success\"><span>Amendment successfully confirmed.</span></div>";
				
				//echo "$amendmentSave<br>"."$feedbackAdd<br>"."$amendmendIdDetail<br>"."$proposalId<br>"."$thesisId<br>";
			}
		}
		else {
			$msg[] = "<div class=\"error\"><span>Please tick checkbox provided.</span></div>";
		}
	}

}


$sql_supervisor = " SELECT a.pg_employee_empid, a.ref_supervisor_type_id, d.description as supervisor_type, 
DATE_FORMAT(a.acceptance_date,'%d-%b-%Y') as acceptance_date, h.description as role_status_desc, g.thesis_title
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
$thesis_title = $db_klas2->f('thesis_title');


$sql4 = "SELECT a.id
FROM pg_calendar a
LEFT JOIN pg_viva b ON (b.pg_calendar_id = a.id)
WHERE a.student_matrix_no = '$matrixNo'
AND a.thesis_id = '$thesisId'
AND a.ref_session_type_id = 'VIV'
AND a.recomm_status = 'REC'
AND a.status = 'A'
AND b.pg_thesis_id = '$thesisId'
AND b.student_matrix_no = '$matrixNo'
AND b.status = 'A'
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
		var ask = window.confirm("Are you sure to confirm all the feedback by examiners report? \nClick OK to proceed or CANCEL to stay on the same page.");
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
					<td><label><?=$thesisId;?></label><input type="hidden" name="thesisTitle" id="thesisTitle" value="<?=$thesis_title; ?>"></td>
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
					<td><?=$sname?>
				    <input type="hidden" name="studentName" id="studentName" value="<?=$sname?>" /></td>
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
					<input type="hidden" name="scheId" id= "scheId" value="<?=$calendarIdViva?>" /></td>				
				</tr>   			
			</table>
			<br />

<? 
	$sqlamend1 = "SELECT id, confirm_status, pg_viva_id FROM pg_amendment
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
	$mainStatus= $dbf->f('confirm_status');
	$pg_viva_id= $dbf->f('pg_viva_id');
	


?>
<? // Call list of amendment
	
	$sqlamend = "SELECT a.id,a.amendment_by_examiner AS amendment, a.feedback_by_examiner, a.amendment_detail_status, 
	a.amendment_confirm_status As amendmentConfirmStatus
	FROM pg_amendment_detail a
	WHERE a.pg_amendment_id = '$amendmentId'
	AND a.pg_thesis_id = '$thesisId'
	AND a.student_matrix_no = '$matrixNo'
	AND a.amendment_detail_status = 'SUB' 
	/*AND a.confirm_by IS NULL
	AND a.confirm_date IS NULL*/
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
	
	do{
		$id = $dbb->f('id');
		$amendment = $dbb->f('amendment');
		$feedbackByExaminer = $dbb->f('feedback_by_examiner');
		$amendment_detail_status = $dbb->f('amendment_detail_status');
		$commentArray[$i]=$dbb->f('comment');
		$commentIdArray[$i]=$dbb->f('commentId');
		$confirmStatusArray[$i]=$dbb->f('confirm_status');
		$amendmentConfirmStatusArray[$i]=$dbb->f('amendmentConfirmStatus');
		
		$idArray[$i] = $id;
		$amendmentArray[$i] =$amendment;
		$feedbackByExaminerArray[$i] =$feedbackByExaminer;
		$amendment_detail_statusArray[$i] =$amendment_detail_status;
		
		$inc++;
		$i++;

	}while($dbb->next_record());
	
	$sql155 = "SELECT a.id,a.amendment_by_examiner AS amendment, a.feedback_by_examiner, a.amendment_detail_status, 
	a.amendment_confirm_status As amendmentConfirmStatus
	FROM pg_amendment_detail a
	WHERE a.pg_amendment_id = '$amendmentId'
	AND a.pg_thesis_id = '$thesisId'
	AND a.student_matrix_no = '$matrixNo'
	AND a.amendment_detail_status IN ('SUB', 'SAV') 
	/*AND a.confirm_by IS NULL
	AND a.confirm_date IS NULL*/
	AND a.status = 'A'
	AND a.amendment_confirm_status = 'CON'";
	$db5 = $db;
	$result_sql155 = $db5->query($sql155); 
	$db5->next_record();
	$row_cnt555 = mysql_num_rows($result_sql155);
?>
<style>
.thetable {
	 table-layout: fixed;
}
.thetable td {
	word-wrap: break-word;         /* All browsers since IE 5.5+ */
    overflow-wrap: break-word;     /* Renamed property in CSS3 draft spec */
    width: 80%;
}
</style>

	<fieldset><legend><strong>List of Amendment</strong></legend>
	<table width="90%" class="thetable" border="1">
		<tr>
			<th align="center" width="3%">Tick
		  <input type="hidden" name="amendmentId" id="amendmentId" value="<?=$amendmentId?>" />
		  <input type="hidden" name="pg_viva_id" id="pg_viva_id" value="<?=$pg_viva_id?>" /></th>
			<th align="center" width="3%">No</th>
			<th align="left" width="30%">Feedback of External Examiner</th>
			<? if($row_cnt555 >0) { ?>
			<!--<th align="left"width="37%">Amendmenst Based on the comment<br />
		  from External Examiner<br />(Please specify the page number)</th>-->
		  <? } ?>
			<th width="25%" align="left">Comment</th>
			<th width="11%">Status
			  <input type="hidden" name="totaldetail" id="totaldetail" value="<?=$inc?>" />
			<input type="hidden" name="amendStat" id="amendStat" value="<?=$amendStat?>" />		  </th>
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
				
				
			if (empty($comment)) {
				$comment =$_REQUEST['comment'][$i];
			} else {
			
			}	
			?>
			
			<tr>
				<td align="center"><input type="hidden" name="revConfirm" id="revConfirm" value="<?=$revConfirm?>" />
				<? if($amendmentConfirmStatusArray[$i] == 'CON') {?>
				<input type="checkbox" name="amendCheck[]" disabled="disabled" id="amendCheck" value="<?=$i?>"/>
				<? } else { ?>
				<input type="checkbox" name="amendCheck[]" id="amendCheck" value="<?=$i?>"/>
				<? } ?>
				<input type="hidden" name="amendmendIdDetail[]" id="amendmendIdDetail" value="<?=$idArray[$i]?>" />
				<input type="hidden" name="commentId[]" id="commentId" value="<?=$commentId?>" /></td>
				<td align="center"><?=$i+1?></td>
				<? if($amendmentConfirmStatusArray[$i] == 'CON') { ?>
				<!--<td align="left"><?=$amendmentArray[$i]?></td><!--<textarea cols="40" name="amendmentSave[]" id="amendmentSave"></textarea>-->
				<? } ?>
				<td align="left" width="30%"><p style="width:98%;"><?=$feedbackByExaminerArray[$i]?></p>
				<input type="hidden" name="statusA[]" id="statusA" value="<?=$amendment_detail_statusArray[$i]?>" />
				<input type="hidden" name="countDetail" id="countDetail" value="<?=$row_cnt5?>" /></td>
				<td>
				<? if($amendmentConfirmStatusArray[$i] == 'CON') {?>
				<textarea cols="40" name="comment[]" id="comment" readonly="readonly"><?=$comment?></textarea>
				<? } else { ?>
				<textarea cols="40" name="comment[]" id="comment"><?=$comment?></textarea>
				<? } ?>
				</td>
				<? if($confirmStatus == 'CON' && empty($amendmentConfirmStatusArray[$i])) { $confirmDesc = 'Confirmed';?>
				<td align="center"><?=$confirmDesc?></td>
				<? } else if (empty($confirmStatus) && empty($amendmentConfirmStatusArray[$i]) ) { $confirmDesc = 'Not Confirmed Yet';?>
				<td width="7%" align="center"><?=$confirmDesc?></td>
				<? } else if ($amendmentConfirmStatusArray[$i] == 'CON') { $confirmDesc = 'Confirmed by Main Supervisor';?>
				<td width="7%" align="center"><?=$confirmDesc?></td>
				<? } ?>
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
			<td><input type="submit" name="btnConfirm" id="btnConfirm" value="Confirm" /></td>
		</tr>
	</table>
<?
		$sqlUpload="SELECT * FROM file_upload_amendment
		WHERE amendment_id = '$amendmentId' 
		AND student_matrix_no = '$matrixNo'";			
		$dbh = $dbf;
		$result = $dbh->query($sqlUpload); 
		$dbh->next_record();
		$row_cnt_sqlUpload = mysql_num_rows($result);
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
			<td>
			<strong>Attachment by Student</strong>
			</td>
		</tr>
		<tr>
			<td>
			<? for ($i=0; $i<$inc; $i++)
			{ 
				if ($row_cnt_sqlUpload > 0) {
			?>
			
				<a href="downloadamend.php?id=<?=$fu_cdArray[$i];?>" target="_blank"><?=$namefileArray[$i] ?>
				<img src="../images/download.png" width="20" height="19" style="border:0px;" title="Download <?=$namefileArray[$i]?>"></a>
					
				<? 
					if($i%4 == 0 && $i !=0)
					{
					   echo "<br>";
					}  
				} else { 
					echo "<span style=\"margin-left: 10px;\">No Attachment Uploaded</div>";
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
		  <input type="button" name="btnBack" id="btnBack" value="Back" onClick="javascript:document.location.href='review_amendment.php';"/>	  
		  <input type="submit" name="btnReq" id="btnReq" value="Request Changes" onclick = ""/>
		  <!--<input type="submit" name="btnSubmit" id="btnSubmit" value="Submit" onclick = "return submitReport()"/>-->
		  <input type="submit" name="btnConfirmAll" id="btnConfirmAll" value="Confirm All" onclick = "return submitReport()"/></td>
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




