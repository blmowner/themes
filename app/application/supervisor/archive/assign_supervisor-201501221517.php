<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: assign_supervisor.php
//
// Created by: Zuraimi on 14-Jan-2015
// Modified by: Zuraimi on 14-Jan-2015l
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

session_start();
$userid=$_SESSION['user_id'];
$studentMatrixNo=$_SESSION['studentMatrixNo'];
$curdatetime = date("Y-m-d H:i:s");

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

if(isset($_POST['btnSubmit']) && ($_POST['btnSubmit'] <> "")) {
	$myVerifiedStatus=$_POST['myVerifiedStatus'];
	$myVerifiedRemarks=$_POST['myVerifiedRemarks'];
	
	/*echo "btnSubmit senateDate ".$senateDate."<br/>";
	echo "btnSubmit respondedByDate ".$respondedByDate."<br/>";
	echo "btnSubmit myVerifiedStatus ".$myVerifiedStatus."<br/>";
	echo "btnSubmit myVerifiedRemarks ".$myVerifiedRemarks."<br/>";*/
	
	$myApprovalBox=$_POST['myApprovalBox'];
	$myPgThesisId=$_SESSION['myPgThesisId'];
	$myStudentName=$_SESSION['myStudentName'];
	$myReportDate=$_SESSION['myReportDate'];


	while (list ($key,$val) = @each ($myApprovalBox)) 
	{
		/*echo "myApprovalBox key ".$key." "."val ".$val."<br/>";
		echo "myPgThesisId [".$val."] "."val ".$myPgThesisId[$val]."<br/>";
		echo "myStudentMatrixNo [".$val."] "."val ".$myStudentMatrixNo[$val]."<br/>";
		echo "myStudentName [".$val."] "."val ".$myStudentName[$val]."<br/>";
		echo "myReportDate [".$val."] "."val ".$myReportDate[$val]."<br/>";
		echo "myProposalId [".$val."] "."val ".$myProposalId[$val]."<br/>";*/

		
		$sql5 = "UPDATE pg_supervisor
				SET assigned_by = '$userid', assigned_date = '$curdatetime',
				assigned_remarks = '$myVerifiedRemarks', modify_by = '$userid', modify_date = '$curdatetime'
				WHERE pg_student_matrix_no= '$myStudentMatrixNo[$val]'
				AND STATUS = 'A'";
		
		$result5 = $dbd->query($sql5); 
		//echo "sql5 ".$sql5;
		//var_dump($dbd);
		$dbd->next_record();
		
		$tmpSenateDate = date('Y-m-d', strtotime($senateDate));
	
	//report_date,'%Y-%m-%d %h:%i:%s')
	
	 $sql6_1 = "SELECT id, report_date, thesis_title, thesis_type, introduction, objective, 
				description,discussion_status,proposal_remarks, verified_by, 
				IFNULL(verified_date,'0000-00-00 00:00:00') as verified_date, verified_status, verified_remarks, endorsed_by, 
				IFNULL(endorsed_date,'0000-00-00 00:00:00') as endorsed_date, endorsed_remarks,status, 
				insert_by, IFNULL(insert_date,'0000-00-00 00:00:00') as insert_date, 
				modify_by, IFNULL(modify_date,'0000-00-00 00:00:00') as modify_date, 
				pg_thesis_id, pg_proposal_approval_id
				FROM 
				pg_proposal 
				WHERE id = '$myProposalId[$val]'";

		$result6_1 = $dbg->query($sql6_1); 			
		//echo "sql6_1 ".$sql6_1;
		//exit();
		//var_dump($dbg);
		$dbg->next_record();


		$id = $dbg->f('id'); 
		$reportDate = $dbg->f('report_date'); 			
		$thesisTitle = $dbg->f('thesis_title'); 
		$thesisType = $dbg->f('thesis_type'); 
		$introduction = $dbg->f('introduction'); 
		$objective = $dbg->f('objective'); 
		$description = $dbg->f('description'); 
		$discussionStatus = $dbg->f('discussion_status'); 
		$proposalRemarks = $dbg->f('proposal_remarks'); 
		$verifiedBy = $dbg->f('verified_by'); 
		$verifiedDate = $dbg->f('verified_date'); 
		$verifiedStatus = $dbg->f('verified_status'); 
		$verifiedRemarks = $dbg->f('verified_remarks');
		$endorsedBy = $dbg->f('endorsed_by'); 
		$endorsedDate = $dbg->f('endorsed_date'); 
		$endorsedRemarks = $dbg->f('endorsed_remarks'); 
		$status = $dbg->f('status'); 
		$insertBy = $dbg->f('insert_by'); 
		$insertDate = $dbg->f('insert_date'); 
		$modifyBy = $dbg->f('modify_by'); 
		$modifyDate = $dbg->f('modify_date'); 
		$pgThesisId = $dbg->f('pg_thesis_id'); 
		$pgProposalApprovalId = $dbg->f('pg_proposal_approval_id');
		
		
		
		
	$proposal_id = "P".runnum('id','pg_proposal');	
	
		$sql6_2 = "INSERT INTO pg_proposal 
				(id, report_date, thesis_title, thesis_type, introduction, objective, description, discussion_status, proposal_remarks,
				verified_by, verified_date, verified_status, verified_remarks, 
				endorsed_by, endorsed_date, endorsed_remarks, status, 
				insert_by, insert_date, modify_by, modify_date, 
				pg_thesis_id)
				VALUES
				('$proposal_id', '$reportDate', '$thesisTitle', '$thesisType', '$introduction', '$objective', '$description', '
				$discussionStatus', '$proposalRemarks', 
				'$userid', '$curdatetime', '$myVerifiedStatus', '$myVerifiedRemarks', 
				'$endorsedBy', '$endorsedDate', '$endorsedRemarks','$status',
				'$insertBy', '$insertDate', '$userid', '$curdatetime', '$pgThesisId')";
			
		$result6_2 = $dbg->query($sql6_2); 			
		//echo "sql6_2 ".$sql6_2;
		//var_dump($dbg);
		$dbg->next_record();
		//$dbg->free();
		//exit();	
		
	$sql6 = "UPDATE pg_proposal 		
			SET archived_status = 'ARC', archived_date = '$curdatetime' 			
			WHERE id = '$myProposalId[$val]'";
	
	/*$sql6 = "UPDATE pg_proposal 		
			SET status = 'ARC', verified_by='$userid', verified_date = '$curdatetime', verified_status = 'ARC', 
			modify_by = '$modifyBy', modify_date = '$modifyDate'
			WHERE id = '$myProposalId[$val]'";*/
	
	$result6 = $dbg->query($sql6); 			
	//echo "sql6 ".$sql6;
	//var_dump($dbg);
	$dbg->next_record();
	
	$sql4 = "UPDATE pg_thesis
			SET ref_thesis_status_id_proposal = 'INP', modify_by = '$userid', modify_date = '$curdatetime'
			WHERE id = '$myPgThesisId[$val]'
			AND student_matrix_no = '$myStudentMatrixNo[$val]'";

	$result4 = $dbf->query($sql4); 
	//echo "sql4 ".$sql4;
	//var_dump($dbf);
	$dbf->next_record();
	//$dbf->free();	
	}
}

