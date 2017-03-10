<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Untitled Document</title>
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
	    <style type="text/css">
	    .style2 {font-size: 14px}
        </style>
		<script language="JavaScript" src="../../../lib/js/windowopen.js"></script>
	</head> 
	<body>  
<?
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: approve_proposal.php
//
// Created by: Zuraimi on 26-Dec-2014
// Modified by: Zuraimi on 30-Dec-2014 - Added code for thesis proposal approval
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

session_start();
$userid=$_SESSION['user_id'];
$studentMatrixNo=$_SESSION['studentMatrixNo'];

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
///////////////////////////////////////////////////////////////
// used for pagination
	$page = ($_GET['page'] == 0 ? 1 : $_GET['page']);
	$perpage = 2;
	$startpoint = ($page * $perpage) - $perpage;

$varParamSend="";

foreach($_REQUEST as $key => $value)
{
	if($key!="page")
		$varParamSend.="&$key=$value";
}

function runnum($column_name, $tblname) 
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

function runnum2($column_name, $tblname) 
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

if(isset($_POST['btnSubmit']) && ($_POST['btnSubmit'] <> "")) {
	$senateMtgDate=$_POST['senateMtgDate'];
	$endorsedStatus=$_POST['endorsedStatus'];
	$respondedByDate=$_POST['respondedByDate'];
	$myApprovalBox=$_POST['myApprovalBox'];
	$myPgThesisId=$_POST['myPgThesisId'];
	$myStudentMatrixNo=$_SESSION['myStudentMatrixNo'];
	$myStudentName=$_SESSION['myStudentName'];
	$myReportDate=$_SESSION['myReportDate'];
	$myProposalId=$_SESSION['myProposalId'];
	$remark = $_REQUEST['endorsedRemarks'];

	$msg=array();
	/*?><br/><?echo 'endorsedStatus ===========>'.$endorsedStatus;
	?><br/><?echo 'myPgThesisId'.$myPgThesisId;
	?><br/><?echo 'myStudentMatrixNo'.$myStudentMatrixNo;
	?><br/><?echo 'myStudentName'.$myStudentName;
	?><br/><?echo 'myReportDate'.$myReportDate;
	?><br/><?echo 'myProposalId'.$myProposalId;*/
	
	
	$curdatetime = date("Y-m-d H:i:s");
	
	$proposalApprovalId = "A".runnum('id','pg_proposal_approval');	
	
	if (sizeof($_POST['myApprovalBox'])>0) {
		
		$sql7 = "INSERT INTO pg_proposal_approval
		(id, endorsed_by,endorsed_date,endorsed_remarks,insert_by,insert_date,modify_by,modify_date)
		VALUES ('$proposalApprovalId', '$userid', STR_TO_DATE('$senateMtgDate','%d-%b-%Y'), '$endorsedRemarks', '$userid', 
		'$curdatetime', '$userid', '$curdatetime')";
		
		$result_sql7=$dbg->query($sql7);
		$dbg->next_record();
	
		$curdatetime = date("Y-m-d H:i:s");
		
		//$process = $dbg->query($sql5);
		
		//for ($i=0; $i<sizeof($_POST['myApprovalBox']); $i++) {
		while (list ($key,$val) = @each ($myApprovalBox)) {

			//------amalina add pg_proposal_area----------
			$sql6_1 = "SELECT pp.id, IFNULL(pp.report_date,'0000-00-00 00:00:00') as report_date, pp.thesis_title, pp.thesis_type, DATE_FORMAT(pp.report_date,'%d-%b-%Y') as report_date_email,
			pp.introduction, pp.objective, pp.description, pp.discussion_status,
			pp.verified_by, IFNULL(pp.verified_date,'0000-00-00 00:00:00') as verified_date, pp.verified_status, pp.verified_remarks, 
			pp.endorsed_by, IFNULL(pp.endorsed_date,'0000-00-00 00:00:00') as endorsed_date, pp.endorsed_remarks, pp.status, 
			pp.marked_by, IFNULL(pp.marked_date,'0000-00-00 00:00:00') as marked_date, pp.marked_status, 
			pp.faculty_remarks_by, IFNULL(pp.faculty_remarks_date,'0000-00-00 00:00:00') as faculty_remarks_date,
			pp.cancel_requested_by, IFNULL(pp.cancel_requested_date,'0000-00-00 00:00:00') as cancel_requested_date, pp.cancel_requested_remarks, 
			pp.cancel_approved_by, IFNULL(pp.cancel_approved_date,'0000-00-00 00:00:00') as cancel_approved_date, pp.cancel_approved_remarks, 		
			pp.insert_by as insert_by, IFNULL(pp.insert_date,'0000-00-00 00:00:00') as myinsert_date, 
			pp.modify_by, IFNULL(pp.modify_date,'0000-00-00 00:00:00') as modify_date, 
			pp.pg_thesis_id, pp.pg_proposal_approval_id, ppa.*, rtt.description as type
			FROM pg_proposal pp 
			LEFT JOIN pg_proposal_area ppa ON (ppa.pg_proposal_id=pp.id)
			LEFT JOIN ref_thesis_type rtt ON (rtt.id = pp.thesis_type)
			WHERE pp.id = '$myProposalId[$val]'";

			$result6_1 = $dbg->query($sql6_1);		
			$dbg->next_record();
			
			$id = $dbg->f('id'); 
			$reportDate = $dbg->f('report_date'); 			
			$reportDateEmail = $dbg->f('report_date_email'); 
			$thesisTitle = $dbg->f('thesis_title'); 
			$thesisType = $dbg->f('thesis_type'); 
			$introduction = $dbg->f('introduction'); 
			$objective = $dbg->f('objective'); 
			$description = $dbg->f('description'); 
			$discussionStatus = $dbg->f('discussion_status'); 
			$verifiedBy = $dbg->f('verified_by'); 
			$verifiedDate = $dbg->f('verified_date'); 
			$verifiedStatus = $dbg->f('verified_status'); 
			$verifiedRemarks = $dbg->f('verified_remarks');
			$endorsedBy = $dbg->f('endorsed_by'); 
			$endorsedDate = $dbg->f('endorsed_date'); 
			$endorsedRemarks = $dbg->f('endorsed_remarks'); 
			$status = $dbg->f('status'); 
			$markedBy = $dbg->f('marked_by'); 
			$markedDate = $dbg->f('marked_date'); 
			$markedStatus = $dbg->f('marked_status');		
			$facultyRemarksBy = $dbg->f('faculty_remarks_by'); 
			$facultyRemarksDate = $dbg->f('faculty_remarks_date'); 
			$cancelRequestedBy = $dbg->f('cancel_requested_by'); 
			$cancelRequestedDate = $dbg->f('cancel_requested_date');
			$cancelRequestedRemarks = $dbg->f('cancel_requested_remarks'); 
			$cancelApprovedBy = $dbg->f('cancel_approved_by'); 
			$cancelApprovedDate = $dbg->f('cancel_approved_date');
			$cancelApprovedRemarks = $dbg->f('cancel_approved_remarks'); 		
			$insertBy = $dbg->f('myinsert_by'); 
			$insertDate = $dbg->f('myinsert_date'); 
			$modifyBy = $dbg->f('modify_by'); 
			$modifyDate = $dbg->f('modify_date'); 
			$pgThesisId = $dbg->f('pg_thesis_id'); 
			$pgProposalApprovalId = $dbg->f('pg_proposal_approval_id');
			$jobArea1 = $dbg->f('job_id1_area');
			$jobArea2 = $dbg->f('job_id2_area');
			$jobArea3 = $dbg->f('job_id3_area');
			$jobArea4 = $dbg->f('job_id4_area');
			$jobArea5 = $dbg->f('job_id5_area');
			$jobArea6 = $dbg->f('job_id6_area');
			
			
			$proposal_id = "P".runnum('id','pg_proposal');	
		
			$sql6_2 = "INSERT INTO pg_proposal 
			(id, report_date, thesis_title, thesis_type, introduction, objective, description, discussion_status, 
			verified_by, verified_date, verified_status, verified_remarks, 
			endorsed_by, endorsed_date, endorsed_remarks, status, 
			marked_by, marked_date,marked_status,
			faculty_remarks_by, faculty_remarks_date,
			cancel_requested_by, cancel_requested_date, cancel_requested_remarks,
			cancel_approved_by, cancel_approved_date, cancel_approved_remarks,		
			insert_by, insert_date, modify_by, modify_date, pg_thesis_id, pg_proposal_approval_id)
			VALUES
			('$proposal_id', '$reportDate', '".mysql_real_escape_string($thesisTitle)."', '$thesisType', '$introduction', '$objective', '$description', '$discussionStatus', 
			'$verifiedBy', '$verifiedDate', '$verifiedStatus', '$verifiedRemarks', 
			'$userid', '$curdatetime', '$endorsedRemarks','$endorsedStatus', 
			'$markedBy', '$markedDate', '$markedStatus',
			'$facultyRemarksBy', '$facultyRemarksDate',
			'$cancelRequestedBy', '$cancelRequestedDate', '$cancelRequestedRemarks',
			'$cancelApprovedBy', '$cancelApprovedDate', '$cancelApprovedRemarks',
			'$insertBy', '$insertDate', '$userid', '$curdatetime', '$pgThesisId','$proposalApprovalId')";
			$result6_2 = $dbg->query($sql6_2); 	
			
			// --- Job Area Category (START) ---
			$selectArea = "SELECT * FROM pg_proposal_area
			WHERE pg_proposal_id = '$myProposalId[$val]' ";
			$db_klas2->query($selectArea); //echo $updateArea;
			
			$result_selectArea = $db_klas2->query($selectArea);	
			$row_cnt = mysql_num_rows($result_selectArea);	
			
			if ($row_cnt > 0) {

				$job_area_id = runnum2('id','pg_proposal_area');
				$insertArea = "INSERT INTO pg_proposal_area
				(id, pg_proposal_id, job_id1_area, job_id2_area, job_id3_area, job_id4_area, job_id5_area, job_id6_area, insert_date, insert_by,
				modified_date, modified_by)
				VALUES('$job_area_id', '$proposal_id', '$jobArea1', '$jobArea2', '$jobArea3', '$jobArea4', '$jobArea5', '$jobArea6',
				'$curdatetime', '".$_SESSION['user_id']."', '$curdatetime', '".$_SESSION['user_id']."')";
				$db_klas2->query($insertArea);
			}

			// --- Job Area Category (FINISH) ---			
			
			$sql6_3 = "select fu_cd
					FROM file_upload_proposal
					WHERE pg_proposal_id = '$myProposalId[$val]'
					ORDER BY fu_cd";
		
			$result6_3 = $dbg->query($sql6_3); 			

			$row_cnt = mysql_num_rows($result6_3);
			if ($row_cnt>0) {							
				while ($dbg->next_record()) {			
					$fuCdTmp=$dbg->f('fu_cd');
					
					$sql6_4 = "UPDATE file_upload_proposal 
								set pg_proposal_id = '$proposal_id'
							WHERE fu_cd = '$fuCdTmp'";
					
					$result6_4 = $dba->query($sql6_4);
					$dba->next_record();			
				};
						
			}
			
			$sql6_4 = "UPDATE pg_meeting_detail
						SET pg_proposal_id =  '$proposal_id'
						WHERE pg_proposal_id = '$myProposalId[$val]'";
			
			$result6_4 = $dbg->query($sql6_4); 	
			
			$sql6 = "UPDATE pg_proposal 		
				SET archived_status = 'ARC', archived_date = '$curdatetime', 
				modify_by = '$modifyBy', modify_date = '$modifyDate' 			
				WHERE id = '$myProposalId[$val]'";
		
			$dbg->query($sql6); 
			$dbg->next_record();		

			if (strcmp($endorsedStatus,"APP")==0 || strcmp($endorsedStatus,"APC")==0){		
				$sql7_1 = "UPDATE pg_thesis
					SET ref_thesis_status_id_proposal = '$endorsedStatus', ref_thesis_status_id_defense = 'INP',
					modify_by = '$userid', modify_date = '$curdatetime', status = 'INP'
					WHERE id = '$myPgThesisId[$val]'
					AND student_matrix_no = '$myStudentMatrixNo[$val]'";

				$dbg->query($sql7_1);
				$dbg->next_record();		
			}
			else {//endorsedStatus=="DIS"
				 $sql7_2 = "UPDATE pg_thesis
					SET ref_thesis_status_id_proposal = '$endorsedStatus', status = 'INC',
					modify_by = '$userid', modify_date = '$curdatetime'
					WHERE id = '$myPgThesisId[$val]'
					AND student_matrix_no = '$myStudentMatrixNo[$val]'";

					$dbg->query($sql7_2);
					$dbg->next_record();
			}
		
		//EMAIL NOTIFICATION START
			
		$selectfrom = "SELECT const_value
		FROM base_constant WHERE const_term = 'EMAIL_ADMIN'";
		$resultfrom = $db->query($selectfrom);
		$db->next_record();
		$fromadmin =$db->f('const_value');
				
		$sqlfaculty = "SELECT const_value
		FROM base_constant WHERE const_term = 'EMAIL_FACULTY'";
		$resultfaculty = $dbe->query($sqlfaculty);
		$dbe->next_record();
		$facultyemail =$dbe->f('const_value');
		
		$sqlfacid = "SELECT const_value FROM base_constant WHERE const_term = 'FACULTY_STAFF_ID'";
		$resultsqlfalid = $dbd->query($sqlfacid);
		$dbd->next_record();
		$facid = $dbd->f('const_value');
		
		
		$sqlname = "SELECT name
		FROM new_employee WHERE empid = '$facid'";
		$resultsqlname = $dbl->query($sqlname);
		$dbl->next_record();
		$faculty =$dbl->f('name');
		
		$selectto = "SELECT const_value
		FROM base_constant WHERE const_term = 'EMAIL_SENATE'";
		$resultto = $dbb->query($selectto);
		$dbb->next_record();
		$tosenate =$dbb->f('const_value');
		
		$sqltype = "SELECT description FROM ref_thesis_type WHERE id = '$thesisType'";
		$resultsqltype = $dbg->query($sqltype);
		$dbg->next_record();
		$typedesc =$dbg->f('description');
		
		$sqlname = "SELECT name FROM new_employee 
		WHERE empid = '$faculty'";
		$resultname = $dbc->query($sqlname);		
		$dbc->next_record();
		$supername = $dbc->f('name');
		$superemail = $dbc->f('email');

		
		$sqlsuper = "SELECT pg_employee_empid, ref_supervisor_type_id  FROM pg_supervisor
		WHERE pg_thesis_id = '$pgThesisId' AND ref_supervisor_type_id in ('SV','CS') ";
		$resultsuper = $db->query($sqlsuper);		
		while($db->next_record())
		{
			$superid = $db->f('pg_employee_empid');
			$typeid = $db->f('ref_supervisor_type_id');
			
			$sqlname = "SELECT name,email FROM new_employee 
			WHERE empid = '$superid'";
			$resultname = $dbc->query($sqlname);		
			$dbc->next_record();
			$supername = $dbc->f('name');
			$superemail = $dbc->f('email');

			
			$sqlsuper = "SELECT description FROM ref_supervisor_type
			WHERE id = '$typeid'";
			$resultsuper = $dbe->query($sqlsuper);		
			$dbe->next_record();
			$position = $dbe->f('description');
			
			$studentid = $_REQUEST['hidstudid'];
			$studname = "SELECT name,email FROM student WHERE matrix_no='$myStudentMatrixNo[$val]'";
			
			$resultstudname = $dbk->query($studname);
			$dbk->next_record();
			$studidname =$dbk->f('name');
			$studemail =$dbk->f('email');

			
			//$time = strtotime($insertDate);
			$myFormatForView = $reportDateEmail;
				
			$verified = $_REQUEST['endorsedStatus'];
			if($verified == 'APP')
			{
				$vstatus = "Approved";
				$FileType = Array();
				$selectattachment= "SELECT *
				FROM file_upload_proposal WHERE student_matrix_no = '$myStudentMatrixNo[$val]' 
				AND pg_proposal_id = '$myProposalId[$val]'"; 
				$resultattachment = $dbf->query($selectattachment);
			
				while($dbf->next_record())
				{
					$rowData = $dbf->rowdata();
					$FileName[] = $rowData['fu_document_filename'];
					$FileType[] = $rowData['fu_document_filetype'];
					$attachmentdata[] = $rowData['fu_document_filedata'];	
			 
				}

				$selectconstant= "SELECT const_value
				FROM base_constant WHERE const_term = 'EMAIL_SEN_TO_FAC'"; 
				$resultconstant = $dbj->query($selectconstant);
				$dbj->next_record();
				$constvalue =$dbj->f('const_value');
				if($constvalue = 'Y')
				{
					include("../../../app/application/email/email_assign_supervisor.php");
					include("../../../app/application/email/senate_approved.php");
					include("../../../app/application/email/senate_approved_student.php");
				}
				 
			}
			elseif($verified == 'APC')
			{
				$vstatus = "Approved with Changes";
				$FileType = Array();
				$selectattachment= "SELECT *
				FROM file_upload_proposal WHERE student_matrix_no = '$myStudentMatrixNo[$val]' 
				AND pg_proposal_id = '$myProposalId[$val]'"; 
				$resultattachment = $dbf->query($selectattachment);
				
				while($dbf->next_record())
				{
					$rowData = $dbf->rowdata();
					$FileName[] = $rowData['fu_document_filename'];
					$FileType[] = $rowData['fu_document_filetype'];
					$attachmentdata[] = $rowData['fu_document_filedata'];	
			 
				}					 
				$selectconstant= "SELECT const_value
				FROM base_constant WHERE const_term = 'EMAIL_SEN_TO_FAC'"; 
				$resultconstant = $dbj->query($selectconstant);
				$dbj->next_record();
				$constvalue =$dbj->f('const_value');
				if($constvalue = 'Y')
				{
					include("../../../app/application/email/email_assign_supervisor.php");
					include("../../../app/application/email/senate_app_changes.php");
					include("../../../app/application/email/senate_app_changes_student.php");
				}

			}
			elseif($verified == 'DIS')
			{
				$vstatus = "Disapproved";
				$FileType = Array();
				$selectconstant= "SELECT const_value
				FROM base_constant WHERE const_term = 'EMAIL_SEN_TO_FAC'"; 
				$resultconstant = $dbj->query($selectconstant);
				$dbj->next_record();
				$constvalue =$dbj->f('const_value');
				if($constvalue = 'Y')
				{
					include("../../../app/application/email/senate_disapprove.php");
					include("../../../app/application/email/senate_disapprove_student.php");
				}

			}
			else
			{
				echo "email not sent";
				echo $verified;
			}
		}
		//EMAIL NOTIFICATION END
		$msg[] = "<div class=\"success\"><span>The selected thesis proposal $myPgThesisId[$val] has been confirmed accordingly.</span></div>";
		} 	
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please select the thesis proposal before proceed with the approval status.</span></div>";
		
		
	}

}	
if(isset($_POST['btnSearch']) && ($_POST['btnSearch'] <> "")) {
	
	$senateMtgDateDropDown = $_POST['senateMtgDateDropDown'];
	$searchThesisDate = $_POST['searchThesisDate'];
	$searchThesisId = $_POST['searchThesisId'];
	$searchStudent = $_POST['searchStudent'];
	
	if ($senateMtgDateDropDown!="") 
	{
		$tmpSearchSenateMtgDate = " AND DATE_FORMAT(f.endorsed_date,'%d-%b-%Y') = '$senateMtgDateDropDown'";
	}
	else 
	{
		$tmpSearchSenateMtgDate="";
	}
	if ($searchThesisDate!="") 
	{
		$tmpSearchThesisDate = " AND DATE_FORMAT(a.report_date,'%d-%b-%Y') = '$searchThesisDate'";
	}
	else 
	{
		$tmpSearchThesisDate="";
	}
	if ($searchThesisId!="") 
	{
		$tmpSearchThesisId = " AND (a.pg_thesis_id = '$searchThesisId' OR a.thesis_title like '%$searchThesisId%')";
	}
	else 
	{
		$tmpSearchThesisId="";
	}
	if ($searchStudent!="") 
	{
		$tmpSearchStudent = " AND d.student_matrix_no = '$searchStudent'";
	}
	else 
	{
		$tmpSearchStudent="";
	}
		
	 $sql2 = "SELECT a.pg_thesis_id , a.id, DATE_FORMAT(a.report_date,'%d-%b-%Y') AS theReportDate, a.thesis_title, a.thesis_type, 
			b.description AS theThesisTypeDescription, a.introduction, a.objective, a.description, a.verified_status, a.verified_by, 
			DATE_FORMAT(a.verified_date,'%d-%b-%Y') AS verified_date, a.verified_remarks, a.status as endorsedStatus, a.discussion_status, d.supervisor_status, c.description AS endorsedDesc, c1.description AS verifiedDesc,d.student_matrix_no, 
			a.endorsed_remarks, DATE_FORMAT(f.endorsed_date,'%d-%b-%Y') AS endorsed_date
			FROM pg_proposal a
			LEFT JOIN ref_thesis_type b ON (b.id = a.thesis_type)
			LEFT JOIN ref_proposal_status c ON (c.id = a.status) 
			LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id) 
			LEFT JOIN ref_proposal_status c1 ON (c1.id = a.verified_status)
			LEFT JOIN pg_proposal_approval f ON (f.id = a.pg_proposal_approval_id)
			WHERE a.status in ('OPN','APP','DIS','APC')"
			.$tmpSearchThesisId." "
			.$tmpSearchStudent." "
			.$tmpSearchThesisDate." "
			.$tmpSearchSenateMtgDate." "."				
			AND a.verified_status in ('APP','AWC')
			AND d.status in ('INP','INC')
			AND a.archived_status is NULL
			ORDER BY a.verified_date DESC, f.endorsed_date, a.pg_thesis_id, a.id";		

		$result2 = $db->query($sql2); 
		$db->next_record();
		
		$pgThesisIdArray = Array();	
		$studentMatrixNoArray = Array();
		$studentNameArray = Array();						
		$proposalIdArray = Array();
		$reportDateArray = Array();
		$thesisTitleArray = Array();
		$descriptionArray = Array();
		$endorsedRemarksArray = Array();
		$endorsedStatusArray = Array();
		$endorsedDescArray = Array();
		$verifiedStatusArray = Array();
		$verifiedDescArray = Array();
		$supervisorStatusArray = Array();
		$verifiedDateArray = Array();
		$endorsedDateArray = Array();
		
		
		
		
		$no1=0;
		$no2=0;
		do {
			$pgThesisIdArray[$no1] = $db->f('pg_thesis_id');	
			$studentMatrixNoArray[$no1] = $db->f('student_matrix_no');
			$proposalIdArray[$no1] = $db->f('id');
			$reportDateArray[$no1] = $db->f('theReportDate');
			$thesisTitleArray[$no1] = $db->f('thesis_title');
			$descriptionArray[$no1] = $db->f('description');
			$endorsedRemarksArray[$no1] = $db->f('endorsed_remarks');
			$endorsedStatusArray[$no1] = $db->f('endorsedStatus');
			$endorsedDescArray[$no1] = $db->f('endorsedDesc');
			$verifiedStatusArray[$no1] = $db->f('verified_status');
			$verifiedDescArray[$no1] = $db->f('verifiedDesc');
			$supervisorStatusArray[$no1] = $db->f('supervisor_status');
			$verifiedDateArray[$no1] = $db->f('verified_date');
			$endorsedDateArray[$no1] = $db->f('endorsed_date');
			$no1++;
			
		} while ($db->next_record());
		
		$studentNameArray = Array();
		for ($i=0; $i<$no1; $i++){
			$sql11 = "SELECT name
				FROM student
				WHERE matrix_no = '$studentMatrixNoArray[$i]'
				AND name like '%$searchStudentName%'";

			$result11 = $dbc->query($sql11); 
			$dbc->next_record();
			if (mysql_num_rows($result11)>0) {
				$studentNameArray[$no2] = $dbc->f('name');
				$pgThesisIdArray[$no2] = $pgThesisIdArray[$i];	
				$studentMatrixNoArray[$no2] = $studentMatrixNoArray[$i];
				$proposalIdArray[$no2] = $proposalIdArray[$i];
				$reportDateArray[$no2] = $reportDateArray[$i];
				$thesisTitleArray[$no2] = $thesisTitleArray[$i];
				$descriptionArray[$no2] = $descriptionArray[$i];
				$endorsedRemarksArray[$no2] = $endorsedRemarksArray[$i];
				$endorsedStatusArray[$no2] = $endorsedStatusArray[$i];
				$endorsedDescArray[$no2] = $endorsedDescArray[$i];
				$verifiedStatusArray[$no2] = $verifiedStatusArray[$i];
				$verifiedDescArray[$no2] = $verifiedDescArray[$i];
				$supervisorStatusArray[$no2] = $supervisorStatusArray[$i];
				$verifiedDateArray[$no2] = $verifiedDateArray[$i];
				$endorsedDateArray[$no2] = $endorsedDateArray[$i];
				$no2++;
			}
		} 	 	
		$row_cnt = $no2;		
}
else 
{
	$sql2 = "SELECT a.pg_thesis_id , a.id, DATE_FORMAT(a.report_date,'%d-%b-%Y') AS theReportDate, a.thesis_title, a.thesis_type, 
			b.description AS theThesisTypeDescription, a.introduction, a.objective, a.description, a.verified_status, a.verified_by, 
			DATE_FORMAT(a.verified_date,'%d-%b-%Y') AS verified_date, a.verified_remarks, a.status as endorsedStatus, a.discussion_status, d.supervisor_status, c.description AS endorsedDesc, c1.description AS verifiedDesc,d.student_matrix_no, 
			a.endorsed_remarks, DATE_FORMAT(f.endorsed_date,'%d-%b-%Y') AS endorsed_date
			FROM pg_proposal a
			LEFT JOIN ref_thesis_type b ON (b.id = a.thesis_type)
			LEFT JOIN ref_proposal_status c ON (c.id = a.status) 
			LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id) 
			LEFT JOIN ref_proposal_status c1 ON (c1.id = a.verified_status)
			LEFT JOIN pg_proposal_approval f ON (f.id = a.pg_proposal_approval_id)			
			WHERE a.status in ('OPN','APP','DIS','APC')
			AND a.verified_status in ('APP','AWC')
			AND d.status in ('INP','INC')
			AND a.archived_status is NULL
			ORDER BY a.verified_date DESC, f.endorsed_date, a.pg_thesis_id, a.id";		

		$result2 = $db->query($sql2); 
		$db->next_record();
		
		$pgThesisIdArray = Array();	
		$studentMatrixNoArray = Array();
		$studentNameArray = Array();						
		$proposalIdArray = Array();
		$reportDateArray = Array();
		$thesisTitleArray = Array();
		$descriptionArray = Array();
		$endorsedRemarksArray = Array();
		$endorsedStatusArray = Array();
		$endorsedDescArray = Array();
		$verifiedStatusArray = Array();
		$verifiedDescArray = Array();
		$supervisorStatusArray = Array();
		$verifiedDateArray = Array();
		$endorsedDateArray = Array();
		
		
		
		
		$no1=0;
		$no2=0;
		do {
			$pgThesisIdArray[$no1] = $db->f('pg_thesis_id');	
			$studentMatrixNoArray[$no1] = $db->f('student_matrix_no');
			$proposalIdArray[$no1] = $db->f('id');
			$reportDateArray[$no1] = $db->f('theReportDate');
			$thesisTitleArray[$no1] = $db->f('thesis_title');
			$descriptionArray[$no1] = $db->f('description');
			$endorsedRemarksArray[$no1] = $db->f('endorsed_remarks');
			$endorsedStatusArray[$no1] = $db->f('endorsedStatus');
			$endorsedDescArray[$no1] = $db->f('endorsedDesc');
			$verifiedStatusArray[$no1] = $db->f('verified_status');
			$verifiedDescArray[$no1] = $db->f('verifiedDesc');
			$supervisorStatusArray[$no1] = $db->f('supervisor_status');
			$verifiedDateArray[$no1] = $db->f('verified_date');
			$endorsedDateArray[$no1] = $db->f('endorsed_date');
			$no1++;
			
		} while ($db->next_record());
		
		$studentNameArray = Array();
		for ($i=0; $i<$no1; $i++){
			$sql11 = "SELECT name
				FROM student
				WHERE matrix_no = '$studentMatrixNoArray[$i]'
				AND name like '%$searchStudentName%'";

			$result11 = $dbc->query($sql11); 
			$dbc->next_record();
			if (mysql_num_rows($result11)>0) {
				$studentNameArray[$no2] = $dbc->f('name');
				$pgThesisIdArray[$no2] = $pgThesisIdArray[$i];	
				$studentMatrixNoArray[$no2] = $studentMatrixNoArray[$i];
				$proposalIdArray[$no2] = $proposalIdArray[$i];
				$reportDateArray[$no2] = $reportDateArray[$i];
				$thesisTitleArray[$no2] = $thesisTitleArray[$i];
				$descriptionArray[$no2] = $descriptionArray[$i];
				$endorsedRemarksArray[$no2] = $endorsedRemarksArray[$i];
				$endorsedStatusArray[$no2] = $endorsedStatusArray[$i];
				$endorsedDescArray[$no2] = $endorsedDescArray[$i];
				$verifiedStatusArray[$no2] = $verifiedStatusArray[$i];
				$verifiedDescArray[$no2] = $verifiedDescArray[$i];
				$supervisorStatusArray[$no2] = $supervisorStatusArray[$i];
				$verifiedDateArray[$no2] = $verifiedDateArray[$i];
				$endorsedDateArray[$no2] = $endorsedDateArray[$i];
				$no2++;
			}
		} 	
		$row_cnt = $no2;
}

