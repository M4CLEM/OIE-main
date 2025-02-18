<?php
session_start();
include_once("../includes/connection.php");

// Initialize response
$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from the form
    $student_id = $_POST['studentID'];
    $status = $_POST['status'];
    $contactNo = $_POST['contact'];
    $objective = $_POST['objective'];
    $skills = $_POST['skills'];
    $seminars = $_POST['seminars'];

    // Update other information except for the photo in the database
    $stmt = $connect->prepare('UPDATE studentinfo SET status = ?, contactNo = ?, objective = ?, skills = ?, seminars = ? WHERE studentID = ?');
    $stmt->bind_param('sisssi', $status, $contactNo, $objective, $skills, $seminars, $student_id);
    $stmt->execute();
    $stmt->close();

    // Prepare response
    $response['success'] = true;
    $response['message'] = 'Your information has been updated.';

    // Send JSON response to JavaScript
    header('Content-Type: application/json');
    echo json_encode($response);
}

$connect->close();
?>