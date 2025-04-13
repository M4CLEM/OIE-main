<?php

require '../vendor/autoload.php';
include("../includes/connection.php");

if (!isset($connect)) {
    die("Database connection not established.");
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use Swift\Mailer;
use Swift\Message;
use Swift\SmtpTransport;

try {
    if (isset($_POST['send'])) {
        $email = isset($_GET['email']) ? $_GET['email'] : '';
        if (empty($email)) {
            header('Location: ../index.php');
            exit;
        }

        // Optional: check if user exists
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

        // SMTP credentials
        $smtpEmail = 'cipa@plmun.edu.ph'; // App email
        $smtpPassword = 'iqwrimadvoliiaoc'; // App password

        // Create transport
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

        $mailer = new Swift_Mailer($transport);

        // Create email message
        $message = (new Swift_Message('OTP Verification Code'))
            ->setFrom([$smtpEmail => 'PLMUN OIE'])
            ->setTo([$email])
            ->setBody('
                <!DOCTYPE html>
                <html>
                <head>
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                </head>
                <body>
                    <div class="container mt-4">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h3 class="card-title text-primary">OTP Verification</h3>
                                <p class="card-text">
                                    Your OTP (One-Time Password) for verification is:
                                    <strong class="text-success" style="font-size: 1.5rem;">' . $otp . '</strong>
                                </p>
                            </div>
                        </div>
                    </div>
                </body>
                </html>', 'text/html');

        $result = $mailer->send($message);

        echo '
        <div class="position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index: 1050; width: 100%; max-width: 600px;">
            <div class="alert alert-success d-flex align-items-center justify-content-center shadow" role="alert">
                <svg xmlns="http://www.w3.org/2000/svg" class="bi flex-shrink-0 me-2" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM6.97 11.03a.75.75 0 0 0 1.07 0l4.992-4.992a.75.75 0 0 0-1.06-1.06L7.5 9.439 5.53 7.47a.75.75 0 0 0-1.06 1.06l2.5 2.5z"/>
                </svg>
                <div>
                    Send OTP successfully. Please check your Gmail.
                </div>
            </div>
        </div>';        
    }
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
}
?>
