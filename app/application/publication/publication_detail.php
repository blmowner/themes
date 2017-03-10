<?php

    include("../../../lib/common.php");
    checkLogin();
	$PID = $_GET['id'];
	
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



?>
<style>
.idd {
font-family:Verdana;
font-size:11px;
}
.idd td{
background-color: rgba(105, 162, 255, 0.7);

}
</style>
<?
	$sql_thesis="SELECT a.id,a.issue, a.publication_id, a.volume_id, a.title, DATE_FORMAT(a.published_date,'%d-%b-%Y') AS publishDate, a.type_pub_id, a.abstract, a.publisher_id, a.website, a.country_id, a.keyword,a.publication_uploader,
	b.id AS publisherId,b.publisher_name,
	c.description as typePubDesc, c.id AS pub_type_id,
	e.id AS countryId, e.description AS country_name,
	f.id as volumeId, f.volume, a.author
	FROM pg_publication_detail a
	LEFT JOIN ref_publisher b ON (b.id = a.publisher_id)
	LEFT JOIN ref_publication_type c ON (c.id = a.type_pub_id)
	LEFT JOIN ref_country e ON (e.id = a.country_id)
	LEFT JOIN ref_volume f ON (f.id = a.volume_id)
	WHERE a.id = '$PID'";
				
	$result_sql_thesis = $db->query($sql_thesis);
	$resultsqlthesis = $db->next_record();
	 
	//pg_publication_detail//
	$pid = $db->f('id');
	$issue = $db->f('issue');
	$publication_id = $db->f('publication_id');
	$volume_id = $db->f('volume_id');
	$publishDate = $db->f('publishDate');
	$title = $db->f('title');
	$publisher_id = $db->f('publisher_id');
	$type_pub_id = $db->f('type_pub_id');
	$abstract = $db->f('abstract');
	$publisher_id = $db->f('publisher_id');
	$website = $db->f('website');
	$country_id = $db->f('country_id');
	$keyword = $db->f('keyword');
	$publication_uploader = $db->f('publication_uploader');
	//ref_publisher//
	$publisherIdDetail = $db->f('publisherId');
	$publisherNameDetail = $db->f('publisher_name');
	//ref_publication_type//
	$typePubDesc = $db->f('typePubDesc');
	$pub_type_id = $db->f('pub_type_id');
	//ref_country//
	$countryId = $db->f('countryId');
	$country_name = $db->f('country_name');
	//ref_volume//
	$volumeId = $db->f('volumeId');
	$volume = $db->f('volume');
	$author = $db->f('author');
	
	
	$sqlissn = "SELECT id,issn_no FROM pg_publication WHERE id = '$publication_id'";
	$result_sql_isn = $dbu->query($sqlissn);
	$resultsqlisn = $dbu->next_record();
	 
	$issn_no = $dbu->f('issn_no');
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang; ?>" lang="<?php echo $lang; ?>">

<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
	<meta name="author" content="MJMZ" />

	<title>Publication Detail</title>
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
function newAttachment(pid) {
    var ask = window.confirm("Ensure your publication has been saved before proceed or otherwise the last change will be discarded.\nClick OK to proceed or CANCEL to stay on the same page.");
    if (ask) {
		document.location.href = "../publication/edit_publication_attachment.php?pid=" + pid;

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
	<fieldset><legend>Publication Detail</legend>
	<table width="657" class="idd">
		<tr>
			<td width="132" height="32" style="">Publication Date</td>
			<td colspan="5" style="background-color: rgba(0, 0, 0, 0.1); "><?= $publishDate?>			
		</tr>
		<tr>
			<td width="84" style="background-color: rgba(105, 162, 255, 0.7);"> ISSN </td>
			<td width="106" style="background-color: rgba(0, 0, 0, 0.1) "><?=$issn_no?></td>		
			<td width="89" style="background-color: rgba(105, 162, 255, 0.7);"> Volume </td>
			<td width="101" style="background-color: rgba(0, 0, 0, 0.1) "><?=romanNumerals($volume)?></td>
			<td style="background-color: rgba(105, 162, 255, 0.7);">Issue</td>
			<td width="117" style="background-color: rgba(0, 0, 0, 0.1)"><?=$issue?></td>
		
		</tr>
		<tr>
			<td style="background-color: rgba(105, 162, 255, 0.7);">Title </td>
			<td colspan="5" style="background-color: rgba(0, 0, 0, 0.1) "><?=$title?></td>
		</tr>
		<tr>
			<td style="background-color: rgba(105, 162, 255, 0.7);">Keyword</td>
			<td style="background-color: rgba(0, 0, 0, 0.1); " colspan="5"><?=$keyword?></td>
		</tr>
		<tr>
			<td style="background-color: rgba(105, 162, 255, 0.7);">Abstract</td>
			<td colspan="5" style="background-color: rgba(0, 0, 0, 0.1); "><?=$abstract?></td>
		</tr>
		<tr>		
			<td style="background-color: rgba(105, 162, 255, 0.7);">Publisher</td>
			<td colspan="5" style="background-color: rgba(0, 0, 0, 0.1); "><?=$publisherNameDetail?></td>
		</tr>
		<tr>
			<td style="background-color: rgba(105, 162, 255, 0.7);">Author</td>
			<td colspan="5" style="background-color: rgba(0, 0, 0, 0.1); "><label><?=$author?></label></td>
		</tr>
		<tr>
			<td style="background-color: rgba(105, 162, 255, 0.7);">Type of Publication</td>
			<td colspan="5" style="background-color: rgba(0, 0, 0, 0.1); "><?=$typePubDesc?></td>			
		</tr>
		<td style="background-color: rgba(105, 162, 255, 0.7);">Website</td>
			<td colspan="5" style="background-color: rgba(0, 0, 0, 0.1); "><?=$website?></td>
		
		<tr>
		<td style="background-color: rgba(105, 162, 255, 0.7);">Country</td>
			<td colspan="5" style="background-color: rgba(0, 0, 0, 0.1); "><?=$country_name?></td>
		</tr>
		<? 
		
		$namefileArray = array();
		$fu_cdArray = array();
		$i = 0;
		$inc = 0;

		$sqlUpload="SELECT * FROM file_upload_publication
		WHERE publication_id = '$pid' 
		AND uploaded_by = '$publication_uploader'";			
		
		$result = $dbf->query($sqlUpload); 
		while($dbf->next_record())
		{
			$namefile = $dbf->f('fu_document_filename');
			$fu_cd = $dbf->f('fu_cd');
			$namefileArray[$i] = $namefile;
			$fu_cdArray[$i] = $fu_cd;
			$i++;
			$inc++;
		}
		?>

		<tr>
			<td style="background-color: rgba(105, 162, 255, 0.7);">Attachment</td>
			<td colspan="5" style="background-color: rgba(0, 0, 0, 0.1); ">
			<? for ($i=0; $i<$inc; $i++)
			{?>
			
			<a href="download.php?id=<?=$fu_cdArray[$i];?>" target="_blank"><?php echo $namefileArray[$i] ?>
		 	<img src="../images/download.png" width="20" height="19" style="border:0px;" title="Download <?=$namefileArray[$i]?>"></a>
				
			<? 
				if($i%4 == 0 && $i !=0)
				{
				   echo "<br>";
				}  
			} ?>
			</td>
		</tr>		
	</table>
	</fieldset>
	<table>
		<tr>
		<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../publication/list_of_publication_general.php';" /></td>
		</tr>
	</table>
	
    </form>
</body>
</html>