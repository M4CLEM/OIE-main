<?php
    session_start();
    include("../includes/connection.php");

    // Get the student's email from the session
    $email = $_SESSION['student'];
    $semester = $_SESSION['semester'];
    $schoolYear = $_SESSION['schoolYear'];

    $applicationQuery = "SELECT * FROM applications WHERE email = ? AND semester = ? AND schoolYear = ?";
    $applicationStmt = $connect->prepare($applicationQuery);
    $applicationStmt->bind_param("sss", $email, $semester, $schoolYear);
    $applicationStmt->execute();
    $applicationResult = $applicationStmt->get_result();
?>

<!DOCTYPE html>
<html>
    <head>
        <?php include("../elements/meta.php"); ?>
        <title>Intern Portal</title>
        <?php include("embed.php"); ?>
        <link rel="stylesheet" href="../assets/css/new-style.css">
    </head>
    <body id="page-top">
        <div id="wrapper">
            <!-- Sidebar Wrapper -->
            <aside id="sidebar" class="expand">
                <?php include('../elements/stud_sidebar.php') ?>
            </aside>

            <div class="main">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 static-top shadow">
                    <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Applications</h4>
                    <!-- Topbar Navbar -->
                    <?php include('../elements/stud_navbar_user_info.php'); ?>
                </nav>
                
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Company</th>
                                        <th>Jobrole</th>
                                        <th>Application Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($applicationResult->num_rows > 0): ?>
                                        <?php while ($row = $applicationResult->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($row['companyName']) ?></td>
                                                <td><?= htmlspecialchars($row['jobrole']) ?></td>
                                                <td><?= htmlspecialchars($row['applicationDate'])?></td>
                                                <td><?= htmlspecialchars($row['status']) ?></td>
                                                <td>
                                                    <!-- Replace with your desired action, such as view/delete -->
                                                    <a href="#" class="btn btn-info btn-sm viewBtn" data-bs-target="#viewModal" data-bs-toggle="modal" data-companyname="<?= $row['companyName']; ?>" data-jobrole="<?= $row['jobrole']; ?>" data-applicationdate="<?= $row['applicationDate']; ?>" data-status="<?= $row['status']; ?>" data-id="<?= $row['id'] ?>">View</a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No applications found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- VIEW MODAL-->
        <div class="modal fade" role="dialog" tabindex="-1" id="viewModal" aria-labelledby="viewModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="col">
                            <h5 class="modal-title">Application Details</h5>
                            <p class="small" id="applicationDate"></p>
                        </div>
                        <button class="close" type="button" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col">
                                <label for="status">Status:</label>
                            </div>
                            <div class="col">
                                <p id="status"></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <label for="applicationID">Application ID:</label>
                            </div>
                            <div class="col">
                                <p id="applicationID"></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md">
                                <label for="companyName">Company Name:</label>
                            </div>
                            <div class="col-md">
                                <p id="companyName"></p>
                            </div>
                        </div>
                        <div>
                            <div class="row">
                                <div class="col-md">
                                    <label for="jobrole">Jobrole:</label>
                                </div>
                                <div class="col-md">
                                    <p id="jobrole"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
            crossorigin="anonymous"></script>
        <script src="../assets/js/sidebarscript.js"></script>

        <script>
            $(document).ready(function() {
                $('.viewBtn').on('click', function() {
                    const button = $(this);

                    // Get data from button attributes
                    const applicationID = button.data('id');
                    const companyName = button.data('companyname');
                    const jobrole = button.data('jobrole');
                    const applicationDate = button.data('applicationdate');
                    const status = button.data('status');

                    // Insert into modal fields
                    $('#applicationID').text(applicationID);
                    $('#companyName').text(companyName);
                    $('#jobrole').text(jobrole);
                    $('#applicationDate').text("Applied on: " + applicationDate);
                    $('#status').text(status);

                    // Open the modal
                    $('#viewModal').modal('show');
                });
            });
        </script>
    </body>
</html>