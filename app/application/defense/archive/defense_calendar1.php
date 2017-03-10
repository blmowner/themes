<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: defense_calendar.php
//
// Created by: Zuraimi
// Created Date: 13-July-2015
// Modified by: Zuraimi
// Modified Date: 13-July-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

session_start();
$user_id=$_SESSION['user_id'];
$thesis_id=$_GET['tid'];
$proposal_id=$_GET['pid'];
$defense_id=$_GET['did'];
$referenceNo=$_GET['ref'];

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

				
						
						
if(isset($_POST['btnAdd']) && ($_POST['btnAdd'] <> ""))
{
	$msg = array();
	if (empty($_POST['add_published_date'])) $msg[] = "<div class=\"error\"><span>Please provide Published Date.</span></div>";
	if (empty($_POST['add_publication_title'])) $msg[] = "<div class=\"error\"><span>Please provide Publication Title.</span></div>";
	if (empty($_POST['add_publication_name'])) $msg[] = "<div class=\"error\"><span>Please provide Publication Name.</span></div>";
	if (empty($_POST['add_website'])) $msg[] = "<div class=\"error\"><span>Please provide the website of the publication.</span></div>";
	
	if(empty($msg)) 
	{
		$myAddPublishedDate = $_POST['add_published_date'];
		$myAddPublicationTitle = $_POST['add_publication_title'];
		$myAddPublicationName = $_POST['add_publication_name'];
		$addPublicationType = $_POST['add_publication_type'];
		$myAddWebsite = $_POST['add_website'];
		$myAddCountry = $_POST['add_country'];
		
		$curdatetime = date("Y-m-d H:i:s");
		$publication_id = runnum2('id','pg_defense_publication');	

		if ($defense_id="") {
			$sqlMeeting = "INSERT INTO pg_defense_publication(
			id, pg_defense_id, reference_no, pg_thesis_id, pg_proposal_id, student_matrix_no, 
			published_date,	publication_title, publication_name, publication_type, website, country_id,
			add_status, insert_by, insert_date, modify_by, modify_date)
			VALUES ('$publication_id', '$defense_id', '$referenceNo', '$thesis_id', '$proposal_id', '$user_id',
			STR_TO_DATE('$myAddPublishedDate','%d-%M-%Y'), '$myAddPublicationTitle', '$myAddPublicationName', '$addPublicationType',
			'$myAddWebsite', '$myAddCountry',
			'TMP', '$user_id', '$curdatetime', '$user_id', '$curdatetime')";

			$db_klas2->query($sqlMeeting); 
		}
		else {
			$sqlMeeting = "INSERT INTO pg_defense_publication(
			id, pg_defense_id, reference_no, pg_thesis_id, pg_proposal_id, student_matrix_no, 
			published_date,	publication_title, publication_name, publication_type, website, country_id,
			add_status, insert_by, insert_date, modify_by, modify_date)
			VALUES ('$publication_id', null, null, '$thesis_id', '$proposal_id', '$user_id',
			STR_TO_DATE('$myAddPublishedDate','%d-%M-%Y'), '$myAddPublicationTitle', '$myAddPublicationName', '$addPublicationType',
			'$myAddWebsite', '$myAddCountry',
			'TMP', '$user_id', '$curdatetime', '$user_id', '$curdatetime')";

			$db_klas2->query($sqlMeeting); 
		}
		
		$msg[] = "<div class=\"success\"><span>Publication detail has been added successfully.</span></div>";
	}
	unset($_POST);
}

if(isset($_POST['btnUpdate']) && ($_POST['btnUpdate'] <> ""))
{					
	$tmpDefenseCheckBox = $_POST['defense_checkbox'];
	$no=1;
	while (list ($key,$val) = @each ($tmpDefenseCheckBox)) 
	{
		$no=$no+$val;
		if (empty($_POST['published_date'][$val])) $msg[] = "<div class=\"error\"><span>Please provide Published Date for record no $no.</span></div>";
		if (empty($_POST['publication_title'][$val])) $msg[] = "<div class=\"error\"><span>Please provide Publication Title for record no $no.</span></div>";
		if (empty($_POST['publication_name'][$val])) $msg[] = "<div class=\"error\"><span>Please provide Publication Name for record no $no.</span></div>";
		if (empty($_POST['website'][$val])) $msg[] = "<div class=\"error\"><span>Please provide the website of the publication for record no $no.</span></div>";
	}
	
	if (sizeof($_POST['defense_checkbox'])>0) {
		$curdatetime = date("Y-m-d H:i:s");
		while (list ($key,$val) = @each ($_POST['defense_checkbox'])) 
		{
			$publication_id = $_POST['publication_id'][$val];
			$published_date = $_POST['published_date'][$val];
			$publication_title = $_POST['publication_title'][$val];
			$publication_name = $_POST['publication_name'][$val];
			$publication_type = $_POST['publication_type'][$val];
			$website = $_POST['website'][$val];
			$country = $_POST['country'][$val];
			
			$sql1 = "UPDATE pg_defense_publication
			SET published_date = STR_TO_DATE('$published_date','%d-%b-%Y'), 
			publication_title = '$publication_title',
			publication_name = '$publication_name',
			publication_type = '$publication_type',
			website = '$website',
			country_id = '$country',
			modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE id = '$publication_id'
			AND student_matrix_no = '$user_id'";
			
			$dba->query($sql1); 
			
			
		}
		$msg[] = "<div class=\"success\"><span>The selected Publication detail has been updated successfully.</span></div>";
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please tick which Publication record to update!</span></div>";
	}
	unset($_POST);
}

