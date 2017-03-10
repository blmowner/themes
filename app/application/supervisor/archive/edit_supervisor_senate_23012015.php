<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: assign_supervisor.php
//
// Created by: Zuraimi
// Created Date: 27-Dec-2014
// Modified by: Zuraimi
// Modified Date: 27-Dec-2014
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

session_start();
$userid=$_SESSION['user_id'];
$proposalId=$_REQUEST['pid'];
$thesisId=$_REQUEST['tid'];
$matrixNo=$_REQUEST['mn'];
//$deptId=$_SESSION['dropDownDept'];

//used for pagination-------------------
	$page = ($_GET['page'] == 0 ? 1 : $_GET['page']);
	$perpage = 5;
	$startpoint = ($page * $perpage) - $perpage;

$varParamSend="";
$curdatetime = date("Y-m-d H:i:s");

foreach($_REQUEST as $key => $value)
{
	if($key!="page")
		$varParamSend.="&$key=$value";
}
//---------------------------------------

//ZZZZZZZZZZZZZZZ

if(isset($_POST['btnEdit']) && ($_POST['btnEdit'] <> ""))
{
	//echo "btnEdit set ++++++++++++++++++++"."<br/>";
	$supervisorBox1=$_POST['supervisorBox1'];
	$myDropDownSType1=$_POST['myDropDownSType1'];
	
	while (list ($key,$val) = @each ($supervisorBox1)) 
		{
			/*echo "supervisorBox1 key ".$key." "."val ".$val."<br/>";
			echo "supervisorId1 ".$val." ".$supervisorId1[$val]."<br/>";	
			echo "myEmployeeId1 ".$val." ".$myEmployeeId1[$val]."<br/>";	
			echo "myEmployeeName1 ".$val." ".$myEmployeeName1[$val]."<br/><br/>>";
			echo "myExpertise1 ".$val." ".$myExpertise1[$val]."<br/><br/>";
			echo "mySkypeId1 ".$val." ".$mySkypeId1[$val]."<br/><br/>";
			echo "myDropDownSType1 ".$val." ".$myDropDownSType1[$val]."<br/><br/>";*/
			
			$sql7 = "UPDATE pg_supervisor
			SET expertise='$myExpertise1[$val]', ref_supervisor_type_id='$myDropDownSType1[$val]', 
			skype_id='$mySkypeId1[$val]', modify_by='$user_id', modify_date='$curdatetime'
			WHERE id='$supervisorId1[$val]'
			AND pg_student_matrix_no = '$matrixNo'
			AND STATUS = 'A'";
			
			$dbg->query($sql7); 
			$dbg->next_record();
			//echo $sql7;
			
			$sql9 = "UPDATE pg_thesis
			SET supervisor_status = 'A'
			WHERE id ='$thesisId'
			AND student_matrix_no = '$matrixNo'";
						
			$dbg->query($sql9); 
			//$dbg->next_record();
			//echo $sql9;
		
		}

}

if(isset($_POST['btnDelete']) && ($_POST['btnDelete'] <> ""))
{
	//echo "btnDelete set ++++++++++++++++++++"."<br/>";
	$supervisorBox1=$_POST['supervisorBox1'];
	$myDropDownSType1=$_POST['myDropDownSType1'];
	
	while (list ($key,$val) = @each ($supervisorBox1)) 
		{
			/*echo "supervisorBox1 key ".$key." "."val ".$val."<br/>";
			echo "supervisorId1 ".$val." ".$supervisorId1[$val]."<br/>";	
			echo "myEmployeeId1 ".$val." ".$myEmployeeId1[$val]."<br/>";	
			echo "myEmployeeName1 ".$val." ".$myEmployeeName1[$val]."<br/><br/>>";
			echo "myExpertise1 ".$val." ".$myExpertise1[$val]."<br/><br/>";
			echo "mySkypeId1 ".$val." ".$mySkypeId1[$val]."<br/><br/>";
			echo "myDropDownSType1 ".$val." ".$myDropDownSType1[$val]."<br/><br/>";*/
			
			$sql8 = "UPDATE pg_supervisor
			SET status='D'
			WHERE id='$supervisorId1[$val]'
			AND pg_student_matrix_no = '$matrixNo'
			AND STATUS = 'A'";
			
			$dbg->query($sql8); 
			//$dbg->next_record();
			//echo $sql8;		
		}
		
		$sql9 = "SELECT id
		FROM pg_supervisor
		WHERE pg_student_matrix_no = '$matrixNo'
		AND STATUS = 'A'";

		$result_sql9 = $dbg->query($sql9); 
		//$dbg->next_record();
		//echo $sql8;
		
		$row_cnt = mysql_num_rows($result_sql9);
		if ($row_cnt==0) {
			$sql10="UPDATE pg_thesis
			SET supervisor_status = 'U'
			WHERE id = '$thesisId'
			AND student_matrix_no = '$matrixNo'";
			
			//echo $sql5; exit();
			$dbe->query($sql10); 
		}

}

