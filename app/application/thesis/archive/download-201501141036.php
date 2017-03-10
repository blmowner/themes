<?php

/** * @author MJMZ
 * @copyright 2011
 */
	include("../../../lib/common.php");
	checkLogin();
    
    $download_sql = "SELECT fu_cd,fu_download_fileName,fu_download_fileType,fu_download_fileData FROM fu_upload_proposal WHERE fu_cd = '".$_GET['id']."'";
    $db_klas2->query($download_sql);
    $db_klas2->next_record();
    $row_download = $db_klas2->rowdata();				
    
    $fileName = $row_download["fu_download_fileName"];
    $fileType = $row_download["fu_download_fileType"];
    $fileData = $row_download["fu_download_fileData"];
    
    //header('Content-length:' . $fileSize);
    header('Content-type:' . $fileType);
    header('Content-Disposition: attachment; filename=' . $fileName); 
    echo $fileData;

?>