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

if(isset($_POST['btnRecommendation']) && ($_POST['btnRecommendation'] <> "")) {
	$no=1;
	if (sizeof($_POST['defense_checkbox'])>0) {
		
		$curdatetime = date("Y-m-d H:i:s");
		while (list ($key,$val) = @each ($_POST['defense_checkbox'])) 
		{
			if ($_POST['roleStatusArray'.$val]=="PRI") {
				$thesisId = $_POST['thesisIdArray'.$val];
				$studentMatrixNo = $_POST['studentMatrixNoArray'.$val];
				$defenseSchedule = $_POST['defenseSchedule'.$val];
				
				$sql1 = "SELECT DATE_FORMAT(defense_date,'%d-%b-%Y') as defense_date
				FROM pg_calendar
				WHERE id = '$defenseSchedule'
				/*AND recomm_status = 'REC'*/
				AND status = 'A'";
				
				$result_sql1 = $db->query($sql1); 
				$db->next_record();
				$defenseDate = $db->f('defense_date');
				
				$defenseDateFirst1 = date('d-M-Y', strtotime($defenseDate));
				$defenseDateFirst2 = new DateTime($defenseDateFirst1);
				$defenseDateFirst3 = $defenseDateFirst2->format('d-M-Y');
				$defenseDateFirst4 = new DateTime($defenseDateFirst3);
				
				$currentDateSecnd0 = date('d-M-Y');
				$currentDateSecnd1 = date('d-M-Y', strtotime($currentDateSecnd0));
				$currentDateSecnd2 = new DateTime($currentDateSecnd1);
				$currentDateSecnd3 = $currentDateSecnd2->format('d-M-Y');
				$currentDateSecnd4 = new DateTime($currentDateSecnd3);

				if ($defenseDateFirst4 < $currentDateSecnd4) {
					$msg[] = "<div class=\"error\"><span>The selected schedule date is earlier than current date. Please select the date which is later than current date.</span></div>";
				}
				else {

					$sql2 = "UPDATE pg_calendar
					SET recomm_status = 'REC', 
					recomm_by = '$user_id',
					recomm_date = '$curdatetime',
					modify_by = '$user_id', modify_date = '$curdatetime'
					WHERE id = '$defenseSchedule'
					AND status = 'A'";
					
					$dba->query($sql2); 
					
					$msg[] = "<div class=\"success\"><span>The selected Evaluation Schedule has been updated successfully.</span></div>";
				}	

				
			}
			else {
				$msg[] = "<div class=\"error\"><span>The selected Evaluation Schedule (record no. $val) cannot be updated. Only Main Supervisor can recommend the date.</span></div>";
			}
		}
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please tick which Evaluation Schedule to be updated!</span></div>";
	}
}

if(isset($_POST['btnCancelDefence']) && ($_POST['btnCancelDefence'] <> "")) {
	$no=1;
	if (sizeof($_POST['defense_checkbox'])>0) {
		
		$no1=1;
		while (list ($key,$val) = @each ($_POST['defense_checkbox'])) 
		{
			$thesisId = $_POST['thesisIdArray1'.$val];
			$studentMatrixNo = $_POST['studentMatrixNoArray1'.$val];
			$recommendedId = $_POST['recommendedIdArray1'.$val];
			$curdatetime = date("Y-m-d H:i:s");
			
			
			$sql1 = "SELECT role_status
			FROM pg_supervisor
			WHERE pg_thesis_id = '$thesisId'
			AND pg_student_matrix_no = '$studentMatrixNo'
			AND pg_employee_empid = '$user_id'
			AND acceptance_status = 'ACC'
			AND status = 'A'";
			
			$result_sql1 = $db->query($sql1);
			$db->next_record();
			
			$roleStatus = $db->f('role_status');
			$no1 = $val + 1;
			if ($roleStatus == "PRI") {
				$sql2 = "UPDATE pg_calendar
				SET recomm_status = 'CAN', 
				recomm_by = '$user_id',
				recomm_date = '$curdatetime',
				modify_by = '$user_id', modify_date = '$curdatetime'
				WHERE id = '$recommendedId'
				AND status = 'A'";

				$dba->query($sql2); 
				
				$msg[] = "<div class=\"success\"><span>The selected Evaluation Schedule (record no. $no1)  has been cancelled successfully.</span></div>";				
			}
			else {
				$msg[] = "<div class=\"error\"><span>The selected Evaluation Schedule (record no. $no1) was updated unsuccessfully. Only Main Supervisor can recommend the proposal defence date.</span></div>";
			}
			
		}
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please tick which Evaluation Schedule to be cancelled!</span></div>";
	}
}

