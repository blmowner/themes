<?php
    include("../../../lib/common.php");
    checkLogin();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang; ?>" lang="<?php echo $lang; ?>">

<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
	<meta name="author" content="MJMZ" />

	<title>Manage User</title>
    <link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
    <script src="../../../lib/js/jquery-1.4.2.min.js" type="text/javascript"></script>
    <script type="text/javascript">
    
    $(document).ready(function(){
    
    
        $(".slidingDiv").hide();
    	$(".show_hide").show();
    	
    	$('.show_hide').click(function(){
    	$(".slidingDiv").slideToggle();
    	});
    
    });
    
    </script>
</head>

<body>

<div class="padding-5 margin-5 outer">
    <form method="post" id="form-set">
        <label class="labeling"><?php echo load_lang('keyword'); ?></label><input type="text" name="find" size="35" />
        <input type="submit" name="submit" value="<?php echo load_lang('find_btn'); ?>" class="fancy-button-blue" />
        <?php echo load_hyperlink('3', 'ADD_USER',load_lang('add_new_user'),"add_user.php",''); ?>
    </form><br class="clear" />
    
    <a href="#" class="show_hide">Info [Show/hide]</a>
    <div class="slidingDiv inline-ul">
        <ul>
            <li><font color="green"><b>Online : <?php echo get_count_data('staff_id','user_acc', 'user_online_stat', '1'); ?></b></font> | </li>
            <li><font color="grey"><b>Offline : <?php echo get_count_data('staff_id','user_acc', 'user_online_stat', '0'); ?></b></font> | </li>
            <li><font><b>Active : <?php echo get_count_data('staff_id','user_acc', 'user_status', 'ACTIVE'); ?></b></font> | </li>
            <li><font><b>Inactive : <?php echo get_count_data('staff_id','user_acc', 'user_status', 'INACTIVE'); ?></b></font></li>
        </ul>
    </div>
    
    <table cellpadding="3" cellspacing="0" width="100%" class="thetable">
        <tr>
            <th><?php echo load_lang('number#'); ?></th>
            <th><?php echo load_lang('staff_id'); ?></th>
            <th><?php echo load_lang('role_type'); ?></th>
            <th><?php echo load_lang('name'); ?></th>
            <th><?php echo load_lang('department'); ?></th>
            <th><?php echo load_lang('online_status'); ?></th>
            <th><?php echo load_lang('status'); ?></th>
            <th><?php echo load_lang('online_time'); ?></th>
            <th><?php echo load_lang('ip_address'); ?></th>
            <th><?php echo load_lang('action'); ?></th>
        </tr>
    <?php
	
	
	        // used for pagination
        $page = ($_GET['page'] == 0 ? 1 : $_GET['page']);
        $perpage = 25;
        $startpoint = ($page * $perpage) - $perpage;
        
        $sql = "SELECT a.staff_id, c.role_type, a.user_online_stat, a.user_status, a.user_last_login, a.user_ip
                FROM user_acc a 
                /*LEFT JOIN employee b ON (b.empid = a.staff_id)*/
                LEFT JOIN base_user_role c ON (a.role_id = c.role_id) 
                WHERE a.staff_id LIKE '%".$_POST['find']."%' /*OR
                b.name LIKE '%".$_POST['find']."%'*/ LIMIT $startpoint, $perpage";
        $db->query($sql);
        $rec_next = $db->next_record();
        if($rec_next) {
            $inc = 1;
            do {
                if($inc % 2) $color = "first-row"; else $color = "second-row";
    ?>
        <tr class="<?php echo $color; ?>">
            <td align="center"><?php echo $inc; ?></td>
            <td align="center"><?php echo $db->f('staff_id'); ?></td>
            <td align="center"><?php echo $db->f('role_type'); ?></td>
			<? 
			$user_idx=$db->f('staff_id');
			$sqlstd="SELECT CHAR_LENGTH(matrix_no) as total FROM student WHERE matrix_no='$user_idx'";
			$dbc->query($sqlstd);
			$dbc->next_record();
			$total=$dbc->f('total');
			if ($total==12)
			{
			$name = getValue('name','student','matrix_no',$db->f('staff_id')); 
			$dept='';
			}else
			{
			$name = getValue('name','new_employee','empid',$db->f('staff_id'));
			$dept = getValue('unit_id','new_employee','empid',$db->f('staff_id'));	
			}
			?>
            <td><?php echo $name; ?></td>
            <td align="center"><? echo $dept; ?></td>
            <td align="center">
            <?php 
            if($db->f('user_online_stat')== load_constant('ZERO'))  $online_stat = "<font color=\"grey\"><b>Offline</b></font>"; else $online_stat = "<font color=\"green\"><b>Online</b></font>";
            echo $online_stat;
            ?>
            </td>
            <td align="center"><?php echo $db->f('user_status'); ?></td>
            <td align="center"><?php echo $db->f('user_last_login'); ?></td>
            <td align="center"><?php echo $db->f('user_ip'); ?></td>
            <td align="center"><?php echo load_hyperlink('3','EDIT_USER',load_lang('edit'),'edit_user.php?uid='.$db->f('staff_id'),''); ?></td>
        </tr>
    <?php
            $inc++;
            $rec_next = $db->next_record();
                }
            while($rec_next);
        } 
    ?>
    </table>
    <?php
        
        // count total number of result - without LIMIT
        $count_total_result = "SELECT a.staff_id, c.role_type, a.user_online_stat, a.user_status, a.user_last_login, a.user_ip
                FROM user_acc a 
                /*LEFT JOIN employee b ON (b.empid = a.staff_id)*/
                LEFT JOIN base_user_role c ON (a.role_id = c.role_id) 
                WHERE a.staff_id LIKE '%".$_POST['find']."%' /*OR
                b.name LIKE '%".$_POST['find']."%'*/";
        $db->query($count_total_result);
        $a = $db->num_rows($count_total_result);
        $db->free();

        //This is the actual usage of function, It prints the paging links
        doPages($perpage, 'manage_user.php', '', $a); 
    
    ?>
</div>


</body>
</html>