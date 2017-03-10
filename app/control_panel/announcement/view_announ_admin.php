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


    if($_POST['btnUpdate'] <> ""){
        
        $msg = array();

		$announ = $_REQUEST['announ'];
		$title = $_REQUEST['title'];
		$start_date = $_REQUEST['start_date'];
		$end_date = $_REQUEST['end_date'];
		$Aid = runnum('id','pg_announcement');
		$trackId = "R".runnum2('id','pg_announcement_tracking');
		
		$curdatetime = date("Y-m-d H:i:s");
		$start = date("Y-m-d");	
		$end = date("Y-m-d");
			
		$startDate = strtotime($start_date);
		$newStartDate = date ( 'Y-m-d' , $startDate );	
	
		$endDate = strtotime($end_date);
		$newEndDate = date ( 'Y-m-d' , $endDate );		
		
		if(empty($_REQUEST['title'])) $msg[] = "<div class=\"error\"><span>Please enter the title as required below.</span></div>";
		if(empty($_REQUEST['announ'])) $msg[] = "<div class=\"error\"><span>Please enter the Annoucement as required below.</span></div>";
		if(empty($_POST['start_date'])) $msg[] = "<div class=\"error\"><span>Please enter the Start date as required below.</span></div>";
		if(empty($_POST['end_date'])) $msg[] = "<div class=\"error\"><span>Please enter the Expected End date as required below.</span></div>";
		if($newStartDate < $start) 
		{
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

			$msg[] = "<div class=\"error\"><span>Start date cannot be before than current date.</span></div>"; 
		}
		if(empty($msg)) 
		{	
		
			/*$sqlUpd = "UPDATE pg_announcement SET title='$title', announcement = '$announ', modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE id='$id'";*/
			
			$sqlUpd = "UPDATE pg_announcement SET status= 'I', modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE id='$id'";			
			
			$process = $db->query($sqlUpd);
			
			$sqlUpd1 = "INSERT INTO pg_announcement 
			(`id`,`title`,`announcement`,`insert_by`,`insert_date`, `status`, start_date, end_date, publish_status)
			VALUES
			('$Aid', '$title', '".mysql_escape_string($announ)."', '$user_id' , '$curdatetime', 'A', '$newStartDate', '$newEndDate', 'P')";			
			$process1 = $db->query($sqlUpd1);

			//echo "Value = $onoff <br> Update value = Y";
			
			$query = preg_replace("/'/","/",$sqlUpd1);
			$lol = mysql_escape_string($sqlUpd1);
			
			$sqltracking = "INSERT INTO pg_announcement_tracking
					(`id`,`action`,`action_by`,`query_track`,`announcement_id`,`status`, action_date, display_status)
					VALUES
					('$trackId', 'E', '$user_id', '".$lol."' , '$Aid', 'A', '$curdatetime', 'D')";
			$resultT = $dbf->query($sqltracking);
				
			$sqlUpd = "UPDATE pg_announcement_tracking SET display_status='N'
			WHERE announcement_id='$Aid' 
			AND action = 'I'";
			$process = $db->query($sqlUpd);
			
			echo "<script>parent.$.fn.colorbox.close();</script>"; 
			$msg[] = "<div class=\"success\"><span>Announcement update success.</span></div>";	
		}
    }
	
