<?php

include("../../../lib/common.php");
checkLogin();

session_start();
$userid=$_REQUEST['uid'];
$studentMatrixNo=$_REQUEST['uid'];
$pgThesisId=$_REQUEST['tid'];
$pgProposalId=$_REQUEST['pid'];

function convertname($user_id)
{
	global $db;
	$sql_login = "SELECT name FROM new_employee WHERE empid='$user_id' union SELECT name FROM student WHERE matrix_no='$user_id'";
	$db->query($sql_login);
	$db->next_record();
	$rows = $db->rowdata();
	$name = $rows['name'];
	return $name;
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

if(isset($_POST['btnSave']) && ($_POST['btnSave'] <> ""))
{	
	$msg = array();
	if (empty($_POST['thesis_title'])) $msg[] = "<div class=\"error\"><span>Please enter Thesis Title</span></div>";
	if (empty($_POST['introduction'])) $msg[] = "<div class=\"error\"><span>Please enter Introduction</span></div>";
	if (empty($_POST['objective'])) $msg[] = "<div class=\"error\"><span>Please enter objective</span></div>";
	if (empty($_POST['description'])) $msg[] = "<div class=\"error\"><span>Please enter Description</span></div>";
	if ($_POST['JobArea'] == "") $msg[] = "<div class=\"error\"><span>Please select Thesis Area</span></div>";
	
	if(empty($msg)) 
	{
		/*$myjobs_area1 = $_POST['jobs1_area'];
		$myjobs_area2 = $_POST['jobs2_area'];
		$myjobs_area3 = $_POST['jobs3_area'];
		$myjobs_area4 = $_POST['jobs4_area'];
		$myjobs_area5 = $_POST['jobs5_area'];
		$myjobs_area6 = $_POST['jobs6_area'];*/
		
		$myjobs_area1 = $_REQUEST['JobAreaID'];
		$myjobs_area2 = $_REQUEST['JobAreaID1'];
		$myjobs_area3 = $_REQUEST['JobAreaID2'];
		$myjobs_area4 = $_REQUEST['JobAreaID3'];
		$myjobs_area5 = $_REQUEST['JobAreaID4'];
		$myjobs_area6 = $_REQUEST['JobAreaID5'];


		$curdatetime = date("Y-m-d H:i:s");	
		
		$sql1 ="SELECT pp.id as proposal_id, pt.id as thesis_id, pp.verified_status,ppa.*
					FROM pg_thesis pt 
					LEFT JOIN pg_proposal pp ON (pp.pg_thesis_id=pt.id) 
					LEFT JOIN ref_proposal_status rps ON (rps.id = pp.verified_status)  
					LEFT JOIN pg_proposal_area ppa ON (ppa.pg_proposal_id=pp.id) 
					WHERE pt.student_matrix_no = '$user_id'
					AND pp.verified_status in ('SAV','REQ','CAN')
					AND pp.archived_status is null
					AND pt.ref_thesis_status_id_proposal = 'INP'
					ORDER BY pt.id";

		$result_sql1 = $db->query($sql1);
		$row_area = $db->fetchArray();
		$thesis_id=$row_area['thesis_id'];
		$proposal_id=$row_area['proposal_id'];
		$verified_status=$row_area['verified_status'];
		$jobs_area1=$row_area['job_id1_area'];
		$jobs_area2=$row_area['job_id2_area'];
		$jobs_area3=$row_area['job_id3_area'];
		$jobs_area4=$row_area['job_id4_area'];
		$jobs_area5=$row_area['job_id5_area'];
		$jobs_area6=$row_area['job_id6_area'];
		
		
		$row_cnt = mysql_num_rows($result_sql1);
		if ($row_cnt==0) //no record
		{	
			
			$thesis_id = "T".runnum('id','pg_thesis');
			$proposal_id = "P".runnum('id','pg_proposal');
			
			
			$lock_tables="LOCK TABLES pg_thesis WRITE"; //lock the table
			$db->query($lock_tables);
			$sqlsubmit2="INSERT INTO pg_thesis(id,status, student_matrix_no,insert_by,insert_date,
			modify_by,modify_date,ref_thesis_status_id_proposal)
			VALUES('$thesis_id','INP','$user_id','$user_id','$curdatetime','$user_id','$curdatetime','INP') ";
			$db_klas2->query($sqlsubmit2); 
			$unlock_tables="UNLOCK TABLES"; //unlock the table;
			$db->query($unlock_tables);
			 
			$sqlsubmit="INSERT INTO pg_proposal
					(id,thesis_title,thesis_type,introduction,objective,description,discussion_status,
					pg_thesis_id, verified_status, status, report_date, insert_by,insert_date,modify_by,modify_date)
					VALUES('$proposal_id','".mysql_real_escape_string($thesis_title)."','$thesis_type','$introduction','$objective','$description',
					'$discussion_status','$thesis_id','SAV', 'OPN', '$curdatetime','$user_id','$curdatetime',
					'$user_id','$curdatetime') ";
					
			$db_klas2->query($sqlsubmit); 
			
			$db_klas2->query($sqlUpdate2);
				
			$selectArea = "SELECT * FROM pg_proposal_area
			WHERE pg_proposal_id = '$pgProposalId' ";
			$db_klas2->query($selectArea); //echo $updateArea;
			
			$result_selectArea = $db_klas2->query($selectArea);	
			$row_cnt = mysql_num_rows($result_selectArea);	
			
			if ($row_cnt > 0) {

				$updateArea = "UPDATE pg_proposal_area
				SET job_id1_area = '$myjobs_area1', job_id2_area = '$myjobs_area2', job_id3_area = '$myjobs_area3',
					job_id4_area = '$myjobs_area4',job_id5_area = '$myjobs_area5',job_id6_area = '$myjobs_area6',
					insert_date = '$curdatetime', insert_by = '".$_SESSION['user_id']."', modified_date = '$curdatetime', modified_by = '".$_SESSION['user_id']."'
				WHERE pg_proposal_id = '$pgProposalId' ";
				$db_klas2->query($updateArea); //echo $updateArea;
			}
			else {
				$job_area_id = runnum2('id','pg_proposal_area');
				$insertArea = "INSERT INTO pg_proposal_area
				(id, pg_proposal_id, job_id1_area, job_id2_area, job_id3_area, job_id4_area, job_id5_area, job_id6_area, insert_date, insert_by,
				modified_date, modified_by)
				VALUES('$job_area_id', '$proposal_id', '$myjobs_area1', '$myjobs_area2', '$myjobs_area3', '$myjobs_area4', '$myjobs_area5', 
				'$myjobs_area6', '$curdatetime', '".$_SESSION['user_id']."', '$curdatetime', '".$_SESSION['user_id']."')";
				$db_klas2->query($insertArea);
			}
				
			$sql7_2 = "UPDATE pg_thesis
						SET archived_date = '$curdatetime', archived_status = 'ARC',
						modify_by = '$userid', modify_date = '$curdatetime'
						WHERE id = '$pgThesisId'
						AND student_matrix_no = '$studentMatrixNo'";

						$dbg->query($sql7_2);

						
			$sql3 = "UPDATE pg_proposal 		
						SET archived_status = 'ARC', archived_date = '$curdatetime' 
						WHERE id = '$pgProposalId'
						AND verified_status in ('REQ','CAN')
						AND status = 'OPN'
						AND archived_status is null";

			$db_klas2->query($sql3);
			
			for ($i=0; $i<sizeof($_POST['date']); $i++) {
			
					$meeting_detail_id = runnum2('id','pg_meeting_detail');	
					
					$myMeetingDate = $_POST['date'][$i]." ".$_POST['meeting_time'][$i];
					$myLecturer = mysql_real_escape_string($_POST['lecturer_name'][$i]);
					$myRemarks = mysql_real_escape_string($_POST['remarks'][$i]);
					$sqlMeeting = "INSERT INTO pg_meeting_detail(
									id, 
									lecturer_name,
									meeting_sdate,
									remark,
									pg_proposal_id,
									pg_thesis_progress_id,
									insert_by,
									insert_date,
									modify_by,
									modify_date)
									VALUES (
									'$meeting_detail_id',
									'$myLecturer',
									STR_TO_DATE('$myMeetingDate','%m-%d-%Y %h:%i'), 
									'$myRemarks',
									'$proposal_id',
									null,
									'$user_id',
									'$curdatetime',
									'$user_id',
									'$curdatetime')";
					$db_klas2->query($sqlMeeting); 
				}
				
				for ($i=0; $i<sizeof($_FILES['fileData']['name']); $i++) {
						
					$upload_id = runnum2('fu_cd','file_upload_proposal');
					
					$file_name = $_FILES['fileData']['name'][$i];
					$fileType = $_FILES['fileData']['type'][$i];
					$fileSize = intval($_FILES['fileData']['size'][$i]);
					$fileData = file_get_contents($_FILES['fileData']['tmp_name'][$i]);
					
							
					$sqlUpload = "INSERT INTO file_upload_proposal (
										fu_cd, 
										fu_document_filename, 
										fu_document_filetype, 
										fu_document_filedata,
										fu_document_thumbnail,
										insert_by,
										insert_date,
										modify_by,
										modify_date,
										pg_proposal_id,
										attachment_level)
										VALUES (
										'$upload_id',
										'".$file_name."', 
										'".$fileType."',
										'".mysql_escape_string($fileData)."',
										'',
										'$user_id',
										'$curdatetime',
										'$user_id',
										'$curdatetime',
										'$proposal_id','S')";
							$db_klas2->query($sqlUpload);
				}
					
		}
		else //has record
		{
			if (strcmp($verified_status,'SAV')==0)
			{
				$sqlUpdate1 = "UPDATE pg_thesis
						SET modify_by = '$user_id', modify_date = '$curdatetime', ref_thesis_status_id_proposal = 'INP'
						WHERE id = '$thesis_id'";
						
				$db_klas2->query($sqlUpdate1);
			
				$sqlUpdate2 = "UPDATE pg_proposal
						SET thesis_title = '".mysql_real_escape_string($thesis_title)."', thesis_type = '$thesis_type', introduction = '$introduction', 
						objective = '$objective', description = '$description', modify_by = '$user_id', modify_date = '$curdatetime',
						discussion_status = '$discussion_status', verified_status = 'SAV',
						report_date = '$curdatetime' 
						WHERE id = '$proposal_id'
						AND verified_status in ('SAV','REQ','CAN')";

				$db_klas2->query($sqlUpdate2);
				
				$selectArea = "SELECT * FROM pg_proposal_area
				WHERE pg_proposal_id = '$proposal_id' ";
				$db_klas2->query($selectArea); //echo $updateArea;
				
				$result_selectArea = $db_klas2->query($selectArea);	
				$row_cnt = mysql_num_rows($result_selectArea);	
				
				if ($row_cnt > 0) {

					$updateArea = "UPDATE pg_proposal_area
					SET job_id1_area = '$myjobs_area1', job_id2_area = '$myjobs_area2', job_id3_area = '$myjobs_area3',
						job_id4_area = '$myjobs_area4',job_id5_area = '$myjobs_area5',job_id6_area = '$myjobs_area6',
						insert_date = '$curdatetime', insert_by = '".$_SESSION['user_id']."', modified_date = '$curdatetime', modified_by = '".$_SESSION['user_id']."'
					WHERE pg_proposal_id = '$proposal_id' ";
					$db_klas2->query($updateArea); //echo $updateArea;
				}
				else {
					$job_area_id = runnum2('id','pg_proposal_area');
					$insertArea = "INSERT INTO pg_proposal_area
					(id, pg_proposal_id, job_id1_area, job_id2_area, job_id3_area, job_id4_area, job_id5_area, job_id6_area, insert_date, insert_by,
					modified_date, modified_by)
					VALUES('$job_area_id', '$proposal_id', '$myjobs_area1', '$myjobs_area2', '$myjobs_area3', '$myjobs_area4', '$myjobs_area5', '$myjobs_area6',
					'$curdatetime', '".$_SESSION['user_id']."', '$curdatetime', '".$_SESSION['user_id']."')";
					$db_klas2->query($insertArea);
				}
				
				for ($i=0; $i<sizeof($_POST['date']); $i++) {
			
					$meeting_detail_id = runnum2('id','pg_meeting_detail');	
					
					$myMeetingDate = $_POST['date'][$i]." ".$_POST['meeting_time'][$i];
					$myLecturer = mysql_real_escape_string($_POST['lecturer_name'][$i]);
					$myRemarks = mysql_real_escape_string($_POST['remarks'][$i]);
					$sqlMeeting = "INSERT INTO pg_meeting_detail(
									id, 
									lecturer_name,
									meeting_sdate,
									remark,
									pg_proposal_id,
									pg_thesis_progress_id,
									insert_by,
									insert_date,
									modify_by,
									modify_date)
									VALUES (
									'$meeting_detail_id',
									'$myLecturer',
									STR_TO_DATE('$myMeetingDate','%m-%d-%Y %h:%i'), 
									'$myRemarks',
									'$proposal_id',
									null,
									'$user_id',
									'$curdatetime',
									'$user_id',
									'$curdatetime')";
					$db_klas2->query($sqlMeeting); 
				}
				
				for ($i=0; $i<sizeof($_FILES['fileData']['name']); $i++) {
						
					$upload_id = runnum2('fu_cd','file_upload_proposal');
					
					$file_name = $_FILES['fileData']['name'][$i];
					$fileType = $_FILES['fileData']['type'][$i];
					$fileSize = intval($_FILES['fileData']['size'][$i]);
					$fileData = file_get_contents($_FILES['fileData']['tmp_name'][$i]);
					
							
					$sqlUpload = "INSERT INTO file_upload_proposal (
										fu_cd, 
										fu_document_filename, 
										fu_document_filetype, 
										fu_document_filedata,
										fu_document_thumbnail,
										insert_by,
										insert_date,
										modify_by,
										modify_date,
										pg_proposal_id,
										attachment_level)
										VALUES (
										'$upload_id',
										'".$file_name."', 
										'".$fileType."',
										'".mysql_escape_string($fileData)."',
										'',
										'$user_id',
										'$curdatetime',
										'$user_id',
										'$curdatetime',
										'$proposal_id','S')";
							$db_klas2->query($sqlUpload);
				}

			}
			
			
			else {//REQ, CAN
				
				$new_proposal_id = "P".runnum('id','pg_proposal');
				
			
				$sql1="INSERT INTO pg_proposal
					(id,thesis_title,thesis_type,introduction,objective,description,discussion_status,
					pg_thesis_id, verified_status, status, report_date, insert_by,insert_date,modify_by,modify_date)
					VALUES('$new_proposal_id','".mysql_real_escape_string($thesis_title)."','$thesis_type','$introduction','$objective','$description',
					'$discussion_status',
					'$thesis_id','SAV', 'OPN', '$curdatetime','$user_id','$curdatetime','$user_id','$curdatetime') ";
					
				$db_klas2->query($sql1); 
			
				$sql2 = "UPDATE pg_thesis
					SET modify_by = '$user_id', modify_date = '$curdatetime', ref_thesis_status_id_proposal = 'INP'
					WHERE id = '$thesis_id'";
					
				$db_klas2->query($sql2);
				
				$sql3 = "UPDATE pg_proposal 		
						SET archived_status = 'ARC', archived_date = '$curdatetime' 
						WHERE id = '$pgProposalId'
						AND verified_status in ('REQ','CAN')
						AND status = 'OPN'
						AND archived_status is null";

				$db_klas2->query($sql3);

				// --- Job Area Category (START) ---
				$selectArea = "SELECT * FROM pg_proposal_area
				WHERE pg_proposal_id = '$pgProposalId' ";
				$db_klas2->query($selectArea); //echo $updateArea;
				
				$result_selectArea = $db_klas2->query($selectArea);	
				$row_cnt = mysql_num_rows($result_selectArea);	
				
				if ($row_cnt > 0) {

					$job_area_id = runnum2('id','pg_proposal_area');
					$insertArea = "INSERT INTO pg_proposal_area
					(id, pg_proposal_id, job_id1_area, job_id2_area, job_id3_area, job_id4_area, job_id5_area, job_id6_area, insert_date, insert_by,
					modified_date, modified_by)
					VALUES('$job_area_id', '$new_proposal_id', '$myjobs_area1', '$myjobs_area2', '$myjobs_area3', '$myjobs_area4', 
					'$myjobs_area5', '$myjobs_area6', '$curdatetime', '".$_SESSION['user_id']."', '$curdatetime', '".$_SESSION['user_id']."')";
					$db_klas2->query($insertArea);
				}

				// --- Job Area Category (FINISH) ---
				
				$sqlMeeting2 = "UPDATE pg_meeting_detail
						SET pg_proposal_id =  '$new_proposal_id'
						WHERE pg_proposal_id = '$proposal_id'";

				$result_sqlMeeting2 = $dbg->query($sqlMeeting2); 
				
					
				
				$sqlUpload2 = "select fu_cd
							FROM file_upload_proposal
							WHERE pg_proposal_id = '$proposal_id'
							ORDER BY fu_cd";
				
				$result_sqlUpload2 = $dbg->query($sqlUpload2); 			

				$row_cnt = mysql_num_rows($result_sqlUpload2);
				if ($row_cnt>0) {							
					while ($dbg->next_record()) {			
						$fuCdTmp=$dbg->f('fu_cd');
						
						$sqlUpload3 = "UPDATE file_upload_proposal 
									set pg_proposal_id = '$new_proposal_id'
								WHERE fu_cd = '$fuCdTmp'";
						
						$result_sqlUpload3 = $dba->query($sqlUpload3);
						$dba->next_record();			
					};		
				} 
				
				for ($i=0; $i<sizeof($_POST['date']); $i++) {
			
					$meeting_detail_id = runnum2('id','pg_meeting_detail');	
					
					$myMeetingDate = $_POST['date'][$i]." ".$_POST['meeting_time'][$i];
					$myLecturer = mysql_real_escape_string($_POST['lecturer_name'][$i]);
					$myRemarks = mysql_real_escape_string($_POST['remarks'][$i]);
					$sqlMeeting = "INSERT INTO pg_meeting_detail(
									id, 
									lecturer_name,
									meeting_sdate,
									remark,
									pg_proposal_id,
									pg_thesis_progress_id,
									insert_by,
									insert_date,
									modify_by,
									modify_date)
									VALUES (
									'$meeting_detail_id',
									'$myLecturer',
									STR_TO_DATE('$myMeetingDate','%m-%d-%Y %h:%i'), 
									'$myRemarks',
									'$new_proposal_id',
									null,
									'$user_id',
									'$curdatetime',
									'$user_id',
									'$curdatetime')";
					$db_klas2->query($sqlMeeting); 
				}
				
				for ($i=0; $i<sizeof($_FILES['fileData']['name']); $i++) {
						
					$upload_id = runnum2('fu_cd','file_upload_proposal');
					
					$file_name = $_FILES['fileData']['name'][$i];
					$fileType = $_FILES['fileData']['type'][$i];
					$fileSize = intval($_FILES['fileData']['size'][$i]);
					$fileData = file_get_contents($_FILES['fileData']['tmp_name'][$i]);
					
							
					$sqlUpload = "INSERT INTO file_upload_proposal (
										fu_cd, 
										fu_document_filename, 
										fu_document_filetype, 
										fu_document_filedata,
										fu_document_thumbnail,
										insert_by,
										insert_date,
										modify_by,
										modify_date,
										pg_proposal_id,
										attachment_level)
										VALUES (
										'$upload_id',
										'".$file_name."', 
										'".$fileType."',
										'".mysql_escape_string($fileData)."',
										'',
										'$user_id',
										'$curdatetime',
										'$user_id',
										'$curdatetime',
										'$new_proposal_id','S')";
							$db_klas2->query($sqlUpload);
				}
				
			}
		}	
		
		$msg[] = "<div class=\"success\"><span>The thesis proposal has been saved successfully.</span></div>";
	} 		
}

