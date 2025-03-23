<?php
    session_start();
    include("../includes/connection.php");
    
    $trainerEmail = $_SESSION['trainerEmail'];
    $companyName = $_SESSION['companyName'];

    // Query to fetch data from both tables based on studentID
    $studentQuery = "
        SELECT ci.studentID, 
               ci.jobrole,
               CONCAT(si.firstname, ' ', si.lastname) AS fullStudentName
        FROM company_info ci
        LEFT JOIN studentinfo si ON ci.studentID = si.studentID
        WHERE ci.trainerEmail = '$trainerEmail'
    ";
    $studentResult = mysqli_query($connect, $studentQuery);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include("../elements/meta.php"); ?>
        <title>INDUSTRY PARTNER PORTAL</title>
        <?php include("embed.php"); ?>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    </head>
    <body id="page-top">
        <div id="wrapper">
            <!--Sidebar Wrapper-->
            <aside id="sidebar" class="expand">
                <?php include('../elements/ip_sidebar.php') ?>
            </aside>

            <div class="main">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 static-top shadow">
                    <!-- Title -->
                    <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Grading</h4>
                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <div class="topbar-divider d-none d-sm-block"></div>
                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img class="img-profile rounded-circle" src="../img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Settings
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Activity Log
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="../logout.php" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
                <div class="row m-1">
                    <div class="col-md-6 mb-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-dark">Student Grades</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="small" width="20%">STUDENT NO.</th>
                                                <th scope="col" class="small">NAME</th>
                                                <th scope="col" class="small">JOBROLE</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                while ($row = mysqli_fetch_assoc($studentResult)){ ?>
                                                    <tr>
                                                        <td>
                                                            <a href="#" class="studentNumber-link" data-studentnumber="<?php echo $row['studentID'];?>"><?php echo $row['studentID'];?></a>
                                                        </td>
                                                        <td>
                                                            <p><?php echo $row['fullStudentName']?></p>
                                                        </td>
                                                        <td>
                                                            <p><?php echo $row['jobrole']?></p>
                                                        </td>
                                                    </tr>
                                            <?php
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-dark">STUDENT INFORMATION</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <div>
                                            <label for="stud_id" class="small">Student No:</label>
                                            <p class="small font-weight-bold" id="stud_id"></p>
                                        </div>
                                        <div>
                                            <label for="surname" class="small">Surname:</label>
                                            <p class="small font-weight-bold" id="surname"></p>
                                        </div>
                                        <div>
                                            <label for="companyName" class="small">Company:</label>
                                            <p class="small font-weight-bold" id="companyName"></p>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div>
                                            <label for="section" class="small">Section:</label>
                                            <p class="small font-weight-bold" id="section"></p>
                                        </div>
                                        <div>
                                            <label for="firstName" class="small">First Name:</label>
                                            <p class="small font-weight-bold" id="firstName"></p>
                                        </div>
                                        <div>
                                            <label for="jobrole" class="small">Jobrole:</label>
                                            <p class="small font-weight-bold" id="jobrole"></p>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div>
                                            <label for="program" class="small">Program:</label>
                                            <p class="small font-weight-bold" id="program"></p>
                                        </div>
                                        <div>
                                            <label for="midName" class="small">Middle Name:</label>
                                            <p class="small font-weight-bold" id="midName"></p>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <form action="functions/submit_grade.php" id="criteriaForm" method="POST">
                                        <div class="row">
                                            <label for="criteriaContainer" class="text-center small font-weight-bold border-0">Grading</label>
                                        </div>
                                        <div id="criteriaContainer">
                                            <p class="text-center p-4">Criteria will be displayed here</p>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <!-- Submit button will be shown if no grades are in the database -->
                                            <button type="submit" class="btn btn-success" id="submitButton">
                                                <span class="fas fa-save fw-fa"></span> Submit Grade
                                            </button>

                                            <!-- Save button will be shown if grades are already in the database -->
                                            <button type="submit" class="btn btn-primary" id="saveButton" style="display: none;">
                                                <span class="fas fa-save fw-fa"></span> Save Grade
                                            </button>
                                            <div class="col-4">
                                                <label class="sr-only" for="totalGrade">Total Grade</label>
                                                <div class="input-group mb-2 mr-sm-2">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">Total</div>
                                                    </div>
                                                    <input type="number" id="totalGrade" name="totalGrade" class="form-control" min="0" max="100" oninput="distributeTotalGrade()" required>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Logout Modal-->
                <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                                <a class="btn btn-primary" href="../logout.php">Logout</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
        <script src="../assets/js/sidebarscript.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

        <script>
            $(document).ready(function() {
                let criteriaInputs = null;
                let totalGradeInput = document.getElementById('totalGrade');
                let totalGradeHiddenInput = document.getElementById('totalGradeInput');

                // Function to fetch and update student information
                function fetchAndUpdateStudentInfo(studentID) {
                    // AJAX call to fetch student information based on studentID
                    $.ajax({
                        url: 'functions/fetch_stud_info_grading.php', // Provide the path to your PHP script that fetches student information
                        method: 'POST',
                        data: {
                            studentID: studentID
                        },
                        success: function(response) {
                            // Check if the response is an empty array (not registered yet)
                            if (response.trim() === '[]') {
                                // Show SweetAlert indicating that the student is not registered yet
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Student Not Registered',
                                    text: 'The student with the provided ID has no information registered.',
                                    showConfirmButton: false,
                                    toast: true,
                                    position: 'top',
                                    timer: 2000
                                });
                                $('#stud_id').text('');
                                $('#surname').text('');
                                $('#firstName').text('');
                                $('#midName').text('');
                                $('#section').text('');
                                $('#program').text('');
                            } else {
                                // Parse the response as JSON
                                var studentInfo = JSON.parse(response);
                                // Update the student information fields with the fetched data
                                $('#stud_id').text(studentInfo.studentID);
                                $('#surname').text(studentInfo.lastName);
                                $('#firstName').text(studentInfo.firstName);
                                $('#midName').text(studentInfo.middleName);
                                $('#section').text(studentInfo.section);
                                $('#program').text(studentInfo.program);
                            }
                        }
                    });
                }

                function fetchAdviserStudentCriteria(studentID) {
                    $.ajax({
                        url: 'functions/fetch_criteria.php',
                        method: 'POST',
                        data: {
                            studentID: studentID
                        },
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        success: function(response) {
                            try {
                                var criteria = response.criteria;
                                var gradeData = response.gradeData;
                                var companyName = response.companyName;
                                var jobrole = response.jobrole;

                                console.log("Company Name:", companyName);
                                console.log("Jobrole:", jobrole);
                                console.log("Parsed Criteria:", criteria);  // Debugging: check parsed criteria
                                console.log("Parsed Grade Data:", gradeData);  // Debugging: check parsed grade data

                                $('#companyName').text(companyName);
                                $('#jobrole').text(jobrole);

                                if (!Array.isArray(criteria)) {
                                    throw new Error("Response is not an array");
                                }

                                renderCriteria(criteria, gradeData);
                                initializeInputs(); // Initialize inputs after criteria are rendered
                
                                if (gradeData && gradeData.finalGrade !== null) {
                                    // Set the total grade from the backend if available
                                    document.getElementById("totalGrade").value = gradeData.finalGrade;

                                    // Change the submit button to save button if grades are available
                                    document.getElementById("submitButton").style.display = 'none';
                                    document.getElementById("saveButton").style.display = 'inline-block';
                                } else {
                                    // Show submit button if no grades are available
                                    document.getElementById("submitButton").style.display = 'inline-block';
                                    document.getElementById("saveButton").style.display = 'none';
                                }

                            } catch (e) {
                                console.error("JSON Parsing Error:", e);
                                $("#criteriaContainer").html("<p class='text-center text-danger'>Error fetching criteria.</p>");
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX Error:", status, error);  // Debugging: log AJAX error
                            console.error("Response Text:", xhr.responseText);
                            $("#criteriaContainer").html("<p class='text-center text-danger'>Error fetching criteria.</p>");
                        }
                    });
                }

                // Function to render criteria dynamically
                function renderCriteria(criteria, gradeData) {
                    var criteriaContainer = document.getElementById('criteriaContainer');
                    criteriaContainer.innerHTML = '';

                    if (criteria.length === 0) {
                        criteriaContainer.innerHTML = "<p class='text-center p-4'>No criteria found for this student.</p>";
                        return;
                    }

                    criteria.forEach(function(criteriaItem) {
                        // Check if grade data exists for this criteria
                        var grade = gradeData && gradeData.grades && gradeData.grades[criteriaItem.criteria] ? gradeData.grades[criteriaItem.criteria] : 0;
        
                        var criteriaHtml = `
                        <div class='p-3 mb-2 border rounded'>
                            <div class='row'>
                                <div class='col-md-8'>
                                    <h6 data-id="${criteriaItem.id}">${criteriaItem.criteria}</h6>
                                    <p class="small"><i>${criteriaItem.description}</i></p>
                                </div>
                                <div class='col-md-4'>
                                    <input type="hidden" name="criteria[${criteriaItem.criteria}][criteria]" value="${criteriaItem.criteria}">
                                    <input type="hidden" name="criteria[${criteriaItem.criteria}][description]" value="${criteriaItem.description}">
                                    <input type="hidden" name="criteria[${criteriaItem.criteria}][percentage]" value="${criteriaItem.percentage}">
                                    <label class="sr-only" for="displayValue${criteriaItem.id}">Grade</label>
                                    <div class="input-group input-group-sm mb-2 mr-sm-2">
                                        <input type="number" required class="form-control custom-number-input" min="0" max="${criteriaItem.percentage}" value="${grade}" name="grade[${criteriaItem.criteria}]" id="displayValue${criteriaItem.id}" oninput="enforceMaxLimit(this, '${criteriaItem.id}')" data-percentage="${criteriaItem.percentage}">
                                        <div class="input-group-append">
                                            <div class="input-group-text">${criteriaItem.percentage}%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>`;

                        criteriaContainer.innerHTML += criteriaHtml;
                    });

                    // If final grade exists, display it
                    if (gradeData && gradeData.finalGrade !== null) {
                        var finalGradeHtml = `
                            <div class="p-3 mb-2 border rounded">
                                <h6>Final Grade: ${gradeData.finalGrade}</h6>
                            </div>`;
                        criteriaContainer.innerHTML += finalGradeHtml;
                    }
                }

                // Event listener for student links
                $(document).on('click', '.studentNumber-link', function(e) {
                    e.preventDefault();
                    var studentID = $(this).data('studentnumber');
                    //console.log(studentID);
                    // Show SweetAlert2 alert with loading animation
                    Swal.fire({
                        title: 'Please Wait...',
                        showConfirmButton: false,
                        position: 'top',
                        toast: true,
                        willOpen: () => {
                        Swal.showLoading();
                        },
                        didOpen: () => {
                            setTimeout(() => {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Student Information Loaded',
                                    position: 'top',
                                    toast: true,
                                    showConfirmButton: false,
                                    timer: 2000
                                });
                                fetchAndUpdateStudentInfo(studentID);
                                fetchAdviserStudentCriteria(studentID)
                            }, 2000); // Simulated loading delay of 2 seconds
                        }
                    });
                });

                // Initialize event listeners for input elements
                function initializeInputs() {
                    criteriaInputs = document.querySelectorAll('.custom-number-input');
                        criteriaInputs.forEach(function(input) {
                            input.addEventListener('input', function() {
                                enforceMaxLimit(input, input.id.replace('displayValue', ''));
                                updateTotal();
                            });
                        });

                        // Update total grade dynamically
                        function updateTotal() {
                            if (!criteriaInputs) return;

                            var total = 0;
                            criteriaInputs.forEach(function(input) {
                                var value = parseInt(input.value);
                                if (!isNaN(value)) {
                                    total += value;
                                }
                            });

                            totalGradeInput.value = total;
                            if (totalGradeHiddenInput) {
                                totalGradeHiddenInput.value = total; // Update hidden total grade input
                            }
                        }

                        // Update hidden input values when criteria changes
                        function updateValue(value, criteriaId) {
                            var hiddenInput = document.getElementById('hiddenInputForCriteria' + criteriaId);
                            if (hiddenInput) {
                                hiddenInput.value = value;
                            }
                        }

                        // Ensure input values stay within allowed range
                        function enforceMaxLimit(input, criteriaId) {
                            var maxValue = parseInt(input.max);
                            var value = parseInt(input.value);
                            if (value < 0) {
                                input.value = 0;
                                updateValue(0, criteriaId);
                            } else if (value > maxValue) {
                                input.value = maxValue;
                                updateValue(maxValue, criteriaId);
                            } else {
                                updateValue(value, criteriaId);
                            }
                        }

                        // Distribute total grade across criteria inputs
                        function distributeTotalGrade() {
                            var totalGradeValue = totalGradeInput.value.trim();

                            if (totalGradeValue === "") {
                                totalGradeInput.value = "0";
                                criteriaInputs.forEach(input => input.value = "0");
                                return;
                            }

                            var totalGrade = parseInt(totalGradeValue);
                            if (isNaN(totalGrade)) totalGrade = 0;
                            totalGrade = Math.max(0, Math.min(100, totalGrade));

                            totalGradeInput.value = totalGrade;

                            // Initialize variables for distributing points across criteria
                            var remainingPoints = totalGrade;
                            var criteriaPoints = [];

                            // First, calculate the points for each criterion based on its percentage
                            criteriaInputs.forEach(function(input) {
                                var maxValue = parseInt(input.dataset.percentage); // Get the percentage max from data-percentage
                                var pointsForThisCriterion = Math.floor((maxValue / 100) * totalGrade); // Calculate based on percentage
                                pointsForThisCriterion = Math.min(pointsForThisCriterion, maxValue); // Ensure it doesn't exceed maxValue
                                criteriaPoints.push(pointsForThisCriterion);
                                remainingPoints -= pointsForThisCriterion;
                            });

                            // Second pass: Distribute any remaining points (if any)
                            for (var i = 0; i < criteriaInputs.length && remainingPoints > 0; i++) {
                                var input = criteriaInputs[i];
                                var maxValue = parseInt(input.dataset.percentage); // Max percentage value for this criterion
                                var currentPoints = criteriaPoints[i];
        
                                // If there are leftover points, add them without exceeding the max limit
                                if (currentPoints < maxValue) {
                                    var additionalPoints = Math.min(remainingPoints, maxValue - currentPoints);
                                    criteriaPoints[i] += additionalPoints;
                                    remainingPoints -= additionalPoints;
                                }
                            }

                            // Update the input fields and hidden values
                            criteriaInputs.forEach(function(input, index) {
                                input.value = criteriaPoints[index];
                                var criteriaId = input.id.replace('displayValue', '');
                                updateValue(criteriaPoints[index], criteriaId); // Update hidden value
                            });

                            updateTotal(); // Make sure to update the total grade or any other summary field
                        }


                        // Add event listener to trigger distributeTotalGrade when typing in totalGrade field
                        totalGradeInput.addEventListener('input', function() {
                            distributeTotalGrade();
                        });

                        // Add event listener to distribute total grade after finishing input (blur event)
                        totalGradeInput.addEventListener('blur', function() {
                            distributeTotalGrade();
                        });
                }

                // Submit Grade (for the submit button)
                    $("#submitButton").click(function(event) {
                        event.preventDefault(); // Prevent default form submission

                        var studentID = $("#stud_id").text().trim();
                        var companyName = $("#companyName").text().trim();
                        var jobrole = $("#jobrole").text().trim();

                        console.log("Student ID:", studentID);
                        console.log("Company Name:", companyName);
                        console.log("Job Role:", jobrole);

                        if (!studentID) {
                            Swal.fire({
                                title: "Error",
                                text: "Student ID not found. Please ensure the student is selected.",
                                icon: "error",
                            });
                            return;
                        }

                        Swal.fire({
                            title: "Are you sure?",
                            text: "You won't be able to revert this!",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#3085d6",
                            cancelButtonColor: "#d33",
                            confirmButtonText: "Yes, submit it!",
                        }).then((result) => {
                            if (result.isConfirmed) {
                                let formData = $("#criteriaForm").serialize(); // Serialize form data
                                formData += "&studentID=" + encodeURIComponent(studentID) +
                                "&companyName=" + encodeURIComponent(companyName) +
                                "&jobrole=" + encodeURIComponent(jobrole);

                                console.log("Final Form Data (Before Sending):", formData); // Log final data before sending

                                $.ajax({
                                    url: $("#criteriaForm").attr("action"),
                                    type: "POST",
                                    data: formData,
                                    dataType: "json",
                                    success: function(response) {
                                        console.log("Response from server:", response);

                                        if (response.status === "success") {
                                            // Success case
                                            Swal.fire({
                                                title: "Success!",
                                                text: response.message,  // Using the 'message' from backend response
                                                icon: "success",
                                            }).then(() => {
                                                $("#successMessage").text(response.message).show(); // Optionally, show success message on page
                                            });
                                        } else if (response.error) {
                                            // Error case
                                            Swal.fire({
                                                title: "Error!",
                                                text: response.error,  // Using the 'error' from backend response
                                                icon: "error",
                                            });
                                        } else {
                                            // If the response structure is not as expected
                                            Swal.fire({
                                                title: "Error!",
                                                text: "Unexpected response from server.",
                                                icon: "error",
                                            });
                                        }
                                    },
                                    error: function(jqXHR, textStatus, errorThrown) {
                                        console.log("AJAX error:", textStatus, errorThrown);
                                        Swal.fire({
                                            title: "Error!",
                                            text: "Failed to submit the grade. Please try again.",
                                            icon: "error",
                                        });
                                    },
                                });

                            }
                        });
                    });

                    // Update Grade (for the save button)
                    $("#saveButton").click(function(event) {
                        event.preventDefault(); // Prevent default form submission

                        var studentID = $('#stud_id').text(); // Assuming the studentID is displayed in this field

                        if (!studentID) {
                            Swal.fire({
                                title: 'Error',
                                text: 'Student ID not found. Please ensure the student is selected.',
                                icon: 'error'
                            });
                            return;
                        }

                        Swal.fire({
                            title: 'Are you sure?',
                            text: "You won't be able to revert this!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, save it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Proceed with form submission via AJAX
                                let formData = $("#criteriaForm").serialize(); // Serialize form data
                                formData += '&studentID=' + studentID; // Append studentID to form data

                                $.ajax({
                                    url: 'functions/update_grade.php', // The update grade script URL
                                    type: "POST",
                                    data: formData,
                                    dataType: "json",
                                    success: function(response) {
                                        console.log('Response from server:', response); // Log the full response object

                                        // Check if status is success
                                        if (response.status === 'success') {
                                            console.log('Success block triggered');
                                            Swal.fire({
                                                title: 'Success!',
                                                text: response.message, // Display the 'message' field from the response
                                                icon: 'success'
                                            }).then(() => {
                                                $('#successMessage').text(response.message).show();
                                            });
                                        } else {
                                            console.log('Error block triggered');
                                            Swal.fire({
                                                title: 'Error!',
                                                text: response.message || 'Something went wrong.', // Use 'message' instead of 'error'
                                                icon: 'error'
                                            });
                                        }
                                    },
                                    error: function(jqXHR, textStatus, errorThrown) {
                                        console.log('AJAX error:', textStatus, errorThrown); // Log any AJAX errors
                                        Swal.fire({
                                            title: 'Error!',
                                            text: 'Failed to save the grade. Please try again.',
                                            icon: 'error'
                                        });
                                    }
                                });
                            }
                        });
                    });
            })
        </script>
    </body>
</html>