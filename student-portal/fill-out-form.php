<?php
session_start();
include_once("../includes/connection.php");
?>
<!DOCTYPE html>

<html>

<head>

	<?php include("../elements/meta.php"); ?>
	<title>Student Information</title>
	<?php include("embed.php"); ?>

</head>

<body>

	<div class="container">
		<div class="col-md-12 my-3">
			<div class="card shadow-sm px-5">
				<div class="py-5">
					<form id="studentForm" action="FOF.php" method="POST" enctype="multipart/form-data">
						<div class="text-center"></div>
						<h2 class="mb-5 text-center">PERSONAL INFORMATION</h2>
						<div class="row">
							<div class="col-6">
								<div class="col-md-6 d-flex justify-content-center mx-auto">
									<img class="img-profile rounded-circle" id="default" src="../img/undraw_profile.svg">
								</div>
								<div class="col-md-6 d-flex justify-content-center mx-auto pt-2 mb-4">
									<label for="photo" class="btn btn-primary pt-2">Upload Photo</label>
									<input type="file" id="photo" name="photo" accept="image/jpg , image/png, image/jpeg" class="form-control-file" required hidden>
								</div>
								<div class="border rounded pt-2">
									<div class="p-3 ml-3">
										<label for="resume">Resume:</label>
										<input type="file" name="resume" class="form-control-file mb-3" accept="application/pdf" required>
									</div>

								</div>
								<div class="pt-3">
									<input type="number" name="studentID" class="form-control" placeholder="Enter your Student ID" any value="" required>
								</div>


								<div class="pt-3">
									<input type="firstname" class="form-control" id="firstname" name="firstname" placeholder="Firstname" onkeyup="javascript:capitalize(this.id, this.value);" any value="" required>
								</div>
								<div class="pt-3">
									<input type="middlename" class="form-control" id="middlename" name="middlename" placeholder="Middle Name" onkeyup="javascript:capitalize(this.id, this.value);" any value="" required>
								</div>
								<div class="pt-3">
									<input type="lastname" class="form-control" id="lastname" name="lastname" placeholder="Lastname" onkeyup="javascript:capitalize(this.id, this.value);" any value="" required>
								</div>
								<div class="row pt-3">
									<div class="col">
										<input type="number" class="form-control input-sm" id="age" name="age" placeholder="Age" any value="" required>
									</div>
									<div class="col">
										<select class="form-control" name="gender" id="gender" any value="" required>
											<option hidden disable value="select">Gender</option>
											<option value="female">Female</option>
											<option value="male">Male</option>
										</select>
									</div>
								</div>

								<div class="pt-3">
									<input type="number" class="form-control" id="contactNo" name="contactNo" placeholder="Contact No." any value="" required>
								</div>
								<div class="pt-3">
									<textarea type="address" class="form-control" id="address" name="address" placeholder="Address" any value="" required></textarea>
								</div>
							</div>
							<div class="col-6">
								<h4 class="mb-4">Educational Info</h4>
								<div class="row">
									<div class="col">
										<label for="department">College:</label>
										<select class="form-control" name="department" id="dropdowndept" onchange="showCourses(this.value);" required>
											<option value="" selected disabled>Select Department</option>
											<?php
											$queryDept = "select * from department_list";
											$resultDept = mysqli_query($connect, $queryDept);
											while ($rowDept = mysqli_fetch_assoc($resultDept)) {
											?>
												<option value="<?php echo $rowDept['department']; ?>"><?php echo $rowDept['department']; ?></option>
											<?php
											}
											?>
										</select>
									</div>
									<div class="col">
										<label for="course">Course:</label>
										<select class="form-control" name="course" id="dropdowncourse" onchange="showSections($('#dropdowndept').val(), this.value);" required>
											<option value="" selected disabled>Select Course</option>
										</select>
									</div>
								</div>
								<div class="row">
									<div class="col">
										<label for="section">Section:</label>
										<select class="form-control" name="section" id="dropdownsection" required>
											<option value="" selected disabled>Select Section</option>
										</select>
									</div>
									<div class="col">
										<label for="email">Institutional Email:</label>
										<input type="email" name="email" class="form-control" placeholder="Enter Institutional Email" autocomplete="off" required pattern=".+@plmun\.edu\.ph">
									</div>
								</div>
								<div class="row pb-4 mb-4">
									<div class="col">
										<label for="SY">School Year:</label>
										<input type="SY" name="SY" class="form-control" placeholder="Enter School Year" autocomplete="off" required>
									</div>
									<div class="col">
										<label for="semester">Semester:</label>
										<select class="form-control" name="semester" id="select3">
											<option hidden disable value="select">Select</option>
											<option value="1st semester">1st semester</option>
											<option value="2nd semester">2nd semester</option>
										</select>
									</div>
								</div>
								<h4 class="mb-4">Summary</h4>
								<div class="form-group">
									<label for="objective">Objective</label>
									<textarea class="form-control" name="objective" id="objective" rows="3" placeholder="Write a brief statement to summarize your goals and objectives."></textarea>
								</div>
								<div class="form-group">
									<label for="skills">Skills</label>
									<textarea class="form-control" name="skills[]" id="skills" rows="3" placeholder="Separate your skills with commas. eg. (Critical Thinking, Problem Solving, Time Management)"></textarea>
								</div>
								<div class="form-group">
									<label for="seminars">Seminars Attended</label>
									<textarea class="form-control" name="seminars[]" id="seminars" rows="3" placeholder="Write the seminars you've attended, separating your entries with commas. eg. CITCS 13th IT Summit 2023"></textarea>
								</div>
								<div class="row">
									<div class="col text-right">
										<button type="button" id="submitButton" class="btn btn-success" name="Save"><span class="fas fa-save fw-fa"></span> Submit</button>

									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script>
		function capitalize(id) {
			var input = document.getElementById(id);
			var words = input.value.split(' ');
			for (var i = 0; i < words.length; i++) {
				if (words[i].length > 0) {
					words[i] = words[i].charAt(0).toUpperCase() + words[i].slice(1);
				}
			}
			input.value = words.join(' ');
		}

		function showCourses(department) {
			$.ajax({
				url: 'functions/get_courses.php',
				type: 'GET',
				data: {
					department: department
				},
				success: function(data) {
					document.getElementById('dropdowncourse').innerHTML = data;
				}
			});
		}

		function showSections(department, course) {
			$.ajax({
				url: 'functions/get_sec.php',
				type: 'GET',
				data: {
					department: department,
					course: course
				},
				success: function(data) {
					document.getElementById('dropdownsection').innerHTML = data;
				}
			});
		}

		function validateForm() {
			var requiredFields = document.querySelectorAll('[required]');
			for (var i = 0; i < requiredFields.length; i++) {
				if (!requiredFields[i].value) {
					return false;
				}
			}
			return true;
		}

		// Intercept form submission
		document.getElementById("submitButton").addEventListener("click", function() {
			if (!validateForm()) {
				Swal.fire({
					title: 'Oops!',
					text: 'Please fill in all required fields.',
					icon: 'error'
				});
				return;
			}

			Swal.fire({
				title: 'Are you sure?',
				html: '<div style="font-size: 18px;">Please review your information before submitting.<br><br><b>Data Privacy Warning:</b> By submitting this form, you acknowledge that your personal information will be processed in accordance with our privacy policy.</div>',
				icon: 'info',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Yes, submit!'
			}).then((result) => {
				if (result.isConfirmed) {
					// If user confirms, submit the form
					document.getElementById("studentForm").submit();
				}
			});
		});

		//For User Photo Edit
		$(document).ready(function() {
			let profilePic = document.getElementById("default");
			let inputFile = document.getElementById("photo");

			inputFile.onchange = function() {
				profilePic.src = URL.createObjectURL(inputFile.files[0]);
			}
		});
	</script>
</body>

</html>