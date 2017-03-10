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

include("../../../lib/common.php");
checkLogin();

session_start();
$user_id=$_SESSION['user_id'];
$studentMatrixNo=$_REQUEST['uid'];
$pgThesisId=$_REQUEST['tid'];
$pgProposalId=$_REQUEST['pid'];
$tmpProposalId=$_REQUEST['pid'];

function runnum($column_name, $tblname) 
{ 
    global $db_klas2;
    
    $run_start = "001";
    
    $sql_slct_max = "SELECT MAX(SUBSTR($column_name,2,11)) AS run_id FROM $tblname";
    $sql_slct = $db_klas2;
    $sql_slct->query($sql_slct_max);
    $sql_slct->next_record();

    if($sql_slct->num_rows($sql_slct_max)== 0 || $sql_slct->f("run_id")==NULL) 
	{
        $run_id = date("Ymd").$run_start;
    } 
	else 
	{
        $todate = date("Ymd");
        
        if($todate > substr($sql_slct->f("run_id"),0,8)) 
		{
            $run_id = $todate.$run_start;
        } 
		else 
		{
            $run_id = $sql_slct->f("run_id") + 1; 
        }
    }
    return $run_id;
}

function runnum2($column_name, $tblname) 
{ 
    global $db_klas2;
    
    $run_start = "001";
    
    $sql_slct_max = "SELECT MAX($column_name) AS run_id FROM $tblname";
    $sql_slct = $db_klas2;
    $sql_slct->query($sql_slct_max);
    $sql_slct->next_record();

    if($sql_slct->num_rows($sql_slct_max)== 0 || $sql_slct->f("run_id")==NULL) 
	{
        $run_id = date("Ymd").$run_start;
    } 
	else 
	{
        $todate = date("Ymd");
        
        if($todate > substr($sql_slct->f("run_id"),0,8)) 
		{
            $run_id = $todate.$run_start;
        } 
		else 
		{
            $run_id = $sql_slct->f("run_id") + 1; 
        }
    }
    return $run_id;
}

if(isset($_POST['btnSearch']) && ($_POST['btnSearch'] <> ""))
{				
	$searchStudentName = $_POST['search_student_name'];
	$searchMatrixNo = $_POST['search_matrix_no'];
	$searchBranch = $_POST['selectBranch'];
	
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
	
	if ($searchBranch == 'MSUKL') {
		$theDBConn = $dbc;
	}
	else {
		$theDBConn = $dbc1;
	}
	
	$sql="SELECT DISTINCT s.matrix_no, MD5(s.matrix_no) as student_password, s.name
	FROM student s 
	LEFT JOIN student_program sp ON (sp.matrix_no = s.matrix_no) 
	LEFT JOIN program p ON (p.programid = sp.program_code) 
	WHERE p.stage IN (4,5) "
	.$tmpSearchStudentName." "
	.$tmpSearchMatrixNo." "."
	AND s.student_status LIKE 'ACTIVE' 
	ORDER BY s.matrix_no";
	
	$result = $theDBConn->query($sql); 
	$theDBConn->next_record(); 	

	$row_cnt = mysql_num_rows($result);
	
	$matrixNo = Array();	
	$studentName = Array();
	$studentPassword = Array();
	
	$no=0;
	if ($row_cnt > 0){
		do {
			$matrixNo[$no] = $theDBConn->f('matrix_no');	
			$studentName[$no] = $theDBConn->f('name');
			$studentPassword[$no] = $theDBConn->f('student_password');
			$no++;		
		} while ($theDBConn->next_record());	
	}
}


