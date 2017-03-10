<?php

include("../../../lib/common.php");
checkLogin();
ob_start();
session_start();
$userid=$_REQUEST['uid'];
$pgProposalId=$_REQUEST['pid'];

if(isset($_POST['btnDelete']) && ($_POST['btnDelete'] <> ""))
{
	 $msg = array();
	if (isset($_REQUEST['id'])) /* checks weather $_GET['empids'] is set */
	{
		$checkbox = $_REQUEST['id'];
		if (is_array($checkbox)) /* value is stored in $checkbox variable */
		{
			foreach ($checkbox as $key => $checkbox) /* for each loop is used to get id and that id is used to delete the record below */
  			{
				//echo $check = $_REQUEST['id'];
				
				$q="DELETE FROM pg_messages WHERE id = '$checkbox' ";/* Sql query to delete the records whose id is equal to $your_slected_id */
				$db->query($q); /* runs the query */
				
			}
			
		}
		$msg[] = "<div class=\"success\"><span>The Message has been Delete Successfully!</span></div>";
	}
	else 
	{	
		$msg[] = "<div class=\"error\"><span>There is no selected record</span></div>";
	}
}	

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
<?php

		$curdatetime = date("Y-m-d H:i:s");

		//$db->free();
	 // used for pagination
    	$page = ($_GET['page'] == 0 ? 1 : $_GET['page']);
    	$perpage = 5;
    	$startpoint = ($page * $perpage) - $perpage;
		$status = 0;
		$name = convertname($user_id);
		
	// count total number of result - without LIMIT
    	$count_total_result = "SELECT * FROM pg_messages";
    	$db->query($count_total_result);
    	$a = $db->num_rows($count_total_result);
    	$db->free();
    	//sql2 = "SELECT DATE_FORMAT(date_time, '%d/%m/%Y') FROM messages where `receive_by` = '$user_id'";
		
		
    //  sql for total number with LIMIT
	//s = attachment m = messages
	$sql2 = "SELECT ";
    	$sql = "SELECT *  
		FROM pg_messages 
		WHERE `receive_by` = '$user_id' 
		AND `process_status` = 'Submit' 
		ORDER BY `status` ASC,`date_time` DESC 
		LIMIT $startpoint, $perpage  ";
		$result_sql_msg = $db->query($sql);
		$db->next_record();
	
		
	
