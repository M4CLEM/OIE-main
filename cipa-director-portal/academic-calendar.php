<?php
    session_start();
    include_once("../includes/connection.php");

    // Set timezone to correct region
    date_default_timezone_set('Asia/Manila'); // Change this if needed

    // Fetch all records
    $result = mysqli_query($connect, "SELECT * FROM academic_year");
    if (!$result) {
        die("Query Failed: " . mysqli_error($connect));
    }

    $currentDate = date("Y-m-d");
    $currentTime = date("F d, Y - h:i:s A"); // Include seconds here
    $activeSemester = "N/A";
    $activeSchoolYear = "N/A";

    // Loop to find the active semester
    while ($rows = mysqli_fetch_assoc($result)) {
        $id = $rows['id'];
        $startDate = $rows['start_date'];
        $endDate = $rows['end_date'];
        $semester = $rows['semester'];
        $schoolYear = $rows['schoolYear'];

        // Check if current date falls within this academic period
        if ($currentDate >= $startDate && $currentDate <= $endDate) {
            $activeSemester = $semester;
            $activeSchoolYear = $schoolYear;
        }

        // Store all records for table display
        $data[] = $rows;
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include("../elements/meta.php"); ?>
        <title>CIPA ADMIN</title>
        <?php include("embed.php"); ?>
    </head>
    <body id="page-top">
        <div class="wrapper">
            <aside id="sidebar" class="expand">
                <?php include('../elements/cipa_sidebar.php') ?>
            </aside>
            <div class="main">
                <nav class="navbar navbar-expand navbar-light bg-white topbar static-top shadow mb-3">
                    <h2 class="my-0 mr-auto font-weight-bold text-dark ml-3">Academic Calendar</h2>
                    <ul class="navbar-nav ml-auto">
                        <div class="topbar-divider d-none d-sm-block"></div>
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    <?php echo $_SESSION['CIPA']; ?></span>
                                <img class="img-profile rounded-circle" src="../img/undraw_profile.svg">
                            </a>
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
                                <a class="dropdown-item" href="../logout.php" data-toggle="logout" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>

                <div id="content" class="py-2">
                    <div class="row">
                        <div class="ms-3">
                            <a href="management-acc.php" class="btn btn-primary"><i class="fa fa-arrow-circle-left"></i></a>
                        </div>
                        <h1 class="text-center">Current Timeline</h1>

                        <div class="text-center">
                            <h3 id="current-time">Current Date and Time: <?php echo $currentTime; ?></h3>
                            <h4>Active Semester: <?php echo $activeSemester; ?></h4>
                            <h4>School Year: <?php echo $activeSchoolYear; ?></h4>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <a class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addModal"> <i class="fa fa-plus-circle fw-fa"></i> Add Calendar </a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th scope="col">Starting Date</th>
                                                <th scope="col">Ending Date</th>
                                                <th scope="col">Semester</th>
                                                <th scope="col">School Year</th>
                                                <th scope="col" width="14%">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php if (!empty($data)): ?>
                                            <?php foreach ($data as $row): ?>
                                                <?php 
                                                    $id = $row['id'];
                                                    $startDate = $row['start_date'];
                                                    $endDate = $row['end_date'];
                                                    $semester = $row['semester'];
                                                    $schoolYear = $row['schoolYear'];

                                                    // Highlight active semester
                                                    $isActive = ($currentDate >= $startDate && $currentDate <= $endDate);
                                                ?>
                                                    <tr <?php echo $isActive ? 'style="background-color: #d4edda; font-weight: bold;"' : ''; ?>>
                                                        <td><?php echo $startDate; ?></td>
                                                        <td><?php echo $endDate; ?></td>
                                                        <td><?php echo $semester; ?></td>
                                                        <td><?php echo $schoolYear; ?></td>
                                                        <td>
                                                            <a href="modal.php" class="btn btn-primary btn-sm editBtn" data-toggle="modal"
                                                                data-target="#editModal" data-id="<?php echo $id; ?>"
                                                                data-start-date="<?php echo  $startDate; ?>"
                                                                data-end-date="<?php echo $endDate; ?>" data-semester="<?php echo $semester; ?>" 
                                                                data-school-year="<?php echo $schoolYear; ?>">
                                                                <i class="fa fa-edit fw-fa"></i> Edit
                                                            </a>
                                                            <button type="button" class="btn btn-danger btn-sm deleteBtn" data-toggle="modal"
                                                                data-target="#deleteModal" data-id="<?php echo $id; ?>">
                                                                <i class="fa fa-trash fw-fa"></i> Delete
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="5" class="text-center">No records found.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ADD MODAL -->
                <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form action="functions/add_academic_year.php" method="POST">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addManagement">Add Academic Year</h5>
                                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <div class="row align-items-center">
                                            <div class="col-md-4 text-right">
                                                <label for="startingDate">Starting Date</label>
                                            </div>
                                            <div class="col-md-4 text-center"></div>
                                            <div class="col-md-4 text-left">
                                                <label for="endingDate">Ending Date</label>
                                            </div>
                                        </div>
                                        <div class="row align-items-center mt-2">
                                            <div class="col-md-5">
                                                <input type="date" id="startingDate" name="startingDate" class="form-control">
                                            </div>
                                            <div class="col-md-2 text-center">To</div>
                                            <div class="col-md-5">
                                                <input type="date" id="endingDate" name="endingDate" class="form-control">
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="form-group">
                                            <div class="row">
                                                <label for="semester">Semester</label>
                                                <select class="form-control" name="semester" id="semester" required>
                                                    <option value="">Select Semester</option>
                                                    <option value="1st Semester">1st Semester</option>
                                                    <option value="2nd Semester">2nd Semester</option>
                                                </select>
                                            </div>
                                            <div class="row">
                                                <label for="schoolYear">School Year</label>
                                                <input type="text" name="schoolYear" id="schoolYear" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-primary btn-sm" type="submit" id="submitBtn">
                                        <span class="fa fa-save fw-fa"></span> Submit
                                    </button>
                                    <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- EDIT MODAL -->
                <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form action="functions/edit_academic_year.php" method="POST" id="editForm">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Academic Year</h5>
                                    <input type="hidden" id="editId" name="id">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <div class="row align-items-center">
                                            <div class="col-md-4 text-right">
                                                <label for="startingDate">Starting Date</label>
                                            </div>
                                            <div class="col-md-4 text-center"></div>
                                            <div class="col-md-4 text-left">
                                                <label for="endingDate">Ending Date</label>
                                            </div>
                                        </div>
                                        <div class="row align-items-center mt-2">
                                            <div class="col-md-5">
                                                <input type="date" id="editStartingDate" name="editStartingDate" class="form-control">
                                            </div>
                                            <div class="col-md-2 text-center">To</div>
                                            <div class="col-md-5">
                                                <input type="date" id="editEndingDate" name="editEndingDate" class="form-control">
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="form-group">
                                            <div class="row">
                                                <label for="semester">Semester</label>
                                                <select class="form-control" name="editSemester" id="editSemester" required>
                                                    <option value="">Select Semester</option>
                                                    <option value="1st Semester">1st Semester</option>
                                                    <option value="2nd Semester">2nd Semester</option>
                                                </select>
                                            </div>
                                            <div class="row">
                                                <label for="schoolYear">School Year</label>
                                                <input type="text" name="editSchoolYear" id="editSchoolYear" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-primary btn-sm" name="save" type="submit"><span class="fa fa-save fw-fa"></span> Save</button>
                                    <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Success Modal -->
                <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="successModalLabel">Update Successful</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                The academic year has been updated successfully!
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DELETE MODAL -->
                <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalLabel">Delete Confirmation</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                Are you sure you want to delete this academic year?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DELETE LOADING MODAL -->
                <div class="modal fade" id="deleteLoadingModal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content d-flex flex-column align-items-center justify-content-center p-4">
                            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                                <span class="sr-only">Deleting...</span>
                            </div>
                            <p class="mt-3">Deleting academic year, please wait...</p>
                        </div>
                    </div>
                </div>

                <!-- SUCCESS LOADING MODAL -->
                <div class="modal fade" id="successLoadingModal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content text-center p-4">
                            <h5 class="text-success">Success!</h5>
                            <p>The academic year has been deleted successfully.</p>
                            <button type="button" class="btn btn-success" data-dismiss="modal">OK</button>
                        </div>
                    </div>
                </div>

                <!-- LOG OUT MODAL-->
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
    </body>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../assets/js/sidebarscript.js"></script>

    <script>
        // JavaScript to update time every second
        setInterval(function() {
            var currentTime = new Date();
            var formattedTime = currentTime.toLocaleString('en-US', { 
                hour12: true, 
                hour: 'numeric', 
                minute: 'numeric', 
                second: 'numeric', 
                month: 'long', 
                day: 'numeric', 
                year: 'numeric' 
            });

            document.getElementById('current-time').innerHTML = "Current Date and Time: " + formattedTime;
        }, 1000); // Update every second

        $(document).ready(function () {
            $('.editBtn').click(function() {
                var id = $(this).data('id');
                var startDate = $(this).data('start-date');
                var endDate = $(this).data('end-date');
                var semester = $(this).data('semester');
                var schoolYear = $(this).data('schoolYear');

                // Populate modal fields with the record's data
                $('#editId').val(id);  // Set the ID in the hidden input
                $('#editStartingDate').val(startDate);  // Set the starting date
                $('#editEndingDate').val(endDate);  // Set the ending date
                $('#editSemester').val(semester);  // Set the semester
                $('#editSchoolYear').val(schoolYear);  // Set the school year
            })

            $('.deleteBtn').click(function() {
                var id = $(this).data('id');
                $('#confirmDelete').data('id', id);
            });

            $('#confirmDelete').click(function() {
                var id = $(this).data('id');

                // Hide delete modal, show delete loading modal
                $('#deleteModal').modal('hide');
                $('#deleteLoadingModal').modal('show');

                $.ajax({
                    url: 'functions/delete_academic_year.php',
                    type: 'POST',
                    data: {
                        id: id
                    },
                    success: function(response) {
                        // Hide delete loading modal, show success loading modal
                        $('#deleteLoadingModal').modal('hide');
                        $('#successLoadingModal').modal('show');

                        // Reload the page after a short delay (optional)
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    },
                    error: function(xhr, status, error) {
                        $('#deleteLoadingModal').modal('hide'); // Hide loading modal
                        alert('An error occurred: ' + error);
                    }
                });
            });

            $('#editForm').submit(function(event) {
                event.preventDefault();  // Prevent the default form submission

                var formData = $(this).serialize();  // Get the form data

                $.ajax({
                    url: 'functions/edit_academic_year.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',  // Expect JSON response
                    success: function(response) {
                        if (response.status === 'success') {
                            // Hide the modal
                            $('#editModal').modal('hide');

                            // Show success modal
                            $('#successModal').modal('show');
                
                            // Optionally reload the page after a short delay
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('An error occurred: ' + error);
                    }
                });
            });
        })
    </script>
</html>