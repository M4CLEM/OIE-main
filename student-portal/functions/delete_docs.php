<?php
session_start();
require __DIR__ . '/../../vendor/autoload.php'; // Ensure Google API client is installed
include_once("../../includes/connection.php");

use Google\Client as Google_Client;
use Google\Service\Drive as Google_Service_Drive;

function deleteFromGoogleDrive($fileId) {
    $client = new Google_Client();
    $client->setAuthConfig(__DIR__ . '/../../credentials/credentials.json'); // Ensure correct path
    $client->addScope(Google_Service_Drive::DRIVE_FILE);
    $client->setAccessToken(json_decode(file_get_contents(__DIR__ . '/../../credentials/token.json'), true));

    if ($client->isAccessTokenExpired()) {
        // Handle token refresh logic here if needed
    }

    $driveService = new Google_Service_Drive($client);
    try {
        $driveService->files->delete($fileId);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $documentId = $_POST['id'];
    
    // Retrieve file ID from database
    $sql = "SELECT file_template FROM documents_list WHERE id = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("i", $documentId);
    $stmt->execute();
    $stmt->bind_result($fileLink);
    $stmt->fetch();
    $stmt->close();

    if ($fileLink) {
        // Extract file ID from Google Drive link
        preg_match('/\/d\/(.+)/', $fileLink, $matches);
        $fileId = $matches[1] ?? '';

        if ($fileId) {
            deleteFromGoogleDrive($fileId);
        }
    }

    // Delete from database
    $deleteSql = "DELETE FROM documents_list WHERE id = ?";
    $deleteStmt = $connect->prepare($deleteSql);
    $deleteStmt->bind_param("i", $documentId);
    
    if ($deleteStmt->execute()) {
       echo json_encode(['status' => 'success', 'message' => 'Record deleted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete record']);
    }
    
} else {
    header("Location: ../documents.php?success=deleted");
        exit();
}

$deleteStmt->close();
$connect->close();
?>
