<?php
session_start();
include_once("../../includes/connection.php");
require '../../vendor/autoload.php'; // PHPMailer via Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['staffname']);
    $email = trim($_POST['email']);
    $role = trim($_POST['role']);
    $employeeNumber = trim($_POST['employeenumber']);
    $department = ($role === "CIPA") ? null : trim($_POST['department']);

    // Validate required fields
    if (empty($name) || empty($email) || empty($role) || empty($employeeNumber) || ($role !== "CIPA" && empty($department))) {
        echo json_encode(["status" => "error", "message" => "Please fill all required fields."]);
        exit();
    }

    // Check if email already exists in either table
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

    // Generate random password and hash it
    $plainPassword = bin2hex(random_bytes(4)); // 8-character password

    // Insert into staff_list
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

    // Insert into users table
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

    // Send email with PHPMailer + SMTP with port fallback
    $mail = new PHPMailer(true);

    $smtpHost = 'smtp.gmail.com';          // ← REPLACE with your SMTP host
    $smtpUser = 'accsample193@gmail.com';    // ← REPLACE with your SMTP username/email
    $smtpPass = 'llfjcrvifhijwgqs';       // ← REPLACE with your SMTP password

    $smtpPorts = [
        ['port' => 587, 'secure' => 'tls'],
        ['port' => 465, 'secure' => 'ssl'],
        ['port' => 25,  'secure' => 'tls'],
    ];

    $sent = false;

    // Optional: Better display labels for roles
    $roleLabelMap = [
        "CIPA" => "CIPA Director",
        "Coordinator" => "Coordinator",
        // Add more roles if needed
    ];

    $roleDisplay = isset($roleLabelMap[$role]) ? $roleLabelMap[$role] : ucfirst(strtolower($role));

    // Attempt sending email using multiple SMTP ports
    foreach ($smtpPorts as $config) {
        try {
            $mail->isSMTP();
            $mail->Host = $smtpHost;
            $mail->SMTPAuth = true;
            $mail->Username = $smtpUser;
            $mail->Password = $smtpPass;
            $mail->SMTPSecure = $config['secure'];
            $mail->Port = $config['port'];

            $mail->setFrom($smtpUser, 'CIPA Admin');
            $mail->addAddress($email, $name);
            $mail->isHTML(true);

            // ✅ Dynamic subject based on role
            $mail->Subject = "$roleDisplay Account Created | OJT Portal";

            // ✅ Dynamic body with role-based message
            $mail->Body = "
            <p>Hello <strong>$name</strong>,</p>
            <p>Your <strong>$roleDisplay</strong> account has been created.</p>
            
            <p><strong>Login Email:</strong> $email<br>
            <strong>Password:</strong> $plainPassword</p>
            <p>Please log in and change your password immediately.</p>
            <p>Thank you,<br>CIPA Admin</p>
        ";

            $mail->send();
            $sent = true;
            break; // stop on successful send
        } catch (Exception $e) {
            // fail silently and try next config
            continue;
        }
    }


    if ($sent) {
        echo json_encode(["status" => "success", "message" => "Account created and password sent via email."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Account created, but failed to send email on all ports."]);
    }

    $stmt->close();
    $connect->close();
    exit();
}
?>
