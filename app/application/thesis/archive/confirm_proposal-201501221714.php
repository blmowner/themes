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
		echo "myProposalId [".$val."] "."val ".$myProposalId[$val]."<br/>";
		echo "myVerifiedStatus [".$val."] "."val ".$myVerifiedStatus."<br/>";*/

		$sql5 = "UPDATE pg_supervisor
				SET assigned_by = '$userid', assigned_date = '$curdatetime',
				modify_by = '$userid', modify_date = '$curdatetime'
				WHERE pg_student_matrix_no= '$myStudentMatrixNo[$val]'
				AND STATUS = 'A'";
				
		$result5 = $dbd->query($sql5); 
		//echo "sql5 ".$sql5;
		//var_dump($dbd);
		$dbd->next_record();
		
		//$tmpSenateDate = date('Y-m-d', strtotime($senateDate));
	
	//report_date,'%Y-%m-%d %h:%i:%s')
	
	 $sql6_1 = "SELECT id, report_date, thesis_title, thesis_type, introduction, objective, 
				description,discussion_status,proposal_remarks, verified_by, 
				IFNULL(verified_date,'0000-00-00 00:00:00') as verified_date, verified_status, verified_remarks, endorsed_by, 
				IFNULL(endorsed_date,'0000-00-00 00:00:00') as endorsed_date, endorsed_remarks,status, 
				marked_by, marked_status, IFNULL(marked_date,'0000-00-00 00:00:00') as marked_date,
				insert_by, IFNULL(insert_date,'0000-00-00 00:00:00') as insert_date, 
				modify_by, IFNULL(modify_date,'0000-00-00 00:00:00') as modify_date, 
				pg_thesis_id, pg_proposal_approval_id
				FROM pg_proposal 
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
		
		
	$proposal_id = "P".runnum('id','pg_proposal');	
	
		$sql6_2 = "INSERT INTO pg_proposal 
				(id, report_date, thesis_title, thesis_type, introduction, objective, description, discussion_status, proposal_remarks,
				verified_by, verified_date, verified_status, verified_remarks, endorsed_by, endorsed_date, endorsed_remarks, status,
				marked_by, marked_date, marked_status, insert_by, insert_date, modify_by, modify_date, 
				pg_thesis_id)
				VALUES
				('$proposal_id', '$reportDate', '$thesisTitle', '$thesisType', '$introduction', '$objective', '$description', '
				$discussionStatus', '$proposalRemarks', 
				'$userid', '$curdatetime', '$myVerifiedStatus', '$verifiedRemarks', 
				'$endorsedBy', '$endorsedDate', '$endorsedRemarks','$status', '$markedBy','$markedDate','$markedStatus',
				'$insertBy', '$insertDate', '$userid', '$curdatetime', '$pgThesisId')";
			
		$result6_2 = $dbg->query($sql6_2); 			
		//echo "sql6_2 ".$sql6_2;
		//var_dump($dbg);
		$dbg->next_record();
		//$dbg->free();
		//exit();	
		
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
		
		
	$sql6 = "UPDATE pg_proposal 		
			SET archived_status = 'ARC', archived_date = '$curdatetime' 			
			WHERE id = '$myProposalId[$val]'";
	
	$result6 = $dbg->query($sql6); 			
	//echo "sql6 ".$sql6;
	//var_dump($dbg);
	$dbg->next_record();
	
	}
}

///////////////////////////////////////////////////////////////
/*$sql2 = "SELECT a.pg_thesis_id, a.id, DATE_FORMAT(a.report_date,'%d-%b-%Y') AS theReportDate, a.thesis_title, a.thesis_type, 
		b.description AS theThesisTypeDescription, a.introduction, a.objective, a.description, a.status, a.discussion_status, a.proposal_remarks, c.description AS theProposalStatusDescription, d.student_matrix_no, e.name, d.supervisor_status,
		a.verified_status, c2.description as verified_desc, a.verified_remarks, a.marked_status
		FROM pg_proposal a
		LEFT JOIN  ref_thesis_type b ON (b.id = a.thesis_type) 
		LEFT JOIN ref_proposal_status c ON (c.id = a.status)
		LEFT JOIN ref_proposal_status c2 ON (c2.id = a.verified_status)
		LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id) 
		LEFT JOIN student e ON (e.matrix_no = d.student_matrix_no) 
		WHERE a.verified_status in ('INP') 
		AND a.archived_status IS NULL 
		AND a.status = 'OPN'
		AND d.status = 'INP'";	*/
		
