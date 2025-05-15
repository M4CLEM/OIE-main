<?php
    session_start();
    include("../includes/connection.php");

    // Get the student's email from the session
    $email = $_SESSION['student'];
    $semester = $_SESSION['semester'];
    $schoolYear = $_SESSION['schoolYear'];

    $applicationQuery = "SELECT * FROM applications WHERE email = ? AND semester = ? AND schoolYear = ?";
    $applicationStmt = $connect->prepare($applicationQuery);
    $applicationStmt->bind_param("sss", $email, $semester, $schoolYear);
    $applicationStmt->execute();
    $applicationResult = $applicationStmt->get_result();
?>

<!DOCTYPE html>
<html>
    <head>
        <?php include("../elements/meta.php"); ?>
        <title>Intern Portal</title>
        <?php include("embed.php"); ?>
        <link rel="stylesheet" href="../assets/css/new-style.css">
    </head>
    <body id="page-top">
        <div id="wrapper">
            <!-- Sidebar Wrapper -->
            <aside id="sidebar" class="expand">
                <?php include('../elements/stud_sidebar.php') ?>
            </aside>

            <div class="main">
                <?php
                    $email = $_SESSION['student'];
                    $query = "SELECT * FROM studentinfo WHERE email ='$email'";
                    $result = mysqli_query($connect, $query);
                    while ($rows = mysqli_fetch_array($result)) {
                        $image = $rows['image'];
                    }
                ?>

                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 static-top shadow">
                    <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Applications</h4>
                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    <?php echo $_SESSION['student']; ?>
                                </span>
                                <?php
                                    function get_drive_image_url($image)
                                    {
                                        // Check if the image is a Google Drive URL
                                        if (strpos($image, 'drive.google.com') !== false) {
                                            // Extract the File ID from different Drive URL formats
                                            preg_match('/(?:id=|\/d\/)([a-zA-Z0-9_-]{25,})/', $image, $matches);
                                            $image = $matches[1] ?? null; // Get the File ID if found
                                        }

                                        // If a valid Google Drive File ID is found, return the direct image link
                                        if ($image && preg_match('/^[a-zA-Z0-9_-]{25,}$/', $image)) {
                                            return "https://lh3.googleusercontent.com/d/{$image}=w1000";
                                        }
                                        // If it's not a Google Drive image, return it as is
                                        return $image;
                                    }
                                ?>
                                <img class="img-profile rounded-circle" src="<?php echo $image ? get_drive_image_url($image) : '../img/undraw_profile.svg'; ?>">
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
                                <a class="dropdown-item" href="../logout.php" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
                
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Company</th>
                                        <th>Jobrole</th>
                                        <th>Application Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($applicationResult->num_rows > 0): ?>
                                        <?php while ($row = $applicationResult->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($row['companyName']) ?></td>
                                                <td><?= htmlspecialchars($row['jobrole']) ?></td>
                                                <td><?= htmlspecialchars($row['applicationDate'])?></td>
                                                <td><?= htmlspecialchars($row['status']) ?></td>
                                                <td>
                                                    <!-- Replace with your desired action, such as view/delete -->
                                                    <a href="view_application.php?id=<?= $row['id'] ?>" class="btn btn-info btn-sm">View</a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No applications found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            

            <!-- LOG OUT MODAL-->
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
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
            crossorigin="anonymous"></script>
        <script src="../assets/js/sidebarscript.js"></script>
    </body>
</html>