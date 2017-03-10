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
	<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>
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

 <form id="form1" name="form1" method="post" action="thesis_profile_senate.php?act=search"; >
  <input type="hidden" name="thesis_id" id="thesis_id" value="<?=$thesis_id; ?>">
    
 	<fieldset>
	<legend><strong>LIST OF STUDENT</strong></legend>
			<table>
				<tr>
				  <td width="438"><label>Please enter search criteria below to search postgraduate student:-</label></td>
				</tr>
	</table>
			<br/>
			<table width="98%">	
				<tr>
					<td width="12%" class="tbmenu"><strong>[Thesis/Project ID]/[Title] </strong></td>
					<td width="88%" class="tbmain">
					  <input name="search" value="<?=$search?>" size="30"></input>
				  	  <input type="submit" name="find" value="Search" onClick="return check(document.f1)"/><span style="color:#FF0000"> Note:</span> If no entry is provided, it will search all postgraduate student.</td>
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
					<th width="10%" align="center"><strong>Thesis/Project ID</strong></th>
					<th width="11%" align="center"><strong>Matric No</strong></th>
					<th width="19%" align="center"><strong>Thesis Title</strong></th>
					<th width="10%" align="center"><strong>Cohort</strong></th>
					<th width="24%" align="center"><strong>Name</strong></th>
					<th width="8%" align="center"><strong>Status</strong></th>
					<th width="8%" align="center"><strong>Entry</strong></th>
					<th width="7%" align="center"><strong>Action</strong></th>
				</tr>
			
			<? if($act=="search") 
				{
					$i=$startpoint;
					$sql="select distinct pt.id AS thesis_id, pt.student_matrix_no, pg.thesis_title 
							from pg_thesis pt
							LEFT JOIN pg_proposal pg ON (pg.pg_thesis_id=pt.id)
							where ((pt.id LIKE '%$search%') OR (pg.thesis_title LIKE '%$search%')) 
							AND pg.archived_status IS NULL
							order by pt.id
							LIMIT $startpoint,$perpage";
					$result = $db->query($sql); 
					$db->next_record(); 
					if (mysql_num_rows($result)>0)  {
							$inc = 0;
							$intake_no=array();
							$name=array();
							$stud_stat=array();
							$entry=array();
							$thesis_id=array();
							$thesis_title=array();
							$matrix_no=array();
					
						do  {
							
							$matrix_no[$i]=$db->f(student_matrix_no);
							
							$sqlStudent="select distinct s.matrix_no, s.name, s.rel_mat_no, s.ic_passport,s.passport_no,
									s.student_status,lss.title,lss.title as stud_stat,color, s.entry, le.description, 
									s.print_card_status, s.collect_card_status,s.status_invalid, sp.intake_no
									from student s
									LEFT JOIN student_program sp ON (sp.matrix_no=s.matrix_no)  
									left join lookup_student_stat lss on (lss.title = s.student_status)
									left join lookup_entry le on (le.code=s.entry)
									where s.matrix_no='$matrix_no[$i]'
									order by s.matrix_no ";
							if (substr($matrix_no,0,2) != '07') { 
								$dbConnStudent= $dbc; 
							} 
							else { 
								$dbConnStudent=$dbc1; 
							}
							$dbConnStudent->query($sqlStudent); 
							$row_student=$dbConnStudent->fetchArray();
							
							$intake_no[$i]=$row_student["intake_no"];
							$name[$i]=$row_student["name"];
							$stud_stat[$i]=$row_student["stud_stat"];
							$entry[$i]=$row_student["entry"];
							$thesis_id[$i]=$db->f("thesis_id");
							$thesis_title[$i]=$db->f("thesis_title");
							$i++;
							$inc++;
						}while ($db->next_record());
						
						for ($i=0; $i<$inc; $i++) 
						{

							$thesisTitleString[$i] = strip_tags($thesis_title[$i]);
							
							if (strlen($thesisTitleString[$i]) > 100) 
							{
								$more[$i] = "<a href=\"#\" value=\".$thesis_id[$i].\" title=\"".preg_replace('/"/',"'",$thesis_title[$i])."\"> . .read more</a>";
							}
							//$string;
							$thesisTitleCut[$i] = substr($thesisTitleString[$i], 0, 100);

							?>
						
							<tr>
								<td align="center"><label name="no[]"><?=($i+1)?>.</label></td>
								<td align="center"><label name="thesis_id[]"><?=$thesis_id[$i]?></label></td>
								<td align="center"><label name="matrix_no[]"><?=$matrix_no[$i];?></label></td>
								<td align="left"><label name="thesis_title[]"><?=$thesisTitleCut[$i];?></label><?=$more[$i]?></td>
								<td align="left" nowrap="nowrap"><label name="intake[]"><?=$intake_no[$i];?></label></td>
								<td align="left"><label name="name[]"><?=$name[$i];?></label></td>
								<td align="center"><label name="stud_stat[]"><?=$stud_stat[$i];?></label></td>
								<td align="center"><label name="entry[]"><?=$entry[$i];?></label></td>
								<td align="center"><input type="button" value="View" onClick="javascript:document.location.href='thesis_profile_detail_senate.php?matrix_no=<?=$matrix_no[$i]?>&search=<?=$search?>&act=search';" name="B3"></td>
							</tr>
							<?
							}
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
			?>
			
			
			
				
			</table>
			<?		
				if($act=="search") 
				{	
						$count_total_result = "select count(*) as total
										from pg_thesis pt 
								LEFT JOIN pg_proposal pg ON (pg.pg_thesis_id=pt.id)
								where ((pt.id LIKE '%$search%') OR (pg.thesis_title LIKE '%$search%')) 
								and pg.archived_status IS NULL
								order by pt.id";
								
						$dba->query($count_total_result);
						$dba->next_record();
						$a = $dba->f('total');
						$dba->free();
			
				}
			
				//This is the actual usage of function, It prints the paging links
				doPages($perpage, 'thesis_profile_senate.php', $varParamSend, $a);
			?>
</fieldset>
</body>
</form>
</html>