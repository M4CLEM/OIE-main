<?php
include_once("../../includes/connection.php");
require '../../vendor/autoload.php'; // PHPMailer via Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];
    $companyName = mysqli_real_escape_string($connect, $_POST["editCompanyName"]);
    $email = mysqli_real_escape_string($connect, $_POST["editEmail"]);
    $resetPassword = isset($_POST["resetPassword"]) ? true : false;

    // Build base query
    if ($resetPassword) {
        // Generate new password
        $plainPassword = bin2hex(random_bytes(4)); // 8-char
        $password = $plainPassword;

        $query = "UPDATE users SET companyName='$companyName', username='$email', password='$password' WHERE id='$id'";
    } else {
        $query = "UPDATE users SET companyName='$companyName', username='$email' WHERE id='$id'";
    }

    if (!mysqli_query($connect, $query)) {
        echo json_encode(["status" => "error", "message" => "Failed to update account."]);
        exit();
    }

    // If password was reset, send email
    if ($resetPassword) {
        $mail = new PHPMailer(true);

        $smtpHost = 'smtp.gmail.com';
        $smtpUser = 'accsample193@gmail.com';
        $smtpPass = 'llfjcrvifhijwgqs';

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
                $mail->addAddress($email, $companyName);
                $mail->isHTML(true);
                $mail->Subject = "OJT Portal | Account Credentials Updated for $companyName";
                $mail->Body = "
                    <p>Hello <strong>$companyName</strong>,</p>
                    <p>Your account credentials have been updated. Here are your new login details:</p>
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
            echo json_encode(["status" => "success", "message" => "Company account updated and password emailed."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Updated, but failed to send new password via email."]);
        }
    } else {
        echo json_encode(["status" => "success", "message" => "Company account updated successfully."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}
?>
