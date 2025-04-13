<?php

require '../vendor/autoload.php';

use Swift\Mailer;
use Swift\Message;
use Swift\Transport\SmtpTransport;

require '../includes/connection.php'; // Ensure connection to the database

$response = array();

try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve values from the form
        $recipient_emails = explode(',', $_POST['recipient-email']);
        $sender = $_POST['sender'];
        $senderEmail = $_POST['sender-email'];
        $subject = $_POST['email-subject'];

        // Create the Transport
        $transport = (new Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl'))
            ->setUsername('cipa@plmun.edu.ph')
            ->setPassword('iqwrimadvoliiaoc')
            ->setStreamOptions([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ]);

        // Create the Mailer using your created Transport
        $mailer = new Swift_Mailer($transport);

        foreach ($recipient_emails as $recipient_email) {
            $recipient_email = trim($recipient_email);
            
            // Fetch students assigned to this trainerEmail
            $query = "SELECT studentID, lastname, firstname, section FROM studentinfo WHERE trainerEmail = ?";
            $stmt = $connect->prepare($query);
            $stmt->bind_param("s", $recipient_email);
            $stmt->execute();
            $result = $stmt->get_result();

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

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $student_id = $row['studentID'];
                    $last_name = $row['lastname'];
                    $first_name = $row['firstname'];

                    // Construct the link with student ID as a query parameter
                    $link = 'http://localhost/OIE-main/grading_page.php?student_id=' . urlencode($student_id);

                    // Create the link in the body
                    $body .= '<p><a href="' . $link . '">' . $last_name . ' ' . $first_name . '</a></p><br>';
                }
            } else {
                $body .= '<p>No students are currently assigned to your supervision.</p><br>';
            }

            $body .= '<p>Kindly click the provided link(s) to proceed to the grading page.</p>
                                <p>For inquiries email me at: ' . $senderEmail . '</p>
                                <p>Best Regards,</p>
                                <p>' . $sender . '<br>OJT Adviser</p>
                </div>
            </body>
            </html>';

            // Create email message
            $message = (new Swift_Message($subject))
                ->setFrom(['agl.systems.info@gmail.com' => 'PLMUN CIPA'])
                ->setTo($recipient_email)
                ->setBody($body, 'text/html');

            // Send the message
            $result = $mailer->send($message);

            if (!$result) {
                throw new Exception("Message could not be sent to $recipient_email.");
            }
        }

        $response['status'] = 'success';
        $response['message'] = 'Emails sent successfully!';
    }
} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = $e->getMessage();
    error_log($e->getMessage()); // Log the error
}

echo json_encode($response);

