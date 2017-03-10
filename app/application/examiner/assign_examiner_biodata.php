<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: assign_examiner_biodata.php
//
// Created by: Zuraimi
// Created Date: 12-Jun-2015
// Modified by: Zuraimi
// Modified Date: 12-Jun-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

session_start();
$userid=$_SESSION['user_id'];
$thesisId=$_REQUEST['tid'];
$proposalId=$_REQUEST['pid'];
$staffId=$_REQUEST['sid'];
$staffName=$_REQUEST['ename'];
$matrixNo=$_REQUEST['mn'];
$studentName=$_REQUEST['sname'];
$examinerId=$_REQUEST['id'];




if(isset($_POST['btnUpdate']) && ($_POST['btnUpdate'] <> ""))
{	
	$theBriefBiodata = $_POST['briefBiodata'];
	$curdatetime = date("Y-m-d H:i:s");
	$msg = array();
	
	$sql1 = "SELECT staff_id
	FROM pg_employee
	WHERE staff_id = '$staffId'
	AND status = 'A'";
	
	$result1 = $dba->query($sql1);
	$dba->next_record();
	
	$row_cnt1 = mysql_num_rows($result1);
	
	if ($row_cnt1 > 0 ) {
		$sql2 = "UPDATE pg_employee
		SET biodata = '$theBriefBiodata', modify_by = '$userid', modify_date = '$curdatetime'
		WHERE staff_id = '$staffId'
		AND status = 'A'";

		$result2 = $dba->query($sql2);
		$dba->next_record();
	}
	else {
		$sql3 = "INSERT INTO pg_employee
		(staff_id, biodata, insert_by, insert_date, modify_by, modify_date, status)
		VALUES ('$staffId','$theBriefBiodata','$userid','$curdatetime','$userid','$curdatetime','A')";
		
		$result3 = $dba->query($sql3);
		$dba->next_record();
	}
	
	$msg[] = "<div class=\"success\"><span>Biodata for this staff $staffId has been updated successfully.</span></div>";
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
		<script src="../../../lib/js/jquery.min2.js"></script>
		<script src="../../../lib/js/jquery.colorbox.js"></script>
		<script src="../../../lib/js/jquery.mask_input-1.3.js"></script>
		<script src="../../../lib/js/jquery.min2.js"></script>
   		<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
    	<script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
		<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>	
		<script language="JavaScript" src="../../../lib/js/windowopen.js"></script>	
		<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>
	</head>
	<body>
	<?php
    if(!empty($msg)) 
	{
        foreach($msg as $err) 
		{
            echo $err;
        }
    }
?>
		<form id="form2" name="form2" method="post" enctype="multipart/form-data">
			<?			
			$sql1 = "SELECT biodata
			FROM pg_employee
			WHERE staff_id = '$staffId'";

			//echo "sql1 ".$sql1;
			$result1 = $db->query($sql1);
			$db->next_record();

			$briefBiodata=$db->f('biodata');		
				
			?>
			<fieldset>
			<legend><strong>Brief Biodata</strong></legend>
			<table>
				<tr>
					<td><label>Staff ID</label></td>
					<td>:</td>
					<td><label type="text" name="examinerStaffId" ><?=$staffId?></label></td>
				</tr>
				<tr>
					<td><label>Examiner Name</label></td>
					<td>:</td>
					<td><label type="text" name="examinerName" ><?=$staffName?></label></td>
				</tr>
				<tr>
				</tr>
			</table>
			<table>	
				<tr>
					<td><br/><label>You may update this biodata if it is required:-</label></td>					
				</tr>
				<tr>
					<td><textarea name="briefBiodata" cols="30" rows="3" id="briefBiodata" class="ckeditor"><?=$briefBiodata?></textarea></td>														
				</tr>	
			</table>
			<br/>
			<table>
				<tr>
					<td><input type="submit" name="btnUpdate" value="Update" /></td>
					<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../examiner/assign_examiner_change.php?tid=<?=$thesisId;?>&pid=<?=$proposalId;?>&mn=<?=$matrixNo;?>&sname=<?=$studentName;?>';" /></td>
				</tr>
			</table>
			<table>	
				<tr>
					<td><br/><label><strong>Attachment Document</strong> (i.e CV, supporting documents)</label></td>					
				</tr>
			</table>
			<table border="1" cellpadding="3" cellspacing="3" width="100%" id="inputs9" class="thetable">
				<tr>
					<th align="center" width="5%"><label>No</label></td>
					<th width="40%"><span class="labeling">Document Description</th>
					<th width="55%"><span class="labeling">Document Name</span></th>
					<th width="5%"><span class="labeling">Download</span></th>
				</tr>
				<?				
				$sqlUpload="SELECT * FROM file_upload_biodata 
				WHERE employee_id = '$staffId'";			

				$result = $db->query($sqlUpload); 
				$row_cnt = mysql_num_rows($result);

				if ($row_cnt > 0) {	
					$tmp_no=0;					
					while($row = mysql_fetch_array($result)) 					
					{ 
						
						?><tr>
								<td align="center"><label><?=$tmp_no+1;?>.</label></td>
								<input type="hidden" name="attachment_id[]" id="attachment_id" value="<?=$row['fu_cd'];?>" />
								<td><label name="file_desc[]" id="file_desc" size="60" ></label><?=$row["fu_document_filedesc"];?></td>
								<input type="hidden" name="file_name[]" id="file_name" size="60" value="<?=$row["fu_document_filename"];?>"></input>
								<td><label name="file_name[]" size="40" ></label><?=$row["fu_document_filename"];?></td>
								<td><a href="download.php?fc=<?=$row["fu_cd"];?>" target="_blank" onMouseOver="toolTip('<?=$row["fu_document_filename"];?>', 300)" onMouseOut="toolTip()" align="center">
								<img src="../images/view_doc.jpg" width="20" height="19" style="border:0px;" title="View document"></a>
								</td>
							</tr>										
					<?
					$tmp_no++;
					}
				}
				else {
					?>
					<table>
						<tr>
							<td><label>No attachment found!</label></td>
						</tr>
					</table>
					<?
				}?> 
			</table>	
			</fieldset>
		</form>
	</body>
</html>