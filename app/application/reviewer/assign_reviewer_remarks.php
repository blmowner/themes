<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: assign_reviewer_ouline.php
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
$matrixNo=$_REQUEST['mn'];
$thesisId=$_REQUEST['tid'];
$reviewerId=$_REQUEST['rid'];

///////////////////////////////////////////////////////////////
// used for pagination
	$page = ($_GET['page'] == 0 ? 1 : $_GET['page']);
	$perpage = 2;
	$startpoint = ($page * $perpage) - $perpage;

$varParamSend="";

foreach($_REQUEST as $key => $value)
{
	if($key!="page")
		$varParamSend.="&$key=$value";
}

if(isset($_POST['btnUpdate']) && ($_POST['btnUpdate'] <> "")) {
	
	$assignedRemarks=$_POST['assignedRemarks'];	
	$currentDate = date('Y-m-d H:i:s');
	
	$sql1 = "UPDATE pg_supervisor SET
				assigned_by='$userid', assigned_date = '$currentDate', 
				assigned_remarks = '$assignedRemarks', modify_by = '$userid', modify_date = '$currentDate' 	
				WHERE id='$reviewerId'";
	//echo $sql1;exit();
	$dbg->query($sql1); 
}

///////////////////////////////////////////////////////////////
		
		$sql = "select DATE_FORMAT(f.assigned_date,'%d-%b-%Y') AS assigned_date, f.assigned_remarks, f.pg_employee_empid,
		DATE_FORMAT(f.recipient_date,'%d-%b-%Y') AS recipient_date, f.recipient_remarks, f.assigned_by
		FROM pg_proposal a 
		LEFT JOIN ref_thesis_type b ON (b.id = a.thesis_type)
		LEFT JOIN ref_proposal_status c1 ON (c1.id=a.verified_status) 
		LEFT JOIN ref_proposal_status c2 ON (c2.id=a.status) 
		LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id )	
		LEFT JOIN pg_supervisor f ON (f.pg_student_matrix_no = d.student_matrix_no)
		WHERE f.id = '$reviewerId'";
			
		$result = $db->query($sql); 
		//echo $sql;
		//var_dump($db);
		$db->next_record();

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
		<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>
		<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>	
	</head>
	<body>  
		<form id="form1" name="form1" method="post" enctype="multipart/form-data">		
		<?
		$pgThesisId=$db->f('pg_thesis_id');
		$id=$db->f('id');
		
		$assignedBy=$db->f('assigned_by');
		$assignedDate=$db->f('assigned_date');
		$assignedRemarks=$db->f('assigned_remarks');
		
		$recipientBy=$db->f('pg_employee_empid');
		$recipientName=$db->f('recipient_name');
		$recipientDate=$db->f('recipient_date');
		$recipientRemarks=$db->f('recipient_remarks');

		?>
		
		<fieldset>
		<legend><strong>Remarks by Faculty to Reviewer</strong></legend>		
			<table>
				<tr>
					<td>By</td>
					<td>:</td>
					<?
					$sql4 = "SELECT name AS assigned_name
							FROM new_employee 
							WHERE empid = '$assignedBy'";
								
							$result4 = $dbc->query($sql4); 
							$dbc->next_record();
							$assignedName=$dbc->f('assigned_name');
				
					?>
					<td><label name="assignedName" size="50" id="assignedName" ></label><?=$assignedName;?></td>					
				</tr>
				<tr>
					<td>Date</td>
					<td>:</td>
					<td><label name="assignedDate" size="15" id="assignedDate" ></label><?=$assignedDate;?></td>					
				</tr>
				<tr>
					<td>Remarks</td>
					<td>:</td>
					<td><textarea name="assignedRemarks" id="assignedRemarks" class="ckeditor" cols="50" rows="3"><?=$assignedRemarks;?></textarea></td>					
						
				</tr>
			</table>
		</fieldset>
		<br/>
	<fieldset>
		<legend><strong>Feedback by Reviewer to Faculty</strong></legend>		
			<table>
				<tr>
					<td>By</td>
					<td>:</td>
					<?
					$sql4 = "SELECT name AS recipient_name
							FROM new_employee 
							WHERE empid = '$recipientBy'";
								
							$result4 = $dbc->query($sql4); 
							$dbc->next_record();
							$recipientName=$dbc->f('recipient_name');
				
					?>
					<td><label name="recipientName" size="50" id="recipientName" ></label><?=$recipientName;?></td>					
				</tr>
				<tr>
					<td>Date</td>
					<td>:</td>
					<td><label name="recipientDate" size="15" id="recipientDate" ></label><?=$recipientDate;?></td>					
				</tr>
				<tr>
					<td>Feedback</td>
					<td>:</td>
					<td><label name="recipientRemarks" id="recipientRemarks" cols="50" rows="3"><?=$recipientRemarks;?></label></td>					
						
				</tr>
			</table>
		</fieldset>
		<br/>
	<table>
		<tr>
			<?$_POST['assignedRemarks']=$assignedRemarks;?>
			<td></td>
			<td><input type="submit" name="btnUpdate" id="btnUpdate" value="Update" /></td>	
			<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../reviewer/assign_reviewer_change.php?&mn=<?=$matrixNo?>&tid=<?=$thesisId?>&pid=<?=$proposalId?>';" /></td>			
		</tr>	
	</table>
	<br/>
	  </form>
	</body>
</html>




