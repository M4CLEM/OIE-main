<?php
session_start();
include("../includes/connection.php");

$department = $_SESSION['department'];
$activeSemester = $_SESSION['semester'];
$activeSchoolYear = $_SESSION['schoolYear'];

// Assuming these are passed from dropdown filters or preset:
$course = $_POST['course'] ?? 'All'; // or get from GET if using links
$sectionFilter = $_POST['section'] ?? 'All';

$query = "SELECT * FROM studentinfo WHERE department = ? AND semester = ? AND school_year = ?";
$params = [$department, $activeSemester, $activeSchoolYear];
$types = "sss";

// Add course filter if specified
if ($course !== 'All') {
    $query .= " AND course = ?";
    $params[] = $course;
    $types .= "s";
}

// Add section filter if specified
if ($sectionFilter !== 'All') {
    $query .= " AND section = ?";
    $params[] = $sectionFilter;
    $types .= "s";
}

$query .= " ORDER BY course ASC, section ASC, lastName ASC";

// Prepare and execute
$stmt = $connect->prepare($query);
$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // display table rows
    } else {
        echo "No results found.";
    }
} else {
    echo "SQL Error: " . $stmt->error;
}
?>

<!DOCTYPE html>
<html>

<head>
    <?php include("../elements/meta.php"); ?>
    <title>OJT COOORDINATOR PORTAL</title>
    <?php include("embed.php"); ?>
    <link rel="stylesheet" href="../assets/css/new-style.css">
</head>

