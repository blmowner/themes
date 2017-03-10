<?php

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
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Untitled Document</title>
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

	$msg = array();

	if(empty($_POST['addChapterNo'])) $msg[] = "<div class=\"error\"><span>Please select Chapter No from the list given.</span></div>";
	if(empty($_POST['addChapterDesc'])) $msg[] = "<div class=\"error\"><span>Please enter Chapter Description</span></div>";
	
	if(empty($msg)) 
	{	
  			
		$addChapterNo = $_POST['addChapterNo'];	
		$addChapterDesc = $_POST['addChapterDesc'];
		$curdatetime = date("Y-m-d H:i:s");	
		$chapterId = runnum('id','pg_chapter');

		$sql2_1 = "SELECT id
		FROM pg_chapter
		WHERE chapter_no IN ('$addChapterNo')
		AND student_matrix_no = '$matrixNo'
		AND status = 'A'";
		
		$result_sql2_1 = $dba->query($sql2_1); 
		$dba->next_record();
		$row_cnt_2_1 = mysql_num_rows($result_sql2_1);
		if ($row_cnt_2_1==0) {
			$sql2=" INSERT INTO pg_chapter
			(id, chapter_no, description, student_matrix_no, status, insert_by, insert_date, modify_by, modify_date)
			VALUES ('$chapterId', '$addChapterNo', '$addChapterDesc', '$matrixNo', 'A', '$matrixNo', '$curdatetime', '$matrixNo', '$curdatetime') ";
			
			$dbb->query($sql2);
			
			$msg[] = "<div class=\"success\"><span>The new Chapter has been added successfully.</span></div>";
		}
		else {
			$msg[] = "<div class=\"error\"><span>The chapter is already exist! Adding is aborted.</span></div>";			
		}
  	}
}


if(isset($_POST['btnDelChapter']) && ($_POST['btnDelChapter'] <> ""))
{
	
	$chapterBox=$_POST['chapterBox'];
	$updChapterNo=$_POST['updChapterNo'];
	$updChapterDesc=$_POST['updChapterDesc'];
	$thesisId=$_POST['thesisId'];
	$proposalId=$_POST['proposalId'];
	$msg = Array();
	$count = count($chapterBox);
	if ($count>0) {		
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
			
				$sql4_2="SELECT id
				FROM pg_subchapter 
				WHERE chapter_id = '$chapterId[$val]'";
				$result_sql4_2 = $dbb->query($sql4_2); 
				$row_cnt4_2 = mysql_num_rows($result_sql4_2);
				
				if ($row_cnt4_2 == 0) {
					$sql4_1=" DELETE pg_chapter
					FROM pg_chapter
					WHERE id = '$chapterId[$val]'";
					
					$dbb->query($sql4_1); 
					
					$msg[] = "<div class=\"success\"><span>The Chapter ". romanNumerals($updChapterNo[$val])." - $updChapterDesc[$val] has been deleted successfully.</span></div>";
				}
				else {
					$msg[] = "<div class=\"error\"><span>The Chapter ". romanNumerals($updChapterNo[$val])." - $updChapterDesc[$val] is linked to the subchapter as shown in subchapter table below. Please delete the subchapter first. Deletion is aborted!.</span></div>";
				}

			}
			else {				
				$msg[] = "<div class=\"error\"><span>The Chapter ". romanNumerals($updChapterNo[$val])." - $updChapterDesc[$val] is used by the application already. Deletion is aborted!.</span></div>";
			}
			
		}
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please select which Chapter to be deleted before click DELETE CHAPTER button.</span></div>";
	}
}

