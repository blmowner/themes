<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: approve_request_proposal.php
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

if(isset($_POST['btnSearch']) && ($_POST['btnSearch'] <> "")) {
	
	$searchRequestDate = $_POST['searchRequestDate'];
	$searchApprovedStatus = $_POST['searchApprovedStatus'];
	
	if ($searchApprovedStatus=="All") 
	{
		$searchApprovedStatus="WIT','CAN";
	}
	
	$sql2 = "SELECT a.pg_thesis_id, a.id, DATE_FORMAT(a.report_date,'%d-%b-%Y') AS theReportDate, a.thesis_title, a.thesis_type, 
		b.description AS theThesisTypeDescription, a.introduction, a.objective, a.description, a.status, a.discussion_status, c.description AS theProposalStatusDescription, d.student_matrix_no, e.name, d.supervisor_status,
		a.verified_status, c2.description as verified_desc, a.verified_remarks, a.marked_status, a.cancel_approved_remarks,
		DATE_FORMAT(a.cancel_requested_date,'%d-%b-%Y %h:%i:%s') AS cancel_requested_date,
		DATE_FORMAT(a.cancel_approved_date,'%d-%b-%Y %h:%i:%s') AS cancel_approved_date
		FROM pg_proposal a
		LEFT JOIN  ref_thesis_type b ON (b.id = a.thesis_type) 
		LEFT JOIN ref_proposal_status c ON (c.id = a.status)
		LEFT JOIN ref_proposal_status c2 ON (c2.id = a.verified_status)
		LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id) 
		LEFT JOIN student e ON (e.matrix_no = d.student_matrix_no) 
		WHERE a.verified_status in ('$searchApprovedStatus') 
		AND (DATE_FORMAT(a.cancel_approved_date,'%d-%b-%Y') = '$searchRequestDate'
		OR a.cancel_approved_date is null)		
		AND a.archived_status IS NULL 
		AND a.status = 'OPN'
		AND d.status = 'INP'
		ORDER BY a.pg_thesis_id, a.id, a.cancel_requested_date DESC";		
		
		$result2 = $db->query($sql2); 
		$db->next_record();

} else 
{
	$sql2 = "SELECT a.pg_thesis_id, a.id, DATE_FORMAT(a.report_date,'%d-%b-%Y') AS theReportDate, a.thesis_title, a.thesis_type, 
		b.description AS theThesisTypeDescription, a.introduction, a.objective, a.description, a.status, a.discussion_status, c.description AS theProposalStatusDescription, d.student_matrix_no, e.name, d.supervisor_status,
		a.verified_status, c2.description as verified_desc, a.verified_remarks, a.marked_status, a.cancel_approved_remarks,
		DATE_FORMAT(a.cancel_requested_date,'%d-%b-%Y %h:%i:%s') AS cancel_requested_date,
		DATE_FORMAT(a.cancel_approved_date,'%d-%b-%Y %h:%i:%s') AS cancel_approved_date
		FROM pg_proposal a
		LEFT JOIN  ref_thesis_type b ON (b.id = a.thesis_type) 
		LEFT JOIN ref_proposal_status c ON (c.id = a.status)
		LEFT JOIN ref_proposal_status c2 ON (c2.id = a.verified_status)
		LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id) 
		LEFT JOIN student e ON (e.matrix_no = d.student_matrix_no) 
		WHERE a.verified_status in ('WIT','CAN') 
		AND (DATE_FORMAT(a.cancel_approved_date,'%Y-%m-%d') = CURDATE()
		OR a.cancel_approved_date is null)
		AND a.archived_status IS NULL 
		AND a.status = 'OPN'
		AND d.status = 'INP'
		ORDER BY a.pg_thesis_id, a.id, a.cancel_requested_date DESC";		
		
		$result2 = $db->query($sql2); 
		$db->next_record();
}

