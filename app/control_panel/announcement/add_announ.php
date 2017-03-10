<?php

    include("../../../lib/common.php");
    checkLogin();
    
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
			//echo substr($sql_slct->f("run_id"),0,8)."<br>$todate";
		
            $run_id = $sql_slct->f("run_id") + 1; 
        }
    }
    return $run_id;
}
	function runnum3($column_name, $tblname) 
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
			
			if($todate > substr($sql_slct->f("run_id"),1,8)) 
			{
				//echo substr($sql_slct->f("run_id"),1,8);
				$run_id = $todate.$run_start;
			} 
			else 
			{
				//echo substr($sql_slct->f("run_id"),1,12);
				$run_id = substr($sql_slct->f("run_id"),1,12) + 1; 
			}
		}
		return $run_id;
	}

if($_POST['btnsubmit'] <> "") 
{
	$announ = $_REQUEST['announ'];
	$title = $_REQUEST['title'];
	$start_date = $_REQUEST['start_date'];
	$end_date = $_REQUEST['end_date'];
	$id = runnum('id','pg_announcement');
	$refId = runnum3('ref_no','pg_announcement');
	$trackId = "R".runnum2('id','pg_announcement_tracking');
	
	$announId = $_REQUEST['announId'];
	$curdatetime = date("Y-m-d H:i:s");
	$start = date("Y-m-d");	
	$end = date("Y-m-d");
		
	$startDate = strtotime($start_date);
	$newStartDate = date ( 'Y-m-d' , $startDate );	

	$endDate = strtotime($end_date);
	$newEndDate = date ( 'Y-m-d' , $endDate );		
	/*$newdate = strtotime ( '+7 days' , strtotime ( $curdatetime ) ) ;
	$newdate = date ( 'Y-m-d' , $newdate );	*/
	$sqlannounce1 = "SELECT * from pg_announcement WHERE id = '$announId'";
	$resultA1 = $db->query($sqlannounce1);
	$db->next_record();
	$row_cnt = mysql_num_rows($resultA1);	
	$idAnnoun=$db->f('id');

	
	if(empty($_POST['title'])) $msg[] = "<div class=\"error\"><span>Please enter the title as required below.</span></div>";
	if(empty($_POST['announ'])) $msg[] = "<div class=\"error\"><span>Please enter the Annoucement as required below.</span></div>";
	if(empty($_POST['start_date'])) $msg[] = "<div class=\"error\"><span>Please enter the Start date as required below.</span></div>";
	if(empty($_POST['end_date'])) $msg[] = "<div class=\"error\"><span>Please enter the Expected End date as required below.</span></div>";
	if($newStartDate < $start) $msg[] = "<div class=\"error\"><span>Start date cannot be before than current date.</span></div>";
	if($newEndDate < $newStartDate) $msg[] = "<div class=\"error\"><span>End date cannot be before than start date.</span></div>";

	if(empty($msg)) 
	{	
		if($row_cnt == 0)
		{
			$sqlannounce = "INSERT INTO pg_announcement 
					(`id`,`title`,`announcement`,`insert_by`,`insert_date`, `status`, start_date, end_date, publish_status, announcer, ref_no)
					VALUES
					('$id', '$title', '".mysql_escape_string($announ)."', '$user_id' , '$curdatetime', 'A', '$newStartDate', '$newEndDate', 'P', '$user_id', '$refId')";
			$resultA = $dbf->query($sqlannounce);
			
			
			$sqlannounce1 = "SELECT * from pg_announcement WHERE id = '$id'";
			$resultA1 = $db->query($sqlannounce1);
			$db->next_record();
			$row_cnt = mysql_num_rows($resultA1);	
			$idAnnoun=$db->f('id');
			
			$sqltracking = "INSERT INTO pg_announcement_tracking
					(`id`,`action`,`action_by`,`query_track`,`announcement_id`,`status`, action_date , display_status)
					VALUES
					('$trackId', 'I', '$user_id', '".mysql_escape_string($sqlannounce)."' , '$id', 'A', '$curdatetime' , 'D')";
					
			$resultA = $dbf->query($sqltracking);
			
		}
		else 
		{
			$sqlupdate = "UPDATE pg_announcement 
			SET title = '$title', announcement = '".mysql_escape_string($announ)."', publish_status= 'P', start_date = '$newStartDate', 
			end_date = '$newEndDate',  modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE id='$announId'";
			
			$resultT = $db->query($sqlupdate);

			$sqltracking = "INSERT INTO pg_announcement_tracking
					(`id`,`action`,`action_by`,`query_track`,`announcement_id`,`status`, action_date, display_status)
					VALUES
					('$trackId', 'I', '$user_id', '".mysql_escape_string($sqlupdate)."' , '$id', 'A', '$curdatetime', 'D')";
			
			$resultTt = $dbf->query($sqltracking);

			
			$sqlannounce1 = "SELECT * from pg_announcement WHERE id = '$announId'";
			$resultA1 = $db->query($sqlannounce1);
			$db->next_record();
			$row_cnt = mysql_num_rows($resultA1);	
			$idAnnoun=$db->f('id');


		}
			$announ = "";
			$title = "";
			$start_date = "";
			$end_date = "";
			$announId = "";

			$msg[] = "<div class=\"success\"><span>Announcement publish success.</span></div>";	
	}
}

