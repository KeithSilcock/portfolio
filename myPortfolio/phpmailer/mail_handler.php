
<?php
require_once('email_config.php');
require('./PHPMailer/PHPMailerAutoload.php');

$message = [];
$output = [
    'success' => null,
    'messages' => [],
];

//sanitize
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
$message['message'] = filter_var($_POST['message'], FILTER_SANITIZE_STRING);
if(empty($message['message'])){
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
$mail->FromName = "Porfolio: {$message['name']}";
$mail->addAddress(EMAIL_TO_ADDRESS);
$mail->addReplyTo($message['email'], $message['name']);
$mail->isHTML(true);

$message['subject'] = "Portfolio Message from {$message['name']}";
$message['subject'] = substr($message['subject'], 0, 78);


$message['message'] = nl2br($message['message']);
$mail->Subject = $message['subject'];
$mail->Body    = $message['message'];
$mail->AltBody = htmlentities($message['message']);

if(!$mail->send()) {
    $output['success'] = false;
    $output['message'][] = $mail->ErrorInfo;
} else {
    $output['success'] = true;
    header('Location: https://keithsilcock.com#contact');
}
// echo json_encode($output);
?>
