<?php
    session_start();

    include_once("../../includes/connection.php");

    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            
        }
    }
    
?>