<?php 
include_once("../../includes/connection.php");
$connect = new mysqli('localhost', 'root', '', 'plmunoiedb');

$department = $_GET['department'];
$course = $_GET['course'];

$stmt = $connect->prepare("SELECT * FROM sections_list WHERE department = ? AND course = ? ");

$stmt->bind_param("ss", $department, $course);
$stmt->execute();
$sections = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

foreach ($sections as $section) {
    echo "<li>
            <a class='dropdown-item' onclick='showStudents(\"{$department}\", \"{$course}\", \"{$section['section']}\"); 
            updateButtonSection(\"{$section['section']}\")' data-deptartment='{$department} data-course='{$course} '>{$section['section']}
            </a>
        </li>";
}

?>