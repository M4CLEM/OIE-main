<?php
    session_start();
    include("../../includes/connection.php");
    $semester = $_SESSION['semester'];
    $schoolYear = $_SESSION['schoolYear'];

    if (isset($_POST['update'])) {   
        $studentID = $_POST['studentID']; 
        $firstname = $_POST['firstname'];
        $middlename = $_POST['middlename'];
        $lastname = $_POST['lastname'];
        $course = $_POST['course'];
        $status = trim($_POST['status']); // Trim to avoid accidental spaces

        $query = "UPDATE studentinfo SET firstname = ?, middlename = ?, lastname = ?, course = ?, status = ? WHERE studentID = ? AND semester = ? AND school_year = ?";
        $stmt = $connect->prepare($query);

        if ($stmt) {
            $stmt->bind_param("ssssssss", $firstname, $middlename, $lastname, $course, $status, $studentID, $semester, $schoolYear);
            $stmt->execute();

            if ($stmt->affected_rows >= 0) {
                echo '<script>alert("Data Updated");</script>';
                header("Location: ../student-list.php");
                exit();
            } else {
                echo '<script>alert("Data Not Updated");</script>';
            }

            $stmt->close();
        } else {
            echo '<script>alert("Query Preparation Failed");</script>';
        }
    }
?>
