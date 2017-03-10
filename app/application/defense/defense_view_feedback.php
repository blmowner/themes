<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: defense_view_feedback.php
//
// Created by: Zuraimi
// Created Date: 07-Jul-2015
// Modified by: Zuraimi
// Modified Date: 07-Jul-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

session_start();
$user_id=$_SESSION['user_id'];
$thesisId=$_GET['tid'];
$proposalId=$_GET['pid'];
$studentMatrixNo=$_GET['mn'];
$defenseId = $_GET['id'];
$employeeId=$_GET['eid'];
$referenceNo=$_GET['ref'];
$defenseCalendarId=$_GET['cid'];

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

$sql2 = "SELECT a.id, a.reference_no, DATE_FORMAT(b.responded_date, '%d-%b-%Y') as responded_date, a.student_matrix_no, 
a.pg_thesis_id, a.pg_proposal_id, 
a.status as defense_status, 
a.insert_by, a.insert_date, a.modify_by, a.modify_date,	c1.description as defense_desc, 
b.status as defense_detail_status, c1.description as defense_detail_desc, 
b.id as defense_detail_id, a.respond_status, b.performance_status, b.work_status,
b.comment, b.other_info,
DATE_FORMAT(d.defense_date,'%d-%b-%Y') as defense_date, 
DATE_FORMAT(d.defense_stime,'%h:%i%p') as defense_stime,
DATE_FORMAT(d.defense_etime,'%h:%i%p') as defense_etime, d.venue,
e.description as session_type_desc
FROM pg_defense a
LEFT JOIN pg_defense_detail b ON (b.pg_defense_id = a.id)
LEFT JOIN ref_proposal_status c1 ON (c1.id = b.status)
LEFT JOIN pg_calendar d ON (d.id = a.pg_calendar_id)
LEFT JOIN ref_session_type e ON (e.id = d.ref_session_type_id)
WHERE a.reference_no = '$referenceNo'
AND b.pg_employee_empid = '$employeeId'
AND a.student_matrix_no = '$studentMatrixNo'
AND a.pg_thesis_id = '$thesisId'
AND a.pg_proposal_id = '$proposalId'
AND a.pg_calendar_id = '$defenseCalendarId'
AND a.archived_status IS NULL
AND b.archived_status IS NULL";
		
$result2 = $dba->query($sql2); 
$dba->next_record();
$id=$dba->f('id');
$defenseStatus=$dba->f('defense_status');
$defenseDesc=$dba->f('defense_desc');
$defenseDetailStatus=$dba->f('defense_detail_status');
$defenseDetailDesc=$dba->f('defense_detail_desc');
$defenseDate=$dba->f('defense_date');
$respondedDate=$dba->f('responded_date');
$performanceStatus=$dba->f('performance_status');
$progressWorkStatus=$dba->f('work_status');
$comment=$dba->f('comment');
$otherInfo=$dba->f('other_info');
$defenseDate = $dba->f('defense_date1');
$defenseSTime = $dba->f('defense_stime');
$defenseETime = $dba->f('defense_etime');	
$venue = $dba->f('venue');	
$sessionTypeDesc = $dba->f('session_type_desc');	
$row_cnt2 = mysql_num_rows($result2);

$sql2_1 = "SELECT a.id as chapter_id, a.chapter_no, a.description as chapter_desc, 
b.id as subchapter_id, b.subchapter_no, b.description as subchapter_desc,
a.discussed_status as chapter_discussed_status, 
b.discussed_status as subchapter_discussed_status
FROM pg_chapter a
LEFT JOIN pg_subchapter b ON (b.chapter_id = a.id)  
WHERE a.status = 'A'
AND a.student_matrix_no = '$studentMatrixNo'
AND (b.status = 'A' OR b.status IS NULL)
ORDER BY a.chapter_no, b.subchapter_no";

