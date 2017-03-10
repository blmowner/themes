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
	
    <style type="text/css">
<!--
.style3 {color: #FFFFFF; font-weight: bold; }
.style4 {color: #FFFFFF}
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
			$from = $_REQUEST['from'];
			$userID;
			
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
								//echo "The file $filename exists";
								//echo $filename;
								//exit();
								  $db->query($sql2);
							
							
					
				} 
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
	?>
 <div id = "div">
      <table width="992" height="79">
        <tr bgcolor="#5B74A8">
          <td width="69" height="23"><span class="style3">From:
            <?=convertname($user_id)?>
          </span></td>
          <td width="209"><span class="style4">Send To:
            <?= $from; ?>
            <span class="style3">
            <input type="hidden" name="from" value = "<?=$userID?>" />
          </span></span></td>
          <td width="289"><span class="style3">Sender ID:
            <?= $user_id; ?>
                <?php $name = convertname($user_id); ?>
                <input type="hidden" name="nama" value = "<?=$name?>" />
          </span></td>
          <td width="314"><span class="style3">Subject:
            <input type="text" name="subject" id = "subject" value = "<?=$subject;?>" />
          </span></td>
          <td width="87"><p class="style3">Sent:
            <?= $curdatetime; ?>
          </p></td>
        </tr>
        <tr bgcolor="#000000">
          <td height="23" bgcolor="#DBDBDB">&nbsp;</td>
          <?php //$message = $_SESSION['message'];  ?>
          <td colspan="3" nowrap="nowrap" bgcolor="#DBDBDB"><textarea name="message" cols="50" class = "ckeditor" rows="8">
  	      </textarea></td>
          <td bgcolor="#DBDBDB"><p> </p>
              <p>
                <input type="submit" name="btnSubmit" value="Submit" class="fancy-button-blue" />
              </p>
            <p><a href="inbox.php?">
                <input type="submit" name="Submit" value="Back" onclick="javascript: form.action='inbox.php';" submit="submit"" class="fancy-button-grey" />
          </a> </p></td>
        </tr>
        <tr bgcolor="#000000">
          <td height="23" bgcolor="#CCCCCC">File</td>
          <td colspan="3" bgcolor="#DBDBDB"><input type="file" name="file_name" style="width:200px;" /></td>
          <td bgcolor="#DBDBDB">&nbsp;</td>
        </tr>
    </table>
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
