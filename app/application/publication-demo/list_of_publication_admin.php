<?php
    include("../../../lib/common.php");
    checkLogin();

function romanNumerals($num) 
{
    $n = intval($num);
    $res = '';
 
    /*** roman_numerals array  ***/
    $roman_numerals = array(
                'M'  => 1000,
                'CM' => 900,
                'D'  => 500,
                'CD' => 400,
                'C'  => 100,
                'XC' => 90,
                'L'  => 50,
                'XL' => 40,
                'X'  => 10,
                'IX' => 9,
                'V'  => 5,
                'IV' => 4,
                'I'  => 1);
 
    foreach ($roman_numerals as $roman => $number) 
    {
        /*** divide to get  matches ***/
        $matches = intval($n / $number);
 
        /*** assign the roman char * $matches ***/
        $res .= str_repeat($roman, $matches);
 
        /*** substract from the number ***/
        $n = $n % $number;
    }
 
    /*** return the res ***/
    return $res;
}    
    
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
	$curdatetime1 = date("Y-m-d");
	if (sizeof($_POST['announBox'])>0) {
		while (list ($key,$val) = @each ($announBox)) 
		{
			$sql = "SELECT * FROM pg_publication_detail WHERE id = '$announBox[$key]'";
			$dbg->query($sql);
			$dbg->next_record();
			$publishStatus =$dbg->f('publication_status');
			if($publishStatus == 'S')
			{
				$sql1 = "DELETE FROM pg_publication_detail WHERE id ='$announBox[$key]'";		
			}
			else if($publishStatus == 'A')
			{
				$sql1 = "UPDATE pg_publication_detail SET status= 'I', publication_status = 'D' WHERE id ='$announBox[$key]'";	
			}
			else if($publishStatus == '')
			{
				$sql1 = "DELETE FROM pg_publication_detail WHERE id ='$announBox[$key]'";	
			}
			/*else
			{
				$msg[] = "<div class=\"error\"><span>Announcement that has been publish cannot be deleted before announcement end date is finish.</span></div>";
			}*/
			
			/*$sql1 = "UPDATE pg_announcement_tracking
			SET status = 'IN', modify_date = '$curdatetime', modify_by = '$user_id'
			WHERE id='$announBox[$key]'";*/ 
			
			$dbg->query($sql1);			
		}
		$msg[] = "<div class=\"success\"><span>The selected publication has been deleted from the list successfully.</span></div>";
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please select the publication from the list before click DELETE button.</span></div>";
	}
}

