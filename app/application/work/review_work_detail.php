<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: review_work_detail.php
//
// Created by: Zuraimi
// Created Date: 28-August-2015
// Modified by: Zuraimi
// Modified Date: 28-August-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

session_start();
$user_id=$_SESSION['user_id'];
$workId=$_GET['id'];
$studentMatrixNo=$_GET['mn'];
$thesisId=$_GET['tid'];
$proposalId=$_GET['pid'];
$referenceNo=$_GET['ref'];
$roleStatus=$_GET['rol'];
$calendarId=$_GET['cid'];

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
	$examinerIdArray = $_POST['examinerIdArray'];
	$performanceStatus = $_POST['performanceStatus'];
	$progressWorkStatus = $_POST['progressWorkStatus'];
	$recommendationStatus = $_POST['recommendationStatus'];
	$comment = $_POST['comment'];
	$otherInfo = $_POST['otherInfo'];
	$remark = $_POST['remark'];
	$curdatetime = date("Y-m-d H:i:s");	

	$msg = Array();
	
	if ($roleStatus == "PRI") {
		$arr_length = count($examinerIdArray);
		if ($arr_length == 0) {
			$msg[] = "<div class=\"error\"><span>The submission of the feedback is put on-hold. Please check with the Faculty if any Evaluation Committee Panel has accepted the invitation.</span></div>";
		}	
	}
	if (empty($performanceStatus)) $msg[] = "<div class=\"error\"><span>Please confirm status for <strong>Performance in line with objective</strong>.</span></div>";
	if (empty($progressWorkStatus)) $msg[] = "<div class=\"error\"><span>Please confirm status for <strong>Progress work in line with objective</strong>.</span></div>";
	if (empty($recommendationStatus)) $msg[] = "<div class=\"error\"><span>Please confirm status for <strong>Recommendation for Work Completion</strong>.</span></div>";
	if (($recommendationStatus=="NRE") && ($comment=="")) $msg[] = "<div class=\"error\"><span>You have selected NO for <strong>Recommendation for Work Completion</strong>. Please specify your reason.</span></div>";

	if(empty($msg)) 
	{
		$sql8_0 = "SELECT id, pg_work_id, pg_employee_empid, status, responded_status, 
		IFNULL(responded_date,'0000-00-00 00:00:00') as responded_date, submit_date, comment, other_info,
		insert_by, IFNULL(insert_date,'0000-00-00 00:00:00') as insert_date, 
		modify_by, IFNULL(modify_date,'0000-00-00 00:00:00') as modify_date
		FROM pg_work_detail
		WHERE id = '$workDetailId'
		AND archived_status is NULL";
		
		$dbg->query($sql8_0);
		$dbg->next_record();
		
		$id = $dbg->f('id');
		$workId = $dbg->f('pg_work_id');
		$employeeId = $dbg->f('pg_employee_empid');
		$respondedStatus = $dbg->f('responded_status');
		$respondedDate = $dbg->f('responded_date');
		$submitDate = $dbg->f('submit_date');
		$insertBy = $dbg->f('insert_by');
		$insertDate = $dbg->f('insert_date');
		$modifyBy = $dbg->f('modify_by');
		$modifyDate = $dbg->f('modify_date');
		
		$newWorkDetailId = runnum('id','pg_work_detail');
		
		$sql8_1 = "INSERT INTO pg_work_detail
		(id, pg_work_id, pg_employee_empid, status,responded_status, responded_date, submit_date, performance_status, work_status, 
		comment, other_info, insert_by, insert_date, modify_by, modify_date)
		VALUES ('$newWorkDetailId','$workId','$employeeId', '$recommendationStatus',
		'$respondedStatus', '$curdatetime', '$submitDate', '$performanceStatus', '$progressWorkStatus', '$comment','$otherInfo', 
		'$insertBy', '$insertDate', '$modifyBy','$modifyDate')";
		
		$dbg->query($sql8_1);
		
		$sql8_2 = "UPDATE pg_work_detail
		SET modify_by = '$user_id', modify_date = '$curdatetime', archived_status = 'ARC', archived_date = '$curdatetime'
		WHERE id = '$workDetailId'";
				
		$dbg->query($sql8_2);

		if ($roleStatus =="PRI") {
			$sql10 = "UPDATE pg_work
			SET status = '$recommendationStatus', respond_status = 'Y', modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE id = '$workId'
			AND reference_no = '$referenceNo'
			AND archived_status is null";
		
			$dbg->query($sql10);
			
			if ($recommendationStatus=="REC") {
				$curdatetime = date("Y-m-d H:i:s");	
				$newEvaluationId = runnum('id','pg_work_evaluation');
				$newReferenceNo = "E".runnum2('reference_no','pg_work_evaluation');
				$sql11 = "INSERT INTO pg_work_evaluation
				(id, pg_work_id, reference_no, student_matrix_no, pg_thesis_id, pg_proposal_id, respond_status, confirmed_status,
				ref_work_marks_id, status, remark_evaluation, insert_by, insert_date, modify_by, modify_date)
				VALUES ('$newEvaluationId', '$workId','$newReferenceNo','$studentMatrixNo','$thesisId','$proposalId', 'N', 'N',
				null, 'IN1', '$remark', '$user_id', '$curdatetime', '$user_id', '$curdatetime')";
				$dbg->query($sql11);
				
				$arr_length = count($examinerIdArray);
				if ($arr_length > 0) {
					for ($j=0;$j<$arr_length;$j++) {
						$newEvaluationDetailId = runnum('id','pg_work_evaluation_detail');
						$sql13 = "INSERT INTO pg_work_evaluation_detail
						(id, pg_eval_id, pg_employee_empid, major_revision, other_comment, responded_date, ref_work_marks_id, status, 
						insert_by, insert_date, modify_by, modify_date)
						VALUES ('$newEvaluationDetailId', '$newEvaluationId','$examinerIdArray[$j]', null, null, null, null,'IN1',
						'$user_id', '$curdatetime', '$user_id', '$curdatetime')";
						$dbg->query($sql13);
						
						
						///////////////////////Notification Start///////////////////////////////
						$calendarId = $_REQUEST['calendarId'];
		
						$sqlstaff = "SELECT name, email from new_employee WHERE empid = '$user_id'";
						$dbcstaff = $dbc;
						$result_sqlstaff = $dbcstaff->query($sqlstaff); 
						$dbcstaff->next_record();
						$staffName = $dbcstaff->f('name');
						$staffEmail = $dbcstaff->f('email');
											
						$sqlstudentdata = "SELECT name, email from student WHERE matrix_no = '$studentMatrixNo'";
						$dbcstudata = $dbc;
						$result_sqlstudentdata = $dbcstudata->query($sqlstudentdata); 
						$dbcstudata->next_record();
						$studentName = $dbcstudata->f('name');
						$studentEmail = $dbcstudata->f('email');
						
						$sqlotherstaff = "SELECT name, email from new_employee WHERE empid = '$examinerIdArray[$j]'";
						$dbcstaff2 = $dbc;
						$result_sqlotherstaff = $dbcstaff2->query($sqlotherstaff); 
						$dbcstaff2->next_record();
						$staffOtherName = $dbcstaff2->f('name');
						$staffOtherEmail = $dbcstaff2->f('email');
						
						$sql3 = "SELECT id, defense_date, DATE_FORMAT(defense_date,'%d-%b-%Y') as viva_date, 
						DATE_FORMAT(defense_stime,'%h:%i%p') as viva_stime,
						DATE_FORMAT(defense_etime,'%h:%i%p') as viva_etime, venue, recomm_status, status
						FROM pg_calendar
						WHERE student_matrix_no = '$studentMatrixNo'
						AND status = 'A'
						AND id = '$calendarId'
						ORDER BY defense_date ASC";
						$dba2 = $dba;
						$result_sql3 = $dba2->query($sql3); 
						$dba2->next_record();
						
						$recommendedId = $dba2->f('id');
						$defenseDate = $dba2->f('viva_date');
						$defenseSTime = $dba2->f('viva_stime');
						$defenseETime = $dba2->f('viva_etime');	
						$venue = $dba2->f('venue');		
						$calendarStatus = $dba2->f('status');
						$recommStatus = $dba2->f('recomm_status');
						
						
						$sqlmsg = "SELECT const_value FROM base_constant
						WHERE const_term = 'MESSAGE_SUP_TO_EXA'";
						$dbgmsj = $dbg;
						$result_sqlmsg = $dbgmsj->query($sqlmsg); 
						$dbgmsj->next_record();
						$row_cnt_msg = mysql_num_rows($result_sqlmsg);
						$msgConstValue = $dbgmsj->f('const_value');
						
						if ($msgConstValue == 'Y')///////////////message notification
						{
							include("../../../app/application/inbox/work/submit_work_exa.php");
						}
						
						////email notification/////
						$sqlemail = "SELECT const_value FROM base_constant
						WHERE const_term = 'EMAIL_SUP_TO_EXA'";
						$dbg1 = $dbg;
						$result_sqlemail = $dbg1->query($sqlemail); 
						$dbg1->next_record();
						$row_cnt_email = mysql_num_rows($result_sqlemail);
						$emailConstValue = $dbg1->f('const_value');
						
						$sqlemail1 = "SELECT email_status FROM pg_employee
						WHERE staff_id = '$examinerIdArray[$j]'
						AND STATUS = 'A'";
						$dbg3 = $dbg;
						$result_sqlemail1 = $dbg3->query($sqlemail1); 
						$dbg3->next_record();
						$row_cnt_email1 = mysql_num_rows($result_sqlemail1);
						$emailConstValue1 = $dbg3->f('email_status');
						
						if ($emailConstValue == 'Y' && $emailConstValue1 == 'Y')
						{
							include("../../../app/application/email/work/email_submit_work_exa.php");
						}			
						////////////////////end notification//////////////////////////////////
					}
				}
			}
			$msg[] = "<div class=\"success\"><span>The feedback on the Work Completion has been submitted successfully to Evaluation Panel for approval.</span></div>";
		}
		else {
			$msg[] = "<div class=\"success\"><span>The feedback on the Work Completion has been submitted successfully to Main Supervisor for review.</span></div>";
		}		
	}
}

