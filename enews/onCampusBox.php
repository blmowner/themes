<?php    
    include("include/cfg.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<meta name="author" content="MSUITICWS06" />

	<title>Untitled 2</title>
    <style>
    body {
        font-family: verdana;
        font-size:11px;
    }
    
    a { text-decoration:none; }
        .link a{ text-decoration:none; color:#CC3300;}
        .link a:hover{ color:#000000;}    
    </style>
</head>

<body>
<table width="260" border="0" cellspacing="0" cellpadding="5">
<?php
    $sql = mysql_query("SELECT * FROM colombo_news_event ORDER BY insert_dt DESC LIMIT 4",$CONN2) or die(mysql_error());
    while($rows = mysql_fetch_array($sql)){
?>
    <tr>
		<td align="left" style="border-bottom:1px; border-bottom-style:dashed; border-bottom-color:#999999; padding-bottom:10px; padding-top:20px;"><div class="link"><a href="view_event.php?id=<?php echo $rows['news_event_id']; ?>" target="_blank"><strong><?php echo $rows['news_event_title']; ?></strong></a></div></td>
	</tr>
<?
    }
?>
</table>
</body>
</html>