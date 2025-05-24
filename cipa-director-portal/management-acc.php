<?php
session_start();
include_once("../includes/connection.php");

$result = mysqli_query($connect, "SELECT * FROM staff_list");
if (!$result) {
    die("Query Failed: " . mysqli_error($connect));
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("../elements/meta.php"); ?>
    <title>CIPA ADMIN</title>
    <?php include("embed.php"); ?>
    <link rel="stylesheet" href="../assets/css/new-style.css">

    <style>
        /* Default style for the department dropdown */
        #department:disabled {
            background-color: #e0e0e0;
            /* Light gray */
            color: #888;
            /* Dark gray */
            cursor: not-allowed;
        }
    </style>
</head>

<body id="page-top">
    <div class="wrapper">
        <aside id="sidebar" class="expand">
            <?php include('../elements/cipa_sidebar.php') ?>
        </aside>
        <div class="main">
            <nav class="navbar navbar-expand navbar-light bg-white topbar static-top shadow mb-3">
                <h2 class="my-0 mr-auto font-weight-bold text-dark ml-3">Management Accounts</h2>
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
                            <a class="dropdown-item" href="../logout.php" data-toggle="logout" data-target="logout">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                Logout
                            </a>
                        </div>
                    </li>
                </ul>
            </nav>
            
            <div id="content" class="py-2">
                <div class="col-lg-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <a class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addModal"> <i class="fa fa-plus-circle fw-fa"></i> Add </a>
                            <a href="academic-calendar.php" class="btn btn-primary btn-sm"><i class="fa fa-calendar-week"></i> Academic Calendar </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th scope="col">Employee Number</th>
                                            <th scope="col">Name</th>
                                            <th scope="col" width="11%">Department</th>
                                            <th scope="col">Email</th>
                                            <th scope="col">Password</th>
                                            <th scope="col" width="10%">Role</th>
                                            <th scope="col" width="14%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        while ($rows = mysqli_fetch_assoc($result)) {
                                        ?>
                                            <tr>
                                                <td><?php echo $rows['employeeNumber']?></td>
                                                <td><?php echo $rows['name'] ?></td>
                                                <td><?php echo $rows['department'] ?></td>
                                                <td><?php echo $rows['email'] ?></td>
                                                <td><?php echo $rows['password'] ?></td>
                                                <td><?php echo $rows['role'] ?></td>
                                                <td>
                                                    <a href="modal.php" class="btn btn-primary btn-sm editBtn" data-toggle="modal"
                                                        data-target="#editModal" data-id="<?php echo $rows['id']; ?>"
                                                        data-name="<?php echo $rows['name']; ?>"
                                                        data-department="<?php echo $rows['department']; ?>" data-email="<?php echo $rows['email']; ?>" data-role="<?php echo $rows['role'] ?>" data-employeenumber="<?php echo $rows['employeeNumber']?>"><i class="fa fa-edit fw-fa"></i>Edit</a>
                                                    <button type="button" class="btn btn-danger btn-sm deleteBtn" data-toggle="modal"
                                                        data-target="#deleteModal" data-id="<?php echo $rows['id']; ?>">
                                                        <i class="fa fa-trash fw-fa"></i> Delete
                                                    </button>
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
            <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addManagementAcc" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form id="addForm">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addManagement">Add Management Account</h5>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div id="addMessage"></div> <!-- Success/Error messages -->
                                <div class="form-group">
                                    <input type="text" class="form-control" id="employeenumber" name="employeenumber" placeholder="Employee Number" required>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" id="staffname" name="staffname" placeholder="Name" required>
                                </div>
                                <div class="form-group">
                                    <select name="role" id="role" class="form-control">
                                        <option value="">Select Role</option>
                                        <option value="CIPA">CIPA</option>
                                        <option value="Coordinator">Coordinator</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select name="department" id="department" class="form-control">
                                        <option value="">Select Department</option>
                                        <?php
                                        $query = "SELECT * FROM department_list";
                                        $departments = mysqli_query($connect, $query);
                                        while ($department = mysqli_fetch_assoc($departments)) {
                                            echo "<option value='" . $department['department'] . "'>" . $department['department'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Confirm Password" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-primary btn-sm" type="submit"><span class="fa fa-save fw-fa"></span> Save</button>
                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                            </div>
                        </form>
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
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
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


            <!-- EDIT MODAL -->
            <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form id="editForm">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel">Edit Information</h5>
                                <input type="hidden" id="editID" name="id">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <!-- Alert Message Placeholder -->
                                <div id="editMessage"></div>

                                <div class="form-group">
                                    <input type="text" class="form-control" id="editEmployeeNumber" name="editEmployeeNumber">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" id="editStaffName" name="editStaffName">
                                </div>
                                <div class="form-group">
                                    <select name="editRole" id="editRole" class="form-control">
                                        <option value="">Select Role</option>
                                        <option value="CIPA">CIPA</option>
                                        <option value="Coordinator">Coordinator</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select name="editDepartment" id="editDepartment" class="form-control">
                                        <option value="">Select Department</option>
                                        <?php
                                        include_once("../../includes/connection.php");
                                        $query = "SELECT * FROM department_list";
                                        $departments = mysqli_query($connect, $query);
                                        while ($department = mysqli_fetch_assoc($departments)) {
                                            echo "<option value='" . $department['department'] . "'>" . $department['department'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" id="editEmail" name="editEmail">
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control" id="editPassword" name="editPassword" placeholder="New password">
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control" id="editConfirmPassword" name="editConfirmPassword" placeholder="Confirm password">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" id="saveEditBtn" class="btn btn-primary btn-sm">
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
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../assets/js/sidebarscript.js"></script>

    <script>
        // ============ ADD SCRIPT ================
        document.addEventListener("DOMContentLoaded", function() {
            const roleSelect = document.getElementById("role");
            const departmentSelect = document.getElementById("department");

            roleSelect.addEventListener("change", function() {
                if (roleSelect.value === "CIPA") {
                    departmentSelect.disabled = true;
                    departmentSelect.style.backgroundColor = "#e0e0e0"; // Light gray
                    departmentSelect.style.color = "#888"; // Dark gray
                    departmentSelect.style.cursor = "not-allowed";
                } else {
                    departmentSelect.disabled = false;
                    departmentSelect.style.backgroundColor = ""; // Reset to default
                    departmentSelect.style.color = ""; // Reset to default
                    departmentSelect.style.cursor = "";
                }
            });
        });

        // =========== ADD SCRIPT (Password Validation) =================
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.querySelector("form"); // Update if your form has an ID
            const password = document.getElementById("password");
            const confirmPassword = document.getElementById("confirmPassword");

            form.addEventListener("submit", function(event) {
                if (password.value !== confirmPassword.value) {
                    event.preventDefault(); // Stop form submission
                    alert("Passwords do not match! Please re-enter.");
                    confirmPassword.focus();
                }
            });
        });

        // =========== EDIT SCRIPT (Password Validation) ===============
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.querySelector("editForm");
            const password = document.getElementById("editPassword");
            const confirmPassword = document.getElementById("editConfirmPassword");

            form.addEventListener("submit", function(event) {
                if (password.value != confirmPassword.value) {
                    event.preventDefault();
                    alert("Passwords do not match! Please re-enter.");
                    confirmPassword.focus();
                }
            });
        });

        // ============ EDIT SCRIPT (Populate Edit Modal) ==============
        $(document).ready(function() {
            $('.editBtn').click(function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var role = $(this).data('role');
                var department = $(this).data('department');
                var email = $(this).data('email');
                var employeenumber = $(this).data('employeenumber');

                $('#editID').val(id);
                $('#editStaffName').val(name);
                $('#editRole').val(role);
                $('#editDepartment').val(department);
                $('#editEmail').val(email);
                $('#editEmployeeNumber').val(employeenumber)
            });

        // ============ DELETE SCRIPT =============
            // Open Delete Modal
            $(".deleteBtn").click(function() {
                var id = $(this).data("id");
                $("#confirmDelete").data("id", id);
            });

            // Confirm and Process Deletion
            $("#confirmDelete").click(function() {
                var id = $(this).data("id");

                // Hide delete modal
                $("#deleteModal").modal("hide");

                $.ajax({
                    url: "functions/delete_management_acc.php", // Delete function script
                    type: "POST",
                    data: {
                        id: id
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
        });
    </script>

    <!--Add Modal AJAX-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#addForm").submit(function(event) {
                event.preventDefault(); // Prevent page reload

                var formData = $(this).serialize(); // Get form data

                $.ajax({
                    url: "functions/add_management_acc.php",
                    type: "POST",
                    data: formData,
                    dataType: "json",
                    beforeSend: function() {
                        $("#addMessage").html('<div class="alert alert-info">Processing...</div>');
                    },
                    success: function(response) {
                        if (response.status === "success") {
                            $("#addMessage").html('<div class="alert alert-success">' + response.message + "</div>");
                            setTimeout(function() {
                                $("#addModal").modal("hide"); // Hide modal
                                location.reload(); // Reload page
                            }, 1500);
                        } else {
                            $("#addMessage").html('<div class="alert alert-danger">' + response.message + "</div>");
                        }
                    },
                    error: function() {
                        $("#addMessage").html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $("#saveEditBtn").click(function() {
                var formData = $("#editForm").serialize(); // Get all form data

                $.ajax({
                    type: "POST",
                    url: "functions/edit_management_acc.php", // PHP file for processing
                    data: formData,
                    dataType: "json",
                    beforeSend: function() {
                        $("#editMessage").html('<div class="alert alert-info">Processing...</div>');
                    },
                    success: function(response) {
                        if (response.status === "success") {
                            $("#editMessage").html('<div class="alert alert-success">' + response.message + "</div>");
                            setTimeout(function() {
                                $("#editModal").modal("hide"); // Hide modal
                                location.reload(); // Reload page
                            }, 1500);
                        } else {
                            $("#editMessage").html('<div class="alert alert-danger">' + response.message + "</div>");
                        }
                    },
                    error: function() {
                        $("#editMessage").html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
                    }
                });
            });
        });
    </script>

</body>

</html>