<?php
$connect = new mysqli('localhost', 'root', '', 'plmunoiedb');
$query = "DELETE FROM studentinfo WHERE studentID='" . $_GET["id"] . "'";
if (mysqli_query($connect, $query)) {
   header("Location:../masterlist.php");
} else {
    echo "Error deleting record: " . mysqli_error($connect);
}
mysqli_close($connect);
?>