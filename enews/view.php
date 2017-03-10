<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
a:link {
	text-decoration: none;
	color: #666666;
}
a:visited {
	text-decoration: none;
	color: #666666;
}
a:hover {
	text-decoration: none;
	color: #006699;
	border-bottom-width: 1px;
	border-bottom-style: dotted;
	border-bottom-color: #006699;
}
a:active {
	text-decoration: none;
	color: #666666;
}
body,td,th a:active{
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 9pt;
}
img {
	border: 1px solid #999999;
	margin-bottom: 5px;
}
-->
</style>
</head>

<body>
<?php
include("include/cfg.php");
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <?php
						
						$sql = "SELECT fid,imgtype,imgdata,imgtypeT,imgdataT,source,title,description,advert_date as advert_date1,DATE_FORMAT(advert_date, '%D %M %Y') as advert_date FROM eadvertorial_upload WHERE typeInst='MSU_COLOMBO'";
						$sql .= " ORDER BY advert_date1 DESC";
						$sql .= " LIMIT 3";
						$sql = mysql_query($sql,$CONN) or die(mysql_error());
						$num = mysql_num_rows($sql) or die(mysql_error());
						$sql2 = mysql_query("SELECT fid,imgtype,imgdata,imgtypeT,imgdataT,source,title,description,advert_date as advert_date1,DATE_FORMAT(advert_date, '%D %M %Y') as advert_date FROM eadvertorial_upload WHERE typeInst='MSU_COLOMBO' ORDER BY advert_date1 DESC LIMIT 3",$CONN) or die(mysql_error());
						$num2 = mysql_num_rows($sql2) or die(mysql_error());						
						$tab_color = array("#F0F0F0", "#FDFDFD");
						$i = 1;						
						while($row = mysql_fetch_array($sql)) {
					?>
  <tr valign="top"> 
    <td width="2%" align="left"></td>
    <td width="9%"><img src="display_image_thumb.php?fid=<?=$row['fid'];?>" width="65" height="65" hspace="10" vspace="0"  border="0" />    </td>
    <td width="89%"><font size="-4" face="Verdana, Arial, Helvetica, sans-serif">
      <? $tipu=md5('enews');?>
      <a href="display_image.php?mainid=<?=$tipu;?>&fid=<?=$row['fid'];?>&<?=$tipu;?>&subid=<?=$tipu;?>" target="_blank"> 
      <?php
							if(!empty($row['advert_date'])) {
								echo strip_tags($row['advert_date'])."";
							}
							
							else {
								echo "None";
							}
						?>
		<?php
							if(!empty($row['title'])) {
								echo "<br />" . strip_tags($row['title'])."";
							}
							
							else {
								echo "None";
							}
						?>
		<?php
							if(!empty($row['source'])) {
								echo "<br />" . strip_tags($row['source'])."";
							}
							
							else {
								echo "None";
							}
						?>								
    </a></font><br /></td>
  </tr>
  <?php
						$i++;
						}
						
						mysql_close($CONN);
					?>
</table>

</body>
</html>
