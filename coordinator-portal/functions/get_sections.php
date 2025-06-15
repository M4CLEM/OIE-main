<?php 
include_once("../../includes/connection.php");
// No need to create new mysqli instance if you already have $connect from included file.
// But if $connect is not available, keep this:
// $connect = new mysqli('localhost', 'root', '', 'plmunoiedb');

$department = $_GET['department'] ?? '';
$course = $_GET['course'] ?? '';

$stmt = $connect->prepare("SELECT * FROM sections_list WHERE department = ? AND course = ?");
$stmt->bind_param("ss", $department, $course);
$stmt->execute();
$sections = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

foreach ($sections as $section) {
    $deptEscaped = htmlspecialchars($department, ENT_QUOTES);
    $courseEscaped = htmlspecialchars($course, ENT_QUOTES);
    $sectionEscaped = htmlspecialchars($section['section'], ENT_QUOTES);

    echo "<li>
            <a class='dropdown-item' 
               onclick='showStudents(\"{$deptEscaped}\", \"{$courseEscaped}\", \"{$sectionEscaped}\"); 
                        updateButtonSection(\"{$sectionEscaped}\")' 
               data-department='{$deptEscaped}' 
               data-course='{$courseEscaped}'>
               {$sectionEscaped}
            </a>
          </li>";
}
?>
