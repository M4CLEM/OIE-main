<?php
session_start();
include("../includes/connection.php");

// Get the student's email from the session
$email = $_SESSION['student'];

// Fetch the department for the student based on the email
$departmentResult = mysqli_query($connect, "SELECT department FROM studentinfo WHERE email='$email'");

// Check if the query was successful and if there is a department
if (!$departmentResult) {
    die("Query Failed: " . mysqli_error($connect));
}

// Check if the department exists in the database
if (mysqli_num_rows($departmentResult) > 0) {
    $row = mysqli_fetch_assoc($departmentResult);
    $department = $row['department'];
    
    // Trim any extra spaces from the department value
    $department = trim($department);

    // Now that we have the department, set it in the session
    $_SESSION['department'] = $department;

    // Fetch documents related to the department
    $documentQuery = "SELECT * FROM documents_list WHERE department='$department'";

    $documentResult = mysqli_query($connect, $documentQuery);

    if (!$documentResult) {
        die("Query Failed: " . mysqli_error($connect));
    }

    $studDocumentQuery = "SELECT * FROM documents WHERE email= '$email'";

    $studDocumentResult = mysqli_query($connect, $studDocumentQuery);
    if (!$documentResult) {
        die("" . mysqli_error($connect));
    }

} else {
    echo "No department found for this student.<br>";  // Debugging if no department is found for the student
}


?>

