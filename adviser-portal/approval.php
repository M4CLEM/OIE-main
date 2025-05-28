<?php
session_start();
include_once("../includes/connection.php");

$activeSemester = $_SESSION['semester'];
$activeSchoolYear = $_SESSION['schoolYear'];

if (isset($_SESSION['dept_sec']) && !empty($_SESSION['dept_sec'])) {
    // Handle case where dept_sec is a single string with comma-separated values
    if (is_array($_SESSION['dept_sec'])) {
        $sectionArray = [];
        foreach ($_SESSION['dept_sec'] as $sec) {
            $parts = explode(',', $sec); // Split strings like "4A,4B,4C"
            foreach ($parts as $part) {
                $sectionArray[] = trim($part);
            }
        }
    } else {
        $sectionArray = explode(',', $_SESSION['dept_sec']); // fallback, just in case
        $sectionArray = array_map('trim', $sectionArray);
    }

    // Build FIND_IN_SET conditions
    $conditions = [];
    foreach ($sectionArray as $section) {
        $section = mysqli_real_escape_string($connect, $section);
        $conditions[] = "FIND_IN_SET('$section', section)";
    }

    $sectionFilter = implode(" OR ", $conditions);

    // Escape semester and school year
    $activeSemesterEscaped = mysqli_real_escape_string($connect, $activeSemester);
    $activeSchoolYearEscaped = mysqli_real_escape_string($connect, $activeSchoolYear);

    // Build the query
    $query = "SELECT * FROM company_info 
              WHERE ($sectionFilter) 
              AND semester = '$activeSemesterEscaped' 
              AND schoolYear = '$activeSchoolYearEscaped'";

    $result = mysqli_query($connect, $query);
}



if (isset($_POST['Approve'])) {
    echo ('button clicked');
    $query = "select * from company_info"; 
    $result = mysqli_query($connect, $query);

    $companyCode = $_POST['companyCode'];
    $studentEmail = $_POST['studentEmail'];
    $trainerEmail = $_POST['trainerEmail'];

    echo $studentEmail;

    $updateCompanyInfo = $connect->prepare("UPDATE company_info SET status = 'Approved', dateStarted = NOW() WHERE companyCode = ?");
    $updateCompanyInfo->bind_param("s", $companyCode);
    $updateCompanyInfo->execute();

    $updateStudentInfo = $connect->prepare("UPDATE studentinfo SET status = 'Deployed', companyCode = ?, trainerEmail = ? WHERE email = ? AND semester = ? AND school_year = ?");
    $updateStudentInfo->bind_param("sssss", $companyCode, $trainerEmail, $studentEmail, $activeSemester, $activeSchoolYear);
    $updateStudentInfo->execute();

    $applicationDelete = "UPDATE applications SET schoolRemarks = 'Approved' WHERE studentID = ? AND semester = ? AND schoolYear = ?";
    $applicationStmt = $connect->prepare($applicationDelete);
    $applicationStmt->bind_param("sss", $studentID, $activeSemester, $activeSchoolYear);
    $applicationStmt->execute();

    header("Location: approval.php");

    exit;
}

if (isset($_POST['ApproveChange'])) {
    $companyCode = $_POST['companyCode'];
    $studentID = $_POST['studentID'];

    // Delete the row from the company_info table
    $deleteQuery = "DELETE FROM company_info WHERE companyCode = ?";
    $stmt = $connect->prepare($deleteQuery);
    $stmt->bind_param("s", $companyCode);
    $stmt->execute();

    $updateQuery = "UPDATE studentinfo SET status = 'Undeployed', companyCode = NULL, trainerEmail = NULL WHERE studentID = ? AND semester = ? AND school_year = ?";
    $updateStmt = $connect->prepare($updateQuery);
    $updateStmt->bind_param("sss", $studentID, $activeSemester, $activeSchoolYear);
    $updateStmt->execute();

    $applicationDelete = "DELETE FROM applications WHERE studentID = ? AND semester = ? AND school_year = ?";
    $applicationStmt = $connect->prepare($applicationDelete);
    $applicationStmt->bind_param("sss", $studentID, $activeSemester, $activeSchoolYear);
    $applicationStmt->execute();

    header("Location: approval.php");
    exit;
}

if (isset($_POST['PullOut'])) {
    $studentID = $_POST['studentID'];

    // Delete the row from the company_info table
    $deleteQuery = "DELETE FROM company_info WHERE studentID = ? AND semester = ? AND schoolYear = ?";
    $stmt = $connect->prepare($deleteQuery);
    $stmt->bind_param("sss", $studentID, $activeSemester, $activeSchoolYear);
    $stmt->execute();

    $updateQuery = "UPDATE studentinfo SET status = 'Undeployed', companyCode = NULL, trainerEmail = NULL WHERE studentID = ? AND semester = ? AND school_year = ?";
    $updateStmt = $connect->prepare($updateQuery);
    $updateStmt->bind_param("sss", $studentID, $activeSemester, $activeSchoolYear);
    $updateStmt->execute();

    $applicationDelete = "DELETE FROM applications WHERE studentID = ? AND semester = ? AND school_year = ?";
    $applicationStmt = $connect->prepare($applicationDelete);
    $applicationStmt->bind_param("sss", $studentID, $activeSemester, $activeSchoolYear);
    $applicationStmt->execute();

    header("Location: approval.php");
    exit;
}

