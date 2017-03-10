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
		a.feedback_date, a.feedback_remarks, a.status, a.discussion_status, a.proposal_remarks, 
		c.description AS theProposalStatusDescription, d.student_matrix_no, e.name,
		ps.ref_supervisor_type_id,ps.skype_id,ps.expertise,(SELECT count(pg_employee_empid) FROM pg_supervisor WHERE pg_student_matrix_no='" . $studentMatrixNo . "') lecturer_count,ne.name as lecturer_name ,ne.empid as lecturer_id,ne.mobile,rst.description
		FROM ref_thesis_type b
		LEFT JOIN pg_proposal a ON (a.thesis_type = b.id)
		LEFT JOIN ref_proposal_status c ON (c.id = a.status) 
		LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id) 
		LEFT JOIN student e ON (e.matrix_no = d.student_matrix_no)
		LEFT JOIN pg_supervisor ps ON (ps.pg_student_matrix_no = e.matrix_no)
		LEFT JOIN ref_supervisor_type rst ON (rst.id=ps.ref_supervisor_type_id)
		LEFT JOIN new_employee ne ON (ps.pg_employee_empid=ne.empid)
		WHERE a.status = 'INP' AND d.status = 'INP'";
		
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
	$approvalStatus=$_POST['approvalStatus'];
	/*echo "approvalStatus ".$approvalStatus."<br/>";
	echo "senateDate ".$senateDate."<br/>";
	echo "respondedByDate ".$respondedByDate."<br/>";
	echo "approvalRemark ".$approvalRemark."<br/>";*/
	
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
		echo "myReportDate [".$val."] "."val ".$myReportDate[$val]."<br/>";*/
		
		$sql5 = "UPDATE pg_supervisor
		SET respondedby_date = '$respondedByDate', senate_date = '$senateDate', approved_date = '$curdatetime',
		approval_remark = '$approvalRemark', approved_by = '$userid' 
		WHERE id = '$myStudentMatrixNo[$val]'
		AND STATUS = 'A'";
		
		$result5 = $dbd->query($sql5); 
		//echo "sql5".$sql5;
		//var_dump($dbd);
		$dbd->next_record();
		
		$tmpSenateDate = date('Y-m-d', strtotime($senateDate));
	
		$sql6 = "UPDATE pg_proposal 		
		SET senate_date = '$tmpSenateDate', approved_date = '$curdatetime',
		approval_remark = '$approvalRemark', approved_by = '$userid', STATUS = '$approvalStatus'
		WHERE id = '$myPgThesisId[$val]'
		AND STATUS = 'INP'";
		
		$result6 = $dbg->query($sql6); 			
		//echo "sql6".$sql6;
		//var_dump($dbg);
		$dbg->next_record();
			
			
	}
	
	$sql4 = "UPDATE pg_thesis
			SET ref_thesis_status_id_proposal = 'CMP', modify_by = '$userid', modify_date = '$curdatetime'
			WHERE id = '$myPgThesisId[$val]'
			AND student_matrix_no = '$myStudentMatrixNo[$val]'";

			$result4 = $dbc->query($sql4); 
			//echo "sql4".$sql4;
			//var_dump($dbc);
			$dbc->next_record();

	
			

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
		<script type="text/javascript" src="//cdn.ckeditor.com/4.4.6/standard/ckeditor.js"></script>		
	    <style type="text/css">