$result2_1 = $dbb->query($sql2_1); 
$dbb->next_record();
$row_cnt2_1 = mysql_num_rows($result2_1);
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
		<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>	
		<script language="JavaScript" type="text/javascript" src="../../../lib/js/tooltip.js"></script>		
	</head>
	<body>		
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
		<table border="0">
			<tr>
			<td><strong>Report Details - VIEW</strong></td>
			</tr>
		</table>
		<input type="hidden" name="thesisId" id="thesisId" value="<?=$thesisId; ?>">
		<input type="hidden" name="proposalId" id="proposalId" value="<?=$proposalId; ?>">
		<input type="hidden" name="progressDetailId" id="progressDetailId" value="<?=$progressDetailId; ?>">

		<input type="hidden" name="progressStatus" id="progressStatus" value="<?=$progressStatus; ?>">
		<table>
			<tr>
				<td>Report Status</td>
				<td>:</td>
				<?if ($defenseDetailStatus=="") $defenseDetailDesc='New';?>
				<td><strong><?=$defenseDetailDesc?></strong></td>
			</tr>
			<tr>
				<td>Last Update</td>
				<td>:</td>
				<td><?=$respondedDate?></td>
			</tr>
			<tr>
				<td>Reference No.</td>
				<td>:</td>
				<td><?=$referenceNo?></td>
			</tr>
			<tr>
				<td>Student Matrix No</td>
				<td>:</td>
				<td><?=$studentMatrixNo?></td>
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
			<tr>
				<td>Thesis / Project ID</td>
				<td>:</td>
				<td><label><?=$thesisId;?></label></td>
			</tr>
			<tr>
				<td>Evaluation Schedule</td>
				<td>:</td>
				<td><?=$sessionTypeDesc?> - <?=$defenseDate?>, <?=$defenseSTime?> to <?=$defenseETime?>, <?=$venue?></td>
			</tr>		
		</table>
		</br>

		<table>
			<tr>
				<td><label><strong>Supervisor</strong></label></td>
			</tr>
		</table>
		<table>			
		
				 <?php				
				
				$sql_supervisor = "SELECT d.description AS supervisor_desc, e.description as role_status_desc 
				FROM pg_defense_detail b 
				LEFT JOIN pg_defense a ON (a.id = b.pg_defense_id) 
				LEFT JOIN pg_supervisor c ON (c.pg_employee_empid = b.pg_employee_empid) 
				LEFT JOIN ref_supervisor_type d ON (d.id = c.ref_supervisor_type_id) 
				LEFT JOIN ref_role_status e ON (e.id = c.role_status)
				WHERE a.id = '$id'
				AND a.reference_no = '$referenceNo'
				AND a.student_matrix_no = '$studentMatrixNo'
				AND b.pg_employee_empid = '$employeeId'
				AND a.pg_thesis_id = '$thesisId'
				AND a.pg_proposal_id = '$proposalId'
				AND c.pg_student_matrix_no = '$studentMatrixNo'
				AND c.pg_thesis_id = '$thesisId'
				AND c.ref_supervisor_type_id in ('SV','CS','XS')
				AND c.status = 'A'";

				$result_sql_supervisor = $db_klas2->query($sql_supervisor); //echo $sql;
				
				//$row_cnt_supervisor = mysql_num_rows($result_sql_supervisor);
				$db_klas2->next_record();
				$varRecCount=0;	
						
				//$employeeId = $db_klas2->f('pg_employee_empid');
				$advice = $db_klas2->f('advice');
				$supervisorIssues = $db_klas2->f('supervisor_issues');
				$supervisorDesc = $db_klas2->f('supervisor_desc');
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

				?>
				<input type="hidden" name="supervisorIdArray[]" id="supervisorIdArray" value="<?=$employeeId; ?>">
				<tr>			
					<td><label>Staff ID</label></td>
					<td>:</td>
					<td align="left"><?=$employeeId;?></td>
				</tr>
				<tr>
					<td><label>Staff Name</label></td>
					<td>:</td>
					<td align="left"><?=$employeeName;?></td>
				</tr>
				<?if ($roleStatusDesc != "") $roleStatusDesc = "(".$roleStatusDesc.")";?>
				<tr>
					<td><label>Role</label></td>
					<td>:</td>
					<td align="left"><?=$supervisorDesc;?> <?=$roleStatusDesc?></td>
				</tr>
				<tr>
					<td><label>Department</label></td>
					<td>:</td>							
					<td align="left"><?=$departmentName;?></td>
				</tr>
			</table>
			</br>		
			<table>
			<tr>
				<td><h3><strong>To be completed by Supervisor/Co-Supervisor</strong></h3></td>
			</tr>
			</table>
			<table width="36%">
				<tr>
				  <td width="25%"><label>Performance in line with objectives</label></td>
					<td width="1%">:</td>
					<td width="10%">
						<?if ($performanceStatus == "Y") {?>
							<input type="radio" name="performanceStatus" value="Y" checked="" disabled=""> Yes
							<input type="radio" name="performanceStatus" value="N" disabled=""> No
						<?}
						else if ($performanceStatus == "N"){
							?>
							<input type="radio" name="performanceStatus" value="Y" disabled=""> Yes
							<input type="radio" name="performanceStatus" value="N" checked="" disabled=""> No
							<?
						}
						else {
							?>
							<input type="radio" name="performanceStatus" value="Y" disabled=""> Yes
							<input type="radio" name="performanceStatus" value="N" disabled=""> No
							<?
						}
					?></td>
				</tr>
				<tr>
					<td><label>Progress work in line with objectives</label></td>
					<td>:</td>
					<td>
						<?if ($progressWorkStatus == "Y") {?>
							<input type="radio" name="progressWorkStatus" value="Y" checked="" disabled=""> Yes
							<input type="radio" name="progressWorkStatus" value="N" disabled=""> No
						<?}
						else if ($progressWorkStatus == "N"){
							?>
							<input type="radio" name="progressWorkStatus" value="Y" disabled=""> Yes
							<input type="radio" name="progressWorkStatus" value="N" checked="" disabled=""> No
							<?
						}
						else {
							?>
							<input type="radio" name="progressWorkStatus" value="Y" disabled=""> Yes
							<input type="radio" name="progressWorkStatus" value="N" disabled=""> No
							<?
						}
					?></td>
				</tr>				
				<tr>
				  <td><label>Recommendation for Defence Proposal</label></td>
					<td>:</td>
					<td>
					<? if ($defenseDetailStatus == "REC") {?>
							<input type="radio" name="recommendationStatus" value="Y" checked="" disabled=""> Yes
							<input type="radio" name="recommendationStatus" value="N" disabled=""> No
						<?}
						else if ($defenseDetailStatus == "NRE"){
							?>
							<input type="radio" name="recommendationStatus" value="Y" disabled=""> Yes
							<input type="radio" name="recommendationStatus" value="N" checked="" disabled=""> No
							<?
						}
						else {
							?>
							<input type="radio" name="recommendationStatus" value="Y" disabled=""> Yes
							<input type="radio" name="recommendationStatus" value="N" disabled=""> No
							<?
						}
					?></td>
				</tr>
			</table>
			<br/>
			<table width="100%">
				<tr>
					<td width="25%"><label>If No, please specify your reason</label></td>
					<td width="1%">:</td>
					<td width="74%"><label><?=$comment?></label><br/><br/></td>
				</tr>
				<tr>
					<td><label>Any other information, please specify:</label></td>
					<td>:</td>
					<td><label><?=$otherInfo?></label></td>
				</tr>
			</table>
		<table>
			<tr>
				<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../defense/review_defense_detail_view.php?pid=<?=$proposalId;?>&tid=<?=$thesisId;?>&id=<?=$defenseId;?>&mn=<?=$studentMatrixNo;?>&ref=<?=$referenceNo;?>&cid=<?=$defenseCalendarId;?>';" /></td>
			</tr>
		</table>
		</br>
		</form>
</body>
</html>




