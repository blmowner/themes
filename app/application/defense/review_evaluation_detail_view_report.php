<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: review_evaluation_detail_view.php
//
// Created by: Zuraimi
// Created Date: 28-Jul-2015
// Modified by: Zuraimi
// Modified Date: 28-Jul-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

session_start();
$user_id=$_SESSION['user_id'];
$evaluationId=$_GET['id'];
$calendarId=$_GET['cid'];
$studentMatrixNo=$_GET['mn'];
$thesisId=$_GET['tid'];
$proposalId=$_GET['pid'];
$referenceNo=$_GET['ref'];
$referenceNoDefence=$_GET['refd'];
$supervisorTypeId=$_GET['rol'];
$defenseId=$_GET['did'];
$marksGivenAlert = false;
$overallRatingAlert = false;


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

/*function runnum2($column_name, $tblname) 
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
}*/

if(isset($_POST['btnSave']) && ($_POST['btnSave'] <> ""))
{
	$evaluationDetailStatus = $_POST['evaluationDetailStatus'];
	$overallStyleIdArray = $_POST['overallStyleIdArray'];
	$addReportRating = $_POST['addReportRating'];
	$overallStyleComment = $_POST['overallStyleComment'];
	
	$majorRevision = $_POST['majorRevision'];
	$otherComments = $_POST['otherComments'];
	$defenseMarksCb = $_POST['defenseMarks_cb'];
	$overallCommentsArray = $_POST['overallCommentsArray'];
	$overallCommentsIdArray = $_POST['overallCommentsIdArray'];
	$addOverallRating = $_POST['addOverallRating'];

	$msg = Array();
	if (empty($defenseMarksCb)) { 
		$msg[] = "<div class=\"error\"><span>Please provide your recommendation as required in <strong>Mark Given</strong> section.</span></div>";
		$marksGivenAlert = true;
	}
	
	$lenght_arr = count($addOverallRating);
	for ($i=0;$i<$lenght_arr;$i++) 
	{
		if ($addOverallRating[$i]== "") {
			$j = $i + 1;
			$msg[] = "<div class=\"error\"><span>Please select Rating for item <strong>$j</strong> in <strong>Overall Comment on Student</strong> section. It cannot be empty.</span></div>";
			$overallRatingAlert = true;
		}
	}
	
	$curdatetime = date("Y-m-d H:i:s");	
			
	if(empty($msg)) 
	{
		if ($evaluationDetailStatus=="IN1") {
			$sql7 = "SELECT pg_eval_id, pg_employee_empid, major_revision, other_comment, 
			IFNULL(responded_date,'0000-00-00 00:00:00') as responded_date, status, 
			insert_by, IFNULL(insert_date,'0000-00-00 00:00:00') as insert_date, 
			modify_by, IFNULL(modify_date,'0000-00-00 00:00:00') as modify_date
			FROM pg_evaluation_detail
			WHERE pg_eval_id = '$evaluationId'
			AND pg_employee_empid = '$user_id'
			AND status = 'IN1'";
			$dbg->query($sql7);
			$dbg->next_record();
			
			$theEvaluatuionId = $dbg->f('pg_eval_id');
			$theEmployeeId = $dbg->f('pg_employee_empid');
			$theMajorRevision = $dbg->f('major_revision');
			$theOtherComment = $dbg->f('other_comment');
			$theRespondedDate = $dbg->f('responded_date');
			$theStatus = $dbg->f('status');
			$theInsertBy = $dbg->f('insert_by');
			$theInsertDate = $dbg->f('insert_date');
			$theModifyBy = $dbg->f('modify_by');
			$theModifyDate = $dbg->f('modify_date');
			
			$newEvaluationDetailId = runnum('id','pg_evaluation_detail');
			
	}		

if(isset($_POST['btnSubmit']) && ($_POST['btnSubmit'] <> ""))
{
	$overallStyleIdArray = $_POST['overallStyleIdArray'];
	$addReportRating = $_POST['addReportRating'];
	$overallStyleComment = $_POST['overallStyleComment'];
	
	$majorRevision = $_POST['majorRevision'];
	$otherComments = $_POST['otherComments'];
	$defenseMarksCb = $_POST['defenseMarks_cb'];
	$overallCommentsArray = $_POST['overallCommentsArray'];
	$addOverallRating = $_POST['addOverallRating'];
	$msg = Array();
	$msg = Array();
	
	if (empty($defenseMarksCb)) { 
		$msg[] = "<div class=\"error\"><span>Please provide your recommendation as required in <strong>Mark Given</strong> section.</span></div>";
		$marksGivenAlert = true;
	}
	
	$lenght_arr = count($addOverallRating);
	for ($i=0;$i<$lenght_arr;$i++) 
	{
		if ($addOverallRating[$i]== "") {
			$j = $i + 1;
			$msg[] = "<div class=\"error\"><span>Please select Rating for item <strong>$j</strong> in <strong>Overall Comment on Student</strong> section. It cannot be empty.</span></div>";
			$overallRatingAlert = true;
		}
	}
	
	if(empty($msg)) 
	{
		if ($evaluationDetailStatus=="IN1") {
			$sql7 = "SELECT pg_eval_id, pg_employee_empid, major_revision, other_comment, 
			IFNULL(responded_date,'0000-00-00 00:00:00') as responded_date, status, 
			insert_by, IFNULL(insert_date,'0000-00-00 00:00:00') as insert_date, 
			modify_by, IFNULL(modify_date,'0000-00-00 00:00:00') as modify_date
			FROM pg_evaluation_detail
			WHERE pg_eval_id = '$evaluationId'
			AND pg_employee_empid = '$user_id'
			AND status = 'IN1'";
			$dbg->query($sql7);
			$dbg->next_record();
			
			$theEvaluatuionId = $dbg->f('pg_eval_id');
			$theEmployeeId = $dbg->f('pg_employee_empid');
			$theMajorRevision = $dbg->f('major_revision');
			$theOtherComment = $dbg->f('other_comment');
			$theRespondedDate = $dbg->f('responded_date');
			$theStatus = $dbg->f('status');
			$theInsertBy = $dbg->f('insert_by');
			$theInsertDate = $dbg->f('insert_date');
			$theModifyBy = $dbg->f('modify_by');
			$theModifyDate = $dbg->f('modify_date');
			
			$newEvaluationDetailId = runnum('id','pg_evaluation_detail');
			
			$sql9 = "INSERT INTO pg_evaluation_detail
			(id, pg_eval_id, pg_employee_empid, major_revision, other_comment, responded_date, ref_defense_marks_id,
			status, insert_by, insert_date, modify_by, modify_date)
			VALUES ('$newEvaluationDetailId','$evaluationId','$theEmployeeId','$majorRevision','$otherComments',
			'$curdatetime','$defenseMarksCb','REC','$theInsertBy','$theInsertDate','$user_id','$curdatetime')";
			$dbg->query($sql9);
			
			$sql8 = "UPDATE pg_evaluation_detail
			SET modify_by = '$user_id', modify_date = '$curdatetime', archived_status = 'ARC', archived_date = '$curdatetime'
			WHERE pg_eval_id = '$evaluationId'
			AND pg_employee_empid = '$user_id'
			AND status = 'IN1'";
					
			$dbg->query($sql8);
			
			while (list ($key,$val) = @each ($overallStyleIdArray)) 
			{
				$curdatetime = date("Y-m-d H:i:s");	
				$newEvaluationStyleId = runnum('id','pg_evaluation_style');
				$sql11 = "INSERT INTO pg_evaluation_style
				(id, pg_evaldetail_id, ref_overall_style_id, ref_report_rating_id, comments, 
				status, insert_by, insert_date, modify_by, modify_date)
				VALUES ('$newEvaluationStyleId','$newEvaluationDetailId','$overallStyleIdArray[$key]','$addReportRating[$key]',
				'$overallStyleComment[$key]','A','$user_id','$curdatetime','$user_id','$curdatetime')";
				$dbg->query($sql11);
			}
			while (list ($key,$val) = @each ($overallCommentsArray)) 
			{
				$newEvaluationOverallId = runnum('id','pg_evaluation_overall');
				$sql12 = "INSERT INTO pg_evaluation_overall
				(id, pg_evaldetail_id, ref_overall_comments_id, ref_overall_rating_id, 
				status, insert_by, insert_date, modify_by, modify_date)
				VALUES ('$newEvaluationOverallId','$newEvaluationDetailId','$overallCommentsArray[$key]','$addOverallRating[$key]',
				'A','$user_id','$curdatetime','$user_id','$curdatetime')";
				$dbg->query($sql12);
			}
		
			if ($supervisorTypeId =="EC") {
				$sql10 = "UPDATE pg_evaluation
				SET ref_defense_marks_id = '$defenseMarksCb', status = 'REC',
				respond_status = 'Y', modify_by = '$user_id', modify_date = '$curdatetime'
				WHERE id = '$evaluationId'
				AND reference_no = '$referenceNo'
				AND status = 'IN1'
				AND archived_status is null";
			
				$dbg->query($sql10);										
			}
			$msg[] = "<div class=\"success\"><span>Defence Proposal Evaluation Report has been submitted successfully.</span></div>";
						
		}
		else if ($evaluationDetailStatus=="SV1") {
			$sql13 = "UPDATE pg_evaluation_detail
			SET major_revision = '$majorRevision', other_comment = '$otherComments', responded_date = '$curdatetime',
			ref_defense_marks_id = '$defenseMarksCb', status = 'REC', modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE id = '$evaluationDetailId'
			AND pg_eval_id = '$evaluationId'
			AND pg_employee_empid = '$user_id'
			AND status = 'SV1'
			AND archived_status IS NULL";
			$dbg->query($sql13);
			
			while (list ($key,$val) = @each ($overallStyleIdArray)) 
			{
				$curdatetime = date("Y-m-d H:i:s");	
				$sql14 = "UPDATE pg_evaluation_style
				SET ref_report_rating_id = '$addReportRating[$key]', comments = '$overallStyleComment[$key]',
				modify_by = '$user_id', modify_date = '$curdatetime'
				WHERE pg_evaldetail_id = '$evaluationDetailId'
				AND ref_overall_style_id = '$overallStyleIdArray[$key]'
				AND status = 'A'";
				$dbg->query($sql14);
			} 
			while (list ($key,$val) = @each ($overallCommentsArray)) 
			{
				$curdatetime = date("Y-m-d H:i:s");
				$sql15 = "UPDATE pg_evaluation_overall
				SET ref_overall_rating_id = '$addOverallRating[$key]', modify_by = '$user_id', modify_date = '$curdatetime'
				WHERE pg_evaldetail_id = '$evaluationDetailId'
				AND ref_overall_comments_id = '$overallCommentsArray[$key]'
				AND status = 'A'";
				$dbg->query($sql15);
			}
			
			if ($supervisorTypeId =="EC") {
				$sql10 = "UPDATE pg_evaluation
				SET ref_defense_marks_id = '$defenseMarksCb', status = 'REC',
				respond_status = 'Y', modify_by = '$user_id', modify_date = '$curdatetime'
				WHERE id = '$evaluationId'
				AND reference_no = '$referenceNo'
				AND status = 'IN1'
				AND archived_status is null";
			
				$dbg->query($sql10);										
			}
			$msg[] = "<div class=\"success\"><span>Defence Proposal Evaluation Report has been submitted successfully.</span></div>";	
		}
	}
}
}
}

