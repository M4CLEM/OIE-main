<?php
include("../includes/connection.php");
session_start();

$output = "";

if (isset($_POST['submit'])) {
    $companyName = $_POST['companyName'];
    $companyAddress = $_POST['companyAddress'];
    $trainerContact = $_POST['trainerContact'];
    $trainerEmail = $_POST['trainerEmail'];
    $workType = $_POST['workType'];

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
    $section = $row['section'];
    $stmt->close();


    if (count($error) < 1) {

        $query = "INSERT INTO company_info (companyName, companyAddress, trainerContact, trainerEmail, workType, status, studentID, student_email, section) 
                VALUES ('$companyName', '$companyAddress', '$trainerContact', '$trainerEmail', '$workType', 'Pending', $studentID, '{$_SESSION['student']}', '$section')";

        $res = mysqli_query($connect, $query);

        if ($res) {
            header("Location:deploy.php");
        } else {
            $output = "Registration failed. Please try again.";
        }
    } else {
        $output = implode("<br>", $error);
    }
}
