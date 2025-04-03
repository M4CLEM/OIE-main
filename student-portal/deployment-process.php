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

    echo $companyName, $companyAddress, $trainerContact, $trainerEmail, $workType;

    $error = array();

    if (empty($companyName)) {
        $error[] = "Company name is Empty";
    } else if (empty($companyAddress)) {
        $error[] = "PCompany address is Empty";
    } else if (empty($trainerContact)) {
        $error[] = "Trainer's contact number is Empty";
    } else if (empty($trainerEmail)) {
        $error[] = "Trainer's email address is Empty";
    } else if (empty($workType)) {
        $error[] = "Work type is empty";
    }

    $stmt = $connect->prepare("SELECT * FROM studentinfo WHERE email = ?");
    $stmt->bind_param("s", $_SESSION['student']);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
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
    $stmt->close();

    if (count($error) < 1) {

        $studentInfoQuery = "INSERT INTO studentinfo (studentID, firstname, middlename, lastname, address, age, gender, contactNo, section, course, department, email, status, image, school_year, semester, objective, skills, seminars) 
            VALUES ('$studentID', '$firstName', '$middleName', '$lastName', '$address', '$age', '$gender', '$contactNo', '$section', '$course', '$department', '{$_SESSION['student']}', 'Undeployed', '$image', '$activeSchoolYear', '$activeSemester', '$objective', '$skills', '$seminars')";

        $result = mysqli_query($connect, $studentInfoQuery);

        $query = "INSERT INTO company_info (companyName, companyAddress, trainerContact, trainerEmail, workType, jobrole, status, studentID, student_email, section, semester, schoolYear) 
                VALUES ('$companyName', '$companyAddress', '$trainerContact', '$trainerEmail', '$workType', '$jobrole', 'Pending', $studentID, '{$_SESSION['student']}', '$section', '$activeSemester', '$activeSchoolYear')";

        $res = mysqli_query($connect, $query);

        $companyQuery = "INSERT INTO companylist (companyName, companyaddress, contactPerson, jobrole, jobdescription, jobreq, link, dept, semester, schoolYear) VALUES ('$companyName', '$companyAddress', '$contactPerson', '$jobrole', '$jobdescription', '$jobRequirement', '$companyLink', '$department', '$activeSemester', '$activeSchoolYear')";

        $companyResult = mysqli_query($connect, $companyQuery);

        if ($res || $companyResult || $result) {
            header("Location:deploy.php");
        } else {
            $output = "Registration failed. Please try again.";
        }
    } else {
        $output = implode("<br>", $error);
    }
}
