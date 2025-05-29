<?php
    include_once("../includes/connection.php");
    session_start();

    $activeSemester = $_SESSION['semester'];
    $activeSchoolYear = $_SESSION['schoolYear'];
    $companyName = $_SESSION['companyName'];

    $studQuery = "SELECT si.*, ci.*, si.trainerEmail AS si_trainerEmail, ci.trainerEmail AS ci_trainerEmail, ci.trainerContact AS ci_trainerContact
    FROM studentinfo si
    INNER JOIN company_info ci ON si.studentID = ci.studentID
    WHERE ci.companyName = ?
        AND ci.semester = ?
        AND ci.schoolYear = ?
        AND si.semester = ?
        AND si.school_year = ?
    ";

    $studStmt = $connect->prepare($studQuery);
    $studStmt->bind_param("sssss", $companyName, $activeSemester, $activeSchoolYear, $activeSemester, $activeSchoolYear);
    $studStmt->execute();
    $studResult = $studStmt->get_result();

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
            <aside id="sidebar" class="expand">
                <?php include('../elements/ip_sidebar.php') ?>
            </aside>

            <div class="main">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 static-top shadow">
                    <!-- Title -->
                    <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Intern Tracker</h4>
                    <!-- Topbar Navbar -->
                    <?php include('../elements/ip_navbar_user_info.php') ?>
                </nav>

                <div class="row m-1">
                    <div class="col-md-5 mb-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-dark">STUDENT INTERNS</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>

                                            </tr>
                                            <tr>
                                                <th class="small" scope="col">STUDENT NO.</th>
                                                <th class="small" scope="col">NAME</th>
                                                <th class="small" scope="col">COURSE-SECTION</th>
                                            </tr>
                                        </thead>
                                        <tbody style="max-height: 80vh; overflow-y: auto;">
                                            <?php if ($studResult->num_rows > 0): ?>
                                                <?php while ($row = $studResult->fetch_assoc()): ?>
                                                    <?php
                                                        // Check if any of the required fields are empty or null
                                                        $isIncomplete = empty($row['si_trainerEmail']) || empty($row['ci_trainerEmail']) || empty($row['ci_trainerContact']);

                                                        // Assign row class based on condition
                                                        $rowClass = $isIncomplete ? 'table-danger' : '';
                                                    ?>
                                                    <tr class="<?php echo $rowClass; ?>">
                                                        <td>
                                                            <a href="#" class="student-link" data-studentnumber="<?php echo htmlspecialchars($row['studentID']); ?>">
                                                                <?php echo htmlspecialchars($row['studentID']); ?>
                                                            </a>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['course'] . '-' . $row['section']); ?></td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="9" class="text-center">No students found.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-7 mb-4 p-lg-0" style="max-height: 90vh; overflow-y: auto;">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-dark">STUDENT INFO</h6>
                                <div id="assignTrainerContainer"></div>
                            </div>
                            <div class="card-body">
                                <div class="row m-1">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <h6>Total Rendered Hours:</h6>
                                        </div>
                                        <div class="col-md-3">
                                            <p class="font-weight-bold" id="totalHours"></p>
                                        </div>
                                        <div class="col-md-3">
                                            <h6>Required Hours:</h6>
                                        </div>
                                        <div class="col-md-2">
                                            <p class="font-weight-bold" id="requireHours"></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label for="studentName" class="small">Student Name:</label>
                                        </div>
                                        <div class="col-md-9">
                                            <p class="small font-weight-bold" id="studentName"></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label for="courseSection" class="small">Course-Section:</label>
                                        </div>
                                        <div class="col-md-9">
                                            <p class="small font-weight-bold" id="courseSection"></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label for="department" class="small">College:</label>
                                        </div>
                                        <div class="col-md-9">
                                            <p class="small font-weight-bold" id="department"></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label for="dateStarted" class="small">Date Started:</label>
                                        </div>
                                        <div class="col-md-2">
                                            <p class="small font-weight-bold" id="dateStarted"></p>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="dateEnded" class="small">Date Ended:</label>
                                        </div>
                                        <div class="col">
                                            <p class="small font-weight-bold" id="dateEnded"></p>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row m-1">
                                    <div class="col">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="jobrole" class="small">Jobrole:</label>
                                            </div>
                                            <div class="col-md-9">
                                                <p class="small font-weight-bold" id="jobrole"></p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="workType" class="small">Work Type:</label>
                                            </div>
                                            <div class="col-md-9">
                                                <p class="small font-weight-bold" id="worktype"></p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="trainerEmail" class="small">Trainer Email:</label>
                                            </div>
                                            <div class="col-md-9">
                                                <p class="small font-weight-bold" id="trainerEmail"></p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="trainerContact" class="small">Trainer Contact No.:</label>
                                            </div>
                                            <div class="col-md-9">
                                                <p class="small font-weight-bold" id="trainerContact"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <div class="row m-1">
                                    <ul class="nav nav-pills flex-column flex-sm-row" id="pills-tab" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active btn-sm" id="pills-dtr-tab" data-toggle="pill" href="#dtr-tab" role="tab" aria-controls="dtr-tab" aria-selected="true">DTR Logs</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link btn-sm" id="pills-documents-tab" data-toggle="pill" href="#documents-tab" role="tab" aria-controls="documents-tab" aria-selected="false">Documents</a>
                                        </li>
                                    </ul>
                                </div>

                                <div class="tab-content" id="pills-tabContent">
                                    <div class="tab-pane fade show active" id="dtr-tab" role="tabpanel" aria-labelledby="dtr-tab">
                                        <div class="table-responsive">
                                            <table class="table table-bordered" width="100%" cellspacing="0">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">Date</th>
                                                        <th scope="col">Time-in</th>
                                                        <th scope="col">Time-out</th>
                                                        <th scope="col">Total Hours</th>
                                                        <th scope="col" width="27%">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="documents-tab" role="tabpanel" aria-labelledby="documents-tab">
                                        <div class="table-responsive">
                                            <table class="table table-bordered" width="100%" cellspacing="0">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">Document</th>
                                                        <th scope="col">File Name</th>
                                                        <th scope="col">Status</th>
                                                        <th scope="col">Date Uploaded</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        
        <!-- ASSIGN TRAINER MODAL -->
        <div class="modal fade" id="assignTrainerModal" tabindex="-1" role="dialog" aria-labelledby="assignTrainerModal" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="assignTrainerModalLabel">Assign Trainer</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form id="assignTrainerForm">
                        <div class="modal-body">
                            <input type="hidden" name="studentID" id="studentID">
                            <div class="form-group md-5">
                                <div class="col-md">
                                    <div>
                                        <span>Trainer's Email</span>
                                    </div>
                                    <input type="email" class="form-control" name="trainerEmail" id="trainerEmail" placeholder="Enter Trainer Email" required>
                                </div>
                            </div>
                            <div class="form-group md-5">
                                <div class="col-md">
                                    <div>
                                        <span>Trainer's Contact Number</span>
                                    </div>
                                    <input type="number" class="form-control" name="trainerContact" id="trainerContact" placeholder="Enter Trainer Contact No." required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" type="submit">Submit</button>
                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Confirmation Modal -->
        <div class="modal fade" id="confirmAssignTrainerModal" tabindex="-1" role="dialog" aria-labelledby="confirmAssignTrainerModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Assignment</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to assign this trainer to the student?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" id="confirmAssignTrainerBtn" class="btn btn-primary">
                                <span class="spinner-border spinner-border-sm d-none" id="assignTrainerSpinner" role="status" aria-hidden="true"></span>
                                <span id="assignTrainerBtnText">Yes, Assign</span>
                            </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approve Modal -->
        <div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Approval</h5>
                        <button class="close" type="button" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to approve this log?
                        <input type="hidden" id="approveLogID">
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button class="btn btn-success" id="confirmApproveBtn">Yes, Approve</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Rejection</h5>
                        <button class="close" type="button" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to reject this log?
                        <input type="hidden" id="rejectLogID">
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button class="btn btn-danger" id="confirmRejectBtn">Yes, Reject</button>
                    </div>
                </div>
            </div>
        </div>

    </body>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).on('click', '.student-link', function (e) {
            e.preventDefault();
            const studentID = $(this).data('studentnumber');
            console.log('Clicked studentID:', studentID); // Debugging

            $.ajax({
                url: 'functions/student_tracker_info.php',
                method: 'GET',
                data: { studentID: studentID },
                dataType: 'json',
                success: function (response) {
                    console.log('AJAX response:', response); // Debugging

                    // Student Info
                    const student = response.student || {};
                    console.log('Student info:', student); // Debugging

                    $('#studentName').text(student.firstname + ' ' + student.middlename + ' ' + student.lastname + " | " + student.studentID);
                    $('#courseSection').text(student.course + '-' + student.section);
                    $('#department').text(student.department);
                    $('#dateStarted').text(student.dateStarted);
                    $('#dateEnded').text(student.dateEnded);
                    $('#jobrole').text(student.jobrole || 'N/A');
                    $('#worktype').text(student.workType);
                    $('#trainerEmail').text(student.trainerEmail || 'N/A');
                    $('#trainerContact').text(student.trainerContact || 'N/A');
                    $('#requireHours').text(student.hoursRequirement + ' Hours');

                    // Show "Assign Trainer" button if both email and contact are missing
                    if (!student.trainerEmail && !student.trainerContact) {
                        $('#assignTrainerContainer').html(`
                            <button class="btn btn-primary btn-sm assignTrainer" type="button" data-toggle="modal" data-target="#assignTrainerModal" data-id="${student.studentID}">
                                Assign Trainer
                            </button>
                        `);
                    } else {
                        $('#assignTrainerContainer').empty(); // Clear the container if trainer info exists
                    }

                    // Logs (DTR)
                    let dtrBody = '';
                    const logs = response.logs || [];
                    console.log('Log entries:', logs); // Debugging

                    let totalMinutesSum = 0; // total minutes accumulator

                    if (logs.length > 0) {
                        logs.forEach((log, index) => {
                            console.log(`Log ${index + 1}:`, log); // Debugging

                            const start = new Date(log.time_in);
                            const end = new Date(log.time_out);
                            let durationMs = end - start;
                            let totalMinutes = Math.floor(durationMs / 60000);

                            // Deduct break_minutes from totalMinutes
                            const breakMinutes = log.break_minutes ? parseInt(log.break_minutes) : 0;
                            totalMinutes -= breakMinutes;

                            // Prevent negative durations
                            if (totalMinutes < 0) totalMinutes = 0;

                            totalMinutesSum += totalMinutes;

                            const hours = Math.floor(totalMinutes / 60);
                            const minutes = totalMinutes % 60;
                            const formattedDuration = `${hours} hrs ${minutes} mins`;


                            let actionButtons = '';

                            if (log.is_approved === 'Approved') {
                                actionButtons = `
                                    <button class="btn btn-success btn-sm approve-log" data-logid="${log.id}" disabled>Approved</button>
                                    <button class="btn btn-danger btn-sm reject-log" data-logid="${log.id}" data-toggle="modal" data-target="#rejectModal">Reject</button>
                                `;
                            } else if (log.is_approved === 'Rejected') {
                                actionButtons = `
                                    <button class="btn btn-success btn-sm approve-log" data-logid="${log.id}" data-toggle="modal" data-target="#approveModal">Approve</button>
                                    <button class="btn btn-danger btn-sm reject-log" data-logid="${log.id}" disabled>Rejected</button>
                                `;
                            } else {
                                actionButtons = `
                                    <button class="btn btn-success btn-sm approve-log" data-logid="${log.id}" data-toggle="modal" data-target="#approveModal">Approve</button>
                                    <button class="btn btn-danger btn-sm reject-log" data-logid="${log.id}" data-toggle="modal" data-target="#rejectModal">Reject</button>
                                `;
                            }

                            dtrBody += `
                                <tr>
                                    <td>${log.date}</td>
                                    <td>${log.time_in}</td>
                                    <td>${log.time_out}</td>
                                    <td>${formattedDuration}</td>
                                    <td id="action-buttons-${log.id}">
                                        ${actionButtons}
                                    </td>
                                </tr>
                            `;
                        });
                    } else {
                        console.warn('No DTR logs found.');
                        dtrBody = `<tr><td colspan="5" class="text-center">No DTR logs found.</td></tr>`;
                    }
                    $('#dtr-tab tbody').html(dtrBody);

                    // Format total hours and minutes from totalMinutesSum
                    const totalHours = Math.floor(totalMinutesSum / 60);
                    const totalMins = totalMinutesSum % 60;
                    $('#totalHours').text(response.totalRendered ?? '0');

                    // Documents
                    let docBody = '';
                    const documents = response.documents || [];
                    console.log('Documents:', documents); // Debugging

                    if (documents.length > 0) {
                        documents.forEach((doc, i) => {
                            console.log(`Document ${i + 1}:`, doc); // Debugging

                            docBody += `
                                <tr>
                                    <td>${doc.document || 'N/A'}</td>
                                    <td>
                                        ${doc.file_name && doc.file_link
                                        ? `<a href="${doc.file_link}" target="_blank" rel="noopener noreferrer">${doc.file_name}</a>`
                                        : 'N/A'}
                                    </td>
                                    <td>${doc.status}</td>
                                    <td>${doc.date}</td>
                                </tr>
                            `;
                        });
                    } else {
                        console.warn('No documents found.');
                        docBody = `<tr><td colspan="4" class="text-center">No documents found.</td></tr>`;
                    }
                    $('#documents-tab table tbody').html(docBody);
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    console.error('Response Text:', xhr.responseText);
                    alert('Failed to fetch student info.');
                }
            });
        });
    </script>

    <script>
        let assignTrainerFormData = null;

        $(document).ready(function () {
            // Handle Assign Trainer button click
            $(document).on('click', '.assignTrainer', function () {
                const studentID = $(this).data('id');
                $('#studentID').val(studentID);
                $('#assignTrainerModal').modal('show');
            });

            // Intercept the form submission to show confirmation modal
            $('#assignTrainerForm').on('submit', function (e) {
                e.preventDefault();
                assignTrainerFormData = $(this).serialize();
                $('#confirmAssignTrainerModal').modal('show');
            });

            // Handle confirmation modal "Yes, Assign" button
            $('#confirmAssignTrainerBtn').on('click', function () {
                // Show loading state
                $('#assignTrainerSpinner').removeClass('d-none');
                $('#assignTrainerBtnText').text('Assigning...');
                $(this).prop('disabled', true);

                $.ajax({
                    url: 'functions/assign_trainer.php',
                    type: 'POST',
                    data: assignTrainerFormData,
                    success: function (response) {
                        $('#assignTrainerModal').modal('hide');
                        $('#assignTrainerForm')[0].reset();
                        location.reload();
                    },
                    error: function (xhr, status, error) {
                        alert('Failed to assign trainer. Please try again.');
                        console.error('AJAX Error:', status, error);
                    },
                    complete: function () {
                        // Reset loading state
                        $('#assignTrainerSpinner').addClass('d-none');
                        $('#assignTrainerBtnText').text('Yes, Assign');
                        $('#confirmAssignTrainerBtn').prop('disabled', false);
                        $('#confirmAssignTrainerModal').modal('hide');
                    }
                });
            });

            // Reset Approve button when modal is shown
            $('#approveModal').on('show.bs.modal', function () {
                $('#confirmApproveBtn').html('Yes, Approve');
            });

            // Reset Reject button when modal is shown
            $('#rejectModal').on('show.bs.modal', function () {
                $('#confirmRejectBtn').html('Yes, Reject');
            });

            // Store logID in modals
            $(document).on('click', '.approve-log', function () {
                const logID = $(this).data('logid');
                const studentID = $(this).data('studentid');
                $('#approveLogID').val(logID);
                $('#approveModal').modal('show');
            });

            $(document).on('click', '.reject-log', function () {
                const logID = $(this).data('logid');
                const studentID = $(this).data('studentid');
                $('#rejectLogID').val(logID);
                $('#rejectModal').modal('show');
            });

            // Approve Log
            $('#confirmApproveBtn').on('click', function () {
                const logID = $('#approveLogID').val();
    
                $.ajax({
                    url: 'functions/approve_dtr.php',
                    method: 'POST',
                    data: { logID },
                    dataType: 'json',
                    beforeSend: function () {
                        $('#confirmApproveBtn').html('<span class="spinner-border spinner-border-sm"></span> Approving...');
                    },
                    success: function (res) {
                        $('#approveModal').modal('hide');
                        if (res.success) {
                            $(`#action-buttons-${logID}`).html(`
                                <button class="btn btn-success btn-sm" disabled>Approved</button>
                                <button class="btn btn-danger btn-sm reject-log" data-logid="${logID}" data-toggle="modal" data-target="#rejectModal">Reject</button>
                            `);
                            Swal.fire('Approved!', res.message, 'success');
                        } else {
                            Swal.fire('Error!', res.message, 'error');
                        }
                    },
                    error: function () {
                        $('#approveModal').modal('hide');
                        Swal.fire('Error!', 'Failed to approve log.', 'error');
                    },
                    complete: function () {
                        $('#confirmApproveBtn').html('Yes, Approve');
                    }
                });
            });

            // Reject Log
            $('#confirmRejectBtn').on('click', function () {
                const logID = $('#rejectLogID').val();
    
                $.ajax({
                    url: 'functions/reject_dtr.php',
                    method: 'POST',
                    data: { logID },
                    dataType: 'json',
                    beforeSend: function () {
                        $('#confirmRejectBtn').html('<span class="spinner-border spinner-border-sm"></span> Rejecting...');
                    },
                    success: function (res) {
                        $('#rejectModal').modal('hide');
                        if (res.success) {
                            $(`#action-buttons-${logID}`).html(`
                                <button class="btn btn-success btn-sm approve-log" data-logid="${logID}" data-toggle="modal" data-target="#approveModal">Approve</button>
                                <button class="btn btn-danger btn-sm" disabled>Rejected</button>
                            `);
                            Swal.fire('Rejected!', res.message, 'success');
                        } else {
                            Swal.fire('Error!', res.message, 'error');
                        }
                    },
                    error: function () {
                        $('#rejectModal').modal('hide');
                        Swal.fire('Error!', 'Failed to reject log.', 'error');
                    },
                    complete: function () {
                        $('#confirmRejectBtn').html('Yes, Reject');
                    }
                });
            });

        });
    </script>

</html>