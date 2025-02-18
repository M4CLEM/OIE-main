<?php 
session_start();
include("../includes/connection.php");

if (isset($_SESSION['registration_success'])) {
    echo '<div id="registrationSuccess" class="alert alert-success ml-5" role="alert">';
    echo '<button type="button" class="close" onclick="dismissSuccessMessage()">&times;</button>';
    echo $_SESSION['registration_success'];
    echo '</div>';
    // Optionally, unset the session variable to prevent the message from showing again on page refresh
    unset($_SESSION['registration_success']);
}

$output = "";

if (isset($_POST['login'])) {
	
	$uname = $_POST['uname'];


	if (empty($uname)) {
	
	}else{

		$query = "SELECT * FROM company_info WHERE companyCode='$uname'";
		$res = mysqli_query($connect,$query);

		if (mysqli_num_rows($res) == 1) {

			$_SESSION['IP_num'] = $uname;
			header("Location: grading.php");
			
		}else{
			$output .= "Failed to login";
			echo $output;
		}
	}
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/login.css">
    <title>OJT Portal</title>
</head>
<body>

    <!-- Main Container -->
	<div class="container d-flex justify-content-center align-items-center min-vh-100">

	<div class="container">

			<!-- Login Container -->
			<div class="row justify-content-center">

				<div class="col-md-7 border rounded-5 p-4 bg-white shadow" >

					<div class="col-md-12 rounded-4 d-flex justify-content-center align-items-center flex-column left-box p-5" style="background: #104911;">
						<div class="featured-image">
							<img src="../img/logo2.png" class="img-fluid" style="width: 200px;">
						</div>
						<p class="text-white fs-2" style="font-weight: 600;">Industry Partner Portal</p>
					</div>

					<div class="row align-items-center">
						<form method="post">
							<div class="input-group mb-3 mt-4">
								<input type="text" name="uname" class="form-control form-control-lg bg-light fs-6" placeholder="Enter Company Code">
							</div>
							
							<div class="input-group mb-3">
								<input type="submit" name="login" class="btn btn-success w-100 rounded-2" value="Login"><span class="fa fa-sign-in fw-fa"></span>
							</div>
						</form>
					</div>
				</div> 
			</div>
		</div>
	</div>


</body>
</html>

<script>
function dismissSuccessMessage() {
    document.getElementById('registrationSuccess').style.display = 'none';
}
</script>