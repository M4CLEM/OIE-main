<?php
session_start();
require_once __DIR__ . '/../../vendor/autoload.php';
include_once("../../includes/connection.php");

$activeSemester = $_SESSION['semester'];
$activeSchoolYear = $_SESSION['schoolYear'];

use Google\Client as Google_Client;
use Google\Service\Drive as Google_Service_Drive;
use Google\Service\Drive\DriveFile as Google_Service_Drive_DriveFile;

$client = new Google_Client();
$client->setAuthConfig(__DIR__ . '/../../credentials/credentials.json');
$client->addScope(Google_Service_Drive::DRIVE_FILE);
$client->setAccessType('offline');

if (file_exists(__DIR__ . '/../../credentials/token.json')) {
    $accessToken = json_decode(file_get_contents(__DIR__ . '/../../credentials/token.json'), true);
    $client->setAccessToken($accessToken);
    
    if ($client->isAccessTokenExpired()) {
        if ($client->getRefreshToken()) {
            $newToken = $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            file_put_contents(__DIR__ . '/../../credentials/token.json', json_encode($newToken));
            $client->setAccessToken($newToken);
        } else {
            die("Token expired. Please reauthorize.");
        }
    }
} else {
    die("No token found. Please authenticate first.");
}

$driveService = new Google_Service_Drive($client);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_SESSION['student'];
    $query = "SELECT * FROM studentinfo WHERE email ='$email'";
    $result = mysqli_query($connect, $query);
    $rows = mysqli_fetch_array($result);

    $studentID = $rows['studentID'];
    $course = $rows['course'];
    $department = trim($rows['department']);
    $SY = $rows['school_year'];
    $semester = $rows['semester'];
    $section = $rows['section'];
    $documentType = $_POST['documentType']; // Document type from the modal

    $documentsFolderId = createFolder($driveService, "OJT Student Requirements", null);
    $deptFolderId = createFolder($driveService, $department, $documentsFolderId);
    $courseFolderId = createFolder($driveService, $course, $deptFolderId);
    $SYFolderId = createFolder($driveService, $SY, $courseFolderId);
    $semesterFolderId = createFolder($driveService, $semester, $SYFolderId);
    $sectionFolderId = createFolder($driveService, $section, $semesterFolderId);
    $studentFolderId = createFolder($driveService, "{$course}_{$studentID}", $sectionFolderId);
    $documentFolderId = createFolder($driveService, $documentType, $studentFolderId);

    // Check if file is uploaded
    if (isset($_FILES["uploadFile"]) && $_FILES["uploadFile"]["error"] == 0) {
        $fileName = basename($_FILES["uploadFile"]["name"]);
        
        // Prepare file metadata for Google Drive upload
        $fileMetadata = new Google_Service_Drive_DriveFile([
            'name' => $fileName,
            'parents' => [$documentFolderId]
        ]);
        
        // Get file content and upload to Google Drive
        $content = file_get_contents($_FILES["uploadFile"]["tmp_name"]);
        $file = $driveService->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => $_FILES["uploadFile"]["type"],
            'uploadType' => 'multipart',
            'fields' => 'id'
        ]);
        
        // Check if file upload was successful
        if ($file) {
            $fileId = $file->id;
            $fileLink = "https://drive.google.com/file/d/" . $fileId;
            
            // Insert document information into the database
            $stmt = $connect->prepare("INSERT INTO documents (student_ID, email, document, file_name, file_link, status, semester, schoolYear) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $status = 'Pending';
            $stmt->bind_param('isssssss', $studentID, $email, $documentType, $fileName, $fileLink, $status, $activeSemester, $activeSchoolYear);
            $stmt->execute();
            $stmt->close();
        } else {
            echo "Error uploading file to Google Drive.";
        }
    } else {
        echo "No file uploaded or there was an upload error.";
    }
}
$connect->close();

header("Location: ../stud_documents.php?success=1");

function createFolder($driveService, $folderName, $parentFolderId) {
    $query = "name='$folderName' and mimeType='application/vnd.google-apps.folder' and trashed=false";
    if ($parentFolderId) {
        $query .= " and '$parentFolderId' in parents";
    }
    $existingFolders = $driveService->files->listFiles(['q' => $query]);
    
    if (count($existingFolders->getFiles()) > 0) {
        return $existingFolders->getFiles()[0]->getId();
    }
    
    $fileMetadata = new Google_Service_Drive_DriveFile([
        'name' => $folderName,
        'mimeType' => 'application/vnd.google-apps.folder',
        'parents' => $parentFolderId ? [$parentFolderId] : []
    ]);
    $folder = $driveService->files->create($fileMetadata, ['fields' => 'id']);
    
    return $folder->id;
}
?>
