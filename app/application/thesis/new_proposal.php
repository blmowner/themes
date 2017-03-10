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
	global $dbc;
	global $dbc1;
	//global $dbConnStudent;
	$sql_login = "SELECT name FROM student WHERE matrix_no='$user_id'";
	if (substr($user_id,0,2) != '07') { 
		$dbConnStudent= $dbc; 
	} 
	else { 
		$dbConnStudent=$dbc1; 
	}
	$dbConnStudent->query($sql_login);
	$dbConnStudent->next_record();	
	$name = $dbConnStudent->f('name');
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
	if (empty($_POST['thesis_title'])) $msg[] = "<div class=\"error\"><span>Please enter the Thesis Title as required below.</span></div>";
	if (empty($_POST['introduction'])) $msg[] = "<div class=\"error\"><span>Please enter the Introduction  as required below.</span></div>";
	if (empty($_POST['objective'])) $msg[] = "<div class=\"error\"><span>Please enter the Objective  as required below.</span></div>";
	if (empty($_POST['description'])) $msg[] = "<div class=\"error\"><span>Please enter the Description as required below.</span></div>";
	if ($_POST['JobArea'] == "") $msg[] = "<div class=\"error\"><span>Please select Thesis Area from the list given below.</span></div>";
	
	if(empty($msg)) 
	{

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
					AND pp.verified_status in ('SAV','DIS','APP','AWC') 
					AND pp.status in ('OPN','DIS')
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
					VALUES('$new_proposal_id','".mysql_real_escape_string($thesis_title)."','$thesis_type','$introduction','$objective','$description',
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
			
			$sqlMeeting = "UPDATE pg_meeting_detail
			SET pg_proposal_id = '$new_proposal_id'
			WHERE student_matrix_no = '$user_id'
			AND (pg_proposal_id is null OR pg_proposal_id = '')";
			
			$db_klas2->query($sqlMeeting);
			
			$sqlUpload = "UPDATE file_upload_proposal
			SET pg_proposal_id = '$new_proposal_id'
			WHERE student_matrix_no = '$user_id'
			AND (pg_proposal_id is null OR pg_proposal_id = '')";
			
			$db_klas2->query($sqlUpload);
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
				
				$sqlMeeting = "UPDATE pg_meeting_detail
				SET pg_proposal_id = '$proposal_id'
				WHERE student_matrix_no = '$user_id'
				AND (pg_proposal_id is null OR pg_proposal_id = '')";
				
				$db_klas2->query($sqlMeeting);
				
				$sqlUpload = "UPDATE file_upload_proposal
				SET pg_proposal_id = '$proposal_id'
				WHERE student_matrix_no = '$user_id'
				AND (pg_proposal_id is null OR pg_proposal_id = '')";
			
				$db_klas2->query($sqlUpload);
				
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
					VALUES('$new_proposal_id','".mysql_real_escape_string($thesis_title)."','$thesis_type','$introduction','$objective','$description',
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
						WHERE student_matrix_no = '$user_id'
						AND (pg_proposal_id is null OR pg_proposal_id = '')";

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
				
				$sqlMeeting = "UPDATE pg_meeting_detail
				SET pg_proposal_id = '$new_proposal_id'
				WHERE student_matrix_no = '$user_id'
				AND (pg_proposal_id is null OR pg_proposal_id = '')";
				
				$db_klas2->query($sqlMeeting);
				
				$sqlUpload = "UPDATE file_upload_proposal
				SET pg_proposal_id = '$new_proposal_id'
				WHERE student_matrix_no = '$user_id'
				AND (pg_proposal_id is null OR pg_proposal_id = '')";
				
				$db_klas2->query($sqlUpload);
				
			}				
		}	 
		$msg[] = "<div class=\"success\"><span>The thesis proposal has been saved successfully.</span></div>";
	}
}

