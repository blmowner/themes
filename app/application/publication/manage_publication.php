<?php
include("../../../lib/common.php");
checkLogin();

$user_id=$_SESSION['user_id'];

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

if(isset($_POST['btnDelete']) && ($_POST['btnDelete'] <> ""))
{
	
	$publicationBox=$_POST['publicationBox'];
	$publicationId=$_POST['publicationId'];
	$pubDetailId=$_POST['pubDetailId'];
	$status=$_POST['status'];

	$msg=array();
	
	if (sizeof($_POST['publicationBox'])>0) {
		while (list ($key,$val) = @each ($publicationBox)) 
		{
			if ($status[$val] == "SAV") {
				$sql2 = "DELETE FROM pg_publication_detail
				WHERE id = '$pubDetailId[$val]'
				AND publication_id = '$publicationId[$val]'
				AND publication_uploader = '$user_id'
				AND status = 'SAV'
				AND archived_status IS NULL";
				
				$db->query($sql2);
				
				$sql1 = "SELECT id
				FROM pg_publication_detail
				WHERE publication_id = '$publicationId[$val]'
				AND publication_uploader = '$user_id'
				AND status = 'SAV'
				AND archived_status IS NULL";
				
				$result_sql1 = $db->query($sql1);
				$db->next_record();
				
				$row_cnt_sql1 = mysql_num_rows($result_sql1);
				
				if ($row_cnt_sql1 == 0) {
					$sql4 = "DELETE FROM pg_publication
					WHERE id = '$publicationId[$val]'
					AND status = 'A'
					AND archived_status IS NULL";
					
					$db->query($sql4);					
				}
			}
			else if ($status[$val] == "ADD") {
				$curdatetime = date("Y-m-d H:i:s");
				$sql2 = "UPDATE pg_publication_detail
				SET status = 'DEL', modify_by = '$user_id', modify_date = '$curdatetime'
				WHERE id = '$pubDetailId[$val]'
				AND publication_id = '$publicationId[$val]'
				AND publication_uploader = '$user_id'
				AND status = 'ADD'
				AND archived_status IS NULL";
				
				$db->query($sql2);
				
				$sql1 = "SELECT id
				FROM pg_publication_detail
				WHERE publication_id = '$publicationId[$val]'
				AND publication_uploader = '$user_id'
				AND status = 'ADD'
				AND archived_status IS NULL";
				
				$result_sql1 = $db->query($sql1);
				$db->next_record();
				
				$row_cnt_sql1 = mysql_num_rows($result_sql1);
				$curdatetime = date("Y-m-d H:i:s");
				if ($row_cnt_sql1 == 0) {
					$sql4 = "UPDATE pg_publication
					SET status = 'I', modify_by = '$user_id', modify_date = '$curdatetime'
					WHERE id = '$publicationId[$val]'
					AND status = 'A'
					AND archived_status IS NULL";
					
					$db->query($sql4);					
				}
			}			
		}
		$msg[] = "<div class=\"success\"><span>The selected publication has been deleted from the list successfully.</span></div>";
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please select the publication from the list before click DELETE button.</span></div>";
	}
}

