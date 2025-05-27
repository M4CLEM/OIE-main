<?php
include_once("../includes/connection.php");
session_start();

$activeSemester = $_SESSION['semester'];
$activeSchoolYear = $_SESSION['schoolYear'];
$companyName = $_SESSION['companyName'];
$applicationStat = 'Pending';

$query = "
    SELECT a.id AS applicationID, a.studentID, a.jobrole, a.companyCode, a.applicationDate,
           s.firstName, s.lastName, s.course, s.section, d.document, d.file_name, d.file_link
    FROM applications a
    JOIN student_masterlist s ON a.studentID = s.studentID
    JOIN documents d ON a.studentID = d.student_ID
    WHERE a.companyName = ? 
      AND a.semester = ? 
      AND a.schoolYear = ? 
      AND a.status = ?
      AND s.semester = ? 
      AND s.schoolYear = ?
      AND d.semester = ?
      AND d.schoolYear = ?
      AND d.document = 'Resume'
";

$stmt = $connect->prepare($query);
$stmt->bind_param("ssssssss", $companyName, $activeSemester, $activeSchoolYear, $applicationStat, $activeSemester, $activeSchoolYear, $activeSemester, $activeSchoolYear);
$stmt->execute();
$result = $stmt->get_result();

$applicants = [];
while ($row = $result->fetch_assoc()) {
    $applicants[] = $row;
}
$stmt->close();
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
            <!--Sidebar Wrapper-->
            <aside id="sidebar" class="expand">
                <?php include('../elements/ip_sidebar.php') ?>
            </aside>

            <div class="main">
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

                <div class="col-lg-12 mb-4">
                    <div class="card shadow mb-3">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-dark">List of Applicants</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="1">
                                    <thead>
                                        <tr>
                                            <th>Student Number</th>
                                            <th>Student Name</th>
                                            <th>Course-Section</th>
                                            <th>Jobrole</th>
                                            <th>Application Date</th>
                                            <th>Resume</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody style="max-height: 80vh; overflow-y: auto;">
                                        <?php if (!empty($applicants)): ?>
                                            <?php foreach ($applicants as $applicant): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($applicant['studentID']) ?></td>
                                                    <td><?= htmlspecialchars($applicant['lastName'] . ', ' . $applicant['firstName']) ?></td>
                                                    <td><?= htmlspecialchars($applicant['course'] . '-' . $applicant['section']) ?></td>
                                                    <td><?= htmlspecialchars($applicant['jobrole']) ?></td>
                                                    <td><?= htmlspecialchars($applicant['applicationDate']) ?></td>
                                                    <td><?= htmlspecialchars($applicant['file_name']) ?></td>
                                                    <td>
                                                        <a class="btn btn-primary btn-sm" href="<?= htmlspecialchars($applicant['file_link']) ?>" target="_blank" rel="noopener noreferrer"><i class="fa fa-eye">View</i></a>
                                                        <!-- Approve Form -->
                                                        <form action="functions/application_approval.php" method="POST" style="display:inline;">
                                                            <input type="hidden" name="studentID" value="<?= htmlspecialchars($applicant['studentID']) ?>">
                                                            <input type="hidden" name="companyCode" value="<?= htmlspecialchars($applicant['companyCode']) ?>">
                                                            <input type="hidden" name="applicationID" value="<?= htmlspecialchars($applicant['applicationID']) ?>">
                                                            <input type="hidden" name="jobrole" value="<?= htmlspecialchars($applicant['jobrole']) ?>">
                                                            <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                                        </form>

                                                        <!-- Reject Form -->
                                                        <form action="application_rejection.php" method="POST" style="display:inline;">
                                                            <input type="hidden" name="studentID" value="<?= htmlspecialchars($applicant['studentID']) ?>">
                                                            <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center">No applicants found.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
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

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
            <script src="../assets/js/sidebarscript.js"></script>
        </div>
    </body>
</html>