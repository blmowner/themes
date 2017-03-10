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
$referenceNo=$_GET['ref'];
$mid=$_GET['mid'];

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

if(isset($_POST['btnAdd']) && ($_POST['btnAdd'] <> ""))
{
	$feedbackEE = $_REQUEST['feedbackEE'];
	$amendment = $_REQUEST['amendment'];
	$thesisId = $_REQUEST['thesisId'];
	$proposalId = $_REQUEST['proposalId'];
	$amendmentId = $_REQUEST['amendmentId'];
	
	$amendmentDetailId = "A".runnum2('id','pg_amendment_detail');
	$amendmentIdNew = runnum('id','pg_amendment');	
	
	$curdatetime = date("Y-m-d H:i:s");

	//echo "$amendment<br>"."$feedbackEE<br>"."$thesisId<br>"."$proposalId<br>";
	$msg = array();
	if (empty($_REQUEST['feedbackEE'])) $msg[] = "<div class=\"error\"><span>Please provide Feedback of External Examiner.</span></div>";
	//if (empty($_REQUEST['amendment'])) $msg[] = "<div class=\"error\"><span>Please insert Amendments based on the comment from external examiner.</span></div>";
	
	if(empty($msg))
	{
		if(empty($amendmentId))
		{
			$reference_no = "R".runnum3('reference_no','pg_amendment');	
			
			$lock_tables="LOCK TABLES pg_amendment WRITE, pg_amendment_detail WRITE"; //lock the table
			$db->query($lock_tables);
			
			$sqlamend = "INSERT INTO pg_amendment
			(id, pg_thesis_id, reference_no, pg_proposal_id, status, amendment_status, student_matrix_no, insert_by, insert_date)
			VALUES
			('$amendmentIdNew', '$thesisId', '$reference_no', '$proposalId', 'A', 'SAV', '$user_id', '$user_id', '$curdatetime')";
			
			$dba->query($sqlamend);

			$sqlamendetail = "INSERT INTO pg_amendment_detail 
			(id, pg_amendment_id, amendment_by_examiner, feedback_by_examiner, status, insert_by, insert_date, pg_thesis_id, student_matrix_no, amendment_detail_status)
			VALUES
			('$amendmentDetailId', '$amendmentIdNew', '$amendment', '$feedbackEE', 'A', '$user_id', '$curdatetime', '$thesisId', '$user_id', 'SAV')";
			
			$dba->query($sqlamendetail);
			
			$msg[] = "<div class=\"success\"><span>Amendment successfully added.</span></div>";
			$referenceNo = $reference_no;
			
			$lock_tables="UNLOCK TABLES"; //lock the table
			$db->query($lock_tables);
			 
		}
		else
		{
			$lock_tables="LOCK TABLES pg_amendment_detail WRITE"; //lock the table
			$db->query($lock_tables);
			
			$sqlamendetail = "INSERT INTO pg_amendment_detail 
			(id, pg_amendment_id, amendment_by_examiner, feedback_by_examiner, status, insert_by, insert_date, pg_thesis_id, student_matrix_no, amendment_detail_status)
			VALUES
			('$amendmentDetailId', '$amendmentId', '$amendment', '$feedbackEE', 'A', '$user_id', '$curdatetime', '$thesisId', '$user_id', 'SAV')";
			
			$dba->query($sqlamendetail);
			
			$lock_tables="UNLOCK TABLES"; //lock the table
			$db->query($lock_tables);
			
			$msg[] = "<div class=\"success\"><span>Amendment successfully added.</span></div>";
		}
	}

}

