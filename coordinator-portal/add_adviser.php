<?php
session_start();
include_once("../includes/connection.php");
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
        $insertUser = $connect->prepare("INSERT INTO users (username, role, password) VALUES (?, ?, ?)");
        $insertUser->bind_param("sss", $email, $role, $plainPassword);
        $insertUser->execute();
        $insertUser->close();
    }

    // SMTP Configurations to try
    $smtpHost = 'smtp.gmail.com';
    $smtpUser = 'accsample193@gmail.com'; // Sender email
    $smtpPass = 'llfjcrvifhijwgqs';  // App password

    $smtpPorts = [
        ['port' => 587, 'secure' => 'tls'],
        ['port' => 465, 'secure' => 'ssl'],
        ['port' => 25,  'secure' => 'tls']
    ];

    $sent = false;

    foreach ($smtpPorts as $config) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $smtpHost;
            $mail->SMTPAuth = true;
            $mail->Username = $smtpUser;
            $mail->Password = $smtpPass;
            $mail->SMTPSecure = $config['secure'];
            $mail->Port = $config['port'];

            $mail->setFrom($smtpUser, 'CIPA Admin');
            $mail->addAddress($email, $fullName);

            $mail->isHTML(true);
            $mail->Subject = "OJT Portal Account Created | Adviser";
            $mail->Body = "
                <p>Hello <strong>$fullName</strong>,</p>
                <p>Your Adviser account has been created in the OJT Portal.</p>
                <p><strong>Department:</strong> $dept<br>
                   <strong>Course:</strong> $course</p>
                <p><strong>Email:</strong> $email<br>
                   <strong>Password:</strong> $plainPassword</p>
                <p>Please log in and change your password immediately.</p>
                <p>Thank you,<br>CIPA Admin</p>
            ";

            $mail->send();
            $sent = true;
            break;
        } catch (Exception $e) {
            // Try next config
            continue;
        }
    }

    echo json_encode(["status" => "success", "message" => "Adviser account created and password sent via email."]);
    exit;
}
?>