if(isset($_POST['btnUpdChapter']) && ($_POST['btnUpdChapter'] <> ""))
{
	$anchorChap = $_POST['anchorChap'];
	$chapterBox=$_POST['chapterBox'];
	$chapterId=$_POST['chapterId'];
	$updChapterNo=$_POST['updChapterNo'];
	$updChapterDesc=$_POST['updChapterDesc'];
	$curdatetime = date("Y-m-d H:i:s");	
	$msg = array();	
	$i=0;
	$count = count($chapterBox);
	if ($count>0) {
		while (list ($key,$val) = @each ($chapterBox)) 
		{
			$sql1="SELECT * FROM pg_chapter 
			WHERE student_matrix_no = '$matrixNo'
			AND chapter_no = '$updChapterNo[$val]'";
						
			$result_sql = $dbf->query($sql1);
			$dbf->next_record();
			$chapterno=$dbf->f('chapter_no');
			$row_cnt = mysql_num_rows($result_sql);
			$updChapterNo[$val];
			//echo "Chapter no : ".$chapterno;
			//echo "Update chapter no : ".$updChapterNo[$val];
			if($row_cnt < 0)//if chapter no sama
			{	
				if($updChapterDesc[$val]!="") // if description tak kosong
				{	
					$sql5=" UPDATE pg_chapter
					set chapter_no = '$updChapterNo[$val]', description = '$updChapterDesc[$val]', 
					modify_by = '$matrixNo', modify_date = '$curdatetime'
					WHERE id = '$chapterId[$val]'";
							
					$dbb->query($sql5); 
					
					$msg[$i] = "<div class=\"success\"><span>The selected Chapter ". romanNumerals($updChapterNo[$val])." - $updChapterDesc[$val] has been updated successfully!</span></div>";					
				}
				else {			
					$msg[$i] = "<div class=\"error\"><span>The selected Chapter ". romanNumerals($updChapterNo[$val])." - $updChapterDesc[$val] has been provided with empty description. Please enter the new description! For now it will be reverted to the previous description.</span></div>";					
				}
			}
			else if($chapterno == $anchorChap[$val])//if chapter tak sama
			{
				if($updChapterDesc[$val]!="") // if description tak kosong
				{	
					$sql5=" UPDATE pg_chapter
					set chapter_no = '$updChapterNo[$val]', description = '$updChapterDesc[$val]', 
					modify_by = '$matrixNo', modify_date = '$curdatetime'
					WHERE id = '$chapterId[$val]'";
							
					$dbb->query($sql5); 
					
					$msg[$i] = "<div class=\"success\"><span>The selected Chapter ". romanNumerals($updChapterNo[$val])." - $updChapterDesc[$val] has been updated successfully!</span></div>";					
				}
				else 
				{
					$msg[$i] = "<div class=\"error\"><span>The selected Chapter ". romanNumerals($updChapterNo[$val])." - $updChapterDesc[$val] has been provided with empty description. Please enter the new description! For now it will be reverted to the previous description.</span></div>";					
				}
				$i++;
			}
			else if ($chapterno ==$updChapterNo[$val])
			{
				$msg[] = "<div class=\"error\"><span>The Chapter is already exist! Adding is aborted.</span></div>";
			}
			else 
			{	
				if($updChapterDesc[$val]!="") // if description tak kosong
				{	
					$sql5=" UPDATE pg_chapter
					set chapter_no = '$updChapterNo[$val]', description = '$updChapterDesc[$val]', 
					modify_by = '$matrixNo', modify_date = '$curdatetime'
					WHERE id = '$chapterId[$val]'";
							
					$dbb->query($sql5); 
					
					$msg[$i] = "<div class=\"success\"><span>The selected Chapter ". romanNumerals($updChapterNo[$val])." - $updChapterDesc[$val] has been updated successfully!</span></div>";					
				}
				else
				{
					$msg[$i] = "<div class=\"error\"><span>The selected Chapter ". romanNumerals($updChapterNo[$val])." - $updChapterDesc[$val] has been provided with empty description. Please enter the new description! For now it will be reverted to the previous description.</span></div>";							
				}
			
			}		
		}		
	}
	else {
		$msg[$i] = "<div class=\"error\"><span>Please select which Chapter to be updated before click UPDATE CHAPTER button.</span></div>";
	}
}

