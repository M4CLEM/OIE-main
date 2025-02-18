<?php
session_start();
include_once("../includes/connection.php");
//$query="select * from student_user";
$query = "select * from users";
$result = mysqli_query($connect, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <?php include("../elements/meta.php"); ?>
    <title>Intern Portal</title>
    <?php include("embed.php"); ?>
    

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!--Sidebar Wrapper-->
        <aside id="sidebar" class="expand">
            <?php include('../elements/stud_sidebar.php')?>
        </aside>

        <div class="main">

            <?php

                $email = $_SESSION['student'];
                $query = "SELECT * FROM studentinfo WHERE email ='$email'";
                $result = mysqli_query($connect, $query);
                while ($rows = mysqli_fetch_array($result)) {
                    $image = $rows['image'];
                }

            ?>

            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 static-top shadow">

            <!-- Title -->
            <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Documents</h4>

                <!-- Topbar Navbar -->
                <ul class="navbar-nav ml-auto">

                    <div class="topbar-divider d-none d-sm-block"></div>

                    <!-- Nav Item - User Information -->
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">  
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                            <?php (isset($_SESSION['student'])) ?> <?php echo $_SESSION['student']; ?></span>
                            <img class="img-profile rounded-circle" src="<?php echo $image ? $image : '../img/undraw_profile.svg'; ?>">
                        </a>
                        <!-- Dropdown - User Information -->
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                            aria-labelledby="userDropdown">
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

            <!-- Begin Page Content -->
            <!-- Illustrations -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h3 class="m-0 font-weight-bold text-dark">OJT Documents</h3>
                    <button class="btn btn-primary" id="uploadButton" data-toggle="modal" data-target="#uploadModal">Upload New File</button>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th scope="col">Document</th>
                                    <th width="30%" scope="col">File Name</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Date</th>
                                    <th width="11%" align="center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $email = $_SESSION['student'];
                                $query = "SELECT * FROM documents WHERE email ='$email'";
                                $result = mysqli_query($connect, $query);
                                while ($rows = mysqli_fetch_array($result)) {
                                    $studemail = $rows['email'];
                                    $file = $rows['file_name'];
                                    $documentType = str_replace('_', '/', $rows['document']);
                                    $status = $rows['status'];
                                    $date = $rows['date'];
                                    $filename = basename($file);
                                    $rowId = $rows['id'];
                                ?>
                                    <tr>
                                        <td><?php echo $documentType; ?></td>
                                        <td id="fileNameCell_<?php echo $rowId; ?>"><?php echo $filename; ?></td>
                                        <td>
                                            <div id="statusCell_<?php echo $rowId; ?>" class="text-center p-1 status-<?php echo strtolower($status); ?> bg-<?php echo strtolower($status) === 'pending' ? 'warning text-white' : (strtolower($status) === 'approved' ? 'success text-white' : 'danger text-white'); ?> rounded">
                                                <?php echo $status; ?>
                                            </div>
                                        </td>
                                        <td><?php echo $date; ?></td>
                                        <td>
                                            <div class="row"> <!-- Removed gutters using g-0 -->
                                                <div class="col-md-4"> <!-- Reduced column width -->
                                                    <button class="btn btn-primary btn-sm" onclick="viewPDF('<?php echo $file; ?>')"><i class="far fa-eye"></i></button>
                                                </div>
                                                <div class="col-md-4"> <!-- Reduced column width -->
                                                    <input type="file" style="display: none;" id="fileInput_<?php echo $rowId; ?>" accept=".docx,.pdf" onchange="uploadFile(<?php echo $rowId; ?>, '<?php echo $documentType; ?>')">
                                                    <button class="btn btn-success btn-sm ml-2" onclick="document.getElementById('fileInput_<?php echo $rowId; ?>').click();"><i class="fas fa-upload"></i></button>
                                                </div>
                                            </div>
                                        </td>

                                    </tr>
                                <?php
                                }
                                ?>
                                <tr id='noResult' class='text-center' style='display: none;'><td colspan='5'>No Results Found</td></tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

        <!-- Upload New File Modal -->
        <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadModalLabel">Upload Document</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form enctype="multipart/form-data" action="upload_docs.php" method="POST">
                            <div class="form-group">
                                <label for="documentType">Document Type:</label>
                                <select class="form-control" id="documentType" name="documentType">
                                    <optgroup label="Pre Requirements">
                                        <option value="Resume">Resume</option>
                                        <option value="Waiver_Intent">Waiver/Intent</option>
                                        <option value="Medical Certificate">Medical Certificate</option>
                                        <option value="Trainee's Profile and Locator Sheet">Trainee's Profile and Locator Sheet</option>
                                        <option value="Resume">Notarized Internship Agreement(MOA)</option>
                                        <option value="COM">COM</option>
                                        <option value="OJT Student Training Program">OJT Student Training Program</option>
                                        <option value="Acceptance Letter">Acceptance Letter</option>
                                        <option value="SEC Registration Certificate">SEC Registration Certificate(for Private Offices)</option>
                                        <option value="Endorsement Letter">Endorsement Letter</option>
                                        <option value="Certificate of Grades">Certificate of Grades</option>
                                        <option value="OJT Rules and Procedures Locator Sheet">OJT Rules and Procedures Locator Sheet</option>
                                    </optgroup>
                                    <optgroup label="Post Requirements">
                                        <option value="Performance Evaluation Sheet">Performance Evaluation Sheet</option>
                                        <option value="Clearance from OJT Coordinator">Clearance from OJT Coordinator</option>
                                        <option value="Company Exit Clearance">Company Exit Clearance</option>
                                        <option value="Trainer's Profile">Trainer's Profile</option>
                                        <option value="Weekly Journal">Weekly Journal</option>
                                    </optgroup>
                                    <!-- Add more options as needed -->
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="file" class="form-control-file" id="newFile" name="newFile" accept=".docx,.pdf">
                            </div>
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </form>
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
    </div>
</body>

<script>
    $(document).ready(function() {
        $('#searchInput').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('#table tbody tr').filter(function() {
                var rowText = $(this).text().toLowerCase();
                var isVisible = rowText.indexOf(value) > -1;
                $(this).toggle(isVisible);
            });

            // Check if any rows are visible after filtering
            var visibleRows = $('#table tbody tr:visible').length;
            if (visibleRows === 0) {
                $('#noResult').show(); // Display "No Results" message
            } else {
                $('#noResult').hide(); // Hide "No Results" message if there are visible rows
            }
        });
    });

    function viewPDF(pdfPath) {
        // Open the PDF in a new tab/window
        window.open(pdfPath, '_blank');
    }

    function uploadFile(rowId, documentType) {
        var fileInput = document.getElementById('fileInput_' + rowId);
        var file = fileInput.files[0];
        var formData = new FormData();
        formData.append('file', file);
        formData.append('rowId', rowId);
        formData.append('documentType', documentType);

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'update_docs.php', true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                // Parse the JSON response
                var response = JSON.parse(xhr.responseText);
                if (response.status === 'success') {
                    // Update the table with the new file name
                    var fileNameCell = document.querySelector(`#fileNameCell_${rowId}`);
                    var statusCell = document.querySelector(`#statusCell_${rowId}`);
                    if (fileNameCell && statusCell) {
                        fileNameCell.innerText = response.newFileName; // Update the text inside the file name cell
                        statusCell.innerText = 'Pending'; // Update the status cell to display "Pending"
                        statusCell.classList.remove('bg-success'); // Remove success class if present
                        statusCell.classList.add('bg-warning'); // Add warning class
                    } else {
                        console.error('Could not find the cell to update with rowId: ' + rowId);
                    }
                } else {
                    console.error('Error: ' + response.message);
                }
            } else {
                console.error('Error: ' + xhr.statusText);
            }
        };
        xhr.send(formData);
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>
<script src="../assets/js/sidebarscript.js"></script>