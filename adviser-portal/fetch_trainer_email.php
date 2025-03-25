<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set response type to JSON
header("Content-Type: application/json");

// ✅ Include database connection
require_once("../includes/connection.php");

// Check if `$connect` is set
if (!isset($connect)) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

// Get JSON input from JavaScript
$input = json_decode(file_get_contents("php://input"), true);

// Validate studentIDs
if (!isset($input['studentIDs']) || !is_array($input['studentIDs']) || count($input['studentIDs']) === 0) {
    echo json_encode(["error" => "No student IDs provided"]);
    exit;
}

// Prepare the SQL query for multiple student IDs
$placeholders = implode(',', array_fill(0, count($input['studentIDs']), '?'));
$query = "SELECT trainerEmail FROM studentinfo WHERE studentID IN ($placeholders)";
$stmt = $connect->prepare($query);

if (!$stmt) {
    echo json_encode(["error" => "Database error: " . $connect->error]);
    exit;
}

// Bind student IDs dynamically
$stmt->bind_param(str_repeat("s", count($input['studentIDs'])), ...$input['studentIDs']);
$stmt->execute();
$result = $stmt->get_result();

$trainerEmails = [];
while ($row = $result->fetch_assoc()) {
    if (!empty($row["trainerEmail"])) {
        $trainerEmails[] = $row["trainerEmail"];
    }
}

// Close statement and connection
$stmt->close();
$connect->close();

// Return the trainer emails as JSON
if (!empty($trainerEmails)) {
    echo json_encode(["trainerEmails" => array_unique($trainerEmails)]); // Remove duplicates
} else {
    echo json_encode(["error" => "No trainers found for selected students"]);
}
?>
