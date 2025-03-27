<?php 
include_once("../../includes/connection.php");

$department = $_GET['department'];

$stmt = $connect->prepare("SELECT * FROM course_list WHERE department = ?");
$stmt->bind_param("s", $department);
$stmt->execute();
$courses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

foreach ($courses as $course) {
    echo "<li>
            <a class='dropdown-item' onclick='showSections(\"{$department}\", \"{$course['course']}\");  
            updateButtonCourse(\"{$course['course']}\")' data-course='{$course['course']}'>{$course['course']}
            </a>
        </li>";
}

?>