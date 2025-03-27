<?php 

if(isset($_POST['edit']))
{
    include_once("../../includes/connection.php");

	$courseTitle = $_POST['editCourseTitle'];
	$courseAcronym = $_POST['editCourseAcr'];
    $numSections = $_POST['editNumSections'];

    $stmt = $connect->prepare("UPDATE course_list SET course = ?, course_title = ? WHERE id = ?");
    $stmt->bind_param("ssi", $courseAcronym, $courseTitle, $_GET['courseID']);
    
    if ($stmt->execute()) {

        $queryDel = "DELETE FROM sections_list WHERE department = ? AND course = ?";
        $stmtDel = $connect->prepare($queryDel);
        $stmtDel->bind_param('ss', $_GET['dept'], $courseAcronym);
        $stmtDel->execute();

        for ($i =   1; $i <= $numSections; $i++) {

            $sectionName = "4" . chr(65 + $i -  1);
            $stmtSection = $connect->prepare("INSERT INTO sections_list (department, course, section) VALUES (?, ?, ?)");
            $stmtSection->bind_param("sss", $_GET['dept'], $courseAcronym, $sectionName);
            
            if (!$stmtSection->execute()) {
                echo "Error: " . $stmtSection->error;
            }

            $stmtSection->close();
        }

        $stmtDel->close();

		header('Location: ../view-department.php?dept=' . $_GET['dept']);
	
		$stmt->close();
		mysqli_close($connect);

    } else {
        echo "Update failed: " . $stmt->error;
    }

}

?>


