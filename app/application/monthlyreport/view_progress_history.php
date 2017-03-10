<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: view_progress.php
//
// Created by: Zuraimi
// Created Date: 26-Mar-2015
// Modified by: Zuraimi
// Modified Date: 26-Mar-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

session_start();
$user_id = $_SESSION['user_id'];
$thesisId = $_GET['tid'];
$proposalId = $_GET['pid'];
$progressId = $_GET['id'];
$progressDetailId = $_GET['pdid'];
$empid = $_GET['empid'];

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

$sql2 = "SELECT a.id,a.archived_status, a.reference_no, a.report_month, a.report_year, DATE_FORMAT(a.submit_date, '%d-%b-%Y') as submit_date, a.student_matrix_no, 
		a.pg_thesis_id, a.pg_proposal_id, 
		DATE_FORMAT(a.meeting_stime,'%h:%i') as meeting_stime, DATE_FORMAT(a.meeting_stime,'%p') as stime_pm, 
		DATE_FORMAT(a.meeting_etime,'%h:%i') as meeting_etime, DATE_FORMAT(a.meeting_etime,'%p') as etime_pm,		
		DATE_FORMAT(a.meeting_date,'%d-%b-%Y') as meeting_date, a.status as progress_status, 
		a.insert_by, a.insert_date, a.modify_by, a.modify_date,	a.issues as student_issues, a.advice, c1.description as progress_desc
		FROM pg_progress a
		LEFT JOIN ref_proposal_status c1 ON (c1.id = a.status)
		WHERE a.id = '$progressId'
		AND a.student_matrix_no = '$user_id'
		AND a.pg_thesis_id = '$thesisId'
		AND a.pg_proposal_id = '$proposalId'";
		
