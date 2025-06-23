<?php
session_start();
include_once("../includes/connection.php");
require '../vendor/autoload.php'; // Still required for Composer packages if any

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $compName = trim($_POST['companyName']);
    $email = trim($_POST['email']);
    $role = "IndustryPartner";

    // Check if email already exists
    $emailCheckQuery = "SELECT username FROM users WHERE username = ?";
    $stmt = $connect->prepare($emailCheckQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Email is already in use."]);
        exit();
    }
    $stmt->close();

    // Generate random password
    $plainPassword = bin2hex(random_bytes(4)); // 8-character password

    // Insert into users table
    $insertStmt = $connect->prepare("INSERT INTO users (companyName, username, role, password) VALUES (?, ?, ?, ?)");
    $insertStmt->bind_param("ssss", $compName, $email, $role, $plainPassword);

    if (!$insertStmt->execute()) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $insertStmt->error]);
        exit();
    }
    $insertStmt->close();

    // ✅ Send email using Brevo API
    $brevoApiKey = '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'; // replace with your Brevo API key

    $emailBody = "
        <p>Hello <strong>$compName</strong>,</p>
        <p>Your <strong>Industry Partner</strong> account has been successfully created in the OJT Portal.</p>
        <p><strong>Email:</strong> $email<br>
        <strong>Password:</strong> $plainPassword</p>
        <p>Please log in and change your password immediately.</p>
        <p>Thank you,<br>CIPA Admin</p>
    ";

    $payload = [
        'sender' => [
            'name' => 'CIPA Admin',
            'email' => 'cipa@plmun.edu.ph' // Must be verified in Brevo
        ],
        'to' => [
            ['email' => $email, 'name' => $compName]
        ],
        'subject' => "OJT Portal Account Created | $compName",
        'htmlContent' => $emailBody
    ];

    $ch = curl_init('https://api.brevo.com/v3/smtp/email');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'api-key: ' . $brevoApiKey
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 201) {
        echo json_encode(["status" => "success", "message" => "Company account created. Password sent via email."]);
    } else {
        error_log("Brevo API Error [$httpCode]: $response");
        echo json_encode(["status" => "error", "message" => "Account created, but email sending failed."]);
    }

    $connect->close();
    exit();
}
?>
