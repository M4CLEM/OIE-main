<?php
require __DIR__ . '/../vendor/autoload.php';

use Google\Client;
use Google\Service\Drive;

session_start();

function getClient() {
    $client = new Client();
    $client->setAuthConfig('credentials2.json');
    $client->addScope(Drive::DRIVE_FILE);
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');
    $client->setRedirectUri('http://localhost/OIE-main/coordinator-portal/import.php');

    // Load previously authorized token from a file, if it exists.
    $tokenPath = 'token.json';
    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
    }

    // If token is expired or missing, get a new one
    if ($client->isAccessTokenExpired()) {
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        } else {
            if (!isset($_GET['code'])) {
                $authUrl = $client->createAuthUrl();
                header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
                exit();
            } else {
                $accessToken = $client->fetchAccessTokenWithAuthCode($_GET['code']);
                $client->setAccessToken($accessToken);
                file_put_contents($tokenPath, json_encode($accessToken));
            }
        }
    }

    return $client;
}

$client = getClient();

if ($client->getAccessToken()) {
    echo "Google Drive API is authenticated! ✅";
} else {
    echo "Authentication failed. ❌";
}
?>
