<?php 
    include("../../../lib/common.php"); 
    checkLogin();

    // used for pagination
    $page = ($page == 0 ? 1 : $page);
    $perpage = 20;
    $startpoint = ($page * $perpage) - $perpage;

if(isset($_POST['btnSearch']) && ($_POST['btnSearch'] <> "")) {
	$searchStudent = $_POST['searchStudent'];

	if ($searchStudent =="") {
		$tmpSearchStaff = "";
	}
	else {
		$tmpSearchStaff = "AND a.empid like '%$searchStudent%' OR a.name like '%$searchStudent%'";			
	}
	
	$sql1 = "SELECT DISTINCT a.pg_student_matrix_no
	FROM pg_supervisor a
	LEFT JOIN pg_thesis b ON (b.id = a.pg_thesis_id)
	LEFT JOIN pg_proposal c ON (c.pg_thesis_id = b.id)
	WHERE b.status = 'INP'
	AND b.status IN ('APC','APP')
	AND c.status IN ('AWC','APP')
	AND b.archived_status IS NULL
	AND c.archived_status IS NULL";

/*	
	$sql1 = "SELECT DISTINCT a.empid, a.name, b.description, a.skype_id, a.unit_id, b.description AS department  
	FROM pg_supervisor 
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
*/	
	$result_sql1 = $dbc->query($sql1);
	$dbc->next_record(); 
	$row_cnt = mysql_num_rows($result_sql1);
	
	$staffIdArray = Array();
	$staffNameArray = Array();
	$i=0;
	if ($row_cnt>0) {
		do {
			$staffIdArray[$i] = $dbc->f('empid');
			$staffNameArray[$i] = $dbc->f('name');
			$unitIdArray[$i] = $dbc->f('department');
			$i++;
		} while ($dbc->next_record());	
	}
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
<style>
.close-btn { 
    border: 2px solid #c2c2c2;
    position: relative;
    padding: 1px 5px;
    bottom: 20px;
    background-color: #605F61;
    left: 620px; 
    border-radius: 20px;
}

.close-btn a {
    font-size: 15px;
    font-weight: bold;
    color: white;
    text-decoration: none;
}

</style>

<?php
    if(!empty($msg)) 
	{
        foreach($msg as $err) 
		{
            echo $err;
        }
    }
	?>



	<form id="frmPopup" action=""  method="post" name="frmPopup">
	<fieldset>
			<legend><strong>Select Student</strong></legend>
<span class="close-btn"><a href="#" onclick="javascript: parent.$.fn.colorbox.close();">X</a></span>

	<table>
		<tr>
			<td><label>Student Matric No/ Name</label></input>
			<td><input type="text" size="50" name="searchStudent" /></input>
			<input type="submit" name="btnSearch" value="Search" class="fancy-button-blue" /></td>
		</tr>
	</table>
	<table>
		<tr>
			<td><label><span style="color:#FF0000"> Note:</span> If no entry is provided, it will search all.</label></td>
		</tr>
	</table>
	<table>
		<tr>							
			<td>Searching Results:- <?=$row_cnt ?> record(s) found.</td>
		</tr>
	</table>
	<?if ($row_cnt <= 0) {?>
		<div id = "tabledisplay" style="overflow:auto; height:80px;">
	<?}
	else if ($row_cnt <= 1) {?>
		<div id = "tabledisplay" style="overflow:auto; height:100px;">
	<?}
	else if ($row_cnt <= 2) {?>
		<div id = "tabledisplay" style="overflow:auto; height:150px;">
	<?}
	else if ($row_cnt <= 3) {
		?>
		<div id = "tabledisplay" style="overflow:auto; height:200px;">
		<?
	}
	else {
		?>
		<div id = "tabledisplay" style="overflow:auto; height:300px;">
		<?
	}?>		
	<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="75%" class="thetable">		
	<tr>
		<th width="5%" align="center"><label>No</label></th>
		<th width="10%" align="center"><div align="left">Matric No</div></th>
		<th width="20%" align="left">Student Name</th>
		<th width="10%" align="left">Thesis / Project ID</th>
		<th width="30%" align="left">Thesis / Project Title</th>

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
					<td><a href="#" onclick="closeThePop('<?php echo $staffIdArray[$no]; ?>','<?=$_GET['field2'];?>','<?php echo $staffNameArray[$no]; ?>','<?=$_GET['field'];?>');"><?php echo $staffNameArray[$no]; ?></a></td>
						<? } else { ?>
						<td><a href="#" onclick="closeThePop('<?php echo $staffIdArray[$no]; ?>','<?php echo $staffNameArray[$no]; ?>');"><?php echo $staffNameArray[$no]; ?></a></td>
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
	</div>
	</fieldset>
	</form>
</body>
</html>