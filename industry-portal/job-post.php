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
                    <ul class="navbar-nav ml-auto">
                        <div class="topbar-divider d-none d-sm-block"></div>
                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    <?php (isset($_SESSION['IndustryPartner'])) ?> <?php echo $_SESSION['IndustryPartner']; ?></span>
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

                <div class="row m-1">
                    <div class="col-md-6 mb-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h5 class="m-0 font-weight-bold text-dark">Jobs</h5>
                                <button class="btn btn-primary btn-sm addBtn" type="button" data-target="" data-toggle="modal">Post Job</button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered"  width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th scope="col">JOBROLE</th>
                                                <th scope="col">WORK TYPE</th>
                                                <th scope="col" width="23%">ACTION</th>
                                            </tr>
                                        </thead>
                                       <tbody>
                                            <?php
                                                if ($jobResult->num_rows === 0): ?>
                                                    <tr><td colspan="2">No job roles found.</td></tr>
                                            <?php else:
                                                while ($row = $jobResult->fetch_assoc()):
                                                    $jobRole = trim($row['jobrole']);
                                                    $workType = trim($row['workType']);
                                                ?>
                                                    <tr>
                                                        <td>
                                                            <a href="#" class="jobrole-link" data-jobrole="<?php echo htmlspecialchars($jobRole);?>">
                                                                <?php echo htmlspecialchars($jobRole);?>
                                                            </a>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($workType); ?></td>
                                                        <td>
                                                            <a href="" class="btn btn-primary btn-sm editBtn">Edit</a>
                                                            <button type="button" class="btn btn-danger btn-sm deleteBtn">Delete</button>
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
                            <div class="card-body">
                                <div class="row m-1">
                                    <div class="row">
                                        <div class="col">
                                            <label for="">Company Number:</label>
                                            <p></p>
                                        </div>
                                        <div class="col">
                                            <label for="">Jobrole</label>
                                            <p></p>
                                        </div>
                                        <div class="col">
                                            <label for="">Work Type</label>
                                            <p></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="">Job Description:</label>
                                        <p></p>
                                    </div>
                                    <div class="row">
                                        <label for="">Job Requirements:</label>
                                        <p></p>
                                    </div>
                                    <div class="row">
                                        <label for="">Link:</label>
                                        <p></p>
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

        <!-- Logout Modal-->
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
    </body>
</html>