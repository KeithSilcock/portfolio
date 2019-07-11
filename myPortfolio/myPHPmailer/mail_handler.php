
<?php
// ini_set('display_errors', 'On');
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL | E_STRICT);


require_once('email_config.php');
require('./PHPMailer/PHPMailerAutoload.php');
require_once "recaptchalib.php";


$secret = getenv('recaptcha');

// empty response
$response = null;
 
// check secret key
$reCaptcha = new ReCaptcha($secret);

if ($_POST["g-recaptcha-response"]) {
    $response = $reCaptcha->verifyResponse(
        $_SERVER["REMOTE_ADDR"],
        $_POST["g-recaptcha-response"]
    );
}

$output = [
    'success'=>false,
    'messages'=>[]
];


if ($response == null || !$response->success) {
    $output['success'] = false;
    $output['messages'][] = "Sorry, not today bro-bot!";
  } 


$message = [];
// $output = [
//     'success' => null,
//     'messages' => [],
// ];

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
    // echo json_encode($output);
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

$subject = "Portfolio Message from {$message['name']}";
$subject = substr($subject, 0, 78);


$message['message'] = nl2br($message['message']);
$mail->Subject = $subject;
$mail->Body    = nl2br("Subject:\n {$message['subject']} \n\n  Message:\n{$message['message']}");
$mail->AltBody = htmlentities($message['message']);

if(!$mail->send() || !$output['success']) {
    $output['success'] = false;
    $output['message'][] = $mail->ErrorInfo;

    // echo 'Message could not be sent.' . $error['message'];
    // echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    // echo "HELLO!";
    $output['success'] = true;
    $output['message'][] = "Thank you for submitting an email!";

    // echo '<script type="text/javascript">
    //        window.location = "https://keithsilcock.com#contact"
    //        alert("Thank you for reaching out! \nHave a great day!")
    //   </script>';
    // echo 'Message has been sent';
    // header("Location: https://keithsilcock.com#contact");
    // die();

    
}
echo json_encode($output) . "HELLO!";
?>
