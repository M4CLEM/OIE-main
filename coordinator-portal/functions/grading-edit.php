<?php 
session_start();
include_once("../../includes/connection.php");

$id = $_GET['id']; // Make sure this is correctly passed

// Check if the necessary form data is set
if (isset($_POST['editTitle']) && isset($_POST['editDescription']) && isset($_POST['editPercentage'])) {
    $titles = $_POST['editTitle']; // Array of titles
    $descriptions = $_POST['editDescription']; // Array of descriptions
    $percentages = $_POST['editPercentage']; // Array of percentages

    // Prepare an array to store the updated criteria
    $updatedCriteria = [];

    // Loop through each set of criteria
    for ($i = 0; $i < count($titles); $i++) {
        // Make sure the data is not empty
        if (empty($titles[$i]) || empty($descriptions[$i]) || empty($percentages[$i])) {
            echo '<script> alert("One or more fields are empty. Please fill out all fields."); </script>';
            exit; // Stop further execution if there's empty data
        }

        // Create the new criteria structure for each set of data
        $updatedCriteria[] = [
            'criteria' => mysqli_real_escape_string($connect, $titles[$i]),
            'description' => mysqli_real_escape_string($connect, $descriptions[$i]),
            'percentage' => mysqli_real_escape_string($connect, $percentages[$i])
        ];
    }

    // Encode the updated criteria array as JSON
    $updatedCriteriaJson = json_encode($updatedCriteria);

    // Update the database with the new JSON data
    $query = "UPDATE criteria_list SET criteria = '$updatedCriteriaJson' WHERE id = '$id'";
    $result = mysqli_query($connect, $query);

    if ($result) {
        echo '<script> alert("Data Updated Successfully"); </script>';
        header("Location:../grading-view.php");
    } else {
        echo '<script> alert("Data Not Updated"); </script>';
    }
} else {
    echo '<script> alert("No data to update."); </script>';
}
?>
