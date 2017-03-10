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
		var ask = window.confirm("Are you sure to approve this appeal? \nClick OK to proceed or CANCEL to stay on the same page.");
		if (ask) 
		{
			//document.location.href = "../monthlyreport/new_defense.php?pid=" + pid + "&tid=" + tid + "&id=" + id;
			return true;
		}
		return false;
	}

	</script>
	<script type="text/javascript">
	function submitReportDis() 
	{
		var ask = window.confirm("Are you sure to disapprove this appeal? \nClick OK to proceed or CANCEL to stay on the same page.");
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
$matrixNo=$_GET['mn'];
$pgVivaId=$_GET['vid'];
$appealId=$_GET['aid'];

$save=$_GET['save'];

if($save == '2')
{
	$msg[] = "<div class=\"success\"><span>Appeal result has been submitted successfully.</span></div>";
}

///////select name//////////////////////////
$sqlstaffname = "SELECT name from new_employee where empid = '$matrixNo'";
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

if(isset($_POST['btnDis']) && ($_POST['btnDis'] <> ""))
{
	$curdatetime = date("Y-m-d H:i:s");
	$newAppealId = "A".runnum2('id','pg_viva_appeal');
	$scheId = $_REQUEST['scheId'];
		
	$sqlapp = "SELECT appeal_desc,date_request, insert_by, insert_date, STATUS
	FROM pg_viva_appeal
	WHERE id = '$appealId'";
	$dbApp = $db;		
	$dbApp->query($sqlapp);
	$dbApp->next_record();
		
	$date_request = $dbApp->f('date_request');
	$insert_by = $dbApp->f('insert_by');
	$insert_date = $dbApp->f('insert_date');
	$appeal_desc = $dbApp->f('appeal_desc');	

	$sql7 = "INSERT INTO pg_viva_appeal
	(id, appeal_desc, date_request, pg_viva_id, insert_by, insert_date, STATUS, student_matrix_no,result_appeal, respond_date, respond_by)
	VALUES
	('$newAppealId','$appeal_desc','$date_request','$pgVivaId','$matrixNo','$insert_date','SUB', '$matrixNo', 'DIS', '$curdatetime', '$user_id')";
	$db->query($sql7);			

	$sql7 = "UPDATE pg_viva_appeal
	SET modify_by = '$user_id', modify_date = '$curdatetime', status = 'ARC'
	WHERE id = '$appealId'
	AND pg_viva_id = '$pgVivaId'
	AND student_matrix_no = '$matrixNo'";
						
	$db->query($sql7);
	
	$sql7 = "UPDATE pg_viva 
	SET appeal_result = 'DIS', appeal_date = '$curdatetime', appeal_confirm_by = '$user_id'
	WHERE id = '$pgVivaId' 
	AND pg_calendar_id = '$scheId'
	AND pg_thesis_id = '$pgThesisId'
	AND student_matrix_no = '$matrixNo'
	AND viva_status = 'FAI'
	AND STATUS = 'ARC'";
						
	$db->query($sql7);

	$msg = array();
	$msg[] = "<div class=\"success\"><span>Your Appeal has has been saved successfully.</span></div>";	
}

if(isset($_POST['btnApp']) && ($_POST['btnApp'] <> ""))
{	
	$curdatetime = date("Y-m-d H:i:s");
	$newAppealId = "A".runnum2('id','pg_viva_appeal');
	$scheId = $_REQUEST['scheId'];
	
		
	$sqlapp = "SELECT appeal_desc,date_request, insert_by, insert_date, STATUS
	FROM pg_viva_appeal
	WHERE id = '$appealId'";
	$dbApp = $db;		
	$dbApp->query($sqlapp);
	$dbApp->next_record();

	$date_request = $dbApp->f('date_request');
	$insert_by = $dbApp->f('insert_by');
	$insert_date = $dbApp->f('insert_date');
	$appeal_desc = $dbApp->f('appeal_desc');	

	$sql7 = "INSERT INTO pg_viva_appeal
	(id, appeal_desc, date_request, pg_viva_id, insert_by, insert_date, STATUS, student_matrix_no,result_appeal, respond_date, respond_by)
	VALUES
	('$newAppealId','$appeal_desc','$date_request','$pgVivaId','$matrixNo','$insert_date','SUB', '$matrixNo', 'APP', '$curdatetime', '$user_id')";
	$db->query($sql7);
	

	$sql7 = "UPDATE pg_viva_appeal
	SET modify_by = '$user_id', modify_date = '$curdatetime', status = 'ARC'
	WHERE id = '$appealId'
	AND pg_viva_id = '$pgVivaId'
	AND student_matrix_no = '$matrixNo'";
						
	$db->query($sql7);
	
	$sql7 = "UPDATE pg_viva 
	SET appeal_result = 'APP', appeal_date = '$curdatetime', appeal_confirm_by = '$user_id', 
	status = 'ARC1', modify_by = '$user_id', modify_date = '$curdatetime'
	WHERE id = '$pgVivaId' 
	AND pg_calendar_id = '$scheId'
	AND pg_thesis_id = '$pgThesisId'
	AND student_matrix_no = '$matrixNo'
	AND viva_status = 'FAI'
	AND STATUS = 'ARC'";
						
	$db->query($sql7);
	
	$sqlamend = "SELECT id FROM pg_amendment
	WHERE pg_thesis_id = '$pgThesisId'
	AND pg_viva_id = '$pgVivaId'
	AND student_matrix_no = '$matrixNo'
	AND ref_req_no IS NULL";
	
	$result_sqlamend = $dbg->query($sqlamend); 
	$dbg->next_record();
	$row_cnt_amend = mysql_num_rows($result_sqlamend);
	$amendId = $dbg->f('id');
	
	if ($row_cnt_amend>0){
	
		$sql7 = "UPDATE pg_amendment
		SET status = 'A', modify_date = '$curdatetime', modify_by = '$user_id'
		WHERE pg_thesis_id = '$thesisId'
		AND pg_proposal_id = '$proposalId'
		AND pg_viva_id = '$pg_viva_id'
		AND student_matrix_no = '$matrixNo'
		AND id = '$amendId'
		AND ref_req_no IS NULL";
					
		$db->query($sql7);
	
	}
	
	$msg = array();
	$msg[] = "<div class=\"success\"><span>This Appeal has has been Approved.</span></div>";		
		
	/*	
	///////select date schedule//////////////////////////
	$sql3 = "SELECT id, defense_date, DATE_FORMAT(defense_date,'%d-%b-%Y') as viva_date, 
	DATE_FORMAT(defense_stime,'%h:%i%p') as viva_stime,
	DATE_FORMAT(defense_etime,'%h:%i%p') as viva_etime, venue, recomm_status, status
	FROM pg_calendar
	WHERE student_matrix_no = '$matrixNo'
	AND thesis_id = '$pgThesisId'
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
	
	
	$sqlemail = "SELECT name,email FROM student
	WHERE `matrix_no` = '$matrixNo'";
	$dbk1 = $dbk;
	$resultreceive = $dbk1->query($sqlemail);
	$resultsqlreceive = $dbk1->next_record(); 
	$studemail = $dbk1->f('email');
	$studentName = $dbk1->f('name');
		
	///// message notification////
	$sqlmsg = "SELECT const_value FROM base_constant
	WHERE const_term = 'MESSAGE_STU_TO_FAC'";
	
	$result_sqlmsg = $dbg->query($sqlmsg); 
	$dbg->next_record();
	$row_cnt_msg = mysql_num_rows($result_sqlmsg);
	$msgConstValue = $dbg->f('const_value');
	
	if ($msgConstValue == 'Y')
	{
		//include("../../../app/application/inbox/viva/thesis_confirm.php");
	}
	
	////email notification/////
	$sqlemail = "SELECT const_value FROM base_constant
	WHERE const_term = 'EMAIL_STU_TO_FAC'";
	$dbg1 = $dbg;
	$result_sqlemail = $dbg1->query($sqlemail); 
	$dbg1->next_record();
	$row_cnt_email = mysql_num_rows($result_sqlemail);
	$emailConstValue = $dbg1->f('const_value');
	
	if ($emailConstValue == 'Y')
	{
		//include("../../../app/application/email/viva/email_thesis_confirm.php");
	}
	$save = "2";
	echo "<script>window.location = 'view_appeal.php?tid=".$pgThesisId."&pid=".$proposalId."&vid=".$pgVivaId."';</script>";
	*/
		
		
					
}



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
WHERE pt.student_matrix_no = '$matrixNo'
AND pp.verified_status in ('APP','AWC')				
AND pp.archived_status is null
AND pt.ref_thesis_status_id_proposal in ('APP','AWC','APC')
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
WHERE a.pg_student_matrix_no='$matrixNo'
AND g.pg_thesis_id = '$thesisId'
AND g.id = '$proposalId'
AND a.acceptance_status = 'ACC'
AND a.ref_supervisor_type_id in ('SV','CS','XS')
AND g.verified_status in ('APP','AWC')
AND g.status in ('APP','APC')
AND g.archived_status IS NULL
AND a.status = 'A'
AND a.pg_employee_empid <> '$matrixNo'
ORDER BY d.seq, a.ref_supervisor_type_id";

$result_sql_supervisor = $db_klas2->query($sql_supervisor); //echo $sql;
$db_klas2->next_record();
$row_cnt_supervisor = mysql_num_rows($result_sql_supervisor);



$sql4 = "SELECT b.pg_calendar_id
FROM pg_calendar a
LEFT JOIN pg_viva b ON (b.pg_calendar_id = a.id)
WHERE a.student_matrix_no = '$matrixNo'
AND a.thesis_id = '$thesisId'
AND a.ref_session_type_id = 'VIV'
AND a.recomm_status = 'REC'
AND b.id = '$pgVivaId'
ORDER BY defense_date ASC";

$result_sql4 = $dbg->query($sql4); 
$dbg->next_record();
$row_cnt4 = mysql_num_rows($result_sql4);
$calendarIdViva = $dbg->f('pg_calendar_id');

$sql_appeal = "SELECT id, appeal_desc from pg_viva_appeal
WHERE pg_viva_id = '$pgVivaId'
AND student_matrix_no = '$matrixNo'
AND status IN ('A', 'SUB', 'SAV')";
$dba1 = $dba;			
$result_sql_appeal = $dba1->query($sql_appeal); //echo $sql;
$dba1->next_record();
$row_cnt_appeal = mysql_num_rows($result_sql_appeal);
							
$appealId = $dba1->f('id');
$appeal_desc = $dba1->f('appeal_desc');


///////////////////////list of evaluation panel for viva///////////////////////
$sql_supervisor = " SELECT a.pg_empid_viva, b.role_status, b.ref_supervisor_type_id, e.description as recDesc,
c.description AS roleDesc, a.report_status, DATE_FORMAT(a.submit_date,'%d-%b-%Y %h:%i:%s %p') as submit_date, a.id as otherDetailId
FROM pg_evaluation_viva_detail a
LEFT JOIN pg_supervisor b ON (b.pg_employee_empid = a.pg_empid_viva)
LEFT JOIN ref_supervisor_type c ON (c.id = b.ref_supervisor_type_id)
LEFT JOIN pg_evaluation_viva d ON (d.id = a.pg_eva_viva_id)
LEFT JOIN ref_recommendation e ON (e.id = a.recommendation_id)
WHERE b.pg_thesis_id = '$thesisId'
AND d.pg_viva_id = '$pgVivaId'
AND b.status = 'A'
ORDER by b.ref_supervisor_type_id, b.role_status ASC";

$db_klas3 = $db_klas2;
$result_sql_supervisor = $db_klas3->query($sql_supervisor); //echo $sql;
$db_klas3->next_record();
$row_cnt_supervisor = mysql_num_rows($result_sql_supervisor);

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
function newAttachment(tid,pid,mn,role,vid, vd) {
    var ask = window.confirm("Ensure your viva has been saved before proceed or otherwise the last change will be discarded.\nClick OK to proceed or CANCEL to stay on the same page.");
    if (ask) {
		document.location.href = "../viva/viva_rev_attachment.php?vid=" + vid + "&tid=" +tid+"&pid=" +pid+"&mn=" +mn+"&role=" +role+"&vd=" +vd;

    }
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
				<td><strong>Appeal Session </strong> <h3></td>
				</tr>
			</table>
			<table width="52%">
				<!--<tr>
					<td width="20%">Report Status</td>
					<td width="2%">:</td>
					<?if ($defenseDesc=="") $defenseDesc='New';?>
					<td width="30%"><strong><?=$defenseDesc?></strong></td>
				</tr>-->
				<!--<tr>
					<td>Reference No</td>
					<td>:</td>
					<td><strong><?=$referenceNo?></strong></td>
				</tr>-->
				<tr>
					<td>Thesis / Project ID</td>
					<td>:</td>
					<td><label>
					<?=$pgThesisId;?>
					</label>
					<input type="hidden" name="thesisTitle" id="thesisTitle" value="<?=$thesisTitle; ?>"></td>
					<input type="hidden" name="id" id="id" value="<?=$id; ?>">
					<input type="hidden" name="thesisId" id="thesisId" value="<?=$thesisId; ?>">
					<input type="hidden" name="proposalId" id="proposalId" value="<?=$proposalId; ?>">
					<input type="hidden" name="vivaId" id="vivaId" value="<?=$pgVivaId; ?>">
					<input type="hidden" name="appealId" id="appealId" value="<?=$appealId?>" />
				</tr>
				<tr>
					<td>Student Matrix No</td>
					<td>:</td>
					<td><?=$matrixNo?></td>
				</tr>
					<?
					$sql1 = "SELECT name AS student_name
					FROM student
					WHERE matrix_no = '$matrixNo'";
					if (substr($matrixNo,0,2) != '07') { 
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
					<?
		
					$sql3 = "SELECT id, defense_date, DATE_FORMAT(defense_date,'%d-%b-%Y') as viva_date, 
					DATE_FORMAT(defense_stime,'%h:%i%p') as viva_stime,
					DATE_FORMAT(defense_etime,'%h:%i%p') as viva_etime, venue, recomm_status, status
					FROM pg_calendar
					WHERE student_matrix_no = '$matrixNo'
					AND thesis_id = '$thesisId'
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
                    <a href="view_viva_report_faculty.php?tid=<?=$thesisId?>&type=<?=$ref_supervisor_type_id?>&mn=<?=$matrixNo?>&role=<?=$role_status?>&pd=<?=$otherDetailId?>&empid=<?=$employeeId?>&other=<?=$evaluationId?>&othertype=<?=$roleType?>&vid=<?=$pgVivaId?>&aid=<?=$appealId?>" name="thesisId" value="<?=$thesisId?>" title="View feedback"><img src="../images/view.jpg" width="45" height="30" style="border:0px;" title="View feedback" /></a>
					
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
	  </fieldset>
	<fieldset><legend><strong>Content</strong></legend>
		<table width="90%">
			<tr>
				<td width="94%"><?=$appeal_desc?></td>
				<!--<textarea class="ckeditor" name="content" id="content"></textarea>-->
			</tr>
		</table>
	</fieldset>
<?
		$sqlUpload="SELECT COUNT(*) as total  FROM file_upload_viva
		WHERE pg_viva_id = '$pgVivaId' 
		AND pg_viva_detail_id = '$pgVivaDetailId'";			
		$dbr = $dbf;
		$result = $dbr->query($sqlUpload); 
		$dbr->next_record();
		$attachment = $dbr->f('total');
		
		if($attachment == '0')
		{
			$a = '';
		}
		else
		{
			$a = "(".$attachment.")";
		}

?>


	<!--<table>
		<tr>
			<td>
			<button type="button" name="btnAttachment" value="Attachment" onClick="return newAttachment('<?=$pgThesisId?>', '<?=$proposalId?>', '<?=$matrixNo?>', '<?=$role?>','<?=$pgVivaId?>','<?=$pgVivaDetailId?>')" >
			Attachment <FONT COLOR="#FF0000"><sup><?=$a?></sup></FONT></button>
			</td>
		</tr>
	</table>-->
<table>
	<tr>
		<td colspan="2">
			<input type="hidden" name="totalA" id="totalA" value="<?=$attachment?>"/>
			<input type="button" name="btnBack" id="btnBack" value="Back" onClick="javascript:document.location.href='list_appeal_faculty.php';"/>
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




