<?php
session_start();
include_once("../includes/connection.php");
require '../vendor/autoload.php';

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

    // Reset password
    if ($resetPassword) {
        $plainPassword = bin2hex(random_bytes(4));

        $updateUser = $connect->prepare("UPDATE users SET username = ?, password = ? WHERE username = ?");
        $updateUser->bind_param("sss", $email, $plainPassword, $oldEmail);
        $updateUser->execute();
        $updateUser->close();

        // ✅ Send via Brevo API
        $brevoApiKey = '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'; // Replace with your API key

        $emailBody = "
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

        $payload = [
            'sender' => [
                'name' => 'CIPA Admin',
                'email' => 'cipa@plmun.edu.ph'
            ],
            'to' => [
                ['email' => $email, 'name' => $fullName]
            ],
            'subject' => "OJT Portal | Adviser Account Credentials Updated",
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
            echo json_encode(["status" => "success", "message" => "Adviser updated and password emailed."]);
        } else {
            error_log("Brevo API Error [$httpCode]: $response");
            echo json_encode(["status" => "error", "message" => "Updated, but email failed to send."]);
        }
    } else {
        // Only update email in users table if no password reset
        $updateUser = $connect->prepare("UPDATE users SET username = ? WHERE username = ?");
        $updateUser->bind_param("ss", $email, $oldEmail);
        $updateUser->execute();
        $updateUser->close();

        echo json_encode(["status" => "success", "message" => "Adviser information updated successfully."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>
