<?php
session_start();
include_once("../../includes/connection.php");

if(isset($_POST['studentID'])) {    
    $studentID = $_POST['studentID'];

    $query = "SELECT * FROM studentinfo WHERE studentID = ?";
    $stmt = mysqli_prepare($connect, $query);
    mysqli_stmt_bind_param($stmt, "s", $studentID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if($row = mysqli_fetch_assoc($result)) {
        
        $studentInfo = array(
            'studentID' => $row['studentID'],
            'lastName' => $row['lastname'],
            'firstName' => $row['firstname'],
            'middleName' => $row['middlename'],
            'section' => $row['section'],
            'program' => $row['course']
        );

        // Convert the array to JSON format and echo it back
        echo json_encode($studentInfo);
    } else {
        // If no student found with the given ID, you can echo an empty JSON object or any other suitable response
        echo json_encode(array());
    }
}
?>