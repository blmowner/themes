<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: progress_history.php
//
// Created by: Zuraimi
// Created Date: 06 April 2015
// Modified by: Zuraimi
// Modified Date: 06 April 2015
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



?>

	

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Monthly Progress Report History</title>
		<link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
		<link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
		<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
		<link rel="stylesheet" href="../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
		<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
		<script src="../../../lib/js/jquery.min2.js"></script>
		<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>
		<script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
	</head>
	<body>
		<form id="form1" name="form1" method="post" enctype="multipart/form-data">
			<fieldset>
				<?
				$sql = "SELECT a.id, b.id as pdid, a.pg_thesis_id, a.pg_proposal_id, a.reference_no, 
					DATE_FORMAT(a.submit_date,'%d-%b-%Y %h:%i %p') AS submit_date,
					c.thesis_title, a.submit_status, d1.description as status_desc, b.status, d2.description as detail_status_desc, 
					a.archived_status, d3.description as archived_desc, b.pg_employee_empid, 
					DATE_FORMAT(b.responded_date,'%d-%b-%Y %h:%i %p') AS responded_date
					FROM pg_progress a
					LEFT JOIN pg_progress_detail b ON (b.pg_progress_id = a.id)
					LEFT JOIN pg_proposal c ON (c.id = a.pg_proposal_id)
					LEFT JOIN ref_proposal_status d1 ON (d1.id = a.submit_status)
					LEFT JOIN ref_proposal_status d2 ON (d2.id = b.status)
					LEFT JOIN ref_proposal_status d3 ON (d3.id = b.archived_status)
					WHERE a.student_matrix_no = '$user_id'
					ORDER BY b.id DESC, a.pg_thesis_id, a.pg_proposal_id";

					$result = $db->query($sql); 
					$db->next_record();
					$row_cnt = mysql_num_rows($result);

					$no=$startpoint;
					
					
				?>
				<legend><strong>Summary List - </strong><?=$row_cnt?> record(s) found.</legend>
				
					<div id = "tabledisplay" style="overflow:auto; height:700px;">		
					<table width="100%" border="1" cellpadding="3" cellspacing="1" style="border-collapse:collapse;" class="thetable">
					<?  
					
					if ($row_cnt>0) {?>

					<tr>
						<th><strong>No.</strong></th>
						<th><strong>ID</strong></th>
						<th><strong>Thesis/Project ID</strong></th>
						<th><strong>Thesis/Project Title</strong></th>
						<th><strong>Reference No.</strong></th>						
						<th><strong>Student Status</strong></th>	
						<th><strong>Supervisor Status</strong></th>	
						<th><strong>Record Status</strong></th>						
					</tr>
					<?
					
				
					do {						
						$id=$db->f('id');
						$pdid=$db->f('pdid');
						$thesisId=$db->f('pg_thesis_id');	
						$proposalId=$db->f('pg_proposal_id');	
						$referenceNo=$db->f('reference_no');	
						$thesisTitle=$db->f('thesis_title');
						$submitDate=$db->f('submit_date');
						$submitStatus=$db->f('submit_status');
						$submitStatusDesc=$db->f('status_desc');						
						$employeeId=$db->f('pg_employee_empid');
						$detailStatus=$db->f('detail_status');
						$respondedDate=$db->f('responded_date');
						$detailStatusDesc=$db->f('detail_status_desc');
						$archivedStatus=$db->f('archived_status');
						$archivedDesc=$db->f('archived_desc');
					?>
						<tr>
							<td align="center"><?=++$no?>.</td>
							<td><label><?=$pdid?></label></td>
							<td><label><a href="../monthlyreport/progress_detail_history.php?pid=<?=$proposalId;?>"><?=$thesisId?></a></label></td>
							<td><label><?=$thesisTitle?></label></td>
							<td><label><a href="../monthlyreport/view_progress_history.php?pid=<?=$proposalId;?>&tid=<?=$thesisId?>&id=<?=$id?>&pdid=<?=$pdid?>&empid=<?=$employeeId?>"><?=$referenceNo?></a></label></td>
							<td><label><?=$submitStatusDesc?><br/><?=$submitDate?><br/><br/></label></td>
							<?
							$sql2="SELECT name AS employee_name
							FROM new_employee
							WHERE empid = '$employeeId'";
							
							$dbc->query($sql2);
							$row_personal=$dbc->fetchArray();
							$employeeName=$row_personal['employee_name'];
							?>
							<td><label><?=$detailStatusDesc?><br/><?=$respondedDate?><br/><?=$employeeName?></label></td>
							<td><label><?=$archivedDesc?></label></td>
						</tr>
					<?
					}while($db->next_record());
					

					?>					
		  </table>
		  </div>
		  </fieldset>
			<?
				
				
				}
				else {
					?>
					<table>
						<tr>
							<td>
								<p>You don't have progress report history to view!</p>
							</td>
						</tr>
					</table>
					<?
				}
			?>
		</form>
	</body>
</html>




