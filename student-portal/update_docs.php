<?php
session_start();
include_once("../includes/connection.php");

// Initialize response
$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from the form
    $rowId = $_POST['rowId']; // ID of the row to update
    $documentType = str_replace('/', '_', $_POST['documentType']);
    $fileUploadPath = null;

    // File upload handling for new file
    if (isset($_FILES["file"]) && $_FILES["file"]["error"] == 0) {
        // Fetch relevant data from session
        $email = $_SESSION['student'];
        $query = "SELECT * FROM studentinfo WHERE email ='$email'";
        $result = mysqli_query($connect, $query);
        $rows = mysqli_fetch_array($result);
        $stud_email = $rows['email'];
        $studentID = $rows['studentID'];
        $course = $rows['course'];
        $department = $rows['department'];
        $SY = $rows['school_year'];
        $semester = $rows['semester'];
        $section = $rows['section'];

        $uploadsDirectory = "coordinator-portal/documents/$department/$course/$SY/$semester/$section/";

        // Create a folder based on $course and $studentID
        $folderPath = $uploadsDirectory . $course . "_" . $studentID . "/" . $documentType . "/";
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        // Delete existing files in the folder that are not the current file
        $files = glob($folderPath . "*"); // Get all files in the folder
        foreach ($files as $file) {
            if ($file !== $folderPath . basename($_FILES["file"]["name"])) {
                unlink($file); // Delete files that are not the current file being uploaded
            }
        }

        // Construct file upload path
        $fileUploadPath = $folderPath . basename($_FILES["file"]["name"]);
        move_uploaded_file($_FILES["file"]["tmp_name"], $fileUploadPath);
    }

    // Update file name in the database for the specified row
    $stmt = $connect->prepare("UPDATE documents SET file_name = ?, status = ?, date = ? WHERE id = ?");
    $status = 'Pending';
    $date = date('Y-m-d');
    $stmt->bind_param('sssi', $fileUploadPath, $status, $date, $rowId);
    if ($stmt->execute()) {
        // Success response
        $response['status'] = 'success';
        $response['message'] = 'File updated successfully';
        $response['newFileName'] = basename($fileUploadPath);
    } else {
        // Error response
        $response['status'] = 'error';
        $response['message'] = 'Failed to update file';
    }
    $stmt->close();
}

$connect->close();


// Send response as JSON
header('Content-Type: application/json');
echo json_encode($response);