$sql2 = "SELECT b.id as evaluation_detail_id, 
DATE_FORMAT(b.responded_date, '%d-%b-%Y %h:%i %p') as responded_date,  
a.student_matrix_no, a.pg_thesis_id, a.pg_proposal_id, 
b.status as evaluation_detail_status, 
a.status as evaluation_status, c2.description as evaluation_desc,
a.insert_by, a.insert_date, a.modify_by, a.modify_date,	 
c.description as evaluation_detail_desc,
b.major_revision, b.other_comment, b.ref_defense_marks_id
FROM pg_evaluation a
LEFT JOIN pg_evaluation_detail b ON (b.pg_eval_id = a.id)
LEFT JOIN ref_proposal_status c ON (c.id = b.status)
LEFT JOIN ref_proposal_status c2 ON (c2.id = a.status)
WHERE a.id = '$evaluationId'
AND a.student_matrix_no = '$studentMatrixNo'
AND a.pg_thesis_id = '$thesisId'
AND a.pg_proposal_id = '$proposalId'
AND a.reference_no = '$referenceNo'
AND b.pg_employee_empid = '$user_id'
AND a.archived_status is null
AND b.archived_status is null";

$result2 = $dbg->query($sql2); 
$dbg->next_record();

$evaluationStatus=$dbg->f('evaluation_status');
$evaluationDesc=$dbg->f('evaluation_desc');
$evaluationDetailStatus1=$dbg->f('evaluation_detail_status');
$evaluationDetailDesc1=$dbg->f('evaluation_detail_desc');
$respondedDate=$dbg->f('responded_date');
$evaluationDetailId=$dbg->f('evaluation_detail_id');
$majorRevision1=$dbg->f('major_revision');
$otherComment1=$dbg->f('other_comment');
$refDefenseMarksId1=$dbg->f('ref_defense_marks_id');
$row_cnt2 = mysql_num_rows($result2);

