<?php
session_start();
include_once("../includes/connection.php");

// Check if section is set in the POST request
if (isset($_POST['section'])) {
    // Sanitize the input to prevent SQL injection
    $section = mysqli_real_escape_string($connect, $_POST['section']);

    // Prepare query to fetch data based on selected section
    if ($section == "All Sections") {
        $getSections = "SELECT section FROM listadviser WHERE email = '{$_SESSION['adviser']}'";
        $sectionsResult = mysqli_query($connect, $getSections);

        $sections = [];
        while ($row = mysqli_fetch_assoc($sectionsResult)) {
            $sections[] = $row['section'];
        }
        $sectionsString = implode("','", $sections);
        $query = "SELECT * FROM student_masterlist WHERE section IN ('$sectionsString') ORDER BY section ASC, lastName ASC";
    } else {
        $query = "SELECT * FROM student_masterlist WHERE section = '$section' ORDER BY section ASC, lastName ASC";
    }

    // Execute the query
    $result = mysqli_query($connect, $query);

    // Initialize variable to store table rows
    $tableRows = "";

    // Check if query was successful
    if ($result) {
        // Check if there are any rows returned
        if (mysqli_num_rows($result) > 0) {
            // Loop through each row in the result set and create table rows
            while ($row = mysqli_fetch_assoc($result)) {
                $tableRows .= "<tr>";
                $tableRows .= "<td class='text-center'><div class='form-check'><input name='studentIDs[]' class='form-check-input p-0 checkbox-highlight' type='checkbox' style='transform: scale(1.5);' value='{$row['studentID']}' id='flexCheck{$row['studentID']}'></div></td>";
                //$tableRows .= "<td><a title='Input' href='pgrade-input.php?studentID={$row['studentID']}' class='btn btn-primary btn-sm'>Input<span class='fa fa-input fw-fa'></span></a></td>";
                $tableRows .= "<td>{$row['studentID']}</td>";
                $tableRows .= "<td>{$row['lastName']}, {$row['firstName']}</td>";
                $tableRows .= "<td>{$row['section']}</td>";
                $tableRows .= "<td>{$row['year']}</td>";
                $tableRows .= "</tr>";
            }
        } else {
            // If no rows found, set an appropriate message
            $tableRows = "<tr><td colspan='5'>No Results Found</td></tr>";
        }
    } else {
        // If query fails, set an error message
        $tableRows = "<tr><td colspan='5'>Error: " . mysqli_error($connect) . "</td></tr>";
    }

    // Echo the table rows back to the AJAX request
    echo $tableRows;
} else {
    // If section is not set in the POST request, echo error message
    echo "Error: Section not specified";
}
