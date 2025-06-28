<?php
    include("../includes/connection.php");

    $semester = $_SESSION['semester'];
    $schoolYear = $_SESSION['schoolYear'];
?>

<style>
    #sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: 250px;
        height: 100vh;
        padding: 10px;
        overflow-x: hidden;
        transition: width 0.3s ease, padding 0.3s ease;
        background-color: black;
    }

    #sidebar.collapsed {
        width: 0 !important;
        padding: 0 !important;
        overflow: hidden !important;
        background-color: transparent !important;
        border: none !important;
    }

    #sidebar.collapsed .sidebar-hide {
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
    }

    .main, #content-wrapper {
        margin-left: 230px;
        transition: margin-left 0.3s ease;
        position: relative;
    }

    #sidebar.collapsed ~ .main , #sidebar.collapsed ~ #content-wrapper {
        margin-left: 0 !important;
    }

    /* Toggle button default - visually inside sidebar */
    #sidebarToggle {
        position: fixed;
        top: 20px;
        left: 34px; /* Appears inside expanded sidebar */
        padding: 4px 8px;
        font-size: 14px;
        border-radius: 4px;
        border: 1px solid #ccc;
        background: #fff;
        color: #333;
        transition: left 0.3s ease, background 0.3s ease, color 0.3s ease, border-color 0.3s ease;
    }

    /* When sidebar is collapsed */
    body.sidebar-collapsed #sidebarToggle {
        left: 15px; /* Moves into navbar space */
        background-color: #000;
        color: #fff;
        border-color: #000;
    }


    .sidebar-toggle-container {
        padding: 10px;
        position: fixed;
        top: 0;
        left: 0;
    }

    .sidebar-hide {
        padding-top: 15px; /* adjust value as needed */
    }

    /* Default h4 padding (when sidebar is expanded) */
    nav.navbar h4, nav.navbar h2 {
        padding-left: 0;
        transition: padding-left 0.3s ease;
    }

    /* Add left padding only when sidebar is collapsed */
    body.sidebar-collapsed nav.navbar h4, body.sidebar-collapsed nav.navbar h2 {
        padding-left: 40px; /* adjust value as needed to clear the toggle button */
    }

    .sidebar-no-transition #sidebar,
    .sidebar-no-transition #sidebar *,
    .sidebar-no-transition .main,
    .sidebar-no-transition .main * {
        transition: none !important;
    }
</style>

<div class="sidebar-toggle-container">
    <button id="sidebarToggle" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-bars"></i>
    </button>
</div>

<div class="sidebar-hide">
    <div class="d-flex">
        <button class="toggle-btn mt-3" type="button">
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
                <a href="applications.php" class="sidebar-link">
                    <i class="fas fa-user-tie" aria-hidden="true"></i>
                    <span>Applications</span>
                </a>
            </li>

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
</div>

<script>
    // Add no-transition class early to suppress animation on load
    document.documentElement.classList.add("sidebar-no-transition");

    document.addEventListener("DOMContentLoaded", function () {
        const sidebar = document.getElementById("sidebar");
        const toggleBtn = document.getElementById("sidebarToggle");
        const body = document.body;

        // Restore sidebar state from localStorage
        const savedState = localStorage.getItem("sidebarState");
        if (savedState === "collapsed") {
            sidebar.classList.add("collapsed");
            sidebar.classList.remove("expand");
            body.classList.add("sidebar-collapsed");
        } else {
            sidebar.classList.add("expand");
            sidebar.classList.remove("collapsed");
            body.classList.remove("sidebar-collapsed");
        }

        // Enable transition AFTER classes are applied
        requestAnimationFrame(() => {
            document.documentElement.classList.remove("sidebar-no-transition");
        });

        // Toggle and save sidebar state
        toggleBtn.addEventListener("click", function () {
            const isCollapsed = sidebar.classList.toggle("collapsed");
            sidebar.classList.toggle("expand");
            body.classList.toggle("sidebar-collapsed");

            localStorage.setItem("sidebarState", isCollapsed ? "collapsed" : "expand");
        });
    });
</script>