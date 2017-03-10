<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: senate_approval_outline.php
//
// Created by: Zuraimi
// Created Date: 14-Jan-2015
// Modified by: Zuraimi
// Modified Date: 14-Jan-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

session_start();
$userid=$_SESSION['user_id'];
$thesisId=$_REQUEST['thesisId'];
$proposalId=$_REQUEST['proposalId'];
$approvalId=$_GET['aid'];

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

function retrieveJobAreaId($jobAreaId)
{
	global $dbc;
	$sql = "SELECT area from job_list_category WHERE JobArea = '$jobAreaId'";
	$dbc->query($sql);
	$dbc->next_record();
	$jobAreaDesc = $dbc->f('area');
	return $jobAreaDesc;

}

///////////////////////////////////////////////////////////////

if(isset($_POST['btnSave']) && ($_POST['btnSave'] <> "")) {
	
	$endorsedRemarks=$_POST['endorsedRemarks'];
	$currentDate = date('Y-m-d H:i:s');
	
	$sql1 = "UPDATE pg_proposal SET
				endorsed_by = '$userid', endorsed_date = '$currentDate', endorsed_remarks='$endorsedRemarks',	
				modify_date = '$currentDate', modify_by = '$userid'		
				WHERE id='$proposalId'";
	//echo $sql1;exit();
	$dbg->query($sql1); 
}

