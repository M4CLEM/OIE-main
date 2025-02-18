<?php
$connect = new mysqli('localhost', 'root', '', 'plmunoiedb');

if(isset($_POST['update']))
   
    {   
        $studentID = $_POST['studentID']; 
        $firstname = $_POST['firstname'];
        $middlename = $_POST['middlename'];
        $lastname = $_POST['lastname'];
        $course = $_POST['course'];
        $status = $_POST['status'];

        $query = "UPDATE studentinfo SET firstname='$firstname', middlename='$middlename', lastname='$lastname', course='$course', status=' $status' WHERE studentID='$studentID'  ";
        $result = mysqli_query($connect, $query);

        if($result)
        {
            echo '<script> alert("Data Updated"); </script>';
            header("Location:../student-list.php");
        }
        else
        {
            echo '<script> alert("Data Not Updated"); </script>';
        }
    }
?>