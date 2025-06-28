<?php
    session_start();
    include("../../includes/connection.php");

    $semester = $_SESSION['semester'];
    $schoolYear = $_SESSION['schoolYear'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $studentID = $_POST['studentID']; 
        $firstname = $_POST['firstname'];
        $middlename = $_POST['middlename'];
        $lastname = $_POST['lastname'];
        $course = $_POST['course'];
        $status = trim($_POST['status']);

        $query = "UPDATE studentinfo 
              SET firstname = ?, middlename = ?, lastname = ?, course = ?, status = ? 
              WHERE studentID = ? AND semester = ? AND school_year = ?";

        $stmt = $connect->prepare($query);

        if ($stmt) {
            $stmt->bind_param("ssssssss", $firstname, $middlename, $lastname, $course, $status, $studentID, $semester, $schoolYear);
            $stmt->execute();

            if ($stmt->affected_rows >= 0) {
                header("Location: ../student-edit.php?studentID=$studentID&update=success");
                exit();
            } else {
                echo '<script>alert("No changes made.");</script>';
            }

            $stmt->close();
        } else {
            echo '<script>alert("Query Preparation Failed: ' . $connect->error . '");</script>';
        }
    }
?>
