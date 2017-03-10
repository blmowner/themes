<?php 
    include("../../../lib/common.php"); 
    checkLogin();
	
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

    // used for pagination
$page = "";
$page = ($page == 0 ? 1 : $page);
$perpage = 20;
$startpoint = ($page * $perpage) - $perpage;

if(isset($_POST['btnSearch']) && ($_POST['btnSearch'] <> "")) {
	$issn = $_POST['searchIssnNo'];
	$issue = $_POST['searchIssue'];
	$volume = $_POST['searchVolume'];
	$title = $_POST['searchTitle'];
	$keyword = $_POST['searchKeyword'];
	
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
		$sql3 = "SELECT id FROM pg_publication where issn_no = 'issn'";
		$dba2 = $dba;
		$result_sql3 = $dba2->query($sql3); 
		$dba2->next_record();
		
		$publicationId = $dba2->f('id');	
		
		$tmpSearchIssn = " AND a.publication_id = '$publicationId'";
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


	$pubDetailIdArray = array();
	$issueArray = array();
	$issnNoArray = array();
	$volumeIdArray = array();
	$volumeDescArray = array();
	$publishDateArray = array();
	$titleArray = array();
	$publisherIdArray = array();
	$publisherNameArray = array();
	$publicationTypeIdArray = array();
	$publicationTypeDescArray = array();
	$websiteArray = array();
	$countryIdArray = array();
	$countryNameArray = array();
	$statusArray = array();
	$statusDescArray = array();
	
	$sql = "SELECT a.id,a.issue, i.issn_no, a.volume_id, a.title, DATE_FORMAT(a.published_date,'%d-%b-%Y') AS publishDate, 
	a.type_pub_id, a.abstract, a.publisher_id, a.website, a.country_id, a.keyword, b.id AS publisherId,b.publisher_name, 
	c.description AS pub_type_desc, c.id AS pub_type_id, e.id AS countryId, e.description AS country_name, 
	f.id AS volumeId, f.volume, a.status, h.description AS status_desc
	FROM pg_publication i  
	LEFT JOIN pg_publication_detail a ON (a.publication_id = i.id) 
	LEFT JOIN ref_publisher b ON (b.id = a.publisher_id) 
	LEFT JOIN ref_publication_type c ON (c.id = a.type_pub_id) 
	LEFT JOIN ref_country e ON (e.id = a.country_id) 
	LEFT JOIN ref_volume f ON (f.id = a.volume_id) 
	LEFT JOIN ref_pub_status h ON (h.id = a.status) 
	WHERE a.publication_uploader = '$user_id'" 
	.$tmpSearchKeyword." "
	.$tmpSearchIssue." "
	.$tmpSearchtitle." "
	.$tmpSearchIssn." "
	.$tmpSearchVolume." "." 
	AND i.status = 'A'
	AND a.status = 'ADD'
	AND i.archived_status IS NULL
	AND a.archived_status IS NULL
	ORDER BY a.insert_date DESC";
					
	$resultA = $dbf->query($sql);
	$row_cnt = mysql_num_rows($resultA);	
				
	$i = 0;
	while($dbf->next_record())
	{
		$pubDetailIdArray[$i] = $dbf->f('id');
		$issueArray[$i] = $dbf->f('issue');
		$issnNoArray[$i] = $dbf->f('issn_no');
		$volumeIdArray[$i] = $dbf->f('volume_id');
		$volumeArray[$i] = $dbf->f('volume');
		$publishDateArray[$i] = $dbf->f('publishDate');
		$titleArray[$i] = $dbf->f('title');
		$publisherIdArray[$i] = $dbf->f('publisher_id');
		$publisherNameArray[$i] = $dbf->f('publisher_name');
		$publicationTypeIdArray[$i] = $dbf->f('pub_type_id');
		$publicationTypeDescArray[$i] = $dbf->f('pub_type_desc');
		$websiteArray[$i] = $dbf->f('website');
		$countryIdArray[$i] = $dbf->f('country_id');
		$countryNameArray[$i] = $dbf->f('country_name');
		$statusArray[$i] = $dbf->f('status');
		$statusDescArray[$i] = $dbf->f('status_desc');
		$i++;
	}
}
else {
	$row_cnt = 0;
}

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang; ?>" lang="<?php echo $lang; ?>">

<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
	<meta name="author" content="MJMZ" />

	<title>Select Publication</title>
    <link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
    <script>
	<? if (!isset($_GET['field'])) { ?>
        function closeThePop(issnNo, volume, issue, publishedDate, title, publisherId, publisherName, publicationTypeId, publicationTypeDesc, website, countryId, countryName) 
		{
            parent.$.fn.colorbox.close(); //call the colorbox's close function
            parent.$.fn.getParameterValue(issnNo, volume, issue, publishedDate, title, publisherId, publisherName, publicationTypeId, publicationTypeDesc, website, countryId, countryName); // pass the parameter to parent
        }
	<? } ?>
    </script>
