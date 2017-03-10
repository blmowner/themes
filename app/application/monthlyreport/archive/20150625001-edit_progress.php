<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Submit Monthly Progress Report</title>
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
		<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>
	</head> 
	<body> 
	
<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: edit_progress.php
//
// Created by: Zuraimi
// Created Date: 17-Mar-2015
// Modified by: Zuraimi
// Modified Date: 17-Mar-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

session_start();
$user_id=$_SESSION['user_id'];
$thesisId = $_GET['tid']; 
$proposalId = $_GET['pid']; 
$referenceNo = $_GET['ref']; 

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
    global $db;
    
    $run_start = "0001";
    
    $sql_slct_max = "SELECT MAX(SUBSTR($column_name,2,11)) AS run_id FROM $tblname";
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
	
	$content_checkbox = $_POST['content_checkbox'];
	//$id = $_POST['id'];
	$progressId = $_POST['id'];
	$oldProgressId = $_POST['id'];
	$thesisId = $_POST['thesisId'];
	$proposalId = $_POST['proposalId'];
	$reportMonth = $_POST['reportMonth'];		
	$reportYear = $_POST['reportYear'];
	$meetingDate = $_POST['meetingDate'];
	$tmpStartTime = $_POST['startHour']." ".$_POST['selectStartPM'];
	$tmpEndTime = $_POST['endHour']." ".$_POST['selectEndPM'];
	$progressStatus = $_POST['progressStatus']; 
	$progressDetailId = $_POST['progressDetailId'];
	$studentIssues = $_POST['studentIssues'];
	$advice = $_POST['advice'];
	$supervisorIdArray = $_POST['supervisorIdArray'];
	$referenceNo = $_POST['referenceNo'];
	$contentDescriptionArray = Array();
	$chapterId = $_POST['chapterId'];
	$subchapterId = $_POST['subchapterId'];
	$totalNoOfContent = $_POST['totalNoOfContent'];
	$curdatetime = date("Y-m-d H:i:s");	
	$chapterNo = $_POST['chapterNo'];
	$subchapterNo = $_POST['subchapterNo'];
	$confirmAcceptanceDate = $_POST['confirmAcceptanceDate'];
	$firstMonthlyReportParam = $_POST['firstMonthlyReportParam'];
	$firstMonthlyReport = $_POST['firstMonthlyReport'];
	$expectedReport = $_POST['expectedReport'];
	
	$msg = Array();
	if (empty($_POST['reportMonth'])) $msg[] = "<div class=\"error\"><span>Please select Month for this monthly progress report from the given list.</span></div>";
	if (empty($_POST['reportYear'])) $msg[] = "<div class=\"error\"><span>Please select Year for this monthly progress report from the given list.</span></div>";
	
	$count = count($content_checkbox);
	if ($count == 0) {
		$msg[] = "<div class=\"error\"><span>Please tick the checkbox for which content has been discussed for this monthly progress report.</span></div>";
	}
	
	if (empty($_POST["studentIssues"])) $msg[] = "<div class=\"error\"><span>Please enter the description of topic or issues facing by Student. It will be reverted back to the previous one if unintentionally left blank.</span></div>";
	if (empty($_POST["advice"])) $msg[] = "<div class=\"error\"><span>Please enter the Advice from Supervisor & List of Action to be taken by Student. It will be reverted back to the previous one if unintentionally left blank.</span></div>";
	
	$sql0 = "SELECT id
	FROM pg_progress
	WHERE student_matrix_no = '$user_id'
	AND reference_no <> '$referenceNo'
	AND report_month = '$reportMonth'
	AND report_year = '$reportYear'";
	
	$result_sql0 = $dba->query($sql0); 
	$dba->next_record();
	$row_cnt_sql0 = mysql_num_rows($result_sql0);		
			
	if ($row_cnt_sql0 > 0 ) {
		$msg[] = "<div class=\"error\"><span>You have already submitted the report for month of $reportMonth $reportYear! Please submit for another new month/year report.</span></div>";
	}
	
	$currentDate1 = date('M-Y');
	$tmpCurrentDate = new DateTime($currentDate1);
	$myTmpCurrentDate = $tmpCurrentDate->format('M-Y');
	$currentDate = new DateTime($myTmpCurrentDate);
	
	$expectedDate1 = date('M-Y', strtotime($reportMonth.' '.$reportYear));
	$expectedDate2 = date('M-Y', strtotime($confirmAcceptanceDate. ' '.($firstMonthlyReportParam).' month'));
	
	$tmpExpectedDate1 = new DateTime($expectedDate1);
	$myTmpExpectedDate1 = $tmpExpectedDate1->format('M-Y');
	$theReportDate = new DateTime($myTmpExpectedDate1);
	
	$tmpExpectedDate2 = new DateTime($expectedDate2);
	$myTmpExpectedDate2 = $tmpExpectedDate2->format('M-Y');
	$theExpectedDate = new DateTime($myTmpExpectedDate2);
	
	if ($theReportDate < $theExpectedDate) {
		$msg[] = "<div class=\"error\"><span>Your Monthly Progress Report for <strong>$expectedDate1</strong> should not be earlier than <strong>$firstMonthlyReport</strong>.\n It should be <strong>$expectedReport</strong>  after the earliest Supervisor's acceptance date <strong>$confirmAcceptanceDate</strong>. </span></div>";
	}
	
	if ($theReportDate > $currentDate) {
		$msg[] = "<div class=\"error\"><span>Your Monthly Progress Report for <strong>$expectedDate1</strong> should not be later than current month <strong>$myTmpCurrentDate</strong>.</div>";
	}

	if(empty($msg)) 
	{
		if ($progressStatus=="") {
			$progressId = runnum('id','pg_progress');
			$referenceNo = "R".runnum2('reference_no','pg_progress');
			
			$sql1 = " INSERT INTO pg_progress
			(id, reference_no, report_month, report_year, submit_date, student_matrix_no, pg_thesis_id, pg_proposal_id,
			issues, advice, status, submit_status, respond_status,
			insert_by, insert_date, modify_by, modify_date )
			VALUES ('$progressId', '$referenceNo', '$reportMonth', '$reportYear','$curdatetime', '$user_id', '$thesisId', '$proposalId', 
			'$studentIssues', '$advice', 'SAV','SAV', 'N', '$user_id', '$curdatetime', '$user_id', '$curdatetime')";
			
			$dba->query($sql1); 
			$newProgressDetailIdArray = Array();
			while (list ($key,$val) = @each ($supervisorIdArray)) 
			{
				$newProgressDetailId = runnum('id','pg_progress_detail');
				$newProgressDetailIdArray[$key] = $newProgressDetailId;
				
				$sql2 = " INSERT INTO pg_progress_detail
				(id, pg_progress_id, pg_employee_empid, issues, advice, status, submit_date,
				insert_by, insert_date, modify_by, modify_date )
				VALUES ('$newProgressDetailId', '$progressId', '$val', '$studentIssues', '$advice', 'SAV', '$curdatetime',
				'$user_id', '$curdatetime', '$user_id', '$curdatetime')";
				
				$dba->query($sql2); 
			}

			for ($k=0; $k<$totalNoOfContent; $k++)
			{	
				$contentDescriptionArray[$k] = $_POST["contentDescription".$k];
				$newDiscussionId = runnum('id','pg_discussion');

				$sql3 = " INSERT INTO pg_discussion
				(id, progress_id, reference_no, pg_thesis_id, pg_proposal_id, student_matrix_no, pg_chapter_id, pg_subchapter_id, discussed_status, content_discussion, 
				insert_by, insert_date, modify_by, modify_date )
				VALUES ('$newDiscussionId', '$progressId', '$referenceNo', '$thesisId', '$proposalId', '$user_id', '$chapterId[$k]', '$subchapterId[$k]',   
				null, '$contentDescriptionArray[$k]', '$user_id', '$curdatetime', '$user_id', '$curdatetime')";
				
				$dba->query($sql3);

				for ($l=0; $l<count($supervisorIdArray); $l++) 
				{
					$newDiscussionDetailId = runnum('id','pg_discussion_detail');
					$sql4 = "INSERT INTO pg_discussion_detail 
					(id, pg_discussion_id, progress_detail_id, pg_employee_empid, discussed_status, content_discussion, insert_by, 
					insert_date, modify_by, modify_date)
					VALUES ('$newDiscussionDetailId', '$newDiscussionId', '$newProgressDetailIdArray[$l]', '$supervisorIdArray[$l]', null, 
					'$contentDescriptionArray[$k]', '$user_id',	'$curdatetime', '$user_id','$curdatetime') ";		
					$dba->query($sql4);			
				}
			}
			
			while (list ($key,$val) = @each ($content_checkbox)) 
			{
				$sql2_1 = "update pg_discussion 
				set discussed_status = 'Y'
				WHERE pg_thesis_id = '$thesisId'
				AND pg_proposal_id = '$proposalId'
				AND pg_chapter_id = '$chapterId[$val]'
				AND pg_subchapter_id = '$subchapterId[$val]'
				AND student_matrix_no = '$user_id'";

				$result2_1 = $dbb->query($sql2_1); 
				$dbb->next_record();
				
				
				$sql2_2 = "update pg_discussion_detail 
				set discussed_status = 'Y'
				WHERE pg_discussion_id IN (
					SELECT id
					FROM pg_discussion
					WHERE pg_thesis_id = '$thesisId'
					AND pg_proposal_id = '$proposalId'
					AND reference_no = '$referenceNo'
					AND pg_chapter_id = '$chapterId[$val]'
					AND pg_subchapter_id = '$subchapterId[$val]'
					AND discussed_status = 'Y'
					AND student_matrix_no = '$user_id')";

				$result2_2 = $dbb->query($sql2_2); 
				$dbb->next_record();

			}
			
			$sql5 = "UPDATE file_upload_progress
			SET progress_id = '$progressId', reference_no = '$referenceNo', 
			upload_status = 'CFM', modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE progress_id IS NULL
			AND reference_no IS NULL
			AND student_matrix_no = '$user_id'
			AND pg_proposal_id = '$proposalId'
			AND attachment_level = 'S'
			AND attachment_type IN ('I','A')
			AND upload_status = 'TMP'
			AND status = 'A'
			AND archived_status IS NULL";
			
			$dba->query($sql5);
			
			$sql6 = "UPDATE pg_progress_meeting
			SET pg_progress_id = '$progressId', reference_no = '$referenceNo', 
			add_status = 'CFM', modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE pg_progress_id IS NULL
			AND reference_no IS NULL
			AND pg_thesis_id = '$thesisId'
			AND pg_proposal_id = '$proposalId'
			AND student_matrix_no = '$user_id'
			AND add_status = 'TMP'
			AND archived_status IS NULL";
			
			$dba->query($sql6);
			
			$msg[] = "<div class=\"success\"><span>Your Monthly Progress Report has been saved successfully.</span></div>";

		}
		else if ($progressStatus=="SAV") {//SAV

			$sql5 = " UPDATE pg_progress
			SET report_month = '$reportMonth', report_year = '$reportYear',	submit_date = '$curdatetime', 
			issues = '$studentIssues', advice = '$advice', status = 'SAV', submit_status = 'SAV', respond_status = 'N', modify_by = '$user_id', 
			modify_date = '$curdatetime'
			WHERE id = '$progressId'
			AND reference_no = '$referenceNo'
			AND student_matrix_no = '$user_id'
			AND pg_thesis_id = '$thesisId'
			AND pg_proposal_id = '$proposalId'";

			$dba->query($sql5);
			
			while (list ($key,$val) = @each ($supervisorIdArray)) 
			{
				$sql5_4 = "SELECT id, pg_progress_id, pg_employee_empid, status, issues, advice, responded_status, responded_date, 
				submit_date, insert_by, insert_date, modify_by, modify_date
				FROM pg_progress_detail
				WHERE pg_progress_id = '$progressId'
				AND pg_employee_empid = '$val'
				AND archived_status is NULL";
				
				$result_5_4 = $dba->query($sql5_4);
				$dba->next_record();
				
				$pg_progress_detail_id = $dba->f('id');
				//$pg_progress_id = $dba->f('pg_progress_id'); 
				$employeeId = $dba->f('pg_employee_empid');
				$status = $dba->f('status');
				$issues = $dba->f('issues'); 
				$advice = $dba->f('advice'); 
				$responded_status = $dba->f('responded_status'); 
				$responded_date = $dba->f('responded_date'); 
				$submitDate = $dba->f('submit_date');
				$insert_by = $dba->f('insert_by'); 
				$insert_date = $dba->f('insert_date'); 
				$modify_by = $dba->f('modify_by'); 
				$modify_date = $dba->f('modify_date'); 
				
				$progressDetailIdArray[$key] = $pg_progress_detail_id;
				$row_cnt_5_4 = mysql_num_rows($result_5_4);
				if ($row_cnt_5_4 > 0) {
					if ($status != 'APP') {
						$sql5_5 = " UPDATE pg_progress_detail
						SET issues = '".$_POST['studentIssues']."', status = 'SAV', advice = '".$_POST['advice']."',
						submit_date = '$curdatetime', modify_by = '$user_id', modify_date = '$curdatetime'			
						WHERE pg_progress_id = '$progressId'
						AND pg_employee_empid = '$val'
						AND archived_status IS NULL ";
						
						$dba->query($sql5_5);
					}
				}
				else {
					$newProgressDetailId = runnum('id','pg_progress_detail');
					$newProgressDetailIdArray[$key] = $newProgressDetailId;
					
					$sql2 = " INSERT INTO pg_progress_detail
					(id, pg_progress_id, pg_employee_empid, issues, advice, status, submit_date, 
					insert_by, insert_date, modify_by, modify_date )
					VALUES ('$newProgressDetailId', '$progressId', '$val', '".$_POST['studentIssues']."', '".$_POST['advice']."', 'SAV', 
					'$curdatetime', '$user_id', '$curdatetime', '$user_id', '$curdatetime')";
					
					$dba->query($sql2);
				}
			}
			$discussionIdArray = Array();
			for ($k=0; $k<$totalNoOfContent; $k++)
			{	

				$sql6 = "SELECT id 
				FROM pg_discussion
				WHERE progress_id = '$progressId'
				AND pg_thesis_id = '$thesisId'
				AND pg_proposal_id = '$proposalId'
				AND reference_no = '$referenceNo'
				AND student_matrix_no = '$user_id'
				AND pg_chapter_id = '$chapterId[$k]'
				AND pg_subchapter_id = '$subchapterId[$k]'
				AND archived_status is NULL";
				
				$result_sql6 = $dba->query($sql6);
				$dba->next_record();
				//$id = $dba->f('id');
				$discussionId = $dba->f('id');
				
				$contentDescriptionArray[$k] = $_POST["contentDescription".$k];
				
				$row_cnt_sql6 = mysql_num_rows($result_sql6);
				
				$newDiscussionId = runnum('id','pg_discussion');
				
				
				if ($row_cnt_sql6 == 0) {
					$discussionIdArray[$k] = $newDiscussionId;
					$sql3 = " INSERT INTO pg_discussion
					(id, progress_id, reference_no, pg_thesis_id, pg_proposal_id, student_matrix_no, pg_chapter_id, pg_subchapter_id, discussed_status, 
					content_discussion, insert_by, insert_date, modify_by, modify_date )
					VALUES ('$newDiscussionId', '$progressId', '$referenceNo', '$thesisId', '$proposalId', '$user_id', '$chapterId[$k]', '$subchapterId[$k]',   
					null, '$contentDescriptionArray[$k]', '$user_id', '$curdatetime', '$user_id', '$curdatetime')";
					
					$dba->query($sql3);
				}
				else {
					$discussionIdArray[$k] = $discussionId;
					$sql6_1 = "UPDATE pg_discussion
					SET discussed_status = null
					WHERE id = '$discussionId'
					AND progress_id = '$progressId'
					AND pg_thesis_id = '$thesisId'
					AND pg_proposal_id = '$proposalId'
					AND reference_no = '$referenceNo'
					AND student_matrix_no = '$user_id'
					AND pg_chapter_id = '$chapterId[$k]'
					AND pg_subchapter_id = '$subchapterId[$k]'";
					
					$dba->query($sql6_1);
				}
				
				for ($l=0; $l<count($supervisorIdArray); $l++) 
				{
					$sql6_3 = "SELECT id, status as progress_detail_status
					FROM pg_progress_detail 
					WHERE pg_progress_id = '$progressId'
					AND pg_employee_empid = '$supervisorIdArray[$l]'
					AND status = 'SAV'
					AND archived_status is NULL";
					
					$result_sql6_3 = $dba->query($sql6_3);
					$dba->next_record();
					
					$progressDetailId = $dba->f('id');
					$theProgressDetailIdArray = Array();
					$theProgressDetailIdArray[$l] = $progressDetailId;
					$progressDetailStatus = $dba->f('progress_detail_status');
					
					$row_cnt_sql6_3 = mysql_num_rows($result_sql6_3);
					
					if ($row_cnt_sql6_3 > 0) {
					
						$sql6_4_0 = "SELECT id
						FROM pg_discussion 
						WHERE progress_id = '$progressId'
						AND pg_thesis_id = '$thesisId'
						AND pg_proposal_id = '$proposalId'
						AND reference_no = '$referenceNo'
						AND student_matrix_no = '$user_id'
						AND archived_status IS NULL";
						
						$result_sql6_4_0 = $db->query($sql6_4_0);
						$db->next_record();							
						$row_cnt_sql6_4_0 = mysql_num_rows($result_sql6_4_0);
						
						do {
							$myDiscussionId = $db->f('id'); 
							$sql6_4_1 = "SELECT id
							FROM pg_discussion_detail
							WHERE pg_discussion_id = '$myDiscussionId'
							AND progress_detail_id = '$progressDetailId'
							AND pg_employee_empid = '$supervisorIdArray[$l]'
							AND archived_status IS NULL";
							
							$result_sql6_4_1 = $dba->query($sql6_4_1);
							$dba->next_record();						
							$myDiscussionDetailId = $dba->f('id');
							$row_cnt_sql6_4_1 = mysql_num_rows($result_sql6_4_1);
						
							if ($row_cnt_sql6_4_1 > 0) {
								$sql6_4_2 = "UPDATE pg_discussion_detail 
								SET discussed_status = null, modify_by = '$user_id', modify_date = '$curdatetime'
								WHERE id = '$myDiscussionDetailId'
								AND progress_detail_id = '$progressDetailId'
								AND pg_discussion_id = '$myDiscussionId'
								AND pg_employee_empid = '$supervisorIdArray[$l]'";
								
								$dba->query($sql6_4_2);
							}
							else {
								$newDiscussionDetailId = runnum('id','pg_discussion_detail');
					
								$sql6_4_3 = "INSERT INTO pg_discussion_detail
								(id, pg_discussion_id, progress_detail_id, pg_employee_empid, content_discussion, discussed_status, insert_by, insert_date, modify_by,  modify_date)
								VALUES ('$newDiscussionDetailId', '$myDiscussionId', '$progressDetailId', '$supervisorIdArray[$l]', 
								'$contentDescriptionArray[$k]', null, '$user_id', '$curdatetime', '$user_id', '$curdatetime')";
								
								$dba->query($sql6_4_3);
							}	
						} while ($db->next_record());
					}
				}
			}
			
			while (list ($key,$val) = @each ($content_checkbox)) 
			{
				$sql2_1 = "update pg_discussion 
				set discussed_status = 'Y'
				WHERE progress_id = '$progressId'
				AND pg_thesis_id = '$thesisId'
				AND pg_proposal_id = '$proposalId'
				AND pg_chapter_id = '$chapterId[$val]'
				AND pg_subchapter_id = '$subchapterId[$val]'
				AND student_matrix_no = '$user_id'";

				$result2_1 = $dbb->query($sql2_1); 
				$dbb->next_record();
				
			}
			
			for ($k=0; $k<$totalNoOfContent; $k++)
			{
				for ($l=0; $l<count($supervisorIdArray); $l++) 
				{
					$sql2_2 = "UPDATE pg_discussion_detail 
					SET discussed_status = 'Y'
					WHERE pg_discussion_id = 
						(SELECT id
						FROM pg_discussion
						WHERE progress_id = '$progressId'
						AND pg_thesis_id = '$thesisId'
						AND pg_proposal_id = '$proposalId'
						AND reference_no = '$referenceNo'
						AND pg_chapter_id = '$chapterId[$k]'
						AND pg_subchapter_id = '$subchapterId[$k]'
						AND student_matrix_no = '$user_id'
						AND discussed_status = 'Y'
						AND archived_status IS NULL)
					/*AND progress_detail_id = '$theProgressDetailIdArray[$l]'*/
					AND pg_employee_empid = '$supervisorIdArray[$l]'
					AND archived_status IS NULL";

					$result2_2 = $dbb->query($sql2_2); 
					$dbb->next_record();
				}
			}
			
			$sql5 = "UPDATE file_upload_progress
			SET progress_id = '$progressId', upload_status = 'CFM', modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE progress_id IS NULL
			AND student_matrix_no = '$user_id'
			AND pg_proposal_id = '$proposalId'
			AND attachment_level = 'S'
			AND attachment_type IN ('I','A')
			AND upload_status = 'TMP'
			AND status = 'A'";
			
			$dba->query($sql5);
			
			$sql6 = "UPDATE pg_progress_meeting
			SET pg_progress_id = '$progressId', add_status = 'CFM', modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE pg_progress_id IS NULL
			AND pg_thesis_id = '$thesisId'
			AND pg_proposal_id = '$proposalId'
			AND student_matrix_no = '$user_id'
			AND add_status = 'TMP'
			AND archived_status IS NULL";
			
			$dba->query($sql6);
			
			$msg[] = "<div class=\"success\"><span>Your Monthly Progress Report has been saved successfully.</span></div>";
			
		}
		else if ($progressStatus=="REQ") {
			
			$sql5_1 = "SELECT id, reference_no, student_matrix_no, pg_thesis_id, pg_proposal_id, status, submit_status, respond_status,
			report_month, report_year, submit_date, issues, advice, insert_by, insert_date, modify_by, modify_date
			FROM pg_progress
			WHERE id = '$progressId'
			AND student_matrix_no = '$user_id'
			AND reference_no = '$referenceNo'
			AND pg_thesis_id = '$thesisId'
			AND pg_proposal_id = '$proposalId'";
			
			$dba->query($sql5_1);
			$dba->next_record();
			
			////$progressId = $dba->f('id'); 
			$reference_no = $dba->f('reference_no');
			$student_matrix_no = $dba->f('student_matrix_no');
			$pg_thesis_id = $dba->f('pg_thesis_id');
			$pg_proposal_id = $dba->f('pg_proposal_id');
			$submitStatus = $dba->f('submit_status');
			
			$newProgressId = runnum('id','pg_progress');
			
			$sql5_2 = "INSERT INTO pg_progress
			(id, reference_no, student_matrix_no, pg_thesis_id, pg_proposal_id, status, submit_status, respond_status, report_month, report_year, 
			submit_date, issues, advice, insert_by, insert_date, modify_by, modify_date)
			VALUES ('$newProgressId', '$reference_no', '$student_matrix_no', '$pg_thesis_id', '$pg_proposal_id', 'SAV', 'SAV', 'N',
			'".$_POST['reportMonth']."', '".$_POST['reportYear']."', '$curdatetime', '".$_POST['studentIssues']."', '".$_POST['advice']."',
			'$user_id', '$curdatetime', '$user_id', '$curdatetime')";
			
			
			$dba->query($sql5_2);
			
			$sql5_3 = " UPDATE pg_progress
			SET modify_by = '$user_id', modify_date = '$curdatetime', archived_status = 'ARC', archived_date = '$curdatetime'
			WHERE id = '$progressId'
			AND reference_no = '$referenceNo'
			AND student_matrix_no = '$user_id'
			AND pg_thesis_id = '$thesisId'
			AND pg_proposal_id = '$proposalId'";
			
			$dba->query($sql5_3);
			$newProgressDetailIdArray = Array();
			$oldProgressDetailIdArray = Array();
			while (list ($key,$val) = @each ($supervisorIdArray)) 
			{
				$sql5_4 = "SELECT id, pg_progress_id, pg_employee_empid, status, issues, advice, responded_status, responded_date, 
				submit_date, insert_by, insert_date, modify_by, modify_date
				FROM pg_progress_detail
				WHERE pg_progress_id = '$progressId'
				AND pg_employee_empid = '$val'
				AND archived_status is NULL";
				
				$dba->query($sql5_4);
				$dba->next_record();
				
				$pg_progress_detail_id = $dba->f('id');
				//$pg_progress_id = $dba->f('pg_progress_id'); 
				$employeeId = $dba->f('pg_employee_empid');
				$status = $dba->f('status');
				$issues = $dba->f('issues'); 
				$advice = $dba->f('advice'); 
				$responded_status = $dba->f('responded_status'); 
				$responded_date = $dba->f('responded_date'); 
				$submitDate = $dba->f('submit_date'); 
				$insert_by = $dba->f('insert_by'); 
				$insert_date = $dba->f('insert_date'); 
				$modify_by = $dba->f('modify_by'); 
				$modify_date = $dba->f('modify_date'); 
				
				$row_cnt_sql5_4 = mysql_num_rows($result_sql5_4);
				
				$newProgressDetailId = runnum('id','pg_progress_detail');
				
				$newProgressDetailIdArray[$key] = $newProgressDetailId;
				$oldProgressDetailIdArray[$key] = $pg_progress_detail_id;
				
				if ($row_cnt_sql5_4 > 0) {
					if ($status != 'APP') {
						$sql5_5 = "INSERT INTO pg_progress_detail
						(id, pg_progress_id, pg_employee_empid, status, issues, advice, responded_date, submit_date,
						insert_by, insert_date, modify_by, modify_date)
						VALUES ('$newProgressDetailId', '$newProgressId', '$employeeId', 'SAV', '".$_POST['studentIssues']."', '".$_POST['advice']."',
						null, '$curdatetime', '$user_id', '$curdatetime', '$user_id', '$curdatetime')";
						
						$dba->query($sql5_5);
					}
					else {
						$sql5_5 = "INSERT INTO pg_progress_detail
						(id, pg_progress_id, pg_employee_empid, status, issues, advice, responded_date, submit_date,
						insert_by, insert_date, modify_by, modify_date)
						VALUES ('$newProgressDetailId', '$newProgressId', '$employeeId', '$status', '$issues', 
						'$advice', '$responded_date', '$submitDate', '$user_id', '$curdatetime', '$user_id', '$curdatetime')";
						
						$dba->query($sql5_5);
					}
					$sql5_6 = " UPDATE pg_progress_detail
					SET modify_by = '$user_id', modify_date = '$curdatetime', archived_status = 'ARC', archived_date = '$curdatetime'
					WHERE id = '$pg_progress_detail_id'
					AND pg_progress_id = '$progressId'
					AND pg_employee_empid = '$val'
					AND archived_status IS NULL";
					
					$dba->query($sql5_6);
				}
				else {
					$sql5_5 = "INSERT INTO pg_progress_detail
					(id, pg_progress_id, pg_employee_empid, status, issues, advice, responded_date, submit_date,
					insert_by, insert_date, modify_by, modify_date)
					VALUES ('$newProgressDetailId', '$newProgressId', '$val', 'SAV', '".$_POST['studentIssues']."', '".$_POST['advice']."',
					null, '$curdatetime', '$user_id', '$curdatetime', '$user_id', '$curdatetime')";
					
					$dba->query($sql5_5);
				}				
			}
			
			$newDiscussionIdArray = Array();
			for ($k=0; $k<$totalNoOfContent; $k++)
			{	
				$sql6_0 = "SELECT id, progress_id, pg_thesis_id, pg_proposal_id, student_matrix_no, pg_chapter_id, pg_subchapter_id, 
				content_discussion,	insert_by, insert_date, modify_by, modify_date 
				FROM pg_discussion
				WHERE progress_id = '$progressId'
				AND pg_thesis_id = '$thesisId'
				AND pg_proposal_id = '$proposalId'
				AND reference_no = '$referenceNo'
				AND student_matrix_no = '$user_id'
				AND pg_chapter_id = '$chapterId[$k]'
				AND pg_subchapter_id = '$subchapterId[$k]'
				AND archived_status is NULL";
				
				$result_sql6_0 = $dba->query($sql6_0);
				$dba->next_record();
				
				$discussionId = $dba->f('id');
				$progress_id = $dba->f('progress_id');
				$pg_thesis_id = $dba->f('pg_thesis_id'); 
				$pg_proposal_id = $dba->f('pg_proposal_id'); 
				$student_matrix_no = $dba->f('student_matrix_no'); 
				$pg_chapter_id = $dba->f('pg_chapter_id'); 
				$pg_subchapter_id = $dba->f('pg_subchapter_id'); 
				$content_discussion = $dba->f('content_discussion');
				$insert_by = $dba->f('insert_by'); 
				$insert_date = $dba->f('insert_date'); 
				$modify_by = $dba->f('modify_by');
				$modify_date = $dba->f('modify_date'); 
				
				
				$newDiscussionId = runnum('id','pg_discussion');
				$newDiscussionIdArray[$k] = $newDiscussionId;
				
				$contentDescriptionArray[$k] = $_POST["contentDescription".$k];
				
				$row_cnt_sql6_0 = mysql_num_rows($result_sql6_0);
				if ($row_cnt_sql6_0 > 0) {
					$sql6_1 = "INSERT INTO pg_discussion
					(id, progress_id, reference_no, pg_thesis_id, pg_proposal_id, student_matrix_no, pg_chapter_id, pg_subchapter_id, content_discussion, discussed_status, 
					insert_by, insert_date, modify_by, modify_date)
					VALUES ('$newDiscussionId', '$newProgressId', '$referenceNo', '$pg_thesis_id', '$pg_proposal_id', '$student_matrix_no', 
					'$pg_chapter_id', '$pg_subchapter_id', '$contentDescriptionArray[$k]', null, '$user_id', '$curdatetime', '$user_id', 
					'$curdatetime')";
					
					$dba->query($sql6_1);
					
					
					$sql6_2 = "UPDATE pg_discussion
					SET modify_by = '$user_id', modify_date = '$curdatetime', archived_status = 'ARC', archived_date = '$curdatetime'
					WHERE id = '$discussionId'
					AND progress_id = '$progress_id'
					AND pg_thesis_id = '$thesisId'
					AND pg_proposal_id = '$proposalId'
					AND reference_no = '$referenceNo'
					AND student_matrix_no = '$user_id'
					AND pg_chapter_id = '$chapterId[$k]'
					AND pg_subchapter_id = '$subchapterId[$k]'";
					
					$dba->query($sql6_2);
				}
				else {
					$sql6_1 = "INSERT INTO pg_discussion
					(id, progress_id, reference_no, pg_thesis_id, pg_proposal_id, student_matrix_no, pg_chapter_id, pg_subchapter_id, content_discussion, discussed_status, 
					insert_by, insert_date, modify_by, modify_date)
					VALUES ('$newDiscussionId', '$newProgressId', '$referenceNo', '$thesisId', '$proposalId', '$user_id', '$chapterId[$k]',
					'$subchapterId[$k]', '$contentDescriptionArray[$k]', null, '$user_id', '$curdatetime', '$user_id', '$curdatetime')";
					
					$dba->query($sql6_1);
				}

				for ($l=0; $l<count($supervisorIdArray); $l++) 
				{

					$sql6_3 = "SELECT a.id, a.pg_discussion_id, a.pg_employee_empid, a.content_discussion, a.discussed_status, a.responded_status,
					a.responded_date, a.insert_by, a.insert_date, a.modify_by,  a.modify_date, b.status as progress_detail_status
					FROM pg_discussion_detail a
					LEFT JOIN pg_progress_detail b ON (b.id = a.progress_detail_id)
					WHERE a.pg_discussion_id = '$discussionId'
					AND a.progress_detail_id = '$oldProgressDetailIdArray[$l]'
					AND a.pg_employee_empid = '$supervisorIdArray[$l]'
					AND a.archived_status is NULL";
					
					$result_sql6_3 = $dba->query($sql6_3);
					$dba->next_record();
					
					$discussionDetailId = $dba->f('id');
					$pg_employee_empid = $dba->f('pg_employee_empid'); 
					$content_discussion = $dba->f('content_discussion');
					$discussedStatus = $dba->f('discussed_status');
					$progressDetailStatus = $dba->f('progress_detail_status');
					
					$newDiscussionDetailId = runnum('id','pg_discussion_detail');
					$row_cnt_sql6_3 = mysql_num_rows($result_sql6_3);
					
					if ($row_cnt_sql6_3 > 0) {
						if ($progressDetailStatus == 'IN1' || $progressDetailStatus == 'REQ') {
							$sql6_4 = "INSERT INTO pg_discussion_detail
							(id, pg_discussion_id, progress_detail_id, pg_employee_empid, content_discussion, discussed_status, insert_by,
							insert_date, modify_by,  modify_date)
							VALUES ('$newDiscussionDetailId', '$newDiscussionId', '$newProgressDetailIdArray[$l]', '$pg_employee_empid', 
							'$contentDescriptionArray[$k]', null, '$user_id', '$curdatetime', '$user_id', '$curdatetime')";
							
							$dba->query($sql6_4);														
						}
						else { //APP
							$sql6_4 = "INSERT INTO pg_discussion_detail
							(id, pg_discussion_id, progress_detail_id, pg_employee_empid, content_discussion, discussed_status, insert_by, insert_date, modify_by,  modify_date)
							VALUES ('$newDiscussionDetailId', '$newDiscussionId', '$newProgressDetailIdArray[$l]', '$pg_employee_empid', 
							'$contentDescriptionArray[$k]', '$discussedStatus', '$user_id', '$curdatetime', '$user_id', '$curdatetime')";
							
							$dba->query($sql6_4);
							
							
						}
						$sql6_5 = "UPDATE pg_discussion_detail 
						SET modify_by = '$user_id', modify_date = '$curdatetime', 
						archived_status = 'ARC', archived_date = '$curdatetime'
						WHERE id = '$discussionDetailId'
						AND progress_detail_id = '$oldProgressDetailIdArray[$l]'
						AND pg_discussion_id = '$discussionId'
						AND pg_employee_empid = '$supervisorIdArray[$l]'";
						
						$dba->query($sql6_5);
						
					}
					else {
						
						$sql6_4 = "INSERT INTO pg_discussion_detail
						(id, pg_discussion_id, progress_detail_id, pg_employee_empid, content_discussion, discussed_status, insert_by, 
						insert_date, modify_by,  modify_date)
						VALUES ('$newDiscussionDetailId', '$newDiscussionId', '$newProgressDetailIdArray[$l]', '$supervisorIdArray[$l]', 
						'$contentDescriptionArray[$k]', null, '$user_id', '$curdatetime', '$user_id', '$curdatetime')";
						
						$dba->query($sql6_4);

					}					
				}								
			}
			
			$sql7 = "SELECT fu_cd, fu_document_filename, fu_document_filedesc, fu_document_filetype, fu_document_filedata, fu_document_thumbnail, 
			progress_id, pg_employee_empid, student_matrix_no, pg_proposal_id, insert_by, IFNULL(insert_date,'0000-00-00 00:00:00') as insert_date, 
			modify_by, IFNULL(modify_date,'0000-00-00 00:00:00') as modify_date, attachment_level, attachment_type, status, upload_status
			FROM file_upload_progress
			WHERE progress_id = '$progressId'
			/*AND student_matrix_no = '$user_id*/
			AND upload_status = 'CFM'
			AND status = 'A'
			AND archived_status IS NULL";
			
			$result7 = $dba->query($sql7);
			$dba->next_record();
		
			if (mysql_num_rows($result7) > 0){
				do {
					$fuCd = $dba->f('fu_cd');
					$fuDocumentFilename = $dba->f('fu_document_filename'); 
					$fuDdocumentFiledesc = $dba->f('fu_document_filedesc'); 
					$fuDdocumentFiletype = $dba->f('fu_document_filetype'); 
					$fuDocumentFiledata = addslashes($dba->f('fu_document_filedata')); 
					$fuDocumentThumbnail = $dba->f('fu_document_thumbnail');
					$progressId = $dba->f('progress_id'); 
					$employeeId = $dba->f('pg_employee_empid'); 
					$studentMatrixNo = $dba->f('student_matrix_no'); 
					$proposalId = $dba->f('pg_proposal_id'); 
					$insertBy = $dba->f('insert_by'); 
					$insertDate = $dba->f('insert_date'); 
					$modifyBy = $dba->f('modify_by'); 
					$modifyDate = $dba->f('modify_date'); 
					$attachmentLevel = $dba->f('attachment_level'); 
					$attachmentType = $dba->f('attachment_type'); 
					$status = $dba->f('status');
					$uploadStatus = $dba->f('upload_status');

					$newFcCd = runnum('fu_cd','file_upload_progress');
					$sql7_1 = "INSERT INTO file_upload_progress
					(fu_cd, fu_document_filename, fu_document_filedesc, fu_document_filetype, fu_document_filedata, fu_document_thumbnail, 
					progress_id, student_matrix_no, pg_employee_empid, pg_proposal_id, insert_by, insert_date, modify_by, modify_date, 
					attachment_level, attachment_type, status, upload_status)
					VALUES ('$newFcCd', '$fuDocumentFilename','$fuDdocumentFiledesc', '$fuDdocumentFiletype', '$fuDocumentFiledata', '$fuDocumentThumbnail', 
					'$newProgressId', '$studentMatrixNo', '$employeeId', '$proposalId', '$insertBy', '$insertDate', '$modifyBy', 
					'$modifyDate', '$attachmentLevel',	'$attachmentType', '$status', '$uploadStatus')";
					
					$result7_1 = $db->query($sql7_1);
					
					
				} while ($dba->next_record());
			
			$sql7_2 = "UPDATE file_upload_progress 
			SET modify_by = '$user_id', modify_date = '$curdatetime', 
			archived_status = 'ARC', archived_date = '$curdatetime'
			WHERE progress_id = '$progressId'
			/*AND student_matrix_no = '$user_id'*/
			AND upload_status = 'CFM'
			AND status = 'A'
			AND archived_status IS NULL";
			
			$result7_2 = $dbg->query($sql7_2);
			}
			
			$sql5 = "UPDATE file_upload_progress
			SET progress_id = '$newProgressId', upload_status = 'CFM', modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE progress_id IS NULL
			AND student_matrix_no = '$user_id'
			AND pg_proposal_id = '$proposalId'
			AND attachment_level = 'S'
			AND attachment_type IN ('I','A')
			AND upload_status = 'TMP'
			AND status = 'A'
			AND archived_status IS NULL";			
			
			$dba->query($sql5);
			
			while (list ($key,$val) = @each ($content_checkbox)) 
			{
				$sql2_1 = "UPDATE pg_discussion 
				SET discussed_status = 'Y'
				WHERE progress_id = '$newProgressId'
				AND pg_thesis_id = '$thesisId'
				AND pg_proposal_id = '$proposalId'
				AND reference_no = '$referenceNo'
				AND pg_chapter_id = '$chapterId[$val]'
				AND pg_subchapter_id = '$subchapterId[$val]'
				AND student_matrix_no = '$user_id'
				AND archived_status IS NULL";

				$result2_1 = $dbb->query($sql2_1); 
				$dbb->next_record();
				
				for ($l=0; $l<count($supervisorIdArray); $l++) 
				{
					$sql2_0 = " SELECT id
					FROM pg_progress_detail 
					WHERE id = '$newProgressDetailIdArray[$l]'
					AND status NOT IN ('APP')";
					
					$result2_0 = $db->query($sql2_0); 
					$db->next_record();
					$row_cnt2_0 = mysql_num_rows($result2_0);
					if ($row_cnt2_0 > 0) {
						$sql2_2 = "UPDATE pg_discussion_detail 
						SET discussed_status = 'Y'
						WHERE pg_discussion_id = (
							SELECT id
							FROM pg_discussion
							WHERE progress_id = '$newProgressId'
							AND pg_thesis_id = '$thesisId'
							AND pg_proposal_id = '$proposalId'
							AND reference_no = '$referenceNo'
							AND pg_chapter_id = '$chapterId[$val]'
							AND pg_subchapter_id = '$subchapterId[$val]'
							AND student_matrix_no = '$user_id'
							AND archived_status IS NULL)
						AND progress_detail_id = '$newProgressDetailIdArray[$l]'
						AND pg_employee_empid = '$supervisorIdArray[$l]'
						AND archived_status IS NULL";
						$result2_2 = $dbb->query($sql2_2); 
						$dbb->next_record();

					}
				}
			}
			
			$sql6_0 = "SELECT id, pg_progress_id, pg_thesis_id, pg_proposal_id, student_matrix_no, 
			IFNULL(meeting_date,'0000-00-00 00:00:00') as meeting_date, IFNULL(meeting_stime,'00:00:00') as meeting_stime,
			IFNULL(meeting_etime,'00:00:00') as meeting_etime, add_status, insert_by, insert_date, modify_by, modify_date,
			archived_status, archived_date
			FROM pg_progress_meeting
			WHERE pg_progress_id = '$progressId'
			AND pg_thesis_id = '$thesisId'
			AND pg_proposal_id = '$proposalId'
			AND student_matrix_no = '$user_id'
			AND add_status = 'CFM'
			AND archived_status IS NULL";
			
			$result6_0 = $dba->query($sql6_0);
			$dba->next_record();
			
			$row_cnt = mysql_num_rows($result6_0);
			if ($row_cnt >0){			
				do {
					//$id = $dba->f('id'); 			
					$pg_progress_id = $dba->f('pg_progress_id');
					$pg_thesis_id = $dba->f('pg_thesis_id');
					$pg_proposal_id = $dba->f('pg_proposal_id');
					$student_matrix_no = $dba->f('student_matrix_no');
					$meeting_date = $dba->f('meeting_date');
					$meeting_stime = $dba->f('meeting_stime');
					$meeting_etime = $dba->f('meeting_etime');
					$add_status = $dba->f('add_status');
					$insert_by = $dba->f('insert_by');
					$insert_date = $dba->f('insert_date');
					$modify_by = $dba->f('modify_by');
					$modify_date = $dba->f('modify_date');

					$newProgressMeetingId = runnum('id','pg_progress_meeting');
					
					$sql6_1 = "INSERT INTO pg_progress_meeting
					(id, pg_progress_id, pg_thesis_id, pg_proposal_id, student_matrix_no, meeting_date, meeting_stime, meeting_etime,
					add_status, insert_by, insert_date, modify_by, modify_date)
					VALUES ('$newProgressMeetingId', '$newProgressId', '$pg_thesis_id', '$pg_proposal_id', '$student_matrix_no', 
					'$meeting_date', '$meeting_stime', '$meeting_etime', '$add_status', '$insert_by', '$insert_date', '$modify_by', '$modify_date')";
					
					$db->query($sql6_1);
				} while ($dba->next_record());
			}
			
			$sql6_2 = "UPDATE pg_progress_meeting 
			SET modify_by = '$user_id', modify_date = '$curdatetime', 
			archived_status = 'ARC', archived_date = '$curdatetime'
			WHERE pg_progress_id = '$progressId'
			AND pg_thesis_id = '$thesisId'
			AND pg_proposal_id = '$proposalId'
			AND student_matrix_no = '$user_id'
			AND add_status = 'CMP'
			AND archived_status IS NULL";
			
			$result6_2 = $dbg->query($sql6_2);
			
			$sql6 = "UPDATE pg_progress_meeting
			SET pg_progress_id = '$newProgressId', add_status = 'CFM', modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE pg_progress_id IS NULL
			AND pg_thesis_id = '$thesisId'
			AND pg_proposal_id = '$proposalId'
			AND student_matrix_no = '$user_id'
			AND add_status = 'TMP'
			AND archived_status IS NULL";
			
			$dba->query($sql6);
			
			$msg[] = "<div class=\"success\"><span>Your Monthly Progress Report has been saved successfully.</span></div>";
		}		
		//$msg[] = "<div class=\"success\"><span>Your Monthly Progress Report has been saved successfully.</span></div>";
	}
}

