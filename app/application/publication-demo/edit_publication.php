<?php

include("../../../lib/common.php");
checkLogin();
$user_id=$_SESSION['user_id'];
$publicationId = $_GET['pid'];
$pubDetailId = $_GET['pdid'];
	
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

if($_POST['btnUpdate'] <> "") 
{
	//$publicationDetailId = $_POST['publication_detail_id'];
	$publishedDate = $_POST['pdate'];
	$issue = $_POST['issue'];
	$issn = $_POST['issn_no'];
	$volume = $_POST['volume_id'];
	$publisher = $_POST['publisher'];
	$title = $_POST['title'];
	$add_publication_type = $_POST['add_publication_type'];
	$website = $_POST['website'];
	$add_country = $_POST['add_country'];
	$author = $_POST['author'];
	$keyword = $_POST['keyword'];
	$abstract = $_POST['abstract'];
	$author_Id = $_POST['authorID'];
	$status = $_POST['status'];
	$pubDetailStatus = $_POST['pub_detail_status'];
	
	$msg = Array();

	if(empty($_POST['pdate'])) $msg[] = "<div class=\"error\"><span>Please enter the published date as required below.</span></div>";
	if(empty($_POST['issue'])) $msg[] = "<div class=\"error\"><span>Please enter the issue as required below.</span></div>";
	if(empty($_POST['issn_no'])) $msg[] = "<div class=\"error\"><span>Please enter the ISSN as required below.</span></div>";
	if(empty($_POST['publisher'])) $msg[] = "<div class=\"error\"><span>Please enter the publisher as required below.</span></div>";
	if(empty($_POST['volume_id'])) $msg[] = "<div class=\"error\"><span>Please choose the volume as required below.</span></div>";
	if(empty($_POST['title'])) $msg[] = "<div class=\"error\"><span>Please enter the title as required below.</span></div>";
	if(empty($_POST['author'])) $msg[] = "<div class=\"error\"><span>Please enter the author as required below.</span></div>";
	if($author == " " || $author == "," || $author == ", " || $author == " ," || $author == " , ") $msg[] = "<div class=\"error\"><span>Please enter the author as required below.</span></div>";
	if(empty($_POST['add_country'])) $msg[] = "<div class=\"error\"><span>Please choose the country as required below.</span></div>";
	if(empty($_POST['add_publication_type'])) $msg[] = "<div class=\"error\"><span>Please choose the type of publication as required below.</span></div>";
	if(empty($_POST['website'])) $msg[] = "<div class=\"error\"><span>Please enter the website as required below.</span></div>";
	if(empty($_POST['keyword'])) $msg[] = "<div class=\"error\"><span>Please enter the keyword as required below.</span></div>";
	if(empty($_POST['abstract'])) $msg[] = "<div class=\"error\"><span>Please enter the abstract as required below.</span></div>";
	if(!is_numeric($issue)) $msg[] = "<div class=\"error\"><span>Please enter issue as required below and Issue must be in numeric only.</span></div>";
	if(!is_numeric($issn)) $msg[] = "<div class=\"error\"><span>Please enter issn as required below and ISSN no must be in numeric only.</span></div>";
	
	if(empty($msg))
	{
		$curdatetime = date("Y-m-d H:i:s");
		$sql="UPDATE pg_publication
		SET modify_by='$user_id', modify_date='$curdatetime'
		WHERE id='$publicationId'
		AND status = 'A'
		AND archived_status IS NULL";
		$dbe->query($sql);
		
		if ($pubDetailStatus == "SAV") {
			$sql="UPDATE pg_publication_detail
			SET issue = '$issue', volume_id = '$volume', author = '$author', title = '$title', abstract = '$abstract',
			type_pub_id = '$add_publication_type', website = '$website', country_id = '$add_country', publication_uploader = '$user_id',
			published_date = STR_TO_DATE('$publishedDate','%d-%M-%Y'), keyword = '$keyword', publisher_id = '$publisher',
			modify_by='$user_id', modify_date='$curdatetime'
			WHERE id = '$pubDetailId'
			AND publication_id='$publicationId'
			AND status = 'SAV'
			AND archived_status IS NULL";
			$dbe->query($sql);
			$msg[] = "<div class=\"success\"><span>The publication detail has been updated successfully.</span></div>";
		}
		else if ($pubDetailStatus == "ADD") {
			
			$sql5 = "UPDATE pg_publication_detail
			SET modify_by='$user_id', modify_date='$curdatetime', archived_status = 'ARC', archived_date = '$curdatetime'
			WHERE id = '$pubDetailId'";
			$dbe->query($sql5);
			
			$newPublicationDetailId = runnum('id','pg_publication_detail');
			$sql="INSERT INTO pg_publication_detail 
			(id, issue, volume_id, publication_id, author, title, publisher_id, abstract, type_pub_id, website,
			country_id, publication_uploader, published_date, insert_by, insert_date, status, keyword, modify_by, modify_date)
			VALUES ('$newPublicationDetailId', '$issue', '$volume', '$publicationId', '$author', '$title', '$publisher', '$abstract', 
			'$add_publication_type', '$website', '$add_country', '$user_id', STR_TO_DATE('$publishedDate','%d-%M-%Y'), 
			'$user_id', '$curdatetime', 'ADD', '$keyword', '$user_id', '$curdatetime')";
			$dbe->query($sql);
			
			$msg[] = "<div class=\"success\"><span>The publication detail has been updated successfully.</span></div>";
		}
	}
	
}

