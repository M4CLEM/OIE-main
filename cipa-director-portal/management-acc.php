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
                <?php include('../elements/cipa_navbar_user_info.php') ?>
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

        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="../assets/js/sidebarscript.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    // Role / Department dropdown toggle for Add Modal
    document.addEventListener("DOMContentLoaded", function() {
        const roleSelect = document.getElementById("role");
        const departmentSelect = document.getElementById("department");

        function toggleDepartment() {
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
        }

        roleSelect.addEventListener("change", toggleDepartment);
        toggleDepartment(); // Run on page load to set initial state
    });

    // Password validation for Add Form (before AJAX submit)
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById("addForm");
        const password = document.getElementById("password");
        const confirmPassword = document.getElementById("confirmPassword");

        if (form) {
            form.addEventListener("submit", function(event) {
                if (password.value !== confirmPassword.value) {
                    event.preventDefault(); // Stop form submission
                    alert("Passwords do not match! Please re-enter.");
                    confirmPassword.focus();
                }
            });
        }
    });

    // AJAX Submit for Add Form
    $(document).ready(function() {
        $("#addForm").submit(function(event) {
            event.preventDefault(); // Prevent page reload

            var formData = $(this).serialize();

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
                            $("#addModal").modal("hide");
                            location.reload();
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

    // AJAX Submit for Edit Form with password validation inside the click handler
    $(document).ready(function() {
        $("#saveEditBtn").click(function() {
            var password = $("#editPassword").val();
            var confirmPassword = $("#editConfirmPassword").val();

            if (password !== confirmPassword) {
                alert("Passwords do not match! Please re-enter.");
                $("#editConfirmPassword").focus();
                return; // Stop AJAX call
            }

            var formData = $("#editForm").serialize();

            $.ajax({
                type: "POST",
                url: "functions/edit_management_acc.php",
                data: formData,
                dataType: "json",
                beforeSend: function() {
                    $("#editMessage").html('<div class="alert alert-info">Processing...</div>');
                },
                success: function(response) {
                    if (response.status === "success") {
                        $("#editMessage").html('<div class="alert alert-success">' + response.message + "</div>");
                        setTimeout(function() {
                            $("#editModal").modal("hide");
                            location.reload();
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

    // Populate Edit Modal and Delete Modal logic remains unchanged (your original jQuery code)
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
            $('#editEmployeeNumber').val(employeenumber);
        });

        // Delete modal open
        $(".deleteBtn").click(function() {
            var id = $(this).data("id");
            $("#confirmDelete").data("id", id);
        });

        // Confirm delete
        $("#confirmDelete").click(function() {
            var id = $(this).data("id");
            $("#deleteModal").modal("hide");

            $.ajax({
                url: "functions/delete_management_acc.php",
                type: "POST",
                data: { id: id },
                success: function(response) {
                    $("#successDeleteModal").modal("show");
                },
                error: function(xhr, status, error) {
                    alert("An error occurred: " + error);
                }
            });
        });

        // Reload page after success delete modal closes or OK clicked
        $("#successDeleteModal").on("hidden.bs.modal", function() {
            location.reload();
        });
        $("#successDeleteModal .btn-primary").click(function() {
            $("#successDeleteModal").modal("hide");
        });
    });
</script>
