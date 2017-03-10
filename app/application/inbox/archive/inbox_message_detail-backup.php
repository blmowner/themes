<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: sent_message_detail.php
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
$messageDetailId = $_GET['mdid'];


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


$sql = "SELECT a.id, a.message_id, b.sender, a.recipient, b.subject, b.message, DATE_FORMAT(b.message_date,'%d-%b-%Y %h:%i %p') AS message_date
FROM pg_messages_detail a
LEFT JOIN pg_messages b ON (b.id = a.message_id) 
WHERE a.id = '$messageDetailId'";

$result_sql = $db->query($sql);
$db->next_record();

$theMessageDetailId = $db->f('id');
$theSender = $db->f('sender');
$theRecipient = $db->f('recipient');
$theSubject = $db->f('subject');
$theMessage = $db->f('message');
$theMessageDate = $db->f('message_date');

$curdatetime = date("Y-m-d H:i:s");

$sql1 = "UPDATE pg_messages_detail
SET recipient_status = 'VIU', recipient_status_date = '$curdatetime'
WHERE id = '$messageDetailId'
AND recipient_status IN ('NEW', 'VIU')";

$result_sql1 = $db->query($sql1);

$sql2 = "SELECT DATE_FORMAT(recipient_status_date,'%d-%b-%Y %h:%i %p') AS recipient_status_date
FROM pg_messages_detail
WHERE id = '$messageDetailId'";

$result_sql2 = $db->query($sql2);
$db->next_record();
$theViewedDate = $db->f('recipient_status_date');

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

</head>
<body>
<form method="post" id="form1" name="form1" enctype="multipart/form-data">			
	<fieldset>
	<table>
		<tr>
			<td bgcolor="#5B74A8"><span class="style3"><font color="#FFFFFF">Viewed Date</font></span></td>
			<td bgcolor="#DBDBDB"><label><?=$theViewedDate?></label></td>
		</tr>
		<tr>
			<td bgcolor="#5B74A8"><span class="style3"><font color="#FFFFFF">From</font></span></td>
			<td bgcolor="#DBDBDB"><label><?=convertname($theSender)?></label></td>
		</tr>
		<tr>			
			<?$userName = convertname($theRecipient);?>
			<td bgcolor="#5B74A8"><span class="style3"><font color="#FFFFFF">To</span></td>
			<td bgcolor="#DBDBDB"><input id ="staff_id"  type="text" size="100" name="staff_id" value="<?=$userName?>" /></td>
		</tr>
			<tr>
			  <td bgcolor="#5B74A8"><span class="style3"><font color="#FFFFFF">Subject</font></span></td>
			  <td bgcolor="#DBDBDB"><input type="text" name="subject" size = "120" id = "subject" value="<?=$theSubject?>"/></td>
		</tr>
			<tr>
			  <td bgcolor="#5B74A8"><span class="style3"><font color="#FFFFFF">Message</font></span></td>
			  <td bgcolor="#DBDBDB"><textarea name="message" id="message" cols="50" class = "ckeditor" rows="8"><?=$theMessage?></textarea></td>

		</tr>
			<tr>
			  <td bgcolor="#5B74A8"><span class="style3"><font color="#FFFFFF">Attachment</font></span></td>
		<?php 
    	echo $sql_file = "SELECT * FROM file_upload_inbox 
		WHERE SUBSTRING(message_id,2,11) = '$messageDetailId'";
		$result_sql_file = $dba->query($sql_file);
		//$dba->next_record();
		//$row = mysql_fetch_assoc($result_sql_file);
		$row_cnt = mysql_num_rows($result_sql_file);
		?> <td bgcolor="#DBDBDB"> <?
		if ($row_cnt > 0)
		{
			while($row = mysql_fetch_assoc($result_sql_file))
			{
				$inbox_id = $row['message_id'];
				$inboxname = $row['fu_document_filename'];

		 ?>

			  <a href="download.php?id=<?=$inbox_id; ?>" target="_blank"><?php echo $inboxname ?> </a>
		 
		 <?	} //&table=file_upload_inbox&where=message_id&theArray=fu_document_filename+fu_document_filetype+fu_document_filedata
		}
		 else
		 { ?>
		 	<td bgcolor="#DBDBDB"></td>
			<?
		 }
		  ?></td>
			</tr>
	  </table>
	  
		  
	   <table width="856" height="39">
			<tr>				
				<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../inbox/inbox.php';" /></td>
			</tr>
	  </table>
	  </fieldset>

		
</form>
</body>
</html>
