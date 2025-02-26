<?php
session_start();
include_once("../../includes/connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $program = $_SESSION['program'];
    $company = $_POST['companyDropdown'];
    $jobrole = $_POST['jobrole'];

    // Check if required data exists for company
    if (isset($_POST['companyTitle'], $_POST['companyPercentage'], $_POST['companyDescription'])) {
        $titles = $_POST['companyTitle'];
        $percentages = $_POST['companyPercentage'];
        $descriptions = $_POST['companyDescription'];

        $companyCriteriaArray = [];
        for ($i = 0; $i < count($titles); $i++) {
            $companyCriteriaArray[] = [
                "companyCriteria" => $titles[$i],
                "companyPercentage" => $percentages[$i],
                "companyDescription" => $descriptions[$i]
            ];
        }

        $companyCriteriaJson = json_encode($companyCriteriaArray, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        $sql = "INSERT INTO criteria_list_view (program, criteria, company, jobrole, status) VALUES (?, ?, ?, ?, 'Pending')";
        $stmt = $connect->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ssss", $program, $companyCriteriaJson, $company, $jobrole);
            $stmt->execute();
            $stmt->close();
        } else {
            echo "Error preparing company statement: " . $connect->error;
        }
    }

    // Check if required data exists for adviser
    if (isset($_POST['adviserTitle'], $_POST['adviserPercentage'], $_POST['adviserDescription'])) {
        $titles = $_POST['adviserTitle'];
        $percentages = $_POST['adviserPercentage'];
        $descriptions = $_POST['adviserDescription'];

        $adviserCriteriaArray = [];
        for ($i = 0; $i < count($titles); $i++) {
            $adviserCriteriaArray[] = [
                "adviserCriteria" => $titles[$i],
                "adviserPercentage" => $percentages[$i],
                "adviserDescription" => $descriptions[$i]
            ];
        }

        $adviserCriteriaJson = json_encode($adviserCriteriaArray, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        $sql = "INSERT INTO adviser_criteria (program, criteria, company, jobrole, status) VALUES (?, ?, ?, ?, 'Pending')";
        $stmt = $connect->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ssss", $program, $adviserCriteriaJson, $company, $jobrole);
            $stmt->execute();
            $stmt->close();
        } else {
            echo "Error preparing adviser statement: " . $connect->error;
        }
    }

    $connect->close();
    header("Location: ../grading-view.php");
    exit();
}
?>