if(isset($_POST['btnSearch']) && ($_POST['btnSearch'] <> "")) {
	
	$issn = $_POST['issn'];
	$issue = $_POST['issue'];
	$volume = $_POST['volume'];
	$title = $_POST['title'];
	$keyword = $_POST['keyword'];
	
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
	$publisherNameArray = array();
	$statusArray = array();
	$statusDescArray = array();
	
	$sql = "SELECT a.id,a.issue, i.issn_no, a.volume_id, a.title, DATE_FORMAT(a.published_date,'%d-%b-%Y') AS publishDate, 
	a.type_pub_id, a.abstract, a.publisher_id, a.website, a.country_id, a.keyword, b.id AS publisherId,b.publisher_name, 
	c.description AS typePubDesc, c.id AS pub_type_id, e.id AS countryId, e.description AS country_name, 
	f.id AS volumeId, f.volume AS volumeDesc, a.status, h.description AS status_desc
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
		$volumeDescArray[$i] = $dbf->f('volumeDesc');
		$publishDateArray[$i] = $dbf->f('publishDate');
		$titleArray[$i] = $dbf->f('title');
		$publisherNameArray[$i] = $dbf->f('publisher_name');
		$statusArray[$i] = $dbf->f('status');
		$statusDescArray[$i] = $dbf->f('status_desc');
		$i++;
	}

}
else 
{
	$publicationIdArray = array();
	$pidArray = array();
	$issueArray = array();
	$issnNoArray = array();
	$volumeIdArray = array();
	$volumeDescArray = array();
	$publishDateArray = array();
	$titleArray = array();
	$publisherNameArray = array();
	$statusArray = array();
	$statusDescArray = array();

	$i = 0;
	$sql = "SELECT a.id, i.id as publication_id, a.issue, i.issn_no, a.volume_id, a.title, DATE_FORMAT(a.published_date,'%d-%b-%Y') AS publishDate, 
	a.type_pub_id, a.abstract, a.publisher_id, a.website, a.country_id, a.keyword, b.id AS publisherId,b.publisher_name, 
	c.description AS typePubDesc, c.id AS pub_type_id, e.id AS countryId, e.description AS country_name, 
	f.id AS volumeId, f.volume AS volumeDesc, a.status, h.description AS status_desc
	FROM pg_publication i  
	LEFT JOIN pg_publication_detail a ON (a.publication_id = i.id) 
	LEFT JOIN ref_publisher b ON (b.id = a.publisher_id) 
	LEFT JOIN ref_publication_type c ON (c.id = a.type_pub_id) 
	LEFT JOIN ref_country e ON (e.id = a.country_id) 
	LEFT JOIN ref_volume f ON (f.id = a.volume_id) 
	LEFT JOIN ref_pub_status h ON (h.id = a.status) 
	WHERE a.publication_uploader = '$user_id'
	AND i.status = 'A'
	AND i.archived_status IS NULL
	AND a.archived_status IS NULL
	ORDER BY a.insert_date DESC";
	$resultA = $dbf->query($sql);
	$row_cnt = mysql_num_rows($resultA);	
	while($dbf->next_record())
	{
		$publicationIdArray[$i] = $dbf->f('publication_id');
		$pubDetailIdArray[$i] = $dbf->f('id');
		$issueArray[$i] = $dbf->f('issue');
		$issnNoArray[$i] = $dbf->f('issn_no');
		$volumeIdArray[$i] = $dbf->f('volume_id');
		$volumeDescArray[$i] = $dbf->f('volumeDesc');
		$publishDateArray[$i] = $dbf->f('publishDate');
		$titleArray[$i] = $dbf->f('title');
		$publisherNameArray[$i] = $dbf->f('publisher_name');
		$statusArray[$i] = $dbf->f('status');
		$statusDescArray[$i] = $dbf->f('status_desc');
		$i++;
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
	function editDetail(pid) 
	{
			//document.location.href = "../thesis/new_proposal_discussion.php?pid=" + pid;
			//alert ("edit_publication.php?id=" + pid);
			document.location.href = "edit_publication.php?id=" + pid;
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
	<table>
		<tr>
			<td><strong>Manage Publication</td>
		</tr>
	</table>
	<table>
		<tr>							
			<td>Please enter searching criteria below:-</td>
		</tr>
		<tr>
			<td><strong>Note:</strong> by default it will display the current publication in which the status has been saved as draft and published.</td>
	</table>
	<br>
	<table>
		<tr>
			<td>ISSN</td>
			<td><span style="font-size:14px">:</span><input type="text" id="issn" name="issn" value="" />
			</td>
		</tr>
		<tr>
			<td>Volume</td>
			<td><span style="font-size:14px">:</span><select name="volume" id="volume">
                <option value="" selected="selected"></option>
                <? 
					while ($dbu->next_record()) {
						$volId=$dbu->f('id');
						$volume=$dbu->f('volume');

						?>
			    <option value="<?=$volId?>">
			      <?=romanNumerals($volume)?>
                </option>
			    <?

					};
				?>
		      </select></td>
		</tr>
		<tr>
			<td>Issue</td>
			<td><span style="font-size:14px">:</span><input type="text" id="issue" name="issue" value="" /></td>
		</tr>
		<tr>
			<td>Keyword</td>
			<td><span style="font-size:14px">:</span><input type="text" id="keyword" size="50" name="keyword" value="" /></td>
		</tr>
		<tr>
			<td>Title</td>
			<td><span style="font-size:14px">:</span><input type="text" id="title" name="title" value="" /><input type="submit" name="btnSearch" value="Search" />
				<span style="color:#FF0000;">Note:</span>
				If no parameters are provided, it will search all.</td>
		</tr>
		
	</table>
	<br>
	<fieldset>
	<legend>Searching Results: <?=$row_cnt?> record(s) found</legend>
		<? if($row_cnt > 0) {?>
		<div class = "viewA" style="overflow:auto;width: 980px; height: 150px;">
		<? } else { ?>
		<div class = "viewA" style="overflow:auto;width: 980px; height: 100px;">
		<? } ?>
      <table width="100%" class = "thetable" border="1"> 
        <tr>
		  <th width="5%">Tick</th>
		  <th width="5%">No</th>
		  <th width="10%">ISSN</th>
		  <th width="10%">Volume</th>
		  <th width="10%">Issue</th>  
		  <th width="10%">Published Date</th>
		  <th align="left" width="15%">Title</th> 
          <th align="left" width="15%">Publisher</th>
		  <th width="10%">Status</th>
          <th width="10%">Action</th>
		</tr>  
		<?
		if ($row_cnt > 0) {		
			for ($i=0; $i<$row_cnt; $i++){	?>
				<?
				if($i % 2) $color ="first-row"; else $color = "second-row";

				?>			
			<tr class="<?=$color?>">
				<input type = "hidden" id="publicationId" name="publicationId[]" value="<?=$publicationIdArray[$i]?>"/>	
				<input type = "hidden" id="pubDetailId" name="pubDetailId[]" value="<?=$pubDetailIdArray[$i]?>"/>	
				<input type = "hidden" id="status" name="status[]" value="<?=$statusArray[$i]?>"/>
				<td align="center"><input type="checkbox" name="publicationBox[]" id="publicationBox" value= "<?=$i?>"/></td>
				<td align="center"><?=$i+1?>.</td>
				<td align="center"><?=$issnNoArray[$i]?></td>
				<td align="center"><?=romanNumerals($volumeDescArray[$i])?></td>
				<td align="center"><?=$issueArray[$i]?></td>
				<td align="center"><?=$publishDateArray[$i]?></td>
				<td><?=$titleArray[$i]?></td> 
				<td><?=$publisherNameArray[$i]?></td>
				<td align="center"><?=$statusDescArray[$i]?></td>				
				<td align="center"><input class="edit" id="detail" name="detail" style = " float: center; width:80px; margin-right: 0px" id="edit" name="edit" onClick="javascript:document.location.href='edit_publication.php?pid=<?=$publicationIdArray[$i]?>&pdid=<?=$pubDetailIdArray[$i]?>';"
				type="button" value="Edit"></td>
			</tr>		
			<? }
		}						
		else {
			?>
			<table>
				<tr>
					<td>No record(s) found.</td>
				</tr>
			</table>
			<?
		}?>
	  </table>
    </div>
	 </fieldset>
	<table>
		<tr>
			<td><input id="new" name="new" onClick="javascript:document.location.href='add_publication.php';" type="button" value="New">
			</td>
			<td colspan="6" align="left"><input type="submit" name="btnDelete" id="btnDelete" value="Delete" style = "width:80px;" onclick="return deleteReport()" />
		  </td>
		</tr>
      </table>
    </form>
</div>
</body>
</html>