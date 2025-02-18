<?php 
    $id = $_POST['id'];
    $username = $_POST['username'];
    $role = $_POST['role'];
    $password = $_POST['password'];

    $connect = new mysqli('localhost', 'root', '', 'plmunoiedb');
    if($connect->connect_error){
        die('connection failed : '.$connect->connect_error);
    }else{
        $stmt = $connect->prepare("INSERT INTO users(id,username,role,password) VALUES(?,?,?,?)");
        $stmt->bind_param('isss',$id, $username, $role, $password);
        $stmt->execute();
        header("Location:../manage-user.php");
        $stmt->close();
        $connect->close();
    }
?>
