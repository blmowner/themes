<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: work_review_feedback_detail.php
//
// Created by: Zuraimi
// Created Date: 12-September-2015
// Modified by: Zuraimi
// Modified Date: 12-September-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

$user_id=$_SESSION['user_id'];
$workAmendmentId = $_GET['waid'];
$thesisId = $_GET['tid'];
$referenceNo = $_GET['ref'];
$proposalId = $_GET['pid'];
$calendarId = $_GET['cid'];
$workMarksId = $_GET['wmid'];
$workEvaluationStatus = $_GET['wes'];
$proposedMarksId = $_GET['pmid'];
$studentMatrixNo = $_GET['mn'];

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

if(isset($_POST['btnVerify']) && ($_POST['btnVerify'] <> ""))
{
	$workAmendmentId = $_POST['workAmendmentId'];
	$workFeedbackId = $_POST['workFeedbackId'];
	$panelEmployeeId = $_POST['panelEmployeeId'];
	$curdatetime = date("Y-m-d H:i:s");	
	
	$sql2 = "SELECT id, feedback_status
	FROM pg_work_amendment
	WHERE id = '$workAmendmentId'
	AND pg_thesis_id = '$thesisId'
	AND pg_proposal_id = '$proposalId'
	AND student_matrix_no = '$studentMatrixNo'
	AND reviewer_id = '$user_id'
	AND archived_status IS NULL";
	$result_sql2 = $db->query($sql2);
	$db->next_record();
	$theFeedbackStatus = $db->f('feedback_status');
	
	$row_cnt2 = mysql_num_rows($result_sql2);
	if ($theFeedbackStatus == 'FVE') {
		$msg[] = "<div class=\"error\"><span>The submission is aborted. The list has been verified already. It is now pending at Supervisee for amendment.</span></div>";
	}
	else if ($theFeedbackStatus == 'FRQ') {
		$msg[] = "<div class=\"error\"><span>The submission is aborted. The feedback list is currently pending at Supervisee for changes.</span></div>";
	}
	if (count($panelEmployeeId) == 0) {
		$msg[] = "<div class=\"error\"><span>The submission is aborted. No Evaluation Panel has been assigned for this student.</span></div>";
	}
	if(empty($msg))  
	{			
		$sql13 = "SELECT id, pg_thesis_id, pg_proposal_id, student_matrix_no, reviewer_id, pg_work_evaluation_id, 
		pg_calendar_id, feedback_date, feedback_status, status, insert_by, insert_date, modify_by, modify_date
		FROM pg_work_amendment
		WHERE id = '$workAmendmentId'";
		$dbb->query($sql13);
		$dbb->next_record();
		
		$theWorkEvaluationId = $dbb->f('pg_work_evaluation_id'); 
		$theCalendarId = $dbb->f('pg_calendar_id'); 
		$theFeedbackStatus = $dbb->f('feedback_status'); 
		$theInsertBy = $dbb->f('insert_by'); 
		$theInsertDate = $dbb->f('insert_date'); 

		$newWorkAmendmentId = runnum('id','pg_work_amendment');
		$sql14 = "INSERT INTO pg_work_amendment
		(id, pg_thesis_id, pg_proposal_id, student_matrix_no, reviewer_id, pg_work_evaluation_id, 
		pg_calendar_id, feedback_date, feedback_status, status, insert_by, insert_date, modify_by, modify_date)
		VALUES ('$newWorkAmendmentId', '$thesisId', '$proposalId', '$studentMatrixNo', '$user_id', '$theWorkEvaluationId',
		'$theCalendarId', '$curdatetime', 'FVE', 'PND', '$theInsertBy', '$theInsertDate',
		'$user_id', '$curdatetime')";
		$dbd->query($sql14);
		
		$sql15 = "UPDATE pg_work_amendment
		SET archived_status = 'ARC', archived_date = '$curdatetime'
		WHERE id = '$workAmendmentId'";
		$dbd->query($sql15);
		
		for ($i=0;$i<count($workFeedbackId);$i++) {			
			$sql12 = "SELECT c.id
			FROM pg_work_amendment a
			LEFT JOIN pg_work_feedback b ON (b.work_amendment_id = a.id)
			LEFT JOIN pg_work_feedback_detail c ON (c.work_feedback_id = b.id)
			WHERE a.id = '$workAmendmentId'
			AND a.pg_thesis_id = '$thesisId'
			AND a.pg_proposal_id = '$proposalId'
			AND a.student_matrix_no = '$studentMatrixNo'
			AND a.reviewer_id = '$user_id'
			AND b.id = '$workFeedbackId[$i]'
			AND c.status = 'N'
			AND b.archived_status IS NULL
			AND b.archived_status IS NULL";
			$result_sql12 = $db->query($sql12);
			$db->next_record();
			$row_cnt12 = mysql_num_rows($result_sql12);

			if ($row_cnt12 > 0) {
				$sql8 = "SELECT id, work_amendment_id, panel_employee_id, verified_date, feedback_status, 
				insert_by, insert_date, modify_by, modify_date
				FROM pg_work_feedback
				WHERE id = '$workFeedbackId[$i]'
				AND feedback_status = 'FSU'
				AND archived_status IS NULL";
				$dba->query($sql8);
				$dba->next_record();

				$thePanelEmployeeId= $dba->f('panel_employee_id');				
				$theVerifiedDate = $dba->f('verified_date');  
				$theInsertBy = $dba->f('insert_by');  
				$theInsertDate = $dba->f('insert_date');   

				$newWorkFeedbackId = runnum('id','pg_work_feedback');

				$sql9 = "INSERT INTO pg_work_feedback
				(id, work_amendment_id, panel_employee_id, verified_date, feedback_status, status,
				insert_by, insert_date, modify_by, modify_date)
				VALUES ('$newWorkFeedbackId', '$newWorkAmendmentId', '$thePanelEmployeeId', '$curdatetime',
				'FVE', 'PND', '$theInsertBy', '$theInsertDate', '$user_id', '$curdatetime')";
				$dba->query($sql9);

				$sql10 = "UPDATE pg_work_feedback
				SET archived_status = 'ARC', archived_date = '$curdatetime'
				WHERE id = '$workFeedbackId[$i]'
				AND feedback_status = 'FSU'
				AND archived_status IS NULL";
				$dba->query($sql10);
				
				$sql17 = "SELECT id, panel_feedback, page_affected, comment_date, comment, submit_date, status,
				insert_by, insert_date
				FROM pg_work_feedback_detail
				WHERE work_feedback_id = '$workFeedbackId[$i]'
				AND status in ('A','N')
				AND archived_status IS NULL";
				
				$result_sql17 = $dba->query($sql17);
				$dba->next_record();
				$row_cnt17 = mysql_num_rows($result_sql17);
				
				$theWorkFeedbackDetailIdArray = Array(); 
				$thePanelFeedbackArray = Array();
				$thePageAffectedArray = Array();  
				$theCommentDateArray = Array();  
				$theCommentArray = Array();  
				$theSubmitDateArray = Array();  
				$theStatusArray = Array();  
				$theInsertByArray = Array(); 
				$theInsertDateArray = Array(); 

				$k=0;
					
				if ($row_cnt17 > 0) {
					do {
						$theWorkFeedbackDetailIdArray[$k] = $dba->f('id');  
						$thePanelFeedbackArray[$k] = $dba->f('panel_feedback');  
						$thePageAffectedArray[$k] = $dba->f('page_affected');  
						$theCommentDateArray[$k] = $dba->f('comment_date');  
						$theCommentArray[$k] = $dba->f('comment'); 
						$theSubmitDateArray[$k] = $dba->f('submit_date');  
						$theStatusArray[$k] = $dba->f('status');  
						$theInsertByArray[$k] = $dba->f('insert_by'); 
						$theInsertDateArray[$k] = $dba->f('insert_date'); 
						$k++;
					} while ($dba->next_record());
					
					for ($l=0;$l<$row_cnt17;$l++) {
						if ($theStatusArray[$l] == "A") {
							$newWorkFeedbackDetailId = runnum('id','pg_work_feedback_detail');
							$sql18 = "INSERT INTO pg_work_feedback_detail
							(id, work_feedback_id, panel_feedback, page_affected, comment_date, comment, submit_date, 
							status, insert_by, insert_date, modify_by, modify_date)
							VALUES ('$newWorkFeedbackDetailId', '$newWorkFeedbackId', '$thePanelFeedbackArray[$l]', '$thePageAffectedArray[$l]', 
							'$theCommentDateArray[$l]', '$theCommentArray[$l]', '$theSubmitDateArray[$l]', '$theStatusArray[$l]', '$theInsertByArray[$l]', 
							'$theInsertDateArray[$l]', '$user_id', '$curdatetime')";
							$dba->query($sql18);
							
							$sql19 = "UPDATE pg_work_feedback_detail
							SET archived_status = 'ARC', archived_date = '$curdatetime'
							WHERE id = '$theWorkFeedbackDetailIdArray[$l]'
							AND work_feedback_id = '$workFeedbackId[$i]'";
							$dba->query($sql19);
												
							$newWorkAmendmentDetailId = runnum('id','pg_work_amendment_detail');
							$sql20 = "INSERT INTO pg_work_amendment_detail
							(id, work_amendment_id, work_feedback_detail_id, page_after_chg, amendment_before_chg, amendment_after_chg, confirmed_date, 
							confirmed_status, verified_by, verified_date,verified_status, verified_remark, 
							status, insert_by, insert_date, modify_by, modify_date)
							VALUES ('$newWorkAmendmentDetailId', '$newWorkAmendmentId', '$newWorkFeedbackDetailId', null, null, 
							null, null, 'N', null, null, 'N', null, 'PND', '$user_id', '$curdatetime', '$user_id', '$curdatetime')";
							$dba->query($sql20);
						}
						else if ($theStatusArray[$l] == "N") {
							$newWorkAmendmentDetailId = runnum('id','pg_work_amendment_detail');
							$sql20 = "INSERT INTO pg_work_amendment_detail
							(id, work_amendment_id, work_feedback_detail_id, page_after_chg, amendment_before_chg, amendment_after_chg, confirmed_date, 
							confirmed_status, verified_by, verified_date,verified_status, verified_remark, 
							status, insert_by, insert_date, modify_by, modify_date)
							VALUES ('$newWorkAmendmentDetailId', '$newWorkAmendmentId', '$theWorkFeedbackDetailIdArray[$l]', null, null, 
							null, null, 'N', null, null, 'N', null, 'PND', '$user_id', '$curdatetime', '$user_id', '$curdatetime')";
							$dba->query($sql20);
						}
					}				
					$sql11 = "UPDATE pg_work_feedback_detail
					SET work_feedback_id = '$newWorkFeedbackId', status = 'A', modify_by = '$user_id', modify_date = '$curdatetime'
					WHERE work_feedback_id = '$workFeedbackId[$i]'
					AND status = 'N'
					AND archived_status IS NULL";
					$dba->query($sql11);
				}
			}
			else {
				$sql8 = "SELECT id, work_amendment_id, panel_employee_id, verified_date, feedback_status, 
				insert_by, insert_date, modify_by, modify_date
				FROM pg_work_feedback
				WHERE id = '$workFeedbackId[$i]'
				AND feedback_status IN ('FSU', 'FNC')
				AND archived_status IS NULL";
				$dba->query($sql8);
				$dba->next_record();

				$thePanelEmployeeId= $dba->f('panel_employee_id');				
				$theVerifiedDate = $dba->f('verified_date');  
				$theInsertBy = $dba->f('insert_by');  
				$theInsertDate = $dba->f('insert_date');   

				$newWorkFeedbackId = runnum('id','pg_work_feedback');

				$sql9 = "INSERT INTO pg_work_feedback
				(id, work_amendment_id, panel_employee_id, verified_date, feedback_status, status,
				insert_by, insert_date, modify_by, modify_date)
				VALUES ('$newWorkFeedbackId', '$newWorkAmendmentId', '$thePanelEmployeeId', '$theVerifiedDate',
				'FVE', 'PND', '$theInsertBy', '$theInsertDate', '$user_id', '$curdatetime')";
				$dba->query($sql9);

				$sql10 = "UPDATE pg_work_feedback
				SET archived_status = 'ARC', archived_date = '$curdatetime'
				WHERE id = '$workFeedbackId[$i]'
				AND feedback_status IN ('FSU', 'FNC')
				AND archived_status IS NULL";
				$dba->query($sql10);
				
				$sql17 = "SELECT id, panel_feedback, page_affected, comment_date, comment, submit_date, status,
				insert_by, insert_date
				FROM pg_work_feedback_detail
				WHERE work_feedback_id = '$workFeedbackId[$i]'
				AND status = 'A'
				AND archived_status IS NULL";
				
				$result_sql17 = $dba->query($sql17);
				$dba->next_record();
				$row_cnt17 = mysql_num_rows($result_sql17);
				
				$theWorkFeedbackDetailIdArray = Array(); 
				$thePanelFeedbackArray = Array();
				$thePageAffectedArray = Array();  
				$theCommentDateArray = Array();  
				$theCommentArray = Array();  
				$theSubmitDateArray = Array();  
				$theStatusArray = Array();  
				$theInsertByArray = Array(); 
				$theInsertDateArray = Array(); 

				$k=0;
					
				if ($row_cnt17 > 0) {
					do {
						$theWorkFeedbackDetailIdArray[$k] = $dba->f('id');  
						$thePanelFeedbackArray[$k] = $dba->f('panel_feedback');  
						$thePageAffectedArray[$k] = $dba->f('page_affected');  
						//$theCommentDateArray[$k] = $dba->f('comment_date');  
						//$theCommentArray[$k] = $dba->f('comment'); 
						$theSubmitDateArray[$k] = $dba->f('submit_date');  
						$theStatusArray[$k] = $dba->f('status');  
						$theInsertByArray[$k] = $dba->f('insert_by'); 
						$theInsertDateArray[$k] = $dba->f('insert_date'); 
						$k++;
					} while ($dba->next_record());
					
					for ($l=0;$l<$row_cnt17;$l++) {
						$newWorkFeedbackDetailId = runnum('id','pg_work_feedback_detail');
						$sql18 = "INSERT INTO pg_work_feedback_detail
						(id, work_feedback_id, panel_feedback, page_affected, comment_date, comment, submit_date, 
						status, insert_by, insert_date, modify_by, modify_date)
						VALUES ('$newWorkFeedbackDetailId', '$newWorkFeedbackId', '$thePanelFeedbackArray[$l]', '$thePageAffectedArray[$l]', 
						null, null, '$theSubmitDateArray[$l]', '$theStatusArray[$l]', '$theInsertByArray[$l]', 
						'$theInsertDateArray[$l]', '$user_id', '$curdatetime')";
						$dba->query($sql18);
						
						$sql19 = "UPDATE pg_work_feedback_detail
						SET archived_status = 'ARC', archived_date = '$curdatetime'
						WHERE id = '$theWorkFeedbackDetailIdArray[$l]'
						AND work_feedback_id = '$workFeedbackId[$i]'";
						$dba->query($sql19);
						
						$newWorkAmendmentDetailId = runnum('id','pg_work_amendment_detail');
						$sql20 = "INSERT INTO pg_work_amendment_detail
						(id, work_amendment_id, work_feedback_detail_id, page_after_chg, amendment_before_chg, amendment_after_chg, confirmed_date, 
						confirmed_status, verified_by, verified_date,verified_status, verified_remark, 
						status, insert_by, insert_date, modify_by, modify_date)
						VALUES ('$newWorkAmendmentDetailId', '$newWorkAmendmentId', '$newWorkFeedbackDetailId', null, null, 
						null, null, 'N', null, null, 'N', null, 'PND', '$user_id', '$curdatetime', '$user_id', '$curdatetime')";
						$dba->query($sql20);
					}	
				}
			}
			
		}		
		$msg[] = "<div class=\"success\"><span>The provided feedback by each Evaluation Panel has been verified and submitted back to the Supervisee for amendment.</span></div>";
	}
}

