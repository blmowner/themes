<?php

include("../../../lib/common.php");
checkLogin();
session_start();
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
$userid=$_REQUEST['uid'];
$pgProposalId=$_REQUEST['pid'];
$notification = "SELECT COUNT(*) FROM messages WHERE status = 0 AND process_status = 'Submit' AND receive_by = '$user_id'";
$result_sql_notification = $db->query($notification);
$row_area = $db->fetchArray();
$row_cnt = mysql_num_rows($result_sql_notification);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang; ?>" lang="<?php echo $lang; ?>">

<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
	<meta name="author" content="MJMZ" />

	<title>New Mail</title>
    <link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
	 <link rel="stylesheet" type="text/css" href="../../../theme/css/default.css" />
    <link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
	<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>   
    <script src="../../../lib/js/jquery.min2.js"></script>
	<script src="../../../lib/js/jquery.colorbox.js"></script>
    <script>
  
	</script>
</head>
<body>

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
<form method="post" id="form1" name="form1" action = "">

<div class = 'notify'>	
  <p>Inbox<? echo mysql_result($result_sql_notification, 0); ?></p>
</div>
<div class = 'top1'>
	<h1> Notification Testing </h1>
	</hr>
	 <?
			
		$notification1 = "SELECT sent_by, id FROM messages WHERE status = 0 AND process_status = 'Submit' AND user_id = '$user_id'";
		$result_sql_notification1 = $db->query($notification1);
		//$row_area = $db->fetchArray();
		$db->next_record();	
	?>			
</div>
<? 			do{
			
   			$from = $db->f("sent_by");
			$id = $db->f("id");
			?>
<p>
<div class = 'note'>
		
		<?php echo '<td><a href = "?msg='.$id.'">'.$from.'</a></td>';?> Has Sent a Message!
		<input type="hidden" name="id" value= "<?=$id;?>" />
</div>
</p>
<? }while($db->next_record());?>

<?php ?>
<p>
<?php
if(isset($_GET['msg']))
	{
		
		//$msg_id = $_GET['msg'];
		$id = $_REQUEST['msg'];
		$update = mysql_query("UPDATE messages SET status = '1' WHERE id = '$id'");
		$msge = mysql_query("SELECT * FROM messages WHERE id = '$id'");
		$row = mysql_fetch_assoc($msge);
		
			$id = $row['id'];
			$from = $row['sent_by'];
			$userID = $row['user_id'];
			$subject = $row['subject'];
			$message1 = $row['message'];
			$date = $row['date_time'];
		//$from = $row['from'];
	
?>
<div id = "msg">
  	<table width="944" height="281" bordercolor="#FFFFFF">
  	  <tr bgcolor="#CCCCCC">
	  
  	    <td height="21"><span class="style6">From:
  	        <?= $from; ?>
            <span class="note">
            <input type="text" name="id2" value= "<?=$id;?>" />
        </span></span></td>
  	    <td><span class="style6"><?= $date; ?>
        </span></td>
      </tr>
  	  <tr bgcolor="#CCCCCC">
	  <?php $name = convertname($user_id); ?>
        <td height="21" colspan="2"><span class="style6">To:
          <?= $name; ?>
          <input type="hidden" name="textfield" value ="<?= $userID; ?>" />        
          </span></td>
      </tr>
		<tr>
		  <td height="21" colspan="2" bgcolor="#CCCCCC"><span class="style6">Subject:
		      <?=$subject; ?>
          </span></td>
	  </tr>
		<tr>
		  <td height="185" bgcolor="#FFFFFF"><?=$message1?></td>
		  <td width="118" bgcolor="#CCCCCC"><p><a href="reply(0.1).php?id=<?=$id?>&amp;subject=<?=$subject?>&amp;from=<?=$from?>&amp;userID=<?=$userID?>">Reply Message </a></p>
	      <p><a class = "remove" href ="../../application/inbox/inbox.php?remove=<?=$id?>">Delete message</a></p></td>
	  </tr>
	</table>
		<a href="inbox.php?">&lt;--Back to Inbox </a>
</div>
</p>
 <?php 
  	exit(); }
?>

</form>
</body>
</html>
