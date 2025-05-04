<?php
session_start();
include_once("../includes/connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the POST data
    $id = $_POST['id']; // ID for deleting from the listadviser table
    $email = $_POST['email']; // Email for deleting from the users table
    $removeRecord = isset($_POST['removeRecord']) && $_POST['removeRecord'] === 'true'; // Checkbox value for removing record from list
    $removeAccess = isset($_POST['removeAccess']) && $_POST['removeAccess'] === 'true'; // Checkbox value for removing system account access

    // Validation for ID and email
    if (empty($id) || empty($email)) {
        echo '<div class="alert alert-danger" role="alert">Invalid ID or Email. Please try again.</div>';
        exit;
    }

    try {
        // Start the transaction to ensure atomic deletion
        $connect->begin_transaction();

        // Delete from `listadviser` table if `removeRecord` is true
        if ($removeRecord) {
            $deleteFromList = $connect->prepare('DELETE FROM listadviser WHERE id = ?');
            $deleteFromList->bind_param('i', $id);
            $deleteFromList->execute();
            $deleteFromList->close();
        }

        // Delete from `users` table if `removeAccess` is true
        if ($removeAccess) {
            $deleteFromUsers = $connect->prepare('DELETE FROM users WHERE username = ?');
            $deleteFromUsers->bind_param('s', $email);
            $deleteFromUsers->execute();
            $deleteFromUsers->close();
        }

        // Commit the transaction
        $connect->commit();

        echo json_encode(['success' => 'Account deleted successfully.']);
    } catch (Exception $e) {
        // Rollback in case of error
        $connect->rollback();
        echo json_encode(['error' => 'An error occurred: ' . $e->getMessage()]);
    }
}
?>
