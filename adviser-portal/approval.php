<?php
session_start();
include_once("../includes/connection.php");

$activeSemester = $_SESSION['semester'];
$activeSchoolYear = $_SESSION['schoolYear'];

if(isset($_SESSION['dept_sec']) && !empty($_SESSION['dept_sec'])) {
    // If sections are available, filter the query based on them
    $sections = implode("','", $_SESSION['dept_sec']);
    $query = "SELECT * FROM company_info WHERE section IN ('$sections') AND semester = '$activeSemester' AND schoolYear = '$activeSchoolYear'";
}

$result = mysqli_query($connect, $query);


if (isset($_POST['Approve'])) {
    echo ('button clicked');
    $query = "select * from company_info"; 
    $result = mysqli_query($connect, $query);

    $companyCode = $_POST['companyCode'];
    $studentEmail = $_POST['studentEmail'];
    $trainerEmail = $_POST['trainerEmail'];

    echo $studentEmail;

    $updateCompanyInfo = $connect->prepare("UPDATE company_info SET status = 'Approved' WHERE companyCode = ?");
    $updateCompanyInfo->bind_param("s", $companyCode);
    $updateCompanyInfo->execute();

    $updateStudentInfo = $connect->prepare("UPDATE studentinfo SET status = 'Deployed', companyCode = ?, trainerEmail = ? WHERE email = ? AND semester = ? AND school_year = ?");
    $updateStudentInfo->bind_param("sssss", $companyCode, $trainerEmail, $studentEmail, $activeSemester, $activeSchoolYear);
    $updateStudentInfo->execute();

    header("Location: approval.php");

    exit;
}

if (isset($_POST['ApproveChange'])) {
    $companyCode = $_POST['companyCode'];


    // Delete the row from the company_info table
    $deleteQuery = "DELETE FROM company_info WHERE companyCode = ?";
    $stmt = $connect->prepare($deleteQuery);
    $stmt->bind_param("s", $companyCode);
    $stmt->execute();

    header("Location: approval.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <?php include("../elements/meta.php"); ?>
    <title>Adviser Portal</title>
    <?php include("embed.php"); ?>

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!--Sidebar Wrapper-->
        <aside id="sidebar" class="expand">
            <?php include('../elements/adv_sidebar.php') ?>
        </aside>

        <div class="main">

            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 static-top shadow">

                <!-- Title -->
                <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Student Interns</h4>

                <!-- Topbar Navbar -->
                <ul class="navbar-nav ml-auto">

                    <div class="topbar-divider d-none d-sm-block"></div>

                    <!-- Nav Item - User Information -->
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                <?php (isset($_SESSION['adviser'])) ?> <?php echo $_SESSION['adviser']; ?></span>
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

            <!-- Begin Page Content -->
            <div class="col-lg-12 mb-4">

                <!-- Illustrations -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-dark">DEPLOYMENT INFORMATION</h6>
                    </div>
                    <div class="card-body">
                        <style>
                            .table td,
                            .table th {
                                font-size: 11px;
                            }
                        </style>
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th scope="col" width=25%;>Student Name</th>
                                        <th scope="col" width=10%;>Company Code</th>
                                        <th scope="col">Company Name</th>
                                        <th scope="col">Company Address</th>
                                        <th scope="col">Contact Number</th>
                                        <th scope="col" width=15%;>Trainer Email</th>
                                        <th scope="col">Work Type</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {

                                            $queryStud = "SELECT * FROM studentinfo WHERE email='{$row['student_email']}'";
                                            $resultStud = mysqli_query($connect, $queryStud);
                                            $rowStud = $resultStud->fetch_assoc();
                                            echo "<form method='post'>";
                                            echo "<input type='hidden' name='studentEmail' value='{$row['student_email']}'>";
                                            echo "<input type='hidden' name='companyCode' value='{$row['companyCode']}'>";
                                            echo "<input type='hidden' name='trainerEmail' value='{$row['trainerEmail']}'>";
                                            echo "<tr>";
                                            echo "<td>" . $rowStud['firstname'] . " " . $rowStud['middlename'] . " " . $rowStud['lastname'] . "</td>";
                                            echo "<td>" . $row['companyCode'] . "</td>";
                                            echo "<td>" . $row['companyName'] . "</td>";
                                            echo "<td>" . $row['companyAddress'] . "</td>";
                                            echo "<td>" . $row['trainerContact'] . "</td>";
                                            echo "<td>" . $row['trainerEmail'] . "</td>";
                                            echo "<td>" . $row['workType'] . "</td>";
                                            if ($row['status'] == 'Change Request') {
                                                echo "<td><form action='delete_company_change.php' method='post'>";
                                                echo "<input type='hidden' name='companyCode' value='{$row['companyCode']}'>";
                                                echo "<input type='hidden' name='companyEmail' value='{$row['trainerEmail']}'>";
                                                echo "<button type='submit' name='ApproveChange' class='btn btn-success rounded-2' style='font-size: 11px;'>Approve Company Change</button>";
                                            } elseif ($row['status'] == 'Pending') {
                                                echo "<td><input type='submit' name='Approve' class='btn btn-sm btn-success w-100 rounded-2' value='Approve'><span class='fa fa-sign-in fw-fa'></span></td>";
                                            } elseif ($row['status'] == 'Approved') {
                                                echo "<td><b><p style='color: green;'>Approved!</p></b></td>";
                                            } else {
                                                echo "<b><p style='color: gray;'>Status Unknown</p></b>";
                                            }

                                            echo "</td>";
                                            echo "</tr>";
                                            echo "</form>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='7'>No students found.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>

                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>
    <!-- End of Main Content -->

    </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    <script src="../assets/js/sidebarscript.js"></script>

</body>

</html>