$thesisId=$_GET['tid'];
$proposalId=$_GET['pid'];

$sql2 = "SELECT b.id as work_detail_id, 
DATE_FORMAT(b.responded_date, '%d-%b-%Y %h:%i %p') as responded_date,  
DATE_FORMAT(a.submit_date, '%d-%b-%Y %h:%i %p') as submit_date, a.student_matrix_no, a.pg_thesis_id, a.pg_proposal_id, 
b.status as defense_detail_status, 
a.status as defense_status, c2.description as defense_desc,
a.insert_by, a.insert_date, a.modify_by, a.modify_date,	 
c.description as defense_detail_desc
FROM pg_work a
LEFT JOIN pg_work_detail b ON (b.pg_work_id = a.id)
LEFT JOIN ref_proposal_status c ON (c.id = b.status)
LEFT JOIN ref_proposal_status c2 ON (c2.id = a.status)
WHERE a.id = '$workId'
AND a.student_matrix_no = '$studentMatrixNo'
AND a.pg_thesis_id = '$thesisId'
AND a.pg_proposal_id = '$proposalId'
AND a.reference_no = '$referenceNo'
AND b.pg_employee_empid = '$user_id'
AND a.archived_status is null
AND b.archived_status is null";

$result2 = $dbg->query($sql2); 
$dbg->next_record();

