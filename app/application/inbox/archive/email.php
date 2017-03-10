<?php

	// static params (static values, no need to change the values)
	$random_hash = md5(date('r', time()));
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= "Content-Type: multipart/mixed; boundary=\"PHP-mixed-".$random_hash."\"\r\n";
	$adminname = "Postgrad Admin";
	// from
	$headers .= "From: \"" . $adminname . "\" <" . $fromadmin . ">\r\n";
	$headers .= 'Cc: somebody@domain.com' . "\r\n";

	
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
						<table width=\"479\">						
							<tr>
						  	<td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\">Subject </font></strong></td>
						  	<td width=\"430\" bgcolor=\"#DBDBDB\">".$subject."</td>
						  	</tr>
	
							<tr>
						  	<td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\">Subject </font></strong></td>
						  	<td width=\"430\" bgcolor=\"#DBDBDB\">".$inbox."</td>
						  	</tr>
							<tr>
								<td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\">Email for cc </font></strong></td>
								<td width=\"430\" bgcolor=\"#DBDBDB\">".$senderemail."</td>
						  	</tr>
							<tr>
								<td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\">Receiver Email </font></strong></td>
								<td width=\"430\" bgcolor=\"#DBDBDB\">".$receiveemail."</td>
						  	</tr>
						</div>
						</table>
						Thank you for your cooperation. You can safely delete this message.
						
						<div><br />
					  </div>
					  <div>Regards,<br /></div>
					  <div>Management &amp; Science University<br /></div>
					  <div><br />
						<center>
						  <div><br /></div>
						  <hr noshade=\"noshade\" size=\"1\" />
						  <font size='-1' color='#837E7E'>
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
	
	//for ($i=0; $i<sizeof($FileType); $i++) {

		$data = chunk_split(base64_encode($attachmentdata));
		//$data = $attachmentData;
	
		$message .= "--PHP-mixed-".$random_hash."\r\n" .
					"Content-Type: {\"" . $FileType . "\"};\r\n" . 
					" name=\"" . $FileName . "\"\r\n" .
					"Content-Disposition: attachment;\r\n" . " filename=\"" . $FileName . "\"\r\n" .
					"Content-Transfer-Encoding: base64\n\n" . 
					$data . "\r\n\r\n";
	//}
	
	// end message
	$message .=  "--PHP-mixed-".$random_hash."\r\n";

	// sending the mail
	//mail($tofaculty, $subject, $message, $headers);

	$ok = mail($fromadmin, $subject, $message, $headers);

?>