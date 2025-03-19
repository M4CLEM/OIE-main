<?php
session_start();
include("../includes/connection.php");

if (isset($_GET['studentId']) && isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    $studentId = $_GET['studentId'];

    // Fetch company and job role for selected student
    $studQuery = "SELECT companyName, jobrole FROM company_info WHERE studentID = ?";
    $stmtStud = $connect->prepare($studQuery);
    $stmtStud->bind_param("s", $studentId);
    $stmtStud->execute();
    $studResult = $stmtStud->get_result();

    if ($studResult->num_rows > 0) {
        $row = $studResult->fetch_assoc();
        $jobrole = $row['jobrole'];
        $companyName = $row['companyName'];

        $companyCriteriaGrouped = [];

        // Fetch criteria for company and job role
        $criteriaQuery = "SELECT * FROM criteria_list_view WHERE company = ? AND jobrole = ?";
        $stmtCriteria = $connect->prepare($criteriaQuery);
        $stmtCriteria->bind_param("ss", $companyName, $jobrole);
        $stmtCriteria->execute();
        $resultCriteria = $stmtCriteria->get_result();

        while ($row = $resultCriteria->fetch_assoc()) {
            $criteriaData = json_decode($row['criteria'], true);
            foreach ($criteriaData as $companyCriteriaItem) {
                $companyCriteriaGrouped[] = [
                    'id' => $row['id'],
                    'criteria' => $companyCriteriaItem['companyCriteria'],
                    'percentage' => $companyCriteriaItem['companyPercentage'],
                    'description' => $companyCriteriaItem['companyDescription']
                ];
            }
        }
    }

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($companyCriteriaGrouped);
    exit;
}

// Fetch students for dropdown
$allStudents = [];
$queryStudents = "SELECT * FROM studentinfo WHERE status = 'Deployed'";
$resultStudents = $connect->query($queryStudents);
while ($row = $resultStudents->fetch_assoc()) {
    $studentName = $row['lastname'] . ", " . $row['firstname'] . " " . substr($row['middlename'], 0, 1) . ".";
    $allStudents[$row['studentID']] = $studentName;
}
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
                    <form id="criteriaForm" method="POST" action="functions/submit_grade.php">
                        <div class="card shadow mb-4">
                            <div class="m-4">
                                <div class="px-3">
                                    <label for="studentNameDropdown"><b>Select a Student:</b></label>
                                    <select name="studentId" id="studentNameDropdown" class="form-select mb-3" required>
                                        <option value="" selected disabled>Select a student...</option>
                                        <?php foreach ($allStudents as $studentId => $studentName): ?>
                                            <option value="<?= $studentId ?>"><?= $studentName ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div id="criteriaContainer">
                                            <p class="text-center p-4">Criteria will be displayed here</p>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <button type="submit" class="btn btn-success">
                                                <span class="fas fa-save fw-fa"></span> Submit Grade
                                            </button>
                                            <div class="col-4">
                                                <label class="sr-only" for="totalGrade">Total Grade</label>
                                                <div class="input-group mb-2 mr-sm-2">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">Total</div>
                                                    </div>
                                                    <input type="number" id="totalGrade" name="totalGrade" class="form-control" min="0" max="100" oninput="distributeTotalGrade()" required>
                                                </div>
                                                <!--<input type="hidden" name="totalGrade" id="hiddenTotalGrade">-->
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
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    <script src="../assets/js/sidebarscript.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>

