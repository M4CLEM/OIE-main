<?php
session_start();
include_once("../includes/connection.php");

$activeSemester = $_SESSION['active_semester'];
$activeSchoolYear = $_SESSION['active_schoolYear'];

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$response = [];

// Log POST data for debugging
error_log('POST Data: ' . print_r($_POST, true));

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get the student ID from POST data
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

    // Validate and decode criteria and grades data
    $criteriaData = isset($_POST['criteria']) ? json_decode($_POST['criteria'], true) : [];
    $gradesData = isset($_POST['grade']) ? json_decode($_POST['grade'], true) : [];

    // Check if data is empty or invalid
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

        // Check if the grade for this criteria exists in gradesData
        $grade = isset($gradesData[$criteriaTitle]) ? (int)$gradesData[$criteriaTitle] : 0;
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

    // Get companyName and jobrole from the form
    $companyName = isset($_POST['companyName']) ? trim($_POST['companyName']) : null;
    $jobrole = isset($_POST['jobrole']) ? trim($_POST['jobrole']) : null;

    // Insert into database
    $stmt = $connect->prepare("INSERT INTO student_grade (studentID, email, criteria, grade, finalGrade, companyName, jobrole, semester, schoolYear) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssssss", $studentId, $email, $criteriaJson, $gradesJson, $totalGrade, $companyName, $jobrole, $activeSemester, $activeSchoolYear);
    
    if ($stmt->execute()) {
        // Update student's status to "Completed" after inserting the grade
        $updateStmt = $connect->prepare("UPDATE studentinfo SET status = 'Completed' WHERE studentID = ? AND semester = ? AND school_year = ?");
        $updateStmt->bind_param("iss", $studentId, $activeSemester, $activeSchoolYear);
        
        if ($updateStmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Grade successfully submitted and status updated to "Completed"!']);
        } else {
            error_log("Error updating student status: " . $updateStmt->error);
            echo json_encode(['status' => 'success', 'message' => 'Grade submitted, but failed to update student status.']);
        }

        $endDateUpdate = $connect->prepare("UPDATE company_info SET dateEnded = CURDATE() WHERE studentID = ? AND semester = ? AND schoolYear = ?");
        $endDateUpdate->bind_param("iss", $studentId, $activeSemester, $activeSchoolYear);
        $endDateUpdate->execute();

        $updateStmt->close();
    } else {
        error_log("Database Insert Error: " . $stmt->error);
        echo json_encode(['error' => 'Error submitting grade: ' . $stmt->error]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['error' => 'Invalid request method.']);
}
?>
