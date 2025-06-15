<?php
    include_once("../includes/connection.php");
    session_start();

    $activeSemester = $_SESSION['semester'];
    $activeSchoolYear = $_SESSION['schoolYear'];
    $companyName = $_SESSION['companyName'];
    $applicationStat = 'Pending';

    // Step 1: Get slots per jobrole for this company in the current semester and schoolYear
    $slotsQuery = "
        SELECT jobrole, slots
        FROM companylist
        WHERE TRIM(companyName) = TRIM(?)
            AND TRIM(semester) = TRIM(?)
            AND TRIM(schoolYear) = TRIM(?)
        ";
    $slotsStmt = $connect->prepare($slotsQuery);
    $slotsStmt->bind_param("sss", $companyName, $activeSemester, $activeSchoolYear);
    $slotsStmt->execute();
    $slotsResult = $slotsStmt->get_result();

    $jobSlots = [];
    while ($row = $slotsResult->fetch_assoc()) {
        $jobSlots[trim($row['jobrole'])] = (int)$row['slots'];
    }
    $slotsStmt->close();

    // Step 2: Get count of approved applications per jobrole for this company
    $approvedCountQuery = "
        SELECT jobrole, COUNT(*) as approvedCount
        FROM applications
        WHERE companyName = ?
            AND semester = ?
            AND schoolYear = ?
            AND status = 'Approved'
        GROUP BY jobrole
    ";
    $approvedStmt = $connect->prepare($approvedCountQuery);
    $approvedStmt->bind_param("sss", $companyName, $activeSemester, $activeSchoolYear);
    $approvedStmt->execute();
    $approvedResult = $approvedStmt->get_result();

    $approvedCounts = [];
    while ($row = $approvedResult->fetch_assoc()) {
        $approvedCounts[trim($row['jobrole'])] = (int)$row['approvedCount'];
    }
    $approvedStmt->close();

    // Step 3: Calculate remaining slots per jobrole
    $remainingSlots = [];
    foreach ($jobSlots as $jobrole => $slots) {
        $approved = $approvedCounts[$jobrole] ?? 0;
        $remainingSlots[$jobrole] = $slots - $approved;
    }

    // Step 4: Query pending applications but only for jobroles with remaining slots > 0
    // Prepare a list of jobroles with available slots
    $allowedJobRoles = array_filter($remainingSlots, fn($slots) => $slots > 0);
    if (empty($allowedJobRoles)) {
        // No available slots, so no applicants to show
        $applicants = [];
    } else {
        // Use placeholders for prepared statement IN clause
        $placeholders = implode(',', array_fill(0, count($allowedJobRoles), '?'));

$query = "
    SELECT a.id AS applicationID, a.studentID, a.jobrole, a.companyCode, a.applicationDate,
           s.firstName, s.lastName, s.course, s.section, d.file_name, d.file_link
    FROM applications a
    JOIN student_masterlist s ON a.studentID = s.studentID
    JOIN (
        SELECT d1.*
        FROM documents d1
        INNER JOIN (
            SELECT student_ID, MAX(id) AS max_id
            FROM documents
            WHERE document = 'Resume'
              AND semester = ?
              AND schoolYear = ?
            GROUP BY student_ID
        ) latest ON d1.id = latest.max_id
    ) d ON a.studentID = d.student_ID
    WHERE a.companyName = ? 
        AND a.semester = ? 
        AND a.schoolYear = ? 
        AND a.status = ?
        AND s.semester = ? 
        AND s.schoolYear = ?
        AND a.jobrole IN ($placeholders)
";


        $stmt = $connect->prepare($query);

        // Bind parameters: first the fixed ones, then the jobroles dynamically
        $types = str_repeat('s', 8 + count($allowedJobRoles)); // 8 fixed string params + jobroles
$params = array_merge(
    [$activeSemester, $activeSchoolYear,  // For subquery
     $companyName, $activeSemester, $activeSchoolYear, $applicationStat, 
     $activeSemester, $activeSchoolYear], // For main query
    array_keys($allowedJobRoles)
);
$types = str_repeat('s', count($params));

        // Use a reference array for bind_param
        $refs = [];
        foreach ($params as $key => $value) {
            $refs[$key] = &$params[$key];
        }

        // call_user_func_array for bind_param with references
        call_user_func_array([$stmt, 'bind_param'], array_merge([$types], $refs));

        $stmt->execute();
        $result = $stmt->get_result();

        $applicants = [];
        while ($row = $result->fetch_assoc()) {
            $applicants[] = $row;
        }
        $stmt->close();
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <?php include("../elements/meta.php"); ?>
        <title>Industry Partner Portal</title>
        <?php include("embed.php"); ?>
        <link rel="stylesheet" href="../assets/css/new-style.css">
    </head>
    <body id="page-top">
        <div id="wrapper">
            <!--Sidebar Wrapper-->
            <aside id="sidebar" class="expand">
                <?php include('../elements/ip_sidebar.php') ?>
            </aside>

            <div class="main">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 static-top shadow">
                    <!-- Title -->
                    <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Student Interns</h4>
                    <!-- Topbar Navbar -->
                    <?php include('../elements/ip_navbar_user_info.php') ?>
                </nav>

                <div class="col-lg-12 mb-4">
                    <div class="card shadow mb-3">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-dark">List of Applicants</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="1">
                                    <thead>
                                        <tr>
                                            <th>Student Number</th>
                                            <th>Student Name</th>
                                            <th>Course-Section</th>
                                            <th>Jobrole</th>
                                            <th>Application Date</th>
                                            <th>Resume</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody style="max-height: 80vh; overflow-y: auto;">
                                        <?php if (!empty($applicants)): ?>
                                            <?php foreach ($applicants as $applicant): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($applicant['studentID']) ?></td>
                                                    <td><?= htmlspecialchars($applicant['lastName'] . ', ' . $applicant['firstName']) ?></td>
                                                    <td><?= htmlspecialchars($applicant['course'] . '-' . $applicant['section']) ?></td>
                                                    <td><?= htmlspecialchars($applicant['jobrole']) ?></td>
                                                    <td><?= htmlspecialchars($applicant['applicationDate']) ?></td>
                                                    <td><?= htmlspecialchars($applicant['file_name']) ?></td>
                                                    <td>
                                                        <a class="btn btn-primary btn-sm" href="<?= htmlspecialchars($applicant['file_link']) ?>" target="_blank" rel="noopener noreferrer"><i class="fa fa-eye">View</i></a>
                                                        <!-- Approve Form -->
                                                        <form action="functions/application_approval.php" method="POST" style="display:inline;">
                                                            <input type="hidden" name="studentID" value="<?= htmlspecialchars($applicant['studentID']) ?>">
                                                            <input type="hidden" name="companyCode" value="<?= htmlspecialchars($applicant['companyCode']) ?>">
                                                            <input type="hidden" name="applicationID" value="<?= htmlspecialchars($applicant['applicationID']) ?>">
                                                            <input type="hidden" name="jobrole" value="<?= htmlspecialchars($applicant['jobrole']) ?>">
                                                            <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                                        </form>

                                                        <!-- Reject Form -->
                                                        <form action="functions/application_rejection.php" method="POST" style="display:inline;">
                                                            <input type="hidden" name="studentID" value="<?= htmlspecialchars($applicant['studentID']) ?>">
                                                            <input type="hidden" name="applicationID" value="<?= htmlspecialchars($applicant['applicationID']) ?>">
                                                            <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center">No applicants found.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
            <script src="../assets/js/sidebarscript.js"></script>
        </div>
    </body>
</html>