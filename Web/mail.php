<?php
require ('../vendor/autoload.php');


$mail = new PHPMailer;

//$mail->SMTPDebug = 3;                               // Enable verbose debug output

$mail->isSMTP();
$mail->SMTPDebug = 2;
$mail->Debugoutput = 'html';                       // Set mailer to use SMTP
$mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = 'b.perraud@dreamcentury.com';                 // SMTP username
$mail->Password = 'rE78i9c';                           // SMTP password
$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
//$mail->Port = 587;                                    // TCP port to connect to
$mail->Port = 465;                                    // TCP port to connect to

$mail->setFrom('b.perraud@dreamcentury.com', 'Mailer');
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
die('ici');
ini_set('SMTP','smtp.gmail.com');
ini_set('smtp_port','587');

ini_set("auth_username", "b.perraud@dreamcentury.com");
ini_set("auth_password", "rE78i9c?");
mail( "b.perraud@dreamcentury.com" , "Sujet" , "Contenu du message" );