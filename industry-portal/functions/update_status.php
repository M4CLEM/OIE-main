<?php
session_start();
include_once("../../includes/connection.php");

if (isset($_POST['documentId']) && isset($_POST['status'])) {
    $documentId = $_POST['documentId'];
    $status = $_POST['status'];
    $statusUC = ucfirst($status);
    
    // Prepare the statement to update status
    $stmt = $connect->prepare("UPDATE documents SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $statusUC, $documentId);

    // Execute the statement to update status
    if (!$stmt->execute()) {
        http_response_code(500);
        echo "Error updating status: " . $stmt->error;
        exit(); // Exit if there's an error
    }

    // Select the document based on documentId
    $selectStmt = $connect->prepare("SELECT document FROM documents WHERE id = ?");
    $selectStmt->bind_param("i", $documentId);
    $selectStmt->execute();
    $result = $selectStmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $document = $row['document'];

        // Return JSON response with document content
        echo json_encode(array("status" => "success", "document" => $document));
    } else {
        // Return JSON response if document not found
        echo json_encode(array("status" => "error", "message" => "Document not found"));
    }
} else {
    // Return JSON response if parameters are missing
    echo json_encode(array("status" => "error", "message" => "Missing parameters"));
}

$connect->close();
?>
