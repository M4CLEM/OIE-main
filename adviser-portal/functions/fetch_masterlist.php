<?php
session_start();
include_once("../../includes/connection.php");

// Check if section is provided
if(isset($_POST['section'], $_POST['course'])) {
    // Get the section value
    $section = $_POST['section'];
    $course = $_POST['course'];

    
    // Prepare the SQL statement with a parameterized query to prevent SQL injection
    $query = "SELECT * FROM student_masterlist WHERE section = ? AND course = ? ORDER BY lastName ASC";
    $stmt = mysqli_prepare($connect, $query);
    mysqli_stmt_bind_param($stmt, "ss", $section, $course);
    
    // Execute the prepared statement
    mysqli_stmt_execute($stmt);
    
    // Get the result set
    $result = mysqli_stmt_get_result($stmt);
    
    // Check if there are rows returned
    if(mysqli_num_rows($result) >  0) {
        // Initialize counter
        $counter =  1;
    
        // Fetch data and display in a format suitable for your dataTable2
        while($row = mysqli_fetch_assoc($result)) {
            // Output the counter as part of the row
            echo "<tr>";
            echo "<td class='small'>{$counter}</td>"; // This will output the current value of the counter
            echo "<td class='small'><a href='' class='info-link' data-section='{$row['studentID']}'>{$row['studentID']}</a></td>";
            echo "<td class='small'>{$row['lastName']} {$row['firstName']}</td>";
            echo "<td class='small'>{$row['year']}</td>";
            echo "</tr>";
    
            // Increment the counter for the next iteration
            $counter++;
        }
    }
     else {
        // If no rows are returned, display a message or handle the scenario accordingly
        echo "<tr><td colspan='3'>No students found for this section.</td></tr>";
    }
    
    // Close the prepared statement
    mysqli_stmt_close($stmt);
} else {
    // If section is not provided in the POST data, handle the scenario accordingly
    echo "<tr><td colspan='3'>No section provided.</td></tr>";
}
?>
