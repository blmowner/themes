<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: view_supervisor.php
//
// Created by: Zuraimi
// Created Date: 29-Dec-2014
// Modified by: Zuraimi
// Modified Date: 29-Dec-2014
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

session_start();
$userid=$_SESSION['user_id'];
$matrixNo=$_REQUEST['mn'];
$sname=$_REQUEST['sname'];
$thesisId=$_REQUEST['tid'];

//used for pagination-------------------
	$page = ($_GET['page'] == 0 ? 1 : $_GET['page']);
	$perpage = 2;
	$startpoint = ($page * $perpage) - $perpage;

$varParamSend="";
$curdatetime = date("Y-m-d H:i:s");

foreach($_REQUEST as $key => $value)
{
	if($key!="page")
		$varParamSend.="&$key=$value";
}
//---------------------------------------


$sql6 = "SELECT a.id, a.pg_employee_empid, 
		a.ref_supervisor_type_id, e.description AS stype, a.acceptance_status, f.description as  acceptance_desc,
		DATE_FORMAT(a.acceptance_date,'%d-%b-%Y') AS acceptance_date, DATE_FORMAT(a.assigned_date,'%d-%b-%Y') AS assigned_date,
		DATE_FORMAT(a.respondedby_date,'%d-%b-%Y') AS respondedby_date
		FROM pg_supervisor a
		LEFT JOIN ref_supervisor_type e ON (e.id = a.ref_supervisor_type_id)
		LEFT JOIN ref_acceptance_status f ON (f.id = a.acceptance_status)
		WHERE a.pg_student_matrix_no = '$matrixNo' 
		AND a.ref_supervisor_type_id IN ('RV')
		AND pg_thesis_id = '$thesisId'
		AND a.status = 'A' 
		ORDER BY e.seq, a.pg_employee_empid";

		$dbe->query($sql6); 
		//$dbe->next_record();
		//echo $sql6;
		
$sql3 = "SELECT id, description
		FROM ref_supervisor_type
		WHERE status='A'";
 
