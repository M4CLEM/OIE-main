<?php
include_once("../../includes/connection.php");
session_start();
$activeSemester = $_SESSION['semester'];
$activeSchoolYear = $_SESSION['schoolYear'];
header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Something went wrong.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $logID = $_POST['logID'] ?? '';

    if (empty($logID)) {
        $response['message'] = "Missing log ID.";
    } else {
        $stmt = $connect->prepare("UPDATE logdata SET is_approved = 'Rejected' WHERE id = ? AND semester = ? AND schoolYear = ?");
        $stmt->bind_param("iss", $logID, $activeSemester, $activeSchoolYear);
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Log rejected successfully.";
        } else {
            $response['message'] = "Failed to update log.";
        }
    }
} else {
    $response['message'] = "Invalid request method.";
}

echo json_encode($response);
