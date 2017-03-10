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
$roleType=$_GET['type'];
$matrixNo=$_GET['mn'];
$rolestatus=$_GET['role'];
$save=$_GET['save'];
$pd=$_GET['pd'];

$msg = array();

if(isset($save) || $save == '1')
{
	$msg[] = "<div class=\"success\"><span>VIVA report successfully saved.</span></div>";
}

$sqlstaffname = "SELECT name from new_employee where empid = '$user_id'";
$dbcstaffname = $dbc;
$result_sqlstaffname = $dbcstaffname->query($sqlstaffname); 
$dbcstaffname->next_record();
$staffName = $dbcstaffname->f('name');

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
	$evaVivaId = $_REQUEST['evaVivaId']; // VIVA ID
	//////Section A////////
	$overallStyle=$_REQUEST['overallStyle'];//hold section A question id	
	$addRating = $_REQUEST['addRating'];
	$comment = $_REQUEST['comment'];
	$vivaStyleId = $_REQUEST['vivaStyleId'];// hold section A id
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
	$sectionEid = $_REQUEST['sectionEid']; // hold section e question id
	$addoverall = $_REQUEST['addoverall']; // hold section e answer id
	$vivaOverallId = $_REQUEST['vivaOverallId'];//// hold section e id
	////end///////
	
	/////hold report status either it is first time or not/////
	$reportStatusSingle = $_REQUEST['reportStatusSingle'];
	////////////////////////////////////////////////////////////
	
	$curdatetime = date("Y-m-d H:i:s");
	$curdatetime1 = date("Y-m-d H:i:s");
	
	$lock_tables="LOCK TABLES pg_evaluation_viva_detail WRITE"; //lock the table
	$db->query($lock_tables);
	
	$sql7 = "UPDATE pg_evaluation_viva_detail
			SET major_revision = '$commentB', other_comment = '$commentC', comment_sec_a = '$commentA', comment_sec_e = '$commentE', 
			recommendation_id = '$recCheck', modify_by = '$user_id', modify_date = '$curdatetime', report_status = 'SAV'
			WHERE id = '$pd'
			AND status = 'A'
			AND pg_empid_viva = '$user_id'
			AND pg_eva_viva_id = '$evaVivaId'";
				
	$db->query($sql7);
	
	$lock_tables="UNLOCK TABLES"; //lock the table
	$db->query($lock_tables);
				
	$k = 1;
	if ($reportStatusSingle == 'SAV')
	{
		while (list ($key,$val) = @each ($overallStyle)) // content of section A
		{
			$pgVivaStyleNew = "S".runnum2('id','pg_evaluation_viva_style');
			
			$lock_tables="LOCK TABLES pg_evaluation_viva_style WRITE"; //lock the table
			$db->query($lock_tables);
			
			$sqlviva = "UPDATE pg_evaluation_viva_style
			SET ref_overall_rating_id = '$addRating[$key]', comments = '$comment[$key]', modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE id = '$vivaStyleId[$key]'
			AND ref_overall_style_id = '$overallStyle[$key]'
			AND status = 'A'
			AND pg_eva_viva_detail_id = '$pd'";
			
			$dba->query($sqlviva);
			
			$lock_tables="UNLOCK TABLES"; //lock the table
			$db->query($lock_tables);
			$k++;
		}
		$l = 1;
		while (list ($key,$val) = @each ($sectionEid)) // content of section E
		{
			
			/*$pgVivaStyleNew = "S".runnum2('id','pg_evaluation_viva_style');// content of section A
			echo $sectionEid[$key].": ".$addoverall[$key]." Comment:".$comment[$key];
			echo "<br>";*/
			$pgVivaOverallNew = "O".runnum2('id','pg_evaluation_viva_overall'); 
			
			$lock_tables="LOCK TABLES pg_evaluation_viva_overall WRITE"; //lock the table
			$db->query($lock_tables);
			
			$sqloverall = "UPDATE pg_evaluation_viva_overall
			SET ref_overall_rating_id = '$addoverall[$key]', modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE id = '$vivaOverallId[$key]'
			AND ref_overall_comment_id = '$sectionEid[$key]'
			AND status = 'A'
			AND pg_eva_viva_detail_id = '$pd'";
			
			$dba->query($sqloverall);
			
			$lock_tables="UNLOCK TABLES"; //lock the table
			$db->query($lock_tables);
			
			$l++;
		}
	}
	else {
	
		while (list ($key,$val) = @each ($overallStyle)) 
		{
			$pgVivaStyleNew = "S".runnum2('id','pg_evaluation_viva_style');// content of section A
			
			$lock_tables="LOCK TABLES pg_evaluation_viva_style WRITE"; //lock the table
			$db->query($lock_tables);
			
			$sqlviva = "INSERT INTO pg_evaluation_viva_style 
			(id, pg_eva_viva_detail_id, ref_overall_style_id, ref_overall_rating_id, insert_by, insert_date, status,seq, comments)
			VALUES
			('$pgVivaStyleNew', '$pd', '$overallStyle[$key]' , '$addRating[$key]' , '$user_id', '$curdatetime', 'A', '$k', '$comment[$key]')";
			
			$dba->query($sqlviva);
			
			$lock_tables="UNLOCK TABLES"; //lock the table
			$db->query($lock_tables);
			
			$k++;
		}
		while (list ($key,$val) = @each ($sectionEid)) 
		{
			/*$pgVivaStyleNew = "S".runnum2('id','pg_evaluation_viva_style');// content of section A
			echo $sectionEid[$key].": ".$addoverall[$key]." Comment:".$comment[$key];
			echo "<br>";*/
			$pgVivaOverallNew = "O".runnum2('id','pg_evaluation_viva_overall'); // content of section E
			
			$lock_tables="LOCK TABLES pg_evaluation_viva_overall WRITE"; //lock the table
			$db->query($lock_tables);
			
			$sqloverall = "INSERT INTO pg_evaluation_viva_overall 
			(id, pg_eva_viva_detail_id, status, insert_by, insert_date, ref_overall_comment_id, ref_overall_rating_id)
			VALUES
			('$pgVivaOverallNew', '$pd', 'A', '$user_id', '$curdatetime', '$sectionEid[$key]', '$addoverall[$key]')";
			
			$dba->query($sqloverall);
			
			$lock_tables="UNLOCK TABLES"; //lock the table
			$db->query($lock_tables);
			
			$k++;
		}	
	
	
	}
	
	
	
	$msg[] = "<div class=\"success\"><span>VIVA report successfully saved.</span></div>";
	
}

