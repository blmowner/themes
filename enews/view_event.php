<?php
    include("include/cfg.php");

?>
<?php
	
	$id = $_GET['id'];
	
	$sql = mysql_query("SELECT * FROM colombo_news_event WHERE news_event_id = '".mysql_real_escape_string($id)."'",$CONN2) or die(mysql_error());
	$row = mysql_fetch_array($sql);
	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang; ?>" lang="<?php echo $lang; ?>">
<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
	<meta name="author" content="MJMZ" />
<link rel="stylesheet" href="../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
<link rel="stylesheet" type="text/css" href="../../theme/css/<?php echo $css; ?>" />
<title>Untitled Document</title>
<script src="../../lib/js/datePicker/jquery-1.5.1.js"></script>
<script src="../../lib/js/datePicker/jquery.ui.core.js"></script>
<script src="../../lib/js/datePicker/jquery.ui.widget.js"></script>
<script src="../../lib/js/datePicker/jquery.ui.datepicker.js"></script>
<!--script type="text/javascript" src="../../lib/js/ckeditor/ckeditor.js"></script-->
<script>
$(function() {
	$( "#datepicker1" ).datepicker();
});

$(function() {
	$( "#datepicker2" ).datepicker();
});
</script>
<script> 
function toggleview(element1) 
{   
    element1 = document.getElementById(element1);   
    if (element1.style.display == 'block' || element1.style.display == '')      
        element1.style.display = 'none';   
    else      
        element1.style.display = 'block';   
return;
}			

</script>
</head>
<?php 
    $row['news_event_news'] == "Y" ? $toggle = "" : $toggle = "toggleview('show_event_col');";
?>


<body onload="<?php echo $toggle; ?>">
<div class="padding-5 margin-5 outer">
    <h3 style="font-size: 12pt;">News / Event Details</h3>
    <?php
        if(!empty($msg)) {
            foreach($msg as $err) {
                echo $err;
            }
        }
    ?>
    <form id="form-set">
    <div class="float-left" style="width: 50%;">
        <label class="labeling">Title :</label><?php echo stripslashes($row['news_event_title']); ?><br clear="all" />
        <label class="labeling">Attachment :</label><a href="../include/download.php?id=<?php echo $row['news_event_id'] ?>&table=colombo_news_event&where=news_event_id&theArray=<?php //echo load_constant('COLOMBO_NEWS_EVENT_DOWNLOAD') ?>"><?php echo $row['news_event_file_name']; ?></a><br clear="all" />
        <label class="labeling">Description :</label><div style="text-align: justify; padding-right: 8px; overflow: auto; height: 155px;"><?php echo stripslashes($row['news_event_desc']); ?></div><br clear="all" />
    </div>

    <div id='show_event_col' style='display:none; float: right; width: 40%;'> 
        <font style="font-size: 12pt; width:40%; display:block; border-bottom:1px solid #000000;">Event Details</font><br />
        <label class="labeling">Venue :</label><?php echo $row['news_event_venue']; ?><br />
        <label class="labeling">Start Date :</label><?php echo $row['news_event_start_dt']; ?><br />
        <label class="labeling">End Date :</label><?php echo $row['news_event_end_dt']; ?><br />
        <label class="labeling">Time :</label><?php echo $row['news_event_time']; ?><br /><br />
        
    </div><br clear="all" />

        
    
      
        
        
    </form>
    <a align="center" href="#" onclick="parent.$.fn.colorbox.close();">[ Close ]</a>
</div>
</body>
</html>