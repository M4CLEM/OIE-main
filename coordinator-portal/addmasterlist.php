<?php
session_start();
include_once("../includes/connection.php");
$result=mysqli_query($connect,"SELECT * FROM studentinfo WHERE status = 'ENROLLED'");
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
            <?php include('../elements/cood_sidebar.php')?>
        </aside>

        <div class="main">

            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 static-top shadow">

            <!-- Title -->
            <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Masterlist</h4>

                <!-- Topbar Navbar -->
                <ul class="navbar-nav ml-auto">

                    <div class="topbar-divider d-none d-sm-block"></div>

                    <!-- Nav Item - User Information -->
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">  
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                <?php (isset($_SESSION['coordinator'])) ?> <?php echo $_SESSION['coordinator']; ?></span>
                            <img class="img-profile rounded-circle"
                                src="../img/undraw_profile.svg">
                        </a>
                        <!-- Dropdown - User Information -->
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                            aria-labelledby="userDropdown">
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
            <div id="content" class="py-2">

            <div class="col-lg-12 mb-4">
                <!-- Illustrations -->
                <div class="card shadow mb-4">
            
                    <div class="container-fluid m-4">
                        <form method="post" action="import.php" enctype="multipart/form-data" id="form">

                            <div class="row mb-3">
                                <label for="School year" class="col-sm-2 col-form-label">School Year</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="colFormLabel" placeholder="School year" name="SY">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="Department" class="col-sm-2 col-form-label">Department</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="colFormLabel" placeholder="Department" name="dept">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="Course" class="col-sm-2 col-form-label">Course</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="colFormLabel" placeholder="Course" name="course">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="Section" class="col-sm-2 col-form-label">Section</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="colFormLabel" placeholder="Section" name="section">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-11">
                                    <div class="input-group">
                                        <label class="input-group-text" for="inputGroupSelect02">Semester</label>
                                        <select class="form-select" id="inputGroupSelect02" name="Semester">
                                            <option selected>Semester</option>
                                            <option value="1st semester">1st semester</option>
                                            <option value="2nd semester">2nd semester</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-11">
                                    <div class="input-group mb-3">
                                        <input type="file" name="file" class="form-control" id="exampleInputFile">
                                        <label class="input-group-text" for="exampleInputFile">Upload</label>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" name="submit"class="btn btn-outline-success">Submit</button>
                    
                        </form>
                    </div>
                </div>  
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
            crossorigin="anonymous"></script>
    <script src="../assets/js/sidebarscript.js"></script>

    <!-- Loading Modal -->
    <div id="loadingModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; text-align: center;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 10px;">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                <span class="sr-only">Loading...</span>
            </div>
            <p>Processing... Please wait.</p>
        </div>
    </div>

    <!-- Script to show the loading modal -->
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelector("form").addEventListener("submit", function () {
            document.getElementById("loadingModal").style.display = "block";
        });
    });
    </script>
</body>
</html> 