if(isset($_POST['btnRequest']) && ($_POST['btnRequest'] <> ""))
{
	$workAmendmentId = $_POST['workAmendmentId'];
	$workFeedbackId = $_POST['workFeedbackId'];
	$panelEmployeeId = $_POST['panelEmployeeId'];
	$curdatetime = date("Y-m-d H:i:s");	
	
	$sql2 = "SELECT id, feedback_status
	FROM pg_work_amendment
	WHERE id = '$workAmendmentId'
	AND pg_thesis_id = '$thesisId'
	AND pg_proposal_id = '$proposalId'
	AND student_matrix_no = '$studentMatrixNo'
	AND reviewer_id = '$user_id'
	AND archived_status IS NULL";
	$result_sql2 = $db->query($sql2);
	$db->next_record();
	$theFeedbackStatus = $db->f('feedback_status');
	
	$row_cnt2 = mysql_num_rows($result_sql2);
	if ($theFeedbackStatus == 'FVE') {
		$msg[] = "<div class=\"error\"><span>The submission is aborted. The list has been verified already. It is now pending at Supervisee for amendment.</span></div>";
	}
	else if ($theFeedbackStatus == 'FRQ') {
		$msg[] = "<div class=\"error\"><span>The submission is aborted. The list has been submitted for changes already.</span></div>";
	}
	else if ($theFeedbackStatus == 'FSU'){
		$sql7 = "SELECT c.id
		FROM pg_work_amendment a
		LEFT JOIN pg_work_feedback b ON (b.work_amendment_id = a.id)
		LEFT JOIN pg_work_feedback_detail c ON (c.work_feedback_id = b.id)
		WHERE a.id = '$workAmendmentId'
		AND a.pg_thesis_id = '$thesisId'
		AND a.pg_proposal_id = '$proposalId'
		AND a.student_matrix_no = '$studentMatrixNo'
		AND a.reviewer_id = '$user_id'
		AND c.status = 'N'
		AND a.archived_status IS NULL
		AND b.archived_status IS NULL
		AND b.archived_status IS NULL";
		$result_sql7 = $db->query($sql7);
		$db->next_record();
		$row_cnt7 = mysql_num_rows($result_sql7);
		if ($row_cnt7 == 0) {
			$msg[] = "<div class=\"error\"><span>The submission is aborted. There is no comment for changes have been provided.</span></div>";
		}
	}
	if (count($panelEmployeeId) == 0) {
		$msg[] = "<div class=\"error\"><span>The submission is aborted. No Evaluation Panel has been assigned for this student.</span></div>";
	}

	if(empty($msg))  
	{			
		$sql13 = "SELECT id, pg_thesis_id, pg_proposal_id, student_matrix_no, reviewer_id, pg_work_evaluation_id, 
		pg_calendar_id, feedback_date, feedback_status, status, insert_by, insert_date, modify_by, modify_date
		FROM pg_work_amendment
		WHERE id = '$workAmendmentId'";
		$dbb->query($sql13);
		$dbb->next_record();
		
		$theWorkEvaluationId = $dbb->f('pg_work_evaluation_id'); 
		$theCalendarId = $dbb->f('pg_calendar_id'); 
		$theFeedbackStatus = $dbb->f('feedback_status'); 
		$theInsertBy = $dbb->f('insert_by'); 
		$theInsertDate = $dbb->f('insert_date'); 
		
		$newWorkAmendmentId = runnum('id','pg_work_amendment');
		$sql14 = "INSERT INTO pg_work_amendment
		(id, pg_thesis_id, pg_proposal_id, student_matrix_no, reviewer_id, pg_work_evaluation_id, 
		pg_calendar_id, feedback_date, feedback_status, status, insert_by, insert_date, modify_by, modify_date)
		VALUES ('$newWorkAmendmentId', '$thesisId', '$proposalId', '$studentMatrixNo', '$user_id', '$theWorkEvaluationId',
		'$theCalendarId', '$curdatetime', 'FRQ', null, '$theInsertBy', '$theInsertDate',
		'$user_id', '$curdatetime')";
		$dbd->query($sql14);
		
		$sql15 = "UPDATE pg_work_amendment
		SET archived_status = 'ARC', archived_date = '$curdatetime'
		WHERE id = '$workAmendmentId'";
		$dbd->query($sql15);
		
		for ($i=0;$i<count($workFeedbackId);$i++) {			
			$sql12 = "SELECT c.id
			FROM pg_work_amendment a
			LEFT JOIN pg_work_feedback b ON (b.work_amendment_id = a.id)
			LEFT JOIN pg_work_feedback_detail c ON (c.work_feedback_id = b.id)
			WHERE a.id = '$workAmendmentId'
			AND a.pg_thesis_id = '$thesisId'
			AND a.pg_proposal_id = '$proposalId'
			AND a.student_matrix_no = '$studentMatrixNo'
			AND a.reviewer_id = '$user_id'
			AND b.id = '$workFeedbackId[$i]'
			AND c.status = 'N'
			AND b.archived_status IS NULL
			AND b.archived_status IS NULL";
			$result_sql12 = $db->query($sql12);
			$db->next_record();
			$row_cnt12 = mysql_num_rows($result_sql12);

			if ($row_cnt12 > 0) {
				$sql8 = "SELECT id, work_amendment_id, panel_employee_id, verified_date, feedback_status, 
				insert_by, insert_date, modify_by, modify_date
				FROM pg_work_feedback
				WHERE id = '$workFeedbackId[$i]'
				AND feedback_status = 'FSU'
				AND archived_status IS NULL";
				$dba->query($sql8);
				$dba->next_record();

				$thePanelEmployeeId= $dba->f('panel_employee_id');				
				$theVerifiedDate = $dba->f('verified_date');  
				$theInsertBy = $dba->f('insert_by');  
				$theInsertDate = $dba->f('insert_date');   

				$newWorkFeedbackId = runnum('id','pg_work_feedback');

				$sql9 = "INSERT INTO pg_work_feedback
				(id, work_amendment_id, panel_employee_id, verified_date, feedback_status, 
				insert_by, insert_date, modify_by, modify_date)
				VALUES ('$newWorkFeedbackId', '$newWorkAmendmentId', '$thePanelEmployeeId', '$theVerifiedDate',
				'FRQ', '$theInsertBy', '$theInsertDate', '$user_id', '$curdatetime')";
				$dba->query($sql9);

				$sql10 = "UPDATE pg_work_feedback
				SET archived_status = 'ARC', archived_date = '$curdatetime'
				WHERE id = '$workFeedbackId[$i]'
				AND feedback_status = 'FSU'
				AND archived_status IS NULL";
				$dba->query($sql10);
				
				$sql17 = "SELECT id, panel_feedback, page_affected, comment_date, comment, submit_date, status,
				insert_by, insert_date
				FROM pg_work_feedback_detail
				WHERE work_feedback_id = '$workFeedbackId[$i]'
				AND status = 'A'
				AND archived_status IS NULL";
				
				$result_sql17 = $dba->query($sql17);
				$dba->next_record();
				$row_cnt17 = mysql_num_rows($result_sql17);
				
				$theWorkFeedbackDetailIdArray = Array(); 
				$thePanelFeedbackArray = Array();
				$thePageAffectedArray = Array();  
				$theCommentDateArray = Array();  
				$theCommentArray = Array();  
				$theSubmitDateArray = Array();  
				$theStatusArray = Array();  
				$theInsertByArray = Array(); 
				$theInsertDateArray = Array(); 

				$k=0;
					
				if ($row_cnt17 > 0) {
					do {
						$theWorkFeedbackDetailIdArray[$k] = $dba->f('id');  
						$thePanelFeedbackArray[$k] = $dba->f('panel_feedback');  
						$thePageAffectedArray[$k] = $dba->f('page_affected');  
						$theCommentDateArray[$k] = $dba->f('comment_date');  
						$theCommentArray[$k] = $dba->f('comment'); 
						$theSubmitDateArray[$k] = $dba->f('submit_date');  
						$theStatusArray[$k] = $dba->f('status');  
						$theInsertByArray[$k] = $dba->f('insert_by'); 
						$theInsertDateArray[$k] = $dba->f('insert_date'); 
						$k++;
					} while ($dba->next_record());
					
					for ($l=0;$l<$row_cnt17;$l++) {
						$newWorkFeedbackDetailId = runnum('id','pg_work_feedback_detail');
						$sql18 = "INSERT INTO pg_work_feedback_detail
						(id, work_feedback_id, panel_feedback, page_affected, comment_date, comment, submit_date, 
						status, insert_by, insert_date, modify_by, modify_date)
						VALUES ('$newWorkFeedbackDetailId', '$newWorkFeedbackId', '$thePanelFeedbackArray[$l]', '$thePageAffectedArray[$l]', 
						'$theCommentDateArray[$l]', '$theCommentArray[$l]', '$theSubmitDateArray[$l]', '$theStatusArray[$l]', '$theInsertByArray[$l]', 
						'$theInsertDateArray[$l]', '$user_id', '$curdatetime')";
						$dba->query($sql18);
						
						$sql19 = "UPDATE pg_work_feedback_detail
						SET archived_status = 'ARC', archived_date = '$curdatetime'
						WHERE id = '$theWorkFeedbackDetailIdArray[$l]'
						AND work_feedback_id = '$workFeedbackId[$i]'";
						$dba->query($sql19);
					}				
					$sql11 = "UPDATE pg_work_feedback_detail
					SET work_feedback_id = '$newWorkFeedbackId', status = 'A', modify_by = '$user_id', modify_date = '$curdatetime'
					WHERE work_feedback_id = '$workFeedbackId[$i]'
					AND status = 'N'
					AND archived_status IS NULL";
					$dba->query($sql11);
				}
			}
			else {
				$sql8 = "SELECT id, work_amendment_id, panel_employee_id, verified_date, feedback_status, 
				insert_by, insert_date, modify_by, modify_date
				FROM pg_work_feedback
				WHERE id = '$workFeedbackId[$i]'
				AND feedback_status IN ('FSU', 'FNC')
				AND archived_status IS NULL";
				$dba->query($sql8);
				$dba->next_record();

				$thePanelEmployeeId= $dba->f('panel_employee_id');				
				$theVerifiedDate = $dba->f('verified_date');  
				$theInsertBy = $dba->f('insert_by');  
				$theInsertDate = $dba->f('insert_date');  

				$newWorkFeedbackId = runnum('id','pg_work_feedback');

				$sql9 = "INSERT INTO pg_work_feedback
				(id, work_amendment_id, panel_employee_id, verified_date, feedback_status, 
				insert_by, insert_date, modify_by, modify_date)
				VALUES ('$newWorkFeedbackId', '$newWorkAmendmentId', '$thePanelEmployeeId', '$theVerifiedDate',
				'FNC', '$theInsertBy', '$theInsertDate', '$user_id', '$curdatetime')";
				$dba->query($sql9);

				$sql10 = "UPDATE pg_work_feedback
				SET archived_status = 'ARC', archived_date = '$curdatetime'
				WHERE id = '$workFeedbackId[$i]'
				AND feedback_status IN ('FSU', 'FNC')
				AND archived_status IS NULL";
				$dba->query($sql10);
				
				$sql17 = "SELECT id, panel_feedback, page_affected, comment_date, comment, submit_date, status,
				insert_by, insert_date
				FROM pg_work_feedback_detail
				WHERE work_feedback_id = '$workFeedbackId[$i]'
				AND status = 'A'
				AND archived_status IS NULL";
				
				$result_sql17 = $dba->query($sql17);
				$dba->next_record();
				$row_cnt17 = mysql_num_rows($result_sql17);
				
				$theWorkFeedbackDetailIdArray = Array(); 
				$thePanelFeedbackArray = Array();
				$thePageAffectedArray = Array();  
				$theCommentDateArray = Array();  
				$theCommentArray = Array();  
				$theSubmitDateArray = Array();  
				$theStatusArray = Array();  
				$theInsertByArray = Array(); 
				$theInsertDateArray = Array(); 

				$k=0;
					
				if ($row_cnt17 > 0) {
					do {
						$theWorkFeedbackDetailIdArray[$k] = $dba->f('id');  
						$thePanelFeedbackArray[$k] = $dba->f('panel_feedback');  
						$thePageAffectedArray[$k] = $dba->f('page_affected');  
						$theCommentDateArray[$k] = $dba->f('comment_date');  
						$theCommentArray[$k] = $dba->f('comment'); 
						$theSubmitDateArray[$k] = $dba->f('submit_date');  
						$theStatusArray[$k] = $dba->f('status');  
						$theInsertByArray[$k] = $dba->f('insert_by'); 
						$theInsertDateArray[$k] = $dba->f('insert_date'); 
						$k++;
					} while ($dba->next_record());
					
					for ($l=0;$l<$row_cnt17;$l++) {
						$newWorkFeedbackDetailId = runnum('id','pg_work_feedback_detail');
						$sql18 = "INSERT INTO pg_work_feedback_detail
						(id, work_feedback_id, panel_feedback, page_affected, comment_date, comment, submit_date, 
						status, insert_by, insert_date, modify_by, modify_date)
						VALUES ('$newWorkFeedbackDetailId', '$newWorkFeedbackId', '$thePanelFeedbackArray[$l]', '$thePageAffectedArray[$l]', 
						'$theCommentDateArray[$l]', '$theCommentArray[$l]', '$theSubmitDateArray[$l]', '$theStatusArray[$l]', '$theInsertByArray[$l]', 
						'$theInsertDateArray[$l]', '$user_id', '$curdatetime')";
						$dba->query($sql18);
						
						$sql19 = "UPDATE pg_work_feedback_detail
						SET archived_status = 'ARC', archived_date = '$curdatetime'
						WHERE id = '$theWorkFeedbackDetailIdArray[$l]'
						AND work_feedback_id = '$workFeedbackId[$i]'";
						$dba->query($sql19);
					}	
				}
			}
			
		}		
		$msg[] = "<div class=\"success\"><span>The request change for the provided feedback by each Evaluation Panel has been submitted successfully.</span></div>";
	}	
}

