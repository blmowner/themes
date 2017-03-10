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
$matrix_no=$_GET['matrix_no']; 

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
DATE_FORMAT(a.verified_date,'%d-%b-%Y') AS theVerifiedDate, a.verified_remarks, a.verified_status, a.status, a.discussion_status,c1.description AS confirm_desc, c2.description AS status_desc,
a.archived_status, c3.description as archived_desc
FROM pg_proposal a
LEFT JOIN ref_thesis_type b ON (b.id=a.thesis_type)
LEFT JOIN ref_proposal_status c1 ON (c1.id=a.verified_status)
LEFT JOIN ref_proposal_status c2 ON (c2.id=a.status)
LEFT JOIN ref_proposal_status c3 ON (c3.id=a.archived_status)
LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id)
WHERE d.student_matrix_no = '$matrix_no'
ORDER BY a.id desc
LIMIT $startpoint, $perpage";
$result = $db->query($sql); 

		
?>

	

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Thesis History</title>
		<link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
		<link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
		<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
		<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
		<script src="../../../lib/js/jquery.min2.js"></script>
		<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
		<script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
		<script language="JavaScript" src="../../../lib/js/windowopen.js"></script>
		<script language="JavaScript" type="text/javascript" src="../../../lib/js/tooltip.js"></script>
		<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>
	</head>
	<body>
		<form id="form1" name="form1" method="post" enctype="multipart/form-data">
			<fieldset>
			<legend><strong>SUMMARY LIST</strong></legend>
			<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="100%" class="thetable">
				<tr>
					<th width="24"><strong>No.</strong></th>
					<th width="120"><strong>Thesis/Project ID</strong></th>
					<th width="91"><strong>Proposal ID</strong></th>						
					<th width="263"><strong>Thesis/Project Title</strong></th>
					<th width="130"><strong>Modification Date</strong></th>
					<th width="120"><strong>Faculty Status</strong></th>	
					<th width="120"><strong>Senate Status</strong></th>	
					<th width="120"><strong>Archived Status</strong></th>						
				</tr>
				<?
				$row_cnt = mysql_num_rows($result);
				$db->next_record();
				$no=1;
				if ($row_cnt>0) {
					$i =0;
					$inc = 0;
					$pgThesisId=array();
					$id=array();	
					$modifiedDate=array();
					$thesisTitle=array();
					$confirmDesc=array();	
					$statusDesc=array();						
					$status=array();
					$archivedStatus=array();
					$archivedDesc=array();

					do {						
						$pgThesisId[$i]=$db->f('pg_thesis_id');	
						$id[$i]=$db->f('id');	
						$modifiedDate[$i]=$db->f('theModifiedDate');
						$thesisTitle[$i]=$db->f('thesis_title');
						$confirmDesc[$i]=$db->f('confirm_desc');	
						$statusDesc[$i]=$db->f('status_desc');						
						$status[$i]=$db->f('status');
						$archivedStatus[$i]=$db->f('archived_status');
						$archivedDesc[$i]=$db->f('archived_desc');
						$inc++;
						$i++;
						$no=$no+1;
						}while($db->next_record());
					?>
					<?
					for ($i=0; $i<$inc; $i++) 
					{
						// strip tags to avoid breaking any html
						$thesisTitleString[$i] = strip_tags($thesisTitle[$i]);
						
						if (strlen($thesisTitleString[$i]) > 100) 
						{
							$more[$i] = "<a href=\"#\" value=\".$thesisId[$i].\" title=\"".preg_replace('/"/',"'",$thesisTitle[$i])."\"> ... Read more</a>";
						}
						//$string;
						$thesisTitleCut[$i] = substr($thesisTitleString[$i], 0, 100);
								
						?>
						<tr>
							<td align="center"><?=$i+1?>.</td>
							<td><label><?=$pgThesisId[$i]?></label></td>
							<td><label><a href="../thesis/detail_proposal_history.php?pid=<?=$id[$i];?>"><?=$id[$i]?></a></label></td>						
							<td><label><?=$thesisTitleCut[$i]?></label><?=$more[$i]?></td>
							<td><label><?=$modifiedDate[$i]?></label></td>
							<td><label><?=$confirmDesc[$i]?></label><div align="center"></div></td>
							<td><label><?=$statusDesc[$i]?></label></td>
							<td><label><?=$archivedDesc[$i]?></label></td>
						</tr>
					<?
					
					}

					?>									
			</table>

			<?
			$count_total_result ="SELECT count(*) as total from pg_proposal a, pg_thesis b 
			WHERE a.pg_thesis_id = b.id AND b.student_matrix_no = '$matrix_no'";
			$db->query($count_total_result);
			$db->next_record();
			$a = $db->f('total');
			$db->free();
		
			//This is the actual usage of function, It prints the paging links
			doPages($perpage, 'proposal_history_staff_detail.php', $varParamSend, $a);					
			
			}
			else {
				?>
				<table>
					<tr>
						<td>
							<p>No record found!</p>
						</td>
					</tr>
				</table>
				<?
			}
			?>
		  </fieldset>		
		</form>
	</body>
</html>




