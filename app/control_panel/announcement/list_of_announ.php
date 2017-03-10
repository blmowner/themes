<?php
    include("../../lib/common.php");
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

	$idArray = array();
	$titleArray =array();
	$announcementArray =array();
	$insertDateArray = array();
	$modifyDateArray = array();
	$publishStatusArray = array();
	$startDateArray = array();
	$endDateArray = array();
	

	$i = 0;
	$inc = 0;
	$sqlannounce = "SELECT * FROM pg_announcement 
	WHERE insert_by = 'user_id' 
	ORDER BY insert_date ASC
	status= ''";
	$resultA = $dbf->query($sqlannounce);
	while($dbf->next_record())
	{
		$id =$dbf->f('id');
		$title =$dbf->f('title');
		$announcement =$dbf->f('announcement');
		$insertDate = $dbf->f('insert_date');
		$modifyDate = $dbf->f('modify_date');
		$publish_status = $dbf->f('publish_status');
		$start_date = $dbf->f('start_date');
		$end_date = $dbf->f('end_date');

		
		$idArray[$i] =$id;
		$titleArray[$i] = $title;
		$announcementArray[$i] =$announcement;
		$insertDateArray[$i] = $insertDate;
		$modifyDateArray[$i] = $modifyDate;
		$$publishStatusArray[$i] = $publish_status;
		$startDateArray[$i] = $start_date;
		$endDateArray[$i] = $end_date;

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
    <link rel="stylesheet" type="text/css" href="../../theme/css/<?php echo $css; ?>" />
    <link rel="stylesheet" type="text/css" href="../../theme/css/colorbox.css" media="screen" />
   	<link rel="stylesheet" href="../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
	<link rel="stylesheet" href="../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
	<link id="bs-css" href="../../theme/css/button.css" rel="stylesheet" />
	<script src="../../lib/js/jquery.min2.js"></script>
	<script src="../../lib/js/jquery.colorbox.js"></script>
	<script type="text/javascript" src="../../lib/js/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
	<script type="text/javascript" src="../../lib/js/rightClick.js"></script>
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
	<div class = "viewA" style="overflow:auto; height: 500px;">
      <table width="auto" class = "thetable2">
        <tr>
		  <th width="50px">Tick</th>
		  <th> ID</th>
          <th width="520px">Title</th> <!--style = "background-color: #837ECD;"-->
          <th width = "50px">Start Date </th>
          <th width="110" >Expected End Date </th>
          <th >Status</th>
		  <th width = "100px">Detail/Action</th>
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

		$titleArray[$i] = $title;
		$announcementArray[$i] =$announcement;
		$insertDateArray[$i] = $insertDate;
		$modifyDateArray[$i] = $modifyDate;
		$$publishStatusArray[$i] = $publish_status;
		$startDateArray[$i] = $start_date;
		$endDateArray[$i] = $end_date;

		
		?>
		
        <tr class="<?=$color?>">
		  <td align="center"><input type="checkbox" name="announBox[]" id="announBox" value= "<?=$idArray[$i]?>"/></td>
		  <td><?=$idArray[$i]?></td>
          <td><?=$titleArray[$i]?></td> 
          <td align="center"><?=$startDateArray[$i]?></td>
          <td align="center"><?=$endDateArray[$i]?><br /></td>
          <td><?=$publishStatusArray[$i]?></td>
		  <td align="center"><a class="editAnnoun btn btn-primary btn-xs" id="editAnnoun" name="editAnnoun" style = "width:80px;" 
		  href="detail_announ.php?id=<?=$idArray[$i]?>">Detail</a>
		  <br /><a class="editAnnoun btn btn-primary btn-xs" id="editAnnoun" name="editAnnoun" style = "width:80px;" href="edit_announ.php?id=<?=$idArray[$i]?>">Edit</a></td>
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
		<tr>
		  <td colspan="6" align="left"><input type="submit" name="btndelete" id="btndelete" value="Delete" style = "width:80px;" class="btn btn-danger btn-xs" /></td>
		</tr>

      </table>
	 </div>
    </form>
</div>
</body>
</html>