<?php

/**
 * @author softboxkid
 * @copyright 2011
 */

include("../../../lib/common.php");
checkLogin(); 

if (isset($_POST['allToDelete'])) {
	foreach ($_POST['allToDelete'] as $data) {
		$sql = " DELETE FROM pg_meeting_detail WHERE id = '" . $data . "' ";
		$db->query($sql);
		//print_r ($_POST['allToDelete']);
	}
}
?>