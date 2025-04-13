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
                <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Masterlist - <?php echo $_SESSION['coordinator']; ?></h4>

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
                            <a class="dropdown-item" href="../logout.php" data-toggle="logout" data-target="logout">
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
                                    <th scope="col">Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Section</th>
                                    <th scope="col">Department</th>
                                    <th width="10%" align="center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($rows = mysqli_fetch_assoc($result)) {
                                ?>
                                    <tr>
                                        <td><?php echo $rows['fullName']; ?></td>
                                        <td><?php echo $rows['email']; ?></td>
                                        <td><?php echo $rows['section']; ?></td>
                                        <td><?php echo $rows['dept']; ?></td>
                                        <td>
                                            <a title="Edit" href="advisers-edit.php?id=<?php echo $rows['id']; ?>" class="btn btn-xs"><span class="fa fa-edit fw-fa"></span>
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

        function showCourses(department) {
            $.ajax({
                url: 'functions/get_courses.php',
                type: 'GET',
                data: {
                    department: department
                },
                success: function(data) {
                    document.getElementById('dropdowncourse').innerHTML = data;
                }
            });
        }

        function showSections(department, course) {
            $.ajax({
                url: 'functions/get_sec.php',
                type: 'GET',
                data: {
                    department: department,
                    course: course
                },
                success: function(data) {
                    document.getElementById('dropdownsection').innerHTML = data;
                    bindSectionCheckboxes(); // Bind after content is loaded
                }
            });
        }

        function bindSectionCheckboxes() {
            const checkboxes = document.querySelectorAll('.section-check');
            const sectionInput = document.getElementById('sectionInput');
            const dropdownBtn = document.getElementById('dropdownSectionBtn');

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', () => {
                    const selected = Array.from(checkboxes)
                    .filter(cb => cb.checked)
                    .map(cb => cb.value);

                    // Update hidden input
                    sectionInput.value = selected.join(',');

                    // Update dropdown button text
                    dropdownBtn.textContent = selected.length > 0 ? selected.join(', ') : 'Select Sections';

                    // Toggle "selected" class on labels
                    checkboxes.forEach(cb => {
                        const label = cb.closest('.dropdown-item');
                        if (cb.checked) {
                            label.classList.add('selected');
                        } else {
                            label.classList.remove('selected');
                        }
                    });
                });
            });
        }

    </script>
</body>

</html>