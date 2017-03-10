<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: recommend_defense.php
//
// Created by: Zuraimi
// Created Date: 18-Mar-2015
// Modified by: Zuraimi
// Modified Date: 18-Mar-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

session_start();
$user_id=$_SESSION['user_id'];
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
function runnum2($column_name, $tblname) 
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

if(isset($_POST['btnRecommendation1']) && ($_POST['btnRecommendation1'] <> "")) {
}

if(isset($_POST['btnCancelDefence']) && ($_POST['btnCancelDefence'] <> "")) {
}

if(isset($_POST['btnSearch']) && ($_POST['btnSearch'] <> "")) {
	$searchThesisId = $_POST['searchThesisId'];
	$searchStudent = $_POST['searchStudent'];
	$searchDate = $_POST['searchDate'];
	$msg = Array();
	
	if ($searchThesisId!="") 
	{
		$tmpSearchThesisId = " AND i.pg_thesis_id = '$searchThesisId'";
	}
	else 
	{
		$tmpSearchThesisId="";
	}
	
	if ($searchStudent!="") 
	{
		$tmpSearchStudent = " AND i.student_matrix_no = '$searchStudent'";
	}
	else 
	{
		$tmpSearchStudent="";
	}

	if ($searchDate!="") 
	{
		$tmpSearchDate = " AND '%$searchDate%'";
	}
	else 
	{
		$tmpSearchDate="";
	}
	
	
	$sql = " SELECT g.thesis_title, i.respond_status, i.result_status, i.reference_no AS refNo,  
	DATE_FORMAT(i.respond_date,'%d-%b-%Y %h:%i:%s %p') AS respond_date, i.id as vivaEvaId,
	a.pg_thesis_id, g.id AS proposal_id, 
	a.pg_student_matrix_no, a.ref_supervisor_type_id, 
	d.description AS supervisor_type_desc, DATE_FORMAT(a.acceptance_date,'%d-%b-%Y %h:%i:%s %p') AS acceptance_date, 
	a.role_status, h.description AS role_status_desc, v.report_status, v.recommendation_id, i.result_status as resultStatusMain, v.submit_date, 
	v.recommendation_id, l.description as recommDesc, a.ref_supervisor_type_id, i.final_result,s.id as vivaId, s.pg_calendar_id
	FROM pg_supervisor a 
	LEFT JOIN ref_supervisor_type d ON (d.id = a.ref_supervisor_type_id) 
	LEFT JOIN pg_thesis f ON (f.student_matrix_no = a.pg_student_matrix_no) 
	LEFT JOIN pg_proposal g ON (g.pg_thesis_id = f.id) 
	LEFT JOIN ref_role_status h ON (h.id = a.role_status) 
	LEFT JOIN pg_evaluation_viva i ON (i.pg_thesis_id = f.id)
	LEFT JOIN pg_evaluation_viva_detail v ON (v.pg_eva_viva_id = i.id)
	LEFT JOIN ref_recommendation l ON (l.id = v.recommendation_id)
	LEFT JOIN pg_viva s ON (s.id = i.pg_viva_id)
	LEFT JOIN pg_calendar k ON (k.id = s.pg_calendar_id)
	LEFT JOIN pg_invitation m ON (m.pg_calendar_id = k.id)
	LEFT JOIN pg_invitation_detail j ON (j.pg_invitation_id = m.id)  
	WHERE a.pg_employee_empid = '$user_id' 
	AND j.acceptance_status = 'ACC' 
	AND a.ref_supervisor_type_id IN ('SV','CS','XS', 'EE', 'EI', 'EC') 
	AND g.verified_status IN ('APP','AWC') 
	AND g.status IN ('APP','APC') "
	.$tmpSearchThesisId." "
	.$tmpSearchStudent." 
	AND g.archived_status IS NULL 
	AND a.status = 'A' 
	AND k.ref_session_type_id = 'VIV'
	AND k.recomm_status = 'REC'
	AND i.id is not null
	AND v.id is not null
	AND v.pg_empid_viva = '$user_id'
	AND j.pg_employee_empid = '$user_id'
	ORDER BY d.seq, a.ref_supervisor_type_id  ";

	$result_sql = $dbg->query($sql); 
	$dbg->next_record();
	$row_cnt = mysql_num_rows($result_sql);

	$thesisIdArray = Array();
	$thesisTitleArray = Array();
	$proposalIdArray = Array();
	$studentMatrixNoArray = Array();
	$studentNameArray = Array();
	$supervisorTypeArray = Array();
	$supervisorTypeArray = Array();
	$acceptanceDateArray = Array();
	$roleStatusArray = Array();
	$roleStatusDescArray = Array();
	$respond_dateArray = Array();
	
	
	$refNoArray = Array();
	$respond_statusArray = Array();
	$result_statusArray = Array(); 
	$vivaEvaIdArray = Array();
	$report_status = Array();
	$result_status = Array();
	$resultStatusMain = Array();
	$submit_date = Array();
	$recommendation_id = Array();
	$recommDesc = Array();
	$ref_supervisor_type_id = Array();
	$final_result_status= Array();
	$vivaId= Array();
	$pg_calendar_id= Array();


	$no=0;
	$no1=0;
	$no3 = 0;

	if ($row_cnt > 0) {
		do {
			$studentMatrixNoArray[$no]=$dbg->f('pg_student_matrix_no');
			$refNoArray[$no]=$dbg->f('reference_no');
			$thesisIdArray[$no]=$dbg->f('pg_thesis_id');
			$proposalIdArray[$no]=$dbg->f('proposal_id');
			$supervisorTypeIdArray[$no]=$dbg->f('ref_supervisor_type_id');
			$supervisorTypeDescArray[$no]=$dbg->f('supervisor_type_desc');
			$acceptanceDateArray[$no]=$dbg->f('acceptance_date');
			$respond_dateArray[$no]=$dbg->f('respond_date');
			$thesisTitleArray[$no]=$dbg->f('thesis_title');
			$roleStatusArray[$no] = $dbg->f('role_status');
			
			$respond_statusArray[$no] = $dbg->f('respond_status');
			$result_statusArray[$no] = $dbg->f('result_status');
			$vivaEvaIdArray[$no] = $dbg->f('vivaEvaId');
			$refNoArray[$no] = $dbg->f('refNo');
			
			$report_status[$no] = $dbg->f('report_status');
			$result_status[$no] = $dbg->f('result_status');
			$resultStatusMain[$no] = $dbg->f('resultStatusMain');
			$submit_date[$no] = $dbg->f('submit_date');
			$recommendation_id[$no] = $dbg->f('recommendation_id');
			$recommDesc[$no]= $dbg->f('recommDesc');
			$ref_supervisor_type_id[$no] = $dbg->f('ref_supervisor_type_id');
			$final_result_status[$no] = $dbg->f('final_result');
			$vivaId[$no] = $dbg->f('vivaId');
			$pg_calendar_id[$no] = $dbg->f('pg_calendar_id');

			
			$no++;
			$no3++;
		}while ($dbg->next_record());
		
		for ($i=0; $i<$no; $i++){
			if (substr($studentMatrixNoArray[$i],0,2) != '07') { 
				$dbConn=$dbc; 
			} 
			else { 
				$dbConn=$dbc1; 
			}

			$sql1 = "SELECT name
			FROM student
			WHERE matrix_no = '$studentMatrixNoArray[$i]'"
			.$tmpSearchStudentName." ";			
			
			$result_sql1 = $dbConn->query($sql1); 
			$dbConn->next_record();
			$row_cnt1 = mysql_num_rows($result_sql1);
			
			if ($row_cnt1 > 0) {
				$studentNameArray[$no1]=$dbConn->f('name');
				$thesisIdArray[$no1]=$thesisIdArray[$i];
				$proposalIdArray[$no1]=$proposalIdArray[$i];
				$studentMatrixNoArray[$no1]=$studentMatrixNoArray[$i];
				$supervisorTypeIdArray[$no1]=$supervisorTypeIdArray[$i];
				$supervisorTypeDescArray[$no1]=$supervisorTypeDescArray[$i];
				$acceptanceDateArray[$no1]=$acceptanceDateArray[$i];
				$no1++;
			}			
		}
		if ($no1 == 0) {			
			$msg[] = "<div class=\"error\"><span>No record(s) found.</span></div>";			
		}
		$row_cnt = $no1;
	}
	else {
		$msg[] = "<div class=\"error\"><span>No record(s) found.</span></div>";
	}
}
else {

	$sql = "SELECT g.thesis_title, i.respond_status, i.result_status, i.reference_no AS refNo,  
	DATE_FORMAT(i.respond_date,'%d-%b-%Y %h:%i:%s %p') AS respond_date, i.id as vivaEvaId,
	a.pg_thesis_id, g.id AS proposal_id, 
	a.pg_student_matrix_no, a.ref_supervisor_type_id, 
	d.description AS supervisor_type_desc, DATE_FORMAT(a.acceptance_date,'%d-%b-%Y %h:%i:%s %p') AS acceptance_date, 
	a.role_status, h.description AS role_status_desc, v.report_status, v.recommendation_id, i.result_status as resultStatusMain, v.submit_date, 
	v.recommendation_id, l.description as recommDesc, a.ref_supervisor_type_id, i.final_result,s.id as vivaId, s.pg_calendar_id
	FROM pg_supervisor a 
	LEFT JOIN ref_supervisor_type d ON (d.id = a.ref_supervisor_type_id) 
	LEFT JOIN pg_thesis f ON (f.student_matrix_no = a.pg_student_matrix_no) 
	LEFT JOIN pg_proposal g ON (g.pg_thesis_id = f.id) 
	LEFT JOIN ref_role_status h ON (h.id = a.role_status) 
	LEFT JOIN pg_evaluation_viva i ON (i.pg_thesis_id = f.id)
	LEFT JOIN pg_evaluation_viva_detail v ON (v.pg_eva_viva_id = i.id)
	LEFT JOIN ref_recommendation l ON (l.id = v.recommendation_id)
	LEFT JOIN pg_viva s ON (s.id = i.pg_viva_id)
	LEFT JOIN pg_calendar k ON (k.id = s.pg_calendar_id)
	LEFT JOIN pg_invitation m ON (m.pg_calendar_id = k.id)
	LEFT JOIN pg_invitation_detail j ON (j.pg_invitation_id = m.id) 
	WHERE a.pg_employee_empid = '$user_id' 
	AND j.acceptance_status = 'ACC' 
	AND a.ref_supervisor_type_id IN ('SV','CS','XS', 'EE', 'EI', 'EC') 
	AND g.verified_status IN ('APP','AWC') 
	AND g.status IN ('APP','APC') 
	AND g.archived_status IS NULL 
	AND a.status = 'A' 
	AND k.ref_session_type_id = 'VIV'
	AND k.recomm_status = 'REC'
	AND i.id is not null
	AND v.id is not null
	AND v.pg_empid_viva = '$user_id'
	AND j.pg_employee_empid = '$user_id'
	ORDER BY d.seq, a.ref_supervisor_type_id  ";

	$result_sql = $dbg->query($sql); 
	$dbg->next_record();
	$row_cnt = mysql_num_rows($result_sql);

	$thesisIdArray = Array();
	$thesisTitleArray = Array();
	$proposalIdArray = Array();
	$studentMatrixNoArray = Array();
	$studentNameArray = Array();
	$supervisorTypeArray = Array();
	$supervisorTypeArray = Array();
	$acceptanceDateArray = Array();
	$roleStatusArray = Array();
	$roleStatusDescArray = Array();
	$respond_dateArray = Array();
	
	
	$refNoArray = Array();
	$respond_statusArray = Array();
	$result_statusArray = Array(); 
	$vivaEvaIdArray = Array();
	$report_status = Array();
	$result_status = Array();
	$resultStatusMain = Array();
	$submit_date = Array();
	$recommendation_id = Array();
	$recommDesc = Array();
	$ref_supervisor_type_id = Array();
	$final_result_status= Array();
	$vivaId= Array();
	$pg_calendar_id= Array();

	$no=0;
	$no1=0;
	$no3 = 0;

	if ($row_cnt > 0) {
		do {
			$studentMatrixNoArray[$no]=$dbg->f('pg_student_matrix_no');
			$refNoArray[$no]=$dbg->f('reference_no');
			$thesisIdArray[$no]=$dbg->f('pg_thesis_id');
			$proposalIdArray[$no]=$dbg->f('proposal_id');
			$supervisorTypeIdArray[$no]=$dbg->f('ref_supervisor_type_id');
			$supervisorTypeDescArray[$no]=$dbg->f('supervisor_type_desc');
			$acceptanceDateArray[$no]=$dbg->f('acceptance_date');
			$respond_dateArray[$no]=$dbg->f('respond_date');
			$thesisTitleArray[$no]=$dbg->f('thesis_title');
			$roleStatusArray[$no] = $dbg->f('role_status');
			
			$respond_statusArray[$no] = $dbg->f('respond_status');
			$result_statusArray[$no] = $dbg->f('result_status');
			$vivaEvaIdArray[$no] = $dbg->f('vivaEvaId');
			$refNoArray[$no] = $dbg->f('refNo');
			
			$report_status[$no] = $dbg->f('report_status');
			$result_status[$no] = $dbg->f('result_status');
			$resultStatusMain[$no] = $dbg->f('resultStatusMain');
			$submit_date[$no] = $dbg->f('submit_date');
			$recommendation_id[$no] = $dbg->f('recommendation_id');
			$recommDesc[$no]= $dbg->f('recommDesc');
			$ref_supervisor_type_id[$no] = $dbg->f('ref_supervisor_type_id');
			$final_result_status[$no] = $dbg->f('final_result');
			$vivaId[$no] = $dbg->f('vivaId');
			$pg_calendar_id[$no] = $dbg->f('pg_calendar_id');
			
			$no++;
			$no3++;
		}while ($dbg->next_record());
		
		for ($i=0; $i<$row_cnt; $i++){
			if (substr($studentMatrixNoArray[$i],0,2) != '07') { 
				$dbConn=$dbc; 
			} 
			else { 
				$dbConn=$dbc1; 
			}

			$sql1 = "SELECT name
			FROM student
			WHERE matrix_no = '$studentMatrixNoArray[$i]'";
			
			$result_sql1 = $dbConn->query($sql1); 
			$dbConn->next_record();
			$studentNameArray[$no1]=$dbConn->f('name');
			$thesisIdArray[$no1]=$thesisIdArray[$i];
			$proposalIdArray[$no1]=$proposalIdArray[$i];
			$studentMatrixNoArray[$no1]=$studentMatrixNoArray[$i];
			$supervisorTypeIdArray[$no1]=$supervisorTypeIdArray[$i];
			$supervisorTypeDescArray[$no1]=$supervisorTypeDescArray[$i];
			$acceptanceDateArray[$no1]=$acceptanceDateArray[$i];
			$roleStatusArray[$no1]=$roleStatusArray[$i];
			$roleStatusDescArray[$no1]=$roleStatusDescArray[$i];
			$no1++;
		}
	}
}

	/*$sql1 = "SELECT i.respond_status, i.result_status, i.reference_no AS refNo, i.respond_by,
	DATE_FORMAT(i.respond_date,'%d-%b-%Y %h:%i:%s %p') AS respond_date, 
	i.id AS vivaEvaId, a.pg_thesis_id, g.id AS proposal_id, a.pg_student_matrix_no, a.ref_supervisor_type_id, 
	d.description AS supervisor_type_desc, DATE_FORMAT(a.acceptance_date,'%d-%b-%Y %h:%i:%s %p') AS acceptance_date, a.role_status, 
	h.description AS role_status_desc, i.result_status AS resultStatusMain, 
	l.description AS recommDesc, a.ref_supervisor_type_id 
	FROM pg_supervisor a 
	LEFT JOIN ref_supervisor_type d ON (d.id = a.ref_supervisor_type_id) 
	LEFT JOIN pg_thesis f ON (f.student_matrix_no = a.pg_student_matrix_no) 
	LEFT JOIN pg_proposal g ON (g.pg_thesis_id = f.id) 
	LEFT JOIN ref_role_status h ON (h.id = a.role_status) 
	LEFT JOIN pg_evaluation_viva i ON (i.pg_thesis_id = f.id) 
	LEFT JOIN ref_recommendation l ON (l.id = i.result_status) 
	LEFT JOIN pg_calendar k ON (k.thesis_id = f.id) 
	WHERE a.acceptance_status = 'ACC' 
	AND a.ref_supervisor_type_id IN ('EC') 
	AND g.verified_status IN ('APP','AWC') 
	AND g.status IN ('APP','APC') 
	AND g.archived_status IS NULL 
	AND a.status = 'A' AND k.ref_session_type_id = 'VIV' 
	AND k.recomm_status = 'REC' 
	AND i.id IS NOT NULL 
	ORDER BY d.seq, a.ref_supervisor_type_id  ";
	
	$dbg33 = $dbg;
	$result_sql1 = $dbg33->query($sql1); 
	$dbg33->next_record();
	$row_cnt = mysql_num_rows($result_sql1);
	$thesisId = $dbg33->f('pg_thesis_id');
	$matrixNo = $dbg33->f('pg_student_matrix_no');
	$vivaEvaId = $dbg33->f('vivaEvaId');
	$respond_by = $dbg33->f('respond_by');*/
	
