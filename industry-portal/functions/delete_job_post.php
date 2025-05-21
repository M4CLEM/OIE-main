<?php
    include_once("../../includes/connection.php");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'];

        $stmt = $connect->prepare("DELETE FROM companylist WHERE No = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete job post.']);
        }

        $stmt->close();
    $connect->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
