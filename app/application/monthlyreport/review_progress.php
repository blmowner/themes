<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: review_progress.php
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

if(isset($_POST['btnSearch']) && ($_POST['btnSearch'] <> "")) {
	
	$searchThesisId = $_POST['searchThesisId'];
	$searchStudent = $_POST['searchStudent'];
	$searchStudentName = $_POST['searchStudentName'];
	$msg = Array();
	if ($searchThesisId!="") 
	{
		$tmpSearchThesisId = " AND pp.pg_thesis_id = '$searchThesisId'";
	}
	else 
	{
		$tmpSearchThesisId="";
	}
	if ($searchStudent!="") 
	{
		$tmpSearchStudent = " AND pt.student_matrix_no = '$searchStudent'";
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
	
	$sql1 = "SELECT pt.id AS thesis_id, pt.student_matrix_no, pp.id AS proposal_id, a.reference_no
	FROM pg_thesis pt 
	LEFT JOIN pg_proposal pp ON (pp.pg_thesis_id=pt.id) 
	LEFT JOIN ref_proposal_status rps ON (rps.id = pp.verified_status) 
	LEFT JOIN ref_proposal_status rps2 ON (rps2.id = pp.status) 
	LEFT JOIN pg_progress a ON (a.pg_thesis_id = pt.id)
	LEFT JOIN pg_progress_detail b ON (b.pg_progress_id = a.id)
	LEFT JOIN ref_proposal_status c ON (c.id = a.status)
	WHERE pp.verified_status in ('APP','AWC')
	AND b.status <> 'APP'"
	.$tmpSearchThesisId." "
	.$tmpSearchStudent." "."
	AND pp.archived_status is null
	AND pt.ref_thesis_status_id_proposal in ('APP','AWC','APC')
	AND a.archived_status is null
	AND b.archived_status is null
	AND b.pg_employee_empid = '$user_id'
	ORDER BY pt.student_matrix_no, STR_TO_DATE(a.report_year, '%Y'), STR_TO_DATE(a.report_month,'%M')";

	$result1 = $dba->query($sql1); 
	$dba->next_record();
	
	$studentMatrixNoArray = Array();
	$thesisIdArray = Array();
	$proposalIdArray = Array();
	$referenceNoArray = Array();
	
	$no1=0;
	$no2=0;
	$no3=0;
	
	if (mysql_num_rows($result1) > 0){
		do {
			$studentMatrixNoArray[$no1] = $dba->f('student_matrix_no');
			$thesisIdArray[$no1] = $dba->f('thesis_id');
			$proposalIdArray[$no1] = $dba->f('proposal_id');
			$referenceNoArray[$no1] = $dba->f('reference_no');
			$no1++;
		} while ($dba->next_record());
		
		$studentNameArray = Array();
		for ($i=0; $i<$no1; $i++){
			if (substr($studentMatrixNoArray[$i],0,2) != '07') { 
				$dbConnStudent= $dbc; 
			} 
			else { 
				$dbConnStudent=$dbc1; 
			}
			$sql9 = "SELECT name
			FROM student
			WHERE matrix_no = '$studentMatrixNoArray[$i]'"
			.$tmpSearchStudentName." ";
			$result9 = $dbConnStudent->query($sql9); 
			
			$dbConnStudent->next_record();
			if (mysql_num_rows($result9)>0) {
				$studentNameArray[$no2] = $dbConnStudent->f('name');
				$studentMatrixNoArray[$no2] = $studentMatrixNoArray[$i];
				$thesisIdArray[$no2] = $thesisIdArray[$i];
				$proposalIdArray[$no2] = $proposalIdArray[$i];
				$referenceNoArray[$no2] = $referenceNoArray[$i];
				$no2++;
			}
		}
		
		for ($j=0; $j<$no2; $j++){
			$sql3 = "SELECT id
			FROM pg_supervisor
			WHERE pg_thesis_id = '$thesisIdArray[$j]'
			AND pg_student_matrix_no = '$studentMatrixNoArray[$j]'
			AND pg_employee_empid = '$user_id'
			AND acceptance_status IS NOT NULL 
			AND ref_supervisor_type_id IN ('SV','CS','XS') 
			AND STATUS = 'A'";
			
			$result_sql3 = $dba->query($sql3); 
			$dba->next_record();
			
			if (mysql_num_rows($result_sql3)>0) {
				$studentNameArray[$no3] = $studentNameArray[$j];
				$studentMatrixNoArray[$no3] = $studentMatrixNoArray[$j];
				$thesisIdArray[$no3] = $thesisIdArray[$j];
				$proposalIdArray[$no3] = $proposalIdArray[$j];
				$referenceNoArray[$no3] = $referenceNoArray[$j];
				$no3++;
			}
		}
		
		if ($no3 == 0) {			
			$msg[] = "<div class=\"error\"><span>No record(s) found.</span></div>";			
		}		
	}
	else {
		$msg[] = "<div class=\"error\"><span>No record(s) found.</span></div>";
	}	
	$row_cnt = $no3;			
}
else 
{
	$sql1 = "SELECT pt.id AS thesis_id, pt.student_matrix_no, pp.id AS proposal_id, a.reference_no
	FROM pg_thesis pt 
	LEFT JOIN pg_proposal pp ON (pp.pg_thesis_id=pt.id) 
	LEFT JOIN ref_proposal_status rps ON (rps.id = pp.verified_status) 
	LEFT JOIN ref_proposal_status rps2 ON (rps2.id = pp.status) 
	LEFT JOIN pg_progress a ON (a.pg_thesis_id = pt.id)
	LEFT JOIN pg_progress_detail b ON (b.pg_progress_id = a.id)
	LEFT JOIN ref_proposal_status c ON (c.id = a.status)
	WHERE pp.verified_status in ('APP','AWC')
	AND pp.archived_status is null
	AND pt.ref_thesis_status_id_proposal in ('APP','AWC','APC')
	AND b.status <> 'APP'
	AND a.archived_status is null
	AND b.archived_status is null
	AND b.pg_employee_empid = '$user_id'
	ORDER BY pt.student_matrix_no, STR_TO_DATE(a.report_year, '%Y'), STR_TO_DATE(a.report_month,'%M')";

	$result1 = $dba->query($sql1); 
	$dba->next_record();
	
	$studentMatrixNoArray = Array();
	$thesisIdArray = Array();
	$proposalIdArray = Array();
	$referenceNoArray = Array();
	
	$no1=0;
	$no2=0;
	$no3=0;
	if (mysql_num_rows($result1) > 0){
		do {
			$studentMatrixNoArray[$no1] = $dba->f('student_matrix_no');
			$thesisIdArray[$no1] = $dba->f('thesis_id');
			$proposalIdArray[$no1] = $dba->f('proposal_id');
			$referenceNoArray[$no1] = $dba->f('reference_no');
			$no1++;
		} while ($dba->next_record());
		$studentNameArray = Array();
		for ($i=0; $i<$no1; $i++){
			$sql9 = "SELECT name
					FROM student
					WHERE matrix_no = '$studentMatrixNoArray[$i]'";
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
				$studentMatrixNoArray[$no2] = $studentMatrixNoArray[$i];
				$thesisIdArray[$no2] = $thesisIdArray[$i];
				$proposalIdArray[$no2] = $proposalIdArray[$i];
				$referenceNoArray[$no2] = $referenceNoArray[$i];
				$no2++;
			}
		}

		for ($j=0; $j<$no2; $j++){
			$sql3 = "SELECT id
			FROM pg_supervisor
			WHERE pg_thesis_id = '$thesisIdArray[$j]'
			AND pg_student_matrix_no = '$studentMatrixNoArray[$j]'
			AND pg_employee_empid = '$user_id'
			AND acceptance_status IS NOT NULL 
			AND ref_supervisor_type_id IN ('SV','CS','XS') 
			AND STATUS = 'A'";
			
			$result_sql3 = $dba->query($sql3); 
			$dba->next_record();
			
			if (mysql_num_rows($result_sql3)>0) {
				$studentNameArray[$no3] = $studentNameArray[$j];
				$studentMatrixNoArray[$no3] = $studentMatrixNoArray[$j];
				$thesisIdArray[$no3] = $thesisIdArray[$j];
				$proposalIdArray[$no3] = $proposalIdArray[$j];
				$referenceNoArray[$no3] = $referenceNoArray[$j];
				$no3++;
			}
		}

		$row_cnt = $no3;
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
	
	<SCRIPT LANGUAGE="JavaScript">

	function respConfirm () {
		var confirmSubmit = confirm("Click OK if confirm to submit or CANCEL to return back.");
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



	
	
		<fieldset>
		<legend><strong>List of Student</strong></legend>
			<?
			$curdatetime = date("Y-m-d H:i:s");
			$time=strtotime($curdatetime);
			$month=date("F",$time);
			$year=date("Y",$time);?>
			<table>
				<tr>							
					<td>Please enter searching criteria below:-</td>
				</tr>
			</table>
			<table>
				<tr>
					<td>Thesis / <br/>Project ID</td>
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
			</br>
			
			<table>
				<tr>							
					<td>Searching Results:- <?=$row_cnt ?> record(s) found.</td>
				</tr>
			</table>
			<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="95%" class="thetable">			
				<tr>
					<th width="5%">No</th>					
					<th width="10%" align="left">Staff Role</th>
					<th width="10%" align="left">Student Matrix No.</th>
					<th width="20%" align="left">Student Name</th>
					<th width="10%" align="left">Thesis / Project ID</th>
					<th width="20%" align="left">Month<br>Submission Date<br>Reference No</th>						
					<th width="15%" align="left">Status</th>	
					<th width="5%">Action</th>
				</tr>
				<?if ($row_cnt > 0 ) {?>	
				<?
				$no=0;
				for ($i=0; $i<$no3; $i++){
					if($i % 2) $color ="first-row"; else $color = "second-row";?>
					<tr class="<?=$color?>">
						<td align="center"><?=$no+1;?>.</td>
						<?
						$sql_supervisor = " SELECT a.ref_supervisor_type_id, d.description as supervisor_type
						FROM pg_supervisor a 
						LEFT JOIN ref_supervisor_type d ON (d.id = a.ref_supervisor_type_id) 
						LEFT JOIN pg_thesis f ON (f.student_matrix_no = a.pg_student_matrix_no)
						LEFT JOIN pg_proposal g ON (g.pg_thesis_id = f.id)
						WHERE a.pg_student_matrix_no='$studentMatrixNoArray[$i]'
						AND g.pg_thesis_id = '$thesisIdArray[$i]'
						AND g.id = '$proposalIdArray[$i]'
						AND a.pg_employee_empid = '$user_id'
						AND a.acceptance_status is not null
						AND a.ref_supervisor_type_id in ('SV','CS','XS')
						AND g.verified_status in ('APP','AWC')
						/*AND a.status = 'A'*/
						AND g.status in ('APP','APC')
						AND g.archived_status IS NULL
						ORDER BY d.seq, a.ref_supervisor_type_id";

						$result_sql_supervisor = $dbg->query($sql_supervisor); 
						$dbg->next_record();
						$supervisorTypeId=$dbg->f('ref_supervisor_type_id');
						$supervisorType=$dbg->f('supervisor_type');
						
						//echo $row_cnt = mysql_num_rows($result_sql_supervisor);
						
						if ($supervisorTypeId == 'XS') {
						?>
							<td><label><span style="color:#FF0000"><?=$supervisorType?></span></label></td>
						<?}
						else {
							?>
							<td><label><?=$supervisorType?></label></td>
							<?
						}?>
						<td><label><?=$studentMatrixNoArray[$i]?></label></td>
						<td><label><?=$studentNameArray[$i]?></label></td>
						<td><label><?=$thesisIdArray[$i]?></label></td>
						
						<?
						$sql2 = "SELECT a.id, a.report_month, a.report_year, DATE_FORMAT(a.submit_date, '%d-%b-%Y %h:%i %p') as submit_date, 
						a.reference_no, a.status as progress_status, b.status as progress_detail_status, c1.description as progress_desc,
						c2.description as progress_detail_desc
						FROM pg_progress a
						LEFT JOIN pg_progress_detail b ON (b.pg_progress_id = a.id)
						LEFT JOIN ref_proposal_status c1 ON (c1.id = a.status)
						LEFT JOIN ref_proposal_status c2 ON (c2.id = b.status)
						WHERE a.student_matrix_no = '$studentMatrixNoArray[$i]'
						AND a.pg_thesis_id = '$thesisIdArray[$i]'
						AND a.pg_proposal_id = '$proposalIdArray[$i]'
						AND a.reference_no = '$referenceNoArray[$i]'
						AND b.pg_employee_empid = '$user_id'
						AND a.archived_status is null
						AND b.archived_status is null";
						
						$result2 = $dbg->query($sql2); 
						$dbg->next_record();
						$id=$dbg->f('id');
						
						
						$progressDesc=$dbg->f('progress_desc');
						$progressDetailDesc=$dbg->f('progress_detail_desc');
						$submitDate=$dbg->f('submit_date');
						$referenceNo=$dbg->f('reference_no');
						$progressStatus=$dbg->f('progress_status');
						$progressDetailStatus=$dbg->f('progress_detail_status');
						$reportMonth=$dbg->f('report_month');
						$reportYear=$dbg->f('report_year');
						
						$row_cnt2 = mysql_num_rows($result2);						
						?>						
						<td><label><strong><?=$reportMonth?> <?=$reportYear?></strong></br><?=$submitDate?></br><?=$referenceNo?></br> </label></td>
						<td><label><?=$progressDetailDesc?></label></td>			
						<?if ($progressDetailStatus =='IN1' || $progressDetailStatus =='SV1') {
							if ($supervisorTypeId != 'XS') {?>										
								<td><input type="button" name="btnReview" value="Update" onClick="javascript:document.location.href='../monthlyreport/review_progress_detail.php?id=<?=$id?>&mn=<?=$studentMatrixNoArray[$i]?>&tid=<?=$thesisIdArray[$i]?>&pid=<?=$proposalIdArray[$i]?>&ref=<?=$referenceNoArray[$i]?>';" /></td>
							<?}
							else {?>
								<td><label>Note: Please check Staff Role status.</label><br/><br/>
								<input type="button" name="btnReview" value="Update" onClick="javascript:document.location.href='../monthlyreport/review_progress_detail.php?id=<?=$id?>&mn=<?=$studentMatrixNoArray[$i]?>&tid=<?=$thesisIdArray[$i]?>&pid=<?=$proposalIdArray[$i]?>&ref=<?=$referenceNoArray[$i]?>';" /></td>
								<?
							}
						}
						else {?>
							<td><input type="button" name="btnReview" value="View" onClick="javascript:document.location.href='../monthlyreport/review_progress_detail_view.php?id=<?=$id?>&mn=<?=$studentMatrixNoArray[$i]?>&tid=<?=$thesisIdArray[$i]?>&pid=<?=$proposalIdArray[$i]?>&ref=<?=$referenceNoArray[$i]?>';" /></td>
						<?}?>
					</tr>
					<?
				$no++;	
				}
				?>
		</table>
		</fieldset>
		

	<?}
	else {
	?>
		<table>
			<tr>
				<td>No record found!</td>
			</tr>
		</table>	
		<?
	}?>	
		
</body>
</html>




