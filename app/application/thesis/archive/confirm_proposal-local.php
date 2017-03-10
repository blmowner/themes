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
<?php
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
$curdatetime = date("Y-m-d H:i:s");

/*ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);*/

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
?>

<?
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

if(isset($_POST['btnMark']) && ($_POST['btnMark'] <> ""))
	{			
		
		
		$myApprovalBox=$_POST['myApprovalBox'];

		$curdatetime = date("Y-m-d H:i:s");
		//$meeting_detail_id = runnum('id','pg_meeting_detail');
			
		while (list ($key,$val) = @each ($myApprovalBox)) 
		{
			/*echo "myApprovalBox key ".$key." "."val ".$val."<br/>";
			echo "myPgThesisId [".$val."] "."val ".$myPgThesisId[$val]."<br/>";
			echo "myStudentMatrixNo [".$val."] "."val ".$myStudentMatrixNo[$val]."<br/>";
			echo "myStudentName [".$val."] "."val ".$myStudentName[$val]."<br/>";
			echo "myReportDate [".$val."] "."val ".$myReportDate[$val]."<br/>";
			echo "myProposalId [".$val."] "."val ".$myProposalId[$val]."<br/>";*/
			
			$sql7 = "UPDATE pg_proposal
						SET marked_status = 'MAR', marked_by = '$user_id', marked_date = '$curdatetime',
						modify_by = '$user_id', modify_date = '$curdatetime'
						WHERE id = '$myProposalId[$val]'
						AND pg_thesis_id = '$myPgThesisId[$val]'
						AND archived_status is null";
						
			//echo "sql7 ".$sql7;
			$db->query($sql7);
		}
		
	}
			
