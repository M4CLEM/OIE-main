<?php
include_once("../../includes/connection.php");
session_start();

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => 'Something went wrong.'
];

// Validate POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentID = $_POST['studentID'] ?? '';
    $trainerEmail = $_POST['trainerEmail'] ?? '';
    $trainerContact = $_POST['trainerContact'] ?? '';
    $semester = $_SESSION['semester'] ?? '';
    $schoolYear = $_SESSION['schoolYear'] ?? '';

    if (empty($studentID) || empty($trainerEmail) || empty($trainerContact)) {
        $response['message'] = "Missing required fields.";
        echo json_encode($response);
        exit;
    }

    // Prepare UPDATE queries
    $updateCompanyInfo = "
        UPDATE company_info 
        SET trainerEmail = ?, trainerContact = ? 
        WHERE studentID = ? AND semester = ? AND schoolYear = ?
    ";

    $updateStudentInfo = "
        UPDATE studentinfo 
        SET trainerEmail = ? 
        WHERE studentID = ? AND semester = ? AND school_year = ?
    ";

    // Use transaction for safety
    $connect->begin_transaction();

    try {
        // Update company_info
        $stmt1 = $connect->prepare($updateCompanyInfo);
        $stmt1->bind_param("sssss", $trainerEmail, $trainerContact, $studentID, $semester, $schoolYear);
        $stmt1->execute();

        // Update studentinfo
        $stmt2 = $connect->prepare($updateStudentInfo);
        $stmt2->bind_param("ssss", $trainerEmail, $studentID, $semester, $schoolYear);
        $stmt2->execute();

        // Commit transaction
        $connect->commit();

        $response['success'] = true;
        $response['message'] = "Trainer assigned successfully.";
    } catch (Exception $e) {
        // Rollback on error
        $connect->rollback();
        $response['message'] = "Database error: " . $e->getMessage();
    }
} else {
    $response['message'] = "Invalid request method.";
}

echo json_encode($response);