///////////////////////////////////////////////////////////////
$sql2 = "SELECT a.pg_thesis_id, a.id, DATE_FORMAT(a.report_date,'%d-%b-%Y') AS theReportDate, a.thesis_title, a.thesis_type, 
		b.description AS theThesisTypeDescription, a.introduction, a.objective, a.description, a.status, a.discussion_status, a.proposal_remarks, c.description AS theProposalStatusDescription, d.student_matrix_no, e.name, d.supervisor_status,
		a.verified_status, c2.description as verified_desc
		FROM pg_proposal a
		LEFT JOIN ref_thesis_type b ON (b.id = a.thesis_type) 
		LEFT JOIN ref_proposal_status c ON (c.id = a.status) 
		LEFT JOIN ref_proposal_status c2 ON (c2.id = a.verified_status) 
		LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id) 
		LEFT JOIN student e ON (e.matrix_no = d.student_matrix_no) 
		WHERE a.archived_status IS NULL";
		
$result2 = $db->query($sql2); 
//echo $sql2; 

//var_dump($db);
$db->next_record();
		

$sql3 = "SELECT value
		FROM pg_parameter
		WHERE id = 'RESPOND_DURATION'
		AND STATUS = 'A'";

$result3 = $dbb->query($sql3); 
//echo $sql3;
//var_dump($dbb);
$dbb->next_record();
		
	if(isset($_POST['btnReview']) && ($_POST['btnReview'] <> ""))
	{			
		
		/*echo "btnSubmit senateDate ".$senateDate."<br/>";
		echo "btnSubmit respondedByDate ".$respondedByDate."<br/>";
		echo "btnSubmit myVerifiedStatus ".$myVerifiedStatus."<br/>";
		echo "btnSubmit myVerifiedRemarks ".$myVerifiedRemarks."<br/>";*/
		
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
						SET verified_status = 'REV', verified_by = '$user_id', verified_date = '$curdatetime',
						modify_by = '$user_id', modify_date = '$curdatetime'
						WHERE id = '$myProposalId[$val]'
						AND pg_thesis_id = '$myPgThesisId[$val]'
						AND archived_status is null";
						
			//echo "sql7 ".$sql7;
			$db->query($sql7);
		}
	}
			
