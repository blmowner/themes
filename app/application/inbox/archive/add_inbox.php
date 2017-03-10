<?php

include("../../../lib/common.php");
checkLogin();

session_start();
$userid=$_REQUEST['uid'];
$pgProposalId=$_REQUEST['pid'];

	$status_draft = "SELECT * FROM pg_messages WHERE process_status = 'Save as Draft' AND user_id = '$user_id'";
	$result_sql_draft = $db->query($status_draft);
	$row_area = $db->fetchArray();;
	$process_status = $row_area['process_status'];
	$hidden_id = $row_area['id'];
	$empname = $row_area['receive_by'];
		//$staff_id = $row_area['receive_by'];
	$subject = $row_area['subject'];
	$message = $row_area['message'];
	$row_cnt = mysql_num_rows($result_sql_draft);
	$staff_id = convertname($empname);

function runnum($column_name, $tblname) 
{ 
    global $db_klas2;
    
    $run_start = "001";
    
    $sql_slct_max = "SELECT MAX(SUBSTR($column_name,2,12)) AS run_id FROM $tblname";
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
function convertid($staff_id)
{
global $dbc;
$convert_name_id = "SELECT empid from new_employee WHERE `name` = '$staff_id'";
$dbc->query($convert_name_id);
$rows = $dbc->next_record();
$rows = $dbc->rowdata();
$empid = $rows['empid'];
return $empid;

}
if(isset($_POST['btnSubmit']) && ($_POST['btnSubmit'] <> ""))
{

	
	$msg = array();
	//$name = convertid($user_id);
	$empid = convertid($staff_id);
	$name = $_REQUEST['name'];
	if(empty($_POST['staff_id'])) $msg[] = "<div class=\"error\"><span>Please select Receiver</span></div>";
	if(empty($_POST['subject'])) $msg[] = "<div class=\"error\"><span>Please insert Subject</span></div>";
	if(empty($_POST['message'])) $msg[] = "<div class=\"error\"><span>Please insert Message</span></div>";
	//echo $filename = $_REQUEST['file_name'];
	
	
if ($row_cnt == 0)
{	
		if(empty($msg)) 
		{
			$staffid = explode(" , ", $_REQUEST['staff_id']);	
			foreach ($staffid as $s)
			{			
			
			  if (!empty($s)) 
			  {
				$curdatetime = date("Y-m-d H:i:s");	
				$inboxid = "I".runnum('id','pg_messages');
				$submit = "Submit";	
				$message = $_REQUEST['message'];
				$subject = $_REQUEST['subject'];
				$staff_id = $_REQUEST['staff_id'];
				$empid = convertid($staff_id);
				$file = $_REQUEST['file_name'];
								
				$convert_name_id = "SELECT empid FROM new_employee WHERE name = '$s'
				UNION 
				SELECT matrix_no FROM student WHERE name = '$s'";
				$dbc->query($convert_name_id);
				$rows = $dbc->next_record();
				$rows = $dbc->rowdata();
				$empid = $rows['empid'];

					
				$sql2="INSERT INTO pg_messages(id, `sent_by`, `user_id`, `subject`, `message`, `date_time`, `status`, `receive_by`, `process_status`)
				VALUES('$inboxid','$name','$user_id','$subject','$message', '$curdatetime','0','$empid', '$submit')";
				
				
				//exit();
				$db->query($sql2);
				
				$filename = $_FILES['file_name']['name'];
				
	
				if (!empty($filename)) 
				{
					$file_id = "F".runnum('file_id','file_upload_inbox');
							$data = file_get_contents($_FILES['file_name']['tmp_name']);
									$FileName = $_FILES['file_name']['name'];
									$FileType = $_FILES['file_name']['type'];
									$FileSize = intval($_FILES['file_name']['size']);
								
									  $sql2 = "INSERT INTO file_upload_inbox (
											 file_id,
											 inbox_id,
											 file_name, 
											 file_type,
											 file_size,
											 file_data) 
											 VALUES (
											 '$file_id',
											 '$inboxid',
											 '".$FileName."', 
											 '".$FileType."', 
											 '".$FileSize."', 
											 '".mysql_escape_string($data)."')";

								  $db->query($sql2);
							
							
					
				} 

				
				
				$name = $_REQUEST['name'];
				$inbox = $_REQUEST['message'];
				$subject = $_REQUEST['subject'];
				$staff_id = $_REQUEST['staff_id'];
				
				$attachmentdata = file_get_contents($_FILES['file_name']['tmp_name']);
				$FileName = $_FILES['file_name']['name'];
				$FileType = $_FILES['file_name']['type'];
				
				$sqlemail = "SELECT email from new_employee 
					WHERE `empid` = '$user_id'
					UNION 
					SELECT email FROM student
					WHERE `matrix_no` = '$user_id'";
				 	
					$resultsendere = $dbk->query($sqlemail);
					$resultsqlemail = $dbk->next_record(); 
					$senderemail = $dbk->f('email');
				
				$sqlemail2 = "SELECT email from new_employee 
					WHERE `empid` = '$empid'
					UNION 
					SELECT email FROM student
					WHERE `matrix_no` = '$empid'";
				 	
					$resultreceive = $dbc->query($sqlemail2);
					$resultsqlreceive = $dbc->next_record(); 
					$receiveemail = $dbc->f('email');
					

					
					$selectfrom = "SELECT const_value
					FROM base_constant WHERE const_term = 'EMAIL_ADMIN'";
					$resultfrom = $db->query($selectfrom);
					$db->next_record();
					$fromadmin =$db->f('const_value');
							 
				//$_FILES['file_name'];
				include("email.php");
		
				if ($ok) 
				{
					// email sent
					$msg[] = "<div class=\"success\"><span>The Message has been sent successfully!</span></div>";
				} 
				else 
				{
					// email not sent, error
					$msg[] = "<div class=\"error\"><span>Email not sent!</span></div>";
				}
			}
				
		}
		
	}
}
else
{
	$staffid = explode(" , ", $_REQUEST['staff_id']);	
	foreach ($staffid as $s) 
	{			
		
		if (!empty($s)) 
		{
			$curdatetime = date("Y-m-d H:i:s");
			$message = $_REQUEST['message'];
			$subject = $_REQUEST['subject'];
			$staff_id = $_REQUEST['staff_id'];
			$empid = convertid($staff_id);
	
			$sqlUpdate = "UPDATE pg_messages
					SET message = '$message', date_time = '$curdatetime', process_status = 'Submit', subject = '$subject', receive_by = '$s'
					WHERE id = '$hidden_id'";
					
			$db_klas2->query($sqlUpdate);
			$msg[] = "<div class=\"success\"><span>Mail has been sent!</span></div>";
			
				$sqlemail = "SELECT email from new_employee 
					WHERE `empid` = '$user_id'
					UNION 
					SELECT email FROM student
					WHERE `matrix_no` = '$user_id'";
				 	
					$resultsendere = $dbk->query($sqlemail);
					$resultsqlemail = $dbk->next_record(); 
					$senderemail = $dbk->f('email');
				
				$sqlemail2 = "SELECT email from new_employee 
					WHERE `empid` = '$empid'
					UNION 
					SELECT email FROM student
					WHERE `matrix_no` = '$empid'";
				 	
					$resultreceive = $dbc->query($sqlemail2);
					$resultsqlreceive = $dbc->next_record(); 
					$receiveemail = $dbc->f('email');
					

					
					$selectfrom = "SELECT const_value
					FROM base_constant WHERE const_term = 'EMAIL_ADMIN'";
					$resultfrom = $db->query($selectfrom);
					$db->next_record();
					$fromadmin =$db->f('const_value');

							 
				include("email.php");

								 

		}
	}
	

}			
}
if(isset($_POST['btnSave']) && ($_POST['btnSave'] <> ""))
{

if ($row_cnt == 0)
{		
			$curdatetime = date("Y-m-d H:i:s");	
			$inboxid = "I".runnum('id','pg_messages');
			$submit1 = "Save as Draft";	
			
			$curdatetime = date("Y-m-d H:i:s");
			$message = $_REQUEST['message'];
			$subject = $_REQUEST['subject'];
			$staff_id = $_REQUEST['staff_id'];
			$empid = convertid($staff_id);		
			
			$sqlsubmit1="INSERT INTO pg_messages(id, `sent_by`, `user_id`, `subject`, `message`, `date_time`, `status`, `receive_by`, `process_status`)
			VALUES('$inboxid','$name','$user_id','$subject','$message', '$curdatetime','0','$staff_id', '$submit1')";
			$db_klas2->query($sqlsubmit1);
			  //mail($admin_email, "$subject", "$message", "$headers");
			
			//echo("<font color=\"red\">Success</font>"); 
			$msg[] = "<div class=\"success\"><span>The Message has been save as Draft Successfuly</span></div>";
			//$("#form1")[0].reset();
			//header("Location: add_inbox.php");
	
}
else
{
			$curdatetime = date("Y-m-d H:i:s");
			$inbox = $_REQUEST['message'];
			$subject = $_REQUEST['subject'];
			$staff_id = $_REQUEST['staff_id'];
			$empid = convertid($staff_id);
			
			$sqlUpdate = "UPDATE pg_messages
							SET message = '$message', date_time = '$curdatetime', subject = '$subject', receive_by = '$staff_id'
							WHERE id = '$hidden_id'";
							
			$db_klas2->query($sqlUpdate);
			$msg[] = "<div class=\"success\"><span>Update draft successfully</span></div>";
		
}
}
?>	
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang; ?>" lang="<?php echo $lang; ?>">

