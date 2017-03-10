<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: review_evaluation_partner_view.php
//
// Created by: Zuraimi
// Created Date: 03-August-2015
// Modified by: Zuraimi
// Modified Date: 03-August-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

session_start();
$user_id=$_SESSION['user_id'];
$evaluationId=$_GET['id'];
$calendarId=$_GET['cid'];
$employeeId=$_GET['eid'];
$studentMatrixNo=$_GET['mn'];
$thesisId=$_GET['tid'];
$proposalId=$_GET['pid'];
$referenceNo=$_GET['ref'];
$referenceNoDefence=$_GET['refd'];
$partnerSupervisorTypeId=$_GET['rol'];
$supervisorTypeId=$_GET['rolx'];
$marksGivenAlert = false;
$overallRatingAlert = false;

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
AND b.pg_employee_empid = '$employeeId'
AND a.archived_status is null
AND b.archived_status is null";

$result2 = $dbg->query($sql2); 
$dbg->next_record();

$evaluationDetailStatus1=$dbg->f('evaluation_detail_status');
$evaluationDetailDesc1=$dbg->f('evaluation_detail_desc');
$respondedDate=$dbg->f('responded_date');
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
	<table border="0">
		<tr>
		<td><h2><strong>Report Details</strong><h2></td>
		</tr>
	</table>
	<table>
		<tr>
			<td>Evaluation Status</td>
			<td>:</td>
			<?if ($evaluationDetailStatus1=="") $evaluationDetailDesc1='New';?>
			<td><strong><?=$evaluationDetailDesc1?></strong></td>
		</tr>
		<?
		$sql16 = "SELECT description as examiner_desc
		FROM ref_supervisor_type
		WHERE id = '$partnerSupervisorTypeId'
		AND type = 'EX'
		AND status = 'A'";
		
		$dbg->query($sql16);
		$dbg->next_record();
		$examinerDesc = $dbg->f('examiner_desc');
		?>
		<tr>
			<td>Evaluation Committee Role</td>
			<td>:</td>
			<td><strong><?=$examinerDesc?></strong></td>
		</tr>
		<tr>
			<td>Last Update</td>
			<td>:</td>
			<td><?=$respondedDate?></td>
		</tr>
		<?
		$sql1 = "SELECT name AS staff_name
		FROM new_employee
		WHERE empid = '$employeeId'";
		if (substr($employeeId,0,3) != 'S07') { 
			$dbConn= $dbc; 
		} 
		else { 
			$dbConn=$dbc1; 
		}
		$result1 = $dbConn->query($sql1); 
		$dbConn->next_record();
		$staffName=$dbConn->f('staff_name');
		?>
		<tr>
			<td>Partner Name</td>
			<td>:</td>
			<td><?=$staffName?> (<?=$employeeId?>)</td>
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
	<br/>
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
	<table>
		<tr>
			<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../defense/review_evaluation_detail_view.php?id=<?=$evaluationId?>&cid=<?=$calendarId?>&mn=<?=$studentMatrixNo?>&tid=<?=$thesisId?>&pid=<?=$proposalId?>&ref=<?=$referenceNo?>&refd=<?=$referenceNoDefence?>&rol=<?=$supervisorTypeId?>';" /></td>
		</tr>
	</table>
	
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

					if ($row_cnt2A > 0) {
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
						<?}
						else {
							?>
							<td></td><?
						}
					}?>
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
			<?if (empty($majorRevision1)) { 
				?><td>None</td>
			<? }
			else {?>
			<td><?=$majorRevision1?></td>
			<?}?>
		</tr>
	</table>
	<table>
		<tr>
			<td><h3><label>OTHER COMMENTS</label><h3></td>
		</tr>
		<tr>
			<?if (empty($otherComments1)) { 
				?><td>None</td>
			<? }
			else {?>
			<td><?=$otherComments1?></td>
			<?}?>
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
			<?for ($i=0;$i<$row_cnt16;$i++) {?>
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
				<?						
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
	<table>
		<tr>
			<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../defense/review_evaluation_detail_view.php?id=<?=$evaluationId?>&cid=<?=$calendarId?>&mn=<?=$studentMatrixNo?>&tid=<?=$thesisId?>&pid=<?=$proposalId?>&ref=<?=$referenceNo?>&refd=<?=$referenceNoDefence?>&rol=<?=$supervisorTypeId?>';" /></td>			
		</tr>
	</table>
	</form>
</body>
</html>