if($_POST['btnSave'] <> "") 
{
	//$publicationDetailId = $_POST['publication_detail_id'];
	$publishedDate = $_POST['pdate'];
	$issue = $_POST['issue'];
	$issn = $_POST['issn_no'];
	$volume = $_POST['volume_id'];
	$publisher = $_POST['publisher'];
	$title = $_POST['title'];
	$add_publication_type = $_POST['add_publication_type'];
	$website = $_POST['website'];
	$add_country = $_POST['add_country'];
	$author = $_POST['author'];
	$keyword = $_POST['keyword'];
	$abstract = $_POST['abstract'];
	$author_Id = $_POST['authorID'];
	$status = $_POST['status'];
			
	$msg = Array();
	if(empty($_POST['pdate'])) $msg[] = "<div class=\"error\"><span>Please enter the published date as required below.</span></div>";
	if(empty($_POST['issue'])) $msg[] = "<div class=\"error\"><span>Please enter the issue as required below.</span></div>";
	if(empty($_POST['issn_no'])) $msg[] = "<div class=\"error\"><span>Please enter the ISSN as required below.</span></div>";
	if(empty($_POST['publisher'])) $msg[] = "<div class=\"error\"><span>Please enter the publisher as required below.</span></div>";
	if(empty($_POST['volume_id'])) $msg[] = "<div class=\"error\"><span>Please choose the volume as required below.</span></div>";
	if(empty($_POST['title'])) $msg[] = "<div class=\"error\"><span>Please enter the title as required below.</span></div>";
	if(empty($_POST['author'])) $msg[] = "<div class=\"error\"><span>Please enter the author as required below.</span></div>";
	if($author == " " || $author == "," || $author == ", " || $author == " ," || $author == " , ") $msg[] = "<div class=\"error\"><span>Please enter the author as required below.</span></div>";
	if(empty($_POST['add_country'])) $msg[] = "<div class=\"error\"><span>Please choose the country as required below.</span></div>";
	if(empty($_POST['add_publication_type'])) $msg[] = "<div class=\"error\"><span>Please choose the type of publication as required below.</span></div>";
	if(empty($_POST['website'])) $msg[] = "<div class=\"error\"><span>Please enter the website as required below.</span></div>";
	if(empty($_POST['keyword'])) $msg[] = "<div class=\"error\"><span>Please enter the keyword as required below.</span></div>";
	if(empty($_POST['abstract'])) $msg[] = "<div class=\"error\"><span>Please enter the abstract as required below.</span></div>";
	if(!is_numeric($issue)) $msg[] = "<div class=\"error\"><span>Please enter issue as required below and Issue must be in numeric only.</span></div>";
	if(!is_numeric($issn_no)) $msg[] = "<div class=\"error\"><span>Please enter issn as required below and ISSN no must be in numeric only.</span></div>";
	
	if(empty($msg)) {		
		$curdatetime = date("Y-m-d H:i:s");
		$sql="UPDATE pg_publication
		SET modify_by='$user_id', modify_date='$curdatetime'
		WHERE id='$publicationId'
		AND status = 'A'
		AND archived_status IS NULL";
		$dbe->query($sql);
		
		$sql="UPDATE pg_publication_detail
		SET author = '$author', title = '$title', abstract = '$abstract',
		type_pub_id = '$add_publication_type', website = '$website', country_id = '$add_country', publication_uploader = '$user_id',
		published_date = STR_TO_DATE('$publishedDate','%d-%M-%Y'), keyword = '$keyword', publisher_id = '$publisher',
		modify_by='$user_id', modify_date='$curdatetime'
		WHERE id = '$pubDetailId'
		AND publication_id='$publicationId'
		AND status = 'SAV'
		AND archived_status IS NULL";
		$dbe->query($sql);		
			
		$msg[] = "<div class=\"success\"><span>The publication detail has been saved successfully.</span></div>";
		
	}	
}

