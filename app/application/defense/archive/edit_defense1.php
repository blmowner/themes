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
	
<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: edit_defense.php
//
// Created by: Zuraimi
// Created Date: 29-Jun-2015
// Modified by: Zuraimi
// Modified Date: 29-Jun-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

session_start();
$user_id=$_SESSION['user_id'];
$thesisId = $_GET['tid']; 
$proposalId = $_GET['pid'];
$referenceNo=$_GET['ref'];

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

if(isset($_POST['btnSave']) && ($_POST['btnSave'] <> ""))
{
	
	$content_checkbox = $_POST['content_checkbox'];
	//$id = $_POST['id'];
	$defenseId = $_POST['id'];
	$oldDefenseId = $_POST['id'];
	$thesisId = $_POST['thesisId'];
	$proposalId = $_POST['proposalId'];
	$defenseDate = $_POST['defenseDate'];
	$defenseStatus = $_POST['defenseStatus'];
	$defenseDetailId = $_POST['defenseDetailId'];
	$supervisorIdArray = $_POST['supervisorIdArray'];
	$referenceNo = $_POST['referenceNo'];
	$contentDescriptionArray = Array();
	$chapterId = $_POST['chapterId'];
	$subchapterId = $_POST['subchapterId'];
	$totalNoOfContent = $_POST['totalNoOfContent'];
	$curdatetime = date("Y-m-d H:i:s");	
	$chapterNo = $_POST['chapterNo'];
	$subchapterNo = $_POST['subchapterNo'];
	
	$msg = Array();
	$currentDate1 = date('d-M-Y');
	$currentDate2 = new DateTime($currentDate1);
	$currentDate3 = $currentDate2->format('d-M-Y');
	$currentDate4 = new DateTime($currentDate3);
	
	$defenseDate1 = date('d-M-Y', strtotime($_POST['defenseDate']));
	$defenseDate2 = new DateTime($defenseDate1);
	$defenseDate3 = $defenseDate2->format('d-M-Y');
	$defenseDate4 = new DateTime($defenseDate3);
	
	$plannedDate1 = date('d-M-Y', strtotime($_POST['plannedDate'.$val]));
	$plannedDate2 = new DateTime($plannedDate1);
	$plannedDate3 = $plannedDate2->format('d-M-Y');
	$plannedDate4 = new DateTime($plannedDate3);

	$completionDate1 = date('d-M-Y', strtotime($_POST['completionDate'.$val]));
	$completionDate2 = new DateTime($completionDate1);
	$completionDate3 = $completionDate2->format('d-M-Y');
	$completionDate4 = new DateTime($completionDate3);	
	
	if ($defenseDate4 < $currentDate4) $msg[] = "<div class=\"error\"><span>Defense Date cannot be earlier than Current Date.</span></div>";
	if ($plannedDate4 > $completionDate4) $msg[] = "<div class=\"error\"><span>Planned Date for $romanChapter $romanSubChapter cannot be later than Completion Date.</span></div>";		
	if ($plannedDate4 > $currentDate4) $msg[] = "<div class=\"error\"><span>Planned Date for $romanChapter $romanSubChapter cannot be later than Current Date.</span></div>";
	if ($completionDate4 > $currentDate4) $msg[] = "<div class=\"error\"><span>Completion Date for $romanChapter $romanSubChapter cannot be later than Current Date.</span></div>";	
	
	while (list ($key,$val) = @each ($content_checkbox)) {
		if ($subchapterNo[$val] != "") {
			$romanSubChapter = "Subchapter ".romanNumerals($subchapterNo[$val]);
		}
		else {
			$romanChapter = "Chapter ".romanNumerals($chapterNo[$val]);
		}
		
		if (empty($_POST['plannedDate'.$val])) $msg[] = "<div class=\"error\"><span>Please provide Planned Date for $romanChapter $romanSubChapter</span></div>";	
		if (empty($_POST['completionDate'.$val])) $msg[] = "<div class=\"error\"><span>Please provide Completion Date for $romanChapter $romanSubChapter</span></div>";
		if (empty($_POST['contentDescription'.$val])) $msg[] = "<div class=\"error\"><span>Please provide Description of Work for $romanChapter $romanSubChapter</span></div>";		
	}
	
	if(empty($msg)) 
	{
		if ($defenseStatus=="") {
			
			$defenseId = runnum('id','pg_defense');
			$referenceNo = "D".runnum2('reference_no','pg_defense');
			
			$sql1 = " INSERT INTO pg_defense
			(id, reference_no, defense_date, submit_date, student_matrix_no, pg_thesis_id, pg_proposal_id,
			status, submit_status, respond_status,
			insert_by, insert_date, modify_by, modify_date )
			VALUES ('$defenseId', '$referenceNo', STR_TO_DATE('$defenseDate','%d-%M-%Y'), '$curdatetime', '$user_id', '$thesisId', '$proposalId', 
			'SAV','SAV', 'N', '$user_id', '$curdatetime', '$user_id', '$curdatetime')";
			
			$dba->query($sql1); 
			$newDefenseDetailIdArray = Array();
			while (list ($key,$val) = @each ($supervisorIdArray)) 
			{
				$newDefenseDetailId = runnum('id','pg_defense_detail');
				$newDefenseDetailIdArray[$key] = $newDefenseDetailId;
				
				$sql2 = " INSERT INTO pg_defense_detail
				(id, pg_defense_id, pg_employee_empid, status, submit_date,
				insert_by, insert_date, modify_by, modify_date )
				VALUES ('$newDefenseDetailId', '$defenseId', '$val', 'SAV', '$curdatetime',
				'$user_id', '$curdatetime', '$user_id', '$curdatetime')";
				
				$dba->query($sql2);
			}

			for ($k=0; $k<$totalNoOfContent; $k++)
			{	
				$contentDescriptionArray[$k] = $_POST["contentDescription".$k];
				$plannedDate[$k] = $_POST['plannedDate'.$k];
				$completionDate[$k] = $_POST['completionDate'.$k];

				$newAchievementId = runnum('id','pg_achievement');

				$sql3 = " INSERT INTO pg_achievement
				(id, defense_id, reference_no, pg_thesis_id, pg_proposal_id, student_matrix_no, pg_chapter_id, 
				pg_subchapter_id, discussed_status, 
				planned_date, completion_date, content_discussion, 
				insert_by, insert_date, modify_by, modify_date )
				VALUES ('$newAchievementId', '$defenseId', '$referenceNo', '$thesisId', '$proposalId', '$user_id', '$chapterId[$k]', 
				'$subchapterId[$k]',  null, STR_TO_DATE('$plannedDate[$k]','%d-%M-%Y'), STR_TO_DATE('$completionDate[$k]','%d-%M-%Y'), 
				'$contentDescriptionArray[$k]', '$user_id', '$curdatetime', '$user_id', '$curdatetime')";
				
				$dba->query($sql3);

			}
			
			$sql6 = "UPDATE pg_defense_publication
			SET pg_defense_id = '$defenseId', reference_no = '$referenceNo', 
			add_status = 'CFM', modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE pg_defense_id IS NULL
			AND reference_no IS NULL
			AND pg_thesis_id = '$thesisId'
			AND pg_proposal_id = '$proposalId'
			AND student_matrix_no = '$user_id'
			AND add_status = 'TMP'
			AND archived_status IS NULL";
			
			$dba->query($sql6);
			
			$sql7 = "UPDATE pg_defense_conference
			SET pg_defense_id = '$defenseId', reference_no = '$referenceNo', 
			add_status = 'CFM', modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE pg_defense_id IS NULL
			AND reference_no IS NULL
			AND pg_thesis_id = '$thesisId'
			AND pg_proposal_id = '$proposalId'
			AND student_matrix_no = '$user_id'
			AND add_status = 'TMP'
			AND archived_status IS NULL";
			
			$dba->query($sql7);
			
			$msg[] = "<div class=\"success\"><span>Your Progression Monitoring Report(Defense Proposal) has been saved successfully.</span></div>";
		}
		else if ($defenseStatus=="SAV") {//SAV

			$sql5 = " UPDATE pg_defense
			SET submit_date = '$curdatetime', 
			status = 'SAV', defense_date = STR_TO_DATE('$defenseDate','%d-%M-%Y'), submit_status = 'SAV', respond_status = 'N', modify_by = '$user_id', 
			modify_date = '$curdatetime'
			WHERE id = '$defenseId'
			AND student_matrix_no = '$user_id'
			AND pg_thesis_id = '$thesisId'
			AND pg_proposal_id = '$proposalId'";

			$dba->query($sql5);
			
			while (list ($key,$val) = @each ($supervisorIdArray)) 
			{
				$sql5_4 = "SELECT id, pg_defense_id, pg_employee_empid, status, responded_status, responded_date, 
				submit_date, insert_by, insert_date, modify_by, modify_date
				FROM pg_defense_detail
				WHERE pg_defense_id = '$defenseId'
				AND pg_employee_empid = '$val'
				AND archived_status is NULL";
				
				$result_5_4 = $dba->query($sql5_4);
				$dba->next_record();
				
				$pg_defense_detail_id = $dba->f('id');
				//$pg_defense_id = $dba->f('pg_defense_id'); 
				$employeeId = $dba->f('pg_employee_empid');
				$status = $dba->f('status');
				$responded_status = $dba->f('responded_status'); 
				$responded_date = $dba->f('responded_date'); 
				//$submitDate = $dba->f('submit_date');
				$insert_by = $dba->f('insert_by'); 
				$insert_date = $dba->f('insert_date'); 
				$modify_by = $dba->f('modify_by'); 
				$modify_date = $dba->f('modify_date'); 
				
				$defenseDetailIdArray[$key] = $pg_defense_detail_id;
				$row_cnt_5_4 = mysql_num_rows($result_5_4);
				if ($row_cnt_5_4 > 0) {
					if ($status != 'APP') {
						$sql5_5 = " UPDATE pg_defense_detail
						SET submit_date = '$curdatetime', modify_by = '$user_id', modify_date = '$curdatetime'			
						WHERE pg_defense_id = '$defenseId'
						AND pg_employee_empid = '$val'
						AND archived_status IS NULL ";
						
						$dba->query($sql5_5);
					}
				}
				else {
					$newDefenseDetailId = runnum('id','pg_defense_detail');
					$newDefenseDetailIdArray[$key] = $newDefenseDetailId;
					
					$sql2 = " INSERT INTO pg_defense_detail
					(id, pg_defense_id, pg_employee_empid, status, submit_date, 
					insert_by, insert_date, modify_by, modify_date )
					VALUES ('$newDefenseDetailId', '$defenseId', '$val', 'SAV', 
					'$submitDate', '$user_id', '$curdatetime', '$user_id', '$curdatetime')";
					
					$dba->query($sql2);
				}
			}
			$achievementIdArray = Array();
			for ($k=0; $k<$totalNoOfContent; $k++)
			{	

				$sql6 = "SELECT id 
				FROM pg_achievement
				WHERE defense_id = '$defenseId'
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
				$achievementId = $dba->f('id');
				
				$plannedDate[$k] = $_POST['plannedDate'.$k];
				$completionDate[$k] = $_POST['completionDate'.$k];
				$contentDescriptionArray[$k] = $_POST["contentDescription".$k];
				
				$row_cnt_sql6 = mysql_num_rows($result_sql6);
				
				$newAchievementId = runnum('id','pg_achievement');
				
				
				if ($row_cnt_sql6 == 0) {
					$achievementIdArray[$k] = $newAchievementId;
					$sql3 = " INSERT INTO pg_achievement
					(id, defense_id, reference_no, pg_thesis_id, pg_proposal_id, student_matrix_no, pg_chapter_id, pg_subchapter_id, discussed_status, planned_date, completion_date, 
					content_discussion, insert_by, insert_date, modify_by, modify_date )
					VALUES ('$newAchievementId', '$defenseId', '$referenceNo', '$thesisId', '$proposalId', '$user_id', '$chapterId[$k]', 
					'$subchapterId[$k]', null, STR_TO_DATE('$plannedDate[$k]','%d-%M-%Y'), STR_TO_DATE('$completionDate[$k]','%d-%M-%Y'), 
					'$contentDescriptionArray[$k]', '$user_id', '$curdatetime', '$user_id', '$curdatetime')";
					
					$dba->query($sql3);
				}
				else {
					$achievementIdArray[$k] = $achievementId;
					$sql6_1 = "UPDATE pg_achievement
					SET discussed_status = null, planned_date = STR_TO_DATE('$plannedDate[$k]','%d-%M-%Y'), 
					completion_date = STR_TO_DATE('$completionDate[$k]','%d-%M-%Y') 
					WHERE id = '$achievementId'
					AND defense_id = '$defenseId'
					AND pg_thesis_id = '$thesisId'
					AND pg_proposal_id = '$proposalId'
					AND reference_no = '$referenceNo'
					AND student_matrix_no = '$user_id'
					AND pg_chapter_id = '$chapterId[$k]'
					AND pg_subchapter_id = '$subchapterId[$k]'";
					
					$dba->query($sql6_1);
				}
				
				
			}
			
			$sql6 = "UPDATE pg_defense_publication
			SET pg_defense_id = '$defenseId', reference_no = '$referenceNo', 
			add_status = 'CFM', modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE pg_defense_id IS NULL
			AND pg_thesis_id = '$thesisId'
			AND pg_proposal_id = '$proposalId'
			AND student_matrix_no = '$user_id'
			AND add_status = 'TMP'
			AND archived_status IS NULL";
			
			$dba->query($sql6);
			
			$sql7 = "UPDATE pg_defense_conference
			SET pg_defense_id = '$defenseId', reference_no = '$referenceNo', 
			add_status = 'CFM', modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE pg_defense_id IS NULL
			AND reference_no IS NULL
			AND pg_thesis_id = '$thesisId'
			AND pg_proposal_id = '$proposalId'
			AND student_matrix_no = '$user_id'
			AND add_status = 'TMP'
			AND archived_status IS NULL";
			
			$dba->query($sql7);
			
			$msg[] = "<div class=\"success\"><span>Your Progression Monitoring Report(Defense Proposal) has been saved successfully.</span></div>";
			
		}
		
	}
}

