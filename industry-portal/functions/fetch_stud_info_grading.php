<?php
session_start();
include_once("../../includes/connection.php");

if(isset($_POST['studentID'])) {    
    $studentID = $_POST['studentID'];

    // Prepare the query
    $query = "SELECT * FROM studentinfo WHERE studentID = ?";
    $stmt = mysqli_prepare($connect, $query);
    
    if ($stmt === false) {
        error_log("MySQL prepare error: " . mysqli_error($connect));
        echo json_encode(array("error" => "Failed to prepare the statement"));
        exit;
    }

    // Bind the parameter
    mysqli_stmt_bind_param($stmt, "s", $studentID);
    
    // Execute the statement
    if (!mysqli_stmt_execute($stmt)) {
        error_log("MySQL execution error: " . mysqli_stmt_error($stmt));
        echo json_encode(array("error" => "Failed to execute query"));
        exit;
    }

    // Get the result
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        // Return the student info as JSON
        $studentInfo = array(
            'studentID' => $row['studentID'],
            'lastName' => $row['lastname'],
            'firstName' => $row['firstname'],
            'middleName' => $row['middlename'],
            'section' => $row['section'],
            'program' => $row['course']
        );
        echo json_encode($studentInfo);
    } else {
        // If no student found, return empty object
        echo json_encode(array());
    }
} else {
    // Handle case where 'studentID' is not provided
    echo json_encode(array("error" => "studentID is required"));
}
?>
