<?php
    include_once("../../includes/connection.php");

    $department = isset($_GET['dept']) ? $_GET['dept'] : '';
    $targetPage = $department !== '' ? 'company-filter.php?dept=' . $department : 'company.php';

    $query = "DELETE FROM companylist WHERE No='" . $_GET["number"] . "'";
    if (mysqli_query($connect, $query)) {
        header('Location:../' . $targetPage);
    } else {
        echo "Error deleting record: " . mysqli_error($connect);
    }
    mysqli_close($connect);
?>