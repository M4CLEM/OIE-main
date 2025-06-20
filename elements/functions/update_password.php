<?php
session_start();
require __DIR__ . '/../../includes/connection.php';
header('Content-Type: application/json');

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

// Check OTP verification
if (!isset($_SESSION['otp_verified']) || $_SESSION['otp_verified'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Please verify the OTP before updating your password.']);
    exit;
}

// Get new passwords
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm'] ?? '';

if (empty($password) || empty($confirm)) {
    echo json_encode(['status' => 'error', 'message' => 'Password fields cannot be empty.']);
    exit;
}

if ($password !== $confirm) {
    echo json_encode(['status' => 'error', 'message' => 'Passwords do not match.']);
    exit;
}

// Identify user and role from session
$sessionRoles = [
    'coordinator' => ['email' => $_SESSION['coordinator'] ?? null, 'role' => 'coordinator'],
    'CIPA' => ['email' => $_SESSION['CIPA'] ?? null, 'role' => 'CIPA'],
    'IndustryPartner' => ['email' => $_SESSION['IndustryPartner'] ?? null, 'role' => 'IndustryPartner'],
    'student' => ['email' => $_SESSION['student'] ?? null, 'role' => 'student'],
    'adviser' => ['email' => $_SESSION['adviser'] ?? null, 'role' => 'adviser'],
];

$currentUser = null;

foreach ($sessionRoles as $key => $data) {
    if ($data['email']) {
        $currentUser = $data;
        break;
    }
}

if (!$currentUser) {
    echo json_encode(['status' => 'error', 'message' => 'User session expired or unknown role.']);
    exit;
}

$email = $currentUser['email'];
$role = $currentUser['role'];

// Define update queries
$updateQueries = [
    'coordinator' => [
        'users' => "UPDATE users SET password = ? WHERE username = ? AND role = 'coordinator'",
        'staff_list' => "UPDATE staff_list SET password = ? WHERE email = ? AND role = 'coordinator'"
    ],
    'CIPA' => [
        'users' => "UPDATE users SET password = ? WHERE username = ? AND role = 'CIPA'",
        'staff_list' => "UPDATE staff_list SET password = ? WHERE email = ? AND role = 'CIPA'"
    ],
    'IndustryPartner' => [
        'users' => "UPDATE users SET password = ? WHERE username = ? AND role = 'IndustryPartner'",
    ],
    'student' => [
        'users' => "UPDATE users SET password = ? WHERE username = ? AND role = 'student'",
    ],
    'adviser' => [
        'users' => "UPDATE users SET password = ? WHERE username = ? AND role = 'adviser'",
    ],
];

// Run queries
$queries = $updateQueries[$role] ?? null;

if (!$queries) {
    echo json_encode(['status' => 'error', 'message' => 'Update query not defined for this role.']);
    exit;
}

$allSuccessful = true;

foreach ($queries as $table => $query) {
    $stmt = $connect->prepare($query);
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => "Database error preparing $table: " . $connect->error]);
        $allSuccessful = false;
        break;
    }

    $stmt->bind_param('ss', $password, $email);
    if (!$stmt->execute()) {
        $allSuccessful = false;
    }

    $stmt->close();
}

// Clear OTP
if ($allSuccessful) {
    unset($_SESSION['otp'], $_SESSION['otp_created_at'], $_SESSION['otp_verified']);
    echo json_encode(['status' => 'success', 'message' => 'Password updated successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update password in one or more tables.']);
}

$connect->close();