if(isset($_POST['btnSubmit']) && ($_POST['btnSubmit'] <> ""))
{
	$content_checkbox = $_POST['content_checkbox'];
	$progressId = $_POST['id'];
	$oldProgressId = $_POST['id'];
	$thesisId = $_POST['thesisId'];
	$proposalId = $_POST['proposalId'];
	$reportMonth = $_POST['reportMonth'];		
	$reportYear = $_POST['reportYear'];
	$meetingDate = $_POST['meetingDate'];
	$tmpStartTime = $_POST['startHour']." ".$_POST['selectStartPM'];
	$tmpEndTime = $_POST['endHour']." ".$_POST['selectEndPM'];
	$progressStatus = $_POST['progressStatus']; 
	$progressDetailId = $_POST['progressDetailId'];
	$studentIssues = $_POST['studentIssues'];
	$advice = $_POST['advice'];
	$supervisorIdArray = $_POST['supervisorIdArray'];
	$referenceNo = $_POST['referenceNo'];
	$contentDescriptionArray = Array();
	$chapterId = $_POST['chapterId'];
	$subchapterId = $_POST['subchapterId'];
	$totalNoOfContent = $_POST['totalNoOfContent'];
	$curdatetime = date("Y-m-d H:i:s");	
	$chapterNo = $_POST['chapterNo'];
	$subchapterNo = $_POST['subchapterNo'];
	$confirmAcceptanceDate = $_POST['confirmAcceptanceDate'];
	$firstMonthlyReportParam = $_POST['firstMonthlyReportParam'];
	$firstMonthlyReport = $_POST['firstMonthlyReport'];
	$expectedReport = $_POST['expectedReport'];
	
	$msg = Array();
	if (empty($_POST['reportMonth'])) $msg[] = "<div class=\"error\"><span>Please select Month for this monthly progress report from the given list.</span></div>";
	if (empty($_POST['reportYear'])) $msg[] = "<div class=\"error\"><span>Please select Year for this monthly progress report from the given list.</span></div>";
	
	$count = count($content_checkbox);
	if ($count == 0) {
		$msg[] = "<div class=\"error\"><span>Please tick the checkbox for which content has been discussed for this monthly progress report.</span></div>";
	}
	
	if (empty($_POST["studentIssues"])) $msg[] = "<div class=\"error\"><span>Please enter the description of topic or issues facing by Student. It will be reverted back to the previous one if unintentionally left blank.</span></div>";
	if (empty($_POST["advice"])) $msg[] = "<div class=\"error\"><span>Please enter the Advice from Supervisor & List of Action to be taken by Student. It will be reverted back to the previous one if unintentionally left blank.</span></div>";
	
	$sql0 = "SELECT id
	FROM pg_progress
	WHERE student_matrix_no = '$user_id'
	AND reference_no <> '$referenceNo'
	AND report_month = '$reportMonth'
	AND report_year = '$reportYear'";
	
	$result_sql0 = $dba->query($sql0); 
	$dba->next_record();
	$row_cnt_sql0 = mysql_num_rows($result_sql0);		
			
	if ($row_cnt_sql0 > 0 ) {
		$msg[] = "<div class=\"error\"><span>You have already submitted the report for month of $reportMonth $reportYear! Please submit for another new month/year report.</span></div>";
	}
	
	$currentDate1 = date('M-Y');
	$tmpCurrentDate = new DateTime($currentDate1);
	$myTmpCurrentDate = $tmpCurrentDate->format('M-Y');
	$currentDate = new DateTime($myTmpCurrentDate);
	
	$expectedDate1 = date('M-Y', strtotime($reportMonth.' '.$reportYear));
	$expectedDate2 = date('M-Y', strtotime($confirmAcceptanceDate. ' '.($firstMonthlyReportParam).' month'));
	
	$tmpExpectedDate1 = new DateTime($expectedDate1);
	$myTmpExpectedDate1 = $tmpExpectedDate1->format('M-Y');
	$theReportDate = new DateTime($myTmpExpectedDate1);
	
	$tmpExpectedDate2 = new DateTime($expectedDate2);
	$myTmpExpectedDate2 = $tmpExpectedDate2->format('M-Y');
	$theExpectedDate = new DateTime($myTmpExpectedDate2);
	
	if ($theReportDate < $theExpectedDate) {
		$msg[] = "<div class=\"error\"><span>Your Monthly Progress Report for <strong>$expectedDate1</strong> should not be earlier than <strong>$firstMonthlyReport</strong>.\n It should be <strong>$expectedReport</strong>  after the earliest Supervisor's acceptance date <strong>$confirmAcceptanceDate</strong>. </span></div>";
	}
	
	if ($theReportDate > $currentDate) {
		$msg[] = "<div class=\"error\"><span>Your Monthly Progress Report for <strong>$expectedDate1</strong> should not be later than current month <strong>$myTmpCurrentDate</strong>.</div>";
	}
	
	if(empty($msg)) 
	{
		if ($progressStatus=="") {
			$progressId = runnum('id','pg_progress');
			$referenceNo = "R".runnum2('reference_no','pg_progress');
			
			$sql1 = " INSERT INTO pg_progress
			(id, reference_no, report_month, report_year, submit_date, student_matrix_no, pg_thesis_id, pg_proposal_id,
			issues, advice, status, submit_status, respond_status,
			insert_by, insert_date, modify_by, modify_date )
			VALUES ('$progressId', '$referenceNo', '$reportMonth', '$reportYear','$curdatetime', '$user_id', '$thesisId', '$proposalId', 
			'$studentIssues', '$advice', 'IN1','INP', 'N', '$user_id', '$curdatetime', '$user_id', '$curdatetime')";
			
			$dba->query($sql1); 
			$newProgressDetailIdArray = Array();
			while (list ($key,$val) = @each ($supervisorIdArray)) 
			{
				$newProgressDetailId = runnum('id','pg_progress_detail');
				$newProgressDetailIdArray[$key] = $newProgressDetailId;
				$sql2 = " INSERT INTO pg_progress_detail
				(id, pg_progress_id, pg_employee_empid, issues, advice, status, submit_date,
				insert_by, insert_date, modify_by, modify_date )
				VALUES ('$newProgressDetailId', '$progressId', '$val', '$studentIssues', '$advice', 'IN1', '$curdatetime',
				'$user_id', '$curdatetime', '$user_id', '$curdatetime')";
				
				$dba->query($sql2);
			}

			for ($k=0; $k<$totalNoOfContent; $k++)
			{	
				$contentDescriptionArray[$k] = $_POST["contentDescription".$k];
				$newDiscussionId = runnum('id','pg_discussion');

				$sql3 = " INSERT INTO pg_discussion
				(id, progress_id, reference_no, pg_thesis_id, pg_proposal_id, student_matrix_no, pg_chapter_id, pg_subchapter_id, discussed_status, content_discussion, 
				insert_by, insert_date, modify_by, modify_date )
				VALUES ('$newDiscussionId', '$progressId', '$referenceNo', '$thesisId', '$proposalId', '$user_id', '$chapterId[$k]', '$subchapterId[$k]',   
				null, '$contentDescriptionArray[$k]', '$user_id', '$curdatetime', '$user_id', '$curdatetime')";
				
				$dba->query($sql3);

				
				for ($l=0; $l<count($supervisorIdArray); $l++) 
				{
					$newDiscussionDetailId = runnum('id','pg_discussion_detail');

					$sql4 = "INSERT INTO pg_discussion_detail 
					(id, pg_discussion_id, progress_detail_id, pg_employee_empid, discussed_status, content_discussion, insert_by, 
					insert_date, modify_by, modify_date)
					VALUES ('$newDiscussionDetailId', '$newDiscussionId', '$newProgressDetailIdArray[$l]', '$supervisorIdArray[$l]', null, 
					'$contentDescriptionArray[$k]', '$user_id',	'$curdatetime', '$user_id','$curdatetime') ";		
					$dba->query($sql4);	

				}
			}
			
			while (list ($key,$val) = @each ($content_checkbox)) 
			{
				$sql2_1 = "update pg_discussion 
				set discussed_status = 'Y'
				WHERE pg_thesis_id = '$thesisId'
				AND pg_proposal_id = '$proposalId'
				AND pg_chapter_id = '$chapterId[$val]'
				AND pg_subchapter_id = '$subchapterId[$val]'
				AND student_matrix_no = '$user_id'";

				$result2_1 = $dbb->query($sql2_1); 
				$dbb->next_record();
				
				
				$sql2_2 = "update pg_discussion_detail 
				set discussed_status = 'Y'
				WHERE pg_discussion_id IN (
					SELECT id
					FROM pg_discussion
					WHERE pg_thesis_id = '$thesisId'
					AND pg_proposal_id = '$proposalId'
					AND reference_no = '$referenceNo'
					AND pg_chapter_id = '$chapterId[$val]'
					AND pg_subchapter_id = '$subchapterId[$val]'
					AND discussed_status = 'Y'
					AND student_matrix_no = '$user_id')";

				$result2_2 = $dbb->query($sql2_2); 
				$dbb->next_record();

			}
			$sql5 = "UPDATE file_upload_progress
			SET progress_id = '$progressId', reference_no = '$referenceNo', 
			upload_status = 'CFM', modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE progress_id IS NULL
			AND reference_no IS NULL
			AND student_matrix_no = '$user_id'
			AND pg_proposal_id = '$proposalId'
			AND attachment_level = 'S'
			AND attachment_type IN ('I','A')
			AND upload_status = 'TMP'
			AND status = 'A'
			AND archived_status IS NULL";
			
			$dba->query($sql5);
			
			$sql6 = "UPDATE pg_progress_meeting
			SET pg_progress_id = '$progressId', reference_no = '$referenceNo', 
			add_status = 'CFM', modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE pg_progress_id IS NULL
			AND reference_no IS NULL
			AND pg_thesis_id = '$thesisId'
			AND pg_proposal_id = '$proposalId'
			AND student_matrix_no = '$user_id'
			AND add_status = 'TMP'
			AND archived_status IS NULL";
			
			$dba->query($sql6);
			
			$msg[] = "<div class=\"success\"><span>Your Monthly Progress Report has been submitted successfully.</span></div>";		
		
		}
		else if ($progressStatus=="SAV") {//SAV

			$sql5 = " UPDATE pg_progress
			SET report_month = '$reportMonth', report_year = '$reportYear',	submit_date = '$curdatetime', 
			issues = '$studentIssues', advice = '$advice', status = 'IN1', submit_status = 'INP', respond_status = 'N',
			modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE id = '$progressId'
			AND reference_no = '$referenceNo'
			AND student_matrix_no = '$user_id'
			AND pg_thesis_id = '$thesisId'
			AND pg_proposal_id = '$proposalId'";

			$dba->query($sql5);
			
			while (list ($key,$val) = @each ($supervisorIdArray)) 
			{
				$sql5_4 = "SELECT id, pg_progress_id, pg_employee_empid, status, issues, advice, responded_status, responded_date, 
				submit_date, insert_by, insert_date, modify_by, modify_date
				FROM pg_progress_detail
				WHERE pg_progress_id = '$progressId'
				AND pg_employee_empid = '$val'
				AND archived_status is NULL";
				
				$dba->query($sql5_4);
				$dba->next_record();
				
				$pg_progress_detail_id = $dba->f('id');
				//$pg_progress_id = $dba->f('pg_progress_id'); 
				$employeeId = $dba->f('pg_employee_empid');
				$status = $dba->f('status');
				$issues = $dba->f('issues'); 
				$advice = $dba->f('advice'); 
				$responded_status = $dba->f('responded_status'); 
				$submitDate = $dba->f('submit_date'); 
				$responded_date = $dba->f('responded_date'); 
				$insert_by = $dba->f('insert_by'); 
				$insert_date = $dba->f('insert_date'); 
				$modify_by = $dba->f('modify_by'); 
				$modify_date = $dba->f('modify_date'); 
				
				$progressDetailIdArray[$key] = $pg_progress_detail_id;
				
				if ($status != 'APP') {
					$sql5_5 = " UPDATE pg_progress_detail
					SET issues = '".$_POST['studentIssues']."', status = 'IN1', advice = '".$_POST['advice']."', submit_date = '$curdatetime',
					modify_by = '$user_id', modify_date = '$curdatetime'			
					WHERE pg_progress_id = '$progressId'
					AND pg_employee_empid = '$val'
					AND archived_status IS NULL ";
					
					$dba->query($sql5_5);
				}				
			}
			
			for ($k=0; $k<$totalNoOfContent; $k++)
			{	

				$sql6 = "SELECT id 
				FROM pg_discussion
				WHERE progress_id = '$progressId'
				AND pg_thesis_id = '$thesisId'
				AND pg_proposal_id = '$proposalId'
				AND reference_no = '$referenceNo'
				AND student_matrix_no = '$user_id'
				AND pg_chapter_id = '$chapterId[$k]'
				AND pg_subchapter_id = '$subchapterId[$k]'
				AND archived_status is NULL";
				
				$result_sql6 = $dba->query($sql6);
				$dba->next_record();
				//$id = $dba->f('id');
				$discussionId = $dba->f('id');
				
				$contentDescriptionArray[$k] = $_POST["contentDescription".$k];
				
				$row_cnt_sql6 = mysql_num_rows($result_sql6);
				
				$newDiscussionId = runnum('id','pg_discussion');
				$discussionIdArray = Array();
				
				if ($row_cnt_sql6 == 0) {
					$discussionIdArray[$k] = $newDiscussionId;
					$sql3 = " INSERT INTO pg_discussion
					(id, progress_id, reference_no, pg_thesis_id, pg_proposal_id, student_matrix_no, pg_chapter_id, pg_subchapter_id, discussed_status, 
					content_discussion, insert_by, insert_date, modify_by, modify_date )
					VALUES ('$newDiscussionId', '$progressId', '$referenceNo', '$thesisId', '$proposalId', '$user_id', '$chapterId[$k]', '$subchapterId[$k]',   
					null, '$contentDescriptionArray[$k]', '$user_id', '$curdatetime', '$user_id', '$curdatetime')";
					
					$dba->query($sql3);
				}
				else {
					$discussionIdArray[$k] = $discussionId;
					$sql6_1 = "UPDATE pg_discussion
					SET discussed_status = null
					WHERE id = '$discussionId'
					AND progress_id = '$progressId'
					AND reference_no = '$referenceNo'
					AND pg_thesis_id = '$thesisId'
					AND pg_proposal_id = '$proposalId'
					AND student_matrix_no = '$user_id'
					AND pg_chapter_id = '$chapterId[$k]'
					AND pg_subchapter_id = '$subchapterId[$k]'";
					
					$dba->query($sql6_1);
				}
				$myProgressDetailIdArray = Array();
				for ($l=0; $l<count($supervisorIdArray); $l++) 
				{
					$sql6_3 = "SELECT id, status as progress_detail_status
					FROM pg_progress_detail 
					WHERE pg_progress_id = '$progressId'
					AND pg_employee_empid = '$supervisorIdArray[$l]'
					AND status = 'IN1'
					AND archived_status is NULL";
					
					$result_sql6_3 = $dba->query($sql6_3);
					$dba->next_record();
					
					$progressDetailId = $dba->f('id');
					
					$myProgressDetailIdArray[$l] = $progressDetailId;
					$progressDetailStatus = $dba->f('progress_detail_status');
					
					$row_cnt_sql6_3 = mysql_num_rows($result_sql6_3);
					
					if ($row_cnt_sql6_3 > 0) {
					
						$sql6_4_0 = "SELECT id
						FROM pg_discussion 
						WHERE progress_id = '$progressId'
						AND pg_thesis_id = '$thesisId'
						AND pg_proposal_id = '$proposalId'
						AND reference_no = '$referenceNo'
						AND student_matrix_no = '$user_id'
						AND archived_status IS NULL";
						
						$result_sql6_4_0 = $db->query($sql6_4_0);
						$db->next_record();							
						$row_cnt_sql6_4_0 = mysql_num_rows($result_sql6_4_0);
						
						do {
							$myDiscussionId = $db->f('id'); 
							$sql6_4_1 = "SELECT id
							FROM pg_discussion_detail
							WHERE pg_discussion_id = '$myDiscussionId'
							AND progress_detail_id = '$progressDetailId'
							AND pg_employee_empid = '$supervisorIdArray[$l]'
							AND archived_status IS NULL";
							
							$result_sql6_4_1 = $dba->query($sql6_4_1);
							$dba->next_record();						
							$myDiscussionDetailId = $dba->f('id');
							$row_cnt_sql6_4_1 = mysql_num_rows($result_sql6_4_1);
						
							if ($row_cnt_sql6_4_1 > 0) {
								$sql6_4_2 = "UPDATE pg_discussion_detail 
								SET discussed_status = null, modify_by = '$user_id', modify_date = '$curdatetime'
								WHERE id = '$myDiscussionDetailId'
								AND progress_detail_id = '$progressDetailId'
								AND pg_discussion_id = '$myDiscussionId'
								AND pg_employee_empid = '$supervisorIdArray[$l]'";
								
								$dba->query($sql6_4_2);
							}
							else {
								$newDiscussionDetailId = runnum('id','pg_discussion_detail');
					
								$sql6_4_3 = "INSERT INTO pg_discussion_detail
								(id, pg_discussion_id, progress_detail_id, pg_employee_empid, content_discussion, discussed_status, insert_by, insert_date, modify_by,  modify_date)
								VALUES ('$newDiscussionDetailId', '$myDiscussionId', '$progressDetailId', '$supervisorIdArray[$l]', 
								'$contentDescriptionArray[$k]', null, '$user_id', '$curdatetime', '$user_id', '$curdatetime')";
								
								$dba->query($sql6_4_3);
							}	
						} while ($db->next_record());
					}
				}
				
			}
			
			while (list ($key,$val) = @each ($content_checkbox)) 
			{
				$sql2_1 = "UPDATE pg_discussion 
				SET discussed_status = 'Y'
				WHERE progress_id = '$progressId'
				AND pg_thesis_id = '$thesisId'
				AND pg_proposal_id = '$proposalId'
				AND reference_no = '$referenceNo'
				AND pg_chapter_id = '$chapterId[$val]'
				AND pg_subchapter_id = '$subchapterId[$val]'
				AND student_matrix_no = '$user_id'
				AND archived_status IS NULL";

				$result2_1 = $dbb->query($sql2_1); 
				$dbb->next_record();
				
				for ($l=0; $l<count($supervisorIdArray); $l++) 
				{
					$sql2_0 = " SELECT id
					FROM pg_progress_detail 
					WHERE id = '$myProgressDetailIdArray[$l]'
					AND status NOT IN ('APP')";
					
					$result2_0 = $db->query($sql2_0); 
					$db->next_record();
					$row_cnt2_0 = mysql_num_rows($result2_0);
					if ($row_cnt2_0 > 0) {
						$sql2_2 = "UPDATE pg_discussion_detail 
						SET discussed_status = 'Y'
						WHERE pg_discussion_id = (
							SELECT id
							FROM pg_discussion
							WHERE progress_id = '$progressId'
							AND pg_thesis_id = '$thesisId'
							AND pg_proposal_id = '$proposalId'
							AND reference_no = '$referenceNo'
							AND pg_chapter_id = '$chapterId[$val]'
							AND pg_subchapter_id = '$subchapterId[$val]'
							AND student_matrix_no = '$user_id'
							AND archived_status IS NULL)
						AND progress_detail_id = '$myProgressDetailIdArray[$l]'
						AND pg_employee_empid = '$supervisorIdArray[$l]'
						AND archived_status IS NULL";
						$result2_2 = $dbb->query($sql2_2);
						$dbb->next_record();

					}
				}
			}
			
			$sql5 = "UPDATE file_upload_progress
			SET progress_id = '$progressId', upload_status = 'CFM', modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE progress_id IS NULL
			AND student_matrix_no = '$user_id'
			AND pg_proposal_id = '$proposalId'
			AND attachment_level = 'S'
			AND attachment_type IN ('I','A')
			AND upload_status = 'TMP'
			AND status = 'A'
			AND archived_status IS NULL";
			
			$dba->query($sql5);
			
			$sql6 = "UPDATE pg_progress_meeting
			SET pg_progress_id = '$progressId', add_status = 'CFM', modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE pg_progress_id IS NULL
			AND pg_thesis_id = '$thesisId'
			AND pg_proposal_id = '$proposalId'
			AND student_matrix_no = '$user_id'
			AND add_status = 'TMP'
			AND archived_status IS NULL";
			
			$dba->query($sql6);
			
			$msg[] = "<div class=\"success\"><span>Your Monthly Progress Report has been submitted successfully.</span></div>";
			
		}
		else if ($progressStatus=="REQ") {
			
			$sql5_1 = "SELECT id, reference_no, student_matrix_no, pg_thesis_id, pg_proposal_id, status, submit_status, respond_status,
			report_month, report_year, submit_date, issues, advice, insert_by, insert_date, modify_by, modify_date
			FROM pg_progress
			WHERE id = '$progressId'
			AND student_matrix_no = '$user_id'
			AND reference_no = '$referenceNo'
			AND pg_thesis_id = '$thesisId'
			AND pg_proposal_id = '$proposalId'";
			
			$dba->query($sql5_1);
			$dba->next_record();
			
			$progressId = $dba->f('id'); 
			$reference_no = $dba->f('reference_no');
			$student_matrix_no = $dba->f('student_matrix_no');
			$pg_thesis_id = $dba->f('pg_thesis_id');
			$pg_proposal_id = $dba->f('pg_proposal_id');
			$submitStatus = $dba->f('submit_status');
			$respondStatus = $dba->f('respond_status');
			
			$newProgressId = runnum('id','pg_progress');
			
			$sql5_2 = "INSERT INTO pg_progress
			(id, reference_no, student_matrix_no, pg_thesis_id, pg_proposal_id, status, submit_status, respond_status,
			report_month, report_year, submit_date, issues, advice, insert_by, insert_date, modify_by, modify_date)
			VALUES ('$newProgressId', '$reference_no', '$student_matrix_no', '$pg_thesis_id', '$pg_proposal_id', 'IN1', '$submitStatus',
			'$respondStatus', '".$_POST['reportMonth']."', '".$_POST['reportYear']."', '$curdatetime', '".$_POST['studentIssues']."', 
			'".$_POST['advice']."',	'$user_id', '$curdatetime', '$user_id', '$curdatetime')";
			
			
			$dba->query($sql5_2);
			
			$sql5_3 = " UPDATE pg_progress
			SET modify_by = '$user_id', modify_date = '$curdatetime', archived_status = 'ARC', archived_date = '$curdatetime'
			WHERE id = '$progressId'
			AND reference_no = '$referenceNo'
			AND student_matrix_no = '$user_id'
			AND pg_thesis_id = '$thesisId'
			AND pg_proposal_id = '$proposalId'";
			
			$dba->query($sql5_3);
			$newProgressDetailIdArray = Array();
			$oldProgressDetailIdArray = Array();
			while (list ($key,$val) = @each ($supervisorIdArray)) 
			{
				$sql5_4 = "SELECT id, pg_progress_id, pg_employee_empid, status, issues, advice, responded_status, responded_date, 
				submit_date, insert_by, insert_date, modify_by, modify_date
				FROM pg_progress_detail
				WHERE pg_progress_id = '$progressId'
				AND pg_employee_empid = '$val'
				AND archived_status is NULL";
				
				$dba->query($sql5_4);
				$dba->next_record();
				
				$pg_progress_detail_id = $dba->f('id');
				//$pg_progress_id = $dba->f('pg_progress_id'); 
				$employeeId = $dba->f('pg_employee_empid');
				$status = $dba->f('status');
				$issues = $dba->f('issues'); 
				$advice = $dba->f('advice'); 
				$responded_status = $dba->f('responded_status'); 
				$responded_date = $dba->f('responded_date'); 
				$submitDate = $dba->f('submit_date'); 
				$insert_by = $dba->f('insert_by'); 
				$insert_date = $dba->f('insert_date'); 
				$modify_by = $dba->f('modify_by'); 
				$modify_date = $dba->f('modify_date'); 
				
				$row_cnt_sql5_4 = mysql_num_rows($result_sql5_4);
				
				$newProgressDetailId = runnum('id','pg_progress_detail');
				
				$newProgressDetailIdArray[$key] = $newProgressDetailId;
				$oldProgressDetailIdArray[$key] = $pg_progress_detail_id;
				
				if ($row_cnt_sql5_4 > 0) {
					if ($status != 'APP') {
						$sql5_5 = "INSERT INTO pg_progress_detail
						(id, pg_progress_id, pg_employee_empid, status, issues, advice, responded_date,
						submit_date, insert_by, insert_date, modify_by, modify_date)
						VALUES ('$newProgressDetailId', '$newProgressId', '$employeeId', 'IN1', '".$_POST['studentIssues']."', '".$_POST['advice']."',
						null, '$curdatetime', '$user_id', '$curdatetime', '$user_id', '$curdatetime')";
						
						$dba->query($sql5_5);
					}
					else {
						$sql5_5 = "INSERT INTO pg_progress_detail
						(id, pg_progress_id, pg_employee_empid, status, issues, advice, responded_date, submit_date,
						insert_by, insert_date, modify_by, modify_date)
						VALUES ('$newProgressDetailId', '$newProgressId', '$employeeId', '$status', '$issues', 
						'$advice', '$responded_date', '$submitDate', '$user_id', '$curdatetime', '$user_id', '$curdatetime')";
						
						$dba->query($sql5_5);
					}
				
					$sql5_6 = " UPDATE pg_progress_detail
					SET modify_by = '$user_id', modify_date = '$curdatetime', archived_status = 'ARC', archived_date = '$curdatetime'
					WHERE id = '$pg_progress_detail_id'
					AND pg_progress_id = '$progressId'
					AND pg_employee_empid = '$val'
					AND archived_status IS NULL";
					
					$dba->query($sql5_6);				
					
				}
				else {
					$sql5_5 = "INSERT INTO pg_progress_detail
					(id, pg_progress_id, pg_employee_empid, status, issues, advice, responded_date, submit_date,
					insert_by, insert_date, modify_by, modify_date)
					VALUES ('$newProgressDetailId', '$newProgressId', '$val', 'IN1', '".$_POST['studentIssues']."', '".$_POST['advice']."',
					null, '$curdatetime', '$user_id', '$curdatetime', '$user_id', '$curdatetime')";
					
					$dba->query($sql5_5);
				}
			}
			
			$newDiscussionIdArray = Array();
			for ($k=0; $k<$totalNoOfContent; $k++)
			{	
				$sql6_0 = "SELECT id, progress_id, pg_thesis_id, pg_proposal_id, student_matrix_no, pg_chapter_id, pg_subchapter_id, 
				content_discussion,	insert_by, insert_date, modify_by, modify_date 
				FROM pg_discussion
				WHERE progress_id = '$progressId'
				AND pg_thesis_id = '$thesisId'
				AND pg_proposal_id = '$proposalId'
				AND reference_no = '$referenceNo'
				AND student_matrix_no = '$user_id'
				AND pg_chapter_id = '$chapterId[$k]'
				AND pg_subchapter_id = '$subchapterId[$k]'
				AND archived_status is NULL";
				
				$result_sql6_0 = $dba->query($sql6_0);
				$dba->next_record();
				
				$discussionId = $dba->f('id');
				$progress_id = $dba->f('progress_id');
				$pg_thesis_id = $dba->f('pg_thesis_id'); 
				$pg_proposal_id = $dba->f('pg_proposal_id'); 
				$student_matrix_no = $dba->f('student_matrix_no'); 
				$pg_chapter_id = $dba->f('pg_chapter_id'); 
				$pg_subchapter_id = $dba->f('pg_subchapter_id'); 
				$content_discussion = $dba->f('content_discussion');
				$insert_by = $dba->f('insert_by'); 
				$insert_date = $dba->f('insert_date'); 
				$modify_by = $dba->f('modify_by');
				$modify_date = $dba->f('modify_date'); 
				
				
				$newDiscussionId = runnum('id','pg_discussion');
				$newDiscussionIdArray[$k] = $newDiscussionId;
				
				$contentDescriptionArray[$k] = $_POST["contentDescription".$k];
				
				$row_cnt_sql6_0 = mysql_num_rows($result_sql6_0);
				if ($row_cnt_sql6_0 > 0) {
					$sql6_1 = "INSERT INTO pg_discussion
					(id, progress_id, reference_no, pg_thesis_id, pg_proposal_id, student_matrix_no, pg_chapter_id, pg_subchapter_id, content_discussion, discussed_status, 
					insert_by, insert_date, modify_by, modify_date)
					VALUES ('$newDiscussionId', '$newProgressId', '$referenceNo', '$pg_thesis_id', '$pg_proposal_id', '$student_matrix_no', '$pg_chapter_id',
					'$pg_subchapter_id', '$contentDescriptionArray[$k]', null, '$user_id', '$curdatetime', '$user_id', '$curdatetime')";
					
					$dba->query($sql6_1);
					
					
					$sql6_2 = "UPDATE pg_discussion
					SET modify_by = '$user_id', modify_date = '$curdatetime', archived_status = 'ARC', archived_date = '$curdatetime'
					WHERE id = '$discussionId'
					AND progress_id = '$progress_id'
					AND pg_thesis_id = '$thesisId'
					AND pg_proposal_id = '$proposalId'
					AND reference_no = '$referenceNo'
					AND student_matrix_no = '$user_id'
					AND pg_chapter_id = '$chapterId[$k]'
					AND pg_subchapter_id = '$subchapterId[$k]'";
					
					$dba->query($sql6_2);
				}
				else {
					$sql6_1 = "INSERT INTO pg_discussion
					(id, progress_id, reference_no, pg_thesis_id, pg_proposal_id, student_matrix_no, pg_chapter_id, pg_subchapter_id, content_discussion, discussed_status, 
					insert_by, insert_date, modify_by, modify_date)
					VALUES ('$newDiscussionId', '$newProgressId', '$referenceNo', '$thesisId', '$proposalId', '$user_id', '$chapterId[$k]',
					'$subchapterId[$k]', '$contentDescriptionArray[$k]', null, '$user_id', '$curdatetime', '$user_id', '$curdatetime')";
					
					$dba->query($sql6_1);
				}

				for ($l=0; $l<count($supervisorIdArray); $l++) 
				{
					
					$sql6_3 = "SELECT a.id, a.pg_discussion_id, a.pg_employee_empid, a.content_discussion, a.discussed_status, a.responded_status,
					a.responded_date, a.insert_by, a.insert_date, a.modify_by,  a.modify_date, b.status as progress_detail_status
					FROM pg_discussion_detail a
					LEFT JOIN pg_progress_detail b ON (b.id = a.progress_detail_id)
					WHERE a.pg_discussion_id = '$discussionId'
					AND a.progress_detail_id = '$oldProgressDetailIdArray[$l]'
					AND a.pg_employee_empid = '$supervisorIdArray[$l]'
					AND a.archived_status is NULL";
					
					$result_sql6_3 = $dba->query($sql6_3);
					$dba->next_record();
					
					$discussionDetailId = $dba->f('id');
					$pg_employee_empid = $dba->f('pg_employee_empid'); 
					$content_discussion = $dba->f('content_discussion');
					$discussedStatus = $dba->f('discussed_status');
					$progressDetailStatus = $dba->f('progress_detail_status');
					
					$newDiscussionDetailId = runnum('id','pg_discussion_detail');
					$row_cnt_sql6_3 = mysql_num_rows($result_sql6_3);
					
					if ($row_cnt_sql6_3 > 0) {
						if ($progressDetailStatus == 'REQ' || $progressDetailStatus == 'IN1') {
							$sql6_4 = "INSERT INTO pg_discussion_detail
							(id, pg_discussion_id, progress_detail_id, pg_employee_empid, content_discussion, discussed_status, insert_by,
							insert_date, modify_by,  modify_date)
							VALUES ('$newDiscussionDetailId', '$newDiscussionId', '$newProgressDetailIdArray[$l]', '$pg_employee_empid', 
							'$contentDescriptionArray[$k]', null, '$user_id', '$curdatetime', '$user_id', '$curdatetime')";
							
							$dba->query($sql6_4);														
						}
						else { //APP, SV1
							$sql6_4 = "INSERT INTO pg_discussion_detail
							(id, pg_discussion_id, progress_detail_id, pg_employee_empid, content_discussion, discussed_status, insert_by, insert_date, modify_by,  modify_date)
							VALUES ('$newDiscussionDetailId', '$newDiscussionId', '$newProgressDetailIdArray[$l]', '$pg_employee_empid', 
							'$contentDescriptionArray[$k]', '$discussedStatus', '$user_id', '$curdatetime', '$user_id', '$curdatetime')";
							
							$dba->query($sql6_4);
							
							
						}
						$sql6_5 = "UPDATE pg_discussion_detail 
						SET modify_by = '$user_id', modify_date = '$curdatetime', 
						archived_status = 'ARC', archived_date = '$curdatetime'
						WHERE id = '$discussionDetailId'
						AND progress_detail_id = '$oldProgressDetailIdArray[$l]'
						AND pg_discussion_id = '$discussionId'
						AND pg_employee_empid = '$supervisorIdArray[$l]'";
						
						$dba->query($sql6_5);
						
					}
					else {

						$sql6_4 = "INSERT INTO pg_discussion_detail
						(id, pg_discussion_id, progress_detail_id, pg_employee_empid, content_discussion, discussed_status, insert_by, 
						insert_date, modify_by,  modify_date)
						VALUES ('$newDiscussionDetailId', '$newDiscussionId', '$newProgressDetailIdArray[$l]', '$supervisorIdArray[$l]', 
						'$contentDescriptionArray[$k]', null, '$user_id', '$curdatetime', '$user_id', '$curdatetime')";
						
						$dba->query($sql6_4);

					}
				}
			}
			
			$sql7 = "SELECT fu_cd, fu_document_filename, fu_document_filedesc, fu_document_filetype, fu_document_filedata, fu_document_thumbnail, 
			progress_id, pg_employee_empid, student_matrix_no, pg_proposal_id, insert_by, IFNULL(insert_date,'0000-00-00 00:00:00') as insert_date, 
			modify_by, IFNULL(modify_date,'0000-00-00 00:00:00') as modify_date, attachment_level, attachment_type, status, upload_status
			FROM file_upload_progress
			WHERE progress_id = '$progressId'
			/*AND student_matrix_no = '$user_id'*/
			AND upload_status = 'CFM'
			AND status = 'A'
			AND archived_status IS NULL";
			
			$result7 = $dba->query($sql7);
			$dba->next_record();
		
			if (mysql_num_rows($result7) > 0){
				do {
					$fuCd = $dba->f('fu_cd');
					$fuDocumentFilename = $dba->f('fu_document_filename'); 
					$fuDdocumentFiledesc = $dba->f('fu_document_filedesc'); 
					$fuDdocumentFiletype = $dba->f('fu_document_filetype'); 
					$fuDocumentFiledata = addslashes($dba->f('fu_document_filedata')); 
					$fuDocumentThumbnail = $dba->f('fu_document_thumbnail');
					$progressId = $dba->f('progress_id'); 
					$employeeId = $dba->f('pg_employee_empid'); 
					$studentMatrixNo = $dba->f('student_matrix_no'); 
					$proposalId = $dba->f('pg_proposal_id'); 
					$insertBy = $dba->f('insert_by'); 
					$insertDate = $dba->f('insert_date'); 
					$modifyBy = $dba->f('modify_by'); 
					$modifyDate = $dba->f('modify_date'); 
					$attachmentLevel = $dba->f('attachment_level'); 
					$attachmentType = $dba->f('attachment_type'); 
					$status = $dba->f('status');
					$uploadStatus = $dba->f('upload_status');

					$newFcCd = runnum('fu_cd','file_upload_progress');
					$sql7_1 = "INSERT INTO file_upload_progress
					(fu_cd, fu_document_filename, fu_document_filedesc, fu_document_filetype, fu_document_filedata, fu_document_thumbnail, 
					progress_id, student_matrix_no, pg_employee_empid, pg_proposal_id, insert_by, insert_date, modify_by, modify_date, 
					attachment_level, attachment_type, status, upload_status)
					VALUES ('$newFcCd', '$fuDocumentFilename','$fuDdocumentFiledesc', '$fuDdocumentFiletype', '$fuDocumentFiledata', '$fuDocumentThumbnail', 
					'$newProgressId', '$studentMatrixNo', '$employeeId','$proposalId', '$insertBy', '$insertDate', '$modifyBy', 
					'$modifyDate', '$attachmentLevel',	'$attachmentType', '$status', '$uploadStatus')";
					
					$result7_1 = $db->query($sql7_1);
					
					
				} while ($dba->next_record());
			
			$sql7_2 = "UPDATE file_upload_progress 
			SET modify_by = '$user_id', modify_date = '$curdatetime', 
			archived_status = 'ARC', archived_date = '$curdatetime'
			WHERE progress_id = '$progressId'
			/*AND student_matrix_no = '$user_id'*/
			AND upload_status = 'CFM'
			AND status = 'A'
			AND archived_status IS NULL";
			
			$result7_2 = $dbg->query($sql7_2);
			}
			
			$sql5 = "UPDATE file_upload_progress
			SET progress_id = '$newProgressId', upload_status = 'CFM', modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE progress_id IS NULL
			AND student_matrix_no = '$user_id'
			AND pg_proposal_id = '$proposalId'
			AND attachment_level = 'S'
			AND attachment_type IN ('I','A')
			AND upload_status = 'TMP'
			AND status = 'A'
			AND archived_status IS NULL";
			
			$dba->query($sql5);			
			
			while (list ($key,$val) = @each ($content_checkbox)) 
			{
				$sql2_1 = "UPDATE pg_discussion 
				SET discussed_status = 'Y'
				WHERE progress_id = '$newProgressId'
				AND pg_thesis_id = '$thesisId'
				AND pg_proposal_id = '$proposalId'
				AND reference_no = '$referenceNo'
				AND pg_chapter_id = '$chapterId[$val]'
				AND pg_subchapter_id = '$subchapterId[$val]'
				AND student_matrix_no = '$user_id'
				AND archived_status IS NULL";

				$result2_1 = $dbb->query($sql2_1); 
				$dbb->next_record();
				
				
				for ($l=0; $l<count($supervisorIdArray); $l++) 
				{
					$sql2_0 = " SELECT id
					FROM pg_progress_detail 
					WHERE id = '$newProgressDetailIdArray[$l]'
					AND status NOT IN ('APP')";
					
					$result2_0 = $db->query($sql2_0); 
					$db->next_record();
					$row_cnt2_0 = mysql_num_rows($result2_0);
					if ($row_cnt2_0 > 0) {
						$sql2_2 = "UPDATE pg_discussion_detail 
						SET discussed_status = 'Y'
						WHERE pg_discussion_id = (
							SELECT id
							FROM pg_discussion
							WHERE progress_id = '$newProgressId'
							AND pg_thesis_id = '$thesisId'
							AND pg_proposal_id = '$proposalId'
							AND reference_no = '$referenceNo'
							AND pg_chapter_id = '$chapterId[$val]'
							AND pg_subchapter_id = '$subchapterId[$val]'
							AND student_matrix_no = '$user_id'
							AND archived_status IS NULL)
						AND progress_detail_id = '$newProgressDetailIdArray[$l]'
						AND pg_employee_empid = '$supervisorIdArray[$l]'
						AND archived_status IS NULL";
						$result2_2 = $dbb->query($sql2_2);
						$dbb->next_record();

					}
				}
				
								
			}
			
			$sql6_0 = "SELECT id, pg_progress_id, pg_thesis_id, pg_proposal_id, student_matrix_no, 
			IFNULL(meeting_date,'0000-00-00 00:00:00') as meeting_date, IFNULL(meeting_stime,'00:00:00') as meeting_stime,
			IFNULL(meeting_etime,'00:00:00') as meeting_etime, add_status, insert_by, insert_date, modify_by, modify_date,
			archived_status, archived_date
			FROM pg_progress_meeting
			WHERE pg_progress_id = '$progressId'
			AND pg_thesis_id = '$thesisId'
			AND pg_proposal_id = '$proposalId'
			AND student_matrix_no = '$user_id'
			AND add_status = 'CFM'
			AND archived_status IS NULL";
			
			$result6_0 = $dba->query($sql6_0);
			$dba->next_record();
			
			$row_cnt = mysql_num_rows($result6_0);
			if ($row_cnt >0){
				do {
					//$id = $dba->f('id'); 			
					$pg_progress_id = $dba->f('pg_progress_id');
					$pg_thesis_id = $dba->f('pg_thesis_id');
					$pg_proposal_id = $dba->f('pg_proposal_id');
					$student_matrix_no = $dba->f('student_matrix_no');
					$meeting_date = $dba->f('meeting_date');
					$meeting_stime = $dba->f('meeting_stime');
					$meeting_etime = $dba->f('meeting_etime');
					$add_status = $dba->f('add_status');
					$insert_by = $dba->f('insert_by');
					$insert_date = $dba->f('insert_date');
					$modify_by = $dba->f('modify_by');
					$modify_date = $dba->f('modify_date');

					$newProgressMeetingId = runnum('id','pg_progress_meeting');
					
					$sql6_1 = "INSERT INTO pg_progress_meeting
					(id, pg_progress_id, pg_thesis_id, pg_proposal_id, student_matrix_no, meeting_date, meeting_stime, meeting_etime,
					add_status, insert_by, insert_date, modify_by, modify_date)
					VALUES ('$newProgressMeetingId', '$newProgressId', '$pg_thesis_id', '$pg_proposal_id', '$student_matrix_no', 
					'$meeting_date', '$meeting_stime', '$meeting_etime', '$add_status', '$insert_by', '$insert_date', '$modify_by', '$modify_date')";
					
					$db->query($sql6_1);
				} while ($dba->next_record());
			}
			
			$sql6_2 = "UPDATE pg_progress_meeting 
			SET modify_by = '$user_id', modify_date = '$curdatetime', 
			archived_status = 'ARC', archived_date = '$curdatetime'
			WHERE pg_progress_id = '$progressId'
			AND pg_thesis_id = '$thesisId'
			AND pg_proposal_id = '$proposalId'
			AND student_matrix_no = '$user_id'
			AND add_status = 'CMP'
			AND archived_status IS NULL";
			
			$result6_2 = $dbg->query($sql6_2);
			
			$sql6 = "UPDATE pg_progress_meeting
			SET pg_progress_id = '$newProgressId', add_status = 'CFM', modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE pg_progress_id IS NULL
			AND pg_thesis_id = '$thesisId'
			AND pg_proposal_id = '$proposalId'
			AND student_matrix_no = '$user_id'
			AND add_status = 'TMP'
			AND archived_status IS NULL";
			
			$dba->query($sql6);
			
			$msg[] = "<div class=\"success\"><span>Your Monthly Progress Report has been submitted successfully.</span></div>";
		}
	}
}

