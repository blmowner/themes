<?php

include("../../../lib/common.php");
checkLogin();
session_start();
$user_id=$_SESSION['user_id'];
?>

	
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
	<link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
	<link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
   	<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />

	<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
	<script src="../../../lib/js/jquery.min2.js"></script>
    <script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
	<script language="JavaScript" src="../../../lib/js/windowopen.js"></script>
	<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>
	<script language="JavaScript" type="text/javascript" src="../../../lib/js/tooltip.js"></script>
	<script type="text/javascript">
		$(function() {
			$("#tab1").tabs();
		});
	</script>
</head>
<body>

<? 
if (substr($user_id,0,2) != '07') { 
	$dbConn=$dbc; 
} 
else { 
	$dbConn=$dbc1; 
}
$sql_personal="SELECT s.student_status,s.matrix_no,s.name,s.ic_passport,s.address_aa,s.address_ab,s.city_a,s.state_a,
		s.postcode_a,s.country_a,s.handphone,s.house,s.office,s.email,s.address_bb,s.address_ba,s.city_b,s.state_b,s.postcode_b,
		s.country_b,s.xgender,sp.intake_no,sp.program_code,po.code,po.program_e,s.skype_id
		FROM student s
		LEFT JOIN student_program sp ON (sp.matrix_no=s.matrix_no) 
		LEFT JOIN pro_off po ON (po.code=sp.program_code) 
		WHERE (sp.program_code LIKE 'M%' OR sp.program_code LIKE 'P%' AND sp.program_code NOT LIKE 'MBBS%')
		AND s.matrix_no = '$user_id'";
	
$dbConn->query($sql_personal);
$row_personal = $dbConn->fetchArray(); //echo $sql_personal;

$matrix_no=$row_personal['matrix_no'];
$skype_id=$row_personal['skype_id'];
$name=$row_personal['name'];
$program_code=$row_personal['program_code'];
$program_e=$row_personal['program_e'];
$ic_passport=$row_personal['ic_passport'];
$address_aa=$row_personal['address_aa'];
$address_ab=$row_personal['address_ab'];
$city_a=$row_personal['city_a'];
$state_a=$row_personal['state_a'];
$postcode_a=$row_personal['postcode_a'];
$country_a=$row_personal['country_a'];
$address_bb=$row_personal['address_bb'];
$address_ba=$row_personal['address_ba'];
$city_b=$row_personal['city_b'];
$state_b=$row_personal['state_b'];
$postcode_b=$row_personal['postcode_b'];
$country_b=$row_personal['country_b'];
$citizenship=$row_personal['citizenship'];
$gender=$row_personal['xgender'];
$intake_no=$row_personal['intake_no'];
$mobile=$row_personal['handphone'];
$house=$row_personal['house'];
$office=$row_personal['office'];
$email=$row_personal['email'];
$student_status=$row_personal["student_status"];


$sql_thesis="SELECT pa.verified_by,
pa.verified_date, pa.verified_status, pa.status,pa.thesis_title,pa.id,pt.id AS thesis_id, 
pt.ref_thesis_status_id_proposal, pt.ref_thesis_status_id_defense, pt.ref_thesis_status_id_work, pt.ref_thesis_status_id_evaluation, 
pt.ref_thesis_status_id_final, pt.ref_thesis_status_id_senate, rps1.description AS proposal_desc, rps2.description AS defense_desc, 
rps3.description AS work_desc, rps4.description AS evaluation_desc, rps5.description AS final_desc, 
rps6.description AS senate_desc, DATE_FORMAT(ppa.endorsed_date,'%d-%b-%Y') AS endorsed_date
FROM pg_thesis pt
LEFT JOIN pg_proposal pa ON (pa.pg_thesis_id = pt.id)
LEFT JOIN pg_proposal_approval ppa ON (ppa.id = pa.pg_proposal_approval_id)
LEFT JOIN ref_thesis_status rps1 ON (rps1.id = pt.ref_thesis_status_id_proposal) 
LEFT JOIN ref_thesis_status rps2 ON (rps2.id = pt.ref_thesis_status_id_defense) 
LEFT JOIN ref_thesis_status rps3 ON (rps3.id = pt.ref_thesis_status_id_work) 
LEFT JOIN ref_thesis_status rps4 ON (rps4.id = pt.ref_thesis_status_id_evaluation) 
LEFT JOIN ref_thesis_status rps5 ON (rps5.id = pt.ref_thesis_status_id_final) 
LEFT JOIN ref_thesis_status rps6 ON (rps6.id = pt.ref_thesis_status_id_senate) 
WHERE pt.student_matrix_no = '$user_id'
AND pt.archived_status is null
ORDER BY pa.id DESC"; 
$db->query($sql_thesis);
$row_thesis=$db->fetchArray(); //echo $sql_thesis;


