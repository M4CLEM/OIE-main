<?php
$connect = new mysqli('localhost', 'root', '', 'plmunoiedb');

if(isset($_POST['update']))
   
    {   
        $studentID = $_POST['studentID']; 
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $department = $_POST['department'];
        $course = $_POST['course'];
        $status = $_POST['status'];

        $query = "UPDATE studentinfo SET firstname='$firstname', lastname='$lastname', department=' $department', course='$course', status=' $status' WHERE studentID='$studentID'  ";
        $result = mysqli_query($connect, $query);

        if($result)
        {
            echo '<script> alert("Data Updated"); </script>';
            header("Location:../masterlist.php");
        }
        else
        {
            echo '<script> alert("Data Not Updated"); </script>';
        }
    }
?>