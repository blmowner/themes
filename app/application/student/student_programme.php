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
	<!--<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>-->
	<script language="JavaScript" type="text/javascript" src="../../../lib/js/tooltip.js"></script>
	<script type="text/javascript">
		$(function() {
			$("#tab1").tabs();
		});
	</script>
<style>
	
ul {
    padding: 0;
    margin: 0;
}


li {
    display: inline;
    position: relative;
}

ul ul {
    position: absolute;
    display: none;
}

ul ul ul {
    left: 100%;
    top: 0;
}

li:hover > ul {
    display: block;
    padding-top:25px;
}


</style>
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
s.country_b,s.xgender,sp.intake_no,sp.program_code,po.code,po.program_e,s.skype_id, sp.manage_by_whom
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
$manageby=$row_personal["manage_by_whom"];

//Tracking Status for Thesis Proposal
$sql_thesis="SELECT pa.verified_by,
pa.verified_date, pa.verified_status, pa.status,pa.thesis_title,pa.id as proposal_id,pt.id AS thesis_id, 
ppa.endorsed_status as ref_thesis_status_id_proposal, 
pt.ref_thesis_status_id_defense, pt.ref_thesis_status_id_work, pt.ref_thesis_status_id_evaluation, 
pt.ref_thesis_status_id_final, pt.ref_thesis_status_id_senate, rps1.description AS proposal_desc, /*rps2.description AS defense_desc, 
rps3.description AS work_desc, rps4.description AS evaluation_desc, rps5.description AS final_desc, 
rps6.description AS senate_desc, */DATE_FORMAT(ppa.endorsed_date,'%d-%b-%Y') AS endorsed_date
FROM pg_thesis pt
LEFT JOIN pg_proposal pa ON (pa.pg_thesis_id = pt.id)
LEFT JOIN pg_proposal_approval ppa ON (ppa.id = pa.pg_proposal_approval_id)
LEFT JOIN ref_thesis_status rps1 ON (rps1.id = ppa.endorsed_status) 
/*LEFT JOIN ref_thesis_status rps2 ON (rps2.id = pt.ref_thesis_status_id_defense) 
LEFT JOIN ref_thesis_status rps3 ON (rps3.id = pt.ref_thesis_status_id_work) 
LEFT JOIN ref_thesis_status rps4 ON (rps4.id = pt.ref_thesis_status_id_evaluation) 
LEFT JOIN ref_thesis_status rps5 ON (rps5.id = pt.ref_thesis_status_id_final) 
LEFT JOIN ref_thesis_status rps6 ON (rps6.id = pt.ref_thesis_status_id_senate) */
WHERE pt.student_matrix_no = '$user_id'
AND pt.archived_status is null
AND pa.archived_status is null
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
$proposal_id=$row_thesis['proposal_id'];
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
/*$defense_desc=$row_thesis['defense_desc'];
$work_desc=$row_thesis['work_desc'];
$evaluation_desc=$row_thesis['evaluation_desc'];
$final_desc=$row_thesis['final_desc'];
$senate_desc=$row_thesis['senate_desc'];*/

//Tracking Status for Defence Proposal
$sql_defence_proposal = "SELECT a.id as defense_id, b.id as evaluation_id, a.status as defense_status, 
b.status as def_evaluation_status, h.description as def_evaluation_status_desc,
b.respond_status, b.confirmed_status,
b.ref_defense_marks_id, g.description as ref_defense_marks_desc,
b.proposed_marks_id, i.description as def_proposed_marks_desc,
DATE_FORMAT(b.confirmed_date,'%d-%b-%Y %h:%i%p') AS def_confirmed_date
FROM pg_defense a
LEFT JOIN pg_evaluation b ON (b.pg_defense_id = a.id)
LEFT JOIN ref_proposal_status h ON (h.id = b.status)
LEFT JOIN ref_defense_marks g ON (g.id = b.ref_defense_marks_id)
LEFT JOIN ref_defense_marks i ON (i.id = b.proposed_marks_id)
WHERE a.student_matrix_no = '$user_id'
AND a.pg_thesis_id = '$thesis_id'
AND a.pg_proposal_id = '$proposal_id'
AND a.status = 'REC'
AND a.submit_status = 'INP'
AND a.respond_status = 'Y'
AND b.respond_status = 'Y'
AND b.confirmed_status = 'Y'
AND a.archived_status IS NULL
AND b.archived_status IS NULL
ORDER BY a.submit_date DESC";