$sql2A = "SELECT a.id, a.pg_evaldetail_id, a.ref_overall_style_id, a.ref_report_rating_id, a.comments
FROM pg_evaluation_style a
LEFT JOIN ref_overall_style d ON (d.id = a.ref_overall_style_id)
LEFT JOIN pg_evaluation_detail c ON (c.id = a.pg_evaldetail_id)
LEFT JOIN pg_evaluation b ON (b.id = c.pg_eval_id)
WHERE b.id = '$evaluationId'
AND b.pg_thesis_id = '$thesisId'
AND b.pg_proposal_id = '$proposalId'
AND b.reference_no = '$referenceNo'
AND c.pg_employee_empid = '$user_id'
AND a.status = 'A'
ORDER BY d.seq";

$result2A = $dbg->query($sql2A); 
$dbg->next_record();
$row_cnt2A = mysql_num_rows($result2A);
$i=0;
$esIdArray = Array();
$esEvalDetailIdArray = Array();
$esRefOverallStyleIdArray = Array();
$esRefReportRatingIdArray = Array();
$esCommentsArray = Array();

do {
	$esIdArray[$i] = $dbg->f('id');
	$esEvalDetailIdArray[$i] = $dbg->f('pg_evaldetail_id');
	$esRefOverallStyleIdArray[$i] = $dbg->f('ref_overall_style_id');
	$esRefReportRatingIdArray[$i] = $dbg->f('ref_report_rating_id');
	$esCommentsArray[$i] = $dbg->f('comments');
	$i++;
} while ($dbg->next_record());

$sql2B = "SELECT a.id, a.pg_evaldetail_id, a.ref_overall_comments_id, a.ref_overall_rating_id
FROM pg_evaluation_overall a
LEFT JOIN pg_evaluation_detail c ON (c.id = a.pg_evaldetail_id)
LEFT JOIN pg_evaluation b ON (b.id = c.pg_eval_id)
LEFT JOIN ref_overall_comments d ON (d.id = a.ref_overall_comments_id)
WHERE b.id = '$evaluationId'
AND b.pg_thesis_id = '$thesisId'
AND b.pg_proposal_id = '$proposalId'
AND b.reference_no = '$referenceNo'
AND c.pg_employee_empid = '$user_id'
AND a.status = 'A'
ORDER BY d.seq";

$result2B = $dbg->query($sql2B); 
$dbg->next_record();
$row_cnt2B = mysql_num_rows($result2B);
$i=0;
$eoIdArray = Array();
$eoEvalDetailIdArray = Array();
$eoRefOverallCommentsIdArray = Array();
$eoRefOverallRatingIdArray = Array();

do {
	$eoIdArray[$i] = $dbg->f('id');
	$eoEvalDetailIdArray[$i] = $dbg->f('pg_evaldetail_id');
	$eoRefOverallCommentsIdArray[$i] = $dbg->f('ref_overall_comments_id');
	$eoRefOverallRatingIdArray[$i] = $dbg->f('ref_overall_rating_id');
	$i++;
} while ($dbg->next_record());
?>

<? 
if (substr($user_id,0,2) != '07') { 
	$dbConn=$dbc; 
} 
else { 
	$dbConn=$dbc1; 
}

$sql_rate = "SELECT description 
	FROM ref_report_rating 
	WHERE id = 'RPR0002'";
	
	//$dbConn->query($sql_rate);
	$dbg->query($sql_rate);
	$dbg->next_record();
	$not_appropriate= $dbg->f('description');



