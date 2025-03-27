<?php
include_once("../../includes/connection.php");

if(isset($_POST['update']))
   
    {   
        $id = $_POST['id']; 
        $username = $_POST['username'];
        $password = $_POST['password'];
        $role = $_POST['role'];

        $query = "UPDATE users SET username='$username', password='$password' , role='$role' WHERE id='$id'  ";
        $result = mysqli_query($connect, $query);

        if($result)
        {
            echo '<script> alert("Data Updated"); </script>';
            header("Location:../manage-user.php");
        }
        else
        {
            echo '<script> alert("Data Not Updated"); </script>';
        }
    }
?>