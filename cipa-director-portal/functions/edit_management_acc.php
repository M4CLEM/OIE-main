<?php
session_start();
include_once("../../includes/connection.php");
require '../../vendor/autoload.php'; // Make sure PHPMailer is installed via Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = trim($_POST['id']);
    $employeeNumber = trim($_POST['editEmployeeNumber']);
    $name = trim($_POST['editStaffName']);
    $email = trim($_POST['editEmail']);
    $role = trim($_POST['editRole']);
    $resetPassword = isset($_POST['resetPassword']) && $_POST['resetPassword'] == '1';

    // Get current email before updating
    $currentEmailQuery = "SELECT email FROM staff_list WHERE id = ?";
    $currentEmailStmt = $connect->prepare($currentEmailQuery);
    $currentEmailStmt->bind_param("i", $id);
    $currentEmailStmt->execute();
    $currentEmailStmt->bind_result($currentEmail);
    $currentEmailStmt->fetch();
    $currentEmailStmt->close();

    // Check if email already exists
    $emailCheckQuery = "SELECT id FROM staff_list WHERE email = ? AND id != ? UNION SELECT id FROM users WHERE username = ? AND username != ?";
    $emailCheckStmt = $connect->prepare($emailCheckQuery);
    $emailCheckStmt->bind_param("ssss", $email, $id, $email, $currentEmail);
    $emailCheckStmt->execute();
    $emailCheckStmt->store_result();

    if ($emailCheckStmt->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Email is already in use."]);
        exit();
    }
    $emailCheckStmt->close();

    if ($role === "CIPA") {
        $sql = "UPDATE staff_list SET employeeNumber = ?, name = ?, email = ?, role = ? WHERE id = ?";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("ssssi", $employeeNumber, $name, $email, $role, $id);
    } else {
        $department = trim($_POST['editDepartment']);
        $sql = "UPDATE staff_list SET employeeNumber = ?, name = ?, email = ?, role = ?, department = ? WHERE id = ?";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("sssssi", $employeeNumber, $name, $email, $role, $department, $id);
    }

    if ($stmt->execute()) {
        // Update users table email
        $updateUsernameStmt = $connect->prepare("UPDATE users SET username = ? WHERE username = ?");
        $updateUsernameStmt->bind_param("ss", $email, $currentEmail);
        $updateUsernameStmt->execute();
        $updateUsernameStmt->close();

        if ($role === "CIPA") {
            $accStmt = $connect->prepare("UPDATE users SET role = ? WHERE username = ?");
            $accStmt->bind_param("ss", $role, $email);
        } else {
            $accStmt = $connect->prepare("UPDATE users SET role = ?, department = ? WHERE username = ?");
            $accStmt->bind_param("sss", $role, $department, $email);
        }
        $accStmt->execute();
        $accStmt->close();

        // If reset password is requested
        if ($resetPassword) {
            $plainPassword = bin2hex(random_bytes(4)); // 8-character password

            // Update both staff_list and users table
            $passStmt = $connect->prepare("UPDATE staff_list SET password = ? WHERE id = ?");
            $passStmt->bind_param("si", $plainPassword, $id);
            $passStmt->execute();
            $passStmt->close();

            $passUserStmt = $connect->prepare("UPDATE users SET password = ? WHERE username = ?");
            $passUserStmt->bind_param("ss", $plainPassword, $email);
            $passUserStmt->execute();
            $passUserStmt->close();

            // Send email with new credentials
            $smtpHost = 'smtp.gmail.com';
            $smtpUser = 'cipa@plmun.edu.ph';
            $smtpPass = 'oaoybffujhnigslm';
            $smtpPorts = [
                ['port' => 587, 'secure' => 'tls'],
                ['port' => 465, 'secure' => 'ssl'],
                ['port' => 25, 'secure' => 'tls']
            ];

            $sent = false;
            foreach ($smtpPorts as $config) {
                try {
                    $mail = new PHPMailer(true);
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
                    $mail->Subject = "Updated Credentials for Your $role Account";
                    $mail->Body = "
                        <p>Hello <strong>$name</strong>,</p>
                        <p>Your <strong>$role</strong> account credentials have been reset.</p>
                        <p><strong>Department:</strong> $department</p>
                        <p><strong>Login Email:</strong> $email<br>
                        <strong>New Password:</strong> $plainPassword</p>
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
        }

        echo json_encode(["status" => "success", "message" => "Update successful!"]);
        exit();
    }

    echo json_encode(["status" => "error", "message" => "Update failed."]);
    exit();
}
$connect->close();
?>
