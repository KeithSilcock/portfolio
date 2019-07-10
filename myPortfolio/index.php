<?php
 
// grab recaptcha library
require_once "recaptchalib.php";
include_once "./index.html";

$secret = $_SERVER['recaptcha'];

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

echo "./index.html"

?>