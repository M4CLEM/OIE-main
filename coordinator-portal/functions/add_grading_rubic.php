<?php
    session_start();
    include_once('../../includes/connection.php');

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!isset($_SESSION['department'])) {
            die("Error: Program not set in session.");
        }

        $department = $_SESSION['department'];
        $adviserGradingWeight = $_POST['adviserWeight'];
        $companyGradingWeight = $_POST['companyWeight'];
        $semester = htmlspecialchars($_POST['semester'], ENT_QUOTES, 'UTF-8');
        $schoolYear = htmlspecialchars($_POST['schoolYear'], ENT_QUOTES, 'UTF-8');

        $sqlQuery = "INSERT INTO grading_rubics (adviserWeight, companyWeight, department, semester, schoolYear) VALUES (?, ?, ?, ?, ?)";
        $stmt = $connect->prepare($sqlQuery);

        if ($stmt) {
            $stmt->bind_param("iisss", $adviserGradingWeight, $companyGradingWeight, $department, $semester, $schoolYear);
            if ($stmt->execute()) {
                header("Location: ../grading-rubics.php");
                exit();
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