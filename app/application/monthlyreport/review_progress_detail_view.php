<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: review_progress_detail_view.php
//
// Created by: Zuraimi
// Created Date: 19-Mar-2015
// Modified by: Zuraimi
// Modified Date: 19-Mar-2015
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
$referenceNo=$_GET['ref'];

/**
 *
 * @create a roman numeral from a number
 *
 * @param int $num
 *
 * @return string
 *
 */
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

$sql2 = "SELECT a.id, b.id as progress_detail_id, a.reference_no, a.report_month, a.report_year, 
DATE_FORMAT(b.responded_date, '%d-%b-%Y %h:%i %p') as responded_date, 
DATE_FORMAT(a.submit_date, '%d-%b-%Y %h:%i %p') as submit_date, a.student_matrix_no, a.pg_thesis_id, a.pg_proposal_id, 
DATE_FORMAT(a.meeting_stime,'%h:%i') as meeting_stime, DATE_FORMAT(a.meeting_stime,'%p') as stime_pm, 
DATE_FORMAT(a.meeting_etime,'%h:%i') AS meeting_etime, DATE_FORMAT(a.meeting_etime,'%p') as etime_pm,		
DATE_FORMAT(a.meeting_date,'%d-%b-%Y') as meeting_date, b.status as progress_detail_status, b.advice,
a.status as progress_status, c2.description as progress_desc,
a.insert_by, a.insert_date, a.modify_by, a.modify_date,	a.issues as student_issues, b.issues as supervisor_issues, 
c.description as progress_detail_desc
FROM pg_progress a
LEFT JOIN pg_progress_detail b ON (b.pg_progress_id = a.id)
LEFT JOIN ref_proposal_status c ON (c.id = b.status)
LEFT JOIN ref_proposal_status c2 ON (c2.id = a.status)
WHERE a.student_matrix_no = '$studentMatrixNo'
AND a.pg_thesis_id = '$thesisId'
AND a.pg_proposal_id = '$proposalId'
AND a.reference_no = '$referenceNo'
AND b.pg_employee_empid = '$user_id'
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
$submitDate=$dbg->f('submit_date');
$progressDetailStatus1=$dbg->f('progress_detail_status');
$progressDetailDesc1=$dbg->f('progress_detail_desc');
$respondedDate1=$dbg->f('responded_date');
$studentIssues=$dbg->f('student_issues');
$supervisorIssues=$dbg->f('supervisor_issues');
$advice=$dbg->f('advice');
$referenceNo=$dbg->f('reference_no');
$progressDetailId=$dbg->f('progress_detail_id');
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
	
	
	</SCRIPT>
	<table border="0">
				<tr>
				<td><strong>Report Details</strong></td>
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
					<td>Submitted Date</td>
					<td>:</td>
					<td><?=$submitDate?></td>
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
				<input type="hidden" name="sname" id="sname" value="<?=$sname; ?>">
				<tr>
					<td>Thesis / Project ID</td>
					<td>:</td>
					<td><label><?=$thesisId;?></label></td>
				</tr>
			</table>	
			</br>
			<?
			$sqlMeeting="SELECT a.id, DATE_FORMAT(a.meeting_date,'%d-%b-%Y') as meeting_date, DATE_FORMAT(a.meeting_stime,'%h:%i %p') as meeting_stime, 
			DATE_FORMAT(a.meeting_etime,'%h:%i %p') as meeting_etime, b.description as meeting_mode_desc
			FROM  pg_progress_meeting a
			LEFT JOIN ref_meeting_mode b ON (b.id = a.meeting_mode)
			WHERE a.pg_proposal_id='$proposalId'
			AND a.pg_thesis_id = '$thesisId'
			AND a.student_matrix_no = '$studentMatrixNo' 
			ORDER BY a.meeting_date DESC ";			
			$result = $db->query($sqlMeeting); 
			$row_cnt = mysql_num_rows($result);
			?>
			<table>
				<tr>
					<td><input type="button" name="btnBack" value="List of Other Submitted Monthly Progress Report" onclick="javascript:document.location.href='list_other_progress_report.php?ref=<?=$referenceNo?>&theId=<?=$progressId?>&mn=<?=$studentMatrixNo?>&pid=<?=$proposalId;?>&tid=<?=$thesisId;?>';" /></td>
				</tr>
			</table>
			<br/>
			<fieldset>
			<legend><strong>Meeting Details</strong></legend>
				<table width="50%" border="1" cellpadding="3" cellspacing="1" style="border-collapse:collapse;" class="thetable">			
						<tr>
							<th>No</th>					
							<th>Meeting Date</th>
							<th>Meeting Start Time</th>
							<th>Meeting End Time</th>
							<th>Meeting Mode</th>
						</tr>
						
						<?php
					if ($row_cnt > 0) {
						
						$tmp_no = 0;
						while($row = mysql_fetch_array($result)) 									
						{ 
							if($tmp_no % 2) $color ="first-row"; else $color = "second-row";
							?><tr class="<?=$color?>">
									<td align="center"><label><?=$tmp_no+1;?>.</label></td>
									<td align="center"><label><?=$row["meeting_date"];?></label></td>
									<td align="center"><label><?=$row["meeting_stime"];?></label></td>
									<td align="center"><label><?=$row["meeting_etime"];?></label></td>		
									<td align="center"><label><?=$row["meeting_mode_desc"];?></label></td>											
								</tr>
							<?
							$tmp_no++;}
					}
					else {
						?>
						<table>
							<tr>
								<td><label>No record found!</label></td>
							</tr>
						</table>
						<?
					}?> 
						
				</table>
			</fieldset>
			<br/>
			<fieldset>
			<legend><strong>Partner(s)</strong></legend>
			<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="90%" class="thetable">				
				<tr>
					<th width="5%">No</th>					
					<th width="10%" align="left">Role</th>
					<th width="10%" align="left">Staff ID</th>
					<th width="20%" align="left">Name</th>
					<th width="5%" align="left">Faculty</th>
					<th width="10%">View Feedback</th>
					<th width="20%" align="left">Status</th>
					<th width="10%">Last Update</th>
				</tr>
				 <?php				
				
				$sql_supervisor = "SELECT a.id, b.pg_employee_empid, c.ref_supervisor_type_id, d.description as supervisor_desc, 
				DATE_FORMAT(b.responded_date,'%d-%b-%Y %h:%i %p') AS responded_date, e.description as progress_detail_desc,
				f.description as role_status_desc
				FROM pg_progress_detail b 
				LEFT JOIN pg_progress a ON (a.id = b.pg_progress_id)
				LEFT JOIN pg_supervisor c ON (c.pg_employee_empid = b.pg_employee_empid)
				LEFT JOIN ref_supervisor_type d ON (d.id = c.ref_supervisor_type_id)
				LEFT JOIN ref_proposal_status e ON (e.id = b.status)
				LEFT JOIN ref_role_status f ON (f.id = c.role_status)
				WHERE a.id = '$progressId'
				AND a.student_matrix_no = '$studentMatrixNo'
				AND b.pg_employee_empid <> '$user_id'
				AND a.pg_thesis_id = '$thesisId'
				AND a.pg_proposal_id = '$proposalId'
				AND a.reference_no = '$referenceNo'
				AND a.archived_status is null
				AND b.archived_status is null
				AND c.pg_student_matrix_no = '$studentMatrixNo'
				AND c.pg_thesis_id = '$thesisId'
				AND c.ref_supervisor_type_id in ('SV','CS','XS')
				AND c.status = 'A'";

				$result_sql_supervisor = $db_klas2->query($sql_supervisor); 
				
				$row_cnt_supervisor = mysql_num_rows($result_sql_supervisor);
				$db_klas2->next_record();
				$varRecCount=0;	
				if ($row_cnt_supervisor>0) {

					do {
						$employeeId = $db_klas2->f('pg_employee_empid');
						$supervisorTypeId = $db_klas2->f('ref_supervisor_type_id');
						$supervisorDesc = $db_klas2->f('supervisor_desc');
						$respondedDate = $db_klas2->f('responded_date');
						$progressDetailDesc = $db_klas2->f('progress_detail_desc');
					
						$sql_employee="SELECT  b.name, c.id, c.description
							FROM new_employee b 
							LEFT JOIN dept_unit c ON (c.id = b.unit_id) 
							WHERE b.empid= '$employeeId'";
							
						$result_sql_employee = $dbc->query($sql_employee);
						$dbc->next_record();
						
						$employeeName = $dbc->f('name');
						$departmentId = $dbc->f('id');
						$departmentName = $dbc->f('description');
						$varRecCount++;
						
						if($i % 2) $color ="first-row"; else $color = "second-row";

						?>
						<input type="hidden" name="supervisorIdArray[]" id="supervisorIdArray" value="<?=$employeeId; ?>">
						<tr class="<?=$color?>">
							<td align="center"><?=$varRecCount;?>.</td>					
							<?
							if ($supervisorTypeId == 'XS') {
							?>
								<td><label><span style="color:#FF0000"><?=$supervisorDesc?></span></label></td>
							<?}
							else {
								?>
								<td><label><?=$supervisorDesc?></label></td>
								<?
							}?>
							<td align="left"><?=$employeeId;?></td>
							<td align="left"><?=$employeeName;?></td>
							<td align="left"><a href="javascript:void(0);" onMouseOver="toolTip('<?=$departmentName;?>', 300)" onMouseOut="toolTip()"><?=$departmentId;?></a></td>
							
							<?
							$sql12 = "SELECT a.status as progress_status, c.description as progress_desc
							FROM pg_progress_detail a
							LEFT JOIN pg_progress b ON (b.id = a.pg_progress_id)
							LEFT JOIN ref_proposal_status c ON (c.id = a.status)
							WHERE b.student_matrix_no = '$studentMatrixNo'
							AND a.pg_employee_empid = '$employeeId'
							AND b.pg_thesis_id = '$thesisId'
							AND b.pg_proposal_id = '$proposalId'
							AND a.archived_status is null
							AND b.archived_status is null";
							
							$result12 = $dbg->query($sql12); 
							$dbg->next_record();
							$row_cnt12 = mysql_num_rows($result12);
							$progressStatus=$dbg->f('progress_status');
							$progressDesc=$dbg->f('progress_desc');
							
							if ($progressStatus != 'IN1') {
							?>								
								<td align="center"><input type="button" name="btnView" value="View" onClick="javascript:document.location.href='review_progress_detail_staff.php?eid=<?=$employeeId?>&mn=<?=$studentMatrixNo?>&pid=<?=$proposalId;?>&tid=<?=$thesisId;?>&id=<?=$id;?>&ref=<?=$referenceNo;?>';" /></td>								
							<?}
							else {
								?>
								<td></td>
								<?
							}?>
							<td><label><?=$progressDesc?></label></td>
							<td align="center"><label><?=$respondedDate?></label></td>
						</tr>
						<?
						} while($db_klas2->next_record());
					}
					else {
						?>
						<table>				
							<tr><td>No record found! <br/><br/>Possible Reasons:-<br/>
										1. Supervisor/Co-Supervisor is yet to be assigned.<br/>
										2. Pending approval by the Senate.<br/>
										3. If already assigned, it could be the Supervisor/Co-Supervisor pending to accept.<br/></td>
							</tr>
						</table>
						<?
					}?>
					
			</table>
			</fieldset>
			<table>
				<tr>
					<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../monthlyreport/review_progress.php';" /></td>
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
			<br/>
			<fieldset>
			<legend><strong>Content of Discussion</strong></legend>
			<table>
				<?
				for ($i=0; $i<$no; $i++){
				?>
					<?
						$sql11= " SELECT a.discussed_status, b.id as discussion_id
						FROM pg_discussion_detail a
						LEFT JOIN pg_discussion b ON (b.id = a.pg_discussion_id)
						WHERE b.pg_thesis_id = '$thesisId'
						AND b.pg_proposal_id = '$proposalId'
						AND b.student_matrix_no = '$studentMatrixNo'
						AND b.pg_chapter_id = '$chapterId[$i]'
						AND b.pg_subchapter_id = '$subchapterId[$i]'
						AND a.pg_employee_empid = '$user_id'
						AND a.archived_status is null
						AND b.archived_status is null";
											
						$result_sql11 = $dbb->query($sql11); 
						$dbb->next_record();
						$discussedStatus = $dbb->f('discussed_status');
						$discussionId = $dbb->f('discussion_id');
					?>
					<tr>
						<?
						if ($discussedStatus == 'Y') {									
						?>
							<td align="center"><input name="content_checkbox[]" type="checkbox" id="content_checkbox" value="<?=$i;?>" checked="checked" disabled="" /></input></td>
						<?									
						}
						else {									
							?>
							<td align="center"><input name="content_checkbox[]" type="checkbox" id="content_checkbox" value="<?=$i;?>" disabled=""/></input></td>
							<?									
						}?>

						<td><label>Chapter <?=romanNumerals($chapterNo[$i]);?>. <?=$chapterDesc[$i];?></label>			
						<input type="hidden" name="chapterNo[]" id="chapterNo" value="<?=$chapterNo[$i];?>"></input>

						<?if ($subchapterNo[$i] != "") {?>
								<label>, Subchapter <?=romanNumerals($subchapterNo[$i]);?>. <?=$subchapterDesc[$i];?></label>									
							
						<?}?>
						</td>
					</tr>
					<input type="hidden" name="discussionId[]" id="discussionId" value="<?=$discussionId; ?>">
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
					<td><?=$supervisorIssues; ?></td>
				</tr>
				<tr>
					<td></br><strong>Attachment file by Student:</strong></td>
				</tr>
				<tr>
					<td align="left">
				<?php
					$sqlUpload="SELECT * FROM file_upload_progress 
					WHERE progress_id = '$id'
					AND pg_proposal_id='$proposalId' 
					AND attachment_level = 'S' 
					AND attachment_type = 'I'
					AND upload_status = 'CFM'
					AND status = 'A'
					AND archived_status IS NULL";			

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
						?><?
					}
					else {
						?>No attachment found.<br/><?
					}
				?>
					</td>
				</tr>			
			</table>
			</fieldset>
			</br>
			<fieldset>
			<legend><strong>Advice from Supervisor & list of Action to be taken by student</strong></legend>
			<table>
				<tr>
					<td><?=$advice; ?></td>
				</tr>	

				<tr>
					<td><br /><strong>Attachment file by Student:</strong></td>
				</tr>
				<tr>
					<td align="left">
				<?php
					$sqlUpload="SELECT * FROM file_upload_progress 
					WHERE progress_id = '$id'
					AND pg_proposal_id='$proposalId' 
					AND student_matrix_no = '$studentMatrixNo'
					AND attachment_level = 'S' 
					AND attachment_type = 'A'
					AND upload_status = 'CFM'
					AND status = 'A'
					AND archived_status IS NULL";			

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
						?><?
					}
					else {
						?>No attachment found.<?
					}
				?>
					</td>
				</tr>		
				
				<tr>
					<td></br><strong>Attachment file by Supervisor:</strong></td>
				</tr>
				<tr>
					<td align="left">
				<?php
					$sqlUpload="SELECT * FROM file_upload_progress 
					WHERE progress_id = '$id'
					AND pg_proposal_id='$proposalId' 
					AND pg_employee_empid = '$user_id'
					AND attachment_level = 'F' 
					AND attachment_type = 'A'
					AND upload_status = 'CFM'
					AND status = 'A'
					AND archived_status IS NULL";		

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
						?><?
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
					<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../monthlyreport/review_progress.php';" /></td>
				</tr>
			</table>
			</br>

		</form>
</body>
</html>