<!--
.style1 {color: #FF0000}
-->
        </style>
</head>
	<body>  
	
		<form id="form1" name="form1" method="post" enctype="multipart/form-data">	
		<input type="hidden" name="empid" id="empid" value="<?php echo $user_id; ?>">			
		<br />
		<table>
			<tr>
			  <td><strong>List of Thesis Pending for Senate Approval</strong></td>      
			</tr>
		</table>
		
		<table border="1" width="100%">
			<?  
				if ($result2) {?>

				<tr>						
					<td width="31" nowrap="nowrap">Tick</td>	
					<td width="29">No.</td>					
					<td width="109" nowrap="nowrap">Thesis/Project ID</td>
					<td width="250">Student Info</td>												
					<?php /*?><td width="314">Student Name</td><?php */?>
					<td width="192">Thesis Date</td>
					<td width="389">Thesis/Project Title</td>
					<td width="271">Supervisor Info</td>																																				
				</tr>
				<?
				$no=0;
				do {						
					$pgThesisId=$db->f('pg_thesis_id');	
					$studentMatrixNo=$db->f('student_matrix_no');
					$studentName=$db->f('name');						
					$id=$db->f('id');
					$reportDate=$db->f('theReportDate');
					$thesisTitle=$db->f('thesis_title');
					$lecturer_name=$db->f('lecturer_name');
					$lecturer_id=$db->f('lecturer_id');
					$description=$db->f('description');
														
														
				?>
					<tr>
						<td><input name="myApprovalBox[]" type="checkbox" value="<?=$no;?>" /></td>	
						
						<input type="hidden" name="myProposalId[]" size="12" id="proposalId" value="<?=$proposalId;?>"/>
						<?$myPgThesisId[$no]=$pgThesisId;?>
											
						<td align="center"><?php echo $no+1;?></td>	
						
						<td><?php echo $pgThesisId;?></td>	
						<? $myPgThesisId[$no]=$pgThesisId;?>
						<? //echo "myPgThesisId[$no] ".$myPgThesisId[$no];?>
						
						<td><input type="hidden" name="myStudentName[]" size="30" id="studentName" value="<?=$studentName;?>" disabled="disabled"/><?php echo $studentName;?>
						<input type="hidden" name="myStudentMatrixNo[]" size="12" id="studentMatrixNo" value="<?=$studentMatrixNo;?>" disabled="disabled"/> - <?php echo $studentMatrixNo;?></td><? $myStudentMatrixNo[$no]=$studentMatrixNo;?>
						<td><input type="hidden" name="myReportDate[]" size="15" id="reportDate" value="<?=$reportDate;?>" disabled="disabled"/><?php echo $reportDate;?></td>						
						<td><input type="hidden" name="myThesisTitle[]" id="thesisTitle" value="<?=$thesisTitle;?>" disabled="disabled"/><?php echo $thesisTitle; ?></td>
						<?	$sqlSupervisor="SELECT ps.ref_supervisor_type_id,ps.skype_id,ps.expertise,ne.name as name,ne.empid,ne.mobile,rst.description  
									FROM  pg_supervisor ps 
									LEFT JOIN ref_supervisor_type rst ON (rst.id=ps.ref_supervisor_type_id)
									LEFT JOIN new_employee ne ON (ps.pg_employee_empid=ne.empid)
									WHERE ps.pg_student_matrix_no='$studentMatrixNo'";
							$result = $db_klas2->query($sqlSupervisor);
							$varRecCount=0;					
							while($row = mysql_fetch_array($result)) 
							{ 
								$varRecCount++;
								echo "<td>".$row["name"]." <br \>
								".$row["empid"]." <br \>
								".$row["description"]." <br \> " ;
							
							} 
						 echo " </td>";
						?>
						
																				
					</tr>
				<?
				$no=$no+1;
				}while($db->next_record());	
				?>
				<? $_SESSION['myPgThesisId'] = $myPgThesisId;?>				
				<? $_SESSION['myStudentMatrixNo'] = $myStudentMatrixNo;?>

				<?
			}
			else {
				?>
				<table border="1">
					<tr>
						<td>
							<p>No record found to display!</p>
						</td>
					</tr>
				</table>
				<?
			}				
			?>					
		</table>		
		<table>
			<tr>
				<td><INPUT name="btnPrintList" TYPE="button" onClick="open_win('pdf_thesis_list.php?',900,700,0,0,0,0,1,1,0,0,5);return false;" VALUE="Print List of Thesis"></td>				
			</tr>
		</table>
		
		<br />
		 <br />
		 <table>
			<tr>
				<td><strong>Thesis Proposal for Approval</strong></td>
			</tr>
		</table>
		 <table>
			 <tr>
				<td>Senate Approval Date</td>
				<td><input type="text" name="senateDate" size="15" id="senateDate" value="<?=date('d-M-Y');?>"/></td>
			 </tr>
			 <?$result3 = $dbb->query($sql3);
			 $parameterValue=$dbb->f('value');
			 $currentDate = date('Y-m-d H:i:s');?>			 
			 <tr>
				<td>Supervisor/Co-Supervisor to respond the invitation by</td>
				<td><input type="text" name="respondedByDate" size="15" id="respondedByDate" value="<?=date('d-M-Y', strtotime($currentDate. ' + '.$parameterValue.' days'));?>"/></td>
			 </tr>
			 <tr>
				<td><p>Approval Status</p></td>
				<td>				
				  <input type="radio" name="approvalStatus" value="APP" checked="checked"/>Approved</label>
				  <input name="approvalStatus" type="radio" value="REQ" />Request with Changes</label>
				  <label><input type="radio" name="approvalStatus" value="DIS" />Disapproved</label>		  
				</td>
			</tr>
			<tr>
				<td>Remarks</td>
				<td><textarea name="approvalRemark" cols="50" id="approvalRemark"></textarea></td>
			</tr>
			<? $_SESSION['myApprovalBox'] = $myApprovalBox;?>
							
		</table>
		<table>
			 <tr>
				   <td>Supporting attachment for the approval </td>
				   <td><input type="submit" name="Submit" value="Attachment" /></td>
			 </tr>
		</table>
		<table>
			<tr>				
				<td><input type="submit" name="btnSubmit" value="Submit" /></td>
			</tr>
		</table>
	   </p>
	  </form>
	</body>
</html>