if(isset($_POST['btnUnmark']) && ($_POST['btnUnmark'] <> ""))
	{			
		
		
		$myApprovalBox=$_POST['myApprovalBox'];

		$curdatetime = date("Y-m-d H:i:s");
		//$meeting_detail_id = runnum('id','pg_meeting_detail');
		
		$sql7 = "UPDATE pg_proposal
						SET marked_status = 'UNM', marked_by = '$user_id', marked_date = '$curdatetime',
						modify_by = '$user_id', modify_date = '$curdatetime'
						WHERE marked_status='MAR'
						AND archived_status is null";
						
		$db->query($sql7);
		
}	
if(isset($_POST['btnSubmit']) && ($_POST['btnSubmit'] <> "")) {
	
	$msg = array();
	$myVerifiedStatus=$_POST['myVerifiedStatus'];
	$myVerifiedRemarks=$_POST['myVerifiedRemarks'];
	$supervisorStatusArray=$_POST['supervisorStatusArray'];
		
	$myApprovalBox=$_POST['myApprovalBox'];
	$myPgThesisId=$_SESSION['myPgThesisId'];
	$myStudentName=$_REQUEST['studentName'];
	$myReportDate=$_SESSION['myReportDate'];
	$myStudentId = $_REQUEST['studentId'];
	$myProposalId = $_REQUEST['myProposalId'];
	
	if (sizeof($_POST['myApprovalBox'])>0) {

		while (list ($key,$val) = @each ($myApprovalBox)) 
		{

			
			$sql_supervisor = " SELECT a.id, a.pg_employee_empid
						FROM pg_supervisor a
						LEFT JOIN ref_supervisor_type e ON (e.id = a.ref_supervisor_type_id)
						WHERE a.pg_student_matrix_no = '$myStudentMatrixNo[$val]' 
						AND a.ref_supervisor_type_id in ('SV','CS','XS')
						AND a.acceptance_status IS NULL
						AND a.pg_thesis_id = '$myPgThesisId[$val]'
						AND a.status = 'A' 
						ORDER BY e.seq, a.pg_employee_empid";

						
			$result_sql_supervisor = $db_klas2->query($sql_supervisor); //echo $sql;
			$db_klas2->next_record();
			
			$row_cnt_supervisor = mysql_num_rows($result_sql_supervisor);
			
			if ($row_cnt_supervisor > 0) {
				$confirmProposal = 'Y';				
			}
			else {
				if ($myVerifiedStatus =='REQ') {					
					$confirmProposal = 'Y';
				}
				else if ($myVerifiedStatus =='DIS') {
					$confirmProposal = 'Y';
				}
				else {
					$confirmProposal = 'N';
				}
			} 
				
			if ($confirmProposal == 'Y'){				
				/*echo "myApprovalBox key ".$key." "."val ".$val."<br/>";
				echo "myPgThesisId [".$val."] "."val ".$myPgThesisId[$val]."<br/>";
				echo "myStudentMatrixNo [".$val."] "."val ".$myStudentMatrixNo[$val]."<br/>";
				echo "myStudentName [".$val."] "."val ".$myStudentName[$val]."<br/>";
				echo "myReportDate [".$val."] "."val ".$myReportDate[$val]."<br/>";
				echo "myProposalId [".$val."] "."val ".$myProposalId[$val]."<br/>";
				echo "myVerifiedStatus [".$val."] "."val ".$myVerifiedStatus."<br/>";*/

				/*$sql5 = "UPDATE pg_supervisor
						SET assigned_by = '$userid', assigned_date = '$curdatetime',
						modify_by = '$userid', modify_date = '$curdatetime'
						WHERE pg_student_matrix_no= '$myStudentMatrixNo[$val]'
						AND STATUS = 'A'";
						
				$result5 = $dbd->query($sql5); 
				//echo "sql5 ".$sql5;
				//var_dump($dbd);
				$dbd->next_record();*/
				
				$sql6_1 = "SELECT pp.id, pp.report_date, pp.thesis_title, pp.thesis_type, pp.introduction, pp.objective, 
						pp.description,pp.discussion_status,pp.verified_by, 
						IFNULL(pp.verified_date,'0000-00-00 00:00:00') as verified_date, 
						DATE_FORMAT(pp.report_date,'%d-%b-%Y') as report_date_email, pp.verified_status, pp.verified_remarks, pp.endorsed_by, 
						IFNULL(pp.endorsed_date,'0000-00-00 00:00:00') as endorsed_date, pp.endorsed_remarks,pp.status, 
						pp.marked_by, pp.marked_status, IFNULL(pp.marked_date,'0000-00-00 00:00:00') as marked_date,
						pp.insert_by, IFNULL(pp.insert_date,'0000-00-00 00:00:00') as insert_date, 
						pp.modify_by, IFNULL(pp.modify_date,'0000-00-00 00:00:00') as modify_date, 
						pp.pg_thesis_id, pg_proposal_approval_id,ppa.id, ppa.pg_proposal_id, ppa.job_id1_area, ppa.job_id2_area,ppa.job_id3_area,ppa.job_id4_area, 
						ppa.job_id5_area, ppa.job_id6_area, rtt.description as type   
						FROM pg_proposal pp
						LEFT JOIN pg_proposal_area ppa ON (ppa.pg_proposal_id = pp.id)
						LEFT JOIN ref_thesis_type rtt ON (rtt.id = pp.thesis_type)
						WHERE pp.id = '$myProposalId[$val]'";

				$result6_1 = $dbg->query($sql6_1); 			
				//echo "sql6_1 ".$sql6_1;
				//exit();
				//var_dump($dbg);
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
				$markedBy = $dbg->f('marked_by'); 
				$markedDate = $dbg->f('marked_date'); 
				$markedStatus = $dbg->f('marked_status');
				$status = $dbg->f('status'); 
				$insertBy = $dbg->f('insert_by'); 
				$insertDate = $dbg->f('insert_date'); 
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
				$type = $dbg->f('type');
				
				
				
				$proposal_id = "P".runnum('id','pg_proposal');	
			
				$sql6_2 = "INSERT INTO pg_proposal 
						(id, report_date, thesis_title, thesis_type, introduction, objective, description, discussion_status,
						verified_by, verified_date, verified_status, verified_remarks, endorsed_by, endorsed_date, endorsed_remarks, status,
						marked_by, marked_date, marked_status, insert_by, insert_date, modify_by, modify_date, 
						pg_thesis_id)
						VALUES
						('$proposal_id', '$reportDate', '".mysql_real_escape_string($thesisTitle)."', '$thesisType', '".mysql_real_escape_string($introduction)."',
						'".mysql_real_escape_string($objective)."', '".mysql_real_escape_string($description)."', 
						'$discussionStatus', '$userid', '$curdatetime', '$myVerifiedStatus', '$verifiedRemarks', 
						'$endorsedBy', '$endorsedDate', '$endorsedRemarks','$status', '$markedBy','$markedDate','$markedStatus',
						'$insertBy', '$insertDate', '$userid', '$curdatetime', '$pgThesisId')";
					//'$introduction', '$objective', '$description'
				$result6_2 = $dbg->query($sql6_2); 			
				//echo "sql6_2 ".$sql6_2;
				//var_dump($dbg);
				$dbg->next_record();
				//$dbg->free();
				//exit();	
				
				
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
						SET archived_status = 'ARC', archived_date = '$curdatetime' 			
						WHERE id = '$myProposalId[$val]'";
				
				$result6 = $dbg->query($sql6); 			
				
				
				//ADDED EMAIL FEATURE START
				$check = $_REQUEST['myApprovalBox'];
				$verified = $_REQUEST['myVerifiedStatus'];
				
				$selectfrom = "SELECT const_value
				FROM base_constant WHERE const_term = 'EMAIL_ADMIN'";
				$resultfrom = $db->query($selectfrom);
				$db->next_record();
				$fromadmin =$db->f('const_value');
								
				$selectto = "SELECT const_value
				FROM base_constant WHERE const_term = 'EMAIL_FACULTY'";
				$resultto = $dbf->query($selectto);
				$dbf->next_record();
				$fromfaculty =$dbf->f('const_value');
	
				$selectto = "SELECT const_value
				FROM base_constant WHERE const_term = 'EMAIL_SENATE'";
				$resultto = $dbb->query($selectto);
				$dbb->next_record();
				$tosenate =$dbb->f('const_value');
				
				$selectsenate = "SELECT const_value
				FROM base_constant WHERE const_term = 'SENATE_STAFF_ID'";
				$resultselectfaculty = $dbe->query($selectsenate);
				$dbe->next_record();
				$selectidsenate =$dbe->f('const_value');
				
				$selectsenname = "SELECT a.name,a.email,b.title 
				FROM new_employee a
				LEFT JOIN lookup_gelaran b ON(b.id = a.title)
				WHERE a.empid = '$selectidsenate'";
				$resultselsenname = $dbc->query($selectsenname);
				$dbc->next_record();
				$selectname =$dbc->f('name');
				$senateemail =$dbc->f('email');
				$senatetitle=$dbc->f('title');
				
				$studname = "SELECT name,email FROM student WHERE matrix_no='$myStudentId[$val]'";
				$resultstudname = $dbk->query($studname);
				$dbk->next_record();
				$studidname =$dbk->f('name');
				$studemail	=$dbk->f('email');
			
				$myFormatForView = $reportDateEmail; 
				
				$verified = $_REQUEST['myVerifiedStatus'];
								
				$sqlsuper = "SELECT description FROM ref_supervisor_type
				WHERE id = 'RV'";
				$resultsuper = $dbg->query($sqlsuper);		
				$dbg->next_record();
				$position = $dbg->f('description');
				//Verification				
				if($verified == 'APP')
				{
					$vstatus = "Approved";
					//select reviewer id
					$sqlselrev = "SELECT pg_employee_empid 
					FROM pg_supervisor  
					WHERE pg_student_matrix_no = '$myStudentId[$val]'
					AND ref_supervisor_type_id = 'RV'
					AND STATUS = 'A'
					AND pg_thesis_id = '$pgThesisId'";
					$resultsqlselrev = $dba->query($sqlselrev);
					while($dba->next_record())// Loop for every reviewer that has been assigned.
					{
						$reviewerid =$dba->f('pg_employee_empid');	
						
						//select reviewer name, email
						$sqlrev = "SELECT a.name,a.email,b.title 
						FROM new_employee a
						LEFT JOIN lookup_gelaran b ON(b.id = a.title)
						WHERE a.empid = '$reviewerid'";
						$resultsqlrev = $dbl->query($sqlrev);
						$dbl->next_record();
						$reviewername =$dbl->f('name');	
						$revieweremail =$dbl->f('email');
						$title =$dbl->f('title');			
	
						
						$FileType = Array();
						$selectattachment= "SELECT *
						FROM file_upload_proposal WHERE student_matrix_no = '$myStudentId[$val]' 
						AND pg_proposal_id = '$proposal_id'
						AND attachment_level = 'S'";
						$resultattachment = $dbj->query($selectattachment);
					
						while($dbj->next_record())
						{
							$rowData = $dbj->rowdata();
							$FileName[] = $rowData['fu_document_filename'];
							$FileType[] = $rowData['fu_document_filetype'];
							$attachmentdata[] = $rowData['fu_document_filedata'];
	
						}
						$sqlvalidation= "SELECT const_value
						FROM base_constant WHERE const_term = 'EMAIL_FAC_TO_REV'";
						$resultsqlvalidation = $dbu->query($sqlvalidation);
						$dbu->next_record();
						$facrev =$dbu->f('const_value');
	
						if($facrev == 'Y')
						{
							include("../../../app/application/email/email_assign_reviewer.php");	
						}

					}
					
					$validation= "SELECT const_value
					FROM base_constant WHERE const_term = 'EMAIL_FAC_TO_SEN'";
					$resultvalidation = $db->query($validation);
					$db->next_record();
					$valid =$db->f('const_value');

					if($valid == 'Y')
					{
						include("../../../app/application/email/email_approved.php");
						
						
					}
	
				}
				elseif($verified == 'AWC')
				{
					$vstatus = "Approved with Changes";
					//select reviewer id
					$sqlselrev = "SELECT pg_employee_empid 
					FROM pg_supervisor  
					WHERE pg_student_matrix_no = '$myStudentId[$val]'
					AND ref_supervisor_type_id = 'RV'
					AND STATUS = 'A'
					AND pg_thesis_id = '$pgThesisId'";
					$resultsqlselrev = $dba->query($sqlselrev);
					while($dba->next_record())// Loop for every reviewer that has been assigned.
					{
						$reviewerid =$dba->f('pg_employee_empid');	

						//select reviewer name, email
						$sqlrev = "SELECT a.name,a.email,b.title 
						FROM new_employee a
						LEFT JOIN lookup_gelaran b ON(b.id = a.title)
						WHERE a.empid = '$reviewerid'";
						$resultsqlrev = $dbl->query($sqlrev);
						$dbl->next_record();
						$reviewername =$dbl->f('name');	
						$revieweremail =$dbl->f('email');
						$title =$dbl->f('title');			

						$FileType = Array();
						$selectattachment= "SELECT *
						FROM file_upload_proposal WHERE student_matrix_no = '$myStudentId[$val]' 
						AND pg_proposal_id = '$proposal_id'
						AND attachment_level = 'S'";
						$resultattachment = $dbj->query($selectattachment);
					
						while($dbj->next_record())
						{
							$rowData = $dbj->rowdata();
							$FileName[] = $rowData['fu_document_filename'];
							$FileType[] = $rowData['fu_document_filetype'];
							$attachmentdata[] = $rowData['fu_document_filedata'];
						
						}
						$sqlvalidation= "SELECT const_value
						FROM base_constant WHERE const_term = 'EMAIL_FAC_TO_REV'";
						$resultsqlvalidation = $dbu->query($sqlvalidation);
						$dbu->next_record();
						$facrev =$dbu->f('const_value');
	
						if($facrev == 'Y')
						{
							include("../../../app/application/email/email_assign_reviewer.php");	
						}
					}
					
					$validation= "SELECT const_value
					FROM base_constant WHERE const_term = 'EMAIL_FAC_TO_SEN'";
					$resultvalidation = $db->query($validation);
					$db->next_record();
					$valid =$db->f('const_value');

					if($valid == 'Y')
					{					 
						include("../../../app/application/email/email_app_changes.php");
					}
					

				}
				elseif($verified == 'REQ')
				{
					$vstatus = "Request Changes";
					$FileType = Array();
					$selectattachment= "SELECT *
					FROM file_upload_proposal 
					WHERE pg_proposal_id = '$proposal_id'
					AND attachment_level = 'F'";
					$resultattachment = $dbj->query($selectattachment);
				
					while($dbj->next_record())
					{
						$rowData = $dbj->rowdata();
						$FileName[] = $rowData['fu_document_filename'];
						$FileType[] = $rowData['fu_document_filetype'];
						$attachmentdata[] = $rowData['fu_document_filedata'];
				 	
					}
					$validation= "SELECT const_value
					FROM base_constant WHERE const_term = 'EMAIL_FAC_TO_STU'";
					$resultvalidation = $db->query($validation);
					$db->next_record();
					$valid =$db->f('const_value');
					if($valid == 'Y')
					{							 
						include("../../../app/application/email/email_changes.php");
					}
				}
				else//($verified == 'DIS')
				{
					$vstatus = "Disapproved";
					$FileType = Array();
					$selectattachment= "SELECT *
					FROM file_upload_proposal 
					WHERE pg_proposal_id = '$proposal_id'
					AND attachment_level = 'F'";
					$resultattachment = $dbj->query($selectattachment);
				
					while($dbj->next_record())
					{
						$rowData = $dbj->rowdata();
						$FileName[] = $rowData['fu_document_filename'];
						$FileType[] = $rowData['fu_document_filetype'];
						$attachmentdata[] = $rowData['fu_document_filedata'];
				 	
					}					 
					$validation= "SELECT const_value
					FROM base_constant WHERE const_term = 'EMAIL_FAC_TO_STU'";
					$resultvalidation = $db->query($validation);
					$db->next_record();
					$valid =$db->f('const_value');
					if($valid == 'Y')
					{							 
						include("../../../app/application/email/email_disapproved.php");
					}
					else
					{  }
				}

				//ADDED EMAIL FEATURE END
				
				$msg[] = "<div class=\"success\"><span>The selected thesis proposal $myPgThesisId[$val] has been confirmed accordingly.</span></div>";

			}
			else {		
				$msg[] = "<div class=\"error\"><span>The confirmation of this thesis proposal $myPgThesisId[$val] is put on hold due to no supervisor is assigned.</span></div>";
			}		
			
		}
	}
	else {
		
		$msg[] = "<div class=\"error\"><span>Please tick the checkbox before submit.</div>";
		
	}

}

