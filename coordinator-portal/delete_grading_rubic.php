<?php
require '../includes/connection.php'; // Adjust the path as necessary

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"])) {
    $id = $_POST["id"];

    // Prepare and execute delete query
    $sql = "DELETE FROM grading_rubics WHERE id = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "Success"; // Send success response
    } else {
        echo "Error: " . $stmt->error; // Send error message
    }

    $stmt->close();
    $connect->close();
} else {
    echo "Invalid Request";
}
?>