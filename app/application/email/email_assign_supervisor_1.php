<?php

	// static params (static values, no need to change the values)
	$random_hash = md5(date('r', time()));
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= "Content-Type: multipart/mixed; boundary=\"PHP-mixed-".$random_hash."\"\r\n";
	$admin = "Postgrad Administrator";
	$subject = "Thesis Proposal by ".$studidname." - Appointment as ".$position.".";
	$fromadmin = "postgradadmin@msu.edu.my";
	$headers .= "From: \"" . $admin . "\" <" . $fromadmin . ">\r\n";
	//$headers .= 'Cc: ridxvin11@gmail.com' . "\r\n";
	// message untuk dihantar, in HTML (replace this with your email message)
	$messageBody = "<div>
					  <div><span color=\"#837E7E\" size=\"2\" face=\"Trebuchet MS, Arial, Helvetica, sans-serif\" data-mce-style=\"color: #837e7e; font-family: Trebuchet MS,Arial,Helvetica,sans-serif;\">
						<center>
						  <img src=\"http://www.msu.edu.my/v9/images/logo2t.png\" alt=\"Management &amp; Science University\" data-mce-src=\"http://www.msu.edu.my/v9/images/logo2t.png\" /> <br />
						  <hr noshade=\"noshade\" size=\"2\" />
						  <br>
						</center>
						
						<table>

						 <div></div>
						<tr>
							<td><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">\r\nDear ".$empName[$val].",\r\n\n</font><td>
						</tr>
						 <div></div>
						<tr>
							<td>
							<font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">\r\nPlease be informed, the following student has submitted his/her thesis proposal for our approval. On behalf of senate, we would like to extend an invitation to you to serve as a ".$position." for the PhD thesis of the following candidate.</font>
							</td>
						 </tr><div></div>
						 <tr>
							<td>&nbsp;</td>
						 </tr>
						 </table>
<table style =\"table-layout: auto;\">
						<tr>
						  
						  <td width=\"80\" bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">Student Name </font></strong></td>
						  <td width=\"566\" bgcolor=\"#DBDBDB\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">".$studidname."</font></td>
  </tr>
						<tr>
						  <td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">Matric No </font></strong></td>
						  <td bgcolor=\"#DBDBDB\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">".$matrixNo."</font></td>
						  </tr>
						<tr>
						  <td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">Submit date </font></strong></td>
						  <td bgcolor=\"#DBDBDB\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">".$submitDate."</font></td>
						  </tr>
						<tr>
						  <td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">Thesis ID </font></strong></td>
						  <td bgcolor=\"#DBDBDB\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">".$thesisId."</font></td>
						  </tr>
						<tr>
						  <td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">
						  Thesis/Project Title </font></strong></td>
						  <td bgcolor=\"#DBDBDB\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">".$thesisTitle."</font></td>
						</tr>
						<tr>
						  <td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">Thesis Type </font></strong></td>
						  <td bgcolor=\"#DBDBDB\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">".$thesisType."</font></td>
						</tr>
						  </table>						
						  <div><br />
					  </div>
					   <div></div>
					  <div></div>
					  <br><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">Thank you,\r\n</font><br>
					   <div></div>
					  <div><br><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">Best Regards,</font><br /></div>
					  <div><br><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">Management &amp; Science University</font><br /></div>
					  <div><br />
						<center>
						  <div><br /></div>
						  <hr noshade=\"noshade\" size=\"2\" />
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
				"Content-Type: text/html; charset=\"iso-88591\"\r\n" . "Content-Transfer-Encoding: 7bit\r\n\r\n"
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
	//$superemail - supervisor email
	// sending the mail >>>>>>> 
	mail('mohd_nizam@msu.edu.my', $subject, $message, $headers); // >>>>. Supervisor email : $empEmail[$val]
?>