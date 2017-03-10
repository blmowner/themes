<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: defense_history_staff_detail.php
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
$studentMatrixNo = $_GET['mn'];
$referenceNo = $_GET['ref'];

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
		<title>Defense Proposal History</title>
		<link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
		<link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
		<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />		
		<script src="../../../lib/js/jquery.min2.js"></script>
		<script src="../../../lib/js/jquery.colorbox.js"></script>
		<script src="../../lib/js/jquery.mask_input-1.3.js"></script>
		<script src="../../../lib/js/jquery.min2.js"></script>
   		<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
    	<script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
		<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>	
		<script language="JavaScript" src="../../../lib/js/windowopen.js"></script>
		<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>	
		<script language="JavaScript" type="text/javascript" src="../../../lib/js/tooltip.js"></script>
	</head>
	<body>
		<form id="form1" name="form1" method="post" enctype="multipart/form-data">
			<fieldset>
				<legend><strong>List of Defense Proposal History </strong></legend>
				<table>
					<tr>
						<td>Student Matrix No</td>
						<td>:</td>
						<td><?=$studentMatrixNo?></td>
					</tr>
						<?
						$sql1 = "SELECT name AS student_name
						FROM student
						WHERE matrix_no = '$studentMatrixNo'";
						if (substr($studentMatrixNo,0,2) != '07') { 
							$dbConnStudent= $dbc; 
						} 
						else { 
							$dbConnStudent=$dbc1; 
						}
						$result1 = $dbConnStudent->query($sql1); 
						$dbConnStudent->next_record();
						$sname=$dbConnStudent->f('student_name');
						?>
					<tr>
						<td>Student Name</td>
						<td>:</td>
						<td><?=$sname?></td>
					</tr>  
				</table>
				<br/>

				<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="100%" class="thetable">				
				<?  
					$sql = "SELECT a.id, b.id as pdid, a.pg_thesis_id, a.pg_proposal_id, a.reference_no, 
					DATE_FORMAT(a.submit_date,'%d-%b-%Y %h:%i %p') AS submit_date,
					c.thesis_title, a.submit_status, d1.description as status_desc, b.status, d2.description as detail_status_desc, 
					a.archived_status, d3.description as archived_desc, b.pg_employee_empid, 
					DATE_FORMAT(b.responded_date,'%d-%b-%Y %h:%i %p') AS responded_date,
					b.performance_status, b.work_status
					FROM pg_defense a
					LEFT JOIN pg_defense_detail b ON (b.pg_defense_id = a.id)
					LEFT JOIN pg_proposal c ON (c.id = a.pg_proposal_id)
					LEFT JOIN ref_proposal_status d1 ON (d1.id = a.submit_status)
					LEFT JOIN ref_proposal_status d2 ON (d2.id = b.status)
					LEFT JOIN ref_proposal_status d3 ON (d3.id = b.archived_status)
					WHERE a.reference_no = '$referenceNo'
					AND a.student_matrix_no = '$studentMatrixNo'
					AND b.pg_employee_empid = '$user_id'
					ORDER BY b.id DESC, a.pg_thesis_id, a.pg_proposal_id";

					$result = $db->query($sql); 
					$db->next_record();
					$row_cnt = mysql_num_rows($result);

					$no=$startpoint;
					if ($row_cnt>0) {?>

					<tr>
						<th width="5%"><strong>No.</strong></th>
						<th width="45%"><strong>Thesis/Project</strong></th>
						<th width="10%"><strong>Student Status</strong></th>	
						<th width="10%"><strong>Performance in line with objectives</strong></th>	
						<th width="10%"><strong>Work Progress in line with objectives</strong></th>	
						<th width="10%"><strong>Recommendation for Defence Proposal</strong></th>	
						<th width="10%"><strong>Record Status</strong></th>						
					</tr>
					<?
					
					$id=array();
					$pdid=array();
					$thesisId=array();	
					$proposalId=array();	
					$referenceNo=array();	
					$thesisTitle=array();
					$submitDate=array();
					$submitStatus=array();
					$submitStatusDesc=array();						
					$employeeId=array();
					$detailStatus=array();
					$respondedDate=array();
					$performanceStatus=array();
					$workStatus=array();
					$detailStatusDesc=array();
					$archivedStatus=array();
					$archivedDesc=array();
						
					$i = 0;
					$inc = 0;	
					do {						
						$id[$i]=$db->f('id');
						$pdid[$i]=$db->f('pdid');
						$thesisId[$i]=$db->f('pg_thesis_id');	
						$proposalId[$i]=$db->f('pg_proposal_id');	
						$referenceNo[$i]=$db->f('reference_no');	
						$thesisTitle[$i]=$db->f('thesis_title');
						$submitDate[$i]=$db->f('submit_date');
						$submitStatus[$i]=$db->f('submit_status');
						$submitStatusDesc[$i]=$db->f('status_desc');						
						$detailStatus[$i]=$db->f('detail_status');
						$respondedDate[$i]=$db->f('responded_date');
						$performanceStatus[$i]=$db->f('performance_status');
						$workStatus[$i]=$db->f('work_status');
						$detailStatusDesc[$i]=$db->f('detail_status_desc');
						$archivedStatus[$i]=$db->f('archived_status');
						$archivedDesc[$i]=$db->f('archived_desc');
						
						$i++;
						$inc++;			
					}while($db->next_record());
					
					for ($i=0; $i<$inc; $i++) 
					{
						// strip tags to avoid breaking any html
						$thesisTitleString[$i] = strip_tags($thesisTitle[$i]);
						
						if (strlen($thesisTitleString[$i]) > 100) 
						{
							$more[$i] = "<a href=\"#\" value=\".$thesisId[$i].\" title=\"".preg_replace('/"/',"'",$thesisTitle[$i])."\">... Read more</a>";
						}
						//$string;
						$thesisTitleCut[$i] = substr($thesisTitleString[$i], 0, 100);
						if($i % 2) $color ="first-row"; else $color = "second-row";
					?>
						<tr class="<?=$color?>">
							<td align="center"><?=++$no?>.</td>
							<td>Thesis / Project ID: <label><a href="../defense/defense_history_outline_staff.php?pid=<?=$proposalId[$i];?>&mn=<?=$studentMatrixNo?>&ref=<?=$referenceNo[$i]?>"><?=$thesisId[$i]?></a></label><br/>
							Reference No: <label><a href="../defense/view_defense_history_staff.php?pid=<?=$proposalId[$i];?>&tid=<?=$thesisId[$i]?>&id=<?=$id[$i]?>&pdid=<?=$pdid[$i]?>&mn=<?=$studentMatrixNo?>&ref=<?=$referenceNo[$i]?>"><?=$referenceNo[$i]?></a></label><br/><br/>
							<strong>Thesis Title:</strong><br/> <label><?=$thesisTitleCut[$i]?></label><?=$more[$i]?></label></td>
							<td><label><?=$submitStatusDesc[$i]?><br/><?=$submitDate[$i]?><br/><br/></label></td>
							<?if ($performanceStatus[$i]=="Y") $performanceStatusDesc = "Yes"; 
							else if ($performanceStatus[$i]=="N") $performanceStatusDesc = "No";
							else $performanceStatusDesc = "";?>
							<td align="center"><label><?=$performanceStatusDesc?></label></td>
							<?if ($workStatus[$i]=="Y") $workStatusDesc = "Yes"; 
							else if ($workStatus[$i]=="N") $workStatusDesc = "No";
							else $workStatusDesc = "";?>
							<td align="center"><label><?=$workStatusDesc?></label></td>
							<td><label><?=$detailStatusDesc[$i]?><br/><?=$respondedDate[$i]?></label></td>
							<td><label><?=$archivedDesc[$i]?></label></td>
						</tr>
					<?
					
					}

					?>					
					  </table>
					  </fieldset>
					  <table>
						<tr>
							<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../defense/defense_history_staff.php';" /></td>
						</tr>
					</table>
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




