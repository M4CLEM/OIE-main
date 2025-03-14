<?php
require_once __DIR__ . '/../vendor/autoload.php';
include_once("../includes/connection.php");

use Google\Client as Google_Client;
use Google\Service\Drive as Google_Service_Drive;
use Google\Service\Drive\DriveFile as Google_Service_Drive_DriveFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

set_time_limit(300);

$client = new Google_Client();
$client->setAuthConfig(__DIR__ . '../../credentials/credentials.json');
$client->addScope(Google_Service_Drive::DRIVE_FILE);
$client->setAccessType('offline');

if (file_exists(__DIR__ . '../../credentials/token.json')) {
    $accessToken = json_decode(file_get_contents(__DIR__ . '../../credentials/token.json'), true);
    $client->setAccessToken($accessToken);

    if ($client->isAccessTokenExpired()) {
        if ($client->getRefreshToken()) {
            $newToken = $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            file_put_contents(__DIR__ . '../../credentials/token.json', json_encode($newToken));
            $client->setAccessToken($newToken);
        } else {
            die("Token expired. Please reauthorize.");
        }
    }
} else {
    die("No token found. Please authenticate first.");
}

$driveService = new Google_Service_Drive($client);

echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
        }
        .alert {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center text-primary">Import Status</h2>
        <div class="text-center">
        <a href="masterlist.php" class="btn btn-primary">Close</a>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <div class="mt-4">';

$messages = [];

if (isset($_POST['submit'])) {
    $SY = $_POST['SY'];
    $course = $_POST['course'];
    $semester = $_POST['Semester'];
    $section = $_POST['section'];
    $dept = $_POST['dept'];

    $check_query = "SELECT * FROM sections_list WHERE course = '$course' AND section = '$section'";
    $check_result = mysqli_query($connect, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $messages[] = "Skipping insertion: Course '$course', Section '$section' already exists.";
    } else {
        $query = "INSERT INTO sections_list (department, course, section) VALUES ('$dept', '$course', '$section')";
        mysqli_query($connect, $query);
    }

    $checkSY_query = "SELECT * FROM school_year WHERE schoolYear = '$SY'";
    $checkSY_result = mysqli_query($connect, $checkSY_query);

    if (mysqli_num_rows($checkSY_result) == 0) {
        $query = "INSERT INTO school_year (schoolYear) VALUES ('$SY')";
        mysqli_query($connect, $query);
    }

    $file_mimes = ['text/csv', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

    if (isset($_FILES['file']['name']) && in_array($_FILES['file']['type'], $file_mimes)) {
        $arr_file = explode('.', $_FILES['file']['name']);
        $extension = end($arr_file);
        $reader = $extension === 'csv' ? new Csv() : new Xlsx();
        $spreadsheet = $reader->load($_FILES['file']['tmp_name']);
        $sheetData = $spreadsheet->getActiveSheet()->toArray();

        if (empty($sheetData[0]) || !array_filter($sheetData[0])) {
            die("No data found in the sheet.");
        }

        $documentsFolderId = createFolder($driveService, "OJT Student Requirements", null);
        $deptFolderId = createFolder($driveService, $dept, $documentsFolderId);
        $courseFolderId = createFolder($driveService, $course, $deptFolderId);
        $SYFolderId = createFolder($driveService, $SY, $courseFolderId);
        $semesterFolderId = createFolder($driveService, $semester, $SYFolderId);
        $sectionFolderId = createFolder($driveService, $section, $semesterFolderId);

        for ($i = 6; $i < count($sheetData); $i++) {
            if (empty($sheetData[$i]) || !array_filter($sheetData[$i])) continue;
            
            $studentid = $sheetData[$i][1];
            $lastName = $sheetData[$i][2];
            $firstName = $sheetData[$i][3];
            $excelCourse = $sheetData[$i][4];
            $year = $sheetData[$i][5];

            $checkQuery = "SELECT * FROM student_masterlist WHERE studentID = '$studentid' AND semester = '$semester'";
            $checkResult = mysqli_query($connect, $checkQuery);

            if (mysqli_num_rows($checkResult) == 0) {
                $sql = "INSERT INTO student_masterlist(studentID, lastName, firstName, course, year, section, semester, schoolYear) 
                        VALUES('$studentid', '$lastName', '$firstName','$excelCourse', '$year', '$section', '$semester' , '$SY')";

                if (mysqli_query($connect, $sql)) {
                    $messages[] = "$lastName $firstName added to database with semester $semester and folder created.";
                } else {
                    $messages[] = "Error inserting $lastName $firstName: " . mysqli_error($connect);
                }
            } else {
                $messages[] = "$lastName $firstName already exists in database for semester $semester, only creating folder.";
            }
            createFolder($driveService, "{$course}_{$studentid}", $sectionFolderId);
        }
    }
}

foreach ($messages as $message) {
    echo "<div class='alert " . (strpos($message, 'Skipping insertion') !== false ? "alert-warning" : "alert-info") . "'>$message</div>";
}

echo "</div>
    </div>
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>
</body>
</html>";

/**
 * Function to create a folder in Google Drive
 *
 * @param Google_Service_Drive $driveService Google Drive API service instance
 * @param string $folderName Name of the folder to be created
 * @param string|null $parentFolderId ID of the parent folder (if any)
 * @return string ID of the created or existing folder
 */
function createFolder($driveService, $folderName, $parentFolderId) {
    // Check if the folder already exists
    $query = "name='$folderName' and mimeType='application/vnd.google-apps.folder' and trashed=false";
    if ($parentFolderId) {
        $query .= " and '$parentFolderId' in parents";
    }
    $existingFolders = $driveService->files->listFiles(['q' => $query]);
    
    if (count($existingFolders->getFiles()) > 0) {
        // Return the existing folder ID if found
        return $existingFolders->getFiles()[0]->getId();
    }
    
    // Create a new folder in Google Drive
    $fileMetadata = new Google_Service_Drive_DriveFile([
        'name' => $folderName,
        'mimeType' => 'application/vnd.google-apps.folder',
        'parents' => $parentFolderId ? [$parentFolderId] : []
    ]);
    $folder = $driveService->files->create($fileMetadata, ['fields' => 'id']);
    
    return $folder->id;
}
?>
