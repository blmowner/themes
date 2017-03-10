<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: review_progress_detail.php
//
// Created by: Zuraimi
// Created Date: 19-Mar-2015
// Modified by: Zuraimi
// Modified Date: 19-Mar-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

session_start();
$user_id=$_SESSION['user_id'];
$progressId=$_GET['id'];
$studentMatrixNo=$_GET['mn'];
$thesisId=$_GET['tid'];
$proposalId=$_GET['pid'];
$referenceNo=$_GET['ref'];

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
function runnum3($column_name, $tblname) 
{ 
    global $dbf;
    
    $run_start = "001";
    
    $sql_slct_max = "SELECT MAX($column_name) AS run_id FROM $tblname";
    $sql_slct = $dbf;
    $sql_slct->query($sql_slct_max);
    $sql_slct->next_record();

    if($sql_slct->num_rows($sql_slct_max)== 0 || $sql_slct->f("run_id")==NULL) 
	{
        $run_id = date("Ymd").$run_start;
    } 
	else 
	{
        $todate = date("Ymd");
        
        if($todate > substr($sql_slct->f("run_id"),1,8)) 
		{
            $run_id = $todate.$run_start;
			//echo $sql_slct->f("run_id")."<br>";
			//echo $lol = substr($sql_slct->f("run_id"),1,8);
        } 
		else 
		{
            //$run_id = $sql_slct->f("run_id") + 1; 
			//echo "2<br>";
			//echo $sql_slct->f("run_id")."<br>";
			//echo substr($sql_slct->f("run_id"),1,11)."<br>";
			$run_id = substr($sql_slct->f("run_id"),1,11) + 1;
			//echo "<br>".$sql_slct->f("run_id") + 1;
        }
    }
    return $run_id;
}

function runnum4($column_name, $tblname) 
{ 
    global $db;
    
    $run_start = "0001";
    
    $sql_slct_max = "SELECT MAX(SUBSTR($column_name,1,8)) AS run_id FROM $tblname";
    $sql_slct = $db;
    $sql_slct->query($sql_slct_max);
    $sql_slct->next_record();

    if($sql_slct->num_rows($sql_slct_max)== 0 || $sql_slct->f("run_id")==NULL) 
	{
        $run_id = date("Ymd").$run_start;
		//echo "first if <br>";
    } 
	else 
	{
        $todate = date("Ymd");
        
        if($todate > $sql_slct->f("run_id")) 
		{
            $run_id = $todate.$run_start;
			//echo "1<br>";
        } 
		else 
		{
            $run_id = $sql_slct->f("run_id") + 1; 
			//echo "2<br>";
        }
    }
    return $run_id;
}

