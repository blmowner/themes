<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: review_work.php
//
// Created by: Zuraimi
// Created Date: 23-August-2015
// Modified by: Zuraimi
// Modified Date: 23-August-2015
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
	
	$sql1 = "SELECT a.pg_thesis_id AS thesis_id, a.student_matrix_no, a.pg_proposal_id AS proposal_id, a.reference_no 
	FROM pg_work a 
	LEFT JOIN pg_work_detail b ON (b.pg_work_id = a.id) 
	LEFT JOIN ref_proposal_status c ON (c.id = a.status) 
	WHERE /*a.archived_status is null*/"
	.$tmpSearchThesisId." "
	.$tmpSearchStudent." "." 
	/*AND*/ b.archived_status is null 
	AND b.pg_employee_empid = '$user_id'";
	
	$result1 = $dba->query($sql1); 
	$dba->next_record();
	
	$studentMatrixNoArray = Array();
	$thesisIdArray = Array();
	$proposalIdArray = Array();
	$referenceNoArray = Array();
	
	$no1=0;
	$no2=0;
	
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
	}	
	$row_cnt = $no2;			
}
else 
{
	$sql1 = "SELECT a.pg_thesis_id AS thesis_id, a.student_matrix_no, a.pg_proposal_id AS proposal_id, a.reference_no 
	FROM pg_work a 
	LEFT JOIN pg_work_detail b ON (b.pg_work_id = a.id) 
	LEFT JOIN ref_proposal_status c ON (c.id = a.status) 
	WHERE /*a.archived_status is null 
	AND*/ b.archived_status is null 
	AND b.pg_employee_empid = '$user_id'
	ORDER BY a.submit_date DESC";
	
	$result1 = $dba->query($sql1); 
	$dba->next_record();
	
	$studentMatrixNoArray = Array();
	$thesisIdArray = Array();
	$proposalIdArray = Array();
	$referenceNoArray = Array();
	
	$no1=0;
	$no2=0;
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
	}
	$row_cnt = $no2;
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
			</br>
			
			<table>
				<tr>							
					<td>Searching Results:- <?=$row_cnt ?> record(s) found.</td>
				</tr>
			</table>
			<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="100%" class="thetable">			
				<tr>
					<th width="5%">No</th>					
					<th width="10%" align="left">Staff Role</th>
					<th width="20%"  align="left">Student Name</th>
					<th width="10%">Thesis / Project ID</th>
					<th width="20%" align="left">Evaluation Schedule</th>						
					<th width="15%"  align="left">Proposed Status</th>					
					<th width="15%"  align="left">Evaluation Status</th>
					<th width="5%">Action</th>
				</tr>
				<?if ($row_cnt > 0 ) {?>	
				<?
				$no=0;
				for ($i=0; $i<$no2; $i++){
					if($i % 2) $color ="first-row"; else $color = "second-row";?>
					<tr class="<?=$color?>">
						<td align="center"><?=$no+1;?>.</td>
						<td>
						<?
						$sql_supervisor = " SELECT a.ref_supervisor_type_id, d.description as supervisor_type, a.role_status,
						h.description as role_desc
						FROM pg_supervisor a 
						LEFT JOIN ref_supervisor_type d ON (d.id = a.ref_supervisor_type_id) 
						LEFT JOIN pg_thesis f ON (f.student_matrix_no = a.pg_student_matrix_no)
						LEFT JOIN pg_proposal g ON (g.pg_thesis_id = f.id)
						LEFT JOIN ref_role_status h ON (h.id = a.role_status)
						WHERE a.pg_student_matrix_no='$studentMatrixNoArray[$i]'
						AND g.pg_thesis_id = '$thesisIdArray[$i]'
						AND g.id = '$proposalIdArray[$i]'
						AND a.pg_employee_empid = '$user_id'
						AND a.acceptance_status is not null
						AND a.ref_supervisor_type_id in ('SV','CS','XS')
						AND g.verified_status in ('APP','AWC')
						AND g.status in ('APP','APC')
						AND g.archived_status IS NULL
						ORDER BY d.seq, a.ref_supervisor_type_id";

						
						$result_sql_supervisor = $dbg->query($sql_supervisor); //echo $sql;
						$dbg->next_record();
						$supervisorTypeId=$dbg->f('ref_supervisor_type_id');
						$supervisorType=$dbg->f('supervisor_type');
						$roleStatus=$dbg->f('role_status');
						$roleDesc=$dbg->f('role_desc');
						
						if ($supervisorTypeId == 'XS') {
						?>
							<label><span style="color:#FF0000"><?=$supervisorType?></span></label>
						<?}
						else {
							?>
							<label><?=$supervisorType?></label>
							<?
						}?>
						<br/><?=$roleDesc?></td>
						<td><label><?=$studentNameArray[$i]?><br><?=$studentMatrixNoArray[$i]?></label></td>
						<td align="center"><label><?=$thesisIdArray[$i]?></label></td>
						
						<?
						$sql2 = "SELECT a.id, d.id as defense_calendar_id,
						DATE_FORMAT(a.submit_date, '%d-%b-%Y %h:%i%p') as submit_date, 
						DATE_FORMAT(d.defense_date, '%d-%b-%Y') as work_date,
						DATE_FORMAT(d.defense_stime, '%h:%i%p') as work_stime,
						DATE_FORMAT(d.defense_etime, '%h:%i%p') as work_etime,
						a.reference_no, a.status as work_status, b.status as work_detail_status, c1.description as work_desc,
						c2.description as work_detail_desc,
						f.ref_work_marks_id, g.description as work_marks_desc, f.reference_no as reference_no_eval,
						f.status as evaluation_status, DATE_FORMAT(f.confirmed_date, '%d-%b-%Y %h:%i%p') as confirmed_date,
						DATE_FORMAT(b.responded_date, '%d-%b-%Y %h:%i%p') as responded_date,
						h.description as session_type_desc
						FROM pg_work a
						LEFT JOIN pg_work_detail b ON (b.pg_work_id = a.id)
						LEFT JOIN ref_proposal_status c1 ON (c1.id = a.status)
						LEFT JOIN ref_proposal_status c2 ON (c2.id = b.status)
						LEFT JOIN pg_calendar d ON (d.id = a.pg_calendar_id)
						LEFT JOIN pg_work_evaluation f ON (f.pg_work_id = a.id)
						LEFT JOIN ref_work_marks g ON (g.id = f.ref_work_marks_id)
						LEFT JOIN ref_session_type h ON (h.id = d.ref_session_type_id)
						WHERE a.student_matrix_no = '$studentMatrixNoArray[$i]'
						AND a.pg_thesis_id = '$thesisIdArray[$i]'
						AND a.pg_proposal_id = '$proposalIdArray[$i]'
						AND a.reference_no = '$referenceNoArray[$i]'
						AND b.pg_employee_empid = '$user_id'
						/*AND a.archived_status is null*/
						AND b.archived_status is null";
						
						$result2 = $dbg->query($sql2); 
						$dbg->next_record();
						
						$id=$dbg->f('id');
						$workDesc=$dbg->f('work_desc');
						$workDetailDesc=$dbg->f('work_detail_desc');
						$submitDate=$dbg->f('submit_date');
						$workDate=$dbg->f('work_date');
						$workSTime=$dbg->f('work_stime');
						$workETime=$dbg->f('work_etime');
						$referenceNo=$dbg->f('reference_no');
						$workStatus=$dbg->f('work_status');
						$workDetailStatus=$dbg->f('work_detail_status');
						$defenseCalendarId=$dbg->f('defense_calendar_id');
						$workMarksDesc=$dbg->f('work_marks_desc');
						$referenceNoEval=$dbg->f('reference_no_eval');
						$evaluationStatus=$dbg->f('evaluation_status');
						$confirmedDate=$dbg->f('confirmed_date');
						$respondedDate=$dbg->f('responded_date');
						$sessionTypeDesc=$dbg->f('session_type_desc');
						$row_cnt2 = mysql_num_rows($result2);						
						?>						
						<td><label><a href="javascript:void(0);" onMouseOver="toolTip('ID:<?=$defenseCalendarId?>', 100)" onMouseOut="toolTip()"><?=$sessionTypeDesc?></a> - <?=$workDate?>, <?=$workSTime?> to <?=$workETime?></strong><br/></br><?=$referenceNo?><br><?=$submitDate?><br></br><?=$workDesc?></label></td>
						<td><label><?=$workDetailDesc?><br><?=$respondedDate?></label></td>
						<td><label><?=$referenceNoEval;?><br><?=$workMarksDesc;?><br><?=$confirmedDate?></label></td>
						<td align="center">			
						<?if ($workStatus != 'REC') {
							if ($workDetailStatus =='IN1' || $workDetailStatus =='SV1') {
								if ($supervisorTypeId != 'XS') {?>										
									<a href="../work/review_work_detail.php?id=<?=$id?>&mn=<?=$studentMatrixNoArray[$i]?>&tid=<?=$thesisIdArray[$i]?>&pid=<?=$proposalIdArray[$i]?>&ref=<?=$referenceNoArray[$i]?>&rol=<?=$roleStatus?>&cid=<?=$defenseCalendarId?>" >Update</a></td>
								<?}
								else {?>
									<label>Note: Please check Staff Role status.</label><br/><br/>
									<a href="../work/review_work_detail.php?id=<?=$id?>&mn=<?=$studentMatrixNoArray[$i]?>&tid=<?=$thesisIdArray[$i]?>&pid=<?=$proposalIdArray[$i]?>&ref=<?=$referenceNoArray[$i]?>&rol=<?=$roleStatus?>&cid=<?=$defenseCalendarId?>" >Update</a></td>
									<?
								}
							}
							else {?>
								<a href="../work/review_work_detail_view.php?id=<?=$id?>&mn=<?=$studentMatrixNoArray[$i]?>&tid=<?=$thesisIdArray[$i]?>&pid=<?=$proposalIdArray[$i]?>&ref=<?=$referenceNoArray[$i]?>&rol=<?=$roleStatus?>&cid=<?=$defenseCalendarId?>" >View</a></td>
							<?}
						}
						else {?>
								<a href="../work/review_work_detail_view.php?id=<?=$id?>&mn=<?=$studentMatrixNoArray[$i]?>&tid=<?=$thesisIdArray[$i]?>&pid=<?=$proposalIdArray[$i]?>&ref=<?=$referenceNoArray[$i]?>&rol=<?=$roleStatus?>&cid=<?=$defenseCalendarId?>" >View</a></td>
						<?}?>
						</td>
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
		<br/>
		<table>				
			<tr><td><br/><span style="color:#FF0000">Notes:</span><br/>
				Possible Reasons:-<br/>
				1. The Work Completion could be pending submission by the Student OR<br>
				2. It could be pending at Supervisor/Co-Supervisor or Evaluation Panel.</td>
			</tr>
		</table>		
		<?
	}?>	
		
</body>
</html>




