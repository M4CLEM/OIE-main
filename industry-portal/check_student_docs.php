<?php
session_start();
include_once("../includes/connection.php");

$IP = $_SESSION['IndustryPartner'];

$query = "SELECT * FROM studentinfo WHERE trainerEmail = '$IP'";
$result = mysqli_query($connect, $query);

$studentIDs = array();

while ($row = mysqli_fetch_assoc($result)) {
    $studentIDs[] = $row['studentID'];
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("../elements/meta.php"); ?>
    <title>Industry Partner Portal</title>
    <?php include("embed.php"); ?>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!--Sidebar Wrapper-->
        <aside id="sidebar" class="expand">
            <?php include('../elements/ip_sidebar.php') ?>
        </aside>

        <div class="main">

            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 static-top shadow">

                <!-- Title -->
                <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Documents</h4>

                <!-- Topbar Navbar -->
                <ul class="navbar-nav ml-auto">

                    <div class="topbar-divider d-none d-sm-block"></div>

                    <!-- Nav Item - User Information -->
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                <?php (isset($_SESSION['IndustryPartner'])) ?> <?php echo $_SESSION['IndustryPartner']; ?></span>
                            <img class="img-profile rounded-circle" src="../img/undraw_profile.svg">
                        </a>
                        <!-- Dropdown - User Information -->
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                Profile
                            </a>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                Settings
                            </a>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                Activity Log
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="../logout.php" data-toggle="modal" data-target="#logoutModal">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                Logout
                            </a>
                        </div>
                    </li>
                </ul>
            </nav>
            <!-- End of Topbar -->

            <div class="row m-1">
                <!-- Begin Page Content -->
                <div class="col-md-5 mb-4">
                    <!-- Illustrations -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-dark">STUDENT INTERNS</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table" id="dataTable2" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="small">STUDENT NO.</th>
                                            <th scope="col" class="small">NAME</th>
                                            <th scope="col" class="small">YEAR LEVEL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (!empty($studentIDs)) {
                                            $studentIDsString = implode(',', $studentIDs);

                                            $studentQuery = "SELECT * FROM student_masterlist WHERE studentID IN ($studentIDsString) ORDER BY lastName ASC";
                                            $studentResult = mysqli_query($connect, $studentQuery);
                                            
                                            while ($studentRow = mysqli_fetch_assoc($studentResult)) {
                                        ?>
                                                <tr>
                                                    <td class="small"><a href="" class="info-link" data-section="<?php echo $studentRow['studentID']; ?>"><?php echo $studentRow['studentID']; ?></a></td>
                                                    <td class="small"><?php echo $studentRow['lastName'] . ', ' . $studentRow['firstName']; ?></td>
                                                    <td class="small"><?php echo $studentRow['year']; ?></td>
                                                </tr>
                                            <?php
                                            }
                                        } else {
                                            ?>
                                            <tr>
                                                <td colspan="4" class="text-center small">No associated students</td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- card right column -->
                <div class="col-md-7 mb-4 p-lg-0">
                    <!-- Illustrations -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-dark">STUDENT INFORMATION</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <div>
                                        <label for="stud_id" class="small">Student No.:</label>
                                        <p class="small font-weight-bold" id="stud_id"></p>
                                    </div>
                                    <div>
                                        <label for="surname" class="small">Surname:</label>
                                        <p class="small font-weight-bold" id="surname"></p>
                                    </div>
                                </div>
                                <div class="col">
                                    <div>
                                        <label for="section" class="small">Section:</label>
                                        <p class="small font-weight-bold" id="section"></p>
                                    </div>
                                    <div>
                                        <label for="firstName" class="small">First Name:</label>
                                        <p class="small font-weight-bold" id="firstName"></p>
                                    </div>
                                </div>
                                <div class="col">
                                    <div>
                                        <label for="program" class="small">Program:</label>
                                        <p class="small font-weight-bold" id="program"></p>
                                    </div>
                                    <div>
                                        <label for="midName" class="small">Middle Name:</label>
                                        <p class="small font-weight-bold" id="midName"></p>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <div class="table-responsive">
                                    <table class="table " id="docsTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th colspan="5" class="text-center small font-weight-bold border-0">DOCUMENTS</th>
                                            </tr>
                                            <tr>
                                                <th colspan="1" class="text-center font-weight-bold border-0 pt-1">
                                                    <button id="approveSelected" class="btn btn-primary btn-sm btn-success" title="Approve Selected"><i class="fas fa-thumbs-up"></i></button>
                                                </th>
                                                <th colspan="4" class="text-center font-weight-bold border-0 pt-1">
                                                    <input type="text" class="form-control form-control-sm" readonly id="searchInput" placeholder="Search...">
                                                </th>
                                            </tr>
                                            <tr>
                                                <th class="text-center">
                                                    <div class="form-check">
                                                        <input class="form-check-input" style='transform: scale(1.5);' type="checkbox" id="selectAll">
                                                    </div>
                                                </th>
                                                <th scope="col" class="small text-center">DOCUMENT</th>
                                                <th scope="col" class="small text-center">FILE NAME</th>
                                                <th scope="col" class="small text-center" title="Date Submitted"><i class="fas fa-calendar-alt"></i></th>
                                                <th scope="col" class="small text-center">STATUS</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="5" class="text-center small">Student's Submitted OJT Documents will appear here</td>
                                            </tr>
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



    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="../logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
<script src="../assets/js/sidebarscript.js"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script>
    function viewPDF(pdfPath) {
        // Open the PDF in a new tab/window
        window.open(pdfPath, '_blank');
    }

    var isMultiApprovalInProgress = false;

    $(document).ready(function() {
        $('#searchInput').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('#docsTable tbody tr').filter(function() {
                var rowText = $(this).text().toLowerCase();
                var isVisible = rowText.indexOf(value) > -1;
                $(this).toggle(isVisible);
            });

            // Check if any rows are visible after filtering
            var visibleRows = $('#docsTable tbody tr:visible').length;
            if (visibleRows === 0) {
                $('#noResult').show(); // Display "No Results" message
            } else {
                $('#noResult').hide(); // Hide "No Results" message if there are visible rows
            }
        });

        // Disable the "Approve Selected" button initially
        $('#approveSelected').prop('disabled', true);

        // Function to toggle the "Approve Selected" button state
        function toggleApproveButton() {
            // Check if any checkbox in the tbody is checked
            var anyChecked = $('.checkbox-highlight:checked').length > 0;

            // Enable/disable the "Approve Selected" button based on checkbox state
            $('#approveSelected').prop('disabled', !anyChecked);
        }

        // Function to toggle highlight for checked rows
        function toggleHighlight() {
            // Remove table-success class from all rows in the tbody
            $('#docsTable tbody tr').removeClass('table-success');

            // Add table-success class to checked rows
            $('.checkbox-highlight:checked').closest('tr').addClass('table-success');
        }

        // Listen for changes in the checkboxes' state
        $(document).on('change', '.checkbox-highlight', function() {
            // Toggle the "Approve Selected" button state
            toggleApproveButton();

            // Toggle highlight for checked rows
            toggleHighlight();
        });

        // Handle multi-approval button click
        $('#approveSelected').click(function() {
            isMultiApprovalInProgress = true;
            // Collect selected document IDs
            var selectedDocuments = [];
            $('.checkbox-highlight:checked').each(function() {
                selectedDocuments.push($(this).val());
            });

            // AJAX call to update the status of selected documents
            $.ajax({
                url: 'functions/multi_approve.php', // Path to PHP script that handles multi-approval
                method: 'POST',
                data: {
                    documentIDs: selectedDocuments
                },
                success: function(response) {
                    // Handle success response
                    console.log(response);
                    // Refresh table or do any necessary updates

                    // Update status and apply background color immediately
                    $('.checkbox-highlight:checked').each(function() {
                        var documentId = $(this).val();
                        $('#status_' + documentId).val('approved').change(); // Change status
                        $('#status_' + documentId).removeClass('bg-warning bg-danger bg-secondary').addClass('bg-success'); // Apply background color
                    });

                    // Display success notification using SweetAlert
                    Swal.fire({
                        icon: 'success',
                        title: 'Documents Approved Successfully',
                        position: 'top',
                        toast: true,
                        showConfirmButton: false,
                        timer: 2000
                    });
                    isMultiApprovalInProgress = false;
                    $('#approveSelected').prop('disabled', true);
                },
                error: function(xhr, status, error) {
                    // Handle error
                    console.error('Error:', error);

                    // Display error notification using SweetAlert
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed to Approve Documents',
                        text: 'An error occurred while processing your request.',
                        position: 'top',
                        toast: true,
                        showConfirmButton: false,
                        timer: 2000
                    });
                    isMultiApprovalInProgress = false;
                    $('#approveSelected').prop('disabled', true);
                }
            });
        });

        // Listen for changes in the "Select All" checkbox state
        $('#selectAll').change(function() {
            // Filter out disabled checkboxes
            var enabledCheckboxes = $('.checkbox-highlight:not(:disabled)');

            // Set the checked state for enabled checkboxes
            enabledCheckboxes.prop('checked', $(this).prop('checked'));

            // Toggle button state and highlight for enabled checkboxes
            toggleApproveButton();
            toggleHighlight();
        });


        // Function to fetch and update student information
        function fetchAndUpdateStudentInfo(studentID) {
            // AJAX call to fetch student information based on studentID
            $.ajax({
                url: 'functions/fetch_stud_info.php', // Provide the path to your PHP script that fetches student information
                method: 'POST',
                data: {
                    studentID: studentID
                },
                success: function(response) {
                    // Check if the response is an empty array (not registered yet)
                    if (response.trim() === '[]') {
                        // Show SweetAlert indicating that the student is not registered yet
                        Swal.fire({
                            icon: 'warning',
                            title: 'Student Not Registered',
                            text: 'The student with the provided ID has no information registered.',
                            showConfirmButton: false,
                            toast: true,
                            position: 'top',
                            timer: 2000
                        });
                        $('#stud_id').text('');
                        $('#surname').text('');
                        $('#firstName').text('');
                        $('#midName').text('');
                        $('#section').text('');
                        $('#program').text('');
                    } else {
                        // Parse the response as JSON
                        var studentInfo = JSON.parse(response);
                        // Update the student information fields with the fetched data
                        $('#stud_id').text(studentInfo.studentID);
                        $('#surname').text(studentInfo.lastName);
                        $('#firstName').text(studentInfo.firstName);
                        $('#midName').text(studentInfo.middleName);
                        $('#section').text(studentInfo.section);
                        $('#program').text(studentInfo.program);
                    }
                }
            });
        }

        function fetchStudentDocuments(studentID) {
            // AJAX call to fetch documents based on studentID
            $.ajax({
                url: 'functions/fetch_stud_docs.php', // Path to PHP script that fetches documents
                method: 'POST',
                data: {
                    studentID: studentID
                },
                success: function(response) {
                    // Update the docsTable tbody with the fetched documents
                    $('#docsTable tbody').html(response);

                    // Check if the table contains the "No documents found" message
                    if ($('#docsTable tbody').text().trim() === "No documents found for this student.") {
                        // Make the search bar readonly if no documents are found
                        $('#searchInput').prop('readonly', true);
                    } else {
                        // Display the search bar if there are documents
                        $('#searchInput').prop('readonly', false);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching documents:', error);
                    // Display an error message or handle the error as needed

                    // Make the search bar readonly in case of an error
                    $('#searchInput').prop('readonly', true);
                }
            });
        }

        // Event listener for student links
        $(document).on('click', '.info-link', function(e) {
            e.preventDefault();
            var studentID = $(this).data('section');
            //console.log(studentID);
            // Show SweetAlert2 alert with loading animation
            Swal.fire({
                title: 'Please Wait...',
                showConfirmButton: false,
                position: 'top',
                toast: true,
                willOpen: () => {
                    Swal.showLoading();
                },
                didOpen: () => {
                    setTimeout(() => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Student Information Loaded',
                            position: 'top',
                            toast: true,
                            showConfirmButton: false,
                            timer: 2000
                        });
                        fetchAndUpdateStudentInfo(studentID);
                        fetchStudentDocuments(studentID)
                    }, 2000); // Simulated loading delay of 2 seconds
                }
            });
        });

        $('#dataTable').DataTable();
    });

    function toggleCheckbox(selectElement) {
        var checkbox = document.getElementById('flexCheck' + selectElement.dataset.id);
        var tableRow = checkbox.closest('tr'); // Find the parent table row

        // Remove the "table-success" class from the parent table row
        tableRow.classList.remove('table-success');

        // Uncheck the checkbox before disabling it
        checkbox.checked = false;

        // Disable the checkbox if the selectElement value is 'approved', otherwise enable it
        checkbox.disabled = (selectElement.value.toLowerCase() === 'approved');

        // Uncheck the #selectAll checkbox if any checkbox is unchecked
        if (!checkbox.checked) {
            $('#selectAll').prop('checked', false);
        }
    }

    function updateStatus(selectElement) {
        if (isMultiApprovalInProgress) {
            // If multi-approval is in progress, do nothing
            return;
        }

        var documentId = selectElement.dataset.id;
        var newStatus = selectElement.value;

        var xhr = new XMLHttpRequest();

        xhr.open('POST', 'functions/update_status.php', true);

        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.send(`documentId=${encodeURIComponent(documentId)}&status=${encodeURIComponent(newStatus)}`);

        selectElement.classList.remove('bg-warning', 'bg-success', 'bg-danger', 'bg-secondary');

        var newStatus = selectElement.value.toLowerCase();

        switch (newStatus) {
            case 'pending':
                selectElement.classList.add('bg-warning');
                break;
            case 'approved':
                selectElement.classList.add('bg-success');
                break;
            case 'rejected':
                selectElement.classList.add('bg-danger');
                break;
            default:
                selectElement.classList.add('bg-secondary');
                break;
        }

        xhr.onload = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                console.log(response.document + ' updated successfully');
                Swal.fire({
                    icon: 'success',
                    title: response.document.replace(/_/g, '/') + ' Status Updated',
                    position: 'top',
                    toast: true,
                    showConfirmButton: false,
                    timer: 2000
                });
            } else {
                console.error('An error occurred during the transaction');
                Swal.fire({
                    icon: 'error',
                    title: 'Unable to Update ' + response.document + ' Status at this time',
                    position: 'top',
                    toast: true,
                    showConfirmButton: false,
                    timer: 2000
                });
            }
        };
    }
</script>

</html>