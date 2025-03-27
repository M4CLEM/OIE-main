<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';
include_once("../includes/connection.php");

if ($connect->connect_error) {
    die('Connection failed: ' . $connect->connect_error);
}

use Google\Client as Google_Client;
use Google\Service\Drive as Google_Service_Drive;
use Google\Service\Drive\DriveFile as Google_Service_Drive_DriveFile;

$client = new Google_Client();
$client->setAuthConfig(__DIR__ . '/../credentials/credentials.json');
$client->addScope(Google_Service_Drive::DRIVE_FILE);
$client->setAccessType('offline');

if (file_exists(__DIR__ . '/../credentials/token.json')) {
    $accessToken = json_decode(file_get_contents(__DIR__ . '/../credentials/token.json'), true);
    $client->setAccessToken($accessToken);
    
    if ($client->isAccessTokenExpired()) {
        if ($client->getRefreshToken()) {
            $newToken = $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            file_put_contents(__DIR__ . '/../credentials/token.json', json_encode($newToken));
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
    $studentID = trim($_POST['studentID']);
    $firstname = trim($_POST['firstname']);
    $middlename = trim($_POST['middlename']);
    $lastname = trim($_POST['lastname']);
    $address = trim($_POST['address']);
    $age = trim($_POST['age']);
    $gender = trim($_POST['gender']);
    $contactNo = trim($_POST['contactNo']);
    $course = trim($_POST['course']);
    $department = trim($_POST['department']);
    $email = trim($_POST['email']);
    $SY = trim($_POST['SY']);
    $section = trim($_POST['section']);
    $semester = trim($_POST['semester']);
    $status = "Undeployed";
    $objective = trim($_POST['objective']);

    $skills = isset($_POST['skills']) ? implode(',', $_POST['skills']) : '';
    $seminars = isset($_POST['seminars']) ? implode(',', $_POST['seminars']) : '';

    $documentsFolderId = createFolder($driveService, "OJT Student Requirements", null);
    $deptFolderId = createFolder($driveService, $department, $documentsFolderId);
    $courseFolderId = createFolder($driveService, $course, $deptFolderId);
    $SYFolderId = createFolder($driveService, $SY, $courseFolderId);
    $semesterFolderId = createFolder($driveService, $semester, $SYFolderId);
    $sectionFolderId = createFolder($driveService, $section, $semesterFolderId);
    $studentFolderId = createFolder($driveService, "{$course}_{$studentID}", $sectionFolderId);

    $resumeLink = null;
    $photoLink = null;

    // Upload Resume
    if (isset($_FILES["resume"]) && $_FILES["resume"]["error"] == 0) {
        $resumeFolderId = createFolder($driveService, "Resume", $studentFolderId);
        $resumeLink = uploadFileToDrive($driveService, $_FILES["resume"], $resumeFolderId);
    }
    
    // Upload Photo
    if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] == 0) {
        $photoFolderId = createFolder($driveService, "Profile", $studentFolderId);
        $photoLink = uploadFileToDrive($driveService, $_FILES["photo"], $photoFolderId);
    }

    // Insert student information
    $stmt = $connect->prepare("INSERT INTO studentinfo (studentID, firstname, middlename, lastname, address, age, gender, contactNo, course, department, email, status, image, section, school_year, semester, objective, skills, seminars) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('issssisisssssssssss', $studentID, $firstname, $middlename, $lastname, $address, $age, $gender, $contactNo, $course, $department, $email, $status, $photoLink, $section, $SY, $semester, $objective, $skills, $seminars);
    $stmt->execute();
    $stmt->close();

    // Insert resume document information
    if ($resumeLink) {
        $stmtResume = $connect->prepare("INSERT INTO documents (student_ID, email, document, file_name, file_link, status) VALUES (?, ?, ?, ?, ?, ?)");
        $documentType = 'Resume';
        $documentStatus = 'Pending';
        $stmtResume->bind_param('isssss', $studentID, $email, $documentType, $_FILES["resume"]["name"], $resumeLink, $documentStatus);
        $stmtResume->execute();
        $stmtResume->close();
    }

    $connect->close();
    header("Location: fill-out-form.php?success=1");
}

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

function uploadFileToDrive($driveService, $file, $parentFolderId) {
    if ($file && $file["error"] == UPLOAD_ERR_OK) {
        $fileName = basename($file["name"]);
        $fileMetadata = new Google_Service_Drive_DriveFile([
            'name' => $fileName,
            'parents' => [$parentFolderId]
        ]);
        $content = file_get_contents($file["tmp_name"]);
        $uploadedFile = $driveService->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => $file["type"],
            'uploadType' => 'multipart',
            'fields' => 'id'
        ]);
        return "https://drive.google.com/uc?id=" . $uploadedFile->id;
    }
    return null;
}
?>
