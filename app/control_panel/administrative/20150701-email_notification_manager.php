<?php
    include("../../../lib/common.php");
    checkLogin(); 
    $delete = $_GET['delete'];
    if(isset($delete)) {
        $sql_delete = "DELETE FROM base_constant WHERE const_id='".$_GET['delete']."'";
        $process = $db->query($sql_delete);
        if($process) {
            tracking($_SESSION['user_id'], load_constant('DELETE'), 'DELETE MENU');
            header("refresh:1; url=email_notification_manager.php");
        }
        
    }   
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang; ?>" lang="<?php echo $lang; ?>">

<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
	<meta name="author" content="MJMZ" />

	<title>Base Manager</title>
    <link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
    <link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
    <script language="Javascript">
        function DoConfirm(message, url)
        {
        	if(confirm(message)) location.href = url;
        }
    </script>    
    <script src="../../../lib/js/jquery.min2.js"></script>
	<script src="../../../lib/js/jquery.colorbox.js"></script>
    <script>
  
		$(document).ready(function(){
              
               $(".add_email").colorbox({width:"70%", height:"60%", iframe:true,          
               onClosed:function(){ 
                window.location.reload(true); //uncomment this line if you want to refresh the page when child close
                window.location = window.location;   //reload the page   
                                
                } }); 
                
               $(".EDIT_MENU").colorbox({width:"70%", height:"50%", iframe:true,          
               onClosed:function(){ 
                //window.location.reload(true); //uncomment this line if you want to refresh the page when child close
                  window.location = window.location;   //reload the page                 
                } }); 
          });
	</script>
</head>

<body>
<?php
    
    //select all
    $sql = "SELECT * FROM base_constant WHERE const_category = 'EMAIL'";
    $var = $db;
    $count_total_result=$var->query($sql);
	$var->next_record();
	$a = $var->num_rows($count_total_result);
	    
?>

<div class="padding-5 margin-5 outer">
    <h3><?php echo load_lang('email_notification_manager'); ?></h3>
    Total Result :<?=$a?><span class="float-right">
	<a class = "add_email" href= "add_email_notification.php">Add Email Notification Status</a></span><br /><br class="clear" />
    <table cellpadding="3" cellspacing="3" width="100%" class="thetable">
        <tr>
            <th width="20%">Constant Id</th>
            <th width="24%">Constant Category</th>
            <th width="17%">Constant Term</th>
            <th width="25%">Constant Value</th>
            <th width="14%">Action</th>
        </tr>
<?php
    
        $inc = 1;
        do {
			$id = $var->f('const_id');
			$category = $var->f('const_category');
			$term = $var->f('const_term');
			$value = $var->f('const_value');
?>
        <tr>
            <td align="center"><?=$id ?></td>
            <td align="center"><?=$category ?></td>
            <td><?=$term ?></td>
            <td><?=$value ?></td>
            <td align="center"><?php 
            $link = "javascript:DoConfirm('Are you sure you want to delete?','email_notification_manager.php?delete=".$id."')";
            
            if($var->f('text')=='' || $var->f('term')=='' || $var->f('language_code')=='') 
            {                
                echo load_hyperlink('5','DELETE_MENU', load_lang('delete'), $link,'');
            } 
			else 
			{
                echo "<a class='EDIT_MENU' href= edit_base.php?lang=".$lang."&term=".$term."&text=".$text."> Edit Base Language</a>"; 
            }   
            
              ?></td>
        </tr>
<?php
        $inc++;      
        } while($var->next_record());
    
?>
    </table>
</div>
</body>
</html>