if(isset($_POST['btnAddSubChapter']) && ($_POST['btnAddSubChapter'] <> ""))
{
  	$msg = array();

	if(empty($_POST['addSubChapterNo'])) $msg[] = "<div class=\"error\"><span>Please select Sub-Chapter No from the list given.</span></div>";
	if(empty($_POST['addSubChapterDesc'])) $msg[] = "<div class=\"error\"><span>Please enter Sub-Chapter Description</span></div>";
	if($_POST['chapterIdList'] == "") $msg[] = "<div class=\"error\"><span>Please select Chapter No from the list given.</span></div>";

	if(empty($msg)) 
	{	
				
		$addChapterId = $_POST['chapterIdList'];
		$addSubChapterNo = $_POST['addSubChapterNo'];
		$addSubChapterDesc = $_POST['addSubChapterDesc'];
		$curdatetime = date("Y-m-d H:i:s");	
		$subChapterId = runnum('id','pg_subchapter');

		$sql6_1 = "SELECT id
					FROM pg_subchapter
					WHERE chapter_id = '$addChapterId'
					AND subchapter_no = '$addSubChapterNo'
					AND student_matrix_no = '$matrixNo'
					AND status = 'A'";
		
		$result_sql6_1 = $dba->query($sql6_1); 
		$dba->next_record();
		$row_cnt_6_1 = mysql_num_rows($result_sql6_1);
		if ($row_cnt_6_1==0) {			
			$sql6=" INSERT INTO pg_subchapter
			(id, chapter_id, subchapter_no, description, student_matrix_no, status, insert_by, insert_date, modify_by, modify_date)
			VALUES ('$subChapterId', '$addChapterId', '$addSubChapterNo', '$addSubChapterDesc', '$matrixNo', 'A', '$matrixNo', '$curdatetime', 		'$matrixNo', '$curdatetime') ";
			
			$dbb->query($sql6); 
			
			$msg[] = "<div class=\"success\"><span>The new Sub-Chapter has been added successfully.</span></div>";
		}
		else {
			$msg[] = "<div class=\"error\"><span>The Sub-Chapter is already exist! Adding is aborted.</span></div>";
		}
	}	
}

if(isset($_POST['btnDelSubChapter']) && ($_POST['btnDelSubChapter'] <> ""))
{
	$chapterBox=$_POST['chapterBox'];
	$updSubChapterNo=$_POST['updSubChapterNo'];
	$updSubChapterDesc=$_POST['updSubChapterDesc'];
	$thesisId=$_POST['thesisId'];
	$proposalId=$_POST['proposalId'];
	
	$count = count($chapterBox);
	if ($count>0) {	
		while (list ($key,$val) = @each ($chapterBox)) 
		{
			
			$sql8="SELECT c.pg_subchapter_id 
				FROM pg_subchapter b 
				LEFT JOIN pg_discussion c ON (c.pg_subchapter_id = b.id) 
				WHERE b.id = '$subChapterId[$val]'
				AND c.student_matrix_no = '$user_id'
				AND c.pg_thesis_id = '$thesisId'
				AND c.pg_proposal_id = '$proposalId'
				AND c.archived_status is null";

			$result8 = $dbb->query($sql8); 
			$row_cnt8 = mysql_num_rows($result8);

			if ($row_cnt8 == 0) {
				$sql8_1=" DELETE 
					FROM pg_subchapter
					WHERE id = '$subChapterId[$val]'";
					
				$dbb->query($sql8_1); 
				$msg[] = "<div class=\"success\"><span>The selected sub-chapter ". romanNumerals($updSubChapterNo[$val])." - $updSubChapterDesc[$val] is deleted successfully!</span></div>";
			}
			else {
				$msg[] = "<div class=\"error\"><span>This Sub-chapter ". romanNumerals($updSubChapterNo[$val])." - $updSubChapterDesc[$val] is used by the application already. Deletion is aborted!</span></div>";
				
			}
			
		}
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please select which Sub-Chapter to be deleted before click DELETE SUB-CHAPTER button.</span></div>";
	}
}

