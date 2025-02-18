<?php 
include_once("../../includes/connection.php");

$department = $_GET['department'];

$stmt = $connect->prepare("SELECT * FROM course_list WHERE department = ?");
$stmt->bind_param("s", $department);
$stmt->execute();
$courses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

echo "<option value='' selected disabled>Select Course</option>";

foreach ($courses as $course) {
    echo "<option value='{$course['course']}'>{$course['course']}</option>";
}
?>
