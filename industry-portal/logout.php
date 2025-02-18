<?php 
session_start();


if (isset($_SESSION['IP_num'])) {
	unset($_SESSION['IP_num']);
	header("Location: index.php");
}


 ?>