<!DOCTYPE html>
<html>
    <head>
        <?php include("../elements/meta.php"); ?>
        <title>Intern Portal</title>
        <?php include("embed.php"); ?>
    </head>
    <body id="page-top">
        <div id="wrapper">
            <!-- Sidebar Wrapper -->
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
                
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 static-top shadow">
                    <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Documents</h4>
                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">  
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    <?php echo $_SESSION['student']; ?>
                                </span>
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

                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h3 class="m-0 font-weight-bold text-dark">OJT Documents</h3>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th scope="col">Document</th>
                                        <th width="25%" scope="col">File Name</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Date</th>
                                        <th width="36%" align="center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                // Store student-uploaded documents in an associative array
                                $studentDocuments = [];
                                while ($row = mysqli_fetch_assoc($studDocumentResult)) {
                                    $studentDocuments[$row['document']] = [
                                        'id' => $row['id'],
                                        'file_name' => $row['file_name'],
                                        'file_link' => $row['file_link'],
                                        'status' => $row['status'],
                                        'date' => $row['date']
                                    ];
                                }

                                // Display all document templates, matching any student-uploaded files
                                while ($row = mysqli_fetch_assoc($documentResult)) {
                                    $documentName = $row['documentName'];
                                    $doc_template = $row['file_template'];

                                    // Extract file ID from Google Drive URL
                                    preg_match('/\/d\/([^\/]+)/', $doc_template, $matches);
                                    $file_id = $matches[1] ?? '';

                                    // Construct Google Drive download link
                                    $drive_file_url = $file_id ? "https://drive.google.com/uc?export=download&id=" . urlencode($file_id) : "#";

                                    // Check if a student has uploaded this document
                                    $fileName = $studentDocuments[$documentName]['file_name'] ?? '';
                                    $fileLink = $studentDocuments[$documentName]['file_link'] ?? '';
                                    $status = $studentDocuments[$documentName]['status'] ?? '';
                                    $date = $studentDocuments[$documentName]['date'] ?? '';
                                    $id = $studentDocuments[$documentName]['id'] ?? '';
                                ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($documentName); ?></td>
                                        <td><?php echo htmlspecialchars($fileName); ?></td>
                                        <td>
                                            <?php if (!empty($status)): ?>
                                                <div class="text-center p-1 status-<?php echo strtolower($status); ?> bg-<?php echo strtolower($status) === 'pending' ? 'warning text-white' : (strtolower($status) === 'approved' ? 'success text-white' : 'danger text-white'); ?> rounded">
                                                <?php echo htmlspecialchars($status); ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($date); ?></td>
                                        <td>
                                            <a href="<?php echo $drive_file_url; ?>" class="btn btn-success btn-sm">
                                                <i class="fas fa-download"></i> Download Template
                                            </a>
                                            <button class="btn btn-success btn-sm uploadButton" data-toggle="modal" data-target="#uploadFileModal" data-document="<?php echo htmlspecialchars($documentName); ?>">
                                                <i class="fas fa-upload"></i> Upload
                                            </button>
                                            <?php if ($fileName) { ?>
                                                <button class="btn btn-primary btn-sm" onclick="viewPDF('<?php echo $fileLink; ?>')">
                                                    <i class="far fa-eye"></i> View
                                                </button>
                                                <button class="btn btn-danger btn-sm deleteBtn" data-toggle="modal" data-target="#deleteModal" data-id="<?php echo $id;?>">
                                                    <i class="fa fa-trash"></i> Delete
                                                </button>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                                <!-- DELETE MODAL -->
                                <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteModalLabel">Delete Confirmation</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure to delete this row?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                    </div>
                </div>
            </div>
            
                                    <!-- LOG OUT MODAL-->
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

                        <!-- Upload File Modal -->
                        <div class="modal fade" id="uploadFileModal" tabindex="-1" role="dialog" aria-labelledby="uploadFileModal" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Upload File</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="uploadForm" action="functions/upload_docs.php" method="POST" enctype="multipart/form-data">
                                            <div class="form-group">
                                                <label>Document Type:</label>
                                                <input type="text" class="form-control input-sm" id="documentType" name="documentType" readonly>
                                                
                                                <label>Upload File:</label>
                                                <input type="file" class="form-control-file" id="uploadFile" name="uploadFile" accept=".docx,.pdf">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary">Upload</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>
        <script src="../assets/js/sidebarscript.js"></script>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body d-flex flex-column align-items-center justify-content-center text-center" style="min-height: 200px;">
                <h5 class="text-primary">Please Wait...</h5>
                <div class="spinner-border text-primary my-3" role="status" style="width: 3rem; height: 3rem;"></div>
                <p>Uploading document...</p>
            </div>
        </div>
    </div>
</div>

<!--Upload Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content text-center p-4">
            <h5 class="text-success">Success!</h5>
            <i class="fa fa-check-circle text-success fa-3x my-3"></i>
            <p>Document uploaded successfully.</p>
            <button class="btn btn-success" type="button" data-dismiss="modal" id="closeSuccessModal">OK</button>
        </div>
    </div>
</div>

<!--Upload JavaScript -->
<script>
    document.getElementById("uploadForm").addEventListener("submit", function() {
        $("#uploadFileModal").modal("hide"); // Hide upload modal
        $("#loadingModal").modal("show"); // Show loading modal
    });

    // Show success modal if the URL contains "success=1"
    window.onload = function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('success')) {
            $("#loadingModal").modal("hide"); // Ensure loading modal is closed
            $("#successModal").modal("show"); // Show success modal

            // Remove "success=1" from the URL without reloading
            const newUrl = window.location.pathname + window.location.search.replace(/(\?|&)success=1/, '');
            window.history.replaceState(null, '', newUrl);
        }
    };

    // Close success modal on button click
    document.getElementById("closeSuccessModal").addEventListener("click", function() {
        $("#successModal").modal("hide");
    });
</script>


        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Select all upload buttons
                let uploadButtons = document.querySelectorAll(".uploadButton");

                uploadButtons.forEach(button => {
                    button.addEventListener("click", function() {
                        // Get document name from data attribute
                        let documentName = this.getAttribute("data-document");
            
                        // Set it in the modal's input field
                        document.getElementById("documentType").value = documentName;
                    });
                });
            });

            function viewPDF(pdfPath) {
                // Open the PDF in a new tab/window
                window.open(pdfPath, '_blank');
            }

            $(document).ready(function() {
                $('.deleteBtn').click(function() {
                var id = $(this).data('id');
                $('#confirmDelete').data('id', id);
                });

                $('#confirmDelete').click(function() {
                    var id = $(this).data('id');
                    $.ajax({
                        url: 'functions/delete_docs.php',
                        type: 'POST',
                        data: {id: id},
                        success: function(response) {
                            alert(response);
                            location.reload();
                        },
                        error: function(xhr, status, error) {
                            alert('An error occured:' + error);
                        }
                    })
                });
            })

        </script>
    </body>
</html>
