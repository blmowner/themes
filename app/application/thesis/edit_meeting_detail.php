<?php

/** * @author MJMZ
 * @copyright 2011
 */
	include("../../../lib/common.php");
	checkLogin();
	
	echo "XXXXXX";
    echo $id = $_GET['id'];
	echo $myDate = $_GET['dt'];exit();
    $sql_delete_attachment = "UPDATE FROM pg_meeting_detail
							SET 
							WHERE id = '".$_GET['id']."'";
	
	$db->query($sql_delete_attachment);
    //$db_klas2->next_record();
			
    
    ?>
		
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Meeting Details</title>
<link rel="stylesheet" type="text/css" href="../../theme/css/<?php echo $css; ?>" />
<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>
</head>

<body>

		<fieldset>
		<legend><strong><span style="color:#FF0000">Notification Message</span></strong></legend>
		<table>
			<tr>
				<td>The meeting detail has been updated successfully.</td>
			</tr>
		</table>
		<br/>
		<table>
			<tr>		
				<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='../student/student_programme.php';" /></td>		
			</tr>
		</table>
	</fieldset>
</body>