$selectDateSql = "SELECT a.id, DATE_FORMAT(a.defense_date,'%d-%b-%Y') as defense_date, 
DATE_FORMAT(a.defense_stime,'%h:%i%p') as defense_stime,
DATE_FORMAT(a.defense_etime,'%h:%i%p') as defense_etime,
a.venue
FROM pg_calendar a
LEFT JOIN pg_viva b ON (b.pg_calendar_id = a.id)
WHERE a.recomm_status = 'REC'
AND a.ref_session_type_id = 'VIV'";
$dbgDate = $dbg;
$result_selectDateSql = $dbgDate->query($selectDateSql); 
$dbgDate->next_record();
$row_cnt_date = mysql_num_rows($result_selectDateSql);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
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

	<fieldset>
	<legend><strong>Viva Evaluation </strong></legend>
		<table>
			<tr>							
				<td>Please enter searching criteria below:-</td>
			</tr>
		</table>
		<table>
			<tr>
				<td>Thesis / Project ID</td>
				<td>:</td>
				<td><input type="text" name="searchThesisId" size="15" id="searchThesisId" value="<?=$searchThesisId;?>"/></td>
			</tr>
			<tr>
				<td>Matrix No</td>
				<td>:</td>
				<td><input type="text" name="searchStudent" size="15" id="searchStudent" value="<?=$searchStudent;?>"/></td>
				<td><input type="submit" name="btnSearch" value="Search" /><span style="color:#FF0000"> Note:</span> If no entry is provided, it will search all.</td>
			</tr>
			<!--<tr>
				<td>Session Date</td>
				<td>:</td>
			  <td><select name="searchDate" id="searchDate">
					<option value=""></option>
				<? do { 
					$defense_date2=$dbgDate->f('defense_date');
					$defense_stime2=$dbgDate->f('defense_stime');
					$defense_etime2=$dbgDate->f('defense_etime');
					$venue2=$dbgDate->f('venue');
					$calId=$dbgDate->f('id');
				?>
					<option value="<?=$calId?>"><?=$defense_date2?>, <?=$defense_stime2?> to <?=$defense_etime2?>, <?=$venue2?></option>
				<? }while ($dbgDate->next_record()); ?>
				</select>
				</td>
				</tr>-->
		</table>
		<table>
			<tr>							
				<td><span style="color:#FF0000"> Notes:</span><br/>
				1. VIVA evaluation report will appear if the invitation has been accepted &amp; student has submitted the thesis.</td>
			</tr>
		</table>
		<br/>
		<table>
			<tr>							
				<td>Searching Results:- <?=$row_cnt ?> record(s) found.</td>
			</tr>
		</table>
		<?if ($row_cnt <= 1) {?>
			<div id = "tabledisplay" style="overflow:auto; height:110px;">
		<?}
		else if ($row_cnt <= 3) {?>
			<div id = "tabledisplay" style="overflow:auto; height:200px;">
		<?}
		else {
			?>
			<div id = "tabledisplay" style="overflow:auto; height:300px;">
			<?
		}?>		
		<?
		
		$sql2 = "SELECT const_value
		FROM base_constant
		WHERE const_category = 'DEFENSE_PROPOSAL'
		AND const_term = 'DEFENSE_DURATION'";
		
		$result_sql2 = $db->query($sql2); 
		$db->next_record();
		$defenseDurationParam = $db->f('const_value');
		
		$currentDate1 = date('d-M-Y');
		$tmpCurrentDate = new DateTime($currentDate1);
		$myTmpCurrentDate = $tmpCurrentDate->format('d-M-Y');
		$currentDate = new DateTime($myTmpCurrentDate);
		$expectedDate = date('d-M-Y', strtotime($currentDate1. ' '.($defenseDurationParam).' day'));
		?>
	    <table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="91%" class="thetable">
          <tr>
            <th width="3%">No</th>
            <!--<th width="7%">Reference No </th>-->
            <th width="9%" align="left">Thesis / Project ID</th>
            <!--<th width="7%" align="left">Thesis Title</th>-->
            <th width="13%" align="left">Student Name<br />(Student Matrix No)</th>
            <th width="17%" align="left">Session Date</th>
            <!--<th width="12%" align="left">Submitted Date</th>-->
            <th width="12%" align="left">Result Status </th>
            <th width="4%">Viva Report Status </th>
            <th width="2%">Action </th>
          </tr>
          <?
			if ($row_cnt > 0 ) {?>
          <? 
				$no=0;
				for ($j=0; $j<$no3; $j++){
				
					$sql3 = "SELECT a.id, a.defense_date, DATE_FORMAT(a.defense_date,'%d-%b-%Y') as defense_date1, 
					DATE_FORMAT(a.defense_stime,'%h:%i%p') as defense_stime,
					DATE_FORMAT(a.defense_etime,'%h:%i%p') as defense_etime, a.venue, a.ref_session_type_id, 
					b.description as ref_session_type_desc
					FROM pg_calendar a
					LEFT JOIN ref_session_type b ON (b.id = a.ref_session_type_id)
					LEFT JOIN pg_viva c ON (c.pg_calendar_id = a.id)
					WHERE a.student_matrix_no = '$studentMatrixNoArray[$j]'
					AND a.thesis_id = '$thesisIdArray[$j]'
					AND a.status = 'A'
					AND a.id = '$pg_calendar_id[$j]'
					ORDER BY a.defense_date ASC";
					
					$result_sql3 = $dba->query($sql3); 
					$dba->next_record();
					$row_cnt_sql3 = mysql_num_rows($result_sql3);
					

					$recommendedIdArray = $dba->f('id');
					$defenseDateArray = $dba->f('defense_date1');
					$defenseSTimeArray = $dba->f('defense_stime');
					$defenseETimeArray = $dba->f('defense_etime');
					$venueArray = $dba->f('venue');
					$sessionTypeArray = $dba->f('ref_session_type_id');
					$sessionTypeDescArray = $dba->f('ref_session_type_desc');
					
					$sqlvivaresult = "SELECT b.id AS pg_eva_viva_detail_id, a.id AS pg_eva_viva_id, b.report_status, 
					DATE_FORMAT(b.submit_date,'%d-%b-%Y %h:%i:%s %p') as submit_date
					FROM pg_evaluation_viva a
					LEFT JOIN pg_evaluation_viva_detail b ON (b.pg_eva_viva_id=a.id)
					WHERE a.student_matrix_no = '$studentMatrixNoArray[$j]'
					AND a.status = 'A'
					AND b.pg_empid_viva = '$user_id'
					AND a.pg_thesis_id = '$thesisIdArray[$j]'
					AND b.id is not null
					AND a.pg_viva_id = '$vivaId[$j]'";
					
					$dbf1 = $dbf;
					$result_sqlvivaresult = $dbf1->query($sqlvivaresult); 
					$dbf1->next_record();
					$pg_eva_viva_detail_id = $dbf1->f('pg_eva_viva_detail_id');					
					
					$sqlvivaresult = "SELECT description from ref_recommendation
					WHERE id = '$resultStatusMain[$j]'
					AND status = 'A'";
					
					$dbf2 = $dbf;
					$result_sqlvivaresult = $dbf2->query($sqlvivaresult); 
					$dbf2->next_record();
					
					$mainDesc = $dbf2->f('description');
					
					$sqlschool = "SELECT description from ref_recommendation
					WHERE id = '$final_result_status[$j]'
					AND status = 'A'";
					
					$dbf3 = $dbf;
					$result_sqlschool = $dbf3->query($sqlschool); 
					$dbf3->next_record();
					
					$schoolDesc = $dbf3->f('description');
					
					
					?>
          <tr>
            <td align="center"><?=$no+1;?>.</td>
            <input type="hidden" name="roleStatusArray<?=$j?>" id="roleStatusArray<?=$j?>" value="<?=$roleStatusArray[$j]; ?>" />
            <input type="hidden" name="thesisIdArray<?=$j?>" id="thesisIdArray<?=$j?>" value="<?=$thesisIdArray[$j]; ?>" />
            <input type="hidden" name="studentMatrixNoArray<?=$j?>" id="studentMatrixNoArray<?=$j?>" value="<?=$studentMatrixNoArray[$j]; ?>" />
            <!--<td align="left"><label><?=$refNoArray[$j]?></label></td>-->
            <td align="left"><label>
              <?=$thesisIdArray[$j]?>
            </label></td>
            <!--<td align="left"><label><?=$thesisTitleArray[$j]?></label></td>-->
            <td align="left"><label>
              <?=$studentNameArray[$j]?>
              <br />
              (
              <?=$studentMatrixNoArray[$j]?>
              )</label></td>
            <td><?=$defenseDateArray?>
              ,
              <?=$defenseSTimeArray?>
              to
              <?=$defenseETimeArray?>
              , <br />
              <?=$venueArray?></td>
            <!--<td align="left"><?=$submit_date[$j]?></td>-->
            <? if(empty($recommendation_id[$j]) && !empty($resultStatusMain[$j]) && empty($final_result_status[$j])) {?>
            <td width="12%" align="left"><?=$mainDesc?>
                <br />
            (Chairman Decision)</td>
            <? } else if(!empty($recommendation_id[$j]) && empty($resultStatusMain[$j])&& empty($final_result_status[$j])) { ?>
            <td width="8%" align="left"><?=$recommDesc[$j]?></td>
            <? } else if(!empty($recommendation_id[$j]) && !empty($resultStatusMain[$j]) && empty($final_result_status[$j])) { ?>
            <td width="7%" align="left"><?=$mainDesc?>
                <br />
            (Chairman Decision)</td>
            <? } else if(!empty($final_result_status[$j])) { ?>
            <td width="6%" align="left"><?=$schoolDesc?>
                <br />
              (School Board Decision)</td>
            <? } else {?>
            <td width="3%" align="center"><?=$result_status[$j]?></td>
            <? } ?>
            <? if(empty($report_status[$j]) && empty($final_result_status[$j])) { $statusCon = 'None'?>
            <td width="3%" align="center"><?=$statusCon?></td>
            <? } else if($report_status[$j] == 'SAV') { $statusCon = 'Save as Draft'?>
            <td width="3%" align="center"><?=$statusCon?></td>
            <? } else { $statusCon = 'Submitted'?>
            <td width="3%" align="center"><?=$statusCon?></td>
            <? } ?>
            <td width="4%" align="center"><? if(empty($report_status[$j])) { ?>
                <input type="button" name="btnAdd" id="btnAdd" value="Add" 
						onclick="javascript:document.location.href='new_viva_report.php?tid=<?=$thesisIdArray[$j]?>&pid=<?=$proposalIdArray[$j]?>&type=<?=$ref_supervisor_type_id[$j]?>&mn=<?=$studentMatrixNoArray[$j]?>&role=<?=$roleStatusArray[$j]?>&pd=<?=$pg_eva_viva_detail_id?>';"/>
                <? } else if($report_status[$j] == 'SAV') { ?>
                <input type="button" name="btnUpdate" id="btnUpdate" value="Update" 
						onclick="javascript:document.location.href='edit_viva_report.php?tid=<?=$thesisIdArray[$j]?>&pid=<?=$proposalIdArray[$j]?>&type=<?=$ref_supervisor_type_id[$j]?>&mn=<?=$studentMatrixNoArray[$j]?>&role=<?=$roleStatusArray[$j]?>&pd=<?=$pg_eva_viva_detail_id?>';"/>
                <? } else if($report_status[$j] == 'SUB') { ?>
                <input type="button" name="btnView" id="btnView" value="View" 
						onclick="javascript:document.location.href='view_viva_report.php?tid=<?=$thesisIdArray[$j]?>&pid=<?=$proposalIdArray[$j]?>&type=<?=$ref_supervisor_type_id[$j]?>&mn=<?=$studentMatrixNoArray[$j]?>&role=<?=$roleStatusArray[$j]?>&pd=<?=$pg_eva_viva_detail_id?>';"/>
                <? } else {  }?>
            </td>
          </tr>
          <?
				$no++;	
				}
				?>
          <?
			}
			else {
			?>
          <table>
            <tr>
              <td>No record found!</td>
            </tr>
          </table>
	      <?
			}?>
        </table>
	</div>
	</fieldset>			
</body>
</html>





