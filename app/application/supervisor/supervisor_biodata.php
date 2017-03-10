<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: supervisor_biodata.php
//
// Created by: Zuraimi
// Created Date: 06-May-2015
// Modified by: Zuraimi
// Modified Date: 06-May-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

session_start();
$userid=$_SESSION['user_id'];



if(isset($_POST['btnUpdateBiodata']) && ($_POST['btnUpdateBiodata'] <> ""))
{	
	$theBriefBiodata = $_POST['briefBiodata'];
	$curdatetime = date("Y-m-d H:i:s");
	$msg = array();
	$sql2 = "UPDATE pg_employee
			SET biodata = '$theBriefBiodata', modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE staff_id = '$userid'
			AND status = 'A'";

	$result2 = $dba->query($sql2);
	$dba->next_record();
	
	$msg[] = "<div class=\"success\"><span>Biodata for this staff $staffId has been updated successfully.</span></div>";
}

if(isset($_POST['btnUpdate']) && ($_POST['btnUpdate'] <> ""))
{					
	$msg = array();
	
	
	if (sizeof($_POST['attachment_checkbox'])>0) {
		
		$curdatetime = date("Y-m-d H:i:s");
		while (list ($key,$val) = @each ($_POST['attachment_checkbox'])) 
		{
			$attachment_id = $_POST['attachment_id'][$val];
			$file_desc = $_POST['file_desc'][$val];
			$file_name = $_POST['file_name'][$val];

			if ($file_desc=="") {
				$msg[] = "<div class=\"error\"><span>Please provide description for filename $file_name </span></div>";
			}
			else {
				$sql1 = "UPDATE file_upload_biodata
				SET fu_document_filedesc = '$file_desc', 
				modify_by = '$user_id', modify_date = '$curdatetime'
				WHERE fu_cd = '$attachment_id'
				AND employee_id = '$user_id'";
				
				$dba->query($sql1); 
				$msg[] = "<div class=\"success\"><span>The file description for filename $file_name has been updated successfully.</span></div>";
			}
			
			
		}
		
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please tick for which record to update!</span></div>";
	}
}

if(isset($_POST['btnDelete']) && ($_POST['btnDelete'] <> ""))
{					
	if (sizeof($_POST['attachment_checkbox'])>0) {
		$curdatetime = date("Y-m-d H:i:s");
		while (list ($key,$val) = @each ($_POST['attachment_checkbox'])) 
		{
			$attachment_id = $_POST['attachment_id'][$val];
			
			$sql1 = "DELETE FROM file_upload_biodata
			WHERE fu_cd = '$attachment_id'
			AND employee_id = '$user_id'";
			
			$dba->query($sql1); 
			
			
		}
		$msg[] = "<div class=\"success\"><span>The attachment has been deleted successfully.</span></div>";
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please tick for which record to delete!</span></div>";
	}
}
if(isset($_POST['UpdateEmail']) && ($_POST['UpdateEmail'] <> ""))
{					
	$onoff = $_POST['onoff']; // Check box value = const_id
	//$term = $_POST['term']; // const_term
	$onhid = $_POST['onoffhidden'];
	$theBriefBiodata = $_POST['briefBiodata'];
	$curdatetime = date("Y-m-d H:i:s");

	$lang;
	$sql = "SELECT COUNT(*) as number FROM pg_employee WHERE staff_id = '$user_id'";
    $dbf->query($sql);
	$dbf->next_record();
	$number = $dbf->f('number');
	
	if(empty($number))
	{
		$msg = array();
		$sql2 = "INSERT INTO pg_employee
		(staff_id, biodata, insert_by, insert_date, modify_by, modify_date, status, email_status)
		VALUES ('$user_id', NULL, '$user_id', '$curdatetime', NULL, NULL,'A', NULL)";
	
		$result2 = $dba->query($sql2);
		$dba->next_record();
	
	}
	if($onoff <> ""){
	
		$sqlUpd = "UPDATE pg_employee SET email_status='Y' 
		WHERE staff_id='$onoff'";
		$process = $db->query($sqlUpd);
		//echo "Value = $onoff <br> Update value = Y";
		
	}
	else if($onoff == ""){
		
		$sqlUpd = "UPDATE pg_employee SET email_status='N' 
		WHERE staff_id='$onhid'";
		$process = $db->query($sqlUpd);
		
		//echo "Hidden value = $onhid <br> Update value = N";
	}
}


$sqlUpload="SELECT * FROM file_upload_biodata 
WHERE employee_id = '$user_id'";			

$result = $db->query($sqlUpload); 
$row_cnt = mysql_num_rows($result);

