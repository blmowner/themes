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
	
	if (sizeof($_POST['myApprovalBox'])>0) {

		while (list ($key,$val) = @each ($myApprovalBox)) 
		{
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
			
		
		 $sql6_1 = "SELECT id, report_date, thesis_title, thesis_type, introduction, objective, 
					description,discussion_status,verified_by, 
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
					(id, report_date, thesis_title, thesis_type, introduction, objective, description, discussion_status,
					verified_by, verified_date, verified_status, verified_remarks, endorsed_by, endorsed_date, endorsed_remarks, status,
					marked_by, marked_date, marked_status, insert_by, insert_date, modify_by, modify_date, 
					pg_thesis_id)
					VALUES
					('$proposal_id', '$reportDate', '$thesisTitle', '$thesisType', '$introduction', '$objective', '$description', '
					$discussionStatus',  
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
	else {
		?>
		<fieldset>
		<legend><strong><span style="color:#FF0000">Notification Message</span></strong></legend>	
		<table>
			<tr>
				<td>Please tick the checkbox before submit.</td>
			</tr>
		</table>
	</fieldset>		
	<br/>
		<?
		
		
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
		$tmpSearchStudent = " AND (d.student_matrix_no = '$searchStudent' OR e.name like '%$searchStudent%')";
	}
	else 
	{
		$tmpSearchStudent="";
	}
	
$sql2 = "SELECT a.pg_thesis_id, a.id, DATE_FORMAT(a.report_date,'%d-%b-%Y') AS theReportDate, a.thesis_title, a.thesis_type, 
		b.description AS theThesisTypeDescription, a.introduction, a.objective, a.description, a.status, a.discussion_status, c.description AS theProposalStatusDescription, d.student_matrix_no, e.name, d.supervisor_status, d.reviewer_status,
		a.verified_status, c2.description as verified_desc, a.verified_remarks, a.marked_status
		FROM pg_proposal a
		LEFT JOIN  ref_thesis_type b ON (b.id = a.thesis_type) 
		LEFT JOIN ref_proposal_status c ON (c.id = a.status)
		LEFT JOIN ref_proposal_status c2 ON (c2.id = a.verified_status)
		LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id) 
		LEFT JOIN student e ON (e.matrix_no = d.student_matrix_no) 
		WHERE a.verified_status in ('INP','APP','REQ','DIS')" 
		.$tmpSearchThesisId." "
		.$tmpSearchStudent." "
		.$tmpSearchThesisDate." "."
		AND a.archived_status IS NULL 
		AND a.status = 'OPN'
		AND d.status = 'INP'";		
		
		$result2 = $db->query($sql2); 
		//echo $sql2; 

		//var_dump($db);
		$db->next_record();
		
}
else 
{
	$sql2 = "SELECT a.pg_thesis_id, a.id, DATE_FORMAT(a.report_date,'%d-%b-%Y') AS theReportDate, a.thesis_title, a.thesis_type, 
		b.description AS theThesisTypeDescription, a.introduction, a.objective, a.description, a.status, a.discussion_status, c.description AS theProposalStatusDescription, d.student_matrix_no, e.name, d.supervisor_status, d.reviewer_status,
		a.verified_status, c2.description as verified_desc, a.verified_remarks, a.marked_status
		FROM pg_proposal a
		LEFT JOIN  ref_thesis_type b ON (b.id = a.thesis_type) 
		LEFT JOIN ref_proposal_status c ON (c.id = a.status)
		LEFT JOIN ref_proposal_status c2 ON (c2.id = a.verified_status)
		LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id) 
		LEFT JOIN student e ON (e.matrix_no = d.student_matrix_no) 
		WHERE a.verified_status in ('INP','APP','REQ','DIS') 
		AND a.archived_status IS NULL 
		AND a.status = 'OPN'
		AND d.status = 'INP'";		
		
		$result2 = $db->query($sql2); 
		//echo $sql2; 

		//var_dump($db);
		$db->next_record();
		
}

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
	
	
	
		<form id="form1" name="form1" method="post" enctype="multipart/form-data">				
			
				<?  
				$result2 = $db->query($sql2); 
				$row_cnt = mysql_num_rows($result2);
				if ($row_cnt>0) 
				{?>
				
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
						</tr>
						<tr>
							<?$searchRequestDate = date("d-M-Y");?>
							<td>Thesis ID / Thesis Title</td>
							<td>:</td>
							<td><input type="text" name="searchThesisId" size="50" id="searchThesisId" value="<?=$searchThesisId;?>"/></td>
						</tr>
						<tr>
							<td>Student Name / Matrix No </td>
							<td>:</td>
							<td><input type="text" name="searchStudent" size="30" id="searchStudent" value="<?=$searchStudent;?>"/></td>
							<td><input type="submit" name="btnSearch" value="Search" /> Note: If no parameters are provided, it will search all.</td>
						</tr>
					</table>
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
							<td width="77"><strong>Faculty Status</strong></td>
							<td width="103"><strong>Thesis Date</strong></td>
							<td width="103"><strong>Thesis / Project ID</strong></td>
							<td width="156"><strong>Thesis / Project Title</strong></td>
							<td width="69"><strong>Student Name</strong></td>
							<td width="98"><strong>Attachment by Student</strong></td>
							<td width="96"><strong>Attachment by Faculty</strong></td>
							<td width="78"><strong>Supervisor / Co-Supervisor</strong></td>
							<td width="78"><strong>Reviewer</strong></td>
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
							$reviewerStatus=$db->f('reviewer_status');
							$verifiedDesc=$db->f('verified_desc');	
							$verifiedRemarks=$db->f('verified_remarks');	
							$markedStatus=$db->f('marked_status');
							$verifiedStatus=$db->f('verified_status');							
						?>
							
							<tr>
							
								<? if ($verifiedStatus=='APP' || $verifiedStatus=='REQ' || $verifiedStatus=='DIS'){
									?><td align="center"><label><input name="myApprovalBox[]" type="checkbox" value="<?=$no;?>" disabled="disabled"/></label></td><?
								}
								else {
									?><td align="center"><label><input name="myApprovalBox[]" type="checkbox" value="<?=$no;?>"/></label></td><?
								}
								?>
								
								
								<input type="hidden" name="myProposalId[]" size="12" id="proposalId" value="<?=$proposalId;?>"/>
								<?$myProposalId[$no]=$proposalId;?>
								<?//echo "myProposalId[$no] ".$myProposalId[$no];?>
								
								<td align="center"><?=$myNo++;?>.
								<?
								$sql3_1 = "SELECT const_value
								FROM base_constant
								WHERE const_term = 'NEW_PROPOSAL_FACULTY'";

								$result3_1 = $dbb->query($sql3_1);
								$dbb->next_record();
								$parameterValue=$dbb->f('const_value');
								
								$newReportDate = date('d-M-Y', strtotime($reportDate. ' + '.$parameterValue.' days'));		
								$currentDate = new DateTime();			
								$tmpNewReportDate = new DateTime($newReportDate);
								
								if ($tmpNewReportDate->format('d-M-Y') >= $currentDate->format('d-M-Y')) {
								?>
									<img src="../images/new.jpg" width="50" height="40" style="border:0px;" title="Proposal is considered new if it is submitted within <?=$parameterValue?> day(s)">
								<?}?></td>	

								<td width="22"><label name="myVerifiedDesc[]" cols="45" id="verifiedDesc"><?=$verifiedDesc?></label></td>
								
								<td><label name="reportDate[]" cols="45" id="reportDate"><?=$reportDate?></label></td>
								
								<td><a href="proposal_outline.php?thesisId=<? echo $pgThesisId;?>&proposalId=<? echo $proposalId;?>" name="myPgThesisId[]" value="<?=$pgThesisId?>" title="Outline of Proposed Case Study by the Student - Read more..."><?=$pgThesisId;?></a><br/>
						
								<?if ($verifiedRemarks == null || $verifiedRemarks ==""){?>
									<img src="../images/red_edit.jpg" width="20" height="19" style="border:0px;" title="Faculty Remark is not yet provided" >Enter remarks</a>	
								<?}
								else {
								?>
									<img src="../images/edit.jpg" width="20" height="19" style="border:0px;" title="Faculty Remark is provided" >Read remarks</a>	
								<?
								}
								if ($markedStatus == "MAR"){?>
									<br/><img src="../images/mark.jpg" width="20" height="19" style="border:0px;" title="Thesis is marked for review" >Review in progress</a>								
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
									WHERE pg_proposal_id='$proposalId' 
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
										?><br/><input type="submit" name="submit" value="Upload" onclick="javascript:open_win('confirm_proposal_upload.php?pid=<?=$proposalId;?>&al=F',480,280,0,0,0,1,0,1,1,0,5,'winupload'); "/></td><?
									}
									else {
										?><td width="80">No attachment<br/>
										  <input type="submit" name="submit" value="Upload" onclick="javascript:open_win('confirm_proposal_upload.php?pid=<?=$proposalId;?>&al=F',480,280,0,0,0,1,0,1,1,0,5,'winupload'); "/></td><?
									}
								?>
								
								
							<td width="84">
								<?$_SESSION['studentMatrixNo']=$studentMatrixNo;
								if ($supervisorStatus =='A') {?>
									<a href="../supervisor/edit_supervisor.php?pid=<?php echo $proposalId?>&tid=<? echo $pgThesisId?>&mn=<? echo $studentMatrixNo?>&sname=<?php echo $studentName?>">Change <img src="../images/person_reassigned.jpg" width="20" height="19" style="border:0px;" title="Supervisor has been assigned" ></a><br/>
									<br/><a href="../supervisor/view_supervisor.php?sname=<?php echo $studentName?>&mn=<? echo $studentMatrixNo?>&tid=<? echo $pgThesisId?>">View</a> 
								<?}
								else {?>
									<a href="../supervisor/edit_supervisor.php?pid=<?php echo $proposalId?>&tid=<? echo $pgThesisId?>&mn=<? echo $studentMatrixNo?>&sname=<?php echo $studentName?>">Assign <img src="../images/person_assigned.jpg" width="20" height="19" style="border:0px;" title="Supervisor is not assigned yet" ></a>
								<?}?>															
							</td>
							
							<td width="84">
								<?
								if ($reviewerStatus =='A') {?>
									<a href="../reviewer/edit_reviewer.php?pid=<?php echo $proposalId?>&tid=<? echo $pgThesisId?>&mn=<? echo $studentMatrixNo?>&sname=<?php echo $studentName?>">Change <img src="../images/person_reassigned.jpg" width="20" height="19" style="border:0px;" title="Reviewer has been assigned" ></a><br/>
									<br/><a href="../reviewer/view_reviewer.php?sname=<?php echo $studentName?>&mn=<? echo $studentMatrixNo?>&tid=<? echo $pgThesisId?>">View</a> 
								<?}
								else {?>
									<a href="../reviewer/edit_reviewer.php?pid=<?php echo $proposalId?>&tid=<? echo $pgThesisId?>&mn=<? echo $studentMatrixNo?>&sname=<?php echo $studentName?>">Escalate <img src="../images/person_assigned.jpg" width="20" height="19" style="border:0px;" title="Reviewer is not assigned yet" ></a>
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
								<input name="myVerifiedStatus" type="radio" value="APP" checked="checked"/>
								Approved
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
									<p>There is no proposal to show for verification and supervisor invitation.</p>
								</td>
							</tr>
						</table>
					</fieldset>
					<table>					
						<tr>				
							<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../thesis/confirm_proposal.php';" /></td>
						</tr>
					</table>
					<?
				}				
				?>					
		</form>
	</body>
</html>




