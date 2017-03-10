<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: proposal_history_staff.php
//
// Created by: Zuraimi
// Created Date: 07-Jan-15
// Modified by: Zuraimi
// Modified Date: 07-Jan-15, 08-Jan-15, 09-Jan-15 (After review on 08-Jan-15)
//
//**************************************************************************************


//Read common library for page execution i.e. database connection. login validation
include("../../../lib/common.php");
checkLogin();

session_start();
$userid=$_SESSION['user_id'];
$studentMatrixNo2='';
//$studentMatrixNo1='';



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
	
$sql1 = "SELECT DISTINCT d.student_matrix_no, e.name
		FROM pg_proposal a
		LEFT JOIN ref_thesis_type b ON (b.id=a.thesis_type)
		LEFT JOIN ref_proposal_status c1 ON (c1.id=a.confirm_status)
		LEFT JOIN ref_proposal_status c2 ON (c2.id=a.status)
		LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id)
		LEFT JOIN student e ON (e.matrix_no = d.student_matrix_no)
		WHERE d.status = 'INP'
		ORDER BY d.student_matrix_no, e.name DESC
		LIMIT $startpoint, $perpage";
	
		$result1 = $db->query($sql1); 
		$db->next_record();
		//echo 'sql1 -->'.$sql1;

		
if(isset($_POST['btnNext']) || ($_POST['btnNext'] <> ""))
{
	$studentMatrixNo = $_POST['dropDownStudent'];	
	//echo "btnNext - studentMatrixNo ".$studentMatrixNo."<br/>";	
	$_SESSION['studentMatrixNo1'] = $studentMatrixNo;	

	
}


