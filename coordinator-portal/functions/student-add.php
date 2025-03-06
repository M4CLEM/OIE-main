<?php 
require_once __DIR__ . '/../../vendor/autoload.php';
include_once("../../includes/connection.php");

use Google\Client as Google_Client;
use Google\Service\Drive as Google_Service_Drive;
use Google\Service\Drive\DriveFile as Google_Service_Drive_DriveFile;

set_time_limit(300); // Increase script execution time

// Initialize Google Client
$client = new Google_Client();
$client->setAuthConfig(__DIR__ . '/../credentials.json'); 
$client->addScope(Google_Service_Drive::DRIVE_FILE);
$client->setAccessType('offline');

// Load access token
if (file_exists(__DIR__ . '/../token.json')) {
    $accessToken = json_decode(file_get_contents(__DIR__ . '/../token.json'), true);
    $client->setAccessToken($accessToken);

    if ($client->isAccessTokenExpired()) {
        if ($client->getRefreshToken()) {
            $newToken = $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            file_put_contents(__DIR__ . '/../token.json', json_encode($newToken));
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
    $documentsFolderId = createFolder($driveService, "documents", null);
    $deptFolderId = createFolder($driveService, $dept, $documentsFolderId);
    $courseFolderId = createFolder($driveService, $course, $deptFolderId);
    $SYFolderId = createFolder($driveService, $SY, $courseFolderId);
    $semesterFolderId = createFolder($driveService, $semester, $SYFolderId);
    $sectionFolderId = createFolder($driveService, $section, $semesterFolderId);

    // Check if student exists
    $checkQuery = "SELECT * FROM student_masterlist WHERE studentID = ?";
    $stmt = $connect->prepare($checkQuery);
    $stmt->bind_param("s", $studentID);
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
            echo "$lastName $firstName added to database.";
        } else {
            echo "Error inserting $lastName $firstName: " . $stmt->error;
        }
        $stmt->close();

        // Make sure the message is flushed to the browser immediately
        ob_flush();
        flush();

        // Add JavaScript to redirect to masterlist.php after 3 seconds
        echo "<script type='text/javascript'>
                setTimeout(function() {
                    window.location.href = '../masterlist.php';
                }, 5000); // 5-second delay
            </script>";
        exit(); // Make sure to stop the rest of the script from executing after this point
    } else {
        echo "$lastName $firstName already exists in database.";
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

    $fileMetadata = new Google_Service_Drive_DriveFile(['name' => $folderName, 'mimeType' => 'application/vnd.google-apps.folder', 'parents' => $parentFolderId ? [$parentFolderId] : []]);
    $folder = $driveService->files->create($fileMetadata, ['fields' => 'id']);
    return $folder->id;
}
?>
