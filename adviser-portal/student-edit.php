<?php
    session_start();
    include_once("../includes/connection.php");

    if (!isset($_SESSION['adviser'])) {
        header("Location: ../logout.php");
        exit();
    }

    // Fetch session values
    $semester = trim($_SESSION['semester']);
    $schoolYear = trim($_SESSION['schoolYear']);

    // Get the studentID from the URL
    $studentID = $_GET['studentID'] ?? '';

    // Fetch student record using correct session values
    $query = "SELECT * FROM studentinfo WHERE studentID = ? AND semester = ? AND school_year = ?";
    $stmt = $connect->prepare($query);

    if (!$stmt) {
        die("Query preparation failed: " . $connect->error);
    }

    $stmt->bind_param("sss", $studentID, $semester, $schoolYear);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($rows = $result->fetch_assoc()) {
        $firstname = $rows['firstname'];
        $middlename = $rows['middlename'];
        $lastname = $rows['lastname'];
        $department = $rows['department'];
        $course = $rows['course'];
        $status = $rows['status'];
    } else {
        $firstname = $middlename = $lastname = $department = $course = $status = '';
        echo '<div class="alert alert-danger m-3">No student record found.</div>';
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include("../elements/meta.php"); ?>
        <title>Adviser Portal</title>
        <?php include("embed.php"); ?>
    </head>

    <body id="page-top">
        <div id="wrapper">
            <!-- Sidebar -->
            <aside id="sidebar" class="expand">
                <?php include('../elements/adv_sidebar.php'); ?>
            </aside>

            <div class="main">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 shadow">
                    <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Student Interns</h4>
                    <?php include('../elements/adv_navbar_user_info.php'); ?>
                </nav>

                <!-- Page Content -->
                <div class="col-lg-12 mb-4">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <form id="updateForm" action="functions/student-edit-process.php" method="POST">
                                <input type="hidden" name="update" value="1">
                                <h4 class="m-0 font-weight-bold text-dark">Edit</h4>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-lg-5">
                                            <div class="col-md-10">
                                                <label for="studentID">Student ID:</label>
                                                <input class="form-control" id="studentID" name="studentID" type="text"
                                                    value="<?php echo htmlspecialchars($studentID); ?>" autocomplete="off" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-5">
                                            <div class="col-md-10">
                                                <label for="firstname">First Name:</label>
                                                <input class="form-control" id="firstname" name="firstname" type="text"
                                                    value="<?php echo htmlspecialchars($firstname); ?>" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-lg-5">
                                            <div class="col-md-10">
                                                <label for="middlename">Middle Name:</label>
                                                <input class="form-control" id="middlename" name="middlename" type="text"
                                                    value="<?php echo htmlspecialchars($middlename); ?>" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-5">
                                            <div class="col-md-10">
                                                <label for="lastname">Last Name:</label>
                                                <input class="form-control" id="lastname" name="lastname" type="text"
                                                    value="<?php echo htmlspecialchars($lastname); ?>" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-lg-5">
                                            <div class="col-md-10">
                                                <label for="course">Course:</label>
                                                <input class="form-control" id="course" name="course" type="text"
                                                    value="<?php echo htmlspecialchars($course); ?>" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-5">
                                            <div class="col-md-10">
                                                <label for="status">Status:</label>
                                                <input class="form-control mb-4" id="status" name="status" type="text"
                                                    value="<?php echo htmlspecialchars($status); ?>" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-md-8">
                                            <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#confirmModal">
                                                <span class="fa fa-save"></span> Update
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div> <!-- End of main -->
            </div> <!-- End of wrapper -->

            <!-- Confirm Modal -->
            <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirm Update</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">Are you sure you want to update this student's record?</div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary btn-sm" id="confirmUpdateBtn">Yes, Update</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading Modal -->
            <div class="modal fade" id="loadingModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content text-center p-4">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-3 mb-0">Updating, please wait...</p>
                    </div>
                </div>
            </div>

            <!-- Success Modal -->
            <div class="modal fade" id="successModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content text-center p-4">
                        <h5 class="mb-3 text-success">Update Successful!</h5>
                        <button type="button" class="btn btn-success btn-sm" onclick="window.location.href='student-list.php'">OK</button>
                    </div>
                </div>
            </div>

            <!-- Scripts -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
                integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
                crossorigin="anonymous"></script>
            <script src="../assets/js/sidebarscript.js"></script>

            <script>
                document.getElementById('confirmUpdateBtn').addEventListener('click', function () {
                    // Manually hide confirm modal (Bootstrap 5.0 style)
                    const confirmModalEl = document.getElementById('confirmModal');
                    const confirmModal = new bootstrap.Modal(confirmModalEl);
                    confirmModalEl.classList.remove('show');
                    confirmModalEl.style.display = 'none';
                    document.body.classList.remove('modal-open');
                    document.querySelector('.modal-backdrop').remove();

                    // Show loading modal
                    const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
                    loadingModal.show();

                    // Submit the form after slight delay
                    setTimeout(() => {
                        document.getElementById('updateForm').submit();
                    }, 500);
                });
            </script>


            <?php if (isset($_GET['update']) && $_GET['update'] === 'success'): ?>
                <script>
                    window.addEventListener('DOMContentLoaded', () => {
                        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                        successModal.show();
                    });
                </script>
            <?php endif; ?>
    </body>
</html>
