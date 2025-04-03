<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set response type to JSON
header("Content-Type: application/json");

// ✅ Include database connection
require_once("../includes/connection.php");

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Retrieve semester and school year from session
$semester = $_SESSION['semester'] ?? null;
$schoolYear = $_SESSION['schoolYear'] ?? null;

// Validate semester and school year
if (!$semester || !$schoolYear) {
    echo json_encode(["error" => "Semester or school year is missing"]);
    exit;
}

// Check if database connection is established
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

// Prepare placeholders for IN clause
$placeholders = implode(',', array_fill(0, count($input['studentIDs']), '?'));

// Prepare SQL query
$query = "SELECT trainerEmail FROM studentinfo 
          WHERE studentID IN ($placeholders) 
          AND semester = ? 
          AND school_year = ?";

$stmt = $connect->prepare($query);

if (!$stmt) {
    echo json_encode(["error" => "Database error: " . $connect->error]);
    exit;
}

// Prepare types dynamically (assuming studentID is an integer)
$types = str_repeat("i", count($input['studentIDs'])) . "ss";

// Bind parameters
$params = array_merge($input['studentIDs'], [$semester, $schoolYear]);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Fetch trainer emails
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
