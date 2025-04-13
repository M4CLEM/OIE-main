<?php 
include_once("../../includes/connection.php");

$department = $_GET['department'];
$course = $_GET['course'];

$stmt = $connect->prepare("SELECT * FROM sections_list WHERE department = ? AND course = ? ");
$stmt->bind_param("ss", $department, $course);
$stmt->execute();
$sections = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Build checkbox list for dropdown menu
foreach ($sections as $section) {
    $secName = htmlspecialchars($section['section']);
    echo "
        <li>
            <label class='dropdown-item'>
                <input type='checkbox' class='section-check' value='{$secName}'> {$secName}
            </label>
        </li>
    ";
}
?>

