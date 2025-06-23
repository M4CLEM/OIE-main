<?php
session_start();
include_once("../../includes/connection.php");
require '../../vendor/autoload.php';

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

            // ✅ Send email using Brevo API
            $brevoApiKey = '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'; // replace with your actual Brevo API key

            $roleDisplay = ucfirst(strtolower($role));
            $emailBody = "
                <p>Hello <strong>$name</strong>,</p>
                <p>Your <strong>$roleDisplay</strong> account credentials have been reset.</p>" .
                ($role !== 'CIPA' ? "<p><strong>Department:</strong> $department</p>" : "") .
                "<p><strong>Login Email:</strong> $email<br>
                <strong>New Password:</strong> $plainPassword</p>
                <p>Please log in and change your password immediately.</p>
                <p>Thank you,<br>CIPA Admin</p>";

            $brevoPayload = [
                'sender' => [
                    'name' => 'CIPA Admin',
                    'email' => 'cipa@plmun.edu.ph' // Must be verified in Brevo
                ],
                'to' => [
                    ['email' => $email, 'name' => $name]
                ],
                'subject' => "Updated Credentials for Your $roleDisplay Account",
                'htmlContent' => $emailBody
            ];

            $ch = curl_init('https://api.brevo.com/v3/smtp/email');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'api-key: ' . $brevoApiKey
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($brevoPayload));

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode != 201) {
                error_log("Brevo API Error [$httpCode]: $response");
                // You can also notify the frontend if needed
            }
        }

        echo json_encode(["status" => "success", "message" => "Update successful!"]);
        exit();
    }

    echo json_encode(["status" => "error", "message" => "Update failed."]);
    exit();
}

$connect->close();
