<?php
session_start();
include_once("../includes/connection.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['criteriaData']) && is_string($_POST['criteriaData'])) {
        $criteriaData = json_decode($_POST['criteriaData'], true);
        if ($criteriaData === null) {
            echo json_encode(array('status' => 'error', 'message' => 'Error: criteriaData is not a valid JSON string.'));
            exit;
        }
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Error: criteriaData is not set or is not a string.'));
        exit;
    }

    $studentId = $_POST['studentId'];

    $sql = "SELECT * FROM studentinfo WHERE studentID = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $email = $row['email'];
    $stmt->close(); // Close the statement

    

    $success = true; // Variable to track if all criteria were inserted successfully

    // Prepare the INSERT statement outside the loop
    $sql = "INSERT INTO student_grade (studentID, criteria, grade, email) VALUES (?, ?, ?, ?)";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("ssss", $studentId, $criteriaText, $grade, $email);

    foreach ($criteriaData as $criteriaId => $data) {
        $criteriaText = $data['text'];
        $grade = $data['grade'];

        // Execute the prepared statement for each criterion
        if (!$stmt->execute()) {
            $success = false; // If execution fails for any criterion, set success to false
            break; // Exit the loop immediately
        }
    }

    if ($success) {
        $sql = "UPDATE studentinfo SET status = 'Completed' WHERE studentID = ?";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("i", $studentId);
    
        if ($stmt->execute()) {
            echo json_encode(array('status' => 'success', 'message' => 'Grade submitted successfully!'));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Error updating status.'));
        }
    
        $stmt->close();
        
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Error submitting grade.'));
    }
    
    exit;
    $stmt->close(); // Close the statement

}

?>