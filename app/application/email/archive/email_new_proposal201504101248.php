<?php

	// static params (static values, no need to change the values)
	$random_hash = md5(date('r', time()));
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= "Content-Type: multipart/mixed; boundary=\"PHP-mixed-".$random_hash."\"\r\n";
	
	// from
	$headers .= "From: \"" . $fromadmin . "\" <" . $tofaculty . ">\r\n";
	
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
						<table>
						 <div></div>
						<tr>
							<td>\r\nDear Sir ".$selectname.",\r\n\n<td>
						</tr>
						 <div></div>
						<tr>
							<td>\r\nThe following student has submitted the thesis proposal for your review and approval.\r\n\n</td>
						 </tr><div></div>
						 <tr>
							<td>&nbsp;</td>
						 </tr>
						 </table>
						<table width=\"479\">
						<tr>
						  
						  <td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\">Student Name </font></strong></td>
						  <td bgcolor=\"#DBDBDB\">".$username."</td>
						  </tr>
						<tr>
						  <td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\">Matric No </font></strong></td>
						  <td bgcolor=\"#DBDBDB\">".$user_id."</td>
						  </tr>
						<tr>
						  <td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\">Thesis ID </font></strong></td>
						  <td bgcolor=\"#DBDBDB\">".$thesis_id."</td>
						  </tr>
						  <tr>
						  <td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\">Submit Date </font></strong></td>
						  <td bgcolor=\"#DBDBDB\">".$curdatetime1."</td>
						  </tr>
						<tr>
						  <td bgcolor=\"#5B74A8\" width=\"137\"><div><strong><font color=\"#FFFFFF\">Thesis/Project Title </font></strong></div></td>
						  <td bgcolor=\"#DBDBDB\" width=\"330\"><div>".$thesis_title."</div></td>
						</tr>
						<tr>
                          <td bgcolor=\"#5B74A8\"><div><strong><font color=\"#FFFFFF\">Proposal Type </font></strong></div></td>
						  <td bgcolor=\"#DBDBDB\"><div>".$type."</div></td>
						  </tr>
						<tr>
						  <td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\">Introduction</font></strong></td>
						  <td bgcolor=\"#DBDBDB\">".$introduction."</td>
						  </tr>
						<tr>
						  <td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\">Objective</font></strong></td>
						  <td bgcolor=\"#DBDBDB\">".$objective."</td>
						  </tr>
						<tr>
						  <td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\">Brief Description </font></strong></td>
						  <td bgcolor=\"#DBDBDB\">".$description."</td>
						  </tr>
	
						<tr>
						  <td>                          
						  </table>
						<div><br />
					  </div>
					   <div></div>
					   
					  Please check this proposal via our Postgrad portal at http://postgrad.msu.edu.my.<br>\r\n
					  <div></div>
					  <br>Thank you,\r\n<br>
					   <div></div>
					  <div><br>Best Regards,<br /></div>\r\n<br>
					  <div><br>Management &amp; Science University<br /></div>
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
				"Content-Type: text/html; charset=\"iso-8859-1\"\r\n" . "Content-Transfer-Encoding: 8bit\r\n\r\n"
				. $messageBody . "\r\n";
	
	$data = chunk_split(base64_encode($data));
	//$filedata= $data;
	
	
	/***
	$message .= "--PHP-mixed-".$random_hash."\r\n" .
				"Content-Type: {\"" . $attachmentFiletype . "\"};\r\n" . 
				" name=\"" . $attachmentFilename . "\"\r\n" .
				"Content-Disposition: attachment;\r\n" . " filename=\"" . $attachmentFilename . "\"\r\n" .
				"Content-Transfer-Encoding: base64\n\n" . 
				$data . "\r\n\r\n" .
				"--PHP-mixed-".$random_hash."\r\n";
	***/
	
	
	$message .= "--PHP-mixed-".$random_hash."\r\n" .
				"Content-Type: \"" . $FileType . "\"\r\n" . 
				" name=\"" . $FileName . "\"\r\n" .
				"Content-Disposition: attachment;\r\n" . " filename=\"" . $FileName . "\"\r\n\n" .
				$data . "\r\n\r\n" .
				"--PHP-mixed-".$random_hash."\r\n";

	// sending the mail
	$ok = mail('ridxvin11@gmail.com', $subject, $message, $headers);

?>