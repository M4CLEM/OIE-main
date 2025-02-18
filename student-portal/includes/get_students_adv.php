<?php 
include("db_connect.php"); 

$course = $_GET['course'];
$section = $_GET['section'];

$stmt = $conn->prepare("SELECT * FROM studentinfo WHERE course = ? AND section = ?");

$stmt->bind_param("ss", $course, $section);
$stmt->execute();
$students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

foreach ($students as $student) {
    echo "<tr>
            <td>
                <a href='view_logs_adviser.php?number={$student['studentID']}'>{$student['firstname']} {$student['middlename']} {$student['lastname']}</a>
            </td>
            <td>
                <p>{$student['studentID']}</p>
            </td>
        </tr>";
}

?>