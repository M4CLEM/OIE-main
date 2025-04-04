<?php
include("../includes/connection.php");
session_start();

$activeSemester = $_SESSION['semester'];
$activeSchoolYear = $_SESSION['schoolYear'];

$output = "";

if (isset($_POST['submit'])) {
    $companyName = $_POST['companyName'];
    $companyAddress = $_POST['companyAddress'];
    $contactPerson = $_POST['contactPerson'];
    $trainerContact = $_POST['trainerContact'];
    $trainerEmail = $_POST['trainerEmail'];
    $companyLink = $_POST['companyLink'];
    $workType = $_POST['workType'];
    $jobrole = $_POST['jobrole'];
    $jobdescription = $_POST['jobDescription'];
    $jobRequirement = $_POST['jobRequirement'];

    $error = array();

    if (empty($companyName)) {
        $error[] = "Company name is Empty";
    } 
    if (empty($companyAddress)) {
        $error[] = "Company address is Empty";
    } 
    if (empty($trainerContact)) {
        $error[] = "Trainer's contact number is Empty";
    } 
    if (empty($trainerEmail)) {
        $error[] = "Trainer's email address is Empty";
    } 
    if (empty($workType)) {
        $error[] = "Work type is empty";
    }

    // Fetch student details
    $stmt = $connect->prepare("SELECT * FROM studentinfo WHERE email = ?");
    $stmt->bind_param("s", $_SESSION['student']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $studentID = $row['studentID'];
        $firstName = $row['firstname'];
        $middleName = $row['middlename'];
        $lastName = $row['lastname'];
        $address = $row['address'];
        $age = $row['age'];
        $gender = $row['gender'];
        $contactNo = $row['contactNo'];
        $section = $row['section'];
        $course = $row['course'];
        $image = $row['image'];
        $department = $row['department'];
        $objective = $row['objective'];
        $skills = $row['skills'];
        $seminars = $row['seminars'];
    }
    $stmt->close();

    if (count($error) < 1) {
        // Check if the student record already exists for the active semester and school year
        $checkQuery = "SELECT * FROM studentinfo WHERE studentID = ? AND school_year = ? AND semester = ?";
        $checkStmt = $connect->prepare($checkQuery);
        $checkStmt->bind_param("iss", $studentID, $activeSchoolYear, $activeSemester);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        $recordExists = $checkResult->num_rows > 0;
        $checkStmt->close();

        if (!$recordExists) {
            // Insert student info only if no record exists
            $studentInfoQuery = "INSERT INTO studentinfo (studentID, firstname, middlename, lastname, address, age, gender, contactNo, section, course, department, email, status, image, school_year, semester, objective, skills, seminars) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Undeployed', ?, ?, ?, ?, ?, ?)";

            $stmt = $connect->prepare($studentInfoQuery);
            $stmt->bind_param("issssissssssssssss", $studentID, $firstName, $middleName, $lastName, $address, $age, $gender, $contactNo, $section, $course, $department, $_SESSION['student'], $image, $activeSchoolYear, $activeSemester, $objective, $skills, $seminars);
            $stmt->execute();
            $stmt->close();
        }

        // Insert into company_info
        $query = "INSERT INTO company_info (companyName, companyAddress, trainerContact, trainerEmail, workType, jobrole, status, studentID, student_email, section, semester, schoolYear) 
                  VALUES (?, ?, ?, ?, ?, ?, 'Pending', ?, ?, ?, ?, ?)";
        
        $stmt = $connect->prepare($query);
        $stmt->bind_param("ssssssissss", $companyName, $companyAddress, $trainerContact, $trainerEmail, $workType, $jobrole, $studentID, $_SESSION['student'], $section, $activeSemester, $activeSchoolYear);
        $stmt->execute();
        $stmt->close();

        // Insert into companylist
        $companyQuery = "INSERT INTO companylist (companyName, companyaddress, contactPerson, jobrole, jobdescription, jobreq, link, dept, semester, schoolYear) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $connect->prepare($companyQuery);
        $stmt->bind_param("ssssssssss", $companyName, $companyAddress, $contactPerson, $jobrole, $jobdescription, $jobRequirement, $companyLink, $department, $activeSemester, $activeSchoolYear);
        $stmt->execute();
        $stmt->close();

        header("Location: deploy.php");
    } else {
        $output = implode("<br>", $error);
    }
}

?>