if(isset($_POST['btnSubmit']) && ($_POST['btnSubmit'] <> ""))
{
	$pgVivaIdNew = "V".runnum2('id','pg_evaluation_viva');//main hold thesis id and student matrix no;
	$refVivaNew = "R".runnum2('reference_no','pg_evaluation_viva');//reference for main
	$pgVivaDetailNew = "D".runnum2('id','pg_evaluation_viva_detail');//viva detail hold for every supervisor that submit report regarding thesis on main
																	//section dbce comment
	$evaVivaId = $_REQUEST['evaVivaId']; // VIVA ID
	//////Section A////////
	$overallStyle=$_REQUEST['overallStyle'];//hold section A question id	
	$addRating = $_REQUEST['addRating'];
	$comment = $_REQUEST['comment'];
	$vivaStyleId = $_REQUEST['vivaStyleId'];// hold section A id
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
	$sectionEid = $_REQUEST['sectionEid']; // hold section e question id
	$addoverall = $_REQUEST['addoverall']; // hold section e answer id
	$vivaOverallId = $_REQUEST['vivaOverallId'];//// hold section e id
	////end///////
	
	/////hold report status either it is first time or not/////
	$reportStatusSingle = $_REQUEST['reportStatusSingle'];
	////////////////////////////////////////////////////////////
	
	$curdatetime = date("Y-m-d H:i:s");
	$curdatetime1 = date("Y-m-d H:i:s");
	
	$supervisorIdArray = $_REQUEST['supervisorIdArray'];
	$studentName = $_REQUEST['studentName'];
	$matrixNo = $_REQUEST['matrixNo'];
	$calendarIdViva = $_REQUEST['calendarIdViva'];
	$thesisTitle = $_REQUEST['calendarIdViva'];
	
	if($roleType == 'EC') //////// chairman role
	{
	
		$lock_tables="LOCK TABLES pg_evaluation_viva WRITE"; //lock the table
		$db->query($lock_tables);
			
		$sql7 = "UPDATE pg_evaluation_viva SET respond_date = '$curdatetime', respond_by = '$user_id', 
		respond_status = 'SUB', result_status = '$recCheck', modify_by = '$user_id', modify_date = '$curdatetime', result_status_date = '$curdatetime'
		WHERE id = '$evaVivaId'
		AND pg_thesis_id = '$thesisId'
		AND student_matrix_no = '$matrixNo'
		AND STATUS = 'A'";
					
		$db->query($sql7);
		
		$lock_tables="UNLOCK TABLES"; //lock the table
		$db->query($lock_tables);
		
		
	
	}
	
	
	$lock_tables="LOCK TABLES pg_evaluation_viva_detail WRITE"; //lock the table
	$db->query($lock_tables);
	
	$sql7 = "UPDATE pg_evaluation_viva_detail
			SET major_revision = '$commentB', other_comment = '$commentC', comment_sec_a = '$commentA', comment_sec_e = '$commentE', report_status = 'SUB', 
			recommendation_id = '$recCheck', modify_by = '$user_id', modify_date = '$curdatetime', submit_date = '$curdatetime'
			WHERE id = '$pd'
			AND status = 'A'
			AND pg_empid_viva = '$user_id'
			AND pg_eva_viva_id = '$evaVivaId'";
				
	$db->query($sql7);
	
	$lock_tables="UNLOCK TABLES"; //lock the table
	$db->query($lock_tables);
		
	if ($reportStatusSingle == 'SAV')
	{
			
		$k = 1;
		while (list ($key,$val) = @each ($overallStyle)) // content of section A
		{
			$pgVivaStyleNew = "S".runnum2('id','pg_evaluation_viva_style');
			
			$lock_tables="LOCK TABLES pg_evaluation_viva_style WRITE"; //lock the table
			$db->query($lock_tables);
			
			$sqlviva = "UPDATE pg_evaluation_viva_style
			SET ref_overall_rating_id = '$addRating[$key]', comments = '$comment[$key]', modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE id = '$vivaStyleId[$key]'
			AND ref_overall_style_id = '$overallStyle[$key]'
			AND status = 'A'
			AND pg_eva_viva_detail_id = '$pd'";
			
			$dba->query($sqlviva);
			
			$lock_tables="UNLOCK TABLES"; //lock the table
			$db->query($lock_tables);
			$k++;
		}
		$l = 1;
		while (list ($key,$val) = @each ($sectionEid)) // content of section E
		{
			
			/*$pgVivaStyleNew = "S".runnum2('id','pg_evaluation_viva_style');// content of section A
			echo $sectionEid[$key].": ".$addoverall[$key]." Comment:".$comment[$key];
			echo "<br>";*/
			$pgVivaOverallNew = "O".runnum2('id','pg_evaluation_viva_overall'); 
			
			$lock_tables="LOCK TABLES pg_evaluation_viva_overall WRITE"; //lock the table
			$db->query($lock_tables);
			
			$sqloverall = "UPDATE pg_evaluation_viva_overall
			SET ref_overall_rating_id = '$addoverall[$key]', modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE id = '$vivaOverallId[$key]'
			AND ref_overall_comment_id = '$sectionEid[$key]'
			AND status = 'A'
			AND pg_eva_viva_detail_id = '$pd'";
			
			$dba->query($sqloverall);
			
			$lock_tables="UNLOCK TABLES"; //lock the table
			$db->query($lock_tables);
			$l++;
		}
	}
	else {
		$k = 1;
		while (list ($key,$val) = @each ($overallStyle)) 
		{
			$pgVivaStyleNew = "S".runnum2('id','pg_evaluation_viva_style');// content of section A
			
			$lock_tables="LOCK TABLES pg_evaluation_viva_style WRITE"; //lock the table
			$db->query($lock_tables);
			
			$sqlviva = "INSERT INTO pg_evaluation_viva_style 
			(id, pg_eva_viva_detail_id, ref_overall_style_id, ref_overall_rating_id, insert_by, insert_date, status,seq, comments)
			VALUES
			('$pgVivaStyleNew', '$pd', '$overallStyle[$key]' , '$addRating[$key]' , '$user_id', '$curdatetime', 'A', '$k', '$comment[$key]')";
			
			$dba->query($sqlviva);
			
			$lock_tables="UNLOCK TABLES"; //lock the table
			$db->query($lock_tables);
			$k++;
		}
		while (list ($key,$val) = @each ($sectionEid)) 
		{
			/*$pgVivaStyleNew = "S".runnum2('id','pg_evaluation_viva_style');// content of section A
			echo $sectionEid[$key].": ".$addoverall[$key]." Comment:".$comment[$key];
			echo "<br>";*/
			$pgVivaOverallNew = "O".runnum2('id','pg_evaluation_viva_overall'); // content of section E
			
			$lock_tables="LOCK TABLES pg_evaluation_viva_overall WRITE"; //lock the table
			$db->query($lock_tables);
			
			$sqloverall = "INSERT INTO pg_evaluation_viva_overall 
			(id, pg_eva_viva_detail_id, status, insert_by, insert_date, ref_overall_comment_id, ref_overall_rating_id)
			VALUES
			('$pgVivaOverallNew', '$pd', 'A', '$user_id', '$curdatetime', '$sectionEid[$key]', '$addoverall[$key]')";
			
			$dba->query($sqloverall);
			
			$lock_tables="UNLOCK TABLES"; //lock the table
			$db->query($lock_tables);
			$k++;
		}	
	
	}
	$save = "1";
	
	foreach ($supervisorIdArray as $empid) 
    {
		$sqlotherstaff = "SELECT name, email from new_employee where empid = '$empid'";
		$dbcotherstaff = $dbc;
		$result_sqlotherstaff = $dbcotherstaff->query($sqlotherstaff); 
		$dbcotherstaff->next_record();
		$staffOtherName = $dbcotherstaff->f('name');
		$staffOtherEmail = $dbcotherstaff->f('email');
		
		$sqlrec = "SELECT description from ref_recommendation where id = '$recCheck'";
		$dbrec = $dbg;
		$result_sqlrec = $dbrec->query($sqlrec); 
		$dbrec->next_record();
		$recDescription = $dbrec->f('description');
		
		$sqlemail = "SELECT email FROM new_employee
		WHERE `empid` = '$user_id'";
		$resultreceive = $dbk->query($sqlemail);
		$resultsqlreceive = $dbk->next_record(); 
		$superEmail = $dbk->f('email');
		
		$sql3 = "SELECT id, defense_date, DATE_FORMAT(defense_date,'%d-%b-%Y') as viva_date, 
		DATE_FORMAT(defense_stime,'%h:%i%p') as viva_stime,
		DATE_FORMAT(defense_etime,'%h:%i%p') as viva_etime, venue, recomm_status, status
		FROM pg_calendar
		WHERE student_matrix_no = '$matrixNo'
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
		
        $sqlmsg = "SELECT const_value FROM base_constant
		WHERE const_term = 'MESSAGE_SUP_TO_SUP'";
		
		$result_sqlmsg = $dbg->query($sqlmsg); 
		$dbg->next_record();
		$row_cnt_msg = mysql_num_rows($result_sqlmsg);
		$msgConstValue = $dbg->f('const_value');
		
		if ($msgConstValue == 'Y')
		{
			include("../../../app/application/inbox/viva/report_submit.php");
		}
		
		////email notification/////
		$sqlemail = "SELECT const_value FROM base_constant
		WHERE const_term = 'EMAIL_SUP_TO_SUP'";
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
			include("../../../app/application/email/viva/email_report_submit.php");
		}	
    }
	if($roleType == 'EC') //////// chairman role
	{
		$sqlmsg = "SELECT const_value FROM base_constant
		WHERE const_term = 'MESSAGE_SUP_TO_SCH'";
		
		$result_sqlmsg = $dbg->query($sqlmsg); 
		$dbg->next_record();
		$row_cnt_msg = mysql_num_rows($result_sqlmsg);
		$msgConstValue = $dbg->f('const_value');
		if($msgConstValue == 'Y')
		{
			include("../../../app/application/inbox/viva/report_submit_school.php");
		}
		
		$sqlemail = "SELECT const_value FROM base_constant
		WHERE const_term = 'EMAIL_SUP_TO_FAC'";
		$dbg1 = $dbg;
		$result_sqlemail = $dbg1->query($sqlemail); 
		$dbg1->next_record();
		$row_cnt_email = mysql_num_rows($result_sqlemail);
		$emailConstValue = $dbg1->f('const_value');
		if($emailConstValue == 'Y')
		{
			//echo "ec2";
			//include("../../../app/application/email/viva/email_report_submit_school.php");
		}
		
	}
		echo "<script>window.location = 'view_viva_report.php?tid=".$thesisId."&pd=".$pd."&save=".$save."&type=".$roleType."&mn=".$matrixNo."&role=".$rolestatus."';</script>";		
	$msg[] = "<div class=\"success\"><span>VIVA report successfully submitted.</span></div>";


}