$result2 = $dba->query($sql2); 
$dba->next_record();
$id=$dba->f('id');
$reportMonth=$dba->f('report_month');
$reportYear=$dba->f('report_year');
$meetingDate=$dba->f('meeting_date');
$startTime=$dba->f('meeting_stime');
$endTime=$dba->f('meeting_etime');
$sTimePM=$dba->f('stime_pm');
$eTimePM=$dba->f('etime_pm');
$progressStatus=$dba->f('progress_status');
$progressDesc=$dba->f('progress_desc');
//$progressDetailId=$dba->f('progress_detail_id');
//$progressDetailStatus=$dba->f('progress_detail_status');
//$progressDetailDesc=$dba->f('progress_detail_desc');
$submitDate=$dba->f('submit_date');
$studentIssues=$dba->f('student_issues');
//$supervisorIssues=$dba->f('supervisor_issues');
$advice=$dba->f('advice');
$referenceNo=$dba->f('reference_no');
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
			<td><?=$user_id?></td>
		</tr>
			<?
			$sql1 = "SELECT name AS student_name
			FROM student
			WHERE matrix_no = '$user_id'";
			if (substr($user_id,0,2) != '07') { 
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
			<td>Thesis ID</td>
			<td>:</td>
			<td><label><?=$thesisId;?></label></td>
		</tr>
	</table>			
	<tr>
	<br/>
	<?
	$sqlMeeting="SELECT id, DATE_FORMAT(meeting_date,'%d-%b-%Y') as meeting_date, DATE_FORMAT(meeting_stime,'%h:%i %p') as meeting_stime, 
	DATE_FORMAT(meeting_etime,'%h:%i %p') as meeting_etime
	FROM  pg_progress_meeting  
	WHERE pg_progress_id = '$progressId'
	AND pg_proposal_id='$proposalId'
	AND pg_thesis_id = '$thesisId'
	AND student_matrix_no = '$user_id' 
	ORDER BY meeting_date DESC ";			
	$result = $db->query($sqlMeeting); 
	$row_cnt = mysql_num_rows($result);
	?>
	<fieldset>
	<legend><strong>Meeting Details</strong></legend>
		<table width="50%" border="1" cellpadding="3" cellspacing="1" style="border-collapse:collapse;" class="thetable">			
				<tr>
					<th>No</th>					
					<th>Meeting Date</th>
					<th>Meeting Start Time</th>
					<th>Meeting End Time</th>
				</tr>
				
				<?php
			if ($row_cnt > 0) {
				
				$tmp_no = 0;
				while($row = mysql_fetch_array($result)) 									
				{ 
					?><tr>
							<td align="center"><label><?=$tmp_no+1;?>.</label></td>
							<td align="center"><label><?=$row["meeting_date"];?></label></td>
							<td align="center"><label><?=$row["meeting_stime"];?></label></td>
							<td align="center"><label><?=$row["meeting_etime"];?></label></td>														
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
	<br />	
	
	<fieldset>
	<legend><strong>Supervisor List</strong></legend>
	<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="100%" class="thetable">			
			<tr>
				<th>No</th>					
				<th>Role</th>
				<th>Staff ID</th>
				<th>Name</th>
				<th>Faculty</th>
				<th>View Report</th>
				<th>Status</th>
				<th>Feedback Date</th>
			</tr>
			 <?php				
			
			$sql_supervisor = " SELECT a.pg_employee_empid, d.description as supervisor_type, h.description as status_desc,
			DATE_FORMAT(e.responded_date,'%d-%b-%Y') AS responded_date
			FROM pg_progress_detail e 
			LEFT JOIN pg_progress b ON (b.id = e.pg_progress_id)
			LEFT JOIN ref_proposal_status h ON (h.id = e.status)
			LEFT JOIN pg_supervisor a ON (a.pg_employee_empid = e.pg_employee_empid)
			LEFT JOIN ref_supervisor_type d ON (d.id = a.ref_supervisor_type_id) 
			LEFT JOIN pg_thesis f ON (f.student_matrix_no = a.pg_student_matrix_no)
			LEFT JOIN pg_proposal g ON (g.pg_thesis_id = f.id)
			WHERE a.pg_student_matrix_no='$user_id'
			AND a.acceptance_status is not null
			AND a.ref_supervisor_type_id in ('SV','CS','XS')
			AND g.verified_status in ('APP','AWC')
			AND g.status in ('APP','APC')
			AND g.archived_status IS NULL
			AND e.archived_status IS NULL
			AND e.id = '$progressDetailId'
			ORDER BY d.seq, a.ref_supervisor_type_id";
			
			$result_sql_supervisor = $db_klas2->query($sql_supervisor); //echo $sql;
			$db_klas2->next_record();
			
			$row_cnt_supervisor = mysql_num_rows($result_sql_supervisor);

			$varRecCount=0;	
			if ($row_cnt_supervisor>0) {
				do {
				
				
					$employeeId = $db_klas2->f('pg_employee_empid');
					$status_desc = $db_klas2->f('status_desc');
					$respondedDate = $db_klas2->f('responded_date');
					$supervisorType = $db_klas2->f('supervisor_type');
				
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

					?>
					<tr>
						<td align="center"><?=$varRecCount;?>.</td>					
						<td align="left"><?=$supervisorType;?></td>
						<td align="left"><?=$employeeId;?></td>
						<td align="left"><?=$employeeName;?></td>
						<td align="left"><a href="javascript:void(0);" onMouseOver="toolTip('<?=$departmentName;?>', 300)" onMouseOut="toolTip()"><?=$departmentId;?></a></td>
						<td><a href="progress_view_report_history.php?eid=<?=$employeeId?>&pid=<?=$proposalId;?>&tid=<?=$thesisId;?>&pdid=<?=$progressDetailId;?>" title="Description of topic or Issues facing by Student - Read more..."><img src="../images/view.jpg" width="45" height="30" style="border:0px;" title="View feedback"></a>
								</td>	
						<td align="left"><?=$status_desc;?></td>
						<td align="left"><?=$respondedDate;?></td>
							
			<? 	} while($db_klas2->next_record());
				
			}
			else {
				?>
				<table>				
					<tr><td>No record found!</tr>
				</table>
				<br/>
				<table>				
					<tr><td>Possible reasons:-<br/>									
							1. Supervisor/Co-Supervisor is yet to be assigned OR<br/>
							2. Supervisor/Co-Supervisor is pending for Senate approval OR<br/>
							3. Supervisor/Co-Supervisor is pending to accept the invitation.<br/></td>
					</tr>
				</table>
				<?
			}?>	
			</table>
			
	</fieldset>
	<table>
		<tr>
			<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../monthlyreport/progress_history.php';" /></td>
		</tr>
	</table>			
	<?
	$sql2 = "SELECT a.id as chapter_id, a.chapter_no, a.description as chapter_desc, 
			b.id as subchapter_id, b.subchapter_no, b.description as subchapter_desc
			FROM pg_chapter a
			LEFT JOIN pg_subchapter b ON (b.chapter_id = a.id)  
			WHERE a.status = 'A'
			AND a.student_matrix_no = '$user_id'
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
									AND student_matrix_no = '$user_id'
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
					<td align="center"><input name="content_checkbox[]" type="checkbox" id="content_checkbox" value="<?=$i;?>" checked="checked" /></input></td>
				<?									
				}
				else {									
					?>
					<td align="center"><input name="content_checkbox[]" type="checkbox" id="content_checkbox" value="<?=$i;?>" /></input></td>
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
			<td><?=$studentIssues; ?><!--<textarea name="studentIssues" class="ckeditor" ></textarea>--></td>
		</tr>
			
		<tr>
		<td align="left">
		<br /><strong>Attachment:</strong>
		<?php
		$sql3="SELECT a.archived_status
		FROM pg_progress a
		LEFT JOIN ref_proposal_status c1 ON (c1.id = a.status)
		WHERE a.id = '$progressId'
		AND a.student_matrix_no = '$user_id'
		AND a.pg_thesis_id = '$thesisId'
		AND a.pg_proposal_id = '$proposalId'";
		
		$result3 = $dba->query($sql3); 
		$dba->next_record();
		$archivedStatus=$dba->f('archived_status');
		if($archivedStatus == '')
		{
			$sqlUpload="SELECT * 
			FROM file_upload_progress 
			WHERE pg_proposal_id='$proposalId' 
			AND student_matrix_no = '$user_id'
			AND progress_id = '$progressId'
			AND attachment_level = 'S' 
			AND attachment_type = 'I'
			AND status = 'A'
			AND archived_status IS NULL";			
		}
		else
		{
			$sqlUpload="SELECT * 
			FROM file_upload_progress 
			WHERE pg_proposal_id='$proposalId' 
			AND student_matrix_no = '$user_id'
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
						
				<?}?><?
			}
			else {
				?><br/>No attachment found.<br/><?
			}
		?>
			</td>
		</tr>			
	</table>
	</fieldset>
	
	<br/>
	<fieldset>
	<legend><strong>Advice from Supervisor & List of Action to be taken by student</strong></legend>
	<table>
      <tr>
        <td><?=$advice;?></td>
      </tr>            <!--<textarea name="advice" class="ckeditor" ></textarea>-->
      <tr>
        <td><br /><strong>Attachment by Student:</strong></td>
		
      </tr>
      <tr>
	  	<td align="left">
        <?php
		if($archivedStatus == '')
		{
			$sqlUpload="SELECT * FROM file_upload_progress 
			WHERE pg_proposal_id='$proposalId'
			AND progress_id = '$progressId'
			AND student_matrix_no = '$user_id'
			AND attachment_level = 'S' 
			AND attachment_type = 'A'
			AND status = 'A'
			AND archived_status IS NULL";			

		}
		else
		{
			$sqlUpload="SELECT * FROM file_upload_progress 
			WHERE pg_proposal_id='$proposalId'
			AND progress_id = '$progressId'
			AND student_matrix_no = '$user_id'
			AND attachment_level = 'S' 
			AND attachment_type = 'A'
			AND status = 'A'";			
		}
			$result = $db_klas2->query($sqlUpload); //echo $sql;
			$row_cnt = mysql_num_rows($result);
			$attachmentNo2=1;
			if ($row_cnt>0)
			{
				?>
        <?
				while($row = mysql_fetch_array($result)) 					
				{ 
					?>
            <a href="progress_download.php?fc=<?=$row["fu_cd"];?>&al=S&at=A" title="File Description: <?=$row["fu_document_filedesc"];?>">Attachment
              <?=$attachmentNo2++;?>
              : <img src="../images/download.png" width="20" height="19" style="border:0px;" title="Download <?=$row["fu_document_filename"];?>" /></a>
            <? }
				?>
        <?
			}
			else 
			{
				
				echo " No attachment found.<br/>";
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
			AND pg_employee_empid = '$empid'
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
			AND pg_employee_empid = '$empid'
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
				?>
        <?
				while($row = mysql_fetch_array($result)) 					
				{ 
					?>
            <a href="progress_download.php?fc=<?=$row["fu_cd"];?>&al=F&at=A" title="File Description: <?=$row["fu_document_filedesc"];?>">Attachment
              <?=$attachmentNo2++;?>
              : <img src="../images/download.png" width="20" height="19" style="border:0px;" title="Download <?=$row["fu_document_filename"];?>" /></a>
            <? }						
			}
			else {
				echo " No attachment found.<br/>";
			}
		?></td>
      </tr>
    </table>
	</fieldset>
	</br>
	
	<table>
		<tr>
			<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../monthlyreport/progress_history.php';" /></td>
		</tr>
	</table>
	</form>

	<script>
		<?=$jscript;?>
	</script>
</body>
</html>