if(isset($_POST['btnRequestChange']) && ($_POST['btnRequestChange'] <> ""))
{
	$content_checkbox = $_POST['content_checkbox'];
	$totalNoOfContent = $_POST['totalNoOfContent'];
	$month = $_POST['monthdate'];
	$year = $_POST['yeardate'];
	$progressStatus = $_POST['progressStatus'];
	$progressDetailStatus = $_POST['progressDetailStatus'];
	$progressDetailId = $_POST['progressDetailId'];
	$discussionId = $_POST['discussionId'];
	$chapterNo = $_POST['chapterNo'];
	$subchapterNo = $_POST['subchapterNo'];
	$curdatetime = date("Y-m-d H:i:s");	
	$studentMatrixNo;
	//$studentMatrixNo=$_GET['studentID'];
	$msg = Array();
	$count = count($content_checkbox);
	if ($count == 0) {
		$msg[] = "<div class=\"error\"><span>Please tick the checkbox for which content has been discussed for this monthly progress report. The previous checked checkbox will be restored.</span></div>";
	}
	if (empty($_POST['studentIssues'])) $msg[] = "<div class=\"error\"><span>You have removed the student issues entirely. It has been reverted back to the previous one. Please enter new issues facing by student if you want to replace it.</span></div>";
	if (empty($_POST['advice'])) $msg[] = "<div class=\"error\"><span>Advice field is empty. You might unintentionally removed it. It will be reverted back to the previous one if it has the data. Please enter new advice if you want to replace it.</span></div>";
	
	if(empty($msg)) 
	{
		if ($progressDetailStatus == 'IN1') {
			$sql8_0 = "SELECT id, pg_progress_id, pg_employee_empid, status,issues, advice, responded_status, submit_date,
			IFNULL(responded_date,'0000-00-00 00:00:00') as responded_date, 
			insert_by, IFNULL(insert_date,'0000-00-00 00:00:00') as insert_date, 
			modify_by, IFNULL(modify_date,'0000-00-00 00:00:00') as modify_date
			FROM pg_progress_detail
			WHERE id = '$progressDetailId'
			AND archived_status is NULL";
			
			$dbg->query($sql8_0);
			$dbg->next_record();
			
			$id = $dbg->f('id');
			$progressId = $dbg->f('pg_progress_id');
			$employeeId = $dbg->f('pg_employee_empid');
			$status = $dbg->f('status');
			$issues = $dbg->f('issues');
			$advice = $dbg->f('advice'); 
			$respondedStatus = $dbg->f('responded_status');
			$respondedDate = $dbg->f('responded_date');
			$submitDate = $dbg->f('submit_date');
			$insertBy = $dbg->f('insert_by');
			$insertDate = $dbg->f('insert_date');
			$modifyBy = $dbg->f('modify_by');
			$modifyDate = $dbg->f('modify_date');
			
			$newProgressDetailId = runnum('id','pg_progress_detail');
			
			$sql8_1 = "INSERT INTO pg_progress_detail
			(id, pg_progress_id, pg_employee_empid, status,issues, advice, responded_status, responded_date, submit_date, insert_by, 
			insert_date, modify_by, modify_date)
			VALUES ('$newProgressDetailId','$progressId','$employeeId', 'REQ','".$_POST['studentIssues']."','".$_POST['advice']."','$respondedStatus', '$curdatetime', '$submitDate', '$insertBy','$insertDate', '$modifyBy','$modifyDate')";
			
			$dbg->query($sql8_1);
			
			$sql8_2 = "UPDATE pg_progress_detail
					SET modify_by = '$user_id', modify_date = '$curdatetime', archived_status = 'ARC', archived_date = '$curdatetime'
					WHERE id = '$progressDetailId'";
					
			$dbg->query($sql8_2);
			
			for ($k=0; $k<$totalNoOfContent; $k++)
			{	
				$contentDescriptionArray[$k] = $_POST["contentDescription".$k];
				
				$sql8_3 = "SELECT id, pg_discussion_id, progress_detail_id, pg_employee_empid, content_discussion, discussed_status,
				responded_status, IFNULL(responded_date,'0000-00-00 00:00:00') as responded_date, 
				insert_by, IFNULL(insert_date,'0000-00-00 00:00:00') as insert_date, 
				modify_by, IFNULL(modify_date,'0000-00-00 00:00:00') as modify_date
				FROM pg_discussion_detail
				WHERE pg_discussion_id = '$discussionId[$k]'
				AND pg_employee_empid = '$user_id'
				AND archived_status is null";
				
				$dbg->query($sql8_3);
				$dbg->next_record();
				
				$discussionDetailId = $dbg->f('id');
				//$progressDetailId = $dbg->f('progress_detail_id');
				$mydiscussionId = $discussionId[$k];
				$employeeId = $dbg->f('pg_employee_empid'); 
				$contentDiscussion = $dbg->f('content_discussion');
				$respondedStatus = $dbg->f('responded_status'); 
				$respondedDate = $dbg->f('responded_date'); 
				$insertBy = $dbg->f('insert_by'); 
				$insertDate = $dbg->f('insert_date'); 
				$modifyBy = $dbg->f('modify_by'); 
				$modifyDate = $dbg->f('modify_date');
				
				$newDiscussionDetailId = runnum('id','pg_discussion_detail');
				
				$sql8_4 = "INSERT INTO pg_discussion_detail
				(id, pg_discussion_id, progress_detail_id, pg_employee_empid, content_discussion, responded_status, responded_date, discussed_status, insert_by, 
				insert_date, modify_by, modify_date)
				VALUES ('$newDiscussionDetailId', '$mydiscussionId', '$newProgressDetailId', '$employeeId', '$contentDescriptionArray[$k]',
				'$respondedStatus', '$curdatetime',	null, '$insertBy', '$insertDate', '$modifyBy', '$modifyDate')";
				
				$dbg->query($sql8_4);
				
				$sql8_5 = "UPDATE pg_discussion_detail
				SET modify_by = '$user_id', modify_date = '$curdatetime', 
				archived_status = 'ARC', archived_date = '$curdatetime'
				WHERE id = '$discussionDetailId'
				AND progress_detail_id = '$progressDetailId'
				AND pg_discussion_id = '$discussionId[$k]'
				AND pg_employee_empid = '$user_id'
				AND archived_status is null";
											
				$dbg->query($sql8_5);
			}
			
			while (list ($key,$val) = @each ($content_checkbox)) 
			{
				$sql2_2 = "update pg_discussion_detail 
				set discussed_status = 'Y'
				WHERE progress_detail_id = '$newProgressDetailId'
				AND pg_discussion_id = '$discussionId[$val]'
				AND pg_employee_empid = '$user_id'
				AND archived_status is null";

				$result2_2 = $dbb->query($sql2_2); 
				$dbb->next_record();

			}
			
			$sql5 = "UPDATE file_upload_progress
			SET progress_id = '$progressId', upload_status = 'CFM', modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE progress_id IS NULL
			AND pg_employee_empid = '$user_id'
			AND pg_proposal_id = '$proposalId'
			AND attachment_level = 'F'
			AND attachment_type IN ('A')
			AND upload_status = 'TMP'
			AND status = 'A'
			AND archived_status IS NULL";			
			
			$dba->query($sql5);
			
			$sql10 = "UPDATE pg_progress
			SET status = 'REQ', respond_status = 'Y', modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE id = '$progressId'
			AND reference_no = '$referenceNo'
			AND archived_status is null";
		
			$dbg->query($sql10);

			$msg[] = "<div class=\"success\"><span>Your update to this progress report has been saved successfully.</span></div>";

			$sqlemail = "SELECT name,email FROM student
			WHERE `matrix_no` = '$studentMatrixNo'";
			$resultreceive = $dbk->query($sqlemail);
			$resultsqlreceive = $dbk->next_record(); 
			$studemail = $dbk->f('email');
			$studname = $dbk->f('name');

			$sqlemail = "SELECT name,email FROM new_employee
			WHERE `empid` = '$user_id'";
			$resultreceive = $dbn->query($sqlemail);
			$resultsqlreceive = $dbn->next_record(); 
			$superEmail = $dbn->f('email');
			$superName = $dbn->f('name');

			$sqlthesis = "SELECT thesis_title FROM pg_proposal WHERE pg_thesis_id = '$thesisId'";
			$resultthesis = $dba->query($sqlthesis);
			$resultsqlthesis = $dba->next_record(); 
			$title = $dba->f('thesis_title');
			
			$sqlvalidate = "SELECT const_value
			FROM base_constant WHERE const_term = 'EMAIL_SUP_TO_STU'";
			$resultvalidate = $dbd->query($sqlvalidate);
			$dbd->next_record();
			$valid =$dbd->f('const_value');
			if($valid == 'Y')
			{
				include("../../../app/application/email/monthlyemail/req_change.php");
			}
			$sqlmsg = "SELECT const_value
			FROM base_constant WHERE const_term = 'MESSAGE_SUP_TO_STU'";
			$resultmsg = $dbd->query($sqlmsg);
			$dbd->next_record();
			$msg =$dbd->f('const_value');
			if($msg == 'Y')
			{
				include("../../../app/application/inbox/monthly/request_changes_msg.php");
			}
	
		}
		elseif ($progressDetailStatus == 'SV1')  {
			$sql8_6 = "UPDATE pg_progress_detail
					SET issues = '".$_POST['studentIssues']."', advice = '".$_POST['advice']."', responded_date = '$curdatetime',
					status = 'REQ', modify_by = '$user_id', modify_date = '$curdatetime'
					WHERE id = '$progressDetailId'";
					
			$dbg->query($sql8_6);
			
			for ($k=0; $k<$totalNoOfContent; $k++)
			{	
				$sql8_7 = "UPDATE pg_discussion_detail 
				set discussed_status = null
				WHERE pg_discussion_id = '$discussionId[$k]'
				AND progress_detail_id = '$progressDetailId'
				AND pg_employee_empid = '$user_id'
				AND archived_status is null";
											
				$dbg->query($sql8_7);
			}
			while (list ($key,$val) = @each ($content_checkbox)) 
			{
				$sql2_2 = "UPDATE pg_discussion_detail 
				set discussed_status = 'Y'
				WHERE pg_discussion_id = '$discussionId[$val]'
				AND progress_detail_id = '$progressDetailId'
				AND pg_employee_empid = '$user_id'
				AND archived_status is null";

				$result2_2 = $dbb->query($sql2_2); 
				$dbb->next_record();

			}
			
			$sql5 = "UPDATE file_upload_progress
			SET progress_id = '$progressId', upload_status = 'CFM', modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE progress_id IS NULL
			AND pg_employee_empid = '$user_id'
			AND pg_proposal_id = '$proposalId'
			AND attachment_level = 'F'
			AND attachment_type IN ('A')
			AND upload_status = 'TMP'
			AND status = 'A'
			AND archived_status IS NULL";			
			
			$dba->query($sql5);
			
			$sql10 = "UPDATE pg_progress
			SET status = 'REQ', respond_status = 'Y', modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE id = '$progressId'
			AND reference_no = '$referenceNo'
			AND archived_status is null";			
		
			$dbg->query($sql10);
				
			$msg[] = "<div class=\"success\"><span>Your update to this progress report has been saved successfully.</span></div>";			

			$sqlemail = "SELECT name,email FROM student
			WHERE `matrix_no` = '$studentMatrixNo'";
			$resultreceive = $dbk->query($sqlemail);
			$resultsqlreceive = $dbk->next_record(); 
			$studemail = $dbk->f('email');
			$studname = $dbk->f('name');

			$sqlthesis = "SELECT thesis_title FROM pg_proposal WHERE pg_thesis_id = '$thesisId'";
			$resultthesis = $dba->query($sqlthesis);
			$resultsqlthesis = $dba->next_record(); 
			$title = $dba->f('thesis_title');

			$sqlemail = "SELECT name,email FROM new_employee
			WHERE `empid` = '$user_id'";
			$resultreceive = $dbn->query($sqlemail);
			$resultsqlreceive = $dbn->next_record(); 
			$superEmail = $dbn->f('email');
			$superName = $dbn->f('name');

			$sqlvalidate = "SELECT const_value
			FROM base_constant WHERE const_term = 'EMAIL_SUP_TO_STU'";
			$resultvalidate = $dbd->query($sqlvalidate);
			$dbd->next_record();
			$valid =$dbd->f('const_value');
			if($valid == 'Y')
			{
				include("../../../app/application/email/monthlyemail/req_change.php");
			}
			$sqlmsg = "SELECT const_value
			FROM base_constant WHERE const_term = 'MESSAGE_SUP_TO_STU'";
			$resultmsg = $dbd->query($sqlmsg);
			$dbd->next_record();
			$msg =$dbd->f('const_value');
			if($msg == 'Y')
			{
				include("../../../app/application/inbox/monthly/request_changes_msg.php");
			}

		}
	}
}


