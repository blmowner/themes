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

/*$sql = "select a.pg_thesis_id, a.id, DATE_FORMAT(a.report_date,'%d-%b-%Y %h:%i:%s %p') as theReportDate, a.thesis_title, a.thesis_type, 
		b.description as theThesisTypeDescription, a.introduction, a.objective, a.description, a.feedback_by, a.feedback_date, 
		a.feedback_remarks, a.status, a.discussion_status, a.proposal_remarks, c.description as theProposalStatusDescription 
		from pg_proposal a, ref_thesis_type b, ref_proposal_status c
		where a.thesis_type = b.id
		AND a.status = c.id
		AND a.pg_thesis_id in 
			(SELECT id from pg_thesis 
			WHERE student_matrix_no = '$userid'
			AND status = 'INP')
		ORDER BY a.report_date 
		LIMIT $startpoint, $perpage";*/

		
		$sql="SELECT a.pg_thesis_id, a.id, DATE_FORMAT(a.report_date,'%d-%b-%Y %h:%i:%s %p') AS theReportDate, a.thesis_title, 
		a.thesis_type, b.description AS theThesisTypeDescription, a.introduction, a.objective, a.description, a.feedback_by, 
		a.feedback_date, a.feedback_remarks, a.status, a.discussion_status, a.proposal_remarks, c.description AS theProposalStatusDescription FROM pg_proposal a

		LEFT JOIN ref_thesis_type b ON (b.id=a.thesis_type)
		LEFT JOIN ref_proposal_status c ON (c.id=a.status)
		LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id)

		AND d.student_matrix_no = '$userid'
		AND d.status = 'INP'
		ORDER BY a.report_date
		LIMIT $startpoint, $perpage";
		
		$result = $db->query($sql); 
		//echo $sql;
		//var_dump($db);
		$db->next_record();
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
					<td><strong>Summary List</strong>
					</td>
				</tr>
			</table>
			<table >
				<?  
					if ($result) {?>

					<tr>
						<td>Thesis/Project ID</td>
						<td>Proposal ID</td>	
						<td>Report Date</td>
						<td>Thesis/Project Title</td>
						<td>Thesis Type</td>
						<td>Proposal Status</td>		
					</tr>
					<?
					
					do {						
						$pgThesisId=$db->f('pg_thesis_id');	
						$id=$db->f('id');	
						$reportDate=$db->f('theReportDate');
						$thesisTitle=$db->f('thesis_title');
						$thesisType=$db->f('thesis_type');		
						$theThesisTypeDescription=$db->f('theThesisTypeDescription');				
						$theProposalStatusDescription=$db->f('theProposalStatusDescription');						
						$status=$db->f('status');
					?>
						<tr>
							<td><input type="text" name="pgThesisId" size="15" id="pgThesisId" value="<?=$pgThesisId?>" disabled="disabled"/></td>
							<td><input type="text" name="id" size="15" id="id" value="<?=$id?>" disabled="disabled"/></td>
							<td><input type="text" name="reportDate" size="25" id="reportDate" value="<?=$reportDate?>" disabled="disabled"/></td>
							<td><textarea name="thesisTitle" cols="45" id="thesisTitle" disabled="disabled"><?=$thesisTitle?></textarea></td>
							<td><input type="text" name="thesisType" size="15" id="thesisType" value="<?=$theThesisTypeDescription?>" disabled="disabled"/></td>
							<td><input type="text" name="status" size="20" id="status" value="<?=$theProposalStatusDescription?>" disabled="disabled"/></td>
							<td><a href="detail_proposal_history.php?pid=<?php echo $id;?>">View</a></td>
						</tr>
					<?
					}while($db->next_record());						  
				
				}
				else {
					?>
					<table>
						<tr>
							<td>
								<p>No record found to display!</p>
							</td>
						</tr>
					</table>
					<?
				}
				
				?>					
			</table>
			<?
				$count_total_result ="SELECT count(*) as total from pg_proposal a, pg_thesis b 
				WHERE a.pg_thesis_id = b.id AND b.student_matrix_no = '$userid'";
				$db->query($count_total_result);
				$db->next_record();
				$a = $db->f('total');
				$db->free();
			
				//This is the actual usage of function, It prints the paging links
				doPages($perpage, 'proposal_history.php', $varParamSend, $a);
			?>
		</form>
	</body>
</html>




