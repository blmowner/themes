<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: draft_message.php
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

if(isset($_POST['btnDelete']) && ($_POST['btnDelete'] <> ""))
{
	
	$messageBox=$_POST['messageBox'];
	$msg=array();
	
	if (sizeof($_POST['messageBox'])>0) {
		while (list ($key,$val) = @each ($messageBox)) 
		{
			$sql1 = "DELETE FROM pg_messages
			WHERE id='$messageBox[$key]'
			AND STATUS = 'SAV'";
			
			$dbg->query($sql1); 
			
			$sql2 = "DELETE FROM pg_messages_detail
			WHERE message_id='$messageBox[$key]'
			AND STATUS = 'SAV'";
			
			$dbg->query($sql2); 
			
		}
		$msg[] = "<div class=\"success\"><span>The selected message has been deleted from the list successfully.</span></div>";
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please select the message from the list before click DELETE button.</span></div>";
	}
}

$sql = "SELECT id, sender, subject, DATE_FORMAT(message_date,'%d-%b-%Y %h:%i %p') AS message_date
FROM pg_messages 
WHERE sender = '$user_id'
AND status = 'SAV'
ORDER BY message_date DESC";

$result_sql = $db->query($sql);
$db->next_record();
$row_cnt = mysql_num_rows($result_sql);



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
		<legend><strong>List of Draft Messages</strong></legend>
		<table border="1" style="border-collapse:collapse;" cellpadding="3" cellspacing="1" width="100%">
			<tr>
				<td align="center"><label>Tick</label></td>
				<td><label>Subject</label></td>
				<td><label>Date Saved</label></td>				
				<td><label>Action</label></td>		
				
			</tr>					
			<?
			if ($row_cnt > 0) {?>
				<?do {
						$messageId = $db->f('id');
						$sender = $db->f('sender');
						$subject = $db->f('subject');
						$messageDate = $db->f('message_date');
						
						?>
						
							<tr>
							<td align="center"><input type="checkbox" name="messageBox[]" id="messageBox" value= "<?=$messageId;?>"/></td>
							<td><label><?=$subject?></label></td>
							<td><label><?=$messageDate?></label></td>				
							<td><label><a href="draft_message_detail.php?mid=<?=$messageId;?>"> View Message</a></label></td>
						
						</tr>	
			
					<?} while ($db->next_record());?>	
					<table>
						<tr>				
							<td width="853" height="30"><input type="submit" name="btnDelete" id = "btnDelete" value="Delete"/><span style="color:#FF0000"> Note:</span> Please select the message from the list before click DELETE button.</td>
						</td>
						</tr>
				  </table>
			<?}
			else {
				?>
				<table>
					<tr>
						<td><label>No record found!</label></td>
					</tr>
				</table>
				<?
			}?>
		</table>
		
	</fieldset>

		
</form>
</body>
</html>
