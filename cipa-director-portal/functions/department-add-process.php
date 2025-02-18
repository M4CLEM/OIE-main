<?php 
$connect = new mysqli('localhost', 'root', '', 'plmunoiedb');

if(isset($_POST['save']))
{

	$departmentTitle = $_POST['deptTitle'];
	$departmentAcronym = $_POST['deptAcr'];

	$stmt = $connect->prepare("INSERT INTO department_list (department, department_title) VALUES (?, ?)");
	$stmt->bind_param("ss", $departmentAcronym, $departmentTitle);
	
	if ($stmt->execute()) {

		header("Location: ../managecollege.php");
	
		$stmt->close();
		mysqli_close($connect);
	}
}
?>