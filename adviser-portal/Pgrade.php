<?php
session_start();
include_once("../includes/connection.php");

$semester = $_SESSION['semester'];
$schoolYear = $_SESSION['schoolYear'];

$getSections = "SELECT section FROM listadviser WHERE email = '{$_SESSION['adviser']}'";
$sectionsResult = mysqli_query($connect, $getSections);

$sections = [];
while ($row = mysqli_fetch_assoc($sectionsResult)) {
    $sections[] = $row['section'];
}
$sectionsString = implode("','", $sections);
$query = "SELECT DISTINCT * FROM studentinfo WHERE section IN ('$sectionsString') AND semester = '$semester' AND school_year = '$schoolYear' AND status = 'Deployed' ORDER BY section ASC, lastname ASC";

$result = mysqli_query($connect, $query);
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
                <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Performance Grade</h4>

                <!-- Topbar Navbar -->
                <ul class="navbar-nav ml-auto">

                    <div class="topbar-divider d-none d-sm-block"></div>

                    <!-- Nav Item - User Information -->
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                <?php (isset($_SESSION['adviser'])) ?> <?php echo $_SESSION['adviser']; ?></span>
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

            <div class="col-lg-12 mb-4">

                <!-- Illustrations -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <div class="row justify-content-between align-items-center">
                            <div class="col-md-6">
                                <h6 class="font-weight-bold text-dark">INPUT GRADE</h6>
                            </div>
                            <div class="col-md-6">
                                <div class="row justify-content-end align-items-center">
                                    <div class="col-md-3">
                                        <button id="composeButton" class="btn btn-primary btn-sm mr-6" onclick="openComposeModal()">
                                            Compose <i class="fa fa-envelope" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                    <div class="col-md-9">
                                    <?php
                                        $email = $_SESSION['adviser'];
                                        $getsections = "SELECT section FROM listadviser WHERE email = '$email' AND semester = '$semester' AND schoolYear = '$schoolYear'";
                                        $sections = mysqli_query($connect, $getsections);

                                        if ($sections) {
                                            echo '<select name="sections" id="sections" class="form-control form-control-sm" onchange="filterTable(this.value)">';
    
                                            // "All Sections" option at the top
                                            echo '<option value="All">All Sections</option>';

                                            $addedSections = []; // Track unique section entries

                                            while ($sect = mysqli_fetch_assoc($sections)) {
                                                $sectionList = explode(',', $sect['section']);

                                                foreach ($sectionList as $singleSection) {
                                                    $singleSection = trim($singleSection);
                                                    if (!in_array($singleSection, $addedSections) && $singleSection !== '') {
                                                        $addedSections[] = $singleSection;
                                                        echo '<option value="' . $singleSection . '">' . $singleSection . '</option>';
                                                    }
                                                }
                                            }

                                            echo '</select>';
                                        } else {
                                            echo "Error: " . mysqli_error($connect);
                                        }
                                    ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="gradeTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th colspan="5" class="text-center font-weight-bold border-0 pt-1"><input type="text" class="form-control form-control-sm" id="searchInput" placeholder="Search..."></th>
                                    </tr>
                                    <tr>
                                        <th width="11%" class="text-center">Action</th>
                                        <th scope="col">#</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Section</th>
                                        <th scope="col">Year</th>
                                        <!--<th scope="col">Sense of Urgency</th>
                                <th scope="col">Quality of Work</th>
                                <th scope="col">Execution Concept</th>
                                <th scope="col">Promptness and Punctuality</th>
                                <th scope="col">Work Ethics</th>
                                <th scope="col">Demeanor</th> 
                                <th scope="col">Final Grade</th>
                                <th scope="col">Remarks</th> !-->

                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                        <!-- Dynamic Masterlist goes here, Do not put anything here!-->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

    <!-- Email Compose Modal -->
    <div class="modal fade" id="composeEmailModal" tabindex="-1" role="dialog" aria-labelledby="composeEmailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="composeEmailModalLabel">Compose Email</h5>
                    <button class="close" type="button" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="composeEmailForm" action="grading_email.php" method="post">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="recipient-email">Recipient Email:</label>
                            <input type="email" class="form-control" id="recipient-email" placeholder="recipient@example.com" name="recipient-email" required readonly>
                        </div>
                        <div class="form-group">
                            <label for="sender">Sender Name:</label>
                            <input class="form-control" id="sender" placeholder="eg. (Juan Dela Cruz)" name="sender" required>
                        </div>
                        <div class="form-group">
                            <label for="sender-email">Sender Email:</label>
                            <input class="form-control" id="sender-email" placeholder="senderemail@email.com" name="sender-email" required>
                        </div>
                        <div class="form-group">
                            <label for="email-subject">Subject:</label>
                            <input type="text" class="form-control" id="email-subject" placeholder="Subject" name="email-subject" required>
                        </div>
                        <div class="form-group">
                            <label for="students">Students:</label>
                            <textarea class="form-control" id="students" rows="8" placeholder="Students Here" name="students" readonly></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-primary" type="submit">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Compose Email Loading Modal -->
    <div class="modal fade" id="loadingModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-center p-4">
                <div class="modal-body d-flex flex-column align-items-center justify-content-center" style="min-height: 200px;">
                    <div class="spinner-border text-success" role="status" style="width: 4rem; height: 4rem;"></div>
                    <h5 class="text-success mt-3">Please Wait...</h5>
                    <p>Sending email...</p>
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

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
            <script src="../assets/js/sidebarscript.js"></script>

            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                $(document).ready(function() {
                    $('#composeEmailForm').on('submit', function(event) {
                        event.preventDefault(); // Prevent default form submission
                
                        // Show loading modal
                        var loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
                        loadingModal.show();
                
                        $.ajax({
                            url: 'grading_email.php',
                            type: 'POST',
                            data: $(this).serialize(),
                            success: function(response) {
                                loadingModal.hide(); // Hide loading modal
                                var composeModal = bootstrap.Modal.getInstance(document.getElementById('composeEmailModal'));
                                composeModal.hide(); // Hide compose email modal
                            },
                            error: function() {
                                loadingModal.hide(); // Hide loading modal
                                alert('Failed to send email. Please try again.');
                            }
                        });
                    });

                    // Function to handle clicking on the "Compose Email" button
                    $('#composeButton').click(function() {
                        var checkedCheckboxes = $('.checkbox-highlight:checked');

                        if (checkedCheckboxes.length > 0) {
                            var studentsData = [];

                            // Loop through each checked checkbox to gather data
                            checkedCheckboxes.each(function() {
                                var row = $(this).closest('tr');
                                var studentID = row.find('td:eq(1)').text();
                                var fullName = row.find('td:eq(2)').text();
                                var section = row.find('td:eq(3)').text();

                                // Push data for each student as a single string
                                studentsData.push(studentID + ' ' + fullName + ' ' + section);
                            });

                            // Join all student data strings with newline character
                            var studentsDataString = studentsData.join('\n');

                            // Populate form fields
                            $('#email-subject').val('PLMUN OJT GRADING');
                            $('#students').val(studentsDataString);

                            // Show the modal
                            $('#composeEmailModal').modal('show');
                        } else {
                            alert('Please select at least one student.');
                        }
                    });
                    $('#composeEmailModal').on('hidden.bs.modal', function(e) {
                        $('.modal-backdrop').hide(); // Manually hide the modal backdrop
                    });

                    // Function to handle highlighting effect
                    $(document).on('change', '.checkbox-highlight', function() {
                        if ($(this).prop('checked')) {
                            $(this).closest('tr').addClass('table-success');
                        } else {
                            $(this).closest('tr').removeClass('table-success');
                        }
                    });
                    // Function to update button state
                    function updateButtonState() {
                        // Get all checkboxes
                        var checkboxes = document.querySelectorAll('.form-check-input');

                        // Get the button
                        var composeButton = document.getElementById('composeButton');

                        // Check if any checkbox is checked
                        var isChecked = Array.from(checkboxes).some(function(checkbox) {
                            return checkbox.checked;
                        });

                        // Enable/disable the button based on whether any checkbox is checked
                        composeButton.disabled = !isChecked;
                    }

                    // Add event listener to the body for change events on checkboxes
                    $('body').on('change', '.form-check-input', updateButtonState);

                    // Function to filter table based on search input
                    $('#searchInput').on('keyup', function() {
                        var value = $(this).val().toLowerCase();
                        $('#gradeTable tbody tr').filter(function() {
                            var rowText = $(this).text().toLowerCase();
                            var isVisible = rowText.indexOf(value) > -1;
                            $(this).toggle(isVisible);
                        });

                        // Check if any rows are visible after filtering
                        var visibleRows = $('#gradeTable tbody tr:visible').length;
                        if (visibleRows === 0) {
                            $('#noResult').show(); // Display "No Results" message
                        } else {
                            $('#noResult').hide(); // Hide "No Results" message if there are visible rows
                        }

                        // Update button state after filtering
                        updateButtonState();
                    });

                    // Function to fetch and update table data based on selected section
                    function updateTable(section) {
                        $.ajax({
                            type: 'POST',
                            url: 'fetch_section.php', // Path to your PHP script that fetches data based on section
                            data: {
                                section: section
                            },
                            success: function(data) {
                                $('#tableBody').html(data); // Update table content

                                // Update button state after updating table
                                updateButtonState();
                            }
                        });
                    }

                    // Event listener for dropdown change
                    $('#sections').change(function() {
                        var selectedSection = $(this).val();
                        updateTable(selectedSection);
                    });

                    // Initially update table with data for the first section
                    var initialSection = $('#sections').val();
                    updateTable(initialSection);

                    // Function to handle form submission and show SweetAlert
                    $('#composeEmailForm').submit(function(event) {
                        event.preventDefault(); // Prevent form submission
                        var formData = $(this).serialize(); // Serialize form data

                        $.ajax({
                            type: 'POST',
                            url: 'grading_email.php',
                            data: formData,
                            dataType: 'json',
                            success: function(response) {
                                if (response.status === 'success') {
                                    // Show success message using SweetAlert
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Email Sent Successfully',
                                        text: response.message,
                                        didClose: function() {
                                            location.reload(); // Reload the page
                                        }
                                    });
                                } else {
                                    // Show error message using SweetAlert
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.message,
                                        didClose: function() {
                                            location.reload(); // Reload the page
                                        }
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                // Show error message using SweetAlert if request fails
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'An error occurred while sending the email.',
                                });
                                console.log(error);

                                // Check if the response contains HTML content
                                if (xhr.responseText.includes('<')) {
                                    // Log HTML content if present
                                    console.log("HTML Error Response:", xhr.responseText);
                                }
                            }
                        });
                    });
                });
            </script>
            <script>
                function openComposeModal() {
                    // Get selected student IDs
                    let selectedStudents = Array.from(document.querySelectorAll("input[name='studentIDs[]']:checked"))
                    .map(checkbox => checkbox.value);

                    if (selectedStudents.length === 0) {
                        alert("Please select at least one student.");
                        return;
                    }

                    console.log("Selected Student IDs:", selectedStudents); // Debugging

                    // Send selected student IDs to PHP
                    fetch("fetch_trainer_email.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify({ studentIDs: selectedStudents })
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log("Trainer Email Response:", data); // Debugging

                        if (data.error) {
                            alert("Error: " + data.error);
                        } else {
                            // Combine all trainer emails into one string (comma-separated)
                            document.getElementById("recipient-email").value = data.trainerEmails.join(", ");
                        }
                    })
                    .catch(error => console.error("Error fetching trainerEmail:", error));
    
                    // Show the modal
                    $('#composeEmailModal').modal('show');
                }
            </script>
</body>
</html>