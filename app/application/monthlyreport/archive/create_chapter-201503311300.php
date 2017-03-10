<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Untitled Document</title>
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
<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: create_chapter.php
//
// Created by: Zuraimi
// Created Date: 16-Mar-2015
// Modified by: Zuraimi
// Modified Date: 17-Mar-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

session_start();
$matrixNo=$_SESSION['user_id'];

function runnum($column_name, $tblname) 
{ 
    global $db_klas2;
    
    $run_start = "001";
    
    $sql_slct_max = "SELECT MAX($column_name) AS run_id FROM $tblname";
    $sql_slct = $db_klas2;
    $sql_slct->query($sql_slct_max);
    $sql_slct->next_record();

    if($sql_slct->num_rows($sql_slct_max)== 0 || $sql_slct->f("run_id")==NULL) 
	{
        $run_id = date("Ymd").$run_start;
    } 
	else 
	{
        $todate = date("Ymd");
        
        if($todate > substr($sql_slct->f("run_id"),0,8)) 
		{
            $run_id = $todate.$run_start;
        } 
		else 
		{
            $run_id = $sql_slct->f("run_id") + 1; 
        }
    }
    return $run_id;
}

if(isset($_POST['btnAddChapter']) && ($_POST['btnAddChapter'] <> ""))
{
	$addChapterNo = $_POST['addChapterNo'];	
	$addChapterDesc = $_POST['addChapterDesc'];
	$curdatetime = date("Y-m-d H:i:s");	
	$chapterId = runnum('id','pg_chapter');
	
	$sql2=" INSERT INTO pg_chapter
				(id, chapter_no, description, student_matrix_no, status, insert_by, insert_date, modify_by, modify_date)
				VALUES ('$chapterId', '$addChapterNo', '$addChapterDesc', '$matrixNo', 'A', '$matrixNo', '$curdatetime', '$matrixNo', '$curdatetime') ";
				
				$dbb->query($sql2); 
				
}

if(isset($_POST['btnDelChapter']) && ($_POST['btnDelChapter'] <> ""))
{
	$chapterBox=$_POST['chapterBox'];
	$thesisId=$_POST['thesisId'];
	$proposalId=$_POST['proposalId'];
	
	
				
	while (list ($key,$val) = @each ($chapterBox)) 
	{
		$sql4="SELECT c.pg_chapter_id
			FROM pg_chapter a 
			LEFT JOIN pg_subchapter b ON (b.chapter_id = a.id) 
			LEFT JOIN pg_discussion c ON (c.pg_chapter_id = a.id) 
			WHERE a.id = '$chapterId[$val]'
			AND c.student_matrix_no = '$user_id'
			AND c.pg_thesis_id = '$thesisId'
			AND c.pg_proposal_id = '$proposalId'
			AND c.archived_status is null";

		$result4 = $dbb->query($sql4); 
		$row_cnt4 = mysql_num_rows($result4);
		if ($row_cnt4 == 0) {
		$sql4_1=" DELETE pg_chapter
				FROM pg_chapter
				WHERE id = '$chapterId[$val]'";
				
				$dbb->query($sql4_1); 
				?>			
			<fieldset>
				<legend><strong><span style="color:#FF0000">Notification Message</span></strong></legend>				
				<table>
					<tr>
						<td><label>This Chapter has been deleted successfully.</label></td>
					</tr>
				</table>
			</fieldset>
			<?
		}
		else {
			?>			
			<fieldset>
				<legend><strong><span style="color:#FF0000">Notification Message</span></strong></legend>				
				<table>
					<tr>
						<td><label>This Chapter is used by the application already. Deletion is aborted!</label></td>
					</tr>
				</table>
			</fieldset>
			<?
		}
		
	}
}

if(isset($_POST['btnUpdChapter']) && ($_POST['btnUpdChapter'] <> ""))
{
	$chapterBox=$_POST['chapterBox'];
	$updChapterNo=$_POST['updChapterNo'];
	$updChapterDesc=$_POST['updChapterDesc'];
	
	while (list ($key,$val) = @each ($chapterBox)) 
	{
		
		$sql5=" UPDATE pg_chapter
				set chapter_no = '$updChapterNo[$val]', description = '$updChapterDesc[$val]'
				WHERE id = '$chapterId[$val]'";
				
		$dbb->query($sql5); 
	}
}

