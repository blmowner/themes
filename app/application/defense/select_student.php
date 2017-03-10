<?php 
    include("../../../lib/common.php"); 
    checkLogin();
	
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

    // used for pagination
$page = "";
$page = ($page == 0 ? 1 : $page);
$perpage = 20;
$startpoint = ($page * $perpage) - $perpage;

if(isset($_POST['btnSearch']) && ($_POST['btnSearch'] <> "")) {
	$searchMatrixNo = $_POST['searchMatrixNo'];
	$searchStudentName = $_POST['searchStudentName'];

	if ($searchMatrixNo =="") {
		$tmpSearchMatrixNo = "";
	}
	else {
		$tmpSearchMatrixNo = "AND a.pg_student_matrix_no like '%$searchMatrixNo%'";			
	}

	$sql1 = "SELECT DISTINCT a.pg_student_matrix_no, b.id, c.thesis_title
	FROM pg_supervisor a
	LEFT JOIN pg_thesis b ON (b.id = a.pg_thesis_id)
	LEFT JOIN pg_proposal c ON (c.pg_thesis_id = b.id)
	WHERE b.status = 'INP'
	AND c.status IN ('APC','APP')
	AND c.verified_status IN ('AWC','APP')"
	.$tmpSearchMatrixNo." ".
	"AND b.archived_status IS NULL
	AND c.archived_status IS NULL";

	$result_sql1 = $db->query($sql1);
	$db->next_record(); 
	$row_cnt = mysql_num_rows($result_sql1);
	
	$matrixNoArray = Array();
	$thesisIdArray = Array();
	$thesisTitleArray = Array();
	$studentNameArray = Array();
	$i=0;
	$j=0;
	if ($row_cnt>0) {
		do {
			$matrixNoArray[$i] = $db->f('pg_student_matrix_no');
			$thesisIdArray[$i] = $db->f('id');
			$thesisTitleArray[$i] = $db->f('thesis_title');
			$i++;
		} while ($db->next_record());
		
		for ($k=0;$k<$i;$k++) {
			$sql2 = "SELECT name
			FROM student
			WHERE matrix_no = '$matrixNoArray[$k]'
			AND name like '%$searchStudentName%'";
			
			$result_sql2 = $dbc->query($sql2);
			$dbc->next_record(); 
			$row_cnt2 = mysql_num_rows($result_sql2);
			if ($row_cnt2>0) {
				$studentNameArray[$j] = $dbc->f('name');
				$matrixNoArray[$j] = $matrixNoArray[$k];	
				$thesisIdArray[$j] = $thesisIdArray[$k];
				$thesisTitleArray[$j] = $thesisTitleArray[$k];
				$j++;			
			}
			
		}
		$row_cnt = $j;	
	}
}
else {
	$row_cnt = 0;
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
        function closeThePop(matrixNo, studentName, thesisId) 
		{
            parent.$.fn.colorbox.close(); //call the colorbox's close function
            parent.$.fn.getParameterValue(matrixNo, studentName, thesisId); // pass the parameter to parent
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
	<table>
		<tr>							
			<td>Please enter searching criteria below to find the record:-</td>
		</tr>
	</table>		
	<table>
		<tr>
			<td><label>Student Matric No</label></td>
			<td>:</td>
			<td><input type="text" name="searchMatrixNo" /></td>
		</tr>
		<tr>
			<td><label>Student Name</label></td>
			<td>:</td>
			<td><input type="text" size="50" name="searchStudentName" />
			<input type="submit" name="btnSearch" value="Search" class="fancy-button-blue" /></td>
		</tr>
	</table>
	<table>
		<tr>
			<td><label>Note: If no entry is provided, it will search all.</label></td>
		</tr>
	</table>
	<br/>
	<table>
		<tr>							
			<td>Searching Results:- <?=$row_cnt ?> record(s) found.</td>
		</tr>
	</table>
	<?if ($row_cnt <= 0) {?>
		<div id = "tabledisplay" style="overflow:auto; height:80px;">
	<?}
	else if ($row_cnt <= 1) {?>
		<div id = "tabledisplay" style="overflow:auto; height:150px;">
	<?}
	else if ($row_cnt <= 2) {?>
		<div id = "tabledisplay" style="overflow:auto; height:200px;">
	<?}
	else if ($row_cnt <= 3) {
		?>
		<div id = "tabledisplay" style="overflow:auto; height:250px;">
		<?
	}
	else {
		?>
		<div id = "tabledisplay" style="overflow:auto; height:300px;">
		<?
	}?>		
	<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="90%" class="thetable">		
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
					<td align="center"><?=$no+1?>.</td>
					<td style="display:none;" align="center"><?php echo $matrixNoArray[$no]; ?></td>
					<input type="hidden" name="studentName<?php echo $no;?>" id="studentName<?php echo $no;?>" value="<?=$studentNameArray[$no]?>"></input>
					<td align="left"><label><?php echo $matrixNoArray[$no]; ?></label></td>
					<td><a href="#" onclick="closeThePop('<?=$matrixNoArray[$no];?>','<?=mysql_real_escape_string($studentNameArray[$no]);?>','<?=$thesisIdArray[$no];?>');"><?=$studentNameArray[$no]; ?></a></td>
					<td align="left"><?=$thesisIdArray[$no]; ?></td>
					<?
					// strip tags to avoid breaking any html
						$thesisTitleString[$no] = strip_tags($thesisTitleArray[$no]);
						
						if (strlen($thesisTitleString[$no]) > 100) 
						{
							$more[$no] = "<a href=\"#\" value=\".$thesisIdArray[$no].\" title=\"".preg_replace('/"/',"'",$thesisTitleArray[$no])."\">... Read more</a>";							
						}
						else{
							$more[$no] = "";
						}
						//$string;
						$thesisTitleCut[$no] = substr($thesisTitleString[$no], 0, 100);
					?>
					<td align="left"><?=$thesisTitleCut[$no]?><?=$more[$no]?></td>
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
	<table>
		<tr>							
			<table>
				<tr>
					<td><input type="button" name="btnClose" onclick="javascript: parent.$.fn.colorbox.close();" value="Close" /></input></td>
				</tr>
			</table>
		</tr>
	</table>	
	</form>
</body>
</html>