$sql2 = "SELECT a.id, a.reference_no, a.report_month, a.report_year, DATE_FORMAT(a.submit_date, '%d-%b-%Y') as submit_date, a.student_matrix_no, 
a.pg_thesis_id, a.pg_proposal_id, 
DATE_FORMAT(a.meeting_stime,'%h:%i') as meeting_stime, DATE_FORMAT(a.meeting_stime,'%p') as stime_pm, 
DATE_FORMAT(a.meeting_etime,'%h:%i') as meeting_etime, DATE_FORMAT(a.meeting_etime,'%p') as etime_pm,		
DATE_FORMAT(a.meeting_date,'%d-%b-%Y') as meeting_date, a.status as progress_status, 
a.insert_by, a.insert_date, a.modify_by, a.modify_date,	a.issues as student_issues, a.advice, c1.description as progress_desc,
d.status as progress_detail_status, c2.description as progress_detail_desc, d.id as progress_detail_id, a.respond_status
FROM pg_progress a
LEFT JOIN ref_proposal_status c1 ON (c1.id = a.status)
LEFT JOIN pg_progress_detail d ON (d.pg_progress_id = a.id)
LEFT JOIN ref_proposal_status c2 ON (c2.id = d.status)
WHERE a.student_matrix_no = '$user_id'
AND a.reference_no = '$referenceNo'
AND a.pg_thesis_id = '$thesisId'
AND a.pg_proposal_id = '$proposalId'
AND a.archived_status is null
AND d.archived_status is null";
		