if(isset($_POST['btnSearch']) && ($_POST['btnSearch'] <> "")) {
	
	$searchThesisDate = $_POST['searchThesisDate'];
	$searchThesisId = $_POST['searchThesisId'];
	$searchStudent = $_POST['searchStudent'];
	
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
	
$sql2 = "SELECT a.pg_thesis_id, a.id, DATE_FORMAT(a.report_date,'%d-%b-%Y') AS theReportDate, a.thesis_title, a.thesis_type, 
		b.description AS theThesisTypeDescription, a.introduction, a.objective, a.description, a.status, a.discussion_status, c.description AS theProposalStatusDescription, d.student_matrix_no, d.supervisor_status, d.reviewer_status,
		a.verified_status, c2.description as verified_desc, a.verified_remarks, a.marked_status
		FROM pg_proposal a
		LEFT JOIN  ref_thesis_type b ON (b.id = a.thesis_type) 
		LEFT JOIN ref_proposal_status c ON (c.id = a.status)
		LEFT JOIN ref_proposal_status c2 ON (c2.id = a.verified_status)
		LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id) 
		WHERE a.verified_status in ('INP','APP','AWC','REQ','DIS')" 
		.$tmpSearchThesisId." "
		.$tmpSearchStudent." "
		.$tmpSearchThesisDate." "."
		AND a.archived_status IS NULL 
		AND a.status = 'OPN'
		AND d.status = 'INP'
		ORDER BY a.report_date DESC, a.pg_thesis_id, a.id";		
		
		$result2 = $db->query($sql2); 
		$db->next_record();
		$pgThesisIdArray = Array();	
		$studentMatrixNoArray = Array();
		$studentNameArray = Array();						
		$proposalIdArray = Array();
		$reportDateArray = Array();
		$thesisTitleArray = Array();
		$supervisorStatusArray = Array();	
		$reviewerStatusArray = Array();
		$verifiedDescArray = Array();	
		$verifiedRemarksArray = Array();	
		$markedStatusArray = Array();
		$verifiedStatusArray = Array();
		
		
		
		
		$no1=0;
		$no2=0;
		do {
			$pgThesisIdArray[$no1] = $db->f('pg_thesis_id');	
			$studentMatrixNoArray[$no1] = $db->f('student_matrix_no');
			$proposalIdArray[$no1] = $db->f('id');
			$reportDateArray[$no1] = $db->f('theReportDate');
			$thesisTitleArray[$no1] = mysql_real_escape_string($db->f('thesis_title'));
			$supervisorStatusArray[$no1] = $db->f('supervisor_status');	
			$reviewerStatusArray[$no1] = $db->f('reviewer_status');
			$verifiedDescArray[$no1] = $db->f('verified_desc');	
			$verifiedRemarksArray[$no1] = $db->f('verified_remarks');	
			$markedStatusArray[$no1] = $db->f('marked_status');
			$verifiedStatusArray[$no1] = $db->f('verified_status');
			$no1++;
			
		} while ($db->next_record());
		
		$studentNameArray = Array();
		for ($i=0; $i<$no1; $i++){
			$sql9 = "SELECT name
				FROM student
				WHERE matrix_no = '$studentMatrixNoArray[$i]'
				AND name like '%$searchStudentName%'";
			if (substr($studentMatrixNoArray[$i],0,2) != '07') { 
				$dbConnStudent= $dbc; 
			} 
			else { 
				$dbConnStudent=$dbc1; 
			}
			$result9 = $dbConnStudent->query($sql9); 
			$dbConnStudent->next_record();
			if (mysql_num_rows($result9)>0) {
				$studentNameArray[$no2++] = $dbConnStudent->f('name');
			}
		}
		$row_cnt = $no2;
}
else 
{
	$sql2 = "SELECT a.pg_thesis_id, a.id, DATE_FORMAT(a.report_date,'%d-%b-%Y') AS theReportDate, a.thesis_title, a.thesis_type, 
		b.description AS theThesisTypeDescription, a.introduction, a.objective, a.description, a.status, a.discussion_status, c.description AS theProposalStatusDescription, d.student_matrix_no, d.supervisor_status, d.reviewer_status,
		a.verified_status, c2.description as verified_desc, a.verified_remarks, a.marked_status
		FROM pg_proposal a
		LEFT JOIN  ref_thesis_type b ON (b.id = a.thesis_type) 
		LEFT JOIN ref_proposal_status c ON (c.id = a.status)
		LEFT JOIN ref_proposal_status c2 ON (c2.id = a.verified_status)
		LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id) 
		WHERE a.verified_status in ('INP','APP','AWC','REQ','DIS') 
		AND a.archived_status IS NULL 
		AND a.status = 'OPN'
		AND d.status = 'INP'
		ORDER BY a.report_date DESC, a.pg_thesis_id, a.id ";		
		
		$result2 = $db->query($sql2); 
		$db->next_record();
		$pgThesisIdArray = Array();	
		$studentMatrixNoArray = Array();
		$studentNameArray = Array();						
		$proposalIdArray = Array();
		$reportDateArray = Array();
		$thesisTitleArray = Array();
		$supervisorStatusArray = Array();	
		$reviewerStatusArray = Array();
		$verifiedDescArray = Array();	
		$verifiedRemarksArray = Array();	
		$markedStatusArray = Array();
		$verifiedStatusArray = Array();
		
		
		
		
		$no1=0;
		$no2=0;
		do {
			$pgThesisIdArray[$no1] = $db->f('pg_thesis_id');	
			$studentMatrixNoArray[$no1] = $db->f('student_matrix_no');
			$proposalIdArray[$no1] = $db->f('id');
			$reportDateArray[$no1] = $db->f('theReportDate');			
			$thesisTitleArray[$no1] = $db->f('thesis_title');
			$supervisorStatusArray[$no1] = $db->f('supervisor_status');	
			$reviewerStatusArray[$no1] = $db->f('reviewer_status');
			$verifiedDescArray[$no1] = $db->f('verified_desc');	
			$verifiedRemarksArray[$no1] = $db->f('verified_remarks');	
			$markedStatusArray[$no1] = $db->f('marked_status');
			$verifiedStatusArray[$no1] = $db->f('verified_status');
			$no1++;
			
		} while ($db->next_record());
		
		$studentNameArray = Array();
		for ($i=0; $i<$no1; $i++){
			$sql9 = "SELECT name
				FROM student
				WHERE matrix_no = '$studentMatrixNoArray[$i]'
				AND name like '%$searchStudentName%'";
			if (substr($studentMatrixNoArray[$i],0,2) != '07') { 
				$dbConnStudent= $dbc; 
			} 
			else { 
				$dbConnStudent=$dbc1; 
			}
			$result9 = $dbc->query($sql9); 
			$dbc->next_record();
			if (mysql_num_rows($result9)>0) {
				$studentNameArray[$no2] = $dbc->f('name');
				$pgThesisIdArray[$no2] = $pgThesisIdArray[$i];	
				$studentMatrixNoArray[$no2] = $studentMatrixNoArray[$i];
				$proposalIdArray[$no2] = $proposalIdArray[$i];
				$reportDateArray[$no2] = $reportDateArray[$i];			
				$thesisTitleArray[$no2] = $thesisTitleArray[$i];
				$supervisorStatusArray[$no2] = $supervisorStatusArray[$i];	
				$reviewerStatusArray[$no2] = $reviewerStatusArray[$i];
				$verifiedDescArray[$no2] = $verifiedDescArray[$i];	
				$verifiedRemarksArray[$no2] = $verifiedRemarksArray[$i];	
				$markedStatusArray[$no2] = $markedStatusArray[$i];
				$verifiedStatusArray[$no2] = $verifiedStatusArray[$i];
				$no2++;
			}
		}
		$row_cnt = $no2;
}

