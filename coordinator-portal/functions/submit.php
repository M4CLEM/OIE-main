<?php
session_start();
include_once("../../includes/connection.php");

$activeSemester = $_SESSION['semester'];
$activeSchoolYear = $_SESSION['schoolYear'];

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $department = $_SESSION['department'];
    $coordinatorRole = $_SESSION['coordinator']; // Get coordinator role
    $company = isset($_POST['companyDropdown']) ? trim($_POST['companyDropdown']) : ''; // Ensure company is not NULL
    $jobrole = isset($_POST['jobrole']) ? trim($_POST['jobrole']) : '';

    // Function to check if criteria already exist
    function criteriaExists($connect, $table, $department, $companyName, $jobRole, $activeSemester, $activeSchoolYear) {
        $query = $connect->prepare("SELECT 1 FROM $table WHERE department = ? AND company = ? AND jobrole = ? AND semester = ? AND schoolYear = ? LIMIT 1");
        $query->bind_param("sssss", $department, $companyName, $jobRole, $activeSemester, $activeSchoolYear);
        $query->execute();
        $query->store_result();
        $exists = $query->num_rows > 0;
        $query->close();
        return $exists;
    }

    // Check if required data exists for company criteria
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

        if (empty($company)) { // Apply to companies within the same department
            $companyQuery = $connect->prepare("SELECT companyName, jobrole FROM companylist WHERE dept = ? AND semester = ? AND schoolYear = ?");
            $companyQuery->bind_param("sss", $department, $activeSemester, $activeSchoolYear);
            $companyQuery->execute();
            $result = $companyQuery->get_result();

            while ($row = $result->fetch_assoc()) {
                $companyName = trim($row['companyName']);
                $jobRole = trim($row['jobrole']);

                // Skip if criteria already exist
                if (criteriaExists($connect, "criteria_list_view", $department,$companyName, $jobRole, $activeSemester, $activeSchoolYear)) {
                    error_log("Skipping: Criteria already exist for company=$companyName, jobrole=$jobRole");
                    continue;
                }

                if (!empty($companyName) && !empty($jobRole)) {
                    $stmt = $connect->prepare("INSERT INTO criteria_list_view (department, criteria, company, jobrole, semester, schoolYear) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssss", $department, $companyCriteriaJson, $companyName, $jobRole, $activeSemester, $activeSchoolYear);
                    $stmt->execute();
                    $stmt->close();
                }
            }
            $companyQuery->close();
        } else { // Insert for selected company and job role
            if (!criteriaExists($connect, "criteria_list_view", $department, $company, $jobrole, $activeSemester, $activeSchoolYear)) {
                $stmt = $connect->prepare("INSERT INTO criteria_list_view (department, criteria, company, jobrole, semester, schoolYear) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssss", $department, $companyCriteriaJson, $company, $jobrole, $activeSemester, $activeSchoolYear);
                $stmt->execute();
                $stmt->close();
            } else {
                error_log("Skipping: Selected company ($company) already has criteria.");
            }
        }
    }

    // Check if required data exists for adviser criteria
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

        if (empty($company)) { // Apply to companies within the same department
            $companyQuery = $connect->prepare("SELECT companyName, jobrole FROM companylist WHERE dept = ? AND semester = ? AND schoolYear = ?");
            $companyQuery->bind_param("sss", $department, $activeSemester, $activeSchoolYear);
            $companyQuery->execute();
            $result = $companyQuery->get_result();

            while ($row = $result->fetch_assoc()) {
                $companyName = trim($row['companyName']);
                $jobRole = trim($row['jobrole']);

                // Skip if criteria already exist
                if (criteriaExists($connect, "adviser_criteria", $department,$companyName, $jobRole, $activeSemester, $activeSchoolYear)) {
                    error_log("Skipping: Adviser criteria already exist for company=$companyName, jobrole=$jobRole");
                    continue;
                }

                if (!empty($companyName) && !empty($jobRole)) {
                    $stmt = $connect->prepare("INSERT INTO adviser_criteria (department, criteria, company, jobrole, semester, schoolYear) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssss", $department, $adviserCriteriaJson, $companyName, $jobRole, $activeSemester, $activeSchoolYear);
                    $stmt->execute();
                    $stmt->close();
                }
            }
            $companyQuery->close();
        } else { // Insert for selected company and job role
            if (!criteriaExists($connect, "adviser_criteria", $department,$company, $jobrole, $activeSemester, $activeSchoolYear)) {
                $stmt = $connect->prepare("INSERT INTO adviser_criteria (department, criteria, company, jobrole, semester, schoolYear) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssss", $department, $adviserCriteriaJson, $company, $jobrole, $activeSemester, $activeSchoolYear);
                $stmt->execute();
                $stmt->close();
            } else {
                error_log("Skipping: Selected company ($company) already has adviser criteria.");
            }
        }
    }

    $connect->close();
    header("Location: ../grading-view.php");
    exit();
}
