<?php
    include_once("../../includes/connection.php");
    session_start();

    $activeSemester = $_SESSION['semester'];
    $activeSchoolYear = $_SESSION['schoolYear'];
    $companyName = $_SESSION['companyName'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['studentID'])) {
        $studentID = $_POST['studentID'];
        $applicationID = $_POST['applicationID'];

        $rejectionQuery = "UPDATE applications SET status = 'Rejected' WHERE studentID = ? AND applicationID = ? AND semester = ? AND schoolYear = ?";
        $rejectionStmt = $connect->prepare($rejectionQuery);
        $rejectionStmt->bind_param("ssss", $studentID, $applicationID, $activeSemester, $activeSchoolYear);
        
        if ($rejectionStmt->execute()) {
            echo "Application rejected successfully.";
        } else {
            echo "Failed to reject application.";
        }

        $rejectionStmt->close();
        
    } else {
        echo "Invalid request.";
    }

    $connect->close();
?>