if(isset($_POST['btnSearch']) && ($_POST['btnSearch'] <> "")) {
	
	$keyword = $_POST['keyword'];
	$issue = $_POST['issue'];
	$issn = $_POST['issn'];
	$title = $_POST['title'];
	$volume = $_POST['volume'];
	
	if ($keyword!="") 
	{
		$tmpSearchKeyword = " AND a.keyword = '$keyword'";
	}
	else 
	{
		$tmpSearchKeyword="";
	}
	if ($issue!="") 
	{
		$tmpSearchIssue = " AND a.issue = '$issue'";
	}
	else 
	{
		$tmpSearchIssue="";
	}
	if ($issn!="") 
	{
		$tmpSearchIssn = " AND a.issn_no = '$issn'";
	}
	else 
	{
		$tmpSearchIssn="";
	}
	if ($title!="") 
	{
		$tmpSearchtitle = " AND a.title like '%$title%'";
	}
	else 
	{
		$tmpSearchtitle="";
	}
	if ($volume!="") 
	{
		$tmpSearchVolume = " AND a.volume_id = '$volume'";
	}
	else 
	{
		$tmpSearchVolume="";
	}


	$pidArray = array();
	$issueArray = array();
	$issn_noArray = array();
	$volume_idArray = array();
	$volumeDescArray = array();
	$publishDateArray = array();
	$titleArray = array();
	$publisherNameDetailArray = array();
	$statusArray = array();
	$uploaderPublicationArray = array();
	$statusPublicationArray = array();
	
	$sqlannounce = "SELECT a.id,a.issue, a.issn_no, a.volume_id, a.title, DATE_FORMAT(a.publish_date,'%d-%b-%Y') AS publishDate, a.type_pub_id, a.publication_status,
		a.abstract, a.publisher_id, a.website, a.country_id, a.keyword, b.id AS publisherId,b.publisher_name, c.description AS typePubDesc, a.publication_uploader,
		c.id AS pub_type_id, e.id AS countryId, e.description AS country_name, f.id AS volumeId, f.volume AS volumeDesc, g.id AS authorId, 
		g.name AS authorName ,
		h.description as status
		FROM pg_publication_detail a 
		LEFT JOIN ref_publisher b ON (b.id = a.publisher_id) 
		LEFT JOIN ref_publication_type c ON (c.id = a.type_pub_id) 
		LEFT JOIN ref_country e ON (e.id = a.country_id) 
		LEFT JOIN ref_volume f ON (f.id = a.volume_id) 
		LEFT JOIN ref_author g ON (g.id = a.author_id)
		LEFT JOIN ref_announcement h ON (h.id = a.publication_status) 
		WHERE a.status = 'A'" 
		.$tmpSearchKeyword." "
		.$tmpSearchIssue." "
		.$tmpSearchtitle." "
		.$tmpSearchIssn." "
		.$tmpSearchVolume." "." 
		AND a.publication_status IN ('S','A')
		ORDER BY a.insert_date DESC";
					
	$resultA = $dbf->query($sqlannounce);
	$row_cnt = mysql_num_rows($resultA);	
				
	$i = 0;
	$inc = 0;
	while($dbf->next_record())
	{
		$pid = $dbf->f('id');
		$issue = $dbf->f('issue');
		$issn_no = $dbf->f('issn_no');
		$volume_id = $dbf->f('volume_id');
		$publishDate = $dbf->f('publishDate');
		$title = $dbf->f('title');
		$volumeDesc = $dbf->f('volumeDesc');
		$publisherNameDetail = $dbf->f('publisher_name');
		$status = $dbf->f('status');
		$uploader_publication = $dbf->f('publication_uploader');
		$publication_status = $dbf->f('publication_status');

		$pidArray[$i] = $pid; 
		$issueArray[$i] = $issue; 
		$issn_noArray[$i] = $issn_no; 
		$volume_idArray[$i] = $volume_id; 
		$volumeDescArray[$i] = $volumeDesc; 
		$publishDateArray[$i] = $publishDate; 
		$titleArray[$i] = $title;
		$publisherNameDetailArray[$i] = $publisherNameDetail;
		$statusArray[$i]=$status;
		$uploaderPublicationArray[$i] = $uploader_publication;
		$statusPublicationArray[$i] = $publication_status;

		$i++;
		$inc++;
	}

}
else 
{
	$pidArray = array();
	$issueArray = array();
	$issn_noArray = array();
	$volume_idArray = array();
	$volumeDescArray = array();
	$publishDateArray = array();
	$titleArray = array();
	$publisherNameDetailArray = array();
	$statusArray = array();
	$statusPublicationArray = array();

	$i = 0;
	$inc = 0;
	$sqlannounce = "SELECT a.id,a.issue, a.issn_no, a.volume_id, a.title, DATE_FORMAT(a.publish_date,'%d-%b-%Y') AS publishDate, a.type_pub_id, a.publication_status,
	a.abstract, a.publisher_id, a.website, a.country_id, a.keyword, b.id AS publisherId,b.publisher_name, c.description AS typePubDesc, a.publication_uploader,
	c.id AS pub_type_id, e.id AS countryId, e.description AS country_name, f.id AS volumeId, f.volume AS volumeDesc, g.id AS authorId, 
	g.name AS authorName ,
	h.description as status
	FROM pg_publication_detail a 
	LEFT JOIN ref_publisher b ON (b.id = a.publisher_id) 
	LEFT JOIN ref_publication_type c ON (c.id = a.type_pub_id) 
	LEFT JOIN ref_country e ON (e.id = a.country_id) 
	LEFT JOIN ref_volume f ON (f.id = a.volume_id) 
	LEFT JOIN ref_author g ON (g.id = a.author_id)
	LEFT JOIN ref_announcement h ON (h.id = a.publication_status) 
	WHERE a.status = 'A'
	AND a.publication_status IN ('S','A')
	ORDER BY a.insert_date DESC";
	$resultA = $dbf->query($sqlannounce);
	$row_cnt = mysql_num_rows($resultA);	
	while($dbf->next_record())
	{
		$pid = $dbf->f('id');
		$issue = $dbf->f('issue');
		$issn_no = $dbf->f('issn_no');
		$volume_id = $dbf->f('volume_id');
		$publishDate = $dbf->f('publishDate');
		$title = $dbf->f('title');
		$volumeDesc = $dbf->f('volumeDesc');
		$publisherNameDetail = $dbf->f('publisher_name');
		$status = $dbf->f('status');
		$uploader_publication = $dbf->f('publication_uploader');
		$publication_status = $dbf->f('publication_status');

		$pidArray[$i] = $pid; 
		$issueArray[$i] = $issue; 
		$issn_noArray[$i] = $issn_no; 
		$volume_idArray[$i] = $volume_id; 
		$volumeDescArray[$i] = $volumeDesc; 
		$publishDateArray[$i] = $publishDate; 
		$titleArray[$i] = $title;
		$publisherNameDetailArray[$i] = $publisherNameDetail;
		$statusArray[$i]=$status;
		$uploaderPublicationArray[$i] = $uploader_publication;
		$statusPublicationArray[$i] = $publication_status;

		$i++;
		$inc++;
	}
}	
$sql3 = "SELECT id, volume
FROM ref_volume
ORDER BY order_by";
		
