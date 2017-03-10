<?php

include("../../../lib/common.php");
checkLogin();

session_start();
$userid=$_REQUEST['uid'];
$studentMatrixNo=$_REQUEST['uid'];
$pgThesisId=$_REQUEST['tid'];
$pgProposalId=$_REQUEST['pid'];


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
	$myjobs_area1 = $_POST['jobs1_area'];
	$myjobs_area2 = $_POST['jobs2_area'];
	$myjobs_area3 = $_POST['jobs3_area'];
	$myjobs_area4 = $_POST['jobs4_area'];
	$myjobs_area5 = $_POST['jobs5_area'];
	$myjobs_area6 = $_POST['jobs6_area'];

	$curdatetime = date("Y-m-d H:i:s");	
	
	$sql1 ="SELECT pp.id as proposal_id, pt.id as thesis_id, pp.verified_status,ppa.*
				FROM pg_thesis pt 
				LEFT JOIN pg_proposal pp ON (pp.pg_thesis_id=pt.id) 
				LEFT JOIN ref_proposal_status rps ON (rps.id = pp.verified_status)  
				LEFT JOIN new_employee ne ON (ne.empid = pp.verified_by) 
				LEFT JOIN pg_proposal_area ppa ON (ppa.pg_proposal_id=pp.id)
				WHERE pt.student_matrix_no = '$user_id'
				AND (pp.verified_status in ('SAV','DIS') OR pp.status = 'DIS')
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
		$new_proposal_id = "P".runnum('id','pg_proposal');
		
		$sqlsubmit2="INSERT INTO pg_thesis(id,status, student_matrix_no,insert_by,insert_date,
					modify_by,modify_date,ref_thesis_status_id_proposal)
					VALUES('$thesis_id','INP','$user_id','$user_id','$curdatetime','$user_id','$curdatetime','INP') ";

		$db_klas2->query($sqlsubmit2); 
		 
		$sqlsubmit="INSERT INTO pg_proposal
				(id,thesis_title,thesis_type,introduction,objective,description,discussion_status,
				pg_thesis_id, verified_status, status, report_date, insert_by,insert_date,modify_by,modify_date)
				VALUES('$new_proposal_id','$thesis_title','$thesis_type','$introduction','$objective','$description',
				'$discussion_status','$thesis_id','SAV', 'OPN', '$curdatetime','$user_id','$curdatetime',
				'$user_id','$curdatetime') ";
				
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
					AND verified_status = 'APP'
					AND status = 'DIS'
					AND archived_status is null";

		$db_klas2->query($sql3);

		$selectArea = "SELECT * FROM pg_proposal_area
		WHERE pg_proposal_id = '$pgProposalId' ";
		$db_klas2->query($selectArea); //echo $updateArea;
		
		$result_selectArea = $db_klas2->query($selectArea);	
		$row_cnt = mysql_num_rows($result_selectArea);	
		
		if ($row_cnt == 0) {
			$job_area_id = runnum2('id','pg_proposal_area');
			$insertArea = "INSERT INTO pg_proposal_area
			(id, pg_proposal_id, job_id1_area, job_id2_area, job_id3_area, job_id4_area, job_id5_area, job_id6_area, insert_date, insert_by,
			modified_date, modified_by)
			VALUES('$job_area_id', '$new_proposal_id', '$myjobs_area1', '$myjobs_area2', '$myjobs_area3', '$myjobs_area4', '$myjobs_area5', 
			'$myjobs_area6', '$curdatetime', '".$_SESSION['user_id']."', '$curdatetime', '".$_SESSION['user_id']."')";
			$db_klas2->query($insertArea);
		}
		
		for ($i=0; $i<sizeof($_POST['date']); $i++) {
		
				$meeting_detail_id = runnum2('id','pg_meeting_detail');	
				
				$tmpDate = date_create($_POST['date'][$i]." ".$_POST['meeting_time'][$i]);
				$newDate = date_format($tmpDate, 'Y-m-d H:i:s');
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
								'" . $_POST['lecturer_name'][$i] . "',
								'" . $newDate ."', 
								'" . $_POST['remarks'][$i] . "',
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
	else //has record
	{
		if (strcmp($verified_status,'SAV')==0)
		{
			$sqlUpdate1 = "UPDATE pg_thesis
					SET modify_by = '$user_id', modify_date = '$curdatetime', ref_thesis_status_id_proposal = 'INP'
					WHERE id = '$thesis_id'";
					
			$db_klas2->query($sqlUpdate1);
		
			$sqlUpdate2 = "UPDATE pg_proposal
					SET thesis_title = '$thesis_title', thesis_type = '$thesis_type', introduction = '$introduction', 
					objective = '$objective', description = '$description', modify_by = '$user_id', modify_date = '$curdatetime',
					discussion_status = '$discussion_status', verified_status = 'SAV',
					report_date = '$curdatetime' 
					WHERE id = '$proposal_id'
					AND verified_status in ('SAV','REQ')";

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
					insert_date = '$curdatetime', insert_by = '".$_SESSION['user_id']."', modified_date = '$curdatetime', 
					modified_by = '".$_SESSION['user_id']."'
				WHERE pg_proposal_id = '$proposal_id' ";
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
			
			for ($i=0; $i<sizeof($_POST['date']); $i++) {
		
				$meeting_detail_id = runnum2('id','pg_meeting_detail');	
				
				$tmpDate = date_create($_POST['date'][$i]." ".$_POST['meeting_time'][$i]);
				$newDate = date_format($tmpDate, 'Y-m-d H:i:s');
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
								'" . $_POST['lecturer_name'][$i] . "',
								'" . $newDate ."', 
								'" . $_POST['remarks'][$i] . "',
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
		else {//DIS
			
			$new_thesis_id = "T".runnum('id','pg_thesis');
			$new_proposal_id = "P".runnum('id','pg_proposal');
		
			$sqlsubmit2="INSERT INTO pg_thesis(id,status, student_matrix_no,insert_by,insert_date,
					modify_by,modify_date,ref_thesis_status_id_proposal)
					VALUES('$new_thesis_id','INP','$user_id','$user_id','$curdatetime','$user_id','$curdatetime','INP') ";

			$db_klas2->query($sqlsubmit2); 
		
			$sql1="INSERT INTO pg_proposal
				(id,thesis_title,thesis_type,introduction,objective,description,discussion_status,
				pg_thesis_id, verified_status, status, report_date, insert_by,insert_date,modify_by,modify_date)
				VALUES('$new_proposal_id','$thesis_title','$thesis_type','$introduction','$objective','$description',
				'$discussion_status',
				'$new_thesis_id','SAV', 'OPN', '$curdatetime','$user_id','$curdatetime','$user_id','$curdatetime') ";
				
			$db_klas2->query($sql1); 
		
			$sql7_2 = "UPDATE pg_thesis
					SET archived_date = '$curdatetime', archived_status = 'ARC',
					modify_by = '$userid', modify_date = '$curdatetime'
					WHERE id = '$pgThesisId'
					AND student_matrix_no = '$studentMatrixNo'
					AND archived_status is null";

			$dbg->query($sql7_2);

			$sql3 = "UPDATE pg_proposal 		
					SET archived_status = 'ARC', archived_date = '$curdatetime' 
					WHERE id = '$pgProposalId'
					AND verified_status = 'DIS'
					AND archived_status is null";

			$db_klas2->query($sql3);
			
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
				VALUES('$job_area_id', '$new_proposal_id', '$myjobs_area1', '$myjobs_area2', '$myjobs_area3', '$myjobs_area4', '$myjobs_area5', 
				'$myjobs_area6', '$curdatetime', '".$_SESSION['user_id']."', '$curdatetime', '".$_SESSION['user_id']."')";
				$db_klas2->query($insertArea);
			}
			
			for ($i=0; $i<sizeof($_POST['date']); $i++) {
		
				$meeting_detail_id = runnum2('id','pg_meeting_detail');	
				
				$tmpDate = date_create($_POST['date'][$i]." ".$_POST['meeting_time'][$i]);
				$newDate = date_format($tmpDate, 'Y-m-d H:i:s');
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
								'" . $_POST['lecturer_name'][$i] . "',
								'" . $newDate ."', 
								'" . $_POST['remarks'][$i] . "',
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
}

if(isset($_POST['btnSubmit']) && ($_POST['btnSubmit'] <> ""))
{
	$myjobs_area1 = $_POST['jobs1_area'];
	$myjobs_area2 = $_POST['jobs2_area'];
	$myjobs_area3 = $_POST['jobs3_area'];
	$myjobs_area4 = $_POST['jobs4_area'];
	$myjobs_area5 = $_POST['jobs5_area'];
	$myjobs_area6 = $_POST['jobs6_area'];
	
	$curdatetime = date("Y-m-d H:i:s");	
	
	$sql2 ="SELECT pp.id as proposal_id, pt.id as thesis_id, pp.verified_status
				FROM pg_thesis pt 
				LEFT JOIN pg_proposal pp ON (pp.pg_thesis_id=pt.id) 
				LEFT JOIN ref_proposal_status rps ON (rps.id = pp.verified_status)  
				LEFT JOIN new_employee ne ON (ne.empid = pp.verified_by) 
				WHERE pt.student_matrix_no = '$user_id'
				AND pp.verified_status in ('SAV','DIS')				
				AND pp.archived_status is null
				AND pt.ref_thesis_status_id_proposal = 'INP'
				ORDER BY pt.id";
				
	$result_sql2 = $db->query($sql2);
	$row_area = $db->fetchArray(); 
	$thesis_id=$row_area['thesis_id'];
	$proposal_id=$row_area['proposal_id'];
	$verified_status=$row_area['verified_status'];
	
	$row_cnt = mysql_num_rows($result_sql2); 
	if ($row_cnt==0) //no record
	{		
		$thesis_id = "T".runnum('id','pg_thesis');
		$new_proposal_id = "P".runnum('id','pg_proposal');
		
		$sqlsubmit2="INSERT INTO pg_thesis(id,status, student_matrix_no,insert_by,insert_date,
					modify_by,modify_date,ref_thesis_status_id_proposal)
					VALUES('$thesis_id','INP','$user_id','$user_id','$curdatetime','$user_id','$curdatetime','INP') ";

		$db_klas2->query($sqlsubmit2); 
		 
		$sqlsubmit="INSERT INTO pg_proposal
				(id,thesis_title,thesis_type,introduction,objective,description,discussion_status,
				pg_thesis_id, verified_status, status, report_date, insert_by,insert_date,modify_by,modify_date)
				VALUES('$new_proposal_id','$thesis_title','$thesis_type','$introduction','$objective','$description',
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
					AND verified_status = 'APP'
					AND status = 'DIS'
					AND archived_status is null";

		$db_klas2->query($sql3);
		
		
		$selectArea = "SELECT * FROM pg_proposal_area
		WHERE pg_proposal_id = '$pgProposalId' ";
		$db_klas2->query($selectArea); //echo $updateArea;
		
		$result_selectArea = $db_klas2->query($selectArea);	
		$row_cnt = mysql_num_rows($result_selectArea);	
		
		if ($row_cnt == 0) {
			$job_area_id = runnum2('id','pg_proposal_area');
			$insertArea = "INSERT INTO pg_proposal_area
			(id, pg_proposal_id, job_id1_area, job_id2_area, job_id3_area, job_id4_area, job_id5_area, job_id6_area, insert_date, insert_by,
			modified_date, modified_by)
			VALUES('$job_area_id', '$new_proposal_id', '$myjobs_area1', '$myjobs_area2', '$myjobs_area3', '$myjobs_area4', '$myjobs_area5', 
			'$myjobs_area6', '$curdatetime', '".$_SESSION['user_id']."', '$curdatetime', '".$_SESSION['user_id']."')";
			$db_klas2->query($insertArea);
		}
		
		for ($i=0; $i<sizeof($_POST['date']); $i++) {
		
				$meeting_detail_id = runnum2('id','pg_meeting_detail');	
				
				$tmpDate = date_create($_POST['date'][$i]." ".$_POST['meeting_time'][$i]);
				$newDate = date_format($tmpDate, 'Y-m-d H:i:s');
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
								'" . $_POST['lecturer_name'][$i] . "',
								'" . $newDate ."', 
								'" . $_POST['remarks'][$i] . "',
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
	else 
	{
		if ($verified_status=='SAV')
		{
			$sqlUpdate1 = "UPDATE pg_thesis
					SET modify_by = '$user_id', modify_date = '$curdatetime', ref_thesis_status_id_proposal = 'INP'
					WHERE id = '$thesis_id'";
					
			$db_klas2->query($sqlUpdate1);
		
			$sqlUpdate2 = "UPDATE pg_proposal
					SET thesis_title = '$thesis_title', thesis_type = '$thesis_type', introduction = '$introduction', 
					objective = '$objective', description = '$description', modify_by = '$user_id', modify_date = '$curdatetime',
					discussion_status = '$discussion_status', verified_status = 'INP', report_date = '$curdatetime'
					WHERE id = '$proposal_id'
					AND verified_status in ('SAV','REQ')";

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
					insert_date = '$curdatetime', insert_by = '".$_SESSION['user_id']."', modified_date = '$curdatetime', 
					modified_by = '".$_SESSION['user_id']."'
				WHERE pg_proposal_id = '$proposal_id' ";
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
			
			for ($i=0; $i<sizeof($_POST['date']); $i++) {
		
				$meeting_detail_id = runnum2('id','pg_meeting_detail');	
				
				$tmpDate = date_create($_POST['date'][$i]." ".$_POST['meeting_time'][$i]);
				$newDate = date_format($tmpDate, 'Y-m-d H:i:s');
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
								'" . $_POST['lecturer_name'][$i] . "',
								'" . $newDate ."', 
								'" . $_POST['remarks'][$i] . "',
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
		else //DIS
		{
			$new_thesis_id = "T".runnum('id','pg_thesis');
			$new_proposal_id = "P".runnum('id','pg_proposal');
			
			$sqlsubmit2="INSERT INTO pg_thesis(id,status, student_matrix_no,insert_by,insert_date,
					modify_by,modify_date,ref_thesis_status_id_proposal)
					VALUES('$new_thesis_id','INP','$user_id','$user_id','$curdatetime','$user_id','$curdatetime','INP') ";

		$db_klas2->query($sqlsubmit2); 
		
			$sql1="INSERT INTO pg_proposal
				(id,thesis_title,thesis_type,introduction,objective,description,discussion_status,
				pg_thesis_id, verified_status, status, report_date, insert_by,insert_date,modify_by,modify_date)
				VALUES('$new_proposal_id','$thesis_title','$thesis_type','$introduction','$objective','$description',
				'$discussion_status',
				'$new_thesis_id','INP', 'OPN', '$curdatetime','$user_id','$curdatetime','$user_id','$curdatetime') ";
				
			$db_klas2->query($sql1); 
		
			$sql2 = "UPDATE pg_thesis
				SET modify_by = '$user_id', modify_date = '$curdatetime', ref_thesis_status_id_proposal = 'INP'
				WHERE id = '$thesis_id'";
				
			$db_klas2->query($sql2);
			
			$sql7_2 = "UPDATE pg_thesis
					SET archived_date = '$curdatetime', archived_status = 'ARC',
					modify_by = '$userid', modify_date = '$curdatetime'
					WHERE id = '$pgThesisId'
					AND student_matrix_no = '$studentMatrixNo'";

					$dbg->query($sql7_2);

			$sql3 = "UPDATE pg_proposal 		
					SET archived_status = 'ARC', archived_date = '$curdatetime' 
					WHERE id = '$proposal_id'
					AND verified_status = 'DIS'";

			$db_klas2->query($sql3);
			
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
				VALUES('$job_area_id', '$proposal_id', '$myjobs_area1', '$myjobs_area2', '$myjobs_area3', '$myjobs_area4', '$myjobs_area5', 
				'$myjobs_area6', '$curdatetime', '".$_SESSION['user_id']."', '$curdatetime', '".$_SESSION['user_id']."')";
				$db_klas2->query($insertArea);
			}
			
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
				VALUES('$job_area_id', '$new_proposal_id', '$myjobs_area1', '$myjobs_area2', '$myjobs_area3', '$myjobs_area4', '$myjobs_area5', 
				'$myjobs_area6', '$curdatetime', '".$_SESSION['user_id']."', '$curdatetime', '".$_SESSION['user_id']."')";
				$db_klas2->query($insertArea);
			}
			
			for ($i=0; $i<sizeof($_POST['date']); $i++) {
		
				$meeting_detail_id = runnum2('id','pg_meeting_detail');	
				
				$tmpDate = date_create($_POST['date'][$i]." ".$_POST['meeting_time'][$i]);
				$newDate = date_format($tmpDate, 'Y-m-d H:i:s');
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
								'" . $_POST['lecturer_name'][$i] . "',
								'" . $newDate ."', 
								'" . $_POST['remarks'][$i] . "',
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
	
</head>
<body>

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
    var confirmSubmit = confirm("Are you confirm to submit?");
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
				rps.description AS proposal_description, ne.name AS verified_name
				FROM pg_thesis pt 
				LEFT JOIN pg_proposal pp ON (pp.pg_thesis_id=pt.id) 
				LEFT JOIN ref_proposal_status rps ON (rps.id = pp.verified_status)  
				LEFT JOIN new_employee ne ON (ne.empid = pp.verified_by) 
				WHERE pt.student_matrix_no = '$user_id'
				AND pp.verified_status in ('SAV','INP')				
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
		<td><strong>Notes:</strong><td>
	</tr>
	<tr>
		<td>(1) This form should be submitted to MSU Graduate School of Management (GSM) upon completing of the Research Methodology and before student starts the project.</td>
	</tr>
	<tr>
		<td>(2) Students are advised to seek the lecturer's advice before proceeding with the proposal.</td>
	</tr>
	<tr>
		<td>(3) Student should plan on 6-month's time from the Official Approval Date to complete the Final Project.</td>
	</tr>
	<tr>
		<td>(4) As refer to MBA rules, No candidate with CGPA below 3.0 shall be eligible to register for the Final Project of the degree unless recommended by the Board of Examiners.</td>
	</tr>
	<tr>
		<td>(5) Appointment of supervisor is subject to the recommendation from the Director of MSU Graduate School of Management (GSM).</td>
	</tr>				
</table>
<br/>			
<fieldset>
<legend><strong>New Application - Outline of Proposed Research/Case Study</strong></legend>	
	<table>
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
	<table>
     <tr>
      <td><p>Have you discussed about your research/case to any lecturer of MSU?  </p>       </td>
	   <td><p>
	  	<?php if($discussion_status=='Y')	{	?>
			<input type="radio" name="discussion_status" value="Y" checked> Yes
			<input type="radio" name="discussion_status" value="N"> No
		<?php	}	else if ($discussion_status=='N')	{	?>
			<input type="radio" name="discussion_status" value="Y"> Yes
			<input type="radio" name="discussion_status" value="N" checked> No
		<?php	}	else  {	?>
			<input type="radio" name="discussion_status" value="Y" checked> Yes
			<input type="radio" name="discussion_status" value="N" > No
		<?php	}	?> 
	    
	   </p></td>
    </tr>
	</table>
		 <fieldset style="width:800px">
			<legend><strong>Discussion Details</strong></legend>	 
		 <p>
			<a href="javascript:void(0)" class="add-certification add_btn" >Add</a>&nbsp; 
			<a href="javascript:void(0)" class="remove-certification delete" >Delete</a>
		 </p>
		 <table border="1" cellpadding="2" cellspacing="1" width="99%" id="inputs10" class="thetable">
		 
		 <tr>
		   <th width="6%"><input type="checkbox" name="box[]" id="selectall_file" /></th>
			 <th width="12%">Date</th>
			 <th width="12%">Time</th>
			 <th width="29%">Lecturer Name</th>
			 <th width="41%">Notes</th>
			 <th width="41%">Action</th>
		 </tr>
		
		 <?php
										$sqlMeeting="SELECT pmd.id,pmd.lecturer_name, DATE_FORMAT(pmd.meeting_sdate,'%d/%m/%Y') as date,
										DATE_FORMAT(pmd.meeting_sdate,'%h:%i %p') as time, pmd.remark, pmd.insert_by, pmd.insert_date 
										FROM  pg_meeting_detail pmd  
										WHERE pmd.pg_proposal_id='$proposal_id' 
										ORDER BY pmd.meeting_sdate DESC ";			
										
										$result = $db_klas2->query($sqlMeeting); //echo $sql;
					
											while($row = mysql_fetch_array($result)) 					
											{ 
												?><tr>
														<td align="center" width="30"><input type="checkbox" class="case_certificate" name="cbDelFile[]" /><input type="hidden" name="delFile[]" value="<?=$row['document_id'];?>" /></td>
														<td align="center" width="116"><label name="date[]" ></label><?=$row["date"];?></td>
														<td align="center" width="124"><label name="meeting_time[]"></label><?=$row["time"];?></td>
														<td align="left" width="185"><label name="lecturer_name[]" ></label><?=$row["lecturer_name"];?></td>
														<td align="left" width="340"><label name="remarks[]" size="50" ></label><?=$row["remark"];?></td>		
														<td width="34" align="center"></a>
													<a href="delete_meeting_detail.php?id=<?=$row["id"];?>"><img src="../images/delete_on.gif" width="20" height="19" style="border:0px;" title="Delete Meeting Information" ></a>
													</td>		
													</tr>
											<?} ?> 	
								</table>
	  </fieldset>
	  
	   <div>
	   
	   
	   <br />
	   <? $sqlLookupJobArea = "SELECT jobarea, area
											FROM job_list_category
											ORDER By jobarea";?>
											
	   <? $sqlPgProposalArea = "SELECT pg_proposal_id,job_id1_area,job_id2_area,job_id3_area,job_id4_area,job_id5_area,job_id6_area
	 										FROM pg_proposal_area
											WHERE pg_proposal_id = '$proposal_id'"; ?>
		<br />									
	   <fieldset style="width:800px">
	   <legend><strong>Thesis Areas</strong></legend>	
	  
	  	<table  align="center">
		
		<tr>
			<td width="70" nowrap><font color="#FF0000">*</font><b>Area 1</b></td>
			<td width="134"><span id="span_area1" style="display:">
			  <select name="jobs1_area" id="jobs1_area"><option value="">--Please Select--</option>
        <?php
			$rsLookupJobArea = $db->query($sqlLookupJobArea);
						
						$rsPgProposalArea = $dbf->query($sqlPgProposalArea);						
						$dbf->next_record();
						$jobArea1=$dbf->f('job_id1_area');						
						
						while ($db->next_record()) {
							$jobarea=$db->f('jobarea');
							$area=$db->f('area');
							if ($jobArea1==$jobarea) {
								?><option value="<?=$jobarea?>" selected="selected"><?=$area?></option><?
							}
							else {
								?><option value="<?=$jobarea?>"><?=$area?></option><?
							}
						};
		?></select></span></td>
		
		
		<td width="10"></td>
        <td width="91"><b>Area 4</b></td>
        <td width="134"><span id="span_area4" style="display:"> 
          <select name="jobs4_area" id="jobs4_area"><option value="">--Please Select--</option>
        <?php
			$rsLookupJobArea = $db->query($sqlLookupJobArea);
						
						$rsPgProposalArea = $dbf->query($sqlPgProposalArea);						
						$dbf->next_record();
						$jobArea4=$dbf->f('job_id4_area');						
						
						while ($db->next_record()) {
							$jobarea=$db->f('jobarea');
							$area=$db->f('area');
							if ($jobArea4==$jobarea) {
								?><option value="<?=$jobarea?>" selected="selected"><?=$area?></option><?
							}
							else {
								?><option value="<?=$jobarea?>"><?=$area?></option><?
							}
						};
		?></select></span></td>
		
		</tr>
		<tr>
		<td ><b>Area 2</b></td>
		<td><span id="span_area2" style="display:"><select name="jobs2_area" id="jobs2_area" ><option value="">--Please Select--</option>
        <?php
			$rsLookupJobArea = $db->query($sqlLookupJobArea);
						
						$rsPgProposalArea = $dbf->query($sqlPgProposalArea);						
						$dbf->next_record();
						$jobArea2=$dbf->f('job_id2_area');						
						
						while ($db->next_record()) {
							$jobarea=$db->f('jobarea');
							$area=$db->f('area');
							if ($jobArea2==$jobarea) {
								?><option value="<?=$jobarea?>" selected="selected"><?=$area?></option><?
							}
							else {
								?><option value="<?=$jobarea?>"><?=$area?></option><?
							}
						};
		?></select></span></td>
		
		<td width="25:px"></td>
        <td nowrap><b>Area 5</b></td>
        <td><span id="span_area5" style="display:"><select name="jobs5_area" id="jobs5_area"><option value="">--Please Select--</option>
        <?php
			$rsLookupJobArea = $db->query($sqlLookupJobArea);
						
						$rsPgProposalArea = $dbf->query($sqlPgProposalArea);						
						$dbf->next_record();
						$jobArea5=$dbf->f('job_id5_area');						
						
						while ($db->next_record()) {
							$jobarea=$db->f('jobarea');
							$area=$db->f('area');
							if ($jobArea5==$jobarea) {
								?><option value="<?=$jobarea?>" selected="selected"><?=$area?></option><?
							}
							else {
								?><option value="<?=$jobarea?>"><?=$area?></option><?
							}
						};
		?></select></span></td>
		
		</tr>
		
		<tr>
        <td ><b>Area 3</b></td>
        <td><span id="span_area3" style="display:"><select name="jobs3_area" id="jobs3_area"><option value="">--Please Select--</option>
        <?php
			$rsLookupJobArea = $db->query($sqlLookupJobArea);
						
						$rsPgProposalArea = $dbf->query($sqlPgProposalArea);						
						$dbf->next_record();
						$jobArea3=$dbf->f('job_id3_area');						
						
						while ($db->next_record()) {
							$jobarea=$db->f('jobarea');
							$area=$db->f('area');
							if ($jobArea3==$jobarea) {
								?><option value="<?=$jobarea?>" selected="selected"><?=$area?></option><?
							}
							else {
								?><option value="<?=$jobarea?>"><?=$area?></option><?
							}
						};
		?></select></span></td>
            
        <td width="25:px"></td>
        <td width="10:px"><b>Area 6</b></td>
        <td><span id="span_area6" style="display:"><select name="jobs6_area" id="jobs6_area"><option value="">--Please Select--</option>
        <?php
			$rsLookupJobArea = $db->query($sqlLookupJobArea);
						
						$rsPgProposalArea = $dbf->query($sqlPgProposalArea);						
						$dbf->next_record();
						$jobArea6=$dbf->f('job_id6_area');						
						
						while ($db->next_record()) {
							$jobarea=$db->f('jobarea');
							$area=$db->f('area');
							if ($jobArea6==$jobarea) {
								?><option value="<?=$jobarea?>" selected="selected"><?=$area?></option><?
							}
							else {
								?><option value="<?=$jobarea?>"><?=$area?></option><?
							}
						};
		?></select></span></td>
    	</tr>		
		</table>
	   </fieldset>
	   
	   
	   
	   <fieldset style="width:800px">
			   <legend><strong>Attachment</strong></legend>
			   <p>
						<a href="javascript:void(0)" class="add-file add_btn">Add </a> &nbsp; 
						<a href="javascript:void(0)" class="remove-file del_btn">Delete </a> </p>
								
						<table border="1" cellpadding="3" cellspacing="3" width="100%" id="inputs9" class="thetable">
							<tr>
								<th><input type="checkbox" id="selectall_certificate" /></th>
								<th><span class="labeling">File Name</span></th>
								<th><span class="labeling">Upload File</span></th>
								<th><span class="labeling">Action</span></th>
							</tr>
								
						<?
										$sqlUpload="SELECT * FROM file_upload_proposal 
										WHERE pg_proposal_id='$proposal_id' 
										AND attachment_level='S' ";			
										
										$result = $db_klas2->query($sqlUpload); //echo $sql;
										$varRecCount=0;					
										while($row = mysql_fetch_array($result)) 					
										{ 
											$varRecCount++;
											?><tr>
													<td align="center"><input type="checkbox" class="case_file" name="cbDelFile[]" /><input type="hidden" name="delFile[]" value="<?=$row["fu_cd"];?>" /></td>
													<td><label name="file_name[]" size="40" ></label><?=$row["fu_document_filename"];?></td>
													<td align="left"></td>
													<td><a href="download.php?fc=<?=$row["fu_cd"];?>&al=S" target="_blank" onMouseOver="toolTip('<?=$row["fu_document_filename"];?>', 300)" onMouseOut="toolTip()" align="center">
													<img src="../images/view_doc.jpg" width="20" height="19" style="border:0px;" title="View document"></a>
													<a href="delete_attachment_proposal.php?fc=<?=$row["fu_cd"];?>&al=S" >
													<img src="../images/delete_doc.jpg" width="20" height="19" style="border:0px;" title="Delete document"></a></td>
												</tr>										
										<?}?>							
							
							
						</table>
						<br/>
						
			</fieldset>
	   </div>
		 <table>
			<tr>
				<td><input type="submit" name="btnSave" id="btnSave" align="center"  value="Save as Draft" /></td>
				<td><input type="submit" name="btnSubmit" id="btnSubmit" align="center"  value="Submit" onClick="return respConfirm()" /></td>
				<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../student/student_programme.php';" /></td>		
			</tr>
		</table>
	<table>
		<tr>
			<td><span style="color:#FF0000">*</span> - is a required field. </td>
		</tr>
	</table>		
  </fieldset>
  <?}
else {
	?>
	<fieldset>
		<legend><strong><span style="color:#FF0000">Notification Message</span></strong></legend>	
		<table>
			<tr>
				<td>Your thesis proposal has been submitted to the Faculty for verification.</td>
			</tr>
		</table>
	</fieldset>	
	<?
	
}?>
  </form>
</body>
</html>