$cases=$row_thesis["thesis_type"];
$introduction=$row_thesis["introduction"];
$objective=$row_thesis["objective"];
$description=$row_thesis["description"];
$proposal_status=$row_thesis["status"];
$thesis_title=$row_thesis["thesis_title"];
$verifiedStatus=$row_thesis['verified_status'];
$status=$row_thesis['status'];
$thesis_id=$row_thesis['thesis_id'];
$supervisor_id=$row_thesis['supervisor_id'];
$supervisor_name=$row_thesis['name'];
$hp_no=$row_thesis['hp_no'];
$ref_thesis_status_id_proposal=$row_thesis['ref_thesis_status_id_proposal'];
$endorsed_date=$row_thesis['endorsed_date'];
$ref_thesis_status_id_defense=$row_thesis['ref_thesis_status_id_defense'];
$ref_thesis_status_id_work=$row_thesis['ref_thesis_status_id_work'];
$ref_thesis_status_id_evaluation=$row_thesis['ref_thesis_status_id_evaluation'];
$ref_thesis_status_id_final=$row_thesis['ref_thesis_status_id_final'];
$ref_thesis_status_id_senate=$row_thesis['ref_thesis_status_id_senate'];
$proposal_desc=$row_thesis['proposal_desc'];
$defense_desc=$row_thesis['defense_desc'];
$work_desc=$row_thesis['work_desc'];
$evaluation_desc=$row_thesis['evaluation_desc'];
$final_desc=$row_thesis['final_desc'];
$senate_desc=$row_thesis['senate_desc'];