$sql_personal="SELECT s.student_status,s.matrix_no,s.name,s.ic_passport,s.address_aa,s.address_ab,s.city_a,s.state_a,
		s.postcode_a,s.country_a,s.handphone,s.house,s.office,s.email,s.address_bb,s.address_ba,s.city_b,s.state_b,s.postcode_b,
		s.country_b,s.xgender,sp.intake_no,sp.program_code,po.code,po.program_e,s.skype_id, sp.manage_by_whom
		FROM student s
		LEFT JOIN student_program sp ON (sp.matrix_no=s.matrix_no) 
		LEFT JOIN pro_off po ON (po.code=sp.program_code) 
		WHERE (sp.program_code LIKE 'M%' OR sp.program_code LIKE 'P%' AND sp.program_code NOT LIKE 'MBBS%')
		AND s.matrix_no = '$studentMatrixNo'";
	
$dbConn->query($sql_personal);
$row_personal = $dbConn->fetchArray(); //echo $sql_personal;

$matrix_no=$row_personal['matrix_no'];
$skype_id=$row_personal['skype_id'];
$name=$row_personal['name'];
$program_code=$row_personal['program_code'];
$program_e=$row_personal['program_e'];
$ic_passport=$row_personal['ic_passport'];
$address_aa=$row_personal['address_aa'];
$address_ab=$row_personal['address_ab'];
$city_a=$row_personal['city_a'];
$state_a=$row_personal['state_a'];
$postcode_a=$row_personal['postcode_a'];
$country_a=$row_personal['country_a'];
$address_bb=$row_personal['address_bb'];
$address_ba=$row_personal['address_ba'];
$city_b=$row_personal['city_b'];
$state_b=$row_personal['state_b'];
$postcode_b=$row_personal['postcode_b'];
$country_b=$row_personal['country_b'];
$citizenship=$row_personal['citizenship'];
$gender=$row_personal['xgender'];
$intake_no=$row_personal['intake_no'];
$mobile=$row_personal['handphone'];
$house=$row_personal['house'];
$office=$row_personal['office'];
$email=$row_personal['email'];
$student_status=$row_personal["student_status"];
$manageby=$row_personal["manage_by_whom"];


$sql_thesis="SELECT pa.verified_by,
pa.verified_date, pa.verified_status, pa.status,pa.thesis_title,pa.id,pt.id AS thesis_id, 
pt.ref_thesis_status_id_proposal, pt.ref_thesis_status_id_defense, pt.ref_thesis_status_id_work, pt.ref_thesis_status_id_evaluation, 
pt.ref_thesis_status_id_final, pt.ref_thesis_status_id_senate, rps1.description AS proposal_desc, rps2.description AS defense_desc, 
rps3.description AS work_desc, rps4.description AS evaluation_desc, rps5.description AS final_desc, 
rps6.description AS senate_desc, DATE_FORMAT(ppa.endorsed_date,'%d-%b-%Y') AS endorsed_date
FROM pg_thesis pt
LEFT JOIN pg_proposal pa ON (pa.pg_thesis_id = pt.id)
LEFT JOIN pg_proposal_approval ppa ON (ppa.id = pa.pg_proposal_approval_id)
LEFT JOIN ref_thesis_status rps1 ON (rps1.id = pt.ref_thesis_status_id_proposal) 
LEFT JOIN ref_thesis_status rps2 ON (rps2.id = pt.ref_thesis_status_id_defense) 
LEFT JOIN ref_thesis_status rps3 ON (rps3.id = pt.ref_thesis_status_id_work) 
LEFT JOIN ref_thesis_status rps4 ON (rps4.id = pt.ref_thesis_status_id_evaluation) 
LEFT JOIN ref_thesis_status rps5 ON (rps5.id = pt.ref_thesis_status_id_final) 
LEFT JOIN ref_thesis_status rps6 ON (rps6.id = pt.ref_thesis_status_id_senate) 
WHERE pt.student_matrix_no = '$studentMatrixNo'
AND pt.archived_status is null
ORDER BY pa.id DESC"; 

$db->query($sql_thesis);
$row_thesis=$db->fetchArray(); //echo $sql_thesis;


$cases=$row_thesis["thesis_type"];
$introduction=$row_thesis["introduction"];
$objective=$row_thesis["objective"];
$description=$row_thesis["description"];
$proposal_status=$row_thesis["status"];
$thesis_title=$row_thesis["thesis_title"];
$verifiedStatus=$row_thesis['verified_status'];
$status=$row_thesis['status'];
$thesis_id=$row_thesis['thesis_id'];
$supervisor_id=$row_thesis['supervisor_id'];
$supervisor_name=$row_thesis['name'];
$hp_no=$row_thesis['hp_no'];
$ref_thesis_status_id_proposal=$row_thesis['ref_thesis_status_id_proposal'];
$endorsed_date=$row_thesis['endorsed_date'];
$ref_thesis_status_id_defense=$row_thesis['ref_thesis_status_id_defense'];
$ref_thesis_status_id_work=$row_thesis['ref_thesis_status_id_work'];
$ref_thesis_status_id_evaluation=$row_thesis['ref_thesis_status_id_evaluation'];
$ref_thesis_status_id_final=$row_thesis['ref_thesis_status_id_final'];
$ref_thesis_status_id_senate=$row_thesis['ref_thesis_status_id_senate'];
$proposal_desc=$row_thesis['proposal_desc'];
$defense_desc=$row_thesis['defense_desc'];
$work_desc=$row_thesis['work_desc'];
$evaluation_desc=$row_thesis['evaluation_desc'];
$final_desc=$row_thesis['final_desc'];
$senate_desc=$row_thesis['senate_desc'];

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
	<style type="text/css">
	
	.checkboxes label {
     float:right;
    padding-left: 50px;
	}
	</style>
	
	<body>	