if(isset($_POST['btnDelete']) && ($_POST['btnDelete'] <> ""))
{					
	if (sizeof($_POST['defense_checkbox'])>0) {
		$curdatetime = date("Y-m-d H:i:s");
		while (list ($key,$val) = @each ($_POST['defense_checkbox'])) 
		{
			$publication_id = $_POST['publication_id'][$val];
			
			$sql1 = "DELETE FROM pg_defense_publication
			WHERE id = '$publication_id'
			AND student_matrix_no = '$user_id'";
			
			$dba->query($sql1); 
			
			
		}
		$msg[] = "<div class=\"success\"><span>The selected Publication detail has been deleted successfully.</span></div>";
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please tick which Publication record to delete!</span></div>";
	}
	unset($_POST);
}

		
		
$sqlPublication="SELECT id, DATE_FORMAT(published_date,'%d-%b-%Y') as published_date, 
publication_title, publication_name, publication_type, website, country_id
FROM  pg_defense_publication 
WHERE (pg_defense_id IS NULL OR pg_defense_id = '$defense_id')
AND pg_proposal_id='$proposal_id'
AND pg_thesis_id = '$thesis_id'
AND (reference_no = '$referenceNo' OR reference_no IS NULL)
AND student_matrix_no = '$user_id' 
ORDER BY published_date DESC ";

