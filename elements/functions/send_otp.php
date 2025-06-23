<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../includes/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';

    if (empty($email)) {
        http_response_code(400);
        echo "Email is required.";
        exit;
    }

    // Generate 6-digit OTP and store in session
    $otp = random_int(100000, 999999);
    $_SESSION['otp'] = $otp;
    $_SESSION['otp_created_at'] = time();
    $_SESSION['otp_verified'] = false;

    // Your Brevo API key
    $apiKey = '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';

    // Build the Brevo API payload
    $data = [
        'sender' => [
            'name' => 'PLMUN CIPA',
            'email' => 'cipa@plmun.edu.ph' // ← Must be verified in Brevo
        ],
        'to' => [
            ['email' => $email]
        ],
        'subject' => 'Your OTP Code',
        'htmlContent' => "
            <p>Hello,</p>
            <p>Your OTP code is: <strong>$otp</strong></p>
            <p>This code will expire in 2 minutes.</p>
            <p>Regards,<br>PLMUN OJT Portal</p>
        "
    ];

    // Send the request to Brevo API
    $ch = curl_init('https://api.brevo.com/v3/smtp/email');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'api-key: ' . $apiKey
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Handle response
    if ($httpCode == 201) {
        echo "OTP sent!";
    } else {
        http_response_code(500);
        echo "Failed to send OTP. Brevo response: $response";
    }
}
