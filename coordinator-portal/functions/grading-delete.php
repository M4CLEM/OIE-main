<?php
session_start();
include_once("../../includes/connection.php");

// Check if the request is an AJAX request
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $id = intval($_POST['id']); // Ensure it's an integer

        // Use a prepared statement to prevent SQL injection
        $query = "DELETE FROM criteria_list_view WHERE id = ?";
        $stmt = $connect->prepare($query);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Record deleted successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error deleting record: ' . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid ID received.']);
    }
} else {
    // Redirect if accessed directly
    header("Location: ../grading-view.php");
    exit();
}

mysqli_close($connect);
?>
