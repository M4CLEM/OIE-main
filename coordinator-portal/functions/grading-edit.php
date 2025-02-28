<?php 
session_start();
include_once("../../includes/connection.php"); // Ensure correct database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['editId']) || empty($_POST['editId'])) {
        die("Error: No ID provided.");
    }

    $id = $_POST['editId'];

    // Handle Company Criteria Update
    if (isset($_POST['companyCriteria'], $_POST['companyPercentage'], $_POST['companyDescription'])) {
        $companyTitles = $_POST['companyCriteria']; 
        $companyPercentages = $_POST['companyPercentage'];
        $companyDescriptions = $_POST['companyDescription']; 

        if (empty($companyTitles) || empty($companyPercentages) || empty($companyDescriptions)) {
            die("Error: One or more fields in Company Criteria are empty.");
        }

        $companyUpdatedCriteria = [];

        for ($i = 0; $i < count($companyTitles); $i++) {
            if (empty($companyTitles[$i]) || empty($companyPercentages[$i]) || empty($companyDescriptions[$i])) {
                die("Error: All Company fields must be filled.");
            }

            $companyUpdatedCriteria[] = [
                'companyCriteria' => mysqli_real_escape_string($connect, $companyTitles[$i]),
                'companyPercentage' => (int) $companyPercentages[$i], 
                'companyDescription' => mysqli_real_escape_string($connect, $companyDescriptions[$i])
            ];
        }

        $companyUpdatedCriteriaJson = json_encode($companyUpdatedCriteria);

        $query = "UPDATE criteria_list_view SET criteria = ? WHERE id = ?";
        $stmt = mysqli_prepare($connect, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "si", $companyUpdatedCriteriaJson, $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        } else {
            die("Error: SQL statement preparation failed for Company Criteria. " . mysqli_error($connect));
        }
    }

    // Handle Adviser Criteria Update
    if (isset($_POST['adviserCriteria'], $_POST['adviserPercentage'], $_POST['adviserDescription'])) {
        $adviserTitles = $_POST['adviserCriteria']; 
        $adviserPercentages = $_POST['adviserPercentage'];
        $adviserDescriptions = $_POST['adviserDescription']; 

        if (empty($adviserTitles) || empty($adviserPercentages) || empty($adviserDescriptions)) {
            die("Error: One or more fields in Adviser Criteria are empty.");
        }

        $adviserUpdatedCriteria = [];

        for ($i = 0; $i < count($adviserTitles); $i++) {
            if (empty($adviserTitles[$i]) || empty($adviserPercentages[$i]) || empty($adviserDescriptions[$i])) {
                die("Error: All Adviser fields must be filled.");
            }

            $adviserUpdatedCriteria[] = [
                'adviserCriteria' => mysqli_real_escape_string($connect, $adviserTitles[$i]),
                'adviserPercentage' => (int) $adviserPercentages[$i], 
                'adviserDescription' => mysqli_real_escape_string($connect, $adviserDescriptions[$i])
            ];
        }

        $adviserUpdatedCriteriaJson = json_encode($adviserUpdatedCriteria);

        $query = "UPDATE adviser_criteria SET criteria = ? WHERE id = ?";
        $stmt = mysqli_prepare($connect, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "si", $adviserUpdatedCriteriaJson, $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        } else {
            die("Error: SQL statement preparation failed for Adviser Criteria. " . mysqli_error($connect));
        }
    }

    echo '<script> alert("Data Updated Successfully"); window.location.href="../grading-view.php"; </script>';
}

// Fetch Company Criteria
echo "<h3>Company Criteria List</h3>";
$query = "SELECT criteria FROM criteria_list_view WHERE id = ?";
$stmt = mysqli_prepare($connect, $query);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $criteriaJson = $row['criteria'] ?? "";

        $criteriaArray = json_decode($criteriaJson, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($criteriaArray)) {
            echo "<p>Error: Invalid JSON format in Company Criteria.</p>";
            continue;
        }

        echo "<ul>";
        foreach ($criteriaArray as $criteriaItem) {
            $title = htmlspecialchars($criteriaItem['companyCriteria'] ?? "No Title");
            $percentage = (int) ($criteriaItem['companyPercentage'] ?? 0);
            $description = htmlspecialchars($criteriaItem['companyDescription'] ?? "No Description");
            echo "<li><strong>$title</strong> - $percentage%</li>";
            echo "<li>$description</li>";
        }
        echo "</ul>";
    }
    mysqli_stmt_close($stmt);
} else {
    die("Error: SQL query preparation failed for Company Criteria. " . mysqli_error($connect));
}

// Fetch Adviser Criteria
echo "<h3>Adviser Criteria List</h3>";
$query = "SELECT criteria FROM adviser_criteria WHERE id = ?";
$stmt = mysqli_prepare($connect, $query);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $criteriaJson = $row['criteria'] ?? "";

        $criteriaArray = json_decode($criteriaJson, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($criteriaArray)) {
            echo "<p>Error: Invalid JSON format in Adviser Criteria.</p>";
            continue;
        }

        echo "<ul>";
        foreach ($criteriaArray as $criteriaItem) {
            $title = htmlspecialchars($criteriaItem['adviserCriteria'] ?? "No Title");
            $percentage = (int) ($criteriaItem['adviserPercentage'] ?? 0);
            $description = htmlspecialchars($criteriaItem['adviserDescription'] ?? "No Description");
            echo "<li><strong>$title</strong> - $percentage%</li>";
            echo "<li>$description</li>";
        }
        echo "</ul>";
    }
    mysqli_stmt_close($stmt);
} else {
    die("Error: SQL query preparation failed for Adviser Criteria. " . mysqli_error($connect));
}

?>
