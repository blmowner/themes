<?php

    include("../../lib/common.php");
    checkLogin();
    
	if(isset($_POST['sub']))
	{		
	   $msg = array();
       if(empty($_POST['title'])) $msg[] = "<div class=\"error\"><span>Please insert the title!</span></div>";
       if(empty($_POST['date_publish'])) $msg[] = "<div class=\"error\"><span>Please insert the date published!</span></div>";
       if($_POST['source'] == "") $msg[] = "<div class=\"error\"><span>Please select source!</span></div>";

       
       if(empty($msg)) {
        
            $fileName = $_FILES['attachment']['name'];
            $tmpName = $_FILES['attachment']['tmp_name'];
            $fileType = $_FILES['attachment']['type'];
            
            if(!is_uploaded_file($tmpName)) { // if the attachment is empty
                
                $msg[] = "<div class=\"error\"><span>Please select file to upload!</span></div>";
            
            } else { // if the attachment is not empty
            
                switch ($fileType)
                {
                  case "image/gif";       
                     $mime = TRUE;
                     break;
                  case "image/jpg";          
                     $mime = TRUE;
                     break;
                case "image/jpeg";          
                     $mime = TRUE;
                     break;
                  case "image/pjpeg";          
                     $mime = TRUE;
                     break; 
                  case "image/png";       
                     $mime = TRUE;
                     break;
                  case "image/x-MS-bmp";       
                     $mime = TRUE;
                     break;
                  default: 
                     $mime = FALSE;
                     break;
                }
                
                if($mime) {
                    include('../../lib/SimpleImage_class.php');
                
                    //resize thumbnail image
                    $image = new SimpleImage();
                    $image->load($tmpName);
                    $image->resize(70,70);
                     
                    //save thumbnail image
                    $thumb_dest = $fileName."thumb.".substr($fileName,-3,7);
                    $image->save($thumb_dest);
                    $file = fopen($thumb_dest, "r");
                    $thumb_contents = fread($file, filesize($thumb_dest));
                    $thumbfileContents = addslashes($thumb_contents);
                    
                    //resize large image
                    $image_large = new SimpleImage();
                    $image_large->load($tmpName);
                    
                    //save large image
                    $large_dest = $fileName."large.".substr($fileName,-3,7);
                    $image_large->save($large_dest);
                    $file = fopen($large_dest, "r");
                    $large_contents = fread($file, filesize($large_dest));
                    $largefileContents = addslashes($large_contents);
                    
                    
                    
                    $sql_add_headlines= "INSERT INTO eadvertorial_upload (imgType, imgData, source, title, description, advert_date, typeInst, createdBy, creation_date, imgdataT)
                                         VALUES('$fileType','$largefileContents','".$_POST['source']."','".addslashes($_POST['title'])."','".trim(addslashes($_POST['description']))."','".$_POST['date_publish']."','COLOMBO','".$_SESSION['user_id']."',now(),'$thumbfileContents')";	
                    
                    $process_headlines = $dbklas->query($sql_add_headlines);
                    
                    if($process_headlines) {
                        
                        fclose($file); //close the read file before delete
                        unlink($thumb_dest); //delete the thumbnail image
                        unlink($large_dest); //delete the large image
                        
                        tracking($_SESSION['user_id'], load_constant("INSERT"), "INSERT HEADLINES"); //($uid, $activity, $module)
                        echo "<script>parent.$.fn.colorbox.close();</script>";
                    }
                    
                // end of mime = TRUE 
                } else {
                    $msg[] = "<div class=\"error\"><span>Invalid file format!</span></div>";
                }
                
            } //end of the attachment
        
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
<script type="text/javascript" src="../../lib/js/ckeditor/ckeditor.js"></script>
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
    <h3 style="font-size: 12pt;">Add Headlines</h3>
    <div class="info"><span><?php echo load_lang('compulsary'); ?></span></div><br />
    <?php
        if(!empty($msg)) {
            foreach($msg as $err) {
                echo $err;
            }
        }
    ?>
    <form method="post" id="form-set" action="add_headlines.php" enctype="multipart/form-data">
    <div class="float-left">
        <label class="labeling">* File to upload :</label><input type="file" name="attachment" /><br clear="all" />
        <label class="labeling">* Title :</label><input type="text" name="title" style="width:350px;" value="<?php echo isset($_POST['title']) ? $_POST['title'] : ""; ?>" /><br clear="all" />
        <label class="labeling">* Date Published :</label> <input type="text" id="datepicker1" value="<?php echo isset($_POST['date_publish']) ? $_POST['date_publish'] : ""; ?>" name="date_publish" style="width:100px;" readonly="" /><br />
        <label class="labeling">* Source :</label>
        <select name="source">
            <?php 
                $source = isset($_POST['source']) ? $_POST['source'] : "";
                dd_menu(array('source_desc AS source_cd','source_desc'), 'ref_headlines_source', $source,''); 
            ?>
        </select><br clear="all" />
        <label class="labeling">Description :</label><textarea name="description" rows="3" cols="40" class="ckeditor"><?php echo isset($_POST['description']) ? $_POST['description'] : ""; ?></textarea><br clear="all" />
    </div>

        
        <input type="submit" name="sub" value="Submit" class="fancy-button-green" />
        <a href="#" onclick="parent.$.fn.colorbox.close();">[ Cancel ]</a>
    </form>
</div>
</body>
</html>