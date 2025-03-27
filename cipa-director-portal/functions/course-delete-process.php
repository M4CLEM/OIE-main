<?php

    include_once("../../includes/connection.php");
    
    $query = "DELETE FROM course_list WHERE course='" . $_GET["course"] . "'";
    if (mysqli_query($connect, $query)) {

        $queryDelete = "DELETE FROM sections_list WHERE course='" . $_GET["course"] . "'";

        if (mysqli_query($connect, $queryDelete)) {

            header('Location:../view-department.php?dept=' . $_GET['dept']);

        }else {

            echo "Error deleting section record: " . mysqli_error($connect);

        }
        
    } else {

        echo "Error deleting course record: " . mysqli_error($connect);

    }
    mysqli_close($connect);
    
?>