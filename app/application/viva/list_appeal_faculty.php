<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: recommend_defense.php
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
/*ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);*/

$user_id=$_SESSION['user_id'];
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
function runnum2($column_name, $tblname) 
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

if(isset($_POST['btnaccept']) && ($_POST['btnaccept'] <> "")) {

	$curdatetime = date("Y-m-d H:i:s");
	
	$sql1 = "SELECT b.acceptance_status, b.pg_employee_empid,b.id, a.pg_student_matrix_no, a.pg_thesis_id
	FROM pg_invitation a 
	LEFT JOIN pg_invitation_detail b ON (b.pg_invitation_id = a.id) 
	WHERE b.pg_employee_empid = '$user_id' 
	AND a.status = 'A'";
	
	$result_sql1 = $db->query($sql1); 
	$db->next_record();
	$acceptance_status=$db->f('acceptance_status');
	$pg_employee_empid=$db->f('pg_employee_empid');
	$id=$db->f('id');
	$pg_thesis_id=$db->f('pg_thesis_id');
	$pg_student_matrix_no=$db->f('pg_student_matrix_no');
	
	$sql7 = "UPDATE pg_invitation_detail
	SET modify_by = '$user_id', modify_date = '$curdatetime', acceptance_status = 'ACC'
	WHERE id = '$id'
	AND status = 'A'
	AND pg_employee_empid = '$pg_employee_empid'";
				
	$db->query($sql7);
	
	$sql2 = "SELECT * FROM pg_evaluation_viva
	WHERE student_matrix_no = '$pg_student_matrix_no'
	AND pg_thesis_id = '$pg_thesis_id'
	AND status = 'A'";
	
	$result_sql2 = $dbf->query($sql2); 
	$dbf->next_record();
	$id=$dbf->f('id');
	
	if(!empty($id))
	{
		$pgVivaDetailNew = "D".runnum2('id','pg_evaluation_viva_detail');
		
		$sqlviva = "INSERT INTO pg_evaluation_viva_detail 
		(id, pg_eva_viva_id, status, insert_by, insert_date, pg_empid_viva)
		VALUES
		('$pgVivaDetailNew', '$id', 'A' , '$user_id' , '$curdatetime', '$user_id')";
		$db->query($sqlviva);
	}


}


if(isset($_POST['btnUpdateDay']) && ($_POST['btnUpdateDay'] <> "")) {
	
	$totalDay = $_REQUEST['dayEnd'];
	$msg = Array();
	if(!preg_match('/^[0-9]*$/', $totalDay)) 
	{
		$msg[] = "<div class=\"error\"><span>Please insert positive number only.</span></div>";	
	}
	
	if(empty($msg)) {
	
		$sql_day="UPDATE base_constant
		SET const_value = '$totalDay'
		WHERE const_term = 'NO_OF_APPEAL_DAY'";
		$dbgDay = $dbg;
		$result_sql_day = $dbgDay->query($sql_day);
		
		$msg[] = "<div class=\"success\"><span>Update Success.</span></div>";	
		
	}
	

}

if(isset($_POST['btnCancelDefence']) && ($_POST['btnCancelDefence'] <> "")) {
}

