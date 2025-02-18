<?php 
	$studentID = $_POST['studentID'];
	$Fullname = $_POST['Fullname'];
	$SenseofUrgency = $_POST['SenseofUrgency'];
	$QualityofWork = $_POST['QualityofWork'];
	$ExecutionConcept = $_POST['ExecutionConcept'];
	$PromptnessandPunctuality = $_POST['PromptnessandPunctuality'];
	$WorkEthics = $_POST['WorkEthics'];
	$Demeanor = $_POST['Demeanor'];
	

	$connect = new mysqli('localhost', 'root', '', 'plmunoiedb');
	if($connect->connect_error){
		die('connection failed : '.$connect->connect_error);
	}else{
		$stmt = $connect->prepare("INSERT INTO student_grade(studentID,Fullname,SenseofUrgency,QualityofWork,ExecutionConcept,PromptnessandPunctuality,WorkEthics,Demeanor) VALUES(?,?,?,?,?,?,?,?)");
		$stmt->bind_param('isiiiiii',$studentID, $Fullname, $SenseofUrgency, $QualityofWork, $ExecutionConcept, $PromptnessandPunctuality,$WorkEthics,$Demeanor,);
		$stmt->execute();
		header("Location:Pgrade.php");
		$stmt->close();
		$connect->close();
	}
?>