if ($newWorkAmendmentId!="") $workAmendmentId = $newWorkAmendmentId;

$sql1 = "SELECT a.id as work_feedback_id, a.panel_employee_id, a.feedback_status, b.description as feedback_status_desc, 
DATE_FORMAT(a.verified_date,'%d-%b-%Y %h:%i%p') as verified_date, b.remarks
FROM pg_work_feedback a
LEFT JOIN ref_amendment_status b ON (b.id = a.feedback_status)
WHERE a.work_amendment_id = '$workAmendmentId'
AND a.archived_status IS NULL
AND b.status = 'A'
ORDER BY a.id";

$result_sql1 = $dbg->query($sql1); 
$dbg->next_record();
$row_cnt1 = mysql_num_rows($result_sql1);

$workFeedbackIdArray = Array();
$panelEmployeeIdArray = Array();
$panelEmployeeNameArray = Array();
$feedbackStatusArray = Array();
$feedbackStatusDescArray = Array();
$verifiedDateArray = Array();
$remarksArray = Array();

$i=0;
do {
	$panelEmployeeIdArray[$i] = $dbg->f('panel_employee_id');
	
	$sql5 = "SELECT name
	FROM new_employee
	WHERE empid = '$panelEmployeeIdArray[$i]'";
	
	$dbc->query($sql5); 
	$dbc->next_record();
	
	$panelEmployeeNameArray[$i] = $dbc->f('name');
	$workFeedbackIdArray[$i] = $dbg->f('work_feedback_id');
	$reviewedStatusArray[$i] = $dbg->f('reviewed_status');
	$feedbackStatusArray[$i] = $dbg->f('feedback_status');
	$feedbackStatusDescArray[$i] = $dbg->f('feedback_status_desc');
	$reviewedStatusDescArray[$i] = $dbg->f('reviewed_status_desc');
	$verifiedDateArray[$i] = $dbg->f('verified_date');
	$remarksArray[$i] = $dbg->f('remarks');
	$i++;
} while ($dbg->next_record());

