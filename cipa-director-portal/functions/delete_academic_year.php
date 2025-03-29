<?php
    session_start();
    include_once("../../includes/connection.php");

    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $id = intval($_POST['id']);

            $query = "DELETE FROM academic_year WHERE id = ?";
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
        header("Location: ../academic-calendar.php");
        exit();
    }

    mysqli_close($connect);
?>
