<?php

include("../../../lib/common.php");
checkLogin();


?>

	
<? 
if(isset($_POST['btnSave']) && ($_POST['btnSave'] <> ""))
{
	
	$sqlUpdStud="UPDATE student  
				SET skype_id = '$skype_id'
				WHERE matrix_no = '$user_id'";
	$db_klas2->query($sqlUpdStud); 
	//echo $sqlUpdStud;
}

$varBtnNm="Update";
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
$sql_personal="SELECT s.student_status,s.matrix_no,s.name,s.ic_passport,s.address_aa,s.address_ab,s.city_a,s.state_a,s.postcode_a,s.country_a,s.handphone,s.house,s.office,s.email,s.address_bb,s.address_ba,s.city_b,s.state_b,s.postcode_b,s.country_b,s.xgender,sp.intake_no,pa.feedback_by,pa.feedback_date,pa.status,pa.thesis_title,po.code,po.program_e,sp.program_code,pgs.skype_id,pa.id,pt.id AS thesis_id, pgs.skype_id
		FROM student s
		LEFT JOIN student_program sp ON (sp.matrix_no=s.matrix_no) 
		LEFT JOIN pg_thesis pt ON (pt.student_matrix_no=sp.matrix_no) 
		LEFT JOIN pg_proposal pa ON (pa.id=pt.id) 
		LEFT JOIN student pgs ON (pgs.matrix_no=pt.student_matrix_no) 
		LEFT JOIN pro_off po ON (po.code=sp.program_code) 
		LEFT JOIN pg_supervisor ps ON (ps.id=pt.id)
		WHERE sp.program_code LIKE 'M%' AND s.matrix_no = '$user_id'
		ORDER BY pa.id DESC LIMIT 1";
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
$status=$row_personal["status"];
$thesis_title=$row_personal["thesis_title"];
$student_status=$row_personal["student_status"];
$status=$row_personal['status'];
$thesis_id=$row_personal['thesis_id'];
$supervisor_id=$row_personal['supervisor_id'];
$supervisor_name=$row_personal['name'];
$email=$row_personal['email'];
$hp_no=$row_personal['hp_no'];


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
 	<div id="tab1">
		<ul>
            <li><a href="#tabs-1">Student Profile</a></li>
            <li><a href="#tabs-2">Supervisor/Co-Supervisor</a></li>
            <li><a href="#tabs-3">Subject Taken</a></li>
        </ul>
		
		<div id="tabs-1">
			<fieldset>
			  <legend><strong>STUDENT PROFILE</strong></legend>
			
			  <table border="0" width="100%">
			  
			  	<tr>
				<td class="tbmenu" width="17%"><font size="1">Matric&nbsp;No</font></td>
				<td colspan="3" ><input type="text" name="matrix_no" size="20" id="matrix_no" value="<?=$user_id?>" disabled="disabled"/></td>
				<td width="25%" rowspan="20" align="center">&nbsp;<img src="image.php" width="100" height="130" /><br> <input type="button" value="Upload Picture" id="submitpic" name="submitpic" alt="Recommended size:(126x170)" 
						onclick="javascript:open_win('image_insert.php?pid=<?=$user_id?>',480,280,0,0,0,1,0,1,1,0,5,'winupload'); " />	</td>
				</tr>
			  	
				<tr>
				  <td width="17%">Student Name </td>
				  <td colspan="3" ><input type="text" name="name" size="30" id="name" value="<?=$name?>" disabled="disabled"/></td>
				</tr>
							
				<tr>
				  <td>Programme</td>
				  <td colspan="3"><input type="text" name="program_code" size="10" id="program_code" value="<?=$program_code?>" disabled="disabled"/><input type="text" name="program_e" size="30" id="program_e" value="<?=$program_e?>" disabled="disabled"/></td>
				</tr>
				<tr>
				  <td>Intake</td>
				  <td width="10%"><input type="text" name="intake_no" size="15" id="intake_no" value="<?=$intake_no?>" disabled="disabled"/></td>
				
				  <td width="9%">Thesis ID </td>
				  <td width="39%"><input type="text" name="thesis_id" size="20" id="thesis_id" value="<?=$thesis_id?>" disabled="disabled"/></td>
				</tr>
				<tr>
				  <td>Thesis Title </td>
				  <td colspan="3"><input type="text" name="thesis_title" size="30" id="thesis_title" value="<?=$thesis_title?>" disabled="disabled"/></td>
				</tr>
				<tr>
				  <td>Student Status </td>
				  <td width="10%"><input type="text" name="student_status" size="15" id="student_status" value="<?=$student_status?>" disabled="disabled"/></td>
				  
				  <td width="9%">Skype ID </td>
				  
				  <td width="39%"><input type="text" name="skype_id" size="30" id="skype_id" value="<?=$skype_id?>" /></td>
				  
				</tr>
			</table>
			
			<table border="0">
				<tr>
				  <td>1. </td>
				  <td><strong>Thesis Process Flow </strong></td>
				  <td><strong>Status</strong></td>
				</tr>
				<tr>
				  <td>2. </td>
				  <td >Thesis Proposal </td>
				  <td ><input type="text" name="thesisProposal" size="10" id="thesisProposal" value="In Progress" disabled="disabled"/></td
				></tr>
				<tr>
				  <td>3. </td>
				  <td>Proposal Defense </td>
				  <td><input type="text" name="proposalDefense" size="10" id="proposalDefense" value="Pending" disabled="disabled"/></td>
			
				</tr>
				<tr>
				  <td>4. </td>
				  <td>Work Completion </td>
				  <td><input type="text" name="programme" size="10" id="programme" value="Pending" disabled="disabled"/></td>
			
				</tr>
				<tr>
				  <td>5. </td>
				  <td>Thesis Evaluation/VIVA </td>
				  <td><input type="text" name="intake" size="10" id="intake" value="Pending" disabled="disabled"/></td>
			
				</tr>
				<tr>
				  <td>6. </td>
				  <td>Final Submission </td>
				  <td><input type="text" name="thesisTitle" size="10" id="thesisTitle" value="Pending" disabled="disabled"/></td>
			
				</tr>
				<tr>
				  <td>7. </td>
				  <td>Senate Endorsement </td>
				  <td><input type="text" name="thesisTitle2" size="10" id="thesisTitle2" value="Pending" disabled="disabled"/></td>
			
				</tr>
			</table>
			

			 <input type="submit" name="btnSave" id="btnSave" align="center"  value="<?php echo $varBtnNm; ?>" />

	      </fieldset>
		  
		</div>
		
		<div id="tabs-2">
			
			<legend><strong>LIST OF SUPERVISOR/CO-SUPERVISOR</strong></legend>	
			<br/>			
			<table border="1" cellpadding="0" cellspacing="0" width="100%" bordercolorlight="#cccccc" bordercolordark="#eeeeee">
				<tr>
					<th>No</th>					
					<th>Staff ID</th>
					<th>Name</th>
					<th>Faculty</th>
					<th>Hp. No</th>
					<th>Email ID</th>
					<th>Skype ID</th>
					<th>Role</th>
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
    			?>			
		 	</table>






						
		</div>
		
		<div id="tabs-3">
			<fieldset>
				<legend><strong>SUBJECT TAKEN</strong></legend>
				
				<table border="1" width="100%">
				<tr> 
				  <td height="20" colspan="11" class="tbtitle"><b>&nbsp;STUDENT INFORMATION</b></td>
				</tr>
				<?
				  $sql = "select matrix_no,name from student where matrix_no = '$user_id'";
				  $dbstux=$db;
				  $dbstux->query($sql);
				  $nxstux=$dbstux->next_record();
				  $mtx=$dbstux->f("matrix_no");
				  if($nxstux){
				  ?>
					<tr> 
					  <td width="78" height="20" class="tbmenu"><font size="1">&nbsp;Matric No</font></td>
					  <td width="78" class="tbmain">&nbsp;<?=$dbstux->f("matrix_no")?>      </td>
					  <td width="82" class="tbmenu"><font size="1">&nbsp;Name</font></td>
					  <td width="522" class="tbmain">&nbsp;<?=$dbstux->f("name")?></td>
					<tr> 
					  <td width="78" height="20" class="tbmenu"><font size="1">&nbsp;Cohort</font></td>
					  <td colspan="3" class="tbsubmenu">&nbsp;<?=$intake_no?></td>
					</tr>
					<?
					$nxstux=$dbstux->next_record();
					}
				  ?>
				</table>
				 <?
				 /* function get_subj($rfs,$cod)
					{
						global $db;
						$sql="SELECT subject_eng, cre_hour FROM subject WHERE subject_code='$rfs'";
						$dbsbj=$db;
						$dbsbj->query($sql);
						$nxdbsbj=$dbsbj->next_record();
							if($cod =="1")
								{
									return $dbsbj->f("subject_eng");
								}
								if($cod =="2")
									{
										return $dbsbj->f("cre_hour");
									}			
					}*/
					?>
				<table border="1" width="100%">
					<tr>
						<th colspan="11" valign="top" class="tbtitle" height="15"><p align="center">List Of Subject Registered</p></th>
					   
					</tr>
					 <?php	
					  $sql="select ag.sem_no,ag.subject_code,ag.grade,ag.semester_id,s.sub_desc 
					  		FROM asess_grade ag 
							LEFT JOIN `subject` s ON (s.subject_code=ag.subject_code)
							WHERE ag.matrix_no='$user_id' 
							order by ag.semester_id desc";			
					//$sql="SELECT * FROM pg_supervisor WHERE pg_thesis_id='$pg_thesis_id' ORDER BY type,supervisor_id  ";					
					$result = $db_klas2->query($sql); //echo $sql;
					$varRecCount=0;					
					while($row = mysql_fetch_array($result)) 
					{ 
						$varRecCount++;
						echo "<tr>
								<td align=\"left\">".$row["sem_no"]."</td>
								<td align=\"left\">".$row["semester_id"]."</td>
								<td align=\"left\">".$row["subject_code"]."</td>
								<td align=\"left\">".$row["sub_desc"]."</td>
								<td align=\"left\">".$row["grade"]."</td>";	?>
								<!--<td align=center><?=$start = implode( '-', array_reverse( explode( '-', $row["start"] ))); ?></td>
								<td align=center><?=$conf_date = implode( '-', array_reverse( explode( '-', $row["conf_date"] ))); ?></td>
								<td align=center><?=$end = implode( '-', array_reverse( explode( '-', $row["end"] ))); ?></td-->
								<?php  
								echo "</tr>";
					}
							?>						
				</table>
				
			</fieldset>
		</div>
		
	</div>
 </form>
</body>
</html>