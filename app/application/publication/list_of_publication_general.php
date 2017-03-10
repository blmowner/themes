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
			else if($publishStatus == 'P')
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
	$studentName = $_POST['studentName'];
	$matricNo = $_POST['matricNo'];
	
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
		$tmpSearchIssn = " AND a.issn_no = '$issnId'";
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
	if ($studentName!="") 
	{
		$tmpSearchStudentName = " AND name LIKE '%$studentName%'";
	}
	else 
	{
		$tmpSearchStudentName="";
	}
	if ($matricNo!="") 
	{
		$tmpSearchMatricNo = " AND a.publication_uploader like '%$matricNo%'";
	}
	else 
	{
		$tmpSearchMatricNo="";
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
	$uploadBy = array();
	
	$sqlannounce = "SELECT a.id,a.issue, i.issn_no, a.volume_id, a.title, DATE_FORMAT(a.published_date,'%d-%b-%Y') AS publishDate, a.type_pub_id, a.publication_uploader,
	a.abstract, a.publisher_id, a.website, a.country_id, a.keyword, b.id AS publisherId,b.publisher_name, c.description AS typePubDesc, 
	c.id AS pub_type_id, e.id AS countryId, e.description AS country_name, f.id AS volumeId, f.volume AS volumeDesc,
	h.description as status
	FROM pg_publication_detail a 
	LEFT JOIN ref_publisher b ON (b.id = a.publisher_id) 
	LEFT JOIN pg_publication i ON (i.id = a.publication_id) 
	LEFT JOIN ref_publication_type c ON (c.id = a.type_pub_id) 
	LEFT JOIN ref_country e ON (e.id = a.country_id) 
	LEFT JOIN ref_volume f ON (f.id = a.volume_id) 
	LEFT JOIN ref_pub_status h ON (h.id = a.status) 
	WHERE i.status = 'A'
	AND a.status = 'ADD'" 
	.$tmpSearchKeyword." "
	.$tmpSearchIssue." "
	.$tmpSearchtitle." "
	.$tmpSearchMatricNo." "
	.$tmpSearchVolume." "." 
	AND i.archived_status IS NULL
	AND a.archived_status IS NULL
	ORDER BY a.insert_date DESC";
	
	$resultA = $dbf->query($sqlannounce);
	$row_cnt = mysql_num_rows($resultA);	
				
	$i = 0;
	$inc = 0;
	while($dbf->next_record())
	{
		$pidArray[$i] = $dbf->f('id');
		$issueArray[$i] = $dbf->f('issue');
		$issn_noArray[$i] = $dbf->f('issn_no');
		$volume_idArray[$i] = $dbf->f('volume_id');
		$volumeDescArray[$i] = $dbf->f('volumeDesc');
		$publishDateArray[$i] = $dbf->f('publishDate');
		$titleArray[$i] = $dbf->f('title');
		$publisherNameDetailArray[$i] = $dbf->f('publisher_name');
		$statusArray[$i]=$dbf->f('status');
		$uploadBy[$i] = $dbf->f('publication_uploader');

		$i++;
	}
	
	$studentNameArray = Array();
	for ($j=0; $j<$i; $j++){
		    $sql9 = "SELECT name
			FROM student
			WHERE matrix_no = '$uploadBy[$j]'"
			.$tmpSearchStudentName." ";
		if (substr($uploadBy[$j],0,2) != '07') { 
			$dbConnStudent= $dbc; 
		} 
		else { 
			$dbConnStudent=$dbc1; 
		}
		$result9 = $dbConnStudent->query($sql9); 
		$dbConnStudent->next_record();
		if (mysql_num_rows($result9)>0) {
			$studentNameArray[$inc] = $dbConnStudent->f('name');
			$pidArray[$inc] = $pidArray[$j];
			$issueArray[$inc] = $issueArray[$j];
			$issn_noArray[$inc] = $issn_noArray[$j];
			$volume_idArray[$inc] = $volume_idArray[$j];
			$volumeDescArray[$inc] = $volumeDescArray[$j];
			$publishDateArray[$inc] = $publishDateArray[$j];
			$titleArray[$inc] = $titleArray[$j];
			$publisherNameDetailArray[$inc] = $publisherNameDetailArray[$j];
			$statusArray[$inc] = $statusArray[$j];
			$uploadBy[$inc] = $uploadBy[$j];
			$inc++;
		}
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
	$uploadBy = array();

	$i = 0;
	$inc = 0;
	$sqlannounce = "SELECT a.id,a.issue, i.issn_no, a.volume_id, a.title, DATE_FORMAT(a.published_date,'%d-%b-%Y') AS publishDate, a.type_pub_id, a.publication_uploader,
	a.abstract, a.publisher_id, a.website, a.country_id, a.keyword, b.id AS publisherId,b.publisher_name, c.description AS typePubDesc, 
	c.id AS pub_type_id, e.id AS countryId, e.description AS country_name, f.id AS volumeId, f.volume AS volumeDesc,
	h.description as status
	FROM pg_publication_detail a 
	LEFT JOIN ref_publisher b ON (b.id = a.publisher_id) 
	LEFT JOIN pg_publication i ON (i.id = a.publication_id) 
	LEFT JOIN ref_publication_type c ON (c.id = a.type_pub_id) 
	LEFT JOIN ref_country e ON (e.id = a.country_id) 
	LEFT JOIN ref_volume f ON (f.id = a.volume_id) 
	LEFT JOIN ref_pub_status h ON (h.id = a.status) 
	WHERE i.status = 'A'
	AND a.status = 'ADD'
	AND i.archived_status IS NULL
	AND a.archived_status IS NULL
	ORDER BY a.insert_date DESC";
	$resultA = $dbf->query($sqlannounce);
	$row_cnt = mysql_num_rows($resultA);	
	while($dbf->next_record())
	{
		$pidArray[$i] = $dbf->f('id');
		$issueArray[$i] = $dbf->f('issue');
		$issn_noArray[$i] = $dbf->f('issn_no');
		$volume_idArray[$i] = $dbf->f('volume_id');
		$volumeDescArray[$i] = $dbf->f('volumeDesc');
		$publishDateArray[$i] = $dbf->f('publishDate');
		$titleArray[$i] = $dbf->f('title');
		$publisherNameDetailArray[$i] = $dbf->f('publisher_name');
		$statusArray[$i]=$dbf->f('status');
		$uploadBy[$i] = $dbf->f('publication_uploader');

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

	<title>List of Publication</title>
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
		function viewDetail(pid) 
		{
				//document.location.href = "../thesis/new_proposal_discussion.php?pid=" + pid;
				//alert ("edit_publication.php?id=" + pid);
				document.location.href = "publication_detail.php?id=" + pid;
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
	<table>
		<tr>
			<td><strong>List of Publication</td>
		</tr>
	</table>
	<table>
		<tr>							
			<td>Please enter searching criteria below:-</td>
		</tr>
		<tr>
			<td><strong>Note: </strong> by default it will display the current publication in which it status has been published.</td>
	</table>
	<br>
	<table>
		<tr>
			<td>ISSN</td>
			<td><span style="font-size:14px">:</span><input type="text" id="issn" name="issn" value="" /></td>

		</tr>
		<tr>
			<td>Volume</td>
			<td><span style="font-size:14px">:</span> <select name="volume" id="volume">
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
			<td><span style="font-size:14px">:</span><input type="text" id="issue" name="issue"/></td>
		</tr>
		<tr>
			<td>Title</td>
			<td><span style="font-size:14px">:</span><input type="text" id="title" name="title" value="" /></td>
		</tr>
		<tr>
			<td>Keyword</td>
			<td><span style="font-size:14px">:</span><input type="text" id="keyword" size="50"  name="keyword" value="" /></td>
		</tr>
		<tr>
			<td>Student Name</td>
			<td><span style="font-size:14px">:</span><input type="text" id="studentName" name="studentName"/></td>
		</tr>
		<tr>
			<td>Matric No</td>
			<td><span style="font-size:14px">:</span><input type="text" id="matricNo" name="matricNo"/><input type="submit" name="btnSearch" value="Search" />
				<span style="color:#FF0000;">Note:</span>
				If no parameters are provided, it will search all.</td>
		</tr>
		
	</table>
	<br>
	<fieldset>
	<legend>Searching Results:- <?=$row_cnt?> record(s) found</legend>
		<? if($row_cnt > 0) {?>
		<div class = "viewA" style="overflow:auto;width: 980px; height: 150px;">
		<? } else { ?>
		<div class = "viewA" style="overflow:auto;width: 980px; height: 100px;">
		<? } ?>
      <table width="100%" class = "thetable" border="1"> <!--thetable2-->
        <tr>
		  <th width="5%">No</th>
		  <th width="10%">ISSN</th>
		  <th width="10%">Volume</th>
		  <th width="10%">Issue</th>
		  <th width="10%">Published Date</th>
		  <th align="left" width="15%">Title</th> 
          <th align="left" width="10%">Publisher</th>
		  <th width="20%" align="left">Student</th>
          <th width="10%">Action</th>
		  
		<?if ($row_cnt > 0) {
			for ($i=0; $i<$inc; $i++){	?>
			<?

			if($i % 2) $color ="first-row"; else $color = "second-row";

			$sqlname = "SELECT name FROM student WHERE matrix_no = '$uploadBy[$i]' ";
			if (substr($insertDateArray[$i],0,2) != '07') { 
				$dbConnStudent= $dbc; 
			} 
			else { 
				$dbConnStudent=$dbc1; 
			}
			$resultName = $dbConnStudent->query($sqlname);
			$dbConnStudent->next_record();
			
			$studName =$dbConnStudent->f('name');

			?>
			</tr>

			<tr class="<?=$color?>">
			  <td align="center"><?=$i+1?>.</td>
			  <td align="center"><?=$issn_noArray[$i]?></td>
			  <td align="center"><?=romanNumerals($volumeDescArray[$i])?></td>
			  <td align="center"><?=$issueArray[$i]?></td>
			  <td align="center"><?=$publishDateArray[$i]?></td>
			  <td><?=$titleArray[$i]?></td> 
			  <td align="left"><?=$publisherNameDetailArray[$i]?></td>
			  <td><?=$studName?><br />(<?=$uploadBy[$i]?>)</td>
			  <td align="center">
			  <input class="edit" id="detail" name="detail" style = " float: right; width:80px; margin-right: 10px" onClick="viewDetail('<?=$pidArray[$i]?>')" type="button" value="View"></td>
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
      </table>
	 </div>
	 </fieldset>
    </form>
</div>
</body>
</html>