if(isset($_POST['btnApproved']) && ($_POST['btnApproved'] <> ""))
{
	$content_checkbox = $_POST['content_checkbox'];
	$totalNoOfContent = $_POST['totalNoOfContent'];
	$month = $_POST['monthdate'];
	$year = $_POST['yeardate'];
	$progressStatus = $_POST['progressStatus'];
	$progressDetailStatus = $_POST['progressDetailStatus'];
	$progressDetailId = $_POST['progressDetailId'];
	$discussionId = $_POST['discussionId'];
	$chapterNo = $_POST['chapterNo'];
	$subchapterNo = $_POST['subchapterNo'];
	$curdatetime = date("Y-m-d H:i:s");	
	//$studentMatrixNo=$_GET['studentID'];	
	$msg = Array();
	$count = count($content_checkbox);
	if ($count == 0) {
		$msg[] = "<div class=\"error\"><span>Please tick the checkbox for which content has been discussed for this monthly progress report. The previous checked checkbox will be restored.</span></div>";
	}
	if (empty($_POST['studentIssues'])) $msg[] = "<div class=\"error\"><span>You have removed the student issues entirely. It has been reverted back to the previous one. Please enter new issues facing by student if you want to replace it.</span></div>";
	if (empty($_POST['advice'])) $msg[] = "<div class=\"error\"><span>Advice field is empty. You might unintentionally removed it. It will be reverted back to the previous one if it has the data. Please enter new advice if you want to replace it.</span></div>";
	
	if(empty($msg)) 
	{
		if ($progressDetailStatus == 'IN1') {
			$sql8_0 = "SELECT id, pg_progress_id, pg_employee_empid, status,issues, advice, responded_status, submit_date,
			IFNULL(responded_date,'0000-00-00 00:00:00') as responded_date, 
			insert_by, IFNULL(insert_date,'0000-00-00 00:00:00') as insert_date, 
			modify_by, IFNULL(modify_date,'0000-00-00 00:00:00') as modify_date
			FROM pg_progress_detail
			WHERE id = '$progressDetailId'
			AND archived_status is NULL";
			
			$dbg->query($sql8_0);
			$dbg->next_record();
			
			$id = $dbg->f('id');
			$progressId = $dbg->f('pg_progress_id');
			$employeeId = $dbg->f('pg_employee_empid');
			$status = $dbg->f('status');
			$issues = $dbg->f('issues');
			$advice = $dbg->f('advice'); 
			$respondedStatus = $dbg->f('responded_status');
			$respondedDate = $dbg->f('responded_date');
			$submitDate = $dbg->f('submit_date');
			$insertBy = $dbg->f('insert_by');
			$insertDate = $dbg->f('insert_date');
			$modifyBy = $dbg->f('modify_by');
			$modifyDate = $dbg->f('modify_date');
			
			$newProgressDetailId = runnum('id','pg_progress_detail');
			
			$sql8_1 = "INSERT INTO pg_progress_detail
			(id, pg_progress_id, pg_employee_empid, status,issues, advice, responded_status, responded_date, submit_date, insert_by, 
			insert_date, modify_by, modify_date)
			VALUES ('$newProgressDetailId','$progressId','$employeeId', 'APP','".$_POST['studentIssues']."','".$_POST['advice']."',
			'$respondedStatus',	'$curdatetime', '$submitDate', '$insertBy','$insertDate', '$modifyBy','$modifyDate')";
			
			$dbg->query($sql8_1);
			
			$sql8_2 = "UPDATE pg_progress_detail
					SET modify_by = '$user_id', modify_date = '$curdatetime', archived_status = 'ARC', archived_date = '$curdatetime'
					WHERE id = '$progressDetailId'";
					
			$dbg->query($sql8_2);
			
			for ($k=0; $k<$totalNoOfContent; $k++)
			{	
				
				$contentDescriptionArray[$k] = $_POST["contentDescription".$k];
				
				$sql8_3 = "SELECT id, pg_discussion_id, progress_detail_id, pg_employee_empid, content_discussion, discussed_status,
				responded_status, IFNULL(responded_date,'0000-00-00 00:00:00') as responded_date, 
				insert_by, IFNULL(insert_date,'0000-00-00 00:00:00') as insert_date, 
				modify_by, IFNULL(modify_date,'0000-00-00 00:00:00') as modify_date
				FROM pg_discussion_detail
				WHERE pg_discussion_id = '$discussionId[$k]'
				AND pg_employee_empid = '$user_id'
				AND archived_status is null";
				
				$dbg->query($sql8_3);
				$dbg->next_record();
				
				$discussionDetailId = $dbg->f('id');
				//$progressDetailId = $dbg->f('progress_detail_id');
				$progressDetailId = $dbg->f('progress_detail_id');
				$mydiscussionId = $discussionId[$k];
				$employeeId = $dbg->f('pg_employee_empid'); 
				$contentDiscussion = $dbg->f('content_discussion');
				$respondedStatus = $dbg->f('responded_status'); 
				$respondedDate = $dbg->f('responded_date'); 
				$insertBy = $dbg->f('insert_by'); 
				$insertDate = $dbg->f('insert_date'); 
				$modifyBy = $dbg->f('modify_by'); 
				$modifyDate = $dbg->f('modify_date');
				
				$newDiscussionDetailId = runnum('id','pg_discussion_detail');
				
				$sql8_4 = "INSERT INTO pg_discussion_detail
				(id, pg_discussion_id, progress_detail_id, pg_employee_empid, content_discussion, responded_status, responded_date, discussed_status, insert_by, insert_date, modify_by, modify_date)
				VALUES ('$newDiscussionDetailId', '$mydiscussionId', '$newProgressDetailId', '$employeeId', '$contentDescriptionArray[$k]',
				'$respondedStatus', '$curdatetime',	null, '$insertBy', '$insertDate', '$modifyBy', '$modifyDate')";
				
				$dbg->query($sql8_4);
				
				$sql8_5 = "UPDATE pg_discussion_detail
				SET modify_by = '$user_id', modify_date = '$curdatetime', 
				archived_status = 'ARC', archived_date = '$curdatetime'
				WHERE id = '$discussionDetailId'
				AND progress_detail_id = '$progressDetailId'
				AND pg_discussion_id = '$discussionId[$k]'
				AND pg_employee_empid = '$user_id'
				AND archived_status is null";
											
				$dbg->query($sql8_5);
			}
			
			while (list ($key,$val) = @each ($content_checkbox)) 
			{
				$sql2_2 = "UPDATE pg_discussion_detail 
				set discussed_status = 'Y'
				WHERE pg_discussion_id = '$discussionId[$val]'
				AND progress_detail_id = '$newProgressDetailId'
				AND pg_employee_empid = '$user_id'
				AND archived_status is null";

				$result2_2 = $dbb->query($sql2_2); 
				$dbb->next_record();

			}
			
			$sql5 = "UPDATE file_upload_progress
			SET progress_id = '$progressId', upload_status = 'CFM', modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE progress_id IS NULL
			AND pg_employee_empid = '$user_id'
			AND pg_proposal_id = '$proposalId'
			AND attachment_level = 'F'
			AND attachment_type IN ('A')
			AND upload_status = 'TMP'
			AND status = 'A'
			AND archived_status IS NULL";			
			
			$dba->query($sql5);
			
			//Check1 - Check total number of supervisor for this student
			$sql9 = "SELECT id 
			FROM pg_progress_detail				
			WHERE pg_progress_id = '$progressId'
			AND archived_status is null";
					
			$result9 = $dbg->query($sql9);
			$row_cnt9 = mysql_num_rows($result9);
			
			//Check2 - Check total number of supervisor who return back the monthly report back to the student
			$sql10 = "SELECT id 
			FROM pg_progress_detail				
			WHERE pg_progress_id = '$progressId'
			AND status IN ('APP')
			AND archived_status is null";
					
			$result10 = $dbg->query($sql10);
			$row_cnt10 = mysql_num_rows($result10);
			
			//If Check1 and Check2 tally, then change report status to be available to the student for amendment
			if ($row_cnt9 == $row_cnt10) {
				$sql10 = "UPDATE pg_progress
				SET status = 'APP', respond_status = 'Y', modify_by = '$user_id', modify_date = '$curdatetime'
				WHERE id = '$progressId'
				AND reference_no = '$referenceNo'
				AND archived_status is null";
			
				$dbg->query($sql10);
			}

			$msg[] = "<div class=\"success\"><span>Your update to this progress report has been saved and submitted successfully.</span></div>";	

			$sqlemail = "SELECT name,email FROM student
			WHERE `matrix_no` = '$studentMatrixNo'";
			$resultreceive = $dbk->query($sqlemail);
			$resultsqlreceive = $dbk->next_record(); 
			$studemail = $dbk->f('email');
			$studname = $dbk->f('name');
			
			$sqlthesis = "SELECT thesis_title FROM pg_proposal WHERE pg_thesis_id = '$thesisId'";
			$resultthesis = $dba->query($sqlthesis);
			$resultsqlthesis = $dba->next_record(); 
			$title = $dba->f('thesis_title');

			$sqlemail = "SELECT name,email FROM new_employee
			WHERE `empid` = '$user_id'";
			$resultreceive = $dbn->query($sqlemail);
			$resultsqlreceive = $dbn->next_record(); 
			$superEmail = $dbn->f('email');
			$superName = $dbn->f('name');

			$sqlvalidate = "SELECT const_value
			FROM base_constant WHERE const_term = 'EMAIL_SUP_TO_STU'";
			$resultvalidate = $dbd->query($sqlvalidate);
			$dbd->next_record();
			$valid =$dbd->f('const_value');
			if($valid == 'Y')
			{
				include("../../../app/application/email/monthlyemail/approved.php");
			}
			$sqlmsg = "SELECT const_value
			FROM base_constant WHERE const_term = 'MESSAGE_SUP_TO_STU'";
			$resultmsg = $dbd->query($sqlmsg);
			$dbd->next_record();
			$msg =$dbd->f('const_value');
			if($msg == 'Y')
			{
				include("../../../app/application/inbox/monthly/approved_msg.php");
			}

		}
		elseif ($progressDetailStatus == 'SV1')  {
			$sql8_6 = "UPDATE pg_progress_detail
					SET issues = '".$_POST['studentIssues']."', advice = '".$_POST['advice']."', responded_date = '$curdatetime',
					status = 'APP', modify_by = '$user_id', modify_date = '$curdatetime'
					WHERE id = '$progressDetailId'";
					
			$dbg->query($sql8_6);
			
			for ($k=0; $k<$totalNoOfContent; $k++)
			{	
				$sql8_7 = "UPDATE pg_discussion_detail 
				set discussed_status = null
				WHERE pg_discussion_id = '$discussionId[$k]'
				AND progress_detail_id = '$progressDetailId'
				AND pg_employee_empid = '$user_id'
				AND archived_status is null";
											
				$dbg->query($sql8_7);
			}
			while (list ($key,$val) = @each ($content_checkbox)) 
			{
				$sql2_2 = "UPDATE pg_discussion_detail 
				set discussed_status = 'Y'
				WHERE pg_discussion_id = '$discussionId[$val]'
				AND progress_detail_id = '$progressDetailId'
				AND pg_employee_empid = '$user_id'
				AND archived_status is null";

				$result2_2 = $dbb->query($sql2_2); 
				$dbb->next_record();

			}
			
			//Check1 - Check total number of supervisor for this student
			$sql9 = "SELECT id 
			FROM pg_progress_detail				
			WHERE pg_progress_id = '$progressId'
			AND archived_status is null";
					
			$result9 = $dbg->query($sql9);
			$row_cnt9 = mysql_num_rows($result9);
			
			//Check2 - Check total number of supervisor who return back the monthly report back to the student
			$sql10 = "SELECT id 
			FROM pg_progress_detail				
			WHERE pg_progress_id = '$progressId'
			AND status IN ('APP')
			AND archived_status is null";
					
			$result10 = $dbg->query($sql10);
			$row_cnt10 = mysql_num_rows($result10);
			
			//If Check1 and Check2 tally, then change report status to be available to the student for amendment
			if ($row_cnt9 == $row_cnt10) {
				$sql10 = "UPDATE pg_progress
				SET status = 'APP', respond_status = 'Y', modify_by = '$user_id', modify_date = '$curdatetime'
				WHERE id = '$progressId'
				AND reference_no = '$referenceNo'
				AND archived_status is null";			
			
				$dbg->query($sql10);
			}
			
			$msg[] = "<div class=\"success\"><span>This progress report has been save and submitted successfully.</span></div>";			

			$sqlemail = "SELECT name,email FROM student
			WHERE `matrix_no` = '$studentMatrixNo'";
			$resultreceive = $dbk->query($sqlemail);
			$resultsqlreceive = $dbk->next_record(); 
			$studemail = $dbk->f('email');
			$studname = $dbk->f('name');
	
			$sqlthesis = "SELECT thesis_title FROM pg_proposal WHERE pg_thesis_id = '$thesisId'";
			$resultthesis = $dba->query($sqlthesis);
			$resultsqlthesis = $dba->next_record(); 
			$title = $dba->f('thesis_title');

			$sqlemail = "SELECT name,email FROM new_employee
			WHERE `empid` = '$user_id'";
			$resultreceive = $dbn->query($sqlemail);
			$resultsqlreceive = $dbn->next_record(); 
			$superEmail = $dbn->f('email');
			$superName = $dbn->f('name');
			
			$sqlvalidate = "SELECT const_value
			FROM base_constant WHERE const_term = 'EMAIL_SUP_TO_STU'";
			$resultvalidate = $dbd->query($sqlvalidate);
			$dbd->next_record();
			$valid =$dbd->f('const_value');
			if($valid == 'Y')
			{
				include("../../../app/application/email/monthlyemail/approved.php");
			}
			$sqlmsg = "SELECT const_value
			FROM base_constant WHERE const_term = 'MESSAGE_SUP_TO_STU'";
			$resultmsg = $dbd->query($sqlmsg);
			$dbd->next_record();
			$msg =$dbd->f('const_value');
			if($msg == 'Y')
			{
				include("../../../app/application/inbox/monthly/approved_msg.php");
			}


		}
	}
}