$(document).ready(function() {
    let criteriaInputs = null;
    let totalGradeInput = document.getElementById('totalGrade');
    let totalGradeHiddenInput = document.getElementById('totalGradeInput'); // Hidden input

    // Fetch criteria when a student is selected
    $("#studentNameDropdown").change(function() {
        var studentId = $(this).val();

        if (studentId) {
            $.ajax({
                url: window.location.href,
                type: "GET",
                data: { studentId: studentId },
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function(response) {
                    console.log("AJAX Response:", response);
                    try {
                        var criteria = response;
                        renderCriteria(criteria);
                        initializeInputs(); // Initialize inputs after criteria are rendered
                    } catch (e) {
                        console.error("JSON Parsing Error:", e);
                        $("#criteriaContainer").html("<p class='text-center text-danger'>Error fetching criteria.</p>");
                    }
                },
                error: function() {
                    $("#criteriaContainer").html("<p class='text-center text-danger'>Error fetching criteria.</p>");
                }
            });
        }
    });

    // Function to render criteria dynamically
    function renderCriteria(criteria) {
        var criteriaContainer = document.getElementById('criteriaContainer');
        criteriaContainer.innerHTML = '';

        if (criteria.length === 0) {
            criteriaContainer.innerHTML = "<p class='text-center p-4'>No criteria found for this student.</p>";
            return;
        }

        criteria.forEach(function(criteriaItem) {
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
                            <input type="number" required class="form-control custom-number-input" min="0" max="${criteriaItem.percentage}" value="0" name="grade[${criteriaItem.criteria}]" id="displayValue${criteriaItem.id}" oninput="enforceMaxLimit(this, '${criteriaItem.id}')" data-percentage="${criteriaItem.percentage}">
                            <div class="input-group-append">
                                <div class="input-group-text">${criteriaItem.percentage}%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;
            criteriaContainer.innerHTML += criteriaHtml;
        });
    }

    // Initialize event listeners for input elements
    function initializeInputs() {
        criteriaInputs = document.querySelectorAll('.custom-number-input');
        criteriaInputs.forEach(function(input) {
            input.addEventListener('input', function() {
                enforceMaxLimit(input, input.id.replace('displayValue', ''));
                updateTotal();
            });
        });
    }

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

        // Initialize variables for integer distribution
        var remainingPoints = totalGrade;
        var criteriaPoints = [];

        // First pass: Assign points as evenly as possible
        var criteriaCount = criteriaInputs.length;
        var basePoints = Math.floor(totalGrade / criteriaCount);
        var leftoverPoints = totalGrade % criteriaCount;

        criteriaInputs.forEach(function(input) {
            input.value = basePoints;
            criteriaPoints.push(basePoints);
        });

        // Second pass: Distribute leftover points (1 point each) to any criteria that hasn't hit its max
        if (leftoverPoints > 0) {
            for (var i = 0; i < criteriaInputs.length && leftoverPoints > 0; i++) {
                var input = criteriaInputs[i];
                var maxValue = parseInt(input.max);
                var currentValue = parseInt(input.value);

                if (currentValue < maxValue) {
                    input.value = Math.min(currentValue + 1, maxValue); // Add 1 point without exceeding the max
                    criteriaPoints[i] = input.value;
                    leftoverPoints--;
                }
            }
        }

        // Update the total and hidden input fields
        updateTotal();
        criteriaInputs.forEach(function(input, index) {
            var criteriaId = input.id.replace('displayValue', '');
            updateValue(criteriaPoints[index], criteriaId);
        });
    }

    // Add event listener to trigger distributeTotalGrade when typing in totalGrade field
    totalGradeInput.addEventListener('input', function() {
        distributeTotalGrade();
    });

    // Add event listener to distribute total grade after finishing input (blur event)
    totalGradeInput.addEventListener('blur', function() {
        distributeTotalGrade();
    });

    $("#criteriaForm").submit(function(event) {
    event.preventDefault(); // Prevent default form submission

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
            // Proceed with form submission via AJAX
            let formData = $(this).serialize(); // Serialize form data

            $.ajax({
                url: $(this).attr('action'), // Get the form action URL
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
                            location.reload(); // Reload page after success
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
                        text: 'Failed to submit the grade. Please try again.',
                        icon: 'error'
                    });
                }
            });
        }
    });
});



});
     
    </script>

</body>
</html>