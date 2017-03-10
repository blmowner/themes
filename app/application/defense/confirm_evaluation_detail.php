<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: confirm_evaluation_detail.php
//
// Created by: Zuraimi
// Created Date: 28-Jul-2015
// Modified by: Zuraimi
// Modified Date: 28-Jul-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();
/*ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
session_start();*/
$user_id=$_SESSION['user_id'];
$evaluationId=$_GET['id'];
$employeeId=$_GET['eid'];
$studentMatrixNo=$_GET['mn'];
$thesisId=$_GET['tid'];
$proposalId=$_GET['pid'];
$referenceNo=$_GET['ref'];
$referenceNoDefence=$_GET['refd'];
$supervisorTypeId=$_GET['rol'];
$calendarId=$_GET['cid'];
$defenseId=$_GET['did'];
$marksGivenAlert = false;
$overallRatingAlert = false;
$confirmRemarkAlert = false;

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


if(isset($_POST['btnApproved']) && ($_POST['btnApproved'] <> ""))
{
	$confirmRemark = $_POST['confirmRemark'];
	$evaluationId = $_POST['evaluationId'];
	$curdatetime = date("Y-m-d H:i:s");

	$sql19 = "UPDATE pg_evaluation
	SET confirmed_by = '$user_id', confirmed_remark = '$confirmRemark', confirmed_date = '$curdatetime', 
	confirmed_status = 'Y', status = 'APP',
	modify_by = '$user_id', modify_date = '$curdatetime'
	WHERE id = '$evaluationId'";
	
	$db->query($sql19);
	$db->next_record();
	
	$msg[] = "<div class=\"success\"><span>The Defence Proposal report has been approved successfully.</span></div>";	
}

if(isset($_POST['btnDisapproved']) && ($_POST['btnDisapproved'] <> ""))
{
	$proposedDefenseMarks = $_POST['proposedDefenseMarks_cb'];
	$confirmRemark = $_POST['confirmRemark'];
	$evaluationId = $_POST['evaluationId'];
	$curdatetime = date("Y-m-d H:i:s");
	
	$msg = Array();
	
	if ($confirmRemark == "") {
		$msg[] = "<div class=\"error\"><span>Please provide your remarks for the decision you have taken for this Defence Proposal status.</span></div>";
		$confirmRemarkAlert = true;
	}
	if ($proposedDefenseMarks == "") {
		$msg[] = "<div class=\"error\"><span>Please provide your proposed mark for the decision you have taken for this Defence Proposal status.</span></div>";
		$confirmRemarkAlert = true;
	}
	if(empty($msg)) {
	
		$sql19 = "UPDATE pg_evaluation
		SET confirmed_by = '$user_id', confirmed_remark = '$confirmRemark', confirmed_date = '$curdatetime', 
		confirmed_status = 'Y', status = 'DIS', proposed_marks_id = '$proposedDefenseMarks',
		modify_by = '$user_id', modify_date = '$curdatetime'
		WHERE id = '$evaluationId'";
		
		$db->query($sql19);
		$db->next_record();
	
		$msg[] = "<div class=\"success\"><span>The selected Defence Proposal report has been disapproved with the proposed recommendation.</span></div>";	
	}
}

