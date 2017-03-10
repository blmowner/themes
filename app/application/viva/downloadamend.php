<?php

/** * @author MJMZ
 * @copyright 2011
 */
	include("../../../lib/common.php");
	checkLogin();
    
    $download_sql = "SELECT fu_cd,fu_document_filename, fu_document_filetype, fu_document_filedata 
	FROM file_upload_amendment
	WHERE fu_cd = '".$_GET['id']."'";
	
	$db->query($download_sql);
    $db->next_record();
    $row_download = $db->rowdata();				
    
    $fileName = $row_download["fu_document_filename"];
    $fileType = $row_download["fu_document_filetype"];
    $fileData = $row_download["fu_document_filedata"];
    
    //header('Content-length:' . $fileSize);
    header('Content-type:' . $fileType);
    header('Content-Disposition: attachment; filename=' . $fileName); 
    echo $fileData;

?>