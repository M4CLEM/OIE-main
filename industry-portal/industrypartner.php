<?php
include_once("../includes/connection.php");
session_start();

$activeSemester = $_SESSION['semester'];
$activeSchoolYear = $_SESSION['schoolYear'];
$companyName = $_SESSION['companyName'];

// Get all studentIDs associated with the company
$studNumQuery = "SELECT studentID FROM company_info WHERE companyName = ? AND semester = ? AND schoolYear = ?";
$studNumStmt = $connect->prepare($studNumQuery);
$studNumStmt->bind_param("sss", $companyName, $activeSemester, $activeSchoolYear);
$studNumStmt->execute();
$resultStudNum = $studNumStmt->get_result();

$studentIDs = array(); // Array to hold student IDs
while ($row = $resultStudNum->fetch_assoc()) {
    $studentIDs[] = $row['studentID'];
}
$studNumStmt->close();

$studentsInfo = [];

if (!empty($studentIDs)) {
    // Dynamically create placeholders
    $placeholders = implode(',', array_fill(0, count($studentIDs), '?'));
    $queryDept = "SELECT * FROM studentinfo WHERE studentID IN ($placeholders) AND status = ? AND semester = ? AND school_year = ?";
    
    $stmtDept = $connect->prepare($queryDept);
    
    // Bind parameters dynamically
    $types = str_repeat('s', count($studentIDs) + 3); // one 's' for each studentID + 3 for status, semester, school_year
    $params = array_merge($studentIDs, ['Deployed', $activeSemester, $activeSchoolYear]);
    
    $stmtDept->bind_param($types, ...$params);
    
    $stmtDept->execute();
    $result = $stmtDept->get_result();

    while ($row = $result->fetch_assoc()) {
        $studentsInfo[] = $row;
    }

    $stmtDept->close();
} else {
    // No student IDs found
    $result = null;
}
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
                        <h6 class="m-0 font-weight-bold text-dark">List of Deployed Interns</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="studentTable" width="100%" cellspacing="1">
                                <thead>
                                    <tr>
                                        <th colspan="5" class="text-center font-weight-bold border-0 pt-1"><input type="text" class="form-control form-control-sm" id="searchInput" placeholder="Search..."></th>
                                    </tr>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Course</th>
                                        <th scope="col">Section</th>
                                        <th scope="col">Grade</th>
                                    </tr>
                                </thead>
                                <tbody style="max-height: 80vh; overflow-y: auto;">
                                    <?php
                                        if (!empty($studentsInfo)) {
                                            foreach ($studentsInfo as $rows) {
                                                $studentID = $connect->real_escape_string($rows['studentID']);
                                                $activeSemesterEscaped = $connect->real_escape_string($activeSemester);
                                                $activeSchoolYearEscaped = $connect->real_escape_string($activeSchoolYear);

                                                $gradeQuery = "SELECT finalGrade AS totalGrade FROM student_grade WHERE studentID = '$studentID' AND semester = '$activeSemesterEscaped' AND schoolYear = '$activeSchoolYearEscaped'";
                                                $gradeResult = mysqli_query($connect, $gradeQuery);

                                                if (!$gradeResult) {
                                                    echo "<tr><td colspan='5'>Grade query failed: " . $connect->error . "</td></tr>";
                                                    break;
                                                }

                                                $gradeRow = mysqli_fetch_assoc($gradeResult);
                                                $totalGrade = ($gradeRow && $gradeRow['totalGrade'] !== null) ? $gradeRow['totalGrade'] : "No grades recorded";
                                                ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($rows['studentID']); ?></td>
                                                        <td><?php echo htmlspecialchars($rows['firstname'] . ' ' . $rows['lastname']); ?></td>
                                                        <td><?php echo htmlspecialchars($rows['course']); ?></td>
                                                        <td><?php echo htmlspecialchars($rows['section']); ?></td>
                                                        <td><?php echo htmlspecialchars($totalGrade); ?></td>
                                                    </tr>
                                                <?php
                                            }
                                        } else {
                                            echo '<tr><td colspan="5">No student records found for the selected filters.</td></tr>';
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
</script>