<?php
session_start();
include("../../includes/connection.php");

$email = $_SESSION['student'] ?? null;

if (!$email) {
    echo json_encode(['unreadCount' => 0]);
    exit;
}

$query = "SELECT studentID, department FROM studentinfo WHERE email = ?";
$stmt = $connect->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$studentID = $user['studentID'];
$department = $user['department'];

$notifQuery = "SELECT viewer FROM event_reminder WHERE department = ? OR department = 'All'";
$notifStmt = $connect->prepare($notifQuery);
$notifStmt->bind_param("s", $department);
$notifStmt->execute();
$notifResult = $notifStmt->get_result();

$unreadCount = 0;

while ($row = $notifResult->fetch_assoc()) {
    $viewers = json_decode($row['viewer'], true) ?? [];
    if (!in_array($studentID, $viewers)) {
        $unreadCount++;
    }
}

echo json_encode(['unreadCount' => $unreadCount]);
?>
