<?php
    session_start();
    include("../includes/connection.php");

    $email = $_SESSION['adviser'];
    $query = "SELECT * FROM listadviser WHERE email ='$email'";
    $result = mysqli_query($connect, $query);
?>
<!DOCTYPE html>
<html>
    <head>
        <?php include("../elements/meta.php"); ?>
        <title>Adviser Portal</title>
        <?php include("embed.php"); ?>
    </head>
    <body id="page-top">
        <div class="wrapper">
            <aside id="sidebar" class="expand">
                <?php include('../elements/adv_sidebar.php') ?>
            </aside>
            <div class="main">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 static-top shadow">

                    <!-- Title -->
                    <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Grading</h4>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    <?php (isset($_SESSION['adviser'])) ?> <?php echo $_SESSION['adviser']; ?></span>
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
                                <ul class="nav nav-pills flex-column flex-sm-row" id="pills-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active btn-sm" id="pills-section-tab" data-toggle="pill" href="#section-tab" role="tab" aria-controls="section-tab" aria-selected="true">Handled Sections</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link btn-sm" id="pills-students-tab" data-toggle="pill" href="#students-tab" role="tab" aria-controls="students-tab" aria-selected="false">Students</a>
                                    </li>
                                </ul>
                            </div>

                            <div class="tab-content" id="pills-tabContent">
                                <div class="tab-pane fade show active" id="section-tab" role="tabpanel" aria-labelledby="section-tab">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered" width="100%" cellspacing="0">
                                                <thead>
                                                    <tr>
                                                        <th scope="col" class="small">Section</th>
                                                        <th width="11%" align="center" class="small">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    while ($rows = mysqli_fetch_assoc($result)) {
                                                    ?>
                                                        <tr>
                                                            <td><a href="#" class="section-link" data-section="<?php echo $rows['section']; ?>" data-course="<?php echo $rows['course'];?>"><?php echo $rows['course'];?> <?php echo $rows['section']; ?></a></td>
                                                            <td>
                                                                <a title="Edit" href="" class="btn btn-xs"><span class="fa fa-edit fw-fa"></span></a>
                                                                <a title="Delete" href="" class="btn btn-xs"><span class="fa fa-trash"></span></a>
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
                                <div class="tab-pane fade" id="students-tab" role="tabpanel" aria-labelledby="students-tab">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover" id="students-table" width="100%" cellspacing="0">
                                                <thead>
                                                    <tr>
                                                        <th scope="col" class="small"></th>
                                                        <th scope="col" class="small">STUDENT NUMBER</th>
                                                        <th scope="col" class="small">NAME</th>
                                                        <th scope="col" class="small">YEAR LEVEL</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td colspan="4" class="text-center small">Loaded Masterlists will appear here</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4 p-lg-0">
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
                                            </div>
                                        </div>
                                    </form>
                                </div>
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
            
                $('.section-link').click(function(e) {
                    e.preventDefault();
                    var section = $(this).data('section');
                    var course = $(this).data('course');

                    $.ajax({
                        url: 'functions/fetch_masterlist.php', // Provide the path to your PHP script that fetches student data
                        method: 'POST',
                        data: {
                            section: section,
                            course: course
                        },
                        success: function(response) {
                            // Destroy existing DataTable
                            if ($.fn.DataTable.isDataTable('#students-table')) {
                                $('#students-table').DataTable().clear().destroy();
                            }

                            // Clear table body
                            $('#students-table tbody').empty();

                            // After successfully loading data, call the loadMasterList function
                            loadMasterList(section, course, response);
                        }
                    });
                });

                function loadMasterList(section, course, response) {
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
                                    title: course + ' ' + section + ' Masterlist Loaded',
                                    position: 'top',
                                    toast: true,
                                    showConfirmButton: false,
                                    timer: 2000
                                });

                                // Replace table data with new response
                                $('#students-table tbody').html(response);

                                // Check if DataTables has already been initialized on the element
                                if ($.fn.DataTable.isDataTable('#students-table')) {
                                    // If DataTables is already initialized, clear and destroy it
                                    $('#students-table').DataTable().clear().destroy();
                                }

                                // Delay the initialization of DataTables to ensure the table is fully rendered
                                setTimeout(function() {
                                    // Check if the table body has rows
                                    if ($('#students-table tbody tr').length > 0) {
                                        // If there are rows, reinitialize DataTables
                                        $('#students-table').DataTable();
                                    }
                                }, 100); // Delay of 100 milliseconds
                            }, 2000); // Simulated loading delay of 2 seconds
                        }
                    });
                }

                // Function to fetch and update student information
                function fetchAndUpdateStudentInfo(studentID) {
                    // AJAX call to fetch student information based on studentID
                    $.ajax({
                        url: 'functions/fetch_stud_info.php', // Provide the path to your PHP script that fetches student information
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
                            'X-Requested-With' : 'XMLHttpRequest'
                        },
                        success: function(response) {
                            try {
                                var criteria = response;

                                console.log("Parsed Criteria:", criteria);  // Debugging: check parsed criteria

                                if (!Array.isArray(criteria)) {  
                                    throw new Error("Response is not an array");
                                }

                                renderCriteria(criteria);
                                initializeInputs(); // Initialize inputs after criteria are rendered
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
                    })
                }

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

                // Event listener for student links
                $(document).on('click', '.info-link', function(e) {
                    e.preventDefault();
                    var studentID = $(this).data('section');
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

                $('#dataTable').DataTable();

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

                    $("#criteriaForm").submit(function(event) {
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
                            confirmButtonText: 'Yes, submit it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Proceed with form submission via AJAX
                                let formData = $(this).serialize(); // Serialize form data

                                formData += '&studentID=' + studentID;

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
            })
        </script>
    </body>
</html>