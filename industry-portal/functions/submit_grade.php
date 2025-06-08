<?php
session_start();
include_once("../../includes/connection.php");

$activeSemester = $_SESSION['semester'];
$activeSchoolYear = $_SESSION['schoolYear'];

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$response = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $studentId = isset($_POST['studentID']) ? trim($_POST['studentID']) : '';
    if (empty($studentId)) {
        echo json_encode(['error' => 'Student ID is required.']);
        exit;
    }

    // Fetch email from studentinfo table
    $sql = "SELECT email FROM studentinfo WHERE studentID = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['error' => 'Student not found.']);
        exit;
    }

    $row = $result->fetch_assoc();
    $email = $row['email'];
    $stmt->close();

    // Validate criteria and grades data
    $criteriaData = isset($_POST['criteria']) ? $_POST['criteria'] : [];
    $gradesData = isset($_POST['grade']) ? $_POST['grade'] : [];
    if (empty($criteriaData) || empty($gradesData)) {
        echo json_encode(['error' => 'Invalid criteria or grades data.']);
        exit;
    }

    // Process criteria and grades
    $criteriaArray = [];
    $finalGrades = [];
    foreach ($criteriaData as $index => $value) {
        $criteriaTitle = isset($value['criteria']) ? $value['criteria'] : '';
        $criteriaDescription = isset($value['description']) ? $value['description'] : '';
        $criteriaPercentage = isset($value['percentage']) ? (int)$value['percentage'] : 0;

        if (empty($criteriaTitle) || $criteriaPercentage < 0) {
            echo json_encode(['error' => 'Invalid criteria data.']);
            exit;
        }

        $criteriaArray[] = [
            'criteria' => $criteriaTitle,
            'description' => $criteriaDescription,
            'percentage' => $criteriaPercentage
        ];

        $grade = isset($gradesData[$index]) ? (int)$gradesData[$index] : 0;
        $finalGrades[$criteriaTitle] = $grade;
    }

    // Encode data to JSON
    $criteriaJson = !empty($criteriaArray) ? json_encode($criteriaArray, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : json_encode([], JSON_FORCE_OBJECT);
    $gradesJson = !empty($finalGrades) ? json_encode($finalGrades, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : json_encode([], JSON_FORCE_OBJECT);

    // Validate total grade
    $totalGrade = isset($_POST['totalGrade']) ? (int)$_POST['totalGrade'] : 0;
    if ($totalGrade < 0 || $totalGrade > 100) {
        echo json_encode(['error' => 'Total grade must be between 0 and 100.']);
        exit;
    }

    // Get companyName and jobrole from the form
    $companyName = isset($_POST['companyName']) ? trim($_POST['companyName']) : null;
    $jobrole = isset($_POST['jobrole']) ? trim($_POST['jobrole']) : null;

    // Insert into database
    $stmt = $connect->prepare("INSERT INTO student_grade (studentID, email, criteria, grade, finalGrade, companyName, jobrole, semester, schoolYear) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssssss", $studentId, $email, $criteriaJson, $gradesJson, $totalGrade, $companyName, $jobrole, $activeSemester, $activeSchoolYear);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Grade successfully submitted!']);

        // Update student's status to "Completed" in the background
        $updateStmt = $connect->prepare("UPDATE studentinfo SET status = 'Completed' WHERE studentID = ? AND semester = ? AND school_year = ?");
        $updateStmt->bind_param("iss", $studentId, $activeSemester, $activeSchoolYear);
        
        if (!$updateStmt->execute()) {
            error_log("Error updating student status: " . $updateStmt->error);
        }

        $endDateUpdate = $connect->prepare("UPDATE company_info SET dateEnded = CURDATE() WHERE studentID = ? AND semester = ? AND schoolYear = ?");
        $endDateUpdate->bind_param("iss", $studentId, $activeSemester, $activeSchoolYear);
        $endDateUpdate->execute();
        
        $updateStmt->close();
    } else {
        // Log error for debugging
        error_log("Database Insert Error: " . $stmt->error);
        echo json_encode(['error' => 'Error submitting grade: ' . $stmt->error]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['error' => 'Invalid request method.']);
}
?>