$result2 = $dba->query($sql2); 
$dba->next_record();
$id=$dba->f('id');
$reportMonth=$dba->f('report_month');
$reportYear=$dba->f('report_year');
$meetingDate=$dba->f('meeting_date');
$startTime=$dba->f('meeting_stime');
$endTime=$dba->f('meeting_etime');
$sTimePM=$dba->f('stime_pm');
$eTimePM=$dba->f('etime_pm');
$progressStatus=$dba->f('progress_status');
$progressDesc=$dba->f('progress_desc');
$progressDetailId=$dba->f('progress_detail_id');
$progressDetailStatus=$dba->f('progress_detail_status');
$progressDetailDesc=$dba->f('progress_detail_desc');
$submitDate=$dba->f('submit_date');
$studentIssues=$dba->f('student_issues');
$supervisorIssues=$dba->f('supervisor_issues');
$advice=$dba->f('advice');
$referenceNo=$dba->f('reference_no');
$respondStatus=$dba->f('respond_status');
$row_cnt2 = mysql_num_rows($result2);

$sql2_1 = "SELECT a.id as chapter_id, a.chapter_no, a.description as chapter_desc, 
b.id as subchapter_id, b.subchapter_no, b.description as subchapter_desc,
a.discussed_status as chapter_discussed_status, 
b.discussed_status as subchapter_discussed_status
FROM pg_chapter a
LEFT JOIN pg_subchapter b ON (b.chapter_id = a.id)  
WHERE a.status = 'A'
AND a.student_matrix_no = '$user_id'
AND (b.status = 'A' OR b.status IS NULL)
ORDER BY a.chapter_no, b.subchapter_no";

