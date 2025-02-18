<?php 
include("includes/connection.php");
session_start();

$role = $_GET['role'];

$output = "";

if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($connect, $_POST['username']);
    $password = mysqli_real_escape_string($connect, $_POST['password']);
    $c_pass = mysqli_real_escape_string($connect, $_POST['c_pass']);
	$program = mysqli_real_escape_string($connect, $_POST['program']);
    $error = array();

    if (empty($username)){
        $error[] = "Username is empty";
    } else if(empty($password)){
        $error[] = "Password is empty";
    } else if(empty($c_pass)){
        $error[] = "Confirm Password is empty";
    } else if($password != $c_pass){
        $error[] = "Both password do not match";
    }

    if (count($error) < 1) {
        
        $query = "INSERT INTO users(username, role, password, program) VALUES ('$username', '$role', '$password' , '$program')";
        $res = mysqli_query($connect, $query);

        if ($res) {
            $_SESSION['registration_success'] = "You have successfully registered! Log in to continue.";
        	header("Location:index.php");
        } else {
            $output = "Registration failed. Please try again.";
        }
    } else {
        $output = implode("<br>", $error);
    }
}


 ?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/login.css">
    <title>OJT Portal</title>
</head>
<body style="background: #ececec;">

    <!-- Main Container -->
    <div class="container d-flex justify-content-center align-items-center min-vh-100 custom-container">
        <!-- Login Container -->
        <div class="row border rounded-5 p-1 bg-white shadow box-area">
            <!-- Right Box -->
            <div class="col-md-12 right-box">
                <div class="row align-items-center">
                    <form method="post">
                        <div class="header-text mb-4 text-center">
                            <h3>Register as <?php echo $role?>!</h3>
                        </div>

                        <div class="alert alert-danger mb-4" role="alert" style="display: <?php echo !empty($output) ? 'block' : 'none'; ?>;">
                            <?php echo $output; ?>
                        </div>
                        
                        <div class="input-group mb-3">
                            <input type="text" name="username" class="form-control form-control-lg bg-light fs-6" placeholder="Enter Username" autocomplete="off" required>
                        </div>

                        <div class="input-group mb-3">
                            <input type="password" name="password" class="form-control form-control-lg bg-light fs-6" placeholder="Enter Password" autocomplete="off" required>
                        </div>

                        <div class="input-group mb-3">
                            <input type="password" name="c_pass" class="form-control form-control-lg bg-light fs-6" placeholder="Confirm Password"> 
                        </div>
                        
                        <?php if ($role === 'Adviser' || $role === 'Coordinator'): ?>
                        <div class="input-group mb-3">
                            <input type="text" name="program" class="form-control form-control-lg bg-light fs-6" placeholder="Enter Program" autocomplete="off" required>
                        </div>
                        <?php endif; ?>
                        
                        <div class="input-group mb-5">
                            <input type="submit" name="register" class="btn btn-success w-100 rounded-2" value="Register"><span class="fa fa-sign-in fw-fa"></span>
                        </div>

                        <div class="row text-center mt-5">
                            <small>Already registered? <a href="index.php">Log in</a></small>
                        </div>
                    </form>
                </div>
            </div> 
        </div>
    </div>

</body>
</html>