<?php 
session_start();
include_once("../../includes/connection.php"); // Ensure correct database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['editId']) || empty($_POST['editId'])) {
        die("Error: No ID provided.");
    }
    
    $id = $_POST['editId'];
    
    if (!isset($_POST['criteria'], $_POST['percentage'])) {
        die("Error: No data received for update.");
    }

    $titles = $_POST['criteria']; 
    $percentages = $_POST['percentage']; 

    if (empty($titles) || empty($percentages)) {
        die("Error: One or more fields are empty.");
    }

    $updatedCriteria = [];

    for ($i = 0; $i < count($titles); $i++) {
        if (empty($titles[$i]) || empty($percentages[$i])) {
            die("Error: All fields must be filled.");
        }

        $updatedCriteria[] = [
            'criteria' => mysqli_real_escape_string($connect, $titles[$i]),
            'percentage' => (int) $percentages[$i] // Convert percentage to integer
        ];
    }

    $updatedCriteriaJson = json_encode($updatedCriteria);

    $query = "UPDATE criteria_list_view SET criteria = ? WHERE id = ?";
    $stmt = mysqli_prepare($connect, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "si", $updatedCriteriaJson, $id);
        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            echo '<script> alert("Data Updated Successfully"); window.location.href="../grading-view.php"; </script>';
        } else {
            die("Error: Data not updated. " . mysqli_error($connect));
        }

        mysqli_stmt_close($stmt);
    } else {
        die("Error: SQL statement preparation failed. " . mysqli_error($connect));
    }
}

// Fetch criteria and safely decode JSON
echo "<h3>Criteria List</h3>";
$query = "SELECT criteria FROM criteria_list_view WHERE id = ?";
$stmt = mysqli_prepare($connect, $query);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $criteriaJson = $row['criteria'] ?? "";
        
        // Decode JSON safely
        $criteriaArray = json_decode($criteriaJson, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($criteriaArray)) {
            echo "<p>Error: Invalid JSON format.</p>";
            continue;
        }
        
        // Display criteria properly
        echo "<ul>";
        foreach ($criteriaArray as $criteriaItem) {
            $title = isset($criteriaItem['criteria']) ? htmlspecialchars($criteriaItem['criteria']) : "No Title";
            $percentage = isset($criteriaItem['percentage']) ? (int) $criteriaItem['percentage'] : 0;
            echo "<li><strong>$title</strong> - $percentage%</li>";
        }
        echo "</ul>";
    }
    mysqli_stmt_close($stmt);
} else {
    die("Error: SQL query preparation failed. " . mysqli_error($connect));
}
?>
