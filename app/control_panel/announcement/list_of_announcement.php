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
            $run_id = $sql_slct->f("run_id") + 1; 
        }
    }
    return $run_id;
}

if(isset($_POST['btndelete']) && ($_POST['btndelete'] <> ""))
{
	
	$announBox=$_POST['announBox'];
	$msg=array();
	$curdatetime = date("Y-m-d H:i:s");
	$curdatetime2 = date("Y-m-d H:i:s");
	$curdatetime1 = date("Y-m-d");
	$trackId = "R".runnum2('id','pg_announcement_tracking');

	if (sizeof($_POST['announBox'])>0) {
		while (list ($key,$val) = @each ($announBox)) 
		{
			$sql = "SELECT * FROM pg_announcement WHERE id = '$announBox[$key]'";
			$dbg->query($sql);
			$dbg->next_record();
			$publishStatus =$dbg->f('publish_status');
			$endDate = $dbg->f('end_date');
			if($publishStatus == 'S')
			{
				$sql1 = "DELETE FROM pg_announcement WHERE id ='$announBox[$key]'";		
			}
			else if($publishStatus == 'P')
			{
				$sql1 = "UPDATE pg_announcement SET status= 'I', modify_by = '$user_id', modify_date = '$curdatetime' WHERE id ='$announBox[$key]'";	
			}
			else if($publishStatus == '')
			{
				$sql1 = "DELETE FROM pg_announcement WHERE id ='$announBox[$key]'";	
			}
			
			$dbg->query($sql1);	
			
			$lol = mysql_escape_string($sql1);

			$sqltracking = "INSERT INTO pg_announcement_tracking
				(`id`,`action`,`action_by`,`query_track`,`announcement_id`,`status`, action_date, display_status)
				VALUES
				('$trackId', 'D', '$user_id', '".$lol."' , '$announBox[$key]', 'A', '$curdatetime2', 'D')";
			$resultT = $dbf->query($sqltracking);

			/*else
			{
				$msg[] = "<div class=\"error\"><span>Announcement that has been publish cannot be deleted before announcement end date is finish.</span></div>";
			}*/
			
			/*$sql1 = "UPDATE pg_announcement_tracking
			SET status = 'IN', modify_date = '$curdatetime', modify_by = '$user_id'
			WHERE id='$announBox[$key]'";*/ 
			
					
		}
		$msg[] = "<div class=\"success\"><span>The selected announcement has been deleted from the list successfully.</span></div>";
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please select the announcement from the list before click DELETE button.</span></div>";
	}
}
if(isset($_POST['btnSearch']) && ($_POST['btnSearch'] <> "")) {
	
	$publish_by = $_POST['publish_by'];
	$start_date = $_POST['start_date'];
	$end_date = $_POST['end_date'];
	$title = $_POST['title'];
	
	$start = date("Y-m-d");	
	$end = date("Y-m-d");
		
	$startDate = strtotime($start_date);
	$newStartDate = date ( 'Y-m-d' , $startDate );	

	$endDate = strtotime($end_date);
	$newEndDate = date ( 'Y-m-d' , $endDate );		

	if ($start_date!="") 
	{
		$tmpSearchStartDate = " AND a.start_date = '$newStartDate'";
	}
	else 
	{
		$tmpSearchStartDate="";
	}
	if ($end_date!="") 
	{
		$tmpSearchEndDate = " AND a.end_date = '$newEndDate'";
	}
	else 
	{
		$tmpSearchEndDate="";
	}
	if ($title!="") 
	{
		$tmpSearchtitle = " AND a.title like '%$title%'";
	}
	else 
	{
		$tmpSearchtitle="";
	}

	
	$idArray = array();
	$titleArray =array();
	$announcementArray =array();
	$insertDateArray = array();
	$modifyDateArray = array();
	$publishStatusArray = array();
	$startDateArray = array();
	$endDateArray = array();
	$publishStatusIdArray = array();
	$reviewStatusArray = array();
	$reviewByArray = array();
	$reviewDescArray = array();
	$refNoArray = array();
	$reviewedDateArray = array();

	$i = 0;
	$inc = 0;
	$sqlannounce = "SELECT *, a.id, c.description, d.description AS reviewDesc, DATE_FORMAT(a.reviewed_date,'%d-%b-%Y %l:%i %p') AS reviewed_date,
	DATE_FORMAT(a.start_date,'%d-%b-%Y') AS start_date, DATE_FORMAT(a.end_date,'%d-%b-%Y') AS end_date
	FROM pg_announcement a 
	LEFT JOIN ref_announcement c ON (c.id=a.publish_status) 
	LEFT JOIN ref_announcement d ON (d.id =a.review_status)
	WHERE a.announcer = '$user_id' 
	AND a.status= 'A'"
	.$tmpSearchStartDate." "
	.$tmpSearchEndDate." "
	.$tmpSearchtitle." "." 	
	ORDER BY a.ref_no DESC, a.insert_date
	";
	$resultA = $dbf->query($sqlannounce);
	while($dbf->next_record())
	{
		$id =$dbf->f('id');
		$title =$dbf->f('title');
		$announcement =$dbf->f('announcement');
		$insertDate = $dbf->f('insert_date');
		$modifyDate = $dbf->f('modify_date');
		$publishStatusId = $dbf->f('publish_status');
		$publish_status = $dbf->f('description');
		$start_date = $dbf->f('start_date');
		$end_date = $dbf->f('end_date');
		$review_status = $dbf->f('review_status');
		$review_desc = $dbf->f('reviewDesc');		
		$review_by = $dbf->f('review_by');
		$ref_no = $dbf->f('ref_no');
		$reviewed_date = $dbf->f('reviewed_date');

		
		$idArray[$i] =$id;
		$titleArray[$i] = $title;
		$announcementArray[$i] =$announcement;
		$insertDateArray[$i] = $insertDate;
		$modifyDateArray[$i] = $modifyDate;
		$publishStatusArray[$i] = $publish_status;
		$startDateArray[$i] = $start_date;
		$endDateArray[$i] = $end_date;
		$publishStatusIdArray[$i] = $publishStatusId;
		$reviewStatusArray[$i] = $review_status;
		$reviewByArray[$i] =$review_by;
		$reviewDescArray[$i] =$review_desc;
		$refNoArray[$i] =$ref_no;
		$reviewedDateArray[$i] = $reviewed_date;

		$i++;
		$inc++;
	}

}
else 
{

	$idArray = array();
	$titleArray =array();
	$announcementArray =array();
	$insertDateArray = array();
	$modifyDateArray = array();
	$publishStatusArray = array();
	$startDateArray = array();
	$endDateArray = array();
	$publishStatusIdArray = array();
	$reviewStatusArray = array();
	$reviewByArray = array();
	$reviewDescArray = array();
	$refNoArray = array();
	$reviewedDateArray = array();


	$i = 0;
	$inc = 0;
	$sqlannounce = "SELECT *, a.id, c.description, d.description AS reviewDesc, DATE_FORMAT(a.reviewed_date,'%d-%b-%Y %l:%i %p') AS reviewed_date,
	DATE_FORMAT(a.start_date,'%d-%b-%Y') AS start_date, DATE_FORMAT(a.end_date,'%d-%b-%Y') AS end_date
	FROM pg_announcement a 
	LEFT JOIN ref_announcement c ON (c.id=a.publish_status) 
	LEFT JOIN ref_announcement d ON (d.id =a.review_status)
	WHERE a.status= 'A'
	AND a.announcer = '$user_id' 
	ORDER BY a.ref_no DESC, a.insert_date
	";
	$resultA = $dbf->query($sqlannounce);
	while($dbf->next_record())
	{
		$id =$dbf->f('id');
		$title =$dbf->f('title');
		$announcement =$dbf->f('announcement');
		$insertDate = $dbf->f('insert_date');
		$modifyDate = $dbf->f('modify_date');
		$publishStatusId = $dbf->f('publish_status');
		$publish_status = $dbf->f('description');
		$start_date = $dbf->f('start_date');
		$end_date = $dbf->f('end_date');
		$review_status = $dbf->f('review_status');
		$review_desc = $dbf->f('reviewDesc');		
		$review_by = $dbf->f('reviewed_by');
		$ref_no = $dbf->f('ref_no');
		$reviewed_date = $dbf->f('reviewed_date');
		
		$idArray[$i] =$id;
		$titleArray[$i] = $title;
		$announcementArray[$i] =$announcement;
		$insertDateArray[$i] = $insertDate;
		$modifyDateArray[$i] = $modifyDate;
		$publishStatusArray[$i] = $publish_status;
		$startDateArray[$i] = $start_date;
		$endDateArray[$i] = $end_date;
		$publishStatusIdArray[$i] = $publishStatusId;
		$reviewStatusArray[$i] = $review_status;
		$reviewByArray[$i] =$review_by;
		$reviewDescArray[$i] =$review_desc;
		$refNoArray[$i] =$ref_no;
		$reviewedDateArray[$i] = $reviewed_date;
		$i++;
		$inc++;
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang; ?>" lang="<?php echo $lang; ?>">

<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
	<meta name="author" content="MJMZ" />

	<title>Change Password</title>
    <link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
    <link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
   	<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
	<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
	<link id="bs-css" href="../../../theme/css/button.css" rel="stylesheet" />
	<script src="../../../lib/js/jquery.min2.js"></script>
	<script src="../../../lib/js/jquery.colorbox.js"></script>
	<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
	<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>
	<script>
	$(document).ready(function(){
		$(".editAnnoun").colorbox({width:"84%", height:"90%", iframe:true,          
		onClosed:function(){ 
		//window.location.reload(true); //uncomment this line if you want to refresh the page when child close
		window.location = window.location;   //reload the page                 
		} }); 
	});
	
	</script>
	<script>
	function deleteReport() 
	{
		var ask = window.confirm("Are you sure to delete this Announcement? \nClick OK to proceed or CANCEL to stay on the same page.");
		if (ask) 
		{
			return true;
		}
		return false;
	}
	
	</script>
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
    <form method="post" id="form-set">
	<table>
		<tr>							
			<td>Please enter searching criteria below</td>
		</tr>
		<tr>
			<td><strong>Notes:-</strong>(by default it will display,<br/>
			1. Current publication in which it status has been saved as draft and publish			</td>
	</table>
	<table>
      <tr>
        <td>Title</td>
        <td><span style="font-size:14px">:</span>
            <input type="text" id="title" name="title" value="" /></td>
      </tr>
      <tr>
        <td>Start Date </td>
        <td><span style="font-size:14px">:</span>
            <input type="text" id="start_date" name="start_date" value="" /></td>
        <?	$jscript .= "\n" . '$( "#start_date" ).datepicker({
												changeMonth: true,
												changeYear: true,
												yearRange: \'-100:+0\',
												dateFormat: \'dd-M-yy\'
											});';
					 
				?>
      </tr>
      <tr>
        <td>End Date </td>
        <td><span style="font-size:14px">:</span>
            <input type="text" id="end_date" name="end_date" value="" /></td>
        <?	$jscript .= "\n" . '$( "#end_date" ).datepicker({
												changeMonth: true,
												changeYear: true,
												yearRange: \'-100:+0\',
												dateFormat: \'dd-M-yy\'
											});';
					 
				?>
        <td><input type="submit" name="btnSearch" value="Search" />
            <span style="color:#FF0000;">Note:</span> If no parameters are provided, it will search all.</td>
      </tr>
    </table>
	<div class = "viewA" style="overflow:auto; height: 300px;">
      <table width="1028" border="1" class = "thetable"> <!--thetable2-->
        <tr>
		  <th width="60">Tick</th>
		  <!--<th width="128"> ID</th>-->
          <th width="403" align="left">Title</th> 
          <!--style = "background-color: #837ECD;"-->
          <th width = "139"> Start Date </th>
          <th width="124" > End Date </th>
		  <!--<th width="124" > Review Status </th>
		  <th width="124" > Review By </th>
		  <th> Reviewed Date</th>
		  <th>Reference No</th>-->
          <th width="87" >Status</th>
		  <th width = "123">Action</th>
        </tr>
		<? for ($i=0; $i<$inc; $i++){	?>
		<?

		if($i % 2) $color ="first-row"; else $color = "second-row";

		$sqlname = "SELECT name FROM new_employee WHERE empid = '$reviewByArray[$i]' ";
		if (substr($insertDateArray[$i],0,2) != '07') { 
			$dbConnStudent= $dbc; 
		} 
		else { 
			$dbConnStudent=$dbc1; 
		}
		$resultName = $dbConnStudent->query($sqlname);
		$dbConnStudent->next_record();
		
		$empName =$dbConnStudent->f('name');
		
		
		?>
		
        <tr class="<?=$color?>">
		  <td align="center"><input type="checkbox" name="announBox[]" id="announBox" value= "<?=$idArray[$i]?>"/></td>
		  <!--<td><?=$idArray[$i]?></td>-->
          <td><?=$titleArray[$i]?></td> 
          <td align="center"><?=$startDateArray[$i]?></td>
          <td align="center"><?=$endDateArray[$i]?><br /></td>
          <!--<td align="center"><?=$reviewDescArray[$i]?></td>
		  <td align="center">
		  <? if(!empty($reviewByArray[$i])) {?>
		  <?=$empName?>(<?=$reviewByArray[$i]?>)
		  <? } else {?>
		  None
		  <? } ?>
		  </td>
		  <td>
			<? if(!empty($reviewedDateArray[$i])) {?>
		  		<?=$reviewedDateArray[$i]?>
			<? } else { ?>
				None
			<? } ?>
		  </td>
		  <td align="center"><?=$refNoArray[$i]?></td>-->
		  <td align="center"><?=$publishStatusArray[$i]?></td>
		  <td align="center">
		  <button class="editAnnoun" id="editAnnoun" name="editAnnoun" style = "width:80px;" href="edit_announ.php?id=<?=$idArray[$i]?>">Edit</button><!-- btn btn-primary btn-xs-->
		  </td>
        </tr>
		<?
		/*if (!empty($modifyByArray[$i]))
		{ ?>
			<tr class="<?=$color?>">
			  <td align="center"></td>
			  <td></td> 
			  <td align="center"><?=$actionArray[$i]?></td>
			  <td align="center"><?=$empName1?><br />(<?=$modifyByArray[$i]?>)</td>
			  <td><?=$modifyDateArray[$i]?></td>
			  <td align="center"><a class="editAnnoun btn btn-primary btn-xs" id="editAnnoun" name="editAnnoun" style = "width:80px;" 
			  href="edit_announ.php?id=<?=$idArray[$i]?>">Edit</a></td>
			</tr>
			
	<?	}*/
		?>
		<? } ?>
	  </table>
		<table>
		<tr>
		  <td colspan="6" align="left"><input type="submit" name="btndelete" id="btndelete" onclick = " return deleteReport()" value="Delete" style = "width:80px;" class="" /><!--btn btn-danger btn-xs--></td>
		</tr>

      </table>
	 </div>
    </form>
<script>
<?=$jscript;?>
</script>
	
</div>
</body>
</html>