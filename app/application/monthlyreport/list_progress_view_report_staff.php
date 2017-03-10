<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: list_progress_view_report_staff.php
//
// Created by: Zuraimi
// Created Date: 20-Mar-2015
// Modified by: Zuraimi
// Modified Date: 20-Mar-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

session_start();
$user_id=$_SESSION['user_id'];
$progressId=$_GET['id'];
$studentMatrixNo=$_GET['mn'];
$thesisId=$_GET['tid'];
$proposalId=$_GET['pid'];
$progressDetailId = $_GET['pdid'];
$referenceNo=$_GET['ref'];
$employeeId = $_GET['eid'];
$theEmployeeId = $_GET['theEid'];

function romanNumerals($num) 
{
    $n = intval($num);
    $res = '';
 
    /*** roman_numerals array  ***/
    $roman_numerals = array(
                'M'  => 1000,
                'CM' => 900,
                'D'  => 500,
                'CD' => 400,
                'C'  => 100,
                'XC' => 90,
                'L'  => 50,
                'XL' => 40,
                'X'  => 10,
                'IX' => 9,
                'V'  => 5,
                'IV' => 4,
                'I'  => 1);
 
    foreach ($roman_numerals as $roman => $number) 
    {
        /*** divide to get  matches ***/
        $matches = intval($n / $number);
 
        /*** assign the roman char * $matches ***/
        $res .= str_repeat($roman, $matches);
 
        /*** substract from the number ***/
        $n = $n % $number;
    }
 
    /*** return the res ***/
    return $res;
}


$sql2 = "SELECT a.id, b.id as progress_detail_id, a.reference_no, a.report_month, a.report_year, DATE_FORMAT(b.responded_date, '%d-%b-%Y') as responded_date, a.student_matrix_no, a.pg_thesis_id, a.pg_proposal_id, 
DATE_FORMAT(a.meeting_stime,'%h:%i') as meeting_stime, DATE_FORMAT(a.meeting_stime,'%p') as stime_pm, 
DATE_FORMAT(a.meeting_etime,'%h:%i') AS meeting_etime, DATE_FORMAT(a.meeting_etime,'%p') as etime_pm,		
DATE_FORMAT(a.meeting_date,'%d-%b-%Y') as meeting_date, b.status as progress_status, b.advice,
a.insert_by, a.insert_date, a.modify_by, a.modify_date,	b.issues as student_issues, b.issues as supervisor_issues, c.description as progress_desc
FROM pg_progress a
LEFT JOIN pg_progress_detail b ON (b.pg_progress_id = a.id)
LEFT JOIN ref_proposal_status c ON (c.id = b.status)
WHERE a.reference_no = '$referenceNo'
AND a.student_matrix_no = '$studentMatrixNo'
AND b.pg_employee_empid = '$theEmployeeId'
AND a.pg_thesis_id = '$thesisId'
AND a.pg_proposal_id = '$proposalId'
AND a.archived_status is null
AND b.archived_status is null";

