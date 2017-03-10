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

	$sqlval = "SELECT COUNT(*) AS total
	FROM pg_supervisor a
	LEFT JOIN ref_role_status b ON (b.id = a.role_status)
	LEFT JOIN ref_supervisor_type c ON (c.id = a.ref_supervisor_type_id)
	WHERE pg_thesis_id = '$pgThesisId' 
	AND ref_supervisor_type_id IN ('SV','CS') 
	";
	$resultvalidate = $dbj->query($sqlval);
	$dbj->next_record();
	$total =$dbj->f('total');

	$studidname = preg_replace('/[^\p{L}\p{N}\s]/u', '', $studidname);
	$curdatetime = date("Y-m-d H:i:s");
	$subject = "Thesis Proposal Submitted by ".$studidname."";
	//$empid = convertid($staff_id);		
	//$staffid = explode(",", $_POST['staff_id']);				
			
	$newMessageId = "I".runnum('id','pg_messages');

	$message = "<div>
					  <div><span color=\"#837E7E\" size=\"-1\" face=\"Trebuchet MS, Arial, Helvetica, sans-serif\" data-mce-style=\"color: #837e7e; font-family: Trebuchet MS,Arial,Helvetica,sans-serif;\">
						<center>
						  <img src=\"http://www.msu.edu.my/v9/images/logo2t.png\" alt=\"Management &amp; Science University\" data-mce-src=\"http://www.msu.edu.my/v9/images/logo2t.png\" /> <br />
						  <hr noshade=\"noshade\" size=\"1\" />
						  <br>
						</center>
						<br />
						<table>

						 <div></div>
						<tr>
							<td><font face=\"Verdana, Arial, Helvetica, sans-serif\">\r\nDear Sir,\r\n\n</font><td>
						</tr>
						 <div></div>
						<tr>
							<td><font face=\"Verdana, Arial, Helvetica, sans-serif\">
							\r\nPlease be informed, the following student thesis proposal has been approved with changes by the Senate.</font></td>
						 </tr><div></div>
						 <tr>
							<td>&nbsp;</td>
						 </tr>
						 </table>
				<table width=\"auto\">
						<tr>
						  
						  <td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\">Student Name </font></strong></td>
						  <td bgcolor=\"#DBDBDB\"><font face=\"Verdana, Arial, Helvetica, sans-serif\">".$studidname."</font></td>
						  </tr>
						<tr>
						  <td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\">Matric No </font></strong></td>
						  <td bgcolor=\"#DBDBDB\"><font face=\"Verdana, Arial, Helvetica, sans-serif\">".$myStudentMatrixNo[$val]."</font></td>
						 </tr>
						<tr>
						  <td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\">Submit date </font></strong></td>
						  <td bgcolor=\"#DBDBDB\"><font face=\"Verdana, Arial, Helvetica, sans-serif\">".$myFormatForView."</font></td>
						 </tr>
						<tr>
						  <td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\">Thesis ID </font></strong></td>
						  <td bgcolor=\"#DBDBDB\"><font face=\"Verdana, Arial, Helvetica, sans-serif\">".$pgThesisId."</font></td>
						  </tr>
						<tr>
						  <td bgcolor=\"#5B74A8\" width=\"137\"><strong><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\">
						  Thesis/Project Title </font></strong></td>
						  <td bgcolor=\"#DBDBDB\" width=\"330\"><font face=\"Verdana, Arial, Helvetica, sans-serif\">".mysql_real_escape_string($thesisTitle)."</font></td>
						</tr>
						<tr>
						  <td bgcolor=\"#5B74A8\" width=\"137\"><strong><font color=\"#FFFFFF\"face=\"Verdana, Arial, Helvetica, sans-serif\">Thesis Type </font></strong></td>
						  <td bgcolor=\"#DBDBDB\" width=\"330\"><font face=\"Verdana, Arial, Helvetica, sans-serif\">".$typedesc."</font></td>
						</tr>
						<tr>
						  <td bgcolor=\"#5B74A8\" width=\"137\"><strong><font color=\"#FFFFFF\"face=\"Verdana, Arial, Helvetica, sans-serif\">
						  List of Supervisor </font></strong></td>
						  <td bgcolor=\"#DBDBDB\" width=\"330\"><font face=\"Verdana, Arial, Helvetica, sans-serif\">";
	for ($in=0; $in<$totalSup; $in++) 
	{
			$message .="$empNameArray[$in] - $positionDescArray[$in]($roleDescArray[$in])<br>";
	} 		
	
		$message .= "</font></td>
						</tr>
					</table>
						<div><br />
					  </div>
					   <div></div>
					  <div></div>
					  <br><font face=\"Verdana, Arial, Helvetica, sans-serif\">Thank you,\r\n</font><br>
					   <div></div>
					  <div><br><font face=\"Verdana, Arial, Helvetica, sans-serif\">Best Regards,</font><br/></div>
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
							
	if($studDept == 'GSM')
	{
		/*$sqlstaff = "SELECT empid FROM new_employee WHERE unit_id = '$studDept'
		UNION
		SELECT empid FROM new_employee WHERE unit_id = 'ACADOF'";*/
		$sqlstaff = "SELECT const_value FROM base_constant WHERE const_term = 'MESSAGE_FACULTY_GSM'";
			
	}
	else if($studDept == 'SGS')
	{
		/*$sqlstaff = "SELECT empid FROM new_employee WHERE unit_id = '$studDept'
		UNION
		SELECT empid FROM new_employee WHERE unit_id = 'ACADOF'";*/
		$sqlstaff = "SELECT const_value FROM base_constant WHERE const_term = 'MESSAGE_FACULTY_SGS'";
 
	}
	else
	{
		$sqlstaff = "SELECT const_value FROM base_constant WHERE const_term = 'MESSAGE_FACULTY_OTH'";
	
	}
	
	$sqlstaff  = $dba->query($sqlstaff);
	$dba->next_record();
	$allempid =$dba->f('const_value');
				
	$facuId = explode(" , ", $allempid);
	$facuId[0]; /// email = Prof gapar
	$facuId[1]; /// email = prof ali
	foreach($facuId as $empid)
	{
		$newMessageDetailId = runnum2('id','pg_messages_detail');
		
		$sq2="INSERT INTO pg_messages_detail
		(id, message_id, recipient, recipient_status, recipient_status_date, sender_status, sender_status_date)
		VALUES('$newMessageDetailId','$newMessageId', '$empid', 'NEW', '$curdatetime', 'NEW', '$curdatetime')";
		$dbf->query($sq2);
	} 



?>	






