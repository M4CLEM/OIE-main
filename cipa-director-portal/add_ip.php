<?php
session_start();
include_once("../includes/connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $compName = $_POST['companyName'];
    $email = $_POST['email'];
    $role = "IndustryPartner";
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm'];

    // Validate password confirmation
    if ($password !== $confirmPassword) {
        echo '<div class="alert alert-danger" role="alert">Passwords do not match. Please try again.</div>';
        // You can customize the error message or style as needed.
    } else {

            // Insert values into Users table
            $addToUsers = $connect->prepare('INSERT INTO users (companyName, username, role, password) VALUES (?, ?, ?, ?)');
            $addToUsers->bind_param('ssss', $compName, $email, $role, $password);
            $addToUsers->execute();
            $addToUsers->close();

            header('Location: add_company_acc.php');
        }
    }

?>