?>

	

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Thesis History</title>
			<link rel="stylesheet" type="text/css" href="../../theme/css/<?php echo $css; ?>" />
			<link rel="stylesheet" type="text/css" href="../../theme/css/colorbox.css" media="screen" />
			<link rel="stylesheet" href="../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />    
			<script src="../../lib/js/jquery.min2.js"></script>
			<script src="../../lib/js/jquery.colorbox.js"></script>
			<script src="../../lib/js/jquery.mask_input-1.3.js"></script>
			<script type="text/javascript" src="../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
			<script src="../../lib/js/datePicker/jquery.ui.core.js"></script>
			<script src="../../lib/js/datePicker/jquery.ui.widget.js"></script>
			<script src="../../lib/js/datePicker/jquery.ui.datepicker.js"></script>
	</head>
	<body>
		<form id="form1" name="form1" method="post" enctype="multipart/form-data">
			<table>
				<tr>
					<td><strong>Search Student</strong>
					</td>
				</tr>
			</table>
			<table>
				<tr>
					<td>Please select student </td>			
					<td>
						<select name = "dropDownStudent">
							<option value="" selected="selected"></option>
							<option value="All" >All Student</option>							
						<?					
							do {						
								$studentMatrixNo=$db->f('student_matrix_no');							
								$name=$db->f('name');
								if (strcmp($studentMatrixNo,$studentMatrixNoTmp)==!true) {
								?>
									<option value="<?=$studentMatrixNo?>" selected="selected"><?=$studentMatrixNo?> - <?=$name?></option>
								<?
								}
								else {?>
									<option value="<?=$studentMatrixNo?>"><?=$studentMatrixNo;?> - <?=$name?></option>
								<?}
							}while($db->next_record());	?>
						</select>
					</td>					
					<br/>				
					<td><input type="submit" name="btnNext" id="btnNext" align="center"  value="Next" /></td>
				</tr>
			</table>
			<br />
		<?$studentMatrixNo2=$_SESSION['studentMatrixNo1'];
		//echo "y studentMatrixNo2 ".$studentMatrixNo2;
		?><br/><?
		//$_SESSION['studentMatrixNo']=$studentMatrixNo2;?>
		
		<?if ($dropDownStudent=='All') 
		{
		?>			
			
				<?
				$sql2 = "SELECT d.student_matrix_no, e.name, a.pg_thesis_id, a.id, DATE_FORMAT(a.modify_date,'%d-%b-%Y %h:%i %p') AS theModifiedDate,  	
				a.thesis_title, a.thesis_type, b.description AS theThesisTypeDescription, a.introduction, a.objective, a.description, a.feedback_by, DATE_FORMAT(a.feedback_date,'%d-%b-%Y') AS theFeedbackDate, a.feedback_remarks, a.confirm_status, a.status, a.discussion_status, a.proposal_remarks, c1.description AS confirm_desc, c2.description AS status_desc
				FROM pg_proposal a
				LEFT JOIN ref_thesis_type b ON (b.id=a.thesis_type)
				LEFT JOIN ref_proposal_status c1 ON (c1.id=a.confirm_status)
				LEFT JOIN ref_proposal_status c2 ON (c2.id=a.status)
				LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id)
				LEFT JOIN student e ON (e.matrix_no = d.student_matrix_no)
				WHERE d.status = 'INP'
				ORDER BY a.pg_thesis_id, a.id DESC
				LIMIT $startpoint, $perpage";

				//echo "sql2 ".$sql2;
				//var_dump($db);
				$result2 = $dba->query($sql2); 
				$dba->next_record();

		
				$row_cnt = mysql_num_rows($result2);				
				if ($row_cnt>0) {?>
				<table>
					<tr>
						<td><strong>Search Result for <?=$dropDownStudent?> Student</strong></td>
					</tr>
				</table>
				<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" >				
				<tr>
					<td width="24"><strong>No.</strong></td>
					<td width="200"><strong>Student Name</strong></td>
					<td width="120"><strong>Thesis/Project ID</strong></td>
					<td width="91"><strong>Proposal ID</strong></td>						
					<td width="263"><strong>Thesis/Project Title</strong></td>
					<td width="139"><strong>Modification Date</strong></td>
					<td width="97"><strong>Faculty Status</strong></td>	
					<td width="95"><strong>Senate Status</strong></td>							
				</tr>
				<?
				$no=1;
				do {						
					$studentName = $dba->f('name');	
					$studentMatrixNo = $dba->f('student_matrix_no');	
					$pgThesisId=$dba->f('pg_thesis_id');	
					$id=$dba->f('id');	
					$modifiedDate=$dba->f('theModifiedDate');
					$thesisTitle=$dba->f('thesis_title');
					$thesisType=$dba->f('thesis_type');		
					$theThesisTypeDescription=$dba->f('theThesisTypeDescription');				
					$confirmDesc=$dba->f('confirm_desc');	
					$statusDesc=$dba->f('status_desc');						
					$status=$dba->f('status');
				?>
					<tr>
						<td><?=$no?>.</td>
						<td><label><a href="detail_proposal_history_staff.php?pid=<?php echo $id;?>" title="Matrix No. :<?=$studentMatrixNo?>"><?=$studentName?></a></label></td>
						<td><label><?=$pgThesisId?></label></td>
						<td><label><?=$id?></label></td>						
						<td><label><?=$thesisTitle?></label></td>
						<td><label><?=$modifiedDate?></label></td>
						<td><label><?=$confirmDesc?></label><div align="center"></div></td>
						<td><label><?=$statusDesc?></label></td>						
					</tr>
				<?
				$no=$no+1;
				}while($dba->next_record());	

				?>					
		  </table>
			<?
				$count_total_result ="SELECT count(*) as total from pg_proposal a, pg_thesis b 
				WHERE a.pg_thesis_id = b.id";
				$dbg->query($count_total_result);
				$dbg->next_record();
				$a = $dbg->f('total');
				$dbg->free();
			
				//This is the actual usage of function, It prints the paging links
				doPages($perpage, 'proposal_history_staff.php', $varParamSend, $a);					
				
				}

		}
		else //if ($studentMatrixNo2!='All' || $studentMatrixNo2<>'')
		{
				$sql3 = "SELECT d.student_matrix_no, e.name, a.pg_thesis_id, a.id, DATE_FORMAT(a.modify_date,'%d-%b-%Y %h:%i %p') AS theModifiedDate, 	
				a.thesis_title, a.thesis_type, b.description AS theThesisTypeDescription, a.introduction, a.objective, a.description, a.feedback_by, DATE_FORMAT(a.feedback_date,'%d-%b-%Y') AS theFeedbackDate, a.feedback_remarks, a.confirm_status, a.status, a.discussion_status, a.proposal_remarks, c1.description AS confirm_desc, c2.description AS status_desc
				FROM pg_proposal a
				LEFT JOIN ref_thesis_type b ON (b.id=a.thesis_type)
				LEFT JOIN ref_proposal_status c1 ON (c1.id=a.confirm_status)
				LEFT JOIN ref_proposal_status c2 ON (c2.id=a.status)
				LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id)
				LEFT JOIN student e ON (e.matrix_no = d.student_matrix_no)
				WHERE d.status = 'INP'
				AND d.student_matrix_no = '$studentMatrixNo2'
				ORDER BY a.pg_thesis_id, a.id DESC
				LIMIT $startpoint, $perpage";

				//echo "sql3 ".$sql3;
				//var_dump($db);
				$result3 = $dba->query($sql3); 
				$dba->next_record();
		
				$row_cnt1 = mysql_num_rows($result3);				
				if ($row_cnt1>0) {?>
				<table>
					<tr>
						<td><strong>Search Result for Matrix No. <?=$studentMatrixNo?> <?=$name?></strong></td>
					</tr>
				</table>
				<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" >

				<tr>
					<td width="24"><strong>No.</strong></td>
					<td width="200"><strong>Student Name</strong></td>
					<td width="120"><strong>Thesis/Project ID</strong></td>
					<td width="91"><strong>Proposal ID</strong></td>						
					<td width="263"><strong>Thesis/Project Title</strong></td>
					<td width="139"><strong>Modification Date</strong></td>
					<td width="97"><strong>Faculty Status</strong></td>	
					<td width="95"><strong>Senate Status</strong></td>				
				</tr>
				<?
				$no=1;
				do {						
					$studentName = $dba->f('name');	
					$studentMatrixNo = $dba->f('student_matrix_no');	
					$pgThesisId=$dba->f('pg_thesis_id');	
					$id=$dba->f('id');	
					$modifiedDate=$dba->f('theModifiedDate');
					$thesisTitle=$dba->f('thesis_title');
					$thesisType=$dba->f('thesis_type');		
					$theThesisTypeDescription=$dba->f('theThesisTypeDescription');				
					$confirmDesc=$dba->f('confirm_desc');	
					$statusDesc=$dba->f('status_desc');						
					$status=$dba->f('status');
				?>
					<tr>
						<td><?=$no?>.</td>
						<td><label><a href="detail_proposal_history_staff.php?pid=<?php echo $id;?>" title="Matrix No. :<?=$studentMatrixNo?>"><?=$studentName?></a></label></td>
						<td><label><?=$pgThesisId?></label></td>
						<td><label><?=$id?></label></td>						
						<td><label><?=$thesisTitle?></label></td>
						<td><label><?=$modifiedDate?></label></td>
						<td><label><?=$confirmDesc?></label><div align="center"></div></td>
						<td><label><?=$statusDesc?></label></td>						
					</tr>
				<?
				$no=$no+1;
				}while($dba->next_record());	

				?>					
			</table>
			<?
				$count_total_result ="SELECT count(*) as total from pg_proposal a, pg_thesis b 
				WHERE a.pg_thesis_id = b.id";
				$dbg->query($count_total_result);
				$dbg->next_record();
				$a = $dbg->f('total');
				$dbg->free();
			
				//This is the actual usage of function, It prints the paging links
				doPages($perpage, 'proposal_history_staff.php', $varParamSend, $a);					
				
				}

		}
		?>
		</form>
	</body>
</html>




