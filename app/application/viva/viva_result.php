<? session_start(); ?>
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


$user_id = $_SESSION['user_id'];


if(isset($_POST['btnDelete']) && ($_POST['btnDelete'] <> ""))
{
	$thesisId = $_REQUEST['thesisId'];
	$proposalId = $_REQUEST['proposalId'];
	
	$curdatetime = date("Y-m-d H:i:s");
	
	$check = $_REQUEST['report_checkbox'];
	
	while (list ($key,$val) = @each ($check)) 
	{
		$no=1+$val;
		if (empty($_POST['amendmentSave'][$val])) $msg[] = "<div class=\"error\"><span>Please provide amendment for record no $no.</span></div>";
		if (empty($_POST['feedbackAdd'][$val])) $msg[] = "<div class=\"error\"><span>Please provide feedback of external examiner for record no $no.</span></div>";
	}
	
	if (sizeof($_POST['amendCheck'])>0) {
		while (list ($key,$val) = @each ($_POST['amendCheck'])) 
		{
			$amendmentId = $_REQUEST['amendmentId'][$val];
			$amendmentStatus = $_POST['amendmentStatus'][$val];
			$referenceNo = $_POST['referenceNo'][$val];
			
			if($amendmentStatus == 'SAV') {
			
				$sql1 = "DELETE from pg_amendment
				WHERE id = '$amendmentId'
				AND student_matrix_no = '$user_id'
				AND pg_thesis_id = '$thesisId'";
				
				$dba->query($sql1);
				
				$sql1 = "DELETE from pg_amendment_detail
				WHERE pg_amendment_id = '$amendmentId'
				AND student_matrix_no = '$user_id'
				AND pg_thesis_id = '$thesisId'";
				
				$dba->query($sql1);	
				
				$msg[] = "<div class=\"success\"><span>Amendment deleted successfully</span></div>";					
			}
			else {
				$msg[] = "<div class=\"error\"><span>The amendment already been submitted to supervisor.</span></div>";				
			}
			
			
		
			//echo "$amendmentSave<br>"."$feedbackAdd<br>"."$amendmendIdDetail<br>"."$proposalId<br>"."$thesisId<br>";
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
AND g.archived_status IS NULL
AND a.status = 'A'
ORDER BY d.seq, a.ref_supervisor_type_id";

$result_sql_supervisor = $db_klas2->query($sql_supervisor); //echo $sql;
$db_klas2->next_record();

$row_cnt_supervisor = mysql_num_rows($result_sql_supervisor);


$sql4 = "SELECT id
FROM pg_calendar
WHERE student_matrix_no = '$user_id'
AND thesis_id = '$thesisId'
AND ref_session_type_id = 'VIV'
AND recomm_status = 'REC'
AND status = 'A'
ORDER BY defense_date ASC";

$result_sql4 = $dbf->query($sql4); 
$dbf->next_record();
$row_cnt4 = mysql_num_rows($result_sql4);
$calendarIdViva = $dbf->f('id');


$sql_viva = "SELECT id AS pgVivaId, viva_status, submit_status, DATE_FORMAT(submit_date,'%d-%b-%Y %h:%i%p') as submit_date , session_no , pg_calendar_id
FROM pg_viva
WHERE pg_thesis_id = '$thesisId'
AND pg_proposal_id = '$proposalId'
AND student_matrix_no = '$user_id'
AND insert_by IS NOT NULL
AND STATUS in ('ARC','A', 'ARC1')";// 'ARC1'
				
$result_sql_viva = $dba->query($sql_viva); //echo $sql;
$dba->next_record();
$row_cnt_viva = mysql_num_rows($result_sql_viva);

	
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
		document.location.href = "new_viva.php?tid=" + tid + "&pid=" + pid;
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
<? 
$curdatetime3 = date("d-m-Y");
$curdcurdatetime3ate=strtotime($curdatetime3);
$dateViva;
$dateViva=strtotime($dateViva);


if($row_cnt4>0)
{ 

?>	
	<? if ($row_cnt_supervisor > 0 ) {
				?>
				<fieldset>
					<legend><strong>VIVA Result </strong></legend>
					<table width="90%">
						<tr>
							<td width="13%">Thesis / Project ID</td>
							<td width="2%">:</td>
							<td width="85%"><label><?=$thesisId;?></label></td>
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
							<td><legend><strong>List of Supervisor/Co-Supervisor</strong></legend></td>
						</tr>
					</table>
					<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="65%" class="thetable">			
						<tr>
							<th width="5%">No</th>					
							<th width="15%" align="left">Role</th>
							<th width="10%" align="left">Staff ID</th>
							<th width="20%" align="left">Supervisor Name</th>
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

				<div id = "tabledisplay" style="overflow:auto; height:100px;">
					<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="100%" class="thetable">			
							<tr>
								<!--<th width="4%">Tick</th>-->
								<th width="4%">No</th>
								<th width="7%">Reference No </th>
								<th width="11%" align="left">Project/Thesis Id </th>
								<th width="12%" align="left">Submit Date</th>
								<th width="18%" align="left">Evaluation Date </th>
								<th width="10%" align="left"> Status </th>
								<th width="6%">Action</th>
							</tr>
							<?
							$no=0;
							if ($row_cnt_viva > 0) {
							
								do {
								$pgVivaId = $dba->f('pgVivaId');
								$viva_status = $dba->f('viva_status');
								$submit_status = $dba->f('submit_status');
								$submit_date = $dba->f('submit_date');
								$session_no = $dba->f('session_no');
								$calendarIdViva = $dba->f('pg_calendar_id');
								
								$sql3 = "SELECT id, DATE_FORMAT(defense_date,'%d-%m-%Y') as dateViva, DATE_FORMAT(defense_date,'%d-%b-%Y') as viva_date, 
								DATE_FORMAT(defense_stime,'%h:%i%p') as viva_stime,
								DATE_FORMAT(defense_etime,'%h:%i%p') as viva_etime, venue, recomm_status, status
								FROM pg_calendar
								WHERE student_matrix_no = '$user_id'
								AND thesis_id = '$thesisId'
								AND status = 'A'
								AND id = '$calendarIdViva'
								AND archived_status = 'ARC' OR archived_status IS NULL
								ORDER BY defense_date ASC";
								
								$result_sql3 = $dbu->query($sql3); 
								$dbu->next_record();
								$row_cnt2 = mysql_num_rows($result_sql3);
								
								$recommendedId = $dbu->f('id');
								$vivaDate = $dbu->f('viva_date');
								$vivaSTime = $dbu->f('viva_stime');
								$vivaETime = $dbu->f('viva_etime');	
								$venue = $dbu->f('venue');		
								$calendarStatus = $dbu->f('status');
								$recommStatus = $dbu->f('recomm_status');
								$dateViva = $dbu->f('dateViva');
								
								
								$sql_employee="SELECT a.pg_employee_empid, a.ref_supervisor_type_id, d.description as supervisor_type,
								DATE_FORMAT(a.acceptance_date,'%d-%b-%Y %h:%i%p') as acceptance_date, h.description as role_status_desc
								FROM pg_supervisor a 
								LEFT JOIN ref_supervisor_type d ON (d.id = a.ref_supervisor_type_id) 
								LEFT JOIN pg_thesis f ON (f.student_matrix_no = a.pg_student_matrix_no)
								LEFT JOIN pg_proposal g ON (g.pg_thesis_id = f.id)
								LEFT JOIN ref_role_status h ON (h.id = a.role_status)
								LEFT JOIN pg_viva_detail i ON (i.viva_empid = a.pg_employee_empid)
								WHERE a.pg_student_matrix_no='$user_id'
								AND g.pg_thesis_id = '$thesisId'
								AND g.id = '$proposalId'
								AND a.acceptance_status = 'ACC'
								AND a.ref_supervisor_type_id in ('SV','CS','XS')
								AND g.verified_status in ('APP','AWC')
								AND g.status in ('APP','APC')
								AND g.archived_status IS NULL
								AND a.status = 'A'
								AND i.respond_status = 'REQ'
								AND i.status <> 'ARC'
								AND i.pg_viva_id = '$pgVivaId'
								ORDER BY d.seq, a.ref_supervisor_type_id";
								
								$dbr = $dbu;
								$result_sql_employee = $dbr->query($sql_employee);
								$dbr->next_record();
								
								$rowReq = mysql_num_rows($result_sql_employee);

									?>							
								<tr>

									<td align="center"><?=++$no;?>.</td>
									<td align="center"><?=$pgVivaId;?></td>
									<td align="left"><?=$thesisId;?></td>
									<td align="left"><?=$submit_date?></td>
									<td align="left"><?=$vivaDate?>, <?=$vivaSTime?> to <?=$vivaETime?> </td>
									<? 
									if($submit_status == 'SAV')
									{
										$statDesc = "Save in Draft";
									}
									else if($submit_status == 'SUB' && $rowReq == 0)
									{
										$statDesc = "Submitted";
									}
									else if($submit_status == 'CON')
									{
										$statDesc = "Confirmed";
									}
									else if($rowReq > 0)
									{	
										$statDesc = "Request Changes";
									}
									?>
									<td><?=$statDesc?></td>
									<input type="hidden" name="thesisId" id="thesisId" value="<?=$thesisId;?>">
									<input type="hidden" name="proposalId" id="proposalId" value="<?=$proposalId;?>">
									<input type="hidden" name="amendmentId[]" id="amendmentId" value="<?=$pgVivaId;?>">
									<input type="hidden" name="amendmentStatus[]" id="submit_status" value="<?=$submit_status;?>">
									
										<td width="6%" align="center">
										<input type="button" name="btnView" id="btnView" value="View" 
										onclick="javascript:document.location.href='view_viva_result.php?id=<?=$id;?>&pid=<?=$proposalId;?>&tid=<?=$thesisId;?>&vid=<?=$pgVivaId;?>'" />
										<!--<a href="view_amendment.php?id=<?=$id;?>&pid=<?=$proposalId;?>&tid=<?=$thesisId;?>&ref=<?=$referenceNo;?>">View</a>-->
										</td>
								</tr>
								<? } while($dba->next_record());
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
					<!--<table>
						<tr>
							<td><label><span style="color:#FF0000">Notes:-</span><br/>
							1. Amendmend can only be deleted if it is not yet submitted to the Supervisor/Co-Supervisor (Edit Mode).</label></td>
						</tr>
					</table>-->
<? 
$sql_viva1 = "SELECT id AS pgVivaId, viva_status, submit_status, DATE_FORMAT(submit_date,'%d-%b-%Y %h:%i%p') as submit_date , session_no 
FROM pg_viva
WHERE pg_thesis_id = '$thesisId'
AND pg_proposal_id = '$proposalId'
AND student_matrix_no = '$user_id'
AND viva_status = 'PMR'
AND insert_by IS NOT NULL
AND STATUS = 'ARC1' ";
$dba2 = $dba;				
$result_sql_viva1 = $dba2->query($sql_viva1); //echo $sql;
$dba2->next_record();
$row_cnt_viva1 = mysql_num_rows($result_sql_viva1);

							
$pgVivaId = $dba2->f('pgVivaId');
$viva_status = $dba2->f('viva_status');
$submit_status = $dba2->f('submit_status');
$submit_date = $dba2->f('submit_date');
$session_no = $dba2->f('session_no');

$sql_amend = "SELECT a.id, a.amendment_status, a.confirm_status as confirmMain, a.confirm_date, a.reference_no
FROM pg_amendment a
WHERE a.pg_thesis_id = '$thesisId'
AND a.pg_proposal_id = '$proposalId'
AND a.amendment_status IS NOT NULL
AND a.status = 'A'
AND a.student_matrix_no = '$user_id'
AND a.ref_req_no IS NULL
AND a.pg_viva_id = '$pgVivaId'";
$dba3 = $dba;				
$result_sql_amend = $dba3->query($sql_amend); //echo $sql;
$dba3->next_record();
$row_cnt_viva12 = mysql_num_rows($result_sql_amend);

							
$amendId = $dba3->f('id');
$amendment_status = $dba3->f('amendment_status');
$confirmMain = $dba3->f('confirmMain');
$confirm_date = $dba3->f('confirm_date');
$reference_no = $dba3->f('reference_no');

$sql_viva2 = "SELECT id AS pgVivaId, viva_status, submit_status, DATE_FORMAT(submit_date,'%d-%b-%Y %h:%i%p') as submit_date , session_no , pg_calendar_id
FROM pg_viva
WHERE pg_thesis_id = '$thesisId'
AND pg_proposal_id = '$proposalId'
AND student_matrix_no = '$user_id'
AND insert_by IS NOT NULL
AND STATUS in ('ARC','A', 'ARC1')
order by id desc";// 'ARC1'
$dba4 = $dba;	
$result_sql_viva2 = $dba4->query($sql_viva2); //echo $sql;
$dba4->next_record();
$row_cnt_viva1 = mysql_num_rows($result_sql_viva2);

$sql5 = "SELECT id
FROM pg_calendar
WHERE student_matrix_no = '$user_id'
AND thesis_id = '$thesisId'
AND ref_session_type_id = 'VIV'
AND recomm_status = 'REC'
AND status = 'A'
AND archived_status is null
ORDER BY defense_date ASC";
$dbfg = $dbf;
$result_sql5 = $dbf->query($sql5); 
$dbf->next_record();
$row_cnt5 = mysql_num_rows($result_sql5);


?>					
									
				</fieldset>
				<?
		}
		else {
			/*$err = "<div class=\"error\"><span>Your Supervisor/Co-Supervisor has yet to accept the invitation to supervise you as his/her student.
						You can submit the report once he/she has accepted it.</span></div>";
			*/			
			$err = "<div class=\"error\"><span>Your Supervisor/Co-Supervisor has not been assigned yet. You can submit the report once the the Supervisor/Co-Supervisor has been assigned and accepted the invitation.</span></div>";
						?>
			<table>
				<tr>
					<td><?echo $err;?></td>						 
				</tr>
			</table>
			<?			
		}
?>
<? 

} else {
	$msg = array();
	echo $msg = "<div class=\"error\"><span>You are not able to submit your <strong>Thesis</strong>. You can only submit thesis for thesis after the date of viva evaluation has been recommended by supervisor.</span></div>"; } ?>
	</form>
	<script>
		<?=$jscript;?>	
	</script>
	
	</body>
</html>




