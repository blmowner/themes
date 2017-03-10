<?php 
    include("../../../lib/common.php"); 
    checkLogin();

///////////////////////////////////////////////////////////////
// used for pagination
	$page = ($_GET['page'] == 0 ? 1 : $_GET['page']);
	$perpage = 10;
	$startpoint = ($page * $perpage) - $perpage;

	$varParamSend="";
	
	foreach($_REQUEST as $key => $value)
	{
		if($key!="page")
			$varParamSend.="&$key=$value";
	}

///////////////////////////////////////////////////////////////

if(isset($_POST['search']) || isset($_POST['search'])) 
{
      	$sql = "SELECT staff_id from user_acc 
				WHERE `user_status` = 'ACTIVE'
				ORDER BY staff_id
				LIMIT  $startpoint,$perpage";
		$dba->query($sql);
		$result = $dba->next_record();     


}
else 
{
   $sql = "SELECT staff_id from user_acc 
			WHERE `user_status` = 'ACTIVE' 
			ORDER BY staff_id 
			LIMIT  $startpoint,$perpage";

	$dba->query($sql);
	$result = $dba->next_record();     
}

 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang; ?>" lang="<?php echo $lang; ?>">

<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
	<meta name="author" content="MJMZ" />

	<title>Select User</title>
    <link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
    <script>
        function closeThePop(data) 
		{
            parent.$.fn.colorbox.close(); //call the colorbox's close function
            parent.$.fn.getParameterValue(data); // pass the parameter to parent
        }
    </script>
	
	<script language="javascript">
	function selValue()
	{
		var val = '';
		for(i=0;i<frmPopup.hdnLine.value;i++)
		{
			if(eval("frmPopup.Chk"+i+".checked")==true)
			{
				val = val + eval("frmPopup.Chk"+i+".value") + ' , ';
			}
		}
		
		//window.opener.document.getElementById("txtSel").value = val;
		//window.close();
		
		parent.$.fn.getParameterValue(val); // pass the parameter to parent
		parent.$.fn.colorbox.close(); //call the colorbox's close function
		
		return false;
	}
</script>
</head>

<body>
<h3>Search User </h3>
<form id="frmPopup" action="" onsubmit="return false;" method="post" name="frmPopup">
 <input type="text" size="50" name="search_box" />
 <input type="submit" name="search" value="Search" class="" />
<input type="submit" name="select" value="Select" class="" onclick="return selValue();" />

 

 <table cellpadding="3" cellspacing="3" width="90%" class="thetable">
    <tr>
      <th width="4%" style="display:none;" >&nbsp;</th>
      <th width="4%">&nbsp;</th>
      <th width="6%"><div align="left"><?php echo load_lang('staff_id'); ?></div></th>
        <th width="50%"><?php echo load_lang('name'); ?></th>
    </tr>
    <?php
	
        if($result) { //continue from the if isset($_POST['search']
        $inc = 1;
		$i = 0;
            do{
                if($inc % 2) $color="first-row"; else $color="second-row";
				
    ?>
    <tr class="<?php echo $color; ?>">
	<?php $id = $dba->f('staff_id'); ?>
      <td style="display:none;" align="center"><?php echo $a = $dba->f('staff_id'); ?></td>

	  <?php
					//$a = $dba->f('staff_id');
			
					$sql1 = "SELECT name from new_employee 
					WHERE `empid` = '$id'
					UNION 
					SELECT name FROM student
					WHERE `matrix_no` = '$id'";
	
					$obj = $dbc->query($sql1);
           			$result1 = $dbc->next_record(); 
					//$objQuery = $db->query($strSQL); $db->next_record();
    				$b = $dbc->f('name');
					//$c = $obj["name"];
	 	?>
      <td align="center">
	  <input name="Chk<?php echo $i;?>" id="Chk<?php echo $i;?>" type="checkbox" value="<?php echo $b?>" /></td>
      <?php $id = $dba->f('staff_id'); ?>
      <td align="center"><div align="left"><?php echo $dba->f('staff_id'); ?></div></td>
	  <td><a href="#" onclick="closeThePop('<?php echo $b; ?>');"><?php echo $dbc->f('name'); ?></a></td>
    </tr>    
    <?php			
            $inc++;
			$dbc->query($sql1);
			$result1 = $dbc->next_record();
			$result = $dba->next_record();
			$i++;
            } while($result);
            
            echo $inc-1 ." result(s) found."; // show number of result found using search func
        }
    ?>
 </table>
  <input name="hdnLine" type="hidden" value="<?php echo $i;?>">
  <input type="submit" name="select" value="Select" class="" onclick="return selValue();" />
 <p>
   <?php
    // count total number of result - without LIMIT
        $count_total_result = "SELECT staff_id from user_acc WHERE staff_id LIKE '%".$_POST['search_box']. "' AND `user_status` = 'ACTIVE'";
        $dba->query($count_total_result);
        $a = $dba->num_rows($count_total_result);
        $dba->free();

        //This is the actual usage of function, It prints the paging links
        doPages($perpage, 'select_receiver.php', $varParamSend, $a); 
 ?>
</form></p>
 <p>&nbsp; </p>
</body>
</html>