<?php
/*
 * @author- Vandish Gandhi
 * @desc -  Main controller class to request the view for the URL 
 * 
 * Version History:
 * 1.0 - initiated the class and extended it to its sub classes
 */
class email {	
	
	public function send($to,$sendmessage,$subject='Alert') {		
		$message = '<html><body>';
		$message .= '<img src="http://smartafarm.com.au/application/src/templates/smartfarm.png"><br><h1>Smartafarm Alert!</h1>';
		$message .= $sendmessage;
		$message .= '</body></html>';
		$headers = 'From: Smartafarm' . "\r\n" .
		    'Reply-To: smartafarm@gmail.com' . "\r\n" ;
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
		    'X-Mailer: PHP/' . phpversion();
		mail($to, $subject, $message, $headers)	;
	}
}


?> 