if(isset($_POST['btnSearchByName']) && ($_POST['btnSearchByName'] <> "")) {
	
	$searchStudentName = $_POST['searchStudentName'];
	
	$sql10 = "SELECT a.pg_thesis_id , a.id, DATE_FORMAT(a.report_date,'%d-%b-%Y') AS theReportDate, a.thesis_title, a.thesis_type, 
			b.description AS theThesisTypeDescription, a.introduction, a.objective, a.description, a.verified_status, a.verified_by, 
			DATE_FORMAT(a.verified_date,'%d-%b-%Y') AS verified_date, a.verified_remarks, a.status as endorsedStatus, a.discussion_status, d.supervisor_status, c.description AS endorsedDesc, c1.description AS verifiedDesc,d.student_matrix_no, 
			a.endorsed_remarks, DATE_FORMAT(f.endorsed_date,'%d-%b-%Y') AS endorsed_date
			FROM pg_proposal a
			LEFT JOIN ref_thesis_type b ON (b.id = a.thesis_type)
			LEFT JOIN ref_proposal_status c ON (c.id = a.status) 
			LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id) 
			LEFT JOIN ref_proposal_status c1 ON (c1.id = a.verified_status)
			LEFT JOIN pg_proposal_approval f ON (f.id = a.pg_proposal_approval_id)
			WHERE a.status in ('OPN','APP','DIS','APC')				
			AND a.verified_status in ('APP','AWC')
			AND d.status in ('INP','INC')
			AND a.archived_status is NULL
			ORDER BY a.verified_date DESC, f.endorsed_date, a.pg_thesis_id, a.id";		
		
		$result10 = $db->query($sql10); 
		$db->next_record();
		
		$pgThesisIdArray = Array();	
		$studentMatrixNoArray = Array();
		$studentNameArray = Array();						
		$proposalIdArray = Array();
		$reportDateArray = Array();
		$thesisTitleArray = Array();
		$descriptionArray = Array();
		$endorsedRemarksArray = Array();
		$endorsedStatusArray = Array();
		$endorsedDescArray = Array();
		$verifiedStatusArray = Array();
		$verifiedDescArray = Array();
		$supervisorStatusArray = Array();
		$verifiedDateArray = Array();
		$endorsedDateArray = Array();
		
		
		
		
		$no1=0;
		$no2=0;
		do {
			$pgThesisIdArray[$no1] = $db->f('pg_thesis_id');	
			$studentMatrixNoArray[$no1] = $db->f('student_matrix_no');
			$proposalIdArray[$no1] = $db->f('id');
			$reportDateArray[$no1] = $db->f('theReportDate');
			$thesisTitleArray[$no1] = $db->f('thesis_title');
			$descriptionArray[$no1] = $db->f('description');
			$endorsedRemarksArray[$no1] = $db->f('endorsed_remarks');
			$endorsedStatusArray[$no1] = $db->f('endorsedStatus');
			$endorsedDescArray[$no1] = $db->f('endorsedDesc');
			$verifiedStatusArray[$no1] = $db->f('verified_status');
			$verifiedDescArray[$no1] = $db->f('verifiedDesc');
			$supervisorStatusArray[$no1] = $db->f('supervisor_status');
			$verifiedDateArray[$no1] = $db->f('verified_date');
			$endorsedDateArray[$no1] = $db->f('endorsed_date');
			$no1++;
			
		} while ($db->next_record());
		
		$studentNameArray = Array();
		for ($i=0; $i<$no1; $i++){
			$sql11 = "SELECT name
				FROM student
				WHERE matrix_no = '$studentMatrixNoArray[$i]'
				AND name like '%$searchStudentName%'";

			$result11 = $dbc->query($sql11); 
			$dbc->next_record();
			if (mysql_num_rows($result11)>0) {
				$studentNameArray[$no2] = $dbc->f('name');
				$pgThesisIdArray[$no2] = $pgThesisIdArray[$i];	
				$studentMatrixNoArray[$no2] = $studentMatrixNoArray[$i];
				$proposalIdArray[$no2] = $proposalIdArray[$i];
				$reportDateArray[$no2] = $reportDateArray[$i];
				$thesisTitleArray[$no2] = $thesisTitleArray[$i];
				$descriptionArray[$no2] = $descriptionArray[$i];
				$endorsedRemarksArray[$no2] = $endorsedRemarksArray[$i];
				$endorsedStatusArray[$no2] = $endorsedStatusArray[$i];
				$endorsedDescArray[$no2] = $endorsedDescArray[$i];
				$verifiedStatusArray[$no2] = $verifiedStatusArray[$i];
				$verifiedDescArray[$no2] = $verifiedDescArray[$i];
				$supervisorStatusArray[$no2] = $supervisorStatusArray[$i];
				$verifiedDateArray[$no2] = $verifiedDateArray[$i];
				$endorsedDateArray[$no2] = $endorsedDateArray[$i];
				$no2++;
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
		<script language="JavaScript" src="../js/windowopen.js"></script>	
		
<script>
<?=$msg;?>
function open_win(what_link,the_x,the_y,toolbar,addressbar,directories,statusbar,menubar,scrollbar,resize,history,pos,wname)
{ 
  var the_url = what_link;
  the_x -= 0;
  the_y -= 0;
  var how_wide = screen.availWidth;
  var how_high = screen.availHeight;

  if(toolbar == "0"){var the_toolbar = "no";}else{var the_toolbar = "yes";}
  if(addressbar == "0"){var the_addressbar = "no";}else{var the_addressbar = "yes";}
  if(directories == "0"){var the_directories = "no";}else{var the_directories = "yes";}
  if(statusbar == "0"){var the_statusbar = "no";}else{var the_statusbar = "yes";}
  if(menubar == "0"){var the_menubar = "no";}else{var the_menubar = "yes";}
  if(scrollbar == "0"){var the_scrollbars = "no";}else{var the_scrollbars = "yes";}
  if(resize == "0"){var the_do_resize =  "no";}else{var the_do_resize = "yes";}
  if(history == "0"){var the_copy_history = "no";}else{var the_copy_history = "yes";}
  if(pos == 1){top_pos=0;left_pos=0;}
  if(pos == 2){top_pos = 0;left_pos = (how_wide/2) -  (the_x/2);}
  if(pos == 3){top_pos = 0;left_pos = how_wide - the_x;}
  if(pos == 4){top_pos = (how_high/2) -  (the_y/2);left_pos = 0;}
  if(pos == 5){top_pos = (how_high/2) -  (the_y/2);left_pos = (how_wide/2) -  (the_x/2);}
  if(pos == 6){top_pos = (how_high/2) -  (the_y/2);left_pos = how_wide - the_x;}
  if(pos == 7){top_pos = how_high - the_y;left_pos = 0;}
  if(pos == 8){top_pos = how_high - the_y;left_pos = (how_wide/2) -  (the_x/2);}
  if(pos == 9){top_pos = how_high - the_y;left_pos = how_wide - the_x;}
  if (window.outerWidth )
  {
    var option = "toolbar="+the_toolbar+",location="+the_addressbar+",directories="+the_directories+",status="+the_statusbar+",menubar="+the_menubar+",scrollbars="+the_scrollbars+",resizable="+the_do_resize+",outerWidth="+the_x+",outerHeight="+the_y+",copyhistory="+the_copy_history+",left="+left_pos+",top="+top_pos;
    wname=window.open(the_url, wname, option);
    wname.focus();
  }
  else
  {
    var option = "toolbar="+the_toolbar+",location="+the_addressbar+",directories="+the_directories+",status="+the_statusbar+",menubar="+the_menubar+",scrollbars="+the_scrollbars+",resizable="+the_do_resize+",Width="+the_x+",Height="+the_y+",copyhistory="+the_copy_history+",left="+left_pos+",top="+top_pos;
    if (!wname.closed && wname.location)
    {
      wname.location.href=the_url;
    }
    else
    {
      wname=window.open(the_url, wname, option);
      //wname.resizeTo(the_x,the_y);
      wname.focus();
      wname.location.href=the_url;
    }
  }
} 
</script>

<script type="text/javascript" src="tinymce/jscripts/tiny_mce/tiny_mce.js"></script><script type="text/javascript">
tinyMCE.init({
        // General options
        mode : "textareas",
        theme : "advanced",
        plugins : "autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

        // Theme options
        theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
        theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        theme_advanced_resizing : true,

        // Skin options
        skin : "o2k7",
        skin_variant : "silver",

        // Example content CSS (should be your site CSS)
        content_css : "css/example.css",

        // Drop lists for link/image/media/template dialogs
        template_external_list_url : "js/template_list.js",
        external_link_list_url : "js/link_list.js",
        external_image_list_url : "js/image_list.js",
        media_external_list_url : "js/media_list.js",

        // Replace values for the template plugin
        template_replace_values : {
                username : "Some User",
                staffid : "991234"
        }
});
</script>

<script>
$(function() {
	$( "#datepickerFirst" ).datepicker({
		changeMonth: true,
		changeYear: true,
		yearRange: '-100:+0'
		});
});
</script>	
<SCRIPT LANGUAGE="JavaScript">

function respConfirm() {
    var confirmSubmit = confirm("Click OK if you confirm to submit else click Cancel to stay on the same page.");
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
	
		<form id="form1" name="form1" method="post" action="<? echo $_SERVER["PHP_SELF"]; ?>" enctype="multipart/form-data">	
		<input type="hidden" name="empid" id="empid" value="<?php echo $user_id; ?>">			
				
		<?  
		
		//echo "XXX===>".$row_cnt = mysql_num_rows($result2);
		?>	
			<fieldset>
			<legend><strong>List of Thesis Proposal for Senate Review and Approval</strong></legend><br/>		
			<table>
					<tr>							
						<td><strong>Please enter searching criteria below</strong></td>
					</tr>
					<tr>
						<td><strong>Notes: </strong>(by default it will display,<br/>
						1. Current proposal in which it status has been confirmed by Senate and<br/>
						2. Proposal in which the status is still pending for Senate confirmation)</td>
					</tr>
			</table>
			<br/>
			<table>				
				<tr>
					<td>Senate Meeting Date</td>
					<td>:</td>
					<td><select name = "senateMtgDateDropDown" >
					<option value="" selected="selected"></option>						
				
				<?
				
				$sql3 = "SELECT DISTINCT DATE_FORMAT(endorsed_date,'%d-%b-%Y') AS endorsed_date
				FROM pg_proposal_approval
				ORDER BY endorsed_date DESC";
				
				$dbf->query($sql3); 
				$dbf->next_record();
				do {
					$senate_mtg_date=$dbf->f('endorsed_date');	
					if (strcmp($senateMtgDateDropDown,$senate_mtg_date)!=true) {
						?>
							<option value="<?=$senate_mtg_date?>" selected="selected"><?=$senate_mtg_date?></option>
						<?
					}
					else {?>
							<option value="<?=$senate_mtg_date?>"><?=$senate_mtg_date?></option>
						<?}
				}while ($dbf->next_record());
				?>
				</select></td>
				</tr>
				<tr>
					<?$jscript1 = "";?>
					<td>Thesis Date</td>
					<td>:</td>
					<td><input type="text" name="searchThesisDate" size="15" id="searchThesisDate" value="<?=$searchThesisDate;?>"/></td>
					<?	$jscript1 .= "\n" . '$( "#searchThesisDate" ).datepicker({
														changeMonth: true,
														changeYear: true,
														yearRange: \'-100:+0\',
														dateFormat: \'dd-M-yy\'
													});';
							 
					?>
				</tr>
				<tr>
					<?$searchRequestDate = date("d-M-Y");?>
					<td>Thesis ID / Thesis Title</td>
					<td>:</td>
					<td><input type="text" name="searchThesisId" size="50" id="searchThesisId" value="<?=$searchThesisId;?>"/></td>
				</tr>
				<tr>
					<td>Matrix No </td>
					<td>:</td>
					<td><input type="text" name="searchStudent" size="30" id="searchStudent" value="<?=$searchStudent;?>"/></td>
					<td><input type="submit" name="btnSearch" value="Search" />
				</tr>
				<tr>
						<td>Student Name</td>
						<td>:</td>
						<td><input type="text" name="searchStudentName" size="30" id="searchStudentName" value="<?=$searchStudentName;?>"/></td>
						<td><input type="submit" name="btnSearchByName" value="Search by Name Only" /><span style="color:#FF0000"> Note:</span> If no parameters are provided, it will search all.</td>
				</tr>
			</table>
			<br/>
			
			<table>
					<tr>							
						<td><strong>Searching Results:-</strong></td>
					</tr>
			</table>
			<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1">						
				<tr>						
					<td width="30" align="center"><strong>Tick</strong></td>	
					<td width="25" align="center"><strong>No.</strong></td>					
					<td width="100"><strong>Senate Status</strong></td>
					<td width="100"><strong>Thesis Date</strong></td>
					<td width="131"><strong>Thesis/Project ID</strong></td>
					<td width="208"><strong>Thesis/Project Title</strong></td>
					<td width="102"><strong>Student Name</strong></td>
					<td width="102"><strong>Attachment by Student</strong></td>					
					<td width="151"><strong>Supervisor </strong></td>
					
				</tr>
				<?if ($row_cnt>0) {?>
				<?
				$no=0;
				//while($db->next_record()) {	
				for ($i=0; $i<$no2; $i++){				
				
														
														
				?>
					<tr>
						<? if ($endorsedStatusArray[$i]=='APP' || $endorsedStatusArray[$i]=='APC' || $endorsedStatusArray[$i]=='DIS'){
							?><td align="center"><input name="myApprovalBox[]" type="checkbox" value="<?=$no;?>" disabled="disabled" /></td><?							
						}
							else {
								?><td align="center"><input name="myApprovalBox[]" type="checkbox" value="<?=$no;?>" /></td><?
						}
						?>		
						
						<input type="hidden" name="myProposalId[]" size="12" id="proposalId" value="<?=$proposalIdArray[$i];?>"/>
						<? $myProposalId[$no]=$proposalIdArray[$i];?>
						
						<input type="hidden" name="myPgThesisId[]" size="12" id="pgThesisId" value="<?=$pgThesisIdArray[$i];?>"/>
						<? $myPgThesisId[$no]=$pgThesisIdArray[$i];?>
						
						<td align="center"><?=$no+1;?>.	
						<?
						$sql3_1 = "SELECT const_value
						FROM base_constant
						WHERE const_term = 'NEW_PROPOSAL_SENATE'";

						$result3_1 = $dbb->query($sql3_1);
						$dbb->next_record();
						$parameterValue=$dbb->f('const_value');
						
						$newVerifiedDate = date('d-M-Y', strtotime($verifiedDateArray[$i]. ' + '.$parameterValue.' days'));		
						$currentDate = new DateTime();			
						$tmpNewVerifiedDate = new DateTime($newVerifiedDate);
						
						$myTmpNewVerifiedDate = $tmpNewVerifiedDate->format('d-M-Y');
						$myCurrentDate = $currentDate->format('d-M-Y');
						
						$myTmpNewVerifiedDate1 = new DateTime($myTmpNewVerifiedDate);
						$myCurrentDate1 = new DateTime($myCurrentDate);
								
						//$myTmpNewVerifiedDate1 = date_create($myTmpNewVerifiedDate);
						//$myCurrentDate1 = date_create($myCurrentDate);
						//echo date_format($myTmpNewReportDate1,'d-M-Y');
						//echo date_format($myCurrentDate1,'d-M-Y');
						
						//if ($tmpNewVerifiedDate->format('d-M-Y') < $currentDate->format('d-M-Y')) {
						if ($myCurrentDate1 <= $myTmpNewVerifiedDate1) {
						?>
							<img src="../images/new.jpg" width="50" height="40" style="border:0px;" title="Proposal is considered new if it is submitted within <?=$parameterValue?> day(s)">
						<?}?></td>

						<td><label name="myEndorsedDesc[]" id="endorsedDesc" ></label><?=$endorsedDescArray[$i];?><br/><?=$endorsedDateArray[$i];?></td>
						<td><label name="reportDate[]" cols="45" id="reportDate"><?=$reportDateArray[$i]?></label></td>
						<td><a href="senate_approval_outline.php?thesisId=<? echo $pgThesisIdArray[$i];?>&proposalId=<? echo $proposalIdArray[$i];?>" name="myPgThesisId[]" value="<?=$pgThesisIdArray[$i]?>" title="Outline of Proposed Case Study by the Student - Read more..."><?=$pgThesisIdArray[$i];?><br/>
						
						
						<? if ($endorsedRemarksArray[$i] == null || $endorsedRemarksArray[$i] ==""){?>
						
							<img src="../images/red_edit.jpg" width="20" height="19" style="border:0px;" title="Senate Remark is not yet provided" >Enter Remarks</a></td>	
						<? }
						else {
						?>
							<img src="../images/edit.jpg" width="20" height="19" style="border:0px;" title="Senate Remark is provided" >Read Remarks</a></td>	
						<?
						}?>
						
												
						<td><label name="myThesisTitle[]" id="thesisTitle" ></label><?=$thesisTitleArray[$i]; ?></td>
						
						<td><label name="myStudentName[]" size="30" id="studentName" ></label><?=$studentNameArray[$i];?>
						(<?=$studentMatrixNoArray[$i];?>)</td>
						<?$myStudentMatrixNo[$no]=$studentMatrixNoArray[$i];?>
						
						<?php
									$sqlUpload="SELECT * FROM file_upload_proposal 
									WHERE pg_proposal_id='$proposalIdArray[$i]' 
									AND attachment_level='S' ";			

									$result = $db_klas2->query($sqlUpload); //echo $sql;
									$row_cnt = mysql_num_rows($result);
									$attachmentNo1=1;
									if ($row_cnt>0)
									{
										?><td align="left"><?
										while($row = mysql_fetch_array($result)) 					
										{ 
											?>
													<a href="download.php?fc=<?=$row["fu_cd"];?>&al=S" title="File Description: <?=$row["fu_document_filedesc"];?>">Attachment <?=$attachmentNo1++;?>: <br/><img src="../images/download.png" width="20" height="19" style="border:0px;" title="Download <?=$row["fu_document_filename"];?>"></a><br/>
													
															
												
										<?}
										?></td><?
									}
									else {
										?><td>No attachment</td><?
									}
								?>
											
						<td>
						<?	$sqlSupervisor="SELECT ps.id, ps.ref_supervisor_type_id, ps.pg_employee_empid, rst.description  
									FROM  pg_supervisor ps 
									LEFT JOIN ref_supervisor_type rst ON (rst.id=ps.ref_supervisor_type_id)
									WHERE ps.pg_student_matrix_no='$studentMatrixNoArray[$i]'
									AND ps.ref_supervisor_type_id in ('SV','CS','EI','EE','XS')
									AND ps.pg_thesis_id = '$pgThesisIdArray[$i]'
									AND ps.status = 'A'";
							
							$result_sqlSupervisor = $db_klas2->query($sqlSupervisor);	
							$row_cnt = mysql_num_rows($result_sqlSupervisor);							
							$no1=1;
							if ($row_cnt>0) {
								
								while($row = mysql_fetch_array($result_sqlSupervisor)) 
								{ 
									$employeeId = $row["pg_employee_empid"];
									
									$sql1="SELECT name
									FROM new_employee
									WHERE empid = '$employeeId'";
									
									$dbc->query($sql1);
									$row_personal=$dbc->fetchArray();
									//$name=$row_personal['name'];
									?>
									<?=$no1?>) <?=$row_personal["name"];?> (<?=$employeeId;?>)<br/>								
									<? $no1++;
								} ?>
								<? if($endorsedStatusArray[$i]=='OPN' || $endorsedStatusArray[$i]=='APC' || $endorsedStatusArray[$i]=='APP') 
								{ ?>
									<a href="../supervisor/edit_supervisor_senate.php?mn=<?=$studentMatrixNoArray[$i];?>&tid=<?=$pgThesisIdArray[$i];?>&sname=<?php echo $studentNameArray[$i]?>" name="mySupervisor[]">Change <img src="../images/person_reassigned.jpg" width="20" height="19" style="border:0px;" title="Supervisor details" ></a>
									<br/><a href="../supervisor/view_supervisor_senate.php?sname=<?php echo $studentNameArray[$i]?>&mn=<? echo $studentMatrixNoArray[$i]?>&tid=<?=$pgThesisIdArray[$i];?>">View</a> 
								 <? 
								 } 
								 else //DIS
								 {?>
									<a><img src="../images/person_reassigned.jpg" width="20" height="19" style="border:0px;" title="Supervisor details" ></a>
									<br/><a href="../supervisor/view_supervisor_senate.php?sname=<?php echo $studentNameArray[$i]?>&mn=<? echo $studentMatrixNoArray[$i]?>&tid=<?=$pgThesisIdArray[$i];?>">View</a>
								<?}
							}
							else 
							{
								?>
								<a href="../supervisor/edit_supervisor_senate.php?mn=<?=$studentMatrixNoArray[$i];?>&tid=<?=$pgThesisIdArray[$i];?>&sname=<?php echo $studentNameArray[$i]?>" name="mySupervisor[]">Assign <img src="../images/person_assigned.jpg" width="20" height="19" style="border:0px;" title="Supervisor details" ></a>
								<?
							}
						?>
											
						
						</td>				  
					</tr>
				<?
				$no=$no+1;
				};	
				?>
				<? //$_SESSION['myPgThesisId'] = $myPgThesisId;?>				
				<? $_SESSION['myStudentMatrixNo'] = $myStudentMatrixNo;?>
			</table>				
			<br />		
			<br/>
			</fieldset>
			<br />
			<fieldset>
			<legend><strong>Approval Confirmation by Senate</strong></legend><br/>				
			<table>				 
					<tr>
						<?$jscript = "";?>
						<?$senateCurrentDate = date("d-M-Y");?>
						<td>Senate Meeting Date</td>
						<td>:</td>
						<td><input type="text" name="senateMtgDate" size="15" id="senateMtgDate" value="<?=$senateCurrentDate;?>"/></td>
						<?	$jscript .= "\n" . '$( "#senateMtgDate" ).datepicker({
														changeMonth: true,
														changeYear: true,
														yearRange: \'-100:+0\',
														dateFormat: \'dd-M-yy\'
													});';
							 
						?>
					</tr>
					<tr>
						<td>Approval Status</td>
						<td>:</td>
						<td>
							<input type="radio" name="endorsedStatus" value="APP" checked="checked"/>Approved
							<input type="radio" name="endorsedStatus" value="APC" />Approved with Changes
							<input type="radio" name="endorsedStatus" value="DIS" />Disapproved	  
						</td>
					</tr>
					<tr>
					<td>Overall Remarks by Senate</td>				
					<td></td>
					<td><textarea name="endorsedRemarks" class="ckeditor" cols="50" id="endorsedRemarks"></textarea></td>
				</tr>				
			</table>
			</fieldset>
			<? $_SESSION['myApprovalBox'] = $myApprovalBox;?>
				<? $_POST['myPgThesisId'] = $myPgThesisId;?>	
				<? $_SESSION['myProposalId'] = $myProposalId;?>		
			<table>
				<tr>	
					<td><input type="submit" name="btnPrintThesis" id="btnPrintProposal" value="Print Proposal List" /></td>
					<td><input type="submit" name="btnSubmit" id="btnSubmit" value="Submit" onClick="return respConfirm()" /><span style="color:#FF0000"> Note:</span> Ensure the proposal above has been selected before click Submit.</td>
				</tr>
			</table>
			<br/>
		<?}
		else {
			?>
			<table>
				<tr>
					<td>
						<p>No record found!</p>
					</td>
				</tr>
			</table>
			<table>					
				<tr>				
					<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../thesis/senate_approval.php';" /></td>
				</tr>
			</table>
			<?
		}?>

	  </form>
	  <script>
		<?=$jscript;?>
		<?=$jscript1;?>			
	</script>
	</body>
</html>




