<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: edit_reviewer.php
//
// Created by: Zuraimi
// Created Date: 28-Jan-2015
// Modified by: Zuraimi
// Modified Date: 28-Jan-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

session_start();
$userid=$_SESSION['user_id'];
$proposalId=$_REQUEST['pid'];
$thesisId=$_REQUEST['tid'];
$matrixNo=$_REQUEST['mn'];
$sname=$_REQUEST['sname'];

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


if(isset($_POST['btnEdit']) && ($_POST['btnEdit'] <> ""))
{
	//echo "btnEdit set ++++++++++++++++++++"."<br/>";
	$reviewerBox1=$_POST['reviewerBox1'];
	$myDropDownSType1=$_POST['myDropDownSType1'];
	$respondedByDate=$_POST['respondedByDate'];
	
	while (list ($key,$val) = @each ($reviewerBox1)) 
	{
		/*echo "reviewerBox1 key ".$key." "."val ".$val."<br/>";
		echo "reviewerId1 ".$val." ".$reviewerId1[$val]."<br/>";	
		echo "myEmployeeId1 ".$val." ".$myEmployeeId1[$val]."<br/>";	
		echo "myEmployeeName1 ".$val." ".$myEmployeeName1[$val]."<br/><br/>>";
		echo "myExpertise1 ".$val." ".$myExpertise1[$val]."<br/><br/>";
		echo "mySkypeId1 ".$val." ".$mySkypeId1[$val]."<br/><br/>";
		echo "myDropDownSType1 ".$val." ".$myDropDownSType1[$val]."<br/><br/>";
		echo "respondedByDate ".$val." ".$respondedByDate[$val]."<br/><br/>";*/
		
		$sql7 = "UPDATE pg_supervisor
		SET ref_supervisor_type_id='RV', respondedby_date = STR_TO_DATE('$respondedByDate[$val]','%d-%b-%Y'),
		modify_by='$user_id', modify_date='$curdatetime'
		WHERE id='$reviewerId1[$val]'
		AND pg_student_matrix_no = '$matrixNo'
		AND pg_thesis_id = '$thesisId'
		AND STATUS = 'A'";
		
		$dbg->query($sql7); 
		$dbg->next_record();
		//echo $sql7;
		
		$sql9 = "UPDATE pg_thesis
		SET reviewer_status = 'A'
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
	$reviewerBox1=$_POST['reviewerBox1'];
	$myDropDownSType1=$_POST['myDropDownSType1'];
	$acceptanceStatus=$_POST['acceptanceStatus'];
	
	while (list ($key,$val) = @each ($reviewerBox1)) 
		{
			/*echo "reviewerBox1 key ".$key." "."val ".$val."<br/>";
			echo "reviewerId1 ".$val." ".$reviewerId1[$val]."<br/>";	
			echo "myEmployeeId1 ".$val." ".$myEmployeeId1[$val]."<br/>";	
			echo "myEmployeeName1 ".$val." ".$myEmployeeName1[$val]."<br/><br/>>";
			echo "myExpertise1 ".$val." ".$myExpertise1[$val]."<br/><br/>";
			echo "mySkypeId1 ".$val." ".$mySkypeId1[$val]."<br/><br/>";
			echo "myDropDownSType1 ".$val." ".$myDropDownSType1[$val]."<br/><br/>";*/
			
			if ($acceptanceStatus == 'DNE') {
				echo $sql8 = "UPDATE pg_supervisor
				SET STATUS = 'D'
				WHERE id='$reviewerId1[$val]'
				AND ref_supervisor_type_id='RV'
				AND pg_student_matrix_no = '$matrixNo'
				AND pg_thesis_id = '$thesisId'
				AND STATUS = 'A'";
				
				$dbg->query($sql8); 
				//$dbg->next_record();
				//echo $sql8;
			} else {				
				echo $sql8 = "DELETE FROM pg_supervisor
				WHERE id='$reviewerId1[$val]'
				AND ref_supervisor_type_id='RV'
				AND pg_student_matrix_no = '$matrixNo'
				AND pg_thesis_id = '$thesisId'
				AND STATUS = 'A'";
				
				$dbg->query($sql8); 
				//$dbg->next_record();
				//echo $sql8;		
			}
		}
		
		$sql9 = "SELECT id
		FROM pg_supervisor
		WHERE pg_student_matrix_no = '$matrixNo'
		AND ref_supervisor_type_id = 'RV'
		AND pg_thesis_id = '$thesisId'
		AND STATUS = 'A'";

		$result_sql9 = $dbg->query($sql9); 
		//$dbg->next_record();
		//echo $sql8;
		
		$row_cnt = mysql_num_rows($result_sql9);
		if ($row_cnt==0) {
			$sql10="UPDATE pg_thesis
			SET reviewer_status = 'U'
			WHERE id = '$thesisId'
			AND student_matrix_no = '$matrixNo'";
			
			//echo $sql5; exit();
			$dbe->query($sql10); 
		}

}

if(isset($_POST['btnSearch']) && ($_POST['btnSearch'] <> ""))
{
	$searchDeptId = $_POST['searchDeptId'];	
	$searchReviewerName = $_POST['searchReviewerName'];
	$searchExpertise = $_POST['searchExpertise'];
	
	if ($searchDeptId!="") 
	{
		$tmpSearchDeptId = " AND a.unit_id = '$searchDeptId'";
	}
	else 
	{
		$tmpSearchDeptId="";
	}
	if ($searchReviewerName!="") 
	{
		$tmpSearchReviewerName = " AND (a.name like '%$searchReviewerName%' OR a.empid like '%$searchReviewerName%')";
	}
	else 
	{
		$tmpSearchReviewerName="";
	}
	if ($searchExpertise!="") 
	{
		$tmpSearchExpertise = " AND (f.expertise like '%$searchExpertise%' OR g.area like '%$searchExpertise%')";
	}
	else 
	{
		$tmpSearchExpertise="";
	}
	
	
	$sql2 = "SELECT DISTINCT a.empid, a.name, b.description, a.email, a.unit_id, b.description AS department  
		FROM new_employee a 
		LEFT JOIN dept_unit b ON (b.id = a.unit_id) 
		LEFT JOIN education c ON (c.empid = a.empid)
		LEFT JOIN lookup_level_qualification d ON (d.id = c.level)
		LEFT JOIN lookup_teaching e ON (e.id = a.teachingcat)
		LEFT JOIN employee_expertise f ON (f.empid = a.empid)
		LEFT JOIN job_list_category g ON (g.jobarea = f.expertise)
		WHERE a.teachingcat IN ('1','3') 
		AND a.dept_id='ACAD'
		AND  a.empid NOT IN (SELECT pg_employee_empid FROM pg_supervisor
		WHERE ref_supervisor_type_id in ('RV')
		AND pg_student_matrix_no = '$matrixNo'
		AND pg_thesis_id = '$thesisId'
		AND status = 'A')"		
		.$tmpSearchDeptId." "
		.$tmpSearchReviewerName." "
		.$tmpSearchExpertise." "."
		AND c.level IN ('4','5')
		ORDER BY a.unit_id, a.name, a.empid";		
		
		$result2 = $dba->query($sql2); 
		$dba->next_record();

}
else {
	/*$sql2 = "SELECT DISTINCT a.empid, a.name, b.description, a.email, a.unit_id, b.description AS department  
		FROM new_employee a 
		LEFT JOIN dept_unit b ON (b.id = a.unit_id) 
		LEFT JOIN education c ON (c.empid = a.empid)
		LEFT JOIN lookup_level_qualification d ON (d.id = c.level)
		LEFT JOIN lookup_teaching e ON (e.id = a.teachingcat)
		LEFT JOIN employee_expertise f ON (f.empid = a.empid)
		LEFT JOIN job_list_category g ON (g.jobarea = f.expertise)
		WHERE a.teachingcat IN ('1','3') 
		AND a.dept_id='ACAD'
		AND  a.empid NOT IN (SELECT pg_employee_empid FROM pg_supervisor
		WHERE ref_supervisor_type_id in ('RV')
		AND pg_student_matrix_no = '$matrixNo'
		AND pg_thesis_id = '$thesisId'
		AND status = 'A')
		AND c.level IN ('4','5')
		ORDER BY a.unit_id, a.name, a.empid";		
		
		$result2 = $dba->query($sql2); 
		$dba->next_record();*/
}

$sql6 = "SELECT a.id, a.pg_employee_empid, d.id AS dept_id, d.description AS department, b.name,  
		a.ref_supervisor_type_id, e.description AS stype, b.email, DATE_FORMAT(a.respondedby_date,'%d-%b-%Y') as respondedby_date,
		a.assigned_remarks, a.recipient_remarks, a.extension_status, a.acceptance_status
		FROM pg_supervisor a
		LEFT JOIN ref_supervisor_type e ON (e.id = a.ref_supervisor_type_id)
		LEFT JOIN new_employee b ON (b.empid = a.pg_employee_empid)
		LEFT JOIN student c ON (c.matrix_no = a.pg_student_matrix_no)
		LEFT JOIN dept_unit d ON (d.id = b.unit_id)
		WHERE a.pg_student_matrix_no = '$matrixNo' 
		AND a.ref_supervisor_type_id = 'RV'
		AND a.acceptance_status IS NULL
		AND a.pg_thesis_id = '$thesisId'
		AND a.status = 'A' 
		ORDER BY e.seq, d.id, a.pg_employee_empid, b.name";		

$sql3 = "SELECT id, description
		FROM ref_supervisor_type
		WHERE status='A'";
 
$dbf->query($sql3); 
$dbf->next_record();
//echo $sql3;	



if(isset($_POST['btnAssign']) && !empty($_POST['btnAssign']) )
{
	//echo "btnAssign set ++++++++++++++++++++"."<br/>";
	$reviewerBox=$_POST['reviewerBox'];
	$myDropDownSType=$_POST['myDropDownSType'];
	$myRespondedByDate=$_SESSION['myRespondedByDate'];
	
	//$arrLength = count($reviewerBox);
	//echo "arrLength ".$arrLength." ";
		
	//if ($arrLenghth>0){
	

		while (list ($key,$val) = @each ($reviewerBox)) 
		{			
			$run_pg_supervisor_id = run_num('id','pg_supervisor'); // generate id (running number)
			
			/*echo "reviewerBox key".$key." ";
			echo "val".$val."<br/>";
			echo "myEmployeeId ".$val." ".$myEmployeeId[$val]."<br/>";	
			echo "myEmployeeName ".$val." ".$myEmployeeName[$val]."<br/><br/>>";
			echo "myExpertise ".$val." ".$myExpertise[$val]."<br/><br/>";
			echo "mySkypeId ".$val." ".$mySkypeId[$val]."<br/><br/>";
			echo "myDropDownSType ".$val." ".$myDropDownSType[$val]."<br/><br/>";*/
			

				$lock_tables="LOCK TABLES pg_supervisor WRITE"; //lock the table
				$db->query($lock_tables);
				
				$sql4="INSERT INTO pg_supervisor
				(id, pg_employee_empid, pg_student_matrix_no, pg_thesis_id, assigned_by, assigned_date, 
				respondedby_date, ref_supervisor_type_id, status, insert_by, insert_date, modify_by, modify_date)
				VALUES ('$run_pg_supervisor_id', '$myEmployeeId[$val]', '$matrixNo', '$thesisId', '$userid', '$curdatetime', STR_TO_DATE('$myRespondedByDate[$val]','%d-%b-%Y'),'RV', 'A', '$userid', '$curdatetime', '$userid', '$curdatetime') ";
				
				//echo $sql4;
				$dbb->query($sql4); 
				//$dbb->next_record();
				$unlock_tables="UNLOCK TABLES"; //unlock the table;
				$db->query($unlock_tables);
			
		}
		$sql5="UPDATE pg_thesis
			SET reviewer_status = 'A'
			WHERE id = '$thesisId'
			AND student_matrix_no = '$matrixNo'";
		
			//echo $sql5;
		
			$dbe->query($sql5); 
		//$dbe->next_record();	

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
			<fieldset>
			<legend><strong>List of Assigned Reviewer</strong></legend>
			<table>
				<tr>
					<td>Student Matrix No</td>
					<td>:</td>
					<td><?=$matrixNo?></td>
				</tr>
				<tr>
					<td>Student Name</td>
					<td>:</td>
					<td><?=$sname?></td>
				</tr>
			</table>
			<br/>
			<?$result6 = $dbe->query($sql6); 
			$dbe->next_record();
			//echo $sql6;
			$row_cnt = mysql_num_rows($result6);
			if ($row_cnt>0) {?>		
			<table border="1" style="border-collapse:collapse;" cellpadding="4" cellspacing="3">
				<tr>
					<td><strong>Tick</strong></td>
					<td><strong>No.</strong></td>
					<td><strong>Dept</strong></td>
					<td><strong>Name</strong></td>  
					<td><strong>Qualification</strong></td>
					<td><strong>Field of Expertise</strong></td>					
					<td><strong>Email ID</strong></td>
					<td><strong>Reply Date</strong></td>					
				</tr>    
				<?
				//$dbe->query($sql6); 
				//$dbe->next_record();
				//echo $sql6;
				$no1=0;
				$myArrayNo=0;
				do {						
					
					$reviewerId1=$dbe->f('id');	
					$employeeId1=$dbe->f('pg_employee_empid');	
					$departmentId1=$dbe->f('dept_id');
					$department1=$dbe->f('department');
					$employeeName1=$dbe->f('name');
					$expertise1=$dbe->f('expertise');					
					$stDescription1=$dbe->f('stype');
					$email1=$dbe->f('email');
					$respondedByDate=$dbe->f('respondedby_date');
					$assignedRemarks=$dbe->f('assigned_remarks');
					$recipientRemarks=$dbe->f('recipient_remarks');
					$acceptanceStatus=$dbe->f('acceptance_status');
				?>
				<tr>
					<input type="hidden" name="reviewerId1[]" value="<?=$reviewerId1;?>"/>
					<input type="hidden" name="acceptanceStatus[]" value="<?=$acceptanceStatus;?>"/>					
					<?$acceptanceStatus[$no1]=$acceptanceStatus;?>
					
					<td><input name="reviewerBox1[]" type="checkbox" value="<?=$no1;?>"/></td>				
					<td><?=$no1+1;?>.</td>
					
					<td><a><width="20" height="19" style="border:0px;" title="<?=$department1;?>"><?=$departmentId1;?></a></td>
					
					<input type="hidden" name="myEmployeeId1[]" size="15" id="employeeId1" >
					<?$myEmployeeId1[$no1]=$employeeId1;?>
					<?//echo "$no1"."-"."myEmployeeId1"."-".$myEmployeeId1[$no1];?>
					
					<td><label name="myEmployeeName1[]" size="50" id="employeeName1"></label><?=$employeeName1;?><br/>(<?=$employeeId1;?>)
					<br/>
                        <a href="../reviewer/edit_reviewer_remarks.php?rid=<?=$reviewerId1;?>&mn=<?=$matrixNo?>&tid=<?=$thesisId?>&pid=<?=$proposalId?>" title="Remarks">
                          <?=$pgThesisId;?>
						  <?if ($assignedRemarks == null || $assignedRemarks ==""){?>
									<img src="../images/red_edit.jpg" width="20" height="19" style="border:0px;" title="Remark is not yet provided" >Enter Faculty remarks	
								<?}
								else {
								?>
									<img src="../images/edit.jpg" width="20" height="19" style="border:0px;" title="Remark is provided" >Read remarks by Faculty	
								<?
							}?><br/>
							<?if ($recipientRemarks == null || $recipientRemarks ==""){?>
										<img src="../images/red_edit.jpg" width="20" height="19" style="border:0px;" title="Faculty Remark is not yet provided" >Pending feedback by Reviewer
									<?}
									else {
									?>
										<img src="../images/edit.jpg" width="20" height="19" style="border:0px;" title="Faculty Remark is provided" >Read feedback provided by Reviewer
									<?
							}?></a></td>
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
					
					<label name="myExpertise1[]" size="50" id="expertise1" >- <?=$educationDesc1;?></label><br/>
					<?
					} while ($db->next_record())
					
					
					?>					
					</td>
					
					<td>
					<?$sqlAreaExpertise = "SELECT a.expertise, b.area
						FROM employee_expertise a
						LEFT JOIN job_list_category b ON (b.jobarea = a.expertise)
						WHERE empid = '$employeeId1'";
						
						$resultSqlAreaExpertise = $db->query($sqlAreaExpertise); 
						$db->next_record();
						
						do {
							$area=$db->f('area');
							if ($area!="") {							
								?><label>- <?=$area;?></label><br/><?
							}
						} while ($db->next_record());
						?>					
					</td>
					
					<?$myExpertise1[$no1]=$expertise1;?>
					<?//echo "$no1"."-"."myExpertise1"."-".$myExpertise1[$no1];?>
					
					<td>
					<label name="myEmail1[]" id="email1" size="20" ></label><?=$email1?></td>
					<?$myEmail1[$no1]=$email1;?>
					
					<td><input type="text" name="respondedByDate[]" size="15" id="respondedByDate" value="<?=$respondedByDate;?>"/></td>
					<?$respondedByDate[$no1]=$respondedByDate;?>
					
					
				</tr>  
				<?
				$no1=$no1+1;
				$myArrayNo1=$myArrayNo1+1;
				}while($dbe->next_record());	
				?>			
				<?$_SESSION['myEmployeeName1'] = $myEmployeeName1;?>
				<?$_SESSION['myEmployeeId1'] = $myEmployeeId1;?>
				<?$_POST['respondedByDate'] = $respondedByDate;?>
				<?$_SESSION['myDropDownSType1'] = $myDropDownSType1;?>
				
				
			</table>
			<br />
			<table>
				<tr>
					<td><input type="submit" name="btnEdit" value="Update" /></td>
					<td><input type="submit" name="btnDelete" value="Delete" /></td>
					<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../thesis/confirm_proposal.php';" /></td>					
				</tr>
			</table>
			</fieldset>
			<?
		}
		else {
			?>
			<fieldset>
			<legend><strong><span style="color:#FF0000">Notification Message</span></strong></legend>
				<table>
					<tr>
						<td>There is no Reviewer has been assigned which pending feedback.</td>
					</tr>
				</table>
			</fieldset>
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
		<legend><strong>Assign New Reviewer</strong></legend>
		<table>
			<tr>							
				<td><strong>Please enter searching criteria below</strong></td>
			</tr>
		</table>
		<br/>
		<table>
			<tr>
				<td>Department</td>			
				<td>
					<select name = "searchDeptId">
						<option value="" selected="selected"></option>
						
					<?					
						do {						
							$id=$db->f('id');							
							$description=$db->f('description');
							if ($id==$searchDeptId) {
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
			</tr>
			<tr>
			<tr>
				<td>Reviewer Name / Staff ID</td>			
				<td><input name="searchReviewerName" type="text" id="searchReviewerName" size="40" value="<?=$searchReviewerName;?>" /></td>				
			</tr>
			<tr>
				<td>Field of Expertise / ID</td>			
				<td><input name="searchExpertise" type="text" id="searchExpertise" size="40" value="<?=$searchExpertise;?>" /></td>
				<?$_POST['searchDeptId'] = $searchDeptId;?>
				<?$_POST['searchReviewerName'] = $searchReviewerName;?>
				<?$_POST['searchExpertise'] = $searchExpertise;?>
				<td><input type="submit" name="btnSearch" id="btnSearch" align="center"  value="Search" /></td>
				<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../thesis/confirm_proposal.php';" /></td>
				<td>Note: When clicked, if no parameters are provided, it will search all.</td>
			</tr>
		</table>
		<br />
		
		<?
		
		/*$sql2 = "SELECT DISTINCT a.empid, a.name, b.description, a.email 
		FROM new_employee a 
		LEFT JOIN dept_unit b ON (b.id = a.unit_id) 
		LEFT JOIN education c ON (c.empid = a.empid)
		LEFT JOIN lookup_level_qualification d ON (d.id = c.level)
		LEFT JOIN lookup_teaching e ON (e.id = a.teachingcat)
		WHERE a.unit_id = '$deptId'
		-- AND e.code IN ('TR','TT')
		-- AND d.id IN ('4','5')
		ORDER BY a.empid, a.name";	*/	
 
		//$result2 = $dba->query($sql2); 
		//$dba->next_record();
		//echo $sql2;
		
		$row_cnt = mysql_num_rows($result2);
		if ($row_cnt>0) {?>	
		<table>
				<tr>							
					<td><strong>Searching Results:-</strong></td>
				</tr>
		</table>
		<table border="1" style="border-collapse:collapse;" cellpadding="4" cellspacing="3">
			<tr>
				<td><strong>Tick</strong></td>
				<td><strong>No.</strong></td>
				<td><strong>Dept</strong></td>
				<td><strong>Staff ID</strong></td>
				<td><strong>Name</strong></td>  
				<td><strong>Qualification</strong></td>				
				<td><strong>Email ID</strong></td>		  
				<?
				$sql3_1 = "SELECT const_value
				FROM base_constant
				WHERE const_term = 'RV_RESPOND_DURATION'";

				$result3_1 = $dbb->query($sql3_1);
				$dbb->next_record();
				$parameterValue=$dbb->f('const_value');
				$currentDate = date('d-M-Y');					
				?>				
				<td><strong>Reply Date</strong><br/> within <?=$parameterValue?> day(s)</td>	
			</tr>    
			<?

			$no=0;
			$myArrayNo=0;
			do {						
				$employeeId=$dba->f('empid');	
				$employeeName=$dba->f('name');
				$expertise=$dba->f('description');
				$emailId=$dba->f('email');
				$department=$dba->f('department');
				$unitId=$dba->f('unit_id');
			?>
			<tr>
				<td><input name="reviewerBox[]" type="checkbox" value="<?=$no;?>" /></td>
				<td><?=$no+1;?>.</td>
				
				<td><a><width="20" height="19" style="border:0px;" title="<?=$department;?>"><?=$unitId;?></a></td>
				
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
					
					<label name="myExpertise[]" size="50" id="expertise" >- <?=$educationDesc;?></label><br/>
					<?
					} while ($db->next_record())
					
			
					?></td>
					<?$myExpertise1[$no1]=$expertise1;?>
					<?//echo "$no1"."-"."myExpertise1"."-".$myExpertise1[$no1];?>
				
				
				<td>
			    <label name="myEmailId[]" id="emailId" size="20"></label><?=$emailId;?></td>
				
				<?$respondedbyDate = date('d-M-Y', strtotime($currentDate. ' + '.$parameterValue.' days'));
					?>	
					
					<td><label name="myRespondedByDate[]" size="15" id="respondedByDate" ><?=$respondedbyDate;?></label></td>
					<?$myRespondedByDate[$no]=$respondedbyDate;?>
					
					
			</tr>  
			<?
			$no=$no+1;
			$myArrayNo=$myArrayNo+1;
			}while($dba->next_record());	
			?>			
			<?$_SESSION['myEmployeeName'] = $myEmployeeName;?>
			<?$_SESSION['myEmployeeId'] = $myEmployeeId;?>
			<?$_SESSION['myRespondedByDate'] = $myRespondedByDate;?>
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
				<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../thesis/confirm_proposal.php';" /></td>
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
					<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../thesis/confirm_proposal.php';" /></td>
				</tr>
			</table>
			<?
		}
		?>
	</form>
	</body>
</html>