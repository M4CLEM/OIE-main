<?php
session_start();
include_once("../includes/connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = $_POST['fullName'];
    $email = $_POST['email'];
    $sectionRaw = $_POST['section']; // Comma-separated section IDs
    $dept = $_POST['dept'];
    $course = $_POST['course'];
    $semester = $_POST['semester'];
    $schoolYear = $_POST['schoolYear'];
    $employeeNumber = $_POST['employeeNumber'];

    $role = "Adviser";
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm'];

    // Validate password confirmation
    if ($password !== $confirmPassword) {
        echo '<div class="alert alert-danger" role="alert">Passwords do not match. Please try again.</div>';
    } else {
        // Sanitize section input
        $sections = array_filter(array_map('trim', explode(',', $sectionRaw)));
        $sectionString = implode(',', $sections); // This will be stored in one column

        // Check if the email already exists in the users table
        $checkUserExists = $connect->prepare('SELECT COUNT(*) FROM users WHERE username = ?');
        $checkUserExists->bind_param('s', $email);
        $checkUserExists->execute();
        $checkUserExists->bind_result($userCount);
        $checkUserExists->fetch();
        $checkUserExists->close();

        // Insert values into listadviser (only once)
        $addToList = $connect->prepare('INSERT INTO listadviser (employeeNumber, fullName, email, section, course, dept, semester, schoolYear) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $addToList->bind_param('ssssssss', $employeeNumber, $fullName, $email, $sectionString, $course, $dept, $semester, $schoolYear);
        $addToList->execute();
        $addToList->close();

        // Insert into users table only if they don't exist yet
        if ($userCount == 0) {
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
