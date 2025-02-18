<?php 
function connection(){
$servername = "localhost";
$username = "root";
$password = "";
$database = "plmunoiedb";

$connect = mysqli_connect($servername,$username,$password,$database);

if(!$connect){
	die("connection failed: ". mysqli_connect_error());
	}
}


