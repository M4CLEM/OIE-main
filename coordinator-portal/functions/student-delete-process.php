<?php
include_once("../../includes/connection.php");
$query = "DELETE FROM studentinfo WHERE studentID='" . $_GET["id"] . "'";
if (mysqli_query($connect, $query)) {
   header("Location:../masterlist.php");
} else {
    echo "Error deleting record: " . mysqli_error($connect);
}
mysqli_close($connect);
?>