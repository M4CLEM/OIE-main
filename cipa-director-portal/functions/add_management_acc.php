<?php
session_start();
include_once("../../includes/connection.php");

header("Content-Type: application/json"); // Ensure JSON response

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['staffname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirmPassword']);
    $role = trim($_POST['role']);
    $employeeNumber = trim($_POST['employeenumber']);

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

    // Prepare insert query based on role
    if ($role === "CIPA") {
        $sql = "INSERT INTO staff_list (employeeNumber, name, email, password, role) VALUES (?, ?, ?, ?, ?)";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("sssss", $employeeNumber, $name, $email, $password, $role);
    } else {
        $department = trim($_POST['department']);
        $sql = "INSERT INTO staff_list (employeeNumber, name, email, password, role, department) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("ssssss", $employeeNumber, $name, $email, $password, $role, $department);
    }

    // Execute insertion
    if ($stmt->execute()) {
        // Insert into users table
        if ($role === "CIPA") {
            $accStmt = $connect->prepare("INSERT INTO users (username, role, password) VALUES (?, ?, ?)");
            $accStmt->bind_param("sss", $email, $role, $password);
        } else {
            $accStmt = $connect->prepare("INSERT INTO users (username, role, password, department) VALUES (?, ?, ?, ?)");
            $accStmt->bind_param("ssss", $email, $role, $password, $department);
        }
        $accStmt->execute();
        $accStmt->close();

        echo json_encode(["status" => "success", "message" => "Registration successful!"]);
        exit();
    } else {
        echo json_encode(["status" => "error", "message" => "Database error: " . $connect->error]);
        exit();
    }

    $stmt->close();
    $connect->close();
}
?>
