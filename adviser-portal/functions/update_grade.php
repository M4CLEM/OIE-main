<?php
session_start();
include_once("../../includes/connection.php");

$activeSemester = $_SESSION['semester'];
$activeSchoolYear = $_SESSION['schoolYear'];

// Enable error reporting for debugging
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$response = [];

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate and sanitize studentId
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
    $criteriaData = $_POST['criteria'] ?? [];
    $gradesData = $_POST['grade'] ?? [];
    if (empty($criteriaData) || empty($gradesData)) {
        echo json_encode(['error' => 'Invalid criteria or grades data.']);
        exit;
    }

    // Process criteria and grades
    $criteriaArray = [];
    $finalGrades = [];
    foreach ($criteriaData as $index => $value) {
        $criteriaTitle = $value['criteria'] ?? '';
        $criteriaDescription = $value['description'] ?? '';
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
    $criteriaJson = json_encode($criteriaArray, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    $gradesJson = json_encode($finalGrades, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

    // Validate total grade
    $totalGrade = isset($_POST['totalGrade']) ? (int)$_POST['totalGrade'] : 0;
    if ($totalGrade < 0 || $totalGrade > 100) {
        echo json_encode(['error' => 'Total grade must be between 0 and 100.']);
        exit;
    }

    // Check if the grade record exists for the student
    $stmt = $connect->prepare("SELECT id FROM adviser_student_grade WHERE studentID = ? AND semester = ? AND schoolYear = ?");
    $stmt->bind_param("sss", $studentId, $activeSemester, $activeSchoolYear);
    $stmt->execute();
    $result = $stmt->get_result();

    // If record exists, update it; otherwise, insert a new record
    if ($result->num_rows > 0) {
        // Record exists, update it
        $updateStmt = $connect->prepare("UPDATE adviser_student_grade SET criteria = ?, grade = ?, finalGrade = ? WHERE studentID = ? AND semester = ? AND schoolYear = ?");
        $updateStmt->bind_param("ssssss", $criteriaJson, $gradesJson, $totalGrade, $studentId, $activeSemester, $activeSchoolYear);
        
        if ($updateStmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Grade successfully updated!']);
        } else {
            echo json_encode(['error' => 'Error updating grade: ' . $updateStmt->error]);
        }
        $updateStmt->close();
    } else {
        // Record does not exist, insert a new one
        $insertStmt = $connect->prepare("INSERT INTO adviser_student_grade (studentID, email, criteria, grade, finalGrade, semester, schoolYear) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insertStmt->bind_param("sssssss", $studentId, $email, $criteriaJson, $gradesJson, $totalGrade, $activeSemester, $activeSchoolYear);
        
        if ($insertStmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Grade successfully submitted!']);
        } else {
            echo json_encode(['error' => 'Error submitting grade: ' . $insertStmt->error]);
        }
        $insertStmt->close();
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'Invalid request method.']);
}
?>
