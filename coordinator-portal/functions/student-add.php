<?php 
// Include the database connection
include_once("../../includes/connection.php");

if(isset($_POST['save'])) {

    // Ensure $connect is properly defined
    if (!isset($connect)) {
        die("Database connection error.");
    }

    // Retrieve and sanitize inputs
    $studentID = trim($_POST['studentID']); 
    $lastName = trim($_POST['lastName']); 
    $firstName = trim($_POST['firstName']);
    $course = trim($_POST['course']);
    $section = trim($_POST['section']);
    $year = trim($_POST['year']);
    $semester = trim($_POST['semester']);

    // Prepare the SQL statement
    $query = "INSERT INTO student_masterlist (studentID, lastName, firstName, course, section, year, semester) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $connect->prepare($query)) {
        $stmt->bind_param("sssssss", $studentID, $lastName, $firstName, $course, $section, $year, $semester);
        
        // Execute and check if successful
        if ($stmt->execute()) {
            echo "New record created successfully!";
            header("Location: ../masterlist.php");
            exit(); // Ensure script stops after redirection
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error preparing statement: " . $connect->error;
    }

    // Close the database connection
    $connect->close();
}
?>
