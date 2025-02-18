<?php
session_start();
include_once("../includes/connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = $_POST['fullName'];
    $email = $_POST['email'];
    $section = $_POST['section'];
    $dept = $_POST['dept'];
    $course = $_POST['course'];

    $role = "Adviser";
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm'];

    // Validate password confirmation
    if ($password !== $confirmPassword) {
        echo '<div class="alert alert-danger" role="alert">Passwords do not match. Please try again.</div>';
        // You can customize the error message or style as needed.
    } else {
        // Check if the email already exists in the users table
        $checkUserExists = $connect->prepare('SELECT COUNT(*) FROM users WHERE username = ?');
        $checkUserExists->bind_param('s', $email);
        $checkUserExists->execute();
        $checkUserExists->bind_result($userCount);
        $checkUserExists->fetch();
        $checkUserExists->close();

        if ($userCount > 0) {
            // Insert values into Adviser List
            $addToList = $connect->prepare('INSERT INTO listadviser (fullName, email, section, course, dept) VALUES (?, ?, ?, ?, ?)');
            $addToList->bind_param('sssss', $fullName, $email, $section, $course, $dept);
            $addToList->execute();
            $addToList->close();
        } else {
            // Insert values into Adviser List
            $addToList = $connect->prepare('INSERT INTO listadviser (fullName, email, section, course, dept) VALUES (?, ?, ?, ?, ?)');
            $addToList->bind_param('sssss', $fullName, $email, $section, $course, $dept);
            $addToList->execute();
            $addToList->close();

            // Insert values into Users table
            $addToUsers = $connect->prepare('INSERT INTO users (username, role, password) VALUES (?, ?, ?)');
            $addToUsers->bind_param('sss', $email, $role, $password);
            $addToUsers->execute();
            $addToUsers->close();
        }

        header("Location: advisers.php");
    exit;
    }
}
?>
