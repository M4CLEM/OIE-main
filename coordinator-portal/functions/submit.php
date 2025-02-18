<?php
session_start();
include_once("../../includes/connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titles = $_POST['title'];
    $percentages = $_POST['percentage'];
    $descriptions = $_POST['description'];

    for ($i = 0; $i < count($titles); $i++) {

        $prog = $_SESSION['program'];
        $title = $titles[$i];
        $percentage = $percentages[$i];
        $description = $descriptions[$i];

        $sql = "INSERT INTO criteria_list (program, criteria, percentage, description) VALUES ('$prog', '$title', '$percentage', '$description')";

        if ($connect->query($sql) === TRUE) {
            header("Location: ../grading-view.php");
        } else {
            echo "Error: " . $sql . "<br>" . $connect->error;
        }
    }

    $connect->close();
}

?>
