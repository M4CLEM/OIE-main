<?php
session_start();
include_once("../includes/connection.php");

$activeSemester = $_SESSION['semester'];
$activeSchoolYear = $_SESSION['schoolYear'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['status'])) {
    $status = $_POST['status'];
    $studentEmail = $_SESSION['student']; // Assuming you have the student's email in the session

    $stmt = $connect->prepare("UPDATE company_info SET status = ? WHERE student_email = ? AND semester = ? AND schoolYear = ?");
    $stmt->bind_param("ssss", $status, $studentEmail, $activeSemester, $activeSchoolYear);
    $stmt->execute();

    echo "Status updated successfully.";
} else {
    echo "Error: Invalid request.";
}
?>
