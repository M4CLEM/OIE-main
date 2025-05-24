<?php
session_start();
include_once("../includes/connection.php");

// Check if department session is set
if (!isset($_SESSION['department'])) {
    die("Error: Department session is not set.");
}

$department = $_SESSION['department']; // Get the department of the logged-in student

// Query to filter companies based on the department
$query = "SELECT * FROM companylist WHERE dept = ?";
$stmt = $connect->prepare($query);
$stmt->bind_param("s", $department);
$stmt->execute();
$companyResult = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include("../elements/meta.php"); ?>
    <title>Adviser Portal</title>
    <?php include("embed.php"); ?>
    <link rel="stylesheet" href="../assets/css/new-style.css">
</head>

<body id="page-top">

<!-- Page Wrapper -->
<div id="wrapper">

    <!-- Sidebar -->
    <aside id="sidebar" class="expand">
        <?php include('../elements/stud_sidebar.php'); ?>
    </aside>

    <!-- Main Content -->
    <div class="main">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 static-top shadow">
            <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Companies</h4>
            <!-- Topbar Navbar -->
            <?php include('includes/navbar_user_info.php'); ?>
        </nav>
        <!-- End of Topbar -->

        <!-- Page Content -->
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h3 class="m-0 font-weight-bold text-dark mb-2">List of Companies</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                            <tr>
                                <th scope="col">Company Name</th>
                                <th scope="col">Job Role</th>
                                <th width="11%" align="center">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php while ($row = $companyResult->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['companyName']); ?></td>
                                    <td><?php echo htmlspecialchars($row['jobrole']); ?></td>
                                    <td>
                                        <a title="view" href="company-view.php?id=<?php echo $row['No']; ?>" class="btn btn-info btn-sm">View</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>
<script src="../assets/js/sidebarscript.js"></script>

</body>
</html>