if(isset($_POST['btnUpload']) && ($_POST['btnUpload'] <> ""))
{					
	$searchBranch = $_POST['selectBranch'];
	if (sizeof($_POST['student_checkbox'])>0) {
		$curdatetime = date("Y-m-d H:i:s");
		while (list ($key,$val) = @each ($_POST['student_checkbox'])) 
		{
			$uploadMatrixNo = $_POST['upload_matrix_no'][$val];
			$uploadStudentName = $_POST['upload_student_name'][$val];
			$uploadStudentPassword = $_POST['upload_student_password'][$val];
			$curdatetime = date("Y-m-d H:i:s");
			
			$sql1="SELECT staff_id
			FROM user_acc
			WHERE staff_id = '$uploadMatrixNo'";
			
			$result1 = $dba->query($sql1); 
			
			if (mysql_num_rows($result1) > 0){
				$sql2 = "UPDATE user_acc
				SET user_pass = '$uploadStudentPassword', modify_by = '$user_id', modify_date = '$curdatetime',
				user_branch = '$searchBranch', user_type = 'S'
				WHERE staff_id = '$uploadMatrixNo'";
				
				$result2 = $dba->query($sql2); 
			}
			else {
				
				$userId = runnum2('user_id','user_acc');
				
				$sql3="INSERT INTO user_acc (user_id, role_id, staff_id, user_type, user_branch, attempts, lock_status,  user_pass, user_online_stat, user_status, user_ip, user_curr_login, user_last_login, user_last_session, created_by,created_date, modify_by, modify_date )
				VALUES	('$userId', '20141107001', '$uploadMatrixNo', 'S', '$searchBranch', 0, 'U', '$uploadStudentPassword', 0, 'ACTIVE', null, '$curdatetime' , '$curdatetime', '$curdatetime', '$user_id', '$curdatetime', '$user_id', '$curdatetime' )";
				
				$result3 = $dba->query($sql3);
			}
			
		}
		$msg[] = "<div class=\"success\"><span>KLAS2 postgrad student have been uploaded into Postgrad successfully.</span></div>";
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please tick the record to update!</span></div>";
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
		<legend><strong>Upload Postgrad Student from KLAS2 to Postgrad</strong></legend>

		<?php ?> <p>
		<table>
			<tr>
				<td><label>Student Matrix No<span style="color:#FF0000">*</span></label></td>
				<td><input name="search_matrix_no" type="text" id="search_matrix_no" size="25" value="<?=$_POST['search_matrix_no']?>"></td>				
			</tr>
			<tr>
				<td><label>Student Name <span style="color:#FF0000">*</span></label></td>
				<td><input name="search_student_name" type="text" id="search_student_name" size="50" value="<?=$_POST['search_student_name']?>"></td>
			</tr>
			<tr>
			  <td>Branch</td>
			 <?if ($selectBranch == "") $selectBranch = $_POST['selectBranch'];?>
			  <td><label>
			    <select name="selectBranch" size="1">
			      <?if ($selectBranch=="" || $selectBranch=="MSUKL") {?><option value="MSUKL" selected="selected">MSU KL</option><?} else {?><option value="MSUKL">MSU KL</option><?}?>
				  <?if ($selectBranch=="MSULK") {?><option value="MSULK" selected="selected">MSU Colombo</option><?} else {?><option value="MSULK">MSU Colombo</option><?}?>
              </select>
			  </label></td>
		  </tr>			
		</table>
		<table>
			<tr>
				<td><input type="submit" name="btnSearch" value="Search" /><span style="color:#FF0000"> Note:</span> If no entry is provided, it will search all.</td>
			</tr>
		</table>

		</p><?php ?>
		<table>
		<tr>
			<td><input type="submit" name="btnUpload" value="Upload to Postgrad" /></td>			
		</tr>
		</table>
		<table>
		<tr>							
			<td><strong>Searching Results:-</strong> <?
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
				<th align="center"><label>Tick</label></td>
				<th align="center"><label>No</label></td>
				<th align="center"><label>Student Matrix No <span style="color:#FF0000">*</span></label></th>
				<th align="center"><label>Student Name <span style="color:#FF0000">*</span></label></th>
				
			  </tr>

			<?php

			if ($no > 0) {
				$tmp_no=1;
				for ($i=0; $i<$no; $i++) 					
				{ 
					?><tr>
							<td align="center" width="30"><input name="student_checkbox[]" type="checkbox" id="student_checkbox" value="<?=$i;?>" checked="checked" /></td>
							<td align="left"><label><?=$tmp_no;?>.</label></td>
							<td align="left"><input type="text" name="upload_matrix_no[]" id="upload_matrix_no" size="50" value="<?=$matrixNo[$i];?>" ></input></td>			
							<td align="left"><input type="text" name="upload_student_name[]" id="upload_student_name" size="50" value="<?=$studentName[$i];?>" ></input></td>
							<input type="hidden" name="upload_student_password[]" id="upload_student_password" size="50" value="<?=$studentPassword[$i];?>" ></input>
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
		<br/>
		<table>
			<tr>
				<td>Notes:</td>
			</tr>
			<tr>
				<td>1. <span style="color:#FF0000">*</span> - Indicate mandatory field</td>
			</tr>
			<tr>
				<td>2. Please tick the checkbox before click Update or Delete button.</td>
			</tr>
		</table>
	</fieldset>
	<table>
		<tr>
			<td><input type="submit" name="btnUpload" value="Upload to Postgrad" /></td>			
		</tr>
	</table>

  </form>
  	<script>
		<?=$jscript;?>
	</script>
</body>
</html>





