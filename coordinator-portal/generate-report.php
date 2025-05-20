<?php
session_start();
include("../includes/connection.php");

$department = $_SESSION['department'];
$activeSemester = $_SESSION['semester'];
$activeSchoolYear = $_SESSION['schoolYear'];

// Check if dept_sec is set and is an array
if (isset($_SESSION['dept_sec']) && is_array($_SESSION['dept_sec']) && count($_SESSION['dept_sec']) > 0) {
    // Create an array to hold exploded section values
    $explodedSections = [];

    // Exploding the sections into individual values and adding to the array
    foreach ($_SESSION['dept_sec'] as $section) {
        $sectionsArray = explode(',', $section); // Explode the section string into individual sections
        foreach ($sectionsArray as $individualSection) {
            $explodedSections[] = trim($individualSection); // Add each exploded section to the array
        }
    }

    // Create placeholders dynamically for the number of exploded sections
    $placeholders = implode(',', array_fill(0, count($explodedSections), '?'));

    $query = "SELECT * FROM studentinfo WHERE department= ? AND course= ? AND semester = ? AND school_year = ? AND section IN ($placeholders) ORDER BY section ASC, lastName ASC";

    // Prepare the statement
    $stmt = $connect->prepare($query);

    // Merge department, course, and section values
    $params = array_merge([$_SESSION['dept_adv'], $_SESSION['dept_crs'], $activeSemester, $activeSchoolYear], $explodedSections);

    // Define parameter types
    $types = str_repeat('s', count($params));

    // Bind the parameters dynamically
    $stmt->bind_param($types, ...$params);

    // Execute the statement
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            // Continue processing rows
        } else {
            echo "No results found for the given criteria.";
        }
    } else {
        echo "SQL Error: " . $stmt->error;
    }
} else {
    echo "No sections found for the adviser.";
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
                                            $getCourses = "SELECT course FROM listadviser WHERE dept = '$department' AND semester = '$activeSemester' AND schoolYear = '$activeSchoolYear'";
                                            $courses = mysqli_query($connect, $getCourses);

                                            // Check if query was successful
                                            if ($courses) {
                                                echo '<label class="input-group-text" for="courses">Course</label>';
                                                echo '<select name="courses" id="courses" class="form-select">';
                                                echo '<option value="All Courses">All Courses</option>';
                                                while ($row = mysqli_fetch_assoc($courses)) {
                                                    echo '<option value="' . $row['course'] . '">' . $row['course'] . '</option>';
                                                }
                                                echo '</select>';
                                            } else {
                                                echo "Error: " . mysqli_error($connect);
                                            }

                                            // Assuming $connect is your mysqli connection object
                                            $getsections = "SELECT section FROM listadviser WHERE dept = '$department' AND semester = '$activeSemester' AND schoolYear = '$activeSchoolYear'";
                                            $sections = mysqli_query($connect, $getsections);

                                            // Check if query was successful
                                            if ($sections) {
                                                echo '<label class="input-group-text ms-2" for="sections">Section</label>';
                                                echo '<select name="sections" id="sections" class="form-select">';
                                                echo '<option value="All Sections">All Sections</option>';
                                                $sectionSet = [];

                                                // Collect and split sections
                                                while ($sect = mysqli_fetch_assoc($sections)) {
                                                    $individualSections = array_map('trim', explode(',', $sect['section']));
                                                    foreach ($individualSections as $s) {
                                                        if (!in_array($s, $sectionSet)) {
                                                            $sectionSet[] = $s;
                                                        }
                                                    }
                                                }

                                                // Sort the sections before displaying
                                                sort($sectionSet);
                                                foreach ($sectionSet as $s) {
                                                    echo '<option value="' . $s . '">' . $s . '</option>';
                                                }

                                                echo '</select>
                                                        <form method="POST" action="export_pdf.php" target="_blank" style="display:inline-block; margin-left: 10px;">
                                                            <input type="hidden" name="selected_course" id="selected_course_input">
                                                            <input type="hidden" name="selected_section" id="selected_section_input">
                                                            <button type="submit" class="btn btn-primary"><i class="fa fa-file-pdf-o"></i> Export PDF</button>
                                                        </form>';

                                                echo '</div>'; // Close input-group
                                            } else {
                                                echo "Error: " . mysqli_error($connect);
                                            }
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
                                            <th colspan="8" class="text-center font-weight-bold border-0 pt-1"><input type="text" class="form-control form-control-sm" id="searchInput" placeholder="Search..."></th>
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

                                                // Initialize grades to 0 to prevent undefined variable warnings
                                                $adviserGrade = 0;
                                                $companyGrade = 0;

                                                // Your original echo statement
                                                echo "<tr>";
                                                echo "<td>" . $row['studentID'] . "</td>";
                                                echo "<td>" . $row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['lastname'] . "</td>";
                                                echo "<td>" . $row['department'] . "</td>";
                                                echo "<td>" . $row['course'] . '-' . $row['section'] . "</td>";
                                                echo "<td>" . $row['status'] . "</td>";

                                                // Initialize the grouped criteria arrays
                                                $adviserCriteriaGrouped = [];
                                                $companyCriteriaGrouped = [];

                                                // Adviser Grade Query
                                                $adviserGradeStmt = $connect->prepare("SELECT * FROM adviser_student_grade WHERE email = ? AND semester = ? AND schoolYear = ?");
                                                $adviserGradeStmt->bind_param("sss", $row['email'], $activeSemester, $activeSchoolYear);
                                                $adviserGradeStmt->execute();
                                                $adviserGradeResult = $adviserGradeStmt->get_result();

                                                // If grades found, assign them
                                                if ($adviserGradeResult->num_rows > 0) {
                                                    while ($rowAdviserGrade = $adviserGradeResult->fetch_assoc()) {
                                                        $adviserGrade = $rowAdviserGrade['finalGrade']; // Assign grade if found
                                                        $criteria = json_decode($rowAdviserGrade['criteria'], true);
                                                        $grade = json_decode($rowAdviserGrade['grade'], true);

                                                        foreach ($criteria as $criterion) {
                                                            $name = $criterion['criteria'];
                                                            $percentage = $criterion['percentage'];
                                                            $score = isset($grade[$name]) ? $grade[$name] : 0;

                                                            $adviserCriteriaGrouped[] = [
                                                                'adviserCriteria' => $name,
                                                                'adviserPercentage' => $percentage,
                                                                'adviserDescription' => $criterion['description'],
                                                                'score' => $score
                                                            ];
                                                        }
                                                    }
                                                }

                                                echo "<td align=\"center\">" . $adviserGrade . "</td>"; // Display adviser grade

                                                // Company Grade Query
                                                $companyGradeStmt = $connect->prepare("SELECT * FROM student_grade WHERE email = ? AND semester = ? AND schoolYear = ?");
                                                $companyGradeStmt->bind_param("sss", $row['email'], $activeSemester, $activeSchoolYear);
                                                $companyGradeStmt->execute();
                                                $companyGradeResult = $companyGradeStmt->get_result();

                                                // If grades found, assign them
                                                if ($companyGradeResult->num_rows > 0) {
                                                    while ($rowCompanyGrade = $companyGradeResult->fetch_assoc()) {
                                                        $companyGrade = $rowCompanyGrade['finalGrade']; // Assign grade if found
                                                        $criteria = json_decode($rowCompanyGrade['criteria'], true);
                                                        $grade = json_decode($rowCompanyGrade['grade'], true);

                                                        foreach ($criteria as $criterion) {
                                                            $name = $criterion['criteria'];
                                                            $percentage = $criterion['percentage'];
                                                            $score = isset($grade[$name]) ? $grade[$name] : 0;

                                                            $companyCriteriaGrouped[] = [
                                                                'companyCriteria' => $name,
                                                                'companyPercentage' => $percentage,
                                                                'companyDescription' => $criterion['description'],
                                                                'score' => $score
                                                            ];
                                                        }
                                                    }
                                                }

                                                echo "<td align=\"center\">" . $companyGrade . "</td>"; // Display company grade

                                                // Finalized grade calculation
                                                $finalizedGradeQuery = "SELECT * FROM grading_rubics WHERE department = ? AND semester = ? AND schoolYear = ?";
                                                $stmt = $connect->prepare($finalizedGradeQuery);
                                                $stmt->bind_param("sss", $row['department'], $semester, $schoolYear);
                                                $stmt->execute();
                                                $gradingResult = $stmt->get_result();
                                                $gradingInfo = $gradingResult->fetch_assoc();

                                                // Assuming 'adviserWeight' and 'companyWeight' are columns in the grading_rubics table
                                                if ($gradingInfo) {
                                                    $adviserWeight = $gradingInfo['adviserWeight'];
                                                    $companyWeight = $gradingInfo['companyWeight'];
                                                } else {
                                                    $adviserWeight = 0; // Default value
                                                    $companyWeight = 0; // Default value
                                                }

                                                $finalizedGrade = ($adviserGrade * ($adviserWeight / 100)) + ($companyGrade * ($companyWeight / 100));

                                                echo "<td align=\"center\">" . $finalizedGrade . "</td>";

                                                // Edit button and modal (no changes here)

                                                echo "<tr data-course=\"" . $row['course'] . "\" data-section=\"" . $row['section'] . "\">";

                                            }
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

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const table = $('#studentTable').DataTable();

                const courseFilter = document.getElementById('courses');
                const sectionFilter = document.getElementById('sections');

                function customFilterFunction(settings, data, dataIndex) {
                    const row = table.row(dataIndex).node();
                    const courseValue = courseFilter.value;
                    const sectionValue = sectionFilter.value;

                    const rowCourse = row.getAttribute('data-course');
                    const rowSection = row.getAttribute('data-section');

                    const courseMatch = (courseValue === 'All Courses' || courseValue === rowCourse);
                    const sectionMatch = (sectionValue === 'All Sections' || sectionValue === rowSection);

                    return courseMatch && sectionMatch;
                }

                // Register the filter
                $.fn.dataTable.ext.search.push(customFilterFunction);

                // Apply filtering when dropdowns change
                courseFilter.addEventListener('change', function() {
                    table.draw();
                });

                sectionFilter.addEventListener('change', function() {
                    table.draw();
                });
            });
        </script>

        <script>
            // Update hidden input when section changes
            document.getElementById('sections').addEventListener('change', function() {
                document.getElementById('selected_section_input').value = this.value;
            });

            // Update hidden input when course changes
            document.getElementById('courses').addEventListener('change', function() {
                document.getElementById('selected_course_input').value = this.value;
            });

            // Set default values on page load
            window.addEventListener('DOMContentLoaded', function() {
                var selectedSection = document.getElementById('sections').value;
                var selectedCourse = document.getElementById('courses').value;
                document.getElementById('selected_section_input').value = selectedSection;
                document.getElementById('selected_course_input').value = selectedCourse;
            });
        </script>

</body>

</html>