<?php
include_once("../../includes/connection.php");
session_start();

$activeSemester = $_SESSION['semester'];
$activeSchoolYear = $_SESSION['schoolYear'];
$companyName = $_SESSION['companyName'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $jobrole = trim($_POST['jobrole']);
    $companyName = trim($_POST['companyName']);

    $stmt = $connect->prepare("SELECT * FROM companylist 
                               WHERE TRIM(companyName) = TRIM(?) 
                               AND TRIM(jobrole) = TRIM(?) 
                               AND TRIM(semester) = TRIM(?) 
                               AND TRIM(schoolYear) = TRIM(?)");
    
    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => 'Prepare failed: ' . $connect->error]);
        exit;
    }

    $stmt->bind_param("ssss", $companyName, $jobrole, $activeSemester, $activeSchoolYear);

    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'error' => 'Execute failed: ' . $stmt->error]);
        exit;
    }

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        $deplymentStatus = 'Approved';

        // Fetch deployed students related to the company and job role
        $studentsQuery = $connect->prepare("
            SELECT si.studentID, CONCAT(si.firstname, ' ', si.lastname) as fullName, si.department, CONCAT(si.course, '-', si.section) as courseSection
            FROM studentinfo si
            INNER JOIN company_info ci ON si.studentID = ci.studentID
            WHERE TRIM(ci.companyName) = TRIM(?)
                AND TRIM(ci.jobrole) = TRIM(?)
                AND TRIM(ci.status) = TRIM(?)
                AND TRIM(ci.semester) = TRIM(?)
                AND TRIM(ci.schoolYear) = TRIM(?)
                AND TRIM(si.semester) = TRIM(?)
                AND TRIM(si.school_year) = TRIM(?)
        ");
        $studentsQuery->bind_param("sssssss", $companyName, $jobrole, $deplymentStatus, $activeSemester, $activeSchoolYear, $activeSemester, $activeSchoolYear);
        $studentsQuery->execute();
        $studentsResult = $studentsQuery->get_result();

        $students = [];
        while ($student = $studentsResult->fetch_assoc()) {
            $students[] = $student;
        }

        echo json_encode([
            'success' => true,
            'companyNumber' => $row['No'],
            'jobrole' => $row['jobrole'],
            'workType' => $row['workType'],
            'address' => $row['companyaddress'],
            'contactPerson' => $row['contactPerson'],
            'jobDescription' => $row['jobdescription'],
            'jobRequirement' => $row['jobreq'],
            'link' => $row['link'],
            'department' => $row['dept'],
            'students' => $students
        ]);
    } else {
        echo json_encode(['success' => false]);
    }
}
?>