<form id="form1" name="form1" method="post" enctype="multipart/form-data">	
	<table border="0">
			<tr>
				<td rowspan="2"><img border="0" align="left" img src="../../../theme/images/msuLogo.gif" alt="MSU logo"  width="275" height="100" style="width:300px;height:100px;"></td>
				<td><font size="6"><b>Management and Science University</b></font></td>
			</tr>
									
  </table>

        <table border="0" align="right">
			<tr>
				<td><strong><span style="color:#FF0000"><i>Private & Confidential</i></span></strong></td>
			</tr>
			
		</table>
		  
		<table width="950" border="1">
			<tr>
				<td><font size="5"><strong>Defence Proposal Report</strong></font></td>
			</tr>
			
			
  </table>
</br>


		<table width="100%" border="2" cellpadding="4" cellspacing="2" style="border-collapse:collapse;" class="thetable">
		<tr>
		
		
	<!--must redi-->
				<th> <span style="float:left;">Period / Month </span></th>
				
		<?
			$sql111="SELECT registered_date as period_month FROM student 
				WHERE matrix_no ='$studentMatrixNo'";
			if (substr($studentMatrixNo,0,2) != '07') { 
				$dbConnStudent= $dbc; 
			} 
			else { 
				$dbConnStudent=$dbc1; 
			}
			$result1 = $dbConnStudent->query($sql111); 
			$dbConnStudent->next_record();
			$period_month=$dbConnStudent->f('period_month');
			?>
				<td width="412"><span style="float:left;"><?=$period_month?></span></td>
				<input type="hidden" name="sname" id="sname" value="<?=$period_month; ?>">
				<th width="74"><span style="float:left;">Office Use</span></th>
				<td width="464"></td>
		</tr>
		<!--Student Name SQL-->
		<tr>
				<th><span style="float:left;">Name as per Identify Card</span></th>
				<td colspan="3"><?=$name?></td>
		</tr>
			
		<tr>
				<th width="211"><span style="float:left;">Email</span></th>
				<td colspan ="3"><?=$email?></td>
		</tr>
		<tr>
				<th width="211"><span style="float:left;">Matrix Number</span></th>
				<td colspan ="3"><?=$matrix_no?></td>
		</tr>
		<tr>
				<th width="211"><span style="float:left;">Program</span></th>
				<td colspan ="3"><input type="hidden" name="program_code" size="15" id="program_code" value="<?=$program_code?>" disabled="disabled"/><?=$program_code?> - <input type="hidden" name="program_e" size="30" id="program_e" value="<?=$program_e?>" disabled="disabled"/><?=$program_e?></td>
		</tr>
		<tr>
				<th width="211"><span style="float:left;">Title of Thesis</span></th>
				<td colspan = "3"><?=$thesisId?> - <input type="hidden" name="thesis_title" size="30" id="thesis_title" value="<?=$thesis_title?>" disabled="disabled"/><?=$thesis_title?></td>
		</tr>
		<?
			$sql22 = "SELECT registered_date AS start_date
			FROM student
			WHERE matrix_no = '$studentMatrixNo'";
			if (substr($studentMatrixNo,0,2) != '07') { 
				$dbConnStudent= $dbc; 
			} 
			else { 
				$dbConnStudent=$dbc1; 
			}
			$result1 = $dbConnStudent->query($sql22); 
			$dbConnStudent->next_record();
			$ssdate=$dbConnStudent->f('start_date');
		?>
		<tr>
			<th width="211"> <span style="float:left;">Date of Programme Started</span></th>
			<td colspan = "3"><?=$ssdate?></td>
		</tr>	
			
		<tr>
			<th width="211"><p align="left">Name of 1. Supervisor and 2. Co. Supervisor</p></th>
			<td colspan = "3">
			
			<?php				
				$sql="SELECT  a.pg_employee_empid, d.description as supervisor_type
				FROM pg_supervisor a 
				LEFT JOIN ref_supervisor_type d ON (d.id = a.ref_supervisor_type_id) 
				LEFT JOIN pg_thesis f ON (f.student_matrix_no = a.pg_student_matrix_no)
				LEFT JOIN pg_proposal g ON (g.pg_thesis_id = f.id)
				WHERE a.pg_student_matrix_no='$studentMatrixNo' 
				AND a.ref_supervisor_type_id in ('SV','CS')
				AND a.acceptance_status is not null
				AND g.verified_status in ('APP','AWC')
				AND g.status in ('APP','APC')
				AND g.archived_status IS NULL
				AND a.status='A'
				ORDER BY d.seq, a.ref_supervisor_type_id";
				
				$result = $db_klas2->query($sql); //echo $sql;
				$varRecCount=0;	
			
					while($row = mysql_fetch_array($result)) 		
					{ 
						$sqlSupervisor="SELECT  b.name, c.id, c.description,b.mobile, b.email, b.skype_id
							FROM new_employee b 
							LEFT JOIN dept_unit c ON (c.id = b.unit_id) 
							WHERE b.empid= '".$row["pg_employee_empid"]."' ";
						if (substr($row["pg_employee_empid"],0,3) != 'S07') 
						{ 
							$dbConnStaff=$dbc; 				
						} 						
						else 
						{ 							
							$dbConnStaff=$dbc1; 						
						}
						$result1 = $dbConnStaff->query($sqlSupervisor);
						$row_supervise=$dbConnStaff->fetchArray();
						
						$name=$row_supervise["name"];
						$id1=$row_supervise["id"];
												
						$varRecCount++;
			?>		
	 
			<?=$varRecCount;?>
			. 
			<?=$name;?>
			<br>
			(
			<?=$row["pg_employee_empid"];?>
			)
			  <p>
			    		        <?php  
					}
					?>
              </p></td>
	  	  </tr>
		</table>
		</br>
		
