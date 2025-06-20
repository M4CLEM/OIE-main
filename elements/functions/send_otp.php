<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../includes/connection.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';

    if (empty($email)) {
        http_response_code(400);
        echo "Email is required.";
        exit;
    }

    // Generate 6-digit OTP
    $otp = random_int(100000, 999999);
    $_SESSION['otp'] = $otp;
    $_SESSION['otp_created_at'] = time();
    $_SESSION['otp_verified'] = false;

    $mail = new PHPMailer(true);
    $smtpHost = 'smtp.gmail.com'; // <-- replace with your SMTP server hostname
    $username = 'cipa@plmun.edu.ph';
    $password = 'oaoybffujhnigslm';

    // List of SMTP configs to try (port, security)
    $smtpConfigs = [
        ['port' => 587, 'security' => 'tls'],
        ['port' => 465, 'security' => 'ssl'],
        ['port' => 25,  'security' => ''],   // no encryption
    ];

    $sent = false;
    $lastError = '';

    foreach ($smtpConfigs as $config) {
        try {
            $mail->clearAllRecipients();
            $mail->clearAttachments();

            $mail->isSMTP();
            $mail->Host = $smtpHost;
            $mail->SMTPAuth = true;
            $mail->Username = $username;
            $mail->Password = $password;
            $mail->SMTPSecure = $config['security'];
            $mail->Port = $config['port'];

            $mail->setFrom($username, 'OJT Portal');
            $mail->addAddress($email);
            $mail->Subject = 'Your OTP Code';
            $mail->isHTML(true);
            $mail->Body = "Your OTP code is: <strong>$otp</strong><br><br>This code will expire in 2 minutes.";


            $mail->send();
            $sent = true;
            echo "OTP sent!";
            break;  // stop trying after successful send
        } catch (Exception $e) {
            $lastError = $mail->ErrorInfo;
            // try next config
        }
    }

    if (!$sent) {
        http_response_code(500);
        echo "Failed to send OTP. Last error: $lastError";
    }
}
