<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: new_work_publication.php
//
// Created by: Zuraimi
// Created Date: 18-August-2015
// Modified by: Zuraimi
// Modified Date: 18-August-2015
//
//**************************************************************************************

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Untitled Document</title>
		<link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
		<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
		<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
		<script src="../../../lib/js/jquery.min2.js"></script>
		<script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
		<script language="JavaScript" src="../../../lib/js/windowopen.js"></script>
		<script language="JavaScript" type="text/javascript" src="../../../lib/js/tooltip.js"></script>
	</head>
	
	<body>

<?php

include("../../../lib/common.php");
checkLogin();

session_start();
$user_id=$_SESSION['user_id'];
$thesis_id=$_GET['tid'];
$proposal_id=$_GET['pid'];
$defense_id=$_GET['pgid'];
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
		$myAddIssnNo = $_POST['add_issn_no'];
		$myAddVolume = $_POST['add_volume'];
		$myAddIssue = $_POST['add_issue'];
		$myAddPublishedDate = $_POST['add_published_date'];
		$myAddPublicationTitle = $_POST['add_publication_title'];
		$myAddPublicationId = $_POST['add_publication_id'];
		$addPublicationTypeId = $_POST['add_publication_type_id'];
		$myAddWebsite = $_POST['add_website'];
		$myAddCountryId = $_POST['add_country_id'];
		
		$curdatetime = date("Y-m-d H:i:s");
		$publication_id = runnum2('id','pg_defense_publication');	

		if ($defense_id!="") { //Tertinggal symbol (!)
			$sqlMeeting = "INSERT INTO pg_defense_publication(
			id, pg_defense_id, reference_no, pg_thesis_id, pg_proposal_id, student_matrix_no, issn_no, volume, issue,
			published_date,	publication_title, ref_session_type_id, publication_id, publication_type, website, country_id,
			add_status, insert_by, insert_date, modify_by, modify_date)
			VALUES ('$publication_id', '$defense_id', '$referenceNo', '$thesis_id', '$proposal_id', '$user_id',
			'$myAddIssnNo','$myAddVolume','$myAddIssue',
			STR_TO_DATE('$myAddPublishedDate','%d-%M-%Y'), '$myAddPublicationTitle', 'WCO', '$myAddPublicationId', '$addPublicationTypeId',
			'$myAddWebsite', '$myAddCountryId',
			'TMP', '$user_id', '$curdatetime', '$user_id', '$curdatetime')";

			$db_klas2->query($sqlMeeting); 
		}
		else {
			$sqlMeeting = "INSERT INTO pg_defense_publication(
			id, pg_defense_id, reference_no, pg_thesis_id, pg_proposal_id, student_matrix_no, issn_no, volume, issue,
			published_date,	publication_title, ref_session_type_id, publication_id, publication_type, website, country_id,
			add_status, insert_by, insert_date, modify_by, modify_date)
			VALUES ('$publication_id', null, null, '$thesis_id', '$proposal_id', '$user_id',
			'$myAddIssnNo','$myAddVolume','$myAddIssue',
			STR_TO_DATE('$myAddPublishedDate','%d-%M-%Y'), '$myAddPublicationTitle', 'WCO', '$myAddPublicationId', '$addPublicationTypeId',
			'$myAddWebsite', '$myAddCountryId',
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
		if(empty($msg)) 
		{
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
				AND ref_session_type_id = 'WCO'
				AND student_matrix_no = '$user_id'";
				
				$dba->query($sql1); 
				
				
			}
			$msg[] = "<div class=\"success\"><span>The selected Publication detail has been updated successfully.</span></div>";
		}
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
		
