<?php

require '../vendor/autoload.php';

// Fix the incorrect path issue
include("../includes/connection.php");

// Ensure database connection exists
if (!isset($connect)) {
    die("Database connection not established.");
}

// Prevent duplicate session start warning
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use Swift\Mailer;
use Swift\Message;
use Swift\Transport\SmtpTransport;

try {
    if (isset($_POST['send'])) {
        $email = isset($_GET['email']) ? $_GET['email'] : '';
        if (empty($email)) {
            header('Location: ../index.php');
            exit;
        }

        // Optional: check if user exists (optional for sending OTP)
        $query = "SELECT username FROM users WHERE username = ?";
        $stmt = $connect->prepare($query);
        if (!$stmt) {
            die("Database error: " . $connect->error);
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (!$user) {
            die("User not found.");
        }

        // Generate OTP
        $otp = mt_rand(100000, 999999);
        $_SESSION['otp'] = $otp;

        // Your system's SMTP credentials
        $smtpEmail = 'cipa@plmun.edu.ph'; // Replace with your app sender email
        $smtpPassword = 'iqwrimadvoliiaoc';   // Replace with your Gmail App Password

        // Create the Transport
        $transport = (new Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl'))
            ->setUsername($smtpEmail)
            ->setPassword($smtpPassword)
            ->setStreamOptions([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ]);

        // Create the Mailer using your created Transport
        $mailer = new Swift_Mailer($transport);

        // Create a message
        $message = (new Swift_Message('OTP Verification Code'))
            ->setFrom([$smtpEmail => 'PLMUN OIE'])
            ->setTo([$email])
            ->setBody('
                <!DOCTYPE html>
                <html>
                <head>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            background-color: #f0f0f0;
                        }
                        .container {
                            max-width: 600px;
                            margin: 0 auto;
                            padding: 20px;
                            background-color: #ffffff;
                            border-radius: 5px;
                            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
                        }
                        h1 {
                            color: #333;
                        }
                        p {
                            font-size: 16px;
                            line-height: 1.6;
                        }
                        #otp {
                            font-size: 24px;
                            font-weight: bold;
                            color: #007bff;
                        }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <h1>OTP Verification</h1>
                        <p>Your OTP (One-Time Password) for verification is: <span id="otp">' . $otp . '</span></p>
                    </div>
                </body>
                </html>', 'text/html');

        // Send the message
        $result = $mailer->send($message);

        echo "OTP sent successfully.";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
