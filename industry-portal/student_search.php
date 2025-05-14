<?php
include_once("../includes/connection.php");
session_start();
$queryDept = "SELECT * FROM studentinfo WHERE status = 'Undeployed'";
$stmtDept = $connect->prepare($queryDept);
$stmtDept->execute();
$result = mysqli_stmt_get_result($stmtDept);
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <?php include("../elements/meta.php"); ?>
    <title>Industry Partner Portal</title>
    <?php include("embed.php"); ?>
    <link rel="stylesheet" href="../assets/css/new-style.css">

</head>


<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!--Sidebar Wrapper-->
        <aside id="sidebar" class="expand">
            <?php include('../elements/ip_sidebar.php') ?>
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
            <!-- End of Topbar -->

            <!-- Begin Page Content -->
            <div class="col-lg-12 mb-4">

                <!-- Illustrations -->
                <div class="card shadow mb-3">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-dark">List of Undeployed Interns</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover" id="studentTable" width="100%" cellspacing="1">
                                <thead>
                                    <tr>
                                        <th colspan="6" class="text-center font-weight-bold border-0 pt-1"><input type="text" class="form-control form-control-sm" id="searchInput" placeholder="Search..."></th>
                                    </tr>
                                    <tr>
                                        <th scope="col" class="small">Name</th>
                                        <th scope="col" class="small">Course</th>
                                        <th scope="col" class="small">Section</th>
                                        <th scope="col" class="small">Email</th>
                                        <th scope="col" class="small">Contact</th>
                                        <th scope="col" class="small">Skills</th>
                                        <th scope="col" class="small">Resume</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($rows = mysqli_fetch_assoc($result)) {
                                    ?>
                                        <tr>
                                            <td class="small"><?php echo $rows['firstname'] . ' ' . $rows['lastname']; ?></td>
                                            <td class="small"><?php echo $rows['course']; ?></td>
                                            <td class="small"><?php echo $rows['section']; ?></td>
                                            <td class="small"><?php echo $rows['email']; ?></td>
                                            <td class="small"><?php echo $rows['contactNo']; ?></td>
                                            <td class="small"><?php echo $rows['skills']; ?></td>
                                            <td class="small">
                                                <?php
                                                $studentID = $rows['studentID'];

                                                // Query to check if a resume is available for the student
                                                $query = "SELECT file_name FROM documents WHERE document = 'Resume' AND student_id = ?";
                                                $stmt = $connect->prepare($query);
                                                $stmt->bind_param("i", $studentID);
                                                $stmt->execute();
                                                $resumeResult = $stmt->get_result();
                                                $resumeRow = $resumeResult->fetch_assoc();

                                                $resumeAvailable = $resumeRow && !is_null($resumeRow['file_name']);
                                                ?>
                                                <div class="col-md-4">
                                                    <?php if ($resumeAvailable) : ?>
                                                        <button class="btn btn-primary btn-sm ml-2" onclick="viewPDF('<?php echo $resumeRow['file_name']; ?>')"><i class="far fa-eye"></i></button>
                                                    <?php else : ?>
                                                        <button class="btn btn-sm btn-primary export-btn ml-2" data-studentid="<?php echo $rows['studentID']; ?>"><i class="fas fa-file-export"></i></button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php
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
    </div>

    <!-- End of Content Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
<script src="../assets/js/sidebarscript.js"></script>
<script>
    $(document).ready(function() {
        // Function to filter table based on search input
        $('#searchInput').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('#studentTable tbody tr').filter(function() {
                var rowText = $(this).text().toLowerCase();
                var isVisible = rowText.indexOf(value) > -1;
                $(this).toggle(isVisible);
            });

            // Check if any rows are visible after filtering
            var visibleRows = $('#studentTable tbody tr:visible').length;
            if (visibleRows === 0) {
                $('#noResult').show(); // Display "No Results" message
            } else {
                $('#noResult').hide(); // Hide "No Results" message if there are visible rows
            }
        });
    })

    function viewPDF(pdfPath) {
        // Open the PDF in a new tab/window
        window.open(pdfPath, '_blank');
    }
    $(document).ready(function() {
        $(".export-btn").click(function() {
            var studentID = $(this).data("studentid");
            window.location.href = "export-resume.php?studentID=" + studentID;
        });
    });
</script>