<?php
session_start();
require_once __DIR__ . '/../../vendor/autoload.php';
include_once("../../includes/connection.php");

use Google\Client as Google_Client;
use Google\Service\Drive as Google_Service_Drive;
use Google\Service\Drive\DriveFile as Google_Service_Drive_DriveFile;

$client = new Google_Client();
$client->setAuthConfig(__DIR__ . '../credentials.json');
$client->addScope(Google_Service_Drive::DRIVE_FILE);
$client->setAccessType('offline');

if (file_exists(__DIR__ . '../token.json')) {
    $accessToken = json_decode(file_get_contents(__DIR__ . '/token.json'), true);
    $client->setAccessToken($accessToken);
    
    if ($client->isAccessTokenExpired()) {
        if ($client->getRefreshToken()) {
            $newToken = $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            file_put_contents(__DIR__ . '../token.json', json_encode($newToken));
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
    $department = $rows['department'];
    $SY = $rows['school_year'];
    $semester = $rows['semester'];
    $section = $rows['section'];
    $documentType = $_POST['documentType'];

    $documentsFolderId = createFolder($driveService, "OJT Student Requirements", null);
    $deptFolderId = createFolder($driveService, $department, $documentsFolderId);
    $courseFolderId = createFolder($driveService, $course, $deptFolderId);
    $SYFolderId = createFolder($driveService, $SY, $courseFolderId);
    $semesterFolderId = createFolder($driveService, $semester, $SYFolderId);
    $sectionFolderId = createFolder($driveService, $section, $semesterFolderId);
    $studentFolderId = createFolder($driveService, "{$course}_{$studentID}", $sectionFolderId);
    $documentFolderId = createFolder($driveService, $documentType, $studentFolderId);

    if (isset($_FILES["newFile"]) && $_FILES["newFile"]["error"] == 0) {
        $fileName = basename($_FILES["newFile"]["name"]);
        
        $fileMetadata = new Google_Service_Drive_DriveFile([
            'name' => $fileName,
            'parents' => [$documentFolderId]
        ]);
        
        $content = file_get_contents($_FILES["newFile"]["tmp_name"]);
        $file = $driveService->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => $_FILES["newFile"]["type"],
            'uploadType' => 'multipart',
            'fields' => 'id'
        ]);
        
        $fileId = $file->id;
        $fileLink = "https://drive.google.com/file/d/" . $fileId;
        
        $stmt = $connect->prepare("INSERT INTO documents (student_ID, email, document, file_name, file_link, status) VALUES (?, ?, ?, ?, ?, ?)");
        $status = 'Pending';
        $stmt->bind_param('isssss', $studentID, $email, $documentType, $fileName, $fileLink, $status);
        $stmt->execute();
        $stmt->close();
    }
}
$connect->close();

header("Location: ../student-portal/student_docs.php");

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