if($_POST['btnsave'] <> "") 
{
	$announ = $_REQUEST['announ'];
	$title = $_REQUEST['title'];
	$start_date = $_REQUEST['start_date'];
	$end_date = $_REQUEST['end_date'];
	$id = runnum('id','pg_announcement');
	$trackId = "R".runnum2('id','pg_announcement_tracking');
	$refId = "R".runnum3('ref_no','pg_announcement');
	$announId = $_REQUEST['announId'];
	
	$curdatetime = date("Y-m-d H:i:s");
	$start = date("Y-m-d");	
	$end = date("Y-m-d");
		
	$startDate = strtotime($start_date);
	$newStartDate = date ( 'Y-m-d' , $startDate );	

	$endDate = strtotime($end_date);
	$newEndDate = date ( 'Y-m-d' , $endDate );		
	/*$newdate = strtotime ( '+7 days' , strtotime ( $curdatetime ) ) ;
	$newdate = date ( 'Y-m-d' , $newdate );	*/

	$sqlannounce1 = "SELECT * from pg_announcement WHERE id = '$announId'";
	$resultA1 = $db->query($sqlannounce1);
	$db->next_record();
	$row_cnt = mysql_num_rows($resultA1);	
	$idAnnoun=$db->f('id');

	if(empty($_POST['title'])) $msg[] = "<div class=\"error\"><span>Please enter the title as required below.</span></div>";
	if(empty($_POST['announ'])) $msg[] = "<div class=\"error\"><span>Please enter the Annoucement as required below.</span></div>";
	if(empty($_POST['start_date'])) $msg[] = "<div class=\"error\"><span>Please enter the Start date as required below.</span></div>";
	if(empty($_POST['end_date'])) $msg[] = "<div class=\"error\"><span>Please enter the Expected End date as required below.</span></div>";
	if($newStartDate < $start) $msg[] = "<div class=\"error\"><span>Start date cannot be before than current date.</span></div>";
	if($newEndDate < $newStartDate) $msg[] = "<div class=\"error\"><span>End date cannot be before than start date.</span></div>";

	if(empty($msg)) 
	{	
		if($row_cnt == 0)
		{	
			if(empty($start_date) && empty($end_date))
			{
				$sqlannounce = "INSERT INTO pg_announcement 
						(`id`,`title`,`announcement`,`insert_by`,`insert_date`, `status` , start_date, end_date, publish_status, announcer, ref_no)
						VALUES
						('$id', '$title', '".mysql_escape_string($announ)."', '$user_id' , '$curdatetime', 'A', null, null, 'S', '$user_id', '$refId')";
			}
			else if(empty($end_date) && !empty($start_date))
			{
				$sqlannounce = "INSERT INTO pg_announcement 
						(`id`,`title`,`announcement`,`insert_by`,`insert_date`, `status` , start_date, end_date, publish_status, announcer, ref_no)
						VALUES
						('$id', '$title', '".mysql_escape_string($announ)."', '$user_id' , '$curdatetime', 'A', '$newStartDate', null, 'S', '$user_id', '$refId')";		
			
			}
			else if (empty($start_date) && !empty($end_date))
			{
				$sqlannounce = "INSERT INTO pg_announcement 
						(`id`,`title`,`announcement`,`insert_by`,`insert_date`, `status` , start_date, end_date, publish_status, announcer, ref_no)
						VALUES
						('$id', '$title', '".mysql_escape_string($announ)."', '$user_id' , '$curdatetime', 'A', null, $newEndDate, 'S', '$user_id', '$refId')";				
			
			}
			else {
			$sqlannounce = "INSERT INTO pg_announcement 
					(`id`,`title`,`announcement`,`insert_by`,`insert_date`, `status`, start_date, end_date, publish_status, announcer, ref_no)
					VALUES
					('$id', '$title', '".mysql_escape_string($announ)."', '$user_id' , '$curdatetime', 'A', '$newStartDate', '$newEndDate', 'S', '$user_id', '$refId')";		
			
			}
			$resultA = $dbf->query($sqlannounce);
			
			$sqlannounce1 = "SELECT * from pg_announcement WHERE id = '$id'";
			$resultA1 = $db->query($sqlannounce1);
			$db->next_record();
			$row_cnt = mysql_num_rows($resultA1);	
			$idAnnoun=$db->f('id');

			$sqltracking = "INSERT INTO pg_announcement_tracking
					(`id`,`action`,`action_by`,`query_track`,`announcement_id`,`status`, action_date , display_status, remarks)
					VALUES
					('$trackId', 'I', '$user_id', '".mysql_escape_string($sqlannounce)."' , '$id', 'A', '$curdatetime' , 'D', 'Insert by user<br>($user_id)')";
					
			$resultA1 = $dbf->query($sqltracking);
			

		}
		else
		{
			$sqlupdate = "UPDATE pg_announcement 
			SET title = '$title', announcement = '".mysql_escape_string($announ)."', publish_status= 'S', start_date = '$newStartDate', 
			end_date = '$newEndDate',  modify_by = '$user_id', modify_date = '$curdatetime'
			WHERE id='$announId'";
			
			$resultT = $db->query($sqlupdate);


			$sqltracking = "INSERT INTO pg_announcement_tracking
					(`id`,`action`,`action_by`,`query_track`,`announcement_id`,`status`, action_date , display_status, remarks)
					VALUES
					('$trackId', 'E', '$user_id', '".mysql_escape_string($sqlupdate)."' , '$id', 'A', '$curdatetime' , 'D', 'Edit by user<br>($user_id)')";
					
			$resultA1 = $dbf->query($sqltracking);
							
			$sqlannounce1 = "SELECT * from pg_announcement WHERE id = '$announId'";
			$resultA1 = $db->query($sqlannounce1);
			$db->next_record();
			$row_cnt = mysql_num_rows($resultA1);	
			$idAnnoun=$db->f('id');
		
		
		}	

		
		$msg[] = "<div class=\"success\"><span>Announcement successfully save in draft.</span></div>";	
	}
}

