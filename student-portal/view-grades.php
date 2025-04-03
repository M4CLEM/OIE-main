<?php
session_start();
include_once("../includes/connection.php"); 

$email = $_SESSION['student'];
$semester = $_SESSION['semester'];
$schoolYear = $_SESSION['schoolYear'];

// Fetch adviser criteria and grades
$stmt = $connect->prepare("SELECT * FROM adviser_student_grade WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

$adviserCriteriaGrouped = [];
$totalGradeAdviser = null;

while ($row = $result->fetch_assoc()) {
    $criteria = json_decode($row['criteria'], true);
    $grade = json_decode($row['grade'], true);
    $totalGradeAdviser = $row['finalGrade'];

    if (!isset($adviserCriteriaGrouped[$row['id']])) {
        $adviserCriteriaGrouped[$row['id']] = [
            'adviserCriteria' => []
        ];
    }

    if ($criteria && $grade) {
        foreach ($criteria as $criterion) {
            $name = $criterion['criteria'];
            $percentage = $criterion['percentage'];
            $score = isset($grade[$name]) ? $grade[$name] : 0;
            
            $adviserCriteriaGrouped[$row['id']]['adviserCriteria'][] = [
                'adviserCriteria' => $name,
                'adviserPercentage' => $percentage,
                'adviserDescription' => $criterion['description'],
                'score' => $score
            ];
        }
    }
}

// Fetch company criteria and grades
$stmt = $connect->prepare("SELECT * FROM student_grade WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

$companyCriteriaGrouped = [];
$totalGradeCompany = null;

while ($row = $result->fetch_assoc()) {
    $criteria = json_decode($row['criteria'], true);
    $grade = json_decode($row['grade'], true);
    $totalGradeCompany = $row['finalGrade'];

    if (!isset($companyCriteriaGrouped[$row['id']])) {
        $companyCriteriaGrouped[$row['id']] = [
            'companyCriteria' => []
        ];
    }

    if ($criteria && $grade) {
        foreach ($criteria as $criterion) {
            $name = $criterion['criteria'];
            $percentage = $criterion['percentage'];
            $score = isset($grade[$name]) ? $grade[$name] : 0;
            
            $companyCriteriaGrouped[$row['id']]['companyCriteria'][] = [
                'companyCriteria' => $name,
                'companyPercentage' => $percentage,
                'companyDescription' => $criterion['description'],
                'score' => $score
            ];
        }
    }
}

// Pass data to frontend
$adviserCriteriaJSON = json_encode($adviserCriteriaGrouped);
$companyCriteriaJSON = json_encode($companyCriteriaGrouped);
?>



<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include("../elements/meta.php"); ?>
        <title>Student Portal</title>
        <?php include("embed.php"); ?>
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
                    <ul class="navbar-nav ml-auto">
                        <div class="topbar-divider d-none d-sm-block"></div>
                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">  
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                <?php (isset($_SESSION['student'])) ?> <?php echo $_SESSION['student']; ?></span>
                                <?php
                                    function get_drive_image_url($image) {
                                        // Check if the image is a Google Drive URL
                                        if (strpos($image, 'drive.google.com') !== false) {
                                            // Extract the File ID from different Drive URL formats
                                            preg_match('/(?:id=|\/d\/)([a-zA-Z0-9_-]{25,})/', $image, $matches);
                                            $image = $matches[1] ?? null; // Get the File ID if found
                                        }

                                        // If a valid Google Drive File ID is found, return the direct image link
                                        if ($image && preg_match('/^[a-zA-Z0-9_-]{25,}$/', $image)) {
                                            return "https://lh3.googleusercontent.com/d/{$image}=w1000";
                                        }
                                        // If it's not a Google Drive image, return it as is
                                        return $image;
                                    }
                                ?>
                                    <img class="img-profile rounded-circle" src="<?php echo $image ? get_drive_image_url($image) : '../img/undraw_profile.svg'; ?>">
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
                        <div class="card-header py-2">
                            <div class="row m-3">
                                <div class="col md-6">
                                    <h4 class="m-0 font-weight-bold text-dark">Company Name</h4>
                                    <h5>Jobrole</h5>
                                </div>
                                <div class="col md-6">
                                    <h4 class="m-0 font-weight-bold text-dark">Final Grade: %</h4>
                                </div>
                            </div>
                        </div>
                        <div class="card-body m-3">
                            <div class="row">
                                <!--ADVISER CRITERION-->
                                <div class="col-md-6">
                                    <div class="card mb-2">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-md-10">
                                                    <h6 class="m-0 font-weight-bold text-dark">ADVISER'S CRITERIA</h6>
                                                </div>
                                                <div class="col-md-2">
                                                    <h6 class="m-0 font-weight-bold text-dark">GRADE</h6>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body" id="adviserCriteria">
                                            <div class="row">
                                                <?php $id = key($adviserCriteriaGrouped);  // Use the first key if no specific ID is set ?>
                                                <?php if (isset($adviserCriteriaGrouped[$id]['adviserCriteria'])) { ?>
                                                    <?php foreach ($adviserCriteriaGrouped[$id]['adviserCriteria'] as $criteriaItem) { ?>
                                                        <!-- Row for Criteria and Grade -->
                                                        <div class="col-md-10">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <strong>
                                                                    <?php echo $criteriaItem['adviserCriteria']; ?>
                                                                </strong>
                                                            </div>

                                                            <!-- Description Below the Criteria -->
                                                            <div class="criteria-description" style="margin-left: 20px;">
                                                                <strong>Description:</strong><br>
                                                                <?php echo $criteriaItem['adviserDescription']; ?>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <!-- Score/Percentage aligned with Grade Column -->
                                                            <div class="d-flex justify-content-start align-items-center">
                                                                <span class="grade">
                                                                    <?php echo $criteriaItem['score']; ?> / <?php echo $criteriaItem['adviserPercentage']; ?>%
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <hr style="width: 530px; margin-block-start: 0.5em; margin-block-end: 0.5em; display: block;">
                                                    <?php } ?>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <div class="row m-2">
                                                <div class="col-md-10">
                                                    <h5 class="m-0 font-weight-bold text-dark">Total Grade:</h5>
                                                </div>
                                                <div class="col-md-2">
                                                    <h5 class="m-0 font-weight-bold text-dark"><?php echo $totalGradeAdviser?>%</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card mb-2">
                                        <div>
                                            <div class="card-header">
                                                <div class="row">
                                                    <div class="col-md-10">
                                                        <h6 class="m-0 font-weight-bold text-dark">COMPANY'S CRITERIA</h6>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <h6 class="m-0 font-weight-bold text-dark">GRADE</h6>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body" id="companyCriteria">
                                                
                                                <div class="row">
                                                    <?php $id = key($companyCriteriaGrouped)?>
                                                    <?php if (isset($companyCriteriaGrouped[$id]['companyCriteria'])) { ?>
                                                    <?php foreach ($companyCriteriaGrouped[$id]['companyCriteria'] as $criteriaItem) { ?>
                                                        <!-- Row for Criteria and Grade -->
                                                        <div class="col-md-10">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <strong>
                                                                    <?php echo $criteriaItem['companyCriteria']; ?>
                                                                </strong>
                                                            </div>

                                                            <!-- Description Below the Criteria -->
                                                            <div class="criteria-description" style="margin-left: 20px;">
                                                                <strong>Description:</strong><br>
                                                                <?php echo $criteriaItem['companyDescription']; ?>
                                                            </div>
                                                        </div>
                
                                                        <div class="col-md-2">
                                                            <!-- Score/Percentage aligned with Grade Column -->
                                                            <div class="d-flex justify-content-start align-items-center">
                                                                <span class="grade">
                                                                    <?php echo $criteriaItem['score']; ?> / <?php echo $criteriaItem['companyPercentage']; ?>%
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <hr style="width: 530px; margin-block-start: 0.5em; margin-block-end: 0.5em; display: block;">
                                                    <?php } ?>
                                                <?php } ?>
                                                </div>
                                            </div>
                                            <div class="card-footer">
                                                <div class="row m-2">
                                                    <div class="col-md-10">
                                                        <h5 class="m-0 font-weight-bold text-dark">Total Grade:</h5>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <h5 class="m-0 font-weight-bold text-dark"><?php echo $totalGradeCompany?>%</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- LOG OUT MODAL-->
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
    </body>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>
    <script src="../assets/js/sidebarscript.js"></script>
    
</html>