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

    $companyCriteriaGrouped = [];
    $gradeData = null; // Default value

    if ($studResult->num_rows > 0) {
        $row = $studResult->fetch_assoc();
        $jobrole = $row['jobrole'];
        $companyName = $row['companyName'];

        // Fetch criteria for company and job role
        $criteriaQuery = "SELECT * FROM criteria_list_view WHERE company = ? AND jobrole = ?";
        $stmtCriteria = $connect->prepare($criteriaQuery);
        $stmtCriteria->bind_param("ss", $companyName, $jobrole);
        $stmtCriteria->execute();
        $resultCriteria = $stmtCriteria->get_result();

        while ($row = $resultCriteria->fetch_assoc()) {
            $rawCriteria = $row['criteria'];
            error_log("Raw criteria: " . $rawCriteria);

            $criteriaData = json_decode($rawCriteria, true);

            if (is_array($criteriaData)) {
                foreach ($criteriaData as $companyCriteriaItem) {
                    $companyCriteriaGrouped[] = [
                        'id' => $row['id'],
                        'criteria' => $companyCriteriaItem['companyCriteria'] ?? 'N/A',
                        'percentage' => $companyCriteriaItem['companyPercentage'] ?? 0,
                        'description' => $companyCriteriaItem['companyDescription'] ?? 'N/A'
                    ];
                }
            } else {
                error_log("Invalid criteria JSON format for ID " . $row['id']);
            }
        }

        // Check if the student's grade exists in company_student_grade
        $gradeQuery = "SELECT grade, finalGrade FROM student_grade WHERE studentID = ?";
        $stmtGrade = $connect->prepare($gradeQuery);
        $stmtGrade->bind_param("s", $studentId);
        $stmtGrade->execute();
        $gradeResult = $stmtGrade->get_result();

        if ($gradeResult->num_rows > 0) {
            $gradeRow = $gradeResult->fetch_assoc();
            $gradeJson = json_decode($gradeRow['grade'], true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($gradeJson)) {
                // Ensure the JSON structure is preserved
                $gradeData = [
                    'grades' => $gradeJson, // Example: { "Work Ethics": 30 }
                    'finalGrade' => $gradeRow['finalGrade']
                ];
            } else {
                error_log("Invalid grade JSON format for student ID " . $studentId);
                $gradeData = [
                    'grades' => [],
                    'finalGrade' => $gradeRow['finalGrade']
                ];
            }
        }
    }
    
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode([
        'companyName' => $companyName,
        'jobrole' => $jobrole,
        'criteria' => $companyCriteriaGrouped,
        'gradeData' => $gradeData
    ]);
    exit;
}
?>
