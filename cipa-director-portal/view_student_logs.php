<?php
    include_once("../includes/connection.php");
    $connect = new mysqli('localhost', 'root', '', 'plmunoiedb');

    include("functions/view_logs.php");

    if (isset($_GET['number'])) {
        $studentNumber = $_GET['number'];

    } else {
        echo "No student number provided.";
    }

    $post = new viewStudentLogs();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include("../elements/meta.php"); ?>
    <title>CIPA ADMIN</title>
    <?php include("embed.php"); ?>
</head>
<body>

    <!-- Page Wrapper -->
    <div class="wrapper">

        <!--Sidebar Wrapper-->
        <aside id="sidebar" class="expand">
            <?php include('../elements/cipa_sidebar.php')?>
        </aside>

        <div class="main">

            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar static-top shadow mb-3">
                <!-- Dashboard Title -->
                <h2 class="my-0 mr-auto font-weight-bold text-dark ml-3">Student Interns</h2>

                <!-- Sidebar Toggle (Topbar) -->
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                    <i class="fa fa-bars"></i>
                </button>

                <ul class="navbar-nav ml-auto">
                    <div class="topbar-divider d-none d-sm-block"></div>
                    <!-- Nav Item - User Information -->
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">  
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                <?php echo $_SESSION['admin']; ?></span>
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

                <div class="container mt-6">

                    <div class="row">

                        <div class='card-body col-md-12 border m-3 rounded'>
                            <div class="m-4">
                                <?php $post->loadStudentInfo($connect, $studentNumber); ?>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-12 border m-3 rounded">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                    <th>Date</th>
                                    <th>Time in</th>
                                    <th>Time out</th>
                                    <th>Total hours</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        if (isset($_POST['filter'])) {
                                            $dateFrom = $_POST['dateFrom'];
                                            $dateTo = $_POST['dateTo'];
                                            $post->loadStudentLogs($connect, $studentNumber, $dateFrom, $dateTo);
                                        } else {
                                            $post->loadStudentLogs($connect, $studentNumber);
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 border m-3 rounded">
                            <form method="post" class="form-inline p-4">
                                <label for="dateFrom">Filter Date: </label>
                                <div class="input-group mb-2">
                                    <span class="input-group-text ml-3">From:</span>
                                    <input type="date" id="dateFrom" name="dateFrom" class="form-control">
                                    <span class="input-group-text ml-3">To:</span>
                                    <input type="date" id="dateTo" name="dateTo" class="form-control">
                                </div>
                                <div class="input-group-append mb-2 ml-3 text-right">
                                    <input type="submit" name="filter" value="Submit" class="btn btn-outline-secondary">
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
\        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
            crossorigin="anonymous"></script>
    <script src="../assets/js/sidebarscript.js"></script>

</body>
</html>