if(isset($_POST['btnNext']) && ($_POST['btnNext'] <> ""))
{
	$deptId = $_POST['dropDownDept'];	
	//echo "dropDownDeptId ".$deptId."<br/>";	 
}

$sql6 = "SELECT a.id, a.pg_employee_empid, d.id AS dept_id, d.description AS department, b.name, a.expertise, 
		a.ref_supervisor_type_id, e.description AS stype, b.skype_id, DATE_FORMAT(a.respondedby_date,'%d-%b-%Y') as respondedby_date
		FROM pg_supervisor a
		LEFT JOIN ref_supervisor_type e ON (e.id = a.ref_supervisor_type_id)
		LEFT JOIN new_employee b ON (b.empid = a.pg_employee_empid)
		LEFT JOIN student c ON (c.matrix_no = a.pg_student_matrix_no)
		LEFT JOIN dept_unit d ON (d.id = b.unit_id)
		WHERE a.pg_student_matrix_no = '$matrixNo' 
		AND a.status = 'A' 
		ORDER BY d.id, a.pg_employee_empid, b.name";
	

$sql3 = "SELECT id, description
		FROM ref_supervisor_type
		WHERE status='A'";
 
$dbf->query($sql3); 
$dbf->next_record();
//echo $sql3;	



if(isset($_POST['btnAssign']) && !empty($_POST['btnAssign']) )
{
	//echo "btnAssign set ++++++++++++++++++++"."<br/>";
	$supervisorBox=$_POST['supervisorBox'];
	$myDropDownSType=$_POST['myDropDownSType'];
	
	//$arrLength = count($supervisorBox);
	//echo "arrLength ".$arrLength." ";
		
	//if ($arrLenghth>0){
	
		while (list ($key,$val) = @each ($supervisorBox)) 
		{			
			$run_pg_supervisor_id = run_num('id','pg_supervisor'); // generate id (running number)
			
			/*echo "supervisorBox key".$key." ";
			echo "val".$val."<br/>";
			echo "myEmployeeId ".$val." ".$myEmployeeId[$val]."<br/>";	
			echo "myEmployeeName ".$val." ".$myEmployeeName[$val]."<br/><br/>>";
			echo "myExpertise ".$val." ".$myExpertise[$val]."<br/><br/>";
			echo "mySkypeId ".$val." ".$mySkypeId[$val]."<br/><br/>";
			echo "myDropDownSType ".$val." ".$myDropDownSType[$val]."<br/><br/>";*/
			
			$sql4="INSERT INTO pg_supervisor
				(id, pg_employee_empid, pg_student_matrix_no, assigned_by, assigned_date, skype_id, 
				expertise, respondedby_date, ref_supervisor_type_id, status, insert_by, insert_date, modify_by, modify_date)
				VALUES ('$run_pg_supervisor_id', '$myEmployeeId[$val]', '$matrixNo', '$userid', '$curdatetime', '$mySkypeId[$val]', 
				'$myExpertise[$val]',STR_TO_DATE('$myRespondedByDate[$val]','%d-%b-%Y'),'$myDropDownSType[$val]', 'A', '$userid', '$curdatetime', '$userid', '$curdatetime') ";
				
				$dbb->query($sql4); 
				//$dbb->next_record();
		}
		$sql5="UPDATE pg_thesis
			SET supervisor_status = 'A'
			WHERE id = '$thesisId'
			AND student_matrix_no = '$matrixNo'";
		
			//echo $sql5;
		
			$dbe->query($sql5); 
		//$dbe->next_record();	
		
		//echo "btnAssign set ++++++++++++++++++++"."<br/>";
	//}
	//else {
		/*?><script type='text/javascript'>alert('Please tick the checkbox before proceed...');history.back();</script>";<?*/
	//}
}
else {
	//echo "btnAssign not set ++++++++++++++++++++"."<br/>";
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
	</head>
	
	<body>
	<form id="form1" name="form1" method="post" enctype="multipart/form-data">
		
		
		<?$result6 = $dbe->query($sql6); 
		$dbe->next_record();
		//echo $sql6;
		$row_cnt = mysql_num_rows($result6);
		if ($row_cnt>0) {?>		
			<fieldset>
			<legend><strong>List of Assigned Supervisor</strong></legend>
			<br/>
			<table border="1" style="border-collapse:collapse;" cellpadding="4" cellspacing="3">
				<tr>
					<td><strong>Tick</strong></td>
					<td><strong>No.</strong></td>
					<td><strong>Department</strong></td>
					<td><strong>Staff ID</strong></td>
					<td><strong>Supervisor Name</strong></td>  
					<td><strong>Field of Expertise</strong></td>
					<td><strong>Supervisor Role</strong></td>
					<td><strong>Skype ID</strong></td>
					<td><strong>Reply Date</strong></td>					
				</tr>    
				<?
				//$dbe->query($sql6); 
				//$dbe->next_record();
				//echo $sql6;
				$no1=0;
				$myArrayNo=0;
				do {						
					
					$supervisorId1=$dbe->f('id');	
					$employeeId1=$dbe->f('pg_employee_empid');	
					$departmentId1=$dbe->f('dept_id');
					$department1=$dbe->f('department');
					$employeeName1=$dbe->f('name');
					$expertise1=$dbe->f('expertise');
					$refSupervisorType1=$dbe->f('ref_supervisor_type_id');
					$stDescription1=$dbe->f('stype');
					$skypeId1=$dbe->f('skype_id');
					$respondedByDate=$dbe->f('respondedby_date');
				?>
				<tr>
					<input type="hidden" name="supervisorId1[]" value="<?=$supervisorId1;?>"/>				
					<td><input name="supervisorBox1[]" type="checkbox" value="<?=$no1;?>"/></td>				
					<td><?=$no1+1;?>.</td>
					
					<td><label name="myDepartment1" size="10" id="myDepartment1" ></label><?=$departmentId1;?></td>
					
					<td><label name="myEmployeeId1[]" size="15" id="employeeId1" ></label><?=$employeeId1;?></td>
					<?$myEmployeeId1[$no1]=$employeeId1;?>
					<?//echo "$no1"."-"."myEmployeeId1"."-".$myEmployeeId1[$no1];?>
					
					<td><label name="myEmployeeName1[]" size="50" id="employeeName1"></label><?=$employeeName1;?></td>
					<?$myEmployeeName1[$no1]=$employeeName1;?>
					<?//echo "$no1"."-"."myEmployeeName1"."-".$myEmployeeName1[$no1];?>
											
					<td>
					<?
					$sql_expertise1 = "SELECT descrip
					FROM education
					WHERE empid='$employeeId1'
					AND LEVEL IN ('4','5')
					ORDER BY LEVEL";
					
					$sql_expertise1;
					$result_sql_expertise1 = $db->query($sql_expertise1); 
					$db->next_record();
					
					do {
						$educationDesc1=$db->f('descrip');	
					?>
					
					<label name="myExpertise1[]" size="50" id="expertise1" ><?=$educationDesc1;?></label>
					<?
					} while ($db->next_record())
					
			
					?></td>
					<?$myExpertise1[$no1]=$expertise1;?>
					<?//echo "$no1"."-"."myExpertise1"."-".$myExpertise1[$no1];?>
					
					<td><select name = "myDropDownSType1[]">
						<option value="" selected="selected"></option>
					<?
					$dbf->query($sql3); 
					$dbf->next_record();
					do {
						$stId1=$dbf->f('id');	
						$stDescription1=$dbf->f('description');	
						if (strcmp($stId1,$refSupervisorType1)!=true) {
							?>
								<option value="<?=$stId1?>" selected="selected"><?=$stDescription1?></option>
								<?$myDropDownSType1[$no1]=$stId;?>							
								<?echo "myDropDownSType1".$myDropDownSType1[$no1];?>
							<?
						}
						else {?>
								<option value="<?=$stId1?>"><?=$stDescription1?></option>
								<?$myDropDownSType1[$no1]=$stId1;?>
								<?echo "myDropDownSType1".$myDropDownSType1[$no1];?>
							<?}
					}while ($dbf->next_record());
					?>
					</select></td>
					<td>
					<label name="mySkypeId1[]" id="skypeId1" size="20" ></label><?=$skypeId1?></td>
					<?$mySkypeId1[$no1]=$skypeId1;?>
					
					<td><input type="text" name="respondedByDate[]" size="15" id="respondedByDate" value="<?=$respondedByDate;?>"/></td>
					
					
				</tr>  
				<?
				$no1=$no1+1;
				$myArrayNo1=$myArrayNo1+1;
				}while($dbe->next_record());	
				?>			
				<?$_SESSION['myEmployeeName1'] = $myEmployeeName1;?>
				<?$_SESSION['myEmployeeId1'] = $myEmployeeId1;?>
				<?$_SESSION['myDropDownSType1'] = $myDropDownSType1;?>
				
			</table>
			<br />
			<table>
				<tr>
					<td><input type="submit" name="btnEdit" value="Update" /></td>
					<td><input type="submit" name="btnDelete" value="Delete" /></td>				
				</tr>
			</table>
			</fieldset>
			<?
		}
		else {
			?>
			<table>
				<tr>
					<td>Currently no Supervisor/Co-Supervisor has been assigned to supervise this student.</td>
				</tr>
			</table>
			<?
		}?>
		<br/>
		<?
		
		$sql1 = "SELECT du.id, du.description
		FROM new_employee e 
		LEFT JOIN dept_unit du ON (du.id=e.unit_id)
		WHERE e.dept_id='ACAD' 
		AND du.id IS NOT NULL
		GROUP BY du.id
		ORDER BY du.description";
		
		$result = $db->query($sql1); 
		//echo $sql1;
		//var_dump($db);
		$db->next_record();

		?>
		<fieldset>
		<legend><strong>Assign New Supervisor</strong></legend>
		<table>
			<tr>
				<td>Please Select Department</td>			
				<td>
					<select name = "dropDownDept">
						<option value="" selected="selected"></option>
						
					<?					
						do {						
							$id=$db->f('id');							
							$description=$db->f('description');
							if (strcmp($id,$deptId)!=true) {
							?>
								<option value="<?=$id?>" selected="selected"><?=$description;?></option>
							<?
							}
							else {?>
								<option value="<?=$id?>"><?=$description;?></option>
							<?}
						}while($db->next_record());	?>
					</select>
				</td>			
				<td><input type="submit" name="btnNext" id="btnNext" align="center"  value="Next" /></td>
			</tr>
		</table>
		<br />
		
		<?
		
		$sql2 = "SELECT DISTINCT a.empid, a.name, b.description, a.skype_id 
		FROM new_employee a 
		LEFT JOIN dept_unit b ON (b.id = a.unit_id) 
		LEFT JOIN education c ON (c.empid = a.empid)
		LEFT JOIN lookup_level_qualification d ON (d.id = c.level)
		LEFT JOIN lookup_teaching e ON (e.id = a.teachingcat)
		WHERE a.unit_id = '$deptId'
		AND e.code IN ('TR')
		AND d.id IN ('4','5')";		
 
		$result2 = $dba->query($sql2); 
		$dba->next_record();
		//echo $sql2;
		
		$row_cnt = mysql_num_rows($result2);
		if ($row_cnt>0) {?>	
		
		<table border="1" style="border-collapse:collapse;" cellpadding="4" cellspacing="3">
			<tr>
				<td><strong>Tick</strong></td>
				<td><strong>No.</strong></td>
				<td><strong>Staff ID</strong></td>
				<td><strong>Supervisor Name</strong></td>  
				<td><strong>Field of Expertise</strong></td>
				<td><strong>Supervisor Role</strong></td>
				<td><strong>Skype ID</strong></td>		  
				<td><strong>Reply Date</strong></td>	
			</tr>    
			<?

			$no=0;
			$myArrayNo=0;
			do {						
				$employeeId=$dba->f('empid');	
				$employeeName=$dba->f('name');
				$expertise=$dba->f('description');
				$skypeId=$dba->f('skype_id');
			?>
			<tr>
				<td><input name="supervisorBox[]" type="checkbox" value="<?=$no;?>" /></td>
				<td><?=$no+1;?>.</td>
				
				<td><label name="myEmployeeId[]" size="15" id="employeeId" ></label><?=$employeeId;?></td>
				<?$myEmployeeId[$no]=$employeeId;?>
				<?//echo "$no"."-"."myEmployeeId"."-".$myEmployeeId[$no];?>
				
				<td><label name="myEmployeeName[]" size="50" id="employeeName" ></label><?=$employeeName;?></td>
				<?$myEmployeeName[$no]=$employeeName;?>
				<?//echo "$no"."-"."myEmployeeName"."-".$myEmployeeName[$no];?>
										
				<td>
					<?
					$sql_expertise = "SELECT descrip
					FROM education
					WHERE empid='$employeeId'
					AND LEVEL IN ('4','5')
					ORDER BY LEVEL";
					
					$sql_expertise;
					$result_sql_expertise = $db->query($sql_expertise); 
					$db->next_record();
					
					do {
						$educationDesc=$db->f('descrip');	
					?>
					
					<label name="myExpertise[]" size="50" id="expertise" ><?=$educationDesc;?></label>
					<?
					} while ($db->next_record())
					
			
					?></td>
					<?$myExpertise1[$no1]=$expertise1;?>
					<?//echo "$no1"."-"."myExpertise1"."-".$myExpertise1[$no1];?>
				
				<td><select name = "myDropDownSType[]">
					<option value="" selected="selected"></option>
				<?
				$dbf->query($sql3); 
				$dbf->next_record();
				do {
					$stId=$dbf->f('id');	
					$stDescription=$dbf->f('description');	
					if (strcmp($stId,$stId2)!=true) {
						?>
							<option value="<?=$stId?>" selected="selected"><?=$stDescription?></option>
							<?$myDropDownSType[$no]=$stId;?>							
							<?echo "myDropDownSType".$myDropDownSType[$no];?>
						<?
					}
					else {?>
							<option value="<?=$stId?>"><?=$stDescription?></option>
							<?$myDropDownSType[$no]=$stId;?>
							<?echo "myDropDownSType".$myDropDownSType[$no];?>
						<?}
				}while ($dbf->next_record());
				?>
				</select></td>
				<td>
			    <label name="mySkypeId[]" id="skypeId" size="20"></label><?=$skypeId;?></td>
				
				<?
					$sql3_1 = "SELECT value
					FROM pg_parameter
					WHERE id = 'RESPOND_DURATION'
					AND STATUS = 'A'";

					$result3_1 = $dbb->query($sql3_1);
					$dbb->next_record();
					$parameterValue=$dbb->f('value');
					$currentDate = date('Y-m-d H:i:s');?>	
					<td><input type="text" name="myRespondedByDate[]" size="15" id="respondedByDate" value="<?=date('d-M-Y', strtotime($currentDate. ' + '.$parameterValue.' days'));?>"/></td>
					
					
			</tr>  
			<?
			$no=$no+1;
			$myArrayNo=$myArrayNo+1;
			}while($dba->next_record());	
			?>			
			<?$_SESSION['myEmployeeName'] = $myEmployeeName;?>
			<?$_SESSION['myEmployeeId'] = $myEmployeeId;?>
			<?$_SESSION['myDropDownSType'] = $myDropDownSType;?>
			
		</table>
		<br />
		<br />
		<table>
			<tr>
				<td><input type="submit" name="btnAssign" value="Assign" /></td>
			</tr>
		</table>
		</fieldset>
		<table>
			<tr>
				<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../thesis/senate_approval.php';" /></td>
			</tr>
		</table>
		<?
		}
		else
		{
			?>
			</fieldset>
			<table>
				<tr>
					<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../thesis/senate_approval.php';" /></td>
				</tr>
			</table>
			<?
		}
		?>
	</form>
	</body>
</html>