<?php
session_start();
include_once("../includes/connection.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['status'])) {
    $status = $_POST['status'];
    $studentEmail = $_SESSION['student']; // Assuming you have the student's email in the session

    $stmt = $connect->prepare("UPDATE company_info SET status = ? WHERE student_email = ?");
    $stmt->bind_param("ss", $status, $studentEmail);
    $stmt->execute();

    echo "Status updated successfully.";
} else {
    echo "Error: Invalid request.";
}
?>