<?	
	$sql18 = "SELECT id, rate, description
	FROM ref_overall_rating
	WHERE status = 'A'								
	ORDER BY seq";
	
	$result18 = $db->query($sql18);
	$db->next_record();
	
	$overallRatingIdArray = Array();
	$overallRatingRateArray = Array();
	$overallRatingDescArray = Array();
	$i=0;
	do {
		$overallRatingIdArray[$i] = $db->f('id');
		$overallRatingRateArray[$i] = $db->f('rate');
		$overallRatingDescArray[$i] = $db->f('description');
		$i++;
	} while ($db->next_record());
	$row_cnt18 = mysql_num_rows($result18);
	
	$sql14 = "SELECT id, description, rating_status
	FROM ref_overall_style
	WHERE status = 'A'
	ORDER by seq";
	
	$result14 = $db->query($sql14);
	$db->next_record();
	
	$overallStyleIdArray = Array();
	$overallStyleDescArray = Array();
	$overallStyleRatingStatusArray = Array();
	
	$i=0;
	$inc = 0;
	do {
		$overallStyleIdArray[$i] = $db->f('id');
		$overallStyleDescArray[$i] = $db->f('description');
		$overallStyleRatingStatusArray[$i] = $db->f('rating_status');
		$i++;
		$inc++;
	} while ($db->next_record());
	
	$row_cnt14 = mysql_num_rows($result14);
	?>
	
	<fieldset>
		<legend><h3><strong>OVERALL STYLE AND ORGANIZATION</strong></h3></legend>
		<table>
			<tr><td><label><em>Please tick at appropriate number. Please state "not applicable N/A " where appropriate.<em></label></td></tr>
		</table>
		<table width="85%" border="1" cellpadding="3" cellspacing="1" style="border-collapse:collapse;" class="thetable">			
			<tr>
				<th width="5%">No</th>					
				<th width="30%" align="left">Item Description</th>
				<th width="50%" align="left">Rating</th>
				<!--<th width="30%" align="left">Comments</th>-->
			</tr>
			<?
			for ($j=0;$j<$row_cnt14;$j++) {
				?>
				<tr>
					<td align="center"><a href="javascript:void(0);" onMouseOver="toolTip('<?=$overallStyleIdArray[$j];?>', 100)" onMouseOut="toolTip()"><?=$j+1?>.</a></td>
					<td><?=$overallStyleDescArray[$j]?></td>
					<input type="hidden" name="overallStyleIdArray[]" id="overallStyleIdArray" value="<?=$overallStyleIdArray[$j];?>"></input>
					<?
					$sql15 = "SELECT id, rate, description
					FROM ref_report_rating
					WHERE rating_status = '$overallStyleRatingStatusArray[$j]'
					AND status = 'A'								
					ORDER BY seq";
					
					$result15 = $db->query($sql15);
					$db->next_record();
					
					$reportRatingIdArray = Array();
					$reportRatingRateArray = Array();
					$reportRatingDescArray = Array();
					$i=0;
					do {
						$reportRatingIdArray[$i] = $db->f('id');
						$reportRatingRateArray[$i] = $db->f('rate');
						$reportRatingDescArray[$i] = $db->f('description');
						$i++;
					} while ($db->next_record());
					$row_cnt15 = mysql_num_rows($result15);	

					if ($row_cnt2A > 0) {
						if ($row_cnt15 > 0) {
						?>
						<td>
						<?
							for ($k1=0;$k1<$row_cnt15;$k1++) {
								if ($reportRatingRateArray[$k1]==1) {
								?><label><span style="float:left;">1-<?=$reportRatingDescArray[$k1]?></span></label> <?
								}
								else
									if($reportRatingRateArray[$k1]==5) {
								?><label><span style="float:right;">5-<?=$reportRatingDescArray[$k1]?></span></label> <?
								}
							}
							?>
							<br>
							<br />
							<div class="checkboxes">
							<?
							for ($i=0;$i<$row_cnt2A;$i++) {
								if ($overallStyleIdArray[$j] == $esRefOverallStyleIdArray[$i]) {
									for ($k=0;$k<$row_cnt15;$k++) {
										if ($reportRatingIdArray[$k] == $esRefReportRatingIdArray[$i]) {	
											if ($reportRatingRateArray[$k]==0) {?> 
												<label for="input1"><input name="myCheckbox[]" type="checkbox" value="<?=$reportRatingIdArray[$k]?>" disabled="disabled" checked="true"/><?=$reportRatingDescArray[$k]?>
												<?}
											else {
												if ($reportRatingRateArray[$k]==1 || $reportRatingRateArray[$k]==5) {?>
													<label for="input2"><input name="myCheckbox[]" type="checkbox" value="<?=$reportRatingIdArray[$k]?>" disabled="disabled" checked="true"/><?=$reportRatingRateArray[$k]?>
												<?}
												else {
													?>
													<label for="input3"><input name="myCheckbox[]" type="checkbox" value="<?=$reportRatingIdArray[$k]?>" disabled="disabled" checked="true"/><?=$reportRatingRateArray[$k]?>								
													<?
												}
											}
										}
										else {
											if ($reportRatingRateArray[$k]==0) {?>
												<label for="input4"><input name="myCheckbox[]" type="checkbox" value="<?=$reportRatingIdArray[$k]?>" disabled="disabled"/><?=$reportRatingDescArray[$k]?>
												<?}
											else {
												if ($reportRatingRateArray[$k]==1 || $reportRatingRateArray[$k]==5) {?>
													<label for="input5"><input name="myCheckbox[]" type="checkbox" value="<?=$reportRatingIdArray[$k]?>" disabled="disabled"/><?=$reportRatingRateArray[$k]?>
												<?}
												else {
													?>
													<label for="input6"><input name="myCheckbox[]" type="checkbox" value="<?=$reportRatingIdArray[$k]?>" disabled="disabled"/><?=$reportRatingRateArray[$k]?>									
													<?
												}
											}
										}
									}
								}
							}
						?>
						<br>
						<br />
						</div>
						<br />
						
						<label><strong>Comments : </strong><?=$esCommentsArray[$j];?></label></td>
						<? }
						else {
							?>
							<td><label><?=$esCommentsArray[$j];?></label></td><?
						}
					}
					else {
						if ($row_cnt15 > 0) {
						?>
						<td><select name="addReportRating[]" id="addReportRating" disabled="true">
						<?								
							for ($i=0;$i<$row_cnt15;$i++) {
								if ($reportRatingIdArray[$i] == $_REQUEST['addReportRating'][$j]) {
									if ($reportRatingRateArray[$i]==0) {?>
										<option value="<?=$reportRatingIdArray[$i]?>" selected="selected"><?=$reportRatingDescArray[$i]?></option>
										<?}
									else {
										if ($reportRatingRateArray[$i]==1 || $reportRatingRateArray[$i]==5) {?>
										<option value="<?=$reportRatingIdArray[$i]?>" selected="selected"><?=$reportRatingRateArray[$i]?> - <?=$reportRatingDescArray[$i]?></option>
										<?}
										else {
											?>
											<option value="<?=$reportRatingIdArray[$i]?>" selected="selected"><?=$reportRatingRateArray[$i]?></option>									
											<?
										}
									}
								}
								else {
									if ($reportRatingRateArray[$i]==0) {?>
										<option value="<?=$reportRatingIdArray[$i]?>"><?=$reportRatingDescArray[$i]?></option>
										<?}
									else {
										if ($reportRatingRateArray[$i]==1 || $reportRatingRateArray[$i]==5) {?>
										<option value="<?=$reportRatingIdArray[$i]?>"><?=$reportRatingRateArray[$i]?> - <?=$reportRatingDescArray[$i]?></option>
										<?}
										else {
											?>
											<option value="<?=$reportRatingIdArray[$i]?>"><?=$reportRatingRateArray[$i]?></option>									
											<?
										}
									}
								}
							}
						?>
						</select></td>
						<? }
						else {
							?>
							<td></td><?
						}
						}
					}?>
				</tr>
				
			
		</table>
  </fieldset>	
	
  


