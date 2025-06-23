<?php

require '../vendor/autoload.php';
require '../includes/connection.php';

$response = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $recipient_emails = array_filter(array_map('trim', explode(',', $_POST['recipient-email'])));
    $sender = $_POST['sender'];
    $senderEmail = $_POST['sender-email'];
    $subject = $_POST['email-subject'];

    $apiKey = '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'; // replace this!

    foreach ($recipient_emails as $recipient_email) {
        // Fetch student info
        $query = "SELECT studentID, lastname, firstname, section FROM studentinfo WHERE trainerEmail = ?";
        $stmt = $connect->prepare($query);
        $stmt->bind_param("s", $recipient_email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Construct email HTML
        $body = '
        <!DOCTYPE html>
        <html>
        <body>
            <p>Good Day!</p>
            <p>I hope this letter finds you well. I am writing to inform you that the list below are the following deployed student(s) in your company that requires grading.</p><br>';

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $link = 'http://localhost/OIE-main/grading_page.php?student_id=' . urlencode($row['studentID']);
                $body .= '<p><a href="' . $link . '">' . $row['lastname'] . ' ' . $row['firstname'] . '</a></p><br>';
            }
        } else {
            $body .= '<p>No students are currently assigned to your supervision.</p><br>';
        }

        $body .= '
            <p>Kindly click the provided link(s) to proceed to the grading page.</p>
            <p>For inquiries email me at: ' . $senderEmail . '</p>
            <p>Best Regards,</p>
            <p>' . $sender . '<br>OJT Adviser</p>
        </body>
        </html>';

        // Build API payload
        $data = [
            'sender' => [
                'name' => 'PLMUN CIPA',
                'email' => 'cipa@plmun.edu.ph' // must be verified in Brevo
            ],
            'to' => [
                ['email' => $recipient_email]
            ],
            'subject' => $subject,
            'htmlContent' => $body
        ];

        // Send via Brevo API
        $ch = curl_init('https://api.brevo.com/v3/smtp/email');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'api-key: ' . $apiKey
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $responseRaw = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode != 201) {
            $response['status'] = 'error';
            $response['message'] = "Failed to send to $recipient_email. Response: $responseRaw";
            echo json_encode($response);
            exit;
        }
    }

    $response['status'] = 'success';
    $response['message'] = 'Emails sent successfully!';
}

echo json_encode($response);
