<?php
    include_once("../includes/connection.php");
    session_start();

    $activeSemester = $_SESSION['semester'];
    $activeSchoolYear = $_SESSION['schoolYear'];
    $companyName = $_SESSION['companyName'];

    $studQuery = "SELECT si.*, ci.*, si.trainerEmail AS si_trainerEmail, ci.trainerEmail AS ci_trainerEmail, ci.trainerContact AS ci_trainerContact
    FROM studentinfo si
    INNER JOIN company_info ci ON si.studentID = ci.studentID
    WHERE ci.companyName = ?
        AND ci.semester = ?
        AND ci.schoolYear = ?
        AND si.semester = ?
        AND si.school_year = ?
    ";

    $studStmt = $connect->prepare($studQuery);
    $studStmt->bind_param("sssss", $companyName, $activeSemester, $activeSchoolYear, $activeSemester, $activeSchoolYear);
    $studStmt->execute();
    $studResult = $studStmt->get_result();

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
                    <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Intern Tracker</h4>
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
                    <div class="col-md-5 mb-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-dark">STUDENT INTERNS</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>

                                            </tr>
                                            <tr>
                                                <th class="small text-center" scope="col">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="selectAll" style='transform: scale(1.5); margin-top: 6px'>
                                                    </div>
                                                </th>
                                                <th class="small" scope="col">STUDENT NO.</th>
                                                <th class="small" scope="col">NAME</th>
                                                <th class="small" scope="col">COURSE-SECTION</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($studResult->num_rows > 0): ?>
                                                <?php while ($row = $studResult->fetch_assoc()): ?>
                                                    <?php
                                                        // Check if any of the required fields are empty or null
                                                        $isIncomplete = empty($row['si_trainerEmail']) || empty($row['ci_trainerEmail']) || empty($row['ci_trainerContact']);

                                                        // Assign row class based on condition
                                                        $rowClass = $isIncomplete ? 'table-danger' : '';
                                                    ?>
                                                    <tr class="<?php echo $rowClass; ?>">
                                                        <td class="text-center">
                                                            <div class="form-check">
                                                                <input class="form-check-input student-checkbox" type="checkbox" name="selected_students[]" value="<?php echo htmlspecialchars($row['studentID']); ?>" style='transform: scale(1.5); margin-top: 6px'>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <a href="#" class="student-link" data-studentnumber="<?php echo htmlspecialchars($row['studentID']); ?>">
                                                                <?php echo htmlspecialchars($row['studentID']); ?>
                                                            </a>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['course'] . '-' . $row['section']); ?></td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="9" class="text-center">No students found.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-7 mb-4 p-lg-0">

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