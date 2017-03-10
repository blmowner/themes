<?php
    include("../../../lib/common.php");
    checkLogin(); 
    
    if(isset($delete)) {
        $sql_delete = "DELETE FROM base_user_sys_menu WHERE menu_id='".$_GET['delete']."'";
        $process = $db->query($sql_delete);
        if($process) {
            tracking($_SESSION['user_id'], load_constant('DELETE'), 'DELETE MENU');
            header("refresh:1; url=base_manager.php");
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
		      //$(".select_user").colorbox({width:"60%", height:"40%", iframe:true});
              
              //$.fn.getParameterValue = function(data) {
                  //alert(data);
                //  document.addStaff.staff_id.value = data;
                //};
              
               $(".ADD_MENU").colorbox({width:"70%", height:"60%", iframe:true,          
               onClosed:function(){ 
                //window.location.reload(true); //uncomment this line if you want to refresh the page when child close
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
<div class="padding-5 margin-5 outer">
    <h3><?php echo load_lang('base_manager'); ?></h3>
    <span class="float-right"><?php echo load_hyperlink('5','ADD_MENU',load_lang('add_base'), 'add_base.php',''); ?></span><br /><br class="clear" />
<?php
    // used for pagination
    $page = ($_GET['page'] == 0 ? 1 : $_GET['page']);
    $perpage = 25;
    $startpoint = ($page * $perpage) - $perpage;
    
    // count total number of result - without LIMIT
    $count_total_result = "SELECT * FROM base_language_text";
    $db->query($count_total_result);
    $a = $db->num_rows($count_total_result);
    $db->free();
    
    //  sql for total number with LIMIT
    $sql = "SELECT * FROM base_language_text ORDER BY variable LIMIT $startpoint, $perpage";
    $var = $db;
    $var->query($sql);
	$var->next_record();
	
    
    
?>
    <table cellpadding="3" cellspacing="3" width="100%" class="thetable">
        <tr>
            <th width="20%">Language Code </th>
            <th width="24%">Variable</th>
            <th width="17%">Term</th>
            <th width="25%">Text</th>
            <th width="14%">Action</th>
        </tr>
<?php
    
        $inc = 1;
        do {
			$term = $var->f('term');
			$lang = $var->f('language_code');
			$text = $var->f('text');
			$variable = $var->f('variable');

            if($inc % 2) $color ="first-row"; else $color = "second-row";
?>
        <tr class="<?php echo $color; ?>">
            <td align="center"><?php echo $var->f('language_code'); ?></td>
            <td align="center"><?php echo $var->f('variable'); ?></td>
            <td><?php echo $var->f('term'); ?></td>
            <td><?php echo $var->f('text'); ?></td>
            <td align="center"><?php 
            $link = "javascript:DoConfirm('Are you sure you want to delete?','base_manager.php?delete=".$var->f('menu_id')."')";
            
            if($var->f('text')=='' || $var->f('term')=='' || $var->f('language_code')=='') 
            {                
                echo load_hyperlink('5','DELETE_MENU', load_lang('delete'), $link,'');
            } else {
                echo "<a class='EDIT_MENU' href= edit_base.php?lang=".$lang."&term=".$term."&text=".$text."> Edit Base Language</a>"; 
            }   
            
              ?></td>
        </tr>
<?php
        $inc++;      
        } while($var->next_record());
    
?>
    </table>
    <?php
        //This is the actual usage of function, It prints the paging links
        doPages($perpage, 'base_manager.php', '', $a); 
    ?>
</div>
</body>
</html>