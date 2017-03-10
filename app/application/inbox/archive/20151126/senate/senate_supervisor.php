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
	$subject = "Thesis Endorsement";
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
						 <div></div>
						<tr>
							<td><font face=\"Verdana, Arial, Helvetica, sans-serif\">\r\nDear ".$supervisorName.",\r\n\n</font><td>
						</tr>
						 <div></div>
						<tr>
							<td><font face=\"Verdana, Arial, Helvetica, sans-serif\">\r\n
							Please be informed, the following thesis has been endorsed by Senate.\r\n\n</font></td>
						 </tr><div></div>
						 <tr>
							<td>&nbsp;</td>
						 </tr>
						 </table>
						<table style =\"table-layout: auto;\">
						<tr>
						  
						  <td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\">Student Name </font></strong></td>
						  <td bgcolor=\"#DBDBDB\"><font face=\"Verdana, Arial, Helvetica, sans-serif\">".$studentName."</font></td>
						  </tr>
						<tr>
						  <td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\">Matric No </font></strong></td>
						  <td bgcolor=\"#DBDBDB\"><font face=\"Verdana, Arial, Helvetica, sans-serif\">".$studentMatrixNo."</font></td>
						  </tr>
						<tr>
						  <td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\"  face=\"Verdana, Arial, Helvetica, sans-serif\">Thesis ID </font></strong></td>
						  <td bgcolor=\"#DBDBDB\"><font face=\"Verdana, Arial, Helvetica, sans-serif\">".$thesisId."</font></td>
						  </tr>
						  <tr>
						  <td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\"  face=\"Verdana, Arial, Helvetica, sans-serif\">Endosed Date </font></strong></td>
						  <td bgcolor=\"#DBDBDB\"><font face=\"Verdana, Arial, Helvetica, sans-serif\">".$curdatetime1."</font></td>
						  </tr>
						<tr>
						  <td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\">
						  Session Date</font></strong></td>
						  <td bgcolor=\"#DBDBDB\"><font face=\"Verdana, Arial, Helvetica, sans-serif\">".$vivaDate.", ".$vivaSTime." to ".$vivaETime.", ".$venue."</font></td>
						</tr>  
						  </table>
						<div><br />
					  </div>
					  <font face=\"Verdana, Arial, Helvetica, sans-serif\"> 

					  <br>Thank you,\r\n<br></font>
					   <div></div>
					  <div><br><font face=\"Verdana, Arial, Helvetica, sans-serif\">Best Regards,</font></div>
					  <div><br><font face=\"Verdana, Arial, Helvetica, sans-serif\">Management &amp; Science University</font><br /></div>
					  <div><br />
						<center>
						  <div><br /></div>
						  <hr noshade=\"noshade\" size=\"1\" />
						  <font size=\"-1\" color=\"#837E7E\"  face=\"Verdana, Arial, Helvetica, sans-serif\">
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
	VALUES('$newMessageDetailId','$newMessageId', '$employeeId', 'NEW', '$curdatetime', 'NEW', '$curdatetime')";
	$dbf->query($sq2);
	

	



?>	