$result2 = $dbg->query($sql2); 
$dbg->next_record();
$id=$dbg->f('id');
$reportMonth=$dbg->f('report_month');
$reportYear=$dbg->f('report_year');
$meetingDate=$dbg->f('meeting_date');
$startTime=$dbg->f('meeting_stime');
$sTimePM=$dbg->f('stime_pm');
$eTimePM=$dbg->f('etime_pm');
$endTime=$dbg->f('meeting_etime');
$progressStatus=$dbg->f('progress_status');
$progressDesc=$dbg->f('progress_desc');
$respondedDate=$dbg->f('responded_date');
$studentIssues=$dbg->f('student_issues');
$supervisorIssues=$dbg->f('supervisor_issues');
$advice=$dbg->f('advice');
//$referenceNo=$dbg->f('reference_no');
//$progressDetailId=$dbg->f('progress_detail_id');
$row_cnt2 = mysql_num_rows($result2);
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
	
	<SCRIPT LANGUAGE="JavaScript">

	function respConfirm () {
		var confirmSubmit = confirm("Click OK if confirm to submit or CANCEL to return back.");
		if (confirmSubmit==true)
		{
			return saveStatus;
		}
		if (confirmSubmit==false)
		{
			return false;
		}
	}

	</SCRIPT>
		<table border="0">
			<tr>
			<td><strong>Report Details - VIEW</strong></td>
			</tr>
		</table>
		<?
		$curdatetime = date("Y-m-d H:i:s");
		$time=strtotime($curdatetime);
		$month=date("F",$time);
		$year=date("Y",$time);?>
		<table>
			<tr>
				<td>Report Status</td>
				<td>:</td>
				<?if ($progressDesc=="") $progressDesc='New';?>
				<td><strong><?=$progressDesc?></strong></td>
			</tr>
			<tr>
				<td>Report for Month of </td>
				<td>:</td>
				<td><label><?=$reportMonth?> <?=$reportYear?></label></td>			  
			</tr>
			<tr>
				<td>Last Update</td>
				<td>:</td>
				<td><label><?=$respondedDate?></label></td>			  
			</tr>
			<tr>
				<td>Reference No.</td>
				<td>:</td>
				<td><?=$referenceNo?></td>
			</tr>
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
			<tr>
				<td>Thesis / Project ID</td>
				<td>:</td>
				<td><label><?=$thesisId;?></label></td>
			</tr>			
		</table>
		</br>

		<table>
			<tr>
				<td><label><strong>Partner</strong></label></td>
			</tr>
		</table>
		<table>			

				 <?php				
				
				$sql_supervisor = "SELECT b.issues as supervisor_issues, b.advice,
						d.description as supervisor_desc
						FROM pg_progress_detail b 
						LEFT JOIN pg_progress a ON (a.id = b.pg_progress_id)
						LEFT JOIN pg_supervisor c ON (c.pg_employee_empid = b.pg_employee_empid)
						LEFT JOIN ref_supervisor_type d ON (d.id = c.ref_supervisor_type_id)
						WHERE a.id = '$id'
						AND a.reference_no = '$referenceNo'
						AND a.student_matrix_no = '$studentMatrixNo'
						AND b.pg_employee_empid = '$theEmployeeId'
						AND a.pg_thesis_id = '$thesisId'
						AND a.pg_proposal_id = '$proposalId'
						AND c.pg_student_matrix_no = '$studentMatrixNo'
						AND c.pg_thesis_id = '$thesisId'
						AND c.ref_supervisor_type_id in ('SV','CS','XS')";

				$result_sql_supervisor = $db_klas2->query($sql_supervisor); //echo $sql;
				
				//$row_cnt_supervisor = mysql_num_rows($result_sql_supervisor);
				$db_klas2->next_record();
				$varRecCount=0;	
						
						//$theEmployeeId = $db_klas2->f('pg_employee_empid');
						$advice = $db_klas2->f('advice');
						$supervisorIssues = $db_klas2->f('supervisor_issues');
						$supervisorDesc = $db_klas2->f('supervisor_desc');
					
						$sql_employee="SELECT  b.name, c.id, c.description
							FROM new_employee b 
							LEFT JOIN dept_unit c ON (c.id = b.unit_id) 
							WHERE b.empid= '$theEmployeeId'";
							
						$result_sql_employee = $dbc->query($sql_employee);
						$dbc->next_record();
						
						$employeeName = $dbc->f('name');
						$departmentId = $dbc->f('id');
						$departmentName = $dbc->f('description');

						?>
						<input type="hidden" name="supervisorIdArray[]" id="supervisorIdArray" value="<?=$theEmployeeId; ?>">
						<tr>			
							<td><label>Staff ID</label></td>
							<td>:</td>
							<td align="left"><?=$theEmployeeId;?></td>
						</tr>
						<tr>
							<td><label>Staff Name</label></td>
							<td>:</td>
							<td align="left"><?=$employeeName;?></td>
						</tr>
						<tr>
							<td><label>Role</label></td>
							<td>:</td>
							<td align="left"><?=$supervisorDesc;?></td>
						</tr>
						<tr>
							<td><label>Department</label></td>
							<td>:</td>							
							<td align="left"><?=$departmentName;?></td>
						</tr>
					</table>
					</br>
					<?
					$sql2 = "SELECT a.id as chapter_id, a.chapter_no, a.description as chapter_desc, 
							b.id as subchapter_id, b.subchapter_no, b.description as subchapter_desc
							FROM pg_chapter a
							LEFT JOIN pg_subchapter b ON (b.chapter_id = a.id)  
							WHERE a.status = 'A'
							AND a.student_matrix_no = '$studentMatrixNo'
							AND (b.status = 'A' OR b.status IS NULL)
							ORDER BY a.chapter_no, b.subchapter_no";

					$result2 = $dbb->query($sql2); 
					$dbb->next_record();
					$row_cnt2 = mysql_num_rows($result2);
					
					$chapterArray = Array();
					$no=0;
					do {											
						$chapterId[$no]=$dbb->f('chapter_id');	
						$chapterNo[$no]=$dbb->f('chapter_no');
						$chapterDesc[$no]=$dbb->f('chapter_desc');
						$subchapterId[$no]=$dbb->f('subchapter_id');	
						$subchapterNo[$no]=$dbb->f('subchapter_no');
						$subchapterDesc[$no]=$dbb->f('subchapter_desc');
						$no++;
					}while($dbb->next_record());	
					
					?>
					
					<table>
						<tr>
							<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../monthlyreport/list_view_progress_staff.php?eid=<?=$employeeId?>&pid=<?=$proposalId;?>&tid=<?=$thesisId;?>&id=<?=$progressId;?>&mn=<?=$studentMatrixNo;?>&ref=<?=$referenceNo;?>&pdid=<?=$progressDetailId?>';" /></td>
						</tr>
					</table>
					
				<?
	$sql2 = "SELECT a.id as chapter_id, a.chapter_no, a.description as chapter_desc, 
	b.id as subchapter_id, b.subchapter_no, b.description as subchapter_desc
	FROM pg_chapter a
	LEFT JOIN pg_subchapter b ON (b.chapter_id = a.id)  
	WHERE a.status = 'A'
	AND a.student_matrix_no = '$studentMatrixNo'
	AND (b.status = 'A' OR b.status IS NULL)
	ORDER BY a.chapter_no, b.subchapter_no";

	$result2 = $dbb->query($sql2); 
	$dbb->next_record();
	$row_cnt2 = mysql_num_rows($result2);
	
	$chapterArray = Array();
	$no=0;
	do {											
		$chapterId[$no]=$dbb->f('chapter_id');	
		$chapterNo[$no]=$dbb->f('chapter_no');
		$chapterDesc[$no]=$dbb->f('chapter_desc');
		$subchapterId[$no]=$dbb->f('subchapter_id');	
		$subchapterNo[$no]=$dbb->f('subchapter_no');
		$subchapterDesc[$no]=$dbb->f('subchapter_desc');
		$no++;
	}while($dbb->next_record());	
	
	?>
	<fieldset>
		<legend><strong>Content of Discussion</strong></legend>
		<table>
			<?
			for ($i=0; $i<$no; $i++){
			?>
				<input type="hidden" name="chapterId[]" id="chapterId" value="<?=$chapterId[$i];?>">
				<input type="hidden" name="subchapterId[]" id="subchapterId" value="<?=$subchapterId[$i];?>">
				<?
					$sql_discussion = " SELECT discussed_status
					FROM pg_discussion
					WHERE pg_thesis_id = '$thesisId'
					AND pg_proposal_id = '$proposalId'
					AND student_matrix_no = '$studentMatrixNo'
					AND pg_chapter_id = '$chapterId[$i]'
					AND pg_subchapter_id = '$subchapterId[$i]'
					AND archived_status is null";
										
					$result_sql_discussion = $dbb->query($sql_discussion); 
					$dbb->next_record();
					$discussedStatus = $dbb->f('discussed_status');
				?>
				<tr>
					<?
					if ($discussedStatus == 'Y') {									
					?>
						<td align="center"><input name="content_checkbox[]" type="checkbox" id="content_checkbox" value="<?=$i;?>" checked="checked" disabled=""/></input></td>
					<?									
					}
					else {									
						?>
						<td align="center"><input name="content_checkbox[]" type="checkbox" id="content_checkbox" value="<?=$i;?>" disabled="" /></input></td>
						<?									
					}?>

					<td><label>Chapter <?=romanNumerals($chapterNo[$i]);?>. <?=$chapterDesc[$i];?></label>			
					<input type="hidden" name="chapterNo[]" id="chapterNo" value="<?=$chapterNo[$i];?>"></input>

					<?if ($subchapterNo[$i] != "") {?>
							<label>, Subchapter <?=romanNumerals($subchapterNo[$i]);?>. <?=$subchapterDesc[$i];?></label>									
						
					<?}?>
					</td>
				</tr>
			<?
			}
			?>	
			<input type="hidden" name="totalNoOfContent" id="totalNoOfContent" value="<?=$i; ?>">
			
		</table>
		</fieldset>
		<br/>
		<fieldset>
		<legend><strong>Description of topic or Issues facing by Student</strong></legend>
		<table>
			<tr>
				<td><?=$studentIssues; ?></br><!--<textarea name="studentIssues" class="ckeditor" ></textarea>--></td>
			</tr>
			<tr>
				<td><strong>Attachment:</strong></td>
			</tr>
			<tr>
			<td align="left">
			<?php
			$sql3="SELECT a.archived_status
			FROM pg_progress a
			LEFT JOIN ref_proposal_status c1 ON (c1.id = a.status)
			WHERE a.id = '$progressId'
			AND a.reference_no = '$referenceNo'
			AND a.student_matrix_no = '$studentMatrixNo'
			AND a.pg_thesis_id = '$thesisId'
			AND a.pg_proposal_id = '$proposalId'";
			
			$result3 = $dba->query($sql3); 
			$dba->next_record();
			$archivedStatus=$dba->f('archived_status');
			if($archivedStatus == '')
			{

				$sqlUpload="SELECT * FROM file_upload_progress 
				WHERE pg_proposal_id='$proposalId' 
				AND student_matrix_no = '$studentMatrixNo'
				AND reference_no = '$referenceNo'
				AND progress_id = '$progressId'
				AND attachment_level = 'S' 
				AND attachment_type = 'I'
				AND status = 'A'
				AND archived_status is null";			
			}
			else
			{
				$sqlUpload="SELECT * FROM file_upload_progress 
				WHERE pg_proposal_id='$proposalId' 
				AND student_matrix_no = '$studentMatrixNo'
				AND reference_no = '$referenceNo'
				AND progress_id = '$progressId'
				AND attachment_level = 'S' 
				AND attachment_type = 'I'
				AND status = 'A'
				AND archived_status = 'ARC'";			
			
			}
				$result = $db_klas2->query($sqlUpload); //echo $sql;
				$row_cnt = mysql_num_rows($result);
				$attachmentNo2=1;
				if ($row_cnt>0)
				{
					?><?
					while($row = mysql_fetch_array($result)) 					
					{ 
						?>
							<a href="progress_download.php?fc=<?=$row["fu_cd"];?>&al=S&at=I" title="File Description: <?=$row["fu_document_filedesc"];?>">Attachment <?=$attachmentNo2++;?>: <img src="../images/download.png" width="20" height="19" style="border:0px;" title="Download <?=$row["fu_document_filename"];?>"></a>						
					<?}						
				}
				else {
					?>No attachment found.<br/><?
				}
			?>
				</td>
			</tr>			
		</table>
		</fieldset>
		<br/>
		<fieldset>
			<legend><strong>Advice from Supervisor & list of Action to be taken by student</strong></legend>
			<table>
				<tr>
					<td><?=$advice; ?><br /><!--<textarea name="advice" class="ckeditor" ></textarea>--></td>
				</tr>
						<tr>
					<td><strong>Attachment by Student:</strong></td>
				</tr>
				<tr>
				<td align="left">
				<?php
				if($archivedStatus == '')
				{
					//"archived status : ".$archivedStatus;
					$sqlUpload="SELECT * FROM file_upload_progress 
					WHERE pg_proposal_id='$proposalId'
					AND student_matrix_no = '$studentMatrixNo'
					AND reference_no = '$referenceNo'
					AND progress_id = '$progressId'			
					AND attachment_level = 'S' 
					AND attachment_type = 'A'
					AND status = 'A'
					AND archived_status is null";			
				}
				else
				{
					//"archived status : ".$archivedStatus;
					$sqlUpload="SELECT * FROM file_upload_progress 
					WHERE pg_proposal_id='$proposalId'
					AND student_matrix_no = '$studentMatrixNo'
					AND reference_no = '$referenceNo'
					AND progress_id = '$progressId'			
					AND attachment_level = 'S' 
					AND attachment_type = 'A'
					AND status = 'A'
					AND archived_status = 'ARC'";			

				
				}
					$result = $db_klas2->query($sqlUpload); //echo $sql;
					$row_cnt = mysql_num_rows($result);
					$attachmentNo2=1;
					if ($row_cnt>0)
					{
						?><?
						while($row = mysql_fetch_array($result)) 					
						{ 
							?>
								<a href="progress_download.php?fc=<?=$row["fu_cd"];?>&al=S&at=A" title="File Description: <?=$row["fu_document_filedesc"];?>">Attachment <?=$attachmentNo2++;?>: <img src="../images/download.png" width="20" height="19" style="border:0px;" title="Download <?=$row["fu_document_filename"];?>"></a>						
						<?}						
					}
					else {
						?>No attachment found.<br/><br /><?
					}
				?>
				</td>		
				</tr>				
				
				<tr>
					<td><strong>Attachment by Supervisor:</strong></td>
				</tr>
				<tr>
					<td align="left">
				<?php
				if($archivedStatus == '')
				{
					//"archived status : ".$archivedStatus;
					$sqlUpload="SELECT * FROM file_upload_progress 
					WHERE pg_proposal_id='$proposalId'
					AND reference_no = '$referenceNo'
					AND pg_employee_empid = '$theEmployeeId'
					AND progress_id = '$progressId'			
					AND attachment_level = 'F' 
					AND attachment_type = 'A'
					AND status = 'A'
					AND archived_status is null";			
				}
				else
				{
					//"archived status : ".$archivedStatus;
					$sqlUpload="SELECT * FROM file_upload_progress 
					WHERE pg_proposal_id='$proposalId'
					AND reference_no = '$referenceNo'
					AND pg_employee_empid = '$theEmployeeId'
					AND progress_id = '$progressId'			
					AND attachment_level = 'F' 
					AND attachment_type = 'A'
					AND status = 'A'
					AND archived_status = 'ARC'";			

				
				}
					$result = $db_klas2->query($sqlUpload); //echo $sql;
					$row_cnt = mysql_num_rows($result);
					$attachmentNo2=1;
					if ($row_cnt>0)
					{
						?><?
						while($row = mysql_fetch_array($result)) 					
						{ 
							?>
								<a href="progress_download.php?fc=<?=$row["fu_cd"];?>&al=F&at=A" title="File Description: <?=$row["fu_document_filedesc"];?>">Attachment <?=$attachmentNo2++;?>: <img src="../images/download.png" width="20" height="19" style="border:0px;" title="Download <?=$row["fu_document_filename"];?>"></a>						
						<?}						
					}
					else {
						?>No attachment found.<br/><?
					}
				?>
					</td>		
				</tr>				
			</table>
		</fieldset>
		<table>
			<tr>
				<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../monthlyreport/list_view_progress_staff.php?eid=<?=$employeeId?>&pid=<?=$proposalId;?>&tid=<?=$thesisId;?>&id=<?=$progressId;?>&mn=<?=$studentMatrixNo;?>&ref=<?=$referenceNo;?>&pdid=<?=$progressDetailId?>';" /></td>
			</tr>
		</table>
		</br>
		</form>
</body>
</html>




