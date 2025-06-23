<?php
session_start();
include_once("../../includes/connection.php");
require '../../vendor/autoload.php';

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['staffname']);
    $email = trim($_POST['email']);
    $role = trim($_POST['role']);
    $employeeNumber = trim($_POST['employeenumber']);
    $department = ($role === "CIPA") ? null : trim($_POST['department']);

    if (empty($name) || empty($email) || empty($role) || empty($employeeNumber) || ($role !== "CIPA" && empty($department))) {
        echo json_encode(["status" => "error", "message" => "Please fill all required fields."]);
        exit();
    }

    // Check if email exists
    $emailCheckQuery = "SELECT email FROM staff_list WHERE email = ? UNION SELECT username FROM users WHERE username = ?";
    $emailCheckStmt = $connect->prepare($emailCheckQuery);
    $emailCheckStmt->bind_param("ss", $email, $email);
    $emailCheckStmt->execute();
    $emailCheckStmt->store_result();
    if ($emailCheckStmt->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Email is already in use."]);
        exit();
    }
    $emailCheckStmt->close();

    // Generate password
    $plainPassword = bin2hex(random_bytes(4));

    // Insert staff
    if ($role === "CIPA") {
        $stmt = $connect->prepare("INSERT INTO staff_list (employeeNumber, name, email, password, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $employeeNumber, $name, $email, $plainPassword, $role);
    } else {
        $stmt = $connect->prepare("INSERT INTO staff_list (employeeNumber, name, email, password, role, department) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $employeeNumber, $name, $email, $plainPassword, $role, $department);
    }

    if (!$stmt->execute()) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $stmt->error]);
        exit();
    }

    // Insert user login
    if ($role === "CIPA") {
        $accStmt = $connect->prepare("INSERT INTO users (username, role, password) VALUES (?, ?, ?)");
        $accStmt->bind_param("sss", $email, $role, $plainPassword);
    } else {
        $accStmt = $connect->prepare("INSERT INTO users (username, role, password, department) VALUES (?, ?, ?, ?)");
        $accStmt->bind_param("ssss", $email, $role, $plainPassword, $department);
    }

    if (!$accStmt->execute()) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $accStmt->error]);
        exit();
    }

    $accStmt->close();

    // Role label
    $roleLabelMap = [
        "CIPA" => "CIPA Director",
        "Coordinator" => "Coordinator",
    ];
    $roleDisplay = $roleLabelMap[$role] ?? ucfirst(strtolower($role));

    // Brevo API send
    $apiKey = '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'; // replace with your API key

    $emailBody = "
        <p>Hello <strong>$name</strong>,</p>
        <p>Your <strong>$roleDisplay</strong> account has been created.</p>
        <p><strong>Login Email:</strong> $email<br>
        <strong>Password:</strong> $plainPassword</p>
        <p>Please log in and change your password immediately.</p>
        <p>Thank you,<br>CIPA Admin</p>
    ";

    $data = [
        'sender' => [
            'name' => 'CIPA Admin',
            'email' => 'cipa@plmun.edu.ph' // Must be verified in Brevo
        ],
        'to' => [
            ['email' => $email, 'name' => $name]
        ],
        'subject' => "$roleDisplay Account Created | OJT Portal",
        'htmlContent' => $emailBody
    ];

    $ch = curl_init('https://api.brevo.com/v3/smtp/email');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'api-key: ' . $apiKey
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $responseRaw = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 201) {
        echo json_encode(["status" => "success", "message" => "Account created and password sent via email."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Account created, but failed to send email. Brevo: $responseRaw"]);
    }

    $stmt->close();
    $connect->close();
}
?>
