<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Submit Defence Proposal</title>
		<link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
		<link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
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
// Program Name: defense_proposal.php
//
// Created by: Zuraimi
// Created Date: 24-Jun-2015
// Modified by: Zuraimi
// Modified Date: 24-Jun-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

session_start();
$user_id = $_SESSION['user_id'];


if(isset($_POST['btnDeleteReport']) && ($_POST['btnDeleteReport'] <> ""))
{
	$msg = Array();
	$report_checkbox = $_POST['report_checkbox'];
	$thesisId = $_POST['thesisId'];
	$proposalId = $_POST['proposalId'];
	$defenseId = $_POST['defenseId'];
	$referenceNo = $_POST['referenceNo'];
	$defenseStatus = $_POST['defenseStatus'];
	
	$count = count($report_checkbox);
	if ($count == 0) {
		$msg[] = "<div class=\"error\"><span>Please tick the checkbox first before proceed with the deletion.</span></div>";
	}
	$tmp_report_checkbox = $report_checkbox;
	while (list ($key,$val) = @each ($tmp_report_checkbox)) 
	{
		if ($defenseStatus[$val] != 'SAV') {
			$msg[] = "<div class=\"error\"><span>The Defence Proposal $referenceNo[$val] is already submitted. It cannot be deleted from the list.</span></div>";
		}
		else {
			$sql1 = "SELECT id 
			FROM pg_defense
			WHERE pg_thesis_id = '$thesisId'
			AND pg_proposal_id = '$proposalId'
			AND reference_no = '$referenceNo[$val]'";
			
			$result_sql1 = $db->query($sql1);
			$db->next_record();
			$row_cnt_sql1 = mysql_num_rows($result_sql1);
			
			if ($row_cnt_sql1 > 1) {
				$msg[] = "<div class=\"error\"><span>The Defence Proposal $referenceNo[$val] has the detail history already. It cannot be deleted from the list.</span></div>";
			}
			
		}		
	}
		
	if(empty($msg)) 
	{
		$lock_tables="LOCK TABLES pg_defense WRITE, pg_defense_detail WRITE, 
		pg_achievement_detail WRITE, pg_achievement WRITE"; //lock the table
		$db->query($lock_tables);
		
		while (list ($key,$val) = @each ($report_checkbox)) 
		{
			$sql1 = "DELETE FROM pg_defense
			WHERE id = '$defenseId[$val]'";
			
			$db->query($sql1);
			$db->next_record();
			
			$sql2 = "DELETE FROM pg_defense_detail
			WHERE pg_defense_id = '$defenseId[$val]'";
			
			$db->query($sql2);
			$db->next_record();
			
			$sql3 = "DELETE FROM pg_achievement_detail
			WHERE pg_achievement_id IN 
				(SELECT id
				FROM pg_achievement
				WHERE defense_id = '$defenseId[$val]')";
			
			$db->query($sql3);
			$db->next_record();
			
			$sql4 = "DELETE FROM pg_achievement
			WHERE defense_id = '$defenseId[$val]'";
			
			$db->query($sql4);
			$db->next_record();
		}
		$unlock_tables="UNLOCK TABLES"; //unlock the table;
		$db->query($unlock_tables);	
	}
}

$sql1 = "SELECT pt.id AS thesis_id, pt.student_matrix_no,pt.status AS thesis_status,
pp.id AS proposal_id, pp.thesis_title,pp.thesis_type, pp.objective, pp.introduction,pp.description,pp.discussion_status, 
DATE_FORMAT(pp.verified_date,'%d-%b-%Y') AS verified_date, pp.verified_remarks, pp.verified_by,
pp.verified_status AS proposal_status,pp.endorsed_by, DATE_FORMAT(pp.endorsed_date,'%d-%b-%Y') AS endorsed_date, 
pp.endorsed_remarks, pp.status AS endorsed_status, 
rps.description AS proposal_description, rps2.description AS endorsed_desc, 
DATE_FORMAT(pp.report_date,'%d-%b-%Y') AS report_date,
DATE_FORMAT(pp.cancel_requested_date,'%d-%b-%Y') AS cancel_requested_date,
DATE_FORMAT(pp.cancel_approved_date,'%d-%b-%Y') AS cancel_approved_date, 
pp.cancel_approved_by, pp.cancel_approved_remarks 
FROM pg_thesis pt 
LEFT JOIN pg_proposal pp ON (pp.pg_thesis_id=pt.id) 
LEFT JOIN ref_proposal_status rps ON (rps.id = pp.verified_status) 
LEFT JOIN ref_proposal_status rps2 ON (rps2.id = pp.status) 
WHERE pt.student_matrix_no = '$user_id'
AND pp.verified_status in ('APP','AWC')				
AND pp.archived_status is null
AND pt.ref_thesis_status_id_proposal in ('APP','AWC','APC')
ORDER BY pt.id";

