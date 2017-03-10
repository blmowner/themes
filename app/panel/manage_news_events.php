<?php
    include("../../lib/common.php");
    checkLogin();

// DELETE HUMAN RESOURCE DOC FROM LIST //
	if(isset($_GET['mode']) && $_GET['mode'] == "del" && !empty($_GET['id'])) {
		$sql_del = "DELETE FROM colombo_news_event WHERE news_event_id='".$_GET['id']."'";
		$result = $db->query($sql_del);
		if($result) {
			/* ADDED BY MJMZ - for tracking purpose 22/04/11 */
			tracking($usrid, load_constant("DELETE"), "DELETE LIBRARY ANNOUNCE"); //($uid, $activity, $module)
		}
	}
?>
<?php
    // used for pagination
    $page = ($_GET['page'] == 0 ? 1 : $_GET['page']);
    $perpage = 30;
    $startpoint = ($page * $perpage) - $perpage;
    
    // SEARCH //
	if(isset($_POST['find'])) {
		$sql = "SELECT *
				FROM colombo_news_event
				WHERE (news_event_title LIKE '%".stripslashes($_POST['search'])."%' OR news_event_venue LIKE '%".$_POST['search']."') 
				ORDER BY insert_dt DESC LIMIT $startpoint, $perpage";
	} else {
	
		$sql = "SELECT * FROM colombo_news_event
				ORDER BY insert_dt DESC LIMIT $startpoint, $perpage";
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
              
               $(".add_news_events").colorbox({width:"90%", height:"70%", iframe:true,          
               onClosed:function(){ 
                window.location = window.location;   //reload the page 
                                
                } }); 
                
                $(".edit_news_event").colorbox({width:"90%", height:"70%", iframe:true,          
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
		<a href="add_news_events.php" class="float-right add_news_events">Add News or Event</a>
	</form><br class="clear" />

	<table cellpadding="3" cellspacing="0" width="100%" class="thetable">
		<tr> 
			<th width="70%">Title</th>
			<th width="10%">Type</th>
			<th width="20%">Modify</th>
		</tr>
		<?php
					$sql_slct = $db;
					$sql_slct->query($sql);
					$nx_rec = $sql_slct->next_record();
					if($nx_rec) {
						$color=1;
						do {
							if($color % 2) { $col="first-row"; } else { $col ="second-row"; } //switch color
				?>
		<tr class="<?php echo $col; ?>"> 
		  <td width="41%"><a href="view_news_events.php?id=<?php echo $sql_slct->f("news_event_id"); ?>&title=<?php echo $sql_slct->f("news_event_news") == "N" ? "Event" : "News"; ?>" class="view_news_event"><?php echo $sql_slct->f("news_event_title"); ?></a></td>
		  <td width="11%" align="center"><?php echo $sql_slct->f("news_event_news") == "Y" ? "News" : "Event"; ?></td>
		  <td width="20%" align="center"><!--input type="submit" class="edit_news_event fancy-button-blue" value="Edit" onclick="window.open('edit_news_events.php?id=<?php echo $sql_slct->f("news_event_id"); ?>','windowname2','width=600, \height=620, \directories=no, \
   location=no, \
   menubar=no, \
   resizable=no, \
   scrollbars=1, \
   status=no, \
   top=100, \
   left=200, \
   toolbar=no'); 
  return false;" /-->
  
  <a href="edit_news_events.php?id=<?php echo $sql_slct->f("news_event_id"); ?>" class="edit_news_event">Edit</a>
  
  <input type="submit" value="Delete" class="fancy-button-grey" onclick="javascript:if(confirm('Are you sure you want to delete this data?')){location.href='manage_news_events.php?mode=del&id=<?php echo $sql_slct->f("news_event_id"); ?>';}" />
  
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
        $sql = "SELECT *
				FROM colombo_news_event
				WHERE (news_event_title LIKE '%".$_POST['search']."%' OR news_event_venue LIKE '%".$_POST['search']."') 
				ORDER BY insert_dt DESC LIMIT $startpoint, $perpage";
        
        $db->query($count_total_result);
        $a = $db->num_rows($count_total_result);
        $db->free();

        //This is the actual usage of function, It prints the paging links
        doPages($perpage, 'manage_news_events.php', '', $a); 
    ?>
    </div>
</body>
</html>