$sql3 = "SELECT DATE_FORMAT(defense_date,'%d-%b-%Y') as defense_date, 
DATE_FORMAT(defense_stime,'%h:%i%p') as defense_stime,
DATE_FORMAT(defense_etime,'%h:%i%p') as defense_etime, venue
FROM pg_calendar
WHERE id = '$calendarId'
AND status = 'A'
AND archived_status IS NULL";
$dba->query($sql3);
$dba->next_record();
$defenseDate = $dba->f('defense_date');
$defenseSTime = $dba->f('defense_stime');
$defenseETime = $dba->f('defense_etime');
$defenseVenue = $dba->f('venue');

$sql4 = "SELECT description
FROM ref_work_marks
WHERE id = '$workMarksId'
AND status = 'A'";
$dba->query($sql4);
$dba->next_record();
$workMarksDesc = $dba->f('description');

$sql5 = "SELECT description
FROM ref_work_marks
WHERE id = '$proposedMarksId'
AND status = 'A'";
$dba->query($sql5);
$dba->next_record();
$proposedMarksDesc = $dba->f('description');

$sql6 = "SELECT description
FROM ref_proposal_status
WHERE id = '$workEvaluationStatus'
AND status = 'A'";
$dba->query($sql6);
$dba->next_record();
$workEvaluationDesc = $dba->f('description');