$sqleva = " SELECT b.id AS evaVivaDetailId, b.major_revision, b.other_comment, b.recommendation_id, c.description AS recoDesc,
b.comment_sec_e AS commentSecE, b.comment_sec_a as commentSecA, a.id AS evaVivaId, a.id as evaVivaId, b.report_status, d.thesis_title
FROM pg_evaluation_viva a
LEFT JOIN pg_evaluation_viva_detail b ON (b.pg_eva_viva_id = a.id)
LEFT JOIN ref_recommendation c ON (c.id = b.recommendation_id)
LEFT JOIN pg_proposal d ON (d.pg_thesis_id = a.pg_thesis_id)
WHERE b.id = '$pd'
AND a.pg_thesis_id = '$thesisId'
AND a.student_matrix_no = '$matrixNo'
AND a.status = 'A'
AND b.status = 'A'
AND d.archived_status is null";

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
$thesis_title = $db_klas2->f('thesis_title');

///////////////////////list of evaluation panel for viva///////////////////////
$sql_supervisor = " SELECT a.pg_empid_viva, b.role_status, b.ref_supervisor_type_id, 
c.description AS roleDesc, a.report_status, DATE_FORMAT(a.submit_date,'%d-%b-%Y %h:%i:%s %p') as submit_date, a.id as otherDetailId
FROM pg_evaluation_viva_detail a
LEFT JOIN pg_supervisor b ON (b.pg_employee_empid = a.pg_empid_viva)
LEFT JOIN ref_supervisor_type c ON (c.id = b.ref_supervisor_type_id)
LEFT JOIN pg_evaluation_viva d ON (d.id = a.pg_eva_viva_id)
WHERE b.pg_thesis_id = '$thesisId'
AND a.pg_eva_viva_id = '$evaVivaId'
AND d.id = '$evaVivaId'
AND b.status = 'A'
AND a.pg_empid_viva <> '$user_id'";

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