</br>

<h3><label>MAJOR REVISION REQUIRED (<em>if any</em>)</label></h3>

	<table>
		<tr>
		<? if (empty($majorRevision1)) { 
				?><td>None</td> 
		<? }
			else {?>
			<td><?=$majorRevision1?></td>
			<? }?>
		</tr>
  	</table>
  </br>
	<h3><label>OTHER COMMENTS</label></h3>

	<table>
		<tr>
		<? if (empty($otherComment1)) { 
				?><td>None</td>
		<? }
		else {?>
		
			<td><?=$otherComment1?></td>
			<? }?>
		</tr>
	</table>
	</br>
	<p><h3><label>MARK GIVEN</label></H3></p>

		<?
			$sql16 = "SELECT id, description, remarks
			FROM ref_defense_marks
			WHERE status = 'A'								
			ORDER BY seq";
		
			$result16 = $db->query($sql16);
			$db->next_record();
		
			$defenseMarksIdArray = Array();
			$defenseMarksDescArray = Array();
			$defenseMarksRemarksArray = Array();
			$i=0;
			do {
				$defenseMarksIdArray[$i] = $db->f('id');
				$defenseMarksDescArray[$i] = $db->f('description');
				$defenseMarksRemarksArray[$i] = $db->f('remarks');
				$i++;}
			while ($db->next_record());
				$row_cnt16 = mysql_num_rows($result16);
		?>

  <table width="60%" border="1" cellpadding="3" cellspacing="1" style="border-collapse:collapse;" class="thetable">			
			<tr>
				<th width="5%">No</th>					
				<th width="50%" align="left">Components</th>
				<th width="5%">Recomendations {Please Tick}<span style="color:#FF0000">*</span></th>
			</tr>
			
			<? for ($i=0;$i<$row_cnt16;$i++) {
				if ($refDefenseMarksId1!="") {?>
			<tr>
				<td align="center"><label><?=$i+1?>.</label></td>
				<td><label><strong><?=$defenseMarksDescArray[$i]?></strong> <?=$defenseMarksRemarksArray[$i]?></label></td>
			
			<? if ($defenseMarksIdArray[$i] == $refDefenseMarksId1) {?>
				<td align="center"><img border="0" align="center" img src="../../../theme/images/success.png" alt="success logo"  width="16" height="16" style="width:16px;height:16px;">	</td>
				
			
			<? }
				else {
			?>
				<td align="center">&nbsp;</td>
			<? }?>
			</tr>
			<? } 
			else { ?>
			<tr>
				<td align="center"><label><?=$i+1?>.</label></td>
			   	<td><label><strong><?=$defenseMarksDescArray[$i]?></strong> <?=$defenseMarksRemarksArray[$i]?></label></td> 
					   
			<? if ($defenseMarksIdArray[$i] == $_POST['defenseMarks_cb']) {?>
			<td align="center"><img border="0" align="center" img src="../../../theme/images/success.png" alt="success logo"  width="16" height="16" style="width:16px;height:16px;"></td>
			<? }
			else {
			?>
			<td align="center"></td>
			<? }?>
			</tr>
			<? }	
			} ?>
  </table>

	</br>
	<p><strong><h3>OVERALL COMMENT ON STUDENT</h3></strong></p>

	<p><STRONG>As evaluation committee, how do you asses this student on the following ground. please rate how strongly you agree or disagree with the statement about the candidate on the following ground.</STRONG></p>
	<p><strong>(1= strongly disagree, 2 = disagree, 3 = slightly disagree, 4 = neither agree nor disagree, 5 = slightly agree, 6 = agree. 7 = strong agree)</strong></p>

	<?
		$sql17 = "SELECT id, description
		FROM ref_overall_comments
		WHERE status = 'A'								
		ORDER BY seq";
	
		$result17 = $db->query($sql17);
		$db->next_record();
	
		$overallCommentsIdArray = Array();
		$overallCommentsArray = Array();
		$i=0;
	do {
		$overallCommentsIdArray[$i] = $db->f('id');
		$overallCommentsArray[$i] = $db->f('description');
		$i++;
	} while ($db->next_record());
	$row_cnt17 = mysql_num_rows($result17);
	
	$sql18 = "SELECT id, rate, description
	FROM ref_overall_rating
	WHERE status = 'A'								
	ORDER BY seq";
	
	$result18 = $db->query($sql18);
	$db->next_record();
	
	$overallRatingIdArray = Array();
	$overallRatingRateArray = Array();
	$overallRatingDescArray = Array();
	$i=0;
	do {
		$overallRatingIdArray[$i] = $db->f('id');
		$overallRatingRateArray[$i] = $db->f('rate');
		$overallRatingDescArray[$i] = $db->f('description');
		$i++;
	} while ($db->next_record());
	$row_cnt18 = mysql_num_rows($result18);
	?>
	
	<table border="2" cellpadding="4" cellspacing="2" style="border-collapse:collapse;" class="thetable">
		<tr>
			<th width="5%" align="center"><b>No</b></th>
			<th width="50%" align="left"><b>Components</b></th>
			<th width="20%" align="center"><b>Rating</b></th>
		</tr>
		<?
		for ($i=0;$i<$row_cnt17;$i++) { //ref_overall_comments
			?>									
			<tr>
			<td align="center"><a href="javascript:void(0);" onMouseOver="toolTip('<?=$overallCommentsIdArray[$i]?>', 100)" onMouseOut="toolTip()"><?=$i+1?>.</a></td>									 			<td><label><?=$overallCommentsArray[$i]?>.</label></td>
			<input type="hidden" name="overallCommentsIdArray[]" id="overallCommentsIdArray" value="<?=$overallCommentsIdArray[$i];?>"></input>	
	
	<? if ($row_cnt2B>0) {
					?>
					<td>							
					<?
					for ($l=0;$l<$row_cnt2B;$l++) {//ref_overall_rating
						if ($overallCommentsIdArray[$i] == $eoRefOverallCommentsIdArray[$l]) {
							for ($k=0;$k<$row_cnt18;$k++) {//ref_overall_rating
								if ($overallRatingIdArray[$k] == $eoRefOverallRatingIdArray[$l]) {
									?>
									<?=$overallRatingRateArray[$k]?> - <?=$overallRatingDescArray[$k]?>									
									<?
								}
								else {
									
								}
							}
						}							
					}
					?>
</td>
					
					<?																		
				}
				else {	
					?>
					<td>
					
					<?
					for ($j=0;$j<$row_cnt18;$j++) {
						
						if ($overallRatingIdArray[$j] == $_REQUEST['addOverallRating'][$i])
						{
						?>
							<?=$overallRatingRateArray[$j]?> - <?=$overallRatingDescArray[$j]?>							
							<?
						}
						else{
							?>
																					
							<?
						}
					}
					
					?>
			  </td>
					<?
				}?>
	  
	  
<? }?>
	  </tr>
			  
			  
			  
	</table>
