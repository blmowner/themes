<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: view_progress_faculty.php
//
// Created by: Zuraimi
// Created Date: 30-Mar-2015
// Modified by: Zuraimi
// Modified Date: 30-Mar-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

session_start();
$user_id=$_SESSION['user_id'];


if(isset($_POST['btnSearch']) && ($_POST['btnSearch'] <> "")) {
	
	$searchThesisId = $_POST['searchThesisId'];
	$searchStudent = $_POST['searchStudent'];
	$searchStudentName = $_POST['searchStudentName'];
	
	if ($searchThesisId!="") 
	{
		$tmpSearchThesisId = " AND pp.pg_thesis_id = '$searchThesisId'";
	}
	else 
	{
		$tmpSearchThesisId="";
	}
	if ($searchStudent!="") 
	{
		$tmpSearchStudent = " AND pt.student_matrix_no = '$searchStudent'";
	}
	else 
	{
		$tmpSearchStudent="";
	}
	
	$sql1 = "SELECT DISTINCT pt.id AS thesis_id, pt.student_matrix_no, pp.id AS proposal_id
				FROM pg_thesis pt 
				LEFT JOIN pg_proposal pp ON (pp.pg_thesis_id=pt.id) 
				LEFT JOIN ref_proposal_status rps ON (rps.id = pp.verified_status) 
				LEFT JOIN ref_proposal_status rps2 ON (rps2.id = pp.status) 
				LEFT JOIN pg_progress a ON (a.pg_thesis_id = pt.id)
				LEFT JOIN pg_progress_detail b ON (b.pg_progress_id = a.id)
				LEFT JOIN ref_proposal_status c ON (c.id = a.status)
				WHERE pp.verified_status in ('APP','AWC')"
				.$tmpSearchThesisId." "
				.$tmpSearchStudent." "."
				AND pp.archived_status is null
				AND pt.ref_thesis_status_id_proposal in ('APP','AWC','APC')
				AND a.archived_status is null
				ORDER BY pt.id";

		$result1 = $dba->query($sql1); 
		$dba->next_record();
		$row_cnt1 = mysql_num_rows($result1);
		
		$studentMatrixNoArray = Array();
		$thesisIdArray = Array();
		$proposalIdArray = Array();
		
		$no1=0;
		$no2=0;
		if ($row_cnt1 > 0) {
			do {
				$studentMatrixNoArray[$no1] = $dba->f('student_matrix_no');
				$thesisIdArray[$no1] = $dba->f('thesis_id');
				$proposalIdArray[$no1] = $dba->f('proposal_id');
				$no1++;
			} while ($dba->next_record());
			
			$studentNameArray = Array();
			for ($i=0; $i<$no1; $i++){
				$sql9 = "SELECT name
				FROM student
				WHERE matrix_no = '$studentMatrixNoArray[$i]'
				AND name like '%$searchStudentName%'";
				if (substr($studentMatrixNoArray[$i],0,2) != '07') { 
					$dbConnStudent= $dbc; 
				} 
				else { 
					$dbConnStudent=$dbc1; 
				}
				$result9 = $dbConnStudent->query($sql9); 
				$dbConnStudent->next_record();
				if (mysql_num_rows($result9)>0) {
					$studentNameArray[$no2] = $dbConnStudent->f('name');
					$studentMatrixNoArray[$no2] = $studentMatrixNoArray[$i];
					$thesisIdArray[$no2] = $thesisIdArray[$i];
					$proposalIdArray[$no2] = $proposalIdArray[$i];
					$no2++;
				}
			}
		}
		$row_cnt = $no2;
}
else 
{
	$sql1 = "SELECT DISTINCT pt.id AS thesis_id, pt.student_matrix_no, pp.id AS proposal_id 
					FROM pg_thesis pt 
					LEFT JOIN pg_proposal pp ON (pp.pg_thesis_id = pt.id) 
					LEFT JOIN pg_progress a ON (a.pg_thesis_id = pt.id) 
					LEFT JOIN pg_progress_detail b ON (b.pg_progress_id = a.id) 
					WHERE pp.verified_status IN ('APP','AWC') 
					AND pt.ref_thesis_status_id_proposal IN ('APP','AWC','APC') 
					AND pp.archived_status IS NULL 
					AND a.archived_status IS NULL 
					ORDER BY pt.id";

		$result1 = $dba->query($sql1); 
		$dba->next_record();
		
		$studentMatrixNoArray = Array();
		$thesisIdArray = Array();
		$proposalIdArray = Array();
		
		$no1=0;
		$no2=0;
		
		do {
			$studentMatrixNoArray[$no1] = $dba->f('student_matrix_no');
			$thesisIdArray[$no1] = $dba->f('thesis_id');
			$proposalIdArray[$no1] = $dba->f('proposal_id');
			$no1++;
		} while ($dba->next_record());
		
		$studentNameArray = Array();
		for ($i=0; $i<$no1; $i++){
			$sql9 = "SELECT name
			FROM student
			WHERE matrix_no = '$studentMatrixNoArray[$i]'
			AND name like '%$searchStudentName%'";
			if (substr($studentMatrixNoArray[$i],0,2) != '07') { 
				$dbConnStudent= $dbc; 
			} 
			else { 
				$dbConnStudent=$dbc1; 
			}
			$result9 = $dbConnStudent->query($sql9); 
			$dbConnStudent->next_record();
			if (mysql_num_rows($result9)>0) {
				$studentNameArray[$no2] = $dbConnStudent->f('name');
				$studentMatrixNoArray[$no2] = $studentMatrixNoArray[$i];
				$thesisIdArray[$no2] = $thesisIdArray[$i];
				$proposalIdArray[$no2] = $proposalIdArray[$i];
				$no2++;
			}
		}
		$row_cnt = $no2;
}

