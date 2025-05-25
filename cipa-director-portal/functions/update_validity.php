<?php
    include_once("../../includes/connection.php");

    if (!isset($_POST['documentIDs'], $_POST['validityDuration'])) {
        http_response_code(400);
        echo 'Missing data';
        exit;
    }

    $ids = array_filter(array_map('intval', explode(',', $_POST['documentIDs'])));
    $duration = (int)$_POST['validityDuration'];

    if (!$ids || $duration <= 0) {
        http_response_code(400);
        echo 'Invalid input';
        exit;
    }

    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $sql = "UPDATE documents SET validity = ? WHERE id IN ($placeholders)";
    $stmt = $connect->prepare($sql);

    if (!$stmt) {
        http_response_code(500);
        echo 'Failed to prepare statement';
        exit;
    }

    // Build parameter types and values
    $types = str_repeat('i', count($ids) + 1); // one for duration + IDs
    $params = array_merge([$duration], $ids);

    // Convert to references
    $refs = [];
    foreach ($params as $key => $value) {
        $refs[$key] = &$params[$key];
    }

    $stmt->bind_param($types, ...$refs);

    if ($stmt->execute()) {
        echo 'OK';
    } else {
        http_response_code(500);
        echo 'Failed to execute statement';
    }
?>
