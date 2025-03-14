<?php 
require_once __DIR__ . '/../../vendor/autoload.php';
include_once("../../includes/connection.php");

use Google\Client as Google_Client;
use Google\Service\Drive as Google_Service_Drive;
use Google\Service\Drive\DriveFile as Google_Service_Drive_DriveFile;

set_time_limit(300);

// Initialize Google Client
$client = new Google_Client();
$client->setAuthConfig(__DIR__ . '/../../credentials/credentials.json'); 
$client->addScope(Google_Service_Drive::DRIVE_FILE);
$client->setAccessType('offline');

// Load access token
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

if(isset($_POST['save'])) {

    if (!isset($connect)) {
        die("Database connection error.");
    }

    // Retrieve and sanitize inputs
    $studentID = trim($_POST['studentID']); 
    $lastName = trim($_POST['lastName']); 
    $firstName = trim($_POST['firstName']);
    $course = trim($_POST['course']);
    $dept = trim($_POST['dept']);
    $section = trim($_POST['section']);
    $year = trim($_POST['year']);
    $semester = trim($_POST['semester']);
    $SY = trim($_POST['SY']);

    // Ensure "documents" folder exists
    $documentsFolderId = createFolder($driveService, "OJT Student Requirements", null);
    $deptFolderId = createFolder($driveService, $dept, $documentsFolderId);
    $courseFolderId = createFolder($driveService, $course, $deptFolderId);
    $SYFolderId = createFolder($driveService, $SY, $courseFolderId);
    $semesterFolderId = createFolder($driveService, $semester, $SYFolderId);
    $sectionFolderId = createFolder($driveService, $section, $semesterFolderId);

    // Check if student already exists for the same semester
    $checkQuery = "SELECT * FROM student_masterlist WHERE studentID = ? AND semester = ?";
    $stmt = $connect->prepare($checkQuery);
    $stmt->bind_param("ss", $studentID, $semester);
    $stmt->execute();
    $checkResult = $stmt->get_result();
    $stmt->close();

    if ($checkResult->num_rows == 0) {
        // Insert student into database
        $query = "INSERT INTO student_masterlist (studentID, lastName, firstName, course, section, year, semester, schoolYear) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $connect->prepare($query);
        $stmt->bind_param("ssssssss", $studentID, $lastName, $firstName, $course, $section, $year, $semester, $SY);
        
        if ($stmt->execute()) {
            echo "<div style='padding: 10px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; 
                        border-radius: 5px; text-align: center; width: 50%; margin: 20px auto;'>
                    <strong>Success!</strong> " . htmlspecialchars($lastName) . " " . htmlspecialchars($firstName) . " added for " . htmlspecialchars($semester) . ".
                    <br><br>
                    <a href='../masterlist.php' 
                        style='display: inline-block; padding: 5px 10px; background-color: #155724; color: #fff; text-decoration: none; border-radius: 5px;'>
                        Close
                    </a>
                  </div>";
        } else {
            echo "Error inserting student: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "<div style='padding: 10px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; 
                    border-radius: 5px; text-align: center; width: 50%; margin: 20px auto; position: relative;'>
                <strong>Error!</strong> " . htmlspecialchars($lastName) . " " . htmlspecialchars($firstName) . " already exists for " . htmlspecialchars($semester) . ".
                <br><br>
                <a href='../masterlist.php' 
                   style='display: inline-block; padding: 5px 10px; background-color: #721c24; color: #fff; text-decoration: none; border-radius: 5px;'>
                    Close
                </a>
              </div>";
    }

    // Create Google Drive folder for student
    $studentFolderName = "{$course}_{$studentID}";
    createFolder($driveService, $studentFolderName, $sectionFolderId);

    // Close DB connection
    $connect->close();
}

function createFolder($driveService, $folderName, $parentFolderId) {
    $query = "name='$folderName' and mimeType='application/vnd.google-apps.folder' and trashed=false";
    if ($parentFolderId) $query .= " and '$parentFolderId' in parents";

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
