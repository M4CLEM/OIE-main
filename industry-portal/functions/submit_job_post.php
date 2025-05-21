<?php
    ini_set('display_errors', 0); // Disable error output to browser
    ini_set('log_errors', 1);
    error_reporting(E_ALL);

    header('Content-Type: application/json');

    include_once("../../includes/connection.php");
    session_start();

    $activeSemester = $_SESSION['semester'];
    $activeSchoolYear = $_SESSION['schoolYear'];
    $companyName = $_SESSION['companyName'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Sanitize input
        $jobrole = trim($_POST['jobrole']);
        $address = trim($_POST['address']);
        $contactPerson = trim($_POST['contactPerson']);
        $workType = trim($_POST['workType']);
        $department = trim($_POST['department']);
        $jobDescription = trim($_POST['jobDescription']);
        $jobRequirements = trim($_POST['jobRequirements']);
        $link = trim($_POST['link']);

        // Prepare statement
        $stmt = $connect->prepare("INSERT INTO companylist 
            (companyName, companyaddress, contactPerson, jobrole, workType, jobdescription, jobreq, link, dept, semester, schoolYear)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        if ($stmt) {
            $stmt->bind_param("sssssssssss", $companyName, $address, $contactPerson, $jobrole, $workType, $jobDescription, $jobRequirements, $link, $department, $activeSemester, $activeSchoolYear);
        
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Execution failed: ' . $stmt->error]);
            }

            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Statement preparation failed: ' . $connect->error]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    }
?>
