<?php
include_once("../../includes/connection.php");
$query = "DELETE FROM users WHERE id='" . $_GET["id"] . "'";
if (mysqli_query($connect, $query)) {
   header("Location:../manage-user.php");
} else {
    echo "Error deleting record: " . mysqli_error($connect);
}
mysqli_close($connect);
?>