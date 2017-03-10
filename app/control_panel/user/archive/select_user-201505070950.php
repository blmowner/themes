<?php 
    include("../../../lib/common.php"); 
    checkLogin();

    // used for pagination
    $page = ($page == 0 ? 1 : $page);
    $perpage = 20;
    $startpoint = ($page * $perpage) - $perpage;
    
    if(isset($_POST['search']) || isset($_POST['search_box'])) {
       $sql = "SELECT matrix_no empid, name from student 
	   			WHERE (name LIKE '%".$_POST['search_box']."%' OR matrix_no LIKE '%".$_POST['search_box']."%') 
				AND student_status IN ('ACTIVE', 'LI', 'Project')
				UNION 
				SELECT empid, name FROM new_employee
				WHERE (name LIKE'%".$_POST['search_box']."%' OR empid LIKE '%".$_POST['search_box']."%') 
				AND xemployee_status='AC' ";
				/*LIMIT $startpoint, $perpage*/

    } else {
       $sql = "SELECT matrix_no empid, name from student 
				WHERE student_status IN ('ACTIVE', 'LI', 'Project')
				UNION
				SELECT empid, name FROM new_employee
				WHERE xemployee_status='AC' ";
				/*LIMIT $startpoint, $perpage*/
    }

    //$dbs->query($sql);
	$dbc->query($sql);
    //$result = $dbs->next_record();
	$result = $dbc->next_record();
        
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang; ?>" lang="<?php echo $lang; ?>">

<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
	<meta name="author" content="MJMZ" />

	<title>Select User</title>
    <link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
    <script>
        function closeThePop(data) {
            parent.$.fn.colorbox.close(); //call the colorbox's close function
            parent.$.fn.getParameterValue(data); // pass the parameter to parent
        }
    </script>
</head>

<body>
<h3>Search Staff</h3>
<form method="post" id="form-set">
 <input type="text" size="25" name="search_box" />
 <input type="submit" name="search" value="Search" class="fancy-button-blue" /><br /><br />
</form>
 <table cellpadding="3" cellspacing="3" width="100%" class="thetable">
    <tr>
        <th width="20%"><?php echo load_lang('staff_id'); ?></th>
        <th width="70%"><?php echo load_lang('name'); ?></th>
    </tr>
    <?php
        if($result) { //continue from the if isset($_POST['search']
        $inc = 1;
            do{
                if($inc % 2) $color="first-row"; else $color="second-row";
    ?>
    <tr class="<?php echo $color; ?>">
        <?php /*?><td align="center"><?php echo $dbs->f('empid'); ?></td><?php */?>
		<td align="center"><?php echo $dbc->f('empid'); ?></td>
        <?php
            $a = $dbc->f('empid');
        ?>
        <td><a href="#" onclick="closeThePop('<?php echo $a; ?>');"><?php echo $dbc->f('name'); ?></a></td>
    </tr>    
    <?php
            $inc++;
            $result = $dbc->next_record();
            } while($result);
            
            echo $inc-1 ." result(s) found."; // show number of result found using search func
        }
    ?>
 </table>
 <?php
    // count total number of result - without LIMIT
        $count_total_result = "SELECT empid, name from siso_employee WHERE name LIKE '%".$_POST['search_box']."%' OR empid LIKE '%".$_POST['search_box']."%'";
        $dbc->query($count_total_result);
        $a = $dbc->num_rows($count_total_result);
        //$dbs->free();

        //This is the actual usage of function, It prints the paging links
        doPages($perpage, 'select_user.php', '', $a); 
 ?>
</body>
</html>