$sql = "select a.pg_thesis_id, a.id, DATE_FORMAT(a.report_date,'%d-%b-%Y') AS theReportDate, a.thesis_title, a.thesis_type, 
		b.description as thesisTypeDescription, a.introduction, a.objective, a.description, d.student_matrix_no, 
		a.endorsed_remarks
		FROM pg_proposal a
		LEFT JOIN ref_thesis_type b ON (b.id = a.thesis_type)
		LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id )
		WHERE a.id = '$proposalId'
		AND a.pg_thesis_id = '$thesisId'";
			
		$result = $db->query($sql); 
		//echo $sql;
		//var_dump($db);
		$db->next_record();				
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
	</head>
	<body>  
		<form id="form1" name="form1" method="post" enctype="multipart/form-data">		
		<?
		$pgThesisId=$db->f('pg_thesis_id');
		$id=$db->f('id');
		$studentMatrixNo=$db->f('student_matrix_no');
		$thesisTitle=$db->f('thesis_title');
		$thesisType=$db->f('thesis_type');
		$introduction=$db->f('introduction');
		$objective=$db->f('objective');
		$description=$db->f('description');
		$thesisTypeDescription=$db->f('thesisTypeDescription');
		$reportDate=$db->f('theReportDate');
		$endorsedRemarks=$db->f('endorsed_remarks');
		
		?>
		<fieldset>
		<legend><strong>Approval Details</strong></legend>	
		<table>
		<input type="hidden" name="proposalId" size="15" id="proposalId" value="<?=$id?>"/>
			<?
			$sql3 = "SELECT a.endorsed_status, b.description, a.endorsed_by, DATE_FORMAT(a.endorsed_date,'%d-%b-%Y') as endorsed_date, a.endorsed_remarks
					FROM pg_proposal_approval a
					LEFT JOIN ref_proposal_status b ON (b.id = a.endorsed_status)
					WHERE a.id = '$approvalId'";		
			
			$result3 = $dba->query($sql3); 
			$dba->next_record();
			$endorsedStatus=$dba->f('endorsed_status');
			$endorsedDesc=$dba->f('description');
			$endorsedBy=$dba->f('endorsed_by');
			$endorsedDate=$dba->f('endorsed_date');
			$endorsedRemarks=$dba->f('endorsed_remarks');
		
			?>
			
			<tr>
				<td>Approval/Status ID</td>
				<td>:</td>
				<td><label><?=$approvalId?></label></td>
			</tr>
			<tr>
				<td>Endorsed Status</td>
				<td>:</td>
				<td><label><?=$endorsedDesc;?></label></td>
			</tr>  			
			<?
			 $sql3 = "SELECT name
					FROM new_employee
					WHERE empid = '$endorsedBy'";		
			
			$result3 = $dbc->query($sql3); 
			$dbc->next_record();
			$staffName=$dbc->f('name');						
		
			?>
			<tr>
				<td>Endorsed Date</td>
				<td>:</td>
				<td><label><?=$endorsedDate;?></label></td>		
			</tr>
			<tr>
				<td>Endorsed By</td>
				<td>:</td>
				<td><label><?=$staffName;?></label></td>
			</tr>
			
		</table>
		<?
		$sql2 = "SELECT a.pg_thesis_id , a.id, DATE_FORMAT(a.report_date,'%d-%b-%Y') AS theReportDate, a.thesis_title, a.thesis_type, 
			b.description AS theThesisTypeDescription, a.introduction, a.objective, a.description, a.verified_status, a.verified_by, 
			DATE_FORMAT(a.verified_date,'%d-%b-%Y') AS verified_date, a.verified_remarks, a.status as endorsedStatus, a.discussion_status, d.supervisor_status, c.description AS endorsedDesc, c1.description AS verifiedDesc,d.student_matrix_no, 
			a.endorsed_remarks, DATE_FORMAT(f.endorsed_date,'%d-%b-%Y') AS endorsed_date, f.id as approval_id
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
			AND f.id = '$approvalId'
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
		$approvalIdArray = Array();
		
		
		
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
			$approvalIdArray[$no1] = $db->f('approval_id');
			$no1++;
			
		} while ($db->next_record());
		
		$studentNameArray = Array();
		for ($i=0; $i<$no1; $i++){
			$sql11 = "SELECT name
				FROM student
				WHERE matrix_no = '$studentMatrixNoArray[$i]'
				AND name like '%$searchStudentName%'";
			if (substr($studentMatrixNoArray[$i],0,2) != '07') { 
				$dbConnStudent= $dbc; 
			} 
			else { 
				$dbConnStudent=$dbc1; 
			}
			$result11 = $dbConnStudent->query($sql11); 
			$dbConnStudent->next_record();
			if (mysql_num_rows($result11)>0) {
				$studentNameArray[$no2] = $dbConnStudent->f('name');
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
				$approvalIdArray[$no2] = $approvalIdArray[$i];
				$no2++;
			}
		} 	
		$row_cnt = $no2;
		
		?>
		
			<br/>
			<table>
				<tr>
					<td><label><strong>List of Thesis</strong></label></td>
				</tr>
			</table>
			<table width="100%" border="1" cellpadding="3" cellspacing="1" style="border-collapse:collapse;" class="thetable">
				<tr>				
					<th width="25" align="center"><strong>No.</strong></th>					
					<th width="100"><strong>Thesis Date</strong></th>
					<th width="131"><strong>Thesis/Project ID</strong></th>
					<th width="208"><strong>Thesis/Project Title</strong></th>
					<th width="102"><strong>Student Name</strong></th>
					<th width="102"><strong>Attachment by Student</strong></th>					
					<th width="151"><strong>Supervisor </strong></th>
				</tr>
			 <?if ($row_cnt>0) {
				$no=0;
				//while($db->next_record()) {	
				for ($i=0; $i<$no2; $i++){	
					?>
					<tr>
							
						
						<input type="hidden" name="myProposalId[]" size="12" id="proposalId" value="<?=$proposalIdArray[$i];?>"/>
						<? $myProposalId[$no]=$proposalIdArray[$i];?>
						
						<input type="hidden" name="myPgThesisId[]" size="12" id="pgThesisId" value="<?=$pgThesisIdArray[$i];?>"/>
						<? $myPgThesisId[$no]=$pgThesisIdArray[$i];?>
						
						<td align="center"><?=$no+1;?>.</td>
						
						<td><label name="reportDate[]" cols="45" id="reportDate"><?=$reportDateArray[$i]?></label></td>
						<td><a href="senate_approval_outline_view.php?thesisId=<?=$pgThesisIdArray[$i];?>&proposalId=<?=$proposalIdArray[$i];?>&aid=<?=$approvalId;?>" name="myPgThesisId[]" value="<?=$pgThesisIdArray[$i]?>" title="Outline of Proposed Case Study by the Student - Read more..."><?=$pgThesisIdArray[$i];?><br/>
						
						
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
								?><td>No Attachment</td><?
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
								} 
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
				$no++;
				}
			 
			 
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
				 <?
			 }?>
			</table>
			
		</fieldset>
		<br/>
		<fieldset>
		<legend><strong>Overall Remarks by Senate</strong></legend>
		<tr>
			<td><textarea name="endorsedRemarks" id="endorsedRemarks" class="ckeditor" cols="50" rows="3"><?=$endorsedRemarks; ?></textarea></td>					
		</tr>
		</table>
		</fieldset>
		<br/>
		<fieldset>
		<legend><strong>Attachment by Senate </strong></legend>
		<table border="1" cellpadding="3" cellspacing="3" width="70%" id="inputs9" class="thetable">
			<tr>
				<th align="center" width="5%"><label>No</label></td>
				<th width="30%"><span class="labeling">Document Description</span></th>
				<th width="30%"><span class="labeling">Document Name</span></th>
				<th width="5%"><span class="labeling">Download</span></th>
			</tr>
				
		<?
		$sqlUpload="SELECT * FROM file_upload_senate 
		WHERE approval_id = '$approvalId'";			

		$result = $db->query($sqlUpload); 
		$row_cnt = mysql_num_rows($result);
						
		if ($row_cnt > 0) {	
			$tmp_no=0;					
			while($row = mysql_fetch_array($result)) 					
			{ 
				
				?><tr>
						<td align="center"><label><?=$tmp_no+1;?>.</label></td>
						<td><label><?=$row["fu_document_filedesc"];?></label></td>
						<td><label name="file_name[]" size="40" ></label><?=$row["fu_document_filename"];?></td>
						<td><a href="download_senate.php?fc=<?=$row["fu_cd"];?>" target="_blank" onMouseOver="toolTip('<?=$row["fu_document_filename"];?>', 300)" onMouseOut="toolTip()" align="center">
						<img src="../images/view_doc.jpg" width="20" height="19" style="border:0px;" title="View document"></a>
						</td>
					</tr>										
			<?
			$tmp_no++;
			}
		}
		else {
			?>
			<table>
				<tr>
					<td><label>No record found!</label></td>
				</tr>
			</table>
			<?
		}?> 								
		</table>
		<br/>
		</fieldset>				
		
		<table>
		<tr>
			<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='senate_approval.php';" /></td>			
		</tr>	
		</table>
		<br/>
	  </form>
	</body>
</html>




