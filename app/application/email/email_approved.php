<?php
//////////////////////>>>>>>>>>>>>>>>>>>>>>>>>not applicable
	// static params (static values, no need to change the values)
	$random_hash = md5(date('r', time()));
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= "Content-Type: multipart/mixed; boundary=\"PHP-mixed-".$random_hash."\"\r\n";
	$subject = "Thesis Proposal by ".$studidname." - For Approval";
	$admin = "Postgrad Administrator";

	$headers .= "From: \"" . $admin . "\" <" . $fromadmin . ">\r\n";
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
							<td><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">\r\nDear ".$senatetitle." ".$selectname.",</font>\r\n\n<td>
						</tr>
						 <div></div>
						<tr>
							<td><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">
							\r\nPlease be informed, the following student has submitted his/her thesis proposal for Senate endorsement.
							<td>\r\n\n</td>
						 </tr><div></div>
						 <tr>
							<td>&nbsp;</td>
						 </tr>
						 </table>
						<table style =\"table-layout: auto;\">
						<tr>
						  
						  <td bgcolor=\"#5B74A8\"><strong><font size=\"2\" color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\">Student Name </font></strong></td>
						  <td bgcolor=\"#DBDBDB\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">".$studidname."</font></td>
						  </tr>
						<tr>
						  <td bgcolor=\"#5B74A8\"><strong><font size=\"2\" color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\">Matric No </font></strong></td>
						  <td bgcolor=\"#DBDBDB\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">".$myStudentId[$val]."</font></td>
						  </tr>
						<tr>
						  <td bgcolor=\"#5B74A8\"><strong><font size=\"2\" color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\">Thesis ID </font></strong></td>
						  <td bgcolor=\"#DBDBDB\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">".$pgThesisId."</font></td>
						  </tr>
						  <tr>
						  <td bgcolor=\"#5B74A8\"><strong><font size=\"2\" color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\">Submit Date </font></strong></td>
						  <td bgcolor=\"#DBDBDB\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">".$myFormatForView."</font></td>
						  </tr>
						<tr>
						  <td bgcolor=\"#5B74A8\"><strong><font size=\"2\" color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\">
						  Thesis/Project Title </font></strong></td>
						  <td bgcolor=\"#DBDBDB\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\"><div>".$thesisTitle."</font></td>
						</tr>
						<tr>
                          <td bgcolor=\"#5B74A8\"><strong><font size=\"2\" color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\">Proposal Type </font></strong></td>
						  <td bgcolor=\"#DBDBDB\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">".$type."</font></td>
						  </tr>
						<tr>
						  <td bgcolor=\"#5B74A8\"><strong><font size=\"2\" color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\">Faculty Proposal Status </font></strong></td>
						  <td bgcolor=\"#DBDBDB\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">".$vstatus."</font></td>
						  </tr>

						  <td>                          
						  </table>
						<div><br />
					  </div>
					   <div></div>
					   <font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">
					  <br>Thank you,\r\n<br></font>
					   <div></div>
					  <div><br><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">Best Regards,</font><br /></div>\r\n<br>
					  <div><br><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">Management &amp; Science University</font><br /></div>
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