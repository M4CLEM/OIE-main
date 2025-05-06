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

// Fetch student image using prepared statement
$image = null;
if (isset($_SESSION['student'])) {
    $email = $_SESSION['student'];
    $stmtImg = $connect->prepare("SELECT image FROM studentinfo WHERE email = ?");
    $stmtImg->bind_param("s", $email);
    $stmtImg->execute();
    $imgResult = $stmtImg->get_result();
    if ($imgResult->num_rows > 0) {
        $row = $imgResult->fetch_assoc();
        $image = $row['image'] ?? null;
    }
}

// Function to convert Google Drive links to direct image links
function get_drive_image_url($image) {
    if (strpos($image, 'drive.google.com') !== false) {
        preg_match('/(?:id=|\/d\/)([a-zA-Z0-9_-]{25,})/', $image, $matches);
        $image = $matches[1] ?? null;
    }

    if ($image && preg_match('/^[a-zA-Z0-9_-]{25,}$/', $image)) {
        return "https://lh3.googleusercontent.com/d/{$image}=w1000";
    }
    return $image;
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
            <ul class="navbar-nav ml-auto">
                <div class="topbar-divider d-none d-sm-block"></div>

                <!-- User Info -->
                <li class="nav-item dropdown no-arrow">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                            <?php echo htmlspecialchars($_SESSION['student']); ?>
                        </span>
                        <img class="img-profile rounded-circle"
                             src="<?php echo $image ? get_drive_image_url($image) : '../img/undraw_profile.svg'; ?>">
                    </a>

                    <!-- Dropdown Menu -->
                    <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                         aria-labelledby="userDropdown">
                        <a class="dropdown-item" href="#"><i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>Profile</a>
                        <a class="dropdown-item" href="#"><i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>Settings</a>
                        <a class="dropdown-item" href="#"><i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>Activity Log</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="../logout.php" data-toggle="modal" data-target="#logoutModal">
                            <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>Logout
                        </a>
                    </div>
                </li>
            </ul>
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

<!-- Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
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

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>
<script src="../assets/js/sidebarscript.js"></script>

</body>
</html>
