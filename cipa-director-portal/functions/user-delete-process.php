<?php
$connect = new mysqli('localhost', 'root', '', 'plmunoiedb');
$query = "DELETE FROM users WHERE id='" . $_GET["id"] . "'";
if (mysqli_query($connect, $query)) {
   header("Location:../manage-user.php");
} else {
    echo "Error deleting record: " . mysqli_error($connect);
}
mysqli_close($connect);
?>