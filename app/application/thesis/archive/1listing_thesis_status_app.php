<?
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
$endorsedDate=$_GET['endorsedDate'];


if(isset($_POST['btnSearch']) && ($_POST['btnSearch'] <> "")) {
	$searchRequestDate = $_POST['searchRequestDate'];
	
	$sql2 = "SELECT a.pg_thesis_id , a.id, DATE_FORMAT(a.report_date,'%d-%b-%Y') AS theReportDate, a.thesis_title, a.thesis_type, 
		b.description AS theThesisTypeDescription, a.introduction, a.objective, a.description, a.verified_by, 
		DATE_FORMAT(a.verified_date,'%d-%b-%Y') AS verified_date,
		a.verified_remarks, a.verified_status,a.status as endorsedStatus, a.discussion_status,  
		c1.description AS verifiedDesc, c2.description AS endorsedDesc, d.student_matrix_no, 
		a.endorsed_remarks, DATE_FORMAT(f.endorsed_date,'%d-%b-%Y') AS endorsed_date
		FROM pg_proposal a
		LEFT JOIN ref_thesis_type b ON (b.id = a.thesis_type)
		LEFT JOIN ref_proposal_status c1 ON (c1.id = a.verified_status) 
		LEFT JOIN ref_proposal_status c2 ON (c2.id = a.status)
		LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id) 
		LEFT JOIN pg_proposal_approval f ON (f.id = a.pg_proposal_approval_id)
		WHERE a.status in ('APP','APC') 
		AND DATE_FORMAT(f.endorsed_date,'%d-%b-%Y') = '$searchRequestDate' 	
		AND a.archived_status IS NULL 
		AND d.status = 'INP'
		ORDER BY a.pg_thesis_id, a.id";		
		
		$result2 = $db->query($sql2); 
		$db->next_record();
		$result2 = $db->query($sql2); 
		$row_cnt = mysql_num_rows($result2);

} else 
{
	$sql2 = "SELECT a.pg_thesis_id , a.id, DATE_FORMAT(a.report_date,'%d-%b-%Y') AS theReportDate, a.thesis_title, a.thesis_type, 
		b.description AS theThesisTypeDescription, a.introduction, a.objective, a.description, a.verified_by, 
		DATE_FORMAT(a.verified_date,'%d-%b-%Y') AS verified_date,
		a.verified_remarks, a.verified_status,a.status as endorsedStatus, a.discussion_status, 
		c1.description AS verifiedDesc, c2.description AS endorsedDesc, d.student_matrix_no, c1.description AS facultyStatus,
		a.endorsed_remarks, DATE_FORMAT(f.endorsed_date,'%d-%b-%Y') AS endorsed_date
		FROM pg_proposal a
		LEFT JOIN ref_thesis_type b ON (b.id = a.thesis_type)
		LEFT JOIN ref_proposal_status c1 ON (c1.id = a.verified_status) 
		LEFT JOIN ref_proposal_status c2 ON (c2.id = a.status)
		LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id) 
		LEFT JOIN pg_proposal_approval f ON (f.id = a.pg_proposal_approval_id)
		WHERE a.status in ('APP','APC') 
		AND (DATE_FORMAT(f.endorsed_date,'%Y-%m-%d') = CURDATE() OR DATE_FORMAT(f.endorsed_date,'%d-%b-%Y') = '$searchRequestDate')
		AND a.archived_status IS NULL 
		AND d.status = 'INP'
		ORDER BY a.pg_thesis_id, a.id, f.endorsed_date DESC";		
		
		$result2 = $db->query($sql2); 
		$db->next_record();
		$result2 = $db->query($sql2); 
		$row_cnt = mysql_num_rows($result2);
}


