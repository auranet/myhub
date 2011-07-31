<?php


$recip = $adminemail;
$headers = 'MIME-Version: 1.0' . "\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
$headers .= 'Message-ID: <'. time() . rand(1,1000). '@myhub.com>' . "\n";
$headers .= 'From: Support <noreply@myhub.com>' . "\n";
$subject = 'Warning: Login Failed '.$TypeCount.' times.';

				$message = 'Login from IP: '. $ip .'<br />';
				$message .= 'For username: ' . $username . '<br />';
				$message .= 'is failed ' . $TypeCount . ' times<br />';

			// Sends email
				mail( $recip, $subject, $message, $headers );

?>
