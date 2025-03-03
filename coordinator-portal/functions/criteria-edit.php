<?php
session_start();
include_once('../../includes/connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['id'])) {
        die("Error: No criteria ID provided.");
    }

    $id = intval($_POST['id']); // Ensure ID is an integer
    $titles = htmlspecialchars($_POST['editTitle'], ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars($_POST['editDescription'], ENT_QUOTES, 'UTF-8');

    $sql = "UPDATE criteria_presets SET criteria = ?, description = ? WHERE id = ?";
    $stmt = $connect->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssi", $titles, $description, $id);
        if ($stmt->execute()) {
            header("Location: ../criteria-presets.php");
            exit(); // Ensure script stops executing after redirection
        } else {
            echo "Error executing statement: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $connect->error;
    }

    $connect->close();
}
?>
