<?php
session_start();
include("includes/connection.php");

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
	$role = $_POST['role'];
	$pass = $_POST['pass'];

	if (empty($uname)) {
	} else if (empty($role)) {
	} else if (empty($pass)) {
	} else {

		$query = "SELECT * FROM users WHERE username='$uname' AND role='$role' AND password='$pass'";
		$res = mysqli_query($connect, $query);

		if (mysqli_num_rows($res) == 1) {

			if ($role == "CIPA") {

				$_SESSION['CIPA'] = $uname;
				header("Location: cipa-director-portal/student-interns.php");
			} else if ($role == "coordinator") {

				$_SESSION['coordinator'] = $uname;
				$stmt = $connect->prepare("SELECT department FROM users WHERE username = ?");
				$stmt->bind_param("s", $uname);
				$stmt->execute();
				$result = $stmt->get_result();

				// Check if the query returned any rows
				if ($result->num_rows > 0) {
					// Fetch the program value from the result set
					$row = $result->fetch_assoc();
					$department = $row['department'];

					// Assign the program value to the session variable
					$_SESSION['department'] = $department;
				} else {
					// Handle the case when the query returns no rows
					echo "No department found for the user.";
				}

				header("Location: coordinator-portal/masterlist.php");
			} else if ($role == "adviser") {
				$stmt = $connect->prepare("SELECT * FROM listadviser WHERE email = ?");
				$stmt->bind_param("s", $uname);
				$stmt->execute();

				$resultAdv = $stmt->get_result();

				if ($resultAdv->num_rows > 0) {
					$rowsAdv = $resultAdv->fetch_assoc(); // Fetch the row
					$_SESSION['adviser'] = $uname;
					$_SESSION['dept_adv'] = $rowsAdv['dept']; // Set department
					$_SESSION['dept_crs'] = $rowsAdv['course']; // Set course

					// Fetch sections
					$stmtSections = $connect->prepare("SELECT section FROM listadviser WHERE email = ?");
					$stmtSections->bind_param("s", $uname);
					$stmtSections->execute();
					$resultSections = $stmtSections->get_result();

					$sections = array();
					while ($row = $resultSections->fetch_assoc()) {
						$sections[] = $row['section'];
					}
					$_SESSION['dept_sec'] = $sections; // Store the array of sections

					// Redirect or perform further actions
					header("Location: adviser-portal/student-list.php");
					exit();
				} else {
					echo "Adviser not found in the database.";
				}
			} else if ($role == "Student") {

				$_SESSION['student'] = $uname;
			
				// Get the student's program
				$stmtStudent = $connect->prepare("SELECT department FROM users WHERE username = ?");
				$stmtStudent->bind_param("s", $uname);
				$stmtStudent->execute();
				$studentResult = $stmtStudent->get_result();
			
				if ($studentResult->num_rows > 0) {
					$rowsStudent = $studentResult->fetch_assoc();
					$department = $rowsStudent['department'];
					$_SESSION['department'] = $department;
				} else {
					echo "No Department found for the user";
				}

				$stmtCourse = $connect->prepare("SELECT course FROM studentinfo WHERE email = ?");
				$stmtCourse->bind_param("s", $uname);
				$stmtCourse->execute();
				$courseResult = $stmtCourse->get_result();

				if ($courseResult->num_rows > 0) {
					$rowsCourse = $courseResult->fetch_assoc();
					$course = $rowsCourse['course'];
					$_SESSION['course'] = $course;
				}
			
				header("Location: student-portal/student.php");
			}
			 else if ($role == "IndustryPartner") {

				$_SESSION['IndustryPartner'] = $uname;

				$stmtCompany = $connect->prepare("SELECT companyName FROM users WHERE username = ?");
				$stmtCompany->bind_param("s", $uname);
				$stmtCompany->execute();
				$companyNameResult = $stmtCompany->get_result();

				if ($companyNameResult->num_rows > 0) {
					$rowsCompanyName = $companyNameResult->fetch_assoc();
					$companyName = $rowsCompanyName['companyName'];
					$_SESSION['companyName'] = $companyName;
				}



				$sql = "SELECT companyCode FROM company_info WHERE trainerEmail = ?";
				// Prepare statement
				$stmt = $connect->prepare($sql);
				$stmt->bind_param("s", $uname);
				// Execute the query
				$stmt->execute();
				// Bind the result to a variable
				$stmt->bind_result($companyCode);

				// Fetch the result
				if ($stmt->fetch()) {
					// Here, $companyCode contains the companyCode you want
					// Assign it to the session IP number
					$_SESSION['IP_num'] = $companyCode;
				} else {
					echo "No companyCode found for the given uname.";
				}

				// Close the statement and connection
				$stmt->close();
				$connect->close();
				header("Location: industry-portal/industryPartner.php");
			}

			$output .= "you have logged-In";
		} else {
			$output .= "Failed to login";
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
	<link rel="stylesheet" href="assets/css/login.css">
	<title>OJT Portal</title>
</head>

<body>

	<!-- Main Container -->

	<div class="container d-flex justify-content-center align-items-center min-vh-100">

		<!-- Login Container -->

		<div class="row border rounded-5 p-4 bg-white shadow box-area">

			<!-- Left Box -->

			<div class="col-md-6 rounded-4 d-flex justify-content-center align-items-center flex-column left-box" style="background: #104911;">
				<div class="featured-image mb-3">
					<img src="img/logo2.png" class="img-fluid" style="width: 200px;">
				</div>
				<p class="text-white fs-2" style="font-weight: 600;">OJT Portal</p>
			</div>

			<!-- Right Box -->

			<div class="col-md-6 right-box">
				<div class="row align-items-center">
					<form method="post">
						<div class="header-text mb-2">
							<h2>Hello!</h2>
							<p>Welcome to PLMUN OJT Portal</p>
						</div>
						<div class="input-group mb-3">
							<input type="text" name="uname" class="form-control form-control-lg bg-light fs-6" placeholder="Email address">
						</div>
						<div class="input-group mb-3">
							<input type="password" name="pass" class="form-control form-control-lg bg-light fs-6" placeholder="Password">
						</div>

						<div class="input-group mb-3">
							<select name="role" class="form-control my-2">
								<option hidden disable value="select Role">Select Role</option>
								<option value="Student">Student</option>
								<option value="adviser">Adviser</option>
								<option value="coordinator">OJT Coordinator</option>
								<option value="CIPA">CIPA</option>
								<option value="IndustryPartner">Industry Partner</option>
							</select>
						</div>
						<div class="input-group mb-3">

							<input type="submit" name="login" class="btn
						btn-success w-100 rounded-2" value="Login"><span class="fa
						fa-sign-in fw-fa"></span>

						</div>

						<div class="row">
							<small>Don't have an account? <a href="student-register.php">Sign Up</a></small>
						</div>
					</form>
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