if(isset($_POST['btnSearchByName']) && ($_POST['btnSearchByName'] <> "")) {
	
	$searchStudentName = $_POST['searchStudentName'];
	
	$sql1 = "SELECT DISTINCT pt.id AS thesis_id, pt.student_matrix_no, pp.id AS proposal_id
			FROM pg_thesis pt 
			LEFT JOIN pg_proposal pp ON (pp.pg_thesis_id=pt.id) 
			LEFT JOIN ref_proposal_status rps ON (rps.id = pp.verified_status) 
			LEFT JOIN ref_proposal_status rps2 ON (rps2.id = pp.status) 
			LEFT JOIN pg_progress a ON (a.pg_thesis_id = pt.id)
			LEFT JOIN pg_progress_detail b ON (b.pg_progress_id = a.id)
			LEFT JOIN ref_proposal_status c ON (c.id = a.status)
			WHERE pp.verified_status in ('APP','AWC')
			AND pp.archived_status is null
			AND pt.ref_thesis_status_id_proposal in ('APP','AWC','APC')
			AND a.archived_status is null
			ORDER BY pt.id";

	$result1 = $dba->query($sql1); 
	$dba->next_record();
	
	$studentMatrixNoArray = Array();
	$thesisIdArray = Array();
	$proposalIdArray = Array();
	
	$no1=0;
	$no2=0;
	
	do {
		$studentMatrixNoArray[$no1] = $dba->f('student_matrix_no');
		$thesisIdArray[$no1] = $dba->f('thesis_id');
		$proposalIdArray[$no1] = $dba->f('proposal_id');
		$no1++;
	} while ($dba->next_record());
	
	$studentNameArray = Array();
	for ($i=0; $i<$no1; $i++){
		$sql9 = "SELECT name
				FROM student
				WHERE matrix_no = '$studentMatrixNoArray[$i]'
				AND name like '%$searchStudentName%'";
		if (substr($studentMatrixNoArray[$i],0,2) != '07') { 
			$dbConnStudent= $dbc; 
		} 
		else { 
			$dbConnStudent=$dbc1; 
		}
		$result9 = $dbConnStudent->query($sql9); 
		$dbConnStudent->next_record();
		if (mysql_num_rows($result9)>0) {
			$studentNameArray[$no2] = $dbConnStudent->f('name');
			$studentMatrixNoArray[$no2] = $studentMatrixNoArray[$i];
			$thesisIdArray[$no2] = $thesisIdArray[$i];
			$proposalIdArray[$no2] = $proposalIdArray[$i];
			$no2++;
		}
	}
		
		
}

