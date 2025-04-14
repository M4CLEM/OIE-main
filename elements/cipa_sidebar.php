<div class="d-flex">
    <button class="toggle-btn mt-2" type="button">
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
        <a href="management-acc.php" class="sidebar-link">
            <i class="fa fa-school" aria-hidden="true"></i>
            <span>Management</span>
        </a>
    </li>

    <li class="sidebar-item">
        
        <a href="managecollege.php" class="sidebar-link"><i class="lni lni-cog"></i>Manage Colleges</a>
    </li>


</ul>