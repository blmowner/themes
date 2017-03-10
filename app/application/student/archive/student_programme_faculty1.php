<?php

include("../../../lib/common.php");
checkLogin();
session_start();
$userid=$_SESSION['user_id'];
$matrix_no=$_POST['matrix_no'];

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

?>


	
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
	<link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
	<link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
   	<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />

	<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
	<script src="../../../lib/js/jquery.min2.js"></script>
    <script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
	<script language="JavaScript" src="../../../lib/js/windowopen.js"></script>
	<script type="text/javascript">
		$(function() {
			$("#tab1").tabs();
		});
	</script>
	
	

</head>

<script type="text/javascript">
function saveRec()
{
	if(document.form1.find.value=="")
	{
		alert("Please enter student matric / name.");
		return false;
	}
	
}
</script>

<script language="javascript">
function upd_stu(acode){	
		document.location="student_programme.php?id="+acode;
		}
		
</script>

<body>


 <form id="form1" name="form1" method="post" action="student_programme_staff.php?act=search"; >
  <input type="hidden" name="matrix_no" id="matrix_no" value="<?=$matrix_no; ?>">
    
	<fieldset>
	<legend><strong>LIST OF STUDENT</strong></legend>
			<table>
				<tr>
					<td><label>Please enter search criteria below to search the student:-</label></td>
				</tr>
			</table>
			<br/>
			<table width="100%">	
				<tr>
					<td width="20%" class="tbmenu"><strong>Student Name/Matric No</strong></td>
					<td width="80%" class="tbmain"><input name="search" value="<?=$search?>" size="30"><input type="submit" name="find" value="Search" onClick="return check(document.f1)"><span style="color:#FF0000"> Note:</span> If no entry is provided, it will search all postgraduate student.</td>
				</tr>
			</table>
			<table  width="100%">
				<tr>
					<td width="20%"></td>
					<td width="80%"><input name="searchSupervisee" value="<?=$searchSupervisee?>" size="30"><input type="submit" name="find" value="Search My Supervisee" ><span style="color:#FF0000"> Note:</span> If no entry is provided, it will search all students under you.</td>
				</tr>
			</table>			
			<br />
			<table>
				<tr>
					<td><label>Searching Results:-</label></td>
				</tr>
			</table>
			<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="100%" class="thetable">
				<tr>
					<th width="3%" align="center"><strong>No</strong></th>
					<th width="12%" align="center"><strong>Matric No</strong></th>
					<th width="13%" align="center"><strong>NRIC No</strong></th>
					<th width="14%" align="center"><strong>Passport No</strong></th>
					<th width="11%" align="center"><strong>Cohort</strong></th>
					<th width="16%" align="center"><strong>Name</strong></th>
					<th width="11%" align="center"><strong>Status</strong></th>
					<th width="11%" align="center"><strong>Entry</strong></th>
					<th width="9%" align="center"><strong>Action</strong></th>
				</tr>
			
			<? if($act=="search") 
				{	
					$i=$startpoint;
					$sql="select DISTINCT s.matrix_no, s.name, s.rel_mat_no, s.ic_passport,s.passport_no,
							s.student_status as stud_stat, s.entry, sp.intake_no 
							FROM student s 
							LEFT JOIN student_program sp ON (sp.matrix_no=s.matrix_no) 
							LEFT JOIN program p ON (p.programid = sp.program_code) 
							WHERE p.stage IN (4,5) 
							AND (s.matrix_no LIKE '%$search%' OR s.name LIKE '%$search%') 
							AND s.student_status = 'ACTIVE' 
							ORDER BY s.matrix_no
							LIMIT $startpoint,$perpage";
					$result_sql = $dbc->query($sql); 
					$dbc->next_record(); 
					$row_cnt = mysql_num_rows($result_sql);
					if ($row_cnt > 0 )  
					{
						do  {
							$matrix_no=$dbc->f("matrix_no");
							$ic_pasport=$dbc->f("ic_passport");
							$passport_no=$dbc->f("passport_no");
							$intake_no=$dbc->f("intake_no");
							$name=$dbc->f("name");
							$stud_stat=$dbc->f("stud_stat");
							$entry=$dbc->f("entry");
							//}while ($db->next_record());	
							?>
						
							<tr>
								<td align="center"><label name="no[]"><?=($i+1)?></label></td>
								<td align="center"><label name="matrix_no[]"><?=$matrix_no;?></label></td>
								<td align="center"><label name="ic_pasport[]"><?=$ic_pasport;?></label></td>
								<td align="center"><label name="passport_no[]"><?=$passport_no;?></label></td>
								<td align="left" nowrap="nowrap"><label name="intake[]"><?=$intake_no;?></label></td>
								<td align="left"><label name="name[]"><?=$name;?></label></td>
								<td align="center"><label name="stud_stat[]"><?=$stud_stat;?></label></td>
								<td align="center"><label name="entry[]"><?=$entry;?></label></td>
								<td align="center"><input type="button" value="View" onClick="javascript:document.location.href='student_programme_detail.php?matrix_no=<?=$matrix_no?>&act=search&search=<?=$search?>';" name="B3"></td>
							</tr>
							<?
								//$result = $dbc->next_record(); 
								$i++;
							}while ($dbc->next_record());	
					
					}	
					else {
						?>
						<table>
							<tr>
								<td><label>No record found!</label></td>
							</tr>
						</table>
						<?
					}
						
				}
				else if ($act=="searchSupervisee") {
					
				}
			?>
			
			
			
				
			</table>
			<?	if($act=="search") 
				{	
					$count_total_result ="select DISTINCT s.matrix_no, s.name, s.rel_mat_no, s.ic_passport,s.passport_no,
										s.student_status as stud_stat, s.entry, sp.intake_no 
										FROM student s 
										LEFT JOIN student_program sp ON (sp.matrix_no=s.matrix_no) 
										LEFT JOIN program p ON (p.programid = sp.program_code) 
										WHERE p.stage IN (4,5) 
										AND (s.matrix_no LIKE '%$search%' OR s.name LIKE '%$search%') 
										AND s.student_status = 'ACTIVE' 
										ORDER BY s.matrix_no";
						$result = $dbc->query($count_total_result);
						$dbc->next_record();
						$a = mysql_num_rows($result);
						//$a = $dbc->f('total');
						$dbc->free();
			
				}
			
				//This is the actual usage of function, It prints the paging links
				doPages($perpage, 'student_programme_staff.php', $varParamSend, $a);
			?>
	</fieldset>
</body>
</form>
</html>