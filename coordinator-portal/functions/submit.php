<?php
session_start();
include_once("../../includes/connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $program = $_SESSION['program'];
    $company = $_POST['companyDropdown']; // Get the selected company
    $jobrole = $_POST['jobrole']; // Get the selected job role
    $designation = $_POST['designation'];

    // Check if required data exists
    if (!isset($_POST['title'], $_POST['percentage'], $_POST['description'], $company, $jobrole)) {
        die("Error: Missing required fields.");
    }

    $titles = $_POST['title'];
    $percentages = $_POST['percentage'];
    $descriptions = $_POST['description'];

    $criteriaArray = [];

    for ($i = 0; $i < count($titles); $i++) {
        $criteriaArray[] = [
            "criteria" => $titles[$i],
            "percentage" => $percentages[$i],
            "description" => $descriptions[$i]
        ];
    }

    // Convert array to JSON
    $criteriaJson = json_encode($criteriaArray, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

    // Insert into criteria_list_view
    $sql = "INSERT INTO criteria_list_view (program, criteria, company, jobrole, status, designation) VALUES (?, ?, ?, ?, 'Pending', ?)";
    $stmt = $connect->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sssss", $program, $criteriaJson, $company, $jobrole, $designation);
        if ($stmt->execute()) {
            header("Location: ../grading-view.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $connect->error;
    }

    $connect->close();
}
?>
