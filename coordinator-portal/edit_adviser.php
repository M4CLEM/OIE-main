<?php
session_start();
include_once("../includes/connection.php");
require '../vendor/autoload.php'; // PHPMailer via Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $fullName = $_POST['editFullName'];
    $email = $_POST['editEmail'];
    $sectionRaw = $_POST['section'];
    $dept = $_POST['dept'];
    $course = $_POST['course'];
    $semester = $_POST['editSemester'];
    $schoolYear = $_POST['editSchoolYear'];
    $employeeNumber = $_POST['editEmployeeNumber'];
    $resetPassword = isset($_POST['resetPassword']) ? true : false;

    // Sanitize and prepare section
    $sections = array_filter(array_map('trim', explode(',', $sectionRaw)));
    $sectionString = implode(',', $sections);

    // Fetch old email
    $stmt = $connect->prepare("SELECT email FROM listadviser WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($oldEmail);
    $stmt->fetch();
    $stmt->close();

    // Update listadviser
    $updateAdviser = $connect->prepare("UPDATE listadviser SET employeeNumber = ?, fullName = ?, email = ?, section = ?, course = ?, dept = ?, semester = ?, schoolYear = ? WHERE id = ?");
    $updateAdviser->bind_param("ssssssssi", $employeeNumber, $fullName, $email, $sectionString, $course, $dept, $semester, $schoolYear, $id);
    $updateAdviser->execute();
    $updateAdviser->close();

    // If password is to be reset
    if ($resetPassword) {
        $plainPassword = bin2hex(random_bytes(4));
        $password = $plainPassword;

        $updateUser = $connect->prepare("UPDATE users SET username = ?, password = ? WHERE username = ?");
        $updateUser->bind_param("sss", $email, $password, $oldEmail);
        $updateUser->execute();
        $updateUser->close();

        // Send email
        $mail = new PHPMailer(true);
        $smtpHost = 'smtp.gmail.com';
        $smtpUser = 'cipa@plmun.edu.ph';
        $smtpPass = 'dogebwgizyidnura';

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
                $mail->addAddress($email, $fullName);
                $mail->isHTML(true);
                $mail->Subject = "OJT Portal | Adviser Account Credentials Updated";
                $mail->Body = "
                    <p>Hello <strong>$fullName</strong>,</p>
                    <p>Your Adviser account credentials have been updated. Here are your new login details:</p>
                    <p><strong>Department:</strong> $dept<br>
                    <strong>Course:</strong> $course<br>
                    <strong>Section(s):</strong> $sectionString</p>
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
            echo json_encode(["status" => "success", "message" => "Adviser updated and password emailed."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Updated, but email failed to send."]);
        }
    } else {
        // Only update email if reset is not requested
        $updateUser = $connect->prepare("UPDATE users SET username = ? WHERE username = ?");
        $updateUser->bind_param("ss", $email, $oldEmail);
        $updateUser->execute();
        $updateUser->close();

        echo json_encode(["status" => "success", "message" => "Adviser information updated successfully."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
