<?php
session_start();
include_once("../includes/connection.php");

$semester = $_SESSION['semester'];
$schoolYear = $_SESSION['schoolYear'];

if (isset($_POST['section'])) {
    $section = mysqli_real_escape_string($connect, $_POST['section']);
    $adviserEmail = $_SESSION['adviser'];
    $tableRows = "";

    if ($section === "All") {
        // Get all sections assigned to the adviser
        $getSections = "SELECT section FROM listadviser WHERE email = '$adviserEmail'";
        $sectionsResult = mysqli_query($connect, $getSections);

        $sectionArray = [];

        if ($sectionsResult) {
            while ($row = mysqli_fetch_assoc($sectionsResult)) {
                $splitSections = explode(',', $row['section']);
                foreach ($splitSections as $sec) {
                    $trimmed = trim($sec);
                    if ($trimmed !== "") {
                        $sectionArray[] = mysqli_real_escape_string($connect, $trimmed);
                    }
                }
            }
        }

        if (!empty($sectionArray)) {
            $sectionList = "'" . implode("','", $sectionArray) . "'";
            $query = "SELECT * FROM studentinfo 
                      WHERE section IN ($sectionList) 
                      AND semester = '$semester' 
                      AND school_year = '$schoolYear'
                      AND status = 'Deployed' 
                      ORDER BY section ASC, lastName ASC";
        } else {
            echo "<tr><td colspan='5'>No sections assigned to this adviser.</td></tr>";
            exit;
        }
    } else {
        // Single section selected
        $query = "SELECT * FROM studentinfo 
                  WHERE section = '$section' 
                  AND semester = '$semester' 
                  AND school_year = '$schoolYear' 
                  AND status = 'Deployed' 
                  ORDER BY section ASC, lastName ASC";
    }

    $result = mysqli_query($connect, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Safe access with null coalescing operator
            $studentID = $row['studentID'] ?? '';
            $lastName = $row['lastname'] ?? '';
            $firstName = $row['firstname'] ?? '';
            $section = $row['section'] ?? '';
            $year = '';

            $yearQuery = "SELECT year FROM student_masterlist 
                          WHERE studentID = '$studentID' 
                          AND semester = '$semester' 
                          AND schoolYear = '$schoolYear' 
                          LIMIT 1";
            $yearResult = mysqli_query($connect, $yearQuery);
            if ($yearResult && mysqli_num_rows($yearResult) > 0) {
                $yearRow = mysqli_fetch_assoc($yearResult);
                $year = $yearRow['year'] ?? '';
            }

            $tableRows .= "<tr>";
            $tableRows .= "<td class='text-center'>
                <div class='form-check'>
                    <input name='studentIDs[]' class='form-check-input p-0 checkbox-highlight' 
                        type='checkbox' style='transform: scale(1.5);' 
                        value='{$studentID}' id='flexCheck{$studentID}'>
                </div>
            </td>";
            $tableRows .= "<td>{$studentID}</td>";
            $tableRows .= "<td>{$lastName}, {$firstName}</td>";
            $tableRows .= "<td>{$section}</td>";
            $tableRows .= "<td>{$year}</td>";
            $tableRows .= "</tr>";
        }
    } else {
        $tableRows = "<tr><td colspan='5'>No Results Found</td></tr>";
    }

    echo $tableRows;
} else {
    echo "<tr><td colspan='5'>Error: Section not specified</td></tr>";
}