$result2_1 = $dbb->query($sql2_1); 
$dbb->next_record();
$row_cnt2_1 = mysql_num_rows($result2_1);

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

$sql_supervisor1 = " SELECT DATE_FORMAT(a.acceptance_date,'%d-%b-%Y') as acceptance_date
FROM pg_supervisor a 
LEFT JOIN ref_supervisor_type d ON (d.id = a.ref_supervisor_type_id) 
LEFT JOIN pg_thesis f ON (f.student_matrix_no = a.pg_student_matrix_no)
LEFT JOIN pg_proposal g ON (g.pg_thesis_id = f.id)
WHERE a.pg_student_matrix_no='$user_id'
AND g.pg_thesis_id = '$thesisId'
AND g.id = '$proposalId'
AND a.acceptance_status = 'ACC'
AND a.ref_supervisor_type_id in ('SV','CS','XS')
AND g.verified_status in ('APP','AWC')
AND g.status in ('APP','APC')
AND g.archived_status IS NULL
AND a.status = 'A'
ORDER BY a.acceptance_date
LIMIT 1";

$result_sql_supervisor1 = $db->query($sql_supervisor1); //echo $sql;
$db->next_record();
$confirmAcceptanceDate = $db->f('acceptance_date');		

$sql1 = "SELECT const_value
FROM base_constant
WHERE const_term = 'FIRST_MONTHLY_REPORT'";
$db->query($sql1);
$db->next_record();
$firstMonthlyReportParam = $db->f('const_value');	