$result_sql11 = $dbg->query($sql_defence_proposal); 
$dbg->next_record();
$row_sql11 = mysql_num_rows($result_sql11);

$defense_desc = $dbg->f('def_evaluation_status_desc');
$def_evaluation_status = $dbg->f('def_evaluation_status');
$ref_defense_marks_desc = $dbg->f('ref_defense_marks_desc');
$def_proposed_marks_desc = $dbg->f('def_proposed_marks_desc');
$defense_date = $dbg->f('def_confirmed_date');

//Tracking Status for Work Completion
$sql_work_completion = "SELECT a.id as defense_id, b.id as evaluation_id, a.status as work_status, 
b.status as wc_evaluation_status, h.description as wc_evaluation_status_desc,
b.respond_status, b.confirmed_status,
b.ref_work_marks_id, g.description as ref_work_marks_desc,
b.proposed_marks_id, i.description as wc_proposed_marks_desc,
DATE_FORMAT(b.confirmed_date,'%d-%b-%Y %h:%i%p') AS wc_confirmed_date
FROM pg_work a
LEFT JOIN pg_work_evaluation b ON (b.pg_work_id = a.id)
LEFT JOIN ref_proposal_status h ON (h.id = b.status)
LEFT JOIN ref_work_marks g ON (g.id = b.ref_work_marks_id)
LEFT JOIN ref_work_marks i ON (i.id = b.proposed_marks_id)
WHERE a.student_matrix_no = '$user_id'
AND a.pg_thesis_id = '$thesis_id'
AND a.pg_proposal_id = '$proposal_id'
AND a.status = 'REC'
AND a.submit_status = 'INP'
AND a.respond_status = 'Y'
AND b.respond_status = 'Y'
AND b.confirmed_status = 'Y'
AND a.archived_status IS NULL
AND b.archived_status IS NULL
ORDER BY a.submit_date DESC";

$result_sql12 = $dbg->query($sql_work_completion); 
$dbg->next_record();
$row_sql12 = mysql_num_rows($result_sql12);

$work_desc = $dbg->f('wc_evaluation_status_desc');
$ref_work_marks_desc = $dbg->f('ref_work_marks_desc');
$wc_proposed_marks_desc = $dbg->f('wc_proposed_marks_desc');
$wc_evaluation_status = $dbg->f('wc_evaluation_status');
$work_date = $dbg->f('wc_confirmed_date');

//Tracking Status for VIVA Evaluation
$sql_viva = "SELECT a.id, a.student_matrix_no, a.pg_thesis_id, a.pg_proposal_id, a.reference_no, 
a.submit_status, d.description as submit_status_desc,
a.pg_work_id, a.pg_calendar_id, 
c.final_result, e.description AS final_result_desc,
DATE_FORMAT(c.final_result_date,'%d-%b-%Y %h:%i%p') AS final_result_date
FROM pg_senate a
LEFT JOIN pg_viva b ON (b.id = a.pg_viva_id)
LEFT JOIN pg_evaluation_viva c ON (c.pg_viva_id = b.id)
LEFT JOIN ref_proposal_status d ON (d.id = a.submit_status)
LEFT JOIN ref_recommendation e ON (e.id = c.final_result)
WHERE a.status = 'A'
AND a.student_matrix_no = '$user_id'
AND a.pg_thesis_id = '$thesis_id'
AND a.pg_proposal_id = '$proposal_id'
/*AND a.respond_status = 'N'
AND c.respond_status = 'SUB'*/
AND c.status = 'A'
AND a.archived_status IS NULL";

$result_sql13 = $dbg->query($sql_viva); 
$dbg->next_record();
$row_sql13 = mysql_num_rows($result_sql13);

$viva_desc = $dbg->f('final_result_desc');
$viva_date = $dbg->f('final_result_date');



