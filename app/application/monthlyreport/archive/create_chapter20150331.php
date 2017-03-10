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

 function check_user_aut($usrid) 
{
	global $db;

	$sql_check_user_aut = "SELECT count(*) as total FROM user_aut WHERE staff_id='$usrid' AND status='ACT'";
	$result_check_user_aut = $db;
	$result_check_user_aut->query($sql_check_user_aut);
	$result_check_user_aut->next_record();
		
	$rows = $result_check_user_aut->rowdata();
	$value = $rows['total'];       
	return $value;
} 


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

	if(empty($_POST['addChapterNo'])) $msg[] = "<div class=\"error\"><span>Please insert Chapter No</span></div>";
	if(empty($_POST['addChapterDesc'])) $msg[] = "<div class=\"error\"><span>Please insert Description</span></div>";
	//if(empty($_POST['objective'])) $msg[] = "<div class=\"error\"><span>Please insert objective</span></div>";
	//if(empty($_POST['description'])) $msg[] = "<div class=\"error\"><span>Please insert Description</span></div>";
	//if($_POST['jobs1_area'] == "") $msg[] = "<div class=\"error\"><span>Please select Thesis Area</span></div>";

	if(empty($msg)) 
	{	
  			
				$addChapterNo = $_POST['addChapterNo'];	
				$addChapterDesc = $_POST['addChapterDesc'];
				$curdatetime = date("Y-m-d H:i:s");	
				$chapterId = runnum('id','pg_chapter');
	
				$sql2=" INSERT INTO pg_chapter
				(id, chapter_no, description, student_matrix_no, status, insert_by, insert_date, modify_by, modify_date)
				VALUES ('$chapterId', '$addChapterNo', '$addChapterDesc', '$matrixNo', 'A', '$matrixNo', '$curdatetime', '$matrixNo', '$curdatetime') ";
				
				$dbb->query($sql2);
				echo '<i style="color:black;font-size:15px;font-family:calibri ;"> Add Chapter Success! </i> ';
  	}
  			
		
				
}


if(isset($_POST['btnDelChapter']) && ($_POST['btnDelChapter'] <> ""))
{
	$chapterBox=$_POST['chapterBox'];
	
	while (list ($key,$val) = @each ($chapterBox)) 
	{
		
		$sql4=" DELETE pg_chapter
				FROM pg_chapter
				WHERE id = '$chapterId[$val]'";
				
		$dbb->query($sql4); 
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
  		$msg = array();

	if(empty($_POST['addSubChapterNo'])) $msg[] = "<div class=\"error\"><span>Please insert Sub Chapter No</span></div>";
	if(empty($_POST['addSubChapterDesc'])) $msg[] = "<div class=\"error\"><span>Please insert Sub Description</span></div>";
	//if(empty($_POST['objective'])) $msg[] = "<div class=\"error\"><span>Please insert objective</span></div>";
	//if(empty($_POST['description'])) $msg[] = "<div class=\"error\"><span>Please insert Description</span></div>";
	if($_POST['chapterIdList'] == "") $msg[] = "<div class=\"error\"><span>Please select Chapter No</span></div>";

	if(empty($msg)) 
	{	
				
				$addChapterId = $_POST['chapterIdList'];
				$addSubChapterNo = $_POST['addSubChapterNo'];
				$addSubChapterDesc = $_POST['addSubChapterDesc'];
				$curdatetime = date("Y-m-d H:i:s");	
				$subChapterId = runnum('id','pg_subchapter');
	
				$sql6=" INSERT INTO pg_subchapter
				(id, chapter_id, subchapter_no, description, student_matrix_no, status, insert_by, insert_date, modify_by, modify_date)
				VALUES ('$subChapterId', '$addChapterId', '$addSubChapterNo', '$addSubChapterDesc', '$matrixNo', 'A', '$matrixNo', '$curdatetime', 		'$matrixNo', '$curdatetime') ";
				
				$dbb->query($sql6); 
				echo '<i style="color:black;font-size:15px;font-family:calibri ;"> Add Sub-Chapter Success! </i> ';
  			
	}	
}

if(isset($_POST['btnDelSubChapter']) && ($_POST['btnDelSubChapter'] <> ""))
{
	$chapterBox=$_POST['chapterBox'];
	
	while (list ($key,$val) = @each ($chapterBox)) 
	{
		
		$sql8=" DELETE 
				FROM pg_subchapter
				WHERE id = '$subChapterId[$val]'";
				
		$dbb->query($sql8); 
	}
}

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
	<?php
    if(!empty($msg)) {
        foreach($msg as $err) {
            echo $err;
        }
    }
?>
<script>
/*
	$(document).ready(function() {         
		
		$('.error').hide();
		var showError = <?php //echo $msg; ?>;         
		if (showError) {             
			$('.error').fadeIn(500).delay(5000).fadeOut(500);
			alert("introduction: " + document.form1.introduction.value);
			$msg.focus();       
		} else {
			
		}
	
	});*/
	$(document).ready(function() {
$('#add-job').validate({
    ignore: [],         
    rules: {
                introduction: {
                    required: function() 
                    {
                    CKEDITOR.instances.introduction.updateElement();
					
					$msg.focus();       
                    }
                    }
                },
                messages: {
                thesis_title: "Required",
                objective: "Required",
                description: "Required",
                //Job_Cat: "Required",
                //editor1: "Required"
                },
                /* use below section if required to place the error*/
                errorPlacement: function(error, element) 
                {
                    if (element.attr("name") == "introduction") 
                   {
                    error.insertBefore("textarea#introduction");
                    } else {
                    error.insertBefore(element);
                    }
                }
				
            });
			$('.error').fadeIn(500).delay(1000).fadeOut(500);
			$('.success').fadeIn(500).delay(1000).fadeOut(500);
});
</script>
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
		</table>
		<br/>
			
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
						<td><label>Sub-Chapter No</label></td>
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