if(isset($_POST['btnSubmit']) && ($_POST['btnSubmit'] <> ""))
{
	$msg = array();
	if (empty($_POST['thesis_title'])) $msg[] = "<div class=\"error\"><span>Please enter Thesis Title</span></div>";
	if (empty($_POST['introduction'])) $msg[] = "<div class=\"error\"><span>Please enter Introduction</span></div>";
	if (empty($_POST['objective'])) $msg[] = "<div class=\"error\"><span>Please enter objective</span></div>";
	if (empty($_POST['description'])) $msg[] = "<div class=\"error\"><span>Please enter Description</span></div>";
	if ($_POST['jobs1_area'] == "") $msg[] = "<div class=\"error\"><span>Please select Thesis Area</span></div>";
	
	if(empty($msg)) 
	{
		/*$myjobs_area1 = $_POST['jobs1_area'];
		$myjobs_area2 = $_POST['jobs2_area'];
		$myjobs_area3 = $_POST['jobs3_area'];
		$myjobs_area4 = $_POST['jobs4_area'];
		$myjobs_area5 = $_POST['jobs5_area'];
		$myjobs_area6 = $_POST['jobs6_area'];*/
		
		$myjobs_area1 = $_REQUEST['JobAreaID'];
		$myjobs_area2 = $_REQUEST['JobAreaID1'];
		$myjobs_area3 = $_REQUEST['JobAreaID2'];
		$myjobs_area4 = $_REQUEST['JobAreaID3'];
		$myjobs_area5 = $_REQUEST['JobAreaID4'];
		$myjobs_area6 = $_REQUEST['JobAreaID5'];


		$curdatetime = date("Y-m-d H:i:s");	
		
		$sql2 ="SELECT pp.id as proposal_id, pt.id as thesis_id, pp.verified_status,ppa.*
					FROM pg_thesis pt 
					LEFT JOIN pg_proposal pp ON (pp.pg_thesis_id=pt.id) 
					LEFT JOIN ref_proposal_status rps ON (rps.id = pp.verified_status)  
					LEFT JOIN pg_proposal_area ppa ON (ppa.pg_proposal_id=pp.id)
					WHERE pt.student_matrix_no = '$user_id'
					AND pp.verified_status in ('SAV','REQ','CAN')				
					AND pp.archived_status is null
					AND pt.ref_thesis_status_id_proposal = 'INP'
					ORDER BY pt.id";
					
		$result_sql2 = $db->query($sql2);
		$row_area = $db->fetchArray(); 
		$thesis_id=$row_area['thesis_id'];
		$proposal_id=$row_area['proposal_id'];
		$verified_status=$row_area['verified_status'];
		$jobs_area1=$row_area['job_id1_area'];
		$jobs_area2=$row_area['job_id2_area'];
		$jobs_area3=$row_area['job_id3_area'];
		$jobs_area4=$row_area['job_id4_area'];
		$jobs_area5=$row_area['job_id5_area'];
		$jobs_area6=$row_area['job_id6_area'];
		
		$row_cnt = mysql_num_rows($result_sql2); 
		if ($row_cnt==0) //no record
		{		
			$thesis_id = "T".runnum('id','pg_thesis');
			$proposal_id = "P".runnum('id','pg_proposal');
			
			$sqlsubmit2="INSERT INTO pg_thesis(id,status, student_matrix_no,insert_by,insert_date,
						modify_by,modify_date,ref_thesis_status_id_proposal)
						VALUES('$thesis_id','INP','$user_id','$user_id','$curdatetime','$user_id','$curdatetime','INP') ";

			$db_klas2->query($sqlsubmit2); 
			 
			$sqlsubmit="INSERT INTO pg_proposal
					(id,thesis_title,thesis_type,introduction,objective,description,discussion_status,
					pg_thesis_id, verified_status, status, report_date, insert_by,insert_date,modify_by,modify_date)
					VALUES('$proposal_id','".mysql_real_escape_string($thesis_title)."','$thesis_type','$introduction','$objective','$description',
					'$discussion_status','$thesis_id','INP', 'OPN', '$curdatetime','$user_id',
					'$curdatetime','$user_id','$curdatetime') ";
					
			$db_klas2->query($sqlsubmit); 
			
			$sql7_2 = "UPDATE pg_thesis
						SET archived_date = '$curdatetime', archived_status = 'ARC',
						modify_by = '$userid', modify_date = '$curdatetime'
						WHERE id = '$pgThesisId'
						AND student_matrix_no = '$studentMatrixNo'";

						$dbg->query($sql7_2);
						
			$sql3 = "UPDATE pg_proposal 		
						SET archived_status = 'ARC', archived_date = '$curdatetime' 
						WHERE id = '$pgProposalId'
						AND verified_status in ('REQ','CAN')
						AND status = 'OPN'
						AND archived_status is null";

			$db_klas2->query($sql3);
			
			for ($i=0; $i<sizeof($_POST['date']); $i++) {
			
					$meeting_detail_id = runnum2('id','pg_meeting_detail');	
					
					$myMeetingDate = $_POST['date'][$i]." ".$_POST['meeting_time'][$i];
					$myLecturer = mysql_real_escape_string($_POST['lecturer_name'][$i]);
					$myRemarks = mysql_real_escape_string($_POST['remarks'][$i]);
					$sqlMeeting = "INSERT INTO pg_meeting_detail(
									id, 
									lecturer_name,
									meeting_sdate,
									remark,
									pg_proposal_id,
									pg_thesis_progress_id,
									insert_by,
									insert_date,
									modify_by,
									modify_date)
									VALUES (
									'$meeting_detail_id',
									'$myLecturer',
									STR_TO_DATE('$myMeetingDate','%m-%d-%Y %h:%i'), 
									'$myRemarks',
									'$proposal_id',
									null,
									'$user_id',
									'$curdatetime',
									'$user_id',
									'$curdatetime')";
					$db_klas2->query($sqlMeeting); 
				}
				
				for ($i=0; $i<sizeof($_FILES['fileData']['name']); $i++) {
						
					$upload_id = runnum2('fu_cd','file_upload_proposal');
					
					$file_name = $_FILES['fileData']['name'][$i];
					$fileType = $_FILES['fileData']['type'][$i];
					$fileSize = intval($_FILES['fileData']['size'][$i]);
					$fileData = file_get_contents($_FILES['fileData']['tmp_name'][$i]);
					
							
					$sqlUpload = "INSERT INTO file_upload_proposal (
										fu_cd, 
										fu_document_filename, 
										fu_document_filetype, 
										fu_document_filedata,
										fu_document_thumbnail,
										insert_by,
										insert_date,
										modify_by,
										modify_date,
										pg_proposal_id,
										attachment_level)
										VALUES (
										'$upload_id',
										'".$file_name."', 
										'".$fileType."',
										'".mysql_escape_string($fileData)."',
										'',
										'$user_id',
										'$curdatetime',
										'$user_id',
										'$curdatetime',
										'$proposal_id','S')";
							$db_klas2->query($sqlUpload);
				}

		}
		else 
		{
			if ($verified_status=='SAV')
			{
				$sqlUpdate1 = "UPDATE pg_thesis
						SET modify_by = '$user_id', modify_date = '$curdatetime', ref_thesis_status_id_proposal = 'INP'
						WHERE id = '$thesis_id'";
						
				$db_klas2->query($sqlUpdate1);
			
				$sqlUpdate2 = "UPDATE pg_proposal
						SET thesis_title = '".mysql_real_escape_string($thesis_title)."', thesis_type = '$thesis_type', introduction = '$introduction', 
						objective = '$objective', description = '$description', modify_by = '$user_id', modify_date = '$curdatetime',
						discussion_status = '$discussion_status', verified_status = 'INP', report_date = '$curdatetime'
						WHERE id = '$proposal_id'
						AND verified_status in ('SAV','REQ','CAN')";

				$db_klas2->query($sqlUpdate2);
				
				$selectArea = "SELECT * FROM pg_proposal_area
				WHERE pg_proposal_id = '$proposal_id' ";
				$db_klas2->query($selectArea); //echo $updateArea;
				
				$result_selectArea = $db_klas2->query($selectArea);	
				$row_cnt = mysql_num_rows($result_selectArea);	
				
				if ($row_cnt > 0) {

					$updateArea = "UPDATE pg_proposal_area
					SET job_id1_area = '$myjobs_area1', job_id2_area = '$myjobs_area2', job_id3_area = '$myjobs_area3',
						job_id4_area = '$myjobs_area4',job_id5_area = '$myjobs_area5',job_id6_area = '$myjobs_area6',
						insert_date = '$curdatetime', insert_by = '".$_SESSION['user_id']."', modified_date = '$curdatetime', modified_by = '".$_SESSION['user_id']."'
					WHERE pg_proposal_id = '$proposal_id' ";
					$db_klas2->query($updateArea); //echo $updateArea;
				}
				else {
					$insertArea = "INSERT INTO pg_proposal_area
					(id, pg_proposal_id, job_id1_area, job_id2_area, job_id3_area, job_id4_area, job_id5_area, job_id6_area, insert_date, insert_by,
					modified_date, modified_by)
					VALUES('$id', '$proposal_id', '$myjobs_area1', '$myjobs_area2', '$myjobs_area3', '$myjobs_area4', '$myjobs_area5', '$myjobs_area6',
					'$curdatetime', '".$_SESSION['user_id']."', '$curdatetime', '".$_SESSION['user_id']."')";
					$db_klas2->query($insertArea);
				}
				
				for ($i=0; $i<sizeof($_POST['date']); $i++) {
			
					$meeting_detail_id = runnum2('id','pg_meeting_detail');	
					
					$myMeetingDate = $_POST['date'][$i]." ".$_POST['meeting_time'][$i];
					$myLecturer = mysql_real_escape_string($_POST['lecturer_name'][$i]);
					$myRemarks = mysql_real_escape_string($_POST['remarks'][$i]);
					$sqlMeeting = "INSERT INTO pg_meeting_detail(
									id, 
									lecturer_name,
									meeting_sdate,
									remark,
									pg_proposal_id,
									pg_thesis_progress_id,
									insert_by,
									insert_date,
									modify_by,
									modify_date)
									VALUES (
									'$meeting_detail_id',
									'$myLecturer',
									STR_TO_DATE('$myMeetingDate','%m-%d-%Y %h:%i'), 
									'$myRemarks',
									'$proposal_id',
									null,
									'$user_id',
									'$curdatetime',
									'$user_id',
									'$curdatetime')";
					$db_klas2->query($sqlMeeting); 
				}
				
				for ($i=0; $i<sizeof($_FILES['fileData']['name']); $i++) {
						
					$upload_id = runnum2('fu_cd','file_upload_proposal');
					
					$file_name = $_FILES['fileData']['name'][$i];
					$fileType = $_FILES['fileData']['type'][$i];
					$fileSize = intval($_FILES['fileData']['size'][$i]);
					$fileData = file_get_contents($_FILES['fileData']['tmp_name'][$i]);
					
							
					$sqlUpload = "INSERT INTO file_upload_proposal (
										fu_cd, 
										fu_document_filename, 
										fu_document_filetype, 
										fu_document_filedata,
										fu_document_thumbnail,
										insert_by,
										insert_date,
										modify_by,
										modify_date,
										pg_proposal_id,
										attachment_level)
										VALUES (
										'$upload_id',
										'".$file_name."', 
										'".$fileType."',
										'".mysql_escape_string($fileData)."',
										'',
										'$user_id',
										'$curdatetime',
										'$user_id',
										'$curdatetime',
										'$proposal_id','S')";
							$db_klas2->query($sqlUpload);
				}
				
			}
			else //REQ or CAN
			{
				$new_proposal_id = "P".runnum('id','pg_proposal');
			
				$sql1="INSERT INTO pg_proposal
					(id,thesis_title,thesis_type,introduction,objective,description,discussion_status,
					pg_thesis_id, verified_status, status, report_date, insert_by,insert_date,modify_by,modify_date)
					VALUES('$new_proposal_id','".mysql_real_escape_string($thesis_title)."','$thesis_type','$introduction','$objective','$description',
					'$discussion_status',
					'$thesis_id','INP', 'OPN', '$curdatetime','$user_id','$curdatetime','$user_id','$curdatetime') ";
					
				$db_klas2->query($sql1); 
			
				$sql2 = "UPDATE pg_thesis
					SET modify_by = '$user_id', modify_date = '$curdatetime', ref_thesis_status_id_proposal = 'INP'
					WHERE id = '$thesis_id'";
					
				$db_klas2->query($sql2);

				$sql3 = "UPDATE pg_proposal 		
						SET archived_status = 'ARC', archived_date = '$curdatetime' 
						WHERE id = '$pgProposalId'
						AND verified_status in ('REQ','CAN')
						AND status = 'OPN'
						AND archived_status is null";

				$db_klas2->query($sql3);
			
				// --- Job Area Category (START) ---
				$selectArea = "SELECT * FROM pg_proposal_area
				WHERE pg_proposal_id = '$pgProposalId' ";
				$db_klas2->query($selectArea); //echo $updateArea;
				
				$result_selectArea = $db_klas2->query($selectArea);	
				$row_cnt = mysql_num_rows($result_selectArea);	
				
				if ($row_cnt > 0) {

					$job_area_id = runnum2('id','pg_proposal_area');
					$insertArea = "INSERT INTO pg_proposal_area
					(id, pg_proposal_id, job_id1_area, job_id2_area, job_id3_area, job_id4_area, job_id5_area, job_id6_area, insert_date, insert_by,
					modified_date, modified_by)
					VALUES('$job_area_id', '$new_proposal_id', '$myjobs_area1', '$myjobs_area2', '$myjobs_area3', '$myjobs_area4', 
					'$myjobs_area5', '$myjobs_area6', '$curdatetime', '".$_SESSION['user_id']."', '$curdatetime', '".$_SESSION['user_id']."')";
					$db_klas2->query($insertArea);
				}

				// --- Job Area Category (FINISH) ---
				
				$sql6_3 = "select fu_cd
						FROM file_upload_proposal
						WHERE pg_proposal_id = '$pgProposalId'
						ORDER BY fu_cd";
			
				$result6_3 = $dbg->query($sql6_3); 			

				$row_cnt = mysql_num_rows($result6_3);
				if ($row_cnt>0) {							
					while ($dbg->next_record()) {			
						$fuCdTmp=$dbg->f('fu_cd');
						
						$sql6_4 = "UPDATE file_upload_proposal 
									set pg_proposal_id = '$new_proposal_id'
								WHERE fu_cd = '$fuCdTmp'";
						
						$result6_4 = $dba->query($sql6_4);
						$dba->next_record();			
					};
							
				}
				
				$sql6_4 = "UPDATE pg_meeting_detail
							SET pg_proposal_id =  '$new_proposal_id'
							WHERE pg_proposal_id = '$pgProposalId'";
				
				$result6_4 = $dbg->query($sql6_4);
			
				for ($i=0; $i<sizeof($_POST['date']); $i++) {
			
					$meeting_detail_id = runnum2('id','pg_meeting_detail');	
					$myMeetingDate = $_POST['date'][$i]." ".$_POST['meeting_time'][$i];
					$myLecturer = mysql_real_escape_string($_POST['lecturer_name'][$i]);
					$myRemarks = mysql_real_escape_string($_POST['remarks'][$i]);
					$sqlMeeting = "INSERT INTO pg_meeting_detail(
									id, 
									lecturer_name,
									meeting_sdate,
									remark,
									pg_proposal_id,
									pg_thesis_progress_id,
									insert_by,
									insert_date,
									modify_by,
									modify_date)
									VALUES (
									'$meeting_detail_id',
									'$myLecturer',
									STR_TO_DATE('$myMeetingDate','%m-%d-%Y %h:%i'), 
									'$myRemarks',
									'$new_proposal_id',
									null,
									'$user_id',
									'$curdatetime',
									'$user_id',
									'$curdatetime')";
					$db_klas2->query($sqlMeeting); 
				}
				
				for ($i=0; $i<sizeof($_FILES['fileData']['name']); $i++) {
						
					$upload_id = runnum2('fu_cd','file_upload_proposal');
					
					$file_name = $_FILES['fileData']['name'][$i];
					$fileType = $_FILES['fileData']['type'][$i];
					$fileSize = intval($_FILES['fileData']['size'][$i]);
					$fileData = file_get_contents($_FILES['fileData']['tmp_name'][$i]);
					
							
					$sqlUpload = "INSERT INTO file_upload_proposal (
										fu_cd, 
										fu_document_filename, 
										fu_document_filetype, 
										fu_document_filedata,
										fu_document_thumbnail,
										insert_by,
										insert_date,
										modify_by,
										modify_date,
										pg_proposal_id,
										attachment_level)
										VALUES (
										'$upload_id',
										'".$file_name."', 
										'".$fileType."',
										'".mysql_escape_string($fileData)."',
										'',
										'$user_id',
										'$curdatetime',
										'$user_id',
										'$curdatetime',
										'$new_proposal_id','S')";
							$db_klas2->query($sqlUpload);
				}

			}
		}
		$msg[] = "<div class=\"success\"><span>The thesis proposal has been submitted successfully to the Faculty for verification.</span></div>";
	
		$selectfrom = "SELECT const_value
		FROM base_constant WHERE const_term = 'EMAIL_ADMIN'";
		$resultfrom = $db->query($selectfrom);
		$db->next_record();
		$fromadmin =$db->f('const_value');
		
		$selectto = "SELECT const_value
		FROM base_constant WHERE const_term = 'EMAIL_FACULTY'";
		$resultto = $dbb->query($selectto);
		$dbb->next_record();
		$tofaculty =$dbb->f('const_value');
		
		$selectfaculty = "SELECT const_value
		FROM base_constant WHERE const_term = 'FACULTY_STAFF_ID'";
		$resultselectfaculty = $dbe->query($selectfaculty);
		$dbe->next_record();
		$selectidfaculty =$dbe->f('const_value');
		
		$sqlemail = "SELECT email FROM student
		WHERE `matrix_no` = '$user_id'";
		$resultreceive = $dbk->query($sqlemail);
		$resultsqlreceive = $dbk->next_record(); 
		$receiveemail = $dbk->f('email');
		
		$selectfalname = "SELECT a.name,b.title 
		FROM new_employee a
		LEFT JOIN lookup_gelaran b ON(b.id = a.title)
		WHERE a.empid = '$selectidfaculty'";
		$resultselfalname = $dbc->query($selectfalname);
		$dbc->next_record();
		$selectname =$dbc->f('name');
		$title =$dbc->f('title');


		$curdatetime1 = date("d-m-Y");	
							
		$username = convertname($user_id);
		if ($thesis_type =  "R")
		{
			$type = "Research";
			
		}
		else if ($thesis_type =  "C")
		{
			$type = "Case Study";

		}
		else
		{
			$type = "Project";
		}
	
		$selectattachment= "SELECT *
		FROM file_upload_proposal WHERE student_matrix_no = '$user_id' 
		AND pg_proposal_id = '$new_proposal_id'";
		$resultattachment = $db->query($selectattachment);
		
		while($db->next_record())
		{
			$rowData = $db->rowdata();
			$FileName[] = $rowData['fu_document_filename'];
			$FileType[] = $rowData['fu_document_filetype'];
			$attachmentdata[] = $rowData['fu_document_filedata'];		 
		}					 
		$sqlvalidate = "SELECT const_value
		FROM base_constant WHERE const_term = 'EMAIL_STU_TO_FAC'";
		$resultvalidate = $dbd->query($sqlvalidate);
		$dbd->next_record();
		$valid =$dbd->f('const_value');
		if($valid == 'Y')
		{
			include("../../../app/application/email/email_new_proposal.php");
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
	<link rel="stylesheet" href="../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
	<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
	<script src="../../../lib/js/jquery.min2.js"></script>
    <script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
	<script src="../../../lib/js/jquery.colorbox.js"></script>
    <script>

		$(document).ready(function(){
		      //$(".select_user").colorbox({width:"60%", height:"40%", iframe:true});
              
              $.fn.getParameterValue = function(data) {
                  //alert(data);
                  document.form1.JobArea.value = data;
                };
              
               $(".select_job").colorbox({width:"80%", height:"90%", iframe:true,          
               onClosed:function(){ 
                //location.reload(true); //uncomment this line if you want to refresh the page when child close
                                
                } }); 
				
				$.fn.getParameterValue2 = function(data,data2,data3,data4) {
                  //alert(data);
                  //document.form1.JobArea.value = data;
				  $("#"+data2).val(data);
				  $("#"+data4).val(data3);

                };
              
               $(".select_job").colorbox({width:"80%", height:"90%", iframe:true,          
               onClosed:function(){ 
                //location.reload(true); //uncomment this line if you want to refresh the page when child close
                                
                } }); 
          });

	</script>

	
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
<script>
function saveRec()
{
	saveStatus=true;
	var introduction = CKEDITOR.instances['introduction'].getData();
	var objective = CKEDITOR.instances['objective'].getData();
	var description = CKEDITOR.instances['description'].getData();
	
	if(document.form1.thesis_title.value=="")
	{
		alert("Please enter your Thesis / Project title.");
		return false;
	}
	
	if(document.form1.thesis_type.value=="")
	{
		alert("Please enter your Thesis / Project proposal type.");
		return false;
	}
	
	if(introduction.length==0)
	{
		alert("Please enter your thesis / project introduction.");
		return false;
	}
	
	if(objective.length==0)
	{
		alert("Please enter your thesis / project objective.");
		return false;
	}
	
	if(description.length==0)
	{
		alert("Please enter your thesis / project description");
		return false;
	}
	
	/*if((document.f1.semid_end.value!="") && (document.f1.semid.value>document.f1.semid_end.value))
	{
		alert("Semester Start cannot greater than Semester End.");
		return false;
	}
	
	if((document.f1.end_date.value!="") && (document.f1.start_date.value>document.f1.end_date.value))
	{
		alert("Start Date cannot greater than End Date.");
		return false;
	}*/
	/*var confirmSubmit = confirm("Are you confirm to submit?");
	if (confirmSubmit==true)
	{
		return saveStatus;
	}
	if (confirmSubmit==false)
	{
		return false;
	}*/
	return saveStatus;
}					
</script>


<SCRIPT LANGUAGE="JavaScript">

function respConfirm () {
    var confirmSubmit = confirm("Click OK if you confirm to submit or CANCEL if to stay on the same page?");
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
$(function() {
	$( "#datepickerFirst" ).datepicker({
		changeMonth: true,
		changeYear: true,
		yearRange: '-100:+0',
		dateFormat: 'dd-mm-yy'
		});
});
</script>

<script type="text/javascript">
$(function() {

	var i = $('input').size() + 1;
	
	//###################################### funtion add more document files ######################################//
	$('a.add-file').click(function() {

		$('<tr><td align="center"><input type="checkbox" class="case_file" /></td><td align=\"center\"><label name=\"file_name[]\" size="40" ></label></td><td align="center"><input name="fileData[]" type="file" size="40"/></td></tr>').animate({ opacity: "show" }, "slow").appendTo('#inputs9');
		i++;
	});
	
	$('a.remove-file').click(function() {
        $(".case_file:checked").each(function() {
            $(this).parent().parent().remove()
        });
    });
    
    // add multiple select / deselect functionality
    $("#selectall_file").click(function () {
          $('.case_file').attr('checked', this.checked);
    });
 
    // if all checkbox are selected, check the selectall checkbox and viceversa
    $(".case_file").click(function(){
 
        if($(".case_file").length == $(".case_file:checked").length) {
            $("#selectall_file").attr("checked", "checked");
        } else {
            $("#selectall_file").removeAttr("checked");
        }
 
    });
	
	//###################################### end of funtion add more document files ######################################//
	
});

</script>	

<script type="text/javascript">
$(function() 
{
	var i = $('input').size() + 1;
	
	//###################################### funtion add more @ certification ######################################//
	$('a.add-certification').click(function() {

	$('<tr><td align="center"><input type="checkbox" name="box[]" class="case_certificate" /></td><td><input type="text" name="date[]" size="15" readonly id="datepicker'+i+'" /></td><td width="66" height="1" class="tbmain"><select name="meeting_time[]" size="1" ><option value=\"\"></option><option value="07:00">07:00 AM</option><option value="07:30">07:30 AM</option><option value="08:00">08:00 AM</option><option value="08:30">08:30 AM</option><option value="09:00">09:00 AM</option><option value="09:30">09:30 AM</option><option value="10:00">10:00 AM</option><option value="10:30">10:30 AM</option><option value="11:00">11:00 AM</option><option value="11:30">11:30 AM</option><option value="12:00">12:00 PM</option><option value="12:30">12:30 PM</option><option value="13:00">01:00 PM</option><option value="13:30">01:30 PM</option><option value="14:00">02:00 PM</option><option value="14:30">02:30 PM</option><option value="15:00">03:00 PM</option><option value="15:30">03:30 PM</option><option value="16:00">04:00 PM</option><option value="16:30">04:30 PM</option><option value="17:00">05:00 PM</option><option value="17:30">05:30 PM</option><option value="18:00">06:00 PM</option><option value="18:30">06:30 PM</option><option value="19:00">07:00 PM</option><option value="19:30">07:30 PM</option><option value="20:00">08:00 PM</option><option value="20:30">08:30 PM</option><option value="21:00">09:00 PM</option><option value="21:30">09:30 PM</option><option value="22:00">10:00 PM</option><option value="22:30">10:30 PM</option><option value="23:00">11:00 PM</option><option value="23:30">11:30 PM</option><option value="24:00">12:00 PM</option></select></td><td><input type="text" name="lecturer_name[]" size="30" /></td><td><input type="text" name="remarks[]" size="50" id="remarks" /></td></tr>').animate({ opacity: "show" }, "slow").appendTo('#inputs10');

	$("#datepicker"+i).datepicker({
		changeMonth: true,
		changeYear: true,
		yearRange: '-100:+0',
		dateFormat: 'dd-mm-yy'
		});

		i++;		
	});
    
	
	$('a.remove-certification').click(function() {
        $(".case_certificate:checked").each(function() {
            $(this).parent().parent().remove()
        });	
    });
    
    // add multiple select / deselect functionality
    $("#selectall_certificate").click(function () 
	{
          $('.case_certificate').attr('checked', this.checked);
    });
 
    // if all checkbox are selected, check the selectall checkbox and viceversa
    $(".case_certificate").click(function()
	{
		if($(".case_certificate").length == $(".case_certificate:checked").length) 
		{
            $("#selectall_certificate").attr("checked", "checked");
        } else {
            $("#selectall_certificate").removeAttr("checked");
        }
    });
	//###################################### end of funtion add more @ certification ######################################//
});


</script>


  <form id="form1" name="form1" method="post" enctype="multipart/form-data" onsubmit="return saveRec();">

<?
$sql_thesis="SELECT pt.id AS thesis_id, pt.student_matrix_no,pt.status as thesis_status,
				pp.id AS proposal_id, pp.thesis_title,pp.thesis_type, pp.objective, pp.introduction,pp.description,pp.discussion_status, 
				DATE_FORMAT(pp.verified_date,'%d-%b-%Y') AS verified_date, pp.verified_remarks, pp.verified_by,
				pp.verified_status,pp.endorsed_by, DATE_FORMAT(pp.endorsed_date,'%d-%m-%y') as endorsed_date, 
				pp.endorsed_remarks, pp.status as endorsement_status,
				rps.description AS proposal_description
				FROM pg_thesis pt 
				LEFT JOIN pg_proposal pp ON (pp.pg_thesis_id=pt.id) 
				LEFT JOIN ref_proposal_status rps ON (rps.id = pp.verified_status)  
				WHERE pt.student_matrix_no = '$user_id'
				AND pp.verified_status in ('SAV','INP','REQ','CAN')				
				AND pp.archived_status is null
				AND pt.ref_thesis_status_id_proposal = 'INP'
				ORDER BY pt.id";
				
	$result_sql_thesis = $db->query($sql_thesis);
	$row_area = $db->fetchArray();
	
	$thesis_id=$row_area['thesis_id'];
	$proposal_id=$row_area['proposal_id'];
	$thesis_title=$row_area['thesis_title'];
	$thesis_type=$row_area['thesis_type'];
	$introduction=$row_area['introduction'];
	$objective=$row_area['objective'];
	$description=$row_area['description'];
	$discussion_status=$row_area['discussion_status'];
	$verified_date=$row_area['verified_date'];
	$verified_remarks=$row_area['verified_remarks'];
	$verified_status=$row_area['verified_status'];
	$proposal_description=$row_area['proposal_description'];
	
if (strcmp($verified_status,'INP')!=0)
{
?>
<table>
	<tr>
		<td><strong>Notes:</strong><td></td><td>
	</tr>
	<tr>
		<td>(1)</td><td>This form should be submitted to MSU Graduate School of Management (GSM) upon completing of the Research Methodology and before student starts the project.</td>
	</tr>
	<tr>
		<td>(2)</td><td>Students are advised to seek the lecturer's advice before proceeding with the proposal.</td>
	</tr>
	<tr>
		<td>(3)</td><td>Student should plan on 6-month's time from the Official Approval Date to complete the Final Project.</td>
	</tr>
	<tr>
		<td>(4)</td><td>As refer to MBA rules, No candidate with CGPA below 3.0 shall be eligible to register for the Final Project of the degree unless recommended by the Board of Examiners.</td>
	</tr>
	<tr>
		<td>(5)</td><td>Appointment of supervisor is subject to the recommendation from the Director of MSU Graduate School of Management (GSM).</td>
	</tr>				
</table>
<br/>			
<fieldset>
<legend><strong>Edit Application - Outline of Proposed Research/Case Study</strong></legend>	
	<table>
		<tr>
			<td>Thesis / Project ID</td>
			<td><label type="text" name="thesis_id" size="15" id="thesis_id" ><?=$thesis_id;?></label></td>
		</tr>
		<tr>
			<td><span style="color:#FF0000">*</span> Thesis / Project Title</td>
			<td><input type="text" name="thesis_title" size="100" maxlength="100"  id="thesis_title" value="<?=$thesis_title;?>"></td>
		</tr>
		<tr>
			<td>Proposal Type</td>
			<td>
				<?php if($thesis_type=='R')	{	?>
				<input type="radio" name="thesis_type" value="R" checked>Research
				<input type="radio" name="thesis_type" value="C">Case Study						
				<input type="radio" name="thesis_type" value="P">Project	
				<?php	}	else if ($thesis_type=='C')	{	?>
					<input type="radio" name="thesis_type" value="R">Research
					<input type="radio" name="thesis_type" value="C" checked >Case Study
					<input type="radio" name="thesis_type" value="P">Project
				<?php	}	else if ($thesis_type=='P')	{	?>
					<input type="radio" name="thesis_type" value="R">Research
					<input type="radio" name="thesis_type" value="C">Case Study
					<input type="radio" name="thesis_type" value="P" checked>Project
				<?php	}	else {	?>
					<input type="radio" name="thesis_type" value="R" checked>Research
					<input type="radio" name="thesis_type" value="C">Case Study
					<input type="radio" name="thesis_type" value="P">Project
				<?php	}
				?> 
			</td>		
		</tr>
		<tr>
			<td><span style="color:#FF0000">*</span> Introduction</td>
			<td><textarea name="introduction" cols="30" class="ckeditor" rows="3" id="introduction" ><?=$introduction;?></textarea></td>
		</tr>
		<tr>
			<td><span style="color:#FF0000">*</span> Objective</td>
			<td><textarea name="objective" cols="30" class="ckeditor" rows="3" ><?=$objective;?></textarea></td>
		</tr>
		<tr>
			<td><span style="color:#FF0000">*</span> Brief Description</td>
			<td><textarea name="description" cols="30" class="ckeditor" rows="3" ><?=$description;?></textarea></td>
		</tr>
	</table>
	
	<input type="hidden" name="discussion_status" id="discussion_status" value="Y" />
	
		 
	  
	   <div>
	   
	   
	   <br />
	   <? $sqlPgProposalArea = "SELECT pg_proposal_id,job_id1_area,job_id2_area,job_id3_area,job_id4_area,job_id5_area,job_id6_area
	 										FROM pg_proposal_area
											WHERE pg_proposal_id = '$proposal_id'"; ?>									
	   <fieldset style="width:800px">
	   <legend><strong>Thesis Areas</strong></legend>	
	  
	  	<table width="928"  align="center">
		<tr>
			<td width="53" nowrap><font color="#FF0000">*</font><b>Area 1</b></td>
			<td width="294"><?php
			$rsPgProposalArea = $dbf->query($sqlPgProposalArea);						
			$dbf->next_record();
			$jobArea1=$dbf->f('job_id1_area');

		
			if ($jobArea1 == "") $jobArea1 = $_POST['JobArea'];
			$sqlLookupJobArea = "SELECT jobarea, area
								FROM job_list_category
								WHERE jobarea = '$jobArea1'";
			
			$rsLookupJobArea = $dbc->query($sqlLookupJobArea);						
			$dbc->next_record();
			$JobAreaID=$dbc->f('jobarea');
			$JobArea=$dbc->f('area');
			if($jobArea1 == $JobAreaID)
			{
			?><input id ="JobArea"  type="text" size="30" name="JobArea" readonly="" value="<?php echo isset($JobArea) ? $JobArea : "" ?>" />
			<a class='select_job' href="../../application/thesis/select_job.php?field=JobArea&field2=JobAreaID">[ Select ]</a>
			<input id ="JobAreaID"  type="hidden" size="30" name="JobAreaID" readonly="" value="<?php echo isset($JobAreaID) ? $JobAreaID : "" ?>" /></td><?
			 }
			 else
			 {?><input id ="JobArea"  type="text" size="30" name="JobArea" readonly="" value="<?php echo isset($JobArea) ? $JobArea : "" ?>" />
			<a class='select_job' href="../../application/thesis/select_job.php?field=JobArea&field2=JobAreaID">[ Select ]</a>
			<input id ="JobAreaID"  type="hidden" size="30" name="JobAreaID" readonly="" value="<?php echo isset($JobAreaID) ? $JobAreaID : "" ?>" /></td><?
			}?>		    
		<td width="10"></td>
        <td width="56"><b>Area 4</b></td>
			<td width="294"><?php
			$rsPgProposalArea = $dbf->query($sqlPgProposalArea);						
			$dbf->next_record();
			$jobArea3=$dbf->f('job_id4_area');
			if ($jobArea3 == "") $jobArea3 = $_POST['JobArea3'];
			$sqlLookupJobArea = "SELECT jobarea, area
								FROM job_list_category
								WHERE jobarea = '$jobArea3'";
			
			$rsLookupJobArea = $dbc->query($sqlLookupJobArea);						
			$dbc->next_record();
			$JobAreaID3=$dbc->f('jobarea');
			$JobArea3=$dbc->f('area');

			?><input id ="JobArea3"  type="text" size="30" name="JobArea3" readonly="" value="<?php echo isset($JobArea3) ? $JobArea3 : "" ?>" />
			<a class='select_job' href="../../application/thesis/select_job.php?field=JobArea3&field2=JobAreaID3">[ Select ]</a>
			<input id ="JobAreaID3"  type="hidden" size="30" name="JobAreaID3" readonly="" value="<?php echo isset($JobAreaID3) ? $JobAreaID3 : "" ?>" /></td><?
			 ?>		    
		</tr>
		<tr>
		<td ><b>Area 2</b></td>
			<td width="294"><?php
			$rsPgProposalArea = $dbf->query($sqlPgProposalArea);						
			$dbf->next_record();
			$jobArea1=$dbf->f('job_id2_area');	
		
			if ($jobArea1 == "") $jobArea1 = $_POST['JobArea1'];
			$sqlLookupJobArea = "SELECT jobarea, area
								FROM job_list_category
								WHERE jobarea = '$jobArea1'";
			
			$rsLookupJobArea = $dbc->query($sqlLookupJobArea);						
			$dbc->next_record();
			$JobAreaID1=$dbc->f('jobarea');
			$JobArea1=$dbc->f('area');

			?><input id ="JobArea1"  type="text" size="30" name="JobArea1" readonly="" value="<?php echo isset($JobArea1) ? $JobArea1 : "" ?>" />
			<a class='select_job' href="../../application/thesis/select_job.php?field=JobArea1&field2=JobAreaID1">[ Select ]</a>
			<input id ="JobAreaID1"  type="hidden" size="30" name="JobAreaID1" readonly="" value="<?php echo isset($JobAreaID1) ? $JobAreaID1 : "" ?>" /></td><?
			 ?>		    
		<td width="10"></td>
        <td nowrap><b>Area 5</b></td>
		<td width="294"><?php
			$rsPgProposalArea = $dbf->query($sqlPgProposalArea);						
			$dbf->next_record();
			$jobArea4=$dbf->f('job_id5_area');	
		
			if ($jobArea4 == "") $jobArea4 = $_POST['JobArea4'];
			$sqlLookupJobArea = "SELECT jobarea, area
								FROM job_list_category
								WHERE jobarea = '$jobArea4'";
			
			$rsLookupJobArea = $dbc->query($sqlLookupJobArea);						
			$dbc->next_record();
			$JobAreaID4=$dbc->f('jobarea');
			$JobArea4=$dbc->f('area');

			?><input id ="JobArea4"  type="text" size="30" name="JobArea4" readonly="" value="<?php echo isset($JobArea4) ? $JobArea4 : "" ?>" />
			<a class='select_job' href="../../application/thesis/select_job.php?field=JobArea4&field2=JobAreaID4">[ Select ]</a>
			<input id ="JobAreaID4"  type="hidden" size="30" name="JobAreaID4" readonly="" value="<?php echo isset($JobAreaID4) ? $JobAreaID4 : "" ?>" /></td><?
			 ?>		    
		</tr>
		
		<tr>
        <td ><b>Area 3</b></td>
		<td width="294"><?php
			$rsPgProposalArea = $dbf->query($sqlPgProposalArea);						
			$dbf->next_record();
			$jobArea2=$dbf->f('job_id3_area');	
		
			if ($jobArea2 == "") $jobArea2 = $_POST['JobArea2'];
			$sqlLookupJobArea = "SELECT jobarea, area
								FROM job_list_category
								WHERE jobarea = '$jobArea2'";
			
			$rsLookupJobArea = $dbc->query($sqlLookupJobArea);						
			$dbc->next_record();
			$JobAreaID2=$dbc->f('jobarea');
			$JobArea2=$dbc->f('area');

			?><input id ="JobArea2"  type="text" size="30" name="JobArea2" readonly="" value="<?php echo isset($JobArea2) ? $JobArea2 : "" ?>" />
			<a class='select_job' href="../../application/thesis/select_job.php?field=JobArea2&field2=JobAreaID2">[ Select ]</a>
			<input id ="JobAreaID2"  type="hidden" size="30" name="JobAreaID2" readonly="" value="<?php echo isset($JobAreaID2) ? $JobAreaID2 : "" ?>" /></td><?
			 ?>		    
            
        <td width="10"></td>
        <td width="56"><b>Area 6</b></td>
		<td width="294"><?php
			$rsPgProposalArea = $dbf->query($sqlPgProposalArea);						
			$dbf->next_record();
			$jobArea5=$dbf->f('job_id6_area');	
		
			if ($jobArea5 == "") $jobArea5 = $_POST['JobArea5'];
			$sqlLookupJobArea = "SELECT jobarea, area
								FROM job_list_category
								WHERE jobarea = '$jobArea5'";
			
			$rsLookupJobArea = $dbc->query($sqlLookupJobArea);						
			$dbc->next_record();
			$JobAreaID5=$dbc->f('jobarea');
			$JobArea5=$dbc->f('area');

			?><input id ="JobArea5"  type="text" size="30" name="JobArea5" readonly="" value="<?php echo isset($JobArea5) ? $JobArea5 : "" ?>" />
			<a class='select_job' href="../../application/thesis/select_job.php?field=JobArea5&field2=JobAreaID5">[ Select ]</a>
			<input id ="JobAreaID5"  type="hidden" size="30" name="JobAreaID5" readonly="" value="<?php echo isset($JobAreaID5) ? $JobAreaID5 : "" ?>" /></td><?
			 ?>		    
    	</tr>		
	   </table>
	   </fieldset>
	   </div>
	   <?
		$sqlMeeting="SELECT pmd.id,pmd.lecturer_name, DATE_FORMAT(pmd.meeting_sdate,'%d-%b-%Y') as date,
		DATE_FORMAT(pmd.meeting_sdate,'%H:%i') as time, pmd.remark, pmd.insert_by, pmd.insert_date 
		FROM  pg_meeting_detail pmd  
		WHERE pmd.pg_proposal_id='$proposal_id'
		AND student_matrix_no = '$user_id' 
		ORDER BY pmd.meeting_sdate DESC ";			

		$result = $db->query($sqlMeeting); 
		$row_cnt_discussion = mysql_num_rows($result);

		$sqlUpload="SELECT COUNT(*) as total  FROM file_upload_proposal 
		WHERE pg_proposal_id='$proposal_id' 
		AND student_matrix_no = '$user_id'
		AND attachment_level='S' ";			

		$result = $db->query($sqlUpload); 
		$db->next_record();
    	$a = $db->f('total');

		if($a == '0')
		{
			$a = '';
		}
		else
		{
			$a = $a;
		}
		
		if($row_cnt_discussion == '0')
		{
			$row_cnt_discussion = '';
		}
		else
		{
			$row_cnt_discussion = $row_cnt_discussion;
		}

	   ?>
		  <table>
			<tr>
				<td><button type="button" name="btnDiscussionDetail" onclick="javascript:document.location.href='../thesis/edit_proposal_discussion.php?tid=<?=$thesis_id?>&pid=<?=$proposal_id?>';">Discussion Detail <FONT COLOR="#FF0000"><sup>(<?=$row_cnt_discussion?>)</sup></FONT></button></td>
				<td><button type="button" name="btnAttachment" onclick="javascript:document.location.href='../thesis/edit_proposal_attachment.php?tid=<?=$thesis_id?>&pid=<?=$proposal_id?>';">Attachment <FONT COLOR="#FF0000"><sup>(<?=$a?>)</sup></FONT></button></td>		
			</tr>
		</table>
  </fieldset>
  <table>
		<tr>
			<td><input type="submit" name="btnSave" id="btnSave" align="center"  value="Save as Draft" /></td>
			<td><input type="submit" name="btnSubmit" id="btnSubmit" align="center"  value="Submit" onClick="return respConfirm()"/></td>
			<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../student/student_programme.php';" /></td>
			<td>Note: Field marks with (<span style="color:#FF0000">*</span>) is compulsory.</td>		
		</tr>
	</table>	
	<?}
else {
	
}?>
  </form>
</body>
</html>