<body id="page-top">
    <div class="wrapper">
        <aside id="sidebar" class="expand">
            <?php include('../elements/cood_sidebar.php') ?>
        </aside>
        <div class="main">
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 static-top shadow">
                <!-- Title -->
                <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Generate Grades</h4>
                <!-- Topbar Navbar -->
                <ul class="navbar-nav ml-auto">
                    <div class="topbar-divider d-none d-sm-block"></div>
                    <!-- Nav Item - User Information -->
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                <?php (isset($_SESSION['coordinator'])) ?> <?php echo $_SESSION['coordinator']; ?></span>
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
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <div class="row justify-content-between align-items-center">
                            <div class="col-md-3">
                                <h6 class="m-0 font-weight-bold text-dark">STUDENT INFORMATION</h6>
                            </div>
                            <div class="col-md-6">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <div class="input-group input-group-sm mb-2">
                                            <?php
                                            // Get courses from studentinfo
                                            $courseQuery = "SELECT DISTINCT course FROM studentinfo WHERE department = ? AND semester = ? AND school_year = ?";
                                            $courseStmt = $connect->prepare($courseQuery);
                                            $courseStmt->bind_param("sss", $department, $activeSemester, $activeSchoolYear);
                                            $courseStmt->execute();
                                            $courseResult = $courseStmt->get_result();

                                            // Course Dropdown
                                            echo '<label class="input-group-text" for="courses">Course</label>';
                                            echo '<select name="courses" id="courses" class="form-select">';
                                            echo '<option value="All">All Courses</option>';
                                            while ($row = $courseResult->fetch_assoc()) {
                                                echo '<option value="' . htmlspecialchars($row['course']) . '">' . htmlspecialchars($row['course']) . '</option>';
                                            }
                                            echo '</select>';

                                            // Section Dropdown (initially empty)
                                            echo '<label class="input-group-text ms-2" for="sections">Section</label>';
                                            echo '<select name="sections" id="sections" class="form-select">';
                                            echo '<option value="All">All Sections</option>';
                                            echo '</select>';

                                            // Export Form
                                            echo '
                    <form method="POST" action="export_pdf.php" target="_blank" style="display:inline-block; margin-left: 10px;">
                        <input type="hidden" name="selected_course" id="selected_course_input">
                        <input type="hidden" name="selected_section" id="selected_section_input">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-file-pdf-o"></i> Export PDF</button>
                    </form>';
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="studentTable" class="table table-hover" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th colspan="8" class="text-center font-weight-bold border-0 pt-1"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col" class="small">StudentID</th>
                                            <th scope="col" class="small">Name</th>
                                            <th scope="col" class="small">Department</th>
                                            <th scope="col" class="small">Course-Section</th>
                                            <th scope="col" class="small">Status</th>
                                            <th scope="col" class="small" width="10%">Adviser Grade</th>
                                            <th scope="col" class="small" width="12%">Company Grade</th>
                                            <th scope="col" class="small" width="10%">Final Grade</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                $semester = $row['semester'];
                                                $schoolYear = $row['school_year'];
                                                $adviserGrade = 0;
                                                $companyGrade = 0;

                                                echo "<tr data-course=\"" . htmlspecialchars($row['course']) . "\" data-section=\"" . htmlspecialchars($row['section']) . "\">";

                                                echo "<td>" . $row['studentID'] . "</td>";
                                                echo "<td>" . $row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['lastname'] . "</td>";
                                                echo "<td>" . $row['department'] . "</td>";
                                                echo "<td>" . $row['course'] . '-' . $row['section'] . "</td>";
                                                echo "<td>" . $row['status'] . "</td>";

                                                // Adviser Grade
                                                $adviserGradeStmt = $connect->prepare("SELECT * FROM adviser_student_grade WHERE email = ? AND semester = ? AND schoolYear = ?");
                                                $adviserGradeStmt->bind_param("sss", $row['email'], $activeSemester, $activeSchoolYear);
                                                $adviserGradeStmt->execute();
                                                $adviserGradeResult = $adviserGradeStmt->get_result();

                                                if ($adviserGradeResult->num_rows > 0) {
                                                    while ($rowAdviserGrade = $adviserGradeResult->fetch_assoc()) {
                                                        $adviserGrade = $rowAdviserGrade['finalGrade'];
                                                    }
                                                }
                                                echo "<td align=\"center\">" . $adviserGrade . "</td>";

                                                // Company Grade
                                                $companyGradeStmt = $connect->prepare("SELECT * FROM student_grade WHERE email = ? AND semester = ? AND schoolYear = ?");
                                                $companyGradeStmt->bind_param("sss", $row['email'], $activeSemester, $activeSchoolYear);
                                                $companyGradeStmt->execute();
                                                $companyGradeResult = $companyGradeStmt->get_result();

                                                if ($companyGradeResult->num_rows > 0) {
                                                    while ($rowCompanyGrade = $companyGradeResult->fetch_assoc()) {
                                                        $companyGrade = $rowCompanyGrade['finalGrade'];
                                                    }
                                                }
                                                echo "<td align=\"center\">" . $companyGrade . "</td>";

                                                // Final Grade Calculation (using grading_rubics table)
                                                $rubricsQuery = "SELECT * FROM grading_rubics WHERE department = ? AND semester = ? AND schoolYear = ?";
                                                $stmt = $connect->prepare($rubricsQuery);
                                                $stmt->bind_param("sss", $row['department'], $semester, $schoolYear);
                                                $stmt->execute();
                                                $rubricResult = $stmt->get_result();
                                                $grading = $rubricResult->fetch_assoc();

                                                if ($grading) {
                                                    $adviserWeight = $grading['adviserWeight'];
                                                    $companyWeight = $grading['companyWeight'];
                                                } else {
                                                    $adviserWeight = 0;
                                                    $companyWeight = 0;
                                                }

                                                $finalGrade = ($adviserGrade * ($adviserWeight / 100)) + ($companyGrade * ($companyWeight / 100));
                                                echo "<td align=\"center\">" . number_format($finalGrade, 2) . "</td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='8' class='text-center'>No student records found.</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
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

            </div>
        </div>


        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const courseSelect = document.getElementById('courses');
                const sectionSelect = document.getElementById('sections');
                const selectedCourseInput = document.getElementById('selected_course_input');
                const selectedSectionInput = document.getElementById('selected_section_input');
                const exportForm = document.querySelector('form[action="export_pdf.php"]');
                const exportButton = exportForm?.querySelector('button[type="submit"]');

                const table = $('#studentTable').DataTable({
                    paging: false, // turn off pagination
                    lengthChange: false, // hide “Show X entries”
                    info: false, // hide “Showing X of Y entries”
                    searching: true, // keep the search box
                    ordering: true, // keep column sorting
                    language: {
                        search: "Search Student:" // customize the search label
                    }
                });

                // Ensure export inputs reflect current dropdowns
                function updateExportInputs() {
                    selectedCourseInput.value = courseSelect.value;
                    selectedSectionInput.value = sectionSelect.value;
                }

                // DataTables filtering based on selected dropdowns
                function customFilterFunction(settings, data, dataIndex) {
                    const row = table.row(dataIndex).node();
                    const courseValue = courseSelect.value;
                    const sectionValue = sectionSelect.value;

                    const rowCourse = row.getAttribute('data-course');
                    const rowSection = row.getAttribute('data-section');

                    const courseMatch = (courseValue === 'All' || courseValue === rowCourse);
                    const sectionMatch = (sectionValue === 'All' || sectionValue === rowSection);

                    return courseMatch && sectionMatch;
                }

                $.fn.dataTable.ext.search.push(customFilterFunction);

                // Update section options when course changes
                courseSelect.addEventListener('change', function() {
                    const selectedCourse = this.value;
                    updateExportInputs();
                    table.draw();

                    fetch('stud_get_sections.php?course=' + encodeURIComponent(selectedCourse))
                        .then(response => response.json())
                        .then(sections => {
                            sectionSelect.innerHTML = '';
                            const defaultOption = document.createElement('option');
                            defaultOption.value = 'All';
                            defaultOption.textContent = 'All Sections';
                            sectionSelect.appendChild(defaultOption);

                            sections.forEach(section => {
                                const option = document.createElement('option');
                                option.value = section;
                                option.textContent = section;
                                sectionSelect.appendChild(option);
                            });

                            sectionSelect.value = 'All';
                            updateExportInputs();
                            table.draw();
                        })
                        .catch(error => console.error('Error fetching sections:', error));
                });

                // Redraw table and update inputs on section change
                sectionSelect.addEventListener('change', function() {
                    updateExportInputs();
                    table.draw();
                });

                // 🛠 Force input update right before exporting
                if (exportButton) {
                    exportButton.addEventListener('click', function() {
                        updateExportInputs();
                        // Optional: you could add a loading spinner or disable the button here
                    });
                }

                // Initial sync
                updateExportInputs();
            });
        </script>
</body>
</html>