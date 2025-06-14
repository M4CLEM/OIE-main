<?php
session_start();
include_once('../../includes/connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['department'])) {
        die("Error: Program not set in session.");
    }

    $department = $_SESSION['department'];
    $titles = htmlspecialchars($_POST['addTitle'], ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars($_POST['addDescription'], ENT_QUOTES, 'UTF-8');

    $sql = "INSERT INTO criteria_presets (department, criteria, description) VALUES (?, ?, ?)";
    $stmt = $connect->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sss", $department, $titles, $description);
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
