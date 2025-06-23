<?php
include_once("../../includes/connection.php");
require '../../vendor/autoload.php'; // Composer autoload (keep it if you use other packages)

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];
    $companyName = mysqli_real_escape_string($connect, $_POST["editCompanyName"]);
    $email = mysqli_real_escape_string($connect, $_POST["editEmail"]);
    $resetPassword = isset($_POST["resetPassword"]);

    if ($resetPassword) {
        $plainPassword = bin2hex(random_bytes(4)); // Generate 8-character password
        $query = "UPDATE users SET companyName='$companyName', username='$email', password='$plainPassword' WHERE id='$id'";
    } else {
        $query = "UPDATE users SET companyName='$companyName', username='$email' WHERE id='$id'";
    }

    if (!mysqli_query($connect, $query)) {
        echo json_encode(["status" => "error", "message" => "Failed to update account."]);
        exit();
    }

    
    if ($resetPassword) {
        $brevoApiKey = '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'; // Replace with your API key

        $emailBody = "
            <p>Hello <strong>$companyName</strong>,</p>
            <p>Your account credentials have been updated. Here are your new login details:</p>
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
                ['email' => $email, 'name' => $companyName]
            ],
            'subject' => "OJT Portal | Account Credentials Updated for $companyName",
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
            echo json_encode(["status" => "success", "message" => "Company account updated and password emailed."]);
        } else {
            error_log("Brevo API Error [$httpCode]: $response");
            echo json_encode(["status" => "error", "message" => "Updated, but failed to send password email."]);
        }
    } else {
        echo json_encode(["status" => "success", "message" => "Company account updated successfully."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}
?>
