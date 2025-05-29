<?php
include_once("../../includes/connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];

    $query = "DELETE FROM users WHERE id = '$id'";
    if (mysqli_query($connect, $query)) {
        echo "success";
    } else {
        http_response_code(500);
        echo "Error deleting account.";
    }
} else {
    http_response_code(400);
    echo "Invalid request.";
}
?>
