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
        <div class="sidebar-logo">
            <a href="student-interns.php">CIPA Portal</a>
        </div>
    </div>
    <ul class="sidebar-nav">
        <li class="sidebar-item">
            <a href="student-interns.php" class="sidebar-link">
                <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                <span>Student Interns</span></a>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="check_student_docs.php" class="sidebar-link">
                <i class="fa fa-file" aria-hidden="true"></i>
                <span>Student Documents</span></a>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="company.php" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse" data-bs-target="#comp" aria-expanded="false" aria-controls="comp">
                <i class="lni lni-briefcase-alt"></i>
                <span>Companies</span>
            </a>
            <ul id="comp" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                <li class="sidebar-item">
                    <a href="company.php" class="sidebar-link">All Departments</a>
                </li>

                <?php
                    $queryDept = "select * from department_list";
                    $resultDept = mysqli_query($connect, $queryDept);
                    while ($rowDept = mysqli_fetch_assoc($resultDept)) {
                ?>

                    <li class="sidebar-item">
                        <a href='company-filter.php?dept=<?php echo $rowDept['department']; ?>' class="sidebar-link"><?php echo $rowDept['department']; ?></a>
                    </li>

                <?php
                }
                ?>

            </ul>
        </li>
        <li class="sidebar-item">
            <a href="add_company_acc.php" class="sidebar-link">
                <i class="fa fa-file" aria-hidden="true"></i>
                <span>Company Accounts</span></a>
            </a>
        </li>

        <li class="sidebar-item">
            <a href="moa.php" class="sidebar-link">
                <i class="fa fa-handshake" aria-hidden="true"></i>
                <span>MOAs</span>
            </a>
        </li>

        <li class="sidebar-item">
            <a href="management-acc.php" class="sidebar-link">
                <i class="fa fa-school" aria-hidden="true"></i>
                <span>Management</span>
            </a>
        </li>

        <li class="sidebar-item">
            <a href="managecollege.php" class="sidebar-link">
                <i class="lni lni-cog"></i>
                <span>Manage Colleges</span>
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
