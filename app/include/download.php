<?php

/**
 * @author MJMZ
 * @copyright 2011
 */
 
    include("../../lib/common.php");
    checkLogin();
    
    //ob_clean();
	
	$arr = $_GET['theArray'];
	
    
    $$arr = str_replace(" ",", ",$arr);

    
    $download_sql = "SELECT ".$$arr." FROM ".$_GET['table']." WHERE ".$_GET['where']." = '".$_GET['id']."'";
    $db->query($download_sql);
    $db->next_record();
	$row_download = $db->rowdata();				
	
    
    $fileName = $row_download[0];
    $fileType = $row_download[1];
    $fileData = $row_download[2];
	$fileSize = $row_download[3];
    
    
	/*header('Content-length:' . $fileSize);
	header('Content-type:' . $fileType);
	header('Content-Disposition: attachment; filename=' . $fileName); 
	echo $fileData;*/



    header("Pragma: public"); // required 
    header("Expires: 0"); 
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
    header("Cache-Control: private",false); // required for certain browsers 
    header("Content-Type: $fileType"); 
    header("Content-Disposition: attachment; filename=\"".basename($fileName)."\";" ); 
    header("Content-Transfer-Encoding: binary"); 
    ob_clean(); 
    flush();
    echo $fileData;


?>