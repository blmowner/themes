<?php

    
include("../../../lib/common.php");
checkLogin();
ob_start();
session_start();
$userid=$_REQUEST['uid'];
$pgProposalId=$_REQUEST['pid'];
$id = $_REQUEST['id'];
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
	
<script>
function goBack() {
    window.history.back();
}
</script>
    <style type="text/css">
<!--
.style3 {color: #FFFFFF; font-weight: bold; }
-->
    </style>

</head>

<body>
<div id = "replymsg">

  <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data">
  	
    <?php 
	$curdatetime = date("Y-m-d H:i:s");

if(isset($_POST['btnSubmit']) && ($_POST['btnSubmit'] <> ""))
{
	$msg = array();
	
		if(empty($msg))
		{
			$curdatetime = date("Y-m-d H:i:s");
			//echo $id = $_REQUEST['reply'];
			$message = $_REQUEST['message'];
			$name = convertname($user_id);
			$process = "Submit";
			$user_id;
			$inboxid ="I".runnum('id','pg_messages');
			$message = $_REQUEST['message'];
			$subject = $_REQUEST['subject'];
			//$from = $_REQUEST['from'];
			$userID;
			$receive = $_REQUEST['from'];

			$reply = "INSERT INTO pg_messages(`id`, `sent_by`, user_id, subject, message, `date_time`, `status`, `receive_by`, `process_status`)
			VALUES('$inboxid', '$name','$user_id', '$subject', '$message', '$curdatetime','0','$userID', '$process')";
			//exit();
			$db_klas2->query($reply);
			$msg[] = "<div class=\"success\"><span>Mail has been sent!</span></div>";
			
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
				
				$sqlemail = "SELECT email from new_employee 
					WHERE `empid` = '$user_id'
					UNION 
					SELECT email FROM student
					WHERE `matrix_no` = '$user_id'";
				 	
					$resultsendere = $dbk->query($sqlemail);
					$resultsqlemail = $dbk->next_record(); 
					$senderemail = $dbk->f('email');
				
				$sqlemail2 = "SELECT email from new_employee 
					WHERE `empid` = '$receive'
					UNION 
					SELECT email FROM student
					WHERE `matrix_no` = '$receive'";
				
				$resultreceive = $dbc->query($sqlemail2);
				$resultsqlreceive = $dbc->next_record(); 
				$receiveemail = $dbc->f('email');
				
				$selectfrom = "SELECT const_value
				FROM base_constant WHERE const_term = 'EMAIL_ADMIN'";
				$resultfrom = $db->query($selectfrom);
				$db->next_record();
				$fromadmin =$db->f('const_value');
				
				$attachmentdata = file_get_contents($_FILES['file_name']['tmp_name']);
				$FileName = $_FILES['file_name']['name'];
				$FileType = $_FILES['file_name']['type'];
				
				include("email_reply.php");
	
				 
		}
	
}
 ?>
 <?php 
	if(!empty($msg)) 
	{
        foreach($msg as $err) 
		{
            echo $err;
			//reset form;
        }
    }
		$time = strtotime($curdatetime);
		$myFormatForView = date("d/m/y g:i A", $time);  

	?>
 <div id = "div">
      <table width="998" height="129">
        <tr>
			  <td bgcolor="#5B74A8" height="23"><span class="style3">From</span></td>
			  
			   <td colspan="2" bgcolor= "#ACB7D2">
				<div align="left" style="width: 600px; float: left;">
					<?=convertname($user_id)?>
				</div>
			  
				<div style="margin-left: 730px;">
  	         	      <div align="right"><?= $myFormatForView; ?></div>
		  		</div></td>
		</tr>
        <tr bgcolor="#5B74A8">
          <td height="23"><span class="style3">To</span></td>
          <td colspan="2"  bgcolor= "#ACB7D2">
            <?= $from; ?>
            
            <input type="hidden" name="from" value = "<?=$userID?>" />
          </td>
        </tr>
        <tr bgcolor="#5B74A8">
          <td width="78" height="23"><span class="style3">Subject</span></td>
          <td colspan="2"  bgcolor= "#ACB7D2"><input size = "134" type="text" name="subject" id = "subject" value = "<?=$subject;?>" />
          
                <input type="hidden" name="nama" value = "<?= $user_id; ?>" />
                <?php $name = convertname($user_id); ?>
                <input type="hidden" name="nama" value = "<?=$name?>" />
          </td>
        </tr>
        <tr bgcolor="#000000">
          <td height="23" colspan="3" bgcolor="#DBDBDB"><textarea name="message" cols="50" class = "ckeditor" rows="8"></textarea></td>
          <?php //$message = $_SESSION['message'];  ?>
        </tr>
        <tr bgcolor="#000000">
          <td height="23" bgcolor="#CCCCCC">Attachment</td>
          <td bgcolor="#DBDBDB"><input type="file" name="file_name" style="width:200px;" /></td>
          <td width="1" bgcolor="#DBDBDB">&nbsp;</td>
        </tr>
    </table>
    <input type="submit" name="btnSubmit" value="Submit" />
	<input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../inbox/read_inbox.php?msg=<?=$id?>';" />
    </div>
  
<script>

	$(document).ready(function() {         
		
		$('.error').hide();
		var showError = <?php echo $msg; ?>;         
		if (showError) {             
			$('.error').fadeIn(500).delay(1000).fadeOut(500);
			$('.success').fadeIn(500).delay(1000).fadeOut(500);
			//alert("introduction: " + document.form1.introduction.value);
			$msg.focus();       
		} else {
			
		}
	
	});

</script>
 
</form>
</body>
</html>
