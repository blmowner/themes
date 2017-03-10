<?php
include("../../../lib/common.php");
checkLogin(); 

session_start();
$userid=$_SESSION['user_id'];
$studentMatrixNo=$_SESSION['studentMatrixNo'];
$curdatetime = date("Y-m-d H:i:s");


?>
<?
$sql2 = " SELECT a.pg_thesis_id , a.id, DATE_FORMAT(a.report_date,'%d-%b-%Y') AS theReportDate, a.thesis_title, a.thesis_type, 
		b.description AS theThesisTypeDescription, a.introduction, a.objective, a.description, a.feedback_by, 
		a.feedback_date, a.feedback_remarks, a.status, a.discussion_status,
		c.description AS theProposalStatusDescription, d.student_matrix_no,
		ps.ref_supervisor_type_id,ps.skype_id,ps.expertise,ne.name as lecturer_name ,ne.empid as lecturer_id,ne.mobile,rst.description
		FROM ref_thesis_type b
		LEFT JOIN pg_proposal a ON (a.thesis_type = b.id)
		LEFT JOIN ref_proposal_status c ON (c.id = a.status) 
		LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id) 
		LEFT JOIN pg_supervisor ps ON (ps.pg_thesis_id = d.id)
		LEFT JOIN ref_supervisor_type rst ON (rst.id=ps.ref_supervisor_type_id)
		LEFT JOIN new_employee ne ON (ps.pg_employee_empid=ne.empid)
		WHERE a.status = 'INP' AND d.status = 'INP'
		GROUP BY e.matrix_no ";
		
		$result2 = $db->query($sql2); 
		//echo $sql2;
		//var_dump($db);
		$db->next_record();
		
		
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Job Application</title>
<link rel="stylesheet" type="text/css" href="../../theme/css/<?php echo $css; ?>" />
<link rel="stylesheet" type="text/css" href="../../theme/css/colorbox.css" media="screen" />
<link rel="stylesheet" href="../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />

<script src="../../lib/js/jquery.min2.js"></script>
<script src="../../lib/js/jquery.colorbox.js"></script>
<script type="text/javascript" src="../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
<script language="JavaScript" type="text/javascript" src="../../lib/js/tooltip.js"></script>
<script src="../../lib/js/datePicker/jquery.ui.core.js"></script>
<script src="../../lib/js/datePicker/jquery.ui.widget.js"></script>
<script src="../../lib/js/datePicker/jquery.ui.datepicker.js"></script>
<script language="JavaScript" src="../js/windowopen.js"></script>
<script src="../../lib/js/datePicker/jquery.ui.datepicker.js"></script>

</head>

<body>
<div class="margin-5">
<!--h3>Application Record</h3><br /-->



<form method="post" id="form-set" name="f1" enctype="multipart/form-data">
<input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id; ?>">
	
<table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td width="28%" height="75" rowspan="6" align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="../../../theme/images/msuLogo.gif" width="191" height="68" border="0"></td>
        <br \>
        <td width="72%" align="center"><span class="style1"><b>LIST OF THESIS/PROJECT TITLE FOR SENATE APPROVAL</b></span></td>
    </tr>
    
</table>
<br \>
<p \>
<table border="1" width="100%">
			<tr>						
					<td width="29">No.</td>					
					<td width="109" nowrap="nowrap" align="center">Thesis/Project ID</td>
					<td width="250" align="center">Student Info</td>												
					<td width="192" align="center">Thesis Date</td>
					<td width="389" align="center">Thesis/Project Title</td>
					<td width="271" align="center">Supervisor Info</td>																																				
			</tr>
	
			<?
				$no=0;
				do {						
					$pgThesisId=$db->f('pg_thesis_id');	
					$studentMatrixNo=$db->f('student_matrix_no');
					$id=$db->f('id');
					$reportDate=$db->f('theReportDate');
					$thesisTitle=$db->f('thesis_title');
					$lecturer_name=$db->f('lecturer_name');
					$lecturer_id=$db->f('lecturer_id');
					$description=$db->f('description');
														
														
				?>
			
			<tr>
														
						<td align="center"><?php echo $no+1;?></td>	
						
						<td><?php echo $pgThesisId;?></td>	
						<? $myPgThesisId[$no]=$pgThesisId;?>
						<? //echo "myPgThesisId[$no] ".$myPgThesisId[$no];?>
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
						<td><input type="hidden" name="myStudentName[]" size="30" id="studentName" value="<?=$studentName;?>" disabled="disabled"/><?php echo $studentName;?>
						<input type="hidden" name="myStudentMatrixNo[]" size="12" id="studentMatrixNo" value="<?=$studentMatrixNo;?>" disabled="disabled"/> - <?php echo $studentMatrixNo;?></td><? $myStudentMatrixNo[$no]=$studentMatrixNo;?>
						<td><input type="hidden" name="myReportDate[]" size="15" id="reportDate" value="<?=$reportDate;?>" disabled="disabled"/><?php echo $reportDate;?></td>						
						<td><input type="hidden" name="myThesisTitle[]" id="thesisTitle" value="<?=$thesisTitle;?>" disabled="disabled"/><?php echo $thesisTitle; ?></td>
						<td>
						<?	$sqlSupervisor="SELECT ps.ref_supervisor_type_id,ps.skype_id,ps.expertise,ne.name AS name,ne.empid,
									ne.mobile,rst.description  
									FROM  pg_supervisor ps 
									LEFT JOIN ref_supervisor_type rst ON (rst.id=ps.ref_supervisor_type_id)
									LEFT JOIN new_employee ne ON (ps.pg_employee_empid=ne.empid)
									WHERE ps.pg_student_matrix_no='$studentMatrixNo'
									GROUP BY name,ps.pg_student_matrix_no";
							$result = $db_klas2->query($sqlSupervisor);
							$varRecCount=0;
							while($row = mysql_fetch_array($result)) 
							{ 
								$varRecCount++;
								echo "".$row["name"]." ( ".$row["empid"]." ) <br \>
								".$row["description"]." <br \> " ;
							} 
						 
						?>
						</td>
			</tr>
			<?
				$no=$no+1;
				}while($db->next_record());	
				?>
</table>		
<br \>
<br \>

<table>
	<tr>
		<td>Signature:&nbsp;____________________________________</td>
		<td align="center">&nbsp;</td>
		<td align="center">&nbsp;</td>
	</tr>
	<? 
		$sqlEmp = "SELECT name,empid FROM new_employee WHERE empid='$user_id' ";
		$dbc->query($sqlEmp); 
		$row_personal=$dbc->fetchArray();
		
		$name=$row_personal['name'];
		$empid=$row_personal['empid'];
	?>
	<tr>
		<td>Approve By:&nbsp;<?=$name?>&nbsp;(<?=$empid?>)</td>
		<td align="center">&nbsp;</td>
		<td align="center">&nbsp;</td>
	</tr>
	<tr>
		<td>Approve Date / Time:&nbsp;<?=date('Y-m-d H:i:s')?></td>
		<td align="center">&nbsp;</td>
		<td align="center">&nbsp;</td>
	</tr>
	<?php /*?><tr>
		<td>Date / Time:&nbsp;<?=date('j-n-Y H:i:s')?></td>
		<td align="center">&nbsp;</td>
		<td align="center">&nbsp;</td>
	</tr><?php */?>
</table>
</form>	
</div>
</body>
</html>