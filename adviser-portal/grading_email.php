<?php

require '../vendor/autoload.php';

use Swift\Mailer;
use Swift\Message;
use Swift\Transport\SmtpTransport;

require '../includes/connection.php';

$response = array();

function createWorkingMailer($host, $username, $password) {
    $ports = [
        ['port' => 587, 'encryption' => 'tls'],
        ['port' => 465, 'encryption' => 'ssl'],
        ['port' => 25, 'encryption' => null]
    ];

    foreach ($ports as $config) {
        try {
            $transport = (new Swift_SmtpTransport($host, $config['port'], $config['encryption']))
                ->setUsername($username)
                ->setPassword($password)
                ->setStreamOptions([
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ]
                ]);

            $mailer = new Swift_Mailer($transport);
            $mailer->getTransport()->start(); // test connection
            return $mailer;
        } catch (Exception $e) {
            error_log("SMTP failed on port {$config['port']}: " . $e->getMessage());
            continue;
        }
    }

    throw new Exception("All SMTP ports failed. Check your credentials or network.");
}

try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve values from the form
        $recipient_emails = explode(',', $_POST['recipient-email']);
        $sender = $_POST['sender'];
        $senderEmail = $_POST['sender-email'];
        $subject = $_POST['email-subject'];

        // Create the Mailer (tries 587, 465, then 25)
        $mailer = createWorkingMailer('smtp.gmail.com', 'cipa@plmun.edu.ph', 'iqwrimadvoliiaoc');

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

                    $link = 'http://localhost/OIE-main/grading_page.php?student_id=' . urlencode($student_id);

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

            $message = (new Swift_Message($subject))
                ->setFrom(['agl.systems.info@gmail.com' => 'PLMUN CIPA'])
                ->setTo($recipient_email)
                ->setBody($body, 'text/html');

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
    error_log($e->getMessage());
}

echo json_encode($response);
