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

$searchThesisId = $_REQUEST['searchThesisId'];
$searchStudent = $_REQUEST['searchStudent'];
$searchRes = $_REQUEST['searchRes'];

	if ($searchThesisId!="") 
	{
		$tmpSearchThesisId = " AND a.pg_thesis_id = '$searchThesisId'";
	}
	else 
	{
		$tmpSearchThesisId="";
	}
	
	if ($searchStudent!="") 
	{
		$tmpSearchStudent = " AND a.student_matrix_no = '$searchStudent'";
	}
	else 
	{
		$tmpSearchStudent="";
	}

	if ($searchRes!="") 
	{
		$tmpSearchRes = " AND a.final_result = '$searchRes'";
	}
	else 
	{
		$tmpSearchtmpSearchResStudentName="";
	}
	
	
	$sql = " SELECT a.pg_thesis_id, a.pg_viva_id, a.id AS evaluationId, a.student_matrix_no, a.reference_no, a.modify_date, 
	a.result_status AS resultFromChair, a.final_result, a.final_result_date, c.thesis_title, c.id AS proposalId, d.description AS resultDesc, f.pg_calendar_id
	FROM pg_evaluation_viva a
	LEFT JOIN pg_thesis b ON (b.id = a.pg_thesis_id)
	LEFT JOIN pg_proposal c ON (c.pg_thesis_id = b.id)
	LEFT JOIN ref_recommendation d ON (d.id = a.result_status)
	LEFT JOIN pg_viva f ON (f.id = a.pg_viva_id)
	WHERE a.result_status IS NOT NULL
	AND c.verified_status IN ('APP','AWC') 
	AND c.status IN ('APP','APC') "
	.$tmpSearchThesisId." "
	.$tmpSearchStudent." "
	.$tmpSearchRes." 
	AND c.archived_status IS NULL 
	AND a.final_result IS NOT NULL";

	$result_sql = $dbg->query($sql); 
	$dbg->next_record();
	$row_cnt = mysql_num_rows($result_sql);

	$pg_thesis_id = Array();
	$pg_viva_id = Array();
	$evaluationId = Array();
	$student_matrix_no = Array();
	$reference_no = Array();
	$modify_date = Array();
	$resultFromChair = Array();
	$final_result = Array();
	$final_result_date = Array();
	$thesis_title = Array();
	$proposalId = Array();
	$resultDesc = Array();
	$pg_calendar_id = Array();
	$thesis_title = Array();
	

	$no=0;
	$no1=0;
	$no3 = 0;

	if ($row_cnt > 0) {
		do {
			$pg_thesis_id[$no]=$dbg->f('pg_thesis_id');
			$pg_viva_id[$no]=$dbg->f('pg_viva_id');
			$evaluationId[$no]=$dbg->f('evaluationId');
			$student_matrix_no[$no]=$dbg->f('student_matrix_no');
			$reference_no[$no]=$dbg->f('reference_no');
			$modify_date[$no]=$dbg->f('modify_date');
			$resultFromChair[$no]=$dbg->f('resultFromChair');
			$final_result[$no]=$dbg->f('final_result');
			$final_result_date[$no]=$dbg->f('final_result_date');
			$thesis_title[$no] = $dbg->f('thesis_title');
			$proposalId[$no] = $dbg->f('proposalId');
			$resultDesc[$no] = $dbg->f('resultDesc');
			$pg_calendar_id[$no] = $dbg->f('pg_calendar_id');
			$thesis_title[$no] = $dbg->f('thesis_title');
						
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
			WHERE matrix_no = '$student_matrix_no[$i]'";
			
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
	<legend><strong>List of VIVA Result </strong></legend>

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
	    <table border="1" style="border-collapse: collapse;border-spacing: 0px;" cellpadding="2" cellspacing="1" width="95%" class="">
          <tr>
            <th width="4%">No</th>
            <!--<th width="7%">Reference No </th>-->
            <th width="14%" align="left">Thesis / Project ID</th>
			<th width="20%" align="left">Thesis Title</th>
            <!--<th width="7%" align="left">Thesis Title</th>-->
            <th width="19%" align="left">Student Name<br />(Student Matrix No)</th>
            <th width="24%" align="left">Session Date</th>
            <!--<th width="17%" align="left">Result Status </th>-->
            <th width="19%" align="left">Final Result Status </th>
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
					WHERE a.student_matrix_no = '$student_matrix_no[$j]'
					AND a.thesis_id = '$pg_thesis_id[$j]'
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
					
					$sqlvivaresult = "SELECT description from ref_recommendation where id = '$final_result[$j]'";
					
					$dbf1 = $dbf;
					$result_sqlvivaresult = $dbf1->query($sqlvivaresult); 
					$dbf1->next_record();
					$descriptionF = $dbf1->f('description');
					if (empty($descriptionF))
					{
						$descriptionF = 'Not decided yet';
					}					
					
					$sqlvivaresult = "SELECT description from ref_recommendation
					WHERE id = '$resultStatusMain[$j]'
					AND status = 'A'";
					
					$dbf2 = $dbf;
					$result_sqlvivaresult = $dbf2->query($sqlvivaresult); 
					$dbf2->next_record();
					
					$mainDesc = $dbf2->f('description');
					
					/*$report_status[$j] = $dbg->f('report_status');
					$result_status[$j] = $dbg->f('result_status');
					$resultStatusMain[$j] = $dbg->f('resultStatusMain');
					$submit_date[$j] = $dbg->f('submit_date');
					$recommDesc[$j]*/
					
					?>
          <tr>
            <td align="center"><strong><?=$no+1;?>.</strong></td>
            <input type="hidden" name="roleStatusArray<?=$j?>" id="roleStatusArray<?=$j?>" value="<?=$roleStatusArray[$j]; ?>" />
            <input type="hidden" name="thesisIdArray<?=$j?>" id="thesisIdArray<?=$j?>" value="<?=$pg_thesis_id[$j]; ?>" />
            <input type="hidden" name="studentMatrixNoArray<?=$j?>" id="studentMatrixNoArray<?=$j?>" value="<?=$student_matrix_no[$j]; ?>" />
            <!--<td align="left"><label><?=$refNoArray[$j]?></label></td>-->
            <td align="left"><label>
              <?=$pg_thesis_id[$j]?>
            </label></td>
            <td align="left"><label><?=$thesis_title[$j]?></label></td>
            <td align="left"><label>
              <?=$studentNameArray[$j]?>
              <br />
              (
              <?=$student_matrix_no[$j]?>
              )</label></td>
            <td align="left"><?=$defenseDateArray?>
              ,
              <?=$defenseSTimeArray?>
              to
              <?=$defenseETimeArray?>
              , <br />
              <?=$venueArray?></td>
            <!--$modify_date[$j]-->
             <!--<td width="17%" align="left"><?=$resultDesc[$j]?></td>-->
            <td width="19%"align="left"><?=$descriptionF?></td>
            
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
	</fieldset>			
</body>
</html>