if(isset($_POST['btnAddSubChapter']) && ($_POST['btnAddSubChapter'] <> ""))
{
	$addChapterId = $_POST['chapterIdList'];
	$addSubChapterNo = $_POST['addSubChapterNo'];
	$addSubChapterDesc = $_POST['addSubChapterDesc'];
	$curdatetime = date("Y-m-d H:i:s");	
	$subChapterId = runnum('id','pg_subchapter');
	
	$sql6=" INSERT INTO pg_subchapter
				(id, chapter_id, subchapter_no, description, student_matrix_no, status, insert_by, insert_date, modify_by, modify_date)
				VALUES ('$subChapterId', '$addChapterId', '$addSubChapterNo', '$addSubChapterDesc', '$matrixNo', 'A', '$matrixNo', '$curdatetime', '$matrixNo', '$curdatetime') ";
				
				$dbb->query($sql6); 
				
}

if(isset($_POST['btnDelSubChapter']) && ($_POST['btnDelSubChapter'] <> ""))
{
	$chapterBox=$_POST['chapterBox'];
	$thesisId=$_POST['thesisId'];
	$proposalId=$_POST['proposalId'];
	
	while (list ($key,$val) = @each ($chapterBox)) 
	{
		
		echo $sql8="SELECT c.pg_subchapter_id 
			FROM pg_subchapter b 
			LEFT JOIN pg_discussion c ON (c.pg_subchapter_id = b.id) 
			WHERE b.id = '$subChapterId[$val]'
			AND c.student_matrix_no = '$user_id'
			AND c.pg_thesis_id = '$thesisId'
			AND c.pg_proposal_id = '$proposalId'
			AND c.archived_status is null";

		$result8 = $dbb->query($sql8); 
		echo $row_cnt8 = mysql_num_rows($result8);

		if ($row_cnt8 == 0) {
			$sql8_1=" DELETE 
				FROM pg_subchapter
				WHERE id = '$subChapterId[$val]'";
				
			$dbb->query($sql8_1); 
			?>			
			<fieldset>
				<legend><strong><span style="color:#FF0000">Notification Message</span></strong></legend>				
				<table>
					<tr>
						<td><label>This Sub-chapter has been deleted successfully.</label></td>
					</tr>
				</table>
			</fieldset>
			<?
		}
		else {
			?>			
			<fieldset>
				<legend><strong><span style="color:#FF0000">Notification Message</span></strong></legend>				
				<table>
					<tr>
						<td><label>This Sub-chapter is used by the application already. Deletion is aborted!</label></td>
					</tr>
				</table>
			</fieldset>
			<?
		}
	}
}

$sql0 = "SELECT pt.id AS thesis_id, pt.student_matrix_no,pt.status AS thesis_status,
				pp.id AS proposal_id, pp.thesis_title,pp.thesis_type, pp.objective, pp.introduction,pp.description,pp.discussion_status, 
				DATE_FORMAT(pp.verified_date,'%d-%b-%Y') AS verified_date, pp.verified_remarks, pp.verified_by,
				pp.verified_status AS proposal_status,pp.endorsed_by, DATE_FORMAT(pp.endorsed_date,'%d-%b-%Y') AS endorsed_date, 
				pp.endorsed_remarks, pp.status AS endorsed_status, 
				rps.description AS proposal_description, rps2.description AS endorsed_desc, 
				DATE_FORMAT(pp.cancel_requested_date,'%d-%b-%Y') AS cancel_requested_date,
				DATE_FORMAT(pp.cancel_approved_date,'%d-%b-%Y') AS cancel_approved_date, 
				pp.cancel_approved_by, pp.cancel_approved_remarks 
				FROM pg_thesis pt 
				LEFT JOIN pg_proposal pp ON (pp.pg_thesis_id=pt.id) 
				LEFT JOIN ref_proposal_status rps ON (rps.id = pp.verified_status) 
				LEFT JOIN ref_proposal_status rps2 ON (rps2.id = pp.status) 
				WHERE pt.student_matrix_no = '$user_id'
				AND pp.verified_status in ('APP','AWC')				
				AND pp.archived_status is null
				AND pt.ref_thesis_status_id_proposal in ('APP','AWC','APC')
				ORDER BY pt.id";

$result0 = $dba->query($sql0); 
$dba->next_record();
$thesisId=$dba->f('thesis_id');
$proposalId=$dba->f('proposal_id');
$row_cnt0 = mysql_num_rows($result0);

