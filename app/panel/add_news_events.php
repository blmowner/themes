<?php

    include("../../lib/common.php");
    checkLogin();
    
	if(isset($_POST['sub']))
	{		
	   $msg = array();
       if(empty($_POST['title'])) $msg[] = "<div class=\"error\"><span>Please insert the title!</span></div>";
       if(empty($_POST['content'])) $msg[] = "<div class=\"error\"><span>Please insert the description!</span></div>";
       
       
       //generate running number for news/event
       $news_event_id = run_num('news_event_id', 'colombo_news_event');
       
       if(empty($msg)) {
        
            $thefileName = $_FILES['attachment']['name'];
            $tempName = $_FILES['attachment']['tmp_name'];
            $fileType = $_FILES['attachment']['type'];
            
            if(is_uploaded_file($tempName)) { // if the attachment is not empty
                
                
                $file = addslashes(file_get_contents($tempName));
                
                $sql= "INSERT INTO colombo_news_event (
                                             news_event_id, 
                                             news_event_title, 
                                             news_event_desc, 
                                             news_event_news, 
                                             news_event_venue,
                                             news_event_start_dt, 
                                             news_event_end_dt, 
                                             news_event_time, 
                                             news_event_file_name, 
                                             news_event_file_type, 
                                             news_event_file, 
                                             insert_by, 
                                             insert_dt)
                                       VALUES (
                                             '$news_event_id', 
                                             '".addslashes($_POST['title'])."',
                                             '".$_POST['content']."',
                                             '".$_POST['type']."', 
                                             '".$_POST['venue']."', 
                                             '".$_POST['event_start_date']."', 
                                             '".$_POST['event_end_date']."',
                                             '".$_POST['event_time']."', 
                                             '$thefileName', 
                                             '$fileType', 
                                             '$file', 
                                             '".$_SESSION['user_id']."', 
                                             now())";	
                
                $db->query($sql);
                
                tracking($_SESSION['user_id'], load_constant("INSERT"), "INSERT NEWS/EVENTS"); //($uid, $activity, $module)
                echo "<script>parent.$.fn.colorbox.close();</script>";
            
            //end of the attachment
            } elseif (!is_uploaded_file($_POST['attachment'])) {
                
                $sql= "INSERT INTO colombo_news_event (news_event_id, news_event_title, news_event_desc, news_event_news, news_event_venue,
                                                       news_event_start_dt, news_event_end_dt, news_event_time, insert_by, insert_dt)
                       VALUES ('$news_event_id', '".addslashes($_POST['title'])."','".$_POST['content']."','".$_POST['type']."', 
                               '".$_POST['venue']."', '".$_POST['event_start_date']."', '".$_POST['event_end_date']."',
                               '".$_POST['event_time']."', '".$_SESSION['user_id']."', now())";		
                		
                $db->query($sql); 
                
                tracking($_SESSION['user_id'], load_constant("INSERT"), "INSERT NEWS/EVENTS"); //($uid, $activity, $module)
                echo "<script>parent.$.fn.colorbox.close();</script>";
            }
        
       }
       
	}
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

<body>
<div class="padding-5 margin-5 outer">
    <h3 style="font-size: 12pt;">Add News or Event</h3>
    <div class="info"><span><?php echo load_lang('compulsary'); ?></span></div><br />
    <?php
        if(!empty($msg)) {
            foreach($msg as $err) {
                echo $err;
            }
        }
    ?>
    <form method="post" id="form-set" action="add_news_events.php" enctype="multipart/form-data">
    <div class="float-left">
        <label class="labeling">* Title :</label><input type="text" name="title" style="width:300px;" value="" /><br clear="all" />
        <label class="labeling">* Description :</label><textarea name="content" rows="5" cols="40"></textarea><br clear="all" />
        <label class="labeling">Attachment (optional) :</label><input type="file" name="attachment" /><br /><br clear="all" />  
        <label class="labeling">Type :</label><label><input type="radio" name="type" value="Y" checked="" onclick="toggleview('show_event_col')" />News</label>
    			                      <label><input type="radio" name="type" value="N" onclick="toggleview('show_event_col')" />Event</label><br /><br clear="all" />
        </div>

       
        <div id='show_event_col' style='display:none; float: right;'> 
            <font style="font-size: 12pt; width:400px; display:block; border-bottom:1px solid #000000;">Event Details</font><br /><br />
            <label class="labeling">Venue :</label> <input type="text" name="venue" style="width:200px;" value="" /><br />
            <label class="labeling">Start Date :</label> <input type="text" id="datepicker1" name="event_start_date" style="width:100px;" readonly="" /><br />
            <label class="labeling">End Date :</label> <input type="text" id="datepicker2" name="event_end_date" style="width:100px;" readonly="" /><br />
            <label class="labeling">Time :</label> <input type="text" name="event_time" style="width:150px;" value="" />
        </div><br clear="all" />
        
        <input type="submit" name="sub" value="Submit" class="fancy-button-green" />
        <a href="#" onclick="parent.$.fn.colorbox.close();">[ Cancel ]</a>
    </form>
</div>
</body>
</html>