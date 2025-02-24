<?php 
include_once("../../includes/connection.php");

$department = $_GET['department'];
$course = $_GET['course'];

$stmt = $connect->prepare("SELECT * FROM sections_list WHERE department = ? AND course = ? ");
$stmt->bind_param("ss", $department, $course);
$stmt->execute();
$sections = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

echo "<option value='' selected disabled>Select Section</option>";

foreach ($sections as $section) {
    echo "<option value='{$section['section']}'>{$section['section']}</option>";
}
?>