</br>
<table width = "80%" border="2" cellpadding="4" cellspacing="2" style="border-collapse:collapse;" class="thetable">

<tr>
<th width="25%"><span style="float:left;"><b>Name evaluation committee</b></span></td>

<?

			$sql31 = "SELECT NAME as staffname 
						FROM new_employee 
						WHERE empid ='$user_id'";
			
			if (substr($user_id,0,2) != '07') { 
				$dbConnStaff= $dbc; 
			} 
			else { 
				$dbConnStaff=$dbc1; 
			}
			$result1 = $dbConnStaff->query($sql31); 
			$dbConnStaff->next_record();
			$staffname=$dbConnStaff->f('staffname');
		?>
<td><?=$staffname?></td>
<input type="hidden" name="sname" id="sname" value="<?=$staffname; ?>">
</tr>
<tr>
<th><span style="float:left;"><b>Signature</b></span></td>
<td></td>
</tr>
<tr>
<th><span style="float:left;"><b>Date</b></span></td>

<td>&nbsp;</td>
<input type="hidden" name="indate" id="indate" value="<?=$indate; ?>">
</tr>
</table>

<br />	

<table>
			<tr>
			<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../defense/defence_proposal_evaluate.php';" /></td>
						
		 <!-- <td><input type="button" name="btnPrint" value="Print me" onClick="javascript:document.location.href='../defense/review_evaluation_detail_view_report.php?id=<?=$id?>&mn=<?=$studentMatrixNo?>&tid=<?=$thesisIdArray[$i]?>&pid=<?=$proposalIdArray[$i]?>&ref=<?=$referenceNoArray[$i]?>&refd=<?=$referenceNoDefence?>&rol=<?=$supervisorTypeId?>&cid=<?=$calendarIdArray[$i]?>';" /></td>-->
		 
		 
		    <td><input type="button" name="btnPrint" value="Print me PDF" onClick="javascript:document.location.href='../defense/pdf_defense_approval_report.php?id=<?=$id?>&mn=<?=$studentMatrixNo?>&tid=<?=$thesisId?>&pid=<?=$proposalId?>&ref=<?=$referenceNo?>&refd=<?=$referenceNoDefence?>&rol=<?=$supervisorTypeId?>&cid=<?=$calendarId?> &did=<?=$defenseId?>';" /></td>
			
	<? echo $id;?>
		</tr>
  </table> 
</br>
<table>				
</form>
</body>
</html>