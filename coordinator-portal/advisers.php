<?php
session_start();
include_once("../includes/connection.php");
$query = "select * from listadviser";
$result = mysqli_query($connect, $query);
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <?php include("../elements/meta.php"); ?>
    <title>OJT COORDINATOR PORTAL</title>
    <?php include("embed.php"); ?>
    <link rel="stylesheet" href="../assets/css/new-style.css">
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!--Sidebar Wrapper-->
        <aside id="sidebar" class="expand">
            <?php include('../elements/cood_sidebar.php') ?>
        </aside>

        <div class="main">

            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 static-top shadow">

                <!-- Title -->
                <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Advisers - <?php echo $_SESSION['coordinator']; ?></h4>

                <!-- Topbar Navbar -->
                <ul class="navbar-nav ml-auto">

                    <div class="topbar-divider d-none d-sm-block"></div>

                    <!-- Nav Item - User Information -->
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                <?php (isset($_SESSION['coordinator'])) ?> <?php echo $_SESSION['coordinator']; ?></span>
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
            <!-- End of Topbar -->

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <a class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addModal  "> <i class="fa fa-plus-circle fw-fa"></i> Add </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th scope="col">Employee Number</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Section</th>
                                    <th scope="col">Course</th>
                                    <th scope="col">Department</th>
                                    <th scope="col">Semester-SchoolYear</th>
                                    <th width="8%" align="center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($rows = mysqli_fetch_assoc($result)) {
                                ?>
                                    <tr>
                                        <td><?php echo $rows['employeeNumber']; ?></td>
                                        <td><?php echo $rows['fullName']; ?></td>
                                        <td><?php echo $rows['email']; ?></td>
                                        <td><?php echo $rows['section']; ?></td>
                                        <td><?php echo $rows['course']; ?></td>
                                        <td><?php echo $rows['dept']; ?></td>
                                        <td><?php echo $rows['semester']; ?> - <?php echo $rows['schoolYear']; ?></td>
                                        <td>
                                            <a href="modal.php" class="btn btn-primary btn-sm editBtn" data-toggle="modal" data-target="#editModal" data-id="<?php echo $rows['id']; ?>" data-name="<?php echo $rows['fullName']; ?>" data-email="<?php echo $rows['email']; ?>" data-section="<?php echo $rows['section']; ?>" data-course="<?php echo $rows['course']; ?>" data-department="<?php echo $rows['dept']; ?>" data-semester="<?php echo $rows['semester']; ?>" data-schoolyear="<?php echo $rows['schoolYear']; ?>" data-employeeid="<?php echo $rows['employeeNumber']; ?>"><span class="fa fa-edit fw-fa"></span></a>

                                            <button type="button" class="btn btn-danger btn-sm deleteBtn" data-toggle="modal"
                                            data-target="#deleteModal" data-id="<?php echo $rows['id']; ?>" data-email="<?php echo $rows['email']; ?>"><span class="fa fa-trash fw-fa"></span></button>
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
    </div>
    
    <!-- ADD MODAL -->
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addAdvisers" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="add_adviser.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addAdvisers">Add Adviser</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group md-5">
                            <div class="col-md-10">
                                <input class="form-control" id="employeeNumber" name="employeeNumber" type="text" value="" autocomplete="none" placeholder="Employee Number" onkeyup="javascript:capitalize(this.id, this.value);" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="form-group md-5">
                            <div class="col-md-10">
                                <input class="form-control" id="Fullname" name="fullName" type="text" value="" autocomplete="none" placeholder="Full Name" onkeyup="javascript:capitalize(this.id, this.value);" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="form-group md-5">
                            <div class="col-md-10">
                                <input class="form-control input-sm" id="email" name="email" type="email" value="" autocomplete="none" placeholder="Email" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="form-group md-5">
                            <div class="col-md-10">
                                <select class="form-control" name="dept" id="dropdowndept" onchange="showCourses(this.value);" required>
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
                        </div>

                        <div class="form-group md-5">
                            <div class="col-md-10">
                                <select class="form-control" name="course" id="dropdowncourse" onchange="showSections($('#dropdowndept').val(), this.value);" required>
                                    <option value="" selected disabled>Select Course</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group md-5">
                            <div class="col-md-10">
                                <div class="dropdown">
                                    <button class="form-control text-start dropdown-toggle" type="button" id="dropdownSectionBtn" data-bs-toggle="dropdown" aria-expanded="false">
                                        Select Sections
                                    </button>
                                    <ul class="dropdown-menu w-100" id="dropdownsection">
                                        <!-- AJAX will populate this -->
                                    </ul>
                                </div>
                                <!-- Hidden input to submit selected values -->
                                <input type="hidden" name="section" id="sectionInput" required>
                            </div>
                        </div>

                        <div class="form-group md-5">
                            <div class="col-md-10">  
                                <select name="semester" class="form-control">
                                    <option hidden disable value="select ">Select Semester</option>
                                    <option value="1st Semester">1st Semester</option>
                                    <option value = "2nd Semester">2nd Semester</option>          
                                </select>
                            </div>
                        </div>

                        <div class="form-group md-5">
                            <div class="col-md-10">
                                <input type="text" class="form-control" id="schoolYear" name="schoolYear" value="" autocomplete="none" placeholder="School Year" required>
                            </div>
                        </div>

                        <div class="form-group md-5">
                            <div class="col-md-10">
                                <input type="password" class="form-control input-sm" id="password" name="password" value="" autocomplete="none" placeholder="Password" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="form-group md-5">
                            <div class="col-md-10">
                                <input type="password" class="form-control input-sm" id="confirm" name="confirm" value="" autocomplete="none" placeholder="Confirm Password" autocomplete="off" required>
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

    <!-- EDIT Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="edit_adviser.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Information</h5>
                        <input type="hidden" id="editID" name="id">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group md-5">
                            <div class="col-md-10">
                                <input class="form-control" id="editEmployeeNumber" name="editEmployeeNumber" type="text" value="" autocomplete="none" placeholder="Employee Number" onkeyup="javascript:capitalize(this.id, this.value);" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="form-group md-5">
                            <div class="col-md-10">
                                <input class="form-control" id="editFullName" name="editFullName" type="text" value="" autocomplete="none" placeholder="Full Name" onkeyup="javascript:capitalize(this.id, this.value);" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="form-group md-5">
                            <div class="col-md-10">
                                <input class="form-control input-sm" id="editEmail" name="editEmail" type="email" value="" autocomplete="none" placeholder="Email" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="form-group md-5">
                            <div class="col-md-10">
                                <select class="form-control" name="dept" id="editDropdowndept" onchange="showCourses(this.value, 'edit');" required>
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
                        </div>

                        <div class="form-group md-5">
                            <div class="col-md-10">
                                <select class="form-control" name="course" id="editDropdowncourse" onchange="showSections($('#editDropdowndept').val(), this.value, 'edit');" required>
                                    <option value="" selected disabled>Select Course</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group md-5">
                            <div class="col-md-10">
                                <div class="dropdown">
                                    <button class="form-control text-start dropdown-toggle" type="button" id="editDropdownSectionBtn" data-bs-toggle="dropdown" aria-expanded="false">
                                        Select Sections
                                    </button>
                                    <ul class="dropdown-menu w-100" id="editDropdownsection">
                                        <!-- AJAX will populate this -->
                                    </ul>
                                </div>
                                <!-- Hidden input to submit selected values -->
                                <input type="hidden" name="section" id="editSectionInput" required>
                            </div>
                        </div>

                        <div class="form-group md-5">
                            <div class="col-md-10">  
                                <select name="editSemester" id="editSemester" class="form-control">
                                    <option hidden disable value="select ">Select Semester</option>
                                    <option value="1st Semester">1st Semester</option>
                                    <option value = "2nd Semester">2nd Semester</option>          
                                </select>
                            </div>
                        </div>

                        <div class="form-group md-5">
                            <div class="col-md-10">
                                <input type="text" class="form-control" id="editSchoolYear" name="editSchoolYear" value="" autocomplete="none" placeholder="School Year" required>
                            </div>
                        </div>

                        <div class="form-group md-5">
                            <div class="col-md-10">
                                <input type="password" class="form-control input-sm" id="editPassword" name="editPassword" value="" autocomplete="none" placeholder="Password" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="form-group md-5">
                            <div class="col-md-10">
                                <input type="password" class="form-control input-sm" id="confirmEdit" name="confirmEdit" value="" autocomplete="none" placeholder="Confirm Password" autocomplete="off" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="saveEditBtn" class="btn btn-primary btn-sm">
                            <span class="fa fa-save fw-fa"></span> Save
                        </button>
                        <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
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

    <!-- DELETE CONFIRMATION MODAL -->
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
                    Are you sure you want to delete this account?
                    <br><br>
                    <div style="display: flex; align-items: center;">
                        <input type="checkbox" id="removeRecord" name="removeRecord" style="margin-right: 10px;">
                        <label for="removeRecord" style="font-weight: bold; font-size: 16px; margin-top: 6px;">Remove record from list</label>
                    </div>
                    <br>
                    <div style="display: flex; align-items: center;">
                        <input type="checkbox" id="removeAccess" name="removeAccess" style="margin-right: 10px;">
                        <label for="removeAccess" style="font-weight: bold; font-size: 16px; margin-top: 6px;">Remove system account access</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete" disabled>Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- SUCCESS DELETE MODAL -->
    <div class="modal fade" id="successDeleteModal" tabindex="-1" role="dialog" aria-labelledby="successDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successDeleteModalLabel">Success</h5>
                </div>
                <div class="modal-body">
                    <p>The account has been successfully deleted.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
    
    </div>


    <style>
        /* Style for selected checkbox items */
        .dropdown-item.selected {
            background-color: #0d6efd;
            color: white;
            margin: 0;
            padding: 8px 12px;
            border-radius: 0;
        }

        /* Tighten up list and remove gaps */
        .dropdown-menu li {
            margin: 0;
            padding: 0;
        }

        .dropdown-menu .dropdown-item {
            margin: 0;
            padding: 8px 12px;
            border-radius: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .dropdown-item input[type="checkbox"] {
            margin: 0;
        }

        .dropdown-item:active {
            background-color: transparent;
            color: inherit;
        }

    </style>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    <script src="../assets/js/sidebarscript.js"></script>
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

        function showCourses(department, mode = 'add') {
            const courseDropdown = document.getElementById(mode === 'edit' ? 'editDropdowncourse' : 'dropdowncourse');
            courseDropdown.innerHTML = '<option value="" disabled selected>Loading...</option>';

            $.ajax({
                url: 'functions/get_courses.php',
                type: 'GET',
                data: { department: department },
                success: function(data) {
                    courseDropdown.innerHTML = data;
                }
            });
        }

        function showSections(department, course, mode = 'add') {
            const sectionList = document.getElementById(mode === 'edit' ? 'editDropdownsection' : 'dropdownsection');
            sectionList.innerHTML = '<li class="dropdown-item">Loading...</li>';

            $.ajax({
                url: 'functions/get_sec.php',
                type: 'GET',
                data: { department: department, course: course },
                success: function(data) {
                    sectionList.innerHTML = data;
                    bindSectionCheckboxes(mode); // Pass mode to target correct modal
                }
            });
        }

        function bindSectionCheckboxes(mode = 'add') {
            const sectionInput = document.getElementById(mode === 'edit' ? 'editSectionInput' : 'sectionInput');
            const dropdownBtn = document.getElementById(mode === 'edit' ? 'editDropdownSectionBtn' : 'dropdownSectionBtn');
            const checkboxes = (mode === 'edit' ? document.querySelectorAll('#editDropdownsection .section-check') : document.querySelectorAll('#dropdownsection .section-check'));

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', () => {
                    const selected = Array.from(checkboxes)
                    .filter(cb => cb.checked)
                    .map(cb => cb.value);

                    sectionInput.value = selected.join(',');
                    dropdownBtn.textContent = selected.length > 0 ? selected.join(', ') : 'Select Sections';

                    checkboxes.forEach(cb => {
                        const label = cb.closest('.dropdown-item');
                        if (cb.checked) label.classList.add('selected');
                        else label.classList.remove('selected');
                    });
                });
            });
        }
        
        // Get references to the checkboxes and the delete button
        const removeRecordCheckbox = document.getElementById('removeRecord');
        const removeAccessCheckbox = document.getElementById('removeAccess');
        const confirmDeleteButton = document.getElementById('confirmDelete');

        // Function to check if at least one checkbox is checked
        function checkCheckboxes() {
            if (removeRecordCheckbox.checked || removeAccessCheckbox.checked) {
                confirmDeleteButton.disabled = false;  // Enable the delete button
            } else {
                confirmDeleteButton.disabled = true;   // Disable the delete button
            }
        }

        // Add event listeners to checkboxes
        removeRecordCheckbox.addEventListener('change', checkCheckboxes);
        removeAccessCheckbox.addEventListener('change', checkCheckboxes);

        // Initialize button state
        checkCheckboxes();
    </script>

    <script>
        $(document).ready(function() {
            $('.editBtn').click(function() {
                var name = $(this).data('name');
                var email = $(this).data('email');
                var section = $(this).data('section');
                var course = $(this).data('course');
                var department = $(this).data('department');
                var semester = $(this).data('semester');
                var schoolYear = $(this).data('schoolyear');
                var employeeNumber = $(this).data('employeeid');
                var id = $(this).data('id');


                $('#editID').val(id);
                $('#editFullName').val(name);
                $('#editEmail').val(email);
                $('#editSection').val(section);
                $('#editSemester').val(semester);
                $('#editSchoolYear').val(schoolYear);
                $('#editDropdownSectionBtn').text(section);
                $('#editEmployeeNumber').val(employeeNumber);

                // Set department and trigger courses to load
                $('#editDropdowndept').val(department).change(); // this will call showCourses(department, 'edit')

                // Wait a moment for courses to be populated dynamically
                setTimeout(function() {
                    $('#editDropdowncourse').val(course).change();

                    // Now populate section list
                    setTimeout(function() {
                        showSections(department, course, 'edit');
                    }, 200);

                }, 200);
            })

            $(".deleteBtn").click(function() {
                var id = $(this).data("id");
                var email = $(this).data("email");  // Retrieve the email
                $("#confirmDelete").data("id", id);
                $("#confirmDelete").data("email", email);  // Store the email for confirmation
            });

            // Confirm and Process Deletion
            $("#confirmDelete").click(function() {
                var id = $(this).data("id");
                var email = $(this).data("email");  // Get the stored email
                var removeRecord = $("#removeRecord").prop("checked"); // Get the status of removeRecord checkbox
                var removeAccess = $("#removeAccess").prop("checked"); // Get the status of removeAccess checkbox

                // Check if at least one checkbox is checked
                if (!removeRecord && !removeAccess) {
                    alert("Please select at least one option to proceed with the deletion.");
                    return; // Prevent deletion if no checkbox is selected
                }

                // Hide delete modal
                $("#deleteModal").modal("hide");

                $.ajax({
                    url: "delete_adviser.php", // Delete function script
                    type: "POST",
                    data: {
                        id: id,
                        email: email, // Send email for deleting from the users table
                        removeRecord: removeRecord ? 'true' : 'false', // Send only true or false
                        removeAccess: removeAccess ? 'true' : 'false'  // Send only true or false
                    },
                    success: function(response) {
                        // Show success modal
                        $("#successDeleteModal").modal("show");
                    },
                    error: function(xhr, status, error) {
                        alert("An error occurred: " + error);
                    },
                });
            });

            // Reload page only when clicking "OK" button
            $("#successDeleteModal").on("hidden.bs.modal", function() {
                location.reload();
            });

            $("#successDeleteModal .btn-primary").click(function() {
                $("#successDeleteModal").modal("hide"); // Close modal
            });
        })
    </script>
</body>

</html>