$dbf->query($sql3); 
$dbf->next_record();
//echo $sql3;	
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
		<script language="JavaScript" src="../../../lib/js/windowopen.js"></script>	
		<script language="JavaScript" type="text/javascript" src="../../../lib/js/tooltip.js"></script>		
	</head>
	
	<body>
	<form id="form1" name="form1" method="post" enctype="multipart/form-data">
		<fieldset>
			<legend><strong>List of Assigned Reviewer</strong></legend>
		<br/>
		<table>
			<tr>
				<td>Student Matrix No</td>
				<td>:</td>
				<td><?=$matrixNo?></td>
			</tr>
			<?
					$sql5 = "SELECT name as student_name
							FROM student 
							WHERE matrix_no = '$matrixNo'";
							if (substr($matrixNo,0,2) != '07') { 
								$dbConnStudent= $dbc; 
							} 
							else { 
								$dbConnStudent=$dbc1; 
							}	
							$result5 = $dbConnStudent->query($sql5); 
							$dbConnStudent->next_record();
							$sname=$dbConnStudent->f('student_name');
				
				?>
			<tr>
				<td>Student Name</td>
				<td>:</td>
				<td><?=$sname?></td>
			</tr>
		</table>
		<br/>
		<table width="100%" border="1" cellpadding="3" cellspacing="1" style="border-collapse:collapse;" class="thetable">
			<tr>				
				<th align="center"><strong>No.</strong></th>
				<th><strong>Dept</strong></th>
				<th><strong>Name</strong></th> 
				<th><strong>Qualification</strong></th>
				<th><strong>Field of Expertise</strong></th>								
				<th><strong>Email ID</strong></th>		  
				<th><strong>Assigned Date</strong></th>
				<th><strong>Expected Reply Date</strong></th>					
				<th><strong>Acceptance Status</strong></th>
			</tr>    
			<?
			$dbe->query($sql6); 
			$dbe->next_record();
			//echo $sql6;
			$no1=0;
			$myArrayNo=0;
			do {						
				$supervisorId=$dbe->f('id');	
				$employeeId=$dbe->f('pg_employee_empid');	
				//$departmentId=$dbe->f('dept_id');
				//$department=$dbe->f('department');
				$acceptanceDesc=$dbe->f('acceptance_desc');
				$acceptanceDate=$dbe->f('acceptance_date');
				$assignedDate=$dbe->f('assigned_date');
				$respondedByDate=$dbe->f('respondedby_date');
			?>
			<tr>
				<input type="hidden" name="supervisorId" value="<?=$supervisorId;?>"/>								
				<td align="center"><?=$no1+1;?>.</td>
				<?
				$sql4="SELECT a.id as dept_id, a.description as department
				FROM dept_unit a
				LEFT JOIN new_employee b ON (b.unit_id = a.id)
				WHERE empid = '$employeeId'";
				
				$dbc->query($sql4);
				$row_personal=$dbc->fetchArray();
				$departmentId=$row_personal['dept_id'];
				$department=$row_personal['department'];
				?>				
				<td><a><width="20" height="19" style="border:0px;" title="<?=$department;?>"><?=$departmentId;?></a></td>				
				<?
					$sql4 = "SELECT name as employee_name
							FROM new_employee 
							WHERE empid = '$employeeId'";
								
							$result4 = $dbc->query($sql4); 
							$dbc->next_record();
							$employeeName=$dbc->f('employee_name');
				
				?>
				<td><label name="myEmployeeName" size="50" id="employeeName"></label><?=$employeeName;?><br/>(<?=$employeeId;?>)</td>
									
				<td>
					<?
					$sql_expertise1 = "SELECT descrip
					FROM education
					WHERE empid='$employeeId'
					AND LEVEL IN ('4','5')
					ORDER BY LEVEL";
					
					$sql_expertise1;
					$result_sql_expertise1 = $dbc->query($sql_expertise1); 
					$dbc->next_record();
					
					do {
						$educationDesc1=$dbc->f('descrip');	
					?>
					
					<label name="qualification" size="50" id="qualification" >- <?=$educationDesc1;?></label><br/>
					<?
					} while ($dbc->next_record())					

					?>					
					</td>
					<?
					
					$sqlProposalArea = "SELECT job_id1_area, job_id2_area, job_id3_area, job_id4_area, job_id5_area, job_id6_area
					FROM pg_proposal_area
					WHERE pg_proposal_id = '$proposalId'";
					
					$resultSqlProposalArea = $dbg->query($sqlProposalArea); 
					$dbg->next_record();
					
					$jobIdArea1=$dbg->f('job_id1_area');
					$jobIdArea2=$dbg->f('job_id2_area');
					$jobIdArea3=$dbg->f('job_id3_area');
					$jobIdArea4=$dbg->f('job_id4_area');
					$jobIdArea5=$dbg->f('job_id5_area');
					$jobIdArea6=$dbg->f('job_id6_area');
						
					$sqlAreaExpertise = "SELECT a.expertise, b.area
					FROM employee_expertise a
					LEFT JOIN job_list_category b ON (b.jobarea = a.expertise)
					WHERE empid = '$employeeId'";
					
					$resultSqlAreaExpertise = $dbc->query($sqlAreaExpertise); 
					$dbc->next_record();
					$recommended=0;
					$tmpAreaExpertise="";
					do {
						$expertise=$dbc->f('expertise');
						$area=$dbc->f('area');
						
						if (($expertise==$jobIdArea1 || $expertise==$jobIdArea2 || $expertise==$jobIdArea3 || $expertise==$jobIdArea4 || $expertise==$jobIdArea5 || $expertise==$jobIdArea6))				
						{
							if($tmpAreaExpertise!="" || $expertise!=""){
								$tmpAreaExpertise = $area." ".$tmpAreaExpertise;
								$recommended=1;
							}else{
								$tmpAreaExpertise = $area." ".$tmpAreaExpertise;
								$recommended=0;
							}
						}else
						{
							if($tmpAreaExpertise!="" || $expertise!=""){
								$tmpAreaExpertise = $tmpAreaExpertise." ".$area;
								$recommended=1;
							}else{
								$tmpAreaExpertise = $tmpAreaExpertise." ".$area;
								$recommended=0;
							}
						}
						
					} while ($dbc->next_record());?>
							
				<td><label name="$areaExpertise"><?=$tmpAreaExpertise;?></label></td>			
							
				<?
					$sql4 = "SELECT email
							FROM new_employee 
							WHERE empid = '$employeeId'";
								
							$result4 = $dbc->query($sql4); 
							$dbc->next_record();
							$emailId=$dbc->f('email');
				
				?>
				<td><label  name="myEmailId" id="emailId" size="20" ></label><?=$emailId?></td>
				<td><label name="myAssignedDate[]" size="15" id="assignedDate" ><?=$assignedDate;?></label></td>
			
					<td><label name="myRespondedByDate[]" size="15" id="respondedByDate" ><?=$respondedByDate;?></label></td>

					<?if ($acceptanceDesc==null || $acceptanceDesc=="")
					{?>
						<td></td>
					<?
					}
					else 
					{
						?><td><label  name="myAcceptance" id="myAcceptance" size="20" ><?=$acceptanceDesc?> on <?=$acceptanceDate?></label></td>
						<?
					}?>
					
			</tr>  
			<?
			$no1=$no1+1;
			}while($dbe->next_record());	
			?>					
		</table>
		</fieldset>
		<br />
		<table>
			<tr>
				<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../thesis/confirm_proposal.php';" /></td>				
			</tr>
		</table>
		<br/>
		
	</form>
	</body>
</html>
