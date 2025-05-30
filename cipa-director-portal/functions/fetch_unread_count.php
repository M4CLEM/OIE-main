<?php
    session_start();
    include("../../includes/connection.php");

    $cipa = $_SESSION['CIPA'];

    if (!$cipa) {
        echo json_encode(['unreadCount' => 0]);
        exit;
    }

    $employeeQuery = "SELECT employeeNumber FROM staff_list WHERE email = ?";
    $employeeStmt = $connect->prepare($employeeQuery);
    $employeeStmt->bind_param("s", $cipa);
    $employeeStmt->execute();
    $result = $employeeStmt->get_result();
    $employee = $result->fetch_assoc();

    $employeeNumber = $employee['employeeNumber'];

    $notificationQuery = "SELECT viewer FROM event_reminder";
    $notificationStmt = $connect->prepare($notificationQuery);
    $notificationStmt->execute();
    $notificationResult = $notificationStmt->get_result();

    $unreadCount = 0;

    while ($row = $notificationResult->fetch_assoc()) {
        $viewers = json_decode($row['viewer'], true) ?? [];
        if (!in_array($employeeNumber, $viewers)) {
            $unreadCount++;
        }
    }

    echo json_encode(['unreadCount' => $unreadCount]);
?>