if(isset($_POST['btnSave']) && ($_POST['btnSave'] <> ""))
{
	$content_checkbox = $_POST['content_checkbox'];
	$totalNoOfContent = $_POST['totalNoOfContent'];
	$progressStatus = $_POST['progressStatus'];
	$progressDetailStatus = $_POST['progressDetailStatus'];
	$progressDetailId = $_POST['progressDetailId'];
	$discussionId = $_POST['discussionId'];
	$chapterNo = $_POST['chapterNo'];
	$subchapterNo = $_POST['subchapterNo'];
	$curdatetime = date("Y-m-d H:i:s");	
	
	$msg = Array();
	$count = count($content_checkbox);
	if ($count == 0) {
		$msg[] = "<div class=\"error\"><span>Please tick the checkbox for which content has been discussed for this monthly progress report. The previous checked checkbox will be restored.</span></div>";
	}
	if (empty($_POST['studentIssues'])) $msg[] = "<div class=\"error\"><span>You have removed the student issues entirely. It has been reverted back to the previous one. Please enter new issues facing by student if you want to replace it.</span></div>";
	if (empty($_POST['advice'])) $msg[] = "<div class=\"error\"><span>Advice field is empty. You might unintentionally removed it. It will be reverted back to the previous one if it has the data. Please enter new advice if you want to replace it.</span></div>";
	
	if(empty($msg)) 
	{
		if ($progressDetailStatus == 'IN1') {
			$sql8_0 = "SELECT id, pg_progress_id, pg_employee_empid, status,issues, advice, responded_status, 
			IFNULL(responded_date,'0000-00-00 00:00:00') as responded_date, submit_date,
			insert_by, IFNULL(insert_date,'0000-00-00 00:00:00') as insert_date, 
			modify_by, IFNULL(modify_date,'0000-00-00 00:00:00') as modify_date
			FROM pg_progress_detail
			WHERE id = '$progressDetailId'
			AND archived_status is NULL";
			
			$dbg->query($sql8_0);
			$dbg->next_record();
			
			$id = $dbg->f('id');
			$progressId = $dbg->f('pg_progress_id');
			$employeeId = $dbg->f('pg_employee_empid');
			$status = $dbg->f('status');
			$issues = $dbg->f('issues');
			$advice = $dbg->f('advice'); 
			$respondedStatus = $dbg->f('responded_status');
			$respondedDate = $dbg->f('responded_date');
			$submitDate = $dbg->f('submit_date');
			$insertBy = $dbg->f('insert_by');
			$insertDate = $dbg->f('insert_date');
			$modifyBy = $dbg->f('modify_by');
			$modifyDate = $dbg->f('modify_date');
			
			$newProgressDetailId = runnum('id','pg_progress_detail');
			
			$sql8_1 = "INSERT INTO pg_progress_detail
			(id, pg_progress_id, pg_employee_empid, status,issues, advice, responded_status, responded_date, submit_date, insert_by, 
			insert_date, modify_by, modify_date)
			VALUES ('$newProgressDetailId','$progressId','$employeeId', 'SV1','".$_POST['studentIssues']."','".$_POST['advice']."','$respondedStatus', '$curdatetime', '$submitDate', '$insertBy','$insertDate', '$modifyBy','$modifyDate')";
			
			$dbg->query($sql8_1);
			
			$sql8_2 = "UPDATE pg_progress_detail
					SET modify_by = '$user_id', modify_date = '$curdatetime', archived_status = 'ARC', archived_date = '$curdatetime'
					WHERE id = '$progressDetailId'";
					
			$dbg->query($sql8_2);
			
			for ($k=0; $k<$totalNoOfContent; $k++)
			{	
				$contentDescriptionArray[$k] = $_POST["contentDescription".$k];
				
				$sql8_3 = "SELECT id, pg_discussion_id, progress_detail_id, pg_employee_empid, content_discussion, discussed_status,
				responded_status, IFNULL(responded_date,'0000-00-00 00:00:00') as responded_date, 
				insert_by, IFNULL(insert_date,'0000-00-00 00:00:00') as insert_date, 
				modify_by, IFNULL(modify_date,'0000-00-00 00:00:00') as modify_date
				FROM pg_discussion_detail
				WHERE pg_discussion_id = '$discussionId[$k]'
				AND pg_employee_empid = '$user_id'
				AND archived_status is null";
				
				$dbg->query($sql8_3);
				$dbg->next_record();
				
				$discussionDetailId = $dbg->f('id');
				//$progressDetailId = $dbg->f('progress_detail_id');
				$mydiscussionId = $discussionId[$k];
				$employeeId = $dbg->f('pg_employee_empid'); 
				$contentDiscussion = $dbg->f('content_discussion');
				$discussedStatus = $dbg->f('discussed_status');
				$respondedStatus = $dbg->f('responded_status'); 
				$respondedDate = $dbg->f('responded_date'); 
				$insertBy = $dbg->f('insert_by'); 
				$insertDate = $dbg->f('insert_date'); 
				$modifyBy = $dbg->f('modify_by'); 
				$modifyDate = $dbg->f('modify_date');
				
				$newDiscussionDetailId = runnum('id','pg_discussion_detail');
				
				$sql8_4 = "INSERT INTO pg_discussion_detail
				(id, pg_discussion_id, progress_detail_id, pg_employee_empid, content_discussion, responded_status, responded_date, discussed_status, insert_by, 
				insert_date, modify_by, modify_date)
				VALUES ('$newDiscussionDetailId', '$mydiscussionId', '$newProgressDetailId', '$employeeId', '$contentDescriptionArray[$k]',
				'$respondedStatus', '$curdatetime',	null, '$insertBy', '$insertDate', '$modifyBy', '$modifyDate')";
				
				$dbg->query($sql8_4);
				
				$sql8_5 = "UPDATE pg_discussion_detail
						SET modify_by = '$user_id', modify_date = '$curdatetime', 
						archived_status = 'ARC', archived_date = '$curdatetime'
						WHERE id = '$discussionDetailId'
						AND progress_detail_id = '$progressDetailId'
						AND pg_discussion_id = '$discussionId[$k]'
						AND pg_employee_empid = '$user_id'
						AND archived_status is null";
											
				$dbg->query($sql8_5);
			}
			
			while (list ($key,$val) = @each ($content_checkbox)) 
			{
				$sql2_2 = "update pg_discussion_detail 
				set discussed_status = 'Y'
				WHERE pg_discussion_id = '$discussionId[$val]'
				AND progress_detail_id = '$newProgressDetailId'
				AND pg_employee_empid = '$user_id'
				AND archived_status is null";

				$result2_2 = $dbb->query($sql2_2); 
				$dbb->next_record();

			}
			
			$sql5 = "UPDATE file_upload_progress
			SET progress_id = '$progressId', upload_status = 'CFM', modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE progress_id IS NULL
			AND pg_employee_empid = '$user_id'
			AND pg_proposal_id = '$proposalId'
			AND attachment_level = 'F'
			AND attachment_type IN ('A')
			AND upload_status = 'TMP'
			AND status = 'A'
			AND archived_status IS NULL";			
			
			$dba->query($sql5);
			
			$msg[] = "<div class=\"success\"><span>Your update to this progress report has been saved successfully.</span></div>";	
		}
		elseif ($progressDetailStatus == 'SV1')  {
			$sql8_6 = "UPDATE pg_progress_detail
					SET issues = '".$_POST['studentIssues']."', advice = '".$_POST['advice']."', responded_date = '$curdatetime',
					modify_by = '$user_id', modify_date = '$curdatetime'
					WHERE id = '$progressDetailId'";
					
			$dbg->query($sql8_6);
			
			for ($k=0; $k<$totalNoOfContent; $k++)
			{
				$sql2_2 = "update pg_discussion_detail 
				set discussed_status = null
				WHERE pg_discussion_id = '$discussionId[$k]'
				AND progress_detail_id = '$progressDetailId'
				AND pg_employee_empid = '$user_id'
				AND archived_status is null";

				$result2_2 = $dbb->query($sql2_2); 
				$dbb->next_record();
			}
			while (list ($key,$val) = @each ($content_checkbox)) 
			{
				$sql2_2 = "update pg_discussion_detail 
				set discussed_status = 'Y'
				WHERE pg_discussion_id = '$discussionId[$val]'
				AND progress_detail_id = '$progressDetailId'
				AND pg_employee_empid = '$user_id'
				AND archived_status is null";

				$result2_2 = $dbb->query($sql2_2); 
				$dbb->next_record();

			}
			
			$msg[] = "<div class=\"success\"><span>Your update to this progress report has been saved successfully.</span></div>";			
		}
	}
}

