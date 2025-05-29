<?php
include_once("../../includes/connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];
    $companyName = mysqli_real_escape_string($connect, $_POST["editCompanyName"]);
    $email = mysqli_real_escape_string($connect, $_POST["editEmail"]);
    $password = isset($_POST["editPassword"]) ? $_POST["editPassword"] : "";

    // Update query based on whether password is provided
    if (!empty($password)) {
        $query = "UPDATE users SET companyName='$companyName', username='$email', password='$password' WHERE id='$id'";
    } else {
        $query = "UPDATE users SET companyName='$companyName', username='$email' WHERE id='$id'";
    }

    if (mysqli_query($connect, $query)) {
        echo json_encode(["status" => "success", "message" => "Company account updated successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update account."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}
?>

