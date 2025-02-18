<?php 

	$No = $_POST['No'];
	$companyName = $_POST['companyName'];
	$companyaddress = $_POST['companyaddress'];
	$contactPerson = $_POST['contact'];
	$jobrole = $_POST['jobrole'];
	$jobdescription = $_POST['jobdescription'];
	$jobreq = $_POST['jobreq'];
	$link = $_POST['link'];


	$connect = new mysqli('localhost', 'root', '', 'plmunoiedb');

    $stmt = $connect->prepare("UPDATE companylist SET companyName = ?, companyaddress = ?, contactPerson = ?, jobrole = ?, jobdescription = ?, jobreq = ?, link = ? WHERE No = ?");
    $stmt->bind_param("sssssssi", $companyName, $companyaddress, $contactPerson, $jobrole, $jobdescription, $jobreq, $link, $No);
    $result = $stmt->execute();

    if ($result) {
        header('Location: ../view-company.php?number=' . $No . '&dept=' . $_GET['dept']);
    } else {
        echo "Update failed: " . $stmt->error;
    }

?>


