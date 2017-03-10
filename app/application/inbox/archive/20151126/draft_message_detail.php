<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: draft_message_detail.php
//
// Created by: Mohd Nizam
// Created Date: 08-April-2015
// Modified by: Zuraimi
// Modified Date: 27-April-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

session_start();
$user_id=$_SESSION['user_id'];
$messageId = $_GET['mid'];

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

function runnum2($column_name, $tblname) 
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

function convertname($user_id)
{
	global $db;
	$sql_login = "SELECT name 
	FROM new_employee 
	WHERE empid='$user_id' 
	union 
	SELECT name 
	FROM student 
	WHERE matrix_no='$user_id'";

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

/*
if(isset($_POST['btnSubmit']) && ($_POST['btnSubmit'] <> "")) {
	$msg = array();
	//$name = convertid($user_id);
	$empid = convertid($staff_id);
	$name = $_REQUEST['name'];
	if(empty($_POST['staff_id'])) $msg[] = "<div class=\"error\"><span>Please select Receiver</span></div>";
	if(empty($_POST['subject'])) $msg[] = "<div class=\"error\"><span>Please insert Subject</span></div>";
	if(empty($_POST['message'])) $msg[] = "<div class=\"error\"><span>Please insert Message</span></div>";
	//echo $filename = $_REQUEST['file_name'];
	
	
	if ($row_cnt == 0) {	
		if(empty($msg)) {
			$staffid = explode(",", $_REQUEST['staff_id']);	
			foreach ($staffid as $s) {						
				if (!empty($s)) {
					$curdatetime = date("Y-m-d H:i:s");	
					$inboxid = "I".runnum('id','pg_messages');
					$submit = "Submit";	
					$message = $_REQUEST['message'];
					$subject = $_REQUEST['subject'];
					$staff_id = $_REQUEST['staff_id'];
					$empid = convertid($staff_id);
					$file = $_REQUEST['file_name'];
					$nameid = convertid($s);

					$sql2="INSERT INTO pg_messages(id, `sent_by`, `user_id`, `subject`, `message`, `date_time`, `status`, `receive_by`, `process_status`)
					VALUES('$inboxid','$name','$user_id','$subject','$message', '$curdatetime','0','$nameid', '$submit')";


					//exit();
					$db->query($sql2);

					$filename = $_FILES['file_name']['name'];
						
			
					if (!empty($filename)) {
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
					$name = $_REQUEST['name'];
					$inbox = $_REQUEST['message'];
					$subject = $_REQUEST['subject'];
					$staff_id = $_REQUEST['staff_id'];
					$attachmentdata = file_get_contents($_FILES['file_name']['tmp_name']);
					$FileName = $_FILES['file_name']['name'];
					$FileType = $_FILES['file_name']['type'];
					$FileSize = intval($_FILES['file_name']['size']);
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
	else {
		$staffid = explode(",", $_REQUEST['staff_id']);	
		foreach ($staffid as $s) {			
			if (!empty($s)) {
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
			}
		}
	}			
}
*/

if(isset($_POST['btnSave']) && ($_POST['btnSave'] <> ""))
{	
	$msg = Array();
	$messageId = $_POST['messageId'];
	if(empty($_POST['staff_id'])) $msg[] = "<div class=\"error\"><span>Please select Recipient</span></div>";
	if(empty($_POST['subject'])) $msg[] = "<div class=\"error\"><span>Please enter Subject</span></div>";
	if(empty($_POST['message'])) $msg[] = "<div class=\"error\"><span>Please enter Message</span></div>";
	$curdatetime = date("Y-m-d H:i:s");
	$message = $_POST['message'];
	$subject = $_POST['subject'];
	$empid = convertid($staff_id);		
	$staffid = explode(",", $_POST['staff_id']);			
	if(empty($msg)) {
		
		$sql0="SELECT id
		FROM pg_messages
		WHERE sender = '$user_id'
		AND id = '$messageId'
		AND status = 'SAV'";
		$result_sql0 = $dba->query($sql0);	
		$row_cnt = mysql_num_rows($result_sql0);	
		if ($row_cnt == 0) {
			
			$newMessageId = "I".runnum('id','pg_messages');
					
			$sql="INSERT INTO pg_messages
			(id, sender, subject, message, message_date, status)
			VALUES('$newMessageId','$user_id','$subject','$message', '$curdatetime','SAV')";
			$dba->query($sql);		
					
			foreach ($staffid as $recipient) {	
			
				if (!empty($recipient)) {	
					$newMessageDetailId = runnum2('id','pg_messages_detail');
					
					$sq2="INSERT INTO pg_messages_detail
					(id, message_id, recipient, status)
					VALUES('$newMessageDetailId','$newMessageId', '$recipient', 'SAV')";
					$dba->query($sq2);			
				}
			}						
		}
		else {
			$sql3 = "UPDATE pg_messages
			SET subject = '$subject', message = '$message', message_date = '$curdatetime'
			WHERE id = '$messageId'";
			
			$dba->query($sql3);
			
			$sql4 = "DELETE FROM pg_messages_detail
			WHERE message_id = '$messageId'";
			
			$dba->query($sql4);
			
			foreach ($staffid as $recipient) {	
			
				if (!empty($recipient)) {	
					$newMessageDetailId = runnum2('id','pg_messages_detail');
					
					$sq5="INSERT INTO pg_messages_detail
					(id, message_id, recipient, status)
					VALUES('$newMessageDetailId','$messageId', '$recipient', 'SAV')";
					$dba->query($sq5);			
				}
			}			
		}
		$msg[] = "<div class=\"success\"><span>The message has been saved successfully.</span></div>";		
	}	
}

if(isset($_POST['btnNew']) && ($_POST['btnNew'] <> "")) 
{
	$_POST['staff_id']="";
	$_POST['messageId']="";
	$_POST['subject']="";
	$_POST['message']="";
}

if(isset($_POST['btnSubmit']) && ($_POST['btnSubmit'] <> "")) 
{	
	$msg = Array();
	$messageId = $_POST['messageId'];
	if(empty($_POST['staff_id'])) $msg[] = "<div class=\"error\"><span>Please select Recipient</span></div>";
	if(empty($_POST['subject'])) $msg[] = "<div class=\"error\"><span>Please enter Subject</span></div>";
	if(empty($_POST['message'])) $msg[] = "<div class=\"error\"><span>Please enter Message</span></div>";
	$curdatetime = date("Y-m-d H:i:s");
	$message = $_POST['message'];
	$subject = $_POST['subject'];
	$empid = convertid($staff_id);		
	$staffid = explode(",", $_POST['staff_id']);			
	if(empty($msg)) {
		
		$sql0="SELECT id
		FROM pg_messages
		WHERE sender = '$user_id'
		AND id = '$messageId'
		AND status = 'SAV'";
		$result_sql0 = $dba->query($sql0);	
		$row_cnt = mysql_num_rows($result_sql0);	
		if ($row_cnt == 0) {
			
			$newMessageId = "I".runnum('id','pg_messages');
					
			$sql="INSERT INTO pg_messages
			(id, sender, subject, message, message_date, status)
			VALUES('$newMessageId','$user_id','$subject','$message', '$curdatetime','INP')";
			$dba->query($sql);		
					
			foreach ($staffid as $recipient) {	
			
				if (!empty($recipient)) {	
					$newMessageDetailId = runnum2('id','pg_messages_detail');
					
					$sq2="INSERT INTO pg_messages_detail
					(id, message_id, recipient, status)
					VALUES('$newMessageDetailId','$newMessageId', '$recipient', 'INP')";
					$dba->query($sq2);			
				}
			}						
		}
		else {
			$sql3 = "UPDATE pg_messages
			SET subject = '$subject', message = '$message', message_date = '$curdatetime', status = 'INP'
			WHERE id = '$messageId'";
			
			$dba->query($sql3);
			
			$sql4 = "DELETE FROM pg_messages_detail
			WHERE message_id = '$messageId'";
			
			$dba->query($sql4);
			
			foreach ($staffid as $recipient) {	
			
				if (!empty($recipient)) {	
					$newMessageDetailId = runnum2('id','pg_messages_detail');
					
					$sq5="INSERT INTO pg_messages_detail
					(id, message_id, recipient, status)
					VALUES('$newMessageDetailId','$messageId', '$recipient', 'INP')";
					$dba->query($sq5);			
				}
			}			
		}
		$msg[] = "<div class=\"success\"><span>The message has been sent to the recipient successfully.</span></div>";	
		$_POST['staff_id']="";
		$_POST['messageId']="";
		$_POST['subject']="";
		$_POST['message']="";		
	}	
}

$sql = "SELECT id, subject, message, DATE_FORMAT(message_date,'%d-%b-%Y %h:%i %p') AS message_date
FROM pg_messages
WHERE id = '$messageId'
AND STATUS = 'SAV'";

$sql_result = $db->query($sql);
$db->next_record();

$theMessageId = $db->f('id');
$theSubject = $db->f('subject');
$theMessage = $db->f('message');
$theMessageDate = $db->f('message_date');
$_POST['messageId'] = $theMessageId;
$_POST['subject'] = $theSubject;
$_POST['message'] = $theMessage;
$_POST['messageDate'] = $theMessageDate;

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


</head>
<body>

 <?php
    if(!empty($msg)) 
	{
        foreach($msg as $err) 
		{
            echo $err;
        }
    }
?>



<form method="post" id="form1" name="form1" action = "" enctype="multipart/form-data">			
	<fieldset>
	<table>
		<tr>
			<td bgcolor="#5B74A8"><span class="style3">Last Update</span></td>
			<td bgcolor="#DBDBDB"><label><?=$_POST['messageDate']?></label></td>
		</tr>
		<tr>
			<td bgcolor="#5B74A8"><span class="style3">From</span></td>
			<td bgcolor="#DBDBDB"><?=convertname($user_id)?>
			<input type="hidden" name="userId" id="userId" value = "<?=$user_id?>" /></td>
		</tr>
		<tr>
			<input type="hidden" name="messageId" id="messageId" value="<?=$_POST['messageId']?>"></input>
			
			<?
			$sql1 = "SELECT recipient
			FROM pg_messages_detail
			WHERE message_id = '$theMessageId'";
			
			$result_sql1 = $dba->query($sql1);
			$dba->next_record();

			do {
				$userId = $dba->f('recipient');
				$userName = convertname($userId);
				$theUserName = $userName.','.$theUserName;
				$theUserId = $userId.', '.$theUserId;
			} while ($dba->next_record());
			$_POST['staff_name'] = $theUserName;
			$_POST['staff_id'] = $theUserId;
			?>
			
			<td bgcolor="#5B74A8"><span class="style3">To</span></td>
			<td bgcolor="#DBDBDB"><input id ="user_id"  type="text" size="100" name="user_id" value="<?=$_POST['staff_name']?>" /><a class='select_user' href="../../application/inbox/select_receiver.php">[ Select Recipient ]</a></td>
			<input id ="staff_id"  type="hidden" size="100" name="staff_id" value="<?=$_POST['staff_id']?>" /></input>
		</tr>
			<tr>
			  <td bgcolor="#5B74A8"><span class="style3">Subject</span></td>
			  <td bgcolor="#DBDBDB"><input type="text" name="subject" size = "120" id = "subject" value="<?=$_POST['subject']?>"/></td>
		</tr>
			<tr>
			  <td bgcolor="#5B74A8"><span class="style3">Message</span></td>
			  <td bgcolor="#DBDBDB"><textarea name="message" id="message" cols="50" class = "ckeditor" rows="8"><?=$_POST['message']?></textarea></td>

		</tr>
			<tr>
			  <td bgcolor="#5B74A8"><span class="style3">Attachment</span></td>
			  <td bgcolor="#DBDBDB"><input type="file" name="file_name" style="width:200px;" /></td>
		</tr>
	  </table>
	  
		  
	   <table width="856" height="39">
			<tr>				
				<td width="853" height="30"><input type="submit" name="btnSave" value="Save Draft" id = "btnSave" />
				<input type="submit" name="btnSubmit" id="btnSubmit" align="center"  value="Send" />
				<input type="submit" name="btnNew" id="btnNew" align="center"  value="New" />
				<input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../inbox/draft_message.php';" /></td>
			</tr>
	  </table>
	  </fieldset>

		
</form>
</body>
</html>