$thesisId=$_GET['tid'];
$proposalId=$_GET['pid'];


$sql2 = "SELECT a.id, b.id as progress_detail_id, a.reference_no, a.report_month, a.report_year, 
DATE_FORMAT(b.responded_date, '%d-%b-%Y %h:%i %p') as responded_date, 
DATE_FORMAT(a.submit_date, '%d-%b-%Y %h:%i %p') as submit_date, a.student_matrix_no, a.pg_thesis_id, a.pg_proposal_id, 
DATE_FORMAT(a.meeting_stime,'%h:%i') as meeting_stime, DATE_FORMAT(a.meeting_stime,'%p') as stime_pm, 
DATE_FORMAT(a.meeting_etime,'%h:%i') AS meeting_etime, DATE_FORMAT(a.meeting_etime,'%p') as etime_pm,		
DATE_FORMAT(a.meeting_date,'%d-%b-%Y') as meeting_date, b.status as progress_detail_status, b.advice,
a.status as progress_status, c2.description as progress_desc,
a.insert_by, a.insert_date, a.modify_by, a.modify_date,	b.issues as student_issues, b.issues as supervisor_issues, 
c.description as progress_detail_desc
FROM pg_progress a
LEFT JOIN pg_progress_detail b ON (b.pg_progress_id = a.id)
LEFT JOIN ref_proposal_status c ON (c.id = b.status)
LEFT JOIN ref_proposal_status c2 ON (c2.id = a.status)
WHERE a.student_matrix_no = '$studentMatrixNo'
AND a.pg_thesis_id = '$thesisId'
AND a.pg_proposal_id = '$proposalId'
AND a.reference_no = '$referenceNo'
AND b.pg_employee_empid = '$user_id'
AND a.archived_status is null
AND b.archived_status is null";