?>
  <form id="form1" name="form1" method="post" enctype="multipart/form-data">
 	<div id="tab1" style="width:950px">
		
		<ul>
            <li><a href="#tabs-1">Student Profile</a></li>
			<?if ($verifiedStatus == 'SAV') {?>
				<li><a href="#tabs-2"><span style="color:#FF0000">Thesis</span></a></li>
			<?}
			else {
				?>
				<li><a href="#tabs-2">Thesis</a></li>
				<?
			}?>
            
			<li><a href="#tabs-3">Supervisor/Co-Supervisor</a></li>
            <li><a href="#tabs-4">Subject Taken</a></li>
			<li><a href="#tabs-5">Thesis History</a></li>
			
        </ul>
				
		<div id="tabs-1">
			<fieldset style="width:900px">
			  <legend><strong>STUDENT PROFILE</strong></legend>
			  <table border="0" width="100%">			  
			  	<tr>
					<td width="8%">Matric No&nbsp;</td>
					<td width="1%">:</td>
					<td colspan="7" ><input type="hidden" name="matrix_no" size="20" id="matrix_no" value="<?=$user_id?>" disabled="disabled"/><?=$user_id?></td>					
					<td width="20%" rowspan="20" align="center">&nbsp;<img src="getImage.php?userId=<?=$user_id?>" width="100" height="130" /><br> <?php?></td>					
				</tr>
			  	
				<tr>
				  <td>Student Name</td>
				  <td >:</td>
				  <td colspan="7" ><input type="hidden" name="name" size="30" id="name" value="<?=$name?>" disabled="disabled"/><?=$name?></td>
				</tr>
							
				<tr>
				  <td>Programme</td>
				  <td>:</td>
				  <td colspan="7"><input type="hidden" name="program_code" size="15" id="program_code" value="<?=$program_code?>" disabled="disabled"/><?=$program_code?>&nbsp;-&nbsp;<input type="hidden" name="program_e" size="30" id="program_e" value="<?=$program_e?>" disabled="disabled"/><?=$program_e?></td>
				</tr>
				<tr>
				  <td>Intake</td>
				  <td>:</td>
				  <td width="24%" ><input type="hidden" name="intake_no" size="15" id="intake_no" value="<?=$intake_no?>" disabled="disabled"/><?=$intake_no?></td>
				
				  <td width="6%">Thesis/Project ID</td>
				  <td width="1%">:</td>
				  <td width="14%"><input type="hidden" name="thesis_id" size="20" id="thesis_id" value="<?=$thesis_id?>" disabled="disabled"/><?=$thesis_id?></td>
				  <td width="8%">Student Status </td>
				  <td width="1%">:</td>
				  <td width="17%"><input type="hidden" name="student_status" size="15" id="student_status" value="<?=$student_status?>" disabled="disabled"/><?=$student_status?></td>
				</tr>
				<tr>
				  <td>Thesis / Project Title </td>
				  <td>:</td>
				  <td colspan="7"><input type="hidden" name="thesis_title" size="30" id="thesis_title" value="<?=$thesis_title?>" disabled="disabled"/><?=$thesis_title?></td>
				</tr>
				<tr>
				  <td>Email ID </td>
				  <td>:</td>
				  <td><input type="hidden" name="student_status" size="15" id="student_status" value="<?=$student_status?>" disabled="disabled"/><?=$email?></td>
				  
				  <td>Skype ID</td>
				  <td>:</td>
				  <td colspan="4"><input type="hidden" name="skype_id" size="30" id="skype_id" value="<?=$skype_id?>" /><?=$skype_id?></td>
				</tr>
			</table>
			
			<br/>
			<br/>
			<table width="397" border="0" cellpadding="0">
				<tr>
				  <td colspan="2"><strong>Thesis Progress Flow </strong></td>
				  <td width="97"><strong>Status</strong></td>
				  <td width="110"><strong>Approval Date</strong></td>
				</tr>
				<tr>
				  <td width="137" >1. Thesis Proposal </td>
				  <td width="13" >:</td>
				  <td><label name="proposal_desc" size="10" id="proposal_desc"></label><?=$proposal_desc?></td>
				  <td><label name="endorsed_date" size="10" id="endorsed_date"></label><?=$endorsed_date?></td>
				</tr>
				<tr>
				  <td>2. Proposal Defense </td>
				  <td>:</td>
				  <td><label name="defense_desc" size="10" id="defense_desc" value="<?=$defense_desc?>"></label><?=$defense_desc?></td>
				  <td><label name="defense_date" size="10" id="defense_date" value="<?=$defense_date?>"></label><?=$defense_date?></td>
				</tr>
				<tr>
				  <td>3. Work Completion </td>
				  <td>:</td>
				  <td><label name="work_desc" size="10" id="work_desc" value="<?=$work_desc?>"></label><?=$work_desc?></td>
				  <td><label name="work_date" size="10" id="work_date" value="<?=$work_date?>"></label><?=$work_date?></td>
				</tr>
				<tr>
				  <td>4. Thesis Evaluation/VIVA </td>
				  <td>:</td>
				  <td><label name="evaluation_desc" size="10" id="evaluation_desc" value="<?=$evaluation_desc?>"></label><?=$evaluation_desc?></td>
				  <td><label name="evaluation_date" size="10" id="evaluation_date" value="<?=$evaluation_date?>"></label><?=$evaluation_date?></td>
				</tr>
				<tr>
				  <td>5. Final Submission </td>
				  <td>:</td>
				  <td><label name="final_desc" size="10" id="final_desc" value="<?=$final_desc?>"></label><?=$final_desc?></td>
				  <td><label name="final_date" size="10" id="final_date" value="<?=$final_date?>"></label><?=$final_date?></td>
				</tr>
				
			</table>
			
	      </fieldset>
		  
		</div>
		
		<div id="tabs-2">				
				<?php include("../thesis/submit_proposal.php"); ?>	
		</div>
		
		<div id="tabs-3">
			<fieldset>	
			<legend><strong>LIST OF SUPERVISOR/CO-SUPERVISOR</strong></legend>	
				<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="100%" class="thetable">			
				<tr>
					<th width="5%">No</th>					
					<th width="7%">Staff ID</th>
					<th width="20%">Name</th>
					<th width="9%">Faculty</th>
					<th width="9%">Contact No</th>
					<th width="24%">Email ID</th>
					<th width="19%">Skype ID</th>
					<th width="10%">Role</th>
				</tr>
				 <?php				
							
				$sql="SELECT  a.pg_employee_empid, d.description as supervisor_type
				FROM pg_supervisor a 
				LEFT JOIN ref_supervisor_type d ON (d.id = a.ref_supervisor_type_id) 
				LEFT JOIN pg_thesis f ON (f.student_matrix_no = a.pg_student_matrix_no)
				LEFT JOIN pg_proposal g ON (g.pg_thesis_id = f.id)
				WHERE a.pg_student_matrix_no='$user_id' 
				AND a.ref_supervisor_type_id in ('SV','CS')
				AND a.acceptance_status is not null
				AND g.verified_status in ('APP','AWC')
				AND g.status in ('APP','APC')
				AND g.archived_status IS NULL
				ORDER BY d.seq, a.ref_supervisor_type_id";

				
				$result = $db_klas2->query($sql); //echo $sql;
				$varRecCount=0;	
				if (mysql_num_rows($result)>0)  {
					while($row = mysql_fetch_array($result)) 		
					{ 
						$sqlSupervisor="SELECT  b.name, c.id, c.description,b.mobile, b.email, b.skype_id
							FROM new_employee b 
							LEFT JOIN dept_unit c ON (c.id = b.unit_id) 
							WHERE b.empid= '".$row["pg_employee_empid"]."' ";
						if (substr($row["pg_employee_empid"],0,3) != 'S07') 
						{ 
							$dbConnStaff=$dbc; 				
						} 						
						else 
						{ 							
							$dbConnStaff=$dbc1; 						
						}
						$result1 = $dbConnStaff->query($sqlSupervisor);
						$row_supervise=$dbConnStaff->fetchArray();
						
						$name=$row_supervise["name"];
						$id=$row_supervise["id"];
						$description=$row_supervise["description"];
						$mobile=$row_supervise["mobile"];
						$email=$row_supervise["email"];
						$skype_id=$row_supervise["skype_id"];
						
						$varRecCount++;
						?>
						<tr>
								<td align="center"><?=$varRecCount;?></td>					
								<td align="left"><?=$row["pg_employee_empid"];?></td>
								<td align="left"><?=$name;?></td>
								<td align="left"><a href="javascript:void(0);" onMouseOver="toolTip('<?=$description;?>', 300)" onMouseOut="toolTip()"><?=$id;?></a></td>
								<td align="left"><?=$mobile;?></td>
								<td align="left"><?=$email;?></td>                    
								<td align="left"><?=$skype_id;?></td>  
								<td align="left"><?=$row["supervisor_type"];?></td>
						</tr><?php  
					}
				}
				else {
					?>
					<table>
						<tr>
							<td><label>No record found!</label></td>
						</tr>
					</table>
					<br/>					
					<table>				
						<tr><td>Notes: It could be:-<br/>									
									1. Supervisor/Co-Supervisor is yet to be assigned OR<br/>
									2. Supervisor/Co-Supervisor is pending for Senate approval OR<br/>
									3. Supervisor/Co-Supervisor is pending to accept the invitation.<br/></td>
						</tr>
					</table>
					<?
				}?> 
				</table>
		 	</fieldset>						
		</div>
		
		<div id="tabs-4">
			<fieldset>
				<legend><strong>SUBJECT TAKEN</strong></legend>
				
				<table>
				
				<?
				  $sql = "select s.matrix_no,s.name, sp.intake_no from student s 
				  			left join student_program sp ON (sp.matrix_no=s.matrix_no)
							where s.matrix_no = '$user_id'";
				  $dbstux=$dbConn;
				  $dbstux->query($sql);
				  $nxstux=$dbstux->next_record(); //echo $sql;
				  $mtx=$dbstux->f("matrix_no");
				  if($nxstux){
				  ?>
					<tr>
						<td width="82">Name</td>
						<td>:</td>
						<td width="522"><?=$dbstux->f("name")?></td>
					</tr>
					<tr> 
						<td width="78" >Matric No</td>
						<td>:</td>
						<td width="78"><?=$dbstux->f("matrix_no")?></td>
					</tr>					
					<tr>
					  <td width="78">Cohort</td>
					  <td>:</td>
					  <td colspan="3"><?=$dbstux->f("intake_no")?></td>
					</tr>
					<?
					$nxstux=$dbstux->next_record();
					}
				  ?>
				</table>
				<br/>
				<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="100%" class="thetable">
					<tr>
						<th colspan="11"><p align="center">List Of Subject Registered</p></th>					   
					</tr>
					<tr>
						<th width="6%">Sem No</th>					
						<th width="14%">Semester</th>
						<th width="16%">Subject Code</th>
						<th width="52%">Description</th>
						<th width="12%">Grade</th>
					</tr>
					 <?php	
					  $sql="select ag.sem_no,ag.subject_code,ag.grade,ag.semester_id,s.sub_desc,s.subject_eng 
						FROM asess_grade ag 
						LEFT JOIN `subject` s ON (s.subject_code=ag.subject_code)
						WHERE ag.matrix_no='$user_id' 
						order by ag.semester_id desc";			
					
					$result = $dbConn->query($sql); //echo $sql;
					$varRecCount=0;	
					if (mysql_num_rows($result)>0)  {
						while($row = mysql_fetch_array($result)) 
						{ 
							$varRecCount++;
							echo "<tr>
									<td align=\"center\" nowrap=\"nowrap\">".$row["sem_no"]."</td>
									<td align=\"left\" nowrap=\"nowrap\">".$row["semester_id"]."</td>
									<td align=\"left\" nowrap=\"nowrap\">".$row["subject_code"]."</td>
									<td align=\"left\" nowrap=\"nowrap\">".$row["subject_eng"]."</td>
									<td align=\"center\" nowrap=\"nowrap\">".$row["grade"]."</td>";	?>
									
									<?php  
									echo "</tr>";
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
				
			</fieldset>
		</div>
		
		<div id="tabs-5">
			<iframe id="iframeAma" src="../thesis/proposal_history.php" frameborder="0" style="height:500px;width:100%"></iframe>
				<?php // include("../thesis/proposal_history.php"); ?>
		</div>
	</div>
 </form>
</body>
</html>