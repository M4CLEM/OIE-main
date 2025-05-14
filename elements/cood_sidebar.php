<div class="d-flex">
    <button class="toggle-btn mt-3" type="button">
        <img src="../img/logo2.png" alt="Logo">
    </button>
    <div class="sidebar-logo mt-4">
        <a href="masterlist.php">Coordinator<br>Portal</a>
    </div>
</div>
<ul class="sidebar-nav">

    <li class="sidebar-item">
        <a href="dashboard.php" class="sidebar-link">
            <i class="fas fa-chart-bar fa-sm fa-fw mr-2 text-gray-400"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <li class="sidebar-item">
        <a href="masterlist.php" class="sidebar-link">
            <i class="fa fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
            <span>Masterlist</span>
        </a>
    </li>

    <li class="sidebar-item">
        <a href="grading-view.php" class="sidebar-link">
            <i class="fa fa-id-card fa-sm fa-fw mr-2 text-gray-400"></i>
            <span>Grading</span>
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
        <a href="advisers.php" class="sidebar-link">
            <i class="fa fa-id-card fa-sm fa-fw mr-2 text-gray-400"></i>
            <span>Advisers</span>
        </a>
    </li>
    <li class="sidebar-item">
        <a href="documents.php" class="sidebar-link">
            <i class="fas fa-folder fa-sm fa-fw mr-2 text-gray-400"></i>
            <span>Documents</span>
        </a>
    </li>

</ul>