if(isset($_POST['btnSubmit']) && ($_POST['btnSubmit'] <> ""))
{
	$msg = array();
	if(empty($_POST['thesis_title'])) $msg[] = "<div class=\"error\"><span>Please enter the Thesis Title as required below.</span></div>";
	if(empty($_POST['introduction'])) $msg[] = "<div class=\"error\"><span>Please enter the Introduction as required below.</span></div>";
	if(empty($_POST['objective'])) $msg[] = "<div class=\"error\"><span>Please enter the Objective as required below.</span></div>";
	if(empty($_POST['description'])) $msg[] = "<div class=\"error\"><span>Please enter the Description as required below.</span></div>";
	if($_POST['JobArea'] == "") $msg[] = "<div class=\"error\"><span>Please select the Thesis Area from the list given below.</span></div>";

	if(empty($msg)) 
	{
		$myjobs_area1 = $_REQUEST['JobAreaID'];
		$myjobs_area2 = $_REQUEST['JobAreaID1'];
		$myjobs_area3 = $_REQUEST['JobAreaID2'];
		$myjobs_area4 = $_REQUEST['JobAreaID3'];
		$myjobs_area5 = $_REQUEST['JobAreaID4'];
		$myjobs_area6 = $_REQUEST['JobAreaID5'];
		
		$curdatetime = date("Y-m-d H:i:s");	
		
		$sql2 ="SELECT pp.id as proposal_id, pt.id as thesis_id, pp.verified_status
					FROM pg_thesis pt 
					LEFT JOIN pg_proposal pp ON (pp.pg_thesis_id=pt.id) 
					LEFT JOIN ref_proposal_status rps ON (rps.id = pp.verified_status)  
					WHERE pt.student_matrix_no = '$user_id'
					AND pp.verified_status in ('SAV','DIS','APP','AWC')
					AND pp.status in ('OPN','DIS')
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
					VALUES('$new_proposal_id','".mysql_real_escape_string($thesis_title)."','$thesis_type','".mysql_real_escape_string($introduction)."',
					'".mysql_real_escape_string($objective)."', '".mysql_real_escape_string($description)."',
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
						AND verified_status in ('APP','AWC')
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
			
			$sqlMeeting = "UPDATE pg_meeting_detail
				SET pg_proposal_id = '$new_proposal_id'
				WHERE student_matrix_no = '$user_id'
				AND (pg_proposal_id is null OR pg_proposal_id = '')";
				
				$db_klas2->query($sqlMeeting);
			
			$sqlUpload = "UPDATE file_upload_proposal
				SET pg_proposal_id = '$new_proposal_id'
				WHERE student_matrix_no = '$user_id'
				AND (pg_proposal_id is null OR pg_proposal_id = '')";
				
				$db_klas2->query($sqlUpload);
				
			$sqlLookupJobArea = "SELECT area
								FROM job_list_category
								WHERE jobarea = '$myjobs_area1' ";
			$rsLookupJobArea = $dbc->query($sqlLookupJobArea);
			$jobarea1 =$dbc->f('area');
			
				
					
			
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
				
				$sqlMeeting = "UPDATE pg_meeting_detail
				SET pg_proposal_id = '$proposal_id'
				WHERE student_matrix_no = '$user_id'
				AND (pg_proposal_id is null OR pg_proposal_id = '')";
				
				$db_klas2->query($sqlMeeting);
				
				$sqlUpload = "UPDATE file_upload_proposal
				SET pg_proposal_id = '$proposal_id'
				WHERE student_matrix_no = '$user_id'
				AND (pg_proposal_id is null OR pg_proposal_id = '')";
				
				$db_klas2->query($sqlUpload);
				
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
					VALUES('$new_proposal_id','".mysql_real_escape_string($thesis_title)."','$thesis_type','$introduction','$objective','$description',
					'$discussion_status',
					'$new_thesis_id','INP', 'OPN', '$curdatetime','$user_id','$curdatetime','$user_id','$curdatetime') ";
					
				$db_klas2->query($sql1); 
			
				$sql7_2 = "UPDATE pg_thesis
						SET archived_date = '$curdatetime', archived_status = 'ARC',
						modify_by = '$userid', modify_date = '$curdatetime'
						WHERE id = '$pgThesisId'
						AND student_matrix_no = '$studentMatrixNo'";
	
						$dbg->query($sql7_2);
	
				$sql3 = "UPDATE pg_proposal 		
						SET archived_status = 'ARC', archived_date = '$curdatetime' 
						WHERE id = '$proposal_id'
						AND verified_status = 'DIS'
						OR status = 'DIS'";
	
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
				
				$sqlMeeting = "UPDATE pg_meeting_detail
				SET pg_proposal_id = '$new_proposal_id'
				WHERE student_matrix_no = '$user_id'
				AND (pg_proposal_id is null OR pg_proposal_id = '')";
				
				$db_klas2->query($sqlMeeting);
				
				$sqlUpload = "UPDATE file_upload_proposal
				SET pg_proposal_id = '$new_proposal_id'
				WHERE student_matrix_no = '$user_id'
				AND (pg_proposal_id is null OR pg_proposal_id = '')";
				
				$db_klas2->query($sqlUpload);
			}			
		}
		$msg[] = "<div class=\"success\"><span>The thesis proposal has been submitted successfully to the Faculty for verification.</span></div>";
	
		$selectfrom = "SELECT const_value
		FROM base_constant WHERE const_term = 'EMAIL_ADMIN'";
		$resultfrom = $dba->query($selectfrom);
		$dba->next_record();
		$fromadmin =$dba->f('const_value');
		
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
		
		$selectfalname = "SELECT a.name,a.email,b.title 
		FROM new_employee a
		LEFT JOIN lookup_gelaran b ON(b.id = a.title)
		WHERE a.empid = '$selectidfaculty'";
		$resultselfalname = $dbc->query($selectfalname);
		$dbc->next_record();
		$selectname =$dbc->f('name');
		$title =$dbc->f('title');

		$curdatetime1 = date("d-m-Y");			
		$username = convertname($user_id);
		
		$sql4 = "SELECT description
		FROM ref_thesis_type
		WHERE id = '$thesis_type'
		AND status = 'A'";
		
		$db->query($sql4);
		$db->next_record();
		$type = $db->f('description');
			
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
		$studdept = "SELECT a.program_code, b.programid,a.manage_by_whom
					FROM student_program a 
					LEFT JOIN program b ON (a.program_code =b.programid)
					WHERE matrix_no = '$user_id'";		
		$resultstuddept = $dbn->query($studdept);
		$dbn->next_record();
		$dept = $dbn->f('manage_by_whom');
		
		$sqlvalidate = "SELECT const_value
		FROM base_constant WHERE const_term = 'EMAIL_STU_TO_FAC'";
		$resultvalidate = $dbd->query($sqlvalidate);
		$dbd->next_record();
		$valid = $dbd->f('const_value');

		if (($valid == 'Y') && (!empty($receiveemail)))
		{
			include("../../../app/application/email/email_new_proposal.php");
		}

		$sqlval = "SELECT const_value
		FROM base_constant WHERE const_term = 'MESSAGE_STU_TO_FAC'";
		$resultvalidate = $dbd->query($sqlval);
		$dbd->next_record();
		$valid =$dbd->f('const_value');
		if($valid == 'Y')
		{
			include("../../../app/application/inbox/submission/new_proposal_inbox.php");

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
	<script src="../../../lib/js/jquery.min2.js"></script>
	<script src="../../../lib/js/jquery.colorbox.js"></script>
	<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
	<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>
</head>
<body>

<SCRIPT LANGUAGE="JavaScript">

function respConfirm(data) {
    //alert (""+ data);
	//if (data == 0)
	if (data == '')
	{
		var confirmSubmit = confirm("There is no attachment being uploaded. Are you sure to proceed ?");
		if (confirmSubmit==true)
		{
			return saveStatus;
		}
		if (confirmSubmit==false)
		{
			return false;
		}

	}
	else
	{
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
}

</SCRIPT>

<script type="text/javascript">
function newDicussion(pid) 
{
    var ask = window.confirm("Ensure your proposal has been saved before proceed or otherwise the last change will be discarded.\nClick OK to proceed or CANCEL to stay on the same page.");
    if (ask) 
	{
		//document.location.href = "../thesis/new_proposal_discussion.php?pid=" + pid;
		document.location.href = "../thesis/new_proposal_discussion.php?pid=" + pid;
		return true;
    }
	return false;
}

</script>

<script>
function newAttachment(pid) {
    var ask = window.confirm("Ensure your proposal has been saved before proceed or otherwise the last change will be discarded.\nClick OK to proceed or CANCEL to stay on the same page.");
    if (ask) {
		document.location.href = "../thesis/new_proposal_attachment.php?pid=" + pid;

    }
}
</script>

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

	$('<tr><td align="center"><input type="checkbox" name="box[]" class="case_certificate" /></td><td><input type="text" name="date[]" size="15" readonly id="datepicker'+i+'"/></td><td width="66" height="1" class="tbmain"><select name="meeting_time[]" size="1" ><option value=\"\"></option><option value="07:00">07:00 AM</option><option value="07:30">07:30 AM</option><option value="08:00">08:00 AM</option><option value="08:30">08:30 AM</option><option value="09:00">09:00 AM</option><option value="09:30">09:30 AM</option><option value="10:00">10:00 AM</option><option value="10:30">10:30 AM</option><option value="11:00">11:00 AM</option><option value="11:30">11:30 AM</option><option value="12:00">12:00 PM</option><option value="12:30">12:30 PM</option><option value="13:00">01:00 PM</option><option value="13:30">01:30 PM</option><option value="14:00">02:00 PM</option><option value="14:30">02:30 PM</option><option value="15:00">03:00 PM</option><option value="15:30">03:30 PM</option><option value="16:00">04:00 PM</option><option value="16:30">04:30 PM</option><option value="17:00">05:00 PM</option><option value="17:30">05:30 PM</option><option value="18:00">06:00 PM</option><option value="18:30">06:30 PM</option><option value="19:00">07:00 PM</option><option value="19:30">07:30 PM</option><option value="20:00">08:00 PM</option><option value="20:30">08:30 PM</option><option value="21:00">09:00 PM</option><option value="21:30">09:30 PM</option><option value="22:00">10:00 PM</option><option value="22:30">10:30 PM</option><option value="23:00">11:00 PM</option><option value="23:30">11:30 PM</option><option value="24:00">12:00 PM</option></select></td><td><input type="text" name="lecturer_name[]" size="30" /></td><td><input type="text" name="remarks[]" size="50" id="remarks" /></td></tr>').animate({ opacity: "show" }, "slow").appendTo('#inputs10');

	$("#datepicker"+i).datepicker({
		changeMonth: true,
		changeYear: true,
		yearRange: '-100:+0',
		dateFormat: 'dd-mm-yy'
		});

		i++;		
	});
    
	
	$('a.remove-certification').click(function() {
        if ($('input[type=checkbox]').is(':checked')){
			$( "#dialog-confirm" ).dialog({
					modal: false,
						  buttons: {
								"Yes": function() {
								  $(this ).dialog( "close" );
								  var data = $(":checkbox:checked").map(function(i,n) {
																		return $(n).val();
																	}).get();
								  $.post("delete_meeting_detail.php", { 'allToDelete': data },
										function(da){
											$(".case_certificate:checked").each(function() {
												$(this).parent().parent().remove();
												//alert(da);
											});
										});
								},
								Cancel: function() {
								  $( this ).dialog( "close" );
								  return false;
								}
							} //end buttons
			}); 
        }	
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

function updateMeeting(data) {
	$( "#dialog-update" ).dialog({
			modal: false,
				  buttons: {
						"Yes": function() {
						  $(this ).dialog( "close" );
						  $.post("update_meeting.php", {
							  //'meeting_date': $("#meeting_date" + data).val(),
							  //'meeting_time': $("#meeting_time" + data).val(),
							  'lecturer_name': $("#lecturer_name" + data).val(),
							  'remark': $("#remark" + data).val(),
							  'id': data
							},
							function(da){
								alert(da);
								alert('Data saved.');
							});
						},
						Cancel: function() {
						  $( this ).dialog( "close" );
						  return false;
						}
					} //end buttons
	}); 
	
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
	<script>
	window.onload = firstLoad;
	function firstLoad() 
	{
		document.getElementById("link").style.color = "blue";
		var txt = document.getElementById("JobArea").value; //txt area 1		
		var txt1 = document.getElementById("JobArea1").value; //txt area 2
		var txt2 = document.getElementById("JobArea2").value; //txt area 3
		var txt3 = document.getElementById("JobArea3").value; //txt area 4
		var txt4 = document.getElementById("JobArea4").value; //txt area 5
		var txt5 = document.getElementById("JobArea5").value; //txt area 6
		
		if(txt !== '' && txt1 == '' && txt2 == '' && txt3 == '' && txt4 == '' && txt5 == '')//2
		{
			document.getElementById("link1").style.color = "blue";
			document.getElementById('link1').disabled = false;
			document.getElementById("link1").href = "../../application/thesis/select_job.php?field=JobArea1&field2=JobAreaID1";
			//document.getElementById("link1").style.color = "blue";
			//unselect1
			document.getElementById("btnCancel1").disabled = false;
			document.getElementById("btnCancel1").style.color = "blue";

			document.getElementById('link2').disabled = true;
			document.getElementById("link2").href = "#link2";
			document.getElementById("link2").style.color = "grey";
			document.getElementById("btnCancel2").style.color = "grey";
			document.getElementById("btnCancel2").disabled = true;
			
			document.getElementById('link3').disabled = true;
			document.getElementById("link3").href = "#link3";
			document.getElementById("link3").style.color = "grey";
			document.getElementById("btnCancel3").style.color = "grey";
			document.getElementById("btnCancel3").disabled = true;
			
			document.getElementById('link4').disabled = true;
			document.getElementById("link4").href = "#link4";
			document.getElementById("link4").style.color = "grey";
			document.getElementById("btnCancel4").style.color = "grey";
			document.getElementById("btnCancel4").disabled = true;
			
			document.getElementById('link5').disabled = true;
			document.getElementById("link5").href = "#link5";
			document.getElementById("link5").style.color = "grey";
			document.getElementById("btnCancel5").style.color = "grey";
			document.getElementById("btnCancel5").disabled = true;



		}
		else if(txt1 !== '' && txt2 == '' && txt3 == '' && txt4 == '' && txt5 == '')//3
		{
			document.getElementById("link1").style.color = "blue"; // 2
			document.getElementById('link1').disabled = false;
			document.getElementById("link1").href = "../../application/thesis/select_job.php?field=JobArea1&field2=JobAreaID1";
			document.getElementById("btnCancel1").disabled = false;
			document.getElementById("btnCancel1").style.color = "blue";

			document.getElementById('link2').disabled = false; // 3
			document.getElementById("link2").href = "../../application/thesis/select_job.php?field=JobArea2&field2=JobAreaID2";
			document.getElementById("link2").style.color = "blue";
			document.getElementById("btnCancel2").style.color = "blue";
			document.getElementById("btnCancel2").disabled = false;
			
			document.getElementById('link3').disabled = true;
			document.getElementById("link3").href = "#link3";
			document.getElementById("link3").style.color = "grey";
			document.getElementById("btnCancel3").style.color = "grey";
			document.getElementById("btnCancel3").disabled = true;
			
			document.getElementById('link4').disabled = true;
			document.getElementById("link4").href = "#link4";
			document.getElementById("link4").style.color = "grey";
			document.getElementById("btnCancel4").style.color = "grey";
			document.getElementById("btnCancel4").disabled = true;
			
			document.getElementById('link5').disabled = true;
			document.getElementById("link5").href = "#link5";
			document.getElementById("link5").style.color = "grey";
			document.getElementById("btnCancel5").style.color = "grey";
			document.getElementById("btnCancel5").disabled = true;
			
		}
		else if(txt2 !== '' && txt3 == '' && txt4 == '' && txt5 == '')//4
		{
			document.getElementById("link1").style.color = "blue"; // 2
			document.getElementById('link1').disabled = false;
			document.getElementById("link1").href = "../../application/thesis/select_job.php?field=JobArea1&field2=JobAreaID1";
			document.getElementById("btnCancel1").disabled = false;
			document.getElementById("btnCancel1").style.color = "blue";

			document.getElementById('link2').disabled = false; // 3
			document.getElementById("link2").href = "../../application/thesis/select_job.php?field=JobArea2&field2=JobAreaID2";
			document.getElementById("link2").style.color = "blue";
			document.getElementById("btnCancel2").style.color = "blue";
			document.getElementById("btnCancel2").disabled = false;
			
			document.getElementById('link3').disabled = false; // 4
			document.getElementById("link3").href = "../../application/thesis/select_job.php?field=JobArea3&field2=JobAreaID3";
			document.getElementById("link3").style.color = "blue";
			document.getElementById("btnCancel3").style.color = "blue";
			document.getElementById("btnCancel3").disabled = false;

			document.getElementById('link4').disabled = true;
			document.getElementById("link4").href = "#link4";
			document.getElementById("link4").style.color = "grey";
			document.getElementById("btnCancel4").style.color = "grey";
			document.getElementById("btnCancel4").disabled = true;
			
			document.getElementById('link5').disabled = true;
			document.getElementById("link5").href = "#link5";
			document.getElementById("link5").style.color = "grey";
			document.getElementById("btnCancel5").style.color = "grey";
			document.getElementById("btnCancel5").disabled = true;
			
		}
		else if(txt3 !== '' && txt4 == '' && txt5 == '')//5
		{
			document.getElementById("link1").style.color = "blue"; // 2
			document.getElementById('link1').disabled = false;
			document.getElementById("link1").href = "../../application/thesis/select_job.php?field=JobArea1&field2=JobAreaID1";
			document.getElementById("btnCancel1").disabled = false;
			document.getElementById("btnCancel1").style.color = "blue";

			document.getElementById('link2').disabled = false; // 3
			document.getElementById("link2").href = "../../application/thesis/select_job.php?field=JobArea2&field2=JobAreaID2";
			document.getElementById("link2").style.color = "blue";
			document.getElementById("btnCancel2").style.color = "blue";
			document.getElementById("btnCancel2").disabled = false;
			
			document.getElementById('link3').disabled = false; // 4
			document.getElementById("link3").href = "../../application/thesis/select_job.php?field=JobArea3&field2=JobAreaID3";
			document.getElementById("link3").style.color = "blue";
			document.getElementById("btnCancel3").style.color = "blue";
			document.getElementById("btnCancel3").disabled = false;

			document.getElementById('link4').disabled = false; // 4
			document.getElementById("link4").href = "../../application/thesis/select_job.php?field=JobArea4&field2=JobAreaID4";
			document.getElementById("link4").style.color = "blue";
			document.getElementById("btnCancel4").style.color = "blue";
			document.getElementById("btnCancel4").disabled = false;
			
			document.getElementById('link5').disabled = true;
			document.getElementById("link5").href = "#link5";
			document.getElementById("link5").style.color = "grey";
			document.getElementById("btnCancel5").style.color = "grey";
			document.getElementById("btnCancel5").disabled = true;
			
		}
		else if(txt4 !== '' && txt5 == '')//5
		{
			document.getElementById("link1").style.color = "blue"; // 2
			document.getElementById('link1').disabled = false;
			document.getElementById("link1").href = "../../application/thesis/select_job.php?field=JobArea1&field2=JobAreaID1";
			document.getElementById("btnCancel1").disabled = false;
			document.getElementById("btnCancel1").style.color = "blue";

			document.getElementById('link2').disabled = false; // 3
			document.getElementById("link2").href = "../../application/thesis/select_job.php?field=JobArea2&field2=JobAreaID2";
			document.getElementById("link2").style.color = "blue";
			document.getElementById("btnCancel2").style.color = "blue";
			document.getElementById("btnCancel2").disabled = false;
			
			document.getElementById('link3').disabled = false; // 4
			document.getElementById("link3").href = "../../application/thesis/select_job.php?field=JobArea3&field2=JobAreaID3";
			document.getElementById("link3").style.color = "blue";
			document.getElementById("btnCancel3").style.color = "blue";
			document.getElementById("btnCancel3").disabled = false;

			document.getElementById('link4').disabled = false; // 4
			document.getElementById("link4").href = "../../application/thesis/select_job.php?field=JobArea4&field2=JobAreaID4";
			document.getElementById("link4").style.color = "blue";
			document.getElementById("btnCancel4").style.color = "blue";
			document.getElementById("btnCancel4").disabled = false;
			
			document.getElementById('link5').disabled = false;
			document.getElementById("link5").href = "../../application/thesis/select_job.php?field=JobArea5&field2=JobAreaID5";
			document.getElementById("link5").style.color = "blue";
			document.getElementById("btnCancel5").style.color = "blue";
			document.getElementById("btnCancel5").disabled = false;
			
		}
		else if (txt5 !== '')
		{
			document.getElementById("link1").style.color = "blue"; // 2
			document.getElementById('link1').disabled = false;
			document.getElementById("link1").href = "../../application/thesis/select_job.php?field=JobArea1&field2=JobAreaID1";
			document.getElementById("btnCancel1").disabled = false;
			document.getElementById("btnCancel1").style.color = "blue";

			document.getElementById('link2').disabled = false; // 3
			document.getElementById("link2").href = "../../application/thesis/select_job.php?field=JobArea2&field2=JobAreaID2";
			document.getElementById("link2").style.color = "blue";
			document.getElementById("btnCancel2").style.color = "blue";
			document.getElementById("btnCancel2").disabled = false;
			
			document.getElementById('link3').disabled = false; // 4
			document.getElementById("link3").href = "../../application/thesis/select_job.php?field=JobArea3&field2=JobAreaID3";
			document.getElementById("link3").style.color = "blue";
			document.getElementById("btnCancel3").style.color = "blue";
			document.getElementById("btnCancel3").disabled = false;

			document.getElementById('link4').disabled = false; // 4
			document.getElementById("link4").href = "../../application/thesis/select_job.php?field=JobArea4&field2=JobAreaID4";
			document.getElementById("link4").style.color = "blue";
			document.getElementById("btnCancel4").style.color = "blue";
			document.getElementById("btnCancel4").disabled = false;
			
			document.getElementById('link5').disabled = false;
			document.getElementById("link5").href = "../../application/thesis/select_job.php?field=JobArea5&field2=JobAreaID5";
			document.getElementById("link5").style.color = "blue";
			document.getElementById("btnCancel5").style.color = "blue";
			document.getElementById("btnCancel5").disabled = false;
		
		}
		else //if field pertama kosong lain disable
		{
			document.getElementById("link1").style.color = "grey";
			document.getElementById('link1').disabled = true;
			document.getElementById("link1").href = "#link1";
			//document.getElementById("link1").style.color = "blue";
			//unselect1
			document.getElementById("btnCancel1").disabled = true;
			document.getElementById("btnCancel1").style.color = "grey";

			document.getElementById('link2').disabled = true;
			document.getElementById("link2").href = "#link2";
			document.getElementById("link2").style.color = "grey";
			document.getElementById("btnCancel2").style.color = "grey";
			document.getElementById("btnCancel2").disabled = true;
			
			document.getElementById('link3').disabled = true;
			document.getElementById("link3").href = "#link3";
			document.getElementById("link3").style.color = "grey";
			document.getElementById("btnCancel3").style.color = "grey";
			document.getElementById("btnCancel3").disabled = true;
			
			document.getElementById('link4').disabled = true;
			document.getElementById("link4").href = "#link4";
			document.getElementById("link4").style.color = "grey";
			document.getElementById("btnCancel4").style.color = "grey";
			document.getElementById("btnCancel4").disabled = true;
			
			document.getElementById('link5').disabled = true;
			document.getElementById("link5").href = "#link5";
			document.getElementById("link5").style.color = "grey";
			document.getElementById("btnCancel5").style.color = "grey";
			document.getElementById("btnCancel5").disabled = true;
		
		
		}

	
}
	</script>
		
	<script>
	function cancelselect(btnid) 
	{
		//var txt = textfield;
		//var btn = buttonname;
		var cancel = document.getElementById('btnCancel');
		//alert (" Luar "+btnid);
		if(btnid == 'btnCancel1')//////////////////////////unselect area2
		{
			var txt = document.getElementById("JobArea").value; //txt area 1		
			var txt1 = document.getElementById("JobArea1").value; //txt area 2
			var txt2 = document.getElementById("JobArea2").value; //txt area 3
			var txt3 = document.getElementById("JobArea3").value; //txt area 4
			var txt4 = document.getElementById("JobArea4").value; //txt area 5
			var txt5 = document.getElementById("JobArea5").value; //txt area 6
	
			if(txt2 == '' && txt3 =='' && txt4 == '' && txt5 == '')
			{
				if(txt1 == '')
				{
					var color = document.getElementById("link1").style.color;
					//alert (""+color);
					if(color == "grey")
					{
						//alert ("jadi");
						return false;
					}
					else
					{
						alert("Please do selection for Thesis Proposal Area 2 first. Unselection is aborted.");
						return false;
					}
				}
				else
				{
					if(txt == '')
					{	
						//select area 2 	
						document.getElementById('link1').disabled = true;
						document.getElementById("link1").href = "#";
						document.getElementById("link1").style.color = "grey";
						document.getElementById("JobArea1").value = ""; //txt area 2
						document.getElementById("JobAreaID1").value = ""; //txt area 2
						//unselect area 2
						document.getElementById("btnCancel1").disabled = true;
						document.getElementById("btnCancel1").style.color = "grey";
						return false;
					}
					else
					{
						//select area 3 	
						document.getElementById('link2').disabled = true;
						document.getElementById("link2").href = "#";
						document.getElementById("link2").style.color = "grey";
						document.getElementById("JobArea1").value = ""; //txt area 2 kosongkan textfield tapi link area 3 disabled
						document.getElementById("JobAreaID1").value = ""; //txt area 2
						//unselect area 3
						document.getElementById("btnCancel2").disabled = true;
						document.getElementById("btnCancel2").style.color = "grey";
						return false;
					
					}
				}
			}
			else
			{
				alert ("Unselection for Thesis Proposal Area 2 cannot be made, please unselect the latest one first.");
				return false;
			}
			
		}
		else if(btnid == 'btnCancel2')//////////////////////////unselect area3
		{
			var txt1 = document.getElementById("JobArea1").value; //txt area 2
			var txt2 = document.getElementById("JobArea2").value; //txt area 3
			var txt3 = document.getElementById("JobArea3").value; //txt area 4
			var txt4 = document.getElementById("JobArea4").value; //txt area 5
			var txt5 = document.getElementById("JobArea5").value; //txt area 6
			
			if(txt3 == '' && txt4 == '' && txt5 == '')
			{
				if(txt2 == '')
				{
					var color = document.getElementById("link2").style.color;
					//alert (""+color);
					if(color == "grey")
					{
						//alert ("jadi2");
						return false;
					}
					else
					{
						alert("Please do selection for Thesis Proposal Area 3 first. Unselection is aborted.");
						return false;
					}
				}
				else
				{
					if(txt1 == '')
					{	
						document.getElementById('link2').disabled = true;
						document.getElementById("link2").href = "#";
						document.getElementById("link2").style.color = "grey";
						document.getElementById("JobArea2").value = "";
						document.getElementById("JobAreaID2").value = ""; //txt area 2
						document.getElementById("btnCancel2").disabled = true;
						document.getElementById("btnCancel2").style.color = "grey";
						//alert("Button cancel2 baca");
						return false;
					}
					else
					{
						//select area 4 	
						document.getElementById('link3').disabled = true;
						document.getElementById("link3").href = "#";
						document.getElementById("link3").style.color = "grey";
						document.getElementById("JobArea2").value = ""; //txt area 3 kosongkan textfield tapi link area 4 disabled
						document.getElementById("JobAreaID2").value = ""; //txt area 2
						//unselect area 4
						document.getElementById("btnCancel3").disabled = true;
						document.getElementById("btnCancel3").style.color = "grey";
						return false;
					
					}
				}
			}
			else
			{
				alert ("Unselection for Thesis Proposal Area 3 cannot be made, please unselect the latest one first.");
				return false;
			}
		}
		else if(btnid == 'btnCancel3')//////////////////////////unselect area4
		{
			var txt1 = document.getElementById("JobArea1").value; //txt area 2
			var txt2 = document.getElementById("JobArea2").value; //txt area 3
			var txt3 = document.getElementById("JobArea3").value; //txt area 4
			var txt4 = document.getElementById("JobArea4").value; //txt area 5
			var txt5 = document.getElementById("JobArea5").value; //txt area 6
			
			if(txt4 == '' && txt5 == '')
			{
				if(txt3 == '')
				{
					var color = document.getElementById("link3").style.color;
					//alert (""+color);
					if(color == "grey")
					{
						//alert ("jadi3");
						return false;
					}
					else
					{
						alert("Please do selection for Thesis Proposal Area 4 first. Unselection is aborted.");
						return false;
					}
				}
				else
				{
					if(txt2 == '')
					{
						document.getElementById('link3').disabled = true;
						document.getElementById("link3").href = "#";
						document.getElementById("link3").style.color = "grey";
						document.getElementById("JobArea3").value = "";
						document.getElementById("JobAreaID3").value = "";
						document.getElementById("btnCancel3").disabled = true;
						document.getElementById("btnCancel3").style.color = "grey";
						//alert("Button cancel3 baca");
						return false;
					}
					else
					{
						//select area 5 	
						document.getElementById('link4').disabled = true;
						document.getElementById("link4").href = "#";
						document.getElementById("link4").style.color = "grey";
						document.getElementById("JobArea3").value = ""; //txt area 4 kosongkan textfield tapi link area 5 disabled
						document.getElementById("JobAreaID3").value = ""; //txt area 2
						//unselect area 5
						document.getElementById("btnCancel4").disabled = true;
						document.getElementById("btnCancel4").style.color = "grey";
						return false;
						
					}
				}
			}
			else
			{
				alert ("Unselection for Thesis Proposal Area 4 cannot be made, please unselect the latest one first.");
				return false;
			}
		}
		else if(btnid == 'btnCancel4')//////////////////////////unselect area5
		{
			var txt1 = document.getElementById("JobArea1").value; //txt area 2
			var txt2 = document.getElementById("JobArea2").value; //txt area 3
			var txt3 = document.getElementById("JobArea3").value; //txt area 4
			var txt4 = document.getElementById("JobArea4").value; //txt area 5
			var txt5 = document.getElementById("JobArea5").value; //txt area 6
			
			if(txt5 == '')
			{	
				if(txt4 == '')
				{
					var color = document.getElementById("link5").style.color;
					//alert (""+color);
					if(color == "grey")
					{
						//alert ("jadi4");
						return false;
					}
					else
					{
						alert("Please do selection for Thesis Proposal Area 5 first. Unselection is aborted.");
						return false;
					}
				}
				else
				{
					if(txt3 == '')
					{
						document.getElementById('link4').disabled = true;
						document.getElementById("link4").href = "#";
						document.getElementById("link4").style.color = "grey";
						document.getElementById("JobArea4").value = "";
						document.getElementById("JobAreaID4").value = ""; //txt area 2
						document.getElementById("btnCancel4").disabled = true;
						document.getElementById("btnCancel4").style.color = "grey";
						//alert("Button cancel4 baca");
						return false;
					}
					else
					{
						//select area 6 	
						document.getElementById('link5').disabled = true;
						document.getElementById("link5").href = "#";
						document.getElementById("link5").style.color = "grey";
						document.getElementById("JobArea4").value = ""; //txt area 5 kosongkan textfield tapi link area 6 disabled
						document.getElementById("JobAreaID4").value = ""; //txt area 2
						//unselect area 6
						document.getElementById("btnCancel5").disabled = true;
						document.getElementById("btnCancel5").style.color = "grey";
						return false;				
					}
				}
			}
			else
			{
				alert ("Unselection for Thesis Proposal Area 5 cannot be made, please unselect the latest one first.");
				return false;
			}
		}
		else if(btnid == 'btnCancel5')//////////////////////////unselect area6
		{		
				var txt5 = document.getElementById("JobArea5").value;
				if(txt5 == '')
				{
					var color = document.getElementById("link5").style.color;
					//alert (""+color);
					if(color == "grey")
					{
						//alert ("jadi");
						return false;
					}
					else
					{
						alert("Please do selection for Thesis Proposal Area 6 first. Unselection is aborted.");
						return false;
					}
				}
				else
				{
					document.getElementById('link5').disabled = true;
					document.getElementById("link5").href = "#";
					document.getElementById("link5").style.color = "grey";
					document.getElementById("JobArea5").value = "";
					document.getElementById("JobAreaID5").value = ""; //txt area 2
					document.getElementById("btnCancel5").disabled = true;
					document.getElementById("btnCancel5").style.color = "grey";
					//alert("Button cancel5 baca");
					return false; 
				}
	
		}
		else
		{
			
			return false;
		}
	
		
	
	
	}
	
	</script>
    <script>

		$(document).ready(function(){
				
				$.fn.getParameterValue2 = function(data,data2,data3,data4) {
                  //alert(data);
                  //document.form1.JobArea.value = data; $("[name='field07']").prop("disabled", false);
				  var field1 = $("#"+data2).val(data);
				  $("#"+data4).val(data3);
				  
				  
				  //alert(""+field1);
				  
				  if(data2 == 'JobArea')
				  {
					var a = document.getElementById('link1');
					a.href = "../../application/thesis/select_job.php?field=JobArea1&field2=JobAreaID1"
					document.getElementById('link1').disabled = false;
					document.getElementById("link1").style.color = "blue";
					document.getElementById("btnCancel1").disabled = false;
					document.getElementById("btnCancel1").style.color = "blue";

					//return false;
				  }
				 
				  else if(data2 == 'JobArea1') 
				  {
				  	//alert (""+data2);
				  	var a = document.getElementById('link2');
					a.href = "../../application/thesis/select_job.php?field=JobArea2&field2=JobAreaID2"
					document.getElementById('link2').disabled = false;
					document.getElementById("link2").style.color = "blue";
					document.getElementById("btnCancel2").disabled = false;
					document.getElementById("btnCancel2").style.color = "blue"
				  }
				  else if(data2 == 'JobArea2')
				  {
					var a = document.getElementById('link3');
					a.href = "../../application/thesis/select_job.php?field=JobArea3&field2=JobAreaID3"
					document.getElementById('link3').disabled = false;
					document.getElementById("link3").style.color = "blue";
					document.getElementById("btnCancel3").disabled = false;
					document.getElementById("btnCancel3").style.color = "blue"
				  }
				  else if(data2 == 'JobArea3')
				  {
				  	
				  	var a = document.getElementById('link4');
					a.href = "../../application/thesis/select_job.php?field=JobArea4&field2=JobAreaID4"
					document.getElementById('link4').disabled = false;
					document.getElementById("link4").style.color = "blue";
					document.getElementById("btnCancel4").disabled = false;
					document.getElementById("btnCancel4").style.color = "blue"
				  }
				  else if(data2 == 'JobArea4')
				  {
				  	
				  	var a = document.getElementById('link5');
					a.href = "../../application/thesis/select_job.php?field=JobArea5&field2=JobAreaID5"
					document.getElementById('link5').disabled = false;
					document.getElementById("link5").style.color = "blue";
					document.getElementById("btnCancel5").disabled = false;
					document.getElementById("btnCancel5").style.color = "blue"
				  }
				  else
				  {
				  	
				  }
						/*$('.link1').click(function () {return false;});
						$('.link1').unbind('click');*/
						/*var j1 = document.getElementByName('JobArea').value;
						alert ("sad "+j1);*/
						/*$('#link1').attr('disabled', 'disabled');
						
						$('a').live('click', function(e) {
						if ($(this).attr('disabled') == 'disabled') {
						e.preventDefault();
						}
						});*/					

						//alert ("data read");
						/*var j1= document.getElementById('JobArea');
						if(j1.value = '')
						{
							alert ("Not read la kawan");
						}
						else
						{
							
							
						}*/

						//$("[name='JobArea1']").prop("enabled", false);
						//alert ("1st");
						//document.getElementById('link1').disabled=true;
						
						//link = document.getElementById('link1').href;
						//alert (""+ document.getElementById('link').href);
						
						//document.getElementById("link1").removeAttribute("href");
						
						//$('link1').bind('click', function(e){e.preventDefault();})
						//document.getElementById("link1").style.color = "grey";
						//$('body').on('click', 'a.disabled', function(event) {event.preventDefault();});	
						//$('.link1').bind('click', false);
						/*$('#link1').click(function(e) {
						e.preventDefault();*/

                };
               //$("a").colorbox({ bottom: 100, left: "50%" }) bottom: "90%",
               $(".select_job").colorbox({ bottom: "65px", width:"70%", height:"75%", iframe:true, onClosed:function(){} }); 
                //location.reload(true); //uncomment this line if you want to refresh the page when child close
                               
          });

	</script>

<script>
$(document).ready(function() 
{
	$('#add-job').validate({
		ignore: [],         
		rules: {
					introduction: 
					{
						required: function() 
						{
							this.dataProcessor.writer.setRules( 'p',
                        {
                        indent : false,
                        breakBeforeOpen : true,
                        breakAfterOpen : false,
                        breakBeforeClose : false,
                        breakAfterClose : true
                        });
							
							CKEDITOR.instances.question.updateElement();
							CKEDITOR.instances.answer.updateElement();
							$('.error').fadeIn(500).delay(5000).fadeOut(500);
							$msg.focus();       
						}
					 }
				},
				messages: 
				{
				},
					/* use below section if required to place the error*/
				errorPlacement: function(error, element) 
			   {
					if (element.attr("name") == "introduction") 
					{
						error.insertBefore("textarea#introduction");
					} else 
					{
						error.insertBefore(element);
					}
				}
	});
});
</script>

  <form id="form1" name="form1" method="post" enctype="multipart/form-data">

<?
$sql_thesis="SELECT pt.id AS thesis_id, pt.student_matrix_no,pt.status as thesis_status,
pp.id AS proposal_id, pp.thesis_title,pp.thesis_type, pp.objective, pp.introduction,pp.description,pp.discussion_status, 
DATE_FORMAT(pp.verified_date,'%d-%b-%Y') AS verified_date, pp.verified_remarks, pp.verified_by,
pp.verified_status,pp.endorsed_by, DATE_FORMAT(pp.endorsed_date,'%d-%m-%y') as endorsed_date, 
pp.endorsed_remarks, pp.status as endorsement_status,
rps.description AS proposal_description /*, ne.name AS verified_name*/
FROM pg_thesis pt 
LEFT JOIN pg_proposal pp ON (pp.pg_thesis_id=pt.id) 
LEFT JOIN ref_proposal_status rps ON (rps.id = pp.verified_status)  
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
<legend><strong>New Application - Outline of Proposed Research/Case Study</strong></legend>	
	<table>
		<tr>
			<td><span style="color:#FF0000">*</span> Thesis / Project Title</td>
			<?if ($thesis_title == "") $thesis_title = $_POST['thesis_title'];?>
			<td><textarea type="text" name="thesis_title" cols="100" rows="3" id="thesis_title"><?=$thesis_title;?></textarea></td>			
		</tr>
		<tr>
			<td>Proposal Type</td>
			<?if ($thesis_type == "") $thesis_type = $_POST['thesis_type'];?>
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
			<?if ($introduction == "") $introduction = $_POST['introduction'];?>
			<td><textarea name="introduction" cols="30" class="ckeditor" rows="3" id="introduction" ><?=$introduction;?></textarea></td>
		</tr>
		<tr>
			<td><span style="color:#FF0000">*</span> Objective</td>
			<?if ($objective == "") $objective = $_POST['objective'];?>
			<td><textarea name="objective" cols="30" class="ckeditor" rows="3" ><?=$objective;?></textarea></td>
		</tr>
		<tr>
			<td><span style="color:#FF0000">*</span> Brief Description</td>
			<?if ($description == "") $description = $_POST['description'];?>
			<td><textarea name="description" cols="30" class="ckeditor" rows="3" ><?=$description;?></textarea>
</td>
		</tr>
	</table>

	   <input type="hidden" name="discussion_status" id="discussion_status" value="Y" />
		
	  
  <div>
	   
	   
	   <br />
		<? 
		$count_total_result = "SELECT COUNT(*) AS total FROM pg_meeting_detail 
		WHERE pg_proposal_id = '$proposal_id' 
		AND student_matrix_no = '$user_id'";
		$db->query($count_total_result);
		$db->next_record();
    	$a = $db->f('total');
		if ($a == '0')
		{
			$b = '';
		}
		else
		{
			$b = "(".$a.")";
		}					
		
		?>
											
	   <? 
	   $sqlUpload="SELECT COUNT(*) as total  FROM file_upload_proposal 
		WHERE pg_proposal_id='$proposal_id' 
		AND student_matrix_no = '$user_id'
		AND attachment_level='S' ";			

		$result = $dbb->query($sqlUpload); 
		$dbb->next_record();
    	$attachment = $dbb->f('total');

		if($attachment == '0')
		{
			$a = '';
		}
		else
		{
			$a = "(".$attachment.")";
		}
 ?>
											
	   <? $sqlPgProposalArea = "SELECT pg_proposal_id,job_id1_area,job_id2_area,job_id3_area,job_id4_area,job_id5_area,job_id6_area
	 										FROM pg_proposal_area
											WHERE pg_proposal_id = '$proposal_id'"; ?>									
	   <fieldset style="width:800px">
	   <legend><strong>Thesis Proposal Area</strong></legend>	
	  
       <table width="958"  align="center">
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
			?>
               <input id ="JobArea"  type="text" size="30" name="JobArea" readonly="" value="<?php echo isset($JobArea) ? $JobArea : "" ?>" />
               <a id = "link" class='select_job' href="../../application/thesis/select_job.php?field=JobArea&field2=JobAreaID">[Select]</a>
               <input id ="JobAreaID"  type="hidden" size="30" name="JobAreaID" readonly="" value="<?php echo isset($JobAreaID) ? $JobAreaID : "" ?>" /></td>
           <?
			 
			 }
			 else
			 {?>
           <input id ="JobArea"  type="text" size="30" name="JobArea" readonly="" value="<?php echo isset($JobArea) ? $JobArea : "" ?>" />
           <a id = "link" class='select_job' href="../../application/thesis/select_job.php?field=JobArea&field2=JobAreaID">[Select]</a>
           <input id ="JobAreaID"  type="hidden" size="30" name="JobAreaID" readonly="" value="<?php echo isset($JobAreaID) ? $JobAreaID : "" ?>" />
           <?
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

			?>
             <input id ="JobArea3"  type="text" size="30" name="JobArea3" readonly="" value="<?php echo isset($JobArea3) ? $JobArea3 : "" ?>" />
             <a id = "link3" class='select_job' href="../../application/thesis/select_job.php?field=JobArea3&field2=JobAreaID3">[Select]</a>
               <input id ="JobAreaID3"  type="hidden" size="30" name="JobAreaID3" readonly="" value="<?php echo isset($JobAreaID3) ? $JobAreaID3 : "" ?>" />
			   <!--<input type="submit" name="btnCancel3" id="btnCancel3" align="center"  value="Cancel" onclick="return cancelselect('btnCancel3')" />-->
		   <a id = "btnCancel3" name = "btnCancel3" href="#" onclick="return cancelselect('btnCancel3')">[Unselect]</a></td>
           <?
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

			?>
               <input id ="JobArea1"  type="text" size="30" name="JobArea1" readonly="" value="<?php echo isset($JobArea1) ? $JobArea1 : "" ?>" />
               <a id = "link1" name = "link1" class='select_job' href="#">[Select]</a>
               <input id ="JobAreaID1"  type="hidden" size="30" name="JobAreaID1" readonly="" value="<?php echo isset($JobAreaID1) ? $JobAreaID1 : "" ?>" />
               <!--<input type="submit" name="btnCancel1" id="btnCancel1" align="center"  value="Cancel" onclick="return cancelselect('btnCancel1')" />-->
			   <a id = "btnCancel1" name = "btnCancel1" href="#" onclick="return cancelselect('btnCancel1')">[Unselect]</a></td>
           <?
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

			?>
               <input id ="JobArea4"  type="text" size="30" name="JobArea4" readonly="" value="<?php echo isset($JobArea4) ? $JobArea4 : "" ?>" />
               <a id = "link4" class='select_job' href="../../application/thesis/select_job.php?field=JobArea4&field2=JobAreaID4">[Select]</a>
               <input id ="JobAreaID4"  type="hidden" size="30" name="JobAreaID4" readonly="" value="<?php echo isset($JobAreaID4) ? $JobAreaID4 : "" ?>" />
           <!--<input type="submit" name="btnCancel4" id="btnCancel4" align="center"  value="Cancel" onclick="return cancelselect('btnCancel4')" />-->
           <a id = "btnCancel4" name = "btnCancel4" href="#" onclick="return cancelselect('btnCancel4')">[Unselect]</a></td>
           <?
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

			?>
               <input id ="JobArea2"  type="text" size="30" name="JobArea2" readonly="" value="<?php echo isset($JobArea2) ? $JobArea2 : "" ?>" />
               <a id = "link2" class='select_job' href="../../application/thesis/select_job.php?field=JobArea2&field2=JobAreaID2">[Select]</a>
               <input id ="JobAreaID2"  type="hidden" size="30" name="JobAreaID2" readonly="" value="<?php echo isset($JobAreaID2) ? $JobAreaID2 : "" ?>" />
               <!--<input type="submit" name="btnCancel2" id="btnCancel2" align="center"  value="Cancel" onclick="return cancelselect('btnCancel2')" />-->
           	   <a id = "btnCancel2" name = "btnCancel2" href="#" onclick="return cancelselect('btnCancel2')">[Unselect]</a></td>
           <?
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

			?>
				<input id ="JobArea5"  type="text" size="30" name="JobArea5" readonly="" value="<?php echo isset($JobArea5) ? $JobArea5 : "" ?>" />
				<a id = "link5" class='select_job' href="../../application/thesis/select_job.php?field=JobArea5&field2=JobAreaID5">[Select]</a>
				<input id ="JobAreaID5"  type="hidden" size="30" name="JobAreaID5" readonly="" value="<?php echo isset($JobAreaID5) ? $JobAreaID5 : "" ?>" />
				<!--<input type="submit" name="btnCancel5" id="btnCancel5" align="center"  value="Cancel" onclick="return cancelselect('btnCancel5')" />-->
				<a id = "btnCancel5" name = "btnCancel5" href="#" onclick="return cancelselect('btnCancel5')">[Unselect]</a></td>
           <?
			 ?>
         </tr>
       </table>
	   </fieldset>
	   <br/>
	   <table>
		<tr>
			<td><button type = "button" name="btnDiscussionDetail" value="Discussion Detail" onclick="return newDicussion('<?=$proposal_id?>')">
			Discussion Detail <FONT COLOR="#FF0000"><sup><?=$b?></strong></FONT></button></td>
			<td><button type="button" name="btnAttachment" value="Attachment" onclick="return newAttachment('<?=$proposal_id?>')" >
			Attachment <FONT COLOR="#FF0000"><sup><?=$a?></sup></FONT></button></td>
		
		</tr>
	</table>  
  </fieldset>
   <? /*$sqlUpload="SELECT COUNT(*) as total  FROM file_upload_proposal 
		WHERE pg_proposal_id='$proposal_id' 
		AND student_matrix_no = '$user_id'
		AND attachment_level='S' ";			

		$result = $dbb->query($sqlUpload); 
		$dbb->next_record();
    	$attachment = $dbb->f('total');*/
	?>
	<table>
		<tr>
			<td><input type="submit" name="btnSave" id="btnSave" align="center"  value="Save as Draft" /></td>
			<td><input type="submit" name="btnSubmit" id="btnSubmit" align="center"  value="Submit" onClick="return respConfirm('<?=$a?>')" /></td>
			<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../student/student_programme.php';" /></td>
			<td><span style="color:#FF0000">Note:</span> Field marks with (<span style="color:#FF0000">*</span>) is compulsory.</td>				
		
		</tr>
	</table>	
	<?}
else {	
}?>
  </form>
</body>
</html>




