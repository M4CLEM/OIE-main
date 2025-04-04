<?php
    include("../includes/connection.php");

    $semester = $_SESSION['semester'];
    $schoolYear = $_SESSION['schoolYear'];
?>

<div class="d-flex">
    <button class="toggle-btn mt-2" type="button">
        <img src="../img/logo2.png" alt="Logo">
    </button>
    <div class="sidebar-logo mt-4">
        <a href="student.php">Intern Portal</a>
    </div>
</div>
<ul class="sidebar-nav">

    <li class="sidebar-item ">
        <a href="student.php" class="sidebar-link">
            <i class="fa fa-user-circle" aria-hidden="true"></i>            
            <span>Your Resume</span></a>
        </a>
    </li>

    <?php 
    
    $studentEmail = $_SESSION['student'] ?? null;

if (!$studentEmail) {
    echo "Student email is not set.";
    exit;
}

$studentStatus = null;
$studentID = null;
$hasStudentInfo = false;
$showCompanyLink = false;

// Step 1: Get studentID from studentinfo
$query = "SELECT studentID, status FROM studentinfo WHERE email = ? AND semester = ? AND school_year = ?";
$stmt = $connect->prepare($query);
$stmt->bind_param("sss", $studentEmail, $semester, $schoolYear);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $hasStudentInfo = true;
    $studentID = $row['studentID'];
    $studentStatus = $row['status'];

    // Condition 1: Status is Undeployed
    if ($studentStatus === 'Undeployed') {
        $showCompanyLink = true;
    }
} else {
    // Step 2: If no record in studentinfo, get studentID from any record in studentinfo (no semester filtering here)
    $getIDQuery = "SELECT studentID FROM studentinfo WHERE email = ? LIMIT 1";
    $stmtID = $connect->prepare($getIDQuery);
    $stmtID->bind_param("s", $studentEmail);
    $stmtID->execute();
    $idResult = $stmtID->get_result();

    if ($idRow = $idResult->fetch_assoc()) {
        $studentID = $idRow['studentID'];

        // Check if student is enrolled in current semester/year
        $enrollQuery = "SELECT 1 FROM student_masterlist WHERE studentID = ? AND semester = ? AND schoolYear = ? LIMIT 1";
        $enrollStmt = $connect->prepare($enrollQuery);
        $enrollStmt->bind_param("sss", $studentID, $semester, $schoolYear);
        $enrollStmt->execute();
        $enrollResult = $enrollStmt->get_result();

        if ($enrollResult->num_rows > 0) {
            $showCompanyLink = true;
        }
    }
}
    
    ?>

    <?php if ($showCompanyLink): ?>
        <li class="sidebar-item">
            <a href="company.php" class="sidebar-link">
                <i class="fa fa-building fa-sm fa-fw mr-2 text-gray-400"></i>
                <span>List of Company</span>
            </a>
        </li>
    <?php endif; ?>
    <?php if ($studentStatus == 'Deployed'): ?>
        <li class="sidebar-item">
            <a href="dtr.php" class="sidebar-link">
                <i class="fa fa-plus-square" aria-hidden="true"></i>
                <span>Attendance</span>
            </a>
        </li>
    <?php endif; ?>


    <li class="sidebar-item">
        <a href="deploy.php" class="sidebar-link">
            <i class="fa fa-building fa-sm fa-fw mr-2 text-gray-400"></i>
            <span>Deployment</span>
        </a>
    </li>

    <li class="sidebar-item">
        <a href="view-grades.php" class="sidebar-link">
            <i class="fas fa-star fa-sm fa-fw mr-2 text-gray-400"></i>
            <span>View Grade</span>
        </a>
    </li>

    <li class="sidebar-item">
        <a href="stud_documents.php" class="sidebar-link">
            <i class="fas fa-folder fa-sm fa-fw mr-2 text-gray-400"></i>
            <span>Documents</span>
        </a>
    </li>
    
</ul>