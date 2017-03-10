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
$messageId = $_GET['mid'];
$messageDetailId = $_GET['mdid'];


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


$sql = "SELECT a.id, a.subject, a.message, DATE_FORMAT(a.message_date,'%d-%b-%Y %h:%i %p') AS message_date,
b.recipient_status, DATE_FORMAT(b.recipient_status_date,'%d-%b-%Y %h:%i %p') AS recipient_status_date
FROM pg_messages a
LEFT JOIN pg_messages_detail b ON (b.message_id = a.id)
WHERE a.id = '$messageId'
AND b.id = '$messageDetailId'";

$sql_result = $db->query($sql);
$db->next_record();

$theMessageId = $db->f('id');
$theSubject = $db->f('subject');
$theMessage = $db->f('message');
$theMessageDate = $db->f('message_date');
$theRecipientStatus = $db->f('recipient_status');
$theRecipientStatusDate = $db->f('recipient_status_date');


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
        <?if ($theRecipientStatus != 'NEW') {?>
        <td bgcolor="#DBDBDB"><label>
          <?=$theRecipientStatusDate?>
        </label></td>
        <?}
			else {
				?>
        <td bgcolor="#DBDBDB"><label></label></td>
        <?
			}?>
      </tr>
      <tr>
        <td bgcolor="#5B74A8"><span class="style3"><font color="#FFFFFF">From</font></span></td>
        <td bgcolor="#DBDBDB"><label>
          <?=convertname($user_id)?>
        </label></td>
        <input type="hidden" name="userId" id="userId" value = "<?=$user_id?>" />
      </tr>
      <tr>
        <input type="hidden" name="messageId" id="messageId" value="<?=$theMessageId?>" />
        <?
			$sql1 = "SELECT recipient
			FROM pg_messages_detail
			WHERE message_id = '$theMessageId'";
			
			$result_sql1 = $dba->query($sql1);
			$dba->next_record();

			do {
				$userId = $dba->f('recipient');
				$userName = convertname($userId);
				$theUserName = $userName.', '.$theUserName;
			} while ($dba->next_record());
			
			?>
        <td bgcolor="#5B74A8"><span class="style3"><font color="#FFFFFF">To</font></span></td>
        <td bgcolor="#DBDBDB"><input id ="staff_id"  type="text" size="100" name="staff_id" value="<?=$theUserName?>" /></td>
      </tr>
      <tr>
        <td bgcolor="#5B74A8"><span class="style3"><font color="#FFFFFF">Subject</font></span></td>
        <td bgcolor="#DBDBDB"><input type="text" name="subject" size = "120" id = "subject" value="<?=$theSubject?>"/></td>
      </tr>
      <tr>
        <td bgcolor="#5B74A8"><span class="style3"><font color="#FFFFFF">Message</font></span></td>
        <td bgcolor="#DBDBDB" width="800"><div style="background-color:#FFFFFF;margin:10px;">
          <?=$theMessage?>
        </div></td>
      </tr>
      <tr>
        <td bgcolor="#5B74A8"><span class="style3"><font color="#FFFFFF">Attachment</font></span></td>
        <?php
				$sqlUpload="SELECT * FROM file_upload_inbox 
				WHERE user_id = '$user_id' 
				AND message_id = '$theMessageId'";			

				$result = $db_klas2->query($sqlUpload); //echo $sql;
				$row_cnt = mysql_num_rows($result);
				$attachmentNo1=1;
				if ($row_cnt>0)
				{
					?>
        <td width="128" align="left"><?
					while($row = mysql_fetch_array($result)) 					
					{ 
						$namefile = $row['fu_document_filename'];
						?>
            <a href="download.php?fc=<?=$row["fu_cd"];?>&al=S" title="File Description: <?=$row["fu_document_filedesc"];?>">
              <?=$namefile;?>
              : <img src="../images/download.png" width="20" height="19" style="border:0px;" title="Download <?=$row["fu_document_filename"];?>" /></a>
            <?}
					?></td>
        <?
				}
				else {
					?>
        <td bgcolor="#DBDBDB">No attachment</td>
        <?
				}
			?>
      </tr>
    </table>
	<table width="856" height="39">
			<tr>				
				<td><input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../inbox/sent_message.php';" /></td>
			</tr>
    </table>
  </fieldset>

		
</form>
</body>
</html>
