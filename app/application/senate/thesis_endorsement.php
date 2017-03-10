<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: thesis_endorsement.php
//
// Created by: Zuraimi
// Created Date: 15-October-2015
// Modified by: Zuraimi
// Modified Date: 15-October-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

session_start();
$user_id=$_SESSION['user_id'];

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

if(isset($_POST['btnEndorsed']) && ($_POST['btnEndorsed'] <> "")) 
{
	$approvalBox=$_POST['approvalBox'];
	$curdatetime = date("Y-m-d H:i:s");
	
	if (sizeof($_POST['approvalBox'])== 0) {
		$msg[] = "<div class=\"error\"><span>Please select the required thesis before proceed with the endorsement.</span></div>";	
	}
	
	if(empty($msg))  
	{
		while (list ($key,$val) = @each ($approvalBox)) {
			$sql2 = "SELECT id, pg_viva_id, pg_work_id, pg_work_evaluation_id, reference_no, student_matrix_no, pg_thesis_id, 
			pg_proposal_id, pg_calendar_id, status, submit_status, submit_date, remarks, respond_status, 
			insert_by, insert_date, modify_by, modify_date
			FROM pg_senate
			WHERE id ='$val'
			AND submit_status = 'IN1'
			AND status = 'A'
			AND archived_status IS NULL";
			
			$db->query($sql2);
			$db->next_record();
			
			$senateId = $db->f('id');
			$vivaId = $db->f('pg_viva_id');
			$workId = $db->f('pg_work_id');
			$workEvaluationId = $db->f('pg_work_evaluation_id');
			$referenceNo = $db->f('reference_no');
			$studentMatrixNo = $db->f('student_matrix_no');
			$thesisId = $db->f('pg_thesis_id');
			$proposalId = $db->f('pg_proposal_id');
			$calendarId = $db->f('pg_calendar_id');
			$status = $db->f('status');
			//$submitStatus = $db->f('submit_status');
			//$submitDate = $db->f('submit_date');
			$remarks = $db->f('remarks');
			//$respondStatus = $db->f('respond_status');
			$insertBy = $db->f('insert_by');
			$insertDate = $db->f('insert_date');
			$modifyBy = $db->f('modify_by');
			$modifyDate = $db->f('modify_date');
			
			$newSenateId = runnum('id','pg_senate');
			
			$sql3 = "INSERT INTO pg_senate
			(id, pg_viva_id, pg_work_id, pg_work_evaluation_id, reference_no, student_matrix_no, pg_thesis_id, pg_proposal_id, 
			pg_calendar_id, status, submit_status, submit_date, remarks, respond_status, insert_by, insert_date, modify_by, modify_date)
			VALUES ('$newSenateId', '$vivaId', '$workId', '$workEvaluationId', '$referenceNo', '$studentMatrixNo', 
			'$thesisId', '$proposalId', '$calendarId', '$status', 'END', '$curdatetime', '$remarks', 'Y', 
			'$insertBy', '$insertDate', '$modifyBy','$modifyDate')";
			
			$db->query($sql3);
			
			$sql4 = "UPDATE pg_senate
			SET archived_status = 'ARC', archived_date = '$curdatetime'
			WHERE id = '$val'
			AND submit_status = 'IN1'
			AND status = 'A'
			AND archived_status IS NULL";
			
			$db->query($sql4);
		}
		
		$msg[] = "<div class=\"success\"><span>The selected thesis has been endorsed successfully.</span></div>";	
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
		$tmpSearchStudent = " AND a.student_matrix_no = '$searchStudent'";
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
	
	$sql1 = "SELECT a.id, a.student_matrix_no, a.pg_thesis_id, a.pg_proposal_id, a.reference_no, 
	a.submit_status, d.description as submit_status_desc,
	a.pg_work_id, a.pg_calendar_id, 
	c.final_result, e.description AS final_result_desc,
	DATE_FORMAT(c.final_result_date,'%d-%b-%Y %h:%i%p') AS final_result_date,
	DATE_FORMAT(a.submit_date,'%d-%b-%Y %h:%i%p') AS submit_date
	FROM pg_senate a
	LEFT JOIN pg_viva b ON (b.id = a.pg_viva_id)
	LEFT JOIN pg_evaluation_viva c ON (c.pg_viva_id = b.id)
	LEFT JOIN ref_proposal_status d ON (d.id = a.submit_status)
	LEFT JOIN ref_recommendation e ON (e.id = c.final_result)
	WHERE a.status = 'A'"
	.$tmpSearchThesisId." "
	.$tmpSearchStudent." "."	
	AND a.respond_status IN ('N', 'Y')
	AND c.respond_status = 'SUB'
	AND c.status = 'A'
	AND a.archived_status IS NULL
	ORDER BY a.student_matrix_no, a.reference_no DESC";

	$result1 = $dba->query($sql1); 
	$dba->next_record();
	
	$senateIdArray = Array();
	$studentMatrixNoArray = Array();
	$thesisIdArray = Array();
	$proposalIdArray = Array();
	$referenceNoArray = Array();
	$workIdArray = Array();
	$submitStatusArray = Array();
	$submitDateArray = Array();
	$submitStatusDescArray = Array();
	$calendarIdArray = Array();
	$finalResultArray = Array();
	$finalResultDescArray = Array();
	$finalResultDateArray = Array();
	
	$no1=0;
	$no2=0;
	
	if (mysql_num_rows($result1) > 0){
		do {
			$senateIdArray[$no1] = $dba->f('id');
			$studentMatrixNoArray[$no1] = $dba->f('student_matrix_no');
			$thesisIdArray[$no1] = $dba->f('pg_thesis_id');
			$proposalIdArray[$no1] = $dba->f('pg_proposal_id');
			$referenceNoArray[$no1] = $dba->f('reference_no');
			$workIdArray[$no1] = $dba->f('pg_work_id');
			$submitStatusArray[$no1] = $dba->f('submit_status');
			$submitDateArray[$no1] = $dba->f('submit_date');
			$submitStatusDescArray[$no1] = $dba->f('submit_status_desc');
			$calendarIdArray[$no1] = $dba->f('pg_calendar_id');
			$finalResultArray[$no1] = $dba->f('final_result');
			$finalResultDescArray[$no1] = $dba->f('final_result_desc');
			$finalResultDateArray[$no1] = $dba->f('final_result_date');
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
				$senateIdArray[$no2] = $senateIdArray[$i];
				$studentMatrixNoArray[$no2] = $studentMatrixNoArray[$i];
				$thesisIdArray[$no2] = $thesisIdArray[$i];
				$proposalIdArray[$no2] = $proposalIdArray[$i];
				$referenceNoArray[$no2] = $referenceNoArray[$i];
				$workIdArray[$no2] = $workIdArray[$i];
				$submitStatusArray[$no2] = $submitStatusArray[$i];
				$submitDateArray[$no2] = $submitDateArray[$i];
				$submitStatusDescArray[$no2] = $submitStatusDescArray[$i];
				$calendarIdArray[$no2] = $calendarIdArray[$i];
				$finalResultArray[$no2] = $finalResultArray[$i];
				$finalResultDescArray[$no2] = $finalResultDescArray[$i];
				$finalResultDateArray[$no2] = $finalResultDateArray[$i];
				$no2++;
			}
		}
		if ($no2 == 0) {			
			$msg[] = "<div class=\"error\"><span>No record(s) found.</span></div>";			
		}		
	}
	else {
		$msg[] = "<div class=\"error\"><span>No record(s) found.</span></div>";
	}	
	$row_cnt = $no2;			
}
else 
{
	$sql1 = "SELECT a.id, a.student_matrix_no, a.pg_thesis_id, a.pg_proposal_id, a.reference_no, 
	a.submit_status, d.description as submit_status_desc,
	a.pg_work_id, a.pg_calendar_id, 
	c.final_result, e.description AS final_result_desc,
	DATE_FORMAT(c.final_result_date,'%d-%b-%Y %h:%i%p') AS final_result_date,
	DATE_FORMAT(a.submit_date,'%d-%b-%Y %h:%i%p') AS submit_date
	FROM pg_senate a
	LEFT JOIN pg_viva b ON (b.id = a.pg_viva_id)
	LEFT JOIN pg_evaluation_viva c ON (c.pg_viva_id = b.id)
	LEFT JOIN ref_proposal_status d ON (d.id = a.submit_status)
	LEFT JOIN ref_recommendation e ON (e.id = c.final_result)
	WHERE a.status = 'A'
	AND a.respond_status IN ('N', 'Y')
	AND c.respond_status = 'SUB'
	AND c.status = 'A'
	AND a.archived_status IS NULL
	ORDER BY a.student_matrix_no, a.reference_no DESC";

	$result1 = $dba->query($sql1); 
	$dba->next_record();
	
	$senateIdArray = Array();
	$studentMatrixNoArray = Array();
	$thesisIdArray = Array();
	$proposalIdArray = Array();
	$referenceNoArray = Array();
	$workIdArray = Array();
	$submitStatusArray = Array();
	$submitDateArray = Array();
	$submitStatusDescArray = Array();
	$calendarIdArray = Array();
	$finalResultArray = Array();
	$finalResultDescArray = Array();
	$finalResultDateArray = Array();
	
	$no1=0;
	$no2=0;
	if (mysql_num_rows($result1) > 0){
		do {
			$senateIdArray[$no1] = $dba->f('id');
			$studentMatrixNoArray[$no1] = $dba->f('student_matrix_no');
			$thesisIdArray[$no1] = $dba->f('pg_thesis_id');
			$proposalIdArray[$no1] = $dba->f('pg_proposal_id');
			$referenceNoArray[$no1] = $dba->f('reference_no');
			$workIdArray[$no1] = $dba->f('pg_work_id');
			$submitStatusArray[$no1] = $dba->f('submit_status');
			$submitDateArray[$no1] = $dba->f('submit_date');
			$submitStatusDescArray[$no1] = $dba->f('submit_status_desc');
			$calendarIdArray[$no1] = $dba->f('pg_calendar_id');
			$finalResultArray[$no1] = $dba->f('final_result');
			$finalResultDescArray[$no1] = $dba->f('final_result_desc');
			$finalResultDateArray[$no1] = $dba->f('final_result_date');
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
				$senateIdArray[$no2] = $senateIdArray[$i];
				$studentMatrixNoArray[$no2] = $studentMatrixNoArray[$i];
				$thesisIdArray[$no2] = $thesisIdArray[$i];
				$proposalIdArray[$no2] = $proposalIdArray[$i];
				$referenceNoArray[$no2] = $referenceNoArray[$i];
				$workIdArray[$no2] = $workIdArray[$i];
				$submitStatusArray[$no2] = $submitStatusArray[$i];
				$submitDateArray[$no2] = $submitDateArray[$i];
				$submitStatusDescArray[$no2] = $submitStatusDescArray[$i];
				$calendarIdArray[$no2] = $calendarIdArray[$i];
				$finalResultArray[$no2] = $finalResultArray[$i];
				$finalResultDescArray[$no2] = $finalResultDescArray[$i];
				$finalResultDateArray[$no2] = $finalResultDateArray[$i];
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
					<td >Thesis / Project ID</td>
					<td>:</td>
					<td ><input type="text" name="searchThesisId" size="15" id="searchThesisId" value="<?=$searchThesisId;?>"/></td>
				</tr>
				<tr>
					<td >Matrix No</td>
					<td>:</td>
					<td><input type="text" name="searchStudent" size="15" id="searchStudent" value="<?=$searchStudent;?>"/></td>					
				</tr>
				<tr>
					<td >Student Name</td>
					<td>:</td>
					<td><input type="text" name="searchStudentName" size="30" id="searchStudentName" value="<?=$searchStudentName;?>"/> <input type="submit" name="btnSearch" value="Search" /> <strong> Note:</strong> If no entry is provided, it will search all.</td>								
				</tr>
			</table>

			</br>
			
			<table>
				<tr>							
					<td>Searching Results:- <?=$row_cnt ?> record(s) found.</td>
				</tr>
			</table>
			<? if($row_cnt == 2)
				{?>
					<div id = "tabledisplay" style="overflow:auto; height:120px;">
				<? }
				else 
				{ ?>
					<div id = "tabledisplay" style="overflow:auto; height:200px;">

			<? }?>		
			<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="100%" class="thetable">			
				<tr>
					<th width="5%" align="center"><strong>Tick</strong></th>
					<th width="5%" align="center"><strong>No.</strong></th>	
					<th width="20%"  align="left">Student Name</th>
					<th width="10%">Thesis / Project ID</th>
					<th width="20%" align="left">Evaluation Report</th>						
					<th width="15%"  align="left">VIVA Status</th>
					<th width="20%"  align="left">Senate Status</th>
					<th width="5%">Action</th>
				</tr>
				<?if ($row_cnt > 0 ) {?>	
				<?
				$no=0;
				for ($i=0; $i<$no2; $i++){?>
					<tr>
						<? if ($submitStatusArray[$i]=='END'){
							?><td align="center"><input name="approvalBox[]" type="checkbox" value="<?=$senateIdArray[$i];?>" disabled="disabled" /></td><?							
						}
							else {
								?><td align="center"><input name="approvalBox[]" type="checkbox" value="<?=$senateIdArray[$i];?>" /></td><?
						}
						?>	
						<td align="center"><?=$no+1;?>.</td>
						<td><label><?=$studentNameArray[$i]?><br><?=$studentMatrixNoArray[$i]?></label></td>
						<td align="center"><label><?=$thesisIdArray[$i]?></label></td>
						<?
						$sql3 = "SELECT DATE_FORMAT(a.defense_date,'%d-%b-%Y') as defense_date,
						DATE_FORMAT(a.defense_stime,'%h:%i%p') as defense_stime,
						DATE_FORMAT(a.defense_etime,'%h:%i%p') as defense_etime, a.venue,
						e.description as session_type_desc
						FROM pg_calendar a
						LEFT JOIN pg_senate b ON (b.pg_calendar_id = a.id)
						LEFT JOIN ref_session_type e ON (e.id = a.ref_session_type_id)
						WHERE a.id = '$calendarIdArray[$i]'
						AND a.student_matrix_no = '$studentMatrixNoArray[$i]'
						AND a.thesis_id = '$thesisIdArray[$i]'
						AND a.status = 'A'
						AND b.archived_status IS NULL";
						
						$result_sql3 = $dba->query($sql3); 
						$dba->next_record();
						
						$recommendedId = $dba->f('id');
						$defenseDate = $dba->f('defense_date');
						$defenseSTime = $dba->f('defense_stime');
						$defenseETime = $dba->f('defense_etime');
						$sessionTypeDesc = $dba->f('session_type_desc');	
						$venue = $dba->f('venue');		
						?>
						<td><label><?=$sessionTypeDesc?> - <?=$defenseDate?>, <?=$defenseSTime?>-<?=$defenseETime?></label></td>
						<td><label><?=$finalResultDescArray[$i]?><br><?=$finalResultDateArray[$i]?></label></td>
						<td><label><?=$submitStatusDescArray[$i]?></br><?=$referenceNoArray[$i]?>
						<br/><?=$submitDateArray[$i]?></br> </label></td>
						<td align="center">			
						<?if ($submitStatusArray[$i] == 'IN1') {?>										
							<a href="../senate/thesis_endorsement_detail.php?id=<?=$senateIdArray[$i]?>&mn=<?=$studentMatrixNoArray[$i]?>&tid=<?=$thesisIdArray[$i]?>&pid=<?=$proposalIdArray[$i]?>&ref=<?=$referenceNoArray[$i]?>&cid=<?=$calendarIdArray[$i]?>">Remark</a></td>
							<?
						}
						else {
							?>
								<a href="../senate/thesis_endorsement_view.php?id=<?=$senateIdArray[$i]?>&mn=<?=$studentMatrixNoArray[$i]?>&tid=<?=$thesisIdArray[$i]?>&pid=<?=$proposalIdArray[$i]?>&ref=<?=$referenceNoArray[$i]?>&cid=<?=$calendarIdArray[$i]?>">View</a></td>
							<?
						}?>
						</td>
					</tr>
					<?
				$no++;	
				}
				?>
		</table>
		</div>
		</fieldset>
		<table>
			<tr>	
				<td><input type="submit" name="btnEndorsed" id="btnEndorsed" value="Endorsed" onClick="return respConfirm()" /> <strong>Note:</strong> Ensure the required thesis above has been selected before click Submit.</td>
			</tr>
		</table>
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




