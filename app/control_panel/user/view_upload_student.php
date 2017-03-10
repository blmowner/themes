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

<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: view_upload_student.php
//
// Created by: Zuraimi
// Created Date: 02-Jun-2015
// Modified by: Zuraimi
// Modified Date: 02-Jun-2015
//
//**************************************************************************************
include("../../../lib/common.php");
checkLogin();

session_start();
$user_id=$_SESSION['user_id'];
$studentMatrixNo=$_REQUEST['uid'];
$pgThesisId=$_REQUEST['tid'];
$pgProposalId=$_REQUEST['pid'];
$tmpProposalId=$_REQUEST['pid'];

if(isset($_POST['btnSearch']) && ($_POST['btnSearch'] <> ""))
{				
	//$searchStudentName = $_POST['search_student_name'];
	//$searchMatrixNo = $_POST['search_matrix_no'];
	$searchUserType = $_POST['selectUserType'];
	$searchBranch = $_POST['selectBranch'];
	/*
	if ($searchStudentName!="") 
	{
		$tmpSearchStudentName = " AND s.name LIKE '%$searchStudentName%'";
	}
	else 
	{
		$tmpSearchStudentName="";
	}
	if ($searchMatrixNo!="") 
	{
		$tmpSearchMatrixNo = " AND s.matrix_no = '$searchMatrixNo'";
	}
	else 
	{
		$tmpSearchMatrixNo="";
	}
	*/
	$tmpSearchUserType = " WHERE a.user_type = '$searchUserType'";
	$tmpSearchBranch = " AND a.user_branch = '$searchBranch'";
		
	if ($searchBranch == 'MSUKL') {
		$theDBConn = $dbc;		
	}
	else {
		$theDBConn = $dbc1;
	}
	
	
	$sql="SELECT a.staff_id, a.role_id, b.role_type
	FROM user_acc a
	LEFT JOIN base_user_role b ON (b.role_id = a.role_id)"
	.$tmpSearchUserType." "
	.$tmpSearchBranch." "."
	ORDER BY a.staff_id";
	
	$result = $db->query($sql); 
	$db->next_record(); 	

	$row_cnt = mysql_num_rows($result);
	
	$userId = Array();	
	$userName = Array();
	
	$no=0;
	if ($row_cnt > 0){
		do {
			$userId[$no] = $db->f('staff_id');	
			
			if ($searchUserType == 'E') {
				$sql1 = "SELECT name
				FROM new_employee
				WHERE empid = '$userId[$no]'";
				$result_sql2 = $theDBConn->query($sql1);				
				$theDBConn->next_record(); 	
				$userName[$no] = $theDBConn->f('name');
			}
			else {
				$sql2 = "SELECT name
				FROM student
				WHERE matrix_no = '$userId[$no]'";
				$result_sql2 = $theDBConn->query($sql2); 
				$theDBConn->next_record(); 	
				$userName[$no] = $theDBConn->f('name');
			}
			$roleId[$no] = $db->f('role_id');	
			$roleType[$no] = $db->f('role_type');	
			$no++;		
		} while ($db->next_record());	
	}
}
?>	


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
	<link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
    <link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
   	<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
	<link rel="stylesheet" href="../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
	<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
	<script src="../../../lib/js/jquery.min2.js"></script>
    <script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
	
</head>
<body>

<SCRIPT LANGUAGE="JavaScript">

function respConfirm () {
    var confirmSubmit = confirm("Click OK if you confirm to delete else click Cancel to stay on the same page.");
	if (confirmSubmit==true)
	{
		return saveStatus;
	}
	if (confirmSubmit==false)
	{
		return false;
	}
}

</SCRIPT>
<script>
$(function() {
	$( "#datepickerFirst" ).datepicker({
		changeMonth: true,
		changeYear: true,
		yearRange: '-100:+0',
		dateFormat: 'dd-mm-yy'
		});
});
</script>

 <?php
    if(!empty($msg)) 
	{
        foreach($msg as $err) 
		{
            echo $err;
        }
    }