if($_POST['btnAdd'] <> "") 
{
	//$publicationDetailId = $_POST['publication_detail_id'];
	$publishedDate = $_POST['pdate'];
	$issue = $_POST['issue'];
	$issn = $_POST['issn_no'];
	$volume = $_POST['volume_id'];
	$publisher = $_POST['publisher'];
	$title = $_POST['title'];
	$add_publication_type = $_POST['add_publication_type'];
	$website = $_POST['website'];
	$add_country = $_POST['add_country'];
	$author = $_POST['author'];
	$keyword = $_POST['keyword'];
	$abstract = $_POST['abstract'];
	$author_Id = $_POST['authorID'];
	$status = $_POST['status'];
			
	$msg = Array();
	if(empty($_POST['pdate'])) $msg[] = "<div class=\"error\"><span>Please enter the published date as required below.</span></div>";
	if(empty($_POST['issue'])) $msg[] = "<div class=\"error\"><span>Please enter the issue as required below.</span></div>";
	if(empty($_POST['issn_no'])) $msg[] = "<div class=\"error\"><span>Please enter the ISSN as required below.</span></div>";
	if(empty($_POST['publisher'])) $msg[] = "<div class=\"error\"><span>Please enter the publisher as required below.</span></div>";
	if(empty($_POST['volume_id'])) $msg[] = "<div class=\"error\"><span>Please choose the volume as required below.</span></div>";
	if(empty($_POST['title'])) $msg[] = "<div class=\"error\"><span>Please enter the title as required below.</span></div>";
	if(empty($_POST['author'])) $msg[] = "<div class=\"error\"><span>Please enter the author as required below.</span></div>";
	if($author == " " || $author == "," || $author == ", " || $author == " ," || $author == " , ") $msg[] = "<div class=\"error\"><span>Please enter the author as required below.</span></div>";
	if(empty($_POST['add_country'])) $msg[] = "<div class=\"error\"><span>Please choose the country as required below.</span></div>";
	if(empty($_POST['add_publication_type'])) $msg[] = "<div class=\"error\"><span>Please choose the type of publication as required below.</span></div>";
	if(empty($_POST['website'])) $msg[] = "<div class=\"error\"><span>Please enter the website as required below.</span></div>";
	if(empty($_POST['keyword'])) $msg[] = "<div class=\"error\"><span>Please enter the keyword as required below.</span></div>";
	if(empty($_POST['abstract'])) $msg[] = "<div class=\"error\"><span>Please enter the abstract as required below.</span></div>";
	if(!is_numeric($issue)) $msg[] = "<div class=\"error\"><span>Please enter issue as required below and Issue must be in numeric only.</span></div>";
	if(!is_numeric($issn_no)) $msg[] = "<div class=\"error\"><span>Please enter issn as required below and ISSN no must be in numeric only.</span></div>";
	
	if(empty($msg)) {		
		$curdatetime = date("Y-m-d H:i:s");
		$sql="UPDATE pg_publication
		SET modify_by='$user_id', modify_date='$curdatetime'
		WHERE id='$publicationId'
		AND status = 'A'
		AND archived_status IS NULL";
		$dbe->query($sql);
		
		$sql="UPDATE pg_publication_detail
		SET author = '$author', title = '$title', abstract = '$abstract',
		type_pub_id = '$add_publication_type', website = '$website', country_id = '$add_country', publication_uploader = '$user_id',
		published_date = STR_TO_DATE('$publishedDate','%d-%M-%Y'), keyword = '$keyword', publisher_id = '$publisher',
		status = 'ADD', modify_by='$user_id', modify_date='$curdatetime'
		WHERE id = '$pubDetailId'
		AND publication_id='$publicationId'
		AND status = 'SAV'
		AND archived_status IS NULL";
		$dbe->query($sql);		
			
		$msg[] = "<div class=\"success\"><span>The publication detail has been added successfully.</span></div>";
		
	}	
}

