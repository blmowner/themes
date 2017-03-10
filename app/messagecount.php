<?php

include("../lib/common.php");

$sql = "SELECT a.id, a.message_id, b.sender, b.subject, DATE_FORMAT(b.message_date,'%d-%b-%Y %h:%i %p') AS message_date, a.recipient_status,
c.description as message_detail_desc
FROM pg_messages_detail a
LEFT JOIN pg_messages b ON (b.id = a.message_id) 
LEFT JOIN ref_message_status c ON (c.id = a.recipient_status)
WHERE a.recipient = '$user_id'
AND a.recipient_status IN ('NEW')
ORDER BY a.recipient_status, b.message_date";

$result_sql = $db->query($sql);
$db->next_record();
$row_cnt = mysql_num_rows($result_sql);
if($row_cnt> 0)
{
	echo $row_cnt;
}
else
{
	echo $row_cnt = "";
}
//echo "NOBBB";
?>