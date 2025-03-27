<?php
session_start();
include_once("../../includes/connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['staffname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirmPassword']);
    $role = trim($_POST['role']);

    // Check if passwords match
    if ($password !== $confirmPassword) {
        echo "<script>alert('Error: Passwords do not match.'); window.history.back();</script>";
        exit();
    }

    // Hash the password before storing (Security best practice)
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Check if email already exists in staff_list or users table
    $emailCheckQuery = "SELECT email FROM staff_list WHERE email = ? UNION SELECT username FROM users WHERE username = ?";
    $emailCheckStmt = $connect->prepare($emailCheckQuery);
    $emailCheckStmt->bind_param("ss", $email, $email);
    $emailCheckStmt->execute();
    $emailCheckStmt->store_result();

    if ($emailCheckStmt->num_rows > 0) {
        echo "<script>alert('Error: Email is already in use.'); window.history.back();</script>";
        exit();
    }
    $emailCheckStmt->close();

    // Prepare insert query based on role
    if ($role === "CIPA") {
        $sql = "INSERT INTO staff_list (name, email, password, role) VALUES (?, ?, ?, ?)";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("ssss", $name, $email, $hashedPassword, $role);
    } else {
        $department = trim($_POST['department']);
        $sql = "INSERT INTO staff_list (name, email, password, role, department) VALUES (?, ?, ?, ?, ?)";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("sssss", $name, $email, $hashedPassword, $role, $department);
    }

    // Execute insertion
    if ($stmt->execute()) {
        // Insert into users table
        if ($role === "CIPA") {
            $accStmt = $connect->prepare("INSERT INTO users (username, role, password) VALUES (?, ?, ?)");
            $accStmt->bind_param("sss", $email, $role, $hashedPassword);
        } else {
            $accStmt = $connect->prepare("INSERT INTO users (username, role, password, department) VALUES (?, ?, ?, ?)");
            $accStmt->bind_param("ssss", $email, $role, $hashedPassword, $department);
        }
        $accStmt->execute();
        $accStmt->close();

        // Redirect after successful insertion
        echo "<script>alert('Registration successful!'); window.location.href = '../management-acc.php';</script>";
        exit();
    }

    $stmt->close();
    $connect->close();
}
?>