?>
<style>
.idd {
font-family:Verdana, Arial, Helvetica, sans-serif;
font-size:12px;
}
</style>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang; ?>" lang="<?php echo $lang; ?>">

<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
	<meta name="author" content="MJMZ" />

	<title>Change Password</title>
    <link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
    <link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
	<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />    
	<script src="../../../lib/js/jquery.min2.js"></script>
	<script src="../../../lib/js/jquery.colorbox.js"></script>
	<script src="../../../lib/js/jquery.mask_input-1.3.js"></script>
	<script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
	<script src="../../../lib/js/datePicker/jquery.ui.core.js"></script>
	<script src="../../../lib/js/datePicker/jquery.ui.widget.js"></script>
	<script src="../../../lib/js/datePicker/jquery.ui.datepicker.js"></script>
	<script type="text/javascript" src="../../../../lib/js/rightClick.js"></script>
	<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
</head>

<body>
<div class="margin-5 padding-5 outer">
<?php
if(!empty($msg)) 
{
    foreach($msg as $key) {
       echo $key;
    }
}

?>
<script>
// intilize datepicker at document ready or load..
$(document).ready(function(){

        setdatepicker();

});
</script>
    <form method="post" id="form-set">
	<table class="idd">
		<tr>
			<td style="background-color: rgba(105, 162, 255, 0.7);"><font color="#FF0000">*</font>Start Date
		  <input type="hidden" name="announId" id="announId" maxlength="50" readonly=""  value="<?=$idAnnoun?>"/></td>
			<td style="background-color: rgba(33, 26, 16, 0.3); "><input type="text" name="start_date" id="start_date" maxlength="50" readonly=""  value="<?= $start_date?>"/></td>
				<?	$jscript .= "\n" . '$( "#start_date" ).datepicker({
												changeMonth: true,
												changeYear: true,
												yearRange: \'-100:+0\',
												dateFormat: \'dd-M-yy\'
											});';
					 
				?>
			<td style="background-color: rgba(105, 162, 255, 0.7);"> <font color="#FF0000">*</font>End Date</td>
			<td style="background-color: rgba(33, 26, 16, 0.3); "><input type="text" name="end_date" id="end_date" maxlength="50" readonly=""  value="<?= $end_date;?>"/></td>
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
			<td colspan="3" style="background-color: rgba(33, 26, 16, 0.3); "><input type="text" id="title" name="title"  size = "100" value="<?= $title;?>"/></td>
		</tr>
		<tr>
			<td style="background-color: rgba(105, 162, 255, 0.7);"><font color="#FF0000">*</font>Announcement </td>
		    <td colspan="3" style="background-color: rgba(33, 26, 16, 0.3); "><textarea name="announ" cols="30" class="ckeditor" rows="3" id="announ" ><?= $announ;?>
		    </textarea></td>
		</tr>
		<tr>
			<td colspan="4" align="right"><span style="color:#FF0000; font-family:Verdana;font-size:11px;">Note:</span> <span style="font-family:Verdana;font-size:11px;">Field marks with (</span><span style="color:#FF0000">*</span><span style="font-family:Verdana;font-size:11px;">) is compulsory.</span>
			  <input type="submit" id="btnsave" name="btnsave" value = "Save as Draft" />
			  <input type="submit" id="btnsubmit" name="btnsubmit" value = "Publish" /></td></tr>
	</table>
    </form>
<script>
	<?=$jscript;?>
</script>
</div>
</body>
</html>