$sql4 = "SELECT a.pg_calendar_id AS id 
FROM pg_viva a
LEFT JOIN pg_evaluation_viva c ON (c.pg_viva_id=a.id)
LEFT JOIN pg_calendar b ON (b.id=a.pg_calendar_id)
WHERE b.student_matrix_no = '$matrixNo' 
AND b.thesis_id = '$thesisId' 
AND b.ref_session_type_id = 'VIV' 
AND b.recomm_status = 'REC' 
AND b.status = 'A' 
AND b.archived_status IS NULL 
ORDER BY b.defense_date ASC ";


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
		var ask = window.confirm("Are you sure to submit VIVA report? \nClick OK to proceed or CANCEL to stay on the same page.");
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
					<td><?=$sname?><input type="hidden" name="studentName" id="studentName" value="<?=$sname?>" /></td>
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
					<td><?=$vivaDate?>, <?=$vivaSTime?> to <?=$vivaETime?>, <?=$venue?><input type="hidden" name="calendarIdViva" id="calendarIdViva" value="<?=$calendarIdViva?>" /></td>				
				</tr>   			
			</table>
			<br />
			<legend><strong>List of Evaluation Panel on VIVA </strong></legend>
			<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="80%" class="thetable">			
					<tr>
						<th width="5%">No</th>					
						<th width="15%">Role / Acceptance Date</th>
						<th width="15%" align="left">Staff ID</th>
						<th width="25%" align="left">Name</th>
						<th width="5%" align="left">Faculty</th>
						<th width="15%" align="left">Status</th>
						<th width="15%">Last Update</th>
						<th width="15%">Feedback</th>
					</tr>
					<input type="hidden" name="rowCntSupervisor" id="rowCntSupervisor" value="<?=$row_cnt_supervisor;?>"><?

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
								<?=$roleDesc?>
								<? if($ref_supervisor_type_id == 'SV'){
										if($role_status == 'PRI')
										{
											echo "- Main";
										}
									} 
								?>
								</td>
								<input type="hidden" name="confirmAcceptanceDate" id="confirmAcceptanceDate" value="<?=$confirmAcceptanceDate; ?>">
								<td align="left"><?=$employeeId;?></td>
								<td align="left"><?=$employeeName;?></td>
								<td align="left"><a href="javascript:void(0);" onMouseOver="toolTip('<?=$departmentName;?>', 300)" onMouseOut="toolTip()"><?=$departmentId;?></a></td>
								
								<?
								if ($report_status == '') {
								?>
									<td align="left"><label>Not Submitted Yet</label></td>
								<? }
								else if ($report_status == 'SUB') {
								?>
									<td align="left"><label><span style="color:#FF0000">Submitted</span></label></td>
								<? }?>
								<td align="left"><label><?=$submit_date;?></label></td>
								<td align="left">
								<? if($report_status == 'SUB') {?>
								<a href="view_viva_report_staff.php?tid=<?=$thesisId?>&type=<?=$ref_supervisor_type_id?>&mn=<?=$matrixNo?>&role=<?=$role_status?>&pd=<?=$otherDetailId?>&empid=<?=$employeeId?>&other=<?=$pd?>&othertype=<?=$roleType?>" name="thesisId" value="<?=$thesisId?>" title="View feedback"><img src="../images/view.jpg" width="45" height="30" style="border:0px;" title="View feedback"></a>
								<? } ?></td>
								
							</tr>
									
					<? 	} while($db_klas2->next_record());
						
					}
					else {
						?>
						<table>				
							<tr><td><span style="color:#FF0000">Notes</span>: <br/>No Supervisor/Co-Supervisor in the list.
										Possible Reasons:-<br/>
										1. Evaluation Panel is yet to be accept invitation<br/>
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
	<input type="hidden" id="reportStatusSingle" name="reportStatusSingle" value="<?=$reportStatusSingle?>" />
		<table class="thetable" width="90%" border="1">
			<tr>
				<th width="3%">No</td>
				<th width="47%" align="left">Description</td>
				<th width="10%" align="left">Rate</td>
			  <th width="40%" align="left">Comments</td>		  
			</tr>
		<? for ($iq=0; $iq<$inq; $iq++){ ?>
		<tr>

			<td width="3%" align="center"><?=$iq+1?><input type="hidden" name="overallStyle[]" id="overallStyle" value="<?=$idArray[$iq]?>" />
			<input type="hidden" name="vivaStyleId[]" id="vivaStyleId" value="<?=$vivaStyleId[$iq]?>" /></td>
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
			<?	if($ref_overall_rating_idA[$iq]==$idRateArray[$ir]){?>
				<? if(!empty($answerA[$iq])) { ?>
				<option selected="selected" value="<?=$ref_overall_rating_idA[$iq]?>"><?=$rateA[$iq]?>-<?=$answerA[$iq]?></option>
				<? } else { ?>
				<option selected="selected" value="<?=$ref_overall_rating_idA[$iq]?>"><?=$rateA[$iq]?></option>
				<? } ?>
			<? } else {?>
				<? if(!empty($descRateArray[$ir])) { ?>
				<option value="<?=$idRateArray[$ir]?>"><?=$rateArray[$ir]?>-<?=$descRateArray[$ir]?></option>
				<? } else { ?>
				<option value="<?=$idRateArray[$ir]?>"><?=$rateArray[$ir]?></option>
				<? } ?>
			<? } ?>
			<? } ?>
				</select></td>
			
			<td><textarea cols="40" name="comment[]" id="comment[]"><?=$comments[$iq]?></textarea></td>	
			
		</tr>

		<? }?>
		<tr>
			<td colspan="4"><span style="font-style:italic;"><strong>Other Comment</strong></span></td>
		</tr>
		<tr>
			<td colspan="4"><textarea cols="70" rows="4" name="commentA" id="commentA"><?=$commentSecA?></textarea></td>
		</tr>
	</table>

	</fieldset>
	<br />
	<fieldset><legend><strong>SECTION B: MAJOR REVISIONS REQUIRED <span style="font-style:italic;">(if any)</span></strong></legend>
	<table>
		<tr>
			<td><span style="font-style:italic;">Please use additional sheet if required</span></td>
		</tr>
		<tr>
			<td><textarea cols="70" rows="4" name="commentB" id="commentB"><?=$major_revision?></textarea></td>
		</tr>
	</table>
	
	
	</fieldset>
	<br />
	<fieldset><legend><strong>SECTION C: OTHER COMMENTS</strong></legend>
	<table>
		<tr>
			<td><span style="font-style:italic;">For example: Suitability for publication and award, if any. Please use additional sheet if required</span></td>
		</tr>
		<tr>
			<td><textarea cols="70" rows="4" name="commentC" id="commentC"><?=$other_comment?></textarea></td>
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
	<br />
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
			<input type="radio" name="recCheck" id="recCheck" checked="checked" value="<?=$idRecArray[$no]?>" required="required"/>
			<? } else { ?>
			<input type="radio" name="recCheck" id="recCheck" value="<?=$idRecArray[$no]?>" required="required"/>
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
	<br />
	<fieldset><legend><strong>SECTION E: OVERALL COMMENT ON STUDENT</strong></legend>
	<table>
		<tr>
			<td colspan="3"><strong>As a Examiner, how do you asses your student on the following grounds. Please rate how strongly you agree or disagree with the statement about the candidate on the following ground</strong></td>
		</tr>
	</table>
	<table class="thetable" border="1" width="70%">
		
		<tr>
			<th width="2%" align="center">No</td>
			<th width="58%" align="left">Components</td>
			<th width="15%" align="left">Ratings</td>		
		</tr>
		<? for ($no1=0; $no1<$noC; $no1++){ ?>
		<tr>
			<td align="center"><?=$no1+1?><input type="hidden" name="sectionEid[]" id="sectionEid" value="<?=$idOrArray[$no1]?>" />
			<input type="hidden" name="vivaOverallId[]" id="vivaOverallId" value="<?=$vivaOverallId[$no1]?>" /></td>
			<td><?=$descComArray[$no1]?></td>
			
			<td><select name="addoverall[]" id="addoverall">
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
			<td colspan="3"><strong>Other Comments</strong></td>
		</tr>
		<tr>
			<td colspan="3"><textarea cols="70" rows="5" name="commentE" id="commentE"><?=$commentSecE?></textarea></td>
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




