<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: defense_history.php
//
// Created by: Zuraimi
// Created Date: 03 July 2015
// Modified by: Zuraimi
// Modified Date: 03 July 2015
//
//**************************************************************************************


//Read common library for page execution i.e. database connection. login validation
include("../../../lib/common.php");
//checkLogin();

session_start();
$userid=$_SESSION['user_id'];

if(isset($_POST['btnSearch']) && ($_POST['btnSearch'] <> "")) {
	
	$searchStaffName = $_POST['searchStaffName'];
	$searchStaffId = $_POST['searchStaffId'];
	$searchReferenceNo = $_POST['searchReferenceNo'];
	
	if ($searchStaffId!="") 
	{
		$tmpSearchStaffId = " AND b.pg_employee_empid = '$searchStaffId'";
	}
	else 
	{
		$tmpSearchStaffId="";
	}
	if ($searchReferenceNo!="") 
	{
		$tmpSearchReferenceNo = " AND a.reference_no like '%$searchReferenceNo%'";
	}
	else 
	{
		$tmpSearchReferenceNo="";
	}
	
	$sql1 = "SELECT DISTINCT a.reference_no, a.pg_thesis_id, c.thesis_title, a.pg_proposal_id, b.pg_employee_empid
	FROM pg_defense a
	LEFT JOIN pg_defense_detail b ON (b.pg_defense_id = a.id)
	LEFT JOIN pg_proposal c ON (c.id = a.pg_proposal_id)
	LEFT JOIN ref_proposal_status d1 ON (d1.id = a.status)
	LEFT JOIN ref_proposal_status d2 ON (d2.id = b.status)
	LEFT JOIN ref_proposal_status d3 ON (d3.id = b.archived_status)
	WHERE a.student_matrix_no = '$user_id' "
	.$tmpSearchReferenceNo." "
	.$tmpSearchStaffId." ";
	$result1 = $db->query($sql1);
	$db->next_record();
	
	$referenceNoArray = Array();
	$thesisIdArray = Array();	
	$proposalIdArray = Array();	
	$thesisTitleArray = Array();
	$employeeIdArray = Array();
	
	$no1=0;
	$no2=0;
	do {
		$referenceNoArray[$no1] = $db->f('reference_no');
		$thesisIdArray[$no1] = $db->f('pg_thesis_id');	
		$thesisTitleArray[$no1] = $db->f('thesis_title');
		$proposalIdArray[$no1] = $db->f('pg_proposal_id');	
		$employeeIdArray[$no1] = $db->f('pg_employee_empid');	
				
		$no1++;
		
	} while ($db->next_record());
	
	$employeeNameArray = Array();
	for ($i=0; $i<$no1; $i++){
		$sql9 = "SELECT name
		FROM new_employee
		WHERE empid = '$employeeIdArray[$i]'
		AND name like '%$searchStaffName%'";
		if (substr($employeeIdArray[$i],0,3) != 'S07') { 
			$dbConn= $dbc; 
		} 
		else { 
			$dbConn=$dbc1; 
		}
		$result9 = $dbConn->query($sql9); 
		$dbConn->next_record();
		if (mysql_num_rows($result9)>0) {
			$employeeNameArray[$no2] = $dbConn->f('name');
			$referenceNoArray[$no2] = $referenceNoArray[$i];
			$thesisIdArray[$no2] = $thesisIdArray[$i];
			$thesisTitleArray[$no2] = $thesisTitleArray[$i];
			$proposalIdArray[$no2] = $proposalIdArray[$i];
			$employeeIdArray[$no2] = $employeeIdArray[$i];
						
			$no2++;
		}
	}
	$row_cnt = $no2;
}
else 
{
	$sql1 = "SELECT DISTINCT a.reference_no, a.pg_thesis_id, c.thesis_title, a.pg_proposal_id, b.pg_employee_empid,
	DATE_FORMAT(e.defense_date,'%d-%b-%Y') AS defense_date
	FROM pg_defense a
	LEFT JOIN pg_defense_detail b ON (b.pg_defense_id = a.id)
	LEFT JOIN pg_proposal c ON (c.id = a.pg_proposal_id)
	LEFT JOIN pg_calendar e ON (e.id = a.pg_calendar_id)
	LEFT JOIN ref_proposal_status d1 ON (d1.id = a.status)
	LEFT JOIN ref_proposal_status d2 ON (d2.id = b.status)
	LEFT JOIN ref_proposal_status d3 ON (d3.id = b.archived_status)
	WHERE a.student_matrix_no = '$user_id'
	AND e.ref_session_type_id = 'DEF'";
						
		
	$result1 = $db->query($sql1); 
	$db->next_record();
					
	$referenceNoArray = Array();
	$thesisIdArray = Array();	
	$proposalIdArray = Array();	
	$thesisTitleArray = Array();
	$employeeIdArray = Array();
	$defenseDateArray = Array();
	$no1=0;
	$no2=0;
	$row_cnt=0;
	do {
		$referenceNoArray[$no1] = $db->f('reference_no');
		$thesisIdArray[$no1] = $db->f('pg_thesis_id');	
		$thesisTitleArray[$no1] = $db->f('thesis_title');
		$proposalIdArray[$no1] = $db->f('pg_proposal_id');	
		$employeeIdArray[$no1] = $db->f('pg_employee_empid');
		$defenseDateArray[$no1] = $db->f('defense_date');
		$no1++;
		
	} while ($db->next_record());

	$studentNameArray = Array();
	for ($i=0; $i<$no1; $i++){
		$sql9 = "SELECT name
		FROM new_employee
		WHERE empid = '$employeeIdArray[$i]'
		AND name like '%$searchStaffName%'";
		if (substr($employeeIdArray[$i],0,3) != 'S07') { 
			$dbConn= $dbc; 
		} 
		else { 
			$dbConn=$dbc1; 
		}
		$result9 = $dbConn->query($sql9); 
		$dbConn->next_record();
		if (mysql_num_rows($result9)>0) {
			$employeeNameArray[$no2] = $dbConn->f('name');
			$referenceNoArray[$no2] = $referenceNoArray[$i];
			$thesisIdArray[$no2] = $thesisIdArray[$i];
			$thesisTitleArray[$no2] = $thesisTitleArray[$i];
			$proposalIdArray[$no2] = $proposalIdArray[$i];
			$employeeIdArray[$no2] = $employeeIdArray[$i];
			$defenseDateArray[$no2] = $defenseDateArray[$i];
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
						<td>Supervisor / Co-Supervisor Name</td>
						<td>:</td>
						<td><input type="text" name="searchStaffName" size="30" id="searchStaffName" value="<?=$searchStaffName;?>"/></td>						
					</tr>
					<tr>
						<td>Staff ID</td>
						<td>:</td>
						<td><input type="text" name="searchStaffId" size="30" id="searchStaffId" value="<?=$searchStaffId;?>"/></td>						
					</tr>
					<tr>
						<td>Reference No.</td>
						<td>:</td>
						<td><input type="text" name="searchReferenceNo" size="30" id="searchReferenceNo" value="<?=$searchReferenceNo;?>"/></td>
						<td><input type="submit" name="btnSearch" value="Search" /> Note: If no entry is provided, it will search all.</td>
					</tr>
				</table>
				<br/>
				<table>
					<tr>							
						<td>Searching Results:- <?=$row_cnt?> record(s) found.</td>
					</tr>
				</table>
				<?if ($row_cnt <= 0) {?>
					<div id = "tabledisplay" style="overflow:auto; height:80px;">
				<?}
				else if ($row_cnt <= 2) {?>
					<div id = "tabledisplay" style="overflow:auto; height:150px;">
				<?}
				else {
					?>
					<div id = "tabledisplay" style="overflow:auto; height:250px;">
					<?
				}?>
				<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="85%" class="thetable">
					<tr>
						<th align="center" width="5%"><strong>No.</strong></th>
						<th align="left" width="10%"><strong>Thesis / Project ID</strong></th>
						<th align="left" width="30%"><strong>Thesis Title</strong></th>
						<th align="left" width="15%"><strong>Supervisor / Co-Supervisor</strong></th>
						<th align="left" width="10%"><strong>Reference No</strong></th>
						<th align="center" width="10%"><strong>Defense Date</strong></th>
						<th align="center" width="5%"><strong><label>History Detail</label></strong></th>
					</tr>
					<?
					if ($row_cnt>0) {
					$no=0;
					for ($i=0; $i<$no2; $i++){	
					?>
						<tr>
							<td align="center"><?=++$no?>.</td>
							<td><label><?=$thesisIdArray[$i]?></label></td>
							<?
							$thesisTitleArray1[$i] = strip_tags($thesisTitleArray[$i]);
						
							if (strlen($thesisTitleArray1[$i]) > 100) 
							{
							
								$more[$i] = "<a href=\"#\" value=\".$thesisId[$i].\" title=\"".preg_replace('/"/',"'",$thesisTitleArray[$i])."\">... Read more</a>";
							}
							//$string;
							$thesisTitleCut[$i] = substr($thesisTitleArray1[$i], 0, 100);
							?>
							<td><label><?=$thesisTitleCut[$i]?></label><?=$more[$i]?></td>
							<td><label><?=$employeeNameArray[$i]?><br/>(<?=$employeeIdArray[$i]?>)</label></td>
							<td><label><?=$referenceNoArray[$i]?></label></td>
							<td align="center"><label><?=$defenseDateArray[$i]?></label></td>
							<td align="center"><label><a href="../defense/defense_history_detail.php?ref=<?=$referenceNoArray[$i]?>&eid=<?=$employeeIdArray[$i]?>">View</a></label></td>
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
			</div>
		</form>
	</body>
</html>