if(isset($_POST['btnUpdSubChapter']) && ($_POST['btnUpdSubChapter'] <> ""))
{
	$chapterBox=$_POST['chapterBox'];
	$chapterId=$_POST['chapterIdSub']; 
	$anchorSub =$_POST['anchorSub'];
	//$chapid = $_REQUEST['chapterId'];
	//chapterId[]
	$subChapterId=$_POST['subChapterId'];
	$updSubChapterNo=$_POST['updSubChapterNo'];
	$updSubChapterDesc=$_POST['updSubChapterDesc'];
	$curdatetime = date("Y-m-d H:i:s");	
	$count = count($chapterBox);
	$msg = array();
	$i=0;
	//echo "Luar <br>";
	if ($count>0) {		
		while (list ($key,$val) = @each ($chapterBox)) 
		{
			//echo $anchorSub[$val];
			if(empty($updSubChapterDesc[$val])) $msg[$i] = "<div class=\"error\"><span>Please enter Sub-chapter Description</span></div>";
			$count;
			//echo "<br>1 <br> $updSubChapterDesc[$val]<br>";
			$sql1="SELECT * FROM pg_subchapter 
			WHERE student_matrix_no = '$matrixNo'
			AND subchapter_no = '$updSubChapterNo[$val]'
			AND chapter_id = '$chapterId[$val]'";
						
			$result_sql = $dbf->query($sql1);
			$dbf->next_record();
			$sChapterNo=$dbf->f('subchapter_no');
			$desc=$dbf->f('description');
			$row_cnt = mysql_num_rows($result_sql);
			if($row_cnt == 1) 
			{
				if($updSubChapterDesc[$val]!="" && $updSubChapterNo[$val] == $anchorSub[$val]) 
				{	
					//echo "<br>2/1 <br>$updSubChapterNo[$val] : $anchorSub[$val] <br>$updSubChapterDesc[$val] : $desc";
					if($updSubChapterNo[$val] != $anchorSub[$val] && $updSubChapterDesc[$val] == $desc)
					{
						$msg[] = "<div class=\"error\"><span>The Sub-Chapter is already exist! Updating is aborted.</span></div>";
					}
					else 
					{
						$sql8_1=" UPDATE pg_subchapter
						SET description = '$updSubChapterDesc[$val]',
						modify_by = '$matrixNo', modify_date = '$curdatetime'
						WHERE id = '$subChapterId[$val]'
						AND chapter_id = '$chapterId[$val]'
						AND student_matrix_no = '$user_id'
						AND status ='A'";
		
						$result8_1 = $dbb->query($sql8_1); 
					//$row_cnt8_1 = mysql_num_rows($result8_1);
					
					$msg[$i] = "<div class=\"success\"><span>The selected sub-chapter ". romanNumerals($updSubChapterNo[$val])." - $updSubChapterDesc[$val] has been updated successfully!</span></div>";	
					}				
				}
				else if($updSubChapterNo[$val]==$sChapterNo && $updSubChapterDesc[$val]!= ""){
						$msg[] = "<div class=\"error\"><span>The Sub-Chapter is already exist! Updating is aborted.</span></div>";
				}
				else 
				{
					$msg[$i] = "<div class=\"error\"><span>The selected sub-chapter ". romanNumerals($updSubChapterNo[$val])." - $updSubChapterDesc[$val] has been provided with empty description. Please enter the new description! For now it will be reverted to the previous description.</span></div>";
				}
				$i++;
			}
			else if ($updSubChapterDesc[$val] != "")
			{
				$sql8_1=" UPDATE pg_subchapter
				SET subchapter_no = '$updSubChapterNo[$val]' , description = '$updSubChapterDesc[$val]',
				modify_by = '$matrixNo', modify_date = '$curdatetime'
				WHERE id = '$subChapterId[$val]'
				AND chapter_id = '$chapterId[$val]'
				AND student_matrix_no = '$user_id'
				AND status ='A'";

				$result8_1 = $dbb->query($sql8_1); 
				//$row_cnt8_1 = mysql_num_rows($result8_1);
				
				$msg[$i] = "<div class=\"success\"><span>The selected sub-chapter ". romanNumerals($updSubChapterNo[$val])." - $updSubChapterDesc[$val] has been updated successfully!</span></div>";					
				
			}
			else
			{
				$msg[$i] = "<div class=\"error\"><span>The selected sub-chapter ". romanNumerals($updSubChapterNo[$val])." - $updSubChapterDesc[$val] has been provided with empty description. Please enter the new description! For now it will be reverted to the previous description.</span></div>";
			
			}
		}
	}
	else {
		$msg[$i] = "<div class=\"error\"><span>Please select which Sub-Chapter to be updated before click UPDATE SUB-CHAPTER button.</span></div>";		
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
		<link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
		<link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
		<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
		<link rel="stylesheet" href="../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
		<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
		<script src="../../../lib/js/jquery.min2.js"></script>
		<script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
		
		
		
	</head>
	<body>
	<?php
    if(!empty($msg)) {
        foreach($msg as $err) {
            echo $err;
        }
    }
	?>
	<form id="form1" name="form1" method="post" enctype="multipart/form-data">
	<?if ($row_cnt0>0) {?>
		<fieldset>
		<legend><strong>Thesis Chapter</strong></legend>
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
							if (substr($matrixNo,0,2) != '07') { 
								$dbConnStudent= $dbc; 
							} 
							else { 
								$dbConnStudent=$dbc1; 
							}	
							$result5 = $dbConnStudent->query($sql5); 
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
					<td><?=$thesisId?></td>
				</tr>
			</table>
			<br/>
			<input type="hidden" name="thesisId" id="thesisId" value="<?=$thesisId; ?>">
			<input type="hidden" name="proposalId" id="proposalId" value="<?=$proposalId; ?>">
			<fieldset>
			<legend><strong>Chapter</strong></legend>
				<table>
				<tr>
				<?$sql3 = "SELECT id, chapter_no, description
									FROM ref_chapter
									WHERE status = 'A'
									ORDER BY chapter_no";
									
									$result_sql3 = $dba->query($sql3);
				?>
					<td><label>Chapter No <span style="color:#FF0000">*</span></label></td>
					<td><select name="addChapterNo" id="addChapterNo">
						<option value="" selected="selected"></option>
						<? 
							while ($dba->next_record()) {
								$chapterId=$dba->f('id');
								$chapterNo=$dba->f('chapter_no');
								$chapterDesc=$dba->f('description');
								?><option value="<?=$chapterId?>"><?=romanNumerals($chapterNo)?> <?=$chapterDesc?></option><?
							};
						?>
						</select>
					</td>
				</tr>
				<tr>
					<td><label>Description <span style="color:#FF0000">*</span></label></td>
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
					<table>
						<tr>							
							<td><strong>List of Defined Chapter:-</strong></td>
						</tr>
					</table>			
					<table width="60%" border="1" cellpadding="3" cellspacing="1" style="border-collapse:collapse;" class="thetable">
						<tr>
							<th width="5%" align="center"><strong>Tick</strong></th>
							<th width="10%"><strong>Chapter No. <span style="color:#FF0000">*</span></strong></th>
							<th width="35%"><strong>Description <span style="color:#FF0000">*</span></strong></th>
						</tr>    
						<?
						if ($row_cnt>0)	{
						$no1=0;
						$myArrayNo=0;
						do {											
							$chapterId=$dbf->f('chapter_id');	
							$chapterNo=$dbf->f('chapter_no');
							$chapterDesc=$dbf->f('chapter_desc');
						?>
						<tr>
							<td align="center"><input name="chapterBox[]" type="checkbox" value="<?=$no1;?>"/></td>				
							<input type="hidden" name="chapterId[]" id="chapterId" size="15" value="<?=$chapterId?>">
							
							<?$sql3 = "SELECT id, chapter_no, description
									FROM ref_chapter
									WHERE status = 'A'
									ORDER BY chapter_no";
									
									$result_sql3 = $dba->query($sql3);
							?>
						  <td><select name="updChapterNo[]" id="updChapterNo[]">
								<? 
									while ($dba->next_record()) {
										$refChapterId=$dba->f('id');
										$refChapterNo=$dba->f('chapter_no');
										//$chapterDesc=$dba->f('description');
										if ($refChapterNo==$chapterNo) {
										?><option value="<?=$refChapterId?>" selected="selected"><?=romanNumerals($refChapterNo)?></option><?
										}
										else {
											?><option value="<?=$refChapterId?>"><?=romanNumerals($refChapterNo)?></option><?
										}
									};
								?>
							</select>
						    <input type="hidden" id="anchorChap[]" name="anchorChap[]" value="<?=$chapterNo?>" /></td>
							
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
							<td><input type="submit" name="btnDelChapter" value="Delete Chapter" /> <span style="color:#FF0000">Note:</span> Field marks with (<span style="color:#FF0000">*</span>) is compulsory.</td>
							
						</tr>
					</table>
			
					<br/>
				<?
				}
				else {
					?>
					<table>
						<tr>
							<td>No record found!</td>
						</tr>
					</table>					
				<?
				}?>
			</fieldset>
			
			<br/>
			<fieldset>
				<legend><strong>Sub-Chapter</strong></legend>
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
							<td><label>Chapter No <span style="color:#FF0000">*</span></label></td>
							<td><select name="chapterIdList" id="chapterIdList">
								<option value="" selected="selected"></option>
								<? 
									while ($dbf->next_record()) {
										$chapterId=$dbf->f('id');
										$chapterNo=$dbf->f('chapter_no');
										$chapterDesc=$dbf->f('description');
										?><option value="<?=$chapterId?>"><?=romanNumerals($chapterNo)?> - <?=$chapterDesc?></option><?
									};
								?>
								</select>
							</td>
						</tr>
						<tr>
						<?$sql3 = "SELECT id, subchapter_no, description
											FROM ref_subchapter
											WHERE status = 'A'
											ORDER BY subchapter_no";
											
											$result_sql3 = $dba->query($sql3);
						?>
							<td><label>Sub-Chapter No <span style="color:#FF0000">*</span></label></td>
							<td><select name="addSubChapterNo" id="addSubChapterNo">
								<option value="" selected="selected"></option>
								<? 
									while ($dba->next_record()) {
										$subChapterId=$dba->f('id');
										$subChapterNo=$dba->f('subchapter_no');
										$subChapterDesc=$dba->f('description');
										?><option value="<?=$subChapterId?>"><?=romanNumerals($subChapterNo)?> <?=$subChapterDesc?></option><?
									};
								?>
								</select>
							</td>
						</tr>
						<tr>
							<td><label>Description <span style="color:#FF0000">*</span></label></td>
							<td><input type="text" name="addSubChapterDesc" size="50" id="addSubChapterDesc"></td>
						</tr>
					</table>
					<table>
						<tr>
							<td><input type="submit" name="btnAddSubChapter" value="Add Sub-Chapter" /></td>
						</tr>  
					</table>
					<br/>

					<table>
						<tr>							
							<td><strong>List of Defined Sub-Chapter:-</strong></td>
						</tr>
					</table>
					<table width="100%" border="1" cellpadding="3" cellspacing="1" style="border-collapse:collapse;" class="thetable">
						<tr>
							<th align="center"><strong>Tick</strong></th>
							<th><strong>Chapter No.</strong></th>
							<th><strong>Description</strong></th>
							<th><strong>Sub-Chapter No. <span style="color:#FF0000">*</span></strong></th>
							<th><strong>Description <span style="color:#FF0000">*</span></strong></th>
						</tr>    
						<?
						if ($row_cnt2>0) {
						$no1=0;
						$myArrayNo=0;
						do {											
							$chapterNo=$dbb->f('chapter_no');
							$chapterId=$dbb->f('chapter_id');
							$chapterDesc=$dbb->f('chapter_desc');
							$subChapterId=$dbb->f('subchapter_id');
							$subChapterNo=$dbb->f('subchapter_no');
							$subChapterDesc=$dbb->f('subchapter_desc');
						?>
						<tr>
							<td align="center"><input name="chapterBox[]" type="checkbox" value="<?=$no1;?>"/>			
							<input type="hidden" name="chapterIdSub[]" id="chapterIdSub[]" size="15" value="<?=$chapterId?>"/>
							<input type="hidden" name="subChapterId[]" id="subChapterId" size="15" value="<?=$subChapterId?>"/></td>	
							<td><label><?=romanNumerals($chapterNo)?></label></td>
							<td><label><?=$chapterDesc?></label></td>
							
							<?$sql3 = "SELECT id, subchapter_no, description
									FROM ref_subchapter
									WHERE status = 'A'
									ORDER BY subchapter_no";
									
									$result_sql3 = $dba->query($sql3);
							?>
							<td><select name="updSubChapterNo[]" id="updSubChapterNo[]">
								<? 
									while ($dba->next_record()) {
										$refSubChapterId=$dba->f('id');
										$refSubChapterNo=$dba->f('subchapter_no');
										//$subChapterDesc=$dba->f('description');
										if ($refSubChapterNo==$subChapterNo) {
										?><option value="<?=$refSubChapterId?>" selected="selected"><?=romanNumerals($refSubChapterNo)?></option><?
										}
										else {
											?><option value="<?=$refSubChapterId?>"><?=romanNumerals($refSubChapterNo)?></option><?
										}
									};
								?>
								</select>
								<input type="hidden" id="anchorSub[]" name="anchorSub[]" value="<?=$subChapterNo?>" />
							</td>
							
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
							<td><input type="submit" name="btnDelSubChapter" value="Delete Sub-Chapter" /> <span style="color:#FF0000">Note:</span> Field marks with (<span style="color:#FF0000">*</span>) is compulsory.</td>
							
						</tr>
					</table>
					
				<?
				}
				else {
					?>
					<table>
						<tr>
							<td>No record found!</td>
						</tr>
					</table>

					<?
				}?>			
			</fieldset>
		</fieldset>
	<?}
	else {
		?>
		<fieldset>
		<legend><strong><span style="color:#FF0000">Notification Message</span></strong></legend>
			<table>
				<tr>
					<td>Your thesis proposal is not yet approved. You can define your chapter after it has been approved.</td>
				</tr>
			</table>
		</fieldset>
		<?
	}?>	
	<br/>
	</form>
	</body>
</html>