$result = $db->query($sqlPublication); 
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
	<script src="../../../lib/js/jquery.colorbox.js"></script>
	
	    <script>
  
		$(document).ready(function(){
		      //$(".select_user").colorbox({width:"60%", height:"40%", iframe:true});
              
              $.fn.getParameterValue = function(data,data2) {
                  //alert(data + ' - ' + data2);
                  document.form1.staff_id.value = data;
				  document.form1.staff_name.value = data2;
                };
              
               $(".select_student").colorbox({width:"60%", height:"90%", iframe:true,          
               onClosed:function(){ 
                //location.reload(true); //uncomment this line if you want to refresh the page when child close
                                
                } }); 
				
				$.fn.getParameterValue2 = function(data,data2) {
                  //alert(data);
				  //alert(data2);
                  document.form1.staff_id.value = data;
				  document.form1.staff_name.value = data2;
				  //$("#"+data2).val(data);
				  //$("#"+data4).val(data3);

                };
              
               $(".select_student").colorbox({width:"60%", height:"90%", iframe:true,          
               onClosed:function(){ 
                //location.reload(true); //uncomment this line if you want to refresh the page when child close
                                
                } }); 
          });
	</script>

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
		<legend><strong>Defence Calendar Setup</strong></legend>

		<?php ?> <p>
		<table>
			<tr>
				<td><label>Defence End Date <span style="color:#FF0000">*</span></label></td>
				<td><input name="add_defense_sdate" type="text" id="add_defense_sdate" size="10" value="<?=$_POST['add_defense_sdate']?>"readonly=""></td>
				<?	$jscript .= "\n" . '$( "#add_defense_sdate" ).datepicker({
												changeMonth: true,
												changeYear: true,
												yearRange: \'-100:+0\',
												dateFormat: \'dd-M-yy\'
											});';
					 
				?>
			</tr>
			<tr>
				<td><label>Defence Start Date <span style="color:#FF0000">*</span></label></td>
				<td><input name="add_defense_edate" type="text" id="add_defense_edate" size="10" value="<?=$_POST['add_defense_edate']?>"readonly=""></td>
				<?	$jscript .= "\n" . '$( "#add_defense_edate" ).datepicker({
												changeMonth: true,
												changeYear: true,
												yearRange: \'-100:+0\',
												dateFormat: \'dd-M-yy\'
											});';
					 
				?>
			</tr>
			<tr>
				<td><label>Venue <span style="color:#FF0000">*</span></label></td>
				<td><textarea name="add_venue" cols="40" rows="2" id="add_venue" type="text" value="<?=$_POST['add_venue']?>"></textarea></td>
			</tr>
			<tr>
				<td><label>Remarks <span style="color:#FF0000">*</span></label></td>
				<td><input name="add_publication_name" type="text" id="add_remarks" size="50" value="<?=$_POST['add_remarks']?>"></td>
			</tr>	
			<tr>
				<td><label>Student <span style="color:#FF0000">*</span></label></td>
				<td><input name="add_lecturer_name" type="text" id="add_lecturer_name" size="50" readonly=""/>
				<a class='select_student' href="../../application/defense/select_student.php">[Select]</a>
				<input id ="add_staff_id" type="hidden" size="100" name="add_staff_id" /></td>
			</tr>			
		</table>
		<table>
			<tr>
				<td><input type="submit" name="btnAdd" value="Add" /></td>
			</tr>
		</table>

		</p><?php ?>
		<table>
		<tr>							
			<td>Searching Results:- <?=$row_cnt?> record(s) found.</td></td>
		</tr>
		</table>
		<?if ($row_cnt <= 0) {?>
			<div id = "tabledisplay" style="overflow:auto; height:80px;">
		<?}
		else if ($row_cnt <= 1) {?>
			<div id = "tabledisplay" style="overflow:auto; height:100px;">
		<?}
		else if ($row_cnt <= 2) {?>
			<div id = "tabledisplay" style="overflow:auto; height:150px;">
		<?}
		else if ($row_cnt <= 3) {
			?>
			<div id = "tabledisplay" style="overflow:auto; height:200px;">
			<?
		}
		else {
			?>
			<div id = "tabledisplay" style="overflow:auto; height:300px;">
			<?
		}?>		
		<table border="1" cellpadding="3" cellspacing="3" width="90%" id="inputs10" class="thetable">
			  <tr align="left">
				<th align="center" width="5%"><label>Tick</label></td>
				<th align="center" width="5%"><label>No</label></td>
				<th align="center" width="10%"><label>Defence Start Date <span style="color:#FF0000">*</span></label></th>
				<th align="center" width="10%"><label>Defence End Date <span style="color:#FF0000">*</span></label></th>
				<th align="left" width="15%"><label>Venue <span style="color:#FF0000">*</span></label></th>
				<th align="left" width="20%"><label>Remarks <span style="color:#FF0000">*</span></label></th>
				<th align="left" width="15%"><label>Student <span style="color:#FF0000">*</span></label></th>
				<th align="left" width="20%"><label>Evaluation Committee <span style="color:#FF0000">*</span></label></th>
			  </tr>

			<?php
			if ($row_cnt > 0) {
				
				$tmp_no = 0;
				while($row = mysql_fetch_array($result)) 					
				{ 
					?><tr>
						<td align="center"><input type="checkbox" name="defense_checkbox[]" id="defense_checkbox" value="<?=$tmp_no;?>" /></td>
						<td align="center"><label><?=$tmp_no+1;?>.</label></td>
						<td align="center"><input type="text" name="defence_sdate[]" id="defence_sdate<?=$tmp_no;?>" size="10" value="<?=$row["defence_sdate"];?>" readonly=""></input></td>
						<?	$jscript .= "\n" . '$( "#defence_sdate' . $tmp_no . '" ).datepicker({
											changeMonth: true,
											changeYear: true,
											yearRange: \'-100:+0\',
											dateFormat: \'dd-M-yy\'
										});';
				 
						?>
						<td align="center"><input type="text" name="defence_edate[]" id="defence_edate<?=$tmp_no;?>" size="10" value="<?=$row["defence_edate"];?>" readonly=""></input></td>
						<?	$jscript .= "\n" . '$( "#defence_edate' . $tmp_no . '" ).datepicker({
											changeMonth: true,
											changeYear: true,
											yearRange: \'-100:+0\',
											dateFormat: \'dd-M-yy\'
										});';
				 
						?>
						<input type="hidden" name="defense_calendar_id[]" id="defense_calendar_id" value="<?=$row['id'];?>" />
						<td><input name="venue[]" type="text" id="venue" value="<?=$row["venue"];?>" size="50"></input>
						<input type="text" name="remarks[]" id="remarks" value="<?=$row["remarks"];?>"></input></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					<?
					$tmp_no++;}
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
				<td><span style="color:#FF0000">Notes:</span></td>
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
			<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../defense/edit_defense.php?tid=<?=$thesis_id?>&pid=<?=$proposal_id?>&ref=<?=$referenceNo?>';" /></input></td>		
			<td><input type="submit" name="btnUpdate" value="Update" /></input></td>
			<td><input type="submit" name="btnDelete" onClick="return respConfirm()" value="Delete" /></input></td>
		</tr>
	</table>

  </form>
  	<script>
		<?=$jscript;?>
	</script>
</body>
</html>





