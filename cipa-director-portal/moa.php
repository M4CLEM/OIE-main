<?php
    session_start();
    include_once("../includes/connection.php");
?>

<!DOCTYPE html>
<html>
    <head>
        <?php include("../elements/meta.php"); ?>
        <title>CIPA ADMIN</title>
        <?php include("embed.php"); ?>
        <link rel="stylesheet" href="../assets/css/new-style.css">
    </head>
    <body id="page-top">
        <div class="wrapper">
            <aside id="sidebar" class="expand">
                <?php include('../elements/cipa_sidebar.php') ?>
            </aside>

            <div class="main">
                
            </div>
        </div>
    </body>
</html>