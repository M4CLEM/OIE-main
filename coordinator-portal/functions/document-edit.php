<?php
session_start();
require __DIR__ . '/../../vendor/autoload.php';
include_once("../../includes/connection.php");

use Google\Client as Google_Client;
use Google\Service\Drive as Google_Service_Drive;
use Google\Service\Drive\DriveFile as Google_Service_Drive_DriveFile;

function updateGoogleDriveFile($fileId, $newFile) {
    $client = new Google_Client();
    $client->setAuthConfig('../credentials.json');
    $client->addScope(Google_Service_Drive::DRIVE_FILE);
    $client->setAccessToken(json_decode(file_get_contents('../token.json'), true));

    if ($client->isAccessTokenExpired()) {
        // Handle token refresh logic here if needed
    }

    $driveService = new Google_Service_Drive($client);

    // Update file metadata
    $fileMetadata = new Google_Service_Drive_DriveFile([
        'name' => $newFile['name']
    ]);

    $content = file_get_contents($newFile['tmp_name']);

    $updatedFile = $driveService->files->update(
        $fileId, 
        $fileMetadata, 
        [
            'data' => $content,
            'mimeType' => $newFile['type'],
            'uploadType' => 'multipart'
        ]
    );

    if ($updatedFile) {
        return [
            'file_link' => "https://drive.google.com/file/d/" . $updatedFile->id,
            'file_name' => $newFile['name']
        ];
    }

    return false;
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (!isset($_POST['id'])) {
        die("Error: Missing document ID.");
    }

    $documentId = $_POST['id'];
    $documentName = htmlspecialchars($_POST['editDocumentName'], ENT_QUOTES, 'UTF-8');
    $documentType = htmlspecialchars($_POST['editDocumentType'], ENT_QUOTES, 'UTF-8');

    // Retrieve existing file details from the database
    $sql = "SELECT file_template, file_name FROM documents_list WHERE id = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("i", $documentId);
    $stmt->execute();
    $stmt->bind_result($fileLink, $existingFileName);
    $stmt->fetch();
    $stmt->close();

    if (!$fileLink) {
        die("Error: File record not found.");
    }

    // Extract Google Drive File ID
    preg_match('/\/d\/([^\/]+)/', $fileLink, $matches);
    $fileId = $matches[1] ?? '';

    if (!$fileId) {
        die("Error: Invalid Google Drive file ID.");
    }

    $driveFileLink = $fileLink; // Keep existing file link
    $fileName = $existingFileName; // Keep existing file name

    if (isset($_FILES["updateFile"]) && $_FILES["updateFile"]["error"] == 0) {
        $allowedTypes = ['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $fileType = mime_content_type($_FILES["updateFile"]["tmp_name"]);

        if (!in_array($fileType, $allowedTypes)) {
            die("Error: Invalid file type. Only PDF and DOCX files are allowed.");
        }

        // Update file on Google Drive
        $uploadResult = updateGoogleDriveFile($fileId, $_FILES["updateFile"]);

        if (!$uploadResult) {
            die("Error: Failed to update file on Google Drive.");
        }

        $driveFileLink = $uploadResult['file_link'];
        $fileName = $uploadResult['file_name'];
    }

    // Update database query (conditionally update file_name)
    $sql = "UPDATE documents_list SET documentName = ?, documentType = ?, file_template = ?";
    
    // Only update file_name if a new file was uploaded
    if ($fileName !== $existingFileName) {
        $sql .= ", file_name = ?";
    }

    $sql .= " WHERE id = ?";

    $stmt = $connect->prepare($sql);
    
    if ($stmt) {
        if ($fileName !== $existingFileName) {
            $stmt->bind_param("ssssi", $documentName, $documentType, $driveFileLink, $fileName, $documentId);
        } else {
            $stmt->bind_param("sssi", $documentName, $documentType, $driveFileLink, $documentId);
        }
        
        if ($stmt->execute()) {
            header("Location: ../documents.php?success=updated");
            exit();
        } else {
            echo "Error updating record: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $connect->error;
    }

    $connect->close();
}
?>
