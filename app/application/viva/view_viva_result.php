<? session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Submit Defense Proposal</title>
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
	<script type="text/javascript">
	function deleteReport() 
	{
		var ask = window.confirm("Are you sure to delete this amendment? \nClick OK to proceed or CANCEL to stay on the same page.");
		if (ask) 
		{
			return true;
		}
		return false;
	}
	</script>

	<script type="text/javascript">
	function submitReport() 
	{
		var ask = window.confirm("Are you sure to submit Amendment On Thesis? \nClick OK to proceed or CANCEL to stay on the same page.");
		if (ask) 
		{
			//document.location.href = "../monthlyreport/new_defense.php?pid=" + pid + "&tid=" + tid + "&id=" + id;
			return true;
		}
		return false;
	}

	</script>
	
<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: new_defense.php
//
// Created by: Zuraimi
// Created Date: 24-Jun-2015
// Modified by: Zuraimi
// Modified Date: 24-Jun-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();
$pgThesisId=$_REQUEST['tid'];
$proposalId=$_GET['pid'];
$vivaId=$_GET['vid'];

$save=$_GET['save'];

if($save == '1')
{
	$msg[] = "<div class=\"success\"><span>Your thesis has been submitted successfully.</span></div>";
}


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

/**
 *
 * @create a roman numeral from a number
 *
 * @param int $num
 *
 * @return string
 *
 */
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

