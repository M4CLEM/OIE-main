<?php
// Include database connection
include_once("../includes/connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $id = $_POST['id']; // The hidden ID field
    $adviserWeight = $_POST['adviserWeight'];
    $companyWeight = $_POST['companyWeight'];
    $semester = $_POST['semester'];
    $schoolYear = $_POST['schoolYear'];

    // Validate inputs
    if (empty($id) || empty($adviserWeight) || empty($companyWeight) || empty($semester) || empty($schoolYear)) {
        echo "Error: All fields are required!";
        exit;
    }

    // Ensure values are within the correct range
    if ($adviserWeight < 0 || $adviserWeight > 100 || $companyWeight < 0 || $companyWeight > 100) {
        echo "Error: Weights must be between 0 and 100!";
        exit;
    }

    // Prepare SQL query
    $sql = "UPDATE grading_rubics 
            SET adviserWeight = ?, companyWeight = ?, semester = ?, schoolYear = ? 
            WHERE id = ?";
    
    if ($stmt = mysqli_prepare($connect, $sql)) {
        mysqli_stmt_bind_param($stmt, "iissi", $adviserWeight, $companyWeight, $semester, $schoolYear, $id);
        
        // Execute query
        if (mysqli_stmt_execute($stmt)) {
            echo "Success: Grading rubric updated!";
        } else {
            echo "Error: Could not update record.";
        }

        // Close statement
        mysqli_stmt_close($stmt);
    } else {
        echo "Error: Database query failed.";
    }

    // Close database connection
    mysqli_close($connect);
}
?>
