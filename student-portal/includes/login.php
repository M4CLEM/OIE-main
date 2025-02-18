<?php
include 'db_connect.php';
session_start();

if(isset($_POST['submit'])) {

  $uname = $_POST['uname'];
  $role = $_POST['role'];
  $pass =  $_POST['pass']; 
  

  $sql = "";
  $session_prefix = "";

  if ($role == 'Student') {

    $sql = "SELECT * FROM users WHERE username = '$uname' AND role='$role' AND password = '$pass'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {

      $_SESSION['student'] = $uname;
      header("location: ../main.php");

    } else {
      echo '<script> window.location.href = "../index.php";
            alert("Login failed. Invalid username or password!")
            </script>';
    }

  } elseif ($role == 'Adviser') {

    $sql = "SELECT * FROM users WHERE username = '$uname' AND role='$role' AND password = '$pass'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {

      $row = mysqli_fetch_assoc($result);
      $_SESSION['adviser'] = $uname;

      header("location: ../adviser.php");

    } else {
      echo '<script> window.location.href = "../index.php";
            alert("Login failed. Invalid username or password!")
            </script>';
    }

  } elseif ($role == 'Coordinator') {

    $sql = "SELECT * FROM users WHERE username = '$uname' AND role='$role' AND password = '$pass'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {

      $row = mysqli_fetch_assoc($result);
      $_SESSION['coord'] = $uname;

      header("Location: http://localhost/OJT/USER_OJT/coord.php");

    } else {

      echo '<script> window.location.href = "../index.php";
            alert("Login failed. Invalid username or password!")
            </script>';
  
    }

  } elseif ($role == 'CIPA') {

    $sql = "SELECT * FROM users WHERE username = '$uname' AND role='$role' AND password = '$pass'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {

      $row = mysqli_fetch_assoc($result);
      $_SESSION['cipa'] = $uname;

      header("location: ../cipa.php");

    } else {

      echo '<script> window.location.href = "../index.php";
            alert("Login failed. Invalid username or password!")
            </script>';
  
    }
  }
}



?>
