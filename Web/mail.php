<?php
require ('../vendor/autoload.php');


$mail = new PHPMailer;

$mail->isSMTP();
$mail->SMTPDebug = 3;
$mail->Debugoutput = 'html';                       // Set mailer to use SMTP
$mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = 'dreamcenturyfaformation@gmail.com';                 // SMTP username
$mail->Password = 'UJ691vWtcdrm';                           // SMTP password
$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
$mail->Port = 465;                                    // TCP port to connect to

$mail->setFrom('dreamcentury.fa.formation2016@gmail.com', 'Formation test');
$mail->addAddress('b.perraud@dreamcentury.com', 'Joe User');     // Add a recipient

$mail->Subject = 'Here is the subject';
$mail->Body    = 'This is the HTML message body <b>in bold!</b>';
$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

if(!$mail->send()) {
	echo 'Message could not be sent.';
	echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
	echo 'Message has been sent';
}