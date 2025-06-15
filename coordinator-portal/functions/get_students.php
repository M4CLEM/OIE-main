<?php
include_once("../../includes/connection.php");

if (isset($_GET['department'], $_GET['section'])) {
    $department = $_GET['department'];
    $section = $_GET['section'];

    // Fetch all courses under this department
    $stmtCourses = $connect->prepare("SELECT course FROM course_list WHERE department = ?");
    $stmtCourses->bind_param("s", $department);
    $stmtCourses->execute();
    $resultCourses = $stmtCourses->get_result();

    $courses = [];
    while ($row = $resultCourses->fetch_assoc()) {
        $courses[] = $row['course'];
    }
    $stmtCourses->close();

    if (empty($courses)) {
        echo "No courses found for this department.";
        exit;
    }

    // Prepare placeholders for the IN clause
    $placeholders = implode(',', array_fill(0, count($courses), '?'));
    
    // Query students with course in these courses and matching section
    $sql = "
        SELECT sm.studentID, sm.firstName, sm.lastName, sm.schoolYear, sm.semester,
               COALESCE(si.email, 'N/A') AS email, 
               COALESCE(si.status, 'N/A') AS status,
               COALESCE(SUM(sg.grade), 0) AS totalGrade
        FROM student_masterlist sm
        LEFT JOIN studentinfo si ON sm.studentID = si.studentID
        LEFT JOIN student_grade sg ON sm.studentID = sg.studentID
        WHERE sm.course IN ($placeholders) AND sm.section = ?
        GROUP BY sm.studentID, sm.firstName, sm.lastName, si.email, si.status, sm.schoolYear, sm.semester
    ";

    $stmt = $connect->prepare($sql);

    // Bind all course values + section
    $types = str_repeat('s', count($courses)) . 's'; // e.g. 'sss' + 's' = 'ssss' if 3 courses
    $params = array_merge($courses, [$section]);

    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    foreach ($students as $student) {
        echo "<tr>
            <td>{$student['studentID']}</td>
            <td>{$student['firstName']} {$student['lastName']}</td>
            <td>{$student['email']}</td>
            <td>{$student['status']}</td>
            <td>{$student['semester']}</td>
            <td>{$student['schoolYear']}</td>
            <td><p>{$student['totalGrade']}</p></td>
            <td> 
                <a title='Edit' href='student-edit.php?id={$student['studentID']}' class='btn btn-xs'>
                    <span class='fa fa-edit fw-fa'></span>
                </a>
                <a title='Delete' href='functions/student-delete-process.php?id={$student['studentID']}' class='btn btn-xs'>
                    <span class='fa fa-trash'></span>
                </a>
            </td>
        </tr>";
    }
} else {
    echo "Required parameters are missing.";
}
?>
