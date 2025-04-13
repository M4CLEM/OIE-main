<?php
session_start();
include_once("../includes/connection.php");

header("Content-Type: application/json"); // Ensure JSON response

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and trim inputs
    $name = trim($_POST['reg_name']);
    $email = trim($_POST['reg_uname']);
    $password = trim($_POST['reg_pass']);
    $confirmPassword = trim($_POST['reg_confirm_pass']);
    
    // Check if passwords match
    if ($password !== $confirmPassword) {
        echo json_encode(["status" => "error", "message" => "Passwords do not match."]);
        exit();
    }

    // Check if email already exists in staff_list or users table
    $emailCheckQuery = "SELECT email FROM staff_list WHERE email = ? UNION SELECT username FROM users WHERE username = ?";
    $emailCheckStmt = $connect->prepare($emailCheckQuery);
    $emailCheckStmt->bind_param("ss", $email, $email);
    $emailCheckStmt->execute();
    $emailCheckStmt->store_result();

    if ($emailCheckStmt->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Email is already in use."]);
        exit();
    }
    $emailCheckStmt->close();

    // Hardcode role to "CIPA"
    $role = "CIPA";

    // Prepare insert query for CIPA
    $sql = "INSERT INTO staff_list (name, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("ssss", $name, $email, $password, $role);

    // Execute insertion
    if ($stmt->execute()) {
        // Insert into users table for CIPA role
        $accStmt = $connect->prepare("INSERT INTO users (username, role, password) VALUES (?, ?, ?)");
        $accStmt->bind_param("sss", $email, $role, $password);
        $accStmt->execute();
        $accStmt->close();

        // Redirect to index.php after successful registration
        header("Location: ../index.php");
        exit();
    } else {
        echo json_encode(["status" => "error", "message" => "Database error: " . $connect->error]);
        exit();
    }
}
?>
