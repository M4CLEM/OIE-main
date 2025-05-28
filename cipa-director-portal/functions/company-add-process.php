<?php 
session_start();
include_once("../../includes/connection.php");

$department = isset($_GET['dept']) ? $_GET['dept'] : '';
$targetPage = $department !== '' ? 'company-filter.php?dept=' . $department : 'company.php';

$activeSemester = $_SESSION['semester'];
$activeSchoolYear = $_SESSION['schoolYear'];


if(isset($_POST['save']))
{
	$companyName = $_POST['companyName'];
	$companyaddress = $_POST['companyaddress'];
	$contact = $_POST['contact'];
	$jobrole = $_POST['jobrole'];
	$workType = $_POST['workType'];
	$jobdescription = $_POST['jobdescription'];
	$jobreq = $_POST['jobreq'];
	$link = $_POST['link'];
	$dept = $_POST['dept'];
	$slots = $_POST['slots'];

	$stmt = $connect->prepare("INSERT INTO companylist (companyName, companyaddress, contactPerson, jobrole, slots, workType, jobdescription, jobreq, link, dept, semester, schoolYear) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
	$stmt->bind_param("ssssssssssss", $companyName, $companyaddress, $contact, $jobrole, $slots, $workType, $jobdescription, $jobreq, $link, $dept, $activeSemester, $activeSchoolYear);
	
	if ($stmt->execute()) {

		header("Location: ../$targetPage");
	
		$stmt->close();
		mysqli_close($connect);
	}
}
?>