?>

	

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Untitled Document</title>
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
		<script language="JavaScript" type="text/javascript" src="../../../lib/js/tooltip.js"></script>		
	</head>
	<body>		
	<form id="form1" name="form1" method="post" enctype="multipart/form-data">	
		<fieldset>
		<legend><strong>List of Student</strong></legend>
			<?
			$curdatetime = date("Y-m-d H:i:s");
			$time=strtotime($curdatetime);
			$month=date("F",$time);
			$year=date("Y",$time);?>
			<table>
				<tr>							
					<td>Please enter searching criteria below:</td>
				</tr>
			</table>
			<table>
				<tr>
					<?$searchRequestDate = date("d-M-Y");?>
					<td>Thesis / Project ID</td>
					<td>:</td>
					<td><input type="text" name="searchThesisId" size="15" id="searchThesisId" value="<?=$searchThesisId;?>"/></td>
				</tr>
				<tr>
					<td>Matrix No</td>
					<td>:</td>
					<td><input type="text" name="searchStudent" size="15" id="searchStudent" value="<?=$searchStudent;?>"/></td>					
				</tr>
				<tr>
					<td>Student Name</td>
					<td>:</td>
					<td><input type="text" name="searchStudentName" size="30" id="searchStudentName" value="<?=$searchStudentName;?>"/></td>
					<td><input type="submit" name="btnSearch" value="Search" /><span style="color:#FF0000"> Note:</span> If no entry is provided, it will search all.</td>
				</tr>
			</table>
			</br>
			
			<table>
				<tr>							
					<td>Searching Results:- <?=$row_cnt ?> record(s) found.</td>
				</tr>
			</table>
			<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="85%" class="thetable">			
				<tr>
					<th width="5%">No</th>					
					<th width="10%" align="left">Student Matrix No.</th>
					<th width="20%" align="left">Student Name</th>
					<th width="10%" align="left">Thesis / Project ID</th>
					<th width="15%" align="left">Report for</th>						
					<th width="20%" align="left">Status</th>	
					<th width="5%">Action</th>
				</tr>
				<?if ($row_cnt > 0 ) {?>	
				<?
				$no=0;
				
					for ($i=0; $i<$no2; $i++){?>
						<tr>
							<td align="center"><?=$no+1;?>.</td>
							
							<td><label><?=$studentMatrixNoArray[$i]?></label></td>
							<td><label><?=$studentNameArray[$i]?></label></td>
							<td><label><?=$thesisIdArray[$i]?></label></td>
							
							<?
							$sql2 = "SELECT distinct a.id, a.report_month, a.report_year, DATE_FORMAT(a.submit_date, '%d-%b-%Y') as submit_date, 
							a.reference_no, a.status as progress_status, c1.description as progress_desc, b.status as progress_detail_status, c2.description as progress_detail_desc
							FROM pg_progress a
							LEFT JOIN pg_progress_detail b ON (b.pg_progress_id = a.id)
							LEFT JOIN ref_proposal_status c1 ON (c1.id = a.status)
							LEFT JOIN ref_proposal_status c2 ON (c2.id = a.status)
							WHERE a.student_matrix_no = '$studentMatrixNoArray[$i]'
							AND a.pg_thesis_id = '$thesisIdArray[$i]'
							AND a.pg_proposal_id = '$proposalIdArray[$i]'
							AND a.archived_status is null";
							
							$result2 = $dbg->query($sql2); 
							$dbg->next_record();
							$id=$dbg->f('id');
							
							
							$progressDesc=$dbg->f('progress_desc');
							$progressDetailDesc=$dbg->f('progress_detail_desc');
							$submitDate=$dbg->f('submit_date');
							$referenceNo=$dbg->f('reference_no'); 
							$progressStatus=$dbg->f('progress_status');
							$reportMonth=$dbg->f('report_month');
							$reportYear=$dbg->f('report_year');
							
							$row_cnt2 = mysql_num_rows($result2);
							?>
							
							<?if ($reportMonth != "") {?>						
								<td><label><?=$reportMonth?> <?=$reportYear?></br><strong>Ref. No:</strong> <?=$referenceNo?></label></td>
							<?}
							else {
								?>
								<td></td>
								<?
							}?>
							
							<?if ($progressDetailDesc != "") {?>
								<td><label><?=$progressDetailDesc?></br> on <?=$submitDate?></label></td>	
							<?}
							else {
								?>
								<td><label>Pending submission</label></td>
								<?
							}?>
							
							<?if ($progressStatus != "") {
								if ($supervisorTypeId != 'XS') {?>										
									<td align="center"><input type="button" name="btnReview" value="View" onClick="javascript:document.location.href='../monthlyreport/review_progress_detail_faculty.php?id=<?=$id?>&mn=<?=$studentMatrixNoArray[$i]?>&tid=<?=$thesisIdArray[$i]?>&pid=<?=$proposalIdArray[$i]?>';" /></td>
								<?}
								else {?>
									<td align="center"><label>Please check Staff Role status.</label><br/>
									<input type="button" name="btnReview" value="View" onClick="javascript:document.location.href='../monthlyreport/review_progress_detail_faculty.php?id=<?=$id?>&mn=<?=$studentMatrixNoArray[$i]?>&tid=<?=$thesisIdArray[$i]?>&pid=<?=$proposalIdArray[$i]?>';" /></td>
									<?
								}
							}
							else {
								?>
								<td><label></label></td>
								<?
							}?>
								
						</tr>
						<?
					$no++;	

				}?>
		</table>
		</fieldset>		
	<?}
	else
	{
		?>
		<table>
			<tr>
				<td>No record found!</td>
			</tr>
		</table>	
		<?
	}		
	?>	
	</form>	
</body>
</html>




