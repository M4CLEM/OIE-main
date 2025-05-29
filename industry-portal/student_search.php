<?php
include_once("../includes/connection.php");
session_start();
$activeSemester = $_SESSION['semester'];
$activeSchoolYear = $_SESSION['schoolYear'];
$queryDept = "SELECT * FROM studentinfo WHERE status = 'Undeployed' AND semester = '$activeSemester' AND school_year = '$activeSchoolYear'";
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
                <?php include('../elements/ip_navbar_user_info.php') ?>
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
                                <tbody style="max-height: 80vh; overflow-y: auto;">
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
                                                $query = "SELECT * FROM documents WHERE document = 'Resume' AND student_ID = ? AND semester = ? AND schoolYear = ?";
                                                $stmt = $connect->prepare($query);
                                                $stmt->bind_param("iss", $studentID, $activeSemester, $activeSchoolYear);
                                                $stmt->execute();
                                                $resumeResult = $stmt->get_result();
                                                $resumeRow = $resumeResult->fetch_assoc();

                                                $resumeAvailable = $resumeRow && !is_null($resumeRow['file_name']);
                                                ?>
                                                <div class="col-md-4">
                                                    <?php if ($resumeAvailable) : ?>
                                                        <button class="btn btn-primary btn-sm ml-2" onclick="viewPDF('<?php echo $resumeRow['file_link']; ?>')"><i class="far fa-eye"></i></button>
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