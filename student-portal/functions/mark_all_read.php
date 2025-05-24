<?php
session_start();
include("../../includes/connection.php");

header('Content-Type: application/json');

if (!isset($_POST['studentID']) || !isset($_POST['department'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing required data.'
    ]);
    exit;
}

$studentID = $_POST['studentID'];
$department = $_POST['department'];

$stmt = $connect->prepare("SELECT id, viewer FROM event_reminder WHERE department = ?");
$stmt->bind_param("s", $department);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $id = $row['id'];
    $viewers = json_decode($row['viewer'], true) ?? [];

    if (!in_array($studentID, $viewers)) {
        $viewers[] = $studentID;
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