?>

	

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
	    <style type="text/css"></style>
		<script language="JavaScript" src="../../../lib/js/windowopen.js"></script>
		</head> 
	<body>  
		<form id="form1" name="form1" method="post" enctype="multipart/form-data">				
			
				<?  
				$row_cnt = mysql_num_rows($result2);
				if ($row_cnt>0) {?>
				<fieldset>
				<legend><strong>List of Thesis Proposal</strong></legend>
					<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1">
						<tr>							
							<td><strong>No.</strong></td>											
							<td><strong>Student Matrix No.</strong></td>												
							<td><strong>Student Name</td>
							<td><strong>Thesis/Project ID</strong></td>
							<td><strong>Thesis/Project Title</strong></td>
							<td><strong>Attachment by Student</strong></td>
							<td><strong>Supervisor</strong></td>																																			
						</tr>
						<?
						$no=0;
						do {						
							$pgThesisId=$db->f('pg_thesis_id');	
							$studentMatrixNo=$db->f('student_matrix_no');
							$studentName=$db->f('name');						
							$proposalId=$db->f('id');
							$reportDate=$db->f('theReportDate');
							$thesisTitle=$db->f('thesis_title');
							$supervisorStatus=$db->f('supervisor_status');	
							$verifiedDesc=$db->f('verified_desc');							
																
						?>
							<tr>
								<input type="hidden" name="myProposalId[]" size="12" id="proposalId" value="<?=$proposalId;?>"/>
								<?$myProposalId[$no]=$proposalId;?>
								<?//echo "myProposalId[$no] ".$myProposalId[$no];?>
								
								<td><?=$no+1;?>.</td>	
								
								<td><label name="myStudentMatrixNo[]" size="15" id="studentMatrixNo" ></label><?=$studentMatrixNo;?></td>
								<?$myStudentMatrixNo[$no]=$studentMatrixNo;?>
								<?//echo "myStudentMatrixNo[$no] ".$myStudentMatrixNo[$no];?>
								
								<td><label name="myStudentName[]" size="30" id="studentName" </label><?=$studentName;?></td>						
								
								<td><a href="assign_supervisor_outline.php?thesisId=<? echo $pgThesisId;?>&proposalId=<? echo $proposalId;?>" name="myPgThesisId[]" value="<?=$pgThesisId?>" title="Outline of Proposed Case Study by the Student - Read more..."><?=$pgThesisId;?></a></td>	
								<?$myPgThesisId[$no]=$pgThesisId;?>
								<?//echo "myPgThesisId[$no] ".$myPgThesisId[$no];?>
								
								<td><label name="myThesisTitle[]" cols="45" id="thesisTitle"><?=$thesisTitle?></label></td>
								
								
								<?php
									$sqlUpload="SELECT * FROM file_upload_proposal 
									WHERE pg_proposal_id='$proposalId' 
									AND attachment_level='S' ";			

									$result = $db_klas2->query($sqlUpload); //echo $sql;
									$row_cnt = mysql_num_rows($result);
									if ($row_cnt>0)
									{
										?><td align="left"><?
										while($row = mysql_fetch_array($result)) 					
										{ 
											?>
													<a href="download.php?fc=<?=$row["fu_cd"];?>&al=S"><?=$row["fu_document_filename"];?><img src="../images/download.png" width="20" height="19" style="border:0px;" title="Download"></a><br/>
													<?//href='assign_supervisor_download.php?>
															
												
										<?}
										?></td><?
									}
									else {
										?><td>No attachment</td><?
									}
								?>
															
									<td>
								<?$_SESSION['studentMatrixNo']=$studentMatrixNo;
								if ($supervisorStatus =='A') {?>
									<a href="../supervisor/assign_supervisor_change.php?pid=<?php echo $myProposalId[$no]?>&tid=<? echo $myPgThesisId[$no]?>&mn=<? echo $myStudentMatrixNo[$no]?>">Change <img src="../images/edit.jpg" width="20" height="19" style="border:0px;" title="Supervisor has been assigned" ></a><br/><br/><a href="../supervisor/assign_supervisor_view.php?sname=<?php echo $studentName?>&mn=<? echo $myStudentMatrixNo[$no]?>">View</a> 
								<?}
								else {?>
									<a href="../supervisor/assign_supervisor_change.php?pid=<?php echo $myProposalId[$no]?>&tid=<? echo $myPgThesisId[$no]?>&mn=<? echo $myStudentMatrixNo[$no]?>">Assign <img src="../images/red_edit.jpg" width="20" height="19" style="border:0px;" title="Supervisor is not assigned yet" ></a>
								<?}?>															
							</td></tr>
						<?
						$no=$no+1;
						}while($db->next_record());	
						?>
						<?$_SESSION['myPgThesisId'] = $myPgThesisId;?>				
						<?$_SESSION['myStudentMatrixNo'] = $myStudentMatrixNo;?>
							
					</table>
					
					<br/>
					
					</fieldset>
					<br/>
					
		
					<?
				}
				else {
					?>
					<fieldset>
					<legend><strong>Notification Message</strong></legend>
						<table>
							<tr>
								<td>
									<p>Currently no student available to assign/re-assign with a supervisor.</p>
								</td>
							</tr>
						</table>
					</fieldset>
					<?
				}				
				?>					
		</form>
	</body>
</html>




