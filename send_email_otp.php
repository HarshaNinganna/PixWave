<?php
require 'path/to/sendgrid-php.php'; // Include SendGrid's library
use SendGrid\Mail\Mail;

// Get the email from the POST request
$email = $_POST['email'];

// Generate a random 6-digit OTP
$otp = rand(100000, 999999);

// Store the OTP in session or database for verification later
$_SESSION['email_otp'] = $otp;

// Send the OTP via SendGrid
$emailObj = new Mail();
$emailObj->setFrom("your-email@example.com", "YourAppName");
$emailObj->setSubject("Your OTP Code");
$emailObj->addTo($email);
$emailObj->addContent("text/plain", "Your OTP code is " . $otp);

$sendgrid = new \SendGrid('your_sendgrid_api_key');
try {
    $response = $sendgrid->send($emailObj);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
