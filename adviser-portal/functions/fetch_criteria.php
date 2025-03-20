<?php
session_start();
include("../../includes/connection.php");

if (isset($_POST['studentID']) && isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    $studentId = $_POST['studentID'];

    // Fetch company and job role for selected student
    $studQuery = "SELECT companyName, jobrole FROM company_info WHERE studentID = ?";
    $stmtStud = $connect->prepare($studQuery);
    $stmtStud->bind_param("s", $studentId);
    $stmtStud->execute();
    $studResult = $stmtStud->get_result();

    if ($studResult->num_rows > 0) {
        $row = $studResult->fetch_assoc();
        $jobrole = $row['jobrole'];
        $companyName = $row['companyName'];

        $adviserCriteriaGrouped = [];

        // Fetch criteria for company and job role
        $criteriaQuery = "SELECT * FROM adviser_criteria WHERE company = ? AND jobrole = ?";
        $stmtCriteria = $connect->prepare($criteriaQuery);
        $stmtCriteria->bind_param("ss", $companyName, $jobrole);
        $stmtCriteria->execute();
        $resultCriteria = $stmtCriteria->get_result();

        while ($row = $resultCriteria->fetch_assoc()) {
            // Log the raw criteria data for debugging
            $rawCriteria = $row['criteria'];
            error_log("Raw criteria: " . $rawCriteria); // Logs raw JSON data for debugging

            $criteriaData = json_decode($rawCriteria, true);

            // Check if criteriaData is an array and contains expected fields
            if (is_array($criteriaData)) {
                foreach ($criteriaData as $adviserCriteriaItem) {
                    // Ensure keys exist before accessing them, with default values
                    $adviserCriteriaGrouped[] = [
                        'id' => $row['id'],
                        'criteria' => isset($adviserCriteriaItem['adviserCriteria']) ? $adviserCriteriaItem['adviserCriteria'] : 'N/A',
                        'percentage' => isset($adviserCriteriaItem['adviserPercentage']) ? $adviserCriteriaItem['adviserPercentage'] : 0,
                        'description' => isset($adviserCriteriaItem['adviserDescription']) ? $adviserCriteriaItem['adviserDescription'] : 'N/A'
                    ];
                }
            } else {
                error_log("Invalid criteria JSON format for ID " . $row['id']); // Log an error if JSON is invalid
            }
        }
    }

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($adviserCriteriaGrouped);
    exit;
}
?>
