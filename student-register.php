<?php
include("includes/connection.php");
session_start();

$output = "";

if (isset($_POST['register'])) {


	$studentID = $_POST['studentID'];
	$fullName = $_POST['fullName'];
	$email = $_POST['email'];
	$password = $_POST['password'];
	$c_pass = $_POST['c_pass'];

	// Split by comma first
	$parts = explode(',', $fullName);

	// Trim whitespace from each part
	$parts = array_map('trim', $parts);

	// Assign parts to variables safely
	$lastName = $parts[0] ?? '';
	$firstName = $parts[1] ?? '';

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

		$find = "SELECT * FROM student_masterlist WHERE studentID = '$studentID' AND lastName = '$lastName' AND firstName = '$firstName'";

		$courseQuery = "SELECT course FROM student_masterlist WHERE studentID = '$studentID'";
		$courseResult = mysqli_query($connect, $courseQuery);

		if ($courseResult->num_rows > 0) {
			$row = $courseResult->fetch_assoc();
			$course = $row['course'];
		}

		$departmentQuery = "SELECT department FROM course_list where course = '$course'";
		$departmentResult = mysqli_query($connect, $departmentQuery);

		if ($departmentResult->num_rows > 0) {
			$rows = $departmentResult->fetch_assoc();
			$department = $rows['department'];
		}

		$execute = mysqli_query($connect, $find);

		if ($execute && mysqli_num_rows($execute) > 0) {
			$check = "SELECT * FROM users WHERE username = '$email'";
			$execute = mysqli_query($connect, $check);

			if (mysqli_num_rows($execute) > 0) {
				$output = "<span class='text-danger'>Institutional Email is already in use</span>";
			}else{
				$insert = "INSERT INTO users (username, role, password, department) VALUES ('$email', 'Student', '$password', '$department')";
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

				<div class="input-group mb-3 position-relative">
  					<input type="text" name="fullName"
         				class="form-control form-control-lg bg-light fs-6"
         				placeholder="Enter Full Name"
         				autocomplete="off"
         				onfocus="showSubtext(this)"
         				onblur="hideSubtext(this)"
         			required>
  					<div class="form-text text-muted position-absolute subtext">
    					Format: Last Name, First Name
  					</div>
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

	<style>
  		.input-group {
    		position: relative;
  		}
  		.subtext {
    		position: absolute;
    		top: 100%;
    		left: 0;
    		display: none;
    		font-size: 0.875rem;
    		color: #6c757d;
    		margin-top: 4px;
    		z-index: 10;
  		}
  		.input-group.show-subtext .subtext {
    		display: block;
  		}
  		.input-group.show-subtext {
    		margin-bottom: 3rem !important;
  		}
	</style>

	<script>
  		function showSubtext(input) {
    		const group = input.closest('.input-group');
    		group.classList.add('show-subtext');
  		}

  		function hideSubtext(input) {
    		const group = input.closest('.input-group');
    		group.classList.remove('show-subtext');
  		}
	</script>
</body>
</html>