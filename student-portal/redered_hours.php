<?php
session_start();
include_once("../includes/connection.php");

if (!isset($_SESSION['stud_code'], $_SESSION['semester'], $_SESSION['schoolYear'])) {
    echo "0 hrs 0 mins";
    exit;
}

$studentID = $_SESSION['stud_code'];
$semester = $_SESSION['semester'];
$schoolYear = $_SESSION['schoolYear'];

$query = "SELECT 
            SUM(TIMESTAMPDIFF(SECOND, time_in, time_out)) AS total_seconds,
            SUM(break_minutes) * 60 AS total_break_seconds
          FROM logdata 
          WHERE student_num = ? 
            AND semester = ? 
            AND schoolYear = ?
            AND time_out IS NOT NULL";

$stmt = $connect->prepare($query);
$stmt->bind_param("sss", $studentID, $semester, $schoolYear);
$stmt->execute();
$result = $stmt->get_result();

$total_seconds = 0;
$total_break_seconds = 0;

if ($row = $result->fetch_assoc()) {
    $total_seconds = $row['total_seconds'] ?? 0;
    $total_break_seconds = $row['total_break_seconds'] ?? 0;
}

$net_seconds = max(0, $total_seconds - $total_break_seconds);

$total_hours = floor($net_seconds / 3600);
$total_minutes = floor(($net_seconds % 3600) / 60);
echo "{$total_hours} hrs {$total_minutes} mins";
?>

