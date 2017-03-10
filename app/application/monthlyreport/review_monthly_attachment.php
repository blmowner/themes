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
	</head>
	
	<body>

<?php

include("../../../lib/common.php");
checkLogin();

session_start();
$user_id=$_SESSION['user_id'];
$studentMatrixNo=$_GET['mn'];
$pgThesisId=$_GET['tid'];
$pgProposalId=$_GET['pid'];
$progressId=$_GET['prgid'];
$attachmentType=$_GET['at'];

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
						
if(isset($_POST['btnUpdate']) && ($_POST['btnUpdate'] <> ""))
{					
	if (sizeof($_POST['attachment_checkbox'])>0) {
		$curdatetime = date("Y-m-d H:i:s");
		while (list ($key,$val) = @each ($_POST['attachment_checkbox'])) 
		{
			$attachment_id = $_POST['attachment_id'][$val];
			$file_desc = $_POST['file_desc'][$val];
			
			$sql1 = "UPDATE file_upload_progress
			SET fu_document_filedesc = '$file_desc', 
			modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE fu_cd = '$attachment_id'
			AND pg_employee_empid = '$user_id'";
			
			$dba->query($sql1); 
			
			
		}
		$msg[] = "<div class=\"success\"><span>The file description has been updated successfully.</span></div>";
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
			
			$sql1 = "DELETE FROM file_upload_progress
			WHERE fu_cd = '$attachment_id'
			AND pg_employee_empid = '$user_id'";
			
			$dba->query($sql1); 
			
			
		}
		$msg[] = "<div class=\"success\"><span>The attachment has been deleted successfully.</span></div>";
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please tick for which record to delete!</span></div>";
	}
}

if ($progressId != "")	{
	$tmpProgressId = " AND (progress_id = '$progressId' OR progress_id = '' OR progress_id IS NULL)";
}
else {
	$tmpProgressId = "";
}
$sqlUpload="SELECT * 
FROM file_upload_progress 
WHERE pg_proposal_id='$pgProposalId' 
AND pg_employee_empid = '$user_id'
AND attachment_level = 'F' 
AND attachment_type = '$attachmentType' "
.$tmpProgressId.
" AND upload_status IN ('TMP','CFM')
AND status = 'A'
AND archived_status is NULL";			

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
	<link rel="stylesheet" href="../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
	<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
	<script src="../../../lib/js/jquery.min2.js"></script>
    <script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
	<script language="JavaScript" src="../../../lib/js/windowopen.js"></script>
	
</head>
<body>	

<SCRIPT LANGUAGE="JavaScript">
function respConfirm () {
    var confirmSubmit = confirm("Click OK if you confirm to delete else click CANCEL to proceed with the changes.");
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
		<legend><strong>Attachment</strong></legend>
		<table>
			<tr>
				<td><input type="submit" name="submit" value="Add Attachment" onclick="javascript:open_win('review_monthly_upload.php?tid=<?=$pgThesisId;?>&pid=<?=$pgProposalId;?>&mn=<?=$studentMatrixNo;?>&prgid=<?=$progressId;?>&al=F&at=<?=$attachmentType;?>',480,280,0,0,0,1,0,1,1,0,5,'winupload'); "/></td>
			</tr>
		</table>
		<table>
		<tr>							
			<td>Searching Results:- <?=$row_cnt?> record(s) found.</td></td>
		</tr>
		</table>
				<table border="1" cellpadding="3" cellspacing="3" width="100%" id="inputs9" class="thetable">
					<tr>
						<th align="center"><label>Tick</label></td>
						<th align="center"><label>No</label></td>
						<th><span class="labeling">Document Description <span style="color:#FF0000">*</span></span></th>
						<th><span class="labeling">Document Name</span></th>
						<th><span class="labeling">Download</span></th>
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
								<td><input type="text" name="file_desc[]" id="file_desc" size="50" value="<?=$row["fu_document_filedesc"];?>"></input></td>
								<td><label name="file_name[]" size="40" ></label><?=$row["fu_document_filename"];?></td>
								<td><a href="download.php?fc=<?=$row["fu_cd"];?>&al=S" target="_blank" onMouseOver="toolTip('<?=$row["fu_document_filename"];?>', 300)" onMouseOut="toolTip()" align="center">
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
							<td><label>No record found!</label></td>
						</tr>
					</table>
					<?
				}?> 								
				</table>
				<br/>
				

		<br/>
		<table>
			<tr>
				<td>Notes:</td>
			</tr>
			<tr>
				<td>1. Field marks with (<span style="color:#FF0000">*</span>) is compulsory.</td>
			</tr>
			<tr>
				<td>2. Please tick the checkbox before click Update or Delete button.</td>
			</tr>
		</table>
	</fieldset>
	<table>
		<tr>
			<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../monthlyreport/review_progress_detail.php?tid=<?=$pgThesisId?>&pid=<?=$pgProposalId?>&mn=<?=$studentMatrixNo?>&id=<?=$progressId?>';" /></td>		
			<td><input type="submit" name="btnUpdate" value="Update" /></td>
			<td><input type="submit" name="btnDelete" onClick="return respConfirm()" value="Delete" /></td>
		</tr>
	</table>

  </form>
  	<script>
		<?=$jscript;?>
	</script>
</body>
</html>





