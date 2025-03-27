<?php
    session_start();
    include_once("../includes/connection.php");
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include("../elements/meta.php"); ?>
        <title>CIPA ADMIN</title>
        <?php include("embed.php"); ?>

        <style>
            /* Default style for the department dropdown */
            #department:disabled {
                background-color: #e0e0e0; /* Light gray */
                color: #888; /* Dark gray */
                cursor: not-allowed;
            }
        </style>
    </head>
    <body id="page-top">
        <div class="wrapper">
            <aside id="sidebar" class="expand">
                <?php include('../elements/cipa_sidebar.php')?>
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
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th scope="col">Name</th>
                                                <th scope="col">Department</th>
                                                <th scope="col">Email</th>
                                                <th scope="col">Password</th>
                                                <th scope="col">Role</th>
                                                <th scope="col">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addAdvisers" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form action="functions/add_management_acc.php" method="POST">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addAdvisers">Add Management Account</h5>
                                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                                <div class="modal-body">
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
                                        <input type="text" class="form-control" id="email" name="email" placeholder="Email" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="password" name="password" placeholder="Password" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Confirm Password" required>
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
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="../assets/js/sidebarscript.js"></script>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const roleSelect = document.getElementById("role");
                const departmentSelect = document.getElementById("department");

                roleSelect.addEventListener("change", function () {
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

            document.addEventListener("DOMContentLoaded", function () {
                const form = document.querySelector("form"); // Update if your form has an ID
                const password = document.getElementById("password");
                const confirmPassword = document.getElementById("confirmPassword");

                form.addEventListener("submit", function (event) {
                    if (password.value !== confirmPassword.value) {
                        event.preventDefault(); // Stop form submission
                        alert("Passwords do not match! Please re-enter.");
                        confirmPassword.focus();
                    }
                });
            });
        </script>
    </body>
</html>