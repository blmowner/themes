<?php

/**
 * @author MJMZ
 * @copyright 2011
 */
 ob_start();
    include("../lib/common.php");
    checkLogin();
    //ob_start();
    $arr = $_GET['theArray'];
    
    $$arr = str_replace(" ",", ",$arr);

    
    $download_sql = "SELECT ".$$arr." FROM ".$_GET['table']." WHERE ".$_GET['where']." = '".$_GET['id']."'";
    
	$db->query($download_sql);
    $db->next_record();
	$row_download = $db->rowdata();				
	
    
    $fileName = $row_download[0];
    $fileType = $row_download[1];
	$fileSize = $row_download[2];
	$fileData = $row_download[3];
    
   
	header('Content-length:' . $fileSize);
	header('Content-type:' . $fileType);
	header('Content-Disposition: attachment; filename=' . $fileName); 
	echo $fileData;
	
?>