$sqlPublication="SELECT a.id, DATE_FORMAT(a.published_date,'%d-%b-%Y') as published_date, 
a.publication_title, a.publication_id, a.publication_type, a.website, a.country_id,
b.publisher_name as publication_name
FROM  pg_defense_publication a
LEFT JOIN ref_publisher b ON (b.id = a.publication_id)
WHERE (a.pg_defense_id IS NULL OR a.pg_defense_id = '$defense_id')
AND a.pg_proposal_id='$proposal_id'
AND a.pg_thesis_id = '$thesis_id'
AND (a.reference_no = '$referenceNo' OR a.reference_no IS NULL)
AND a.student_matrix_no = '$user_id' 
AND ref_session_type_id = 'WCO'
ORDER BY a.published_date DESC ";

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
		<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
		<script src="../../../lib/js/jquery.min2.js"></script>
		<script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
		<script language="JavaScript" src="../../../lib/js/windowopen.js"></script>
		<script src="../../../lib/js/jquery.colorbox.js"></script>
		<script language="JavaScript" type="text/javascript" src="../../../lib/js/tooltip.js"></script>
	
</head>
<body>
<script>
	$(document).ready(function(){
		  
		  $.fn.getParameterValue = function(issnNo, volume, issue, publishedDate, title, publisherId, publisherName, publicationTypeId, publicationTypeDesc, website, countryId, countryName) {
			  //alert(matrixNo + ' - ' + studentName);
			  document.form1.add_issn_no.value = issnNo;
			  document.form1.add_volume.value = volume;
			  document.form1.add_issue.value = issue;
			  document.form1.add_published_date.value = publishedDate;
			  document.form1.add_publication_title.value = title;
			  document.form1.add_publication_id.value = publisherId;
			  document.form1.add_publication_name.value = publisherName;
			  document.form1.add_publication_type_id.value = publicationTypeId;
			  document.form1.add_publication_type_desc.value = publicationTypeDesc;
			  document.form1.add_website.value = website;
			  document.form1.add_country_id.value = countryId;
			  document.form1.add_country_name.value = countryName;
			};
		  
		   $(".select_publication").colorbox({width:"90%", height:"100%", iframe:true,          
		   onClosed:function(){ 
			//location.reload(true); //uncomment this line if you want to refresh the page when child close
							
			} }); 

	  });
