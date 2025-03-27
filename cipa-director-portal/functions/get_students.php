<?php  
include_once("../../includes/connection.php");


if (isset($_GET['department'], $_GET['course'], $_GET['section'])) {
    $department = $_GET['department'];
    $course = $_GET['course'];
    $section = $_GET['section'];

    $stmt = $connect->prepare("SELECT * FROM studentinfo WHERE department = ? AND course = ? AND section = ?");
    $stmt->bind_param("sss", $department, $course, $section);
    $stmt->execute();
    
    
    
    $students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    

    foreach ($students as $student) {

        $stmtGrade = $connect->prepare("SELECT * FROM student_grade WHERE email = ?");
        $stmtGrade->bind_param("s", $student['email']);
        $stmtGrade->execute();
        $resultGrade = $stmtGrade->get_result();
    
        $totalGrade = 0;
    
        while($rowGrade = $resultGrade->fetch_assoc()){
            $totalGrade += intval($rowGrade['grade']);
        }

        echo "<tr>
                <td>
                    <p>{$student['firstname']} {$student['middlename']} {$student['lastname']}</p>
                </td>
                <td>
                    <p>{$student['studentID']}</p>
                </td>
                <td>
                    <p>{$totalGrade}</p>
                </td>
            </tr>";

        
    }
} else {
    echo "Required parameters are missing.";
}

?>