if(isset($_POST['btnSearchByName']) && ($_POST['btnSearchByName'] <> "")) {
	
	$searchStudentName = $_POST['searchStudentName'];
	
	$sql8 = "SELECT a.pg_thesis_id, a.id, DATE_FORMAT(a.report_date,'%d-%b-%Y') AS theReportDate, a.thesis_title, a.thesis_type, 
		b.description AS theThesisTypeDescription, a.introduction, a.objective, a.description, a.status, a.discussion_status, c.description AS theProposalStatusDescription, d.student_matrix_no, d.supervisor_status, d.reviewer_status,
		a.verified_status, c2.description as verified_desc, a.verified_remarks, a.marked_status
		FROM pg_proposal a
		LEFT JOIN  ref_thesis_type b ON (b.id = a.thesis_type) 
		LEFT JOIN ref_proposal_status c ON (c.id = a.status)
		LEFT JOIN ref_proposal_status c2 ON (c2.id = a.verified_status)
		LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id) 
		WHERE a.verified_status in ('INP','APP','AWC','REQ','DIS')
		AND a.archived_status IS NULL 
		AND a.status = 'OPN'
		AND d.status = 'INP'
		ORDER BY a.report_date DESC, a.pg_thesis_id, a.id";		
		
		$result8 = $db->query($sql8); 
		$db->next_record();
		
		$pgThesisIdArray = Array();	
		$studentMatrixNoArray = Array();
		$proposalIdArray = Array();
		$reportDateArray = Array();
		$thesisTitleArray = Array();
		$supervisorStatusArray = Array();	
		$reviewerStatusArray = Array();
		$verifiedDescArray = Array();	
		$verifiedRemarksArray = Array();	
		$markedStatusArray = Array();
		$verifiedStatusArray = Array();
		
		
		
		
		$no1=0;
		$no2=0;
		do {
			$pgThesisIdArray[$no1] = $db->f('pg_thesis_id');	
			$studentMatrixNoArray[$no1] = $db->f('student_matrix_no');
			$proposalIdArray[$no1] = $db->f('id');
			$reportDateArray[$no1] = $db->f('theReportDate');
			$thesisTitleArray[$no1] = $db->f('thesis_title');
			$supervisorStatusArray[$no1] = $db->f('supervisor_status');	
			$reviewerStatusArray[$no1] = $db->f('reviewer_status');
			$verifiedDescArray[$no1] = $db->f('verified_desc');	
			$verifiedRemarksArray[$no1] = $db->f('verified_remarks');	
			$markedStatusArray[$no1] = $db->f('marked_status');
			$verifiedStatusArray[$no1] = $db->f('verified_status');
			$no1++;
			
		} while ($db->next_record());
		
		$studentNameArray = Array();
		for ($i=0; $i<$no1; $i++){
			$sql9 = "SELECT name
				FROM student
				WHERE matrix_no = '$studentMatrixNoArray[$i]'
				AND name like '%$searchStudentName%'";
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
				$pgThesisIdArray[$no2] = $pgThesisIdArray[$i];
				$studentMatrixNoArray[$no2] = $studentMatrixNoArray[$i];
				$proposalIdArray[$no2] = $proposalIdArray[$i];
				$reportDateArray[$no2] = $reportDateArray[$i];
				$thesisTitleArray[$no2] = $thesisTitleArray[$i];
				$supervisorStatusArray[$no2] = $supervisorStatusArray[$i];
				$reviewerStatusArray[$no2] = $reviewerStatusArray[$i];
				$verifiedDescArray[$no2] = 	$verifiedDescArray[$i];
				$verifiedRemarksArray[$no2] = $verifiedRemarksArray[$i];
				$markedStatusArray[$no2] = $markedStatusArray[$i];
				$verifiedStatusArray[$no2] = $verifiedStatusArray[$i];
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
		<link rel="stylesheet" href="../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
		<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
		<script src="../../../lib/js/jquery.min2.js"></script>
		<script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
		<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>
		
		<SCRIPT LANGUAGE="JavaScript">

function respConfirm () {
    var confirmSubmit = confirm("Click OK if you confirm to submit else click CANCEL to proceed with the changes.");
	if (confirmSubmit==true)
	{
		return saveStatus;
	}
	if (confirmSubmit==false)
	{
		return false;
	}
}
<script>
$(function() {
	$( "#datepickerFirst" ).datepicker({
		changeMonth: true,
		changeYear: true,
		yearRange: '-100:+0',
		dateFormat: 'dd-mm-yy'
		});
});
</script>
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

		<form id="form1" name="form1" method="post" enctype="multipart/form-data">				
			
				<?  
				//$result2 = $db->query($sql2); 
				//$row_cnt = mysql_num_rows($result2);
				?>
				
				<fieldset>
				<legend><strong>List of Thesis Proposal</strong></legend>
					<table>
						<tr>							
							<td><strong>Please enter searching criteria below</strong></td>
						</tr>
						<tr>
							<td><strong>Notes:-</strong>(by default it will display,<br/>
							1. Current proposal in which it status has been confirmed by Faculty and<br/>
							2. Proposal in which the status is still pending for Faculty confirmation)</td>
					</table>
					<br/>
					<table>
						<tr>
							<?$searchRequestDate = date("d-M-Y");?>
							<td>Thesis Date</td>
							<td>:</td>
							<td><input type="text" name="searchThesisDate" size="15" id="searchThesisDate" value="<?=$searchThesisDate;?>"/></td>
							<?	$jscript .= "\n" . '$( "#searchThesisDate' . $no . '" ).datepicker({
												changeMonth: true,
												changeYear: true,
												yearRange: \'-100:+0\',
												dateFormat: \'dd-M-yy\'
											});';
					 
				?>
						</tr>
						<tr>
							<?$searchRequestDate = date("d-M-Y");?>
							<td>[Thesis / Project ID] / [Thesis Title]</td>
							<td>:</td>
							<td><input type="text" name="searchThesisId" size="50" id="searchThesisId" value="<?=$searchThesisId;?>"/></td>
						</tr>
						<tr>
							<td>Matrix No </td>
							<td>:</td>
							<td><input type="text" name="searchStudent" size="30" id="searchStudent" value="<?=$searchStudent;?>"/></td>
							<td><input type="submit" name="btnSearch" value="Search" /></td>
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
								<td>Searching Results:-</td>
							</tr>
					</table>
					<table width="100%" border="1" cellpadding="3" cellspacing="1" style="border-collapse:collapse;" class="thetable">
						<tr>						
							<th width="30" align="center"><strong>Tick</strong></th>	
							<th width="24" align="center"><strong>No.</strong></th>
							<th width="77"><strong>Faculty Status</strong></th>
							<th width="103"><strong>Thesis Date</strong></th>
							<th width="103"><strong>Thesis / Project ID</strong></th>
							<th width="156"><strong>Thesis / Project Title</strong></th>
							<th width="69"><strong>Student Name</strong></th>
							<th width="98"><strong>Attachment by Student</strong></th>
							<th width="96"><strong>Attachment by Faculty</strong></th>
							<th width="78"><strong>Supervisor / Co-Supervisor</strong></th>
							<th width="78"><strong>Reviewer</strong></th>
						</tr>
					<?
					if ($no2>0) 
					{?>
					<?
						$no=0;
						$myNo=1;
						//while($db->next_record()) {	
						for ($i=0; $i<$no2; $i++){
						
						?>
							
							<tr>
							
								<? 
								
								if ($verifiedStatusArray[$i]=='APP' || $verifiedStatusArray[$i]=='AWC' || $verifiedStatusArray[$i]=='REQ' || $verifiedStatusArray[$i]=='DIS'){
									?><td align="center"><label><input name="myApprovalBox[]" type="checkbox" value="<?=$no;?>" disabled="disabled"/></label></td><?
								}
								else {
									?><td align="center"><label><input name="myApprovalBox[]" type="checkbox" value="<?=$no;?>"/></label></td><?
								}
								?>
								
								
								<?$myProposalId[$no]=$proposalIdArray[$i];?>
								
								<td align="center"><?=$myNo++;?>.
								<?
								$sql3_1 = "SELECT const_value
								FROM base_constant
								WHERE const_term = 'NEW_PROPOSAL_FACULTY'";

								$result3_1 = $dbb->query($sql3_1);
								$dbb->next_record();
								$parameterValue=$dbb->f('const_value');
								//var_dump($dbb);
								
								$newReportDate = date('d-M-Y', strtotime($reportDateArray[$i]. ' + '.$parameterValue.' days'));		
								$currentDate = new DateTime();			
								$tmpNewReportDate = new DateTime($newReportDate);
								
								$myTmpNewReportDate = $tmpNewReportDate->format('d-M-Y');
								$myCurrentDate = $currentDate->format('d-M-Y');
								
								$myTmpNewReportDate1 = new DateTime($myTmpNewReportDate);
								$myCurrentDate1 = new DateTime($myCurrentDate);
								
								//$myTmpNewReportDate1 = date_create($myTmpNewReportDate);
								//$myCurrentDate1 = date_create($myCurrentDate);
								
								//echo date_format($myTmpNewReportDate1,'d-M-Y');
								//echo date_format($myCurrentDate1,'d-M-Y');
								
								//if (($tmpNewReportDate->format('d-M-Y') < $currentDate->format('d-M-Y'))) {
								if ($myCurrentDate1 <= $myTmpNewReportDate1) {
								?>
									<img src="../images/new.jpg" width="50" height="40" style="border:0px;" title="Proposal is considered new if it is submitted within <?=$parameterValue?> day(s)">
								<?}?></td>
								
								<td width="22"><label name="myVerifiedDesc[]" cols="45" id="verifiedDesc"><?=$verifiedDescArray[$i]?></label>
								<input type="hidden" name="myProposalId[]" size="12" id="proposalId" value="<?=$proposalIdArray[$i];?>"/></td>
								
								<td><label name="reportDate[]" cols="45" id="reportDate"><?=$reportDateArray[$i]?></label></td>
								
								<td><a href="proposal_outline.php?thesisId=<? echo $pgThesisIdArray[$i];?>&proposalId=<? echo $proposalIdArray[$i];?>" name="myPgThesisId[]" value="<?=$pgThesisIdArray[$i]?>" title="Outline of Proposed Case Study by the Student - Read more..."><?=$pgThesisIdArray[$i];?><br/>
								<?if ($verifiedRemarksArray[$i] == null || $verifiedRemarksArray[$i] ==""){?>
									<img src="../images/red_edit.jpg" width="20" height="19" style="border:0px;" title="Faculty Remark is not yet provided" >Enter remarks</a>	
								<?}
								else {
								?>
									<img src="../images/edit.jpg" width="20" height="19" style="border:0px;" title="Faculty Remark is provided" >Read remarks</a>	
								<?
								}
								if ($markedStatusArray[$i] == "MAR"){?>
									<br/><img src="../images/mark.jpg" width="20" height="19" style="border:0px;" title="Thesis is marked for review" >Review in progress</a>								
								<?}?></td>	
						
								<?$myPgThesisId[$no]=$pgThesisIdArray[$i];?>
								
								<td><label name="myThesisTitle[]" cols="45" id="thesisTitle"><?=$thesisTitleArray[$i]?></label></td>
								
								<td><label name="myStudentName[]" size="30" id="studentName" ></label><?=$studentNameArray[$i];?><br/>(<?=$studentMatrixNoArray[$i];?>)
								<input type="hidden" name="studentId[]" size="12" id="studentId" value="<?=$studentMatrixNoArray[$i];?>"/>
								<input type="hidden" name="studentName[]" size="12" id="studentName" value="<?=$studentNameArray[$i];?>"/></td>

								<?$myStudentMatrixNo[$no]=$studentMatrixNoArray[$i];?>
								<?$myStudentName[$no]=$studentNameArray[$i];?>
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
								
								
								<?php
									$sqlUpload="SELECT * FROM file_upload_proposal 
									WHERE pg_proposal_id='$proposalIdArray[$i]' 
									AND attachment_level='F' ";			

									$result = $db_klas2->query($sqlUpload); //echo $sql;
									$row_cnt = mysql_num_rows($result);
									$attachmentNo2=1;
									if ($row_cnt>0)
									{
										?><td align="left"><?
										while($row = mysql_fetch_array($result)) 					
										{ 
											?>
												<a href="download.php?fc=<?=$row["fu_cd"];?>&al=F" title="File Description: <?=$row["fu_document_filedesc"];?>">Attachment <?=$attachmentNo2++;?>: <br/><img src="../images/download.png" width="20" height="19" style="border:0px;" title="Download <?=$row["fu_document_filename"];?>"></a>
												<a href="delete_attachment_confirm.php?fc=<?=$row["fu_cd"];?>&al=F" ><img src="../images/delete_on.gif" width="20" height="19" style="border:0px;" title="Delete <?=$row["fu_document_filename"];?>"></a>
												<a href="edit_attachment_confirm.php?fc=<?=$row["fu_cd"];?>&al=F" ><img src="../images/red_edit.jpg" width="20" height="19" style="border:0px;" title="Edit file description"></a><br/><br/>
										<?}
										?><br/><input type="submit" name="submit" value="Upload" onclick="javascript:open_win('confirm_proposal_upload.php?pid=<?=$proposalIdArray[$i];?>&al=F',480,280,0,0,0,1,0,1,1,0,5,'winupload'); "/></td><?
									}
									else {
										?><td width="80">No attachment<br/>
										  <input type="submit" name="submit" value="Upload" onclick="javascript:open_win('confirm_proposal_upload.php?pid=<?=$proposalIdArray[$i];?>&al=F',480,280,0,0,0,1,0,1,1,0,5,'winupload'); "/></td><?
									}
								?>
								
								
							<td width="84">
								<?$_SESSION['studentMatrixNo']=$studentMatrixNo;
								if ($supervisorStatusArray[$i] =='A') {?>
									<a href="../supervisor/edit_supervisor.php?pid=<?php echo $proposalIdArray[$i]?>&tid=<? echo $pgThesisIdArray[$i]?>&mn=<? echo $studentMatrixNoArray[$i]?>">Change <img src="../images/person_reassigned.jpg" width="20" height="19" style="border:0px;" title="Supervisor has been assigned" ></a><br/>
									<br/><a href="../supervisor/view_supervisor.php?mn=<? echo $studentMatrixNoArray[$i]?>&tid=<? echo $pgThesisIdArray[$i]?>">View</a> 
								<?}
								else {?>
									<a href="../supervisor/edit_supervisor.php?pid=<?php echo $proposalIdArray[$i]?>&tid=<? echo $pgThesisIdArray[$i]?>&mn=<? echo $studentMatrixNoArray[$i]?>">Assign <img src="../images/person_assigned.jpg" width="20" height="19" style="border:0px;" title="Supervisor is not assigned yet" ></a>
								<?}?>															
							</td>
							
							<td width="84">
								<?
								if ($reviewerStatusArray[$i] =='A') {?>
									<a href="../reviewer/edit_reviewer.php?pid=<?php echo $proposalIdArray[$i]?>&tid=<? echo $pgThesisIdArray[$i]?>&mn=<? echo $studentMatrixNoArray[$i]?>">Change <img src="../images/person_reassigned.jpg" width="20" height="19" style="border:0px;" title="Reviewer has been assigned" ></a><br/>
									<br/><a href="../reviewer/view_reviewer.php?mn=<? echo $studentMatrixNoArray[$i]?>&tid=<? echo $pgThesisIdArray[$i]?>">View</a> 
								<?}
								else {?>
									<a href="../reviewer/edit_reviewer.php?pid=<?php echo $proposalIdArray[$i]?>&tid=<? echo $pgThesisIdArray[$i]?>&mn=<? echo $studentMatrixNoArray[$i]?>">Assign <img src="../images/person_assigned.jpg" width="20" height="19" style="border:0px;" title="Reviewer is not assigned yet" ></a>
								<?}?>															
							</td>
							
						</tr>
							
						<?
						$no++;
						
						};	
						?>

						<?$_SESSION['myPgThesisId'] = $myPgThesisId;?>				
						<?$_SESSION['myStudentMatrixNo'] = $myStudentMatrixNo;?>

				</table>
					
					
					<?$_SESSION['myApprovalBox'] = $myApprovalBox;?>
					<?$_SESSION['myVerifiedStatus'] = $myVerifiedStatus;?>
					<?$_SESSION['myVerifiedRemarks'] = $myVerifiedRemarks;?>
					<br/>
					<table>					
						<tr>				
							<td><input type="submit" name="btnMark" value="Mark For Review" /></td>
							<td><input type="submit" name="btnUnmark" value="Unmark All" /></td>							
							<td><input type="button" name="btnPrintReview" value="Print for Review" onclick="javascript:document.location.href='pdf_marked_proposal.php';" /></td>							
						</tr>
					</table>
		  </fieldset>
					<br/>
					<br/>
					<fieldset>
					<legend><strong>Verification Confirmation by Faculty</strong></legend>				
					<table>				 
						 <tr>
							<td>Proposal Status</td>
							<td>:</td>
							<td>				
								<input name="myVerifiedStatus" type="radio" value="APP" checked="checked"/>Approved
								<input name="myVerifiedStatus" type="radio" value="AWC"/>Approved with Changes
								<input type="radio" name="myVerifiedStatus" value="REQ"/>Request Changes
								<input type="radio" name="myVerifiedStatus" value="DIS"/>Disapproved
							</td>
						</tr>
					</table>
					</fieldset>
					<br/>
					<table>					
						<tr>				
							<td><input type="button" name="btnPrintProposal" value="Print Proposal List" onclick="javascript:document.location.href='pdf_senate_approval.php';" /></td>
							<td><input type="submit" name="btnSubmit" value="Submit" onClick="return respConfirm()" /><span style="color:#FF0000"> Note:</span> Ensure the proposal above has been selected before click Submit.</td>
						</tr>
					</table>							
					<?
				}
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
							<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../thesis/confirm_proposal.php';" /></td>
						</tr>
					</table>
					<?
				}				
				?>					
		</form>
		<script>
			<?=$jscript;?>
		</script>
	</body>
</html>