$sql1 = "SELECT id as chapter_id, chapter_no, description as chapter_desc
		FROM pg_chapter 
		WHERE status = 'A'
		AND student_matrix_no = '$matrixNo'
		ORDER BY chapter_no";

$result1 = $dbf->query($sql1); 
$dbf->next_record();
$row_cnt = mysql_num_rows($result1);

$sql7 = "SELECT a.id as chapter_id, a.chapter_no, a.description as chapter_desc, 
		b.id as subchapter_id, b.subchapter_no, b.description as subchapter_desc
		FROM  pg_subchapter b
		LEFT JOIN pg_chapter a ON (a.id = b.chapter_id) 
		WHERE a.status = 'A'
		AND a.student_matrix_no = '$matrixNo'
		AND (b.status = 'A' OR b.status IS NULL)
		ORDER BY a.chapter_no, b.subchapter_no";

$result7 = $dbb->query($sql7); 
$dbb->next_record();
$row_cnt2 = mysql_num_rows($result7);



?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Untitled Document</title>
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
	<fieldset>
	<legend><strong>Chapter Maintenance for Thesis</strong></legend>
		<table>
			<tr>
				<td>Student Matrix No</td>
				<td>:</td>
				<td><?=$matrixNo?></td>
			</tr>
			<?
				$sql5 = "SELECT name AS student_name
						FROM student
						WHERE matrix_no = '$matrixNo'";
							
						$result5 = $dbc->query($sql5); 
						$dbc->next_record();
						$sname=$dbc->f('student_name');
			
				?>
			<tr>
				<td>Student Name</td>
				<td>:</td>
				<td><?=$sname?></td>
			</tr>
			<tr>
				<td>Thesis ID</td>
				<td>:</td>
				<td><?=$thesisId?></td>
			</tr>
		</table>
		<br/>
		<input type="hidden" name="thesisId" id="thesisId" value="<?=$thesisId; ?>">
		<input type="hidden" name="proposalId" id="proposalId" value="<?=$proposalId; ?>">
		<fieldset>
		<legend><strong>Chapter Maintenance</strong></legend>
			<table>
			<tr>
				<td><label>Chapter No</label></td>
				<td><input type="text" name="addChapterNo" size="10" id="addChapterNo"></td>
			</tr>
			<tr>
				<td><label>Description</label></td>
				<td><input type="text" name="addChapterDesc" size="50" id="addChapterDesc"></td>
			</td>
			<tr>
			</table>
			<table>
				<tr>
					<td><input type="submit" name="btnAddChapter" value="Add Chapter" /></td>
					
				</tr>  
			</table>
			<br/>
			<?
			if ($row_cnt>0) {?>	
				<table>
					<tr>							
						<td><strong>List of Defined Chapter:-</strong></td>
					</tr>
				</table>			
				<table border="1" style="border-collapse:collapse;" cellpadding="4" cellspacing="3">
					<tr>
						<td><strong>Tick</strong></td>
						<td><strong>Chapter No.</strong></td>
						<td><strong>Description</strong></td>
					</tr>    
					<?
					$no1=0;
					$myArrayNo=0;
					do {											
						$chapterId=$dbf->f('chapter_id');	
						$chapterNo=$dbf->f('chapter_no');
						$chapterDesc=$dbf->f('chapter_desc');
					?>
					<tr>
						<td><input name="chapterBox[]" type="checkbox" value="<?=$no1;?>"/></td>				
						<input type="hidden" name="chapterId[]" id="chapterId" size="15" value="<?=$chapterId?>">
						<td><input type="text" name="updChapterNo[]" id="updChapterNo" size="15" value="<?=$chapterNo?>"></td>
						<td><input name="updChapterDesc[]" id="updChapterDesc" size="50" value="<?=$chapterDesc?>"></label></td>
					</tr>  
					<?
					$no1=$no1+1;
					}while($dbf->next_record());	
					?>			
				</table>
				<br />
				<table>
					<tr>
						<td><input type="submit" name="btnUpdChapter" value="Update Chapter" /></td>
						<td><input type="submit" name="btnDelChapter" value="Delete Chapter" /></td>
						
					</tr>
				</table>
		
				<br/>
			<?
			}
			else {
				?>
				<table>
					<tr>							
						<td><strong>List of Defined Chapter:-</strong></td>
					</tr>
				</table>
				<fieldset>
				<legend><strong><span style="color:#FF0000">Notification Message</span></strong></legend>
					<table>
						<tr>
							<td>No sub-chapter has been defined for monthly report.</td>
						</tr>
					</table>
				</fieldset>
			<?
			}?>
		</fieldset>
		
		<br/>
		<fieldset>
			<legend><strong>Sub-Chapter Maintenance</strong></legend>
				<table>
					<tr>
					<?$sql3 = "SELECT id, chapter_no, description
										FROM pg_chapter
										WHERE student_matrix_no = '$matrixNo'
										AND status = 'A'
										ORDER BY chapter_no";
										
										$result_sql3 = $dbf->query($sql3);
										//$dbf->next_record()
					?>
						<td><label>Chapter No</label></td>
						<td><select name="chapterIdList" id="chapterIdList">
							<option value="" selected="selected">--Please Select--</option>
							<? 
								while ($dbf->next_record()) {
									echo $chapterId=$dbf->f('id');
									$chapterNo=$dbf->f('chapter_no');
									$chapterDesc=$dbf->f('description');
									?><option value="<?=$chapterId?>"><?=$chapterNo?> <?=$chapterDesc?></option><?
								};
							?>
							</select>
						</td>
					</tr>
					<tr>
						<td><label>Subchapter No</label></td>
						<td><input type="text" name="addSubChapterNo" size="10" id="addSubChapterNo"></td>
					</tr>
					<tr>
						<td><label>Description</label></td>
						<td><input type="text" name="addSubChapterDesc" size="50" id="addSubChapterDesc"></td>
					</tr>
				</table>
				<table>
					<tr>
						<td><input type="submit" name="btnAddSubChapter" value="Add Sub-Chapter" /></td>
					</tr>  
				</table>
				<br/>
				<?
			if ($row_cnt2>0) {?>	
				<table>
					<tr>							
						<td><strong>List of Defined Sub-chapter:-</strong></td>
					</tr>
				</table>
				<table border="1" style="border-collapse:collapse;" cellpadding="4" cellspacing="3">
					<tr>
						<td><strong>Tick</strong></td>
						<td><strong>Chapter No.</strong></td>
						<td><strong>Description</strong></td>
						<td><strong>Sub-Chapter No.</strong></td>
						<td><strong>Description</strong></td>
					</tr>    
					<?
					$no1=0;
					$myArrayNo=0;
					do {											
						$chapterNo=$dbb->f('chapter_no');
						$chapterDesc=$dbb->f('chapter_desc');
						$subChapterId=$dbb->f('subchapter_id');
						$subChapterNo=$dbb->f('subchapter_no');
						$subChapterDesc=$dbb->f('subchapter_desc');
					?>
					<tr>
						<td><input name="chapterBox[]" type="checkbox" value="<?=$no1;?>"/></td>				
						<input type="hidden" name="subChapterId[]" id="subChapterId" size="15" value="<?=$subChapterId?>">
						<td><label><?=$chapterNo?></label></td>
						<td><label><?=$chapterDesc?></label></td>
						<td><input type="text" name="updSubChapterNo[]" id="updSubChapterNo" size="15" value="<?=$subChapterNo?>"></td>
						<td><input name="updSubChapterDesc[]" id="updSubChapterDesc" size="50" value="<?=$subChapterDesc?>"></label></td>
					</tr>  
					<?
					$no1=$no1+1;
					}while($dbb->next_record());	
					?>			
				</table>
				<br />
				<table>
					<tr>
						<td><input type="submit" name="btnUpdSubChapter" value="Update Sub-Chapter" /></td>
						<td><input type="submit" name="btnDelSubChapter" value="Delete Sub-Chapter" /></td>
						
					</tr>
				</table>
				
			<?
			}
			else {
				?>
				<table>
					<tr>							
						<td><strong>List of Defined Sub-chapter:-</strong></td>
					</tr>
				</table>
				<fieldset>
				<legend><strong><span style="color:#FF0000">Notification Message</span></strong></legend>
					<table>
						<tr>
							<td>No sub-chapter has been defined for monthly report.</td>
						</tr>
					</table>
				</fieldset>
				<?
			}?>			
		</fieldset>
	</fieldset>
	<br/>
	</form>
	</body>
</html>