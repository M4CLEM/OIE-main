<?php 
$connect = new mysqli('localhost', 'root', '', 'plmunoiedb');

$department = isset($_GET['dept']) ? $_GET['dept'] : '';
$targetPage = $department !== '' ? 'company-filter.php?dept=' . $department : 'company.php';


if(isset($_POST['save']))
{
	$companyName = $_POST['companyName'];
	$companyaddress = $_POST['companyaddress'];
	$contact = $_POST['contact'];
	$jobrole = $_POST['jobrole'];
	$jobdescription = $_POST['jobdescription'];
	$jobreq = $_POST['jobreq'];
	$link = $_POST['link'];
	$dept = $_POST['dept'];

	$stmt = $connect->prepare("INSERT INTO companylist (companyName, companyaddress, contactPerson, jobrole, jobdescription, jobreq, link, dept) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
	$stmt->bind_param("ssssssss", $companyName, $companyaddress, $contact, $jobrole, $jobdescription, $jobreq, $link, $dept);
	
	if ($stmt->execute()) {

		header("Location: ../$targetPage");
	
		$stmt->close();
		mysqli_close($connect);
	}
}
?>