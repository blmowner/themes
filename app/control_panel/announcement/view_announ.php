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

if(isset($_POST['btndelete']) && ($_POST['btndelete'] <> ""))
{
	
	$announBox=$_POST['announBox'];
	$msg=array();
	$curdatetime = date("Y-m-d H:i:s");
	if (sizeof($_POST['announBox'])>0) {
		while (list ($key,$val) = @each ($announBox)) 
		{
			$sql1 = "UPDATE pg_announcement_tracking
			SET status = 'IN', modify_date = '$curdatetime', modify_by = '$user_id'
			WHERE id='$announBox[$key]'";
			
			$dbg->query($sql1); 			
		}
		$msg[] = "<div class=\"success\"><span>The selected announcement has been deleted from the list successfully.</span></div>";
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please select the announcement from the list before click DELETE button.</span></div>";
	}
}

	$trackIdArray = array();
	$actionByArray = array();
	$queryTrackArray = array();
	$titleArray = array();
	$announcementIdArray = array();
	$descriptionArray = array();
	$action_dateArray = array();
	$remarkArray = array();

	$i = 0;
	$inc = 0;
	$sqlannounce = "SELECT a.id AS trackId, a.action_by, DATE_FORMAT(a.action_date,'%d-%b-%Y, %l:%i %p') as action_date, a.query_track, a.remarks,
	b.title, b.id AS announcementId, c.description
	FROM pg_announcement_tracking a
	LEFT JOIN pg_announcement b ON (b.id=a.announcement_id)
	LEFT JOIN ref_announcement c ON (c.id=a.action)
	WHERE a.status = 'A'
	ORDER BY b.id DESC, a.action_date DESC";
	$resultA = $dbf->query($sqlannounce);
	while($dbf->next_record())
	{
		$trackId =$dbf->f('trackId');
		$actionBy =$dbf->f('action_by');
		$queryTrack =$dbf->f('query_track');
		$title = $dbf->f('title');
		$announcementId = $dbf->f('announcementId');
		$description = $dbf->f('description');
		$action_date = $dbf->f('action_date');
		$remark = $dbf->f('remarks');
		
		$trackIdArray[$i] = $trackId;
		$actionByArray[$i] = $actionBy;
		$queryTrackArray[$i] = $queryTrack;
		$titleArray[$i] = $title;
		$announcementIdArray[$i] = $announcementId;
		$descriptionArray[$i] = $description;
		$action_dateArray[$i] = $action_date;
		$remarkArray[$i]= $remark;
		$i++;
		$inc++;
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
	<div class = "viewA" style="overflow:auto; height: 350px;">
      <table width="auto" border="1" class = "thetable2">
        <tr>
		  <th width="50">Tick</th>
		  <th width="82">Tracking ID</th>
		  <th width="127">Announcement Id</th>
          <th width="432" align="left">Title</th> 
          <!--style = "background-color: #837ECD;"-->
          <th width = "81">Action</th>
          <th width="102" >Action By</th>
          <th width="99" >Action Date</th>
		  <th width="91">Remark</th>
		  <th width = "100">Action</th>
        </tr>
		<? for ($i=0; $i<$inc; $i++){	?>
		<?

		if($i % 2) $color ="first-row"; else $color = "second-row";

		$sqlname = "SELECT name FROM new_employee WHERE empid = '$actionByArray[$i]' ";
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
		  <td align="center"><input type="checkbox" name="announBox[]" id="announBox" value= "<?=$trackIdArray[$i]?>"/></td>
		  <td><?=$trackIdArray[$i]?></td>
		  <td align="center"><?=$announcementIdArray[$i]?></td>
          <td>
			  <?=$titleArray[$i]?>
		  </td> 
          <td align="center"><?=$descriptionArray[$i]?></td>
          <td align="center"><?=$empName?><br />(<?=$actionByArray[$i]?>)</td>
          <td><?=$action_dateArray[$i]?></td>
		  <td><?=$remarkArray[$i]?></td>
		  <td align="center"><a class="editAnnoun btn btn-primary btn-xs" id="editAnnoun" name="editAnnoun" style = "width:80px;" 
		  href="detail_announ.php?id=<?=$trackIdArray[$i]?>">Detail</a>
		  <br />
		  <!--<a class="editAnnoun btn btn-primary btn-xs" id="editAnnoun" name="editAnnoun" style = "width:80px;" href="edit_announ.php?id=<?=$announcementIdArray[$i]?>">Edit</a>-->
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
	 </div>
	 <table>
		<tr>
		  <td colspan="6" align="left"><input type="submit" name="btndelete" id="btndelete" value="Delete" style = "width:80px;" class="btn btn-danger btn-xs" /></td>
		</tr>
	 </table>
    </form>
</div>
</body>
</html>