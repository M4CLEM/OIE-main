<?php
session_start();
include_once("../../includes/connection.php");

// Check if the request is an AJAX request
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $id = intval($_POST['id']); // Ensure it's an integer

        // Start transaction
        $connect->begin_transaction();

        try {
            // Delete from adviser_criteria first to maintain referential integrity
            $query1 = "DELETE FROM adviser_criteria WHERE id = ?";
            $stmt1 = $connect->prepare($query1);
            $stmt1->bind_param("i", $id);
            $stmt1->execute();
            $stmt1->close();

            // Delete from criteria_list_view
            $query2 = "DELETE FROM criteria_list_view WHERE id = ?";
            $stmt2 = $connect->prepare($query2);
            $stmt2->bind_param("i", $id);
            $stmt2->execute();
            $stmt2->close();

            // Commit transaction
            $connect->commit();

            echo json_encode(['status' => 'success', 'message' => 'Record deleted successfully.']);
        } catch (Exception $e) {
            // Rollback in case of error
            $connect->rollback();
            echo json_encode(['status' => 'error', 'message' => 'Error deleting record: ' . $e->getMessage()]);
        }
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
