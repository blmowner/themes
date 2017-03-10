<?php

include("../../../lib/common.php");
checkLogin();
session_start();
$userid=$_SESSION['user_id'];
?>

	
<? 
/*if(isset($_POST['btnSave']) && ($_POST['btnSave'] <> ""))
{
	
	$sqlUpdStud="UPDATE student  
				SET skype_id = '$skype_id'
				WHERE matrix_no = '$user_id'";
	$db_klas2->query($sqlUpdStud); 
	echo $sqlUpdStud;
}

$varBtnNm="Update";*/
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>

	<link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
   	<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />

	<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
	<script src="../../../lib/js/jquery.min2.js"></script>
    <script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
	<script language="JavaScript" src="../../../lib/js/windowopen.js"></script>
	<script type="text/javascript">
		$(function() {
			$("#tab1").tabs();
		});
	</script>
	
	

</head>
<body>

<? 
//untuk view student profile details
$sql_personal="SELECT s.student_status,s.matrix_no,s.name,s.ic_passport,s.address_aa,s.address_ab,s.city_a,s.state_a,s.postcode_a,s.country_a,s.handphone,
s.house,s.office,s.email,s.address_bb,s.address_ba,s.city_b,s.state_b,s.postcode_b,s.country_b,s.xgender,sp.intake_no,pa.feedback_by,pa.feedback_date,
pa.status,pa.thesis_title,po.code,po.program_e,sp.program_code,pgs.skype_id,pa.id,pt.id AS thesis_id, s.skype_id, pt.ref_thesis_status_id_proposal, 
pt.ref_thesis_status_id_defense, pt.ref_thesis_status_id_work, pt.ref_thesis_status_id_evaluation, pt.ref_thesis_status_id_final, pt.ref_thesis_status_id_senate,
rts1.description as proposal_desc, rts2.description as defense_desc, rts3.description as work_desc, rts4.description as evaluation_desc, rts5.description as final_desc, 
rts6.description as senate_desc 
		FROM student s
		LEFT JOIN student_program sp ON (sp.matrix_no=s.matrix_no) 
		LEFT JOIN pg_thesis pt ON (pt.student_matrix_no=sp.matrix_no) 
		LEFT JOIN pg_proposal pa ON (pa.id=pt.id) 
		LEFT JOIN student pgs ON (pgs.matrix_no=pt.student_matrix_no) 
		LEFT JOIN pro_off po ON (po.code=sp.program_code) 
		LEFT JOIN pg_supervisor ps ON (ps.id=pt.id)
		LEFT JOIN ref_thesis_status rts1 ON (rts1.id = pt.ref_thesis_status_id_proposal)
		LEFT JOIN ref_thesis_status rts2 ON (rts2.id = pt.ref_thesis_status_id_defense)
		LEFT JOIN ref_thesis_status rts3 ON (rts3.id = pt.ref_thesis_status_id_work)
		LEFT JOIN ref_thesis_status rts4 ON (rts4.id = pt.ref_thesis_status_id_evaluation)
		LEFT JOIN ref_thesis_status rts5 ON (rts5.id = pt.ref_thesis_status_id_final)
		LEFT JOIN ref_thesis_status rts6 ON (rts6.id = pt.ref_thesis_status_id_senate)
		WHERE sp.program_code LIKE 'M%' AND s.matrix_no = '$user_id'
		ORDER BY pa.id DESC";
$db_klas2->query($sql_personal);
$row_personal=$db_klas2->fetchArray();

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
$research=$row_personal["title"];
$cases=$row_personal["thesis_type"];
$introduction=$row_personal["introduction"];
$objective=$row_personal["objective"];
$description=$row_personal["description"];
$proposal_status=$row_personal["status"];
$thesis_title=$row_personal["thesis_title"];
$student_status=$row_personal["student_status"];
$status=$row_personal['status'];
$thesis_id=$row_personal['thesis_id'];
$supervisor_id=$row_personal['supervisor_id'];
$supervisor_name=$row_personal['name'];
$email=$row_personal['email'];
$hp_no=$row_personal['hp_no'];
$ref_thesis_status_id_proposal=$row_personal['ref_thesis_status_id_proposal'];
$ref_thesis_status_id_defense=$row_personal['ref_thesis_status_id_defense'];
$ref_thesis_status_id_work=$row_personal['ref_thesis_status_id_work'];
$ref_thesis_status_id_evaluation=$row_personal['ref_thesis_status_id_evaluation'];
$ref_thesis_status_id_final=$row_personal['ref_thesis_status_id_final'];
$ref_thesis_status_id_senate=$row_personal['ref_thesis_status_id_senate'];
$proposal_desc=$row_personal['proposal_desc'];
$defense_desc=$row_personal['defense_desc'];
$work_desc=$row_personal['work_desc'];
$evaluation_desc=$row_personal['evaluation_desc'];
$final_desc=$row_personal['final_desc'];
$senate_desc=$row_personal['senate_desc'];


