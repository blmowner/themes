<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: progress_history_staff.php
//
// Created by: Zuraimi
// Created Date: 20 April 2015
// Modified by: Zuraimi
// Modified Date: 20 April 2015
//
//**************************************************************************************


//Read common library for page execution i.e. database connection. login validation
include("../../../lib/common.php");
//checkLogin();

session_start();
$userid=$_SESSION['user_id'];

///////////////////////////////////////////////////////////////
// used for pagination
	$page = ($_GET['page'] == 0 ? 1 : $_GET['page']);
	$perpage = 10;
	$startpoint = ($page * $perpage) - $perpage;

$varParamSend="";

foreach($_REQUEST as $key => $value)
{
	if($key!="page")
		$varParamSend.="&$key=$value";
}

if(isset($_POST['btnSearch']) && ($_POST['btnSearch'] <> "")) {
	
	$searchThesisId = $_POST['searchThesisId'];
	$searchStudent = $_POST['searchStudent'];
	$searchStudentName = $_POST['searchStudentName'];	
	
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
		$tmpSearchStudent = " AND a.student_matrix_no = '$searchStudent'";
	}
	else 
	{
		$tmpSearchStudent="";
	}
	
	$sql1 = "SELECT DISTINCT a.reference_no, a.report_month, a.report_year, a.pg_thesis_id, c.thesis_title, 
	a.pg_proposal_id, a.student_matrix_no 
	FROM pg_progress a
	LEFT JOIN pg_progress_detail b ON (b.pg_progress_id = a.id)
	LEFT JOIN pg_proposal c ON (c.id = a.pg_proposal_id)
	LEFT JOIN ref_proposal_status d1 ON (d1.id = a.status)
	LEFT JOIN ref_proposal_status d2 ON (d2.id = b.status)
	LEFT JOIN ref_proposal_status d3 ON (d3.id = b.archived_status)
	WHERE b.pg_employee_empid = '$user_id'"
	.$tmpSearchThesisId." "
	.$tmpSearchStudent." "." 
	ORDER BY a.student_matrix_no, a.pg_thesis_id, a.pg_proposal_id, a.report_month, a.report_year";

	$result1 = $db->query($sql1);
	$db->next_record();
	
	$referenceNoArray = Array();
	$reportMonthArray = Array();
	$reportYearArray = Array();
	$studentMatrixNoArray = Array();
	$thesisIdArray = Array();	
	$proposalIdArray = Array();	
	$thesisTitleArray = Array();
	
	$no1=0;
	$no2=0;
	do {
		$referenceNoArray[$no1] = $db->f('reference_no');
		$reportMonthArray[$no1] = $db->f('report_month');
		$reportYearArray[$no1] = $db->f('report_year');
		$studentMatrixNoArray[$no1] = $db->f('student_matrix_no');
		$thesisIdArray[$no1] = $db->f('pg_thesis_id');	
		$thesisTitleArray[$no1] = $db->f('thesis_title');
		$proposalIdArray[$no1] = $db->f('pg_proposal_id');	
		
		$no1++;
		
	} while ($db->next_record());
	
	$studentNameArray = Array();
	for ($i=0; $i<$no1; $i++){
		 $sql9 = "SELECT name
			FROM student
			WHERE matrix_no = '$studentMatrixNoArray[$i]'
			AND name like '%$searchStudentName%'";
		if (substr($studentMatrixNoArray[$i],0,2) != '07') { 
			$dbConnStudent= $dbc; 
		} 
		else { 
			$dbConnStudent=$dbc1; 
		}
		$result9 = $dbConnStudent->query($sql9); 
		$dbConnStudent->next_record();
		if (mysql_num_rows($result9)>0) {
			$studentNameArray[$no2] = $dbConnStudent->f('name');
			$referenceNoArray[$no2] = $referenceNoArray[$i];
			$reportMonthArray[$no2] = $reportMonthArray[$i];
			$reportYearArray[$no2] = $reportYearArray[$i];
			$thesisIdArray[$no2] = $thesisIdArray[$i];
			$thesisTitleArray[$no2] = $thesisTitleArray[$i];
			$proposalIdArray[$no2] = $proposalIdArray[$i];
			
			$no2++;
		}
	}
	$row_cnt = $no2;
}
else 
{
	$sql1 = "SELECT DISTINCT a.reference_no, a.report_month, a.report_year, a.pg_thesis_id, c.thesis_title, 
	a.pg_proposal_id, a.student_matrix_no	
	FROM pg_progress a
	LEFT JOIN pg_progress_detail b ON (b.pg_progress_id = a.id)
	LEFT JOIN pg_proposal c ON (c.id = a.pg_proposal_id)
	LEFT JOIN ref_proposal_status d1 ON (d1.id = a.status)
	LEFT JOIN ref_proposal_status d2 ON (d2.id = b.status)
	LEFT JOIN ref_proposal_status d3 ON (d3.id = b.archived_status)
	WHERE b.pg_employee_empid = '$user_id'
	ORDER BY a.student_matrix_no, a.pg_thesis_id, a.pg_proposal_id, a.report_month, a.report_year";
						
		
	$result1 = $db->query($sql1); 
	$db->next_record();
					
	$referenceNoArray = Array();
	$reportMonthArray = Array();
	$reportYearArray = Array();
	$studentMatrixNoArray = Array();
	$thesisIdArray = Array();	
	$proposalIdArray = Array();	
	$thesisTitleArray = Array();
	
	$no1=0;
	$no2=0;
	$row_cnt=0;
	do {
		$referenceNoArray[$no1] = $db->f('reference_no');
		$reportMonthArray[$no1] = $db->f('report_month');
		$reportYearArray[$no1] = $db->f('report_year');
		$studentMatrixNoArray[$no1] = $db->f('student_matrix_no');
		$thesisIdArray[$no1] = $db->f('pg_thesis_id');	
		$thesisTitleArray[$no1] = $db->f('thesis_title');
		$proposalIdArray[$no1] = $db->f('pg_proposal_id');	
		
		$no1++;
		
	} while ($db->next_record());

	$studentNameArray = Array();
	for ($i=0; $i<$no1; $i++){
		$sql9 = "SELECT name
			FROM student
			WHERE matrix_no = '$studentMatrixNoArray[$i]'
			AND name like '%$searchStudentNameArray[$i]%'";
		if (substr($studentMatrixNoArray[$i],0,2) != '07') { 
			$dbConnStudent= $dbc; 
		} 
		else { 
			$dbConnStudent=$dbc1; 
		}
		$result9 = $dbConnStudent->query($sql9); 
		$dbConnStudent->next_record();
		if (mysql_num_rows($result9)>0) {
			$studentNameArray[$no2] = $dbConnStudent->f('name');
			$referenceNoArray[$no2] = $referenceNoArray[$i];
			$reportMonthArray[$no2] = $reportMonthArray[$i];
			$reportYearArray[$no2] = $reportYearArray[$i];
			$thesisIdArray[$no2] = $thesisIdArray[$i];
			$thesisTitleArray[$no2] = $thesisTitleArray[$i];
			$proposalIdArray[$no2] = $proposalIdArray[$i];
			
			$no2++;
		}
	}
	$row_cnt = $no2;
}
?>

	

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Monthly Progress Report History</title>
		<link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
		<link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
		<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />

		<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
		<script src="../../../lib/js/jquery.min2.js"></script>
		<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
		<script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
		<script language="JavaScript" src="../../../lib/js/windowopen.js"></script>
		<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>
	</head>
	<body>
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
						<td><input type="submit" name="btnSearch" value="Search" /><span style="color:#FF0000"> Note:</span> If no entry is provided, it will search all.</td>
					</tr>
				</table>
				<br/>
				<table>
					<tr>							
						<td>Searching Results:- <?=$row_cnt?> record(s) found.</td>
					</tr>
				</table>
				<?if ($row_cnt <= 0) {?>
					<div id = "tabledisplay" style="overflow:auto; height:60px;">
				<?}
				else if ($row_cnt <= 2) {?>
					<div id = "tabledisplay" style="overflow:auto; height:100px;">
				<?}
				else if ($row_cnt <= 4) {
					?>
					<div id = "tabledisplay" style="overflow:auto; height:160px;">
					<?
				}
				else if ($row_cnt <= 6) {
					?>
					<div id = "tabledisplay" style="overflow:auto; height:220px;">
					<?
				}
				else if ($row_cnt <= 8) {
					?>
					<div id = "tabledisplay" style="overflow:auto; height:290px;">
					<?
				}
				else if ($row_cnt <= 10) {
					?>
					<div id = "tabledisplay" style="overflow:auto; height:350px;">
					<?
				}
				else {
					?>
					<div id = "tabledisplay" style="overflow:auto; height:380px;">
					<?
				}?>
				<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="80%" class="thetable">
					<tr>
						<th align="center" width="5%"><strong>No.</strong></th>
						<th align="left" width="10%"><strong>Matric No</strong></th>
						<th align="left" width="25%"><strong>Student Name</strong></th>
						<th align="left" width="10%"><strong>Thesis / Project ID</strong></th>
						<th align="left" width="10%"><strong>Reference No</strong></th>
						<th align="center" width="10%"><strong>Report for</strong></th>
						<th align="center" width="10%"><strong><label>History Detail</label></strong></th>
					</tr>
					<?
					if ($row_cnt>0) {
					$no=0;
					for ($i=0; $i<$no2; $i++){
						if($i % 2) $color ="first-row"; else $color = "second-row";						
					?>
						<tr class="<?=$color?>">
							<td align="center"><?=++$no?>.</td>
							<td><label><?=$studentMatrixNoArray[$i]?></label></td>
							<td><label><?=$studentNameArray[$i]?></label></td>
							<td><label><?=$thesisIdArray[$i]?></label></td>
							<td><label><?=$referenceNoArray[$i]?></label></td>
							<?$theReportMonth = date("M",strtotime("$reportMonthArray[$i]"));?>
							<td align="center"><label><?=$theReportMonth?> <?=$reportYearArray[$i]?></label></td>
							<td align="center"><label><a href="../monthlyreport/progress_history_staff_detail.php?mn=<?=$studentMatrixNoArray[$i]?>&ref=<?=$referenceNoArray[$i]?>">View</a></label></td>
						</tr>
					<?
					};					
				}
				else {
					?>
					<table>
						<tr>
							<td><label>No record found!</label></p>
							</td>
						</tr>
					</table>
					<?
				}
			?>
			</table>
			</fieldset>
				
		</form>
	</body>
</html>