<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
	<meta name="author" content="MJMZ" />

	<title>New Mail</title>
    <link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
    <link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
	<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>   
    <script src="../../../lib/js/jquery.min2.js"></script>
	<script src="../../../lib/js/jquery.colorbox.js"></script>
	<script language="JavaScript">

</script>
    <script>
  
		$(document).ready(function(){
		      //$(".select_user").colorbox({width:"60%", height:"40%", iframe:true});
              
              $.fn.getParameterValue = function(data) {
                  //alert(data);
                  document.form1.staff_id.value = data;
                };
              
               $(".select_user").colorbox({width:"80%", height:"90%", iframe:true,          
               onClosed:function(){ 
                //location.reload(true); //uncomment this line if you want to refresh the page when child close
                                
                } }); 
          });
	</script>
<script>


$(document).ready(function() 
{         
		
		$('.error').hide();
		$('.success').hide();
		var showError = <?php echo $msg; ?>;         
		if (showError) {             
			$('.error').fadeIn(500).delay(500).fadeOut(500);
			$('.success').fadeIn(500).delay(500).fadeOut(500);
			//alert("introduction: " + document.form1.introduction.value);
			$msg.focus();
			
    }       
		} else {
			
		}
	
});
</script>

<style type="text/css">
<!--
.style3 {color: #FFFFFF; font-weight: bold; }
-->
</style>
</head>
<body>
 <?php
    if(!empty($msg)) 
	{
        foreach($msg as $err) 
		{
			//$_POST=array();  
            echo $err;
			
        }
    }
	else
	{
		
	}
?>


<SCRIPT LANGUAGE="JavaScript">
	
</SCRIPT>

<script>
$(function() {
	$( "#datepickerFirst" ).datepicker({
		changeMonth: true,
		changeYear: true,
		yearRange: '-100:+0',
		dateFormat: 'dd-mm-yy'
		});
});
</script>

<form method="post" id="form1" name="form1" action = "" enctype="multipart/form-data">
  
  <p>
  <?
	$name = convertname($user_id);
?>
  </p>
  <fieldset>
<strong>New Message </strong>
<input type="hidden" name="hidden_id" id = "hidden_id" value = "<?=$hidden_id ?>" />
<table width="830">
		<tr>
		  <td width="77" bgcolor="#5B74A8"><span class="style3">User ID </span></td>
		  <td width="729" bgcolor="#DBDBDB"><?=$user_id?>
	      <input type="hidden" name="status_draft" value = "<?=$status_draft?>" /></td>
    </tr>
		<tr>

		<? $name = convertname($user_id)?>
		  <td bgcolor="#5B74A8"><span class="style3">Username</span></td>
		  <td bgcolor="#DBDBDB"><?=convertname($user_id)?>
	      <input type="hidden" name="name" value = "<?=convertname($user_id)?>" /></td>
    </tr>
		<tr>
          <td bgcolor="#5B74A8"><span class="style3">To</span></td>
		  <td bgcolor="#DBDBDB"><p>
		  
                <input id ="staff_id"  type="text" size="103" name="staff_id" readonly="" value="<?php echo isset($staff_id) ? $staff_id : "" ?>" />
          <a class='select_user' href="../../application/inbox/select_receiver.php">[ Select User ]</a><a href="select_receiver2.0.php"></a></td>
    </tr>
		<tr>
		  <td bgcolor="#5B74A8"><span class="style3">Subject</span></td>
		  <td bgcolor="#DBDBDB"><input type="text" name="subject" size = "120" id = "subject" value = "<?=$subject;?>" /></td>
    </tr>
		<tr>
          <td bgcolor="#5B74A8"><span class="style3">Message</span></td>
		  <td bgcolor="#DBDBDB"><textarea name="message" cols="50" class = "ckeditor" rows="8"><?=$message;?>
        </textarea></td>

    </tr>
		<tr>
		  <td bgcolor="#5B74A8"><span class="style3">Attachment</span></td>
		  <td bgcolor="#DBDBDB"><input type="file" name="file_name" style="width:200px;" /></td>
    </tr>
  </table>
  
	  
	   <table width="856" height="39">
			<tr>
				
			  <td width="853" height="30" bgcolor="#666666"><input type="submit" name="btnSave" value="Save as Draft" id = "btnSave" />
		      <input type="submit" name="btnSubmit" id="btnSubmit" align="center" onClick = "clearForm();" value="Submit" /></td>
					
			</tr>
  </table>
  </fieldset>
  
 
		
</form>
</body>
</html>