if(isset($_POST['btnSearch']) && ($_POST['btnSearch'] <> "")) {
	$searchThesisId = $_POST['searchThesisId'];
	$searchStudent = $_POST['searchStudent'];
	$searchStudentName = $_POST['searchStudentName'];
	$msg = Array();
	
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
		$tmpSearchStudent = " AND a.pg_student_matrix_no = '$searchStudent'";
	}
	else 
	{
		$tmpSearchStudent="";
	}

	if ($searchStudentName!="") 
	{
		$tmpSearchStudentName = " AND name LIKE '%$searchStudentName%'";
	}
	else 
	{
		$tmpSearchStudentName="";
	}
	
	$sql = " SELECT a.pg_thesis_id, g.id as proposal_id, a.pg_student_matrix_no, a.ref_supervisor_type_id, 
	d.description as supervisor_type_desc, DATE_FORMAT(a.acceptance_date,'%d-%b-%Y %h:%i %p') as acceptance_date,
	a.role_status, h.description as role_status_desc
	FROM pg_supervisor a 
	LEFT JOIN ref_supervisor_type d ON (d.id = a.ref_supervisor_type_id) 
	LEFT JOIN pg_thesis f ON (f.student_matrix_no = a.pg_student_matrix_no)
	LEFT JOIN pg_proposal g ON (g.pg_thesis_id = f.id)
	LEFT JOIN ref_role_status h ON (h.id = a.role_status)
	WHERE a.pg_employee_empid = '$user_id'"
	.$tmpSearchThesisId." "
	.$tmpSearchStudent." 
	AND a.acceptance_status = 'ACC'
	AND a.ref_supervisor_type_id in ('SV','CS','XS')
	AND g.verified_status in ('APP','AWC')
	AND g.status in ('APP','APC')
	AND g.archived_status IS NULL
	AND a.role_status = 'PRI'
	AND a.status = 'A'
	ORDER BY d.seq, a.ref_supervisor_type_id";

	$result_sql = $dbg->query($sql); 
	$dbg->next_record();
	$row_cnt = mysql_num_rows($result_sql);

	$thesisIdArray = Array();
	$proposalIdArray = Array();
	$studentMatrixNoArray = Array();
	$studentNameArray = Array();
	$supervisorTypeArray = Array();
	$supervisorTypeArray = Array();
	$acceptanceDateArray = Array();
	$roleStatusArray = Array();
	$roleStatusDescArray = Array();
	$no=0;
	$no1=0;

	if ($row_cnt > 0) {
		do {
			$studentMatrixNoArray[$no]=$dbg->f('pg_student_matrix_no');
			$thesisIdArray[$no]=$dbg->f('pg_thesis_id');
			$proposalIdArray[$no]=$dbg->f('proposal_id');
			$supervisorTypeIdArray[$no]=$dbg->f('ref_supervisor_type_id');
			$supervisorTypeDescArray[$no]=$dbg->f('supervisor_type_desc');
			$acceptanceDateArray[$no]=$dbg->f('acceptance_date');
			$roleStatusArray[$no]=$dbg->f('role_status');
			$roleStatusDescArray[$no]=$dbg->f('role_status_desc');
			$no++;
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
				$roleStatusArray[$no1]=$roleStatusArray[$i];
				$roleStatusDescArray[$no1]=$roleStatusDescArray[$i];
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

	$sql = " SELECT a.pg_thesis_id, g.id as proposal_id, a.pg_student_matrix_no, a.ref_supervisor_type_id, 
	d.description as supervisor_type_desc, DATE_FORMAT(a.acceptance_date,'%d-%b-%Y %h:%i %p') as acceptance_date,
	a.role_status, h.description as role_status_desc
	FROM pg_supervisor a 
	LEFT JOIN ref_supervisor_type d ON (d.id = a.ref_supervisor_type_id) 
	LEFT JOIN pg_thesis f ON (f.student_matrix_no = a.pg_student_matrix_no)
	LEFT JOIN pg_proposal g ON (g.pg_thesis_id = f.id)
	LEFT JOIN ref_role_status h ON (h.id = a.role_status)
	WHERE a.pg_employee_empid = '$user_id'
	AND a.acceptance_status = 'ACC'
	AND a.ref_supervisor_type_id in ('SV','CS','XS')
	AND g.verified_status in ('APP','AWC')
	AND g.status in ('APP','APC')
	AND g.archived_status IS NULL
	AND a.role_status = 'PRI'
	AND a.status = 'A'
	ORDER BY d.seq, a.ref_supervisor_type_id";

	$result_sql = $dbg->query($sql); 
	$dbg->next_record();
	$row_cnt = mysql_num_rows($result_sql);

	$thesisIdArray = Array();
	$proposalIdArray = Array();
	$studentMatrixNoArray = Array();
	$studentNameArray = Array();
	$supervisorTypeArray = Array();
	$supervisorTypeArray = Array();
	$acceptanceDateArray = Array();
	$roleStatusArray = Array();
	$roleStatusDescArray = Array();
	$no=0;
	$no1=0;

	if ($row_cnt > 0) {
		do {
			$studentMatrixNoArray[$no]=$dbg->f('pg_student_matrix_no');
			$thesisIdArray[$no]=$dbg->f('pg_thesis_id');
			$proposalIdArray[$no]=$dbg->f('proposal_id');
			$supervisorTypeIdArray[$no]=$dbg->f('ref_supervisor_type_id');
			$supervisorTypeDescArray[$no]=$dbg->f('supervisor_type_desc');
			$acceptanceDateArray[$no]=$dbg->f('acceptance_date');
			$roleStatusArray[$no]=$dbg->f('role_status');
			$roleStatusDescArray[$no]=$dbg->f('role_status_desc');
			$no++;
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
	<legend><strong>List of Student</strong></legend>
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

			</tr>
			<tr>
				<td>Student Name</td>
				<td>:</td>
				<td><input type="text" name="searchStudentName" size="30" id="searchStudentName" value="<?=$searchStudentName;?>"/></td>
				<td><input type="submit" name="btnSearch" value="Search" /> Note: If no entry is provided, it will search all.</td>
			</tr>
		</table>
		<table>
			<tr>							
				<td>Notes:<br/>
				1. Only Main Supervisor can recommend the date.<br/>
				2. Please recommend at least 1 date to enable your supervisee to submit the Defence Proposal, Work Completion or VIVA report.</td>
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
		<?} else {
			?>
			<div id = "tabledisplay" style="overflow:auto; height:200px;">
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
		<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="88%" class="thetable">			
			<tr>
				<th width="5%">Tick</th>
				<th width="5%">No</th>					
				<th width="18%">Acceptance Date</th>
				<th width="10%" align="left">Thesis / Project ID</th>
				<th width="25%" align="left">Student Name</th>
				<th width="25%">Next Evaluation Schedule <br/>(recommended within <?=$defenseDurationParam?> days)</th>

			</tr>
			<?
			if ($row_cnt > 0 ) {?>	
				<?
				$no=0;
				for ($j=0; $j<$row_cnt; $j++){
					$sql3 = "SELECT a.id, a.defense_date, a.defense_date, DATE_FORMAT(a.defense_date,'%d-%b-%Y') as defense_date1, 
					DATE_FORMAT(a.defense_stime,'%h:%i%p') as defense_stime,
					DATE_FORMAT(a.defense_etime,'%h:%i%p') as defense_etime, a.venue, a.ref_session_type_id, 
					b.description as ref_session_type_desc
					FROM pg_calendar a
					LEFT JOIN ref_session_type b ON (b.id = a.ref_session_type_id)
					WHERE a.student_matrix_no = '$studentMatrixNoArray[$j]'
					AND a.thesis_id = '$thesisIdArray[$j]'
					AND a.status = 'A'
					AND (a.recomm_status IN ('CAN', '') OR a.recomm_status IS NULL) 
					ORDER BY b.seq, a.defense_date ASC";
					
					$result_sql3 = $dba->query($sql3); 
					$dba->next_record();
					$row_cnt_sql3 = mysql_num_rows($result_sql3);
					
					$recommendedIdArray = Array(); 
					$defenseDateArray = Array(); 
					$defenseSTimeArray = Array();
					$defenseETimeArray = Array();
					$venueArray = Array();
					$sessionTypeArray = Array();
					$sessionTypeDescArray = Array();

					$k=0;
					do {
						$recommendedIdArray[$k] = $dba->f('id');
						$defenseDateArray[$k] = $dba->f('defense_date1');
						$defenseSTimeArray[$k] = $dba->f('defense_stime');
						$defenseETimeArray[$k] = $dba->f('defense_etime');
						$venueArray[$k] = $dba->f('venue');
						$sessionTypeArray[$k] = $dba->f('ref_session_type_id');
						$sessionTypeDescArray[$k] = $dba->f('ref_session_type_desc');
						$k++;
					} while ($dba->next_record());
					if($j % 2) $color ="first-row"; else $color = "second-row";
					?>
					<tr class="<?=$color?>">
						<?if ($row_cnt_sql3 > 0 ) {
							?>
							<td align="center"><input name="defense_checkbox[]" type="checkbox" value="<?=$j;?>"/></td>
							<?
						}
						else {
							?>
							<td align="center"><input name="defense_checkbox[]" type="checkbox" value="<?=$j;?>" disabled="disabled"/></td>
							<?
						}?>
						
						<td align="center"><?=$no+1;?>.</td>
						<input type="hidden" name="roleStatusArray<?=$j?>" id="roleStatusArray<?=$j?>" value="<?=$roleStatusArray[$j]; ?>">
						<input type="hidden" name="thesisIdArray<?=$j?>" id="thesisIdArray<?=$j?>" value="<?=$thesisIdArray[$j]; ?>">
						<input type="hidden" name="studentMatrixNoArray<?=$j?>" id="studentMatrixNoArray<?=$j?>" value="<?=$studentMatrixNoArray[$j]; ?>">
						<td align="left"><label><?=$acceptanceDateArray[$j]?></label></td>
						<td align="left"><label><?=$thesisIdArray[$j]?></label></td>
						<td align="left"><label><?=$studentNameArray[$j]?><br><?=$studentMatrixNoArray[$j]?></label></td>
						
						<?
						
						if ($row_cnt_sql3 > 0) {
							?>
							<td>
								<select name="defenseSchedule<?=$j?>" id="defenseSchedule<?=$j?>">
								<?
								for ($l=0;$l<$row_cnt_sql3;$l++) {
									?>
									<option value="<?=$recommendedIdArray[$l]?>"><label><?=$sessionTypeDescArray[$l]?> - <?=$defenseDateArray[$l]?>, <?=$defenseSTimeArray[$l]?> to <?=$defenseETimeArray[$l]?></label></option>
									<?
								}
								?>
								</select>
								</td>
							<?
						}
						else {
							?>
							<td><label>The date is yet to be defined OR already selected as in the list below.</label></td>
							<?
						}
						?>
					</tr>
					<?
				$no++;	
				}
				?>
				<table>
					<tr>
						<td><input type="submit" name="btnRecommendation" value="Recommend Evaluation Schedule"></td>
					</tr>
				</table>
				
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
		<table>
			<tr>
				<td><strong><label>List of Recommended Evaluation Schedule</strong></label></td>
			</tr>
		</table>
		<?
		$sql4 = "(SELECT a.id, a.student_matrix_no, a.thesis_id,
		a.defense_date, DATE_FORMAT(a.defense_date,'%d-%b-%Y') as defense_date1, 
		DATE_FORMAT(a.defense_stime,'%h:%i%p') as defense_stime,
		DATE_FORMAT(a.defense_etime,'%h:%i%p') as defense_etime, a.venue, a.recomm_by, a.ref_session_type_id, 
		b.description as ref_session_type_desc, d.status as evaluation_status
		FROM pg_calendar a
		LEFT JOIN ref_session_type b ON (b.id = a.ref_session_type_id)
		LEFT JOIN pg_defense c ON (c.pg_calendar_id = a.id)
		LEFT JOIN pg_evaluation d ON (d.pg_defense_id = c.id)
		WHERE a.recomm_by = '$user_id'
		AND a.recomm_status = 'REC'
		AND a.ref_session_type_id = 'DEF'
		AND a.status = 'A'
		ORDER BY a.student_matrix_no, b.seq, a.defense_date ASC)
		UNION
		(SELECT a.id, a.student_matrix_no, a.thesis_id,
		a.defense_date, DATE_FORMAT(a.defense_date,'%d-%b-%Y') as defense_date1, 
		DATE_FORMAT(a.defense_stime,'%h:%i%p') as defense_stime,
		DATE_FORMAT(a.defense_etime,'%h:%i%p') as defense_etime, a.venue, a.recomm_by, a.ref_session_type_id, 
		b.description as ref_session_type_desc, d.status as evaluation_status
		FROM pg_calendar a
		LEFT JOIN ref_session_type b ON (b.id = a.ref_session_type_id)
		LEFT JOIN pg_work c ON (c.pg_calendar_id = a.id)
		LEFT JOIN pg_work_evaluation d ON (d.pg_work_id = c.id)
		WHERE a.recomm_by = '$user_id'
		AND a.recomm_status = 'REC'
		AND a.ref_session_type_id = 'WCO'
		AND a.status = 'A'
		ORDER BY a.student_matrix_no, b.seq, a.defense_date ASC)
		UNION
		(SELECT a.id, a.student_matrix_no, a.thesis_id,
		a.defense_date, DATE_FORMAT(a.defense_date,'%d-%b-%Y') as defense_date1, 
		DATE_FORMAT(a.defense_stime,'%h:%i%p') as defense_stime,
		DATE_FORMAT(a.defense_etime,'%h:%i%p') as defense_etime, a.venue, a.recomm_by, a.ref_session_type_id, 
		b.description as ref_session_type_desc, d.status as evaluation_status
		FROM pg_calendar a
		LEFT JOIN ref_session_type b ON (b.id = a.ref_session_type_id)
		LEFT JOIN pg_viva c ON (c.pg_calendar_id = a.id)
		LEFT JOIN pg_evaluation_viva d ON (d.pg_viva_id = c.id)
		WHERE a.recomm_by = '$user_id'
		AND a.recomm_status = 'REC'
		AND a.ref_session_type_id = 'VIV'
		AND a.status = 'A'
		ORDER BY a.student_matrix_no, b.seq, a.defense_date ASC)";

		$result_sql4 = $dbg->query($sql4); 
		$dbg->next_record();
		
		$recommendedIdArray1 = Array(); 
		$recommendedByArray1 = Array(); 
		$studentMatrixNoArray1 = Array(); 
		$thesisIdArray1 = Array(); 
		$defenseDateArray1 = Array(); 
		$defenseSTimeArray1 = Array();
		$defenseETimeArray1 = Array();
		$venueArray1 = Array();
		$sessionTypeArray1 = Array();
		$evaluationStatusArray1 = Array();

		$i=0;
		$row_cnt_sql4 = mysql_num_rows($result_sql4);
		if ($row_cnt_sql4 > 0) {
			do {
				$recommendedIdArray1[$i] = $dbg->f('id');
				$recommendedByArray1[$i] = $dbg->f('recomm_by');
				$studentMatrixNoArray1[$i] = $dbg->f('student_matrix_no');
				$thesisIdArray1[$i] = $dbg->f('thesis_id');
				$defenseDateArray1[$i] = $dbg->f('defense_date1');
				$defenseSTimeArray1[$i] = $dbg->f('defense_stime');
				$defenseETimeArray1[$i] = $dbg->f('defense_etime');	
				$venueArray1[$i] = $dbg->f('venue');
				$sessionTypeArray1[$i] = $dbg->f('ref_session_type_desc');				
				$evaluationStatusArray1[$i] = $dbg->f('evaluation_status');	
				$i++;
			} while ($dbg->next_record());
		}
		?>
		<table>
			<tr>							
				<td>Searching Results:- <?=$row_cnt_sql4 ?> record(s) found.</td>
			</tr>
		</table>
		<? if ($row_cnt_sql4 > 5)
		{?>
			<div id = "tabledisplay" style="overflow:auto; height:200px;">
		<? }
		else 
		{ ?>
			<div id = "tabledisplay">
		<? } ?>
		<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="90%" class="thetable">			
			<tr>
				<th width="5%">Tick</th>	
				<th width="5%">No</th>					
				<th width="10%" align="left">Thesis / Project ID</th>
				<th width="10%" align="left">Student Matrix No.</th>
				<th width="20%" align="left">Student Name</th>
				<th width="15%" align="left">Evaluation Session</th>
				<th width="25%" align="left">Evaluation Schedule Date</th>
				<?if ($row_cnt_sql4 > 0 ) {?>	
					<?
					$no=0;
					for ($i=0; $i<$row_cnt_sql4; $i++){
						if($i % 2) $color ="first-row"; else $color = "second-row";?>
						<tr class="<?=$color?>">
							<?
							if ($evaluationStatusArray1[$i]=="") {?>
								<td align="center"><input name="defense_checkbox[]" id="defense_checkbox" type="checkbox" value="<?=$i;?>"/></td>
							<?}
							else {
								?>
								<td align="center"><input name="defense_checkbox[]" id="defense_checkbox" type="checkbox" value="<?=$i;?>" disabled="disabled"/></td>
							<?}?>
							<td align="center"><?=$no+1;?>.</td>
							<input type="hidden" name="recommendedIdArray1<?=$i?>" id="recommendedIdArray1<?=$i?>" value="<?=$recommendedIdArray1[$i]; ?>">
							<input type="hidden" name="thesisIdArray1<?=$i?>" id="thesisIdArray1<?=$i?>" value="<?=$thesisIdArray1[$i]; ?>">
							<input type="hidden" name="studentMatrixNoArray1<?=$i?>" id="studentMatrixNoArray1<?=$i?>" value="<?=$studentMatrixNoArray1[$i]; ?>">							
							<td align="left"><label><?=$thesisIdArray1[$i]?></label></td>
							<td align="left"><label><?=$studentMatrixNoArray1[$i]?></label></td>
							<?
							if (substr($studentMatrixNoArray1[$i],0,2) != '07') { 
								$dbConn=$dbc; 
							} 
							else { 
								$dbConn=$dbc1; 
							}

							$sql1 = "SELECT name
							FROM student
							WHERE matrix_no = '$studentMatrixNoArray1[$i]'";
							
							$result_sql1 = $dbConn->query($sql1); 
							$dbConn->next_record();
							$studentName=$dbConn->f('name');
							?>
							<td align="left"><label><?=$studentName?></label></td>
							<?
							if (substr($recommendedByArray1[$i],0,3) != 'S07') { 
								$dbConn=$dbc; 
							} 
							else { 
								$dbConn=$dbc1; 
							}

							$sql1 = "SELECT name
							FROM new_employee
							WHERE empid = '$recommendedByArray1[$i]'";
							
							$result_sql1 = $dbConn->query($sql1); 
							$dbConn->next_record();
							$employeeName=$dbConn->f('name');
							?>
							<td align="left"><label><?=$sessionTypeArray1[$i]?></label></td>
							<td align="left"><label><?=$defenseDateArray1[$i]?>, <?=$defenseSTimeArray1[$i]?> to <?=$defenseETimeArray1[$i]?>,<br><?=$venueArray1[$i]?></label></td>
						</tr>
						<?
					$no++;	
					}
					?>
					<table>
						<tr>
							<td><input type="submit" name="btnCancelDefence" value="Cancel Evaluation Schedule"></td>
						</tr>
					</table>
					<table>
						<tr>							
							<td>Note:<br/>
							1. If Checkbox is disabled, it is due to the schedule date has been reserved for the evaluation session.</td>
						</tr>
					</table>
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
				</tr>
			</table>
			</div>
		</fieldset>			
</body>
</html>





