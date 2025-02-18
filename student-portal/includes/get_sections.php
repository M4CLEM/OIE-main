<?php 
include("db_connect.php"); 

$course = $_GET['course'];

$stmt = $conn->prepare("SELECT * FROM sections_list WHERE course = ?");
$stmt->bind_param("s", $course);
$stmt->execute();
$sections = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

foreach ($sections as $section) {
    echo "<li><a class='dropdown-item' onclick='showStudents(\"{$course}\", \"{$section['section']}\"); updateButtonSection(\"{$section['section']}\")' data-course='{$course}'>{$section['section']}</a></li>";
}

?>