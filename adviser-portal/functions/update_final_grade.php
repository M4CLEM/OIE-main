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

    // Get the criteria and grades data from POST
    $adviserCriteriaData = isset($_POST['adviserCriteria']) ? $_POST['adviserCriteria'] : [];
    $adviserGrades = isset($_POST['adviserScore']) ? $_POST['adviserScore'] : [];
    $companyCriteriaData = isset($_POST['companyCriteria']) ? $_POST['companyCriteria'] : [];
    $companyGrades = isset($_POST['companyScore']) ? $_POST['companyScore'] : [];

    // Check if all required data is present
    if (empty($adviserCriteriaData) || empty($adviserGrades) || empty($companyCriteriaData) || empty($companyGrades)) {
        echo json_encode(['error' => 'Invalid criteria or grades data.']);
        exit;
    }

    // Process adviser criteria and grades
    $adviserCriteriaArray = [];
    $adviserFinalGrades = [];
    foreach ($adviserCriteriaData as $index => $criteria) {
        $criteriaTitle = $criteria['criteria'];
        $criteriaDescription = $criteria['description'];
        $criteriaPercentage = (int)$criteria['percentage'];
        $grade = isset($adviserGrades[$criteriaTitle]) ? (int)$adviserGrades[$criteriaTitle] : 0;

        if (empty($criteriaTitle) || $criteriaPercentage < 0) {
            echo json_encode(['error' => 'Invalid adviser criteria data.']);
            exit;
        }

        $adviserCriteriaArray[] = [
            'criteria' => $criteriaTitle,
            'description' => $criteriaDescription,
            'percentage' => $criteriaPercentage
        ];

        $adviserFinalGrades[$criteriaTitle] = $grade;
    }

    // Process company criteria and grades
    $companyCriteriaArray = [];
    $companyFinalGrades = [];
    foreach ($companyCriteriaData as $index => $criteria) {
        $criteriaTitle = $criteria['criteria'];
        $criteriaDescription = $criteria['description'];
        $criteriaPercentage = (int)$criteria['percentage'];
        $grade = isset($companyGrades[$criteriaTitle]) ? (int)$companyGrades[$criteriaTitle] : 0;

        if (empty($criteriaTitle) || $criteriaPercentage < 0) {
            echo json_encode(['error' => 'Invalid company criteria data.']);
            exit;
        }

        $companyCriteriaArray[] = [
            'criteria' => $criteriaTitle,
            'description' => $criteriaDescription,
            'percentage' => $criteriaPercentage
        ];

        $companyFinalGrades[$criteriaTitle] = $grade;
    }

    // Convert criteria and grades to JSON
    $adviserCriteriaJson = json_encode($adviserCriteriaArray, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    $adviserGradesJson = json_encode($adviserFinalGrades, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    $companyCriteriaJson = json_encode($companyCriteriaArray, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    $companyGradesJson = json_encode($companyFinalGrades, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

    // Validate total grades
    $adviserTotalGrade = isset($_POST['adviserGrade']) ? (int)$_POST['adviserGrade'] : 0;
    $companyTotalGrade = isset($_POST['companyGrade']) ? (int)$_POST['companyGrade'] : 0;

    if ($adviserTotalGrade < 0 || $adviserTotalGrade > 100 || $companyTotalGrade < 0 || $companyTotalGrade > 100) {
        echo json_encode(['error' => 'Total grades must be between 0 and 100.']);
        exit;
    }

    // Update or insert data for adviser
    $stmt = $connect->prepare("SELECT id FROM adviser_student_grade WHERE studentID = ? AND semester = ? AND schoolYear = ?");
    $stmt->bind_param("sss", $studentId, $activeSemester, $activeSchoolYear);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Record exists, update it
        $updateStmt = $connect->prepare("UPDATE adviser_student_grade SET criteria = ?, grade = ?, finalGrade = ? WHERE studentID = ? AND semester = ? AND schoolYear = ?");
        $updateStmt->bind_param("ssssss", $adviserCriteriaJson, $adviserGradesJson, $adviserTotalGrade, $studentId, $activeSemester, $activeSchoolYear);
        
        if ($updateStmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Adviser grade successfully updated!']);
        } else {
            echo json_encode(['error' => 'Error updating adviser grade: ' . $updateStmt->error]);
        }
        $updateStmt->close();
    } else {
        // Record does not exist, insert a new one
        $insertStmt = $connect->prepare("INSERT INTO adviser_student_grade (studentID, email, criteria, grade, finalGrade, semester, schoolYear) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insertStmt->bind_param("sssssss", $studentId, $email, $adviserCriteriaJson, $adviserGradesJson, $adviserTotalGrade, $activeSemester, $activeSchoolYear);
        
        if ($insertStmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Adviser grade successfully submitted!']);
        } else {
            echo json_encode(['error' => 'Error submitting adviser grade: ' . $insertStmt->error]);
        }
        $insertStmt->close();
    }

    // Update or insert data for company
    $stmt = $connect->prepare("SELECT id FROM student_grade WHERE studentID = ? AND semester = ? AND schoolYear = ?");
    $stmt->bind_param("sss", $studentId, $activeSemester, $activeSchoolYear);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Record exists, update it
        $updateStmt = $connect->prepare("UPDATE student_grade SET criteria = ?, grade = ?, finalGrade = ? WHERE studentID = ? AND semester = ? AND schoolYear = ?");
        $updateStmt->bind_param("ssssss", $companyCriteriaJson, $companyGradesJson, $companyTotalGrade, $studentId, $activeSemester, $activeSchoolYear);
        
        if ($updateStmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Company grade successfully updated!']);
        } else {
            echo json_encode(['error' => 'Error updating company grade: ' . $updateStmt->error]);
        }
        $updateStmt->close();
    } else {
        // Record does not exist, insert a new one
        $insertStmt = $connect->prepare("INSERT INTO student_grade (studentID, email, criteria, grade, finalGrade, semester, schoolYear) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insertStmt->bind_param("sssssss", $studentId, $email, $companyCriteriaJson, $companyGradesJson, $companyTotalGrade, $activeSemester, $activeSchoolYear);
        
        if ($insertStmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Company grade successfully submitted!']);
        } else {
            echo json_encode(['error' => 'Error submitting company grade: ' . $insertStmt->error]);
        }
        $insertStmt->close();
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'Invalid request method.']);
}
?>
