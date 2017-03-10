<?php

	// static params (static values, no need to change the values)
	$random_hash = md5(date('r', time()));
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= "Content-Type: multipart/mixed; boundary=\"PHP-mixed-".$random_hash."\"\r\n";
	$subject = "Thesis Proposal Submitted by ".$username."";
	$admin = "Postgrad Administrator";

	// from
	$headers .= "From: \"" . $admin . "\" <" . $tofaculty . ">\r\n";// from jadi student email // $receiveemail (student Email)
	//$headers .= 'Cc: ridxvin11@gmail.com' . "\r\n"; from jadi student
	// message untuk dihantar, in HTML (replace this with your email message)
	$messageBody = "<div>
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
							\r\nI am pleased to submit the thesis proposal  as follows :-.\r\n\n</font></td>
						 </tr>
						 <tr>
							<td>&nbsp;</td>
						 </tr>
						 </table>
						<table style =\"table-layout: auto;\">
						<tr>
						  
						  <td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\">Student Name </font></strong></td>
						  <td bgcolor=\"#DBDBDB\"><font face=\"Verdana, Arial, Helvetica, sans-serif\">".$username."</font></td>
						  </tr>
						<tr>
						  <td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\">Matric No </font></strong></td>
						  <td bgcolor=\"#DBDBDB\"><font face=\"Verdana, Arial, Helvetica, sans-serif\">".$user_id."</font></td>
						  </tr>
						<tr>
						  <td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\"face=\"Verdana, Arial, Helvetica, sans-serif\">Thesis ID </font></strong></td>
						  <td bgcolor=\"#DBDBDB\"><font face=\"Verdana, Arial, Helvetica, sans-serif\">".$thesis_id."</font></td>
						  </tr>
						  <tr>
						  <td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\">Submit Date </font></strong></td>
						  <td bgcolor=\"#DBDBDB\"><font face=\"Verdana, Arial, Helvetica, sans-serif\">".$curdatetime1."</font></td>
						  </tr>
						<tr>
						  <td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\">
						  Thesis/Project Title </font></strong></td>
						  <td bgcolor=\"#DBDBDB\"><font face=\"Verdana, Arial, Helvetica, sans-serif\">".$thesis_title."</font></td>
						</tr>
						<tr>
                          			<td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\">Proposal Type </font></strong></td>
						  <td bgcolor=\"#DBDBDB\"><font face=\"Verdana, Arial, Helvetica, sans-serif\">".$type."</font></td>
						<tr>
                          			<td bgcolor=\"#5B74A8\"><strong><font color=\"#FFFFFF\" face=\"Verdana, Arial, Helvetica, sans-serif\">Student Email </font></strong></td>
						  <td bgcolor=\"#DBDBDB\"><font face=\"Verdana, Arial, Helvetica, sans-serif\">".$receiveemail."</font></td>
						</tr>
						</tr>	                         
						  </table>
						<div><br />
					  </div>
					   
					  <font face=\"Verdana, Arial, Helvetica, sans-serif\">

					  <br>Thank you,\r\n</font><br>
					   <div></div>
					  <div><br><font face=\"Verdana, Arial, Helvetica, sans-serif\">Best Regards,</font><br /></div>
					  <div><br><font face=\"Verdana, Arial, Helvetica, sans-serif\">".$username."</font><br /></div>
					  <div><br><font face=\"Verdana, Arial, Helvetica, sans-serif\">Management &amp; Science University</font><br /></div>
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
	

						/*<tr>
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
						  </tr> */
	// bind the message body
	$message = 	"--PHP-mixed-".$random_hash."\r\n" .
				"Content-Type: text/html; charset=\"iso-8859-1\"\r\n" . "Content-Transfer-Encoding: 7bit\r\n\r\n"
				. $messageBody . "\r\n";
	
	/*for ($i=0; $i<sizeof($FileType); $i++) {
		$data = chunk_split(base64_encode($attachmentdata[$i]));
		//$data = $attachmentData;
	
		$message .= "--PHP-mixed-".$random_hash."\r\n" .
					"Content-Type: {\"" . $FileType[$i] . "\"};\r\n" . 
					" name=\"" . $FileName[$i] . "\"\r\n" .
					"Content-Disposition: attachment;\r\n" . " filename=\"" . $FileName[$i] . "\"\r\n" .
					"Content-Transfer-Encoding: base64\n\n" . 
					$data . "\r\n\r\n";
	}*/
	
	// end message
	//>>>>>>>>>>>>>>>>Select email gsm or sgs >>>>>>>>>>>>>>>>>>>>>>>>>
	$select = "SELECT const_value from base_constant where const_term = 'EMAIL_FACULTY'";
	$resultselect = $dbj->query($select);
	$dbj->next_record();
	$email =$dbj->f('const_value');
	
	$email2 = explode(" , ", $email);
	$email2[0]; /// email = test
	$email2[1]; /// email = GSM
	$email2[2]; /// email = SGS
	$message .=  "--PHP-mixed-".$random_hash."\r\n";

	if($dept == 'GSM')
	{
		// sending the mail
		//echo $dept;
		mail($email2[0], $subject, $message, $headers);
		//echo " ".$email2[0]." ".$email2[1]." ".$email2[2]; 
	}
	else if($dept == 'SGS')
	{
		//email : sgs@msu.edu.my
		//echo $dept;
		mail($email2[0], $subject, $message, $headers);
		//echo " ".$email2[0]." ".$email2[1]." ".$email2[2];
	}
	else
	{
		//exit();
		//echo "not found";
	}

?>