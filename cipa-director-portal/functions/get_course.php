<?php
include_once("../../includes/connection.php");

if (!isset($_GET['department'])) {
    echo json_encode([]);
    exit;
}

$department = $_GET['department'];

$stmt = $connect->prepare("SELECT course FROM course_list WHERE department = ?");
$stmt->bind_param("s", $department);
$stmt->execute();
$result = $stmt->get_result();

$courses = [];
while ($row = $result->fetch_assoc()) {
    $courses[] = $row['course'];
}

echo json_encode($courses);
?>
