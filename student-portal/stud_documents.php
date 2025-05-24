<?php
session_start();
include("../includes/connection.php");

// Get the student's email from the session
$email = $_SESSION['student'];
$semester = $_SESSION['semester'];
$schoolYear = $_SESSION['schoolYear'];

$studNumberResult = mysqli_query($connect, "SELECT studentID FROM studentinfo WHERE email = '$email'");

if ($studNumberResult && mysqli_num_rows($studNumberResult) > 0) {
    $row = mysqli_fetch_assoc($studNumberResult);
    $studentID = $row['studentID'];
} 

$enrollResult = mysqli_query($connect, "SELECT semester, schoolYear FROM student_masterlist WHERE studentID = '$studentID'");

$enrollments = []; // Array to store all semester and schoolYear values

if ($enrollResult && mysqli_num_rows($enrollResult) > 0) {
    while ($row = mysqli_fetch_assoc($enrollResult)) {
        $enrollments[] = [
            'semester' => $row['semester'],
            'schoolYear' => $row['schoolYear']
        ];
    }
}

// Fetch the department for the student based on the email
$departmentResult = mysqli_query($connect, "SELECT department FROM studentinfo WHERE email='$email'");

if (!$departmentResult) {
    die("Query Failed: " . mysqli_error($connect));
}

