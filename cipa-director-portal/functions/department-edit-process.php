<?php 

	$departmentTitle = $_POST['deptTitle'];
	$departmentAcronym = $_POST['deptAcr'];

	include_once("../../includes/connection.php");

    $stmt = $connect->prepare("UPDATE department_list SET department = ?, department_title = ? WHERE id = ?");
    $stmt->bind_param("ssi", $departmentAcronym, $departmentTitle, $_GET['id']);
    $result = $stmt->execute();

    if ($result) {
        header('Location: ../view-department.php?dept=' . $departmentAcronym);
    } else {
        echo "Update failed: " . $stmt->error;
    }

?>


