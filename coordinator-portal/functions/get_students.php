<?php  
include_once("../../includes/connection.php");
$connect = new mysqli('localhost', 'root', '', 'plmunoiedb');


if (isset($_GET['department'], $_GET['course'], $_GET['section'])) {
    $department = $_GET['department'];
    $course = $_GET['course'];
    $section = $_GET['section'];

    $stmt = $connect->prepare("SELECT * FROM student_masterlist WHERE course = ? AND section = ?");
    $stmt->bind_param("ss", $course, $section);
    $stmt->execute();
    //$isDeployed
    
    $students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    foreach ($students as $student) {

        $stmtGrade = $connect->prepare("SELECT * FROM student_grade WHERE studentID = ?");
        $stmtGrade->bind_param("s", $student['studentID']);
        $stmtGrade->execute();
        $resultGrade = $stmtGrade->get_result();
    
        $totalGrade = 0;
    
        while($rowGrade = $resultGrade->fetch_assoc()){
            $totalGrade += intval($rowGrade['grade']);
        }


        echo "<tr>
                <td>{$student['studentID']}</td>
                <td>{$student['firstName']} {$student['lastName']}</td>
                <td>{$student['course']}</td>
                <td>{$student['section']}</td>
                <td> <p>{$totalGrade}</p></td>

                <td> 
                    <a title='Edit' href='student-edit.php?id={$student['studentID']}' class='btn btn-xs'><span class='fa fa-edit fw-fa'></span>
                    <a title='Delete' href='functions/student-delete-process.php?id={$student['studentID']}' class='btn btn-xs'><span class='fa fa-trash'></a></span>
                </td>

            </tr>";
    }
} else {
    echo "Required parameters are missing.";
}

?>
