<?php

/** * @author MJMZ
 * @copyright 2011
 */
	include("../../../lib/common.php");
	checkLogin();
	
	//var_dump($_GET);
	
	$download_sql = "SELECT fu_cd,fu_document_filename,fu_document_filetype,fu_document_filedata 
	FROM file_upload_proposal 
	WHERE pg_proposal_id = '".$_GET['pid']."'
	AND attachment_level='".$_GET['al']."'";
	
	//echo 'download_sql '.$download_sql;exit();
	
	$result_download_sql = $db->query($download_sql);
	$row_cnt = mysql_num_rows($result_download_sql);
	if ($row_cnt>0) {
	
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
	}
	else{
		?>
		<fieldset>
			<legend><strong>Notification Message</strong></legend>				
			<table>
				<tr>
					<td>No attachment available to download!</td>
				</tr>
			</table>
		</fieldset>
		<br/>
		<table>					
			<tr>				
				<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='assign_supervisor.php';" /></td>							
			</tr>
		</table>
	<?}


?>