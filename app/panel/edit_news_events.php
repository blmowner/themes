<?php
    include("../../lib/common.php");
    checkLogin();

?>
<?php
	
	if($_POST['sub'] <> "") // if the button submit is press
	{	
        $msg = array();
        if(empty($_POST['title'])) $msg[] = "<div class=\"error\"><span>Please insert the title!</span></div>";
        if(empty($_POST['content'])) $msg[] = "<div class=\"error\"><span>Please insert the description!</span></div>";
        
        if(empty($msg)) {
	   
            $thefileName = $_FILES['attachment']['name'];
            $tempName = $_FILES['attachment']['tmp_name'];
            $fileType = $_FILES['attachment']['type'];
            
            if(is_uploaded_file($tempName)) { // if the attachment is not empty
            
                $file = addslashes(file_get_contents($tempName));
                $sql_upd="UPDATE colombo_news_event SET 
        					news_event_title ='".addslashes($_POST['title'])."',
        					news_event_desc ='".addslashes($_POST['content'])."',
                            news_event_news = '".$_POST['type']."',
                            news_event_venue = '".$_POST['venue']."',
        					news_event_start_dt ='".$_POST['event_start_date']."',
        					news_event_end_dt ='".$_POST['event_end_date']."',
        					news_event_time ='".$_POST['event_time']."',
        					news_event_file_name ='$thefileName',
        					news_event_file_type ='$fileType',
        					news_event_file ='$file',
        					modify_by ='".$_POST['status']."',
        					modify_dt = now() WHERE news_event_id = '".$_GET['id']."'";	   		
        			
   		            $db->query($sql_upd);
    			
        			/* ADDED BY MJMZ - for tracking purpose 22/04/11 */
        			tracking($_SESSION['user_id'], load_constant("UPDATE"), "UPDATE NEWS/EVENT"); //($uid, $activity, $module)
        		
        			echo "<div class=\"success\"><span>Successfully saved!</span></div>";
        			echo "<script>parent.$.fn.colorbox.close();</script>";	
                    
             //end of file upload
             } else {
                
                $sql_upd="UPDATE colombo_news_event SET 
        					news_event_title ='".addslashes($_POST['title'])."',
        					news_event_desc ='".addslashes($_POST['content'])."',
                            news_event_news = '".$_POST['type']."',
                            news_event_venue = '".$_POST['venue']."',
        					news_event_start_dt ='".$_POST['event_start_date']."',
        					news_event_end_dt ='".$_POST['event_end_date']."',
        					news_event_time ='".$_POST['event_time']."',
        					modify_by ='".$_POST['status']."',
        					modify_dt = now() WHERE news_event_id = '".$_GET['id']."'";	   		
        			
   		        $db->query($sql_upd);
			
    			/* ADDED BY MJMZ - for tracking purpose 22/04/11 */
    			tracking($_SESSION['user_id'], load_constant("UPDATE"), "UPDATE NEWS/EVENT"); //($uid, $activity, $module)
    		
    			echo "<div class=\"success\"><span>Successfully saved!</span></div>";
    			echo "<script>parent.$.fn.colorbox.close();</script>";
                
                
             }
        }			
	} // end of button submit is press
	
    
	// retrieve data from DB
	$id = $_GET['id'];
	
	$sql = "SELECT * FROM colombo_news_event WHERE news_event_id = '$id'";
	$sql_slct = $db;
	$sql_slct->query($sql);	
	$result = $sql_slct->next_record();
	$row = $sql_slct->rowdata($result);
	
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
    <h3 style="font-size: 12pt;">Edit News or Event</h3>
    <div class="info"><span><?php echo load_lang('compulsary'); ?></span></div><br />
    <?php
        if(!empty($msg)) {
            foreach($msg as $err) {
                echo $err;
            }
        }
    ?>
    <form method="post" id="form-set" enctype="multipart/form-data">
    <div class="float-left">
        <label class="labeling">* Title :</label><input type="text" name="title" style="width:300px;" value="<?php echo stripslashes($row['news_event_title']); ?>" /><br clear="all" />
        <label class="labeling">* Description :</label><textarea name="content" rows="5" cols="40"><?php echo stripslashes($row['news_event_desc']); ?></textarea><br clear="all" />
        <label class="labeling">Attachment (optional) :</label><input type="file" name="attachment" /><br /><br clear="all" />  
        <label class="labeling">Type :</label>
        <?php if($row['news_event_news'] == "Y") { ?>
        <label><input type="radio" name="type" value="Y" checked="" onclick="toggleview('show_event_col')" />News</label>
        <label><input type="radio" name="type" value="N"  onclick="toggleview('show_event_col')" />Event</label>
        <?php } else { ?>
        <label><input type="radio" name="type" value="Y" onclick="toggleview('show_event_col')" />News</label>
    	<label><input type="radio" name="type" value="N" checked="" onclick="toggleview('show_event_col')" />Event</label><br /><br clear="all" />
        <?php } ?>
        </div>

       
        <div id='show_event_col' style='display:none; float: right;'> 
            <font style="font-size: 12pt; width:400px; display:block; border-bottom:1px solid #000000;">Event Details</font><br /><br />
            <label class="labeling">Venue :</label> <input type="text" name="venue" style="width:200px;"  value="<?php echo $row['news_event_venue']; ?>" /><br />
            <label class="labeling">Start Date :</label> <input type="text" id="datepicker1" value="<?php echo $row['news_event_start_dt']; ?>" name="event_start_date" style="width:100px;" readonly="" /><br />
            <label class="labeling">End Date :</label> <input type="text" id="datepicker2" value="<?php echo $row['news_event_end_dt']; ?>" name="event_end_date" style="width:100px;" readonly="" /><br />
            <label class="labeling">Time :</label> <input type="text" name="event_time" style="width:150px;" value="<?php echo $row['news_event_time']; ?>" />
        </div><br clear="all" />
        
        <input type="submit" name="sub" value="Submit" class="fancy-button-green" />
        <a href="#" onclick="parent.$.fn.colorbox.close();">[ Cancel ]</a>
    </form>
</div>
</body>
</html>