if(isset($_POST['btnSubmit']) && ($_POST['btnSubmit'] <> ""))
{
	$content_checkbox = $_POST['content_checkbox'];
	$defenseId = $_POST['id'];
	$oldDefenseId = $_POST['id'];
	$thesisId = $_POST['thesisId'];
	$proposalId = $_POST['proposalId'];
	$reportMonth = $_POST['reportMonth'];		
	$reportYear = $_POST['reportYear'];
	$meetingDate = $_POST['meetingDate'];
	$tmpStartTime = $_POST['startHour']." ".$_POST['selectStartPM'];
	$tmpEndTime = $_POST['endHour']." ".$_POST['selectEndPM'];
	$defenseStatus = $_POST['defenseStatus']; 
	$defenseDetailId = $_POST['defenseDetailId'];
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
	$expectedReport = $_POST['expectedReport'];	
	
	$msg = Array();
	$currentDate1 = date('d-M-Y');
	$currentDate2 = new DateTime($currentDate1);
	$currentDate3 = $currentDate2->format('d-M-Y');
	$currentDate4 = new DateTime($currentDate3);
	
	$defenseDate1 = date('d-M-Y', strtotime($_POST['defenseDate']));
	$defenseDate2 = new DateTime($defenseDate1);
	$defenseDate3 = $defenseDate2->format('d-M-Y');
	$defenseDate4 = new DateTime($defenseDate3);
	
	$plannedDate1 = date('d-M-Y', strtotime($_POST['plannedDate'.$val]));
	$plannedDate2 = new DateTime($plannedDate1);
	$plannedDate3 = $plannedDate2->format('d-M-Y');
	$plannedDate4 = new DateTime($plannedDate3);

	$completionDate1 = date('d-M-Y', strtotime($_POST['completionDate'.$val]));
	$completionDate2 = new DateTime($completionDate1);
	$completionDate3 = $completionDate2->format('d-M-Y');
	$completionDate4 = new DateTime($completionDate3);	
	
	if ($defenseDate4 < $currentDate4) $msg[] = "<div class=\"error\"><span>Defense Date cannot be earlier than Current Date.</span></div>";
	if ($plannedDate4 > $completionDate4) $msg[] = "<div class=\"error\"><span>Planned Date for $romanChapter $romanSubChapter cannot be later than Completion Date.</span></div>";		
	if ($plannedDate4 > $currentDate4) $msg[] = "<div class=\"error\"><span>Planned Date for $romanChapter $romanSubChapter cannot be later than Current Date.</span></div>";
	if ($completionDate4 > $currentDate4) $msg[] = "<div class=\"error\"><span>Completion Date for $romanChapter $romanSubChapter cannot be later than Current Date.</span></div>";	
	
	while (list ($key,$val) = @each ($content_checkbox)) {
		if ($subchapterNo[$val] != "") {
			$romanSubChapter = "Subchapter ".romanNumerals($subchapterNo[$val]);
		}
		else {
			$romanChapter = "Chapter ".romanNumerals($chapterNo[$val]);
		}
		
		if (empty($_POST['plannedDate'.$val])) $msg[] = "<div class=\"error\"><span>Please provide Planned Date for $romanChapter $romanSubChapter</span></div>";	
		if (empty($_POST['completionDate'.$val])) $msg[] = "<div class=\"error\"><span>Please provide Completion Date for $romanChapter $romanSubChapter</span></div>";
		if (empty($_POST['contentDescription'.$val])) $msg[] = "<div class=\"error\"><span>Please provide Description of Work for $romanChapter $romanSubChapter</span></div>";		
	}
	
	if(empty($msg)) 
	{
		if ($defenseStatus=="") {
			$defenseId = runnum('id','pg_defense');
			$referenceNo = "D".runnum2('reference_no','pg_defense');
			
			$sql1 = " INSERT INTO pg_defense
			(id, reference_no, defense_date, submit_date, student_matrix_no, pg_thesis_id, pg_proposal_id,
			status, submit_status, respond_status,
			insert_by, insert_date, modify_by, modify_date )
			VALUES ('$defenseId', '$referenceNo', STR_TO_DATE('$defenseDate','%d-%M-%Y'), '$curdatetime', '$user_id', '$thesisId', '$proposalId', 
			'IN1','INP', 'N', '$user_id', '$curdatetime', '$user_id', '$curdatetime')";
			
			$dba->query($sql1); 
			$newDefenseDetailIdArray = Array();
			while (list ($key,$val) = @each ($supervisorIdArray)) 
			{
				$newDefenseDetailId = runnum('id','pg_defense_detail');
				$newDefenseDetailIdArray[$key] = $newDefenseDetailId;
				$sql2 = " INSERT INTO pg_defense_detail
				(id, pg_defense_id, pg_employee_empid, status, submit_date,
				insert_by, insert_date, modify_by, modify_date )
				VALUES ('$newDefenseDetailId', '$defenseId', '$val', 'IN1', '$curdatetime',
				'$user_id', '$curdatetime', '$user_id', '$curdatetime')";
				
				$dba->query($sql2);
			}

			for ($k=0; $k<$totalNoOfContent; $k++)
			{	
				$contentDescriptionArray[$k] = $_POST["contentDescription".$k];
				$plannedDate[$k] = $_POST['plannedDate'.$k];
				$completionDate[$k] = $_POST['completionDate'.$k];

				$newAchievementId = runnum('id','pg_achievement');

				$sql3 = " INSERT INTO pg_achievement
				(id, defense_id, reference_no, pg_thesis_id, pg_proposal_id, student_matrix_no, pg_chapter_id, 
				pg_subchapter_id, discussed_status, 
				planned_date, completion_date, content_discussion, 
				insert_by, insert_date, modify_by, modify_date )
				VALUES ('$newAchievementId', '$defenseId', '$referenceNo', '$thesisId', '$proposalId', '$user_id', '$chapterId[$k]', 
				'$subchapterId[$k]',  null, STR_TO_DATE('$plannedDate[$k]','%d-%M-%Y'), STR_TO_DATE('$completionDate[$k]','%d-%M-%Y'), 
				'$contentDescriptionArray[$k]', '$user_id', '$curdatetime', '$user_id', '$curdatetime')";
				
				$dba->query($sql3);

			}

			$sql6 = "UPDATE pg_defense_publication
			SET pg_defense_id = '$defenseId', reference_no = '$referenceNo', 
			add_status = 'CFM', modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE pg_defense_id IS NULL
			AND reference_no IS NULL
			AND pg_thesis_id = '$thesisId'
			AND pg_proposal_id = '$proposalId'
			AND student_matrix_no = '$user_id'
			AND add_status = 'TMP'
			AND archived_status IS NULL";
			
			$dba->query($sql6);	
			
			$sql7 = "UPDATE pg_defense_conference
			SET pg_defense_id = '$defenseId', reference_no = '$referenceNo', 
			add_status = 'CFM', modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE pg_defense_id IS NULL
			AND reference_no IS NULL
			AND pg_thesis_id = '$thesisId'
			AND pg_proposal_id = '$proposalId'
			AND student_matrix_no = '$user_id'
			AND add_status = 'TMP'
			AND archived_status IS NULL";
			
			$dba->query($sql7);	

			$msg[] = "<div class=\"success\"><span>Your Progression Monitoring Report(Defense Proposal) has been submitted successfully.</span></div>";		
		}
		else if ($defenseStatus=="SAV") {//SAV

			$sql5 = " UPDATE pg_defense
			SET defense_date = STR_TO_DATE('$defenseDate','%d-%M-%Y'), submit_date = '$curdatetime', 
			status = 'IN1', submit_status = 'INP', respond_status = 'N',
			modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE id = '$defenseId'
			AND student_matrix_no = '$user_id'
			AND pg_thesis_id = '$thesisId'
			AND pg_proposal_id = '$proposalId'";

			$dba->query($sql5);
			
			while (list ($key,$val) = @each ($supervisorIdArray)) 
			{
				$sql5_4 = "SELECT id, pg_defense_id, pg_employee_empid, status, responded_status, responded_date, 
				submit_date, insert_by, insert_date, modify_by, modify_date
				FROM pg_defense_detail
				WHERE pg_defense_id = '$defenseId'
				AND pg_employee_empid = '$val'
				AND archived_status is NULL";
				
				$result_5_4 = $dba->query($sql5_4);
				$dba->next_record();
				
				$pg_defense_detail_id = $dba->f('id');
				//$pg_defense_id = $dba->f('pg_defense_id'); 
				$employeeId = $dba->f('pg_employee_empid');
				$status = $dba->f('status');
				$responded_status = $dba->f('responded_status'); 
				//$submitDate = $dba->f('submit_date'); 
				$responded_date = $dba->f('responded_date'); 
				$insert_by = $dba->f('insert_by'); 
				$insert_date = $dba->f('insert_date'); 
				$modify_by = $dba->f('modify_by'); 
				$modify_date = $dba->f('modify_date'); 
				
				$defenseDetailIdArray[$key] = $pg_defense_detail_id;
				$row_cnt_5_4 = mysql_num_rows($result_5_4);
				if ($row_cnt_5_4 > 0) {					
					if ($status != 'APP') {
						$sql5_5 = " UPDATE pg_defense_detail
						SET status = 'IN1', submit_date = '$curdatetime',
						modify_by = '$user_id', modify_date = '$curdatetime'			
						WHERE pg_defense_id = '$defenseId'
						AND pg_employee_empid = '$val'
						AND archived_status IS NULL ";
						
						$dba->query($sql5_5);
					}
				}
				else {
					$newDefenseDetailId = runnum('id','pg_defense_detail');
					$newDefenseDetailIdArray[$key] = $newDefenseDetailId;
					
					$sql2 = " INSERT INTO pg_defense_detail
					(id, pg_defense_id, pg_employee_empid, status, submit_date, 
					insert_by, insert_date, modify_by, modify_date )
					VALUES ('$newDefenseDetailId', '$defenseId', '$val', 'IN1', 
					'$submitDate', '$user_id', '$curdatetime', '$user_id', '$curdatetime')";
					
					$dba->query($sql2);
				}					
			}
			$achievementIdArray = Array();
			for ($k=0; $k<$totalNoOfContent; $k++)
			{	

				$sql6 = "SELECT id 
				FROM pg_achievement
				WHERE defense_id = '$defenseId'
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
				$achievementId = $dba->f('id');
				
				$contentDescriptionArray[$k] = $_POST["contentDescription".$k];
				
				$row_cnt_sql6 = mysql_num_rows($result_sql6);
				
				$newAchievementId = runnum('id','pg_achievement');
				
				
				if ($row_cnt_sql6 == 0) {
					$achievementIdArray[$k] = $newAchievementId;
					$sql3 = " INSERT INTO pg_achievement
					(id, defense_id, reference_no, pg_thesis_id, pg_proposal_id, student_matrix_no, pg_chapter_id, pg_subchapter_id, discussed_status, planned_date, completion_date,
					content_discussion, insert_by, insert_date, modify_by, modify_date )
					VALUES ('$newAchievementId', '$defenseId', '$referenceNo', '$thesisId', '$proposalId', '$user_id', '$chapterId[$k]', '$subchapterId[$k]',   
					null, STR_TO_DATE('$plannedDate[$k]','%d-%M-%Y'), STR_TO_DATE('$completionDate[$k]','%d-%M-%Y'),'$contentDescriptionArray[$k]', '$user_id', '$curdatetime', '$user_id', '$curdatetime')";
					
					$dba->query($sql3);
				}
				else {
					$achievementIdArray[$k] = $achievementId;
					$sql6_1 = "UPDATE pg_achievement
					SET discussed_status = null, planned_date = STR_TO_DATE('$plannedDate[$k]','%d-%M-%Y'), 
					completion_date = STR_TO_DATE('$completionDate[$k]','%d-%M-%Y') 
					WHERE id = '$achievementId'
					AND defense_id = '$defenseId'
					AND pg_thesis_id = '$thesisId'
					AND pg_proposal_id = '$proposalId'
					AND reference_no = '$referenceNo'
					AND student_matrix_no = '$user_id'
					AND pg_chapter_id = '$chapterId[$k]'
					AND pg_subchapter_id = '$subchapterId[$k]'";
					
					$dba->query($sql6_1);
				}				
			}
				
			$sql6 = "UPDATE pg_defense_publication
			SET pg_defense_id = '$defenseId', reference_no = '$referenceNo', 
			add_status = 'CFM', modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE pg_defense_id IS NULL
			AND pg_thesis_id = '$thesisId'
			AND pg_proposal_id = '$proposalId'
			AND student_matrix_no = '$user_id'
			AND add_status = 'TMP'
			AND archived_status IS NULL";
			
			$dba->query($sql6);
			
			$sql7 = "UPDATE pg_defense_conference
			SET pg_defense_id = '$defenseId', reference_no = '$referenceNo', 
			add_status = 'CFM', modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE pg_defense_id IS NULL
			AND pg_thesis_id = '$thesisId'
			AND pg_proposal_id = '$proposalId'
			AND student_matrix_no = '$user_id'
			AND add_status = 'TMP'
			AND archived_status IS NULL";
			
			$dba->query($sql7);
			
			$msg[] = "<div class=\"success\"><span>Your Progression Monitoring Report(Defense Proposal) has been submitted successfully.</span></div>";
			
		}		
	}
}

