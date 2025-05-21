<?php

session_start();
include("../includes/connection.php");

header('Content-Type: application/json');

$department = $_SESSION['department'] ?? null;
$semester = $_SESSION['semester'] ?? null;
$schoolYear = $_SESSION['schoolYear'] ?? null;
$course = $_GET['course'] ?? '';

if (!$department || !$semester || !$schoolYear) {
    echo json_encode([]); // fail silently but safely
    exit;
}

$query = "SELECT DISTINCT section FROM studentinfo WHERE department = ? AND semester = ? AND school_year = ?";
$params = [$department, $semester, $schoolYear];
$types = "sss";

if ($course !== 'All') {
    $query .= " AND course = ?";
    $params[] = $course;
    $types .= "s";
}

$query .= " ORDER BY section ASC";

$stmt = $connect->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$sections = [];
while ($row = $result->fetch_assoc()) {
    $sections[] = $row['section'];
}

echo json_encode($sections);
?>