$defenseStatus=$dbg->f('defense_status');
$defenseDesc=$dbg->f('defense_desc');
$submitDate=$dbg->f('submit_date');
$defenseDate=$dbg->f('defense_date');
$defenseDetailStatus1=$dbg->f('defense_detail_status');
$defenseDetailDesc1=$dbg->f('defense_detail_desc');
$respondedDate=$dbg->f('responded_date');
$workDetailId=$dbg->f('work_detail_id');
$row_cnt2 = mysql_num_rows($result2);
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
	<SCRIPT LANGUAGE="JavaScript">

	function respConfirm () {
		var confirmSubmit = confirm("Are you sure to submit? \nClick OK to confirm or CANCEL to return back.");
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
	<input type="hidden" name="workId" id="id" value="<?=$workId; ?>">
	<input type="hidden" name="thesisId" id="thesisId" value="<?=$thesisId; ?>">
	<input type="hidden" name="proposalId" id="proposalId" value="<?=$proposalId; ?>">
	<input type="hidden" name="workDetailId" id="workDetailId" value="<?=$workDetailId; ?>">
	<input type="hidden" name="referenceNo" id="referenceNo" value="<?=$referenceNo; ?>">
	<input type="hidden" name="defenseStatus" id="defenseStatus" value="<?=$defenseStatus; ?>">
	<input type="hidden" name="defenseDetailStatus" id="defenseDetailStatus" value="<?=$defenseDetailStatus1; ?>">
	<?if ($row_cnt2>0) {
		if ($defenseDetailStatus1=="IN1") {?>
			<table border="0">
				<tr>
				<td><h2><strong>Report Details</strong><h2></td>
				</tr>
			</table>
			<table>
				<tr>
					<td>Proposed Status</td>
					<td>:</td>
					<?if ($defenseDetailStatus1=="") $defenseDetailDesc1='New';?>
					<td><strong><?=$defenseDetailDesc1?></strong></td>
				</tr>
				<tr>
					<td>Last Update</td>
					<td>:</td>
					<td><?=$submitDate?></td>
				</tr>
				<tr>
					<td>Reference No.</td>
					<td>:</td>
					<td><?=$referenceNo?></td>
				</tr>
				<tr>
					<td>Student Matrix No</td>
					<td>:</td>
					<td><?=$studentMatrixNo?><input type="hidden" name = "studentID" id = "studentID" value="<?=$studentMatrixNo?>" /></td>
				</tr>
					<?
					$sql1 = "SELECT name AS student_name
					FROM student
					WHERE matrix_no = '$studentMatrixNo'";
					if (substr($studentMatrixNo,0,2) != '07') { 
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
				<input type="hidden" name="sname" id="sname" value="<?=$sname; ?>">
				<tr>
					<td>Thesis / Project ID</td>
					<td>:</td>
					<td><label><?=$thesisId;?></label></td>
				</tr>
				<tr>
				<td>Evaluation Schedule</td>
				<td>:</td>	
				<?				
					$sql7 = "SELECT const_value
					FROM base_constant
					WHERE const_category = 'DEFENSE_PROPOSAL'
					AND const_term = 'DEFENSE_DURATION'";
					
					$result_sql7 = $db->query($sql7); 
					$db->next_record();
					$defenseDurationParam = $db->f('const_value');
		
					$sql3 = "SELECT a.id, a.defense_date, DATE_FORMAT(a.defense_date,'%d-%b-%Y') as defense_date1, 
					DATE_FORMAT(a.defense_stime,'%h:%i%p') as defense_stime,
					DATE_FORMAT(a.defense_etime,'%h:%i%p') as defense_etime, a.venue, a.recomm_status, a.status,
					c.description as session_type_desc
					FROM pg_calendar a
					LEFT JOIN pg_work b On (b.pg_calendar_id = a.id)
					LEFT JOIN ref_session_type c ON (c.id = a.ref_session_type_id)
					WHERE a.id = '$calendarId'
					AND a.student_matrix_no = '$studentMatrixNo'					
					AND a.thesis_id = '$thesisId'
					AND a.status = 'A'
					ORDER BY a.defense_date ASC";
					
					$result_sql3 = $dba->query($sql3); 
					$dba->next_record();
					
					$recommendedId = $dba->f('id');
					$defenseDate = $dba->f('defense_date1');
					$defenseSTime = $dba->f('defense_stime');
					$defenseETime = $dba->f('defense_etime');	
					$venue = $dba->f('venue');		
					$calendarStatus = $dba->f('status');
					$recommStatus = $dba->f('recomm_status');
					$sessionTypeDesc = $dba->f('session_type_desc');
					?>				
				<td><label><?=$sessionTypeDesc?> - <?=$defenseDate?>, <?=$defenseSTime?> to <?=$defenseETime?>, <?=$venue?></label></td>				
			</tr>   				
			</table>				
			</br>
			<?
			$sqlPublication=" SELECT DATE_FORMAT(a.published_date,'%d-%b-%Y') as published_date, a.publication_title, a.publication_id, 
			c.description as publication_type, a.website, b.description as country_desc, d.publisher_name as publication_name
			FROM pg_defense_publication a
			LEFT JOIN ref_country b ON (b.id = a.country_id)
			LEFT JOIN ref_publication_type c ON (c.id = a.publication_type)
			LEFT JOIN ref_publisher d ON (d.id = a.publication_id)
			WHERE (a.reference_no = '$referenceNo' OR a.reference_no IS NULL)
			/*AND (a.pg_work_id = '$defenseId' OR a.pg_work_id IS NULL)*/
			AND a.pg_proposal_id='$proposalId'
			AND a.pg_thesis_id = '$thesisId'
			AND a.student_matrix_no = '$studentMatrixNo'
			AND a.ref_session_type_id = 'WCO'			
			ORDER BY a.published_date DESC ";			
			
			$resultPublication = $db->query($sqlPublication); 
			$db->next_record();
			$row_cnt = mysql_num_rows($resultPublication);
			?>
			<fieldset>
			<legend><strong>Publications</strong></legend>
				<table width="100%" border="1" cellpadding="3" cellspacing="1" style="border-collapse:collapse;" class="thetable">			
						<tr>
							<th width="5%">No</th>					
							<th width="10%">Published Date</th>
							<th align="left" width="25%">Title</th>
							<th align="left" width="20%">Name</th>
							<th align="left" width="10%">Type of Publication</th>
							<th align="left" width="15%">Website</th>
							<th align="left" width="15%">Country</th>
						</tr>
						
						<?php
					if ($row_cnt > 0) {
						
						$tmp_no = 0;
						do { 
							?><tr>
									<td align="center"><label><?=$tmp_no+1;?>.</label></td>
									<td align="center"><label><?=$db->f('published_date');?></label></td>
									<td align="left"><label><?=$db->f('publication_title');?></label></td>
									<td align="left"><label><?=$db->f('publication_name');?></label></td>									
									<td align="left"><label><?=$db->f('publication_type');?></label></td>
									<td align="left"><label><?=$db->f('website');?></label></td>
									<td align="left"><label><?=$db->f('country_desc');?></label></td>
								</tr>
							<?
							$tmp_no++;
						} while ($db->next_record());
					}
					else {
						?>
						<table>
							<tr>
								<td><label>No record found!</label></td>
							</tr>
						</table>
						<?
					}?> 
						
				</table>
			</fieldset>
			</br>
			<?
			$sqlPublication=" SELECT conference, location, DATE_FORMAT(conference_sdate,'%d-%b-%Y') as conference_sdate, 
			DATE_FORMAT(conference_edate,'%d-%b-%Y') as conference_edate, presentation_status, presentation_title			
			FROM pg_defense_conference 
			WHERE (reference_no = '$referenceNo' OR reference_no IS NULL)
			/*AND (pg_work_id = '$defenseId' OR pg_work_id IS NULL)*/
			AND pg_proposal_id='$proposalId'
			AND pg_thesis_id = '$thesisId'
			AND student_matrix_no = '$studentMatrixNo' 
			ORDER BY conference_sdate DESC ";			
			
			$resultPublication = $db->query($sqlPublication); 
			$db->next_record();
			$row_cnt = mysql_num_rows($resultPublication);
			?>
			<fieldset>
			<legend><strong>Participation in Conference</strong></legend>
				<table width="100%" border="1" cellpadding="3" cellspacing="1" style="border-collapse:collapse;" class="thetable">			
						<tr>
							<th align="center" width="5%">No</th>					
							<th align="left" width="20%">Conference</th>
							<th align="left" width="25%">Location</th>
							<th width="10%">Conference Start Date</th>
							<th width="10%">Conference End Date</th>
							<th width="10%">Presentation (Y/N)</th>
							<th align="left" width="20%">Title of Presentation</th>							
						</tr>
						
						<?php
					if ($row_cnt > 0) {
						
						$tmp_no = 0;
						do { 
							?><tr>
									<td align="center"><label><?=$tmp_no+1;?>.</label></td>
									<td><label><?=$db->f('conference');?></label></td>
									<td><label><?=$db->f('location');?></label></td>
									<td align="center"><label><?=$db->f('conference_sdate');?></label></td>									
									<td align="center"><label><?=$db->f('conference_edate');?></label></td>
									<?if ($db->f('presentation_status')=="Y") $presentationStatusDesc = "Yes";
									else $presentationStatusDesc = "No";?>
									<td align="center"><label><?=$presentationStatusDesc;?></label></td>
									<td><label><?=$db->f('presentation_title');?></label></td>
								</tr>
							<?
							$tmp_no++;
						} while ($db->next_record());
					}
					else {
						?>
						<table>
							<tr>
								<td><label>No record found!</label></td>
							</tr>
						</table>
						<?
					}?> 
						
				</table>
			</fieldset>
			<br/>
			<h3><legend><strong>Partner(s)</strong></h3>
			<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="85%" class="thetable">			
				<tr>
					<th width="5%">No</th>					
					<th width="15%" align="left">Role</th>
					<th width="10%" align="left">Staff ID</th>
					<th width="20%" align="left">Name</th>
					<th width="5%" align="left">Faculty</th>
					<th width="15%" align="left">Proposed Status</th>
					<th width="15%">Last Update</th>
				</tr>
				 <?php								
				$sql_supervisor = "SELECT a.id, b.pg_employee_empid, c.ref_supervisor_type_id, d.description as supervisor_desc, 
				DATE_FORMAT(b.responded_date,'%d-%b-%Y %h:%i %p') AS responded_date, e.description as defense_detail_desc,
				f.description as role_status_desc
				FROM pg_work_detail b 
				LEFT JOIN pg_work a ON (a.id = b.pg_work_id)
				LEFT JOIN pg_supervisor c ON (c.pg_employee_empid = b.pg_employee_empid)
				LEFT JOIN ref_supervisor_type d ON (d.id = c.ref_supervisor_type_id)
				LEFT JOIN ref_proposal_status e ON (e.id = b.status)
				LEFT JOIN ref_role_status f ON (f.id = c.role_status)
				WHERE a.id = '$workId'
				AND a.student_matrix_no = '$studentMatrixNo'
				AND b.pg_employee_empid <> '$user_id'
				AND a.pg_thesis_id = '$thesisId'
				AND a.pg_proposal_id = '$proposalId'
				AND a.reference_no = '$referenceNo'
				AND a.archived_status is null
				AND b.archived_status is null
				AND c.pg_student_matrix_no = '$studentMatrixNo'
				AND c.pg_thesis_id = '$thesisId'
				AND c.ref_supervisor_type_id in ('SV','CS','XS')
				AND c.status = 'A'";

				$result_sql_supervisor = $db_klas2->query($sql_supervisor); 
				
				$row_cnt_supervisor = mysql_num_rows($result_sql_supervisor);
				$db_klas2->next_record();
				$varRecCount=0;	
				if ($row_cnt_supervisor>0) {

					do {
						$employeeId = $db_klas2->f('pg_employee_empid');
						$supervisorTypeId = $db_klas2->f('ref_supervisor_type_id');
						$supervisorDesc = $db_klas2->f('supervisor_desc');
						$respondedDate = $db_klas2->f('responded_date');
						$defenseDetailDesc = $db_klas2->f('defense_detail_desc');
						$roleStatusDesc = $db_klas2->f('role_status_desc');
					
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
							<td>
							<?
							if ($supervisorTypeId == 'XS') {
							?>
								<label><span style="color:#FF0000"><?=$supervisorDesc?></span></label>
							<?}
							else {
								?>
								<label><?=$supervisorDesc?></label>
								<?
							}?>
							<br/><?=$roleStatusDesc?></td>
							<td align="left"><?=$employeeId;?></td>
							<td align="left"><?=$employeeName;?></td>
							<td align="left"><a href="javascript:void(0);" onMouseOver="toolTip('<?=$departmentName;?>', 300)" onMouseOut="toolTip()"><?=$departmentId;?></a></td>
							
							<?
							$sql12 = "SELECT a.status as defense_detail_status, c.description as defense_detail_desc
							FROM pg_work_detail a
							LEFT JOIN pg_work b ON (b.id = a.pg_work_id)
							LEFT JOIN ref_proposal_status c ON (c.id = a.status)
							WHERE b.student_matrix_no = '$studentMatrixNo'
							AND a.pg_employee_empid = '$employeeId'
							AND b.reference_no = '$referenceNo'
							AND b.pg_thesis_id = '$thesisId'
							AND b.pg_proposal_id = '$proposalId'
							AND a.archived_status is null
							AND b.archived_status is null";
							
							$result12 = $dbg->query($sql12); 
							$dbg->next_record();
							$row_cnt12 = mysql_num_rows($result12);
							$defenseDetailStatus=$dbg->f('defense_detail_status');
							$defenseDetailDesc=$dbg->f('defense_detail_desc');
							?>
							<td align="left"><a href="work_view_feedback_staff.php?eid=<?=$employeeId?>&mn=<?=$studentMatrixNo?>&pid=<?=$proposalId;?>&tid=<?=$thesisId;?>&id=<?=$id;?>&ref=<?=$referenceNo;?>&cid=<?=$calendarId?>"><?=$defenseDetailDesc;?></a></td>								
							
							<td><label><?=$respondedDate?></label></td>
						<?
						} while($db_klas2->next_record());
					}
					else {
						?>
						<table>				
							<tr><td>No record found!</tr>
						</table>
						<br/>
						<table>				
							<tr><td><br/><span style="color:#FF0000">Note:</span><br/>
										Possible Reasons:-<br/>
										1. Supervisor/Co-Supervisor is yet to be assigned.<br/>
										2. Pending approval by the Senate.<br/>
										3. If already assigned, it could be the Supervisor/Co-Supervisor pending to accept.</td>
							</tr>
						</table>
						<?
					}?>
					
			</table>
			<br/>
			<h3><legend><strong>List of Evaluation Committee</strong></h3>
			<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="70%" class="thetable">			
				<tr>
					<th width="5%">No</th>					
					<th width="15%" align="left">Role</th>
					<th width="10%" align="left">Staff ID</th>
					<th width="20%" align="left">Name</th>
					<th width="5%" align="left">Faculty</th>
					<th width="15%" align="left">Invitation Status</th>
				</tr>
				 <?php								
				$sql_examiner = "SELECT b.pg_employee_empid, c.ref_supervisor_type_id, d.description AS supervisor_desc, 
				DATE_FORMAT(b.acceptance_date,'%d-%b-%Y %h:%i %p') AS acceptance_date, b.acceptance_status,
				e.description as acceptance_status_desc
				FROM pg_invitation a
				LEFT JOIN pg_invitation_detail b ON (b.pg_invitation_id = a.id)
				LEFT JOIN pg_supervisor c ON (c.id = b.pg_supervisor_id)
				LEFT JOIN ref_supervisor_type d ON (d.id = c.ref_supervisor_type_id) 
				LEFT JOIN ref_acceptance_status e ON (e.id = b.acceptance_status)
				WHERE c.pg_student_matrix_no = '$studentMatrixNo'
				AND c.pg_thesis_id = '$thesisId'
				AND c.ref_supervisor_type_id in ('EI','EE','EC','XE')
				AND a.pg_calendar_id = '$calendarId'
				AND b.ref_invite_status_id = 'INV'
				AND c.status = 'A'
				AND d.status = 'A'
				ORDER BY d.seq";

				$result_sql_examiner = $db_klas2->query($sql_examiner); 
				
				$row_cnt_examiner = mysql_num_rows($result_sql_examiner);
				$db_klas2->next_record();
				$varRecCount=0;	
				if ($row_cnt_examiner>0) {

					do {
						$employeeId = $db_klas2->f('pg_employee_empid');
						$supervisorTypeId = $db_klas2->f('ref_supervisor_type_id');
						$supervisorDesc = $db_klas2->f('supervisor_desc');
						$acceptanceDate = $db_klas2->f('acceptance_date');
						$acceptanceStatus = $db_klas2->f('acceptance_status');
						$acceptanceStatusDesc = $db_klas2->f('acceptance_status_desc');
						
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
						<? if ($acceptanceStatus == "ACC") {?>
							<input type="hidden" name="examinerIdArray[]" id="examinerIdArray" value="<?=$employeeId; ?>">
						<?}?>
						<tr>
							<td align="center"><?=$varRecCount;?>.</td>					
							<td>
							<?
							if ($supervisorTypeId == 'XS') {
							?>
								<label><span style="color:#FF0000"><?=$supervisorDesc?></span></label>
							<?}
							else {
								?>
								<label><?=$supervisorDesc?></label>
								<?
							}?></td>
							<td align="left"><?=$employeeId;?></td>
							<td align="left"><?=$employeeName;?></td>
							<td align="left"><a href="javascript:void(0);" onMouseOver="toolTip('<?=$departmentName;?>', 300)" onMouseOut="toolTip()"><?=$departmentId;?></a></td>
							<td><label><?=$acceptanceStatusDesc?><br/><?=$acceptanceDate?></label></td>
						<?
						} while($db_klas2->next_record());
					}
					else {
						?>
						<table>				
							<tr><td>No record found!</tr>
						</table>
						<table>				
							<tr><td>Possible Reasons:-<br/>
									1. Evaluation Committee is yet to be assigned.<br/>
									2. If already assigned, it could be the Committee Member is pending to accept.</td>
							</tr>
						</table>
						<?
					}?>
					
			</table>
			<br>
			<table>
				<tr>
					<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../work/review_work.php';" /></td>
				</tr>
			</table>	
			</br>
			<?
			$sql2_1 = "SELECT a.id as chapter_id, a.chapter_no, a.description as chapter_desc, 
			b.id as subchapter_id, b.subchapter_no, b.description as subchapter_desc,
			a.discussed_status as chapter_discussed_status, 
			b.discussed_status as subchapter_discussed_status
			FROM pg_chapter a
			LEFT JOIN pg_subchapter b ON (b.chapter_id = a.id)  
			WHERE a.status = 'A'
			AND a.student_matrix_no = '$studentMatrixNo'
			AND (b.status = 'A' OR b.status IS NULL)
			ORDER BY a.chapter_no, b.subchapter_no";

			$result2_1 = $dbb->query($sql2_1); 
			$dbb->next_record();
			$row_cnt2_1 = mysql_num_rows($result2_1);

			
			?>
			
		<fieldset>
		<legend><h3><strong>Work Achievement</strong></h3></legend>
			<?
			$chapterArray = Array();
			$no=0;
			do {											
				$chapterId[$no]=$dbb->f('chapter_id');	
				$chapterNo[$no]=$dbb->f('chapter_no');
				$chapterDesc[$no]=$dbb->f('chapter_desc');
				$subchapterId[$no]=$dbb->f('subchapter_id');	
				$subchapterNo[$no]=$dbb->f('subchapter_no');
				$subchapterDesc[$no]=$dbb->f('subchapter_desc');
				$chapterDiscussedStatus[$no]=$dbb->f('chapter_discussed_status');
				$subchapterDiscussedStatus[$no]=$dbb->f('subchapter_discussed_status');
				$no++;
			}while($dbb->next_record());	

			for ($i=0; $i<$no; $i++){
			?>
			<table>

				<?
					$sql_discussion = " SELECT DATE_FORMAT(planned_date,'%d-%b-%Y') AS planned_date,
					DATE_FORMAT(completion_date,'%d-%b-%Y') AS completion_date, 
					content_discussion
					FROM pg_work_achievement 
					WHERE pg_thesis_id = '$thesisId'
					AND pg_proposal_id = '$proposalId'
					AND reference_no = '$referenceNo'
					AND student_matrix_no = '$studentMatrixNo'
					AND pg_chapter_id = '$chapterId[$i]'
					AND pg_subchapter_id = '$subchapterId[$i]'
					AND archived_status is null";
										
					$result_sql_discussion = $dbb->query($sql_discussion); 
					$dbb->next_record();
					$discussedStatus = $dbb->f('discussed_status');
					$plannedDate = $dbb->f('planned_date');
					$completionDate = $dbb->f('completion_date');
					$contentDiscussion = $dbb->f('content_discussion');
				?>
				<tr>

					<td><h3><strong><label>Chapter <?=romanNumerals($chapterNo[$i]);?>. <?=$chapterDesc[$i];?></label>			


					<?if ($subchapterNo[$i] != "") {?>
							<label>, Subchapter <?=romanNumerals($subchapterNo[$i]);?>. <?=$subchapterDesc[$i];?></label>
					<?}?>					
					</strong></h3></td>
				</tr>
				<table width="31%">
				<tr>
					<td width="25%">Planned Date</td>
					<td width="1%">:</td>
					<td width="5%"><label><?=$plannedDate?></label></td>
			
				</tr>
				<tr>
					<td width="25%">Completion Date</td>
					<td width="1%">:</td>
					<td width="5%"><label><?=$completionDate?></label></td>					
				</tr>
				</table>
				<table>
					<tr>
						<td><label>Description of Work</label></td>
					</tr>
					<tr>
						<td><?=$contentDiscussion?></td>
					</tr>
				</table>

			</table>
			<?
			}
			?>	
			<input type="hidden" name="totalNoOfContent" id="totalNoOfContent" value="<?=$i; ?>">
		</fieldset>
			<table>
			<tr>
				<td><h3><strong>To be completed by Supervisor/Co-Supervisor</strong></h3></td>
			</tr>
			</table>
			<table width="55%">
				<tr>
				  <td width="35%"><label>Performance in line with objectives <span style="color:#FF0000">*</span></label></td>
					<td width="3%">:</td>
					<?if ($_POST['performanceStatus']=="Y") {?>
						<td width="10%"><input type="radio" name="performanceStatus" value="Y" checked=""> Yes
						<input type="radio" name="performanceStatus" value="N"> No</td>
					<?}
					else if ($_POST['performanceStatus']=="N"){
						?>
						<td width="10%"><input type="radio" name="performanceStatus" value="Y"> Yes
						<input type="radio" name="performanceStatus" value="N" checked=""> No</td>
						<?
					}
					else {
						?>
						<td width="10%"><input type="radio" name="performanceStatus" value="Y"> Yes
						<input type="radio" name="performanceStatus" value="N"> No</td>
						<?
					}?>
				</tr>
				<tr>
					<td><label>Progress work in line with objectives <span style="color:#FF0000">*</span></label></td>
					<td>:</td>
					<?if ($_POST['progressWorkStatus']=="Y") {?>
						<td><input type="radio" name="progressWorkStatus" value="Y" checked=""> Yes
						<input type="radio" name="progressWorkStatus" value="N"> No</td>
					<?}
					else if ($_POST['progressWorkStatus']=="N"){
						?>
						<td><input type="radio" name="progressWorkStatus" value="Y"> Yes
						<input type="radio" name="progressWorkStatus" value="N" checked=""> No</td>
						<?
					}
					else {
						?>
						<td><input type="radio" name="progressWorkStatus" value="Y"> Yes
						<input type="radio" name="progressWorkStatus" value="N"> No</td>
						<?
					}?>
				</tr>				
				<tr>
				  <td><label>Recommendation for Work Completion <span style="color:#FF0000">*</span></label></td>
					<td>:</td>
					<?if ($_POST['recommendationStatus']=="REC") {?>
						<td><input type="radio" name="recommendationStatus" value="REC" checked=""> Yes
						<input type="radio" name="recommendationStatus" value="NRE"> No</td>
					<?}
					else if ($_POST['recommendationStatus']=="NRE"){
						?>
						<td><input type="radio" name="recommendationStatus" value="REC"> Yes
						<input type="radio" name="recommendationStatus" value="NRE" checked=""> No</td>
						<?
					}
					else {
						?>
						<td><input type="radio" name="recommendationStatus" value="REC"> Yes
						<input type="radio" name="recommendationStatus" value="NRE"> No</td>
						<?
					}?>
				</tr>			
				<tr>
					<td><label>If No, please specify your reason</label></td>
					<td>:</td>					
					<td><textarea type="text" name="comment" cols="30" rows="3" id="comment" ></textarea></td>
				</tr>
				<tr>
					<td><label>Any other information, please specify:</label></td>
					<td>:</td>
					<td><textarea type="text" name="otherInfo" cols="30" rows="3" id="otherInfo" ><?=$_POST['otherInfo']?></textarea></td>
				</tr>
			</table>
			<?if ($roleStatus == "PRI") {
				?>
				<table>
					<tr>
						<td><h3><strong>Remarks for Academic Committee/Schoolboard</strong></h3></td>
					</tr>
					<tr>
						<td>If you choose <em>Recommendation for Work Completion</em></td>
					</tr>
				</table>
				<table>
					<td><label>Remarks</label></td>
					<td>:</td>
					<td><textarea type="text" name="remark" cols="60" rows="3" id="remark" ><?=$_POST['remark']?></textarea></td>
				</table>
				<?
			}
			?>
			<br/>
			<table>
				<tr>
					<td><label>Notes:</td>
				</tr>
				<tr>
					<td></span>1. Field marks with (<span style="color:#FF0000">*</span>) is compulsory.</td>
				</tr>

			</table>

			<table>
				<tr>
					<td><input type="submit" name="btnSubmit" value="Submit" onClick="return respConfirm()"/></td>
					<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../work/review_work.php';" /></td>
				</tr>
			</table>
		<?}
		else {
			?>			
			<table>
				<tr>
					<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../work/review_work.php';" /></td>
				</tr>
			</table>
			<?
		}
	}	
	else  
	{?><fieldset>
			<table>
				<tr>
					<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../work/review_work.php';" /></td>
				</tr>
			</table>
	<?}?>
	</form>
</body>
</html>




