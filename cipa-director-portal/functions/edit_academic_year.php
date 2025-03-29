<?php
session_start();
include_once("../../includes/connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize input
    $id = trim($_POST['id']);  // Get the ID of the record to update
    $startDate = trim($_POST['editStartingDate']);
    $endDate = trim($_POST['editEndingDate']);
    $semester = trim($_POST['editSemester']);
    $schoolYear = trim($_POST['editSchoolYear']);

    // Check if the ID is numeric
    if (is_numeric($id)) {
        // Prepare SQL statement for updating the academic year record
        $sql = "UPDATE academic_year SET start_date = ?, end_date = ?, semester = ?, schoolYear = ? WHERE id = ?";
        $stmt = $connect->prepare($sql);

        if ($stmt) {
            // Bind parameters to the prepared statement
            $stmt->bind_param("ssssi", $startDate, $endDate, $semester, $schoolYear, $id);

            // Execute the statement
            if ($stmt->execute()) {
                // Send a success response back to the frontend
                echo json_encode(['status' => 'success']);
            } else {
                // Send an error response back to the frontend
                echo json_encode(['status' => 'error', 'message' => $stmt->error]);
            }

            // Close the statement
            $stmt->close();
        } else {
            // Send an error response back to the frontend
            echo json_encode(['status' => 'error', 'message' => $connect->error]);
        }
    } else {
        // Send an error response if the ID is invalid
        echo json_encode(['status' => 'error', 'message' => 'Invalid ID provided.']);
    }

    // Close the database connection
    $connect->close();
}
?>