$sql_sv = "SELECT a.pg_employee_empid, a.ref_supervisor_type_id, d.description as supervisor_type,
DATE_FORMAT(a.acceptance_date,'%d-%b-%Y %h:%i%p') as acceptance_date, h.description as role_status_desc
FROM pg_supervisor a 
LEFT JOIN ref_supervisor_type d ON (d.id = a.ref_supervisor_type_id) 
LEFT JOIN pg_thesis f ON (f.student_matrix_no = a.pg_student_matrix_no)
LEFT JOIN pg_proposal g ON (g.pg_thesis_id = f.id)
LEFT JOIN ref_role_status h ON (h.id = a.role_status)
WHERE a.pg_student_matrix_no='$user_id'
AND g.pg_thesis_id = '$thesisId'
AND g.id = '$proposalId'
AND a.acceptance_status = 'ACC'
AND a.ref_supervisor_type_id = 'SV'
AND a.role_status = 'PRI'
AND g.verified_status in ('APP','AWC')
AND g.status in ('APP','APC')
AND f.archived_status IS NULL 
AND g.archived_status IS NULL
AND a.status = 'A'";

$result_sql_sv = $db->query($sql_sv); //echo $sql;
$db->next_record();
$row_cnt_supervisor = mysql_num_rows($result_sql_sv);

