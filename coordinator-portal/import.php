<?php
require_once __DIR__ . '/../vendor/autoload.php';
include_once("../includes/connection.php");

use Google\Client as Google_Client;
use Google\Service\Drive as Google_Service_Drive;
use Google\Service\Drive\DriveFile as Google_Service_Drive_DriveFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

set_time_limit(300); // Increase script execution time

// Initialize Google Client
$client = new Google_Client();
$client->setAuthConfig(__DIR__ . '/credentials.json'); // Set path to credentials
$client->addScope(Google_Service_Drive::DRIVE_FILE);
$client->setAccessType('offline');

// Load access token
if (file_exists(__DIR__ . '/token.json')) {
    $accessToken = json_decode(file_get_contents(__DIR__ . '/token.json'), true);
    $client->setAccessToken($accessToken);

    // Refresh token if expired
    if ($client->isAccessTokenExpired()) {
        if ($client->getRefreshToken()) {
            $newToken = $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            file_put_contents(__DIR__ . '/token.json', json_encode($newToken)); // Save new token
            $client->setAccessToken($newToken);
        } else {
            die("Token expired. Please reauthorize.");
        }
    }
} else {
    die("No token found. Please authenticate first.");
}

$driveService = new Google_Service_Drive($client);

// Process form submission
if (isset($_POST['submit'])) {
    $SY = $_POST['SY']; // School year
    $course = $_POST['course']; // Course name
    $semester = $_POST['Semester']; // Semester
    $section = $_POST['section']; // Section name
    $dept = $_POST['dept']; // Department name

    // Check if course, section, and school year already exist
    $check_query = "SELECT * FROM sections_list WHERE course = '$course' AND section = '$section'";
    $check_result = mysqli_query($connect, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        echo"Skipping insertion: Course '$course', Section '$section' already exists.";
    } else {
        // Insert section details into the database
        $query = "INSERT INTO sections_list (department, course, section,) VALUES ('$dept', '$course', '$section')";
        $result = mysqli_query($connect, $query);
    }

    // SCHOOL YEAR QUERY
    $checkSY_query = "SELECT * FROM school_year WHERE schoolYear = '$SY'";
    $checkSY_result = mysqli_query($connect, $checkSY_query);

    if (mysqli_num_rows($checkSY_result) > 0) {
        //----------------------B  L A N K ---------------------//
    } else {
        $query = "INSERT INTO school_year (schoolYear) VALUES ('$SY')";
        $result = mysqli_query($connect, $query);
    }
    

    $file_mimes = ['text/csv', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

    // Check if file is uploaded and is a valid format (CSV or XLSX)
    if (isset($_FILES['file']['name']) && in_array($_FILES['file']['type'], $file_mimes)) {
        $arr_file = explode('.', $_FILES['file']['name']);
        $extension = end($arr_file);

        // Determine file type and load spreadsheet
        $reader = $extension === 'csv' ? new Csv() : new Xlsx();
        $spreadsheet = $reader->load($_FILES['file']['tmp_name']);
        $sheetData = $spreadsheet->getActiveSheet()->toArray();

        // Validate that the sheet has data
        if (empty($sheetData[0]) || !array_filter($sheetData[0])) {
            die("No data found in the sheet.");
        }

        // Ensure "documents" folder exists in Google Drive
        $documentsFolderId = createFolder($driveService, "documents", null);
        
        // Create nested folders in Google Drive for department, course, school year, semester, and section
        $deptFolderId = createFolder($driveService, $dept, $documentsFolderId);
        $courseFolderId = createFolder($driveService, $course, $deptFolderId);
        $SYFolderId = createFolder($driveService, $SY, $courseFolderId);
        $semesterFolderId = createFolder($driveService, $semester, $SYFolderId);
        $sectionFolderId = createFolder($driveService, $section, $semesterFolderId);

        // Process student data from the uploaded file (starting from row 6)
        for ($i = 6; $i < count($sheetData); $i++) {
            if (empty($sheetData[$i]) || !array_filter($sheetData[$i])) continue;
            
            $studentid = $sheetData[$i][1]; // Student ID
            $lastName = $sheetData[$i][2]; // Last name
            $firstName = $sheetData[$i][3]; // First name
            $excelCourse = $sheetData[$i][4]; // Course
            $year = $sheetData[$i][5]; // Year level

            // Check if student with the same semester already exists in the database
            $checkQuery = "SELECT * FROM student_masterlist WHERE studentID = '$studentid' AND semester = '$semester'";
            $checkResult = mysqli_query($connect, $checkQuery);

            if (mysqli_num_rows($checkResult) == 0) {
                // Insert new student record with semester
                $sql = "INSERT INTO student_masterlist(studentID, lastName, firstName, course, year, section, semester, schoolYear) 
                        VALUES('$studentid', '$lastName', '$firstName','$excelCourse', '$year', '$section', '$semester' , '$SY')";

                if (mysqli_query($connect, $sql)) {
                    echo "$lastName $firstName added to database with semester $semester and folder created.<br>";
                } else {
                    echo "Error inserting $lastName $firstName: " . mysqli_error($connect) . "<br>";
                }
            } else {
                echo "$lastName $firstName already exists in database for semester $semester, only creating folder.<br>";
            }

            // Create a Google Drive folder for the student
            $studentFolderName = "{$course}_{$studentid}";
            createFolder($driveService, $studentFolderName, $sectionFolderId);
        }
    }
}

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