</head>
<body>
<style>
.close-btn { 
    border: 2px solid #c2c2c2;
    position: relative;
    padding: 1px 5px;
    bottom: 20px;
    background-color: #605F61;
    left: 620px; 
    border-radius: 20px;
}

.close-btn a {
    font-size: 15px;
    font-weight: bold;
    color: white;
    text-decoration: none;
}

</style>

<?php
    if(!empty($msg)) 
	{
        foreach($msg as $err) 
		{
            echo $err;
        }
    }
	?>



	<form id="frmPopup" action=""  method="post" name="frmPopup">
	<fieldset>
		<legend><strong>Select Publication</strong></legend>
	<table>
		<tr>							
			<td>Please enter searching criteria below to find the record:-</td>
		</tr>
	</table>		
	<table>
		<tr>
			<td><label>ISSN</label></td>
			<td>:</td>
			<td><input type="text" name="searchIssnNo" /></td>
		</tr>
		<tr>
			<td><label>Volume</label></td>
			<td>:</td>
			<td><input type="text" name="searchVolume" /></td>
		</tr>
		<tr>
			<td><label>Issue</label></td>
			<td>:</td>
			<td><input type="text" name="searchIssue" /></td>
		</tr>
		<tr>
			<td><label>Title</label></td>
			<td>:</td>
			<td><input type="text" size="50" name="searchTitle" /></td>
		</tr>
		<tr>
			<td><label>Keyword</label></td>
			<td>:</td>
			<td><input type="text" name="searchKeyword" />
			<input type="submit" name="btnSearch" value="Search" class="fancy-button-blue" />Note: If no entry is provided, it will search all.</td>
		</tr>
		
	</table>
	<br/>
	<table>
		<tr>							
			<td>Searching Results:- <?=$row_cnt ?> record(s) found.</td>
		</tr>
	</table>
	<?if ($row_cnt <= 0) {?>
		<div id = "tabledisplay" style="overflow:auto; height:100px;">
	<?}
	else if ($row_cnt <= 1) {?>
		<div id = "tabledisplay" style="overflow:auto; height:150px;">
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
	<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="100%" class="thetable">		
	<tr>
		<th width="5%" align="center"><label>No</label></th>
		<th width="15%" align="left">ISSN</th>
		<th width="15%" align="left">Volume</th>
		<th width="15%" align="left">Issue</th>
		<th width="25%" align="left">Title</th>
		<th width="20%" align="left">Publisher</th>

	</tr>
	<?php
		if ($row_cnt > 0) {
			$inc = 1;
			for ($no = 0; $no < $row_cnt; $no++) {
				if($inc % 2) $color="first-row"; else $color="second-row";
				?>
				<tr class="<?php echo $color; ?>">
					<td align="center"><?=$no+1?>.</td>
					<td><a href="#" onclick="closeThePop('<?=$issnNoArray[$no]?>','<?=$volumeArray[$no]?>','<?=$issueArray[$no]?>','<?=$publishDateArray[$no]?>','<?=$titleArray[$no]?>','<?=$publisherIdArray[$no]?>','<?=$publisherNameArray[$no]?>','<?=$publicationTypeIdArray[$no]?>','<?=$publicationTypeDescArray[$no]?>','<?=$websiteArray[$no]?>','<?=$countryIdArray[$no]?>','<?=$countryNameArray[$no]?>');"><?=$issnNoArray[$no]?></a></td>
					<td align="left"><?php echo $volumeArray[$no]; ?></td>
					<td align="left"><label><?php echo $issueArray[$no]; ?></label></td>
					<td align="left"><?=$titleArray[$no]; ?></td>
					<td align="left"><?=$publisherNameArray[$no]; ?></td>
				</tr>    
				<?php				
				$inc++;			
			};
		?>
			<table>
				<tr>
					<input name="row_cnt" type="hidden" value="<?php echo $row_cnt?>">
				</tr>
			</table>
		


	</table>
	<?}
		else {
			?>
			<table>
				<tr>
					<td><label>No record(s) found.</label></td>
				</tr>
			</table>
			<br>
			<table>
				<tr>
					<td><label>Possible Reason:-</label></td>
				</tr>
				<tr>
					<td><label>1. It could be your Publication is not in the list yet. Please provide it via Manage Publication.</label></td>
				</tr>
			</table>
			<?
		}?>
	</div>
	</fieldset>
	<table>
		<tr>							
			<table>
				<tr>
					<td><input type="button" name="btnClose" onclick="javascript: parent.$.fn.colorbox.close();" value="Close" /></input></td>
				</tr>
			</table>
		</tr>
	</table>	
	</form>
</body>
</html>