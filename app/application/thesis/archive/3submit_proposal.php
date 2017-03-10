<?php

//include("../../../lib/common.php");
//checkLogin();

session_start();
$thesis_id=$_SESSION["thesis_id"];
$proposal_id=$_SESSION["proposal_id"];
$userid=$_SESSION['user_id'];

function convertname($user_id)
{
	global $db;
	$sql_login = "SELECT name FROM new_employee WHERE empid='$user_id' union SELECT name FROM student WHERE matrix_no='$user_id'";
	$db->query($sql_login);
	$db->next_record();
	$rows = $db->rowdata();
	$name = $rows['name'];
	return $name;
}

function runnum($column_name, $tblname) 
{ 
    global $db;
    
    $run_start = "001";
    
    $sql_slct_max = "SELECT MAX(SUBSTR($column_name,2,11)) AS run_id FROM $tblname";
    $sql_slct = $db;
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

function runnum2($column_name, $tblname) 
{ 
    global $db;
    
    $run_start = "001";
    
    $sql_slct_max = "SELECT MAX($column_name) AS run_id FROM $tblname";
    $sql_slct = $db;
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

if(isset($_POST['btnSave']) && ($_POST['btnSave'] <> ""))
{		
	$thesis_title = $_POST['thesis_title'];
	$thesis_type = $_POST['thesis_type'];
	$introduction = $_POST['introduction'];
	$objective = $_POST['objective'];
	$description = $_POST['description'];
	
	/*$jobs_area1 = $_POST['jobs_area1'];
	$jobs_area2 = $_POST['jobs_area2'];
	$jobs_area3 = $_POST['jobs_area3'];
	$jobs_area4 = $_POST['jobs_area4'];
	$jobs_area5 = $_POST['jobs_area5'];
	$jobs_area6 = $_POST['jobs_area6'];*/
	
	$jobs_area1 = $_REQUEST['JobAreaID'];
	$jobs_area2 = $_REQUEST['JobAreaID1'];
	$jobs_area3 = $_REQUEST['JobAreaID2'];
	$jobs_area4 = $_REQUEST['JobAreaID3'];
	$jobs_area5 = $_REQUEST['JobAreaID4'];
	$jobs_area6 = $_REQUEST['JobAreaID5'];

	
	$curdatetime = date("Y-m-d H:i:s");
	$meeting_detail_id = runnum2('id','pg_meeting_detail');
	
	$sqlUpdate1 = "UPDATE pg_thesis
				SET modify_by = '$user_id', modify_date = '$curdatetime', ref_thesis_status_id_proposal = 'INP'
				WHERE id = '$thesis_id'";

	$db->query($sqlUpdate1);
	
	$sqlUpdate2 = "UPDATE pg_proposal
				SET thesis_title = '".mysql_real_escape_string($thesis_title)."', thesis_type = '$thesis_type', introduction = '$introduction', objective = '$objective', 
				description = '$description', modify_by = '$user_id', modify_date = '$curdatetime', discussion_status = '$discussion_status',
				report_date = '$curdatetime'
				WHERE id = '$proposal_id'
				AND verified_status in ('SAV')";
				
	$db->query($sqlUpdate2);
	
		// --- Job Area Category (START) ---
		$selectArea = "SELECT * FROM pg_proposal_area
			WHERE pg_proposal_id = '$proposal_id' ";
			$db_klas2->query($selectArea); //echo $updateArea;
			
			$result_selectArea = $db_klas2->query($selectArea);	
			$row_cnt = mysql_num_rows($result_selectArea);	
			
			if ($row_cnt > 0) {

				$updateArea = "UPDATE pg_proposal_area
				SET job_id1_area = '$jobs_area1', job_id2_area = '$jobs_area2', job_id3_area = '$jobs_area3',
					job_id4_area = '$jobs_area4',job_id5_area = '$jobs_area5',job_id6_area = '$jobs_area6',
					insert_date = '$curdatetime', insert_by = '".$_SESSION['user_id']."', modified_date = '$curdatetime', modified_by = '".$_SESSION['user_id']."'
				WHERE pg_proposal_id = '$proposal_id' ";
				$db_klas2->query($updateArea); //echo $updateArea;
			}
			else {
				$job_area_id = runnum2('id','pg_proposal_area');
				$insertArea = "INSERT INTO pg_proposal_area
				(id, pg_proposal_id, job_id1_area, job_id2_area, job_id3_area, job_id4_area, job_id5_area, job_id6_area, insert_date, insert_by,
				modified_date, modified_by)
				VALUES('$job_area_id', '$proposal_id', '$jobs_area1', '$jobs_area2', '$jobs_area3', '$jobs_area4', '$jobs_area5', '$jobs_area6',
				'$curdatetime', '".$_SESSION['user_id']."', '$curdatetime', '".$_SESSION['user_id']."')";
				$db_klas2->query($insertArea);
			}

		// --- Job Area Category (FINISH) ---
	
		
	for ($i=0; $i<sizeof($_POST['date']); $i++) {
	
	$myMeetingDate = $_POST['date'][$i]." ".$_POST['meeting_time'][$i];
	$myLecturer = mysql_real_escape_string($_POST['lecturer_name'][$i]);
	$myRemarks = mysql_real_escape_string($_POST['remarks'][$i]);
	
	$meeting_detail_id = runnum2('id','pg_meeting_detail');	

	
	$sqlUpdate3 = "INSERT INTO pg_meeting_detail (id,lecturer_name,meeting_sdate,remark,pg_proposal_id,
					insert_by,insert_date,modify_by,modify_date)
					VALUES ('$meeting_detail_id','$myLecturer',
					STR_TO_DATE('$myMeetingDate','%m/%d/%Y %h:%i'),'$myRemarks',
			'$proposal_id','$user_id','$curdatetime','$user_id','$curdatetime')";
	

	$db->query($sqlUpdate3); 			 
	}
	for ($i=0; $i<sizeof($_FILES['fileData']['name']); $i++) 
	{
		$upload_id = runnum2('fu_cd','file_upload_proposal');
		
		$file_name = $_FILES['fileData']['name'][$i];
		$fileType = $_FILES['fileData']['type'][$i];
		$fileSize = intval($_FILES['fileData']['size'][$i]);
		$fileData = file_get_contents($_FILES['fileData']['tmp_name'][$i]);
		if ($fileSize>0)
		{
			$sqlUpload = "INSERT INTO file_upload_proposal (
							fu_cd, 
							fu_document_filename, 
							fu_document_filetype, 
							fu_document_filedata,
							fu_document_thumbnail,
							insert_by,
							insert_date,
							modify_by,
							modify_date,
							pg_proposal_id,
							attachment_level)
							VALUES (
							'$upload_id',
							'".$file_name."', 
							'".$fileType."',
							'".mysql_escape_string($fileData)."',
							'',
							'$user_id',
							'$curdatetime',
							'$user_id',
							'$curdatetime',
							'$proposal_id',
							'S')";

			$db->query($sqlUpload);
		}
					
	}			

}
	
