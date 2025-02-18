<?php

require '../vendor/autoload.php';

use Swift\Mailer;
use Swift\Message;
use Swift\Transport\SmtpTransport;

try {
    if (isset($_POST['send'])) {
        $email = isset($_GET['email']) ? $_GET['email'] : '';
        if (empty($email)) {
            header('Location: ../index.php');
            exit; // Ensure the script stops executing after the redirect
        }

        // Generate OTP
        $otp = mt_rand(100000, 999999);
        $_SESSION['otp'] = $otp;

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

        // Create a message
        $message = (new Swift_Message('OTP Verification Code'))
            ->setFrom(['garcenico_bsit@plmun.edu.ph' => 'PLMUN OIE'])
            ->setTo([$email])
            ->setBody('
                <!DOCTYPE html>
                <html>
                <head>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            background-color: #f0f0f0;
                        }
                        .container {
                            max-width: 600px;
                            margin: 0 auto;
                            padding: 20px;
                            background-color: #ffffff;
                            border-radius: 5px;
                            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
                        }
                        h1 {
                            color: #333;
                        }
                        p {
                            font-size: 16px;
                            line-height: 1.6;
                        }
                        #otp {
                            font-size: 24px;
                            font-weight: bold;
                            color: #007bff;
                        }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <h1>OTP Verification</h1>
                        <p>Your OTP (One-Time Password) for verification is: <span id="otp">' . $otp . '</span></p>
                    </div>
                </body>
                </html>', 'text/html');

        // Send the message
        $result = $mailer->send($message);

        if ($result) {
            echo "OTP sent successfully!";
        } else {
            throw new Exception('Message could not be sent.');
        }
    }
} catch (Exception $e) {
    echo "Message could not be sent. Error: " . $e->getMessage();
}