?>

	

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Untitled Document</title>
		<link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
		<link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
		<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
		<link href='../../../theme/UI/css/jquery.iphone.toggle.css' rel='stylesheet'>		
		<script src="../../../lib/js/jquery.min2.js"></script>
		<script src="../../../lib/js/jquery.colorbox.js"></script>
		<script src="../../../lib/js/jquery.mask_input-1.3.js"></script>
		<script src="../../../lib/js/jquery.min2.js"></script>
   		<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
    	<script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
		<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>	
		<script language="JavaScript" src="../../../lib/js/windowopen.js"></script>	
		<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>
		<script src="../../../theme/UI/bower_components/jquery/jquery.min.js"></script>

		<!-- external javascript -->
		
		<script src="../../../theme/UI/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
		
		<!-- library for cookie management -->
		<script src="../../../theme/UI/js/jquery.cookie.js"></script>
		<!-- calender plugin -->
		<script src='../../../theme/UI/bower_components/moment/min/moment.min.js'></script>
		<script src='UI/bower_components/fullcalendar/dist/fullcalendar.min.js'></script>
		<!-- data table plugin -->
		<script src='../../../theme/UI/js/jquery.dataTables.min.js'></script>
		
		<!-- select or dropdown enhancer -->
		<script src="../../../theme/UI/bower_components/chosen/chosen.jquery.min.js"></script>
		<!-- plugin for gallery image view -->
		<script src="../../../theme/UI/bower_components/colorbox/jquery.colorbox-min.js"></script>
		<!-- notification plugin -->
		<script src="../../../theme/UI/js/jquery.noty.js"></script>
		<!-- library for making tables responsive -->
		<script src="../../../theme/UI/bower_components/responsive-tables/responsive-tables.js"></script>
		<!-- tour plugin -->
		<script src="../../../theme/UI/bower_components/bootstrap-tour/build/js/bootstrap-tour.min.js"></script>
		<!-- star rating plugin -->
		<script src="../../../theme/UI/js/jquery.raty.min.js"></script>
		<!-- for iOS style toggle switch -->
		<script src="../../../theme/UI/js/jquery.iphoneyn.toggle.js"></script>
		<!-- autogrowing textarea plugin -->
		<script src="../../../theme/UI/js/jquery.autogrow-textarea.js"></script>
		<!-- multiple file upload plugin -->
		<script src="../../../theme/UI/js/jquery.uploadify-3.1.min.js"></script>
		<!-- history.js for cross-browser state change on ajax -->
		<script src="../../../theme/UI/js/jquery.history.js"></script>
		<!-- application script for Charisma demo -->
		<script src="../../../theme/UI/js/charisma.js"></script>

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
		WHERE staff_id = '$userid'
		AND status = 'A'";

		//echo "sql1 ".$sql1;
		$result1 = $dba->query($sql1);
		$dba->next_record();

		$briefBiodata=$dba->f('biodata');		
			
		?>
		<fieldset>
		<legend><strong>Brief Biodata</strong></legend>
		<table>	
			<tr>
				<td><label>Please enter your biodata here:-</label></td>					
			</tr>
			<tr>
				<td><textarea name="briefBiodata" cols="30" rows="3" id="briefBiodata" class="ckeditor"><?=$briefBiodata?>
				</textarea></td>
			</tr>	
		</table>
		<table>
			<tr>
				<td><input type="submit" name="btnUpdateBiodata" value="Update Biodata" /></td>					
			</tr>
		</table>
		</fieldset>
		<br/>
	<fieldset>		
	<legend><strong>Attachment</strong></legend>
	<table>
		<tr>
			<td><input type="submit" name="submit" value="Add Attachment" onclick="javascript:open_win('supervisor_biodata_upload.php',480,280,0,0,0,1,0,1,1,0,5,'winupload'); "/></td>
		</tr>
	</table>
	<table>
	<tr>							
		<td>Searching Results:- <?=$row_cnt?> record(s) found.</td></td></td>
	</tr>
	</table>
			<table border="1" cellpadding="3" cellspacing="3" width="100%" id="inputs9" class="thetable">
				<tr>
					<th align="center" width="5%"><label>Tick</label></td>
					<th align="center" width="5%"><label>No</label></td>
					<th width="40%"><span class="labeling">Document Description <span style="color:#FF0000">*</span></span></th>
					<th width="55%"><span class="labeling">Document Name</span></th>
					<th width="5%"><span class="labeling">Download</span></th>
				</tr>
					
			<?
			

			if ($row_cnt > 0) {	
				$tmp_no=0;					
				while($row = mysql_fetch_array($result)) 					
				{ 
					
					?><tr>
							<td align="center" width="30"><input type="checkbox" name="attachment_checkbox[]" id="meeting_detail_checkbox" value="<?=$tmp_no;?>" /></td>
							<td align="center"><label><?=$tmp_no+1;?>.</label></td>
							<input type="hidden" name="attachment_id[]" id="attachment_id" value="<?=$row['fu_cd'];?>" />
							<td><input type="text" name="file_desc[]" id="file_desc" size="60" value="<?=$row["fu_document_filedesc"];?>"></input></td>
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
			<br/>
			<table>
			<tr>
				<td><input type="submit" name="btnUpdate" value="Update" /></td>
				<td><input type="submit" name="btnDelete" onClick="return respConfirm()" value="Delete" /></td>
			</tr>
		</table>
		<table>
			<tr>
				<td><span style="color:#FF0000">Notes:</span></td>
			</tr>
			<tr>
				<td>1. The attachment could be your CV or any supporting documents</td>
			</tr>
			<tr>
				<td>2. Field marks with (<span style="color:#FF0000">*</span>) is compulsory.</td>
			</tr>
			<tr>
				<td>3. Please tick the checkbox before click Update or Delete button.</td>
			</tr>
		</table>
	</fieldset>		

<?
	$sql = "SELECT email_status, staff_id FROM pg_employee WHERE staff_id = '$user_id'";
    $dbf->query($sql);
	$dbf->next_record();
	$emailStatus = $dbf->f('email_status');
	$staffID = $dbf->f('staff_id');
	
	$sqlr = "SELECT * FROM base_constant 
	WHERE const_category = 'EMAIL'
	AND const_term IN ('EMAIL_STU_TO_SUP', 'EMAIL_SEN_TO_SUP', 'EMAIL_FAC_TO_SUP')
	AND const_value = 'N'
	ORDER BY const_term DESC";
	$result_sqlr = $dbu->query($sqlr);
	$row_cnt = mysql_num_rows($result_sqlr);
		$constArray = array();
		$constTermArray = array();
		$i = 0;
		$inc = 0;
		$sql = "SELECT * FROM base_constant 
		WHERE const_category = 'EMAIL'
		AND const_term IN ('EMAIL_STU_TO_SUP', 'EMAIL_SEN_TO_SUP', 'EMAIL_FAC_TO_SUP')
		ORDER BY const_term DESC";
		$dbu->query($sql);
		
		do{
			$const_term = $dbu->f('const_term');
			$const_value = $dbu->f('const_value');
			$constArray[$i] = $const_value;
			$constTermArray[$i] = $const_term;
			$i++;
			$inc++;
		
		}while($dbu->next_record());
	
?>
		<fieldset>
		<legend><strong>Email Notification Setting </strong></legend>
	    <table>
		<tr>
			<td>Email receiving status</td>
			<td>
		<?
		if($emailStatus == "Y") {				
			echo "<input name = \"onoff\" id=\"onoff\" data-no-uniform=\"true\" value = ".$staffID." checked=\"checked\" type=\"checkbox\" class=\"iphone-toggle\">";
			echo "<input name = \"onoffhidden\" id=\"onoffhidden\" value = ".$staffID." type=\"hidden\">";	
	
		}
		else if ($emailStatus == "N"){
			echo "<input name = \"onoffhidden\" id=\"onoffhidden\" value = ".$staffID." type=\"hidden\">";	
			echo "<input name = \"onoff\" id=\"onoff\" data-no-uniform=\"true\" value = ".$staffID." type=\"checkbox\" class=\"iphone-toggle\">";										
		}
		else
		{
			echo "<input name = \"onoffhidden\" id=\"onoffhidden\" value = ".$user_id." type=\"hidden\">";	
			echo "<input name = \"onoff\" id=\"onoff\" data-no-uniform=\"true\" value = ".$user_id." type=\"checkbox\" class=\"iphone-toggle\">";								
		}
	?>		</td>
			
		</tr>
		<tr>
			<td colspan="2" align="left"><input class="btn btn-primary btn-xs"  type="submit" value="Update" name="UpdateEmail" />
			</td>
		</tr>
	</table>
		<?
		if($row_cnt >0)
		{
			echo "<span style=\"color:#FF0000\">Notes: </span><br>";
			$no = 0;
			$no1 = $no +1;
			for ($i = 0; $i < $inc; $i++) 
			{	//
				if($constTermArray[$i] == 'EMAIL_STU_TO_SUP' && $constArray[$i] == 'N')
				{
					$no1 = $no +1;
					$no =0+1;
					echo "".$no1.". <label style=\"color:#010000\">Auto email notification to Supervisor trigger by student's action is currently disabled by Admin.</label>";
					echo "<br>";
				}
				else if($constTermArray[$i] == 'EMAIL_SEN_TO_SUP' && $constArray[$i] == 'N')
				{
					$no1 = $no +1;
					echo "".$no1.". <label style=\"color:#010000\">Auto email notification to Supervisor trigger by senate's action is currently disabled by Admin.</label>";
					echo "<br>";
					$no = $no +1;
				}
				else if($constTermArray[$i] == 'EMAIL_FAC_TO_SUP' && $constArray[$i] == 'N')
				{
					$no1 = $no +1;
					echo "".$no1.". <label style=\"color:#010000\">Auto email notification to Supervisor trigger by faculty's action is currently disabled by Admin.</label>";
				}
				
			}
		}
	?>

</fieldset>
	<br />
	<br/>
	</form>
	</body>
</html>