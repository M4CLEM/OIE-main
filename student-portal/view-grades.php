<?php
session_start();
include_once("../includes/connection.php"); 

$email = $_SESSION['student'];
$department = $_SESSION['department'];

$studNumberResult = mysqli_query($connect, "SELECT studentID FROM studentinfo WHERE email = '$email'");

if ($studNumberResult && mysqli_num_rows($studNumberResult) > 0) {
    $row = mysqli_fetch_assoc($studNumberResult);
    $studentID = $row['studentID'];
} 

// Fetch unique semesters and school years for tabs
$enrollmentsQuery = "SELECT DISTINCT semester, schoolYear FROM student_masterlist WHERE studentID = ? ORDER BY schoolYear DESC, semester DESC";
$stmt = $connect->prepare($enrollmentsQuery);
$stmt->bind_param("s", $studentID);
$stmt->execute();
$enrollmentsResult = $stmt->get_result();
$enrollments = $enrollmentsResult->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include("../elements/meta.php"); ?>
        <title>Student Portal</title>
        <?php include("embed.php"); ?>
        <link rel="stylesheet" href="../assets/css/new-style.css">
    </head>
    <body id="page-top">
        <div class="wrapper">
            <!--Sidebar Wrapper-->
            <aside id="sidebar" class="expand">
                <?php include('../elements/stud_sidebar.php')?>
            </aside>
            <div class="main">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 static-top shadow">
                    <!-- Title -->
                    <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Grades</h4>
                    <!-- Topbar Navbar -->
                    <?php include('includes/navbar_user_info.php'); ?>
                </nav>

                <div class="card shadow mb-4">
    <div class="card-body">
        <ul class="nav nav-tabs" id="gradesTabs" role="tablist">
            <?php foreach ($enrollments as $index => $enrollment): ?>
                <li class="nav-item" role="presentation">
                    <a class="nav-link <?php echo $index === 0 ? 'active' : ''; ?>" id="tab-<?php echo $index; ?>" data-toggle="tab" href="#semester-<?php echo $index; ?>" role="tab">
                        <?php echo htmlspecialchars($enrollment['semester']) . ' ' . htmlspecialchars($enrollment['schoolYear']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="tab-content mt-3" id="gradesTabsContent">
            <?php foreach ($enrollments as $index => $enrollment): ?>
                <div class="tab-pane fade <?php echo $index === 0 ? 'show active' : ''; ?>" id="semester-<?php echo $index; ?>" role="tabpanel">
                    <?php
                    $semester = $enrollment['semester'];
                    $schoolYear = $enrollment['schoolYear'];
                    
                    // Fetch company name, job role, and grades
                    $stmt = $connect->prepare("SELECT companyName, jobRole FROM adviser_student_grade WHERE email = ? AND semester = ? AND schoolYear = ? LIMIT 1");
                    $stmt->bind_param("sss", $email, $semester, $schoolYear);
                    $stmt->execute();
                    $companyResult = $stmt->get_result();
                    $companyInfo = $companyResult->fetch_assoc();

                    if ($companyInfo) {
                        $companyName = htmlspecialchars($companyInfo['companyName']);
                        $jobRole = htmlspecialchars($companyInfo['jobRole']);
                    } else {
                        $companyName = null;
                        $jobRole = null;
                    }
                    
                    $stmt = $connect->prepare("SELECT * FROM adviser_student_grade WHERE email = ? AND semester = ? AND schoolYear = ?");
                    $stmt->bind_param("sss", $email, $semester, $schoolYear);
                    $stmt->execute();
                    $adviserResult = $stmt->get_result();
                    
                    $stmt = $connect->prepare("SELECT * FROM student_grade WHERE email = ? AND semester = ? AND schoolYear = ?");
                    $stmt->bind_param("sss", $email, $semester, $schoolYear);
                    $stmt->execute();
                    $companyResult = $stmt->get_result();
                    
                    $adviserCriteriaGrouped = [];
                    $companyCriteriaGrouped = [];
                    $totalGradeAdviser = null;
                    $totalGradeCompany = null;
                    
                    while ($row = $adviserResult->fetch_assoc()) {
                        $criteria = json_decode($row['criteria'], true);
                        $grade = json_decode($row['grade'], true);
                        $totalGradeAdviser = $row['finalGrade'];

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
                    
                    while ($row = $companyResult->fetch_assoc()) {
                        $criteria = json_decode($row['criteria'], true);
                        $grade = json_decode($row['grade'], true);
                        $totalGradeCompany = $row['finalGrade'];
                        
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

                    $finalizedGradeQuery = "SELECT * FROM grading_rubics WHERE department = ? AND semester = ? AND schoolYear = ?";
                    $stmt = $connect->prepare($finalizedGradeQuery);
                    $stmt->bind_param("sss", $department, $semester, $schoolYear);
                    $stmt->execute();
                    $gradingResult = $stmt->get_result();
                    $gradingInfo = $gradingResult->fetch_assoc();

                    // Assuming 'adviserWeight' and 'companyWeight' are columns in the grading_rubics table
                    if ($gradingInfo) {
                        $adviserWeight = $gradingInfo['adviserWeight']; // this is line 180
                        $companyWeight = $gradingInfo['companyWeight']; // this is line 181
                        $finalizedGrade = ($totalGradeAdviser * ($adviserWeight / 100)) + ($totalGradeCompany * ($companyWeight / 100));
                    } else {
                        // Handle the case where no grading information is available
                        $adviserWeight = 0;  // Set default values or handle the error
                        $companyWeight = 0;  // Set default values or handle the error
                        $finalizedGrade = 0; // Set default grade or handle the error
                    }
                    ?>
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h3 class="m-0 font-weight-bold text-dark">Grades Overview</h3>
                        <h4 class="m-0 font-weight-bold text-dark">Final Grade: <?php echo $finalizedGrade;?>%</h4>
                        <div>
                            <?php if ($companyName && $jobRole): ?>
                                <h5 class="m-0 font-weight-bold text-dark">Company: <?php echo $companyName; ?></h5>
                                <h6 class="m-0 text-muted">Role: <?php echo $jobRole; ?></h6>
                            <?php else: ?>
                                <p class="m-0 text-muted">No company data available.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="row mt-3">
    <!-- ADVISER'S CRITERIA -->
    <div class="col-md-6">
        <div class="card mb-2">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-dark">ADVISER'S CRITERIA</h6>
            </div>
            <div class="card-body">
                <?php if (!empty($adviserCriteriaGrouped)): ?>
                    <?php foreach ($adviserCriteriaGrouped as $criteriaItem): ?>
                        <div>
                            <strong><?php echo $criteriaItem['adviserCriteria']; ?></strong>
                            <p><strong>Description:</strong> <?php echo $criteriaItem['adviserDescription']; ?></p>
                            <span><?php echo $criteriaItem['score']; ?> / <?php echo $criteriaItem['adviserPercentage']; ?>%</span>
                        </div>
                        <hr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted">No criteria available.</p>
                <?php endif; ?>
            </div>
            <div class="card-footer">
                <h5 class="m-0 font-weight-bold text-dark">Total Grade: <?php echo !empty($adviserCriteriaGrouped) ? $totalGradeAdviser : '0'; ?>%</h5>
            </div>
        </div>
    </div>

    <!-- COMPANY'S CRITERIA -->
    <div class="col-md-6">
        <div class="card mb-2">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-dark">COMPANY'S CRITERIA</h6>
            </div>
            <div class="card-body">
                <?php if (!empty($companyCriteriaGrouped)): ?>
                    <?php foreach ($companyCriteriaGrouped as $criteriaItem): ?>
                        <div>
                            <strong><?php echo $criteriaItem['companyCriteria']; ?></strong>
                            <p><strong>Description:</strong> <?php echo $criteriaItem['companyDescription']; ?></p>
                            <span><?php echo $criteriaItem['score']; ?> / <?php echo $criteriaItem['companyPercentage']; ?>%</span>
                        </div>
                        <hr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted">No criteria available.</p>
                <?php endif; ?>
            </div>
            <div class="card-footer">
                <h5 class="m-0 font-weight-bold text-dark">Total Grade: <?php echo !empty($companyCriteriaGrouped) ? $totalGradeCompany : '0'; ?>%</h5>
            </div>
        </div>
    </div>
</div>

                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

            </div>
        </div>
    </body>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>
    <script src="../assets/js/sidebarscript.js"></script>
    
</html>