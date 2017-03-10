<?php
//ini_set('display_errors', 'On');
//ini_set('html_errors', 0);
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: sent_message.php
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


if(isset($_POST['btnDelete']) && ($_POST['btnDelete'] <> ""))
{
	
	$messageBox=$_POST['messageBox'];
	$msg=array();
	$curdatetime = date("Y-m-d H:i:s");
	if (sizeof($_POST['messageBox'])>0) {
		while (list ($key,$val) = @each ($messageBox)) 
		{
			$sql1 = "UPDATE pg_messages_detail
			SET recipient_status = 'DEL', recipient_status_date = '$curdatetime'
			WHERE id='$messageBox[$key]'";
			
			$dbg->query($sql1); 			
		}
		$msg[] = "<div class=\"success\"><span>The selected message has been deleted from the list successfully.</span></div>";
	}
	else {
		$msg[] = "<div class=\"error\"><span>Please select the message from the list before click DELETE button.</span></div>";
	}
}

$sql = "SELECT a.id, a.message_id, b.sender, b.subject, DATE_FORMAT(b.message_date,'%d-%b-%Y %h:%i %p') AS message_date, a.recipient_status,
c.description as message_detail_desc
FROM pg_messages_detail a
LEFT JOIN pg_messages b ON (b.id = a.message_id) 
LEFT JOIN ref_message_status c ON (c.id = a.recipient_status)
WHERE a.recipient = '$user_id'
AND a.recipient_status IN ('NEW','VIU')
ORDER BY a.recipient_status, b.message_date";

$result_sql = $db->query($sql);
$db->next_record();
$row_cnt = mysql_num_rows($result_sql);

$idArray = Array();
$messageArray = Array();
$senderArray = Array();
$subjectArray = Array();
$messageDateArray = Array();
$detailStatusArray = Array();
$detailStatusDescArray = Array();
$no=0;
do {
	$idArray[$no] = $db->f('id');
	$messageArray[$no] = $db->f('message');
	$senderArray[$no] = $db->f('sender');
	$subjectArray[$no] = $db->f('subject');
	$messageDateArray[$no] = $db->f('message_date');
	$detailStatusArray[$no] = $db->f('recipient_status');
	$detailStatusDescArray[$no] = $db->f('message_detail_desc');
	$no++;
} while ($db->next_record());

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
		<legend><strong>List of Messages in Inbox</strong></legend>
		<div id = "tabledisplay" style="overflow:auto; height:400px;">
		<table border="1" style="border-collapse:collapse;" cellpadding="3" cellspacing="1" width="100%" class="thetable">
			<tr>
				<th align="center"><label>Tick</label></th>
				<th><label>Sender ID</label></th>
				<th><label>Sender</label></th>
				<th><label>Subject</label></th>
				<th><label>Date Received</label></th>				
				<th><label>Status</label></th>		
				
			</tr>		
			<?if ($row_cnt > 0) {?>
				<?for ($i=0; $i<$row_cnt; $i++){
					?>
					<tr>
							<td align="center"><input type="checkbox" name="messageBox[]" id="messageBox" value= "<?=$idArray[$i]?>"/></td>
							<td><label><?=$senderArray[$i]?></label></td>
							<?$userName = convertname($senderArray[$i]);?>
							<td><label><?=$userName?></label></td>
							<td><label><?=$subjectArray[$i]?></label></td>
							<td><label><?=$messageDateArray[$i]?></label></td>				
							<?if ($detailStatusArray[$i] != 'NEW') {?>
								<td><label><a href="inbox_message_detail.php?mdid=<?=$idArray[$i]?>"><?=$detailStatusDescArray[$i]?></a></label></td>
							<?}
							else {
								?>
								<td><label><a href="inbox_message_detail.php?mdid=<?=$idArray[$i]?>"><strong><?=$detailStatusDescArray[$i]?></strong></a></label></td>
								<?
							}?>
							
					</tr>	
					<?} ?>	
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
		</div>
	</fieldset>

		
</form>
</body>
</html>