$firstMonthlyReportParam = $firstMonthlyReportParam + 1;

if ($firstMonthlyReportParam == 1) {
	$expectedReport = $firstMonthlyReportParam.' month';
}
else {
	$expectedReport = $firstMonthlyReportParam.' months';
}

$firstMonthlyReport = date('M-Y', strtotime($confirmAcceptanceDate. ' '.($firstMonthlyReportParam).' month'));				
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
	<script type="text/javascript">
	function newReport() 
	{
		var ask = window.confirm("Are you sure to submit another Monthly Progress Report? \nClick OK to proceed or CANCEL to stay on the same page.");
		if (ask) 
		{
			//document.location.href = "../monthlyreport/new_progress.php?pid=" + pid + "&tid=" + tid + "&id=" + id;
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
	function report_attachment(pid, al, at) {
		var ask = window.confirm("Ensure your report has been saved before proceed or otherwise the last change will be discarded.\nClick OK to proceed or CANCEL to stay on the same page.");
		if (ask) {
			document.location.href = "javascript:open_win('progress_monthly_upload.php?pid="+pid+"&al="+al+"&at="+at+",480,280,0,0,0,1,0,1,1,0,5,'winupload')";

		}
	}
	</script>

	<script type="text/javascript">
	function newDicussion(pid, tid, pgid, ref) 
	{
		var ask = window.confirm("Ensure your report has been saved before proceed or otherwise the last change will be discarded.\nClick OK to proceed or CANCEL to stay on the same page.");
		if (ask) 
		{
			document.location.href = "../monthlyreport/progress_discussion_detail.php?pid=" + pid + "&tid=" + tid + "&pgid=" + pgid + "&ref=" + ref;
			return true;
		}
		return false;
	}
	</script>
	<script>
	function issueAttachment(tid, pid, prgid, at, ref) {
		var ask = window.confirm("Ensure your report has been saved before proceed or otherwise the last change will be discarded.\nClick OK to proceed or CANCEL to stay on the same page.");
		if (ask) {
			document.location.href = "../monthlyreport/submit_progress_attachment.php?tid=" + tid + "&pid=" + pid + "&prgid=" + prgid + "&at=" + at + "&ref=" + ref;

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
	<?
	if ($progressStatus == "" || $progressStatus == "SAV" || $progressStatus == "REQ") {?>
		<strong>Important Notes:</strong><br />
		1) Student must bring this form to each meeting with the supervisor /Co-supervisor. <br /> 
		2) An original copy of the completed form must be returned to the MSU Colombo in end of every month.<br />
		3) Students are not allowed to hand over the final completed Thesis if he /she does not submit the completed and signed form every month. <br />
		4) Students should meet supervisor/co-supervisor through the face-to-face, Skype or email at least once a month.<br/>
		<br/>
		<table border="0">
			<tr>
			<td><h3><strong>Report Details </strong></h3></td>
			</tr>
		</table>
		<?
		$curdatetime = date("Y-m-d H:i:s");
		$time=strtotime($curdatetime);
		$month=date("F",$time);
		$year=date("Y",$time);?>
		<table>
			<tr>
				<td>Report Status</td>
				<td>:</td>
				<?if ($progressDesc=="") $progressDesc='New';?>
				<td><strong><?=$progressDesc?></strong></td>
			</tr>
			<tr>
				<td>Reference No</td>
				<td>:</td>
				<td><strong><?=$referenceNo?></strong></td>
			</tr>
			<tr>
				<td>Thesis / Project ID</td>
				<td>:</td>
				<td><label><?=$thesisId;?></label></td>
				<input type="hidden" name="id" id="id" value="<?=$id; ?>">
				<input type="hidden" name="thesisId" id="thesisId" value="<?=$thesisId; ?>">
				<input type="hidden" name="proposalId" id="proposalId" value="<?=$proposalId; ?>">
				<input type="hidden" name="progressDetailId" id="progressDetailId" value="<?=$progressDetailId; ?>">
				<input type="hidden" name="progressStatus" id="progressStatus" value="<?=$progressStatus; ?>">
				<input type="hidden" name="referenceNo" id="referenceNo" value="<?=$referenceNo; ?>">
				<input type="hidden" name="firstMonthlyReportParam" id="firstMonthlyReportParam" value="<?=$firstMonthlyReportParam; ?>">
				<input type="hidden" name="firstMonthlyReport" id="firstMonthlyReport" value="<?=$firstMonthlyReport; ?>">
				<input type="hidden" name="expectedReport" id="expectedReport" value="<?=$expectedReport; ?>">
			</tr>
			<tr>
				<td>Student Matrix No</td>
				<td>:</td>
				<td><?=$user_id?></td>
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
				<td><?=$sname?></td>
			</tr>    
			<tr>
			  <td>Month <span style="color:#FF0000">*</span></td>
			  <td>:</td>
			  <td><select name="reportMonth">
			  
					<?if ($reportMonth == "") $reportMonth = $_POST['reportMonth'];?>
					<?if ($reportMonth=="") {?><option value="" selected="selected"></option><?} else {?><option value=""></option><?}?>
					<?if ($reportMonth=="January") {?><option value="January" selected="selected">January</option><?} else {?><option value="January">January</option><?}?>
					<?if ($reportMonth=="February") {?><option value="February" selected="selected">February</option><?} else {?><option value="February">February</option><?}?>
					<?if ($reportMonth=="March") {?><option value="March" selected="selected">March</option><?} else {?><option value="March">March</option><?}?>
					<?if ($reportMonth=="April") {?><option value="April" selected="selected">April</option><?} else {?><option value="April">April</option><?}?>
					<?if ($reportMonth=="May") {?><option value="May" selected="selected">May</option><?} else {?><option value="May">May</option><?}?>
					<?if ($reportMonth=="June") {?><option value="June" selected="selected">June</option><?} else {?><option value="June">June</option><?}?>
					<?if ($reportMonth=="July") {?><option value="July" selected="selected">July</option><?} else {?><option value="July">July</option><?}?>
					<?if ($reportMonth=="August") {?><option value="August" selected="selected">August</option><?} else {?><option value="August">August</option><?}?>
					<?if ($reportMonth=="September") {?><option value="September" selected="selected">September</option><?} else {?><option value="September">September</option><?}?>
					<?if ($reportMonth=="October") {?><option value="October" selected="selected">October</option><?} else {?><option value="October">October</option><?}?>
					<?if ($reportMonth=="November") {?><option value="November" selected="selected">November</option><?} else {?><option value="November">November</option><?}?>
					<?if ($reportMonth=="December") {?><option value="December" selected="selected">December</option><?} else {?><option value="December">December</option><?}?>
					
					</select>
				<select name="reportYear">
					<?if ($reportYear == "") $reportYear = $_POST['reportYear'];?>
					<?if ($reportYear=="") {?><option value="" selected="selected"></option><?} else {?><option value=""></option><?}?>
					<?if ($reportYear=="2014") {?><option value="2014" selected="selected">2014</option><?} else {?><option value="2014">2014</option><?}?>
					<?if ($reportYear=="2015") {?><option value="2015" selected="selected">2015</option><?} else {?><option value="2015">2015</option><?}?>
					<?if ($reportYear=="2016") {?><option value="2016" selected="selected">2016</option><?} else {?><option value="2016">2016</option><?}?>
					<?if ($reportYear=="2017") {?><option value="2017" selected="selected">2017</option><?} else {?><option value="2017">2017</option><?}?>
					<?if ($reportYear=="2018") {?><option value="2018" selected="selected">2018</option><?} else {?><option value="2018">2018</option><?}?>
					<?if ($reportYear=="2019") {?><option value="2019" selected="selected">2019</option><?} else {?><option value="2019">2019</option><?}?>
					<?if ($reportYear=="2020") {?><option value="2020" selected="selected">2020</option><?} else {?><option value="2020">2020</option><?}?>
				</select></td>
			</tr>
		</table>	
		<table>
			<tr>
				<td><span style="color:#FF0000">Notes:</span><br/>
				1. Your Monthly Progress Report should be <strong><?=$expectedReport?></strong>  after the earliest Supervisor's acceptance date <strong><?=$confirmAcceptanceDate?></strong>.</br>
				2. Your first expected Monthly Progress Report is <strong><?=$firstMonthlyReport?></strong>.</td>
			</tr>
		</table>		<?
		$sqlMeeting="SELECT id, DATE_FORMAT(meeting_date,'%d-%b-%Y') as meeting_date, DATE_FORMAT(meeting_stime,'%H:%i') as meeting_stime, 
		DATE_FORMAT(meeting_etime,'%H:%i') as meeting_etime
		FROM  pg_progress_meeting  
		WHERE (pg_progress_id = '$id' OR pg_progress_id = '' OR pg_progress_id IS NULL)
		AND pg_proposal_id='$proposalId'
		AND pg_thesis_id = '$thesisId'
		AND student_matrix_no = '$user_id' 
		ORDER BY meeting_date DESC ";			

		$result = $db->query($sqlMeeting); 
		$row_cnt = mysql_num_rows($result);
		if ($row_cnt == '0')
		{
			$row_cnt_tmp = '';
		}
		else
		{
			$row_cnt_tmp = "(".$row_cnt.")";
		}	
		
		?>
		<table>
			<tr>
				<td><button type = "button" name="btnDiscussionDetail" onclick="return newDicussion('<?=$proposalId?>','<?=$thesisId?>','<?=$id?>','<?=$referenceNo?>')">
				Add Discussion Date <FONT COLOR="#FF0000"><sup><?=$row_cnt_tmp?></sup></FONT></button></td>
				<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='submit_progress_new.php';" /></td>		
			</tr>
		</table>
		<br/>
		<table>
			<tr>
				<td><h3><strong>List of Supervisor/Co-Supervisor</strong></h3></td>
			</tr>
		</table>
		<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="100%" class="thetable">			
				<tr>
					<th>No</th>					
					<th>Role</th>
					<th>Staff ID</th>
					<th>Name</th>
					<th>Faculty</th>
					<th>View Feedback</th>
					<th>Status</th>
					<th>Last Update</th>
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
							
							<?
							$sql12 = "SELECT b.status as progress_detail_status, c.description as progress_detail_desc,
							DATE_FORMAT(b.responded_date,'%d-%b-%Y %h:%i %p') AS responded_date
							FROM pg_progress a
							LEFT JOIN pg_progress_detail b ON (b.pg_progress_id = a.id)
							LEFT JOIN ref_proposal_status c ON (c.id = b.status)
							WHERE a.student_matrix_no = '$user_id'
							AND b.pg_employee_empid = '$employeeId'
							AND a.reference_no = '$referenceNo'
							AND a.pg_thesis_id = '$thesisId'
							AND a.pg_proposal_id = '$proposalId'
							/*AND a.status NOT IN ('SAV')*/
							AND a.archived_status is null
							AND b.archived_status is NULL";
							
							$result12 = $dbg->query($sql12); 
							$dbg->next_record();
							$row_cnt12 = mysql_num_rows($result12);
							$progressDetailStatus=$dbg->f('progress_detail_status');
							$progressDetailDesc=$dbg->f('progress_detail_desc');
							$respondedDate=$dbg->f('responded_date');
							
							if ($row_cnt12>0) {
							?>
							
							<td><a href="progress_view_feedback.php?tid=<?=$thesisId;?>&pid=<?=$proposalId;?>&eid=<?=$employeeId;?>&id=<?=$id;?>&mn=<?=$user_id?>&ref=<?=$referenceNo?>" name="thesisId" value="<?=$thesisId?>" title="View feedback"><img src="../images/view.jpg" width="45" height="30" style="border:0px;" title="View feedback"></a></td>	
							<?}
							else {
								?>
								<td></td>
								<?
							}
							if ($progressDetailStatus == '') {
							?>
								<td align="left"><label>Expecting Monthly Progress Report</label></td>
							<?}
							else if ($progressDetailStatus == 'SV1') {
							?>
								<td align="left"><label><span style="color:#FF0000"><?=$progressDetailDesc;?></span></label></td>
							<?}
							else {
								?>								
								<td align="left"><label><?=$progressDetailDesc;?></label></td>
								<?
							}?>
							<td align="left"><label><?=$respondedDate;?></label></td>
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
									<span style="color:#FF0000"> Make sure your Supervisor has been assigned first before submit the Monthly Progress report</span></td>
						</tr>
					</table>
					<?
				}?>	
				</table>
				
		</fieldset>
					
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

		?>
		<br/>
		<table>
			<tr>
				<td><h3><strong>Content of Discussion</strong> <span style="color:#FF0000"> *</span></h3></td>
			</tr>
		</table>
		<table>
			<tr>
				<td><label>To be filled in by Student <em>(Please tick).</em></label></td>
			</tr>
		</table>
		<table>
			<?
			for ($i=0; $i<$no; $i++){
			?>
				<input type="hidden" name="chapterId[]" id="chapterId" value="<?=$chapterId[$i];?>"></input>
				<input type="hidden" name="subchapterId[]" id="subchapterId" value="<?=$subchapterId[$i];?>"></input>
				<?
					$sql_discussion = " SELECT discussed_status
					FROM pg_discussion 
					WHERE pg_thesis_id = '$thesisId'
					AND pg_proposal_id = '$proposalId'
					AND reference_no = '$referenceNo'
					AND student_matrix_no = '$user_id'
					AND pg_chapter_id = '$chapterId[$i]'
					AND pg_subchapter_id = '$subchapterId[$i]'
					AND archived_status is null";
										
					$result_sql_discussion = $dbb->query($sql_discussion); 
					$dbb->next_record();
					$discussedStatus = $dbb->f('discussed_status');
				?>
				<tr>
					<?
					if ($discussedStatus == 'Y') {									
					?>
						<td align="center"><input name="content_checkbox[]" type="checkbox" id="content_checkbox" value="<?=$i;?>" checked="checked"/></input></td>
					<?									
					}
					else {									
						?>
						<td align="center"><input name="content_checkbox[]" type="checkbox" id="content_checkbox" value="<?=$i;?>" /></input></td>
						<?									
					}?>

					<td><label>Chapter <?=romanNumerals($chapterNo[$i]);?>. <?=$chapterDesc[$i];?></label>			
					<input type="hidden" name="chapterNo[]" id="chapterNo" value="<?=$chapterNo[$i];?>"></input>

					<?if ($subchapterNo[$i] != "") {?>
							<label>, Subchapter <?=romanNumerals($subchapterNo[$i]);?>. <?=$subchapterDesc[$i];?></label>									
						
					<?}?>
					</td>
				</tr>
				
				<input type="hidden" name="subchapterNo[]" id="subchapterNo" value="<?=$subchapterNo[$i];?>"></input>

			<?
			}
			?>	
			<input type="hidden" name="totalNoOfContent" id="totalNoOfContent" value="<?=$i; ?>">
			
		</table>
		</fieldset>
		<br/>
		<table>
			<tr>
				<td><h3><strong>Description of topic or Issues facing by Student</strong><span style="color:#FF0000"> *</span></h3></td>
			</tr>
		</table>
		<table>
			<?
			if ($studentIssues == "") $studentIssues = $_POST["studentIssues"];?>
			<tr>
				<td><textarea name="studentIssues" class="ckeditor" ><?=$studentIssues; ?></textarea></td>
			</tr>
			<tr>
				<td><strong>Please attach additional file if necessary:</strong></td>
			</tr>
				
		</table>
		<?
		if ($id != "")	{
			$tmpProgressId = " AND progress_id = '$progressId'";
		}
		else {
			$tmpProgressId = " AND (progress_id = '' OR progress_id IS NULL)";
		}
		if ($referenceNo != "")	{
			$tmpReferenceNo = " AND reference_no = '$referenceNo'";
		}
		else {
			$tmpReferenceNo = " AND (reference_no = '' OR reference_no IS NULL)";
		}

		$sqlMeeting="SELECT fu_cd
		FROM file_upload_progress 
		WHERE pg_proposal_id='$proposalId'
		AND student_matrix_no = '$user_id'
		AND attachment_level = 'S' 
		AND attachment_type = 'I' "
		.$tmpProgressId
		.$tmpReferenceNo.
		" AND upload_status IN ('TMP','CFM')
		AND status = 'A'
		AND archived_status is NULL";				

		$result = $db->query($sqlMeeting); 
		$row_cnt = mysql_num_rows($result);
		if ($row_cnt == '0')
		{
			$row_cnt_tmp = '';
		}
		else
		{
			$row_cnt_tmp = "(".$row_cnt.")";
		}	
		
		?>
		<table>
			<tr>
				<td><button type="button" name="btnAttachment" value="Attachment" onclick="return issueAttachment('<?=$thesisId?>', '<?=$proposalId?>','<?=$id?>','I','<?=$referenceNo?>')" >
				Attachment <FONT COLOR="#FF0000"><sup><?=$row_cnt_tmp?></sup></FONT></button></td>
			
			</tr>
		</table>  
		</fieldset>
		<br/>
		<table>
			<tr>
				<td><h3><strong>Advice from Supervisor & List of Action to be taken by student</strong><span style="color:#FF0000"> *</span></h3></td>
			</tr>
		</table>
		<table>
			<?
			if ($advice == "") $advice = $_POST["advice"];?>
			<tr>
				<td><textarea name="advice" class="ckeditor" ><?=$advice; ?></textarea></td>
			</tr>
			<tr>
				<td><strong>Please attach additional file if necessary:</strong></td>
			</tr>						
		</table>
		<?
		$sqlMeeting="SELECT fu_cd
		FROM file_upload_progress 
		WHERE (progress_id IS NULL OR progress_id = '$id')
		AND pg_proposal_id='$proposalId'
		AND student_matrix_no = '$user_id'
		AND attachment_level = 'S' 
		AND attachment_type = 'A' "
		.$tmpProgressId
		.$tmpReferenceNo.
		" AND upload_status IN ('TMP','CFM')
		AND status = 'A'
		AND archived_status is NULL";				

		$result = $db->query($sqlMeeting); 
		$row_cnt = mysql_num_rows($result);
		if ($row_cnt == '0')
		{
			$row_cnt_tmp = '';
		}
		else
		{
			$row_cnt_tmp = "(".$row_cnt.")";
		}	
		
		?>
		<table>
			<tr>
				<td><button type="button" name="btnAttachment" value="Attachment" onclick="return issueAttachment('<?=$thesisId?>', '<?=$proposalId?>','<?=$id?>','A','<?=$referenceNo?>')" >
				Attachment <FONT COLOR="#FF0000"><sup><?=$row_cnt_tmp?></sup></FONT></button></td>
			
			</tr>
		</table>  
		</fieldset>
		<table>
			<tr>
				<td><label><span style="color:#FF0000">Notes:</span></td>
			</tr>
			<tr>
				<td></span>1. Field marks with (<span style="color:#FF0000">*</span>) is compulsory.</td>
			</tr>
			<tr>
				<td>2. If you proceed to save/resubmit your monthly progress report, the report which are still pending at your Supervisor / Co-Supervisor (if any) will be replaced.</td>
			</tr>
		</table>
		<table>
			<tr>
				<td><input type="submit" name="btnSave" value="Save"/></td>
				<td><input type="submit" name="btnSubmit" value="Submit" onClick="return respConfirm()" /></td>
				<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='submit_progress_new.php';" /></td>		
			</tr>
		</table>
		</form>

		<script>
			<?=$jscript;?>
		</script>
		
	<?} 
	else //$progressStatus == 'IN1'
	{?>
		<fieldset>
		<legend><strong>Monthly Progress Report Status</strong></legend>
			<table>
				<tr>
					<td>You have submitted your Monthly Progress Report for month of <strong><?=$reportMonth?> <?=$reportYear?></strong> to your Supervisor(s) on <strong><?=$submitDate?>.</strong></td>
				</tr>
			</table>

			<table>
				<tr>
					<td><br/>Click here <a href="../monthlyreport/view_progress.php?pid=<?=$proposalId;?>&tid=<?=$thesisId;?>&id=<?=$id;?>"><img src="../images/view.jpg" width="45" height="30" style="border:0px;" title="View Submitted Monthly Progress Report"></a> to view your previously submitted report. </td>
				</tr>									
			</table>		
		</fieldset>
		<table>
			<tr>		
				<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='submit_progress_new.php';" /></td>		
			</tr>
		</table>
	<?}?>
	<script>
		<?=$jscript;?>	
	</script>
	</body>
</html>




