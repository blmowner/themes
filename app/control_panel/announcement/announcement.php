<?php

    include("../../../lib/common.php");
    checkLogin();
	
	$sqlannounce = "SELECT id, title, announcement, insert_by, DATE_FORMAT(insert_date,'%d-%b-%Y, %l:%i %p') AS insert_date, 
	DATE_FORMAT(modify_date,'%d-%b-%Y, %l:%i %p') AS date FROM pg_announcement
	WHERE status = 'A' 
	ORDER BY insert_date DESC";
	$resultA = $dba->query($sqlannounce);
	while($dbf->next_record())
	{
		$idA =$dba>f('id');
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
		$(".title").colorbox({width:"83%", height:"90%", iframe:true,          
		onClosed:function(){ 
		//window.location.reload(true); //uncomment this line if you want to refresh the page when child close
		window.location = window.location;   //reload the page                 
		} }); 
	});
	
	</script>
	<style type="text/css">
	.style5 {color: #FFFFFF; font-weight: bold; }
	.style6 {color: #FFFFFF}
	.style8 {color: #5B74A8; font-weight: bold; font-size: 14px; }
    </style>
	
<script>
function iframecode(url, w, h) {
				//alert("111");
				window.parent.$.colorbox({href:url, iframe: true, scrolling: true, width: w, height: h});
			}
//$(".forgot").colorbox({ bottom: "65px", width:"70%", height:"75%", iframe:true, onClosed:function(){} }); 
</script>
<script>
    $(document).ready(function(){
        setInterval(function() {
            $("#try").load("announcement.php");
        }, 300000);
    });

</script>

</head>

<body>
<?php
if(!empty($msg)) 
{
    foreach($msg as $key) {
       echo $key;
    }
}
?>
    <form method="post" id="form-set">
	<div class="wrapper2" id="try" name="try">
		<div class = "banner_center">
		  <div class="announce_table" id= "latestData" name= "latestData">
	<?
	$titleArray = array();
	$idArray = array();
	$announcementArray = array();
	$insertByArray = array();
	$insertDateArray = array();
	$startDateArray = array();
	$curdatetime = date("Y-m-d");
	$i = 0;
	$inc = 0;
	
	
	$sqlannounce = "SELECT *, DATE_FORMAT(insert_date,'%d-%b-%Y, %l:%i %p') AS insert_date1, DATE_FORMAT(modify_date,'%d-%b-%Y, %l:%i %p') AS DATE 
	FROM pg_announcement 
	WHERE STATUS= 'A'
	AND publish_status = 'P'
	AND start_date <= DATE('$curdatetime') 
	AND end_date >= DATE('$curdatetime') /*OR end_date IS NULL*/
	ORDER BY insert_date DESC";
	
	/*$sqlannounce = "SELECT a.id, a.title, a.announcement, a.insert_by, DATE_FORMAT(a.insert_date,'%d-%b-%Y, %l:%i %p') AS insert_date, b.start_date,
	b.expected_end_date , DATE_FORMAT(a.modify_date,'%d-%b-%Y, %l:%i %p') AS DATE 
	FROM pg_announcement a 
	LEFT JOIN pg_announcement_tracking b ON (b.announcement_id = a.id) 
	WHERE a.status = 'A'
	AND b.display_status = 'D'
	AND b.start_date <= DATE('$curdatetime') 
	AND b.expected_end_date >= DATE('$curdatetime') OR b.expected_end_date IS NULL 
	ORDER BY a.insert_date DESC ";*/

	$resultA = $dbf->query($sqlannounce);
	$row_cnt = mysql_num_rows($resultA);	
	
	while($dbf->next_record())
	{
		$idA =$dbf->f('id');
		$titleA =$dbf->f('title');
		$announcement =$dbf->f('announcement');
		$insertBy = $dbf->f('insert_by');
		$insertDate = $dbf->f('insert_date1');
		$Date = $dbf->f('DATE');
		$startDate = $dbf->f('start_date');
		
		$titleArray[$i] = $titleA;
		$idArray[$i] = $idA;
		$announcementArray[$i] = $announcement;
		$insertByArray[$i] = $insertBy;
		$insertDateArray[$i] = $insertDate;
		$DateArray[$i] = $Date;
		$startDateArray[$i] = $startDate;
		
		$i++;
		$inc++;
	}
	
	if($row_cnt>0) {
		for ($i=0; $i<$inc; $i++){	
		
			$sqlname = "SELECT name FROM new_employee WHERE empid = '$insertByArray[$i]' ";
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
		  <a class="title" href="read_announce.php?id=<?=$idArray[$i]?>"  id = "title" name = "title"><?=$titleArray[$i]?></a>
		  <?
			$nowdate = date("Y-m-d");
			if ($startDateArray[$i] == $nowdate)
			{
				echo "<span class=\"label-success label label-default\">New</span>";
			}
			//else { echo "$startDateArray[$i] == $nowdate"; }
		  ?></td>
		<? 
		if(!empty($DateArray[$i])) { ?>
		 <br /><p><?=$DateArray[$i]?> (<?=$empName?>)</p>
		 <? } else { ?> <br /><p><?=$insertDateArray[$i]?> (<?=$empName?>)</p> <? } ?>
		<br /><p style="border-bottom: dashed #818489; margin-bottom: 1px; margin-top:2px;"></p>
		
	<?}
	}
	else
	{
	?>
	<table>
		<tr>
			<td><span class="style8"><label>Currently there is no announcement available.</label></span></td>
		</tr>
	</table>
	<?
	}

?>
          </div>
		</div>
	</div>

    </form>

</body>
</html>