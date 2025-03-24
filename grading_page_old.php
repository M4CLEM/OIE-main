<?php
session_start();
include_once("includes/connection.php");

// Check if student_id parameter exists in the URL
if (isset($_GET['student_id'])) {
    // Get the student_id from the URL
    $student_id = $_GET['student_id'];

    // Prepare the query to select data from both tables using the student_id
    $query = "SELECT sm.*, si.* FROM student_masterlist sm JOIN studentinfo si ON sm.studentID = si.studentID WHERE sm.studentID = ? AND si.studentID = ?";

    // Prepare the statement
    $stmt = mysqli_prepare($connect, $query);

    // Bind the parameter
    mysqli_stmt_bind_param($stmt, "ss", $student_id, $student_id);

    // Execute the statement
    mysqli_stmt_execute($stmt);

    // Get the result
    $result = mysqli_stmt_get_result($stmt);

    // Fetch data from the result
    $row = mysqli_fetch_assoc($result);
    $_SESSION['IP_num'] = $row['companyCode'];
    if (!$row) {
        // If no data found for the provided student_id
        echo "<h1>No student found with the provided ID</h1>";
        exit; // Stop further execution
    }
} else {
    // If student_id parameter is not provided in the URL
    echo "<h1>No student ID provided in the URL</h1>";
    exit; // Stop further execution
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <?php include("elements/meta.php"); ?>
    <title>Intern Performance Evaluation</title>
    <?php include("elements/embed.php"); ?>

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

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
                            <a class="dropdown-item" href="logout.php" data-toggle="logout" data-target="logout">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                Logout
                            </a>
                        </div>
                    </li>

                </ul>

            </nav>
            <!-- End of Topbar -->

            <!-- Main Content -->
            <div id="content" class="py-4 px-4">

                <div class="col-lg-12 mb-4">

                    <?php

                    $queryDept = "SELECT course FROM studentinfo WHERE companyCode = ?";
                    $stmtDept = $connect->prepare($queryDept);
                    $stmtDept->bind_param("s", $_SESSION['IP_num']);
                    $stmtDept->execute();
                    $resultDept = $stmtDept->get_result();

                    if (!$resultDept) {
                        die("Query failed: " . $connect->error);
                    }

                    $rowDept = $resultDept->fetch_assoc();

                    $query = "SELECT * FROM criteria_list WHERE program = ?";
                    $stmt = $connect->prepare($query);
                    $stmt->bind_param("s", $rowDept['course']);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if (!$result) {
                        die("Query failed: " . $connect->error);
                    }

                    ?>

                    <form id="criteriaForm" method="post">
                        <!-- Illustrations -->
                        <div class="card shadow mb-4">
                            <div class="m-4">

                                <!-- Get selected name-->
                                <?php
                                // Check if student_id parameter exists in the URL
                                if (isset($_GET['student_id'])) {
                                    // Get the student_id from the URL
                                    $student_id = $_GET['student_id'];

                                    // Query to select the specific student who does not have records in student_grade table
                                    $queryStudents = "SELECT * FROM studentinfo s WHERE s.studentID = ? AND s.companyCode = ? AND s.status = 'Deployed'
                                        AND NOT EXISTS (
                                        SELECT 1 FROM student_grade sg
                                        WHERE sg.studentID = s.studentID
                                    )";
                                    $stmtStud = $connect->prepare($queryStudents);
                                    $stmtStud->bind_param("ss", $student_id, $_SESSION['IP_num']);
                                    $stmtStud->execute();
                                    $resultStud = $stmtStud->get_result();

                                    if (!$resultStud) {
                                        die("Query failed: " . $connect->error);
                                    }

                                    // Fetch the student's details
                                    $rowStud = $resultStud->fetch_assoc();
                                    $studentName = $rowStud['lastname'] . ", " . $rowStud['firstname'] . " " . substr($rowStud['middlename'], 0, 1) . ".";
                                } else {
                                    // If student_id parameter is not provided in the URL
                                    echo "<h1>No student ID provided in the URL</h1>";
                                    exit; // Stop further execution
                                }
                                ?>
                                <div class="px-3">
                                    <label for="studentNameInput"><b>Student Name:</b></label>
                                    <input type="text" name="studentName" id="studentNameInput" class="form-control mb-3" value="<?php echo $studentName; ?>" data-id="<?php echo $rowStud['studentID']; ?>" readonly>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <?php while ($row = $result->fetch_assoc()) { ?>
                                            <div class='p-3 mb-2 border rounded'>
                                                <div class='row'>
                                                    <div class='col-md-8'>
                                                        <h6 data-id="<?php echo $row['id']; ?>"><?php echo $row['criteria'] ?></h6>
                                                        <p class="small"><i><?php echo $row['description'] ?></i></p>
                                                    </div>

                                                    <div class='col-md-4'>
                                                        <style>
                                                            .custom-number-input {
                                                                width: 100%;
                                                            }
                                                        </style>
                                                        <input type="hidden" id="hiddenInputForCriteria<?php echo $row['id']; ?>" name="criteria[<?php echo $row['id']; ?>]" value="">
                                                        <label class="sr-only" for="displayValue<?php echo $row['id']; ?>">Username</label>
                                                        <div class="input-group input-group-sm mb-2 mr-sm-2">
                                                            <input type="number" required class="form-control custom-number-input" min="0" max="<?php echo $row['percentage']; ?>" value="0" id="displayValue<?php echo $row['id']; ?>" data-initial-value="0" oninput="enforceMaxLimit(this, '<?php echo $row['id']; ?>')" data-percentage="<?php echo $row['percentage']; ?>">
                                                            <div class="input-group-append">
                                                                <div class="input-group-text"><?php echo $row['percentage'] . '%'; ?></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>

                                        <div class="d-flex justify-content-end">
                                            <input type="hidden" name="studentId" id="studentId" value="">
                                            <input type="hidden" name="criteriaData" id="criteriaData" value="">

                                            <button type="submit" class="btn btn-success">
                                                <span class="fas fa-save fw-fa"></span> Submit Grade
                                            </button>
                                            <div class="col-4">
                                                <label class="sr-only" for="totalGrade">Total Grade</label>
                                                <div class="input-group mb-2 mr-sm-2">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">Total</div>
                                                    </div>
                                                    <input type="number" id="totalGrade" class="form-control" min="0" max="100" oninput="distributeTotalGrade()">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
                </form>


            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Retrieve the student ID initially
            var initialStudentId = document.getElementById('studentNameInput').getAttribute('data-id');
            document.getElementById('studentId').value = initialStudentId;

            // No need for 'change' event on a text input, we can use input event instead
            document.getElementById('studentNameInput').addEventListener('input', function() {
                var selectedId = this.getAttribute('data-id');
                document.getElementById('studentId').value = selectedId;
            });

            // Handle submit event for the criteriaForm
            document.getElementById('criteriaForm').addEventListener('submit', function(event) {
                // Prevent the default form submission
                event.preventDefault();

                // Confirmation dialog using SweetAlert2
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, submit it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Prepare the form data
                        var criteriaData = {};
                        var h6Elements = document.querySelectorAll('h6');
                        h6Elements.forEach(function(h6) {
                            var criteriaId = h6.getAttribute('data-id');
                            var criteriaText = h6.textContent;
                            var gradeValue = document.getElementById('displayValue' + criteriaId).value;
                            gradeValue = gradeValue.replace('%', '');

                            criteriaData[criteriaId] = {
                                text: criteriaText,
                                grade: gradeValue
                            };
                        });

                        // Convert criteriaData to a JSON string
                        var criteriaDataString = JSON.stringify(criteriaData, null, 2);
                        document.getElementById('criteriaData').value = criteriaDataString;

                        var formData = new FormData(this);
                        fetch('industry-portal/functions/submit_grade.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status === 'success') {
                                    Swal.fire(
                                        'Submitted!',
                                        'Your grade has been submitted successfully.',
                                        'success'
                                    ).then(() => {
                                        window.close();
                                    });
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        'There was an error submitting your grade.',
                                        'error'
                                    );
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                console.log(response.text());
                                Swal.fire(
                                    'Error!',
                                    'There was an error submitting your grade.',
                                    'error'
                                );
                            });
                    }
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            var criteriaInputs = document.querySelectorAll('.custom-number-input');

            function updateTotal() {
                var total = 0;
                criteriaInputs.forEach(function(input) {
                    var value = parseInt(input.value);
                    if (!isNaN(value)) {
                        total += value;
                    }
                });

                // Update the total input field
                document.getElementById('totalGrade').value = total;
            }
            criteriaInputs.forEach(function(input) {
                input.addEventListener('input', updateTotal);
            });
            updateTotal();
        });

        function updateValue(value, criteriaId) {
            var hiddenInput = document.getElementById('hiddenInputForCriteria' + criteriaId);
            if (hiddenInput) {
                hiddenInput.value = value;
            }
        }

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
                updateValue(input.value, criteriaId);
            }
        }

        function distributeTotalGrade() {
            // Step 1: Collect the total grade value
            var totalGradeInput = document.getElementById('totalGrade');
            var totalGradeValue = totalGradeInput.value.trim();

            if (totalGradeValue === "") {
                totalGradeInput.value = "0";
                var criteriaInputs = document.querySelectorAll('.custom-number-input');
                criteriaInputs.forEach(function(input) {
                    input.value = "0";
                });
                return;
            }

            var totalGrade = parseFloat(totalGradeValue);
            if (isNaN(totalGrade)) {
                totalGrade = 0;
            }
            if (totalGrade < 0) {
                totalGrade = 0;
            } else if (totalGrade > 100) {
                totalGrade = 100;
            }
            if (Number.isInteger(totalGrade)) {
                totalGradeInput.value = totalGrade;
            } else {
                var formattedValue = totalGrade.toFixed(2);
                if (formattedValue.endsWith('.00')) {
                    totalGradeInput.value = parseInt(totalGrade);
                } else {
                    totalGradeInput.value = formattedValue;
                }
            }
            var criteriaInputs = document.querySelectorAll('.custom-number-input');

            criteriaInputs.forEach(function(input) {
                var percentage = parseFloat(input.getAttribute('data-percentage')) / 100;

                // Calculate the portion of the total grade for the current criteria
                var criteriaValue = totalGrade * percentage;

                // Update the value for the current criteria
                var formattedCriteriaValue = criteriaValue.toFixed(2);
                if (formattedCriteriaValue.endsWith('.00')) {
                    input.value = parseInt(criteriaValue);
                } else {
                    input.value = formattedCriteriaValue;
                }
            });
        }
    </script>

</body>

</html>