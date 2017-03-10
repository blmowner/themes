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
	$subject = "Monthly Progress Report by ".$studname."";
	//$empid = convertid($staff_id);		
	//$staffid = explode(",", $_POST['staff_id']);				
			
	$newMessageId = "I".runnum3('id','pg_messages');

	$message = "<div>
					  <div><span color=\"#837E7E\" size=\"-1\" face=\"Trebuchet MS, Arial, Helvetica, sans-serif\" data-mce-style=\"color: #837e7e; font-family: Trebuchet MS,Arial,Helvetica,sans-serif;\">
						<center>
						  <img src=\"http://www.msu.edu.my/v9/images/logo2t.png\" alt=\"Management &amp; Science University\" data-mce-src=\"http://www.msu.edu.my/v9/images/logo2t.png\" /> <br />
						  <hr noshade=\"noshade\" size=\"1\" />
						  <br>
						</center>
						<table>
						<tr>
							<td><font face=\"Verdana, Arial, Helvetica, sans-serif\">\r\nDear Sir,</font>\r\n\n<td>
						</tr>
						<tr><br>
						</tr>
						<tr>
							<td><font face=\"Verdana, Arial, Helvetica, sans-serif\">
							\r\nI am pleased to submit the monthly progress report as follows :-.\r\n\n</font></td>
						 </tr>
						 <tr>
							<td>&nbsp;</td>
						 </tr>
						 </table>
						<table style =\"table-layout: auto;\">
						<tr>
						  
						  <td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\">Student Name </font></strong></td>
						  <td bgcolor=\"#DBDBDB\"><font face=\"Verdana, Arial, Helvetica, sans-serif\">".$studname."</font></td>
						  </tr>
						<tr>
						  <td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\">Matric No </font></strong></td>
						  <td bgcolor=\"#DBDBDB\"><font face=\"Verdana, Arial, Helvetica, sans-serif\">".$user_id."</font></td>
						  </tr>
						<tr>
						  <td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\"face=\"Verdana, Arial, Helvetica, sans-serif\">Thesis ID </font></strong></td>
						  <td bgcolor=\"#DBDBDB\"><font face=\"Verdana, Arial, Helvetica, sans-serif\">".$thesisId."</font></td>
						  </tr>
						  <tr>
						  <td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\"face=\"Verdana, Arial, Helvetica, sans-serif\">Thesis Title </font></strong></td>
						  <td bgcolor=\"#DBDBDB\"><font face=\"Verdana, Arial, Helvetica, sans-serif\">".mysql_real_escape_string($title)."</font></td>
						  </tr>
						  <tr>
						  <td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\">Month </font></strong></td>
						  <td bgcolor=\"#DBDBDB\"><font face=\"Verdana, Arial, Helvetica, sans-serif\">".$reportMonth." ".$reportYear."</font></td>
						  </tr>
						<tr>
						  <td bgcolor=\"#5B74A8\" width=\"137\"><strong><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\">
						  Reference No </font></strong></td>
						  <td bgcolor=\"#DBDBDB\"><font face=\"Verdana, Arial, Helvetica, sans-serif\">".$referenceNo."</font></td>
						</tr>
						<tr>
						  <td bgcolor=\"#5B74A8\" width=\"137\"><strong><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\">
						  Submission Date </font></strong></td>
						  <td bgcolor=\"#DBDBDB\"><font face=\"Verdana, Arial, Helvetica, sans-serif\">".$curdatetime."</font></td>
						</tr>

							                         
						  </table>
						<div><br />
					  </div>
					   
					  <font face=\"Verdana, Arial, Helvetica, sans-serif\">

					  <br>Thank you,\r\n</font><br>
					   <div></div>
					  <div><br><font face=\"Verdana, Arial, Helvetica, sans-serif\">Best Regards,</font><br /></div>
					  <div><br><font face=\"Verdana, Arial, Helvetica, sans-serif\">".$studname."</font><br /></div>
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
					
	$newMessageDetailId = runnum4('id','pg_messages_detail');
	//$msg = mysql_real_escape_string($message);							
	$sql="INSERT INTO pg_messages
	(id, sender, subject, message, message_date, status, status_date)
	VALUES('$newMessageId','$user_id','$subject','$message', '$curdatetime','NEW', '$curdatetime')";
	$dba->query($sql);		
				
	
	$sq2="INSERT INTO pg_messages_detail
	(id, message_id, recipient, recipient_status, recipient_status_date, sender_status, sender_status_date)
	VALUES('$newMessageDetailId','$newMessageId', '$val', 'NEW', '$curdatetime', 'NEW', '$curdatetime')";
	$dbf->query($sq2);
			

	/*$sqlUpload = "UPDATE file_upload_inbox
	SET message_id = '$newMessageId'
	WHERE user_id = '$user_id'
	AND (message_id = '$messageId' OR message_id = '')";

	$db_klas2->query($sqlUpload);*/			



?>	






