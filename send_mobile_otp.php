<?php
session_start();
require 'vendor/autoload.php';
use Twilio\Rest\Client;

// Your Twilio Account SID and Auth Token (make sure to keep these secret)
$sid = 'ACb22859c767fea203cc4772a10c75cfe7'; // Ensure SID is enclosed in quotes
$token = 'd1fc3f0c64eb444641b1d7bab54ad859'; // Ensure token is enclosed in quotes
$client = new Client($sid, $token);

// Get the mobile number from the POST request
$mobile = $_POST['mobile'];

// Validate the mobile number (you may want to add more validation)
if (!preg_match('/^\+\d{1,3}\d{7,15}$/', $mobile)) {
    echo json_encode(['success' => false, 'error' => 'Invalid mobile number format.']);
    exit();
}

// Generate a random 6-digit OTP
$otp = rand(100000, 999999);

// Store the OTP in session for verification later
$_SESSION['mobile_otp'] = $otp;

// Send the OTP via Twilio
try {
    $message = $client->messages->create(
        $mobile,
        array(
            'from' => 'your_twilio_number', // Replace with your Twilio number
            'body' => 'Your OTP code is ' . $otp
        )
    );
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
