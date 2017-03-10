<?php 
    include("../../../lib/common.php"); 
    checkLogin();

    // used for pagination
    $page = ($page == 0 ? 1 : $page);
    $perpage = 20;
    $startpoint = ($page * $perpage) - $perpage;

if(isset($_POST['btnSearch']) && ($_POST['btnSearch'] <> "")) {
	$searchStaff = $_POST['searchStaff'];

	if ($searchStaff =="") {
		$tmpSearchStaff = "";
	}
	else {
		$tmpSearchStaff = "AND a.empid like '%$searchStaff%' OR a.name like '%$searchStaff%'";			
	}
	
	$sql1 = "SELECT staff_id from user_acc 
	WHERE user_status = 'ACTIVE' "
	.$tmpSearchStaff."
	ORDER BY staff_id";		


	$sql1 = "SELECT DISTINCT a.empid, a.name, b.description, a.skype_id, a.unit_id, b.description AS department  
	FROM new_employee a 
	LEFT JOIN dept_unit b ON (b.id = a.unit_id) 
	LEFT JOIN education c ON (c.empid = a.empid)
	LEFT JOIN lookup_level_qualification d ON (d.id = c.level)
	LEFT JOIN lookup_teaching e ON (e.id = a.teachingcat)
	LEFT JOIN employee_expertise f ON (f.empid = a.empid)
	LEFT JOIN job_list_category g ON (g.jobarea = f.expertise)
	WHERE a.teachingcat IN ('1','3') 
	AND a.dept_id='ACAD' "		
	.$tmpSearchStaff." "."
	AND c.level IN ('4','5')
	ORDER BY a.name, a.empid, a.unit_id ";	
	
	$result_sql1 = $dbc->query($sql1);
	$dbc->next_record(); 
	$row_cnt1 = mysql_num_rows($result_sql1);
	
	$staffIdArray = Array();
	$staffName1Array = Array();
	$staffName2Array = Array();
	$i=0;
	do {
		$staffIdArray[$i] = $dbc->f('empid');
		/*$staffName1Array[$i] = $dbc->f('name');
		$staffName2Array[$i] = mysql_real_escape_string($staffName1Array[$i]);*/
		$staffNameArray[$i] = $dbc->f('name');
		$staffNameArray[$i] = mysql_real_escape_string($staffNameArray[$i]);
		$unitIdArray[$i] = $dbc->f('department');
		$i++;
	} while ($dbc->next_record());	
	$row_cnt = $i;	
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
	<? if (!isset($_GET['field'])) { ?>
        function closeThePop(data, data2) 
		{
            parent.$.fn.colorbox.close(); //call the colorbox's close function
            parent.$.fn.getParameterValue(data, data2); // pass the parameter to parent
        }
	<? } else { ?>
		function closeThePop(data, data2, data3, data4) 
		{
            parent.$.fn.colorbox.close(); //call the colorbox's close function
            parent.$.fn.getParameterValue3(data, data2, data3, data4); // pass the parameter to parent
        }
	<? } ?>
    </script>
</head>
<body>
<?php
    if(!empty($msg)) 
	{
        foreach($msg as $err) 
		{
            echo $err;
        }
    }
	?>

<style>
.close-btn { 
    border: 2px solid #c2c2c2;
    position: relative;
    padding: 1px 5px;
    bottom: 43px;
    background-color: #605F61;
    left: 873px; 
    border-radius: 20px;
}

.close-btn a {
    font-size: 15px;
    font-weight: bold;
    color: white;
    text-decoration: none;
}

</style>

<h3>Search Staff</h3>


<form id="frmPopup" action=""  method="post" name="frmPopup">
<!--<span class="close-btn"><a href="#" onclick="javascript: parent.$.fn.colorbox.close();">X</a></span>-->
<table>
	<tr>
		<td><label>Staff ID/ Name</label></input>
		<td><input type="text" size="50" name="searchStaff" /></input>
		<input type="submit" name="btnSearch" value="Search" class="fancy-button-blue" /> <?=$row_cnt?> result(s) found.
		</td>
	</tr>
</table>
<table>
	<tr>
		<td><label><span style="color:#FF0000"> Note:</span> If no search value is provided, it will search all.</label></td>
	</tr>
</table>
<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="95%" class="thetable">		
<tr>
	<th width="5%"><label>No</label></th>
	<th width="15%"><div align="left">Staff ID</div></th>
	<th width="40%">Name</th>
	<th width="30%">Department</th>

</tr>
<?php
	if ($row_cnt > 0) {
		$inc = 1;
		for ($no = 0; $no < $row_cnt; $no++) {
			if($inc % 2) $color="first-row"; else $color="second-row";
			?>
			<tr class="<?php echo $color; ?>">
				<td align="center"><?=$no+1?></td>
				<td style="display:none;" align="center"><?php echo $staffIdArray[$no]; ?></td>
				<input type="hidden" name="staffName<?php echo $no;?>" id="staffName<?php echo $no;?>" value="<?=$staffNameArray[$no]?>"></input>
				<td align="left"><label><?php echo $staffIdArray[$no]; ?></label></td>
					<? if (isset($_GET['field'])) { ?>
				<td><a href="#" onclick="closeThePop('<?php echo $staffIdArray[$no]; ?>','<?=$_GET['field2'];?>','<?=$staffNameArray[$no]; ?>','<?=$_GET['field'];?>');"><?=stripslashes($staffNameArray[$no]); ?></a></td>
					<? } else { ?>
					<td><a href="#" onclick="closeThePop('<?php echo $staffIdArray[$no]; ?>','<?=$staffNameArray[$no]; ?>');"><?=stripslashes($staffNameArray[$no]); ?></a></td>
					<? }?>
				<td align="left"><?php echo $unitIdArray[$no]; ?></td>
			</tr>    
			<?php				
			$inc++;			
		};
	?>
		<table>
			<tr>
				<input name="row_cnt" type="hidden" value="<?php echo $row_cnt?>">
			</tr>
		</table>
	


</table>
<?}
	else {
		?>
		<table>
			<tr>
				<td><label>No record(s) found.</label></td>
			</tr>
		</table>
		<?
	}?>
</form>
</body>
</html>