<?php
    include("../../includes/connection.php");

    header('Content-Type: application/json');

    // Validate POST fields
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $postDate = $_POST['postDate'] ?? '';
    $endDate = $_POST['endDate'] ?? '';

    if (empty($title) || empty($description) || empty($department) || empty($postDate) || empty($endDate)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    // Insert into DB
    try {
        $stmt = $connect->prepare("INSERT INTO event_reminder (title, description, department, datePosted, endDate) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $title, $description, $department, $postDate, $endDate);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Announcement created.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to create announcement.']);
        }

        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()]);
    }

    $connect->close();
?>
