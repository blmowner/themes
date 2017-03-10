<?php
include("../../../lib/common.php");
checkLogin();
echo $userid=$_GET['user_id'];

// static params (static values, no need to change the values)
$random_hash = md5(date('r', time()));
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= "Content-Type: multipart/mixed; boundary=\"PHP-mixed-".$random_hash."\"\r\n";

$sql1 = "SELECT const_term, const_value
		FROM base_constant 
		WHERE const_term IN ('EMAIL_DIRECTOR','EMAIL_ADMIN')";
		
		$result_sql1 = $dbg->query($sql1);				
		$dbg->next_record();
		
		
		if ($const_term=="EMAIL_DIRECTOR") {
			echo $emailDirector = $dbg->f('const_value'); }
		if ($const_term=="EMAIL_ADMIN") {
		echo $emailAdmin = $dbg->f('const_value'); }

		exit();

// prepare params
$subject ="Thesis Proposal for Review";
$to = "$emailDirector";
$headers .= "From: \"Me Myself\" <$emailAdmin>\r\n";

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
					<div>Hi <em>'My Name Here, if you want'</em> (<em>USERID: 012220000</em>),</div>
					<div><br />
					</div>
					
					Your message here, kalau perlu.
					
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
			"Content-Type: text/html; charset=\"iso-8859-1\"\r\n" . "Content-Transfer-Encoding: 8bit\r\n\r\n"
			. $messageBody . "\r\n";

// sending the mail
$ok = @mail($to, $subject, $message, $headers);

// check if mail() function berjaya send atau tidak
if ($ok) {
	?><table>
		<tr>
			<td><span style="color:#FF0000">Message:</span> Email notification has been sent to <?=$to;?> for his/her further action.</td>		
		<tr>
			<td>You may change your proposal (if required) now or before the feedback is received. Thank you. </td>
		</tr>		
		</tr>
	<table>
	<br/>	
	<?
} else {	
}

?>