$sql="SELECT a.id,a.issue, h.id as publication_id, h.issn_no, a.volume_id, a.title, 
DATE_FORMAT(a.published_date,'%d-%b-%Y') AS publishedDate, a.type_pub_id,
a.abstract, a.publisher_id, a.website, a.country_id, a.keyword,
b.id AS publisherId,
c.description as typePubDesc, c.id AS pub_type_id,
e.id AS countryId, e.description AS country_name,
f.id as volumeId, f.volume as volumeDesc, a.author, a.status as pub_detail_status
FROM pg_publication h 
LEFT JOIN pg_publication_detail a ON (a.publication_id = h.id)
LEFT JOIN ref_publisher b ON (b.id = a.publisher_id)
LEFT JOIN ref_publication_type c ON (c.id = a.type_pub_id)
LEFT JOIN ref_country e ON (e.id = a.country_id)
LEFT JOIN ref_volume f ON (f.id = a.volume_id)
WHERE a.publication_uploader = '$user_id'
AND h.id = '$publicationId'
AND a.id = '$pubDetailId'
AND h.status = 'A'
AND h.archived_status IS NULL
AND a.archived_status IS NULL";
		
$result_sql = $db->query($sql);
$db->next_record();
$row_cnt = mysql_num_rows($result_sql);	

//$publicationDetailId = $db->f('id');
$issue = $db->f('issue');
$issn_no = $db->f('issn_no');
//$publicationId = $db->f('publication_id');
$volume_id = $db->f('volume_id');
$publishedDate = $db->f('publishedDate');
$title = $db->f('title');
$publisher_id = $db->f('publisher_id');
$type_pub_id = $db->f('type_pub_id');
$abstract = $db->f('abstract');
$website = $db->f('website');
$country_id = $db->f('country_id');
$keyword = $db->f('keyword');
$publisherId = $db->f('publisherId');
$typePubDesc = $db->f('typePubDesc');
$pub_type_id = $db->f('pub_type_id');
$countryId = $db->f('countryId');
$country_name = $db->f('country_name');
$volumeId = $db->f('volumeId');
$volumeDesc = $db->f('volumeDesc');
$author = $db->f('author');
$pubDetailStatus = $db->f('pub_detail_status');
?>
<style>
.idd {
font-family:Verdana;
font-size:11px;
}
</style>
<?
	
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
<script>
function newAttachment(pid,pdid) {
    var ask = window.confirm("Ensure your publication has been saved before proceed or otherwise the last change will be discarded.\nClick OK to proceed or CANCEL to stay on the same page.");
    if (ask) {
		document.location.href = "../publication/edit_publication_attachment.php?pid=" + pid + "&pdid=" + pdid;

    }
}
</script>