?>	

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
	<link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
	<link rel="stylesheet" type="text/css" href="../../../theme/css/default.css" />
	<link rel="stylesheet" type="text/css" href="../../../theme/css/CSSTableGenerator.css" />
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
.style6 {color: #000000; font-weight: bold; }
.style7 {color: #000000}
-->
    </style>
</head>
<body>
 <?php
    if(!empty($msg)) {
        foreach($msg as $err) 
		{
            echo $err;
        }
    }
?>
<script>

	$(document).ready(function() {         
		$('.error').hide();
		var showError = <?php echo $msg; ?>;         
		if (showError) {             
			$('.error').fadeIn(500).delay(5000).fadeOut(500);
			$msg.focus();       
		} else {
			
		}
	
	});				
</script>
 <form id="form1" name="form1" method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
 <input type="hidden" name="action" id="action" value="">
<?php 
	if(isset($_GET['msg']))
	{
		$msg_id = $_GET['msg'];
		$update = mysql_query("UPDATE pg_messages SET status = '1' WHERE id = '$msg_id'");
		$msge = mysql_query("SELECT * FROM pg_messages WHERE id = '$msg_id'");
		
		$row = mysql_fetch_assoc($msge);
		
			$id = $row['id'];
			$from = $row['sent_by'];
			$userID = $row['user_id'];
			$subject = $row['subject'];
			$message1 = $row['message'];
			$date = $row['date_time'];
		//$from = $row['from'];
		
?>
<?php 
		$time = strtotime($date);
		$myFormatForView = date("d/m/y g:i A", $time);  
?>
  <div id = "msg" class = 'msg'>
  	<table width="887" height="303" bordercolor="#FFFFFF">
  	  <tr bgcolor="#5B74A8">
  	    <td width="57" height="34"><span class="style4 style6"><span class="style4">From:
            
        </span></span></td>
  	    <td width="682"><span class="style4 style6"><span class="style4">
  	      <?= $from; ?>
  	    </span></span></td>
  	    <td width="132"><span class="style4 style6"><span class="style4">
  	      <?= $myFormatForView; ?>
        </span></span></td>
      </tr>
  	  <tr bgcolor="#5B74A8">
        <td height="21"><span class="style4 style6"><span class="style4">To:
          
                  
        </span></span></td>
        <td height="21" colspan="2"><span class="style4 style6"><span class="style4">
          <?= $name; ?>
          <input type="hidden" name="textfield" value ="<?= $userID; ?>" />
        </span></span></td>
      </tr>
		<tr bgcolor="#5B74A8">
		  <td height="21" bgcolor="#5B74A8"><p class="style3">Subject:
	        
	          
		  </p></td>
	      <td height="21" colspan="2" bgcolor="#5B74A8"><span class="style3">
	        <?=$subject; ?>
	      </span></td>
      </tr>
		<tr>
          <td height="185" colspan="3" bgcolor="#DBDBDB"><?=$message1?> </td>
	  </tr>
		<tr>
		<?php 
    	$sql_file = "SELECT *  
		FROM file_upload_inbox 
		WHERE inbox_id = '$id'";
		$result_sql_file = $dba->query($sql_file);
		//$dbc->next_record();
		$row = mysql_fetch_assoc($result_sql_file);
		$inbox_id = $row['inbox_id'];
		$inboxname = $row['file_name'];
		if (!empty($inbox_id))
		{
		 ?>
		  <td height="28" colspan="3" bgcolor="#B7B7FF"><a href="../../../include/download.php?id=<?php echo $inbox_id; ?>&table=file_upload_inbox&where=inbox_id&theArray=file_name+file_type+file_size+file_data" target="_blank"><?php echo $inboxname ?></a></td>
		 <? }
		 else
		 {
		 	
		 }
		  ?>
	  </tr>
	</table>
<a href="../../application/inbox/reply.php?id=<?=$id?>&subject=<?=$subject?>&from=<?=$from?>&userID=<?=$userID?>" class="style3"><button type="button">Reply</button></a>
<a href ="../../application/inbox/inbox.php?remove=<?=$id?>" class = "style4 remove" onclick = "return confirm('Do you wish to proceed?')" ><strong><button type="button" class>Delete</button></a>
		<input type="submit" name="Submit" value="Back" onclick="javascript: form.action='inbox.php';" />
  </div>
  
  
  <p>
    <?php 
  	exit(); }
?>
    <?php 
 
 	if(isset($_GET['remove']))
	{
		//$msg = array();
		$id = $_REQUEST['remove'];
		//echo $id;
		$remove = mysql_query("DELETE FROM pg_messages WHERE id = '$id' ");
		//$msg[] = "<div class=\"success\"><span>The Message has been Delete Successfully!</span></div>";
		header("Location: inbox.php");
		exit();
		
	}
 	
 ?>
    
    <?php  
 
	if(isset($_GET['replymsg']))
	{
		$id = $_GET['replymsg'];
		//$update = mysql_query("UPDATE pg_messages SET status = '1' WHERE id = '$msg_id'");

		
		$msge1 = mysql_query("SELECT * FROM pg_messages WHERE id = '$id'");
		
		$row1 = mysql_fetch_assoc($msge1);
		
		
			$from1 = $row1['sent_by'];
			$userID1 = $row1['user_id'];
			$subject = $row1['subject'];
			//$message = $row['message'];
			//$date = $row['date/time'];
		//$from = $row['from'];
		
  ?>
</p>
  <p><?php //echo $inboxid = "I".runnum('id','pg_messages'); ?> </p>
  <div id = "replymsg">
  <table width="992" height="54">
  	  <tr bgcolor="#5B74A8">
        <td width="69" height="23"><span class="style3">From:
          <?=convertname($user_id)?>
        </span></td>
  	    <td width="209"><span class="style3">Send To:
        <?= $userID1; ?>
  	    </span></td>
  	    <td width="289"><span class="style3">Sender ID:
  	      <?= $user_id; ?>
		  <?php $name = convertname($user_id); ?>
  	      <input type="hidden" name="name" value = "<?=convertname($user_id)?>" />
		   <input type="hidden" name="from" value = "<?=$from?>" />
  	    </span></td>
  	    <td width="314"><span class="style3">Subject:
  	      <input type="text" name="subject" id = "subject" value = "Reply: <?=$subject;?>" />
        </span></td>
  	    <td width="87"><p class="style3">Sent:
  	      <?= $curdatetime; ?>
        </p></td>
      </tr>
  	  <tr bgcolor="#000000">
  	    <td height="23" bgcolor="#CCCCCC"><?=$from?></td>
		 <?php //$message = $_SESSION['message'];  ?>
  	    <td colspan="3" bgcolor="#B7B7FF"><textarea name="message" cols="50" class = "ckeditor" rows="8" value = ""><?=$message;?>
  	    </textarea></td>
  	    <td bgcolor="#CCCCCC"><a class = "reply" href ="?reply=<?=$id?>&subject=<?=$subject?>&from=<?=$from?>&message=<?=$message?>&name=<?=$name?>">Reply </a>
		<br><a class = "remove" href ="#" onClick="javascript:document.getElementById('action').value='reply';document.getElementById('form1').submit();">Reply </a>		</td>
      </tr>
  </table>
<p><a href="inbox.php?">&lt;--Back to Inbox </a></p>
</div>

<p>
  <?php 
	exit(); 
}
 ?>
  
  <?php 
 	if(isset($_POST['action']) && $_POST['action']=="reply")
	{
			$id = $_REQUEST['reply'];
			//echo $message = $_REQUEST['message'];
			$inboxid = "I".runnum('id','pg_messages');
			$from = $_REQUEST['from'];
			$message = $_REQUEST['message'];
			$reply = "INSERT INTO pg_messages(id, `sent_by`, user_id, subject, message, `date_time`, status, `receive_by`)
			VALUES('$inboxid', '$name','$user_id', '$subject', '$message', '$curdatetime','0','$from')";
			
			//exit();
			$db_klas2->query($reply);
			//exit();	
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
</p>
<p></p>
<fieldset>
<legend><strong>View Inbox </strong></legend>	
<div class = "CSSTableGenerator">
	<table width="898">
		<tr bgcolor="#5B74A8">
		  <td width="34"><span class="style4"></span></td>
		    
	        <td width="295"><span class="style4"><strong>From</strong></span></td>
          <td width="313"><p class="style4"><strong>Subject</strong></p></td>
            <td width="145"><span class="style4"><strong>Sent</strong></span></td>
			<td width="87"><span class="style4"><strong>Status</strong></span></td>
      </tr>
		<?
	 		do{
			$id = $db->f("id");
   			$from=$db->f("sent_by");
   			$userID=$db->f("user_id");
   			$subject=$db->f("subject");
			$message=$db->f("message");
   			$date=$db->f("date_time");
			$status = $db->f("status");
			
			if($status == 0)
			{
				$open = "New";
				
			}
			else
			{
				$open = "Opened";
			}
   		?>
			<tr bgcolor="#DBDBDB">
		 	<?php 
					$time = strtotime($date);
					$myFormatForView = date("d/m/y g:i A", $time); 
					
			?>
		 	<td><input type="checkbox" name="id[]" value= "<?=$id;?>"/></td>
		<?
			//echo '<td><a href = "?msg='.$id.'">'.$id.'</a></td>';
			 
			echo '<td><a style="color: #000000" href = "read_inbox.php?msg='.$id.'">'.$from.'</a></td>';
			echo '<td><a style="color: #000000" href = "read_inbox.php?msg='.$id.'">'.$subject.'</a></td>';
			echo '<td><a style="color: #000000" href = "read_inbox.php?msg='.$id.'">'.$myFormatForView.'</a></td>';
			echo '<td><a style="color: #000000" href = "read_inbox.php?msg='.$id.'">'.$open.'</a></td>';
			echo '</tr>'

		?>
				<?
	 		}while($db->next_record());
			 
		?>
	</table>
 </div> 
	   <legend>
	   <input type="submit" name="btnDelete" value="Delete" onclick = "return confirm('Do you wish to proceed?:')" />
	   </legend>

       <p><?
			doPages($perpage, 'inbox.php', '', $a); 
		?></p>
		


	  		
</fieldset>
		</table>
	</fieldset>	

 
</form>
</body>
</html>