$sql2 = "SELECT b.id as evaluation_detail_id, 
DATE_FORMAT(b.responded_date, '%d-%b-%Y %h:%i %p') as responded_date,  
a.student_matrix_no, a.pg_thesis_id, a.pg_proposal_id, 
b.status as evaluation_detail_status, 
a.status as evaluation_status, c2.description as evaluation_desc,
a.insert_by, a.insert_date, a.modify_by, a.modify_date,	 
c.description as evaluation_detail_desc,
b.major_revision, b.other_comment, a.ref_defense_marks_id, c3.description as defense_marks_desc,
a.remark_evaluation
FROM pg_evaluation a
LEFT JOIN pg_evaluation_detail b ON (b.pg_eval_id = a.id)
LEFT JOIN ref_proposal_status c ON (c.id = b.status)
LEFT JOIN ref_proposal_status c2 ON (c2.id = a.status)
LEFT JOIN ref_defense_marks c3 ON (c3.id = a.ref_defense_marks_id)
WHERE a.id = '$evaluationId'
AND a.student_matrix_no = '$studentMatrixNo'
AND a.pg_thesis_id = '$thesisId'
AND a.pg_proposal_id = '$proposalId'
AND a.reference_no = '$referenceNo'
AND b.pg_employee_empid = '$employeeId'
AND a.respond_status = 'Y'
/*AND b.status = 'IN2'*/
/*AND a.archived_status is null*/
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
$refDefenseMarksDesc=$dbg->f('defense_marks_desc');
$remarkEvaluation=$dbg->f('remark_evaluation');
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
AND c.pg_employee_empid = '$employeeId'
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
AND c.pg_employee_empid = '$employeeId'
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
	<form id="form1" name="form1" method="post" enctype="multipart/form-data">
	<?if ($evaluationStatus == "IN1") {?>
		<table border="0">
			<tr>
				<td><h2><strong>Report Details</strong><h2></td>
			</tr>
		</table>
		<table>
			<tr>
				<td>School Board Confirmation Status</td>
				<td>:</td>
				<td><strong><?=$evaluationDesc?></strong></td>
			</tr>
			<tr>
				<td>Evaluation Committee Status</td>
				<td>:</td>
				<td><strong><?=$refDefenseMarksDesc?></strong></td>
			</tr>
			<tr>
				<td>Last Update</td>
				<td>:</td>
				<td><?=$respondedDate?></td>
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
				LEFT JOIN pg_defense b On (b.pg_calendar_id = a.id)
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
		WHERE a.reference_no = '$referenceNoDefence'
		AND a.pg_defense_id = '$defenseId'
		AND a.pg_proposal_id='$proposalId'
		AND a.pg_thesis_id = '$thesisId'
		AND a.add_status = 'CFM'
		AND a.student_matrix_no = '$studentMatrixNo' 
		AND a.ref_session_type_id = 'DEF'
		AND a.archived_status IS NULL
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
		$sqlConference=" SELECT conference, location, DATE_FORMAT(conference_sdate,'%d-%b-%Y') as conference_sdate, 
		DATE_FORMAT(conference_edate,'%d-%b-%Y') as conference_edate, presentation_status, presentation_title			
		FROM pg_defense_conference 
		WHERE reference_no = '$referenceNoDefence'
		AND pg_defense_id = '$defenseId'
		AND pg_proposal_id='$proposalId'
		AND pg_thesis_id = '$thesisId'
		AND add_status = 'CFM'
		AND student_matrix_no = '$studentMatrixNo' 
		AND archived_status IS NULL
		ORDER BY conference_sdate DESC ";			
		
		$resultConference = $db->query($sqlConference); 
		$db->next_record();
		$row_cnt = mysql_num_rows($resultConference);
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
		<h3><legend><strong>Evaluation Committee Member</strong></h3>
		<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="55%" class="thetable">			
			<tr>
				<th width="5%">No</th>					
				<th width="15%" align="left">Role</th>
				<th width="10%" align="left">Staff ID</th>
				<th width="20%" align="left">Name</th>
				<th width="5%" align="left">Faculty</th>
			</tr>
			 <?php								
			$sql_examiner = "SELECT pg_employee_empid, ref_supervisor_type_id, d.description AS supervisor_desc, 
			DATE_FORMAT(c.acceptance_date,'%d-%b-%Y %h:%i %p') AS acceptance_date,
			DATE_FORMAT(c.assigned_date,'%d-%b-%Y %h:%i %p') AS assigned_date,
			e.description as acceptance_status_desc
			FROM pg_supervisor c 
			LEFT JOIN ref_supervisor_type d ON (d.id = c.ref_supervisor_type_id) 
			LEFT JOIN ref_acceptance_status e ON (e.id = c.acceptance_status)
			WHERE c.pg_student_matrix_no = '$studentMatrixNo'
			AND c.pg_thesis_id = '$thesisId'
			AND c.ref_supervisor_type_id in ('EI','EE','EC','XE')
			AND c.status = 'A'
			ORDER BY d.seq";

			$result_sql_examiner = $db_klas2->query($sql_examiner); 
			
			$row_cnt_examiner = mysql_num_rows($result_sql_examiner);
			$db_klas2->next_record();
			$varRecCount=0;	
			if ($row_cnt_examiner>0) {

				do {
					$employeeId = $db_klas2->f('pg_employee_empid');
					$partnerSupervisorTypeId = $db_klas2->f('ref_supervisor_type_id');
					$supervisorDesc = $db_klas2->f('supervisor_desc');
					$acceptanceDate = $db_klas2->f('acceptance_date');
					$assignedDate = $db_klas2->f('assigned_date');
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
					<input type="hidden" name="supervisorIdArray[]" id="supervisorIdArray" value="<?=$employeeId; ?>">
					<tr>
						<td align="center"><?=$varRecCount;?>.</td>					
						<td>
						<?
						if ($partnerSupervisorTypeId == 'XE') {
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
						<tr><td>Note:<br/>
							Possible Reasons:-<br/>
							1. Evaluation Committee is yet to be assigned.<br/>
							2. If already assigned, it could be the Committee Member is pending to accept.</td>
						</tr>
					</table>
					<?
				}?>
				
		</table>
		<table>
			<tr>
				<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../defense/defence_proposal_confirm.php';" /></td>
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
				FROM pg_achievement 
				WHERE pg_thesis_id = '$thesisId'
				AND pg_proposal_id = '$proposalId'
				AND reference_no = '$referenceNoDefence'
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
		</fieldset>
		<?
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
		do {
			$overallStyleIdArray[$i] = $db->f('id');
			$overallStyleDescArray[$i] = $db->f('description');
			$overallStyleRatingStatusArray[$i] = $db->f('rating_status');
			$i++;
		} while ($db->next_record());
		
		$row_cnt14 = mysql_num_rows($result14);
		
		?>
		<fieldset>
			<legend><strong>Overall Style and Organization</strong></legend>
			<table>
				<tr><td><label><em>Please select the appropriate rating.<em></label></td></tr>
			</table>
			<table width="85%" border="1" cellpadding="3" cellspacing="1" style="border-collapse:collapse;" class="thetable">			
				<tr>
					<th width="5%">No</th>					
					<th width="30%" align="left">Item Description</th>
					<th width="20%" align="left">Rating</th>
					<th width="30%" align="left">Comments</th>
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

						
						if ($row_cnt15 > 0) {
						?>
						<td><select name="addReportRating[]" id="addReportRating" disabled="true">
						<?
							for ($i=0;$i<$row_cnt2A;$i++) {
								if ($overallStyleIdArray[$j] == $esRefOverallStyleIdArray[$i]) {
									for ($k=0;$k<$row_cnt15;$k++) {
										if ($reportRatingIdArray[$k] == $esRefReportRatingIdArray[$i]) {	
											if ($reportRatingRateArray[$k]==0) {?>
												<option value="<?=$reportRatingIdArray[$k]?>" selected="selected"><?=$reportRatingDescArray[$k]?></option>
												<?}
											else {
												if ($reportRatingRateArray[$k]==1 || $reportRatingRateArray[$k]==5) {?>
												<option value="<?=$reportRatingIdArray[$k]?>" selected="selected"><?=$reportRatingRateArray[$k]?> - <?=$reportRatingDescArray[$k]?></option>
												<?}
												else {
													?>
													<option value="<?=$reportRatingIdArray[$k]?>" selected="selected"><?=$reportRatingRateArray[$k]?></option>									
													<?
												}
											}
										}
										else {
											if ($reportRatingRateArray[$k]==0) {?>
												<option value="<?=$reportRatingIdArray[$k]?>"><?=$reportRatingDescArray[$k]?></option>
												<?}
											else {
												if ($reportRatingRateArray[$k]==1 || $reportRatingRateArray[$k]==5) {?>
												<option value="<?=$reportRatingIdArray[$k]?>"><?=$reportRatingRateArray[$k]?> - <?=$reportRatingDescArray[$k]?></option>
												<?}
												else {
													?>
													<option value="<?=$reportRatingIdArray[$k]?>"><?=$reportRatingRateArray[$k]?></option>									
													<?
												}
											}
										}
									}
								}
							}
						?>
						</select></td>
						<?}
						else {
							?>
							<td></td><?
						}
						
						?>
						<td><label><?=$esCommentsArray[$j];?></label></td>
					</tr>
					<?
				}?>
			</table>
		</fieldset>
		<table>
			<tr>
				<td><h3><label>MAJOR REVISION REQUIRED (<em>if any<em>)</label><h3></td>
			</tr>
			<tr>
				<td><label><?=$majorRevision1?></label></td>
			</tr>
		</table>
		<table>
			<tr>
				<td><h3><label>OTHER COMMENTS</label><h3></td>
			</tr>
			<tr>
				<td><label><?=$otherComment1?></label></td>
			</tr>
		</table>
		<table>
			<tr>
				<td><h3><label>MARKS GIVEN</label><h3></td>
			</tr>
			<tr>
				<td><label><strong>Recommendation (Please Check)</strong></label></td>
			</tr>
		</table>
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
			$i++;
		} while ($db->next_record());
		$row_cnt16 = mysql_num_rows($result16);
		?>
		<table width="60%" border="1" cellpadding="3" cellspacing="1" style="border-collapse:collapse;" class="thetable">			
				<tr>
					<th width="5%">Tick<span style="color:#FF0000">*</span></th>
					<th width="5%">No</th>					
					<th width="50%" align="left">Components</th>
				</tr>
				<?for ($i=0;$i<$row_cnt16;$i++) {
					if ($refDefenseMarksId1!="") {?>
					<tr>
						<?if ($defenseMarksIdArray[$i] == $refDefenseMarksId1) {?>
							<td align="center"><input name="defenseMarks_cb" type="radio" id="defenseMarks_cb" value="<?=$defenseMarksIdArray[$i];?>" checked="checked" disabled="true"></td>
						<?}
						else {
							?>
							<td align="center"><input name="defenseMarks_cb" type="radio" id="defenseMarks_cb" value="<?=$defenseMarksIdArray[$i];?>" disabled="true"></td>
							<?
						}?>
						
						<td align="center"><label><?=$i+1?>.</label></td>
						<td><label><strong><?=$defenseMarksDescArray[$i]?></strong> <?=$defenseMarksRemarksArray[$i]?></label></td>
					</tr>
					<?}
					else {?>
					<tr>
						<?if ($defenseMarksIdArray[$i] == $_POST['defenseMarks_cb']) {?>
							<td align="center"><input name="defenseMarks_cb" type="radio" id="defenseMarks_cb" value="<?=$defenseMarksIdArray[$i];?>" checked="checked"></td>
						<?}
						else {
							?>
							<td align="center"><input name="defenseMarks_cb" type="radio" id="defenseMarks_cb" value="<?=$defenseMarksIdArray[$i];?>"></td>
							<?
						}?>
						
						<td align="center"><label><?=$i+1?>.</label></td>
						<td><label><strong><?=$defenseMarksDescArray[$i]?></strong> <?=$defenseMarksRemarksArray[$i]?></label></td>
					</tr>
					<?}
				}?>
		</table>
		<table>
			<tr>
				<td><h3><label>OVERALL COMMENT ON STUDENT</label><h3></td>
			</tr>
			<tr>
				<td>As evaluation committee, how do you assess this student on the following grounds? Please rate how strongly you agree or disagree with the statement about the candidate on the following ground.</td>
			</tr>
		</table>
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
		<table width="75%" border="1" cellpadding="3" cellspacing="1" style="border-collapse:collapse;" class="thetable">			
			<tr>
				<th width="5%">No</th>					
				<th width="50%" align="left">Components</th>
				<th width="15%" align="left">Rating<span style="color:#FF0000">*</span></th>
			</tr>
			<?
			for ($i=0;$i<$row_cnt17;$i++) { //ref_overall_comments
				?>									
				<tr>
					<td align="center"><a href="javascript:void(0);" onMouseOver="toolTip('<?=$overallCommentsIdArray[$i]?>', 100)" onMouseOut="toolTip()"><?=$i+1?>.</a></td>
					<td><label><?=$overallCommentsArray[$i]?>.</label></td>
					<input type="hidden" name="overallCommentsIdArray[]" id="overallCommentsIdArray" value="<?=$overallCommentsIdArray[$i];?>"></input>						
					<?if ($row_cnt2B>0) {
						?>
						<td><select name="addOverallRating[]" id="addOverallRating" disabled="true">
						<option value=""></option>							
						<?
						for ($l=0;$l<$row_cnt2B;$l++) {//ref_overall_rating
							if ($overallCommentsIdArray[$i] == $eoRefOverallCommentsIdArray[$l]) {
								for ($k=0;$k<$row_cnt18;$k++) {//ref_overall_rating
									if ($overallRatingIdArray[$k] == $eoRefOverallRatingIdArray[$l]) {
										?>
										<option value="<?=$overallRatingIdArray[$k]?>" selected="selected"><?=$overallRatingRateArray[$k]?> - <?=$overallRatingDescArray[$k]?></option>									
										<?
									}
									else {
										?>
										<option value="<?=$overallRatingIdArray[$k]?>"><?=$overallRatingRateArray[$k]?> - <?=$overallRatingDescArray[$k]?></option>									
										<?
									}
								}
							}							
						}
						?>
						</select></td>
						
						<?																		
					}
					else {	
						?>
						<td><select name="addOverallRating[]" id="addOverallRating" disabled="true">
						<option value=""></option>
						
						<?
						for ($j=0;$j<$row_cnt18;$j++) {
							
							if ($overallRatingIdArray[$j] == $_REQUEST['addOverallRating'][$i])
							{
							?>
								<option value="<?=$overallRatingIdArray[$j]?>" selected = "selected" ><?=$overallRatingRateArray[$j]?> - <?=$overallRatingDescArray[$j]?></option>									
								<?
							}
							else{
								?>
								<option value="<?=$overallRatingIdArray[$j]?>"><?=$overallRatingRateArray[$j]?> - <?=$overallRatingDescArray[$j]?></option>																	
								<?
							}
						}
						
						?>
							</select></td>
						<?
					}?>
					
					
				</tr>
				<?
			}?>
		</table>
		<br/>
		<fieldset>
		<legend><strong>DEFENCE PROPOSAL CONFIRMATION By SCHOOLBOARD</strong></legend>	
			<table>
				<tr>
					<td><label><strong>Remark by Supervisor for Schoolboard Consideration</strong></label></td>
				</tr>
				<tr>
					<td><label><?=$remarkEvaluation?></label></td>
				</tr>
			</table>
			<br>
			<table>
				<tr>
					<td>
						<?if ($confirmRemarkAlert == true) {?>
							<label><span style="color:#FF0000"><strong>Confirmation Remark</strong></span></label>
						<?}
						else {
							?>
							<label><strong>Confirmation Remark</strong></label>
							<?
						}?>
					</td>
				</tr>
				<tr>
					<td><textarea name="confirmRemark" id="confirmRemark" class="ckeditor"></textarea></td>
				</tr>
			</table>
			</fieldset>
			<br>
			<table>
			<tr>
				<td>
					<?if ($confirmRemarkAlert == true) {?>
						<label><span style="color:#FF0000"><label><strong>Proposed Recommendation (Please Check)</strong> - If you choose to <strong>Disapproved</strong></label></span></label>
					<?}
					else {
						?>
						<label><label><strong>Proposed Recommendation (Please Check)</strong> - If you choose to <strong>Disapproved</strong></label></label>
						<?
					}?>
				</td>
			</tr>
		</table>
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
			$i++;
		} while ($db->next_record());
		$row_cnt16 = mysql_num_rows($result16);
		?>
		<table width="60%" border="1" cellpadding="3" cellspacing="1" style="border-collapse:collapse;" class="thetable">			
			<tr>
				<th width="5%">Tick</th>
				<th width="5%">No</th>					
				<th width="50%" align="left">Components</th>
			</tr>
			<?for ($i=0;$i<$row_cnt16;$i++) {
				?>				
				<tr>
				<?
				if ($defenseMarksIdArray[$i] == $refDefenseMarksId1) {?>
					<td align="center"><input name="proposedDefenseMarks_cb" type="radio" id="proposedDefenseMarks_cb" disabled="true" value="<?=$defenseMarksIdArray[$i]?>"></td>
				<?}
				else if ($_POST['proposedDefenseMarks_cb']==$defenseMarksIdArray[$i]) {?>
					<td align="center"><input name="proposedDefenseMarks_cb" type="radio" id="proposedDefenseMarks_cb" checked="true" value="<?=$defenseMarksIdArray[$i]?>"></td>
				<?}
				else {
					?>
					<td align="center"><input name="proposedDefenseMarks_cb" type="radio" id="proposedDefenseMarks_cb" value="<?=$defenseMarksIdArray[$i]?>"></td>
					<?
				}?>
					<td align="center"><label><?=$i+1?>.</label></td>
					<td><label><strong><?=$defenseMarksDescArray[$i]?></strong> <?=$defenseMarksRemarksArray[$i]?></label></td>
				</tr>
				<?
			}?>
	</table>
	<br>
	<table>
		<input type="hidden" name="evaluationId" id="evaluationId" value="<?=$evaluationId; ?>">
		<tr>
			<td><input type="submit" name="btnApproved" value="Approved" onClick="return respConfirm()"/></td>
			<td><input type="submit" name="btnDisapproved" value="Disapproved" onClick="return respConfirm()"/></td>
			<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../defense/defence_proposal_confirm.php';" /></td>
		</tr>
	</table>		
	<?}
	else if ($evaluationStatus == "APP") {
	?>
		<table>
			<tr>
				<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../defense/defence_proposal_confirm.php';" /></td>
			</tr>
		</table>
	<?
	}
	else if ($evaluationStatus == "DIS") {
	?>
		<table>
			<tr>
				<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../defense/defence_proposal_confirm.php';" /></td>
			</tr>
		</table>
	<?
	}?>
	</form>
</body>
</html>




