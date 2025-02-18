<?php
include("includes/connection.php");
session_start();

$output = "";

if (isset($_POST['register'])) {


	$studentID = $_POST['studentID'];
	$email = $_POST['email'];
	$password = $_POST['password'];
	$c_pass = $_POST['c_pass'];

	$error = array();

	if (empty($studentID) && empty($email)) {
		$error['error'] = "<span class='text-danger'>Please Enter Your Credentials</span>";
	} elseif (empty($studentID)) {
		$error['error'] = "<span class='text-danger'>Please Enter Your Student ID</span>";
	} else if (empty($email)) {
		$error['error'] = "<span class='text-danger'>Please Enter Your Institutional Email</span>";
	}else if(empty($password)){
		$error['error'] = "Please Enter Password";
	}else if(empty($c_pass)){
		$error['error'] = "Please Confirm Password";
	}else if($password != $c_pass){
		$error['error'] = "Both password do not match";
	}


	if (isset($error['error'])) {
		$output .= $error['error'];
	} else {
		$output .= "";
	}


	if (count($error) < 1) {

		$find = "SELECT * FROM student_masterlist WHERE studentID = '$studentID'";
		$execute = mysqli_query($connect, $find);

		if ($execute && mysqli_num_rows($execute) > 0) {
			$check = "SELECT * FROM users WHERE username = '$email'";
			$execute = mysqli_query($connect, $check);

			if (mysqli_num_rows($execute) > 0) {
				$output = "<span class='text-danger'>Institutional Email is already in use</span>";
			}else{
				$insert = "INSERT INTO users (username, role, password) VALUES ('$email', 'Student', '$password')";
				$execute = mysqli_query($connect,$insert);

				header("Location: student-portal/otp-verify.php?email=$email");
			}
		} else {
			$output = "<span class='text-danger'>Your Student Number is not yet in the Masterlist</span>";
		}
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
					<h3>Register as Student!</h3>
				</div>

				<div class="alert alert-danger mb-4" role="alert" style="display: <?php echo !empty($output) ? 'block' : 'none'; ?>;">
                    <?php echo $output; ?>
                </div>
				
				<div class="input-group mb-3">
					<input type="text" name="studentID" class="form-control form-control-lg bg-light fs-6" placeholder="Enter Student ID" autocomplete="off" required>
				</div>

				<div class="input-group mb-3">
					<input type="email" name="email" class="form-control form-control-lg bg-light fs-6" placeholder="Enter Institutional Email" autocomplete="off" required pattern=".+@plmun\.edu\.ph">
				</div>

				<div class="input-group mb-3">
					<input type="password" name="password" class="form-control form-control-lg bg-light fs-6" placeholder="Enter Password" autocomplete="off" required>
				</div>

				<div class="input-group mb-3">
					<input type="password" name="c_pass" class="form-control form-control-lg bg-light fs-6" placeholder="Confirm Password" autocomplete="off" required> 
				</div>
				
				<div class="input-group mb-5">

					<input type="submit" name="register" class="btn
					btn-success w-100 rounded-2" value="Register"><span class="fa
					fa-sign-in fw-fa"></span>

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