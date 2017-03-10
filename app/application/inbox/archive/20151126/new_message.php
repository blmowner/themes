<?php

//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: new_message.php
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
	global $dbc;
	$sql_login = "SELECT name 
	FROM new_employee 
	WHERE empid='$user_id' 
	union 
	SELECT name 
	FROM student 
	WHERE matrix_no='$user_id'";

	$dbc->query($sql_login);
	$dbc->next_record();
	$rows = $dbc->rowdata();
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
		AND recipient_status = 'SAV'";
		$result_sql0 = $dba->query($sql0);	
		$row_cnt = mysql_num_rows($result_sql0);	
		if ($row_cnt == 0) {
			
			$newMessageId = "I".runnum('id','pg_messages');
					
			$sql="INSERT INTO pg_messages
			(id, sender, subject, message, message_date, status, status_date)
			VALUES('$newMessageId','$user_id','$subject','$message', '$curdatetime','NEW', '$curdatetime')";
			$dba->query($sql);		
					
			foreach ($staffid as $recipient) {	
			
				if (!empty($recipient)) {	
					$newMessageDetailId = runnum2('id','pg_messages_detail');
					
					$sq2="INSERT INTO pg_messages_detail
					(id, message_id, recipient, recipient_status, recipient_status_date, sender_status, sender_status_date)
					VALUES('$newMessageDetailId','$newMessageId', '$recipient', 'NEW', '$curdatetime', 'NEW', '$curdatetime')";
					$dba->query($sq2);			
				}
			}						
		}
		else {
			$sql3 = "UPDATE pg_messages
			SET subject = '$subject', message = '$message', message_date = '$curdatetime', status = 'SAV', status_date = '$curdatetime'
			WHERE id = '$messageId'";
			
			$dba->query($sql3);
			
			$sql4 = "DELETE FROM pg_messages_detail
			WHERE message_id = '$messageId'";
			
			$dba->query($sql4);
			
			foreach ($staffid as $recipient) {	
			
				if (!empty($recipient)) {	
					$newMessageDetailId = runnum2('id','pg_messages_detail');
					
					$sq5="INSERT INTO pg_messages_detail
					(id, message_id, recipient, recipient_status, recipient_status_date, sender_status, sender_status_date)
					VALUES('$newMessageDetailId','$messageId', '$recipient', 'SAV' , '$curdatetime', 'SAV', '$curdatetime')";
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
	$_POST['staff_name']="";
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
			(id, sender, subject, message, message_date, status, status_date)
			VALUES('$newMessageId','$user_id','$subject','$message', '$curdatetime','NEW', '$curdatetime')";
			$dba->query($sql);		
					
			foreach ($staffid as $recipient) {	
			
				if (!empty($recipient)) {	
					$newMessageDetailId = runnum2('id','pg_messages_detail');
					
					$sq2="INSERT INTO pg_messages_detail
					(id, message_id, recipient, recipient_status, recipient_status_date, sender_status, sender_status_date)
					VALUES('$newMessageDetailId','$newMessageId', '$recipient', 'NEW', '$curdatetime', 'NEW', '$curdatetime')";
					$dba->query($sq2);			
				}
			}
			$sqlUpload = "UPDATE file_upload_inbox
			SET message_id = '$newMessageId'
			WHERE user_id = '$user_id'
			AND (message_id = '$messageId' OR message_id = '')";

			$db_klas2->query($sqlUpload);			
		}
		else {
			$sql3 = "UPDATE pg_messages
			SET subject = '$subject', message = '$message', message_date = '$curdatetime', status = 'NEW', status_date = '$curdatetime'
			WHERE id = '$messageId'";
			
			$dba->query($sql3);
			
			$sql4 = "DELETE FROM pg_messages_detail
			WHERE message_id = '$messageId'";
			
			$dba->query($sql4);
			
			foreach ($staffid as $recipient) {	
			
				if (!empty($recipient)) {	
					$newMessageDetailId = runnum2('id','pg_messages_detail');
					
					$sq5="INSERT INTO pg_messages_detail
					(id, message_id, recipient, recipient_status, recipient_status_date, sender_status, sender_status_date)
					VALUES('$newMessageDetailId','$messageId', '$recipient', 'NEW' , '$curdatetime', 'NEW', '$curdatetime')";
					$dba->query($sq5);			
				}
			}
			$sqlUpload = "UPDATE file_upload_inbox
			SET message_id = '$messageId'
			WHERE user_id = '$user_id'
			AND (message_id = '$messageId' OR message_id = '')";

			$db_klas2->query($sqlUpload);						
		}
		$msg[] = "<div class=\"success\"><span>The message has been sent to the recipient successfully.</span></div>";	
		$_POST['staff_id']="";
		$_POST['staff_name']="";
		$_POST['messageId']="";
		$_POST['subject']="";
		$_POST['message']="";		
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
   	<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
	<link rel="stylesheet" href="../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
	<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
	<script src="../../../lib/js/jquery.min2.js"></script>
    <script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
	<script language="JavaScript" src="../../../lib/js/windowopen.js"></script>
	<script src="../../../lib/js/jquery.colorbox.js"></script>

    <script>
  
		$(document).ready(function(){
		      //$(".select_user").colorbox({width:"60%", height:"40%", iframe:true});
              
              $.fn.getParameterValue = function(data,data2) {
                  //alert(data + ' - ' + data2);
                  document.form1.staff_id.value = data;
				  document.form1.staff_name.value = data2;
                };
              
               $(".select_user").colorbox({width:"80%", height:"90%", iframe:true,          
               onClosed:function(){ 
                //location.reload(true); //uncomment this line if you want to refresh the page when child close
                                
                } }); 
				
				$.fn.getParameterValue2 = function(data,data2) {
                  //alert(data);
				  //alert(data2);
                  document.form1.staff_id.value = data;
				  document.form1.staff_name.value = data2;
				  //$("#"+data2).val(data);
				  //$("#"+data4).val(data3);

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
			<td bgcolor="#5B74A8"><span class="style3"><font color="#FFFFFF">From</font></span></td>
			<td bgcolor="#DBDBDB"><?=convertname($user_id)?></td>
		</tr>
		<tr>
			<input type="hidden" name="messageId" id="messageId" value="<?=$_POST['messageId']?>"></input>
			<td bgcolor="#5B74A8"><span class="style3"><font color="#FFFFFF">To</font></span></td>
			<td bgcolor="#DBDBDB"><input id ="staff_name"  type="text" size="100" name="staff_name" value="<?=$_POST['staff_name']?>" />
			<a class='select_user' href="../../application/inbox/select_receiver.php">[ Select Recipient ]</a></td>
			<input id ="staff_id" type="hidden" size="100" name="staff_id" value="<?=$_POST['staff_id']?>" /></input>
		</tr>
			<tr>
			  <td bgcolor="#5B74A8"><span class="style3"><font color="#FFFFFF">Subject</font></span></td>
			  <td bgcolor="#DBDBDB"><input type="text" name="subject" size = "120" id = "subject" value="<?=$_POST['subject']?>"/></td>
		</tr>
			<tr>
			  <td bgcolor="#5B74A8"><span class="style3"><font color="#FFFFFF">Message</font></span></td>
			  <td bgcolor="#DBDBDB"><textarea name="message" id="message" cols="50" class = "ckeditor" rows="8"><?=$_POST['message']?></textarea></td>

		</tr>
	</table>
	<?
	$sqlUpload="SELECT * FROM file_upload_inbox 
	WHERE user_id = '$user_id' 
	AND (message_id = '".$_POST['messageId']."'	OR message_id ='')";			

	$result = $db->query($sqlUpload); 
	$row_cnt_attachment = mysql_num_rows($result);
	?>
	<table>
			<tr>
			<?if ($row_cnt_attachment > 0) {?>
				<td><button type="button" name="btnAttachment" onclick="javascript:document.location.href='../inbox/new_message_attachment.php';">Attachment <FONT COLOR="#FF0000"><sup>(<?=$row_cnt_attachment?>)</sup></FONT></button></td>
			<?}
			else {
				?>
				<td><input type="button" name="btnAttachment" value="Attachment" onclick="javascript:document.location.href='../inbox/new_message_attachment.php';" /></td>
				<?
			}?>
		</tr>
	  </table>

				
	  </fieldset>
		  
	   <table width="856" height="39">
			<tr>				
				<td width="853" height="30"><input type="submit" name="btnSubmit" id="btnSubmit" align="center"  value="Send" />
				<input type="submit" name="btnNew" id="btnNew" align="center"  value="Clear" /> <span style="color:#FF0000">Note:</span> 'Clear' will remove all the above entries from the form.</td>
			</tr>
	  </table>
	  

		
</form>
</body>
</html>
