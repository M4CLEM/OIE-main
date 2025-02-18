<?php
session_start();
include_once("../includes/connection.php");
$program = $_SESSION['program'];
$result=mysqli_query($connect,"SELECT * FROM criteria_list WHERE program = '$program'");
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
            <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Grading</h4>

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

                <!-- Begin Page Content -->
                <div class="col-lg-13 m-3">
                    <form id="gradingForm" method="post" action="functions/submit.php">
                        
                        <div id="gradingCriteria">
                            <div class="card shadow mb-4">
                                <div class="card-body">
                                    <div class="form-group row">
                                        <div class="col-md-8">
                                            <label for="title">Grading Criteria Title</label>
                                            <input type="text" class="form-control" id="title" name="title[]" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="percentage">Percentage</label>
                                            <select class="form-control" id="percentage" name="percentage[]">
                                                <option value="5">5%</option>
                                                <option value="10">10%</option>
                                                <option value="15">15%</option>
                                                <option value="20">20%</option>
                                                <option value="25">25%</option>
                                                <option value="30">30%</option>
                                                <option value="35">35%</option>
                                                <option value="40">40%</option>
                                                <option value="45">45%</option>
                                                <option value="50">50%</option>
                                                <option value="55">55%</option>
                                                <option value="60">60%</option>
                                                <option value="65">65%</option>
                                                <option value="70">70%</option>
                                                <option value="75">75%</option>
                                                <option value="80">80%</option>
                                                <option value="85">85%</option>
                                                <option value="90">90%</option>
                                                <option value="95">95%</option>
                                                <option value="100">100%</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea class="form-control" id="description" name="description[]" rows="3" required></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-primary" id="addCriteria">+</button>
                        <button type="submit" class="btn btn-success">Submit</button>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
            crossorigin="anonymous"></script>
    <script src="../assets/js/sidebarscript.js"></script>

    <script>

        document.getElementById('addCriteria').addEventListener('click', function() {
            var gradingCriteria = document.getElementById('gradingCriteria');
            var newCriteria = document.createElement('div');
            newCriteria.innerHTML = `
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-8">
                                <label for="title">Grading Criteria Title</label>
                                <input type="text" class="form-control" id="title" name="title[]" required>
                            </div>
                            <div class="col-md-4">
                                <label for="percentage">Percentage</label>
                                <select class="form-control" id="percentage" name="percentage[]">
                                    <option value="5">5%</option>
                                    <option value="10">10%</option>
                                    <option value="15">15%</option>
                                    <option value="20">20%</option>
                                    <option value="25">25%</option>
                                    <option value="30">30%</option>
                                    <option value="35">35%</option>
                                    <option value="40">40%</option>
                                    <option value="45">45%</option>
                                    <option value="50">50%</option>
                                    <option value="55">55%</option>
                                    <option value="60">60%</option>
                                    <option value="65">65%</option>
                                    <option value="70">70%</option>
                                    <option value="75">75%</option>
                                    <option value="80">80%</option>
                                    <option value="85">85%</option>
                                    <option value="90">90%</option>
                                    <option value="95">95%</option>
                                    <option value="100">100%</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <input type="text" class="form-control" id="description" name="description[]" required>
                        </div>
                    </div>
                </div>
            `;
            gradingCriteria.appendChild(newCriteria);
        });

    </script>

</body>
</html>	