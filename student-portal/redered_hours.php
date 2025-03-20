<?php
session_start();
include_once("../includes/connection.php");

if (!isset($_SESSION['stud_code'])) {
    echo "0 hrs 0 mins";
    exit;
}

$studentID = $_SESSION['stud_code']; // Get the logged-in student ID

// Calculate total rendered time for this student
$query = "SELECT SUM(TIMESTAMPDIFF(SECOND, time_in, time_out)) AS total_seconds FROM logdata WHERE student_num = '$studentID'";
$result = mysqli_query($connect, $query);

$total_seconds = 0;
if ($row = mysqli_fetch_assoc($result)) {
    $total_seconds = $row['total_seconds'] ?? 0;
}

// Convert seconds to hours and minutes
$total_hours = floor($total_seconds / 3600);
$total_minutes = floor(($total_seconds % 3600) / 60);

// Return the result dynamically
echo "{$total_hours} hrs {$total_minutes} mins";
?>