function runnum2($column_name, $tblname) 
{ 
    global $db_klas2;
    
    $run_start = "001";
    
    $sql_slct_max = "SELECT MAX(SUBSTR($column_name,2,11)) AS run_id FROM $tblname";
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
function runnum3($column_name, $tblname) 
{ 
    global $db;
    
    $run_start = "0001";
    
    $sql_slct_max = "SELECT MAX(SUBSTR($column_name,2,12)) AS run_id FROM $tblname";
    $sql_slct = $db;
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

$sql_viva = "SELECT a.id AS pgVivaId, a.viva_status, a.submit_status, a.submit_date,
a.session_no, b.final_result, DATE_FORMAT(b.final_result_date,'%d-%b-%Y') as final_result_date, c.description, b.school_remark
FROM pg_viva a
LEFT JOIN pg_evaluation_viva b ON (b.pg_viva_id = a.id)
LEFT JOIN ref_recommendation c ON (c.id = b.final_result)
WHERE a.pg_thesis_id = '$pgThesisId'
AND a.pg_proposal_id = '$proposalId'
AND a.student_matrix_no = '$user_id'
AND (submit_status IN ('SAV', 'SUB', 'REQ', 'CON') OR a.submit_status IS NULL)
AND a.id = '$vivaId'"; 
				
$result_sql_viva = $dba->query($sql_viva); //echo $sql;
$dba->next_record();
$row_cnt_viva = mysql_num_rows($result_sql_viva);

							
$pgVivaId = $dba->f('pgVivaId');
$final_result = $dba->f('final_result');
$final_result_date = $dba->f('final_result_date');
$finalDesc = $dba->f('description');
$school_remark = $dba->f('school_remark');

$sql_supervisor = " SELECT a.pg_employee_empid, a.ref_supervisor_type_id, d.description as supervisor_type, 
DATE_FORMAT(a.acceptance_date,'%d-%b-%Y') as acceptance_date, h.description as role_status_desc,i.id as pgVivaDetailId, i.respond_status
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
AND i.status <> 'ARC'
AND i.pg_viva_id = '$pgVivaId'
ORDER BY d.seq, a.ref_supervisor_type_id";

$result_sql_supervisor = $db_klas2->query($sql_supervisor); //echo $sql;
$db_klas2->next_record();
$row_cnt_supervisor = mysql_num_rows($result_sql_supervisor);



$sql4 = "SELECT a.id
FROM pg_calendar a
LEFT JOIN pg_viva b ON (b.pg_calendar_id = a.id)
WHERE a.student_matrix_no = '$user_id'
AND a.thesis_id = '$thesisId'
AND a.ref_session_type_id = 'VIV'
AND a.recomm_status = 'REC'
AND a.status = 'A'
AND b.id = '$pgVivaId'
ORDER BY defense_date ASC";

$result_sql4 = $dbg->query($sql4); 
$dbg->next_record();
$row_cnt4 = mysql_num_rows($result_sql4);
$calendarIdViva = $dbg->f('id');


?>

	

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?
if(isset($redirect)) {
?>
		<META http-equiv="refresh" content="4;URL=list_amendment.php"> 
<? } ?>
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
	function newReport() 
	{
		var ask = window.confirm("Are you sure to submit another Monthly Defense Report? \nClick OK to proceed or CANCEL to stay on the same page.");
		if (ask) 
		{
			//document.location.href = "../monthlyreport/new_defense.php?pid=" + pid + "&tid=" + tid + "&id=" + id;
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
function newAttachment(vid,tid,pid) {
    var ask = window.confirm("Ensure your viva has been saved before proceed or otherwise the last change will be discarded.\nClick OK to proceed or CANCEL to stay on the same page.");
    if (ask) {
		document.location.href = "../viva/viva_edit_attachment.php?vid=" + vid + "&tid=" +tid+"&pid=" +pid;

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
			<table border="0"> 
				<tr>
				<td><h3><strong>VIVA Evaluation Details </strong><h3></td>
				</tr>
			</table>
			<table width="52%">
				<!--<tr>
					<td width="20%">Report Status</td>
					<td width="2%">:</td>
					<?if ($defenseDesc=="") $defenseDesc='New';?>
					<td width="30%"><strong><?=$defenseDesc?></strong></td>
				</tr>-->
				<!--<tr>
					<td>Reference No</td>
					<td>:</td>
					<td><strong><?=$referenceNo?></strong></td>
				</tr>-->
				<tr>
					<td>Thesis / Project ID</td>
					<td>:</td>
					<td><label><?=$thesisId;?></label></td>
					<input type="hidden" name="id" id="id" value="<?=$id; ?>">
					<input type="hidden" name="thesisId" id="thesisId" value="<?=$thesisId; ?>">
					<input type="hidden" name="proposalId" id="proposalId" value="<?=$proposalId; ?>">
					<input type="hidden" name="vivaId" id="vivaId" value="<?=$pgVivaId; ?>">
				</tr>
				<tr>
					<td>Student Matrix No</td>
					<td>:</td>
					<td><?=$user_id?></td>
				</tr>
					<?
					$sql1 = "SELECT name AS student_name
					FROM student
					WHERE matrix_no = '$user_id'";
					if (substr($user_id,0,2) != '07') { 
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
				<?$jscript3 = "";?>
				<tr>
					<td>Evaluation Schedule</td>
					<td>:</td>				
					<?
		
					$sql3 = "SELECT id, defense_date, DATE_FORMAT(defense_date,'%d-%b-%Y') as viva_date, 
					DATE_FORMAT(defense_stime,'%h:%i%p') as viva_stime,
					DATE_FORMAT(defense_etime,'%h:%i%p') as viva_etime, venue, recomm_status, status
					FROM pg_calendar
					WHERE student_matrix_no = '$user_id'
					AND thesis_id = '$thesisId'
					/*AND recomm_status = 'REC'*/
					/*AND defense_date >= DATE_ADD(DATE_FORMAT(NOW(),'%Y-%m-%d'),INTERVAL ".$defenseDurationParam." DAY)*/
					AND status = 'A'
					AND id = '$calendarIdViva'
					ORDER BY defense_date ASC";
					
					$result_sql3 = $dba->query($sql3); 
					$dba->next_record();
					
					$recommendedId = $dba->f('id');
					$vivaDate = $dba->f('viva_date');
					$vivaSTime = $dba->f('viva_stime');
					$vivaETime = $dba->f('viva_etime');	
					$venue = $dba->f('venue');		
					$calendarStatus = $dba->f('status');
					$recommStatus = $dba->f('recomm_status');

					?>
					<td><?=$vivaDate?>, <?=$vivaSTime?> to <?=$vivaETime?>, <?=$venue?></td>				
				</tr>   			
			</table>
			<br />
			<legend><strong>List of Supervisor/Co-Supervisor</strong></legend>
			<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="80%" class="thetable">			
					<tr>
						<th width="5%">No</th>					
						<th width="15%">Role / Acceptance Date</th>
						<th width="15%" align="left">Staff ID</th>
						<th width="25%" align="left">Name</th>
						<th width="5%" align="left">Faculty</th>
						<th width="15%" align="left">Feedback</th>
						<th width="15%" align="left">Status</th>
						<!--<th width="15%">Last Update</th>-->
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
							$confirmAcceptanceDate = $db_klas2->f('acceptance_date');
							
							$pgVivaDetailId = $db_klas2->f('pgVivaDetailId');
							$respond_status = $db_klas2->f('respond_status');
							

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
							<input type="hidden" name="supervisorIdArray[]" id="supervisorIdArray" value="<?=$employeeId; ?>">
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
								<br/><?=$acceptanceDate?></td>
								<input type="hidden" name="confirmAcceptanceDate" id="confirmAcceptanceDate" value="<?=$confirmAcceptanceDate; ?>">
								<td align="left"><?=$employeeId;?></td>
								<td align="left"><?=$employeeName;?></td>
								<td align="left"><a href="javascript:void(0);" onMouseOver="toolTip('<?=$departmentName;?>', 300)" onMouseOut="toolTip()"><?=$departmentId;?></a></td>
								
								<?
								/*$sql12 = "SELECT b.status as defense_detail_status, c.description as defense_detail_desc,
								DATE_FORMAT(b.responded_date,'%d-%b-%Y %h:%i %p') AS responded_date
								FROM pg_defense a
								LEFT JOIN pg_defense_detail b ON (b.pg_defense_id = a.id)
								LEFT JOIN ref_proposal_status c ON (c.id = b.status)
								WHERE a.student_matrix_no = '$user_id'
								AND a.reference_no = '$referenceNo'
								AND b.pg_employee_empid = '$employeeId'
								AND a.pg_thesis_id = '$thesisId'
								AND a.pg_proposal_id = '$proposalId'
								/*AND a.status NOT IN ('SAV')
								AND a.archived_status is null
								AND b.archived_status is NULL";
								
								$result12 = $dbg->query($sql12); 
								$dbg->next_record();
								$row_cnt12 = mysql_num_rows($result12);
								$defenseDetailStatus=$dbg->f('defense_detail_status');
								$defenseDetailDesc=$dbg->f('defense_detail_desc');
								$respondedDate=$dbg->f('responded_date');
								*/
								if ($respond_status == 'REQ') {
								?>
								
								<td><a href="../viva/view_viva_feedback_result.php?tid=<?=$thesisId;?>&pid=<?=$proposalId;?>&empid=<?=$employeeId;?>&mn=<?=$user_id?>&vd=<?=$pgVivaDetailId?>&vid=<?=$vivaId?>" name="thesisId" value="<?=$thesisId?>" title="View feedback"><img src="../images/view.jpg" width="45" height="30" style="border:0px;" title="View feedback"></a></td>
							<? } else if ($respond_status == 'CON') {
								?>
								
								<td><a href="../viva/view_viva_feedback_result.php?tid=<?=$thesisId;?>&pid=<?=$proposalId;?>&empid=<?=$employeeId;?>&mn=<?=$user_id?>&vd=<?=$pgVivaDetailId?>&vid=<?=$vivaId?>" name="thesisId" value="<?=$thesisId?>" title="View feedback"><img src="../images/view.jpg" width="45" height="30" style="border:0px;" title="View feedback"></a></td>
							<? } else if($respond_status == 'SUB'){?>
									<td>Pending</td>
									<?
								}
								if ($respond_status == '' || empty($respond_status) || $respond_status == 'SUB') {
								?>
									<td align="left"><label>Expecting on Thesis for VIVA </label></td>
								<? } else if ($respond_status == 'REQ') {?>
									<td align="left"><label>Request Changes</label></td>
								<? } else if ($respond_status == 'CON') {?>
									<td align="left"><label>Confirmed</label></td>
								<? } else {
									?>								
									<td></td>
									<?
								}?>
								<!--<td align="left"><label><?=$respondedDate;?></label></td>-->
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
										<!--<span style="color:#FF0000"> Make sure your Supervisor has been assigned first before submit the Monthly Defense report</span>--></td>
							</tr>
						</table>
						<?
					}?>	
					</table>
			<br/>
<? 
	/*$sqlex = "SELECT a.pg_employee_empid, b.description
	FROM pg_supervisor a
	LEFT JOIN ref_supervisor_type b ON (b.id = a.ref_supervisor_type_id)
	WHERE a.pg_student_matrix_no = '$user_id'
	AND a.ref_supervisor_type_id IN ('EE','IE')
	AND a.status = 'A'";
	
	$result_sqlex = $dbb->query($sqlex); 
	$dbb->next_record();
	$row_cntex = mysql_num_rows($result_sqlex);
	$i= 0;
	$inc= 0;
	$exEmpid = array();
	$exDesc = array();
	
	do{
		$exEmpid[$i] = $dbb->f('pg_employee_empid');
		$exDesc[$i] = $dbb->f('description');

		$inc++;
		$i++;

	}while($dbb->next_record());


?>
	<fieldset><legend><strong>List of Examiner</strong></legend>
	<table class="thetable" width="60%">
		<tr>
			<th width="5%">No</th>
			<th width="30%">Name/Staff ID</th>
			<th width="17%">Role</th>
			<th width="48%">Faculty</th>
		</tr>
		<? for ($i=0; $i<$inc; $i++){ ?>
		<? 
			$sql_employee="SELECT  b.name, c.id, c.description
			FROM new_employee b 
			LEFT JOIN dept_unit c ON (c.id = b.unit_id) 
			WHERE b.empid= '$exEmpid[$i]'";
			$dbc1 = $dbc;
			$result_sql_employee = $dbc1->query($sql_employee);
			$dbc1->next_record();
			
			$exName = $dbc1->f('name');
			$exDescFac = $dbc1->f('description');
		
		
		?>
		<tr>
			<td><?=$i+1?>.</td>
			<td><?=$exName?> (<?=$exEmpid[$i]?>)</td>
			<td><?=$exDesc[$i]?></td>
			<td><?=$exDescFac?></td>
		</tr>
		<? } *///?>
	</table>
	  </fieldset>


<?
		$sqlUpload="SELECT * FROM file_upload_viva
		WHERE pg_viva_id = '$pgVivaId' 
		AND student_matrix_no = '$user_id'";			
		$dbh = $dbf;
		$result = $dbh->query($sqlUpload); 
		$dbh->next_record();
		$row_upload = mysql_num_rows($result);
		$i = 0;
		$inc = 0;
		do{
			
			$namefile = $dbh->f('fu_document_filename');
			$fu_cd = $dbh->f('fu_cd');
			$namefileArray[$i] = $namefile;
			$fu_cdArray[$i] = $fu_cd;
			$i++;
			$inc++;
		}while($dbh->next_record());

?>
	<table>
		<tr>
			<td><strong>Attachment by Student For VIVA</strong></td>
		</tr>
		<tr>
			<td>
			<? for ($i=0; $i<$inc; $i++)
			{ if($row_upload>0) {?>
			
				<a href="downloadviva1.php?id=<?=$fu_cdArray[$i];?>" target="_blank"><?=$namefileArray[$i] ?>
				<img src="../images/download.png" width="20" height="19" style="border:0px;" title="Download <?=$namefileArray[$i]?>"></a>
					
				<? 
					if($i%4 == 0 && $i !=0)
					{
					   echo "<br>";
					}  
				} 
				else {
				?>No attachment Uploaded<?
				}
			}?>
			</td>
		</tr>
	</table>

	
	<table>
		<tr>
		  <td colspan="2">
		  <input type="hidden" name="totalA" id="totalA" value="<?=$attachment?>"/>
		  <input type="button" name="btnBack" id="btnBack" value="Back" onClick="javascript:document.location.href='viva_result.php';"/>			
		</tr>
	</table>
<?
//////////////DATA from pg_evaluation_viva////////////





?>
	<strong>VIVA Result</strong>
	<table class="thetable" border="1">
		<tr>
			<th>Final Result</td>
			<td width="450"><span style="margin-left:10px;"><?=$finalDesc?></span></td>
		</tr>
		<tr>
			<th>Remarks from Academic Committee</td>
			<td width="450"><span style="margin-left:10px;"><?=$school_remark?></span></td>
		</tr>
	</table>

	</form>
	<script>
		<?=$jscript1;?>
		<?=$jscript2;?>
		<?=$jscript3;?>
	</script>
</body>
</html>




