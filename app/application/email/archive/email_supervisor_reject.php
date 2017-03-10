<?php

	// static params (static values, no need to change the values)
	$random_hash = md5(date('r', time()));
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= "Content-Type: multipart/mixed; boundary=\"PHP-mixed-".$random_hash."\"\r\n";
	$subject = "Supervisor/Co-Supervisor Invitation for ".$studname[$val]."";
	$admin = "Postgrad Administrator";
	// from
	$headers .= "From: \"" . $admin . "\" <" . $fromadmin . ">\r\n";
	$headers .= 'Cc: ridxvin11@gmail.com' . "\r\n";
	
	// message untuk dihantar, in HTML (replace this with your email message)
	$messageBody = "<div>
					  <div><span color=\"#837E7E\" size=\"-1\" face=\"Trebuchet MS, Arial, Helvetica, sans-serif\" data-mce-style=\"color: #837e7e; font-family: Trebuchet MS,Arial,Helvetica,sans-serif;\">
						<center>
						  <img src=\"http://www.msu.edu.my/v9/images/logo2t.png\" alt=\"Management &amp; Science University\" data-mce-src=\"http://www.msu.edu.my/v9/images/logo2t.png\" /> <br />
						  <hr noshade=\"noshade\" size=\"1\" />
						  <br>
						</center>
						<div><br />
						</div>
												
							<font size = \"3\" face=\"Verdana, Arial, Helvetica, sans-serif\">
								
							<br>Dear ".$title." ".$faculty.",
							<br><p>Please be informed, the following staff has rejected of the offer of appointment to serve as a ".$superrole[$val]." for the
							Phd Candidate, ".$studname[$val]."(Matric No: ".$studid[$val].") as mentioned in the invitation email dated ".$senatedate[$val]." .</font></br>
							<br>
					<table>
						<tr>
						  <td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\">Name </font></strong></td>
						  <td bgcolor=\"#DBDBDB\"><font face=\"Verdana, Arial, Helvetica, sans-serif\">".$supertitle." ".$namesuper."</font></td>
						  </tr>
						<tr>
						  <td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\">Date </font></strong></td>
						  <td bgcolor=\"#DBDBDB\"><font face=\"Verdana, Arial, Helvetica, sans-serif\">".$senatedate[$val]."</font></td>
						  </tr>
						
						<tr>
						  <td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\">Postal Address </font></strong></td>
						  <td bgcolor=\"#DBDBDB\"><font face=\"Verdana, Arial, Helvetica, sans-serif\">Management & Science University.<br />
								University Drive, Off Persiaran Olahraga,<br />
								Section 14, 40100 Shah Alam,<br />
								Selangor Darul Ehsan.<br /></font></td>
						  </tr>
						<tr>
						  <td bgcolor=\"#5B74A8\" width=\"137\"><strong><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\">Email </font></strong></td>
						  <td bgcolor=\"#DBDBDB\" width=\"330\"><font face=\"Verdana, Arial, Helvetica, sans-serif\">".$superemail."</font></td>
						</tr>
						<tr>
						  <td bgcolor=\"#5B74A8\" width=\"137\"><strong><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\">Reason </font></strong></td>
						  <td bgcolor=\"#DBDBDB\" width=\"330\"><font face=\"Verdana, Arial, Helvetica, sans-serif\">".$remarks."</font></td>
						</tr>
						  </table>
						<div><br />
					  </div>
					  <font face=\"Verdana, Arial, Helvetica, sans-serif\"> 
					  You can login into our Postgrad system at http://postgrad.msu.edu.my to view the detail.<br>
					  <div></div>
					  <br>Thank you, <br></font>

					  <div><br><font face=\"Verdana, Arial, Helvetica, sans-serif\">Best Regards,</font><br /></div><br>
					  <div><font face=\"Verdana, Arial, Helvetica, sans-serif\">Management & Science University</font></div>
					  <div><br />
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
	
	// bind the message body
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
	mail($facultyemail, $subject, $message, $headers);
	//mail('ridxvin11@gmail.com', $subject, $message, $headers);

?>