if ($_POST['referenceNo'] != '') $referenceNo = $_POST['referenceNo'];

$sql2 = "SELECT a.id, a.reference_no, DATE_FORMAT(a.submit_date, '%d-%b-%Y') as submit_date, 
DATE_FORMAT(a.defense_date, '%d-%b-%Y') as defense_date, a.student_matrix_no, 
a.pg_thesis_id, a.pg_proposal_id, a.status as defense_status, 
a.insert_by, a.insert_date, a.modify_by, a.modify_date,	c1.description as defense_desc,
d.status as defense_detail_status, c2.description as defense_detail_desc, d.id as defense_detail_id, a.respond_status
FROM pg_defense a
LEFT JOIN ref_proposal_status c1 ON (c1.id = a.status)
LEFT JOIN pg_defense_detail d ON (d.pg_defense_id = a.id)
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
$defenseStatus=$dba->f('defense_status');
$defenseDesc=$dba->f('defense_desc');
$defenseDetailId=$dba->f('defense_detail_id');
$defenseDetailStatus=$dba->f('defense_detail_status');
$defenseDetailDesc=$dba->f('defense_detail_desc');
$submitDate=$dba->f('submit_date');
$defenseDate=$dba->f('defense_date');
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
	<script type="text/javascript">
	function newPublication(pid, tid, pgid, ref) 
	{
		var ask = window.confirm("Ensure your report has been saved before proceed or otherwise the last change will be discarded.\nClick OK to proceed or CANCEL to stay on the same page.");
		if (ask) 
		{
			document.location.href = "../defense/defense_publication.php?pid=" + pid + "&tid=" + tid + "&pgid=" + pgid + "&ref=" + ref;
			return true;
		}
		return false;
	}
	</script>
	<script type="text/javascript">
	function newConference(pid, tid, pgid, ref) 
	{
		var ask = window.confirm("Ensure your report has been saved before proceed or otherwise the last change will be discarded.\nClick OK to proceed or CANCEL to stay on the same page.");
		if (ask) 
		{
			document.location.href = "../defense/defense_conference.php?pid=" + pid + "&tid=" + tid + "&pgid=" + pgid + "&ref=" + ref;
			return true;
		}
		return false;
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
	if ($defenseStatus == "" || $defenseStatus == "SAV") {?>
		<table border="0">
			<tr>
			<td><h3><strong>Defense Proposal Details </strong><h3></td>
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
				<?if ($defenseDesc=="") $defenseDesc='New';?>
				<td><strong><?=$defenseDesc?></strong></td>
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
				<input type="hidden" name="defenseDetailId" id="defenseDetailId" value="<?=$defenseDetailId; ?>">
				<input type="hidden" name="defenseStatus" id="defenseStatus" value="<?=$defenseStatus; ?>">
				<input type="hidden" name="referenceNo" id="referenceNo" value="<?=$referenceNo; ?>">
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
			<?$jscript3 = "";?>
			<tr>
				<td>Defense Date <span style="color:#FF0000"> *</span></td>
				<td>:</td>
				<?if ($_POST['defenseDate'] != "") $defenseDate = $_POST['defenseDate'];?>
				<td><input type="text" name="defenseDate" id="defenseDate" size="10" value="<?=$defenseDate?>" readonly=""/></td>
				<?	$jscript3 .= "\n" . '$( "#defenseDate" ).datepicker({
												changeMonth: true,
												changeYear: true,
												yearRange: \'-100:+0\',
												dateFormat: \'dd-M-yy\'
											});';						 
				?>
				
			</tr>   			
		</table>	
		<?
		$sqlPublication="SELECT id
		FROM  pg_defense_publication  
		WHERE (pg_defense_id = '$id' OR pg_defense_id = '' OR pg_defense_id IS NULL)
		AND pg_proposal_id='$proposalId'
		AND pg_thesis_id = '$thesisId'
		AND (reference_no = '$referenceNo' OR reference_no IS NULL)
		AND student_matrix_no = '$user_id'";			

		$result_sqlPublication = $db->query($sqlPublication); 
		$row_cnt_sqlPublication = mysql_num_rows($result_sqlPublication);
		if ($row_cnt_sqlPublication == '0')
		{
			$row_cnt_sqlPublication1 = '';
		}
		else
		{
			$row_cnt_sqlPublication1 = "(".$row_cnt_sqlPublication.")";
		}			
		?>
		<?
		$sqlConference="SELECT id
		FROM  pg_defense_conference  
		WHERE (pg_defense_id = '$id' OR pg_defense_id = '' OR pg_defense_id IS NULL)
		AND pg_proposal_id='$proposalId'
		AND pg_thesis_id = '$thesisId'
		AND (reference_no = '$referenceNo' OR reference_no IS NULL)
		AND student_matrix_no = '$user_id'";			

		$result_sqlConference = $db->query($sqlConference); 
		$row_cnt_sqlConference = mysql_num_rows($result_sqlConference);
		if ($row_cnt_sqlConference == '0')
		{
			$row_cnt_sqlConference1 = '';
		}
		else
		{
			$row_cnt_sqlConference1 = "(".$row_cnt_sqlConference.")";
		}			
		?>
		<br/>
		<table>
			<tr>
				<td><button type = "button" name="btnDiscussionDetail" onclick="return newPublication('<?=$proposalId?>','<?=$thesisId?>','<?=$id?>','<?=$referenceNo?>')">
				Publication <FONT COLOR="#FF0000"><sup><?=$row_cnt_sqlPublication1?></sup></FONT></button></td>		
				<td><button type = "button" name="btnDiscussionDetail" onclick="return newConference('<?=$proposalId?>','<?=$thesisId?>','<?=$id?>','<?=$referenceNo?>')">
				Conference <FONT COLOR="#FF0000"><sup><?=$row_cnt_sqlConference1?></sup></FONT></button></td>
				<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='defense_proposal.php';" /></td>		
			</tr>
		</table>
		<br/>

		<h3><strong>List of Supervisor/Co-Supervisor</strong></h3>
		<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="100%" class="thetable">			
				<tr>
					<th width="5%">No</th>					
					<th width="15%">Role / Acceptance Date</th>
					<th width="15%">Staff ID</th>
					<th width="25%">Name</th>
					<th width="5%">Faculty</th>
					<th width="5%">View Feedback</th>
					<th width="15%">Status</th>
					<th width="15%">Last Update</th>
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
							$sql12 = "SELECT b.status as defense_detail_status, c.description as defense_detail_desc,
							DATE_FORMAT(b.responded_date,'%d-%b-%Y %h:%i %p') AS responded_date
							FROM pg_defense a
							LEFT JOIN pg_defense_detail b ON (b.pg_defense_id = a.id)
							LEFT JOIN ref_proposal_status c ON (c.id = b.status)
							WHERE a.student_matrix_no = '$user_id'
							AND a.reference_no = '$referenceNo'
							AND b.pg_employee_empid = '$employeeId'
							AND a.pg_thesis_id = '$thesisId'
							AND a.pg_proposal_id = '$proposalId'
							AND a.archived_status is null
							AND b.archived_status is NULL";
							
							$result12 = $dbg->query($sql12); 
							$dbg->next_record();
							$row_cnt12 = mysql_num_rows($result12);
							$defenseDetailStatus=$dbg->f('defense_detail_status');
							$defenseDetailDesc=$dbg->f('defense_detail_desc');
							$respondedDate=$dbg->f('responded_date');
							
							if ($row_cnt12>0) {
							?>
							
							<td><a href="defense_view_feedback.php?tid=<? echo $thesisId;?>&pid=<? echo $proposalId;?>&eid=<? echo $employeeId;?>&id=<? echo $id;?>&mn=<?=$user_id?>" name="thesisId" value="<?=$thesisId?>" title="View feedback"><img src="../images/view.jpg" width="45" height="30" style="border:0px;" title="View feedback"></a></td>	
							<?}
							else {
								?>
								<td></td>
								<?
							}
							if ($defenseDetailStatus == '') {
							?>
								<td align="left"><label>Expecting Defense Proposal</label></td>
							<?}
							else if ($defenseDetailStatus == 'SV1') {
							?>
								<td align="left"><label><span style="color:#FF0000"><?=$defenseDetailDesc;?></span></label></td>
							<?}
							else {
								?>								
								<td align="left"><label><?=$defenseDetailDesc;?></label></td>
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
									<span style="color:#FF0000"> Make sure your Supervisor has been assigned first before submit the Monthly Defense report</span></td>
						</tr>
					</table>
					<?
				}?>	
				</table>
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
				<td><label>To be completed by candidate:</label><br/><h3>Work Achievement</strong> <span style="color:#FF0000"> *</span><h3></td>
			</tr>
		</table>
			<?
			$jscript1 = "";
			$jscript2 = "";
			for ($i=0; $i<$no; $i++){
			?>
			<table>
			
				<input type="hidden" name="chapterId[]" id="chapterId" value="<?=$chapterId[$i];?>">
				<input type="hidden" name="subchapterId[]" id="subchapterId" value="<?=$subchapterId[$i];?>">
				<?
					$sql_achievement = " SELECT discussed_status, 
					DATE_FORMAT(planned_date,'%d-%b-%Y') AS planned_date,
					DATE_FORMAT(completion_date,'%d-%b-%Y') AS completion_date, 
					content_discussion
					FROM pg_achievement 
					WHERE pg_thesis_id = '$thesisId'
					AND pg_proposal_id = '$proposalId'
					AND reference_no = '$referenceNo'
					AND student_matrix_no = '$user_id'
					AND pg_chapter_id = '$chapterId[$i]'
					AND pg_subchapter_id = '$subchapterId[$i]'
					AND archived_status is null";
										
					$result_sql_achievement = $dbb->query($sql_achievement); 
					$dbb->next_record();
					$discussedStatus = $dbb->f('discussed_status');
					$plannedDate = $dbb->f('planned_date');
					$completionDate = $dbb->f('completion_date');
					$contentDiscussion = $dbb->f('content_discussion');
				?>
				<tr>
					<input type="hidden" name="content_checkbox[]" type="checkbox" id="content_checkbox" value="<?=$i;?>">
					<td><strong><label>Chapter <?=romanNumerals($chapterNo[$i]);?>. <?=$chapterDesc[$i];?></label>			
					<input type="hidden" name="chapterNo[]" id="chapterNo" value="<?=$chapterNo[$i];?>">

					<?if ($subchapterNo[$i] != "") {?>
							<label>, Subchapter <?=romanNumerals($subchapterNo[$i]);?>. <?=$subchapterDesc[$i];?></label>
					<?}?>					
					<strong></td>
				</tr>
				<table width="31%">
				<tr>
					<td width="25%">Planned Date</td>
					<td width="1%">:</td>
					<?if ($_POST['plannedDate'.$i]!="") $plannedDate = $_POST['plannedDate'.$i];?>
					<td width="5%"><input type="text" name="plannedDate<?=$i?>" id="plannedDate<?=$i?>" size="10" value="<?=$plannedDate?>" readonly=""/></td>
					<?	$jscript1 .= "\n" . '$( "#plannedDate' . $i . '" ).datepicker({
													changeMonth: true,
													changeYear: true,
													yearRange: \'-100:+0\',
													dateFormat: \'dd-M-yy\'
												});';						 
					?>
					
				</tr>
				<tr>
					<td width="25%">Completion Date</td>
					<td width="1%">:</td>
					<?if ($_POST['completionDate'.$i]!="") $completionDate = $_POST['completionDate'.$i];?>
					<td width="5%"><input type="text" name="completionDate<?=$i?>" id="completionDate<?=$i?>" size="10" value="<?=$completionDate?>" readonly=""/></td>
					<?	$jscript2 .= "\n" . '$( "#completionDate' . $i . '" ).datepicker({
													changeMonth: true,
													changeYear: true,
													yearRange: \'-100:+0\',
													dateFormat: \'dd-M-yy\'
												});';						 
					?>
				</tr>
				</table>
				<table>
					<tr>
						<td><label>Description of Work</label></td>
					</tr>
					<?if ($_POST['contentDescription'.$i]!="") $contentDiscussion = $_POST['contentDescription'.$i];?>
					<tr>
						<td><textarea name="contentDescription<?=$i?>" id="contentDescription<?=$i?>" class="ckeditor" ><?=$contentDiscussion?></textarea><br/></td>
					</tr>
				</table>
				<input type="hidden" name="subchapterNo[]" id="subchapterNo" value="<?=$subchapterNo[$i];?>">
			</table>
			<?
			}
			?>	
			
		<input type="hidden" name="totalNoOfContent" id="totalNoOfContent" value="<?=$i; ?>">
			
		
		<table>
			<tr>
				<td><label><span style="color:#FF0000">Notes:</span></td>
			</tr>
			<tr>
				<td></span>1. Field marks with (<span style="color:#FF0000">*</span>) is compulsory.</td>
			</tr>
			<tr>
				<td>2. If you proceed to save/resubmit your defense report, the report which are still pending at your Supervisor / Co-Supervisor (if any) will be replaced.</td>
			</tr>
		</table>
		<table>
			<tr>
				<td><input type="submit" name="btnSave" value="Save"/></td>
				<td><input type="submit" name="btnSubmit" value="Submit" onClick="return respConfirm()" /></td>
				<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='defense_proposal.php';" /></td>		
			</tr>
		</table>

		
	<?} 
	else //$defenseStatus == 'IN1'
	{?>
		<table>
			<tr>		
				<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='defense_proposal.php';" /></td>		
			</tr>
		</table>
	<?}?>
		</form>
		<script>
			<?=$jscript1;?>
			<?=$jscript2;?>
			<?=$jscript3;?>
		</script>
	</body>
</html>