$result1 = $db->query($sql1); 
$db->next_record();
$thesisId=$db->f('thesis_id');
$proposalId=$db->f('proposal_id');
$thesisTitle=$db->f('thesis_title');
$reportDate=$db->f('report_date');
$endorsedDate=$db->f('endorsed_date');
$row_cnt1 = mysql_num_rows($result1);

$sql2 = "SELECT a.id, a.reference_no, DATE_FORMAT(d.submit_date, '%d-%b-%Y %h:%i%p') as submit_date, 
a.student_matrix_no, 
a.pg_thesis_id, a.pg_proposal_id, 
DATE_FORMAT(d.responded_date,'%d-%b-%Y %h:%i%p') as responded_date, 
a.insert_by, a.insert_date, a.modify_by, a.modify_date,	a.status as defense_status, c1.description as defense_desc,
d.status as defense_detail_status, c2.description as defense_detail_desc, d.id as defense_detail_id, a.pg_calendar_id,
d.pg_employee_empid, DATE_FORMAT(e.defense_date,'%d-%b-%Y') as defense_date,	
DATE_FORMAT(e.defense_stime,'%h:%i%p') as defense_stime, DATE_FORMAT(e.defense_etime,'%h:%i%p') as defense_etime,
f.ref_defense_marks_id, g.description as defense_marks_desc, f.reference_no as reference_no_eval,
DATE_FORMAT(f.confirmed_date, '%d-%b-%Y %h:%i%p') as confirmed_date, f.status as evaluation_status,
h.description as evaluation_status_desc
FROM pg_defense a
LEFT JOIN ref_proposal_status c1 ON (c1.id = a.status)
LEFT JOIN pg_defense_detail d ON (d.pg_defense_id = a.id)
LEFT JOIN ref_proposal_status c2 ON (c2.id = d.status)
LEFT JOIN pg_calendar e ON (e.id = a.pg_calendar_id)
LEFT JOIN pg_evaluation f ON (f.pg_defense_id = a.id)
LEFT JOIN ref_defense_marks g ON (g.id = f.ref_defense_marks_id)
LEFT JOIN ref_proposal_status h ON (h.id = f.status)
WHERE a.student_matrix_no = '$user_id'
AND a.pg_thesis_id = '$thesisId'
AND a.pg_proposal_id = '$proposalId'
/*AND a.archived_status is null*/
AND d.archived_status is null
ORDER BY a.reference_no DESC";
		
$result2 = $dba->query($sql2); 
$dba->next_record();
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
DATE_FORMAT(a.acceptance_date,'%d-%b-%Y %h:%i%p') as acceptance_date, h.description as role_status_desc
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
AND f.archived_status IS NULL 
AND g.archived_status IS NULL
AND a.status = 'A'
ORDER BY d.seq, a.ref_supervisor_type_id";

						
$result_sql_supervisor = $db_klas2->query($sql_supervisor); //echo $sql;
$db_klas2->next_record();

