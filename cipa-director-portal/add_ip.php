<?php
session_start();
include_once("../includes/connection.php");
require '../vendor/autoload.php'; // PHPMailer via Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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

    // Generate random password and hash it
    $plainPassword = bin2hex(random_bytes(4)); // 8-character password
    
    // Insert into users table
    $insertStmt = $connect->prepare("INSERT INTO users (companyName, username, role, password) VALUES (?, ?, ?, ?)");
    $insertStmt->bind_param("ssss", $compName, $email, $role, $plainPassword);

    if (!$insertStmt->execute()) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $insertStmt->error]);
        exit();
    }
    $insertStmt->close();

    // Send email with PHPMailer + SMTP
    $mail = new PHPMailer(true);

    $smtpHost = 'smtp.gmail.com';
    $smtpUser = 'accsample193@gmail.com'; // ← Your sender email
    $smtpPass = 'llfjcrvifhijwgqs';  // ← Your app password

    $smtpPorts = [
        ['port' => 587, 'secure' => 'tls'],
        ['port' => 465, 'secure' => 'ssl'],
        ['port' => 25,  'secure' => 'tls'],
    ];

    $sent = false;

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
            $mail->addAddress($email, $compName);
            $mail->isHTML(true);
            $mail->Subject = "OJT Portal Account Created | $compName";
            $mail->Body = "
                <p>Hello <strong>$compName</strong>,</p>
                <p>Your <strong>Industry Partner</strong> account has been successfully created in the OJT Portal.</p>
                <p><strong>Email:</strong> $email<br>
                   <strong>Password:</strong> $plainPassword</p>
                <p>Please log in and change your password immediately.</p>
                <p>Thank you,<br>CIPA Admin</p>
            ";

            $mail->send();
            $sent = true;
            break;
        } catch (Exception $e) {
            continue;
        }
    }

    if ($sent) {
        echo json_encode(["status" => "success", "message" => "Company account created. Password sent via email."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Account created, but email sending failed."]);
    }

    $connect->close();
    exit();
}
?>
