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

if(isset($_POST['btnRecommendation']) && ($_POST['btnRecommendation'] <> "")) {
}


if(isset($_POST['btnRecommendation1']) && ($_POST['btnRecommendation1'] <> "")) {
}

if(isset($_POST['btnCancelDefence']) && ($_POST['btnCancelDefence'] <> "")) {
}

if(isset($_POST['btnSearch']) && ($_POST['btnSearch'] <> "")) {
	$searchThesisId = $_POST['searchThesisId'];
	$searchStudent = $_POST['searchStudent'];
	$searchReferenceNo = $_POST['searchReferenceNo'];
	$msg = Array();
	
	if ($searchThesisId!="") 
	{
		$tmpSearchThesisId = " AND i.pg_thesis_id = '$searchThesisId'";
	}
	else 
	{
		$tmpSearchThesisId="";
	}
	
	if ($searchStudent!="") 
	{
		$tmpSearchStudent = " AND i.student_matrix_no = '$searchStudent'";
	}
	else 
	{
		$tmpSearchStudent="";
	}

	if ($searchReferenceNo!="") 
	{
		$tmpSearchReferenceNo = " AND i.reference_no = '$searchReferenceNo'";
	}
	else 
	{
		$tmpSearchReferenceNo="";
	}
	
	
	$sql = " SELECT i.reference_no, g.thesis_title, i.amendment_status AS amendmentStatus, i.confirm_status as realConfStatus,  
	DATE_FORMAT(i.submit_date,'%d-%b-%Y %h:%i:%s %p') AS submit_date, i.id as amendmentId, j.submit_status as mainStatus,
	a.pg_thesis_id, g.id AS proposal_id, 
	a.pg_student_matrix_no, a.ref_supervisor_type_id, 
	d.description AS supervisor_type_desc, DATE_FORMAT(a.acceptance_date,'%d-%b-%Y %h:%i:%s %p') AS acceptance_date, 
	a.role_status, h.description AS role_status_desc, j.pg_calendar_id
	FROM pg_supervisor a 
	LEFT JOIN ref_supervisor_type d ON (d.id = a.ref_supervisor_type_id) 
	LEFT JOIN pg_thesis f ON (f.student_matrix_no = a.pg_student_matrix_no) 
	LEFT JOIN pg_proposal g ON (g.pg_thesis_id = f.id) 
	LEFT JOIN ref_role_status h ON (h.id = a.role_status) 
	LEFT JOIN pg_viva j ON (j.pg_thesis_id = f.id) 
	LEFT JOIN pg_amendment i ON (i.pg_viva_id = j.id) 
	LEFT JOIN pg_calendar k ON (k.id = j.pg_calendar_id) 
	WHERE a.pg_employee_empid = '$user_id' 
	AND a.acceptance_status = 'ACC' 
	AND a.ref_supervisor_type_id IN ('SV','CS','XS') 
	AND g.verified_status IN ('APP','AWC') 
	AND g.status IN ('APP','APC') 
	AND g.archived_status IS NULL 
	AND a.status = 'A' "
	.$tmpSearchStudent." "
	.$tmpSearchReferenceNo." "
	.$tmpSearchThesisId." 
	AND k.ref_session_type_id = 'VIV'
	AND k.recomm_status = 'REC'
	AND i.ref_req_no IS NULL
	AND a.role_status = 'PRI'
	AND (i.amendment_status <> 'SAV' AND i.amendment_status IS NOT NULL)
	ORDER BY d.seq, a.ref_supervisor_type_id ";

	$result_sql = $dbg->query($sql); 
	$dbg->next_record();
	$row_cnt = mysql_num_rows($result_sql);

	$thesisIdArray = Array();
	$thesisTitleArray = Array();
	$refNoArray = Array();
	$proposalIdArray = Array();
	$studentMatrixNoArray = Array();
	$studentNameArray = Array();
	$supervisorTypeArray = Array();
	$supervisorTypeArray = Array();
	$acceptanceDateArray = Array();
	$roleStatusArray = Array();
	$roleStatusDescArray = Array();
	$submitDateArray = Array();
	$amendmentStatusArray = Array();
	$realConfStatusArray = Array(); 
	$amendmentIdArray = Array();
	$mainStatusArray = Array();
	$pg_calendar_id = Array();
	$no=0;
	$no1=0;

	if ($row_cnt > 0) {
		do {
			$studentMatrixNoArray[$no]=$dbg->f('pg_student_matrix_no');
			$refNoArray[$no]=$dbg->f('reference_no');
			$thesisIdArray[$no]=$dbg->f('pg_thesis_id');
			$proposalIdArray[$no]=$dbg->f('proposal_id');
			$supervisorTypeIdArray[$no]=$dbg->f('ref_supervisor_type_id');
			$supervisorTypeDescArray[$no]=$dbg->f('supervisor_type_desc');
			$acceptanceDateArray[$no]=$dbg->f('acceptance_date');
			$roleStatusArray[$no]=$dbg->f('role_status');
			$roleStatusDescArray[$no]=$dbg->f('role_status_desc');
			$submitDateArray[$no]=$dbg->f('submit_date');
			$amendmentStatusArray[$no]=$dbg->f('amendmentStatus');
			$thesisTitleArray[$no]=$dbg->f('thesis_title');
			$realConfStatusArray[$no]=$dbg->f('realConfStatus');
			$amendmentIdArray[$no]=$dbg->f('amendmentId');
			$mainStatusArray[$no]=$dbg->f('mainStatus');
			$pg_calendar_id[$no]=$dbg->f('pg_calendar_id');
			$no++;
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
			WHERE matrix_no = '$studentMatrixNoArray[$i]'";
			
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

	$sql = "SELECT i.reference_no, g.thesis_title, i.amendment_status AS amendmentStatus, i.confirm_status as realConfStatus,  
	DATE_FORMAT(i.submit_date,'%d-%b-%Y %h:%i:%s %p') AS submit_date, i.id as amendmentId, j.submit_status as mainStatus,
	a.pg_thesis_id, g.id AS proposal_id, 
	a.pg_student_matrix_no, a.ref_supervisor_type_id, 
	d.description AS supervisor_type_desc, DATE_FORMAT(a.acceptance_date,'%d-%b-%Y %h:%i:%s %p') AS acceptance_date, 
	a.role_status, h.description AS role_status_desc, j.pg_calendar_id
	FROM pg_supervisor a 
	LEFT JOIN ref_supervisor_type d ON (d.id = a.ref_supervisor_type_id) 
	LEFT JOIN pg_thesis f ON (f.student_matrix_no = a.pg_student_matrix_no) 
	LEFT JOIN pg_proposal g ON (g.pg_thesis_id = f.id) 
	LEFT JOIN ref_role_status h ON (h.id = a.role_status) 
	LEFT JOIN pg_viva j ON (j.pg_thesis_id = f.id) 
	LEFT JOIN pg_amendment i ON (i.pg_viva_id = j.id) 
	LEFT JOIN pg_calendar k ON (k.id = j.pg_calendar_id) 
	WHERE a.pg_employee_empid = '$user_id' 
	AND a.acceptance_status = 'ACC' 
	AND a.ref_supervisor_type_id IN ('SV','CS','XS') 
	AND g.verified_status IN ('APP','AWC') 
	AND g.status IN ('APP','APC') 
	AND g.archived_status IS NULL 
	AND a.status = 'A' 
	AND k.ref_session_type_id = 'VIV'
	AND k.recomm_status = 'REC'
	AND i.ref_req_no IS NULL
	AND i.status = 'A'
	AND a.role_status = 'PRI'
	AND (i.amendment_status <> 'SAV' AND i.amendment_status IS NOT NULL)
	ORDER BY d.seq, a.ref_supervisor_type_id";

	$result_sql = $dbg->query($sql); 
	$dbg->next_record();
	$row_cnt = mysql_num_rows($result_sql);

	$thesisIdArray = Array();
	$thesisTitleArray = Array();
	$refNoArray = Array();
	$proposalIdArray = Array();
	$studentMatrixNoArray = Array();
	$studentNameArray = Array();
	$supervisorTypeArray = Array();
	$supervisorTypeArray = Array();
	$acceptanceDateArray = Array();
	$roleStatusArray = Array();
	$roleStatusDescArray = Array();
	$submitDateArray = Array();
	$amendmentStatusArray = Array();
	$realConfStatusArray = Array(); 
	$amendmentIdArray = Array();
	$mainStatusArray = Array();
	$pg_calendar_id = Array();
	$no=0;
	$no1=0;

	if ($row_cnt > 0) {
		do {
			$studentMatrixNoArray[$no]=$dbg->f('pg_student_matrix_no');
			$refNoArray[$no]=$dbg->f('reference_no');
			$thesisIdArray[$no]=$dbg->f('pg_thesis_id');
			$proposalIdArray[$no]=$dbg->f('proposal_id');
			$supervisorTypeIdArray[$no]=$dbg->f('ref_supervisor_type_id');
			$supervisorTypeDescArray[$no]=$dbg->f('supervisor_type_desc');
			$acceptanceDateArray[$no]=$dbg->f('acceptance_date');
			$roleStatusArray[$no]=$dbg->f('role_status');
			$roleStatusDescArray[$no]=$dbg->f('role_status_desc');
			$submitDateArray[$no]=$dbg->f('submit_date');
			$amendmentStatusArray[$no]=$dbg->f('amendmentStatus');
			$thesisTitleArray[$no]=$dbg->f('thesis_title');
			$realConfStatusArray[$no]=$dbg->f('realConfStatus');
			$amendmentIdArray[$no]=$dbg->f('amendmentId');
			$mainStatusArray[$no]=$dbg->f('mainStatus');
			$pg_calendar_id[$no]=$dbg->f('pg_calendar_id');
			$no++;
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
			WHERE matrix_no = '$studentMatrixNoArray[$i]'";
			
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

	<fieldset>
	<legend><strong>List of Amendment's for Review </strong></legend>
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
				<td>Reference No</td>
				<td>:</td>
				<td><input type="text" name="searchReferenceNo" size="30" id="searchReferenceNo" value="<?=$searchReferenceNo;?>"/></td>
				<td><input type="submit" name="btnSearch" value="Search" />
			    <span style="color:#FF0000"> Note:</span> If no entry is provided, it will search all.</td>
			</tr>
		</table>
		<table width="485">
			<tr>							
				<td width="477"><span style="color:#FF0000"> Notes:</span><br/>
			  1. By default, all the amendment that has been sent student will display.<br />
			  2. Only Main supervisor will be receive the amendment on thesis</td>
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
	    <table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="93%" class="thetable">
          <tr>
            <th width="2%">No</th>
            <th width="7%">Reference No </th>
            <th width="5%" align="left">Thesis / Project ID</th>
            <th width="16%" align="left">Student Name (Student Matrix No)</th>
			<th width="16%" align="left">Session Date</th>
            <th width="10%" align="left">Submitted Date</th>
            <th width="8%">Amendment Status </th>
            <th width="11%">Confirm Status </th>
            <th width="6%">Action </th>
          </tr>
          <?
			if ($row_cnt > 0 ) {?>
          <?
				$no=0;
				for ($j=0; $j<$row_cnt; $j++){
					$sql3 = "SELECT a.id, a.defense_date, DATE_FORMAT(a.defense_date,'%d-%b-%Y') as defense_date1, 
					DATE_FORMAT(a.defense_stime,'%h:%i%p') as defense_stime,
					DATE_FORMAT(a.defense_etime,'%h:%i%p') as defense_etime, a.venue, a.ref_session_type_id, 
					b.description as ref_session_type_desc
					FROM pg_calendar a
					LEFT JOIN ref_session_type b ON (b.id = a.ref_session_type_id)
					WHERE a.student_matrix_no = '$studentMatrixNoArray[$j]'
					AND a.thesis_id = '$thesisIdArray[$j]'
					AND a.status = 'A'
					AND a.id = '$pg_calendar_id[$j]'
					ORDER BY a.defense_date ASC";
					
					$result_sql3 = $dba->query($sql3); 
					$dba->next_record();
					$row_cnt_sql3 = mysql_num_rows($result_sql3);
				
					$recommendedIdArray = $dba->f('id');
					$defenseDateArray = $dba->f('defense_date1');
					$defenseSTimeArray = $dba->f('defense_stime');
					$defenseETimeArray = $dba->f('defense_etime');
					$venueArray = $dba->f('venue');
					$sessionTypeArray = $dba->f('ref_session_type_id');
					$sessionTypeDescArray = $dba->f('ref_session_type_desc');

			
					$sql13 = "SELECT confirm_status
					FROM pg_amendment_confirmation
					WHERE pg_supervisor_empid = '$user_id'
					AND pg_amendment_id = '$amendmentIdArray[$j]'";
					$db2 = $db;
					$result_sql13 = $db2->query($sql13); 
					$db2->next_record();
					$roleConStatus = $db2->f('confirm_status');
					
					$sql134 = "SELECT confirm_status
					FROM pg_amendment_confirmation
					WHERE pg_supervisor_empid = '$user_id'
					AND pg_amendment_id = '$amendmentIdArray[$j]'
					AND confirm_status = 'CON'";
					$db34 = $db;
					$result_sql13 = $db34->query($sql134); 
					$db2->next_record();
					$roleConStatus1 = $db34->f('confirm_status');
					
					
					?>
          <tr>
            <td align="center"><?=$no+1;?>.</td>
            <input type="hidden" name="roleStatusArray<?=$j?>" id="roleStatusArray<?=$j?>" value="<?=$roleStatusArray[$j]; ?>" />
            <input type="hidden" name="thesisIdArray<?=$j?>" id="thesisIdArray<?=$j?>" value="<?=$thesisIdArray[$j]; ?>" />
            <input type="hidden" name="studentMatrixNoArray<?=$j?>" id="studentMatrixNoArray<?=$j?>" value="<?=$studentMatrixNoArray[$j]; ?>" />
            <td align="left"><label>
              <?=$refNoArray[$j]?>
            </label></td>
            <td align="left"><label>
              <?=$thesisIdArray[$j]?>
            </label></td>
            <!--<td align="left"><label><?=$thesisTitleArray[$j]?></label></td>-->
            <td align="left"><label>
              <?=$studentNameArray[$j]?>
              <br />
              (
              <?=$studentMatrixNoArray[$j]?>
              )</label></td>
			  <td><?=$defenseDateArray?>, <?=$defenseSTimeArray?> to <?=$defenseETimeArray?>, <br /><?=$venueArray?></td>
            <td align="left"><?=$submitDateArray[$j]?></td>
            <? if($amendmentStatusArray[$j] == 'SUB') { $statusDesc = 'Submitted'?>
            <td width="8%" align="center"><?=$statusDesc?></td>
            <? } else if(empty($amendmentStatusArray[$j])) { $statusDesc = 'Not Submitted'?>
            <td width="8%" align="center"><?=$statusDesc?></td>
			<? } else if($amendmentStatusArray[$j] == 'SUB1') { $statusDesc = 'Submitted'?>
            <td width="8%" align="center"><?=$statusDesc?></td>
            <? } else if($amendmentStatusArray[$j] == 'REQ') { $statusDesc = 'Request Changes'?>
            <td width="8%" align="center"><?=$statusDesc?></td>
            <? } else if($amendmentStatusArray[$j] == 'COM') { $statusDesc = 'Completed'?>
            <td width="8%" align="center"><?=$statusDesc?></td>
            <? } else { ?> <td width="1%"></td> 
            <? } ?>
			
            <? if($realConfStatusArray[$j] == 'CON') { $statusCon = 'Feedback by Examiner Confirmed'?>
            <td width="2%" align="center"><?=$statusCon?></td>
            <? } else if($realConfStatusArray[$j] == 'CON1') { $statusCon = 'Amendment Confirmed'?>
            <td width="2%" align="center"><?=$statusCon?></td>
			  <? } else if($realConfStatusArray[$j] == 'CON2') { $statusCon = 'Amendment Confirmed by Faculty'?>
            <td width="2%" align="center"><?=$statusCon?></td>
            <? } else { $statusCon = 'Not Confirmed'; ?>
            <td width="2%" align="center"><?=$statusCon?> </td>
            <? } ?>
            <td width="6%" align="center">
			<? if(($amendmentStatusArray[$j] == 'SUB' || $amendmentStatusArray[$j] == 'SUB1') && ($roleConStatus == 'CHA' || empty($roleConStatus)) && empty($realConfStatusArray[$j]) && empty($roleConStatus1)) 				{ ?>
                <input type="button" name="btnUpdate" id="btnUpdate" value="Update" 
						onclick="javascript:document.location.href='review_amendment_staff.php?tid=<?=$thesisIdArray[$j]?>&pid=<?=$proposalIdArray[$j]?>&rid=<?=$refNoArray[$j]?>&mn=<?=$studentMatrixNoArray[$j]?>&role=<?=$roleStatusArray[$j]?>&mid=<?=$amendmentIdArray[$j]?>';"/>
                 <? } else if($realConfStatusArray[$j] == 'CON' && $amendmentStatusArray[$j] == 'SUB1') { ?>
                <input type="button" name="btnUpdate1" id="btnUpdate1" value="Update" 
						onclick="javascript:document.location.href='review_amendment_staff_after.php?tid=<?=$thesisIdArray[$j]?>&pid=<?=$proposalIdArray[$j]?>&rid=<?=$refNoArray[$j]?>&mn=<?=$studentMatrixNoArray[$j]?>&role=<?=$roleStatusArray[$j]?>&mid=<?=$amendmentIdArray[$j]?>';"/>
				<? } else if(empty($amendmentStatusArray[$j]) ) { ?>
                <? } else if((($realConfStatusArray[$j] == 'CON' ||$realConfStatusArray[$j] == 'CON1' || $realConfStatusArray[$j] == 'CON2') && $amendmentStatusArray[$j] == 'SUB1') || ($amendmentStatusArray[$j] == 'REQ1' || $amendmentStatusArray[$j] == 'SUB')) { ?>
                <input type="button" name="btnView" id="btnView" value="View" 
						onclick="javascript:document.location.href='view_amendment_staff_after.php?tid=<?=$thesisIdArray[$j]?>&pid=<?=$proposalIdArray[$j]?>&rid=<?=$refNoArray[$j]?>&mn=<?=$studentMatrixNoArray[$j]?>&role=<?=$roleStatusArray[$j]?>&mid=<?=$amendmentIdArray[$j]?>';"/>
                <? } else if ($amendmentStatusArray[$j] == 'REQ' || $realConfStatusArray[$j] == 'CON'){ ?>
				<input type="button" name="btnView" id="btnView" value="View" 
						onclick="javascript:document.location.href='view_amendment_staff.php?tid=<?=$thesisIdArray[$j]?>&pid=<?=$proposalIdArray[$j]?>&rid=<?=$refNoArray[$j]?>&mn=<?=$studentMatrixNoArray[$j]?>&role=<?=$roleStatusArray[$j]?>&mid=<?=$amendmentIdArray[$j]?>';"/>
				
				<? } else {
					//echo "amendmentStatusArray = $amendmentStatusArray[$j] <br>";
					//echo "roleConStatus = $roleConStatus <br>";
					//echo "realConfStatusArray = $realConfStatusArray[$j] <br>";
					//echo "roleConStatus1 = $roleConStatus1 <br>";
					//echo "amendmentStatusArray = $amendmentStatusArray[$j] <br>";
					
				
				}?>
            </td>
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
</body>
</html>