$employeeId = $db->f('pg_employee_empid');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang; ?>" lang="<?php echo $lang; ?>">

<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
	<meta name="author" content="MJMZ" />

	<title>Manage Work Amendment</title>
    <link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
    <link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
   	<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
	<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
	<link id="bs-css" href="../../../theme/css/button.css" rel="stylesheet" />
	<script src="../../../lib/js/jquery.min2.js"></script>
	<script src="../../../lib/js/jquery.colorbox.js"></script>
	<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
	<!--<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>-->
</head>

<body>
<SCRIPT LANGUAGE="JavaScript">
function verifyConfirm () {
    var confirmSubmit = confirm("Click OK if you confirm to submit it else click Cancel to stay on the same page.");
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
    foreach($msg as $key) {
       echo $key;
    }
}
?>
    <form method="post" id="form-set">
	<table>
		<tr>
			<td><strong>Work Completion - Feedback</strong></td>
		</tr>
	</table>	  
	<input name="workAmendmentId" type="hidden" id="workAmendmentId" value="<?=$workAmendmentId?>">
	<table>
	<?if (substr($studentMatrixNo,0,2) != '07') { 
		$dbConnStudent= $dbc; 
	} 
	else { 
		$dbConnStudent=$dbc1; 
	}
	$sql9 = "SELECT name
	FROM student
	WHERE matrix_no = '$studentMatrixNo'";
	$result9 = $dbConnStudent->query($sql9); 
	
	$dbConnStudent->next_record();
	$studentName = $dbConnStudent->f('name');
	?>
		<tr>
			<td>Student</td>
			<td>:</td>
			<td><label><?=$studentName?> (<?=$studentMatrixNo?>)</label></td>
		</tr>
		<tr>
			<td>Thesis ID</td>
			<td>:</td>
			<td><label><?=$thesisId?></label></td>
		</tr>
		<tr>
			<td>Reference No</td>
			<td>:</td>
			<td><label><?=$referenceNo?></label></td>
			</td>
		</tr>
		<tr>
			<td>Evaluation Schedule</td>
			<td>:</td>
			<td><label><?=$defenseDate?>, <?=$defenseSTime?> to <?=$defenseETime?>, <?=$defenseVenue?></label></td>
		</tr>
		<tr>
			<td>Evaluation Panel Status</td>
			<td>:</td>
			<td><label><?=$workMarksDesc?></label></td>
		</tr>
		<tr>
			<td>Schoolboard Status</td>
			<td>:</td>
			<td><label><?=$workEvaluationDesc?> [<?=$proposedMarksDesc?>]</label></td>
		</tr>		
	</table>
	<br>
	<table>
		<tr>
			<td><label><strong>List of Evaluation Panel</strong></label></td>
		</tr>
		<tr>
			<td>Searching Results: <?=$row_cnt1?> record(s) found</td>
		</tr>
	  </table>
	 	<? if($row_cnt1 > 0) {?>
		<div class = "viewA" style="overflow:auto;width: 980px; height: 150px;">
		<? } else { ?>
		<div class = "viewA" style="overflow:auto;width: 980px; height: 100px;">
		<? } ?>
      <table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="80%" class="thetable">
        <tr>
		  <th width="5%">No</th>
		  <th width="10%" align="left">Staff ID</th>
		  <th width="30%" align="left">Panel Evaluation Name</th>
		  <th width="15%" align="left">Last Update</th>
		  <th width="15%" align="left">Feedback Status</th>
		</tr>  
		<?
		if ($row_cnt1 > 0) {		
			for ($j=0; $j<$row_cnt1; $j++){	?>
				<?
				if($j % 2) $color ="first-row"; else $color = "second-row";
				?>			
				<tr class="<?=$color?>">
					<input name="workFeedbackId[]" type="hidden" id="workFeedbackId" value="<?=$workFeedbackIdArray[$j]?>">
					<input name="panelEmployeeId[]" type="hidden" id="panelEmployeeId" value="<?=$panelEmployeeIdArray[$j]?>">
					<td align="center"><?=$j+1?>.</td>
					<td><?=$panelEmployeeIdArray[$j]?></td>
					<td><?=$panelEmployeeNameArray[$j]?></td>
					<td><?=$verifiedDateArray[$j]?></td>
					<?
					$sql3 = "SELECT id
					FROM pg_work_feedback_detail 
					WHERE work_feedback_id = '$workFeedbackIdArray[$j]'
					AND status IN ('A','N')
					AND archived_status IS NULL
					ORDER BY page_affected";

					$result_sql3 = $dba->query($sql3);
					$dba->next_record();
					$row_cnt3 = mysql_num_rows($result_sql3);
					
					$sql16 = "SELECT id
					FROM pg_work_feedback_detail 
					WHERE work_feedback_id = '$workFeedbackIdArray[$j]'
					AND status IN ('N')
					AND archived_status IS NULL
					ORDER BY page_affected";

					$result_sql16 = $dba->query($sql16);
					$dba->next_record();
					$row_cnt16 = mysql_num_rows($result_sql16);
					?>
					<?if ($feedbackStatusArray[$j]=="FVE"){
						?>
						<td align="left"><a href="../work/work_review_feedback_view.php?wfid=<?=$workFeedbackIdArray[$j]?>&eid=<?=$panelEmployeeIdArray[$j]?>&waid=<?=$workAmendmentId?>&tid=<?=$thesisId?>&ref=<?=$referenceNo?>&pid=<?=$proposalId?>&cid=<?=$calendarId?>&wmid=<?=$workMarksId?>&wes=<?=$workEvaluationStatus?>&pmid=<?=$proposedMarksId?>&mn=<?=$studentMatrixNo?>" title="<?=$remarksArray[$j]?>"><?=$feedbackStatusDescArray[$j]?></a></td>
						<?
					}
					else if ($feedbackStatusArray[$j]=="FSU"){
						?>
						<td align="left"><a href="../work/work_review_feedback_change.php?wfid=<?=$workFeedbackIdArray[$j]?>&eid=<?=$panelEmployeeIdArray[$j]?>&waid=<?=$workAmendmentId?>&tid=<?=$thesisId?>&ref=<?=$referenceNo?>&pid=<?=$proposalId?>&cid=<?=$calendarId?>&wmid=<?=$workMarksId?>&wes=<?=$workEvaluationStatus?>&pmid=<?=$proposedMarksId?>&mn=<?=$studentMatrixNo?>" title="<?=$remarksArray[$j]?>"><?=$feedbackStatusDescArray[$j]?><br>(<?=$row_cnt16?>/<?=$row_cnt3?>)</a></td>
					<?}
					else if (($feedbackStatusArray[$j]=="FRQ") || ($feedbackStatusArray[$j]=="FNC")){
						?>
						<td align="left"><a href="../work/work_review_feedback_view.php?wfid=<?=$workFeedbackIdArray[$j]?>&eid=<?=$panelEmployeeIdArray[$j]?>&waid=<?=$workAmendmentId?>&tid=<?=$thesisId?>&ref=<?=$referenceNo?>&pid=<?=$proposalId?>&cid=<?=$calendarId?>&wmid=<?=$workMarksId?>&wes=<?=$workEvaluationStatus?>&pmid=<?=$proposedMarksId?>&mn=<?=$studentMatrixNo?>" title="<?=$remarksArray[$j]?>"><?=$feedbackStatusDescArray[$j]?></a></td>
					<?}
					else if ($feedbackStatusArray[$j]=="PND"){
						?>
						<td><?=$feedbackStatusDescArray[$j]?></td>
						<?
					}?>					
				</tr>		
			<? }			
		}		
		else {
			?>
			<table>
				<tr>
					<td>No record(s) found.</td>
				</tr>
			</table>
			<?
		}?>
	  </table>
    </div>
	<table>				
		<tr>
			<td><strong>Notes:</strong><br>
			1. Once you are done with the review, please click <strong>Verification Done</strong> button to confirm your verification and get your Supervisee to proceed with necessary action.<br>
			2. If you need your Supervisee to update the feedback based on your comment, please click <strong>Request Change</strong> button.
			</td>
		</tr>
	</table>	
	<table>
		<tr>		
			<td><input type="submit" name="btnVerify" id="btnVerify" onClick="return verifyConfirm()" value="Verification Done"/></td>
			<td><input type="submit" name="btnRequest" id="btnRequest" onClick="return verifyConfirm()" value="Request Change"/></td>
			<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../work/work_review_amendment.php';" /></td>			
		</tr>
	</table>
    </form>
</div>
</body>
</html>