//untuk view supervisor/co_supervisor
/*$sql_area = "SELECT ps.supervisor_id, ps.name, ps.email, ps.hp_no, ps.skype_id, ps.type 
				FROM pg_supervisor ps 
				LEFT JOIN new_employee ne1 ON (ne1.empid=ps.supervisor_id)
				WHERE matrix_no = '$user_id'";
$db_klas2->query($sql_area);
$row_area = $db_klas2->fetchArray();

$thesis_id=$row_area['thesis_id'];
$level=$row_area['level'];
$discipline=$row_area['discipline'];
$institution=$row_area['institution'];
$duration=$row_area['duration'];
$date=$row_area['date'];
$mode=$row_area['mode'];
$justification=$row_area['justification'];
$research=$row_area['research'];
$supervisor_name=$row_area['supervisor_name'];
$supervisor_graduate=$row_area['supervisor_graduate'];
$supervisor_student=$row_area['supervisor_student'];
$supervisor_area=$row_area['supervisor_area'];
$supervisor_service=$row_area['supervisor_service'];
$supervisor_post=$row_area['supervisor_post'];
$cases=$row_area['cases'];
$remarks=$row_area['remarks'];
$introduction=$row_area['introduction'];
$objective=$row_area['objective'];
$description=$row_area['introduction'];*/
?>
  <form id="form1" name="form1" method="post" enctype="multipart/form-data">
 	<div id="tab1" style="width:950px">
		<?php /*?><table>
			<tr>
				<td valign="middle" align="right" height="20:px" class="tbtitle"><input type="button" name="btnNew" id="btnNew" value="New Application" onClick="javascript:window.open('new_proposal.php','aform','dependent=no,width=950,height=300,resizable=yes,scrollbars=yes');"></td>
			</tr>
		</table><?php */?>
		<ul>
            <li><a href="#tabs-1">Student Profile</a></li>
			<li><a href="#tabs-2">Thesis</a></li>
            <li><a href="#tabs-3">Supervisor/Co-Supervisor</a></li>
            <li><a href="#tabs-4">Subject Taken</a></li>
			<li><a href="#tabs-5">Thesis History</a></li>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;	&nbsp;<td valign="middle" align="right" height="20:px" class="tbtitle"><input type="button" name="btnNew" id="btnNew" value="New Application" onClick="javascript:window.open('new_proposal.php','aform','dependent=no,width=950,height=500,resizable=yes,scrollbars=yes');"></td>
        </ul>
				
		<div id="tabs-1">
			<fieldset style="width:900px">
			  <legend><strong>STUDENT PROFILE</strong></legend>
			
			  <table border="0" width="100%">
			  
			  	<tr>
				<td width="8%">Matric No&nbsp;</td>
				<td width="1%">:</td>
				<td colspan="7" ><input type="hidden" name="matrix_no" size="20" id="matrix_no" value="<?=$user_id?>" disabled="disabled"/><?=$user_id?></td>
				<td width="20%" rowspan="20" align="center">&nbsp;<img src="image.php?userId=<?=$user_id?>" width="100" height="130" /><br> <input type="button" value="Upload Picture" id="submitpic" name="submitpic" alt="Recommended size:(126x170)" 
						onclick="javascript:open_win('image_insert.php?pid=<?=$user_id?>',480,280,0,0,0,1,0,1,1,0,5,'winupload'); " />	</td>
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
				
				  <td width="6%">Thesis ID</td>
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
				  <td><input type="hidden" name="student_status" size="15" id="student_status" value="<?=$student_status?>" disabled="disabled"/><?=$emailid?></td>
				  
				  <td>Skype ID</td>
				  <td>:</td>
				  <td colspan="4"><input type="hidden" name="skype_id" size="30" id="skype_id" value="<?=$skype_id?>" /><?=$skype_id?></td>
				</tr>
			</table>
			
			<br \>
			<br \>
			<table width="397" border="0" cellpadding="0">
				<tr>
				  <td width="16"></td>
				  <td colspan="2"><strong>Thesis Process Flow </strong></td>
				  <td width="97"><strong>Status</strong></td>
				  <td width="110"><strong>Approved Date</strong></td>
				</tr>
				<tr>
				  <td height="21">1. </td>
				  <td width="137" >Thesis Proposal </td>
				  <td width="13" >:</td>
				  <td ><input type="hidden" name="thesisProposal" size="10" id="thesisProposal" value="<?=$proposal_desc?>" disabled="disabled"/><?=$proposal_desc?></td>
				  <td ><input type="hidden" name="thesisProposal" size="10" id="thesisProposal" value="<?=$proposal_desc?>" disabled="disabled"/><?=$date1?></td>
				</tr>
				<tr>
				  <td>2. </td>
				  <td>Proposal Defense </td>
				  <td>:</td>
				  <td><input type="hidden" name="proposalDefense" size="10" id="proposalDefense" value="<?=$defense_desc?>" disabled="disabled"/><?=$defense_desc?></td>
				  <td ><input type="hidden" name="thesisProposal" size="10" id="thesisProposal" value="<?=$proposal_desc?>" disabled="disabled"/><?=$date2?></td>
				</tr>
				<tr>
				  <td>3. </td>
				  <td>Work Completion </td>
				  <td>:</td>
				  <td><input type="hidden" name="programme" size="10" id="programme" value="<?=$work_desc?>" disabled="disabled"/><?=$work_desc?></td>
				  <td ><input type="hidden" name="thesisProposal" size="10" id="thesisProposal" value="<?=$proposal_desc?>" disabled="disabled"/><?=$date3?></td>
				</tr>
				<tr>
				  <td>4. </td>
				  <td>Thesis Evaluation/VIVA </td>
				  <td>:</td>
				  <td><input type="hidden" name="intake" size="10" id="intake" value="<?=$evaluation_desc?>" disabled="disabled"/><?=$evaluation_desc?></td>
				  <td ><input type="hidden" name="thesisProposal" size="10" id="thesisProposal" value="<?=$proposal_desc?>" disabled="disabled"/><?=$date4?></td>
				</tr>
				<tr>
				  <td>5. </td>
				  <td>Final Submission </td>
				  <td>:</td>
				  <td><input type="hidden" name="thesisTitle" size="10" id="thesisTitle" value="<?=$final_desc?>" disabled="disabled"/><?=$final_desc?></td>
				  <td ><input type="hidden" name="thesisProposal" size="10" id="thesisProposal" value="<?=$proposal_desc?>" disabled="disabled"/><?=$date5?></td>
				</tr>
				
			</table>
			

			 <?php /*?><input type="submit" name="btnSave" id="btnSave" align="center"  value="<?php echo $varBtnNm; ?>" /><?php */?>

	      </fieldset>
		  
		</div>
		
		<div id="tabs-2">
				<?php include("submit_proposal.php"); ?>		
		</div>
		
		<div id="tabs-3">
			<fieldset>	
			<legend><strong>LIST OF SUPERVISOR/CO-SUPERVISOR</strong></legend>	
				<table border="1" cellpadding="0" cellspacing="0" width="100%" bordercolorlight="#cccccc" bordercolordark="#eeeeee">
				
				<tr><td>Note: No Supervisor/Co-Supervisor has been assigned.<br/>
							It could be:-<br/>
							1. Supervisor/Co-Supervisor is yet to be assigned<br/>
							2. Pending approval by the Senate.<br/>
							3. If already assigned, it could be the Supervisor/Co-Supervisor pending to accept<br/></td>
				</tr>
				</table>
				<br \>
				<br \>
				<table border="1" cellpadding="0" cellspacing="0" width="100%" bordercolorlight="#cccccc" bordercolordark="#eeeeee">			
				<tr>
					<th width="2%">No</th>					
					<th width="7%">Staff ID</th>
					<th width="20%">Name</th>
					<th width="9%">Faculty</th>
					<th width="9%">Hp. No</th>
					<th width="24%">Email ID</th>
					<th width="19%">Skype ID</th>
					<th width="10%">Role</th>
				</tr>
				 <?php				
				/*$sql="SELECT * FROM pg_supervisor 
				WHERE pg_student_matrix_no='$user_id' ORDER BY ref_supervisor_type_id  ";*/
				
				$sql="SELECT  a.pg_employee_empid, b.name, c.description, b.mobile, b.email, a.skype_id, 
				d.description as supervisor_type
				FROM pg_supervisor a 
				LEFT JOIN new_employee b ON (b.id=a.pg_employee_empid) 
				LEFT JOIN dept_unit c ON (c.id = b.unit_id)
				LEFT JOIN ref_supervisor_type d ON (d.id = a.ref_supervisor_type_id)
				WHERE pg_student_matrix_no='$user_id' 
				AND a.acceptance_status = 'ACC'
				ORDER BY ref_supervisor_type_id";
		
				$result = $db_klas2->query($sql); //echo $sql;
				$varRecCount=0;	
				while($row = mysql_fetch_array($result)) 
		
					{ 
						$varRecCount++;
						echo "<tr>
								<td align=\"center\">".$varRecCount."</td>					
								<td align=\"left\">".$row["pg_employee_empid"]."</td>
								<td align=\"left\">".$row["name"]."</td>
								<td align=\"left\">".$row["description"]."</td>
								<td align=\"left\">".$row["mobile"]."</td>
								<td align=\"left\">".$row["email"]."</td>                    
								<td align=\"left\">".$row["skype_id"]."</td>  
								<td align=\"left\">".$row["supervisor_type"]."</td>";	?>
								<?php  
								
					}
				/*$row_cnt = mysql_num_rows($result);
				if ($row_cnt>0) {*/
					?> 
				</table>
					
							
		 	</fieldset>						
		</div>
		
		<div id="tabs-4">
			<fieldset>
				<legend><strong>SUBJECT TAKEN</strong></legend>
				
				<table border="1" cellpadding="0" cellspacing="0" width="100%" bordercolorlight="#cccccc" bordercolordark="#eeeeee">
				<tr> 
				  <td height="20" colspan="11" class="tbtitle"><b>&nbsp;STUDENT INFORMATION</b></td>
				</tr>
				<?
				  $sql = "select s.matrix_no,s.name, sp.intake_no from student s 
				  			left join student_program sp ON (sp.matrix_no=s.matrix_no)
							where s.matrix_no = '$user_id'";
				  $dbstux=$db;
				  $dbstux->query($sql);
				  $nxstux=$dbstux->next_record(); //echo $sql;
				  $mtx=$dbstux->f("matrix_no");
				  if($nxstux){
				  ?>
					<tr> 
					  <td width="78" height="20" class="tbmenu">&nbsp;Matric No</td>
					  <td width="78" class="tbmain">&nbsp;<?=$dbstux->f("matrix_no")?>      </td>
					  <td width="82" class="tbmenu">&nbsp;Name</td>
					  <td width="522" class="tbmain">&nbsp;<?=$dbstux->f("name")?></td>
					<tr> 
					  <td width="78" height="20" class="tbmenu">&nbsp;Cohort</td>
					  <td colspan="3" class="tbmain">&nbsp;<?=$dbstux->f("intake_no")?></td>
					</tr>
					<?
					$nxstux=$dbstux->next_record();
					}
				  ?>
				</table>
				
				<table border="1" cellpadding="0" cellspacing="0" width="100%" bordercolorlight="#cccccc" bordercolordark="#eeeeee">
					<tr>
						<th colspan="11"><p align="center">List Of Subject Registered</p></th>
					   
					</tr>
					 <?php	
					  $sql="select ag.sem_no,ag.subject_code,ag.grade,ag.semester_id,s.sub_desc 
					  		FROM asess_grade ag 
							LEFT JOIN `subject` s ON (s.subject_code=ag.subject_code)
							WHERE ag.matrix_no='$user_id' 
							order by ag.semester_id desc";			
					
					$result = $db_klas2->query($sql); //echo $sql;
					$varRecCount=0;					
					while($row = mysql_fetch_array($result)) 
					{ 
						$varRecCount++;
						echo "<tr>
								<td align=\"left\" nowrap=\"nowrap\">".$row["sem_no"]."</td>
								<td align=\"left\" nowrap=\"nowrap\">".$row["semester_id"]."</td>
								<td align=\"left\" nowrap=\"nowrap\">".$row["subject_code"]."</td>
								<td align=\"left\" nowrap=\"nowrap\">".$row["sub_desc"]."</td>
								<td align=\"left\" nowrap=\"nowrap\">".$row["grade"]."</td>";	?>
								
								<?php  
								echo "</tr>";
					}
							?>						
				</table>
				
			</fieldset>
		</div>
		
		<div id="tabs-5">
			<fieldset>
				<legend><strong>THESIS HISTORY</strong></legend>
			</fieldset>
		</div>
	</div>
 </form>
</body>
</html>