if(isset($_POST['btnSubmit']) && ($_POST['btnSubmit'] <> ""))
{
	unset($redirect);

	$countDetail = $_REQUEST['countDetail'];
	$thesisId = $_REQUEST['thesisId'];
	$proposalId = $_REQUEST['proposalId'];
	$amendmentId = $_REQUEST['amendmentId'];
	$amendmentStatus = $_REQUEST['amendmentStatus'];
	$scheId = $_REQUEST['scheId'];
	$curdatetime = date("Y-m-d H:i:s");
	$curdatetime1 = date("Y-m-d H:i:s");
	$reqChangesId = "C".runnum2('ref_req_no','pg_amendment');
	$msg = array();
	
	$scheId = $_REQUEST['scheId'];
	$studentName = $_REQUEST['studentName'];
	$thesisTitle = $_REQUEST['thesisTitle'];
	$supervisorIdArray = $_REQUEST['supervisorIdArray'];
	
	if ($countDetail == 0) $msg[] = "<div class=\"error\"><span>Please insert amendment detail.</span></div>";
	
	if(empty($msg))
	{	
		if($amendmentStatus == '' || $amendmentStatus == 'SAV' || $amendmentStatus == 'SUB' || $amendmentStatus == 'REQ')
		{
			$lock_tables="LOCK TABLES pg_amendment WRITE, pg_amendment_detail WRITE, pg_amendment_confirmation WRITE"; //lock the table
			$db->query($lock_tables);
			
			$sql1 = "UPDATE pg_amendment SET
			amendment_status = 'SUB', 
			modify_by = '$user_id', modify_date = '$curdatetime',
			submit_date = '$curdatetime1',
			pg_calendar_id = '$scheId'
			WHERE id = '$amendmentId'
			AND pg_thesis_id = '$thesisId'
			AND pg_proposal_id = '$proposalId'
			AND student_matrix_no = '$user_id'
			AND STATUS = 'A'";
			
			$dba->query($sql1); 
			
			$sql1 = "UPDATE pg_amendment_detail SET
			amendment_detail_status = 'SUB', 
			modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE pg_amendment_id = '$amendmentId'
			AND pg_thesis_id = '$thesisId'
			AND student_matrix_no = '$user_id'
			AND STATUS = 'A'";
			
			$dba->query($sql1); 

			$sql1 = "UPDATE pg_amendment_confirmation SET
			confirm_status = 'CHA', 
			modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE pg_amendment_id = '$amendmentId'
			AND STATUS = 'A'";
			
			$dba->query($sql1); 
			
			$lock_tables="UNLOCK TABLES"; //lock the table
			$db->query($lock_tables);
			
			foreach($supervisorIdArray as $empid)
			{			
				///////select date schedule//////////////////////////
				$sql3 = "SELECT id, defense_date, DATE_FORMAT(defense_date,'%d-%b-%Y') as viva_date, 
				DATE_FORMAT(defense_stime,'%h:%i%p') as viva_stime,
				DATE_FORMAT(defense_etime,'%h:%i%p') as viva_etime, venue, recomm_status, status
				FROM pg_calendar
				WHERE student_matrix_no = '$user_id'
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
				
				$sqlemail = "SELECT email FROM new_employee
				WHERE `empid` = '$empid'";
				$resultreceive = $dbk->query($sqlemail);
				$resultsqlreceive = $dbk->next_record(); 
				$superEmail = $dbk->f('email');
				
				$sqlemail = "SELECT email FROM student
				WHERE `matrix_no` = '$user_id'";
				$dbk1 = $dbk;
				$resultreceive = $dbk1->query($sqlemail);
				$resultsqlreceive = $dbk1->next_record(); 
				$studemail = $dbk1->f('email');
						
				///// message notification////
				$sqlmsg = "SELECT const_value FROM base_constant
				WHERE const_term = 'MESSAGE_STU_TO_SUP'";
				
				$result_sqlmsg = $dbg->query($sqlmsg); 
				$dbg->next_record();
				$row_cnt_msg = mysql_num_rows($result_sqlmsg);
				$msgConstValue = $dbg->f('const_value');
				
				if ($msgConstValue == 'Y')
				{
					include("../../../app/application/inbox/viva/new_amendment.php");
				}
				
				////email notification/////
				$sqlemail = "SELECT const_value FROM base_constant
				WHERE const_term = 'EMAIL_STU_TO_SUP'";
				$dbg1 = $dbg;
				$result_sqlemail = $dbg1->query($sqlemail); 
				$dbg1->next_record();
				$row_cnt_email = mysql_num_rows($result_sqlemail);
				$emailConstValue = $dbg1->f('const_value');
				
				////notify from supervisor//////////
				$sqlemail1 = "SELECT email_status FROM pg_employee
				WHERE staff_id = '$empid'
				AND STATUS = 'A'";
				$dbg2 = $dbg;
				$result_sqlemail1 = $dbg2->query($sqlemail1); 
				$dbg2->next_record();
				$row_cnt_email1 = mysql_num_rows($result_sqlemail1);
				$emailConstValue1 = $dbg2->f('email_status');
				
				if ($emailConstValue == 'Y' && $emailConstValue1 == 'Y')
				{
					include("../../../app/application/email/viva/email_new_amendment.php");
				}
			}

			
			$save = "1";
			echo "<script>window.location = 'view_amendment.php?tid=".$thesisId."&pid=".$proposalId."&save=".$save."&ref=".$referenceNo."';</script>";	

			/*$msg[] = "<div class=\"success\"><span>Amendment successfully submitted.</span></div>";
			$msg[] = "<div class=\"info\"><span>The system will redirect to the previous page in 4 seconds.</span></div>";
			$redirect = "1";*/
		}
		/*else if($amendmentStatus == 'SUB')
		{
			///////////////////////////////save previous amendment for tracking///////////////////////////
			$sql1 = "UPDATE pg_amendment SET
			amendment_status = 'SUB', 
			modify_by = '$user_id', modify_date = '$curdatetime',
			submit_date = '$curdatetime1', ref_req_no = '$reqChangesId'
			WHERE id = '$amendmentId'
			AND pg_thesis_id = '$thesisId'
			AND pg_proposal_id = '$proposalId'
			AND student_matrix_no = '$user_id'
			AND STATUS = 'A'";
			
			$dba->query($sql1); 
			
			
			//////////////////////////////////////select amend detail////////////////////////////////////
			$sqlamend = "SELECT id,amendment_by_examiner AS amendment, feedback_by_examiner, amendment_detail_status
			FROM pg_amendment_detail
			WHERE pg_amendment_id = '$amendmentId'
			AND pg_thesis_id = '$thesisId'
			AND student_matrix_no = '$user_id'
			AND amendment_detail_status in ('SAV', 'SUB')
			AND confirm_by IS NULL
			AND confirm_date IS NULL";
			
			$result_sqlamend = $dbb->query($sqlamend); 
			$dbb->next_record();
			$row_cnt5 = mysql_num_rows($result_sqlamend);
			$i= 0;
			$inc= 0;
			$amendmentArray = array();
			$feedbackByExaminerArray = array();
			$idArray = array();
			$amendment_detail_statusArray = array();
			
			do{
				$id = $dbb->f('id');
				$amendment = $dbb->f('amendment');
				$feedbackByExaminer = $dbb->f('feedback_by_examiner');
				$amendment_detail_status = $dbb->f('amendment_detail_status');
		
				$idArray[$i] = $id;
				$amendmentArray[$i] =$amendment;
				$feedbackByExaminerArray[$i] =$feedbackByExaminer;
				$amendment_detail_statusArray[$i] =$amendment_detail_status;
				$inc++;
				$i++;
		
			}while($dbb->next_record());
			

			$sqlamendetail = "INSERT INTO pg_amendment_detail 
			(id, pg_amendment_id, amendment_by_examiner, feedback_by_examiner, status, insert_by, insert_date, pg_thesis_id, student_matrix_no, amendment_detail_status)
			VALUES
			('$amendmentDetailId', '$amendmentIdNew', '$amendment', '$feedbackEE', 'A', '$user_id', '$curdatetime', '$thesisId', '$user_id', 'SAV')";
			
			$dba->query($sqlamendetail);
			
			////////////////////////////Insert amend detail/////////////////////////////////////////
			$reference_no = "R".runnum3('reference_no','pg_amendment');	
			$amendmentDetailId = "A".runnum2('id','pg_amendment_detail');
			$amendmentIdNew = runnum('id','pg_amendment');	

			$sqlamend = "INSERT INTO pg_amendment
			(id, pg_thesis_id, reference_no, pg_proposal_id, status, amendment_status, student_matrix_no, insert_by, insert_date)
			VALUES
			('$amendmentIdNew', '$thesisId', '$reference_no', '$proposalId', 'A', 'SUB', '$user_id', '$user_id', '$curdatetime')";
			
			$dba->query($sqlamend);

			$msg[] = "<div class=\"success\"><span>Amendment successfully submitted.</span></div>";
			$msg[] = "<div class=\"info\"><span>The system will redirect to the previous page in 4 seconds.</span></div>";
			$redirect = "1";
		
		
		}*/
	}

}

