<?php
session_start();
include_once("../includes/connection.php");
$query = "select * from department_list";
$result = mysqli_query($connect, $query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("../elements/meta.php"); ?>
    <title>CIPA Portal</title>
    <?php include("embed.php"); ?>
    <link rel="stylesheet" href="../assets/css/new-style.css">
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!--Sidebar Wrapper-->
        <aside id="sidebar" class="expand">
            <?php include('../elements/cipa_sidebar.php') ?>
        </aside>

        <div class="main">

            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 static-top shadow">

                <!-- Title -->
                <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Student Documents</h4>
                <!-- Topbar Navbar -->
                <?php include('../elements/cipa_navbar_user_info.php') ?>
            </nav>
            <!-- End of Topbar -->

            <div class="row m-1">
                <!-- Begin Page Content -->
                <div class="col-md-6 mb-4">
                    <!-- Illustrations -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-dark">DEPARTMENTS/SECTIONS</h6>
                            <ul class="nav nav-pills flex-column flex-sm-row" id="pills-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active btn-sm" id="pills-section-tab" data-toggle="pill" href="#section-tab" role="tab" aria-controls="section-tab" aria-selected="true">Sections</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link btn-sm" id="pills-students-tab" data-toggle="pill" href="#students-tab" role="tab" aria-controls="students-tab" aria-selected="false">Students</a>
                                </li>
                            </ul>
                        </div>

                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="section-tab" role="tabpanel" aria-labelledby="section-tab">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover" id="collegeTable" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <div class="pb-3">
                                                        <select name="colleges" id="collegeSelect" class="form-select form-select-sm">
                                                            <option value="" selected>Select College</option>
                                                            <?php
                                                            while ($row = mysqli_fetch_assoc($result)) {
                                                                echo '<option value="' . $row['department'] . '">' . $row['department'] . ' - ' . $row['department_title'] . '</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </tr>
                                                <tr>
                                                    <th scope="col" class="small">Section</th>
                                                    <th width="11%" align="center" class="small">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="2" class="text-center small">Sections from Selected Colleges will Appear Here</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="students-tab" role="tabpanel" aria-labelledby="students-tab">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover" id="dataTable2" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th scope="col" class="small"></th>
                                                    <th scope="col" class="small">STUDENT NUMBER</th>
                                                    <th scope="col" class="small">NAME</th>
                                                    <th scope="col" class="small">YEAR LEVEL</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="4" class="text-center small">Loaded Masterlists will appear here</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- card right column -->
                <div class="col-md-6 mb-4 p-lg-0">
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
                                    <table class="table table-hover" id="docsTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th colspan="4" class="text-center small font-weight-bold border-0">DOCUMENTS</th>
                                            </tr>
                                            <tr>
                                                <th colspan="4" class="text-center font-weight-bold border-0 pt-1"><input type="text" class="form-control form-control-sm" readonly id="searchInput" placeholder="Search..."></th>
                                            </tr>
                                            <tr>
                                                <th scope="col" class="small text-center">DOCUMENT</th>
                                                <th scope="col" class="small text-center">FILE NAME</th>
                                                <th scope="col" class="small text-center">DATE SUBMITTED</th>
                                                <th scope="col" class="small text-center">STATUS</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="4" class="text-center small">Student's Submitted OJT Documents will appear here</td>
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

    $(document).ready(function() {
        $(document).ready(function() {
            $('#collegeSelect').change(function() {
                var college = $(this).val();
                if (college) {
                    $.ajax({
                        url: 'functions/fetch_sections.php', // Path to your PHP script that fetches sections
                        method: 'POST',
                        data: {
                            college: college
                        },
                        success: function(response) {
                            // Insert the HTML directly into the table body
                            $('#collegeTable tbody').html(response);
                        }
                    });
                }
            });
        });
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
        $(document).on('click', '.section-link', function(e) {
            e.preventDefault();
            var section = $(this).data('section');

            $.ajax({
                url: 'functions/fetch_masterlist.php',
                method: 'POST',
                data: {
                    section: section
                },
                success: function(response) {
                    // Destroy existing DataTable
                    if ($.fn.DataTable.isDataTable('#dataTable2')) {
                        $('#dataTable2').DataTable().clear().destroy();
                    }
                    // Clear table body
                    $('#dataTable2 tbody').empty();

                    // After successfully loading data, call the loadMasterList function
                    loadMasterList(section, response);
                }
            });
        });

        function loadMasterList(section, response) {
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
                            title: section + ' Masterlist Loaded',
                            position: 'top',
                            toast: true,
                            showConfirmButton: false,
                            timer: 2000
                        });

                        // Check if the response indicates no rows
                        if (response.trim() === "<tr><td colspan='4' class='small text-center'>No students found for this section.</td></tr>") {
                            $('#dataTable2 tbody').html(response);
                            $('#searchInput').prop('readonly', true);
                            // Do not initialize DataTables if no rows are returned
                            return;
                        } else {
                            // Display the search bar if there are documents
                            $('#searchInput').prop('readonly', false);
                        }

                        // Replace table data with new response
                        $('#dataTable2 tbody').html(response);

                        // Check if DataTables has already been initialized on the element
                        if ($.fn.DataTable.isDataTable('#dataTable2')) {
                            // If DataTables is already initialized, clear and destroy it
                            $('#dataTable2').DataTable().clear().destroy();
                        }

                        // Initialize DataTables
                        $('#dataTable2').DataTable();
                    }, 2000); // Simulated loading delay of 2 seconds
                }
            });
        }

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
</script>

</html>