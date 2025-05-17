<?php
session_start();
include("../../includes/connection.php");

// Get the student's email from the session
$email = $_SESSION['student'];
$semester = $_SESSION['semester'];
$schoolYear = $_SESSION['schoolYear'];

$companyNumber = $_POST['No'] ?? null;
$companyName = $_POST['companyName'] ?? null;
$jobrole = $_POST['jobrole'] ?? null;
$status = "Pending";

// Get student info from database
$studInfoQuery = "SELECT * FROM studentinfo WHERE email = ?";
$studInfoStmt = $connect->prepare($studInfoQuery);
$studInfoStmt->bind_param("s", $email);
$studInfoStmt->execute();

$studInfoResult = $studInfoStmt->get_result();

if ($studInfoResult && $studInfoResult->num_rows > 0) {
    $row = $studInfoResult->fetch_assoc();
    $studentID = $row['studentID'];
    $section = $row['section'];
    $course = $row['course'];
    $department = $row['department'];

    // Prepare and execute application insert
    $applicationQuery = "INSERT INTO applications 
        (studentID, email, companyCode, companyName, jobrole, section, course, department, status, applicationDate, semester, SchoolYear) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)";

    $applicationStmt = $connect->prepare($applicationQuery);

    if ($applicationStmt) {
        $applicationStmt->bind_param(
            "isissssssss",
            $studentID,
            $email,
            $companyNumber,
            $companyName,
            $jobrole,
            $section,
            $course,
            $department,
            $status,
            $semester,
            $schoolYear
        );

        if ($applicationStmt->execute()) {
            echo "Application submitted successfully.";
        } else {
            echo "Error inserting application: " . $applicationStmt->error;
        }

        $applicationStmt->close();
    } else {
        echo "Failed to prepare insert statement.";
    }
} else {
    echo "No student information found for the logged-in email.";
}

$studInfoStmt->close();
?>