if(isset($_POST['btnUpdate']) && ($_POST['btnUpdate'] <> ""))
{
	$thesisId = $_REQUEST['thesisId'];
	$proposalId = $_REQUEST['proposalId'];
	$amendmentId = $_REQUEST['amendmentId'];
	$amendmentDetailId = "A".runnum2('id','pg_amendment_detail');
	$amendmentIdNew = runnum('id','pg_amendment');		
	$curdatetime = date("Y-m-d H:i:s");
	
	$check = $_REQUEST['amendCheck'];
	
	while (list ($key,$val) = @each ($check)) 
	{
		$no=1+$val;
		//if (empty($_POST['amendmentSave'][$val])) $msg[] = "<div class=\"error\"><span>Please provide amendment for record no $no.</span></div>";
		if (empty($_REQUEST['feedbackAdd'][$val])) $msg[] = "<div class=\"error\"><span>Please provide feedback of external examiner for record no $no.</span></div>";
	}
	if (empty($msg)) {
		if (sizeof($_REQUEST['amendCheck'])>0) {
			while (list ($key,$val) = @each ($_REQUEST['amendCheck'])) 
			{
				$amendmentSave = $_REQUEST['amendmentSave'][$val];
				$feedbackAdd = $_REQUEST['feedbackAdd'][$val];
				$amendmendIdDetail = $_REQUEST['amendmendIdDetail'][$val];
				//echo $val;
				//exit();
				$lock_tables="LOCK TABLES pg_amendment_detail WRITE"; //lock the table
				$db->query($lock_tables);
				
				$sql1 = "UPDATE pg_amendment_detail
				SET amendment_by_examiner = '$amendmentSave', 
				feedback_by_examiner = '$feedbackAdd',
				modify_by = '$user_id', modify_date = '$curdatetime'
				WHERE id = '$amendmendIdDetail'
				AND student_matrix_no = '$user_id'
				AND STATUS = 'A'
				AND amendment_detail_status IN ('SAV', 'SUB')";
				
				$dba->query($sql1); 
				
				$lock_tables="UNLOCK TABLES"; //lock the table
				$db->query($lock_tables);
				
				$msg[] = "<div class=\"success\"><span>Amendment updated successfully.</span></div>";
				//echo "$amendmentSave<br>"."$feedbackAdd<br>"."$amendmendIdDetail<br>"."$proposalId<br>"."$thesisId<br>";
			}
		}
	}


}
if(isset($_POST['btnDelete']) && ($_POST['btnDelete'] <> ""))
{
	$thesisId = $_REQUEST['thesisId'];
	$proposalId = $_REQUEST['proposalId'];
	$amendmentId = $_REQUEST['amendmentId'];
	$curdatetime = date("Y-m-d H:i:s");
	
	$confirmStat = $_REQUEST['confirmStat'];
	$check = $_REQUEST['amendCheck'];
	
	while (list ($key,$val) = @each ($check)) 
	{
		$no=1+$val;
		//if (empty($_POST['amendmentSave'][$val])) $msg[] = "<div class=\"error\"><span>Please provide amendment for record no $no.</span></div>";
		//if (empty($_POST['feedbackAdd'][$val])) $msg[] = "<div class=\"error\"><span>Please provide feedback of external examiner for record no $no.</span></div>";
		if (!empty($_POST['confirmStat'][$val])) $msg[] = "<div class=\"error\"><span>Amendment for record no $no has been confirm by Main Supervisor and cannot be deleted.</span></div>";
	}
	
	if (sizeof($_POST['amendCheck'])>0) {
		while (list ($key,$val) = @each ($_POST['amendCheck'])) 
		{
			$amendmentSave = $_POST['amendmentSave'][$val];
			$feedbackAdd = $_POST['feedbackAdd'][$val];
			$amendmendIdDetail = $_POST['amendmendIdDetail'][$val];
			$statusA = $_POST['statusA'][$val];
			
			$lock_tables="LOCK TABLES pg_amendment_detail WRITE"; //lock the table
			$db->query($lock_tables);
			
			if($statusA == 'REQ' || $statusA == 'SUB') {
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
			
			$lock_tables="UNLOCK TABLES"; //lock the table
			$db->query($lock_tables);
			
			
			$msg[] = "<div class=\"success\"><span>Amendment deleted successfully</span></div>";
			//echo "$amendmentSave<br>"."$feedbackAdd<br>"."$amendmendIdDetail<br>"."$proposalId<br>"."$thesisId<br>";
		}
	}


}

$sql_supervisor = " SELECT a.pg_employee_empid, a.ref_supervisor_type_id, d.description as supervisor_type, 
DATE_FORMAT(a.acceptance_date,'%d-%b-%Y') as acceptance_date, h.description as role_status_desc, a.role_status, g.thesis_title
FROM pg_supervisor a 
LEFT JOIN ref_supervisor_type d ON (d.id = a.ref_supervisor_type_id) 
LEFT JOIN pg_thesis f ON (f.student_matrix_no = a.pg_student_matrix_no)
LEFT JOIN pg_proposal g ON (g.pg_thesis_id = f.id)
LEFT JOIN ref_role_status h ON (h.id = a.role_status)
WHERE a.pg_student_matrix_no='$user_id'
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

$sql_amend_id = " SELECT pg_viva_id from pg_amendment where id = '$mid' and status = 'A'";
$db_amend = $db;
$result_sql_amend_id = $db_amend->query($sql_amend_id); //echo $sql;
$db_amend->next_record();
$row_cnt_amend_id = mysql_num_rows($result_sql_amend_id);
$pg_viva_id = $db_amend->f('pg_viva_id');


$sql4 = "SELECT a.id
FROM pg_calendar a
LEFT JOIN pg_viva b ON (b.pg_calendar_id = a.id)
WHERE a.student_matrix_no = '$user_id'
AND a.thesis_id = '$thesisId'
AND a.ref_session_type_id = 'VIV'
AND a.recomm_status = 'REC'
AND a.status = 'A'
AND b.pg_thesis_id = '$thesisId'
AND b.student_matrix_no = '$user_id'
AND b.submit_status = 'CON'
AND b.id = '$pg_viva_id'
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
		<META http-equiv="refresh" content="4;URL=list_amendment.php"> 
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
function newAttachment(pid, tid, ppid, rn, mid) {
   
		document.location.href = "../viva/amendment_edit_attachment.php?pid=" + pid +"&tid="+tid+"&ppid="+ppid+"&ref="+rn+"&mid="+mid;

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
					<td><label><?=$thesisId;?>
					    <input type="hidden" name="thesisTitle" id="thesisTitle" value="<?=$thesis_title; ?>" />
					</label></td>
					<input type="hidden" name="id" id="id" value="<?=$id; ?>">
					<input type="hidden" name="thesisId" id="thesisId" value="<?=$thesisId; ?>">
					<input type="hidden" name="proposalId" id="proposalId" value="<?=$proposalId; ?>">
				</tr>
				<tr>
					<td>Student Matrix No</td>
					<td>:</td>
					<td><?=$user_id?></td>
				</tr>
					<?
					$sql1 = "SELECT name AS student_name
					FROM student
					WHERE matrix_no = '$user_id'";
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
				    <input type="hidden" name="studentName" id="studentName" value="<?=$sname; ?>" /></td>
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
					WHERE student_matrix_no = '$user_id'
					AND thesis_id = '$thesisId'
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
					<td><input type="hidden" name="scheId" id="scheId" value="<?=$calendarIdViva?>" />
					<?=$vivaDate?>, <?=$vivaSTime?> to <?=$vivaETime?>, <?=$venue?></td>				
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
						<!--<th width="5%">View Feedback</th>-->

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
							$role_status = $db_klas2->f('role_status');

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
								$sql12 = "SELECT confirm_status
								FROM pg_amendment
								WHERE id = '$mid'
								AND amendment_status = 'REQ'
								AND status = 'A'";
								
								$dbg2 = $dbg;
								$result12 = $dbg2->query($sql12); 
								$dbg2->next_record();
								$row_cnt12 = mysql_num_rows($result12);
								
								if ($row_cnt12>0) {
								?>
								
								<!--<td><a href="view_amendment_comment.php?tid=<?=$thesisId?>&pid=<?=$proposalId?>&rid=<?=$referenceNo?>&mn=<?=$user_id?>&role=<?=$role_status?>&empid=<?=$employeeId?>" name="thesisId" value="<?=$thesisId?>" title="View feedback"><img src="../images/view.jpg" width="45" height="30" style="border:0px;" title="View feedback"></a></td>-->	
								<? } else {?>
									<!--<td></td>-->	
								<? } ?>
								
									
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
	$sqlamend1 = "SELECT id, amendment_status FROM pg_amendment
	WHERE pg_thesis_id = '$thesisId'
	AND pg_proposal_id = '$proposalId'
	AND reference_no = '$referenceNo'
	AND confirm_status IS NULL
	AND STATUS = 'A'
	AND confirm_by IS NULL
	AND student_matrix_no = '$user_id'
	AND amendment_status IN ('REQ','SAV', 'SUB')
	AND ref_req_no IS NULL";
		
	$result_sqlamend1 = $dbf->query($sqlamend1); 
	$dbf->next_record();
	$amendmentId = $dbf->f('id');
	$amendmentStatus = $dbf->f('amendment_status');


?>
	<fieldset><legend><strong>New Amendment</strong></legend>

		<table>
			<tr>
				<td>Feedback of External Examiner</td>
				<td>:</td>
				<td><textarea name="feedbackEE" id="feedbackEE" rows="3" cols="50"></textarea>
				<input type="hidden" name="amendmentId" id="amendmentId" value="<?=$amendmentId?>" />
				<input type="hidden" name="referenceNo" id="referenceNo" value="<?=$referenceNo?>" />
				<input type="hidden" name="amendmentStatus" id="amendmentStatus" value="<?=$amendmentStatus?>" /></td>
			</tr>
			<!--<tr>
				<td>Amendmenst Based on the comment<br />from External Examiner<br />(Please specify the page number)</td>
				<td>:</td>
				<td><textarea name="amendment" id="amendment" rows="3" cols="50"></textarea></td>			
			</tr>-->
			<tr>
				<td colspan="3"><input type="submit" name="btnAdd" id="btnAdd" value= "Add" /></td>
			</tr>
		</table>
	</fieldset>
<? // Call list of amendment
	
	$sqlamend = "SELECT id,amendment_by_examiner AS amendment, feedback_by_examiner, 
	amendment_detail_status, amendment_confirm_status
	FROM pg_amendment_detail
	WHERE pg_amendment_id = '$amendmentId'
	AND pg_thesis_id = '$thesisId'
	AND student_matrix_no = '$user_id'
	AND amendment_detail_status in ('SAV', 'SUB')
	/*AND confirm_by IS NULL
	AND confirm_date IS NULL*/";
	
	$result_sqlamend = $dbb->query($sqlamend); 
	$dbb->next_record();
	$row_cnt5 = mysql_num_rows($result_sqlamend);
	$i= 0;
	$inc= 0;
	$amendmentArray = array();
	$feedbackByExaminerArray = array();
	$idArray = array();
	$amendment_detail_statusArray = array();
	$amendment_confirm_status = array();
	
	do{
		$id = $dbb->f('id');
		$amendment = $dbb->f('amendment');
		$feedbackByExaminer = $dbb->f('feedback_by_examiner');
		$amendment_detail_status = $dbb->f('amendment_detail_status');
		$amendment_confirm_status[$i] = $dbb->f('amendment_confirm_status');

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
	<table width="85%" class="thetable" border="1">
		<tr>
			<th align="center" width="5%">Tick</th>
			<th align="center" width="5%">No</th>
			<th align="left" width="46%">Feedback of External Examiner</th>
			<th align="left" width="51%">Comment by Supervisor</th>
			<th align="center" width="12%">Amendment Status</th>
			
			<!--<th align="left"width="40%">Amendmenst Based on the comment<br />from External Examiner<br />(Please specify the page number)</th>-->
		</tr>
		<? if($row_cnt5>0) {?>
			<? for ($i=0; $i<$inc; $i++){ 
			$sql13 = "SELECT a.confirm_status, a.comment, a.id AS commentId, b.id AS confirmID, b.confirm_status AS amendStat
			FROM pg_amendment_review a
			LEFT JOIN pg_amendment_confirmation b ON (b.pg_supervisor_empid = a.empid)
			WHERE pg_amend_detail_id = '$idArray[$i]'
			AND a.comment_status = 'SUB' OR a.comment_status IS NULL
			AND a.status = 'A' OR a.status IS NULL";
			$db2 = $db;
			$result_sql13 = $db2->query($sql13); 
			$db2->next_record();
			$comment=$db2->f('comment');
			$commentId=$db2->f('commentId');
			$confirmStatus=$db2->f('confirm_status');
			$confirmID=$db2->f('confirmID');
			$amendStat = $db2->f('amendStat');
			
			?>
			<tr>
				<td align="center">
				<? if($amendment_confirm_status[$i] == 'CON') { ?>
				<input type="checkbox" name="amendCheck[]" disabled="disabled" id="amendCheck" value="<?=$i?>"/>
				<? } else { ?>
				<input type="checkbox" name="amendCheck[]" id="amendCheck" value="<?=$i?>"/>
				<? } ?>
				<input type="hidden" name="amendmendIdDetail[]" id="amendmendIdDetail" value="<?=$idArray[$i]?>" /></td>
				<td align="center"><?=$i+1?></td>
				<td align="left">
				<? if($amendment_confirm_status[$i] == 'CON') { ?>
				<textarea cols="40" name="feedbackAdd[]" id="feedbackAdd" readonly="readonly"><?=$feedbackByExaminerArray[$i]?></textarea>
				<? } else { ?>
				<textarea cols="40" name="feedbackAdd[]" id="feedbackAdd"><?=$feedbackByExaminerArray[$i]?></textarea>
				<? } ?>
				</td>
				<td align="left"><p style="width:98%;"><?=$comment?></p></td>
				<td align="center"><input type="hidden" name="confirmStat[]" id="confirmStat" value="<?=$amendment_confirm_status[$i]?>"/>
				<? 
				if ($amendment_confirm_status[$i] == 'CON') {
					echo "Confirm by Main Supervisor";
				}
				else {
				
				}
				?>
				</td>
				<!--<td align="left"><textarea cols="40" name="feedbackAdd[]" id="feedbackAdd"><?=$amendmentArray[$i]?></textarea>
				</td>--><input type="hidden" name="statusA[]" id="statusA" value="<?=$amendment_detail_statusArray[$i]?>" />
				<input type="hidden" name="countDetail" id="countDetail" value="<?=$row_cnt5?>" />
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
			<td><input type="submit" name="btnDelete" id="btnDelete" value="Delete" onclick="return deleteReport()"/></td>
		</tr>
	</table>
<?
$sqlUpload="SELECT COUNT(*) as total  FROM file_upload_amendment
		WHERE amendment_id = '$amendmentId' 
		AND student_matrix_no = '$user_id'";			
		$dbh = $dbf;
		$result = $dbh->query($sqlUpload); 
		$dbh->next_record();
		$attachment = $dbh->f('total');
		
		if($attachment == '0')
		{
			$a = '';
		}
		else
		{
			$a = "(".$attachment.")";
		}

?>
	<table>
		<tr>
			<td><button type="button" name="btnAttachment" value="Attachment" onClick="return newAttachment('<?=$amendmentId?>', '<?=$thesisId?>', '<?=$proposalId?>', '<?=$referenceNo?>', '<?=$mid?>')" >
			Attachment <FONT COLOR="#FF0000"><sup><?=$a?></sup></FONT></button></td>
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
		  <input type="button" name="btnBack" id="btnBack" value="Back" onClick="javascript:document.location.href='list_amendment.php';"/>
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




