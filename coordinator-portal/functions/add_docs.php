<?php
session_start();
require __DIR__ . '/../../vendor/autoload.php'; // Ensure Google API client is installed
include_once("../../includes/connection.php");

use Google\Client as Google_Client;
use Google\Service\Drive as Google_Service_Drive;
use Google\Service\Drive\DriveFile as Google_Service_Drive_DriveFile;

// Main Google Drive Folder ID
const MAIN_FOLDER_ID = '1PeYBhkC6Y67v-VLQ8HUMC0IAKMBOJ6NL'; // Change to your actual folder ID

/**
 * Function to check if a folder exists in Google Drive, and create it if not
 * @param Google_Service_Drive $driveService
 * @param string $parentFolderId - ID of the parent folder
 * @param string $folderName - Name of the folder to check or create
 * @return string - The folder ID
 */
function getOrCreateFolder($driveService, $parentFolderId, $folderName) {
    $query = "name = '$folderName' and '$parentFolderId' in parents and mimeType = 'application/vnd.google-apps.folder' and trashed = false";
    $folders = $driveService->files->listFiles(['q' => $query])->getFiles();

    if (count($folders) > 0) {
        return $folders[0]->getId(); // Return existing folder ID
    }

    // Create new folder
    $folderMetadata = new Google_Service_Drive_DriveFile([
        'name' => $folderName,
        'mimeType' => 'application/vnd.google-apps.folder',
        'parents' => [$parentFolderId]
    ]);
    $folder = $driveService->files->create($folderMetadata, ['fields' => 'id']);
    return $folder->getId();
}

/**
 * Function to upload a file to Google Drive in a structured folder
 * @param array $file - The uploaded file ($_FILES entry)
 * @param string $department - The department name
 * @param string $documentType - The document type
 * @return array|false - Returns an array with file link and name if successful, otherwise false
 */
function uploadToGoogleDrive($file, $department, $documentType) {
    $client = new Google_Client();
    $client->setAuthConfig(__DIR__ . '/../../credentials/credentials.json');
    $client->addScope(Google_Service_Drive::DRIVE_FILE);
    $client->setAccessToken(json_decode(file_get_contents(__DIR__ . '/../../credentials/token.json'), true));

    if ($client->isAccessTokenExpired()) {
        // Handle token refresh logic if needed
    }

    $driveService = new Google_Service_Drive($client);

    // Get or create the department folder inside the main folder
    $departmentFolderId = getOrCreateFolder($driveService, MAIN_FOLDER_ID, $department);
    
    // Get or create the document type folder inside the department folder
    $documentTypeFolderId = getOrCreateFolder($driveService, $departmentFolderId, $documentType);

    // Upload file to the correct folder
    $fileMetadata = new Google_Service_Drive_DriveFile([
        'name' => $file['name'],
        'parents' => [$documentTypeFolderId] // Upload inside correct subfolder
    ]);

    $content = file_get_contents($file['tmp_name']);
    $uploadedFile = $driveService->files->create($fileMetadata, [
        'data' => $content,
        'mimeType' => $file['type'],
        'uploadType' => 'multipart'
    ]);

    if ($uploadedFile) {
        return [
            'file_link' => "https://drive.google.com/file/d/" . $uploadedFile->id,
            'file_name' => $file['name']
        ];
    }
    
    return false;
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (!isset($_SESSION['department'])) {
        die("Error: Department not set in session.");
    }

    $department = $_SESSION['department'];
    $documentName = htmlspecialchars($_POST['addDocumentName'], ENT_QUOTES, 'UTF-8');
    $documentType = !empty($_POST['addDocumentType']) ? htmlspecialchars($_POST['addDocumentType'], ENT_QUOTES, 'UTF-8') : htmlspecialchars($_POST['documentTypeDropdown'], ENT_QUOTES, 'UTF-8');
    $multipleUploadsEnabled = isset($_POST['multipleUploads']) ? true : false;

    // File upload handling
    $driveFileLink = null;
    $fileName = null;
    if (isset($_FILES["newFile"]) && $_FILES["newFile"]["error"] == 0) {
        $allowedTypes = ['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $fileType = mime_content_type($_FILES["newFile"]["tmp_name"]);
        
        if (!in_array($fileType, $allowedTypes)) {
            die("Error: Invalid file type. Only PDF and DOCX files are allowed.");
        }
        
        // Upload to Google Drive with department & document type folder structure
        $uploadResult = uploadToGoogleDrive($_FILES["newFile"], $department, $documentType);
        
        if (!$uploadResult) {
            die("Error: Failed to upload file to Google Drive.");
        }
        
        $driveFileLink = $uploadResult['file_link'];
        $fileName = $uploadResult['file_name'];
    }

    // Insert into database
    $sql = "INSERT INTO documents_list (documentName, documentType, department, file_template, file_name, multiUpload) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $connect->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssssss", $documentName, $documentType, $department, $driveFileLink, $fileName, $multipleUploadsEnabled);
        if ($stmt->execute()) {
            header("Location: ../documents.php?success=1");
            exit();
        } else {
            echo "Error executing statement: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $connect->error;
    }

    $connect->close();
}
?>
