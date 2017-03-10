<?php
    include("../../../lib/common.php");
    checkLogin();
    
	$id = $_GET['id'];

	$sqlannounce = "SELECT *, DATE_FORMAT(start_date,'%d-%M-%Y') AS start_date, DATE_FORMAT(end_date,'%d-%M-%Y') AS end_date 
	FROM pg_announcement
	WHERE status = 'A' 
	AND id = '$id'";
	$resultA = $dbf->query($sqlannounce);
	
	
	$dbf->next_record();
	
	$titleA =$dbf->f('title');
	$announcement =$dbf->f('announcement');
	$start_date=$dbf->f('start_date');
	$end_date=$dbf->f('end_date');
	$publish_by=$dbf->f('announcer');
	$ref_no=$dbf->f('ref_no');
	$reviewed_by = $dbf->f('reviewed_by');
	$reviewed_status = $dbf->f('review_status');
	
function runnum($column_name, $tblname) 
{ 
	global $db_klas2;
	
	$run_start = "0001";
	
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
	
function runnum2($column_name, $tblname) 
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

	
    //$level = 0; /* set the menu level = 0 */


if($_POST['btnsave'] <> "") 
{
	$announ = $_REQUEST['announ'];
	$title = $_REQUEST['title'];
	$start_date = $_REQUEST['start_date'];
	$end_date = $_REQUEST['end_date'];
	$id = runnum('id','pg_announcement');
	$Aid = $_GET['id'];
	$trackId = "R".runnum2('id','pg_announcement_tracking');
	$trackId1 = "R".runnum2('id','pg_announcement_tracking');

	$publishBy = $_REQUEST['publishBy'];
	$refNo = $_REQUEST['ref_no'];
	
	$curdatetime = date("Y-m-d H:i:s");
	$curdatetime3 = date("Y-m-d H:i:s");
	$curdatetime4 = date("Y-m-d H:i:s");


	$start = date("Y-m-d");	
	$end = date("Y-m-d");
		
	$startDate = strtotime($start_date);
	$newStartDate = date ( 'Y-m-d' , $startDate );	

	$endDate = strtotime($end_date);
	$newEndDate = date ( 'Y-m-d' , $endDate );		
	/*$newdate = strtotime ( '+7 days' , strtotime ( $curdatetime ) ) ;
	$newdate = date ( 'Y-m-d' , $newdate );	*/

	if(empty($_REQUEST['announ'])) $msg[] = "<div class=\"error\"><span>Please enter the Annoucement as required below.</span></div>";
	if(empty($_POST['start_date'])) $msg[] = "<div class=\"error\"><span>Please enter the Start date as required below.</span></div>";
	if(empty($_POST['end_date'])) $msg[] = "<div class=\"error\"><span>Please enter the Expected End date as required below.</span></div>";
	if($newEndDate < $newStartDate) 
	{
		$sqlannounce = "SELECT *, DATE_FORMAT(start_date,'%d-%M-%Y') AS start_date, DATE_FORMAT(end_date,'%d-%M-%Y') AS end_date 
		FROM pg_announcement
		WHERE status = 'A' 
		AND id = '$Aid'";
		$resultA = $dbf->query($sqlannounce);
		
		
		$dbf->next_record();
		
		$titleA =$dbf->f('title');
		$announcement =$dbf->f('announcement');
		$start_date=$dbf->f('start_date');
		$end_date=$dbf->f('end_date');

		$msg[] = "<div class=\"error\"><span>End date cannot be before than start date.</span></div>";
	
	}
	if($newStartDate < $start) 
	{
		$sqlannounce = "SELECT *, DATE_FORMAT(start_date,'%d-%M-%Y') AS start_date, DATE_FORMAT(end_date,'%d-%M-%Y') AS end_date 
		FROM pg_announcement
		WHERE status = 'A' 
		AND id = '$Aid'";
		$resultA = $dbf->query($sqlannounce);
		
		
		$dbf->next_record();
		
		$titleA =$dbf->f('title');
		$announcement =$dbf->f('announcement');
		$start_date=$dbf->f('start_date');
		$end_date=$dbf->f('end_date');

		$msg[] = "<div class=\"error\"><span>Start date cannot be before than current date.</span></div>"; 
	}

	if(empty($msg)) 
	{
		if($reviewed_by == $user_id && $reviewed_status == 'R')
		{
			$sqlUpd = "UPDATE pg_announcement SET title = '$title', announcement = '$announ', start_date = '$newStartDate', end_date = '$newEndDate', 
			modify_by = '$user_id', modify_date = '$curdatetime', reviewed_date = '$curdatetime4'
			WHERE id='$Aid'";
			$process = $db->query($sqlUpd);

			$sqltracking = "INSERT INTO pg_announcement_tracking
					(`id`,`action`,`action_by`,`query_track`,`announcement_id`,`status`, action_date , display_status, remarks)
					VALUES
					('$trackId', 'E', '$user_id', '".mysql_escape_string($sqlUpd)."' , '$Aid', 'A', '$curdatetime' , 'D', 'Edit by Admin ($user_id)')";
					
			$resultA1 = $dbf->query($sqltracking);
		
		}
		else
		{
			$sqlUpd = "UPDATE pg_announcement SET review_status= 'BR', reviewed_by = '$user_id',  modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE id='$Aid'";				
		
			$process = $db->query($sqlUpd);

			$sqltracking = "INSERT INTO pg_announcement_tracking
					(`id`,`action`,`action_by`,`query_track`,`announcement_id`,`status`, action_date , display_status, remarks)
					VALUES
					('$trackId', 'E', '$user_id', '".mysql_escape_string($sqlUpd)."' , '$Aid', 'A', '$curdatetime' , 'D', 'Edit by Admin<br>($user_id)')";
					
			$resultA1 = $dbf->query($sqltracking);
								
			$sqlannounce = "INSERT INTO pg_announcement 
					(`id`,`title`,`announcement`,`insert_by`,`insert_date`, `status`, start_date, end_date, publish_status, review_status, reviewed_by, announcer, ref_no,
					reviewed_date)
					VALUES
					('$id', '$title', '".mysql_escape_string($announ)."', '$user_id' , '$curdatetime', 'A', '$newStartDate', '$newEndDate', 'S', 'R', 
					'$user_id', '$publishBy', '$refNo', '$curdatetime4')";		
						
			$resultA = $dbf->query($sqlannounce);

			$sqltracking = "INSERT INTO pg_announcement_tracking
					(`id`,`action`,`action_by`,`query_track`,`announcement_id`,`status`, action_date , display_status, remarks)
					VALUES
					('$trackId1', 'I', '$user_id', '".mysql_escape_string($sqlannounce)."' , '$id', 'A', '$curdatetime' , 'D', 'Edit by Admin(New)<br>($user_id)')";
					
			$resultA1 = $dbf->query($sqltracking);
		
		}
		
		
		echo "<script>parent.$.fn.colorbox.close();</script>"; 
		$msg[] = "<div class=\"success\"><span>Announcement successfully save in draft.</span></div>";	
	}
}

?>
<style>
h3 {
font-family:Verdana, Arial, Helvetica, sans-serif;
font-size:16px;
background: rgba(105, 162, 255, 0.7);
width: 899px;
}
<!--
.style2 {color: #000000}
-->


.close-btn { 
    border: 2px solid #c2c2c2;
    position: relative;
    padding: 1px 5px;
    bottom: -3px;
    background-color: #605F61;
    left: 880px; 
    border-radius: 20px;
}

.close-btn a {
    font-size: 15px;
    font-weight: bold;
    color: white;
    text-decoration: none;
}

.idd {
font-family:Verdana, Arial, Helvetica, sans-serif;
font-size:12px;
}

</style>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang; ?>" lang="<?php echo $lang; ?>">

<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
    <link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
	<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />    
	<script src="../../../lib/js/jquery.min2.js"></script>
	<script src="../../../lib/js/jquery.colorbox.js"></script>
	<script src="../../../lib/js/jquery.mask_input-1.3.js"></script>
	<script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
	<script src="../../../lib/js/datePicker/jquery.ui.core.js"></script>
	<script src="../../../lib/js/datePicker/jquery.ui.widget.js"></script>
	<script src="../../../lib/js/datePicker/jquery.ui.datepicker.js"></script>
	<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>
	<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
		
	<link id="bs-css" href="../../theme/css/button.css" rel="stylesheet" />
	<link id="bs-css" href="administrative/css/button.css" rel="stylesheet" />

	<meta name="author" content="NR" />

	<title>Edit Menu</title>
</head>

<body>
	<!--<span class="close-btn"><a href="#" onClick="javascript: parent.$.fn.colorbox.close();">X</a></span>-->

<?php
   /*$sql = "SELECT b.text, a.menu_level, a.menu_link FROM base_user_sys_menu a
            LEFT JOIN base_language_text b ON (a.menu_id = b.variable)
            WHERE a.menu_id=$menuID AND b.language_code='".$_GET['langcd']."'";
   $result = $db->query($sql);
   $rows = $db->fetchArray($result);
   $menu_title = $rows['text'];
   $menu_link = $rows['menu_link'];
   $menu_level = $rows['menu_level'];
   
   $db->free();*/
        
?>
<div class="padding-5 margin-5 outer">
<h3>Edit Announcement </h3>
<?php
if(!empty($msg)) {
    foreach($msg as $err) {
        echo $err;
    }
}
?>
<form method="post" id="form-set">
	<table class="idd">
		<tr>
			<td style="background-color: rgba(105, 162, 255, 0.7);"><font color="#FF0000">*</font>Publish By</td>
			<td style="background-color: rgba(33, 26, 16, 0.3); " colspan="3"><?=$publish_by?><input type="text" id="publishBy" name="publishBy" value="<?=$publish_by?>" />
			<input type="hidden" id="ref_no" name="ref_no" value="<?=$ref_no?>" /></td>
			
		</tr>
		<tr>
			<td style="background-color: rgba(105, 162, 255, 0.7);"><font color="#FF0000">*</font>Start Date</td>
			<td style="background-color: rgba(33, 26, 16, 0.3); "><input type="text" name="start_date" id="start_date" maxlength="50" readonly="" value="<?=$start_date?>"/></td>
				<?	$jscript .= "\n" . '$( "#start_date" ).datepicker({
												changeMonth: true,
												changeYear: true,
												yearRange: \'-100:+0\',
												dateFormat: \'dd-M-yy\'
											});';
					 
				?>
			<td style="background-color: rgba(105, 162, 255, 0.7);"> <font color="#FF0000">*</font>End Date</td>
			<td style="background-color: rgba(33, 26, 16, 0.3); "><input type="text" name="end_date" id="end_date" maxlength="50" readonly="" value="<?=$end_date?>"/></td>
				<?	$jscript .= "\n" . '$( "#end_date" ).datepicker({
												changeMonth: true,
												changeYear: true,
												yearRange: \'-100:+0\',
												dateFormat: \'dd-M-yy\'
											});';
					 
				?>
			
		</tr>	
		<tr>
			<td style="background-color: rgba(105, 162, 255, 0.7);"><font color="#FF0000">*</font>Title </td>
			<td colspan="3" style="background-color: rgba(33, 26, 16, 0.3); ">
			<input type="text" id="title" name="title"  size = "100" value="<?=$titleA?>"/></td>
		</tr>
		<tr>
			<td style="background-color: rgba(105, 162, 255, 0.7);"><font color="#FF0000">*</font>Announcement </td>
			<td colspan="3" style="background-color: rgba(33, 26, 16, 0.3); ">
			<textarea name="announ" cols="30" class="ckeditor" rows="3" id="announ" ><?=$announcement?></textarea></td>
		</tr>
		<tr>
			<td colspan="4" align="right"><span style="color:#FF0000; font-family:Verdana;font-size:11px;">Note:</span> <span style="font-family:Verdana;font-size:11px;">Field marks with (</span><span style="color:#FF0000">*</span><span style="font-family:Verdana;font-size:11px;">) is compulsory.</span>
			  <input type="submit" id="btnsave" name="btnsave" value = "Save as Draft" />
			  <input type="submit" id="btnsave" name="btnsave" onClick="javascript: parent.$.fn.colorbox.close();" value = "Close" />
			  <!--<input type="submit" id="btnUpdate" name="btnUpdate" value = "Publish" />--></td></tr>

	</table>
</form>
<script>
	<?=$jscript;?>
</script>

</div>
</body>
</html>