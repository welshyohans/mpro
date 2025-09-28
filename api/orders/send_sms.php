<?php
include_once '../../model/SMS.php';

// Check if enough arguments are provided
if ($argc < 3) {
    exit("Usage: php send_sms.php <phone> <message>");
}

// Retrieve phone number and message from command-line arguments
$phone = $argv[1];
$message = $argv[2];

// Send the SMS
try {
    $sms = new SMS();
    $sms->sendSms($phone, $message);
} catch (Exception $e) {
    // Log any errors to a file for debugging
    error_log("Failed to send SMS to $phone: " . $e->getMessage());
}
?>