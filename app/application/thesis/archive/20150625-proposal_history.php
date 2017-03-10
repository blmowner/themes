<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: proposal_history.php
//
// Created by: Fizmie
// Created Date: 24 Dec 2014
// Modified by: Zuraimi
// Modified Date: 24 Dec 2014
//
//**************************************************************************************


//Read common library for page execution i.e. database connection. login validation
include("../../../lib/common.php");
//checkLogin();

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
	
$sql="SELECT a.pg_thesis_id, a.id, DATE_FORMAT(a.modify_date,'%d-%b-%Y %h:%i %p') AS theModifiedDate, a.thesis_title, 
a.thesis_type, b.description AS theThesisTypeDescription, a.introduction, a.objective, a.description, a.verified_by, 
DATE_FORMAT(a.verified_date,'%d-%b-%Y') AS theVerifiedDate, a.verified_remarks, a.verified_status, a.status, a.discussion_status, c1.description AS confirm_desc, c2.description AS status_desc,
a.archived_status, c3.description as archived_desc
FROM pg_proposal a
LEFT JOIN ref_thesis_type b ON (b.id=a.thesis_type)
LEFT JOIN ref_proposal_status c1 ON (c1.id=a.verified_status)
LEFT JOIN ref_proposal_status c2 ON (c2.id=a.status)
LEFT JOIN ref_proposal_status c3 ON (c3.id=a.archived_status)
LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id)
WHERE d.student_matrix_no = '$userid'
ORDER BY a.id desc
LIMIT $startpoint, $perpage";
		
		
?>

	

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Thesis History</title>
		<link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
		<link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
		<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
		<link rel="stylesheet" href="../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
		<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
		<script src="../../../lib/js/jquery.min2.js"></script>
		<script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
		<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>
	</head>
	<body oncontextmenu="return false;">
		<form id="form1" name="form1" method="post" enctype="multipart/form-data">
			<fieldset>
				<legend><strong>SUMMARY LIST</strong></legend>
				<table width="100%" border="1" cellpadding="3" cellspacing="1" style="border-collapse:collapse;" class="thetable">
				<?  
					$result = $db->query($sql); 
					//echo "sql ".$sql;
					//var_dump($db);
					$db->next_record();
					$row_cnt = mysql_num_rows($result);

					$no=$startpoint;
					if ($row_cnt>0) {?>

					<tr>
						<th width="24" align="center"><strong>No.</strong></th>
						<th width="120"><strong>Thesis/Project ID</strong></th>
						<th width="91"><strong>Proposal ID</strong></th>						
						<th width="263"><strong>Thesis/Project Title</strong></th>
						<th width="130"><strong>Modification Date</strong></th>
						<th width="120"><strong>Faculty Status</strong></th>	
						<th width="120"><strong>Senate Status</strong></th>	
						<th width="120"><strong>Archived Status</strong></th>						
					</tr>
					<?
					
					do {						
						$pgThesisId=$db->f('pg_thesis_id');	
						$id=$db->f('id');	
						$modifiedDate=$db->f('theModifiedDate');
						$thesisTitle=$db->f('thesis_title');
						$confirmDesc=$db->f('confirm_desc');	
						$statusDesc=$db->f('status_desc');						
						$status=$db->f('status');
						$archivedStatus=$db->f('archived_status');
						$archivedDesc=$db->f('archived_desc');
					?>
						<tr>
							<td align="center"><?=++$no?>.</td>
							<td><label><?=$pgThesisId?></label></td>
							<td><label><a href="../thesis/detail_proposal_history_student.php?pid=<?=$id;?>"</a><?=$id?></label></td>						
							<td><label><?=$thesisTitle?></label></td>
							<td><label><?=$modifiedDate?></label></td>
							<td><label><?=$confirmDesc?></label><div align="center"></div></td>
							<td><label><?=$statusDesc?></label></td>
							<td><label><?=$archivedDesc?></label></td>
						</tr>
					<?
					}while($db->next_record());
					

					?>					
		  </table>
		  </fieldset>
			<?
				$count_total_result ="SELECT count(*) as total from pg_proposal a, pg_thesis b 
				WHERE a.pg_thesis_id = b.id AND b.student_matrix_no = '$userid'";
				$db->query($count_total_result);
				$db->next_record();
				$a = $db->f('total');
				$db->free();
			
				//This is the actual usage of function, It prints the paging links
				doPages($perpage, 'proposal_history.php', $varParamSend, $a);					
				
				}
				else {
					?>
					<table>
						<tr>
							<td>
								<p><span style="color:#FF0000">Note: </span>You don't have thesis history to view! Please click <strong>Thesis </strong> tab to create a new one and submit. </p>
							</td>
						</tr>
					</table>
					<?
				}
			?>
		</form>
	</body>
</html>