</script>
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
		<legend><strong>Publications</strong></legend>

		<?php ?> <p>
		<table>
			<tr>
				<td><a class='select_publication' href="../../application/work/select_publication.php">[Select From Manage Publication]</a></td>
			</tr>
		</table>
		<table>
			<tr>
				<td><label>ISSN No.</label></td>
				<td><input name="add_issn_no" id="add_issn_no" type="text" value="<?=$_POST['add_issn_no']?>" readonly=""></td>
			</tr>
			<tr>
				<td><label>Volume</label></td>
				<td><input name="add_volume" id="add_volume" type="text" value="<?=$_POST['add_volume']?>" readonly=""></td>
			</tr>
			<tr>
				<td><label>Issue</label></td>
				<td><input name="add_issue" id="add_issue" type="text" value="<?=$_POST['add_issue']?>" readonly=""></td>
			</tr>
			<tr>
				<td><label>Published Date</label></td>
				<td><input name="add_published_date" type="text" id="add_published_date" size="10" value="<?=$_POST['add_published_date']?>"readonly="" ></td>				
			</tr>
			<tr>
				<td><label>Publication Title</label></td>
				<td><textarea name="add_publication_title" cols="40" rows="2" id="add_publication_title" type="text" value="<?=$_POST['add_publication_title']?>" readonly=""></textarea></td>
			</tr>
			<tr>
				<td><label>Publication Name</label></td>
				<td><input name="add_publication_name" type="text" id="add_publication_name" size="50" value="<?=$_POST['add_publication_name']?>" readonly=""></td>
				<input name="add_publication_id" type="hidden" id="add_publication_id" size="50" value="<?=$_POST['add_publication_id']?>">
			</tr>
			<tr>
				<td><label>Type of Publication</label></td>
				<td><input name="add_publication_type_desc" type="text" id="add_publication_type_desc" size="50" value="<?=$_POST['add_publication_type_desc']?>" readonly=""></td>
				<input name="add_publication_type_id" type="hidden" id="add_publication_type_id" value="<?=$_POST['add_publication_type_id']?>">
			</tr>
			<tr>
				<td><label>Website</label></td>
				<td><input name="add_website" type="text" id="add_website" size="50" value="<?=$_POST['add_website']?>" readonly=""></td>
			</tr>
				<td><label>Country</td>
				<td><input name="add_country_name" type="text" id="add_country_name" size="50" value="<?=$_POST['add_country_name']?>" readonly=""></td>
				<td><input name="add_country_id" type="hidden" id="add_country_id" size="50" value="<?=$_POST['add_country_id']?>"></td>
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
		<?if ($row_cnt <= 1) {?>
			<div id = "tabledisplay" style="overflow:auto; height:100px;">
		<?}
		else if ($row_cnt <= 2) {?>
			<div id = "tabledisplay" style="overflow:auto; height:200px;">
		<?}
		else if ($row_cnt <= 3) {
			?>
			<div id = "tabledisplay" style="overflow:auto; height:250px;">
			<?
		}
		else {
			?>
			<div id = "tabledisplay" style="overflow:auto; height:300px;">
			<?
		}?>		
		<table border="1" cellpadding="3" cellspacing="3" width="100%" id="inputs10" class="thetable">
			  <tr align="left">
				<th align="center" width="5%"><label>Tick</label></td>
				<th align="center" width="5%"><label>No</label></td>
				<th align="left" width="10%"><label>Published Date</label></th>
				<th align="left" width="80%"><label>Publication Detail</label></th>
			  </tr>

			<?php
			if ($row_cnt > 0) {
				
				$tmp_no = 0;
				while($row = mysql_fetch_array($result)) 					
				{ 
					?><tr>
						<input type="hidden" name="publication_id[]" id="publication_id" value="<?=$row['id']?>" />
						<td align="center"><input type="checkbox" name="defense_checkbox[]" id="defense_checkbox" value="<?=$tmp_no;?>" /></td>
						<td align="center"><label><?=$tmp_no+1;?>.</label></td>
						<td align="center"><label><?=$row["published_date"]?></td>
						<td>
						<table width="100%" class="idd">
							<tr>
								<td width="15%" style="background-color: rgba(105, 162, 255, 0.7);">Title</td>
								<td width="85%" style="background-color: rgba(0, 0, 0, 0.1) "><label><?=$row["publication_title"];?></label></td>
							</tr>
							<tr>
								<td style="background-color: rgba(105, 162, 255, 0.7);">Publication Name</td>
								<td style="background-color: rgba(0, 0, 0, 0.1) "><label><?=$row["publication_name"]?></label></td>
							</tr>
								<?$sql = "SELECT id, description
								FROM ref_publication_type
								WHERE id = '".$row['publication_type']."' 
								AND  status = 'A'
								ORDER BY description";

								$result_sql = $dbb->query($sql);
								$dbb->next_record();
								$publicationTypeDesc = $dbb->f('description');
								$row_cnt = mysql_num_rows($result_sql);?>
							<tr>
								<td style="background-color: rgba(105, 162, 255, 0.7);">Publication Type</td>
								<td style="background-color: rgba(0, 0, 0, 0.1) "><label><?=$publicationTypeDesc?></label></td>
							</tr>
							<tr>
								<td style="background-color: rgba(105, 162, 255, 0.7);">Website</td>
								<td style="background-color: rgba(0, 0, 0, 0.1) "><label><?=$row["website"]?></label></td>
							</tr>
								<?
								$sql = "SELECT id, description
								FROM ref_country
								WHERE id = '".$row['country_id']."'
								AND status = 'A'
								ORDER BY description";

								$result_sql = $dbb->query($sql);
								$dbb->next_record();
								$countryDesc = $dbb->f('description');
								$row_cnt = mysql_num_rows($result_sql);?>
							<tr>
								<td style="background-color: rgba(105, 162, 255, 0.7);">Country</td>
								<td style="background-color: rgba(0, 0, 0, 0.1) "><label><?=$countryDesc?></label></td>
							</tr>
						</table>						
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
			<td><input type="submit" name="btnDelete" onClick="return respConfirm()" value="Delete" /></input></td>
			<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../work/new_work.php?tid=<?=$thesis_id?>&pid=<?=$proposal_id?>&ref=<?=$referenceNo?>';" /></input></td>		
		</tr>
	</table>

  </form>
  	<script>
		<?=$jscript;?>
	</script>
</body>
</html>