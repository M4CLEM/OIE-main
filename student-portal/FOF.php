<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $studentID = $_POST['studentID'];
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $lastname = $_POST['lastname'];
    $address = $_POST['address'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $contactNo = $_POST['contactNo'];
    $course = $_POST['course'];
    $department = $_POST['department'];
    $email = $_POST['email'];
    $SY = $_POST['SY'];
    $section = $_POST['section'];
    $semester = $_POST['semester'];

    $status = "Undeployed";
    $objective = $_POST['objective'];

    $skillsArray = $_POST['skills'];
    $seminarsArray = $_POST['seminars'];

    $skills = implode(',', $skillsArray);
    $seminars = implode(',', $seminarsArray);

    // File upload handling for photo
    $photoUploadPath = null;
    if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] == UPLOAD_ERR_OK) {
        $uploadsDirectory = "../coordinator-portal/documents/$department/$course/$SY/$semester/$section/";

        // Create a folder based on $course and $studentID
        $folderPath = $uploadsDirectory . $course . "_" . $studentID . "/";
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        $photoUploadPath = $folderPath . basename($_FILES["photo"]["name"]);
        move_uploaded_file($_FILES["photo"]["tmp_name"], $photoUploadPath);
    }

    // File upload handling for resume
    $resumeUploadPath = null;
    if (isset($_FILES["resume"]) && $_FILES["resume"]["error"] == UPLOAD_ERR_OK) {
        $uploadsDirectory = "../coordinator-portal/documents/$department/$course/$SY/$semester/$section/";

        // Create a folder based on $course and $studentID
        $folderPath = $uploadsDirectory . $course . "_" . $studentID . "/Resume/";
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        $resumeUploadPath = $folderPath . basename($_FILES["resume"]["name"]);
        move_uploaded_file($_FILES["resume"]["tmp_name"], $resumeUploadPath);
    }

    echo "Debug Photo Path: " . $photoUploadPath . "<br>";
    echo "Debug Resume Path: " . $resumeUploadPath . "<br>";

    // Database insertion
    $connect = new mysqli('localhost', 'root', '', 'plmunoiedb');
    if ($connect->connect_error) {
        die('Connection failed: ' . $connect->connect_error);
    } else {
        $stmt = $connect->prepare("INSERT INTO studentinfo (studentID, firstname, middlename, lastname, address, age, gender, contactNo, course, department, email, status, image, section, school_year, semester, objective, skills, seminars) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('issssisisssssssssss', $studentID, $firstname, $middlename, $lastname, $address, $age, $gender, $contactNo, $course, $department, $email, $status, $photoUploadPath, $section, $SY, $semester, $objective, $skills, $seminars);
        $stmt->execute();
        $stmt->close();

        // Insert resume path into student_documents table
        $stmtResume = $connect->prepare("INSERT INTO documents (student_ID, email, document, file_name, status) VALUES (?, ?, ?, ?, ?)");
        $documentType = 'Resume';
        $documentStatus = 'Pending';
        $stmtResume->bind_param('issss', $studentID, $email, $documentType, $resumeUploadPath, $documentStatus);
        $stmtResume->execute();
        $stmtResume->close();

        $connect->close();
    }


    header("Location: ../index.php");
}
