<?php
    session_start();
    include("../../includes/connection.php");

    header('Content-Type: application/json');

    if (!isset($_POST['employeeNumber'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Missing required data.'
        ]);
        exit;
    }

    $employeeNumber = $_POST['employeeNumber'];

    $stmt = $connect->prepare("SELECT id, viewer FROM event_reminder");
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $viewers = json_decode($row['viewer'], true) ?? [];

        if (!in_array($employeeNumber, $viewers)) {
            $viewers[] = $employeeNumber;
            $updatedViewers = json_encode($viewers);

            $updateStmt = $connect->prepare("UPDATE event_reminder SET viewer = ? WHERE id = ?");
            $updateStmt->bind_param("si", $updatedViewers, $id);
            $updateStmt->execute();
        }
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'All notifications marked as read.'
    ]);
    exit;
?>