//Senate Endorsement Status 
$sql_viva = "SELECT a.id, a.student_matrix_no, a.pg_thesis_id, a.pg_proposal_id, a.reference_no, 
a.submit_status, d.description as submit_status_desc,
a.pg_work_id, a.pg_calendar_id, 
c.final_result, 
DATE_FORMAT(a.submit_date,'%d-%b-%Y %h:%i%p') AS submit_date
FROM pg_senate a
LEFT JOIN pg_viva b ON (b.id = a.pg_viva_id)
LEFT JOIN pg_evaluation_viva c ON (c.pg_viva_id = b.id)
LEFT JOIN ref_proposal_status d ON (d.id = a.submit_status)
WHERE a.status = 'A'
AND a.student_matrix_no = '$user_id'
AND a.pg_thesis_id = '$thesis_id'
AND a.pg_proposal_id = '$proposal_id'
/*AND a.respond_status = 'N'
AND c.respond_status = 'SUB'*/
AND c.status = 'A'
AND a.archived_status IS NULL";

$result_sql13 = $dbg->query($sql_viva); 
$dbg->next_record();
$row_sql13 = mysql_num_rows($result_sql13);

$senate_desc = $dbg->f('submit_status_desc');
$senate_date = $dbg->f('submit_date');
	
?>
  <form id="form1" name="form1" method="post" enctype="multipart/form-data">
 	<div id="tab1" style="width:1050px">
		
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
            <li><a href="#tabs-5">List of Publication</a></li>
            <li><a href="#tabs-6">Manage Publication</a></li>
	        <li><a href="#tabs-7">Thesis History</a></li>
			
        </ul>
				
		<div id="tabs-1">
			<fieldset style="width:900px">
			  <legend><strong>STUDENT PROFILE</strong></legend>
			  <table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="100%" class="thetable">		  
			  	<tr>
					<th align="left"><label>Matric No</label></th>
					<td colspan="7" ><label><?=$user_id?></label></td>	
					<td rowspan="6" align="center"> <img src="getImage.php?userId=<?=$user_id?>" width="100" height="130" /><br> <?php?></td>						
				</tr>
			  	
				<tr>
				  <th align="left">Student Name</th>
				  <td colspan="7" ><label><?=$name?></label></td>
				</tr>
							
				<tr>
				  <th align="left">Programme</th>
				  <td colspan="7"><label><?=$program_code?> - <?=$program_e?></label></td>
				</tr>
				<tr>
					<th align="left"><label>Manage By</label></th>
					<td colspan="7"><label><?=$manageby?></label></td>
				</tr>

				<tr>
				  <th align="left" width="150px"><label>Intake</label></th>
				  <td width="150px"><label><?=$intake_no?></label></td>
				
				  <th align="left" width="150px">Thesis /Project ID</th>
				  <td width="150px"><label><?=$thesis_id?></label></td>
				  <th align="left" width="150px">Student Status </th>
				  <td width="150px"><label><?=$student_status?></label></td>
				</tr>
				<tr>
				  <th align="left"><label>Email ID</label></th>
				  <td colspan="2"><label><?=$email?></label></td>
				  
				  <th align="left" width="150px"><label>Skype ID</label></th>
				  <td colspan="2"><label><?=$skype_id?></label></td>
				</tr>
				<tr>
				  <th align="left" width="15%">Thesis / Project Title </th>
				  <td colspan="8"><label><?=$thesis_title?></label></td>
				</tr>				
			</table>
			<br>
			<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="80%" class="thetable">	
				<tr>
					<th width="5%"><label>No.</label></th>
					<th width="20%" align="left"><label>Thesis Progress Flow </label></th>
					<th width="35%" align="left"><label>Status</label></th>
					<th width="20%" align="left"><label>Approval Date</label></th>
				</tr>
				<tr>
					<td width="5%" align="center">1.</td>
					<td width="25%">Thesis Proposal </td>
					<td width="15%"><label name="proposal_desc" size="10" id="proposal_desc"></label><?=$proposal_desc?></td>
					<td width="15%"><label name="endorsed_date" size="10" id="endorsed_date"></label><?=$endorsed_date?></td>
				</tr>
				<tr>
					<td width="5%" align="center">2.</td>
					<td width="20%">Proposal Defence </td>
					<?if ($def_evaluation_status == "APP") {?>
						<td width="15%"><label><?=$ref_defense_marks_desc?> [<?=$defense_desc?>]</td>
					<?}
					else if ($def_evaluation_status == "DIS") {
						?>
						<td width="15%"><label><?=$ref_defense_marks_desc?> [<?=$defense_desc?> - <?=$def_proposed_marks_desc?>]</td>
					<?}?>
					<td width="15%"><label name="defense_date" size="10" id="defense_date" value="<?=$defense_date?>"></label><?=$defense_date?></td>
				</tr>
				<tr>
					<td width="5%" align="center">3.</td>
					<td width="20%">Work Completion </td>
					<?if ($wc_evaluation_status == "APP") {?>
						<td width="15%"><label><?=$ref_work_marks_desc?> [<?=$work_desc?>]</td>
					<?}
					else if ($wc_evaluation_status == "DIS"){
						?>
						<td width="15%"><label><?=$ref_work_marks_desc?> [<?=$work_desc?> - <?=$wc_proposed_marks_desc?>]</td>
					<?}
					else {
						?>
						<td></td>
						<?
					}?>
					<td width="15%"><label name="work_date" size="10" id="work_date" value="<?=$work_date?>"></label><?=$work_date?></td>
				</tr>
				<tr>
					<td width="5%" align="center">4.</td>
					<td width="20%">Thesis Evaluation/VIVA </td>
					<td width="15%"><label name="viva_desc" size="10" id="viva_desc" value="<?=$viva_desc?>"></label><?=$viva_desc?></td>
					<td width="15%"><label name="viva_date" size="10" id="viva_date" value="<?=$viva_date?>"></label><?=$viva_date?></td>
				</tr>
				<tr>
					<td width="5%" align="center">5.</td>
					<td width="20%">Senate Endorsement </td>
					<td width="15%"><label name="senate_desc" size="10" id="senate_desc" value="<?=$senate_desc?>"></label><?=$senate_desc?></td>
					<td width="15%"><label name="senate_date" size="10" id="senate_date" value="<?=$senate_date?>"></label><?=$senate_date?></td>
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
					<th width="7%">Picture</th>
					<th width="20%">Name (Staff ID)</th>
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
				AND a.status='A'
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
								<td rowspan="2" align="center"><?=$varRecCount;?></td>					
								<td rowspan="2" align="center"><img src="getImageStaff.php?staffId=<?=$row["pg_employee_empid"];?>" width="40" height="40" /></td>
								<td rowspan= "2"><?=$name;?><br>(<?=$row["pg_employee_empid"];?>)</td>
								
								<td rowspan="2" align="left"><a href="javascript:void(0);" onMouseOver="toolTip('<?=$description;?>', 300)" onMouseOut="toolTip()"><?=$id;?></a>
								</td>
								<td rowspan="2" align="left"><?=$mobile;?></td>
								<td rowspan="2" align="left"><?=$email;?></td>                    
								<td rowspan="2" align="left"><?=$skype_id;?></td>  
								<td rowspan="2" align="left"><?=$row["supervisor_type"];?></td>
						</tr>
						<tr>
						  
				  		</tr>
						<?php  
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
			<iframe id="" src="../publication/list_of_publication_general.php" frameborder="0" style="height:500px;width:100%;"></iframe>
				<?php // include("../publication/list_of_publication_general.php"); ?>
		</div>

	    <div id="tabs-6">
			<iframe id="" src="../publication/manage_publication.php" frameborder="0" style="height:500px;width:100%;"></iframe>
				<?php // include("../publication/manage_publication.php"); ?>
		</div>

		<div id="tabs-7">
			<iframe id="iframeAma" src="../thesis/proposal_history.php" frameborder="0" style="height:500px;width:100%;"></iframe>
				<?php // include("../thesis/proposal_history.php"); ?>
		</div>
	</div>
 </form>
</body>
</html>