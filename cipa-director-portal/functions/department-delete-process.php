<?php
    $connect = new mysqli('localhost', 'root', '', 'plmunoiedb');
    
    $query = "DELETE FROM department_list WHERE department='" . $_GET["dept"] . "'";
    if (mysqli_query($connect, $query)) {

        $queryCourse = "DELETE FROM course_list WHERE department='" . $_GET["dept"] . "'";
        if (mysqli_query($connect, $queryCourse)) {

            $queryDelete = "DELETE FROM sections_list WHERE department='" . $_GET["dept"] . "'";

            if (mysqli_query($connect, $queryDelete)) {

                header('Location:../managecollege.php');

            } else {

                echo "Error deleting section record: " . mysqli_error($connect);

            }
            
        } else {

            echo "Error deleting course record: " . mysqli_error($connect);

        }

    } else {
        echo "Error deleting record: " . mysqli_error($connect);
    }
    mysqli_close($connect);
?>