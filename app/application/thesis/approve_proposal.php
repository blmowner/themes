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

$sql1 = "SELECT supervisor_status
		FROM student
		WHERE matrix_no = '$studentMatrixNo'";
		
$result1 = $dba->query($sql1); 
//echo $sql1;
//var_dump($db);
$dba->next_record();

///////////////////////////////////////////////////////////////
$sql2 = "SELECT a.pg_thesis_id, a.id, DATE_FORMAT(a.report_date,'%d-%b-%Y') AS theReportDate, a.thesis_title, a.thesis_type, 
		b.description AS theThesisTypeDescription, a.introduction, a.objective, a.description, a.feedback_by, 
		a.feedback_date, a.feedback_remarks, a.status, a.discussion_status, 
		c.description AS theProposalStatusDescription, d.student_matrix_no, e.name 
		FROM pg_proposal a
		LEFT JOIN  ref_thesis_type b ON (b.id = a.thesis_type) 
		LEFT JOIN ref_proposal_status c ON (c.id = a.status) 
		LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id) 
		LEFT JOIN student e ON (e.matrix_no = d.student_matrix_no) 
		WHERE a.confirm_status = 'INP' 
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
		
if(isset($_POST['btnSubmit']) && ($_POST['btnSubmit'] <> "")) {
	$confirmStatus=$_POST['confirmStatus'];
	/*echo "confirmStatus ".$confirmStatus."<br/>";
	echo "senateDate ".$senateDate."<br/>";
	echo "respondedByDate ".$respondedByDate."<br/>";
	echo "assignedRemark ".$assignedRemark."<br/>";*/
	
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
				assigned_remark = '$assignedRemark', modify_by = '$userid', modify_date = '$curdatetime'
				WHERE pg_student_matrix_no= '$myStudentMatrixNo[$val]'
				AND STATUS = 'A'";
		
		$result5 = $dbd->query($sql5); 
		echo "sql5 ".$sql5;
		//var_dump($dbd);
		$dbd->next_record();
		
		$tmpSenateDate = date('Y-m-d', strtotime($senateDate));
	
		$sql6 = "UPDATE pg_proposal 		
				SET confirm_date = '$curdatetime', confirm_remarks = '$assignedRemark', 
				confirm_by = '$userid', confirm_status = '$confirmStatus'
				WHERE id = '$myProposalId[$val]'";
		
		$result6 = $dbg->query($sql6); 			
		echo "sql6 ".$sql6;
		//var_dump($dbg);
		$dbg->next_record();
			
		$sql4 = "UPDATE pg_thesis
				SET ref_thesis_status_id_proposal = 'INP', modify_by = '$userid', modify_date = '$curdatetime'
				WHERE id = '$myPgThesisId[$val]'
				AND student_matrix_no = '$myStudentMatrixNo[$val]'";

		$result4 = $dbf->query($sql4); 
		echo "sql4 ".$sql4;
		//var_dump($dbf);
		$dbf->next_record();
	
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
		<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>		
	    <style type="text/css"></style>
		</head>
	<body>  
		<form id="form1" name="form1" method="post" enctype="multipart/form-data">				
			<table>
				<tr>
				  <td><strong>Review Thesis Proposal and Assign Supervisor/Co-Supervisor</strong></td>      
				</tr>
				<br/>
			</table>
			
			<table >
				<?  
				$row_cnt = mysql_num_rows($result2);
					if ($row_cnt>0) {?>

					<tr>						
						<td width="5" nowrap="nowrap">Tick</td>	
						<td>No.</td>					
						<td width="20" nowrap="nowrap">Thesis/Project ID</td>
						<td>Student Matrix No.</td>												
						<td>Student Name</td>
						<td>Thesis Date</td>
						<td>Thesis/Project Title</td>
						<td>Supervisor</td>																		
						<td></td>																		
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
															
					?>
						<tr>
							<td><label><input name="myApprovalBox[]" type="checkbox" value="<?=$no;?>" /></label></td>	
							
							<input type="hidden" name="myProposalId[]" size="12" id="proposalId" value="<?=$proposalId;?>"/>
							<?$myProposalId[$no]=$proposalId;?>
							<?//echo "myProposalId[$no] ".$myProposalId[$no];?>
							
							<td><?=$no+1;?>.</td>	
							
							<td><a href="proposal_outline.php?thesisId=<? echo $pgThesisId;?>&proposalId=<? echo $id;?>" name="myPgThesisId[]" value="<?=$pgThesisId?>" title="Outline of Proposed Case Study by the Student - Read more..."><?=$pgThesisId;?></a></td>	
							<?$myPgThesisId[$no]=$pgThesisId;?>
							<?//echo "myPgThesisId[$no] ".$myPgThesisId[$no];?>
							
							<td><input type="text" name="myStudentMatrixNo[]" size="15" id="studentMatrixNo" value="<?=$studentMatrixNo;?>" disabled="disabled"/></td>
							<?$myStudentMatrixNo[$no]=$studentMatrixNo;?>
							<?echo "myStudentMatrixNo[$no] ".$myStudentMatrixNo[$no];?>
							
							<td><input type="text" name="myStudentName[]" size="30" id="studentName" value="<?=$studentName;?>" disabled="disabled"/></td>
							<td><input type="text" name="myReportDate[]" size="15" id="reportDate" value="<?=$reportDate;?>" disabled="disabled"/></td>						
							<td><textarea name="myThesisTitle[]" cols="45" id="thesisTitle" value="<?=$thesisTitle;?>" disabled="disabled"><?=$thesisTitle?></textarea></td>
							<?						
							$result1 = $dba->query($sql1);
							$dba->next_record();							
							$supervisorStatus=$dba->f('supervisor_status');							
							if (strcmp($supervisorStatus,'A')==true) {?>
								<td><a href="../supervisor/edit_supervisor.php?pid=<?php echo $id?>&matrixNo=<? echo $studentMatrixNo?>&sname=<? echo $studentName?>">Change</a></td>
								<td><a href="../supervisor/view_supervisor.php?pid=<?php echo $id?>&matrixNo=<? echo $studentMatrixNo?>&sname=<? echo $studentName?>">View</a></td>
							<?}
							else {?>
								<td><a href="../supervisor/edit_supervisor.php?pid=<?php echo $id?>&matrixNo=<? echo $studentMatrixNo?>&sname=<? echo $studentName?>" class="style1">Assign</a></td>
							<?}?>															
						</tr>
					<?
					$no=$no+1;
					}while($db->next_record());	
					?>
					<?$_SESSION['myPgThesisId'] = $myPgThesisId;?>				
					<?$_SESSION['myStudentMatrixNo'] = $myStudentMatrixNo;?>
					
					<br />
			 <br />
			 <table>
				<tr>
					<td><strong>Thesis proposal readiness status for Senate approval</strong></td>
				</tr>
				<tr>
					<td><strong>Note:</strong> To check the 'Outline of Proposed Case Study' by the student please click on Thesis/Project ID above.</td>
				</tr>
				<br/>
			</table>
			<br/>
			 <table>				 
				 <tr>
					<td><p>Confirmation status</p></td>
					<td>				
					  <input type="radio" name="confirmStatus" value="APP" checked="checked"/>Approved</label>
					  <input name="confirmStatus" type="radio" value="REQ" />Request Changes</label>
					  <label><input type="radio" name="confirmStatus" value="DIS" />Disapproved</label>		  
					</td>
				</tr>
				<tr>
					<td>Remarks</td>
					<td><textarea name="assignedRemark" cols="50" id="assignedRemark"></textarea></td>
				</tr>
				<?$_SESSION['myApprovalBox'] = $myApprovalBox;?>
				<tr>				
					<td></td>
					 <td><input type="submit" name="Submit" value="Attachment" /></td>
				 </tr>				
			</table>
			<table>
				<tr>				
					<td></td>
				</tr>
				 <tr>				
					<td><input type="submit" name="btnSubmit" value="Submit" /></td>
				</tr>
			</table>
					
					

					<?
				}
				else {
					?>
					
					<table>
						<tr>
							<td>
								<p>There is no proposal available for review and supervisor appointment.</p>
							</td>
						</tr>
					</table>
					<?
				}				
				?>					
			</table>		

			
		</form>
	</body>
</html>




