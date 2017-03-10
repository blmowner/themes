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
	
	if ($searchRequestDate!="") {
		$tmpSearchRequestDate="AND DATE_FORMAT(a.cancel_requested_date,'%d-%b-%Y') = '".$searchRequestDate."' ";
	}
	else {
		$tmpSearchRequestDate="";
	}
	
	if ($searchApprovedStatus=="All") 
	{
		$tmpSearchApprovedStatus="WIT','CAN";
	}
	else {
		$tmpSearchApprovedStatus="$searchApprovedStatus";
	}
	
	$sql2 = "SELECT a.pg_thesis_id, a.id, DATE_FORMAT(a.report_date,'%d-%b-%Y') AS theReportDate, a.thesis_title, a.thesis_type, 
		b.description AS theThesisTypeDescription, a.introduction, a.objective, a.description, a.status, a.discussion_status, c.description AS theProposalStatusDescription, d.student_matrix_no, d.supervisor_status,
		a.verified_status, c2.description as verified_desc, a.verified_remarks, a.marked_status, a.cancel_approved_remarks,
		DATE_FORMAT(a.cancel_requested_date,'%d-%b-%Y %h:%i:%s') AS cancel_requested_date,
		DATE_FORMAT(a.cancel_approved_date,'%d-%b-%Y %h:%i:%s') AS cancel_approved_date
		FROM pg_proposal a
		LEFT JOIN  ref_thesis_type b ON (b.id = a.thesis_type) 
		LEFT JOIN ref_proposal_status c ON (c.id = a.status)
		LEFT JOIN ref_proposal_status c2 ON (c2.id = a.verified_status)
		LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id) 
		WHERE a.verified_status in ('".$tmpSearchApprovedStatus."') 
		".$tmpSearchRequestDate."		
		AND a.archived_status IS NULL 
		AND a.status = 'OPN'
		AND d.status = 'INP'
		ORDER BY a.pg_thesis_id, a.id, a.cancel_requested_date DESC";		
		
		$result2 = $db->query($sql2); 
		$db->next_record();

} else 
{
	$sql2 = "SELECT a.pg_thesis_id, a.id, DATE_FORMAT(a.report_date,'%d-%b-%Y') AS theReportDate, a.thesis_title, a.thesis_type, 
		b.description AS theThesisTypeDescription, a.introduction, a.objective, a.description, a.status, a.discussion_status, c.description AS theProposalStatusDescription, d.student_matrix_no, d.supervisor_status,
		a.verified_status, c2.description as verified_desc, a.verified_remarks, a.marked_status, a.cancel_approved_remarks,
		DATE_FORMAT(a.cancel_requested_date,'%d-%b-%Y %h:%i:%s') AS cancel_requested_date,
		DATE_FORMAT(a.cancel_approved_date,'%d-%b-%Y %h:%i:%s') AS cancel_approved_date
		FROM pg_proposal a
		LEFT JOIN  ref_thesis_type b ON (b.id = a.thesis_type) 
		LEFT JOIN ref_proposal_status c ON (c.id = a.status)
		LEFT JOIN ref_proposal_status c2 ON (c2.id = a.verified_status)
		LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id) 
		WHERE a.verified_status in ('WIT','CAN') 
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
	$msg = array();
	$myApprovalBox=$_POST['myApprovalBox'];
	$myPgThesisId=$_SESSION['myPgThesisId'];
	$myStudentName=$_SESSION['myStudentName'];
	$myReportDate=$_SESSION['myReportDate'];
	

	if (sizeof($myApprovalBox) > 0) {
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
		
		 $sql6_1 = "SELECT a.id, a.report_date, a.thesis_title, a.thesis_type, a.introduction, a.objective, 
					a.description,a.discussion_status, a.verified_by, 
					IFNULL(a.verified_date,'0000-00-00 00:00:00') as verified_date, a.verified_status, a.verified_remarks, a.endorsed_by, 
					IFNULL(a.endorsed_date,'0000-00-00 00:00:00') as endorsed_date, a.endorsed_remarks,a.status, 
					a.marked_by, a.marked_status, IFNULL(a.marked_date,'0000-00-00 00:00:00') as marked_date,
					a.insert_by, IFNULL(a.insert_date,'0000-00-00 00:00:00') as insert_date, 
					a.modify_by, IFNULL(a.modify_date,'0000-00-00 00:00:00') as modify_date, 
					a.pg_thesis_id, a.pg_proposal_approval_id, 
					a.faculty_remarks_by, IFNULL(a.faculty_remarks_date,'0000-00-00 00:00:00') as faculty_remarks_date, a.cancel_requested_by, IFNULL(a.cancel_requested_date,'0000-00-00 00:00:00') as cancel_requested_date, a.cancel_requested_remarks,
					a.cancel_approved_by, IFNULL(a.cancel_approved_date,'0000-00-00 00:00:00') as cancel_approved_date, a.cancel_approved_remarks,
					ppa.job_id1_area, ppa.job_id2_area, ppa.job_id3_area, ppa.job_id4_area, ppa.job_id5_area, ppa.job_id6_area
					FROM pg_proposal a
					LEFT JOIN pg_proposal_area ppa ON (ppa.pg_proposal_id=a.id)
					WHERE a.id = '$myProposalId[$val]'";

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
			$jobs_area1=$dbg->f('job_id1_area');
			$jobs_area2=$dbg->f('job_id2_area');
			$jobs_area3=$dbg->f('job_id3_area');
			$jobs_area4=$dbg->f('job_id4_area');
			$jobs_area5=$dbg->f('job_id5_area');
			$jobs_area6=$dbg->f('job_id6_area');

			
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
					VALUES('$job_area_id', '$proposal_id', '$jobs_area1', '$jobs_area2', '$jobs_area3', '$jobs_area4', 
					'$jobs_area5', '$jobs_area6', '$curdatetime', '".$_SESSION['user_id']."', '$curdatetime', '".$_SESSION['user_id']."')";
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
		//echo "sql6 ".$sql6;
		//var_dump($dbg);
		$dbg->next_record();
		}
		$msg[] = "<div class=\"success\"><span>The selected thesis proposal for cancellation request has been approved successfully.</span></div>";
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please select the thesis proposal first before click APPROVE CANCELLATION button.</span></div>";
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
		<script language="JavaScript" src="../js/windowopen.js"></script>
		<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>
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
				<legend><strong>List of Thesis Proposal for Cancellation</strong></legend>
					<br/>
					<table>
						<tr>							
							<td><strong>Please enter searching criteria below</strong></td>
						</tr>
						<tr>
							<td><strong>Notes:-</strong> by default it will display,<br/>
							1. the proposal which pending for cancellation approval <br/>
							2. the proposal which has been cancelled</td>
						</tr>
					</table>
					<br/>
					<table>
						<tr>
							<?//$searchRequestDate = date("d-M-Y");?>
							<?$jscript1 = "";?>
							<td>Request Date</td>
							<td>:</td>
							<td><input type="text" name="searchRequestDate" size="12" id="searchRequestDate" value="<?=$searchRequestDate;?>"/></td>
							<?	$jscript1 .= "\n" . '$( "#searchRequestDate" ).datepicker({
														changeMonth: true,
														changeYear: true,
														yearRange: \'-100:+0\',
														dateFormat: \'dd-M-yy\'
													});';
							 
					?>
						</tr>
						<tr>
							<td>Cancellation Status</td>
							<td>:</td>							
							<td>
							<select name = "searchApprovedStatus">									
										<option value="All" size="30"  selected="selected"></option>
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
							<td><input type="submit" name="btnSearch" value="Search" /><span style="color:#FF0000"> Note:</span> If no parameters are provided, it will search all. </td>
						</tr>
					</table>
					<?  
				$result2 = $db->query($sql2); 
				$row_cnt = mysql_num_rows($result2);
				?>
				<br/>
					<table>
						<tr>							
							<td>Searching Results:-</td>
						</tr>
					</table>
					<? if ($row_cnt >5)
					{?>
						<div id = "tabledisplay" style="overflow:auto; height:400px;">
					<? }
					else 
					{ ?>
						<div id = "tabledisplay">
					<? } ?>
					<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="100%" class="thetable">				
						<tr>						
							<th width="30" align="center"><strong>Tick</strong></th>	
							<th width="24" align="center"><strong>No.</strong></th>
							<th width="77"><strong>Request Date</strong></th>
							<th width="77"><strong>Approved Date</strong></th>
							<th width="77"><strong>Status by Faculty</strong></th>							
							<th width="103"><strong>Thesis / Project ID</strong></th>
							<th width="223"><strong>Thesis / Project Title</strong></th>
							<th width="139"><strong>Student Name</strong></th>
							<th width="103"><strong>Attachment by Student</strong></th>
							
						</tr>
						<?
						if ($row_cnt>0) {
						$no=0;
						$myNo=1;
						while($db->next_record()) {						
							$pgThesisId=$db->f('pg_thesis_id');	
							$studentMatrixNo=$db->f('student_matrix_no');
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
								<?
									$sql3 = "SELECT name
											FROM student
											WHERE matrix_no = '$studentMatrixNo'";		
									if (substr($studentMatrixNo,0,2) != '07') { 
										$dbConnStudent= $dbc; 
									} 
									else { 
										$dbConnStudent=$dbc1; 
									}	
									
									$result3 = $dbConnStudent->query($sql3); 
									$dbConnStudent->next_record();
									$studentName=$dbConnStudent->f('name');						
								
									?>
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
				</div>	
					
					<?$_SESSION['myApprovalBox'] = $myApprovalBox;?>
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
						<table>
							<tr>
								<td>No record found!</td>
							</tr>
						</table>
					<?
				}				
				?>					
		</form>
	<script>
		<?=$jscript1;?>			
	</script>
	</body>
</html>




