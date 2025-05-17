<?php 

	$No = $_POST['No'];
	$companyName = $_POST['companyName'];
	$companyaddress = $_POST['companyaddress'];
	$contactPerson = $_POST['contact'];
	$jobrole = $_POST['jobrole'];
	$workType = $_POST['workType'];
	$jobdescription = $_POST['jobdescription'];
	$jobreq = $_POST['jobreq'];
	$link = $_POST['link'];


	include_once("../../includes/connection.php");

    $stmt = $connect->prepare("UPDATE companylist SET companyName = ?, companyaddress = ?, contactPerson = ?, jobrole = ?, workType = ?, jobdescription = ?, jobreq = ?, link = ? WHERE No = ?");
    $stmt->bind_param("ssssssssi", $companyName, $companyaddress, $contactPerson, $jobrole, $workType, $jobdescription, $jobreq, $link, $No);
    $result = $stmt->execute();

    if ($result) {
        header('Location: ../view-company.php?number=' . $No . '&dept=' . $_GET['dept']);
    } else {
        echo "Update failed: " . $stmt->error;
    }

?>