$result2 = $dbg->query($sql2); 
$dbg->next_record();

$id=$dbg->f('id');
$reportMonth=$dbg->f('report_month');
$reportYear=$dbg->f('report_year');
$meetingDate=$dbg->f('meeting_date');
$startTime=$dbg->f('meeting_stime');
$sTimePM=$dbg->f('stime_pm');
$eTimePM=$dbg->f('etime_pm');
$endTime=$dbg->f('meeting_etime');
$progressStatus=$dbg->f('progress_status');
$progressDesc=$dbg->f('progress_desc');
$submitDate=$dbg->f('submit_date');
$progressDetailStatus1=$dbg->f('progress_detail_status');
$progressDetailDesc1=$dbg->f('progress_detail_desc');
$respondedDate1=$dbg->f('responded_date');
$studentIssues=$dbg->f('student_issues');
$supervisorIssues=$dbg->f('supervisor_issues');
$advice=$dbg->f('advice');
$referenceNo=$dbg->f('reference_no');
$progressDetailId=$dbg->f('progress_detail_id');
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
	<script>
	function adviceAttachment(tid, pid, prgid, at, mn) {
		var ask = window.confirm("Ensure your report has been saved before proceed or otherwise the last change will be discarded.\nClick OK to proceed or CANCEL to stay on the same page.");
		if (ask) {
			document.location.href = "../monthlyreport/review_monthly_attachment.php?tid=" + tid + "&pid=" + pid + "&prgid=" + prgid + "&at=" + at + "&mn=" + mn;

		}
	}
	</script>
	<SCRIPT LANGUAGE="JavaScript">

	function respConfirm1 () {
		var confirmSubmit = confirm("Make sure any changes done is saved first. \nClick OK if confirm to submit or CANCEL to return back.");
		if (confirmSubmit==true)
		{
			return saveStatus;
		}
		if (confirmSubmit==false)
		{
			return false;
		}
	}
	
	function respConfirm2 () {
		var confirmSubmit = confirm("Make sure the required changes (if any) has been completed by the student. \nClick OK if confirm to submit or CANCEL to return back.");
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
	<input type="hidden" name="id" id="id" value="<?=$id; ?>">
	<input type="hidden" name="thesisId" id="thesisId" value="<?=$thesisId; ?>">
	<input type="hidden" name="proposalId" id="proposalId" value="<?=$proposalId; ?>">
	<input type="hidden" name="progressDetailId" id="progressDetailId" value="<?=$progressDetailId; ?>">
	<input type="hidden" name="referenceNo" id="referenceNo" value="<?=$referenceNo; ?>">
	<input type="hidden" name="progressStatus" id="progressStatus" value="<?=$progressStatus; ?>">
	<input type="hidden" name="progressDetailStatus" id="progressDetailStatus" value="<?=$progressDetailStatus1; ?>">
	<?if ($progressDetailStatus1 == "IN1" || $progressDetailStatus1 == "SV1") {?>
			<table border="0">
				<tr>
				<td><h2><strong>Report Details</strong><h2></td>
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
					<td>Report for Month of </td>
					<td>:</td>
					<td><label><?=$reportMonth?> <?=$reportYear?></label>
					<input type="hidden" name = "monthdate" id = "monthdate" value="<?=$reportMonth?>" />
					<input type="hidden" name = "yeardate" id = "yeardate" value="<?=$reportYear?>" /></td>			  
				</tr>
				<tr>
					<td>Submitted Date</td>
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
			</table>				
			</br>
		<?
		$sqlMeeting="SELECT a.id, DATE_FORMAT(a.meeting_date,'%d-%b-%Y') as meeting_date, 
		DATE_FORMAT(a.meeting_stime,'%h:%i %p') as meeting_stime, 		
		DATE_FORMAT(a.meeting_etime,'%h:%i %p') as meeting_etime, 
		b.description as meeting_mode_desc
		FROM  pg_progress_meeting  a
		LEFT JOIN ref_meeting_mode b ON (b.id = a.meeting_mode)
		WHERE a.pg_proposal_id='$proposalId'
		AND a.pg_thesis_id = '$thesisId'
		AND a.student_matrix_no = '$studentMatrixNo' 
		ORDER BY a.meeting_date DESC ";			
		$result = $db->query($sqlMeeting); 
		$row_cnt = mysql_num_rows($result);
		?>
		<table>
			<tr>
				<td><h3><strong>Meeting Details</strong></h3></td>
			</tr>
		</table>
			<table width="45%" border="1" cellpadding="3" cellspacing="1" style="border-collapse:collapse;" class="thetable">			
					<tr>
						<th width="5%">No</th>					
						<th width="10%">Meeting Date</th>
						<th width="10%">Meeting Start Time</th>
						<th width="10%">Meeting End Time</th>
						<th width="10%">Meeting Mode</th>
					</tr>
					
					<?php
				if ($row_cnt > 0) {
					
					$tmp_no = 0;
					while($row = mysql_fetch_array($result)) 									
					{ 
						?><tr>
								<td align="center"><label><?=$tmp_no+1;?>.</label></td>
								<td align="center"><label><?=$row["meeting_date"];?></label></td>
								<td align="center"><label><?=$row["meeting_stime"];?></label></td>
								<td align="center"><label><?=$row["meeting_etime"];?></label></td>
								<td align="center"><label><?=$row["meeting_mode_desc"];?></label></td>
							</tr>
						<?
						$tmp_no++;}
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
			<br/>
			<h3><legend><strong>Partner(s)</strong></h3>
			<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="90%" class="thetable">			
				<tr>
					<th width="5%">No</th>					
					<th width="15%" align="left">Role</th>
					<th width="10%" align="left">Staff ID</th>
					<th width="20%" align="left">Name</th>
					<th width="5%"  align="left">Faculty</th>
					<th width="10%">View Feedback</th>
					<th width="10%"  align="left">Status</th>
					<th width="15%"  align="left">Last Update</th>
				</tr>
				 <?php				
				
				$sql_supervisor = "SELECT a.id, b.pg_employee_empid, c.ref_supervisor_type_id, d.description as supervisor_desc, 
				DATE_FORMAT(b.responded_date,'%d-%b-%Y %h:%i %p') AS responded_date, e.description as progress_detail_desc,
				f.description as role_status_desc
				FROM pg_progress_detail b 
				LEFT JOIN pg_progress a ON (a.id = b.pg_progress_id)
				LEFT JOIN pg_supervisor c ON (c.pg_employee_empid = b.pg_employee_empid)
				LEFT JOIN ref_supervisor_type d ON (d.id = c.ref_supervisor_type_id)
				LEFT JOIN ref_proposal_status e ON (e.id = b.status)
				LEFT JOIN ref_role_status f ON (f.id = c.role_status)
				WHERE a.id = '$progressId'
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
						$progressDetailDesc = $db_klas2->f('progress_detail_desc');
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
							$sql12 = "SELECT a.status as progress_detail_status, c.description as progress_detail_desc
							FROM pg_progress_detail a
							LEFT JOIN pg_progress b ON (b.id = a.pg_progress_id)
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
							$progressDetailStatus=$dbg->f('progress_detail_status');
							$progressDetailDesc=$dbg->f('progress_detail_desc');
							
							if ($progressDetailStatus != 'IN1') {
							?>								
								<td><a href="progress_view_feedback_staff.php?eid=<?=$employeeId?>&mn=<?=$studentMatrixNo?>&pid=<?=$proposalId;?>&tid=<?=$thesisId;?>&id=<?=$id;?>" title="Description of topic or Issues facing by Student - Read more..."><img src="../images/view.jpg" width="45" height="30" style="border:0px;" title="View feedback"></a>
								</td>	
							<?}
							else {
								?>
								<td></td>
								<?
							}?>
							<?if ($progressDetailStatus != 'SV1') {?>
								<td><label><?=$progressDetailDesc?></label></td>
							<?}
							else {?>
								<td><label><span style="color:#FF0000"><?=$progressDetailDesc?></span></label></td>
							<?}?>
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

			<table>
				<tr>
					<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../monthlyreport/review_progress.php';" /></td>
				</tr>
			</table>	
			</br>
			<?
			$sql2 = "SELECT a.id as chapter_id, a.chapter_no, a.description as chapter_desc, 
			b.id as subchapter_id, b.subchapter_no, b.description as subchapter_desc
			FROM pg_chapter a
			LEFT JOIN pg_subchapter b ON (b.chapter_id = a.id)  
			WHERE a.status = 'A'
			AND a.student_matrix_no = '$studentMatrixNo'
			AND (b.status = 'A' OR b.status IS NULL)
			ORDER BY a.chapter_no, b.subchapter_no";

			$result2 = $dbb->query($sql2); 
			$dbb->next_record();
			$row_cnt2 = mysql_num_rows($result2);
			
			$chapterArray = Array();
			$no=0;
			do {											
				$chapterId[$no]=$dbb->f('chapter_id');	
				$chapterNo[$no]=$dbb->f('chapter_no');
				$chapterDesc[$no]=$dbb->f('chapter_desc');
				$subchapterId[$no]=$dbb->f('subchapter_id');	
				$subchapterNo[$no]=$dbb->f('subchapter_no');
				$subchapterDesc[$no]=$dbb->f('subchapter_desc');
				$no++;
			}while($dbb->next_record());	
			
			?>
			<table>
				<tr>
					<td>Review Status</td>
					<td>:</td>
					<td><strong><?=$progressDetailDesc1?></strong></td>
				</tr>
				<tr>
					<td>Last Update</td>
					<td>:</td>
					<td><label><?=$respondedDate1?></label></td>			  
				</tr>
			</table>

			<h3><strong>Content of Discussion</strong></h3>
			<table>
				<?
				for ($i=0; $i<$no; $i++){
				?>
					<?								
						$sql11= " SELECT a.discussed_status, a.pg_discussion_id as discussion_id
						FROM pg_discussion_detail a
						LEFT JOIN pg_discussion b ON (b.id = a.pg_discussion_id)
						WHERE b.pg_thesis_id = '$thesisId'
						AND b.pg_proposal_id = '$proposalId'
						AND b.student_matrix_no = '$studentMatrixNo'
						AND b.pg_chapter_id = '$chapterId[$i]'
						AND b.pg_subchapter_id = '$subchapterId[$i]'
						AND a.pg_employee_empid = '$user_id'
						AND a.archived_status is null
						AND b.archived_status is null";
											
						$result_sql11 = $dbb->query($sql11); 
						$dbb->next_record();
						$discussedStatus = $dbb->f('discussed_status');
						$discussionId = $dbb->f('discussion_id');
					?>
					<tr>
						<?
						if ($discussedStatus == 'Y') {									
						?>
							<td align="center"><input name="content_checkbox[]" type="checkbox" id="content_checkbox" value="<?=$i;?>" checked="checked" /></input></td>
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
					<input type="hidden" name="discussionId[]" id="discussionId" value="<?=$discussionId; ?>">
				<?
				}
				?>		
				<input type="hidden" name="totalNoOfContent" id="totalNoOfContent" value="<?=$i; ?>">
				
			</table>
			<br/>
			<fieldset>
				<legend><strong>Description of topic or Issues facing by Student</strong><span style="color:#FF0000"> *</span></legend>
			<table>
				<tr>
					<td><textarea name="studentIssues" class="ckeditor" ><?=$studentIssues; ?></textarea></td>
				</tr>
				<tr>
					<td></br><strong>Attachment file by Student:</strong></td>
				</tr>
				<tr>
				<?php
				

					$sqlUpload="SELECT * FROM file_upload_progress 
					WHERE progress_id = '$id'
					AND pg_proposal_id='$proposalId' 
					AND student_matrix_no = '$studentMatrixNo'
					AND attachment_level = 'S' 
					AND attachment_type = 'I'
					AND upload_status IN ('CFM')
					AND status = 'A'
					AND archived_status is null";			
				
					$result = $db_klas2->query($sqlUpload); //echo $sql;
					$row_cnt = mysql_num_rows($result);
					$attachmentNo2=1;
					if ($row_cnt>0)
					{
						?><td align="left"><?
						while($row = mysql_fetch_array($result)) 					
						{ 
							?>
								<a href="progress_download.php?fc=<?=$row["fu_cd"];?>&al=S&at=I" title="File Description: <?=$row["fu_document_filedesc"];?>">Attachment <?=$attachmentNo2++;?>: <img src="../images/download.png" width="20" height="19" style="border:0px;" title="Download <?=$row["fu_document_filename"];?>"></a>							
						<?}
						?></td><?
					}
					else {
						?><br/><td>No attachment found.</td><?
					}
				?>
				</tr>			
			</table>
			</fieldset>
			<br/>
			<fieldset>
			<legend><strong>Advice from Supervisor & List of Action to be taken by student</strong><span style="color:#FF0000"> *</span></legend>
			<table>
				<?if ($advice == "") $advice = $_POST['advice'];?>
				<tr>
					<td><textarea name="advice" class="ckeditor" ><?=$advice; ?></textarea></td>
				</tr>			
				
				<tr>
					<td></br><strong>Attachment file by Student:</strong></td>
				</tr>
				<tr>
				<?php
				

				$sqlUpload="SELECT * FROM file_upload_progress 
				WHERE progress_id = '$id'
				AND pg_proposal_id='$proposalId' 
				AND student_matrix_no = '$studentMatrixNo'
				AND attachment_level = 'S' 
				AND attachment_type = 'A'
				AND upload_status IN ('TMP','CFM')
				AND status = 'A'
				AND archived_status is null";			
				
				$result = $db_klas2->query($sqlUpload); //echo $sql;
				$row_cnt = mysql_num_rows($result);
				$attachmentNo2=1;
				if ($row_cnt>0)
				{
					?><td align="left"><?
					while($row = mysql_fetch_array($result)) 					
					{ 
						?>
							<a href="progress_download.php?fc=<?=$row["fu_cd"];?>&al=S&at=A" title="File Description: <?=$row["fu_document_filedesc"];?>">Attachment <?=$attachmentNo2++;?>: <img src="../images/download.png" width="20" height="19" style="border:0px;" title="Download <?=$row["fu_document_filename"];?>"></a>							
					<?}
					?></td><?
				}
				else {
					?><br/><td>No attachment found.</td><?
				}
				?>
				</tr>			
				
				<tr>
					<td></br><strong>Attachment file by Supervisor/Co-Supervisor:</strong></td>
				</tr>
				<tr>
				<?
				$sqlUpload="SELECT * FROM file_upload_progress 
				WHERE (progress_id IS NULL OR progress_id = '$id')
				AND  pg_proposal_id = '$proposalId' 
				AND pg_employee_empid = '$user_id'
				AND attachment_level = 'F' 
				AND attachment_type = 'A'
				AND upload_status IN ('TMP','CFM')
				AND status = 'A'
				AND archived_status is null";				

				$result = $db->query($sqlUpload); 
				$row_cnt = mysql_num_rows($result);
				if ($row_cnt == '0')
				{
					$row_cnt_tmp = '';
				}
				else
				{
					$row_cnt_tmp = "(".$row_cnt.")";
				}	
				$attachmentNo2=1;?>

					<br/><td><button type="button" name="btnAttachment" value="Attachment" onclick="return adviceAttachment('<?=$thesisId?>','<?=$proposalId?>','<?=$id?>','A','<?=$studentMatrixNo?>')" >
						Attachment <FONT COLOR="#FF0000"><sup><?=$row_cnt_tmp?></sup></FONT></button>
					</td><?

				?>
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

			</table>
			<table>
				<tr>
					<td><input type="submit" name="btnSave" value="Save"/></td>
					<td><input type="submit" name="btnRequestChange" value="Request Change" onClick="return respConfirm1()"/></td>
					<td><input type="submit" name="btnApproved" value="Approve" onClick="return respConfirm2()"/></td>
					<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../monthlyreport/review_progress.php';" /></td>
				</tr>
			</table>
	<?}
	else if ($progressDetailStatus1 == 'REQ')  
	{
		$theReferenceNo = $_POST['referenceNo'];
		$theStudentName = $_POST['sname'];
		$msg = "<div class=\"success\"><span>Monthly Progress Report <strong>$theReferenceNo</strong> has been submitted back to the student $theStudentName for the required changes.</span></div>";
		echo $msg;		
		?>
		<table>
			<tr>
				<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../monthlyreport/review_progress.php';" /></td>
			</tr>
		</table>
	<?}
	else if ($progressDetailStatus1 == 'APP')  
	{?><fieldset>
		<legend><strong>Notification Message</strong></legend>
			<table>
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
					<td>Monthly Progress Report <strong><?=$referenceNo?></strong> for student <strong><?=$sname?></strong> has been approved successfully.</td>
				</tr>
				<tr>
					<td>Click here <a href="../monthlyreport/progress_view_feedback_approved.php?eid=<?=$user_id?>&mn=<?=$studentMatrixNo?>&pid=<?=$proposalId;?>&tid=<?=$thesisId;?>&id=<?=$id;?>" title="View Submitted Monthly Progress Report"><img src="../images/view.jpg" width="45" height="30" style="border:0px;" title="View feedback"></a> to view your previously approved report. </td>
				</tr>
			</table>				
			<br/>
			<table>
					<tr>
						<td><strong>Current Status of Monthly Progress Report - by Partner(s)</strong></td>
					</tr>
				</table>
				<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="100%" class="thetable">			
				<tr>
					<th>No</th>					
					<th>Staff ID</th>
					<th>Name</th>
					<th>Faculty</th>
					<th>View Feedback</th>
					<th>Role</th>
					<th>Status</th>
					<th>Last Update</th>
				</tr>
				 <?php				
				
				$sql_supervisor = "SELECT a.id, b.pg_employee_empid, a.report_month, a.report_year, 
				DATE_FORMAT(a.submit_date, '%d-%b-%Y') as submit_date, 
				a.student_matrix_no, a.pg_thesis_id, a.pg_proposal_id, 
				DATE_FORMAT(a.meeting_stime,'%h:%i') as meeting_stime, DATE_FORMAT(a.meeting_stime,'%p') as stime_pm, 
				DATE_FORMAT(a.meeting_etime,'%h:%i') AS meeting_etime, DATE_FORMAT(a.meeting_etime,'%p') as etime_pm,		
				DATE_FORMAT(a.meeting_date,'%d-%b-%Y') as meeting_date, a.status as progress_status, 
				a.insert_by, a.insert_date, a.modify_by, a.modify_date,	a.issues as student_issues, b.issues as supervisor_issues,
				d.description as supervisor_desc, c.ref_supervisor_type_id, b.status as progress_detail_status,
				e.description as progress_detail_desc, DATE_FORMAT(b.responded_date,'%d-%b-%Y %h:%i %p') AS responded_date,
				f.description as role_status_desc
				FROM pg_progress_detail b 
				LEFT JOIN pg_progress a ON (a.id = b.pg_progress_id)
				LEFT JOIN pg_supervisor c ON (c.pg_employee_empid = b.pg_employee_empid)
				LEFT JOIN ref_supervisor_type d ON (d.id = c.ref_supervisor_type_id)
				LEFT JOIN ref_proposal_status e ON (e.id = b.status)
				LEFT JOIN ref_role_status f ON (f.id = c.role_status)
				WHERE a.id = '$progressId'
				AND a.student_matrix_no = '$studentMatrixNo'
				AND b.pg_employee_empid <> '$user_id'
				AND a.pg_thesis_id = '$thesisId'
				AND a.pg_proposal_id = '$proposalId'
				AND a.archived_status is null
				AND b.archived_status is null
				AND c.pg_student_matrix_no = '$studentMatrixNo'
				AND c.pg_thesis_id = '$thesisId'
				AND c.ref_supervisor_type_id in ('SV','CS','XS')
				AND c.status = 'A'";

				$result_sql_supervisor = $db_klas2->query($sql_supervisor); //echo $sql;
				
				$row_cnt_supervisor = mysql_num_rows($result_sql_supervisor);
				$db_klas2->next_record();
				$varRecCount=0;	
				if ($row_cnt_supervisor>0) {

					do {
						$employeeId = $db_klas2->f('pg_employee_empid');
						$supervisorTypeId = $db_klas2->f('ref_supervisor_type_id');
						$supervisorDesc = $db_klas2->f('supervisor_desc');
						$respondedDate = $db_klas2->f('responded_date');
						$progressDetailDesc = $db_klas2->f('progress_detail_desc');
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
							<td align="left"><?=$employeeId;?></td>
							<td align="left"><?=$employeeName;?></td>
							<td align="left"><a href="javascript:void(0);" onMouseOver="toolTip('<?=$departmentName;?>', 300)" onMouseOut="toolTip()"><?=$departmentId;?></a></td>
							
							<td><a href="../monthlyreport/progress_view_feedback_staff.php?eid=<?=$employeeId?>&mn=<?=$studentMatrixNo?>&pid=<?=$proposalId;?>&tid=<?=$thesisId;?>&id=<?=$id;?>" title="Description of topic or Issues facing by Student - Read more..."><img src="../images/view.jpg" width="45" height="30" style="border:0px;" title="View feedback"></a>
							</td>	
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
							<td><label><?=$progressDetailDesc?></label></td>
							<td><label><?=$respondedDate?></label></td>
						<?
						} while($db_klas2->next_record());						
					}
					else {
						?>
						<table>				
							<tr>
								<td><label>No record found.</label></td>								
							</tr>
							<tr><td><br/><span style="color:#FF0000">Note:</span><br/>
										Possible Reasons:-<br/>
										1. Supervisor/Co-Supervisor is yet to be assigned<br/>
										2. Pending approval by the Senate.<br/>
										3. If already assigned, it could be the Supervisor/Co-Supervisor pending to accept<br/></td>
							</tr>
						</table>
						<?
					}?>
			</table>			
		</fieldset>
		
		<table>
			<tr>
				<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../monthlyreport/review_progress.php';" /></td>
			</tr>
		</table>
	<?}
	else  
	{?><fieldset>
			<legend><strong><span style="color:#FF0000">Notification Message</span></strong></legend>
				<table>
					<tr>
						<td>You have no Monthly Report from your student to review. Please check again later.</td>
					</tr>
				</table>				
			</fieldset>
			<table>
				<tr>
					<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../monthlyreport/review_progress.php';" /></td>
				</tr>
			</table>
	<?}?>
		</form>
</body>
</html>




