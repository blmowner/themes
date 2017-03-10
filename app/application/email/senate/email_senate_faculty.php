<?php

	// static params (static values, no need to change the values)
	
	$sqlAdmin = "SELECT const_value FROM base_constant WHERE const_term = 'EMAIL_ADMIN'";
	$dbgAdmin = $dbg;
	$result_sqlAdmin = $dbgAdmin->query($sqlAdmin); 
	$dbgAdmin->next_record();
	$row_cnt_msg = mysql_num_rows($result_sqlAdmin);
	$fromadmin = $dbgAdmin->f('const_value');	
	
	$sqlDirect = "SELECT const_value FROM base_constant
	WHERE const_term = 'EMAIL_DIRECTOR'";
	$dbDirect = $dbg;
	$result_sqlDirect = $dbDirect->query($sqlDirect); 
	$dbDirect->next_record();
	$row_cnt_email_director = mysql_num_rows($result_sqlDirect);
	
	$directorEmail = $dbDirect->f('const_value');
	
	$random_hash = md5(date('r', time()));
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= "Content-Type: multipart/mixed; boundary=\"PHP-mixed-".$random_hash."\"\r\n";
	$subject = "Thesis Endorsement";
	
	$admin = "Themes Administrator";
	$userEmail = "mohd_nizam@msu.edu.my"; ///// this email for testing

	$headers .= "From: \"" . $userName . "\" <" . $userEmail . ">\r\n"; /// from admin
	$headers .= "Cc: \"" . $admin . "\" <" . $fromadmin . ">\r\n"; 
	
	$messageBody = "<div>
					  <div><span color=\"#837E7E\" size=\"-1\" face=\"Trebuchet MS, Arial, Helvetica, sans-serif\" data-mce-style=\"color: #837e7e; font-family: Trebuchet MS,Arial,Helvetica,sans-serif;\">
						<center>
						  <img src=\"http://www.msu.edu.my/v9/images/logo2t.png\" alt=\"Management &amp; Science University\" data-mce-src=\"http://www.msu.edu.my/v9/images/logo2t.png\" /> <br />
						  <hr noshade=\"noshade\" size=\"1\" />
						  <br>
						</center>
						<table>
						 <div></div>
						<tr>
							<td><font face=\"Verdana, Arial, Helvetica, sans-serif\">\r\nDear Sir,\r\n\n</font><td>
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
					
	$message = 	"--PHP-mixed-".$random_hash."\r\n" .
				"Content-Type: text/html; charset=\"iso-8859-1\"\r\n" . "Content-Transfer-Encoding: 7bit\r\n\r\n"
				. $messageBody . "\r\n";
	
	for ($i=0; $i<sizeof($FileType); $i++) {
		$data = chunk_split(base64_encode($attachmentdata[$i]));
		//$data = $attachmentData;
	
		$message .= "--PHP-mixed-".$random_hash."\r\n" .
					"Content-Type: {\"" . $FileType[$i] . "\"};\r\n" . 
					" name=\"" . $FileName[$i] . "\"\r\n" .
					"Content-Disposition: attachment;\r\n" . " filename=\"" . $FileName[$i] . "\"\r\n" .
					"Content-Transfer-Encoding: base64\n\n" . 
					$data . "\r\n\r\n";
	}
	
	// end message
	$message .=  "--PHP-mixed-".$random_hash."\r\n";

	// sending the mail//$studemail
	// sending the mail//
	
	
	$sqlemailfaculty = "SELECT const_value FROM base_constant
	WHERE const_term = 'EMAIL_FACULTY'";
	$dbg3 = $dbg;
	$result_sqlemailfaculty = $dbg3->query($sqlemailfaculty); 
	$dbg3->next_record();
	$row_cnt_email2 = mysql_num_rows($result_sqlemailfaculty);
	
	$facultyEmail = array();
	$facultyEmail = $dbg3->f('const_value');
	$facultyEmail2 = explode(" , ", $facultyEmail);
	$facultyEmail2[0];///sgs
	$facultyEmail2[1];///gsm
	$facultyEmail2[2];
	
	$sqlfac = "SELECT a.program_code, b.programid,a.manage_by_whom
	FROM student_program a 
	LEFT JOIN program b ON (a.program_code =b.programid)
	WHERE matrix_no = '$studentMatrixNo'";
	$dbcfac = $dbc;
	$result_sqlfac = $dbcfac->query($sqlfac); 
	$dbcfac->next_record();
	$row_cnt_msg = mysql_num_rows($result_sqlfac);
	$manageBy = $dbcfac->f('manage_by_whom');
	
	///////////////////
	if($manageBy == 'SGS') {
		mail('mohd_nizam@msu.edu.my', $subject, $message, $headers); //$facultyEmail2[0]
	}
	else if ($manageBy == 'GSM') {
		mail('mohd_nizam@msu.edu.my', $subject, $message, $headers);//$facultyEmail2[1]
	} else {
	
	}


?>