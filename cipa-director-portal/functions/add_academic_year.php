<?php
session_start();
include_once("../../includes/connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize input
    $startDate = trim($_POST['startingDate']);
    $endDate = trim($_POST['endingDate']);
    $semester = trim($_POST['semester']);
    $schoolYear = trim($_POST['schoolYear']);

    // Prepare SQL statement
    $sql = "INSERT INTO academic_year (start_date, end_date, semester, schoolYear) VALUES (?, ?, ?, ?)";
    $stmt = $connect->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssss", $startDate, $endDate, $semester, $schoolYear);
        
        // Execute the statement
        if ($stmt->execute()) {
            $_SESSION['success'] = "Academic year added successfully!";
        } else {
            $_SESSION['error'] = "Error inserting data: " . $stmt->error;
        }
        
        // Close the statement
        $stmt->close();
    } else {
        $_SESSION['error'] = "Database error: " . $connect->error;
    }

    // Close the database connection
    $connect->close();

    // Redirect back to the form page
    header("Location: ../academic-calendar.php"); // Change to the correct page
    exit();
}
?>
