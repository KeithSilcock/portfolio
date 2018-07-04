<?php
require_once('email_config.php');
require('./PHPMailer/PHPMailerAutoload.php');

$message = [];
$output = [
    'success' => null,
    'messages' => [],
];

$message['name'] = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
if(empty($message['name'])){
    $output['success'] = false;
    $output['message'][]='missing name key';
}
$message['email'] = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
if(empty($message['email'])){
    $output['success'] = false;
    $output['message'][]='invalid email key';
}
$message['subject'] = filter_var($_POST['subject'], FILTER_SANITIZE_STRING);
if(empty($message['subject'])){
//    $output['success'] = false;
//    $output['message'][]='invalid email key';
}
$message['comments'] = filter_var($_POST['comments'], FILTER_SANITIZE_STRING);
if(empty($message['comments'])){
//    $output['success'] = false;
//    $output['message'][]='invalid email key';
}

if($output['success'] !== null){
    http_response_code(400);
    echo json_encode($output);
    exit();
}

$mail = new PHPMailer;
$mail->SMTPDebug = 3;           // Enable verbose debug output. Change to 0 to disable debugging output.

$mail->isSMTP();                // Set mailer to use SMTP.
$mail->Host = 'smtp.gmail.com'; // Specify main and backup SMTP servers.
$mail->SMTPAuth = true;         // Enable SMTP authentication


$mail->Username = EMAIL_USER;   // SMTP username
$mail->Password = EMAIL_PASS;   // SMTP password
$mail->SMTPSecure = 'tls';      // Enable TLS encryption, `ssl` also accepted, but TLS is a newer more-secure encryption
$mail->Port = 587;              // TCP port to connect to
$options = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);

$mail->smtpConnect($options);

$mail->From = $message['email'];
$mail->FromName = $message['name'];
$mail->addAddress(EMAIL_TO_ADDRESS);
$mail->addReplyTo($message['email'], $message['name']);
$mail->isHTML(true);

$message['subject'] = $message['name'] . 'has a message for you on your portfolio';
$message['subject'] = substr($message['comments'], 0, 78);
$mail->Subject = $message['subject'];


$message['comments'] = nl2br($message['comments']);
$mail->Body    = $message['comments'];
$mail->AltBody = htmlentities($message['comments']);

if(!$mail->send()) {
    $output['success'] = false;
    $output['message'][] = $mail->ErrorInfo;
} else {
    $output['success'] = true;
}
echo json_encode($output);
?>