$result_sql3 = $dbu->query($sql3);

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
	 <script type="text/javascript">
		function lol(pid) 
		{
				//document.location.href = "../thesis/new_proposal_discussion.php?pid=" + pid;
				//alert ("edit_publication.php?id=" + pid);
				document.location.href = "edit_publication_admin.php?id=" + pid;
				return true;
				
		}
		function deleteReport() 
		{
			var ask = window.confirm("Are you sure to delete this Publication? \nClick OK to proceed or CANCEL to stay on the same page.");
			if (ask) 
			{
				return true;
			}
			return false;
		}
		function lol1(pid) 
		{
				//document.location.href = "../thesis/new_proposal_discussion.php?pid=" + pid;
				//alert ("edit_publication.php?id=" + pid);
				document.location.href = "publication_detail_admin.php?id=" + pid;
				return true;
				
		}
	
	</script>

<div class="">
<?php
if(!empty($msg)) 
{
    foreach($msg as $key) {
       echo $key;
    }
}
?>
    <form method="post" id="form-set">
	<fieldset><legend><strong>Publication Manager</strong></legend>
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
			<td>Keyword</td>
			<td><span style="font-size:14px">:</span><input type="text" id="keyword" name="keyword" value="" /></td>
		</tr>
		<tr>
			<td>Title</td>
			<td><span style="font-size:14px">:</span><input type="text" id="title" name="title" value="" /></td>
		</tr>
		<tr>
			<td>ISSN</td>
			<td><span style="font-size:14px">:</span><input type="text" id="issn" name="issn" value="" /></td>
		</tr>		

		<tr>
			<td>Volume</td>
			<td><span style="font-size:14px">:</span><select name="volume" id="volume">
				<option value="" selected="selected"></option>
				<? 
					while ($dbu->next_record()) {
						$volId=$dbu->f('id');
						$volume=$dbu->f('volume');

						?><option value="<?=$volId?>"><?=romanNumerals($volume)?> </option><?

					};
				?></select></td>
				
		</tr>
		<tr>
			<td>Issue</td>
			<td><span style="font-size:14px">:</span><input type="text" id="issue" name="issue" value="" /></td>
			<td><input type="submit" name="btnSearch" value="Search" />
				<span style="color:#FF0000;">Note:</span>
				If no parameters are provided, it will search all.</td>
		</tr>

	</table>
	</fieldset>
	<fieldset>
	<legend>Searching Result: <?=$row_cnt?> record(s) found</legend>
	<!--<a style="position: relative;left: 850px;" href="add_publication.php">New Publication</a>-->
	<div class = "viewA" style="overflow:auto; width:990px; height: 150px;">
      <table width="968" class = "thetable" border="1"> <!--thetable2-->
        <tr>
		  <th width="54">Tick</th>
		  <th width="46">ISSN</th>
		  <th width="66">Volume</th>
		  <th width="46">Issue</th>
		  <th width="67">Publish Date</th>
		  <!--<th width="128"> ID</th>-->
          <th align="left" width="275">Title</th> 
          <!--style = "background-color: #837ECD;"-->
		  <th width="105">Publisher</th>
		  <th width="61">Uploader</th>
		  <th width="81">Status</th>
          <th width = "110">Action</th>
		  
		  
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
		</tr>

        <tr class="<?=$color?>">
		  <td align="center"><input type="checkbox" name="announBox[]" id="announBox" value= "<?=$pidArray[$i]?>"/></td>
		  <!--<td><?=$idArray[$i]?></td>-->
		  <td align="center"><?=$issn_noArray[$i]?></td>
		  <td align="center"><?=romanNumerals($volumeDescArray[$i])?></td>
		  <td align="center"><?=$issueArray[$i]?></td>
		  <td align="center"><?=$publishDateArray[$i]?></td>
          <td><?=$titleArray[$i]?></td> 
		  <td align="center"><?=$publisherNameDetailArray[$i]?></td>
		  <td align="center"><?=$uploaderPublicationArray[$i]?></td>
		  <td align="center"><?=$statusArray[$i]?></td>
          <td align="center">
		  <!-- btn btn-primary btn-xs</td>-->
		  <? if($statusPublicationArray[$i] == 'S') { ?>
		  <input class="edit" id="edit" name="edit" style = " float: right; width:80px; margin-right: 25px" onClick="lol('<?=$pidArray[$i]?>')" type="button" value="Edit">
          <? } else { ?> 
		   <input class="view" id="view" name="view" style = " float: right; width:80px; margin-right: 25px" onClick="lol1('<?=$pidArray[$i]?>')" type="button" value="View">
		   <? } ?>
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
	 </fieldset>
	<table>
		<tr>
		  <td colspan="6" align="left"><input type="submit" name="btndelete" id="btndelete" value="Delete" style = "width:80px;" onclick="return deleteReport()" /><!--btn btn-danger btn-xs-->
		  </td>
		</tr>
      </table>

	 <br />
	 <br />
    </form>
</div>
</body>
</html>