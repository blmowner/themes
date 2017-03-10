<?php

/** * @author MJMZ
 * @copyright 2011
 */
	include("../../../lib/common.php");
	checkLogin();
    
    $download_sql = "SELECT fu_cd,fu_document_filename, fu_document_filetype, fu_document_filedata 
	FROM file_upload_biodata
	WHERE fu_cd = '".$_GET['fc']."'";
	
	$db_klas2->query($download_sql);
    $db_klas2->next_record();
    $row_download = $db_klas2->rowdata();				
    
    $fileName = $row_download["fu_document_filename"];
    $fileType = $row_download["fu_document_filetype"];
    $fileData = $row_download["fu_document_filedata"];
    
    //header('Content-length:' . $fileSize);
    header('Content-type:' . $fileType);
    header('Content-Disposition: attachment; filename=' . $fileName); 
    echo $fileData;

?>