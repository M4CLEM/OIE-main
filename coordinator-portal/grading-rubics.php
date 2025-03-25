<?php
    session_start();
    include_once("../includes/connection.php");
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include("../elements/meta.php"); ?>
        <title>OJT COORDINATOR PORTAL</title>
        <?php include("embed.php"); ?>
    </head>
    <body id="page-top">
        <div class="wrapper">
            <aside id="sidebar" class="expand">
                <?php include('../elements/cood_sidebar.php') ?>
            </aside>
            <div class="main">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 static-top shadow">
                    <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Grading Rubics</h4>
                    <ul class="navbar-nav ml-auto">
                        <div class="topbar-divider d-none d-sm-block"></div>
                            <li class="nav-item dropdown no-arrow">
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">  
                                    <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                        <?php echo $_SESSION['coordinator']; ?>
                                    </span>
                                    <img class="img-profile rounded-circle" src="../img/undraw_profile.svg">
                                </a>
                                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                    aria-labelledby="userDropdown">
                                    <a class="dropdown-item" href="#"><i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i> Profile</a>
                                    <a class="dropdown-item" href="#"><i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i> Settings</a>
                                    <a class="dropdown-item" href="#"><i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i> Activity Log</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="../logout.php"><i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i> Logout</a>
                                </div>
                            </li>
                    </ul>
                </nav>

                <div id="content" class="py-2">
                    <div class="col-lg-13 m-3">
                        <div class="card shadow mb-4">
                            <div class="card-header py-2">
                                <a href="modal.php" class="btn btn-primary addBtn" data-toggle="modal" data-target="#addModal">+Add Grading Rubic</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th scope="col"></th>
                                                <th scope="col">Adviser Weight</th>
                                                <th scope="col">Company Weight</th>
                                                <th scope="col">School Year</th>
                                                <th scope="col">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form action="" method="POST">
                                <div class="modal-header">
                                    <h5>Add Grading Rubic</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <div class="row align-items-center">
                                            <div class="col-md-4 text-right">
                                                <label for="adviserWeight">Adviser Weight</label>
                                            </div>
                                            <div class="col-md-4 text-center">
                                                <input type="range" id="weightSlider" min="0" max="100" value="50" class="form-control-range">
                                            </div>
                                            <div class="col-md-4 text-left">
                                                <label for="companyWeight">Company Weight</label>
                                            </div>
                                        </div>
                                        <div class="row align-items-center mt-2">
                                            <div class="col-md-4 text-right d-flex align-items-center">
                                                <div class="input-group">
                                                    <input type="number" id="adviserWeight" class="form-control text-center" min="0" max="100">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text bg-dark text-white">%</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 text-center"> <!-- Slider placeholder --> </div>
                                            <div class="col-md-4 text-left d-flex align-items-center">
                                                <div class="input-group">
                                                    <input type="number" id="companyWeight" class="form-control text-center" min="0" max="100">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text bg-dark text-white">%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="form-group">
                                            <div class="row">
                                                <label for="semester">Semester</label>
                                                <input type="text" id="semester" name="semester" class="form-control">
                                            </div>
                                            <div class="row">
                                                <label for="schoolYear">School Year</label>
                                                <input type="text" name="schoolYear" id="schoolYear" class="form-control">
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
            </div>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const slider = document.getElementById("weightSlider");
                const adviserInput = document.getElementById("adviserWeight");
                const companyInput = document.getElementById("companyWeight");
        
                function updateFromSlider() {
                    adviserInput.value = slider.value;
                    companyInput.value = 100 - slider.value;
                }

                function updateFromAdviserInput() {
                    let value = parseInt(adviserInput.value);
                    if (isNaN(value) || value < 0) value = 0;
                    if (value > 100) value = 100;
                    adviserInput.value = value;
                    companyInput.value = 100 - value;
                    slider.value = value;
                }

                function updateFromCompanyInput() {
                    let value = parseInt(companyInput.value);
                    if (isNaN(value) || value < 0) value = 0;
                    if (value > 100) value = 100;
                    companyInput.value = value;
                    adviserInput.value = 100 - value;
                    slider.value = 100 - value;
                }
        
                slider.addEventListener("input", updateFromSlider);
                adviserInput.addEventListener("input", updateFromAdviserInput);
                companyInput.addEventListener("input", updateFromCompanyInput);
        
                updateFromSlider();
            });
        </script>
    </body>
</html>