<?php 
session_start();
include_once("../../includes/connection.php");

$id = $_GET['id'];

$title = $_POST['editTitle'];
$description = $_POST['editDescription'];
$percentage = $_POST['editPercentage'];

$query = "UPDATE criteria_list SET criteria='$title', description='$description', percentage='$percentage' WHERE id='$id'";
$result = mysqli_query($connect, $query);

if($result)
{
    echo '<script> alert("Data Updated"); </script>';
    header("Location:../grading-view.php");
}
else
{
    echo '<script> alert("Data Not Updated"); </script>';
}
?>