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
	
	$curdatetime = date("Y-m-d");
	
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
	
	if($row_cnt>0)
	{
?>
			<? for ($i=0; $i<$inc; $i++){	?>
            <?
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
			 <br /><p style="border-bottom: dashed #818489; margin-bottom: 1px; margin-top:2px;"></p>
			 <? } else if(!empty($empName)){ ?> <br /><p><?=$insertDateArray[$i]?> (<?=$empName?>)</p>
			 <br /><p style="border-bottom: dashed #818489; margin-bottom: 1px; margin-top:2px;"></p>
			 <? } else { ?><? } ?>
            
		<?	}?>
<?
	}
	else
	{
		echo "<div style=\"margin-top:20px;margin-left:20px;\"><a class=\"title1\"  href=\"#\"  id = \"title\" name = \"title\">Sorry, Currently there is no new announcement</a></div>";
	}

?>
