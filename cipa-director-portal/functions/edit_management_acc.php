<?php
session_start();
include_once("../../includes/connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = trim($_POST['id']); // Staff ID
    $name = trim($_POST['editStaffName']);
    $email = trim($_POST['editEmail']);
    $password = trim($_POST['editPassword']);
    $confirmPassword = trim($_POST['editConfirmPassword']);
    $role = trim($_POST['editRole']);
    
    // Validate passwords if changed
    if (!empty($password) && $password !== $confirmPassword) {
        echo "<script>alert('Error: Passwords do not match.'); window.history.back();</script>";
        exit();
    }

    // Get current email before updating
    $currentEmailQuery = "SELECT email FROM staff_list WHERE id = ?";
    $currentEmailStmt = $connect->prepare($currentEmailQuery);
    $currentEmailStmt->bind_param("i", $id);
    $currentEmailStmt->execute();
    $currentEmailStmt->bind_result($currentEmail);
    $currentEmailStmt->fetch();
    $currentEmailStmt->close();

    // Check if email already exists for another staff member
    $emailCheckQuery = "SELECT id FROM staff_list WHERE email = ? AND id != ? UNION SELECT id FROM users WHERE username = ? AND username != ?";
    $emailCheckStmt = $connect->prepare($emailCheckQuery);
    $emailCheckStmt->bind_param("ssss", $email, $id, $email, $currentEmail);
    $emailCheckStmt->execute();
    $emailCheckStmt->store_result();

    if ($emailCheckStmt->num_rows > 0) {
        echo "<script>alert('Error: Email is already in use.'); window.history.back();</script>";
        exit();
    }
    $emailCheckStmt->close();

    // Update query based on role
    if ($role === "CIPA") {
        $sql = "UPDATE staff_list SET name = ?, email = ?, role = ? WHERE id = ?";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("sssi", $name, $email, $role, $id);
    } else {
        $department = trim($_POST['editDepartment']);
        $sql = "UPDATE staff_list SET name = ?, email = ?, role = ?, department = ? WHERE id = ?";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("ssssi", $name, $email, $role, $department, $id);
    }

    if ($stmt->execute()) {
        // Ensure username update in users table
        $updateUsernameStmt = $connect->prepare("UPDATE users SET username = ? WHERE username = ?");
        $updateUsernameStmt->bind_param("ss", $email, $currentEmail);
        $updateUsernameStmt->execute();
        $updateUsernameStmt->close();

        // Update role and department if needed
        if ($role === "CIPA") {
            $accStmt = $connect->prepare("UPDATE users SET role = ? WHERE username = ?");
            $accStmt->bind_param("ss", $role, $email);
        } else {
            $accStmt = $connect->prepare("UPDATE users SET role = ?, department = ? WHERE username = ?");
            $accStmt->bind_param("sss", $role, $department, $email);
        }
        $accStmt->execute();
        $accStmt->close();

        // Update password if changed
        if (!empty($password)) {
            $passStmt = $connect->prepare("UPDATE staff_list SET password = ? WHERE id = ?");
            $passStmt->bind_param("si", $password, $id);
            $passStmt->execute();
            $passStmt->close();

            $passUserStmt = $connect->prepare("UPDATE users SET password = ? WHERE username = ?");
            $passUserStmt->bind_param("ss", $password, $email);
            $passUserStmt->execute();
            $passUserStmt->close();
        }

        echo "<script>alert('Update successful!'); window.location.href = '../management-acc.php';</script>";
        exit();
    }

    $stmt->close();
    $connect->close();
}
?>
