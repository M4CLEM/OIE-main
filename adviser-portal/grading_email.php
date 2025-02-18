<?php

require '../vendor/autoload.php';

use Swift\Mailer;
use Swift\Message;
use Swift\Transport\SmtpTransport;

$response = array();

try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve values from the form
        $recipient_email = $_POST['recipient-email'];
        $sender = $_POST['sender'];
        $senderEmail = $_POST['sender-email'];
        $subject = $_POST['email-subject'];
        $students = $_POST['students'];

        // Create the Transport
        $transport = (new Swift_SmtpTransport('smtp.gmail.com', 587, 'tls'))
            ->setUsername('citcs_ojt@plmun.edu.ph')
            ->setPassword('euvobesjlupefvsq')
            ->setStreamOptions([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ]);

        // Create the Mailer using your created Transport
        $mailer = new Swift_Mailer($transport);

        // Prepare email body
        $body = '
        <!DOCTYPE html>
        <html>
        <head>
        </head>
        <body>
            <div class="container">
                <p>Good Day!</p>
                <p>I hope this letter finds you well. I am writing to inform you that the list below are the following deployed student(s) in your company that requires grading.</p>
                <br>';
        $studs = explode("\n", $students);
        foreach ($studs as $stud) {
            $student_info = explode(' ', trim($stud)); // Trim each line to remove leading/trailing whitespace
            $student_id = trim($student_info[0]);
            $last_name = trim($student_info[1]);
            $first_name = trim($student_info[2]);
            $section = trim($student_info[3]);

            // Construct the link with student ID as a query parameter
            $link = 'http://localhost/OIE-main/grading_page.php?student_id=' . urlencode($student_id);

            // Create the link in the body
            $body .= '<p><a href="' . $link . '">' . $last_name . ' ' . $first_name . '</a></p><br>';
        }

        $body .= '<p>Kindly click the provided link(s) to proceed to the grading page.</p>
                            <p>For inquiries email me at: ' . $senderEmail . '</p>
                            <p>Best Regards,</p>
                            <p>' . $sender . '<br>OJT Adviser</p>
            </div>
        </body>
        </html>';

        // Create a message
        $message = (new Swift_Message($subject))
            ->setFrom(['agl.systems.info@gmail.com' => 'PLMUN CIPA'])
            ->setTo([$recipient_email])
            ->setBody($body, 'text/html');

        // Send the message
        $result = $mailer->send($message);

        if ($result) {
            $response['status'] = 'success';
            $response['message'] = 'Email sent successfully!';
        } else {
            throw new Exception('Message could not be sent.');
        }
    }
} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = $e->getMessage();
    // Log the error message
    error_log($e->getMessage());
}

echo json_encode($response);
