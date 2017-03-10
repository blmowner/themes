<?php

//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: new_message.php
//
// Created by: Mohd Nizam
// Created Date: 08-April-2015
// Modified by: Zuraimi
// Modified Date: 27-April-2015
//
//**************************************************************************************

//include("../../../lib/common.php");
//checkLogin();

//session_start();
//$user_id=$_SESSION['user_id'];

	
	$curdatetime = date("Y-m-d H:i:s");
	$curdatetime1 = date("d-m-Y H:i A");
	$subject = "Amendment on Thesis Submitted by ".$studentName." Confirmed";
	//$empid = convertid($staff_id);		
	//$staffid = explode(",", $_POST['staff_id']);				
			
	$newMessageId = "I".runnum2('id','pg_messages');
	
	$message = "<div>
					  <div><span color=\"#837E7E\" size=\"-1\" face=\"Trebuchet MS, Arial, Helvetica, sans-serif\" data-mce-style=\"color: #837e7e; font-family: Trebuchet MS,Arial,Helvetica,sans-serif;\">
						<center>
						  <img src=\"http://www.msu.edu.my/v9/images/logo2t.png\" alt=\"Management &amp; Science University\" data-mce-src=\"http://www.msu.edu.my/v9/images/logo2t.png\" /> <br />
						  <hr noshade=\"noshade\" size=\"1\" />
						  <br>
						</center>
						<table>
						<tr>
							<td><font face=\"Verdana, Arial, Helvetica, sans-serif\">\r\nDear ".$studentName.",</font>\r\n\n<td>
						</tr>
						<tr><br>
						</tr>
						<tr>
							<td><font face=\"Verdana, Arial, Helvetica, sans-serif\">
							\r\nThe amendment on thesis status as follows :-.\r\n\n</font></td>
						 </tr>
						 <tr>
							<td>&nbsp;</td>
						 </tr>
						 </table>
						<table style =\"table-layout: auto;\" width = \"80%\">
						<tr>
						  
						  <td bgcolor=\"#5B74A8\" width = \"20%\"><strong><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\">Student Name </font></strong></td>
						  <td bgcolor=\"#DBDBDB\" width = \"60%\"><font face=\"Verdana, Arial, Helvetica, sans-serif\">".$studentName."</font></td>
						  </tr>
						<tr>
						  <td bgcolor=\"#5B74A8\" width = \"20%\"><strong><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\">Matric No </font></strong></td>
						  <td bgcolor=\"#DBDBDB\"><font face=\"Verdana, Arial, Helvetica, sans-serif\">".$matrixNo."</font></td>
						  </tr>
						<tr>
						  <td bgcolor=\"#5B74A8\" width = \"20%\"><strong><font color=\"#FFFFFF\"face=\"Verdana, Arial, Helvetica, sans-serif\">Thesis ID </font></strong></td>
						  <td bgcolor=\"#DBDBDB\"><font face=\"Verdana, Arial, Helvetica, sans-serif\">".$thesisId."</font></td>
						  </tr>
						<tr>
						  <td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\">
						  Thesis/Project Title </font></strong></td>
						  <td bgcolor=\"#DBDBDB\"><font face=\"Verdana, Arial, Helvetica, sans-serif\">".mysql_real_escape_string($thesisTitle)."</font></td>
						</tr>
						<tr>
						  <td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\">
						  Session Date</font></strong></td>
						  <td bgcolor=\"#DBDBDB\"><font face=\"Verdana, Arial, Helvetica, sans-serif\">".$vivaDate.", ".$vivaSTime." to ".$vivaETime.", ".$venue."</font></td>
						</tr>  
						 <tr>
						  <td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\">Submit Date </font></strong></td>
						  <td bgcolor=\"#DBDBDB\"><font face=\"Verdana, Arial, Helvetica, sans-serif\">".$curdatetime1."</font></td>
						 </tr>
						 <tr>
						  <td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\">Amendment Status</font></strong></td>
						  <td bgcolor=\"#DBDBDB\"><font face=\"Verdana, Arial, Helvetica, sans-serif\">Confirmed</font></td>
						 </tr>
						                      
					  </table>
						<div><br />
					  </div>
					   
					  <font face=\"Verdana, Arial, Helvetica, sans-serif\">

					  <br>Thank you,\r\n</font>
					  <div><br><font face=\"Verdana, Arial, Helvetica, sans-serif\">Best Regards,</font><br /></div>
					  <div><br><font face=\"Verdana, Arial, Helvetica, sans-serif\">".$staffName."</font><br /></div>
					  <div><br><font face=\"Verdana, Arial, Helvetica, sans-serif\">Management &amp; Science University</font><br /></div>
					  <div><br />
						<center>
						  <div><br /></div>
						  <hr noshade=\"noshade\" size=\"1\" />
						  <font size=\"-1\" color=\"#837E7E\" face=\"Verdana, Arial, Helvetica, sans-serif\">
								Please do not reply directly to this email. &copy; MSU " . date('Y') . "All rights reserved.<br><br>
								Management & Science University.<br />
								University Drive, Off Persiaran Olahraga,<br />
								Section 14, 40100 Shah Alam,<br />
								Selangor Darul Ehsan.<br />
						  </font>
						</center>
					  </div>
					</div>
					";
								
	$sql="INSERT INTO pg_messages
	(id, sender, subject, message, message_date, status, status_date)
	VALUES('$newMessageId','$user_id','$subject','$message', '$curdatetime','NEW', '$curdatetime')";
	$dba->query($sql);	
	
	$newMessageDetailId = runnum('id','pg_messages_detail');
	
	$sq2="INSERT INTO pg_messages_detail
		(id, message_id, recipient, recipient_status, recipient_status_date, sender_status, sender_status_date)
		VALUES('$newMessageDetailId','$newMessageId', '$matrixNo', 'NEW', '$curdatetime', 'NEW', '$curdatetime')";
	$dbf->query($sq2);
	
	/*if($dept == 'GSM')
	{
		$sqlstaff = "SELECT empid FROM new_employee WHERE unit_id = '$dept'
		UNION
		SELECT empid FROM new_employee WHERE unit_id = 'ACADOF'";
		$sqlstaff = "SELECT const_value FROM base_constant WHERE const_term = 'MESSAGE_FACULTY_GSM'";
			
	}
	else if($dept == 'SGS')
	{
		$sqlstaff = "SELECT empid FROM new_employee WHERE unit_id = '$dept'
		UNION
		SELECT empid FROM new_employee WHERE unit_id = 'ACADOF'";
		$sqlstaff = "SELECT const_value FROM base_constant WHERE const_term = 'MESSAGE_FACULTY_SGS'";
 
	}
	
	$sqlstaff  = $dba->query($sqlstaff);
	$dba->next_record();
	$allempid =$dba->f('const_value');
				
	$facuId = explode(" , ", $allempid);
	$facuId[0]; /// email = Prof gapar
	$facuId[1]; /// email = prof ali
	foreach($facuId as $empid)
	{
		
		
		$sq2="INSERT INTO pg_messages_detail
		(id, message_id, recipient, recipient_status, recipient_status_date, sender_status, sender_status_date)
		VALUES('$newMessageDetailId','$newMessageId', '$empid', 'NEW', '$curdatetime', 'NEW', '$curdatetime')";
		$dbf->query($sq2);
	} 
	*/		

	/*$sqlUpload = "UPDATE file_upload_inbox
	SET message_id = '$newMessageId'
	WHERE user_id = '$user_id'
	AND (message_id = '$messageId' OR message_id = '')";

	$db_klas2->query($sqlUpload);*/			



?>	






