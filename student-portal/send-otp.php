<?php
require '../vendor/autoload.php'; // Load SendGrid's dependencies

use SendGrid\Mail\Mail; // Import only the Mail class

include("../includes/connection.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    if (isset($_POST['send'])) {
        $email = isset($_GET['email']) ? $_GET['email'] : '';
        if (empty($email)) {
            header('Location: ../index.php');
            exit;
        }

        // Generate a random OTP
        $otp = rand(100000, 999999);
        $_SESSION['otp'] = $otp;

        // Create SendGrid mail object
        $mail = new Mail();
        $mail->setFrom("daniellemarsh533@gmail.com", "PLMUN OIE");
        $mail->setSubject("Your OTP Code");
        $mail->addTo($email);
        $mail->addContent("text/plain", "Your OTP is: " . $otp);

        // Send email using SendGrid API
        $sendgrid = new \SendGrid("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"); // Replace with actual API Key
        $response = $sendgrid->send($mail);

        if ($response->statusCode() == 202) {
            echo "OTP sent successfully.";
        } else {
            echo "Failed to send OTP. Error Details: " . $response->statusCode() . " - " . $response->body();
        }
        
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
