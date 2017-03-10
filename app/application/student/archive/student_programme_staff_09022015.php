<?php

include("../../../lib/common.php");
checkLogin();
session_start();
$userid=$_SESSION['user_id'];


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

<? 

function cohort($matrix)
{
global $db;
$sqlcohort="select * from student_program where matrix_no='$matrix'";
$dbcohort=$db;
$dbcohort->query($sqlcohort);
$nxcohort=$dbcohort->next_record();
return $dbcohort->f("intake_no");
}

?>

 <form id="form1" name="form1" method="post" action="student_programme_staff.php?act=search"; >
  <input type="hidden" name="matrix_no" id="matrix_no" value="<?=$matrix_no; ?>">
    
 	<hr align="left"><strong>STUDENT LIST</strong></b><br>
		<hr align="left">
			<table width="98%">	
				<tr>
				<td colspan="12" valign="top" class="tbtitle" height="19"><strong>LIST&nbsp;OF&nbsp;STUDENT</strong></td>
				</tr>
				<tr>
				<td width="12%" class="tbmenu"><strong>Find </strong></td>
				<td width="88%" class="tbmain">
					  <input name="search" value="<?=$search?>" size="30">
				  	  <input type="submit" name="find" value="Find" onClick="return check(document.f1)"></td>
				</tr>
			</table>
			
			<br />
			<table border="1" cellpadding="0" cellspacing="0" width="100%" bordercolorlight="#cccccc" bordercolordark="#eeeeee"> 
				<tr>
					<td width="3%" align="center">No</td>
					<td width="12%" align="center">Matric No</td>
					<td width="13%" align="center">NRIC No</td>
					<td width="14%" align="center">Passport No</td>
					<td width="11%" align="center">Cohort</td>
					<td width="16%" align="center">Name</td>
					<td width="11%" align="center">Status</td>
					<td width="11%" align="center">Entry</td>
					<td width="9%" align="center">Action</td>
				</tr>
			
			<? if($act=="search") 
				{
						$sql="select s.matrix_no, s.name, s.rel_mat_no, s.ic_passport,s.passport_no,
								s.student_status,title,lss.id as stud_stat,color, s.entry, le.description, 
								s.print_card_status, s.collect_card_status,s.status_invalid, sp.intake_no 
								from student s
								LEFT JOIN student_program sp ON (sp.matrix_no=s.matrix_no)  
								left join lookup_student_stat lss on (lss.title = s.student_status)
								left join lookup_entry le on (le.code=s.entry)
								where sp.program_code in ('MAF','MBA','MBM','MBS','MCS','MFST','MICT','MMB','MMG','PAF','PBM','PCS','PFST','PICT','PMB') 
								AND (s.matrix_no like '%$search%' and (s.status_invalid='' OR s.status_invalid is null))
								OR (s.name like '%$search%' and (s.status_invalid='' OR s.status_invalid is null)) 
								AND s.student_status like 'ACTIVE'
								order by s.matrix_no
								LIMIT $startpoint,$perpage";
						$db->query($sql); 
						$result = $db->next_record(); 
							
					
					$i=0;
					do  {
						$matrix_no=$db->f("matrix_no");
						$ic_pasport=$db->f("ic_passport");
						$passport_no=$db->f("passport_no");
						$intake_no=$db->f("intake_no");
						$name=$db->f("name");
						$stud_stat=$db->f("stud_stat");
						$entry=$db->f("entry");
						//}while ($db->next_record());	
						?>
					
						<tr>
							<td align="center"><label name="no[]"><?=$i++;?></label></td>
							<td align="center"><label name="matrix_no[]"><?=$matrix_no;?></label></td>
							<td align="center"><label name="ic_pasport[]"><?=$ic_pasport;?></label></td>
							<td align="center"><label name="passport_no[]"><?=$passport_no;?></label></td>
							<td align="left" nowrap="nowrap"><label name="intake[]"><?=$intake_no;?></label></td>
							<td align="left"><label name="name[]"><?=$name;?></label></td>
							<td align="center"><label name="stud_stat[]"><?=$stud_stat;?></label></td>
							<td align="center"><label name="entry[]"><?=$entry;?></label></td>
							<td align="center"><input type="button" value="View" onClick="javascript:document.location.href='student_programme_detail.php?matrix_no=<?=$matrix_no?>';" name="B3"></td>
						</tr>
						<?
						
						}while ($db->next_record());	
					
					
				}
			?>
			
			
			
				
			</table>
			<?		
			$count_total_result ="select count(*) as total
								from student s
								LEFT JOIN student_program sp ON (sp.matrix_no=s.matrix_no)  
								left join lookup_student_stat lss on (lss.title = s.student_status)
								left join lookup_entry le on (le.code=s.entry)
								where sp.program_code in ('MAF','MBA','MBM','MBS','MCS','MFST','MICT','MMB','MMG','PAF','PBM','PCS','PFST','PICT','PMB') 
								AND (s.matrix_no like '%$search%' and (s.status_invalid='' OR s.status_invalid is null))
								OR (s.name like '%$search%' and (s.status_invalid='' OR s.status_invalid is null)) 
								AND s.student_status like 'ACTIVE'
								order by s.matrix_no";
				$dba->query($count_total_result);
				$dba->next_record();
				$a = $dba->f('total');
				$dba->free();
			

			
				//This is the actual usage of function, It prints the paging links
				doPages($perpage, 'student_programme_staff.php', $varParamSend, $a);
			?>
	
</body>
</form>
</html>