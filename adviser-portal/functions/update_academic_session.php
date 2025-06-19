<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['semester'], $_POST['schoolYear'])) {
        $_SESSION['semester'] = trim($_POST['semester']);
        $_SESSION['schoolYear'] = trim($_POST['schoolYear']);
    }
}

// Redirect back to previous page
header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
