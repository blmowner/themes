<?php
    include("../../lib/common.php");
    checkLogin();


	if(isset($_GET['mode']) && $_GET['mode'] == "del" && !empty($_GET['id'])) {
		$sql_del = "DELETE FROM eadvertorial_upload WHERE fid='".$_GET['id']."'";
		$result = $dbklas->query($sql_del);
		if($result) {
			/* ADDED BY MJMZ - for tracking purpose 22/04/11 */
			tracking($_SESSION['user_id'], load_constant("DELETE"), "DELETE COLOMBO ENEWS"); //($uid, $activity, $module)
		}
	} 
?>
<?php
    // used for pagination
    $page = ($_GET['page'] == 0 ? 1 : $_GET['page']);
    $perpage = 15;
    $startpoint = ($page * $perpage) - $perpage;
    
    // SEARCH //
	if(isset($_POST['find'])) {
		$sql = "SELECT fid, title, source
				FROM eadvertorial_upload
				WHERE (title LIKE '%".trim(stripslashes($_POST['search']))."%' OR description LIKE '%".trim($_POST['search'])."%') 
                AND typeInst = 'COLOMBO' 
				ORDER BY advert_date DESC LIMIT $startpoint, $perpage";
	} else { 
	
		$sql = "SELECT fid, title, source FROM eadvertorial_upload
                WHERE typeInst = 'COLOMBO'
				ORDER BY advert_date DESC LIMIT $startpoint, $perpage";
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang; ?>" lang="<?php echo $lang; ?>">

<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
	<meta name="author" content="MJMZ" />

	<title>Manage News &amp; Events</title>
    <link rel="stylesheet" type="text/css" href="../../theme/css/<?php echo $css; ?>" />
    <link rel="stylesheet" type="text/css" href="../../theme/css/colorbox.css" media="screen" />
    
    <script src="../../lib/js/jquery.min2.js"></script>
	<script src="../../lib/js/jquery.colorbox.js"></script>
    <script>
  
		$(document).ready(function(){
		  
               $(".add_headlines").colorbox({width:"87%", height:"95%", iframe:true,          
               onClosed:function(){ 
                window.location = window.location;   //reload the page 
                                
                } }); 
                
                $(".edit_headlines").colorbox({width:"87%", height:"95%", iframe:true,          
               onClosed:function(){ 
                window.location = window.location;   //reload the page 
                                
                } });
                
                $(".view_news_event").colorbox({width:"80%", height:"55%", iframe:true,          
               onClosed:function(){ 
                //window.location = window.location;   //reload the page 
                                
                } });
                
          });
          
	</script>
    
</head>

<body>
	<div class="padding-5 margin-5 outer">
	
    <form method="post" id="form-set">
		<label class="labeling"><?php echo load_lang('keyword'); ?></label>&nbsp;<input type="text" name="search" size="60" />&nbsp;<input type="submit" name="find" value="<?php echo load_lang('find_btn'); ?>" class="fancy-button-grey" />
		<a href="add_headlines.php" class="float-right add_headlines">Add Headlines</a>
	</form><br class="clear" />

	<table cellpadding="3" cellspacing="0" width="100%" class="thetable">
		<tr> 
			<th width="70%">Title</th>
			<th width="10%">Source</th>
			<th width="20%">Action</th>
		</tr>
		<?php
					$sql_slct = $dbklas;
					$sql_slct->query($sql);
					$nx_rec = $sql_slct->next_record();
					if($nx_rec) {
						$color=1;
						do {
							if($color % 2) { $col="first-row"; } else { $col ="second-row"; } //switch color
				?>
		<tr class="<?php echo $col; ?>"> 
		  <td width="41%"><a href="view_headlines.php?id=<?php echo $sql_slct->f("fid"); ?>" class="view_news_event"><?php echo stripslashes($sql_slct->f("title")); ?></a></td>
		  <td width="11%" align="center"><?php echo $sql_slct->f("source"); ?></td>
		  <td width="20%" align="center">
  
  <a href="edit_headlines.php?id=<?php echo $sql_slct->f("fid"); ?>" class="edit_headlines">Edit</a>
  
  <input type="submit" value="Delete" class="fancy-button-red" onclick="javascript:if(confirm('Are you sure you want to delete this data?')){location.href='manage_headlines.php?mode=del&id=<?php echo $sql_slct->f("fid"); ?>';}" />
  
  </td>
		</tr>
		<?
			$nx_rec = $sql_slct->next_record();
			$color++;
			} while($nx_rec);
		}
	  ?>
	  </table>
    <?php
        $count_total_result = "SELECT fid
				FROM eadvertorial_upload
				WHERE (title LIKE '%".trim($_POST['search'])."%' OR description LIKE '%".trim($_POST['search'])."%') 
                AND typeInst = 'COLOMBO'
				ORDER BY advert_date DESC"; 
        
        $thenum = $dbklas;
        $thenum->query($count_total_result);
        $a = $thenum->num_rows($count_total_result);
        $thenum->free();

        //This is the actual usage of function, It prints the paging links
        doPages($perpage, 'manage_headlines.php', '', $a); 
    ?>
    </div>
</body>
</html>
