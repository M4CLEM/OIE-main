<?php
    header('Content-Type: application/json');
    session_start();
    include_once("../../includes/connection.php");

    // Check for POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Retrieve and sanitize inputs
        $id = intval($_POST['id']);
        $jobrole = trim($_POST['editjobrole']);
        $address = trim($_POST['editAddress']);
        $contactPerson = trim($_POST['editContactPerson']);
        $workType = trim($_POST['editWorkType']);
        $department = trim($_POST['editDepartment']);
        $jobDescription = trim($_POST['editJobDescription']);
        $jobRequirements = trim($_POST['editJobRequirements']);
        $link = trim($_POST['editLink']);

        // Validate ID
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid ID']);
            exit;
        }

        // Prepare update statement
        $stmt = $connect->prepare("UPDATE companylist SET 
            companyaddress = ?, 
            contactPerson = ?, 
            jobrole = ?, 
            workType = ?, 
            jobdescription = ?, 
            jobreq = ?, 
            link = ?, 
            dept = ? 
            WHERE No = ?");

        if ($stmt) {
            $stmt->bind_param("ssssssssi", $address, $contactPerson, $jobrole, $workType, $jobDescription, $jobRequirements, $link, $department, $id);

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
