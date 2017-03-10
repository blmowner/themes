<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Submit Monthly Progress Report</title>
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
// Program Name: submit_progress_new.php
//
// Created by: Zuraimi
// Created Date: 17-Jun-2015
// Modified by: Zuraimi
// Modified Date: 17-Jun-2015
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
	$progressId = $_POST['progressId'];
	$referenceNo = $_POST['referenceNo'];
	$progressStatus = $_POST['progressStatus'];
	
	$count = count($report_checkbox);
	if ($count == 0) {
		$msg[] = "<div class=\"error\"><span>Please tick the checkbox for newly added Monthly Progress Report to be deleted.</span></div>";
	}
	$tmp_report_checkbox = $report_checkbox;
	while (list ($key,$val) = @each ($tmp_report_checkbox)) 
	{
		if ($progressStatus[$val] != 'SAV') {
			$msg[] = "<div class=\"error\"><span>The Monthly Progress Report $referenceNo[$val] is already submitted. It cannot be deleted from the list.</span></div>";
		}
		else {
			$sql1 = "SELECT id 
			FROM pg_progress
			WHERE pg_thesis_id = '$thesisId'
			AND pg_proposal_id = '$proposalId'
			AND reference_no = '$referenceNo[$val]'";
			
			$result_sql1 = $db->query($sql1);
			$db->next_record();
			$row_cnt_sql1 = mysql_num_rows($result_sql1);
			
			if ($row_cnt_sql1 > 1) {
				$msg[] = "<div class=\"error\"><span>The Monthly Progress Report $referenceNo[$val] has the detail history already. It cannot be deleted from the list.</span></div>";
			}
			
		}		
	}
		
	if(empty($msg)) 
	{
		while (list ($key,$val) = @each ($report_checkbox)) 
		{
			$sql1 = "DELETE FROM pg_progress
			WHERE id = '$progressId[$val]'";
			
			$db->query($sql1);
			$db->next_record();
			
			$sql2 = "DELETE FROM pg_progress_detail
			WHERE pg_progress_id = '$progressId[$val]'";
			
			$db->query($sql2);
			$db->next_record();
			
			$sql3 = "DELETE FROM pg_discussion_detail
			WHERE pg_discussion_id IN 
				(SELECT id
				FROM pg_discussion
				WHERE progress_id = '$progressId[$val]')";
			
			$db->query($sql3);
			$db->next_record();
			
			$sql4 = "DELETE FROM pg_discussion
			WHERE progress_id = '$progressId[$val]'";
			
			$db->query($sql4);
			$db->next_record();
		}
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

$sql2 = "SELECT a.id, a.reference_no, a.report_month, a.report_year, DATE_FORMAT(d.submit_date, '%d-%b-%Y %h:%i %p') as submit_date, a.student_matrix_no, 
a.pg_thesis_id, a.pg_proposal_id, 
DATE_FORMAT(a.meeting_stime,'%h:%i') as meeting_stime, DATE_FORMAT(a.meeting_stime,'%p') as stime_pm, 
DATE_FORMAT(a.meeting_etime,'%h:%i') as meeting_etime, DATE_FORMAT(a.meeting_etime,'%p') as etime_pm,		
DATE_FORMAT(a.meeting_date,'%d-%b-%Y') as meeting_date, a.status as progress_status, 
DATE_FORMAT(d.responded_date,'%d-%b-%Y %h:%i %p') as responded_date, 
a.insert_by, a.insert_date, a.modify_by, a.modify_date,	a.issues as student_issues, a.advice, c1.description as progress_desc,
d.status as progress_detail_status, c2.description as progress_detail_desc, d.id as progress_detail_id, 
d.pg_employee_empid
FROM pg_progress a
LEFT JOIN ref_proposal_status c1 ON (c1.id = a.status)
LEFT JOIN pg_progress_detail d ON (d.pg_progress_id = a.id)
LEFT JOIN ref_proposal_status c2 ON (c2.id = d.status)
WHERE a.student_matrix_no = '$user_id'
AND a.pg_thesis_id = '$thesisId'
AND a.pg_proposal_id = '$proposalId'
AND a.archived_status is null
AND d.archived_status is null
ORDER BY STR_TO_DATE(a.report_year, '%Y'), STR_TO_DATE(a.report_month,'%M')";
		
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
		document.location.href = "new_progress.php?tid=" + tid + "&pid=" + pid;
		return true;
	}
	</script>
	<script type="text/javascript">
	function deleteReport() 
	{
		var ask = window.confirm("Are you sure to delete this Monthly Progress Report? \nClick OK to proceed or CANCEL to stay on the same page.");
		if (ask) 
		{
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
	<script>
	function report_attachment(pid, al, at) {
		var ask = window.confirm("Ensure your report has been saved before proceed or otherwise the last change will be discarded.\nClick OK to proceed or CANCEL to stay on the same page.");
		if (ask) {
			document.location.href = "javascript:open_win('progress_monthly_upload.php?pid="+pid+"&al="+al+"&at="+at+",480,280,0,0,0,1,0,1,1,0,5,'winupload')";

		}
	}
	</script>

	<script type="text/javascript">
	function newDicussion(pid, tid, pgid) 
	{
		var ask = window.confirm("Ensure your report has been saved before proceed or otherwise the last change will be discarded.\nClick OK to proceed or CANCEL to stay on the same page.");
		if (ask) 
		{
			//alert(pgid);
			document.location.href = "../monthlyreport/progress_discussion_detail.php?pid=" + pid + "&tid=" + tid + "&pgid=" + pgid;
			return true;
		}
		return false;
	}
	</script>
	<script>
	function issueAttachment(pid, prgid, at) {
		var ask = window.confirm("Ensure your report has been saved before proceed or otherwise the last change will be discarded.\nClick OK to proceed or CANCEL to stay on the same page.");
		if (ask) {
			document.location.href = "../monthlyreport/submit_progress_attachment.php?pid=" + pid + "&prgid=" + prgid + "&at=" + at;

		}
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
					<legend><strong>List of Monthly Progress Report</strong></legend>
					<table width="100%">
						<tr>
							<td width="13%">Thesis / Project ID</td>
							<td>:</td>
							<td><label><?=$thesisId;?></label></td>
						</tr>
						<tr>
							<td>Submitted Date</td>
							<td>:</td>
							<td><label><?=$reportDate;?></label></td>
						</tr>
						<tr>
							<td>Approved Date</td>
							<td>:</td>
							<td><label><?=$endorsedDate;?></label></td>
						</tr>
						<tr>
							<td>Thesis / Project Title</td>
							<td>:</td>
							<td><label><?=$thesisTitle;?></label></td>
						</tr>						
						
					</table>
					<br/>	
					<table>
						<tr>
							<td><h3><strong>List of Supervisor/Co-Supervisor</h3></strong></td>
						</tr>
					</table>
					<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="90%" class="thetable">			
						<tr>
							<th width="5%">No</th>					
							<th width="20%">Role</th>
							<th width="15%">Staff ID</th>
							<th width="25%">Name</th>
							<th width="10%">Faculty</th>
							<th width="15%">Accepted Date</th>
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
									<td align="left"><?=$acceptanceDate;?></td>
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
											<span style="color:#FF0000"> Make sure your Supervisor has been assigned first before submit the Monthly Progress report</span></td>
								</tr>
							</table>
							<?
						}?>	
					</table>
					<br/>
					<table>
						<tr>
							<td><h3><strong>Monthly Progress Report Status</strong></h3></td>
						</tr>						
					</table>
					<?if ($row_cnt2 <= 2) {?>
						<div id = "tabledisplay" style="overflow:auto; height:100px;">
					<?}
					else if ($row_cnt2 <= 4) {
						?>
						<div id = "tabledisplay" style="overflow:auto; height:160px;">
						<?
					}
					else if ($row_cnt2 <= 6) {
						?>
						<div id = "tabledisplay" style="overflow:auto; height:220px;">
						<?
					}
					else if ($row_cnt2 <= 8) {
						?>
						<div id = "tabledisplay" style="overflow:auto; height:290px;">
						<?
					}
					else if ($row_cnt2 <= 10) {
						?>
						<div id = "tabledisplay" style="overflow:auto; height:350px;">
						<?
					}
					else {
						?>
						<div id = "tabledisplay" style="overflow:auto; height:380px;">
						<?
					}?>
					<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="95%" class="thetable">			
							<tr>
								<th width="5%">Tick</th>
								<th width="5%">No</th>
								<th width="10%">Report Month / Year</th>
								<th width="10%">Reference No</th>
								<th width="15%">Last Update by Student</th>
								<th width="15%">Staff ID</th>
								<th width="10%">Status</th>
								<th width="15%">Last Update by Supervisor / Co-Supervisor</th>
								<th width="10%">Action</th>
							</tr>
							<?
							$no=0;
							if ($row_cnt2 > 0) {
								do {
									$id=$dba->f('id');
									$reportMonth=$dba->f('report_month');
									$reportYear=$dba->f('report_year');
									$meetingDate=$dba->f('meeting_date');
									$startTime=$dba->f('meeting_stime');
									$endTime=$dba->f('meeting_etime');
									$sTimePM=$dba->f('stime_pm');
									$eTimePM=$dba->f('etime_pm');
									$progressStatus=$dba->f('progress_status');
									$progressDesc=$dba->f('progress_desc');
									$progressDetailId=$dba->f('progress_detail_id');
									$progressDetailStatus=$dba->f('progress_detail_status');
									$progressDetailDesc=$dba->f('progress_detail_desc');
									$submitDate=$dba->f('submit_date');
									$studentIssues=$dba->f('student_issues');
									$supervisorIssues=$dba->f('supervisor_issues');
									$advice=$dba->f('advice');
									$referenceNo=$dba->f('reference_no');
									$respondedDate=$dba->f('responded_date');
									$employeeId=$dba->f('pg_employee_empid');
									?>							
								<tr>
									<td align="center"><input name="report_checkbox[]" type="checkbox" id="report_checkbox" value="<?=$no;?>"/></td>
									
									<td align="center"><?=++$no;?>.</td>
									<td align="center"><?=$reportMonth;?> <?=$reportYear;?></td>
									<td align="center"><?=$referenceNo;?></td>
									<td align="center"><?=$submitDate;?></td>
									<td align="center"><?=$employeeId;?></td>
									<td align="center"><?=$progressDetailDesc;?></td>
									<td align="center"><?=$respondedDate;?></td>
									<input type="hidden" name="thesisId" id="thesisId" value="<?=$thesisId;?>">
									<input type="hidden" name="proposalId" id="proposalId" value="<?=$proposalId;?>">
									<input type="hidden" name="progressId[]" id="progressId" value="<?=$id;?>">
									<input type="hidden" name="referenceNo[]" id="referenceNo" value="<?=$referenceNo;?>">
									<input type="hidden" name="progressStatus[]" id="progressStatus" value="<?=$progressStatus;?>">
									<?
									if ($progressStatus =='SAV') {
										?>
										<td align="center"><a href="../monthlyreport/edit_progress.php?pid=<?=$proposalId;?>&tid=<?=$thesisId;?>&ref=<?=$referenceNo;?>">Edit Report</a></td>
									<?}
									else if ($progressStatus == 'REQ' && $progressDetailStatus == 'REQ') {
										?>
										<td align="center"><a href="../monthlyreport/edit_progress.php?pid=<?=$proposalId;?>&tid=<?=$thesisId;?>&ref=<?=$referenceNo;?>">Edit Report</a></td>
									<?}
									else {
										?>
										<td align="center"></td>
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
							<td><input type="button" name="btnNewReport" value="New Report" onclick="return newReport('<?=$thesisId?>','<?=$proposalId?>')"></td>
							<td><input type="submit" name="btnDeleteReport" value="Delete Report" onclick="return deleteReport()"></td>
						</tr>
					</table>
					
				</fieldset>
				<?
			}
			else {
				$err = "<div class=\"error\"><span>Please define thesis chapter first before submit monthly progress report.</span></div>";
				echo $err;	
			}
		}
		else {
			$err = "<div class=\"error\"><span>Your Supervisor/Co-Supervisor has yet to accept the invitation to supervise you as his/her student.
			You can submit the report once he/she has accepted it.</span></div>";
			echo $err;			
		}
	}
	else {
			$err = "<div class=\"error\"><span>You are not able to submit your <strong>monthly progress report</strong>. Ensure your thesis proposal has been submitted and approved first by the Senate.</span></div>";
			echo $err;			
	}	?>
	<script>
		<?=$jscript;?>	
	</script>
	</body>
</html>