if($_POST['btnsave'] <> "") 
{
	$announ = $_REQUEST['announ'];
	$title = $_REQUEST['title'];
	$start_date = $_REQUEST['start_date'];
	$end_date = $_REQUEST['end_date'];
	$id = runnum('id','pg_announcement');
	$Aid = $_GET['id'];
	$trackId = "R".runnum2('id','pg_announcement_tracking');
	
	$curdatetime = date("Y-m-d H:i:s");
	$start = date("Y-m-d");	
	$end = date("Y-m-d");
		
	$startDate = strtotime($start_date);
	$newStartDate = date ( 'Y-m-d' , $startDate );	

	$endDate = strtotime($end_date);
	$newEndDate = date ( 'Y-m-d' , $endDate );		
	/*$newdate = strtotime ( '+7 days' , strtotime ( $curdatetime ) ) ;
	$newdate = date ( 'Y-m-d' , $newdate );	*/

	if(empty($_POST['title'])) $msg[] = "<div class=\"error\"><span>Please enter the title as required below.</span></div>";
	if(empty($_POST['announ'])) $msg[] = "<div class=\"error\"><span>Please enter the Annoucement as required below.</span></div>";
	if($newStartDate < $start) 
	{
		$sqlannounce = "SELECT *, DATE_FORMAT(start_date,'%d-%M-%Y') AS start_date, DATE_FORMAT(end_date,'%d-%M-%Y') AS end_date 
		FROM pg_announcement
		WHERE status = 'A' 
		AND id = '$Aid'";
		$resultA = $dbg->query($sqlannounce);
		
		
		$dbg->next_record();
		
		$titleA =$dbg->f('title');
		$announcement =$dbg->f('announcement');
		$start_date=$dbg->f('start_date');
		$end_date=$dbg->f('end_date');

		$msg[] = "<div class=\"error\"><span>Start date cannot be before than current date.</span></div>"; 
	}

	if(empty($msg)) 
	{	
		if(empty($start_date) && empty($end_date))
		{
			$sqlannounce = "INSERT INTO pg_announcement 
					(`id`,`title`,`announcement`,`insert_by`,`insert_date`, `status` , start_date, end_date, publish_status)
					VALUES
					('$id', '$title', '".mysql_escape_string($announ)."', '$user_id' , '$curdatetime', 'A', null, null, 'S')";
		}
		else if(empty($end_date) && !empty($start_date))
		{
			$sqlannounce = "INSERT INTO pg_announcement 
					(`id`,`title`,`announcement`,`insert_by`,`insert_date`, `status` , start_date, end_date, publish_status)
					VALUES
					('$id', '$title', '".mysql_escape_string($announ)."', '$user_id' , '$curdatetime', 'A', '$newStartDate', null, 'S')";		
		
		}
		else if (empty($start_date) && !empty($end_date))
		{
			$sqlannounce = "INSERT INTO pg_announcement 
					(`id`,`title`,`announcement`,`insert_by`,`insert_date`, `status` , start_date, end_date, publish_status)
					VALUES
					('$id', '$title', '".mysql_escape_string($announ)."', '$user_id' , '$curdatetime', 'A', null, $newEndDate, 'S')";				
		
		}
		else {
		$sqlannounce = "INSERT INTO pg_announcement 
				(`id`,`title`,`announcement`,`insert_by`,`insert_date`, `status`, start_date, end_date, publish_status)
				VALUES
				('$id', '$title', '".mysql_escape_string($announ)."', '$user_id' , '$curdatetime', 'A', '$newStartDate', '$newEndDate', 'P')";		
		
		}
		
		$resultA = $dbf->query($sqlannounce);
		
		if(!empty($start_date)&& !empty($end_date))
		{	
			$sqltracking = "INSERT INTO pg_announcement_tracking
					(`id`,`action`,`action_by`,`query_track`,`announcement_id`,`status`, action_date , display_status)
					VALUES
					('$trackId', 'I', '$user_id', '".mysql_escape_string($sqlannounce)."' , '$id', 'A', '$curdatetime', 'D')";
		}
		else if(!empty($start_date)&& empty($end_date))
		{
			$sqltracking = "INSERT INTO pg_announcement_tracking
					(`id`,`action`,`action_by`,`query_track`,`announcement_id`,`status`, action_date , display_status)
					VALUES
					('$trackId', 'I', '$user_id', '".mysql_escape_string($sqlannounce)."' , '$id', 'A', '$curdatetime', 'D')";
		}
		else if(empty($start_date)&& !empty($end_date))
		{
			$sqltracking = "INSERT INTO pg_announcement_tracking
					(`id`,`action`,`action_by`,`query_track`,`announcement_id`,`status`, action_date , display_status)
					VALUES
					('$trackId', 'I', '$user_id', '".mysql_escape_string($sqlannounce)."' , '$id', 'A', '$curdatetime', 'D')";
		}
		else
		{
			$sqltracking = "INSERT INTO pg_announcement_tracking
					(`id`,`action`,`action_by`,`query_track`,`announcement_id`,`status`, action_date , display_status)
					VALUES
					('$trackId', 'I', '$user_id', '".mysql_escape_string($sqlannounce)."' , '$id', 'A', '$curdatetime', 'D')";
		}
		$resultT = $dbf->query($sqltracking);
		
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
<h3>View Announcement </h3>
<?php
if(!empty($msg)) {
    foreach($msg as $err) {
        echo $err;
    }
}
?>
<form method="post" id="form-set">
	<table height="136" class="idd">
		<tr>
			<td width="126" style="background-color: rgba(105, 162, 255, 0.7);">Start Date</td>
			<td width="242" style="background-color: rgba(33, 26, 16, 0.3); "><?=$start_date?></td>
			<td width="83" style="background-color: rgba(105, 162, 255, 0.7);"> End Date</td>
			<td width="229" style="background-color: rgba(33, 26, 16, 0.3); "><?=$end_date?></td>
		</tr>
	
		<tr>
			<td style="background-color: rgba(105, 162, 255, 0.7);">Title </td>
			<td colspan="3" style="background-color: rgba(33, 26, 16, 0.3); ">
			<?=$titleA?></td>
		</tr>
		<tr>
			<td height="36" style="background-color: rgba(105, 162, 255, 0.7);">Announcement </td>
			<td colspan="3" style="background-color: rgba(33, 26, 16, 0.3); ">
			<?=$announcement?></td>
		</tr>
		<tr>
			<td colspan="4" align="right"><input type="submit" id="btnsave" name="btnsave" onClick="javascript: parent.$.fn.colorbox.close();" value = "Close" />
			</td>
		</tr>

	</table>
</form>
<script>
	<?=$jscript;?>
</script>

</div>
</body>
</html>