if(isset($_POST['btnSearch']) && ($_POST['btnSearch'] <> "")) {
	$searchThesisId = $_POST['searchThesisId'];
	$searchStudent = $_POST['searchStudent'];
	$msg = Array();
	
	if ($searchThesisId!="") 
	{
		$tmpSearchThesisId = " AND b.pg_thesis_id = '$searchThesisId'";
	}
	else 
	{
		$tmpSearchThesisId="";
	}
	
	if ($searchStudent!="") 
	{
		$tmpSearchStudent = " AND b.student_matrix_no = '$searchStudent'";
	}
	else 
	{
		$tmpSearchStudent="";
	}

	
	
	$sql = "SELECT a.id as appealId, a.pg_viva_id, a.result_appeal, a.date_request, a.student_matrix_no, 
	b.pg_thesis_id, c.thesis_title, b.pg_calendar_id
	FROM pg_viva_appeal a 
	LEFT JOIN pg_viva b ON (b.id = a.pg_viva_id)
	LEFT JOIN pg_proposal c ON (c.pg_thesis_id = b.pg_thesis_id)
	WHERE b.viva_status = 'FAI'
	".$tmpSearchThesisId."
	".$tmpSearchStudent."
	AND b.status IN ('ARC', 'ARC1') 	
	AND a.status IN ('SUB', 'A')
	AND c.archived_status IS NULL
	ORDER BY a.date_request, a.id";

	$result_sql = $dbg->query($sql); 
	$dbg->next_record();
	$row_cnt = mysql_num_rows($result_sql);

	$appealId = Array();
	$pg_viva_id = Array();
	$result_appeal = Array();
	$student_matrix_no = Array();
	$date_request = Array();
	$pg_thesis_id = Array();
	$pg_calendar_id = Array();
	

	$no=0;
	$no1=0;
	$no3 = 0;

	if ($row_cnt > 0) {
		do {
			$appealId[$no]=$dbg->f('appealId');
			$pg_viva_id[$no]=$dbg->f('pg_viva_id');
			$result_appeal[$no]=$dbg->f('result_appeal');
			$student_matrix_no[$no]=$dbg->f('student_matrix_no');
			$date_request[$no]=$dbg->f('date_request');
			$pg_thesis_id[$no]=$dbg->f('pg_thesis_id');
			$pg_calendar_id[$no]=$dbg->f('pg_calendar_id');
			$appealId[$no]=$dbg->f('appealId');
						
			$no++;
			$no3++;
		}while ($dbg->next_record());
		
		for ($i=0; $i<$row_cnt; $i++){
			if (substr($studentMatrixNoArray[$i],0,2) != '07') { 
				$dbConn=$dbc; 
			} 
			else { 
				$dbConn=$dbc1; 
			}

			$sql1 = "SELECT name
			FROM student
			WHERE matrix_no = '$student_matrix_no[$i]'";
			
			$result_sql1 = $dbConn->query($sql1); 
			$dbConn->next_record();
			$studentNameArray[$no1]=$dbConn->f('name');
			$thesisIdArray[$no1]=$thesisIdArray[$i];
			$proposalIdArray[$no1]=$proposalIdArray[$i];
			$studentMatrixNoArray[$no1]=$studentMatrixNoArray[$i];
			$supervisorTypeIdArray[$no1]=$supervisorTypeIdArray[$i];
			$supervisorTypeDescArray[$no1]=$supervisorTypeDescArray[$i];
			$acceptanceDateArray[$no1]=$acceptanceDateArray[$i];
			$roleStatusArray[$no1]=$roleStatusArray[$i];
			$roleStatusDescArray[$no1]=$roleStatusDescArray[$i];
			$no1++;
		}
		if ($no1 == 0) {			
			$msg[] = "<div class=\"error\"><span>No record(s) found.</span></div>";			
		}
		$row_cnt = $no1;
	}
	else {
		$msg[] = "<div class=\"error\"><span>No record(s) found.</span></div>";
	}
}
else {

	$sql = "SELECT a.id as appealId, a.pg_viva_id, a.result_appeal, a.date_request, a.student_matrix_no, 
	b.pg_thesis_id, c.thesis_title, b.pg_calendar_id
	FROM pg_viva_appeal a 
	LEFT JOIN pg_viva b ON (b.id = a.pg_viva_id)
	LEFT JOIN pg_proposal c ON (c.pg_thesis_id = b.pg_thesis_id)
	WHERE b.viva_status = 'FAI'
	AND b.status IN ('ARC', 'ARC1') 	
	AND a.status IN ('SUB', 'A')
	AND c.archived_status IS NULL
	ORDER BY a.date_request, a.id";

	$result_sql = $dbg->query($sql); 
	$dbg->next_record();
	$row_cnt = mysql_num_rows($result_sql);

	$appealId = Array();
	$pg_viva_id = Array();
	$result_appeal = Array();
	$student_matrix_no = Array();
	$date_request = Array();
	$pg_thesis_id = Array();
	$pg_calendar_id = Array();
	
	$no=0;
	$no1=0;
	$no3 = 0;

	if ($row_cnt > 0) {
		do {
			$appealId[$no]=$dbg->f('appealId');
			$pg_viva_id[$no]=$dbg->f('pg_viva_id');
			$result_appeal[$no]=$dbg->f('result_appeal');
			$student_matrix_no[$no]=$dbg->f('student_matrix_no');
			$date_request[$no]=$dbg->f('date_request');
			$pg_thesis_id[$no]=$dbg->f('pg_thesis_id');
			$pg_calendar_id[$no]=$dbg->f('pg_calendar_id');
			$appealId[$no]=$dbg->f('appealId');
						
			$no++;
			$no3++;
		}while ($dbg->next_record());
		
		for ($i=0; $i<$row_cnt; $i++){
			if (substr($studentMatrixNoArray[$i],0,2) != '07') { 
				$dbConn=$dbc; 
			} 
			else { 
				$dbConn=$dbc1; 
			}

			$sql1 = "SELECT name
			FROM student
			WHERE matrix_no = '$student_matrix_no[$i]'";
			
			$result_sql1 = $dbConn->query($sql1); 
			$dbConn->next_record();
			$studentNameArray[$no1]=$dbConn->f('name');
			
			$thesisIdArray[$no1]=$thesisIdArray[$i];
			$proposalIdArray[$no1]=$proposalIdArray[$i];
			$studentMatrixNoArray[$no1]=$studentMatrixNoArray[$i];
			$supervisorTypeIdArray[$no1]=$supervisorTypeIdArray[$i];
			$supervisorTypeDescArray[$no1]=$supervisorTypeDescArray[$i];
			$acceptanceDateArray[$no1]=$acceptanceDateArray[$i];
			$roleStatusArray[$no1]=$roleStatusArray[$i];
			$roleStatusDescArray[$no1]=$roleStatusDescArray[$i];
			$no1++;
		}
	}
}

	/*$sql1 = "SELECT i.respond_status, i.result_status, i.reference_no AS refNo, i.respond_by,
	DATE_FORMAT(i.respond_date,'%d-%b-%Y %h:%i:%s %p') AS respond_date, 
	i.id AS vivaEvaId, a.pg_thesis_id, g.id AS proposal_id, a.pg_student_matrix_no, a.ref_supervisor_type_id, 
	d.description AS supervisor_type_desc, DATE_FORMAT(a.acceptance_date,'%d-%b-%Y %h:%i:%s %p') AS acceptance_date, a.role_status, 
	h.description AS role_status_desc, i.result_status AS resultStatusMain, 
	l.description AS recommDesc, a.ref_supervisor_type_id 
	FROM pg_supervisor a 
	LEFT JOIN ref_supervisor_type d ON (d.id = a.ref_supervisor_type_id) 
	LEFT JOIN pg_thesis f ON (f.student_matrix_no = a.pg_student_matrix_no) 
	LEFT JOIN pg_proposal g ON (g.pg_thesis_id = f.id) 
	LEFT JOIN ref_role_status h ON (h.id = a.role_status) 
	LEFT JOIN pg_evaluation_viva i ON (i.pg_thesis_id = f.id) 
	LEFT JOIN ref_recommendation l ON (l.id = i.result_status) 
	LEFT JOIN pg_calendar k ON (k.thesis_id = f.id) 
	WHERE a.acceptance_status = 'ACC' 
	AND a.ref_supervisor_type_id IN ('EC') 
	AND g.verified_status IN ('APP','AWC') 
	AND g.status IN ('APP','APC') 
	AND g.archived_status IS NULL 
	AND a.status = 'A' AND k.ref_session_type_id = 'VIV' 
	AND k.recomm_status = 'REC' 
	AND i.id IS NOT NULL 
	ORDER BY d.seq, a.ref_supervisor_type_id  ";
	
	$dbg33 = $dbg;
	$result_sql1 = $dbg33->query($sql1); 
	$dbg33->next_record();
	$row_cnt = mysql_num_rows($result_sql1);
	$thesisId = $dbg33->f('pg_thesis_id');
	$matrixNo = $dbg33->f('pg_student_matrix_no');
	$vivaEvaId = $dbg33->f('vivaEvaId');
	$respond_by = $dbg33->f('respond_by');*/
	

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
    	<script type="text/javascript" src="../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
		<script src="../../lib/js/datePicker/jquery.ui.core.js"></script>
		<script src="../../lib/js/datePicker/jquery.ui.widget.js"></script>
		<script src="../../lib/js/datePicker/jquery.ui.datepicker.js"></script>
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

	<fieldset>
	<legend><strong>List of VIVA Appeal </strong></legend>
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
				<td><input type="text" name="searchStudent" size="15" id="searchStudent" value="<?=$searchStudent;?>"/>
				<input type="submit" name="btnSearch" value="Search" /><span style="color:#FF0000"> Note:</span> If no entry is provided, it will search all.</td>

			</tr>
		</table>
		
		<table>
			<tr>							
				<td><span style="color:#FF0000"> Notes:</span><br/>
				1. The appeal will appear if student has submitted the appeal.</td>
			</tr>
		</table>
		<br/>
		<table>
			<tr>							
				<td>Searching Results:- <?=$row_cnt ?> record(s) found.</td>
			</tr>
		</table>
		<?if ($row_cnt <= 1) {?>
			<div id = "tabledisplay" style="overflow:auto; height:110px;">
		<?}
		else if ($row_cnt <= 3) {?>
			<div id = "tabledisplay" style="overflow:auto; height:200px;">
		<?}
		else {
			?>
			<div id = "tabledisplay" style="overflow:auto; height:300px;">
			<?
		}?>		
		<?
		
		$sql2 = "SELECT const_value
		FROM base_constant
		WHERE const_category = 'DEFENSE_PROPOSAL'
		AND const_term = 'DEFENSE_DURATION'";
		
		$result_sql2 = $db->query($sql2); 
		$db->next_record();
		$defenseDurationParam = $db->f('const_value');
		
		$currentDate1 = date('d-M-Y');
		$tmpCurrentDate = new DateTime($currentDate1);
		$myTmpCurrentDate = $tmpCurrentDate->format('d-M-Y');
		$currentDate = new DateTime($myTmpCurrentDate);
		$expectedDate = date('d-M-Y', strtotime($currentDate1. ' '.($defenseDurationParam).' day'));
		?>
		<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="95%" class="thetable">			
			<tr>
				<th width="3%">No</th>					
				<!--<th width="7%">Reference No </th>-->
				<th width="19%" align="left">Thesis / Project ID</th>
				<!--<th width="7%" align="left">Thesis Title</th>-->
				<th width="23%" align="left">Student Name <br />
			    (Student Matrix No)</th>
				<th width="25%" align="left">Session Date</th>
				<th width="22%" align="left">Result Status </th>
				<th width="8%">Action </th>
			</tr>
			<?
			if ($row_cnt > 0 ) {?>	
				<? 
				$no=0;
				for ($j=0; $j<$no3; $j++){
				
					$sql3 = "SELECT a.id, a.defense_date, DATE_FORMAT(a.defense_date,'%d-%b-%Y') as defense_date1, 
					DATE_FORMAT(a.defense_stime,'%h:%i%p') as defense_stime,
					DATE_FORMAT(a.defense_etime,'%h:%i%p') as defense_etime, a.venue, a.ref_session_type_id, 
					b.description as ref_session_type_desc, c.id AS pgVivaId, c.submit_status, c.viva_status, 
					DATE_FORMAT(c.submit_date,'%d-%b-%Y %h:%i%p') as submit_date
					FROM pg_calendar a
					LEFT JOIN ref_session_type b ON (b.id = a.ref_session_type_id)
					LEFT JOIN pg_viva c ON (c.pg_calendar_id = a.id)
					WHERE a.student_matrix_no = '$student_matrix_no[$j]'
					AND a.thesis_id = '$pg_thesis_id[$j]'
					AND a.id = '$pg_calendar_id[$j]'
					AND a.status = 'A'
					AND (a.recomm_status IN ('REC', '') OR a.recomm_status IS NULL)
					AND c.status IN ('A', 'ARC', 'ARC1') 
					AND c.submit_status <> 'SAV'
					ORDER BY a.defense_date ASC";
					
					$result_sql3 = $dba->query($sql3); 
					$dba->next_record();
					$row_cnt_sql3 = mysql_num_rows($result_sql3);
					

					$recommendedIdArray = $dba->f('id');
					$vivaDate = $dba->f('defense_date1');
					$vivaSTime = $dba->f('defense_stime');
					$vivaETime = $dba->f('defense_etime');
					$venue = $dba->f('venue');
					$sessionTypeArray = $dba->f('ref_session_type_id');
					$sessionTypeDescArray = $dba->f('ref_session_type_desc');
					
					$pgVivaId = $dba->f('pgVivaId');
					$submit_status = $dba->f('submit_status');
					$viva_status = $dba->f('viva_status');
					$submit_date = $dba->f('submit_date');
					
					$sqlvivaresult = "SELECT description from ref_proposal_status where id = '$result_appeal[$j]'";
					
					$dbf1 = $dbf;
					$result_sqlvivaresult = $dbf1->query($sqlvivaresult); 
					$dbf1->next_record();
					$descriptionF = $dbf1->f('description');
					if (empty($descriptionF))
					{
						$descriptionF = 'Not decided yet';
					}					
					
					$sqlvivaresult = "SELECT description from ref_recommendation
					WHERE id = '$resultStatusMain[$j]'
					AND status = 'A'";
					
					$dbf2 = $dbf;
					$result_sqlvivaresult = $dbf2->query($sqlvivaresult); 
					$dbf2->next_record();
					
					$mainDesc = $dbf2->f('description');
					
					?>
					<tr>
						<td align="center"><?=$no+1;?>.</td>
						<!--<td align="left"><label><?=$refNoArray[$j]?></label></td>-->
						<td align="left"><label><?=$pg_thesis_id[$j]?></label></td>
						<!--<td align="left"><label><?=$thesis_title[$j]?></label></td>-->
						<td align="left"><label><?=$studentNameArray[$j]?><br />(<?=$student_matrix_no[$j]?>)</label></td>
						<td align="left"><?=$vivaDate?>, <?=$vivaSTime?> to <?=$vivaETime?>, <br /><?=$venue?></td>
											
						<td width="22%" align="left"><?=$descriptionF?></td>					
						<td width="8%" align="center">
					<? if(empty($result_appeal[$j])) { ?>	
						<input type="button" name="btnUpdate" id="btnUpdate" value="Update" 
						onClick="javascript:document.location.href='appeal_review.php?tid=<?=$pg_thesis_id[$j]?>&vid=<?=$pg_viva_id[$j]?>&mn=<?=$student_matrix_no[$j]?>&aid=<?=$appealId[$j]?>';"/>
					<? } else if(!empty($result_appeal[$j])) { ?>
						<input type="button" name="btnView" id="btnView" value="View" 
						onClick="javascript:document.location.href='appeal_review_view.php?tid=<?=$pg_thesis_id[$j]?>&vid=<?=$pg_viva_id[$j]?>&mn=<?=$student_matrix_no[$j]?>&aid=<?=$appealId[$j]?>';"/>
					<? } else {  }?>						</td>
					</tr>
					<?
				$no++;	
				}
				?>

				
				<?
			}
			else {
			?>
				<table>
					<tr>
						<td>No record found!</td>
					</tr>
				</table>	
				<?
			}?>	
		</table>
		</div>
	</fieldset>
	
	<?
	$sql_day="SELECT const_value FROM base_constant WHERE const_term = 'NO_OF_APPEAL_DAY'";
	$dbgDay = $dbg;
	$result_sql_day = $dbgDay->query($sql_day);
	$dbgDay->next_record();
	
	$noOfDay = $dbgDay->f('const_value');

	
	$date = strtotime("+".$noOfDay." day");
	$defaultEndDate = date('d-M-Y', $date);
	?>
	<fieldset><legend><strong>Number of Minimum Appeal Day Setup</strong></legend>
	<table width="583">
		<tr>
			<td width="167">Number of Minimum Day</td>
			<td width="14">:</td>
			<td width="164">
			<input type="text" id="dayEnd" name="dayEnd" value="<?=$noOfDay?>" required/>
		  </td>
			<td width="218">
			<input name="btnUpdateDay" value="Update" type="submit" />
		  </td>
		</tr>
		<tr>
			<td colspan="4"><span style="color:red;">Notes</span>:<br />
			1. This will be the default minimum day for student to request for appeal.<br />
			2. Only positive number is allow.
			</td>
		</tr>
		
	</table>
	</fieldset>	
	<script>
	<?=$jscript;?>
	</script>		
</body>
</html>