if (isset($_POST['Reject'])) {
    $studentID = $_POST['studentID'];

    $rejectQuery = "UPDATE company_info SET status = 'Rejected' WHERE studentID = ? AND semester = ? AND schoolYear = ?";
    $stmt = $connect->prepare($rejectQuery);
    $stmt->bind_param("sss", $studentID, $activeSemester, $activeSchoolYear);
    $stmt->execute();

    $applicationDelete = "UPDATE applications SET schoolRemarks = 'Rejected' WHERE studentID = ? AND semester = ? AND schoolYear = ?";
    $applicationStmt = $connect->prepare($applicationDelete);
    $applicationStmt->bind_param("sss", $studentID, $activeSemester, $activeSchoolYear);
    $applicationStmt->execute();

    header("Location: approval.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <?php include("../elements/meta.php"); ?>
    <title>Adviser Portal</title>
    <?php include("embed.php"); ?>
    <link rel="stylesheet" href="../assets/css/new-style.css">
    
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!--Sidebar Wrapper-->
        <aside id="sidebar" class="expand">
            <?php include('../elements/adv_sidebar.php') ?>
        </aside>

        <div class="main">

            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 static-top shadow">
                <!-- Title -->
                <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Student Interns</h4>
                <!-- Topbar Navbar -->
                <?php include('../elements/adv_navbar_user_info.php'); ?>
            </nav>
            <!-- End of Topbar -->

            <!-- Begin Page Content -->
            <div class="col-lg-12 mb-4">

                <!-- Illustrations -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-dark">DEPLOYMENT INFORMATION</h6>
                    </div>
                    <div class="card-body">
                        <style>
                            .table td,
                            .table th {
                                font-size: 11px;
                            }
                        </style>
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th scope="col" width=15%;>Student Name</th>
                                        <th scope="col" width=10%;>Company Code</th>
                                        <th scope="col">Company Name</th>
                                        <th scope="col">Company Address</th>
                                        <th scope="col">Contact Number</th>
                                        <th scope="col" width=15%;>Trainer Email</th>
                                        <th scope="col">Work Type</th>
                                        <th scope="col">Company Remark</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        if ($result->num_rows > 0) {
                                            $modalIndex = 0; // to ensure unique modal IDs
                                            while ($row = $result->fetch_assoc()) {
                                                $queryStud = "SELECT * FROM studentinfo WHERE email='{$row['student_email']}'";
                                                $resultStud = mysqli_query($connect, $queryStud);
                                                $rowStud = $resultStud->fetch_assoc();
                                                $studentFullName = $rowStud['firstname'] . " " . $rowStud['middlename'] . " " . $rowStud['lastname'];

                                                $modalApproveID = "confirmApprove{$modalIndex}";
                                                $modalApproveChangeID = "confirmApproveChange{$modalIndex}";
                                                $modalPullOutID = "confirmPullOut{$modalIndex}";
                                                $modalRejectID = "confirmReject{$modalIndex}";

                                                echo "<tr>";
                                                echo "<td>{$studentFullName}</td>";
                                                echo "<td>{$row['companyCode']}</td>";
                                                echo "<td>{$row['companyName']}</td>";
                                                echo "<td>{$row['companyAddress']}</td>";
                                                echo "<td>" . ($row['trainerContact'] ?? 'To be Assigned') . "</td>";
                                                echo "<td>" . ($row['trainerEmail'] ?? 'To be Assigned') . "</td>";
                                                echo "<td>{$row['workType']}</td>";
                                                echo "<td>{$row['remarks']}</td>";

                                                echo "<td>";
                                                // Action buttons + modals
                                                if ($row['status'] == 'Change Request') {
                                                    echo "
                                                        <button type='button' class='btn btn-success rounded-2 btn-sm' style='font-size: 11px;' data-bs-toggle='modal' data-bs-target='#{$modalApproveChangeID}'>Approve Change</button>
                                                        <!-- Modal -->
                                                        <div class='modal fade' id='{$modalApproveChangeID}' tabindex='-1'>
                                                            <div class='modal-dialog'>
                                                                <form method='post'>
                                                                    <input type='hidden' name='companyCode' value='{$row['companyCode']}'>
                                                                    <input type='hidden' name='studentID' value='{$rowStud['studentID']}'>
                                                                    <div class='modal-content'>
                                                                        <div class='modal-header'>
                                                                            <h5 class='modal-title'>Approve Company Change</h5>
                                                                            <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                                                                        </div>
                                                                        <div class='modal-body fs-5'>
                                                                            Are you sure you want to approve this company change for <strong>{$studentFullName}</strong>?
                                                                        </div>
                                                                        <div class='modal-footer'>
                                                                            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancel</button>
                                                                            <button type='submit' name='ApproveChange' class='btn btn-success'>Confirm</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>";
                                                    } elseif ($row['status'] == 'Pending') {
                                                        echo "
                                                            <button type='button' class='btn btn-sm btn-success w-100 rounded-2' data-bs-toggle='modal' data-bs-target='#{$modalApproveID}'>Approve</button>
                                                            <button type='button' class='btn btn-sm btn-danger w-100 rounded-2'
                                                            data-bs-toggle='modal' data-bs-target='#{$modalRejectID}'>Reject</button>
                                                            <!-- Modal -->
                                                                <div class='modal fade' id='{$modalApproveID}' tabindex='-1'>
                                                                    <div class='modal-dialog'>
                                                                        <form method='post'>
                                                                            <input type='hidden' name='studentEmail' value='{$row['student_email']}'>
                                                                            <input type='hidden' name='companyCode' value='{$row['companyCode']}'>
                                                                            <input type='hidden' name='trainerEmail' value='{$row['trainerEmail']}'>
                                                                            <div class='modal-content'>
                                                                                <div class='modal-header'>
                                                                                    <h5 class='modal-title'>Confirm Approval</h5>
                                                                                    <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                                                                                </div>
                                                                                <div class='modal-body fs-5'>
                                                                                    Approve deployment of <strong>{$studentFullName}</strong> to <strong>{$row['companyName']}</strong>?
                                                                                </div>
                                                                                <div class='modal-footer'>
                                                                                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancel</button>
                                                                                    <button type='submit' name='Approve' class='btn btn-success'>Approve</button>
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                                <!-- REJECT MODAL -->
                                                                <div class='modal fade' id='{$modalRejectID}' tabindex='-1'>
                                                                    <div class='modal-dialog'>
                                                                        <form method='post'>
                                                                            <input type='hidden' name='studentID' value='{$row['studentID']}'>
                                                                            <input type='hidden' name='companyCode' value='{$row['companyCode']}'>
                                                                            <input type='hidden' name='trainerEmail' value='{$row['trainerEmail']}'>
                                                                            <div class='modal-content'>
                                                                                <div class='modal-header'>
                                                                                    <h5 class='modal-title'>Confirm Rejection</h5>
                                                                                    <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                                                                                </div>
                                                                                <div class='modal-body fs-5'>
                                                                                    Reject deployment of <strong>{$studentFullName}</strong> to <strong>{$row['companyName']}</strong>?
                                                                                </div>
                                                                                <div class='modal-footer'>
                                                                                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancel</button>
                                                                                    <button type='submit' name='Reject' class='btn btn-success'>Reject</button>
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>";
                                                            } elseif ($row['status'] == 'Approved') {
                                                                echo "
                                                                    <b><p style='color: green;'>Approved!</p></b>
                                                                    <button type='button' class='btn btn-danger rounded-2 btn-sm' style='font-size: 11px;' data-bs-toggle='modal' data-bs-target='#{$modalPullOutID}'>Pull-Out</button>
                                                                    <!-- Modal -->
                                                                    <div class='modal fade' id='{$modalPullOutID}' tabindex='-1'>
                                                                        <div class='modal-dialog'>
                                                                            <form method='post'>
                                                                                <input type='hidden' name='studentID' value='{$rowStud['studentID']}'>
                                                                                <div class='modal-content'>
                                                                                    <div class='modal-header'>
                                                                                        <h5 class='modal-title'>Confirm Pull-Out</h5>
                                                                                        <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                                                                                    </div>
                                                                                    <div class='modal-body fs-5'>
                                                                                        Are you sure you want to pull out <strong>{$studentFullName}</strong> from the assigned company?
                                                                                    </div>
                                                                                    <div class='modal-footer'>
                                                                                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancel</button>
                                                                                        <button type='submit' name='PullOut' class='btn btn-danger'>Confirm</button>
                                                                                    </div>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>";
                                                                } elseif ($row['status'] == 'Rejected'){
                                                                    echo "<b><p style='color: red;'>Rejected!</p></b>";
                                                                } else {
                                                                    echo "<b><p style='color: gray;'>Status Unknown</p></b>";
                                                                }

                                                                echo "</td>";
                                                                echo "</tr>";
                                                                $modalIndex++;
                                                            }
                                                        } else {
                                                            echo "<tr><td colspan='9'>No students found.</td></tr>";
                                                        }
                                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End of Main Content -->

    </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    <script src="../assets/js/sidebarscript.js"></script>

</body>

</html>