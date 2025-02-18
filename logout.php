<?php 
session_start();


if (isset($_SESSION['student'])) {
	unset($_SESSION['student']);
	header("Location:index.php");
}else if(isset($_SESSION['adviser'])){
	unset($_SESSION['adviser']);
	header("Location:index.php");
}else if(isset($_SESSION['admin'])){
	unset($_SESSION['admin']);
	header("Location:index.php");
}else if(isset($_SESSION['coordinator'])){
	unset($_SESSION['coordinator']);
	header("Location:index.php");
}else if(isset($_SESSION['IndustryPartner'])){
	unset($_SESSION['IndustryPartner']);
	if (isset($_SESSION['IP_num'])) {
		unset($_SESSION['IP_num']);
		header("Location: index.php");
	}
	header("Location:index.php");
}
else if(isset($_SESSION['CIPA'])){
	unset($_SESSION['CIPA']);
	header("Location:index.php");
}

 ?>