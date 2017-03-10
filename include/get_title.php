<?php

/**
 * @author MJMZ
 * @copyright 2011
 */
include("../lib/common.php");
checkLogin();

$_GET_TITLE = "SELECT text FROM base_language_text WHERE language_code='".$lang."' AND variable='".$_GET['SUB_MENU_ID']."'";
$db->query($_GET_TITLE);
$db->next_record();

echo $db->f("text");
?>