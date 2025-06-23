<?php
session_start();
include_once("../includes/connection.php");
require '../vendor/autoload.php';

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = $_POST['fullName'];
    $email = $_POST['email'];
    $sectionRaw = $_POST['section'];
    $dept = $_POST['dept'];
    $course = $_POST['course'];
    $semester = $_POST['semester'];
    $schoolYear = $_POST['schoolYear'];
    $employeeNumber = $_POST['employeeNumber'];
    $role = "Adviser";

    $plainPassword = bin2hex(random_bytes(4)); // 8-character random password

    $sections = array_filter(array_map('trim', explode(',', $sectionRaw)));
    $sectionString = implode(',', $sections);

    // Check if email already exists in users table
    $stmt = $connect->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($userCount);
    $stmt->fetch();
    $stmt->close();

    // Insert into listadviser table
    $insertList = $connect->prepare("INSERT INTO listadviser (employeeNumber, fullName, email, section, course, dept, semester, schoolYear) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $insertList->bind_param("ssssssss", $employeeNumber, $fullName, $email, $sectionString, $course, $dept, $semester, $schoolYear);
    $insertList->execute();
    $insertList->close();

    // Insert into users table only if not exists
    if ($userCount == 0) {
        $insertUser = $connect->prepare("INSERT INTO users (username, role, password, department) VALUES (?, ?, ?, ?)");
        $insertUser->bind_param("ssss", $email, $role, $plainPassword, $dept);
        $insertUser->execute();
        $insertUser->close();
    }

    // ✅ Send email using Brevo API key
    $brevoApiKey = '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';

    $emailBody = "
        <p>Hello <strong>$fullName</strong>,</p>
        <p>Your Adviser account has been created in the OJT Portal.</p>
        <p><strong>Department:</strong> $dept<br>
           <strong>Course:</strong> $course</p>
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
            ['email' => $email, 'name' => $fullName]
        ],
        'subject' => "OJT Portal Account Created | Adviser",
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
        echo json_encode(["status" => "success", "message" => "Adviser account created and password sent via email."]);
    } else {
        error_log("Brevo API Error [$httpCode]: $response");
        echo json_encode(["status" => "error", "message" => "Account created, but email sending failed."]);
    }

    exit;
}
?>
