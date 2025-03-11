<?php
    session_start();

    include_once("../../includes/connection.php");

    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $id = intval($_POST['id']);

            $query = "DELETE FROM documents_list WHERE id = ?";
            $stmt = $connect->prepare($query);
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Record deleted successfully.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error deleting record: ' . $stmt->error]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid ID received.']);
        }
    } else {
        // Redirect if accessed directly
    header("Location: ../documents.php");
    exit();
    }
    
    mysqli_close($connect);
?>