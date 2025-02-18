<?php
session_start();
include_once("../includes/connection.php");

// Check if student ID is provided via AJAX
if (isset($_GET['studentId'])) {
    $studentId = $_GET['studentId'];
    $criteria = array();

    // Fetch the course of the selected student
    $queryCourse = "SELECT course FROM studentinfo WHERE studentID = ?";
    $stmtCourse = $connect->prepare($queryCourse);
    $stmtCourse->bind_param("s", $studentId);
    $stmtCourse->execute();
    $resultCourse = $stmtCourse->get_result();

    if ($resultCourse && $resultCourse->num_rows > 0) {
        $rowCourse = $resultCourse->fetch_assoc();
        $course = $rowCourse['course'];

        // Fetch criteria list for the selected student's course
        $queryCriteria = "SELECT * FROM criteria_list WHERE program = ?";
        $stmtCriteria = $connect->prepare($queryCriteria);
        $stmtCriteria->bind_param("s", $course);
        $stmtCriteria->execute();
        $resultCriteria = $stmtCriteria->get_result();

        if ($resultCriteria && $resultCriteria->num_rows > 0) {
            while ($rowCriteria = $resultCriteria->fetch_assoc()) {
                $criteria[] = $rowCriteria;
            }
            // Return criteria data as JSON
            echo json_encode(array('status' => 'success', 'criteria' => $criteria));
        } else {
            // No criteria found for the selected course
            echo json_encode(array('status' => 'success', 'criteria' => array()));
        }
    } else {
        // Failed to fetch course for the selected student
        echo json_encode(array('status' => 'error', 'message' => 'Failed to fetch course for the selected student.'));
    }
    exit; // Terminate further execution
}
?>


<!DOCTYPE html>
<html lang="en">

<head>

    <?php include("../elements/meta.php"); ?>
    <title>INDUSTRY PARTNER PORTAL</title>
    <?php include("embed.php"); ?>

</head>

