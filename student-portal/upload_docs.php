<?php
session_start();
include_once("../includes/connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

    $documentType = $_POST['documentType'];

    // Check if a record with the same document type, student ID, and email already exists
    $checkQuery = "SELECT * FROM documents WHERE student_ID = '$studentID' AND email = '$stud_email' AND document = '$documentType'";
    $checkResult = mysqli_query($connect, $checkQuery);
    if (mysqli_num_rows($checkResult) > 0) {
        // If record exists, delete the existing record
        $deleteQuery = "DELETE FROM documents WHERE student_ID = '$studentID' AND email = '$stud_email' AND document = '$documentType'";
        mysqli_query($connect, $deleteQuery);
    }

    // File upload handling for new file
    $fileUploadPath = null;
    if (isset($_FILES["newFile"]) && $_FILES["newFile"]["error"] == 0) {
        $uploadsDirectory = "../coordinator-portal/documents/$department/$course/$SY/$semester/$section/";

        // Create a folder based on $course and $studentID
        $folderPath = $uploadsDirectory . $course . "_" . $studentID . "/" . $documentType . "/";
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        // Delete existing files in the folder that are not the current file
        $files = glob($folderPath . "*"); // Get all files in the folder
        foreach ($files as $file) {
            if ($file !== $folderPath . basename($_FILES["newFile"]["name"])) {
                unlink($file); // Delete files that are not the current file being uploaded
            }
        }

        $fileUploadPath = $folderPath . basename($_FILES["newFile"]["name"]);
        move_uploaded_file($_FILES["newFile"]["tmp_name"], $fileUploadPath);
    }

    // Insert new record into the database
    $stmt = $connect->prepare("INSERT INTO documents (student_ID, email, document, file_name, status) VALUES (?, ?, ?, ?, ?)");
    $status = 'Pending';
    $stmt->bind_param('issss', $studentID, $stud_email, $documentType, $fileUploadPath, $status);
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

header("Location: ../student-portal/student_docs.php");
?>