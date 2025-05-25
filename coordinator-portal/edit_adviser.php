<?php
session_start();
include_once("../includes/connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $fullName = $_POST['editFullName'];
    $newEmail = $_POST['editEmail'];
    $sectionRaw = $_POST['section'];
    $dept = $_POST['dept'];
    $course = $_POST['course'];
    $semester = $_POST['editSemester'];
    $schoolYear = $_POST['editSchoolYear'];
    $password = $_POST['editPassword'];
    $confirmPassword = $_POST['confirmEdit'];
    $employeeNumber = $_POST['editEmployeeNumber'];

    // Validate password confirmation
    if ($password !== $confirmPassword) {
        echo '<div class="alert alert-danger" role="alert">Passwords do not match. Please try again.</div>';
        exit;
    }

    // Sanitize section input
    $sections = array_filter(array_map('trim', explode(',', $sectionRaw)));
    $sectionString = implode(',', $sections);

    // Fetch old email based on adviser ID
    $oldEmail = '';
    $stmt = $connect->prepare("SELECT email FROM listadviser WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($oldEmail);
    $stmt->fetch();
    $stmt->close();

    // Update listadviser table
    $updateAdviser = $connect->prepare("UPDATE listadviser SET employeeNumber = ?, fullName = ?, email = ?, section = ?, course = ?, dept = ?, semester = ?, schoolYear = ? WHERE id = ?");
    $updateAdviser->bind_param("ssssssssi", $employeeNumber, $fullName, $newEmail, $sectionString, $course, $dept, $semester, $schoolYear, $id);
    $updateAdviser->execute();
    $updateAdviser->close();

    // Update users table using old email
    $updateUser = $connect->prepare("UPDATE users SET username = ?, password = ? WHERE username = ?");
    $updateUser->bind_param("sss", $newEmail, $password, $oldEmail);
    $updateUser->execute();
    $updateUser->close();

    header("Location: advisers.php");
    exit;
}
?>
