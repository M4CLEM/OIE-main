<?php
session_start();
include_once("../../includes/connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = trim($_POST['id']);
    
    // Get the email associated with the staff ID before deleting
    $emailQuery = "SELECT email FROM staff_list WHERE id = ?";
    $emailStmt = $connect->prepare($emailQuery);
    $emailStmt->bind_param("i", $id);
    $emailStmt->execute();
    $emailStmt->bind_result($email);
    $emailStmt->fetch();
    $emailStmt->close();

    if (empty($email)) {
        echo "<script>alert('Error: Staff not found.'); window.history.back();</script>";
        exit();
    }
    
    // Delete from staff_list table
    $deleteStaffQuery = "DELETE FROM staff_list WHERE id = ?";
    $deleteStaffStmt = $connect->prepare($deleteStaffQuery);
    $deleteStaffStmt->bind_param("i", $id);
    
    if ($deleteStaffStmt->execute()) {
        $deleteStaffStmt->close();
        
        // Delete from users table using email
        $deleteUserQuery = "DELETE FROM users WHERE username = ?";
        $deleteUserStmt = $connect->prepare($deleteUserQuery);
        $deleteUserStmt->bind_param("s", $email);
        $deleteUserStmt->execute();
        $deleteUserStmt->close();
        
        echo "<script>alert('Staff deleted successfully.'); window.location.href = '../management-acc.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error deleting staff.'); window.history.back();</script>";
    }
    
    $deleteStaffStmt->close();
    $connect->close();
}
?>