?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Untitled Document</title>
		<link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
		<link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
		<link rel="stylesheet" href="../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" /> 
		<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
		 
		<script src="../../lib/js/jquery.min2.js"></script>
		<script src="../../lib/js/jquery.colorbox.js"></script>
		<script src="../../lib/js/jquery.mask_input-1.3.js"></script>
		<script type="text/javascript" src="../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
		<script src="../../lib/js/datePicker/jquery.ui.core.js"></script>
		<script src="../../lib/js/datePicker/jquery.ui.widget.js"></script>
		<script src="../../lib/js/datePicker/jquery.ui.datepicker.js"></script>
		<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>	
		<script language="JavaScript" src="../js/windowopen.js"></script>	
		
	   		
</head>
	<body>  
	
		<form id="form1" name="form1" method="post" action="<? echo $_SERVER["PHP_SELF"]; ?>?endorsedDate=<?=$searchRequestDate?>" enctype="multipart/form-data">	
		<input type="hidden" name="empid" id="empid" value="<?php echo $user_id; ?>">			
		
			
			<fieldset>
			<legend><strong>List of Thesis Proposal - Approved Thesis by Senate</strong></legend><br/>	
			<table>
						<tr>							
							<td><strong>Please enter searching criteria below</strong> (by default it will display all current thesis which has been approved by Senate)</td>
						</tr>
			</table>
			<table>
			<tr>
				<? $tmpEndorsedDate = date("d-M-Y"); ?>
				<td>Senate Meeting Date</td>	
				<td>:</td>							
				<td>
					<select name = "searchRequestDate">									
										<option value="<?=$endorsedDate?>" size="30"  ></option>
										<?
										$sql3 = "SELECT DISTINCT DATE_FORMAT(endorsed_date,'%d-%b-%Y') AS endorsed_date FROM pg_proposal_approval ORDER BY endorsed_date DESC";
										 
										$dbf->query($sql3); 
										$dbf->next_record();
										do {
											$endorsedDate=$dbf->f('endorsed_date');	
											if ($searchRequestDate==$endorsedDate) 
											{
												?>
													<option value="<?=$endorsedDate?>" size="30" selected="selected"><?=$endorsedDate?></option>
												<?
											}
											else 
											{
												?>
													<option value="<?=$endorsedDate?>" size="30" ><?=$endorsedDate?></option>										
												<?
											}
										}while ($dbf->next_record());
										?>
									</select>
				</td>			
				<td><input type="submit" name="btnSearch" value="Search" /></td>
			</tr>
		</table>
		
		<br/>
		
		<table>
			<tr>							
				<td>Searching Results:- <?=$row_cnt?> record(s) found.</td>
			</tr>
		</table>
		<?
		if($row_cnt >5)
		{ ?>
			<div id = "tabledisplay" style="overflow:auto; height:400px;">
		<?
		}
		else
		{ ?>
			<div id = "tabledisplay">	
		<?
		}
		?>				
		
			<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="100%" class="thetable">
						

				<tr>						
					<th width="25" align="center"><strong>No.</strong></th>					
					<th width="131"><strong>Thesis/Project ID</strong></th>
					<th width="208"><strong>Thesis/Project Title</strong></th>
					<th width="151"><strong>Student </strong></th>												
					<th width="151"><strong>Supervisor </strong></th>
					<th width="100"><strong>Verified by Faculty</strong></th>
					<th width="100"><strong>Endorsed by Senate</strong></th>
				</tr>
				<?  
			
			if ($row_cnt>0) {?>
			<?
				$no=0;
				$myNo=1;
				while($db->next_record()) {						
					$pgThesisId=$db->f('pg_thesis_id');	
					$studentMatrixNo=$db->f('student_matrix_no');
					$proposalId=$db->f('id');
					$reportDate=$db->f('theReportDate');
					$thesisTitle=$db->f('thesis_title');
					$lecturer_name=$db->f('lecturer_name');
					$lecturer_id=$db->f('lecturer_id');
					$description=$db->f('description');
					$verified_status=$db->f('verified_status');
					$verifiedDesc=$db->f('verifiedDesc');
					$endorsedRemarks=$db->f('endorsed_remarks');
					$endorsedStatus=$db->f('endorsedStatus');
					$endorsedDesc=$db->f('endorsedDesc');
					$endorsedDate=$db->f('endorsed_date');
					$verifiedDate=$db->f('verified_date');								
														
				?>
					<tr>
											
						<input type="hidden" name="myProposalId[]" size="12" id="proposalId" value="<?=$proposalId;?>"/>
						<? $myProposalId[$no]=$proposalId;?>
						<?php /*?><?$myProposalId[$no];?><?php */?>
						
						
						<td align="center"><?=$myNo++;?></td>	
						
						<td><a href="../thesis/thesis_approval_outline.php?thesisId=<? echo $pgThesisId;?>&proposalId=<? echo $proposalId;?>&endorsedDate=<?=$searchRequestDate;?>&lot=A" name="myPgThesisId[]" value="<?=$pgThesisId?>" title="Outline of Proposed Case Study by the Student - Read more..."><?=$pgThesisId;?><br/>
						
						<? $myPgThesisId[$no]=$pgThesisId;?>
						
						<td><label name="myThesisTitle[]" id="thesisTitle" ></label><?=$thesisTitle; ?></td>
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
						<td><label name="myStudentName[]" size="30" id="studentName" ></label><?=$studentName;?>
						(<?=$studentMatrixNo;?>)</td>
						<? $myStudentMatrixNo[$no]=$studentMatrixNo;?>
												
						<td>
						<?	$sqlSupervisor="SELECT ps.id, ps.ref_supervisor_type_id,
									ps.pg_employee_empid, rst.description  
									FROM  pg_supervisor ps 
									LEFT JOIN ref_supervisor_type rst ON (rst.id = ps.ref_supervisor_type_id) 
									WHERE ps.pg_student_matrix_no='$studentMatrixNo' 
									AND ps.ref_supervisor_type_id IN ('SV','CS','XS') 
									AND ps.pg_thesis_id = '$pgThesisId' 
									AND ps.status = 'A' 
									ORDER BY rst.seq";

							
							$result_sqlSupervisor = $db_klas2->query($sqlSupervisor);	
							$row_cnt = mysql_num_rows($result_sqlSupervisor);							
							$no1=1;
							if ($row_cnt>0) {
								
								while($row = mysql_fetch_array($result_sqlSupervisor)) 
								{ 
									$sql1 = "SELECT name
									FROM new_employee
									WHERE empid =  '".$row['pg_employee_empid']."' ";		

									$result1 = $dbc->query($sql1); 
									$dbc->next_record();
									$staffName=$dbc->f('name');
									?>
									<?=$no1?>. <?=$staffName;?> (<?=$row["pg_employee_empid"];?>) - <?=$row["description"];?> <br/>								
								<? $no1++;}?>
								
							<? }
							else {
								?> 
								
							<? }						 
						?>
						</td>
						<td><label name="myVerifiedDesc[]" id="verifiedDesc" ></label><?=$verifiedDesc; ?><br/>(<?=$verifiedDate;?>)</td>
						<td><label name="myEndorsedDesc[]" id="endorsedDesc" ></label><?=$endorsedDesc; ?><br/>(<?=$endorsedDate;?>)</td>
										  
					</tr>
				<?
				$no=$no+1;
				};	
				?>
				<? $_SESSION['myPgThesisId'] = $myPgThesisId;?>				
				<? $_SESSION['myStudentMatrixNo'] = $myStudentMatrixNo;?>
		</table>				
		</div>
		<br />		
		<td><input type="button" name="btnPrintProposal" value="Print Listing" onclick="javascript:document.location.href='../thesis/pdf_thesis_approval_app.php?smd=<?=$searchRequestDate?>';" /> </td><br/>
		<br />	

		<? }
		else {
			?>
			
			
			<table>
				<tr>
					<td>There is no thesis available for Senate view.</td>
				</tr>
			</table>
			</fieldset>
			<?
		}?>

	  </form>
	</body>
</html>




