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
		var ask = window.confirm("Are you sure to submit Thesis for VIVA? \nClick OK to proceed or CANCEL to stay on the same page.");
		if (ask) 
		{
			//document.location.href = "../monthlyreport/new_defense.php?pid=" + pid + "&tid=" + tid + "&id=" + id;
			return true;
		}
		return false;
	}

	</script>
	
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
$pgThesisId=$_REQUEST['tid'];
$proposalId=$_GET['pid'];


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

}

if(isset($_POST['btnSubmit']) && ($_POST['btnSubmit'] <> ""))
{
	$vivaId = $_REQUEST['vivaId'];
	$pgThesisId=$_REQUEST['tid'];
	$proposalId=$_GET['pid'];
	$curdatetime = date("Y-m-d H:i:s");
	$totalA = $_REQUEST['totalA'];
	$supervisorIdArray =$_REQUEST['supervisorIdArray'];
	$vivaNewId = "V".runnum2('id','pg_viva');
	$recSche = $_REQUEST['recSche'];
	$thesisTitle = $_REQUEST['thesisTitle'];
	$studentName = $_REQUEST['studentName'];
	
	if($totalA > 0)
	{
		$lock_tables="LOCK TABLES pg_viva WRITE"; //lock the table
		$db->query($lock_tables);
		
		$sql7 = "INSERT INTO pg_viva
			(id, pg_thesis_id, student_matrix_no, insert_by, insert_date, status, submit_date, submit_status, pg_proposal_id, pg_calendar_id)
			VALUES
			('$vivaNewId','$pgThesisId','$user_id','$user_id','$curdatetime','A','$curdatetime', 'SUB', '$proposalId', '$recSche')";
		$db->query($sql7);
		
		$lock_tables="UNLOCK TABLES"; //lock the table
		$db->query($lock_tables);
		
		/*$sql7 = "UPDATE pg_viva
				SET insert_by = '$user_id', insert_date = '$curdatetime', modify_by = '$user_id', modify_date = '$curdatetime', submit_status = 'SUB'
				, submit_date = '$curdatetime'
				WHERE id = '$vivaId'
				AND pg_thesis_id = '$pgThesisId'
				AND status = 'A'
				AND student_matrix_no = '$user_id'
				AND pg_proposal_id = '$proposalId'";
						
		$db->query($sql7);*/
		
		foreach($supervisorIdArray as $empid)
		{
			/*$sql4 = "SELECT respond_status, id from pg_viva_detail
			WHERE viva_empid = '$empid'
			AND status = 'A'";
			
			$result_sql4 = $dbg->query($sql4); 
			$dbg->next_record();
			$row_cnt4 = mysql_num_rows($result_sql4);
			$respond_status = $dbg->f('respond_status');	
			$pgvivadetailId = $dbg->f('id');	
			if($respond_status != 'CON')
			{*/
				$vivadetailIdNew = "D".runnum2('id','pg_viva_detail');
				
				$lock_tables="LOCK TABLES pg_viva_detail WRITE, file_upload_viva WRITE"; //lock the table
				$db->query($lock_tables);
				
				$sql7 = "INSERT INTO pg_viva_detail
				(id, pg_viva_id, viva_empid, insert_by, insert_date, STATUS, respond_status)
				VALUES
				('$vivadetailIdNew','$vivaNewId','$empid','$user_id','$curdatetime','A', 'SUB')";
				$db->query($sql7);
					
				/*$sql7 = "UPDATE pg_viva_detail
				SET modify_by = '$user_id', modify_date = '$curdatetime', status = 'ARC'
				WHERE pg_viva_id = '$vivaId'
				AND viva_empid= '$empid'
				AND respond_status <> 'CON'";
						
				$db->query($sql7);*/
				
				$sql7 = "UPDATE file_upload_viva
				SET pg_viva_id = '$vivaNewId'
				WHERE insert_by= '$user_id'
				AND student_matrix_no = '$user_id'
				AND (pg_viva_id is null OR pg_viva_id = '')";
						
				$db->query($sql7);
				
				$lock_tables="UNLOCK TABLES"; //lock the table
				$db->query($lock_tables);
				
				///////select date schedule//////////////////////////
				$sql3 = "SELECT id, defense_date, DATE_FORMAT(defense_date,'%d-%b-%Y') as viva_date, 
				DATE_FORMAT(defense_stime,'%h:%i%p') as viva_stime,
				DATE_FORMAT(defense_etime,'%h:%i%p') as viva_etime, venue, recomm_status, status
				FROM pg_calendar
				WHERE student_matrix_no = '$user_id'
				AND thesis_id = '$pgThesisId'
				/*AND recomm_status = 'REC'*/
				/*AND defense_date >= DATE_ADD(DATE_FORMAT(NOW(),'%Y-%m-%d'),INTERVAL ".$defenseDurationParam." DAY)*/
				AND status = 'A'
				AND id = '$recSche'
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
				$superEmail = $dbk->f('email'); ///email supervisor
				
				$sqlemail = "SELECT email FROM student
				WHERE `matrix_no` = '$user_id'";
				$dbk1 = $dbk;
				$resultreceive = $dbk1->query($sqlemail);
				$resultsqlreceive = $dbk1->next_record(); 
				$studemail = $dbk1->f('email'); ///email student
						
				///// message notification////
				$sqlmsg = "SELECT const_value FROM base_constant
				WHERE const_term = 'MESSAGE_STU_TO_SUP'";
				
				$result_sqlmsg = $dbg->query($sqlmsg); 
				$dbg->next_record();
				$row_cnt_msg = mysql_num_rows($result_sqlmsg);
				$msgConstValue = $dbg->f('const_value');
				
				if ($msgConstValue == 'Y')
				{
					include("../../../app/application/inbox/viva/thesis_submission.php");
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
				
				//echo "constant: $emailConstValue<br>$sqlemail<br>status: $emailConstValue1<br>$sqlemail1";
				if ($emailConstValue == 'Y' && $emailConstValue1 == 'Y')
				{
					//echo "TEST";
					include("../../../app/application/email/viva/email_thesis_submision.php");
				}
					
			//}
		}
		
		

	$save = "1";
		echo "<script>window.location = 'view_viva.php?tid=".$thesisId."&pi=d".$proposalId."&save=".$save."&vid=".$vivaNewId."';</script>";	
	}
	else {
		
		$msg[] = "<div class=\"error\"><span>Plese upload thesis by attachment below.</span></div>";
		
	}	
}

if(isset($_POST['btnSave']) && ($_POST['btnSave'] <> ""))
{
	$vivaId = $_REQUEST['vivaId'];
	$pgThesisId=$_REQUEST['tid'];
	$proposalId=$_GET['pid'];
	$curdatetime = date("Y-m-d H:i:s");
	$vivaNewId = "V".runnum2('id','pg_viva');
	$recSche = $_REQUEST['recSche'];
	
	
	$lock_tables="LOCK TABLES pg_viva WRITE"; //lock the table
	$db->query($lock_tables);
	
	$sql7 = "INSERT INTO pg_viva
			(id, pg_thesis_id, student_matrix_no, insert_by, insert_date, status, submit_status, pg_proposal_id, pg_calendar_id)
			VALUES
			('$vivaNewId','$pgThesisId','$user_id','$user_id','$curdatetime','A','SAV', '$proposalId', '$recSche')";
	$db->query($sql7);
	
	$lock_tables="UNLOCK TABLES"; //lock the table
	$db->query($lock_tables);
	/*$sql7 = "UPDATE pg_viva
			SET insert_by = '$user_id', insert_date = '$curdatetime', modify_by = '$user_id', modify_date = '$curdatetime', submit_status = 'SAV'
			WHERE id = '$vivaId'
			AND pg_thesis_id = '$pgThesisId'
			AND status = 'A'
			AND student_matrix_no = '$user_id'
			AND pg_proposal_id = '$proposalId'";*/
		foreach($supervisorIdArray as $empid)
		{

				$vivadetailIdNew = "D".runnum2('id','pg_viva_detail');
				
				$lock_tables="LOCK TABLES pg_viva_detail WRITE, file_upload_viva WRITE"; //lock the table
				$db->query($lock_tables);
				
				$sql7 = "INSERT INTO pg_viva_detail
				(id, pg_viva_id, viva_empid, insert_by, insert_date, STATUS)
				VALUES
				('$vivadetailIdNew','$vivaNewId','$empid','$user_id','$curdatetime','SAV1')";
				$db->query($sql7);

				
				$sql7 = "UPDATE file_upload_viva
				SET pg_viva_id = '$vivaNewId', pg_viva_detail_id = '$vivadetailIdNew'
				WHERE insert_by= '$user_id'
				AND student_matrix_no = '$user_id'
				AND (pg_viva_id is null OR pg_viva_id = '')";
						
				$db->query($sql7);
				
				$lock_tables="UNLOCK TABLES"; //lock the table
				$db->query($lock_tables);
				
			//}
		}
					
	$save = "1";
		echo "<script>window.location = 'edit_viva_save.php?tid=".$pgThesisId."&pid=".$proposalId."&save=".$save."&vid=".$vivaNewId."';</script>";	
	$msg[] = "<div class=\"success\"><span>Thesis save successfully.</span></div>";
}

$thesisId = $pgThesisId;

$sql1 = "SELECT pt.id AS thesis_id, pt.student_matrix_no,pt.status AS thesis_status,
pp.id AS proposal_id, pp.thesis_title,pp.thesis_type, pp.objective, pp.introduction,pp.description,pp.discussion_status, 
DATE_FORMAT(pp.verified_date,'%d-%b-%Y') AS verified_date, pp.verified_remarks, pp.verified_by,
pp.verified_status AS proposal_status,pp.endorsed_by, DATE_FORMAT(pp.endorsed_date,'%d-%b-%Y') AS endorsed_date, 
pp.endorsed_remarks, pp.status AS endorsed_status, 
rps.description AS proposal_description, rps2.description AS endorsed_desc, 
DATE_FORMAT(pp.report_date,'%d-%b-%Y') AS report_date,
DATE_FORMAT(pp.cancel_requested_date,'%d-%b-%Y') AS cancel_requested_date,
DATE_FORMAT(pp.cancel_approved_date,'%d-%b-%Y') AS cancel_approved_date, 
pp.cancel_approved_by, pp.cancel_approved_remarks
FROM pg_thesis pt 
LEFT JOIN pg_proposal pp ON (pp.pg_thesis_id=pt.id) 
LEFT JOIN ref_proposal_status rps ON (rps.id = pp.verified_status) 
LEFT JOIN ref_proposal_status rps2 ON (rps2.id = pp.status) 
LEFT JOIN pg_work_evaluation pwe ON (pwe.pg_thesis_id = pt.id)
LEFT JOIN pg_work pw ON (pw.id = pwe.pg_work_id)
WHERE pt.student_matrix_no = '$user_id'
AND pp.verified_status in ('APP','AWC')				
AND pp.archived_status is null
AND pt.ref_thesis_status_id_proposal in ('APP','AWC','APC')
AND pwe.ref_work_marks_id IS NOT NULL 
AND ((pwe.status = 'APP' AND pwe.ref_work_marks_id IN ('SAT','SUB')) 
OR (pwe.status = 'DIS' AND pwe.ref_work_marks_id = 'SUB' AND pwe.proposed_marks_id = 'SAT') 
OR (pwe.status = 'DIS' AND pwe.ref_work_marks_id = 'SAT' AND pwe.proposed_marks_id = 'SUB')
OR (pwe.status = 'DIS' AND pwe.ref_work_marks_id = 'NSA' AND pwe.proposed_marks_id IN ('SAT','SUB')))
AND pw.archived_status IS NULL 
AND pwe.student_matrix_no = '$user_id'
AND pt.id = '$thesisId'
AND pp.id = '$proposalId'
ORDER BY pt.id";

$result1 = $db->query($sql1); 
$db->next_record();
$thesisId=$db->f('thesis_id');
$proposalId=$db->f('proposal_id');
$thesisTitle=$db->f('thesis_title');
$reportDate=$db->f('report_date');
$endorsedDate=$db->f('endorsed_date');
$row_cnt1 = mysql_num_rows($result1);



$sql_supervisor = " SELECT a.pg_employee_empid, a.ref_supervisor_type_id, d.description as supervisor_type, 
DATE_FORMAT(a.acceptance_date,'%d-%b-%Y') as acceptance_date, h.description as role_status_desc
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
ORDER BY d.seq, a.ref_supervisor_type_id";

$result_sql_supervisor = $db_klas2->query($sql_supervisor); //echo $sql;
$db_klas2->next_record();
$row_cnt_supervisor = mysql_num_rows($result_sql_supervisor);


$sql4 = "SELECT a.id FROM pg_calendar a 
WHERE a.student_matrix_no = '$user_id' 
AND a.thesis_id = '$thesisId' 
AND a.ref_session_type_id = 'VIV' 
AND a.recomm_status = 'REC' 
AND a.status = 'A'
AND (DATE_FORMAT(a.defense_date,'%Y-%m-%d') > DATE_FORMAT(NOW(),'%Y-%m-%d')) 
AND id NOT IN (
SELECT b.pg_calendar_id
FROM pg_viva b
WHERE b.pg_thesis_id = '$thesisId'
AND b.student_matrix_no = '$user_id'
AND b.status <> 'CON')
ORDER BY defense_date ASC ";


$result_sql4 = $dbg->query($sql4); 
$dbg->next_record();
$row_cnt4 = mysql_num_rows($result_sql4);
$ir = 0;
$inr = 0;
$calendarIdViva = array();
do {
	$calendarIdViva[$ir] = $dbg->f('id');
	$ir++;
	$inr++;
} while($dbg->next_record());


/*$sql_viva = "SELECT id AS pgVivaId, viva_status, submit_status, submit_date, session_no 
FROM pg_viva
WHERE pg_thesis_id = '$pgThesisId'
AND pg_proposal_id = '$proposalId'
AND student_matrix_no = '$user_id'
AND viva_status IS NULL
AND submit_status IN ('SAV') OR submit_status IS NULL
AND STATUS = 'A'";
				
$result_sql_viva = $dba->query($sql_viva); //echo $sql;
$dba->next_record();
$row_cnt_viva = mysql_num_rows($result_sql_viva);

							
$pgVivaId = $dba->f('pgVivaId');*/
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
	function newReport() 
	{
		var ask = window.confirm("Are you sure to submit another Monthly Defense Report? \nClick OK to proceed or CANCEL to stay on the same page.");
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
function newAttachment(vid,tid,pid) {
   // var ask = window.confirm("Ensure your viva has been saved before proceed or otherwise the last change will be discarded.\nClick OK to proceed or CANCEL to stay on the same page.");
   // if (ask) {
		document.location.href = "../viva/viva_attachment.php?vid=" + vid + "&tid=" +tid+"&pid=" +pid;

    //}
}

</script>
	
	
	
	<form id="form1" name="form1" method="post" enctype="multipart/form-data">	
	<?php
    if(!empty($msg)) 
	{
        foreach($msg as $err) 
		{
            echo $err;
        }
    }
	?>
	<? if($row_cnt1 > 0) { 
			if($row_cnt4 > 0) { ?>
			<table border="0"> 
				<tr>
				<td><h3><strong>VIVA  Details </strong><h3></td>
				</tr>
			</table>
			<table width="75%">
				<tr>
					<td width="16%">Thesis / Project ID</td>
					<td width="2%">:</td>
				  <td width="82%"><label><?=$thesisId;?></label></td>
					<input type="hidden" name="id" id="id" value="<?=$id; ?>">
					<input type="hidden" name="thesisId" id="thesisId" value="<?=$thesisId; ?>">
					<input type="hidden" name="proposalId" id="proposalId" value="<?=$proposalId; ?>">
					<input type="hidden" name="vivaId" id="vivaId" value="<?=$pgVivaId; ?>">
				</tr>
				<tr>
					<td>Student Matrix No</td>
					<td>:</td>
					<td><?=$user_id?><input type="hidden" name="thesisTitle" id="thesisTitle" value="<?=$thesisTitle; ?>"></td>
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
					<td><?=$sname?><input type="hidden" name="studentName" id="studentName" value="<?=$sname; ?>"></td>
				</tr> 
				<?$jscript3 = "";?>
				<tr>
					<td>Evaluation Schedule</td>
					<td>:</td>				
					<td><select name="recSche" id="recSche">
					<? for ($ir=0; $ir<$inr; $ir++){ 
						$sql3 = "SELECT id, defense_date, DATE_FORMAT(defense_date,'%d-%b-%Y') as viva_date, 
						DATE_FORMAT(defense_stime,'%h:%i%p') as viva_stime,
						DATE_FORMAT(defense_etime,'%h:%i%p') as viva_etime, venue, recomm_status, status
						FROM pg_calendar
						WHERE student_matrix_no = '$user_id'
						AND thesis_id = '$thesisId'
						/*AND recomm_status = 'REC'*/
						/*AND defense_date >= DATE_ADD(DATE_FORMAT(NOW(),'%Y-%m-%d'),INTERVAL ".$defenseDurationParam." DAY)*/
						AND status = 'A'
						AND id = '$calendarIdViva[$ir]'
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
						<option value="<?=$recommendedId?>"><?=$vivaDate?>, <?=$vivaSTime?> to <?=$vivaETime?>, <?=$venue?></option>
					<? } ?>
				</select>
					</td>				
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
						<!--<th width="15%">Last Update</th>-->
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
								
							
									<td align="left"><label>Expecting on Thesis for VIVA </label></td>
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
	/*$sqlex = "SELECT a.pg_employee_empid, b.description
	FROM pg_supervisor a
	LEFT JOIN ref_supervisor_type b ON (b.id = a.ref_supervisor_type_id)
	WHERE a.pg_student_matrix_no = '$user_id'
	AND a.ref_supervisor_type_id IN ('EE','IE')
	AND a.status = 'A'";
	
	$result_sqlex = $dbb->query($sqlex); 
	$dbb->next_record();
	$row_cntex = mysql_num_rows($result_sqlex);
	$i= 0;
	$inc= 0;
	$exEmpid = array();
	$exDesc = array();
	
	do{
		$exEmpid[$i] = $dbb->f('pg_employee_empid');
		$exDesc[$i] = $dbb->f('description');

		$inc++;
		$i++;

	}while($dbb->next_record());


?>
	<fieldset><legend><strong>List of Examiner</strong></legend>
	<table class="thetable" width="60%">
		<tr>
			<th width="5%">No</th>
			<th width="30%">Name/Staff ID</th>
			<th width="17%">Role</th>
			<th width="48%">Faculty</th>
		</tr>
		<? for ($i=0; $i<$inc; $i++){ ?>
		<? 
			$sql_employee="SELECT  b.name, c.id, c.description
			FROM new_employee b 
			LEFT JOIN dept_unit c ON (c.id = b.unit_id) 
			WHERE b.empid= '$exEmpid[$i]'";
			$dbc1 = $dbc;
			$result_sql_employee = $dbc1->query($sql_employee);
			$dbc1->next_record();
			
			$exName = $dbc1->f('name');
			$exDescFac = $dbc1->f('description');
		
		
		?>
		<tr>
			<td><?=$i+1?>.</td>
			<td><?=$exName?> (<?=$exEmpid[$i]?>)</td>
			<td><?=$exDesc[$i]?></td>
			<td><?=$exDescFac?></td>
		</tr>
		<? } *///?>
	</table>
	  </fieldset>


<?
$sqlUpload="SELECT COUNT(*) as total  FROM file_upload_viva
		WHERE pg_viva_id = '$pgVivaId' 
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
			<td>
			<input type="hidden" name="totalA" id="totalA" value="<?=$attachment?>"/>
			<button type="button" name="btnAttachment" value="Attachment" onClick="return newAttachment('<?=$pgVivaId?>', '<?=$thesisId?>', '<?=$proposalId?>')" >
			Attachment <FONT COLOR="#FF0000"><sup><?=$a?></sup></FONT></button></td>
		</tr>
	</table>
	
	<table width="664">
		<tr>
			<td width="606" colspan="2"><label><span style="color:#FF0000">Notes:</span><br/>
					1. Submit button is for submit thesis to supervisor for review<br />
					2. Save as Draft button is for save the attachment on the system first before submit to supervisor
			.</label></td>
		</tr>
		<tr>
		  <td colspan="2">
		  <input type="button" name="btnBack" id="btnBack" value="Back" onClick="javascript:document.location.href='list_of_viva.php';"/>
		  <input type="submit" name="btnSave" id="btnSave" value="Save as Draft"/>
		    <input type="submit" name="btnSubmit" id="btnSubmit" value="Submit" onclick = "return submitReport()"/></td>
			
		</tr>
	</table>
	<? 
		} else {
	$msg = array();
	echo $msg = "<div class=\"error\"><span>You are not able to submit your <strong>Thesis</strong>. You can only submit thesis for thesis after the date of viva evaluation has been recommended by supervisor.</span></div>"; ?>
	<table>
		<tr>
			<td><input type="button" name="btnBack" id="btnBack" value="Back" onClick="javascript:document.location.href='list_of_viva.php';"/></td>
		</tr>
	</table>
	<? }
	} else {
	$msg = array();
	echo $msg = "<div class=\"error\"><span>You are not able to submit your <strong>Thesis</strong> for VIVA. You can only submit thesis for viva evaluation after work completion has been done.</span></div>"; ?>	
	<table>
		<tr>
			<td><input type="button" name="btnBack" id="btnBack" value="Back" onClick="javascript:document.location.href='list_of_viva.php';"/></td>
		</tr>
	</table>
	<? } ?>
	</form>
	<script>
		<?=$jscript1;?>
		<?=$jscript2;?>
		<?=$jscript3;?>
	</script>
</body>
</html>



