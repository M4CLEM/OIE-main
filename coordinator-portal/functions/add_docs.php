<?php
session_start();
include_once("../../includes/connection.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (!isset($_SESSION['department'])) {
        die("Error: Department not set in session.");
    }

    $department = $_SESSION['department'];
    $documentName = htmlspecialchars($_POST['addDocumentName'], ENT_QUOTES, 'UTF-8');

    // Prioritize text field, fallback to dropdown if empty
    $documentType = !empty($_POST['addDocumentType']) ? 
                    htmlspecialchars($_POST['addDocumentType'], ENT_QUOTES, 'UTF-8') : 
                    htmlspecialchars($_POST['documentTypeDropdown'], ENT_QUOTES, 'UTF-8');

    // File upload handling
    $fileUploadPath = null;
    if (isset($_FILES["newFile"]) && $_FILES["newFile"]["error"] == 0) {
        $allowedTypes = ['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $fileType = mime_content_type($_FILES["newFile"]["tmp_name"]);
        
        if (!in_array($fileType, $allowedTypes)) {
            die("Error: Invalid file type. Only PDF and DOCX files are allowed.");
        }

        // Define the upload directory
        $uploadsDirectory = "../../uploads/$department/$documentType/";
        if (!file_exists($uploadsDirectory)) {
            mkdir($uploadsDirectory, 0777, true);
        }

        // Use the original filename
        $originalFileName = basename($_FILES["newFile"]["name"]);
        $fileUploadPath = $uploadsDirectory . $originalFileName;

        if (!move_uploaded_file($_FILES["newFile"]["tmp_name"], $fileUploadPath)) {
            die("Error: Failed to upload the file.");
        }
    }

    // Insert into database
    $sql = "INSERT INTO documents_list (documentName, documentType, department, file_template) VALUES (?, ?, ?, ?)";
    $stmt = $connect->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssss", $documentName, $documentType, $department, $fileUploadPath);
        if ($stmt->execute()) {
            header("Location: ../documents.php");
            exit();
        } else {
            echo "Error executing statement: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $connect->error;
    }

    $connect->close();
}
?>
