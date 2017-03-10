<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: list_progress_report_facview.php
//
// Created by: Zuraimi
// Created Date: 23-Jun-2015
// Modified by: Zuraimi
// Modified Date: 23-Jun-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

session_start();
$user_id=$_SESSION['user_id'];
$thesisId = $_GET['tid'];
$proposalId = $_GET['pid'];
$matrixNo = $_GET['mn'];
$employeeId = $_GET['eid'];

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

$sql_supervisor1 = " SELECT DATE_FORMAT(a.acceptance_date,'%d-%b-%Y') as acceptance_date
FROM pg_supervisor a 
LEFT JOIN ref_supervisor_type d ON (d.id = a.ref_supervisor_type_id) 
LEFT JOIN pg_thesis f ON (f.student_matrix_no = a.pg_student_matrix_no)
LEFT JOIN pg_proposal g ON (g.pg_thesis_id = f.id)
WHERE a.pg_student_matrix_no='$matrixNo'
AND g.pg_thesis_id = '$thesisId'
AND g.id = '$proposalId'
AND a.acceptance_status = 'ACC'
AND a.ref_supervisor_type_id in ('SV','CS','XS')
AND g.verified_status in ('APP','AWC')
AND g.status in ('APP','APC')
AND g.archived_status IS NULL
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
		<script language="JavaScript" src="../../../lib/js/windowopen.js"></script>
   		<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
    	<script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
		<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>			
		<script language="JavaScript" type="text/javascript" src="../../../lib/js/tooltip.js"></script>
		<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>
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
	<legend><strong>List of Student's Monthly Progress Report</strong></legend>
		<table>
			<tr>
				<td>Staff ID</td>
				<td>:</td>
				<td><label><?=$employeeId?></label></td>
			</tr>
			<?
			if (substr($employeeIdArray[$i],0,3) != 'S07') { 
				$dbConn=$dbc; 
			} 
			else { 
				$dbConn=$dbc1; 
			}

			$sql2 = "SELECT name
			FROM new_employee
			WHERE empid = '$employeeId'";			
			
			$result_sql2 = $dbConn->query($sql2); 
			$dbConn->next_record();
			$employeeName=$dbConn->f('name');
			?>
			<tr>
				<td>Supervisor / Co-Supervisor Name</td>
				<td>:</td>
				<td><label><?=$employeeName?></label></td>
			</tr>
			<tr>
				<td>Matrix No</td>
				<td>:</td>
				<td><label><?=$matrixNo?></label></td>
			</tr>
			<?
			$sql9 = "SELECT name
					FROM student
					WHERE matrix_no = '$matrixNo'";
			if (substr($matrixNo,0,2) != '07') { 
				$dbConnStudent= $dbc; 
			} 
			else { 
				$dbConnStudent=$dbc1; 
			}
			$result9 = $dbConnStudent->query($sql9); 
			$dbConnStudent->next_record();
			$studentName = $dbConnStudent->f('name');
				
			?>
			<tr>
				<td>Student Name</td>
				<td>:</td>
				<td><label><?=$studentName?></label></td>
			</tr>			
		</table>
		<br/>
		<table>
			<tr>
				<td>Notes:<br/>
				1. The first Monthly Progress Report for this student should be <strong><?=$expectedReport?></strong>  after the earliest Supervisor's acceptance date <strong><?=$confirmAcceptanceDate?></strong> which is <strong><?=$firstMonthlyReport?></strong>.<br/>2. Asterisk (<span style="color:#FF0000">*</span>) - Indicate current month.</td>
			</tr>
		</table>		
		<?
		
		$sql_supervisor2 = "SELECT DATE_FORMAT(a.acceptance_date,'%d-%b-%Y') as acceptance_date
		FROM pg_supervisor a 
		LEFT JOIN ref_supervisor_type d ON (d.id = a.ref_supervisor_type_id) 
		LEFT JOIN pg_thesis f ON (f.student_matrix_no = a.pg_student_matrix_no)
		LEFT JOIN pg_proposal g ON (g.pg_thesis_id = f.id)
		LEFT JOIN ref_role_status h ON (h.id = a.role_status) 
		WHERE a.pg_student_matrix_no = '$matrixNo'
		AND a.pg_thesis_id = '$thesisId'
		AND a.acceptance_status = 'ACC'
		AND a.ref_supervisor_type_id in ('SV','CS','XS')
		AND g.verified_status in ('APP','AWC')
		AND g.status in ('APP','APC')
		AND g.archived_status IS NULL
		AND a.status = 'A'
		ORDER BY a.acceptance_date
		LIMIT 1";

		$result_sql_supervisor = $dba->query($sql_supervisor2);
		$dba->next_record();
		$myAcceptanceDate = $dba->f('acceptance_date');
		
				
		$currMonth = date("F");// current date
		$currMonth1 = date("F Y");// current date

		$newReportDate = date('d-M-Y', strtotime($myAcceptanceDate. ' '.($firstMonthlyReportParam).' month'));		
		$tmpNewReportDate = new DateTime($newReportDate);
		$startMonth = $tmpNewReportDate->format('F');
		
		$no=0;
		$no1=0;
		
		$reportMonth = Array(
			"January" => "1",
			"February" => "2",
			"March" => "3",
			"April" => "4",
			"May" => "5",
			"June" => "6",
			"July" => "7",
			"August" => "8",
			"September" => "9",
			"October" => "10",
			"November" => "11",
			"December" => "12"
		);
		
		$reportMonthReverse = Array(
			"1" => "January",
			"2"=> "February",
			"3"=> "March",
			"4"=> "April",
			"5" => "May",
			"6" => "June",
			"7" => "July",
			"8" => "August",
			"9" => "September",
			"10" => "October",
			"11" => "November",
			"12" => "December"
		);
				
		?>
		<table>
			<tr>
				<td><h3><label><strong>List of Accepted Monthly Progress Report</strong></label></h3></td>
			</tr>
		</table>
		<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="95%" class="thetable">			
			<tr>
				<th width="5%">No</th>					
				<th width="15%" align="left">Expected Monthly Progress Report</th>
				<th width="15%" align="left">Accepted Monthly Progress Report</th>
				<th width="15%">Submitted Date by Student</th>
				<th width="10%">Reference No.</th>
				<th width="15%">Last Update by Supervisor / Co-Supervisor</th>
				<th width="20%">Status</th>
			</tr>
			<?
			$prevYear = date('Y', strtotime($confirmAcceptanceDate));
			$currYear = date('Y');

			for ($j=$prevYear;$j<=$currYear;$j++){
				if ($j==$prevYear){
					$theYear = $j;
					$theStartMonth = $reportMonth[date('F', strtotime($confirmAcceptanceDate. ' '.($firstMonthlyReportParam).' month'))];
					$theEndMonth = 12;
				}
				else if (($j>$prevYear) && ($j<$currYear)){
					$theYear = $j;
					$theStartMonth = 1;
					$theEndMonth = 12;
				}
				else if ($j==$currYear){
					$theYear = $j;
					$theStartMonth = 1;
					$theEndMonth = $reportMonth[date('F')];
				}

				for ($i=$theEndMonth-$theStartMonth;$i>=0;$i--) {?>
					<tr>
						<td align="center"><label><?=++$no1;?>.</label></td>
						<?
						$expectedMonth1 = $theEndMonth-$i;//theStartMonth
						$expectedMonth = $theEndMonth;//theEndMonth
						$expectedYear = $j;
						
						$sql2 = "SELECT a.id as progress_id, b.id as progress_detail_id, a.report_month, a.report_year, 
						DATE_FORMAT(b.responded_date, '%d-%b-%Y %h:%i %p') as responded_date, 
						DATE_FORMAT(a.submit_date, '%d-%b-%Y %h:%i %p') as submit_date,
						a.reference_no, b.status as progress_detail_status, c.description as progress_detail_desc
						FROM pg_progress a
						LEFT JOIN pg_progress_detail b ON (b.pg_progress_id = a.id)
						LEFT JOIN ref_proposal_status c ON (c.id = b.status)
						WHERE a.student_matrix_no = '$matrixNo'
						AND a.pg_thesis_id = '$thesisId'
						AND a.pg_proposal_id = '$proposalId'
						AND b.pg_employee_empid = '$employeeId'
						AND a.report_month = '$reportMonthReverse[$expectedMonth1]'
						AND a.report_year = '$expectedYear'
						AND a.archived_status IS NULL
						AND b.archived_status IS NULL";
						
						$result_sql2 = $dbg->query($sql2); 
						$dbg->next_record();
						
						$row_cnt2 = mysql_num_rows($result_sql2);
						if ($row_cnt2 > 0) {
							$theReportMonth = $dbg->f('report_month');
							$theReportMonth1 = date("M",strtotime("$theReportMonth"));
							$reportYear = $dbg->f('report_year');
							$respondedDate = $dbg->f('responded_date');
							$submitDate = $dbg->f('submit_date');
							$referenceNo = $dbg->f('reference_no');
							$progressId =  $dbg->f('progress_id');
							$progressDetailId =  $dbg->f('progress_detail_id');
							$progressDetailStatus = $dbg->f('progress_detail_status');
							$progressDetailDesc = $dbg->f('progress_detail_desc');						
						}
						else {
							$theReportMonth1 = "";
							$reportYear = "";
							$respondedDate = "";
							$submitDate = "";
							$referenceNo = "";
							$progressId =  "";
							$progressDetailId =  "";
							$progressDetailStatus = "";
							$progressDetailDesc = "";
						}
						$my_date = date('M Y', strtotime($reportMonthReverse[$expectedMonth1]." ".$theYear));
						$expectedMonth2 = $expectedMonth1;
						if (($reportMonthReverse[$expectedMonth1] == $currMonth) && ($j==$currYear)){						
							?>
							<td align="left"><label><?=$my_date?><span style="color:#FF0000">*</span></label></td>
						<?}
						else {						
							?>
							<td align="left"><label><?=$my_date?></label></td>
							<?
						}?>
						<td align="left"><a href="list_view_progress_staff.php?eid=<?=$employeeId?>&mn=<?=$matrixNo?>&pid=<?=$proposalId;?>&tid=<?=$thesisId;?>&id=<?=$progressId;?>&pdid=<?=$progressDetailId;?>&ref=<?=$referenceNo;?>" title=""><?=$theReportMonth1?> <?=$reportYear?></a></td>
						<td align="center"><label><?=$submitDate?></label></td>
						<td align="center"><label><?=$referenceNo?></label></td>
						<td align="center"><label><?=$respondedDate?></label></td>
						<td><label><?=$progressDetailDesc?></label></td>
					</tr>
				<?}
			}
			?>
		</table>
		<table>
			<tr>		
				<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='list_progress_report_faculty.php';" /></td>		
			</tr>
		</table>
		<h3><strong>Partner(s)</strong></h3>
			<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="79%" class="thetable">			
				<tr>
					<th width="4%">No</th>					
					<th width="15%">Role</th>
					<th width="10%">Staff ID</th>
					<th width="30%">Name</th>
					<th width="5%">Faculty</th>
					<th width="15%">Acceptance Date</th>
				</tr>
				 <?php				
				
				$sql_supervisor = "SELECT a.pg_employee_empid, a.ref_supervisor_type_id, d.description as supervisor_desc, 
				DATE_FORMAT(a.acceptance_date,'%d-%b-%Y') as acceptance_date, h.description as role_status_desc
				FROM pg_supervisor a 
				LEFT JOIN ref_supervisor_type d ON (d.id = a.ref_supervisor_type_id) 
				LEFT JOIN pg_thesis f ON (f.student_matrix_no = a.pg_student_matrix_no)
				LEFT JOIN pg_proposal g ON (g.pg_thesis_id = f.id)
				LEFT JOIN ref_role_status h ON (h.id = a.role_status) 
				WHERE a.pg_employee_empid <> '$employeeId'
				AND a.pg_student_matrix_no = '$matrixNo'
				AND a.pg_thesis_id = '$thesisId'
				AND a.acceptance_status = 'ACC'
				AND a.ref_supervisor_type_id in ('SV','CS','XS')
				AND g.verified_status in ('APP','AWC')
				AND g.status in ('APP','APC')
				AND g.archived_status IS NULL
				AND a.status = 'A'
				ORDER BY d.seq, a.ref_supervisor_type_id";

				$result_sql_supervisor = $db_klas2->query($sql_supervisor); 
				
				$row_cnt_supervisor = mysql_num_rows($result_sql_supervisor);
				$db_klas2->next_record();
				$varRecCount=0;	
				
				$employeeIdArray = Array();
				$supervisorTypeIdArray = Array();
				$supervisorDescArray = Array();
				$acceptanceDateArray = Array();
				$roleStatusDescArray = Array();
				$employeeNameArray = Array();
				$departmentIdArray = Array();
				$departmentNameArray = Array();
				$j=0;
				
				if ($row_cnt_supervisor>0) {

					do {
						$employeeIdArray[$j] = $db_klas2->f('pg_employee_empid');
						$supervisorTypeIdArray[$j] = $db_klas2->f('ref_supervisor_type_id');
						$supervisorDescArray[$j] = $db_klas2->f('supervisor_desc');
						$acceptanceDateArray[$j] = $db_klas2->f('acceptance_date');
						$roleStatusDescArray[$j] = $db_klas2->f('role_status_desc');
					
						$sql_employee="SELECT  b.name, c.id, c.description
							FROM new_employee b 
							LEFT JOIN dept_unit c ON (c.id = b.unit_id) 
							WHERE b.empid= '$employeeIdArray[$j]'";
							
						$result_sql_employee = $dbc->query($sql_employee);
						$dbc->next_record();
						
						$employeeNameArray[$j] = $dbc->f('name');
						$departmentIdArray[$j] = $dbc->f('id');
						$departmentNameArray[$j] = $dbc->f('description');
						$varRecCount++;

						?>
						<tr>
							<td align="center"><?=$varRecCount;?>.</td>					
							<td>
							<?
							if ($supervisorTypeIdArray[$j] == 'XS') {
							?>
								<label><span style="color:#FF0000"><?=$supervisorDescArray[$j]?></span></label>
							<?}
							else {
								?>
								<label><?=$supervisorDescArray[$j]?></label>
								<?
							}?>
							<br/><?=$roleStatusDescArray[$j]?></td>
							<td align="left"><?=$employeeIdArray[$j];?></td>
							<td align="left"><?=$employeeNameArray[$j];?></td>
							<td align="left"><a href="javascript:void(0);" onMouseOver="toolTip('<?=$departmentNameArray[$j];?>', 300)" onMouseOut="toolTip()"><?=$departmentIdArray[$j];?></a></td>
							<td><label><?=$acceptanceDateArray[$j]?></label></td>
						<?
						$j++;
						} while($db_klas2->next_record());
					}
					else {
						?>
						<table>				
							<tr><td>No record found!</tr>
						</table>
						<br/>
						<table>				
							<tr><td><br/><span style="color:#FF0000">Notes:</span><br/>
										Possible Reasons:-<br/>
										1. Supervisor/Co-Supervisor is yet to be assigned.<br/>
										2. Pending approval by the Senate.<br/>
										3. If already assigned, it could be the Supervisor/Co-Supervisor pending to accept.</td>
							</tr>
						</table>
						<?
					}?>
					
			</table>
		<?if ($row_cnt_supervisor > 0) {	
		?>
			<table>
			<tr>
				<td><h3><label><strong>List of Accepted Monthly Progress Report - Partner(s)</strong></label></h3></td>
			</tr>
			</table>
		<?
		for ($k=0;$k<$row_cnt_supervisor;$k++) {
				$no2=0;
			?>				
				<table>
				<tr>
					<td><label><strong>Partner Name:</strong></label> - <?=$employeeNameArray[$k]?></td>
				</tr>
				</table>
				<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="95%" class="thetable">			
					<tr>
						<th width="5%">No</th>					
						<th width="15%" align="left">Expected Monthly Progress Report</th>
						<th width="15%" align="left">Accepted Monthly Progress Report</th>
						<th width="15%">Submitted Date by Student</th>
						<th width="10%">Reference No.</th>
						<th width="15%">Last Update by Supervisor / Co-Supervisor</th>
						<th width="20%">Status</th>
					</tr>
					<?
					$prevYear = date('Y', strtotime($confirmAcceptanceDate));
					$currYear = date('Y');

					for ($j=$prevYear;$j<=$currYear;$j++){
						if ($j==$prevYear){
							$theYear = $j;
							$theStartMonth = $reportMonth[date('F', strtotime($confirmAcceptanceDate. ' '.($firstMonthlyReportParam).' month'))];
							$theEndMonth = 12;
						}
						else if (($j>$prevYear) && ($j<$currYear)){
							$theYear = $j;
							$theStartMonth = 1;
							$theEndMonth = 12;
						}
						else if ($j==$currYear){
							$theYear = $j;
							$theStartMonth = 1;
							$theEndMonth = $reportMonth[date('F')];
						}
						for ($i=$theEndMonth-$theStartMonth;$i>=0;$i--) {?>
							<tr>
								<td align="center"><label><?=++$no2;?>.</label></td>
								<?
								$expectedMonth1 = $theEndMonth-$i;//theStartMonth
								$expectedMonth = $theEndMonth;//theEndMonth
								$expectedYear = $j;
								
								$sql2 = "SELECT a.id, a.report_month, a.report_year, DATE_FORMAT(b.responded_date, '%d-%b-%Y %h:%i %p') as responded_date, 
								DATE_FORMAT(a.submit_date, '%d-%b-%Y %h:%i %p') as submit_date,
								a.reference_no, b.status as progress_detail_status, c.description as progress_detail_desc
								FROM pg_progress a
								LEFT JOIN pg_progress_detail b ON (b.pg_progress_id = a.id)
								LEFT JOIN ref_proposal_status c ON (c.id = b.status)
								WHERE a.student_matrix_no = '$matrixNo'
								AND a.pg_thesis_id = '$thesisId'
								AND a.pg_proposal_id = '$proposalId'
								AND b.pg_employee_empid = '$employeeIdArray[$k]'
								AND a.report_month = '$reportMonthReverse[$expectedMonth1]'
								AND a.report_year = '$expectedYear'
								AND a.archived_status IS NULL
								AND b.archived_status IS NULL";
								
								$result_sql2 = $dbg->query($sql2); 
								$dbg->next_record();
								
								$row_cnt2 = mysql_num_rows($result_sql2);
								if ($row_cnt2 > 0) {
									$theReportMonth = $dbg->f('report_month');
									$theReportMonth1 = date("M",strtotime("$theReportMonth"));
									$reportYear = $dbg->f('report_year');
									$respondedDate = $dbg->f('responded_date');
									$submitDate = $dbg->f('submit_date');
									$referenceNo = $dbg->f('reference_no');
									$progressDetailStatus = $dbg->f('progress_detail_status');
									$progressDetailDesc = $dbg->f('progress_detail_desc');
								}
								else {
									$theReportMonth1 = "";
									$reportYear = "";
									$respondedDate = "";
									$submitDate = "";
									$referenceNo = "";
									$progressDetailDesc = "";
								}
								
								$my_date = date('M Y', strtotime($reportMonthReverse[$expectedMonth1]." ".$theYear));
								$expectedMonth2 = $expectedMonth1;
								if (($reportMonthReverse[$expectedMonth1] == $currMonth) && ($j==$currYear)){						
									?>
									<td align="left"><label><?=$my_date?><span style="color:#FF0000">*</span></label></td>
								<?}
								else {						
									?>
									<td align="left"><label><?=$my_date?></label></td>
									<?
								}?>

								<td><label><?=$theReportMonth1?> <?=$reportYear?></label></td>
								<td align="center"><label><?=$submitDate?></label></td>
								<td><label><?=$referenceNo?></label></td>
								<td align="center"><label><?=$respondedDate?></label></td>
								<td><label><?=$progressDetailDesc?></label></td>
							</tr>
						<?}
				}?>
				</table>			
			<?
			}
		}?>
		</fieldset>
		<table>
			<tr>		
				<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='list_progress_report_faculty.php';" /></td>
				<td><input type="button" name="btnPrint" id="btnPrint" value="Print"onclick="javascript:open_win('pdf_list_progress_report.php?tid=<?=$thesisId?>&pid=<?=$proposalId?>&mn=<?=$matrixNo;?>&eid=<?=$employeeId?>',800,700,0,0,0,1,0,1,1,0,5,'winupload'); "/></td>
				
			</tr>
		</table>
</body>
</html>





