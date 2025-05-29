<?php
    include_once("../includes/connection.php");
    session_start();

    $activeSemester = $_SESSION['semester'];
    $activeSchoolYear = $_SESSION['schoolYear'];
    $companyName = $_SESSION['companyName'];

    $jobQuery = "SELECT * FROM companylist 
             WHERE TRIM(companyName) = TRIM(?) 
             AND TRIM(semester) = TRIM(?) 
             AND TRIM(schoolYear) = TRIM(?)";
    $jobStmt = $connect->prepare($jobQuery);
    $jobStmt->bind_param("sss", $companyName, $activeSemester, $activeSchoolYear);
    $jobStmt->execute();
    $jobResult = $jobStmt->get_result();

    $departmentQuery = "SELECT * FROM department_list";
    $departmentResult = mysqli_query($connect, $departmentQuery);

    $departments = [];
    if ($departmentResult && mysqli_num_rows($departmentResult) > 0) {
        while ($row = mysqli_fetch_assoc($departmentResult)) {
            $departments[] = $row;
        }
    }

?>

<!DOCTYPE html>
<html>
    <head>
        <?php include("../elements/meta.php"); ?>
        <title>Industry Partner Portal</title>
        <?php include("embed.php"); ?>
        <link rel="stylesheet" href="../assets/css/new-style.css">
    </head>
    <body id="page-top">
        <div id="wrapper">
            <aside id="sidebar" class="expand">
                <?php include('../elements/ip_sidebar.php') ?>
            </aside>

            <div class="main">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 static-top shadow">
                    <!-- Title -->
                    <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Job Posts</h4>
                    <!-- Topbar Navbar -->
                    <?php include('../elements/ip_navbar_user_info.php') ?>
                </nav>

                <div class="row m-1">
                    <div class="col-md-6 mb-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h5 class="m-0 font-weight-bold text-dark">Jobs</h5>
                                <button class="btn btn-primary btn-sm addBtn" type="button" data-target="#addModal" data-toggle="modal">Post Job</button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered"  width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th scope="col">JOBROLE</th>
                                                <th scope="col">WORK TYPE</th>
                                                <th scope="col">SLOTS</th>
                                                <th scope="col" width="23%">ACTION</th>
                                            </tr>
                                        </thead>
                                       <tbody style="max-height: 80vh; overflow-y: auto;">
                                            <?php
                                                if ($jobResult->num_rows === 0): ?>
                                                    <tr><td colspan="2">No job roles found.</td></tr>
                                            <?php else:
                                                $countQuery = "
                                                    SELECT jobrole, COUNT(*) as count 
                                                    FROM company_info 
                                                    WHERE status = 'Approved' 
                                                        AND semester = ? 
                                                        AND schoolYear = ? 
                                                        AND companyName = ?
                                                    GROUP BY jobrole
                                                ";

                                                $countStmt = $connect->prepare($countQuery);
                                                $countStmt->bind_param("sss", $activeSemester, $activeSchoolYear, $companyName);
                                                $countStmt->execute();
                                                $result = $countStmt->get_result();

                                                $jobCounts = [];
                                                while ($countRow = $result->fetch_assoc()) {
                                                    $jobCounts[trim($countRow['jobrole'])] = $countRow['count'];
                                                }
                                                $countStmt->close();

                                                while ($row = $jobResult->fetch_assoc()):
                                                    $jobRole = trim($row['jobrole']);
                                                    $workType = trim($row['workType']);
                                                    $slots = trim($row['slots']);

                                                    // Get the count of students already approved for this jobrole
                                                    $usedSlots = isset($jobCounts[$jobRole]) ? $jobCounts[$jobRole] : 0;
                                                    $remainingSlots = $slots - $usedSlots;
                                                    if ($remainingSlots < 0) $remainingSlots = 0; // just in case
                                                ?>
                                                    <tr>
                                                        <td>
                                                            <a href="#" class="jobrole-link" data-jobrole="<?php echo htmlspecialchars($jobRole);?>" data-company="<?php echo htmlspecialchars(trim($row['companyName'])); ?>">
                                                                <?php echo htmlspecialchars($jobRole);?>
                                                            </a>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($workType); ?></td>
                                                        <td><?php echo htmlspecialchars($usedSlots); ?> / <?php echo htmlspecialchars($slots); ?></td>
                                                        <td>
                                                            <a href="modal.php" type="button" class="btn btn-primary btn-sm editBtn" data-toggle="modal" data-target="#editModal" data-id="<?php echo $row['No']; ?>" data-companyaddress="<?php echo $row['companyaddress']; ?>" data-contactperson="<?php echo $row['contactPerson']; ?>" data-jobrole="<?php echo $row['jobrole']; ?>" data-worktype="<?php echo $row['workType']; ?>" data-jobdescription="<?php echo $row['jobdescription']; ?>" data-jobrequirement="<?php echo $row['jobreq']; ?>" data-department="<?php echo $row['dept']; ?>" data-link="<?php echo $row['link']; ?>" data-slots="<?php echo $row['slots']; ?>">
                                                                Edit
                                                            </a>

                                                            <button type="button" class="btn btn-danger btn-sm deleteBtn" data-toggle="modal" data-target="#deleteModal" data-id="<?php echo $row['No']; ?>">Delete</button>
                                                        </td>
                                                    </tr>
                                                <?php endwhile;
                                            endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h5 class="m-0 font-weight-bold text-dark">Job Information</h5>
                            </div>
                            <div class="card-body" style="max-height: 80vh; overflow-y: auto;">
                                <div class="row m-1">
                                    <div class="row">
                                        <div class="col">
                                            <label for="companyNumber" class="small">Company Number:</label>
                                            <p class="small font-weight-bold" id="companyNumber"></p>
                                        </div>
                                        <div class="col">
                                            <label for="jobrole" class="small">Jobrole:</label>
                                            <p class="small font-weight-bold" id="jobrole"></p>
                                        </div>
                                        <div class="col">
                                            <label for="workType" class="small">Work Type:</label>
                                            <p class="small font-weight-bold" id="workType"></p>
                                        </div>
                                        <div class="col">
                                            <label for="college">College:</label>
                                            <p class="small font-weight-bold" id="department"></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <label for="address" class="small">Address:</label>
                                            <p class="small font-weight-bold" id="address"></p>
                                        </div>
                                        <div class="col">
                                            <label for="contactPerson" class="small">Contact Person:</label>
                                            <p class="small font-weight-bold" id="contactPerson"></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="jobdescription" class="small">Job Description:</label>
                                        <p class="small font-weight-bold" id="jobDescription"></p>
                                    </div>
                                    <div class="row">
                                        <label for="jobRequirement">Job Requirements:</label>
                                        <p class="small font-weight-bold" id="jobRequirement"></p>
                                    </div>
                                    <div class="row">
                                        <label for="link" class="small">Link:</label>
                                        <p class="small font-weight-bold" id="link"></p>
                                    </div>
                                </div>

                                <hr>

                                <div class="row m-1">
                                    <h5>Students Deployed</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th scope="col" width="20%" class="small">STUDENT NO.</th>
                                                    <th scope="col" class="small">NAME</th>
                                                    <th scope="col" class="small" width="10%">COLLEGE</th>
                                                    <th scope="col" width="26%" class="small">COURSE-SECTION</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>

                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        
        <!-- ADD MODAL -->
        <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModal" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addModalLabel">Add Job</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="jobPostForm">
                            <div class="form-group md-5">

                                <div class="col-md">
                                    <div>
                                        <span>Slots</span>
                                    </div>
                                    <input type="number" class="form-control" name="slots" id="slots" placeholder="Slots" required>
                                </div>

                                <div class="col-md">
                                    <div>
                                        <span>Jobrole</span>
                                    </div>
                                    <input type="text" class="form-control" name="jobrole" id="jobrole" placeholder="Jobrole" required>
                                </div>

                                <div class="form-group md-5">
                                    <div class="col-md">
                                        <div>
                                            <span>Company Address</span>
                                        </div>
                                        <input type="text" class="form-control" name="address" id="address" placeholder="Address" required>
                                    </div>
                                </div>
                                
                                <div class="form-group md-5">
                                    <div class="col-md">
                                        <div>
                                            <span>Contact Person</span>
                                        </div>
                                        <input class="form-control" type="text" name="contactPerson" id="contactPerson" placeholder="Contact Person" required>
                                    </div>
                                </div>

                                <div class="form-group md-5">
                                    <div class="col-md">
                                        <div>
                                            <span>Work Type</span>
                                        </div>
                                        <select name="workType" id="workType" class="form-control" required>
                                            <option hidden disable value="select">Select</option>';
                                            <option value="WFH">Work from Home</option>
                                            <option value="Onsite">On site</option>';
                                            <option value="PB">Project-based</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group md-5">
                                    <div class="col-md">
                                        <div>
                                            <span>College</span>
                                        </div>
                                        <select name="department" id="department" class="form-control" required>
                                            <option value="" disabled selected>Select College</option>
                                            <?php
                                                foreach ($departments as $row) {
                                                    echo '<option value="' . htmlspecialchars($row['department']) . '">' . htmlspecialchars($row['department']) . ' - ' . htmlspecialchars($row['department_title']) . '</option>';
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-group md-5">
                                    <div class="col-md">
                                        <div>
                                            <span>Job Description</span>
                                        </div>
                                        <textarea class="form-control" name="jobDescription" id="jobDescription" rows="5" required></textarea>
                                    </div>
                                </div>

                                <div class="form-group md-5">
                                    <div class="col-md">
                                        <div>
                                            <span>Job Requirements</span>
                                        </div>
                                        <textarea class="form-control" name="jobRequirements" id="jobRequirements" rows="5" required></textarea>
                                    </div>
                                </div>

                                <div class="form-group md-5">
                                    <div class="col-md">
                                        <div>
                                            <span>Link</span>
                                        </div>
                                        <input type="text" class="form-control" name="link" id="link" placeholder="Link" required>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit" form="jobPostForm">Submit</button>
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>

        <!--EDIT MODAL-->
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModal" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Job</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="editJobPostForm">

                            <input type="hidden" id="editID" name="id">

                            <div class="form-group md-5">
                                
                                <div class="col-md">
                                    <div>
                                        <span>Slots</span>
                                    </div>
                                    <input type="number" class="form-control" name="editSlots" id="editSlots" placeholder="Slots" required>
                                </div>

                                <div class="col-md">
                                    <div>
                                        <span>Jobrole</span>
                                    </div>
                                    <input type="text" class="form-control" name="editjobrole" id="editjobrole" placeholder="Jobrole" required>
                                </div>

                                <div class="form-group md-5">
                                    <div class="col-md">
                                        <div>
                                            <span>Company Address</span>
                                        </div>
                                        <input type="text" class="form-control" name="editAddress" id="editAddress" placeholder="Address" required>
                                    </div>
                                </div>
                                
                                <div class="form-group md-5">
                                    <div class="col-md">
                                        <div>
                                            <span>Contact Person</span>
                                        </div>
                                        <input class="form-control" type="text" name="editContactPerson" id="editContactPerson" placeholder="Contact Person" required>
                                    </div>
                                </div>

                                <div class="form-group md-5">
                                    <div class="col-md">
                                        <div>
                                            <span>Work Type</span>
                                        </div>
                                        <select name="editWorkType" id="editWorkType" class="form-control" required>
                                            <option hidden disable value="select">Select</option>';
                                            <option value="WFH">Work from Home</option>
                                            <option value="Onsite">On site</option>';
                                            <option value="PB">Project-based</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group md-5">
                                    <div class="col-md">
                                        <div>
                                            <span>College</span>
                                        </div>
                                        <select name="editDepartment" id="editDepartment" class="form-control" required>
                                            <option value="" disabled selected>Select College</option>
                                                <?php
                                                    foreach ($departments as $row) {
                                                        echo '<option value="' . htmlspecialchars($row['department']) . '">' . htmlspecialchars($row['department']) . ' - ' . htmlspecialchars($row['department_title']) . '</option>';
                                                    }
                                                ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-group md-5">
                                    <div class="col-md">
                                        <div>
                                            <span>Job Description</span>
                                        </div>
                                        <textarea class="form-control" name="editJobDescription" id="editJobDescription" rows="5" required></textarea>
                                    </div>
                                </div>

                                <div class="form-group md-5">
                                    <div class="col-md">
                                        <div>
                                            <span>Job Requirements</span>
                                        </div>
                                        <textarea class="form-control" name="editJobRequirements" id="editJobRequirements" rows="5" required></textarea>
                                    </div>
                                </div>

                                <div class="form-group md-5">
                                    <div class="col-md">
                                        <div>
                                            <span>Link</span>
                                        </div>
                                        <input type="text" class="form-control" name="editLink" id="editLink" placeholder="Link" required>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit" form="editJobPostForm">Save</button>
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Confirmation Modal -->
        <div class="modal fade" id="confirmEditModal" tabindex="-1" role="dialog" aria-labelledby="confirmEditModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title" id="confirmEditModalLabel">Confirm Changes</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to save the changes to this job post?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">No, Cancel</button>
                        <button type="button" class="btn btn-primary" id="confirmEditBtn">Yes, Save Changes</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- DELETE CONFIRMATION MODAL -->
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content border-danger">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this job post? This action cannot be undone.
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="deleteID">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Yes, Delete</button>
                    </div>
                </div>
            </div>
        </div>



    </body>

    <script>
        $(document).ready(function () {
            $('.jobrole-link').on('click', function (e) {
                e.preventDefault();
                const jobrole = $(this).data('jobrole');
                const companyName = '<?php echo $_SESSION["companyName"]; ?>';

                $.ajax({
                    type: 'POST',
                    url: 'functions/get_job_info.php', // update to correct path
                    data: { jobrole, companyName },
                    dataType: 'json',
                    success: function (data) {
                        if (data.success) {
                            $('#companyNumber').text(data.companyNumber);
                            $('#jobrole').text(data.jobrole);
                            $('#workType').text(data.workType);
                            $('#address').text(data.address);
                            $('#contactPerson').text(data.contactPerson);
                            $('#jobDescription').text(data.jobDescription);
                            $('#jobRequirement').text(data.jobRequirement);
                            $('#department').text(data.department);
                            $('#link').text(data.link);

                            // Populate student table
                            const studentTableBody = $('table tbody').last(); // adjust selector if needed
                            studentTableBody.empty();

                            if (data.students.length > 0) {
                                data.students.forEach(student => {
                                    studentTableBody.append(`
                                        <tr>
                                            <td>${student.studentID}</td>
                                            <td>${student.fullName}</td>
                                            <td>${student.department}</td>
                                            <td>${student.courseSection}</td>
                                        </tr>
                                    `);
                                });
                            } else {
                                studentTableBody.append('<tr><td colspan="4">No students deployed for this job role.</td></tr>');
                            }
                        } else {
                            alert('No job information found.');
                        }
                    },
                    error: function () {
                        alert('Something went wrong.');
                    }
                });
            });

            $('#jobPostForm').on('submit', function (e) {
                e.preventDefault(); // Prevent default form submission

                $.ajax({
                    type: 'POST',
                    url: 'functions/submit_job_post.php',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            alert("Job added successfully!");
                            $('#addModal').modal('hide');
                            $('#jobPostForm')[0].reset();
                            location.reload(); // Optional: refresh to show new job post
                        } else {
                            alert("Error: " + response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX Error:", error);
                        console.log("Raw Response:", xhr.responseText); // 👈 Check what’s breaking the JSON
                        alert("AJAX Error: " + error);
                    }
                });
            });

            $(document).on('click', '.editBtn', function () {
                // Get data from the button's data-* attributes
                var id = $(this).data('id');
                var companyaddress = $(this).data('companyaddress');
                var contactperson = $(this).data('contactperson');
                var jobrole = $(this).data('jobrole');
                var worktype = $(this).data('worktype');
                var jobdescription = $(this).data('jobdescription');
                var jobrequirement = $(this).data('jobrequirement');
                var department = $(this).data('department');
                var link = $(this).data('link');
                var slots = $(this).data('slots');

                // Set values into the modal fields
                $('#editID').val(id);
                $('#editAddress').val(companyaddress);
                $('#editContactPerson').val(contactperson);
                $('#editjobrole').val(jobrole);
                $('#editWorkType').val(worktype);
                $('#editJobDescription').val(jobdescription);
                $('#editJobRequirements').val(jobrequirement);
                $('#editDepartment').val(department);
                $('#editLink').val(link);
                $('#editSlots').val(slots);
            });

            let editFormConfirmed = false;

            $('#editJobPostForm').on('submit', function (e) {
                e.preventDefault();

                // If already confirmed, proceed to AJAX
                if (editFormConfirmed) {
                    submitEditForm();
                    editFormConfirmed = false;
                } else {
                    // Show confirmation modal
                    $('#confirmEditModal').modal('show');
                }
            });

            // Handle the confirmation button click
            $('#confirmEditBtn').on('click', function () {
                editFormConfirmed = true;
                $('#confirmEditModal').modal('hide');

                // Re-trigger the form submission
                $('#editJobPostForm').submit();
            });

            function submitEditForm() {
                $.ajax({
                    type: 'POST',
                    url: 'functions/edit_job_post.php',
                    data: $('#editJobPostForm').serialize(),
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            $('#editModal').modal('hide');
                            $('#editJobPostForm')[0].reset();
                            location.reload();
                        } else {
                            alert("Error: " + response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        alert("AJAX Error: " + error);
                    }
                });
            }

            $('.deleteBtn').on('click', function () {
                const id = $(this).data('id');
                $('#deleteID').val(id); // set hidden input in modal
            });

            // When confirm delete button is clicked
            $('#confirmDeleteBtn').on('click', function () {
                const id = $('#deleteID').val();

                $.ajax({
                    type: 'POST',
                    url: 'functions/delete_job_post.php',
                    data: { id: id },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            $('#deleteModal').modal('hide');
                            location.reload(); // Refresh to reflect deletion
                        } else {
                            alert("Error: " + response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        alert("AJAX Error: " + error);
                    }
                });
            });
        });
    </script>

</html>