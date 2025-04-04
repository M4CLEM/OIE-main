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
    
    $studentEmail = $_SESSION['student'];

    $queryChecker = "SELECT status FROM studentinfo WHERE email = ? AND semester = ? AND school_year = ?";
    $stmtChecker = $connect->prepare($queryChecker);
    $stmtChecker->bind_param("sss", $studentEmail, $semester, $schoolYear);
    $stmtChecker->execute();
    $resultChecker = $stmtChecker->get_result();
    $rowChekcer = $resultChecker->fetch_assoc();
    $studentStatus = $rowChekcer['status'];
    
    ?>

    <?php if ($studentStatus == 'Undeployed'): ?>
        <li class="sidebar-item">
            <a href="company.php" class="sidebar-link">
                <i class="fa fa-building fa-sm fa-fw mr-2 text-gray-400"></i>
                <span>List of Company</span>
            </a>
        </li>
    <?php elseif ($studentStatus == 'Deployed'): ?>
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
            <span>Deployment</span></a>
        </a>
    </li>

    <li class="sidebar-item">
        <a href="view-grades.php" class="sidebar-link">
        <i class="fas fa-star fa-sm fa-fw mr-2 text-gray-400"></i>
            <span>View Grade</span></a>
        </a>
    </li>

    <li class="sidebar-item">
        <a href="stud_documents.php" class="sidebar-link">
            <i class="fas fa-folder fa-sm fa-fw mr-2 text-gray-400"></i>
            <span>Documents</span></a>
        </a>
    </li>

    

</ul>