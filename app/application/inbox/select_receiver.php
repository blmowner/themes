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
		$tmpSearchStaff = "AND staff_id like '%$searchStaff%'";			
	}
	
	$sql1 = "SELECT staff_id from user_acc 
	WHERE user_status = 'ACTIVE' "
	.$tmpSearchStaff."
	ORDER BY staff_id";				
	
	$result_sql1 = $dba->query($sql1);
	$dba->next_record(); 
	$row_cnt1 = mysql_num_rows($result_sql1);
	
	$staffIdArray = Array();
	$staffNameArray = Array();
	if ($row_cnt1 > 0) {
		$i=0;
		do {
			$staffIdArray[$i] = $dba->f('staff_id');
			
			$sql2 = "SELECT name from new_employee 
			WHERE empid = '$staffIdArray[$i]'
			UNION 
			SELECT name FROM student
			WHERE matrix_no = '$staffIdArray[$i]'";

			$dbc->query($sql2);
			$dbc->next_record(); 
			$staffNameArray[$i] = $dbc->f('name');
		
			$i++;
		} while ($dba->next_record());	
		$row_cnt = $row_cnt1;
	}
	else {
		
		$sql3 = "SELECT staff_id from user_acc 
		WHERE user_status = 'ACTIVE' 
		ORDER BY staff_id";				
		
		$result_sql3 = $dba->query($sql3);
		$dba->next_record(); 
		$row_cnt2 = mysql_num_rows($result_sql3);
		$j=0;
		$l=0;
		
		if ($row_cnt2 > 0 ) {	
			do {
				$staffIdArray[$j] = $dba->f('staff_id');
				$j++;
			} while ($dba->next_record());
					
			for ($k=0; $k<$j; $k++) {
				$sql4 = "SELECT name from new_employee 
				WHERE empid = '$staffIdArray[$k]'
				AND name like '%$searchStaff%'
				UNION 
				SELECT name FROM student
				WHERE matrix_no = '$staffIdArray[$k]'
				AND name like '%$searchStaff%'";
				
				$result_sql4 = $dbc->query($sql4); 
				$dbc->next_record();
				$row_cnt2 = mysql_num_rows($result_sql4);
				
				if ($row_cnt2 > 0) {
					$staffIdArray[$l] = $staffIdArray[$k];
					$staffNameArray[$l] = $dbc->f('name');
					$l++;
				}
			}
		}
		$row_cnt = $l;
	}

}
else {
	$sql = "SELECT staff_id from user_acc 
	WHERE user_status = 'ACTIVE' 
	ORDER BY staff_id";				
	$result = $dba->query($sql);
	$dba->next_record(); 
	$row_cnt = mysql_num_rows($result);		
	$staffIdArray = Array();
	$staffNameArray = Array();
	$i=0;
	do {
		$staffIdArray[$i] = $dba->f('staff_id');
		
		$sql2 = "SELECT name from new_employee 
		WHERE empid = '$staffIdArray[$i]'
		UNION 
		SELECT name FROM student
		WHERE matrix_no = '$staffIdArray[$i]'";
		
		$result_sql2 = $dbc->query($sql2); 
		$dbc->next_record();
		$staffNameArray[$i] = $dbc->f('name');
		$i++;
	} while ($dba->next_record());
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
        function closeThePop(data, data2) 
		{
            parent.$.fn.colorbox.close(); //call the colorbox's close function
            parent.$.fn.getParameterValue(data, data2); // pass the parameter to parent
        }
    </script>
	
	<script language="javascript">
function selValue()
{
	var val = '';
	var val2 = '';
	for(i=0;i<frmPopup.row_cnt.value;i++)
	{
		if(eval("frmPopup.Chk"+i+".checked")==true)
		{
			val = val + eval("frmPopup.Chk"+i+".value") + ',';
			val2 = val2 + eval("frmPopup.staffName"+i+".value") + ',';
		}
	}
	//alert(val);
	//alert(val2);
	//window.opener.document.getElementById("txtSel").value = val;
	//window.close();
	
    parent.$.fn.getParameterValue2(val, val2); // pass the parameter to parent
	parent.$.fn.colorbox.close(); //call the colorbox's close function
	
	return false;
}
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
    left: 870px; 
    border-radius: 20px;
}

.close-btn a {
    font-size: 15px;
    font-weight: bold;
    color: white;
    text-decoration: none;
}

</style>


<h3>Search Recipient </h3>
	<span class="close-btn"><a href="#" onclick="javascript: parent.$.fn.colorbox.close();">X</a></span>

<form id="frmPopup" action=""  method="post" name="frmPopup">
<table>
	<tr>
		<td><label>Staff ID/Matric No/Name</label>
		  </input>
		<td><input type="text" size="50" name="searchStaff" /></input>
		<input type="submit" name="btnSearch" value="Search" class="fancy-button-blue" />
		<input type="submit" name="select" value="Select" class="fancy-button-green" onclick="return selValue();"/> <?=$row_cnt?> result(s) found.</td>
	</tr>
</table>
<table>
	<tr>
		<td><label><span style="color:#FF0000"> Note:</span> If no search value is provided, it will search all.</label></td>
	</tr>
</table>
<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="100%" class="thetable">		
<tr>
	<th width="5%"><label>Tick</label></th>
	<th width="20%"><div align="left"><?php echo load_lang('staff_id'); ?></div></th>
	<th width="75%"><?php echo load_lang('name'); ?></th>
</tr>
<?php
	if ($row_cnt > 0) {
		$inc = 1;
		for ($no = 0; $no < $row_cnt; $no++) {
			if($inc % 2) $color="first-row"; else $color="second-row";
			?>
			<tr class="<?php echo $color; ?>">
			
				<td style="display:none;" align="center"><?php echo $staffIdArray[$no]; ?></td>
				<td align="center"><input name="Chk<?php echo $no;?>" id="Chk<?php echo $no;?>" type="checkbox" value="<?php echo $staffIdArray[$no]?>" /></td>
				<input type="hidden" name="staffName<?php echo $no;?>" id="staffName<?php echo $no;?>" value="<?=$staffNameArray[$no]?>"></input>
				<td align="left"><label><?php echo $staffIdArray[$no]; ?></label></td>
				<td><a href="#" onclick="closeThePop('<?php echo $staffIdArray[$no]; ?>','<?php echo $staffNameArray[$no]; ?>');"><?php echo $staffNameArray[$no]; ?></a></td>
			</tr>    
			<?php				
			$inc++;			
		};
	?>
		<table>
			<tr>
				<input name="row_cnt" type="hidden" value="<?php echo $row_cnt?>">
				<td><input type="submit" name="select" value="Select" class="fancy-button-green" onclick="return selValue();" /></input></td>
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