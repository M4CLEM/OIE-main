<?php 
include_once("../../includes/connection.php");

if(isset($_POST['add']))
{
	$courseTitle = $_POST['courseTitle'];
	$courseAcronym = $_POST['courseAcr'];
    $numSections = $_POST['numSections'];

	$stmt = $connect->prepare("INSERT INTO course_list (course, course_title, department) VALUES (?, ?, ?)");
	$stmt->bind_param("sss", $courseAcronym, $courseTitle, $_GET['dept']);
	
	if ($stmt->execute()) {

        for ($i =   1; $i <= $numSections; $i++) {

            $sectionName = "4" . chr(65 + $i -  1);
            $stmtSection = $connect->prepare("INSERT INTO sections_list (department, course, section) VALUES (?, ?, ?)");
            $stmtSection->bind_param("sss", $_GET['dept'], $courseAcronym, $sectionName);
            
            if (!$stmtSection->execute()) {
                echo "Error: " . $stmtSection->error;
            }

            $stmtSection->close();
        }

		header('Location: ../view-department.php?dept=' . $_GET['dept']);
	
		$stmt->close();
		mysqli_close($connect);
	}
}
?>