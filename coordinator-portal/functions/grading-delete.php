<?php
session_start();
include_once("../../includes/connection.php");

if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $id = mysqli_real_escape_string($connect, $id);
    $query = "DELETE FROM criteria_list WHERE id='$id'";

    if (mysqli_query($connect, $query)) {
        echo 'Record deleted successfully.';
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error deleting record: ' . mysqli_error($connect)]);
    }
} else {
    header("Location:../grading-view.php");
}

mysqli_close($connect);
?>
