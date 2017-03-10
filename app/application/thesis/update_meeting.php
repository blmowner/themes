<html><body>0000</body></html>
<?php

/**
 * @author softboxkid
 * @copyright 2011
 */

include("../../../lib/common.php");
checkLogin(); 

if (isset($_POST['id'])) {
	
		echo $sql = " UPDATE pg_meeting_detail 
				SET lecturer_name = '" . $_POST['lecturer_name'] . "', remark = '" . $_POST['remark'] . "',
				meeting_sdate = STR_TO_DATE('" . $_POST['meeting_date']." ".$_POST['meeting_time'] "','%d/%m/%Y %H:%i')
				WHERE id = '" . $_POST['id'] . "' ";
		$db->query($sql);
	
}
?>