if (mysqli_num_rows($departmentResult) > 0) {
    $row = mysqli_fetch_assoc($departmentResult);
    $department = $row['department'];

    $department = trim($department);

    $_SESSION['department'] = $department;

    // Fetch documents related to the department
    $documentQuery = "SELECT * FROM documents_list WHERE department='$department'";

    $documentResult = mysqli_query($connect, $documentQuery);

    if (!$documentResult) {
        die("Query Failed: " . mysqli_error($connect));
    }

    // Fetch student's documents
    $studDocumentQuery = "SELECT * FROM documents WHERE email= '$email'";

    $studDocumentResult = mysqli_query($connect, $studDocumentQuery);
    if (!$studDocumentResult) {
        die("Query Failed: " . mysqli_error($connect));
    }

    // Rearrange the enrollments array to make the active semester the first one
    usort($enrollments, function ($a, $b) use ($semester, $schoolYear) {
        // First, place the active semester and school year at the beginning
        if ($a['semester'] == $semester && $a['schoolYear'] == $schoolYear) {
            return -1;
        }
        if ($b['semester'] == $semester && $b['schoolYear'] == $schoolYear) {
            return 1;
        }

        // Then maintain chronological order (assuming "1st Semester" comes before "2nd Semester")
        $semesterOrder = ['1st Semester', '2nd Semester'];
        return array_search($a['semester'], $semesterOrder) - array_search($b['semester'], $semesterOrder);
    });

} else {
    echo "No department found for this student.<br>";
}
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
                <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Documents</h4>
                <!-- Topbar Navbar -->
                <?php include('includes/navbar_user_info.php'); ?>
            </nav>

            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h3 class="m-0 font-weight-bold text-dark">OJT Documents</h3>
                </div>
                <div class="card-body">
                    <!-- Tabs for each semester and school year -->
                    <ul class="nav nav-tabs" id="documentTabs" role="tablist">
                        <?php foreach ($enrollments as $index => $enrollment): ?>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link <?php echo $index === 0 ? 'active' : ''; ?>" id="tab-<?php echo $index; ?>" data-toggle="tab" href="#semester-<?php echo $index; ?>" role="tab" aria-controls="semester-<?php echo $index; ?>" aria-selected="true">
                                    <?php echo htmlspecialchars($enrollment['semester']) . ' ' . htmlspecialchars($enrollment['schoolYear']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="tab-content mt-3" id="documentTabsContent">
                        <?php foreach ($enrollments as $index => $enrollment): ?>
                            <div class="tab-pane fade <?php echo $index === 0 ? 'show active' : ''; ?>" id="semester-<?php echo $index; ?>" role="tabpanel" aria-labelledby="tab-<?php echo $index; ?>">
                                <div class="table-responsive">
                                    <table class="table table-bordered" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th scope="col">Document</th>
                                                <th width="25%" scope="col">File Name</th>
                                                <th scope="col">Status</th>
                                                <th scope="col">Date</th>
                                                <th width="23%" align="center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                // Active semester and schoolYear - Display documents from documents_list
                                                if ($semester === $enrollment['semester'] && $schoolYear === $enrollment['schoolYear']) {
                                                    // Manually insert the "Resume" row
                                                    $documentName = 'Resume';
                                                    // Check if the "Resume" document exists in the database
                                                    $resumeQuery = "SELECT * FROM documents WHERE document = 'Resume' AND semester = '$semester' AND schoolYear = '$schoolYear'";
                                                    $resumeResult = mysqli_query($connect, $resumeQuery);
                                                    $resumeRow = mysqli_fetch_assoc($resumeResult);

                                                    // Get document details for the hardcoded row (Resume)
                                                    $fileName = $resumeRow ? $resumeRow['file_name'] : '';
                                                    $status = $resumeRow ? $resumeRow['status'] : '';
                                                    $date = $resumeRow ? $resumeRow['date'] : '';
                                                    $fileLink = $resumeRow ? $resumeRow['file_link'] : '';
                                                    ?>

                                                    <!-- Hardcoded "Resume" row -->
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
                                                            <!-- View Button - Disabled if no file is uploaded -->
                                                            <button class="btn btn-primary btn-sm" onclick="viewPDF('<?php echo $fileLink; ?>')" <?php echo empty($fileLink) ? 'disabled' : ''; ?>>
                                                                <i class="far fa-eye"></i> View
                                                            </button>

                                                            <!-- Upload Button - Accessible only if no file is uploaded -->
                                                            <button class="btn btn-success btn-sm uploadButton" data-toggle="modal" data-target="#uploadFileModal" data-document="Resume" <?php echo !empty($fileLink) ? 'disabled' : ''; ?>>
                                                                <i class="fas fa-upload"></i> Upload
                                                            </button>

                                                            <!-- Delete Button - Disabled if no file is uploaded -->
                                                            <button class="btn btn-danger btn-sm deleteBtn" data-toggle="modal" data-target="#deleteModal" data-id="<?php echo $resumeRow['id']; ?>" <?php echo empty($fileLink) ? 'disabled' : ''; ?>>
                                                                <i class="fa fa-trash"></i> Delete
                                                            </button>
                                                        </td>
                                                    </tr>
                                                
                                                    <?php
                                                    // Now loop through the documents from the database for the active semester and school year
                                                    while ($docListRow = mysqli_fetch_assoc($documentResult)) {
                                                        $documentName = $docListRow['documentName'];
                                                        $doc_template = $docListRow['file_template'];
                                                
                                                        // Extract file ID from Google Drive URL
                                                        preg_match('/\/d\/([^\/]+)/', $doc_template, $matches);
                                                        $file_id = $matches[1] ?? '';
                                                
                                                        // Construct Google Drive download link
                                                        $drive_file_url = $file_id ? "https://drive.google.com/uc?export=download&id=" . urlencode($file_id) : "#";
                                                
                                                        // Look for the corresponding student document from the documents table
                                                        $studentDocument = null;
                                                        mysqli_data_seek($studDocumentResult, 0); // Reset the pointer to the beginning of the result set
                                                        while ($docRow = mysqli_fetch_assoc($studDocumentResult)) {
                                                            if ($docRow['document'] === $documentName && $docRow['semester'] === $semester && $docRow['schoolYear'] === $schoolYear) {
                                                                $studentDocument = $docRow;
                                                                break;
                                                            }
                                                        }
                                                
                                                        // If document is uploaded by the student, show its details, else leave them empty
                                                        $fileName = $studentDocument ? $studentDocument['file_name'] : '';
                                                        $status = $studentDocument ? $studentDocument['status'] : '';
                                                        $date = $studentDocument ? $studentDocument['date'] : '';
                                                        $fileLink = $studentDocument ? $studentDocument['file_link'] : '';
                                                        ?>
                                                        <!-- Dynamically generated row for other documents -->
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
                                                                <?php if ($fileLink): ?>
                                                                    <button class="btn btn-primary btn-sm" onclick="viewPDF('<?php echo $fileLink; ?>')">
                                                                        <i class="far fa-eye"></i> View
                                                                    </button>
                                                                <?php endif; ?>
                                                
                                                                <?php if (empty($fileLink) && $semester === $enrollment['semester'] && $schoolYear === $enrollment['schoolYear']): ?>
                                                                    <a href="<?php echo $drive_file_url; ?>" class="btn btn-success btn-sm" target="_blank">
                                                                        <i class="fas fa-download"></i> Download Template
                                                                    </a>
                                                                    <button class="btn btn-success btn-sm uploadButton" data-toggle="modal" data-target="#uploadFileModal" data-document="<?php echo htmlspecialchars($documentName); ?>">
                                                                        <i class="fas fa-upload"></i> Upload
                                                                    </button>
                                                                <?php endif; ?>
                                                
                                                                <?php if (!empty($fileLink) && $semester === $enrollment['semester'] && $schoolYear === $enrollment['schoolYear']): ?>
                                                                    <button class="btn btn-danger btn-sm deleteBtn" data-toggle="modal" data-target="#deleteModal" data-id="<?php echo $studentDocument['id']; ?>">
                                                                        <i class="fa fa-trash"></i> Delete
                                                                    </button>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    }
                                                }

                                                // Inactive semester and schoolYear - Display all documents from the documents table
                                                if ($semester !== $enrollment['semester'] || $schoolYear !== $enrollment['schoolYear']) {
                                                    // Loop through all student documents for inactive semesters and school years
                                                    $inactiveDocsQuery = "SELECT * FROM documents WHERE email='$email' AND semester='{$enrollment['semester']}' AND schoolYear='{$enrollment['schoolYear']}'";
                                                    $inactiveDocsResult = mysqli_query($connect, $inactiveDocsQuery);

                                                    while ($inactiveDoc = mysqli_fetch_assoc($inactiveDocsResult)) {
                                                    ?>
                                                        <tr>
                                                            <td><?php echo htmlspecialchars($inactiveDoc['document']); ?></td>
                                                            <td><?php echo htmlspecialchars($inactiveDoc['file_name']); ?></td>
                                                            <td><?php echo htmlspecialchars($inactiveDoc['status']); ?></td>
                                                            <td><?php echo htmlspecialchars($inactiveDoc['date']); ?></td>
                                                            <td>
                                                                <!-- No Download Button for Inactive Semesters -->
                                                                <a href="#" class="btn btn-success btn-sm disabled">
                                                                    <i class="fas fa-download"></i> Download Template
                                                                </a>

                                                                <!-- View Button - Always Available on Inactive Semester if File is Uploaded -->
                                                                <?php if ($inactiveDoc['file_link']): ?>
                                                                    <button class="btn btn-primary btn-sm" onclick="viewPDF('<?php echo $inactiveDoc['file_link']; ?>')">
                                                                        <i class="far fa-eye"></i> View
                                                                    </button>
                                                                <?php endif; ?>

                                                                <!-- No Upload or Delete Button on Inactive Semester -->
                                                            </td>
                                                        </tr>
                                                            <?php
                                                    }
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
        </div>

        <!-- DELETE CONFIRMATION MODAL -->
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
                        Are you sure you want to delete this document?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- DELETE LOADING MODAL -->
        <div class="modal fade" id="deleteLoadingModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content d-flex flex-column align-items-center justify-content-center p-4">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="sr-only">Deleting...</span>
                    </div>
                    <p class="mt-3">Deleting document, please wait...</p>
                </div>
            </div>
        </div>

        <!-- SUCCESS LOADING MODAL -->
        <div class="modal fade" id="successLoadingModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content text-center p-4">
                    <h5 class="text-success">Success!</h5>
                    <p>The document has been deleted successfully.</p>
                    <button type="button" class="btn btn-success" data-dismiss="modal">OK</button>
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

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>
    <script src="../assets/js/sidebarscript.js"></script>


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

                // Hide delete modal, show delete loading modal
                $('#deleteModal').modal('hide');
                $('#deleteLoadingModal').modal('show');

                $.ajax({
                    url: 'functions/delete_docs.php',
                    type: 'POST',
                    data: {
                        id: id
                    },
                    success: function(response) {
                        // Hide delete loading modal, show success loading modal
                        $('#deleteLoadingModal').modal('hide');
                        $('#successLoadingModal').modal('show');

                        // Reload the page after a short delay (optional)
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    },
                    error: function(xhr, status, error) {
                        $('#deleteLoadingModal').modal('hide'); // Hide loading modal
                        alert('An error occurred: ' + error);
                    }
                });
            });
        });
    </script>
</body>

</html>