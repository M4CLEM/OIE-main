<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userOTP = $_POST['otp'] ?? '';

    if (!isset($_SESSION['otp'], $_SESSION['otp_created_at'])) {
        echo "no_otp";
        exit;
    }

    // OTP expired? (5 minutes = 300 seconds)
    if (time() - $_SESSION['otp_created_at'] > 120) {
        unset($_SESSION['otp'], $_SESSION['otp_created_at'], $_SESSION['otp_verified']);
        echo "expired";
        exit;
    }

    // Check if OTP matches
    if ($userOTP == $_SESSION['otp']) {
        $_SESSION['otp_verified'] = true;
        echo "verified";
    } else {
        echo "invalid";
    }
}
?>