?>

  <form id="form1" name="form1" method="post" enctype="multipart/form-data" onsubmit="return saveRec();">

	 <fieldset>
		<legend><strong>View Postgrad Student in Postgrad</strong></legend>

		<?php ?> <p>
		<table>
			<tr>
				<td><label>Student Matrix No<span style="color:#FF0000">*</span></label></td>
				<td><input name="search_matrix_no" type="text" id="search_matrix_no" size="25" value="<?=$_POST['search_matrix_no']?>"> - unused</td>				
			</tr>
			<tr>
				<td><label>Student Name <span style="color:#FF0000">*</span></label></td>
				<td><input name="search_student_name" type="text" id="search_student_name" size="50" value="<?=$_POST['search_student_name']?>"> - unused</td>
			</tr>
			<tr>
			  <td>Branch</td>
			 <?if ($selectBranch == "") $selectBranch = $_POST['selectBranch'];?>
			  <td><label>
			    <select name="selectBranch" size="1">
			      <?if ($selectBranch=="" || $selectBranch=="MSUKL") {?><option value="MSUKL" selected="selected">MSU KL</option><?} else {?><option value="MSUKL">MSU KL</option><?}?>
				  <?if ($selectBranch=="MSULK") {?><option value="MSUKL" selected="selected">MSU Colombo</option><?} else {?><option value="MSULK">MSU Colombo</option><?}?>
              </select>
			  </label></td>
			</tr>	
			<tr>
			  <td>User Type</td>
			 <?if ($selectUserType == "") $selectUserType = $_POST['selectUserType'];?>
			  <td><label>
			    <select name="selectUserType" size="1">
			      <?if ($selectUserType=="" || $selectUserType=="E") {?><option value="E" selected="selected">Staff</option><?} else {?><option value="E">Staff</option><?}?>
				  <?if ($selectUserType=="S") {?><option value="S" selected="selected">Student</option><?} else {?><option value="S">Student</option><?}?>
              </select>
			  </label></td>
			</tr>
					  
		</table>
		<table>
			<tr>
				<td><input type="submit" name="btnSearch" value="Search" /></td>
			</tr>
		</table>

		</p>
		<table>
		<tr>							
			<td>Searching Results:-<?
			if ($no > 0) {
				?> <?=$no?> records found
			<?}?></td>
		</tr>
		</table>
		<? if($no == 1)
		{?>
			<div id = "tabledisplay" style="overflow:auto; height:120px;">
		<? }
		else if($no == 2)
		{ ?>
			<div id = "tabledisplay" style="overflow:auto; height:200px;">

		<? }
		else if ($no == 3)
		{ ?> 
			<div id = "tabledisplay" style="overflow:auto; height:300px;">		
		<? }		
		else if($no > 4)
		{ ?>
			<div id = "tabledisplay" style="overflow:auto; height:500px;">
		<? }
		else
		{?>
			<div id = "tabledisplay">		
		<? }
		?>
		<table border="1" cellpadding="3" cellspacing="3" width="100%" id="inputs10" class="thetable">
			  <tr align="left">
				<th align="center"><label>No</label></td>
				<th align="center"><label>User ID</label></th>
				<th align="center"><label>User Name</label></th>
				<th align="center"><label>Role ID</label></th>
				<th align="center"><label>Role Type</label></th>
				
			  </tr>

			<?php

			if ($no > 0) {
				$tmp_no=1;
				for ($i=0; $i<$no; $i++) 					
				{ 
					?><tr>
							<td align="left"><label><?=$tmp_no;?>.</label></td>
							<td align="left"><label><?=$userId[$i];?></label></td>			
							<td align="left"><label><?=$userName[$i];?></label></td>
							<td align="left"><label><?=$roleId[$i];?></label></td>
							<td align="left"><label><?=$roleType[$i];?></label></td>
						</tr>
					<?
				$tmp_no++;
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
		</div>		
	</fieldset>
  </form>
  	<script>
		<?=$jscript;?>
	</script>
</body>
</html>