$row_cnt_supervisor = mysql_num_rows($result_sql_supervisor);
						
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
	function newReport(tid,pid) 
	{
		document.location.href = "new_defense.php?tid=" + tid + "&pid=" + pid;
		return true;
	}
	</script>
	<script type="text/javascript">
	function deleteReport() 
	{
		var ask = window.confirm("Are you sure to delete this Defence Proposal? All proposal with the same Reference No. will be deleted. \nClick OK to proceed or CANCEL to stay on the same page.");
		if (ask) 
		{
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
	<?if ($row_cnt1 > 0) {
		if ($row_cnt_supervisor > 0 ) {
			if ($row_cnt2_1 > 0) {
				?>
				<fieldset>
					<legend><strong>List of Defence Proposal</strong></legend>
					<table>
						<tr>
							<td><h3><strong>List of Supervisor/Co-Supervisor</h3></strong></td>
						</tr>
					</table>
					<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="65%" class="thetable">			
						<tr>
							<th width="5%">No</th>					
							<th width="15%" align="left">Role</th>
							<th width="10%" align="left">Staff ID</th>
							<th width="20%" align="left">Supervisor / Co-Supervisor Name</th>
							<th width="5%" align="left">Faculty</th>
							<th width="15%">Acceptance Date</th>
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
									</td>
									
									<td align="left"><?=$employeeId;?></td>
									<td align="left"><?=$employeeName;?></td>
									<td align="left"><a href="javascript:void(0);" onMouseOver="toolTip('<?=$departmentName;?>', 300)" onMouseOut="toolTip()"><?=$departmentId;?></a></td>
									<td align="center"><?=$acceptanceDate;?></td>
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
											<span style="color:#FF0000"> Make sure your Supervisor has been assigned first before submit the Defence Proposal.</span></td>
								</tr>
							</table>
							<?
						}?>	
					</table>
					<br/>
					<table>
						<tr>
							<td><h3><strong>Defence Proposal Status</strong></h3>Searching Results - <?=$row_cnt2?> record(s) found.</td>
						</tr>						
					</table>
					<?if ($row_cnt2 <= 1) {
						?>
						<div id = "tabledisplay" style="overflow:auto; height:100px;">
						<?
					}
					else {
						?>
						<div id = "tabledisplay" style="overflow:auto; height:250px;">
						<?
					}?>
					<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="100%" class="thetable">			
							<tr>
								<th width="5%">Tick</th>
								<th width="5%">No</th>
								<th width="10%" align="left">Reference No</th>
								<th width="15%" align="left">Evaluation Schedule</th>
								<th width="15%" align="left">Last Update by Student</th>
								<th width="15%" align="left">Supervisor / Co-Supervisor Name</th>
								<th width="15%" align="left">Last Update by Supervisor / Co-Supervisor</th>
								<th width="15%" align="left">Evaluation Status</th>
								<th width="5%">Action</th>
							</tr>
							<?
							$no=0;
							if ($row_cnt2 > 0) {
								do {
									$id=$dba->f('id');
									$defenseStatus=$dba->f('defense_status');
									$defenseDesc=$dba->f('defense_desc');
									$defenseDetailId=$dba->f('defense_detail_id');
									$defenseDetailStatus=$dba->f('defense_detail_status');
									$defenseDetailDesc=$dba->f('defense_detail_desc');
									$submitDate=$dba->f('submit_date');
									$defenseDate=$dba->f('defense_date');
									$defenseSTime=$dba->f('defense_stime');
									$defenseETime=$dba->f('defense_etime');
									$studentIssues=$dba->f('student_issues');
									$supervisorIssues=$dba->f('supervisor_issues');
									$advice=$dba->f('advice');
									$referenceNo=$dba->f('reference_no');
									$respondedDate=$dba->f('responded_date');
									$employeeId=$dba->f('pg_employee_empid');
									$defenseMarksDesc=$dba->f('defense_marks_desc');
									$referenceNoEval=$dba->f('reference_no_eval');	
									$calendarId=$dba->f('pg_calendar_id');	
									$confirmedDate=$dba->f('confirmed_date');	
									$evaluationStatus=$dba->f('evaluation_status');		
									$evaluationStatusDesc=$dba->f('evaluation_status_desc');										
									?>							
								<tr>
									<td align="center"><input name="report_checkbox[]" type="checkbox" id="report_checkbox" value="<?=$no;?>"/></td>
									
									<td align="center"><?=++$no;?>.</td>
									<td align="left"><?=$referenceNo;?><br><?=$defenseDesc;?></td>
									<td align="left"><?=$defenseDate;?>, <br/><?=$defenseSTime?> to <?=$defenseETime?></td>
									<td align="left"><?=$submitDate;?></td>
									<?$sql_employee="SELECT  b.name, c.id, c.description
										FROM new_employee b 
										LEFT JOIN dept_unit c ON (c.id = b.unit_id) 
										WHERE b.empid= '$employeeId'";
										
									$result_sql_employee = $dbc->query($sql_employee);
									$dbc->next_record();
									
									$employeeName = $dbc->f('name');
									?>
									<td><?=$employeeName?><br/><?=$employeeId;?></td>
									<td align="left"><?=$defenseDetailDesc;?><br><?=$respondedDate;?></td>
									<td align="left"><?=$referenceNoEval;?><br><?=$defenseMarksDesc;?> <?if ($evaluationStatus !="") {?>[<?=$evaluationStatusDesc?>]<?}?><br><?=$confirmedDate?></td>
									<input type="hidden" name="thesisId" id="thesisId" value="<?=$thesisId;?>">
									<input type="hidden" name="proposalId" id="proposalId" value="<?=$proposalId;?>">
									<input type="hidden" name="defenseId[]" id="defenseId" value="<?=$id;?>">
									<input type="hidden" name="referenceNo[]" id="referenceNo" value="<?=$referenceNo;?>">
									<input type="hidden" name="defenseStatus[]" id="defenseStatus" value="<?=$defenseStatus;?>">
									<?
									if ($defenseStatus =='SAV') {
										?>
										<td align="center"><a href="../defense/edit_defense.php?pid=<?=$proposalId;?>&tid=<?=$thesisId;?>&ref=<?=$referenceNo;?>">Edit</a></td>
									<?}
									else if ($defenseStatus == 'REQ' && $defenseDetailStatus == 'REQ') {
										?>
										<td align="center"><a href="../defense/edit_defense.php?pid=<?=$proposalId;?>&tid=<?=$thesisId;?>&ref=<?=$referenceNo;?>">Edit</a></td>
									<?}
									else {
										?>
										<td align="center"><a href="../defense/view_defense.php?id=<?=$id;?>&pid=<?=$proposalId;?>&tid=<?=$thesisId;?>&ref=<?=$referenceNo;?>&cid=<?=$calendarId?>">View</a></td>
										<?
									}?>
								</tr>
								<?
								} while ($dba->next_record());
							}
							else {
								?>
								<table>
									<tr>
										<td>No record(s) found.</td>
									</tr>
								</table>
							<?
							}								
							?>
					</table>
					</div>
					<table>
						<tr>
							<td>Notes:<br/>
							1. Defence Proposal can only be deleted if it is not yet submitted to the Supervisor/Co-Supervisor (Edit Mode).<br/>
							2. The deletion of the Defence Proposal will be applied to the proposal with the same Reference No.</label></td>
						</tr>
					</table>
					<table>
						<tr>
							<td><input type="button" name="btnNewReport" value="New Defence Proposal" onclick="return newReport('<?=$thesisId?>','<?=$proposalId?>')"></td>
							<td><input type="submit" name="btnDeleteReport" value="Delete Defence Proposal" onclick="return deleteReport()"></td>
						</tr>
					</table>					
				</fieldset>
				<?
			}
			else {				
				$err = "<div class=\"error\"><span>Submission of Defence Proposal Report is currently not allowed.</span></div>";
				echo $err;
				
				?>
				<table>
					<tr>		
						<td><label>Possible Reason:-<br>
						1. Please define  <a href="../monthlyreport/create_chapter.php">Thesis Chapter</a> first before submit Defence Proposal.</label></td>		
					</tr>
				</table>		
				<?
			}
		}
		else {
			$err = "<div class=\"error\"><span>Submission of Defence Proposal is currently not allowed.</span></div>";
			echo $err;
			?>
			<table>
				<tr>
					<td><label>Possible Reasons:-</label></td>
				</tr>
				<tr>
					<td><label>1. Your Thesis Proposal is pending approval by Senate.<br>
					2. Your Supervisor/Co-Supervisor has not been assigned yet or pending their invitation acceptance.</label></td>
				</tr>
			</table>
			<?			
		}
	}
	else {
			$err = "<div class=\"error\"><span>You are not able to submit your <strong>Defence Proposal</strong>. Ensure your thesis proposal has been submitted and approved first by the Senate.</span></div>";
			echo $err;
	}	?>
	<script>
		<?=$jscript;?>	
	</script>
	</body>
</html>




