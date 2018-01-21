<?php
require 'inc/php_inc.php';

$oMailer = new PHPMailer\PHPMailer\PHPMailer;
$oMailer->CharSet = 'UTF-8';
//wm.server-home.org
$oMailer->isSMTP();
$oMailer->Host = $mail_data['host'];
$oMailer->SMTPAuth = true;
$oMailer->Username = $mail_data['user'];
$oMailer->Password = $mail_data['pass'];
$oMailer->SMTPSecure = 'tls';
$oMailer->Port = 587;

$oMailer->From = 'simpleyth@randompeople.de';
$oMailer->FromName = 'SimpleYTH Backup E-Mail';
$oMailer->addAddress( 'sascha.u.kaufmann@googlemail.com', 'Sascha Kaufmann' );
$oMailer->addAddress( 'defender833@web.de', 'Defender833' );

$oMailer->isHTML( true );
$oMailer->Subject = 'SYTH - Backup '.date("Y-m-d");
$oMailer->Body = 'Hier könnte ein fancy Text stehen...';
$oMailer->AltBody = strip_tags( $oMailer->Body );
$oMailer->addAttachment("/var/www/backup/simpleyth_error.tar.gz");
$oMailer->addAttachment("/var/www/backup/simpleyth_mysql.tar.gz");

if ( !$oMailer->send() ) {
  trigger_error('Mailer Error: ' . $oMailer->ErrorInfo, E_USER_ERROR);
  exit;
}
echo 'E-Mail wurde versendet!';


?>