$sql2 = "SELECT a.pg_thesis_id, a.id, DATE_FORMAT(a.report_date,'%d-%b-%Y') AS theReportDate, a.thesis_title, a.thesis_type, 
		b.description AS theThesisTypeDescription, a.introduction, a.objective, a.description, a.status, a.discussion_status, a.proposal_remarks, c.description AS theProposalStatusDescription, d.student_matrix_no, e.name, d.supervisor_status,
		a.verified_status, c2.description as verified_desc, a.verified_remarks, a.marked_status
		FROM pg_proposal a
		LEFT JOIN  ref_thesis_type b ON (b.id = a.thesis_type) 
		LEFT JOIN ref_proposal_status c ON (c.id = a.status)
		LEFT JOIN ref_proposal_status c2 ON (c2.id = a.verified_status)
		LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id) 
		LEFT JOIN student e ON (e.matrix_no = d.student_matrix_no) 
		WHERE a.archived_status IS NULL 
		AND a.status = 'OPN'
		AND d.status = 'INP'";		
		
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
		
if(isset($_POST['btnMark']) && ($_POST['btnMark'] <> ""))
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
		
		/*echo "btnSubmit senateDate ".$senateDate."<br/>";
		echo "btnSubmit respondedByDate ".$respondedByDate."<br/>";
		echo "btnSubmit myVerifiedStatus ".$myVerifiedStatus."<br/>";
		echo "btnSubmit myVerifiedRemarks ".$myVerifiedRemarks."<br/>";*/
		
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
				$result2 = $db->query($sql2); 
				$row_cnt = mysql_num_rows($result2);
				if ($row_cnt>0) {?>
				<fieldset>
				<legend><strong>List of Thesis Proposal</strong></legend>
					<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1">
						<tr>						
							<td><strong>Tick</strong></td>	
							<td><strong>No.</strong></td>
							<td><strong>Thesis/Project ID</strong></td>
							<td><strong>Thesis/Project Title</strong></td>
							<td><strong>Student Name</strong></td>
							<td><strong>Attachment by Student</strong></td>
							<td><strong>Attachment by Faculty</strong></td>
							<td><strong>Supervisor</strong></td>
							<td><strong>Status</strong></td>
						</tr>
						<?
						$no=0;
						while($db->next_record()) {						
							$pgThesisId=$db->f('pg_thesis_id');	
							$studentMatrixNo=$db->f('student_matrix_no');
							$studentName=$db->f('name');						
							$proposalId=$db->f('id');
							$reportDate=$db->f('theReportDate');
							$thesisTitle=$db->f('thesis_title');
							$supervisorStatus=$db->f('supervisor_status');	
							$verifiedDesc=$db->f('verified_desc');	
							$verifiedRemarks=$db->f('verified_remarks');	
							$markedStatus=$db->f('marked_status');
							$verifiedStatus=$db->f('verified_status');							
																
						?>
							<tr>
								<? if ($verifiedStatus=='APP'){
									?><td><label><input name="myApprovalBox[]" type="checkbox" value="<?=$no;?>" disabled="disabled"/></label></td><?
								}
								else {
									?><td><label><input name="myApprovalBox[]" type="checkbox" value="<?=$no;?>"/></label></td><?
								}
								?>
								
								
								<input type="hidden" name="myProposalId[]" size="12" id="proposalId" value="<?=$proposalId;?>"/>
								<?$myProposalId[$no]=$proposalId;?>
								<?//echo "myProposalId[$no] ".$myProposalId[$no];?>
								
								<td><?=$no+1;?>.</td>	

								
								<td><a href="proposal_outline.php?thesisId=<? echo $pgThesisId;?>&proposalId=<? echo $proposalId;?>" name="myPgThesisId[]" value="<?=$pgThesisId?>" title="Outline of Proposed Case Study by the Student - Read more..."><?=$pgThesisId;?></a><br/>
						
								<?if ($verifiedRemarks == null || $verifiedRemarks ==""){?>
									<img src="../images/red_edit.jpg" width="20" height="19" style="border:0px;" title="Faculty Remark is not yet provided" >Enter Remarks</a>	
								<?}
								else {
								?>
									<img src="../images/edit.jpg" width="20" height="19" style="border:0px;" title="Faculty Remark is provided" >Read Remarks</a>	
								<?
								}
								if ($markedStatus == "MAR"){?>
									<img src="../images/mark.jpg" width="20" height="19" style="border:0px;" title="Thesis is marked for review" ></a>								
								<?}?></td>	
						
								<?$myPgThesisId[$no]=$pgThesisId;?>
								<?//echo "myPgThesisId[$no] ".$myPgThesisId[$no];?>
								
								<td><label name="myThesisTitle[]" cols="45" id="thesisTitle"><?=$thesisTitle?></label></td>
								
								<td><label name="myStudentName[]" size="30" id="studentName" ></label><?=$studentName;?><br/>(<?=$studentMatrixNo;?>)</td>
								<?$myStudentMatrixNo[$no]=$studentMatrixNo;?>
								
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
													
															
												
										<?}
										?></td><?
									}
									else {
										?><td>No attachment</td><?
									}
								?>
								
								
								<?php
									$sqlUpload="SELECT * FROM file_upload_proposal 
									WHERE pg_proposal_id='$proposalId' 
									AND attachment_level='F' ";			

									$result = $db_klas2->query($sqlUpload); //echo $sql;
									$row_cnt = mysql_num_rows($result);
									if ($row_cnt>0)
									{
										?><td align="left"><?
										while($row = mysql_fetch_array($result)) 					
										{ 
											?>
												<a href="download.php?fc=<?=$row["fu_cd"];?>&al=F"><?=$row["fu_document_filename"];?><img src="../images/download.png" width="20" height="19" style="border:0px;" title="Download"></a>
												<a href="delete_attachment_confirm.php?fc=<?=$row["fu_cd"];?>&al=F" ><img src="../images/delete_on.gif" width="20" height="19" style="border:0px;" title="Delete"></a><br/>
												
												
												
															
												
										<?}
										?><input type="submit" name="submit" value="Upload" onclick="javascript:open_win('confirm_proposal_upload.php?pid=<?=$proposalId;?>&al=F',480,280,0,0,0,1,0,1,1,0,5,'winupload'); "/></td><?
									}
									else {
										?><td>No attachment<br/><input type="submit" name="submit" value="Upload" onclick="javascript:open_win('confirm_proposal_upload.php?pid=<?=$proposalId;?>&al=F',480,280,0,0,0,1,0,1,1,0,5,'winupload'); "/></td><?
									}
								?>
								
								
								<td>
								<?$_SESSION['studentMatrixNo']=$studentMatrixNo;
								if ($supervisorStatus =='A') {?>
									<a href="../supervisor/edit_supervisor.php?pid=<?php echo $myProposalId[$no]?>&tid=<? echo $myPgThesisId[$no]?>&mn=<? echo $myStudentMatrixNo[$no]?>">Change <img src="../images/edit.jpg" width="20" height="19" style="border:0px;" title="Supervisor has been assigned" ></a><br/><br/><a href="../supervisor/view_supervisor.php?sname=<?php echo $studentName?>&mn=<? echo $myStudentMatrixNo[$no]?>">View</a> 
								<?}
								else {?>
									<a href="../supervisor/edit_supervisor.php?pid=<?php echo $myProposalId[$no]?>&tid=<? echo $myPgThesisId[$no]?>&mn=<? echo $myStudentMatrixNo[$no]?>">Assign <img src="../images/red_edit.jpg" width="20" height="19" style="border:0px;" title="Supervisor is not assigned yet" ></a>
								<?}?>															
							</td>
							<td><label name="myDerifiedDesc[]" cols="45" id="verifiedDesc"><?=$verifiedDesc?></label></td>
							
							</tr>
						<?
						$no=$no+1;
						
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
								<input name="myVerifiedStatus" type="radio" value="APP" checked="checked"/>
								Approved
								<input type="radio" name="myVerifiedStatus" value="REQ"/>Request with Changes
								<input type="radio" name="myVerifiedStatus" value="DIS"/>Disapproved	  
							</td>
						</tr>
					</table>
					</fieldset>
					<br/>
					<table>					
						<tr>				
							<td><input type="button" name="btnPrintProposal" value="Print Proposal List" onclick="javascript:document.location.href='pdf_senate_approval.php';" /></td>
							<td><input type="submit" name="btnSubmit" value="Submit" /></td>
						</tr>
					</table>							
					<?
				}
				else {
					?>
					<fieldset>
						<legend><strong>Notification Message</strong></legend>	
						<table>
							<tr>
								<td>
									<p>Currently no proposal available for verification and supervisor invitation.</p>
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




