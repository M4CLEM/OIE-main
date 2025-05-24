<?php
session_start();
include("../../includes/connection.php");

header('Content-Type: application/json');

if (!isset($_POST['notification_id']) || !isset($_POST['studentID'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing parameters: notification_id or studentID.'
    ]);
    exit;
}

$reminderID = intval($_POST['notification_id']);
$studentID = trim($_POST['studentID']);

if (empty($studentID) || $reminderID <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid studentID or notification_id.'
    ]);
    exit;
}

// Fetch current viewers
$stmt = $connect->prepare("SELECT viewer FROM event_reminder WHERE id = ?");
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $connect->error]);
    exit;
}
$stmt->bind_param("i", $reminderID);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    echo json_encode(['status' => 'error', 'message' => 'Notification not found with id ' . $reminderID]);
    exit;
}

$viewersJson = $row['viewer'];

// Debug: output current viewer JSON
error_log("Current viewers JSON for notification $reminderID: $viewersJson");

$viewers = json_decode($viewersJson, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    // If invalid JSON or null, reset to empty array
    $viewers = [];
}

// Debug: output decoded array
error_log("Decoded viewers array: " . print_r($viewers, true));

if (!in_array($studentID, $viewers)) {
    $viewers[] = $studentID;
    $updatedViewers = json_encode($viewers);

    // Debug: show updated viewers JSON
    error_log("Updated viewers JSON: $updatedViewers");

    $updateStmt = $connect->prepare("UPDATE event_reminder SET viewer = ? WHERE id = ?");
    if (!$updateStmt) {
        echo json_encode(['status' => 'error', 'message' => 'Prepare failed on update: ' . $connect->error]);
        exit;
    }
    $updateStmt->bind_param("si", $updatedViewers, $reminderID);
    $execResult = $updateStmt->execute();

    if (!$execResult) {
        echo json_encode(['status' => 'error', 'message' => 'Execute failed on update: ' . $updateStmt->error]);
        exit;
    }

    // Debug: check affected rows
    error_log("Rows affected: " . $updateStmt->affected_rows);

    echo json_encode(['status' => 'success', 'message' => 'Notification marked as read.']);
} else {
    echo json_encode(['status' => 'success', 'message' => 'Notification already marked as read.']);
}
exit;
