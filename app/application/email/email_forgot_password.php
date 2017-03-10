<?php
//////////////////////>>>>>>>>>>>>>>>>>>>>>>>>not applicable
	// static params (static values, no need to change the values)
	$random_hash = md5(date('r', time()));
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= "Content-Type: multipart/mixed; boundary=\"PHP-mixed-".$random_hash."\"\r\n";
	$subject = "THEMES (RESET PASSWORD)";
	$admin = "System Generate";
	$systemEmail = "systemgenerate@msu.edu.my";

	$headers .= "From: \"" . $admin . "\" <" . $systemEmail . ">\r\n";
	//$headers .= 'Cc: ridxvin11@gmail.com' . "\r\n";
	// message untuk dihantar, in HTML (replace this with your email message)
	$messageBody = "<div>
					  <div><span color=\"#837E7E\" size=\"-1\" face=\"Verdana, Arial, Helvetica, sans-serif\" data-mce-style=\"color: #837e7e; font-family: Trebuchet MS,Arial,Helvetica,sans-serif;\">
						<center>
						  <img src=\"http://www.msu.edu.my/v9/images/logo2t.png\" alt=\"Management &amp; Science University\" data-mce-src=\"http://www.msu.edu.my/v9/images/logo2t.png\" /> <br />
						  <hr noshade=\"noshade\" size=\"1\" />
						  <br>
						</center>
						<table>
						<tr>
							<td><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">Hi ".$userName.", </font></td>
						</tr>
						<tr>
							<td><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\"></font></td>
						</tr>
						<tr>
							<td><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">
							\r\nYour password has been reset on $time_reset. Please use the following details to login and strongly advisable to change to another 
							new password immediately after successful login.
							<td>\r\n\n</td>
						 </tr><div></div>
						 <tr>
							<td></td>
						 </tr>
						 </table>
						<table style =\"table-layout: auto;\">
							<tr>						  
							  <td bgcolor=\"#5B74A8\"><strong><font size=\"2\" color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\">User ID </font></strong></td>
							  <td bgcolor=\"#DBDBDB\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">".$username."</font></td>
							</tr>
							<tr>						  
							  <td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\" size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">Password </font></strong></td>
							  <td bgcolor=\"#DBDBDB\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">".$new_pwd."</font></td>
							</tr>
							<tr>
							  <td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">Time Reset</font></strong></td>
							  <td bgcolor=\"#DBDBDB\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">".$time_reset."</font></td>
							</tr>
							<tr>
							  <td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">User Email</font></strong></td>
							  <td bgcolor=\"#DBDBDB\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">".$clean_email."</font></td>
							</tr>
						</table>
						<div><br />
						<font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">
						If you are not requested the above password reset, please contact Themes Admin at $adminEmail for enquiry and assistance.<br>
						<br>
						Thank You,<br>
						Themes Admin</font>
						<br>
					  </div>
						<center>
						  <div><br /></div>
						  <hr noshade=\"noshade\" size=\"1\" />
						  <font size='-1' color='#837E7E' face=\"Verdana, Arial, Helvetica, sans-serif\">
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

	// sending the mail
	mail('mohd_nizam@msu.edu.my', $subject, $message, $headers);


?>