<body id="page-top">

    <!-- Page Wrapper -->
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
                            <a class="dropdown-item" href="../logout.php" data-toggle="logout" data-target="logout">
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
                    $queryIP = "SELECT companyCode FROM company_info WHERE trainerEmail = ?";
                    $stmtIP = $connect->prepare($queryIP);
                    $stmtIP->bind_param("s", $_SESSION['IndustryPartner']);
                    $stmtIP->execute();
                    $resultIP = $stmtIP->get_result();

                    if (!$resultIP) {
                        die("Query failed: " . $connect->error);
                    }

                    // Collect the company codes into an array
                    $companyCodes = array();

                    // Iterate over each companyCode
                    while ($rowIP = $resultIP->fetch_assoc()) {
                        $companyCodes[] = $rowIP['companyCode'];
                    }

                    if (!empty($companyCodes)) {
                        $companyCode = $companyCodes[0];
                    } else {
                        die("No company codes found for the trainer's email.");
                    }
                    $allStudents = [];

                    foreach ($companyCodes as $companyCode) {

                        // Fetch students who do not have records in the student_grade table for the current companyCode
                        $queryStudents = "SELECT * FROM studentinfo s WHERE s.companyCode = ? AND s.status = 'Deployed'
                            AND NOT EXISTS (
                            SELECT 1 FROM student_grade sg
                            WHERE sg.studentID = s.studentID
                        )";
                        $stmtStud = $connect->prepare($queryStudents);
                        $stmtStud->bind_param("s", $companyCode);
                        $stmtStud->execute();
                        $resultStud = $stmtStud->get_result();

                        if (!$resultStud) {
                            die("Query failed: " . $connect->error);
                        }

                        // Collect all students into the $allStudents array
                        while ($rowStud = $resultStud->fetch_assoc()) {
                            $studentName = $rowStud['lastname'] . ", " . $rowStud['firstname'] . " " . substr($rowStud['middlename'], 0, 1) . ".";
                            $allStudents[$rowStud['studentID']] = $studentName;
                        }
                    }

                    ?>

                    <form id="criteriaForm" method="post">
                        <!-- Illustrations -->
                        <div class="card shadow mb-4">
                            <div class="m-4">
                                <div class="px-3">
                                    <label for="studentNameDropdown"><b>Select a Student:</b></label>
                                    <select name="studentId" id="studentNameDropdown" class="form-select mb-3" required>
                                        <option value="" selected disabled>Select a student...</option>
                                        <?php
                                        // Generate select options from the $allStudents array
                                        foreach ($allStudents as $studentId => $studentName) {
                                            echo "<option data-id='" . $studentId . "' value='" . $studentId . "'>" . $studentName . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div id="criteriaContainer">
                                            <p class="text-center p-4">Criterias will be displayed here</p>

                                        </div>
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
    <script src="../assets/js/sidebarscript.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        var criteriaInputs;
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('studentNameDropdown').addEventListener('change', function() {
                var selectedOption = this.options[this.selectedIndex];
                var selectedId = selectedOption.getAttribute('data-id');
                document.getElementById('studentId').value = selectedId;

                fetch('grading.php?studentId=' + selectedId)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            if (data.criteria.length > 0) {
                                renderCriteria(data.criteria);
                            } else {
                                // Display the message when no criteria are found
                                var criteriaContainer = document.getElementById('criteriaContainer');
                                criteriaContainer.innerHTML = '<div class="alert alert-warning">No criteria found for the selected course. Kindly wait for the criteria to be posted or contact an immediate personnel.</div>';
                            }
                        } else {
                            console.error('Error fetching criteria:', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching criteria:', error);
                    });
            });
        });

        function renderCriteria(criteria) {
            var criteriaContainer = document.getElementById('criteriaContainer');
            criteriaContainer.innerHTML = '';

            criteria.forEach(function(criteriaItem) {
                var criteriaHtml = `
                <div class='p-3 mb-2 border rounded'>
                    <div class='row'>
                        <div class='col-md-8'>
                            <h6 data-id="${criteriaItem.id}">${criteriaItem.criteria}</h6>
                            <p class="small"><i>${criteriaItem.description}</i></p>
                        </div>
                        <div class='col-md-4'>
                            <style>
                                .custom-number-input {
                                    width: 100%;
                                }
                            </style>
                            <input type="hidden" id="hiddenInputForCriteria${criteriaItem.id}" name="criteria[${criteriaItem.id}]" value="">
                            <label class="sr-only" for="displayValue${criteriaItem.id}">Username</label>
                            <div class="input-group input-group-sm mb-2 mr-sm-2">
                                <input type="number" required class="form-control custom-number-input" min="0" max="${criteriaItem.percentage}" value="0" id="displayValue${criteriaItem.id}" data-initial-value="0" oninput="enforceMaxLimit(this, '${criteriaItem.id}')" data-percentage="${criteriaItem.percentage}">

                                <div class="input-group-append">
                                    <div class="input-group-text">${criteriaItem.percentage}%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
                criteriaContainer.innerHTML += criteriaHtml;
            });
            updateTotal();
        }


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
                    fetch('submit_grade.php', {
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
                                    location.reload();
                                });
                            } else {
                                Swal.fire(
                                    'Error',
                                    'There was an error submitting your grade.',
                                    'error'
                                );
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire(
                                'Error!',
                                'There was an error submitting your grade.',
                                'error'
                            );
                        });
                }
            });
        });

        function updateTotal() {
            if (!criteriaInputs) {
                criteriaInputs = document.querySelectorAll('.custom-number-input');
                criteriaInputs.forEach(function(input) {
                    input.addEventListener('input', updateTotal);
                });
            }

            var total = 0;
            criteriaInputs.forEach(function(input) {
                var value = parseInt(input.value);
                if (!isNaN(value)) {
                    total += value;
                }
            });
            document.getElementById('totalGrade').value = total;
        }

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