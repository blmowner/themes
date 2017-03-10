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
		if($role == 'PRI')
		{
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
			
			$msg[] = "<div class=\"success\"><span>Amendment successfully submitted.</span></div>";
			$msg[] = "<div class=\"info\"><span>The system will redirect to the previous page in 4 seconds.</span></div>";
			$redirect = "1";
			
		}
		else {
			
			$commentId = $_POST['commentId'];
			for ($i=0; $i<$inc; $i++){
			
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
			}
			$sql1 = "INSERT INTO pg_amendment_confirmation
				(id, pg_amendment_id, confirm_status, pg_supervisor_empid, confirm_by, confirm_date, status, insert_by, insert_date)
				VALUES
				('$conId', '$amendmentId', 'CON',  '$user_id', '$user_id', '$curdatetime', 'A', '$user_id', '$curdatetime')";
					
				$dba->query($sql1); 
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
		///////save old amendment for tracking & create new amendment but same reference no////////////////////////
		$sqlamend = "UPDATE pg_amendment
					SET modify_by = '$user_id', modify_date = '$curdatetime', ref_req_no = '$reqChangesId'
					WHERE id = '$amendmentId'
					AND status = 'A'";
		$dba->query($sqlamend);
	
		$sqlamend = "INSERT INTO pg_amendment
		(id, pg_thesis_id, reference_no, pg_proposal_id, status, amendment_status, student_matrix_no, insert_by, insert_date)
		VALUES
		('$amendmentIdNew', '$thesisId', '$referenceNo', '$proposalId', 'A', 'SUB', '$matrixNo', '$user_id', '$curdatetime')";
		$dba->query($sqlamend);
		//////////////////////////////////////END////////////////////////////////////////////////////////////////////////
		
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
			$amendmentDetailId = "A".runnum2('id','pg_amendment_detail');
			$reviewId = "R".runnum2('id','pg_amendment_review');
			
			$id = $dbb->f('id');
			$amendment = $dbb->f('amendment');
			$feedbackByExaminer = $dbb->f('feedback_by_examiner');
			$amendment_detail_status = $dbb->f('amendment_detail_status');
			$comment=$dbb->f('comment');
			$commentIdArray=$dbb->f('commentId');
			$confirmStatusArray=$dbb->f('confirm_status');
			
			//////copy amendment detail
			$sqlamendetail = "INSERT INTO pg_amendment_detail 
			(id, pg_amendment_id, amendment_by_examiner, feedback_by_examiner, status, insert_by, insert_date, pg_thesis_id, student_matrix_no, amendment_detail_status)
			VALUES
			('$amendmentDetailId', '$amendmentIdNew', '$amendment', '$feedbackByExaminer', 'A', '$user_id', '$curdatetime', '$thesisId', '$matrixNo', 'SUB')";
				
			$dba->query($sqlamendetail);
			
			
			
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
	
			//////copy comment/ review from supervisor/////
			$sql1 = "INSERT INTO pg_amendment_review 
			(id, pg_amendment_id, empid, pg_amend_detail_id, comment, comment_date, status, comment_status, insert_by, insert_date)
			VALUES
			('$reviewId', '$amendmentIdNew', '$user_id',  '$amendmentDetailId', '$comment', '$curdatetime', 'A', 'SUB', '$user_id', '$curdatetime')";
			
			$dba->query($sql1); 
			
		}while($dbb->next_record());
		/////////////////////////////////END//////////////////////////////////////////
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
	
			//////copy comment/ review from supervisor/////
			$sql1 = "INSERT INTO pg_amendment_review 
			(id, pg_amendment_id, empid, pg_amend_detail_id, comment, comment_date, status, comment_status, insert_by, insert_date)
			VALUES
			('$reviewId', '$pg_amendment_idOld', '$user_id',  '$id', '$comment', '$curdatetime', 'A', 'SUB', '$user_id', '$curdatetime')";
			
			$dba->query($sql1); 
			
		}while($dbb->next_record());	
	
	}
	
		if(empty($confirmIdOld))
		{

			/*$sql13 = "SELECT * from pg_amendment_confirmation 
			where pg_amendment_id = '$amendmentId' 
			and pg_supervisor_empid = '$user_id' 
			and ";
			$db2 = $db;
			$result_sql13 = $db2->query($sql13); 
			$db2->next_record();
			$comment=$db2->f('comment');*/
						
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
		}

			
	
		
	$msg[] = "<div class=\"info\"><span>The system will redirect to the previous page in 4 seconds.</span></div>";
	$redirect = "1";


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
					
					$msg[] = "<div class=\"success\"><span>Comment successfully added.</span></div>";
				}
				else{
					$sql1 = "UPDATE pg_amendment_review
					SET comment = '$comment', modify_by = '$user_id', modify_date = '$curdatetime'
					WHERE pg_amend_detail_id = '$amendmendIdDetail'
					AND empid = '$user_id'
					AND comment_status = 'SAV'
					AND id = '$commentId'";
					
					$dba->query($sql1);
					
					$msg[] = "<div class=\"success\"><span>Comment successfully updated.</span></div>";
				
				}
				//echo "$amendmentSave<br>"."$feedbackAdd<br>"."$amendmendIdDetail<br>"."$proposalId<br>"."$thesisId<br>";
			}
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
	$reviewId = "R".runnum2('id','pg_amendment_review');
	$curdatetime = date("Y-m-d H:i:s");
	
	$check = $_REQUEST['amendCheck'];
	
	if(empty($msg))
	{
		if (sizeof($_POST['amendCheck'])>0) {
			while (list ($key,$val) = @each ($_POST['amendCheck'])) 
			{
				$comment = $_POST['comment'][$val];
				$amendmendIdDetail = $_POST['amendmendIdDetail'][$val];
				$commentId = $_POST['commentId'][$val];
				
				$sql1 = "UPDATE pg_amendment_detail
				SET modify_by = '$user_id', modify_date = '$curdatetime'
				WHERE id = '$amendmendIdDetail'
				AND pg_thesis_id = '$thesisId'";
				
				$dba->query($sql1);
			
				$sql4 = "SELECT *FROM pg_amendment_review
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


if(isset($_POST['btnDelete']) && ($_POST['btnDelete'] <> ""))
{
	$thesisId = $_REQUEST['thesisId'];
	$proposalId = $_REQUEST['proposalId'];
	$amendmentId = $_REQUEST['amendmentId'];
	$curdatetime = date("Y-m-d H:i:s");
	
	$check = $_REQUEST['amendCheck'];
	
	while (list ($key,$val) = @each ($check)) 
	{
		$no=1+$val;
		if (empty($_POST['amendmentSave'][$val])) $msg[] = "<div class=\"error\"><span>Please provide amendment for record no $no.</span></div>";
		if (empty($_POST['feedbackAdd'][$val])) $msg[] = "<div class=\"error\"><span>Please provide feedback of external examiner for record no $no.</span></div>";
	}
	
	if (sizeof($_POST['amendCheck'])>0) {
		while (list ($key,$val) = @each ($_POST['amendCheck'])) 
		{
			$amendmentSave = $_POST['amendmentSave'][$val];
			$feedbackAdd = $_POST['feedbackAdd'][$val];
			$amendmendIdDetail = $_POST['amendmendIdDetail'][$val];
			$statusA = $_POST['statusA'][$val];
			
			if($statusA == 'REC' || $statusA == 'SUB') {
				$sql1 = "UPDATE pg_amendment_detail
				SET status = 'A', amendment_detail_status = 'DEL'
				modify_by = '$user_id', modify_date = '$curdatetime'
				WHERE id = '$amendmendIdDetail'
				AND student_matrix_no = '$user_id'
				AND amendment_detail_status = 'SAV'";
				
				$dba->query($sql1);
			}
			else if($statusA == 'SAV') {
			
				$sql1 = "DELETE from pg_amendment_detail
				WHERE id = '$amendmendIdDetail'
				AND student_matrix_no = '$user_id'";
				
				$dba->query($sql1);				
			}
			
			
			$msg[] = "<div class=\"success\"><span>Amendment deleted successfully</span></div>";
			//echo "$amendmentSave<br>"."$feedbackAdd<br>"."$amendmendIdDetail<br>"."$proposalId<br>"."$thesisId<br>";
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
	
?>


	<fieldset><legend><strong>List of Amendment</strong></legend>
	<table width="95%" class="thetable" border="1">
		<tr>
			<th align="center" width="3%">Tick<input type="hidden" name="amendmentId" id="amendmentId" value="<?=$amendmentId?>" /></th>
			<th align="center" width="4%">No</th>
			<th align="left" width="24%">Feedback of External Examiner</th>
			<th align="left"width="38%">Amendmenst Based on the comment<br />
			from External Examiner<br />(Please specify the page number)</th>
			<th width="31%" align="left">Comment</th>
			<th>Status<input type="hidden" name="totaldetail" id="totaldetail" value="<?=$inc?>" />
			<input type="hidden" name="amendStat" id="amendStat" value="<?=$amendStat?>" />
			</th>
		</tr>
		<? if($row_cnt5>0) {?>
			<? for ($i=0; $i<$inc; $i++){ 
			
			$sql13 = "SELECT a.confirm_status, a.comment, a.id AS commentId, b.id AS confirmID, a.confirm_status AS revConfirm, b.confirm_status AS amendStat
			FROM pg_amendment_review a
			LEFT JOIN pg_amendment_confirmation b ON (b.pg_supervisor_empid = a.empid)
			WHERE pg_amend_detail_id = '$idArray[$i]'
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
			$amendStat = $db2->f('amendStat');
			$revConfirm = $db2->f('revConfirm');
				
			?>
			
			<tr>
				<td align="center"><input type="hidden" name="revConfirm" id="revConfirm" value="<?=$revConfirm?>" />
				<input type="checkbox" name="amendCheck[]" id="amendCheck" value="<?=$i?>"/>
				<input type="hidden" name="amendmendIdDetail[]" id="amendmendIdDetail" value="<?=$idArray[$i]?>" />
				<input type="hidden" name="commentId[]" id="commentId" value="<?=$commentId?>" /></td>
				<td align="center"><?=$i+1?></td>
				<td align="left"><?=$amendmentArray[$i]?></td><!--<textarea cols="40" name="amendmentSave[]" id="amendmentSave"></textarea>-->
				<td align="left"><?=$feedbackByExaminerArray[$i]?><!--<textarea cols="40" name="feedbackAdd[]" id="feedbackAdd"></textarea>-->
				<input type="hidden" name="statusA[]" id="statusA" value="<?=$amendment_detail_statusArray[$i]?>" />
				<input type="hidden" name="countDetail" id="countDetail" value="<?=$row_cnt5?>" /></td>
				<td><textarea cols="40" name="comment[]" id="comment"><?=$comment?></textarea></td>
				<? if($confirmStatus == 'CON') { $confirmDesc = 'Confirmed';?>
				<td align="center"><?=$confirmDesc?></td>
				<? } else if (empty($confirmStatus)) { $confirmDesc = '';?>
				<td align="center"><?=$confirmDesc?></td>
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
	</fieldset>
	<table>
		<tr>
			<td colspan="2"><label><span style="color:#FF0000">Notes:</span><br/>
					1. Submit button is for submit amendments on thesis to supervisor for review.</label></td>
		</tr>
		<tr>
		  <td colspan="2">
		  <input type="button" name="btnBack" id="btnBack" value="Back" onClick="javascript:document.location.href='review_amendment.php';"/>
		  <input type="submit" name="btnSubmit" id="btnSubmit" value="Submit" onclick = "return submitReport()"/>
		  <input type="submit" name="btnReq" id="btnReq" value="Request Changes" onclick = ""/></td>
			
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