if(isset($_POST['btnSubmit']) && ($_POST['btnSubmit'] <> ""))
{			
		
	$thesis_title = $_POST['thesis_title'];
	$thesis_type = $_POST['thesis_type'];
	$introduction = $_POST['introduction'];
	$objective = $_POST['objective'];
	$description = $_POST['description'];
	
	/*$jobs_area1 = $_POST['jobs_area1'];
	$jobs_area2 = $_POST['jobs_area2'];
	$jobs_area3 = $_POST['jobs_area3'];
	$jobs_area4 = $_POST['jobs_area4'];
	$jobs_area5 = $_POST['jobs_area5'];
	$jobs_area6 = $_POST['jobs_area6'];*/
	
	$jobs_area1 = $_REQUEST['JobAreaID'];
	$jobs_area2 = $_REQUEST['JobAreaID1'];
	$jobs_area3 = $_REQUEST['JobAreaID2'];
	$jobs_area4 = $_REQUEST['JobAreaID3'];
	$jobs_area5 = $_REQUEST['JobAreaID4'];
	$jobs_area6 = $_REQUEST['JobAreaID5'];

	
	$curdatetime = date("Y-m-d H:i:s");
	$meeting_detail_id = runnum2('id','pg_meeting_detail');
	
	$sqlUpdate1 = "UPDATE pg_thesis
				SET modify_by = '$user_id', modify_date = '$curdatetime', ref_thesis_status_id_proposal = 'INP'
				WHERE id = '$thesis_id'";

	$db->query($sqlUpdate1);
	
	$sqlUpdate2 = "UPDATE pg_proposal
				SET thesis_title = '".mysql_real_escape_string($thesis_title)."', thesis_type = '$thesis_type', introduction = '$introduction', objective = '$objective', 
				description = '$description', modify_by = '$user_id', modify_date = '$curdatetime', discussion_status = '$discussion_status',
				report_date = '$curdatetime', verified_status = 'INP'
				WHERE id = '$proposal_id'
				AND verified_status in ('SAV')";

	$db->query($sqlUpdate2);
	
	
	// --- Job Area Category (START) ---
		$selectArea = "SELECT * FROM pg_proposal_area
			WHERE pg_proposal_id = '$proposal_id' ";
			$db_klas2->query($selectArea); //echo $updateArea;
			
			$result_selectArea = $db_klas2->query($selectArea);	
			$row_cnt = mysql_num_rows($result_selectArea);	
			
			if ($row_cnt > 0) {

				$updateArea = "UPDATE pg_proposal_area
				SET job_id1_area = '$jobs_area1', job_id2_area = '$jobs_area2', job_id3_area = '$jobs_area3',
					job_id4_area = '$jobs_area4',job_id5_area = '$jobs_area5',job_id6_area = '$jobs_area6',
					insert_date = '$curdatetime', insert_by = '".$_SESSION['user_id']."', modified_date = '$curdatetime', modified_by = '".$_SESSION['user_id']."'
				WHERE pg_proposal_id = '$proposal_id' ";
				$db_klas2->query($updateArea); //echo $updateArea;
			}
			else {
				$job_area_id = runnum2('id','pg_proposal_area');
				$insertArea = "INSERT INTO pg_proposal_area
				(id, pg_proposal_id, job_id1_area, job_id2_area, job_id3_area, job_id4_area, job_id5_area, job_id6_area, insert_date, insert_by,
				modified_date, modified_by)
				VALUES('$job_area_id', '$proposal_id', '$jobs_area1', '$jobs_area2', '$jobs_area3', '$jobs_area4', '$jobs_area5', '$jobs_area6',
				'$curdatetime', '".$_SESSION['user_id']."', '$curdatetime', '".$_SESSION['user_id']."')";
				$db_klas2->query($insertArea);
			}

		// --- Job Area Category (FINISH) ---
	
	for ($i=0; $i<sizeof($_POST['date']); $i++) {
	
	$myMeetingDate = $_POST['date'][$i]." ".$_POST['meeting_time'][$i];
	$myLecturer = mysql_real_escape_string($_POST['lecturer_name'][$i]);
	$myRemarks = mysql_real_escape_string($_POST['remarks'][$i]);
	
	$meeting_detail_id = runnum2('id','pg_meeting_detail');	
	
	$sqlUpdate3 = "INSERT INTO pg_meeting_detail (id,lecturer_name,meeting_sdate,remark,pg_proposal_id,
					insert_by,insert_date,modify_by,modify_date)
					VALUES ('$meeting_detail_id','$myLecturer',
					STR_TO_DATE('$myMeetingDate','%m/%d/%Y %h:%i'),'$myRemarks',
			'$proposal_id','$user_id','$curdatetime','$user_id','$curdatetime')";

	$db->query($sqlUpdate3); 			 
	}
	for ($i=0; $i<sizeof($_FILES['fileData']['name']); $i++) 
	{
		$upload_id = runnum2('fu_cd','file_upload_proposal');
		
		$file_name = $_FILES['fileData']['name'][$i];
		$fileType = $_FILES['fileData']['type'][$i];
		$fileSize = intval($_FILES['fileData']['size'][$i]);
		$fileData = file_get_contents($_FILES['fileData']['tmp_name'][$i]);
		if ($fileSize>0)
		{
			$sqlUpload = "INSERT INTO file_upload_proposal (
							fu_cd, 
							fu_document_filename, 
							fu_document_filetype, 
							fu_document_filedata,
							fu_document_thumbnail,
							insert_by,
							insert_date,
							modify_by,
							modify_date,
							pg_proposal_id,
							attachment_level)
							VALUES (
							'$upload_id',
							'".$file_name."', 
							'".$fileType."',
							'".mysql_escape_string($fileData)."',
							'',
							'$user_id',
							'$curdatetime',
							'$user_id',
							'$curdatetime',
							'$proposal_id',
							'S')";

			$db->query($sqlUpload);
		}
					
	}			
	$selectfrom = "SELECT const_value
	FROM base_constant WHERE const_term = 'EMAIL_ADMIN'";
	$resultfrom = $db->query($selectfrom);
	$db->next_record();
	$fromadmin =$db->f('const_value');
	
	$selectto = "SELECT const_value
	FROM base_constant WHERE const_term = 'EMAIL_FACULTY'";
	$resultto = $dbb->query($selectto);
	$dbb->next_record();
	$tofaculty =$dbb->f('const_value');
	
	$selectfaculty = "SELECT const_value
	FROM base_constant WHERE const_term = 'FACULTY_STAFF_ID'";
	$resultselectfaculty = $dbe->query($selectfaculty);
	$dbe->next_record();
	$selectidfaculty =$dbe->f('const_value');
	
	$sqlemail = "SELECT email FROM student
	WHERE `matrix_no` = '$user_id'";
	$resultreceive = $dbk->query($sqlemail);
	$resultsqlreceive = $dbk->next_record(); 
	$receiveemail = $dbk->f('email');
	
	$selectfalname = "SELECT a.name,a.email,b.title 
	FROM new_employee a
	LEFT JOIN lookup_gelaran b ON(b.id = a.title)
	WHERE a.empid = '$selectidfaculty'";
	$resultselfalname = $dbc->query($selectfalname);
	$dbc->next_record();
	$selectname =$dbc->f('name');
	$title =$dbc->f('title');

	$curdatetime1 = date("d-m-Y");	
						
	$username = convertname($user_id);
	if ($thesis_type =  "R")
	{
		$type = "Research";
		
	}
	else if ($thesis_type =  "C")
	{
		$type = "Case Study";

	}
	else
	{
		$type = "Project";
	}

	$selectattachment= "SELECT *
	FROM file_upload_proposal WHERE student_matrix_no = '$user_id' 
	AND pg_proposal_id = '$proposal_id'";
	$resultattachment = $dbj->query($selectattachment);
	
	while($dbj->next_record())
	{
		$rowData = $dbj->rowdata();
		$FileName[] = $rowData['fu_document_filename'];
		$FileType[] = $rowData['fu_document_filetype'];
		$attachmentdata[] = $rowData['fu_document_filedata'];		 
	}
						 
	$sqlvalidate = "SELECT const_value
	FROM base_constant WHERE const_term = 'EMAIL_STU_TO_FAC'";
	$resultvalidate = $dbd->query($sqlvalidate);
	$dbd->next_record();
	$valid =$dbd->f('const_value');
	
	if($valid == 'Y')
	{
		include("../../../app/application/email/email_new_proposal.php");
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
	<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
	<script src="../../../lib/js/jquery.min2.js"></script>
	<script src="../../../lib/js/jquery.colorbox.js"></script>
	<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
    <script>

		$(document).ready(function(){
		      //$(".select_user").colorbox({width:"60%", height:"40%", iframe:true});
              
              $.fn.getParameterValue = function(data) {
                  //alert(data);
                  document.form1.JobArea.value = data;
                };
              
               $(".select_job").colorbox({width:"80%", height:"90%", iframe:true,          
               onClosed:function(){ 
                //location.reload(true); //uncomment this line if you want to refresh the page when child close
                                
                } }); 
				
				$.fn.getParameterValue2 = function(data,data2,data3,data4) {
                  //alert(data);
                  //document.form1.JobArea.value = data;
				  $("#"+data2).val(data);
				  $("#"+data4).val(data3);

                };
              
               $(".select_job").colorbox({width:"80%", height:"90%", iframe:true,          
               onClosed:function(){ 
                //location.reload(true); //uncomment this line if you want to refresh the page when child close
                                
                } }); 
          });

	</script>

</head>
<body>

<script>
function saveRec()
{
	saveStatus=true;
	var introduction = CKEDITOR.instances['introduction'].getData();
	var objective = CKEDITOR.instances['objective'].getData();
	var description = CKEDITOR.instances['description'].getData();
	
	if(document.form1.thesis_title.value=="")
	{
		alert("Please enter your Thesis / Project title.");
		return false;
	}
	
	if(document.form1.thesis_type.value=="")
	{
		alert("Please enter your Thesis / Project proposal type.");
		return false;
	}
	
	if(introduction.length==0)
	{
		alert("Please enter your thesis / project introduction.");
		return false;
	}
	
	if(objective.length==0)
	{
		alert("Please enter your thesis / project objective.");
		return false;
	}
	
	if(description.length==0)
	{
		alert("Please enter your thesis / project description");
		return false;
	}

	return saveStatus;
}					
</script>

<SCRIPT LANGUAGE="JavaScript">

function respConfirm () {
    var confirmSubmit = confirm("Are you confirm to submit?");
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


<?
$sql_thesis="SELECT pt.id AS thesis_id, pt.student_matrix_no,pt.status AS thesis_status,
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
				AND pp.verified_status in ('SAV','INP','APP','AWC','REQ','DIS','REV','WIT','CAN')				
				AND pp.archived_status is null
				AND pt.ref_thesis_status_id_proposal in ('INP','APP','AWC','APC','DIS')
				ORDER BY pt.id";
//echo "sql_thesis ".$sql_thesis;
$db->query($sql_thesis);

$row_personal=$db->fetchArray();

$thesis_id=$row_personal['thesis_id'];
$student_matrix_no=$row_personal['student_matrix_no'];
$thesis_status=$row_personal["thesis_status"];
$proposal_id=$row_personal['proposal_id'];
$thesis_title=$row_personal["thesis_title"];
$thesis_type=$row_personal["thesis_type"];
$objective=$row_personal["objective"];
$introduction=$row_personal["introduction"];
$description=$row_personal["description"];
$discussion_status=$row_personal["discussion_status"];
$verified_by=$row_personal['verified_by'];
$verified_date=$row_personal['verified_date'];
$verified_remarks=$row_personal['verified_remarks'];
$proposal_status=$row_personal["proposal_status"];
$endorsed_by=$row_personal['endorsed_by'];
$endorsed_date=$row_personal['endorsed_date'];
$endorsed_remarks=$row_personal['endorsed_remarks'];
$endorsed_status=$row_personal['endorsed_status'];
$endorsed_desc=$row_personal['endorsed_desc'];
$proposal_description=$row_personal["proposal_description"];


$cancel_requested_date=$row_personal['cancel_requested_date'];
$cancel_approved_date=$row_personal['cancel_approved_date'];

$cancel_approved_by=$row_personal['cancel_approved_by'];
$cancel_approved_name=$row_personal['cancel_approved_name'];
$cancel_approved_remarks=$row_personal['cancel_approved_remarks'];


?>

<script>
$(function() {
	$( "#datepickerFirst" ).datepicker({
		changeMonth: true,
		changeYear: true,
		yearRange: '-100:+0'
		});
});
</script>

<script type="text/javascript">
$(function() {

	var i = $('input').size() + 1;
	
	//###################################### funtion add more document files ######################################//
	$('a.add-file').click(function() {

		$('<tr><td width="40"><input type="checkbox" class="case_file" /></td><td width="30"><input type=\"text\" name=\"file_name[]\" size="40" /></td><td width="30"><input name="fileData[]" type="file" size="40"/></td><td width="30"><label size="30"></label></td></tr>').animate({ opacity: "show" }, "slow").appendTo('#inputs9');
		i++;
	});
	
	$('a.remove-file').click(function() {
        $(".case_file:checked").each(function() {
            $(this).parent().parent().remove()
        });
    });
    
    // add multiple select / deselect functionality
    $("#selectall_file").click(function () {
          $('.case_file').attr('checked', this.checked);
    });
 
    // if all checkbox are selected, check the selectall checkbox and viceversa
    $(".case_file").click(function(){
 
        if($(".case_file").length == $(".case_file:checked").length) {
            $("#selectall_file").attr("checked", "checked");
        } else {
            $("#selectall_file").removeAttr("checked");
        }
 
    });
	
	//###################################### end of funtion add more document files ######################################//
	
});

</script>	
					
<script type="text/javascript">
$(function() 
{
	var i = $('input').size() + 1;
	
	//###################################### funtion add more @ certification ######################################//
	$('a.add-certification').click(function() {

	$('<tr><td width="26" align="center"><input type="checkbox" name="cbDelFile2" class="case_certificate" /></td><td align="center" width="77" ><input type="text" name="date[]" readonly id="datepicker'+i+'" /></td><td width="88" height="1" class="tbmain"><select name="meeting_time[]" size="1" ><option value="" selected></option><option value="07:00">07:00 AM</option><option value="07:30">07:30 AM</option><option value="08:00">08:00 AM</option><option value="08:30">08:30 AM</option><option value="09:00">09:00 AM</option><option value="09:30">09:30 AM</option><option value="10:00">10:00 AM</option><option value="10:30">10:30 AM</option><option value="11:00">11:00 AM</option><option value="11:30">11:30 AM</option><option value="12:00">12:00 PM</option><option value="12:30">12:30 PM</option><option value="01:00">01:00 PM</option><option value="01:30">01:30 PM</option><option value="02:00">02:00 PM</option><option value="02:30">02:30 PM</option><option value="03:00">03:00 PM</option><option value="03:30">03:30 PM</option><option value="04:00">04:00 PM</option><option value="04:30">04:30 PM</option><option value="05:00">05:00 PM</option><option value="05:30">05:30 PM</option><option value="06:00">06:00 PM</option><option value="06:30">06:30 PM</option><option value="07:00">07:00 PM</option><option value="07:30">07:30 PM</option><option value="08:00">08:00 PM</option><option value="08:30">08:30 PM</option><option value="09:00">09:00 PM</option><option value="09:30">09:30 PM</option><option value="10:00">10:00 PM</option><option value="10:30">10:30 PM</option><option value="11:00">11:00 PM</option><option value="11:30">11:30 PM</option><option value="12:00">12:00 PM</option></select></td><td width="170"><input type="text" name="lecturer_name[]"/></td><td width="257"><textarea name="remarks[]" cols="50" id="remarks"></textarea></td><td width="29"><label></label></td></tr>').animate({ opacity: "show" }, "slow").appendTo('#inputs10');

	$("#datepicker"+i).datepicker({
		changeMonth: true,
		changeYear: true,
		yearRange: '-100:+0'
		});

		i++;		
	});
    
	
	$('a.remove-certification').click(function() {
        $(".case_certificate:checked").each(function() {
            $(this).parent().parent().remove()
        });	
    });
    
    // add multiple select / deselect functionality
    $("#selectall_certificate").click(function () 
	{
          $('.case_certificate').attr('checked', this.checked);
    });
 
    // if all checkbox are selected, check the selectall checkbox and viceversa
    $(".case_certificate").click(function()
	{
		if($(".case_certificate").length == $(".case_certificate:checked").length) 
		{
            $("#selectall_certificate").attr("checked", "checked");
        } else {
            $("#selectall_certificate").removeAttr("checked");
        }
    });
	//###################################### end of funtion add more @ certification ######################################//
});
</script>

<script>
function getUpdSP(id,frm)
{
	// START EDIT BY AD 2014-08-25
	var winMe = window.open("edit_proposal.php?thesis_id="+id+"&form="+frm,"aform","dependent=no,width=900,height=900,resizable=yes,scrollbars=yes");
	winMe.focus();
}
</script>
	<form id="form1" name="form1" method="post" enctype="multipart/form-data">
	<input type="hidden" name="thesis_id" id="thesis_id" value="<?=$thesis_id; ?>">
  
	
<?
	$result_sql_thesis=$db->query($sql_thesis);
	$row_cnt = mysql_num_rows($result_sql_thesis);
	//echo "sql_thesis ".$sql_thesis;
	//echo "proposal_status ".$proposal_status; 
	if ($row_cnt>0) 
	{//echo "has record";
		if ($proposal_status=='APP' || $proposal_status=='AWC') 
		{
	
			?>
				<fieldset>
				<legend><strong>Verification by Faculty</strong></legend>
					<table>
						<tr>
							<td>Proposal Status</td>
							<td>:</td>
							<td><?=$proposal_description;?></td>
						</tr>
						<tr>
							<td>Verification Date</td>
							<td>:</td>
							<td><label><?=$verified_date; ?></label></td>
						</tr>
						<?
						$sql1="SELECT name AS verified_name
							FROM new_employee
							WHERE empid = '$verified_by'";
							
							$dbc->query($sql1);
							$row_personal=$dbc->fetchArray();
							$verified_name=$row_personal['verified_name'];
						?>
						<tr>
							<td>Verified By</td>
							<td>:</td>
							<td><label><?=$verified_name; ?></label></td>
						</tr>
						<tr>
							<td> Remarks </td>
							<td>:</td>
							<td><label class="ckeditor"><?=$verified_remarks;?></label></td>							
						</tr>
						<tr>
							<td> Attachment by Faculty </td>
							<td>:</td>							
							<?php
									$sqlUpload="SELECT * FROM file_upload_proposal 
									WHERE pg_proposal_id='$proposal_id' 
									AND attachment_level='F' ";			

									$result = $db->query($sqlUpload); //echo $sql;
									$row_cnt = mysql_num_rows($result);
									if ($row_cnt>0)
									{
										?><td align="left"><?
										while($row = mysql_fetch_array($result)) 					
										{ 
											?>
												<a href="../thesis/download.php?fc=<?=$row["fu_cd"];?>&al=F"><?=$row["fu_document_filename"];?><img src="../images/download.png" width="20" height="19" style="border:0px;" title="Download"></a><br/>	
										<?}										
									}
									else {
										?><td>No attachment<br/></td><?
									}
								?>
						</tr>
					</table>
				</fieldset>
					<br/>
				<fieldset>
				<legend><strong>Endorsement by Senate</strong></legend>
					<table>
						<tr>
							<td>Endorsement Status</td>							
							<td>:</td>
							<td><label><?=$endorsed_desc; ?></label></td>
						</tr>
						<tr>
							<td>Endorsement Date</td>
							<td>:</td>
							<?if ($endorsed_date=='00-00-00'){
							?>
								<td><label></label></td>
							<?	
							}
							else 
							{
							?>
								<td><label><?=$endorsed_date; ?></label></td>
							<?
							}?>
							
						</tr>
						<?
						$sql2="SELECT name AS endorsed_name
						FROM new_employee 
							WHERE empid = '$endorsed_by'";
							
							$dbc->query($sql2);
							$row_personal=$dbc->fetchArray();
							$endorsed_name=$row_personal['endorsed_name'];
						?>
						<tr>
							<td>Endorsed By</td>
							<td>:</td>
							<td><label><?=$endorsed_name; ?></label></td>
						</tr>
						<tr>
							<td> Remarks </td>
							<td>:</td>
							<td><label class="ckeditor"><?=$endorsed_remarks;?></label></td>							
						</tr>						
					</table>
					</fieldset>
					<br/>
					<table>
						<tr>
							<?if (($proposal_status=='APP' || $proposal_status=='AWC') && ($endorsed_status=='APP' || $endorsed_status=='APC'))
							{?>
								<td><strong>Note:</strong> Your thesis proposal has been approved by the Senate. You may need to <a href="../monthlyreport/submit_progress.php"><img src="../images/click_here.jpg" width="60" height="30" style="border:0px;" title="Prepare and submit monthly progress report"></a> to prepare and then submit your monthly progress report.<br/>
								You can <a href="../thesis/view_proposal.php?tid=<?=$thesis_id;?>&pid=<?=$proposal_id;?>"><img src="../images/proposal.jpg" width="60" height="40" style="border:0px;" title="View Proposal Here">click here </a>to view your approved proposal.</td>
							<?}
							elseif (($proposal_status=='APP' || $proposal_status=='AWC') && $endorsed_status=='DIS') {
							?>
								<td><strong>Note:</strong> Your thesis proposal has been disapproved by the Senate. You may need to  <a href="../thesis/new_proposal.php?uid=<?php echo $userid;?>&pid=<?php echo $proposal_id;?>&tid=<?php echo $thesis_id;?>"><img src="../images/click_here.jpg" width="60" height="30" style="border:0px;" title="Plase click here"></a> to create another one and re-submit.</td>
							<?}
							else{
							?>
								<td><strong>Note:</strong> Your thesis proposal is pending for endorsement by the Senate. Please check it again later. </td>
							<?}?>
							
						</tr>
					</table>
			<?
		
		}	
		else if ($proposal_status=='WIT'){?>
				<fieldset>
				<legend><strong><span style="color:#FF0000">Notification Message</span></strong></legend>	
				<table>
						<tr>
							<td>Your thesis proposal <strong>(Ref. No.: <?=$thesis_id?>)</strong> is pending with Faculty for cancellation approval.</td>
						</tr>
					</table>
				</fieldset>
		<?	}
		else if ($proposal_status=='INP'){?>
				<fieldset>
				<legend><strong><span style="color:#FF0000">Notification Message</span></strong></legend>	
				<table>
						<tr>
							<td>Your thesis proposal <strong>(Ref. No.: <?=$thesis_id?>)</strong> is pending with Faculty for verification.<br/><br/>
							You can request for withdraw or cancel it before the Faculty start to review and provide the feedback.<br/>
							Please click here <a href="../thesis/request_cancel_proposal.php?tid=<?=$thesis_id;?>&pid=<?=$proposal_id;?>" >
													<img src="../images/cancel.jpg" width="40" height="40" style="border:0px;" title="Request cancellation for thesis proposal submission"></a>if you want to proceed.</td>
						</tr>
					</table>
				</fieldset>
		<?	}
		else if ($proposal_status=='REQ')
		{ 
			
			?>
		 <fieldset>
		 <legend><strong>Verification by Faculty</strong></legend>
			<table>
				<tr>
					<td>Proposal Status</td>
					<td>:</td>
					<td><?=$proposal_description;?></td>
				</tr>
				<tr>
					<td>Verified Date</td>
					<td>:</td>
					<td><label><?=$verified_date; ?></label></td>
				</tr>
				<?
						$sql1="SELECT name AS verified_name
							FROM new_employee
							WHERE empid = '$verified_by'";
							
							$dbc->query($sql1);
							$row_personal=$dbc->fetchArray();
							$verified_name=$row_personal['verified_name'];
						?>
				<tr>
					<td>Verified By</td>
					<td>:</td>
					<td><label><?=$verified_name; ?></label></td>
				</tr>
				<tr>
					<td> Remarks </td>
					<td>:</td>
					<td><label class="ckeditor"><?=$verified_remarks; ?></label></td>
				</tr>
				<tr>
					<tr>
							<td> Attachment by Faculty </td>
							<td>:</td>							
							<?php
									$sqlUpload="SELECT * FROM file_upload_proposal 
									WHERE pg_proposal_id='$proposal_id' 
									AND attachment_level='F' ";			

									$result = $db->query($sqlUpload); //echo $sql;
									$row_cnt = mysql_num_rows($result);
									if ($row_cnt>0)
									{
										?><td align="left"><?
										while($row = mysql_fetch_array($result)) 					
										{ 
											?>
												<a href="../thesis/download.php?fc=<?=$row["fu_cd"];?>&al=F"><?=$row["fu_document_filename"];?><img src="../images/download.png" width="20" height="19" style="border:0px;" title="Download"></a><br/>	
										<?}										
									}
									else {
										?><td>No attachment<br/></td><?
									}
								?>
				</tr>
			</table>
			<br/>
			</fieldset>
			<table>
				<tr>
					<td><strong>Note: </strong>Your thesis proposal has been reviewed by the Faculty and require changes. Please <a href="../thesis/edit_proposal.php?id=<?=$thesis_id;?>&pid=<?=$proposal_id;?>"><img src="../images/click_here.jpg" width="60" height="30" style="border:0px;" title="Please click here"></a> to update it and re-submit.</td>
				</tr>
			</table>					
		
			<?
				
		}
		else if ($proposal_status=='SAV'){?>
		
			
			<table>
				<tr>
					<td><strong>Notes:</strong><td></td><td>
				</tr>
				<tr>
					<td>(1)</td><td>This form should be submitted to MSU Graduate School of Management (GSM) upon completing of the Research Methodology and before student starts the project.</td>
				</tr>
				<tr>
					<td>(2)</td><td>Students are advised to seek the lecturer's advice before proceeding with the proposal.</td>
				</tr>
				<tr>
					<td>(3)</td><td>Student should plan on 6-month's time from the Official Approval Date to complete the Final Project.</td>
				</tr>
				<tr>
					<td>(4)</td><td>As refer to MBA rules, No candidate with CGPA below 3.0 shall be eligible to register for the Final Project of the degree unless recommended by the Board of Examiners.</td>
				</tr>
				<tr>
					<td>(5)</td><td>Appointment of supervisor is subject to the recommendation from the Director of MSU Graduate School of Management (GSM).</td>
				</tr>				
			</table>
			<br/>		   
			<fieldset>
				<legend><strong>Edit Outline of Proposed Research/Case Study.</strong></legend>	
			<table>
					<table>
						<tr>
							<td>Verification Status by Faculty</td>
							<td>:</td>
							<td><label><?=$proposal_description; ?></label></td>
						</tr>
						<tr>
							<td>Thesis ID</td>
							<td>:</td>
							<td><label><?=$thesis_id; ?></label></td>
						</tr>
						<tr>
							<td><span style="color:#FF0000">*</span> Thesis / Project Title</td>
							<td>:</td>
							<td><input type="text" name="thesis_title" size="100" maxlength="100"  id="thesis_title" value="<?=$thesis_title;?>"></td>
						</tr><tr>
							<td>Proposal Type</td>
							<td>:</td>
							<td>
								<?php if($thesis_type=='R')	{	?>
								<input type="radio" name="thesis_type" value="R" checked>Research
								<input type="radio" name="thesis_type" value="C">Case Study						
								<input type="radio" name="thesis_type" value="P">Project	
								<?php	}	else if ($thesis_type=='C')	{	?>
									<input type="radio" name="thesis_type" value="R">Research
									<input type="radio" name="thesis_type" value="C" checked >Case Study
									<input type="radio" name="thesis_type" value="P">Project
								<?php	}	else if ($thesis_type=='P')	{	?>
									<input type="radio" name="thesis_type" value="R">Research
									<input type="radio" name="thesis_type" value="C">Case Study
									<input type="radio" name="thesis_type" value="P" checked>Project
								<?php	}	else {	?>
									<input type="radio" name="thesis_type" value="R" checked>Research
									<input type="radio" name="thesis_type" value="C">Case Study
									<input type="radio" name="thesis_type" value="P">Project
								<?php	}
								?> 
							</td>	
						</tr>
						<tr>
							<td><span style="color:#FF0000">*</span> Introduction</td>
							<td></td>
							<td><textarea name="introduction" class="ckeditor"><?=$introduction; ?></textarea></td>
						</tr>
						<tr>
							<td><span style="color:#FF0000">*</span> Objective</td>
							<td></td>
							<td><textarea name="objective" class="ckeditor" ><?=$objective; ?></textarea></td>
						</tr>
						<tr>
							<td><span style="color:#FF0000">*</span> Brief Description</td>
							<td></td>
							<td><textarea name="description" class="ckeditor" > <?=$description; ?></textarea></td>
						</tr>
					</table>
					 <input type="hidden" name="discussion_status" id="discussion_status" value="Y" />

		<? 
		/*$count_total_result = "SELECT COUNT(*) AS total FROM pg_meeting_detail 
								WHERE pg_proposal_id = '$proposal_id' 
								AND student_matrix_no = '$user_id'";
		$db->query($count_total_result);
		$db->next_record();
    	$a = $db->f('total');
		if ($a == '0')
		{
			$b = '';
		}
		else
		{
			$b = $a;
		}*/					
		
		?>
											
	   <? ?>
											
	   <? $sqlPgProposalArea = "SELECT pg_proposal_id,job_id1_area,job_id2_area,job_id3_area,job_id4_area,job_id5_area,job_id6_area
	 										FROM pg_proposal_area
											WHERE pg_proposal_id = '$proposal_id'"; ?>									
	   <fieldset style="width:800px">
	   <legend><strong>Thesis Areas</strong></legend>	
	  
	  	<table width="845"  align="center">
		<tr>
			<td width="53" nowrap><font color="#FF0000">*</font><b>Area 1</b></td>
			<td width="294"><?php
			$rsPgProposalArea = $dbf->query($sqlPgProposalArea);						
			$dbf->next_record();
			$jobArea1=$dbf->f('job_id1_area');

		
			if ($jobArea1 == "") $jobArea1 = $_POST['JobArea'];
			$sqlLookupJobArea = "SELECT jobarea, area
								FROM job_list_category
								WHERE jobarea = '$jobArea1'";
			
			$rsLookupJobArea = $dbc->query($sqlLookupJobArea);						
			$dbc->next_record();
			$JobAreaID=$dbc->f('jobarea');
			$JobArea=$dbc->f('area');
			if($jobArea1 == $JobAreaID)
			{
			?><input id ="JobArea"  type="text" size="30" name="JobArea" readonly="" value="<?php echo isset($JobArea) ? $JobArea : "" ?>" />
			<a class='select_job' href="../../application/thesis/select_job.php?field=JobArea&field2=JobAreaID">[ Select ]</a>
			<input id ="JobAreaID"  type="hidden" size="30" name="JobAreaID" readonly="" value="<?php echo isset($JobAreaID) ? $JobAreaID : "" ?>" /></td><?
			 }
			 else
			 {?><input id ="JobArea"  type="text" size="30" name="JobArea" readonly="" value="<?php echo isset($JobArea) ? $JobArea : "" ?>" />
			<a class='select_job' href="../../application/thesis/select_job.php?field=JobArea&field2=JobAreaID">[ Select ]</a>
			<input id ="JobAreaID"  type="hidden" size="30" name="JobAreaID" readonly="" value="<?php echo isset($JobAreaID) ? $JobAreaID : "" ?>" /></td><?
			}?>		    
		<td width="10"></td>
        <td width="56"><b>Area 4</b></td>
			<td width="294"><?php
			$rsPgProposalArea = $dbf->query($sqlPgProposalArea);						
			$dbf->next_record();
			$jobArea3=$dbf->f('job_id4_area');
			if ($jobArea3 == "") $jobArea3 = $_POST['JobArea3'];
			$sqlLookupJobArea = "SELECT jobarea, area
								FROM job_list_category
								WHERE jobarea = '$jobArea3'";
			
			$rsLookupJobArea = $dbc->query($sqlLookupJobArea);						
			$dbc->next_record();
			$JobAreaID3=$dbc->f('jobarea');
			$JobArea3=$dbc->f('area');

			?><input id ="JobArea3"  type="text" size="30" name="JobArea3" readonly="" value="<?php echo isset($JobArea3) ? $JobArea3 : "" ?>" />
			<a class='select_job' href="../../application/thesis/select_job.php?field=JobArea3&field2=JobAreaID3">[ Select ]</a>
			<input id ="JobAreaID3"  type="hidden" size="30" name="JobAreaID3" readonly="" value="<?php echo isset($JobAreaID3) ? $JobAreaID3 : "" ?>" /></td><?
			 ?>		    
		</tr>
		<tr>
		<td ><b>Area 2</b></td>
			<td width="294"><?php
			$rsPgProposalArea = $dbf->query($sqlPgProposalArea);						
			$dbf->next_record();
			$jobArea1=$dbf->f('job_id2_area');	
		
			if ($jobArea1 == "") $jobArea1 = $_POST['JobArea1'];
			$sqlLookupJobArea = "SELECT jobarea, area
								FROM job_list_category
								WHERE jobarea = '$jobArea1'";
			
			$rsLookupJobArea = $dbc->query($sqlLookupJobArea);						
			$dbc->next_record();
			$JobAreaID1=$dbc->f('jobarea');
			$JobArea1=$dbc->f('area');

			?><input id ="JobArea1"  type="text" size="30" name="JobArea1" readonly="" value="<?php echo isset($JobArea1) ? $JobArea1 : "" ?>" />
			<a class='select_job' href="../../application/thesis/select_job.php?field=JobArea1&field2=JobAreaID1">[ Select ]</a>
			<input id ="JobAreaID1"  type="hidden" size="30" name="JobAreaID1" readonly="" value="<?php echo isset($JobAreaID1) ? $JobAreaID1 : "" ?>" /></td><?
			 ?>		    
		<td width="10"></td>
        <td nowrap><b>Area 5</b></td>
		<td width="294"><?php
			$rsPgProposalArea = $dbf->query($sqlPgProposalArea);						
			$dbf->next_record();
			$jobArea4=$dbf->f('job_id5_area');	
		
			if ($jobArea4 == "") $jobArea4 = $_POST['JobArea4'];
			$sqlLookupJobArea = "SELECT jobarea, area
								FROM job_list_category
								WHERE jobarea = '$jobArea4'";
			
			$rsLookupJobArea = $dbc->query($sqlLookupJobArea);						
			$dbc->next_record();
			$JobAreaID4=$dbc->f('jobarea');
			$JobArea4=$dbc->f('area');

			?><input id ="JobArea4"  type="text" size="30" name="JobArea4" readonly="" value="<?php echo isset($JobArea4) ? $JobArea4 : "" ?>" />
			<a class='select_job' href="../../application/thesis/select_job.php?field=JobArea4&field2=JobAreaID4">[ Select ]</a>
			<input id ="JobAreaID4"  type="hidden" size="30" name="JobAreaID4" readonly="" value="<?php echo isset($JobAreaID4) ? $JobAreaID4 : "" ?>" /></td><?
			 ?>		    
		</tr>
		
		<tr>
        <td ><b>Area 3</b></td>
		<td width="294"><?php
			$rsPgProposalArea = $dbf->query($sqlPgProposalArea);						
			$dbf->next_record();
			$jobArea2=$dbf->f('job_id3_area');	
		
			if ($jobArea2 == "") $jobArea2 = $_POST['JobArea2'];
			$sqlLookupJobArea = "SELECT jobarea, area
								FROM job_list_category
								WHERE jobarea = '$jobArea2'";
			
			$rsLookupJobArea = $dbc->query($sqlLookupJobArea);						
			$dbc->next_record();
			$JobAreaID2=$dbc->f('jobarea');
			$JobArea2=$dbc->f('area');

			?><input id ="JobArea2"  type="text" size="30" name="JobArea2" readonly="" value="<?php echo isset($JobArea2) ? $JobArea2 : "" ?>" />
			<a class='select_job' href="../../application/thesis/select_job.php?field=JobArea2&field2=JobAreaID2">[ Select ]</a>
			<input id ="JobAreaID2"  type="hidden" size="30" name="JobAreaID2" readonly="" value="<?php echo isset($JobAreaID2) ? $JobAreaID2 : "" ?>" /></td><?
			 ?>		    
            
        <td width="10"></td>
        <td width="56"><b>Area 6</b></td>
		<td width="294"><?php
			$rsPgProposalArea = $dbf->query($sqlPgProposalArea);						
			$dbf->next_record();
			$jobArea5=$dbf->f('job_id6_area');	
		
			if ($jobArea5 == "") $jobArea5 = $_POST['JobArea5'];
			$sqlLookupJobArea = "SELECT jobarea, area
								FROM job_list_category
								WHERE jobarea = '$jobArea5'";
			
			$rsLookupJobArea = $dbc->query($sqlLookupJobArea);						
			$dbc->next_record();
			$JobAreaID5=$dbc->f('jobarea');
			$JobArea5=$dbc->f('area');

			?><input id ="JobArea5"  type="text" size="30" name="JobArea5" readonly="" value="<?php echo isset($JobArea5) ? $JobArea5 : "" ?>" />
			<a class='select_job' href="../../application/thesis/select_job.php?field=JobArea5&field2=JobAreaID5">[ Select ]</a>
			<input id ="JobAreaID5"  type="hidden" size="30" name="JobAreaID5" readonly="" value="<?php echo isset($JobAreaID5) ? $JobAreaID5 : "" ?>" /></td><?
			 ?>		    
    	</tr>		
	   </table>
						
			  </fieldset>
					<br/>
				<?$_SESSION['thesis_id']=$thesis_id;?>
				<?$_SESSION['proposal_id']=$proposal_id;?>
					   <?
		/*$sqlMeeting="SELECT pmd.id,pmd.lecturer_name, DATE_FORMAT(pmd.meeting_sdate,'%d-%b-%Y') as date,
		DATE_FORMAT(pmd.meeting_sdate,'%H:%i') as time, pmd.remark, pmd.insert_by, pmd.insert_date 
		FROM  pg_meeting_detail pmd  
		WHERE pmd.pg_proposal_id='$proposal_id'
		AND student_matrix_no = '$user_id' 
		ORDER BY pmd.meeting_sdate DESC ";			

		$result = $dbb->query($sqlMeeting);
		//$dbb->next_record(); 
		$row_cnt_discussion = mysql_num_rows($result);

		if($row_cnt_discussion == '0')
		{
			$row_cnt_discussion = '';
		}
		else
		{
			$row_cnt_discussion = "(".$row_cnt_discussion.")";
		}*/
		
		$count_total_result = "SELECT COUNT(*) AS total FROM pg_meeting_detail 
						WHERE pg_proposal_id = '$proposal_id' 
						AND student_matrix_no = '$user_id'";
		$db->query($count_total_result);
		$db->next_record();
    	$a = $db->f('total');
		if ($a == '0')
		{
			$b = '';
		}
		else
		{
			$b = "(".$a.")";
		}					


		$sqlUpload="SELECT COUNT(*) as total  FROM file_upload_proposal 
		WHERE pg_proposal_id='$proposal_id' 
		AND student_matrix_no = '$user_id'
		AND attachment_level='S' ";			

		$result = $dbb->query($sqlUpload); 
		$dbb->next_record();
    	$a = $dbb->f('total');

		if($a == '0')
		{
			$a = '';
		}
		else
		{
			$a = "(".$a.")";
		}
		
		
		/**/					


	   ?>

				<table>
					<tr>
						<td><button type="button" name="btnDiscussionDetail" onclick="javascript:document.location.href='../thesis/update_proposal_discussion.php?tid=<?=$thesis_id?>&pid=<?=$proposal_id?>';">Discussion Detail <FONT COLOR="#FF0000"><sup><?=$b?></sup></FONT></button></td>
						<td><button type="button" name="btnAttachment" onclick="javascript:document.location.href='../thesis/update_proposal_attachment.php?tid=<?=$thesis_id?>&pid=<?=$proposal_id?>';">Attachment <FONT COLOR="#FF0000"><sup><?=$a?></sup></FONT></button></td>
						<td><input type="button" name="btnDiscussionDetail" value = "Discussion Detail" 
						onclick="javascript:document.location.href='../thesis/update_proposal_discussion.php?tid=<?=$thesis_id?>&pid=<?=$proposal_id?>';"/></td>				
					</tr>
				</table>
				</tr>
			</table>
			
		
			
			
			</fieldset>
			<table>
				<tr>
					<td><input type="submit" name="btnSave" value="Save as Draft"/></td>
					<td><input type="submit" name="btnSubmit" value="Submit" onClick="return respConfirm()"/>   </td>
					<td>Note: Field marks with (<span style="color:#FF0000">*</span>) is compulsory.</td>		
				</tr>
			</table>
			<?			
		}
		else if ($proposal_status=='DIS'){?>

		<fieldset>
		 <legend><strong>Verification by Faculty</strong></legend>
			<table>
				<tr>
					<td>Proposal Status</td>
					<td>:</td>
					<td><label><?=$proposal_description; ?></label></td>
				</tr>
				<tr>
					<td>Verified Date</td>
					<td>:</td>
					<td><label><?=$verified_date;?></label></td>
				</tr>
				<?
						$sql1="SELECT name AS verified_name
							FROM new_employee
							WHERE empid = '$verified_by'";
							
							$dbc->query($sql1);
							$row_personal=$dbc->fetchArray();
							$verified_name=$row_personal['verified_name'];
						?>
				<tr>
					<td>Verified By</td>
					<td>:</td>
					<td><label><?=$verified_name;?></label></td>
				</tr>
				<tr>
					<td> Remarks </td>
					<td>:</td>
					<td><label class="ckeditor"><?=$verified_remarks; ?></label></td>
				</tr>			
				<tr>
					<td> Attachment by Faculty </td>
					<td>:</td>							
					<?php
							$sqlUpload="SELECT * FROM file_upload_proposal 
							WHERE pg_proposal_id='$proposal_id' 
							AND attachment_level='F' ";			

							$result = $db->query($sqlUpload); //echo $sql;
							$row_cnt = mysql_num_rows($result);
							if ($row_cnt>0)
							{
								?><td align="left"><?
								while($row = mysql_fetch_array($result)) 					
								{ 
									?>
										<a href="download.php?fc=<?=$row["fu_cd"];?>&al=F"><?=$row["fu_document_filename"];?><img src="../images/download.png" width="20" height="19" style="border:0px;" title="Download"></a><br/>	
								<?}										
							}
							else {
								?><td>No attachment<br/></td><?
							}
						?>
				</tr>
			</table>
			</fieldset>
			<br/>
			<table>
				<tr>
					<td><strong>Note: </strong>Your thesis proposal has been disapproved by the Faculty. You may need to <a href="../thesis/edit_proposal.php?uid=<?php echo $userid;?>&pid=<?php echo $proposal_id;?>&tid=<?php echo $thesis_id;?>"><img src="../images/click_here.jpg" width="60" height="30" style="border:0px;" title="Please click here"></a> to create another one and re-submit.</td>
				</tr>
			</table>
		
			<?
		}
		
		else {//$proposal_status=='CAN'
			?>
		<fieldset>
		 <legend><strong>Verification by Faculty</strong></legend>
			<table>
				<tr>
					<td>Proposal Status</td>
					<td>:</td>
					<td><label><?=$proposal_description; ?></label></td>
				</tr>
				<tr>
					<td>Requested Date</td>
					<td>:</td>
					<td><label><?=$cancel_requested_date;?></label></td>
				</tr>
				<?
						$sql3="SELECT name AS cancel_approved_name
						FROM new_employee 
							WHERE empid = '$cancel_approved_by'";
							
							$dbc->query($sql3);
							$row_personal=$dbc->fetchArray();
							$cancel_approved_name=$row_personal['cancel_approved_name'];
						?>
				<tr>
					<td>Approved By</td>
					<td>:</td>
					<td><label><?=$cancel_approved_name;?></label></td>
				</tr>
				<tr>
					<td>Approved Date</td>
					<td>:</td>
					<td><label><?=$cancel_approved_date;?></label></td>
				</tr>
				<tr>
					<td> Remarks by Faculty</td>
					<td>:</td>
					<td><label class="ckeditor"><?=$cancel_approved_remarks; ?></label></td>
				</tr>							
			</table>
			</fieldset>
			<br/>
			<table>
				<tr>
					<td><strong>Note: </strong>Your request to cancel the proposal has been approved by the Faculty. You may need to <a href="../thesis/edit_proposal.php?id=<?=$thesis_id;?>&pid=<?=$proposal_id;?>"><img src="../images/click_here.jpg" width="60" height="30" style="border:0px;" title="Please click here"></a> to edit it again and re-submit.</td>
				</tr>
			</table>
		
			<?
		}
		
	}
	else {//echo "has no record";?>
		
		<fieldset>
			<legend><strong><span style="color:#FF0000">Notification Message</span></strong></legend>
			<table>
				<tr>			
					<td>There is no thesis proposal available to view. You need to <a href="../thesis/new_proposal.php?uid=<?=$userid;?>&pid=<?php echo $proposal_id;?>&tid=<?php echo $thesis_id;?>"><img src="../images/click_here.jpg" width="60" height="30" style="border:0px;" title="Plase click here"></a> to create a new one and submit. </td>							
					
				</tr>
			</table>
		</fieldset>
	<?}?>

  </form>
</body>
</html>




