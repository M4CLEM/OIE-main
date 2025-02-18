<?php
session_start();
include_once("../../includes/connection.php");

if (isset($_POST['documentIDs'])) {
    $documentIDs = $_POST['documentIDs'];

    // Update the status of each selected document
    foreach ($documentIDs as $documentID) {
        $query = "UPDATE documents SET status = 'Approved' WHERE id = ?";
        $stmt = mysqli_prepare($connect, $query);
        mysqli_stmt_bind_param($stmt, "i", $documentID);
        mysqli_stmt_execute($stmt);
    }

    // Echo a success message or any necessary response
    echo 'Documents approved successfully';
} else {
    // Echo an error message if no document IDs are provided
    echo 'No documents selected for approval';
}

$connect->close();
?>