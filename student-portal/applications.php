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
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 static-top shadow">
                    <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Applications</h4>
                    <!-- Topbar Navbar -->
                    <?php include('../elements/stud_navbar_user_info.php'); ?>
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
            
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
            crossorigin="anonymous"></script>
        <script src="../assets/js/sidebarscript.js"></script>
    </body>
</html>