if(isset($_POST['btnSubmit']) && ($_POST['btnSubmit'] <> "")) {
	//$myVerifiedStatus=$_POST['myVerifiedStatus'];
	//$myVerifiedRemarks=$_POST['myVerifiedRemarks'];
	
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
		
		//$tmpSenateDate = date('Y-m-d', strtotime($senateDate));
	
	//report_date,'%Y-%m-%d %h:%i:%s')
	
	 $sql6_1 = "SELECT id, report_date, thesis_title, thesis_type, introduction, objective, 
				description,discussion_status, verified_by, 
				IFNULL(verified_date,'0000-00-00 00:00:00') as verified_date, verified_status, verified_remarks, endorsed_by, 
				IFNULL(endorsed_date,'0000-00-00 00:00:00') as endorsed_date, endorsed_remarks,status, 
				marked_by, marked_status, IFNULL(marked_date,'0000-00-00 00:00:00') as marked_date,
				insert_by, IFNULL(insert_date,'0000-00-00 00:00:00') as insert_date, 
				modify_by, IFNULL(modify_date,'0000-00-00 00:00:00') as modify_date, 
				pg_thesis_id, pg_proposal_approval_id, 
				faculty_remarks_by, IFNULL(faculty_remarks_date,'0000-00-00 00:00:00') as faculty_remarks_date, cancel_requested_by, IFNULL(cancel_requested_date,'0000-00-00 00:00:00') as cancel_requested_date, cancel_requested_remarks,
				cancel_approved_by, IFNULL(cancel_approved_date,'0000-00-00 00:00:00') as cancel_approved_date, cancel_approved_remarks
				FROM pg_proposal 
				WHERE id = '$myProposalId[$val]'";

		$result6_1 = $dbg->query($sql6_1); 			
		$dbg->next_record();
		

		$id = $dbg->f('id'); 
		$reportDate = $dbg->f('report_date'); 			
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
		$facultyRemarksBy = $dbg->f('faculty_remarks_by'); 
		$facultyRemarksDate = $dbg->f('faculty_remarks_date'); 		
		$cancelRequestedBy = $dbg->f('cancel_requested_by'); 
		$cancelRequestedDate = $dbg->f('cancel_requested_date'); 
		$cancelRequestedRemarks = $dbg->f('cancel_requested_remarks');		
		$cancelApprovedBy = $dbg->f('cancel_approved_by'); 
		$cancelApprovedDate = $dbg->f('cancel_approved_date'); 
		$cancelApprovedRemarks = $dbg->f('cancel_approved_remarks');		
		$insertBy = $dbg->f('insert_by'); 
		$insertDate = $dbg->f('insert_date'); 
		$modifyBy = $dbg->f('modify_by'); 
		$modifyDate = $dbg->f('modify_date'); 
		$pgThesisId = $dbg->f('pg_thesis_id'); 
		$pgProposalApprovalId = $dbg->f('pg_proposal_approval_id');
		
		
	$proposal_id = "P".runnum('id','pg_proposal');	
	
		$sql6_2 = "INSERT INTO pg_proposal 
				(id, report_date, thesis_title, thesis_type, introduction, objective, description, 
				discussion_status, verified_by, verified_date, verified_status, verified_remarks, endorsed_by, endorsed_date,
				endorsed_remarks, status,	marked_by, marked_date, marked_status, insert_by, insert_date, 				
				modify_by, modify_date, pg_thesis_id, faculty_remarks_by, faculty_remarks_date, 				
				cancel_requested_by, cancel_requested_date, cancel_requested_remarks,
				cancel_approved_by, cancel_approved_date, cancel_approved_remarks, pg_proposal_approval_id)
				VALUES
				('$proposal_id', '$reportDate', '$thesisTitle', '$thesisType', '$introduction', '$objective', '$description', 
				'$discussionStatus','$verifiedBy', '$verifiedDate', 'CAN', '$verifiedRemarks', '$endorsedBy', '$endorsedDate', 
				'$endorsedRemarks','$status', '$markedBy','$markedDate','$markedStatus','$insertBy', '$insertDate', 
				'$userid', '$curdatetime', '$pgThesisId', '$facultyRemarksBy', '$facultyRemarksDate', 				
				'$cancelRequestedBy', '$cancelRequestedDate', '$cancelRequestedRemarks',
				'$userid', '$curdatetime', '$cancelApprovedRemarks','$pgProposalApprovalId')";
			
		$result6_2 = $dbg->query($sql6_2); 			
		$dbg->next_record();

		
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
	//echo "sql6 ".$sql6;
	//var_dump($dbg);
	$dbg->next_record();
	
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
			
				
				<fieldset>
				<legend><strong>List of Thesis Proposal for Cancellation</strong></legend>
					<br/>
					<table>
						<tr>							
							<td><strong>Please enter searching criteria below</strong></td>
						</tr>
						<tr>
							<td><strong>Notes:-</strong>(by default it will display,<br/>
							1. Current proposal which the cancellation request has been approved and<br/>
							2. Proposal in which the cancellation request is pending for approval)</td>
						</tr>
					</table>
					<br/>
					<table>
						<tr>
							<?$searchRequestDate = date("d-M-Y");?>
							<td>Cancellation Date</td>
							<td>:</td>
							<td><input type="text" name="searchRequestDate" size="12" id="searchRequestDate" value="<?=$searchRequestDate;?>"/></td>
						</tr>
						<tr>
							<td>Cancellation Status</td>
							<td>:</td>							
							<td>
							<select name = "searchApprovedStatus">									
										<option value="All" size="30"  selected="selected">All</option>
										<?
										$sql3 = "SELECT id, description
												FROM ref_proposal_status
												WHERE usage_id='PCA'
												AND status = 'A'";
										 
										$dbf->query($sql3); 
										$dbf->next_record();
										do {
											$ps_id=$dbf->f('id');	
											$ps_desc=$dbf->f('description');	
											if ($searchApprovedStatus==$ps_id) 
											{
												?>
													<option value="<?=$ps_id?>" size="30" selected="selected"><?=$ps_desc?></option>
												<?
											}
											else 
											{
												?>
													<option value="<?=$ps_id?>" size="30" ><?=$ps_desc?></option>										
												<?
											}
										}while ($dbf->next_record());
										?>
									</select>
							</td>	
							<td><input type="submit" name="btnSearch" value="Search" /> </td>
						</tr>
					</table>
					<?  
				$result2 = $db->query($sql2); 
				$row_cnt = mysql_num_rows($result2);
				if ($row_cnt>0) {?>
				<br/>
					<table>
						<tr>							
							<td><strong>Searching Results:-</strong></td>
						</tr>
					</table>
					<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="100%">				
						<tr>						
							<td width="30" align="center"><strong>Tick</strong></td>	
							<td width="24" align="center"><strong>No.</strong></td>
							<td width="77"><strong>Request Date</strong></td>
							<td width="77"><strong>Approved Date</strong></td>
							<td width="77"><strong>Status by Faculty</strong></td>							
							<td width="103"><strong>Thesis / Project ID</strong></td>
							<td width="223"><strong>Thesis / Project Title</strong></td>
							<td width="139"><strong>Student Name</strong></td>
							<td width="103"><strong>Attachment by Student</strong></td>
							
						</tr>
						<?
						$no=0;
						$myNo=1;
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
							$cancelApprovedRemarks=$db->f('cancel_approved_remarks');			
							$cancelRequestedDate=$db->f('cancel_requested_date');
							$cancelApprovedDate=$db->f('cancel_approved_date');							
						?>
							
							<tr>
							
								<? if ($verifiedStatus=='CAN'){
									?><td align="center"><label><input name="myApprovalBox[]" type="checkbox" value="<?=$no;?>" disabled="disabled"/></label></td><?
								}
								else {
									?><td align="center"><label><input name="myApprovalBox[]" type="checkbox" value="<?=$no;?>"/></label></td><?
								}
								?>
								
								
								<input type="hidden" name="myProposalId[]" size="12" id="proposalId" value="<?=$proposalId;?>"/>
								<?$myProposalId[$no]=$proposalId;?>
								
								<td align="center"><?=$myNo++;?>.</td>	

							  <td><label name="cancelRequestedDate" size="15" id="cancelRequestedDate" ></label><?=$cancelRequestedDate;?></td>
							  <td><label name="cancelApprovedDate" size="15" id="cancelApprovedDate" ></label><?=$cancelApprovedDate;?></td>
							  
							  <td width="103"><label name="myVerifiedDesc[]" cols="45" id="verifiedDesc"><?=$verifiedDesc?></label></td>
								
								
								<td><a href="cancel_proposal_outline.php?thesisId=<? echo $pgThesisId;?>&proposalId=<? echo $proposalId;?>" name="myPgThesisId[]" value="<?=$pgThesisId?>" title="Outline of Proposed Case Study by the Student - Read more..."><?=$pgThesisId;?><br/>
						
								<?if ($cancelApprovedRemarks == null || $cancelApprovedRemarks ==""){?>
									<img src="../images/red_edit.jpg" width="20" height="19" style="border:0px;" title="Faculty Remark is not yet provided" >Enter remarks</a>	
								<?}
								else {
								?>
									<img src="../images/edit.jpg" width="20" height="19" style="border:0px;" title="Faculty Remark is provided" >Read remarks</a>	
								<?
								}
								?>
								</a></td>	
						
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
									$attachmentNo1=1;
									if ($row_cnt>0)
									{
										?><td width="128" align="left"><?
										while($row = mysql_fetch_array($result)) 					
										{ 
											?>
													<a href="download.php?fc=<?=$row["fu_cd"];?>&al=S" title="File Description: <?=$row["fu_document_filedesc"];?>">Attachment <?=$attachmentNo1++;?>: <br/>
								  <img src="../images/download.png" width="20" height="19" style="border:0px;" title="Download <?=$row["fu_document_filename"];?>"></a><br/>
													
															
												
										<?}
										?></td><?
									}
									else {
										?><td width="111">No attachment</td>
										<?
									}
								?>
							</tr>
						<?
						$no++;
						
						};	
						?>
						<?$_SESSION['myPgThesisId'] = $myPgThesisId;?>				
						<?$_SESSION['myStudentMatrixNo'] = $myStudentMatrixNo;?>
				</table>
					
					
					<?$_SESSION['myApprovalBox'] = $myApprovalBox;?>
					<?//$_SESSION['myVerifiedStatus'] = $myVerifiedStatus;?>
					<?//$_SESSION['myVerifiedRemarks'] = $myVerifiedRemarks;?>
					<br/>
					
		  </fieldset>
					<br/>

					<br/>
					<table>					
						<tr>				
							<td><input type="submit" name="btnSubmit" value="Approve Cancellation" /><span style="color:#FF0000"> Note:</span> Ensure the proposal above has been selected before approval.</td>
						</tr>
					</table>							
					<?
				}
				else {
					?>
					<fieldset>
						<legend><strong><span style="color:#FF0000">Notification Message</span></strong></legend>	
						<table>
							<tr>
								<td>
									<p>No thesis proposal available to review and proceed with cancellation approval (if any).</p>
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