<body>
<!--<div class="margin-5 padding-5 outer">-->
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
	<input type="hidden" id="issue" name="issue" value="<?=$issue?>"/>
	<input type="hidden" id="issn_no" name="issn_no" value="<?=$issn_no?>"/>
	<input type="hidden" id="volume_id" name="volume_id" value="<?=$volume_id?>"/>
	<input type="hidden" id="pub_detail_status" name="pub_detail_status" value="<?=$pubDetailStatus?>"/>
	<?  $sql3 = "SELECT volume
		FROM ref_volume
		WHERE id = '$volume_id'
		AND status = 'A'
		ORDER BY order_by";
				
		$result_sql3 = $dbg->query($sql3);
		$dbg->next_record();
		$theVolume=$dbg->f('volume');
	?>
								
	<fieldset><legend>Publication</legend>
		<table class="idd">
			<tr>
				<td style="background-color: rgba(105, 162, 255, 0.7);">ISSN </td>
				<td style="background-color: rgba(0, 0, 0, 0.1); "><label><?=$issn_no?></label></td>
				<td width="10%" style="background-color: rgba(105, 162, 255, 0.7);">Volume </td>
				<td style="background-color: rgba(0, 0, 0, 0.1); "><label><?=romanNumerals($theVolume)?></label></td>
				<td width="10%" style="background-color: rgba(105, 162, 255, 0.7);">Issue</td>
				<td style="background-color: rgba(0, 0, 0, 0.1); "><label><?=$issue?></label></td>
			</tr>
			<tr>
				<td height="32" style="background-color: rgba(105, 162, 255, 0.7);"><font color="#FF0000">*</font>Publication Date</td>
				<td colspan="5" style="background-color: rgba(0, 0, 0, 0.1); ">
				
				<input type="text" id="pdate" readonly="readonly" name="pdate" value="<?= $publishedDate?>"/></td>
					<?	$jscript .= "\n" . '$( "#pdate" ).datepicker({
													changeMonth: true,
													changeYear: true,
													yearRange: \'-100:+0\',
													dateFormat: \'dd-M-yy\',
													maxDate: new Date
												});';				 
					?>
			</tr>
			
			<tr>
				<td style="background-color: rgba(105, 162, 255, 0.7);"><font color="#FF0000">*</font>Title </td>
				<td colspan="5" style="background-color: rgba(0, 0, 0, 0.1); "><textarea name="title" cols="50" rows="2" id="title" ><?=$title?></textarea></td>
			</tr>
			<tr>
				<td style="background-color: rgba(105, 162, 255, 0.7);"><font color="#FF0000">*</font>Keyword</td>
				<td style="background-color: rgba(0, 0, 0, 0.1); " colspan="5"><textarea cols="50" type="text" id="keyword" name="keyword"><?=$keyword?></textarea>			</td>
			</tr>
			<tr>
				<td style="background-color: rgba(105, 162, 255, 0.7);"><font color="#FF0000">*</font>Abstract</td>
				<td colspan="5" style="background-color: rgba(0, 0, 0, 0.1); ">
				<textarea cols="50" type="text" id="abstract" name="abstract"><?=$abstract?></textarea>
				</td>
			</tr>
			<tr>
			<?
				$sql1 = "SELECT id, publisher_name
				FROM ref_publisher
				WHERE status = 'A'
				ORDER BY id";

				$result_sql1 = $dbj->query($sql1);
				$dbj->next_record();
				$row_cnt_sql = mysql_num_rows($result_sql1);			
				?>

				<td style="background-color: rgba(105, 162, 255, 0.7);"><font color="#FF0000">*</font>Publisher</td>
				<td colspan="5" style="background-color: rgba(0, 0, 0, 0.1); ">
					<select name="publisher" id="publisher">
						<option value="" selected="selected"></option>
						<?
						do {
							$publisherId = $dbj->f('id');
							$publisherName = $dbj->f('publisher_name');
							
							if ($publisherId==$publisher_id) {
								?><option value="<?=$publisherId?>" selected="selected"><?=$publisherName?></option><?
							}
							else {						
								?><option value="<?=$publisherId?>"><?=$publisherName?></option><? 
								}					
						} while ($dbj->next_record());?>				
				  </select>
			  </td>
			</tr>
			<tr>
				<td style="background-color: rgba(105, 162, 255, 0.7);"><font color="#FF0000">*</font>Author</td>
				<td colspan="5" style="background-color: rgba(0, 0, 0, 0.1); ">
				 <textarea cols="50" type="text" id="author" name="author"><?=$author?></textarea>
				<br>
				<span style="color:#FF0000">Note</span>: Only information provided in alphabetical, commas and <br>fullstop/period will be accepted.
				</td>
			</tr>
			<tr>
				<td style="background-color: rgba(105, 162, 255, 0.7);"><font color="#FF0000">*</font>Type of Publication</td>
				<td colspan="5" style="background-color: rgba(0, 0, 0, 0.1); "><select name="add_publication_type" id="add_publication_type">
					<?
					$sql = "SELECT id, description
					FROM ref_publication_type
					WHERE status = 'A'
					ORDER BY description";

					$result_sql = $dba->query($sql);
					$dba->next_record();
					$row_cnt_sql = mysql_num_rows($result_sql);		
					do {
						$publicationTypeId = $dba->f('id');
						$publicationTypeDesc = $dba->f('description');
						$defaultId = $dba->f('default_id');
						if ($publicationTypeId==$type_pub_id) {
							?><option value="<?=$publicationTypeId?>" selected="selected"><?=$publicationTypeDesc?></option><?
						}
						else if($defaultId=="Y" && empty($type_pub_id))
						{
							?><option value="<?=$publicationTypeId?>" selected="selected"><?=$publicationTypeDesc?></option><?
						}					
						else {						
							?><option value="<?=$publicationTypeId?>"><?=$publicationTypeDesc?></option><? 
							}					
					} while ($dba->next_record());?>				
																																
					</select></td>			
			</tr>
			<td style="background-color: rgba(105, 162, 255, 0.7);"><font color="#FF0000">*</font>Website</td>
				<td colspan="5" style="background-color: rgba(0, 0, 0, 0.1); ">
				<input type="text" id="website" name="website" value="<?=$website?>"/></td>
			<tr>
				<?
				$sql = "SELECT id, description, default_id
				FROM ref_country
				WHERE status = 'A'
				ORDER BY description";

				$result_sql = $dbb->query($sql);
				$dbb->next_record();
				$row_cnt_sql = mysql_num_rows($result_sql);			
				?>

			<td style="background-color: rgba(105, 162, 255, 0.7);"><font color="#FF0000">*</font>Country</td>
				<td colspan="5" style="background-color: rgba(0, 0, 0, 0.1); "><select name="add_country" id="add_country">
				  <?
					do {
						$countryId = $dbb->f('id');
						$countryDesc = $dbb->f('description');
						$defaultId = $dbb->f('default_id');
						if ($defaultId=="Y" && empty($country_id)) 
						{ 
							?><option value="<?=$countryId?>" selected="selected"><?=$countryDesc?></option><? 
						}
						else if($country_id == $countryId)
						{
							?><option value="<?=$countryId?>" selected="selected"><?=$countryDesc?></option><?
						}
						else 
						{
							?><option value="<?=$countryId?>"><?=$countryDesc?></option><?														
						}
					} while ($dbb->next_record());?>
				</select></td>
			</tr>
			<? 
			$sqlUpload="SELECT COUNT(*) as total  
			FROM file_upload_publication
			WHERE publication_id = '$publicationId' 
			/*AND pub_detail_id = '$pubDetailId'*/
			/*AND uploaded_by = '$user_id'
			AND archived_status IS NULL*/";			
			
			$result = $dbf->query($sqlUpload); 
			$dbf->next_record();
			$attachment = $dbf->f('total');
			
			if($attachment == '0')
			{
				$a = '';
			}
			else
			{
				$a = "(".$attachment.")";
			}
			?>

			<tr>
				<td style="background-color: rgba(105, 162, 255, 0.7);">Attachment</td>
				<td colspan="5" style="background-color: rgba(0, 0, 0, 0.1); "><button type="button" name="btnAttachment" value="Attachment" onClick="return newAttachment('<?=$publicationId?>','<?=$pubDetailId?>')" >
				Attachment <FONT COLOR="#FF0000"><sup><?=$a?></sup></FONT></button></td>
			</tr>		
		</table>
		</fieldset>
		<?if ($pubDetailStatus=="ADD") {?>
			<table>
				<tr>
					<td>
						<input type="submit" id="btnUpdate" name="btnUpdate" value = "Update" />					
						<input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../publication/manage_publication.php';" />
						<span style="color:#FF0000; font-family:Verdana;font-size:11px;">Note:</span> <span style="font-family:Verdana;font-size:11px;">Field marks with (</span><span style="color:#FF0000">*</span><span style="font-family:Verdana;font-size:11px;">) is compulsory.</span><br />
					<span style="font-family:Verdana;font-size:11px;"></span></td>
			  </tr>
			</table>
		<?}
		else if ($pubDetailStatus=="SAV") {
			?>
			<table>
				<tr>
				  <td align="right">
					<input type="submit" id="btnSave" name="btnSave" value = "Save" />
					<input type="submit" id="btnAdd" name="btnAdd" value = "Add" />
					<input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../publication/manage_publication.php';" /></td>
				  <td align="left"><span style="color:#FF0000; font-family:Verdana;font-size:11px;">Note:</span> <span style="font-family:Verdana;font-size:11px;">Field marks with (</span><span style="color:#FF0000">*</span><span style="font-family:Verdana;font-size:11px;">) is compulsory.</span><br />
					<span style="font-family:Verdana;font-size:11px;"></span></td>
				</tr>
			</table>
			<?
		}?>
	
    </form>
<script>
	<?=$jscript;?>
</script>
</body>
</html>