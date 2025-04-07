<?php
    session_start();
    include("../includes/connection.php");

    $email = $_SESSION['adviser'];
    $activeSemester = $_SESSION['semester'];
    $activeSchoolYear = $_SESSION['schoolYear'];
    
    // Check if dept_sec is set and is an array
    if (isset($_SESSION['dept_sec']) && is_array($_SESSION['dept_sec']) && count($_SESSION['dept_sec']) > 0) {
        // Create placeholders dynamically for the number of sections
        $placeholders = implode(',', array_fill(0, count($_SESSION['dept_sec']), '?'));
        $query = "SELECT * FROM studentinfo WHERE department= ? AND course= ? AND semester = ? AND school_year = ? AND section IN ($placeholders) ORDER BY section ASC, lastName ASC";

        // Prepare the statement
        $stmt = $connect->prepare($query);

        // Merge department, course, and section values
        $params = array_merge([$_SESSION['dept_adv'], $_SESSION['dept_crs'], $activeSemester, $activeSchoolYear], $_SESSION['dept_sec']);

        // Define parameter types
        $types = str_repeat('s', count($params));

        // Bind the parameters dynamically
        $stmt->bind_param($types, ...$params);

        // Execute the statement
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                // Fetch all rows
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
        <title>Adviser Portal</title>
        <?php include("embed.php"); ?>
    </head>
    <body id="page-top">
        <div class="wrapper">
            <aside id="sidebar" class="expand">
                <?php include('../elements/adv_sidebar.php') ?>
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
                                <?php (isset($_SESSION['adviser'])) ?> <?php echo $_SESSION['adviser']; ?></span>
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
                                <div class="col-md-3">
                                    <div class="row">
                                        <div class="col">
                                            <?php
                                                // Assuming $connect is your mysqli connection object
                                                $getsections = "SELECT section FROM listadviser WHERE email = '$email'";
                                                $sections = mysqli_query($connect, $getsections);

                                                // Check if query was successful
                                                if ($sections) {
                                                    echo '<div class="input-group input-group-sm">';
        
                                                    // Dropdown
                                                    echo '<select name="sections" id="sections" class="form-control form-control-sm">';
                                                    echo '<option value="All Sections">All Sections</option>';
                                                    while ($sect = mysqli_fetch_assoc($sections)) {
                                                        echo '<option value="' . $sect['section'] . '">' . $sect['section'] . '</option>';
                                                    }
                                                    echo '</select>';

                                                    // Export button
                                                    echo '<button class="export-btn btn btn-primary" type="button">';
                                                    echo 'Export <i class="far fa-file-pdf"></i>';
                                                    echo '</button>';

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
                                            <th scope="col" class="small">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            if ($result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    $semester = $row['semester'];
                                                    $schoolYear = $row['school_year'];
                                        ?>
                                        <?php            
                                                    echo "<tr>";
                                                        echo "<td>" . $row['studentID'] . "</td>";
                                                        echo "<td>" . $row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['lastname'] . "</td>";
                                                        echo "<td>" . $row['department'] . "</td>";
                                                        echo "<td>" . $row['course'] . '-' . $row['section'] . "</td>";
                                                        echo "<td>" . $row['status'] . "</td>";

                                                        $adviserCriteriaGrouped = [];
                                                        $companyCriteriaGrouped = [];

                                                        $adviserGradeStmt = $connect->prepare("SELECT * FROM adviser_student_grade WHERE email = ? AND semester = ? AND schoolYear = ?");
                                                        $adviserGradeStmt->bind_param("sss", $row['email'], $activeSemester, $activeSchoolYear);
                                                        $adviserGradeStmt->execute();
                                                        $adviserGradeResult = $adviserGradeStmt->get_result();

                                                        while ($rowAdviserGrade = $adviserGradeResult->fetch_assoc()) {
                                                            $adviserGrade = $rowAdviserGrade['finalGrade'];
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

                                                        echo "<td align=\"center\">" . $adviserGrade . "</td>";

                                                        $companyGradeStmt = $connect->prepare("SELECT * FROM student_grade WHERE email = ? AND semester = ? AND schoolYear = ?");
                                                        $companyGradeStmt->bind_param("sss", $row['email'], $activeSemester, $activeSchoolYear);
                                                        $companyGradeStmt->execute();
                                                        $companyGradeResult = $companyGradeStmt->get_result();

                                                        while ($rowCompanyGrade = $companyGradeResult->fetch_assoc()) {
                                                            $companyGrade = $rowCompanyGrade['finalGrade'];
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

                                                        echo "<td align=\"center\">" . $companyGrade . "</td>";

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
                                                            // Handle the case where no grading information is available
                                                            $adviserWeight = 0;  // Set default values or handle the error
                                                            $companyWeight = 0;  // Set default values or handle the error
                                                            $finalizedGrade = 0; // Set default grade or handle the error
                                                        }

                                                        $finalizedGrade = ($adviserGrade * ($adviserWeight / 100)) + ($companyGrade * ($companyWeight / 100));

                                                        echo "<td align=\"center\">" . $finalizedGrade . "</td>";
                                                        echo    "<td>
                                                                    <a href=\"#\" class=\"btn btn-primary btn-sm editBtn\" 
                                                                    data-toggle=\"modal\" data-target=\"#editModal\">
                                                                        <i class=\"fa fa-edit fw-fa\"></i> Edit
                                                                    </a>
                                                                </td>";
                                                    echo "</tr>";
                                                }
                                            }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModal" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form action="" method="POST">
                                <div class="modal-header">
                                    <h5>Edit Grades</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">

                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-primary btn-sm" type="submit" id="submitBtn">
                                        <span class="fa fa-save fw-fa"></span> Submit
                                    </button>
                                    <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>